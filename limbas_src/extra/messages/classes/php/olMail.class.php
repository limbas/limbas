<?php
/*
 * Copyright notice
 * (c) 1998-2018 Limbas GmbH(support@limbas.org)
 * All rights reserved
 * This script is part of the LIMBAS project. The LIMBAS project is free software; you can redistribute it and/or modify it on 2 Ways:
 * Under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * Or
 * In a Propritary Software Licence http://limbas.org
 * The GNU General Public License can be found at http://www.gnu.org/copyleft/gpl.html.
 * A copy is found in the textfile GPL.txt and important notices to the license from the author is found in LICENSE.txt distributed with these scripts.
 * This script is distributed WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * This copyright notice MUST APPEAR in all copies of the script!
 * Version 3.5
 */

/*
 * ID:
 */

define('DEFAULT_IMAP_HOSTNAME', 'localhost'); /* no host means localhost */
define('DEFAULT_IMAP_PORT', 143); /* set to 110 for switching to POP3 protocol */
define('DEFAULT_IMAP_OPTIONS', '/novalidate-cert');
define('DEFAULT_ARCH_MBOX', 'limbas'); /* the default archive mailbox is 
													 * a subfolder of the users inbox */

class olMail {
    var $con = false;
    var $config = array();
    var $options = DEFAULT_IMAP_OPTIONS;

    function __construct($config_array) {
        $this->constructor($config_array);
    }

    function constructor($config_array) {
        /* set provided config values */
        $this->config = $config_array;

        /* default values for user mailbox. */
        if ((!isset($config_array['imap_hostname'])) || (trim($config_array['imap_hostname']) == ''))
            $this->config['imap_hostname'] = DEFAULT_IMAP_HOSTNAME;

        /* validate port range, and use default port if invalid or not set */
        if ((!isset($config_array['imap_port'])) ||
            (intval($config_array['imap_port']) >= '32768') ||
            (intval($config_array['imap_port']) <= '0')
        )
            $this->config['imap_port'] = DEFAULT_IMAP_PORT;

        if ((!isset($config_array['reply_address'])) || (trim($config_array['reply_address']) == ''))
            $this->config['reply_address'] = $this->config['email_address'];

        /* default values for archive mailbox. */
        if ((!isset($config_array['arch_hostname'])) || (trim($config_array['arch_hostname']) == '')) {
            foreach (array("hostname", "port", "username", "password") as $_n => $_v)
                $this->config['arch_' . $_v] = $this->config['imap_' . $_v];
        }
        if ((!isset($config_array['arch_mbox'])) || (trim($config_array['arch_mbox']) == '')) {
            $this->config['arch_mbox'] = DEFAULT_ARCH_MBOX;
        }

        if (!isset($config_array['temp_path']))
            $this->config['temp_path'] = 'attachments/files/';

        /* establish a connection to the imap server only in case we have an imap username and password. */
        if ($this->config['imap_username'] && $this->config['imap_password'])
            $this->connect();
    }

    function destructor() {
    }

    function _get_structure_param($info, $name) {
        foreach ($info as $n => $v)
            if ($v->attribute == $name)
                return $v->value;
        return NULL;
    }

//	function _qpdecode_cb($enc, $trans, $data){
//		/* check for base64 transfer encoding, otherwise we assume quoted-printable*/
//		if (lmb_strtoupper($trans)=='B')
//			$data = base64_decode($data);
//		else
//			$data = imap_qprint(preg_replace('/_/', " ", $data));
//
//		/* return data without reencoding, if already utf-8 encoded */
//		if (lmb_strtoupper($enc)=='UTF-8')
//			return $data;
//
//		/* reencode localized data in utf-8 */
//		return utf8_encode($data);
//	}

    static function _qpdecode($s) {
        $decodedHeader = imap_mime_header_decode($s);
        return $decodedHeader[0]->text;
    }

    function connect() {
        if ($this->is_connected())
            return true;

        $this->con = imap_open(
            "{{$this->config['imap_hostname']}:{$this->config['imap_port']}{$this->options}}",
            $this->config['imap_username'], $this->config['imap_password']);

        return $this->is_connected();
    }

    function is_connected() {
        return is_resource($this->con);
    }

    function disconnect() {
        if ($this->is_connected())
            imap_close($this->con);
    }

