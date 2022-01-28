<?php
/*
 * Copyright notice
 * (c) 1998-2021 Limbas GmbH(support@limbas.org)
 * All rights reserved
 * This script is part of the LIMBAS project. The LIMBAS project is free software; you can redistribute it and/or modify it on 2 Ways:
 * Under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * Or
 * In a Propritary Software Licence http://limbas.org
 * The GNU General Public License can be found at http://www.gnu.org/copyleft/gpl.html.
 * A copy is found in the textfile GPL.txt and important notices to the license from the author is found in LICENSE.txt distributed with these scripts.
 * This script is distributed WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * This copyright notice MUST APPEAR in all copies of the script!
 * Version 4.3.36.1319
 */

/*
 * ID: 52
 */


# EXTENSIONS
if($GLOBALS["gLmbExt"]["ext_explorer_detail.inc"]){
	foreach ($GLOBALS["gLmbExt"]["ext_explorer_detail.inc"] as $key => $extfile){
		require_once($extfile);
	}
}

?>

<script language="JavaScript">

// ---------------- Abteilungen verstecken ----------------------
function hide_part() {
	document.getElementById('format').style.display = 'none';
	document.getElementById('metadata').style.display = 'none';
	<?php if($forigin OR $ffile["d_tabid"]){?>
	document.getElementById('origin').style.display = 'none';
	<?php }?>
	<?php if($exifdata){?>
	document.getElementById('exifdata').style.display = 'none';
	<?php }if($vfile['count']){?>
	document.getElementById('versioning').style.display = 'none';
	<?php }if($dfile){?>
	document.getElementById('duplicates').style.display = 'none';
	<?php }?>
}


// ---------------- Zeige Abteilung ----------------------
function show_part(part) {
	hide_part();
	
	el = document.getElementById("menu_"+part);
	limbasSetLayoutClassTabs(el,'tabpoolItemInactive','tabpoolItemActive');
	document.getElementById(part).style.display = '';
}



</SCRIPT>


<FORM ACTION="main.php" METHOD="post" name="form1">
<input type="hidden" name="action" value="explorer_detail">
<input type="hidden" name="ID" value="<?=$ID?>">
<input type="hidden" name="LID" value="<?=$LID?>">
<input type="hidden" name="show_part">
<input type="hidden" name="history_fields">
<input type="hidden" name="change_ok">
<input type="hidden" name="verkn_addfrom" VALUE="<?=$verkn_addfrom;?>">

<div id="lmbAjaxContainer" class="ajax_container" style="position:absolute;visibility:hidden;" OnClick="activ_menu=1;"></div>

<div class="lmbPositionContainerMain">
<TABLE class="tabfringe" CELLPADDING="0" CELLSPACING="0" style="width:660px;">

<TR><TD>

<?php if($ffile["vact"]){$vidnr = "<SPAN STYLE=\"color:green;\"><b>".$ffile["vid"]."</b>";}else{$vidnr = "<SPAN STYLE=\"color:red;\"><b>".$ffile["vid"]."</b>";}?>
<TABLE BORDER="0" cellspacing="0" cellpadding="0"><TR class="tabpoolItemTR">
<TD ID="menu_format" class="tabpoolItemActive" OnClick="show_part('format');"><?=$lang[1634]?>&nbsp;</TD>
<TD ID="menu_metadata" class="tabpoolItemInactive" OnClick="show_part('metadata');"><?=$lang[1635]?>&nbsp;</TD>
<?php if($vfile['count']){?><TD ID="menu_versioning" class="tabpoolItemInactive" OnClick="show_part('versioning');"><?=$lang[2]?>&nbsp;<?=$vidnr?>&nbsp;</TD><?php }?>
        <?php if($dfile["id"]){?><TD ID="menu_duplicates" class="tabpoolItemInactive" OnClick="show_part('duplicates');"><?=$lang[1685]?>&nbsp;<span style="color:Green">(<?=count($dfile["id"])?>)&nbsp;</span></TD><?php }?>
