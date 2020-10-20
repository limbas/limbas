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
	
	if(!el){el = document.getElementById('menu'+elid);}
	
	limbasSetLayoutClassTabs(el,'tabpoolItemInactive','tabpoolItemActive');
	if(document.getElementById("tab"+elid)){
		document.getElementById("tab"+elid).style.display = '';
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
	}elseif($category=="extfields"){
		echo "LIM_activate(null,4);";
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
<?php if($verkntabletype == 1) {?><TD nowrap ID="menu2" OnClick="LIM_activate(this,'2')" class="tabpoolItemInactive"><?=$lang[1823]?></TD><?php }?>
<TD class="tabpoolItemSpace">&nbsp;</TD>
</TR></TABLE>

</TD></TR><TR><TD class="tabpoolfringe">




<FORM ACTION="main_admin.php" METHOD="post" name="form1">
<input type="hidden" name="action" VALUE="setup_verkn_editor">
<input type="hidden" name="tabid" VALUE="<?=$tabid?>">
<input type="hidden" name="verkntabid" VALUE="<?=$verkntabid?>">
<input type="hidden" name="fieldid" VALUE="<?=$fieldid?>">
<input type="hidden" name="drop_viev">
<input type="hidden" name="set_verknfieldid">
<input type="hidden" name="category" value="edit">
<input type="hidden" name="set_vparams">
<input type="hidden" name="relationtree">

<TABLE ID="tab1" BORDER="0" CELLPADDING="2" CELLSPACING="0" STYLE="width:480px;" class="tabBody">
<TR class="tabHeader"><TD COLSPAN="3">
<?php
echo $verkntabdesc;
if($verkntabletype == 3){
	echo "&nbsp;<I>(<?=$lang[2371]?>)</I> &nbsp;&nbsp;$lang[2855]&nbsp;&nbsp;<i style=\"vertical-align:text-bottom\" class=\"lmb-icon lmb-switch\"></i>";
}elseif($verkntabletype == 2){
	echo "&nbsp;<I>(<?=$lang[2371]?>)</I> &nbsp;&nbsp;$lang[2371]&nbsp;&nbsp;<i style=\"vertical-align:text-bottom\" class=\"lmb-icon lmb-long-arrow-left\"></i> ";
}
?>
</TD></TR>
<TR><TD VALIGN="TOP">

<?php if(!$verkntabid){?>

<TABLE BORDER="0" cellspacing="0" cellpadding="0" STYLE="width:480px;">
<TR class="tabSubHeader"><TD VALIGN="TOP" COLSPAN="2" STYLE="height:20px;"><B><?=$lang[1824]?></B></TD></TR>
<?php
$sqlquery = "SELECT DISTINCT LMB_CONF_TABLES.TAB_ID,LMB_CONF_TABLES.TAB_GROUP,LMB_CONF_TABLES.TABELLE,LMB_CONF_TABLES.BESCHREIBUNG,LMB_CONF_GROUPS.NAME,LMB_CONF_GROUPS.ID FROM LMB_CONF_TABLES,LMB_CONF_GROUPS WHERE LMB_CONF_TABLES.TAB_GROUP = LMB_CONF_GROUPS.ID ORDER BY LMB_CONF_GROUPS.ID";
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

<TABLE BORDER="0" cellspacing="0" cellpadding="0" STYLE="width:480px;">
<TR class="tabSubHeader">
<TD></TD>
<TD NOWRAP ALIGN="CENTER" VALIGN="TOP" title="<?=$lang[2954]?>" style="cursor:help">&nbsp;<?=$lang[1825]?>&nbsp;</TD>
<TD NOWRAP ALIGN="CENTER" VALIGN="TOP" title="<?=$lang[2808]?>" style="cursor:help">&nbsp;<?=$lang[1826]?>&nbsp;</TD>
<TD NOWRAP ALIGN="CENTER" VALIGN="TOP" title="<?=$lang[2806]?>" style="cursor:help" colspan="2">&nbsp;<?=$lang[2089]?>&nbsp;</TD>
<TD NOWRAP ALIGN="CENTER" VALIGN="TOP" title="<?=$lang[2807]?>" style="cursor:help" colspan="2">&nbsp;<?=$lang[1846]?>&nbsp;</TD>
</TR>
<?php



# --- ungültige Feldtypen ---
$wrong_fields = array(25,13,23);

// adding fields fom relation table
if($vernparamsid) {
    $qu = "OR TAB_ID = $vernparamsid";
}

$sqlquery =  "SELECT SPELLING,DATA_TYPE,FIELD_TYPE,FIELD_ID,ARTLEISTE,VERKNTABID,VERKNTABLETYPE,FIELD_NAME FROM LMB_CONF_FIELDS WHERE (TAB_ID = $verkntabid $qu) AND FIELD_TYPE < 100 ORDER BY TAB_ID,SORT";
$rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
if(!$rs) {$commit = 1;}

while(lmbdb_fetch_row($rs)) {
    $recrelation = null;
	#if(lmbdb_result($rs, "VERKNTABLETYPE") == 2){$bzm++;continue;} !!!!!!!!!!!!!!! rückwertige Verknüpfung - mögliche ENDLOSSCHLEIFE
    if(lmbdb_result($rs, "FIELD_TYPE") == 11 AND $verkntree){continue;}
    $field_id = lmbdb_result($rs, "FIELD_ID");
    if(lmbdb_result($rs, "FIELD_TYPE") == 11 OR lmbdb_result($rs, "VERKNTABLETYPE") == 2){$recrelation = 'font-style:italic;text-decoration:underline';}
    
	if(!in_array(lmbdb_result($rs, "DATA_TYPE"),$wrong_fields)){
	
		$sqlquery1 = "SELECT ID,MD5TAB FROM LMB_CONF_FIELDS WHERE VERKNTABID = $tabid AND TAB_ID = $verkntabid AND FIELD_ID = ".$field_id;
		$rs1 = lmbdb_exec($db,$sqlquery1) or errorhandle(lmbdb_errormsg($db),$sqlquery1,$action,__FILE__,__LINE__);
		if(lmbdb_result($rs1, "MD5TAB")){
			$onchange = "OnChange=\"create_view();document.form1.set_verknfieldid.value=1;\"";
			$style= "style=\"color:red;\"";
		}elseif($verkntabletype == 2){
			$onchange = "OnChange=\"drop_view();document.form1.set_verknfieldid.value=1;\"";
			$style = "";
		}else{
			$onchange = "OnChange=\"document.form1.set_verknfieldid.value=1;\"";
			$style = "";
		}
		
		if($veknfieldid == $field_id){$checked = "CHECKED";}else{$checked = "";}
		if($verknsearchid){if(in_array($field_id,$verknsearchid)){$schecked = "CHECKED";}else{$schecked = "";}}else{$schecked = "";}
		if($verknfindid){if(in_array($field_id,$verknfindid)){$eschecked = "CHECKED";}else{$eschecked = "";}}else{$eschecked = "";}
		
		if($verknfindid){
		    $sortkeyf = array_search($field_id,$verknfindid);
		    if($sortkeyf !== false){
		        $sortkeyf++;$fchecked = "CHECKED";
		    }else{$fchecked = "";}
		}else{$fchecked = "";}
		
		if($verknviewid){
		    $sortkey = array_search($field_id,$verknviewid);
		    if($sortkey !== false){
		        $sortkey++;$gchecked = "CHECKED";
		    }else{$gchecked = "";}
		}else{$gchecked = "";}

		echo "<TR>";
		echo "<TD NOWRAP TITLE=\"".lmbdb_result($rs, "FIELD_NAME")."\" style=\"$recrelation\">";
		echo $lang[lmbdb_result($rs, "SPELLING")];
        if (lmbdb_result($rs, 'FIELD_TYPE') == 11 AND lmbdb_result($rs, 'VERKNTABLETYPE') != 2 AND $gfield[$verkntabid]['verkntabid'][$field_id] == $tabid) {
            echo '<i class="lmb-icon lmb-long-arrow-left"></i>';
        }
		echo "</TD>";

		echo "<TD align=\"center\">";
		if(lmbdb_result($rs, "VERKNTABLETYPE") != 2 AND $field_id < 1000){echo "<INPUT TYPE=\"RADIO\" $vradio NAME=\"new_verknfieldid\" VALUE=\"".$field_id."\" STYLE=\"border:none;background-color:transparent;\" $checked $onchange>";}
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


<tr><td><i><?=$lang[2595]?></i></td><td></td><td></td><td align="center"><input type="text" name="findidCut" style="width:30px" value="<?=$findidcut?>"></td><td></td><td align="center"><input type="text" name="viewidCut" style="width:30px" value="<?=$viewidcut?>"></td></tr>

</TABLE>
<?php }?>

</TD></TR>

<?php if($verkntabletype == 1) {?>

<TR><TD><HR></TD></TR>
<TR><TD>
<table width="100%"><tr><td width="50%" valign="top">
<?=$lang[941]?><BR>
<INPUT TYPE="CHECKBOX" STYLE="border:none;background-color:transparent;" NAME="new_refint" <?php if($refint){echo "CHECKED";}?>>&nbsp;
<SELECT NAME="new_refint_rule">
<OPTION VALUE="1" <?php if($refint_rule == "DELETE RESTRICT"){echo "SELECTED";}?>>DELETE RESTRICT
</SELECT>
<BR>
<?php if($refint_rule){echo "( $f1 | $f2 )";}?>
</td><td valign="top">
<?=$lang[2809]?><br>
<?php
if($set_vparams AND $fieldid AND $tabid AND $veknfieldid AND !$recursrelation){
	$verknparams = createRelationParams($new_vparams,$verknparams,$md5tab,$groupid,$fieldspelling,$fieldid,$tabid);
}
?>
<INPUT TYPE="CHECKBOX" STYLE="border:none;background-color:transparent;" NAME="new_vparams" <?php if($verknparams){echo "CHECKED";}?> onchange="document.form1.set_vparams.value=1">&nbsp;<?php if($verknparams){echo "(ID: $verknparams)";}?><br>
<span style="color:green;font-style:italic"><?= $GLOBALS['message'] ?></span>

</td></tr></table>

</TD></TR>
<?php }?>


<TR><TD>


<?php



if(($verkntabletype == 1 OR $verkntabletype == 3) AND $tree = recrelationtree()){
	
	echo "<hr>";
	echo $lang[2855];
	
	foreach($tree as $tkey => $path){
		$tree_identifier = md5(implode(",",$path));
		$CHECKED = '';
		if($verkntree == $tree_identifier){$CHECKED = 'CHECKED';}
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




#echo "<pre>";
#print_r($tree[0]);

?>




</TD></TR>
<TR><TD><hr></TD></TR>


<TR><TD COLSPAN="3" align="center"><INPUT TYPE="SUBMIT" VALUE="<?=$lang[33]?>" NAME="vknsave"></TD></TR>
<TR><TD>&nbsp;</TD></TR>
<TR><TD COLSPAN="5"><?=$message1?></TD></TR>
</TABLE>
</FORM>





<?php if($fieldid AND $tabid AND $verkntabid){?>

<FORM ACTION="main_admin.php" METHOD="post" NAME="form2">
<input type="hidden" name="action" value="setup_verkn_editor">
<input type="hidden" name="fieldid" value="<?=$fieldid;?>">
<input type="hidden" name="tabid" value="<?=$tabid?>">
<input type="hidden" name="category" value="create">

<TABLE ID="tab2" STYLE="display:none;width:480px;" BORDER="0" CELLPADDING="0" CELLSPACING="0" WIDTH="100%">
<TR><TD COLSPAN="3"><?=$message2?></TD></TR>
<TR><TD><B><?=$tabname?></B><BR><BR><SELECT NAME="v_field1" SIZE="20"><OPTION VALUE="ID" SELECTED>ID
<?php
$sqlquery =  "SELECT DISTINCT FIELD_NAME FROM LMB_CONF_FIELDS WHERE TAB_ID = $tabid AND FIELD_TYPE < 100 AND FIELD_NAME != 'ID'";
$rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
if(!$rs) {$commit = 1;}
while(lmbdb_fetch_row($rs)) {
	echo "<OPTION VALUE=\"".lmbdb_result($rs, "FIELD_NAME")."\">".lmbdb_result($rs, "FIELD_NAME");
}
?>
</SELECT></TD><TD>&nbsp;</TD><TD><B><?=$verkntabname?></B><BR><BR><SELECT NAME="v_field2" SIZE="20"><OPTION VALUE="ID" SELECTED>ID
<?php
$sqlquery =  "SELECT DISTINCT FIELD_NAME FROM LMB_CONF_FIELDS WHERE TAB_ID = $verkntabid AND FIELD_TYPE < 100 AND FIELD_NAME != 'ID'";
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







<?php if($fieldid AND $tabid AND $verkntabid){?>

<FORM ACTION="main_admin.php" METHOD="post" NAME="form3">
<input type="hidden" name="action" value="setup_verkn_editor">
<input type="hidden" name="tabid" value="<?=$tabid?>">
<input type="hidden" name="fieldid" value="<?=$fieldid;?>">
<input type="hidden" name="relation_extension" value="1">
<input type="hidden" name="category" value="relation">

<TABLE ID="tab3" STYLE="display:none;width:480px;" BORDER="0" CELLPADDING="0" CELLSPACING="0" WIDTH="100%"><TR><TD align="center">
<TABLE BORDER="0" CELLPADDING="1" CELLSPACING="2" WIDTH="80%">
    <TR BGCOLOR="<?=$farbschema['WEB3']?>"><TD style="cursor:pointer" OnClick="document.getElementById('relation_verknview').style.display = '';"><i class="lmb-icon lmb-caret-square-down-alt"></i></TD><TD><b>&nbsp;<?=$lang[2377]?></b></TD></TR>
</TABLE>
<TABLE BORDER="0" CELLPADDING="1" CELLSPACING="2" WIDTH="80%" ID="relation_verknview" style="display:none">
<?php
rec_verknpf_tabs($tabid,$verkntab);
?>
<TR><TD colspan="3"><TEXTAREA style="width:100%;height:50px;background-color:#EEEEEE" ID="relation_preview" readonly OnDblClick="document.getElementById('relation_value').value=this.value"></TEXTAREA><hr></TD></TR>

</TABLE>


<TEXTAREA style="width:80%;height:200px;" ID="relation_value" NAME="relation_value"><?=htmlentities($relext,ENT_QUOTES,$umgvar["charset"]);?></TEXTAREA>

<div style="width:80%;background-color:#FFFFFF;text-align:left">
<b>examples:</b><br>
    $where[] = "VERK_52131C929EFDD.ID = ".$verkn["id"]." AND ....<br>
	$from[] = "VERK_52131C929EFDD";<br>
	$select[] = 'CONTACTS.NAME, CONTACTS.GIVENNAME';<br>
	$order[] = 'CONTACTS.NAME ASC, CONTACTS.GIVENNAME ASC';<br>
	$join[] = '';<br>
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


</TD></TR></TABLE>
<?php }?>

</TD></TR></TABLE></DIV>



<script language="JavaScript">
LIM_setDefault();
</SCRIPT>


<?php
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
?>

