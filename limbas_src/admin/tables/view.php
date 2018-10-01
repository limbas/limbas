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
 * ID: 225
 */

?>

<!-- include codemirror with sql syntax highlighting and sql code completion -->
<script src="extern/codemirror/lib/codemirror.js"></script>
<script src="extern/codemirror/edit/matchbrackets.js"></script>
<script src="extern/codemirror/edit/matchtags.js"></script>
<script src="extern/codemirror/mode/sql/sql.js"></script>
<script src="extern/codemirror/addon/hint/show-hint.js"></script>
<link rel="stylesheet" href="extern/codemirror/addon/hint/show-hint.css">
<script src="extern/codemirror/addon/hint/sql-hint.js"></script>
<link rel="stylesheet" href="extern/codemirror/lib/codemirror.css">
<style>
    .CodeMirror {
        border: 1px solid <?=$farbschema['WEB3']?>;
        position: absolute;
        height: unset;
        top: 0;
        bottom: 0;
        left: 0;
        right: 0;
    }
</style>
<script src="extern/sqlFormatter/sql-formatter.min.js"></script>

<style>

.this_viewfieldform{
	border:none;
	background-color:transparent;
	width:100px;
	height:14px;
	overflow:hidden;
	text-align:left;
        float:left;
}

.this_viewfieldtd{
	border:1px solid grey;
	width:100px;
	height:16px;
}

</style>


<script language="JavaScript">

var zoomKey = null;
var zoomEl = null;
function ZoomViewField(event,el,key) {
	zoomKey = key;
	zoomEl = el;

    limbasDivShow(el,null,'ZoomFieldContainer');

    zoomFieldCodeMirror.setValue(el.value);
    zoomFieldCodeMirror.focus();
    zoomFieldCodeMirror.refresh();

	return false;
}

function ZoomViewFieldUpdate(cm) {
    if (zoomEl) {
        zoomEl.value = cm.getValue();
        lmbAjax_EditViewfield(event,zoomKey,'field','key_'+zoomKey,zoomKey);
        divclose();
    }
}

$(function() {
    zoomFieldCodeMirror = CodeMirror.fromTextArea(document.getElementById("ZoomFieldArea"), {
        lineNumbers: true,
        matchBrackets: true,
        mode: "text/x-sql",
        indentWithTabs: true,
        smartIndent: true,
        autofocus: false,
        extraKeys: {
            "Ctrl-Space": "autocomplete"
        }
    });
    zoomFieldCodeMirror.on("blur", function(cm) {
        ZoomViewFieldUpdate(cm);
    });
});


</script>


<div id="ZoomFieldContainer" class="ajax_container" style="position:absolute;visibility:hidden;width:600px;z-index:999;" onclick="activ_menu=1;">
    <?php pop_closetop('ZoomFieldContainer'); ?>
    <br>
    <textarea id="ZoomFieldArea" style="overflow:auto;"></textarea>
</div>

<div class="lmbPositionContainerMainTabPool lmbFullSize">

<FORM ACTION="main_admin.php" METHOD="post" name="form1" style="height: 100%;">
<input type="hidden" name="action" value="setup_gtab_view">
<input type="hidden" name="view_section" value="<?=$view_section?>">
<input type="hidden" name="viewid" value="<?=$viewid?>">
<input type="hidden" name="setdrag">
<input type="hidden" name="setrelation">
<input type="hidden" name="setviewfield">
<input type="hidden" name="settype">
<input type="hidden" name="showsystabs">
<input type="hidden" name="view_save">
<input type="hidden" name="options_save">
<input type="hidden" name="act">
<?php



if($view_preview){$view_section = 3;}
if(!$view_section){$view_section = 1;}

# save view options
if($options_save){
	lmb_QuestOptions($viewid,$options);
}


// get view infos from limbas
$gview = lmb_getQuestValue($viewid);