<?php if($forigin OR $ffile["d_tabid"]){?><TD ID="menu_origin" class="tabpoolItemInactive" OnClick="show_part('origin');"><?=$lang[2236]?>&nbsp;</TD><?php }?>
<?php if($exifdata){?><TD ID="menu_exifdata" class="tabpoolItemInactive" OnClick="show_part('exifdata');"><?=$lang[1737]?>&nbsp;</TD><?php }?>

<?php /*
<TD ID="menu_iptcdata" class="tabpoolItemInactive" OnClick="show_part('iptcdata');"><?=$lang[1738]?>&nbsp;</TD>
<TD ID="menu_xmpdata" class="tabpoolItemInactive" OnClick="show_part('xmpdata');"><?=$lang[1740]?>&nbsp;</TD>
*/
?>


<TD class="tabpoolItemSpace">&nbsp;</TD>
</TR></TABLE>


<tr class="tabBody"><td class="tabpoolfringe">
<TABLE ID="tab1" width="100%" cellspacing="0" cellpadding="0" class="tabBody">

<TR style="height:40px;"><TD><INPUT TYPE="TEXT" STYLE="border:1px solid <?=$farbschema['WEB4']?>;width:100%;height:16px;overflow:hidden;color:blue;" VALUE="<?=$file_url?>"></TD></TR>
<tr><td>

<DIV ID="format" STYLE="display:none;">
<TABLE BORDER="0" cellspacing="0" cellpadding="2">
<?php
# --- Allgemein ---
echo "<TR class=\"tabHeader\"><TD class=\"tabHeaderItem\" COLSPAN=\"2\">$lang[1634]</TD></TR>";
echo "<TR class=\"tabBody\"><TD VALIGN=\"TOP\" STYLE=\"width:20%\">$lang[4]:</TD><TD><A HREF=\"main.php?action=download&ID=$ID\" TARGET=\"new\">".htmlentities($ffile["name"],ENT_QUOTES,$umgvar["charset"])."</A></TD></TR>";
echo "<TR class=\"tabBody\"><TD VALIGN=\"TOP\">$lang[1638]: </TD><TD STYLE=\"color:{$farbschema['WEB4']}\">".$ffile['erstuser']."</TD></TR>";
echo "<TR class=\"tabBody\"><TD VALIGN=\"TOP\">$lang[1639]: </TD><TD STYLE=\"color:{$farbschema['WEB4']}\">".$ffile['datum']."</TD></TR>";
echo "<TR class=\"tabBody\"><TD VALIGN=\"TOP\">$lang[210]: </TD><TD STYLE=\"color:{$farbschema['WEB4']};cursor:pointer;\">".$ffile['size']."</TD></TR>";


