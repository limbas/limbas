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
 * ID: 225
 */

?>

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
	
	document.getElementById("ZoomFieldArea").value = el.value;
	limbasDivShow(el,null,'ZoomFieldContainer');

	return false;
}

function ZoomViewFieldUpdate(event,el) {
	
	zoomEl.value = el.value;
	lmbAjax_EditViewfield(event,zoomKey,'field','key_'+zoomKey,zoomKey);
	
	zoomKey = null;
	zoomEl = null;
	
	divclose();
}


$(function() {
	$('#vieweditorPattern').height(($( window ).height()) - 340);
});


</script>


<div id="ZoomFieldContainer" class="ajax_container" style="position:absolute;visibility:hidden;z-index:999;" onclick="activ_menu=1;">
<i class="lmb-icon lmb-close-alt" style="position:absolute;right:5px;cursor:pointer" onclick="divclose();"></i><br>
<textarea id="ZoomFieldArea" style="width:250px;height:150px;overflow:auto;" onchange="ZoomViewFieldUpdate(event,this);"></textarea>
</div>

<div class="lmbPositionContainerMainTabPool" style="height:100%">

<FORM ACTION="main_admin.php" METHOD="post" name="form1">
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
<?php



if($view_preview){$view_section = 3;}
if(!$view_section){$view_section = 1;}

# save view options
if($options_save){
	lmb_QuestOptions($viewid,$options);
}

