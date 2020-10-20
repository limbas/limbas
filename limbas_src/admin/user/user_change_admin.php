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
 * ID: 127
 */
?>
<script type="text/javascript" src="extern/jscalendar/calendar.js?v=<?=$umgvar["version"]?>"></script>
<script type="text/javascript" src="extern/jscalendar/lang/calendar-de.js?v=<?=$umgvar["version"]?>"></script>
<style type="text/css">@import url(extern/jscalendar/jscalendar.css?v=<?=$umgvar["version"]?>);</style>

<SCRIPT LANGUAGE="JavaScript">

function selected(cal, date) {
eval("document.form1.elements['" + elfieldname + "'].value = date;");
}

function closeHandler(cal) {
cal.hide();
}

function showCalendar(event,sell,fieldname,value) {
	elfieldname = fieldname;
	var sel = document.getElementById('diagv');
	var cal = new Calendar(true, null, selected, closeHandler);
	calendar = cal;
	cal.create();
	calendar.setDateFormat("%d.%m.%Y");
	calendar.sel = sel;
	if(value){calendar.parseDate(value);}
	calendar.showAtElement(sel);
	return false;
}

function delete_user(ID,USER){
	del=confirm("<?=$lang[908]?> \"" + USER + "\" <?=$lang[160]?>?");
	if(del) {
		document.form1.user_del.value=ID;
		document.form1.action.value='setup_user_erg';
		document.form1.submit();
	}
}

function gurefresh(DATA) {
        gu = confirm("<?=$lang[896]?>");
        if(gu) {
                document.location.href="main_admin.php?action=setup_grusrref&user=<?=$ID?>&datarefresh=" + DATA + "";
        }
}
function lrefresh() {
        link = confirm("<?=$lang[896]?>");
        if(link) {
                document.location.href="main_admin.php?action=setup_linkref&user=<?=$ID?>";
        }
}
function srefresh() {
        link = confirm("<?=$lang[899]?>");
        if(link) {
                document.location.href="main_admin.php?action=setup_user_change_admin&ID=<?=$ID?>&srefresh=1";
        }
}
function createpass(){
    var x = 0;
    var pass = "";
    var laenge = 8;
	var zeichen="abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
    while (x!=laenge){
        pass+=zeichen.charAt(Math.random()*zeichen.length);
        x++;
    }
    document.form1.elements['userdata[passwort]'].value = pass
}
function send(action) {
	if(action == 'setup_user_neu'){document.form1.user_add.value='1';}
	if((document.form1.elements['userdata[passwort]'].value.length < 5 && document.form1.elements['userdata[passwort]'].value.length > 0) || document.form1.elements['userdata[username]'].value.length < 5 ){
		alert('<?=$lang[1315]?>');
	}else{
		document.form1.submit();
	}
}

function newwin1(USERID) {
	tracking = open("main_admin.php?action=setup_user_tracking&typ=1&userid=" + USERID ,"Tracking","toolbar=0,location=0,status=0,menubar=0,scrollbars=1,resizable=1,width=600,height=600");
}

function newwin2(USERID) {
	userstat = open("main.php?action=userstat&userstat=" + USERID ,"userstatistic","toolbar=0,location=0,status=0,menubar=0,scrollbars=1,resizable=1,width=750,height=550");
}
</SCRIPT>




<FORM ENCTYPE="multipart/form-data" ACTION="main_admin.php" METHOD="post" NAME="form1">
<input type="hidden" name="action" value="setup_user_change_admin">
<input type="hidden" name="ID" value="<?= $ID ?>">
<input type="hidden" name="group_id" value="<?= $result_user["group_id"] ?>">
<input type="hidden" name="user_change" value="1">
<input type="hidden" name="user_del">
<input type="hidden" name="fileview_change">
<input type="hidden" name="debug">
<input type="hidden" name="lock">
<input type="hidden" name="user_add">
<input type="hidden" name="lockbackend">
<input type="hidden" name="staticip">
<input type="hidden" name="superadmin">

