<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */

global $DBA;

use Limbas\extra\template\select\TemplateSelector;
use Limbas\lib\db\functions\Dbf;
use Limbas\lib\general\Log\Log;

set_time_limit(3600); #60min
ob_implicit_flush();



$sqlquery = "SELECT LMB_CONF_TABLES.TABELLE,LMB_CONF_TABLES.TAB_ID,LMB_CONF_TABLES.BESCHREIBUNG,LMB_RULES_TABLES.SPECIFICPRIVILEGE 
FROM LMB_CONF_TABLES,LMB_RULES_TABLES 
WHERE LMB_RULES_TABLES.TAB_ID = LMB_CONF_TABLES.TAB_ID AND GROUP_ID = ".$session["group_id"]." AND LMB_CONF_TABLES.USERRULES = ".LMB_DBDEF_TRUE;
$rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
while(lmbdb_fetch_row($rs)) {
	$gtabsid = lmbdb_result($rs,"TAB_ID");
	$gtabs["tablename"][$gtabsid] = lmbdb_result($rs,"TABELLE");
	$gtabs["tabledesc"][$gtabsid] = lmbdb_result($rs,"BESCHREIBUNG");
	if(lmbdb_result($rs,"SPECIFICPRIVILEGE")){$gtabs["specific_userrules"][$gtabsid] = 1;}
}

# -------------------- Standard Ordner anlegen ---------------
function createDefaultDirs(){
	global $session;
	global $db;

	$sqlquery = "SELECT USER_ID,GROUP_ID,USERNAME FROM LMB_USERDB WHERE DEL = ".LMB_DBDEF_FALSE;
	$rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
	if(!$rs){$commit = 1;}
	while(lmbdb_fetch_row($rs)){
		create_default_dir(lmbdb_result($rs,"USER_ID"),lmbdb_result($rs,"USER_ID"),lmbdb_result($rs,"USERNAME"));
	}
}

# -------------------- Standard Ordner anlegen ---------------
function create_default_dir($userid,$groupid,$username){
	global $session;
	global $db;
	global $lang;

	$NEXTID = next_db_id('LDMS_STRUCTURE');
	$NEXTRID = next_db_id('LDMS_RULES');

	# Öffentlicher Ordner
	$sqlquery = "SELECT ID FROM LDMS_STRUCTURE WHERE ERSTUSER = 1 AND TYP = 1 AND FIX = ".LMB_DBDEF_TRUE." AND LEVEL = 0";
	$rs0 = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
	if(!$rs0){$commit = 1;}
	if(!lmbdb_result($rs0,"ID")){

		$sqlquery = "INSERT INTO LDMS_STRUCTURE (ID,NAME,LEVEL,ERSTUSER,ERSTGROUP,TYP,FIX,FIELD_ID,TAB_ID) VALUES ($NEXTID,'$lang[813]',0,1,$groupid,1,".LMB_DBDEF_TRUE.",0,0)";
		$rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
		if(!$rs){$commit = 1;}
		$sqlquery = "INSERT INTO LDMS_RULES (ID,GROUP_ID,FILE_ID,LMVIEW,LMADD,DEL,ADDF,EDIT) VALUES ($NEXTRID,1,$NEXTID,".LMB_DBDEF_TRUE.",".LMB_DBDEF_TRUE.",".LMB_DBDEF_TRUE.",".LMB_DBDEF_TRUE.",".LMB_DBDEF_TRUE.")";
		$rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
		if(!$rs){$commit = 1;}
		$info .= ".. public folder created for user <b>$username</b><br>";

		# Dokumente
		$sqlquery = "INSERT INTO LDMS_STRUCTURE (ID,NAME,LEVEL,ERSTUSER,ERSTGROUP,TYP,FIX,FIELD_ID,TAB_ID) VALUES (".($NEXTID+1).",'$lang[1705]',$NEXTID,1,$groupid,1,".LMB_DBDEF_FALSE.",0,0)";
		$rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
		$NEXTRID++;
		$sqlquery = "INSERT INTO LDMS_RULES (ID,GROUP_ID,FILE_ID,LMVIEW,LMADD,DEL,ADDF,EDIT) VALUES ($NEXTRID,1,".($NEXTID+1).",".LMB_DBDEF_TRUE.",".LMB_DBDEF_TRUE.",".LMB_DBDEF_TRUE.",".LMB_DBDEF_TRUE.",".LMB_DBDEF_TRUE.")";
		$rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
		if(!$rs){$commit = 1;}
		$info .= ".. document folder created for user <b>$username</b><br>";

		# Bilder
		$sqlquery = "INSERT INTO LDMS_STRUCTURE (ID,NAME,LEVEL,ERSTUSER,ERSTGROUP,TYP,FIX,FIELD_ID,TAB_ID) VALUES (".($NEXTID+2).",'$lang[1706]',$NEXTID,1,$groupid,1,".LMB_DBDEF_FALSE.",0,0)";
		$rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
		$NEXTRID++;
		$sqlquery = "INSERT INTO LDMS_RULES (ID,GROUP_ID,FILE_ID,LMVIEW,LMADD,DEL,ADDF,EDIT) VALUES ($NEXTRID,1,".($NEXTID+2).",".LMB_DBDEF_TRUE.",".LMB_DBDEF_TRUE.",".LMB_DBDEF_TRUE.",".LMB_DBDEF_TRUE.",".LMB_DBDEF_TRUE.")";
		$rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
		if(!$rs){$commit = 1;}
		$info .= ".. picture folder created <b>$username</b><br>";
	}

	$NEXTID = ($NEXTID+3);

	# Eigene Dateien
	$sqlquery = "SELECT ID FROM LDMS_STRUCTURE WHERE ERSTUSER = $userid AND TYP = 4 AND FIX = ".LMB_DBDEF_TRUE." AND LEVEL = 0";
	$rs0 = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
	if(!$rs0){$commit = 1;}
	if(!lmbdb_result($rs0,"ID")){
		$sqlquery = "INSERT INTO LDMS_STRUCTURE (ID,NAME,LEVEL,ERSTUSER,ERSTGROUP,TYP,FIX,TAB_ID,SORT) VALUES ($NEXTID,'$lang[812]',0,".$userid.",".$groupid.",4,".LMB_DBDEF_TRUE.",0,2)";
		$rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
		if(!$rs){$commit = 1;}
		$NEXTRID++;
		$info .= ".. user folder created for user <b>$username</b><br>";
	}

	echo $info;
}

