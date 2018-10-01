<?php
/*
 * Copyright notice
 * (c) 1998-2018 Limbas GmbH (support@limbas.org)
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
 * ID: 204
 */

require_once("inc/include_db.lib");
require_once("lib/db/db_".$DBA["DB"]."_admin.lib");
require_once("lib/include.lib");
require_once("lib/session.lib");


# EXTENSIONS
if($gLmbExt["ext_ajax_admin.inc"]){
	foreach ($gLmbExt["ext_ajax_admin.inc"] as $key => $extfile){
		require_once($extfile);
	}
}

function dyns_fileGroupRules($para){
	global $db;
	global $groupdat;
	
	require_once("lib/context.lib");

	if(is_numeric($para["level"])){
		# --- Rechte lesen ------
		$sqlquery = "SELECT DISTINCT GROUP_ID,LMVIEW,LMADD,DEL,ADDF,EDIT,LMLOCK FROM LDMS_RULES WHERE FILE_ID = ".$para["level"]." AND LMVIEW = ".LMB_DBDEF_TRUE;
		$rs = odbc_exec($db,$sqlquery) or errorhandle(odbc_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
		if(!$rs){$commit = 1;}
		$filerules = null;
		$bzm = 1;
		
		echo "<DIV ID=\"fileGroupRules\" class=\"lmbContextMenu\" STYLE=\"position:absolute;z-index:999;\" OnClick=\"activ_menu = 1;\">";
		pop_closetop("fileGroupRules");
		pop_left();
		echo "<table cellpadding=0 cellspacing=0>";
		echo "<tr>
		<td align=\"left\"><i class=\"lmb-icon lmb-group\"></i></td>
		<td align=\"center\"><i class=\"lmb-icon lmb-eye\"></i></td>
		<td align=\"center\"><i class=\"lmb-icon lmb-create-file\"></i></td>
		<td align=\"center\"><i class=\"lmb-icon lmb-create-folder\"></i></td>
		<td align=\"center\"><i class=\"lmb-icon lmb-pencil\"></i></td>
		<td align=\"center\"><i class=\"lmb-icon lmb-trash\"></i></td>
		<td align=\"center\"><i class=\"lmb-icon lmb-lock-file\"></i></td>
		</tr>";
		while(odbc_fetch_row($rs, $bzm)) {
			echo "<tr><td><a OnClick=\"document.location.href='main_admin.php?action=setup_group_erg&ID=".odbc_result($rs, "GROUP_ID")."'\">".$groupdat["name"][odbc_result($rs, "GROUP_ID")]."</a></td>";
			echo "<td align=\"center\">";
			if(odbc_result($rs, "LMVIEW")){echo "&nbsp;<i class=\"lmb-icon lmb-check-alt\"></i>";}
			echo "</td><td align=\"center\">";
			if(odbc_result($rs, "LMADD")){echo "&nbsp;<i class=\"lmb-icon lmb-check-alt\"></i>";}
			echo "</td><td align=\"center\">";
			if(odbc_result($rs, "ADDF")){echo "&nbsp;<i class=\"lmb-icon lmb-check-alt\"></i>";}
			echo "</td><td align=\"center\">";
			if(odbc_result($rs, "EDIT")){echo "&nbsp;<i class=\"lmb-icon lmb-check-alt\"></i>";}
			echo "</td><td align=\"center\">";
			if(odbc_result($rs, "DEL")){echo "&nbsp;<i class=\"lmb-icon lmb-check-alt\"></i>";}
			echo "</td><td align=\"center\">";
			if(odbc_result($rs, "LMLOCK")){echo "&nbsp;<i class=\"lmb-icon lmb-check-alt\"></i>";}
			echo "</td></tr>";
			$bzm++;
		}
		echo "</table>";
		pop_right();
		pop_bottom();
		echo "</DIV>";
	}

}


/**
 * report tablefields
 *
 * @param unknown_type $para
 */
function dyns_reportTabFieldList($para){
	global $gtab;
	global $gfield;
	
	$gtabid = $para["gtabid"];
	$parentrel = $para["parentrel"];
	
	
	echo "<TABLE BORDER=\"0\" CELLPADDING=\"0\" CELLSPACING=\"0\">\n";
	
	$bzm = 1;
	foreach ($gfield[$gtabid]["sort"] as $key => $value){

		$parentrel2 = $gtabid.";".$gfield[$gtabid]["verkntabid"][$key].";".$key.";".$gfield[$gtabid]["data_type"][$key];
		
		if($bzm >= count($gfield[$gtabid]["sort"])){$outliner = "joinbottom";}else{$outliner = "join";}
		
		if($gfield[$gtabid]["field_type"][$key] >= 100){continue;}
		
		if($gfield[$gtabid]["field_type"][$key] == 11 AND $gfield[$gtabid]["verkntabid"][$key]){
		
			foreach ($gtab["raverkn"][$gfield[$gtabid]["verkntabid"][$key]] as $keyskn => $valueskn){
			
				$globid = rand(1,10000);
				
				echo "<TR><TD>\n";
				echo "<TABLE BORDER=\"0\" CELLPADDING=\"0\" CELLSPACING=\"0\"><TR><TD BACKGROUND=\"pic/outliner/line.gif\" VALIGN=\"top\"><IMG SRC=\"pic/outliner/".$outliner.".gif\" WIDTH=\"18\" HEIGHT=\"16\" ALIGN=\"TOP\" BORDER=\"0\"></TD><TD>\n";
					
				echo "<TABLE BORDER=\"0\" CELLPADDING=\"0\" CELLSPACING=\"0\">\n";
				echo "<TR><TD TITLE=\"table : ".$gtab["desc"][$valueskn]."\"><A HREF=\"Javascript:LmAdm_getFields(".$valueskn.",$globid,'".$parentrel."|".$parentrel2."')\"><IMG SRC=\"pic/outliner/plusonly.gif\" WIDTH=\"18\" HEIGHT=\"16\" ALIGN=\"TOP\" BORDER=\"0\" NAME=\"tab_".$globid."_plusminus\"><i class=\"lmb-icon lmb-folder-closed\" WIDTH=\"16\" HEIGHT=\"13\" ALIGN=\"TOP\" BORDER=\"0\" NAME=\"tab_".$globid."_box\"></i></A> <B title=\"".$gtab["table"][$keyskn]."\">".$gtab["desc"][$keyskn]."</B></TD></TR>\n";
				echo "<TR><TD ID=\"el_$globid\">";
								
				echo "</TR></TD>";
				echo "</TABLE>\n";

				echo "</TD></TR></TABLE>\n";
				echo "</TD></TR>\n";
			}

		}else{
			echo "<TR><TD><IMG SRC=\"pic/outliner/".$outliner.".gif\" WIDTH=\"18\" HEIGHT=\"16\" ALIGN=\"TOP\" BORDER=\"0\">&nbsp;<A onclick=\"add_dbfield(this,event);\" title=\"".$gfield[$gtabid]["field_name"][$key]."\" lmfieldid=\"$key\" lmgtabid=\"$gtabid\" lmparentrel=\"$parentrel\" lmdatatype=\"".$gfield[$gtabid]["data_type"][$key]."\">".$gfield[$gtabid]["spelling"][$key]."</A></TD></TR>\n";
		}
		
		$bzm++;
	}
	
	echo "</TABLE>\n";
}


/**
 * form tablefields
 *
 * @param unknown_type $para
 */
function dyns_formTabFieldList($para){
	global $gtab;
	global $gfield;
	
	$gtabid = $para["gtabid"];
	$parent_tab = $para["parent_tab"];
	$parent_field = $para["parent_field"];
	$parentrel = $gfield[$parent_tab]["md5tab"][$parent_field];

	echo "<TABLE BORDER=\"0\" CELLPADDING=\"0\" CELLSPACING=\"0\">\n";
	
	$bzm = 1;
	foreach ($gfield[$gtabid]["sort"] as $key => $value){

		$parentrel2 = $gfield[$gtabid]["md5tab"][$key];
		
		if($bzm >= count($gfield[$gtabid]["sort"])){$outliner = "joinbottom";}else{$outliner = "join";}

		if($gfield[$gtabid]["field_type"][$key] >= 100){$bzm++;continue;}
		
		if($gfield[$gtabid]["field_type"][$key] == 11 AND $gfield[$gtabid]["verkntabid"][$key]){
		
			foreach ($gtab["raverkn"][$gfield[$gtabid]["verkntabid"][$key]] as $keyskn => $valueskn){
			
				$globid = rand(1,10000);
				
				echo "<TR><TD>\n";
				echo "<TABLE BORDER=\"0\" CELLPADDING=\"0\" CELLSPACING=\"0\"><TR><TD BACKGROUND=\"pic/outliner/line.gif\" VALIGN=\"top\"><IMG SRC=\"pic/outliner/".$outliner.".gif\" WIDTH=\"18\" HEIGHT=\"16\" ALIGN=\"TOP\" BORDER=\"0\"></TD><TD>\n";

				echo "<TABLE BORDER=\"0\" CELLPADDING=\"0\" CELLSPACING=\"0\">\n";
				echo "<TR><TD TITLE=\"table : ".$gtab["desc"][$valueskn]."\"><A HREF=\"Javascript:LmAdm_getFields(".$valueskn.",$globid,$gtabid,$key)\"><IMG SRC=\"pic/outliner/plusonly.gif\" WIDTH=\"18\" HEIGHT=\"16\" ALIGN=\"TOP\" BORDER=\"0\" NAME=\"tab_".$globid."_plusminus\"><i class=\"lmb-icon lmb-folder-closed\" WIDTH=\"16\" HEIGHT=\"13\" ALIGN=\"TOP\" BORDER=\"0\" NAME=\"tab_".$globid."_box\"></i></A> <B title=\"".$gtab["table"][$keyskn]."\">".$gtab["desc"][$keyskn]."</B></TD></TR>\n";
				echo "<TR><TD ID=\"el_$globid\">";
								
				echo "</TR></TD>";
				echo "</TABLE>\n";

				echo "</TD></TR></TABLE>\n";
				echo "</TD></TR>\n";
			}
		}

		echo "<TR><TD><IMG SRC=\"pic/outliner/".$outliner.".gif\" WIDTH=\"18\" HEIGHT=\"16\" ALIGN=\"TOP\" BORDER=\"0\">";
		if($gfield[$gtabid]["field_type"][$key] == 11 AND $gfield[$gtabid]["verkntabid"][$key]){echo "<IMG SRC=\"pic/outliner/hline.gif\" WIDTH=\"18\" HEIGHT=\"16\" ALIGN=\"TOP\" BORDER=\"0\">";}
		echo "&nbsp;<A onclick=\"add_dbfield(this,event);\" title=\"".$gfield[$gtabid]["field_name"][$key]."\" lmfieldid=\"$key\" lmgtabid=\"$gtabid\" lmspelling=\"".$gfield[$gtabid]["field_name"][$key]."\" lmparentrel=\"$parentrel\" lmstabgroup=\"".$gtab["tab_group"][$gtabid]."\">".$gfield[$gtabid]["spelling"][$key]."</A>";
		echo "</TD></TR>\n";

		$bzm++;
	}
        
    /* --- Fieldlist for extended relation params ------ */
	$bzm = 1;
    if ($r_verkntabid = $gfield[$gtabid]["r_verkntabid"]) {
        foreach ($r_verkntabid as $key_ => $gtabid_) {
            $r_verknfieldid = $gfield[$gtabid]['r_verknfieldid'][$key_];
            if($verknparams = $gfield[$gtabid_]["verknparams"][$r_verknfieldid]){
                
                $globid = rand(1, 10000);
                
                echo "<TR><TD>\n";
                echo "<TABLE BORDER=\"0\" CELLPADDING=\"0\" CELLSPACING=\"0\"><TR><TD BACKGROUND=\"pic/outliner/line.gif\" VALIGN=\"top\"><IMG SRC=\"pic/outliner/join.gif\" WIDTH=\"18\" HEIGHT=\"16\" ALIGN=\"TOP\" BORDER=\"0\"></TD><TD>\n";
                
                echo "<TABLE BORDER=\"0\" CELLPADDING=\"0\" CELLSPACING=\"0\">\n";
                echo "<TR><TD TITLE=\"table : " . $gtab["desc"][$gtabid_] . "\"><A HREF=\"Javascript:LmAdm_getFields(" . $verknparams . ",$globid,$gtabid_,$r_verknfieldid)\"><IMG SRC=\"pic/outliner/plusonly.gif\" WIDTH=\"18\" HEIGHT=\"16\" ALIGN=\"TOP\" BORDER=\"0\" NAME=\"tab_" . $globid . "_plusminus\"><i class=\"lmb-icon lmb-folder-closed\" WIDTH=\"16\" HEIGHT=\"13\" ALIGN=\"TOP\" BORDER=\"0\" NAME=\"tab_" . $globid . "_box\"></i></A> <B title=\"field: ".$gfield[$gtabid_]["spelling"][$r_verknfieldid]."\"><i>".$gtab["desc"][$verknparams]."</i></B></TD></TR>\n";
                echo "<TR><TD ID=\"el_$globid\">";
                
                echo "</TR></TD>";
                echo "</TABLE>\n";
                
                echo "</TD></TR></TABLE>\n";
                echo "</TD></TR>\n";
                $bzm++;
            }
        }
    }
        
        
	/* --- Fieldlist for extended relation params ------*/
    /*
    if($gfield[$parent_tab]["verknparams"][$parent_field]){
        $vgtabid_ = $gfield[$parent_tab]["verknparams"][$parent_field];
        $bzm_ = 1;
        foreach ($gfield[$vgtabid_]["sort"] as $key_ => $value) {
            
            if($bzm_ >= count($gfield[$vgtabid_]["sort"])){$outliner_ = "joinbottom";}else{$outliner_ = "join";}

            if ($gfield[$vgtabid_]["field_type"][$key_] >= 100) {continue;}
            echo "<TR><TD><IMG SRC=\"pic/outliner/$outliner_.gif\" WIDTH=\"18\" HEIGHT=\"16\" ALIGN=\"TOP\" BORDER=\"0\">&nbsp;";
            echo "<A onclick=\"add_dbfield(this,event);\" title=\"" . $gfield[$vgtabid_]["field_name"][$key_] . "\" lmfieldid=\"$key_\" lmgtabid=\"$vgtabid_\" lmspelling=\"" . $gfield[$vgtabid_]["field_name"][$key_] . "\" lmparentrel=\"$parentrel\" lmstabgroup=\"" . $gtab["tab_group"][$vgtabid_] . "\"><i>" . $gfield[$vgtabid_]["spelling"][$key_] . "</i></A>";
            echo "</TD></TR>\n";
            
            $bzm_++;
            // echo "<span class=\"lmbContextItem$class\" style=\"color:$color;cursor:pointer\" OnClick=\"LmExt_RelationFields(this,'$gtabid','$fieldid','$key','$edittyp','$ID','','','','','$gformid','$formid');\">".$gfield[$vgtabid_]["spelling"][$key]."</span>";
        }
    }
	*/
	
	echo "</TABLE>\n";
}


/**
 * tabschema
 *
 * @$par array fieldid
 */
function dyns_tabschemaInfos($par){
	if($par['act'] == 1){
		require_once("admin/tables/tabschema_dyn.php");
		show_fieldinfo($par["par1"],$par["par2"]);}
	elseif($par['act'] == 2){
		require_once("admin/tables/tabschema_dyn.php");
		show_linkinfo($par["par1"],$par["par2"],$par["par3"]);}
	elseif($par['act'] == 3){
		require_once("admin/tables/view.dao");
		show_viewlinkinfo($par["par1"],$par["par2"],$par["par3"],$par["par4"]);
	}
}


/**
 * vieweditor
 *
 * @$par array fieldid
 */
function dyns_VieweditorPattern($par){
	if(!$viewid = $par["viewid"]){return false;};
	global $db;
	global $session;
	global $DBA;
	global $farbschema;

	require_once("admin/tables/view.dao");
	
	$gview = lmb_getQuestValue($viewid);
	
	$setdrag = $par["setdrag"];
	if($par["setrelation"]){
		$gview["relationstring"] = lmb_createQuestRelation($viewid,$gview["relationstring"],$par["setrelation"],$par["settype"]);
	}

	require_once("admin/tables/viewschema.php");
}

/**
 * vieweditor
 *
 * @$par array fieldid
 */
function dyns_VieweditorFields($par){
	if(!$viewid = $par["viewid"]){return false;};
	global $db;
	global $session;
	global $farbschema;

	require_once("admin/tables/view.dao");
	
	# decode params
	if($par){
		foreach ($par as $key => $val){
			$paru[$key] = lmb_utf8_decode($val);	
		}
	}
	
	if(isset($par["setviewfield"])){
		lmb_editQuestFields($viewid,$paru);
	}
	
	show_viewFields($viewid);
}


/**
 * edit table field
 *
 * @$par array fieldid
 */
function dyns_editTableField($par){
	global $umgvar;
	global $session;
	global $lang;
	global $farbschema;
	global $lmcurrency;
	global $DBA;
	global $db;
	global $gtab;
	global $gfield;
	
	$par = lmb_arrayDecode($par,1);
	if (!$par['val']) {
	    $par['val'] = $_REQUEST['val'];
    }
	
	require_once("lib/db/db_".$DBA["DB"]."_admin.lib");
	require_once("lib/include_admin.lib");
	require_once("admin/tables/tab.lib");
	require_once("admin/setup/language.lib");
	require_once("admin/tables/gtab_ftype_detail.php");
}

/**
 * edit table
 *
 * @$par array fieldid
 */
function dyns_editTable($par){
	global $umgvar;
	global $session;
	global $lang;
	global $farbschema;
	global $DBA;
	global $db;
	global $gtab;
	global $gfield;
	global $gtrigger;
	global $db;
	
	$par = lmb_arrayDecode($par,1);
	
	$tabgroup = $par["tabgroup"];
	$tabid = $par["tabid"];
	$act = $par["act"];
	$val = $par["val"];
	$param2 = $par["param2"];
	if($act){
		${$act} = $val;
	}
	require_once("lib/db/db_".$DBA["DB"]."_admin.lib");
	require_once("lib/include_admin.lib");
	require_once("admin/tables/tab.lib");
	require_once("admin/setup/language.lib");
	require_once("admin/tables/tab.dao");
	require_once("admin/tables/tab_detail.php");
}

/**
 * edit tabletree element
 *
 * @$par array element-id
 */
function dyns_editTableTree($par){
	global $lang;
	global $farbschema;
	global $db;
	
	require_once("admin/tabletrees/tabletree.lib");
	
	show_tabletreeSettings($par);
}

/**
 * pchart
 *
 * @param unknown_type $par
 */
function dyns_saveDiagDetail($par){
	require_once("admin/diagram/diag_detail_ajax.php");
	
	$diag_id = $par['diag_id'];
	$diag_tab_id = $par['diag_tab_id'];
	$field_id = $par['field_id'];
	$show = $par['show']=="true";
	$axis = ($par['axis']) == null ? 0 : $par['axis'];
	$color = $par['color'];
        
	lmb_updateData($diag_id,$field_id,$show,$axis,$color);
}

function dyns_createDiagPChart($par){
	require_once("extra/diagram/diagram.php");
	if($diag = lmb_createDiagram($par['diag_id'])){
	   echo $diag;
	}
}

function dyns_saveDiagSettings($par){
	require_once("admin/diagram/diag_detail_ajax.php");
	lmb_saveCustomizationSettings($par);
}


/**
 * extension manager
 *
 * @param unknown_type $par
 */
function dyns_extensionEditor($par){
	require_once("admin/tools/extension_ajax.php");
	readExtDirPeter($par['path'],$par['open']);
}

/**
 * extension manager
 *
 * @param unknown_type $par
 */
function dyns_extensionEditorUpload($par){
	$fileSrc = $par['src'];
	$fileDest = $par['dest'];
	$todo = $par['todo'];
	require_once("admin/tools/extension_ajax.php");
}

# Buffer
ob_start();


# --- Funktions-Aufruf -----------
if($actid AND function_exists("dyns_".$actid)){
    if ($_REQUEST){
        unset($params);
        foreach ($_REQUEST as $keyR => $valR) {
            if ($keyR and $valR) {
                if (is_string($valR)) {
                    $_REQUEST[$keyR] = lmb_utf8_decode($valR);
                } elseif (is_array($valR)) {
                    $_REQUEST[$keyR] = lmb_arrayDecode($valR, 1);
                }
                $params[$keyR] = $_REQUEST[$keyR];
            }
        }
    }
	$functionName = "dyns_".$actid;
	$functionName($params);
	
}else{
	echo "function not available";
}

# --- Fehlermeldungen -----
if(is_array($alert)){
	if($alert AND !is_array($alert)){$alert = array($alert);}
	$alert = array_unique($alert);
	echo "<script language=\"JavaScript\">\n showAlert('".implode("\\n",$alert)."');\n</script>\n";
}


# Puffersteuerung Ausgabe
if($output = ob_get_contents()){

	#if(!$noencode){
	#	$output = lmb_utf8_encode($output);
	#}

	ob_end_clean();
	echo $output;
}



if ($db) {odbc_close($db);}

?>