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
 * ID: 84
 */

if(!$history_typ){$history_typ = 1;}
?>

<Script language="JavaScript">
function newwin(GTAB,ID) {
	spalte = open("main.php?<?=SID?>&action=gtab_change&ID=" + ID + "&gtabid=" + GTAB + "" ,"Datensatzdetail","toolbar=0,location=0,status=0,menubar=0,scrollbars=1,resizable=0,width=700,height=600");
}
</Script>


<FORM ACTION="main.php" METHOD="post" NAME="form1">
<input type="hidden" name="action" value="history">
<input type="hidden" name="ID" value="<?=$ID?>">
<input type="hidden" name="tab" value="<?=$tab?>">
<input type="hidden" name="wfid" value="<?=$wfid?>">
<input type="hidden" name="history_typ" value="<?=$history_typ?>">
<input type="hidden" name="breakid" value="<?=$breakid?>">
<input type="hidden" name="startid" value="<?=$startid?>">
<input type="hidden" name="previd" value="<?=$previd?>">
<input type="hidden" name="gonext">
<input type="hidden" name="order">

<DIV class="lmbPositionContainerMain">

<TABLE class="tabfringe" BORDER="0" width="100%" cellspacing="0" cellpadding="0"><TR><TD>

<TABLE BORDER="0" cellspacing="0" cellpadding="0"><TR>
<?if($history_typ == 1){$class="tabpoolItemActive";}else{$class="tabpoolItemInactive";}?>
<TD nowrap class="<?=$class?>" OnClick="document.form1.history_typ.value=1;document.form1.submit();"><?=$lang[2497]?></TD>
<?if($history_typ == 2){$class="tabpoolItemActive";}else{$class="tabpoolItemInactive";}?>
<TD nowrap class="<?=$class?>" OnClick="document.form1.history_typ.value=2;document.form1.submit();"><?=$lang[2498]?></TD>
<TD class="tabpoolItemSpace">&nbsp;</TD>
</TR></TABLE>

</TD></TR>
	
<TR><TD class="tabpoolfringe">
	
<TABLE BORDER="0" cellspacing="1" cellpadding="2" width="100%" class="tabBody">

<?
if($history_typ == 2){
	echo "<TR class=\"tabSubHeader\">
	<TD class=\"tabSubHeaderItem\" COLSPAN=\"3\" ALIGN=\"CENTER\">&nbsp;&nbsp;<i class=\"lmb-icon lmb-arrow-left\" OnClick=\"document.form1.gonext.value=1;document.form1.submit();\"></i>&nbsp;&nbsp;".get_date($breakstamp,3)."&nbsp;&nbsp;";
	if($breakstamp){
		echo "<i class=\"lmb-icon lmb-arrow-right\" OnClick=\"document.form1.gonext.value=2;document.form1.submit();\"></i>";
	}
	echo "</TD></TR>";
}
?>

</TABLE>

<TABLE BORDER="0" cellspacing="1" cellpadding="2" width="100%">
<TR class="tabHeader">
<TD VALIGN="TOP" class="tabHeaderItem" OnClick="document.form1.order.value=1;document.form1.submit();"><?=$lang[288]?></TD>
<TD VALIGN="TOP" class="tabHeaderItem" OnClick="document.form1.order.value=2;document.form1.submit();"><?=$lang[289]?></TD>
<TD VALIGN="TOP" class="tabHeaderItem" OnClick="document.form1.order.value=3;document.form1.submit();"><?=$lang[290]?></TD>
<TD VALIGN="TOP" class="tabHeaderItem"><?=$lang[291]?></TD>
</TR>


<TR><TD><input type="text" style="width:120px;" name="s_date" value="<?=$s_date?>" OnChange="document.form1.submit();"></TD>
<TD><input type="text" style="width:120px" name="s_user" value="<?=$s_user?>" OnChange="document.form1.submit();"></TD>
<TD><input type="text" style="width:120px" name="s_field" value="<?=$s_field?>" OnChange="document.form1.submit();"></TD>
<TD></TD></TR>


<?
/* --- Ergebnisliste --------------------------------------- */
if($result_history["id"]){
foreach($result_history["id"] as $key => $value){
	unset($val);
    echo "<TR class=\"tabBody\">\n";
	echo "<TD NOWRAP VALIGN=\"TOP\" width=\"20\">".$result_history["editdatum"][$key]."&nbsp;</TD>\n";
    echo "<TD NOWRAP VALIGN=\"TOP\" width=\"20\">".$userdat["bezeichnung"][$result_history["user"][$key]]."&nbsp;</TD>\n";
    echo "<TD NOWRAP VALIGN=\"TOP\" width=\"20\">".$result_history["field"][$key]."&nbsp;</TD>\n";
    echo "<TD VALIGN=\"TOP\">";
    if($result_history["field_type"][$key] == 11){
    	$links = explode(";",$result_history["fieldvalue"][$key]);
    	if($links){
    	foreach($links as $key1 => $value1){
    		$lid = trim(substr($value1,1,16));
    		$value1 = preg_replace("/^[<]{1}/","<b style=\"color:red\">< </b>",$value1);
    		$value1 = preg_replace("/^[>]{1}/","<b style=\"color:green\"> ></b>",$value1);
    		$val[] = "<A href=\"#\" OnClick=\"newwin('".$gfield[$result_history["tabid"][$key]]["verkntabid"][$result_history["fieldid"][$key]]."','".$lid."');\">".$value1."</A>";
    	}
    	echo implode("<br>",$val);
    	}
    }else{
    	echo $result_history["fieldvalue"][$key];
    }
    echo "</TD>\n";
    echo "</TR>\n";
}}
?>
</TABLE>


</div>


</FORM>

