<?php
/*
 * Copyright notice
 * (c) 1998-2016 Limbas GmbH - Axel westhagen (support@limbas.org)
 * All rights reserved
 * This script is part of the LIMBAS project. The LIMBAS project is free software; you can redistribute it and/or modify it on 2 Ways:
 * Under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * Or
 * In a Propritary Software Licence http://limbas.org
 * The GNU General Public License can be found at http://www.gnu.org/copyleft/gpl.html.
 * A copy is found in the textfile GPL.txt and important notices to the license from the author is found in LICENSE.txt distributed with these scripts.
 * This script is distributed WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * This copyright notice MUST APPEAR in all copies of the script!
 * Version 3.0
 */

/*
 * ID: 66
 */


require_once("extra/explorer/explorer_main.lib");

#----------------- Context-Menü -------------------
explContextDetail();
?>


<div id="lmbAjaxContainer" class="ajax_container" style="position:absolute;visibility:hidden;" OnClick="activ_menu=1;"></div>

<DIV ID="filemenu" class="lmbContextMenu" style="position:absolute;visibility:hidden;top:0;z-index:3;" OnClick="activ_menu = 1;">
<? #----------------- Haupt-Menü -------------------
pop_menu(274,'',''); #save
pop_line();
pop_menu(195,'',''); #Info
pop_line();
if($filestruct[addf][$LID] AND !$filestruct["readonly"][$LID]){pop_menu(119,'','');$ln=1;} #neuer Ordner
if($filestruct[edit][$LID] AND !$filestruct["readonly"][$LID] AND $LID){pop_submenu(116,'','');$ln=1;} #umbenennen
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
pop_bottom();
?>
</DIV>


<DIV ID="editmenu" class="lmbContextMenu" style="position:absolute;visibility:hidden;top:0;z-index:3" OnClick="activ_menu = 1;">
<? #----------------- edit-Menü -------------------
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
pop_bottom();
?>
</DIV>

<DIV ID="viewmenu" class="lmbContextMenu" style="position:absolute;visibility:hidden;top:0;z-index:3" OnClick="activ_menu = 1;">
<?
if($ffilter["viewmode"][$LID] == 1){$a = 1;}else{$a = 0;}
pop_menu(222,'','',$a); 				#Datei
if($ffilter["viewmode"][$LID] == 2){$a = 1;}else{$a = 0;}
pop_menu(223,'','',$a); 				#searchengine view
if($ffilter["viewmode"][$LID] == 5){$a = 1;}else{$a = 0;}
if($typ == 7){pop_menu(264,'','',$a);} #tablerelation view
pop_line();
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
pop_bottom();
?>
</DIV>

<DIV ID="extramenu" class="lmbContextMenu" style="position:absolute;visibility:hidden;top:0;z-index:3" OnClick="activ_menu = 1;">
<? #----------------- Extra-Menü -------------------
pop_menu(200,'',''); 	#thumbs neu berechnen
pop_line();
pop_submenu(203,'','');	#konvertieren
pop_menu(247,'','');	#favoriten
pop_submenu(269,'','');	#dublikate
if($umgvar["ocr_enable"]){pop_submenu(262,'','');}	#ocr
if($LINK[297]){
pop_line();
pop_submenu(297,'','');	#import
}
if($LINK[284]){
pop_line();
pop_submenu(284,'','');	#einstellungen
}

# extension
if(function_exists($GLOBALS["gLmbExt"]["menuDMSExtras"][$gtabid])){
	$GLOBALS["gLmbExt"]["menuDMSExtras"]($LID,$ID,$ffile);
}

pop_bottom();
?>
</DIV>

<DIV ID="settingsmenu" class="lmbContextMenu" style="visibility:hidden;z-index:9993" OnClick="activ_menu = 1;">
<?
pop_top('limbasDivMenuSettings');
if($ffilter["force_delete"]){$checked = "checked";}else{$checked = null;}
pop_checkbox(276,"document.form1.ffilter_force_delete.value=this.checked;","",1,$checked,0);
pop_bottom();
?>
</DIV>

<DIV ID="importmenu" class="lmbContextMenu" style="visibility:hidden;z-index:9993" OnClick="activ_menu = 1;">
<?
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
$opt["desc"] = array($lang[2332],$lang[2191],$lang[2192],$lang[2193]);
pop_select($zl,$opt,$sel,null,'LmEx_ImportPathType',$lang[2480]);

