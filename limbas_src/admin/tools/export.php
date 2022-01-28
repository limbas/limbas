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

/*$remote_sync_precheck
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

	$(".lmbPositionContainerMain").remove();

}


function LIM_setDefault(){
	<?php
	if($single_export){
		echo "LIM_activate(null,1)";
	}elseif($dump_export){
		echo "LIM_activate(null,2)";
	}elseif($group_export){
		echo "LIM_activate(null,3)";
	}elseif($sync_export OR $remote_sync_export OR $remote_sync_precheck OR $sync_export_config){
		echo "LIM_activate(null,4)";
	}else{
		echo "LIM_activate(null,1)";
	}
	?>
}



function lmb_selectAll(el){
    if($(el).prop("checked")){
        $(".syncexport").prop( "checked", true );
    }else{
    	$(".syncexport").prop( "checked", false );
    }
}

function lmb_disableAllBut(el) {
    if ($(el).prop("checked")) {
        // uncheck and disable all .syncexport and .syncexportf
        $(".syncexport, .syncexportf").not($(el)).prop("checked", false).attr("disabled", true);
        // clear text inputs
        $("input.syncexportf[type='text']").val('');
    } else {
        // enable all .syncexport and .syncexportf
        $(".syncexport, .syncexportf").not($(el)).removeAttr("disabled");
    }
}


</SCRIPT>

<div class="lmbPositionContainerMainTabPool">


<TABLE class="tabpool" BORDER="0" width="650" cellspacing="0" cellpadding="0"><TR><TD>

<TABLE BORDER="0" cellspacing="0" cellpadding="0"><TR class="tabpoolItemTR">
<TD nowrap ID="menu1" OnClick="LIM_activate(this,'1')" class="tabpoolItemActive"><?=$lang[965]?></TD>
<TD nowrap ID="menu2" OnClick="LIM_activate(this,'2')" class="tabpoolItemInactive"><?=$lang[967]?></TD>
<TD nowrap ID="menu3" OnClick="LIM_activate(this,'3')" class="tabpoolItemInactive"><?=$lang[968]?></TD>
<TD nowrap ID="menu4" OnClick="LIM_activate(this,'4')" class="tabpoolItemInactive"><?=$lang[2859]?></TD>
<TD class="tabpoolItemSpace">&nbsp;</TD>
</TR></TABLE>

</TD></TR>

<TR><TD class="tabpoolfringe">

<?php /* --- Remote-import ------------------------------- */?>
<TABLE ID="tab1" width="100%" cellspacing="0" cellpadding="0" class="tabBody">


<?php /* --- Tabellenliste ------------------------------- */?>
<FORM ACTION="main_admin.php" METHOD="post" name="form1">
<INPUT TYPE="hidden" NAME="action" VALUE="setup_export">
<INPUT TYPE="hidden" NAME="make_package" VALUE="0">

<TR class="tabHeader"><TD class="tabHeaderItem">&nbsp;<B><?=$lang[961]?></B></TD><TD class="tabHeaderItem"><?=$lang[925]?></TD></TR>
<TR class="tabBody"><TD><SELECT NAME="exptable[]" MULTIPLE SIZE="16" STYLE="width:200">
<?php
$odbc_table = dbf_20(array($DBA["DBSCHEMA"],null,"'TABLE','VIEW'"));
foreach($odbc_table["table_name"] as $tkey => $tvalue) {
	if($exptable){
		if(in_array($tvalue,$exptable)){$slct = "SELECTED";}else{$slct = "";}
	}
	echo "<OPTION VALUE=\"".$tvalue."\" $slct>".$tvalue;
}
?>
<TD ALIGN="CENTER" VALIGN="center">
<?php
if(!$format){$format = "system";}
if($format == "excel"){$chk = "CHECKED";}else{$chk = "";}?>
<i class="lmb-icon lmb-excel-alt2" TITLE="<?=$lang[962]?>" ALT="<?=$lang[962]?>" Border="0"></i><INPUT STYLE="border:0px;" TYPE="RADIO" NAME="format" VALUE="excel" <?=$chk?>>&nbsp;
<?php if($format == "txt"){$chk = "CHECKED";}else{$chk = "";}?>
<i class="lmb-icon lmb-file-text" TITLE="<?=$lang[963]?>" ALT="<?=$lang[963]?>" Border="0"></i><INPUT TYPE="RADIO" STYLE="border:0px;" NAME="format" VALUE="txt" <?=$chk?>>&nbsp;
<?php if($format == "system"){$chk = "CHECKED";}else{$chk = "";}?>
<IMG SRC="pic/limbasicon.gif" TITLE="<?=$lang[964]?>" ALT="<?=$lang[964]?>" Border="0"><INPUT TYPE="RADIO" NAME="format" STYLE="border:0px;" VALUE="system" <?=$chk?>>
<BR><BR>utf8 en/decode&nbsp;<input name="txt_encode" type="checkbox" <?php if($txt_encode){echo "checked";}?>>
</TD>
</TR>

