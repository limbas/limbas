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
 * ID: 195
 */
?>


<script language="JavaScript">

function checkall(el){
	var ar = document.getElementsByTagName("input");
	for (var i = ar.length; i > 0;) {
		cc = ar[--i];
		if(cc.name.substr(0,8) == 'useindex'){
			if(el.checked){
				cc.checked = 1;
			}else{
				cc.checked = 0;
			}
		}
	}
}

function ind_sort(val){
	document.form1.ind_sort.value=val;
	document.form1.submit();
}

function LIM_deactivate(elid){
	document.getElementById("tab"+elid).style.display = 'none';
}


function LIM_activate(el,elid){

	LIM_deactivate('1');
	LIM_deactivate('2');
	LIM_deactivate('3');
	LIM_deactivate('4');
	
	if(!el){el = document.getElementById('menu'+elid);}

	limbasSetLayoutClassTabs(el,'tabpoolItemInactive','tabpoolItemActive');
	document.getElementById("tab"+elid).style.display = '';

}

</Script>


<FORM ACTION="main_admin.php" METHOD="post" NAME="form1">
<input type="hidden" name="action" value="setup_index">
<input type="hidden" name="ind_sort" value="<?=$ind_sort;?>">



<div class="lmbPositionContainerMainTabPool">


<TABLE BORDER="0" width="650" cellspacing="0" cellpadding="0" class="tabpool"><TR><TD>

<TABLE BORDER="0" cellspacing="0" cellpadding="0"><TR class="tabpoolItemTR">
<TD nowrap ID="menu1" OnClick="LIM_activate(this,'1')" class="tabpoolItemActive"><?=$lang[2723]?></TD>
<TD nowrap ID="menu2" OnClick="LIM_activate(this,'2')" class="tabpoolItemInactive"><?=$lang[2725]?></TD>
<TD nowrap ID="menu3" OnClick="LIM_activate(this,'3')" class="tabpoolItemInactive"><?=$lang[2729]?></TD>
<TD nowrap ID="menu3" OnClick="LIM_activate(this,'4')" class="tabpoolItemInactive"><?=$lang[2724]?></TD>
<TD class="tabpoolItemSpace">&nbsp;</TD>
</TR></TABLE>

</TD></TR>

<TR><TD class="tabpoolfringe">


<?php

