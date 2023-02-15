<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */


?>

<Script language="JavaScript">
/* --- Berichtmenü ----------------------------------- */
function showdiv(evt,NAME) {
    document.getElementById("farbschema").style.visibility='hidden';
    document.getElementById("ampel").style.visibility='hidden';
    document.getElementById(NAME).style.left=evt.pageX;
    document.getElementById(NAME).style.top=evt.pageY;
    document.getElementById(NAME).style.visibility='visible';

}

function newwin1(typ,server,user,pass) {
checkemail = open("main.php?action=user_check_email&ID=<?=$ID?>&e_typ=" + typ + "&e_server=" + server + "&e_user=" + user + "&e_pass=" + pass + "" ,"CheckEmail","toolbar=0,location=0,status=0,menubar=0,scrollbars=1,resizable=0,width=650,height=300");
}

function srefresh() {
	link = confirm("<?=$lang[856]?>");
	if(link) {
		document.location.href="main.php?action=user_change&ID=<?=$ID?>&sess_refresh=<?=$ID?>";
	}
}

function check_pass(pass2) {
	if(pass2 != document.form1.passwort.value){
		document.form1.passwort.value = '';
		document.form1.passwort2.value = '';
		alert('Password incorrect\ntry again!');
	}
}

</Script>




<?php /*----------------- Farbschema -------------------*/?>
<div ID="farbschema" style="position:absolute;visibility:hidden;top:100px;left:100px;z-index:1;" OnClick="this.style.visibility='hidden'">
<TABLE BORDER="0" cellspacing="2" cellpadding="0" STYLE="border:2px solid black" BGCOLOR="<?=$farbschema['WEB8']?>">
<?php
$sqlquery = "SELECT DISTINCT * FROM LMB_COLORSCHEMES";
$rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
while(lmbdb_fetch_row($rs)) {
    echo "<TR><TD>".lmbdb_result($rs, "NAME")."</TD><TD BGCOLOR=\"".lmbdb_result($rs, "WEB1")."\">&nbsp;&nbsp;&nbsp;&nbsp;</TD><TD BGCOLOR=\"".lmbdb_result($rs, "WEB2")."\">&nbsp;&nbsp;&nbsp;&nbsp;</TD><TD BGCOLOR=\"".lmbdb_result($rs, "WEB3")."\">&nbsp;&nbsp;&nbsp;&nbsp;</TD><TD BGCOLOR=\"".lmbdb_result($rs, "WEB4")."\">&nbsp;&nbsp;&nbsp;&nbsp;</TD><TD BGCOLOR=\"".lmbdb_result($rs, "WEB8")."\">&nbsp;&nbsp;&nbsp;&nbsp;</TD><TD BGCOLOR=\"".lmbdb_result($rs, "WEB3")."\">&nbsp;&nbsp;&nbsp;&nbsp;</TD><TD BGCOLOR=\"".lmbdb_result($rs, "WEB7")."\">&nbsp;&nbsp;&nbsp;&nbsp;</TD><TD BGCOLOR=\"".lmbdb_result($rs, "WEB8")."\">&nbsp;&nbsp;&nbsp;&nbsp;</TD></TR>";
}
?>
</TABLE>
</div>


<FORM ACTION="main.php" METHOD="post" NAME="form1">
<input type="hidden" name="action" value="user_change">
<input type="hidden" name="ID" value="<?=$ID?>">
<input type="hidden" name="user_change" value="1">
<input type="hidden" name="farbe_change">
<input type="hidden" name="lang_change">
<input type="hidden" name="fileview_change">
<input type="hidden" name="username" value="<?=$result_user["username"]?>">

<div class="lmbPositionContainerMain">

<?php
/* --- Userdaten --------------------------------------- */
echo "<TABLE class=\"tabfringe\" border=0 cellspacing=\"0\" cellpadding=\"0\"><TR><TD VALIGN=\"top\">";


if(!$session["change_pass"]){$redo = "readonly disabled";}