function resetarguments(){
    global $db;
	global $session;
	global $db;
	global $gfield;
	global $gtab;

    set_time_limit(1800); # 30min
    require_once (COREPATH . 'admin/tables/argument.dao');

    $sqlquery = "SELECT LMB_CONF_TABLES.TABELLE,LMB_CONF_FIELDS.FIELD_NAME,LMB_CONF_FIELDS.TAB_ID,LMB_CONF_FIELDS.FIELD_ID,LMB_CONF_FIELDS.ARGUMENT FROM LMB_CONF_FIELDS,LMB_CONF_TABLES WHERE LMB_CONF_FIELDS.ARGUMENT_TYP = 15 AND LMB_CONF_FIELDS.TAB_ID = LMB_CONF_TABLES.TAB_ID";
    $rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
    while(lmbdb_fetch_row($rs)){
        $tab_id = lmbdb_result($rs,"TAB_ID");
        $field_id = lmbdb_result($rs,"FIELD_ID");
        $argument = lmbdb_result($rs,"ARGUMENT");

        if($argument) {
            if(arg_refresh($tab_id, $field_id, $argument)){
                lmb_alert('Rebuild complete for '. lmbdb_result($rs,"TABELLE"). ' '. lmbdb_result($rs,"FIELD_NAME"));
            }
        }
    }

}


if($umgvar["lock"]){
	$user_lock = "CHECKED";
}else{
	$user_lock = "";
}

if($locking){
	if($syslock){
		$sqlquery = "UPDATE LMB_UMGVAR SET NORM = 1 WHERE FORM_NAME = 'lock'";
		$rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);

		#$sqlquery = "UPDATE LMB_SESSION SET LOGOUT = ".LMB_DBDEF_TRUE." WHERE USER_ID != ".$session["user_id"];
		#$rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);

		$sqlquery = "DELETE FROM LMB_SESSION WHERE USER_ID != ".$session["user_id"];
		$rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);

		$user_lock = "CHECKED";
		$_SESSION["umgvar"]["lock"] = 1;
	}else{
		$sqlquery = "UPDATE LMB_UMGVAR SET NORM = 0 WHERE FORM_NAME = 'lock'";
		$rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
		$user_lock = "";
		$_SESSION["umgvar"]["lock"] = 0;
	}
}


