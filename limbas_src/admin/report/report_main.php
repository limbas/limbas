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
 * ID: 168
 */
?>

<STYLE>

BODY{
    background-color: white;
}
INPUT {
    BACKGROUND-COLOR: #EEEEEE;
    BORDER:none;
}
TEXTAREA {
	BACKGROUND-COLOR: #EEEEEE;
    BORDER:none;
}

.activecellborder {
	border: 2px dashed red !important;
}
.defaultcellborder {
	border: 1px dotted grey;
}

.CodeMirror {
    border: 1px solid <?=$farbschema['WEB3']?>;
    width: 300px;
    height: 200px;
}
</STYLE>

<script src="extern/codemirror/lib/codemirror.js?v=<?=$umgvar["version"]?>"></script>
<link rel="stylesheet" href="extern/codemirror/lib/codemirror.css?v=<?=$umgvar["version"]?>">
<script src="extern/codemirror/edit/matchbrackets.js?v=<?=$umgvar["version"]?>"></script>
<script src="extern/codemirror/edit/matchtags.js?v=<?=$umgvar["version"]?>"></script>
<script src="extern/codemirror/mode/htmlmixed/htmlmixed.js?v=<?=$umgvar["version"]?>"></script>
<script src="extern/codemirror/mode/xml/xml.js?v=<?=$umgvar["version"]?>"></script>
<script src="extern/codemirror/mode/javascript/javascript.js?v=<?=$umgvar["version"]?>"></script>
<script src="extern/codemirror/mode/css/css.js?v=<?=$umgvar["version"]?>"></script>
<script src="extern/codemirror/mode/clike/clike.js?v=<?=$umgvar["version"]?>"></script>
<script src="extern/codemirror/mode/php/php.js?v=<?=$umgvar["version"]?>"></script>

<script language="JavaScript">

// ----- Js-Script-Variablen --------
jsvar["action"] = "<?=$action?>";
jsvar["ID"] = "<?=$ID?>";
jsvar["report_viewtab"] = "<?=$report_viewtab?>";
jsvar["report_id"] = "<?php if($report["id"]){echo implode(";",$report["id"]);}?>";
jsvar["WEB7"] = "<?=$farbschema['WEB7']?>";
jsvar["lng_1099"] = "<?=$lang[1099]?>";
jsvar["WEB10"] = "<?=$farbschema['WEB10']?>";
jsvar["defformat"] = "<?=$greportlist[$referenz_tab]["defformat"][$report_id]?>";

<?= $view_tab ?>
var zIndexTop = <?=$report["max_zindex"]?>;
var f_id = new Array();
var f_value = new Array();
var f_typ = new Array();
<?php
//----------------- Element-Schleife -------------------
if(!$report["id"]){$report["id"] = array();}
foreach ($report["id"] as $key => $value){
	if(lmb_strpos($report["value"][$key],";")){
		$content = explode(";",$report["value"][$key]);
		$content = $gfield[$content[0]]["field_name"][$content[1]]." (".$gtab["desc"][$content[0]].")";
	}else{
		$content = htmlentities($report["value"][$key],ENT_QUOTES,$GLOBALS["umgvar"]["charset"]);
	}
	echo "f_id[$value] = '".$report["id"][$key]."';\n";
	echo "f_zindex[$value] = '".$report["zindex"][$key]."';\n";
	echo "f_value[$value] = '".htmljs(lmb_substr($content,0,20));
	if(lmb_strlen($content) > 20){echo "..';\n";}else{echo "';\n";}
	echo "f_typ[$value] = '".$report["typ"][$key]."';\n";
}
?>


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



</script>

<?php /*----------------- Ramenwechsel-Grafik -------------------*/?>
<div ID="border_move" style="position:absolute;top:0px;left:-100px;cursor:se-resize;z-index:10003;" onMousedown="aktivate_resize(event);">
    <i class="lmb-icon lmb-resizer" BORDER="0"></i>
</div>

