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
 * ID: 145
 */
?>


<script language="JavaScript">

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


function LIM_setDefault(){
	<?
	if($single_export){
		echo "LIM_activate(null,1)";
	}elseif($dump_export){
		echo "LIM_activate(null,2)";
	}elseif($group_export){
		echo "LIM_activate(null,3)";
	}elseif($sync_export){
		echo "LIM_activate(null,4)";
	}else{
		echo "LIM_activate(null,1)";
	}
	?>

}

</SCRIPT>

<div class="lmbPositionContainerMainTabPool">


<TABLE class="tabpool" BORDER="0" width="600" cellspacing="0" cellpadding="0"><TR><TD>

<TABLE BORDER="0" cellspacing="0" cellpadding="0"><TR class="tabpoolItemTR">
<TD nowrap ID="menu1" OnClick="LIM_activate(this,'1')" class="tabpoolItemActive"><?=$lang[965]?></TD>
<TD nowrap ID="menu2" OnClick="LIM_activate(this,'2')" class="tabpoolItemInactive"><?=$lang[967]?></TD>
<TD nowrap ID="menu3" OnClick="LIM_activate(this,'3')" class="tabpoolItemInactive"><?=$lang[968]?></TD>
<TD nowrap ID="menu4" OnClick="LIM_activate(this,'4')" class="tabpoolItemInactive"><?=$lang[2859]?></TD>
<TD class="tabpoolItemSpace">&nbsp;</TD>
</TR></TABLE>

</TD></TR>

<TR><TD class="tabpoolfringe">

<?/* --- Remote-import ------------------------------- */?>
<TABLE ID="tab1" width="100%" cellspacing="0" cellpadding="0" class="tabBody">


<?/* --- Tabellenliste ------------------------------- */?>
<FORM ACTION="main_admin.php" METHOD="post" name="form1">
<INPUT TYPE="hidden" NAME="action" VALUE="setup_export">
<INPUT TYPE="hidden" NAME="make_package" VALUE="0">

<TR class="tabHeader"><TD class="tabHeaderItem">&nbsp;<B><?=$lang[961]?></B></TD><TD class="tabHeaderItem"><?=$lang[978]?></TD></TR>
<TR class="tabBody"><TD><SELECT NAME="exptable[]" MULTIPLE SIZE="16" STYLE="width:200">
<?
$odbc_table = dbf_20(array($DBA["DBSCHEMA"],null,"'TABLE','VIEW'"));
foreach($odbc_table["table_name"] as $tkey => $tvalue) {
	if($exptable){
		if(in_array($tvalue,$exptable)){$slct = "SELECTED";}else{$slct = "";}
	}
	echo "<OPTION VALUE=\"".$tvalue."\" $slct>".$tvalue;
}
?>
<TD ALIGN="CENTER" VALIGN="center">
<?
if(!$format){$format = "system";}
if($format == "excel"){$chk = "CHECKED";}else{$chk = "";}?>
<i class="lmb-icon lmb-excel-alt2" TITLE="<?=$lang[962]?>" ALT="<?=$lang[962]?>" Border="0"></i><INPUT STYLE="border:0px;" TYPE="RADIO" NAME="format" VALUE="excel" <?=$chk?>>&nbsp;
<?if($format == "txt"){$chk = "CHECKED";}else{$chk = "";}?>
<i class="lmb-icon lmb-file-text" TITLE="<?=$lang[963]?>" ALT="<?=$lang[963]?>" Border="0"></i><INPUT TYPE="RADIO" STYLE="border:0px;" NAME="format" VALUE="txt" <?=$chk?>>&nbsp;
<?if($format == "system"){$chk = "CHECKED";}else{$chk = "";}?>
<IMG SRC="pic/limbasicon.gif" TITLE="<?=$lang[964]?>" ALT="<?=$lang[964]?>" Border="0"><INPUT TYPE="RADIO" NAME="format" STYLE="border:0px;" VALUE="system" <?=$chk?>>
<BR><BR>utf8 en/decode&nbsp;<input name="txt_encode" type="checkbox" <?if($txt_encode){echo "checked";}?>>
</TD>
</TR>

<TR class="tabBody"><TD valign="top"><B>Filter:</B> <I>SQL conform (WHERE ...)</I><br>
<TEXTAREA STYLE="width:430px;height:50px" NAME="export_filter"><?=$export_filter?></TEXTAREA></TD>
<TD align="CENTER" valign="top"><br><INPUT TYPE="submit" NAME="single_export" VALUE="<?=$lang[979]?>.."></TD>
</TR>

</FORM>
</TABLE>

<?/* --- Komplettexport ------------------------------- */
if ($dump_export)
{
	echo '<script language="JavaScript">
	limbasWaitsymbol(false,true,false);
	</script>';
}
?>

