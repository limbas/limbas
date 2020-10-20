<?php
/*
 * Copyright notice
 * (c) 1998-2019 Limbas GmbH (support@limbas.org)
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
 * ID: 204
 */

require_once("lib/db/db_wrapper.lib");
require_once("lib/include.lib");
require_once("lib/session.lib");
require_once("lib/context.lib");
require_once("extra/snapshot/snapshot.lib");
#require_once("extra/workflow/workflow.lib");

# EXTENSIONS
if($gLmbExt["ext_ajax.inc"]){
    foreach ($gLmbExt["ext_ajax.inc"] as $key => $extfile){
        require_once($extfile);
    }
}

# logout
function dyns_logout($params){

}

/**
 * hide or show frames
 *
 * @param unknown_type $params
 */
function dyns_layoutSettings($params){
    global $db;
    global $session;

    $m_setting = array();

    $sqlquery = "SELECT M_SETTING FROM LMB_USERDB WHERE USER_ID = ".$session["user_id"];
    $rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
    if($m_setting = lmbdb_result($rs, "M_SETTING")){
        $m_setting = unserialize($m_setting);
    }

    if($params["frame"] AND $params["size"]){
        $m_setting["frame"][$params["frame"]] = $params["size"];
    }elseif($params["frame"]){
        unset($m_setting["frame"][$params["frame"]]);
    }elseif($params["menu"] AND $params["show"]){
        $m_setting["menu"][$params["menu"]] = 1;
    }elseif($params["menu"] AND !$params["show"]){
        unset($m_setting["menu"][$params["menu"]]);
    }elseif($params["submenu"] AND $params["show"]){
        $m_setting["submenu"][$params["submenu"]] = 1;
    }elseif($params["submenu"] AND !$params["show"]){
        unset($m_setting["submenu"][$params["submenu"]]);
    }elseif($params["cal"] AND $params["show"]){
        $m_setting["cal"][$params["cal"]] = 1;
    }elseif($params["cal"] AND !$params["show"]){
        unset($m_setting["cal"][$params["cal"]]);
    }

    if(is_array($m_setting)){
        $prepare_string = "UPDATE LMB_USERDB SET M_SETTING = ? WHERE USER_ID = ".$session["user_id"];
        lmb_PrepareSQL($prepare_string,array(serialize($m_setting)),__FILE__,__LINE__);
    }
}


#################### tables ##################



/**
 * custmenu / contextmenu
 *
 * @param $params
 */
function dyns_gtabCustmenu($params){
    lmb_pop_custmenu($params['custmenu'],$params['gtabid'],$params['ID'],null,$params['fieldid']);
}


/**
 * lock dataset
 *
 * @param $params
 */
function dyns_gtabLockData($params){
    global $session;
    global $umgvar;
    global $LINK;

    require_once("gtab/gtab.lib");

    $ID = $params["ID"];
    $gtabid = $params["gtabid"];

    $lock = lock_data_check($gtabid,$ID,$session["user_id"]);
    if($lock["isselflocked"]){
        $commit = lock_data_set($gtabid,$ID,$session["user_id"]);
    }elseif(!$lock){
        echo "free";
    }else{
        echo "lock";
    }

}

/**
 * table relation view
 *
 * @param unknown_type $params
 */
function dyns_extRelationFields($params){
    global $db;
    global $gtab;
    global $gfield;
    global $gform;
    global $farbschema;
    global $lang;
    global $userdat;
    global $session;
    global $filter;

    require_once "gtab/gtab.lib";
    require_once("gtab/gtab_type.lib");
    require_once "gtab/gtab_type_erg.lib";

    $noedit = 1;
    $gtabid = $params["gtabid"];
    $field_id = $params["gfieldid"];
    $ID = $params["ID"];
    $viewmode = $params["viewmode"];
    $typ = $params["edittype"];
    $gformid = $params["gformid"];
    $formid = $params["formid"];
    $vgtabid = $gfield[$gtabid]["verkntabid"][$field_id];
    $vfieldid = $gfield[$gtabid]["verknfieldid"][$field_id];

    # relation based table
    if($vgtabid AND $vfieldid){
        $vuniqueid = $gfield[$gtabid]["form_name"][$field_id]."_".$gformid;
        $linklevel["tabid"] = $vgtabid;
        $linklevel["fieldid"] = $vfieldid;
        $isrelation = 1;
    # independent table
    }else{
        $vuniqueid = "g_0_".$field_id;
        $linklevel["tabid"] = $gtabid;
        $vgtabid = $gtabid;
        $isrelation = false;
    }

    # if post formular save changes first
    if($params["history_fields"]){
        dyns_postHistoryFields($params);
    }

    # formular as ajax select
    if($gfield[$gtabid]["artleiste"][$field_id]){
        $formtype = "ajaxselect";
    }

    # ----------- Edit Permission -----------
    /*
    if(!$params['viewmode'] AND $params["ExtAction"] != 'sortup' AND $params["ExtAction"] != 'sortdown' AND $params["ExtAction"] != 'search' AND $params["ExtAction"] != 'searchval' AND $params["ExtAction"] != 'showall'){
        if($gfield[$gtabid]["perm_edit"][$field_id] AND $typ != 2){
            $noedit = 0;
            $query_dataid = array($field_id => true);
            // postcheck routine
            if(update_data_precheck($gtabid,$ID,$query_dataid,$query_result=null,$old_gresult=null,$triggerResult=null,1)){$noedit = 0;}
        }else{
            lmb_log::error('dyns_extRelationFields - permission denied for edit field or table', $lang[1317], $gtabid, $field_id, $ID);
        }
    }*/

    if($gfield[$gtabid]["perm_edit"][$field_id] AND $typ != 2) {
        $noedit = 0;
        $query_dataid = array($field_id => true);
    }

    # use extended form settings
    #if($gform[$gformid]['parameters'][$formid]){eval($gform[$gformid]['parameters'][$formid].";");}

    # only for relation based table
    if($isrelation){

        if(!$noedit) {
            # add relation
            if ($params["ExtAction"] == 'link' AND $params["relationid"]) {
                $verkn = set_verknpf($gtabid, $field_id, $ID, $params["relationid"], 0, 0, 0);
                set_joins($gtabid, $verkn);
            }

            # create relation
            if ($params["ExtAction"] == 'create') {
                new_record($vgtabid, 1, $field_id, $gtabid, $ID);
            }

            # drop relation
            if ($params["ExtAction"] == 'unlink' AND $params["relationid"]) {
                $verkn = set_verknpf($gtabid, $field_id, $ID, 0, $params["relationid"], 0, 0);
                set_joins($gtabid, $verkn);
            }

            # drop all relations
            if ($params["ExtAction"] == 'unlinkall') {
                $verkn = set_verknpf($gtabid, $field_id, $ID, 0, 'unlinkall', 0, 0);
                set_joins($gtabid, $verkn);
            }

            # drop&delete relation
            if ($params["ExtAction"] == 'delete' AND $params["relationid"]) {
                require_once "extra/explorer/filestructure.lib";
                $dellist = explode(',', $params["relationid"]);
                foreach ($dellist as $dkey => $did) {
                    $verkn = set_verknpf($gtabid, $field_id, $ID, 0, $did, 0, 0);
                    if (set_joins($gtabid, $verkn)) {
                        del_data($vgtabid, $did, "delete");
                    }
                }
            }

            # copy relation
            if ($params["ExtAction"] == 'copy' AND $params["relationid"]) {
                require_once "extra/explorer/filestructure.lib";
                $copylist = explode(',', $params["relationid"]);
                foreach ($copylist as $dkey => $did) {
                    $did_ = explode('_', $did);
                    if ($did_[0]) {
                        $newID = new_record($vgtabid, 1, $field_id, $gtabid, $ID, $did_[0]);
                    }
                }
            }

        }

        # set relation
        $verkn = set_verknpf($gtabid,$field_id,$ID,0,0,1,1);

        # change relation based order
        if(($params["ExtAction"] == 'sortup' OR $params["ExtAction"] == 'sortdown') AND $params["relationid"]){
            $params["orderfield"] = "reset";
            $vgresult = sql_14_c($vgtabid,$verkn,$linklevel,$gformid,$params["orderfield"],$gsr);

            $bzm = 0;
            foreach ($vgresult[$vgtabid]["id"] as $vkey => $vval){
                if($vval == $params["relationid"]){$elkey = $vkey;}
                $vgresult[$vgtabid]["sort"][$vkey] = $bzm;
                $bzm++;
            }

            if($params["ExtAction"] == 'sortup'){
                if($elkey){
                    $vgresult[$vgtabid]["sort"][$elkey] = $vgresult[$vgtabid]["sort"][$elkey]-1;
                    $vgresult[$vgtabid]["sort"][$elkey-1] = $vgresult[$vgtabid]["sort"][$elkey]+1;
                }
            }else{
                if($vgresult[$vgtabid]["sort"][$elkey+1]){
                    $vgresult[$vgtabid]["sort"][$elkey] = $vgresult[$vgtabid]["sort"][$elkey]+1;
                    $vgresult[$vgtabid]["sort"][$elkey+1] = $vgresult[$vgtabid]["sort"][$elkey]-1;
                }
            }

            asort($vgresult[$vgtabid]["sort"]);

            # change specific order
            foreach ($vgresult[$vgtabid]["sort"] as $vkey => $vval){
                $sqlquery = "UPDATE ".$gfield[$gtabid]["md5tab"][$field_id]." SET SORT = $vval WHERE ID = $ID AND VERKN_ID = ".$vgresult[$vgtabid]["id"][$vkey];
                $rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
            }
            save_viewSettings($vuniqueid);
        }

        # only for independent table
    }else{
        $verkn = null;

        # ----------- Edit Permission -----------
        if($gtab["edit"][$gtabid] AND $typ != 2){$noedit = 0;}else{
            lmb_log::error('dyns_extRelationFields - permission denied for edit field or table', $lang[1317], $gtabid, $field_id, $ID);
        }

        # delete dataset
        if($params["ExtAction"] == 'delete' AND $params["relationid"] AND !$noedit){
            $dellist = explode(',',$params["relationid"]);
            foreach ($dellist as $dkey => $did){
                $did_ = explode('_',$did);
                del_data($vgtabid,$did_[0],"delete");
            }
        }

        # order
        if(!$params["orderfield"]){
            $vfilter["order"][$vgtabid] = $filter["ext_RelationFields"]["order"][$vuniqueid];
        }elseif($params["orderfield"] == "reset"){
            unset($filter["ext_RelationFields"]["order"][$vuniqueid]);
        }elseif("ASC" == $filter["ext_RelationFields"]["order"][$vuniqueid][0][2]){
            $vfilter["order"][$vgtabid][0] = array($vgtabid,$params["orderfield"],"DESC");
            $filter["ext_RelationFields"]["order"][$vuniqueid] = $vfilter["order"][$vgtabid];
            save_viewSettings($vuniqueid);
        }else{
            $vfilter["order"][$vgtabid][0] = array($vgtabid,$params["orderfield"],"ASC");
            $filter["ext_RelationFields"]["order"][$vuniqueid] = $vfilter["order"][$vgtabid];
            save_viewSettings($vuniqueid);
        }

    }

    # show all datasets
    if($params["ExtAction"] == 'showall'){
        if($filter["ext_RelationFields"]["showall"][$vuniqueid]) {
            $filter["ext_RelationFields"]["showall"][$vuniqueid] = 0;
        }else{
            $filter["ext_RelationFields"]["showall"][$vuniqueid] = 1;
        }
        save_viewSettings($vuniqueid);
    }

    // get result-count
    get_countFilter($vgtabid,$vuniqueid,null,$vfilter);

    # pagination
    if($params["ExtAction"] == 'page'){
        $filter["ext_RelationFields"]["page"][$vuniqueid] = $params["ExtValue"];
        $vfilter["page"][$vgtabid] = $filter["ext_RelationFields"]["page"][$vuniqueid];
    }

    # maxresult
    if($params["ExtAction"] == 'count'){
        $filter["ext_RelationFields"]["count"][$vuniqueid] = $params["ExtValue"];
        $vfilter["anzahl"][$vgtabid] = $filter["ext_RelationFields"]["count"][$vuniqueid];
    }

    # editmode
    if($params["ExtAction"] == 'edit' AND !$noedit AND $typ == 1){
        if($filter["ext_RelationFields"]["edit"][$vuniqueid] == 1){
            $filter["ext_RelationFields"]["edit"][$vuniqueid] = 0;
        }else{
            $filter["ext_RelationFields"]["edit"][$vuniqueid] = 1;
        }
        save_viewSettings($vuniqueid,null);
    }

    # list of shown fields
    if($viewmode){
        $present = array_keys($filter["ext_RelationFields"][$vuniqueid],$viewmode);
        if(count($present) > 0){
            unset($filter["ext_RelationFields"][$vuniqueid][$present[0]]);
        }else{
            $filter["ext_RelationFields"][$vuniqueid][] = $viewmode;
        }
        save_viewSettings($vuniqueid);
    }

    # search switch on/off
    if($params["ExtAction"] == 'search'){
        if($filter["ext_RelationFields"]["search"][$vuniqueid]){
            unset($filter["ext_RelationFields"]["search"][$vuniqueid]);
            unset($filter["ext_RelationFields"]["searchval"][$vuniqueid]);
        }else{
            $filter["ext_RelationFields"]["search"][$vuniqueid] = 1;
        }
        save_viewSettings($vuniqueid);
    }

    if($filter["ext_RelationFields"]["search"][$vuniqueid] OR $filter["ext_RelationFields"]["searchval"][$vuniqueid]){
        ########### relation params ##########
        $vgtabid_ = $vgtabid;
        if($params["relationid"] > 1000){
            $vgtabid_ = $gfield[$gtabid]["verknparams"][$field_id];
        }
        ########### /relation params ##########
        $gsr = $filter["ext_RelationFields"]["searchval"][$vuniqueid];
        if($params["ExtAction"] == 'searchval'){
            $gsr[$vgtabid_][$params["relationid"]][0] = $params["ExtValue"];
            $filter["ext_RelationFields"]["searchval"][$vuniqueid] = $gsr;
            save_viewSettings($vuniqueid);
        }
    }

    # specific formular filter
    if($gformid AND $formid AND $gform[$gformid]["parameters"][$formid]){
        $filter_ = eval($gform[$gformid]["parameters"][$formid]);
        if(is_array($filter_)){
            if($fval['showall']){$vfilter["anzahl"][$vgtabid] = 'all';}
            foreach ($filter_ as $fkey => $fval){
                if($fkey == 'showfields' AND !$filter["ext_RelationFields"][$vuniqueid]){
                    $filter["ext_RelationFields"][$vuniqueid] = $fval;
                }elseif($fkey == 'order'){
                    if(!$vfilter["order"][$vgtabid]){
                        $vfilter["order"][$vgtabid] = $fval;
                    }
                }else{
                    $filter["ext_RelationFields"][$fkey][$vuniqueid] = $fval;
                }
            }
        }
    }

    # relation based table
    if($isrelation){
        # only for Ajax-Select
        if($formtype == "ajaxselect"){
            $linklevel["norecursiv"] = 1; # do not resolve recursiv relations
            # --- zeige definierte Felder ----
            if($gfield[$gtabid]["verknview"][$field_id]){
                $showfields = $gfield[$gtabid]["verknview"][$field_id];
                $afieldlist[$linklevel["tabid"]] = $gfield[$gtabid]["verknview"][$field_id];
            }
        }
        $vgresult = sql_14_c($vgtabid_,$verkn,$linklevel,$gformid,$params["orderfield"],$gsr,$afieldlist,$extension,$params);
    # simple table
    }else{
        $onlyfield[$vgtabid] = $filter["ext_RelationFields"][$vuniqueid];
        $vgresult = get_gresult($vgtabid,1,$vfilter,$gsr,null,$onlyfield,null,$extension);
    }

    $rscount = $vgresult[$vgtabid]["res_count"];
    # only for Ajax-Select
    if($formtype == "ajaxselect"){
        # unique
        if($gfield[$gtabid]["unique"][$field_id]){
            $sval = array();
            foreach ($showfields as $skey => $sfieldid){
                $fname = "cftyp_".$gfield[$linklevel["tabid"]]["funcid"][$sfieldid];
                $sval[] = $fname(0,$sfieldid,$linklevel["tabid"],3,$vgresult,0);
            }
            echo html_entity_decode(implode($gfield[$gtabid]["verknviewcut"][$field_id],$sval));
        }else{
            cftyp_14_c($gtabid,$field_id,$ID,$typ,$gformid,$formid,$vgtabid,$vfieldid,$showfields,$vgresult);
        }
        # Ajax Detail-View
    }else{
        cftyp_14_d($vgresult,$rscount,$typ,$verkn,$gtabid,$vgtabid,$field_id,$ID,$VID,$linklevel,$vuniqueid,$gformid,$formid,$filter);
    }

}


/**
 * show quicksearch results for relations
 *
 * @param unknown_type $searchvalue
 * @param unknown_type $form_name
 * @param unknown_type $vgtabid
 * @param unknown_type $vfieldid
 * @param unknown_type $gtabid
 * @param unknown_type $fieldid
 * @param unknown_type $ID
 * @param unknown_type $typ
 * @param unknown_type $gformid
 * @param unknown_type $formid
 */

# \$form_value,\$form_name,\$par1,\$par2,\$par3,\$par4,\$par5,\$par6,\$gformid,\$formid,\$nextpage
# function dyns_14_b($searchvalue,$form_name,$vgtabid,$vfieldid,$gtabid,$fieldid,$ID,$typ,$gformid,$formid,$nextpage){
function dyns_14_b($params){

    global $farbschema;
    global $gfield;
    global $gtab;
    global $lang;

    $searchvalue = $params['form_value'];
    $form_name = $params['form_name'];
    $vgtabid = $params['par1'];
    $vfieldid = $params['par2'];
    $gtabid = $params['par3'];
    $fieldid = $params['par4'];
    $ID = $params['par5'];
    $typ = $params['par6'];
    $gformid = $params['gformid'];
    $formid = $params['formid'];
    $nextpage = $params['nextpage'];

    $require = "gtab/gtab.lib";require_once($require);
    $require = "gtab/sql/gtab_erg.dao";require_once($require);
    $require = "gtab/gtab_type_erg.lib";require_once($require);

    # Explorer Zusatz
    if($gtab["typ"][$vgtabid] == 3){
        $require = "extra/explorer/filestructure.lib";require_once($require);
        get_filestructure();
        global $filestruct;
        $fields[$vgtabid] = array($vfieldid,5);
        # Default
    }else{
        if($gfield[$gtabid]["verknfind"][$fieldid]){
            $fields[$vgtabid] = $gfield[$gtabid]["verknfind"][$fieldid];
        }else{
            $fields[$vgtabid] = array($vfieldid);
        }
    }

    # --- Liste aller zu durchsuchenden Verknüpfungsfelder ---
    if($searchvalue != "*"){
        $where_ = array();
        $aval_ = array();
        $from_ = array();
        $whereGlob_ = array();
        foreach($gfield[$gtabid]["verknsearch"][$fieldid] as $vkey => $vval){

            $gsr_ = array();
            $gsr_[$vgtabid][$vval][0] = lmb_utf8_decode($searchvalue);
            #$gsr_[$vgtabid][$vval]["txt"][0] = 1;
            #$gsr_[$vgtabid][$vval]["cs"][0] = 0;
            $result_ = get_where($vval,$vgtabid,$gsr_);

            if($result_["where"]){$where_ = array_merge($where_,$result_["where"]);}
            if($result_["vwhere"]){$where_ = array_merge($where_,$result_["vwhere"]);}
            if($result_["aval"]){$aval_ = array_merge($aval_,$result_["aval"]);}
            if($result_["from_"]){$from_ = array_merge($from_,$result_["from_"]);}
            if($result_["where_glob"]){$whereGlob_ = array_merge($whereGlob_,$result_["where_glob"]);}
        }

        if($aval_[0]){
            $extension["where"][] = "(".implode(" AND ",$aval_).")";
        }
        if($where_[0]){
            $extension["where"][] = "(".implode(" OR ",$where_).")";
        }
        if($from_[0]){
            $extension["from"] = $from_;
        }
        if($whereGlob_[0]) {
            $extension["where"][] = "(".implode(" AND ",$whereGlob_).")";
        }
    }


    # finde verknüpften Datensatz
    # (theoretisch auch über if($gresult[$gtabid]["verkn_id"][$bzm]) möglich aber in gtab.lib für unique ausgeschaltet)
    #$verkn = set_verknpf($gtabid,$fieldid,$ID,0,0,1,1);
    #$gresult = get_gresult($vgtabid,1,$filter,null,$verkn,$fields,0,$extension);
    #$isrelation = $gresult[$vgtabid]['id'][0];

    $filter["anzahl"][$vgtabid] = 25;
    $extension["distinct"] = 'DISTINCT';
    if($nextpage){
        $filter["page"][$vgtabid] = $nextpage++;
    }else{$nextpage = 2;}
    $verkn = set_verknpf($gtabid,$fieldid,$ID,0,0,0,1);
    $gresult = get_gresult($vgtabid,1,$filter,null,$verkn,$fields,0,$extension);

    if($gresult[$vgtabid]["id"][0]){
        /* ------------------ Default --------------------- */

        # Verknüpfung löschen
        #if($isrelation){
        $func = "if(confirm('".$lang[2776]."')){LmExt_RelationFields(this,'$gtabid','$fieldid','','$typ','$ID','','','unlinkall','','$gformid','$formid','".$gfield[$gtabid]["ajaxpost"][$fieldid]."',event);}";
        pop_menu2($lang[1283], "", "", "lmb-icon-cus lmb-rel-del", "", $func);
        pop_line();
        #}

        foreach ($gresult[$vgtabid]["id"] as $key => $value) {
            $retrn = array();
            /* ------------------ Typefunction --------------------- */
            foreach ($fields[$vgtabid] as $fkey => $ffieldid){
                $fname = "cftyp_".$gfield[$vgtabid]["funcid"][$ffieldid];
                $retrn[] = $fname($key,$ffieldid,$vgtabid,3,$gresult,0);
            }
            $retrn = implode($gfield[$gtabid]["verknfindcut"][$fieldid],$retrn);

            if($gtab["typ"][$vgtabid] == 3){
                $path = set_url($filestruct["level"][$gresult[$vgtabid][5][$key]],$gresult[$vgtabid][5][$key])."/";
                $func = "LmExt_Ex_RelationFields('$gtabid','$fieldid','','','$ID','','$value','','','','','','',event)";
            }else{
                $path = "";
                $func = "LmExt_RelationFields(this,'$gtabid','$fieldid','','$typ','$ID','','$value','link','','$gformid','$formid','".$gfield[$gtabid]["ajaxpost"][$fieldid]."',event);";
            }
            #if($gfield[$gtabid]["unique"][$fieldid]){$close = "document.getElementById(dyns_el.name+'l').innerHTML = '';document.getElementById('lmbAjaxContainer').style.visibility='hidden';";}
            pop_menu2($path.$retrn, "", "", "lmb-icon-cus lmb-rel-add", "", $func);
        }
        if($gresult[$vgtabid]["res_count"] > $gresult[$vgtabid]["res_viewcount"]){
            pop_line();
            pop_menu2("... ".$lang[1297], "", "", "lmb-next", "", "lmbAjax_dynsearch(event,null,'14_b','$form_name','$vgtabid','$vfieldid','$gtabid','$fieldid','$ID','$typ','$gformid','$formid','','$nextpage');");
        }
    }else{
        echo $lang[98];
    }
}


/**
 * quick relation for tablelist
 *
 * @param unknown_type $params
 */
function dyns_extRelationList($params){
    global $farbschema;
    global $gfield;

    require_once("gtab/gtab.lib");
    require_once("gtab/gtab_type.lib");
    require_once("gtab/gtab_type_erg.lib");

    $gtabid = $params["gtabid"];
    $fieldid = $params["gfieldid"];
    $ID = $params["ID"];

    $gresult = null;

    dftyp_14($ID,$gresult,$fieldid,$gtabid);

}


/**
 * relation preview
 *
 * @param unknown_type $params
 */
function dyns_gresultPreView($params){
    global $farbschema;
    global $gfield;

    require_once("gtab/gtab.lib");
    require_once("gtab/gtab_type_erg.lib");

    $gtabid = $params["gtabid"];
    $fieldid = $params["gfieldid"];
    $ID = $params["ID"];

    $onlyfield[$gtabid][] = $fieldid;
    $gresult = get_gresult($gtabid,1,null,null,null,$onlyfield,$ID);
    # Ausgabe
    if($gresult){
        $fname = "cftyp_".$gfield[$gtabid]["funcid"][$fieldid];
        /* ------------------ Typefunction --------------------- */
        $retrn = $fname(0,$fieldid,$gtabid,3,$gresult,null);
        /* ------------------ /Typefunction -------------------- */
        echo htmlspecialchars_decode($retrn);
    }

}

