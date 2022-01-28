<?php
/*
 * Copyright notice
 * (c) 1998-2021 Limbas GmbH(support@limbas.org)
 * All rights reserved
 * This script is part of the LIMBAS project. The LIMBAS project is free software; you can redistribute it and/or modify it on 2 Ways:
 * Under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * Or
 * In a Propritary Software Licence http://limbas.org
 * The GNU General Public License can be found at http://www.gnu.org/copyleft/gpl.html.
 * A copy is found in the textfile GPL.txt and important notices to the license from the author is found in LICENSE.txt distributed with these scripts.
 * This script is distributed WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * This copyright notice MUST APPEAR in all copies of the script!
 * Version 4.3.36.1319
 */

/*
 * ID: 170
 */

#/*----------------- Aktions-Eintrag -------------------*/
#if(isset($aktion_value) AND $report_id AND !$del) {
#	$sqlquery = "UPDATE LMB_REPORT_LIST SET SQL_STATEMENT = '".parse_db_string($aktion_value,255)."' WHERE ID = $report_id";
#	$rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
#	if(!$rs) {$commit = 1;}
#}

/*----------------- Report Gruppe anlegen -------------------*/
if($new_group AND $report_list) {
	foreach ($new_group as $key => $value){
		if($group_report[$key]){
			if($report_list[$key]){
				$report_list = explode(";",$report_list[$key]);
				foreach ($report_list as $key1 => $value1){
					if($value1 AND $group_report[$key][$value1]){
						$group_list[] = $value1;
					}
				}
				$NEXTID = next_conf_id("LMB_REPORT_LIST");
				$report_name = preg_replace('/[^a-z0-9]/i','_',$group_name[$key]);
				$report_name = lmb_substr(preg_replace("/[_]{1,}/","_",$report_name),0,18);
				$group_list = implode(";",$group_list);

				# Report
				$sqlquery = "INSERT INTO LMB_REPORT_LIST (ID,NAME,ERSTDATUM,ERSTUSER,REFERENZ_TAB,GROUPLIST,TARGET,DEFFORMAT) VALUES ($NEXTID,'$report_name',".LMB_DBDEF_TIMESTAMP.",'".$session["user_id"]."',".$key.",'".$group_list."',$NEXTID,'".parse_db_string($report_format)."')";
				$rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
				if(!$rs) {$commit = 1;}

				echo $sqlquery;

				$NEXTID2 = next_db_id("LMB_RULES_REPFORM");
				$sqlquery = "INSERT INTO LMB_RULES_REPFORM VALUES ($NEXTID2,1,".$session["group_id"].",".LMB_DBDEF_TRUE.",$NEXTID)";
				$rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
				if(!$rs) {$commit = 1;}

				# ---- Filestrukture ----------------
				$flresult = create_fs_tab_path($key);

				if(!$commit){
					if($flresult['tab_level']){
						$sqlquery = "SELECT ID FROM LDMS_STRUCTURE WHERE TYP = 5 AND LEVEL = ".$flresult['tab_level']." AND NAME = '".parse_db_string($report_name,50)."' AND TABGROUP_ID = ".$gtab['tab_group'][$key]." AND TAB_ID = $key AND FIELD_ID = $NEXTID";
						$rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
						if(!lmbdb_result($rs, "ID")){
							$sqlquery = "INSERT INTO LDMS_STRUCTURE (ID,NAME,LEVEL,TYP,ERSTUSER,TABGROUP_ID,TAB_ID,FIELD_ID,FIX) VALUES (".$flresult['FNEXTID'].",'".parse_db_string($report_name,50)."',".$flresult['tab_level'].",5,".$session['user_id'].",".$gtab['tab_group'][$key].",$key,$NEXTID,".LMB_DBDEF_TRUE.")";
							$rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
							if(!$rs) {$commit = 1;}
						}
					}
				}

			}
		}
	}
}

if($changeext_id) {
	$sqlquery = "UPDATE LMB_REPORT_LIST SET EXTENSION='".parse_db_string($report_extension[$changeext_id],160)."' WHERE ID = ".parse_db_int($changeext_id,3);
	$rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
	if(!$rs) {$commit = 1;}
}