# --- Format ---
echo "<TR class=\"tabHeader\"><TD class=\"tabHeaderItem\" COLSPAN=\"2\">$lang[1667]</TD></TR>";
if($ffile['mimetype']){echo "<TR class=\"tabBody\"><TD  VALIGN=\"TOP\">$lang[1637]: </TD><TD STYLE=\"color:{$farbschema['WEB4']}\">".nl2br(htmlentities($ffile["mimetype"],ENT_QUOTES,$GLOBALS["umgvar"]["charset"]))."</TD></TR>";}
if($ffile['format']){echo "<TR class=\"tabBody\"><TD  VALIGN=\"TOP\">$lang[1563]: </TD><TD STYLE=\"color:{$farbschema['WEB4']}\">".nl2br(htmlentities($ffile["format"],ENT_QUOTES,$GLOBALS["umgvar"]["charset"]))."</TD></TR>";}
if($ffile['geometry']){echo "<TR class=\"tabBody\"><TD  VALIGN=\"TOP\">$lang[1564]: </TD><TD STYLE=\"color:{$farbschema['WEB4']}\">".nl2br(htmlentities($ffile["geometry"],ENT_QUOTES,$GLOBALS["umgvar"]["charset"]))."</TD></TR>";}
if($ffile['resolution']){echo "<TR class=\"tabBody\"><TD  VALIGN=\"TOP\">$lang[1565]: </TD><TD STYLE=\"color:{$farbschema['WEB4']}\">".nl2br(htmlentities($ffile["resolution"],ENT_QUOTES,$GLOBALS["umgvar"]["charset"]))."</TD></TR>";}
if($ffile['depth']){echo "<TR class=\"tabBody\"><TD  VALIGN=\"TOP\">$lang[1566]: </TD><TD STYLE=\"color:{$farbschema['WEB4']}\">".nl2br(htmlentities($ffile["depth"],ENT_QUOTES,$GLOBALS["umgvar"]["charset"]))."</TD></TR>";}
if($ffile['colors']){echo "<TR class=\"tabBody\"><TD  VALIGN=\"TOP\">$lang[1567]: </TD><TD STYLE=\"color:{$farbschema['WEB4']}\">".nl2br(htmlentities($ffile["colors"],ENT_QUOTES,$GLOBALS["umgvar"]["charset"]))."</TD></TR>";}
if($ffile['type']){echo "<TR class=\"tabBody\"><TD  VALIGN=\"TOP\">$lang[623]: </TD><TD STYLE=\"color:{$farbschema['WEB4']}\">".nl2br(htmlentities($ffile["type"],ENT_QUOTES,$GLOBALS["umgvar"]["charset"]))."</TD></TR>";}

# --- Indizierung ---
if($ffile["indize"]){
	echo "<TR class=\"tabHeader\"><TD class=\"tabHeaderItem\" COLSPAN=\"2\">$lang[1720]</TD></TR>";
	echo "<TR class=\"tabBody\"><TD VALIGN=\"TOP\">$lang[1719]: </TD><TD STYLE=\"color:{$farbschema['WEB4']}\">".get_date($ffile['indize_time'],2)." (".round($ffile['indize_needtime'],2)."sec.)</TD></TR>";
}

# --- Vorschau ---
$size = explode("x",$umgvar["thumbsize2"]);
$img = IMACK_ConvertThumbs(array($ID,$ffile["secname"],$ffile["mimeid"],$ffile["thumb_ok"],null,$ffile["mid"]),$size[0],$size[1],1);
if($img){
	#$filename = $umgvar["upload_pfad"].$ffile["secname"].".".$ffile["ext"];
	$filename = lmb_getFilePath($ID,$level,$ffile["secname"],$ffile["ext"]);
	echo "<TR class=\"tabHeader\"><TD class=\"tabHeaderItem\" COLSPAN=\"2\">$lang[1739]</TD></TR>";
	echo "<TR class=\"tabBody\"><TD></TD><TD><IMG SRC=\"$img\" BORDER=\"1\" STYLE=\"padding:10px;background-color:#CCCCCC\"></TD></TR>";
}


?>
</TABLE>
</DIV>



<?php # ----------- Herkunft ---------------?>
<DIV ID="origin" STYLE="display:none;">
<TABLE BORDER="0" cellspacing="0" cellpadding="0" WIDTH="650px" STYLE="border-collapse:collapse;"><TR><TD>
<?php
# Feldtyp Upload
#if($ffile["d_tabid"]){
#	echo "<TR class=\"tabHeader\"><TD class=\"tabHeaderItem\" COLSPAN=\"5\">".htmlentities($ffile["d_tab"],ENT_QUOTES,$GLOBALS["umgvar"]["charset"])."&nbsp;&nbsp;(".htmlentities($ffile["d_field"],ENT_QUOTES,$GLOBALS["umgvar"]["charset"]).")</TD></TR>";
#	echo "<TR class=\"tabBody\" OnClick=\"open_tab('".$ffile["d_tabid"]."','".$ffile["d_id"]."')\"><TD style=\"width:20px;\"></TD><TD class=\"link\" VALIGN=\"TOP\">".$ffile["d_id"]."</TD><TD></TD></TR>";
#}

