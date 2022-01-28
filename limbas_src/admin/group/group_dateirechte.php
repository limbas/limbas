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
 * ID: 213
 */
?>
<SCRIPT LANGUAGE="JavaScript">

var dspl = new Array();
<?php
$dspl_ = array();
if($dspl){
$dspl_ = explode(";",$dspl);
foreach ($dspl_ as $key => $value) {
	if($value){
		echo "dspl[$value] = $value;\n";
	}
}}
?>

function f_3(PARAMETER) {
	document.form1.action.value = PARAMETER;
	document.form1.submit();
}

img3=new Image();img3.src="pic/outliner/plusonly.gif";
img4=new Image();img4.src="pic/outliner/minusonly.gif";

function popup(ID,LEVEL,FORCE){
	var cli;
	if(browser_ns5){cli = ".nextSibling";}else{cli = "";}
	eval("var nested = document.getElementById('f_"+LEVEL+"_"+ID+"').nextSibling"+cli);
	var picname = "i" + ID;
	if((document.images[picname].src == img4.src || FORCE == 1) && FORCE != 2) {
		document.images[picname].src = img3.src;
		nested.style.display="none";
		dspl[ID] = ID;
	}else{
		document.images[picname].src = img4.src;
		nested.style.display='';
		dspl[ID] = 0;
	}
	document.form1.dspl.value = dspl.join(";");
}

function send_rule(id,typ,val){
	document.form1.fileid.value = id;
	document.form1.val.value = val;
	document.form1.typ.value = typ;
	document.form1.submit();
}

function uncheckChildFiles(id,typ){
	var child_elements = document.getElementsByName("hh"+typ+id);
	for(var e=0;e<child_elements.length;e++){
		var elid = child_elements[e].id.substr(2,100);
		document.getElementById("f"+typ+elid).checked = 0;
		uncheckChildFiles(elid,typ);
	}
}

function checkChildFiles(id,typ){
	var child_elements = document.getElementsByName("hh"+typ+id);
	for(var e=0;e<child_elements.length;e++){
		var elid = child_elements[e].id.substr(2,100);
		document.getElementById("f"+typ+elid).checked = 1;
		checkChildFiles(elid,typ);
	}
}

function checkParentFiles(id,level,typ,status){
	parent_el = "f"+typ+level;
	parent_hide = "h"+typ+level;
	if(document.getElementById(parent_el)){
		var parent_level = document.getElementById(parent_hide).value;
		document.getElementById(parent_el).checked = 1;
		checkParentFiles(0,parent_level,typ,1);
	}
}

function checkFiles(id,level,typ,status){
	if(status){
		if(typ == "v"){checkParentFiles(id,level,typ,status);}
		if(document.getElementById("inclsub").checked){
			checkChildFiles(id,typ);
		}
	}else{
		if(typ == "v"){
			uncheckChildFiles(id,typ);
		}else{
			if(document.getElementById("inclsub").checked){
				uncheckChildFiles(id,typ);
			}
		}
	}
}

function opcl_all(typ){

	document.getElementById("opclall1").style.border = 'none';
	document.getElementById("opclall2").style.border = 'none';
	document.getElementById("opclall3").style.border = 'none';
	document.getElementById("opclall"+typ).style.border = '1px solid red';
	

	var child_elements = document.getElementsByTagName("DIV");
	for(var e=0;e<child_elements.length;e++){
		if(child_elements[e].id.substr(0,2) == "f_"){
			var str = child_elements[e].id;
			var idar = str.split("_");
			var picname = "i" + idar[2];

			if(document.images[picname]) {
				if(typ == 3){
					var view = document.getElementById("fv"+idar[2]);
					if(view.checked){
						popup(idar[2],idar[1],2);
					}else{
						popup(idar[2],idar[1],1);
					}
				} else{
					popup(idar[2],idar[1],typ);
				}
			}
		}
	}
}


// Ajax Gruppenrechte
function limbasShowGroups(el,level){
	limbasDivShow(el,"","ShowGroupsInfo");
	url = "main_dyns_admin.php";
	actid = "fileGroupRules&level=" + level;
	ajaxGet(null,url,actid,null,"limbasShowGroupsPost");
}

function limbasShowGroupsPost(result){
	document.getElementById("ShowGroupsInfo").innerHTML = result;
	if(!result){
		document.getElementById("ShowGroupsInfo").style.visibility = 'hidden';
	}
	
}

</SCRIPT>

<STYLE>
    .lmbRightsCheckboxWrapper {
        width: 25px;
        text-align: center;
    }