pop_line();

pop_submit($lang[2798],"LmEx_uploadFromPath(this,$LID)");
pop_bottom();
?>
</DIV>

<DIV ID="searchmenu" class="lmbContextMenu" style="position:absolute;visibility:hidden;top:0;z-index:3" OnClick="activ_menu = 1;">
<? #----------------- Such-Menü -------------------
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

<DIV ID="downloadmenu" class="lmbContextMenu" style="position:absolute;visibility:hidden;top:0;z-index:4" OnClick="activ_menu = 1;">
<?
pop_top('downloadmenu');
pop_left();
echo "<span style=\"height:16px;cursor:default;\">&nbsp;<b>".$lang[1762]."</b></span>";
pop_right();
pop_line();
pop_left();
echo "<a href=\"#\" OnClick=\"LmEx_download_archive('1');\">&nbsp;zip <FONT COLOR=\"".$farbschema["WEB4"]."\">(windows)</font></a>";
pop_right();
pop_left();
echo "<a href=\"#\" OnClick=\"LmEx_download_archive('2');\">&nbsp;tar.gz <FONT COLOR=\"".$farbschema["WEB4"]."\">(unix)</font></a>";
pop_right();
pop_left();
echo "<a href=\"#\" OnClick=\"LmEx_download_archive('3');\">&nbsp;tar.bz2 <FONT COLOR=\"".$farbschema["WEB4"]."\">(unix)</font></a>";
pop_right();
pop_left();
echo "<a href=\"#\" OnClick=\"LmEx_download_archive('4');\">&nbsp;7z <FONT COLOR=\"".$farbschema["WEB4"]."\">(all)</font></a>";
pop_right();
pop_bottom();
?>
</DIV>

<DIV ID="previewmenu" class="lmbContextMenu" style="position:absolute;visibility:hidden;top:0;z-index:99995" OnClick="activ_menu = 1;">
<?
pop_top('previewmenu');

pop_left();
echo "<table><tr><td>$lang[1563]:&nbsp;</td><td><select id=\"convtoformat\" onchange=\"if(this.value=='jpg' || this.value=='png'){document.getElementById('convtopicdiv').style.display='';}else{document.getElementById('convtopicdiv').style.display='none';}\">
	<option></option>
	<option value=\"jpg\">JPEG</option>
	<option value=\"png\">PNG</option>
	<option value=\"pdf\">PDF</option>
	<option value=\"html\">HTML</option>
	";

if($umgvar['use_unoconv']){
	echo "
		<option value=\"0\"></option>
		<option value=\"0\">---------- unoconv ----------</option>
		<option value=\"uc_pdf\">Portable Document Format (pdf)</option>
		<option value=\"uc_eps\">Encapsulated PostScript (eps)</option>
		<option value=\"0\"></option>
		<option value=\"uc_doc\">Word 97/2000/XP (doc)</option>
		<option value=\"uc_docx\">Word 2007 XML (docx)</option>
		<option value=\"uc_odt\">Open Document Text (odt)</option>
		<option value=\"uc_txt\">Text (txt)</option>
		<option value=\"uc_rtf\">Rich Text (rtf)</option>
		<option value=\"uc_bib\">BibTeX (bib)</option>
		<option value=\"uc_docbook\">DocBook (xml)</option>
		<option value=\"uc_latex\">LaTeX (ltx)</option>
		<option value=\"0\"></option>
		<option value=\"uc_jpg\">JPEG (jpg)</option>
		<option value=\"uc_png\">PNG (png)</option>
		<option value=\"uc_gif\">GIF (gif)</option>
		<option value=\"uc_tiff\">TIFF (tiff)</option>
		<option value=\"0\"></option>
		<option value=\"uc_csv\">CSV (csv)</option>
		<option value=\"uc_xls\">Excel 97/2000/XP (xls)</option>
		<option value=\"uc_xlsx\">Excel 2007 (xlsx)</option>
		<option value=\"uc_swf\">Flash (swf)</option>
		</select></td></tr>
		";
}

echo "<tr id=\"convtopicdiv\" style=\"display:none\"><td>$lang[1141]:</td><td><input type=\"text\" id=\"convtopicsize\" style=\"width:30px;\">&nbsp;px</td></tr></table>";


