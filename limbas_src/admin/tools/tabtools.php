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
 * ID: 155
 */
?>

<DIV class="lmbPositionContainerMain">

<TABLE class="tabfringe" BORDER="0" cellspacing="1" cellpadding="0">

<?/* --- Tabellenliste ------------------------------- */?>
<FORM ACTION="main_admin.php" METHOD="post" name="form2">
<input type="hidden" name="<?echo $_SID;?>" value="<?echo session_id();?>">
<INPUT TYPE="hidden" NAME="action" VALUE="setup_tabtools">
<INPUT TYPE="hidden" NAME="empty">
<INPUT TYPE="hidden" NAME="delete">

<TR class="tabHeader"><TD class="tabHeaderItem" HEIGHT="20" COLSPAN="5"><B><?=$lang[1059]?></B></TD></TR>
<TR class="tabBody"><TD><SELECT NAME="table" STYLE="width:200px;">
<?php
$odbc_table = dbf_20(array($DBA["DBSCHEMA"],null,"'TABLE','VIEW'"));
foreach($odbc_table["table_name"] as $tkey => $tvalue) {
	$domaintables["tablename"][] = $odbc_table["table_name"][$tkey];
	$domaintables["owner"][] = $odbc_table["table_owner"][$tkey];
	$domaintables["type"][] = $odbc_table["table_type"][$tkey];
	if($table == $odbc_table["table_name"][$tkey]){$SELECTED = "SELECTED";}else{$SELECTED = "";}
	if(strtoupper($odbc_table["table_type"][$tkey]) == "VIEW"){$val = "VIEW :: ".$odbc_table["table_name"][$tkey];}else{$val = $odbc_table["table_name"][$tkey];}
	echo "<OPTION VALUE=\"".$odbc_table["table_name"][$tkey]."\" $SELECTED>".$val."\n";
}
?>
<TD ALIGN="LEFT">&nbsp;&nbsp;<INPUT TYPE="submit" VALUE="info" NAME="info">&nbsp;</TD>
<TD ALIGN="LEFT"><INPUT TYPE="submit" VALUE="<?=$lang[1061]?>" NAME="show">&nbsp;</TD>
<TD ALIGN="LEFT"><INPUT TYPE="button" onclick="if(confirm('<?=$lang[2153]?>')){document.form2.empty.value=1;document.form2.submit();}" VALUE="<?=$lang[1062]?>" STYLE="COLOR:orange">&nbsp;</TD>
<TD ALIGN="LEFT"><INPUT TYPE="button" onclick="if(confirm('<?=$lang[2287]?>')){document.form2.delete.value=1;document.form2.submit();}" VALUE="<?=$lang[1063]?>" STYLE="COLOR:red">&nbsp;</TD>
</TR>

<TR><TD COLSPAN="5">&nbsp;</TD></TR>

<TR class="tabHeader"><TD class="tabHeaderItem" HEIGHT="20" COLSPAN="5"><B><?=$lang[1060]?></B></TD></TR>
<TR class="tabBody"><TD ALIGN="LEFT">
<SELECT NAME="domaintable" STYLE="width:200px;">
<?
foreach ($DBA["DOMAINTABLE"] as $key => $val){
	echo "<OPTION VALUE=\"".$DBA["DOMAINSCHEMA"][$key].".".$DBA["DOMAINTABLE"][$key]."\">".$DBA["DOMAINTABLE"][$key];
}
?>
</SELECT></TD>
<TD>&nbsp;&nbsp;</TD><TD><INPUT TYPE="submit" VALUE="<?=$lang[1064]?>" NAME="showsys"></TD><TD colspan="2"></TD>
</TR>

<TR><TD COLSPAN="5">&nbsp;</TD></TR>

<TR class="tabBody"><TD class="tabHeaderItem" HEIGHT="20" COLSPAN="5"><B>SQL-Query</B></TD></TR>
<TR class="tabBody"><TD COLSPAN="5"><TEXTAREA NAME="sqlvalue" STYLE="width:600px;height:300px;"><?echo $sqlvalue;?></TEXTAREA></TD></TR>
<TR><TD colspan="5"><INPUT TYPE="submit" VALUE="<?=$lang[1065]?>" NAME="sqlexec"> <div style="float:right"><?=$lang[2770]?>: <input type="text" NAME="sqlexecnum" style="width:50px" value="<?=$sqlexecnum?>"></div></TD></TR>
<TR><TD class="tabFooter" colspan="5"></TR>
</FORM>
</TABLE>
<BR>
<?echo $result;
if($rssql AND strtoupper(substr($sqlvalue,0,6)) == "SELECT"){echo ODBCResourceToHTML($rssql,"cellpadding=\"2\" cellspacing=\"0\" style=\"border-collapse:collapse;\"","style=\"border: 1px solid grey;\"",$sqlexecnum);}


$zeit0 = gettime();

if($show AND $table){
        echo "<H3>".$table."</H3><BR>";
        $sqlquery = "SELECT * FROM $table";
        $rs = odbc_exec($db,$sqlquery) or errorhandle(odbc_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
        if($rs){echo ODBCResourceToHTML($rs,"cellpadding=\"2\" cellspacing=\"0\" style=\"border-collapse:collapse;\"","style=\"border: 1px solid grey;\"",$sqlexecnum);}
}
if($info AND $table){
	echo "<H3>".$table."</H3><BR>";
	$rs = dbf_5(array($DBA["DBSCHEMA"],$table,null,1));
	odbc_result_all($rs);

}

if($showsys AND $domaintable){
        echo "<H3>".$system."</H3><BR>";
        $sqlquery = "SELECT * FROM $domaintable";
        $rs = odbc_exec($db,$sqlquery) or errorhandle(odbc_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
        if($rs){echo ODBCResourceToHTML($rs,"cellpadding=\"2\" cellspacing=\"0\" style=\"border-collapse:collapse;\"","style=\"border: 1px solid grey;\"",$sqlexecnum);}
}
if(!$table AND !$showsys){
        echo "<TABLE BORDER=\"0\" cellspacing=\"1\" cellpadding=\"0\" WIDTH=\"500\">";
        echo "<TR class=\"tabHeader\"><TD>$lang[1066]</TD><TD>$lang[1067]</TD><TD>$lang[1068]</TD></TR>";
        $bzm = 0;
        while($domaintables[tablename][$bzm]) {
                echo "<TR><TD>".$domaintables["tablename"][$bzm]."</TD><TD>".$domaintables["owner"][$bzm]."</TD><TD>".$domaintables["type"][$bzm]."</TD></TR>";
        $bzm++;
        }
        echo "</TABLE><BR><BR>";

}?>



</TD></TR>

<TR><TD></TD><TD>
<?
$zeit = gettime() - $zeit0;
echo "<FONT COLOR=\"green\" SIZE=\"2\">complete execution time!</FONT>&nbsp;($zeit sec.)";
?>
</TD></TR>


</TABLE>