<TABLE ID="tab2" width="100%" cellspacing="2" cellpadding="1" class="tabBody" style="display:none;">

<FORM name="form2">
<input type="hidden" name="<?echo $_SID;?>" value="<?echo session_id();?>">
<INPUT TYPE="hidden" NAME="action" VALUE="setup_export">

<TR class="tabHeader"><TD class="tabHeaderItem" COLSPAN="3">&nbsp;<B><?=$lang[966]?></B></TD></TR>

<TR>
<TD ALIGN="LEFT"><IMG SRC="pic/limbasicon.gif" TITLE="<?=$lang[964]?>" ALT="<?=$lang[964]?>" Border="0"><INPUT TYPE="RADIO" NAME="format" STYLE="border:0px;" VALUE="system" CHECKED></TD>
<TD ALIGN="LEFT"><INPUT TYPE="CHECKBOX" NAME="struct_only" STYLE="border:0px;" VALUE="1"> Nur Struktur</TD>
<TD ALIGN="CENTER"><INPUT TYPE="submit" VALUE="<?=$lang[979]?>.." name="dump_export"></TD>
</TR>
</FORM>
</TABLE>




<?/* --- Projectexport ------------------------------- */?>
<TABLE ID="tab3" width="100%" cellspacing="2" cellpadding="1" class="tabBody" style="display:none;">

<FORM name="form3">
<input type="hidden" name="<?echo $_SID;?>" value="<?echo session_id();?>">
<INPUT TYPE="hidden" NAME="action" VALUE="setup_export">
<INPUT TYPE="hidden" NAME="format" VALUE="group">

<TR class="tabHeader"><TD class="tabHeaderItem" COLSPAN="3">&nbsp;<B><?=$lang[2462]?></B></TD></TR>
<TR><TD>

<?
$submit = 'group_export_';
if(!$group_export){
	lmbExport_groupSelection();
	$submit = 'group_export';
}else
{
	echo '<script language="JavaScript">
limbasWaitsymbol(false,true,false);
</script>';
}
?>
</td></TR>

<TR><TD>&nbsp;</TD></TR>
<TR><TD align="center"><input type="submit" value="<?=$lang[979]?>.." name="<?=$submit?>"></TD></TR>
</TD></TR>

</FORM>
</TABLE>




<?php

/* --- Sync Export ------------------------------- */




if ($sync_export)
{
	echo '<script language="JavaScript">
	limbasWaitsymbol(false,true,false);
	</script>';
}
?>

<TABLE ID="tab4" width="100%" cellspacing="2" cellpadding="1" class="tabBody" style="display:none;">

<FORM name="form4">
<input type="hidden" name="<?echo $_SID;?>" value="<?echo session_id();?>">
<INPUT TYPE="hidden" NAME="action" VALUE="setup_export">

<TR class="tabHeader"><TD class="tabHeaderItem" COLSPAN="3">&nbsp;<B><?=$lang[10001]?></B></TD></TR>

<TR>
<TD ALIGN="LEFT" COLSPAN="2"><IMG SRC="pic/limbasicon.gif" TITLE="<?=$lang[10001]?>" ALT="<?=$lang[10001]?>" Border="0"><INPUT TYPE="RADIO" NAME="format" STYLE="border:0px;" VALUE="sync" CHECKED></TD>
<TD ALIGN="CENTER"><INPUT TYPE="submit" VALUE="<?=$lang[979]?>.." name="sync_export"></TD>
</TR>

<TR>
<TD ALIGN="RIGHT" COLSPAN="2"><INPUT TYPE="checkbox" NAME="syncall" STYLE="border:0px;" VALUE="1"></TD><TD><?=$lang[994]?></TD>
</TR>
<TR>
<TD ALIGN="RIGHT" COLSPAN="2"><INPUT TYPE="checkbox" NAME="synctabs" STYLE="border:0px;" VALUE="1"></TD><TD><?=$lang[577]?></TD>
</TR>
<TR>
<TD ALIGN="RIGHT" COLSPAN="2"><INPUT TYPE="checkbox" NAME="syncforms" STYLE="border:0px;" VALUE="1"></TD><TD><?=$lang[2281]?></TD>
</TR>
<TR>
<TD ALIGN="RIGHT" COLSPAN="2"><INPUT TYPE="checkbox" NAME="syncrep" STYLE="border:0px;" VALUE="1"></TD><TD><?=$lang[2280]?></TD>
</TR>
<TR>
<TD ALIGN="RIGHT" COLSPAN="2"><INPUT TYPE="checkbox" NAME="syncwork" STYLE="border:0px;" VALUE="1"></TD><TD><?=$lang[2035]?></TD>
</TR>
<TR>
<TD ALIGN="RIGHT" COLSPAN="2"><INPUT TYPE="checkbox" NAME="syncgroup" STYLE="border:0px;" VALUE="1"></TD><TD><?=$lang[453]?></TD>
</TR>
<TR>
<TD ALIGN="RIGHT" COLSPAN="2"><INPUT TYPE="checkbox" NAME="syncsons" STYLE="border:0px;" VALUE="1"></TD><TD><?=$lang[1924]?></TD>
</TR>

