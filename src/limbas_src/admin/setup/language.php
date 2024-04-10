<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */


?>



<SCRIPT LANGUAGE="JavaScript">
function change_file(val) {
	if(val) {
		document.form1.EDIT.value = "a";
		document.form1.submit();
	}else{
		document.form1.EDIT.value = "FALSE";
		document.form1.submit();
	}
}
</SCRIPT>


<div class="container-fluid p-3">
    <FORM ENCTYPE="multipart/form-data" ACTION="main_admin.php" METHOD="post" name="form1">
        <input type="hidden" name="action" value="setup_language">
        <input type="hidden" name="language_typ" value="<?=$language_typ?>">
        <input type="hidden" name="language_id" value="<?=$language_id?>">
        <input type="hidden" name="list" value="<?=$list?>">
        <input type="hidden" name="order" value="<?=$order?>">
        <input type="hidden" name="is_value">
        <input type="hidden" name="is_js">
        <input type="hidden" name="is_override">
        <input type="hidden" name="del">

        <ul class="nav nav-tabs">
            
            <?php if($language_typ == 1) : ?>
                    <li class="nav-item">
                        <a class="nav-link active bg-contrast" href="#"><?=$lang[$LINK["desc"][108]]?></a>
                    </li>
                <?php if ($LINK[258]) : ?>
                    <li class="nav-item">
                        <a class="nav-link" href="main_admin.php?action=setup_language&language_typ=2"><?=$lang[$LINK["desc"][258]]?></a>
                    </li>
                <?php endif; ?>
            
            
            <?php elseif($language_typ == 2): ?>
                <?php if ($LINK[108]) : ?>
                    <li class="nav-item">
                        <a class="nav-link" href="main_admin.php?action=setup_language&language_typ=1"><?=$lang[$LINK["desc"][108]]?></a>
                    </li>
                <?php endif; ?>
                <li class="nav-item">
                    <a class="nav-link active bg-contrast" href="#"><?=$lang[$LINK["desc"][258]]?></a>
                </li>
            <?php endif; ?>
        </ul>
        
        
                
                <?php if($language_id AND $list) : ?>
        <div class="tab-content border border-top-0 bg-contrast">
            <div class="tab-pane active">
                <table class="table table-sm mb-0">
                    
                    <thead>
                        <tr>
                            <th class="border-top-0"><A HREF="#" OnClick="document.form1.order.value='ELEMENT_ID';document.form1.submit();"><?=$lang[949]?></A></th>
                            <th class="border-top-0"><A HREF="#" OnClick="document.form1.order.value='EDIT';document.form1.submit();"><?=$lang[1205]?></A> </th>
                            <th class="border-top-0"><A HREF="#" OnClick="document.form1.order.value='LANGUAGE';document.form1.submit();"><?=$lang[624]?></A> </th>
                            <th class="border-top-0"><A HREF="#" OnClick="document.form1.order.value='TYP';document.form1.submit();"><?=$lang[925]?></A> </th>
                            <th class="border-top-0"><A HREF="#" OnClick="document.form1.order.value='FILE';document.form1.submit();"><?=$lang[545]?></A> </th>
                            <th class="border-top-0"><A HREF="#" OnClick="document.form1.order.value='REF';document.form1.submit();"><?=$lang[29]?></A></th>
                            <th class="border-top-0"><A HREF="#" OnClick="document.form1.order.value='WERT';document.form1.submit();"><?=$lang[29]?></A> </th>
                            <?php if($language_typ == 2): ?>
                            <th class="border-top-0"><A HREF="#" OnClick="document.form1.order.value='OVERRIDE';document.form1.submit();"><?=$lang[1002]?></A> </th>
                            <?php endif; ?>
                            <th class="border-top-0">JS</th>
                            <th class="border-top-0"><?=$lang[160]?></th>
                        </tr>
                    </thead>

                        <tr>
                            <td>
                                <INPUT class="form-control form-control-sm" TYPE="TEXT" SIZE="5" NAME="ID" VALUE="<?=$ID?>" OnChange="document.form1.submit();">
                            </TD>
                            <td>
                                <SELECT class="form-select form-select-sm" NAME="EDIT" OnChange="document.form1.submit();">
                                    <OPTION VALUE="a" <?=($EDIT == 'a')?'selected':''?>><?=$lang[994]?>
                                    <OPTION VALUE="FALSE" <?=($EDIT == 'FALSE')?'selected':''?>><?=$lang[1217]?>
                                    <OPTION VALUE="TRUE" <?=($EDIT == 'TRUE')?'selected':''?>><?=$lang[1218]?>
                                </SELECT>
                            </td>
                            <td>
                                <SELECT class="form-select form-select-sm" NAME="language_id" OnChange="document.form1.submit();">
                                    <?php foreach($result_language['language_id'] as $key => $value) : ?>
                                        <option value="<?=$value?>" <?=($value == $language_id)?'selected':''?>><?=$result_language["language"][$key]?></option>
                                    <?php endforeach; ?>
                                </SELECT>
                            </td>
                            <td>
                                <SELECT class="form-select form-select-sm" NAME="TYP" OnChange="document.form1.submit();">
                                    <OPTION VALUE="0" <?=($TYP == 0)?'selected':''?>><?=$lang[994]?>
                                    <OPTION VALUE="1" <?=($TYP == 1)?'selected':''?>><?=$lang[1219]?>
                                    <OPTION VALUE="2" <?=($TYP == 2)?'selected':''?>><?=$lang[1221]?>
                                    <OPTION VALUE="3" <?=($TYP == 3)?'selected':''?>><?=$lang[1220]?>
                                    <OPTION VALUE="4" <?=($TYP == 4)?'selected':''?>><?=$lang[577]?>
                                    <OPTION VALUE="5" <?=($TYP == 5)?'selected':''?>><?=$lang[1986]?>
                                </SELECT>
                            </td>
                            <td>
                                <INPUT class="form-control form-control-sm" TYPE="TEXT" NAME="FILE" VALUE="<?=$FILE?>" OnChange="change_file(this.value)">
                            </td>
                            <td>
                                <SELECT class="form-select form-select-sm" NAME="ref_id" OnChange="document.form1.submit();">
                                    <?php foreach($result_language['language_id'] as $key => $value) : ?>
                                        <option value="<?=$value?>" <?=($value == $ref_id)?'selected':''?>><?=$result_language["language"][$key]?></option>
                                    <?php endforeach; ?>
                                </SELECT>
                                <INPUT class="form-control form-control-sm" TYPE="TEXT" NAME="REFWERT" VALUE="<?=$REFWERT?>" OnChange="document.form1.submit();">&nbsp;
                            </td>
                            <td>
                                <INPUT class="form-control form-control-sm" TYPE="TEXT" NAME="WERT" VALUE="<?=$WERT?>" OnChange="document.form1.submit();">
                            </td>
                            <td>
                                <INPUT TYPE="checkbox" class="form-check-input" name="override" OnChange="document.form1.submit();" <?=($override)?'checked':''?>>
                            </td>
                            <td>
                                <INPUT TYPE="checkbox" class="form-check-input" name="JS" OnChange="document.form1.submit();" <?=($JS)?'checked':''?>>
                            </td>
                            <td></td>
                        </tr>

                        <tr><td colspan="10">&nbsp;</td></tr>

                        <?php
                        /* --- Ergebnisliste bei Refrenzsuche--------------------------------------- */
                        $bzm = 0;
                        if($result_ref["element_id"] AND $revvalue){
                            foreach($result_ref["element_id"] as $val){
                                if($result_el["id"][$val]){
                                    if($result_el["js"][$val]){$CHECKED = "CHECKED";}else{$CHECKED = "";}
                                    ?>
                                    <TR>
                                        <TD>&nbsp;<?= $result_el["element_id"][$val] ?>&nbsp;</TD>
                                        <TD>&nbsp;<?php if($result_el["edit"][$val] == 1){echo "<i class=\"lmb-icon lmb-aktiv\"></i>";}else{echo "<i class=\"lmb-icon lmb-erase\"></i>";}?>&nbsp;</TD>
                                        <TD>&nbsp;<?= $result_el["language"][$val] ?>&nbsp;</TD>
                                        <TD><?= $langtypname[$result_el["typ"][$val]] ?></TD>
                                        <TD style="width:400px"><?= $result_el["file"][$val] ?></TD>
                                        <TD style="width:400px"><?= $result_ref["wert"][$val] ?></TD>
                                        <TD><TEXTAREA class="form-control form-control-sm w-100 p-3" NAME="new_value[<?=$result_el["id"][$val]?>]" OnChange="document.form1.is_value.value=document.form1.is_value.value+';<?=$result_el["id"][$val]?>'"><?= htmlentities($result_el["wert"][$val],ENT_QUOTES,$umgvar["charset"]) ?></TEXTAREA></TD>
                                        <?php if($language_typ == 2): ?>
                                            <TD style="width:50px"><INPUT class="form-control form-control-sm" TYPE="TEXT" NAME="new_override[<?=$result_el["id"][$val]?>]" VALUE="<?=$result_el["override"][$val]?>" OnChange="document.form1.is_override.value=document.form1.is_override.value+';<?=$result_el["id"][$val]?>'"></TD>
                                        <?php endif; ?>
                                        <TD><INPUT TYPE="CHECKBOX" class="form-check-input" NAME="new_js[<?=$result_el["id"][$val]?>]" <?=$CHECKED?> OnChange="document.form1.is_js.value=document.form1.is_js.value+';<?=$result_el["id"][$val]?>'"></TD>
                                        <TD><i class="lmb-icon lmb-trash"></i></TD>
                                    </TR>
                                    <?php
                                }
                                if($bzm > $showlimit){break;}
                                $bzm++;
                            }
                            /* --- Ergebnisliste bei Normalsuche --------------------------------------- */
                        }elseif($result_el["element_id"] AND !$REFWERT){
                            foreach($result_el["element_id"] as $val){
                                if($result_el["id"][$val]){
                                    if($result_el["js"][$val]){$CHECKED = "CHECKED";}else{$CHECKED = "";}
                                    ?>
                                    <TR>
                                        <TD>&nbsp;<?= $result_el["element_id"][$val] ?>&nbsp;</TD>
                                        <TD>&nbsp;<?php if($result_el["edit"][$val] == 1){echo "<i class=\"lmb-icon lmb-aktiv\"></i>";}else{echo "<i class=\"lmb-icon lmb-erase\"></i>";}?>&nbsp;</TD>
                                        <TD>&nbsp;<?= $result_el["language"][$val] ?>&nbsp;</TD>
                                        <TD><?= $langtypname[$result_el["typ"][$val]] ?></TD>
                                        <TD><?= $result_el["file"][$val] ?></TD>
                                        <TD style="width:400px"><?= $result_ref["wert"][$val] ?></TD>
                                        <TD style="width:400px"><TEXTAREA class="form-control form-control-sm w-100 p-3" NAME="new_value[<?=$result_el["id"][$val]?>]" OnChange="document.form1.is_value.value=document.form1.is_value.value+';<?=$result_el["id"][$val]?>'"><?= htmlentities($result_el["wert"][$val],ENT_QUOTES,$umgvar["charset"]) ?></TEXTAREA></TD>
                                        <?php if($language_typ == 2): ?>
                                            <TD style="width:50px"><INPUT class="form-control form-control-sm" TYPE="TEXT" NAME="new_override[<?=$result_el["id"][$val]?>]" VALUE="<?=$result_el["override"][$val]?>" OnChange="document.form1.is_override.value=document.form1.is_override.value+';<?=$result_el["id"][$val]?>'"></TD>
                                        <?php endif; ?>
                                        <TD><INPUT class="form-check-input" TYPE="CHECKBOX" NAME="new_js[<?=$result_el["id"][$val]?>]" <?=$CHECKED?> OnChange="document.form1.is_js.value=document.form1.is_js.value+';<?=$result_el["id"][$val]?>'" ></TD>
                                        <TD><i class="lmb-icon lmb-trash" OnClick="document.form1.del.value='<?=$val?>';document.form1.submit();" STYLE="cursor:pointer"></i></TD>
                                    </TR>
                                    <?php
                                }
                                if($bzm > $showlimit){break;}
                                $bzm++;
                            }
                        }

                        ?>
                        <TR>
                            <TD COLSPAN="6"></TD>
                            <TD><?=$lang[96]?>: <select name="showlimit" onchange="this.form.submit();" class="form-select form-select-sm">
                                    <option value="100" <?php if($showlimit == 100){echo "selected";}?>>100
                                    <option value="500" <?php if($showlimit == 500){echo "selected";}?>>500
                                    <option value="9000" <?php if($showlimit == 9000){echo "selected";}?>><?=$lang[994]?>
                                </select></TD>
                            <TD COLSPAN="2"><INPUT TYPE="submit" VALUE="<?=$lang[522]?>" NAME="change" class="btn btn-sm btn-primary"></TD>
                        </TR>
                        <TR><TD COLSPAN="9"><HR></TD></TR>
                        <TR>
                            <TD></TD>
                            <TD></TD>
                            <TD></TD>
                            <TD><SELECT NAME="typ" class="form-select form-select-sm">
                                    <OPTION VALUE="1" <?php if($typ == 1){echo "SELECTED";}?>><?=$lang[1219]?>
                                    <OPTION VALUE="2" <?php if($typ == 2){echo "SELECTED";}?>><?=$lang[1221]?>
                                    <OPTION VALUE="3" <?php if($typ == 3){echo "SELECTED";}?>><?=$lang[1220]?>
                                    <OPTION VALUE="4" <?php if($typ == 4){echo "SELECTED";}?>><?=$lang[577]?>
                                    <OPTION VALUE="5" <?php if($typ == 5){echo "SELECTED";}?>><?=$lang[1986]?>
                                </SELECT></TD>

                            <TD><INPUT TYPE="TEXT" NAME="file" VALUE="<?=$FILE?>"></TD>
                            <TD></TD>
                            <TD><INPUT TYPE="TEXT" NAME="wert"></TD>
                            <TD COLSPAN="2"><INPUT TYPE="submit" VALUE="<?=$lang[540]?>" NAME="add" class="btn btn-sm btn-primary"></TD>
                        </TR>
                    </table>
            </div>
        </div>

                <?php else :

                    $multi_language = $umgvar['multi_language'];

                    if($language_typ == 2){$mheader = '<th class="border-top-0">'.$lang[2897].'</th>';}
                    ?>


                    <div class="row">
                        <div class="col-md-6 col-lg-4">
                            <div class="tab-content border border-top-0 bg-contrast">
                                <div class="tab-pane active">
                                    <table class="table table-sm mb-0">
                                        <thead>
                                        <tr>
                                            <th class="border-top-0">default</th>
                                            <th class="border-top-0"><?=$lang[624]?></th>
                                            <th class="border-top-0"><?=$lang[1205]?></th>
                                            <?=$mheader?>
                                            <th class="border-top-0"></th>
                                        </tr>
                                        </thead>


                                        <?php foreach ($result_language["language_id"] as $bzm => $lval) :
                                            if($result_language["edit"][$bzm]){$edit_color = "text-danger";}else{$edit_color = "text-success";}
                                            ?>
                                            <tr>

                                                <td><?=($umgvar['default_language'] == $result_language["language_id"][$bzm])?'*':''?></td>

                                                <td><A href="main_admin.php?action=setup_language&list=1&language_id=<?=$result_language["language_id"][$bzm]?>&language_typ=<?=$language_typ?>"><?=$result_language["language"][$bzm]?></A></td>

                                                <td><span class="<?=$edit_color?>"><?=$result_language['edit'][$bzm]?></td>

                                                <?php

                                                // skip default language
                                                if($language_typ == 2) :
                                                    if ($result_language["language_id"][$bzm] != $umgvar['default_language']) : ?>

                                                        <td><input class="form-check-input" type="checkbox" onclick="if(confirm('<?=$lang[2898]?>')){document.location.href='main_admin.php?action=setup_language&language_typ=<?=$language_typ?>&language_id=<?=$result_language["language_id"][$bzm]?>&multilang='+this.checked;}" <?=(is_array($multi_language) && in_array($result_language["language_id"][$bzm],$multi_language))?'checked':''?>></td>

                                                    <?php else: ?>
                                                        <td></td>
                                                    <?php endif;

                                                endif; ?>

                                                <td><A href="main_admin.php?action=setup_language&del_lang=1&language_id=<?=$result_language["language_id"][$bzm]?>&language_typ=<?=$language_typ?>"><i class="lmb-icon lmb-trash"></i></A></td>
                                            </tr>

                                        <?php endforeach; ?>
                                        <tfoot>
                                        <TR>
                                            <TD></TD>
                                            <TD><INPUT TYPE="TEXT" SIZE="15" NAME="add_lang" class="form-control form-control-sm"></TD>
                                            <TD colspan="3"><button class="btn btn-sm btn-primary" type="submit" name="add" value="1"><?= $lang[540] ?></button></TD>
                                        </TR>


                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                
                
                    

                <?php endif; ?>
                

    </FORM>
</div>