/**
 * ajax post for tablelist
 *
 * @param unknown_type $params
 */
function dyns_gresultGtab($params){
    global $filter;
    global $gsr;
    global $gsnap;
    global $LINK;
    global $gverkn;
    global $popc;
    global $pophist;
    global $gtab;


    # encode all requests
    /*
    foreach ($params as $key => $value){
        if(is_string($value)){
            $_REQUEST[$key] = lmb_utf8_decode($value);
            ${$key} = $_REQUEST[$key];
        }elseif(is_array($value)){
            $value = lmb_arrayDecode($value,1);
            ${$key} = $value;
        }
    }*/

    foreach ($params as $key => $value){
        ${$key} = $value;
    }

    require_once("gtab/gtab.lib");
    require_once("gtab/html/gtab_erg.lib");
    require_once("gtab/gtab_type_erg.lib");
    require_once("gtab/gtab_type.lib");
    require_once("gtab/gtab_register.lib");

    #$pophist["cdone"][$gtabid] = 1;

    ####### Relation #######
    if($gtab["groupable"][$gtabid] AND $gverkn[$gtabid]["id"]){
        $pophist["cdone"][$gtabid] = 1;
    }

    if($verknpf){
        $verkn = set_verknpf($verkn_tabid,$verkn_fieldid,$verkn_ID,0,0,$verkn_showonly,$verknpf);
        # Rückwertige Verknüpfung
        if($verkn["verknpf"] == 2 AND $verkn AND !$ID AND $action != 'gtab_neu'){
            $ID = r_verknpf($gtabid,$verkn);
            # Pool Verknüpfung
        }elseif(!$ID AND $verkn AND ($action == 'gtab_change' OR $action == 'gtab_deterg')){
            $ID = p_verknpf($gtabid,$verkn);
        }
    }

    require_once("gtab/sql/gtab_erg.dao");

    if($view_search){
        lmbGlistSearch($gtabid,$verknpf,$verkn,1);
    }
    echo "#LMBSPLIT#";
    if($view_header){
        lmbGlistHeader($gtabid,$gresult,0,1);
    }
    echo "#LMBSPLIT#";
    if($view_body){
        global $popg;
        if($popg[$gtabid][0]['null']){
            global $gfield;
            foreach($popg[$gtabid][0]['null'] as $key => $value){
                if($value AND $gfield[$gtabid]["groupable"][$key]){$group_fields[] = $key;}
            }
        }
        if($group_fields){
            table_group($gtabid,$group_fields,$filter,0,$gsr,$verkn);
        }else{
            lmbGlistBody($gtabid,$gresult,0,$verkn,$verknpf,$filter,0,0,0,0,1,1);
        }
    }
    echo "#LMBSPLIT#";
    if($view_footer){
        lmbGlistFooter($gtabid,$gresult,$filter,1);
    }

}


# ---- ajax post for History Fields --------
function dyns_postHistoryFields($params){
    global $LINK;

    require_once("gtab/gtab.lib");
    #require_once("extra/explorer/filestructure.lib");

    # --- Datenatz-Update -----------------------------------
    if($params["history_fields"] AND $LINK[3]){
        if($params["old_action"] == "gtab_erg"){$chtyp = 2;}else{$chtyp = 1;}
        lmb_StartTransaction();
        if(update_data($params["history_fields"],$chtyp)){
            lmb_EndTransaction(1);
        }else{
            lmb_EndTransaction(0);
        }
    }
}


# ---- form tabulator elements --------
function dyns_formListElements($params){
    global $gform;

    require_once("gtab/gtab.lib");
    require_once("gtab/gtab_type.lib");
    require_once("gtab/gtab_form.lib");

    $gtabid = $params['gtabid'];
    $ID = $params['ID'];
    $form_id = $params['gformid'];
    $action = $params['formaction'];

    $gresult = get_gresult($gtabid,null,null,null,0,$gform[$form_id]["used_fields"],$ID);
    formListElements($action,$gtabid,$ID,$gresult,$form_id,$ellist);

}


/**
 * ajax post for subform
 *
 * @param unknown_type $params
 */
function dyns_openSubForm($params){
    global $gform;
    global $gformlist;

    require_once("gtab/gtab.lib");
    require_once("gtab/gtab_type.lib");
    require_once("gtab/gtab_form.lib");
    require_once("gtab/sql/gtab_change.dao");

    $gtabid = $params['gtabid'];
    $ID = $params['ID'];
    $gformid = $params['gformid'];
    $action = $params['action'];
    if(!$gformid){return false;}

    // create new dataset
    if($action == 'gtab_neu'){
        $ID = new_record($gtabid,null,$params['verkn_fieldid'], $params['verkn_tabid'], $params['verkn_ID']);
    }

    $gresult = get_gresult($gtabid, null, null, null, null, $gform[$gformid]["used_fields"], $ID);
    form_gresult($ID, $gtabid, $gformid, $gresult); # need for fields of related tables
    formListElements('gtab_change', $gtabid, $ID, $gresult, $gformid);

    echo "<input id=\"lmb_SubFormSize_$gformid\" type=\"hidden\" value=\"".$gformlist[$gtabid]["dimension"][$gformid]."\">";
    echo "<input id=\"lmb_SubFormTitle_$gformid\" type=\"hidden\" value=\"".$gformlist[$gtabid]["name"][$gformid]."\">";
}


# ------- Zellen/Reihen-Farben eintragen ------
function dyns_lmbSetGtabColor($params){
    global $LINK;

    require_once("gtab/gtab.lib");

    $td_color = $params["td_color"];
    if($td_color AND $LINK[140]){
        gfield_colors($td_color);
    }

}

// multipleselect - edit details in new div
function dyns_multipleSelect($params){
    global $gfield;
    global $gtab;
    global $lang;
    global $LINK;

    $p = array('ID','gtabid','field_id','start','change_id','del_id','fs_sel','fs_val','fs_kw','fs_checkbox','level_id','find_keyw','find_value','select_add','select_cut','select_value','select_keywords','select_sort','select_sort_d','num_result');
    foreach ($p as $k => $v){
        ${$v} = $params[$v];
    }

    require_once('gtab/gtab.lib');
    require_once('gtab/sql/add_select.dao');
    require_once('gtab/html/add_select.php');

}

// multibleselect - reload / change content
function dyns_11($params){
    global $gform;
    global $LINK;

    require_once("gtab/gtab.lib");
    require_once("gtab/gtab_type.lib");

    $history_fields = $params['history_fields'];
    $gtabid = $params['gtabid'];
    $fieldid = $params['fieldid'];
    $ID = $params['ID'];
    $gformid = $params['form_id'];

    foreach ($params as $k => $v){
        if(substr($k,0,2) == 'g_') {
            global ${$k};
            ${$k} = $v;
        }
    }

    $change_ok = 1;
    // change/add values
    if($history_fields AND $LINK[3]) {
        require_once("gtab/sql/gtab_use.dao");
    }

    #$form_el = $params['form_elid'];
	#if($gform[$gformid]["class"][$form_el]){$class = "CLASS=\"".$gform[$gformid]["class"][$form_el]."\"";}else{$class = "";}

    dftyp_11($ID,$null,$fieldid,$gtabid,$class,-1,null,1,null,null,$gformid,$form_el);

}



// multibleselect - open level in ajax search result
function dyns_11_a_level($params){
    global $gfield;

    $new_params = array(
        'gformid' => $params['level'],
        'par1' => $params['page'],
        'par3' => $params['gtabid'],
        'par4' => $params['fieldid'],
        'par5' => $params['ID'],
        'form_name' => $params['form_name'],
        'form_value' => $params['form_value']
    );

    ob_start();
    dyns_11_a($new_params);
    $tmp = ob_get_clean();

    if(empty($tmp)) $tmp = "<div id=\"{$gfield[$params["gtabid"]]["form_name"][$params["fieldid"]]}_{$params["level"]}\"></div>";

    echo "ok#L#$tmp";
}

/**
 * // multipleselect - ajax search result
 * $form_value,\$form_name,\$par1,\$par2,\$par3,\$par4,\$par5,\$par6,\$gformid,\$formid,\$nextpage
 * $value,$form_name,$page,$null,$gtabid,$fieldid,$ID,$typ,$level=0,$exclude=array()){
 * @param $params
 */
