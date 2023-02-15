<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */



//TODO: Farbauswahl und Indikator funktionieren nicht

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
<TEXTAREA ID="indicator_value" OnChange="document.form2.tabid.value=currenttabid;document.form2.indicator.value=' '+this.value;document.form2.submit();" STYLE="width:150px;height:100px"></TEXTAREA>
<?php pop_right();?>
<?php pop_bottom();?>
</DIV>




<script>

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

    document.getElementById(el).style.left=evt.pageX;
    document.getElementById(el).style.top=evt.pageY;

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
		if(el.checked){document.form2.userrules.value=1;}else{document.form2.userrules.value=2;}
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
            if(el.value) {
                document.form3.val.value = el.value;
            }else{
                document.form3.val.value = el.value+' ';
            }
		}
	}


	ajaxGet(null,"main_dyns_admin.php","editTable&tabid="+gtabid+"&tabgroup="+tabgroup+"&act=" + act,null,"ajaxEditTablePost","form3");
}

function ajaxEditTablePost(result){

    ajaxEvalScript(result);

    $('#tablesettingsContent').html(result);
    $('#tableSettingsModal').modal('show');
    
}

function tab_delete(group_bzm,tab_group,bzm,gtable,tabid){

    $('#tableDeleteModal').modal('show');
    $('#tableDeleteModalTitle').html('<?=$lang[2287]?> ('+gtable+')');

    buttonTableDelete = document.getElementById("buttonTableDelete");
    buttonTableRemove = document.getElementById("buttonTableRemove");

    buttonTableDelete.replaceWith(buttonTableDelete.cloneNode(true));
    buttonTableRemove.replaceWith(buttonTableRemove.cloneNode(true));

    buttonTableDelete = document.getElementById("buttonTableDelete");
    buttonTableRemove = document.getElementById("buttonTableRemove");

    buttonTableDelete.addEventListener("click", function() {
        document.location.href="main_admin.php?action=setup_tab&group_bzm="+group_bzm+"&tab_group="+tab_group+"&bzm="+bzm+"&gtable="+gtable+"&tabid="+tabid+"&del=1&drop_physical=1";
    });

    buttonTableRemove.addEventListener("click", function() {
        document.location.href="main_admin.php?action=setup_tab&group_bzm="+group_bzm+"&tab_group="+tab_group+"&bzm="+bzm+"&gtable="+gtable+"&tabid="+tabid+"&del=1";
    });

}

</script>

<div id="lmbAjaxContainer" class="ajax_container" style="position:absolute;visibility:hidden;" OnClick="activ_menu=1;"></div>



<div class="modal fade" id="tableDeleteModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="tableDeleteModalTitle"><?=$lang[2287]?></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p><?=$lang[1727]?></p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" id="buttonTableDelete"><?=$lang[367]?></button>
          <button type="button" class="btn btn-warning" id="buttonTableRemove"><?=$lang[2811]?></button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?=$lang[844]?></button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="tableSettingsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?=$lang[1896]?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="tablesettingsContent"></div>
        </div>
    </div>
</div>


