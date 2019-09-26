<?php
/*
 * Copyright notice
 * (c) 1998-2019 Limbas GmbH(support@limbas.org)
 * All rights reserved
 * This script is part of the LIMBAS project. The LIMBAS project is free software; you can redistribute it and/or modify it on 2 Ways:
 * Under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * Or
 * In a Propritary Software Licence http://limbas.org
 * The GNU General Public License can be found at http://www.gnu.org/copyleft/gpl.html.
 * A copy is found in the textfile GPL.txt and important notices to the license from the author is found in LICENSE.txt distributed with these scripts.
 * This script is distributed WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * This copyright notice MUST APPEAR in all copies of the script!
 * Version 3.6
 */

/*
 * ID:
 */

global $umgvar;

require_once($umgvar["pfad"].'/extra/messages/classes/php/olTemplate.class.php');
require_once($umgvar["pfad"].'/extra/messages/classes/php/olMailTreeList.class.php');
require_once($umgvar["pfad"].'/extra/messages/classes/php/olMail.class.php');
require_once($umgvar["pfad"].'/extra/messages/classes/php/olMimeMessage.class.php');
require_once($umgvar["pfad"].'/gtab/gtab.lib');


class limbasMail extends olMail{
    const CUST_FLAG_ALREADY_LINKED = 'limbaslinked';

	var $error = array();
	var $imap_cfg;
	var $table_id, $field_id, $record_id, $rtable_id;

    function __construct($table_id, $field_id, $record_id, $rtable_id){
        global $umgvar;

        $this->rtable_id = $rtable_id;
        $this->table_id = $table_id;
        $this->field_id = $field_id;
        $this->record_id = $record_id;

        if (!function_exists("imap_open")){
            $this->error[] = "Unterstützung für IMAP innerhalb PHP ist erforderlich!";
            return $this;
        }

        require_once($umgvar['path'] . '/extra/messages/config.php');
        $imap_cfg = lmb_getImapCfg($rtable_id);
        if (!$imap_cfg){
            $this->error[] = 'Keine gültige <a href="main.php?action=user_change">'.
                'Nachrichten-Konfiguration</a> gefunden!';
            return $this;
        }

        /* instantiate olMail object and exit if connection failed. */
        parent::constructor($imap_cfg);
        $this->imap_cfg = $imap_cfg;

        if (!$this->is_connected()){
            $this->error[] = "Fehler: ".imap_last_error();
            return $this;
        }

        return $this;
    }
    function __destruct(){$this->disconnect();}

	function arch($uid,$mbox){
	    global $umgvar;

	    if (!$mbox || !$uid)
	        return false;

        # get global unique id
        $uuid = $this->get_uuid($this->get($uid, $mbox));

        # check if mail has already been archived
        $gsr = array();
        $gsr[$this->rtable_id][11][0] = $uuid;
        $gsr[$this->rtable_id][11]['txt'][0] = 2; # ==
        $gresult = get_gresult($this->rtable_id, 1, null, $gsr);
        $alreadyArchived = ($gresult[$this->rtable_id]['res_count'] > 0);

//        $status = imap_setflag_full($this->con, "$uid", "label1");
//        $status = imap_setflag_full($this->con, "$uid", "label1", ST_UID);
//        print_r(imap_headers($this->con));
//        print_r(imap_fetchheader($this->con, "$uid", FT_UID));

        $returnID = null;
        if ($alreadyArchived) {
            # simply link to dataset
            $archivedID = $gresult[$this->rtable_id]['id'][0];
            $rel = init_relation($this->table_id, $this->field_id, $this->record_id, array($archivedID));
            if (!set_relation($rel))
                return false;

            $returnID = $archivedID;

        } else {
            # archive and link to dataset
            $newUID = $this->archmail($uid, $mbox);
            if (!$newUID) {
                $this->error[] = "Fehler beim archivieren der Nachricht!";
                return false;
            }

            $new = new_record($this->rtable_id,1,$this->field_id,$this->table_id,$this->record_id);

            $details = $this->get($newUID);
            $history["{$this->rtable_id},6,$new"] = $details["uid"];
            $history["{$this->rtable_id},7,$new"] = lmb_utf8_decode(html_entity_decode($details["fromaddr"],ENT_QUOTES,$umgvar["charset"]));
            $history["{$this->rtable_id},8,$new"] =  lmb_utf8_decode(html_entity_decode($details["toaddr"],ENT_QUOTES,$umgvar["charset"]));
            $history["{$this->rtable_id},9,$new"] =  lmb_utf8_decode(html_entity_decode($details["subject"],ENT_QUOTES,$umgvar["charset"]));
            $history["{$this->rtable_id},10,$new"] =  lmb_utf8_decode(html_entity_decode($details["body"],ENT_QUOTES,$umgvar["charset"]));
            $history["{$this->rtable_id},11,$new"] =  lmb_utf8_decode(html_entity_decode($uuid,ENT_QUOTES,$umgvar["charset"]));

            update_data($history,3,null);

            $returnID = $new;
        }

        if (!$returnID)
            return false;

        imap_setflag_full($this->con, $uid, limbasMail::CUST_FLAG_ALREADY_LINKED, ST_UID);
        return $returnID;
	}

	function get($uid, $mbox=null){
		if (!$mbox) $mbox = $this->config['arch_mbox'];
		if ($uid){
			return $this->_get($uid, $mbox);
		}
		$this->error[] = "Keine gültige Nachrichtnummer angegeben!";
		return false;
	}