echo "<TABLE style=\"width:400px;\">";
echo "<TR class=\"tabHeader\"><TD class=\"tabHeaderItem\" COLSPAN=2><i class=\"lmb-icon lmb-user\" align=\"texttop\"></i>&nbsp;$lang[140]</TD></TR>";
echo "<TR class=\"tabBody\"><TD style=\"width:150px;\">$lang[519]</TD><TD><INPUT TYPE=TEXT style=\"width:150px;\" name=\"vorname\" readonly disabled VALUE=\"".$result_user["username"]."\"></TD></TR>";
echo "<TR class=\"tabBody\"><TD style=\"width:150px;\">$lang[142]</TD><TD><INPUT TYPE=TEXT style=\"width:150px;\" name=\"vorname\" $redo VALUE=\"".$result_user["vorname"]."\"></TD></TR>";
echo "<TR class=\"tabBody\"><TD style=\"width:150px;\">$lang[4]</TD><TD><INPUT TYPE=TEXT style=\"width:150px;\" name=\"name\" $redo VALUE=\"".$result_user["name"]."\"></TD></TR>";
echo "<TR class=\"tabBody\"><TD style=\"width:150px;\">$lang[144]</TD><TD><INPUT TYPE=TEXT style=\"width:150px;\" name=\"email\" $redo VALUE=\"".$result_user["email"]."\"></TD></TR>";
echo "<TR class=\"tabBody\"><TD style=\"width:150px;\">Tel</TD><TD><INPUT TYPE=TEXT style=\"width:150px;\" name=\"tel\" VALUE=\"".$result_user["tel"]."\"></TD></TR>";
echo "<TR class=\"tabBody\"><TD style=\"width:150px;\">Fax</TD><TD><INPUT TYPE=TEXT style=\"width:150px;\" name=\"fax\" VALUE=\"".$result_user["fax"]."\"></TD></TR>";
echo "<TR class=\"tabBody\"><TD style=\"width:150px;\">Position</TD><TD><INPUT TYPE=TEXT style=\"width:150px;\" name=\"position\" VALUE=\"".$result_user["position"]."\"></TD></TR>";
if($session["change_pass"]){echo "<TR><TD width=150>$lang[141]</TD><TD><INPUT TYPE=password style=\"width:150px;\" name=\"passwort\"></TD></TR>";}
if($session["change_pass"]){echo "<TR><TD width=150>$lang[1600]</TD><TD><INPUT TYPE=password style=\"width:150px;\" name=\"passwort2\" Onchange=\"check_pass(this.value)\"></TD></TR>";}
echo "<TR class=\"tabFooter\"><TD COLSPAN=\"2\"></TD></TR>";
echo "</TABLE>";
?>


<?php /* --- Allg. Einstellungen --------------------------------------------- */
echo "<TABLE style=\"width:400px;\">";
echo "<TR class=\"tabHeader\"><TD class=\"tabHeaderItem\" COLSPAN=2><i class=\"lmb-icon lmb-wrench-alt\" align=\"texttop\"></i>&nbsp;$lang[146]</TD></TR>";

/* --- Sprach Liste --------------------------------------------- */
echo "<TR class=\"tabBody\"><TD width=150>$lang[624]</TD><TD><SELECT STYLE=\"width:160px;\" NAME=\"language\" OnChange=\"this.form.lang_change.value='1';\">";
echo "<OPTION VALUE=\"-1\">system";
$sqlquery = "Select DISTINCT LANGUAGE,LANGUAGE_ID FROM LMB_LANG";
$rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
while(lmbdb_fetch_row($rs)) {
$langid = lmbdb_result($rs,"LANGUAGE_ID");
if($result_user["language"] == $langid){$SELECTED =  "SELECTED";}else {unset($SELECTED);}
echo "<OPTION VALUE=\"".urlencode($langid)."\" $SELECTED>".lmbdb_result($rs,"LANGUAGE");
}
echo "</SELECT></TD></TR>";

# Datumsformat
echo "<TR class=\"tabBody\"><TD width=150>$lang[2576]</TD><TD><SELECT STYLE=\"width:160px;\" name=\"dateformat\"><OPTION VALUE=1 "; if($result_user["dateformat"] == "1"){echo "SELECTED";} echo">deutsch<OPTION VALUE=2 ";  if($result_user["dateformat"] == "2"){echo "SELECTED";} echo">english<OPTION VALUE=3 "; if($result_user["dateformat"] == "3"){echo "SELECTED";} echo">us</SELECT></TD></TR>";