<div class="lmbPositionContainerMain" style="overflow: visible;"><?php /* overwriting overflow to fully display the subgroup selection window which otherwise gets cut on long group names */ ?>


<TABLE BORDER="0"  cellspacing="0" cellpadding="2"><TR><TD valign="top">

<TABLE BORDER="0" WIDTH="450" cellspacing="0" cellpadding="2">


<?php
/* --- Ergebnis-Liste Adresseshauptabelle --------------------------------------- */
echo "<TR class=\"tabHeader\"><TD COLSPAN=2 class=\"tabHeaderItem\">";
if($result_user['lock']){echo "<i class=\"lmb-icon-cus lmb-user1-2\"></i>";}
elseif($result_user['aktiv']){echo "<i class=\"lmb-icon lmb-user1-4\"></i>";}
else{echo "<i class=\"lmb-icon lmb-user1-1\"></i>";}
echo "<B>$lang[140]</B></TD></TR>";
echo "<TR class=\"tabBody\"><TD>$lang[563]</TD><TD>".$result_user["erstdatum"]."</TD></TR>";
echo "<TR class=\"tabBody\"><TD>$lang[1792]</TD><TD>".$result_user["editdatum"]."</TD></TR>";
echo "<TR class=\"tabBody\"><TD COLSPAN=\"2\"><HR></TD></TR>";

echo "<TR class=\"tabBody\"><TD>user-id</TD><TD>".$result_user["user_id"]."</TD></TR>";
echo "<TR class=\"tabBody\"><TD>$lang[519]</TD><TD><INPUT TYPE=TEXT STYLE=\"width:250px;\" name=\"userdata[username]\" STYLE=\"width:250px;\" VALUE=\"".$result_user["username"]."\" ";
if($action != "setup_user_neu"){ echo "OnChange=\"alert('for change username, you need to set a password again!');\"";}
echo "></TD></TR>";
if($action == "setup_user_neu"){$pass = lmb_substr(md5(rand()),0,8);}
echo "<TR class=\"tabBody\"><TD>$lang[141]</TD><TD><INPUT TYPE=TEXT name=\"userdata[passwort]\" value=\"$pass\" STYLE=\"width:220px;\">&nbsp;<i class=\"lmb-icon lmb-lock-file\" STYLE=\"cursor:pointer\" OnClick=\"createpass();\"></i></TD></TR>";
echo "<TR class=\"tabBody\"><TD>$lang[142]</TD><TD><INPUT TYPE=TEXT name=\"userdata[vorname]\" STYLE=\"width:250px;\" VALUE=\"".$result_user["vorname"]."\"></TD></TR>";
echo "<TR class=\"tabBody\"><TD>$lang[4]</TD><TD><INPUT TYPE=TEXT name=\"userdata[name]\" STYLE=\"width:250px;\" VALUE=\"".$result_user["name"]."\"></TD></TR>";
echo "<TR class=\"tabBody\"><TD>$lang[612]</TD><TD><INPUT TYPE=TEXT name=\"userdata[email]\" STYLE=\"width:250px;\" VALUE=\"".$result_user["email"]."\"></TD></TR>";
echo "<TR class=\"tabBody\"><TD>Tel</TD><TD><INPUT TYPE=TEXT name=\"userdata[tel]\" STYLE=\"width:250px;\" VALUE=\"".$result_user["tel"]."\"></TD></TR>";
echo "<TR class=\"tabBody\"><TD>Fax</TD><TD><INPUT TYPE=TEXT name=\"userdata[fax]\" STYLE=\"width:250px;\" VALUE=\"".$result_user["fax"]."\"></TD></TR>";
echo "<TR class=\"tabBody\"><TD>Position</TD><TD><INPUT TYPE=TEXT name=\"userdata[position]\" STYLE=\"width:250px;\" VALUE=\"".$result_user["position"]."\"></TD></TR>";
echo "<TR class=\"tabBody\"><TD valign=\"top\">$lang[126]</TD><TD><TEXTAREA name=\"userdata[beschreibung]\" STYLE=\"width:250px;height:40px\">".htmlentities($result_user["beschreibung"],ENT_QUOTES,$umgvar["charset"])."</TEXTAREA></TD></TR>";