# Verknüpfungen
if($forigin){
	foreach($forigin as $key => $value){
		foreach($value as $key1 => $value1){
			echo "<tr class=\"tabHeader\"><td class=\"tabHeaderItem\" COLSPAN=\"5\">".$gtab["desc"][$key]."&nbsp;&nbsp;(".$forigin[$key][$key1]["field"].")</td></tr>";
			echo "<tr class=\"tabSubHeader\"><td class=\"tabSubHeaderItem\" style=\"width:20px;\"></td><td class=\"tabSubHeaderItem\">id</td><td class=\"tabSubHeaderItem\">".$lang[2235]."</td><td class=\"tabSubHeaderItem\">".$lang[2111]."</td><td class=\"tabSubHeaderItem\">".$lang[160]."</td></tr>";
			foreach($forigin[$key][$key1]["id"] as $key2 => $value2){
				echo "<tr class=\"tabBody\">
				<td style=\"width:20px;\"></td><td OnClick=\"lmEx_openDataset('".$key."','".$forigin[$key][$key1]["id"][$key2]."')\" class=\"link\">".$forigin[$key][$key1]["id"][$key2]."</td>
				<td OnClick=\"lmEx_openDataset('".$key."','".$forigin[$key][$key1]["id"][$key2]."')\" class=\"link\">".$forigin[$key][$key1]["value"][$key2]."</td>
				<td>".$forigin[$key][$key1]["folder"][$key2]."</td>";
				if($gfield[$key]["perm_edit"][$key1]){echo "<td class=\"link\"><i class=\"lmb-icon lmb-trash\" border=0 OnClick=\"lmEx_dropRelation($ID,$level,'".$key."_".$key1."_".$forigin[$key][$key1]["id"][$key2]."')\"></i></td>";}
				echo "</tr>";
			}
		}
	}
}
?>
</TD></TR></TABLE>
<BR><BR>
</DIV>

<?php # ----------- Metadaten ---------------?>
<div id="metadata" STYLE="display:none;">
<?php
$gtabid = $gtab["argresult_id"]["LDMS_META"];
if($gtab["tab_view_form"][$gtabid]){
	$dimension = explode('x',$gformlist[$gtabid]["dimension"][$gtab["tab_view_form"][$gtabid]]);
	require_once('gtab/gtab_form.lib');
	$gresult = get_gresult($gtabid,null,null,null,0,0,$ID);
	form_gresult($ID,$gtabid,$gtab["tab_view_form"][$gtabid],$gresult); # need for fields of related tables
	form_gresult($ID,$gtab["argresult_id"]["LDMS_FILES"],$gtab["tab_view_form"][$gtabid],$gresult); # need for fields of related tables
	echo '<div style="width:100%;position:relative;height:'.$dimension[1].'px;">';
	formListElements('gtab_change',$gtabid,$ID,$gresult,$gtab["tab_view_form"][$gtabid]);
	echo '</div>';
}else{
	$gresult = get_gresult($gtabid,null,null,null,0,0,$ID);
	echo "<table width=\"650px\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" style=\"border-collapse:collapse;\">\n";
	defaultViewElements($gtabid,$ID,$gresult);
	echo "<tr><td></td><td><input class=\"submit\" type=\"button\" name=\"LmEx_changeMeta\" style=\"cursor:pointer\" value=\"$lang[33]\" onclick=\"document.form1.show_part.value='metadata';send_form('1');\"></td></tr>";
	echo "</table>";
}

?>
</div>

<?php # ----------- Exifdaten ---------------
if($exifdata){?>
<DIV ID="exifdata" STYLE="display:none;">
<TABLE BORDER="0" cellspacing="0" cellpadding="0" WIDTH="650px" STYLE="border-collapse:collapse;"><TR><TD>
<?= $exifdata ?>
</TD></TR></TABLE>
<BR><BR>
</DIV>
<?php }?>




