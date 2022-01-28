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
 * ID:
 */



require("lib/include.lib");

function call_soap($filter_page,$filter_anzahl,$content,$autor,$titel,$keyword,$year,$part_content,$order){

	global $filter;


	$lmpar[1]["getvars"] = array('fresult');					# return result arrays, you can use (fresult, gtab, gfield, umgvar). fresult is needed for resultsets
	$lmpar[1]["action"] = "explorer_main";						# you can use tables [gtab_erg] or filemanager [explorer_main]
	$lmpar[1]['show_fields'] = array("19_16","19_25","19_19");	# IDs of requested fileds in tables, you can use files-table and meta-tables (tabid_field_id)
	$lmpar[1]["LID"] = "2";										# ID of folder where searched
	#$lmpar[1]["MID"] = "";										#
	$lmpar[1]["typ"] = "1";										# type of filemanager, 1 = public directory, 2 = messages, 3 = tables, 4 = user directory, 5 = reports, 6 = dash, 7 = table relation
	$lmpar[1]["sub"] = "1";										# use subfolders, 1 = yes
	$lmpar[1]["ffilter"]["viewmode"] = 1;						# viewmodus, 1 = normal, 2 = serachengine
	$lmpar[1]["ffilter"]["anzahl"] = 50;						# count of datasets seen in one page
	$lmpar[1]["ffilter"]["page"] = 1;							# current page
	$lmpar[1]["ffilter"]["content_ao1"] = 1;					# search and or, 1 = and, 2 = or
	$lmpar[1]["ffilter"]["content_cs"] = 0;						# search part of the word


	if($content){
		if($part_content){
			$lmpar[1]["ffilter"]["content_cs"] = 1;
			$GLOBALS["part_content"] = "CHECKED";
		}

		$lmpar[1]["ffilter"]["viewmode"] = 2;
		$lmpar[1]["ffilter"]["content"] = $content;
	}

	if($autor){
		$lmpar[1]["ffilter"]["search"]["19_16"] = $autor;
	}
	if($year){
		$lmpar[1]["ffilter"]["search"]["19_25"] = $year;
	}
	if($keyword){
		$lmpar[1]["ffilter"]["search"]["19_19"] = $keyword;
	}
	if($titel){
		$lmpar[1]["ffilter"]["search"]["19_34"] = $titel;
		#$lmpar[1]["ffilter"]["search"]["name"] = $titel;
	}

	if($order){
		$lmpar[1]["ffilter"]["order"] = $order;
	}

	if(is_numeric($filter_anzahl) AND $filter_anzahl > 0){$filter["anzahl"] = $filter_anzahl;}else{$filter["anzahl"] = 50;}
	if(is_numeric($filter_page) AND $filter_page > 0){$filter["page"] = $filter_page;}else{$filter["page"] = 1;}

	$lmpar[1]["ffilter"]["anzahl"] = $filter["anzahl"];
	$lmpar[1]["ffilter"]["page"] = $filter["page"];

	return call_client($lmpar);

}

if($reset){
	$content = "";
	$autor = "";
	$titel = "";
	$keyword = "";
	$year = "";
	$search = "";
	$sub_content = "";
}elseif($search AND ($content OR $autor OR $titel OR $keyword OR $year)){
	$lmb = call_soap($filter_page,$filter_anzahl,$content,$autor,$titel,$keyword,$year,$part_content,$order);
}

if(!$lng){
    $lng = 1;
}

if($lng == 1){

    $la["header1"] = "Literaturliste des Vereins f�r Opensource-Dokumentation vor 1971";
    $la["autor"] = "Autor";
    $la["titel"] = "Titel";
    $la["stichwort"] = "Stichwort";
    $la["im_text"] = "im Text";
    $la["erschienen_im"] = "Erschienen im Jahr";
    $la["original_titel"] = "original Titel";
    $la["teil_des_wortes"] = "Teil des Wortes";
    $la["dateiname"] = "Dateiname";
    $la["erscheinen"] = "Erschienen";
    $la["treffer"] = "Treffer";
    $la["seite"] = "Seite";
    $la["zeige"] = "zeige";
    $la["zeilen"] = "Zeilen";
    $la["suchen"] = "Suchen";
    $la["inhalt"] = "Inhalt";
    $la["nix_gefunden"] = "Keine Daten gefunden";
    $la["suchen"] = "Suchen";

}elseif($lng == 2){

    $la["header1"] = "Literaturlist of Opensource Documentation";
    $la["autor"] = "autor";
    $la["titel"] = "title";
    $la["stichwort"] = "keyword";
    $la["im_text"] = "in text";
    $la["erschienen_im"] = "published date";
    $la["original_titel"] = "original title";
    $la["teil_des_wortes"] = "part of the word";
    $la["dateiname"] = "filename";
    $la["erscheinen"] = "published";
    $la["treffer"] = "hits";
    $la["seite"] = "page";
    $la["zeige"] = "show";
    $la["zeilen"] = "rows";
    $la["suchen"] = "search";
    $la["inhalt"] = "content";
    $la["nix_gefunden"] = "no data";
  	$la["suchen"] = "search";
}