function dyns_11_a($params){
    global $farbschema;
    global $gfield;
    global $db;
    global $umgvar;
    global $session;

    $value = $params['form_value'];
    $form_name = $params['form_name'];
    $page = $params['par1'];
    $null = $params['par2'];
    $gtabid = $params['par3'];
    $fieldid = $params['par4'];
    $ID = $params['par5'];
    $typ = $params['par6'];
    $level = $params['gformid'];
    $exclude = $params['formid'];

    if(!$level){$level = 0;}
    if(!$exclude){$exclude = array();}
    $pool = $gfield[$gtabid]["select_pool"][$fieldid];

    # SELECT / ATTRIBUTE
    if($gfield[$gtabid]["field_type"][$fieldid] == 19){$tabtyp = "LMB_ATTRIBUTE";}else{$tabtyp = "LMB_SELECT";}
    # attribute
    if($gfield[$gtabid]["data_type"][$fieldid] == 46){$selecttyp = "a";}
    # m-select ajax
    elseif($gfield[$gtabid]["data_type"][$fieldid] == 32){$selecttyp = "c";}
    # single select
    else{$selecttyp = "b";}


    // unique
    if($gfield[$gtabid]["unique"][$fieldid]){
        $selecttyp = "f";
    }

    // multilang
    $field_name = 'WERT';
    if($gfield[$gtabid]['multilang'][$fieldid] == 2){
        $field_name = 'LANG'.$session['dlanguage'].'_WERT';
    }

    // multimode from Pool
    $sqlquery = "SELECT MULTIMODE FROM {$tabtyp}_P WHERE ID = $pool";
    $rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
    if(!$rs) {$commit = 1;}
    $multimode = lmbdb_result($rs, 'MULTIMODE');

    // maxresult
    $maxresult = $session['maxresult'];

    // order
    if($gfield[$gtabid]["select_sort"][$fieldid]){
        $selsort = $tabtyp."_W.".$gfield[$gtabid]["select_sort"][$fieldid];
        $selsort = str_replace("ASC","",$selsort);
        $selsort = str_replace("DESC","",$selsort);
    }else{
        $selsort = $tabtyp."_W.$field_name";
    }

    if($multimode){
        $sqlsel = array($selsort,$tabtyp."_W.ID",$tabtyp."_W.$field_name",$tabtyp."_W.KEYWORDS",$tabtyp."_W.LEVEL,".$tabtyp."_W.HASLEVEL");
    }else{
        $sqlsel = array($selsort,$tabtyp."_W.ID",$tabtyp."_W.$field_name",$tabtyp."_W.KEYWORDS",$tabtyp."_D.ID AS PRESENT,".$tabtyp."_W.LEVEL,".$tabtyp."_W.HASLEVEL");
    }
    
    $sqlsel = implode(",",array_unique($sqlsel));

    $value = lmb_utf8_decode($value);
    if($value AND $value != "*"){
        $where = "AND (LOWER(".$tabtyp."_W.$field_name) LIKE '%".parse_db_string(lmb_strtolower($value),255)."%' OR LOWER(".$tabtyp."_W.KEYWORDS) LIKE '%".parse_db_string(lmb_strtolower($value),255)."%')";
    }

    // use Limit & Offset instead of Cursor (PDO)
    $limit = null;
    if(defined('LMB_DBFUNC_OFFSET')) {
        if(!$page OR $page < 1 ){$page = 1;}

        # ---- Zählerbeginn
        $offset = (($maxresult * $page) - $maxresult + LMB_DBFUNC_OFFSET_START);
        $limit = "LIMIT ".($session["maxresult"]+1)." OFFSET $offset";
    }else {

        if (LMB_DBFUNC_LIMIT) {
            $limit = LMB_DBFUNC_LIMIT . " " . $umgvar["resultspace"];
        }elseif (LMB_DBFUNC_ROWNO) {
            $where .= " AND " . LMB_DBFUNC_ROWNO . " <= " . $umgvar["resultspace"];
        }
    }

    # SQL extension
    if($gfield[$gtabid]["relext"][$fieldid]){
        $extension = eval($gfield[$gtabid]["relext"][$fieldid]);
        if($extension['where']){$where .= ' AND ('.$extension['where'].')';}
    }

    $sqlquery = "SELECT DISTINCT $sqlsel
	FROM
	".$tabtyp."_W LEFT JOIN ".$tabtyp."_D ON (".$tabtyp."_D.DAT_ID = $ID AND ".$tabtyp."_D.TAB_ID = $gtabid AND ".$tabtyp."_D.FIELD_ID = $fieldid AND ".$tabtyp."_D.W_ID = ".$tabtyp."_W.ID)
	WHERE
	".$tabtyp."_W.HIDE = ".LMB_DBDEF_FALSE."
	AND ".$tabtyp."_W.POOL = ".$pool
	.(in_array($value,array("#all","*")) || intval($level)>0 ? " AND ".$tabtyp."_W.LEVEL=".parse_db_int($level) : "")."
	$where
	ORDER BY $selsort
	$limit
	";

    $rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
    if(!$rs) {$commit = 1;}

	if($page and is_numeric($page)){
		$bzm = ($page - 1) * $maxresult + 1;
	}else{
		$bzm = 1;
		$page = 1;
	}
	
	if($gfield[$gtabid]["data_type"][$fieldid] == 12 /* auswahl select */){
		$onclick_b = "document.getElementById('lmbAjaxContainer').style.display='none'";
	}

	$bzm2 = 1;

    if($level){
        echo "<div id=\"{$form_name}_{$level}_dsl\">";
    }else{
        echo "<div id=\"{$form_name}_dsl\">";
    }
    echo '<table style="width: 100%; border-collapse: collapse;">';
    while(lmbdb_fetch_row($rs, $bzm) AND $bzm2 <= $maxresult) {
        $id = lmbdb_result($rs, 'ID');
        $checked = lmbdb_result($rs, 'PRESENT');
        $hasChildren = lmbdb_result($rs, 'HASLEVEL');
        $value = htmlentities(lmbdb_result($rs, $field_name), ENT_QUOTES, $umgvar['charset']);
        $keyword = htmlentities(lmbdb_result($rs, 'KEYWORDS'), ENT_QUOTES, $umgvar['charset']);
        $level = lmbdb_result($rs, 'LEVEL');
        if ($level == '0') {
            $level = '';
        }

        if($multimode){
            $checked = null;
        }

        # show check if option is selected
        $check = '';
        if ($checked) {
            $check = '<i class="lmb-icon lmb-check"></i>';
        }

        # show caret if option has children
        $caretTD = '<td></td>';
        if ($hasChildren) {
            $caret = '<i class="lmb-icon lmb-edit-caret"></i>';
            $caretTD = "<td style=\"width: 16px; text-align: right;\" onclick=\"event.stopPropagation();return dyns_11_a(event, '', '$id', '$form_name', '$fieldid', '$gtabid', '$ID', '{$gfield[$gtabid]['select_cut'][$fieldid]}','$tabtyp','e','$level');\">$caret</td>";
        }

        echo <<<EOD
            <tr id="{$form_name}_{$level}_{$id}" title="$keyword" is_active="{$checked}" class="lmbSelectLink" onclick="return dyns_11_a(event, '$value', '$id', '$form_name', '$fieldid', '$gtabid', '$ID', '{$gfield[$gtabid]['select_cut'][$fieldid]}','$tabtyp','$selecttyp','$level','$form_id');">
                <td style="width: 16px;">$check</td>
                <td>$value</td>
                $caretTD
            </tr>
EOD;

        $bzm++;
        $bzm2++;
    }

    if($bzm > $maxresult){echo "<tr><td colspan=\"2\"><hr></td></tr><tr><td></td><td>
    <i class=\"lmb-icon lmb-previous\" style=\"cursor:pointer;font-size:1.5em;\" border=\"0\" OnClick=\"var element=document.getElementsByName('".$form_name."_ds'); lmbAjax_dynsearch(null,element[0],'11_a','".$gfield[$gtabid]["form_name"][$fieldid]."',".($page-1).",0,'$gtabid','$fieldid','$ID','$typ')\"></i>
    <i class=\"lmb-icon lmb-next\" style=\"cursor:pointer;font-size:1.5em;\" border=\"0\" OnClick=\"var element=document.getElementsByName('".$form_name."_ds'); lmbAjax_dynsearch(null,element[0],'11_a','".$gfield[$gtabid]["form_name"][$fieldid]."',".($page+1).",0,'$gtabid','$fieldid','$ID','$typ')\"></i>
    </td></tr>";}

    echo '</table>';
    echo "</div>";

}




# ---- Relation-Popup in tablelist --------
function dyns_linkPopup($params){

    require_once("gtab/gtab.lib");
    require_once("gtab/gtab_type_erg.lib");
    require_once("gtab/html/gtab_erg.lib");

    global $gtab;
    global $gfield;
    global $gverkn;
    global $popc;
    global $pophist;
    global $filter;

    $gtabid = $params["gtabid"];
    $datid = $params["datid"];
    $prev_id = $params["previd"];
    $pop_choice = $params["pop_choice"];
    $ebene = $params["ebene"];
    $pophistg = explode("-",$params["pophistg"]);
    $pophist = unserialize(stripslashes($params["pophist"]));
    foreach ($pophistg as $key => $value){$pophist["gdone"][$value] = 1;}

    if(!$pophist["done"]){$pophist["done"] = array();}

    /* --- Sub-choice ------*/
    if($pop_choice){
        $pop_choice = explode("|",$pop_choice);
        $bzm = 1;
        $popc[$gtabid]["aktiv"] = 0;
        while($pop_choice[$bzm]){
            $pop_choice_ = explode(";",$pop_choice[$bzm]);
            $tmp = explode("_",$pop_choice_[0]);
            $pop_choice_[0] = $tmp[1];
            if($pop_choice_[0] AND $pop_choice_[1]){
                $popc[$pop_choice_[0]][$pop_choice_[1]] = $pop_choice_[2];
            }else{
                $popc[$pop_choice_[0]][$pop_choice_[1]] = 0;
            }
            $bzm++;
        }
    }

    if(!$gtab["groupable"][$gtabid] OR !$popc[$gtabid]){return false;}

    #---- Funktionen aufrufen für Plusverknüpfung aufsteigend -------
    if($gverkn[$gtabid]["id"]){
        $bzm1 = 0;
        foreach($gverkn[$gtabid]["id"] as $key => $value){
            if($popc[$gtabid][$gfield[$gtabid]["verkntabid"][$key]]){
                # check of alredy done
                if($value != $gtabid AND in_array($value,$pophist["done"])) continue;

                $verkn = set_verknpf($gtabid,$gfield[$gtabid]["field_id"][$key],$datid,0,0,1,0);
                #$verkn["is_ajax"] = 1;
                $filter["anzahl"][$gfield[$gtabid]["verkntabid"][$key]] = "all";
                $gresult_ = get_gresult($gfield[$gtabid]["verkntabid"][$key],0,$filter,0,$verkn);
                $filter["unhide"][$gfield[$gtabid]["verkntabid"][$key]] = $filter["unhide"][$gtabid];
                # Ausgabe
                if($gresult_[$value]["res_viewcount"] > 0){
                    echo "
					<div class=\"lmbPopupDiv\">
					<table class=\"lmbPopupGtab\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" style=\"border-collapse:collapse;\">";
                    lmbGlistHeader($gfield[$gtabid]["verkntabid"][$key],$gresult_,1);
                    lmbGlistBody($gfield[$gtabid]["verkntabid"][$key],$gresult_,1,0,0,$filter,$datid,$gtabid,$gfield[$gtabid]["field_id"][$key],1,$ebene,0);
                    echo "</table></div>";
                    $bzm1++;
                }
            }
        }
    }
}



# ---- tabulator  --------
function dyns_subheaderValue($params){
    global $gform;

    require_once("gtab/gtab.lib");
    require_once("gtab/gtab_form.lib");
    require_once("gtab/gtab_type.lib");
    require_once("gtab/gtab_type_erg.lib");

    # EXTENSIONS
    if($GLOBALS["gLmbExt"]["ext_gtab_change.inc"]){
        foreach ($GLOBALS["gLmbExt"]["ext_gtab_change.inc"] as $key => $extfile){
            require_once($extfile);
        }
    }

    $gtabid = $params["gtabid"];
    $fieldid = $params["fieldid"];
    $formid = $params["formid"];
    $form_subel = $params["form_subel"];
    $ID = $params["ID"];
    $action = $params["formaction"];

    $gresult = get_gresult($gtabid,null,null,null,0,$gform[$formid]["used_fields"],$ID);
    form_gresult($ID,$gtabid,$formid,$gresult); # need for fields of related tables

    # tabulator elements
    if($gform[$formid]["subellist"][$form_subel][$fieldid]){$subellist_a = $gform[$formid]["subellist"][$form_subel][$fieldid];}else{$subellist_a = array();}
    # all elements
    if($gform[$formid]["subellist"][$form_subel][0]){$subellist_b = $gform[$formid]["subellist"][$form_subel][0];}else{$subellist_b = array();}
    $subellist = array_merge($subellist_b,$subellist_a);

    formListElements($action,$gtabid,$ID,$gresult,$formid,$subellist,1);
}




# ----QuickSearch Preview --------
function dyns_gtabQuickSearch($params){
    global $gfield;
    global $gtab;
    global $umgvar;
    global $farbschema;
    global $db;

    require_once("gtab/gtab.lib");
    require_once("gtab/gtab_type_erg.lib");

    $gtabid = $params["gtabid"];
    $fieldid = $params["fieldid"];
    $value = $params["value"];

    $gsr[$gtabid][$fieldid][0] = $value;
    $gsr[$gtabid][$fieldid]["txt"][0] = 1;
    $gresult = get_gresult($gtabid,1,null,$gsr,null,array($gtabid=>array($fieldid)));

    if($gresult[$gtabid]["id"][0]){
        /* ------------------ Default --------------------- */
        $fname = "cftyp_".$gfield[$gtabid]["funcid"][$fieldid];

        echo "<DIV STYLE=\"background-color:".$farbschema["WEB3"].";border:1px solid #CCCCCC;padding:4px;\">";
        echo "<TABLE BORDER=\"0\" CELLPADDING=\"0\" CELLSPACING=\"0\">";
        foreach ($gresult[$gtabid]["id"] as $key => $value) {

            /* ------------------ Typefunction --------------------- */
            $retrn = $fname($key,$fieldid,$gtabid,3,$gresult,0);
            echo "<TR><TD NOWRAP STYLE=\"cursor:pointer;\" OnMouseOver=\"this.style.color='blue';\" OnMouseOut=\"this.style.color='black';\"
			OnClick=\"lmbQuickSearchAction(event,$gtabid,$fieldid,$value,'".htmljs($retrn)."');\">".$retrn."</TD></TR>";

        }
        if($gresult[$gtabid]["res_count"] > $gresult[$gtabid]["res_viewcount"]){
            echo "...<BR>";
        }

        echo "</TABLE>";
        echo "</DIV>";
    }
}

# ----Search Menu --------
function dyns_gtabSearch($params){
    global $lang;
    global $gfield;
    global $gtab;
    global $umgvar;
    global $farbschema;
    global $gsr;
    global $filter;
    global $LINK;
    global $gsnap;
    global $db;

    $gtabid = $params["gtabid"];
    $fieldid = $params["fieldid"];
    $snap_id = $filter["snapid"][$gtabid];
    $module = $params["module"];

    if($params["snap_id"]){$snap_id = $params["snap_id"];}

    require_once("gtab/gtab_register.lib");
    require_once("gtab/html/gtab_search.php");
}


# ----- replace Menu -------
function dyns_gtabReplace($params){
    global $lang;
    global $gfield;
    global $gtab;
    global $umgvar;
    global $farbschema;
    global $LINK;
    global $db;

    $gtabid = $params["gtabid"];
    $fieldid = $params["fieldid"];
    $confirm = $params["confirm"];
    $chfid = $params["grplfield"];
    $use_records = $params["use_records"];
    $grplval = $params["grplval"][$gtabid][$chfid];
    $agregate = $params["agregate"][$gtabid][$chfid];
    $params["return"] = 1;
    $filter = $GLOBALS["filter"];

    if(!$LINK[287]){return false;}

    if($confirm AND !$chfid){
        lmb_alert($lang[2678]);
        $confirm = 0;
    }

    if($confirm == 1){
        if(!$gfield[$gtabid]["perm_edit"][$chfid]){lmb_alert('permission denied!');return false;}

        // use records
        if($use_records){
            $use_records = explode(';',$use_records);
            $rcount = count($use_records);
            // use filter
        }else{
            $rcount = dyns_getRowcount($params);
        }

        echo "
		<script language=\"JavaScript\">\n
		conf = confirm('".$lang[2677].": ".$gfield[$gtabid]["spelling"][$chfid]."\\n".$lang[2676].": ".$rcount."\\n".$lang[2675]."');\n
		if(conf){\n
		limbasCollectiveReplace(null,this,2,'$gtabid');\n
		}\n
		</Script>\n";
    } elseif ($confirm == 2) {
        require_once ("gtab/gtab.lib");

        if (! $gfield[$gtabid]["perm_edit"][$chfid]) {
            lmb_alert('permission denied!');
            return false;
        }
        if ($params["verknpf"]) {
            $verkn = set_verknpf($params["verkn_tabid"], $params["verkn_fieldid"], $params["verkn_ID"], $params["verkn_add_ID"], $params["verkn_del_ID"], $params["verkn_showonly"], $params["verknpf"]);
        }

        // use records
        if($use_records){
            $use_records = explode(';',$use_records);
            foreach($use_records as $key => $val){
                $part = explode('_',$val);
                $useid[] = $part[0];
            }
            $extension['where'][0] = $gtab['table'][$gtabid].'.ID IN ('.implode(',',$useid).')';
            // use filter
        }else{
            $rcount = dyns_getRowcount($params);
            $_gsr = $GLOBALS["gsr"];
        }


        $filter["anzahl"][$gtabid] = "all";
        $onlyfield[$gtabid] = array($chfid);
        $gresult = get_gresult($gtabid, 1, $filter, $_gsr, $verkn, $onlyfield, null, $extension);

        if ($gresult[$gtabid]["id"]) {

            // agregate date
            if ($gfield[$gtabid]["parse_type"][$chfid] == 4 and ($agregate['hour'] or $agregate['minute'] or $agregate['month'] or $agregate['day'] or $agregate['year'])) {

                foreach ($gresult[$gtabid]["id"] as $key => $value) {

                    $oldst = get_stamp($gresult[$gtabid][$chfid][$key]);

                    $stmp = mktime(date('H', $oldst) + parse_db_float($agregate['hour']), date('i', $oldst) + parse_db_float($agregate['minute']), date('s', $oldst), date('n', $oldst) + parse_db_float($agregate['month']), date('j', $oldst) + parse_db_float($agregate['day']), date('Y', $oldst) + parse_db_float($agregate['year']));

                    $update["$gtabid,$chfid,$value"] = convert_stamp($stmp);
                }
            }

            // agregate numeric
            elseif ($gfield[$gtabid]["field_type"][$chfid] == 5 and (lmb_substr($grplval, 0, 1) == '+' or lmb_substr($grplval, 0, 1) == '-') and is_numeric($grplval)) {

                foreach ($gresult[$gtabid]["id"] as $key => $value) {

                    $oldval = parse_db_float($gresult[$gtabid][$chfid][$key]);

                    $update["$gtabid,$chfid,$value"] = ($oldval + parse_db_float($grplval));
                }
            }

            else {
                foreach ($gresult[$gtabid]["id"] as $key => $value) {
                    $update["$gtabid,$chfid,$value"] = $grplval;
                }
            }

            if (update_data($update)) {
                echo "
				<script language=\"JavaScript\">\n
				divclose();\n
				alert('" . count($gresult[$gtabid]["id"]) . " " . $lang[2684] . "');\n
				limbasCollectiveReplaceRefresh();\n
				</Script>\n";
                return;
            } else {
                lmb_alert($lang[56]);
            }
        }
        return;
    }

    require_once ("gtab/html/gtab_replace.php");
}


/**
 * Manage the reminder add from xml HTTP request
 *
 * @param unknown_type $params
 */
function dyns_showReminder($params){
    global $lang;
    global $farbschema;
    global $greminder;

    require_once("gtab/gtab.lib");

    $gtabid = $params["gtabid"];
    $ID = $params["ID"];
    $remid = $params["remid"];
    $add = $params["add"];
    $changeViewId = $params["changeViewId"];
    $changeId = $params["changeId"];
    $use_records = $params["use_records"];
    $category = $params["REMINDER_CATEGORY"];
    $gfrist = $params["gfrist"];
    $listmode = $params["listmode"];
    $mwidth = "";
    $defaults = $params['defaults'] ? json_decode($params['defaults'], 1) : array();

    // listmode
    if($listmode AND $use_records){
        // use filter
        if($use_records == 'all'){
            require_once("gtab/gtab.lib");
            if($params["verkn_ID"]){
                $verkn = set_verknpf($params["verkn_tabid"],$params["verkn_fieldid"],$params["verkn_ID"],null,null,$params["verkn_showonly"],1);
            }
            if($gresult = get_gresult($gtabid,1,$GLOBALS["filter"],$GLOBALS["gsr"],$verkn,'ID')){
                $use_records = $gresult[$gtabid]['id'];
            }
            // use selected rows
        }else{
            $use_records = explode(';',$use_records);
        }

        // set reminder
        foreach ($use_records as $key => $id){

            // add reminder
            if($add){
                if(lmb_addReminder($params["REMINDER_DATE_TIME"],$params["REMINDER_BEMERKUNG"],$params["gtabid"],$id,$params["REMINDER_USERGROUP"],$category,null,null,$params["REMINDER_MAIL"])){
                    $success ++;
                    $msg = $lang[2901];
                }
                // drop reminder
            }elseif($remid){
                if(lmb_dropReminder(null,$gtabid,$remid,$id)){
                    $success ++;
                    $msg = $lang[2900];
                }
            }
        }

        // singlemode
    }elseif($ID){
        // add reminder
        if($add){
            if(lmb_addReminder($params["REMINDER_DATE_TIME"],$params["REMINDER_BEMERKUNG"],$gtabid,$ID,$params["REMINDER_USERGROUP"],$category,null,null,$params["REMINDER_MAIL"])){
                $success ++;
                $msg = $lang[2901];
            }
            // drop reminder
        }elseif($remid){
            if(lmb_dropReminder($remid,$gtabid)){
                $success ++;
                $msg = $lang[2900];
            }
        }elseif($changeId){
            if(lmb_changeReminder($changeId, $params["REMINDER_DATE_TIME"], $params["REMINDER_BEMERKUNG"])){
                $success ++;
                $msg = $lang[729] . " " . $lang[2006]; // TODO own lang entry
            }
        }
    }

    if($success){
        $out = "<span style=\"color:green\">$success $msg</span><center><hr></center>";
    }

    echo "<FORM NAME=\"form_reminder\">";

    pop_top('lmbAjaxContainer',$mwidth);

    if($changeViewId AND $reminder = lmb_getReminder($gtabid,$ID,$category)) {
        $changedate = $reminder["datum"][$changeViewId];
        $changedesc = $reminder["desc"][$changeViewId];
    }

    # date time selection
    if($defaults['datetime']) {
        echo "<INPUT TYPE=\"hidden\" NAME=\"REMINDER_DATE_TIME\" VALUE=\"{$defaults['datetime']}\" MAX=16>";
    } else {
        pop_left($mwidth);
        echo "<TABLE width='100%'><TR><TD nowrap>
            &nbsp;$lang[1975] <INPUT onchange='setReminderDateTime(\"REMINDER_VALUE\",\"REMINDER_UNIT\",\"REMINDER_DATE_TIME\");' TYPE=\"TEXT\" NAME=\"REMINDER_VALUE\" STYLE=\"width:25px;\" MAX=4>
            <SELECT NAME=REMINDER_UNIT onchange='setReminderDateTime(\"REMINDER_VALUE\",\"REMINDER_UNIT\",\"REMINDER_DATE_TIME\");' >
            <OPTION VALUE=min>$lang[1980]</OPTION>
            <OPTION VALUE=hour>$lang[1981]</OPTION>
            <OPTION VALUE=day>$lang[1982]</OPTION>
            <OPTION VALUE=week>$lang[1983]</OPTION>
            <OPTION VALUE=month>$lang[296]</OPTION>
            <OPTION VALUE=year>$lang[1985]</OPTION>
            </SELECT></TD>
            <TD nowrap align='right'>$lang[1976]
            <INPUT onchange='setReminderValue(\"REMINDER_DATE_TIME\",\"REMINDER_VALUE\",\"REMINDER_UNIT\");' TYPE=TEXT NAME=\"REMINDER_DATE_TIME\" STYLE=\"width:115px;\" VALUE=\"".($changeViewId ? $changedate : local_date(1))."\" MAX=16>
            <i class=\"lmb-icon lmb-caret-right\" style=\"cursor:pointer\" id=\"CALENDER_DISPLAY\" onclick=\"document.getElementsByName('REMINDER_UNIT')[0][2].selected=true; lmb_datepicker(event,this,'REMINDER_DATE_TIME',document.getElementsByName('REMINDER_DATE_TIME')[0].value,'".dateStringToDatepicker(setDateFormat(0,1))."',10);\"></i>&nbsp;&nbsp;</TD></TR></TABLE>
            <div style=\"text-align: center;\"><hr></div>";
        pop_right();
    }

    pop_left($mwidth);
    echo "<TABLE>";

    # user group selection    
    if(!is_null($defaults['usergroups'])) {
        echo "<tr style=\"display:none;\"><td><input type=\"hidden\" ID=\"REMINDER_USERGROUP\" NAME=\"REMINDER_USERGROUP\" VALUE=\"{$defaults['usergroups']}\"></td></tr>";
    } else {
        if(!$changeViewId) {
            echo "<TR>
                <TD valign=top>$lang[2643]:</TD>
                <TD>
                	<input type=\"hidden\" ID=\"REMINDER_USERGROUP\" NAME=\"REMINDER_USERGROUP\">
                    <div class=\"fgtabchange\">
                        <TABLE cellpadding=0 cellspacing=0 width=\"100%\">
                            <TR>
                                <TD nowrap>
                                    <input type=\"text\" style=\"width:65px;\" OnKeyup=\"lmbAjax_showUserGroupsSearch(event,this.value,'".$ID."','".$gtabid."','','lmb_reminderAddUserGroup','','user','')\" OnDblClick=\"this.value='*';$(this).keyup();\">&nbsp;
                                    <i class=\"lmb-icon lmb-user\" style=\"cursor:pointer;vertical-align:text-bottom\" OnClick=\"lmbAjax_showUserGroupsSearch(event,'*','".$ID."','".$gtabid."','','lmb_reminderAddUserGroup','','user')\"></i>
                                </TD>
                                <TD align=\"right\">
                                    <input type=\"text\" style=\"width:65px;\" OnKeyup=\"lmbAjax_showUserGroupsSearch(event,this.value,'".$ID."','".$gtabid."','','lmb_reminderAddUserGroup','','group','')\" OnDblClick=\"this.value='*';$(this).keyup();\">&nbsp;
                                    <i class=\"lmb-icon lmb-group\" style=\"cursor:pointer;vertical-align:text-bottom\" OnClick=\"lmbAjax_showUserGroupsSearch(event,'*','".$ID."','".$gtabid."','','lmb_reminderAddUserGroup','','group','')\"></i>
                                </TD>
                            </TR>
                        </TABLE>
                        <div id=\"contWvUGList\" style=\"width:100%;background-color:".$farbschema["WEB8"]."\"></div>
                    </div>
                    <div id=\"lmb_reminderAddUserGroupuser\" class=\"ajax_container\" style=\"display:none;position:absolute;border:1px solid black;padding:2px;background-color:".$farbschema["WEB11"]."\"></div>
                    <div id=\"lmb_reminderAddUserGroupgroup\" class=\"ajax_container\" style=\"text-align:left;display:none;position:absolute;border:1px solid black;padding:2px;background-color:".$farbschema["WEB11"]."\"></div>
                </TD>
            </TR>";
        }
    }

    # remark
    if(!is_null($defaults['remark'])){
        echo "<tr style=\"display:none;\"><td><textarea name=\"REMINDER_BEMERKUNG\">{$defaults['remark']}</textarea></td></tr>";
    } else {
        echo "<tr><td valign=\"top\">$lang[295]:</td><td><textarea name=\"REMINDER_BEMERKUNG\" style=\"width:190px;height:20px;\" onkeyup=\"limbasResizeTextArea(this,5);\">" . ($changeViewId ? $changedesc : "") . "</textarea></td></tr>";
    }

    # mail
    if(!is_null($defaults['mail'])) {
        echo "<tr style=\"display:none;\"><td><input type=\"hidden\" name=\"REMINDER_MAIL\" value=\"{$defaults['mail']}\"></td></tr>";
    } else if(!$changeViewId){
        echo "<tr title=\"".$lang[2577]."\"><td valign=\"top\">$lang[612]:</td><td><input type=\"checkbox\" name=\"REMINDER_MAIL\"></td></tr>";
    }

    # category selection
    if(!is_null($defaults['category'])) {
        echo "<tr style=\"display:none;\"><td><input type=\"hidden\" name=\"REMINDER_CATEGORY\" value=\"{$defaults['category']}\"></td></tr>";
    } else {
        if(!$changeViewId AND $greminder[$gtabid]['name']){
            echo "<tr><td valign=\"top\">Kategorie:</td><td colspan=\"5\"><input type=\"radio\" name=\"REMINDER_CATEGORY\" value=\"0\" checked>&nbsp;default<br>";
            foreach ($greminder[$gtabid]['name'] as $rkey => $rval){
                echo "<input type=\"radio\" name=\"REMINDER_CATEGORY\" value=\"$rkey\">&nbsp;".$rval."<br>";
            }
            echo "</td></tr>";
            /*
            echo "<tr><td valign=\"top\">Kategorie:</td><td colspan=\"5\"><select name=\"REMINDER_CATEGORY\"><option>";
            foreach ($greminder[$gtabid]['name'] as $rkey => $rval){
                echo "<option value=\"$rkey\">".$rval;
            }
            echo "</select></td></tr>";
            */
        }
    }

    # separator
    if(is_null($defaults['remark']) AND is_null($defaults['mail']) AND is_null($defaults['category'])) {
        echo "<tr><td colspan=\"2\"><div style=\"text-align: center;\"><hr></div><td></tr>";
    }

    # delete all in list
    if($listmode AND $gfrist){
        echo "
			<tr><td valign=\"top\"><b>$lang[2644]:</b></td>
			<td>".$greminder[$gtabid]["name"][$gfrist]."</td>
			<td><i class=\"lmb-icon lmb-close-alt\" style=\"cursor:pointer;\" OnClick=\"limbasDivShowReminder(event,'','','$gfrist');\" Title=\"".$lang[721]."\"></i></td>
			</tr>";
        # delete single category
    }elseif(!$changeViewId AND $reminder = lmb_getReminder($gtabid,$ID,$category) AND !$defaults['hidecurrent']){
        echo "
		<tr><td valign=\"top\"><b>$lang[2644]:</b></td><td>
		<table cellpadding=2 cellspacing=0>
		";

        # sort reminder by group
        $groups = array();
        foreach ($reminder["id"] as $remkey => $remval){
            if(!$groups[$reminder['name'][$remkey]]) {
                $groups[$reminder['name'][$remkey]] = array();
            }
            $groups[$reminder['name'][$remkey]][] = $remkey;
        }

        # display reminder sorted into groups
        foreach ($groups as $groupLangId => $remKeyArr) {

            # display group name
            $groupname = $groupLangId ? $lang[$groupLangId] : "default";
            echo "<tr><td><i>" . $groupname . "</i></td><td></td><td></td></tr>";

            # display reminders
            foreach($remKeyArr as $notused => $remkey) {
                $remval = $reminder["id"][$remkey];
                echo "<tr>
				<td></td>
				<td><a href=\"#\" title=\"".$reminder["desc"][$remkey]."\" onclick=\"limbasDivShowReminder(event,'','','','$remval');\">".lmb_substr($reminder["datum"][$remkey],0,16)."</a></td>
				<td><i class=\"lmb-icon lmb-close-alt\" style=\"cursor:pointer;\" OnClick=\"limbasDivShowReminder(event,'','','$remval');\" Title=\"".$lang[721]."\"></i></td>";
                if($reminder["from"][$remkey]){echo "<br><i>".$userdat["bezeichnung"][$reminder["from"][$remkey]]."</i>";}
                echo "</tr>";
            }
        }

        echo "</table></td></tr>";
    }

    echo "</TABLE>
		$out";
    pop_right();


    pop_left($mwidth);
    echo "<CENTER><input type=button value=\"" . ($changeViewId ? $lang[2443] : $lang[1038]) . "\" onclick=\"" . ($changeViewId ? "limbasDivShowReminder(event,'','','','','$changeViewId');" : "limbasDivShowReminder(event,'','1');") . "\">&nbsp;<input type=button value=$lang[844] onClick='activ_menu=0;divclose();'></CENTER>";
    echo "<INPUT TYPE=HIDDEN NAME=WIEDERVORLAGENAME ID=WIEDERVORLAGEID>";
    pop_right();

    pop_left($mwidth);
    echo "&nbsp;";
    pop_right();

    pop_bottom($mwidth);
    echo "</FORM>";
}

/**
 * show multilang values for single field
 * @param unknown $params
 */
function dyns_gmultilang($params){
    global $db;
    global $gtab;
    global $gfield;
    global $umgvar;

    $gtabid = $params['gtabid'];
    $fieldid = $params['field_id'];
    $ID = $params['ID'];

    // language definition
    $sqlquery = "SELECT LANGUAGE_ID,LANGUAGE FROM LMB_LANG GROUP BY LANGUAGE_ID,LANGUAGE";
    $rs = lmbdb_exec($db, $sqlquery) or errorhandle(lmbdb_errormsg($db), $sqlquery, $action, __FILE__, __LINE__);
    while(lmbdb_fetch_row($rs)){
        $language[lmbdb_result($rs,'LANGUAGE_ID')] = lmbdb_result($rs,'LANGUAGE');
    }

    if($gfield[$gtabid]['multilang'][$fieldid]){
        $multi_language = $umgvar['multi_language'];

        $fieldname[$umgvar['default_language']] = $gfield[$gtabid]['rawfield_name'][$fieldid];
        foreach ($multi_language as $lkey => $langID) {
            // prefix
            $fieldname[$langID] = 'LANG' . $langID . '_'.$gfield[$gtabid]['rawfield_name'][$fieldid];
        }

        // get field values
        $sqlquery = "SELECT ".implode(',',$fieldname)." FROM ".$gtab['table'][$gtabid]." WHERE ID = $ID";
        $rs = lmbdb_exec($db, $sqlquery) or errorhandle(lmbdb_errormsg($db), $sqlquery, $action, __FILE__, __LINE__);
        if(lmbdb_fetch_row($rs)){
            echo '<table width="100%">';
            foreach ($fieldname as $lkey => $field) {
                echo '<tr><td><b>'.$language[$lkey].'</b></td><td><div class="gtabchange">'.lmbdb_result($rs,$field).'</div></td></tr>';
            }
            echo '</table>';

        }

    }

}



# ---- EXLORER--------

# ---------- Import from path ---------------------
function dyns_fileUploadFromPath($params){
    global $LINK;

    require_once("gtab/gtab.lib");
    require_once("extra/explorer/metadata.lib");
    require_once("extra/explorer/filestructure.lib");

    if($params['path'] AND is_numeric($params['LID']) AND $LINK[297]){
        upload_fromPath($params['LID'],$params['path'],$params['type']);
    }


}

# ---- Explorer main content --------
function dyns_fileMainContent($params){
    global $gfile;
    global $filestruct;
    global $ffilter;

    foreach ($params as $k => $v){
        global ${$k};
        ${$k} = $v;
    }

    require_once("gtab/gtab.lib");
    require_once("extra/explorer/metadata.lib");
    require_once("extra/explorer/filestructure.lib");
    require_once("extra/explorer/explorer_main.lib");
    require_once("extra/explorer/explorer_main.dao");

    explMainContent($ID,$LID,$MID,$fid,$typ,$level,$file_url,$ffile,$ffilter);

}


# ---- Explorer Upload --------
function dyns_fileUpload($params){
    global $LINK;

    $LID = $params['LID'];
    $dublicate = $params['dublicate'];
    $f_tabid = $params['gtabid'];
    $f_fieldid = $params['fieldid'];
    $f_datid = $params['ID'];
    $file_archiv = $params['file_archiv'];

    require_once("extra/explorer/filestructure.lib");
    require_once("extra/explorer/explorer_upload.php");
}



# ---- Explorer Download --------
function dyns_fileDownload($params){
    $requ = "extra/explorer/filestructure.lib";
    require_once($requ);
    $ID = $params["FID"];
    if($link = file_download($ID)){
        echo $link["url"];
    }
}

# ---- Explorer Convert --------
function dyns_fileConvertToHTML($params){
    require_once("extra/explorer/filestructure.lib");

    $ID = $params["FID"];
    if($params["searchwords"]){$searchwords = explode(" ",$params["searchwords"]);}
    if($link = preview_archive(array($ID),4,$searchwords)){
        echo $link[0];
    }

}

/**
 * Explorer view contextmenu for handling dublicates
 *
 */
function print_dublicateMenu($filelist,$fp=null){
    global $lang;
    global $gtab;


    echo "<form name=\"form_dublUpload\" ID=\"limbasDivMenuVersioning\">";

    pop_top(0,250);
    pop_left(250);
    echo "<table style=\"background-color:\" border=0 align=\"center\" width=\"90%\" cellpadding=0 cellspacing=0>
	<tr><td align=\"center\" colspan=\"5\"><hr></td></tr>";
	$sum = count($filelist["id"]);
	$i = 0;
	$bzm = 0;
	foreach ($filelist["id"] as $key => $value){
		$bzm++; $i++;
		if($value){
			echo "<tr><td align=\"center\" colspan=\"5\"><B>".$filelist["name"][$key]."</B></TD></TR>";
			echo "<tr><td>&nbsp;".$lang[2332]."</td><td><input type=\"radio\" name=\"fileVersionType_$bzm\" value=\"skip\" OnChange=\"updateDublicateUploads($sum,0,$bzm)\" CHECKED></td><td style=\"width:30px;\">&nbsp;</td>";
			echo "<td>&nbsp;".$lang[2210]."</td><td><input type=\"radio\" name=\"fileVersionType_$bzm\" value=\"rename\" OnChange=\"updateDublicateUploads($sum,1,$bzm)\"></td></tr>";
			echo "</tr><tr><td>&nbsp;".$lang[1002]."</td><td><input type=\"radio\" name=\"fileVersionType_$bzm\" value=\"overwrite\" OnChange=\"updateDublicateUploads($sum,2,$bzm)\"></td><td style=\"width:30px;\">&nbsp;</td>";
			if($gtab["versioning"][$gtab["argresult_id"]["LDMS_FILES"]] AND $filelist["typ"][$key] != "f"){$disabled = "";}else{$disabled = "disabled";}
			echo "<td>&nbsp;".$lang[2132]."</td><td><input type=\"radio\" $disabled name=\"fileVersionType_$bzm\" value=\"versioning\" OnChange=\"updateDublicateUploads($sum,3,$bzm)\"></td><td style=\"width:30px;\">&nbsp;</td>";
			echo "<tr><td align=\"center\" colspan=\"5\"><hr></td></tr>";
			echo "<tr style=\"display:none;\" id=\"versioning_subj_$bzm\"><td colspan=\"5\"><textarea style=\"width:100%;height:30px;\" name=\"fileVersionSubj_$bzm\" maxlength=180></textarea></td>";
			echo "<input type=\"hidden\" id=\"fileVersionId_$bzm\" value=\"$value\">";
			break;
		}
	}
	if(count($filelist["typ"]) > 1){echo "<tr><td align=\"left\" colspan=\"4\">".$lang[2333]." (".count($filelist["typ"]).")</td><td><input type=\"checkbox\" id=\"forallDublicateUploads\" CHECKED OnClick=\"if(this.checked){document.getElementById('moreDublicateUploads').style.display='none';}else{document.getElementById('moreDublicateUploads').style.display='';}\"></td></tr>";}
	echo "</table>";

    echo "<table id=\"moreDublicateUploads\" align=\"center\" width=\"90%\" style=\"display:none\" cellpadding=0 cellspacing=0>
	<tr><td align=\"center\" colspan=\"5\"><hr></td></tr>";
	$bzm0 = 1;
	foreach ($filelist["id"] as $key => $value){
		if($bzm0 <= $i){$bzm0++;continue;}
		$bzm++; $bzm0++;
		if($value){
			echo "<tr><td align=\"center\" colspan=\"5\"><B>".$filelist["name"][$key]."</B></TD></TR>";
			echo "<tr><td>&nbsp;".$lang[2332]."</td><td><input type=\"radio\" name=\"fileVersionType_$bzm\" value=\"skip\" CHECKED><td style=\"width:30px;\">&nbsp;</td>";
			echo "<td>&nbsp;".$lang[2210]."</td><td><input type=\"radio\" name=\"fileVersionType_$bzm\" value=\"rename\" OnClick=\"document.getElementById('versioning_subj_$bzm').style.display='none';\"></td></tr>";
			if($filelist["vid"][$key] > 1){
				echo "</tr><tr><td>&nbsp;".$lang[1002]."</td><td><input type=\"radio\" name=\"fileVersionType_$bzm\" value=\"overwrite\"></td><td style=\"width:30px;\">&nbsp;</td>";
			}else{
				echo "</tr><tr><td>&nbsp;".$lang[1002]."</td><td><input type=\"radio\" name=\"fileVersionType_$bzm\" value=\"overwrite\" OnClick=\"document.getElementById('versioning_subj_$bzm').style.display='none';\"></td><td style=\"width:30px;\">&nbsp;</td>";
			}
			if($gtab["versioning"][$gtab["argresult_id"]["LDMS_FILES"]] AND $filelist["typ"][$key] != "f"){$disabled = "";}else{$disabled = "disabled";}
			echo "<td>&nbsp;".$lang[2132]."</td><td><input type=\"radio\" $disabled name=\"fileVersionType_$bzm\" value=\"versioning\" OnClick=\"document.getElementById('versioning_subj_$bzm').style.display='';\"></td><td style=\"width:30px;\">&nbsp;</td>";
			echo "<tr><td align=\"center\" colspan=\"5\"><hr></td></tr>";
			echo "<tr style=\"display:none;\" id=\"versioning_subj_$bzm\"><td colspan=\"5\"><textarea style=\"width:100%;height:30px;\" name=\"fileVersionSubj_$bzm\" maxlength=180></textarea></td>";
			echo "<input type=\"hidden\" id=\"fileVersionId_$bzm\" value=\"$value\">";
		}
	}
	echo "</table>";

    echo "<table align=\"center\" width=\"90%\"><tr><td align=\"center\">&nbsp;<input type=\"button\" value=\"OK\" OnClick=\"selectDublicateUploads($sum)\">&nbsp;<input type=\"button\" value=\"abbrechen\" OnClick=\"$('#dublicateCheckLayer').dialog('close');\"></td></tr></table>";

    pop_right(250);
    pop_bottom(250);
    echo "<form>";
}

# ---- Explorer save Copy/Paste event for Symbols --------
function dyns_saveCopyPasteEvent($params){
    $GLOBALS["ffilter"]["copyContext"] = 1;
}


# ---- Explorer Pre-Check / Fileupload --------
function dyns_fileUploadCheck($params){
    global $session;
    global $gmimetypes;
    global $lang;
    global $filestruct;
    global $externalStorage;

    require_once("extra/explorer/filestructure.lib");
    require_once("gtab/gtab.lib");

    $files = explode(";",$params["name"]);
    $size = explode(";",$params["size"]);

    foreach ($files as $key => $filename){
        # check for mimetype
        $ext = lmb_strtolower(trim(lmb_substr($filename,lmb_strrpos($filename,'.')+1,4)));
        if(!in_array($ext,$gmimetypes["ext"])){
            lmb_alert($lang[133]."\\n- ".$filename." (.".$ext.")");
            # check for uploadsize
        }elseif(file_size_convert(ini_get('post_max_size')) < $size[$key] OR file_size_convert(ini_get('upload_max_filesize')) < $size[$key] OR $session["uploadsize"] < $size[$key]){
            $maxs = array(ini_get('post_max_size'), file_size_convert(ini_get('upload_max_filesize')), $session["uploadsize"]);
            sort($maxs);
            lmb_alert($lang[716]." ".file_size($maxs[0])."\\n- ".$filename." (".file_size($size[$key]).")");
            # check for dublicates
        }else{
            if($dublicateFile = check_duplicateFile($filename,$params["level"],$params["ID"])){
                $existingFile["id"][] = $dublicateFile["id"];
                $existingFile["name"][] = $dublicateFile["name"];
                $existingFile["vid"][] = $dublicateFile["vid"];
                $existingFile["typ"][] = "d";
            }else{
                $existingFile["name"][] = null;
                $existingFile["id"][] = null;
                $existingFile["vid"][] = null;
            }
        }
    }

    $result = array();

    # default upload url
    $result['uploadUrl'] = 'main_dyns.php?actid=fileUpload';

    # upload to external storage
    if ($storageID = $filestruct['storageID'][$params['level']]) {
        lmbGetExternalStorageConfig();
        if ($uploadUrl = $externalStorage['externalAccessUrl'][$storageID]) {
            $result['uploadUrl'] = $uploadUrl . 'upload.php';
            if ($authToken = lmbGenerateAuthToken(count($files))) {
                $result['authToken'] = $authToken;
            } else {
                return false;
            }
        }
    }

    if($existingFile['typ']){
        ob_start();
        print_dublicateMenu($existingFile, $params['fp']);
        $html = ob_get_clean();
        $result['status'] = 'confirmDuplicates';
        $result['html'] = lmb_utf8_encode($html);
	} else {
        $result['status'] = 'upload';
    }

	$GLOBALS["noencode"] = 1;

	if($result){
		echo json_encode($result);
	}else{
		echo "false";
	}
}

# ---- Explorer Pre-Check / paste file --------
function dyns_filePasteCheck($params){
    global $db;

    require_once("extra/explorer/filestructure.lib");
    # ----------- Dateien -------------
    $filelist = explode(";",$params["filelist"]);

	foreach ($filelist as $key => $value){
		$typ = lmb_substr($value,0,1);
		$fid = lmb_substr($value,1,20);
		# file
		if($typ == "d"){
			$sqlquery = "SELECT ID,NAME,VID FROM LDMS_FILES WHERE LEVEL = ".$params["level"]." AND VACT = ".LMB_DBDEF_TRUE." AND NAME = (SELECT NAME FROM LDMS_FILES WHERE ID = ".$fid.")";
			$rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
			if(lmbdb_fetch_row($rs)){
				$existingFile["name"][] = lmbdb_result($rs, "NAME");
				$existingFile["id"][] = lmbdb_result($rs, "ID");
				$existingFile["vid"][] = lmbdb_result($rs, "VID");
				$existingFile["typ"][] = "d";
			}else{
				$existingFile["name"][] = null;
				$existingFile["id"][] = null;
				$existingFile["vid"][] = null;
			}
		# folder
		}elseif($typ == "f"){
			$sqlquery = "SELECT ID,LEVEL,NAME FROM LDMS_STRUCTURE WHERE LEVEL = ".$params["level"]." AND NAME = (SELECT NAME FROM LDMS_STRUCTURE WHERE ID = ".$fid.")";
			$rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
			if(lmbdb_fetch_row($rs)){
				$existingFile["name"][] = lmbdb_result($rs, "NAME");
				$existingFile["id"][] = lmbdb_result($rs, "ID");
				$existingFile["typ"][] = "f";
			}else{
				$existingFile["name"][] = null;
				$existingFile["id"][] = null;
				$existingFile["vid"][] = null;
			}
		}
	}
	if($existingFile["typ"]){
		print_dublicateMenu($existingFile);
	}else{
		echo "direct-".$params["filelist"];
	}

}

function dyns_copy_file($params)
{

    global $file;
    require_once("extra/explorer/filestructure.lib");
    require_once("extra/explorer/metadata.lib");

    $copyover = $params["copy_over"];
    if(!$copyover){$copyover = "rename";}

    $dublicate["type"][0] = $copyover;

    get_filestructure(0);

    $file_["file"] = $file;$file_["file_name"] = $params["filename"];$file_["file_type"] = 0;$file_["file_archiv"] = 0;
    if($ufileId = upload($file_,$params["LID"],array("datid" => 0,"gtabid" => 0,"fieldid" => 0),0,$dublicate))
    {
        if($params["metadata"] AND $ufileId)
        {
            foreach ($params["metadata"] as $key => $value)
            {
                $metaForUFile[$key] = $value;
            }
            putmeta_to_db($ufileId,$metaForUFile);
        }
        echo "<BODY><DONE></BODY>";
    }
    elseif(!$ufileId)
    {
        echo "<BODY><ERROR VALUE=\"NO FILE UPLOADED\" PARAMETERS=\"";
        echo $file[0].",".$params["filename"][0].",0,0,".$params["LID"].",0,0,0,".$params["typ"].",0,$copyOver";
        echo "\"></BODY>";
    }
}


/**
 * Explorer Metadaten popupinfo
 *
 * @param unknown_type $value
 * @param unknown_type $null
 * @param unknown_type $file_id
 */
function dyns_fileMetaInfo($params){
    global $farbschema;
    global $lang;
    global $db;
    global $gfile;
    global $gfield;
    global $gtab;
    global $userdat;

    require_once("gtab/gtab_type_erg.lib");
    require_once("gtab/gtab.lib");

    $value = $params["value"];
    $file_id = $params["par1"];

    if($file_id){

        # Feldschleife onlyfield
        if($gfile['id']){
            foreach ($gfile['id'] as $key1 => $val1){
                if($gfile['name'][$key1] AND $gfile['field_type'][$key1] != 100){
                    $onlyfield[$gfile['tabid'][$key1]][] = $gfile['fid'][$key1];
                }
            }
        }


		$gresult = get_gresult($gtab["argresult_id"]["LDMS_FILES"],1,null,null,null,$onlyfield,$file_id);
		$strEcho = "";
		$boolValue = false;
		$strMetaEcho = "";
		$boolMetaValue = false;
		echo "<TABLE STYLE=\"background-color:{$farbschema['WEB11']};border:1px solid #CCCCCC;padding:4px;width:100%\">";
		foreach ($gfile['id'] as $key => $val){
			if(!$gfile["virt"][$key]){
				if($gfile["tabid"][$key] != $prev_tabid){
					if($boolMetaValue){
						echo $strMetaEcho;
						if($boolValue)
						echo $strEcho;
						$strMetaEcho = "";
						$strEcho = "";
					}
					$strMetaEcho =  "<TR><TD colspan=\"2\" bgcolor=\"".$farbschema["WEB10"]."\"><B>".$gtab["desc"][$gfile["tabid"][$key]]."</B></TD></TR>";
					$boolMetaValue=false;
				}
				if($gfile['field_type'][$key] == 100){
					if($boolValue){$strMetaEcho = $strMetaEcho . $strEcho; $strEcho = "";}
					$strEcho =   "<TR><TD colspan=\"2\" title=\"".$gfile['desc'][$key]."\" bgcolor=\"".$farbschema["WEB11"]."\"><B>".$gfile['title'][$key]."</B></TD></TR>";
					$boolValue = false;
				}else{

                    $gtabid	= $gfile['tabid'][$key];
                    $fid = $gfile['fid'][$key];
                    #$gresult[$gtabid][$fid][0] = lmbdb_result($rs,$gfile['name'][$key]);
                    #$gresult[$gtabid][$fid][0] = lmbdb_result($rs,$gfile['name'][$key]);

                    /* ------------------ Default --------------------- */
                    $fname = "cftyp_".$gfield[$gtabid]["funcid"][$fid];
                    /* ------------------ Extension --------------------- */
                    if($gfield[$gtabid]["ext_type"][$fid]){
                        if(function_exists("lmbc_".$gfield[$gtabid]["ext_type"][$fid])){
                            $fname = "lmbc_".$gfield[$gtabid]["ext_type"][$fid];
                        }
                    }

                    /* ------------------ Typefunction --------------------- */
                    $retrn = $fname(0,$fid,$gtabid,4,$gresult,0);
                    /* ------------------ /Typefunction -------------------- */

                    if($retrn){
                        $strEcho = $strEcho . "<TR TITLE=\"".$gfile['desc'][$key]."\"><TD STYLE=\"cursor:help;width:30%\" VALIGN=\"TOP\">".$gfile['title'][$key].": </TD><TD STYLE=\"width:70%\">".$retrn."</TD></TR>\n";
                        $boolValue = true;
                        $boolMetaValue = true;
                    }

                    $prev_tabid = $gfile["tabid"][$key];
                }
            }
        }
        if($boolMetaValue) echo $strMetaEcho;
        if($boolValue)
            echo $strEcho;
        echo "</TABLE>";


    }
}

/**
 * Explorer Herkunft popupinfo
 *
 * @param unknown_type $value
 * @param unknown_type $null
 * @param unknown_type $file_id
 */
function dyns_fileSourceInfo($params){
    global $farbschema;
    global $lang;
    global $db;
    global $gtab;
    global $gfield;
    global $gverkn;

    #$value = $params["value"];
    $ID = $params["par1"];

	if($ID){
		foreach($gtab["table"] as $key0 => $value0){
			if($gverkn[$key0]["id"]){
				foreach($gverkn[$key0]["id"] as $key => $value){
					if($gtab["typ"][$value] == 3 AND $gfield[$key0]["verkntabletype"][$key] == 1){
						if($gfield[$key0]["field_name"][$gfield[$key0]["mainfield"]]){
							$mainfield = $gfield[$key0]["field_name"][$gfield[$key0]["mainfield"]];
						}else{
							$mainfield = $gfield[$key0]["field_name"][$key];
						}
						if($gtab["table"][$key0] AND $gfield[$key0]["md5tab"][$key] AND $gtab["table"][$key0] AND $mainfield){
							$sqlquery = "SELECT DISTINCT ".$gtab["table"][$key0].".ID,".$gtab["table"][$key0].".".$mainfield.",".$gfield[$key0]["md5tab"][$key].".LID FROM ".$gtab["table"][$key0].",".$gfield[$key0]["md5tab"][$key].",".$gtab["table"][$value]." WHERE ".$gtab["table"][$key0].".ID = ".$gfield[$key0]["md5tab"][$key].".ID AND ".$gfield[$key0]["md5tab"][$key].".VERKN_ID = ".$gtab["table"][$value].".ID AND ".$gtab["table"][$value].".ID = $ID";
							$rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
							if(!$rs) {$commit = 1;}
							while(lmbdb_fetch_row($rs)){
								# delete relation
								if($droprelation[0] == $key0 AND $droprelation[1] == $key AND $droprelation[2] == lmbdb_result($rs, "ID")){
									require_once("gtab/gtab.lib");
									$verkn = set_verknpf($droprelation[0],$droprelation[1],$droprelation[2],0,$ID,0,0);
									if($verkn AND $verkn["typ"] AND $verkn["id"] AND $verkn["del_id"]){
										set_joins($value,$verkn);
									}
								}else{
									$forigin[$key0][$key]["value"][] = lmbdb_result($rs, $mainfield);
									$forigin[$key0][$key]["id"][] = lmbdb_result($rs, "ID");
									$forigin[$key0][$key]["field"] = $gfield[$key0]["beschreibung"][$key];
									$forigin[$key0][$key]["folder"][] = $filestruct["name"][lmbdb_result($rs, "LID")];
								}
							}
						}
					}
				}
			}
		}
	}


	# Verknüpfungen
	if($forigin){
		echo "<TABLE STYLE=\"background-color:{$farbschema['WEB11']};border:1px solid #CCCCCC;padding:4px;width:100%\">";
		foreach($forigin as $key => $value){
			foreach($value as $key1 => $value1){
				echo "<tr bgcolor=\"".$farbschema["WEB10"]."\"><td COLSPAN=\"5\"><b STYLE=\"color:{$farbschema['WEB4']}\">".$gtab["desc"][$key]."&nbsp;&nbsp;(".$forigin[$key][$key1]["field"].")</b></td></tr>";
				foreach($forigin[$key][$key1]["id"] as $key2 => $value2){
					echo "<tr>
					<td style=\"width:20px;\"></td><td OnClick=\"open_tab('".$key."','".$forigin[$key][$key1]["id"][$key2]."')\" class=\"link\">".$forigin[$key][$key1]["id"][$key2]."</td>
					<td OnClick=\"open_tab('".$key."','".$forigin[$key][$key1]["id"][$key2]."')\" class=\"link\">".$forigin[$key][$key1]["value"][$key2]."</td>
					<td>".$forigin[$key][$key1]["folder"][$key2]."</td>";
                    if($gfield[$key]["perm_edit"][$key1]){echo "<td class=\"link\"><i class=\"lmb-icon lmb-trash\" border=0 OnClick=\"open_detail($ID,'origin','&drop_relation=".$key."_".$key1."_".$forigin[$key][$key1]["id"][$key2]."')\"></i></td>";}
                    echo "</tr>";
                }
            }
        }
        echo "</TABLE>";
    }

}

/**
 * Explorer versioning popupinfo
 *
 * @param unknown_type $params
 */
function dyns_fileVersioningInfo($params) {
    global $farbschema;
    global $lang;
    global $db;
    global $userdat;

    $value = $params["value"];
    $file_id = $params["par1"];

    if ($file_id) {
        
        echo "<TABLE STYLE=\"background-color:{$farbschema['WEB11']};border:1px solid #CCCCCC;padding:4px;width:100%\">";
        
        // ---------------- Versionen -----------------
        $sqlquery = '
			SELECT
				B.ID,
				B.NAME,
				B.VDESC,
				B.SIZE,
				B.ERSTUSER,
				B.ERSTDATUM
			FROM
				LDMS_FILES A
				JOIN LDMS_FILES B ON A.vpid = B.vpid
				WHERE A.id = ' . parse_db_int($file_id) . ' ORDER BY ERSTDATUM DESC';
        $rs = lmbdb_exec($db, $sqlquery) or errorhandle(lmbdb_errormsg($db), $sqlquery, $action, __FILE__, __LINE__);
        if (! $rs) {
            $commit = 1;
        }
        $bzm = 1;
        while (lmbdb_fetch_row($rs)) {
            $vid = lmbdb_result($rs, "ID");
            $vfile["id"][] = $vid;
            $vfile["erstuser"][] = $userdat['vorname'][lmbdb_result($rs, "ERSTUSER")] . " " . $userdat['name'][lmbdb_result($rs, "ERSTUSER")];
            $vfile["erstdatum"][] = get_date(lmbdb_result($rs, "ERSTDATUM"), 2);
            $vfile["size"][] = lmbdb_result($rs, "SIZE");
            $vfile["vnote"][] = lmbdb_result($rs, "VDESC");
            $vfile["nr"][] = $bzm;
            $prev = $vid;
            $bzm ++;
        }
        if ($bzm <= 2) {
            $vfile["count"] = 0;
        } else {
            $vfile["count"] = $bzm - 1;
        }
        rsort($vfile["nr"]);
        $maxvid = $bzm-1;

        $bzm = 1;
        foreach($vfile['id'] as $key => $vid){
            if($bzm == 1){$fstyle = "color:black;";$fdesc = " ($lang[2018])";}else{$fstyle = "color:grey;";$fdesc = "";}
            echo "<tr><td>";
            if ($maxvid != $bzm) {
                echo "<i class=\"lmb-icon lmb-code\" OnClick=\"limbasFileVersionDiff(this,$vid,".$vfile['id'][$key+1].",1)\" STYLE=\"cursor:pointer;\"></i>";
            }

            echo "</td><td><A HREF=\"main.php?action=download&ID=$vid\" TARGET=\"new\" STYLE=\"$fstyle\">Version " . ($vfile['nr'][$key]) . $fdesc . "</A></td><td nowrap>" . file_size($vfile["size"][$key]) . "</td><td nowrap>" . $vfile["erstuser"][$key] . "</td><td nowrap>" . $vfile["erstdatum"][$key]  . "</td></tr>";
            if($vfile["vnote"][$key]){
                echo "<TR><td></td><td colspan=\"3\"><i>" . $vfile["vnote"][$key] . "</i></td></TR>";
            }

            $bzm ++;
        }

        echo "</TABLE>";
    }
}

/**
 * Explorer dublicates popupinfo
 *
 * @param unknown_type $params
 */
function dyns_fileDublicateInfo($params){
    global $farbschema;
    global $db;
    global $userdat;
    global $filestruct;

	$value = $params["value"];
	$file_id = $params["par1"];
	$file_md5 = $params["par2"];
	
	require_once("extra/explorer/filestructure.lib");
	get_filestructure();
	
	echo "<TABLE STYLE=\"background-color:{$farbschema['WEB11']};border:1px solid #CCCCCC;padding:4px;width:100%\">";
	
	$sqlquery = "SELECT ID,LEVEL,NAME,SIZE,ERSTUSER,ERSTDATUM FROM LDMS_FILES WHERE MD5 = '".parse_db_string($file_md5,50)."' AND ID != ".parse_db_int($file_id,18);
	$rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
	if(!$rs) {$commit = 1;}
	while(lmbdb_fetch_row($rs)) {
		if($filestruct["id"][lmbdb_result($rs,"LEVEL")]){
			echo "<tr><td nowrap><A HREF=\"main.php?&action=download&ID=".lmbdb_result($rs,"ID")."\" TARGET=\"new\">".lmbdb_result($rs,"NAME")."</A></td><td nowrap>".file_size(lmbdb_result($rs,"SIZE"))."</td><td nowrap>".$userdat["vorname"][lmbdb_result($rs,"ERSTUSER")]." ".$userdat["name"][lmbdb_result($rs,"ERSTUSER")]."</td><td nowrap>".get_date(lmbdb_result($rs,"ERSTDATUM"),1)."</td></tr>";
			echo "<TR><td colspan=\"4\" style=\"overflow:hidden;\"><div style=\"overflow:hidden;width:100%;color:blue;cursor:pointer;\" OnClick=\"document.form1.LID.value='".lmbdb_result($rs, "LEVEL")."';LmEx_send_form(1)\"><I>"."/".set_url(lmbdb_result($rs, "LEVEL"),0)."</I></A></div></td></TR>";
		}
	}
	
	echo "</TABLE>";

}

/**
 * Explorer OCR popupinfo
 *
 * @param unknown_type $params
 */
function dyns_fileOCRInfo($params){
    global $farbschema;
    global $lang;
    global $db;
    global $userdat;
    global $umgvar;
    global $gmimetypes;

    if(!$umgvar["ocr_enable"]){echo "no ocr engine found!";return;}

    $value = $params["value"];
    $file_id = $params["par1"];
    $restore = $params["par2"];

    if($file_id){

        # restore original file
        if($restore){
            require_once("extra/explorer/explorer_ocr.lib");
            if(restoreOCR($file_id)){
                echo "<tr><td nowrap>... original file restored!</td></tr>";
            }
        }

		echo "<TABLE STYLE=\"background-color:{$farbschema['WEB11']};border:1px solid #CCCCCC;padding:4px;width:100%\">";
		$sqlquery = "SELECT ID,OCR,OCRD,OCRT,OCRS,NAME,SECNAME,MIMETYPE FROM LDMS_FILES WHERE ID = ".$file_id." AND DEL = ".LMB_DBDEF_FALSE." ORDER BY ERSTDATUM ASC";
		$rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
		if(!$rs) {$commit = 1;}
		if(lmbdb_fetch_row($rs)){
			if(lmbdb_result($rs,"OCR")){
				echo "<tr><td nowrap>OCR Date: </td><td>". get_date(lmbdb_result($rs,"OCRD"),2)."</td></tr>";
				echo "<tr><td nowrap>OCR Time: </td><td>".round(lmbdb_result($rs,"OCRT"),2)." sec.</td></tr>";

                if(lmbdb_result($rs,"OCRS")){
                    $sqlquery1 = "SELECT ID,NAME FROM LDMS_FILES WHERE ID = ".lmbdb_result($rs,"OCRS");
                    $rs1 = lmbdb_exec($db,$sqlquery1) or errorhandle(lmbdb_errormsg($db),$sqlquery1,$action,__FILE__,__LINE__);
                    if(!$rs1) {$commit = 1;}
                    echo "<TR><td>OCR Source</td><td><A HREF=\"main.php?action=download&ID=".lmbdb_result($rs1,"ID")."\" TARGET=\"new\">".lmbdb_result($rs1,"NAME")."</A></td></TR>";
                }

                if($umgvar["ocr_save_copy"]){
                    $fname = lmbdb_result($rs,"SECNAME").".".$gmimetypes["ext"][lmbdb_result($rs, "MIMETYPE")];
                    $fpath = $umgvar["ocr_save_copy"]."/".$fname;
                    if(file_exists($fpath)){
                        #echo "<tr><td nowrap>OCR SaveCopy:</td><td><A HREF=\"main.php?action=download&ID=".lmbdb_result($rs,"ID")."\" TARGET=\"new\">".lmbdb_result($rs,"NAME")."</A></td></tr>";
                        echo "<tr><td nowrap>restore original:</td><td><i class=\"lmb-icon lmb-refresh\" align=\"absbottom\" OnClick=\"LmEx_dynsearchGet(event,'files_meta_$file_id','fileOCRInfo','".$file_id."','restore')\" style=\"cursor:pointer\"></i></td></tr>";
                    }
                }

            }
        }
        echo "</TABLE>";
    }
}

/**
 * Explorer Indize popupinfo
 *
 * @param unknown_type $params
 */
function dyns_fileIndizeInfo($params){
    global $farbschema;
    global $lang;
    global $db;
    global $userdat;

    $value = $params["value"];
    $file_id = $params["par1"];

	if($file_id){
		echo "<TABLE STYLE=\"background-color:{$farbschema['WEB11']};border:1px solid #CCCCCC;padding:4px;width:100%\">";
		$sqlquery = "SELECT ID,INDD,INDT,INDC FROM LDMS_FILES WHERE ID = ".$file_id." AND DEL = ".LMB_DBDEF_FALSE." ORDER BY ERSTDATUM ASC";
		$rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
		if(!$rs) {$commit = 1;}
		if(lmbdb_fetch_row($rs)){
			echo "<tr><td nowrap>Indize Date: </td><td>". get_date(lmbdb_result($rs,"INDD"),2)."</td></tr>";
			echo "<tr><td nowrap>Indize Time: </td><td>".round(lmbdb_result($rs,"INDT"),2)." sec.</td></tr>";
			echo "<tr><td nowrap>Indize Count: </td><td>".lmbdb_result($rs,"INDC")."</td></tr>";
		}
		echo "</TABLE>";
	}
}


/**
 * filew-explorer result count of rows
 *
 * @param unknown_type $params
 */
function dyns_getFileRowCount($params){
    global $db;

    require_once("gtab/gtab.lib");
    require_once("extra/explorer/filestructure.lib");

    if($params["LID"]){
        if($query = get_fwhere($params["LID"],$GLOBALS["ffilter"],$params["typ"])){
            $rs = lmbdb_exec($db,$query["count"]) or errorhandle(lmbdb_errormsg($db),$query["count"],$action,__FILE__,__LINE__);
            echo lmbdb_result($rs, "RESCOUNT");
        }else{
            echo "(unable to get row count!)";
        }
    }
}







# ---- table result count of rows --------
function dyns_getRowcount($params){
    global $db;

    require_once("gtab/gtab.lib");

    if($params["verknpf"]){
        $verkn = set_verknpf($params["verkn_tabid"],$params["verkn_fieldid"],$params["verkn_ID"],$params["verkn_add_ID"],$params["verkn_del_ID"],$params["verkn_showonly"],$params["verknpf"]);
    }

    if($params["gtabid"]){
        $query = get_sqlquery($params["gtabid"],1,$GLOBALS["filter"],$GLOBALS["gsr"],$verkn);
        if($rs = lmbdb_exec($db,$query["count"])){
            $rcount = lmbdb_result($rs, "RESULT");
        }else{
            $rcount = "(unable to get row count!)";
        }
    }

    if($params["return"]){
        return $rcount;
    }else{
        echo $rcount;
    }

}


/*
function dyns_validateWorkflow($params){
	global $workflowId;
	global $workflowInstanceId;

	$workflowId = $params["limbasWorkflowId"];
	$workflowInstanceId = $params["limbasWorkflowInstanceId"];


 	WF_stepForward($workflowId,$workflowInstanceId);
}

function dyns_cancelWorkflow($params){
	global $workflowId;
	global $workflowInstanceId;
	global $gtab;
	global $session;
	global $db;
	global $action;

	$workflowId = $params["limbasWorkflowId"];
	$workflowInstanceId = $params["limbasWorkflowInstanceId"];

	WF_cancel($workflowId,$workflowInstanceId);

 	if(WF_getOwner($workflowId,$workflowInstanceId)==$session["user_id"]){
 		$sqlquery = "delete from " . $gtab["table"][$workflowId] ." where ID = $workflowInstanceId";
 		$rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
 	}
}

*/

/**
 * Save new snapshot
 *
 * @param unknown_type $params
 */
function dyns_snapshotSaveas($params){
    global $farbschema;
    global $gfield;
    global $gtab;
    global $db;
    global $filter;

    require_once("extra/snapshot/snapshot.lib");
    $snapid = SNAP_save($params["gtabid"],$params["limbasSnapshotName"],0);
    echo $snapid;
}

/**
 * Delete snapshot
 *
 * @param unknown_type $params
 */
function dyns_snapshotDelete($params){
    global $farbschema;
    global $gfield;
    global $gtab;
    global $db;

    require_once("extra/snapshot/snapshot.lib");

    if(!SNAP_delete($params["snap_id"],$params["gtabid"]))
        echo "Error on delete snapshot : ".$_REQUEST["snap_id"];
    /**
     * @todo add the refresh of session
     */
}

/**
 * share snapshot
 *
 * @param unknown_type $params
 */
function dyns_lmbSnapShareSelect($params){

    if($params["destUser"] AND $params["gtabid"]){
        SNAP_share($params["destUser"],$params["gtabid"],$params["del"],$params["edit"],$params["drop"]);
    }

    dyns_snapShareDisplay($params);
}

function dyns_snapShareDisplay($params){
    global $session;
    global $db;
    global $lang;
    global $userdat;
    global $groupdat;

    $snapid = $params["gtabid"];

    $sqlquery = "SELECT LMB_SNAP_SHARED.EDIT,LMB_SNAP_SHARED.DEL,LMB_SNAP_SHARED.ENTITY_TYPE,LMB_SNAP_SHARED.ENTITY_ID FROM LMB_SNAP_SHARED WHERE SNAPSHOT_ID = $snapid ORDER BY ENTITY_TYPE DESC,ENTITY_ID";
    $rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);

    echo "<table cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">
	<tr><td colspan=\"6\"><hr></td></tr>
	<tr><td></td><td></td>
	<td style=\"width:25px\" align=\"center\"><i class=\"lmb-icon lmb-eye\"></i></td>
	<td style=\"width:25px\" align=\"center\"><i class=\"lmb-icon lmb-pencil\"></i></td>
	<td style=\"width:25px\" align=\"center\"><i class=\"lmb-icon lmb-trash\"></td>
	</tr>
	";

    while(lmbdb_fetch_row($rs)){
        $uid = lmbdb_result($rs,"ENTITY_ID")."_".lmbdb_result($rs,"ENTITY_TYPE");
        if(lmbdb_result($rs,"EDIT")){$edit = "CHECKED";}else{$edit = "";}
        if(lmbdb_result($rs,"DEL")){$del = "CHECKED";}else{$del = "";}
        if(lmbdb_result($rs,"ENTITY_TYPE")=="U"){
            $pic = " lmb-user ";
            $name = $userdat["bezeichnung"][lmbdb_result($rs,"ENTITY_ID")];
        }elseif(lmbdb_result($rs,"ENTITY_TYPE")=="G"){
            $pic = " lmb-group ";
            $name = $groupdat["name"][lmbdb_result($rs,"ENTITY_ID")];
        }else{
            continue;
        }

        echo "<tr>
		<td><i class=\"lmb-icon $pic\"></i></td>
		<td>".$name."</td>
		<td align=\"center\"><i class=\"lmb-icon lmb-check-alt\"></i></td>
		<td align=\"center\"><input type=\"checkbox\" class=\"checkb\" onclick=\"limbasSnapshotShare(null,$snapid,'$uid',0,1)\" $edit></td>
		<td align=\"center\"><input type=\"checkbox\" class=\"checkb\" onclick=\"limbasSnapshotShare(null,$snapid,'$uid',1)\" $del></td>
		<td align=\"center\" style=\"width:20px;\"><i class=\"lmb-icon lmb-erase\" style=\"cursor:pointer;\" onclick=\"limbasSnapshotShare(null,$snapid,'$uid',0,0,1)\"></td>
		</tr>
		";
    }
    echo "</table>";

}