<TR class="tabBody"><TD valign="top"><B>Filter:</B> <I>SQL conform (WHERE ...)</I><br>
<TEXTAREA STYLE="width:430px;height:50px" NAME="export_filter"><?=$export_filter?></TEXTAREA></TD>
<TD align="CENTER" valign="top"><br><INPUT TYPE="submit" NAME="single_export" VALUE="<?=$lang[979]?>.."></TD>
</TR>

</FORM>
</TABLE>

<?php /* --- Komplettexport ------------------------------- */
if ($dump_export)
{
	echo '<script language="JavaScript">
	limbasWaitsymbol(false,true,false);
	</script>';
}
?>

<TABLE ID="tab2" width="100%" cellspacing="2" cellpadding="1" class="tabBody" style="display:none;">

<FORM name="form2">
<INPUT TYPE="hidden" NAME="action" VALUE="setup_export">

<TR class="tabHeader"><TD class="tabHeaderItem" COLSPAN="3">&nbsp;<B><?=$lang[966]?></B></TD></TR>

<TR>
<TD ALIGN="LEFT"><IMG SRC="pic/limbasicon.gif" TITLE="<?=$lang[964]?>" ALT="<?=$lang[964]?>" Border="0"><INPUT TYPE="RADIO" NAME="format" STYLE="border:0px;" VALUE="system" CHECKED></TD>
<TD ALIGN="LEFT"><INPUT TYPE="CHECKBOX" NAME="struct_only" STYLE="border:0px;" VALUE="1"> Nur Struktur</TD>
<TD ALIGN="CENTER"><INPUT TYPE="submit" VALUE="<?=$lang[979]?>.." name="dump_export"></TD>
</TR>
</FORM>
</TABLE>




<?php /* --- Projectexport ------------------------------- */?>
<TABLE ID="tab3" width="100%" cellspacing="2" cellpadding="1" class="tabBody" style="display:none;">

<FORM name="form3">
<INPUT TYPE="hidden" NAME="action" VALUE="setup_export">
<INPUT TYPE="hidden" NAME="format" VALUE="group">

<TR class="tabHeader"><TD class="tabHeaderItem" COLSPAN="3">&nbsp;<B><?=$lang[2462]?></B></TD></TR>
<TR><TD>

<?php
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
<INPUT TYPE="hidden" NAME="action" VALUE="setup_export">
<INPUT TYPE="hidden" NAME="format" VALUE="sync">
<INPUT TYPE="hidden" NAME="remote_sync_export" VALUE="">

<TR class="tabHeader"><TD class="tabHeaderItem" COLSPAN="3">&nbsp;<B>Sync to file:</B></TD></TR>

<TR>
<TD COLSPAN="3"><TABLE width="100%" cellspacing="2" cellpadding="1" class="tabBody">
<TR>
<TD width="15">&nbsp;</TD>
<TD valign="top"><TABLE width="100%" cellspacing="2" cellpadding="1" class="tabBody">

<TR class="tabSubHeader">
<TD colspan="2" class="tabSubHeaderItem">Module:</TD>
</TR>