if($newsystem){

	# delete all trigger
	lmb_TriggerDropFromDB();

	# delete all constrains
	lmb_dropAllForeignKeys();

	# drop views
	lmb_dropAllViews();
	
	# Tabellen löschen
	$odbc_table = Dbf::getTableList($DBA["DBSCHEMA"],null,"'TABLE'");
	foreach($odbc_table["table_name"] as $tkey => $tablename) {
		if(lmb_strtoupper(lmb_substr($tablename,0,4)) != "LMB_" AND lmb_strtoupper(lmb_substr($tablename,0,5)) != "LDMS_"){
		    lmb_dropTable($tablename);
		}
	}

	# systemtabellen leeren
	$sqlquery = "DELETE FROM LMB_ACTION_DEPEND";
	$rs = lmbdb_exec($db,$sqlquery);
	$sqlquery = "DELETE FROM LMB_ATTRIBUTE_D";
	$rs = lmbdb_exec($db,$sqlquery);
	$sqlquery = "DELETE FROM LMB_ATTRIBUTE_P";
	$rs = lmbdb_exec($db,$sqlquery);
	$sqlquery = "DELETE FROM LMB_ATTRIBUTE_W";
	$rs = lmbdb_exec($db,$sqlquery);
	$sqlquery = "DELETE FROM LMB_CHARTS";
	$rs = lmbdb_exec($db,$sqlquery);
	$sqlquery = "DELETE FROM LMB_CONF_FIELDS";
	$rs = lmbdb_exec($db,$sqlquery);
	$sqlquery = "DELETE FROM LMB_CONF_GROUPS";
	$rs = lmbdb_exec($db,$sqlquery);
	$sqlquery = "DELETE FROM LMB_CONF_TABLES";
	$rs = lmbdb_exec($db,$sqlquery);
	$sqlquery = "DELETE FROM LMB_CONF_VIEWS";
	$rs = lmbdb_exec($db,$sqlquery);
	$sqlquery = "DELETE FROM LMB_CONF_VIEWFIELDS";
	$rs = lmbdb_exec($db,$sqlquery);
	$sqlquery = "DELETE FROM LMB_CRONTAB";
	$rs = lmbdb_exec($db,$sqlquery);
	$sqlquery = "DELETE FROM LMB_FONTS";
	$rs = lmbdb_exec($db,$sqlquery);
	$sqlquery = "DELETE FROM LMB_FORMS";
	$rs = lmbdb_exec($db,$sqlquery);
	$sqlquery = "DELETE FROM LMB_FORM_LIST";
	$rs = lmbdb_exec($db,$sqlquery);
	$sqlquery = "DELETE FROM LMB_GROUPS WHERE GROUP_ID != 1";
	$rs = lmbdb_exec($db,$sqlquery);
	$sqlquery = "DELETE FROM LMB_GTAB_GROUPDAT";
	$rs = lmbdb_exec($db,$sqlquery);
	$sqlquery = "DELETE FROM LMB_GTAB_PATTERN";
	$rs = lmbdb_exec($db,$sqlquery);
	$sqlquery = "DELETE FROM LMB_GTAB_ROWSIZE";
	$rs = lmbdb_exec($db,$sqlquery);
	$sqlquery = "DELETE FROM LMB_REMINDER";
	$rs = lmbdb_exec($db,$sqlquery);
	$sqlquery = "DELETE FROM LMB_REMINDER_GROUP";
	$rs = lmbdb_exec($db,$sqlquery);
	$sqlquery = "DELETE FROM LMB_REMINDER_LIST";
	$rs = lmbdb_exec($db,$sqlquery);
	$sqlquery = "DELETE FROM LMB_HISTORY_ACTION";
	$rs = lmbdb_exec($db,$sqlquery);
	$sqlquery = "DELETE FROM LMB_HISTORY_BACKUP";
	$rs = lmbdb_exec($db,$sqlquery);
	$sqlquery = "DELETE FROM LMB_HISTORY_UPDATE";
	$rs = lmbdb_exec($db,$sqlquery);
	$sqlquery = "DELETE FROM LMB_HISTORY_USER";
	$rs = lmbdb_exec($db,$sqlquery);
	$sqlquery = "DELETE FROM LMB_INDIZE_D";
	$rs = lmbdb_exec($db,$sqlquery);
	$sqlquery = "DELETE FROM LMB_INDIZE_DS";
	$rs = lmbdb_exec($db,$sqlquery);
	$sqlquery = "DELETE FROM LMB_INDIZE_F";
	$rs = lmbdb_exec($db,$sqlquery);
	$sqlquery = "DELETE FROM LMB_INDIZE_FS";
	$rs = lmbdb_exec($db,$sqlquery);
	$sqlquery = "DELETE FROM LMB_INDIZE_HISTORY";
	$rs = lmbdb_exec($db,$sqlquery);
	$sqlquery = "DELETE FROM LMB_INDIZE_W";
	$rs = lmbdb_exec($db,$sqlquery);
	$sqlquery = "DELETE FROM LMB_LANG_DEPEND";
	$rs = lmbdb_exec($db,$sqlquery);
	$sqlquery = "DELETE FROM LMB_REPORTS";
	$rs = lmbdb_exec($db,$sqlquery);
	$sqlquery = "DELETE FROM LMB_REPORT_LIST";
	$rs = lmbdb_exec($db,$sqlquery);
	$sqlquery = "DELETE FROM LMB_RULES_ACTION WHERE GROUP_ID != 1";
	$rs = lmbdb_exec($db,$sqlquery);
	$sqlquery = "DELETE FROM LMB_RULES_DATASET";
	$rs = lmbdb_exec($db,$sqlquery);
	$sqlquery = "DELETE FROM LMB_RULES_FIELDS";
	$rs = lmbdb_exec($db,$sqlquery);
	$sqlquery = "DELETE FROM LMB_RULES_REPFORM";
	$rs = lmbdb_exec($db,$sqlquery);
	$sqlquery = "DELETE FROM LMB_RULES_TABLES";
	$rs = lmbdb_exec($db,$sqlquery);
	$sqlquery = "DELETE FROM LMB_SELECT_D";
	$rs = lmbdb_exec($db,$sqlquery);
	$sqlquery = "DELETE FROM LMB_SELECT_P";
	$rs = lmbdb_exec($db,$sqlquery);
	$sqlquery = "DELETE FROM LMB_SELECT_W";
	$rs = lmbdb_exec($db,$sqlquery);
	$sqlquery = "DELETE FROM LMB_SESSION";
	$rs = lmbdb_exec($db,$sqlquery);
	$sqlquery = "DELETE FROM LMB_SNAP";
	$rs = lmbdb_exec($db,$sqlquery);
	$sqlquery = "DELETE FROM LMB_SNAP_SHARED";
	$rs = lmbdb_exec($db,$sqlquery);
	$sqlquery = "DELETE FROM LMB_TABLETREE";
	$rs = lmbdb_exec($db,$sqlquery);
	$sqlquery = "DELETE FROM LMB_TRIGGER";
	$rs = lmbdb_exec($db,$sqlquery);
    $sqlquery = "DELETE FROM LMB_CUSTVAR";
    $rs = lmbdb_exec($db,$sqlquery);
    $sqlquery = "DELETE FROM LMB_CUSTVAR_DEPEND";
	$rs = lmbdb_exec($db,$sqlquery);
	$sqlquery = "DELETE FROM LMB_USERDB WHERE USER_ID != 1";
	$rs = lmbdb_exec($db,$sqlquery);
	$sqlquery = "DELETE FROM LMB_USER_COLORS WHERE USERID != 1";
	$rs = lmbdb_exec($db,$sqlquery);
	$sqlquery = "DELETE FROM LMB_USRGRP_LST";
	$rs = lmbdb_exec($db,$sqlquery);
	$sqlquery = "DELETE FROM LMB_REVISION";
	$rs = lmbdb_exec($db,$sqlquery);
	$sqlquery = "DELETE FROM LMB_WFL";
	$rs = lmbdb_exec($db,$sqlquery);
	$sqlquery = "DELETE FROM LMB_WFL_HISTORY";
	$rs = lmbdb_exec($db,$sqlquery);
	$sqlquery = "DELETE FROM LMB_WFL_INST";
	$rs = lmbdb_exec($db,$sqlquery);
	$sqlquery = "DELETE FROM LMB_WFL_TASK";
	$rs = lmbdb_exec($db,$sqlquery);
	$sqlquery = "DELETE FROM LDMS_FAVORITES";
	$rs = lmbdb_exec($db,$sqlquery);
	$sqlquery = "DELETE FROM LDMS_RULES";
	$rs = lmbdb_exec($db,$sqlquery);
	$sqlquery = "DELETE FROM LDMS_STRUCTURE";
	$rs = lmbdb_exec($db,$sqlquery);
	$sqlquery = "UPDATE LMB_ACTION SET EXTENSION = ''";
	$rs = lmbdb_exec($db,$sqlquery);
	$sqlquery = "DELETE FROM LMB_CHART_LIST";
	$rs = lmbdb_exec($db,$sqlquery);
	$sqlquery = "DELETE FROM LMB_CHARTS";
	$rs = lmbdb_exec($db,$sqlquery);
	$sqlquery = "DELETE FROM LMB_CODE_FAVORITES";
	$rs = lmbdb_exec($db,$sqlquery);
	$sqlquery = "DELETE FROM LMB_CRYPTO_PRIVATE_KEYS";
	$rs = lmbdb_exec($db,$sqlquery);
	$sqlquery = "DELETE FROM LMB_CRYPTO_PUBLIC_KEYS";
	$rs = lmbdb_exec($db,$sqlquery);
	$sqlquery = "DELETE FROM LMB_CURRENCY_RATE";
	$rs = lmbdb_exec($db,$sqlquery);
    $sqlquery = "DELETE FROM LMB_CUSTMENU";
	$rs = lmbdb_exec($db,$sqlquery);
	$sqlquery = "DELETE FROM LMB_CUSTMENU_LIST";
	$rs = lmbdb_exec($db,$sqlquery);
	$sqlquery = "DELETE FROM LMB_CURRENCY_RATE";
	$rs = lmbdb_exec($db,$sqlquery);
    $sqlquery = "DELETE FROM LMB_DASHBOARD_WIDGETS";
	$rs = lmbdb_exec($db,$sqlquery);
	$sqlquery = "DELETE FROM LMB_DASHBOARDS";
	$rs = lmbdb_exec($db,$sqlquery);
	$sqlquery = "DELETE FROM LMB_EXTERNAL_STORAGE";
	$rs = lmbdb_exec($db,$sqlquery);
	$sqlquery = "DELETE FROM LMB_FIELD_TYPES_DEPEND";
	$rs = lmbdb_exec($db,$sqlquery);
	$sqlquery = "DELETE FROM LMB_GTAB_STATUS_SAVE";
	$rs = lmbdb_exec($db,$sqlquery);
	$sqlquery = "DELETE FROM LMB_MULTITENANT";
	$rs = lmbdb_exec($db,$sqlquery);
	$sqlquery = "DELETE FROM LMB_PRINTERS";
	$rs = lmbdb_exec($db,$sqlquery);
	$sqlquery = "DELETE FROM LMB_DEFAULT_TEMPLATES";
	$rs = lmbdb_exec($db,$sqlquery);
	$sqlquery = "DELETE FROM LMB_SYNC_CACHE";
	$rs = lmbdb_exec($db,$sqlquery);
	$sqlquery = "DELETE FROM LMB_SYNC_CLIENTS";
	$rs = lmbdb_exec($db,$sqlquery);
    $sqlquery = "DELETE FROM LMB_SYNC_CONF";
	$rs = lmbdb_exec($db,$sqlquery);
	$sqlquery = "DELETE FROM LMB_SYNC_GLOBAL";
	$rs = lmbdb_exec($db,$sqlquery);
	$sqlquery = "DELETE FROM LMB_SYNC_HISTORY";
	$rs = lmbdb_exec($db,$sqlquery);
    $sqlquery = "DELETE FROM LMB_SYNC_LOG";
	$rs = lmbdb_exec($db,$sqlquery);
	$sqlquery = "DELETE FROM LMB_SYNC_PARAMS";
	$rs = lmbdb_exec($db,$sqlquery);
	$sqlquery = "DELETE FROM LMB_SYNC_TEMPLATE";
	$rs = lmbdb_exec($db,$sqlquery);
    $sqlquery = "DELETE FROM LMB_SYNCSTRUCTURE_TEMPLATE";
	$rs = lmbdb_exec($db,$sqlquery);

	lmb_dropTable('LDMS_FILES');
	lmb_dropTable('LDMS_META');


	# Dateitabellen anlegen
	create_default_files();
	
	# Dateisystem neu anlegen für User 1
	createDefaultDirs();

    # Eigene globale Variablen Tabelle anlegen
    create_custvar_table();

	# Menürechte neu einlesen für User 1
	#$check_all = 1;
	#require("admin/tools/sysupdate/grusrref.php");
	
	# Temporäre Dateien löschen
	$delpath = array( TEMPPATH . "log/",TEMPPATH."thumpnails/",TEMPPATH."txt/");
	foreach ($delpath as $key => $value){
		rmdirr($value);
	}

}


