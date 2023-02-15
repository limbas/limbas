<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */




?>

<script>

    var argumenteditor = null;
$(function() {
    // sort table field rows
    $("#fieldtable").sortable({
        axis: "y",
        containment: "parent",
        cursor: "move",
        distance: 5,
        handle: ".tabSortableHandle",
        items: "tr[data-lmb-fieldid]", // exclude header and footer rows
        update: function(event, ui) {
            var sortedRow = ui.item.first();
            var sortedRowFieldID = sortedRow.attr("data-lmb-fieldid");
            var nextRowFieldID = sortedRow.next().attr("data-lmb-fieldid");

            if (sortedRowFieldID) {
                if (!nextRowFieldID) {
                    nextRowFieldID = 'last';
                }
                document.form1.move_to.value = nextRowFieldID;
                document.form1.fieldid.value = sortedRowFieldID;
                document.form1.submit();
            }
        }
    });
    
    $('#argument-change').click(saveArgument);
    $('#argument-refresh').click(refreshArgument);
});

/* --- delete field ----------------------------------- */
function delete_field(id,name){

    $('#fieldDeleteModal').modal('show');
    $('#fieldDeleteModalTitle').html('<?=$lang[2019]?> ('+name+')');

    buttonFieldDelete = document.getElementById("buttonFieldDelete");
    buttonFieldRemove = document.getElementById("buttonFieldRemove");

    buttonFieldDelete.replaceWith(buttonFieldDelete.cloneNode(true));
    buttonFieldRemove.replaceWith(buttonFieldRemove.cloneNode(true));

    buttonFieldDelete = document.getElementById("buttonFieldDelete");
    buttonFieldRemove = document.getElementById("buttonFieldRemove");

    buttonFieldDelete.addEventListener("click", function() {
        document.location.href='main_admin.php?&action=setup_gtab_ftype&tab_group=<?=$tab_group?>&del_tabelle=<?= urlencode($table_gtab[$bzm]) ?>&column='+name+'&column_id='+id+'&del=1&atid=<?=$atid?>&drop_physical=1';
    });

    buttonFieldRemove.addEventListener("click", function() {
        document.location.href='main_admin.php?&action=setup_gtab_ftype&tab_group=<?=$tab_group?>&del_tabelle=<?= urlencode($table_gtab[$bzm]) ?>&column='+name+'&column_id='+id+'&del=1&atid=<?=$atid?>';
    });

}

/* --- convert field ----------------------------------- */
function convert_field(convert,fieldid,name,size) {
	var message = '<?=$lang[2021]?>';
	if(convert == 33 || convert == 34){message += '\nTake care of your referential integrity!';}
	var desc = confirm("<?=$lang[2020]?> "+name+" ?\n"+message);
	
	if(desc){
		document.form1.fieldid.value = fieldid;
		document.form1.convert_value.value = convert;
		document.form1.convert_size.value = size;
		document.form1.submit();
	}
}

/* --- extend field ----------------------------------- */
function extend_field(extend,fieldid) {
	document.form1.fieldid.value = fieldid;
	document.form1.extend_value.value = extend+' ';
	document.form1.submit();
}

/* --- view rule ----------------------------------- */
function viewrule_field(val,fieldid) {
	document.form1.fieldid.value = fieldid;
	document.form1.view_rule.value = val+" ";
	document.form1.submit();
}

/* --- edit rule ----------------------------------- */
function editrule_field(val,fieldid) {
	document.form1.fieldid.value = fieldid;
	document.form1.edit_rule.value = val+" ";
	document.form1.submit();
}

// Ajax edit field
function ajaxEditField(fieldid,act){
	ajaxGet(null,"main_dyns_admin.php","editTableField&gtabid=<?=$atid?>&fieldid=" + fieldid + "&tab_group=<?=$tab_group?>&act=" + act,null,"ajaxEditFieldPost","form2");
}

function ajaxEditFieldPost(result){


    ajaxEvalScript(result);

    $('#fieldsettingsContent').html(result);
    $('#fieldsettingsModal').modal('show');
}


function change_memoindex(fieldid,el){
	if(!el.checked){
		var ok = confirm('<?=$lang[1718]?>');
		if(ok){
			document.form1.fieldid.value = fieldid;
			document.form1.memoindex.value=1;
			document.form1.submit();
		}else{el.checked = 1;}
	}else{
		document.form1.fieldid.value = fieldid;
		document.form1.memoindex.value=1;
		document.form1.submit();
	}
}

function change_wysiwyg(fieldid,el){
	document.form1.fieldid.value = fieldid;
	document.form1.wysiwyg.value=1;
	document.form1.submit();
}

function newwin(FIELDID,ATID,POOL,TYP) {
fieldselect = open("main_admin.php?action=setup_fieldselect&fieldid=" + FIELDID + "&atid=" + ATID + "&pool=" + POOL + "&field_pool=" + POOL + "&typ=" + TYP ,"Auswahlfelder","toolbar=0,location=0,status=0,menubar=0,scrollbars=1,resizable=0,width=750,height=500");
}

function newwin3(FIELDID,TABID,ATID,ARGTYP,ARGUMENT) {
    
    
    let data = {
        'actid': 'editFieldTypeArgument',
        'atid': ATID,
        'tabid': TABID,
        'tabgroup': <?= $tab_group ?>,
        'fieldid': FIELDID,
        'type': ARGTYP
    };
    
    if (ARGUMENT != null) {
        data['argument'] = ARGUMENT;
    }
    
    $.ajax({
        url: 'main_dyns_admin.php',
        type: 'GET',
        dataType: 'json',
        data: data,
        success: function(data) {

            let $saveButton = $('#argument-change');
            let $refreshButton = $('#argument-refresh');
            
            if (argumenteditor == null) {
                argumenteditor = CodeMirror.fromTextArea(document.getElementById('fieldargument'), {
                    lineNumbers: true,
                    matchBrackets: true,
                    indentWithTabs: true,
                    smartIndent: true,
                    viewportMargin: 50,
                    mode: 'text/x-php'
                });
                argumenteditor.setSize(null, 80);
                argumenteditor.on('change', function () {
                    $saveButton.removeClass('btn-outline-primary').addClass('btn-primary');
                });

            }

            if (ARGUMENT == null) {
                if (ARGTYP == 15) {
                    argumenteditor.setOption("mode", 'text/x-php');
                    $('.phpargumentonly').removeClass('d-none');
                    
                    let html = '<option></option>';
    
                    for (let fid in data.fields) {
                        if (data.fields.hasOwnProperty(fid)) {
                            html += '<option value="#*' + fid + '#">' + data.fields[fid] + '</option>';
                        }
                        
                    }
                    
                    
                    $('#phpargfields').html(html);
                    
                } else {
                    argumenteditor.setOption("mode", 'text/x-sql');
                    $('.phpargumentonly').addClass('d-none');
                }
    
                argumenteditor.setValue(data.argument);
                setTimeout(function() {
                    argumenteditor.refresh();
                },1);
                
                $('#model-argument-title').text(data.title);
                $('#argument-example').text(data.example);
                $('#argument-refresh-ok').addClass('d-none');
    
                $saveButton.data('fieldid',FIELDID);
                $saveButton.data('tabid',TABID);
                $saveButton.data('atid',ATID);
                $saveButton.data('argtype',ARGTYP);

                $refreshButton.data('fieldid',FIELDID);
                $refreshButton.data('tabid',TABID);
                $refreshButton.data('atid',ATID);
                $refreshButton.data('argtype',ARGTYP);

            
                $('#modal-argument').modal('show');
            }
            
            
            
            
        }
    });
    
}

    function insertArgumentText(val) {
        argumenteditor.replaceRange(val, argumenteditor.getCursor());
    }
    
    function saveArgument() {
        let $this = $(this);

        newwin3(
            $this.data('fieldid'),
        $this.data('tabid'),
        $this.data('atid'),
        $this.data('argtype'),
            argumenteditor.getValue()
        );
    
        $('#argument-change').addClass('btn-outline-primary').removeClass('btn-primary');
    }

    function refreshArgument() {

        let $saveButton = $('#argument-change');
        let $refreshButton = $('#argument-refresh');
    

        $refreshButton.hide();
        $saveButton.prop('disabled',true);
        $('#argument-refresh-ok').addClass('d-none');
        $('#argument-refresh-progress').removeClass('d-none');


        $.ajax({
            url: 'main_dyns_admin.php',
            type: 'GET',
            dataType: 'json',
            data: {
                'actid': 'refreshFieldTypeArgument',
                'atid': $refreshButton.data('atid'),
                'tabid': $refreshButton.data('tabid'),
                'tabgroup': <?= $tab_group ?>,
                'fieldid': $refreshButton.data('fieldid'),
                'type': $refreshButton.data('argtype')
            },
            success: function(data) {

                if (data.success) {
                    $('#argument-refresh-ok').removeClass('d-none');
                }

            }
        }).done(function() {
            $refreshButton.show();
            $saveButton.prop('disabled',false);
            $('#argument-refresh-progress').addClass('d-none');
        });
    }
    
function newwin4(FELD,TAB,ATID) {
verknfield = open("main_admin.php?action=setup_verknfield&tab_group=<?= $tab_group ?>&typ=gtab_ftype&tabid=" + ATID + "&tab=" + TAB + "&fieldid=" + FELD + "" ,"Verknuepfung","toolbar=0,location=0,status=0,menubar=0,scrollbars=1,resizable=0,width=420,height=300");
}
function newwin5(FELD,ATID,VERKNID) {
    $('#relation-settings-frame').attr('src', 'main_admin.php?action=setup_verkn_editor&tabid=' + ATID + '&fieldid=' + FELD + '&verkntabid=' + VERKNID);
    $('#modal-relation-settings').modal('show');
        
}

