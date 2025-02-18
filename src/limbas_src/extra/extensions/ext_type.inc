<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */



/* -------------------------- minimized upload view --------------------------------- */
function lmb_minimizedFileUpload($ID,$gresult,$fieldid,$gtabid,$class,$style,$pos,$edittype,$z_index=null,$event=null,$gformid=null,$formid=null,$line=null) {
	global $gtab;
	global $gfield;
	global $umgvar;
	global $lang;
	global $session;
	global $gfile;
	global $filestruct;
	global $LINK;
	global $ffilter;
	global $filter;
	global $gform;

	if(!$ID){return;}

	require_once(COREPATH . 'extra/explorer/explorer_main.lib');
	require_once(COREPATH . 'extra/explorer/filestructure.lib');

	if($style){$style_abs = "STYLE=\"".$pos.$style."\"";}else{$style_abs = "STYLE=\"margin-top:5px;margin-bottom:5px;\"";}

	# Verknüpfungswerte
	$verkn = set_verknpf($gfield[$gtabid]["verkntabid"][$fieldid],$gfield[$gtabid]["verknfieldid"][$fieldid],0,0,0,0,0);
	$verkn["md5tab"] = $gfield[$gtabid]["md5tab"][$fieldid];
	$verkn["uniqueid"] = $gfield[$gtabid]["form_name"][$fieldid];
	$vuniqueid = $gfield[$gtabid]["form_name"][$fieldid]."_".$gformid;
    $LID = $gfield[$gtabid]["file_level"][$fieldid];

	if(!$verkn) {return;}

    # Anzuzeigende Ordner Ebene
    $linklevel = $GLOBALS["temp"];

	if($filter["ext_RelationFields"]['formid'][$vuniqueid]){
	    $LID = $filter["ext_RelationFields"]['formid'][$vuniqueid];
	}elseif ($session["ExtFileManager"]["LID"][$verkn["uniqueid"]]) {
        $LID = $session["ExtFileManager"]["LID"][$verkn["uniqueid"]];
    }

    # get global ffilter
    get_userShow($LID, 1);

    # viewmode
    $show_details = $ffilter['viewmode'][$LID];

    # show files recursiv
    $show_sub = $session["ExtFileManager"]["viewsub"][$verkn["uniqueid"]];
    if (!$show_sub) {
        $show_sub = 0;
    } else {
        $show_sub = 1;
    }

    # default height
    if (!$filter["ext_RelationFields"]["showall"][$vuniqueid]) {
        if ($vgresult[$vgtabid]["res_viewcount"] > 6) {
            $bt = "height:110px;overflow:auto;";
        }
    }

    # order by
    $filter["order"][$LID] = $ffilter["order"][$LID];

    # search
    foreach ($gfile['show'][$LID] as $key1 => $val1) {
        if ($ffilter[$key1][$LID]) {
            $searchfield = explode('_', $key1);
            $filter["gsr"][$searchfield[0]][$searchfield[1]] = $ffilter[$key1][$LID];
        }
    }

    # specific formular styles
    if ($gformid AND $formid) {
        $st = explode(";", $GLOBALS["gform"][$gformid]["style"][$formid]);
        if ($st[24]) { #overflow
            $bt = "height:" . $GLOBALS["gform"][$gformid]["height"][$formid] . "px;overflow:" . $st[24];
        } else {
            $bt = "";
        }
        $style_abs = str_replace(";height:", ";height_:", $style_abs); # workaround to disable height
        # specific rules
        if ($GLOBALS["gform"][$gformid]["parameters"][$formid]) {
            $filter_ = eval($GLOBALS["gform"][$gformid]["parameters"][$formid]);
            if (is_array($filter_)) {
                foreach ($filter_ as $fkey => $fval) {
                    $filter["ext_RelationFields"][$fkey][$vuniqueid] = $fval;
                }
            }
        }
    }

    ##########

    # ----- Ordnerstruktur ------
    get_filestructure();
    $rootlevel = $filestruct["level"][$gfield[$gtabid]["file_level"][$fieldid]];
    $level = $filestruct["level"][$LID];
    $typ = 7;
    $file_path = lmb_getPathFromLevel($level,$LID);
    array_splice($file_path,-4);
    $filter["nolimit"][$LID] = 1;
    $filter["page"][$LID] = 1;
    $filter["viewmode"][$LID] = 5;
    $filter["order"][$LID] = array($gtab["argresult_id"]["LDMS_FILES"].'_7','ASC',$gtab["argresult_id"]["LDMS_FILES"].'_3','DESC');
    $filter["sub"] = $show_sub;
    $verkn["parent_table"] = $gtab["table"][$gtabid];
    $verkn["parent_datid"] = $ID;

    # only images
    #$filter[$gtab["argresult_id"]["FILES"]."_13"][$LID] = array("image");

    # --- Abfrage starten ---
    if($query = get_fwhere($LID,$filter,$typ,$verkn)){
        if(!$ffile = get_ffile($query,$filter,$LID,$typ)){echo "no result for image!";return false;}
    }
    ###########
    if($style){$style = "STYLE=\"border:1px solid grey;cursor:pointer;".$pos.$style."\"";}

    #---- thumbs-Liste -----
    $bzm1 = 0;
    $img_sn = array();
    $width = $gform[$gformid]["width"][$formid];
    $height = $gform[$gformid]["height"][$formid];

    foreach ($ffile['id'] as $fkey => $fID) {
        if ($img2 = lmb_getThumbnail(array($fID, $ffile['secname'][$fkey], $ffile['mimeid'][$fkey], $ffile['thumb_ok'][$fkey], $LID, $ffile['mid'][$fkey]), ($width - 2), $height, 1)) {
            $bzm1++;
            $img_sn[$bzm1] = $img2;
            $img_id[$bzm1] = $fID;
        }
    }

    $fe = $gtabid.'_'.$fieldid.'_'.$ID;
    $fp = $gfield[$gtabid]["form_name"][$fieldid].'_'.$ID;
    $fpo = $fp;
    if(isset($line)){
        $fpo = "element{$formid}_{$line}";
    }

    if(!$bzm1 AND $edittype == 1){
        $openParams = "OnClick=\"lmb_formpic_open('".$fp."','".$gfield[$gtabid]["spelling"][$fieldid]."','$formid','$gformid','$line');event.stopPropagation();\"";
    }

    echo "<DIV ID=\"".$fp."\" data_form_elid=\"$formid\" $style $openParams>";

    #---- Scriptliste aller Bilder -----
    ?>
    <Script language="JavaScript">

    <?php
    echo "var formpicsn_" . $fe . " = new Array();
    ";
    $fieldid1 = 0;
    echo "formpicsn_" . $fe . "[0] = \"#0\";";
    foreach ($img_sn as $key => $value) {
        $fieldid1++;
        echo "formpicsn_" . $fe . "[" . $fieldid1 . "] = \"" . $img_sn[$key] . "#$img_id[$key]\"; ";
    }
    $max = $fieldid1;
    if($bzm1){$min = 1;}else{
        $min = 0;
        if(file_exists(LOCALASSETSPATH.'images/placeholder.png')) {
            $placeholder = 'localassets/images/placeholder.png';
        }
        #else{
        #    $placeholder = 'assets/images/placeholder.png';
            #$placeholderFont = '<i class=\"lmb-icon lmb-icon-8 lmb-user\"></i>';
        #}
    }

    echo "</Script>\n";

    echo "<div style=\"display:none\" id=\"".$fpo."_detail\"><TABLE><TR>
    <TD><i title=\"$lang[2318]\" class=\"lmb-icon lmb-rep-dbnew\" STYLE=\"cursor:pointer;\" onclick=\"LmEx_showUploadField('lmbUploadLayer', '$LID', '".$fe."', '$gtabid', '$fieldid', '$ID');\">
    <TD><i title=\"$lang[2318]\" class=\"lmb-icon lmb-page-delete-alt\" STYLE=\"cursor:pointer;\" OnClick=\"LmExt_Ex_RelationUploadDelete($gtabid,$fieldid,$ID,$LID,'formpicname0_$fe','$max');\"></i></TD>";
    if($img_id[$min]){echo "<TD><i title=\"$lang[2318]\" class=\"lmb-icon lmb-download\" STYLE=\"cursor:pointer;padding-left:20px\" OnClick=\"document.location.href='main.php?&action=download&ID=".$img_id[$min]."&disposition=a';\"></i></TD>";}

    if (!$gfield[$gtabid]["unique"][$fieldid] OR !$LID) {
        echo "<TD width=\"100%\">&nbsp;</TD>
        <TD><i title=\"$lang[857]\" class=\"lmb-icon lmb-first\" STYLE=\"cursor:pointer;\" OnClick=\"lmb_formpicdmove('" . $fe . "','3','$max');\"></i></TD>
        <TD><i title=\"$lang[860]\" class=\"lmb-icon lmb-previous\" STYLE=\"cursor:pointer;font-size:1.5em;\" OnClick=\"lmb_formpicdmove('" . $fe . "','2','$max');\"></i></TD>
        <TD><i title=\"$lang[859]\" class=\"lmb-icon lmb-next\" STYLE=\"cursor:pointer;font-size:1.5em;\" OnClick=\"lmb_formpicdmove('" . $fe . "','1','$max');\"></i></TD>
        <TD><i title=\"$lang[858]\" class=\"lmb-icon lmb-last\" STYLE=\"cursor:pointer;\" OnClick=\"lmb_formpicdmove('" . $fe . "','4','$max');\"></i></TD>
        ";
    }
    echo "</TR>";
    if (!$gfield[$gtabid]["unique"][$fieldid] OR !$LID) {
        echo "<tr id=\"LmEx_Ex_uploadfile_".$fe."\" ><td colspan=\"9\" id=\"LmEx_Ex_uploadfiles_".$fe."\"><td></tr>";
    }
    echo "</TABLE>";

    echo "<div style=\"width:100%;text-align:center;padding-top:10px\"><IMG id=\"formpicname0_$fe\" NAME=\"formpicname0_" . $fe . "\" SRC=\"" . $img_sn[$min] . "\" data-id=\"" . $img_id[$min] . "\"></div>";
    echo "</div>";

    if($bzm1){
        $preview = "OnClick=\"lmb_formpic_preview('main.php?&action=download&ID=".$img_id[$min]."&disposition=a','".$gfield[$gtabid]["spelling"][$fieldid]."','".$filter['ext_RelationFields']['formsize'][$vuniqueid]."')\"";
    }
    echo "<div class=\"lmbImageUpload\">
    <IMG style=\"position:absolute;width:$width;height:$height;\" id=\"formpicname_" . $fe . "\" NAME=\"formpicname_" . $fe . "\" SRC=\"" . $img_sn[$min] . $placeholder . "\" $preview>
    <div class=\"lmbImageUploadOptions\" style=\"position:relative;top:0px;width:100%;height:30px;\">";
    if($bzm1){
        echo "<i class=\"lmb-icon lmb-download cursor-pointer\" style=\"position:absolute;top:5px;left:5px;\" border=\"0\" onclick=\"document.location.href='main.php?&action=download&ID=".$img_id[$min]."&disposition=a';\"></i>";
    }
    if($edittype == 1){
        echo "<i class=\"lmb-icon lmb-cog-alt cursor-pointer\" style=\"position:absolute;top:5px;right:5px;\" border=\"0\" OnClick=\"lmb_formpic_open('".$fpo."','".$gfield[$gtabid]["spelling"][$fieldid]."','$formid','$gformid','$line');event.stopPropagation();\"></i>";
    }
    echo "
    </div>
    </div>
    ";

    echo "</DIV>";

}