<TR>
<TD ><INPUT CLASS="syncexportf" TYPE="checkbox" STYLE="border:0px;" VALUE="0" OnClick="lmb_selectAll(this)"></TD><TD><?=$lang[994]?></TD>
</TR>
<TR title="lmb_groups, lmb_rules_tables, lmb_rules_fields, lmb_rules_dataset, lmb_rules_repform, lmb_rules_action, ldms_rules">
<TD ><INPUT CLASS="syncexport" TYPE="checkbox" NAME="syncgroup" STYLE="border:0px;" VALUE="1" <?=($syncgroup?"checked":"")?>></TD><TD style="cursor:help">incl. <?=$lang[575]?></TD>
</TR>

<TR>
<TD colspan=2><hr></TD>
</TR>

<TR title="lmb_conf_groups, lmb_conf_tables, lmb_conf_fields, lmb_conf_views, lmb_conf_viewfields, lmb_gtab_pattern, lmb_trigger, lmb_tabletree, lmb_lang_depend, lmb_select_p, lmb_select_w, lmb_attribute_p, lmb_attribute_w, lmb_lang_depend">
<TD><INPUT CLASS="syncexport" TYPE="checkbox" NAME="synctabs" STYLE="border:0px;" VALUE="1" <?=($synctabs?"checked":"")?>></TD><TD style="cursor:help"><?=$lang[577]?></TD>
</TR>
<TR title="lmb_form_list, lmb_forms">
<TD ><INPUT CLASS="syncexport" TYPE="checkbox" NAME="syncforms" STYLE="border:0px;" VALUE="1" <?=($syncforms?"checked":"")?>></TD><TD style="cursor:help"><?=$lang[2281]?></TD>
</TR>
<TR title="lmb_report_list, lmb_reports">
<TD ><INPUT CLASS="syncexport" TYPE="checkbox" NAME="syncrep" STYLE="border:0px;" VALUE="1" <?=($syncrep?"checked":"")?>></TD><TD style="cursor:help"><?=$lang[1788]?></TD>
</TR>
<TR title="lmb_chart_list, lmb_charts">
<TD ><INPUT CLASS="syncexport" TYPE="checkbox" NAME="synccharts" STYLE="border:0px;" VALUE="1" <?=($synccharts?"checked":"")?>></TD><TD style="cursor:help">Charts</TD>
</TR>
<TR title="lmb_wfl_task, lmb_wfl">
<TD ><INPUT CLASS="syncexport" TYPE="checkbox" NAME="syncwork" STYLE="border:0px;" VALUE="1" <?=($syncwork?"checked":"")?>></TD><TD style="cursor:help"><?=$lang[2035]?></TD>
</TR>
<TR title="ldms_structure">
<TD ><INPUT CLASS="syncexport" TYPE="checkbox" NAME="syncdms" STYLE="border:0px;" VALUE="1" <?=($syncdms?"checked":"")?>></TD><TD style="cursor:help">DMS</TD>
</TR>
</TABLE>

</TD><TD valign="top">

