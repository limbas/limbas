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
 * ID: 132
 */

?>

<script language="JavaScript">
function newwin1(USERID) {
	tracking = open("main_admin.php?action=setup_user_tracking&typ=1&userid=" + USERID ,"Tracking","toolbar=0,location=0,status=0,menubar=0,scrollbars=1,resizable=1,width=600,height=600");
}

function newwin2(USERID) {
	userstat = open("main.php?action=kalender&userstat=" + USERID ,"userstatistic","toolbar=0,location=0,status=0,menubar=0,scrollbars=1,resizable=1,width=750,height=550");
}

function activate_user(USERID) {
	document.location.href = "main_admin.php?action=setup_user_change_admin&reactivate=1&ID="+USERID;
}

function select_all_user(el){
	var cc = null;

	var ar = document.getElementsByTagName("input");
	for (var i = ar.length; i > 0;) {
		cc = ar[--i];
		var ln = cc.name.split("[");
		if(cc.type == "checkbox" && ln[0] == 'edit_user'){
			if(el.checked){
				cc.checked = 1;
			}else{
				cc.checked = 0;
			}
		}
	}

}

</script>


<FORM ACTION="main_admin.php" METHOD="post" name="form1" TARGET="user_main">
<input type="hidden" name="action" VALUE="setup_user_erg">
<input type="hidden" name="ID" VALUE="<?=$ID?>">
<input type="hidden" name="order" VALUE="<?=$order?>">
<input type="hidden" name="group_id" VALUE="<?=$group_id?>">
<input type="hidden" name="lock">
<input type="hidden" name="logout">
<input type="hidden" name="debug">
<input type="hidden" name="logging">
<input type="hidden" name="user_del">
<input type="hidden" name="filter" VALUE="<?=$filter?>">
<input type="hidden" name="ufilter_user" VALUE="<?=$ufilter_user?>">
<input type="hidden" name="ufilter_vorname" VALUE="<?=$ufilter_vorname?>">
<input type="hidden" name="ufilter_name" VALUE="<?=$ufilter_name?>">
<input type="hidden" name="ufilter_group" VALUE="<?=$ufilter_group?>">


<div class="lmbPositionContainerMain small">


<TABLE cellspacing="1" cellpadding="1" style="width:100%">
<TR class="tabHeader">
<TD class="tabHeaderItem" ALIGN="LEFT" VALIGN="TOP"><INPUT TYPE="CHECKBOX" STYLE="border:none;background-color:transparent" onclick="select_all_user(this);"></TD>
<TD class="tabHeaderItem"><A HREF="Javascript:document.form1.order.value='LMB_USERDB.USERNAME';document.form1.submit();"><?=$lang[3]?></A></TD>
<TD class="tabHeaderItem"><?=$lang[4]?></TD>
<TD class="tabHeaderItem"><A HREF="Javascript:document.form1.order.value='LMB_GROUPS.NAME';document.form1.submit();"><?=$lang[561]?></TD>
<TD class="tabHeaderItem"><?=$lang[563]?></TD>
<?php /*<TD class="tabHeaderItem"><?=$lang[1249]?></TD>*/?>
<TD class="tabHeaderItem"><?=$lang[657]?></TD>
<TD class="tabHeaderItem"><?=$lang[911]?></TD>
<TD class="tabHeaderItem"><?=$lang[1250]?></TD>
<TD class="tabHeaderItem"><?=$lang[1791]?></TD>
<TD class="tabHeaderItem"></TD>
</TR>

<?php
/* --- Ergebnisliste --------------------------------------- */
$bzm = 1;

