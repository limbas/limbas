<?php
/*
 * Copyright notice
 * (c) 1998-2016 Limbas GmbH - Axel westhagen (support@limbas.org)
 * All rights reserved
 * This script is part of the LIMBAS project. The LIMBAS project is free software; you can redistribute it and/or modify it on 2 Ways:
 * Under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * Or
 * In a Propritary Software Licence http://limbas.org
 * The GNU General Public License can be found at http://www.gnu.org/copyleft/gpl.html.
 * A copy is found in the textfile GPL.txt and important notices to the license from the author is found in LICENSE.txt distributed with these scripts.
 * This script is distributed WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * This copyright notice MUST APPEAR in all copies of the script!
 * Version 3.0
 */

/*
 * ID:
 */

/* -------------------------- Extended File-Manager --------------------------------- */
function lmbd_extendedFileManager($ID,$gresult,$fieldid,$gtabid,$class,$style,$pos,$edittype,$z_index=null,$event=null,$gformid=null,$formid=null) {
	global $gtab;
	global $gfield;
	global $farbschema;
	global $lang;
	global $action;
	global $session;
	global $ufile;
	global $gfile;
	global $filestruct;
	global $LINK;
	global $ffilter;
	
	if(!$ID){return;}

	require_once("extra/explorer/explorer_main.lib");
	require_once("extra/explorer/filestructure.lib");

	if($style){$style_abs = "STYLE=\"".$pos.$style."\"";}else{$style_abs = "STYLE=\"margin-top:5px;margin-bottom:5px;\"";}

	# Verknüpfungswerte
	$verkn = set_verknpf($gfield[$gtabid]["verkntabid"][$fieldid],$gfield[$gtabid]["verknfieldid"][$fieldid],0,0,0,0,0);
	$verkn["md5tab"] = $gfield[$gtabid]["md5tab"][$fieldid];
	$verkn["uniqueid"] = $gfield[$gtabid]["form_name"][$fieldid];
	$vuniqueid = $gfield[$gtabid]["form_name"][$fieldid]."_".$gformid;
	
	if($verkn){

		# Anzuzeigende Ordner Ebene
		$linklevel = $GLOBALS["temp"];
		if($session["ExtFileManager"]["LID"][$verkn["uniqueid"]]){
			$LID = $session["ExtFileManager"]["LID"][$verkn["uniqueid"]];
		}else{
			$LID = $gfield[$gtabid]["file_level"][$fieldid];
		}
		
		# get global ffilter
		get_userShow($LID,1);
		
		# viewmode
		$show_details = $ffilter['viewmode'][$LID];
		
		# show files recursiv
		$show_sub = $session["ExtFileManager"]["viewsub"][$verkn["uniqueid"]];
		if(!$show_sub){$show_sub = 0;}else{$show_sub = 1;}

		# default height
		if(!$filter["ext_RelationFields"]["showall"][$vuniqueid]){
			if($vgresult[$vgtabid]["res_viewcount"] > 6){$bt = "height:110px;overflow:auto;";}
		}
		
		# order by
		$filter["order"][$LID] = $ffilter["order"][$LID];
		
		# search
		foreach ($gfile['show'][$LID] as $key1 => $val1){
			if($ffilter[$key1][$LID]){
				$searchfield = explode('_',$key1);
				$filter["gsr"][$searchfield[0]][$searchfield[1]] = $ffilter[$key1][$LID];
			}
		}

		# specific formular styles
		if($gformid AND $formid){
			$st = explode(";",$GLOBALS["gform"][$gformid]["style"][$formid]);
			if($st[24]){ #overflow
				$bt = "height:".$GLOBALS["gform"][$gformid]["height"][$formid]."px;overflow:".$st[24];
			}else{
				$bt = "";
			}
			$style_abs = str_replace(";height:",";height_:",$style_abs); # workaround to disable height
			# specific rules
			if($GLOBALS["gform"][$gformid]["parameters"][$formid]){
				$filter_ = eval($GLOBALS["gform"][$gformid]["parameters"][$formid]);
				if(is_array($filter_)){
					foreach ($filter_ as $fkey => $fval){
						$filter["ext_RelationFields"][$fkey][$vuniqueid] = $fval;
					}
				}
			}
		}

		/* --- Header ------*/
		echo "<div $style_abs class=\"gtabFringeRelationBody\">\n";
		echo "<TABLE BORDER=\"0\" WIDTH=\"100%\" CELLSPACING=\"0\" CELLPADDING=\"0\" class=\"gtabHeaderRelationTAB\">\n";
		echo "<TR class=\"gtabHeaderSymbolTR\">";
	
		if($action == "gtab_change" AND $edittype == 1){
			$funcpara = rawurlencode("$gtabid,$fieldid,$ID");
			if($LINK[249]){
				echo "<TD STYLE=\"width:20px;\" ALIGN=\"CENTER\" NOWRAP>
				<i class=\"lmb-icon ".$LINK["icon_url"][249]."\" STYLE=\"cursor:pointer;\" TITLE=\"".$lang[$LINK["desc"][249]]."\" OnClick=\"window.open('main.php?&action=mini_explorer&funcname=LmExt_Ex_LinkMiniEx&funcpara=".$funcpara."&typ=1&home_level='+document.getElementById('ExtFileManager_LID_".$gtabid."_".$fieldid."').value ,'_blank','toolbar=0,location=0,status=0,menubar=0,scrollbars=1,resizable=1,width=460,height=320')\"></i></TD>";
			}
			if($ffilter['search'][$LID] != 1){$st = "opacity:0.3;filter:Alpha(opacity=30)";}else{$st = null;}
			if($LINK[14]){echo "<td style=\"width:20px;\" align=\"center\" nowrap><i class=\"lmb-icon lmb-page-find\" id=\"LmEx_Ex_symbol_".$verkn["uniqueid"]."6\" TITLE=\"".$lang[$LINK["desc"][14]]."\" style=\"cursor:pointer;$st\" OnClick=\"LmExt_Ex_RelationFields($gtabid,$fieldid,'','','$ID','','','','','','','','','1');LmExt_Ex_DisplaySymbols(6,'','".$verkn["uniqueid"]."')\"></i></td><td style=\"width:5px;color:green;\" align=\"center\">|</td>";
			echo "<td STYLE=\"width:15px;\">&nbsp;</td>";}
			if($LINK[119] AND $LID){echo "<TD STYLE=\"width:20px;\" ALIGN=\"CENTER\" NOWRAP><i class=\"lmb-icon-cus lmb-folder-add\" style=\"cursor:pointer;\" onclick=\"document.getElementById('LmEx_Ex_uploadfile_".$gtabid."_".$fieldid."').style.display='none';document.getElementById('LmEx_Ex_createfolder_".$gtabid."_".$fieldid."').style.display='';\" TITLE=\"".$lang[$LINK["desc"][119]]."\"></i></td><TD STYLE=\"width:5px;color:green;\" ALIGN=\"CENTER\">|</TD>";}
			if($LINK[128] AND $LID){echo "<TD STYLE=\"width:20px;\" ALIGN=\"CENTER\" NOWRAP><i class=\"lmb-icon-cus lmb-page-new\" style=\"cursor:pointer;\" onclick=\"document.getElementById('LmEx_Ex_createfolder_".$gtabid."_".$fieldid."').style.display='none';document.getElementById('LmEx_Ex_uploadfile_".$gtabid."_".$fieldid."').style.display='';LmEx_multiupload(1,'LmEx_Ex_uploadfiles_".$gtabid."_".$fieldid."',document.getElementById('ExtFileManager_LID_".$gtabid."_".$fieldid."').value,'".$gtabid."_".$fieldid."','$gtabid','$fieldid','$ID')\" TITLE=\"".$lang[$LINK["desc"][128]]."\"></i></td><TD STYLE=\"width:5px;color:green;\" ALIGN=\"CENTER\">|</TD>";}
			if($LINK[171] AND $LID){echo "<TD STYLE=\"width:20px;\" ALIGN=\"CENTER\" NOWRAP><i class=\"lmb-icon-cus lmb-page-delete-fancy\" style=\"cursor:pointer;\" OnClick=\"LmExt_Ex_UnLinkFiles($gtabid,$fieldid,$ID,document.getElementById('ExtFileManager_LID_".$gtabid."_".$fieldid."').value,'$formid',1)\" TITLE=\"".$lang[$LINK["desc"][171]]."\"></i></td><TD STYLE=\"width:5px;color:green;\" ALIGN=\"CENTER\">|</TD>";}
			if($LINK[158] AND $LID){echo "<TD STYLE=\"width:20px;\" ALIGN=\"CENTER\" NOWRAP><i class=\"lmb-icon-cus lmb-rel-del\" STYLE=\"cursor:pointer;\" TITLE=\"".$lang[$LINK["desc"][158]]."\" BORDER=\"0\" OnClick=\"LmExt_Ex_UnLinkFiles($gtabid,$fieldid,$ID,document.getElementById('ExtFileManager_LID_".$gtabid."_".$fieldid."').value,'$formid',0)\"></i></TD>";}
		}else{
			echo "<TD STYLE=\"width:30px;\"></TD>";
		}

		echo "<TD STYLE=\"width:15px;\" ALIGN=\"CENTER\" >&nbsp;&nbsp;&nbsp;</TD>";

		#if($session["ExtFileManager"]["viewmode"][$verkn["uniqueid"]] != 2){$st = "opacity:0.3;filter:Alpha(opacity=30)";}else{$st = null;}
		#echo "<TD STYLE=\"width:20px;\" ALIGN=\"CENTER\" NOWRAP><i ID=\"LmEx_Ex_symbol_".$verkn["uniqueid"]."2\" class=\"lmb-icon-cus lmb-txt-col\" STYLE=\"cursor:pointer;$st\" TITLE=\"$lang[2234]\"BORDER=\"0\" OnClick=\"LmExt_Ex_RelationFields($gtabid,$fieldid,'2','$edittype',$ID,'','','','','','$formid','','','');LmExt_Ex_DisplaySymbols(2,'1,2,4','".$verkn["uniqueid"]."')\"></i></TD>";

		if($LINK[286]){
			echo "<TD STYLE=\"width:20px;\" ALIGN=\"CENTER\" NOWRAP><i class=\"lmb-icon-cus lmb-txt-ol-active\" STYLE=\"cursor:pointer;\" TITLE=\"".$lang[$LINK["desc"][286]]."\" onclick=\"lmb_relShowField(event,this,'extRelationFieldsDIV_".$gtabid."_".$fieldid."');\"></i>";

			/* --- Fieldlist ------*/
			echo "<div ID=\"extRelationFieldsDIV_".$gtabid."_".$fieldid."\" class=\"lmbContextMenu\" style=\"position:absolute;display:none;z-index:9999\" OnClick=\"activ_menu = 1\">\n";
			pop_top('fieldlist');
			pop_left();echo "&nbsp;<B>$lang[1634]</B>";pop_right();pop_line();
			foreach ($gfile["id"] as $key => $value){
				# Felder ohne Rechte
				if(!$gfile["id"][$key] OR $gtab["argresult_id"]["LDMS_FILES"]."_11" == $key){continue;}
				if($gfile['field_type'][$key] == 100){
					pop_left();echo "&nbsp;<B title=\"".$gfile['desc'][$key]."\">".$gfile['title'][$key]."</B>";
					pop_right();
					pop_line();
				}else{
					pop_left();

					if($gfile['show'][$LID][$key]){
						$color = "green";$class = "Icon";$icdis = "";
					}else{
						$color = "black";$class = "";$icdis = "none";
					}
					echo "<i id=\"extRelationFieldsPIC_".$gtabid."_".$fieldid."_".$key."\" class=\"lmbContextLeft lmb-icon lmb-check\" border=\"0\" style=display:$icdis></i>";
					echo "<span id=\"extRelationFields_".$gtabid."_".$fieldid."_".$gfile["tabid"][$key]."_".$gfile["fid"][$key]."\" class=\"lmbContextItem$class\" style=\"color:$color;cursor:pointer\" OnClick=\"LmExt_Ex_RelationFields($gtabid,$fieldid,'".$gfile["tabid"][$key]."_".$gfile["fid"][$key]."','$edittype',$ID,'','','','','','$formid','','','');\">&nbsp;".$gfile['title'][$key]."</span>";
					pop_right();
				}
			}
			pop_bottom();
			echo "</DIV>\n";

			echo "</TD>";
			echo "<TD STYLE=\"width:5px;color:green;\" ALIGN=\"CENTER\" >|</TD>";
		}
		
		if($session["ExtFileManager"]["viewsub"][$verkn["uniqueid"]] != 1){$st = "opacity:0.3;filter:Alpha(opacity=30)";}else{$st = null;}
		echo "<TD STYLE=\"width:20px;\" ALIGN=\"CENTER\" NOWRAP><i ID=\"LmEx_Ex_symbol_".$verkn["uniqueid"]."5\" class=\"lmb-icon-cus lmb-txt-linespace\" STYLE=\"cursor:pointer;$st\" TITLE=\"".$lang[2330]."\" BORDER=\"0\" OnClick=\"LmExt_Ex_RelationFields($gtabid,$fieldid,'','$edittype',$ID,'','','','',1,'$formid','',1,'');LmExt_Ex_DisplaySymbols(5,0,'".$verkn["uniqueid"]."')\"></i></TD>";
		
		echo "<TD STYLE=\"width:5px;color:green;\" ALIGN=\"CENTER\" >&nbsp;&nbsp;&nbsp;</TD>";
		
		
		
		if($show_details != 1){$st = "opacity:0.3;filter:Alpha(opacity=30)";}else{$st = null;}
		echo "<TD STYLE=\"width:20px;\" ALIGN=\"CENTER\" NOWRAP><i ID=\"LmEx_Ex_symbol_".$verkn["uniqueid"]."1\" class=\"lmb-icon lmb-align-justify\" STYLE=\"cursor:pointer;$st\" TITLE=\"$lang[2233]\" BORDER=\"0\" OnClick=\"LmExt_Ex_RelationFields($gtabid,$fieldid,'1','$edittype',$ID,'','','','','','$formid','','','');LmExt_Ex_DisplaySymbols(1,'1,3,4','".$verkn["uniqueid"]."')\"></i></TD>";
		echo "<TD STYLE=\"width:5px;color:green;\" ALIGN=\"CENTER\">|</TD>";
		
		if($show_details != 4){$st = "opacity:0.3;filter:Alpha(opacity=30)";}else{$st = null;}
		if($LINK[257]){echo "<TD STYLE=\"width:20px;\" ALIGN=\"CENTER\" NOWRAP><i ID=\"LmEx_Ex_symbol_".$verkn["uniqueid"]."4\" class=\"lmb-icon-cus lmb-pics\" STYLE=\"cursor:pointer;$st\" TITLE=\"$lang[2261]\" BORDER=\"0\" OnClick=\"LmExt_Ex_RelationFields($gtabid,$fieldid,'4','$edittype',$ID,'','','','',1,'$formid','','','');LmExt_Ex_DisplaySymbols(4,'1,3,4','".$verkn["uniqueid"]."')\"></i></TD>";
		echo "<TD STYLE=\"width:5px;color:green;\" ALIGN=\"CENTER\" >|</TD>";}
		
		if($show_details != 3){$st = "opacity:0.3;filter:Alpha(opacity=30)";}else{$st = null;}
		if($LINK[256]){echo "<TD STYLE=\"width:20px;\" ALIGN=\"CENTER\" NOWRAP><i ID=\"LmEx_Ex_symbol_".$verkn["uniqueid"]."3\" class=\"lmb-icon lmb-picture-icon\" STYLE=\"cursor:pointer;$st\" TITLE=\"$lang[2259]\" BORDER=\"0\" OnClick=\"LmExt_Ex_RelationFields($gtabid,$fieldid,'3','$edittype',$ID,'','','','',1,'$formid','','','');LmExt_Ex_DisplaySymbols(3,'1,3,4','".$verkn["uniqueid"]."')\"></i></TD>";
		echo "<TD STYLE=\"width:5px;color:green;\" ALIGN=\"CENTER\" >|</TD>";}

		# --- Ajax Search ----
		if($gfield[$gtabid]["dynsearch"][$fieldid] AND $action == "gtab_change"){
			echo "<TD nowrap align=\"right\">
				<INPUT TYPE=\"TEXT\" STYLE=\"width:80px;\" NAME=\"".$gfield[$gtabid]["form_name"][$fieldid]."_ds\" OnKeyup=\"lmbAjax_dynsearch(event,this,'14_b','".$gfield[$gtabid]["form_name"][$fieldid]."','".$verkn[tabid]."','".$verkn["fieldid"]."','$gtabid','$fieldid','$ID','$edittype')\">&nbsp;
				<DIV></DIV><DIV ID=\"".$gfield[$gtabid]["form_name"][$fieldid]."_dsl\" STYLE=\"position:absolute;z-index:999;\"></DIV>
				<INPUT TYPE=\"HIDDEN\" NAME=\"".$gfield[$gtabid]["form_name"][$fieldid]."\"></TD>";
		}else{
			echo "<TD><INPUT TYPE=\"HIDDEN\" NAME=\"".$gfield[$gtabid]["form_name"][$fieldid]."\">&nbsp;</TD>";
		}
		echo "</TR></TABLE>
		<table width=\"100%\">
		<tr id=\"LmEx_Ex_createfolder_".$gtabid."_".$fieldid."\" style=\"display:none;\"><td nowrap>$lang[2231]:</td><td colspan=\"13\"><input type=\"text\" id=\"LmEx_Ex_addfolder_".$gtabid."_".$fieldid."\" style=\"width:200px;\">&nbsp;<input type=\"button\" value=\"$lang[2237]\" style=\"cursor:pointer;\" OnClick=\"LmExt_Ex_RelationFields($gtabid,$fieldid,'','$edittype','$ID','','','','','','$formid',document.getElementById('LmEx_Ex_addfolder_".$gtabid."_".$fieldid."').value,'','');\"></td></tr>
		<tr id=\"LmEx_Ex_uploadfile_".$gtabid."_".$fieldid."\" style=\"display:none;\"><td id=\"LmEx_Ex_uploadfiles_".$gtabid."_".$fieldid."\"><td></tr>
		</table>\n";
		
		echo "<div style=\"height:1px;overflow:hidden\"></div>";
		if($gformid){$height = "height:".($GLOBALS["gform"][$gformid]["height"][$formid]+40);}
		echo "<div style=\"width:100%;$bt\" id=\"extRelationFieldsTab_".$gtabid."_".$fieldid."\">";
		
		echo "<TABLE BORDER=\"0\" ID=\"filetab\" CELLSPACING=\"0\" CELLPADDING=\"1\" STYLE=\"width:100%;border-collapse:collapse;table-layout:fixed;\">";
		echo "<input type=\"hidden\" ID=\"ExtFileManager_LID_".$gtabid."_".$fieldid."\" value=\"$LID\">";

		# ----- Ordnerstruktur ------
		get_filestructure();
		$rootlevel = $filestruct["level"][$gfield[$gtabid]["file_level"][$fieldid]];
		$level = $filestruct["level"][$LID];
		$typ = 7;
		$file_path = set_path($level,$LID);
		array_splice($file_path,-4);
		$filter["nolimit"][$LID] = 1;
		$filter["page"][$LID] = 1;
		$filter["viewmode"][$LID] = 5;
		$filter["sub"] = $show_sub;
		$verkn["parent_table"] = $gtab["table"][$gtabid];
		$verkn["parent_datid"] = $ID;

		# Metadaten filter
		if($style_array[22] != 'true' AND $gformid){
			$filter[$gtab["argresult_id"]["FILES"]."_13"][$LID] = array("image");
		}else{
			$filter[$gtab["argresult_id"]["FILES"]."_13"][$LID] = null;
		}

		# --- Abfrage starten ---
		if($query = get_fwhere($LID,$filter,$typ,$verkn)){
			if(!$ffile = get_ffile($query,$filter,$LID,$typ)){echo "no result for image!";return false;}
		}

		if($file_path){echo "<TR><TD colspan=\"10\"><DIV style=\"background-color:".$farbschema["WEB11"]."\">&nbsp;<i class=\"lmb-icon lmb-icon-8 lmb-folder\"></i>&nbsp;<INPUT TYPE=\"TEXT\" STYLE=\"border:none;width:94%;height:17px;background-color:".$farbschema["WEB11"].";color:#777777;z-index:1;\" VALUE=\"/".implode("/",$file_path)."\" READONLY></DIV></TD></TR>";}

		if($show_details == 3){
			extendedExplPicShow($ffile,$gtabid,$fieldid,$ID,$gformid,$formid,1,$edittype);
		}elseif($show_details == 4){
			echo "<tr><td>";
			explPicSummery($ffile,$gtabid,$fieldid,$ID,$gformid,$formid,1);
			echo "</td></tr>";
		}else{
			if($ffilter['search'][$LID]){
				explSearch($LID,$LID,$ffilter,$gtabid,$fieldid,$ID);
			}
			explHeader($LID,$LID,$ffilter,$gtabid,$fieldid,$ID);
			if($show_sub != 1){explFolders($LID,$LID,0,$level,$filter,"$gtabid,$fieldid,$ID",$rootlevel);}
			explFiles($LID,$LID,$level,$ffile,$filter);
		}

		echo "</TABLE></div></div>";
		
		echo "
		<script language=\"JavaScript\">
		var obj = $(\"#extRelationFieldsTab_".$gtabid."_".$fieldid."\");
		obj.on('dragenter', function (e)
		{
		    e.stopPropagation();
		    e.preventDefault();
		    $(\"div\").filter('[id^=\"td_\"]').addClass(\"lmbUploadDragenter\");
		    return false;
		});
		obj.on('dragover', function (e)
		{
		     e.stopPropagation();
		     e.preventDefault();
		});
		obj.on('drop', function (e)
		{
		     $(\"div\").filter('[id^=\"td_\"]').removeClass(\"lmbUploadDragenter\");
		     e.preventDefault();
		     var files = e.originalEvent.dataTransfer.files;
		     LmEx_dragFileUpload(files,'".$gtabid."_".$fieldid."','$LID','$ID','LmEx_Ex_uploadfiles_".$gtabid."_".$fieldid."','$gtabid','$fieldid');
		});
		</script>
		";
	}
}

function extendedExplPicShow(&$ffile,$gtabid,$field_id,$ID,$gformid,$formid,$picshow,$edittype){
	global $lang;
	global $farbschema;
	global $umgvar;
	
	if(!$ID){return;}

	# show element
	if(!$count = count($ffile["id"])){echo "no result for image!";return false;}

	if(!$picshow OR $picshow > $count OR $picshow < 1){$picshow = 1;}
	for($i=1; $i<$picshow; $i++){
		next($ffile["id"]);
	}
	if($cfid = current($ffile["id"])){
		if($gformid AND $formid){
			global $gform;
			$width = ($gform[$gformid]["width"][$formid]-18);
			$height = $gform[$gformid]["height"][$formid];
		}else{
			$size = explode("x",$umgvar["thumbsize3"]);
			$width = $size[0];
			$height = $size[1];
		}
		$dest = IMACK_ConvertThumbs(array($cfid,$ffile["secname"][$cfid],$ffile["mimeid"][$cfid],$ffile["thumb_ok"][$cfid],$ffile["level"][$cfid]),$width,$height,1);
	}

	echo "<TR><TD><TABLE CELLSPACING=\"0\" CELLPADDING=\"0\"><TR BGCOLOR=\"".$farbschema[WEB6]."\"><TD>&nbsp;</TD><TD><i TITLE=\"$lang[857]\" ALT=\"$lang[857]\" class=\"lmb-icon lmb-first\" STYLE=\"cursor:pointer;\" BORDER=\"0\" OnClick=\"LmExt_Ex_RelationFields($gtabid,$field_id,'3','$edittype',$ID,'','','','',1,'".$formid."','','','')\"></i></TD>";
	echo "<TD><i TITLE=\"$lang[860]\" ALT=\"$lang[860]\" class=\"lmb-icon lmb-previous\" STYLE=\"cursor:pointer;\" BORDER=\"0\" OnClick=\"LmExt_Ex_RelationFields($gtabid,$field_id,'3','$edittype',$ID,'','','','',".($i-1).",'".$formid."','','','')\"></i></TD>";
	echo "<TD><INPUT TYPE=\"TEXT\" NAME=\"ExtFMPicShowCount_".$gtabid."_".$field_id."\" READONLY STYLE=\"width:30px;height:13px;font-size:8;text-align:center\" VALUE=\"$i\"></TD>";
	echo "<TD><i TITLE=\"$lang[859]\" ALT=\"$lang[859]\" class=\"lmb-icon lmb-next\" STYLE=\"cursor:pointer;\" BORDER=\"0\" OnClick=\"LmExt_Ex_RelationFields($gtabid,$field_id,'3','$edittype',$ID,'','','','',".($i+1).",'".$formid."','','','')\"></i></TD>";
	echo "<TD><i TITLE=\"$lang[858]\" ALT=\"$lang[858]\" class=\"lmb-icon lmb-last\" STYLE=\"cursor:pointer;\" BORDER=\"0\" OnClick=\"LmExt_Ex_RelationFields($gtabid,$field_id,'3','$edittype',$ID,'','','','','$count','".$formid."','','','')\"></i></TD><TD width=90%></TD></TR></TABLE></TD></TR>";
	echo "<TR><TD COLSPAN=\"6\"><IMG SRC=\"".$dest."\" style=\"margin:5px;border:1px solid #CCCCCC;width:".$width."px;height:".$height."px;\" TITLE=\"".$ffile["name"][$cfid]."\"></TD></TR>";
}


# -------------------------- Mail ---------------------------------
function lmbd_extendedMessages($ID,&$gresult,$fieldid,$gtabid,$class,$style,$pos,$typ,$z_index=null,$event=null,$gformid=null,$formid=null) {
	global $db;
	global $gtab;
	global $gfield;
	global $farbschema;
	global $lang;
	global $userdat;
	global $view_all_verkn;
	global $LINK;
	global $action;
	global $session;
	global $filter;
	global $gtabid;
	
	if(!$ID){return;}

	require_once "gtab/gtab_type_erg.lib";

	# <fetch mail configuration>
	$tabid = $gtabid;
	$gtabid = $gfield[$tabid]["verkntabid"][$fieldid];
	require('extra/messages/config.php');
	$gtabid = $tabid;
	#<a onclick="return false" href="main_dyns.php?actid=messages&gtabid={$gfield[$tabid]["verkntabid"][$fieldid]}&mbox={$imap_cfg["arch_mbox"]}&uid=id">...</a>
	# </fetch mail configuration>

	if($style){$style_abs = "STYLE=\"".$pos.$style."\"";}else{$style_abs = "STYLE=\"margin-top:5px;margin-bottom:5px;\"";}

	$vgtabid = $gfield[$gtabid]["verkntabid"][$fieldid];
	$vuniqueid = $gfield[$gtabid]["form_name"][$fieldid]."_".$gformid;
	$verkn = set_verknpf($gtabid,$fieldid,$ID,0,0,1,1);

	$linklevel["tabid"] = $gfield[$gtabid]["verkntabid"][$fieldid];
	$linklevel["fieldid"] = $gfield[$gtabid]["verknfieldid"][$fieldid];

	# <get additional mail uid,to fields>
	if(!isset($filter["ext_RelationFields"][$vuniqueid]) || empty($filter["ext_RelationFields"][$vuniqueid]))
		$filter["ext_RelationFields"][$vuniqueid] = $gfield[$linklevel["vtabid"]]["verknfieldid"][$linklevel["vfieldid"]];

	$old_filter = $filter["ext_RelationFields"][$vuniqueid];

	$filter["ext_RelationFields"][$vuniqueid][] = 6;
	$filter["ext_RelationFields"][$vuniqueid][] = 7;
	# </get additional mail uid fields>

	$vgresult = sql_14_c($vgtabid,$verkn,$linklevel,$gformid);
	$rscount = $vgresult[$vgtabid]["res_count"];

	# <remove additional mail uid,to fields to prevent unwanted display>
	$filter["ext_RelationFields"][$vuniqueid] = $old_filter;

	$fn = create_function('$a,$b,&$c','$c[1][$b] = strpos(strtolower($a),$c[0])!==false ? "up" : "down";');
	$res = array(strtolower($imap_cfg['email_address']),&$tmp);
	if(is_array($vgresult[$vgtabid][7]) AND $imap_cfg['email_address']){
		array_walk($vgresult[$vgtabid][7],$fn,$res);
		$vgresult[$vgtabid]["img"] = $res[1];
	}

	# </remove additional mail uid fields to prevent unwanted display>

	if(!$filter["ext_RelationFields"]["showall"][$vuniqueid]){
		if($vgresult[$vgtabid]["res_viewcount"] > 6){$bt = "height:110px;overflow:auto;";}
	}

	# specific formular styles
	if($gformid AND $formid){
		$st = explode(";",$GLOBALS["gform"][$gformid]["style"][$formid]);
		if($st[24]){ #overflow
			$bt = "height:".$GLOBALS["gform"][$gformid]["height"][$formid]."px;overflow:".$st[24];
		}else{
			$bt = "";
		}
		$style_abs = str_replace(";height:",";height_:",$style_abs); # workaround to disable height
		# specific rules
		if($GLOBALS["gform"][$gformid]["parameters"][$formid]){
			$filter_ = eval($GLOBALS["gform"][$gformid]["parameters"][$formid]);
			if(is_array($filter_)){
				foreach ($filter_ as $fkey => $fval){
					$filter["ext_RelationFields"][$fkey][$vuniqueid] = $fval;
				}
			}
		}
	}

	/* --- Header ------*/
	echo "<div $style_abs class=\"gtabFringeRelationBody\">\n";
	echo "<TABLE BORDER=\"0\" WIDTH=\"100%\" CELLSPACING=\"0\" CELLPADDING=\"0\" class=\"gtabHeaderRelationTAB\">\n";
	echo "<TR class=\"gtabHeaderSymbolTR\">";
		
	if($typ == 1){
		echo "<TD STYLE=\"width:20px;\" ALIGN=\"CENTER\" NOWRAP><i name=\"mail_new\" id=\"{$vgtabid}_{$gtabid}_{$fieldid}_{$ID}_{$typ}_{$imap_cfg['arch_mbox']}\" class=\"lmb-icon-cus lmb-page-new\" BORDER=\"0\" TITLE=\"$lang[1299]\" STYLE=\"cursor:pointer\"></i></TD><TD STYLE=\"width:5px;color:green;\" ALIGN=\"CENTER\">|</TD>";
		echo "<TD STYLE=\"width:20px;\" ALIGN=\"CENTER\" NOWRAP><i name=\"mail_link\" id=\"{$gtabid}_{$fieldid}_{$ID}_{$typ}_{$gformid}\" class=\"lmb-icon-cus lmb-page-down\" STYLE=\"cursor:pointer;\" TITLE=\"$lang[1282]\" ALT=\"$lang[1282]\" BORDER=\"0\"></i></TD><TD STYLE=\"width:5px;color:green;\" ALIGN=\"CENTER\">|</TD>";
	}
	echo "<TD STYLE=\"width:20px;\" ALIGN=\"CENTER\" NOWRAP><i class=\"lmb-icon lmb-list-ul-alt\" STYLE=\"cursor:pointer;\" TITLE=\"$lang[1298]\" ALT=\"$lang[868]\" BORDER=\"0\" OnClick=\"newwin5('".$verkn["vtabid"]."','".$gtabid."','".$fieldid."','".$ID."')\"></i></TD><TD STYLE=\"width:5px;color:green;\" ALIGN=\"CENTER\">|</TD>";
	echo "<TD STYLE=\"width:20px;\" ALIGN=\"CENTER\" NOWRAP><i class=\"lmb-icon-cus lmb-select-list\" STYLE=\"cursor:pointer;\" onclick=\"lmb_relShowField(event,this,'extRelationFieldsDIV_".$gtabid."_".$fieldid."');\"></i>";
	
	/* --- Fieldlist ------*/
	echo "<div ID=\"extRelationFieldsDIV_".$gtabid."_".$fieldid."\" class=\"lmbContextMenu\" style=\"position:absolute;display:none;z-index:9999\" OnClick=\"activ_menu = 1\">\n";
	pop_top("extRelationFieldsDIV_".$gtabid."_".$fieldid);
	foreach ($gfield[$linklevel["tabid"]]["sort"] as $key => $value){
		pop_left();
		if(in_array($key,$filter["ext_RelationFields"][$vuniqueid])){
			$color = "green";$class = "Icon";$icdis = "";
		}else{
			$color = "black";$class = "";$icdis = "none";
		}
		echo "<i id=\"extRelationFieldsPIC_".$gtabid."_".$fieldid."_".$key."\" class=\"lmbContextLeft lmb-icon lmb-check{\" border=\"0\" style=display:$icdis></i>";
		echo "<span id=\"extRelationFields_".$gtabid."_".$fieldid."_".$key."\" class=\"lmbContextItem$class\" style=\"color:$color;cursor:pointer\" OnClick=\"LmExt_RelationFields(this,'$gtabid','$fieldid','$key','$typ','$ID','','','','','$gformid','$formid');\">".$gfield[$linklevel["tabid"]]["spelling"][$key]."</span>";
		pop_right();
	}
	pop_bottom();
	echo "</DIV>\n";
	
	echo "</TD>";

	if($gformid){
		echo "<TD STYLE=\"width:90%;\"></TD>";
	}else{
		echo "<TD STYLE=\"width:90%;cursor:s-resize\" OnClick=\"LmExt_RelationFields(this,'$gtabid','$fieldid','','$typ','$ID','','extRelationFieldsTab_".$gtabid."_".$fieldid."','showall','','$gformid','$formid');\"></TD>";
	}
    echo '
	<td nowrap="">
	verkn&uuml;pfen:
	<input type="text" ondblclick="this.value=\'*\';search_mails(this,'."'{$gtabid}_{$fieldid}_{$ID}_{$typ}_{$gformid}'".')" onkeyup="search_mails(this,'."'{$gtabid}_{$fieldid}_{$ID}_{$typ}_{$gformid}'".')">
	<div></div>
	<div id="g_4_57_dsl" class="ajax_container" onclick="activ_menu=1;" style="position:fixed;z-index:9999;visibility:hidden;"></div>
	<input type="hidden" name="g_4_57">
	</td>';
	
	# --- Ajax Search ----
	if($gfield[$gtabid]["dynsearch"][$fieldid] AND $typ == 1){
		echo "<TD STYLE=\"width:40%\"></TD><TD nowrap>
			".$lang[30].": <INPUT TYPE=\"TEXT\" NAME=\"".$gfield[$gtabid]["form_name"][$fieldid]."_ds\" OnKeyup=\"lmbAjax_dynsearch(event,this,'14_b','".$gfield[$gtabid]["form_name"][$fieldid]."','".$verkn["vtabid"]."','".$verkn["vfieldid"]."','$gtabid','$fieldid','$ID','$typ')\">
			<DIV></DIV><DIV ID=\"".$gfield[$gtabid]["form_name"][$fieldid]."_dsl\" STYLE=\"visibility:visible;position:absolute;z-index:999;\" OnClick=\"activ_menu=1;\"></DIV>
			<INPUT TYPE=\"HIDDEN\" NAME=\"".$gfield[$gtabid]["form_name"][$fieldid]."\"></TD>";
	}else{
		echo "<INPUT TYPE=\"HIDDEN\" NAME=\"".$gfield[$gtabid]["form_name"][$fieldid]."\">";
	}

	echo "</TR></TABLE>\n";

	echo "<div STYLE=\"width:100%;$bt\" ID=\"extRelationFieldsTab_".$gtabid."_".$fieldid."\">";
	# table list
	cftyp_14_d($vgresult,$rscount,$typ,$verkn,$gtabid,$vgtabid,$fieldid,$ID,$VID,$linklevel,$vuniqueid,$gformid,$formid,$filter);
	echo "<div id=\"mail_status_{$gtabid}_{$fieldid}\"></div>";
	echo "</div>";
	echo "</div>";
	
	echo "<div id=\"mail_status\"></div>";
?>
<script src="extra/messages/classes/js/olAjax.js" language="javascript" type="text/javascript"></script>
<script src="extra/messages/classes/js/olCookie.js" language="javascript" type="text/javascript"></script>
<script src="extra/messages/classes/js/olEvent.js" language="javascript" type="text/javascript"></script>
<script src="EXTENSIONS/system/ext_messages.js" language="javascript" type="text/javascript"></script>
<script language="javascript" type="text/javascript">//<!--
	olEvent().observe(window, "load", mail_check);
//--></script>
<?php
	$gtabid = $tabid;
}




# -------------------------- Calender ---------------------------------
function lmbd_extendedCalender($ID,&$gresult,$fieldid,$gtabid,$class,$style,$pos,$typ,$z_index=null,$event=null,$gformid=null,$formid=null) {
	global $db;
	global $gtab;
	global $gfield;
	global $farbschema;
	global $lang;
	global $userdat;
	global $view_all_verkn;
	global $LINK;
	global $action;
	global $session;
	global $filter;
	global $umgvar;
	
	if(!$ID){return;}

	require_once "gtab/gtab_type_erg.lib";

	if($style){$style_abs = "STYLE=\"".$pos.$style."\"";}else{$style_abs = "STYLE=\"margin-top:5px;margin-bottom:5px;\"";}
	
	$vgtabid = $gfield[$gtabid]["verkntabid"][$fieldid];
	$vuniqueid = $gfield[$gtabid]["form_name"][$fieldid]."_".$gformid;
	$verkn = set_verknpf($gtabid,$fieldid,$ID,0,0,1,1);
	$linklevel["tabid"] = $gfield[$gtabid]["verkntabid"][$fieldid];
	$linklevel["fieldid"] = $gfield[$gtabid]["verknfieldid"][$fieldid];
	$vgresult = sql_14_c($vgtabid,$verkn,$linklevel,$gformid);
	$rscount = $vgresult[$vgtabid]["res_count"];

	# ------- 1:n / n:m Verknüpfung Detail-Darstellung -----
	# list of shown fields
	if(!$filter["ext_RelationFields"][$vuniqueid]){
		if($gfield[$gtabid]["verknview"][$fieldid]){
			$filter["ext_RelationFields"][$vuniqueid] = $gfield[$gtabid]["verknview"][$fieldid];
		}else{
			$filter["ext_RelationFields"][$vuniqueid] = array($linklevel["fieldid"]);
		}
	}
	
	if(!$filter["ext_RelationFields"]["showall"][$vuniqueid]){
		if($vgresult[$vgtabid]["res_viewcount"] > 6){$bt = "height:110px;overflow:auto;";}
	}
	
	# specific formular styles
	if($gformid AND $formid){
		$st = explode(";",$GLOBALS["gform"][$gformid]["style"][$formid]);
		if($st[24]){ #overflow
			$bt = "height:".$GLOBALS["gform"][$gformid]["height"][$formid]."px;overflow:".$st[24];
		}else{
			$bt = "";
		}
		$style_abs = str_replace(";height:",";height_:",$style_abs); # workaround to disable height
		# specific rules
		if($GLOBALS["gform"][$gformid]["parameters"][$formid]){
			$filter_ = eval($GLOBALS["gform"][$gformid]["parameters"][$formid]);
			if(is_array($filter_)){
				foreach ($filter_ as $fkey => $fval){
					$filter["ext_RelationFields"][$fkey][$vuniqueid] = $fval;
				}
			}
		}
	}
	
	/* --- Header ------*/
	echo "<div $style_abs class=\"gtabFringeRelationBody\">\n";
	echo "<TABLE BORDER=\"0\" WIDTH=\"100%\" CELLSPACING=\"0\" CELLPADDING=\"0\" class=\"gtabHeaderRelationTAB\">\n";
	echo "<TR class=\"gtabHeaderSymbolTR\">";
	
	if($typ == 1){
		if(!$filter["ext_RelationFields"]["no_add"][$vuniqueid] AND $LINK[1]){echo "<TD STYLE=\"width:20px;\" ALIGN=\"CENTER\" NOWRAP><i class=\"lmb-icon-cus lmb-page-new\" BORDER=\"0\" title=\"".$lang[$LINK["desc"][1]]."\" STYLE=\"cursor:pointer\" OnClick=\"create_new_verkn('".$verkn["vtabid"]."','$fieldid','$gtabid','$ID')\"></i></TD><TD STYLE=\"width:5px;color:green;\" ALIGN=\"CENTER\">|</TD>";}
		if(!$filter["ext_RelationFields"]["no_edit"][$vuniqueid] AND $gfield[$gtabid]["data_type"][$fieldid] == 27 AND $LINK[3]){
			if($filter["ext_RelationFields"]["edit"][$vuniqueid]){$st = "opacity:0.3;filter:Alpha(opacity=30);";}
			echo "<TD STYLE=\"width:20px;\" ALIGN=\"CENTER\" NOWRAP><i class=\"lmb-icon-cus lmb-page-edit\" title=\"".$lang[$LINK["desc"][3]]."\" STYLE=\"cursor:pointer;$st\" OnClick=\"LmExt_RelationFields(this,'$gtabid','$fieldid','','$typ','$ID','','$vid','edit','','$gformid','$formid');limbasSetOpacity(this)\"></i></TD><TD STYLE=\"width:5px;color:green;\" ALIGN=\"CENTER\">|</TD>";
		}
	}

	if(!$filter["ext_RelationFields"]["no_openlist"][$vuniqueid] AND $LINK[10]){echo "<TD STYLE=\"width:20px;\" ALIGN=\"CENTER\" NOWRAP><i class=\"lmb-icon lmb-list-ul-alt\" STYLE=\"cursor:pointer;\" title=\"".$lang[$LINK["desc"][10]]."\" BORDER=\"0\" OnClick=\"newwin5('".$verkn["vtabid"]."','".$gtabid."','".$fieldid."','".$ID."')\"></i></TD><TD STYLE=\"width:5px;color:green;\" ALIGN=\"CENTER\">|</TD>";}
	if(!$filter["ext_RelationFields"]["no_fieldselect"][$vuniqueid] AND $LINK[286]){echo "<TD STYLE=\"width:20px;\" ALIGN=\"CENTER\" NOWRAP><i class=\"lmb-icon-cus lmb-select-list\" STYLE=\"cursor:pointer;\" title=\"".$lang[$LINK["desc"][286]]."\" onclick=\"lmb_relShowField(event,this,'extRelationFieldsDIV_".$gtabid."_".$fieldid."');\"></i>";
	/* --- Fieldlist ------*/
	echo "<DIV ID=\"extRelationFieldsDIV_".$gtabid."_".$fieldid."\" class=\"lmbContextMenu\" style=\"position:absolute;display:none;z-index:9999\" OnClick=\"activ_menu = 1\">\n";
	pop_top("extRelationFieldsDIV_".$gtabid."_".$fieldid);
	foreach ($gfield[$linklevel["tabid"]]["sort"] as $key => $value){
		pop_left();
		if(in_array($key,$filter["ext_RelationFields"][$vuniqueid])){
			$color = "green";$class = "Icon";$icdis = "";
		}else{
			$color = "black";$class = "";$icdis = "none";
		}
		echo "<i id=\"extRelationFieldsPIC_".$gtabid."_".$fieldid."_".$key."\" class=\"lmbContextLeft lmb-icon lmb-check\" border=\"0\" style=display:$icdis></i>";
		echo "<span id=\"extRelationFields_".$gtabid."_".$fieldid."_".$key."\" class=\"lmbContextItem$class\" style=\"color:$color;cursor:pointer\" OnClick=\"LmExt_RelationFields(this,'$gtabid','$fieldid','$key','$typ','$ID','','','','','$gformid','$formid');\">".$gfield[$linklevel["tabid"]]["spelling"][$key]."</span>";
		pop_right();
	}
	pop_bottom();
	echo "</DIV>\n";
	if(!$filter["ext_RelationFields"]["no_calendar"][$vuniqueid]){
	   echo "</TD><TD STYLE=\"width:15px;color:green;\" ALIGN=\"LEFT\">&nbsp;</TD>";}
	   echo "<TD STYLE=\"width:20px;\" ALIGN=\"CENTER\" NOWRAP><i class=\"lmb-icon-cus lmb-cal-day\" STYLE=\"cursor:pointer;\" TITLE=\"$lang[1929]\" BORDER=\"0\" OnClick=\"newwin14('".$verkn["vtabid"]."','".$gtabid."','".$fieldid."','".$ID."')\"></i></TD>";	
	}
	
	if($gformid){
		echo "<TD></TD>";
	}else{
		echo "<TD cursor:s-resize\" OnClick=\"LmExt_RelationFields(this,'$gtabid','$fieldid','','$typ','$ID','','extRelationFieldsTab_".$gtabid."_".$fieldid."','showall','','$gformid','$formid');\"></TD>";
	}
		
	# --- Ajax Search ----
	if($gfield[$gtabid]["dynsearch"][$fieldid] AND $typ == 1 AND !$filter["ext_RelationFields"]["no_link"][$vuniqueid]){
		echo "<TD nowrap>
					".$lang[30].": <INPUT TYPE=\"TEXT\" NAME=\"".$gfield[$gtabid]["form_name"][$fieldid]."_ds\" OnKeyup=\"lmbAjax_dynsearch(event,this,'14_b','".$gfield[$gtabid]["form_name"][$fieldid]."','".$verkn["vtabid"]."','".$verkn["vfieldid"]."','$gtabid','$fieldid','$ID','$typ')\">
					<DIV></DIV><DIV ID=\"".$gfield[$gtabid]["form_name"][$fieldid]."_dsl\" STYLE=\"visibility:visible;position:absolute;z-index:999;\" OnClick=\"activ_menu=1;\"></DIV>
					<INPUT TYPE=\"HIDDEN\" NAME=\"".$gfield[$gtabid]["form_name"][$fieldid]."\"></TD>
					<TD>&nbsp;</TD>
					";
	}else{
		echo "<INPUT TYPE=\"HIDDEN\" NAME=\"".$gfield[$gtabid]["form_name"][$fieldid]."\">";
	}

	echo "</TR></TABLE>\n";

	#echo "<DIV STYLE=\"width:100%;overflow:auto;\" ID=\"extRelationFieldsTab_".$gtabid."_".$fieldid."\">";
	echo "<div style=\"width:100%;$bt\" ID=\"extRelationFieldsTab_".$gtabid."_".$fieldid."\">";
	# table list
	cftyp_14_d($vgresult,$rscount,$typ,$verkn,$gtabid,$vgtabid,$fieldid,$ID,$VID,$linklevel,$vuniqueid,$gformid,$formid,$filter);
	echo "</div>";
	echo "</div>";
}

# -------------------------- Calender ---------------------------------
function lmbd_extendedCalRepetition($ID,&$gresult,$fieldid,$gtabid,$class,$style,$pos,$typ,$z_index=null,$event=null,$gformid=null,$formid=null) {
	global $lang;
	global $gfield;
	
	if(!$ID){return;}
	
	$event_change = "checktyp('".$gfield[$gtabid]["data_type"][$fieldid]."','".$gfield[$gtabid]["form_name"][$fieldid]."','".$gfield[$gtabid]["spelling"][$fieldid]."','".$fieldid."','".$gtabid."',this.value,'".$ID."','".$gfield[$gtabid]["ajaxpost"][$fieldid]."','".$gfield[$gtabid]["size"][$fieldid]."')";
	$events = dftyp_events($typ,$event,null,$event_dblclick,null,null,$event_change,$gresult,$gtabid,$fieldid);
	$result = $gresult[$gtabid][$fieldid][0];
	
	${'checked'.$result} = 'selected';
	
	echo "<select id=\"".$gfield[$gtabid]["form_name"][$fieldid]."\" $class $style name=\"".$gfield[$gtabid]["form_name"][$fieldid]."\"  $events>
	<option value=\"0\">
	<option value=\"1\" $checked1>$lang[2786]
	<option value=\"2\" $checked2>$lang[2787]
	<option value=\"3\" $checked3>$lang[2788]
	<option value=\"4\" $checked4>$lang[2789]
	<option value=\"5\" $checked5>$lang[2790]
	</select>
	";
	
}
?>