function create_default_files(){
	global $db, $DBA;

	$tab_group = 0;

	require_once(COREPATH . 'admin/tables/tab.lib');
	require_once(COREPATH . 'extra/explorer/filestructure.lib');

	# Alte Tabellen löschen
	$sqlquery = "SELECT TAB_ID,TABELLE,TAB_GROUP FROM LMB_CONF_TABLES WHERE UPPER(TABELLE) LIKE 'LDMS_%'";
	$rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
	while(lmbdb_fetch_row($rs)){
		if(lmb_strtoupper(lmbdb_result($rs,"TABELLE")) == lmb_strtoupper("LDMS_FILES")){delete_tab(lmbdb_result($rs,"TAB_ID"));}
		elseif(lmb_strtoupper(lmbdb_result($rs,"TABELLE")) == lmb_strtoupper("LDMS_META")){delete_tab(lmbdb_result($rs,"TAB_ID"));}
		$tab_group = lmbdb_result($rs,"TAB_GROUP");
	}
	
	$odbc_table = Dbf::getTableList($DBA["DBSCHEMA"],"LDMS_%","'TABLE'");
	foreach($odbc_table["table_name"] as $tkey => $tablename) {
		if(lmb_strtoupper($tablename) == "LDMS_FILES"){
		    lmb_dropTable('LDMS_FILES');
		}
		if(lmb_strtoupper($tablename) == "LDMS_META"){
		    lmb_dropTable('LDMS_META');
		}
	}
	
	#  delete user-definition
	$prepare_string = "UPDATE LMB_USERDB SET UFILE = ?";
	if(!lmb_PrepareSQL($prepare_string,array(''),__FILE__,__LINE__)){$commit = 1;}

	# Gruppe anlegen
	$sqlquery = "SELECT * FROM LMB_CONF_GROUPS WHERE ID = $tab_group";
	$rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
	if(lmbdb_fetch_row($rs)){
	    $group_id = lmbdb_result($rs,"ID");
	}else{
	    $group_id = add_tabgroup("Limbassys","Limbas Systemtabellen");
	}

	if($group_id){
		# Tabelle LDMS_FILES anlegen
		if($tab_id = add_tab("LDMS_FILES",$group_id,"Files",0,3,1)){
			# Limbas Felder anlegen
			$xfield = extended_fields_files();
			add_extended_fields($xfield[0],$tab_id[0],1);
			# Systemfelder anlegen
			add_extended_fields_sys("LDMS_FILES",$xfield[1]);
			# Sonderfelder anlegen
		}
		# Tabelle LDMS_META anlegen
		if($tab_id = add_tab("LDMS_META",$group_id,"Files_Meta",$tab_id[0],3,1)){
			# Felder anlegen
			$nfield = extended_fields_filesmeta();
			add_extended_fields($nfield,$tab_id[0],1);
		}
	}

}

function edit_lockmessage($value){
	global $umgvar;

	# --- update lock.txt ----------------------------
	$file = fopen(TEMPPATH . 'lock.txt',"w");
	fputs($file,$value);
	fclose($file);
}

