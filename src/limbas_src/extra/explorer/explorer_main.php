<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */

require_once(COREPATH . 'extra/explorer/explorer_main.lib');
require_once(COREPATH  . 'gtab/html/contextmenus/gtab_filter.php');

#----------------- Context-Menü -------------------
explContextDetail($LID);
$gtabid = $gtab["argresult_id"]["LDMS_FILES"];
?>

<DIV ID="filemenu" class="lmbContextMenu" style="position:absolute;visibility:hidden;top:0;z-index:3;" OnClick="activ_menu = 1;">
<?php #----------------- Haupt-Menü -------------------
pop_menu(274,'',''); #save
pop_line();
pop_menu(195,'',''); #Info
pop_line();
if($filestruct['addf'][$LID] AND !$filestruct["readonly"][$LID]){pop_menu(119,'','');$ln=1;} #neuer Ordner
if($filestruct['edit'][$LID] AND !$filestruct["readonly"][$LID] AND $LID){pop_submenu(116,'','');$ln=1;} #umbenennen
if($ln){pop_line();}
pop_submenu(190,'','');	#download
if($filestruct["add"][$LID] AND $LID AND $LINK[128]){
	pop_menu(128,'','');	#upload
}
pop_line();
pop_menu(221,'',''); #Einstellungen speichern
if($filestruct["del"][$LID] AND !$filestruct["readonly"][$LID] AND $LID){
	pop_line();
	pop_menu(171,'',''); 	#löschen
	$viewmenu["editmenu"] = 1;
}

// custmenu
if($GLOBALS['gcustmenu'][$gtabid][3]['id'][0]){
    foreach($GLOBALS['gcustmenu'][$gtabid][3]['id'] as $cmkey => $cmid){
        lmb_pop_custmenu($cmid,$gtabid,$ID,$gresult);
    }
}

pop_bottom();
?>
</DIV>


<DIV ID="editmenu" class="lmbContextMenu" style="position:absolute;visibility:hidden;top:0;z-index:3" OnClick="activ_menu = 1;">
<?php #----------------- edit-Menü -------------------
$move = ($filestruct["del"][$LID] AND !$filestruct["readonly"][$LID] AND $LID);
$copy = (!$filestruct["readonly"][$LID] AND $LID);
$insert = ($filestruct["add"][$LID] AND !$filestruct["readonly"][$LID] AND $LID);

if($LINK[241]){
    pop_submenu(241,'',''); 		#suchen
    $viewmenu["editmenu"] = 1;
    if($move || $copy || $insert){ # only pop line if a menu item follows
        pop_line();
    }
}
if($move){pop_menu(130,'','');$viewmenu["editmenu"] = 1;} #verschieben
if($copy){pop_menu(129,'','');}	#kopieren
if($insert){pop_submenu(191,'','');$viewmenu["editmenu"] = 1;} #einfügen

// custmenu
if($GLOBALS['gcustmenu'][$gtabid][4]['id'][0]){
    foreach($GLOBALS['gcustmenu'][$gtabid][4]['id'] as $cmkey => $cmid){
        lmb_pop_custmenu($cmid,$gtabid,$ID,$gresult);
    }
}

pop_bottom();
?>
</DIV>

<DIV ID="viewmenu" class="lmbContextMenu" style="position:absolute;visibility:hidden;top:0;z-index:3" OnClick="activ_menu = 1;">
<?php
if($ffilter["viewmode"][$LID] == 1){$a = 1;}else{$a = 0;}
pop_menu(222,'','',$a); 				#Datei
if($ffilter["viewmode"][$LID] == 2){$a = 1;}else{$a = 0;}
pop_menu(223,'','',$a); 				#searchengine view
if($ffilter["viewmode"][$LID] == 5){$a = 1;}else{$a = 0;}
if($typ == 7){pop_menu(264,'','',$a);} #tablerelation view
if($ffilter["viewmode"][$LID] == 3){$a = 1;}else{$a = 0;}
pop_menu(256,'','',$a);					#picture galerie
#if($ffilter["viewmode"][$LID] == 4){$a = 1;}else{$a = 0;}
#pop_menu(257,'','',$a);					#picture show
pop_line();
pop_submenu(219,'',''); 				#Anzeige
pop_menu(220,'',''); 					#Ansicht speichern
pop_line();
pop_menu(263,'','',$session["symbolbar"]); 	#Symbolleiste
pop_menu(278,'','',$ffilter["view_dublicates"]); 	#Dublikate
pop_menu(202,'','');					#neues explorer Fenster

