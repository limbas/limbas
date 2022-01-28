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
 * ID: 121
 */
?>

<script language="JavaScript">



function create_view() {
	alert('<?=$lang[2214]?>');
}

function drop_view() {
	val = confirm('<?=$lang[2370]?>',' ');
	if(val){
		document.form1.drop_viev.value = 1;
	}
}

function LIM_relationTree(rel){
	val = confirm('<?=$lang[2856]?>',' ');
	if(val){
		document.form1.relationtree.value = rel;
		document.form1.submit();
	}
}

function LIM_deactivate(elid){
	if(document.getElementById("tab"+elid)){
		document.getElementById("tab"+elid).style.display = 'none';
	}
}


function LIM_activate(el,elid){
	
	LIM_deactivate('1');
	LIM_deactivate('2');
	LIM_deactivate('3');
	LIM_deactivate('4');
	LIM_deactivate('5');

	var el_ = el;
	if(!el){el = document.getElementById('menu'+elid);}
	
	limbasSetLayoutClassTabs(el,'tabpoolItemInactive','tabpoolItemActive');
	if(document.getElementById("tab"+elid)){
		document.getElementById("tab"+elid).style.display = '';
	}

	if(el_ && elid == 5){
	    document.form5.submit();
    }
}


function LIM_setDefault(){
	<?php
	if($category=="edit"){
		echo "LIM_activate(null,1);";
	}elseif($category=="create"){
		echo "LIM_activate(null,2);";
	}elseif($category=="relation"){
		echo "LIM_activate(null,3);";
	}elseif($category=="relparams"){
		echo "LIM_activate(null,4);";
	}elseif($category=="relationtree"){
		echo "LIM_activate(null,5);";
	}else{
		echo "LIM_activate(null,1);";
	}
	?>

}


function changeorder(el,id){
	
	maxsort = 0;
	$(".verknviewid").each(function(index) {
		if($( this ).prop( "checked" )){
			esort = $("#verknviewid_"+$(this).attr('elid')).text();
			if(esort > maxsort){maxsort = esort;}
		}
	});
	
	maxsort = parseInt(maxsort)+1;

	if(el.checked){
		document.getElementById("verknviewid_"+id).innerHTML = maxsort;
		document.getElementById("verknsort_"+id).value = maxsort;
	}else{
		document.getElementById("verknviewid_"+id).innerHTML = '';
		document.getElementById("verknsort_"+id).value = '';
	}
	
	

}


function changeorderf(el,id){
	
	maxsort = 0;
	$(".verknfindid").each(function(index) {
		if($( this ).prop( "checked" )){
			esort = $("#verknfindid_"+$(this).attr('elid')).text();
			if(esort > maxsort){maxsort = esort;}
		}
	});
	
	maxsort = parseInt(maxsort)+1;

	if(el.checked){
		document.getElementById("verknfindid_"+id).innerHTML = maxsort;
		document.getElementById("verknsortf_"+id).value = maxsort;
	}else{
		document.getElementById("verknfindid_"+id).innerHTML = '';
		document.getElementById("verknsortf_"+id).value = '';
	}
	
	

}

</SCRIPT>

<DIV>

<div style="position:absolute;top:10px;right:15px;">
<a target="new" href="<?=$LINK["help_url"][$LINK_ID[$action]]?>">
<i align="absmiddle" border="0" class="lmb-icon lmb-help"></i>
</a>
</div>


<TABLE class="tabpool" BORDER="0" cellspacing="0" cellpadding="0" width="80%"><TR><TD>

<TABLE BORDER="0" cellspacing="0" cellpadding="0"><TR>
<TD nowrap ID="menu1" OnClick="LIM_activate(this,'1')" class="tabpoolItemActive"><?=$lang[1634]?></TD>
<TD nowrap ID="menu3" OnClick="LIM_activate(this,'3')" class="tabpoolItemInactive"><?=$lang[2376]?></TD>
<TD nowrap ID="menu4" OnClick="LIM_activate(this,'4')" class="tabpoolItemInactive"><?=$lang[2331]?></TD>
<TD nowrap ID="menu5" OnClick="LIM_activate(this,'5')" class="tabpoolItemInactive"><?=$lang[3013]?></TD>
<?php if($rfield['verkntabletype'] == 1 AND $rfield['datatype'] != 25) {?><TD nowrap ID="menu2" OnClick="LIM_activate(this,'2')" class="tabpoolItemInactive"><?=$lang[1823]?></TD><?php }?>
<TD class="tabpoolItemSpace">&nbsp;</TD>
</TR></TABLE>

</TD></TR><TR><TD class="tabpoolfringe">




