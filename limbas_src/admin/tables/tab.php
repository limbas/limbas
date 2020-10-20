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
 * ID: 140
 */

/*----------------- Farbe DIV -------------------*/?>
<DIV ID="menu_color" class="lmbContextMenu" style="position:absolute;visibility:hidden;z-index:10002;">
<FORM NAME="fcolor_form"><TABLE BORDER="0" cellspacing="0" cellpadding="0">
<TR><TD><?php pop_top('menu_color');?></TD></TR>
<TR><TD>
<?php pop_color(null, null, 'menu_color');?>
</TD></TR>
<TR><TD><?php pop_bottom();?></TD></TR>
</TABLE></FORM></DIV>

<DIV ID="menu_indicator" class="lmbContextMenu" style="position:absolute;visibility:hidden;z-index:10001;" onclick="activ_menu=1">
<?php pop_left();?>
<TEXTAREA ID="indicator_value" OnChange="document.form2.tabid.value=currenttabid;document.form2.indicator.value=' '+this.value;document.form2.submit();" STYLE="width:150px;height:100px;background-color:<?= $farbschema['WEB8'] ?>;"></TEXTAREA>
<?php pop_right();?>
<?php pop_bottom();?>
</DIV>




<Script language="JavaScript">

var currenttabid = null;

function add_tab(){
	document.form2.add.value=1;
	document.form2.submit();
}


function set_color(color){
	if(!color){color = "transparent";}
	document.form2.markcolor.value = color;
	document.form2.submit();
}

// --- Plaziere DIV-Element auf Cursorposition -----------------------------------
function setxypos(evt,el) {
        if(browser_ns5){
                document.getElementById(el).style.left=evt.pageX;
                document.getElementById(el).style.top=evt.pageY;
        }else{
                document.getElementById(el).style.left=window.event.clientX + document.body.scrollLeft - 80;
                document.getElementById(el).style.top=window.event.clientY + document.body.scrollTop;
        }
}

// --- Farbmenüsteuerung -----------------------------------
function div4(el, evt,tab) {
	document.form2.tabid.value=tab;
	setxypos(evt,'menu_color');
        limbasDivShow(el,null,'menu_color');
	//document.getElementById("menu_color").style.visibility='visible';
}

function group_delete(ID){
	var del = confirm('<?=$lang[2286]?>');
	if(del){
		document.location.href="main_admin.php?action=setup_tab&group_del="+ID;
	}
}

function tab_delete(group_bzm,tab_group,bzm,gtable,tabid,drop_physical){
	ph = '';
	if(drop_physical){
		ph = '( <?=$lang[1727]?> ) ';
	}else{
		ph = '( <?=$lang[2811]?> ) ';
	}
	var del = confirm("<?=$lang[2287]?> "+ph+'\n### '+gtable+" ###");
	if(del){
		document.location.href="main_admin.php?action=setup_tab&group_bzm="+group_bzm+"&tab_group="+tab_group+"&bzm="+bzm+"&gtable="+gtable+"&tabid="+tabid+"&del=1&drop_physical="+drop_physical;
	}
}

function set_versioning(tab_id,tab_group,versioning){
	
	var ver = confirm('<?=$lang[2760]?>');
	if(ver){
		document.form2.tabid.value=tab_id;
		document.form2.tab_group.value=tab_group;
		document.form2.versioning.value=versioning;
		document.form2.submit();
	}
}

// --- indicator -----------------------------------
function show_indicator(el,gtabid) {
	limbasDivShow(el,null,'menu_indicator');
	el = "indicator_rule_"+gtabid;
	currenttabid = gtabid;
	document.getElementById("indicator_value").value = document.form2.elements[el].value;
}


var noclick = 0;
function tab_userrule(el,tabid,tab_group){
	if(noclick){noclick = 0;return;}
	var rule = confirm('<?=$lang[2340]?>');
	if(rule){
		document.form2.tabid.value=tabid;
		document.form2.tab_group.value=tab_group;
		if(el.checked){document.form2.userrules.value=1;}else{document.form2.userrules.value=2;};
		document.form2.submit();
	}else{
		noclick = 1;
		el.click();
		noclick = 0;
	}
}


var activ_menu = null;
function divclose(){
	if(!activ_menu){
		hide_trigger();
		document.getElementById('menu_indicator').style.visibility = 'hidden';
        document.getElementById('menu_color').style.visibility = 'hidden';
		document.getElementById('lmbAjaxContainer').style.visibility = 'hidden';
	}
	activ_menu = 0;
}

function hide_trigger(){
	var ar = document.getElementsByTagName("span");
	for (var i = ar.length; i > 0;) {
		cc = ar[--i];
		if(cc.id.substring(0,11) == "tab_trigger"){
			cc.style.display='none';
		}
	}
}