function newwin7(FIELDID,TABID) {
grouping_editor = open("main_admin.php?action=setup_grouping_editor&tabid=" + TABID + "&fieldid=" + FIELDID + "" ,"Grouping_Edito","toolbar=0,location=0,status=0,menubar=0,scrollbars=1,resizable=0,width=420,height=300");
}


function viewsysfield(){
document.getElementById("sys0").style.display="none";
document.getElementById("sys1").style.display="";
document.getElementById("sys2").style.display="";
document.getElementById("sys3").style.display="";
document.getElementById("sys4").style.display="";
document.getElementById("sys5").style.display="";
document.getElementById("sys6").style.display="";
document.getElementById("sys7").style.display="";
document.getElementById("sys8").style.display="";
}

function checkfiledtype(el,el2){
	// || value == "49"  versiondesc
	
	
	if(el){
		var value = el[el.selectedIndex].value;
		if(el[el.selectedIndex].id){var defaultsize=el[el.selectedIndex].id;}
	}
	if(el2){var value2 = el2[el2.selectedIndex].value;}



	if(!value2){
		if(value == "46"){
			document.getElementById("inherit_typ").style.display = "";
			document.getElementById("argument_typ").style.display = "none";
		}else if(value == "29" || value == "53" || value == "72"){
			document.getElementById("inherit_typ").style.display = "none";
			document.getElementById("argument_typ").style.display = "";
		}else{
			document.getElementById("inherit_typ").style.display = "none";
			document.getElementById("argument_typ").style.display = "none";
		}
	}else{
		value = value2;
	}
	
	document.getElementById("typ_size").style.visibility='hidden';
	
	if(defaultsize){
		document.getElementById("typ_size").value=defaultsize;
		document.getElementById("typ_size").style.visibility='visible';
	}

}


var aktive_inherit = 0;
function checkinherittype(value){
	if(aktive_inherit){document.getElementById("inherit_field_"+aktive_inherit).style.display = "none";}
	document.getElementById("inherit_field_"+value).style.display = "";
	aktive_inherit = value;
}

var activ_menu = null;
function divclose(){
	if(!activ_menu){
		hide_trigger();
	}
	activ_menu = 0;
}

function hide_trigger(){
	var ar = document.getElementsByTagName("span");
	for (var i = ar.length; i > 0;) {
		cc = ar[--i];
		if(cc.id.substring(0,13) == "field_trigger"){
			cc.style.display='none';
		}
	}
}


</SCRIPT>


<?php
/* --- Tabellen-Liste --------------------------------------------- */
$bzm = $atid;
if($table_gtab[$bzm]):

if($table_typ[$bzm] == 5){$isview = 1;}

    $argInFields = false;

/* --- Spaltenüberschriften --------------------------------------- */
?>