/**
 * Save an existing snapshot
 *
 * @param unknown_type $params
 */
function dyns_snapshotSave($params){
    global $farbschema;
    global $gfield;
    global $gtab;
    global $db;
    global $filter;

    #$filter = $_SESSION["filter"];
    SNAP_save($params["gtabid"],$params["limbasSnapshotName"],$params["snap_id"]);
    #save_snapshot($_REQUEST["gtabid"],"",$params["snap_id"]);
    /**
     * @todo add the refresh of session
     */

}



function dyns_getDiag($params){
    global $gdiaglist;
    global $umgvar;
    global $gsr;
    global $filter;

    $diag_id = $params['diag_id'];
    $gtabid = $params['gtabid'];

    if ($gdiaglist[$gtabid]["id"][$diag_id]) {
        if ($gdiaglist[$gtabid]["template"][$diag_id] AND file_exists($umgvar["pfad"] . $gdiaglist[$gtabid]["template"][$diag_id])) {
            require_once ($umgvar["pfad"] . $gdiaglist[$gtabid]["template"][$diag_id]);
        } else {
            require_once ('extra/diagram/diagram.php');
            if ($diag = lmb_createDiagram($diag_id, $gsr, $filter)) {
                echo $diag;
            }
        }
    }

}


function dyns_completeTyping($params)
{
    global $farbschema;
    global $gfield;
    global $gtab;
    global $db;

    $tableName = $params['limbasTableName'];
    $fieldSearch = explode('#L#',$params['limbasFieldSearch']);
    $displayField = explode('#L#',$params['limbasDisplayField']);
    $jsFunction = $params['limbasJsFunction'];
    $fieldId = $params['limbasFieldId'];
    $beginValue = $params['limbasBeginValue'];
    $idField = $params['limbasIdField'];
    $inputId = $params['limbasInputId'];
    $inputDisplay = $params['limbasInputDisplay'];

    $sqlquery = "select ".implode(",",$displayField).",$idField from ".$tableName." where UPPER(".implode(") like '".lmb_strtoupper($beginValue)."%' OR UPPER(",$fieldSearch).") like '".lmb_strtoupper($beginValue)."%'";
    $rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
    if(!$rs) {$commit = 1;}

    while(lmbdb_fetch_row($rs)) {
        $value = "";
        $separator ="";
        foreach ($displayField as $keyR => $valR)
        {
            $value .= $separator . lmbdb_result($rs,$valR);
            $separator = " ";
        }

        echo "<span onClick='javascript:limbasClickCompleteTyping(\"$inputDisplay\",\"$value\",\"$inputId\",\"".lmbdb_result($rs,$idField)."\")'>$value";

        echo "</span><br>";
    }
}