<FORM ACTION="main_admin.php" METHOD="post" name="form1">
<input type="hidden" name="action" VALUE="setup_verkn_editor">
<input type="hidden" name="tabid" VALUE="<?=$tabid?>">
<input type="hidden" name="fieldid" VALUE="<?=$fieldid?>">
<input type="hidden" name="drop_viev">
<input type="hidden" name="set_verknfieldid">
<input type="hidden" name="category" value="edit">
<input type="hidden" name="set_vparams">
<input type="hidden" name="relationtree">

<TABLE ID="tab1" BORDER="0" CELLPADDING="2" CELLSPACING="0" STYLE="width:100%;" class="tabBody">
<TR class="tabHeader"><TD COLSPAN="3">
<?php
echo $rfield['verkntabname'];
if($rfield['verkntabletype'] == 3){
	echo "&nbsp; &nbsp;&nbsp;$lang[2855]&nbsp;&nbsp;<i style=\"vertical-align:text-bottom\" class=\"lmb-icon lmb-switch\"></i>";
}elseif($rfield['verkntabletype'] == 2){
	echo "&nbsp;&nbsp;$lang[2371]&nbsp;&nbsp;<i style=\"vertical-align:text-bottom\" class=\"lmb-icon lmb-long-arrow-left\"></i> ";
}
?>
</TD></TR>
<TR><TD VALIGN="TOP">