if($view_section == 1){
	$gview = lmb_getQuestValue($viewid);

	# save view definition
	if($view_save){
		if($view_public){
			lmb_createQuestView($viewid,$view_def,$view_public,1,$view_drop);
		}elseif (!$view_public AND $gview["ispublic"]){
			lmb_createQuestView($viewid,$view_def,0,1,$view_drop);
		}else {
			lmb_createQuestView($viewid,$view_def,0,0,$view_drop);
		}
		$gview["viewdef"] = $view_def;
		$gview["viewdrop"] = $view_drop;
		$gview["ispublic"] = $view_public;
	}
	
	?>
	<TABLE class="tabpool" BORDER="0" width="95%" cellspacing="0" cellpadding="0"><TR><TD>
	<TABLE BORDER="0" cellspacing="0" cellpadding="0" width="100%"><TR class="tabpoolItemTR">
	<TD nowrap class="tabpoolItemActive"><?=$lang[2613]?></TD>
	<TD nowrap class="tabpoolItemInactive" OnClick="document.form1.view_section.value='2';document.form1.submit();"><?=$lang[2612]?></TD>
	<TD nowrap class="tabpoolItemInactive" OnClick="document.form1.view_section.value='3';document.form1.submit();"><?=$lang[2616]?></TD>
	<TD nowrap class="tabpoolItemInactive" OnClick="document.form1.view_section.value='4';document.form1.submit();"><?=$lang[2755]?></TD>
	<TD class="tabpoolItemSpace">&nbsp;
	<?if(1){echo "&nbsp;<a href=\"main_admin.php?&action=setup_gtab_ftype&atid=$viewid\"><i border=\"0\" style=\"cursor:pointer\" class=\"lmb-icon lmb-pencil\"></i></a>";}?>
	</TD>
	</TR></TABLE>

	</TD></TR>
	
	<TR><TD class="tabpoolfringe">
	<TABLE BORDER="0" cellspacing="1" cellpadding="2" width="100%" class="tabBody">
	
	<TR><TD><B><?=$gview["viewname"]?></B></TD></TR>
	
	<TR><TD>
	<textarea name="view_def" style="width:100%;height:300px;background-color:<?=$farbschema["WEB8"]?>"><?=htmlentities($gview["viewdef"],ENT_QUOTES,$GLOBALS["umgvar"]["charset"])?></textarea></TD></TR>

	<TR class="tabBody"><TD><HR></TD></TR>
	<TR class="tabBody"><TD align="right">
	<?=$lang[2615]?> <input type="checkbox" NAME="view_public" <?if($gview["ispublic"]){echo "checked";}?>>&nbsp;&nbsp;
	<?=$lang[2460]?> <input type="checkbox" NAME="view_drop" <?if($gview["viewdrop"]){echo "checked";}?>>
	&nbsp;&nbsp;<input type="submit" NAME="view_save" value="<?=$lang[2614]?>">
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
	
	$gview = lmb_getQuestValue($viewid);
	# save view definition
	if($view_save){
		if($view_def = lmb_questCreateSQL($viewid)){
			if($view_public){
				lmb_createQuestView($viewid,$view_def,$view_public,1,$view_drop);
			}elseif (!$view_public AND $gview["ispublic"]){
				lmb_createQuestView($viewid,$view_def,0,1,$view_drop);
			}else {
				lmb_createQuestView($viewid,$view_def,0,0,$view_drop);
			}
			$gview["viewdef"] = $view_def;
			$gview["viewdrop"] = $view_drop;
			$gview["ispublic"] = $view_public;
		}
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

<TABLE class="tabpool" BORDER="0" width="98%" height="95%" cellspacing="0" cellpadding="0"><TR><TD>
<TABLE BORDER="0" cellspacing="0" cellpadding="0" width="100%"><TR class="tabpoolItemTR">
<TD nowrap class="tabpoolItemInactive" OnClick="document.form1.view_section.value='1';document.form1.submit();"><?=$lang[2613]?></TD>
<TD nowrap class="tabpoolItemActive"><?=$lang[2612]?></TD>
<TD nowrap class="tabpoolItemInactive" OnClick="document.form1.view_section.value='3';document.form1.submit();"><?=$lang[2616]?></TD>
<TD nowrap class="tabpoolItemInactive" OnClick="document.form1.view_section.value='4';document.form1.submit();"><?=$lang[2755]?></TD>
<TD class="tabpoolItemSpace">&nbsp;</TD>
</TR>
</TABLE>

</TD></TR><TR><TD class="tabpoolfringe" height="100%">

<TABLE BORDER="0" cellspacing="1" cellpadding="2" width="100%" height=100% class="tabBody">

<TR><TD><B><?=$gview["viewname"]?></B></TD></TR>

<TR><TD height="100%">
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
<?=$lang[2615]?> <input type="checkbox" NAME="view_public" <?if($gview["ispublic"]){echo "checked";}?>>&nbsp;
	<?=$lang[2460]?> <input type="checkbox" NAME="view_drop" <?if($gview["viewdrop"]){echo "checked";}?>>
&nbsp;&nbsp;<input type="button" value="<?=$lang[2614]?>" style="margin:5px;" OnClick="setDrag();document.form1.view_save.value=1;document.form1.submit();">
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
	<TABLE class="tabpool" BORDER="0" width="95%" cellspacing="0" cellpadding="0"><TR><TD>
	<TABLE BORDER="0" cellspacing="0" cellpadding="0" width="100%"><TR class="tabpoolItemTR">
	<TD nowrap class="tabpoolItemInactive" OnClick="document.form1.view_section.value='1';document.form1.submit();"><?=$lang[2613]?></TD>
	<TD nowrap class="tabpoolItemInactive" OnClick="document.form1.view_section.value='2';document.form1.submit();"><?=$lang[2612]?></TD>
	<TD nowrap class="tabpoolItemActive"><?=$lang[2616]?></TD>
	<TD nowrap class="tabpoolItemInactive" OnClick="document.form1.view_section.value='4';document.form1.submit();"><?=$lang[2755]?></TD>
	<TD class="tabpoolItemSpace">&nbsp;</TD>
	</TR></TABLE>

	</TD></TR>
	
	<TR><TD class="tabpoolfringe">
	<TABLE BORDER="0" cellspacing="1" cellpadding="2" width="100%" class="tabBody">
	
	<TR><TD><B><?=$gview["viewname"]?></B><hr></TD></TR>
	
	<TR><TD>
	
	<?php
	if($gview["viewdef"]){
		echo "<br><br>";
		$sRow = "style=\"border:1px solid grey\"";
		$sTable = "cellpadding=2 cellspacing=0 style=\"border-collapse:collapse\"";
		if($rs = @odbc_exec($db,$gview["viewdef"]) or lmb_questerror(odbc_errormsg($db),$gview["viewdef"])){
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
	<TABLE class="tabpool" BORDER="0" width="95%" cellspacing="0" cellpadding="0"><TR><TD>
	<TABLE BORDER="0" cellspacing="0" cellpadding="0" width="100%"><TR class="tabpoolItemTR">
	<TD nowrap class="tabpoolItemInactive" OnClick="document.form1.view_section.value='1';document.form1.submit();"><?=$lang[2613]?></TD>
	<TD nowrap class="tabpoolItemInactive" OnClick="document.form1.view_section.value='2';document.form1.submit();"><?=$lang[2612]?></TD>
	<TD nowrap class="tabpoolItemInactive" OnClick="document.form1.view_section.value='3';document.form1.submit();"><?=$lang[2616]?></TD>
	<TD nowrap class="tabpoolItemActive"><?=$lang[2755]?></TD>
	<TD class="tabpoolItemSpace">&nbsp;</TD>
	</TR></TABLE>

	</TD></TR>
	
	<TR><TD class="tabpoolfringe">
	<TABLE BORDER="0" cellspacing="1" cellpadding="2" width="100%" class="tabBody">
	
	<TR><TD><B><?=$gview["viewname"]?></B><hr></TD></TR>
	
	<TR><TD>
	
	<br>
	<table border="0" cellspacing="1" cellpadding="2">
	<tr><td width=150><?=$lang[2615]?></td><td valign="top"><input type="checkbox" NAME="view_public" disabled <?if($gview["ispublic"]){echo "checked";}?>></td></tr>
	<tr><td valign="top">Event</td><td valign="top"><textarea name="options[event]" style="width:400px;height:150px;"><?=htmlentities($gview["event"],ENT_QUOTES,$GLOBALS["umgvar"]["charset"])?></textarea></td></tr>	
	</table>
	
	
	<TR class="tabBody"><TD><HR></TD></TR>
	<TR class="tabBody"><TD align="right"><input type="button" value="<?=$lang[2614]?>" style="margin:5px;" OnClick="document.form1.options_save.value=1;document.form1.submit();">
	</TD></TR>
	<TR class="tabFooter"><TD></TD></TR>
	
	</TABLE>
	</TD></TR>
	</TABLE>
<?php
}
?>



</form>
</div>