function dyns_getSelectValues($params){
    global $gfield;
    global $db;
    global $lmfieldtype;
    global $farbschema;
    global $lang;


    # SELECT / ATTRIBUTE
    if($gfield[$params['gtabid']]["field_type"][$params['fieldid']] == 19){$tabtyp = "LMB_ATTRIBUTE";$aselect = ",LMB_ATTRIBUTE_D.VALUE_STRING,LMB_ATTRIBUTE_D.VALUE_NUM,LMB_ATTRIBUTE_W.TYPE";}else{$tabtyp = "LMB_SELECT";}

//	echo "<div class=\"lmbContextMenu\">";
	pop_left();
	echo "<table border=\"0\" cellpadding=\"0\" cellspacing=\"1\" style=\"padding:2px;\">";

	if($gfield[$params['gtabid']]['select_sort'][$params['fieldid']]){
		$order = "ORDER BY ".$tabtyp."_W.".$gfield[$params['gtabid']]['select_sort'][$params['fieldid']];
	}
	$sqlquery = "SELECT WERT $aselect FROM ".$tabtyp."_D,".$tabtyp."_W WHERE ".$tabtyp."_D.W_ID = ".$tabtyp."_W.ID AND ".$tabtyp."_D.TAB_ID = ".$params['gtabid']." AND ".$tabtyp."_D.FIELD_ID = ".$params['fieldid']." AND ".$tabtyp."_D.DAT_ID = ".$params['id']." ".$order;
	$rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
	while(lmbdb_fetch_row($rs)) {
		# ATTRIBUTE
		if($gfield[$params['gtabid']]["field_type"][$params['fieldid']] == 19){
			# date
			if($lmfieldtype[lmbdb_result($rs, "TYPE")]["parse_type"] == 4){
				$attvalue = ":&nbsp;".stampToDate(lmbdb_result($rs, "VALUE_NUM"),1);
			# text / number
			}else{
				if(lmbdb_result($rs, "VALUE_NUM")){$value_num = lmbdb_result($rs, "VALUE_NUM");}else{$value_num = "";}
				$attvalue = ":&nbsp;".htmlentities(trim($value_num." ".lmbdb_result($rs, "VALUE_STRING")),ENT_QUOTES,$GLOBALS["umgvar"]["charset"]);
			}
		}
		echo "<tr><td nowrap>".htmlentities(lmbdb_result($rs, "WERT"),ENT_QUOTES,$GLOBALS["umgvar"]["charset"])."</td><td style=\"color:".$farbschema["WEB12"]."\">$attvalue</td></tr>";
	}
	
	echo "</table>";
	pop_right();
	pop_bottom();
//	echo "</div>";
}

/**
 * inherit search
 *
 * @param unknown_type $params
 */
function dyns_searchInherit($params){
    global $gfield;
    global $gtab;
    global $db;

    require_once("gtab/gtab.lib");

    $destId = $params["ID"];
    $ID = $destId;
    $sourceGtabid = $params["gtabid"];
    $sourceGfieldid = $params["gfieldid"];
    $value = $params["value"];
    $destGfield = $params["dest_gfieldid"];
    $destGtabid = $params["dest_gtabid"];
    $sourcefilter = $gfield[$destGtabid]["inherit_filter"][$destGfield];

    if($sourcefilter){
        eval($sourcefilter.";");
    }

    if($value AND $value != "*"){
        $gsr[$sourceGtabid][$sourceGfieldid][] = $value;
    }

    if($params["showall"] == 1){
        $filter["anzahl"][$sourceGtabid] = "all";
    }

    $onlyfield[$sourceGtabid] = array($sourceGfieldid);
    $gresult = get_gresult($sourceGtabid,1,$filter,$gsr,$verkn,$onlyfield,$sourceId,$extension);

    if($gresult[$sourceGtabid][$sourceGfieldid]){
        pop_top("limbasInheritResult_".$sourceGtabid."_".$sourceGfieldid);
        pop_left();
        echo "<TABLE id=\"limbasInheritResult_".$sourceGtabid."_".$sourceGfieldid."\">";
        foreach ($gresult[$sourceGtabid][$sourceGfieldid] as $key => $value){
            if($value){
                echo "<TR>";
                echo "<TD style=\"cursor:pointer\"><A href=\"#\" onClick=\"limbasInheritFrom(event, $sourceGtabid,".$gresult[$sourceGtabid]["id"][$key].", $destGtabid, $destGfield, '$ID','".$gresult[$sourceGtabid]['parentage']."');\">".$value."</A></TD>";
                echo "</TR>";
            }
        }

        if($params["showall"] != 1 AND $gresult[$sourceGtabid]["res_count"] > $GLOBALS["session"]["maxresult"]){
            echo "<TR><TD><A href=\"#\" OnClick=\"limbasSearchInherit($destGtabid,$destGfield,$sourceGtabid,$sourceGfieldid,document.getElementById('g_".$destGtabid."_".$destGfield."'),'$ID',1)\"><i>show all ..</i></A></TD><TR>";
        }
        echo "</TABLE>";
        pop_right();
        pop_bottom();
    }
}


/**
 * inherit results
 *
 * @param unknown_type $params
 */
function dyns_inheritFrom($params){
    global $gfield;
    global $gtab;
    global $db;

    require_once("gtab/gtab.lib");

    $sourceId = $params["source_id"];
    $destId = $params["dest_id"];
    $destGtabid = $params["gtabid"];
    $destFieldId = $params["dest_gfieldid"];
    $sourceGtabid = $gfield[$destGtabid]["inherit_tab"][$destFieldId];
    $sourceFieldid = $gfield[$destGtabid]["inherit_field"][$destFieldId];
    $sourceGroup = $gfield[$destGtabid]["inherit_group"][$destFieldId];
    $gresultDest = get_gresult($sourceGtabid,1,null,null,null,null,$sourceId);

    foreach ($gfield[$destGtabid]["inherit_tab"] as $fieldId => $inherit_tabid) {
        if($inherit_tabid == $sourceGtabid AND $sourceGroup == $gfield[$destGtabid]["inherit_group"][$fieldId]){
            $srcFieldName = $gfield[$sourceGtabid]["field_name"][$gfield[$destGtabid]["inherit_field"][$fieldId]];
            $srcFieldId = $gfield[$sourceGtabid]["field_id"][$gfield[$destGtabid]["inherit_field"][$fieldId]];

            $resultval = $gresultDest[$sourceGtabid][$srcFieldId][0];
            if($gfield[$destGtabid]["inherit_eval"][$fieldId]){
                if(!$gresult){$gresult = get_gresult($destGtabid,1,null,null,null,null,$destId);}
                $resultval = eval("return \"".trim($resultval)."\";");
            }

            // currency
            if($gfield[$destGtabid]["data_type"][$fieldId] == 30){
                if($gresultDest[$sourceGtabid][$srcFieldId][0]['C']){
                    $resultval = $gresultDest[$sourceGtabid][$srcFieldId][0]['V']." ".$gresultDest[$sourceGtabid][$srcFieldId][0]['C'];
                }else{
                    $resultval = $gresultDest[$sourceGtabid][$srcFieldId][0]['V'];
                }
            }

            // relations
            if($gfield[$destGtabid]["field_type"][$fieldId] == 11){

                # delete old relations
                $sqlquery = "DELETE FROM ".$gfield[$destGtabid]["md5tab"][$fieldId]." WHERE ID = ".$destId;
                $rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$GLOBALS['action'],__FILE__,__LINE__);
                if(!$rs) {$commit = 1;}

                $filter["relationval"][$sourceGtabid] = 1;
                $onlyfield[$sourceGtabid] = array($srcFieldId);
                $gresult_ = get_gresult($sourceGtabid,1,$filter,null,null,$onlyfield,$sourceId);
                if($gresult_ = $gresult_[$sourceGtabid][$srcFieldId][0]){
                    #$relation = init_relation($destGtabid,$fieldId,$destId,$gresult_);
                    #set_relation($relation);
                    $result["resultval"][] = implode(',',$gresult_);
                }else{
                    continue;
                }

            }else{
                $result["resultval"][] = lmb_utf8_encode($resultval);
            }

            $result["destId"][] = $destId;
            $result["destGtabid"][] = $destGtabid;
            $result["destFieldid"][] = $fieldId;
            $result["destFormname"][] = $gfield[$destGtabid]["form_name"][$fieldId];
            $result["destFieldtype"][] = $gfield[$destGtabid]["field_type"][$fieldId];
            $result["ajaxpost"][] = $gfield[$destGtabid]["ajaxpost"][$fieldId];
        }
    }

    $GLOBALS["noencode"] = 1;

    if($result){
        echo json_encode($result);
    }else{
        echo "false";
    }
}


/**
 * show diff of versioned PDFs
 *
 * @param unknown_type $params
 */
function dyns_versionDiffPDF($params){
    global $greportlist;
    global $umgvar;

    $GLOBALS["tmp"]["params"] = $params;

    foreach($greportlist[-1]["name"] as $key => $value){
        if ($value == "_LimbasVersionDiff") {
            $report_id = $greportlist[-1]["id"][$key];
        }
    }

    if($report_id){
        require_once("extra/report/report.dao");
        require_once("extra/report/report_pdf.php");
    }
}


/**
 * show diff of versioned dataset
 *
 * @param unknown_type $params
 */
function dyns_versionDiff($params){
    global $db;
    global $gtab;
    global $lang;
    global $umgvar;
    global $session;
    global $glob_fielddesc;
    global $userdat;

    if(!$GLOBALS["LINK"][205]){die();}

    $filename = $umgvar["pfad"]."/USER/".$session["user_id"]."/temp/diff_".$params["ID"]."_".$params["VID"].".html";
    $filelink = "USER/".$session["user_id"]."/temp/diff_".$params["ID"]."_".$params["VID"].".html";

	# get version number
	$sqlquery = "SELECT ID,VID,ERSTDATUM,ERSTUSER FROM ".$gtab["table"][$params["gtabid"]]." WHERE ID = ".$params["ID"]." OR ID = ".$params["VID"];
	$rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
	if(!$rs) {$commit = 1;}
	$bzm=1;
	while(lmbdb_fetch_row($rs)){
	    if($bzm == 1){
            $v1 = lmbdb_result($rs,"VID");
            $d1 = get_date(lmbdb_result($rs,"ERSTDATUM"));
            $u1 = $userdat["bezeichnung"][lmbdb_result($rs,"ERSTUSER")];
        }elseif($bzm == 2){
            $v2 = lmbdb_result($rs,"VID");
            $d2 = get_date(lmbdb_result($rs,"ERSTDATUM"));
            $u2 = $userdat["bezeichnung"][lmbdb_result($rs,"ERSTUSER")];
        }
	    $bzm++;
    }

    if($out = dyns_versionDiffResult($params)){
        #$glob_fielddesc
        echo "
		    <table cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">
		    <tr><td nowrap><b>V $v1</b> $d1 </td><td nowrap><i>$u1</i></td><td align=\"right\" width=\"90%\"><a href=\"".$filelink.".pdf\" target=\"new\"><i class=\"lmb-icon lmb-file-pdf\" border=0></i></a></td></tr>
		    <tr><td nowrap><b>V $v2</b> $d2 </td><td nowrap><i>$u2</i></td></tr>
		    </table>
		<HR>\n";

        echo $out;
    }else{
        echo "<DIV>".$lang[2177]."</DIV>";
    }

    # write pdf
    if($out){
        $out = "<CENTER><h3>$glob_fielddesc<h4>".$lang[2354]." : $v1 | $v2 (ID ".$params["ID"]." | ".$params["VID"].")<hr></CENTER>".$out;

        if ($handle = fopen($filename, "w")) {
            fwrite($handle, $out);
        }
        if(file_exists($filename)){
            if(file_exists($filename.".pdf")){unlink($filename.".pdf");}
            $cmd = "htmldoc --size 295x210mm --left 10mm --right 10mm --top 10mm --bottom 10mm --webpage --header ... --footer ... -f ".$filename.".pdf $filename";
            $ocrout = `$cmd`;
        }
    }

}


/**
 * calculate version diff
 *
 * @param unknown_type $params
 * @param unknown_type $sub
 * @return unknown
 */