if($changetarget_id) {
	$sqlquery = "UPDATE LMB_REPORT_LIST SET TARGET=".parse_db_int($report_changetarget[$changetarget_id],16)." WHERE ID = ".parse_db_int($changetarget_id,3);
	$rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
	if(!$rs) {$commit = 1;}
}

if($changename_id) {
	$sqlquery = "UPDATE LMB_REPORT_LIST SET NAME='".parse_db_string($report_changename[$changename_id],160)."' WHERE ID = ".parse_db_int($changename_id,3);
	$rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
	if(!$rs) {$commit = 1;}
}

if($changedefaultformat_id) {
	$sqlquery = "UPDATE LMB_REPORT_LIST SET DEFFORMAT='".parse_db_string($report_defaultformat[$changedefaultformat_id])."' WHERE ID = ".parse_db_int($changedefaultformat_id,3);
	$rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
	if(!$rs) {$commit = 1;}
}


if($changeootemplate_id) {

	$tmp_ = explode('-',$changeootemplate_id);
	$changeootemplate_id = $tmp_[0];
	$format = $tmp_[1];
	
	#$fname = get_NameFromID($report_ootemplate[$changeootemplate_id]);
	#$fext = array_pop(explode(".",$fname));
	#if(lmb_strtolower($fext) == "odt"){
	#}else{
	#	echo "only OpenOffice templates permitted!<br>";
	#}
	
	if($format == 'odt'){
		$ootplid = parse_db_int($report_odt_template[$changeootemplate_id]);
		$sqlquery = "UPDATE LMB_REPORT_LIST SET ODT_TEMPLATE=".$ootplid."  WHERE ID = ".parse_db_int($changeootemplate_id,3);
	}elseif($format == 'ods'){
		$ootplid = parse_db_int($report_ods_template[$changeootemplate_id]);
		$sqlquery = "UPDATE LMB_REPORT_LIST SET ODS_TEMPLATE=".$ootplid."  WHERE ID = ".parse_db_int($changeootemplate_id,3);
	}
	
	$rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
	if(!$rs) {$commit = 1;}
}

/*----------------- Report umbenennen -------------------*/
if($savename_id) {
	$sqlquery = "UPDATE LMB_REPORT_LIST SET SAVENAME='".parse_db_string($report_savename[$savename_id],160)."' WHERE ID = ".parse_db_int($savename_id,3);
	$rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
	if(!$rs) {$commit = 1;}
}


/*----------------- Report löschen -------------------*/
if($del AND $report_id){
	/* --- Transaktion START -------------------------------------- */
	lmb_StartTransaction();

	$sqlquery = "SELECT REFERENZ_TAB,GROUPLIST FROM LMB_REPORT_LIST WHERE ID = $report_id";
	$rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
	if(!$rs) {$commit = 1;}
	$rep_ref_tab = lmbdb_result($rs, "REFERENZ_TAB");
	$group_list = lmbdb_result($rs, "GROUPLIST");



	if(!$group_list){
		$sqlquery = "SELECT TAB_SIZE FROM LMB_REPORTS WHERE BERICHT_ID = $report_id AND TYP = 'bild'";
		$rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
		if(!$rs) {$commit = 1;}
		while(lmbdb_fetch_row($rs)) {
			if(file_exists($umgvar['uploadpfad']."report/".lmbdb_result($rs, "TAB_SIZE"))){
				unlink($umgvar['uploadpfad']."report/".lmbdb_result($rs, "TAB_SIZE"));
			}
			if(file_exists($umgvar['pfad']."/TEMP/thumpnails/report/".lmbdb_result($rs, "TAB_SIZE"))){
				unlink($umgvar['pfad']."/TEMP/thumpnails/report/".lmbdb_result($rs, "TAB_SIZE"));
			}
		}
	}

	$sqlquery = "DELETE FROM LMB_REPORTS WHERE BERICHT_ID = $report_id";
	$rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
	if(!$rs) {$commit = 1;}
	$sqlquery = "DELETE FROM LMB_REPORT_LIST WHERE ID = $report_id";
	$rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
	if(!$rs) {$commit = 1;}
	$sqlquery = "DELETE FROM LMB_RULES_REPFORM WHERE REPFORM_ID = $report_id AND TYP = 1";
	$rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);

	# --- Filestructure ------
	$sqlquery = "SELECT ID FROM LDMS_STRUCTURE WHERE TAB_ID = $rep_ref_tab AND FIELD_ID = $report_id AND TYP = 5";
	$rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
	if(!$rs) {$commit = 1;}

	if(!$commit AND lmbdb_result($rs, "ID")){
		$GLOBALS["filestruct"]['admin'] = 1;
		if(!delete_dir(lmbdb_result($rs, "ID"))){$commit = 1;}
	}


	/* --- Transaktion ENDE -------------------------------------- */
	if($commit == 1){
		lmb_EndTransaction(0);
	} else {
		lmb_EndTransaction(1);
	}

}
?>