<?php if(!$rfield['verkntabid'] AND $rfield['datatype'] == 23){?>

<TABLE BORDER="0" cellspacing="0" cellpadding="0" STYLE="width:100%;">

<?php

$sqlquery = "SELECT DISTINCT LMB_CONF_FIELDS.FIELD_ID, LMB_CONF_FIELDS.FIELD_NAME,LMB_CONF_FIELDS.TAB_ID, LMB_CONF_TABLES.TABELLE  FROM LMB_CONF_FIELDS,LMB_CONF_TABLES WHERE LMB_CONF_TABLES.TAB_ID = LMB_CONF_FIELDS.TAB_ID AND LMB_CONF_FIELDS.VERKNTABID = $tabid AND LMB_CONF_FIELDS.FIELD_TYPE = 11 AND LMB_CONF_FIELDS.VERKNTABLETYPE = 1 ORDER BY LMB_CONF_FIELDS.TAB_ID";

$rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
while(lmbdb_fetch_row($rs)) {
	echo "<TR><TD><INPUT TYPE=\"RADIO\" NAME=\"new_backview_verkn\" VALUE=\"".lmbdb_result($rs, "TAB_ID")."_".lmbdb_result($rs, "FIELD_ID")."\" STYLE=\"border:none;background-color:transparent;\" OnChange=\"document.form1.submit();\">&nbsp;<SPAN STYLE=\"color:$tcolor\">".dbf_4(lmbdb_result($rs, "TABELLE"))."&nbsp;(".dbf_4(lmbdb_result($rs, "FIELD_NAME")).")</SPAN></TD></TR>";
	$temp = lmbdb_result($rs, "TAB_ID");
}

?>
</TABLE>

<?php }elseif(!$rfield['verkntabid']){?>


<TABLE BORDER="0" cellspacing="0" cellpadding="0" STYLE="width:100%;">
<TR class="tabSubHeader"><TD VALIGN="TOP" COLSPAN="2" STYLE="height:20px;"><B><?=$lang[1824]?></B></TD></TR>
<?php

# back view relation filter
if($rfield['datatype'] == 23 AND $tabid){
    $bsqu = " AND LMB_CONF_TABLES.TAB_ID IN(SELECT LMB_CONF_FIELDS.TAB_ID FROM LMB_CONF_FIELDS WHERE LMB_CONF_FIELDS.VERKNTABID = $tabid AND LMB_CONF_FIELDS.FIELD_TYPE = 11 AND LMB_CONF_FIELDS.VERKNTABLETYPE = 1)";
}

$sqlquery = "SELECT DISTINCT LMB_CONF_TABLES.TAB_ID,LMB_CONF_TABLES.TAB_GROUP,LMB_CONF_TABLES.TABELLE,LMB_CONF_TABLES.BESCHREIBUNG,LMB_CONF_GROUPS.NAME,LMB_CONF_GROUPS.ID FROM LMB_CONF_TABLES,LMB_CONF_GROUPS WHERE LMB_CONF_TABLES.TAB_GROUP = LMB_CONF_GROUPS.ID $bsqu ORDER BY LMB_CONF_GROUPS.ID";
$rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
while(lmbdb_fetch_row($rs)) {
	if(lmbdb_result($rs, "NAME") != $temp){echo "<TR><TD BGCOLOR=\"".$farbschema['WEB7']."\">".$lang[lmbdb_result($rs, "NAME")]."</TD></TR>";}
	if(lmbdb_result($rs, "TAB_ID") == $tabid){$tcolor = "red";}else{$tcolor = $farbschema['WEB2'];}
	echo "<TR><TD><INPUT TYPE=\"RADIO\" NAME=\"new_verkntabid\" VALUE=\"".lmbdb_result($rs, "TAB_ID")."\" STYLE=\"border:none;background-color:transparent;\" OnChange=\"document.form1.submit();\">&nbsp;<SPAN STYLE=\"color:$tcolor\">".$lang[lmbdb_result($rs, "BESCHREIBUNG")]."&nbsp;(".lmbdb_result($rs, "TABELLE").")</SPAN></TD></TR>";
	$temp = lmbdb_result($rs, "NAME");
}

?>
</TABLE>

<?php }else{?>

<TABLE BORDER="0" cellspacing="0" cellpadding="0" STYLE="width:100%;">
<TR class="tabSubHeader">
<TD></TD>
<TD NOWRAP class="tabSubHeaderItem" ALIGN="CENTER" VALIGN="TOP" title="<?=$lang[2954]?>" style="cursor:help">&nbsp;<?=$lang[1825]?>&nbsp;</TD>
<TD NOWRAP class="tabSubHeaderItem" ALIGN="CENTER" VALIGN="TOP" title="<?=$lang[2808]?>" style="cursor:help">&nbsp;<?=$lang[1826]?>&nbsp;</TD>
<TD NOWRAP class="tabSubHeaderItem" ALIGN="CENTER" VALIGN="TOP" title="<?=$lang[2806]?>" style="cursor:help" colspan="2">&nbsp;<?=$lang[2089]?>&nbsp;</TD>
<TD NOWRAP class="tabSubHeaderItem" ALIGN="CENTER" VALIGN="TOP" title="<?=$lang[2807]?>" style="cursor:help" colspan="2">&nbsp;<?=$lang[1846]?>&nbsp;</TD>
</TR>
<?php



# --- ungültige Feldtypen ---
$wrong_fields = array(13,23);

// adding fields fom relation table
if($rfield['verknparams']) {
    $qo = " OR TAB_ID = ".$rfield['verknparams'];
}


# back view relation filter
if($rfield['datatype'] == 23){
    $qu = " AND LMB_CONF_FIELDS.FIELD_TYPE = 11 AND LMB_CONF_FIELDS.VERKNTABLETYPE = 1";
}

$sqlquery =  "SELECT SPELLING,DATA_TYPE,FIELD_TYPE,FIELD_ID,ARTLEISTE,VERKNTABID,VERKNTABLETYPE,FIELD_NAME FROM LMB_CONF_FIELDS WHERE (TAB_ID = ".$rfield['verkntabid']." $qo) $qu AND FIELD_TYPE < 100 ORDER BY TAB_ID,SORT";
$rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
if(!$rs) {$commit = 1;}

while(lmbdb_fetch_row($rs)) {
    $recrelation = null;
	#if(lmbdb_result($rs, "VERKNTABLETYPE") == 2){$bzm++;continue;} !!!!!!!!!!!!!!! rückwertige Verknüpfung - mögliche ENDLOSSCHLEIFE
    #if(lmbdb_result($rs, "FIELD_TYPE") == 11 AND $rfield['verkntree']){continue;}

    $field_id = lmbdb_result($rs, "FIELD_ID");
    if(lmbdb_result($rs, "FIELD_TYPE") == 11 OR lmbdb_result($rs, "VERKNTABLETYPE") == 2){$recrelation = 'font-style:italic;text-decoration:underline';}
    
	if(!in_array(lmbdb_result($rs, "DATA_TYPE"),$wrong_fields)){

		if($rfield['veknfieldid'] == $field_id){$checked = "CHECKED";}else{$checked = "";}
		if($rfield['verknsearchid']){if(in_array($field_id,$rfield['verknsearchid'])){$schecked = "CHECKED";}else{$schecked = "";}}else{$schecked = "";}
		if($rfield['verknfindid']){if(in_array($field_id,$rfield['verknfindid'])){$eschecked = "CHECKED";}else{$eschecked = "";}}else{$eschecked = "";}
		
		if($rfield['verknfindid']){
		    $sortkeyf = array_search($field_id,$rfield['verknfindid']);
		    if($sortkeyf !== false){
		        $sortkeyf++;$fchecked = "CHECKED";
		    }else{$fchecked = "";}
		}else{$fchecked = "";}
		
		if($rfield['verknviewid']){
		    $sortkey = array_search($field_id,$rfield['verknviewid']);
		    if($sortkey !== false){
		        $sortkey++;$gchecked = "CHECKED";
		    }else{$gchecked = "";}
		}else{$gchecked = "";}

		echo "<TR>";
		echo "<TD NOWRAP TITLE=\"".lmbdb_result($rs, "FIELD_NAME")."\" style=\"$recrelation\">";
		echo $lang[lmbdb_result($rs, "SPELLING")];
        #if (lmbdb_result($rs, 'FIELD_TYPE') == 11 AND lmbdb_result($rs, 'VERKNTABLETYPE') != 2 AND $gfield[$rfield['verkntabid']]['verkntabid'][$field_id] == $tabid) {
        #    echo '<i class="lmb-icon lmb-long-arrow-left"></i>';
        #}
		echo "</TD>";

		echo "<TD align=\"center\">";
		if(lmbdb_result($rs, "VERKNTABLETYPE") != 2 AND $field_id < 1000 AND lmbdb_result($rs, "FIELD_TYPE") != 11){echo "<INPUT TYPE=\"RADIO\" $vradio NAME=\"new_verknfieldid\" VALUE=\"".$field_id."\" STYLE=\"border:none;background-color:transparent;\" $checked OnChange=\"document.form1.set_verknfieldid.value=1;document.form1.submit()\">";}
		echo "</TD>";
		echo "<TD align=\"center\">";
		echo "<INPUT TYPE=\"CHECKBOX\" NAME=\"new_verknsearchid[".$field_id."]\" VALUE=\"".$field_id."\" STYLE=\"border:none;background-color:transparent;\" $schecked>";
		echo "</TD>";
		
		echo "<TD align=\"center\">";
		if(lmbdb_result($rs, "VERKNTABLETYPE") != 2 AND $field_id < 1000){echo "<INPUT TYPE=\"CHECKBOX\" class=\"verknfindid\" elid=\"".$field_id."\" NAME=\"new_verknfindid[".$field_id."]\" VALUE=\"".$field_id."\" STYLE=\"border:none;background-color:transparent;\" $eschecked onchange=\"changeorderf(this,".$field_id.")\">";}
		echo "</TD><TD><span id=\"verknfindid_".$field_id."\" style=\"color:green\">";
		if($fchecked){echo '('.$sortkeyf.')';}
		echo "</span><input type=\"hidden\" id=\"verknsortf_".$field_id."\" name=\"verknsortf[".$field_id."]\" value=\"".$sortkeyf."\">
		</TD>";
		
		echo "<TD align=\"center\"><INPUT TYPE=\"CHECKBOX\" class=\"verknviewid\" elid=\"".$field_id."\" NAME=\"new_verknviewid[".$field_id."]\" VALUE=\"".$field_id."\" STYLE=\"border:none;background-color:transparent;\" $gchecked onchange=\"changeorder(this,".$field_id.")\"></TD>";
		echo "<TD><span id=\"verknviewid_".$field_id."\" style=\"color:green\">";
		if($gchecked){echo '('.$sortkey.')';}
		echo "</span><input type=\"hidden\" id=\"verknsort_".$field_id."\" name=\"verknsort[".$field_id."]\" value=\"".$sortkey."\">
		</TD>";
		echo "</TR>";
	}
	
}
?>


<tr><td colspan="3" align="right"><i><?=$lang[2595]?></i></td>
<td align="center"><input type="text" name="findidCut" style="width:30px;margin:3px;" value="<?=$rfield['findidcut']?>"></td>
<td></td>
<td align="center">
<input type="text" name="viewidCut" style="width:30px;margin:3px;" value="<?=$rfield['viewidcut']?>">
</td></tr>

<?php /*
<tr><td colspan="3" align="right"><i><?=$lang[2595]?></i></td>
<td colspan="6" align="center">

</td></tr>

*/?>


</TABLE>
<?php }?>