if(!$result_user["group_id"] OR !$groupdat["name"][$result_user["group_id"]]){$needgroup = "style=\"color:red;\"";}
echo "<TR class=\"tabBody\"><TD $needgroup>$lang[900]</TD><TD style=\"width:200px\"><DIV style=\"border:1px solid {$farbschema['WEB3']};\">";

echo "<TABLE BORDER=\"0\" cellspacing=\"0\" cellpadding=\"2\" STYLE=\"width:100%;\"><TR>
<TD><li><b><a href=\"main_admin.php?action=setup_group_erg&ID=".$result_user["group_id"]."\">".$groupdat["name"][$result_user["group_id"]]."</a></b></li></TD>
<TD align=\"right\" valign=\"center\" >";
if($ID != 1){
echo "<i class=\"lmb-icon lmb-pencil\" style=\"cursor:pointer;height:24px;\" OnClick=\"limbasDivShow(this,null,'GroupSelect_main');setxypos(event,'GroupSelect_main');\"></i>";
$glitems["name"] = array("maingroup");
$glitems["typ"] = array("radio");
$glsel["maingroup"] = array($result_user["group_id"]);
getGroupTree("GroupSelect_main",$glitems,$glsel);
}
echo "</TD></TR></TABLE>";
#getGroupTree(0,"",array($result_user["group_id"]),"main");
echo "</DIV></TD></TR>";


echo "<TR class=\"tabBody\"><TD VALIGN=\"TOP\">$lang[901]</TD><TD style=\"width:200px\"><DIV style=\"border:1px solid {$farbschema['WEB3']}\">";

echo "<TABLE BORDER=\"0\" cellspacing=\"0\" cellpadding=\"2\" STYLE=\"width:250px;\"><TR><TD>";
if(is_array($result_user["sub_group"])){
foreach ($result_user["sub_group"] as $key => $value){
	echo "<li><a href=\"main_admin.php?action=setup_group_erg&ID=".$value."\">".$groupdat["name"][$value]."</a></li>";
}}
echo "</TD><TD align=\"right\" valign=\"center\">";
echo "<i class=\"lmb-icon lmb-pencil\" style=\"cursor:pointer;height:24px;\" OnClick=\"limbasDivShow(this,null,'GroupSelect_sub');setxypos(event,'GroupSelect_sub');\"></i>";
$glitems["name"] = array("subgroup");
$glitems["typ"] = array("checkbox");
$glsel["subgroup"] = $result_user["sub_group"];
getGroupTree("GroupSelect_sub",$glitems,$glsel);
#getGroupTree(0,"",$result_user["sub_group"],"sub");
echo "</TD></TR></TABLE>";

echo "</DIV></TD></TR>";


echo "<TR class=\"tabBody\"><TD VALIGN=\"TOP\">$lang[2965]</TD><TD style=\"width:200px\"><DIV style=\"border:1px solid {$farbschema['WEB3']}\">";
echo "<TABLE BORDER=\"0\" cellspacing=\"0\" cellpadding=\"2\" STYLE=\"width:100%;\"><TR><TD>";

$result_multitenants = getMultitenant();
if(is_array($result_user["multitenant"])){
foreach ($result_user["multitenant"] as $key => $value){
	echo "<li>".$result_multitenants['name'][$value]."</li>";
}}


echo "</TD><TD align=\"right\" valign=\"center\">";
echo "<i class=\"lmb-icon lmb-pencil\" style=\"cursor:pointer;height:24px;\" OnClick=\"limbasDivShow(this,null,'multitenantlist');setxypos(event,'multitenantlist');\"></i>";
showMultitenant($result_user);
echo "</TD></TR></TABLE>";
echo "</DIV></TD></TR>";

?>
<TR class="tabBody"><TD VALIGN="TOP"><?=$lang[903]?></TD><TD><TEXTAREA NAME="userdata[iprange]" STYLE="width:250px;height:50px"><?=$result_user['iprange']?></TEXTAREA></TD></TR>