<div class="container-fluid p-3">
    <FORM ACTION="main_admin.php" METHOD="post" name="form1">
        <input type="hidden" name="action" value="setup_gtab_ftype">
        <input type="hidden" name="new_gtab" value="<?= $table_gtab[$bzm] ?>">
        <input type="hidden" name="new_conf_gtab" value="<?= $conf_gtab[$bzm] ?>">
        <input type="hidden" name="tab_group" value="<?= $tab_group ?>">
        <input type="hidden" name="tabelle">
        <input type="hidden" name="fieldid">
        <input type="hidden" name="spelling">
        <input type="hidden" name="desc">
        <input type="hidden" name="uniquefield">
        <input type="hidden" name="column">
        <input type="hidden" name="columnid">
        <input type="hidden" name="keyfield">
        <input type="hidden" name="mainfield">
        <input type="hidden" name="fieldindex">
        <input type="hidden" name="atid" value="<?= $bzm ?>">
        <input type="hidden" name="def">
        <input type="hidden" name="verk">
        <input type="hidden" name="artleiste">
        <input type="hidden" name="groupable">
        <input type="hidden" name="dynsearch">
        <input type="hidden" name="move_to">
        <input type="hidden" name="argument_edit">
        <input type="hidden" name="argument_search">
        <input type="hidden" name="convert_value">
        <input type="hidden" name="convert_size">
        <input type="hidden" name="extend_value">
        <input type="hidden" name="new_keyid">
        <input type="hidden" name="memoindex">
        <input type="hidden" name="nformat">
        <input type="hidden" name="ncurrency">
        <input type="hidden" name="wysiwyg">
        <input type="hidden" name="select_cut">
        <input type="hidden" name="trigger">
        <input type="hidden" name="quicksearch">
        <input type="hidden" name="fullsearch">
        <input type="hidden" name="view_rule">
        <input type="hidden" name="edit_rule">
        <input type="hidden" name="ajaxsave">
        <input type="hidden" name="collreplace">
        <input type="hidden" name="solve_dependency">
        
        
        <h3><?=$lang[164] . ': ' . $table_gtab[$bzm]." (".$beschreibung_gtab[$bzm].")"; ?>
            <?php if($isview): ?>
                <a href="main_admin.php?&action=setup_gtab_view&viewid=<?=$atid?>"><i class="lmb-icon lmb-organisation-edit cursor-pointer"></i></a>
            <?php endif; ?>
        </h3>

        <table id="fieldtable" class="table table-sm table-striped table-hover mb-0 border bg-white">
            
            
            <thead>
            <tr class="text-nowrap">
                <th></th>
                <th>ID</th>
                <th></th>
                <?php if(!$isview){echo "<th>$lang[160]</th>";}?>
                <th><?=$lang[922]?></th>
                <th style="min-width:200px"><?=$lang[923]?></th>
                <th style="min-width:200px"><?=$lang[924]?></th>
                <th><?=$lang[925]?></th>
                <th><?=$lang[210]?></th>
                <?php if(!$isview){?><th><?=$lang[928]?></th><?php }?>
                <th><?=$lang[27]?></th>
                <th><?=$lang[930]?></th>
                <th><?=$lang[1986]?></th>
                <th><?=$lang[2505]?></th>
                <?php if(!$isview){?><th><?=$lang[2570]?></th><?php }?>
                <?php if($gtrigger[$bzm] AND !$isview){?><th><?=$lang[2216]?></th><?php }?>
                <th><?=$lang[2235]?></th>
                <?php if(!$isview){?><th><?=$lang[1720]?></th><?php }?>
                <?php if(!$isview){?><th><?=$lang[927]?></th><?php }?>
                <?php if(!$isview){?><th><?=$lang[2639]?></th><?php }?>
                <?php if(!$isview){?><th><?=$lang[2640]?></th><?php }?>
                <th><?=$lang[932]?></th>
                <th><?=$lang[2507]?></th>
                <th><?=$lang[2922]?></th>
                <th><?=$lang[1459]?></th>
                <th><?=$lang[2672]?></th>
            </tr>
            </thead>

            <?php

            /* --- Ergebnisliste --------------------------------------- */
            if($result_fieldtype[$table_gtab[$bzm]]["field_id"]):
            
            foreach ($result_fieldtype[$table_gtab[$bzm]]["field_id"] as $bzm1 => $val):
                $style = '';
            if ($result_fieldtype[$table_gtab[$bzm]]["fieldtype"][$bzm1] == 100) {
                $style = 'class="table-sub-section"';
            }
            ?>

            <TR data-lmb-fieldid="<?= $val ?>" <?=$style?>>

                <td></td>

                <td><?=$result_fieldtype[$table_gtab[$bzm]]["field_id"][$bzm1]?></td>

                <td>
                    <i onclick="activ_menu=1;ajaxEditField('<?=$result_fieldtype[$table_gtab[$bzm]]["field_id"][$bzm1]?>')" class="lmb-icon lmb-cog-alt cursor-pointer"></i>
                </td>
                
                <?php if(!$isview): ?>
                <td>
                    <?php if(!((lmb_strtoupper($table_gtab[$bzm]) == "LDMS_FILES" AND $result_fieldtype[$table_gtab[$bzm]]["field_id"][$bzm1] <= 33)
                        or (lmb_strtoupper($table_gtab[$bzm]) == "LDMS_META" and $result_fieldtype[$table_gtab[$bzm]]["field_id"][$bzm1] <= 37)
                        or ($table_typ[$bzm] == 8 and ($result_fieldtype[$table_gtab[$bzm]]["field_id"][$bzm1] <= 3 or $result_fieldtype[$table_gtab[$bzm]]["field"][$bzm1] === 'FORTABLE')))): ?>
                        <i class="lmb-icon lmb-trash cursor-pointer" onclick="delete_field('<?=$result_fieldtype[$table_gtab[$bzm]]["field_id"][$bzm1]?>','<?=urlencode($result_fieldtype[$table_gtab[$bzm]]["field"][$bzm1])?>')"></i>
                    <?php endif; ?>
                </td>
                <?php endif; ?>
                
                
                <?php
                
                
                if($result_fieldtype[$table_gtab[$bzm]]["view_dependency"][$bzm1]){$color = 'text-info';}else{$color = '';}
                ?>

                
                <td class="tabSortableHandle">
					<span id="field<?=$result_fieldtype[$table_gtab[$bzm]]["field_id"][$bzm1]?>" class="<?=$color?>"><?= $result_fieldtype[$table_gtab[$bzm]]["field"][$bzm1] ?></span>&nbsp;
                </td>
                
                
                <td><input type="text"
                           size="25"
                           name="DESC_<?= $result_fieldtype[$table_gtab[$bzm]]["field_id"][$bzm1] ?>"
                           value="<?= $lang[$result_fieldtype[$table_gtab[$bzm]]["beschreibung_feld"][$bzm1]] ?>"
                           onchange="this.form.fieldid.value='<?= $result_fieldtype[$table_gtab[$bzm]]["field_id"][$bzm1] ?>';this.form.desc.value=this.form.DESC_<?= $result_fieldtype[$table_gtab[$bzm]]["field_id"][$bzm1] ?>.value;this.form.submit();"
                           class="form-control form-control-sm">
                </td>
                <td><input type="TEXT"
                           size="16"
                           name="SPELLING_<?= $result_fieldtype[$table_gtab[$bzm]]["field_id"][$bzm1] ?>"
                           value="<?= $lang[$result_fieldtype[$table_gtab[$bzm]]["spelling"][$bzm1]] ?>"
                           onchange="this.form.fieldid.value='<?= $result_fieldtype[$table_gtab[$bzm]]["field_id"][$bzm1] ?>';this.form.spelling.value=this.form.SPELLING_<?= $result_fieldtype[$table_gtab[$bzm]]["field_id"][$bzm1] ?>.value;this.form.submit();"
                           class="form-control form-control-sm">
                </td>
                
                <td class="text-nowrap">
                    <?php
                    # Typ
                    if($argument_typ = $result_fieldtype[$table_gtab[$bzm]]["argument_typ"][$bzm1]){
                        echo $lmfieldtype["name"][$argument_typ]."&nbsp;-&nbsp;";
                    }
                    echo $result_fieldtype[$table_gtab[$bzm]]["beschreibung_typ"][$bzm1];
                    echo "&nbsp;";
                    if($result_fieldtype[$table_gtab[$bzm]]["inherit_tab"][$bzm1]){
                        echo "<i>[$lang[2086]]</i>";
                    }

                    if($result_fieldtype[$table_gtab[$bzm]]["scale"][$bzm1]){
                        $fsize = $result_fieldtype[$table_gtab[$bzm]]["precision"][$bzm1].",".$result_fieldtype[$table_gtab[$bzm]]["scale"][$bzm1];
                    }else{
                        $fsize = $result_fieldtype[$table_gtab[$bzm]]["precision"][$bzm1];
                    }

                    if($result_fieldtype[$table_gtab[$bzm]]["type_name"][$bzm1]){
                        echo "<i>(".$result_fieldtype[$table_gtab[$bzm]]["type_name"][$bzm1]." ".$fsize.")</i>";
                    }
                    ?>
                </td>
                
                <td>
                    <?php if($lmfieldtype["hassize"][$result_fieldtype[$table_gtab[$bzm]]["datatype"][$bzm1]]): ?>

                        <input type="text" value="<?=$result_fieldtype[$table_gtab[$bzm]]["field_size"][$bzm1]?>" onchange="convert_field('<?=$result_fieldtype[$table_gtab[$bzm]]["datatype"][$bzm1]?>','<?=$result_fieldtype[$table_gtab[$bzm]]["field_id"][$bzm1]?>','<?=$result_fieldtype[$table_gtab[$bzm]]["field"][$bzm1]?>',this.value);" class="form-control form-control-sm">
                    
                    <?php endif; ?>
                </td>
                    
                
                
                    <?php if(!$isview): ?>
                    
                    <td class="text-nowrap">
                <?php
                        
                        # defaultvalue
                        if($result_fieldtype[$table_gtab[$bzm]]["fieldtype"][$bzm1] == 11 /* relation */){
                            $verknTabid = $result_fieldtype[$table_gtab[$bzm]]['verkntabid'][$bzm1];
                            $verknFieldid = $result_fieldtype[$table_gtab[$bzm]]['verknfieldid'][$bzm1];
                            $iconColor = '';
                            if (!$verknTabid or !$verknFieldid) {
                                $iconColor = 'color:red;';
                            }

                            if($verknTabid){
                                $sqlquery = "SELECT BESCHREIBUNG FROM LMB_CONF_TABLES WHERE TAB_ID = ".$verknTabid;
                                $rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
                                $verknTabgroup = $gtab['tab_group'][$verknTabid];
                                echo "<a onclick=\"document.location.href='main_admin.php?&action=setup_gtab_ftype&tab_group=$verknTabgroup&atid=$verknTabid'\">".$lang[lmbdb_result($rs, "BESCHREIBUNG")]."</a> | ";

                                if($verknFieldid){
                                    $sqlquery = "SELECT SPELLING FROM LMB_CONF_FIELDS WHERE TAB_ID = ".$verknTabid." AND FIELD_ID = ".$verknFieldid;
                                    $rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
                                    echo $lang[lmbdb_result($rs, "SPELLING")];
                                } else {
                                    echo '?';
                                }
                            } else {
                                echo '?';
                            }

                            if($LINK[163]){
                                echo "&nbsp;<i STYLE=\"cursor:pointer;$iconColor\" OnClick=\"newwin5('".$result_fieldtype[$table_gtab[$bzm]]["field_id"][$bzm1]."','".$KEYID_gtab[$bzm]."','".$result_fieldtype[$table_gtab[$bzm]]['verkntabid'][$bzm1]."')\" class=\"lmb-icon ".$LINK['icon_url'][163]."\" TITLE=\"".$lang[$LINK['desc'][163]]."\" BORDER=\"0\"></i>";
                            }

                        }else{
                            if(!$result_fieldtype[$table_gtab[$bzm]]["domain_admin_default"][$bzm1] AND $result_fieldtype[$table_gtab[$bzm]]["fieldtype"][$bzm1] != 100 AND $result_fieldtype[$table_gtab[$bzm]]["datatype"][$bzm1] != 22 AND $result_fieldtype[$table_gtab[$bzm]]["datatype"][$bzm1] != 32 AND $result_fieldtype[$table_gtab[$bzm]]["datatype"][$bzm1] != 31 AND $result_fieldtype[$table_gtab[$bzm]]["datatype"][$bzm1] != 18 AND $result_fieldtype[$table_gtab[$bzm]]["fieldtype"][$bzm1] != 6 AND $result_fieldtype[$table_gtab[$bzm]]["fieldtype"][$bzm1] != 8 AND $result_fieldtype[$table_gtab[$bzm]]["fieldtype"][$bzm1] != 10 AND $result_fieldtype[$table_gtab[$bzm]]["fieldtype"][$bzm1] != 9 AND $result_fieldtype[$table_gtab[$bzm]]["fieldtype"][$bzm1] != 11 AND $result_fieldtype[$table_gtab[$bzm]]["fieldtype"][$bzm1] != 12 AND $result_fieldtype[$table_gtab[$bzm]]["fieldtype"][$bzm1] != 13 AND $result_fieldtype[$table_gtab[$bzm]]["fieldtype"][$bzm1] != 19 AND $result_fieldtype[$table_gtab[$bzm]]["fieldtype"][$bzm1] != 18 AND $result_fieldtype[$table_gtab[$bzm]]["fieldtype"][$bzm1] != 14 AND $result_fieldtype[$table_gtab[$bzm]]["fieldtype"][$bzm1] != 15 AND $result_fieldtype[$table_gtab[$bzm]]["fieldtype"][$bzm1] != 16 AND $result_fieldtype[$table_gtab[$bzm]]["datatype"][$bzm1] != 44 AND $result_fieldtype[$table_gtab[$bzm]]["fieldtype"][$bzm1] < 100 AND !$result_fieldtype[$table_gtab[$bzm]]["argument_typ"][$bzm1]){
                                echo "<INPUT TYPE=\"TEXT\" class=\"form-control form-control-sm\" NAME=\"".$result_fieldtype[$table_gtab[$bzm]]["field"][$bzm1]."\" VALUE=\"".htmlentities($result_fieldtype[$table_gtab[$bzm]]["domain_default"][$bzm1],ENT_QUOTES,$umgvar["charset"])."\" OnChange=\"this.form.fieldid.value='".$result_fieldtype[$table_gtab[$bzm]]["field_id"][$bzm1]."';this.form.def.value=this.value+' '; this.form.column.value='".$result_fieldtype[$table_gtab[$bzm]]['field'][$bzm1]."'; this.form.submit();\">";
                            }elseif(!$result_fieldtype[$table_gtab[$bzm]]["domain_admin_default"][$bzm1] AND $result_fieldtype[$table_gtab[$bzm]]["fieldtype"][$bzm1] == 10)
                            {
                                $selected[$result_fieldtype[$table_gtab[$bzm]]["domain_default"][$bzm1]] = 'selected';

                                echo "<SELECT NAME=\"".$result_fieldtype[$table_gtab[$bzm]]['field'][$bzm1]."\" class=\"form-select form-select-sm\" onchange=\"this.form.fieldid.value='".$result_fieldtype[$table_gtab[$bzm]]["field_id"][$bzm1]."';this.form.def.value=this.value; this.form.column.value='".$result_fieldtype[$table_gtab[$bzm]]['field'][$bzm1]."'; this.form.submit();\">
                                <OPTION value='NULL'>
                                <OPTION value='TRUE' {$selected['TRUE']}>{$lang[1506]}
                                <OPTION value='FALSE' {$selected['FALSE']}>{$lang[1507]}
                                </SELECT>
                                ";
                            }
                            else{
                                echo $result_fieldtype[$table_gtab[$bzm]]["domain_admin_default"][$bzm1];
                            }
                        }
                        
                        ?>
                    </td>
                    
                    
                <?php
                    endif;

                    /* --- Argument --------------------------------------- */
                    if($result_fieldtype[$table_gtab[$bzm]]["argument_typ"][$bzm1]){
                        $argInFields=true;
                        
                        echo "<TD  ALIGN=\"RIGHT\" NOWRAP>";
                        if($result_fieldtype[$table_gtab[$bzm]]["argument_typ"][$bzm1] == 15){
                            if($result_fieldtype[$table_gtab[$bzm]]["argument_edit"][$bzm1] == 1){$argument_edit = "CHECKED";}else{$argument_edit = " ";}
                            echo $lang[1879]." <INPUT TYPE=\"CHECKBOX\" OnClick=\"this.form.fieldid.value='".$result_fieldtype[$table_gtab[$bzm]]["field_id"][$bzm1]."';this.form.argument_edit.value='$argument_edit';this.form.submit();\" $argument_edit>&nbsp;";
                        }
                        echo "<A HREF=\"JAVASCRIPT: newwin3('".$result_fieldtype[$table_gtab[$bzm]]["field_id"][$bzm1]."','".$KEYID_gtab[$bzm]."','$bzm','".$result_fieldtype[$table_gtab[$bzm]]["argument_typ"][$bzm1]."',null);\"><i class=\"lmb-icon lmb-pencil\" BORDER=\"0\" TITLE=\"".str_replace("\"","&quot;",$result_fieldtype[$table_gtab[$bzm]]["argument"][$bzm1])."\" ALT=\"".str_replace("\"","&quot;",$result_fieldtype[$table_gtab[$bzm]]["argument"][$bzm1])."\"></i></A></TD>";
                        /* --- Selectauswahl --------------------------------------- */
                    }
                    elseif($result_fieldtype[$table_gtab[$bzm]]["fieldtype"][$bzm1] == 16){
                        echo "<TD ALIGN=\"RIGHT\" NOWRAP>";
                        echo "<INPUT TYPE=\"TEXT\" class=\"form-control form-control-sm\" VALUE=\"".htmlentities($result_fieldtype[$table_gtab[$bzm]]["select_cut"][$bzm1],ENT_QUOTES,$umgvar["charset"])."\" OnChange=\"document.form1.select_cut.value=this.value;document.form1.fieldid.value='".$result_fieldtype[$table_gtab[$bzm]]["field_id"][$bzm1]."';document.form1.submit();\">";

                        /* --- Selectauswahl --------------------------------------- */
                    }
                    elseif($result_fieldtype[$table_gtab[$bzm]]["fieldtype"][$bzm1] == 4){

                        echo "<TD ALIGN=\"RIGHT\" NOWRAP>";
                        if($result_fieldtype[$table_gtab[$bzm]]["datatype"][$bzm1] == 32){
                            echo "<INPUT TYPE=\"TEXT\" class=\"form-control form-control-sm\" VALUE=\"".htmlentities($result_fieldtype[$table_gtab[$bzm]]["select_cut"][$bzm1],ENT_QUOTES,$umgvar["charset"])."\" OnChange=\"document.form1.select_cut.value=this.value;document.form1.fieldid.value='".$result_fieldtype[$table_gtab[$bzm]]["field_id"][$bzm1]."';document.form1.submit();\">";
                        }

                        if($result_fieldtype[$table_gtab[$bzm]]['select_pool'][$bzm1]){
                            $sqlquery = "SELECT NAME FROM LMB_SELECT_P WHERE ID = ".$result_fieldtype[$table_gtab[$bzm]]['select_pool'][$bzm1];
                            $rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
                            echo "&nbsp;&nbsp;".htmlentities(lmbdb_result($rs, "NAME"),ENT_QUOTES,$umgvar["charset"]);
                            $pool = $result_fieldtype[$table_gtab[$bzm]]['select_pool'][$bzm1];
                        }else{
                            $pool = 0;
                        }

                        echo "&nbsp;<A HREF=\"JAVASCRIPT: newwin('".$result_fieldtype[$table_gtab[$bzm]]["field_id"][$bzm1]."','$bzm','$pool','LMB_SELECT');\"><i class=\"lmb-icon lmb-pencil\" BORDER=\"0\"></i></A>";
                        echo "</TD>";

                        /* --- Attribut --------------------------------------- */
                    }
                    elseif($result_fieldtype[$table_gtab[$bzm]]["fieldtype"][$bzm1] == 19){
                        echo "<TD ALIGN=\"RIGHT\" NOWRAP>";
                        if($result_fieldtype[$table_gtab[$bzm]]["select_pool"][$bzm1]){
                            $sqlquery = "SELECT NAME FROM LMB_ATTRIBUTE_P WHERE ID = ".$result_fieldtype[$table_gtab[$bzm]]["select_pool"][$bzm1];
                            $rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
                            echo "&nbsp;&nbsp;".htmlentities(lmbdb_result($rs, "NAME"),ENT_QUOTES,$umgvar["charset"]);
                            $pool = $result_fieldtype[$table_gtab[$bzm]]["select_pool"][$bzm1];
                        }else{
                            $pool = 0;
                        }

                        echo "&nbsp;<A HREF=\"JAVASCRIPT: newwin('".$result_fieldtype[$table_gtab[$bzm]]["field_id"][$bzm1]."','$bzm','$pool','LMB_ATTRIBUTE');\"><i class=\"lmb-icon lmb-pencil\" BORDER=\"0\"></i></A>";
                        echo "</TD>";
                        /* --- Verknüpfung --------------------------------------- */
                    }
                    elseif($result_fieldtype[$table_gtab[$bzm]]["fieldtype"][$bzm1] == 11){
                        $verknTableName = $result_fieldtype[$table_gtab[$bzm]]["verkntab"][$bzm1];
                        if (lmb_strtoupper(lmb_substr($verknTableName, 0, 5)) === 'VERK_') {
                            if ($verknTableID = array_search(lmb_strtolower($verknTableName), $table_gtab)) {
                                $verknTableName = "<a onclick=\"document.location.href='main_admin.php?action=setup_gtab_ftype&atid={$verknTableID}';\" title=\"{$beschreibung_gtab[$verknTableID]}\">$verknTableName</a>";
                            }
                        }

                        echo "<TD  VALIGN=\"TOP\" ALIGN=\"RIGHT\" nowrap>".$verknTableName."&nbsp;";
                        if($result_fieldtype[$table_gtab[$bzm]]["verkntabletype"][$bzm1] == 3){echo "<i class=\"lmb-icon lmb-switch\"></i>";}
                        elseif($result_fieldtype[$table_gtab[$bzm]]["verkntabletype"][$bzm1] == 2){echo "<i class=\"lmb-icon lmb-long-arrow-left\"></i>";}
                        echo "</TD>";
                        /* --- Zeitstempel --------------------------------------- */
                    }
                    elseif($result_fieldtype[$table_gtab[$bzm]]["fieldtype"][$bzm1] == 2){
                        echo "<TD ALIGN=\"RIGHT\"><INPUT TYPE=\"TEXT\" class=\"form-control form-control-sm\" NAME=\"FORMAT_".$result_fieldtype[$table_gtab[$bzm]]["field_id"][$bzm1]."\" VALUE=\"".$result_fieldtype[$table_gtab[$bzm]]['format'][$bzm1]."\" OnChange=\"this.form.fieldid.value='".$result_fieldtype[$table_gtab[$bzm]]["field_id"][$bzm1]."';this.form.nformat.value=this.value+' ';this.form.submit();\">";
                        /* --- Zeit --------------------------------------- */
                    }
                    elseif($result_fieldtype[$table_gtab[$bzm]]["fieldtype"][$bzm1] == 7){
                        echo "<TD ALIGN=\"RIGHT\"><INPUT TYPE=\"TEXT\" class=\"form-control form-control-sm\" NAME=\"FORMAT_".$result_fieldtype[$table_gtab[$bzm]]["field_id"][$bzm1]."\" VALUE=\"".$result_fieldtype[$table_gtab[$bzm]]['format'][$bzm1]."\" OnChange=\"this.form.fieldid.value='".$result_fieldtype[$table_gtab[$bzm]]["field_id"][$bzm1]."';this.form.nformat.value=this.value+' ';this.form.submit();\"></TD>";
                        /* --- Long --------------------------------------- */
                    }
                    elseif($result_fieldtype[$table_gtab[$bzm]]["datatype"][$bzm1] == 39){
                        if($result_fieldtype[$table_gtab[$bzm]]['memoindex'][$bzm1] == 1){$memoindexvalue = "CHECKED";} else{$memoindexvalue = "";}
                        if($result_fieldtype[$table_gtab[$bzm]]['wysiwyg'][$bzm1] == 1){$wysiwygvalue = "CHECKED";} else{$wysiwygvalue = "";}
                        echo "<TD  ALIGN=\"RIGHT\" NOWRAP>".$lang[1581]." <INPUT TYPE=\"CHECKBOX\" NAME=\"MEMOINDEX_".$result_fieldtype[$table_gtab[$bzm]]["field_id"][$bzm1]."\" OnClick=\"change_memoindex('".$result_fieldtype[$table_gtab[$bzm]]["field_id"][$bzm1]."',this);\" ".$memoindexvalue.">";
                        echo "<BR>".$lang[1885]." <INPUT TYPE=\"CHECKBOX\" NAME=\"WYSIWYG_".$result_fieldtype[$table_gtab[$bzm]]["field_id"][$bzm1]."\" OnClick=\"change_wysiwyg('".$result_fieldtype[$table_gtab[$bzm]]["field_id"][$bzm1]."',this);\" ".$wysiwygvalue."></TD>";
                        /* --- NFORMAT --------------------------------------- */
                    }
                    elseif($result_fieldtype[$table_gtab[$bzm]]["fieldtype"][$bzm1] == 5 AND $result_fieldtype[$table_gtab[$bzm]]["datatype"][$bzm1] != 22 AND $result_fieldtype[$table_gtab[$bzm]]["datatype"][$bzm1] != 44){
                        echo "<TD  VALIGN=\"TOP\" ALIGN=\"RIGHT\"><INPUT TYPE=\"TEXT\" class=\"form-control form-control-sm\" NAME=\"FORMAT_".$result_fieldtype[$table_gtab[$bzm]]["field_id"][$bzm1]."\" VALUE=\"".$result_fieldtype[$table_gtab[$bzm]]['format'][$bzm1]."\" OnChange=\"this.form.fieldid.value='".$result_fieldtype[$table_gtab[$bzm]]["field_id"][$bzm1]."';this.form.nformat.value=this.value+' ';this.form.submit();\">";
                        /* --- Währung --------------------------------------- */
                        if($result_fieldtype[$table_gtab[$bzm]]["datatype"][$bzm1] == 30){
                            echo "<SELECT class=\"form-select form-select-sm\" ONCHANGE=\"this.form.fieldid.value='".$result_fieldtype[$table_gtab[$bzm]]["field_id"][$bzm1]."';this.form.ncurrency.value=this.value;this.form.submit();\"><OPTION VALUE=\" \">";
                            asort($lmcurrency['currency']);
                            foreach($lmcurrency['currency'] as $ckey => $cval){
                                if($lmcurrency['code'][$ckey] == $result_fieldtype[$table_gtab[$bzm]]['currency'][$bzm1]){$sel = "SELECTED";}
                                #elseif($lmcurrency['code'][$ckey] == "EUR" AND !$result_fieldtype[$table_gtab[$bzm]]['currency'][$bzm1]){$sel = "SELECTED";}
                                else{$sel = "";}
                                echo "<OPTION VALUE=\"".$lmcurrency['code'][$ckey]."\" $sel>".$lmcurrency['currency'][$ckey];
                            }
                            echo "</SELECT>";
                        }
                        echo "</TD>";
                        /* --- Grouping --------------------------------------- */
                    }
                    elseif($result_fieldtype[$table_gtab[$bzm]]["fieldtype"][$bzm1] == 101){
                        echo "<TD NOWRAP ALIGN=\"right\">";
                        if($result_fieldtype[$table_gtab[$bzm]]['genlink'][$bzm1]){echo "Link";}
                        echo "&nbsp;<A HREF=\"#\" onclick=\"newwin7('".$result_fieldtype[$table_gtab[$bzm]]["field_id"][$bzm1]."','$bzm');\"><i class=\"lmb-icon lmb-edit\" BORDER=\"0\"></i></A>";
                    }
                    else{
                        echo "<TD >&nbsp;</TD>";
                    }


                    /* --- Konvertieren --------------------------------------- */
                    # if(!$isview){
                    if($isview){
                        $lmfieldtype_allow_convert_ = array(1,5);
                        $lmfieldtype_deny_convert_ = array(22);
                        echo "<TD VALIGN=\"TOP\"><SELECT class=\"form-select form-select-sm\" OnChange=\"convert_field(this[this.selectedIndex].value,'".$result_fieldtype[$table_gtab[$bzm]]["field_id"][$bzm1]."','".$result_fieldtype[$table_gtab[$bzm]]['field'][$bzm1]."',0);\"><OPTION>";
                        foreach($lmfieldtype["id"] as $type_key => $type_id){
                            if(in_array($type_key,$lmfieldtype_allow_convert_) AND !in_array($lmfieldtype["data_type"][$type_key],$lmfieldtype_deny_convert_)){
                                echo "<OPTION VALUE=\"".$type_key."\">".$lmfieldtype['name'][$type_key];
                            }
                        }
                        echo "</SELECT></TD>";
                    }
                    elseif($result_fieldtype[$table_gtab[$bzm]]["fieldtype"][$bzm1] == 11){

                        // n:m
                        if($result_fieldtype[$table_gtab[$bzm]]["datatype"][$bzm1] == 24) {
                            $lmfieldtype_allow_convert_ = array(27);
                            // 1:n
                        }elseif($result_fieldtype[$table_gtab[$bzm]]["datatype"][$bzm1] == 27) {
                            $lmfieldtype_allow_convert_ = array(24,25);
                            // 1:n simple
                        }elseif($result_fieldtype[$table_gtab[$bzm]]["datatype"][$bzm1] == 25) {
                            $lmfieldtype_allow_convert_ = array(27);
                        }

                        echo "<TD VALIGN=\"TOP\"><SELECT class=\"form-select form-select-sm\" OnChange=\"convert_field(this[this.selectedIndex].value,'".$result_fieldtype[$table_gtab[$bzm]]["field_id"][$bzm1]."','".$result_fieldtype[$table_gtab[$bzm]]['field'][$bzm1]."',0);\"><OPTION>";
                        foreach($lmfieldtype["id"] as $type_key => $type_id){
                            if(in_array($type_key,$lmfieldtype_allow_convert_)){
                                echo "<OPTION VALUE=\"".$type_key."\">".$lmfieldtype["name"][$type_key];
                            }
                        }
                        echo "</SELECT></TD>";
                    }
                    elseif(($result_fieldtype[$table_gtab[$bzm]]["fieldtype"][$bzm1] == 1 OR $result_fieldtype[$table_gtab[$bzm]]["fieldtype"][$bzm1] == 5 OR $result_fieldtype[$table_gtab[$bzm]]["fieldtype"][$bzm1] == 4 OR $result_fieldtype[$table_gtab[$bzm]]["fieldtype"][$bzm1] == 3 OR $result_fieldtype[$table_gtab[$bzm]]["fieldtype"][$bzm1] == 18 OR $result_fieldtype[$table_gtab[$bzm]]["fieldtype"][$bzm1] == 21) AND $result_fieldtype[$table_gtab[$bzm]]["fieldtype"][$bzm1] != 100 AND $result_fieldtype[$table_gtab[$bzm]]["datatype"][$bzm1] != 22 AND !$result_fieldtype[$table_gtab[$bzm]]["argument_typ"][$bzm1]){
                        # multiselect convert
                        if($result_fieldtype[$table_gtab[$bzm]]["datatype"][$bzm1] == 18 OR $result_fieldtype[$table_gtab[$bzm]]["datatype"][$bzm1] == 31 OR $result_fieldtype[$table_gtab[$bzm]]["datatype"][$bzm1] == 32){
                            $lmfieldtype_allow_convert_ = array(18,31,32);
                        }else{
                            $lmfieldtype_allow_convert_ = array(16,17,33,19,21,1,2,3,4,5,6,7,8,9,10,29,28,12,14,31,18,30,32,39,42,44,45,50);
                        }
                        echo "<TD VALIGN=\"TOP\"><SELECT class=\"form-select form-select-sm\" OnChange=\"convert_field(this[this.selectedIndex].value,'".$result_fieldtype[$table_gtab[$bzm]]["field_id"][$bzm1]."','".$result_fieldtype[$table_gtab[$bzm]]['field'][$bzm1]."',0);\"><OPTION>";
                        foreach($lmfieldtype["id"] as $type_key => $type_id){
                            if(in_array($type_key,$lmfieldtype_allow_convert_)){
                                echo "<OPTION VALUE=\"".$type_key."\">".$lmfieldtype["name"][$type_key];
                            }
                        }
                        echo "</SELECT></TD>";
                    }
                    elseif($result_fieldtype[$table_gtab[$bzm]]["fieldtype"][$bzm1] == 101){
                        echo "<TD VALIGN=\"TOP\"><SELECT class=\"form-select form-select-sm\" OnChange=\"convert_field(this[this.selectedIndex].value,'".$result_fieldtype[$table_gtab[$bzm]]["field_id"][$bzm1]."','".$result_fieldtype[$table_gtab[$bzm]]['field'][$bzm1]."',0);\"><OPTION>";
                        $lmfieldtype_allow_convert_ = array(101,102);
                        foreach($lmfieldtype["id"] as $type_key => $type_id){
                            if(in_array($type_key,$lmfieldtype_allow_convert_)){
                                echo "<OPTION VALUE=\"".$type_key."\">".$lmfieldtype['name'][$type_key];
                            }
                        }
                        echo "</SELECT></TD>";
                    }
                    elseif($result_fieldtype[$table_gtab[$bzm]]["datatype"][$bzm1] != 30){echo "<TD >&nbsp;</TD>";}
                    #}
                    
                    ?>

                
                <td>
                    <?php
                    //--- Extension ------
                    if(!$result_fieldtype[$table_gtab[$bzm]]["domain_admin_default"][$bzm1] AND $ext_fk AND $result_fieldtype[$table_gtab[$bzm]]["fieldtype"][$bzm1] < 100 AND $result_fieldtype[$table_gtab[$bzm]]["datatype"][$bzm1] != 22 AND $result_fieldtype[$table_gtab[$bzm]]["fieldtype"][$bzm1] != 14 AND $result_fieldtype[$table_gtab[$bzm]]["fieldtype"][$bzm1] != 15 AND $result_fieldtype[$table_gtab[$bzm]]["fieldtype"][$bzm1] != 16):
                    ?>

                    <select class="form-select form-select-sm" onchange="extend_field(this[this.selectedIndex].value,'<?=$result_fieldtype[$table_gtab[$bzm]]["field_id"][$bzm1]?>','<?=$result_fieldtype[$table_gtab[$bzm]]['field'][$bzm1]?>');">
                        <option></option>
                        <?php foreach ($ext_fk as $key => $value): ?>
                            <option value="<?=$value?>" <?=($result_fieldtype[$table_gtab[$bzm]]["ext_type"][$bzm1] == $value)?'selected':''?>><?=$value?></option>
                        <?php endforeach; ?>
                    </select>
                    
                    <?php endif; ?>
                </td>
                
                <td>
                    <?php // --- View-Rule ------ ?>
                    <input class="form-control form-control-sm" onchange="viewrule_field(this.value,'<?=$result_fieldtype[$table_gtab[$bzm]]["field_id"][$bzm1]?>');" value="<?=$result_fieldtype[$table_gtab[$bzm]]["view_rule"][$bzm1]?>">
                </td>

                <?php if(!$isview): ?>
                <td>
                    <?php // --- Edit-Rule ------  
                    if($result_fieldtype[$table_gtab[$bzm]]["fieldtype"][$bzm1] < 100 AND $result_fieldtype[$table_gtab[$bzm]]["argument_typ"][$bzm1] != 47 AND $result_fieldtype[$table_gtab[$bzm]]["fieldtype"][$bzm1] != 14 AND $result_fieldtype[$table_gtab[$bzm]]["fieldtype"][$bzm1] != 15 AND $result_fieldtype[$table_gtab[$bzm]]["fieldtype"][$bzm1] != 20 AND $result_fieldtype[$table_gtab[$bzm]]["datatype"][$bzm1] != 47): ?>
                        <input class="form-control form-control-sm" onchange="editrule_field(this.value,'<?=$result_fieldtype[$table_gtab[$bzm]]["field_id"][$bzm1]?>');" value="<?=$result_fieldtype[$table_gtab[$bzm]]["edit_rule"][$bzm1]?>">
                    <?php endif; ?>
                </td>
                <?php endif; ?>

                <?php if(!$isview && $gtrigger[$bzm]): ?>
                    <td>
                        <?php // --- Trigger ------  
                        if($LINK[226] AND $result_fieldtype[$table_gtab[$bzm]]["fieldtype"][$bzm1] < 100 AND $result_fieldtype[$table_gtab[$bzm]]["datatype"][$bzm1] != 22):
                            $fid = $result_fieldtype[$table_gtab[$bzm]]["field_id"][$bzm1];
                            ?>
                            <select class="form-select form-select-sm" NAME="field_trigger_<?=$fid?>[]" onchange="document.form1.trigger.value='<?=$fid?>';if(document.form1.trigger.value=='<?=$fid?>'){document.form1.submit();}">
                                <option value=""></option>
                            <?php
                            
                            $trlist = array();
                            foreach($gtrigger[$bzm]["id"] as $trid => $trval):
                                if(in_array($trid,$result_fieldtype[$table_gtab[$bzm]]["trigger"][$bzm1])){$SELECTED = "SELECTED";$trlist[] = $gtrigger[$bzm]["trigger_name"][$trid];}else{$SELECTED = "";} ?>
                                <option VALUE="<?=$trid?>" <?=$SELECTED?>><?=$gtrigger[$bzm]["trigger_name"][$trid]?> (<?=$gtrigger[$bzm]["type"][$trid]?>)</option>
                                <?php endforeach; ?>

                            </select>
                        
                        <?php endif; ?>
                    </td>
                <?php endif; ?>


                <td>
                    <?php // --- Bezeichner ------  
                    if(!$result_fieldtype[$table_gtab[$bzm]]["domain_admin_default"][$bzm1] AND $result_fieldtype[$table_gtab[$bzm]]["fieldtype"][$bzm1] != 100 AND $result_fieldtype[$table_gtab[$bzm]]["datatype"][$bzm1] != 31 AND $result_fieldtype[$table_gtab[$bzm]]["datatype"][$bzm1] != 18 AND $result_fieldtype[$table_gtab[$bzm]]["fieldtype"][$bzm1] != 6 AND $result_fieldtype[$table_gtab[$bzm]]["fieldtype"][$bzm1] != 10 AND $result_fieldtype[$table_gtab[$bzm]]["fieldtype"][$bzm1] != 9 AND $result_fieldtype[$table_gtab[$bzm]]["fieldtype"][$bzm1] != 13 AND $result_fieldtype[$table_gtab[$bzm]]["fieldtype"][$bzm1] != 16):

                        if($result_fieldtype[$table_gtab[$bzm]]['mainfield'][$bzm1] == 1){$mainfieldvalue = "CHECKED";} else{$mainfieldvalue = "";}
                        ?>
                        <INPUT TYPE="CHECKBOX" NAME="MAINFIELD_<?=$result_fieldtype[$table_gtab[$bzm]]["field_id"][$bzm1]?>" OnClick="this.form.fieldid.value='<?=$result_fieldtype[$table_gtab[$bzm]]["field_id"][$bzm1]?>'; this.form.mainfield.value='this.form.MAINFIELD_<?=$result_fieldtype[$table_gtab[$bzm]]["field_id"][$bzm1]?>.value'; this.form.submit();" <?=$mainfieldvalue?>>
                    <?php endif; ?>
                </td>


                <?php if(!$isview): ?>
                    <td>
                        <?php // --- Index ------
                        $disabled = '';
                        if(!$result_fieldtype[$table_gtab[$bzm]]["domain_admin_default"][$bzm1] AND $result_fieldtype[$table_gtab[$bzm]]["fieldtype"][$bzm1] != 100 AND $result_fieldtype[$table_gtab[$bzm]]["fieldtype"][$bzm1] != 14 AND $result_fieldtype[$table_gtab[$bzm]]["fieldtype"][$bzm1] != 15 AND $result_fieldtype[$table_gtab[$bzm]]["fieldtype"][$bzm1] != 11 AND $result_fieldtype[$table_gtab[$bzm]]["fieldtype"][$bzm1] != 6 AND $result_fieldtype[$table_gtab[$bzm]]["datatype"][$bzm1] != 39 AND $result_fieldtype[$table_gtab[$bzm]]["datatype"][$bzm1] != 22 AND $result_fieldtype[$table_gtab[$bzm]]["fieldtype"][$bzm1] != 16):
                            if($result_fieldtype[$table_gtab[$bzm]]['indexed'][$bzm1] == 1){$indexvalue = "CHECKED";} else{
                                if($result_fieldtype[$table_gtab[$bzm]]["unique"][$bzm1] == 1){$disabled = "disabled";}
                                $indexvalue = "";
                            }

                            ?>

                            <INPUT TYPE="CHECKBOX" NAME="FIELDINDEX_<?=$result_fieldtype[$table_gtab[$bzm]]["field_id"][$bzm1]?>" <?=$disabled?> <?=$indexvalue?> onclick="this.form.fieldid.value='<?=$result_fieldtype[$table_gtab[$bzm]]["field_id"][$bzm1]?>';this.form.fieldindex.value='fieldindex_<?=$indexvalue?>';this.form.column.value='<?=$result_fieldtype[$table_gtab[$bzm]]['field'][$bzm1]?>';this.form.submit();">
                        
                        <?php endif; ?>
                    </td>
                <?php endif; ?>


                <?php if(!$isview): ?>
                    <td>
                        <?php // --- unique ------  
                        $disabled = '';
                        if(!$result_fieldtype[$table_gtab[$bzm]]["domain_admin_default"][$bzm1] AND $result_fieldtype[$table_gtab[$bzm]]["fieldtype"][$bzm1] != 14 AND $result_fieldtype[$table_gtab[$bzm]]["fieldtype"][$bzm1] != 15 AND $result_fieldtype[$table_gtab[$bzm]]["fieldtype"][$bzm1] != 100 AND $result_fieldtype[$table_gtab[$bzm]]["datatype"][$bzm1] != 12 AND $result_fieldtype[$table_gtab[$bzm]]["datatype"][$bzm1] != 14 AND $result_fieldtype[$table_gtab[$bzm]]["datatype"][$bzm1] != 18 AND $result_fieldtype[$table_gtab[$bzm]]["fieldtype"][$bzm1] != 10 AND $result_fieldtype[$table_gtab[$bzm]]["fieldtype"][$bzm1] != 9 AND $result_fieldtype[$table_gtab[$bzm]]["fieldtype"][$bzm1] != 13 AND $result_fieldtype[$table_gtab[$bzm]]["fieldtype"][$bzm1] != 3 AND $result_fieldtype[$table_gtab[$bzm]]["fieldtype"][$bzm1] != 16 AND $result_fieldtype[$table_gtab[$bzm]]["fieldtype"][$bzm1] != 19
                            AND !($result_fieldtype[$table_gtab[$bzm]]["datatype"][$bzm1] == 25 AND $result_fieldtype[$table_gtab[$bzm]]["verkntabletype"][$bzm1] == 2)):
                            if($result_fieldtype[$table_gtab[$bzm]]["unique"][$bzm1] == 1){$unique = "CHECKED";}else{
                                if($result_fieldtype[$table_gtab[$bzm]]['indexed'][$bzm1] == 1){$disabled = 'disabled';}
                                $unique = "";
                            }

                            ?>

                            <INPUT TYPE="CHECKBOX" <?=$unique?> NAME="UNIQUE_<?=$result_fieldtype[$table_gtab[$bzm]]["field_id"][$bzm1]?>" <?=$disabled?> onclick="this.form.fieldid.value='<?=$result_fieldtype[$table_gtab[$bzm]]["field_id"][$bzm1]?>'; this.form.uniquefield.value='uniquefield_<?=$unique?>'; this.form.submit();">

                        <?php endif; ?>
                    </td>
                <?php endif; ?>


                <?php if(!$isview): ?>
                    <td>
                        <?php // --- dynamic search ------  
                        if($result_fieldtype[$table_gtab[$bzm]]["fieldtype"][$bzm1] == 11 OR $result_fieldtype[$table_gtab[$bzm]]["datatype"][$bzm1] == 12 OR $result_fieldtype[$table_gtab[$bzm]]["datatype"][$bzm1] == 32 AND $result_fieldtype[$table_gtab[$bzm]]["fieldtype"][$bzm1] != 16):
                            if($result_fieldtype[$table_gtab[$bzm]]["dynsearch"][$bzm1] == 1){$dynsearch = "CHECKED";}else{$dynsearch = "";}
                            ?>

                            <INPUT TYPE="CHECKBOX" <?=$dynsearch?> NAME="DYNSEARCH_<?=$result_fieldtype[$table_gtab[$bzm]]["field_id"][$bzm1]?>" OnCLICK="this.form.fieldid.value='<?=$result_fieldtype[$table_gtab[$bzm]]["field_id"][$bzm1]?>';this.form.dynsearch.value='dynsearch_<?=$dynsearch?>'; this.form.submit();">

                        <?php endif; ?>
                    </td>
                <?php endif; ?>


                <?php if(!$isview): ?>
                    <td>
                        <?php // --- dynamic post ------  
                        if($result_fieldtype[$table_gtab[$bzm]]["fieldtype"][$bzm1] <= 100 AND $result_fieldtype[$table_gtab[$bzm]]["fieldtype"][$bzm1] != 20 AND $result_fieldtype[$table_gtab[$bzm]]["fieldtype"][$bzm1] != 14 AND $result_fieldtype[$table_gtab[$bzm]]["fieldtype"][$bzm1] != 15 AND $result_fieldtype[$table_gtab[$bzm]]["fieldtype"][$bzm1] != 9 AND $result_fieldtype[$table_gtab[$bzm]]["fieldtype"][$bzm1] != 8 AND $result_fieldtype[$table_gtab[$bzm]]["fieldtype"][$bzm1] != 6 AND $result_fieldtype[$table_gtab[$bzm]]["fieldtype"][$bzm1] != 19 AND $result_fieldtype[$table_gtab[$bzm]]["fieldtype"][$bzm1] != 16):
                            if($result_fieldtype[$table_gtab[$bzm]]["ajaxsave"][$bzm1] == 1){$ajaxsave = "CHECKED";}else{$ajaxsave = "";}
                            ?>

                            <INPUT TYPE="CHECKBOX" <?=$ajaxsave?> NAME="AJAXSAVE_<?=$result_fieldtype[$table_gtab[$bzm]]["field_id"][$bzm1]?>" onclick="this.form.fieldid.value='<?=$result_fieldtype[$table_gtab[$bzm]]["field_id"][$bzm1]?>';this.form.ajaxsave.value='ajaxsave_<?=$ajaxsave?>'; this.form.submit();">

                        <?php endif; ?>
                    </td>
                <?php endif; ?>

                <td>
                    <?php // --- Select ------  
                    if($result_fieldtype[$table_gtab[$bzm]]["fieldtype"][$bzm1] != 100 AND $result_fieldtype[$table_gtab[$bzm]]["fieldtype"][$bzm1] != 16):
                        if($result_fieldtype[$table_gtab[$bzm]]["artleiste"][$bzm1] == 1){$artleistevalue = "CHECKED";}else{$artleistevalue = "";}
                        ?>

                        <INPUT TYPE="CHECKBOX" <?=$artleistevalue?> NAME="ARTLEISTE_<?=$result_fieldtype[$table_gtab[$bzm]]["field_id"][$bzm1]?>" onclick="this.form.fieldid.value='<?=$result_fieldtype[$table_gtab[$bzm]]["field_id"][$bzm1]?>'; this.form.artleiste.value='artleiste_<?=$artleistevalue?>'; this.form.submit();">

                    <?php endif; ?>
                </td>

                <td>
                    <?php // --- quicksearch ------  
                    if($result_fieldtype[$table_gtab[$bzm]]["fieldtype"][$bzm1] != 100 AND $result_fieldtype[$table_gtab[$bzm]]["fieldtype"][$bzm1] != 16):
                        if($result_fieldtype[$table_gtab[$bzm]]["quicksearch"][$bzm1] == 1){$quicksearchvalue = "CHECKED";}else{$quicksearchvalue = "";}
                        ?>

                        <INPUT TYPE="CHECKBOX" <?=$quicksearchvalue?> NAME="QUICKSEARCH_<?=$result_fieldtype[$table_gtab[$bzm]]["field_id"][$bzm1]?>" onclick="this.form.fieldid.value='<?=$result_fieldtype[$table_gtab[$bzm]]["field_id"][$bzm1]?>'; this.form.quicksearch.value='quicksearch_<?=$quicksearchvalue?>'; this.form.submit();">

                    <?php endif; ?>
                </td>

                <td>
                    <?php // --- fullsearch ------  
                    if($result_fieldtype[$table_gtab[$bzm]]["fieldtype"][$bzm1] < 100 AND $result_fieldtype[$table_gtab[$bzm]]["fieldtype"][$bzm1] != 10 AND $result_fieldtype[$table_gtab[$bzm]]["datatype"][$bzm1] != 33 AND $result_fieldtype[$table_gtab[$bzm]]["fieldtype"][$bzm1] != 19 AND $result_fieldtype[$table_gtab[$bzm]]["fieldtype"][1] != 6 AND $result_fieldtype[$table_gtab[$bzm]]["fieldtype"][$bzm1] != 20):
                        if($result_fieldtype[$table_gtab[$bzm]]["fullsearch"][$bzm1] == 1){$fullsearchvalue = "CHECKED";}else{$fullsearchvalue = "";}
                        ?>

                        <INPUT TYPE="CHECKBOX" <?=$fullsearchvalue?> NAME="QUICKSEARCH_<?=$result_fieldtype[$table_gtab[$bzm]]["field_id"][$bzm1]?>" onclick="this.form.fieldid.value='<?=$result_fieldtype[$table_gtab[$bzm]]["field_id"][$bzm1]?>'; this.form.fullsearch.value='fullsearch_<?=$fullsearchvalue?>'; this.form.submit();">

                    <?php endif; ?>
                </td>

                <td>
                    <?php // --- Gruppierbar ------  
                    if(($result_fieldtype[$table_gtab[$bzm]]["fieldtype"][$bzm1] != 100) && !($result_fieldtype[$table_gtab[$bzm]]["fieldtype"][$bzm1] == 11 OR $result_fieldtype[$table_gtab[$bzm]]["fieldtype"][$bzm1] == 3 OR $result_fieldtype[$table_gtab[$bzm]]["datatype"][$bzm1] == 22 OR $result_fieldtype[$table_gtab[$bzm]]["fieldtype"][$bzm1] == 13 OR $result_fieldtype[$table_gtab[$bzm]]["datatype"][$bzm1] == 31 OR $result_fieldtype[$table_gtab[$bzm]]["datatype"][$bzm1] == 32 OR $result_fieldtype[$table_gtab[$bzm]]["datatype"][$bzm1] == 18  OR $result_fieldtype[$table_gtab[$bzm]]["datatype"][$bzm1] == 13 OR $result_fieldtype[$table_gtab[$bzm]]["fieldtype"][$bzm1] == 16)):
                        if($result_fieldtype[$table_gtab[$bzm]]["groupable"][$bzm1] == 1){$groupablevalue = "CHECKED";}else{$groupablevalue = "";}
                        ?>

                        <INPUT TYPE="CHECKBOX" <?=$groupablevalue?> NAME="GROUPABLE_<?=$result_fieldtype[$table_gtab[$bzm]]["field_id"][$bzm1]?>" onclick="this.form.fieldid.value='<?=$result_fieldtype[$table_gtab[$bzm]]["field_id"][$bzm1]?>'; this.form.groupable.value='groupable_<?=$groupablevalue?>'; this.form.submit();">

                    <?php endif; ?>
                </td>

                <td>
                    <?php // --- coll_replace ------  
                    if(($result_fieldtype[$table_gtab[$bzm]]["fieldtype"][$bzm1] != 100) && !($result_fieldtype[$table_gtab[$bzm]]["datatype"][$bzm1] != 22 AND !$result_fieldtype[$table_gtab[$bzm]]["argument"][$bzm1] AND ($result_fieldtype[$table_gtab[$bzm]]["fieldtype"][$bzm1] == 4 OR $result_fieldtype[$table_gtab[$bzm]]["fieldtype"][$bzm1] == 5 OR $result_fieldtype[$table_gtab[$bzm]]["fieldtype"][$bzm1] == 1 OR $result_fieldtype[$table_gtab[$bzm]]["fieldtype"][$bzm1] == 2 OR $result_fieldtype[$table_gtab[$bzm]]["fieldtype"][$bzm1] == 10 OR $result_fieldtype[$table_gtab[$bzm]]["fieldtype"][$bzm1] == 21 OR $result_fieldtype[$table_gtab[$bzm]]["fieldtype"][$bzm1] == 18))):
                        if($result_fieldtype[$table_gtab[$bzm]]["collreplace"][$bzm1] == 1){$collreplacevalue = "CHECKED";}else{$collreplacevalue = "";}
                        ?>

                        <INPUT TYPE="CHECKBOX" <?=$collreplacevalue?> NAME="COLLREPLACE_<?=$result_fieldtype[$table_gtab[$bzm]]["field_id"][$bzm1]?>" onclick="this.form.fieldid.value='<?=$result_fieldtype[$table_gtab[$bzm]]["field_id"][$bzm1]?>'; this.form.collreplace.value='collreplace_<?=$collreplacevalue?>'; this.form.submit();">

                    <?php endif; ?>
                </td>
            </TR>
                
                
                <?php

                    endforeach;
            
            endif;

            ?>

            <?php if(!$isview): ?>

                <tfoot>


                <TR>
                    <td colspan="4"></td>
                    <td><INPUT class="form-control form-control-sm" type="TEXT" SIZE="16" name="field_name"
                                            onchange="this.form.spellingf.value=this.form.field_name.value; this.form.beschreibung.value=this.form.field_name.value;"></td>
                    <td><INPUT class="form-control form-control-sm" type="TEXT" SIZE="25" name="beschreibung"></td>
                    <td><INPUT class="form-control form-control-sm" type="TEXT" SIZE="16" name="spellingf"></td>
                    <td><SELECT class="form-select form-select-sm" name="typ" OnChange="checkfiledtype(this,0)"><option></option>
                            <?php


                            /* --- Vernüpfungsparameter-Tabelle -------- */
                            $sqlquery =  "SELECT VERKNPARAMS FROM LMB_CONF_FIELDS WHERE UPPER(MD5TAB) = '".lmb_strtoupper($table_gtab[$bzm])."' AND VERKNPARAMS > 0";
                            $rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
                            if(lmbdb_result($rs, "VERKNPARAMS")){$verknparams = array(1,2,3,4,5,7,8,10,14,15,18,21,24);}
                            #$headerIds = array(1, 18, 48);

                            # Feldtypen
                            foreach ($lmfieldtype["id"] as $key => $value){

                                // todo field type upload deprecated
                                if($lmfieldtype["field_type"][$key] == 6){continue;}

                                if($lmfieldtype["categorie"][$key] != $categorie) {
                                    $categorie = $lmfieldtype["categorie"][$key];
                                    if ($key != $headerIds[0]) { echo "</OPTGROUP>"; }
                                    echo "<OPTGROUP label=\"" . $categorie . "\">";
                                }

                                if(!$table_validity[$atid] AND $lmfieldtype["field_type"][$key] == 24){continue;}
                                if(!$table_multitenant[$atid] AND $lmfieldtype["field_type"][$key] == 23){continue;}
                                if(!$table_datasync[$atid] AND $lmfieldtype["field_type"][$key] == 22){continue;}
                                if($lmfieldtype["id"][$key] == 49 AND !$tab_versioning[$atid]){continue;}
                                if($verknparams AND !in_array($lmfieldtype["field_type"][$key],$verknparams)){continue;}
                                if($lmfieldtype["field_type"][$key] == 20 /* file content */ AND $gtab['argresult_id']['LDMS_FILES'] != $bzm){continue;}
                                if($lmfieldtype["hassize"][$key]){$hs = "ID=\"".$lmfieldtype["size"][$key]."\"";}else{$hs = "";}
                                echo "<OPTION VALUE=\"".$lmfieldtype["id"][$key]."\" $hs>".$lmfieldtype["name"][$key]."</OPTION>";
                            }
                            echo "</OPTGROUP>";
                            ?>
                        </SELECT>

                        <div id="argument_typ" style="display: none">
                            <SELECT name="typ2" class="form-select form-select-sm" OnChange="checkfiledtype(0,this)">
                                <?php
                                foreach ($lmfieldtype["id"] as $key => $value) {
                                    $allowed_fieldtype = array(5,10,1,3,2,23);
                                    if(in_array($lmfieldtype["field_type"][$key],$allowed_fieldtype) AND $lmfieldtype["data_type"][$key] != 22 AND $lmfieldtype["data_type"][$key] != 44 AND $lmfieldtype["data_type"][$key] != 33 AND $lmfieldtype["data_type"][$key] != 55) {
                                        if($lmfieldtype["field_type"][$key] == 23 AND !$table_multitenant[$table_id]){continue;}
                                        echo "<OPTION VALUE=\"" . $lmfieldtype["id"][$key] . "\">" . $lmfieldtype["name"][$key];
                                    }
                                }
                                ?>
                            </SELECT>
                        </div>


                        <div id="inherit_typ" style="display: none">
                            <SELECT name="inherit_tab" class="form-select form-select-sm" OnChange="checkinherittype(this[this.selectedIndex].value)">
                                <OPTION>
                                    <?php
                                    # tables grouped by tabgroup
                                    foreach ($tabgroup['id'] as $groupKey => $groupID) {
                                        # collect tables of that tabgroup
                                        $tabgroupOptions = '';
                                        foreach ($gtab['tab_id'] as $tabKey => $tabID) {
                                            if($gtab['tab_group'][$tabKey] == $groupID and $tabID != $atid){
                                                $tabgroupOptions .= "<OPTION VALUE=\"{$tabID}\">{$gtab['desc'][$tabKey]}</option>";
                                            }
                                        }
                                        # only show tabgroup if tables are available
                                        if ($tabgroupOptions) {
                                            echo "<optgroup label=\"{$tabgroup['name'][$groupKey]}\">{$tabgroupOptions}</optgroup>";
                                        }
                                    }
                                    ?>
                            </SELECT>
                        </div>


                        <?php
                        foreach ($gtab['tab_id'] as $key => $value){
                            if($key != $atid){
                                echo "<div id=\"inherit_field_$key\" style=\"display:none\">";
                                echo "<select NAME=\"inherit_field[$key]\" class=\"form-select form-select-sm\"><option>";
                                if($gfield[$key]['sort']){
                                    foreach ($gfield[$key]['sort'] as $key1 => $value1){
                                        # group by sparte
                                        if ($gfield[$key]["field_type"][$key1] == 100 /* sparte */) {
                                            echo "<optgroup label=\"{$gfield[$key]['spelling'][$key1]}\">";
                                        }

                                        # show field
                                        if($gfield[$key]["field_type"][$key1] != 14 /* post/edit user */
                                            AND $gfield[$key]["field_type"][$key1] != 15 /* post/edit date */
                                            AND $gfield[$key]["field_type"][$key1] != 16 /* user/group list */
                                            AND $gfield[$key]["data_type"][$key1] != 31 /* multiselect */
                                            AND $gfield[$key]["data_type"][$key1] != 18 /* select checkbox */
                                            AND $gfield[$key]["data_type"][$key1] != 14 /* select radio */
                                            AND $gfield[$key]["field_type"][$key1] != 19 /* attribute */
                                            AND $gfield[$key]["field_type"][$key1] != 6  /* upload */
                                            AND $gfield[$key]["field_type"][$key1] < 100){

                                            echo "<option value=\"{$key1}\">{$gfield[$key]['spelling'][$key1]}</option>";
                                        }
                                    }
                                }
                                echo '</select></div>';
                            }
                        }
                        ?>
                    </td>
                    <td><input class="form-control form-control-sm" type="text" id="typ_size" name="typ_size" style="visibility: hidden"></td>

                    <td colspan="14">
                        <button class="btn btn-primary btn-sm" type="submit" name="add" value="1"><?=$lang[540]?></button> <label class="ms-3"><?=$lang[1263]?> <INPUT type="CHECKBOX" name="add_permission" value="1" CHECKED></label>
                    </td>
                </TR>
                </tfoot>
    
            <?php endif; ?>
            
            
            


        </table>

    </FORM>

    <?=$message;?>