<Script language="JavaScript">


function submit_form(act,targ){
	document.form1.action.value = act;
	document.form1.action = targ;
	alert(document.form1.action);
	//document.form1.submit();
}

// Layer positionieren und öffnen
function setxypos(evt,el) {
	if(browser_ns5){
		document.getElementById(el).style.left = evt.pageX;
		document.getElementById(el).style.top = evt.pageY;
	}else{
		document.getElementById(el).style.left = window.event.clientX + document.body.scrollLeft;
		document.getElementById(el).style.top = window.event.clientY + document.body.scrollTop;
	}
}

function element1(evt,ID) {
	setxypos(evt,'element1');
	document.getElementById('element1').style.visibility='visible';
	document.form1.report_id.value = ID;
	eval("document.form1.aktion_value.value = document.form1.report_"+ID+".value");
}

function report_delete(ID){
	var del = confirm('<?=$lang[2284]?>');
	if(del){
		document.location.href="main_admin.php?&action=setup_report_select&del=1&report_id="+ID;
	}
}

function lmb_setOOTemplate(el,val){
	if(!val){return;}
	var files = val.split('-');
	var el_ = el.split('-');
	elid = el_[0];
	format = el_[1];
	var typ = files[0].substr(0,1);
	if(typ != 'd'){alert('only files permitted!');return;}
	var file = files[0].substring(2);
	if(!file || !elid || !document.form1.elements['report_'+format+'_template['+elid+']']){return;}
	document.form1.elements['report_'+format+'_template['+elid+']'].value=file;
	document.form1.changeootemplate_id.value=el;
	document.form1.action.value='setup_report_select';
	document.form1.submit();
}

/* --- miniexplorer-Fenster ----------------------------------- */
function lmb_openMiniexplorer(elid,format) {
	miniexplorer=open("main.php?&action=mini_explorer&funcname=lmb_setOOTemplate&funcpara=<?=rawurlencode("'")?>"+elid+"-"+format+"<?=rawurlencode("'")?>" ,"miniexplorer","toolbar=0,location=0,status=0,menubar=0,scrollbars=1,resizable=1,width=410,height=320");
}

</Script>



<div class="lmbPositionContainerMain">


<FORM ACTION="main_admin.php" METHOD="post" name="form1">
<input type="hidden" name="action" value="setup_report_frameset">
<input type="hidden" name="referenz_tab" value="<?=$referenz_tab?>">
<input type="hidden" name="report_id">
<input type="hidden" name="rename_id">
<input type="hidden" name="savename_id">
<input type="hidden" name="changename_id">
<input type="hidden" name="changetarget_id">
<input type="hidden" name="changeext_id">
<input type="hidden" name="changeootemplate_id">
<input type="hidden" name="changedefaultformat_id">



<TABLE class="tabfringe" BORDER="0" cellspacing="1" cellpadding="2"  style="width: 100%;">
<TR class="tabHeader">
<TD class="tabHeaderItem">ID</TD>
<TD class="tabHeaderItem"></TD>
<TD class="tabHeaderItem"><?=$lang[160]?></TD>
<TD class="tabHeaderItem"><?=$lang[1137]?></TD>
<TD class="tabHeaderItem"><?=$lang[1162]?></TD>
<TD class="tabHeaderItem"><?=$lang[2509]?></TD>
<TD class="tabHeaderItem"><?=$lang[2111]?></TD>
<TD class="tabHeaderItem"><?=$lang[2511]?></TD>
<TD class="tabHeaderItem"><?=$lang[1163]?></TD>
<TD class="tabHeaderItem"><?=$lang[1161]?></TD>
<TD class="tabHeaderItem"><?=$lang[1161]?></TD>