// custmenu
if($GLOBALS['gcustmenu'][$gtabid][5]['id'][0]){
    foreach($GLOBALS['gcustmenu'][$gtabid][5]['id'] as $cmkey => $cmid){
        lmb_pop_custmenu($cmid,$gtabid,$ID,$gresult);
    }
}
pop_bottom();
?>
</DIV>

<DIV ID="extramenu" class="lmbContextMenu" style="position:absolute;visibility:hidden;top:0;z-index:3" OnClick="activ_menu = 1;">
<?php #----------------- Extra-Menü -------------------
pop_menu(200,'',''); 	#thumbs neu berechnen
pop_line();
pop_submenu(203,'','');	#konvertieren
pop_menu(247,'','');	#favoriten
pop_menu(269,'','');	#duplikate
if($umgvar["ocr_enable"]){pop_submenu(262,'','');}	#ocr
if($LINK[297]){
    pop_line();
    pop_submenu(297,'','');	#import
}
if ($LINK[304] and $gprinter) {
    # separator
    pop_line();

    # print button
    pop_menu(304, "LmEx_print(event, $LID, $('#LmExMenuOptionsPrinter').val())");

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
    pop_select('', $opt, $sel, 1, 'LmExMenuOptionsPrinter', $lang[2939]);
}
if($LINK[284]){
    pop_line();
    pop_submenu(284,'','');	#einstellungen
}

# extension
if(function_exists($GLOBALS["gLmbExt"]["menuDMSExtras"])){
	$GLOBALS["gLmbExt"]["menuDMSExtras"]($LID,$ID,$ffile);
}

// custmenu
if($GLOBALS['gcustmenu'][$gtabid][6]['id'][0]){
    foreach($GLOBALS['gcustmenu'][$gtabid][6]['id'] as $cmkey => $cmid){
        lmb_pop_custmenu($cmid,$gtabid,$ID,$gresult);
    }
}

pop_bottom();
?>
</DIV>

<DIV ID="settingsmenu" class="lmbContextMenu" style="visibility:hidden;z-index:9993;width:230px" OnClick="activ_menu = 1;">
<?php
pop_top('limbasDivMenuSettings');
if($ffilter["force_delete"]){$checked = "checked";}else{$checked = null;}
pop_checkbox(276,"document.form1.ffilter_force_delete.value=this.checked;","",1,$checked,0);

# external cloud storage
if($session['superadmin']) {
    if($filestruct['prevent_meta'][$LID]){$checked = "checked";}else{$checked = null;}
    pop_checkbox(null,"document.form1.ldms_foldersetting_key.value='prevent_meta';document.form1.ldms_foldersetting_value.value=this.checked;LmEx_send_form(1);",'','',$checked,null,'prevent meta',$desc,$img);
    if($filestruct['prevent_thumbs'][$LID]){$checked = "checked";}else{$checked = null;}
    pop_checkbox(null,"document.form1.ldms_foldersetting_key.value='prevent_thumbs';document.form1.ldms_foldersetting_value.value=this.checked;LmEx_send_form(1);",'','',$checked,null,'prevent thumbs',$desc,$img);

    pop_line();

    $opt = array(
        'val' => array('-1'),
        'desc' => array('Keiner') # TODO
    );
    lmbGetExternalStorageConfig();
    foreach ($externalStorage['desc'] as $storageID => $storageDesc) {
        $opt['val'][] = $storageID;
        $opt['desc'][] = $storageDesc;
    }
    pop_select("document.form1.ldms_foldersetting_key.value='externalStorageID';document.form1.ldms_foldersetting_value.value=this.value;LmEx_send_form(1);", $opt, $filestruct['storageID'][$LID], '', '', 'external storage', 60);
}


pop_bottom();
?>
</DIV>