    function preview_inbox() {
        $ret = array('all' => 0, 'new' => 0);
        if (imap_reopen($this->con, "{{$this->config['imap_hostname']}}INBOX")) {
            if (($check = imap_mailboxmsginfo($this->con))) {
                $ret['all'] = $check->Nmsgs;
                $ret['new'] = $check->Unread;
            }
        }
        return $ret;
    }

    function listfolders() {
        if (is_array($mbox = imap_getmailboxes($this->con, "{{$this->config['imap_hostname']}}", "*"))) {
            $ret = new olMailTreeList();
            foreach ($mbox as $i => $m) {
                /* mailboxmsginfo only for inbox */
                if ($m->name == "{{$this->config['imap_hostname']}}INBOX") {
                    if (imap_reopen($this->con, $m->name))
                        if (($check = imap_mailboxmsginfo($this->con)))
                            $ret->add($m->name, $m->delimiter, $check);
                } else
                    $ret->add($m->name, $m->delimiter, array());
            }
            $ret->output();
            return true;
        }
        return false;
    }

    function getpart($uid, $part, $mbox = NULL) {
        if ($mbox)
            if (!imap_reopen($this->con, "{{$this->config['imap_hostname']}}" . $mbox))
                return false;

        if (!($info = imap_fetchstructure($this->con, $uid, FT_UID)))
            return false;

        if (!isset($info->parts[$part - 1]))
            return false;

        $p = $info->parts[$part - 1];

        $body = imap_fetchbody($this->con, $uid, $part, FT_UID);

        if (!$body)
            return false;

        if (!($fname = $this->_get_structure_param($p->dparameters, 'filename')))
            $fname = 'unknown.txt';

        switch ($p->type) {
            /* only images and application/pdf can be displayed inline, as they
             * are handled by the browser internally.
             * all other parts are simply saved to disk.
             */
            case 5: /* image */
                header("Content-Disposition: inline; filename=\"{$fname}\"");
                header("Content-Type: image/" . lmb_strtolower($p->subtype) . "; name=\"{$fname}\"");
                break;
            case 3: /* application */
                if (lmb_strtolower($p->subtype) == 'pdf') {
                    header("Content-Disposition: inline; filename=\"{$fname}\"");
                    header("Content-Type: application/" . lmb_strtolower($p->subtype) . "; name=\"{$fname}\"");
                    break;
                }
            default:
                header("Content-Type: application/binary; name=\"{$fname}\"");
                header("Content-Disposition: attachment; filename=\"{$fname}\"");
        }

        switch ($p->encoding) {
            case 3:    /* BASE64 */
                echo base64_decode($body);
                break;
            case 4:    /* QUOTED-PRINTABLE */
                echo imap_qprint($body);
                break;
            default: /* 7BIT, 8BIT, BINARY or OTHER */
                echo $body;
        }
        return true;
    }

    function movemail($uid_src, $mbox_dst, $mbox_src = NULL) {
        if ($mbox_src) /* change mailbox if mbox_src parameter is given */
            if (!imap_reopen($this->con, "{{$this->config['imap_hostname']}}{$mbox_src}"))
                return false;

        if (!imap_mail_move($this->con, $uid_src, $mbox_dst, FT_UID))
            return false;

        return imap_expunge($this->con);
    }

    function delmail($uid, $mbox = NULL) {
        if ($mbox) /* change mailbox if mbox parameter is given */
            if (!imap_reopen($this->con, "{{$this->config['imap_hostname']}}{$mbox}"))
                return false;

        /* get message header and structure */
        if (!($hdr = imap_fetch_overview($this->con, $uid, FT_UID)))
            return false;

        $subject = (isset($hdr[0]->subject)) ? $this->_qpdecode($hdr[0]->subject) : '';

        if (!imap_delete($this->con, $uid, FT_UID))
            return false;

        return imap_expunge($this->con);
    }

    function _header_addr_decode($src) {
        return preg_replace('/(.*) <(.*)>/', "$1 &lt;$2&gt;", $this->_qpdecode($src));
    }

