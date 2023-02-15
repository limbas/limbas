<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */


?>


	<script src="assets/vendor/codemirror/lib/codemirror.js?v=<?=$umgvar["version"]?>"></script>
    <link rel="stylesheet" href="assets/vendor/codemirror/lib/codemirror.css?v=<?=$umgvar["version"]?>">
	<script src="assets/vendor/codemirror/addon/edit/matchbrackets.js?v=<?=$umgvar["version"]?>"></script>
	<script src="assets/vendor/codemirror/addon/edit/matchtags.js?v=<?=$umgvar["version"]?>"></script>
	<script src="assets/vendor/codemirror/mode/htmlmixed/htmlmixed.js?v=<?=$umgvar["version"]?>"></script>
	<script src="assets/vendor/codemirror/mode/xml/xml.js?v=<?=$umgvar["version"]?>"></script>
	<script src="assets/vendor/codemirror/mode/javascript/javascript.js?v=<?=$umgvar["version"]?>"></script>
	<script src="assets/vendor/codemirror/mode/css/css.js?v=<?=$umgvar["version"]?>"></script>
	<script src="assets/vendor/codemirror/mode/clike/clike.js?v=<?=$umgvar["version"]?>"></script>
	<script src="assets/vendor/codemirror/mode/php/php.js?v=<?=$umgvar["version"]?>"></script>
	

<STYLE>
        .CodeMirror {
            border: 1px solid <?=$farbschema['WEB3']?>;
            height: 200px;
        }

TD TEXTAREA{
	overflow:hidden;
}

TEXTAREA {
	BACKGROUND-COLOR: <?= $farbschema['WEB8'] ?>;
}
</STYLE>

<?php
if($form['css']){
 	echo '<style type="text/css">@import url(' . lmb_substr($form['css'], 1, 120) . '?v='.$umgvar["version"].');</style>';
}

?>

<script language="JavaScript">
// ----- Js-Script-Variablen --------
var jsvar = new Array();
jsvar["action"] = "<?=$action?>";
jsvar["ID"] = "<?=$ID?>";
jsvar["form_viewtab"] = "<?=$form_viewtab?>";
jsvar["form_id"] = "<?php if($form["id"]){echo implode(";",$form["id"]);}?>";
jsvar["WEB7"] = "<?=$farbschema['WEB7']?>";
jsvar["lng_1099"] = "<?=$lang[1099]?>";
jsvar["WEB10"] = "<?=$farbschema['WEB10']?>";
jsvar["form_typ"] = "<?=$form['form_typ']?>";


// --- Farben -----------------------------------
var color = new Array();
color[1] = "<?=$farbschema['WEB1']?>";
color[2] = "<?=$farbschema['WEB2']?>";
color[3] = "<?=$farbschema['WEB3']?>";
color[4] = "<?=$farbschema['WEB4']?>";
color[5] = "<?=$farbschema['WEB5']?>";
color[6] = "<?=$farbschema['WEB6']?>";
color[7] = "<?=$farbschema['WEB7']?>";
color[8] = "<?=$farbschema['WEB8']?>";
color[9] = "<?=$farbschema['WEB9']?>";
color[10] = "<?=$farbschema['WEB10']?>";


<?= $view_tab ?>
var zIndexTop = <?=$form["max_zindex"]?>;
var snap_tabid = 0;

<?php
//----------------- Element-Schleife für DOM Tabelle -------------------
echo "
var f_id = new Array();
var f_zindex = new Array();
var f_value = new Array();
var f_typ = new Array();
";
if($form["id"]){
	foreach ($form["id"] as $key => $bzm){
		echo "f_id[$bzm] = '".$bzm."';\n";
		echo "f_zindex[$bzm] = '".$form["zindex"][$bzm]."';\n";
		if($form["typ"][$bzm] == 'js' OR $form["typ"][$bzm] == 'php'){
			echo "f_value[$bzm] = '".$form["typ"][$bzm]." code';\n";
		}else{
			echo "f_value[$bzm] = '".htmljs($form["value"][$bzm])."';\n";
		}
		echo "f_typ[$bzm] = '".$form["typ"][$bzm]."';\n";
	}
}
?>


</script>



<?php /*----------------- Ramenwechsel-Grafik -------------------*/?>
<div ID="border_move" style="position:absolute;top:0px;left:0px;cursor:se-resize;z-index:10004;display:none;" onMousedown="return aktivate_resize();">
    <i class="lmb-icon lmb-resizer" BORDER="0"></i>
</div>

<?php /*----------------- MainMenu -------------------*/?>
<DIV ID="menu" class="lmbContextMenu lmbContextMenuMove" style="display:none;position:absolute;top:<?=$menuposy?>;left:<?=$menuposx?>;z-index:10003;">
<FORM NAME="form_menu">
<TABLE BORDER="0" cellspacing="0" cellpadding="0">
<TR><TD><?php pop_movetop('menu');?></TD></TR>
<TR id="menu_js_php_text_bild_chart_templ_tab_stab_datum_relpath_notice_filter_globsearch_dbdat_dbdesc_dbnew_dbsearch_rect_line_ellipse_submt_button_inptext_inphidden_inparea_inpselect_inpcheck_inpradio_menue_tabulator_frame_scroll_wflhist_reminder_tabmenu_uform_tile_tabuItem_tabcell_" STYLE="display:none"><TD><?php pop_input2("el_change_id(this.value)","input_id","","",$lang[1099],65)?></TD></TR>
<TR id="menu_js_php_text_bild_chart_templ_tab_stab_datum_relpath_notice_filter_globsearch_dbdat_dbdesc_dbnew_dbsearch_rect_line_ellipse_submt_button_inptext_inphidden_inparea_inpselect_inpcheck_inpradio_menue_tabulator_frame_scroll_wflhist_reminder_tabmenu_uform_tile_tabuItem_tabcell_" STYLE="display:none"><TD><?php pop_input2('el_to_front(this.value)',"ZIndex","","",'ZIndex',65,'oninput')?></TD></TR>

<TR id="menu_dbdat_" STYLE="display:none"><TD><?php pop_line();?></TD></TR>
<TR id="menu_dbdat_" STYLE="display:none;"><TD><?php pop_input(0,'','input_infotable','','readonly');?></TD></TR>
<TR id="menu_dbdat_" STYLE="display:none;"><TD><?php pop_input(0,'','input_infofield','','readonly');?></TD></TR>