</TD></TR>


<?php if($rfield['verkntabletype'] == 1 AND $rfield['verkntabid'] AND $rfield['datatype'] != 23) {?>

<TR><TD><HR></TD></TR>
<TR><TD>
<table width="100%"><tr><td width="50%" valign="top">
<?=$lang[941]?><BR>
<INPUT TYPE="CHECKBOX" STYLE="border:none;background-color:transparent;" NAME="new_refint" <?php if($rfield['refint']){echo "CHECKED";}?>>&nbsp;
<SELECT NAME="new_refint_rule">
<OPTION VALUE="1" <?php if($refint_rule == "DELETE RESTRICT"){echo "SELECTED";}?>>DELETE RESTRICT
</SELECT>
<BR>
<?php if($refint_rule){echo "( $f1 | $f2 )";}?>
</td><td valign="top">

<?php
if($rfield['datatype'] != 25) {
echo $lang[2809].'<br>';

?>
<INPUT TYPE="CHECKBOX" STYLE="border:none;background-color:transparent;" NAME="new_vparams" <?php if($rfield['verknparams']){echo "CHECKED";}?> onchange="document.form1.set_vparams.value=1">&nbsp;<?php if($rfield['verknparams']){echo "(ID: ".$rfield['verknparams'].")";}?><br>
<span style="color:green;font-style:italic"><?= $GLOBALS['message'] ?></span>
<?php } ?>
</td></tr></table>

</TD></TR>
<?php }?>
<TR><TD><hr></TD></TR>


