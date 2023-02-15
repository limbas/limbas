<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */




function dyns_extFileManager($params){
	global $gtab;
	global $gfield;
	global $session;
	global $umgvar;
	global $ufile;
	global $gfile;
	global $filestruct;
	global $farbschema;
	global $LINK;
	global $ffilter;
	
	require_once(COREPATH . 'gtab/gtab.lib');
	require_once(COREPATH . 'extra/explorer/filestructure.lib');
	require_once(COREPATH . 'extra/explorer/explorer_main.lib');
	require_once(EXTENSIONSPATH . 'system/ext_type.inc');

	$gtabid = $params["gtabid"];
	$field_id = $params["gfieldid"];
	$ID = $params["ID"];
	$edittype = $params["edittype"];
    $gformid = $params["gformid"];
    $vuniqueid = $gfield[$gtabid]["form_name"][$field_id]."_".$gformid;

	$verkn = set_verknpf($gfield[$gtabid]["verkntabid"][$field_id],$gfield[$gtabid]["verknfieldid"][$field_id],0,0,0,0,0);
	$verkn["md5tab"] = $gfield[$gtabid]["md5tab"][$field_id];
	$verkn["uniqueid"] = $gfield[$gtabid]["form_name"][$field_id];
	
	# ----------- Edit Permission -----------
	if($gfield[$gtabid]["perm_edit"][$field_id] AND $edittype != 2){$noedit = 0;}
	# ----------- Editrule -----------
	if($gfield[$gtabid]["editrule"][$field_id]){
		$noedit = check_GtabRules($ID,$gtabid,$field_id,$gfield[$gtabid]["editrule"][$field_id]);
	}

    # Unter-Ordner Ebene händisch ausgewählt
	if($params["LID"]){
		$LID = $params["LID"];
		$session["ExtFileManager"]["LID"][$verkn["uniqueid"]] = $LID;
    }elseif ($session["ExtFileManager"]["LID"][$verkn["uniqueid"]]) {
        $LID = $session["ExtFileManager"]["LID"][$verkn["uniqueid"]];
    # Root-Ordner aus Formular / Verknüpfungsparameter
	}elseif($GLOBALS['filter']["ext_RelationFields"]['formid'][$vuniqueid]){
        $LID = $filter["ext_RelationFields"]['formid'][$vuniqueid];
    # Root-Ordner default
    }else{
		$LID = $gfield[$gtabid]["file_level"][$field_id];
	}

	# get global ffilter
	$filter = get_userShow($LID,1);

	# viewmode
	if(is_numeric($params["viewmode"])){
		$ffilter['viewmode'][$LID] = $params["viewmode"];
	# show fields
	}elseif($params["viewmode"]){
		if($gfile['show'][$LID][$params["viewmode"]]){
			unset($gfile['show'][$LID][$params["viewmode"]]);
		}else{
			$gfile['show'][$LID][$params["viewmode"]] = 1;
		}
		$save_userShow = 1;
		$ffilter['viewmode'][$LID] = 1;
	}
	
	# viewmode
	$show_details = $ffilter['viewmode'][$LID];

	# show recursive
	if($params["viewsub"]){
		if($session["ExtFileManager"]["viewsub"][$verkn["uniqueid"]]){
			$show_sub = 0;
		}else{
			$show_sub = $params["viewsub"];
		}
		$session["ExtFileManager"]["viewsub"][$verkn["uniqueid"]] = $show_sub;
	}else{
		$show_sub = $session["ExtFileManager"]["viewsub"][$verkn["uniqueid"]];
	}
	
	# order by
	if($params['orderfield']){
		if($ffilter["order"][$LID][0] == $params['orderfield'] AND !$ffilter["order"][$LID][1]){
			$filter["order"][$LID][1] = 'DESC';
			$ffilter["order"][$LID][1] = 'DESC';
		}else{
			$filter["order"][$LID][1] = null;
			$ffilter["order"][$LID][1] = null;
		}
		$filter["order"][$LID][0] = $params['orderfield'];
		$ffilter["order"][$LID][0] = $params['orderfield'];

		$save_userShow = 1;
	}else{
		$filter["order"][$LID] = $ffilter["order"][$LID];
	}
	
	# new search
	if($params['searchfield'] == 1){
		if($ffilter['search'][$LID]){
			unset($ffilter['search'][$LID]);
			$searchreset = 1;
		}else{
			$ffilter['search'][$LID] = 1;
		}
	}elseif($params['searchfield']){
		$searchfield = explode('_',$params['searchfield']);
		if($searchfield[1] AND $searchfield[2]){
			$filter["gsr"][$searchfield[1]][$searchfield[2]][0] = $params['searchval'];
			$ffilter[$searchfield[1].'_'.$searchfield[2]][$LID][0] = $params['searchval'];
		}
	}
	# default search
	foreach ($gfile['show'][$LID] as $key1 => $val1){
		if($ffilter[$key1][$LID]){
			$searchfield = explode('_',$key1);
			if($searchreset){
				$ffilter[$key1][$LID] = null;
			}else{
				$filter["gsr"][$searchfield[0]][$searchfield[1]] = $ffilter[$key1][$LID];
			}
		}
	}
	
	# save global ffilter
	if($save_userShow){
		save_userShow($LID,1);
	}

	# root level
	$rootlevel = $filestruct["level"][$gfield[$gtabid]["file_level"][$field_id]];


	# ---------------- actions ---------------

	# add folder
	if($params["addfolder"] AND $LINK[119] AND !$noedit){
		add_file($LID,$params["addfolder"]);
		$show_sub = 0;
		$session["ExtFileManager"]["viewsub"][$verkn["uniqueid"]] = 0;
	# add relation
	}elseif($params["addrelation"] AND $LINK[157] AND !$noedit){
		$show_sub = 0;
		$session["ExtFileManager"]["viewsub"][$verkn["uniqueid"]] = 0;
		if(is_numeric($params["addrelation"])){
			$addrelation[0] = $params["addrelation"];
		}else{
			$addrelation = explode("-",$params["addrelation"]);
		}
		foreach ($addrelation as $key => $value){
			if(is_numeric($value)){
				$rid = $value;
				$rtyp = "d";
			}else{
				$addrel = explode("_",$value);
				$rtyp = $addrel[0];
				$rid = $addrel[1];
			}
			if(is_numeric($rid) AND $rtyp == "d"){
				$verkn_ = set_verknpf($gtabid,$field_id,$ID,$rid,0,0,0);
				$verkn_["linkParam"]["LID"] = $LID;
				set_joins($gtabid,$verkn_);
			}
		}
	# drop relation / delete file or folder
	}elseif($params["droprelation"] AND $LINK[171] AND $filestruct["del"][$LID] AND !$noedit){
		if(is_numeric($params["droprelation"])){
			$droprelation[0] = $params["droprelation"];
		}else{
			$droprelation = explode("-",$params["droprelation"]);
		}
		foreach ($droprelation as $key => $value){
			$droprel = explode("_",$value);
			$rtyp = $droprel[0];
			$rid = $droprel[1];
			if(is_numeric($rid)){
				# folder
				if($rtyp == "f" AND $params["delrelation"]){
					delete_dir($rid);
				# file
				}elseif($rtyp == "d"){
					lmb_StartTransaction();
					$verkn_ = set_verknpf($gtabid,$field_id,$ID,0,$rid,0,0);
						if(set_joins($gtabid,$verkn_)){
						# delete
						if($params["delrelation"]){
							if(del_data($gtab["argresult_id"]["LDMS_FILES"],$rid,"delete")){
								lmb_EndTransaction(1);
							}else{
								lmb_EndTransaction(0);
								continue;
							}
						}
						lmb_EndTransaction(1);
					}else{
						lmb_EndTransaction(0);
					}
				}
			}
		}
	}

	if(!$show_sub){$show_sub = 0;}else{$show_sub = 1;}

	# ----- Ordnerstruktur ------
	get_filestructure();
	$level = $filestruct["level"][$LID];
	$typ = 7;
	$file_path = lmb_getPathFromLevel($level,$LID);
	array_splice($file_path,-4);
	
	$filter["viewmode"][$LID] = 5;
	$filter["nolimit"][$LID] = 1;
	$filter["page"][$LID] = 1;
	$filter["sub"] = $show_sub;
	$verkn["parent_table"] = $gtab["table"][$gtabid];
	$verkn["parent_datid"] = $ID;

	# Mimetype filter
	if($show_details == 3){
		$filter[$gtab["argresult_id"]["LDMS_FILES"]."_13"][$LID] = array("image");
	}else{
		$filter[$gtab["argresult_id"]["LDMS_FILES"]."_13"][$LID] = null;
	}

	# --- Abfrage starten ---
	if($query = get_fwhere($LID,$filter,$typ,$verkn)){
		if(!$ffile = get_ffile($query,$filter,$LID,$typ)){return false;}
	}

	echo "<input type=\"hidden\" id=\"ExtFileManager_LID_".$gtabid."_".$field_id."\" value=\"$LID\">";
	echo "<TABLE BORDER=\"0\" ID=\"filetab\" CELLSPACING=\"0\" CELLPADDING=\"1\" STYLE=\"width:100%;border-collapse:collapse;\">";

	if($file_path){echo "<TR><TD colspan=\"10\"><DIV style=\"background-color:".$farbschema["WEB11"]."\">&nbsp;<i class=\"lmb-icon lmb-icon-8 lmb-folder\"></i>&nbsp;<INPUT TYPE=\"TEXT\" STYLE=\"border:none;width:94%;height:17px;background-color:".$farbschema["WEB11"].";color:#777777;z-index:1;\" VALUE=\"/".implode("/",$file_path)."\" READONLY></DIV></TD></TR>";}

	if($show_details == 3){
		extendedExplPicShow($ffile,$gtabid,$field_id,$ID,$params["gformid"],$params["formid"],$params["picshow"],$edittype);
	}elseif($show_details == 4){
		explPicSummery($ffile,$gtabid,$field_id,$ID,$params["gformid"],$params["formid"]);
	}else{
		if($ffilter['search'][$LID]){
			explSearch($LID,$LID,$ffilter,$gtabid,$field_id,$ID);
		}
		explHeader($LID,$LID,$ffilter,$gtabid,$field_id,$ID);
		if($show_sub != 1){explFolders($LID,$LID,0,$level,$filter,"$gtabid,$field_id,$ID",$rootlevel);}
		explFiles($LID,$LID,$level,$ffile,$filter);
	}

	echo "</TABLE>";
}