<DIV ID="menu" class="lmbContextMenu lmbContextMenuMove" style="visibility:hidden;top:<?=$menuposy?>;left:<?=$menuposx?>;z-index:10002;">
<FORM NAME="form_menu">
<TABLE BORDER="0" cellspacing="0" cellpadding="0">
<TR><TD><?php pop_movetop('menu');?></TD></TR>
<TR id="menu_multi_text_bild_chart_templ_datum_dbdat_dbdesc_rect_line_ellipse_tab_tabcell_snr_formel_ureport_" STYLE="display:none;"><TD><?php pop_input(0,'','input_info','','readonly');?></TD></TR>
<TR id="menu_multi_js_php_text_bild_chart_templ_tab_datum_dbdat_dbdesc_dbnew_dbsearch_rect_line_ellipse_submt_inptext_inphidden_inparea_inpselect_inpcheck_inpradio_menue_fmenue_scroll_usetime_uform_snr_formel_ureport_" STYLE="display:none"><TD><?php pop_input(0,$zl,'ZIndex','',0)?></TD></TR>
<TR id="menu_multi_js_php_text_bild_chart_templ_tab_tabcell_datum_dbdat_dbdesc_dbnew_dbsearch_rect_line_ellipse_submt_inptext_inphidden_inparea_inpselect_inpcheck_inpradio_menue_fmenue_scroll_usetime_uform_snr_formel_ureport_" STYLE="display:none"><TD><?php pop_line();?></TD></TR>
<TR id="menu_dbdat_" STYLE="display:none;"><TD><?php pop_input(0,'','input_infotable','','readonly');?></TD></TR>
<TR id="menu_dbdat_" STYLE="display:none;"><TD><?php pop_input(0,'','input_infofield','','readonly');?></TD></TR>
<TR id="menu_dbdat_" STYLE="display:none"><TD><?php pop_line();?></TD></TR>
<TR id="menu_multi_js_php_text_bild_chart_templ_tab_datum_dbdat_dbdesc_dbnew_dbsearch_rect_line_ellipse_submt_inptext_inphidden_inparea_inpselect_inpcheck_inpradio_menue_fmenue_scroll_usetime_uform_snr_formel_ureport_" STYLE="display:none;"><TD><?php pop_menu(0,'el_to_front(zIndexTop+1)',$lang[2064]);?></TD></TR>
<TR id="menu_multi_js_php_text_bild_chart_templ_tab_datum_dbdat_dbdesc_dbnew_dbsearch_rect_line_ellipse_submt_inptext_inphidden_inparea_inpselect_inpcheck_inpradio_menue_fmenue_scroll_usetime_uform_snr_formel_ureport_" STYLE="display:none;"><TD><?php pop_menu(0,'el_to_front(1)',$lang[2065]);?></TD></TR>
<TR id="menu_multi_js_php_text_bild_chart_templ_tab_datum_dbdat_dbdesc_dbnew_dbsearch_rect_line_ellipse_submt_inptext_inphidden_inparea_inpselect_inpcheck_inpradio_menue_fmenue_scroll_usetime_snr_formel_" class="menu_activateParentCell" STYLE="display:none;"><TD><?php pop_menu(0,'el_to_cell()','wechsel zu Tabellenzelle');?></TD></TR>
<TR id="menu_bild_" STYLE="display:none"><TD><?php pop_line();?></TD></TR>
<TR id="menu_bild_" STYLE="display:none"><TD><?php pop_submenu2($lang[1100],"limbasDivShow(this,'menu','pic_info')",$lang[1100]);?></TD></TR>
<TR id="menu_bild_" STYLE="display:none"><TD><?php pop_submenu2($lang[1102],"limbasDivShow(this,'menu','menu_picstyle');",$lang[1102]);?></TD></TR>
<TR id="menu_multi_text_bild_chart_templ_datum_dbdat_dbdesc_rect_line_ellipse_tab_snr_formel_" STYLE="display:none"><TD><?php pop_line();?></TD></TR>
<TR id="menu_text_bild_datum_dbdat_dbdesc_rect_line_ellipse_tab_snr_formel_" STYLE="display:none"><TD><?php pop_submenu2($lang[1464],"document.form1.report_copy.value='1';document.form1.submit();",$lang[1464]);?></TD></TR>
<TR id="menu_text_datum_dbdat_dbdesc_tab_snr_formel_" STYLE="display:none"><TD><?php pop_line();?></TD></TR>
<?php
$opt["val"] = $sysfont;
$opt["desc"] = $sysfont;
?>
<TR id="menu_text_chart_templ_datum_dbdat_dbdesc_snr_formel_" STYLE="display:none"><TD><?php pop_select("fill_style('0','fontFamily',this.value);",$opt,"",1,"input_fontface","Font",60);?></TD></TR>
<?php $opt['val'] = array("5px","6px","7px","8px","9px","10px","11px","12px","13px","14px","15px","16px","17px","18px","19px","20px","21px","22px","23px","24px","25px","26px","27px","28px","29px","30px","35px","40px","45px","50px","55px","60px","70px","80px","90px");
$opt['desc'] = array("5","6","7","8","9","10","11","12","13","14","15","16","17","18","19","20","21","22","23","24","25","26","27","28","29","30","35","40","45","50","55","60","70","80","90");?>
<TR id="menu_multi_text_chart_templ_datum_dbdat_dbdesc_snr_formel_" STYLE="display:none"><TD><?php pop_select("fill_style('3','fontSize',this.value);",$opt,"",1,"input_fontsize",$lang[210],60);?></TD></TR>
<TR id="menu_multi_text_chart_datum_dbdat_dbdesc_snr_formel_" STYLE="display:none"><TD><?php pop_submenu2($lang[1102],"limbasDivShow(this,'menu','menu_fontstyle');",$lang[1102]);?></TD></TR>
<TR id="menu_multi_text_datum_dbdat_dbdesc_snr_formel_" STYLE="display:none"><TD><?php pop_submenu2($lang[1104],"limbasDivShow(this,'menu','menu_color');submenu_style('9;color');",$lang[1104]);?></TD></TR>
<TR id="menu_multi_text_bild_chart_templ_tab_datum_dbdat_dbdesc_rect_line_ellipse_snr_formel_tabcell_" STYLE="display:none"><TD><?php pop_submenu2($lang[1107],"limbasDivShow(this,'menu','menu_color');submenu_style('21;backgroundColor');",$lang[1107]);?></TD></TR>
<TR id="menu_multi_text_bild_chart_templ_datum_dbdat_dbdesc_rect_line_ellipse_snr_formel_tabcell_" STYLE="display:none"><TD><?php pop_line();?></TD></TR>
<TR id="menu_multi_text_bild_chart_templ_datum_dbdat_dbdesc_rect_line_ellipse_tab_snr_formel_tabcell_" STYLE="display:none"><TD><?php pop_submenu2($lang[1541],"limbasDivShow(this,'menu','menu_color');submenu_style('15;borderColor');",$lang[1541]);?></TD></TR>
<?php $opt['val'] = array("none","solid");
$opt['desc'] = array($lang[1246],$lang[1534]);?>
<TR id="menu_multi_text_bild_chart_templ_datum_dbdat_dbdesc_rect_line_ellipse_tab_tabcell__snr_formel_" STYLE="display:none"><TD><?php pop_select("fill_style('14','borderStyle',this.value);",$opt,"",1,"input_borderstyle",$lang[1540],60);?></TD></TR>
<?php $opt['val'] = array("0px","0.1px","0.2px","0.5px","1px","2px","3px","4px","5px","6px","7px","8px","9px","10px");
$opt['desc'] = array("0","0.1","0.2","0.5","1","2","3","4","5","6","7","8","9","10");?>
<TR id="menu_multi_text_bild_chart_templ_datum_dbdat_dbdesc_rect_line_ellipse_tab_tabcell_snr_formel_" STYLE="display:none"><TD><?php pop_select("fill_style('16','borderWidth',this.value);",$opt,"",1,"input_borderwidth",$lang[1105],60);?></TD></TR>
<?php $opt['val'] = array("","1px","2px","3px","4px","5px","6px","7px","8px","9px","10px");
$opt['desc'] = array("","1","2","3","4","5","6","7","8","9","10");?>
<TR id="menu_multi_text_bild_chart_templ_datum_dbdat_dbdesc_tab_tabcell_snr_formel_" STYLE="display:none"><TD><?php pop_select("fill_style('22','padding',this.value);divclose();set_posxy();",$opt,"",1,"input_tabpadding",$lang[1111],60);?></TD></TR>
<TR id="menu_multi_text_bild_chart_templ_datum_dbdat_dbdesc_rect_line_ellipse_tab_tabcell_snr_formel_" STYLE="display:none"><TD>
<?php pop_left();?>
&nbsp;&nbsp;&nbsp;&nbsp;l<INPUT TYPE="checkbox" STYLE="border:none" NAME="borderLeft" ID="borderLeft" OnClick="fill_style('17','border','borderLeft');">&nbsp;r<INPUT TYPE="checkbox" STYLE="border:none" NAME="borderRight" ID="borderRight" OnClick="fill_style('18','border','borderRight');">&nbsp;o<INPUT TYPE="checkbox" STYLE="border:none" NAME="borderTop" ID="borderTop" OnClick="fill_style('19','border','borderTop');">&nbsp;u<INPUT TYPE="checkbox" STYLE="border:none" NAME="borderBottom" ID="borderBottom" OnClick="fill_style('20','border','borderBottom');">
<?php pop_right();?>
</TD></TR>
<TR id="menu_tab_" STYLE="display:none"><TD><?php pop_line();?></TD></TR>
<?php $opt['val'] = array("","1");
$opt['desc'] = array("",$lang[2736]);?>
<TR id="menu_tab_" STYLE="display:none"><TD><?php pop_select("fill_style('44','cellstyle',this.value);divclose();set_posxy();",$opt,"",1,"input_cellstyle",$lang[2735],60);?></TD></TR>
<TR id="menu_multi_text_bild_chart_datum_dbdat_dbdesc_rect_line_ellipse_tab_snr_formel_tabcell_ureport_" STYLE="display:none"><TD><?php pop_line();?></TD></TR>
<TR id="menu_line_ellipse_" STYLE="display:none"><TD>
<?php pop_left();?>
<TABLE cellpadding="0" cellspacing="0"><TR><TD STYLE="width:120px;">&nbsp;<SPAN><?=$lang[1108]?></SPAN></TD><TD><INPUT TYPE="checkbox" STYLE="border:none" ID="input_line_reverse_" NAME="input_line_reverse" OnClick="fill_style('25','',this.checked);"></TD></TR></TABLE>
<?php pop_right();?>
</TD></TR>
<?php $opt['val'] = array("","1","2","3","4","5","6","7","8","9","10","11","12","13","14","15","16","17","18","19","20","21","22","23","24","25","26","27","28","29","30","31","32","33","34","35","36","37","38","39","40","41","42","43","44","45","46","47","48","49","50");
$opt['desc'] = array("","1","2","3","4","5","6","7","8","9","10","11","12","13","14","15","16","17","18","19","20","21","22","23","24","25","26","27","28","29","30","31","32","33","34","35","36","37","38","39","40","41","42","43","44","45","46","47","48","49","50");?>
<TR id="menu_tab_" STYLE="display:none"><TD><?php pop_select("divclose();set_posxy();document.form1.report_tab_cols.value=this.value;document.form1.submit();",$opt,"",1,"input_tabcols",$lang[1109],60);?></TD></TR>
<TR id="menu_tab_" STYLE="display:none"><TD><?php pop_select("divclose();set_posxy();document.form1.report_tab_rows.value=this.value;document.form1.submit();",$opt,"",1,"input_tabrows",$lang[88],60);?></TD></TR>
<?php $opt['val'] = array("","0.05","0.1","0.15","0.2","0.25","0.3","0.35","0.4","0.45","0.5","0.55","0.6","0.65","0.7","0.75","0.8","0.85","0.9","0.95","1");
$opt['desc'] = array("","5%","10%","15%","20%","25%","30%","35%","40%","45%","50%","55%","60%","65%","70%","75%","80%","85%","90%","95%","100%");?>
<TR id="menu_multi_text_bild_chart_templ_datum_dbdat_dbdesc_rect_line_ellipse_tab_snr_formel_tabcell_" STYLE="display:none"><TD><?php pop_select("fill_style('24','opacity',this.value);",$opt,"",1,"input_opacity",$lang[2093],60);?></TD></TR>
<?php $opt['val'] = array(0,1,2,3,4,5);
$opt['desc'] = array("",$lang[2445],$lang[2091],$lang[2092],$lang[2446],$lang[2447]);?>
<TR id="menu_multi_text_bild_templ_datum_dbdat_dbdesc_rect_line_ellipse_snr_formel_onlynotab_" STYLE="display:none"><TD><?php pop_select("fill_style('32','bg',this.value);",$opt,"",1,"input_bg",$lang[2090],60);?></TD></TR>
<?php $opt['val'] = array("0","1","2");
$opt['desc'] = array("",$lang[2098],$lang[2099]);?>
<TR id="menu_text_bild_chart_templ_datum_dbdat_dbdesc_rect_line_ellipse_tab_snr_formel_onlynotab_" STYLE="display:none"><TD><?php pop_select("fill_style('34','pagebreak',this.value);",$opt,"",1,"input_pagebreak",$lang[2100],60);?></TD></TR>
<?php $opt['val'] = array_merge(array(0),$report["id"]);
$opt['desc'] = array_merge(array(''),$report["id"]);?>
<TR id="menu_text_bild_chart_templ_datum_dbdat_dbdesc_rect_line_ellipse_tab_snr_formel_onlynotab_ureport_" STYLE="display:none"><TD><?php pop_select("fill_style('36','relativepos',this.value);",$opt,"",1,"input_relativepos",$lang[2102],60);?></TD></TR>
<?php $opt['val'] = array_merge(array(0),$report["id"]);
$opt['desc'] = array_merge(array(''),$report["id"]);?>
<TR id="menu_text_bild_chart_templ_datum_dbdat_dbdesc_rect_line_ellipse_snr_formel_onlynotab_" STYLE="display:none"><TD><?php pop_select("fill_style('39','relativedisplay',this.value);",$opt,"",1,"input_relativedisplay",$lang[2198],60);?></TD></TR>
<?php $opt['val'] = array("0","1","2","3","4","5","6","7","8","9","10","11","12","13","14","15","16","17","18","19","20","21","22","23","24","25","26","27","28","29","30");
$opt['desc'] = array("","1","2","3","4","5","6","7","8","9","10","11","12","13","14","15","16","17","18","19","20","21","22","23","24","25","26","27","28","29","30");?>
<TR id="menu_tabcell_" STYLE="display:none"><TD><?php pop_select("fill_style('37','colspan',this.value);",$opt,"",1,"input_colspan",$lang[2178],60);?></TD></TR>
<?php $opt['val'] = array(0,1,2,3,4,5);
$opt['desc'] = array("",$lang[2445],$lang[2091],$lang[2092],$lang[2446],$lang[2447]);?>
<TR id="menu_multi_text_bild_chart_templ_datum_dbdat_dbdesc_rect_line_ellipse_tab_snr_formel_" STYLE="display:none"><TD><?php pop_select("fill_style('29','',this.value);",$opt,"",1,"input_hide",$lang[2088],60);?></TD></TR>
<TR id="menu_text_bild_chart_templ_datum_dbdat_dbdesc_rect_line_ellipse_tab_snr_formel_" STYLE="display:none"><TD><?php pop_input2("fill_style('43','rotate',parseInt(this.value));","input_rotate",null,null,$lang[2698],95);?></TD></TR>
<TR id="menu_dbdat_tabcell_" STYLE="display:none"><TD><?php pop_input2("fill_style('40','tr_seperator',this.value);","input_seperator","",0,$lang[2357],95);?></TD></TR>
<TR id="menu_dbdat_dbdesc_datum_text_formel_onlytab_" STYLE="display:none"><TD><?php pop_input2("set_tabcell_width(this.value)","input_tr_width","",null,$lang[2861],95);?></TD></TR>
<?php
#NAME="replace_element" OnClick="if(this.checked){document.form1.report_replace_element.value=document.form1.report_edit_id.value;}else{document.form1.report_replace_element.value=''}"

