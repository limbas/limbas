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

/* -- configuration is done there ----------------------------------------- */
global $gtabid;
require_once('config.php');
$imap_cfg = lmb_getImapCfg($gtabid);

$gui_style =     (int) __default($imap_cfg, 'gui_style', 0);
$gui_behaviour = (int) __default($imap_cfg, 'gui_behaviour', 0);

/* -- there are absolutely no configurable parts below this line ---------- */
if (MAILER_DEBUG)
	ob_end_clean(); // TODO this is why no main_header is shown. Disabling debug causes layout to be distorted

#header("Content-Type: text/html; charset=UTF-8"); /* mandatory */
	
require_once('classes/php/olMimeMessage.class.php');
require_once('classes/php/olMailTreeList.class.php');
require_once('classes/php/olMail.class.php');
require_once('classes/php/olTemplate.class.php');

function output_index_template($content, $status=NULL, $title=NULL){
	global $imap_cfg, $session;

	if (!$title)
		$title = "Nachrichten";
	
	if (!$status)
		$status = MAILER_NAME . " " . MAILER_VERSION;
	
	$tpl = new olTemplate('index.tpl');

	$tpl->output(array(
		"content" => $content,
		"limbas_user_id" => $session["user_id"],
		"gui_style" => (int) __default($imap_cfg, 'gui_style', 0),
		"gui_behaviour" => (int) __default($imap_cfg, 'gui_behaviour', 0),
		"title"  => $title,
		"status" => $status,
	));
}

/* ------------------------------------------------------------------------
 * Put needed GET and POST variables into local scope with type casted,
 * assigning default values in case of absence.
 */
function __default($arr, $name, $value){
	return isset($arr[$name]) ? $arr[$name] : $value;
}



foreach (array('part', 'sort_desc') as $var)
	$$var = (int) __default($_GET, $var, 0);

foreach (array('print', 'stream', 'arch') as $var)
	$$var = (bool) __default($_GET, $var, false);

foreach (array('rename', 'to', 'delete', 'create', 'create_root', 'find') as $var)
	$$var = (string) __default($_POST, $var, NULL);

foreach ( array('mbox', 'uid', 'del_uid', 'del_range', 'reply_uid', 'fwd_uid', 'move', 'sort_col','find','range','crit','search') as $var)
	$$var = (string) __default($_GET, $var, NULL);


if ($find) /* TODO: implement IMAP message search! */
	$status = "Suchergebniss für '<b>$find</b>'.";

if ($stream){ /* stream a message part inline. */
	require_once('classes/php/olIMAPdirect.class.php');

	$port = (isset($imap_cfg["imap_port"]) && intval($imap_cfg["imap_port"]))
			? $imap_cfg["imap_port"] : 143;
			
	$IMAP = new olIMAPdirect($imap_cfg["imap_username"], $imap_cfg["imap_password"],
		$imap_cfg["imap_hostname"], $port);
			
	if (!$IMAP->connect()){
		header("HTTP/1.1 502 Server Error");
		echo "Error: ".$IMAP->last_error();
		$ok = false;
	} else {
		if ($IMAP->fetch_part($mbox, $uid, $part)!=""){
			header("HTTP/1.1 503 Server Error");
			echo "Error: ".$IMAP->last_error();
			$ok = false;
		} else
			$ok = true;

		$IMAP->disconnect();
	}
	exit(1);
}

/* ------------------------------------------------------------------------
 * 1. connect to IMAP server.
 *
 * 2. execute one of this functions:
 *     - list all folders.
 *     - list messages of a folder.
 *     - view a message.
 *     - print a message.
 *     - reply to a message.
 *     - forward a message.
 *     - delete a message.
 *     - move a message into another folder.
 *     - retrieve a particular attachment.
 *
 * 3. print informal message / issue 5xx HTTP Status Code on failure.
 *
 * 4. disconnect from IMAP server.
 */