function LmbExtVersionDiff($pdf,$ID,$report,$el){
	global $gfield;
	global $gtab;
	global $db;
	global $lang;
	global $umgvar;
	global $session;

	$params = $GLOBALS["tmp"]["params"];

	#if(!$GLOBALS["LINK"][205]){die();}

	require_once(COREPATH . 'gtab/gtab.lib');
	require_once(COREPATH . 'gtab/gtab_type_erg.lib');
	# userdefined Table-Tags
	#$require = "EXTENSIONS/system/report_tab_tags.inc";
	#require_once($require);
	$pdf->SetStyle("del", "arial", "BI", 14, "203, 0, 48");


	$ID = $params["ID"];
	$VID = $params["VID"];
	$gtabid = $params["gtabid"];
	$form_id = $params["form_id"];

	# DEFAULT TABLE SETTINGS
	$default = GetDefaultType();
	$columns = 3;

	# we initialize the table class
	$pdf->Table_Init($columns, true, true);
	$pdf->Set_Table_Type($default["table"]);

	//DEFAULT TABLE HEADER SETTINGS
	for($i=0; $i<$columns; $i++) $header_type[$i] = $default["header"];

    //DEFAULT TABLE DATA SETTINGS
	$data_type = Array();//reset the array
	for ($i=0; $i<$columns; $i++) $data_type[$i] = $default["data"];

	$header_type[0]['WIDTH'] = 100;
	$header_type[0]['TEXT'] = ' ';
	$header_type[1]['WIDTH'] = 100;
	$header_type[1]['TEXT'] = ' ';
	$header_type[2]['WIDTH'] = 300;
	$header_type[2]['TEXT'] = ' ';

	//set the header type
	$pdf->tagmod = 1;
	$pdf->Set_Header_Type($header_type);
	$pdf->Set_Table_SplitMode(false);
	$pdf->Draw_Header();


	if($gtab["raverkn"][$gtab["verkn"][$gtabid]]){
		foreach ($gtab["raverkn"][$gtab["verkn"][$gtabid]] as $tabid){
			if($gtab["tab_id"][$tabid]){

				$data[0]['TEXT'] = $gtab["desc"][$tabid];
				#$data[0]['COLSPAN'] = 3;
				$pdf->Set_Data_Type($data_type);
				$pdf->Set_Table_SplitMode(false);
				$pdf->Draw_Data($data);

				if($ID){
					$gsr[$tabid]["ID"] = $ID;
					$filter["viewversion"][$tabid] = 1;
					$gresult1 = get_gresult($tabid,1,$filter,$gsr,0);
				}

				if($VID){
					$gsr[$tabid]["ID"] = $VID;
					$filter["viewversion"][$tabid] = 1;
					$gresult2 = get_gresult($tabid,1,$filter,$gsr,0);
				}

				# Default Ausgabe
				$isdiff = 0;
				foreach ($gfield[$tabid]["id"] as $key => $value){
					$fname = "cftyp_".$gfield[$tabid]["funcid"][$key];
					if(function_exists($fname) AND $gfield[$tabid]["data_type"][$key] != 22 AND $gfield[$tabid]["copy"][$key]){
						$result1 = $fname(0,$key,$tabid,4,$gresult1,0);
						$result2 = $fname(0,$key,$tabid,4,$gresult2,0);

						# Array Diff
						if(is_array($result1) AND is_array($result2)){
							# Sonderfall Verknüpfung
							unset($result1["id"]);
							unset($result2["id"]);
							unset($result1["name"]);
							unset($result2["name"]);

							$diff = arrayDiff($result1,$result2);
							# Text Diff
						}elseif(!is_array($result1)){
							$diff = textDiff($result1,$result2);
						}
						if($diff){
							$data[0]['TEXT'] = " ";
							$data[1]['TEXT'] = $gfield[$tabid]["spelling"][$key];
							$data[2]['TEXT'] = str_replace("<br>","\n",$diff);
							$data_type[2]['LN_SIZE'] = 10;

							$pdf->Set_Data_Type($data_type);
							$pdf->Set_Table_SplitMode(false);
							$pdf->Draw_Data($data);
						}
					}
				}
			}
		}
	}

	$pdf->Draw_Data(array());
}