<TR id="menu_js_php_text_bild_chart_templ_tab_stab_datum_relpath_notice_filter_globsearch_dbdat_dbdesc_dbnew_dbsearch_rect_line_ellipse_submt_button_inptext_inphidden_inparea_inpselect_inpcheck_inpradio_menue_tabulator_frame_scroll_wflhist_reminder_tabmenu_uform_tile_tabuItem_tabcell_" STYLE="display:none"><TD><?php pop_line();?></TD></TR>
<TR id="menu_multi_js_php_text_bild_chart_templ_tab_stab_datum_relpath_notice_filter_globsearch_dbdat_dbdesc_dbnew_dbsearch_rect_line_ellipse_submt_button_inptext_inphidden_inparea_inpselect_inpcheck_inpradio_menue_tabulator_frame_scroll_wflhist_reminder_tabmenu_uform_tile_tabuItem_" STYLE="display:none;"><TD><?php pop_menu(0,'el_to_front(zIndexTop+1)',$lang[2064]);?></TD></TR>
<TR id="menu_multi_js_php_text_bild_chart_templ_tab_stab_datum_relpath_notice_filter_globsearch_dbdat_dbdesc_dbnew_dbsearch_rect_line_ellipse_submt_button_inptext_inphidden_inparea_inpselect_inpcheck_inpradio_menue_tabulator_frame_scroll_wflhist_reminder_tabmenu_uform_tile_tabuItem_" STYLE="display:none;"><TD><?php pop_menu(0,'el_to_front(1)',$lang[2065]);?></TD></TR>
<TR id="menu_bild_menue_" STYLE="display:none"><TD><?php pop_line();?></TD></TR>
<TR id="menu_multi_js_php_text_bild_chart_templ_tab_stab_datum_relpath_notice_filter_globsearch_dbdat_dbdesc_dbnew_dbsearch_rect_line_ellipse_submt_button_inptext_inphidden_inparea_inpselect_inpcheck_inpradio_" STYLE="display:none"><TD><?php pop_line();?></TD></TR>
<TR id="menu_multi_js_php_text_datum_relpath_notice_filter_globsearch_dbdat_dbdesc_dbnew_dbsearch_rect_line_ellipse_submt_button_inptext_inphidden_inparea_inpselect_inpcheck_inpradio_frame_menue_tabulator_" STYLE="display:none"><TD><?php pop_menu(0, "document.form1.form_copy.value='1';set_posxy();document.form1.submit();",$lang[1464]);?></TD></TR>
<TR id="menu_multi_js_php_text_bild_chart_templ_tab_stab_datum_relpath_notice_filter_globsearch_dbdat_dbdesc_dbnew_dbsearch_rect_line_ellipse_submt_button_inptext_inphidden_inparea_inpselect_inpcheck_inpradio_frame_tabulator" STYLE="display:none"><TD><?php pop_menu(0, "lmbCutElements(currentid)", $lang[2666]);?></TD></TR>
<TR id="menu_multi_frame_menue_tabulator_" STYLE="display:none"><TD id="form_movemenu"><?php pop_menu(0,"document.form1.form_move.value=move_id.join(';');set_posxy();document.form1.submit();",$lang[2667]);?></TD></TR>
<TR id="menu_multi_js_php_text_bild_chart_templ_tab_stab_datum_relpath_notice_filter_globsearch_dbdat_dbdesc_dbnew_dbsearch_rect_line_ellipse_submt_button_inptext_inphidden_inparea_inpselect_inpcheck_inpradio_" STYLE="display:none"><TD><?php pop_line();?></TD></TR>
<TR id="menu_php_text_datum_dbdat_dbdesc_dbnew_dbsearch_rect_line_ellipse_submt_button_bild_chart_inptext_inphidden_inparea_inpselect_inpcheck_inpradio_tabuItem_tabcell_" STYLE="display:none"><TD><?php pop_submenu2($lang[1763],"limbasDivShow(this,'menu','menu_events');",$lang[1763]);?></TD></TR>
<TR id="menu_php_text_datum_dbdat_dbdesc_dbnew_dbsearch_rect_line_ellipse_bild_chart_submt_button_inptext_inphidden_inparea_inpselect_inpcheck_inpradio_frame_" STYLE="display:none"><TD><?php pop_line();?></TD></TR>
<?php
$opt["val"] = $sysfont;
$opt["desc"] = $sysfont;
?>
<TR id="menu_multi_js_php_text_chart_templ_datum_relpath_notice_filter_globsearch_dbdat_dbdesc_dbnew_dbsearch_submt_button_inptext_inphidden_inparea_inpselect_inpcheck_inpradio_frame_uform_tile_" STYLE="display:none"><TD><?php pop_select("fill_style('0','fontFamily',this.value);",$opt,"",1,"input_fontface","Font",50);?></TD></TR>
<?php $opt['val'] = array("","1px","2px","3px","4px","5px","6px","7px","8px","9px","10px","11px","12px","13px","14px","15px","16px","17px","18px","19px","20px","21px","22px","23px","24px","25px","26px","27px","28px","29px","30px","31px","32px","33px","34px","35px","36px","37px","38px","39px","40px");
$opt['desc'] = array("","1","2","3","4","5","6","7","8","9","10","11","12","13","14","15","16","17","18","19","20","21","22","23","24","25","26","27","28","29","30","31","32","33","34","35","36","37","38","39","40");?>
<TR id="menu_multi_js_php_text_chart_templ_datum_relpath_notice_filter_globsearch_dbdat_dbdesc_dbnew_dbsearch_submt_button_inptext_inphidden_inparea_inpselect_inpcheck_inpradio_frame_uform_tile_rect_line_ellipse_" STYLE="display:none"><TD><?php pop_select("fill_style('3','fontSize',this.value);",$opt,"",1,"input_fontsize",$lang[210],50);?></TD></TR>
<TR id="menu_multi_text_php_datum_relpath_notice_filter_globsearch_dbdat_dbdesc_dbnew_dbsearch_submt_button_inptext_inphidden_inparea_inpselect_inpcheck_inpradio_frame_tabcell_" STYLE="display:none"><TD><?php pop_submenu2($lang[1102],"limbasDivShow(this,'menu','menu_fontstyle');",$lang[1102]);?></TD></TR>
<TR id="menu_multi_text_chart_templ_php_datum_relpath_notice_filter_globsearch_dbdat_dbdesc_dbnew_dbsearch_rect_line_ellipse_submt_button_inptext_inphidden_inparea_inpselect_inpcheck_inpradio_frame_" STYLE="display:none"><TD><?php pop_submenu2($lang[1104],"limbasDivShow(this,'menu','menu_color');submenu_style('9;color');",$lang[1104]);?></TD></TR>
<TR id="menu_multi_text_php_templ_tab_datum_relpath_notice_filter_globsearch_dbdat_dbdesc_dbnew_dbsearch_line_rect_ellipse_submt_button_inptext_inphidden_inparea_inpselect_inpcheck_inpradio_uform_tile_tabulator_frame_tabcell_chart_" STYLE="display:none"><TD><?php pop_submenu2($lang[1107],"limbasDivShow(this,'menu','menu_color');submenu_style('21;backgroundColor');",$lang[1107]);?></TD></TR>
<TR id="menu_multi_text_php_tab_stab_datum_relpath_notice_filter_globsearch_dbdat_dbdesc_dbnew_dbsearch_rect_line_ellipse_submt_button_inptext_inphidden_inparea_inpselect_inpcheck_inpradio_frame_uform_tile_scroll_wflhist_reminder_tabmenu_tabcell_" STYLE="display:none"><TD><?php pop_line();?></TD></TR>
<TR id="menu_multi_text_php_bild_chart_templ_tab_stab_datum_relpath_notice_filter_globsearch_dbdat_dbdesc_dbnew_dbsearch_rect_line_ellipse_submt_button_inptext_inphidden_inparea_inpselect_inpcheck_inpradio_frame_scroll_wflhist_reminder_tabmenu_uform_tile_tabcell_" STYLE="display:none"><TD><?php pop_submenu2($lang[1541],"limbasDivShow(this,'menu','menu_color');submenu_style('15;borderColor');",$lang[1541]);?></TD></TR>
<?php $opt['val'] = array("","none","solid","dotted","dashed","double","inset","outset");
$opt['desc'] = array("",$lang[1246],$lang[1534],$lang[1535],$lang[1536],$lang[1537],$lang[1538],$lang[1539]);?>
<TR id="menu_multi_text_php_bild_chart_templ_tab_stab_datum_relpath_notice_filter_globsearch_dbdat_dbdesc_dbnew_dbsearch_rect_line_ellipse_submt_button_inptext_inphidden_inparea_inpselect_inpcheck_inpradio_frame_scroll_wflhist_reminder_tabmenu_uform_tile_tabcell_" STYLE="display:none"><TD><?php pop_select("fill_style('14','borderStyle',this.value);",$opt,"",1,"input_borderstyle",$lang[1540],50);?></TD></TR>
<?php $opt['val'] = array("0px","1px","2px","3px","4px","5px","6px","7px","8px","9px","10px");
$opt['desc'] = array("","1","2","3","4","5","6","7","8","9","10");?>
<TR id="menu_multi_text_php_bild_chart_templ_tab_stab_datum_relpath_notice_filter_globsearch_dbdat_dbdesc_dbnew_dbsearch_rect_line_ellipse_submt_button_inptext_inphidden_inparea_inpselect_inpcheck_inpradio_frame_scroll_wflhist_reminder_tabmenu_uform_tile_tabcell_" STYLE="display:none"><TD><?php pop_select("fill_style('16','borderWidth',this.value);",$opt,"",1,"input_borderwidth",$lang[1105],50);?></TD></TR>
<?php $opt['val'] = array("","0","1px","2px","3px","4px","5px","6px","7px","8px","9px","10px");
$opt['desc'] = array("","0","1","2","3","4","5","6","7","8","9","10");?>
<TR id="menu_multi_text_php_bild_chart_templ_tab_stab_datum_relpath_notice_filter_globsearch_dbdat_dbdesc_dbnew_dbsearch_rect_line_ellipse_submt_button_inptext_inphidden_inparea_inpselect_inpcheck_inpradio_frame_scroll_wflhist_reminder_tabmenu_uform_tile_tabcell_" STYLE="display:none"><TD><?php pop_select("fill_style('27','borderRadius',this.value);divclose();set_posxy();",$opt,"",1,"input_borderradius",$lang[2757],50);?></TD></TR>
<?php $opt['val'] = array("","0","1px","2px","3px","4px","5px","6px","7px","8px","9px","10px");
$opt['desc'] = array("","0","1","2","3","4","5","6","7","8","9","10");?>
<TR id="menu_multi_text_php_templ_tab_stab_datum_relpath_notice_filter_globsearch_dbdat_dbdesc_dbnew_dbsearch_rect_tab_stab_submt_button_inptext_inphidden_inparea_inpselect_inpcheck_inpradio_frame_uform_tile_scroll_wflhist_reminder_tabmenu_tabcell_" STYLE="display:none"><TD><?php pop_select("fill_style('22','padding',this.value);divclose();set_posxy();",$opt,"",1,"input_tabpadding",$lang[1111],50);?></TD></TR>
<?php $opt['val'] = array("","0","1px","2px","3px","4px","5px","6px","7px","8px","9px","10px","11px","12px","13px","14px","15px","16px","17px","18px","19px","20px");
$opt['desc'] = array("","0","1","2","3","4","5","6","7","8","9","10","11","12","13","14","15","16","17","18","19","20");?>
<TR id="menu_multi_text_php_tab_stab_datum_relpath_notice_filter_globsearch_dbdat_dbdesc_dbnew_dbsearch_rect_tab_stab_submt_button_inptext_inphidden_inparea_inpselect_inpcheck_inpradio_frame_uform_tile_scroll_wflhist_reminder_tabmenu_tabcell_" STYLE="display:none"><TD><?php pop_select("fill_style('28','margin',this.value);divclose();set_posxy();",$opt,"",1,"input_tabmargin",$lang[2925],50);?></TD></TR>
<?php $opt['val'] = array("","auto","visible","hidden","scroll");
$opt['desc'] = array("","auto",$lang[1776],$lang[1777],$lang[1778]);?>
<TR id="menu_multi_menue_text_php_tab_stab_datum_relpath_notice_filter_globsearch_dbdat_dbdesc_dbnew_dbsearch_rect_tab_stab_bild_chart_submt_button_inptext_inphidden_inparea_inpselect_inpcheck_inpradio_frame_uform_tile_tabcell_" STYLE="display:none"><TD><?php pop_select("fill_style('24','overflow',this.value);divclose();set_posxy();",$opt,"",1,"input_overflow",$lang[1775],50);?></TD></TR>
<TR id="menu_multi_text_php_bild_chart_templ_tab_stab_datum_relpath_notice_filter_globsearch_dbdat_dbdesc_dbnew_dbsearch_rect_line_ellipse_submt_button_inptext_inphidden_inparea_inpselect_inpcheck_inpradio_uform_tile_scroll_wflhist_reminder_tabmenu_tabcell_" STYLE="display:none"><TD>
<?php pop_left();?>
&nbsp;&nbsp;&nbsp;&nbsp;l<INPUT TYPE="checkbox" STYLE="border:none" NAME="borderLeft" ID="borderLeft" OnClick="fill_style('17','border','borderLeft');">&nbsp;r<INPUT TYPE="checkbox" STYLE="border:none" NAME="borderRight" ID="borderRight" OnClick="fill_style('18','border','borderRight');">&nbsp;o<INPUT TYPE="checkbox" STYLE="border:none" NAME="borderTop" ID="borderTop" OnClick="fill_style('19','border','borderTop');">&nbsp;u<INPUT TYPE="checkbox" STYLE="border:none" NAME="borderBottom" ID="borderBottom" OnClick="fill_style('20','border','borderBottom');">
<?php pop_right();?>

