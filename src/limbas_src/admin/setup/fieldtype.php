<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */


?>

<Script language="JavaScript">
function change_field(id) {
    console.log(document.form1.update_data);
	document.form1.update_data.value = document.form1.update_data.value + ";" + id;
}
function del_field(id) {
	document.form1.del_data.value = id;
	document.form1.submit();
}
</Script>

<div class="container-fluid p-3">
<FORM ACTION="main_admin.php" METHOD="post" name="form1">
	<input type="hidden" name="action" value="setup_ftype">
    <input type="hidden" name="update_data" value="">
    <input type="hidden" name="del_data">

        <ul class="nav nav-tabs">
            <li class="nav-item">
                <a class="nav-link active bg-contrast" href="#"><?=$lang[$LINK["desc"][177]]?></a>
            </li>
            <?php if ($LINK['setup_ftype_depend']): ?>
                <li class="nav-item">
                    <a class="nav-link" href="main_admin.php?action=setup_custvar"><?=$lang[$LINK["desc"][1514]]?></a>
                </li>
            <?php endif; ?>
        </ul>
        <div class="tab-content border border-top-0 bg-contrast">
            <div class="tab-pane active">



        <table id="lmb_resource" class="table table-sm table-striped mb-0">
            <thead>
            <tr>
                <th><?=$lang[949]?></th>
                <th>parse_type</th>
                <th><?=$lang[1516]?></th>
                <th><?=$lang[1517]?></th>
                <th><?=$lang[1518]?></th>
                <th><?=$lang[1519]?></th>
                <th><?=$lang[210]?></th>
                <th><?=$lang[924]?></th>
                <th><?=$lang[126]?></th>
                <th><?=$lang[1078]?></th>
                <th><?=$lang[160]?></th>
            </tr>
            </thead>

            <tbody>
            <?php

            // catogorie translate
            $categorie_spell[1] = $lang[2944];
            $categorie_spell[2] = $lang[1397];
            $categorie_spell[3] = $lang[1961];
            $categorie_spell[4] = $lang[1460];
            $categorie_spell[5] = $lang[545];
            $categorie_spell[6] = $lang[2362];
            $categorie_spell[7] = $lang[1924];
            $categorie_spell[10] = $lang[1813];


            /* --- Ergebnisliste --------------------------------------- */
            $currentCategory = null;
            foreach ($result_ftype["id"] as $key => $val) : ?>

            <?php

                    if ($currentCategory !== $result_ftype['categorie'][$key]): ?>
                        <tr class="table-section"><td colspan="11"><?=$categorie_spell[$result_ftype["categorie"][$key]]?></td></tr>
            <?php
                        $currentCategory = $result_ftype['categorie'][$key];
                    endif;

                    $class = '';
                    if ($result_ftype["depend"][$key]) {
                        // changed in _depend
                        $class = "bg-warning";
                    } else {
                        // new in _depend
                        $bg = 'bg-success text-white';
                    }

                    ?>

                    

                    <tr>
                    
                    <TD class="<?=$class?>">
        
                    <?php

                    //TODO: LAYOUT
                    if ($result_ftype["depend"][$key]) :
                    ?>
                    <A onclick="document.getElementById('quickview_<?=$key?>').style.visibility='visible'"><?= $result_ftype["id"][$key] ?></A>&nbsp;
                    <div id="quickview_<?=$key?>" style="position:absolute;overflow:visible;visibility:hidden;border:1px solid black;padding:3px;cursor:pointer;background-color:<?=$farbschema["WEB3"]?>" OnClick="this.style.visibility='hidden'">


                        <?php
                        $result1 = "parse_type: " . $result_ftype["parse_type"][$key] . "\n" . $lang[1516] . ": " . $result_ftype["field_type"][$key] . "\n" . $lang[1517] . ": " . $result_ftype["data_type"][$val] . "\n" . $lang[1518] . ": " . $result_ftype["funcid"][$val] . "\n" . $lang[1519] . ": " . $result_ftype["datentyp"][$val] . "\n" . $lang[210] . ": " . $result_ftype["size"][$val] . "\n" . $lang[924] . ": " . $result_ftype["data_type_exp"][$val] . "\n" . $lang[126] . ": " . $result_ftype["format"][$val] . "\n" . $lang[1078] . ": " . $result_ftype["rule"][$val];
                        $result2 = "parse_type: " . $result_ftype["system_parse_type"][$key] . "\n" . $lang[1516] . ": " . $result_ftype["system_field_type"][$key] . "\n" . $lang[1517] . ": " . $result_ftype["system_data_type"][$val] . "\n" . $lang[1518] . ": " . $result_ftype["system_funcid"][$val] . "\n" . $lang[1519] . ": " . $result_ftype["system_datentyp"][$val] . "\n" . $lang[210] . ": " . $result_ftype["system_size"][$val] . "\n" . $lang[924] . ": " . $result_ftype["system_data_type_exp"][$val] . "\n" . $lang[126] . ": " . $result_ftype["system_format"][$val] . "\n" . $lang[1078] . ": " . $result_ftype["system_rule"][$val];

                        echo tableDiff($result1, $result2, "ID:" . $result_ftype["id"][$key], "local copy", "system", 2, 1);

                        ?>

                    </div>

                    <?php
                    else:
                        echo $result_ftype["id"][$key];
                    endif;

                    $readonly = '';
                    if (!$_SESSION['umgvar']['admin_mode'] AND $key < 1000) {
                        $readonly = 'readonly';
                    }

                    ?>
                    </TD>
                        <TD><INPUT TYPE="TEXT" <?=$readonly?> NAME="parse_type_<?=$result_ftype["id"][$key]?>" VALUE="<?=$result_ftype['parse_type'][$key]?>" OnChange="change_field('<?=$result_ftype['id'][$key]?>')" class="form-control form-control-sm"></TD>
                        <TD><INPUT TYPE="TEXT" <?=$readonly?> NAME="field_type_<?=$result_ftype["id"][$key]?>" VALUE="<?=$result_ftype['field_type'][$key]?>" OnChange="change_field('<?=$result_ftype['id'][$key]?>')" class="form-control form-control-sm"></TD>
                        <TD><INPUT TYPE="TEXT" <?=$readonly?> NAME="data_type_<?=$result_ftype["id"][$key]?>" VALUE="<?=$result_ftype['data_type'][$key]?>" OnChange="change_field('<?=$result_ftype['id'][$key]?>')" class="form-control form-control-sm"></TD>
                        <TD><INPUT TYPE="TEXT" <?=$readonly?> NAME="funcid_<?=$result_ftype["id"][$key]?>" VALUE="<?=$result_ftype['funcid'][$key]?>" OnChange="change_field('<?=$result_ftype['id'][$key]?>')" class="form-control form-control-sm"></TD>
                        <TD><INPUT TYPE="TEXT" NAME="datentyp_<?=$result_ftype["id"][$key]?>" VALUE="<?=$result_ftype['datentyp'][$key]?>" OnChange="change_field('<?=$result_ftype['id'][$key]?>')" class="form-control form-control-sm"></TD>
                        <TD><INPUT TYPE="TEXT" NAME="size_<?=$result_ftype["id"][$key]?>" VALUE="<?=$result_ftype['size'][$key]?>" OnChange="change_field('<?=$result_ftype['id'][$key]?>')" class="form-control form-control-sm"></TD>
                        <TD><div style="display:none"><?=$result_ftype['data_type_exp'][$key]?></div><INPUT TYPE="TEXT" NAME="data_type_exp_<?=$result_ftype["id"][$key]?>" VALUE="<?=$result_ftype['data_type_exp'][$key]?>" OnChange="change_field('<?=$result_ftype['id'][$key]?>')" class="form-control form-control-sm"></TD>
                        <TD><div style="display:none"><?=$result_ftype['format'][$key]?></div><INPUT TYPE="TEXT" NAME="format_<?=$result_ftype["id"][$key]?>" VALUE="<?=$result_ftype['format'][$key]?>" OnChange="change_field('<?=$result_ftype['id'][$key]?>')" class="form-control form-control-sm"></TD>
                        <TD><INPUT TYPE="TEXT" NAME="rule_<?=$result_ftype["id"][$key]?>" VALUE="<?=$result_ftype['rule'][$key]?>" OnChange="change_field('<?=$result_ftype['id'][$key]?>')" class="form-control form-control-sm"></TD>
                        <TD>

                        <?php
        
                        $showTrash = true;
                        if ($result_ftype["depend"][$key]) {
                            // changed in _depend
                            $tooltip = "delete only in depend table";
                        } elseif ($_SESSION['umgvar']['admin_mode']) {
                            $tooltip = "fully delete!";
                        } else {
                            $showTrash = false;
                        }
                        
                        if ($showTrash) : ?>

                            <i class="lmb-icon lmb-trash" title="<?=$tooltip?>" STYLE="cursor:pointer;color:<?=$bg?>;" OnClick="del_field('<?=$result_ftype["id"][$key]?>')"></i>
                            
                        <?php endif; ?>
        
                        </TD>
                        </tr>

            
            
                <?php endforeach; ?>
            </tbody>


            <tfoot>
            <tr class="border-bottom border-top">
                <td colspan="11">
                    <button class="btn btn-sm btn-primary" type="submit"><?= $lang[522] ?></button>
                </td>
            </tr>

            <TR>
                <TD><INPUT TYPE="TEXT" NAME="parse_type" VALUE="2" class="form-control form-control-sm"></TD>
                <TD><INPUT TYPE="TEXT" NAME="field_type" VALUE="<?=$result_ftype["maxftype"]?>" class="form-control form-control-sm"></TD>
                <TD><INPUT TYPE="TEXT" NAME="data_type" VALUE="<?=$result_ftype["maxdtype"]?>" class="form-control form-control-sm"></TD>
                <TD><INPUT TYPE="TEXT" NAME="funcid" VALUE="<?=$result_ftype["maxfid"]?>" class="form-control form-control-sm"></TD>
                <TD><INPUT TYPE="TEXT" NAME="datentyp" class="form-control form-control-sm"></TD>
                <TD><INPUT TYPE="TEXT" NAME="size" class="form-control form-control-sm"></TD>
                <TD><INPUT TYPE="TEXT" NAME="data_type_exp" class="form-control form-control-sm"></TD>
                <TD><INPUT TYPE="TEXT" NAME="format" class="form-control form-control-sm"></TD>
                <TD><INPUT TYPE="TEXT" NAME="rule" class="form-control form-control-sm"></TD>
                <TD>
                    <?php if ($_SESSION['umgvar']['admin_mode']) {?>
                    <SELECT NAME="categorie" class="form-select form-select-sm">
                        <option value="1"><?=$lang[2944]?></option>
                        <option value="2"><?=$lang[1397]?></option>
                        <option value="3"><?=$lang[1961]?></option>
                        <option value="4"><?=$lang[1460]?></option>
                        <option value="5"><?=$lang[545]?></option>
                        <option value="7"><?=$lang[1924]?></option>
                    </SELECT>
                    <?php }?>
                </TD>
                <td><button class="btn btn-sm btn-primary" type="submit" name="add_data" value="1"><?= $lang[540] ?></button></td>
            </TR>

            </tfoot>
        </table>
        </div>
        </div>

</FORM>
</div>