<?php
/* --- Gruppen-Liste --------------------------------------------- */
if(!$tab_group) : ?>
    <div class="container-fluid p-3">
	    <FORM ACTION="main_admin.php" METHOD="post" NAME="form1">
            <input type="hidden" name="action" value="setup_tab">
            <input type="hidden" name="group_change">
            <input type="hidden" name="subgroup_change">
            <input type="hidden" name="icongroup_change">
            <input type="hidden" name="tab_group" value="<?=$tab_group;?>">

            <table class="table table-sm table-striped mb-0 border bg-white">
        <thead>
            <tr>
                <th><?=$lang[949]?>&nbsp;</th>
                <th></th>
                <th><?=$lang[952]?></th>
                <th><?=$lang[160]?></th>
                <th><?=$lang[924]?></th>
                <th><?=$lang[923]?></th>
                <th><?=$lang[897]?></th>
                <th>Icon</th>
                <th><?=$lang[577]?></th>
            </tr>
        </thead>

        <?php foreach($tabgroup_["id"] as $bzm => $value) : ?>
        
        <tr>
            <td><A HREF="main_admin.php?action=setup_tab&group_bzm=<?=$bzm?>&tab_group=<?=$tabgroup_['id'][$bzm]?>"><?=$tabgroup_['id'][$bzm]?></A></td>
            <td><A HREF="main_admin.php?action=setup_tab&group_bzm=<?=$bzm?>&tab_group=<?=$tabgroup_['id'][$bzm]?>"><i class="lmb-icon lmb-pencil cursor-pointer"></i></A></td>

            <td><i class="lmb-icon lmb-long-arrow-up" OnClick="document.location.href='main_admin.php?action=setup_tab&group_change=<?=$tabgroup_['id'][$bzm]?>&sort_id=<?=$tabgroup_['id'][$bzm]?>&gup=1'"></i>&nbsp;<i class="lmb-icon lmb-long-arrow-down" OnClick="document.location.href='main_admin.php?action=setup_tab&group_change=<?=$tabgroup_['id'][$bzm]?>&sort_id=<?=$tabgroup_['id'][$bzm]?>&gdown=1'"></i></td>
            
            <td>
                <?php if (!$tabgroup_["systemtab"][$bzm]): ?>
                    <i class="lmb-icon lmb-trash cursor-pointer" OnClick="group_delete('<?=$tabgroup_['id'][$bzm]?>')"></i>
                <?php endif; ?>
            </td>

            <td><input TYPE="TEXT" NAME="group_name_<?=$tabgroup_['id'][$bzm]?>" VALUE="<?=$tabgroup_["name"][$bzm]?>" OnChange="document.form1.group_change.value='<?=$tabgroup_['id'][$bzm]?>';document.form1.submit();" class="form-control form-control-sm"></td>
            
            <td><input TYPE="TEXT" NAME="group_desc_<?=$tabgroup_['id'][$bzm]?>" VALUE="<?=$tabgroup_["name"][$bzm]?>" OnChange="document.form1.group_change.value='<?=$tabgroup_['beschreibung'][$bzm]?>';document.form1.submit();" class="form-control form-control-sm"></td>
            
            <td>
                <SELECT NAME="subgroup_<?=$tabgroup_['id'][$bzm]?>" OnChange="document.form1.subgroup_change.value='<?=$tabgroup_['id'][$bzm]?>';document.form1.submit();" class="form-select form-select-sm">
                    <OPTION VALUE="0"></OPTION>
            <?php

            // select parent subgroup
            foreach($tabgroup_['id'] as $bzm1 => $value1){
                // dont show the current subgroup as option
                if($value1 != $value) {
                    // dont show any tabgroup-children of the current subgroup as option
                    if(!array_key_exists($value, getTabgroupParents($value1))){
                        echo "<OPTION VALUE=\"$value1\" ".(($value1 == $tabgroup_["level"][$bzm])?'selected':'').">".$tabgroup_["name"][$bzm1]."</OPTION>";
                    }
                }
            }
            
            ?>
                </SELECT>
            </td>
            <td><INPUT TYPE="TEXT" NAME="icongroup_<?=$tabgroup_['id'][$bzm]?>" VALUE="<?=$tabgroup_["icon"][$bzm]?>" OnChange="document.form1.icongroup_change.value='<?=$tabgroup_['id'][$bzm]?>';document.form1.submit();" class="form-control form-control-sm"></td>
            <td><?=$tabgroup_["tabellen"][$bzm]?></td>
        </tr>
        
        
        <?php
            $bzm++;
            endforeach;
        ?>
        
        <tfoot>
            <tr>
                <td COLSPAN="4"></td>
                <td><INPUT TYPE="TEXT" NAME="group_name" class="form-control form-control-sm"></td>
                <td><INPUT TYPE="TEXT" NAME="group_desc" class="form-control form-control-sm"></td>
                <td><button type="submit" name="group_add" class="btn btn-primary btn-sm" value="1"><?=$lang[540]?></button></td>
                <td></td>
                <td></td>
            </tr>
        </tfoot>
        
    </table>
        
        </FORM>
        
    </div>
        <?php
else :?>