<DIV ID="importmenu" class="lmbContextMenu" style="visibility:hidden;z-index:9993" OnClick="activ_menu = 1;">
<?php
pop_top('limbasDivMenuImport');
pop_left();
echo "<i style=\"color:grey;\">/EXTENSIONS/myfolder...</i>";
pop_right();

pop_input('LmEx_ImportPath','','LmEx_ImportPath','',0,155);
pop_left();
echo "
<div id=\"lmbUploadFromPath\" class=\"lmbUploadProgress\">
<div id=\"lmbUploadFromPathBar\" class=\"lmbUploadProgressBar\"></div>
</div>";
pop_right();

$opt["val"] = array('ignore','rename','overwrite','versioning');
$opt["desc"] = array($lang[2332],$lang[1464],$lang[1002],$lang[2132]);
pop_select($zl,$opt,$sel,null,'LmEx_ImportPathType',$lang[1685]);

pop_line();

pop_submit($lang[2798],"LmEx_uploadFromPath(this,$LID)");
pop_bottom();
?>
</DIV>

<DIV ID="searchmenu" class="lmbContextMenu" style="position:absolute;visibility:hidden;top:0;z-index:3" OnClick="activ_menu = 1;">
<?php #----------------- Such-Menü -------------------
pop_top('searchmenu');
pop_submenu(117,'',''); #Detailsuche
pop_menu(217,'',''); #zurück setzen
pop_line();
if($ffilter["sub"]){$checked = "checked";}else{$checked = null;}
pop_checkbox(265,"document.form1.ffilter_sub.value=this.checked;","",1,$checked,"");
if($ffilter["glob"]){$checked = "checked";}else{$checked = null;}
pop_checkbox(218,"document.form1.ffilter_glob.value=this.checked;","",1,$checked,"");
pop_bottom();
?>
</DIV>

<DIV ID="previewmenu" class="lmbContextMenu" style="position:absolute;visibility:hidden;top:0;z-index:99995" OnClick="activ_menu = 1;">
<?php
pop_top('previewmenu');

pop_left();
echo "<table><tr><td>$lang[1563]:&nbsp;</td><td><select id=\"convtoformat\" onchange=\"if(this.value=='jpg' || this.value=='png'){document.getElementById('convtopicdiv').style.display='';}else{document.getElementById('convtopicdiv').style.display='none';}\">
	<option></option>
	<option value=\"jpg\">JPEG</option>
	<option value=\"png\">PNG</option>
	<option value=\"pdf\">PDF</option>
	<option value=\"html\">HTML</option>
	";

# unoconv formats
if (LmbUnoconv::isEnabled()) {
    echo '<optgroup label="Unoconv">';
    $formats = LmbUnoconv::getSupportedFormats();
    foreach ($formats as $formatName => $formatDesc) {
        echo "<option value=\"uc_{$formatName}\">$formatDesc</option>";
    }
    echo '</optgroup>';
}
echo '</select></td></tr>';

echo "<tr id=\"convtopicdiv\" style=\"display:none\"><td>$lang[1141]:</td><td><input type=\"text\" id=\"convtopicsize\" style=\"width:30px;\">&nbsp;px</td></tr></table>";


pop_right();

pop_line();
pop_menu2($lang[2240], $lang[2240], null, null, null, "LmEx_preview_archive(document.getElementById('convtoformat').value,document.getElementById('convtopicsize').value)");
pop_bottom();
?>
</DIV>


<?php
if($umgvar["ocr_enable"] AND $LINK[262]){
echo "<DIV ID=\"ocrmenu\" class=\"lmbContextMenu\" style=\"position:absolute;visibility:hidden;top:0;z-index:4\" OnClick=\"activ_menu = 1;\">";
pop_top('ocrmenu');
$opt["val"] = $umgvar["ocr_format_val"]; // TODO not defined
$opt["desc"] = $umgvar["ocr_format_desc"];
pop_select("",$opt,"",1,"ocr_format",$lang[1563],50);
$opt["val"] = $umgvar["ocr_quality_val"];
$opt["desc"] = $umgvar["ocr_quality_desc"];
pop_select("",$opt,"",1,"ocr_quality",$lang[1176],50);
$opt["val"] = array("preview","rename","overwrite","versioning");
$opt["desc"] = array($lang[1739],$lang[1464],$lang[1002],$lang[2132]);
pop_select("",$opt,"",1,"ocr_destination","$lang[2647]",50);
pop_line();
pop_menu2($lang[2314], $lang[2314], null, null, null, "LmEx_ocrfile(document.getElementsByName('ocr_format')[0].value,document.getElementsByName('ocr_destination')[0].value,document.getElementsByName('ocr_quality')[0].value);");
pop_bottom();
echo "</DIV>";
}
?>