    function replymail($uid, $mbox = NULL) {
        if ($mbox) /* change mailbox if mbox parameter is given */
            if (!imap_reopen($this->con, "{{$this->config['imap_hostname']}}" . $mbox))
                return false;

        /* get message header and structure */
        if (!($hdr = imap_fetch_overview($this->con, $uid, FT_UID)))
            return false;

        if (!($info = imap_fetchstructure($this->con, $uid, FT_UID)))
            return false;

        $tpl = new olTemplate('compose.tpl');

        /* examine which part is meant to be the body part. */
        if (isset($info->parts) && (count($info->parts) > 1)) {
            $partno = 0;
            foreach ($info->parts as $part) {
                $partno++;
                if (!$part->ifdparameters) {
                    $bodypart = $partno;
                    break;
                }
            }
        }

        /* fetch message body */
        $is_html = false;
        if (isset($bodypart)) {
            if ($info->parts[$bodypart - 1]->type == 1) {
                $bodypart = "$bodypart.1";
                $s = imap_bodystruct($this->con, imap_msgno($this->con, $uid), $bodypart);
            } else
                $s = $info->parts[$bodypart - 1];
            $body = imap_fetchbody($this->con, $uid, $bodypart, FT_UID);
        } else {
            $s = $info;
            $body = imap_body($this->con, $uid, FT_UID);
        }

        $charset = lmb_strtoupper($this->_get_structure_param($s->parameters, 'charset'));

        switch ($s->encoding) {
            case 3:    /* BASE64 */
                $body = base64_decode($body);
                break;
            case 4:    /* QUOTED-PRINTABLE */
                $body = imap_qprint($body);
                break;
            default:
                /* 7BIT, 8BIT and BINARY do not need any reencoding */
        }

        if ($charset == '')
            $charset = "UTF-8";

        if (!$charset || ($charset != "UTF-8"))
            $body = utf8_encode($body);

        if ($s->subtype == "HTML") /* HTML content is generally unsafe! */
            $body = preg_replace('/[\r\n]+/', "\n", strip_tags($body));

        /* quote original message */
        $body = preg_replace('/^(.*)$/mu', "> $1", wordwrap(trim($body), 76));

        /* fetch message info */
        $date = date("d.m.Y H:i:s", strtotime($hdr[0]->date));
        $toaddr = preg_replace('/"/', '&quot;', $this->_header_addr_decode($hdr[0]->from));

        $tpl->output(array(
            "fromaddr" => "{$this->config['full_name']} &lt;{$this->config['email_address']}&gt;",
            "toaddr" => $toaddr,
            "subject" => "Re: " . ((isset($hdr[0]->subject)) ? $this->_qpdecode($hdr[0]->subject) :
                    "Ihre Nachricht vom $date"),
            "body" => "${toaddr} schrieb am ${date}:\n\n$body\n\n",
        ));
        return true;
    }

    function temp_foldername() {
        return "{$this->config['temp_path']}{$this->config['imap_hostname']}." .
            implode("", unpack("H*", $this->config['imap_username']));
    }

    function forwardmail($uid, $mbox = NULL, $_fwd_message = "Nachricht.eml") {
        if ($mbox) /* change mailbox if mbox parameter is given */
            if (!imap_reopen($this->con, "{{$this->config['imap_hostname']}}" . $mbox))
                return false;

        /* fetch header and body of the message to be forwarded */
        if (!($headers = imap_fetchheader($this->con, $uid, FT_UID)))
            return false;
        if (!($body = imap_body($this->con, $uid, FT_UID)))
            return false;

        /* create temporary folder with secure file permissions if it does not already exist. */
        $_temp_folder = $this->temp_foldername();
        if (!file_exists($_temp_folder))
            if (!mkdir($_temp_folder, 0700)) {
                echo "Temporäres Verzeichnis '$_temp_folder' konnte nicht erstellt werden!";
                return false;
            }

        /* create a temporary file with secure file permissions. */
        $_fwd_message_filename = $_temp_folder . "/" . $_fwd_message;
        if (!is_resource($f = fopen($_fwd_message_filename, "wb"))) {
            echo "Temporäre Datei '$_fwd_message' konnte nicht erstellt werden! (1)";
            return false;
        }
        fclose($f);
        chmod($_fwd_message_filename, 0600);
        if (!is_resource($f = fopen($_fwd_message_filename, "wb"))) {
            echo "Temporäre Datei '$_fwd_message' konnte nicht erstellt werden! (2)";
            return false;
        }

        /* write the mail to be forwarded into file. */
        fwrite($f, $headers . "\n" . $body);
        fclose($f);

        return true;
    }