if (((bool)__default($_GET, 'folders', false)) || $mbox){
	if (!function_exists("imap_open")){
		header("HTTP/1.1 500 Server Error");
		echo "Unterstützung für IMAP innerhalb PHP ist erforderlich!";
		exit(1);
	}
	if (!$imap_cfg){
		header("HTTP/1.1 500 Server Error");
		echo 'Keine gültige <a href="main.php?action=user_change">'.
			'Nachrichten-Konfiguration</a> gefunden!';
		exit(1);
	}

	/* instantiate olMail object and exit if connection failed. */
	$mail = new olMail($imap_cfg);
	
	if (!$mail->is_connected()){
		header("HTTP/1.1 500 Server Error");
		echo "Fehler: ".imap_last_error();
		exit(1);
	}
        
        

        header('Content-Type: text/html; charset=utf-8');

	
	if ($mbox){
		if ($uid){
			if ($part){ /* get attachment */
				 /* conventional (slow, stupid) method. */
				$ok = $mail->getpart($uid, $part, $mbox);
			} elseif ($move){ /* move message into another mailbox */
				if (($ok = $mail->movemail($uid, $move, $mbox))){
					echo "Nachricht wurde verschoben.";
					/* hide mail from list */
					echo <<<EOD
<script language="javascript" type="text/javascript">
document.getElementById('$uid').style.display = 'none';
</script>
EOD;
				} else
					echo "Fehler beim verschieben der Nachricht!";
			} elseif ($arch){ /* save message in archive mailbox */
				$use_json = (bool) __default($_GET, 'json', false);
				if (($archive_uid = $mail->archmail($uid, $mbox))>0)
					echo ($use_json)
						? "{uid:{$archive_uid}}"
						: "Nachricht wurde archiviert. (uid: {$archive_uid})";
				else
					echo "Fehler beim archivieren der Nachricht!";
				$ok = ($archive_uid != 0) ? true : false;
			} else { /* display (print) message. */
				if ($imap_cfg["gui_style"] == MAILER_GUI_STYLE_WINDOW){
					ob_start();
					$ok = $mail->getmail($uid, $mbox, $print);
					output_index_template(ob_get_clean(), NULL,
						"Nachrichten für {$imap_cfg['full_name']} &lt;{$imap_cfg['email_address']}&gt;");
				} else
					$ok = $mail->getmail($uid, $mbox, $print);
			}
		} else if ($reply_uid){
			if ($imap_cfg["gui_style"] == MAILER_GUI_STYLE_WINDOW){
				ob_start();
				$ok = $mail->replymail($reply_uid, $mbox);
				output_index_template(ob_get_clean());
			} else
				$ok = $mail->replymail($reply_uid, $mbox);
		} else if ($fwd_uid){
			$_fwd_message = "Nachricht.eml";
			$ok = $mail->forwardmail($fwd_uid, $mbox, $_fwd_message);
							
			$tpl_fwd = new olTemplate('compose.tpl');
			$tpl_fwd->add_array(array(
				"fromaddr" => "{$imap_cfg['full_name']} &lt;{$imap_cfg['email_address']}&gt;",
				"fwd_message" => $_fwd_message,
				"gui_style" => $gui_style,
				"toaddr" => "", "subject" => "", "body" => ""));

			if ($imap_cfg["gui_style"] == MAILER_GUI_STYLE_WINDOW){
				output_index_template($tpl_fwd->parse());
			} else
				$tpl_fwd->output();

		} else if ($del_uid){
				/* delete mail from IMAP server. */
				if (($ok = $mail->delmail($del_uid, $mbox))){
					$use_json = (bool) __default($_GET, 'json', false);
					if ($use_json) /* output JSON string */
						echo "{uid:{$del_uid}}";
					else /* output info and hide mail from list */
						echo <<<EOD
Nachricht wurde gelöscht.
<script language="javascript" type="text/javascript">
document.getElementById('$del_uid').style.display = 'none';
</script>
EOD;
				} else
					echo "Fehler beim löschen der Nachricht!";
		} else if ($del_range){
			/* delete a selection of mails from IMAP server. */
			$del_range = preg_replace('/,$/','', $del_range);
			$_del_range = explode(',', $del_range);
			
			$_cnt = count($_del_range);
			switch($_cnt){
				case 1:
					$_msg = "Nachricht wurde gelöscht.";
					break;
				default:
					$_msg = "{$_cnt} Nachrichten wurden gelöscht.";
			}
			if /* (($ok = true)) */ (($ok = $mail->delmail($del_range, $mbox))){
				$use_json = (bool) __default($_GET, 'json', false);
				if ($use_json) /* output JSON string */
					echo "{uid:{$del_range}}";
				else /* output info and hide mail from list */
					echo <<<EOD
$_msg
<script language="javascript" type="text/javascript">//<!--
try {
	var _del_range = '$del_range';
	var _del_uids = _del_range.split(",");
	for (var idx=0; idx < _del_uids.length; idx++)
		document.getElementById(_del_uids[idx]).style.display = 'none';
} catch(e) {}
//--></script>
EOD;
			} else
				echo "Fehler beim löschen der Nachricht(en)!";
		} else
                {
                    if ($find)
                    {
			$rows = $mail->search_mails($mbox,$range,$crit,$search);
                    }
                    else
                    {
                        $rows = $mail->listmails($mbox, $sort_col, $sort_desc);
                    }
                    
                    
                    if ($rows === true)
                    {
                        $ok = true;
                    }
                    elseif ($rows !== false)
                    {
                        $tpl = new olTemplate('list.tpl');
                        $tpl->output(array("mbox" => $mbox, "rows" => $rows));
                        $ok = true;
                    }
                    else
                    {
                        $ok = false;
                    }
                }
	} else
		$ok = $mail->listfolders();

	if (!$ok){
		header("HTTP/1.1 501 Server Error");
		echo "Fehler: ".imap_last_error();
	}
	
	$mail->disconnect();
	exit(0);
}