<TR id="menu_tab_stab_" STYLE="display:none"><TD><?php pop_line();?></TD></TR>
<?php $opt['val'] = array("seperate","collapse");
$opt['desc'] = array($lang[1956],$lang[1955]);?>
<TR id="menu_tab_stab_" STYLE="display:none"><TD><?php pop_select("fill_style('32','borderCollapse',this.value);divclose();set_posxy();",$opt,"",1,"input_collapse",$lang[1957],50);?></TD></TR>
<?php $opt['val'] = array("","1");
$opt['desc'] = array("",$lang[2736]);?>
<TR id="menu_tab_stab_" STYLE="display:none"><TD><?php pop_select("fill_style('34','cellstyle',this.value);divclose();set_posxy();",$opt,"",1,"input_cellstyle",$lang[2735],50);?></TD></TR>

<TR id="menu_text_bild_chart_templ_tab_stab_datum_relpath_notice_filter_globsearch_dbdat_dbdesc_dbnew_dbsearch_rect_line_ellipse_submt_button_inptext_inphidden_inparea_inpselect_inpcheck_inpradio_uform_tile_frame_tabcell_" STYLE="display:none"><TD><?php pop_line();?></TD></TR>
<TR id="menu_tabulator_" STYLE="display:none"><TD><?php pop_line();?></TD></TR>
<TR id="menu_tabulator_" STYLE="display:none"><TD><?php pop_menu(0,"document.form1.form_add.value='tabuItem';document.form1.submit();",$lang[1964]);?></TD></TR>
<?php $opt["val"] = array(1,2,3);
$opt["desc"] = array($lang[1144],$lang[1146],'main menu');?>
<TR id="menu_tabulator_" STYLE="display:none"><TD><?php pop_select("fill_style('38','',this.value);divclose();set_posxy();",$opt,"",1,"input_tabuItemPos",$lang[2708],50);?></TD></TR>
<?php $opt["val"] = array(0,1);
$opt["desc"] = array($lang[2709],$lang[2710]);?>
<TR id="menu_tabulator_menue_" STYLE="display:none"><TD><?php pop_select("fill_style('39','',this.value);divclose();set_posxy();",$opt,"",1,"input_tabuItemMemPos",$lang[2072],50);?></TD></TR>
<TR id="menu_menue_" STYLE="display:none"><TD><?php pop_submenu2($lang[2331],"limbasDivShow(this,'menu','menu_choice');",$lang[2331]);?></TD></TR>
<TR id="menu_line_" STYLE="display:none"><TD>
<?php pop_checkbox("input_line_reverse","fill_style('33','',this.checked)","input_line_reverse",null,null,null,$lang[1108])?>
</TD></TR>
<?php $opt['val'] = array("","1","2","3","4","5","6","7","8","9","10","11","12","13","14","15","16","17","18","19","20","21","22","23","24","25","26","27","28","29","30");
$opt['desc'] = array("","1","2","3","4","5","6","7","8","9","10","11","12","13","14","15","16","17","18","19","20","21","22","23","24","25","26","27","28","29","30");?>
<TR id="menu_tab_" STYLE="display:none"><TD><?php pop_select("divclose();set_posxy();document.form1.form_tab_cols.value=this.value;document.form1.submit();",$opt,"",1,"input_tabcols",$lang[1109],50);?></TD></TR>
<?php $opt['val'] = array("","1","2","3","4","5","6","7","8","9","10","11","12","13","14","15","16","17","18","19","20","21","22","23","24","25","26","27","28","29","30","31","32","33","34","35","36","37","38","39","40");
$opt['desc'] = array("","1","2","3","4","5","6","7","8","9","10","11","12","13","14","15","16","17","18","19","20","21","22","23","24","25","26","27","28","29","30","31","32","33","34","35","36","37","38","39","40");?>
<TR id="menu_tab_" STYLE="display:none"><TD><?php pop_select("divclose();set_posxy();document.form1.form_tab_rows.value=this.value;document.form1.submit();",$opt,"",1,"input_tabrows",$lang[88],50);?></TD></TR>
<TR id="menu_tab_" STYLE="display:none"><TD><?php pop_line();?></TD></TR>
<?php
if($sysclass){
$opt['val'] = $sysclass['val'];
$opt['desc'] = $sysclass['val'];
$opt['label'] = $sysclass['label'];
?>
<TR id="menu_multi_text_php_bild_chart_templ_tab_datum_relpath_notice_filter_globsearch_dbdat_dbdesc_dbnew_dbsearch_submt_button_inptext_inphidden_inparea_inpselect_inpcheck_inpradio_wflhist_uform_tile_tabuItem_frame_tabcell_" STYLE="display:none"><TD><?php pop_select("fill_style('44','class',this.value);",$opt,"",1,"input_class","Class",50);?></TD></TR>
<?php }?>
<?php
$opt = array();
$opt['val'] = array("","0.05","0.1","0.15","0.2","0.25","0.3","0.35","0.4","0.45","0.5","0.55","0.6","0.65","0.7","0.75","0.8","0.85","0.9","0.95","1");
$opt['desc'] = array("","5%","10%","15%","20%","25%","30%","35%","40%","45%","50%","55%","60%","65%","70%","75%","80%","85%","90%","95%","100%");?>
<TR id="menu_multi_text_php_bild_chart_templ_datum_relpath_notice_filter_globsearch_dbdat_dbdesc_rect_line_ellipse_tab_stab_bnr_snr_formel_frame_uform_tile_tabcell_" STYLE="display:none"><TD><?php pop_select("fill_style('25','opacity',this.value);",$opt,"",1,"input_opacity",$lang[2093],50);?></TD></TR>
<?php
$opt['val'] = array("","none","block","inline","inline-block","list-item","run-in");
$opt['desc'] = array("","none","block","inline","inline-block","list-item","run-in");?>
<TR id="menu_multi_text_php_bild_chart_templ_datum_relpath_notice_filter_globsearch_dbdat_dbdesc_rect_line_ellipse_tab_stab_bnr_snr_formel_frame_uform_tabcell_" STYLE="display:none"><TD><?php pop_select("fill_style('26','display',this.value);",$opt,"",1,"input_display",$lang[2638],50);?></TD></TR>

<?php
$opt['val'] = array("","block","tile");
$opt['desc'] = array("","block","tile");?>
<TR id="menu_tile_" STYLE="display:none"><TD><?php pop_select("fill_style('26','tile',this.value);",$opt,"",1,"input_tile",$lang[2638],50);?></TD></TR>

<?php
$opt['val'] = array("","2","3","4","5","6","7","8","9","10","11","12","13","14","15","16","17","18","19","20");
$opt['desc'] = $opt['val'];?>
<TR id="menu_tabcell_" STYLE="display:none"><TD><?php pop_select("fill_style('37','rowspan',this.value);",$opt,"",1,"input_rowspan",$lang[2178],50);?></TD></TR>

<?php
$opt['val'] = array(0,1,2,3);
$opt['desc'] = array("",$lang[2303],$lang[2692],$lang[2321]);?>
<TR id="menu_dbdat_inptext_inparea_inpselect_inpcheck_" STYLE="display:none"><TD><?php pop_select("fill_style('35','',this.value);",$opt,"",1,"input_readonly",$lang[2457],50);?></TD></TR>

<?php
$opt = null;
$opt['val'][] = '';$opt['desc'][] = '';
foreach($gformlist as $key0 => $value0){
	$opt['val'][] = '';
	$opt['desc'][] = "---".$gtab["desc"][$key0]."---";
	foreach($value0["id"] as $key => $value){
		if($value0["typ"][$key] == 1){
		$opt['val'][] = $value;
		$opt['desc'][] = $value0["name"][$key];
		}
	}
}
?>
<TR id="menu_dbdat_" STYLE="display:none"><TD><?php pop_select("fill_style('43','input_formid',this.value);",$opt,"",1,"input_formid",$lang[1179],50);?></TD></TR>
<TR id="menu_inptext_dbdat_" STYLE="display:none"><TD><?php pop_input2("fill_style('36','maxlength',this.value);","input_maxlenght","","",$lang[2458],65,'oninput')?></TD></TR>
<?php $opt['val'] = array("1","2");
$opt['desc'] = array($lang[1962],$lang[1963]);?>
<?php /*<TR id="menu_tab_" STYLE="display:none"><TD><?php pop_select("fill_style('41','',this.value);",$opt,"",1,"input_tab_stab_choice",$lang[1961],50);?></TD></TR>*/?>

<TR id="menu_frame_php_js_" STYLE="display:none"><TD><?php pop_submenu2($lang[2660],"limbasDivShow(this,'menu','menu_value');cmeditor.refresh();",$lang[2660]);?></TD></TR>
<TR id="menu_tabulator_uform_tile_dbdat_inptext_inparea_inpselect_inpcheck_inpradio_chart_wflhist_reminder_filter_globsearch_" STYLE="display:none"><TD><?php pop_submenu2($lang[2331],"limbasDivShow(this,'menu','menu_parameters');",$lang[2331]);?></TD></TR>
<TR id="menu_text_php_datum_relpath_notice_filter_globsearch_dbdesc_dbnew_dbsearch_submt_button_inptext_inphidden_inparea_inpselect_inpcheck_inpradio_uform_tile_scroll_wflhist_reminder_tabmenu_tabcell_" STYLE="display:none"><TD><?php pop_submenu2($lang[923],"limbasDivShow(this,'menu','menu_title');",$lang[923]);?></TD></TR>
<?php if(lmb_count($snap['name']) > 0){?><TR id="menu_uform_tile_" STYLE="display:none"><TD><?php pop_submenu2($lang[1967],"limbasDivShow(this,'menu','menu_snap');",$lang[1967]);?></TD></TR><?php }?>
<TR id="menu_dbdat_dbdesc_" STYLE="display:none"><TD>
<?php pop_checkbox("replace_element","if(this.checked){document.form1.form_replace_element.value=document.form1.form_edit_id.value;}else{document.form1.form_replace_element.value=''}","replace_element",null,null,null,$lang[2460])?>
</TD></TR>
<TR id="menu_multi_js_php_text_bild_chart_templ_datum_relpath_notice_filter_globsearch_dbdat_dbdesc_dbnew_dbsearch_rect_line_ellipse_submt_button_inptext_inphidden_inparea_inpselect_inpcheck_inpradio_menue_tabulator_frame_scroll_wflhist_reminder_tabmenu_tab_stab_uform_tile_tabuItem_" STYLE="display:none"><TD><?php pop_line();?></TD></TR>
<TR id="menu_multi_js_php_text_bild_chart_templ_datum_relpath_notice_filter_globsearch_dbdat_dbdesc_dbnew_dbsearch_rect_line_ellipse_submt_button_inptext_inphidden_inparea_inpselect_inpcheck_inpradio_menue_tabulator_frame_scroll_wflhist_reminder_tabmenu_tab_stab_uform_tile_tabuItem_" STYLE="display:none"><TD><?php pop_menu(0,"lmb_dropEl('".$lang[1099]." ".$lang[160]."');",$lang[160]);?></TD></TR>
<TR><TD><?php pop_bottom();?></TD></TR>
</TABLE></FORM></DIV>