<?php # ----------- Dublikate ---------------?>
<DIV ID="duplicates" STYLE="display:none;">
<br>
<TABLE BORDER="0" cellspacing="0" cellpadding="0" WIDTH="650px" STYLE="border-collapse:collapse;"><TR><TD>
<?php
if($dfile["id"]){
	foreach ($dfile["id"] as $key => $value){
		echo "<tr><td nowrap><A HREF=\"main.php?&action=download&ID=".$dfile["id"][$key]."\" TARGET=\"new\">".$dfile["name"][$key]."</A></td><td nowrap>".file_size($dfile["size"][$key])."</td><td nowrap>".$userdat["vorname"][$dfile["erstuser"][$key]]." ".$userdat["name"][$dfile["erstuser"][$key]]."</td><td nowrap>".get_date($dfile["erstdatum"][$key],1)."</td></tr>";
		echo "<TR><td colspan=\"4\" style=\"overflow:hidden;border-bottom:1px solid grey;\"><div style=\"overflow:hidden;width:100%;\"><I>"."/".lmb_getUrlFromLevel($dfile["level"][$key],0)."</I></A></div></td></TR>";
	}
}
?>
</TD></TR></TABLE>
<BR><BR>
</DIV>

<?php # ----------- Versionen ---------------?>

<DIV ID="versioning" STYLE="display:none;">
<TABLE BORDER="0" cellspacing="0" cellpadding="2" WIDTH="650px" STYLE="border-collapse:collapse;">
<TR><TD></TD><TD BGCOLOR="<?=$farbschema["WEB7"]?>" colspan="3"><B>diff</B></TD></TR>

<?php
$maxvid = count($vfile['id']);
$bzm=1;
if($vfile['id'] AND count($vfile['id']) > 1){
if($ffile['mimetype']){$meta = explode("/",$ffile['mimetype']);}

foreach($vfile['id'] as $key => $value){
	if($value == $ffile['id']){$fstyle = "color:black;";}else{$fstyle = "color:grey;";}
	if($vfile['nr'][$key] == $maxvid){$fdesc = " ($lang[2018])";}else{$fdesc = "";}
	echo "<TR><TD ROWSPAN=\"2\">&nbsp;</TD>";
	if($meta[0] != "image" AND  $meta[0] != "video" AND $meta[0] != "audio"){
		echo "<TD NOWRAP ROWSPAN=\"2\" VALIGN=\"TOP\" STYLE=\"border:1px solid {$farbschema['WEB4']};width:50px;\">";
		if($maxvid != $bzm){
			echo "&nbsp;<i class=\"lmb-icon lmb-file-code\" OnClick=\"limbasFileVersionDiff(this,$value,".$vfile['id'][$key+1].",1)\" STYLE=\"cursor:pointer;\"></i>";
		}
	}
	echo "</TD>";
	echo "<TD ROWSPAN=\"2\" VALIGN=\"TOP\" STYLE=\"border:1px solid {$farbschema['WEB4']};\">";
	if($value != $ffile['id']){echo "<A HREF=\"JavaScript:document.location.href='main.php?&action=explorer_detail&level=$level&LID=$level&ID=$value'\">";}
	echo "<SPAN STYLE=\"$fstyle\">".$lang[2]." ".$vfile['nr'][$key]."</SPAN></A> $fdesc</TD>
	<TD STYLE=\"width:120px;border:1px solid ".$farbschema['WEB4'].";\">".$vfile['erstdatum'][$key]."</TD><TD STYLE=\"border:1px solid ".$farbschema['WEB4']."\">".$vfile['erstuser'][$key]."</TD></TR>
	<TR><TD COLSPAN=\"2\" STYLE=\"border:1px solid ".$farbschema['WEB4'].";font-size:9px;\">".$vfile['vnote'][$key]."</TD></TR>\n";

	$bzm++;
}
}
?>
</TABLE>
<BR><BR>
</DIV>

</td></tr></table>
</td></tr></table></div>
</FORM>




<script language="JavaScript">
<?php if($show_part){?>
show_part('<?=$show_part?>');
<?php }?>
</script>