/* -------------------------- Extended File-Manager List --------------------------------- */
function lmbc_extendedFileManager($line,$fieldid,$gtabid,$edittyp,&$gresult,$event=null,$gformid=null,$formid=null) {
    global $gfield;
    global $gform;
    static $load_explorer_js;

    $vuniqueid = $gfield[$gtabid]["form_name"][$fieldid]."_".$gformid;
    $ID = $gresult[$gtabid]['id'][$line];

    # specific rules
    if($gform[$gformid]["parameters"][$formid]){
        $filter_ = eval($GLOBALS["gform"][$gformid]["parameters"][$formid]);
        if(is_array($filter_)){
            foreach ($filter_ as $fkey => $fval){
                $filter["ext_RelationFields"][$fkey][$vuniqueid] = $fval;
            }
        }
    }

    // minimized Upload view
    if($filter["ext_RelationFields"]["viewmode"][$vuniqueid] == 'minimized'){

        if(!$load_explorer_js) {
            echo "<script src=\"assets/js/extra/explorer/explorer.js?v=" . $umgvar["version"] . "\"></script>";
            $load_explorer_js = 1;
        }

        echo "<div style=\"width:".$gform[$gformid]["width"][$formid]."px;height:".$gform[$gformid]["height"][$formid]."px;z-index:".$gform[$gformid]["z_index"][$formid]."\">";
        lmb_minimizedFileUpload($ID,$gresult,$fieldid,$gtabid,$class,$style,$pos,$edittyp,$z_index,$event,$gformid,$formid,$line);
        echo "</div>";
    }else{
        $fname = "cftyp_".$gfield[$gtabid]["funcid"][$fieldid];
        $fname($line,$fieldid,$gtabid,$edittyp,$gresult,$gform[$gformid]["event"][$formid],$gformid,$formid);
    }
}


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
	global $filter;
    global $fileupdate;
    global $files_order;

	if(!$ID){return;}

	$vuniqueid = $gfield[$gtabid]["form_name"][$fieldid]."_".$gformid;

	require_once(COREPATH . 'extra/explorer/explorer_main.lib');
	require_once(COREPATH . 'extra/explorer/filestructure.lib');
    #require_once(COREPATH . 'extra/explorer/explorer_main.dao');

	if($style){$style_abs = "STYLE=\"".$pos.$style."\"";}else{$style_abs = "STYLE=\"margin-top:5px;margin-bottom:5px;\"";}

	# Verknüpfungswerte
	$verkn = set_verknpf($gfield[$gtabid]["verkntabid"][$fieldid],$gfield[$gtabid]["verknfieldid"][$fieldid],0,0,0,0,0);
	$verkn["md5tab"] = $gfield[$gtabid]["md5tab"][$fieldid];
	$verkn["uniqueid"] = $gfield[$gtabid]["form_name"][$fieldid];


	if($verkn){

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

        // minimized Upload view
        if($filter["ext_RelationFields"]["viewmode"][$vuniqueid] == 'minimized'){
            lmb_minimizedFileUpload($ID,$gresult,$fieldid,$gtabid,$class,$style,$pos,$edittype,$z_index,$event,$gformid,$formid);
            return;
        }


		$linklevel = $GLOBALS["temp"];

        # Unter-Ordner Ebene händisch ausgewählt (wird über ext_ajax.inc dyns_extFileManager() gesetzt)
        if ($session["ExtFileManager"]["LID"][$verkn["uniqueid"]]) {
            $LID = $session["ExtFileManager"]["LID"][$verkn["uniqueid"]];
        # Root-Ordner aus Formular / Verknüpfungsparameter
        }elseif($filter["ext_RelationFields"]['formid'][$vuniqueid]){
            $LID = $filter["ext_RelationFields"]['formid'][$vuniqueid];
        # Root-Ordner default
        }else{
			$LID = $gfield[$gtabid]["file_level"][$fieldid];
		}

        // order
        foreach ($fileupdate[$LID] as $key => $value) {
            if($value == 'sort'){
                sort_file($key, $files_order[$key]);
            }
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

		/* --- Header ------*/
		echo "<div $style_abs class=\"gtabFringeRelationBody form-legacy\">\n";
		echo "<TABLE BORDER=\"0\" WIDTH=\"100%\" CELLSPACING=\"0\" CELLPADDING=\"0\" class=\"gtabHeaderRelationTAB\">\n";
		echo "<TR class=\"gtabHeaderSymbolTR\">";
	
		if($action == "gtab_change" AND $edittype == 1){
			#$funcpara = rawurlencode("$gtabid,$fieldid,$ID");
			if($LINK[249]){
                # window.open('main.php?&action=mini_explorer&funcname=LmExt_Ex_LinkMiniEx&funcpara=".$funcpara."&typ=1&home_level='+document.getElementById('ExtFileManager_LID_".$gtabid."_".$fieldid."').value ,'_blank','toolbar=0,location=0,status=0,menubar=0,scrollbars=1,resizable=1,width=460,height=320')
				echo "<TD STYLE=\"width:20px;\" ALIGN=\"CENTER\" NOWRAP>
				<i class=\"lmb-icon ".$LINK["icon_url"][249]."\" STYLE=\"cursor:pointer;\" TITLE=\"".$lang[$LINK["desc"][249]]."\" OnClick=\"LmEx_open_miniexplorer($('#LID_{$gtabid}_{$fieldid}').val(),{gtabid:$gtabid,fieldid:$fieldid,ID:$ID},LmExt_Ex_LinkMiniEx);\">
				</i></TD>";
			}
			if($ffilter['search'][$LID] != 1){$st = "opacity:0.3;filter:Alpha(opacity=30)";}else{$st = null;}
			if($LINK[14]){echo "<td style=\"width:20px;\" align=\"center\" nowrap><i class=\"lmb-icon lmb-page-find\" id=\"LmEx_Ex_symbol_".$verkn["uniqueid"]."6\" TITLE=\"".$lang[$LINK["desc"][14]]."\" style=\"cursor:pointer;$st\" OnClick=\"LmExt_Ex_RelationFields($gtabid,$fieldid,'','','$ID','','','','','','','','','1');LmExt_Ex_DisplaySymbols(6,'','".$verkn["uniqueid"]."')\"></i></td><td style=\"width:5px;color:green;\" align=\"center\">|</td>";
			echo "<td STYLE=\"width:15px;\">&nbsp;</td>";}
			if($LINK[119] AND $LID){echo "<TD STYLE=\"width:20px;\" ALIGN=\"CENTER\" NOWRAP><i class=\"lmb-icon-cus lmb-folder-add\" style=\"cursor:pointer;\" onclick=\"document.getElementById('LmEx_Ex_uploadfile_".$gtabid."_".$fieldid."').style.display='none';document.getElementById('LmEx_Ex_createfolder_".$gtabid."_".$fieldid."').style.display='';\" TITLE=\"".$lang[$LINK["desc"][119]]."\"></i></td><TD STYLE=\"width:5px;color:green;\" ALIGN=\"CENTER\">|</TD>";}
			if($LINK[128] AND $LID){echo "<TD STYLE=\"width:20px;\" ALIGN=\"CENTER\" NOWRAP><i class=\"lmb-icon lmb-file-upload\" style=\"cursor:pointer;\" onclick=\"document.getElementById('LmEx_Ex_createfolder_".$gtabid."_".$fieldid."').style.display='none';document.getElementById('LmEx_Ex_uploadfile_".$gtabid."_".$fieldid."').style.display='';LmEx_showUploadField('lmbUploadLayer', document.getElementById('LID_{$gtabid}_{$fieldid}').value, '{$gtabid}_{$fieldid}', '$gtabid', '$fieldid', '$ID');\" TITLE=\"".$lang[$LINK["desc"][128]]."\"></i></td><TD STYLE=\"width:5px;color:green;\" ALIGN=\"CENTER\">|</TD>";}
			if($LINK[171] AND $LID){echo "<TD STYLE=\"width:20px;\" ALIGN=\"CENTER\" NOWRAP><i class=\"lmb-icon-cus lmb-page-delete-fancy\" style=\"cursor:pointer;\" OnClick=\"LmExt_Ex_UnLinkFiles($gtabid,$fieldid,$ID,document.getElementById('LID_".$gtabid."_".$fieldid."').value,'$formid',1)\" TITLE=\"".$lang[$LINK["desc"][171]]."\"></i></td><TD STYLE=\"width:5px;color:green;\" ALIGN=\"CENTER\">|</TD>";}
			if($LINK[158] AND $LID){echo "<TD STYLE=\"width:20px;\" ALIGN=\"CENTER\" NOWRAP><i class=\"lmb-icon-cus lmb-rel-del\" STYLE=\"cursor:pointer;\" TITLE=\"".$lang[$LINK["desc"][158]]."\" BORDER=\"0\" OnClick=\"LmExt_Ex_UnLinkFiles($gtabid,$fieldid,$ID,document.getElementById('LID_".$gtabid."_".$fieldid."').value,'$formid',0)\"></i></TD>";}
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
            pop_header(null, $lang[1634]);
			foreach ($gfile["id"] as $key => $value){
				# Felder ohne Rechte
				if(!$gfile["id"][$key] OR $gtab["argresult_id"]["LDMS_FILES"]."_11" == $key){continue;}
				if($gfile['field_type'][$key] == 100){
                    pop_header(null, $gfile['title'][$key]);
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
				<INPUT TYPE=\"TEXT\" STYLE=\"width:80px;\" NAME=\"".$gfield[$gtabid]["form_name"][$fieldid]."_ds\" OnDblClick=\"LmEx_open_miniexplorer($('#LID_{$gtabid}_{$fieldid}').val(),{gtabid:$gtabid,fieldid:$fieldid,ID:$ID},LmExt_Ex_LinkMiniEx);\" OnKeyup=\"lmbAjax_dynsearch(event,this,'14_b','".$gfield[$gtabid]["form_name"][$fieldid]."','".$verkn['tabid']."','".$verkn["fieldid"]."','$gtabid','$fieldid','$ID','$edittype')\">&nbsp;
				<DIV></DIV><DIV ID=\"".$gfield[$gtabid]["form_name"][$fieldid]."_dsl\" STYLE=\"position:absolute;z-index:999;\"></DIV>
				<INPUT TYPE=\"HIDDEN\" NAME=\"".$gfield[$gtabid]["form_name"][$fieldid]."\"></TD>";
		}else{
			echo "<TD><INPUT TYPE=\"HIDDEN\" NAME=\"".$gfield[$gtabid]["form_name"][$fieldid]."\">&nbsp;</TD>";
		}
		echo "</TR></TABLE>
		<table width=\"100%\">
		<tr id=\"LmEx_Ex_createfolder_".$gtabid."_".$fieldid."\" style=\"display:none;\"><td nowrap>$lang[2231]:</td><td colspan=\"13\"><input type=\"text\" id=\"LmEx_Ex_addfolder_".$gtabid."_".$fieldid."\" style=\"width:200px;\">&nbsp;<input type=\"button\" value=\"$lang[571]\" style=\"cursor:pointer;\" OnClick=\"LmExt_Ex_RelationFields($gtabid,$fieldid,'','$edittype','$ID','','','','','','$formid',document.getElementById('LmEx_Ex_addfolder_".$gtabid."_".$fieldid."').value,'','');\"></td></tr>
		<tr id=\"LmEx_Ex_uploadfile_".$gtabid."_".$fieldid."\" style=\"display:none;\"><td id=\"LmEx_Ex_uploadfiles_".$gtabid."_".$fieldid."\"><td></tr>
		</table>\n";
		
		echo "<div style=\"height:1px;overflow:hidden\"></div>";
		if($gformid){$height = "height:".($GLOBALS["gform"][$gformid]["height"][$formid]+40);}
		echo "<div style=\"width:100%;$bt\" id=\"extRelationFieldsTab_".$gtabid."_".$fieldid."\">";
		
		echo "<TABLE BORDER=\"0\" ID=\"filetab\" CELLSPACING=\"0\" CELLPADDING=\"1\" STYLE=\"width:100%;border-collapse:collapse;\">";
		echo "<input type=\"hidden\" ID=\"LID_".$gtabid."_".$fieldid."\" value=\"$LID\">";

		# ----- Ordnerstruktur ------
		get_filestructure();
		$rootlevel = $filestruct["level"][$gfield[$gtabid]["file_level"][$fieldid]];
		$level = $filestruct["level"][$LID];
		$typ = 7;
		$file_path = lmb_getPathFromLevel($level,$LID);
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

		if($file_path){echo "<TR><TD colspan=\"10\"><DIV style=\"background-color:".$farbschema["WEB12"]."\">&nbsp;<i class=\"lmb-icon lmb-folder\"></i>&nbsp;<INPUT TYPE=\"TEXT\" STYLE=\"border:none;width:94%;height:17px;background-color:".$farbschema["WEB12"].";\" VALUE=\"/".implode("/",$file_path)."\" READONLY></DIV></TD></TR>";}

		if($show_details == 3){
			extendedExplPicShow($ffile,$gtabid,$fieldid,$ID,$gformid,$formid,1,$edittype);
		}elseif($show_details == 4){
			echo "<tr><td>";
			explPicSummery($ffile,$gtabid,$fieldid,$ID,$gformid,$formid);
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

        /* todo
		echo "
		<script language=\"JavaScript\">
		    LmEx_createDropArea($('#extRelationFieldsTab_{$gtabid}_{$fieldid}'), function(files) {
                LmEx_showUploadField('lmbUploadLayer', $LID, '{$gtabid}_{$fieldid}', '{$gtabid}', '{$fieldid}', '{$ID}');
		        LmEx_uploadFilesPrecheck(files, '{$LID}', '{$gtabid}_{$fieldid}', '{$gtabid}', '{$fieldid}', '{$ID}');
		    });
		</script>
		";
        */
	}
}

function extendedExplPicShow(&$ffile,$gtabid,$field_id,$ID,$gformid,$formid,$picshow,$edittype){
	global $lang;
	global $farbschema;
	global $umgvar;
	
	if(!$ID){return;}

	# show element
	if(!$count = lmb_count($ffile["id"])){echo "no result for image!";return false;}

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
		$dest = lmb_getThumbnail(array($cfid,$ffile["secname"][$cfid],$ffile["mimeid"][$cfid],$ffile["thumb_ok"][$cfid],$ffile["level"][$cfid],$ffile["mid"][$cfid]),$width,$height,1);
	}

	echo "<TR><TD><TABLE CELLSPACING=\"0\" CELLPADDING=\"0\"><TR BGCOLOR=\"".$farbschema['WEB3']."\"><TD>&nbsp;</TD><TD><i TITLE=\"$lang[857]\" ALT=\"$lang[857]\" class=\"lmb-icon lmb-first\" STYLE=\"cursor:pointer;\" BORDER=\"0\" OnClick=\"LmExt_Ex_RelationFields($gtabid,$field_id,'3','$edittype',$ID,'','','','',1,'".$formid."','','','')\"></i></TD>";
	echo "<TD><i TITLE=\"$lang[860]\" ALT=\"$lang[860]\" class=\"lmb-icon lmb-previous\" STYLE=\"cursor:pointer;font-size:1.5em;\" BORDER=\"0\" OnClick=\"LmExt_Ex_RelationFields($gtabid,$field_id,'3','$edittype',$ID,'','','','',".($i-1).",'".$formid."','','','')\"></i></TD>";
	echo "<TD><INPUT TYPE=\"TEXT\" NAME=\"ExtFMPicShowCount_".$gtabid."_".$field_id."\" READONLY STYLE=\"width:60px;text-align:center\" VALUE=\"$i\"></TD>";
	echo "<TD><i TITLE=\"$lang[859]\" ALT=\"$lang[859]\" class=\"lmb-icon lmb-next\" STYLE=\"cursor:pointer;font-size:1.5em;\" BORDER=\"0\" OnClick=\"LmExt_Ex_RelationFields($gtabid,$field_id,'3','$edittype',$ID,'','','','',".($i+1).",'".$formid."','','','')\"></i></TD>";
	echo "<TD><i TITLE=\"$lang[858]\" ALT=\"$lang[858]\" class=\"lmb-icon lmb-last\" STYLE=\"cursor:pointer;\" BORDER=\"0\" OnClick=\"LmExt_Ex_RelationFields($gtabid,$field_id,'3','$edittype',$ID,'','','','','$count','".$formid."','','','')\"></i></TD><TD width=90%></TD></TR></TABLE></TD></TR>";
	echo "<TR><TD COLSPAN=\"6\"><IMG SRC=\"".$dest."\" style=\"margin:5px;border:1px solid #CCCCCC;width:".$width."px;height:".$height."px;\" TITLE=\"".$ffile["name"][$cfid]."\"></TD></TR>";
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

	require_once COREPATH . 'gtab/gtab_type_erg.lib';

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

    // form parameter
    $formparams = cftyp_OpenFormParams($gtabid,$gformid,$formid,$vuniqueid,$filter);
	
	/* --- Header ------*/
	echo "<div $style_abs class=\"gtabFringeRelationBody form-legacy\">\n";
	echo "<TABLE BORDER=\"0\" WIDTH=\"100%\" CELLSPACING=\"0\" CELLPADDING=\"0\" class=\"gtabHeaderRelationTAB\">\n";
	echo "<TR class=\"gtabHeaderSymbolTR\">";
	
	if($typ == 1){
		if(!$filter["ext_RelationFields"]["no_add"][$vuniqueid] AND $LINK[1]){echo "<TD STYLE=\"width:20px;\" ALIGN=\"CENTER\" NOWRAP><i class=\"lmb-icon-cus lmb-page-new\" BORDER=\"0\" title=\"".$lang[$LINK["desc"][1]]."\" STYLE=\"cursor:pointer\" OnClick=\"create_new_verkn(event,'".$verkn["vtabid"]."','$fieldid','$gtabid','$ID')\"></i></TD><TD STYLE=\"width:5px;color:green;\" ALIGN=\"CENTER\">|</TD>";}
		if(!$filter["ext_RelationFields"]["no_edit"][$vuniqueid] AND $gfield[$gtabid]["data_type"][$fieldid] == 27 AND $LINK[3]){
			if($filter["ext_RelationFields"]["edit"][$vuniqueid]){$st = "opacity:0.3;filter:Alpha(opacity=30);";}
			echo "<TD STYLE=\"width:20px;\" ALIGN=\"CENTER\" NOWRAP><i class=\"lmb-icon-cus lmb-page-edit\" title=\"".$lang[$LINK["desc"][3]]."\" STYLE=\"cursor:pointer;$st\" OnClick=\"LmExt_RelationFields(this,'$gtabid','$fieldid','','$typ','$ID','','$vid','edit','','$gformid','$formid');limbasSetOpacity(this)\"></i></TD><TD STYLE=\"width:5px;color:green;\" ALIGN=\"CENTER\">|</TD>";
		}
	}
	#if(!$filter["ext_RelationFields"]["no_openlist"][$vuniqueid] AND $LINK[10]){echo "<TD STYLE=\"width:20px;\" ALIGN=\"CENTER\" NOWRAP><i class=\"lmb-icon lmb-list-ul-alt\" STYLE=\"cursor:pointer;\" title=\"".$lang[$LINK["desc"][10]]."\" BORDER=\"0\" OnClick=\"newwin5('".$verkn["vtabid"]."','".$gtabid."','".$fieldid."','".$ID."')\"></i></TD><TD STYLE=\"width:5px;color:green;\" ALIGN=\"CENTER\">|</TD>";}
	if(!$filter["ext_RelationFields"]["no_openlist"][$vuniqueid] AND $LINK[10]){echo "<TD STYLE=\"width:20px;\" ALIGN=\"CENTER\" NOWRAP><i class=\"lmb-icon lmb-list-ul-alt\" STYLE=\"cursor:pointer;\" title=\"".$lang[$LINK["desc"][10]]."\" BORDER=\"0\" onclick=\"lmbOpenForm(event,'{$verkn["vtabid"]}','',{action:'gtab_erg', formid:'{$formparams['formid']}',formdimension:'{$formparams['dimension']}',formopenas:'{$formparams['openas']}' v_tabid:'$gtabid',v_fieldid:'$fieldid',v_id:'$ID');\"></i></TD><TD STYLE=\"width:5px;color:green;\" ALIGN=\"CENTER\">|</TD>";}
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