<DIV ID="cachelist" class="lmbContextMenu" style="position:absolute;visibility:hidden;top:0;z-index:3" OnClick="activ_menu = 1;">
<?php
pop_top('cachelist');
pop_left();
?><span ID="cachelist_area"></span><?php
pop_right();
pop_bottom();
?>
</DIV>

<DIV ID="rename" class="lmbContextMenu" style="position:absolute;visibility:hidden;top:0;z-index:4" OnClick="activ_menu = 1;">
<?php
pop_top('rename');
pop_input(0,'document.form1.rename_file.value=this.value;LmEx_send_form(1);LmEx_divclose();','file_dir_rename','','');
pop_bottom();
?>
</DIV>

<DIV ID="fieldlist" class="lmbContextMenu" style="position:absolute;display:none;z-index:4" OnClick="activ_menu = 1;">
<?php
pop_top('fieldlist');
pop_header(null, $lang[1634]);
foreach ($gfile["id"] as $key => $value){
	# Felder ohne Rechte
	if(!$gfile["id"][$key] OR $gtab["argresult_id"]["LDMS_FILES"]."_11" == $key){continue;}
	if($gfile['field_type'][$key] == 100){
	    pop_header(null, $gfile['title'][$key]);
	}else{
        if($gfile['show'][$LID][$key]){
            $color = "color: green;";$icdis = "";
        }else{
            $color = "";$icdis = "hidden";
        }
        pop_menu2(
            "&nbsp;".$gfile['title'][$key],
            $gfile["tabid"][$key]."_".$gfile["fid"][$key],
            "dc_$key\" style=\"$color",
            "lmb-icon lmb-check\" style=\"visibility: $icdis",
            null,
            "fieldlist('$key');"
        );
	}
}
pop_bottom();
?>
</DIV>

<div id="lmbAjaxContainer" style="position:absolute;display:none;" OnClick="activ_menu=1;"></div>


<script language="JavaScript">
// ----- Js-Script-Variablen --------
jsvar["ID"] = "<?=$ID?>";
jsvar["LID"] = "<?=$LID?>";
jsvar["typ"] = "<?=$typ?>";
jsvar["action"] = "<?=$action?>";
jsvar["level"] = "<?=$level?>";
jsvar["copycache"] = "<?=$umgvar["copycache"]?>";
jsvar["res_viewcount"] = "<?=$ffile["res_viewcount"]?>";
jsvar["resultspace"] = "<?=$umgvar["resultspace"]?>";
jsvar["message1"] = "<?=$lang[1696]?>";
jsvar["gtabid"] = <?=$gtab["argresult_id"]["LDMS_FILES"]?>;

// ----- Onload-Aktionen --------
<?php
if($onload){
	echo $onload;
}
?>

</SCRIPT>

<form action="main.php" method="post" name="form2" id="form2">
<input type="hidden" name="action" value="<?=$action;?>">
<input type="hidden" name="ID" value="<?=$ID;?>">
<input type="hidden" name="MID" value="<?=$MID;?>">
<input type="hidden" name="LID" value="<?=$LID;?>">
<input type="hidden" name="typ" value="<?=$typ;?>">
<input type="hidden" name="filter_reset" value="1">
</form>

<form enctype="multipart/form-data" action="main.php" method="post" name="form1" id="form1">

<input type="hidden" name="action" value="<?=$action;?>">
<input type="hidden" name="old_action" value="<?=$old_action?>">
<input type="hidden" name="ID" value="<?=$ID;?>">
<input type="hidden" name="MID" value="<?=$MID;?>">
<input type="hidden" name="LID" value="<?=$LID;?>">
<input type="hidden" name="typ" value="<?=$typ;?>">
<input type="hidden" name="level" value="<?=$level;?>">