function get_lockmessage(){
	global $umgvar;

	# --- get lock.txt ----------------------------
	if(file_exists(TEMPPATH.'lock.txt')){
		$content = file(TEMPPATH.'lock.txt');
		return implode("",$content);
	}
}

function create_custvar_table(){
    global $db;

    require_once(COREPATH . 'admin/tables/tab.lib');

    lmb_dropTable('LMB_CUSTVAR_DEPEND');

    $tab_group = 0;
    $sqlquery = "SELECT TAB_GROUP FROM LMB_CONF_TABLES WHERE UPPER(TABELLE) LIKE 'LDMS_%' LIMIT 1";
    $rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
    while(lmbdb_fetch_row($rs)){
        $tab_group = lmbdb_result($rs,"TAB_GROUP");
    }

    if($tab_group){
        # Tabelle LMB_CUSTVAR_DEPEND anlegen
        if($tab_id = add_tab("LMB_CUSTVAR_DEPEND",$tab_group,"Custom Vars",0,3,1)){
            # Felder anlegen
            $nfield = extended_fields_custvar();
            add_extended_fields($nfield,$tab_id[0],1);
        }
    }

}

/*

# already done in gtab_array.lib (SNAP_loadInSession)
function snaprefresh(){
	global $db;

	require_once(COREPATH . "extra/snapshot/snapshot.lib");

	$sqlquery = "SELECT * FROM LMB_SNAP";
	$rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);

	while(lmbdb_fetch_row($rs)){
		$snapfilter = SNAP_revalidate(lmbdb_result($rs,"FILTER"),lmbdb_result($rs,"TABID"),lmbdb_result($rs,"ID"));
	}
}
*/

function drulesReset($tabid){
	global $db;
	
	if(!is_numeric($tabid)){return false;}
	
	$sqlquery = "DELETE FROM LMB_RULES_DATASET WHERE TABID = $tabid";
	$rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
	if(!$rs){$commit = 1;}

	if(!$commit){return true;}else{return false;}
}

function specificUserrules($tabid){
	global $db;
	global $gtabs;
	global $session;
	
	if(!is_numeric($tabid)){return false;}
	require_once(COREPATH . 'gtab/gtab.lib');
	
	$sqlquery = "SELECT ID FROM ".$gtabs["tablename"][$tabid];
	$rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
	if(!$rs){$commit = 1;}
	while(lmbdb_fetch_row($rs)){
		$rec[] = lmbdb_result($rs,"ID");
	}
	if(!add_GtabUserRules($tabid,$rec,array(null,"g","v",null,2),1)){$commit = 1;}
	
	if(!$commit){return true;}else{return false;}
}


/* --- clean table settings ------------------------------- */
function cleantablesettings(){
	global $db;
    global $gtab;

	$sqlquery = "SELECT ID,FIELD_TYPE,FIELD_NAME,FIELD_ID,TAB_ID,VERKNTABID,VERKNVIEW,VERKNSEARCH,VERKNFIND FROM LMB_CONF_FIELDS WHERE FIELD_TYPE < 100";
	$rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
	if(!$rs){$commit = 1;}
	while(lmbdb_fetch_row($rs)){
		$id = lmbdb_result($rs,"ID");
        $fl['tab_id'][$id] = lmbdb_result($rs,"TAB_ID");
        $fl['field_name'][$id] = lmbdb_result($rs,"FIELD_NAME");
        $fl['verkntab_id'][$id] = lmbdb_result($rs,"VERKNTABID");
        $fl['gf'][lmbdb_result($rs, "TAB_ID")][lmbdb_result($rs, "FIELD_ID")] = 1;
        if(lmbdb_result($rs,"FIELD_TYPE") == 11) {
            if (lmbdb_result($rs, "VERKNVIEW")) {
                $verknview[$id] = explode('?', lmbdb_result($rs, "VERKNVIEW"));
            }
            if (lmbdb_result($rs, "VERKNSEARCH")) {
                $verknsearch[$id] = explode('?', lmbdb_result($rs, "VERKNSEARCH"));
            }
            if (lmbdb_result($rs, "VERKNFIND")) {
                $verknfind[$id] = explode('?', lmbdb_result($rs, "VERKNFIND"));
            }
        }
    }

     // clean verknview sewttings
    function flcl(&$fl,&$verknview,$type){
        global $db;
        global $gtab;

        foreach ($verknview as $fkey => $verknviewval) {
            if (!$verknviewval) {
                continue;
            }
            $verknviewval_ = explode(',', $verknviewval[0]);
            if ($verknviewval[0]) {
                $missmatch = 0;
                $new_verknview = array();
                foreach ($verknviewval_ as $key => $verknviewfield) {
                    if (!$fl['gf'][$fl['verkntab_id'][$fkey]][$verknviewfield]) {
                        $missmatch = 1;
                    } else {
                        $new_verknview[] = $verknviewfield;
                    }
                }
                if ($missmatch) {
                    $new_verknview_ = implode(',', $new_verknview);
                    if ($verknviewval[1]) {
                        $new_verknview_ .= '?' . $verknviewval[1];
                    }
                    $sqlquery = "UPDATE LMB_CONF_FIELDS SET $type = '" . $new_verknview_ . "' WHERE ID = $fkey";
                    $rs = lmbdb_exec($db, $sqlquery) or errorhandle(lmbdb_errormsg($db), $sqlquery, $action, __FILE__, __LINE__);
                    Log::info("clean $type relation settings for table [" . $gtab['table'][$fl['tab_id'][$fkey]] . "] field [" . $fl['field_name'][$fkey] . "]");
                }
            }
        }
    }

    flcl($fl,$verknview,'VERKNVIEW');
    flcl($fl,$verknsearch,'VERKNSEARCH');
    flcl($fl,$verknfind,'VERKNFIND');

}




if($mselect){
	require_once(COREPATH . 'admin/tools/multiselect_refresh.lib');
	multiselectRefreshCount();
	relationRefreshCount();
    usergroupRefreshCount();
}

if($specific_userrules){
	specificUserrules($specific_userrules);
}

if($drules_reset){
	drulesReset($drules_reset);
}

if($sessiondel){
	$sqlquery = "DELETE FROM LMB_SESSION";
	$rs = lmbdb_exec($db,$sqlquery);
	ini_set('soap.wsdl_cache_ttl', 1);
}

# --- Dateisystem Files / Meta erstellen ----
if($filetabs){
	create_default_files();
}