<?php /*----------------- Events-Style DIV -------------------*/?>
<DIV ID="menu_events" class="lmbContextMenu" style="display:none;position:absolute;top:<?=$menuposy?>;left:<?=$menuposx?>;z-index:10001;"  onclick="activ_menu=1">
<?php pop_top('menu_events');?>
<?php pop_submenu2($lang[1764],"limbasDivShow(this,'menu_events','menu_event1');",$lang[1764]);?>
<?php pop_submenu2($lang[1765],"limbasDivShow(this,'menu_events','menu_event2');",$lang[1765]);?>
<?php pop_submenu2($lang[1768],"limbasDivShow(this,'menu_events','menu_event5');",$lang[1768]);?>
<?php pop_submenu2($lang[1766],"limbasDivShow(this,'menu_events','menu_event3');",$lang[1766]);?>
<?php pop_submenu2($lang[1767],"limbasDivShow(this,'menu_events','menu_event4');",$lang[1767]);?>
<?php pop_bottom();?>
</DIV>

<?php /*----------------- Event-OnClick DIV -------------------*/?>
<DIV ID="menu_event1" class="lmbContextMenu" style="display:none;position:absolute;top:<?=$menuposy?>;left:<?=$menuposx?>;z-index:10002;"  onclick="activ_menu=1"><FORM NAME="event_form1">
<?php pop_top('menu_event1');?>
<?php pop_left();?>
&nbsp;<INPUT TYPE="TEXT" maxlength="180" NAME="event1" STYLE="width:140px;height:20px;background-color:<?= $farbschema['WEB8'] ?>;border:1px solid <?=$farbschema['WEB1']?>;" OnChange="lmb_setFormEvent(this,'CLICK');">
<?php pop_right();?>
<?php pop_bottom();?>
</FORM></DIV>

<?php /*----------------- Event-OnDblClick DIV -------------------*/?>
<DIV ID="menu_event2" class="lmbContextMenu" style="display:none;position:absolute;top:<?=$menuposy?>;left:<?=$menuposx?>;z-index:10002;" onclick="activ_menu=1"><FORM NAME="event_form2">
<?php pop_top('menu_event2');?>
<?php pop_left();?>
&nbsp;<INPUT TYPE="TEXT" maxlength="180" NAME="event2" STYLE="width:140px;height:20px;background-color:<?= $farbschema['WEB8'] ?>;border:1px solid <?=$farbschema['WEB1']?>;" OnChange="lmb_setFormEvent(this,'DBLCLICK');">
<?php pop_right();?>
<?php pop_bottom();?>
</FORM></DIV>

<?php /*----------------- Event-OnMouseOver DIV -------------------*/?>
<DIV ID="menu_event3" class="lmbContextMenu" style="display:none;position:absolute;top:<?=$menuposy?>;left:<?=$menuposx?>;z-index:10002;" onclick="activ_menu=1"><FORM NAME="event_form3">
<?php pop_top('menu_event3');?>
<?php pop_left();?>
&nbsp;<INPUT TYPE="TEXT" maxlength="180" NAME="event3" STYLE="width:140px;height:20px;background-color:<?= $farbschema['WEB8'] ?>;border:1px solid <?=$farbschema['WEB1']?>;" OnChange="lmb_setFormEvent(this,'OVER');">
<?php pop_right();?>
<?php pop_bottom();?>
</FORM></DIV>

<?php /*----------------- Event-OnMouseOut DIV -------------------*/?>
<DIV ID="menu_event4" class="lmbContextMenu" maxlength="180" style="display:none;position:absolute;top:<?=$menuposy?>;left:<?=$menuposx?>;z-index:10002;" onclick="activ_menu=1"><FORM NAME="event_form4">
<?php pop_top('menu_event4');?>
<?php pop_left();?>
&nbsp;<INPUT TYPE="TEXT" NAME="event4" STYLE="width:140px;height:20px;background-color:<?= $farbschema['WEB8'] ?>;border:1px solid <?=$farbschema['WEB1']?>;" OnChange="lmb_setFormEvent(this,'OUT');">
<?php pop_right();?>
<?php pop_bottom();?>
</FORM></DIV>

<?php /*----------------- Event-OnChange DIV -------------------*/?>
<DIV ID="menu_event5" class="lmbContextMenu" maxlength="180" style="display:none;position:absolute;top:<?=$menuposy?>;left:<?=$menuposx?>;z-index:10002;" onclick="activ_menu=1"><FORM NAME="event_form5">
<?php pop_top('menu_event5');?>
<?php pop_left();?>
&nbsp;<INPUT TYPE="TEXT" NAME="event5" STYLE="width:140px;height:20px;background-color:<?= $farbschema['WEB8'] ?>;border:1px solid <?=$farbschema['WEB1']?>;" OnChange="lmb_setFormEvent(this,'CHANGE');">
<?php pop_right();?>
<?php pop_bottom();?>
</FORM></DIV>

<?php /*----------------- PIC-INFO DIV -------------------*/?>
<DIV ID="menu_picinfo" class="lmbContextMenu" style="position:absolute;display:none;z-index:10001;"  onclick="activ_menu=1"><FORM NAME="picinfo_form">
<?php pop_top('menu_picinfo');?>
<?php pop_left();?>
&nbsp;<TEXTAREA READONLY NAME="picinfo_val" STYLE="width:140px;height:150px;background-color:<?= $farbschema['WEB8'] ?>;"></TEXTAREA>
<?php pop_right();?>
<?php pop_bottom();?>
</FORM></DIV>

<?php /*----------------- Zeichensatz-Farbe DIV -------------------*/?>
<DIV ID="menu_color" class="lmbContextMenu" style="position:absolute;display:none;z-index:10001;"  onclick="activ_menu=1"><FORM NAME="fcolor_form">
<?php pop_top('menu_color');?>
<?php pop_color(null,  null, 'menu_color');?>
<?php pop_bottom();?>
</FORM></DIV>

<?php /*----------------- Zeichensatz-Style DIV -------------------*/?>
<DIV ID="menu_fontstyle" class="lmbContextMenu" style="display:none;position:absolute;top:<?=$menuposy?>;left:<?=$menuposx?>;z-index:10001;" onclick="activ_menu=1"><FORM NAME="fstyle_form">
<?php pop_top('menu_fontstyle');?>

<?php $opt['val'] = array("normal","italic");
$opt['desc'] = array($lang[1123],$lang[1124]);?>
<?php pop_select("fill_style('1','fontStyle',this.value)",$opt,"",1,"input_fontstyle",$lang[1115],0, 'lmb-italic');?>

<?php $opt['val'] = array("normal","bold");
$opt['desc'] = array($lang[1123],$lang[1125]);?>
<?php pop_select("fill_style('4','fontWeight',this.value)",$opt,"",1,"input_fontweight",$lang[1116],0, 'lmb-bold');?>

<?php $opt['val'] = array("none","underline");
$opt['desc'] = array($lang[1123],$lang[1126]);?>
<?php pop_select("fill_style('7','textDecoration',this.value)",$opt,"",1,"input_fontdeco",$lang[1117],0, 'lmb-underline');?>

<?php $opt['val'] = array("none","uppercase","lowercase");
$opt['desc'] = array($lang[1123],$lang[1127],$lang[1128]);?>
<?php pop_select("fill_style('8','textTransform',this.value)",$opt,"",1,"input_fonttransf",$lang[1118],0, 'lmb-font');?>

<?php $opt['val'] = array("justify","left","center","right");
$opt['desc'] = array($lang[1129],$lang[1130],$lang[1131],$lang[1132]);?>
<?php pop_select("fill_style('12','textAlign',this.value)",$opt,"",1,"input_fontalign",$lang[1119],0, 'lmb-align-left');?>

<?php $opt['val'] = array("top","middle","bottom","baseline","sub","super","text-top","text-bottom");
$opt['desc'] = array($lang[1490],$lang[1491],$lang[1492],$lang[1493],$lang[1494],$lang[1495],$lang[1496],$lang[1497]);?>
<?php pop_select("fill_style('23','verticalAlign',this.value)",$opt,"",1,"input_fontvalign",$lang[1119],0, 'lmb-align-bottom');?>

<?php $opt['val'] = array("1px","2px","3px","4px","5px","6px","7px","8px","9px","10px");
$opt['desc'] = array("1","2","3","4","5","6","7","8","9","10");?>
<?php pop_select("fill_style('11','lineHeight',parseInt(this.value));",$opt,"",1,"input_lineheight",$lang[1120],95, 'lmb-space-v');?>

<?php $opt['val'] = array("1px","2px","3px","4px","5px","6px","7px","8px","9px","10px");
$opt['desc'] = array("1","2","3","4","5","6","7","8","9","10");?>
<?php pop_select("fill_style('6','letterSpacing',parseInt(this.value));",$opt,"",1,"input_letterspacing",$lang[1121],95, 'lmb-space-h');?>

<?php $opt['val'] = array("1px","2px","3px","4px","5px","6px","7px","8px","9px","10px");
$opt['desc'] = array("1","2","3","4","5","6","7","8","9","10");?>
<?php pop_select("fill_style('5','wordSpacing',parseInt(this.value));",$opt,"",1,"input_wordspacing",$lang[1122],95, 'lmb-space-words');?>

<?php pop_bottom();?>
</FORM></DIV>

<?php /*----------------- Bild-History DIV -------------------*/?>
<DIV ID="menu_pichistory" class="lmbContextMenu" style="position:absolute;display:none;z-index:10001;"  onclick="activ_menu=1"><FORM NAME="pichistory_form">
<?php pop_top('menu_pichistory');?>
<?php pop_left();?>
&nbsp;<TEXTAREA ID="pichistory" NAME="pichistory" WRAP="physical" STYLE="width:140px;height:100px;background-color:<?= $farbschema['WEB8'] ?>;" READONLY></TEXTAREA>
<?php pop_right();?>
<?php pop_bottom();?>
</FORM></DIV>