</TR>

<?php
#----------------- Berichte -------------------
function resultreportlist_(){
	global $db;
	global $session;

	#AND LDMS_STRUCTURE.TYP = 5
	#AND LDMS_STRUCTURE.FIX = TRUE
	#WHERE LMB_REPORT_LIST.TARGET = LDMS_STRUCTURE.FIELD_ID
	/*
	$sqlquery = "SELECT DISTINCT LMB_REPORT_LIST.EXTENSION,LMB_REPORT_LIST.SAVENAME,LMB_REPORT_LIST.TARGET,LMB_REPORT_LIST.ERSTUSER,LMB_REPORT_LIST.ID,LMB_REPORT_LIST.GROUPLIST,LMB_REPORT_LIST.NAME,LMB_REPORT_LIST.REFERENZ_TAB,LDMS_STRUCTURE.ID AS FID,LDMS_STRUCTURE.LEVEL
	FROM LMB_REPORT_LIST,LDMS_STRUCTURE,LMB_RULES_REPFORM
	WHERE LMB_REPORT_LIST.TARGET = LDMS_STRUCTURE.ID
	AND LMB_RULES_REPFORM.REPFORM_ID = LMB_REPORT_LIST.ID
	AND LMB_RULES_REPFORM.TYP = 1
	AND LMB_RULES_REPFORM.GROUP_ID = ".$session["group_id"]."
	UNION
	
	SELECT DISTINCT LMB_REPORT_LIST.EXTENSION,LMB_REPORT_LIST.SAVENAME,LMB_REPORT_LIST.TARGET,LMB_REPORT_LIST.ERSTUSER,LMB_REPORT_LIST.ID,LMB_REPORT_LIST.GROUPLIST,LMB_REPORT_LIST.NAME,LMB_REPORT_LIST.REFERENZ_TAB,0 AS FID, 0 AS LEVEL
	FROM LMB_REPORT_LIST,LMB_RULES_REPFORM
	WHERE LMB_RULES_REPFORM.REPFORM_ID = LMB_REPORT_LIST.ID
	AND LMB_RULES_REPFORM.TYP = 1
	AND LMB_RULES_REPFORM.GROUP_ID = ".$session["group_id"]."
	AND LMB_REPORT_LIST.REFERENZ_TAB = -1";
	#ORDER BY LMB_REPORT_LIST.NAME (only maxdb76)
	*/
	$sqlquery = "SELECT DISTINCT LMB_REPORT_LIST.DEFFORMAT,LMB_REPORT_LIST.ODT_TEMPLATE,LMB_REPORT_LIST.ODS_TEMPLATE,LMB_REPORT_LIST.EXTENSION,LMB_REPORT_LIST.SAVENAME,LMB_REPORT_LIST.TARGET,LMB_REPORT_LIST.ERSTUSER,LMB_REPORT_LIST.ID,LMB_REPORT_LIST.GROUPLIST,LMB_REPORT_LIST.NAME,LMB_REPORT_LIST.REFERENZ_TAB,0 AS FID, 0 AS LEVEL
	FROM LMB_REPORT_LIST,LMB_RULES_REPFORM
	WHERE LMB_RULES_REPFORM.REPFORM_ID = LMB_REPORT_LIST.ID
	AND LMB_RULES_REPFORM.TYP = 1
	AND LMB_RULES_REPFORM.GROUP_ID IN (".implode(",",$session["subgroup"]).")
	AND PARENT_ID IS NULL
	ORDER BY REFERENZ_TAB,NAME";

	$rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
	if(!$rs) {$commit = 1;}
	while(lmbdb_fetch_row($rs)) {
		$key = lmbdb_result($rs, "ID");
		$gtabid = lmbdb_result($rs, "REFERENZ_TAB");
		$greportlist[$gtabid]["id"][$key] = lmbdb_result($rs, "ID");
		$greportlist[$gtabid]["name"][$key] = lmbdb_result($rs, "NAME");
		#$greportlist[$gtabid]["fid"][$key] = lmbdb_result($rs, "TARGET");
		#$greportlist[$gtabid]["grouplist"][$key] = lmbdb_result($rs, "GROUPLIST");
		#$greportlist[$gtabid]["level"][$key] = lmbdb_result($rs, "LEVEL");
		$greportlist[$gtabid]["erstuser"][$key] = lmbdb_result($rs, "ERSTUSER");
		$greportlist[$gtabid]["target"][$key] = lmbdb_result($rs, "TARGET");
		$greportlist[$gtabid]["savename"][$key] = lmbdb_result($rs, "SAVENAME");
		$greportlist[$gtabid]["extension"][$key] = lmbdb_result($rs, "EXTENSION");
		$greportlist[$gtabid]["odt_template"][$key] = lmbdb_result($rs, "ODT_TEMPLATE");
		$greportlist[$gtabid]["ods_template"][$key] = lmbdb_result($rs, "ODS_TEMPLATE");
		$greportlist[$gtabid]["defaultformat"][$key] = lmbdb_result($rs, "DEFFORMAT");
		
		if($greportlist[$gtabid]["odt_template"][$key]){$greportlist[$gtabid]["odt_template"][$key] = get_NameFromID($greportlist[$gtabid]["odt_template"][$key]);}else{$greportlist[$gtabid]["odt_template"][$key] = "";}
		if($greportlist[$gtabid]["ods_template"][$key]){$greportlist[$gtabid]["ods_template"][$key] = get_NameFromID($greportlist[$gtabid]["ods_template"][$key]);}else{$greportlist[$gtabid]["ods_template"][$key] = "";}
	}
	return $greportlist;
}

