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
 * ID: 167
 */


#---------------------------- Bericht Kopieren -------------------------
if($reportcopy AND $new){

	/* --- Transaktion START -------------------------------------- */
	lmb_StartTransaction();

	if(!$report_name){
		$report_name = "new report";
	}

	#$report_name = preg_replace("/[^A_Za-z0-9]/","_",$report_name);
	#$report_name = lmb_substr(preg_replace("/[_]{1,}/","_",$report_name),0,18);
	$report_name = parse_db_string($report_name,160);

	$sqlquery = "SELECT REFERENZ_TAB,PAGE_STYLE,TARGET,DEFFORMAT FROM LMB_REPORT_LIST WHERE ID = $reportcopy";
	$rs = odbc_exec($db,$sqlquery) or errorhandle(odbc_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
	if(!$rs) {$commit = 1;}
	$referenz_tab = odbc_result($rs,"REFERENZ_TAB");
	$page_style = odbc_result($rs,"PAGE_STYLE");
	$target = odbc_result($rs,"TARGET");
	$defformat = odbc_result($rs,"DEFFORMAT");
	/* --- NEXT ID ---------------------------------------- */
	$report_id = next_conf_id("LMB_REPORT_LIST");
	/* --- Berichtliste ---------------------------------------- */
	$sqlquery = "INSERT INTO LMB_REPORT_LIST (ID,ERSTUSER,NAME,BESCHREIBUNG,REFERENZ_TAB,PAGE_STYLE,TARGET,EXTENSION,DEFFORMAT) VALUES($report_id,{$session['user_id']},'".parse_db_string($report_name,160)."','',$referenz_tab,'$page_style',".parse_db_int($target,3).",'".parse_db_string($reportextension,160)."','".parse_db_string($defformat,30)."')";
	$rs = odbc_exec($db,$sqlquery) or errorhandle(odbc_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
	$NEXTID = next_db_id("LMB_RULES_REPFORM");
	$sqlquery = "INSERT INTO LMB_RULES_REPFORM (ID,TYP,GROUP_ID,LMVIEW,REPFORM_ID) VALUES ($NEXTID,1,".$session["group_id"].",".LMB_DBDEF_TRUE.",$report_id)";
	$rs = odbc_exec($db,$sqlquery) or errorhandle(odbc_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);

	/* --- Next ID ---------------------------------------- */
	$NEXTKEYID = next_db_id("LMB_REPORTS","ID");

	/* --- Berichtdetail ---------------------------------------- */
	$sqlquery = "SELECT ID,
		EL_ID,
		ERSTUSER,
		TYP,
		POSX,
		POSY,
		HEIGHT,
		WIDTH,
		INHALT,
		DBFIELD,
		VERKN_BAUM,
		STYLE,
		DB_DATA_TYPE,
		SHOW_ALL,
		BERICHT_ID,
		Z_INDEX,
		LISTE,
		TAB,
		TAB_SIZE,
		TAB_EL_COL,
		TAB_EL_ROW,
		TAB_EL_COL_SIZE,
		HEADER,
		FOOTER,
		PIC_TYP,
		PIC_STYLE,
		PIC_SIZE,
		PIC_RES,
		PIC_NAME,
		BG,
		EXTVALUE FROM LMB_REPORTS WHERE BERICHT_ID = $reportcopy";
	$rs0 = odbc_exec($db,$sqlquery) or errorhandle(odbc_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
	if(!$rs0) {$commit = 1;}
	while(odbc_fetch_row($rs0)) {

		$typ = odbc_result($rs0,"TYP");
		$tab_size = odbc_result($rs0,"TAB_SIZE");
		$pic_name = odbc_result($rs0,"PIC_NAME");
		$elid = odbc_result($rs0,"EL_ID");
		
		if($typ == 'bild'){
			$ext = explode(".",$pic_name);
			$secname = $elid.substr(md5($elid.date("U").odbc_result($rs0,"PIC_NAME")),0,12).".".$ext[1];
			if(file_exists($umgvar['path']."/UPLOAD/report/".odbc_result($rs0,"TAB_SIZE"))){
				copy($umgvar['path']."/UPLOAD/report/".odbc_result($rs0,"TAB_SIZE"),$umgvar['path']."/UPLOAD/report/".$secname);
				if(file_exists($umgvar['path']."/TEMP/thumpnails/report/".odbc_result($rs0,"TAB_SIZE"))){
					copy($umgvar['path']."/TEMP/thumpnails/report/".odbc_result($rs0,"TAB_SIZE"),$umgvar['path']."/TEMP/thumpnails/report/".$secname);
				}
			}
			$tab_size = $secname;
			$pic_name = $secname;
		}
		
		/* --- Bericht einfügen ---------------------------------------- */
		
		$sqlquery = "INSERT INTO LMB_REPORTS (
		ID,
		EL_ID,
		ERSTUSER,
		TYP,
		POSX,
		POSY,
		HEIGHT,
		WIDTH,
		INHALT,
		DBFIELD,
		VERKN_BAUM,
		STYLE,
		DB_DATA_TYPE,
		SHOW_ALL,
		BERICHT_ID,
		Z_INDEX,
		LISTE,
		TAB,
		TAB_SIZE,
		TAB_EL_COL,
		TAB_EL_ROW,
		TAB_EL_COL_SIZE,
		HEADER,
		FOOTER,
		PIC_TYP,
		PIC_STYLE,
		PIC_SIZE,
		PIC_RES,
		PIC_NAME,
		BG,
		EXTVALUE
		)
		VALUES (
		$NEXTKEYID,
		".parse_db_int($elid,5).",
		".$session['user_id'].",
		'".$typ."',
		".parse_db_int(odbc_result($rs0,"POSX")).",
		".parse_db_int(odbc_result($rs0,"POSY")).",
		".parse_db_int(odbc_result($rs0,"HEIGHT")).",
		".parse_db_int(odbc_result($rs0,"WIDTH")).",
		'".parse_db_string(odbc_result($rs0,"INHALT"))."',
		'".odbc_result($rs0,"DBFIELD")."',
		'".odbc_result($rs0,"VERKN_BAUM")."',
		'".odbc_result($rs0,"STYLE")."',
		".parse_db_int(odbc_result($rs0,"DB_DATA_TYPE")).",
		".parse_db_bool(odbc_result($rs0,"SHOW_ALL")).",
		".parse_db_int($report_id).",
		".parse_db_int(odbc_result($rs0,"Z_INDEX")).",
		".parse_db_bool(odbc_result($rs0,"LISTE")).",
		".parse_db_int(odbc_result($rs0,"TAB")).",
		'".parse_db_string($tab_size)."',
		".parse_db_int(odbc_result($rs0,"TAB_EL_COL")).",
		".parse_db_int(odbc_result($rs0,"TAB_EL_ROW")).",
		".parse_db_int(odbc_result($rs0,"TAB_EL_COL_SIZE")).",
		".parse_db_bool(odbc_result($rs0,"HEADER")).",
		".parse_db_bool(odbc_result($rs0,"FOOTER")).",
		'".parse_db_string(odbc_result($rs0,"PIC_TYP"),3)."',
		'".parse_db_string(odbc_result($rs0,"PIC_STYLE"),250)."',
		'".parse_db_string(odbc_result($rs0,"PIC_SIZE"),50)."',
		".parse_db_int(odbc_result($rs0,"PIC_RES"),5).",
		'".parse_db_string($pic_name,20)."',
		".parse_db_int(odbc_result($rs0,"bg")).",
		'".parse_db_string(odbc_result($rs0,"EXTVALUE"),1000)."'
		)";
		
		$rs = odbc_exec($db,$sqlquery) or errorhandle(odbc_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);

		$NEXTKEYID++;
	}

	/* --- Transaktion ENDE -------------------------------------- */
	if($commit == 1){
		lmb_EndTransaction(0);
	} else {
		lmb_EndTransaction(1);
	}

#---------------------------- neuer Bericht -------------------------
}elseif($new AND $referenz_tab){
	if(!$report_name){
		$report_name = "new report";
	}

	/* --- Transaktion START -------------------------------------- */
	lmb_StartTransaction();

	#$report_name = preg_replace("/[^A-Za-z0-9]/","_",$report_name);
	#$report_name = lmb_substr(preg_replace("/[_]{1,}/","_",$report_name),0,18);
	$report_name = parse_db_string($report_name,160);
	
	# ------- Next ID ----------
	$report_id = next_conf_id("LMB_REPORT_LIST");
	if(!$reporttarget){$reporttarget_ = $report_id;}
	$sqlquery = "INSERT INTO LMB_REPORT_LIST (ID,ERSTUSER,NAME,BESCHREIBUNG,PAGE_STYLE,REFERENZ_TAB,TARGET,EXTENSION,DEFFORMAT) VALUES($report_id,{$session['user_id']},'".parse_db_string($report_name,50)."','".str_replace("'","''",$report_desc)."','210;295;5;5;5;5',".parse_db_int($referenz_tab,3).",".parse_db_int($reporttarget_,3).",'".parse_db_string($reportextension,160)."','pdf')";
	$rs = odbc_exec($db,$sqlquery) or errorhandle(odbc_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
	if(!$rs) {$commit = 1;}
	$NEXTID = next_db_id("LMB_RULES_REPFORM");
	$sqlquery = "INSERT INTO LMB_RULES_REPFORM (ID,TYP,GROUP_ID,LMVIEW,REPFORM_ID) VALUES ($NEXTID,1,".$session["group_id"].",".LMB_DBDEF_TRUE.",$report_id)";
	$rs = odbc_exec($db,$sqlquery) or errorhandle(odbc_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);

	/* --- Transaktion ENDE -------------------------------------- */
	if($commit == 1){
		lmb_EndTransaction(0);
	} else {
		lmb_EndTransaction(1);
	}

}elseif($new){
	die("<Script language=\"JavaScript\">document.location.href='main_admin.php?action=setup_report_select'</Script>");
}


# ---- Filestructure ----------------
if(!$commit AND $new AND !$reporttarget){
	if($lid = create_fs_report_dir($referenz_tab,$report_id,$report_name)){
		$sqlquery = "UPDATE LMB_REPORT_LIST SET TARGET = $lid WHERE ID = $report_id";
		$rs = odbc_exec($db,$sqlquery) or errorhandle(odbc_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
		if(!$rs) {$commit = 1;}
		get_filestructure(1);
	}
}

if($reportextension){
	die("<Script language=\"JavaScript\">document.location.href='main_admin.php?action=setup_report_select'</Script>");
}


/* --- Frameset ---------------------------------------------------------- */
?>

<div class="frame-container">
    <iframe name="report_main" src="main_admin.php?&action=setup_report_main&report_id=<?=$report_id?>&referenz_tab=<?=$referenz_tab?>&report_id=<?=$report_id?>" class="frame-fill"></iframe>
    <iframe name="report_menu" src="main_admin.php?&action=setup_report_menu&report_id=<?=$report_id?>&referenz_tab=<?=$referenz_tab?>&report_id=<?=$report_id?>" style="width: 240px;"></iframe>
</div>