<TR><TD COLSPAN="3" align="center"><INPUT TYPE="SUBMIT" VALUE="<?=$lang[33]?>" NAME="vknsave"></TD></TR>
<TR><TD>&nbsp;</TD></TR>
<TR><TD COLSPAN="5"><?=$message1?></TD></TR>
</TABLE>
</FORM>





<?php

if($fieldid AND $tabid AND $rfield['verkntabid'] AND $rfield['datatype'] != 23){?>

<FORM ACTION="main_admin.php" METHOD="post" NAME="form2">
<input type="hidden" name="action" value="setup_verkn_editor">
<input type="hidden" name="fieldid" value="<?=$fieldid;?>">
<input type="hidden" name="tabid" value="<?=$tabid?>">
<input type="hidden" name="category" value="create">

<TABLE ID="tab2" STYLE="display:none;width:100%;" BORDER="0" CELLPADDING="0" CELLSPACING="0" WIDTH="100%">
<TR><TD COLSPAN="3"><?=$message2?></TD></TR>
<TR><TD><B><?=$rfield['tabname']?></B><BR><BR><SELECT NAME="v_field1" SIZE="20"><OPTION VALUE="ID" SELECTED>ID
<?php
$sqlquery =  "SELECT DISTINCT FIELD_NAME FROM LMB_CONF_FIELDS WHERE TAB_ID = $tabid AND FIELD_TYPE < 100 AND FIELD_NAME != 'ID'";
$rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
if(!$rs) {$commit = 1;}
while(lmbdb_fetch_row($rs)) {
	echo "<OPTION VALUE=\"".lmbdb_result($rs, "FIELD_NAME")."\">".lmbdb_result($rs, "FIELD_NAME");
}
?>
</SELECT></TD><TD>&nbsp;</TD><TD><B><?=$rfield['verkntabname']?></B><BR><BR><SELECT NAME="v_field2" SIZE="20"><OPTION VALUE="ID" SELECTED>ID
<?php
$sqlquery =  "SELECT DISTINCT FIELD_NAME FROM LMB_CONF_FIELDS WHERE TAB_ID = ".$rfield['verkntabid']." AND FIELD_TYPE < 100 AND FIELD_NAME != 'ID'";
$rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
if(!$rs) {$commit = 1;}
while(lmbdb_fetch_row($rs)) {
	echo "<OPTION VALUE=\"".lmbdb_result($rs, "FIELD_NAME")."\">".lmbdb_result($rs, "FIELD_NAME");
}
?>
</SELECT></TD></TR>
<TR><TD COLSPAN="3" HEIGHT="20">&nbsp;</TD></TR>
<TR><TD COLSPAN="3" align="center"><INPUT TYPE="SUBMIT" VALUE="<?=$lang[1303]?>" NAME="verknrefresh"></TD></TR>
</TABLE>
</FORM>
<?php }?>







<?php if($fieldid AND $tabid AND $rfield['verkntabid'] AND $rfield['datatype'] != 23){?>

<FORM ACTION="main_admin.php" METHOD="post" NAME="form3">
<input type="hidden" name="action" value="setup_verkn_editor">
<input type="hidden" name="tabid" value="<?=$tabid?>">
<input type="hidden" name="fieldid" value="<?=$fieldid;?>">
<input type="hidden" name="relation_extension" value="1">
<input type="hidden" name="category" value="relation">

<TABLE ID="tab3" STYLE="display:none;width:100%;" BORDER="0" CELLPADDING="0" CELLSPACING="0" WIDTH="100%"><TR><TD align="center">
<TABLE BORDER="0" CELLPADDING="1" CELLSPACING="2" WIDTH="80%">
    <TR BGCOLOR="<?=$farbschema['WEB3']?>"><TD style="cursor:pointer" OnClick="document.getElementById('relation_verknview').style.display = '';"><i class="lmb-icon lmb-caret-square-down-alt"></i></TD><TD><b>&nbsp;<?=$lang[2377]?></b></TD></TR>
</TABLE>
<TABLE BORDER="0" CELLPADDING="1" CELLSPACING="2" WIDTH="80%" ID="relation_verknview" style="display:none">
<?php
rec_verknpf_tabs($tabid,$verkntab);
?>
<TR><TD colspan="3"><TEXTAREA style="width:100%;height:50px;background-color:#bfbfbf;color:#000000" ID="relation_preview" readonly OnDblClick="document.getElementById('relation_value').value=this.value"></TEXTAREA><hr></TD></TR>

</TABLE>


<TEXTAREA style="width:80%;height:200px;" ID="relation_value" NAME="relation_value"><?=htmlentities($rfield['relext'],ENT_QUOTES,$umgvar["charset"]);?></TEXTAREA>

<div style="width:80%;background-color:#BFBFBF;color:#000000;text-align:left">
<b>examples:</b><br>
    $extension['where'][] = "VERK_52131C929EFDD.ID = ".$verkn["id"]." AND ....<br>
	$extension['from'][] = "VERK_52131C929EFDD";<br>
	$extension['select'][] = 'CONTACTS.NAME, CONTACTS.GIVENNAME';<br>
	$extension['order'][] = 'CONTACTS.NAME ASC, CONTACTS.GIVENNAME ASC';<br>
	$extension['ojoin'][] = '';<br>
	...<br>
	$gsr = array();<br>
	...<br>
	$filter['order'][$gtabid][0] = array($gtabid,2,'ASC');<br>
	...<br>
	return myExt_function();
</div>


</TD></TR>
<TR><TD COLSPAN="3" HEIGHT="20">&nbsp;</TD></TR>
<TR><TD COLSPAN="3" align="center"><INPUT TYPE="SUBMIT" VALUE="<?=$lang[33]?>"></TD></TR>
</TABLE>
</FORM>
<?php }?>