</div>

    <?php
    $bzm++;
endif;

?>

<div class="modal fade" id="fieldDeleteModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="fieldDeleteModalTitle"><?=$lang[2019]?></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p><?=$lang[1727]?></p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" id="buttonFieldDelete"><?=$lang[367]?></button>
          <button type="button" class="btn btn-warning" id="buttonFieldRemove"><?=$lang[2811]?></button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?=$lang[844]?></button>
      </div>
    </div>
  </div>
</div>

<div class="modal" id="modal-relation-settings" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?=$lang[$LINK['desc'][163]]?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <iframe id="relation-settings-frame" class="w-100" style="min-height:700px"></iframe>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="fieldsettingsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Field Settings</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="fieldsettingsContent"></div>
        </div>
    </div>
</div>




<div id="lmbAjaxContainer" class="ajax_container position-absolute" style="visibility: hidden;" ></div>

<?php if($argInFields): ?>


    <div class="modal" id="modal-argument" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="model-argument-title"></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">

                    <div class="mb-3 row phpargumentonly">
                        <label for="phpargumgvariablen" class="col-sm-4 col-form-label"><?=$lang[518]?></label>
                        <div class="col-sm-8">
                            <select id="phpargumgvariablen" onchange="insertArgumentText(this.value);" class="form-select">
                                <option></option>
                                <option value="<?=$session['username']?>"><?=$lang[519]?></option>
                                <option value="<?=$session['vorname'] . ' ' . $session['name']?>"><?=$lang[520]?></option>
                                <option value="<?=$session['email']?>"><?=$lang[521]?></option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3 row phpargumentonly">
                        <label for="phpargfields" class="col-sm-4 col-form-label"><?=$lang[168]?></label>
                        <div class="col-sm-8">
                            <select id="phpargfields" onchange="insertArgumentText(this.value);" class="form-select"></select>
                        </div>
                    </div>

                    <p id="argument-example"></p>

                    <div class="border mb-3">
                        <textarea id="fieldargument" rows="5"></textarea>
                    </div>


                    <div class="row">
                        <div class="col-8">
                            <button type="button" class="btn btn-outline-dark phpargumentonly" id="argument-refresh"><?=$lang[1304]?></button><span class="text-success small d-none ms-2" id="argument-refresh-ok">Rebuild complete!</span>
                            <div class="progress w-50 d-none" id="argument-refresh-progress">
                                <div class="progress-bar progress-bar-striped progress-bar-animated w-100" role="progressbar"></div>
                            </div>

                        </div>
                        <div class="col-4 text-end">
                            <button type="button" class="btn btn-outline-primary" id="argument-change"><?=$lang[522]?></button>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>


    <link rel="stylesheet" href="assets/vendor/codemirror/lib/codemirror.css?v=<?=$umgvar["version"]?>">
    <script src="assets/vendor/codemirror/lib/codemirror.js?v=<?=$umgvar["version"]?>"></script>
    <script src="assets/vendor/codemirror/addon/edit/matchbrackets.js?v=<?=$umgvar["version"]?>"></script>
    <script src="assets/vendor/codemirror/addon/edit/matchtags.js?v=<?=$umgvar["version"]?>"></script>
    <script src="assets/vendor/codemirror/mode/sql/sql.js?v=<?=$umgvar["version"]?>"></script>
    <script src="assets/vendor/codemirror/mode/php/php.js?v=<?=$umgvar["version"]?>"></script>
<?php endif; ?>