<TR class="tabBody"><TD VALIGN="TOP">Farbkennung</TD><TD><input name="userdata[usercolor]" type="text" value="<?=$result_user["usercolor"]?>" style="background-color:#<?=$result_user["usercolor"]?>; color:<?=lmbSuggestColor('#' . $result_user['usercolor'])?>"></TD></TR>

<TR class="tabBody"><TD></TD><TD>
<?php if(file_exists($umgvar['pfad']."/USER/portrait/portrait_$ID.jpg")){?><IMG SRC="USER/portrait/portrait_<?=$ID?>.jpg" BORDER="1"><?php }?>
<BR>
</TD></TR>
</TABLE>

</TD><TD><DIV style="width:20px;">&nbsp;</DIV></TD><TD valign="top">

<TABLE BORDER="0" WIDTH="450" cellspacing="0" cellpadding="2">

<?php
if(!$result_user["uploadsize"]){$result_user["uploadsize"] = $umgvar["default_uloadsize"];}
if(!$result_user["maxresult"]){$result_user["maxresult"] = $umgvar["default_results"];}
if(!isset($result_user["logging"])){$result_user["logging"] = $umgvar["default_loglevel"];}
echo "<TR class=\"tabHeader\"><TD COLSPAN=2 class=\"tabHeaderItem\"><B>$lang[146]</B></TD></TR>";
echo "<TR class=\"tabBody\"><TD colspan=\"2\"><hr></TD></TR>";
echo "<TR class=\"tabBody\"><TD style=\"width:200px\">$lang[1817]</TD><TD><INPUT TYPE=TEXT name=\"userdata[validdate]\" STYLE=\"width:100%;\" VALUE=\"".$result_user["validdate"]."\" onclick=\"showCalendar(event,'diagv','userdata[validdate]',this.value)\"><span id=\"diagv\" style=\"position:absolute;\"></span></TD></TR>";
echo "<TR class=\"tabBody\"><TD>$lang[1300]</TD><TD><SELECT STYLE=\"width:100%;\" name=\"userdata[change_pass]\"><OPTION VALUE=TRUE "; if($result_user["change_pass"] == "1"){echo "SELECTED";} echo">$lang[867]<OPTION VALUE=FALSE ";  if(!$result_user['change_pass']){echo "SELECTED";} echo">$lang[866]</SELECT></TD></TR>";
echo "<TR class=\"tabBody\"><TD>$lang[2262]</TD><TD><INPUT TYPE=TEXT name=\"userdata[gc_maxlifetime]\" STYLE=\"width:100%;\" VALUE=\"".$result_user["gc_maxlifetime"]."\">&nbsp;days</TD></TR>";
${'SELECTED_'.$result_user["logging"]} = 'SELECTED';
echo "<TR class=\"tabBody\"><TD>$lang[656]</TD><TD><SELECT TYPE=TEXT NAME=\"userdata[logging]\" STYLE=\"width:100%;\"><OPTION VALUE=\"0\" $SELECTED_0>$lang[1797]<OPTION VALUE=\"1\" $SELECTED_1>$lang[1798]<OPTION VALUE=\"2\" $SELECTED_2>$lang[1799]</SELECT>";

echo "<TR class=\"tabBody\"><TD colspan=\"2\"><hr></TD></TR>";

/* --- System-Sprache --------------------------------------------- */
echo "<TR class=\"tabBody\"><TD>$lang[624]</TD><TD><SELECT STYLE=\"width:100%;\" NAME=\"userdata[language]\">";
echo "<OPTION VALUE=\"-1\">system";
$sqlquery = "SELECT DISTINCT LANGUAGE,LANGUAGE_ID FROM LMB_LANG";
$rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
while(lmbdb_fetch_row($rs)) {
$langid = lmbdb_result($rs,"LANGUAGE_ID");
if(!$result_user["language"]){$result_user["language"] = $umgvar["default_language"];}
if($result_user["language"] == $langid){
	$SELECTED =  "SELECTED";
}else{
	unset($SELECTED);
}
echo "<OPTION VALUE=\"".urlencode($langid)."\" $SELECTED>".lmbdb_result($rs,"LANGUAGE");
}
echo "</SELECT></TD></TR>";