$greportlist_ = resultreportlist_();

if($greportlist_){
	# Extension Files
	$extfiles = read_dir($umgvar["pfad"]."/EXTENSIONS",1);

	foreach ($greportlist_ as $gtabid => $value0){
		
		if($gtab["table"][$gtabid]){
			$cat = $gtab["desc"][$gtabid];
		}else{
			$cat = $lang[1986];
		}
		echo "<tr class=\"tabSubHeader\"><td class=\"tabSubHeaderItem\" colspan=\"12\">" . $cat . "</td></tr>";
		
		if($greportlist_[$gtabid]["id"]){
			foreach ($greportlist_[$gtabid]["id"] as $key => $value){
				echo "<TR class=\"tabBody\">";

				if(!$greportlist_[$gtabid]["grouplist"][$key]){
					echo "<TD VALIGN=\"TOP\">&nbsp;".$key."&nbsp</TD>";
				}else{
					echo "<TD VALIGN=\"TOP\"><span style=\"color:red;\">&nbsp;".$key."&nbsp</span></TD>";
				}
				
				if(!$greportlist_[$gtabid]["grouplist"][$key] AND !$greportlist_[$gtabid]["extension"][$key]){
					echo "<TD VALIGN=\"TOP\"><A HREF=\"main_admin.php?action=setup_report_frameset&report_id=".$key."&referenz_tab=".$gtabid."\"><i class=\"lmb-icon lmb-pencil\" BORDER=\"0\" style=\"cursor:pointer\"></i></A></TD>";
				}else{
					echo "<TD></TD>";
				}
				
				echo "<TD ALIGN=\"CENTER\" VALIGN=\"TOP\"><i OnClick=\"report_delete('".$key."')\" class=\"lmb-icon lmb-trash\" BORDER=\"0\" style=\"cursor:pointer\"></i></TD>";

				echo "<TD valign=\"top\"><INPUT TYPE=\"TEXT\" NAME=\"report_changename[".$key."]\" VALUE=\"".$greportlist_[$gtabid]["name"][$key]."\" STYLE=\"width:160px;\" OnChange=\"document.form1.changename_id.value='$key';document.form1.action='main_admin.php';document.form1.action.value='setup_report_select';document.form1.submit();\"></TD>";
				
				echo "<TD NOWRAP valign=\"top\">".$gtab["desc"][$gtabid]."</TD>";
				
				echo "<TD NOWRAP valign=\"top\">";
				if($extfiles["name"]){
					echo "<SELECT NAME=\"report_extension[".$key."]\" OnChange=\"document.form1.changeext_id.value='$key';document.form1.action='main_admin.php';document.form1.action.value='setup_report_select';document.form1.submit();\" style=\"width:100px;\"><OPTION>";
					foreach ($extfiles["name"] as $key1 => $filename){
						if($extfiles["typ"][$key1] == "file" AND ($extfiles["ext"][$key1] == "ext" OR $extfiles["ext"][$key1] == "php")){
							$path = lmb_substr($extfiles["path"][$key1],lmb_strlen($umgvar["pfad"]),100);
							if($path.$filename == $greportlist_[$gtabid]["extension"][$key]){$SELECTED = "SELECTED";}else{$SELECTED = "";}
							echo "<OPTION VALUE=\"".$path.$filename."\" $SELECTED>".str_replace("/EXTENSIONS/","",$path).$filename;
						}
					}
					echo "</SELECT>";
				}
				echo "</TD>";

				echo "<TD NOWRAP valign=\"top\">";
				echo "<SELECT OnChange=\"document.getElementById('report_changetarget_$key').value=this.value;document.form1.changetarget_id.value='$key';document.form1.action='main_admin.php';document.form1.action.value='setup_report_select';document.form1.submit();\" style=\"width:100px;\"><OPTION VALUE=\"0\"></OPTION>";
				if($gtabid){
					$sqlquery = "SELECT ID FROM LDMS_STRUCTURE WHERE TAB_ID = $gtabid AND FIELD_ID = 0 AND TYP = 3";
					$rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
					if(!$rs){$commit = 1;}
					while(lmbdb_fetch_row($rs)){
						rep_sub_folderlist(lmbdb_result($rs,"ID"),$greportlist_[$gtabid]["target"][$key]);
					}
				}
				echo "</SELECT><input type=\"text\" style=\"width:30px;\" id=\"report_changetarget_$key\" name=\"report_changetarget[".$key."]\" value=\"".$greportlist_[$gtabid]["target"][$key]."\" OnChange=\"document.form1.changetarget_id.value='$key';document.form1.action='main_admin.php';document.form1.action.value='setup_report_select';document.form1.submit();\">";
				echo "</TD>";
				
				echo "<TD><INPUT TYPE=\"TEXT\" NAME=\"report_savename[$key]\" STYLE=\"width:100px\" VALUE=\"".htmlentities($greportlist_[$gtabid]["savename"][$key],ENT_QUOTES,$GLOBALS["umgvar"]["charset"])."\" OnChange=\"document.form1.savename_id.value='$key';document.form1.action='main_admin.php';document.form1.action.value='setup_report_select';document.form1.submit();\"></TD>";
				#echo "<TD NOWRAP valign=\"top\">".$userdat["bezeichnung"][$greportlist_[$gtabid]["erstuser"][$key]]."</TD>";
				echo "<TD><select name=\"report_defaultformat[$key]\" style=\"width:80px;\" onchange=\"document.form1.changedefaultformat_id.value='$key';document.form1.action.value='setup_report_select';document.form1.submit();\">";
				if($greportlist_[$gtabid]["defaultformat"][$key] == 'pdf'){$SELECTED="SELECTED";}else{$SELECTED="";}
				echo "<option value=\"pdf\" $SELECTED>fpdf</option>";
                if($greportlist_[$gtabid]["defaultformat"][$key] == 'tcpdf'){$SELECTED="SELECTED";}else{$SELECTED="";}
                echo "<option value=\"tcpdf\" $SELECTED>tcpdf</option>";
				if($greportlist_[$gtabid]["defaultformat"][$key] == 'odt'){$SELECTED="SELECTED";}else{$SELECTED="";}
				echo "<option value=\"odt\" $SELECTED>odt</option>";
				if($greportlist_[$gtabid]["defaultformat"][$key] == 'xml'){$SELECTED="SELECTED";}else{$SELECTED="";}
				echo "<option value=\"xml\" $SELECTED>xml</option>";
				echo "</select></TD>";
				echo "<TD nowrap><INPUT TYPE=\"TEXT\" NAME=\"report_odt_template[$key]\" VALUE=\"".$greportlist_[$gtabid]["odt_template"][$key]."\" STYLE=\"width:100px\" onchange=\"document.form1.changeootemplate_id.value='$key-odt';document.form1.action.value='setup_report_select';document.form1.submit();\">&nbsp;<i align=\"absbottom\" class=\"lmb-icon lmb-file-odt\" onclick=\"lmb_openMiniexplorer($key,'odt');\" title=\"odt-template\" style=\"cursor:pointer\"></i></TD>";
				echo "<TD nowrap><INPUT TYPE=\"TEXT\" NAME=\"report_ods_template[$key]\" VALUE=\"".$greportlist_[$gtabid]["ods_template"][$key]."\" STYLE=\"width:100px\" onchange=\"document.form1.changeootemplate_id.value='$key-ods';document.form1.action.value='setup_report_select';document.form1.submit();\">&nbsp;<i align=\"absbottom\" class=\"lmb-icon lmb-file-ods\" onclick=\"lmb_openMiniexplorer($key,'ods');\" title=\"ods-template\" style=\"cursor:pointer\"></i></TD>";
				echo "</TR>";
				echo "<input type=\"hidden\" name=\"report_".$key."\" VALUE=\"".htmlentities($greportlist_[$gtabid]["sql"][$key],ENT_QUOTES,$GLOBALS["umgvar"]["charset"])."\">";
				
			}

		}

	}

}