#$opt['val'] = array("","1","2","3","4","5","6","7","8","9","10","11","12","13","14","15","16","17","18","19","20","21","22","23","24","25","26","27","28","29","30");
#$opt['desc'] = array("","1","2","3","4","5","6","7","8","9","10","11","12","13","14","15","16","17","18","19","20","21","22","23","24","25","26","27","28","29","30");
#echo "<TR id=\"menu_tabel\" STYLE=\"display:none\"><TD>";
#pop_select("fill_style('38','rowspan',this.value);",$opt,"",1,"input_rowspan",$lang[2179],60);
#echo "</TD></TR>";
?>
<TR id="menu_text_templ_bild_datum_dbdat_dbdesc_rect_line_ellipse_snr_formel_" STYLE="display:none"><TD><?php pop_line();?></TD></TR>

<TR id="menu_dbdat_dbdesc_formel_" STYLE="display:none"><TD>
<?php pop_left();?>
<TABLE cellpadding="0" cellspacing="0"><TR><TD STYLE="width:120px;">&nbsp;<SPAN><?=$lang[1114]?></SPAN></TD><TD><INPUT TYPE="checkbox" STYLE="border:none" ID="input_list" NAME="input_list" OnClick="fill_style('33','',this.checked);"></TD></TR></TABLE>
<?php pop_right();?>
</TD></TR>
<TR id="menu_dbdat_bild_onlynotab_" STYLE="display:none"><TD>
<?php pop_left();?>
<TABLE cellpadding="0" cellspacing="0"><TR><TD STYLE="width:120px;">&nbsp;<SPAN><?=$lang[1463]?></SPAN></TD><TD><INPUT TYPE="checkbox" STYLE="border:none" ID="input_prop" NAME="input_prop" OnClick="fill_style('26','',this.checked);"></TD></TR></TABLE>
<?php pop_right();?>
</TD></TR>
<TR id="menu_text_bild_templ_datum_dbdat_dbdesc_rect_line_ellipse_snr_formel_onlynotab_" STYLE="display:none"><TD>
<?php pop_left();?>
<TABLE cellpadding="0" cellspacing="0"><TR><TD STYLE="width:120px;">&nbsp;<SPAN><?=$lang[1112]?></SPAN></TD><TD><INPUT TYPE="checkbox" STYLE="border:none" ID="input_head" NAME="input_head" OnClick="fill_style('27','',this.checked);"></TD></TR></TABLE>
<?php pop_right();?>
</TD></TR>
<TR id="menu_text_bild_templ_datum_dbdat_dbdesc_rect_line_ellipse_snr_formel_onlynotab_" STYLE="display:none"><TD>
<?php pop_left();?>
<TABLE cellpadding="0" cellspacing="0"><TR><TD STYLE="width:120px;">&nbsp;<SPAN><?=$lang[1113]?></SPAN></TD><TD><INPUT TYPE="checkbox" STYLE="border:none" ID="input_foot" NAME="input_foot" OnClick="fill_style('28','',this.checked);"></TD></TR></TABLE>
<?php pop_right();?>
</TD></TR>
<TR id="menu_text_bild_datum_dbdat_dbdesc_rect_line_ellipse_snr_formel_onlynotab_" STYLE="display:none"><TD>
<?php pop_left();?>
<TABLE cellpadding="0" cellspacing="0"><TR><TD STYLE="width:120px;">&nbsp;<SPAN><?=$lang[2101]?></SPAN></TD><TD><INPUT TYPE="checkbox" STYLE="border:none" ID="input_breaklock" NAME="input_breaklock" OnClick="fill_style('35','',this.checked);"></TD></TR></TABLE>
<?php pop_right();?>
<TR id="menu_text_formel_onlynotab_" STYLE="display:none"><TD>
<?php pop_left();?>
<TABLE cellpadding="0" cellspacing="0"><TR><TD STYLE="width:120px;">&nbsp;<SPAN><?=$lang[2508]?></SPAN></TD><TD><INPUT TYPE="checkbox" STYLE="border:none" ID="input_html" NAME="input_html" OnClick="fill_style('42','',this.checked);"></TD></TR></TABLE>
<?php pop_right();?>
</TD></TR>