/* --- Daten-Sprache --------------------------------------------- */
if($umgvar['multi_language'] AND !$result_user["superadmin"]) {
    echo "<TR class=\"tabBody\"><TD>$lang[2980]</TD><TD><SELECT STYLE=\"width:100%;\" NAME=\"userdata[dlanguage]\">";
    echo "<OPTION VALUE=\"-1\">";
    $sqlquery = "SELECT DISTINCT LANGUAGE,LANGUAGE_ID FROM LMB_LANG";
    $rs = lmbdb_exec($db, $sqlquery) or errorhandle(lmbdb_errormsg($db), $sqlquery, $action, __FILE__, __LINE__);
    while (lmbdb_fetch_row($rs)) {
        $langid = lmbdb_result($rs, "LANGUAGE_ID");
        if ($result_user["dlanguage"] == $langid) {
            $SELECTED = "SELECTED";
        } else {
            unset($SELECTED);
        }
        echo "<OPTION VALUE=\"" . urlencode($langid) . "\" $SELECTED>" . lmbdb_result($rs, "LANGUAGE");
    }
    echo "</SELECT></TD></TR>";
}
/* --- Inhalt-Sprache --------------------------------------------- */
/*
echo "<TR class=\"tabBody\"><TD>$lang[624]</TD><TD><SELECT STYLE=\"width:100%;\" NAME=\"userdata[data-language]\">";
echo "<OPTION VALUE=\"-1\">system";


if($umgvar["multi_language"]) {
    $sqlquery = "Select DISTINCT LANGUAGE,LANGUAGE_ID FROM LMB_LANG WHERE LANGUAGE_ID IN (" . implode(',',$umgvar["multi_language"]) . ")";
    $rs = lmbdb_exec($db, $sqlquery) or errorhandle(lmbdb_errormsg($db), $sqlquery, $action, __FILE__, __LINE__);
    while (lmbdb_fetch_row($rs)) {
        $langid = lmbdb_result($rs, "LANGUAGE_ID");
        if (!$result_user["language"]) {
            $result_user["language"] = $umgvar["default_language"];
        }
        if ($result_user["language"] == $langid) {
            $SELECTED = "SELECTED";
        } else {
            unset($SELECTED);
        }
        echo "<OPTION VALUE=\"" . urlencode($langid) . "\" $SELECTED>" . lmbdb_result($rs, "LANGUAGE");
    }
    echo "</SELECT></TD></TR>";
}
*/


if(!$result_user["dateformat"]){$result_user["dateformat"] = $umgvar["default_dateformat"];}
echo "<TR class=\"tabBody\"><TD>".$lang[2576]."</TD><TD><SELECT STYLE=\"width:100%;\" name=\"userdata[dateformat]\"><OPTION VALUE=1 "; if($result_user["dateformat"] == "1"){echo "SELECTED";} echo">deutsch<OPTION VALUE=2 ";  if($result_user["dateformat"] == "2"){echo "SELECTED";} echo">english<OPTION VALUE=3 "; if($result_user["dateformat"] == "3"){echo "SELECTED";} echo">us</SELECT></TD></TR>";
if(!$result_user["timezone"]){$result_user["timezone"] = $umgvar["default_timezone"];}
echo "<TR class=\"tabBody\"><TD>".$lang[1622]."</TD><TD><INPUT TYPE=TEXT name=\"userdata[timezone]\" STYLE=\"width:100%;\" VALUE=\"".$result_user["timezone"]."\"></TD></TR>";
if(!$result_user["setlocale"]){$result_user["setlocale"] = $umgvar["default_setlocale"];}
echo "<TR class=\"tabBody\"><TD>".$lang[902]."</TD><TD><INPUT TYPE=TEXT name=\"userdata[setlocale]\" STYLE=\"width:100%;\" VALUE=\"".$result_user["setlocale"]."\"></TD></TR>";


