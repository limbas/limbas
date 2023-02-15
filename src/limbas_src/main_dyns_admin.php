<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */



require_once(__DIR__ . '/lib/session.lib');
require_once(COREPATH . 'lib/db/db_'.$DBA['DB'].'_admin.lib');
require_once(COREPATH . 'extra/lmbObject/log/LimbasLogger.php');

# Update functions
require_once(COREPATH . 'admin/tools/update/update_dyns.php');

# Report admin extension
require_once(COREPATH . 'admin/report/ext_ajax.inc');

# include extensions
if($gLmbExt["ext_ajax_admin.inc"]){
	foreach ($gLmbExt["ext_ajax_admin.inc"] as $key => $extfile){
		require_once($extfile);
	}
}

function dyns_fileGroupRules($para){
	global $db;
	global $groupdat;
	
	require_once(COREPATH . 'lib/context.lib');

	if(is_numeric($para["level"])){
		# --- Rechte lesen ------
		$sqlquery = "SELECT DISTINCT GROUP_ID,LMVIEW,LMADD,DEL,ADDF,EDIT,LMLOCK FROM LDMS_RULES WHERE FILE_ID = ".$para["level"]." AND LMVIEW = ".LMB_DBDEF_TRUE;
		$rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
		if(!$rs){$commit = 1;}
		$filerules = null;
		
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
		while(lmbdb_fetch_row($rs)) {
			echo "<tr><td><a OnClick=\"document.location.href='main_admin.php?action=setup_group_erg&ID=".lmbdb_result($rs, "GROUP_ID")."'\">".$groupdat["name"][lmbdb_result($rs, "GROUP_ID")]."</a></td>";
			echo "<td align=\"center\">";
			if(lmbdb_result($rs, "LMVIEW")){echo "&nbsp;<i class=\"lmb-icon lmb-check-alt\"></i>";}
			echo "</td><td align=\"center\">";
			if(lmbdb_result($rs, "LMADD")){echo "&nbsp;<i class=\"lmb-icon lmb-check-alt\"></i>";}
			echo "</td><td align=\"center\">";
			if(lmbdb_result($rs, "ADDF")){echo "&nbsp;<i class=\"lmb-icon lmb-check-alt\"></i>";}
			echo "</td><td align=\"center\">";
			if(lmbdb_result($rs, "EDIT")){echo "&nbsp;<i class=\"lmb-icon lmb-check-alt\"></i>";}
			echo "</td><td align=\"center\">";
			if(lmbdb_result($rs, "DEL")){echo "&nbsp;<i class=\"lmb-icon lmb-check-alt\"></i>";}
			echo "</td><td align=\"center\">";
			if(lmbdb_result($rs, "LMLOCK")){echo "&nbsp;<i class=\"lmb-icon lmb-check-alt\"></i>";}
			echo "</td></tr>";
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
		
		if($bzm >= lmb_count($gfield[$gtabid]["sort"])){$outliner = "joinbottom";}else{$outliner = "join";}
		
		if($gfield[$gtabid]["field_type"][$key] >= 100){continue;}
		
		if($gfield[$gtabid]["field_type"][$key] == 11 AND $gfield[$gtabid]["verkntabid"][$key]){
		
			foreach ($gtab["raverkn"][$gfield[$gtabid]["verkntabid"][$key]] as $keyskn => $valueskn){
			
				$globid = rand(1,10000);
				
				echo "<TR><TD>\n";
				echo "<TABLE BORDER=\"0\" CELLPADDING=\"0\" CELLSPACING=\"0\"><TR><TD BACKGROUND=\"assets/images/legacy/outliner/line.gif\" VALIGN=\"top\"><IMG SRC=\"assets/images/legacy/outliner/".$outliner.".gif\" WIDTH=\"18\" HEIGHT=\"16\" ALIGN=\"TOP\" BORDER=\"0\"></TD><TD>\n";
					
				echo "<TABLE BORDER=\"0\" CELLPADDING=\"0\" CELLSPACING=\"0\">\n";
				echo "<TR><TD TITLE=\"table : ".$gtab["desc"][$valueskn]."\"><A HREF=\"Javascript:LmAdm_getFields(".$valueskn.",$globid,'".$parentrel."|".$parentrel2."')\"><IMG SRC=\"assets/images/legacy/outliner/plusonly.gif\" WIDTH=\"18\" HEIGHT=\"16\" ALIGN=\"TOP\" BORDER=\"0\" NAME=\"tab_".$globid."_plusminus\"><i class=\"lmb-icon lmb-folder-closed\" WIDTH=\"16\" HEIGHT=\"13\" ALIGN=\"TOP\" BORDER=\"0\" NAME=\"tab_".$globid."_box\"></i></A> <B title=\"".$gtab["table"][$keyskn]."\">".$gtab["desc"][$keyskn]."</B></TD></TR>\n";
				echo "<TR><TD ID=\"el_$globid\">";
								
				echo "</TR></TD>";
				echo "</TABLE>\n";

				echo "</TD></TR></TABLE>\n";
				echo "</TD></TR>\n";
			}

		}else{
			echo "<TR><TD><IMG SRC=\"assets/images/legacy/outliner/".$outliner.".gif\" WIDTH=\"18\" HEIGHT=\"16\" ALIGN=\"TOP\" BORDER=\"0\">&nbsp;<A onclick=\"add_dbfield(this,event);\" title=\"".$gfield[$gtabid]["field_name"][$key]."\" lmfieldid=\"$key\" lmgtabid=\"$gtabid\" lmparentrel=\"$parentrel\" lmdatatype=\"".$gfield[$gtabid]["data_type"][$key]."\">".$gfield[$gtabid]["spelling"][$key]."</A></TD></TR>\n";
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
	$parentrelpath = $para["parentrelpath"];


	echo "<TABLE BORDER=\"0\" CELLPADDING=\"0\" CELLSPACING=\"0\">\n";
	
	$bzm = 1;
	foreach ($gfield[$gtabid]["sort"] as $key => $value){

		$parentrel2 = $gfield[$gtabid]["md5tab"][$key];
		$parentrelpath2 = $gtabid.",".$gfield[$gtabid]["verkntabid"][$key].",".$key;
		
		if($bzm >= lmb_count($gfield[$gtabid]["sort"])){$outliner = "joinbottom";}else{$outliner = "join";}

		if($gfield[$gtabid]["field_type"][$key] >= 100){$bzm++;continue;}
		
		if($gfield[$gtabid]["field_type"][$key] == 11 AND $gfield[$gtabid]["verkntabid"][$key]){
		
			foreach ($gtab["raverkn"][$gfield[$gtabid]["verkntabid"][$key]] as $keyskn => $valueskn){
			
				$globid = rand(1,10000);
				
				echo "<TR><TD>\n";
				echo "<TABLE BORDER=\"0\" CELLPADDING=\"0\" CELLSPACING=\"0\"><TR><TD BACKGROUND=\"assets/images/legacy/outliner/line.gif\" VALIGN=\"top\"><IMG SRC=\"assets/images/legacy/outliner/".$outliner.".gif\" WIDTH=\"18\" HEIGHT=\"16\" ALIGN=\"TOP\" BORDER=\"0\"></TD><TD>\n";

				echo "<TABLE BORDER=\"0\" CELLPADDING=\"0\" CELLSPACING=\"0\">\n";
				echo "<TR><TD TITLE=\"table : ".$gtab["desc"][$valueskn]."\"><A HREF=\"Javascript:LmAdm_getFields(".$valueskn.",$globid,$gtabid,$key,'".$parentrelpath."|".$parentrelpath2."')\"><IMG SRC=\"assets/images/legacy/outliner/plusonly.gif\" WIDTH=\"18\" HEIGHT=\"16\" ALIGN=\"TOP\" BORDER=\"0\" NAME=\"tab_".$globid."_plusminus\"><i class=\"lmb-icon lmb-folder-closed\" WIDTH=\"16\" HEIGHT=\"13\" ALIGN=\"TOP\" BORDER=\"0\" NAME=\"tab_".$globid."_box\"></i></A> <B title=\"".$gtab["table"][$keyskn]."\">".$gtab["desc"][$keyskn]."</B></TD></TR>\n";
				echo "<TR><TD ID=\"el_$globid\">";
								
				echo "</TR></TD>";
				echo "</TABLE>\n";

				echo "</TD></TR></TABLE>\n";
				echo "</TD></TR>\n";
			}
		}

		echo "<TR><TD><IMG SRC=\"assets/images/legacy/outliner/".$outliner.".gif\" WIDTH=\"18\" HEIGHT=\"16\" ALIGN=\"TOP\" BORDER=\"0\">";
		if($gfield[$gtabid]["field_type"][$key] == 11 AND $gfield[$gtabid]["verkntabid"][$key]){echo "<IMG SRC=\"assets/images/legacy/outliner/hline.gif\" WIDTH=\"18\" HEIGHT=\"16\" ALIGN=\"TOP\" BORDER=\"0\">";}
		echo "&nbsp;<A onclick=\"add_dbfield(this,event);\" title=\"".$gfield[$gtabid]["field_name"][$key]."\" lmfieldid=\"$key\" lmgtabid=\"$gtabid\" lmspelling=\"".$gfield[$gtabid]["field_name"][$key]."\" lmparentrel=\"$parentrel\" lmparentrelpath=\"$parentrelpath\" lmstabgroup=\"".$gtab["tab_group"][$gtabid]."\">".$gfield[$gtabid]["spelling"][$key]."</A>";
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
                echo "<TABLE BORDER=\"0\" CELLPADDING=\"0\" CELLSPACING=\"0\"><TR><TD BACKGROUND=\"assets/images/legacy/outliner/line.gif\" VALIGN=\"top\"><IMG SRC=\"assets/images/legacy/outliner/join.gif\" WIDTH=\"18\" HEIGHT=\"16\" ALIGN=\"TOP\" BORDER=\"0\"></TD><TD>\n";
                
                echo "<TABLE BORDER=\"0\" CELLPADDING=\"0\" CELLSPACING=\"0\">\n";
                echo "<TR><TD TITLE=\"table : " . $gtab["desc"][$gtabid_] . "\"><A HREF=\"Javascript:LmAdm_getFields(" . $verknparams . ",$globid,$gtabid_,$r_verknfieldid,'')\"><IMG SRC=\"assets/images/legacy/outliner/plusonly.gif\" WIDTH=\"18\" HEIGHT=\"16\" ALIGN=\"TOP\" BORDER=\"0\" NAME=\"tab_" . $globid . "_plusminus\"><i class=\"lmb-icon lmb-folder-closed\" WIDTH=\"16\" HEIGHT=\"13\" ALIGN=\"TOP\" BORDER=\"0\" NAME=\"tab_" . $globid . "_box\"></i></A> <B title=\"field: ".$gfield[$gtabid_]["spelling"][$r_verknfieldid]."\"><i>".$gtab["desc"][$verknparams]."</i></B></TD></TR>\n";
                echo "<TR><TD ID=\"el_$globid\">";
                
                echo "</TR></TD>";
                echo "</TABLE>\n";
                
                echo "</TD></TR></TABLE>\n";
                echo "</TD></TR>\n";
                $bzm++;
            }
        }
    }

	echo "</TABLE>\n";
}

/**
 * form relation parameter
 *
 * @param $formid
 * @param $elid
 */
function dyns_edit_relationparams($par){
    global $lang;
    global $db;

    $formid = parse_db_int($par['formid']);
    $elid = parse_db_int($par['elid']);
    $params = $par['relation_params'];

    if(!$formid or !$elid){return false;}

    // get relation parameter
    function getRelationParameter($formid,$elid){

        global $db;

        $sqlquery = "SELECT ID,PARAMETERS,EXTENSION FROM LMB_FORMS WHERE FORM_ID = $formid AND KEYID = $elid";
        $rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
        $relparams = lmbdb_result($rs, 'PARAMETERS');
        $extension = lmbdb_result($rs, 'EXTENSION');
        $id = lmbdb_result($rs, 'ID');

        if($relparams) {
            $params = lmb_eval($relparams . ";");

            $evalparams = array('showfields','edit','width','order');
            foreach ($evalparams as $key => $value) {
                if ($params[$value]) {
                    $params[$value] = var_export($params[$value],1);
                }
            }
        }

        if($extension) {
            $params['extension'] = $extension;
        }

        if($params) {
            return $params;
        }


    }


    // set relation parameter
    function setRelationParameter($formid,$elid,$params){

        global $db;

        $sqlquery = "SELECT ID FROM LMB_FORMS WHERE FORM_ID = $formid AND KEYID = $elid";
        $rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
        $id = lmbdb_result($rs, 'ID');

        if($params['show_inframe'] == 'tag'){
            $params['show_inframe'] = $params['show_inframe_tag'];
        }
        $params['show_inframe_tag'] = null;

        $extension = $params['extension'];
        unset($params['extension']);

        $params = array_filter($params);

        $evalparams = array('showfields','edit','width','order');
        foreach ($evalparams as $key => $value) {
            if ($params[$value]) {
                $params[$value] = lmb_eval('return ' . $params[$value] . ";");
            }
        }

        if(lmb_count($params) > 0) {
            $p = var_export($params, 1);
            $p = 'return '.$p.';';
        }else{
            $p = '';
        }

        $sqlquery = "UPDATE LMB_FORMS SET PARAMETERS = ? WHERE ID = $id";
        $rs = lmb_PrepareSQL($sqlquery,array($p)) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
        $sqlquery = "UPDATE LMB_FORMS SET EXTENSION = ? WHERE ID = $id";
        $rs = lmb_PrepareSQL($sqlquery,array($extension)) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);

    }

    if($params){
        setRelationParameter($formid,$elid,$params);
    }

    $params = getRelationParameter($formid,$elid);
    foreach ($params as $key => $value){
        if($value == 1){
            ${$key} = 'checked';
        }
    }



    ${'show_inframe_'.$params['show_inframe']} = 'selected';
    ${'viewmode_'.$params['viewmode']} = 'selected';
    ${'validity_'.$params['validity']} = 'selected';

    $show_inframe_mods = array('div','iframe','same','tab');
    if($params['show_inframe'] AND !in_array($params['show_inframe'],$show_inframe_mods)){$show_inframe_tag = 'selected';}else{$params['show_inframe'] = null;}
    if($params['show_inframe'] OR $show_inframe_tag){$inframe_tag_display = '';}else{$inframe_tag_display = 'none';}

    echo "
    <table>
    
    <tr><td colspan=\"2\">
    {$lang[2569]}<br>
    <TEXTAREA name=\"relation_params[extension]\" STYLE=\"width:100%;height:150px;background-color:inherit\" Onchange=\"edit_relationparams($elid,1)\">".htmlentities($params['extension'],ENT_QUOTES)."</TEXTAREA>
    </td></tr>
    
    <tr><td><i>{$lang[3038]}</i></td><td>
        <select name=\"relation_params[show_inframe]\" Onchange=\"if(this.value == 'tag'){document.getElementById('inframe_tag').style.display='';}else{edit_relationparams($elid,1);}\"><option>
        <option value=\"window\" $show_inframe_window>new window
        <option value=\"div\" $show_inframe_div>div
        <option value=\"iframe\" $show_inframe_iframe>iframe
        <option value=\"same\" $show_inframe_same>same
        <option value=\"tab\" $show_inframe_tab>new tab
        <option value=\"tag\" $show_inframe_tag>tag (Element-ID)</select>&nbsp;
    <input style=\"display:$inframe_tag_display\" id=\"inframe_tag\" type=\"text\" name=\"relation_params[show_inframe_tag]\" size=\"5\" value=\"".htmlentities($params['show_inframe'],ENT_QUOTES)."\" Onchange=\"edit_relationparams($elid,1)\">
    </td></tr>
    <tr><td><i>{$lang[3014]}</i></td><td><select name=\"relation_params[viewmode]\" Onchange=\"edit_relationparams($elid,1)\">
        <option>
        <option value=\"dropdown\" $viewmode_dropdown>dropdown
        <option value=\"single_ajax\" $viewmode_single_ajax>single_ajax
        <option value=\"multi_ajax\" $viewmode_multi_ajax>multi_ajax
        <option value=\"minimized\" $viewmode_minimized>minimized
        </select>
    </td></tr>
    <tr><td><i>{$lang[3002]}</i></td><td><select name=\"relation_params[validity]\" Onchange=\"edit_relationparams($elid,1)\">
        <option>
        <option value=\"all\" $validity_all>all
        <option value=\"allto\" $validity_allto>all from today
        <option value=\"allfrom\" $validity_allfrom>all to today
        </select>
    </td></tr>
    
    <tr><td><i>{$lang[3015]}</i></td><td><input type=\"text\" name=\"relation_params[formid]\" value=\"".htmlentities($params['formid'],ENT_QUOTES)."\" Onchange=\"edit_relationparams($elid,1)\"></td></tr>
    <tr><td><i>{$lang[3049]}</i></td><td><input type=\"text\" name=\"relation_params[formsize]\" value=\"".htmlentities($params['formsize'],ENT_QUOTES)."\" Onchange=\"edit_relationparams($elid,1)\"></td></tr>
    <tr><td><i>{$lang[3016]}</i></td><td><input type=\"text\" name=\"relation_params[count]\" value=\"".htmlentities($params['count'],ENT_QUOTES)."\" Onchange=\"edit_relationparams($elid,1)\"></td></tr>
    <tr><td><i>{$lang[3017]}</i></td><td><input type=\"text\" name=\"relation_params[ondblclick]\" value=\"".htmlentities($params['ondblclick'],ENT_QUOTES)."\" Onchange=\"edit_relationparams($elid,1)\"></td></tr>
    <tr><td><i>{$lang[3039]}</i></td><td><input type=\"text\" name=\"relation_params[showfields]\" value=\"".htmlentities($params['showfields'],ENT_QUOTES)."\" Onchange=\"edit_relationparams($elid,1)\"></td></tr>
    <tr><td><i>{$lang[3041]}</i></td><td><input type=\"text\" name=\"relation_params[edit]\" value=\"".htmlentities($params['edit'],ENT_QUOTES)."\" Onchange=\"edit_relationparams($elid,1)\"></td></tr>
    <tr><td><i>{$lang[3040]}</i></td><td><input type=\"text\" name=\"relation_params[width]\" value=\"".htmlentities($params['width'],ENT_QUOTES)."\" Onchange=\"edit_relationparams($elid,1)\"></td></tr>
    <tr><td><i>{$lang[3042]}</i></td><td><input type=\"text\" name=\"relation_params[order]\" value=\"".htmlentities($params['order'],ENT_QUOTES)."\" Onchange=\"edit_relationparams($elid,1)\"></td></tr>
    <tr><td><i>{$lang[3070]}</i></td><td><input type=\"checkbox\" name=\"relation_params[applyfilter]\" value=\"1\" Onchange=\"edit_relationparams($elid,1)\" $applyfilter></td></tr>
    <tr><td><i>{$lang[3029]}</i></td><td><input type=\"checkbox\" name=\"relation_params[no_menu]\" value=\"1\" Onchange=\"edit_relationparams($elid,1)\" $no_menu></td></tr>
    <tr><td><i>{$lang[3018]}</i></td><td><input type=\"checkbox\" name=\"relation_params[no_add]\" value=\"1\" Onchange=\"edit_relationparams($elid,1)\" $no_add></td></tr>
    <tr><td><i>{$lang[3019]}</i></td><td><input type=\"checkbox\" name=\"relation_params[no_new]\" value=\"1\" Onchange=\"edit_relationparams($elid,1)\" $no_new></td></tr>
    <tr><td><i>{$lang[3020]}</i></td><td><input type=\"checkbox\" name=\"relation_params[no_edit]\" value=\"1\" Onchange=\"edit_relationparams($elid,1)\" $no_edit></td></tr>
    <tr><td><i>{$lang[3021]}</i></td><td><input type=\"checkbox\" name=\"relation_params[no_replace]\" value=\"1\" Onchange=\"edit_relationparams($elid,1)\" $no_replace></td></tr>
    <tr><td><i>{$lang[3022]}</i></td><td><input type=\"checkbox\" name=\"relation_params[no_search]\" value=\"1\" Onchange=\"edit_relationparams($elid,1)\" $no_search></td></tr>
    <tr><td><i>{$lang[3023]}</i></td><td><input type=\"checkbox\" name=\"relation_params[no_copy]\" value=\"1\" Onchange=\"edit_relationparams($elid,1)\" $no_copy></td></tr>
    <tr><td><i>{$lang[3024]}</i></td><td><input type=\"checkbox\" name=\"relation_params[no_delete]\" value=\"1\" Onchange=\"edit_relationparams($elid,1)\" $no_delete></td></tr>
    <tr><td><i>{$lang[3025]}</i></td><td><input type=\"checkbox\" name=\"relation_params[no_sort]\" value=\"1\" Onchange=\"edit_relationparams($elid,1)\" $no_sort></td></tr>
    <tr><td><i>{$lang[3026]}</i></td><td><input type=\"checkbox\" name=\"relation_params[no_link]\" value=\"1\" Onchange=\"edit_relationparams($elid,1)\" $no_link></td></tr>
    <tr><td><i>{$lang[3027]}</i></td><td><input type=\"checkbox\" name=\"relation_params[no_openlist]\" value=\"1\" Onchange=\"edit_relationparams($elid,1)\" $no_openlist></td></tr>
    <tr><td><i>{$lang[3028]}</i></td><td><input type=\"checkbox\" name=\"relation_params[no_fieldselect]\" value=\"1\" Onchange=\"edit_relationparams($elid,1)\" $no_fieldselect></td></tr>
    <tr><td><i>{$lang[3051]}</i></td><td><input type=\"checkbox\" name=\"relation_params[no_validity]\" value=\"1\" Onchange=\"edit_relationparams($elid,1)\" $no_validity></td></tr>
    <tr><td><i>{$lang[3030]}</i></td><td><input type=\"checkbox\" name=\"relation_params[search]\" value=\"1\" Onchange=\"edit_relationparams($elid,1)\" $search></td></tr>
    <tr><td><i>{$lang[3031]}</i></td><td><input type=\"checkbox\" name=\"relation_params[showall]\" value=\"1\" Onchange=\"edit_relationparams($elid,1)\" $showall></td></tr>
    <tr><td><i>{$lang[3032]}</i></td><td><input type=\"checkbox\" name=\"relation_params[getlongval]\" value=\"1\" Onchange=\"edit_relationparams($elid,1)\" $getlongval></td></tr>
    <tr><td><i>{$lang[3033]}</i></td><td><input type=\"checkbox\" name=\"relation_params[nogresult]\" value=\"1\" Onchange=\"edit_relationparams($elid,1)\" $nogresult></td></tr>
    <tr><td><i>{$lang[3034]}</i></td><td><input type=\"checkbox\" name=\"relation_params[no_calendar]\" value=\"1\" Onchange=\"edit_relationparams($elid,1)\" $no_calendar></td></tr>
    <tr><td><i>{$lang[3035]}</i></td><td><input type=\"checkbox\" name=\"relation_params[pagination]\" value=\"1\" Onchange=\"edit_relationparams($elid,1)\" $pagination></td></tr>
    <tr><td><i>{$lang[3036]}</i></td><td><input type=\"checkbox\" name=\"relation_params[indicator]\" value=\"1\" Onchange=\"edit_relationparams($elid,1)\" $indicator></td></tr>
    <tr><td><i>{$lang[3037]}</i></td><td><input type=\"checkbox\" name=\"relation_params[show_relationpath]\" value=\"1\" Onchange=\"edit_relationparams($elid,1)\" $show_relationpath></td></tr>
    </table>
    ";

}