function rep_sub_folderlist($LEVEL,$select,$space=null){
	global $filestruct;
	global $ffilter;
	
	get_filestructure();
	
	if($filestruct["id"]){
		foreach($filestruct["id"] as $key => $value){
			if($filestruct["level"][$key] == $LEVEL AND $filestruct["view"][$key]){
				if($select == $filestruct["id"][$key]){$SELECTED = "SELECTED";}else{$SELECTED = "";}
				echo "<option value=\"".$key."\" $SELECTED>".$space.$filestruct["name"][$key];
				if(in_array($filestruct["id"][$key],$filestruct["level"])){
					$space .= "--";
					rep_sub_folderlist($filestruct["id"][$key],$select,$space);
				}
			}
		}
	}
}



?>

<TR><TD COLSPAN="12" class="tabFooter"></TD></TR>

<TR class="tabHeader">
<TD colspan="3"></TD>
<TD class="tabHeaderItem"><?=$lang[4]?></TD>
<TD class="tabHeaderItem"><?=$lang[164]?></TD>
<TD class="tabHeaderItem"><?=$lang[1163]?></TD>
<TD class="tabHeaderItem"><?=$lang[1464]?></TD>
<TD colspan="5" class="tabHeaderItem">&nbsp</TD>

<TR class="tabBody">