function dyns_extAddRelMessages($params){
	global $gtabid,$gfield;

	$gtabid = $gfield[$params["gtabid"]]["verkntabid"][$params["fldid"]];

	$mbox = $params["mbox"];
	$uids = explode(",",rtrim($params["ids"],","));

	if (lmb_count($uids)==0){
		echo "No mails in a queue";
		return;
	}
	$uids_count = lmb_count($uids);

	require_once("ext_limbasMail.php");

	$mail = new limbasMail($params["gtabid"],$params["fldid"],$params["id"],$gtabid);

	if ($mail){
		if (lmb_count($mail->error) > 0){
			echo utf8_decode(implode("<br>",$mail->error));
		} else {
			foreach ($uids as &$uid){
				$uid = $mail->arch($uid,$mbox);
			}
		}
		$mail->disconnect();
	}
#error_log("[".__FILE__."][".__LINE__."][".__FUNCTION__."]\n".print_r($uids,1));
	$gtabid = $params["gtabid"];
	if (lmb_count(array_filter($uids)) != $uids_count){
		echo "Ausgewählte Nachrichten konnten nicht verknüpft werden.";
		return;
	}

	dyns_extMessagesDisplay($params);
}

function dyns_extSendMessage($params){
	global $gtabid,$gfield;

	$gtabid = $gfield[$params["gtabid"]]["verkntabid"][$params["fldid"]];

	require_once("ext_limbasMail.php");
	$mail = new limbasMail($params["gtabid"],$params["fldid"],$params["id"],$gtabid);

	if ($mail){
		if (lmb_count($mail->error) > 0){
			echo utf8_decode(implode("<br>",$mail->error));
		} else {
			$uid = $mail->send();
		}
		$mail->disconnect();
	}

	$gtabid = $params["gtabid"];
	if (lmb_count($mail->error) > 0){
		echo implode('<br>',$mail->error);
		return;
	}
        
    if (!$uid){
		echo "Nachricht wurde versendet, konnte aber nicht verknüpft werden.";
		return;
	}

	echo <<<EOD
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<meta http-equiv="Cache-Control" content="no-cache, must-revalidate">
	<meta http-equiv="Expires" content="0">
	<meta name="robots" content="noindex, nofollow">
	<title>Send message as {$mail->imap_cfg['full_name']}</title>
	<script src="extra/messages/classes/js/olEvent.js" language="javascript" type="text/javascript"></script>
	<script src="extra/messages/classes/js/olAjax.js" language="javascript" type="text/javascript"></script>
	<script src="assets/js/extra/extensions/ext_messages.js" language="javascript" type="text/javascript"></script>
<script language="javascript" type="text/javascript">//<!--
	var _params = ["{$params["gtabid"]}","{$params["fldid"]}","{$params["id"]}","{$params["edittype"]}","{$params["gformid"]}"];

	function _$(){return window.opener.document.getElementById(arguments[0]);};
	function refresh(e){
		var _el = (e.target) ? e.target : e.srcElement;
		var el = _$('mail_status_'+_params[0]+"_"+_params[1]);

		var ajax = new olAjax("main_dyns.php?actid=extMessagesDisplay"
			+"&gtabid="+_params[0]+"&fldid="+_params[1]+"&id="+_params[2]
			+"&edittype="+_params[3]+"&gformid="+_params[4]);

		ajax.onComplete = function(content){
			_$("extRelationFieldsTab_"+_params[0]+"_"+_params[1]).innerHTML = content;
			window.opener.mail_check();
			window.close();
		};
		ajax.onError = function (content){
			el.innerHTML += "<br>Error:"+content + (arguments[1] ? " ("+arguments[1]+")" : '');
		};

		ajax.send('msgs=' + content, el);
	}

	olEvent().observe(window, "load", refresh);
//--></script>
</head>
<body>

</body>
</html>
EOD;
}

