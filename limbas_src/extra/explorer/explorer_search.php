<?php
/*
 * Copyright notice
 * (c) 1998-2018 Limbas GmbH(support@limbas.org)
 * All rights reserved
 * This script is part of the LIMBAS project. The LIMBAS project is free software; you can redistribute it and/or modify it on 2 Ways:
 * Under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * Or
 * In a Propritary Software Licence http://limbas.org
 * The GNU General Public License can be found at http://www.gnu.org/copyleft/gpl.html.
 * A copy is found in the textfile GPL.txt and important notices to the license from the author is found in LICENSE.txt distributed with these scripts.
 * This script is distributed WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * This copyright notice MUST APPEAR in all copies of the script!
 * Version 3.5
 */

/*
 * ID:
 */

# filter ID
if($ffilter['globfilter']){$fid = 0;}else{$fid = $LID;}
?>



<script language="JavaScript">

// ---------------- Sendkeypress----------------------
function sendkeydown(evt) {
	if(evt.keyCode == 13){
		if(browser_ie){
			window.focus();
		}else{
			document.form1.LID.focus();
		}
		send_form();
	}
}

// --- Formular senden ----------------------------------
function send_form() {
	if(opener.parent.explorer_main){
		document.form1.target = 'explorer_main';
	}else if(opener.parent.main){
		document.form1.target = 'main';
	}
	document.form1.submit();
}

function clearall(){
	var cc = null;
	
	var ar = document.getElementsByTagName("input");
	for (var i = ar.length; i > 0;) {
		cc = ar[--i];
		var cid = cc.name;
		if(cc.type == "text" && cid.substr(0,2) == 'fs'){
			cc.value = '';
		}
	}
}

</script>






<TABLE BORDER="0" cellspacing="0" cellpadding="2" WIDTH="120%">
<FORM ACTION="main.php" METHOD="post" name="form1" TARGET="explorer_main">
<input type="hidden" name="action" value="explorer_main">

<INPUT TYPE="hidden" NAME="typ" VALUE="<?=$typ?>">
<INPUT TYPE="hidden" NAME="LID" VALUE="<?=$LID?>">
<input type="hidden" name="ID" value="<?=$ID?>">
<input type="hidden" name="old_action" value="<?=$old_action?>">
<input type="hidden" name="form_id" VALUE="<?=$form_id;?>">
<input type="hidden" name="gtabid" value="<?=$gtabid;?>">
<INPUT TYPE="hidden" NAME="verknpf" VALUE="<?=$verknpf;?>">
<input type="hidden" name="verkn_addfrom" VALUE="<?=$verkn_addfrom;?>">
<input type="hidden" name="verkn_ID" VALUE="<?=$verkn_ID;?>">
<input type="hidden" name="verkn_tabid" VALUE="<?=$verkn_tabid;?>">
<input type="hidden" name="verkn_fieldid" VALUE="<?=$verkn_fieldid;?>">
<input type="hidden" name="verkn_showonly" VALUE="<?=$verkn_showonly;?>">
<INPUT TYPE="hidden" NAME="reset">

<input type="hidden" name="f_fieldid" VALUE="<?=$f_fieldid?>">
<input type="hidden" name="f_tabid" VALUE="<?=$f_tabid?>">
<input type="hidden" name="f_datid" VALUE="<?=$f_datid?>">

<INPUT TYPE="hidden" NAME="ffilter_sub">

<TR STYLE="background-color:<?=$farbschema['WEB7']?>;height:20px;">
    <TD COLSPAN="4"><i class="lmb-icon lmb-folder-open" align="top"></i>&nbsp;&nbsp;<INPUT TYPE="TEXT" STYLE="border:1px solid <?=$farbschema['WEB4']?>;width:550px;height:16px;color:FFFFFF;" VALUE="<?=$file_url?>"></TD></TR>
<TR><TD COLSPAN="4" STYLE="height:20px">&nbsp;</TD></TR>


<TR><TD width="20">&nbsp;</TD><TD VALIGN="TOP">
<TABLE BORDER="0" cellspacing="0" cellpadding="2" width="80%">

<?php
foreach ($gfile['id'] as $key => $val){
	
	# Sonderfelder überspringen
	if($gtab["argresult_id"]["FILES"]."_34" == $key1 OR $gtab["argresult_id"]["FILES"]."_36" == $key1){echo "<TD></TD>";continue;}	
	
	if($gfile["tabid"][$key] != $prev_tabid){
		echo "<TR><TD colspan=\"2\" bgcolor=\"".$farbschema["WEB3"]."\"><B>".$gtab["desc"][$gfile["tabid"][$key]]."</B></TD></TR>";
	}

	if(!$prev_tabid){
		echo "<TR><TD COLSPAN=\"2\" bgcolor=\"".$farbschema["WEB7"]."\"><B>$lang[1634]</B></TD></TR>";
	}
	
	if($gfile['field_type'][$key] == 100){
		echo "<TR><TD colspan=\"2\" title=\"".$gfile['desc'][$key]."\" bgcolor=\"".$farbschema["WEB7"]."\"><B>".$gfile['title'][$key]."</B></TD></TR>";	
	}else{
		echo "<TR TITLE=\"".$gfile['desc'][$key]."\"><TD STYLE=\"cursor:help;width:30%\" VALIGN=\"TOP\">".$gfile['title'][$key].": </TD><TD STYLE=\"width:70%\"><INPUT TYPE=\"TEXT\" STYLE=\"width:95%\" ID=\"fs[$key][$fid][0]\" NAME=\"fs[$key][$fid][0]\" VALUE=\"".$ffilter[$key][$fid][0]."\" MAXLENGTH=\"".$gfile['size'][$key]."\"></TD></TR>\n";
	}
	$prev_tabid = $gfile["tabid"][$key];
}

echo "<TR><TD COLSPAN=\"2\"><B>$lang[1924]</B></TD></TR>\n";
# Sub-Ordner
if($ffilter['sub'][$fid]){$checked = "CHECKED";}else{$checked = "";}
echo "<TR><TD STYLE=\"cursor:help\" VALIGN=\"TOP\">".$lang[1240].": </TD><TD><INPUT TYPE=\"CHECKBOX\" $checked OnClick=\"if(this.checked){document.form1.ffilter_sub.value='true';}else{document.form1.ffilter_sub.value='false';}\"></TD></TR>";

?>
</TD></TR></TABLE>
</TD></TR>

<TR><TD COLSPAN="2" STYLE="height:10px">&nbsp;</TD></TR>
<TR><TD width="20">&nbsp;</TD><TD VALIGN="TOP">
<INPUT TYPE="BUTTON" OnCLick="document.form1.reset.value='0';send_form();" VALUE="<?=$lang[30]?>" STYLE="background-color:<?=$farbschema['WEB7']?>">
<INPUT TYPE="BUTTON" OnCLick="document.form1.reset.value='1';clearall();send_form();" VALUE="<?=$lang[1571]?>" STYLE="background-color:<?=$farbschema['WEB7']?>;width:90px">
</TD></TR>






</FORM></TABLE>