<?php /*----------------- Menu Funktionsauswahl -------------------*/?>
<DIV ID="menu_choice" class="lmbContextMenu" style="position:absolute;display:none;z-index:10001;" onclick="activ_menu=1"><FORM NAME="menuchoice_form">
<?php pop_top('menu_choice');?>

<?php pop_left();?>
&nbsp;<SELECT NAME="input_menu_choice" multiple size="5" style="width:140px;" OnChange="set_multiselect_val('42',this)">
<option value="0"><?=$lang[2554]?></option>
<option value="1"><?=$lang[2555]?></option>
<option value="2"><?=$lang[2557]?></option>
<option value="3"><?=$lang[2713]?></option>
<option value="4"><?=$lang[2988]?></option>
</SELECT>
<?php pop_right();?>
<?php pop_bottom();?>
</FORM></DIV>

<?php /*----------------- Parameters DIV -------------------*/?>
<DIV ID="menu_parameters" class="lmbContextMenu" style="display:none;position:absolute;top:<?=$menuposy?>;left:<?=$menuposx?>;z-index:10002;"  onclick="activ_menu=1"><FORM NAME="parameters">
<?php $paramWidth = 250;pop_top('menu_parameters',$paramWidth);?>
<?php pop_left($paramWidth);?>
<div id="relation_params"></div>
<TEXTAREA ID="lmb_subform_params" NAME="lmb_subform_params" STYLE="width:300px;height:150px;background-color:<?= $farbschema['WEB8'] ?>;border:1px solid <?=$farbschema['WEB1']?>;" OnChange="if(this.value){document.form1.form_parameters.value=this.value;}else{document.form1.form_parameters.value='null'};divclose();document.form1.submit();"></TEXTAREA>
<?php pop_right();?>
<?php pop_bottom($paramWidth);?>
</FORM></DIV>

<?php /*----------------- Title DIV -------------------*/?>
<DIV ID="menu_title" class="lmbContextMenu" style="display:none;position:absolute;top:<?=$menuposy?>;left:<?=$menuposx?>;z-index:10002;"  onclick="activ_menu=1"><FORM NAME="titles">
<?php $paramWidth = 250;pop_top('menu_title',$paramWidth);?>
<?php pop_left($paramWidth);?>
&nbsp;<TEXTAREA NAME="lmb_subform_title" STYLE="width:300px;height:150px;background-color:<?= $farbschema['WEB8'] ?>;border:1px solid <?=$farbschema['WEB1']?>;" OnChange="if(this.value){document.form1.form_title.value=this.value;}else{document.form1.form_title.value='null'};divclose();document.form1.submit();"></TEXTAREA>
<?php pop_right();?>
<?php pop_bottom($paramWidth);?>
</FORM></DIV>

<?php /*----------------- Snap DIV -------------------*/?>
<DIV ID="menu_snap" class="lmbContextMenu" style="display:none;position:absolute;top:<?=$menuposy?>;left:<?=$menuposx?>;z-index:10001;"  onclick="activ_menu=1"><FORM NAME="snap">
<?php pop_top('menu_snap');?>
<?php /*----------------- Auswahl Schnappschuss -------------------*/
foreach ($snap["name"] as $key0 => $value0){
	pop_submenu2($gtab["desc"][$key0],"limbasDivShow(this,'menu_snap','menu_snap_$key0');",$gtab["desc"][$key0]);
}
?>
<?php pop_bottom();?>
</FORM></DIV>

<?php /*----------------- Value DIV -------------------*/?>
<DIV ID="menu_value" style="position:absolute;display:none;z-index:10001;padding:5px;width:575px;height:200px;" onclick="activ_menu=1">
<FORM NAME="fvalue_form">
<textarea class="lmbContextMenu" id="lmb_subform_value" style="z-index:10002;width:100%;height:100%;"> </textarea>
</FORM>
</DIV>

<Script language="JavaScript">
    var cmeditor = CodeMirror.fromTextArea(document.getElementById("lmb_subform_value"), {
        lineNumbers: true,
        matchBrackets: true,
        mode: "text/x-php",
        indentUnit: 4,
        indentWithTabs: true,
        enterMode: "indent",
        tabMode: "shift"
    });

    // update textarea element on cm change
    cmeditor.on('change', function() {
        selectedInput.val(cmeditor.getValue());
    });

    // update cm on textarea change
    $(function() {
        $('[lmbtype=frame],[lmbtype=php],[lmbtype=js]').on('input', function() {
            cmeditor.setValue(selectedInput.val());
        });
    });

</Script>





<?php
foreach ($snap["name"] as $key0 => $value0){
	echo "<DIV class=\"lmbContextMenu\" ID=\"menu_snap_".$key0."\" style=\"display:none;position:absolute;top:" . $menuposy . ";left:" . $menuposx .";z-index:10002;\"  onclick=\"activ_menu=1\"><FORM NAME=\"menusnap_".$key0."_form\">\n";
	pop_top("menu_snap_".$key0);
	pop_left();
	echo "&nbsp;<SELECT multiple NAME=\"input_menu_snap_sub\" ID=\"input_menu_snap_$key0\" style=\"width:140px;height:100px;\" OnClick=\"fill_style(43,'SNAP',this.options[this.options.selectedIndex].value);limbasDivClose('menu')\">";
	foreach ($snap['name'][$key0] as $key => $value){
		if($gsnap[$key0]["owner"][$key]!=0)
			echo "<OPTION VALUE=\"$key\">".$value."</option>";
	}
	echo "</SELECT>";
	pop_right();
	pop_bottom();
	echo "</FORM></DIV>\n\n";
}
?>


<FORM ACTION="main_admin.php" METHOD="post" name="form1">
<input type="hidden" name="action" value="setup_form_main">
<input type="hidden" name="form_id" VALUE="<?= $form_id ?>">
<input type="hidden" name="form_name" VALUE="<?= $form_name ?>">
<input type="hidden" name="referenz_tab" VALUE="<?= $referenz_tab ?>">
<input type="hidden" name="form_typ" value="<?= $form["form_typ"] ?>">
<input type="hidden" name="form_frame" value="<?= $form["form_frame"] ?>">
<input type="hidden" name="form_framesize" value="<?= $form["form_framesize"] ?>">
<input type="hidden" name="localsessionval" value="<?=$localsessionval?>">
<input type="hidden" name="change_id">
<input type="hidden" name="default_font">
<input type="hidden" name="default_size">
<input type="hidden" name="new_text">
<input type="hidden" name="menuposx">
<input type="hidden" name="menuposy">
<input type="hidden" name="uform_style">
<input type="hidden" name="uform_typ">
<input type="hidden" name="uform_set">
<input type="hidden" name="form_copy">
<input type="hidden" name="form_move">
<input type="hidden" name="form_possize">
<input type="hidden" name="form_setstyle">
<input type="hidden" name="form_del">
<input type="hidden" name="form_edit_id">
<input type="hidden" name="form_parameters">
<input type="hidden" name="form_event1">
<input type="hidden" name="form_pic_style">
<input type="hidden" name="form_posxy_edit">
<input type="hidden" name="form_redirect">
<input type="hidden" name="form_add">
<input type="hidden" name="form_replace_element">
<input type="hidden" name="form_title">
<input type="hidden" name="form_raster">
<input type="hidden" name="form_chart_id">
<input type="hidden" name="form_templ_id">
<input type="hidden" name="set_new_zindex">
<input type="hidden" name="form_tab">
<input type="hidden" name="form_tab_size">
<input type="hidden" name="form_tab_rows">
<input type="hidden" name="form_tab_cols">
<input type="hidden" name="form_tab_el">

<input type="hidden" name="form_dbdat_fieldname">
<input type="hidden" name="form_dbdat_fieldid">
<input type="hidden" name="form_dbdat_tabid">
<input type="hidden" name="form_dbdat_tabgroup">
<input type="hidden" name="form_dbdat_parentrel">
<input type="hidden" name="form_dbdat_parentrelpath">

<input type="hidden" name="form_stab_snapid">
<input type="hidden" name="form_stab_show">

<input type="hidden" name="aktiv_id">
<input type="hidden" name="aktiv_tabulator">
<input type="hidden" name="aktiv_tabcontainer">

<input type="hidden" name="tabelement">
<?php


/*------- Style ---------*/
function set_style_form($textstyle){
	global $styletyp;
	$bzm1 = 0;
	foreach ($styletyp as $key => $value){
		if(($textstyle[$key] OR $textstyle[$key] == '0') AND $textstyle[$key] != " "){
			$stylevalue .= $styletyp[$key].$textstyle[$key].";";
		}
	}
	return $stylevalue;
}

function get_title($form_ID,$form_event){
	$title = "ID: $form_ID";
	if($form_event[0]){$title .= "\nOnClick: ".htmljs(htmlentities($form_event[0],ENT_QUOTES,$GLOBALS["umgvar"]["charset"]));}
	if($form_event[1]){$title .= "\nOnDblClick: ".htmljs(htmlentities($form_event[1],ENT_QUOTES,$GLOBALS["umgvar"]["charset"]));}
	if($form_event[2]){$title .= "\nOnMouseOver: ".htmljs(htmlentities($form_event[2],ENT_QUOTES,$GLOBALS["umgvar"]["charset"]));}
	if($form_event[3]){$title .= "\nOnMouseOut: ".htmljs(htmlentities($form_event[3],ENT_QUOTES,$GLOBALS["umgvar"]["charset"]));}
	if($form_event[4]){$title .= "\nOnChange: ".htmljs(htmlentities($form_event[4],ENT_QUOTES,$GLOBALS["umgvar"]["charset"]));}
	return $title;
}