pop_right();

pop_line();
pop_submenu2($lang[2240],"LmEx_preview_archive(document.getElementById('convtoformat').value,document.getElementById('convtopicsize').value)",$lang[2240]);
pop_bottom();
?>
</DIV>


<?
if($umgvar["ocr_enable"] AND $LINK[262]){
echo "<DIV ID=\"ocrmenu\" class=\"lmbContextMenu\" style=\"position:absolute;visibility:hidden;top:0;z-index:4\" OnClick=\"activ_menu = 1;\">";
pop_top('ocrmenu');
$opt["val"] = $umgvar["ocr_format_val"];
$opt["desc"] = $umgvar["ocr_format_desc"];
pop_select("",$opt,"",1,"ocr_format",$lang[2648],50);
$opt["val"] = $umgvar["ocr_quality_val"];
$opt["desc"] = $umgvar["ocr_quality_desc"];
pop_select("",$opt,"",1,"ocr_quality",$lang[2645],50);
$opt["val"] = array("preview","rename","overwrite","versioning");
$opt["desc"] = array($lang[2646],$lang[2191],$lang[2192],$lang[2193]);
pop_select("",$opt,"",1,"ocr_destination","$lang[2647]",50);
pop_line();
pop_submenu2($lang[2314],"LmEx_ocrfile(document.getElementsByName('ocr_format')[0].value,document.getElementsByName('ocr_destination')[0].value,document.getElementsByName('ocr_quality')[0].value);",$lang[2314]);
pop_bottom();
echo "</DIV>";
}
?>


<DIV ID="cachelist" class="lmbContextMenu" style="position:absolute;visibility:hidden;top:0;z-index:3" OnClick="activ_menu = 1;">
<?
pop_top('cachelist');
pop_left();
?><span ID="cachelist_area"></span><?
pop_right();
pop_bottom();
?>
</DIV>

<DIV ID="rename" class="lmbContextMenu" style="position:absolute;visibility:hidden;top:0;z-index:4" OnClick="activ_menu = 1;">
<form name="form_rename">
<?
pop_top('rename');
pop_input(0,'document.form1.rename_file.value=this.value;LmEx_send_form(1);LmEx_divclose();','rename','','');
pop_bottom();
?>
</form>
</DIV>

<DIV ID="fieldlist" class="lmbContextMenu" style="position:absolute;visibility:hidden;top:-500;z-index:4" OnClick="activ_menu = 1;">
<?
pop_top('fieldlist');
pop_left();echo "&nbsp;<B>$lang[1634]</B>";pop_right();pop_line();
foreach ($gfile["id"] as $key => $value){
	# Felder ohne Rechte
	if(!$gfile["id"][$key] OR $gtab["argresult_id"]["LDMS_FILES"]."_11" == $key){continue;}
	if($gfile['field_type'][$key] == 100){
		pop_left();echo "&nbsp;<B title=\"".$gfile['desc'][$key]."\">".$gfile['title'][$key]."</B>";
		pop_right();
		pop_line();
	}else{
		pop_left();
		if($gfile['show'][$LID][$key]){
			$color = "green";$class = "Icon";$icdis = "";
		}else{
			$color = "black";$class = "";$icdis = "none";
		}
		echo "<i id=\"dcp_$key\" class=\"lmbContextLeft lmb-icon lmb-aktiv\" border=\"0\" style=\"display:$icdis\"></i>";
		echo "<span class=\"lmbContextItem$class\" title=\"".$gfile["tabid"][$key]."_".$gfile["fid"][$key]."\" id=\"dc_$key\" style=\"color:$color;cursor:pointer\" OnClick=\"fieldlist('$key');\">&nbsp;".$gfile['title'][$key]."</span>";
		pop_right();
	}
}
pop_bottom();
?>
</DIV>

<DIV ID="dublicateCheckLayer" style="position:absolute;top:25%;left:25%;visibility:hidden;z-index:5"></DIV>
<DIV ID="limbasDetailSearch" style="position:absolute;z-index:9999;" OnClick="activ_menu = 1;"></DIV>


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
jsvar["searchcount"] = "<?=$umgvar["searchcount"]?>";