function dyns_extComposeMessage($params){
	global $gtabid,$gfield,$umgvar,$session;

	$gtabid = $gfield[$params["gtabid"]]["verkntabid"][$params["fldid"]];

	require(COREPATH . 'extra/messages/config.php');
	$imap_cfg = lmb_getImapCfg($gtabid);

	$id = intval($session['user_id']);
	echo <<<EOD
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<meta http-equiv="Cache-Control" content="no-cache, must-revalidate">
	<meta http-equiv="Expires" content="0">
	<meta name="robots" content="noindex, nofollow">
	<title>Compose message as {$imap_cfg['full_name']}</title>
	<link rel="stylesheet" type="text/css" href="assets/css/{$session['legacyCss']}?v={$umgvar['version']}">
</head>
<body>
<form name="compose_form" method="POST" action="main_dyns.php?actid=extSendMessage&amp;gtabid={$params["gtabid"]}&amp;fldid={$params["fldid"]}&amp;id={$params["id"]}&amp;edittype={$params["edittype"]}">
	<input type="hidden" name="compose" value="1">
	<table class="lmbfringeGtabBody" width="100%" height="100%" border="0" cellpadding="0" cellspacing="1">
		<tr class="gtabHeaderTitleTR">
			<td width="20%" align="right" class="mail_desc_name" height="22">From:</td>
			<td width="80%">
				<select name="fromaddr" style="border:solid 1px silver; border-right:0; width:100%;">
				<option>{$imap_cfg['full_name']} &lt;{$imap_cfg['email_address']}&gt;</option>
				</select>
			</td>
		</tr>
		<tr class="gtabHeaderTitleTR">
			<td align="right" class="mail_desc_name" height="22">To:</td>
			<td><input name="toaddr" type="text" size="40"
				style="border:solid 1px silver; border-right:0; width:100%;"
				maxlen="320" value=""></td>
		</tr>
		<tr class="gtabHeaderTitleTR">
			<td align="right" class="mail_desc_name" height="22">Subject:</td>
			<td><input name="subject" type="text" size="40"
				style="border:solid 1px silver; border-right:0; width:100%;"
				maxlen="255" value=""></td>
		</tr>
		<tr class="gtabHeaderTitleTR">
			<td colspan="2" style="
				border-top:solid 1px silver;
				border-bottom:solid 1px silver;
			">
			<table width="100%" height="100%" border="0" cellpadding="0" cellspacing="0">
				<tr style="height:100%;" id="compose_body">
					<td id="mail_body_panel">
						<textarea id="mail_body" name="body" cols="20" rows="5"
							style="width:100%;height:100%;padding:2px;border:0px;"></textarea>
					</td>
				</tr>
			</table>
			</td>
		</tr class="gtabHeaderTitleTR">
		<tr class="gtabHeaderTitleTR">
			<td colspan="2" align="center" height="22">
				<input type="submit" class="submit" value="Send message">
			</td>
		</tr>
	</table>
</form>
</body>
</html>
EOD;
}