if($view_section == 1 OR $view_section == 2) {


    // generate SQL from editor
    if ($view_section == 2 AND ($act == 'view_save' OR $act == 'view_isvalid' OR $act == 'view_create' OR $act == 'view_replace')) {
        $view_def = lmb_questCreateSQL($viewid);
    }

    // save view definition
    if ($act == 'view_save') {
        lmb_saveViewDefinition($viewid, $view_def);
    } elseif ($act == 'view_isvalid') {
        lmb_saveViewDefinition($viewid, $view_def);
        if (lmb_precheckView($viewid, $view_def)) {
            $gview["isvalid"] = 1;
        }
    } elseif ($act == 'view_drop') {
        lmb_createQuestView($viewid, $view_def, $gview["ispublic"], 1);
    } elseif ($act == 'view_create') {
        lmb_saveViewDefinition($viewid, $view_def);
        if (lmb_precheckView($viewid, $view_def)) {
            $gview["isvalid"] = 1;
            lmb_createQuestView($viewid, $view_def, $gview["ispublic"]);
        }
    } elseif ($act == 'view_replace') {
        lmb_saveViewDefinition($viewid, $view_def);
        if (lmb_precheckView($viewid, $view_def)) {
            $gview["isvalid"] = 1;
            lmb_createQuestView($viewid, $view_def, $gview["ispublic"], null, 1);
        } else {

        }
    }


    if ($act == 'view_public') {
        // drop lmb_conf_fields
        if ($gview["ispublic"]) {
            lmb_QuestDeleteConfig($viewid);
            // create lmb_conf_fields
        } elseif ($gview['isvalid']) {
            lmb_QuestConfig($viewid, $view_group, $view_name);
        }
    }

    // get view infos from limbas after update
    $gview = lmb_getQuestValue($viewid);


}