// ----- Onload-Aktionen --------
<?
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
<input type="hidden" name="reset" value="1">
</form>

<form enctype="multipart/form-data" action="main.php" method="post" name="form1" id="form1">

<input type="hidden" name="action" value="<?=$action;?>">
<input type="hidden" name="old_action" value="<?=$old_action?>">
<input type="hidden" name="ID" value="<?=$ID;?>">
<input type="hidden" name="MID" value="<?=$MID;?>">
<input type="hidden" name="LID" value="<?=$LID;?>">
<input type="hidden" name="typ" value="<?=$typ;?>">

<input type="hidden" name="del_file">
<input type="hidden" name="move_file">
<input type="hidden" name="copy_file">
<input type="hidden" name="rename_file">
<input type="hidden" name="refresh_file">
<input type="hidden" name="favorite_file">
<input type="hidden" name="ocr_file">
<input type="hidden" name="ocr_format">
<input type="hidden" name="ocr_destination">
<input type="hidden" name="convert_file">

<input type="hidden" name="add_file">
<input type="hidden" name="edit_id">
<input type="hidden" name="ffilter_order">
<input type="hidden" name="ffilter_viewmode">
<input type="hidden" name="ffilter_glob">
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

<?/*
<input type="hidden" name="f_fieldid" VALUE="<?=$f_fieldid?>">
<input type="hidden" name="f_tabid" VALUE="<?=$f_tabid?>">
<input type="hidden" name="f_datid" VALUE="<?=$f_datid?>">

<input type="hidden" name="form_id" VALUE="<?=$form_id;?>">
<input type="hidden" name="gtabid" value="<?=$gtabid;?>">
<input type="hidden" NAME="verknpf" VALUE="<?=$verknpf;?>">
<input type="hidden" name="verkn_addfrom" VALUE="<?=$verkn_addfrom;?>">
<input type="hidden" name="verkn_ID" VALUE="<?=$verkn_ID;?>">
<input type="hidden" name="verkn_tabid" VALUE="<?=$verkn_tabid;?>">
<input type="hidden" name="verkn_fieldid" VALUE="<?=$verkn_fieldid;?>">
<input type="hidden" name="verkn_showonly" VALUE="<?=$verkn_showonly;?>">
<input type="hidden" name="verkn_poolid" VALUE="<?=$verkn_poolid;?>">
*/?>



<div class="lmbfringeGtab">
<TABLE ID="filetab" CELLPADDING="0" CELLSPACING="0" BORDER="0" > <?php //style="width:<?=$ffilter["tabsize"][$LID]"? > ?>

<?
$headerdesc = $filestruct["name"][$LID];
if(!$headerdesc){
	$headerdesc = $lang[1200];
}
?>




<TR ><TD>
<div class="gtabHeaderMenuTR">
<TABLE CELLPADDING="0" CELLSPACING="0" BORDER="0" width="100%"><TR><TD>&nbsp;</TD>
<?if($LINK[195] OR $LINK[190] OR $LINK[203] OR $LINK[221]){?><TD class="gtabHeaderMenuTD" OnClick="LmEx_open_menu(this,'filemenu');" onmouseover="this.className='gtabHeaderMenuTDhover';" onmouseout="this.className='gtabHeaderMenuTD'"><?=$lang[1624]?>&nbsp;</TD><td> | </td><?}?>
<?if($viewmenu["editmenu"]){?><TD class="gtabHeaderMenuTD" OnClick="LmEx_open_menu(this,'editmenu');" onmouseover="this.className='gtabHeaderMenuTDhover';" onmouseout="this.className='gtabHeaderMenuTD'">&nbsp;<?=$lang[1693]?>&nbsp;</TD><td> | </td><?}?>
<?if($LINK[202] OR $LINK[219] OR $LINK[220]){?><TD class="gtabHeaderMenuTD" OnClick="LmEx_open_menu(this,'viewmenu');" onmouseover="this.className='gtabHeaderMenuTDhover';" onmouseout="this.className='gtabHeaderMenuTD'">&nbsp;<?=$lang[1625]?>&nbsp;</TD><td> | </td><?}?>
<?if($LINK[200]){?><TD class="gtabHeaderMenuTD" OnClick="LmEx_open_menu(this,'extramenu');" onmouseover="this.className='gtabHeaderMenuTDhover';" onmouseout="this.className='gtabHeaderMenuTD'">&nbsp;<?=$lang[1939]?>&nbsp;</TD><?}?>
<TD WIDTH="100%">&nbsp;</TD>
</TR></TABLE></div>
</TD></TR>
<?