# Default Ordner
if($filestructure){
	createDefaultDirs();
}


if($deleteuserfilesave){
    lmb_delete_user_filesave();
}

if($deletereportcache){
    TemplateSelector::resetTemplateCache();
}

#if($snaprefresh){
#	snaprefresh();
#}

if($refresh_foreignkey){
	lmb_rebuildForeignKey($rebuild);
    $modalouput = 'rebuild foreign keys';
}

if($refresh_trigger){
    lmb_TriggerRebuildSystem($rebuild);
    $modalouput = 'rebuild trigger';
}

if($refresh_indexes){
	lmb_rebuildIndex($rebuild);
    $modalouput = 'rebuild Indexes';
}

if($refresh_procedures){
	if(!Dbf::createLimbasVknFunction($DBA["DBSCHEMA"],true)){
		lmb_alert("some errors by rebuild database procedures, check error logs..");
	}else{
		lmb_alert("database procedures successfully build!");
	}
}

if($refresh_hashes){
    $success = 1;
    if(!$gtab["checksum"]){return;}
    foreach($gtab["checksum"] as $tabid => $value){
        if(!lmb_calculateChecksum($tabid)){
            $success = 0;
        }
    }
    if($success) {
        lmb_alert("dataset hashes successfully build!");
    } else {
        lmb_alert("some errors by rebuild dataset hashes, check error logs..");
    }
}

if($refresh_squences){
	#if(lmb_rebuildSequences(null,null,$rebuild)){
    #    lmb_alert("sequences successfully created");}else{lmb_alert("failed to create sequences!");
    #}
    lmb_rebuildSequences(null,null,$rebuild);
    $modalouput = 'rebuild Sequences';
}

# lock message
if($lockmessage){
	edit_lockmessage($lockmessage);
}

if($delete_thumbs){
	rmdirr(TEMPPATH.'thumpnails/',0,1);
}

if($delete_files){
	rmdirr(TEMPPATH.'txt/',0,1);
}

if($refresh_thumps){
	$sqlquery = "UPDATE LDMS_FILES SET THUMB_OK = TRUE";
	$rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
}

if($cleantablesettings){
	cleantablesettings();
    $modalouput = 'clean table relation settings';
}

if($validateFiles){

    require_once (COREPATH . 'admin/tools/validateFiles.php');
    lmb_validateFilesExists();

    return;

}



if($createExifConf){
    require_once(COREPATH . 'extra/explorer/metadata.lib');
	create_exif_conf();
}

if($resetarguments){
	resetarguments();
}

$lock_message = get_lockmessage();

    ?>
    <div class="modal fade" id="modal-system">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title"><?=$modalouput?></h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
    <?php

    $log = Log::getLogger()->getLog(false);

    if ($log) {
        foreach ($log as &$logEntry) {
            if($logEntry->level->name == 'ERROR') {
                echo '<div class="alert alert-danger" role="alert">'.$logEntry->message.'</div>';
                echo "<script>$(function() { $('.modal-title').css('color','red'); });</script>";
            }
            $bzm++;
        }
    }

    if ($log) {
        foreach ($log as &$logEntry) {
            if($logEntry->level->name == 'INFO') {
                echo '<div class="alert alert-info" role="alert">'.$logEntry->message.'</div>';
            }
            $bzm++;
        }
    }

    if(!$bzm){
         echo '<div class="alert alert-info" role="alert">nothing todo!</div>';
    }

    ?>
        </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          </div>
        </div>
      </div>
    </div>
    <?php if($modalouput){?>
    <script>
        $(function() {
            $('#modal-system').modal('show');
        });
    </script>
    <?php }?>

    <?php



?>

<script>

function mselect(evt) {
	link = confirm("<?=$lang[2033]?>");
	if(link) {
		limbasWaitsymbol(evt,1);
		document.location.href="main_admin.php?action=setup_sysupdate&mselect=1";
	}
}

function resetarguments(evt) {
	link = confirm("<?=$lang[3069]?>");
	if(link) {
		limbasWaitsymbol(evt,1);
		document.location.href="main_admin.php?action=setup_sysupdate&resetarguments=1";
	}
}

function resetthumps(evt) {
	link = confirm("<?=$lang[2549]?>");
	if(link) {
		limbasWaitsymbol(evt,1);
		document.location.href="main_admin.php?action=setup_sysupdate&refresh_thumps=1";
	}
}

function deletethumbs(evt) {
	link = confirm("<?=$lang[2548]?>");
	if(link) {
		limbasWaitsymbol(evt,1);
		document.location.href="main_admin.php?action=setup_sysupdate&delete_thumbs=1";
	}
}

function deletefiles(evt) {
	link = confirm("<?=$lang[2694]?>");
	if(link) {
		limbasWaitsymbol(evt,1);
		document.location.href="main_admin.php?action=setup_sysupdate&delete_files=1";
	}
}

function foreignrefresh(evt) {
	link = confirm("<?=$lang[2477]?>");
	if(link) {
		limbasWaitsymbol(evt,1);
        rebuild = ($('#rebuild_fkey').prop("checked") == true) ? '1' : '';
		document.location.href="main_admin.php?action=setup_sysupdate&refresh_foreignkey=1&rebuild="+rebuild;
	}
}

function triggerrefresh(evt) {
	link = confirm("<?=$lang[2696]?>");
	if(link) {
		limbasWaitsymbol(evt,1);
        rebuild = ($('#rebuild_trigger').prop("checked") == true) ? '1' : '';
		document.location.href="main_admin.php?action=setup_sysupdate&refresh_trigger=1&rebuild="+rebuild;
	}
}

function indexrefresh(evt) {
	link = confirm("<?= $lang[2722] ?>");
	if(link) {
		limbasWaitsymbol(evt,1);
        rebuild = ($('#rebuild_index').prop("checked") == true) ? '1' : '';
		document.location.href="main_admin.php?action=setup_sysupdate&refresh_indexes=1&rebuild="+rebuild;
	}
}

function procedurerefresh(evt) {
	link = confirm("<?= $lang[2653] ?>");
	if(link) {
		limbasWaitsymbol(evt,1);
		document.location.href="main_admin.php?action=setup_sysupdate&refresh_procedures=1";
	}
}