	function del($uid, $mbox=null, $record_id){
		if (!$mbox) $mbox = urldecode($this->config['arch_mbox']);

		if ($uid){
			if (($ok = $this->delmail($uid, $mbox))){
				$verkn = set_verknpf($this->table_id,$this->field_id,$this->record_id,null,$record_id,1,1);
				set_joins($this->table_id,$verkn);
				del_data($this->rtable_id,$record_id,'delete');
#error_log("deleted[$uid] from [$mbox] and record_id[$record_id] in[{$this->rtable_id}]");

				return $ok;
			}
			$this->error[] = "Fehler beim löschen der Nachricht!";
		}
		return false;
	}

	private function _get($uid, $mbox){
		if (!imap_reopen($this->con, "{{$this->config['imap_hostname']}}".$mbox))
			return false;

		/* get message header and structure */
		if (!($hdr = imap_fetch_overview($this->con, $uid, FT_UID)))
			return false;

		if (!($info = imap_fetchstructure($this->con, $uid, FT_UID)))
			return false;

		/* examine which part is meant to be the body part and prepare a list of attachments. */
		$attachments = array();
		if (isset($info->parts) && (count($info->parts) > 1)){
			$partno = 0;
			foreach ($info->parts as $part){
				$partno++;
				if ($part->ifdparameters){
					if (!($fname = $this->_get_structure_param($part->dparameters, 'filename')))
						$fname = 'noname.txt';
					$attachments[] = array(
							'part' => $partno,
							'fname' => $fname,
							'type' => $part->type
						);
					//echo $part->type;
				} else {
					if (!isset($bodypart))
						$bodypart = $partno;
				}
			}
		}

		/* fetch message body */
		$is_html = false;
		if (isset($bodypart)){
			if ($info->parts[$bodypart-1]->type==1){
				$bodypart = "$bodypart.1";
				$s = imap_bodystruct($this->con, imap_msgno($this->con, $uid), $bodypart);
			} else
				$s = $info->parts[$bodypart-1];
			$body = imap_fetchbody($this->con, $uid, $bodypart, FT_UID);
		} else {
			$s = $info;
			$body = imap_body($this->con, $uid, FT_UID);
		}

		/* fetch message info */
		$from = $this->_header_addr_decode($hdr[0]->from);
		$to = $this->_header_addr_decode($hdr[0]->to);
		$subject = (isset($hdr[0]->subject)) ? $this->_qpdecode($hdr[0]->subject) : '';
		$date = date("d.m.Y H:i:s", strtotime($hdr[0]->date));
		$charset = lmb_strtoupper($this->_get_structure_param($s->parameters, 'charset'));

		if ($charset=='')
			$charset = "UTF-8";

		if($s->encoding==3)
			$body = base64_decode($body);
		else if($s->encoding==4)
			$body = imap_qprint($body);

		if ($s->subtype=="HTML")
			$is_html = true;

		$mbox = urlencode($mbox);
		$uid = urlencode($uid);

		if (!$charset || ($charset != "UTF-8"))
			$body = utf8_encode($body);

		if ($is_html)
			$body = preg_replace('/[\r\n]+/', "\n", strip_tags($body));

		$body = preg_replace('/[\n]/', "<br>\n", $body);

		return array("fromaddr" => $from, "toaddr" => $to, "date" => $date, "subject" => $subject,
			"body" => $body, "mbox" => $mbox, "uid" => $uid,
			"attachments" => count($attachments));
	}

	function send(){
		$util = new olMailTreeList();

		$fromaddr = (string)($_POST["fromaddr"] ? $_POST["fromaddr"] : "{$this->imap_cfg['full_name']} &lt;{$this->imap_cfg['email_address']}&gt;");

		$toaddr =  (string)($_POST["toaddr"] ? $_POST["toaddr"] : NULL);
		$subject =  (string)($_POST["subject"] ? $_POST["subject"] : "<no subject>");
		$body =  (string)($_POST["body"] ? $_POST["body"] : "");

		$mail_headers  = "From: {$fromaddr}\n";

		/* if desired, append the 'Organisation' mailheader. */
		if (isset($this->imap_cfg['organisation']))
			$mail_headers .= "Organisation: {$this->imap_cfg['organisation']}\n";

		/* always add mailer version mailheader. */
		$mail_headers .= "X-Mailer: ".MAILER_NAME." ".MAILER_VERSION."\n";

		$mm = new olMimeMessage($mail_headers, $body);
		/* $mm->addAttachment("LIMBAS Business Solutions.png"); */

		if (!$toaddr || $toaddr==""){
			$this->error[] = "Nachricht konnte nicht versandt werden!";
			$this->error[] = "Es wurde keine Empfängeradresse angegeben!";
			return false;
		}

		/* add all attachments ... with status. */
		foreach($_POST as $n => $v)
			if (preg_match('/^attachment_/', $n))
				$mm->addAttachment($v, $this->temp_foldername());

		$body = $mm->prepare($mail_headers);
		$toaddr = $util->make_mailaddr($toaddr);

		$ret = mail($toaddr, $util->quote_utf8($subject), $body, $mail_headers);
                var_dump($ret);
		/* save a copy of the message if sent successfully. */
		if ($ret){
			$saved_uid = $this->savemail("Sent",
				$mail_headers."To: {$toaddr}\nSubject: ".
				$util->quote_utf8($subject)."\n", $body);
			$arch_uid = $this->arch($saved_uid,"Sent");
		}else{
			$this->error[] = "Nachricht konnte nicht versandt werden!";
			return false;
		}
		if($arch_uid) return $arch_uid;

                
		return false;
	}
}
?>