<div class="container-fluid p-3">
<?php
	/* --- Tabellen-Liste --------------------------------------------- */
	$bzm = 0;

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
        <input type="hidden" name="custmenu">
        <input type="hidden" name="reserveid">
        <input type="hidden" name="datasync">
        <input type="hidden" name="multitenant">


        <?php if($result_gtab[$tab_group]["id"]): ?>

            <h3><?= $lang[1029] ?>: <?= $tabgroup_["name"][$tab_group] ?></h3>
        
        <table class="table table-sm table-striped border bg-white">
            <thead>
                <tr>
                    <th nowrap>ID</th>
                    <th nowrap colspan="2"></th>
                    <th nowrap><?=$lang[952]?></th>
                    <th nowrap><?=$lang[160]?></th>
                    <th nowrap style="min-width:200px"><?=$lang[951]?></th>
                    <th nowrap style="min-width:200px"><?=$lang[924]?></th>
                    <th nowrap style="min-width:200px"><?=$lang[900]?></th>
                    <th nowrap>1:1 <?=$lang[1460]?></th>
                    <th nowrap><?=$lang[925]?></th>
                    <th nowrap><?=$lang[953]?></th>
                    <th nowrap><?=$lang[294]?></th>
                    <th nowrap><?=$lang[1779]?></th>
                    <th nowrap><?=$lang[657]?></th>
                    <th nowrap><?=$lang[1601]?></th>
                    <th nowrap><?=$lang[575]?></th>
                    <th nowrap><?=$lang[2640]?></th>
                    <th nowrap><?=$lang[2362]?></th>
                    <th nowrap><?=$lang[2703]?></th>
                    <th nowrap><?=$lang[2962]?></th>
                    <th nowrap>Sync</th>
                    <th nowrap><?=$lang[2132]?></th>
                    <th nowrap><?=$lang[2688]?></th>
                    <th nowrap><?=$lang[1255]?></th>
                    <th nowrap><?=$lang[2216]?></th>
                    <th nowrap><?=$lang[575]?></th>
                </tr>
            </thead>

            

            <?php
                /* --- Ergebnisliste --------------------------------------- */
                foreach($result_gtab[$tab_group]["id"] as $bzm => $value){
                    if($result_gtab[$tab_group]["typ"][$bzm] == 5){$isview = 1;}else{$isview = 0;}
                    $gtabid = $result_gtab[$tab_group]["id"][$bzm];
                    ?>
                    <TR>
                        <TD><?= $result_gtab[$tab_group]["id"][$bzm] ?></TD>

                        <TD nowrap>
                            <?php if($gfield[$result_gtab[$tab_group]["id"][$bzm]] OR !$isview){?>
                                <A HREF="main_admin.php?&action=setup_gtab_ftype&group_bzm=<?=$group_bzm?>&tab_group=<?=$tab_group?>&atid=<?=$result_gtab[$tab_group]["id"][$bzm]?>" title="<?=$lang[2689]?>"><i class="lmb-icon lmb-pencil cursor-pointer"></i></A>
                                <A onclick="activ_menu=1;ajaxEditTable(null,'<?=$gtabid?>','<?=$tab_group?>')" title="<?=$lang[2689]?>"><i class="lmb-icon lmb-cog-alt cursor-pointer"></i></A>
                            <?php }?>
                        </TD>
                        <TD nowrap>
                            <?php
                            if($isview){?>
                                <A HREF="main_admin.php?&action=setup_gtab_view&viewid=<?=$result_gtab[$tab_group]["id"][$bzm]?>" title="<?=$lang[2690]?>"><i class="lmb-icon lmb-organisation-edit cursor-pointer"></i></A>
                            <?php }?>
                        </TD>
                        <TD>
                            <i class="lmb-icon lmb-long-arrow-up" onclick="document.location.href='main_admin.php?action=setup_tab&tab_group=<?= $tab_group ?>&up=1;?>&fieldid=<?= $result_gtab[$tab_group]['ID'][$bzm] ?>';"></i>&nbsp;<i class="lmb-icon lmb-long-arrow-down" onclick="document.location.href='main_admin.php?action=setup_tab&tab_group=<?= $tab_group ?>&down=1;&fieldid=<?= $result_gtab[$tab_group]['ID'][$bzm] ?>';"></i>
                        </TD>
                        <TD>
                            <?php if($result_gtab[$tab_group]["tabelle"][$bzm] != "LDMS_FILES" AND $result_gtab[$tab_group]["tabelle"][$bzm] != "LDMS_META"){?>
                                <i class="lmb-icon lmb-trash cursor-pointer" OnClick="tab_delete('<?=$group_bzm?>','<?=$tab_group?>','<?=$bzm?>','<?=urlencode(lmb_strtoupper($result_gtab[$tab_group]['tabelle'][$bzm]))?>','<?=$result_gtab[$tab_group]["id"][$bzm]?>')"></i>
                            <?php }?>
                        </TD>

                        <TD><INPUT TYPE="TEXT" SIZE="25" VALUE="<?= $result_gtab[$tab_group]["tabelle"][$bzm] ?>" OnChange="document.form2.tabname.value=this.value;document.form2.tabid.value='<?= $result_gtab[$tab_group]['id'][$bzm] ?>';document.form2.submit();" class="form-control form-control-sm"></TD>
                        <TD><INPUT TYPE="TEXT" SIZE="25" VALUE="<?= $result_gtab[$tab_group]["beschreibung"][$bzm] ?>" OnChange="document.form2.desc.value=this.value;document.form2.tabid.value='<?= $result_gtab[$tab_group]['id'][$bzm] ?>';document.form2.submit();" class="form-control form-control-sm"></TD>

                        <TD>
                            <select STYLE="width:120px;" NAME="subgroup_<?=$tabgroup_["id"][$bzm]?>" OnChange="document.form2.setmaingroup.value=this.value;document.form2.tabid.value='<?=$result_gtab[$tab_group]["id"][$bzm]?>';document.form2.submit();" class="form-select form-select-sm">
                                <OPTION VALUE="0"></OPTION>
                            <?php
                            # maingroup
                            foreach($tabgroup_["id"] as $bzm1 => $value1){
                                echo "<OPTION VALUE=\"$value1\" ".(($value1 == $result_gtab[$tab_group]["maingroup"][$bzm])?'selected':'').">".$tabgroup_["name"][$bzm1]."</OPTION>";
                            }
                            
                            ?>
                            </select>
                        </TD>


                        <TD>
                            <?php
                            if($isview AND !$result_gtab[$tab_group]["num_gtab"][$bzm]){$result_gtab[$tab_group]["verkn"][$bzm] = $lang[2699];}
                            elseif($isview){$result_gtab[$tab_group]["verkn"][$bzm] = "";}
                            echo $result_gtab[$tab_group]["verkn"][$bzm];
                            ?>
                            &nbsp;</TD>


                        <TD>


                            <?php
                            # typ
                            if($result_gtab[$tab_group]["typ"][$bzm] == 1){echo $lang[164]."&nbsp;";}
                            elseif($result_gtab[$tab_group]["typ"][$bzm] == 2){echo $lang[1929]."&nbsp;";}
                            elseif($result_gtab[$tab_group]["typ"][$bzm] == 6){echo $lang[767]."&nbsp;";}
                            elseif($result_gtab[$tab_group]["typ"][$bzm] == 5){echo $lang[2023]."&nbsp;";}
                            elseif($result_gtab[$tab_group]["typ"][$bzm] == 7){echo "Kanban&nbsp;";}
                            elseif($result_gtab[$tab_group]["typ"][$bzm] == 8){echo $lang[428]."&nbsp;";}
                                /*
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
                                */
                            ?>
                        </TD>

                        <TD><?= $result_gtab[$tab_group]["num_gtab"][$bzm] ?>&nbsp;</TD>

                        <TD><DIV id="color_select_<?=$result_gtab[$tab_group]["id"][$bzm]?>" OnClick="div4(this, event,'<?=$result_gtab[$tab_group]["id"][$bzm]?>')" class="cursor-pointer" style="width:20px;height:20px;border:1px solid black;background-color:<?php if($result_gtab[$tab_group]['markcolor'][$bzm]){echo $result_gtab[$tab_group]['markcolor'][$bzm];}else{echo $farbschema['WEB10'];}?>"></DIV></TD>

                        <TD>
                            <?php if(!$isview){?>
                                <INPUT TYPE="CHECKBOX" VALUE="1" <?=($result_gtab[$tab_group]["logging"][$bzm] == 1)?'checked':''?> OnClick="document.form2.tabid.value=<?= $result_gtab[$tab_group]["id"][$bzm] ?>;document.form2.tab_group.value=<?= $tab_group ?>;if(this.checked){document.form2.logging.value=1;}else{document.form2.logging.value=2;}document.form2.submit();">
                            
                            <?php }?>
                        </TD>                        
                        <TD>
                            <?php if(!$isview){?>
                                <INPUT TYPE="CHECKBOX" VALUE="1" <?=($result_gtab[$tab_group]["lockable"][$bzm] == 1)?'checked':''?> OnClick="document.form2.tabid.value=<?= $result_gtab[$tab_group]["id"][$bzm] ?>;document.form2.tab_group.value=<?= $tab_group ?>;if(this.checked){document.form2.lockable.value=1;}else{document.form2.lockable.value=2;}document.form2.submit();">
                            <?php }?>
                        </TD>
                        <TD>
                            <?php if(!$isview){?><INPUT TYPE="CHECKBOX" VALUE="1" <?=($result_gtab[$tab_group]["linecolor"][$bzm] == 1)?'checked':''?> OnClick="document.form2.tabid.value=<?= $result_gtab[$tab_group]["id"][$bzm] ?>;document.form2.tab_group.value=<?= $tab_group ?>;if(this.checked){document.form2.linecolor.value=1;}else{document.form2.linecolor.value=2;}document.form2.submit();"><?php }?>
                        </TD>

                        <TD>
                            <?php if(!$isview){?>
                                <INPUT TYPE="CHECKBOX" VALUE="1" <?=($result_gtab[$tab_group]["userrules"][$bzm] == 1)?'checked':''?> OnClick="tab_userrule(this,'<?=$result_gtab[$tab_group]["id"][$bzm]?>','<?=$tab_group?>')">
                            <?php }?>
                        </TD>

                        <TD>
                            <?php if(!$isview){?>
                                <INPUT TYPE="CHECKBOX" VALUE="1" <?=($result_gtab[$tab_group]["ajaxpost"][$bzm] == 1)?'checked':''?> OnClick="document.form2.tabid.value=<?= $result_gtab[$tab_group]["id"][$bzm] ?>;document.form2.tab_group.value=<?= $tab_group ?>;if(this.checked){document.form2.ajaxpost.value=1;}else{document.form2.ajaxpost.value=2;}document.form2.submit();">
                            <?php }?>
                        </TD>

                        <TD>
                            <?php if(!$isview){ ?>
                                <INPUT TYPE="CHECKBOX" VALUE="1" <?=($result_gtab[$tab_group]["groupable"][$bzm])?'checked':''?> OnClick="document.form2.tabid.value=<?= $result_gtab[$tab_group]["id"][$bzm] ?>;document.form2.tab_group.value=<?= $tab_group ?>;if(this.checked){document.form2.groupable.value=1;}else{document.form2.groupable.value=2;}document.form2.submit();">
                            <?php }?>
                        </TD>

                        <TD>
                            <?php if(!$isview){?>
                                <INPUT TYPE="CHECKBOX" VALUE="1" <?=($result_gtab[$tab_group]["reserveid"][$bzm] == 1)?'checked':''?> OnClick="document.form2.tabid.value=<?= $result_gtab[$tab_group]["id"][$bzm] ?>;document.form2.tab_group.value=<?= $tab_group ?>;if(this.checked){document.form2.reserveid.value=1;}else{document.form2.reserveid.value=2;}document.form2.submit();">
                            <?php }?>
                        </TD>

                        <TD>
                            <INPUT TYPE="CHECKBOX" VALUE="1" <?=($result_gtab[$tab_group]["multitenant"][$bzm] == 1)?'checked':''?> OnClick="document.form2.tabid.value=<?= $result_gtab[$tab_group]["id"][$bzm] ?>;document.form2.tab_group.value=<?= $tab_group ?>;if(this.checked){document.form2.multitenant.value=1;}else{document.form2.multitenant.value=2;}document.form2.submit();">
                        </TD>
                        
                        <TD>
                            <?php if(!$isview){?>
                                <SELECT class="form-select form-select-sm" OnChange="document.form2.tabid.value=<?= $result_gtab[$tab_group]["id"][$bzm] ?>;document.form2.tab_group.value=<?= $tab_group ?>;document.form2.datasync.value=this.value;document.form2.submit();">
                                    <option VALUE=" " <?php if(!$result_gtab[$tab_group]["datasync"][$bzm]){echo "SELECTED";}?>></option>
                                    <option VALUE="1" <?php if($result_gtab[$tab_group]["datasync"][$bzm] == 1){echo "SELECTED";}?>>client based</option>
                                    <option VALUE="2" <?php if($result_gtab[$tab_group]["datasync"][$bzm] == 2){echo "SELECTED";}?>>global</option>
                                </SELECT>
                            <?php }?>
                        </TD>

                        <?php if($result_gtab[$tab_group]["id"][$bzm] == $result_gtab[$tab_group]["verknid"][$bzm] AND !$isview){?>
                            <TD>
                                <SELECT class="form-select form-select-sm" OnChange="set_versioning('<?= $result_gtab[$tab_group]["id"][$bzm] ?>','<?= $tab_group ?>',this.value);">
                                    <option VALUE="-1" <?php if(!$result_gtab[$tab_group]["versioning"][$bzm]){echo "SELECTED";}?>></option>
                                    <option VALUE="1" <?php if($result_gtab[$tab_group]["versioning"][$bzm] == 1){echo "SELECTED";}?>><?=$lang[2142]?></option>
                                    <option VALUE="2" <?php if($result_gtab[$tab_group]["versioning"][$bzm] == 2){echo "SELECTED";}?>><?=$lang[2143]?></option>
                                </SELECT>
                            </TD>
                        <?php }else{?>
                            <TD>
                                <?php if($result_gtab[$tab_group]["versioning"][$bzm] == 1){echo $lang[2142];}
                                elseif($result_gtab[$tab_group]["versioning"][$bzm] == 2){echo $lang[2143];}?>
                            </TD>
                        <?php }?>


                        <TD>
                            <?php if($result_gtab[$tab_group]["num_gtab"][$bzm] > 0){?>
                                <SELECT class="form-select form-select-sm" OnChange="document.form2.tabid.value=<?= $result_gtab[$tab_group]["id"][$bzm] ?>;document.form2.tab_group.value=<?= $tab_group ?>;document.form2.numrowcalc.value=this.value;document.form2.submit();">
                                    <option VALUE="-1" <?php if(!$result_gtab[$tab_group]["numrowcalc"][$bzm]){echo "SELECTED";}?>><?=$lang[2685]?></option>
                                    <option VALUE="1" <?php if($result_gtab[$tab_group]["numrowcalc"][$bzm] == 1){echo "SELECTED";}?>><?=$lang[2686]?></option>
                                    <option VALUE="2" <?php if($result_gtab[$tab_group]["numrowcalc"][$bzm] == 2){echo "SELECTED";}?>><?=$lang[2687]?></option>
                                </SELECT>
                            <?php }?>
                        </TD>

                        <td nowrap>
                            <i style="float:left;" class="lmb-icon lmb-indicator-rule"></i> <TEXTAREA class="form-control cursor-pointer" NAME="indicator_rule_<?=$result_gtab[$tab_group]["id"][$bzm]?>" readonly style="width:60px;height:17px;" OnClick="show_indicator(this,'<?=$result_gtab[$tab_group]["id"][$bzm]?>')" title="<?=$lang[1255]?>"><?=$result_gtab[$tab_group]["indicator"][$bzm]?></TEXTAREA>
                        </td>

                        <TD>
                            <?php if($gtrigger[$gtabid]["id"]){?>
                                <SPAN STYLE="display:none;position:absolute" ID="tab_trigger_<?=$gtabid?>" OnClick="activ_menu=1">
                                    <SELECT class="form-select form-select-sm" NAME="tab_trigger_<?=$gtabid?>[]" STYLE="width:200px;" MULTIPLE SIZE="5" OnChange="document.form2.tabid.value=<?= $result_gtab[$tab_group]["id"][$bzm] ?>;document.form2.tab_group.value=<?= $tab_group ?>;document.form2.trigger.value='<?=$gtabid?>';" onblur="if(document.form2.trigger.value=='<?=$gtabid?>'){document.form2.submit();}">
                                        <OPTION VALUE="0"></OPTION>
                                    <?php
                                    $trlist = array();
                                    foreach($gtrigger[$gtabid]["id"] as $trid => $trval){
                                        if(in_array($trid,$result_gtab[$tab_group]["trigger"][$bzm])){$SELECTED = "SELECTED";$trlist[] = $gtrigger[$gtabid]["trigger_name"][$trid];}else{$SELECTED = "";}
                                        echo "<OPTION VALUE=\"".$trid."\" $SELECTED>".$gtrigger[$gtabid]["trigger_name"][$trid]." (".$gtrigger[$gtabid]["type"][$trid].")</OPTION>";
                                    }
                                    ?> 
                                    </SELECT>
                                </SPAN>
                                <INPUT class="form-control form-control-sm" TYPE="TEXT" STYLE="width:100px;" VALUE="<?=implode(";",$trlist)?>" OnClick="activ_menu=1;document.getElementById('tab_trigger_<?=$gtabid?>').style.display=''">
                            <?php }?>
                        </TD>


                        <TD>
                            <i title="<?=$lang[1054]?>" class="lmb-icon lmb-refresh cursor-pointer" onclick="open('main_admin.php?action=setup_grusrref&check_table=<?=$gtabid?>&check_all=1' ,'refresh','toolbar=0,location=0,status=1,menubar=0,scrollbars=1,resizable=1,width=550,height=400')"></i>
                        </TD>

                    </TR>
                    <?php
                    $bzm++;
                }
                
            ?>

        </table>
        <?php endif; ?>


        <table class="table table-sm table-striped mb-0 border bg-white">
            <thead>
            <tr>
                <th><?=$lang[951]?></th>
                <th><?=$lang[924]?></th>
                <th>1:1 <?=$lang[1460]?></th>
                <th><?=$lang[1464]?></th>
                <th><?=$lang[925]?></th>
                <th colspan="2">&nbsp;</th>
            </tr>
            </thead>

            <TR>
                <TD><INPUT TYPE="TEXT" SIZE="25" NAME="gtable" ONCHANGE="this.form.beschreibung.value=this.value" class="form-control form-control-sm"></TD>
                <TD><INPUT TYPE="TEXT" SIZE="25" NAME="beschreibung" class="form-control form-control-sm"></TD>
                <TD>
                    <select name="verkn" class="form-select form-select-sm">
                        <option value="0"></option>
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
                            ?>                        
                    </select>
                </TD>
                <td>
                    <select name="copy" id="new_copy"class="form-select form-select-sm">
                        <option></option>
                        <?php
                            if($result_gtab[$tab_group]["tabelle"]){
                                foreach($result_gtab[$tab_group]["tabelle"] as $bzm => $value){
                                    echo "<option value=\"".$result_gtab[$tab_group]["id"][$bzm]."\">".$value."</option>";
                                }
                            }
                        ?>
                    </select>
                </td>

                <TD>
                    <SELECT NAME="typ" OnChange="extendTabTyp(this)" class="form-select form-select-sm">
                        <option value="1"><?= $lang[164]; # table ?></option>
                        <option value="2"><?= $lang[1929]; # calendar ?></option>
                        <option value="7">Kanban</option>
                        <option value="6"><?= $lang[767]; # messages ?></option>
                        <option value="8"><?= $lang[428]; # report template ?></option>
                        <option value="5"><?= $lang[2023]; # view ?></option>
                    </SELECT>

                    <div>
                        <SELECT NAME="viewtyp" style="display:none" class="form-select form-select-sm">
                            <option VALUE="1"><?=$lang[2656]?></option>
                            <option VALUE="2"><?=$lang[2657]?></option>
                            <option VALUE="3"><?=$lang[2658]?></option>
                            <option VALUE="4"><?=$lang[2659]?></option>
                        </SELECT>
                    </div>
                </TD>

                <td><button type="button" class="btn btn-primary btn-sm" OnClick="add_tab()"><?=$lang[540]?></button></td>

                <TD>
                    <div class="form-check mb-1">
                        <input class="form-check-input" type="checkbox" value="1" id="add_permission" name="add_permission" checked>
                        <label class="form-check-label" for="add_permission">
                            <?=$lang[1263]?>
                        </label>
                    </div>
                    <?php  /* todo deprcated
                    <div class="form-check mb-1">
                        <input class="form-check-input" type="checkbox" value="1" id="use_serial" name="use_serial">
                        <label class="form-check-label" for="use_serial">
                            <?=$lang[2664]?>
                        </label>
                    </div>
                    */?>
                    <div class="form-check">
                        <?php if(LMB_DBFUNC_SEQUENCE){?>
                            <input class="form-check-input" type="checkbox" value="1" id="use_sequence" name="use_sequence" readonly disabled checked>
                        <?php }?>
                        <label class="form-check-label" for="use_sequence">
                            <?=$lang[2665]?>
                        </label>
                    </div>
                </TD>
            </TR>

        </table>

    </FORM>
</div>
<?php endif; ?>


