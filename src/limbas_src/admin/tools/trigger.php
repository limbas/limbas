<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */




$odbc_table = dbf_20(array($DBA["DBSCHEMA"],null,"'TABLE'"));
$odbc_table =  $odbc_table["table_name"];

?>

<Script language="JavaScript">

function open_definition(id){
	if(document.getElementById('definition_'+id).style.display == 'none'){
		document.getElementById('definition_'+id).style.display = '';
	}else{
		document.getElementById('definition_'+id).style.display = 'none';
	}
}

function delete_definition(id){
	document.getElementById('trigger_'+id).style.display = 'none';
	document.getElementById('definition_'+id).style.display = 'none';
	document.getElementById("definition_change_"+id).value='2';
}

</Script>


<div class="container-fluid p-3">
    <FORM ACTION="main_admin.php" METHOD="post" NAME="form1">
        <input type="hidden" name="action" value="setup_trigger">
        <input type="hidden" name="sortup_definition">
        <input type="hidden" name="sortdown_definition">
        <input type="hidden" name="trigger_typ" value="<?=$trigger_typ?>">

        <ul class="nav nav-tabs">

            <TD nowrap class="tabpoolItemActive"></TD>
            <TD nowrap class="tabpoolItemInactive" OnClick="document.location.href='main_admin.php?action=setup_trigger&trigger_typ=2'"></TD>
            
            
            <?php if ($trigger_typ == 1): ?>
                <li class="nav-item">
                    <a class="nav-link active bg-contrast" href="#"><?=$lang[2450]?></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="main_admin.php?action=setup_trigger&trigger_typ=2"><?=$lang[2451]?></a>
                </li>
            <?php else: ?>
                <li class="nav-item">
                    <a class="nav-link" href="main_admin.php?action=setup_trigger&trigger_typ=1"><?=$lang[2450]?></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active bg-contrast" href="#"><?=$lang[2451]?></a>
                </li>
            <?php endif; ?>


        </ul>
        <div class="tab-content border border-top-0 bg-contrast">
            <div class="tab-pane active">
                
                    <table class="table table-sm table-striped mb-0">
                        
                        
                        <thead>
                        <tr>
                            <th class="border-top-0"><?=$lang[4]?></th>
                            <th class="border-top-0"><?=$lang[2270]?></th>
                            <th class="border-top-0"><?=$lang[2271]?></th>
                            <th class="border-top-0"><?=$lang[925]?></th>
                            <th class="border-top-0"><?=$lang[2269]?></th>
                            <th class="border-top-0">aktiv</th>
                            
                            <?=($trigger_typ == 2)?'<th class="border-top-0">order</th>':''?>
                            
                            <th class="border-top-0"></th>
                        </tr>
                        </thead>
                        <?php

                        foreach ($result_trigger as $key1 => $value1) :

                            foreach ($value1["id"] as $key2 => $value2){
                                if(!$showsystr AND (strtoupper(lmb_substr($value1["name"][$key2],0,12)) == "INSERT_VERK_" OR lmb_substr($value1["name"][$key2],0,12) == "UPDATE_VERK_" OR lmb_substr($value1["name"][$key2],0,16) == "LMB_LASTMODIFIED")) {continue 2;}
                            }
                            
                            ?>

                            <tr class="table-section"><TD colspan="<?=($trigger_typ == 2)?8:7?>"><?=$lang[164]?>: <span class="fw-bold"><?=$key1?></span></TD></tr>
                        
                        <?php

                            foreach ($value1["id"] as $key2 => $value2):
                                
                                ?>
                        
                        
                            <tr ID="trigger_<?=$value1["id"][$key2]?>">
                                <td><?=$value1["name"][$key2]?></td>
                                <td><?=get_date($value1["editdatum"][$key2],1)?></td>
                                <td><?=$userdat["bezeichnung"][$value1["edituser"][$key2]]?></td>
                                <td><?=$value1["position"][$key2]." ".$value1["type"][$key2]?></td>
                                <td><i class="lmb-icon lmb-pencil" style="cursor:pointer" OnClick="open_definition('<?=$value1["id"][$key2]?>')"></i></td>
                                <td><INPUT TYPE="checkbox" ID="active_<?=$value1["id"][$key2]?>" NAME="active[<?=$value1["id"][$key2]?>]" <?=($value1["active"][$key2]?"checked":"")?> onchange="document.form1.definition_change_<?=$value1["id"][$key2]?>.value='1';"></td>

                                <?php if ($trigger_typ == 2): ?>
                                    <td><i class="lmb-icon lmb-long-arrow-up" style="cursor:pointer" OnClick="document.form1.sortup_definition.value='<?=$value1["id"][$key2]?>';document.form1.submit();"></i> <i class="lmb-icon lmb-long-arrow-down" style="cursor:pointer" OnClick="document.form1.sortdown_definition.value='<?=$value1["id"][$key2]?>';document.form1.submit();"></i></td>
                                <?php endif; ?>
                                
                                <td><i class="lmb-icon lmb-trash" style="cursor:pointer" OnClick="delete_definition('<?=$value1["id"][$key2]?>')"></i></td>
                                
                            </tr>
                        
                            <tr ID="definition_<?=$value1["id"][$key2]?>" style="display:<?=(!$value1["value"][$key2])?'':'none'?>">
                                <TD colspan="<?=($trigger_typ == 2)?8:7 ?>"><TEXTAREA class="form-control" NAME="trigger_definition[<?=$value1["id"][$key2]?>]" OnChange="document.form1.definition_change_<?=$value1["id"][$key2]?>.value='1';" style="width:100%;height:100px;overflow:visible;"><?=htmlentities($value1["value"][$key2],ENT_QUOTES,$umgvar["charset"])?></TEXTAREA><INPUT TYPE="hidden" ID="definition_change_<?=$value1["id"][$key2]?>" NAME="definition_change_<?=$value1["id"][$key2]?>"></TD>
                            </tr>
                        
                        <?php
                                
                                
                            endforeach;
                            
                            
                        endforeach;
                        ?>

                        <tfoot>
                            <tr class="border-bottom border-top">
                                <td colspan="<?=($trigger_typ == 2)?8:7?>" class="text-end">

                                    <?php if($trigger_typ==1): ?>
                                    show system trigger <input type="checkbox" NAME="showsystr" <?= ($showsystr) ? 'checked':'' ?> onchange="this.form.submit();">&nbsp;&nbsp;
                                    <button class="btn btn-sm btn-primary" type="submit" name="syncronize" value="1"><?= $lang[2275] ?></button>
                                    <?php endif; ?>
                                    
                                    
                                    <button class="btn btn-sm btn-primary" type="submit" name="change" value="1"><?= $lang[842] ?></button>
                                </td>
                            </tr>
    
                            <tr>
                                <td><INPUT class="form-control form-control-sm" TYPE="TEXT" NAME="add_trigger_name"></td>
                                <td colspan="2"><SELECT NAME="add_trigger_table" class="form-select form-select-sm"><option></option><?=implode('\n', array_map(function($a) { return "<option>$a</option>";}, $odbc_table))?></td>
                                <td><select NAME="add_trigger_pos" class="form-select form-select-sm"><option value="AFTER">AFTER</option><option value="BEFORE">BEFORE</option></select></td>
                                <td><select NAME="add_trigger_type" class="form-select form-select-sm"><option value="INSERT">INSERT</option><option value="UPDATE">UPDATE</option><option value="DELETE">DELETE</option></select></td>
                                
                                <td colspan="<?=($trigger_typ == 2)?3:2?>" class="text-end"><button class="btn btn-sm btn-primary" type="submit" name="add" value="1"><?= $lang[540] ?></button></td>
                            </tr>
                        </tfoot>
                    </table>


            </div>
        </div>



    </FORM>
</div>