function dyns_extDelRelMessages($params){
#error_log("[".__FILE__."][".__LINE__."][".__FUNCTION__."]\n".print_r($params,1));

	require_once(COREPATH . 'gtab/gtab.lib');
	global $gtab,$gtabid,$gfield;

	$not_deleted = "Ausgewählte Nachrichten konnten nicht gelöscht werden.";
	$gtabid = $gfield[$params["gtabid"]]["verkntabid"][$params["fldid"]];

	$grel = set_verknpf($params["gtabid"],$params["fldid"],$params["id"],0,0,1,0);
	$gext = array("where" => array("{$gtab["table"][$gtabid]}.id in (".rtrim($params["rids"]).")"));
	$gresult = get_gresult($gtabid,1,array("nolimit"=>array($gtabid=>1),"anzahl"=>array($gtabid=>"all")),null,$grel,null,null,$gext);

	if(!isset($gresult[$gtabid][6]) || (!is_array($gresult[$gtabid][6])) || lmb_count($gresult[$gtabid][6])==0){
		echo "No mails selected";
		return;
	}

	$uids = $gresult[$gtabid][6];
	$uids_count = lmb_count($uids);

	require_once("ext_limbasMail.php");

	$mail = new limbasMail($params["gtabid"],$params["fldid"],$params["id"],$gtabid);

	if ($mail){
		if (lmb_count($mail->error) > 0){
			echo utf8_decode(implode("<br>",$mail->error));
		} else {
			foreach ($uids as $k => &$uid){
				$uid = $mail->del($uid,null,$gresult[$gtabid]["id"][$k]);
#error_log("[".__FILE__."][".__LINE__."][".__FUNCTION__."][$uid][{$gresult[$gtabid]["id"][$k]}]");
			}
		}
		$mail->disconnect();
	}

	$gtabid = $params["gtabid"];
	if (lmb_count(array_filter($uids)) != $uids_count){
		echo $not_deleted;
		return;
	}

	dyns_extMessagesDisplay($params);
}