<TR id="menu_templ_dbdat_" STYLE="display:none"><TD><?php pop_line();?></TD></TR>
<TR id="menu_dbdat_" STYLE="display:none"><TD>
<?php pop_left();?>
<TABLE cellpadding="0" cellspacing="0"><TR><TD STYLE="width:120px;">&nbsp;<SPAN><?=$lang[2460]?></SPAN></TD><TD><INPUT TYPE="checkbox" STYLE="border:none" ID="replace_element" NAME="replace_element" OnClick="if(this.checked){document.form1.report_replace_element.value=document.form1.report_edit_id.value;}else{document.form1.report_replace_element.value=''}"></TD></TR></TABLE>
<?php pop_right();?>
</TD></TR>


<TR id="menu_text_dbdat_formel_onlytab_" STYLE="display:none"><TD>
<?php pop_left();?>
<TABLE cellpadding="0" cellspacing="0"><TR><TD STYLE="width:120px;">&nbsp;<SPAN>tagmode</SPAN></TD><TD><INPUT TYPE="checkbox" STYLE="border:none" ID="input_tagmode" NAME="input_tagmode" OnClick="fill_style('41','',this.checked);"></TD></TR></TABLE>
<?php pop_right();?>
</TD></TR>
<TR id="menu_dbdat_bild_chart_" STYLE="display:none"><TD>
</TD></TR>
<TR id="menu_formel_" STYLE="display:none"><TD><?php pop_line();?></TD></TR>
<TR id="menu_formel_templ_" STYLE="display:none"><TD><?php pop_submenu2("Edit","cmeditor.setValue(document.getElementById(currentdiv).value);limbasDivShow(this,'menu','menu_big_input');","Edit");?></TD></TR>
<TR id="menu_dbdat_ureport_chart_" STYLE="display:none"><TD><?php pop_line();?></TD></TR>
<TR id="menu_dbdat_ureport_chart_templ_" STYLE="display:none"><TD><?php pop_submenu2("Parameter","document.getElementById('extendet_input').value=document.getElementById('extendet_input_' + currentdiv).value;limbasDivShow(this,'menu','menu_extendet_input');","Parameter");?></TD></TR>
<TR id="menu_multi_text_bild_chart_templ_datum_dbdat_dbdesc_rect_line_ellipse_tab_snr_formel_ureport_tabcell_" STYLE="display:none"><TD><?php pop_line();?></TD></TR>
<TR id="menu_multi_text_bild_chart_templ_datum_dbdat_dbdesc_rect_line_ellipse_tab_snr_formel_ureport_tabcell_" STYLE="display:none"><TD><?php pop_submenu2($lang[160],"lmb_dropEl('".$lang[1099]." ".$lang[160]."');",$lang[160]);?></TD></TR>
<TR><TD><?php pop_bottom();?></TD></TR>
</TABLE></FORM></DIV>




<?php /*----------------- Zeichensatz-Style DIV -------------------*/?>
<DIV ID="menu_fontstyle" class="lmbContextMenu" style="visibility:hidden;position:absolute;top:<?=$menuposy?>;left:<?=$menuposx?>;z-index:10001;" onclick="activ_menu=1"><FORM NAME="fstyle_form"><TABLE BORDER="0" cellspacing="0" cellpadding="0">
<TR><TD><?php pop_top('menu_fontstyle');?></TD></TR>

<?php $opt['val'] = array("normal","italic");
$opt['desc'] = array($lang[1123],$lang[1124]);?>
<TR><TD><?php pop_select("fill_style('1','fontStyle',this.value)",$opt,"",1,"input_fontstyle",$lang[1115],0);?></TD></TR>

<?php $opt['val'] = array("normal","bold");
$opt['desc'] = array($lang[1123],$lang[1125]);?>
<TR><TD><?php pop_select("fill_style('4','fontWeight',this.value)",$opt,"",1,"input_fontweight",$lang[1116],0);?></TD></TR>

<?php $opt['val'] = array("none","underline");
$opt['desc'] = array($lang[1123],$lang[1126]);?>
<TR><TD><?php pop_select("fill_style('7','textDecoration',this.value)",$opt,"",1,"input_fontdeco",$lang[1117],0);?></TD></TR>

<?php $opt['val'] = array("none","uppercase","lowercase");
$opt['desc'] = array($lang[1123],$lang[1127],$lang[1128]);?>
<TR><TD><?php pop_select("fill_style('8','textTransform',this.value)",$opt,"",1,"input_fonttransf",$lang[1118],0);?></TD></TR>

<?php $opt['val'] = array("justify","left","center","right");
$opt['desc'] = array($lang[1129],$lang[1130],$lang[1131],$lang[1132]);?>
<TR><TD><?php pop_select("fill_style('12','textAlign',this.value)",$opt,"",1,"input_fontalign",$lang[1119],0);?></TD></TR>

<?php $opt['val'] = array("top","middle","bottom","baseline","sub","super","text-top","text-bottom");
$opt['desc'] = array($lang[1490],$lang[1491],$lang[1492],$lang[1493],$lang[1494],$lang[1495],$lang[1496],$lang[1497]);?>
<TR><TD><?php pop_select("fill_style('23','verticalAlign',this.value)",$opt,"",1,"input_fontvalign",$lang[1119],0);?></TD></TR>

<?php $opt['val'] = array("","-3px","-2px","-1px","0px","1px","2px","3px","4px","5px","6px","7px","8px","9px","10px");
$opt['desc'] = array("","-3","-2","-1","0","1","2","3","4","5","6","7","8","9","10");?>
<TR><TD><?php pop_select("fill_style('11','lineHeight',parseInt(this.value));",$opt,"",1,"input_lineheight",$lang[1120],95);?></TD></TR>

<?php $opt['val'] = array("","1px","2px","3px","4px","5px","6px","7px","8px","9px","10px");
$opt['desc'] = array("","1","2","3","4","5","6","7","8","9","10");?>
<TR><TD><?php pop_select("fill_style('6','letterSpacing',parseInt(this.value));",$opt,"",1,"input_letterspacing",$lang[1121],95);?></TD></TR>

<?php /* $opt['val'] = array("","1px","2px","3px","4px","5px","6px","7px","8px","9px","10px");
$opt['desc'] = array("","1","2","3","4","5","6","7","8","9","10");?>
<TR><TD><?php pop_select("fill_style('5','wordSpacing',parseInt(this.value));",$opt,"",1,"input_wordspacing",$lang[1122],95);?></TD></TR>
*/?>

<TR><TD><?php pop_bottom();?></TD></TR>
</TABLE></FORM></DIV>

<?php /*----------------- Zeichensatz-Farbe DIV -------------------*/?>
<DIV ID="menu_color" class="lmbContextMenu" style="position:absolute;visibility:hidden;z-index:10001;" onclick="activ_menu=1"><FORM NAME="fcolor_form"><TABLE BORDER="0" cellspacing="0" cellpadding="0">
<TR><TD ><?php pop_top('menu_color');?></TD></TR>
<TR><TD>
<?php pop_color(null, null, 'menu_color');?>
</TD></TR>
<TR><TD><?php pop_bottom();?></TD></TR>
</TABLE></FORM></DIV>