?>
<!DOCTYPE HTML PUBLIC "-//IETF//DTD HTML//EN">
<html>
<head>

<style>
BODY {
	FONT-SIZE: 10px;
	FONT-FAMILY: Verdana,Arial,Helvetica;
	FONT-WEIGHT: normal;
	COLOR: #000000;
}
TD {
	FONT-SIZE: 10px;
	FONT-FAMILY: Verdana,Arial,Helvetica;
	FONT-WEIGHT: normal;
	COLOR: #000000;
}
A {
	FONT-SIZE: 10px;
	FONT-FAMILY: Verdana,Arial,Helvetica;
	FONT-WEIGHT: normal;
}
INPUT {
	FONT-SIZE: 10px;
	FONT-FAMILY: Verdana,Arial,Helvetica;
	FONT-WEIGHT: normal;
	HEIGHT:20px;
}

INPUT.checkbox {
	FONT-SIZE: 10px;
	FONT-FAMILY: Verdana,Arial,Helvetica;
	FONT-WEIGHT: normal;
	HEIGHT:13px;
	WIDTH:13px;
}
</style>

<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>LIMBAS Soap Demo</title>



<script type="text/javascript" src="lib/lmsoap.js?v=<?=$umgvar["version"]?>"></script>

<script language="JavaScript">

function publicDownloadFile(url,md5){
	proxywindow = open("" ,"download","toolbar=0,location=0,status=0,menubar=0,scrollbars=1,resizable=1,width=500,height=600");
	proxywindow.location.href="proxy.php?key="+md5+"&url="+url;
}

function publicPreviewHTML(FID){
	open("lib/preview.php?ID=" + FID ,"preview","toolbar=0,location=0,status=0,menubar=0,scrollbars=1,resizable=1,width=500,height=600");
}

function publicFilePost(result){
	//document.location.href = "http://192.168.10.11/limbas/"+result;
	document.form2.action = "http://192.168.10.11/openlimbas/"+result;
	document.form2.submit();
}

function publicHTMLPost(result){
	document.form2.action = "http://192.168.10.11/openlimbas/"+result;
	document.form2.submit();
}

</script>


</head>
<body bgcolor="#FFE3B0" text="#003300" link="#0000FF" vlink="#000080" alink="#800080">

<div style="position:absolute;top:13px;left:650px"><img src="pic/lang/en.gif" OnClick="document.form1.lng.value='2';document.form1.submit();">&nbsp;<img src="pic/lang/de.gif" OnClick="document.form1.lng.value='1';document.form1.submit();"></div>