/* ------------------------------------------------------------------------
 * handle mail composition and delivery of messages.
 */
if ((bool)__default($_GET, 'compose', false)){
	$tpl = new olTemplate('compose.tpl');

	$buf = $tpl->parse(array(
		"gui_style" => $gui_style,
		"fromaddr" => "{$imap_cfg['full_name']} &lt;{$imap_cfg['email_address']}&gt;",
		"toaddr" => "",	"subject" => "", "body" => ""));
	
	if ($imap_cfg["gui_style"] == MAILER_GUI_STYLE_WINDOW)
		output_index_template($buf);
	else
		echo $buf;

	exit(0);
} else if ((bool)__default($_POST, 'compose', false)){
	$util = new olMailTreeList();
	
	$fromaddr = (string)__default($_POST, 'fromaddr',
		"{$imap_cfg['full_name']} &lt;{$imap_cfg['email_address']}&gt;");
		
	$toaddr =  (string)__default($_POST, 'toaddr', NULL);
	$subject = (string)__default($_POST, 'subject', "<no subject>");
	$body =    (string)__default($_POST, 'body', "");
		
	$mail_headers  = "From: {$fromaddr}\n";

	/* if desired, append the 'Organisation' mailheader. */
	if (isset($imap_cfg['organisation']))
		$mail_headers .= "Organisation: {$imap_cfg['organisation']}\n";
		
	/* always add mailer version mailheader. */
	$mail_headers .= "X-Mailer: ".MAILER_NAME." ".MAILER_VERSION."\n";

	$mm = new olMimeMessage($mail_headers, $body);
	/* $mm->addAttachment("LIMBAS Business Solutions.png"); */

	$tpl_status = new olTemplate('status_once.tpl');
	$tpl_status->output();
	
	$tpl_status = new olTemplate('status.tpl');
	if (!$toaddr || $toaddr==""){
		$tpl_status->output(array("message" => "Nachricht konnte nicht versandt werden! (1)",
			"image" => "error.gif"));
		echo "Es wurde keine Empfängeradresse angegeben!";
		exit(1);
	}

	$tpl_status->output(array("message" => "Verbindung zum Mailserver wird hergestellt...",
		"image" => "wait.gif"));
	ob_flush();
	flush();

	/* instantiate olMail object and exit if connection failed. */
	$mail = new olMail($imap_cfg);

	if (!$mail->is_connected()){
		$tpl_status->output(array("message" => "Nachricht konnte nicht versandt werden! (2)",
			"image" => "error.gif"));
		echo "Fehler: ".imap_last_error();
		exit(1);
	}
	
	/* add all attachments ... with status. */
	foreach($_POST as $n=>$v){
		if (preg_match('/^attachment_/', $n)){
			$tpl_status->output(array("message" =>
				"Datei <b>$v</b> wird angehängt...","image" => "wait.gif"));
			ob_flush();
			flush();
			$mm->addAttachment($v, $mail->temp_foldername());
		}
	}
	$body = $mm->prepare($mail_headers);
	$toaddr = $util->make_mailaddr($toaddr);

	/* actually send the message... */
	$tpl_status->output(array("message" => "Nachricht wird versandt...",
		"image" => "wait.gif"));
	ob_flush();
	flush();

	$ret = mail($toaddr, $util->quote_utf8($subject), $body, $mail_headers);

	/* save a copy of the message if sent successfully. */
	if ($ret){
		$tpl_status->output(array(
			"message" => "Kopie der Nachricht wird gespeichert...",
			"image" => "wait.gif"));
		ob_flush();
		flush();
		$saved_uid = $mail->savemail("Sent",
			$mail_headers."To: {$toaddr}\nSubject: ".
			$util->quote_utf8($subject)."\n", $body);
	}
	
	$mail->disconnect();

	/* print overall status of mail composition and delivery. */
	if (!$ret){
		$_status_msg = "Nachricht konnte nicht versandt werden! (3)";
		$_status_image = "error.gif";
	} else {
		$_status_msg = "Nachricht wurde versandt.";
		$_status_image = "info.png";
		$_status_autoclose = 1500; /* msec */
	}
	
	$tpl_status->output(array( "message" => $_status_msg,
		"image" => $_status_image,
		"autoclose" => (isset($_status_autoclose)) ? $_status_autoclose : NULL
	));
	exit(0);
}