<?php /*----------------- Bild-Style DIV -------------------*/?>
<DIV ID="menu_picstyle" class="lmbContextMenu" style="visibility:hidden;position:absolute;top:<?=$menuposy?>;left:<?=$menuposx?>;z-index:10001;" onclick="activ_menu=1"><FORM NAME="pstyle_form"><TABLE BORDER="0" cellspacing="0" cellpadding="0">
<TR><TD ><?php pop_top('menu_picstyle');?></TD></TR>
<TR><TD><?php pop_submenu2($lang[1134],"limbasDivShow(this,'menu_picstyle','menu_pichistory');",$lang[1134]);?></TD></TR>
<TR><TD><?php pop_submenu2($lang[1135],"divclose();set_posxy();document.form1.report_pic_style.value='reset';document.form1.submit();",$lang[1135]);?></TD></TR>
<TR><TD><?php pop_submenu2($lang[1136],"divclose();set_posxy();document.form1.report_pic_style.value='renew';document.form1.submit();",$lang[1136]);?></TD></TR>
<TR><TD><?php pop_line();?></TD></TR>
<TR><TD><?php pop_input2("divclose();set_posxy();document.form1.report_pic_style.value='-gamma:'+this.value;document.form1.submit();","pst_gamma","","","Gamma",90);?></TD></TR>
<TR><TD><?php pop_input2("divclose();set_posxy();document.form1.report_pic_style.value='-blur:'+this.value;document.form1.submit();","pst_blur","","","Blur",90);?></TD></TR>
<TR><TD><?php pop_input2("divclose();set_posxy();document.form1.report_pic_style.value='-sharpen:'+this.value;document.form1.submit();","pst_sharpen","","","Sharpen",90);?></TD></TR>
<TR><TD><?php pop_input2("divclose();set_posxy();document.form1.report_pic_style.value='-emboss:'+this.value;document.form1.submit();","pst_emboss","","","Emboss",90);?></TD></TR>
<TR><TD><?php pop_input2("divclose();set_posxy();document.form1.report_pic_style.value='-edge:'+this.value;document.form1.submit();","pst_edge","","","Edge",90);?></TD></TR>
<TR><TD><?php pop_input2("divclose();set_posxy();document.form1.report_pic_style.value='-fuzz:'+this.value+'%';document.form1.submit();","pst_fuzz","","","Fuzz",90);?></TD></TR>
<TR><TD><?php pop_input2("divclose();set_posxy();document.form1.report_pic_style.value='-implode:'+this.value;document.form1.submit();","pst_implode","","","Implode",90);?></TD></TR>
<TR><TD><?php pop_input2("divclose();set_posxy();document.form1.report_pic_style.value='-median:'+this.value;document.form1.submit();","pst_median","","","Median",90);?></TD></TR>
<TR><TD><?php pop_input2("divclose();set_posxy();document.form1.report_pic_style.value='-paint:'+this.value;document.form1.submit();","pst_paint","","","Paint",90);?></TD></TR>
<TR><TD><?php pop_input2("divclose();set_posxy();document.form1.report_pic_style.value='-noise:'+this.value+' Gaussian';document.form1.submit();","pst_noise","","","Noise",90);?></TD></TR>
<TR><TD><?php pop_input2("divclose();set_posxy();document.form1.report_pic_style.value='-raise:'+this.value+'x'+this.value;document.form1.submit();","pst_raise","","","Raise",90);?></TD></TR>
<?php $opt['val'] = array("","+contrast","-contrast");
$opt['desc'] = array("","+","-");?>
<TR><TD><?php pop_select("divclose();set_posxy();document.form1.report_pic_style.value=this.value+':';document.form1.submit();",$opt,"",1,"pst_contrast","Contrast",90);?></TD></TR>
<?php $opt['val'] = array("","90","-90");
$opt['desc'] = array("","90","-90");?>
<TR><TD><?php pop_select("divclose();set_posxy();document.form1.report_pic_style.value='-rotate:'+this.value;document.form1.submit();",$opt,"",1,"","Rotate",90);?></TD></TR>
<?php $opt['val'] = array("","-flip","-flop");
$opt['desc'] = array("","Horizontal","Vertikal");?>
<TR><TD><?php pop_select("divclose();set_posxy();document.form1.report_pic_style.value=this.value+':';document.form1.submit();",$opt,"",1,"","Spiegeln",60);?></TD></TR>
<?php $opt['val'] = array("","-monochrome","-negate","-normalize");
$opt['desc'] = array("","Monochrom","Invertieren","Normalisieren");?>
<TR><TD><?php pop_select("divclose();set_posxy();document.form1.report_pic_style.value=this.value+':';document.form1.submit();",$opt,"",1,"","Filter",60);?></TD></TR>
<TR><TD><?php pop_bottom();?></TD></TR>
</TABLE></FORM></DIV>

<?php /*----------------- Bild-Style DIV -------------------*/?>
<DIV ID="menu_pichistory" class="lmbContextMenu" style="position:absolute;visibility:hidden;z-index:10001;" onclick="activ_menu=1"><FORM NAME="pichistory_form"><TABLE BORDER="0" cellspacing="0" cellpadding="0">
<TR><TD ><?php pop_top('menu_pichistory');?></TD></TR>
<TR><TD>
<?php pop_left();?>
&nbsp;<TEXTAREA ID="pichistory" NAME="pichistory" WRAP="physical" STYLE="width:140px;height:100px;background-color:<?= $farbschema['WEB8'] ?>;" READONLY></TEXTAREA>
<?php pop_right();?>
</TD></TR>
<TR><TD><?php pop_bottom();?></TD></TR>
</TABLE></FORM></DIV>

<?php /*----------------- PIC-INFO DIV -------------------*/?>
<DIV ID="pic_info" class="lmbContextMenu" style="position:absolute;visibility:hidden;z-index:10001;" onclick="activ_menu=1"><FORM NAME="picinfo_form"><TABLE BORDER="0" cellspacing="0" cellpadding="0">
<TR><TD ><?php pop_top('pic_info');?></TD></TR>
<TR><TD>
<?php pop_left();?>
&nbsp;<TEXTAREA READONLY NAME="picinfo_val" STYLE="width:140px;height:150px;background-color:<?= $farbschema['WEB8'] ?>;" READONLY></TEXTAREA>
<?php pop_right();?>
</TD></TR>
<TR><TD><?php pop_bottom();?></TD></TR>
</TABLE></FORM></DIV>

<?php /*----------------- Big Input formel DIV -------------------*/?>
<DIV ID="menu_big_input" class="lmbContextMenu" style="position:absolute;visibility:hidden;z-index:10001;" onclick="activ_menu=1"><FORM NAME="big_input_form"><TABLE BORDER="0" cellspacing="0" cellpadding="0">
<TR><TD ><?php pop_top('pic_info',305);?></TD></TR>
<TR><TD>
<?php pop_left(305);?>
&nbsp;<TEXTAREA onfocus="bigInputEdit=1;" onblur="bigInputEdit=0;" name="big_input" id="big_input"></TEXTAREA>
        <Script language="JavaScript">
            var cmeditor = CodeMirror.fromTextArea(document.getElementById("big_input"), {
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
                document.getElementById(currentdiv).value = cmeditor.getValue();
            });

            // update cm on textarea change
            $(function() {
                $('[lmbtype=formel],[lmbtype=templ]').on('input', function() {
                    cmeditor.setValue(document.getElementById(currentdiv).value);
                });
            });
        </Script>
<?php pop_right();?>
</TD></TR>
<TR><TD><?php pop_bottom(305);?></TD></TR>
</TABLE></FORM></DIV>

<?php /*----------------- extendet_input DIV -------------------*/?>
<DIV ID="menu_extendet_input" class="lmbContextMenu" style="position:absolute;visibility:hidden;z-index:10001;" onclick="activ_menu=1"><FORM NAME="extendet_input_form"><TABLE BORDER="0" cellspacing="0" cellpadding="0">
<TR><TD ><?php pop_top('pic_info',305);?></TD></TR>
<TR><TD>
<?php pop_left(305);?>
&nbsp;<TEXTAREA NAME="extendet_input" id="extendet_input" STYLE="width:300px;height:200px;background-color:<?= $farbschema['WEB8'] ?>;" onchange="document.getElementById('extendet_input_'+currentdiv).value=this.value"></TEXTAREA>
<?php pop_right();?>
</TD></TR>
<TR><TD><?php pop_bottom(305);?></TD></TR>
</TABLE></FORM></DIV>



<FORM ACTION="main_admin.php" METHOD="post" name="form1">
<input type="hidden" name="action" value="setup_report_main">
<input type="hidden" name="report_typ">
<input type="hidden" name="report_id" VALUE="<?= $report_id ?>">
<input type="hidden" name="report_name" VALUE="<?= $report_name ?>">
<input type="hidden" name="referenz_tab" VALUE="<?= $referenz_tab ?>">
<input type="hidden" name="default_font">
<input type="hidden" name="default_size">
<input type="hidden" name="new_text">