function dyns_versionDiffResult($params,$sub=0){
    global $gfield;
    global $gtab;
    global $db;
    global $lang;
    global $umgvar;
    global $session;
    global $farbschema;
    global $glob_fielddesc;
    global $userdat;

    require_once("gtab/gtab.lib");
    require_once("gtab/gtab_type_erg.lib");

    $ID = $params["ID"];
    $VID = $params["VID"];
    $gtabid = $params["gtabid"];
    $form_id = $params["form_id"];
    $pdf = $params["pdf"];
    $sumout = array();

    if($gtab["raverkn"][$gtab["verkn"][$gtabid]]){
        # Schleife aller 1:1 Tabellen
        foreach ($gtab["raverkn"][$gtab["verkn"][$gtabid]] as $key0 => $tabid){

            $out = null;
            $fielddesc = null;
            $fieldkey = $ID;

            if($gtab["tab_id"][$tabid]){

                if($ID){
                    $filter["viewversion"][$tabid] = 1;
                    $gresult1 = get_gresult($tabid,1,$filter,null,null,null,$ID);
                }

                if($VID){

                    $filter["viewversion"][$tabid] = 1;
                    $gresult2 = get_gresult($tabid,1,$filter,null,null,null,$VID);
                }

                # Default Ausgabe
                $isdiff = 0;

                # Schleife aller Felder
                foreach ($gfield[$tabid]["sort"] as $key => $value){
                    $verkn = null;

                    $fname = "cftyp_".$gfield[$tabid]["funcid"][$key];
                    if(function_exists($fname) AND $gfield[$tabid]["data_type"][$key] != 22 AND $gfield[$tabid]["parse_type"][$key] < 100 AND $gfield[$tabid]["copy"][$key]){
                        $result1 = $fname(0,$key,$tabid,4,$gresult1,0);
                        $result2 = $fname(0,$key,$tabid,4,$gresult2,0);


                        # edituser
                        if($gresult2[$tabid]['EDITUSER'][0]){
                            $chuser = $userdat["bezeichnung"][$gresult2[$tabid]['EDITUSER'][0]];
                        }elseif($gresult1[$tabid]['EDITUSER'][0]){
                            $chuser = $userdat["bezeichnung"][$gresult1[$tabid]['EDITUSER'][0]];
                        }elseif($gresult2[$tabid]['EDITUSER'][0]){
                            $chuser = '';
                        }

                        # currency
                        if($gfield[$tabid]["data_type"][$key] == 30){
                            if(!(trim($result1)/1)){$result1 = "";}
                            if(!(trim($result2)/1)){$result2 = "";}
                        }

                        # upload files
                        if($gfield[$tabid]["field_type"][$key] == 6){
                            $result1 = $result1["name"];
                            $result2 = $result2["name"];
                        }

                        # field description
                        if($gfield[$tabid]["mainfield"] == $key){
                            if(is_array($result1) AND $gfield[$tabid]["field_type"][$key] == 11){
                                $fielddesc = " -> (".$result1[0].")";
                            }elseif($result1){
                                $fielddesc = " -> (".$result1.")";
                                if(!$sub){$glob_fielddesc = $result1;}
                            }else{
                                $fielddesc = " -> ".$lang[770];
                            }
                        }
                        # key field
                        if($gfield[$tabid]["fieldkey_id"] == $key){
                            $fieldkey = $result1;
                        }


                        ###################### DIFF ########################
                        if(is_array($result1) OR is_array($result2)){

                            # Verknüpfung 1:n
                            if($gfield[$tabid]["data_type"][$key] == 27){
                                $vgtabid = $gfield[$tabid]["verkntabid"][$key];
                                $linklevel["tabid"] = $gfield[$tabid]["verkntabid"][$key];
                                $linklevel["fieldid"] = $gfield[$tabid]["verknfieldid"][$key];
                                $relation_A = null;
                                $relation_B = null;
                                unset($result1["id"]);
                                unset($result2["id"]);
                                unset($result1["name"]);
                                unset($result2["name"]);
                            }

                            # extended diff for relation, only for version tables and 1:n relations
                            if($gtab["versioning"][$vgtabid] AND $gtab["viewver"][$vgtabid] AND $gfield[$tabid]["data_type"][$key] == 27){

                                # --- get relation A ----
                                if($VID){
                                    $verkn = set_verknpf($tabid,$key,$VID);
                                    $vgresult = sql_14_c($vgtabid,$verkn,$linklevel);
                                    $gkey = 0;
                                    while($gkey < $vgresult[$vgtabid]["res_viewcount"]) {
                                        $relation_A[$vgresult[$vgtabid]["VPID"][$gkey]] = $vgresult[$vgtabid]["id"][$gkey];
                                        $relation_B[$vgresult[$vgtabid]["VPID"][$gkey]] = 0;
                                        $gkey++;
                                    }
                                }

                                # --- get relation B ----
                                if($ID){
                                    $verkn = set_verknpf($tabid,$key,$ID);
                                    $vgresult = sql_14_c($vgtabid,$verkn,$linklevel);
                                    $gkey = 0;
                                    while($gkey < $vgresult[$vgtabid]["res_viewcount"]) {
                                        $relation_B[$vgresult[$vgtabid]["VPID"][$gkey]] = $vgresult[$vgtabid]["id"][$gkey];
                                        if(!$relation_A[$vgresult[$vgtabid]["VPID"][$gkey]]){
                                            $relation_A[$vgresult[$vgtabid]["VPID"][$gkey]] = 0;
                                        }
                                        $gkey++;
                                    }
                                }

                                # --- compare results ----
                                if($relation_B){
                                    foreach ($relation_B as $key_b => $value_b){
                                        #if($value_b AND $relation_A[$key_b] OR !$relation_A[$key_b]){
                                        $subparams = array("ID" => $value_b,"VID" => $relation_A[$key_b],"gtabid" => $vgtabid,"form_id" => $form_id,"pdf" => 0);
                                        if($diff_ = dyns_versionDiffResult($subparams,1)){
                                            $diff[] = $diff_;
                                        }
                                        #}
                                    }
                                }


                                # extended diff for relation, only textdiff
                            }elseif($gfield[$tabid]["data_type"][$key] == 27){

                                $onlyfield = $gfield[$tabid]["verknview"][$key];

                                $res1 = get_gresult($gfield[$tabid]['verkntabid'][$key], 1, null, null, set_verknpf($tabid, $key, $ID, null, null, 1));
                                $res1 = str_replace(array("<",">","|","\\","/"),' ',get_txtresult($gfield[$tabid]['verkntabid'][$key], $res1, ' ; ', $onlyfield));

                                $res2 = get_gresult($gfield[$tabid]['verkntabid'][$key], 1, null, null, set_verknpf($tabid, $key, $VID, null, null, 1));
                                $res2 = str_replace(array("<",">","|","\\","/"),' ',get_txtresult($gfield[$tabid]['verkntabid'][$key], $res2,' ; ', $onlyfield));

                                if($tablediff = tableDiff($res2,$res1,null,null,null,1,1)){
                                    $out .= "<tr><TD VALIGN=\"TOP\">&nbsp;&nbsp;".$gfield[$tabid]["spelling"][$key]."</TD><TD VALIGN=\"TOP\">&nbsp;</TD><td>".$tablediff."</td></tr>";
                                }

                                # simple diff
                            }else{

                                # check if related datasets added or deleted
                                $diff = arrayDiff($result2,$result1);

                            }

                            if($diff){
                                $isdiff = 1;
                                if(is_array($diff) AND count($diff) > 0){
                                    #$chuser = historyGetEdituser($tabid,$key,$ID);
                                    $out .= "<TR><TD VALIGN=\"TOP\">&nbsp;&nbsp;".$gfield[$tabid]["spelling"][$key]."</TD><TD VALIGN=\"TOP\">&nbsp;</TD><TD VALIGN=\"TOP\" style=\"border:3px solid #CCCCCC\" TITLE=\"".$chuser."\">";
                                    foreach ($diff as $keyd => $valued){
                                        if($valued){
                                            $out .= html_entity_decode($valued,ENT_QUOTES,$GLOBALS["umgvar"]["charset"]);
                                        }
                                    }
                                    $out .= "</TD></TR>";
                                }elseif(is_string($diff)){
                                    #$chuser = historyGetEdituser($tabid,$key,$ID);
                                    $out .= "<TR><TD VALIGN=\"TOP\">&nbsp;&nbsp;".$gfield[$tabid]["spelling"][$key]."</TD><TD VALIGN=\"TOP\">&nbsp;</TD><TD VALIGN=\"TOP\" TITLE=\"".$chuser."\">".html_entity_decode($diff,ENT_QUOTES,$GLOBALS["umgvar"]["charset"])."</TD></TR>";
                                }
                                $diff = null;
                            }

                            # Text Diff
                        }elseif(!is_array($result1)){
                            $diff = textDiff($result1,$result2);
                            if(trim($diff)){
                                #$chuser = historyGetEdituser($tabid,$key,$ID);
                                $out .= "<TR><TD VALIGN=\"TOP\">&nbsp;&nbsp;".$gfield[$tabid]["spelling"][$key]."</TD><TD>&nbsp;</TD><TD VALIGN=\"TOP\" TITLE=\"".$chuser."\">".html_entity_decode($diff,ENT_QUOTES,$GLOBALS["umgvar"]["charset"])."</TD></TR>";
                            }
                            $diff = null;
                        }
                    }
                }

                if($out){
                    if(!$sub){$outd = "<TR><TD VALIGN=\"TOP\"><b>".$gtab["desc"][$tabid]."</b></TD><TD COLSPAN=\"3\" VALIGN=\"TOP\" BGCOLOR=\"#EFEFEF\">";}
                    else{$outd = "<TR><TD COLSPAN=\"5\" VALIGN=\"TOP\" BGCOLOR=\"#CCCCCC\">";}
                    $out = $outd."<p style=\"overflow:hidden;width:100%\">".$fieldkey.$fielddesc."</p></TD></TR>".$out;
                    $sumout[] = $out;
                }

            }
        }
    }

    if(count($sumout) > 0){
        if($sub){$w = "style=\"width:100%\"";}else{$w = "";}
        return "<TABLE $w>\n".implode("\n",$sumout)."</TABLE>";
    }else{
        return false;
    }
}

# --- Datei Versionierung Diff -----------
function dyns_fileVersionDiff($params){
    global $db;
    global $lang;
    global $umgvar;
    global $filestruct;

    if(!$GLOBALS["LINK"][205]){return;}

    require_once("extra/explorer/metadata.lib");
    if(!$filestruct){get_filestructure();}

    $file1 = $params["file1"];
    $file2 = $params["file2"];
    $typ = $params["typ"];

    if($file1 AND $file2){
        $toba = array("|","<",">");

        $sqlquery = "SELECT LDMS_FILES.LEVEL,LDMS_FILES.SECNAME,LDMS_FILES.NAME,LDMS_FILES.VID,LMB_MIMETYPES.EXT,LMB_MIMETYPES.MIMETYPE FROM LMB_MIMETYPES,LDMS_FILES WHERE LDMS_FILES.MIMETYPE = LMB_MIMETYPES.ID AND LDMS_FILES.ID = $file1";
        $rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
        if(!$rs) {$commit = 1;}

        if(lmbdb_fetch_row($rs)){
            $level = lmbdb_result($rs, "LEVEL");
            $target = $umgvar["uploadpfad"].$filestruct['path'][$level].lmbdb_result($rs, "SECNAME").".".lmbdb_result($rs, "EXT");
            if(file_exists($target)){
                $target1 = str_replace($toba," ",convert_to_text(lmbdb_result($rs, "SECNAME"),lmbdb_result($rs, "EXT"),lmbdb_result($rs, "MIMETYPE"),$file1,0,1,0,lmbdb_result($rs, "LEVEL")));
                $vcount1 = lmbdb_result($rs, "VID");
                if(lmb_utf8_check(lmb_substr($target1,0,200))){
                    $target1 = lmb_utf8_decode($target1);
                }elseif($GLOBALS["umgvar"]["charset"] == "UTF-8"){
                    $target1 = lmb_utf8_encode($target1);
                }
            }
        }

        $sqlquery = "SELECT LDMS_FILES.LEVEL,LDMS_FILES.SECNAME,LDMS_FILES.NAME,LDMS_FILES.VID,LMB_MIMETYPES.EXT,LMB_MIMETYPES.MIMETYPE FROM LMB_MIMETYPES,LDMS_FILES WHERE LDMS_FILES.MIMETYPE = LMB_MIMETYPES.ID AND LDMS_FILES.ID = $file2";
        $rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
        if(!$rs) {$commit = 1;}
        if(lmbdb_fetch_row($rs)){
            $level = lmbdb_result($rs, "LEVEL");
            $target = $umgvar["uploadpfad"].$filestruct['path'][$level].lmbdb_result($rs, "SECNAME").".".lmbdb_result($rs, "EXT");
            if(file_exists($target)){
                $target2 = str_replace($toba," ",convert_to_text(lmbdb_result($rs, "SECNAME"),lmbdb_result($rs, "EXT"),lmbdb_result($rs, "MIMETYPE"),$file2,0,1,0,lmbdb_result($rs, "LEVEL")));
                $vcount2 = lmbdb_result($rs, "VID");
                if(lmb_utf8_check(lmb_substr($target2,0,200))){
                    $target2 = lmb_utf8_decode($target2);
                }elseif($GLOBALS["umgvar"]["charset"] == "UTF-8"){
                    $target2 = lmb_utf8_decode($target2);
                }
            }
        }

        if($target1 AND $target2){
            $diff = textDiff($target1,$target2);
            if($diff){
                echo "<B>".lmbdb_result($rs, "NAME")."</B>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<SPAN STYLE=\"color:blue\">".$lang[2]."<B> ".$vcount1."</B> -> ".$lang[2]."<B> ".$vcount2."</B></SPAN><BR><hr>";
                echo str_replace("\t","&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;",$diff);
            }
        }
    }
}









/**
 * # report menu
 *
 * @param array $params
 * @return void
 */
function dyns_menuReportOption($params){
	global $lang;
	global $greportlist;
	global $umgvar;
	global $session;
	global $db;
	global $sysfont;
	global $styletyp;
	global $gprinter;
	global $LINK;

	$reportspec = $greportlist[$params['gtabid']];
	if(!$reportspec){return;}

	require_once('extra/report/report.dao');

	$action = $params['action'];
	$report_id = $params['reportid'];
	$report_name = $reportspec['name'][$report_id];
	$report_savename = $reportspec['savename'][$report_id];
	$report_defformat = $reportspec['defformat'][$report_id];
	$report_medium = $params['report_medium'];
	$report_output = $params['report_output'];
	$report_ext = $params['report_ext'];
    $report_rename = $params['report_rename'];
    $report_printer = intval($params['report_printer']);
	$gtabid = $params['gtabid'];
	$use_record = $params['use_record'];
	$ID = $params['ID'];
	$listmode = $greportlist[$gtabid]['listmode'][$report_id];
	if(!$report_medium){$report_medium = $report_defformat;}

	if($report_output){

		$filter = $GLOBALS['filter'];
		$gsr = $GLOBALS['gsr'];
		if($params['verkn_ID']){
			require_once('gtab/gtab.lib');
			$verkn = set_verknpf($params['verkn_tabid'],$params['verkn_fieldid'],$params['verkn_ID'],null,null,$params['verkn_showonly'],1);
		}

		require_once('extra/explorer/filestructure.lib');
		require_once('extra/report/report_'.lmb_substr($report_medium,0,3).'.php');
		if(!file_exists(lmb_utf8_encode($generatedReport))){
		    return false;
        }
		if($report_output == 1 OR $report_output == 3){
			$path = trim(str_replace($umgvar['pfad'],'',$generatedReport),'/') . '?v=' . date('U');
			echo "<Script language=\"JavaScript\">open(\"".$path."\");</Script>"; // todo - wrong html header
		} else if ($report_output == 4 and $LINK[304]) {
            if (!lmbPrint($report_printer, lmb_utf8_encode($generatedReport))) {
            	lmb_log::error('print failed', 'print failed!', $gtabid);
			}
		}
	}

	$opt['val'] = array('xml');
	$opt['desc'] = array('xml');

	# use either pdf or tcpdf in background, dependent on report configuration
	if ($report_defformat === 'pdf' or $report_defformat === 'tcpdf') {
        array_unshift($opt['val'], $report_defformat);
        array_unshift($opt['desc'], 'pdf');
    }

	if($reportspec['odt_template'][$report_id]){
		$opt['val'][] = 'odt';
		$opt['desc'][] = 'text';
	}
	if($reportspec['ods_template'][$report_id]){
		$opt['val'][] = 'ods';
		$opt['desc'][] = 'spreadsheet';
	}

	$report_rename = reportSavename($report_name,$report_savename,$ID,$report_medium,$report_rename, false);

	echo '<form name="report_form">';
	pop_top('limbasDivMenuReportOption');
	pop_select('',$opt,$report_medium,1,'report_medium',$lang[2502]."&nbsp;",'');
	if($params['action'] != 'gtab_erg'){
		pop_input2('','report_rename',$report_rename,$readonly,$title,$titlesize);
	}
	pop_line();
	pop_menu2($lang[1739],'','','lmb-icon-cus lmb-script-go','',"limbasReportMenuOptions(event,'','{$params['gtabid']}','{$params['reportid']}','{$ID}',1,'{$listmode}')");
    pop_menu2($lang[1787],'','lmbReportMenuOptionsArchive','lmb-icon-cus lmb-script-save','',"limbasReportMenuOptions(event,'','{$params['gtabid']}','{$params['reportid']}','{$ID}',2,'{$listmode}')");
    if ($LINK[304] and $gprinter) {
        # separator
        pop_line();

        # print button
		pop_menu(304, "limbasReportMenuOptions(event,'','{$params['gtabid']}','{$params['reportid']}','{$ID}',4,'{$listmode}', null, null, $('#lmbReportMenuOptionsPrinter').val())");

        # printer selection
        $opt = array();
        $sel = '';
        foreach ($gprinter as $id => $printer) {
            $opt['val'][] = $id;
            $opt['desc'][] = $printer['name'];
            if ($printer['default']) {
                $sel = $id;
            }
        }
		pop_select('', $opt, $sel, 1, 'lmbReportMenuOptionsPrinter', $lang[2939]);
    }
	pop_bottom();
	echo "</form>";
	
}


function dyns_printFile($params) {
	global $umgvar;
	global $LINK;

	if (!$LINK[304]) {
		return;
	}

	$printerID = intval($params['printerID']);
	$fileIDs = json_decode($params['fileIDs']);

	require_once('extra/explorer/filestructure.lib');

	foreach ($fileIDs as $fileID) {
		$fileID = parse_db_int($fileID);
		$file = file_download($fileID);
		if (!$file) {
			continue; # TODO maybe user message
		}

        $filePath = $umgvar['path'] . '/' . $file['path'];

		# try to convert to pdf
		if (LmbUnoconv::isEnabled() and $file['mimetype'] != 'application/pdf') {
            $newFilePath = $filePath . '.pdf';
            if (LmbUnoconv::convertFile($filePath, $newFilePath, 'pdf')) {
            	$filePath = $newFilePath;
			}
        }

        if (!lmbPrint($printerID, $filePath)) {
            lmb_log::error('Print failed', 'Drucken fehlgeschlagen!');
        }
    }
}


/**
 * User/Grouplist fieldtype
 *
 * @param unknown_type $params
 */
function dyns_UGtype($params){
    global $farbschema;
    global $session;
    global $userdat;
    global $groupdat;
    global $gfield;
    global $gtab;
    global $db;

    $gtabid = $params["gtabid"];
    $fieldid = $params["fieldid"];
    $ID = $params["ID"];
    $usgr = $params["usgr"];
    $usgra = explode("_",$usgr);

    $redresult["activearray"] = array();
    if(!$ID OR !$fieldid OR !$gtabid){return false;}

    $onlyfield[$gtabid] = array($fieldid);

    # edit element
    if(is_numeric($usgra[0]) AND $gfield[$gtabid]["perm_edit"][$fieldid]){
        $edit_typ = $usgra[1];
        $edit_id = $usgra[0];

        if($edit_typ == 'u' OR $edit_typ == 'g'){
            $sqlquery = "SELECT UGID FROM LMB_UGLST WHERE TABID = ".parse_db_int($gtabid)." AND FIELDID = ".parse_db_int($fieldid)." AND DATID = ".parse_db_int($ID)." AND TYP = '".parse_db_string($edit_typ,1)."' AND UGID = ".parse_db_int($edit_id);
            $rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);

            if(lmbdb_result($rs, "UGID")){
                $sqlquery = "DELETE FROM LMB_UGLST WHERE TABID = ".parse_db_int($gtabid)." AND FIELDID = ".parse_db_int($fieldid)." AND DATID = ".parse_db_int($ID)." AND TYP = '".parse_db_string($edit_typ,1)."' AND UGID = ".parse_db_int($edit_id);
                $rs1 = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
            }else{
                $sqlquery = "INSERT INTO LMB_UGLST (UGID,TABID,FIELDID,DATID,TYP) VALUES (".parse_db_int($edit_id).",".parse_db_int($gtabid).",".parse_db_int($fieldid).",".parse_db_int($ID).",'".parse_db_string($edit_typ,1)."')";
                $rs1 = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
            }

            $sqlquery = "UPDATE ".$gtab["table"][$gtabid]." SET ".$gfield[$gtabid]["field_name"][$fieldid]." = (SELECT COUNT(*) FROM LMB_UGLST WHERE TABID = ".parse_db_int($gtabid)." AND FIELDID = ".parse_db_int($fieldid)." AND DATID = ".parse_db_int($ID).") WHERE ID = $ID";
            $rs1 = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
        }
    }


    $sqlquery = "SELECT UGID,TYP FROM LMB_UGLST WHERE TABID = $gtabid AND FIELDID = $fieldid AND DATID = ".$ID;
    $rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);

    while(lmbdb_fetch_row($rs)){
        $gutyp = lmbdb_result($rs, "TYP");
        $guid = lmbdb_result($rs, "UGID");
        $ers = "";
//		if($typ == 1){
        $ers = "<i class=\"lmb-icon lmb-erase\" style=\"cursor: pointer;\" OnClick=\"lmb_UGtype('{$guid}_{$gutyp}','','$gtabid','$fieldid','$ID','');\"></i>&nbsp;";
//		}
        if($gutyp == 'u'){
            $gres[] = "<span>".$ers."<i class=\"lmb-icon lmb-icon-8 lmb-user\"></i> ".$userdat["bezeichnung"][$guid].$br."</span>";
        }elseif($gutyp == 'g'){
            $gres[] = "<span>".$ers."<i class=\"lmb-icon lmb-icon-8 lmb-group\"></i> ".$groupdat["name"][$guid].$br."</span>";
        }
    }

    if($gres){
        echo "<hr>";
        if(!$gfield[$gtabid]["select_cut"][$fieldid]) {
            $cut = '<br>';
        } else if(lmb_strtoupper($gfield[$gtabid]["select_cut"][$fieldid]) == "<OL>" OR lmb_strtoupper($gfield[$gtabid]["select_cut"][$fieldid]) == "<UL>" OR lmb_strtoupper($gfield[$gtabid]["select_cut"][$fieldid]) == "<LI>"){
            echo "<LI>";$cut = "<LI>";
        } else {
            $cut = $gfield[$gtabid]["select_cut"][$fieldid];
        }
        echo implode($cut,$gres);
    }
}

/**
 * Gtab Userrules - show overview
 *
 * @param unknown_type $params
 */
function dyns_showUserGroups($params){

    pop_left();

    echo "<div style=\"padding:3px\"><TABLE cellpading=0 cellspacing=0 border=0 width=\"100%\">
	<TR><TD COLSPAN=\"6\" NOWRAP>
	<i class=\"lmb-icon lmb-user\" style=\"cursor:pointer\" OnClick=\"lmbAjax_showUserGroupsSearch(event,'*','".$params["ID"]."','".$params["gtabid"]."','".$params["fieldid"]."','".$params["usefunction"]."','".$params["prefix"]."','user','".$params["parameter"]."')\"></i>&nbsp;<input type=\"text\" style=\"width:115px;border:1px solid grey\" OnKeyup=\"lmbAjax_showUserGroupsSearch(event,this.value,'".$params["ID"]."','".$params["gtabid"]."','".$params["fieldid"]."','".$params["usefunction"]."','','user','".$params["parameter"]."')\">
	<div id=\"".$params["prefix"].$params["usefunction"]."user\" class=\"ajax_container\" style=\"display:none;position:absolute;left:30px;top:25px;border:1px solid black;padding:2px;background-color:".$farbschema["WEB11"]."\"></div>&nbsp;&nbsp;&nbsp;
	<i class=\"lmb-icon lmb-group\" style=\"cursor:pointer\" OnClick=\"lmbAjax_showUserGroupsSearch(event,'*','".$params["ID"]."','".$params["gtabid"]."','".$params["fieldid"]."','".$params["usefunction"]."','".$params["prefix"]."','group','".$params["parameter"]."')\"></i>&nbsp;<input type=\"text\" style=\"width:115px;border:1px solid grey\" OnKeyup=\"lmbAjax_showUserGroupsSearch(event,this.value,'".$params["ID"]."','".$params["gtabid"]."','".$params["fieldid"]."','".$params["usefunction"]."','','group','".$params["parameter"]."')\">
	<div id=\"".$params["prefix"].$params["usefunction"]."group\" class=\"ajax_container\" style=\"display:none;position:absolute;left:180px;top:25px;border:1px solid black;padding:2px;background-color:".$farbschema["WEB11"]."\"></div>
	</TD></TR>
	</TABLE>
	";

    if(function_exists("dyns_".$params["usefunction"])){
        $fnk = "dyns_".$params["usefunction"];
        $fnk($params);
    }

    echo "</div>";

    pop_right();
    pop_bottom();

}

/**
 * # User Group List - show userlist
 *
 * @param unknown_type $params
 * @return unknown
 */