<TABLE width="100%" cellspacing="2" cellpadding="1" class="tabBody">
<TR class="tabSubHeader">
<TD colspan="2" class="tabSubHeaderItem"><?=$lang[1924]?>:</TD>
</TR>
<TR title="lmb_snap, lmb_snap_shared">
<TD ><INPUT CLASS="syncexport" TYPE="checkbox" NAME="syncsnapshots" STYLE="border:0px;" VALUE="1" <?=($syncsnapshots?"checked":"")?>></TD><TD style="cursor:help">Snapshots</TD>
</TR>
<TR title="lmb_currency">
<TD ><INPUT CLASS="syncexport" TYPE="checkbox" NAME="synccurrency" STYLE="border:0px;" VALUE="1" <?=($synccurrency?"checked":"")?>></TD><TD style="cursor:help">Currency</TD>
</TR>
<TR title="lmb_reminder_list">
<TD ><INPUT CLASS="syncexport" TYPE="checkbox" NAME="syncreminder" STYLE="border:0px;" VALUE="1" <?=($syncreminder?"checked":"")?>></TD><TD style="cursor:help">Reminder</TD>
</TR>
<TR title="lmb_colorschemes">
<TD ><INPUT CLASS="syncexport" TYPE="checkbox" NAME="synccolorscheme" STYLE="border:0px;" VALUE="1" <?=($synccolorscheme?"checked":"")?>></TD><TD style="cursor:help">Colorscheme</TD>
</TR>
<TR title="lmb_user_colors">
<TD ><INPUT CLASS="syncexport" TYPE="checkbox" NAME="syncusercolors" STYLE="border:0px;" VALUE="1" <?=($syncusercolors?"checked":"")?>></TD><TD style="cursor:help">Usercolors</TD>
</TR>
<TR title="lmb_crontab">
<TD ><INPUT CLASS="syncexport" TYPE="checkbox" NAME="synccrontab" STYLE="border:0px;" VALUE="1" <?=($synccrontab?"checked":"")?>></TD><TD style="cursor:help">Crontab</TD>
</TR>
<TR title="lmb_action_depend">
<TD ><INPUT CLASS="syncexport" TYPE="checkbox" NAME="synclinks" STYLE="border:0px;" VALUE="1" <?=($synclinks?"checked":"")?>></TD><TD style="cursor:help">menustructure</TD>
</TR>
<TR title="lmb_select_p, lmb_select_w, lmb_attribute_p, lmb_attribute_w">
<TD ><INPUT CLASS="syncexport" TYPE="checkbox" NAME="syncpools" STYLE="border:0px;" VALUE="1" <?=($syncpools?"checked":"")?>></TD><TD style="cursor:help">Pools</TD>
</TR>
<TR title="lmb_groups">
<TD ><INPUT CLASS="syncexport" TYPE="checkbox" NAME="syncrules" STYLE="border:0px;" VALUE="1" <?=($syncrules?"checked":"")?>></TD><TD style="cursor:help">Rechte&Gruppen</TD>
</TR>
<TR title="lmb_sync_conf lmb_sync_template">
<TD ><INPUT CLASS="syncexport" TYPE="checkbox" NAME="syncsynchronisation" STYLE="border:0px;" VALUE="1" <?=($syncsynchronisation?"checked":"")?>></TD><TD style="cursor:help">Synchronisation</TD>
</TR>
<TR title="lmb_umgvar, lmb_dbpatch, lmb_field_types, lmb_fonts, lmb_mimetypes, lmb_lang, lmb_action">
<TD ><INPUT CLASS="syncexport" TYPE="checkbox" NAME="syncsystem" STYLE="border:0px;" VALUE="1" <?=($syncsystem?"checked":"")?>></TD><TD style="cursor:help">System</TD>
</TR>
</TABLE>


</TD><TD valign="top">