<input type="hidden" name="menuposx">
<input type="hidden" name="menuposy">

<input type="hidden" name="report_possize">
<input type="hidden" name="report_setstyle">
<input type="hidden" name="report_view_tab">

<input type="hidden" name="report_copy">
<input type="hidden" name="report_del">
<input type="hidden" name="report_edit_id">

<input type="hidden" name="report_tab">
<input type="hidden" name="report_tab_size">
<input type="hidden" name="report_tab_rows">
<input type="hidden" name="report_tab_cols">
<input type="hidden" name="report_tab_el">
<input type="hidden" name="report_settabstyle">
<input type="hidden" name="report_pic_style">
<input type="hidden" name="report_chart_id">
<input type="hidden" name="report_templ_id">
<input type="hidden" name="ureport_id">
<input type="hidden" name="ureport_type">
<input type="hidden" name="report_posxy_edit">
<input type="hidden" name="report_add">
<input type="hidden" name="report_add_tab">
<input type="hidden" name="report_add_field">
<input type="hidden" name="report_add_field_desc">
<input type="hidden" name="report_add_baum">
<input type="hidden" name="report_add_field_data_type">
<input type="hidden" name="set_new_zindex">
<input type="hidden" name="report_replace_element">

<input type="hidden" name="aktiv_id">
<input type="hidden" name="tabelement">

<?php
$report['page_style'][0] = round($report['page_style'][0] * 2.8346);
$report['page_style'][1] = round($report['page_style'][1] * 2.8346);
$report['page_style'][2] = round($report['page_style'][2] * 2.8346);
$report['page_style'][3] = round($report['page_style'][3] * 2.8346);
$report['page_style'][4] = round($report['page_style'][4] * 2.8346);
$report['page_style'][5] = round($report['page_style'][5] * 2.8346);
?>

<div id="ramen" style="position:absolute; left:20px; top:20px; width:<?= $report['page_style'][0] ?>px; height:<?= $report['page_style'][1] ?>px; border:3px groove; z-index:1">
<div id="innenramen" style="position:absolute; left:<?= $report['page_style'][4] ?>px; top:<?= $report['page_style'][2] ?>px; width:<?= ($report['page_style'][0] - $report['page_style'][4] - $report['page_style'][5]) ?>px; height:<?= ($report['page_style'][1] - $report['page_style'][2] - $report['page_style'][3]) ?>px; border:1px solid #BBBBBB;z-index:1">
<?php

/*------- Style ---------*/
function set_style_report($textstyle){
	global $styletyp;
	$textstyle = explode(";",$textstyle);
	$bzm1 = 0;
	while($styletyp[$bzm1]){
		if(($textstyle[$bzm1] OR $textstyle[$bzm1] == '0') AND $textstyle[$bzm1] != " "){
			$stylevalue .= $styletyp[$bzm1].$textstyle[$bzm1].";";
		}
		$bzm1++;
	}
	return $stylevalue;
}


/*----------------- Tabellen -------------------*/
function print_tab($report,$key){
	global $farbschema;
	global $report_id;
	global $form;
	global $db;

    $tab_el_type = $report['typ'][$key];
    $report_ID = $report["id"][$key];
    $report_width = $report['width'][$key];
    $report_height = $report['height'][$key];
    $report_posx = $report['posx'][$key];
    $report_posy = $report['posy'][$key];
    $report_zindex = $report['zindex'][$key];
    $report_style = $report['style'][$key];
    $report_value = $report['value'][$key];
    $report_extvalue = $report['extvalue'][$key];
    $report_picname = $report['tab_size'][$key];

    $report_data_type = $report['data_type'][$key];
    $report_verkn_baum = $report['verkn_baum'][$key];
    $report_dbfield = $report['dbfield'][$key];
    $report_pictype = $report['pic_type'][$key];
    $report_picstyle = $report['pic_style'][$key];
    $report_picsize = $report['pic_size'][$key];
    $report_picres = $report['pic_res'][$key];
    $report_picthumb = $report['pic_name'][$key];

    $report_tab = $report['tab'][$key];
	$report_tab_el = $report['tab_el'][$key];
	$report_tab_size = $report['tab_size'][$key];

	$parameter = $report['extvalue'][$key];
	$tab_size = explode(";",$report_tab_size);
	$report_tab_cells = $tab_size[0];
	$report_tab_rows = $tab_size[1];

	$reportstyle = explode(";",$report_style);
	$stylevalue = set_style_report($report_style);

	if(!$report_tab_el){$position = "position:absolute; left:".$report_posx."; top:".$report_posy;}
	if($reportstyle[24]){$height = "height:$report_height;";}
	$style = "style=\"$position; $height width:$report_width; z-index:".$report_zindex.";overflow:visible;$stylevalue\"";
	#$style = "style=\"overflow:hidden;position:absolute; left:".$report_posx."; top:".$report_posy."; z-index:".$report_zindex."; ".$stylevalue.$size."\"";
	$onMousedown = "OnMousedown=\"limbasMenuOpen(event,this,'".$report_ID."','$report_style','tab','$report_tab_rows','$report_tab_cells','');\"";
	$onMouseOver = "OnMouseOver=\"document.getElementById('tabheader_$report_ID').style.visibility='visible';\"";
	$onMouseOut = "OnMouseOut=\"document.getElementById('tabheader_$report_ID').style.visibility='hidden';\"";
	echo "<div id=\"div".$report_ID."\" $style>\n";

	if($report_tab_cells AND $report_tab_rows){

		echo "\n<table id=\"tab_".$report_ID."\" style=\"border-collapse:collapse;width:100%;\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" $onMouseOver $onMouseOut>\n";
		$tdsize = explode(";",$parameter);

		# table header
		echo "<tr id=\"tabheader_$report_ID\" style=\"visibility:hidden;background-color:".$farbschema['WEB7']."\">";
		$bzm3 = 0;
		while($bzm3 < $report_tab_cells){
			if(!$tdsize[$bzm3]){$tdsize[$bzm3] = '50px';}
			echo "<th id=\"tab_el_".$report_ID."_0_".$bzm3."\" style=\"width:".$tdsize[$bzm3].";height:15px;cursor:move;\" $onMousedown>&nbsp;</th>";
		$bzm3++;
		}
		echo "</tr>";

		$bzm2 = 1;
		while($bzm2 <= $report_tab_rows){
			echo "<tr id=\"tr_$bzm2\">\n";
			$bzm3 = 1;
			$colspan = "";
			while($bzm3 <= $report_tab_cells){
				if($colspan-- > 1){$bzm3++;continue;}
				// --- TD Element ---------------------------------------------
				if($tab_cell_id = $report["tab_cell"][$report_tab][$bzm3][$bzm2]) {

				    $st = $report["style"][$tab_cell_id];
				    $cellstyle = explode(";",$st);

                    // use table style
                    if($reportstyle[44]){
                        for($i=13;$i<=24;$i++) {
                            #if(!$cellstyle[$i] OR ($i >= 17 AND $i <=20) ) {
                                $cellstyle[$i] = $reportstyle[$i];
                            #}
                        }
                    }

                    // no border
                    if(!$cellstyle[14] OR $cellstyle[14]  == 'none' OR ($cellstyle[17] == 'none' AND $cellstyle[18] == 'none' AND $cellstyle[19] == 'none' AND $cellstyle[20] == 'none')){$cellstyle[14] = '';$cellstyle[15] = '';$cellstyle[16] = '';$cellstyle[17] = '';$cellstyle[18] = '';$cellstyle[20] = '';}

                    // inactive cell border
                    for ($i = 17; $i <= 20; $i++) {
                        if($cellstyle[$i] == 'none'){
                            $cellstyle[$i] = '1px dotted grey';
                        }
                    }

                    $st = implode(';',$cellstyle);




				    $onMousedown = "OnMousedown=\"limbasMenuOpen(event,this,'$tab_cell_id','".$report["style"][$tab_cell_id]."','tabcell','$report_tab_rows','$report_tab_cells','','','','$report_tab','$bzm2','$bzm3');\"";

					// colspan
					if($cellstyle[37]){$colspan = $cellstyle[37]; $CLSP = "colspan=\"".$cellstyle[37]."\"";}else{$CLSP = "";}

                    # Zentrierung geht nur ohne text-align
                    #$align = "align=\"" . $cellstyle[12] . "\"";
                    #$valign = "valign=\"" . $cellstyle[23] . "\"";

                    $style = "style=\"" . set_style_report($st) . ";\"";

					/*
                    if($tab_cell_id == 219){
                        error_log($style);
                    }
					*/

                    echo "<td id=\"div" . $tab_cell_id . "\" $CLSP class=\"defaultcellborder $class\" $style $valign $align $onMousedown>";

                    // --- Inhalt Zelle ---------------------------------------------
                    if(!reportElementList($report,array($report_tab,$bzm2,$bzm3))){
                        echo "<div style=\"width:100%;overflow:hidden;height:12;\"></div>";
                    }

                    echo "</td> ";
                }
				$bzm3++;
			}
			echo "\n</tr>\n";
			$bzm2++;
		}
		echo "</table>\n";
	}

	$GLOBALS['printedtabs'][] = $report_ID;

	echo "</div>\n";
}