# rebuild index
if(($rebuild OR $delete) AND $useindex){
	foreach ($useindex as $indname => $value){
		$indspec = explode('#',$value);
		$indt = dbf_4($indspec[0]);
		$indf = dbf_4($indspec[1]);
		$indname = dbf_4($indname);
		if(!$indname OR !$indt OR !$indf){continue;}
		# drop index
		$sqlquery = dbq_5(array($DBA["DBSCHEMA"],$indname,$indt));
		$rs = odbc_exec($db,$sqlquery) or errorhandle(odbc_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
		if($rs){lmb_alert("index $indname deleted");}
		
		if($rebuild){
			# create index
			$indname = dbf_4("LMBINDV_".lmb_substr(md5($indt."_".$indf),0,12));
			$sqlquery = dbq_4(array($DBA["DBSCHEMA"],$indname,$indt,$indf));
			$rs = odbc_exec($db,$sqlquery) or errorhandle(odbc_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
			if($rs){lmb_alert("index $indname created");}
		}
	}
}


/* -------- indexes --------*/
echo "<TABLE ID=\"tab1\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" class=\"tabBody\">";

# get indexes
$sqlquery = dbq_2(array($DBA["DBSCHEMA"],null,null,1));
$rs = odbc_exec($db,$sqlquery) or errorhandle(odbc_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
$bzm = 1;
while(odbc_fetch_row($rs)) {
	$ind["name"][] = odbc_result($rs,"INDEXNAME");
	$ind["table"][] = odbc_result($rs,"TABLENAME");
	$ind["used"][] = odbc_result($rs,"INDEX_USED");
	$ind["column"][] = odbc_result($rs,"COLUMNNAME");
	$ind["unique"][] = odbc_result($rs,"IS_UNIQUE");
	$bzm1++;
}
$ind["key"] = $ind["name"];

if($ind_sort == "name"){
	asort($ind["name"]);
	$ind["key"] = $ind["name"];
}elseif($ind_sort == "table"){
	asort($ind["table"]);
	$ind["key"] = $ind["table"];
}elseif($ind_sort == "column"){
	asort($ind["column"]);
	$ind["key"] = $ind["column"];
}elseif($ind_sort == "used"){
	asort($ind["used"]);
	$ind["key"] = $ind["used"];
}elseif($ind_sort == "unique"){
	asort($ind["unique"]);
	$ind["key"] = $ind["unique"];
}

echo "<TR class=\"tabHeader\">
<TD class=\"tabHeaderItem\"><a href=\"#\" onclick=\"ind_sort('name')\">$lang[4]</a></TD>
<TD class=\"tabHeaderItem\"><a href=\"#\" onclick=\"ind_sort('table')\">$lang[164]</a></TD>
<TD class=\"tabHeaderItem\"><a href=\"#\" onclick=\"ind_sort('column')\">$lang[168]</a></TD>
<TD align=\"center\" class=\"tabHeaderItem\"><a href=\"#\" onclick=\"ind_sort('unique')\">unique</a></TD>
<TD align=\"center\" class=\"tabHeaderItem\"><a href=\"#\" onclick=\"ind_sort('used')\">$lang[1856]</a></TD>
<TD align=\"center\"><input style=\"margin:0px\" type=\"checkbox\" onclick=\"checkall(this)\"></TD>
</TR>";

if($ind["name"]){

	foreach ($ind["key"] as $key => $value){
			echo "<TR class=\"tabBody\">
			<TD> ".$ind["name"][$key]." </TD>
			<TD> ".$ind["table"][$key]." </TD>
			<TD> ".$ind["column"][$key]." </TD>
			<TD align=\"center\"> ".$ind["unique"][$key]." </TD>
			<TD align=\"center\"> ".$ind["used"][$key]." </TD>
			<TD align=\"center\">";
			#if(lmb_strpos($ind["name"][$key],"lmbconst_") === false){
				echo "<input style=\"margin:0px\" type=\"checkbox\" name=\"useindex[".$ind["name"][$key]."]\" value=\"".$ind["table"][$key]."#".$ind["column"][$key]."\">";
			#}
			echo "</TD>
			</TR>";
	}
	echo "<TR><TD COLSPAN=\"9\" class=\"tabFooter\">&nbsp;</TD></TR>";
	echo "<TR><TD colspan=\"6\"><hr><input type=\"submit\" value=\"".$lang[1858]."\" name=\"rebuild\">&nbsp;&nbsp;&nbsp;<input type=\"submit\" value=\"".$lang[160]."\" name=\"delete\"></TD></TR>";
}

echo "</TABLE>";




/* -------- foreign keys --------*/
echo "<TABLE ID=\"tab2\" width=\"100%\" cellspacing=\"0\" cellpadding=\"1\" class=\"tabBody\" style=\"display:none;\">";
echo "<TR class=\"tabHeader\"><TD class=\"tabHeaderItem\">".$lang[4]."</TD><TD class=\"tabHeaderItem\">".$lang[164]."</TD><TD class=\"tabHeaderItem\">".$lang[168]."</TD><TD class=\"tabHeaderItem\">".$lang[2727]."</TD><TD class=\"tabHeaderItem\">".$lang[2728]."</TD></TR>";

# get foreign keys
$fkey = lmb_getForeignKeys();

if($fkey["keyname"]){
	foreach ($fkey["keyname"] as $key => $value){
		echo "<TR class=\"tabBody\">
		<TD> ".$fkey["keyname"][$key]." </TD>
		<TD> ".$fkey["tablename"][$key]." </TD>
		<TD> ".$fkey["columnname"][$key]." </TD>
		<TD> ".$fkey["reftablename"][$key]." </TD>
		<TD> ".$fkey["refcolumnname"][$key]." </TD>
		</TR>";
	}
	echo "<TR><TD COLSPAN=\"9\" class=\"tabFooter\"></TD></TR>";
}


echo "</TABLE>";




/* -------- primary keys --------*/
echo "<TABLE ID=\"tab3\" width=\"100%\" cellspacing=\"0\" cellpadding=\"1\" class=\"tabBody\" style=\"display:none;\">";
echo "<TR class=\"tabHeader\"><TD class=\"tabHeaderItem\">".$lang[4]."</TD><TD class=\"tabHeaderItem\">".$lang[164]."</TD><TD class=\"tabHeaderItem\">".$lang[168]."</TD></TR>";

# get primary keys
$pkey = dbq_23(array($DBA["DBSCHEMA"]));

if($pkey["PK_NAME"]){
	foreach ($pkey["PK_NAME"] as $key => $value){
		echo "<TR class=\"tabBody\">
		<TD> ".$pkey["PK_NAME"][$key]." </TD>
		<TD> ".$pkey["TABLE_NAME"][$key]." </TD>
		<TD> ".$pkey["COLUMN_NAME"][$key]." </TD>
		</TR>";
	}
	echo "<TR><TD COLSPAN=\"9\" class=\"tabFooter\"></TD></TR>";
}


echo "</TABLE>";




/* -------- unique constraints --------*/
echo "<TABLE ID=\"tab4\" width=\"100%\" cellspacing=\"0\" cellpadding=\"1\" class=\"tabBody\" style=\"display:none;\">";
echo "<TR class=\"tabHeader\"><TD class=\"tabHeaderItem\">".$lang[4]."</TD><TD class=\"tabHeaderItem\">".$lang[164]."</TD><TD class=\"tabHeaderItem\">".$lang[168]."</TD></TR>";

# get unique constraints
$constr = dbq_26(array($DBA["DBSCHEMA"]));

if($constr["TABLE_NAME"]){
	foreach ($constr["TABLE_NAME"] as $key => $value){
		echo "<TR class=\"tabBody\">
		<TD> ".$constr["PK_NAME"][$key]." </TD>
		<TD> ".$constr["TABLE_NAME"][$key]." </TD>
		<TD> ".$constr["COLUMN_NAME"][$key]." </TD>
		</TR>";
	}
	echo "<TR><TD COLSPAN=\"9\" class=\"tabFooter\"></TD></TR>";
}

echo "</TABLE>";

?>

<TR><TD>
</TABLE>

</div>
</FORM>