<TABLE width="100%" cellspacing="2" cellpadding="1" class="tabBody">
<TR class="tabSubHeader">
<TD colspan="2" class="tabSubHeaderItem"><?=$lang[2483]?>:</TD>
</TR>
<TR>
<TD ><INPUT CLASS="syncexportf" TYPE="checkbox" NAME="syncfunctmpfields" STYLE="border:0px;" VALUE="1" <?=($syncfunctmpfields?"checked":"")?>></TD><TD style="cursor:help"><?=$lang[2030]?></TD>
</TR>
<TR>
<TD ><INPUT CLASS="syncexportf" TYPE="checkbox" NAME="syncfunctmpfiles" STYLE="border:0px;" VALUE="1" <?=($syncfunctmpfiles?"checked":"")?>></TD><TD style="cursor:help"><?=$lang[2367]?></TD>
</TR>
<TR>
<TD ><INPUT CLASS="syncexportf" TYPE="checkbox" NAME="syncfuncforkey" STYLE="border:0px;" VALUE="1" <?=($syncfuncforkey?"checked":"")?>></TD><TD style="cursor:help"><?=$lang[2476]?></TD>
</TR>
<TR>
<TD ><INPUT CLASS="syncexportf" TYPE="checkbox" NAME="syncfuncindize" STYLE="border:0px;" VALUE="1" <?=($syncfuncindize?"checked":"")?>></TD><TD style="cursor:help"><?=$lang[2721]?></TD>
</TR>
<TR>
<TD ><INPUT CLASS="syncexportf" TYPE="checkbox" NAME="syncfuncproz" STYLE="border:0px;" VALUE="1" <?=($syncfuncproz?"checked":"")?>></TD><TD style="cursor:help"><?=$lang[2652]?></TD>
</TR>
<TR>
<TD ><INPUT CLASS="syncexportf" TYPE="checkbox" NAME="syncfunctrigger" STYLE="border:0px;" VALUE="1" <?=($syncfunctrigger?"checked":"")?>></TD><TD style="cursor:help"><?=$lang[2488]?></TD>
</TR>
<TR>
<TD ><INPUT CLASS="syncexportf" TYPE="checkbox" NAME="syncfuncsequ" STYLE="border:0px;" VALUE="1" <?=($syncfuncsequ?"checked":"")?>></TD><TD style="cursor:help"><?=$lang[2662]?></TD>
</TR>
<TR>
<TD ><INPUT CLASS="syncexportf" TYPE="checkbox" NAME="syncfuncmenurefresh" STYLE="border:0px;" VALUE="1" <?=($syncfuncmenurefresh?"checked":"")?>></TD><TD style="cursor:help"><?=$lang[1056]?></TD>
</TR>
<TR>
<TD ><INPUT CLASS="syncexportf" TYPE="checkbox" NAME="syncfunctablerefresh" STYLE="border:0px;" VALUE="1" <?=($syncfunctablerefresh?"checked":"")?>></TD><TD style="cursor:help"><?=$lang[1054]?></TD>
</TR>
<TR>
<TD ><INPUT CLASS="syncexportf" TYPE="checkbox" NAME="syncfuncdelsession" STYLE="border:0px;" VALUE="1" <?=($syncfuncdelsession?"checked":"")?>></TD><TD style="cursor:help"><?=$lang[1057]?></TD>
</TR>
</TABLE>


</TD><TD valign="top">


<TABLE width="100%" cellspacing="2" cellpadding="1" class="tabBody">
<TR class="tabSubHeader">
<TD colspan="2" class="tabSubHeaderItem">source code:</TD>
</TR>
<TR>
<TD ><INPUT CLASS="syncexportf" TYPE="checkbox" NAME="syncsourcecode" STYLE="border:0px;" VALUE="1" ONCLICK="lmb_disableAllBut(this);" <?=($syncsourcecode?"checked":"")?>></TD><TD style="cursor:help">source code</TD>
</TR>
<TR>
<TD><INPUT CLASS="syncexportf" TYPE="checkbox" NAME="syncextensions" STYLE="border:0px;" VALUE="1" <?=($syncextensions?"checked":"")?>></TD><TD style="cursor:help">EXTENSIONS</TD>
</TR>
<TR title="run own function after update">
<TD>run:</TD><TD><INPUT CLASS="syncexportf" TYPE="text" NAME="synccallextensionfunction" VALUE="<?=($synccallextensionfunction?$synccallextensionfunction:"")?>"></TD>
</TR>
<?php
# if source code is selected, disable all checkboxes again
if($syncsourcecode) {
    echo '<script type="text/javascript">lmb_disableAllBut($("input.syncexportf[name=syncsourcecode]"));</script>';
}
?>

</TABLE></TD></TR>

</TD></TR>

</TABLE></TD></TR>


<TR>
<TD colspan="2" ALIGN="LEFT"><INPUT TYPE="submit" VALUE="export config" name="sync_export_config"></TD>
<TD ALIGN="RIGHT"><INPUT TYPE="submit" VALUE="export archiv" name="sync_export"></TD>
</TR>


<!-- Remote export -->
<tr>
    <td colspan="3"><hr></td>    
</tr>
<tr>
    <td colspan="3"><b>Sync to remote host:</b></td>
</tr>

<tr><td colspan="3"><TABLE width="100%" cellspacing="2" cellpadding="1" class="tabBody">
<tr>
    <TD width="15">&nbsp;</TD><td>Host:</td><td colspan="2"><input type="text" name="remoteHost" value="<?=$remoteHost?>"></td>
</tr>
<tr>
    <TD width="15">&nbsp;</TD><td>User:</td><td colspan="2"><input type="text" name="remoteUser" value="<?=$remoteUser?>"></td>