function printBerichtElement($report,$key){
	global $farbschema;
	global $gfield;
	global $gtab;

    $tab_el_type = $report['typ'][$key];
    $report_ID = $report["id"][$key];
    $report_width = $report['width'][$key];
    $report_height = $report['height'][$key];
    $report_posx = $report['posx'][$key];
    $report_posy = $report['posy'][$key];
    $report_zindex = $report['zindex'][$key];
    $report_style = $report['style'][$key];
    $report_value = $report['value'][$key];
    $report_extvalue = $report['extvalue'][$key];
    $tab_el = $report['tab_el'][$key];
    $report_tab = $report['tab'][$key];
    $report_picname = $report['tab_size'][$key];
    $report_data_type = $report['data_type'][$key];
    $report_verkn_baum = $report['verkn_baum'][$key];
    $report_dbfield = $report['dbfield'][$key];
    $report_pictype = $report['pic_type'][$key];
    $report_picstyle = $report['pic_style'][$key];
    $report_picsize = $report['pic_size'][$key];
    $report_picres = $report['pic_res'][$key];
    $report_picthumb = $report['pic_name'][$key];


	$stylevalue = set_style_report($report_style);
	$st = explode(";",$report_style);

	if($tab_el_type=="text"){
		if($tab_el){
			$style = "style=\"overflow:hidden;background-color:transparent;height:15px;width:$report_width;height:$report_height;$stylevalue\"";
		}else{
			if($report_width AND $report_height){$size = ";width:".$report_width."; height:".$report_height;}
			$style = "style=\"overflow:visible;position:absolute;left:".$report_posx."; top:".$report_posy."; z-index:".$report_zindex."; ".$stylevalue.$size."\"";
			#$onMousedown = "OnMousedown=\"aktivate('div".$report_ID."');\"";
		}
		$tagType = "textarea";
		$textAreaIdTyp = "div";
		$content = htmlentities($report_value,ENT_QUOTES,$GLOBALS["umgvar"]["charset"]);
	}elseif($tab_el_type=="ureport") {
        if ($tab_el) {
            $style = "style=\"overflow:hidden;background-color:transparent;height:15px;width:$report_width;" . $stylevalue . "\"";
        } else {
            if ($report_width AND $report_height) {
                $size = ";width:" . $report_width . "; height:" . $report_height;
            }
            $style = "style=\"overflow:visible;position:absolute;left:" . $report_posx . "; top:" . $report_posy . "; z-index:" . $report_zindex . "; " . $stylevalue . $size . "\"";
            #$onMousedown = "OnMousedown=\"aktivate('div".$report_ID."');\"";
        }
        $tagType = "textarea";
        $textAreaIdTyp = "div";
        $content = htmlentities($report_value, ENT_QUOTES, $GLOBALS["umgvar"]["charset"]);
        echo "<input type=\"hidden\" name=\"extendet_input_div" . $report_ID . "\" id=\"extendet_input_div" . $report_ID . "\" value=\"" . htmlentities($report_extvalue, ENT_QUOTES, $GLOBALS["umgvar"]["charset"]) . "\">";
    }elseif($tab_el_type== "rect"){
		if(!$tab_el){
			$tagType = "textarea";
			if($report_width AND $report_height){$size = ";width:".$report_width."; height:".$report_height;}
			$style = "style=\"overflow:hidden;position:absolute; left:".$report_posx."; top:".$report_posy."; z-index:".$report_zindex."; ".$stylevalue.$size."\"";
			$textAreaIdTyp = "div";
			#$onMousedown = "OnMousedown=\"aktivate('div".$report_ID."');\"";
			$textAreaName = "";
			$readonly = " READONLY ";
			$content = "&nbsp;";
		}
	}elseif($tab_el_type=="datum"){
		if(!$tab_el){
			if($report_width AND $report_height){$size = ";width:".$report_width."; height:$report_height;height:$report_height";}
			$style = "style=\"overflow:hidden;position:absolute; left:".$report_posx."; top:".$report_posy."; z-index:".$report_zindex."; ".$stylevalue.$size."\"";
			#$OnClick = "OnClick=\"menu_open(event,'div".$report_ID."','".$report_ID."','datum','".$report_style."');\"";
			#$onMousedown = "OnMousedown=\"aktivate('div".$report_ID."');\"";
			$tagType = "textarea";
			$readonly = " READONLY ";
			$content = "Datum";
		}else{
			$tagType = "span";
			$style = "style=\"overflow:visible;background-color:transparent;".$stylevalue."\"";
			$content = "Datum";
		}
	}elseif ($tab_el_type=="line" || $tab_el_type=="ellipse"){
		if(!$tab_el){
			if($report_width AND $report_height){$size = ";width:".$report_width."; height:$report_height;height:$report_height";}
			$style = "style=\"overflow:visible;position:absolute; left:".$report_posx."; top:".$report_posy."; z-index:".$report_zindex."; ".$stylevalue.$size."\"";
			#$onMousedown = "OnMousedown=\"aktivate('div".$report_ID."');\"";
			$tagType = "div";
			if($tab_el_type=="ellipse")
				$content = "<script type=\"text/javascript\">var jg".$report_ID." = new jsGraphics(\"div".$report_ID."\");js_ellipse('jg".$report_ID."','$report_width','$report_height','$st[9]','$st[3]');</script>";
			elseif($tab_el_type=="line")
				$content = "<script type=\"text/javascript\">var jg".$report_ID." = new jsGraphics(\"div".$report_ID."\");js_line('jg".$report_ID."','$report_width','$report_height','$st[25]','$st[9]','$st[3]');</script>";
		}
	}elseif($tab_el_type=="chart"){
			if($tab_el){
			$style = "style=\"overflow:hidden;background-color:transparent;height:15px;width:$report_width;".$stylevalue."\"";
		}else{
			if($report_width AND $report_height){$size = ";width:".$report_width."; height:$report_height;height:$report_height";}
			$style = "style=\"overflow:visible;position:absolute;left:".$report_posx."; top:".$report_posy."; z-index:".$report_zindex."; ".$stylevalue.$size."\"";
		}
	    $content = "<li class=\"lmb-icon lmb-line-chart\"></li><i>".htmlentities($report_value,ENT_QUOTES,$GLOBALS["umgvar"]["charset"])."</i>";
	    $tagType = "div";
	    echo "<input type=\"hidden\" name=\"extendet_input_div".$report_ID."\" id=\"extendet_input_div".$report_ID."\" value=\"".htmlentities($report_extvalue,ENT_QUOTES,$GLOBALS["umgvar"]["charset"])."\">";
	}elseif($tab_el_type=="snr"){
		if(!$tab_el){
			if($report_width AND $report_height){$size = ";width:".$report_width."; height:".$report_height;}
			$style = "style=\"overflow:hidden;position:absolute; left:".$report_posx."; top:".$report_posy."; z-index:".$report_zindex."; ".$stylevalue.$size."\"";
			#$onMousedown = "OnMousedown=\"aktivate('div".$report_ID."');\"";
			$tagType = "textarea";

		}else{
			$style = "style=\"overflow:visible;background-color:transparent;".$stylevalue."\"";
			$tagType = "span";
		}
		$content = "Seiten-Nummer";
	}elseif($tab_el_type == "formel" OR $tab_el_type=="templ"){
		$tagType = "textarea";
		$textAreaIdTyp = "div";
		$content = htmlentities($report_value,ENT_QUOTES,$GLOBALS["umgvar"]["charset"]);

		if($tab_el){
			$style = "style=\"overflow:hidden;background-color:transparent;width:".$report_width.";height:15px;height:$report_height;$stylevalue\"";
		}else{
			if($report_width AND $report_height){$size = ";width:".$report_width."; height:".$report_height;}
			$style = "style=\"overflow:hidden;position:absolute; left:".$report_posx."; top:".$report_posy."; z-index:".$report_zindex."; ".$stylevalue.$size."\"";
		}
		echo "<input type=\"hidden\" name=\"extendet_input_div".$report_ID."\" id=\"extendet_input_div".$report_ID."\" value=\"".htmlentities($report_extvalue,ENT_QUOTES,$GLOBALS["umgvar"]["charset"])."\">";
	}elseif($tab_el_type=="bild"){

		/*------- Bild ---------*/
		# --> pic_type = (Bild-TYP)
		# --> pic_size = (Original Bildgrï¿½ï¿½e)
		# --> pic_style = (Bild-Style ImageMagick)
		# --> pic_res = (Auflösung)
		# --> pic_name = (thumb Bildname)
		# --> value = (Bildinfos)
		# --> tab_size = (org. Bildname)
		# --> DB_DATA_TYPE = (Bildkompression)

		$size1 = "width=\"".$report_width."\" height=\"".$report_height."\"";
		$size = ";width:".$report_width."; height:".$report_height;
		$path = "TEMP/thumpnails/report/".$report_picthumb;
		if($st[32]){$opacity = ";opacity:0.3";}

		if($tab_el){
			$style = "style=\"overflow:visible;background-color:transparent;width:$report_width;height:$report_height;$stylevalue\"";
		}else{
			$style = "style=\"position:absolute; left:".$report_posx."; top:".$report_posy."; z-index:".$report_zindex."; ".$stylevalue.$size.$opacity."\"";
			#$onMousedown = "OnMousedown=\"aktivate('div".$report_ID."','pic".$report_ID."');\"";
		}
		$tagType = "div";
		$content = "<IMG SRC=\"".$path."\" ID=\"pic".$report_ID."\" $size1 style=\"z-index:".$report_zindex."\">";
	}elseif($tab_el_type == "dbdat"){

		if(lmb_strpos($report_value,";")){
			$content = explode(";",$report_value);
			$content = $gfield[$content[0]]["field_name"][$content[1]]." (".$gtab["desc"][$content[0]].")";
		}else{
			$content = htmlentities($report_value,ENT_QUOTES,$GLOBALS["umgvar"]["charset"]);
		}
		
		if(!$tab_el){
			if($report_width AND $report_height){$size = ";width:".$report_width."; height:".$report_height;}
			$style = "style=\"overflow:hidden;position:absolute; left:".$report_posx."; top:".$report_posy."; z-index:".$report_zindex."; ".$stylevalue.$size."\"";
			#$onMousedown = "OnMousedown=\"aktivate('div".$report_ID."');\"";
			$tagType = "textarea wrap=\"off\" title=\"$content\"";
			$readonly = " READONLY ";
		}else{
			$style = "style=\"overflow:hidden;background-color:transparent;height:15px;width:$report_width;".$stylevalue."\"";
			$tagType = "div";
		}
		echo "<input type=\"hidden\" name=\"extendet_input_div".$report_ID."\" id=\"extendet_input_div".$report_ID."\" value=\"".htmlentities($report_extvalue,ENT_QUOTES,$GLOBALS["umgvar"]["charset"])."\">";
		
	}elseif ($tab_el_type="tab"){

        $content = print_tab($report,$key);
        $tagType = "div";
	}


	if($tagType){
		if($tab_el_type != 'tab'){
			#$OnClick = "OnClick=\"menu_open(event,'div".$report_ID."','".$report_ID."','$tab_el_type','".$report_style."');\"";
			#$onMousedown = "OnMousedown=\"aktivate('div".$report_ID."');\"";
			#$onMousedown = "OnMousedown=\"limbasMenuOpen(event,this,'".$form_ID."',new limbasDictionary($params));\"";
			if($tab_el_type == "bild"){$value = htmljs($report_value);$picstyle = htmljs($report_picstyle);}else{$value = '';$report_picstyle = '';}
			if($tab_el_type == "dbdat"){
				$rdbf = explode(";",$report_dbfield);
				$dbdat_table = $gtab['desc'][$rdbf[0]];
				$dbdat_field = $gfield[$rdbf[0]]['spelling'][$rdbf[1]];
			}
			$onMousedown = "OnMousedown=\"limbasMenuOpen(event,this,'".$report_ID."','$report_style','$tab_el_type','$value','$picstyle','$tab_el','$dbdat_table','$dbdat_field');\"";
			$textAreaName = " name=\"text".$report_ID."\"";
		}

		#if(!$tab_el){
			echo "<$tagType lmbtype=\"$tab_el_type\" $readonly id=\"div".$report_ID."\" lmbselectable=\"1\" $textAreaName $style $OnClick $onMousedown $onMouseOver $onMouseOut>";
			echo $content;
			echo "</$tagType>\n";
		#}else{
		#	$result = "<$tagType $readonly id=\"div".$report_ID."\" $textAreaName $style $OnClick $onMousedown $onMouseOver $onMouseOut>";
		#	$result .= $content;
		#	$result .= "</$tagType>\n";
		#	return $result;
		#}
	}
}