function extendTabTyp(el){
	if(el.value==5){
		document.getElementsByName("viewtyp")[0].style.display = '';
		document.getElementsByName("verkn")[0].style.display = 'none';
	}else{
		document.getElementsByName("viewtyp")[0].style.display = 'none';
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
	
	if(!el){el = document.getElementById('menu'+elid);}
	
	limbasSetLayoutClassTabs(el,'tabpoolItemInactive','tabpoolItemActive');
	if(document.getElementById("tab"+elid)){
		document.getElementById("tab"+elid).style.display = '';
	}
}


// Ajax edit table
function ajaxEditTable(el,gtabid,tabgroup,act){
	if(el){
		
		if(act == 'trigger'){
			document.form3.val.value=gtabid;
			var i = 0;
			$('#triggerpool input:checked').each(function() {
				var cel = $(this).attr('id').split('_');
				if(cel[0] = 'trigger'){
					$('#form3').append("<input type='hidden' name='tab_trigger_"+gtabid+"["+i+"]' value='"+cel[1]+"'>");
					i++;
				}
			});
		}else if($(el).attr('type') == 'checkbox'){
			if(el.checked){
				document.form3.val.value=1;
			}else{
				document.form3.val.value=2;
			}
		}else{
			document.form3.val.value=el.value+' ';
		}
	}
	ajaxGet(null,"main_dyns_admin.php","editTable&tabid="+gtabid+"&tabgroup="+tabgroup+"&act=" + act,null,"ajaxEditTablePost","form3");
}

function ajaxEditTablePost(result){
	element = document.getElementById("lmbAjaxContainer");
	element.style.visibility = '';
	element.innerHTML = result;
	limbasSetCenterPos(element);
	element.style.left = '180px';
	//hide_selects(1);
}
</Script>

<div id="lmbAjaxContainer" class="ajax_container" style="position:absolute;visibility:hidden;" OnClick="activ_menu=1;"></div>
<div class="lmbPositionContainerMain">



<?php
/* --- Gruppen-Liste --------------------------------------------- */
if(!$tab_group){?>
	<FORM ACTION="main_admin.php" METHOD="post" NAME="form1">
	<input type="hidden" name="action" value="setup_tab">
	<input type="hidden" name="group_change">
	<input type="hidden" name="subgroup_change">
	<input type="hidden" name="icongroup_change">
	<input type="hidden" name="tab_group" value="<?=$tab_group;?>">
	<TABLE BORDER="0" cellspacing="1" cellpadding="2" class="tabfringe">

	<TR class="tabHeader">

	<TD class="tabHeaderItem"><?=$lang[949]?>&nbsp;</TD>
    <TD class="tabHeaderItem"></TD>
	<TD class="tabHeaderItem"><?=$lang[952]?></TD>
    <TD class="tabHeaderItem"><?=$lang[160]?></TD>
	<TD class="tabHeaderItem"><?=$lang[924]?></TD>
	<TD class="tabHeaderItem"><?=$lang[923]?></TD>
	<TD class="tabHeaderItem"><?=$lang[897]?></TD>
	<TD class="tabHeaderItem">Icon</TD>
	<TD class="tabHeaderItem"><?=$lang[577]?></TD>
	</TR>
        <?php
        foreach($tabgroup_["id"] as $bzm => $value){
        	echo "<TR class=\"tabBody\">";
            echo "<TD class=\"vAlignMiddle txtAlignLeft\"><A HREF=\"main_admin.php?action=setup_tab&group_bzm=$bzm&tab_group=".$tabgroup_["id"][$bzm]."\">&nbsp;".$tabgroup_["id"][$bzm]."&nbsp;</A></TD>";
            echo "<TD class=\"vAlignMiddle txtAlignLeft\"><A HREF=\"main_admin.php?action=setup_tab&group_bzm=$bzm&tab_group=".$tabgroup_["id"][$bzm]."\"><i class=\"lmb-icon lmb-pencil\" BORDER=\"0\" style=\"cursor:pointer\"></i></A></TD>";
            echo "<TD class=\"vAlignMiddle txtAlignLeft\"><i class=\"lmb-icon lmb-long-arrow-up\" BORDER=\"0\" OnClick=\"document.location.href='main_admin.php?action=setup_tab&group_change=".$tabgroup_["id"][$bzm]."&sort_id=".$tabgroup_['id'][$bzm]."&gup=1'\"></i>&nbsp;<i class=\"lmb-icon lmb-long-arrow-down\" BORDER=\"0\" OnClick=\"document.location.href='main_admin.php?action=setup_tab&group_change=".$tabgroup_["id"][$bzm]."&sort_id=".$tabgroup_['id'][$bzm]."&gdown=1'\"></i></TD>";
            echo "<TD class=\"vAlignMiddle txtAlignLeft\">";
            if(!$tabgroup_["systemtab"][$bzm]){
                echo "<i class=\"lmb-icon lmb-trash\" BORDER=\"0\" style=\"cursor:pointer;\" OnClick=\"group_delete('".$tabgroup_["id"][$bzm]."')\"></i>";
            }
            echo "</TD>";
        	echo "<TD><INPUT TYPE=\"TEXT\" NAME=\"group_name_".$tabgroup_["id"][$bzm]."\" STYLE=\"width:130px;\" VALUE=\"".$tabgroup_["name"][$bzm]."\" OnChange=\"document.form1.group_change.value='".$tabgroup_["id"][$bzm]."';document.form1.submit();\"></TD>";
        	echo "<TD><INPUT TYPE=\"TEXT\" NAME=\"group_desc_".$tabgroup_["id"][$bzm]."\" STYLE=\"width:130px;\" VALUE=\"".$tabgroup_["beschreibung"][$bzm]."\" OnChange=\"document.form1.group_change.value='".$tabgroup_["id"][$bzm]."';document.form1.submit();\"></TD>";
        	
            // select parent subgroup
        	echo "<TD>";            
        	echo "<SELECT STYLE=\"width:120px;\" NAME=\"subgroup_".$tabgroup_["id"][$bzm]."\" OnChange=\"document.form1.subgroup_change.value='".$tabgroup_["id"][$bzm]."';document.form1.submit();\"><OPTION VALUE=\"0\"></OPTION>";
        	foreach($tabgroup_["id"] as $bzm1 => $value1){
                // dont show the current subgroup as option
        		if($value1 != $value) {
                    // dont show any tabgroup-children of the current subgroup as option
                    if(!array_key_exists($value, getTabgroupParents($value1))){
                        if($value1 == $tabgroup_["level"][$bzm]){$SELECTED = "SELECTED";}else{$SELECTED = "";}
                        echo "<OPTION VALUE=\"$value1\" $SELECTED>".$tabgroup_["name"][$bzm1]."</OPTION>";
                    }
        		}
        	}
        	echo "</SELECT>";
        	echo "</TD>";
        	
        	echo "<TD><INPUT TYPE=\"TEXT\" NAME=\"icongroup_".$tabgroup_["id"][$bzm]."\" STYLE=\"width:130px;\" VALUE=\"".$tabgroup_["icon"][$bzm]."\" OnChange=\"document.form1.icongroup_change.value='".$tabgroup_["id"][$bzm]."';document.form1.submit();\"></TD>";
        	
        	
            echo "<TD class=\"vAlignMiddle txtAlignLeft\">".$tabgroup_["tabellen"][$bzm]."</TD>";
        	echo "</TR>";
        	$bzm++;
        }

        ?>
        <TR class="tabBody"><TD COLSPAN="4"></TD><TD><INPUT TYPE="TEXT" NAME="group_name"></TD><TD><INPUT TYPE="TEXT" NAME="group_desc"></TD><TD></TD><TD><INPUT TYPE="submit" VALUE="<?=$lang[540]?>" NAME="group_add"></TD><TD></TD>
        <TR><TD COLSPAN="8" class="tabFooter"></TD></TR>
        </TABLE>
        </FORM>
        <?php
}else{
	/* --- Tabellen-Liste --------------------------------------------- */
	$bzm = 0;
	echo "<TABLE>";

	/* --- Spaltenüberschriften --------------------------------------- */
	?>
	<FORM ACTION="main_admin.php" METHOD="post" NAME="form2">
	<input type="hidden" name="action" value="setup_tab">
	<input type="hidden" name="bzm" value="<?= $bzm ?>">
	<input type="hidden" name="tabid">
	<input type="hidden" name="desc">
	<input type="hidden" name="tabname">
	<input type="hidden" name="tab_group" value="<?= $tab_group ?>">
	<input type="hidden" name="group_bzm" value="<?= $group_bzm ?>">
	<input type="hidden" name="breite">
	<input type="hidden" name="lockable">
	<input type="hidden" name="logging">
	<input type="hidden" name="groupable">
	<input type="hidden" name="markcolor">
	<input type="hidden" name="linecolor">
	<input type="hidden" name="versioning">
	<input type="hidden" name="add">
	<input type="hidden" name="userrules">
	<input type="hidden" name="trigger">
	<input type="hidden" name="indicator">
	<input type="hidden" name="setmaingroup">
	<input type="hidden" name="ajaxpost">
	<input type="hidden" name="numrowcalc">
	<input type="hidden" name="reserveid">
	<input type="hidden" name="datasync">
	<input type="hidden" name="multitenant">
	
	<?php if($result_gtab[$tab_group]["id"]){?>
	
	<TABLE class="tabfringe" BORDER="0" cellspacing="1" cellpadding="2">
	<TR class="tabHeader"><TD class="tabHeaderItem" colspan="24"><?= $lang[1029] ?>: <?= $tabgroup_["name"][$tab_group] ?></TD></TR>

	<TR class="tabHeader">
	<TD class="tabHeaderItem" nowrap>ID</TD>
	<TD class="tabHeaderItem" nowrap colspan="2"></TD>
	<TD class="tabHeaderItem" nowrap><?=$lang[952]?></TD>
	<TD class="tabHeaderItem" nowrap><?=$lang[160]?></TD>
	<TD class="tabHeaderItem" nowrap><?=$lang[951]?></TD>
	<TD class="tabHeaderItem" nowrap><?=$lang[924]?></TD>
	<TD class="tabHeaderItem" nowrap><?=$lang[900]?></TD>
	<TD class="tabHeaderItem" nowrap>1:1 <?=$lang[1460]?></TD>
	<TD class="tabHeaderItem" nowrap><?=$lang[925]?></TD>
	<TD class="tabHeaderItem" nowrap><?=$lang[953]?></TD>
	<TD class="tabHeaderItem" nowrap><?=$lang[294]?></TD>
	<TD class="tabHeaderItem" nowrap><?=$lang[1779]?></TD>
    <TD class="tabHeaderItem" nowrap><?=$lang[657]?></TD>
    <TD class="tabHeaderItem" nowrap><?=$lang[1601]?></TD>
    <TD class="tabHeaderItem" nowrap><?=$lang[575]?></TD>
    <TD class="tabHeaderItem" nowrap><?=$lang[2640]?></TD>
    <TD class="tabHeaderItem" nowrap><?=$lang[1465]?></TD>
    <TD class="tabHeaderItem" nowrap><?=$lang[2703]?></TD>
    <TD class="tabHeaderItem" nowrap>Sync</TD>
    <TD class="tabHeaderItem" nowrap><?=$lang[2962]?></TD>
    <TD class="tabHeaderItem" nowrap><?=$lang[2132]?></TD>
    <TD class="tabHeaderItem" nowrap><?=$lang[2688]?></TD>
    <TD class="tabHeaderItem" nowrap><?=$lang[1255]?></TD>
    <TD class="tabHeaderItem" nowrap><?=$lang[2216]?></TD>
    <TD class="tabHeaderItem" nowrap><?=$lang[575]?></TD>
	</TR>

	<?php
	/* --- Ergebnisliste --------------------------------------- */
	foreach($result_gtab[$tab_group]["id"] as $bzm => $value){
		if($result_gtab[$tab_group]["typ"][$bzm] == 5){$isview = 1;}else{$isview = 0;}
		$gtabid = $result_gtab[$tab_group]["id"][$bzm];
		?>
			<TR class="tabBody">
			<TD class="vAlignMiddle"><?= $result_gtab[$tab_group]["id"][$bzm] ?></TD>
            
			<TD class="vAlignMiddle" nowrap>
			<?php if($gfield[$result_gtab[$tab_group]["id"][$bzm]] OR !$isview){?>
                            <A HREF="main_admin.php?&action=setup_gtab_ftype&group_bzm=<?=$group_bzm?>&tab_group=<?=$tab_group?>&atid=<?=$result_gtab[$tab_group]["id"][$bzm]?>" title="<?=$lang[2689]?>"><i class="lmb-icon lmb-pencil" BORDER="0" style="cursor:pointer"></i></A>
                            <A onclick="activ_menu=1;ajaxEditTable(null,'<?=$gtabid?>','<?=$tab_group?>')" title="<?=$lang[2689]?>"><i class="lmb-icon lmb-cog-alt" BORDER="0" style="cursor:pointer"></i></A>
			<?php }?>
			</TD><TD class="vAlignMiddle" nowrap>
			<?php
			if($isview){?>
                            <A HREF="main_admin.php?&action=setup_gtab_view&viewid=<?=$result_gtab[$tab_group]["id"][$bzm]?>" title="<?=$lang[2690]?>"><i class="lmb-icon lmb-organisation-edit" BORDER="0" style="cursor:pointer"></i></A>
            <?php }?>
            </TD>
            <TD class="vAlignMiddle txtAlignCenter"><i class="lmb-icon lmb-long-arrow-up" BORDER="0" onclick="document.location.href='main_admin.php?action=setup_tab&tab_group=<?= $tab_group ?>&up=1;?>&fieldid=<?= $result_gtab[$tab_group]['ID'][$bzm] ?>';"></i>&nbsp;<i class="lmb-icon lmb-long-arrow-down" BORDER="0" onclick="document.location.href='main_admin.php?action=setup_tab&tab_group=<?= $tab_group ?>&down=1;&fieldid=<?= $result_gtab[$tab_group]['ID'][$bzm] ?>';"></i></TD>
            <TD class="vAlignMiddle txtAlignCenter">
            <?php if($result_gtab[$tab_group]["tabelle"][$bzm] != "LDMS_FILES" AND $result_gtab[$tab_group]["tabelle"][$bzm] != "LDMS_META"){?>
                <i class="lmb-icon lmb-trash" style="float:left;" BORDER="0" style="cursor:pointer" OnClick="tab_delete('<?=$group_bzm?>','<?=$tab_group?>','<?=$bzm?>','<?=urlencode(lmb_strtoupper($result_gtab[$tab_group]['tabelle'][$bzm]))?>','<?=$result_gtab[$tab_group]["id"][$bzm]?>',0)"></i>
                <i class="lmb-icon lmb-minus-circle" BORDER="0" style="cursor:pointer;height:13px;vertical-align:bottom" OnClick="tab_delete('<?=$group_bzm?>','<?=$tab_group?>','<?=$bzm?>','<?=urlencode(lmb_strtoupper($result_gtab[$tab_group]['tabelle'][$bzm]))?>','<?=$result_gtab[$tab_group]["id"][$bzm]?>',1)"></i>
            <?php }?>
            </TD>

			<TD class="vAlignMiddle"><INPUT TYPE="TEXT" SIZE="25" VALUE="<?= $result_gtab[$tab_group]["tabelle"][$bzm] ?>" OnChange="document.form2.tabname.value=this.value;document.form2.tabid.value='<?= $result_gtab[$tab_group]['id'][$bzm] ?>';document.form2.submit();"></TD>
			<TD class="vAlignMiddle"><INPUT TYPE="TEXT" SIZE="25" VALUE="<?= $result_gtab[$tab_group]["beschreibung"][$bzm] ?>" OnChange="document.form2.desc.value=this.value;document.form2.tabid.value='<?= $result_gtab[$tab_group]['id'][$bzm] ?>';document.form2.submit();"></TD>
			
			<TD class="vAlignMiddle txtAlignLeft">
			<?php
			# maingroup
        	echo "<SELECT STYLE=\"width:120px;\" NAME=\"subgroup_".$tabgroup_["id"][$bzm]."\" OnChange=\"document.form2.setmaingroup.value=this.value;document.form2.tabid.value='".$result_gtab[$tab_group]["id"][$bzm]."';document.form2.submit();\"><OPTION VALUE=\"0\"></OPTION>";
        	foreach($tabgroup_["id"] as $bzm1 => $value1){
        		if($value1 == $result_gtab[$tab_group]["maingroup"][$bzm]){$SELECTED = "SELECTED";}else{$SELECTED = "";}
        		echo "<OPTION VALUE=\"$value1\" $SELECTED>".$tabgroup_["name"][$bzm1]."</OPTION>";
        	}
        	echo "</SELECT>";
			?>
			</TD>
			
			
			<TD class="vAlignMiddle txtAlignLeft">
			<?php
			if($isview AND !$result_gtab[$tab_group]["num_gtab"][$bzm]){$result_gtab[$tab_group]["verkn"][$bzm] = $lang[2699];}
			elseif($isview){$result_gtab[$tab_group]["verkn"][$bzm] = "";}
			echo $result_gtab[$tab_group]["verkn"][$bzm];
			?>
			&nbsp;</TD>


			<TD class="vAlignMiddle txtAlignLeft">
			<?php
			# typ
			if($result_gtab[$tab_group]["typ"][$bzm] == 1){echo $lang[164]."&nbsp;";}
			elseif($result_gtab[$tab_group]["typ"][$bzm] == 2){echo $lang[1929]."&nbsp;";}
			elseif($result_gtab[$tab_group]["typ"][$bzm] == 6){echo $lang[767]."&nbsp;";}
			elseif($result_gtab[$tab_group]["typ"][$bzm] == 5){
				if($result_gtab[$tab_group]["viewtype"][$bzm] == 1){
					echo $lang[2656]."&nbsp;";
				}elseif($result_gtab[$tab_group]["viewtype"][$bzm] == 2){
					echo $lang[2657]."&nbsp;";
				}elseif($result_gtab[$tab_group]["viewtype"][$bzm] == 3){
					echo $lang[2658]."&nbsp;";
				}elseif($result_gtab[$tab_group]["viewtype"][$bzm] == 4){
					echo $lang[2659]."&nbsp;";
				}else{
					echo $lang[2656]."&nbsp;";
				}
			}
			?>
			</TD>

			<TD class="vAlignMiddle txtAlignCenter"><?= $result_gtab[$tab_group]["num_gtab"][$bzm] ?>&nbsp;</TD>
            
            <TD class="vAlignMiddle txtAlignCenter"><DIV id="color_select_<?=$result_gtab[$tab_group]["id"][$bzm]?>" OnClick="div4(this, event,'<?=$result_gtab[$tab_group]["id"][$bzm]?>')" STYLE="cursor:pointer;width:20px;height:20px;border:1px solid black;background-color:<?php if($result_gtab[$tab_group]['markcolor'][$bzm]){echo $result_gtab[$tab_group]['markcolor'][$bzm];}else{echo $farbschema['WEB10'];}?>"></DIV></TD>

            <?php if($result_gtab[$tab_group]["logging"][$bzm] == 1){$CHECKED = "CHECKED";}else{$CHECKED = "";}?>
            <TD class="vAlignMiddle txtAlignCenter">
            <?php if(!$isview){?><INPUT TYPE="CHECKBOX" VALUE="1" <?=$CHECKED?> OnClick="document.form2.tabid.value=<?= $result_gtab[$tab_group]["id"][$bzm] ?>;document.form2.tab_group.value=<?= $tab_group ?>;if(this.checked){document.form2.logging.value=1;}else{document.form2.logging.value=2;};document.form2.submit();"><?php }?>
            </TD>

            <?php if($result_gtab[$tab_group]["lockable"][$bzm] == 1){$CHECKED = "CHECKED";}else{$CHECKED = "";}?>
            <TD class="vAlignMiddle txtAlignCenter">
            <?php if(!$isview){?><INPUT TYPE="CHECKBOX" VALUE="1" <?=$CHECKED?> OnClick="document.form2.tabid.value=<?= $result_gtab[$tab_group]["id"][$bzm] ?>;document.form2.tab_group.value=<?= $tab_group ?>;if(this.checked){document.form2.lockable.value=1;}else{document.form2.lockable.value=2;};document.form2.submit();"><?php }?>
            </TD>

            <?php if($result_gtab[$tab_group]["linecolor"][$bzm] == 1){$CHECKED = "CHECKED";}else{$CHECKED = "";}?>
            <TD class="vAlignMiddle txtAlignCenter">
            <?php if(!$isview){?><INPUT TYPE="CHECKBOX" VALUE="1" <?=$CHECKED?> OnClick="document.form2.tabid.value=<?= $result_gtab[$tab_group]["id"][$bzm] ?>;document.form2.tab_group.value=<?= $tab_group ?>;if(this.checked){document.form2.linecolor.value=1;}else{document.form2.linecolor.value=2;};document.form2.submit();"><?php }?>
            </TD>

            <?php if($result_gtab[$tab_group]["userrules"][$bzm] == 1){$CHECKED = "CHECKED";}else{$CHECKED = "";}?>
            <TD class="vAlignMiddle txtAlignCenter">
            <?php if(!$isview){?><INPUT TYPE="CHECKBOX" VALUE="1" <?=$CHECKED?> OnClick="tab_userrule(this,'<?=$result_gtab[$tab_group]["id"][$bzm]?>','<?=$tab_group?>')"><?php }?>
            </TD>
            
            <?php if($result_gtab[$tab_group]["ajaxpost"][$bzm] == 1){$CHECKED = "CHECKED";}else{$CHECKED = "";}?>
            <TD class="vAlignMiddle txtAlignCenter">
            <?php if(!$isview){?><INPUT TYPE="CHECKBOX" VALUE="1" <?=$CHECKED?> OnClick="document.form2.tabid.value=<?= $result_gtab[$tab_group]["id"][$bzm] ?>;document.form2.tab_group.value=<?= $tab_group ?>;if(this.checked){document.form2.ajaxpost.value=1;}else{document.form2.ajaxpost.value=2;};document.form2.submit();"><?php }?>
            </TD>

            <TD class="vAlignMiddle txtAlignCenter">
            <?php
            if(!$isview){
            if($result_gtab[$tab_group]["groupable"][$bzm]){$CHECKED = "CHECKED";}else{$CHECKED = "";}?>
            <?php if(!$isview){?><INPUT TYPE="CHECKBOX" VALUE="1" <?=$CHECKED?> OnClick="document.form2.tabid.value=<?= $result_gtab[$tab_group]["id"][$bzm] ?>;document.form2.tab_group.value=<?= $tab_group ?>;if(this.checked){document.form2.groupable.value=1;}else{document.form2.groupable.value=2;};document.form2.submit();"><?php }?>
            <?php }?>
            </TD>
            
            <?php if($result_gtab[$tab_group]["reserveid"][$bzm] == 1){$CHECKED = "CHECKED";}else{$CHECKED = "";}?>
            <TD class="vAlignMiddle txtAlignCenter">
            <?php if(!$isview){?><INPUT TYPE="CHECKBOX" VALUE="1" <?=$CHECKED?> OnClick="document.form2.tabid.value=<?= $result_gtab[$tab_group]["id"][$bzm] ?>;document.form2.tab_group.value=<?= $tab_group ?>;if(this.checked){document.form2.reserveid.value=1;}else{document.form2.reserveid.value=2;};document.form2.submit();"><?php }?>
            </TD>
            
            <?php if($result_gtab[$tab_group]["datasync"][$bzm] == 1){$CHECKED = "CHECKED";}else{$CHECKED = "";}?>
            <TD class="vAlignMiddle txtAlignCenter">
            <?php if(!$isview){?><INPUT TYPE="CHECKBOX" VALUE="1" <?=$CHECKED?> OnClick="document.form2.tabid.value=<?= $result_gtab[$tab_group]["id"][$bzm] ?>;document.form2.tab_group.value=<?= $tab_group ?>;if(this.checked){document.form2.datasync.value=1;}else{document.form2.datasync.value=2;};document.form2.submit();"><?php }?>
            </TD>

            <?php if($result_gtab[$tab_group]["multitenant"][$bzm] == 1){$CHECKED = "CHECKED";}else{$CHECKED = "";}?>
            <TD class="vAlignMiddle txtAlignCenter">
            <INPUT TYPE="CHECKBOX" VALUE="1" <?=$CHECKED?> OnClick="document.form2.tabid.value=<?= $result_gtab[$tab_group]["id"][$bzm] ?>;document.form2.tab_group.value=<?= $tab_group ?>;if(this.checked){document.form2.multitenant.value=1;}else{document.form2.multitenant.value=2;};document.form2.submit();">
            </TD>
            
            <?php if($result_gtab[$tab_group]["id"][$bzm] == $result_gtab[$tab_group]["verknid"][$bzm] AND !$isview){?>
            <TD class="vAlignMiddle txtAlignCenter">
            <SELECT OnChange="set_versioning('<?= $result_gtab[$tab_group]["id"][$bzm] ?>','<?= $tab_group ?>',this.value);">
            <OPTION VALUE="-1" <?php if(!$result_gtab[$tab_group]["versioning"][$bzm]){echo "SELECTED";}?>>
            <OPTION VALUE="1" <?php if($result_gtab[$tab_group]["versioning"][$bzm] == 1){echo "SELECTED";}?>><?=$lang[2142]?>
            <OPTION VALUE="2" <?php if($result_gtab[$tab_group]["versioning"][$bzm] == 2){echo "SELECTED";}?>><?=$lang[2143]?>
            </SELECT></TD>
            <?php }else{?>
            <TD class="vAlignMiddle txtAlignCenter">
            <?php if($result_gtab[$tab_group]["versioning"][$bzm] == 1){echo $lang[2142];}
			elseif($result_gtab[$tab_group]["versioning"][$bzm] == 2){echo $lang[2143];}?>
			</TD>
			<?php }?>

			
			<TD class="vAlignMiddle txtAlignCenter">
            <?php if($result_gtab[$tab_group]["num_gtab"][$bzm] > 0){?>
			<SELECT OnChange="document.form2.tabid.value=<?= $result_gtab[$tab_group]["id"][$bzm] ?>;document.form2.tab_group.value=<?= $tab_group ?>;document.form2.numrowcalc.value=this.value;document.form2.submit();">
            <OPTION VALUE="-1" <?php if(!$result_gtab[$tab_group]["numrowcalc"][$bzm]){echo "SELECTED";}?>><?=$lang[2685]?>
            <OPTION VALUE="1" <?php if($result_gtab[$tab_group]["numrowcalc"][$bzm] == 1){echo "SELECTED";}?>><?=$lang[2686]?>
            <OPTION VALUE="2" <?php if($result_gtab[$tab_group]["numrowcalc"][$bzm] == 2){echo "SELECTED";}?>><?=$lang[2687]?>
            </SELECT>
            <?php }?>
            </TD>

            <td VALIGN="TOP" ALIGN="CENTER" nowrap><i style="float:left;" class="lmb-icon lmb-indicator-rule"></i><TEXTAREA NAME="indicator_rule_<?=$result_gtab[$tab_group]["id"][$bzm]?>" readonly style="width:60px;height:17px;cursor:pointer;" OnClick="show_indicator(this,'<?=$result_gtab[$tab_group]["id"][$bzm]?>')" title="<?=$lang[1255]?>"><?=$result_gtab[$tab_group]["indicator"][$bzm]?></TEXTAREA></td>
            
            <TD class="vAlignMiddle txtAlignCenter">
            <?php if($gtrigger[$gtabid]["id"]){?>
			<SPAN STYLE="display:none;position:absolute" ID="tab_trigger_<?=$gtabid?>" OnClick="activ_menu=1">
			<SELECT NAME="tab_trigger_<?=$gtabid?>[]" STYLE="width:200px;" MULTIPLE SIZE="5" OnChange="document.form2.tabid.value=<?= $result_gtab[$tab_group]["id"][$bzm] ?>;document.form2.tab_group.value=<?= $tab_group ?>;document.form2.trigger.value='<?=$gtabid?>';" onblur="if(document.form2.trigger.value=='<?=$gtabid?>'){document.form2.submit();}"><OPTION VALUE="0">
			<?php
			$trlist = array();
			foreach($gtrigger[$gtabid]["id"] as $trid => $trval){
				if(in_array($trid,$result_gtab[$tab_group]["trigger"][$bzm])){$SELECTED = "SELECTED";$trlist[] = $gtrigger[$gtabid]["trigger_name"][$trid];}else{$SELECTED = "";}
				echo "<OPTION VALUE=\"".$trid."\" $SELECTED>".$gtrigger[$gtabid]["trigger_name"][$trid]." (".$gtrigger[$gtabid]["type"][$trid].")</OPTION>";
			}
			?> 
			</SELECT>
			</SPAN>
			<INPUT TYPE="TEXT" STYLE="width:100px;" VALUE="<?=implode(";",$trlist)?>" OnClick="activ_menu=1;document.getElementById('tab_trigger_<?=$gtabid?>').style.display=''">
			<?php }?>
            </TD>
            
            
            <TD class="vAlignMiddle" align="center">
            <i style="cursor:pointer" title="<?=$lang[1054]?>" class="lmb-icon lmb-refresh" onclick="open('main_admin.php?action=setup_grusrref&check_table=<?=$gtabid?>&check_all=1' ,'refresh','toolbar=0,location=0,status=1,menubar=0,scrollbars=1,resizable=1,width=550,height=400')"></i>
            </TD>

            </TR>
		<?php
		$bzm++;
	}
	echo "<TR><TD COLSPAN=\"24\" class=\"tabFooter\"></TD></TR></table>";
	echo "<br>";
	}
	?>

	
	
	<TABLE class="tabfringe" BORDER="0" cellspacing="1" cellpadding="2" >

	<TR class="tabHeader">
	<TD class="tabHeaderItem"><?=$lang[951]?></TD>
	<TD class="tabHeaderItem"><?=$lang[924]?></TD>
    <TD class="tabHeaderItem">1:1 <?=$lang[1460]?></TD>
    <TD class="tabHeaderItem"><?=$lang[1464]?></TD>
    <TD class="tabHeaderItem"><?=$lang[925]?></TD>
    <TD class="tabHeaderItem" colspan="2">&nbsp;</TD>
	</TR>


	<TR class="tabBody">
	<TD valign="top"><INPUT TYPE="TEXT" SIZE="25" NAME="gtable" ONCHANGE="this.form.beschreibung.value=this.value"></TD>
	<TD valign="top"><INPUT TYPE="TEXT" SIZE="25" NAME="beschreibung"></TD>
    <TD valign="top"><SELECT NAME="verkn"><OPTION VALUE="0">
    <?php
    foreach($tabgroup_["id"] as $key => $tabgroupID){
        if($result_gtab[$tabgroupID]["id"] && $result_gtab[$tabgroupID]["id"][1]){
            echo "<optgroup label='{$tabgroup_['name'][$key]}'>";
            foreach($result_gtab[$tabgroupID]["id"] as $tableKey => $tableID){
                $desc = $result_gtab[$tabgroupID]["beschreibung"][$tableKey];
                if ($desc == null) { $desc = $result_gtab[$tabgroupID]["tabelle"][$tableKey]; }
                echo "<option value=\"{$tableID}\">$desc</option>";
            }
            echo "</optgroup>";
        }
    }
    
    echo "<TD class=\"vAlignTop txtAlignLeft\"><select name=\"copy\" id=\"new_copy\"><option>";
    if($result_gtab[$tab_group]["tabelle"]){
        foreach($result_gtab[$tab_group]["tabelle"] as $bzm => $value){
            echo "<option value=\"".$result_gtab[$tab_group]["id"][$bzm]."\">".$value."</option>";
        }
    }
	echo "</select></TD>";
	
	?>
    <OPTION VALUE="3">
    </SELECT></TD>

    <TD valign="top"><SELECT NAME="typ" OnChange="extendTabTyp(this)">
    <?php
	echo "<OPTION VALUE=\"1\">".$lang[164];  # table
	echo "<OPTION VALUE=\"2\">".$lang[1929];  # calendar
    echo "<OPTION VALUE=\"7\">".'Kanban';
	echo "<OPTION VALUE=\"6\">".$lang[767]; # messages
    echo "<OPTION VALUE=\"8\">".$lang[428]; # report template
	echo "<OPTION VALUE=\"5\">".$lang[2023]; # view
	?>
    
    </SELECT>
    
    <div><SELECT NAME="viewtyp" style="display:none">
	<OPTION VALUE="1"><?=$lang[2656]?>
	<OPTION VALUE="2"><?=$lang[2657]?>
	<OPTION VALUE="3"><?=$lang[2658]?>
	<OPTION VALUE="4"><?=$lang[2659]?>
    </SELECT></div>
    
    </TD>

    <td valign="top"><INPUT TYPE="button" VALUE="<?=$lang[540]?>" OnClick="add_tab()">&nbsp;&nbsp;&nbsp;&nbsp;</td>

    <TD valign="top">
    <table cellpadding=0 cellspacing=0>
    <tr><td><?=$lang[1263]?></td>
    <td><INPUT TYPE="CHECKBOX" NAME="add_permission" VALUE="1" CHECKED></td></tr>
    <tr><td><?=$lang[2664]?></td>
    <td><INPUT TYPE="CHECKBOX" NAME="use_serial" VALUE="1"></td></tr>
    <tr><td><?=$lang[2665]?></td>
    <?php if(LMB_DBFUNC_SEQUENCE){?>
    <td><INPUT TYPE="CHECKBOX" NAME="use_sequence" VALUE="1" readonly disabled checked></td></tr>
    <?php }?>
    
    </tr></table>
    </TD>
    </TR>
    
    <TR><TD COLSPAN="6" class="tabFooter"></TD></TR>
    
    </TABLE>

    </FORM>

<?php }?>

</div>