<input type="hidden" name="del_file">
<input type="hidden" name="move_file">
<input type="hidden" name="copy_file">
<input type="hidden" name="rename_file">
<input type="hidden" name="refresh_file">
<input type="hidden" name="favorite_file">
<input type="hidden" name="duplicateTypes">

<input type="hidden" name="ocr_file">
<input type="hidden" name="ocr_format">
<input type="hidden" name="ocr_destination">
<input type="hidden" name="convert_file">

<input type="hidden" name="add_file">
<input type="hidden" name="edit_id">
<input type="hidden" name="ffilter_order">
<input type="hidden" name="ffilter_viewmode">
<input type="hidden" name="ffilter_glob">
<input type="hidden" name="ffilter_fts_search_mode">
<input type="hidden" name="ffilter_content_cs">
<input type="hidden" name="ffilter_content_ts">
<input type="hidden" name="ffilter_content_se">
<input type="hidden" name="ffilter_content_mf">
<input type="hidden" name="ffilter_sub">
<input type="hidden" name="ffilter_onlymeta">
<input type="hidden" name="ffilter_fl_show">
<input type="hidden" name="ffilter_fl_hide">
<input type="hidden" name="ffilter_force_delete">
<input type="hidden" name="ffilter_dublicates">
<input type="hidden" name="download_archive">
<input type="hidden" name="pdf_archive">
<input type="hidden" name="rowsize">
<input type="hidden" name="save_setting">
<input type="hidden" name="view_symbolbar">
<input type="hidden" name="ldms_foldersetting_key">
<input type="hidden" name="ldms_foldersetting_value">

   <?php
   # <div class=" mb-3 legacy-table d-inline-block flex-fill table-responsive" style="height:100%">
   ?>