function hashrefresh(evt) {
	link = confirm("<?=$lang[3156]?> <?=$lang[1038]?>");
	if(link) {
		limbasWaitsymbol(evt,1);
		document.location.href="main_admin.php?action=setup_sysupdate&refresh_hashes=1";
	}
}

function squencerefresh(evt) {
	link = confirm("<?= $lang[2663] ?>");
	if(link) {
		limbasWaitsymbol(evt,1);
        rebuild = ($('#rebuild_sequ').prop("checked") == true) ? '1' : '';
		document.location.href="main_admin.php?action=setup_sysupdate&refresh_squences=1&rebuild="+rebuild;
	}
}

function lrefresh() {
	if(confirm("<?=$lang[1265]?>")) {

        $('#refresh-frame-title').text('<?=$lang[1056]?>');
        $('#refresh-frame').modal('show');
        $('#refresh-frame-body').html('<iframe src="main_admin.php?action=setup_linkref" class="w-100 h-100"></iframe>');
	}
}

function gurefresh(DATA) {
	if(confirm("<?=$lang[1264]?>")) {
	    
	    $('#refresh-frame-title').text('<?=$lang[1054]?>');
        $('#refresh-frame').modal('show');
	    $('#refresh-frame-body').html('<iframe src="main_admin.php?action=setup_grusrref&check_all=' + DATA+ '" class="w-100 h-100"></iframe>');
	}
}

function drulesReset(evt) {
	link = confirm("<?= $lang[2551]."?\\n".$lang[2553] ?>");
	if(link) {
		limbasWaitsymbol(evt,1);
		document.location.href="main_admin.php?action=setup_sysupdate&drules_reset="+document.getElementById('drules_reset').value;
	}
}

function specificUserrules(evt) {
	link = confirm("<?= $lang[2552]."?" ?>");
	if(link) {
		limbasWaitsymbol(evt,1);
		document.location.href="main_admin.php?action=setup_sysupdate&specific_userrules="+document.getElementById('specific_userrules').value;
	}
}

function filestructure(evt) {
	link = confirm("<?=$lang[2697]?>");
	if(link) {
		limbasWaitsymbol(evt,1);
		document.location.href="main_admin.php?action=setup_sysupdate&filestructure=1";
	}
}

function deleteuserfilesave(evt) {
	link = confirm("<?=$lang[2328]?>");
	if(link) {
		limbasWaitsymbol(evt,1);
		document.location.href="main_admin.php?action=setup_sysupdate&deleteuserfilesave=1";
	}
}

function snaprefresh(evt) {
	snap = confirm("<?=$lang[2345]?>");
	if(snap) {
		limbasWaitsymbol(evt,1);
		document.location.href="main_admin.php?action=setup_sysupdate&snaprefresh=1";
	}
}

function sessiondel(evt) {
	link = confirm("<?=$lang[1048]?>");
	if(link) {
		limbasWaitsymbol(evt,1);
		document.location.href="main_admin.php?action=setup_sysupdate&sessiondel=1";
	}
}

function filetabs(evt) {
	link = confirm("<?=$lang[1991]?>");
	if(link) {
		limbasWaitsymbol(evt,1);
		document.location.href="main_admin.php?action=setup_sysupdate&filetabs=1";
	}
}

function newsystem(evt) {
	link = confirm("<?=$lang[1594]?>");
	if(link) {
		limbasWaitsymbol(evt,1);
		document.location.href="main_admin.php?action=setup_sysupdate&newsystem=1";
	}
}

function cleantablesettings(evt) {
	link = confirm("<?=$lang[2734]?>?");
	if(link) {
		limbasWaitsymbol(evt,1);
		document.location.href="main_admin.php?action=setup_sysupdate&cleantablesettings=1";
	}
}

function createExifConf(evt) {

    limbasWaitsymbol(evt, 1);
    document.location.href = "main_admin.php?action=setup_sysupdate&createExifConf=1";

}

function deletereportcache(evt) {

    limbasWaitsymbol(evt, 1);
    document.location.href = "main_admin.php?action=setup_sysupdate&deletereportcache=1";

}

function validateFiles(evt) {

	if(confirm("<?=$lang[3114]?>")) {

	    $('#refresh-frame-title').text('<?=$lang[3114]?>');
        $('#refresh-frame').modal('show');
	    $('#refresh-frame-body').html('<div style="height:600px"><iframe src="main_admin.php?action=setup_sysupdate&validateFiles=1" class="w-100 h-100"></iframe></div>');
	}

}

</SCRIPT>