# Symbolleiste
if($session["symbolbar"]){
	echo "<TR><TD><div class=\"gtabHeaderSymbolTR\"><TABLE CELLPADDING=\"0\" CELLSPACING=\"0\" BORDER=\"0\">";
	echo "<TR>";
	pop_picmenu(274,'','');				# save
	if($filestruct["add"][$LID]){pop_picmenu(128,'','','',"OnClick=\"LmEx_multiupload('1','','','','','','');\"");} 			# upload
	if($filestruct["del"][$LID]){pop_picmenu(171,'','',1);} 		# delete
	pop_picmenu(190,'','',1); 			# download

	echo "<TD>&nbsp;&nbsp;</TD>";
	pop_picmenu(129,'','',1); 			# copy
	if($filestruct["del"][$LID]){pop_picmenu(130,'','',1);} 			# cut
	if($ffilter["copyContext"]){$a = 0;}else{$a = 1;}
	if($filestruct["add"][$LID]){pop_picmenu(191,'','',$a);} 			# paste
	echo "<TD>&nbsp;&nbsp;&nbsp;</TD>";
	#pop_picmenu(219,'',''); 			# fields

	if($ffilter["viewmode"][$LID] == 1){$a = 0;}else{$a = 1;}
	pop_picmenu(222,'','',$a); 			# Datei
	if($ffilter["viewmode"][$LID] == 2){$a = 0;}else{$a = 1;}
	pop_picmenu(223,'','',$a); 			# Schlagwort
	if($ffilter["viewmode"][$LID] == 5){$a = 0;}else{$a = 1;}
	if($typ == 7){pop_picmenu(264,'','',$a);} #tablerelation view
	if($ffilter["viewmode"][$LID] == 3){$a = 0;}else{$a = 1;}
	pop_picmenu(256,'','',$a); 			# Bildshow
	#if($ffilter["viewmode"][$LID] == 4){$a = 1;}else{$a = 0;}
	#pop_picmenu(257,'','',$a); 			# Bild-Übersicht

	echo "<TD>&nbsp;&nbsp;&nbsp;</TD>";
	pop_picmenu(117,'',''); 			# Detailsuche
	pop_picmenu(249,'',''); 			# mini-explorer
	pop_picmenu(217,'',''); 			# zurück setzen

	echo "</TR></TABLE></div></TD></TR>\n";
}

# Pfad
?>
<TR><TD><div class="gtabHeaderInputTR">


<TABLE CELLPADDING="0" CELLSPACING="1" BORDER="0" width="100%">
<TR STYLE="height:20px;"><TD>
<INPUT TYPE="TEXT" STYLE="border:1px solid <?=$farbschema["WEB4"]?>;width:100%;height:17px;background-color:<?=$farbschema["WEB8"]?>;color:blue;z-index:1;" VALUE="<?=$file_url?>" READONLY>
</TD></TR>
</TABLE>

</div>
</TD></TR>

<TR><TD ID="lmbUploadLayer" style="width:100%"></TD></TR>

<tr><td>
<div class="gtabHeaderInputTR" id="gtabExplBody">
<?explMainContent($ID,$LID,$MID,$fid,$typ,$level,$file_url,$ffile,$ffilter);?>
</div>
</td></tr>

<tr><td class="lmbGtabBottom"></td></tr>

</table>
</div>
</form>


<script language="JavaScript">
var obj = $("#gtabExplBody");
obj.on('dragenter', function (e)
{
    e.stopPropagation();
    e.preventDefault();
    $("div").filter('[id^="td_"]').addClass("lmbUploadDragenter");
    return false;
});
obj.on('dragover', function (e)
{
     e.stopPropagation();
     e.preventDefault();
});
obj.on('drop', function (e)
{
     $("div").filter('[id^="td_"]').removeClass("lmbUploadDragenter");
     e.preventDefault();
     var files = e.originalEvent.dataTransfer.files;
     LmEx_dragFileUpload(files,1,'<?=$LID;?>','','','','');
});
</script>