/*----------------- Element-Schleife -------------------*/
function reportElementList($report,$table=null){

    foreach ($report["id"] as $key => $value) {

		if(!$table AND ($report["tab_el_col"][$key] OR $report["tab_el_row"][$key] OR $report["typ"][$key] == "tabuItem") OR $report["typ"][$key] == "tabcell"){continue;}
		if($table AND ($table[0] != $report["tab_el"][$key] OR $table[1] != $report["tab_el_row"][$key] OR $table[2] != $report["tab_el_col"][$key])){continue;}

		// set all cell elements 100%
		if($report['tab_el'][$key]) {
            $report['width'][$key] = '100%';
        }

        printBerichtElement($report, $key);

        #printBerichtElement($report['typ'][$bzm],$report["id"][$bzm],$report[width][$bzm],$report[height][$bzm],$report[posx][$bzm],$report[posy][$bzm],$report[zindex][$bzm],$report[style][$bzm],$report[value][$bzm],$report[extvalue][$bzm],0,$report[tab][$bzm],$report[tab_size][$bzm],$report[data_type][$bzm],$report[verkn_baum][$bzm],$report[dbfield][$bzm],$report[pic_type][$bzm],$report[pic_style][$bzm],$report[pic_size][$bzm],$report[pic_res][$bzm],$report[pic_name][$bzm]);
        #printBerichtElement($report_ID         ,$report_width       ,$report_height       ,$report_posx       ,$report_posy       ,$report_zindex       ,$report_style       ,$report_value     ,$tab_el,$tab_el_type);
        #unset($stylevalue);
        #unset($textstyle);
        $success = 1;
    }

    if($success){return true;}
    return false;
}

reportElementList($report);


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

</div>
</div>
</FORM>

<div style="position:absolute;top:1000px;">&nbsp;</div>
<BR><BR><BR>



<script language="JavaScript">

$(function() {
	$('#innenramen').selectable({
		filter:'[id^="div"]',
		stop: function( event, ui ) {lmb_multiMenu(event);}
	});
});

</script>