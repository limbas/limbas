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


if(file_exists("update.lib")){require_once("update.lib");}
elseif(file_exists($umgvar["pfad"]."/admin/UPDATE/update.lib")){require_once($umgvar["pfad"]."/admin/UPDATE/update.lib");}


# umgvar: add password_hash
$nid = next_db_id('lmb_umgvar', 'ID');
$sqlquery = "INSERT INTO LMB_UMGVAR VALUES($nid,1,'password_hash','1','Password hashing algorithm (see PHP function password_hash)',1900)";
patch_db(1,6,$sqlquery,"update umgvar: add password_hash",3);

# password -> varchar(255)
$sqlquery = dbq_15(array($DBA['SCHEMA'],'LMB_USERDB','PASSWORT','VARCHAR(255)'));
patch_db(2,6,$sqlquery,'increased length of password field to contain secure hashes',3);

# dms cloud
$sqlquery = 'ALTER TABLE LDMS_STRUCTURE ADD STORAGE_ID ' . LMB_DBTYPE_SMALLINT;
patch_db(3,6,$sqlquery,'adding default storage_id for folders',3);

function patch_4() {
    global $db;

    $sqlquery = "SELECT TAB_ID FROM LMB_CONF_TABLES WHERE UPPER(TABELLE) = 'LDMS_FILES'";
    $rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
    $tid = lmbdb_result($rs,'TAB_ID');
    if (!$tid) {
        return false;
    }

    require_once('admin/setup/language.lib');
    require_once('admin/tables/tab.lib');
    $nfield = array();
    $nfield[] = array('field' => 'storage_id','typ' => 2,'typ2' => 0,'size' => 0,'description' => 'storageID','spellingf' => 'storageID','default' => '','sort' => '');
    add_extended_fields($nfield, $tid, 1);

    return true;
}


$sqlquery = 'ALTER TABLE LDMS_FILES ADD DOWNLOAD_LINK ' . LMB_DBTYPE_VARCHAR . '(255)';
patch_db(5,6,$sqlquery,'adding public download link for files',3);

$sqlquery = dbq_15(array($DBA['SCHEMA'],'LDMS_FILES','SECNAME','VARCHAR(128)'));
patch_db(6,6,$sqlquery,'resize files secname for external storage',3);

$sqlquery = 'CREATE TABLE LMB_EXTERNAL_STORAGE (
    ID ' . LMB_DBTYPE_SMALLINT . ' ' . LMB_DBFUNC_PRIMARY_KEY . ',
    DESCR ' . LMB_DBTYPE_VARCHAR . '(255),
    CLASSNAME ' . LMB_DBTYPE_VARCHAR . '(50),
    CONFIG ' . LMB_DBTYPE_LONG . ',
    EXTERNALACCESSURL ' . LMB_DBTYPE_VARCHAR . '(255),
    PUBLICCLOUD ' . LMB_DBTYPE_BOOLEAN . '
)';
patch_db(7,6,$sqlquery,'adding external storage configuration',3);

$sqlquery = 'CREATE TABLE LMB_AUTH_TOKEN (
    TOKEN ' . LMB_DBTYPE_VARCHAR . '(255) ' . LMB_DBFUNC_PRIMARY_KEY . ',
    USER_ID ' . LMB_DBTYPE_INTEGER . ',
    LIFESPAN ' . LMB_DBTYPE_SMALLINT . ',
    EXPIRESTAMP ' . LMB_DBTYPE_TIMESTAMP . '
)';
patch_db(8,6,$sqlquery,'adding auth token access',3);

$sqlquery = "update lmb_conf_tables set keyfield = '' WHERE  TYP = 5";
patch_db(9,6,$sqlquery,'resize files secname for external storage',3);


$sqlquery = "alter table lmb_reports add tab_el smallint";
patch_db(10,6,$sqlquery,"formular subtables",3);