if($view_section == 1){

	?>
	<TABLE class="tabpool" BORDER="0" cellspacing="0" cellpadding="0"><TR><TD>
	<TABLE BORDER="0" cellspacing="0" cellpadding="0" style="width: 100%; border-collapse: collapse;"><TR class="tabpoolItemTR">
	<TD nowrap class="tabpoolItemActive"><?=$lang[2026]?></TD>
	<TD nowrap class="tabpoolItemInactive" OnClick="document.form1.view_section.value='2';document.form1.submit();"><?=$lang[2612]?></TD>
	<TD nowrap class="tabpoolItemInactive" OnClick="document.form1.view_section.value='3';document.form1.submit();"><?=$lang[1739]?></TD>
	<TD nowrap class="tabpoolItemInactive" OnClick="document.form1.view_section.value='4';document.form1.submit();"><?=$lang[2795]?></TD>
    <?php if($gview["ispublic"]){?><TD nowrap class="tabpoolItemInactive" OnClick="document.form1.view_section.value='5';document.form1.submit();"><?=$lang[953]?></TD><?php }?>
	<TD class="tabpoolItemSpace">&nbsp;
	</TD>
	</TR></TABLE>

	</TD></TR>
	
	<TR><TD class="tabpoolfringe">
	<TABLE BORDER="0" cellspacing="1" cellpadding="2" width="100%" class="tabBody" style="height: 100%;">
	
	<TR><TD><B><?=$gview["viewname"]?></B></TD></TR>
	
	<TR style="height: 100%;"><TD style="position: relative;">
	<textarea id="view_def" name="view_def" style="width:100%;height:100%;background-color:<?=$farbschema["WEB8"]?>"><?=htmlentities($gview["viewdef"],ENT_QUOTES,$GLOBALS["umgvar"]["charset"])?></textarea></TD></TR>
    <Script language="JavaScript">

        var editor = CodeMirror.fromTextArea(document.getElementById("view_def"), {
            lineNumbers: true,
            lineWrapping:true,
            matchBrackets: true,
            mode: "text/x-sql",
            indentWithTabs: true,
            smartIndent: true,
            autofocus: true,
            extraKeys: {
                "Ctrl-Space": "autocomplete"
            }
        });

        function formatSQL() {
            editor.setValue(sqlFormatter.format(editor.getValue(), { indent: "    "}));
        }
        editor.on('blur', formatSQL);

        $(function() {
            formatSQL();
        });

    </Script>
    <TR class="tabBody"><TD><HR></TD></TR>
	<TR class="tabBody"><TD align="right">

    <div style="float:left;">
    <?=$lang[1996]?><?php if ($gview["ispublic"]) {echo "<i class=\"lmb-icon lmb-check\"></i>";}else{echo "<i class=\"lmb-icon lmb-minus-circle\"></i>";}?>&nbsp;
    <?=$lang[2023]?><?php if ($gview["viewexists"]) {echo "<i class=\"lmb-icon lmb-check\"></i>";}else{echo "<i class=\"lmb-icon lmb-minus-circle\"></i>";}?>&nbsp;
    Syntax<?php if ($gview["isvalid"]) {echo "<i class=\"lmb-icon lmb-check\"></i>";}else{echo "<i class=\"lmb-icon lmb-minus-circle\"></i>";}?>&nbsp;
    </div>

    <?php
    #if($gview["isvalid"]) {$st_c = "color:black;background-color:".$farbschema['WEB7'];}
	#if($gview["ispublic"]){$st_p = "color:black;background-color:".$farbschema['WEB7'];}
	#if($gview["viewexists"]){$st_v = "color:black;background-color:".$farbschema['WEB7'];}
	?>

	<input type="button" value="<?=$lang[2940]?>" onclick="document.form1.act.value='view_save';setDrag();document.form1.submit();">
    <input type="button" value="<?=$lang[2941]?>" onclick="document.form1.act.value='view_isvalid';document.form1.submit();">

    <?php
    if ($gview["viewexists"]) {
        if (!$gview["isvalid"]) {
            $st = "style=\"opacity:0.4; cursor:default;\" disabled";
        }
            echo "<input type=\"button\" value = \"" . $lang[1996] . "\" $st onclick=\"document.form1.act.value='view_public';document.form1.submit();\">";
            echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
            echo "<input type=\"button\" value=\"" . $lang[2942] . "\"  onclick=\"document.form1.act.value='view_replace';document.form1.submit();\">&nbsp;";
        #}else{
            #echo "&nbsp;&nbsp;&nbsp;&nbsp;";
        #}
        echo "<input type=\"button\" value=\"" . $lang[2023] . " " . $lang[160] . "\" onclick=\"document.form1.act.value='view_drop';document.form1.submit();\">&nbsp;";
    } else {
        #if ($gview["isvalid"]) {
            echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
            echo "<input type=\"button\" value=\"" . $lang[2942] . "\" onclick=\"document.form1.act.value='view_create';document.form1.submit();\">&nbsp;";
        #}
    }
    ?>


	</TD></TR>
	<TR class="tabFooter"><TD></TD></TR>
	</TABLE>
	</TD></TR>
	</TABLE>
	
<?php
}elseif($view_section == 2){
	
	if($showsystabs){
		if($showsystabs == 2){$showsystabs = "FALSE";}else{$showsystabs = "TRUE";}
		$sqlquery = "UPDATE LMB_CONF_VIEWS SET USESYSTABS = $showsystabs WHERE ID = $viewid";
		$rs = odbc_exec($db,$sqlquery) or errorhandle(odbc_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
	}

?>

<i id="relationSign" class="lmb-icon lmb-chain-alt" style="position:absolute;overflow:hidden;z-index:9999;visibility:hidden;"></i>
<div id="fieldinfo" class="ajax_container" style="width:300px;position:absolute;z-index:99999;border:1px solid black;padding:4px;visibility:hidden;background-color:<?=$farbschema["WEB11"]?>" OnClick="activ_menu=1;"></div>
<div id="tablist" class="ajax_container" style="height:300px;position:absolute;overflow:auto;z-index:999;border:1px solid black;padding:4px;visibility:hidden;background-color:<?=$farbschema["WEB11"]?>">
<table>
<?php
$odbc_table = dbf_20(array($DBA["DBSCHEMA"],null,"'TABLE','VIEW'"));
foreach($odbc_table["table_name"] as $tkey => $tvalue) {
        if(!$gview["showsystabs"]){
                if(lmb_substr($odbc_table["table_name"][$tkey],0,4) == "lmb_" OR lmb_substr($odbc_table["table_name"][$tkey],0,5) == "ldms_"){continue;}
        }
        if(lmb_strtoupper($odbc_table["table_type"][$tkey]) == "VIEW"){$val = "VIEW :: ".$odbc_table["table_name"][$tkey];}else{$val = $odbc_table["table_name"][$tkey];}
        echo "<tr><td nowrap><a href=\"#\" OnClick=\"lmbAjax_ViewEditorPattern('".str_replace("=","",base64_encode($tvalue)).";20,20;'+document.getElementById('tablist').style.left+','+document.getElementById('tablist').style.top);divclose();\">".$val."</a></td></tr>";
}
if($gview["showsystabs"]){$CKECKED="CHECKED";$val = 2;}else{$val = 1;}
echo "<tr><td><hr><input type=\"checkbox\" OnClick=\"document.form1.showsystabs.value=$val;document.form1.submit();\" $CKECKED>&nbsp;".$lang[2635]."</td></tr>";
?>
</table>
</div>

<TABLE class="tabpool" BORDER="0" cellspacing="0" cellpadding="0"><TR><TD>
<TABLE BORDER="0" cellspacing="0" cellpadding="0" style="width: 100%; border-collapse: collapse;"><TR class="tabpoolItemTR">
<TD nowrap class="tabpoolItemInactive" OnClick="document.form1.view_section.value='1';document.form1.submit();"><?=$lang[2026]?></TD>
<TD nowrap class="tabpoolItemActive"><?=$lang[2612]?></TD>
<TD nowrap class="tabpoolItemInactive" OnClick="document.form1.view_section.value='3';document.form1.submit();"><?=$lang[1739]?></TD>
<TD nowrap class="tabpoolItemInactive" OnClick="document.form1.view_section.value='4';document.form1.submit();"><?=$lang[2795]?></TD>
<?php if($gview["ispublic"]){?><TD nowrap class="tabpoolItemInactive" OnClick="document.form1.view_section.value='5';document.form1.submit();"><?=$lang[953]?></TD><?php }?>
<TD class="tabpoolItemSpace">&nbsp;</TD>
</TR>
</TABLE>

</TD></TR><TR><TD class="tabpoolfringe" height="100%">

<TABLE BORDER="0" cellspacing="1" cellpadding="2" width="100%" height=100% class="tabBody">

<TR><TD><B><?=$gview["viewname"]?></B></TD></TR>

<TR style="height:100%;"><TD>
<div id="vieweditorPattern" style="position:relative;border:1px solid grey;width:100%;height:100%;overflow:auto;" oncontextmenu="limbasDivShow('',event,'tablist');return false;">
<?php
require_once("admin/tables/viewschema.php");
?>
</div>
</TD></TR>
<TR><TD width="100%">
<div id="lmbViewfieldContainer" style="overflow:auto;">
<?php
show_viewFields($viewid);
?>
</div>
</TD></TR>

<TR class="tabBody"><TD><HR></TD></TR>
<TR class="tabBody"><TD align="right">

    <div style="float:left;">
    <?=$lang[1996]?><?php if ($gview["ispublic"]) {echo "<i class=\"lmb-icon lmb-check\"></i>";}else{echo "<i class=\"lmb-icon lmb-minus-circle\"></i>";}?>&nbsp;
    <?=$lang[2023]?><?php if ($gview["viewexists"]) {echo "<i class=\"lmb-icon lmb-check\"></i>";}else{echo "<i class=\"lmb-icon lmb-minus-circle\"></i>";}?>&nbsp;
    Syntax<?php if ($gview["isvalid"]) {echo "<i class=\"lmb-icon lmb-check\"></i>";}else{echo "<i class=\"lmb-icon lmb-minus-circle\"></i>";}?>&nbsp;
    </div>

    <?php
    #if($gview["isvalid"]) {$st_c = "color:black;background-color:".$farbschema['WEB7'];}
	#if($gview["ispublic"]){$st_p = "color:black;background-color:".$farbschema['WEB7'];}
	#if($gview["viewexists"]){$st_v = "color:black;background-color:".$farbschema['WEB7'];}
	?>

	<input type="button" value="<?=$lang[2940]?>" onclick="document.form1.act.value='view_save';setDrag();document.form1.submit();">
    <input type="button" value="<?=$lang[2941]?>" onclick="document.form1.act.value='view_isvalid';document.form1.submit();">

    <?php
    if ($gview["viewexists"]) {
        if (!$gview["isvalid"]) {
            $st = "style=\"opacity:0.4; cursor:default;\" disabled";
        }
        if($gview["ispublic"]){$ispublic_lang = $lang[2943];$st='';}else{$ispublic_lang = $lang[1996];}
        echo "<input type=\"button\" value = \"" . $ispublic_lang . "\" $st onclick=\"document.form1.act.value='view_public';document.form1.submit();\">";
        echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
        echo "<input type=\"button\" value=\"" . $lang[2942] . "\"  onclick=\"document.form1.act.value='view_replace';document.form1.submit();\">&nbsp;";
        echo "<input type=\"button\" value=\"" . $lang[2023] . " " . $lang[160] . "\" onclick=\"document.form1.act.value='view_drop';document.form1.submit();\">&nbsp;";
    } else {
        #if ($gview["isvalid"]) {
            echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
            echo "<input type=\"button\" value=\"" . $lang[2942] . "\" onclick=\"document.form1.act.value='view_create';document.form1.submit();\">&nbsp;";
        #}
    }
    ?>

</TD></TR>
<TR class="tabFooter"><TD></TD></TR>
</TABLE>
</TD></TR>
</TABLE>

<script language="JavaScript">
document.getElementById("lmbViewfieldContainer").style.width = (window.innerWidth-60);
</script>
	
<?php
}elseif($view_section == 3){
	$gview = lmb_getQuestValue($viewid);
?>
	<TABLE class="tabpool" BORDER="0" cellspacing="0" cellpadding="0"><TR><TD>
	<TABLE BORDER="0" cellspacing="0" cellpadding="0" style="width: 100%; border-collapse: collapse;"><TR class="tabpoolItemTR">
	<TD nowrap class="tabpoolItemInactive" OnClick="document.form1.view_section.value='1';document.form1.submit();"><?=$lang[2026]?></TD>
	<TD nowrap class="tabpoolItemInactive" OnClick="document.form1.view_section.value='2';document.form1.submit();"><?=$lang[2612]?></TD>
	<TD nowrap class="tabpoolItemActive"><?=$lang[1739]?></TD>
	<TD nowrap class="tabpoolItemInactive" OnClick="document.form1.view_section.value='4';document.form1.submit();"><?=$lang[2795]?></TD>
    <?php if($gview["ispublic"]){?><TD nowrap class="tabpoolItemInactive" OnClick="document.form1.view_section.value='5';document.form1.submit();"><?=$lang[953]?></TD><?php }?>
	<TD class="tabpoolItemSpace">&nbsp;</TD>
	</TR></TABLE>

	</TD></TR>
	
	<TR><TD class="tabpoolfringe">
	<TABLE BORDER="0" cellspacing="1" cellpadding="2" width="100%" class="tabBody" style="height: 100%;">
	
	<TR><TD><B><?=$gview["viewname"]?></B><hr></TD></TR>
	
	<TR style="height: 100%; vertical-align: top;"><TD>
	
	<?php
	if($gview["viewdef"]){
		echo "<br><br>";
		$sRow = "style=\"border:1px solid grey\"";
		$sTable = "cellpadding=2 cellspacing=0 style=\"border-collapse:collapse\"";
		if($rs = @odbc_exec($db, lmb_paramTransView($viewid, $gview["viewdef"])) or lmb_questerror(odbc_errormsg($db),$gview["viewdef"])){
			echo ODBCResourceToHTML($rs, $sTable, $sRow, 1000);
		}
	}
	?>
	</TD></TR>
	<TR class="tabFooter"><TD></TD></TR>
	</TABLE>
	</TD></TR>
	</TABLE>

<?php
}elseif($view_section == 4){
	$gview = lmb_getQuestValue($viewid);
?>
	<TABLE class="tabpool" BORDER="0" cellspacing="0" cellpadding="0"><TR><TD>
	<TABLE BORDER="0" cellspacing="0" cellpadding="0" style="width: 100%; border-collapse: collapse;"><TR class="tabpoolItemTR">
	<TD nowrap class="tabpoolItemInactive" OnClick="document.form1.view_section.value='1';document.form1.submit();"><?=$lang[2026]?></TD>
	<TD nowrap class="tabpoolItemInactive" OnClick="document.form1.view_section.value='2';document.form1.submit();"><?=$lang[2612]?></TD>
	<TD nowrap class="tabpoolItemInactive" OnClick="document.form1.view_section.value='3';document.form1.submit();"><?=$lang[1739]?></TD>
	<TD nowrap class="tabpoolItemActive"><?=$lang[2795]?></TD>
    <?php if($gview["ispublic"]){?><TD nowrap class="tabpoolItemInactive" OnClick="document.form1.view_section.value='5';document.form1.submit();"><?=$lang[953]?></TD><?php }?>
	<TD class="tabpoolItemSpace">&nbsp;</TD>
	</TR></TABLE>

	</TD></TR>
	
	<TR><TD class="tabpoolfringe">
	<TABLE BORDER="0" cellspacing="1" cellpadding="2" width="100%" class="tabBody" style="height: 100%;">
	
	<TR><TD><B><?=$gview["viewname"]?></B><hr></TD></TR>
	
	<TR style="height: 100%; vertical-align: top;"><TD>
	
	<br>
	<table border="0" cellspacing="1" cellpadding="2">

    <tr><td width=150><?=$lang[1996]?></td><td valign="top"><?php if ($gview["ispublic"]) {echo "<i class=\"lmb-icon lmb-check\"></i>";}?></td></tr>
    <tr><td width=150><?=$lang[2023]?></td><td valign="top"><?php if ($gview["viewexists"]) {echo "<i class=\"lmb-icon lmb-check\"></i>";}?></td></tr>
    <tr><td width=150>Syntax</td><td valign="top"><?php if ($gview["isvalid"]) {echo "<i class=\"lmb-icon lmb-check\"></i>";}?></td></tr>
    <tr><td>&nbsp;</td></tr>
	<tr><td valign="top">Event</td><td valign="top"><textarea name="options[event]" style="width:500px;height:100px;"><?=htmlentities($gview["event"],ENT_QUOTES,$GLOBALS["umgvar"]["charset"])?></textarea></td></tr>
	<tr><td valign="top">Parameter</td><td valign="top"><textarea name="options[params]" style="width:500px;height:100px;"><?=htmlentities($gview["params"],ENT_QUOTES,$GLOBALS["umgvar"]["charset"])?></textarea></td></tr>
    </table>
	
	
	<TR class="tabBody"><TD><HR></TD></TR>
	<TR class="tabBody"><TD align="right"><input type="button" value="<?=$lang[842]?>" style="margin:5px;" OnClick="document.form1.options_save.value=1;document.form1.submit();">
	</TD></TR>
	<TR class="tabFooter"><TD></TD></TR>
	
	</TABLE>
	</TD></TR>
	</TABLE>
<?php
}elseif($view_section == 5){
    ?>
	<TABLE class="tabpool" BORDER="0" cellspacing="0" cellpadding="0"><TR><TD>
	<TABLE BORDER="0" cellspacing="0" cellpadding="0" style="width: 100%; border-collapse: collapse;"><TR class="tabpoolItemTR">
	<TD nowrap class="tabpoolItemInactive" OnClick="document.form1.view_section.value='1';document.form1.submit();"><?=$lang[2026]?></TD>
	<TD nowrap class="tabpoolItemInactive" OnClick="document.form1.view_section.value='2';document.form1.submit();"><?=$lang[2612]?></TD>
	<TD nowrap class="tabpoolItemInactive" OnClick="document.form1.view_section.value='3';document.form1.submit();"><?=$lang[1739]?></TD>
    <TD nowrap class="tabpoolItemInactive" OnClick="document.form1.view_section.value='4';document.form1.submit();"><?=$lang[2795]?></TD>
    <TD nowrap class="tabpoolItemActive"><?=$lang[953]?></TD>

	<TD class="tabpoolItemSpace">&nbsp;</TD>
	</TR></TABLE>

	</TD></TR>
    <TR><TD class="tabpoolfringe" id="lmbViewfieldContainer" style="height: 100%; padding: 0;">
    <iframe style="width:100%;height:100%" src="main_admin.php?action=setup_gtab_ftype&atid=<?=$viewid?>"></iframe>

    </TD></TR></TABLE>
	</TD></TR>
	</TABLE>
<?php }?>



</form>
</div>