/* --- Farbschema Liste --------------------------------------------- */
echo "<TR class=\"tabBody\"><TD>$lang[623]</TD><TD><SELECT STYLE=\"width:100%;\" name=\"userdata[farbe_schema]\">";
$sqlquery = "SELECT * FROM LMB_COLORSCHEMES";
$rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
while(lmbdb_fetch_row($rs)) {
$farbid = lmbdb_result($rs,"ID");
if(!$result_user["farbschema"]){$result_user["farbschema"] = $umgvar["default_usercolor"];}
if($result_user["farbschema"] == $farbid){
	$SELECTED =  "SELECTED";
}else {
	unset($SELECTED);
}

echo "<OPTION VALUE=\"$farbid\" $SELECTED>".lmbdb_result($rs,"NAME");
}
echo "</SELECT></TD></TR>";

/* --- Layout Liste --------------------------------------------- */
echo "<TR class=\"tabBody\"><TD>$lang[698]</TD><TD><SELECT STYLE=\"width:100%;\" NAME=\"userdata[layout]\">";
if($path = read_dir($umgvar["pfad"]."/layout")){
if(!$result_user["layout"]){$result_user["layout"] = $umgvar["default_layout"];}
foreach($path["name"] as $key => $value){
	if($path["typ"][$key] == "dir"){
		if($result_user["layout"] == $value){
			$SELECTED =  "SELECTED";
		}else{
		 	unset($SELECTED);
		}
		$valuena = $value;
		echo "<OPTION VALUE=\"".$value."\" $SELECTED>".$valuena;
	}
}
}
unset($pfad);
echo "</SELECT></TD></TR>";

echo "<TR class=\"tabBody\"><TD colspan=\"2\"><hr></TD></TR>";

echo "<TR class=\"tabBody\"><TD>$lang[616]</TD><TD><INPUT TYPE=TEXT name=\"userdata[maxresult]\" STYLE=\"width:100%;\" VALUE=\"".$result_user["maxresult"]."\"></TD></TR>";
echo "<TR class=\"tabBody\"><TD>$lang[716]</TD><TD><INPUT TYPE=TEXT name=\"userdata[uploadsize]\" STYLE=\"width:100%;\" VALUE=\"".$result_user["uploadsize"]."\">&nbsp;Mbyte</TD></TR>";
#echo "<TR class=\"tabBody\"><TD>$lang[704]</TD><TD><SELECT STYLE=\"width:100%;\" name=\"userdata[data_display]\"><OPTION VALUE=1 "; if($result_user["data_display"] == "1"){echo "SELECTED";} echo">$lang[633]<OPTION VALUE=2 ";  if($result_user['data_display'] == "2"){echo "SELECTED";} echo">$lang[632]</SELECT></TD></TR>";

echo "<TR class=\"tabBody\"><TD colspan=\"2\"><hr></TD></TR>";

?>