function dyns_extMessagesDisplay($params){
	global $gfield, $filter, $gtabid, $umgvar;

	$gformid = $params['gformid'];
	
	require_once(COREPATH . 'gtab/gtab.lib');
	require_once(COREPATH . 'gtab/gtab_type_erg.lib');

	$vgtabid = $gfield[$params["gtabid"]]["verkntabid"][$params["fldid"]];
	$vuniqueid = $gfield[$params["gtabid"]]["form_name"][$params["fldid"]]."_".$gformid;

	$verkn = set_verknpf($params["gtabid"],$params["fldid"],$params["id"],0,0,1,1);

	$linklevel["tabid"] = $gfield[$gtabid]["verkntabid"][$params["fldid"]];
	$linklevel["fieldid"] = $gfield[$params["gtabid"]]["verknfieldid"][$params["fldid"]];

	# list of shown fields
	if($params["viewmode"]){
		$present = array_keys($filter["ext_RelationFields"][$vuniqueid],$params["viewmode"]);
		if(lmb_count($present) > 0){
			unset($filter["ext_RelationFields"][$vuniqueid][$present[0]]);
		}else{
			$filter["ext_RelationFields"][$vuniqueid][] = $params["viewmode"];
		}
	}

	if(!isset($filter["ext_RelationFields"][$vuniqueid]) || empty($filter["ext_RelationFields"][$vuniqueid]))
		$filter["ext_RelationFields"][$vuniqueid] = $gfield[$linklevel["vtabid"]]["verknfieldid"][$linklevel["vfieldid"]];
	$old_filter = $filter["ext_RelationFields"][$vuniqueid];

	$filter["ext_RelationFields"][$vuniqueid][] = 6;
	$filter["ext_RelationFields"][$vuniqueid][] = 7;

	$vgresult = sql_14_c($vgtabid,$verkn,$linklevel,$gformid);
	$rscount = $vgresult[$vgtabid]["res_count"];

	$filter["ext_RelationFields"][$vuniqueid] = $old_filter;

	cftyp_14_d($vgresult,$rscount,$params["edittype"],$verkn,$params["gtabid"],$vgtabid,$params["fldid"],$params["id"],null,$linklevel,$vuniqueid,$gformid,$formid,$filter);
	echo "<div id=\"mail_status_{$params["gtabid"]}_{$params["fldid"]}\"></div>";
}
function dyns_extSearchMessages($params){
	global $gtabid,$gfield;
        
	$gtabid = $gfield[$params["gtabid"]]["verkntabid"][$params["fldid"]];
        
	require_once("ext_limbasMail.php");

	$mail = new limbasMail($params["gtabid"],$params["fldid"],$params["id"],$gtabid);

	if ($mail){
		if (lmb_count($mail->error) > 0){
			echo utf8_decode(implode("<br>",$mail->error));
		} else {
			echo $mail->search_mails('INBOX', 'ALL', 'FROM', $params['search'],$params["gtabid"].'_'.$params["fldid"].'_'.$params["id"].'_'.$params["edittype"]);
		}
		$mail->disconnect();
	}
}
?>