/**
 * tabschema
 *
 * @$par array fieldid
 */
function dyns_tabschemaInfos($par){
	if($par['act'] == 1){
		require_once(COREPATH . 'admin/tables/tabschema_dyn.php');
		show_fieldinfo($par["par1"],$par["par2"]);}
	elseif($par['act'] == 2){
		require_once(COREPATH . 'admin/tables/tabschema_dyn.php');
		show_linkinfo($par["par1"],$par["par2"],$par["par3"]);}
	elseif($par['act'] == 3){
		require_once(COREPATH . 'admin/tables/view.dao');
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

	require_once(COREPATH . 'admin/tables/view.dao');
	
	$gview = lmb_getQuestValue($viewid);
	
	$setdrag = $par["setdrag"];
	if($par["setrelation"]){
		$gview["relationstring"] = lmb_createQuestRelation($viewid,$gview["relationstring"],$par["setrelation"],$par["settype"]);
	}

	require_once(COREPATH . 'admin/tables/viewschema.php');
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

	require_once(COREPATH . 'admin/tables/view.dao');
	
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
    global $lmfieldtype;
	
	$par = lmb_arrayDecode($par,1);
	if (!$par['val']) {
	    $par['val'] = $_REQUEST['val'];
    }
	
	require_once(COREPATH . 'lib/db/db_'.$DBA['DB'].'_admin.lib');
	require_once(COREPATH . 'lib/include_admin.lib');
	require_once(COREPATH . 'admin/tables/tab.lib');
	require_once(COREPATH . 'admin/setup/language.lib');
	require_once(COREPATH . 'admin/tables/gtab_ftype_detail.php');
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
	require_once(COREPATH . 'lib/db/db_'.$DBA['DB'].'_admin.lib');
	require_once(COREPATH . 'lib/include_admin.lib');
	require_once(COREPATH . 'admin/tables/tab.lib');
	require_once(COREPATH . 'admin/setup/language.lib');
	require_once(COREPATH . 'admin/tables/tab.dao');
	require_once(COREPATH . 'admin/tables/tab_detail.php');
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
	
	require_once(COREPATH . 'admin/tabletrees/tabletree.lib');
	
	show_tabletreeSettings($par);
}

/**
 * pchart
 *
 * @param unknown_type $par
 */
function dyns_saveDiagDetail($par){
	require_once(COREPATH . 'admin/diagram/diag_detail_ajax.php');
	
	$diag_id = $par['diag_id'];
	$diag_tab_id = $par['diag_tab_id'];
	$field_id = $par['field_id'];
	$show = $par['show']=="true";
	$axis = ($par['axis']) == null ? 0 : $par['axis'];
	$color = $par['color'];
        
	lmb_updateData($diag_id,$field_id,$show,$axis,$color);
}

function dyns_createDiagPChart($par){
	require_once(COREPATH . 'extra/diagram/LmbChart.php');
	if($diag = LmbChart::makeChart($par['diag_id'])){
        $diag = str_replace(DEPENDENTPATH,'',$diag);
        echo trim($diag,'/');
	}
}

function dyns_saveDiagSettings($par){
	require_once(COREPATH . 'admin/diagram/diag_detail_ajax.php');
	lmb_saveCustomizationSettings($par);
}


/**
 * menu editor
 *
 * @param unknown_type $par
 */
function dyns_menuEditor($par){
    require_once(COREPATH . 'admin/setup/menueditor.dao');
    
    $linkid = $par['linkid'];
    $custmenu = lmb_custmenuGet($linkid);

    if ($par['detail']) {

        if($par['act']) {
            lmb_custmenuEdit($custmenu, $par);
            $custmenu = lmb_custmenuGet($linkid);
        }
        lmb_custmenuDetail($custmenu,$par);

    } else {

        if($par['act']) {
            lmb_custmenuEdit($custmenu, $par);
            $custmenu = lmb_custmenuGet($linkid);
        }
        lmb_custmenu($custmenu,$linkid,0);
    }
}


/**
 * extension manager
 *
 * @param unknown_type $par
 */
function dyns_extensionEditor($par){
	require_once(COREPATH . 'admin/tools/extension_ajax.php');
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
	require_once(COREPATH . 'admin/tools/extension_ajax.php');
}

/**
 * php editor
 *
 * @param array $par
 */
function dyns_executePhpCode($par){
    // prevent php execution via link
    if (array_key_exists('phpCode', $_GET)) {
        echo "PhpCode in GET request not allowed!";
        return;
    }
    require_once(COREPATH . 'admin/tools/php_editor.lib');
    executePhpCode($par['phpCode'], $par['maxExecutionSeconds']);
}



/**
 * edit the argument of PHP or SQL argument field
 *
 * @param array $par
 */
function dyns_editFieldTypeArgument($par){
    // prevent php execution via link
    require_once(COREPATH . 'admin/tables/argument.dao');
    lmb_editFieldTypeArgumentDyns($par);
}

/**
 * refresh PHP argmuent
 * 
 * @param $par
 */
function dyns_refreshFieldTypeArgument($par){
    // prevent php execution via link
    require_once(COREPATH . 'admin/tables/argument.dao');
    lmb_refreshFieldTypeArgumentDyns($par);
}



/**
 * sync_export
 *
 * @param $par
 */
function dyns_syncToClient($par){
    // prevent php execution via link
    require_once(COREPATH . 'admin/tools/export_sync_ajax.php');
    lmb_syncToClient($par);
}

/**
 * sync_validate
 *
 * @param $par
 */
function dyns_syncValidate($par){
    require_once(COREPATH . 'admin/tools/datasync/validate.lib');

    if($par['phase'] == 2){
        echo json_encode(lmb_SyncValidatePhase2($par['syncid'],$par['table']));
    }else {
        echo json_encode(lmb_SyncValidate($par['syncid']));
    }
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
	echo "<script>\n showAlert('".implode("\\n",$alert)."');\n</script>\n";
}


# Puffersteuerung Ausgabe
if($output = ob_get_contents()){

	#if(!$noencode){
	#	$output = lmb_utf8_encode($output);
	#}

	ob_end_clean();
	echo $output;
}



if ($db) {lmbdb_close($db);}