<div class="p-3 d-flex flex-column">
<nav class="navbar navbar-expand-sm navbar-light bg-nav mb-3 lmbGtabmenu lmbGtabmenu-list lmbGtabmenu-table-20">
    <div class="container-fluid">
        <div class="collapse navbar-collapse" id="lmbDetailsNavbar">
            <ul class="navbar-nav me-auto mb-2 mb-sm-0">

                <?php if($LINK[195] OR $LINK[190] OR $LINK[203] OR $LINK[221]){?>
                <li class="nav-item lmbGtabmenu-file">
                    <a class="nav-link " href="#"  OnClick="LmEx_open_menu(this,'filemenu');" id="edit4"><?=$lang[545]?></a>
                </li>
                <?php }?>
                <?php if($viewmenu["editmenu"]){?>
                <li class="nav-item lmbGtabmenu-edit">
                    <a class="nav-link" href="#" OnClick="LmEx_open_menu(this,'editmenu');"><?=$lang[843]?></a>
                </li>
                <?php }?>
                <?php if($LINK[202] OR $LINK[219] OR $LINK[220]){?>
                <li class="nav-item lmbGtabmenu-view">
                    <a class="nav-link" href="#" OnClick="LmEx_open_menu(this,'viewmenu');"><?=$lang[1625]?></a>
                </li>
                <?php }?>
                <?php if($LINK[200]){?>
                <li class="nav-item lmbGtabmenu-extra">
                    <a class="nav-link" href="#" OnClick="LmEx_open_menu(this,'extramenu');"><?=$lang[1939]?></a>
                </li>
                <?php }?>
                <?php
                // custmenu
                if($GLOBALS['gcustmenu'][$gtabid][2]['id'][0]):
                    foreach($GLOBALS['gcustmenu'][$gtabid][2]['id'] as $cmkey => $cmid):?>
                            <li class="nav-item">
                                <a class="gtabHeaderMenuTD hoverable" onclick="limbasDivShow(this,'','limbasDivCustMenu_<?=$cmid?>');">
                                    <?=$lang[$GLOBALS['gcustmenu'][$gtabid][2]['name'][$cmkey]]?>
                                </a>
                            </li>
                    <?php
                    endforeach;
                endif;
                ?>
            </ul>
            <?php if($session["symbolbar"]): ?>

            <ul class="navbar-nav ms-auto">

                <?php if(($level OR $level == '0') AND $LID != $gfield[$f_tabid]["file_level"][$f_fieldid] AND $level != $rootlevel){ ?>
                    <li class="nav-item nav-link lmb-folder-up">
                        <?php pop_picmenu(326,'', '',''); // show related ?>
                    </li>
                <?php } ?>

                    <li class="nav-item nav-link lmbGtabmenuIcon-274">
                        <?php pop_picmenu(274,'', ''); // show related ?>
                    </li>

                <?php if($filestruct["add"][$LID]): ?>
                    <li class="nav-item nav-link lmbGtabmenuIcon-128">
                        <?php pop_picmenu(128,'', '', '', "OnClick=\"LmEx_showUploadField();\""); // show related ?>
                    </li>
                <?php endif; ?>
                <?php if($filestruct["del"][$LID]): ?>
                    <li class="nav-item nav-link lmbGtabmenuIcon-171">
                        <?php pop_picmenu(171,'','', active:1); // show related ?>
                    </li>
                <?php endif; ?>

                    <li class="nav-item nav-link lmbGtabmenuIcon-190">
                        <?php pop_picmenu(190,'', '', active:1); // show related ?>
                    </li>
                    <li class="nav-item nav-link lmbGtabmenuIcon-129">
                        <?php pop_picmenu(129,'', '', active:1); // show related ?>
                    </li>

                <?php if($filestruct["del"][$LID]): ?>
                    <li class="nav-item nav-link lmbGtabmenuIcon-130">
                        <?php pop_picmenu(130,'', '', active:1); // show related ?>
                    </li>
                <?php endif; ?>
                <?php if($filestruct["add"][$LID]): ?>
                    <li class="nav-item nav-link lmbGtabmenuIcon-191">
                        <?php pop_picmenu(191,'', '', active: $ffilter["copyContext"] ? 0 : 1); // show related ?>
                    </li>
                <?php endif; ?>

                    <li class="nav-item nav-link lmbGtabmenuIcon-222">
                        <?php pop_picmenu(222,'', '', active: $ffilter["viewmode"][$LID] == 1 ? 0 : 1); // show related ?>
                    </li>
                    <li class="nav-item nav-link lmbGtabmenuIcon-223">
                        <?php pop_picmenu(223,'', '', active: $ffilter["viewmode"][$LID] == 2 ? 0 : 1); // show related ?>
                    </li>

                <?php if($typ == 7): ?>
                    <li class="nav-item nav-link lmbGtabmenuIcon-264">
                        <?php pop_picmenu(264,'', '', active: $ffilter["viewmode"][$LID] == 5 ? 0 : 1); // show related ?>
                    </li>
                <?php endif; ?>

                    <li class="nav-item nav-link lmbGtabmenuIcon-256">
                        <?php pop_picmenu(256,'','', active: $ffilter["viewmode"][$LID] == 3 ? 0 : 1); // show related ?>
                    </li>
                    <li class="nav-item nav-link lmbGtabmenuIcon-117">
                        <?php pop_picmenu(117,'',''); // show related ?>
                    </li>
                    <li class="nav-item nav-link lmbGtabmenuIcon-249">
                        <?php pop_picmenu(249,'',''); // show related ?>
                    </li>
                    <li class="nav-item nav-link lmbGtabmenuIcon-217">
                        <?php pop_picmenu(217,'',''); // show related ?>
                    </li>

            </ul>

            <?php
            endif;
            ?>
        </div>
    </div>
</nav>

<?php
$headerdesc = $filestruct["name"][$LID];
if(!$headerdesc){
	$headerdesc = $lang[1200];
}
?>

<?php // dummy div for html injection from leagcy js ?>
<div class="" id="lmbUploadLayer"></div>

<div class="container-fluid p-1 border mb-3 bg-secondary-subtle">
    <span class="w-100" ><?=$file_url?></span>
</div>

<?php explMainContentBootstrap($ID,$LID,$MID,$fid,$typ,$level,$file_url,$ffile,$ffilter);?>
</div>
</form>


<script language="JavaScript">
    LmEx_createDropArea($("#gtabExplBody"), function(files) {
        LmEx_showUploadField('lmbUploadLayer', <?= $LID ?>, 1);
        LmEx_uploadFilesPrecheck(files, <?= $LID ?>, 1);
    });
</script>