<FORM ACTION="main_admin.php" METHOD="post" NAME="form4">
<input type="hidden" name="action" value="setup_verkn_editor">
<input type="hidden" name="tabid" value="<?=$tabid?>">
<input type="hidden" name="fieldid" value="<?=$fieldid;?>">
<input type="hidden" name="relation_parameter" value="1">
<input type="hidden" name="category" value="relparams">
<TABLE ID="tab4" STYLE="display:none;width:100%;" BORDER="0" CELLPADDING="0" CELLSPACING="0" WIDTH="100%">

<TR><TD align="center">
<?php
    edit_relationparams($tabid,$fieldid);
?>
</TD></TR>
<TR><TD COLSPAN="3" HEIGHT="20">&nbsp;</TD></TR>
<TR><TD COLSPAN="3" align="center"><INPUT TYPE="SUBMIT" VALUE="<?=$lang[33]?>"></TD></TR>
</TABLE>
</FORM>



<FORM ACTION="main_admin.php" METHOD="post" NAME="form5">
<input type="hidden" name="action" value="setup_verkn_editor">
<input type="hidden" name="tabid" value="<?=$tabid?>">
<input type="hidden" name="fieldid" value="<?=$fieldid;?>">
<input type="hidden" name="category" value="relationtree">
<TABLE ID="tab5" STYLE="display:none;width:100%;" BORDER="0" CELLPADDING="0" CELLSPACING="0" WIDTH="100%">
<TR><TD>

<?php
if($category == 'relationtree') {
    relationtree($rfield);
}
?>

</TD></TR>
<TR><TD COLSPAN="3" HEIGHT="20">&nbsp;</TD></TR>
<TR><TD COLSPAN="3" align="center"><INPUT TYPE="SUBMIT" VALUE="<?=$lang[33]?>"></TD></TR>
</TABLE>
</FORM>


</TD></TR></TABLE>
</TD></TR></TABLE>
</TD></TR></TABLE></DIV>



<script language="JavaScript">
LIM_setDefault();
</SCRIPT>









<?php





function relationtree($rfield){
    global $lang;
    global $gfield;
    global $gtab;

    if(($rfield['verkntabletype'] == 1 OR $rfield['verkntabletype'] == 3) AND $rfield['datatype'] != 25 AND $tree = recrelationtree()){

        echo "<br>";
        echo $lang[2855];

        foreach($tree as $tkey => $path){
            $tree_identifier = md5(implode(",",$path));
            $CHECKED = '';
            if($rfield['verkntree'] == $tree_identifier){$CHECKED = 'CHECKED';}
            echo "<div><input type=\"checkbox\" onclick=\"LIM_relationTree('".implode(",",$path)."')\" $CHECKED>";
            $tabname = array();

            foreach($path as $key => $md5tab){
                $vTabID = getTabFromMd5($md5tab);

                if (isset($path[$key - 1])) {
                    $lastTab = getTabFromMd5($path[$key - 1]);
                } else {
                    $lastTab = $tabid;
                }

                $fieldName = '';
                foreach($gfield[$lastTab]['md5tab'] as $fieldKey => $md5) {
                    if ($md5 == $md5tab) {
                         $fieldName = $gfield[$lastTab]['field_name'][$fieldKey];
                         break;
                    }
                }
                $tabname[] = "<span title=\"{$fieldName}\">{$gtab['table'][$vTabID]}</span>";
            }
            echo '&rarr;' . implode(" &rarr; ",$tabname);
            echo '</div>';
        }
    }
}


