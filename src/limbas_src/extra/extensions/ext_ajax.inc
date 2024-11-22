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

	echo "<input type=\"hidden\" id=\"LID_".$gtabid."_".$field_id."\" value=\"$LID\">";
	echo "<TABLE BORDER=\"0\" ID=\"filetab\" CELLSPACING=\"0\" CELLPADDING=\"1\" STYLE=\"width:100%;border-collapse:collapse;\">";

	if($file_path){echo "<TR><TD colspan=\"10\"><DIV style=\"background-color:".$farbschema["WEB12"]."\">&nbsp;<i class=\"lmb-icon lmb-folder\"></i>&nbsp;<INPUT TYPE=\"TEXT\" STYLE=\"border:none;width:94%;height:17px;background-color:".$farbschema["WEB12"].";\" VALUE=\"/".implode("/",$file_path)."\" READONLY></DIV></TD></TR>";}

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