<?php if($ID){?>
<?php if($ID != 1){?>
<TR class="tabBody"><TD VALIGN="TOP"><?=$lang[1781]?></TD><TD><TEXTAREA NAME="userdata[locktxt]" STYLE="width:250px;height:50px"><?=$result_user['locktxt']?></TEXTAREA></TD></TR>
<?php }?>
<TR class="tabBody"><TD COLSPAN=2 HEIGHT=20>&nbsp;</TD></TR>
<TR class="tabHeader"><TD COLSPAN=2 class="tabHeaderItem"><B><?=$lang[1780]?></B></TD></TR>
<?php if($ID != 1){?>
<TR class="tabBody"><TD><?=$lang[657]?></TD><TD ALIGN="LEFT"><INPUT TYPE="CHECKBOX" STYLE="border:none;background-color:transparent;" OnClick="document.form1.lock.value='1';" <?php if($result_user['lock']){echo "CHECKED";}?>></TD></TR>


<?php /*<TR class="tabBody"><TD><?=$lang[2239]?></TD><TD ALIGN="LEFT"><INPUT TYPE="CHECKBOX" STYLE="border:none;background-color:transparent;" OnClick="document.form1.lockbackend.value='1';" <?php if($result_user['lockbackend']){echo "CHECKED";}?>></TD></TR>*/?>


<?php }?>

<?php if($session['superadmin'] AND $ID != 1){?><TR class="tabBody"><TD>Superadmin</TD><TD ALIGN="LEFT"><INPUT TYPE="CHECKBOX" STYLE="border:none;background-color:transparent;" OnClick="document.form1.superadmin.value='1';" <?php if($result_user['superadmin']){echo "CHECKED";}?>></TD></TR><?php }?>
<TR class="tabBody"><TD><?=$lang[911]?></TD><TD ALIGN="LEFT"><INPUT TYPE="CHECKBOX" STYLE="border:none;background-color:transparent;" OnClick="document.form1.debug.value='1';" <?php if($result_user['debug']){echo "CHECKED";}?>></TD></TR>
<TR class="tabBody"><TD><?=$lang[2353]?></TD><TD ALIGN="LEFT"><INPUT TYPE="CHECKBOX" STYLE="border:none;background-color:transparent;" OnClick="document.form1.staticip.value='1';" <?php if($result_user['staticip']){echo "CHECKED";}?>></TD></TR>
<TR class="tabBody"><TD colspan="2">&nbsp;</TD></TR>
<TR class="tabBody"><TD><a OnClick="srefresh()" href=#><?=$lang[904]?></a></TD><TD ALIGN="LEFT"><i class="lmb-icon lmb-application-refresh" STYLE="cursor:pointer" border="0" OnClick="srefresh()"></i></TD></TR>
<TR class="tabBody"><TD><a OnClick="newwin1('<?=$ID?>')" href=#><?=$lang[1250]?></a></TD><TD ALIGN="LEFT"><i class="lmb-icon lmb-history" STYLE="cursor:pointer" border="0" OnClick="newwin1('<?=$ID?>')"></i></TD></TR>
<TR class="tabBody"><TD><a OnClick="newwin2('<?=$ID;?>')" href=#><?=$lang[1791]?></a></TD><TD ALIGN="LEFT"><i class="lmb-icon lmb-calendar-alt2" STYLE="cursor:pointer" border="0" OnClick="newwin2('<?=$ID;?>')"></i></TD></TR>
<?php }?>
<TR class="tabBody"><TD style=\"width:100%\"></TD><TD>&nbsp;</TD></TR>


</TABLE></TD></TR>

    <TR class="tabBody"><TD colspan="3"><hr></TD></TR>
    <TR><TD colspan="3">



<TABLE border="0">


<TR><TD VALIGN="TOP"><INPUT TYPE="button" value="<?=$lang[522]?>" STYLE="width:80px;" OnClick="send('<?=$action?>');"></TD>
<TD VALIGN="TOP">
<?php if($session["user_id"] != $ID AND $ID){?>
<INPUT TYPE="CHECKBOX" NAME="usermail" VALUE="1" STYLE="border:none;background-color:transparent;"> <?=$lang[2577]?>
<?php }?>
</TD>

<TD style="width:50px;">&nbsp;</TD>

<?php if($result_user["username"] != 'admin' AND $session["group_id"] == 1 AND $ID){?>
<TD VALIGN="TOP">
<INPUT TYPE="button" value="<?=$lang[160]?>" STYLE="width:80px;" OnClick="delete_user('<?= $result_user["user_id"] ?>','<?= $result_user["username"] ?>')" STYLE="cursor:pointer;color:red;">
</TD><TD>

<INPUT TYPE="CHECKBOX" NAME="delete_user_files" STYLE="border:none;background-color:transparent;" CHECKED>&nbsp;<?=$lang[1481]?>
<BR>
<INPUT TYPE="CHECKBOX" NAME="delete_user_total" STYLE="border:none;background-color:transparent;">&nbsp;<?=$lang[1727]?>
</TD>
<?php }?>

</TR>
</TABLE>


</TD></TR></TABLE>



</div>
</FORM>