function rec_verknpf_tabs($gtabid,$verkntab){
	static $recmd5;
	global $gfield;
	global $gtab;

	if(!$recmd5){$recmd5 = array();}
	if($gfield[$gtabid]["sort"]){
	foreach ($gfield[$gtabid]["sort"] as $key => $value){
		if($gfield[$gtabid]["field_type"][$key] == 11){

			if($gtabid == $verkntab){return;}
			if(in_array($gfield[$gtabid]["md5tab"][$key],$recmd5)){return;}
			$recmd5[] = $gfield[$gtabid]["md5tab"][$key];

			if($gfield[$gtabid]["verkntabletype"][$key] == 1){
				echo "</td><td style=\"color:blue;cursor:pointer\" OnCLick=\"document.getElementById('relation_preview').value='".$gtab["table"][$gtabid].".ID = ".lmb_strtoupper($gfield[$gtabid]["md5tab"][$key]).".ID \\nAND \\n".lmb_strtoupper($gfield[$gtabid]["md5tab"][$key]).".VERKN_ID = ".$gtab["table"][$gfield[$gtabid]["verkntabid"][$key]].".ID';\">".$gtab["desc"][$gtabid]."</td><td><i class=\"lmb-icon lmb-long-arrow-right\"></i></td><td>".$gtab["desc"][$gfield[$gtabid]["verkntabid"][$key]]."</td></tr>";
			}else{
				echo "</td><td style=\"color:blue;cursor:pointer\" OnCLick=\"document.getElementById('relation_preview').value='".$gtab["table"][$gtabid].".ID = ".lmb_strtoupper($gfield[$gtabid]["md5tab"][$key]).".ID \\nAND \\n".lmb_strtoupper($gfield[$gtabid]["md5tab"][$key]).".VERKN_ID = ".$gtab["table"][$gfield[$gtabid]["verkntabid"][$key]].".ID';\">".$gtab["desc"][$gtabid]."</td><td><i class=\"lmb-icon lmb-long-arrow-left\"></i></td><td>".$gtab["desc"][$gfield[$gtabid]["verkntabid"][$key]]."</td></tr>";
			}

			rec_verknpf_tabs($gfield[$gtabid]["verkntabid"][$key],$verkntab);
		}
	}}
	return;
}