</tr>
<tr>
    <TD width="15">&nbsp;</TD><td>Pass:</td><td colspan="2"><input type="password" name="remotePass" value="<?=$remotePass?>"></td>
</tr>
<tr><td colspan="2">&nbsp;</td>
</table></TD></TR>

<tr>
    <td colspan="2"><input type="submit" value="start remote precheck" name="remote_sync_precheck" style="color:green"></td>    
</tr>

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
		<div class="lmbPositionContainerMain small">
            <p><h4><?=$lang[970]?>!</h4></p>
            <p>
                <?=$lang[971]?> <?=$lang[973]?>:<BR>
                <?=$lang[577]?>: <B><?=$result_exp_tabs?></B><BR>
                <?=$lang[972]?>: <B><?=$result_exp_dat?></B>
            </p>
            <p>
                <?=$lang[974]?>:<BR><BR>
                <A HREF="<?=$path_backup?>"><i class="lmb-icon lmb-download"></i>&nbsp;<?=$path_backup?></A> (export dump)
            </p>
        </div>
   	<?php
}elseif(($single_export OR $group_export) AND is_array($exptable)){
	if($result_backup = lmbExport($exptable,$format,$export_filter,null,$txt_encode)){
	?>
        <div class="lmbPositionContainerMain small">
            <p><h4><?=$lang[970]?>!</h4></p>
            <p>
                <?=$lang[971]?> <?=$lang[973]?>:<BR>
                <?=$lang[577]?>: <B><?=$result_exp_tabs?></B><BR>
                <?=$lang[972]?>: <B><?=$result_exp_dat?></B>
            </p>
            <p>
                <?php
                if(array_key_exists('path', $result_backup)){ # TODO is this possible?
                    echo "<i class=\"lmb-icon lmb-download\" style=\"font-size:20px\"></i><b> $lang[975]</b>";
                    foreach ($result_backup["path"] as $key => $value){
                        echo  "<li><I><A NAME=\"download\" HREF=\"$value\">".$result_backup["name"][$key]."</A></I></li>";
                    }
                    if(count($result_backup["path"]) > 1){
                        echo "<BR><i class=\"lmb-icon lmb-collapse-all\" style=\"font-size:20px\"></i><A HREF=\"Javascript:document.form1.make_package.value=1;document.form1.submit();\">$lang[977]!</A>";
                    }
                }else{
    			    echo "&nbsp;<li><I><A NAME=\"download\" HREF=\"$result_backup\" style=\"color:green;\">".$result_backup."</A></I></li>";
        			echo '<script language="JavaScript">limbasWaitsymbol(false,false,true);</script>';
                }
                ?>
            </p>
        </div>
    <?php
	}

}elseif($make_package){
	$path = "USER/".$session["user_id"]."/temp";
	if($path_ = make_fileArchive($path,"export_dump")){
		echo  "<div class=\"lmbPositionContainerMain small\">
		<i class=\"lmb-icon lmb-download\" style=\"font-size:20px\"></i><b> $lang[975]</b><br><br>
		<A NAME=\"download\" HREF=\"$path_\"><li>".$path_."</A></li></div>";
	}
}
elseif($sync_export || $remote_sync_export || $remote_sync_precheck || $sync_export_config)
{
	$tosync = array();

		if ($synctabs) $tosync[] = 'tabs';
		if ($syncforms) $tosync[] = 'forms';
        if ($syncrep) $tosync[] = 'rep';
        if ($synccharts) $tosync[] = 'charts';
        if ($syncwork) $tosync[] = 'work';
        if ($syncgroup) $tosync[] = 'group';
        if ($syncsnapshots) $tosync[] = 'snapshots';
        if ($syncreminder) $tosync[] = 'reminder';
        if ($synccurrency) $tosync[] = 'currency';
        if ($synccolorscheme) $tosync[] = 'colorscheme';
        if ($syncusercolors) $tosync[] = 'usercolors';
        if ($synccrontab) $tosync[] = 'crontab';
        if ($syncdms) $tosync[] = 'dms';
        if ($synclinks) $tosync[] = 'links';
        if ($syncpools) $tosync[] = 'pools';
        if ($syncrules) $tosync[] = 'rules';
        if ($syncsystem) $tosync[] = 'system';
        if ($syncsynchronisation) $tosync[] = 'synchronisation';
        
        if ($syncfunctmpfields) $tosync[] = 'functmpfields';
        if ($syncfunctmpfiles) $tosync[] = 'functmpfiles';
        if ($syncfuncforkey) $tosync[] = 'funcforkey';
        if ($syncfuncindize) $tosync[] = 'funcindize';
        if ($syncfuncproz) $tosync[] = 'funcproz';
        if ($syncfunctrigger) $tosync[] = 'functrigger';
        if ($syncfuncsequ) $tosync[] = 'funcsequ';
        if ($syncfuncmenurefresh) $tosync[] = 'funcmenurefresh';
        if ($syncfunctablerefresh) $tosync[] = 'functablerefresh';
        if ($syncfuncdelsession) $tosync[] = 'funcdelsession';

        if ($syncsourcecode) $tosync[] = 'sourcecode';
        if ($syncextensions) $tosync[] = 'extensions';
        if ($synccallextensionfunction) $tosync[] = "callextensionfunction $synccallextensionfunction";

        # export config as file
        if ($sync_export_config) {
            $configFilePath = $umgvar['path'] . '/TEMP/conf/autosync.conf.php';
            $handle = fopen($configFilePath, 'w+');
            fwrite($handle, '<?php $toSync = ' . var_export($tosync, true) . '; ?>');
            fclose($handle);
            
			echo  "<div class=\"lmbPositionContainerMain small\">
                <div class=\"tabpool\">
    			<i class=\"lmb-icon lmb-download\" style=\"font-size:20px\"></i><b> $lang[975]</b><br>
    			&nbsp;<li><I><A NAME=\"download\" HREF=\"TEMP/conf/autosync.conf.php\" target=\"_new\" style=\"color:green;\">autosync.conf.php</A></I></li>
    			</div></div>";
            return;
        }

    # local export
    if($sync_export) {
        if($result_backup = lmbExport(null,$format,null,$tosync,$txt_encode)){
        ?>
            <div class="lmbPositionContainerMain small">
                <p><h4><?=$lang[970]?>!</h4></p>
                <p>
                    <?=$lang[971]?> <?=$lang[973]?>:<BR>
                    <?=$lang[577]?>: <B><?=$result_exp_tabs?></B><BR>
                    <?=$lang[972]?>: <B><?=$result_exp_dat?></B>
                </p>
                <p>
                <?php
			    echo  "<div class=\"tabpool\">
    			<i class=\"lmb-icon lmb-download\" style=\"font-size:20px\"></i><b> $lang[975]</b><br>
    			&nbsp;<li><I><A NAME=\"download\" HREF=\"$result_backup\" style=\"color:green;\">".$result_backup."</A></I></li>
    			</div>";
    			echo '<script language="JavaScript">limbasWaitsymbol(false,false,true);</script>';
                ?>
                </p>
            </div>
        <?php
        }
        
    # remote export
    } else if($remote_sync_export || $remote_sync_precheck) {
        echo '<div id="limbasWaitsymbol" style="margin-left: 300px;" class="lmbWaitSymbol"></div>';
        ob_flush();
        flush();

        # get answer of soap
        $response = lmbExport_remoteSync($tosync, $remoteHost, $remoteUser, $remotePass, 2, $remote_sync_export);
        echo '<script>limbasWaitsymbol(null, true, true)</script>';

        # if answer valid
        if($response && is_array($response) && array_key_exists('output', $response)) {
            # log errors
            if (!$response['success']) {
                echo "Error!";
                error_log(print_r($response['errLog'], 1));
            }
            
            # output html answer
            echo $response['output'];

            # start remote export button
            if (!$remote_sync_export && $response['success']) {
                echo '<div class="lmbPositionContainerMain">';
                echo '<input type="button" value="start remote export" onclick="document.form4.remote_sync_export.value=1;document.form4.submit();" style="color:green">';
                echo '</div>';
            }
        } else {
            echo "Invalid answer of remote system: " . json_encode($response);
        }
    }
    
}

?>

</div>