<div class="container-fluid p-3">
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <FORM ACTION="main_admin.php" METHOD="post" name="form1">
                        <INPUT TYPE="hidden" NAME="action" VALUE="setup_sysupdate">
                        <INPUT TYPE="hidden" NAME="locking">

                        <table class="table table-sm table-striped mb-0">
                            <TR class="table-section"><TD colspan="3"><?=$lang[2481]?></TD></TR>
                            <TR><TD><?=$lang[2030]?></TD>
                                <TD class="text-end"><button type="button" class="btn btn-primary btn-sm" onclick="mselect(event);">OK</button></TD></TR>
                            <TR><TD><?=$lang[3069]?></TD>
                                <TD class="text-end"><button type="button" class="btn btn-primary btn-sm" onclick="resetarguments(event);">OK</button></TD></TR>
                            <TR><TD><?=$lang[2547]?></TD>
                                <TD class="text-end"><button type="button" class="btn btn-primary btn-sm" onclick="resetthumps(event);">OK</button></TD></TR>
                            <TR><TD><?=$lang[2546]?></TD>
                                <TD class="text-end"><button type="button" class="btn btn-primary btn-sm" onclick="deletethumbs(event);">OK</button></TD></TR>
                            <TR><TD><?=$lang[2695]?></TD>
                                <TD class="text-end"><button type="button" class="btn btn-primary btn-sm" onclick="deletefiles(event);">OK</button></TD></TR>
                            <TR><TD><?=$lang[2734]?></TD>
                                <TD class="text-end"><button type="button" class="btn btn-primary btn-sm" onclick="cleantablesettings(event);">OK</button></TD></TR>
                            <TR><TD><?=$lang[2862]?></TD>
                                <TD class="text-end"><button type="button" class="btn btn-primary btn-sm" onclick="createExifConf(event);">OK</button></TD></TR>
                            <TR><TD><?=$lang[1137]?> Cache <?=$lang[550]?></TD>
                                <TD class="text-end"><button type="button" class="btn btn-primary btn-sm" onclick="deletereportcache(event);">OK</button></TD></TR>

                            <TR class="table-section"><TD colspan="3">DMS</TD></TR>
                            <TR><TD><?=$lang[3114]?></TD>
                                <TD class="text-end"><button type="button" class="btn btn-primary btn-sm" onclick="validateFiles(event);">OK</button></TD></TR>

                            <TR class="table-section"><TD colspan="3"><?=$lang[2482]?></TD></TR>

                            <TR><TD><?=$lang[2476]?></TD>
                                <TD class="text-end">rebuild <input type="checkbox" id="rebuild_fkey"> <button type="button" class="btn btn-primary btn-sm" onclick="foreignrefresh(event);">OK</button></TD></TR>

                            <?php if(LMB_DBFUNC_PROCEDUREHANDLE){?>
                                <TR><TD><?=$lang[2721]?></TD>
                                    <TD class="text-end">rebuild <input type="checkbox" id="rebuild_index"> <button type="button" class="btn btn-primary btn-sm" onclick="indexrefresh(event);">OK</button></TD></TR>
                                <TR><TD><?=$lang[2488]?></TD>
                                    <TD class="text-end">rebuild <input type="checkbox" id="rebuild_trigger"> <button type="button" class="btn btn-primary btn-sm" onclick="triggerrefresh(event);">OK</button></TD></TR>
                                <?php if(LMB_DBFUNC_SEQUENCE){?>
                                    <TR><TD><?=$lang[2662]?></TD>
                                        <TD class="text-end">rebuild <input type="checkbox" id="rebuild_sequ"> <button type="button" class="btn btn-primary btn-sm" onclick="squencerefresh(event);">OK</button></TD></TR>
                                <?php }}?>
                            <TR><TD><?=$lang[2652]?></TD>
                                <TD class="text-end"><button type="button" class="btn btn-primary btn-sm" onclick="procedurerefresh(event);">OK</button></TD></TR>

                            <?php if($gtab["checksum"]){?>
                            <TR><TD><?=$lang[3156]?> <?=$lang[1038]?></TD>
                                <TD class="text-end"><button type="button" class="btn btn-primary btn-sm" onclick="hashrefresh(event);">OK</button></TD></TR>
                            <?php } ?>

                            <TR class="table-section"><TD colspan="3"><?=$lang[2484]?></TD></TR>

                            <TR><TD><?=$lang[1056]?></TD>
                                <TD class="text-end"><button type="button" class="btn btn-primary btn-sm" onclick="lrefresh();">OK</button></TD></TR>

                            <TR><TD><?=$lang[1054]?></TD>
                                <TD class="text-end"><button type="button" class="btn btn-primary btn-sm" onclick="gurefresh('1');">OK</button></TD></TR>

                            <?php if($gtabs["tabledesc"]){?>
                                <TR><TD><?=$lang[2551]?></TD>
                                    <TD class="text-end">
                                        <select id="drules_reset" class="form-select form-select-sm"><option></option>
                                            <?php
                                            foreach ($gtabs["tabledesc"] as $key => $val){
                                                echo "<option value=\"$key\">".$lang[$val]."</option>";
                                            }
                                            ?>
                                        </select>
                                        <button class="btn btn-danger btn-sm" onclick="drulesReset(event);">OK</button></TD></TR>

                                <?php if($gtabs["specific_userrules"]){?>
                                    <TR><TD><?=$lang[2552]?></TD>
                                        <TD class="text-end">
                                            <select id="specific_userrules"><option></option>
                                                <?php
                                                foreach ($gtabs["tabledesc"] as $key => $val){
                                                    if($gtabs["specific_userrules"][$key]){
                                                        echo "<option value=\"$key\">".$lang[$val]."</option>";
                                                    }
                                                }
                                                ?>
                                            </select>
                                            <button class="btn btn-danger btn-sm" onclick="specificUserrules(event);">OK</button></TD></TR>
                                <?php }}?>

                            <TR class="table-section"><TD colspan="3"><?=$lang[2485]?></TD></TR>

                            <TR><TD><?=$lang[1849]?></TD>
                                <TD class="text-end"><button type="button" class="btn btn-primary btn-sm" onclick="filestructure(event);">OK</button></TD></TR>

                            <TR><TD><?=$lang[2367]?></TD>
                                <TD class="text-end"><button type="button" class="btn btn-primary btn-sm" onclick="deleteuserfilesave(event);">OK</button></TD></TR>

                            <?php /*
<TR><TD><?=$lang[2343]?></TD>
<TD class="text-end"><button type="button" class="btn btn-primary btn-sm" onclick="snaprefresh(event);">OK</button></TD></TR>
*/?>

                            <TR><TD><?=$lang[1057]?></TD>
                                <TD class="text-end"><button type="button" class="btn btn-primary btn-sm" onclick="sessiondel(event);">OK</button></TD></TR>


                            <TR><TD colspan="3"><HR></TD></TR>

                            <TR><TD><?=$lang[1420]?></TD>
                                <TD class="text-end"><INPUT TYPE="CHECKBOX" NAME="syslock" VALUE="1" OnClick="this.form.locking.value=1;this.form.submit();" STYLE="border:none;background-color:transparent;" <?=$user_lock?>></TD></TR>

                            <TR><TD><?=$lang[2368]?></TD><TD class="text-end"><TEXTAREA NAME="lockmessage" OnChange="this.form.submit();" STYLE="width:250px;height:50px;border:1px solid black;"><?=$lock_message?></TEXTAREA></TD></TR>


                            <TR class="table-section"><TD class="text-danger fw-bold" colspan="3"><?=$lang[2483]?></TD></TR>

                            <TR><TD><?=$lang[1992]?></TD>
                                <TD class="text-end"><button type="button" class="btn btn-danger btn-sm" onclick="filetabs(event);">OK</button></TD></TR>

                            <TR><TD><?=$lang[1593]?></TD>
                                <TD class="text-end"><button type="button" class="btn btn-danger btn-sm" onclick="newsystem(event);">RESET</button></TD></TR>

                        </table>

                    </FORM>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="refresh-frame" tabindex="-1" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="refresh-frame-title">Modal title</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="refresh-frame-body">
                
            </div>
        </div>
    </div>
</div>