/* --- Farbschema Liste --------------------------------------------- */
echo "<TR class=\"tabBody\"><TD width=150>$lang[623]</TD><TD><SELECT STYLE=\"width:160px;\" name=\"farbe\" OnChange=\"this.form.farbe_change.value='1';\">";
$sqlquery = "SELECT * FROM LMB_COLORSCHEMES WHERE LOWER(NAME) LIKE '%".lmb_strtolower($result_user["layout"])."%' ORDER BY ID";
$rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
while(lmbdb_fetch_row($rs)) {
$farbid = lmbdb_result($rs,"ID");
if($result_user["farbschema"] == $farbid){$SELECTED =  "SELECTED";}else {unset($SELECTED);}
echo "<OPTION VALUE=\"".urlencode($farbid)."\" $SELECTED>".str_replace("(".$result_user["layout"].")","",lmbdb_result($rs,"NAME"));
}
echo "</SELECT></TD></TR>";

/* --- Layout Liste --------------------------------------------- */
echo "<TR class=\"tabBody\"><TD width=150>$lang[698]</TD><TD><SELECT STYLE=\"width:160px;\" NAME=\"layout\">";
if($path = read_dir($umgvar["pfad"]."/layout")){
if(!$result_user["layout"]){$result_user["layout"] = $umgvar["default_layout"];}
foreach($path["name"] as $key => $value){
	if($path["typ"][$key] == "dir"){
		if($result_user["layout"] == $value){
			$SELECTED =  "SELECTED";
		}else{
		 	unset($SELECTED);
		}
		echo "<OPTION VALUE=\"".$value."\" $SELECTED>".$value;
	}
}
}
unset($pfad);
echo "</SELECT></TD></TR>";

#echo "<TR class=\"tabBody\"><TD style=\"width:150px;\">$lang[704]</TD><TD><SELECT STYLE=\"width:160px;\" name=\"data_display\"><OPTION VALUE=1 "; if($result_user["data_display"] == "1"){echo "SELECTED";} echo">$lang[1246]<OPTION VALUE=2 ";  if($result_user['data_display'] == "2"){echo "SELECTED";} echo">$lang[1244]<OPTION VALUE=3 ";  if($result_user['data_display'] == "3"){echo "SELECTED";} echo">$lang[1245]</SELECT></TD></TR>";
if($result_user["symbolbar"]){$CHECKED = "CHECKED";}else{$CHECKED = "";}
echo "<TR class=\"tabBody\"><TD>$lang[2167]</TD><TD><INPUT TYPE=\"CHECKBOX\" NAME=\"symbolbar\" VALUE=\"1\" $CHECKED STYLE=\"border:none;background-color:{$farbschema['WEB8']}\"></TD></TR>";
echo "<TR class=\"tabFooter\"><TD COLSPAN=\"2\"></TD></TR>";
echo "</TABLE>";
?>





</TD><TD valign="top">