</FORM>
</TABLE>




</TR></TD>
</TABLE>

<script language="JavaScript">
LIM_setDefault();
</script>



<?php

ob_flush();
flush();

set_time_limit(1000000);

if($dump_export) {
	
	echo '<script language="JavaScript">
	limbasWaitsymbol(false,false,true);
	</script>';

	$path_backup = lmbExport_Dump($format,$struct_only);
	?>
		<BR><BR>
        &nbsp;&nbsp;<FONT SIZE="2"><U><?=$lang[970]?>!</U></FONT><BR><BR>
        &nbsp;&nbsp;<?=$lang[971]?>:<BR>
        &nbsp;&nbsp;<B><?echo $result_exp_tabs;?></B> <?=$lang[950]?><BR>
        &nbsp;&nbsp;<B><?echo $result_exp_dat;?></B> <?=$lang[972]?><BR>
        &nbsp;&nbsp;<?=$lang[973]?><BR><BR>
        &nbsp;&nbsp;<?=$lang[974]?>: <I><?echo $path_backup;?></I><BR><BR>
        &nbsp;&nbsp;<A HREF="<?=$path_backup?>"><?=$path_backup?></A> (export dump)<BR>
   	<?php
}elseif(($single_export OR $group_export) AND is_array($exptable)){
	if($result_backup = lmbExport($exptable,$format,$export_filter,null,$txt_encode)){
	?>
		<BR><BR>
        &nbsp;&nbsp;<FONT SIZE="2"><U><?=$lang[970]?>!</U></FONT><BR><BR>
        &nbsp;&nbsp;<?=$lang[971]?> <?=$lang[973]?>:<BR>
        &nbsp;&nbsp;<?=$lang[950]?>: <B><?echo $result_exp_tabs;?></B><BR>
        &nbsp;&nbsp;<?=$lang[972]?>: <B><?echo $result_exp_dat;?></B><BR><BR>
        
        <?php
        echo "&nbsp;&nbsp;$lang[976]:<BR>";
        foreach ($result_backup["path"] as $key => $value){
			echo  "&nbsp;&nbsp;<I><A NAME=\"download\" HREF=\"$value\">".$result_backup["name"][$key]."</A></I><BR>";
        }
        if(count($result_backup["path"]) > 1){
        	echo "<BR><BR>&nbsp;&nbsp;<A HREF=\"Javascript:document.form1.make_package.value=1;document.form1.submit();\">$lang[977]!</A>";
        }
	}

}elseif($make_package){
	$path = "USER/".$session["user_id"]."/temp";
	if($path_ = make_fileArchive($path,"export_dump")){
		echo  "<BR><BR>&nbsp;&nbsp;<I><A NAME=\"download\" HREF=\"$path_\">".$path_."</A> ($lang[975])</I><BR>";
	}
}
elseif($sync_export)
{
	$tosync = array();
	if ($syncall)
	{
		$tosync = array('tabs','forms','rep','work','group','sons');
	}
	else 
	{
		if ($synctabs) $tosync[] = 'tabs';
		if ($syncforms) $tosync[] = 'forms';
		if ($syncrep) $tosync[] = 'rep';
		if ($syncwork) $tosync[] = 'work';
		if ($syncgroup) $tosync[] = 'group';
		if ($syncsons) $tosync[] = 'sons';
	}
	if($result_backup = lmbExport(null,$format,null,$tosync,$txt_encode)){
	?>
		<BR><BR>
        &nbsp;&nbsp;<FONT SIZE="2"><U><?=$lang[970]?>!</U></FONT><BR><BR>
        &nbsp;&nbsp;<?=$lang[971]?> <?=$lang[973]?>:<BR>
        &nbsp;&nbsp;<?=$lang[950]?>: <B><?echo $result_exp_tabs;?></B><BR>
        &nbsp;&nbsp;<?=$lang[972]?>: <B><?echo $result_exp_dat;?></B><BR><BR>
        
        <?php
        echo "&nbsp;&nbsp;$lang[976]:<BR>";
        foreach ($result_backup["path"] as $key => $value){
			echo  "&nbsp;&nbsp;<I><A NAME=\"download\" HREF=\"$value\">".$result_backup["name"][$key]."</A></I><BR>";
        }
        if(count($result_backup["path"]) > 1){
        	echo "<BR><BR>&nbsp;&nbsp;<A HREF=\"Javascript:document.form1.make_package.value=1;document.form1.submit();\">$lang[977]!</A>";
        }
	}
}

?>


</div>