function dyns_showUserGroupsSearch($params){
    global $farbschema;
    global $userdat;
    global $groupdat;
    global $db;
    global $gtab;
    global $session;

    $typ = $params["typ"];
    $ID = $params["ID"];
    $gtabid = $params["gtabid"];
    $fieldid = $params["fieldid"];
    $searchvalue = parse_db_string($params["value"],50);
    $usefunction = $params["usefunction"];
    $parameter = $params["parameter"];

    pop_closetop($usefunction.$typ);

    if($typ == "user"){
        $sqlquery = "SELECT LMB_USERDB.USER_ID
			FROM LMB_USERDB WHERE LMB_USERDB.DEL = ".LMB_DBDEF_FALSE;
		if($searchvalue != "*"){
			$sqlquery .= " AND (LOWER(LMB_USERDB.USERNAME) LIKE '%".lmb_strtolower($searchvalue)."%' OR LOWER(LMB_USERDB.VORNAME) LIKE '%".lmb_strtolower($searchvalue)."%' OR LOWER(LMB_USERDB.NAME) LIKE '%".lmb_strtolower($searchvalue)."%')";
		}
		
		$rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
		if(!$rs) {$commit = 1;}
		while(lmbdb_fetch_row($rs)) {
			$key = lmbdb_result($rs,"USER_ID");
            pop_menu2($userdat["bezeichnung"][$key], null, null, "lmb-user", null, "$usefunction('".$key."_u','".$userdat["bezeichnung"][$key]."','$gtabid','$fieldid','$ID','$parameter');");
		}
	}elseif($typ == "group"){
		$sqlquery = "SELECT LMB_GROUPS.GROUP_ID 
			FROM LMB_GROUPS WHERE LMB_GROUPS.DEL = ".LMB_DBDEF_FALSE;
		if($searchvalue != "*"){
			$sqlquery .= " AND LOWER(LMB_GROUPS.NAME) LIKE '%".lmb_strtolower($searchvalue)."%'";
		}
		
		$rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
		if(!$rs) {$commit = 1;}

		while(lmbdb_fetch_row($rs)) {
			$key = lmbdb_result($rs,"GROUP_ID");
            pop_menu2($groupdat["name"][$key], null, null, "lmb-group", null, "$usefunction('".$key."_g','".$groupdat["name"][$key]."','$gtabid','$fieldid','$ID','$parameter');");
		}
	}
	
	pop_bottom();

}





/**
 * Gtab Userrules - show overview
 *
 * @param unknown_type $params
 */
function dyns_showGtabRules($params){
    global $farbschema;
    global $session;
    global $userdat;
    global $groupdat;
    global $gtab;
    global $lang;
    global $db;

    require_once("gtab/gtab.lib");

    $gtabid = $params["gtabid"];
    $use_records = urldecode($params["use_records"]);
    $recordlist = explode(";",$use_records);
    $userpara = urldecode($params["parameter"]);
    $userparams = explode("_",$userpara);

    if((!$gtab["edit_userrules"][$gtabid] AND !$gtab["edit_ownuserrules"][$gtabid]) OR !$use_records){echo "<span style=\"background-color:#000000;color:#FFFFFF\">no permission!</span>";return false;}

    pop_left();
    echo "<DIV style=\"padding:3px;\">";

    # add / modify userrights
    if($d_datidlist = add_GtabUserRules($gtabid,$recordlist,$userparams)){
        $d_datidlist = implode(",",$d_datidlist);
    }else{
        echo $lang[56];
        return false;
    }

    echo "<TABLE cellspan=0 cellspacing=0 border=0 width=\"100%\">";
    echo "<TR><TD COLSPAN=\"6\" NOWRAP>
	<i class=\"lmb-icon lmb-user\" style=\"cursor:pointer\" OnClick=\"limbasAjaxGtabRulesUsersearch('*','".$params["gtabid"]."','".$use_records."')\"></i>&nbsp;<input type=\"text\" style=\"width:115px;border:1px solid grey\" OnKeydown=\"limbasAjaxGtabRulesUsersearch(this.value,'".$params["gtabid"]."','".$use_records."')\">
	<div id=\"ContainerGtabRulesUsersearch\" class=\"ajax_container\" style=\"visibility:hidden;position:absolute;left:30px;top:25px;border:1px solid black;padding:2px;background-color:".$farbschema["WEB11"]."\"></div>&nbsp;&nbsp;&nbsp;
	<i class=\"lmb-icon lmb-group\" style=\"cursor:pointer\" OnClick=\"limbasAjaxGtabRulesGroupsearch('*','".$params["gtabid"]."','".$use_records."')\"></i>&nbsp;<input type=\"text\" style=\"width:115px;border:1px solid grey\" OnKeydown=\"limbasAjaxGtabRulesGroupsearch(this.value,'".$params["gtabid"]."','".$use_records."')\">
	<div id=\"ContainerGtabRulesGroupsearch\" class=\"ajax_container\" style=\"visibility:hidden;position:absolute;left:180px;top:25px;border:1px solid black;padding:2px;background-color:".$farbschema["WEB11"]."\"></div>
	</TD></TR>
	<TR><TD colspan=\"6\"><HR></TD></TR><TR>";
    if($gtab["subedit_userrules"][$gtabid] AND $gtab["edit_userrules"][$gtabid]){
        if($userparams[4]){$CHECKEDUP = "CHECKED";}
        if($userparams[5]){$CHECKEDDOWN = "CHECKED";}
        echo "<TD>
		<i class=\"lmb-icon lmb-caret-up\"><input type=\"checkbox\" id=\"hirar_rules_up\" OnChange=\"lmb_hirarrules='_'+limbasGetBoolVal(this.checked)+'_'+limbasGetBoolVal(document.getElementById('hirar_rules_down').checked);\" $CHECKEDUP></i>
		<i class=\"lmb-icon lmb-caret-down\"><input type=\"checkbox\" id=\"hirar_rules_down\" OnChange=\"lmb_hirarrules='_'+limbasGetBoolVal(document.getElementById('hirar_rules_up').checked)+'_'+limbasGetBoolVal(this.checked);\" $CHECKEDDOWN>
		</TD>
		<TD><i>".$lang[2517]."</i></TD>";
    }else{
        echo "<TD colspan=\"2\"></TD>";
    }
    echo "<TD style=\"width:25px\"><i class=\"lmb-icon lmb-eye\"></i></TD>
	<TD style=\"width:25px\"><i class=\"lmb-icon lmb-pencil\"></i></TD>
	<TD style=\"width:25px\"><i class=\"lmb-icon lmb-trash\"></i></TD>
	</TR>
	";

	$sqlquery = "SELECT DISTINCT LMB_GROUPS.GROUP_ID,LMB_RULES_DATASET.EDIT,LMB_RULES_DATASET.DEL FROM LMB_GROUPS,LMB_RULES_DATASET WHERE LMB_GROUPS.GROUP_ID = LMB_RULES_DATASET.GROUPID AND LMB_RULES_DATASET.TABID = $gtabid AND LMB_RULES_DATASET.DATID IN ($d_datidlist)";
	$rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
	if(!$rs) {$commit = 1;}
	while(lmbdb_fetch_row($rs)) {
		$key = lmbdb_result($rs,"GROUP_ID");
		if(lmbdb_result($rs,"EDIT")){$edit = "CHECKED";}else{$edit = "";}
		if(lmbdb_result($rs,"DEL")){$del = "CHECKED";}else{$del = "";}
		echo "<TR><TD><i class=\"lmb-icon lmb-group\"></i></TD><TD>".$groupdat["name"][$key]."</TD>
		<TD><i class=\"lmb-icon lmb-check-alt\"></i></TD>
		<TD><INPUT TYPE=\"checkbox\" CLASS=\"checkb\" OnClick=\"limbasAjaxGtabRules('$gtabid','$use_records','".$key."_g_e_'+limbasGetBoolVal(this.checked)+lmb_hirarrules,'','');\" $edit></TD>
		<TD><INPUT TYPE=\"checkbox\" CLASS=\"checkb\" OnClick=\"limbasAjaxGtabRules('$gtabid','$use_records','".$key."_g_d_'+limbasGetBoolVal(this.checked)+lmb_hirarrules,'','');\" $del></TD>
		<TD><i class=\"lmb-icon lmb-erase\" style=\"cursor:pointer\" OnClick=\"limbasAjaxGtabRules('$gtabid','$use_records','".$key."_g_r_'+lmb_hirarrules,'','');\"></i></TD>

		</TR>";
	}

	$sqlquery = "SELECT DISTINCT LMB_USERDB.USER_ID,LMB_RULES_DATASET.EDIT,LMB_RULES_DATASET.DEL,LMB_RULES_DATASET.EDITUSER FROM LMB_USERDB,LMB_RULES_DATASET WHERE LMB_USERDB.USER_ID = LMB_RULES_DATASET.USERID AND LMB_RULES_DATASET.TABID = $gtabid AND LMB_RULES_DATASET.DATID IN ($d_datidlist)";
	$rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
	if(!$rs) {$commit = 1;}
	while(lmbdb_fetch_row($rs)) {
		$key = lmbdb_result($rs,"USER_ID");
		if($session["user_id"] == $key){continue;}
		if($key == lmbdb_result($rs,"EDITUSER")){$pic = "lmb-user-plus";}else{$pic = "lmb-user-alt";}
		if(lmbdb_result($rs,"EDIT")){$edit = "CHECKED";}else{$edit = "";}
		if(lmbdb_result($rs,"DEL")){$del = "CHECKED";}else{$del = "";}
		echo "<TR><TD><i class=\"lmb-icon $pic\"></i></TD><TD TITLE=\"".$userdat["groupname"][$key]."\">".$userdat["bezeichnung"][$key]."</TD>
		<TD><i class=\"lmb-icon lmb-check-alt\"></i></TD>
		<TD><INPUT TYPE=\"checkbox\" CLASS=\"checkb\" OnClick=\"limbasAjaxGtabRules('$gtabid','$use_records','".$key."_u_e_'+limbasGetBoolVal(this.checked)+lmb_hirarrules,'','');\" $edit></TD>
		<TD><INPUT TYPE=\"checkbox\" CLASS=\"checkb\" OnClick=\"limbasAjaxGtabRules('$gtabid','$use_records','".$key."_u_d_'+limbasGetBoolVal(this.checked)+lmb_hirarrules,'','');\" $del></TD>
		<TD><i class=\"lmb-icon lmb-erase\" style=\"cursor:pointer\" OnClick=\"limbasAjaxGtabRules('$gtabid','$use_records','".$key."_u_r_'+lmb_hirarrules,'','');\"></i></TD>

		</TR>";
	}


    echo "</TABLE>";
    echo "</DIV>";

    pop_right();
    pop_bottom();
}




/**
 * # Gtab Userrules - show userlist
 *
 * @param unknown_type $params
 * @return unknown
 */
function dyns_showGtabRulesUsersearch($params){
    global $farbschema;
    global $userdat;
    global $db;
    global $gtab;
    global $session;

    $gtabid = $params["gtabid"];
    $use_records = $params["use_records"];
    $searchvalue = parse_db_string($params["searchvalue"],50);

    if((!$gtab["edit_userrules"][$gtabid] AND !$gtab["edit_ownuserrules"][$gtabid]) OR !$searchvalue OR !$use_records){return false;}

    $recordlist = explode(";",$use_records);
    foreach ($recordlist as $key => $value){
        if(is_numeric($value)){
            $records[0] = $value;
        }else{
            $val = explode("_",$value);
            $records[] = $val[0];
        }
    }

    if($grouplist = lmbUserGroupTree($session["group_id"],1) AND $session["group_id"] != 1){
        $grpsql_u = "AND LMB_USERDB.GROUP_ID IN (".implode(",",$grouplist).")";
    }

    # only for one record
    if(count($records) == 1){
        $sqlquery = "SELECT LMB_USERDB.USER_ID,LMB_RULES_DATASET.KEYID
			FROM
			LMB_USERDB LEFT JOIN LMB_RULES_DATASET ON(LMB_USERDB.USER_ID = LMB_RULES_DATASET.USERID AND LMB_RULES_DATASET.TABID = ".parse_db_int($gtabid,5)." AND LMB_RULES_DATASET.DATID = ".$records[0].")
			WHERE LMB_USERDB.DEL = ".LMB_DBDEF_FALSE." ".$grpsql_u;
    }else{
        $sqlquery = "SELECT USER_ID, 0 AS KEYID FROM LMB_USERDB WHERE LMB_USERDB.DEL = ".LMB_DBDEF_FALSE." ".$grpsql_u;
    }
    if($searchvalue != "*"){
        $sqlquery .= " AND (LOWER(LMB_USERDB.USERNAME) LIKE '%".lmb_strtolower($searchvalue)."%' OR LOWER(LMB_USERDB.VORNAME) LIKE '%".lmb_strtolower($searchvalue)."%' OR LOWER(LMB_USERDB.NAME) LIKE '%".lmb_strtolower($searchvalue)."%')";
    }

    pop_closetop('ContainerGtabRulesUsersearch');
    pop_left();

	$rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
	if(!$rs) {$commit = 1;}
	while(lmbdb_fetch_row($rs)) {
		$key = lmbdb_result($rs,"USER_ID");
		if($key == $session["user_id"]){continue;}
		if(lmbdb_result($rs,"KEYID")){
			echo "<span style=\"color:#AAAAAA\">".$userdat["bezeichnung"][$key]."</spn><br>";
		}else{
			echo "<a href=\"Javascript:limbasAjaxGtabRules('$gtabid','$use_records','".$key."_u_v_'+lmb_hirarrules,'','');\">".$userdat["bezeichnung"][$key]."</a><br>";
		}
	}
	
	pop_right();
	pop_bottom();

}

/**
 * # Gtab Userrules - show grouplist
 *
 * @param unknown_type $params
 * @return unknown
 */
function dyns_showGtabRulesGroupsearch($params){
    global $farbschema;
    global $groupdat;
    global $db;
    global $gtab;
    global $session;

    $gtabid = $params["gtabid"];
    $use_records = $params["use_records"];
    $searchvalue = parse_db_string($params["searchvalue"],50);

    if((!$gtab["edit_userrules"][$gtabid] AND !$gtab["edit_ownuserrules"][$gtabid]) OR !$searchvalue OR !$use_records){return false;}

    $recordlist = explode(";",$use_records);
    foreach ($recordlist as $key => $value){
        if(is_numeric($value)){
            $records[0] = $value;
        }else{
            $val = explode("_",$value);
            $records[] = $val[0];
        }
    }

    if($grouplist = lmbUserGroupTree($session["group_id"],1) AND $session["group_id"] != 1){
        $grpsql_g = "AND LMB_GROUPS.GROUP_ID IN (".implode(",",$grouplist).")";
    }

    # only for one record
    if(count($records) == 1){
        $sqlquery = "SELECT LMB_GROUPS.GROUP_ID,LMB_RULES_DATASET.KEYID FROM LMB_GROUPS 
			LEFT JOIN LMB_RULES_DATASET ON(LMB_RULES_DATASET.GROUPID = LMB_GROUPS.GROUP_ID AND LMB_RULES_DATASET.TABID = ".parse_db_int($gtabid,5)." AND LMB_RULES_DATASET.DATID = ".$records[0].")
			WHERE LMB_GROUPS.DEL = ".LMB_DBDEF_FALSE." ".$grpsql_g;
        #$sqlquery = "SELECT LMB_GROUPS.GROUP_ID,LMB_RULES_DATASET.KEYID FROM LMB_GROUPS,LMB_RULES_DATASET WHERE LMB_GROUPS.GROUP_ID = LMB_RULES_DATASET.GROUPID(+) AND LMB_RULES_DATASET.TABID(+) = ".parse_db_int($gtabid,5)." AND LMB_RULES_DATASET.DATID(+) = ".$records[0]." AND LMB_GROUPS.DEL = FALSE";
    }else{
        $sqlquery = "SELECT GROUP_ID, 0 AS KEYID FROM LMB_GROUPS WHERE DEL = ".LMB_DBDEF_FALSE." ".$grpsql_g;
    }
    if($searchvalue != "*"){
        $sqlquery .= " AND LOWER(LMB_GROUPS.NAME) LIKE '%".lmb_strtolower($searchvalue)."%'";
    }

    pop_closetop('ContainerGtabRulesGroupsearch');
    pop_left();

	$rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
	if(!$rs) {$commit = 1;}
	while(lmbdb_fetch_row($rs)) {
		$key = lmbdb_result($rs,"GROUP_ID");
		if($key == $session["group_id"]){continue;}
		if(lmbdb_result($rs,"KEYID")){
			echo "<span style=\"color:#AAAAAA\">".$groupdat["name"][$key]."</span><br>";
		}else{
			echo "<a href=\"Javascript:limbasAjaxGtabRules('$gtabid','$use_records','".$key."_g_v_'+lmb_hirarrules,'','');\">".$groupdat["name"][$key]."</a><br>";
		}
	}
	
	pop_right();
	pop_bottom();
}













########## relationtree ##############


function dyns_getRelationTree($params){
    global $gtab;
    global $gfield;
    global $filter;
    global $gverkn;
    global $session;
    global $db;
    static $tree;

    require_once("gtab/gtab.lib");
    require_once("gtab/gtab_type_erg.lib");

    function dyns_RelationTreeSettings($treeid){
        global $db;

        $sqlquery = "SELECT * FROM LMB_TABLETREE WHERE TREEID = ".parse_db_int($treeid);
        $rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
        while(lmbdb_fetch_row($rs)){
            if($md5tab = lmbdb_result($rs,"RELATIONID")){
                $tree['tform'][$md5tab] = lmbdb_result($rs,"TARGET_FORMID");
                $tree['tsnap'][$md5tab] = lmbdb_result($rs,"TARGET_SNAP");
                $tree['display'][$md5tab] = lmbdb_result($rs,"DISPLAY");
                $tree['tfield'][$md5tab] = lmbdb_result($rs,"DISPLAY_FIELD");
                $tree['ttitle'][$md5tab] = lmbdb_result($rs,"DISPLAY_TITLE");
                $tree['tsort'][$md5tab] = lmbdb_result($rs,"DISPLAY_SORT");
                $tree['ticon'][$md5tab] = lmbdb_result($rs,"DISPLAY_ICON");
                $tree['trule'][$md5tab] = lmbdb_result($rs,"DISPLAY_RULE");
            }
        }
        return $tree;
    }

    $gtabid = $params["gtabid"];
    $treeid = str_replace("PH_279_2790","",$params["treeid"]);
    if(!$tree){$tree = dyns_RelationTreeSettings($treeid);}
    $verkn_tabid = $params["verkn_tabid"];
    $verkn_fieldid = $params["verkn_fieldid"];
    $md5tab = $gfield[$verkn_tabid]["md5tab"][$verkn_fieldid];
    if(!$md5tab){$md5tab = "top";}

    echo "<table cellpadding=\"0\" cellspacing=\"0\">\n";

    if($gtabid){

        #$sqlquery = "SELECT * FROM LMB_TABLETREE WHERE TREEID = ".parse_db_int($treeid)." AND ITEMTAB = ".parse_db_int($gtabid)." AND RELATIONID = '$md5tab'";

        $tfield = $tree['tfield'][$md5tab];
        $ttitle = $tree['ttitle'][$md5tab];
        $tsort = $tree['tsort'][$md5tab];
        $tform = $tree['tform'][$md5tab];
        $trule = $tree['trule'][$md5tab];

        if($gverkn[$gtabid]["id"]){
            foreach ($gverkn[$gtabid]["id"] as $rkey => $rval){$of[] = $rkey;}
            $onlyfield[$gtabid] = $of;
        }
        $fieldid = $gfield[$gtabid]["mainfield"];
        $onlyfield[$gtabid][] = $fieldid;
        if(!$tfield){$tfield = $gfield[$gtabid]["fieldkey_id"][$fieldid];}
        if($tfield){$onlyfield[$gtabid][] = $tfield;}
        if($ttitle){$onlyfield[$gtabid][] = $ttitle;}
        if($tsort){$filter["order"][$gtabid][0] = array($gtabid,$tsort);}
        if($params["verkn_ID"]){$verkn = init_relation($params["verkn_tabid"], $params["verkn_fieldid"], $params["verkn_ID"], null, null, 1);}
        $filter['anzahl'][$gtabid] = 'all';
        if(trim($trule)){eval($trule.";");}
        $gresult = get_gresult($gtabid,1,$filter,$gsr,$verkn,$onlyfield,null,$extension);
        $gsum = count($gresult[$gtabid]['id']);
        $rand = mt_rand();

        $bzm = 1;
        if(is_array($gresult[$gtabid]['id'])){
            foreach ($gresult[$gtabid]['id'] as $gkey => $gval){
                if($gverkn[$gtabid]["id"]){$imgpref = "plus";}else{$imgpref = "join";}
                if($bzm == $gsum){$outliner = "bottom";}
                elseif($bzm == 1){$outliner = "top";}
                else{$outliner = "";}

                if($tfield){
                    $fname = "cftyp_".$gfield[$gtabid]["funcid"][$tfield];
                    $gvalue = $fname($gkey,$tfield,$gtabid,3,$gresult,0);
                    if(!trim($gvalue)){$gvalue = "unknown";}
                }elseif ($fieldid){
                    $fname = "cftyp_".$gfield[$gtabid]["funcid"][$fieldid];
                    $gvalue = $fname($gkey,$fieldid,$gtabid,3,$gresult,0);
                    if(!trim($gvalue)){$gvalue = "unknown";}
                }else{
                    $gvalue = "unknown";
                }
                if($ttitle){
                    $fname = "cftyp_".$gfield[$gtabid]["funcid"][$ttitle];
                    $gtitle = htmlentities($fname($gkey,$ttitle,$gtabid,3,$gresult,0),ENT_QUOTES,$GLOBALS["umgvar"]["charset"]);
                }


                echo "<tr>
			<td style=\"width:18px\" valign=\"top\"><img src=\"pic/outliner/".$imgpref.$outliner.".gif\" id=\"lmbTreePlus_".$treeid."_".$gtabid."_".$gval."_".$rand."\" align=\"top\" border=\"0\" style=\"cursor:pointer\" onclick=\"lmb_treeElOpen('$treeid','$gtabid','$gval','$rand');event.stopPropagation();\"></td>
			<td align=\"left\" nowrap><a class=\"lmbFileTreeItem\" onclick=\"lmbTreeOpenData('$gtabid','$gval','".$params["verkn_tabid"]."','".$params["verkn_fieldid"]."','".$params["verkn_ID"]."','$tform')\" title=\"".$gtitle."\">&nbsp;".$gvalue."</a></td>
			</tr>
			<tr style=\"display:none\" id=\"lmbTreeEl_".$treeid."_".$gtabid."_".$gval."_".$rand."\"><td colspan=\"2\" align=\"left\"><table cellpadding=\"0\" cellspacing=\"0\">
			";

                $ssum = count($gverkn[$gtabid]["id"]);
                $sbzm = 1;
                if($gverkn[$gtabid]["id"]){
                    foreach ($gverkn[$gtabid]["id"] as $rkey => $rval){
                        if($gfield[$gtabid]["verkntabletype"][$rkey] == 2){continue;}
                        if($tree['display'][$gfield[$gtabid]["md5tab"][$rkey]]){continue;}
                        if($gresult[$gtabid][$rkey][$gkey]){$simgpref = "plusonly";}else{$simgpref = "hline";}
                        if($tree['ticon'][$gfield[$gtabid]["md5tab"][$rkey]]){
                            $icon = $tree['ticon'][$gfield[$gtabid]["md5tab"][$rkey]];
                        }else{
                            $icon = "lmb-folder-closed";
                        }

                        echo "<tr>
				<td style=\"width:18px\"><img src=\"pic/outliner/join.gif\" valign=\"top\"></td>
				<td style=\"width:18px\"><img src=\"pic/outliner/".$simgpref.".gif\" align=\"top\" style=\"cursor:pointer\" id=\"lmbTreeSubPlus_".$treeid."_".$rval."_".$gval."_".$rand."\" onclick=\"lmb_treeSubOpen('$treeid','$rval','$gval','$rand','$gtabid','$rkey');event.stopPropagation();\"></td>
				<td style=\"width:18px\"><i class=\"lmb-icon $icon\" align=\"top\" border=\"0\" id=\"lmbTreeSubBox_".$treeid."_".$rval."_".$gval."_".$rand."\"></i></td>
				<td nowrap align=\"left\">&nbsp;<b><a class=\"lmbFileTreeItem\" onclick=\"lmbTreeOpenTable('$rval','$gtabid','$rkey','$gval');event.stopPropagation();\">".$gfield[$gtabid]["spelling"][$rkey]."</a></b></td>
				</tr>
				<tr style=\"display:none\" id=\"lmbTreeTR_".$treeid."_".$rval."_".$gval."_".$rand."\">
				<td style=\"background-image:url(pic/outliner/line.gif);background-repeat:repeat-y;\"></td>
				<td></td>
				<td colspan=\"2\"><div id=\"lmbTreeDIV_".$treeid."_".$rval."_".$gval."_".$rand."\"></div></td>
				</tr>
				";

                        $sbzm++;
                    }}

                echo "</table></td></tr>";

                $bzm++;

            }}

    }

    echo "</table>\n";

}