if($result_user["username"]){
foreach ($result_user["username"] as $bzm => $value){
	
	if(($ufilter == "activ" AND $result_user['aktiv'][$bzm]) OR $ufilter != "activ"){

		if($result_user["del"][$bzm]){
			?>
			<TR class="tabBody">
			<TD>&nbsp;</TD>
			<TD STYLE="color:red"><?=$result_user["username"][$bzm]?></TD>
			<TD STYLE="color:red;"><?=$result_user["vorname"][$bzm]?> <?=$result_user['name'][$bzm]?></TD>
			<TD STYLE="color:red;"><?=$result_user["gruppe"][$bzm]?></TD>
			<TD STYLE="color:red;"><?=$result_user["erstdatum"][$bzm]?></TD>
			<TD COLSPAN="2"></TD>
            <TD ALIGN="CENTER"><i class="lmb-icon lmb-history" STYLE="cursor:pointer" border="0" OnClick="newwin1('<?= $result_user["user_id"][$bzm] ?>')"></i></TD>
            <TD ALIGN="CENTER"><i class="lmb-icon lmb-calendar-alt2" STYLE="cursor:pointer" border="0" OnClick="newwin2('<?= $result_user["user_id"][$bzm] ?>')"></i></TD>
            <TD ALIGN="CENTER" TITLE="<?=$lang[1728]?>"><i class="lmb-icon lmb-action" STYLE="cursor:pointer;" OnClick="activate_user('<?=$result_user["user_id"][$bzm]?>')"></i></TD>
			</TR>
			<?php
		}else{
			if($filter != "activ" OR ($filter == "activ" AND $result_user["aktiv"][$bzm])){
			if($result_user["aktiv"][$bzm]){$usercolor = "green;";}elseif($result_user["gruppen_id"][$bzm] != $group_id AND $group_id){$usercolor = "grey;";}elseif($result_user["lock"][$bzm]){$usercolor = "red";}else{$usercolor = "";}
			?>
			
			<TR class="tabBody">
			
		    <TD ALIGN="LEFT"><INPUT TYPE="CHECKBOX" STYLE="border:none;background-color:transparent" NAME="edit_user[<?=$result_user["user_id"][$bzm]?>]" value="1"></TD>
			
			<TD><A HREF="main_admin.php?action=setup_user_change_admin&ID=<?= $result_user["user_id"][$bzm] ?>" STYLE="color:<?=$usercolor?>"><?= $result_user["username"][$bzm]; ?>&nbsp;</A></TD>
			<TD STYLE="color:<?=$usercolor?>"><?= $result_user["vorname"][$bzm]." ".$result_user["name"][$bzm] ?>&nbsp;</TD>
			<TD STYLE="color:<?=$usercolor?>"><?= $result_user["gruppe"][$bzm] ?>&nbsp;</TD>
			<TD STYLE="color:<?=$usercolor?>"><?= $result_user["erstdatum"][$bzm] ?>&nbsp;</TD>
			
			<?php /*
		    <TD ALIGN="CENTER">
		    <?php if($result_user["username"][$bzm] != 'admin'){?>
		    <INPUT TYPE="CHECKBOX" STYLE="border:none;background-color:transparent" OnClick="document.form1.logout.value='<?= $result_user["user_id"][$bzm] ?>';document.form1.submit();">
		    <?php }?>
		    </TD>
		    */?>
		    
		    <TD ALIGN="CENTER">
		    <?php if($result_user["username"][$bzm] != 'admin'){?>
		    <INPUT TYPE="CHECKBOX" STYLE="border:none;background-color:transparent" OnClick="document.form1.lock.value='<?= $result_user["user_id"][$bzm] ?>';document.form1.submit();" <?php if($result_user["lock"][$bzm] == 1){echo "CHECKED";}?>>
		    <?php }?>
		    </TD>
		    
		    <TD ALIGN="CENTER">
		    <?php if($result_user["username"][$bzm] != 'admin' AND $session['username'] == 'admin'){?>
		    <INPUT TYPE="CHECKBOX" STYLE="border:none;background-color:transparent" OnClick="document.form1.debug.value='<?= $result_user['user_id'][$bzm] ?>';document.form1.submit();" <?php if($result_user["debug"][$bzm] == 1){echo "CHECKED";}?>>
		    <?php }?>
		    </TD>
		    
                    <TD ALIGN="CENTER"><i class="lmb-icon lmb-history" STYLE="cursor:pointer" border="0" OnClick="newwin1('<?= $result_user["user_id"][$bzm] ?>')"></i></TD>
                    <TD ALIGN="CENTER"><i class="lmb-icon lmb-calendar-alt2" STYLE="cursor:pointer" border="0" OnClick="newwin2('<?= $result_user["user_id"][$bzm] ?>')"></i></TD>
		    <TD></TD>
			</TR>
			<?php
			}
		}
	}
	$bzm++;
}
}
?>
<TR class="tabBody"><TD colspan="10">&nbsp;</TD></TR>

<TR class="tabBody"><TD COLSPAN="10">
<INPUT TYPE="RADIO" OnClick="document.form1.submit();" STYLE="border:none;background-color:transparent" NAME="ufilter" VALUE="" <?php if(!$ufilter){echo "CHECKED";}?>>&nbsp;<?=$lang[1790]?>
&nbsp;&nbsp;<INPUT TYPE="RADIO" OnClick="document.form1.submit();" STYLE="border:none;background-color:transparent" NAME="ufilter" VALUE="activ" <?php if($ufilter == "activ"){echo "CHECKED";}?>>&nbsp;<?=$lang[1789]?>
&nbsp;&nbsp;<INPUT TYPE="RADIO" OnClick="document.form1.submit();" STYLE="border:none;background-color:transparent" NAME="ufilter" VALUE="lock" <?php if($ufilter == "lock"){echo "CHECKED";}?>>&nbsp;<?=$lang[1793]?>
&nbsp;&nbsp;<INPUT TYPE="RADIO" OnClick="document.form1.submit();" STYLE="border:none;background-color:transparent" NAME="ufilter" VALUE="viewdel" <?php if($ufilter == "viewdel"){echo "CHECKED";}?>>&nbsp;<?=$lang[1687]?>
</TD></TR>

<TR class="tabBody"><TD colspan="10">&nbsp;</TD></TR>

<TR class="tabBody"><TD COLSPAN="10">
<input type="submit" name="send_message" value="<?=$lang[2463]?>">
</TD></TR>

<TR class="tabFooter"><TD colspan="10">&nbsp;</TD></TR>
</TABLE>

</div>
<FORM>