/* ------------------------------------------------------------------------
 * handle AJAX popup request by parsing and outputting the corresponding
 * template.
 */
if ((bool) __default($_GET, 'popup', false)){
	$util = new olMailTreeList();
	unset($tpl);
        
	foreach (array('rename', 'delete', 'create', 'find') as $var){
		if (!($$var = (string) __default($_GET, $var, NULL)))
			continue;
		$tpl = new olTemplate("popup_{$var}.tpl");
                $tpl->output(array("mbox" => $util->utf7toutf8($$var)));
	}

	if (!isset($tpl)){
		header("HTTP/1.1 500 Server Error");
		echo "<h1>Unsupported AJAX request!</h1><br><pre><xmp>\$_GET = ";
		var_dump($_GET); echo "\n\$_POST = "; var_dump($_POST);
		echo "</xmp></pre>";
	}
	exit(0);
}

/* ------------------------------------------------------------------------
 * 1. connect to IMAP server.
 *
 * 2. execute one of this functions:
 *     - create folder.
 *     - delete folder.
 *     - rename (move) folder.
 *
 * 3. set status to informal message / issue 5xx HTTP Status Code on failure.
 *
 * 4. disconnect from IMAP server and continue with output of main HTML page.
 */
if ($create || $delete || $rename){
	/* instantiate olMail object and exit if connection failed. */
	$mail = new olMail($imap_cfg);
	
	if (!$mail->is_connected()){
		header("HTTP/1.1 500 Server Error");
		echo "Fehler: ".imap_last_error();
		exit(1);
	}
	
	if ($create && $create_root){
		$ok = $mail->folder_create("{$create_root}.{$create}");
		if ($ok)
			$status = "Ordner <b>$create</b> wurde erstellt.";
	} else if ($delete){
		$ok = $mail->folder_delete($delete);
		if ($ok)
			$status = "Ordner <b>$delete</b> wurde gelöscht.";
	} else if ($rename && $to){
		$ok = $mail->folder_rename($rename, $to);
		if ($ok)
			$status = "Ordner <b>$rename</b> wurde umbenannt in <b>$to</b>.";
	}

	if (!$ok){
		header("HTTP/1.1 501 Server Error");
		$status = "Fehler: ".imap_last_error();
	}
	
	$mail->disconnect();
}

/* ------------------------------------------------------------------------
 * parse and output main template.
 */
$tpl_main = new olTemplate('main.tpl');

if (MAILER_DEBUG){
	/* print the HTML document root ourself, using index.tpl and 
	 * force script termination. */
	output_index_template($tpl_main->parse(array("gui_style" => $gui_style)),
		(isset($status)) ? $status : null,
		"Nachrichten für {$imap_cfg['full_name']} &lt;{$imap_cfg['email_address']}&gt;");
	exit(0);
}

/* make the underlying web application responsible for outputting a proper
 * HTML document root - see index.tpl for the minimum requirements.
 *
 * continue executing in case this script is included from somewhere else.
 */
$tpl_main->output(array(
	"gui_style" => $gui_style,
	"gui_behaviour" => $gui_behaviour,
	"title"  => "Nachrichten für {$imap_cfg['full_name']} &lt;{$imap_cfg['email_address']}&gt;",
	"status" => (isset($status)) ? $status : MAILER_NAME." ".MAILER_VERSION,
));

?>