/**
 * writte the preview of the messages for the multiframe menu
 *
 * @param unknown_type $gtabid
 */
function dyns_colorSelect($params){
    pop_color("limbasColorSelectSet","'".$params["formname"]."','".$params["gtabid"]."','".$params["fieldid"]."','".$params["id"]."'",$params["container"]);
}




########## multiframe ##############

function dyns_multiframePreview($params)
{

    global $session;
    echo $params["id"]."#L#";

    if($params["limbasMultiframeItem"]=="Message" AND $params["gtabid"]){
        messagePreview($params["gtabid"]);
    }elseif($params["limbasMultiframeItem"]=="Calendar" AND $params["gtabid"]){
        calendarPreview($params["gtabid"]);
    }elseif($params["limbasMultiframeItem"]=="Explorer"){
        filesFavoritePreview($session["user_id"],$params);
    }elseif($params["limbasMultiframeItem"]=="Reminder"){
        reminderPreview($session["user_id"],$params);
    }elseif($params["limbasMultiframeItem"]=="Workflow"){
        workflowPreview($session["user_id"]);
    }elseif($params["limbasMultiframeItem"]=="Kanban" AND $params["gtabid"]){
        kanbanPreview($params["gtabid"]);
    }elseif(function_exists("LmbExt_".$params["limbasMultiframeItem"])){
        $func = "LmbExt_".$params["limbasMultiframeItem"];
        $func($params);
    }

    #}elseif($params["limbasMultiframeItem"]=="myWorkflow"){
    #	myworkflowPreview($session["user_id"]);
    #}
}

/**
 * write the preview of the files favorites for the multiframe menu
 *
 * @param unknown_type $userid
 */
function filesFavoritePreview($userid,$params){
    global $db;
    global $session;
    global $lang;
    global $umgvar;
    global $filestruct;

    $dropitem = $params["dropitem"];

    $sqlquery = "SELECT DISTINCT LDMS_FILES.NAME,LDMS_FILES.SIZE,LDMS_FAVORITES.FILE_ID,LDMS_FAVORITES.ERSTDATUM,LMB_MIMETYPES.PIC FROM LDMS_FAVORITES,LDMS_FILES,LMB_MIMETYPES WHERE LDMS_FAVORITES.FILE_ID = LDMS_FILES.ID AND LDMS_FILES.MIMETYPE = LMB_MIMETYPES.ID AND LDMS_FAVORITES.USER_ID=".$userid." AND FOLDER = ".LMB_DBDEF_FALSE." ORDER BY ERSTDATUM";
    $rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
    if(lmbdb_fetch_row($rs)){$has_row = 1;}

    $sqlquery1 = "SELECT DISTINCT FILE_ID,ERSTDATUM FROM LDMS_FAVORITES WHERE LDMS_FAVORITES.USER_ID=".$userid." AND FOLDER = ".LMB_DBDEF_TRUE." ORDER BY ERSTDATUM";
    $rs1 = lmbdb_exec($db,$sqlquery1) or errorhandle(lmbdb_errormsg($db),$sqlquery1,$action,__FILE__,__LINE__);
    if(lmbdb_fetch_row($rs1)){$has_row1 = 1;}

    if($has_row OR $has_row1){

        if(!$filestruct){
            require_once("extra/explorer/filestructure.lib");
            get_filestructure();
        }

        echo "<TABLE width=\"100%\" cellspan=0 cellspacing=0 border=0>";

		if($has_row1){
			$maxCount = $umgvar["favorite_limit"];
			while(lmbdb_fetch_row($rs1) AND $maxCount>0){
				$maxCount--;
				if($dropitem == "f".lmbdb_result($rs1,"FILE_ID") AND $dropitem){
					$sqlquery0 = "DELETE FROM LDMS_FAVORITES WHERE FILE_ID = ".parse_db_int(lmbdb_result($rs1,"FILE_ID"))." AND USER_ID=".$userid." AND FOLDER = ".LMB_DBDEF_TRUE;
					$rs0 = lmbdb_exec($db,$sqlquery0) or errorhandle(lmbdb_errormsg($db),$sqlquery0,$action,__FILE__,__LINE__);
				}else{
					echo "<TR><TD><A OnClick=\"parent.main.document.location.href='main.php?action=explorer&LID=".lmbdb_result($rs1,"FILE_ID")."';\">";
					echo "<DIV STYLE=\"width:90%;overflow:hidden;font-size:;color:blue;cursor:pointer\"><i class=\"lmb-icon lmb-folder-closed\" BORDER=\"0\"></i>&nbsp;".string_dispSubstr($filestruct["name"][lmbdb_result($rs1,"FILE_ID")],20)."&nbsp;</DIV></A></TD>";
					echo "<TD ALIGN=\"RIGHT\" STYLE=\"width:10%;overflow:hidden\"><i class=\"lmb-icon lmb-close-alt\" BORDER=\"0\" STYLE=\"cursor:pointer\" OnClick=\"limbasMultiframePreview(".$params["id"].",'Explorer',null,'f".lmbdb_result($rs1,"FILE_ID")."');\"></i></TD>";
					echo "</TR>";
				}
			}
		}

		if($has_row){
			$maxCount = $umgvar["favorite_limit"];
			while(lmbdb_fetch_row($rs) AND $maxCount>0){
				$maxCount--;
				if($dropitem == "d".lmbdb_result($rs,"FILE_ID") AND $dropitem){
					$sqlquery0 = "DELETE FROM LDMS_FAVORITES WHERE FILE_ID = ".parse_db_int(lmbdb_result($rs,"FILE_ID"))." AND USER_ID=".$userid." AND FOLDER = ".LMB_DBDEF_FALSE;
					$rs0 = lmbdb_exec($db,$sqlquery0) or errorhandle(lmbdb_errormsg($db),$sqlquery0,$action,__FILE__,__LINE__);
				}else{
					echo "<TR><TD TITLE=\"".file_size(lmbdb_result($rs,"SIZE"))."\"><A OnClick=\"open('main.php?action=explorer_detail&ID=".lmbdb_result($rs,"FILE_ID")."' ,'Info','toolbar=0,location=0,status=0,menubar=0,scrollbars=1,resizable=1,width=700,height=650');\">";
					echo "<DIV STYLE=\"width:100%;overflow:hidden;font-size:;color:blue;cursor:pointer\"><IMG SRC=\"pic/fileicons/".lmbdb_result($rs,"PIC")."\" BORDER=\"0\">&nbsp;".string_dispSubstr(lmbdb_result($rs,"NAME"),20)."&nbsp;</DIV></A></TD>";
					echo "<TD ALIGN=\"RIGHT\" STYLE=\"width:10%;overflow:hidden\"><i class=\"lmb-icon lmb-close-alt\"  STYLE=\"cursor:pointer\" OnClick=\"limbasMultiframePreview(".$params["id"].",'Explorer',null,'d".lmbdb_result($rs,"FILE_ID")."');\"></i></TD>";
					echo "</TR>";
				}
			}
		}

        echo "</TABLE>";
        echo "";
    }else{
        echo "<span>&nbsp;".$lang[98]."</span>";
    }

}


/**
 * write the preview of the messages for the multiframe menu
 *
 * @param unknown_type $userid
 */
function calendarPreview($gtabid){
    global $session;
    global $lang;
    global $umgvar;
    global $gtab;
    global $gfield;

    require_once("gtab/gtab.lib");
    require_once("extra/calendar/fullcalendar/cal.dao");
    $lmb_calendar = new lmb_calendar($gtabid);

    # EXTENSIONS
    if($GLOBALS["gLmbExt"]["ext_calendar.inc"]){
        foreach ($GLOBALS["gLmbExt"]["ext_calendar.inc"] as $key => $extfile){
            require_once($extfile);
        }
    }

    $lmbCalFields = $lmb_calendar->lmb_getlmbCalFields();
    $lmbCalFieldsID = $lmb_calendar->lmb_getlmbCalFieldsID();

    $onlyfield[$gtabid] = array($gfield[$gtabid]["argresult_name"]["ID"],$lmbCalFieldsID['STARTSTAMP'],$lmbCalFieldsID['SUBJECT']);
    $filter["order"][$gtabid][0] = array($gtabid,$lmbCalFieldsID['STARTSTAMP'],"ASC");
    #$extension["where"][] = "ERSTUSER = ".$session["user_id"];
    $extension["where"][] = "( ".$lmbCalFields['STARTSTAMP']." >= '".convert_stamp(local_stamp(2)-10800)."' AND ".$lmbCalFields['STARTSTAMP']." <= '".convert_stamp(local_stamp(2)+108000)."')";

    ######### gresult Abfrage ##########
    $gresult = get_gresult($gtabid,1,$filter,null,null,$onlyfield,null,$extension);

    $maxCount = $gresult[$gtabid]["res_count"];

    if($gresult[$gtabid]["res_count"] > 0){
        echo "<TABLE width=\"100%\" cellspan=0 cellspacing=0 border=0>";
        for ($i=0;$i<$maxCount;$i++) {

            $stst = get_stamp($gresult[$gtabid][$lmbCalFieldsID['STARTSTAMP']][$i]);
            $st = local_stamp(2);
            $diff = (($st-$stst)/60);

            if($diff > 0){$tpo=0;}else{$tpo=1;}
            $diff = abs($diff);

            # minutes
            if($diff <= 60){
                if($tpo AND floor($diff) >= 15){$time = "<span style=\"color:orange;\">in ".floor($diff)." ".$lang[1980]."</span>";}
                elseif(floor($diff) == 0){$time = "<span style=\"color:red;\"><i class=\"lmb-icon lmb-icon-8 lmb-feed\" border=\"0\" style=\"vertical-align:bottom\"></i>&nbsp;<b>".$lang[2762]."</b></span>";}
                else{$time = "<span style=\"color:grey;\">".$lang[2766]." ".floor($diff)." ".$lang[1980]."</span>";}
                # hours
            }elseif($diff <= 1440){
                if($tpo AND $diff > 180){$tpo = "<span style=\"color:blue;\">".$lang[1975]." ";}elseif($tpo){$tpo = "<span style=\"color:purple;\">".$lang[1975]." ";}else{$tpo = "<span style=\"color:grey;\">".$lang[2766]." ";}
                $time = $tpo.floor($diff/60)." ".$lang[1981]." ".floor($diff-floor($diff/60)*60)." ".$lang[1980]."</span>";
                # days
            }else{
                if($tpo){$tpo = "<span style=\"color:blue;\">".$lang[1975]." ";}else{$tpo = "<span style=\"color:grey;\">".$lang[2766]." ";}
                $time = $tpo.floor($diff/1440)." ".$lang[1982]." ".floor((($diff/1440)-floor($diff/1440))*60)." ".$lang[1981]."</span>";
            }

            echo "<TR><TD style=\"font-style:italic;\" TITLE=\"".get_date($gresult[$gtabid][$lmbCalFieldsID['STARTSTAMP']][$i],2)."\"><A HREF=\"Javascript:open_quickdetail($gtabid,".$gresult[$gtabid]["id"][$i].",'')\">";
            echo "<DIV STYLE=\"width:100%;overflow:hidden;cursor:pointer;\">".$time."&nbsp;</DIV></A></TD></TR>";
            echo "<TR><TD style=\"padding-left:20px;cursor:default\">".string_dispSubstr($gresult[$gtabid][$lmbCalFieldsID['SUBJECT']][$i],50)."&nbsp;</TD></TR>";
            if($i > $umgvar["preview_maxcount"]){break;}
        }
        echo "</TABLE>";

    }else{
        echo "<span>&nbsp;".$lang[98]."</span>";
    }

}

/**
 * write the preview of the assigned kanban cards for the multiframe menu
 *
 */
function kanbanPreview($gtabid){
    global $lang;
    global $session;
    global $umgvar;

    require_once("gtab/gtab.lib");
    require_once("extra/kanban/kanban.dao");

    $lmb_kanban = new lmb_kanban($gtabid);

    # EXTENSIONS
    if($GLOBALS["gLmbExt"]["ext_kanban.inc"]){
        foreach ($GLOBALS["gLmbExt"]["ext_kanban.inc"] as $key => $extfile){
            require_once($extfile);
        }
    }

    echo $lmb_kanban->get_kanbanPreview();

}


/**
 * write the preview of the reminder for the multiframe menu
 *
 * @param unknown_type $userid
 */
function reminderPreview($userid,$params)
{
    global $db;
    global $session;
    global $tabgroup;
    global $gtab;
    global $lang;
    global $userdat;
    global $umgvar;
    global $greminder;

    require_once("gtab/gtab.lib");

    echo "<span>";
    $dropitem = $params["dropitem"];
    $category = $params["category"];
    $gtabid = $params["gtabid"];

    if($dropitem){
        lmb_dropReminder($dropitem,$gtabid,$category);
    }

    if($category){
        $filter["reminder"][$gtabid] = $category;
        $gresult = get_gresult($gtabid,1,$filter,null,null,'ID');
        $gresult = $gresult[$gtabid];
    }else{
        $gresult = lmb_getReminderAktive($gtabid,$params["id"]);
    }


    if($category){$gfrist = $category;}else{$gfrist = 'true';}

    $currentTabId = 0;

    if($gresult["LMBREM_ID"]){
        echo "<TABLE width='100%'>";
        foreach ($gresult["LMBREM_ID"] as $key => $value){
            $found=true;
            if($category){
                $gresult["LMBREM_TABID"][$key] = $gtabid;
            }elseif($currentTabId!=$gresult["LMBREM_TABID"][$key]){
                echo "<TR><TD colspan=\"2\">";
                print_r($gtab[1]);
                echo "<I><A TARGET=\"main\" HREF=\"main.php?&action=gtab_erg&source=root&gtabid=".$gresult["LMBREM_TABID"][$key]."&gfrist=true\">".$gtab["desc"][$gresult["LMBREM_TABID"][$key]]."</A></I>";
                echo "</TD></TR>";
                $currentTabId=$gresult["LMBREM_TABID"][$key];
            }
            $datum = lmb_substr(get_date($gresult["LMBREM_FRIST"][$key],0),0,16);
            echo "<TR><TD style=\"padding-left:20px\" TITLE=\"".$gresult["LMBREM_CONTENT"][$key]."\">";
            echo "<A TARGET=\"main\" HREF=\"main.php?&action=gtab_change&gtabid=".$gresult["LMBREM_TABID"][$key]."&ID=".$gresult["LMBREM_DATID"][$key]."&gfrist=$gfrist&form_id=".$greminder[$gtabid]["formd_id"][$category]."&wfl_id=".$gresult["LMBREM_WFLID"][$key]."\" style=\"color:green\">";
            echo $datum."</A>&nbsp;";
            if($gresult["LMBREM_USER"][$key] AND $gresult["LMBREM_USER"][$key] != $session["user_id"]){echo "<i class=\"lmb-icon lmb-tag\" title=\"".$userdat["bezeichnung"][$gresult["LMBREM_USER"][$key]]."\"></i>";}
            echo "<br>";
            if($gresult["LMBREM_DESCR"][$key]){echo "<i style=\"color:grey\">".lmb_substr($gresult["LMBREM_DESCR"][$key],0,25)."</i>";}
            elseif($gresult["LMBREM_CONTENT"][$key]){echo "<span style=\"color:grey\">".lmb_substr($gresult["LMBREM_CONTENT"][$key],0,25)."</span>";}
            echo "</TD>";
            if(!$gresult["LMBREM_WFLINST"][$key]){
                echo "<TD VALIGN=\"TOP\" ALIGN=\"RIGHT\" STYLE=\"width:10%;overflow:hidden\"><i class=\"lmb-icon lmb-close-alt\" BORDER=\"0\"  STYLE=\"cursor:pointer\" OnClick=\"limbasMultiframePreview(".$params["id"].",'Reminder',null,'".$gresult["LMBREM_ID"][$key]."',".$gresult["LMBREM_TABID"][$key].",'category=$category');\"></i></TD>";
            }
            echo "</TD></TR>";
        }
        echo "</TABLE>";
    }else{
        echo '&nbsp;'.$lang[98];
    }


    echo "</span>";
}


/**
 * write the preview of the messages for the multiframe menu
 *
 * @param unknown_type $gtabid
 */
function messagePreview($gtabid){
    require_once('extra/messages/preview.php');
    echo "</span>";
}

/**
 * write the preview of the workflow actions for the multiframe menu
 *
 * @param unknown_type $userid
 *//*
function workflowPreview($params)
{
	global $session;
	global $gfield;
	global $lang;
	global $glwfs;


	require_once("extra/workflow/lwf.lib");

	$lwf_id = $params["lwf_id"];
	$userid = $params["userid"];

	$lwf_id = 1;

	if($myTasks = LWF_GetWfAction($lwf_id,$session["user_id"],array("open","in work"))){

		echo "<DIV>";
		echo "<TABLE width='100%'>";

		foreach ($myTasks as $prkey => $prvalue){

			echo "<TR><TD colspan=\"2\">\n";
			echo $glwfs[$lwf_id]["name"][$prkey];
			echo "</TD></TR>\n";

			foreach($prvalue["id"] as $keyTask => $valueTask)
			{
				echo "<TR><TD title=\"".$prvalue["date"][$keyTask]."\">\n";
				echo "&nbsp;&nbsp;&nbsp;<a target=\"main\" href=\"".$prvalue["link"][$keyTask]."\">* ".$prvalue["name"][$keyTask]."</a> <span style='font-size:9px'><I>(".lmb_substr($prvalue["date"][$keyTask],0,10).") ".$prvalue["status"][$keyTask]."</I></span>\n";
				echo "</TD></TR>\n";
			}
		}

	}else
	{
		echo "<TR><TD colspan='2'>".$lang[2058]."</TD></TR>\n";
	}

	echo "<TABLE>";
	echo "</DIV>";
}
*/

/**
 * ajax events for fullcalendar
 *
 * @param unknown_type $params
 */
function dyns_fullcalendar(&$params)
{
    global $gfield;
    global $session;
    global $lang;
    global $gtab;
    global $gfield;
    global $gform;
    global $gformlist;
    global $lmbCalFields;
    global $lmbCalFieldsID;
    global $gsr;

    $gtabid = $params["gtabid"];

    // register variables
    if ($params["gs"]) {
        $gsr = $params["gs"];
    }
    if ($params['filter_reset']) {
        unset($gsr[$gtabid]);
        unset($gsr[$gfield[$gtabid]['verkntabid'][$gtab["params1"][$gtabid]]]);
    }

    //error_log(print_r($gsr,1));

    require_once("gtab/gtab.lib");
    require_once("extra/calendar/fullcalendar/cal.dao");
    require_once('extra/calendar/fullcalendar/cal_dyns.php');

}


/**
 * ajax events for kanban
 *
 * @param unknown_type $params
 */
function dyns_kanban(&$params)
{
    global $gfield;
    global $session;
    global $lang;
    global $gtab;
    global $gfield;
    global $gform;
    global $gformlist;
    global $lmbCalFields;
    global $lmbCalFieldsID;
    global $gsr;

    $gtabid = $params["gtabid"];

    // register variables
    if ($params["gs"]) {
        $gsr = $params["gs"];
    }
    if ($params['filter_reset']) {
        unset($gsr[$gtabid]);
        unset($gsr[$gfield[$gtabid]['verkntabid'][$gtab["params1"][$gtabid]]]);
    }

    require_once("gtab/gtab.lib");
    require_once("extra/kanban/kanban.dao");
    require_once('extra/kanban/kanban_dyns.php');

}


/**
 * ajax mailsystem
 *
 * @param unknown_type $params
 * @return unknown
 */
function dyns_messages($params){
    require("extra/messages/index.php");
    $GLOBALS["noencode"] = 1;
}

/**
 * calls extension function that handles number dialing
 * @param array $params: ['phoneNr' => string]
 * @return javascript to be executed, surrounded by script tags
 */
function dyns_dialNumber($params) {
    global $umgvar;

    $fnc = "LmbDialNumber_".$umgvar["dial_engine"];
    if(function_exists($fnc)){
        echo $fnc($params['phoneNr']);
    }else{
        echo '<script>alert("no phone is installed!");</script>';
    }
}

# add menu item to favorites of user
function dyns_addToFavorites($params) {
    global $session;
    global $action;
    global $db;

    # check param
    $type = $params['type'];
    $tabid = $params['tabid'];
    $id = $params['id'];
    if (!$type || !$tabid)
        return;
    $target = $type . ";" . $tabid . ";" . $id;

    # get settings
    $sqlquery = 'SELECT M_SETTING FROM LMB_USERDB WHERE USER_ID = ' . $session['user_id'];
    $rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
    if($m_setting = lmbdb_result($rs, "M_SETTING")){
        $m_setting = unserialize($m_setting);
    }

    # change settings for given menu item
    if ($m_setting['fav'][$target]) {
        unset ($m_setting['fav'][$target]);
    } else {
        $m_setting['fav'][$target] = 1;
    }

    # store settings
    if(is_array($m_setting)){
        $prepare_string = 'UPDATE LMB_USERDB SET M_SETTING = ? WHERE USER_ID = ' . $session['user_id'];
        lmb_PrepareSQL($prepare_string, array(serialize($m_setting)), __FILE__, __LINE__);
    }

    # refresh session
    $session['m_setting'] = $m_setting;
}

/**
 * Saves the width of the gtab window to the database.
 * Can be extended to save other user vars
 * @param $params array of gtabid and filter_gwidth
 */
function dyns_saveViewSettings($params) {
	global $filter;

	if (!$params['gtabid'] or !is_numeric($params['gtabid'])) {
		return;
	}
	$gtabid = $params['gtabid'];

    if ($params['filter_gwidth']) {
        $filter_gwidth = $params['filter_gwidth'];
    }
    require_once('gtab/gtab_register.lib');
    require_once('gtab/gtab.lib');
    save_viewSettings(null, $gtabid);
}


/**
 * Changes the current tenant
 * @param $params array of gtabid and filter_gwidth
 */
function dyns_setMultitenant($params) {
    global $session;
    global $db;

    if (!$params['mid'] || !is_numeric($params['mid']) || !in_array($params['mid'],$session["multitenant"])) {
        return;
    }
    $setMultitenant = $params['mid'];

    $sqlquery = "SELECT MULTITENANT FROM LMB_USERDB WHERE USER_ID = ".$session['user_id'];
    $rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
    $mvalue = explode(',',lmbdb_result($rs, "MULTITENANT"));
    $mvalue = $mvalue[0].','.$setMultitenant;

    $sqlquery = "UPDATE LMB_USERDB SEt MULTITENANT = '$mvalue' WHERE USER_ID = ".$session['user_id'];
    $rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);

    $_SESSION['session']["mid"] = $setMultitenant;
    $session["mid"] = $setMultitenant;
}

# Buffer
ob_start();

# --- Funktions-Aufruf -----------
$actid = $_REQUEST["actid"];
if($actid AND function_exists("dyns_".$actid)){

    #if($actid=="14_b" || $actid== "11_a" || $actid== "explorerRelationSearch"){
    #	eval("dyns_".$actid."(\$form_value,\$form_name,\$par1,\$par2,\$par3,\$par4,\$par5,\$par6,\$gformid,\$formid,\$nextpage);");
    #}else{
    if($_REQUEST){
        foreach ($_REQUEST as $keyR=>$valR){
            if($keyR AND $valR){
                if(is_string($valR)){
                    $_REQUEST[$keyR] = lmb_utf8_decode($valR);
                }elseif(is_array($valR)){
                    $_REQUEST[$keyR] = lmb_arrayDecode($valR,1);
                }
                $params[$keyR] = $_REQUEST[$keyR];
            }
        }
    }

    $functionName = "dyns_".$actid;
    $functionName($params);
    #}

    # save session variables
    save_sessionvars($GLOBALS["globvars"]);

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

if ($db) {lmbdb_close($db);}

?>