</STYLE>



<div id="lmbAjaxContainer" class="ajax_container" style="position:absolute;visibility:hidden;z-index:999" OnClick="activ_menu=1;"></div>
<div style="position:absolute;left:-10px;z-index:9;padding:3px;" id="ShowGroupsInfo"></div>


<FORM ACTION="main_admin.php" METHOD="post" name="form1">
<input type="hidden" name="action" VALUE="setup_group_dateirechte">
<input type="hidden" name="ID" VALUE="<?=$ID?>">
<input type="hidden" name="fileid">
<input type="hidden" name="val">
<input type="hidden" name="typ">
<input type="hidden" name="dspl" VALUE="<?=$dspl?>">

<div>


<TABLE class="tabpool" BORDER="0" cellspacing="0" cellpadding="0"><TR><TD valign="top" height="100%">

<TABLE BORDER="0" cellspacing="0" cellpadding="0" height="100%" style="border-collapse:collapse;position:sticky;top:20px;">
<?php if($LINK[135]){?><TR class="tabHpoolItemTR"><TD class="tabHpoolItemInactive" OnClick="<?=$LINK["link_url"][135]?>" TITLE="<?=$lang[$LINK["desc"][135]]?>"><?= "<i style=\"float:left;\" class=\"lmb-icon ".$LINK["icon_url"][135]."\"></i>&nbsp;".$lang[$LINK["name"][135]] ?></TD></TR><?php }?>
<?php if($LINK[76]){?><TR class="tabHpoolItemTR"><TD class="tabHpoolItemInactive" OnClick="<?=$LINK["link_url"][76]?>" TITLE="<?=$lang[$LINK["desc"][76]]?>"><?= "<i style=\"float:left;\" class=\"lmb-icon ".$LINK["icon_url"][76]."\"></i>&nbsp;".$lang[$LINK["name"][76]] ?></TD></TR><?php }?>
<?php if($LINK[100]){?><TR class="tabHpoolItemTR"><TD class="tabHpoolItemInactive" OnClick="<?=$LINK["link_url"][100]?>" TITLE="<?=$lang[$LINK["desc"][100]]?>"><?= "<i style=\"float:left;\" class=\"lmb-icon ".$LINK["icon_url"][100]."\"></i>&nbsp;".$lang[$LINK["name"][100]] ?></TD></TR><?php }?>
<?php if($LINK[192]){?><TR class="tabHpoolItemTR"><TD class="tabHpoolItemActive" OnClick="<?=$LINK["link_url"][192]?>" TITLE="<?=$lang[$LINK["desc"][192]]?>"><?= "<i style=\"float:left;\" class=\"lmb-icon ".$LINK["icon_url"][192]."\"></i>&nbsp;".$lang[$LINK["name"][192]] ?></TD></TR><?php }?>
<?php if($LINK[260]){?><TR class="tabHpoolItemTR"><TD class="tabHpoolItemInactive" OnClick="<?=$LINK["link_url"][260]?>" TITLE="<?=$lang[$LINK["desc"][260]]?>"><?= "<i style=\"float:left;\" class=\"lmb-icon ".$LINK["icon_url"][260]."\"></i>&nbsp;".$lang[$LINK["name"][260]] ?></TD></TR><?php }?>
<?php if($LINK[291]){?><TR class="tabHpoolItemTR"><TD class="tabHpoolItemInactive" OnClick="<?=$LINK["link_url"][291]?>" TITLE="<?=$lang[$LINK["desc"][291]]?>"><?= "<i style=\"float:left;\" class=\"lmb-icon ".$LINK["icon_url"][291]."\"></i>&nbsp;".$lang[$LINK["name"][291]] ?></TD></TR><?php }?>
<?php if($LINK[292]){?><TR class="tabHpoolItemTR"><TD class="tabHpoolItemInactive" OnClick="<?=$LINK["link_url"][292]?>" TITLE="<?=$lang[$LINK["desc"][292]]?>"><?= "<i style=\"float:left;\" class=\"lmb-icon ".$LINK["icon_url"][292]."\"></i>&nbsp;".$lang[$LINK["name"][292]] ?></TD></TR><?php }?>
<?php if($LINK[290]){?><TR class="tabHpoolItemTR"><TD class="tabHpoolItemInactive" OnClick="<?=$LINK["link_url"][290]?>" TITLE="<?=$lang[$LINK["desc"][290]]?>"><?= "<i style=\"float:left;\" class=\"lmb-icon ".$LINK["icon_url"][290]."\"></i>&nbsp;".$lang[$LINK["name"][290]] ?></TD></TR><?php }?>
<?php if($LINK[293]){?><TR class="tabHpoolItemTR"><TD class="tabHpoolItemInactive" OnClick="<?=$LINK["link_url"][293]?>" TITLE="<?=$lang[$LINK["desc"][293]]?>"><?= "<i style=\"float:left;\" class=\"lmb-icon ".$LINK["icon_url"][293]."\"></i>&nbsp;".$lang[$LINK["name"][293]] ?></TD></TR><?php }?>
<TR><TD class="tabHpoolItemSpaceGtab"><div style="height:100%">&nbsp;</div></TD></TR>