/*----------------- Tabellen -------------------*/
function print_tab($form_ID,$printParams){
	global $farbschema;
	global $form_id;
	global $form;
	global $db;
	
	$form_width = $printParams["WIDTH"];
	$form_height = $printParams["HEIGHT"];
	$form_posx = $printParams["POSX"];
	$form_posy = $printParams["POSY"];
	$form_zindex = $printParams["ZINDEX"];
	$form_style = $printParams["STYLE"];
	$form_value = $printParams["VALUE"];
	$form_event = $printParams["EVENT"];
	$form_tab = $printParams["TAB"];
	$form_tab_el = $printParams["TAB_EL"];
	$form_tab_size = $printParams["TAB_SIZE"];
	$tab_choice = $printParams["TAB_CHOICE"];
	$parameter = $printParams["PARAMETERS"];
	$title = $printParams["TITLE"];

	$tab_size = explode(";",$form_tab_size);
	$form_tab_cells = $tab_size[0];
	$form_tab_rows = $tab_size[1];

	$formstyle = explode(";",$form_style);
	$stylevalue = set_style_form($formstyle);

	# Zusatzparameter für Checkboxen am Zeilenbeginn
	$addpar[] = "1*form_menu*input_tab_choice*".$tab_choice;

	if(!$form_tab_el){$position = "position:absolute; left:".$form_posx."; top:".$form_posy;}
	if($formstyle[24]){$height = "height:$form_height;";}
	$style = "style=\"$position; $height width:$form_width; z-index:".$form_zindex."; ".$stylevalue.";overflow:visible;\"";
	$onMousedown = "OnMousedown=\"limbasMenuOpen(event,this,'".$form_ID."',new limbasDictionary('PARAMETERS','','TYP','tab','STYLE','".$form_style.";".$form_tab_rows.";".$form_tab_cells."','ADDPAR','".implode("#",$addpar)."','COLS','".$tab_size[0]."','ROWS','".$tab_size[1]."','CLASS','".$printParams["CLASS"]."'));\"";
	$onMouseOver = "OnMouseOver=\"document.getElementById('tabheader_$form_ID').style.visibility='visible';\"";
	$onMouseOut = "OnMouseOut=\"document.getElementById('tabheader_$form_ID').style.visibility='hidden';\"";
	echo "<div id=\"div".$form_ID."\"  class=\"".$printParams["CLASS"]."\" $style>\n";
	if($form_tab_cells AND $form_tab_rows){
		echo "\n<table id=\"tab_".$form_ID."\" style=\"border-collapse:".$formstyle[32].";width:100%;\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" $onMouseOver $onMouseOut>\n";
		$tdsize = explode(";",$parameter);
		
		# header
		echo "<tr id=\"tabheader_$form_ID\" style=\"visibility:hidden;background-color:".$farbschema['WEB7']."\">";
		$bzm3 = 0;
		while($bzm3 < $form_tab_cells){
			if(!$tdsize[$bzm3]){$tdsize[$bzm3] = '50px';}
			echo "<th id=\"tab_el_".$form_ID."_0_".$bzm3."\" style=\"width:".$tdsize[$bzm3].";height:15px;cursor:move\" $onMousedown>&nbsp;</th>";
		$bzm3++;
		}
		echo "</tr>";

		$bzm2 = 1;
		while($bzm2 <= $form_tab_rows){
			echo "<tr id=\"tr_$bzm2\">\n";
			$bzm3 = 1;
			$colspan = "";
			while($bzm3 <= $form_tab_cells){
				if($colspan-- > 1){$bzm3++;continue;}
				/* --- Tab_Elemente --------------------------------------------- */
				if($tab_cell_id = $form["tab_cell"][$form_tab][$bzm3][$bzm2]){
					$printParams = formElementParams($form,$tab_cell_id);
					$printParams["MAINELEMENT"] = 1;
					$cellstyle = explode(";",$form["style"][$tab_cell_id]);
					if($cellstyle[37]){$colspan = $cellstyle[37]; $CLSP = "colspan=\"".$cellstyle[37]."\"";}else{$CLSP = "";}
					printFormularElement($tab_cell_id,'tabcell',$printParams,null,null,null,array($form_tab,$bzm2,$bzm3));
				}else{
					echo "<td style=\"overflow:hidden;background-color:transparent;border:1px dotted ".$farbschema['WEB1'].";\" VALIGN=\"TOP\" ID=\"tab_el_".$form_ID."_".$bzm2."_".$bzm3."\" OnClick=\"add_tabelement('".$form_ID."','".$form_tab."','".$bzm2."','".$bzm3."');\" $CLSP>\n";
				}

				if(!formElementList(null,null,array($form_tab,$bzm2,$bzm3))){
					echo "<div style=\"width:15;overflow:hidden;height:12;\"></div>";
				}

				echo "</td> ";
				$bzm3++;
			}
			echo "\n</tr>\n";
			$bzm2++;
		}
		echo "</table>\n";
	}

	$GLOBALS['printedtabs'][] = $form_ID;

	echo "</div>\n";
}