function edit_relationparams($tabid,$fieldid){
    global $lang;

    $params = getRelationParameter($tabid,$fieldid);

    foreach ($params as $key => $value){
        if($value == 1){
            ${$key} = 'checked';
        }
    }

    ${'show_inframe_'.$params['show_inframe']} = 'selected';
    ${'viewmode_'.$params['viewmode']} = 'selected';
    ${'validity_'.$params['validity']} = 'selected';

    $show_inframe_mods = array('div','iframe','same','tab');
    if($params['show_inframe'] AND !in_array($params['show_inframe'],$show_inframe_mods)){$show_inframe_tag = 'selected';}else{$params['show_inframe'] = null;}
    if($params['show_inframe'] OR $show_inframe_tag){$inframe_tag_display = '';}else{$inframe_tag_display = 'none';}

    echo "
    <table
    <tr><td><i>{$lang[3038]}</i></td><td>
        <select name=\"params[show_inframe]\" onchange=\"if(this.value == 'tag'){document.getElementById('inframe_tag').style.display='';}\"><option>
        <option value=\"div\" $show_inframe_div>div
        <option value=\"iframe\" $show_inframe_iframe>iframe
        <option value=\"same\" $show_inframe_same>same
        <option value=\"tab\" $show_inframe_tab>new tab
        <option value=\"tag\" $show_inframe_tag>tag (Element-ID)
        </select>&nbsp;
    <input style=\"display:$inframe_tag_display\" id=\"inframe_tag\" type=\"text\" name=\"params[show_inframe_tag]\" size=\"5\" value=\"".htmlentities($params['show_inframe'],ENT_QUOTES)."\">
    </td></tr>
    <tr><td><i>{$lang[3014]}</i></td><td><select name=\"params[viewmode]\">
        <option>
        <option value=\"dropdown\" $viewmode_dropdown>dropdown
        <option value=\"single_ajax\" $viewmode_single_ajax>single_ajax
        <option value=\"multi_ajax\" $viewmode_multi_ajax>multi_ajax
        </select>
    </td></tr>
    <tr><td><i>{$lang[3002]}</i></td><td><select name=\"params[validity]\">
        <option>
        <option value=\"all\" $validity_all>all
        <option value=\"allto\" $validity_allto>all from today
        <option value=\"allfrom\" $validity_allfrom>all to today
        </select>
    </td></tr>
        
    <tr><td><i>{$lang[3015]}</i></td><td><input type=\"text\" name=\"params[formid]\" value=\"".htmlentities($params['formid'],ENT_QUOTES)."\"></td></tr>
    <tr><td><i>{$lang[3049]}</i></td><td><input type=\"text\" name=\"params[formsize]\" value=\"".htmlentities($params['formsize'],ENT_QUOTES)."\"></td></tr>
    <tr><td><i>{$lang[3016]}</i></td><td><input type=\"text\" name=\"params[count]\" value=\"".htmlentities($params['count'],ENT_QUOTES)."\"></td></tr>
    <tr><td><i>{$lang[3017]}</i></td><td><input type=\"text\" name=\"params[ondblclick]\" value=\"".htmlentities($params['ondblclick'],ENT_QUOTES)."\"></td></tr>
    <tr><td><i>{$lang[3039]}</i></td><td><input type=\"text\" name=\"params[showfields]\" value=\"".htmlentities($params['showfields'],ENT_QUOTES)."\"></td></tr>
    <tr><td><i>{$lang[3041]}</i></td><td><input type=\"text\" name=\"params[edit]\" value=\"".htmlentities($params['edit'],ENT_QUOTES)."\"></td></tr>
    <tr><td><i>{$lang[3040]}</i></td><td><input type=\"text\" name=\"params[width]\" value=\"".htmlentities($params['width'],ENT_QUOTES)."\"></td></tr>
    <tr><td><i>{$lang[3042]}</i></td><td><input type=\"text\" name=\"params[order]\" value=\"".htmlentities($params['order'],ENT_QUOTES)."\"></td></tr>
    <tr><td><i>{$lang[3070]}</i></td><td><input type=\"checkbox\" name=\"params[applyfilter]\" value=\"1\" $applyfilter></td></tr>
    <tr><td><i>{$lang[3029]}</i></td><td><input type=\"checkbox\" name=\"params[no_menu]\" value=\"1\" $no_menu></td></tr>
    <tr><td><i>{$lang[3018]}</i></td><td><input type=\"checkbox\" name=\"params[no_add]\" value=\"1\" $no_add></td></tr>
    <tr><td><i>{$lang[3019]}</i></td><td><input type=\"checkbox\" name=\"params[no_new]\" value=\"1\" $no_new></td></tr>
    <tr><td><i>{$lang[3020]}</i></td><td><input type=\"checkbox\" name=\"params[no_edit]\" value=\"1\" $no_edit></td></tr>
    <tr><td><i>{$lang[3021]}</i></td><td><input type=\"checkbox\" name=\"params[no_replace]\" value=\"1\" $no_replace></td></tr>
    <tr><td><i>{$lang[3022]}</i></td><td><input type=\"checkbox\" name=\"params[no_search]\" value=\"1\" $no_search></td></tr>
    <tr><td><i>{$lang[3023]}</i></td><td><input type=\"checkbox\" name=\"params[no_copy]\" value=\"1\" $no_copy></td></tr>
    <tr><td><i>{$lang[3024]}</i></td><td><input type=\"checkbox\" name=\"params[no_delete]\" value=\"1\" $no_delete></td></tr>
    <tr><td><i>{$lang[3025]}</i></td><td><input type=\"checkbox\" name=\"params[no_sort]\" value=\"1\" $no_sort></td></tr>
    <tr><td><i>{$lang[3026]}</i></td><td><input type=\"checkbox\" name=\"params[no_link]\" value=\"1\" $no_link></td></tr>
    <tr><td><i>{$lang[3027]}</i></td><td><input type=\"checkbox\" name=\"params[no_openlist]\" value=\"1\" $no_openlist></td></tr>
    <tr><td><i>{$lang[3028]}</i></td><td><input type=\"checkbox\" name=\"params[no_fieldselect]\" value=\"1\" $no_fieldselect></td></tr>
    <tr><td><i>{$lang[3051]}</i></td><td><input type=\"checkbox\" name=\"params[no_validity]\" value=\"1\" $no_validity></td></tr>
    <tr><td><i>{$lang[3030]}</i></td><td><input type=\"checkbox\" name=\"params[search]\" value=\"1\" $search></td></tr>
    <tr><td><i>{$lang[3031]}</i></td><td><input type=\"checkbox\" name=\"params[showall]\" value=\"1\" $showall></td></tr>
    <tr><td><i>{$lang[3032]}</i></td><td><input type=\"checkbox\" name=\"params[getlongval]\" value=\"1\" $getlongval></td></tr>
    <tr><td><i>{$lang[3033]}</i></td><td><input type=\"checkbox\" name=\"params[nogresult]\" value=\"1\" $nogresult></td></tr>
    <tr><td><i>{$lang[3034]}</i></td><td><input type=\"checkbox\" name=\"params[no_calendar]\" value=\"1\" $no_calendar></td></tr>
    <tr><td><i>{$lang[3035]}</i></td><td><input type=\"checkbox\" name=\"params[pagination]\" value=\"1\" $pagination></td></tr>
    <tr><td><i>{$lang[3036]}</i></td><td><input type=\"checkbox\" name=\"params[indicator]\" value=\"1\" $indicator></td></tr>
    <tr><td><i>{$lang[3037]}</i></td><td><input type=\"checkbox\" name=\"params[show_relationpath]\" value=\"1\" $show_relationpath></td></tr>
    
    </table>
    ";



}





?>