function patch_11(){
	global $db;

	$def_style = array();
	$def_style = array_pad($def_style,44,'');


	$NEXTID = next_db_id("LMB_REPORTS");

	$sqlquery = "SELECT ID,BERICHT_ID,TAB,TAB_SIZE FROM LMB_REPORTS WHERE TYP = 'tab' ORDER BY BERICHT_ID,TAB";
	$rs = lmbdb_exec($db,$sqlquery);

	while(lmbdb_fetch_row($rs)){

	    if($report_id != lmbdb_result($rs,"BERICHT_ID")){
	        $sqlquery1 = "SELECT MAX(EL_ID) AS NEXTELID FROM LMB_REPORTS WHERE BERICHT_ID = ".lmbdb_result($rs,"BERICHT_ID");
            $rs1 = lmbdb_exec($db,$sqlquery1);
            $NEXTELID = lmbdb_result($rs1,"NEXTELID") + 1;
        }

	    $id = lmbdb_result($rs,"ID");
		$tabid = lmbdb_result($rs,"TAB");
		$report_id = lmbdb_result($rs,"BERICHT_ID");
		$tab_size = explode(";",lmbdb_result($rs,"TAB_SIZE"));

		$extvalue = array();

		// row
		for($row=1;$row<=$tab_size[1];$row++){
            for($col=1;$col<=$tab_size[0];$col++){

                $sqlquery1 = "SELECT TAB_EL_COL_SIZE FROM LMB_REPORTS WHERE BERICHT_ID = $report_id AND TAB = $tabid AND TYP = 'tabhead' AND TAB_EL_COL = $col";
                $rs1 = lmbdb_exec($db,$sqlquery1);
                $colsize = lmbdb_result($rs1,"TAB_EL_COL_SIZE");

                $sqlquery1 = "SELECT ID,STYLE FROM LMB_REPORTS WHERE BERICHT_ID = $report_id AND TAB = $tabid AND TYP != 'tabhead' AND TYP != 'tab' AND TYP != 'tabcell' AND TAB_EL_COL = $col AND TAB_EL_ROW = $row ORDER BY ID";
                $rs1 = lmbdb_exec($db,$sqlquery1);
                $elstyle = lmbdb_result($rs1,"STYLE");
                $elid = lmbdb_result($rs1,"ID");

                if($elid) {

                    // extract border settings to tablecell
                    $elstyle = explode(';', $elstyle);
                    $cell_style = $def_style;

                    // border
                    for ($i = 13; $i <= 24; $i++) {
                        if($elstyle[$i]) {
                            $cell_style[$i] = $elstyle[$i];
                            $elstyle[$i] = '';
                        }
                    }

                    // colspan / rowspan
                    $cell_style[37] = $elstyle[37];
                    $elstyle[37] = '';
                    $cell_style[38] = $elstyle[38];
                    $elstyle[38] = '';

                    $cell_style = implode(';', $cell_style);
                    $elstyle = implode(';', $elstyle);

                    $sqlquery3 = "UPDATE LMB_REPORTS SET STYLE = '$elstyle' WHERE BERICHT_ID = $report_id AND ID = $elid";
                    $rs3 = lmbdb_exec($db,$sqlquery3);
                }


                $sqlquery3 = "INSERT INTO LMB_REPORTS (ID,EL_ID,ERSTUSER,BERICHT_ID,TYP,TAB_EL,TAB_EL_ROW,TAB_EL_COL,TAB_EL_COL_SIZE,STYLE) VALUES ($NEXTID,$NEXTELID,1,$report_id,'tabcell'," . parse_db_int($tabid) . "," . parse_db_int($row) . "," . parse_db_int($col) . "," . parse_db_int($colsize) . ",'$cell_style')";
                $rs3 = lmbdb_exec($db, $sqlquery3);
                $NEXTELID++;
                $NEXTID++;


				$extvalue[$col] = "$colsize";
            }
        }

        if($extvalue){
		    $sqlquery3 = "UPDATE LMB_REPORTS SET EXTVALUE = '".implode(';',$extvalue)."', TAB_EL = 0, STYLE='' WHERE BERICHT_ID = $report_id AND ID = $id";
			$rs3 = lmbdb_exec($db,$sqlquery3);
        }

	}

    $sqlquery3 = "UPDATE LMB_REPORTS SET TAB_EL = TAB WHERE TYP != 'tab' AND TAB > 0";
	$rs3 = lmbdb_exec($db,$sqlquery3);
    $sqlquery3 = "UPDATE LMB_REPORTS SET TAB = 0 WHERE TYP != 'tab'";
	$rs3 = lmbdb_exec($db,$sqlquery3);
    $sqlquery3 = "DELETE FROM LMB_REPORTS WHERE TYP = 'tabhead'";
	$rs3 = lmbdb_exec($db,$sqlquery3);


	mkdir('inc/fonts/tcpdf');
	if(!file_exists('inc/fonts/tcpdf')){
	    echo "<div style=\"color:red\">could not create directory <b>'inc/fonts/tcpdf'</b>! you need to create it manualy</div>";
    }

	return true;
}
patch_scr(11,6,"patch_11","report tables",3);

$sqlquery = "CREATE TABLE LMB_CUSTMENU (ID ".LMB_DBTYPE_SMALLINT." NOT NULL, PARENT ".LMB_DBTYPE_SMALLINT.", NAME ".LMB_DBTYPE_SMALLINT.", TITLE ".LMB_DBTYPE_SMALLINT.", LINKID ".LMB_DBTYPE_SMALLINT.", TYP ".LMB_DBTYPE_SMALLINT.", URL VARCHAR(150), ICON VARCHAR(60), BG VARCHAR(7), SORT ".LMB_DBTYPE_SMALLINT.", PRIMARY KEY (ID))";
patch_db(12,6,$sqlquery,"adding table LMB_SQL_FAVORITES",3);

echo "--><br>";
#lmb_rebuildSequences();

#$impsystables = array("lmb_lang.tar.gz","lmb_action.tar.gz","lmb_field_types.tar.gz");


echo "<h5><i class=\"lmbMenuItemImage lmb-icon lmb-exclamation-triangle\"></i>new menu functions added! To use it you have to refresh the menu rights! <a onclick=\"nu = open('main_admin.php?action=setup_linkref', 'refresh', 'toolbar=0,location=0,status=1,menubar=0,scrollbars=1,resizable=1,width=550,height=400');\">refresh</a></h5>";
echo "<h5><i class=\"lmbMenuItemImage lmb-icon lmb-exclamation-triangle\"></i>new system tablefields added! To use it you have to refresh the table rights! <a onclick=\"nu = open('main_admin.php?action=setup_grusrref&check_all=1','refresh','toolbar=0,location=0,status=1,menubar=0,scrollbars=1,resizable=1,width=550,height=400');\">refresh</a></h5>";





#$require1 = "admin/group/group.lib";
#$require2 = "admin/tools/linkref.php";


// lmb_action -> srefresh(this);

?>