<table border="0" width="713" cellspacing="0" cellpadding="0">
	<tr><td valign="bottom" align="left" colspan="3" width="703"><img src="pic/kopf.gif" border="0"></td></tr>
	<tr><td width="117" valign="top" align="right" background="pic/side.jpg">&nbsp;</td>
	<td width="586">
		<table border="0" cellpadding="4" width="583">
			<tr>
				<td width="569">
					<h2 align="left" style="color:#000080"><?=$la["header1"]?><br></h2>


					<form method="POST" action="filesearch.php" name="form1">
					<input type="hidden" name="lng" value="<?=$lng?>">
					<input type="hidden" name="ffilter_page" value="<?=$ffilter["page"]?>">
					<input type="hidden" name="search" value="<?=$search?>">
					<input type="hidden" name="raw_content" value="<?=rawurlencode($content)?>">
					<input type="hidden" name="order" value="<?=$order?>">

					<?php
					if($origin_title){$origin_title_ = "CHECKED";}
					if($part_content){$part_content_ = "CHECKED";}
					if(!$content){$content = "Linux";}
					?>

					<table>
						<TR><TD><?=$la["autor"]?>:</TD><TD><input type="text" name="autor" value="<?=$autor?>" size="38"></TD></TR>
						<TR><TD><?=$la["titel"]?>:</TD><TD nowrap><input type="text" name="titel" value="<?=$titel?>" size="38"></TD><TD>&nbsp;<?=$la["original_titel"]?>:&nbsp;&nbsp;</TD>
						<TD><input type="checkbox" name="origin_title" value="true" size="7" class="checkbox" <?=$origin_title_?>></TD></TR>
						<TR><TD><?=$la["stichwort"]?>:</TD><TD><input type="text" name="keyword" value="<?=$keyword?>" size="38"></TD></TR>
						<TR><TD><b><?=$la["im_text"]?>:</b></TD><TD><input type="text" name="content" value="<?=$content?>" size="38"></TD><TD>&nbsp;<?=$la["teil_des_wortes"]?>:&nbsp;&nbsp;</TD>
						<TD><input type="checkbox" name="part_content" value="true" size="7" class="checkbox" <?=$part_content_?>></TD></TR>
						<TR><TD><?=$la["erschienen_im"]?>:</TD><TD><input type="text" name="year" value="<?=$year?>" size="7"></TD></TR>
						<TR><TD align="center" colspan=2><input type="submit" value="<?=$la["suchen"]?>" name="search"> <input type="submit" value="Reset" name="reset"></TD></TR>
					</table>
					<br><br>


					<?php
					#echo "<pre>";
					#print_r($lmb[1]["ffile"]);
					?>


					<?php if($lmb[1]["ffile"]["res_count"] > 0){?>
					<table cellpadding="3" cellspacing="1" style="width:570px;">
					<tr STYLE="background-color:#FFEBD4;">
					<td width="40%"><b><span title="sortieren" style="cursor:pointer" OnClick="document.form1.order.value='name';document.form1.submit();"><?=$la["dateiname"]?></span></b></td>
					<td width="20%"><b><span title="sortieren" style="cursor:pointer" OnClick="document.form1.order.value='16_25';document.form1.submit();"><?=$la["erscheinen"]?></span></b></td>
					<td width="40%"><b><span title="sortieren" style="cursor:pointer" OnClick="document.form1.order.value='19_16';document.form1.submit();"><?=$la["autor"]?></span></b></td></tr>
					<?php
					if($lmb[1]["ffile"]["id"] AND !$content){
						foreach($lmb[1]["ffile"]["id"] as $key => $value){
							$mime = explode("/",$lmb[1]["ffile"]["mimetype"][$key]);

							$url = "main.php?action=download&ID=" . $lmb[1]["ffile"]["id"][$key];
							$url = base64_encode($url);
							$md5 = md5($LIM["key"].$url);

							echo "<tr>
							<td style=\"background-color:1px solid #FFEBD4;\" valign=\"top\"><A HREF=\"Javascript:Javascript:publicDownloadFile('".$url."','".$md5."')\"> ".htmlentities($lmb[1]["ffile"]["name"][$key])."</A>";
							if($mime[1] == "pdf" OR $mime[1] == "msword"){echo "&nbsp;&nbsp;<A HREF=\"Javascript:publicPreviewHTML('".$lmb[1]["ffile"]["id"][$key]."')\" style=\"color:green\">HTML</A>";}
							echo "</td><td style=\"background-color:1px solid #FFEBD4;\" valign=\"top\">".htmlentities($lmb[1]["ffile"]["f19_25"][$key])."</td>
							<td style=\"background-color:1px solid #FFEBD4;\" valign=\"top\">".htmlentities($lmb[1]["ffile"]["f19_16"][$key])."</td>
							</tr>";
							if($lmb[1]["ffile"]["f16_34"][$key] AND $origin_title){
							    echo "<tr><td colspan=\"3\" valign=\"top\"><I>".$lmb[1]["ffile"]["f19_34"][$key]."</I></td></tr>";
							}
						}
					}

					if($lmb[1]["ffile"]["context"] AND $content){
						foreach ($lmb[1]["ffile"]["context"] as $key => $value) {
							$mime = explode("/",$lmb[1]["ffile"]["mimetype"][$key]);

							$url = "main.php?action=download&ID=" . $lmb[1]["ffile"]["id"][$key];
							$url = base64_encode($url);
							$md5 = md5($LIM["key"].$url);

							echo "<tr><td valign=\"top\"><A HREF=\"Javascript:publicDownloadFile('".$url."','".$md5."')\"> ".htmlentities($lmb[1]["ffile"]["name"][$key])."</A>
							&nbsp;&nbsp;<A HREF=\"Javascript:publicPreviewHTML('".$lmb[1]["ffile"]["id"][$key]."')\" style=\"color:green\">HTML</A>
							</td>
							<td style=\"background-color:1px solid #FFEBD4;\" valign=\"top\">".htmlentities($lmb[1]["ffile"]["f19_25"][$key])."</td>
							<td style=\"background-color:1px solid #FFEBD4;\" valign=\"top\">".htmlentities($lmb[1]["ffile"]["f19_16"][$key])."</td>
							</tr>";

    							if($lmb[1]["ffile"]["f19_34"][$key] AND $origin_title){
							    echo "<tr><td colspan=\"3\" valign=\"top\"><I>".$lmb[1]["ffile"]["f19_34"][$key]."</I></td></tr>";
							}

							if($mime[1] == "pdf" OR $mime[1] == "msword"){echo "<tr><td valign=\"top\" colspan=\"2\">".$value."</td></tr>";}
							#echo "<tr><td colspan=\"3\"><div style=\"height:1px;width=100%;background-color:grey\"></div></td></tr>";
						}
					}
					?>



					<tr><td>&nbsp;</td></tr>




					<?php
					echo "<TR BGCOLOR=\"#FFEBD4\"><TD COLSPAN=\"13\"><TABLE cellspacing=\"0\" cellpadding=\"0\"><TR>\n";
					echo "<TD NOWRAP>";
					echo "&nbsp;<B>".$lmb[1]["ffile"]["res_count"]."</B>&nbsp;".$la["treffer"]."&nbsp;</TD>\n";
					echo "<TD><B style=\"color:green;\">|</B>&nbsp;".$la["seite"]."&nbsp;</TD>";
					echo "<TD NOWRAP style=\"width:15px;\"><IMG SRC=\"pic/scrollbeginning.gif\" STYLE=\"cursor:pointer;\" BORDER=\"0\" TITLE=\"zur ersten Seite\" OnClick=\"document.form1.filter_page.value='1';document.form1.submit();\"></TD>\n";
					echo "<TD NOWRAP style=\"width:15px;\"><IMG SRC=\"pic/scrolleft.gif\" STYLE=\"cursor:pointer;\" BORDER=\"0\" TITLE=\"eine Seite zur�ck\" OnClick=\"document.form1.filter_page.value='".($filter["page"] - 1)."';document.form1.submit();\"></TD>\n";
					echo "<TD NOWRAP style=\"width:30px;\"><INPUT TYPE=\"TEXT\" STYLE=\"width:30px;height:13px;font-size:9;padding:0px;text-align:center;border:1px solid black;\" NAME=\"filter_page\" VALUE=\"".$filter["page"]."/".(ceil($lmb[1]["ffile"]["max_count"]/$filter["anzahl"]))."\"></TD>\n";
					echo "<TD NOWRAP style=\"width:15px;\"><IMG SRC=\"pic/scrollright.gif\" STYLE=\"cursor:pointer;\" BORDER=\"0\" TITLE=\"eine Seite weiter\" OnClick=\"document.form1.filter_page.value='".($filter["page"] + 1)."';document.form1.submit();\"></TD>\n";
					echo "<TD NOWRAP style=\"width:15px;\"><IMG SRC=\"pic/scrollend.gif\" STYLE=\"cursor:pointer;\" BORDER=\"0\" TITLE=\"zur letzen Seite\" OnClick=\"document.form1.filter_page.value='".(ceil($lmb[1]["ffile"]["max_count"]/$filter["anzahl"]))."';document.form1.submit();\"\"></TD>\n";
					echo "<TD NOWRAP>&nbsp;<B style=\"color:green;\">|</B>&nbsp;".$la["zeige"]."&nbsp;</TD><TD><INPUT TYPE=\"TEXT\" STYLE=\"width:30px;height:13px;font-size:9;padding:0px;text-align:center;border:1px solid black\" VALUE=\"".$filter["anzahl"]."\" NAME=\"filter_anzahl\" MAXLENGTH=\"2\"></TD><TD>&nbsp;".$la["zeilen"]."</TD>\n";
					if($session["debug"]){echo "<TD NOWRAP style=\"color:#707070\">&nbsp;&nbsp;&nbsp;(".$lmb[1]["ffile"]["need_time"]." sec.)</TD>";}
					echo "</TR><TR><TD STYLE=\"height:3px;\"></TD></TR></TABLE></TD></TR>\n";
					?>


					</table>

					<?php
					}elseif($search){
						echo "<B><span style=\"color:red;\">".$la["nix_gefunden"]."!</span></B>";
					}
					?>


					<br>
					<br>
					<br>



					<font size="1">powered by: <a href=http://www.limbas.org>LIMBAS</a>
				</td>
			</tr>
		</table>
		</form>
	</td>
	<td width="6" bgcolor="#9CCE9C"></td>

</tr>
<tr>
	<td width="117" valign="top" align="left" background="pic/side.jpg">&nbsp;</td>
	<td width="586" >

	</td>
	<td width="6" bgcolor="#9CCE9C"></td>
</tr>
<tr>
	<td width="117" valign="top" align="left" background="pic/side.jpg"></td>
	<td width="586" valign="top" align="center" bgcolor="#9CCE9C"><font size="3">Verein f�r Opensource-Dokumentation vor 1971</font></td>
	<td width="6" bgcolor="#9CCE9C"></td>

</tr>
<tr><td  valign="top" align="left" colspan="3" width="703">
		<img src="pic/fuss.gif" width="713" border="0">
	</td></tr>
</table>

<form name="form2" action="GET" target="_blank"></form>










</body>
</html>