</TABLE>

</TD><TD width="500px" class="tabHpoolfringe" style="border-left:none">

<TABLE ID="tab1" width="100%" cellspacing="2" cellpadding="1" class="tabBody">
    <TR class="tabHeader"><TD class="tabHeaderItem" colspan="23"><i class="lmb-icon lmb-group"></i>&nbsp;<?=$groupdat["name"][$ID]?></TD></TR>
<TR><TD colspan="23"><HR></TD></TR>

<TR><TD style="width: 100%">
<?php
function files1($LEVEL,$sub_view,$sub_add,$sub_addf,$sub_edit,$sub_del){
	global $file_struct;
	global $ffilter;
	global $filerules;
	global $farbschema;
	global $lang;
	global $dspl_;
	
	$sublevel = $LEVEL + 1;
	$pis = "minusonly.gif";
	
        
	if($LEVEL){
		if(in_array($LEVEL,$dspl_)){$vis = "style=\"display:none\"";}else{$vis = "";}
		echo "<div id=\"foldinglist\" $vis>\n";
		echo "<TABLE CELLPADDING=\"0\" CELLSPACING=\"0\" BORDER=\"0\" WIDTH=\"100%\" STYLE=\"border-collapse:collapse;\"><TR><TD WIDTH=\"10\">&nbsp;</TD><TD>\n";
	} else{
		if(!$bzm){echo "<TABLE CELLPADDING=\"0\" CELLSPACING=\"0\" BORDER=\"0\" WIDTH=\"100%\"><TR>
		
		<TD align=\"left\" nowrap style=\"cursor:pointer;width:20px;\" TITLE=\"".$lang[2288]."\"><i class=\"lmb-icon lmb-expand-all\" OnClick=\"opcl_all(2);\" STYLE=\"border:1px solid red;padding:3px 5px;\" ID=\"opclall2\"></i></TD>
		<TD align=\"left\" nowrap style=\"cursor:pointer;width:20px;\" TITLE=\"".$lang[2289]."\"><i class=\"lmb-icon lmb-collapse-all\" OnClick=\"opcl_all(1);\" STYLE=\";padding:3px 5px;\" ID=\"opclall1\"></i></TD>
		<TD align=\"left\" nowrap style=\"cursor:pointer;width:20px;\" TITLE=\"".$lang[2290]."\"><i class=\"lmb-icon lmb-expand-some\" OnClick=\"opcl_all(3);\" STYLE=\"padding:3px 5px;\" ID=\"opclall3\"></i></TD>
		<TD >&nbsp;</TD>

                <TD style=\"width:100px\" align=\"center\">$lang[2081]<INPUT TYPE=\"CHECKBOX\" ID=\"inclsub\" CLASS=\"checkb\"></TD>
                <TD class=\"lmbRightsCheckboxWrapper\" align=\"center\"><i class=\"lmb-icon lmb-eye\" BORDER=\"0\" TITLE=\"$lang[2295]\"></i></TD>
                <TD class=\"lmbRightsCheckboxWrapper\" align=\"center\"><i class=\"lmb-icon lmb-create-file\" BORDER=\"0\" TITLE=\"$lang[2296]\"></i></TD>
                <TD class=\"lmbRightsCheckboxWrapper\" align=\"center\"><i class=\"lmb-icon lmb-create-folder\" BORDER=\"0\" TITLE=\"$lang[2297]\"></i></TD>
                <TD class=\"lmbRightsCheckboxWrapper\" align=\"center\"><i class=\"lmb-icon lmb-pencil\" BORDER=\"0\" TITLE=\"$lang[2299]\"></i></TD>
                <TD class=\"lmbRightsCheckboxWrapper\" align=\"center\"><i class=\"lmb-icon lmb-trash\" BORDER=\"0\" TITLE=\"$lang[2298]\"></i></TD>
                <TD class=\"lmbRightsCheckboxWrapper\" align=\"center\"><i class=\"lmb-icon lmb-lock-file\" BORDER=\"0\" TITLE=\"$lang[2300]\"></i></TD>
                <TD class=\"lmbRightsCheckboxWrapper\" align=\"center\">&nbsp;</TD></TR>
                <TR><TD style=\"width:25px\" COLSPAN=\"12\"><HR></TD></TR>
		</TABLE>";}
	}
        
	foreach ($file_struct["id"] as $bzm => $value) {
		if($file_struct["level"][$bzm] == $LEVEL){
			if(in_array($file_struct["id"][$bzm],$file_struct["level"])){
				if(in_array($file_struct["id"][$bzm],$dspl_)){$pis = "plusonly.gif";}else{$pis = "minusonly.gif";}
				$next = 1;
				$pic = "<IMG SRC=\"pic/outliner/$pis\" NAME=\"i".$file_struct["id"][$bzm]."\" OnClick=\"popup('".$file_struct["id"][$bzm]."','$LEVEL',0)\" STYLE=\"cursor:hand\">";
			}else{
				$next = 0;
				$pic = "<IMG SRC=\"pic/outliner/blank.gif\">";
			}
			
			echo "<div ID=\"f_".$LEVEL."_".$file_struct["id"][$bzm]."\"><TABLE CELLPADDING=\"0\" CELLSPACING=\"0\" BORDER=\"0\" WIDTH=\"100%\">";
			echo "<TR title=\"".htmlentities($file_struct["name"][$bzm],ENT_QUOTES,$GLOBALS["umgvar"]["charset"])."\"><TD WIDTH=\"20\" nowrap>$pic</TD><TD nowrap WIDTH=\"20\"><i class=\"lmb-icon lmb-folder-closed\" ID=\"p".$file_struct["id"][$bzm]."\" NAME=\"p".$file_struct["id"][$bzm]."\" STYLE=\"cursor:hand\"></i></TD><TD nowrap>&nbsp;".$file_struct['name'][$bzm]."</TD>";
			echo "<TD ALIGN=\"RIGHT\"><TABLE CELLPADDING=\"0\" CELLSPACING=\"0\" BORDER=\"0\"><TR>";
			
                        # --- view ---
                        echo "<TD class=\"lmbRightsCheckboxWrapper\"><INPUT ID=\"fv".$file_struct["id"][$bzm]."\" TITLE=\"$lang[2295]\" NAME=\"frule[v][".$file_struct["id"][$bzm]."]\" TYPE=\"CHECKBOX\" CLASS=\"checkb\" OnClick=\"checkFiles('".$file_struct["id"][$bzm]."','".$file_struct["level"][$bzm]."','v',this.checked)\"";
                        if($filerules[$file_struct["id"][$bzm]]["view"]){echo " CHECKED";}
                        echo "><INPUT TYPE=\"hidden\" ID=\"hv".$file_struct["id"][$bzm]."\" NAME=\"hhv".$file_struct["level"][$bzm]."\" VALUE=\"".$file_struct["level"][$bzm]."\"></TD>";

                        # --- add ---
                        echo "<TD class=\"lmbRightsCheckboxWrapper\"><INPUT ID=\"fa".$file_struct["id"][$bzm]."\" TITLE=\"$lang[2296]\" NAME=\"frule[a][".$file_struct["id"][$bzm]."]\" TYPE=\"CHECKBOX\" CLASS=\"checkb\" OnClick=\"checkFiles('".$file_struct["id"][$bzm]."','".$file_struct["level"][$bzm]."','a',this.checked)\"";
                        if($filerules[$file_struct["id"][$bzm]]["add"]){echo " CHECKED";}
                        echo "><INPUT TYPE=\"hidden\" ID=\"ha".$file_struct["id"][$bzm]."\" NAME=\"hha".$file_struct["level"][$bzm]."\" VALUE=\"".$file_struct["level"][$bzm]."\"></TD>";

                        # --- addf ---
                        echo "<TD class=\"lmbRightsCheckboxWrapper\"><INPUT ID=\"fc".$file_struct["id"][$bzm]."\" TITLE=\"$lang[2297]\" NAME=\"frule[c][".$file_struct["id"][$bzm]."]\" TYPE=\"CHECKBOX\" CLASS=\"checkb\" OnClick=\"checkFiles('".$file_struct["id"][$bzm]."','".$file_struct["level"][$bzm]."','c',this.checked)\"";
                        if($filerules[$file_struct["id"][$bzm]]["addf"]){echo " CHECKED";}
                        echo "><INPUT TYPE=\"hidden\" ID=\"hc".$file_struct["id"][$bzm]."\" NAME=\"hhc".$file_struct["level"][$bzm]."\" VALUE=\"".$file_struct["level"][$bzm]."\"></TD>";

                        # --- edit ---
                        echo "<TD class=\"lmbRightsCheckboxWrapper\"><INPUT ID=\"fe".$file_struct["id"][$bzm]."\" TITLE=\"$lang[2299]\" NAME=\"frule[e][".$file_struct["id"][$bzm]."]\" TYPE=\"CHECKBOX\" CLASS=\"checkb\" OnClick=\"checkFiles('".$file_struct["id"][$bzm]."','".$file_struct["level"][$bzm]."','e',this.checked)\"";
                        if($filerules[$file_struct["id"][$bzm]]["edit"]){echo " CHECKED";}
                        echo "><INPUT TYPE=\"hidden\" ID=\"he".$file_struct["id"][$bzm]."\" NAME=\"hhe".$file_struct["level"][$bzm]."\" VALUE=\"".$file_struct["level"][$bzm]."\"></TD>";

                        # --- del ---
                        echo "<TD class=\"lmbRightsCheckboxWrapper\"><INPUT ID=\"fd".$file_struct["id"][$bzm]."\" TITLE=\"$lang[2298]\" NAME=\"frule[d][".$file_struct["id"][$bzm]."]\" TYPE=\"CHECKBOX\" CLASS=\"checkb\" OnClick=\"checkFiles('".$file_struct["id"][$bzm]."','".$file_struct["level"][$bzm]."','d',this.checked)\"";
                        if($filerules[$file_struct["id"][$bzm]]["del"]){echo " CHECKED";}
                        echo "><INPUT TYPE=\"hidden\" ID=\"hd".$file_struct["id"][$bzm]."\" NAME=\"hhd".$file_struct["level"][$bzm]."\" VALUE=\"".$file_struct["level"][$bzm]."\"></TD>";

                        # --- lock ---
                        echo "<TD class=\"lmbRightsCheckboxWrapper\"><INPUT ID=\"fl".$file_struct["id"][$bzm]."\" TITLE=\"$lang[2300]\" NAME=\"frule[l][".$file_struct["id"][$bzm]."]\" TYPE=\"CHECKBOX\" CLASS=\"checkb\" OnClick=\"checkFiles('".$file_struct["id"][$bzm]."','".$file_struct["level"][$bzm]."','l',this.checked)\"";
                        if($filerules[$file_struct["id"][$bzm]]["lock"]){echo " CHECKED";}
                        echo "><INPUT TYPE=\"hidden\" ID=\"hl".$file_struct["id"][$bzm]."\" NAME=\"hhl".$file_struct["level"][$bzm]."\" VALUE=\"".$file_struct["level"][$bzm]."\"></TD>";

                        # --- lock ---
                        echo "<TD class=\"lmbRightsCheckboxWrapper\"><i class=\"lmb-icon lmb-info-circle\" style=\"cursor:pointer\" OnClick=\"limbasShowGroups(this,'$value')\" title=\"$lang[2301]\"></i></TD>";

			
			echo "</TR></TABLE></TD>";
			echo "</TR></TABLE></div>\n";			
			if($next){
				files1($file_struct["id"][$bzm],$sub_view,$sub_add,$sub_addf,$sub_edit,$sub_del);
			}else{
				echo "<div id=\"foldinglist\" style=\"display:none\"></div>\n";
			}
		}
	}
        
	if($LEVEL){
		echo "</TD></TR></TABLE>\n";
		echo "</div>\n";
	}
}
files1(0,0,0,0,0,0);
?>

</TD><TD width="30%"></TD></TR>

<?php
if($session["user_id"] != 1 AND $session["group_id"] == $ID){
lmb_alert("no permission to change own group!");
}else{
?>
<TR><TD COLSPAN="10" ALIGN="CENTER"><HR></TD></TR>
<TR><TD colspan="3"><input type="submit" value="<?=$lang[33]?>" name="update_rules">&nbsp;&nbsp;&nbsp;&nbsp;<?=$lang[2107]?>:<input type="checkbox" name="change_subgroup" CLASS="checkb" <?php if($change_subgroup){echo "CHECKED";}?>></TD></TR>
<?php }?>
<TR><TD colspan="3" class="tabFooter">&nbsp;</TD></TR>
</TABLE>

</TD></TR></TABLE>
</DIV>
</FORM>