function printFormularElement($form_ID,$elementType,$printParams,$closediv=null,$elementExtension=null,$extendetStyle=null,$tabCell=null)
{
	global $farbschema;
	global $umgvar;
	global $formliste;
	global $form;
	global $gtab;

	if($printParams["STYLE"])
	{
		$st = explode(";",$printParams["STYLE"]);
	}

	if($elementType == 'uform')
	{
		$target_table = $formliste["referenz_tab"][$printParams["VALUE"]];
	}

	//TITLE
	if($elementType == 'dbsearch' || $elementType == 'dbnew' || $elementType == 'dbdat' || $elementType == 'dbdesc' || $elementType == 'text' || $elementType == 'rect' || $elementType == 'line' || $elementType == 'ellipse' || $elementType == 'datum' || $elementType == 'relpath' || $elementType == 'notice' || $elementType == 'filter' || $elementType == 'globsearch' || $elementType == 'bild')
	{
		$title = get_title($form_ID,$form_event);
	}

	// CLASS
	if($printParams["CLASS"]){
		$pcl = explode(".",$printParams["CLASS"]);
		$class = "CLASS=\"".$pcl[lmb_count($pcl)-1]."\"";
	}

	//STYLE
	#if(!$class){$stylevalue = set_style_form($st);} // not use of style
	$stylevalue = set_style_form($st);


	//SPECIAL PARAMETERS FOR IMAGES
	if($elementType == 'bild')
	{
		$size1 = "width=\"".$printParams["WIDTH"]."\" height=\"".$printParams["HEIGHT"]."\"";
		if($form_convert){
		$path = TEMPPATH . 'thumpnails/form/' . $printParams["TAB_SIZE"];
		}else{
			$path = UPLOADPATH . 'form/'.$printParams["TAB_SIZE"];
		}
	}

	//SIZE OF ELEMENT
	if($printParams["WIDTH"] AND $printParams["HEIGHT"]){$size = ";width:".$printParams["WIDTH"]."; height:".$printParams["HEIGHT"];}else{$size = ";width:50; height:20;";}
	if($printParams["SUBEL"]){$posi = "relative";}else{$posi = "absolute";}

	
	
	if($printParams["IS_TAB_EL"]){
		$style = "style=\"position:relative;background-color:transparent;$stylevalue.$size\"";
	}else{

		if($elementType == "uform" || $elementType == "dbsearch" || $elementType == "dbdat" || $elementType == "dbdes" || $elementType == "dbnew")
		{
			$style = "style=\"position:$posi; left:".$printParams["POSX"]."; top:".$printParams["POSY"]."; z-index:".$printParams["ZINDEX"]."; ".$stylevalue.$size.";overflow:hidden;\"";
		}
		else
		{
			$style = "style=\"overflow:visible;position:$posi; left:".$printParams["POSX"]."; top:".$printParams["POSY"]."; z-index:".$printParams["ZINDEX"]."; ".$stylevalue.$size.";overflow:hidden;\"";
		}
	}
	
	
	
	############################################################################

	$params = "'STYLE','".$printParams["STYLE"]."'";
	$params = "$params,'TYP','$elementType'";
	$params = "$params,'IS_TAB_EL','".$printParams["IS_TAB_EL"]."'";
	$params = "$params,'CLASS','".$printParams["CLASS"]."'";
	$params = "$params,'MAINELEMENT','".$printParams["MAINELEMENT"]."'";
	$params = "$params,'SUBELEMENT','".$printParams["SUBELEMENT"]."'";
	$params = "$params,'CATEGORIE','".$printParams["CATEGORIE"]."'";
	$params = "$params,'TABLE_NAME','".$printParams["TABLE_NAME"]."'";
	$params = "$params,'FIELD_NAME','".$printParams["FIELD_NAME"]."'";
	$params = "$params,'FIELD_TYPE','".$printParams["FIELD_TYPE"]."'";
	$params = "$params,'TITLE','".htmljs($printParams["TITLE"])."'";

	if($elementType == 'php' || $elementType == 'uform' || $elementType == 'dbsearch' || $elementType == 'dbnew' || $elementType == 'dbdesc' || $elementType == 'dbdat' || $elementType == 'bild' || $elementType == 'usetime' || $elementType == 'inpradio' || $elementType == 'text' || $elementType == 'tabuItem' || $elementType == "js" || $elementType == "rect" || $elementType == "line" || $elementType == "ellipse" || $elementType == "datum" || $elementType == "relpath" || $elementType == "notice"  || $elementType == "filter"|| $elementType == "globsearch"||  $elementType == "inptext" || $elementType == "inphidden" || $elementType == "inparea" || $elementType == "inpselect" || $elementType == "inpcheck" || $elementType == "submt" || $elementType == "button" || $elementType == "tabcell")
	{
		#$params = "$params,'EVENT1','".htmljs(htmlentities($printParams["EVENT"][0],ENT_QUOTES,$GLOBALS["umgvar"]["charset"]))."'";
		$params = "$params,'EVENT1','".htmljs(urlencode(lmb_utf8_encode($printParams["EVENT"][0])))."'";
		$params = "$params,'EVENT2','".htmljs(urlencode(lmb_utf8_encode($printParams["EVENT"][1])))."'";
		$params = "$params,'EVENT3','".htmljs(urlencode(lmb_utf8_encode($printParams["EVENT"][2])))."'";
		$params = "$params,'EVENT4','".htmljs(urlencode(lmb_utf8_encode($printParams["EVENT"][3])))."'";
		$params = "$params,'EVENT5','".htmljs(urlencode(lmb_utf8_encode($printParams["EVENT"][4])))."'";
		$params = "$params,'PICSIZE','".$printParams["PIC_SIZE"]."'";
	}

	if($elementType == "uform")
	{
		# Zusatzparameter für Formularansicht (Bearbeitungsmodus)
		$addpar[] = "1*form_menu*input_uform_view*".$printParams["PIC_STYLE"];
		$params = "$params,'ADDPAR','".implode("#",$addpar)."'";
		$params = "$params,'PARAMETERS','".htmljs($printParams["PARAMETERS"])."'";
		$params = "$params,'PICSTYLE','".$printParams["PIC_STYLE"]."'";
		$params = "$params,'PICSIZE','".$printParams["PIC_SIZE"]."'";
	}
	else{
		$params = $params . ",'PARAMETERS','".htmljs($printParams["PARAMETERS"])."'";
	}

	if($elementType == "menue")
	{
		# Zusatzparameter
		$addpar[] = "3*menuchoice_form*input_menu_choice*".$printParams["PIC_SIZE"]; //!!!!
		$params = "$params,'ADDPAR','".implode("#",$addpar)."'";
	}
	
	if($tabCell){
		# Zusatzparameter Tabellenzelle
		$params = "$params,'CELL_TAB','".$tabCell[0]."','CELL_ROW','".$tabCell[1]."','CELL_COL','".$tabCell[2]."'";
	}

	$onMousedown = "OnMousedown=\"limbasMenuOpen(event,this,'".$form_ID."',new limbasDictionary($params));\"";

	//---- text js php rect line ellipse datum menue scroll tabmenu submt inptext inparea inpselect inpcheck inpradio usetime bild dbdat dbdesc dbnew dbsearch uform
	
	if($elementType == "reminder" || $elementType == "wflhist" || $elementType == "bild" || $elementType == "chart" || $elementType == "line" || $elementType == "ellipse" || $elementType == "scroll" || $elementType == "tabmenu" || $elementType == "menue" || $elementType == "tabulator" || $elementType == "frame"){
		echo "<div lmbtype=\"$elementType\" id=\"div".$form_ID."\" $onMousedown $class $style ";
		if($elementType == "bild")
		{
			echo "TITLE=\"$title\">";
			echo "<IMG SRC=\"".$path."\" ID=\"pic".$form_ID."\" $size1>";
		}
		else if($elementType == "line")
		{
			echo "TITLE=\"$title\">";
			echo "<script type=\"text/javascript\">var jg".$form_ID." = new jsGraphics(\"div".$form_ID."\");js_line('jg".$form_ID."','".$printParams["WIDTH"]."','".$printParams["HEIGHT"]."','$st[25]','$st[9]','$st[3]');</script>";
		}
		else if($elementType == "ellipse")
		{
			echo "TITLE=\"$title\">";
			echo "
			<script type=\"text/javascript\">
			var jg".$form_ID." = new jsGraphics(\"div".$form_ID."\");
			js_ellipse('jg".$form_ID."','".$printParams["WIDTH"]."','".$printParams["HEIGHT"]."','$st[9]','$st[3]');
			</script>
			";
		}
		else if($elementType == "scroll")
		{
			echo ">";
			echo "<< scroll-menu >>";
		}
		else if($elementType == "reminder")
		{
			echo " >";
			echo "<< reminder >>";
		}
		else if($elementType == "wflhist")
		{
			echo ">";
			echo "<< workflow history >>";
		}
		else if($elementType == "tabmenu")
		{
			echo ">";
			echo "<< table-menu >>";
		}
		
		else if($elementType == "chart")
		{
			echo ">";
			echo "<li class=\"lmb-icon lmb-line-chart\"></li>";
			echo ' <i>'.htmlentities($printParams["VALUE"],ENT_QUOTES,$GLOBALS["umgvar"]["charset"]).'</i>';
		}
		
		else if($elementType == "menue")
		{
			echo "READONLY>";
		}
		else if($elementType == "tabulator")
		{
			echo "READONLY>";
		}
		else if($elementType == "frame")
		{
			echo "READONLY>";
		}
		if(!$closediv){echo "</div>\n\n";}
		
	}elseif($elementType == "categorieItem"){
		echo "<div id=\"div".$form_ID."\" style=\"float:left;margin:1px;padding:2px;background-color:".$farbschema['WEB13'].";overflow:visible;cursor:pointer;".$extendetStyle."\" $elementExtension>";
		echo htmlentities($printParams["VALUE"],ENT_QUOTES,$GLOBALS["umgvar"]["charset"]);
		echo "</div>\n\n";
	}elseif($elementType == "tabuItem"){
		echo "<input type=\"text\" id=\"div".$form_ID."\" name=\"text".$form_ID."\" title=\"$title\" style=\"width:100px;background-color:".$farbschema['WEB13'].";height:100%;cursor:pointer;z-index:".$printParams["ZINDEX"].";".$extendetStyle."\" $onMousedown $elementExtension ";
		echo "value=\"".htmlentities($printParams["VALUE"],ENT_QUOTES,$GLOBALS["umgvar"]["charset"])."\" >";
	}else if($elementType == "stab" || $elementType == "text" || $elementType == "tabuItem" || $elementType == "inptext" || $elementType == "php" || $elementType == "js" || $elementType == "submt" || $elementType == "button" || $elementType == "inphidden" || $elementType == "inparea" || $elementType == "inpcheck" || $elementType == "inpradio" || $elementType == "inpselect" || $elementType == "rect" || $elementType == "datum" || $elementType == "relpath" || $elementType == "notice"  || $elementType == "filter"|| $elementType == "globsearch"|| $elementType == "usetime" || $elementType == "dbnew" || $elementType == "dbdat" || $elementType == "dbsearch" || $elementType == "dbdesc" || $elementType == "uform" || $elementType == "tile" || $elementType == "templ" ){
		
		if($elementType == 'dbdat' OR $elementType == 'dbdesc' OR $elementType == 'dbsearch' OR $elementType == 'dbdesc'){$wrap = "wrap=\"off\"";}else{$wrap="";}
		
		echo "<textarea lmbtype=\"$elementType\" lmbselectable=\"1\" id=\"div".$form_ID."\" $wrap $class $style $onMousedown";
		
		if($elementType == "text")
		{
		/*------- Text ---------*/
			echo " name=\"text".$form_ID."\" TITLE=\"$title\">";
			echo htmlentities($printParams["VALUE"],ENT_QUOTES,$GLOBALS["umgvar"]["charset"]);
		}
		else if($elementType == "php" || $elementType == "js" || $elementType == "inptext")
		{
		/*------- php js submit inptext  ---------*/
			echo " name=\"text".$form_ID."\">";
			echo htmlentities($printParams["VALUE"],ENT_QUOTES,$GLOBALS["umgvar"]["charset"]);
		}
        else if($elementType == "templ")
        {
            $templateTable = $gtab['desc'][$printParams['PIC_TYPE']];
            echo " title=\"$templateTable\"";
            echo " name=\"text".$form_ID."\">";
            echo htmlentities($printParams["VALUE"],ENT_QUOTES,$GLOBALS["umgvar"]["charset"]);
        }
		else if($elementType == "stab" || $elementType == "inphidden" || $elementType == "inparea" || $elementType == "inpselect" || $elementType == "inpcheck" || $elementType == "inpradio" || $elementType == "submt" || $elementType == "button")
		{
			echo " name=\"text".$form_ID."\">";
			echo htmlentities($printParams["VALUE"],ENT_QUOTES,$GLOBALS["umgvar"]["charset"]);
		}
		else if($elementType == "rect")
		{
			echo " READONLY TITLE=\"$title\">";
			echo "&nbsp;";
		}
		else if($elementType == "datum")
		{
			echo " READONLY TITLE=\"$title\">";
			echo local_date(1);
		}
		else if($elementType == "relpath")
		{
			echo " READONLY TITLE=\"$title\">";
			echo "relation path ->";
		}
		else if($elementType == "notice")
		{
			echo " READONLY TITLE=\"$title\">";
			echo "notice bar ->";
		}
        else if($elementType == "filter")
		{
			echo " READONLY TITLE=\"$title\">";
			echo "filter menue ->";
		}
        else if($elementType == "globsearch")
		{
			echo " READONLY TITLE=\"$title\">";
			echo "global search ->";
		}
		else if($elementType == "usetime")
		{
			echo " READONLY>";
			echo "12:00:00";
		}
		else if($elementType == "dbdat")
		{
			echo " READONLY TITLE=\"$title - ".htmlentities($printParams["VALUE"],ENT_QUOTES,$GLOBALS["umgvar"]["charset"])."\">";
			echo "ab|: ".htmlentities($printParams["VALUE"],ENT_QUOTES,$GLOBALS["umgvar"]["charset"]);
		}
		else if($elementType == "dbdesc")
		{
			echo " READONLY TITLE=\"$title - ".htmlentities($printParams["VALUE"],ENT_QUOTES,$GLOBALS["umgvar"]["charset"])."\">";
			echo "desc|: ".htmlentities($printParams["VALUE"],ENT_QUOTES,$GLOBALS["umgvar"]["charset"]);
		}
		else if($elementType == "dbnew")
		{
			echo " READONLY TITLE=\"$title - ".htmlentities($printParams["VALUE"],ENT_QUOTES,$GLOBALS["umgvar"]["charset"])."\">";
			echo "new|: ".htmlentities($printParams["VALUE"],ENT_QUOTES,$GLOBALS["umgvar"]["charset"]);
		}
		else if($elementType == "dbsearch")
		{
			echo " READONLY TITLE=\"$title - ".htmlentities($printParams["VALUE"],ENT_QUOTES,$GLOBALS["umgvar"]["charset"])."\">";
			echo "sear|: ".htmlentities($printParams["VALUE"],ENT_QUOTES,$GLOBALS["umgvar"]["charset"]);
		}
		else if($elementType == "uform")
		{
			echo " name=\"text".$form_ID."\" TITLE=\"$title\">";
			echo htmlentities($printParams["VALUE"],ENT_QUOTES,$GLOBALS["umgvar"]["charset"]);
		}
        else if($elementType == "tile")
        {
            echo " name=\"text".$form_ID."\" TITLE=\"$title\">";
            echo htmlentities($printParams["VALUE"],ENT_QUOTES,$GLOBALS["umgvar"]["charset"]);
        }
		else if($elementType == "stab")
		{
			echo " READONLY>";
			echo "system-table: ".$printParams["VALUE"];
		}
		echo "</textarea>\n\n";
	}elseif($elementType == "tab"){
		print_tab($form_ID,$printParams);
	}elseif ($elementType == "tabcell"){
		if($st[37]){$colsp = "colspan=\"".$st[37]."\"";}else{$colsp = "";}

		# Zentrierung geht nur ohne text-align
		$align = "align=\"".$st[12]."\"";
		$valign = "valign=\"".$st[23]."\"";
		#unset($st[23],$st[12]);
		$style = "style=\"border:1px dotted ".$farbschema['WEB1']."; ".set_style_form($st).";\"";
		
		echo "<td id=\"div".$form_ID."\" $colsp $class $style $valign $align ";
		echo $onMousedown;
		#echo " VALIGN=\"TOP\" OnClick=\"add_tabelement('".$form_ID."','".$tabCell[0]."','".$tabCell[1]."','".$tabCell[2]."');\">\n";
		echo ">\n";
	}else{
		die("<BODY BGCOLOR=\"{$farbschema['WEB8']}\"><BR><BR><CENTER><B><H3><FONT COLOR=\"red\" FACE=\"VERDANA,ARIAL,HELVETICA\">Form element not found: $elementType</FONT></H1></B></CENTER></BODY>");
	}
}



function formElementParams (&$form,$key){
	global $gtab;
	global $gfield;
	
	$printParams["WIDTH"] = $form["width"][$key];
	$printParams["HEIGHT"] = $form["height"][$key];
	$printParams["POSX"] = $form["posx"][$key];
	$printParams["POSY"] = $form["posy"][$key];
	$printParams["ZINDEX"] = $form["zindex"][$key];
	$printParams["STYLE"] = $form["style"][$key];
	$printParams["VALUE"] = $form["value"][$key];
	$printParams["EVENT"] = $form["event"][$key];
	$printParams["TAB"] = $form["tab"][$key];
	$printParams["TAB_EL"] = $form["tab_el"][$key];
	$printParams["TAB_SIZE"] = $form["tab_size"][$key];
	$printParams["TAB_ID"] = $form["tab_id"][$key];
	$printParams["PIC_TYPE"] = $form["pic_type"][$key];
	$printParams["TAB_GROUT"] = $form["tab_group"][$key];
	$printParams["PIC_STYLE"] = $form["pic_style"][$key];
	$printParams["PIC_SIZE"] = $form["pic_size"][$key];
	$printParams["FIELD_ID"] = $form["field_id"][$key];
	$printParams["PARAMETERS"] = str_replace('\\','\\\\',$form["parameters"][$key]);
	$printParams["TITLE"] = $form["title"][$key];
	$printParams["CLASS"] = $form["class"][$key];
	$printParams["SUBELEMENT"] = $form["subel"][$key];
	$printParams["CATEGORIE"] = $form["categorie"][$key];
	$printParams["TABLE_NAME"] = $gtab['desc'][$printParams["TAB_ID"]];
	$printParams["FIELD_NAME"] = $gfield[$printParams["TAB_ID"]]['spelling'][$printParams["FIELD_ID"]];
	$printParams["FIELD_TYPE"] = $gfield[$printParams["TAB_ID"]]['field_type'][$printParams["FIELD_ID"]];

	return $printParams;
}


/*----------------- Element-Schleife -------------------*/
function formElementList($subel=null,$categorie=null,$table=null){
	global $form;
	global $gfield;
	global $referenz_tab;
	global $localsession;
	global $farbschema;

	if($form["id"]){

	foreach ($form["id"] as $key => $value){

		if(!$table AND ($form["tab_el_col"][$key] OR $form["tab_el_row"][$key] OR $form["typ"][$key] == "tabuItem") OR $form["typ"][$key] == "tabcell"){continue;}
		if($table AND ($table[0] != $form["tab_el"][$key] OR $table[1] != $form["tab_el_row"][$key] OR $table[2] != $form["tab_el_col"][$key])){continue;}
		if($form["subel"][$key] AND !$subel){continue;}
		if($subel AND $form["subel"][$key] != $subel){continue;}
		if(!is_null($categorie) AND $form["categorie"][$key] != $categorie){continue;}
		
		# get params
		$printParams = formElementParams($form,$key);
		
		# some table cellelement settings
		if($table){
			$printParams["IS_TAB_EL"] = 1;
			if($form["form_typ"] == 2){
				$printParams["WIDTH"] = "100%";
			}
		}

		if($form["typ"][$key] == "menue"){
			#$printParams["ZINDEX"] = 0;
			$printParams["MAINELEMENT"] = 1;
			printFormularElement($key,$form["typ"][$key],$printParams,1);
			
			# all tabulators
			if($gfield[$referenz_tab]["sort"] AND in_array(100,$gfield[$referenz_tab]["field_type"])){
				echo "<div style=\"height:22px;background-color:".$farbschema['WEB1']."\">";
				$tabulatorItems = array();
				$bzm = 0;
				
				if(!$localsession["adminFormTabulator"][$form["form_id"]][$key]){$stl = "font-weight:bold;";}else{$stl = "";}
				$printParams_ = array("VALUE" => "all elements","HEIGHT" => 20);
				printFormularElement(10000,"categorieItem",$printParams_,0,"OnClick=\"switchTabulator(this);setTabulator('$key','0')\"",$stl);
				$bzm++;
				
				foreach ($gfield[$referenz_tab]["sort"] as $kkey => $kval){
					if($gfield[$referenz_tab]["field_type"][$kkey] == 100){
						if($localsession["adminFormTabulator"][$form["form_id"]][$key] == $kkey){
							$stl = "font-weight:bold;";
							$tabulatorItems[$kkey] = 1;
						}else{
							$stl = "";
							$tabulatorItems[$kkey] = 0;
						}
						$printParams_ = array("VALUE" => $gfield[$referenz_tab]["spelling"][$kkey],"HEIGHT" => 20);
						printFormularElement($kkey+10000,"categorieItem",$printParams_,0,"OnClick=\"switchTabulator(this);setTabulator('$key','$kkey')\"",$stl);
						$bzm++;
					}
				}
				echo "</div>";
			}

			# all elements without categorie visible in any categorie
			echo "<div style=\"position:absolute;\" id=\"formCatItem10000\">";
			formElementList($key,0);
			echo "</div>";

			# all tabulator items
			if($gfield[$referenz_tab]["sort"] AND in_array(100,$gfield[$referenz_tab]["field_type"])){
				foreach ($tabulatorItems as $tfkey => $tfval){
					if($tfval){$displ = "";}else{$displ = "none";}
					echo "<div id=\"formCatItem".($tfkey+10000)."\" style=\"position:absolute;display:$displ\">";
					formElementList($key,$tfkey);
					echo "</div>";
				}
			}

			echo "\n</div>\n";

		}elseif($form["typ"][$key] == "tabulator"){
			$printParams["MAINELEMENT"] = 1;
			printFormularElement($key,$form["typ"][$key],$printParams,1);
			
			# all tabulators
			$bzm = 0;
			echo "<div style=\"height:22px;background-color:".$farbschema['WEB1']."\">";
			$tabulatorItems = array();
			if($localsession["adminFormTabulator"][$form["form_id"]][$key]){$displayed=1;}else{$displayed=0;}
			foreach ($form["id"] as $sfkey => $sfvalue){
				if($form["subel"][$sfkey] != $key OR $form["typ"][$sfkey] != "tabuItem"){continue;}
				if($localsession["adminFormTabulator"][$form["form_id"]][$key] == $sfkey OR !$displayed){$stl = "font-weight:bold";$displayed=$sfkey;}else{$stl = "";}
				$printParams_ = formElementParams($form,$sfkey);
				printFormularElement($sfkey,"tabuItem",$printParams_,0,"OnClick=\"limbasSetTabulator($value);switchTabulator(this);setTabulator('$key','$sfkey')\";",$stl);
				$tabulatorItems[] = $sfkey;
				$bzm++;
			}
			echo "</div>";
			# all tabulator items
			$displ = "none";
			foreach ($tabulatorItems as $tfkey => $tfval){
				if($displayed == $tfval){$displ = "";$localsession["adminFormTabulator"][$form["form_id"]][$key] = $tfval;}else{$displ = "none";}
				echo "<div id=\"formCatItem".$tfval."\" style=\"position:absolute;display:$displ\">";
				formElementList($key,$tfval);
				echo "</div>";
			}

			echo "\n</div>\n";
		}elseif($form["typ"][$key] == "frame"){
			$printParams["MAINELEMENT"] = 1;
			printFormularElement($key,$form["typ"][$key],$printParams,1);
			#echo "<div id=\"formCatItem".$tfval."\" style=\"position:absolute;display:$displ\">";
			echo "<input type=\"hidden\" name=\"fvalue$key\" id=\"fvalue$key\" value=\"".$form["value"][$key]." \">";
			formElementList($key);
			echo "\n</div>\n";
		}else{
			printFormularElement($key,$form["typ"][$key],$printParams);
		}
	}
	}
	
	if($printParams){return true;}
	
}















?>
<div id="innerframe" style="position:absolute; width:100%; height:100%; z-index:1">
<?php

formElementList();
?>

</div>


</FORM>
<BR><BR><BR>




<?php
if($GLOBALS['printedtabs']){
	krsort($GLOBALS['printedtabs']); # jquery - Sortierung von innen nach außen
	echo "
	<script type=\"text/javascript\">
	$(function(){
	";
	foreach ($GLOBALS['printedtabs'] as $key => $value){
		echo "
			$(\"#div".$value."\").resizable({
				handles: \"e, s\" 
			});
			
			$(\"#tab_".$value."\").colResizable({
				liveDrag:true, 
				draggingClass:\"rangeDrag\", 
				gripInnerHtml:\"<div class='rangeGrip'></div>\", 
				draggingClass:\"dragging\"
			});
		";
	}
	echo "
	});
	</script>";
}



?>
<script language="JavaScript">

$('#innerframe').selectable({
	filter:'[lmbselectable="1"], [lmbtype="bild"], [lmbtype="line"], [lmbtype="ellipse"], [lmbtype="scroll"], [lmbtype="reminder"], [lmbtype="wflhist"]',
	stop: function( event, ui ) {lmb_multiMenu(event);}
});

</script>
