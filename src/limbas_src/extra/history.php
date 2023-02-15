<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */



if(!$history_typ){$history_typ = 1;}
?>

<Script language="JavaScript">
function newwin(GTAB,ID) {
	spalte = open("main.php?action=gtab_change&ID=" + ID + "&gtabid=" + GTAB + "" ,"Datensatzdetail","toolbar=0,location=0,status=0,menubar=0,scrollbars=1,resizable=0,width=700,height=600");
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
<?php if($history_typ == 1){$class="tabpoolItemActive";}else{$class="tabpoolItemInactive";}?>
<TD nowrap class="<?=$class?>" OnClick="document.form1.history_typ.value=1;document.form1.submit();"><?=$lang[1250]?></TD>
<?php if($history_typ == 2){$class="tabpoolItemActive";}else{$class="tabpoolItemInactive";}?>
<TD nowrap class="<?=$class?>" OnClick="document.form1.history_typ.value=2;document.form1.submit();"><?=$lang[2498]?></TD>
<TD class="tabpoolItemSpace">&nbsp;</TD>
</TR></TABLE>

</TD></TR>
	
<TR><TD class="tabpoolfringe">
	
<TABLE BORDER="0" cellspacing="1" cellpadding="2" width="100%" class="tabBody">

<?php
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
<TD VALIGN="TOP" class="tabHeaderItem" OnClick="document.form1.order.value=1;document.form1.submit();"><?=$lang[197]?></TD>
<TD VALIGN="TOP" class="tabHeaderItem" OnClick="document.form1.order.value=2;document.form1.submit();"><?=$lang[3]?></TD>
<TD VALIGN="TOP" class="tabHeaderItem" OnClick="document.form1.order.value=3;document.form1.submit();"><?=$lang[168]?></TD>
<TD VALIGN="TOP" class="tabHeaderItem"><?=$lang[29]?></TD>
</TR>


<TR><TD><input type="text" style="width:120px;" name="s_date" value="<?=$s_date?>" OnChange="document.form1.submit();"></TD>
<TD><input type="text" style="width:120px" name="s_user" value="<?=$s_user?>" OnChange="document.form1.submit();"></TD>
<TD><input type="text" style="width:120px" name="s_field" value="<?=$s_field?>" OnChange="document.form1.submit();"></TD>
<TD></TD></TR>


<?php
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
                $lid = trim(lmb_substr($value1,1,16));
                $value1 = preg_replace("/^[<]{1}/","<b style=\"color:red\">< </b>",$value1);
                $value1 = preg_replace("/^[>]{1}/","<b style=\"color:green\"> ></b>",$value1);
                $val[] = "<A href=\"#\" OnClick=\"newwin('".$gfield[$result_history["tabid"][$key]]["verkntabid"][$result_history["fieldid"][$key]]."','".$lid."');\">".$value1."</A>";
            }
            echo implode("<br>",$val);
    	}
    }else{
        echo nl2br($result_history["fieldvalue"][$key]);
    }
    echo "</TD>\n";
    echo "</TR>\n";
}}
?>
</TABLE>


</div>


</FORM>