<TD colspan="3"></TD>

<TD><INPUT TYPE="TEXT" NAME ="report_name" SIZE="20"></TD>

<TD><SELECT NAME="referenz_tab"><OPTION VALUE="-1"></OPTION>
<?php
foreach ($tabgroup["id"] as $key0 => $value0) {
    echo '<optgroup label="' . $tabgroup["name"][$key0] . '">';
    foreach ($gtab["tab_id"] as $key => $value) {
		if($gtab["tab_group"][$key] == $value0){
			echo "<OPTION VALUE=\"".$value."\">".$gtab["desc"][$key]."</OPTION>";
		}
	}
	echo '</optgroup>';
}
?>
</SELECT></TD>
        
<TD><select name="report_format" style="width:80px;">
<option value="pdf">fpdf</option>
<option value="tcpdf">tcpdf</option>
<option value="odt">odt</option>
<option value="xml">xml</option>
</select></TD>
        

<TD>
<SELECT NAME="reportcopy"><OPTION VALUE="0"></OPTION>
<?php
if($greportlist_){
	foreach ($greportlist_ as $gtabid => $value0){
		if($greportlist_[$gtabid]["id"]){
			foreach ($greportlist_[$gtabid]["id"] as $key => $value){
				echo "<OPTION VALUE=\"".$key."\">".$greportlist_[$gtabid]["name"][$key]."</OPTION>\n";
			}
		}
	}
}
?>
</SELECT>
</TD>

<TD align="right"><INPUT TYPE="SUBMIT" NAME="new" VALUE="<?=$lang[1165]?>"></TD>
<TD align="right" colspan="4">&nbsp;</TD>

</TR>

<TR><TD COLSPAN="12" class="tabFooter"></TD></TR>

</TABLE>
</FORM>

</div>