<?php
foreach ($gtab["table"] as $key => $value){
	if($gtab["typ"][$key] == 6){
		echo "<table style=\"width:400px;\">";
		echo "<tr class=\"tabHeader\"><td class=\"tabHeaderItem\" colspan=2><i class=\"lmb-icon lmb-mail\" align=\"texttop\"></i>&nbsp;".$gtab["desc"][$key]."</td></tr>";
		echo "<tr class=\"tabBody\"><td style=\"width:200px;\">".$lang[4]."</td><td><input type=\"text\" style=\"width:150px;\" name=\"e_setting[$key][full_name]\" value=\"".$result_user["e_setting"][$key]["full_name"]."\"></td></tr>";
		echo "<tr class=\"tabBody\"><td style=\"width:200px;\">".$lang[2519]."</td><td><input type=\"text\" style=\"width:150px;\" name=\"e_setting[$key][email_address]\" value=\"".$result_user["e_setting"][$key]["email_address"]."\"></td></tr>";
		echo "<tr class=\"tabBody\"><td style=\"width:200px;\">".$lang[2520]."</td><td><input type=\"text\" style=\"width:150px;\" name=\"e_setting[$key][reply_address]\" value=\"".$result_user["e_setting"][$key]["reply_address"]."\"></td></tr>";
		echo "<tr class=\"tabBody\"><td style=\"width:200px;\">".$lang[2521]."</td><td><input type=\"text\" style=\"width:150px;\" name=\"e_setting[$key][imap_hostname]\" value=\"".$result_user["e_setting"][$key]["imap_hostname"]."\"></td></tr>";
		echo "<tr class=\"tabBody\"><td style=\"width:200px;\">".$lang[2522]."</td><td><input type=\"text\" style=\"width:150px;\" name=\"e_setting[$key][imap_username]\" value=\"".$result_user["e_setting"][$key]["imap_username"]."\"></td></tr>";
		echo "<tr class=\"tabBody\"><td style=\"width:200px;\">".$lang[2524]."</td><td><input type=\"password\" style=\"width:150px;\" name=\"e_setting[$key][imap_password]\" value=\"".$result_user["e_setting"][$key]["imap_password"]."\"></td></tr>";
		echo "<tr class=\"tabBody\"><td style=\"width:200px;\">".$lang[2523]." (143)</td><td><input type=\"text\" style=\"width:150px;\" name=\"e_setting[$key][imap_port]\" value=\"".$result_user["e_setting"][$key]["imap_port"]."\"></td></tr>";
		#echo "<tr class=\"tabBody\"><td style=\"width:200px;\">smtp_hostname</td><td><input type=\"text\" name=\"e_setting[$key][arch_hostname]\" value=\"".$result_user["e_setting"][$key]["arch_hostname"]."\"></td></tr>";
		#echo "<tr class=\"tabBody\"><td style=\"width:200px;\">smtp_port</td><td><input type=\"text\" name=\"e_setting[$key][arch_port]\" value=\"".$result_user["e_setting"][$key]["arch_port"]."\"></td></tr>";
		#echo "<tr class=\"tabBody\"><td style=\"width:200px;\">smtp_username</td><td><input type=\"text\" name=\"e_setting[$key][arch_username]\" value=\"".$result_user["e_setting"][$key]["arch_username"]."\"></td></tr>";
		#echo "<tr class=\"tabBody\"><td style=\"width:200px;\">smtp_password</td><td><input type=\"password\" name=\"e_setting[$key][arch_password]\" value=\"".$result_user["e_setting"][$key]["arch_password"]."\"></td></tr>";
		#$tls = $result_user["e_setting"][$key]["smtp_encode"];
		#if($tls == 1){$tls_1 = "SELECTED";}elseif($tls == 2){$tls_2 = "SELECTED";}else{$tls_0 = "SELECTED";}
		#echo "<tr class=\"tabBody\"><td style=\"width:200px;\">SMTP-Verschlüsselung</td><td><select style=\"width:160px;\"  name=\"e_setting[$key][smtp_encode]\"><option $tls_0 value=\"0\">keine<option $tls_1 value=\"1\">TLS<option $tls_2 value=\"2\">SSL</select></td></tr>";
		#$ssl = $result_user["e_setting"][$key]["imap_encode"];
		#if($ssl == 1){$ssl_1 = "SELECTED";}elseif($ssl == 2){$ssl_2 = "SELECTED";}else{$ssl_0 = "SELECTED";}
		#echo "<tr class=\"tabBody\"><td style=\"width:200px;\">IMAP-Verschlüsselung</td><td><select style=\"width:160px;\"  name=\"e_setting[$key][imap_encode]\"><option $ssl_0 value=\"0\">keine<option $ssl_1 value=\"1\">TLS<option $ssl_2 value=\"2\">SSL</select></td></tr>";
		echo "</table>";
	}
}
?>






</TD></TR>

<TR><TD COLSPAN="2"><HR></TD></TR>

<TR><TD VALIGN="top">
<TABLE>
<TR><TD><INPUT TYPE="submit" value="<?=$lang[842]?>"></TD></TR>
</TABLE>
</TD></TR></TABLE>

</div>
</FORM>