    function getmail($uid, $mbox = NULL, $print = false) {
        if ($mbox) /* change mailbox if mbox parameter is given */
            if (!imap_reopen($this->con, "{{$this->config['imap_hostname']}}" . $mbox))
                return false;

        /* get message header and structure */
        if (!($hdr = imap_fetch_overview($this->con, $uid, FT_UID)))
            return false;

        if (!($info = imap_fetchstructure($this->con, $uid, FT_UID)))
            return false;


        $tpl = new olTemplate('message.tpl');

        /* examine which part is meant to be the body part and prepare a list of attachments. */
        $attachments = array();
        if (isset($info->parts) && (count($info->parts) > 1)) {
            $partno = 0;
            foreach ($info->parts as $part) {
                $partno++;
                if ($part->ifdparameters) {
                    if (!($fname = $this->_get_structure_param($part->dparameters, 'filename')))
                        $fname = 'noname.txt';
                    $attachments[] = array(
                        'part' => $partno,
                        'fname' => $fname,
                        'type' => $part->type
                    );
                } else {
                    if (!isset($bodypart) || $part->subtype == "HTML") {
                        $bodypart = $partno;
                    }
                }

            }
        }

        /* fetch message body */
        $is_html = false;


        if (isset($bodypart)) {
            if ($info->parts[$bodypart - 1]->type == 1) {
                $bodypart = "$bodypart.1";
                $s = imap_bodystruct($this->con, imap_msgno($this->con, $uid), $bodypart);
            } else {
                $s = $info->parts[$bodypart - 1];
            }
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

        if ($charset == '')
            $charset = "UTF-8";

        if ($s->encoding == 3)
            $body = base64_decode($body);
        else if ($s->encoding == 4)
            $body = imap_qprint($body);

        if ($s->subtype == "HTML")
            $is_html = true;


        $mbox = urlencode($mbox);
        $uid = urlencode($uid);

        if (!$charset || ($charset != "UTF-8"))
            $body = utf8_encode($body);

        if ($is_html) {
            $body = preg_replace('/[\r\n]+/', "\n", $body);
            //try to prevent XSS
            $body = preg_replace('<(\s*)script(.*?)script(\s*)>', '<un>', $body);
        }

        $body = preg_replace('/[\n]/', "<br>\n", $body);


        /* prepare two separate lists of inline images and attachments */
        $attachments_inline = $attachments_list = $attachments_header = "";
        if (count($attachments)) {
            $tpl_attach = new olTemplate('attachment.tpl');

            $attachments_header = "Datei" . ((count($attachments) > 1) ? "anh&auml;nge" : "anhang");

            foreach ($attachments as $idx => $attachment) {
                switch ($attachment['type']) {
                    case 3: // application
                        if (preg_match('/\.pdf$/i', $attachment['fname']))
                            $tpl_inline = new olTemplate('inline_pdf.tpl');
                        break;
                    case 5: // image
                        $tpl_inline = new olTemplate('inline_image.tpl');
                        break;
                    /*
                    case 4: // audio
                        $tpl_inline = new olTemplate('inline_audio.tpl');
                        break;
                    case 6: // video
                        $tpl_inline = new olTemplate('inline_video.tpl');
                        break;*/
                    default: // 7 == other
                        $tpl_inline = new olTemplate('inline_debug.tpl');
                }

                if (isset($tpl_inline))
                    $attachments_inline .= $tpl_inline->parse(array(
                        "mbox" => $mbox, "uid" => $uid,
                        "name" => $attachment['fname'],
                        "part" => $attachment['part'],
                        "type" => $attachment['type']));

                $attachments_list .= $tpl_attach->parse(array(
                    "mbox" => $mbox,
                    "uid" => $uid,
                    "name" => $attachment['fname'],
                    "part" => $attachment['part'],
                ));
            }
        }

        $tpl->output(array(
            "fromaddr" => $from, "toaddr" => $to, "date" => $date, "subject" => $subject,
            "body" => $body, "print" => $print, "mbox" => $mbox, "uid" => $uid,
            "attachments_inline" => $attachments_inline,
            "attachments_all" => (count($attachments)) ?
                "<div id=\"mail_attachments\">" .
                "<h3>{$attachments_header}</h3><ul>{$attachments_list}</ul></div>"
                : ""
        ));
        return true;
    }

    function savemail($mbox, $headers, $body) {
        if (!imap_reopen($this->con, "{{$this->config['imap_hostname']}}" . $mbox))
            return false;

        /* Append current date if no date header is provided. */
        if (!preg_match('/^Date:/i', $headers))
            $headers .= "Date: " . date('r') . "\n";

        if (($ret = imap_append($this->con, "{{$this->config['imap_hostname']}}{$mbox}", $headers . "\r\n" . $body)))
            $check = imap_check($this->con);

        /* return uid of saved message or 0 on error */
        return ($ret) ? imap_uid($this->con, $check->Nmsgs) : 0;
    }

    function archmail($uid, $mbox) {
        $ret = 0;
        if ($mbox) /* change mailbox if mbox parameter is given */
            if (!imap_reopen($this->con, "{{$this->config['imap_hostname']}}" . $mbox))
                return 0;

        /* fetch header and body of the message */
        if (!($headers = imap_fetchheader($this->con, $uid, FT_UID)))
            return 0;
        if (!($body = imap_body($this->con, $uid, FT_UID)))
            return 0;

        /* open a separate connection to the archive server*/
        $con = imap_open("{{$this->config['arch_hostname']}:" .
            "{$this->config['arch_port']}" .
            "{$this->options}}" .
            "{$this->config['arch_mbox']}",
            $this->config['arch_username'],
            $this->config['arch_password']);

        //error_log("{$this->config['arch_hostname']}:"." {$this->config['arch_port']}"." {$this->options}"." {$this->config['arch_mbox']} ".$this->config['arch_username']);//.' '.$this->config['arch_password']);
        if (!$con)
            return 0;

        /* append message, close connection and return uid of saved message or 0 on error */
        if (imap_append($con, "{{$this->config['arch_hostname']}}" . $this->config['arch_mbox'], $headers . "\r\n" . $body)) {
            $check = imap_check($con);
            $ret = imap_uid($con, $check->Nmsgs);
        } else {
            error_log('Append error');
        }
        imap_close($con);
        return $ret;
    }

    function listmails($mbox = NULL, $sort_col = NULL, $sort_desc = false, $msgrange = '', $uids = 0, $prepare_for_link = '') {
        if ($mbox) {
            if (!imap_reopen($this->con, "{{$this->config['imap_hostname']}}" . $mbox))
                return false;
        }

        if (!($o = imap_check($this->con)))
            return false;


        $util = new olMailTreeList();

        if ($o->Nmsgs <= 0) {
            $tpl_info = new olTemplate('info.tpl');
            $tpl_info->output(array(
                "msg" => 'Keine Nachrichten im Ordner <b>' . $util->utf7toutf8($mbox) . '</b>'
            ));
            return true;
        } elseif ($msgrange === NULL) {
            $tpl_info = new olTemplate('info.tpl');
            $tpl_info->output(array(
                "msg" => 'Keine Nachrichten gefunden.'
            ));
            return true;
        }

        $mbox = urlencode($mbox);

        if ($msgrange == '')
            $msgrange = "1:{$o->Nmsgs}";

        $result = imap_fetch_overview($this->con, $msgrange, $uids);

        //Newest first
        if (!$sort_col) {
            $sort_col = 'date';
            $sort_desc = 1;
        }

        # sort by column
        $comparator = function ($a, $b) {
            return $a - $b;
        };
        switch ($sort_col) {
            case 'date':
                $getter = function ($obj) {
                    return strtotime($obj->date);
                };
                break;

            case 'size':
                $getter = function ($obj) {
                    return $obj->size;
                };
                break;

            case 'from':
                $getter = function ($obj) {
                    return olMail::_qpdecode($obj->from);
                };
                $comparator = 'strcmp';
                break;

            default:
                $getter = function ($obj) {
                    return olMail::_qpdecode($obj->subject);
                };
                $comparator = 'strcmp';
        }
        $sortFunc = function ($a, $b) use ($sort_desc, $comparator, $getter) {
            if ($sort_desc) {
                $tmp = $a;
                $a = $b;
                $b = $tmp;
            }
            return $comparator($getter($a), $getter($b));
        };
        usort($result, $sortFunc);


        $rows = '';

        if (!$prepare_for_link != '') {
            $tpl_row = new olTemplate('list_row.tpl');
        }
        foreach ($result as $_idx => $_o) {
            if ($prepare_for_link != '') {
                $rows .= '<div class="lmbContextLink" onclick="link_search_mail(' . urlencode($_o->uid) . ',\'' . $prepare_for_link . '\')" >
                           <div class="lmbContextItem">
                            <i border="0" style="height:12px;" class="lmb-icon-cus lmb-rel-add"></i>
                            <span class="lmbContextItemIcon">
                            ' . lmb_utf8_decode((isset($_o->subject)) ? $this->_qpdecode($_o->subject) : 'Kein Betreff') . '
                            </span>
                           </div>
                    </div>';
            } else {
                $rows .= $tpl_row->parse(array(
                    "mbox" => $mbox,
                    "uid" => urlencode($_o->uid),
                    "size" => sprintf("%3.2d", (int)($_o->size / 1024 + .5)),
                    "date" => date("d.m.Y H:i:s", strtotime($_o->date)),
                    "subject" => (isset($_o->subject)) ? $this->_qpdecode($_o->subject) : '',
                    "from" => $this->_qpdecode($_o->from),
                    "tr_class" => "gtabBodyTR",// gtabBodyTRColorA mail".(($_idx%2==1) ? "_even" : "_odd"),
                    "td_class" => "mail" . ($_o->seen ? "" : "_unread"),
                ));
            }
        }

        //$tpl = new olTemplate('list.tpl');
        //$tpl->output(array("mbox" => $mbox, "rows" => $rows));
        return ($rows != '') ? $rows : false;
    }

    function folder_create($mbox) {
        return imap_createmailbox($this->con, "{{$this->config['imap_hostname']}}" .
            mb_convert_encoding($mbox, "UTF7-IMAP", "UTF-8"));
    }

    function folder_delete($mbox) {
        return imap_deletemailbox($this->con, "{{$this->config['imap_hostname']}}" .
            mb_convert_encoding($mbox, "UTF7-IMAP", "UTF-8"));
    }

    function folder_rename($src, $dst) {
        $src_enc = mb_convert_encoding($src, "UTF7-IMAP", "UTF-8");
        $dst_enc = mb_convert_encoding($dst, "UTF7-IMAP", "UTF-8");
        return imap_renamemailbox($this->con,
            "{{$this->config['imap_hostname']}}{$src_enc}",
            "{{$this->config['imap_hostname']}}{$dst_enc}");
    }

    function search_mails($mbox, $range, $crit, $search, $link = '') {
        if (!imap_reopen($this->con, "{{$this->config['imap_hostname']}}" . $mbox))
            return false;
        if (!($o = imap_check($this->con)))
            return false;


        $list = imap_list($this->con, "{{$this->config['imap_hostname']}}" . $mbox, "*");

        if (($range != 'ALL' && $range != 'SEEN' && $range != 'UNSEEN' && $range != 'ANSWERED' && $range != 'UNANSWERED') || ($crit != 'KEYWORD' && $crit != 'FROM' && $crit != 'TO' && $crit != 'SUBJECT' && $crit != 'BODY')) {
            return false;
        }

        $criteria = $range . ' ' . $crit . ' "' . $search . '"';


        $uids = array();
        $rows = 'Keine Nachrichten gefunden';

        foreach ($list as $val) {
            if ($val == '' || !imap_reopen($this->con, $val))
                continue;
            if ($link != '' && $search === '*') {
                $rows = '';
                $rows .= $this->listmails(NULL, NULL, false, '', 0, '*');
            }
            $found = imap_search($this->con, $criteria, SE_UID);
            if (is_array($found)) {
                $rows = '';
                //$uids = array_merge($uids,$found);
                $msg = '';
                foreach ($found as $uid) {
                    $msg .= $uid . ',';
                }
                $msg = ($msg == '') ? NULL : trim($msg, ',');


                $rows .= $this->listmails(NULL, NULL, false, $msg, FT_UID, $link);
            }
        }
        //error_log(print_r($uids,1));

        //$uids = imap_search ( $this->con , $crit, SE_UID); //$range.' '.$criteria.' "'.$search.'"'

        /*$msg='';
        foreach ($uids as $uid)
        {
            $msg .= $uid.',';
        }

        $msg = ($msg == '') ? NULL : trim($msg,',');*/


        return $rows;
    }

    function get_uuid($resultOfGet) {
        return md5(
//            $this->config['imap_hostname'] .
//            $this->config['imap_port'] .
//            $this->config['imap_username'] .
            $resultOfGet['fromaddr'] .
            $resultOfGet['toaddr'] .
            $resultOfGet['date'] .
            $resultOfGet['subject'] .
            $resultOfGet['body']
        );
    }
}

?>