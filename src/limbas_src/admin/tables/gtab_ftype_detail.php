<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */


global $LINK;
global $gtrigger;

$fieldid = $par["fieldid"];
$gtabid = $par["gtabid"];
$tab_group = $par["tab_group"];
$act = $par["act"];
$val = $par["val"];
$solve_dependency = $par["solve_dependency"];

if($act){
	${$act} = $val;
}
$atid = $gtabid;
$ftid = $fieldid;
if($gtab["typ"][$gtabid] == 5){$isview = 1;}



require_once(COREPATH . 'admin/tables/gtab_ftype.dao');

	
?>

<form action="main_dyns_admin.php" method="post" name="form2">
    <input type="hidden" name="val">
    <input type="hidden" name="solve_dependency">
</form>

    <ul class="nav nav-tabs" id="myTab" role="tablist">
        <li class="nav-item" role="presentation">
            <a class="nav-link active" id="options-tab" data-bs-toggle="tab" href="#options" role="tab"><?=$lang[2795]?></a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link" id="info-tab" data-bs-toggle="tab" href="#info" role="tab"><?=$lang[2836]?></a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link" id="info-tab" data-bs-toggle="tab" href="#grouprights" role="tab"><?=$lang[575]?></a>
        </li>
    </ul>
    <div class="tab-content">
        <div class="tab-pane show active py-3" id="options" role="tabpanel">

            <div class="row">
                <div class="col-6">

                    <?php // fieldname ?>
                    <div class="mb-3 row">
                        <label class="col-sm-4 col-form-label col-form-label-sm"><?=$lang[922]?></label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control form-control-sm" value="<?=$result_fieldtype[$table_gtab[$bzm]]['field'][1]?>" onchange="document.form2.val.value=this.value;ajaxEditField('<?=$fieldid?>','fieldname')" <?=($isview)?'readonly disabled':''?>>
                        </div>
                    </div>


                    <?php // spelling ?>
                    <div class="mb-3 row">
                        <label class="col-sm-4 col-form-label col-form-label-sm"><?=$lang[924]?></label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control form-control-sm" value="<?=$lang[$result_fieldtype[$table_gtab[$bzm]]['spelling'][1]]?>" onchange="document.form2.val.value=this.value;ajaxEditField('<?=$fieldid?>','spelling')">
                        </div>
                    </div>

                    <?php // title ?>
                    <div class="mb-3 row">
                        <label class="col-sm-4 col-form-label col-form-label-sm"><?=$lang[923]?></label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control form-control-sm" value="<?=$lang[$result_fieldtype[$table_gtab[$bzm]]['beschreibung_feld'][1]]?>" onchange="document.form2.val.value=this.value;ajaxEditField('<?=$fieldid?>','desc')">
                        </div>
                    </div>


                    <?php // fieldtype ?>
                    <div class="mb-3 row">
                        <label class="col-sm-4 col-form-label col-form-label-sm"><?=$lang[925]?></label>
                        <div class="col-sm-8">
                            <span class="form-control-plaintext">
                                <?php
                                if($result_fieldtype[$table_gtab[$bzm]]["scale"][1]){
                                    $fsize = $result_fieldtype[$table_gtab[$bzm]]["precision"][1].",".$result_fieldtype[$table_gtab[$bzm]]["scale"][1];
                                }else{
                                    $fsize = $result_fieldtype[$table_gtab[$bzm]]["precision"][1];
                                }
                                if($result_fieldtype[$table_gtab[$bzm]]["argument_typ"][1]){
                                    echo $lmfieldtype["description"][$result_fieldtype[$table_gtab[$bzm]]["argument_typ"][1]]."&nbsp;-&nbsp;";
                                }
                                echo $result_fieldtype[$table_gtab[$bzm]]["beschreibung_typ"][1];
                                if($result_fieldtype[$table_gtab[$bzm]]["inherit_tab"][1]){echo "($lang[2086])";}
                                ?>
                            </span>

                            <small class="form-text text-muted"><?=$result_fieldtype[$table_gtab[$bzm]]["format_typ"][1]?></small>
                            
                        </div>
                    </div>

                    <?php // dbtype 
                    if($result_fieldtype[$table_gtab[$bzm]]["type_name"][1]):
                    ?>
                    <div class="mb-3 row">
                        <label class="col-sm-4 col-form-label col-form-label-sm">DB-<?=$lang[925]?></label>
                        <div class="col-sm-8">
                            <span class="form-control-plaintext">
                                (<?=$result_fieldtype[$table_gtab[$bzm]]["type_name"][1]?> <?=$fsize?>)
                            </span>
                        </div>
                    </div>
                    <?php endif; ?>


                    <?php // fieldsize 
                    if($lmfieldtype["hassize"][$result_fieldtype[$table_gtab[$bzm]]["datatype"][1]]):
                        ?>
                        <div class="mb-3 row">
                            <label class="col-sm-4 col-form-label col-form-label-sm"><?=$lang[210]?></label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control form-control-sm" value="<?=$result_fieldtype[$table_gtab[$bzm]]["field_size"][1]?>" onchange="convert_field('<?=$result_fieldtype[$table_gtab[$bzm]]["datatype"][1]?>','<?=$result_fieldtype[$table_gtab[$bzm]]["field_id"][1]?>','<?=$result_fieldtype[$table_gtab[$bzm]]["field"][1]?>',this.value);">
                                <small class="form-text text-muted"><?=$lang[2655]?></small>
                            </div>
                        </div>
                    <?php endif; ?>



                    <?php // default 
                    if(!$isview):
                    if(!$result_fieldtype[$table_gtab[$bzm]]["domain_admin_default"][1] AND $result_fieldtype[$table_gtab[$bzm]]["fieldtype"][1] < 100 AND $result_fieldtype[$table_gtab[$bzm]]["datatype"][1] != 22 AND $result_fieldtype[$table_gtab[$bzm]]["fieldtype"][1] != 4 AND $result_fieldtype[$table_gtab[$bzm]]["fieldtype"][1] != 6 AND $result_fieldtype[$table_gtab[$bzm]]["fieldtype"][1] != 8 AND $result_fieldtype[$table_gtab[$bzm]]["fieldtype"][1] != 9 AND $result_fieldtype[$table_gtab[$bzm]]["fieldtype"][1] != 11 AND $result_fieldtype[$table_gtab[$bzm]]["fieldtype"][1] != 12 AND $result_fieldtype[$table_gtab[$bzm]]["fieldtype"][1] != 13 AND $result_fieldtype[$table_gtab[$bzm]]["fieldtype"][1] != 19 AND $result_fieldtype[$table_gtab[$bzm]]["fieldtype"][1] != 18 AND $result_fieldtype[$table_gtab[$bzm]]["fieldtype"][1] != 14 AND $result_fieldtype[$table_gtab[$bzm]]["fieldtype"][1] != 15 AND $result_fieldtype[$table_gtab[$bzm]]["fieldtype"][1] != 16 AND $result_fieldtype[$table_gtab[$bzm]]["datatype"][1] != 44 AND $result_fieldtype[$table_gtab[$bzm]]["fieldtype"][1] < 100 AND !$result_fieldtype[$table_gtab[$bzm]]["argument_typ"][1]):
                        
                        ?>
                        <div class="mb-3 row">
                            <label class="col-sm-4 col-form-label col-form-label-sm"><?=$lang[928]?></label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control form-control-sm" value="<?=$result_fieldtype[$table_gtab[$bzm]]["domain_default"][1]?>" onchange="document.form2.val.value=this.value+' ';ajaxEditField('<?=$fieldid?>','def')">
                                <small class="form-text text-muted"><?=$lang[2588]?></small>
                            </div>
                        </div>

                    <?php elseif($result_fieldtype[$table_gtab[$bzm]]["domain_admin_default"][1]): ?>

                        <div class="mb-3 row">
                            <label class="col-sm-4 col-form-label col-form-label-sm"><?=$lang[928]?></label>
                            <div class="col-sm-8">
                            <span class="form-control-plaintext">
                                <?=$result_fieldtype[$table_gtab[$bzm]]["domain_admin_default"][1]?>
                            </span>
                            </div>
                        </div>
                    
                    <?php
                    endif;
                    endif; ?>



                    <?php // regex
                    if(!$isview):
                    if($result_fieldtype[$table_gtab[$bzm]]["fieldtype"][1] < 100
                        AND $result_fieldtype[$table_gtab[$bzm]]["fieldtype"][1] != 4
                        AND $result_fieldtype[$table_gtab[$bzm]]["datatype"][1] != 22
                        AND $result_fieldtype[$table_gtab[$bzm]]["datatype"][1] !=55
                        AND $result_fieldtype[$table_gtab[$bzm]]["datatype"][1] != 50
                        AND $result_fieldtype[$table_gtab[$bzm]]["datatype"][1] != 51
                        AND $result_fieldtype[$table_gtab[$bzm]]["datatype"][1] != 52
                        AND $result_fieldtype[$table_gtab[$bzm]]["datatype"][1] != 47
                        AND $result_fieldtype[$table_gtab[$bzm]]["datatype"][1] != 39
                        AND $result_fieldtype[$table_gtab[$bzm]]["datatype"][1] != 45
                        AND $result_fieldtype[$table_gtab[$bzm]]["datatype"][1] != 44
                        AND $result_fieldtype[$table_gtab[$bzm]]["datatype"][1] != 33
                        AND $result_fieldtype[$table_gtab[$bzm]]["parse_type"][1] != 3
                        AND $result_fieldtype[$table_gtab[$bzm]]["field_type"][1] != 11):

                        ?>
                        <div class="mb-3 row">
                            <label class="col-sm-4 col-form-label col-form-label-sm">RegEx</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control form-control-sm" value="<?=str_replace('"','\"',$result_fieldtype[$table_gtab[$bzm]]["regex"][1])?>" onchange="document.form2.val.value=this.value+' ';ajaxEditField('<?=$fieldid?>','regex')">
                                <small class="form-text text-muted"><?=$lang[3179]?></small>
                            </div>
                        </div>

                    <?php endif; ?>
                    <?php endif; ?>




                    <?php // BOOLEAN Format 
                    if($result_fieldtype[$table_gtab[$bzm]]["fieldtype"][1] == 10):
                        ?>
                        <div class="mb-3 row">
                            <label class="col-sm-4 col-form-label col-form-label-sm"><?=$lang[2998]?></label>
                            <div class="col-sm-8">
                                <select class="form-select form-select-sm" onchange="document.form2.val.value=this.value;ajaxEditField('<?=$fieldid?>','boolformat')">
                                    <option value=" " <?=(empty($result_fieldtype[$table_gtab[$bzm]]["listing_viewmode"][1]))?'selected':''?>>Checkbox</option>
                                    <option value="2" <?=($result_fieldtype[$table_gtab[$bzm]]["listing_viewmode"][1] == 2)?'selected':''?>>Select</option>
                                    <option value="1" <?=($result_fieldtype[$table_gtab[$bzm]]["listing_viewmode"][1] == 1)?'selected':''?>>Radio</option>
                                    <option value="3" <?=($result_fieldtype[$table_gtab[$bzm]]["listing_viewmode"][1] == 3)?'selected':''?>>Radio reversed</option>
                                </select>
                                
                                <small class="form-text text-muted"><?=$lang[2999]?></small>
                            </div>
                        </div>
                    <?php endif; ?>


                    <?php // NFORMAT
                    if($result_fieldtype[$table_gtab[$bzm]]["fieldtype"][1] == 5 AND $result_fieldtype[$table_gtab[$bzm]]["datatype"][1] != 22 AND $result_fieldtype[$table_gtab[$bzm]]["datatype"][1] != 44):
                        ?>

                        <div class="mb-3 row">
                            <label class="col-sm-4 col-form-label col-form-label-sm"><?=$lang[1880]?></label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control form-control-sm" value="<?=$result_fieldtype[$table_gtab[$bzm]]['format'][1]?>" onchange="document.form2.val.value=this.value;ajaxEditField('<?=$fieldid?>','nformat')">
                                <small class="form-text text-muted"><?=$lang[2596]?></small>
                            </div>
                        </div>


                        <div class="mb-3 row">
                            <label class="col-sm-4 col-form-label col-form-label-sm"><?=$lang[2587]?></label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control form-control-sm" value="<?=$result_fieldtype[$table_gtab[$bzm]]['potency'][1]?>" onchange="document.form2.val.value=this.value;ajaxEditField('<?=$fieldid?>','potency')">
                                <small class="form-text text-muted"><?=$lang[2597]?></small>
                            </div>
                        </div>
                    
                    
                    <?php endif; ?>



                    <?php // Timestamp Format
                    if($result_fieldtype[$table_gtab[$bzm]]["fieldtype"][1] == 2 OR $result_fieldtype[$table_gtab[$bzm]]["fieldtype"][1] == 15 OR $result_fieldtype[$table_gtab[$bzm]]["fieldtype"][1] == 24):
                        ?>

                        <div class="mb-3 row">
                            <label class="col-sm-4 col-form-label col-form-label-sm"><?=$lang[2576]?></label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control form-control-sm" value="<?=$result_fieldtype[$table_gtab[$bzm]]['format'][1]?>" onchange="document.form2.val.value=this.value;ajaxEditField('<?=$fieldid?>','nformat')">

                                <?php if($result_fieldtype[$table_gtab[$bzm]]["datatype"][1] == 11 OR $result_fieldtype[$table_gtab[$bzm]]["fieldtype"][1] == 15): ?>
                                <select class="form-select form-select-sm" onchange="document.form2.val.value=this.value;ajaxEditField('<?=$fieldid?>','datetime')">
                                    <option value="1" <?=($result_fieldtype[$table_gtab[$bzm]]['datetime'][1] == 1)?'selected':''?>><?=$lang[197]?></option>
                                    <option value="4" <?=($result_fieldtype[$table_gtab[$bzm]]['datetime'][1] == 4)?'selected':''?>><?=$lang[1723]?></option>
                                    <option value="0" <?=($result_fieldtype[$table_gtab[$bzm]]['datetime'][1] == 0)?'selected':''?>><?=$lang[2702]?></option>
                                </select>
                                <?php endif; ?>

                                <?php if($result_fieldtype[$table_gtab[$bzm]]["fieldtype"][1] != 15): ?>
                                <select class="form-select form-select-sm" onchange="document.form2.val.value=this.value;ajaxEditField('<?=$fieldid?>','datepicker')">
                                    <option value="1" <?=($result_fieldtype[$table_gtab[$bzm]]['select_pool'][1] == 1)?'selected':''?>>UI datepicker</option>
                                    <option value="2" <?=($result_fieldtype[$table_gtab[$bzm]]['select_pool'][1] == 2)?'selected':''?>>HTML5 datepicker</option>
                                </select>
                                <?php endif; ?>
                                
                                <small class="form-text text-muted"><?=$lang[2602]?></small>
                            </div>
                        </div>
                    
                    <?php elseif($result_fieldtype[$table_gtab[$bzm]]["fieldtype"][1] == 7): ?>

                        <div class="mb-3 row">
                            <label class="col-sm-4 col-form-label col-form-label-sm"><?=$lang[2600]?></label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control form-control-sm" value="<?=$result_fieldtype[$table_gtab[$bzm]]['format'][1]?>" onchange="document.form2.val.value=this.value;ajaxEditField('<?=$fieldid?>','nformat')">
                                <small class="form-text text-muted"><?=$lang[2602]?></small>
                            </div>
                        </div>


                    <?php endif; ?>




                    <?php // relations / multiselect
                    if($result_fieldtype[$table_gtab[$bzm]]["fieldtype"][1] == 11 OR $result_fieldtype[$table_gtab[$bzm]]["datatype"][1] == 18 OR $result_fieldtype[$table_gtab[$bzm]]["datatype"][1] == 31 OR $result_fieldtype[$table_gtab[$bzm]]["datatype"][1] == 32):
                        ?>

                        <hr>
                    
                        <div class="mb-3 row">
                            <label class="col-sm-4 col-form-label col-form-label-sm"><?=$lang[2812]?></label>
                            <div class="col-sm-8">
                                <select class="form-select form-select-sm" onchange="document.form2.val.value=this.value;ajaxEditField('<?=$fieldid?>','relviewmode')">
                                    <option value="1" <?=($result_fieldtype[$table_gtab[$bzm]]['listing_viewmode'][1] == 1)?'selected':''?>><?=$lang[2814]?></option>
                                    <option value="2" <?=($result_fieldtype[$table_gtab[$bzm]]['listing_viewmode'][1] == 2)?'selected':''?>><?=$lang[2815]?></option>
                                    <?php if($result_fieldtype[$table_gtab[$bzm]]["datatype"][1] != 25): ?>
                                        <option value="3" <?=($result_fieldtype[$table_gtab[$bzm]]['listing_viewmode'][1] == 3)?'selected':''?>><?=$lang[2709]?></option>
                                        <option value="4" <?=($result_fieldtype[$table_gtab[$bzm]]['listing_viewmode'][1] == 4)?'selected':''?>><?=$lang[2913]?></option>
                                    <?php endif; ?>                                    
                                </select>
                                <small class="form-text text-muted"><?=$lang[2848]?></small>
                            </div>
                        </div>

                        <div class="mb-3 row">
                            <label class="col-sm-4 col-form-label col-form-label-sm"><?=$lang[2813]?></label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control form-control-sm" value="<?=$result_fieldtype[$table_gtab[$bzm]]['listing_cut'][1]?>" onchange="document.form2.val.value=this.value;ajaxEditField('<?=$fieldid?>','relviewcut')">
                                <small class="form-text text-muted"><?=$lang[2849]?></small>
                            </div>
                        </div>

                        <?php if($result_fieldtype[$table_gtab[$bzm]]["fieldtype"][1] == 11): ?>
                        <div class="mb-3 row">
                            <label class="col-sm-4 col-form-label col-form-label-sm"><?=$lang[2991]?></label>
                            <div class="col-sm-8">
                                <select class="form-select form-select-sm" onchange="document.form2.val.value=this.value;ajaxEditField('<?=$fieldid?>','triggercount')">
                                    <option value="1" <?=($result_fieldtype[$table_gtab[$bzm]]['triggercount'][1] == 1)?'selected':''?>><?=$lang[2994]?></option>
                                    <option value="2" <?=($result_fieldtype[$table_gtab[$bzm]]['triggercount'][1] == 2)?'selected':''?>><?=$lang[2993]?></option>
                                </select>
                                <small class="form-text text-muted"><?=$lang[2992]?></small>
                            </div>
                        </div>
                        <?php endif; ?>

                    <hr>
                    
                    <?php endif; ?>



                    <?php // convert
                    if(!$isview):
                        
                    if((($result_fieldtype[$table_gtab[$bzm]]["fieldtype"][1] == 1 OR $result_fieldtype[$table_gtab[$bzm]]["fieldtype"][1] == 5 OR $result_fieldtype[$table_gtab[$bzm]]["fieldtype"][1] == 4 OR $result_fieldtype[$table_gtab[$bzm]]["fieldtype"][1] == 3 OR $result_fieldtype[$table_gtab[$bzm]]["fieldtype"][1] == 18 OR $result_fieldtype[$table_gtab[$bzm]]["fieldtype"][1] == 11) AND $result_fieldtype[$table_gtab[$bzm]]["fieldtype"][1] < 100 AND $result_fieldtype[$table_gtab[$bzm]]["datatype"][1] != 22 AND !$result_fieldtype[$table_gtab[$bzm]]["argument_typ"][1]) || $result_fieldtype[$table_gtab[$bzm]]["fieldtype"][1] == 101):

                        if($result_fieldtype[$table_gtab[$bzm]]["datatype"][1] == 18 OR $result_fieldtype[$table_gtab[$bzm]]["datatype"][1] == 31 OR $result_fieldtype[$table_gtab[$bzm]]["datatype"][1] == 32){
                            $result_type_allow_convert = array(18,31,32);
                        }elseif($result_fieldtype[$table_gtab[$bzm]]["datatype"][1] == 24) {
                            $result_type_allow_convert = array(27);
                        }elseif($result_fieldtype[$table_gtab[$bzm]]["datatype"][1] == 27) {
                            $result_type_allow_convert = array(24);
                        }elseif($result_fieldtype[$table_gtab[$bzm]]["fieldtype"][1] == 101) {
                            $result_type_allow_convert = array(101,102);
                        }
                        else{
                            $result_type_allow_convert = array(16,17,33,19,21,1,2,3,4,5,6,7,8,9,10,29,28,12,14,31,18,30,32,39,42,44,45,50);
                        }
                        
                        ?>
                        <div class="mb-3 row">
                            <label class="col-sm-4 col-form-label col-form-label-sm"><?=$lang[930]?></label>
                            <div class="col-sm-8">
                                <select class="form-select form-select-sm" onchange="convert_field(this[this.selectedIndex].value,'<?=$result_fieldtype[$table_gtab[$bzm]]["field_id"][1]?>','<?=$result_fieldtype[$table_gtab[$bzm]]['field'][1]?>');">
                                    <option></option>
                                    <?php
                                    foreach($lmfieldtype['id'] as $type_key => $type_value){
                                        if(in_array($type_key,$result_type_allow_convert)){
                                            echo "<option value=\"$type_key\">".$lmfieldtype["description"][$type_key].'</option>';
                                        }
                                    }
                                    ?>
                                </select>
                                <small class="form-text text-muted"><?=$lang[2589]?></small>
                            </div>
                        </div>

                    <hr>
                    
                    <?php 
                    
                    endif;
                    endif; ?>

                    <?php
                    if($result_fieldtype[$table_gtab[$bzm]]["fieldtype"][1] < 100){
                    ?>

                    <div class="mb-3 row p-0">
                        <label class="col-sm-4 col-form-label col-form-label-sm"><?=$lang[2861]?></label>
                        <div class="col-sm-3">
                            <input type="text" class="form-control form-control-sm" value="<?=$result_fieldtype[$table_gtab[$bzm]]["row_size"][1]?>" onchange="document.form2.val.value=this.value;ajaxEditField('<?=$fieldid?>','row_size')">
                        </div>
                        <label class="col-sm-3 col-form-label col-form-label-sm"><?=$lang[2638]?></label>
                        <div class="col-sm-2">
                            <input type="checkbox" value="1" <?=($result_fieldtype[$table_gtab[$bzm]]['col_hide'][1] == 1)?'':'checked'?> onchange="document.form2.val.value=this.checked;ajaxEditField('<?=$fieldid?>','col_hide')">
                        </div>
                    </div>

                    <div class="mb-3 row p-0">
                        <div class="col-sm-4 col-form-label col-form-label-sm"></div>
                        <div class="col-sm-8">
                        <small class="form-text text-muted"><?=$lang[3125]?></small>
                        </div>
                    </div>

                    <?php
                    }
                    ?>

                    <?php //extension
                    if(!$result_fieldtype[$table_gtab[$bzm]]["domain_admin_default"][1] AND $ext_fk AND $result_fieldtype[$table_gtab[$bzm]]["fieldtype"][1] < 100 AND $result_fieldtype[$table_gtab[$bzm]]["datatype"][1] != 22 AND $result_fieldtype[$table_gtab[$bzm]]["fieldtype"][1] != 14 AND $result_fieldtype[$table_gtab[$bzm]]["fieldtype"][1] != 15 AND $result_fieldtype[$table_gtab[$bzm]]["fieldtype"][1] != 16): ?>
                        <div class="mb-3 row">
                            <label class="col-sm-4 col-form-label col-form-label-sm"><?=$lang[1986]?></label>
                            <div class="col-sm-8">
                                <select class="form-select form-select-sm" onchange="document.form2.val.value=this.value;ajaxEditField('<?=$fieldid?>','extend_value')">
                                    <option value=" "></option>
                                    <?php foreach ($ext_fk as $key => $value): ?>
                                    
                                    <option value="<?=$value?>" <?=($result_fieldtype[$table_gtab[$bzm]]['ext_type'][1] == $value)?'selected':''?>><?=$value?></option>
                                    
                                    <?php
                                    endforeach;
                                    ?>
                                </select>
                                <small class="form-text text-muted"><?=$lang[2590]?></small>
                            </div>
                        </div>
                    <?php endif; ?>


                    <?php //currency
                    if($result_fieldtype[$table_gtab[$bzm]]["datatype"][1] == 30): ?>
                        <div class="mb-3 row">
                            <label class="col-sm-4 col-form-label col-form-label-sm"><?=$lang[1883]?></label>
                            <div class="col-sm-8">
                                <select class="form-select form-select-sm" onchange="document.form2.val.value=this.value;ajaxEditField('<?=$fieldid?>','ncurrency')">
                                    <option value=" "></option>
                                    <?php
                                    asort($lmcurrency["currency"]);
                                    foreach ($lmcurrency["currency"] as $ckey => $cval): ?>

                                        <option value="<?=$lmcurrency['code'][$ckey]?>" <?=($result_fieldtype[$table_gtab[$bzm]]['currency'][1] == $lmcurrency["code"][$ckey])?'selected':''?>><?=$lmcurrency["currency"][$ckey]?></option>

                                    <?php
                                    endforeach;
                                    ?>
                                </select>
                                <small class="form-text text-muted"><?=$lang[2599]?></small>
                            </div>
                        </div>
                    <?php endif; ?>



                    <?php // Agregatfunktion
                    if($result_fieldtype[$table_gtab[$bzm]]["fieldtype"][1] == 5):
                        ?>
                        <div class="mb-3 row">
                            <label class="col-sm-4 col-form-label col-form-label-sm"><?=$lang[2731]?></label>
                            <div class="col-sm-8">
                                <?php
                                $agregat_fk = array(1=>'AVG',2=>'COUNT',3=>'MAX',4=>'MIN',5=>'SUM');
                                foreach ($agregat_fk as $key => $value):
                                    ?>
                                    <span class="me-2 text-center d-inline-block">
                                        <?=$value?><br>
                                        <input type="checkbox" value="1" <?=(in_array($key,$result_fieldtype[$table_gtab[$bzm]]["aggregate"][1]))?'checked':''?> onchange="document.form2.val.value=this.checked+'_<?=$key?>';ajaxEditField('<?=$fieldid?>','aggregate')">
                                    </span>
                                
                                <?php
                                endforeach;
                                ?>
                                <small class="form-text text-muted"><?=$lang[2732]?></small>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    
                    <hr>



                    <?php // View-Rule ?>
                    <div class="mb-3 row">
                        <label class="col-sm-4 col-form-label col-form-label-sm"><?=$lang[2505]?></label>
                        <div class="col-sm-8">
                            <textarea onchange="document.form2.val.value=this.value+' ';ajaxEditField('<?=$fieldid?>','view_rule')" rows="3" class="form-control form-control-sm"><?=$result_fieldtype[$table_gtab[$bzm]]['view_rule'][1]?></textarea>
                            <small class="form-text text-muted"><?=$lang[2591]?></small>
                        </div>
                    </div>


                    <?php // Edit-Rule 
                    if (!$isview):
                        if($result_fieldtype[$table_gtab[$bzm]]["fieldtype"][1] < 100 AND $result_fieldtype[$table_gtab[$bzm]]["argument_typ"][1] != 47 AND $result_fieldtype[$table_gtab[$bzm]]["fieldtype"][1] != 14 AND $result_fieldtype[$table_gtab[$bzm]]["fieldtype"][1] != 15 AND $result_fieldtype[$table_gtab[$bzm]]["fieldtype"][1] != 20 AND $result_fieldtype[$table_gtab[$bzm]]["datatype"][1] != 47):
                    ?>
                    <div class="mb-3 row">
                        <label class="col-sm-4 col-form-label col-form-label-sm"><?=$lang[2570]?></label>
                        <div class="col-sm-8">
                            <textarea onchange="document.form2.val.value=this.value+' ';ajaxEditField('<?=$fieldid?>','edit_rule')" rows="3" class="form-control form-control-sm"><?=$result_fieldtype[$table_gtab[$bzm]]['edit_rule'][1]?></textarea>
                            <small class="form-text text-muted"><?=$lang[2592]?></small>
                        </div>
                    </div>
                    <?php
                        endif;
                        endif; ?>


                    <?php // trigger
                    if (!$isview && $gtrigger[$bzm]):
                        if($LINK[226] AND $result_fieldtype[$table_gtab[$bzm]]["fieldtype"][1] < 100 AND $result_fieldtype[$table_gtab[$bzm]]["datatype"][1] != 22):
                    ?>
                    <div class="mb-3 row">
                        <label class="col-sm-4 col-form-label col-form-label-sm"><?=$lang[2451]?></label>
                        <div class="col-sm-8">

                            <select style="width:100%" class="form-select form-select-sm select2-selection" data-fieldid="<?=$fieldid?>" multiple="multiple" id="field_trigger">
                                <option value=""></option>
                            <?php
                            foreach($gtrigger[$bzm]["id"] as $trid => $trval):
                                if(in_array($trid,$result_fieldtype[$table_gtab[$bzm]]["trigger"][1])){$SELECTED = "SELECTED";}else{$SELECTED = "";} ?>
                                <option VALUE="<?=$trid?>" <?=$SELECTED?>><?=$gtrigger[$bzm]["trigger_name"][$trid]?> (<?=$gtrigger[$bzm]["type"][$trid]?>)</option>
                                <?php endforeach; ?>
                            </select>

                            <small class="form-text text-muted"><?=$lang[2825]?></small>
                        </div>
                    </div>
                    <?php
                        endif;
                        endif; ?>



                    <?php // Sparten-Event
                    if($result_fieldtype[$table_gtab[$bzm]]["fieldtype"][1] == 100):
                     ?>
                    <div class="mb-3 row">
                        <label class="col-sm-4 col-form-label col-form-label-sm">Click-Event</label>
                        <div class="col-sm-8">
                            <textarea onchange="document.form2.val.value=this.value+' ';ajaxEditField('<?=$fieldid?>','options')" rows="3" class="form-control form-control-sm"><?=$result_fieldtype[$table_gtab[$bzm]]['options'][1]?></textarea>
                            <small class="form-text text-muted"><?=$lang[2712]?></small>
                        </div>
                    </div>
                    <?php endif; ?>                    
                    
                    
                </div>
                <div class="col-6">


                    <?php // Bezeichner
                    if(!$result_fieldtype[$table_gtab[$bzm]]["domain_admin_default"][1] AND $result_fieldtype[$table_gtab[$bzm]]["fieldtype"][1] != 100 AND $result_fieldtype[$table_gtab[$bzm]]["datatype"][$bzm1] != 31 AND $result_fieldtype[$table_gtab[$bzm]]["datatype"][$bzm1] != 18 AND $result_fieldtype[$table_gtab[$bzm]]["fieldtype"][1] != 6 AND $result_fieldtype[$table_gtab[$bzm]]["fieldtype"][1] != 10 AND $result_fieldtype[$table_gtab[$bzm]]["fieldtype"][1] != 9 AND $result_fieldtype[$table_gtab[$bzm]]["fieldtype"][1] != 13 AND $result_fieldtype[$table_gtab[$bzm]]["fieldtype"][1] != 16):
                    ?>
                    <div class="mb-3 row">
                        <label class="col-sm-4 col-form-label col-form-label-sm"><?=$lang[2235]?></label>
                        <div class="col-sm-8">
                            <input type="checkbox" value="1" <?=($result_fieldtype[$table_gtab[$bzm]]['mainfield'][1] == 1)?'checked':''?> onchange="document.form2.val.value=this.checked;ajaxEditField('<?=$fieldid?>','mainfield')">
                            <small class="form-text text-muted"><?=$lang[2837]?></small>
                        </div>
                    </div>
                    <?php endif; ?>


                    <?php if(!$isview): ?>
                    
                        <?php // Index
                        if(!$result_fieldtype[$table_gtab[$bzm]]["domain_admin_default"][1] AND $result_fieldtype[$table_gtab[$bzm]]["fieldtype"][1] != 100 AND $result_fieldtype[$table_gtab[$bzm]]["fieldtype"][1] != 14 AND $result_fieldtype[$table_gtab[$bzm]]["fieldtype"][1] != 15 AND $result_fieldtype[$table_gtab[$bzm]]["fieldtype"][1] != 11 AND $result_fieldtype[$table_gtab[$bzm]]["fieldtype"][1] != 6 AND $result_fieldtype[$table_gtab[$bzm]]["datatype"][1] != 39 AND $result_fieldtype[$table_gtab[$bzm]]["datatype"][1] != 22 AND $result_fieldtype[$table_gtab[$bzm]]["fieldtype"][1] != 16):
                            ?>
                            <div class="mb-3 row">
                                <label class="col-sm-4 col-form-label col-form-label-sm"><?=$lang[1720]?></label>
                                <div class="col-sm-8">
                                    <input type="checkbox" value="1" <?=($result_fieldtype[$table_gtab[$bzm]]['unique'][1] == 1)?'disabled':''?> <?=($result_fieldtype[$table_gtab[$bzm]]['indexed'][1] == 1)?'checked':''?> onchange="document.form2.val.value=this.checked;ajaxEditField('<?=$fieldid?>','fieldindex')">
                                    <small class="form-text text-muted"><?=$lang[2838]?></small>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php // unique
                        if(!$result_fieldtype[$table_gtab[$bzm]]["domain_admin_default"][1] AND $result_fieldtype[$table_gtab[$bzm]]["fieldtype"][1] != 14 AND $result_fieldtype[$table_gtab[$bzm]]["fieldtype"][1] != 15 AND $result_fieldtype[$table_gtab[1]]["fieldtype"][1] != 100 AND $result_fieldtype[$table_gtab[$bzm]]["datatype"][1] != 12 AND $result_fieldtype[$table_gtab[$bzm]]["datatype"][1] != 14 AND $result_fieldtype[$table_gtab[$bzm]]["datatype"][1] != 18 AND $result_fieldtype[$table_gtab[$bzm]]["fieldtype"][1] != 10 AND $result_fieldtype[$table_gtab[$bzm]]["fieldtype"][1] != 9 AND $result_fieldtype[$table_gtab[$bzm]]["fieldtype"][1] != 13 AND $result_fieldtype[$table_gtab[$bzm]]["fieldtype"][1] != 3 AND $result_fieldtype[$table_gtab[$bzm]]["fieldtype"][1] != 16 AND $result_fieldtype[$table_gtab[$bzm]]["fieldtype"][1] != 19
                            AND !($result_fieldtype[$table_gtab[$bzm]]["datatype"][1] == 25 AND $result_fieldtype[$table_gtab[$bzm]]["verkntabletype"][1] == 2)):
                            ?>
                            <div class="mb-3 row">
                                <label class="col-sm-4 col-form-label col-form-label-sm"><?=$lang[927]?></label>
                                <div class="col-sm-8">
                                    <input type="checkbox" value="1" <?=($result_fieldtype[$table_gtab[$bzm]]['indexed'][1] == 1)?'disabled':''?> <?=($result_fieldtype[$table_gtab[$bzm]]['unique'][1] == 1)?'checked':''?> onchange="document.form2.val.value=this.checked;ajaxEditField('<?=$fieldid?>','uniquefield')">
                                    <small class="form-text text-muted"><?=$lang[2839]?></small>
                                </div>
                            </div>
                        <?php endif; ?>

                    
                        <?php if($result_fieldtype[$table_gtab[$bzm]]["fieldtype"][1] != 16 AND $result_fieldtype[$table_gtab[$bzm]]["fieldtype"][1] != 6): ?>
                           <hr>
                        <?php endif; ?>


                        <?php // select
                        if($result_fieldtype[$table_gtab[$bzm]]["fieldtype"][1] < 100 AND $result_fieldtype[$table_gtab[$bzm]]["fieldtype"][1] != 16 AND $result_fieldtype[$table_gtab[$bzm]]["fieldtype"][1] != 6):
                            ?>
                            <div class="mb-3 row">
                                <label class="col-sm-4 col-form-label col-form-label-sm"><?=$lang[932]?></label>
                                <div class="col-sm-8">
                                    <input type="checkbox" value="1" <?=($result_fieldtype[$table_gtab[$bzm]]['artleiste'][1] == 1)?'checked':''?> onchange="document.form2.val.value=this.checked;ajaxEditField('<?=$fieldid?>','artleiste')">
                                    <small class="form-text text-muted"><?=($result_fieldtype[$table_gtab[$bzm]]["fieldtype"][1] == 11)?$lang[2846]:$lang[2841]?></small>
                                </div>
                            </div>
                        <?php endif; ?>


                        <?php // ajax search
                        if($result_fieldtype[$table_gtab[$bzm]]["fieldtype"][1] == 11  OR $result_fieldtype[$table_gtab[$bzm]]["datatype"][1] == 12 OR $result_fieldtype[$table_gtab[$bzm]]["datatype"][1] == 32 AND $result_fieldtype[$table_gtab[$bzm]]["fieldtype"][1] != 16):
                            ?>
                            <div class="mb-3 row">
                                <label class="col-sm-4 col-form-label col-form-label-sm"><?=$lang[2639]?></label>
                                <div class="col-sm-8">
                                    <input type="checkbox" value="1" <?=($result_fieldtype[$table_gtab[$bzm]]['dynsearch'][1] == 1)?'checked':''?> onchange="document.form2.val.value=this.checked;ajaxEditField('<?=$fieldid?>','dynsearch')">
                                    <small class="form-text text-muted">
                                        <?php
                                        if($result_fieldtype[$table_gtab[$bzm]]["fieldtype"][1] == 4){
                                            echo $lang[2845];
                                        }elseif($result_fieldtype[$table_gtab[$bzm]]["fieldtype"][1] == 11){
                                            echo $lang[2847];
                                        }else{
                                            echo $lang[2842];
                                        }
                                        ?>
                                    </small>
                                </div>
                            </div>
                        <?php endif; ?>


                        <?php // Index
                        if($result_fieldtype[$table_gtab[$bzm]]["fieldtype"][1] <= 100 AND $result_fieldtype[$table_gtab[$bzm]]["fieldtype"][1] != 20 AND $result_fieldtype[$table_gtab[$bzm]]["fieldtype"][1] != 14 AND $result_fieldtype[$table_gtab[$bzm]]["fieldtype"][1] != 15 AND $result_fieldtype[$table_gtab[$bzm]]["fieldtype"][1] != 9 AND $result_fieldtype[$table_gtab[$bzm]]["fieldtype"][1] != 8 AND $result_fieldtype[$table_gtab[$bzm]]["fieldtype"][1] != 6 AND $result_fieldtype[$table_gtab[$bzm]]["fieldtype"][1] != 19 AND $result_fieldtype[$table_gtab[$bzm]]["fieldtype"][1] != 16):
                            ?>
                            <div class="mb-3 row">
                                <label class="col-sm-4 col-form-label col-form-label-sm"><?=$lang[2640]?></label>
                                <div class="col-sm-8">
                                    <input type="checkbox" value="1" <?=($result_fieldtype[$table_gtab[$bzm]]['ajaxsave'][1] == 1)?'checked':''?> onchange="document.form2.val.value=this.checked;ajaxEditField('<?=$fieldid?>','ajaxsave')">
                                    <small class="form-text text-muted"><?=$lang[2840]?></small>
                                </div>
                            </div>
                        <?php endif; ?>
                    

                    
                    <?php endif; ?>

                    <?php // detailsearch
                    if($result_fieldtype[$table_gtab[$bzm]]["fieldtype"][1] < 100 AND $result_fieldtype[$table_gtab[$bzm]]["fieldtype"][1] != 16 AND $result_fieldtype[$table_gtab[$bzm]]["fieldtype"][1] != 6):
                        ?>
                        <div class="mb-3 row">
                            <label class="col-sm-4 col-form-label col-form-label-sm"><?=$lang[101]?></label>
                            <div class="col-sm-8">
                                <input type="checkbox" value="1" <?=($result_fieldtype[$table_gtab[$bzm]]['detailsearch'][1] == 1)?'':'checked'?> onchange="document.form2.val.value=this.checked;ajaxEditField('<?=$fieldid?>','detailsearch')">
                                <small class="form-text text-muted"><?=$lang[3168]?></small>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php // quick search
                    if($result_fieldtype[$table_gtab[$bzm]]["fieldtype"][1] < 100 AND $result_fieldtype[$table_gtab[$bzm]]["fieldtype"][1] != 16 AND $result_fieldtype[$table_gtab[$bzm]]["fieldtype"][1] != 6):
                        ?>
                        <div class="mb-3 row">
                            <label class="col-sm-4 col-form-label col-form-label-sm"><?=$lang[2507]?></label>
                            <div class="col-sm-8">
                                <input type="checkbox" value="1" <?=($result_fieldtype[$table_gtab[$bzm]]['quicksearch'][1] == 1)?'checked':''?> onchange="document.form2.val.value=this.checked;ajaxEditField('<?=$fieldid?>','quicksearch')">
                                <small class="form-text text-muted"><?=$lang[2842]?></small>
                            </div>
                        </div>
                    <?php endif; ?>


                    <?php // full table search
                    if($result_fieldtype[$table_gtab[$bzm]]["fieldtype"][1] < 100 AND $result_fieldtype[$table_gtab[$bzm]]["fieldtype"][1] != 10 AND $result_fieldtype[$table_gtab[$bzm]]["datatype"][1] != 33 AND $result_fieldtype[$table_gtab[$bzm]]["fieldtype"][1] != 6 AND $result_fieldtype[$table_gtab[$bzm]]["fieldtype"][1] != 20):
                        ?>
                        <div class="mb-3 row">
                            <label class="col-sm-4 col-form-label col-form-label-sm"><?=$lang[2922]?></label>
                            <div class="col-sm-8">
                                <input type="checkbox" value="1" <?=($result_fieldtype[$table_gtab[$bzm]]['fullsearch'][1] == 1)?'checked':''?> onchange="document.form2.val.value=this.checked;ajaxEditField('<?=$fieldid?>','fullsearch')">
                                <small class="form-text text-muted"><?=$lang[2923]?></small>
                            </div>
                        </div>
                    <?php endif; ?>


                    <?php // Upload - show preview
                    if($result_fieldtype[$table_gtab[$bzm]]["fieldtype"][1] == 6):
                        ?>
                        <div class="mb-3 row">
                            <label class="col-sm-4 col-form-label col-form-label-sm"><?=$lang[1739]?></label>
                            <div class="col-sm-8">
                                <input type="checkbox" value="1" <?=($result_fieldtype[$table_gtab[$bzm]]['quicksearch'][1] == 1)?'checked':''?> onchange="document.form2.val.value=this.checked;ajaxEditField('<?=$fieldid?>','quicksearch')">
                                <small class="form-text text-muted"><?=$lang[997]?></small>
                            </div>
                        </div>
                    <?php endif; ?>


                    <?php // Groupable
                    if($result_fieldtype[$table_gtab[$bzm]]["fieldtype"][1] < 100 AND $result_fieldtype[$table_gtab[$bzm]]["fieldtype"][1] != 11 AND $result_fieldtype[$table_gtab[$bzm]]["fieldtype"][1] != 3 AND $result_fieldtype[$table_gtab[$bzm]]["datatype"][1] != 22 AND $result_fieldtype[$table_gtab[$bzm]]["fieldtype"][1] != 13 AND $result_fieldtype[$table_gtab[$bzm]]["datatype"][1] != 31 AND $result_fieldtype[$table_gtab[$bzm]]["datatype"][1] != 32 AND $result_fieldtype[$table_gtab[$bzm]]["datatype"][1] != 18  AND $result_fieldtype[$table_gtab[$bzm]]["datatype"][1] != 13 AND $result_fieldtype[$table_gtab[$bzm]]["fieldtype"][1] != 16):
                        ?>
                        <div class="mb-3 row">
                            <label class="col-sm-4 col-form-label col-form-label-sm"><?=$lang[1459]?></label>
                            <div class="col-sm-8">
                                <input type="checkbox" value="1" <?=($result_fieldtype[$table_gtab[$bzm]]['groupable'][1] == 1)?'checked':''?> onchange="document.form2.val.value=this.checked;ajaxEditField('<?=$fieldid?>','groupable')">
                                <small class="form-text text-muted"><?=$lang[2843]?></small>
                            </div>
                        </div>
                    <?php endif; ?>


                    <?php if(!$isview): ?>
                    
                        <?php // coll_replace
                        if($result_fieldtype[$table_gtab[$bzm]]["fieldtype"][1] != 100 AND $result_fieldtype[$table_gtab[$bzm]]["datatype"][1] != 22 AND !$result_fieldtype[$table_gtab[$bzm]]["argument"][1] AND ($result_fieldtype[$table_gtab[$bzm]]["fieldtype"][1] == 4 OR $result_fieldtype[$table_gtab[$bzm]]["fieldtype"][1] == 5 OR $result_fieldtype[$table_gtab[$bzm]]["fieldtype"][1] == 1 OR $result_fieldtype[$table_gtab[$bzm]]["fieldtype"][1] == 2 OR $result_fieldtype[$table_gtab[$bzm]]["fieldtype"][1] == 10 OR $result_fieldtype[$table_gtab[$bzm]]["fieldtype"][1] == 21 OR $result_fieldtype[$table_gtab[$bzm]]["fieldtype"][1] == 18)):
                            ?>
                            <div class="mb-3 row">
                                <label class="col-sm-4 col-form-label col-form-label-sm"><?=$lang[2672]?></label>
                                <div class="col-sm-8">
                                    <input type="checkbox" value="1" <?=($result_fieldtype[$table_gtab[$bzm]]['collreplace'][1] == 1)?'checked':''?> onchange="document.form2.val.value=this.checked;ajaxEditField('<?=$fieldid?>','collreplace')">
                                    <small class="form-text text-muted"><?=$lang[2844]?></small>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php // long list edit
                        if($result_fieldtype[$table_gtab[$bzm]]["fieldtype"][1] == 3):
                            ?>
                            <div class="mb-3 row">
                                <label class="col-sm-4 col-form-label col-form-label-sm"><?=$lang[1879]?></label>
                                <div class="col-sm-8">
                                    <input type="checkbox" value="1" <?=($result_fieldtype[$table_gtab[$bzm]]['argument_edit'][1] == 1)?'checked':''?> onchange="document.form2.val.value=this.checked;ajaxEditField('<?=$fieldid?>','argument_edit')">
                                    <small class="form-text text-muted"><?=$lang[3050]?></small>
                                </div>
                            </div>
                        <?php endif; ?>


                        <?php // multi language
                        if($result_fieldtype[$table_gtab[$bzm]]["datatype"][1] == 1 OR $result_fieldtype[$table_gtab[$bzm]]["fieldtype"][1] == 3 OR $result_fieldtype[$table_gtab[$bzm]]["fieldtype"][1] == 4):
                            ?>
                            <div class="mb-3 row">
                                <label class="col-sm-4 col-form-label col-form-label-sm"><?=$lang[2895]?></label>
                                <div class="col-sm-8">
                                    <input type="checkbox" value="1" <?=($result_fieldtype[$table_gtab[$bzm]]['multilang'][1] == 1)?'checked':''?> onchange="document.form2.val.value=this.checked;ajaxEditField('<?=$fieldid?>','multilang')">
                                    <small class="form-text text-muted"><?=$lang[2896]?></small>
                                </div>
                            </div>
                        <?php endif; ?>


                        <?php // searchversion
                        if($result_fieldtype[$table_gtab[$bzm]]["fieldtype"][1] < 100 AND ($tab_versioning[$bzm] OR $table_validity[$bzm]) ):
                            ?>
                            <div class="mb-3 row">
                                <label class="col-sm-4 col-form-label col-form-label-sm"><?=$lang[3045]?></label>
                                <div class="col-sm-8">
                                    <input type="checkbox" value="1" <?=($result_fieldtype[$table_gtab[$bzm]]['searchversion'][1] == 1)?'checked':''?> onchange="document.form2.val.value=this.checked;ajaxEditField('<?=$fieldid?>','searchversion')">
                                    <small class="form-text text-muted"><?=$lang[3046]?></small>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php // encrypted
                        if($result_fieldtype[$table_gtab[$bzm]]['datatype'][1] == 55):
                            ?>
                            <div class="mb-3 row">
                                <label class="col-sm-4 col-form-label col-form-label-sm">Encryption mode</label>
                                <div class="col-sm-8">
                                    <select class="form-select form-select-sm" onchange="document.form2.val.value=this.value;ajaxEditField('<?=$fieldid?>','encryption_mode')">
                                        <option value="0" <?=(empty($result_fieldtype[$table_gtab[$bzm]]['encryption_mode'][1]))?'selected':''?>>at rest</option>
                                        <option value="1" <?=($result_fieldtype[$table_gtab[$bzm]]['encryption_mode'][1] == 1)?'selected':''?>>on demand</option>
                                    </select>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php // md5 checksum
                        #if($result_fieldtype[$table_gtab[$bzm]]["sync_slave"][1] OR $result_fieldtype[$table_gtab[$bzm]]["sync_master"][1]){
                            ?>
                            <div class="mb-3 row">
                                <label class="col-sm-4 col-form-label col-form-label-sm"><?=$lang[3156]?></label>
                                <div class="col-sm-8">
                                    <input type="checkbox" value="1" <?=($result_fieldtype[$table_gtab[$bzm]]['checksum'][1] == 1)?'checked':''?> onchange="document.form2.val.value=this.checked;ajaxEditField('<?=$fieldid?>','checksum')">
                                    <small class="form-text text-muted"><?=$lang[3154]?></small>
                                </div>
                            </div>
                        <?php #}?>

                        <?php // in sync template
                        if($result_fieldtype[$table_gtab[$bzm]]["sync_slave"][1] OR $result_fieldtype[$table_gtab[$bzm]]["sync_master"][1]){
                            ?>
                            <div class="mb-3 row">
                                <label class="col-sm-4 col-form-label col-form-label-sm"><?=$lang[2914]?></label>
                                <div class="col-sm-8">
                                    <?php
                                    if($result_fieldtype[$table_gtab[$bzm]]["sync_master"][1]){
                                        echo '<div>Master: '.implode(', ',$result_fieldtype[$table_gtab[$bzm]]["sync_master"][1]).'</div>';
                                    }
                                    if($result_fieldtype[$table_gtab[$bzm]]["sync_slave"][1]){
                                        echo '<div>Slave: '.implode(', ',$result_fieldtype[$table_gtab[$bzm]]["sync_slave"][1]).'</div>';
                                    }
                                    ?>
                                </div>
                            </div>
                        <?php }?>

                    <?php endif; ?>


                    <?php // relation/grouping popup default
                    if($result_fieldtype[$table_gtab[$bzm]]["fieldtype"][1] == 11 OR $result_fieldtype[$table_gtab[$bzm]]["groupable"][1] == 1):
                        ?>
                        <div class="mb-3 row">
                            <label class="col-sm-4 col-form-label col-form-label-sm"><?=$lang[2918]?></label>
                            <div class="col-sm-8">
                                <input type="checkbox" value="1" <?=($result_fieldtype[$table_gtab[$bzm]]['popupdefault'][1] == 1)?'checked':''?> onchange="document.form2.val.value=this.checked;ajaxEditField('<?=$fieldid?>','popupdefault')">
                                <small class="form-text text-muted"><?=$lang[2919]?></small>
                            </div>
                        </div>
                    <?php endif; ?>

                    
                    <?php // Argument
                    if($result_fieldtype[$table_gtab[$bzm]]["argument_typ"][1]):
                        ?>
                    <hr>
                    <p class="mb-0 fw-bold col-form-label-sm"><?=$lmfieldtype["description"][$result_fieldtype[$table_gtab[$bzm]]["argument_typ"][1]]?></p>
                        <div class="row">
                            <label class="col-sm-4 col-form-label col-form-label-sm"><?=$lang[2593]?></label>
                            <div class="col-sm-8">
                                <span class="form-control-plaintext"><i onclick="newwin3('<?=$result_fieldtype[$table_gtab[$bzm]]["field_id"][1]?>','<?=$KEYID_gtab[$bzm]?>','<?=$bzm?>','<?=$result_fieldtype[$table_gtab[$bzm]]["argument_typ"][1]?>');" class="lmb-icon lmb-pencil cursor-pointer"></i></span>
                            </div>
                        </div>


                        <?php if($result_fieldtype[$table_gtab[$bzm]]["argument_typ"][1] == 15): ?>
                            <div class="mb-3 row">
                                <label class="col-sm-4 col-form-label col-form-label-sm"><?=$lang[1879]?></label>
                                <div class="col-sm-8">
                                    <input type="checkbox" value="1" <?=($result_fieldtype[$table_gtab[$bzm]]['argument_edit'][1] == 1)?'checked':''?> onchange="document.form2.val.value=this.checked;ajaxEditField('<?=$fieldid?>','argument_edit')">
                                </div>
                            </div>
                        <?php endif; ?>
                    
                    <?php endif; ?>


                    <?php // Attribut
                    if($result_fieldtype[$table_gtab[$bzm]]["fieldtype"][1] == 19):
                        ?>
                    <hr>
                        <div class="mb-3 row">
                            <label class="col-sm-4 col-form-label col-form-label-sm"><?=$lang[2594]?></label>
                            <div class="col-sm-8">
                                <span class="form-control-plaintext">
                                <?php
                                $pool = 0;
                                if($pool = $result_fieldtype[$table_gtab[$bzm]]["select_pool"][1]):
                                    $sqlquery = "SELECT NAME FROM LMB_ATTRIBUTE_P WHERE ID = ".$result_fieldtype[$table_gtab[$bzm]]["select_pool"][1];
                                    $rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);?>

                                    <a href="#" onclick="newwin('<?=$result_fieldtype[$table_gtab[$bzm]]["field_id"][1]?>','<?=$bzm?>','<?=$pool?>','LMB_ATTRIBUTE');"><?=htmlentities(lmbdb_result($rs, "NAME"),ENT_QUOTES,$umgvar["charset"])?></a>
                                
                                    <?php
                                endif;
                                ?>
                                <i onclick="newwin('<?=$result_fieldtype[$table_gtab[$bzm]]["field_id"][1]?>','<?=$bzm?>','<?=$pool?>','LMB_ATTRIBUTE');" class="lmb-icon lmb-pencil cursor-pointer"></i>
                                </span>
                            </div>
                        </div>
                    <?php endif; ?>



                    <?php // Selectauswahl
                    if($result_fieldtype[$table_gtab[$bzm]]["fieldtype"][1] == 4):
                        ?>
                    <hr>
                        <p class="mb-0 fw-bold col-form-label-sm"><?=$result_fieldtype[$table_gtab[$bzm]]["beschreibung_typ"][1]?></p>
                        <div class="row">
                            <label class="col-sm-4 col-form-label col-form-label-sm"><?=$lang[2594]?></label>
                            <div class="col-sm-8">
                               <span class="form-control-plaintext">
                                <?php  
                                $pool = 0;
                                if($pool = $result_fieldtype[$table_gtab[$bzm]]["select_pool"][1]):
                                    $sqlquery = "SELECT NAME FROM LMB_SELECT_P WHERE ID = ".$result_fieldtype[$table_gtab[$bzm]]["select_pool"][1];
                                    $rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);?>

                                    <a href="#" onclick="newwin('<?=$result_fieldtype[$table_gtab[$bzm]]["field_id"][1]?>','<?=$bzm?>','<?=$pool?>','LMB_SELECT');"><?=htmlentities(lmbdb_result($rs, "NAME"),ENT_QUOTES,$umgvar["charset"])?></a>

                                <?php
                                endif;
                                ?>
                                   <i onclick="newwin('<?=$result_fieldtype[$table_gtab[$bzm]]["field_id"][1]?>','<?=$bzm?>','<?=$pool?>','LMB_SELECT');" class="lmb-icon lmb-pencil cursor-pointer"></i>
                                </span>
                            </div>
                        </div>


                        <?php if($result_fieldtype[$table_gtab[$bzm]]["datatype"][1] == 32): ?>
                        <div class="mb-3 row">
                            <label class="col-sm-4 col-form-label col-form-label-sm"><?=$lang[2595]?></label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control form-control-sm" value="<?=$result_fieldtype[$table_gtab[$bzm]]['select_cut'][1]?>" onchange="document.form2.val.value=this.value;ajaxEditField('<?=$fieldid?>','select_cut')">
                            </div>
                        </div>
                    <?php endif; ?>


                        <div class="mb-3 row">
                            <label class="col-sm-4 col-form-label col-form-label-sm"><?=$lang[2795]?></label>
                            <div class="col-sm-8">
                                <textarea onchange="document.form2.val.value=this.value+' ';ajaxEditField('<?=$fieldid?>','options')" rows="3" class="form-control form-control-sm"><?=$result_fieldtype[$table_gtab[$bzm]]['options'][1]?></textarea>
                                <small class="form-text text-muted"><?=$lang[2857]?></small>
                            </div>
                        </div>

                        <?php if($result_fieldtype[$table_gtab[$bzm]]["datatype"][1] == 18 OR $result_fieldtype[$table_gtab[$bzm]]["datatype"][1] == 31 OR $result_fieldtype[$table_gtab[$bzm]]["datatype"][1] == 32): ?>
                        <div class="mb-3 row">
                            <label class="col-sm-4 col-form-label col-form-label-sm"><?=$lang[2761]?></label>
                            <div class="col-sm-8">
                                <div class="form-control-plaintext">
                                    <i class="lmb-icon lmb-refresh cursor-pointer" onclick="document.form2.val.value=1;ajaxEditField('<?=$fieldid?>','tablesync')"></i>
                                    <?php if($tablesync): ?>
                                     <i class="lmb-icon lmb-aktiv"></i>
                                    <?php endif; ?>
                                </div>
                                <small class="form-text text-muted"><?=$lang[2030]?></small>
                            </div>
                        </div>
                        <?php endif; ?>
                    

                    <?php endif; ?>




                    <?php // Long
                    if($result_fieldtype[$table_gtab[$bzm]]["datatype"][1] == 39):
                        ?>
                        <div class="mb-3 row">
                            <label class="col-sm-4 col-form-label col-form-label-sm"><?=$lang[1581]?></label>
                            <div class="col-sm-8">
                                <input type="checkbox" value="1" <?=($result_fieldtype[$table_gtab[$bzm]]['memoindex'][1] == 1)?'checked':''?> onchange="document.form2.val.value=this.checked;ajaxEditField('<?=$fieldid?>','memoindex')">
                            </div>
                        </div>
                        <div class="mb-3 row">
                            <label class="col-sm-4 col-form-label col-form-label-sm"><?=$lang[1885]?></label>
                            <div class="col-sm-8">
                                <input type="checkbox" value="1" <?=($result_fieldtype[$table_gtab[$bzm]]['wysiwyg'][1] == 1)?'checked':''?> onchange="document.form2.val.value=this.checked;ajaxEditField('<?=$fieldid?>','wysiwyg')">
                            </div>
                        </div>
                        <div class="mb-3 row">
                            <label class="col-sm-4 col-form-label col-form-label-sm"><?=$lang[2795]?></label>
                            <div class="col-sm-8">
                                <textarea onchange="document.form2.val.value=this.value+' ';ajaxEditField('<?=$fieldid?>','options')" rows="3" class="form-control form-control-sm"><?=$result_fieldtype[$table_gtab[$bzm]]['options'][1]?></textarea>
                                <small class="form-text text-muted"><?=$lang[2857]?></small>
                            </div>
                        </div>
                    
                    <?php endif; ?>


                    <?php // Long / Textblock
                    if($result_fieldtype[$table_gtab[$bzm]]["datatype"][1] == 39 OR $result_fieldtype[$table_gtab[$bzm]]["datatype"][1] == 10):
                        ?>
                        <div class="mb-3 row">
                            <label class="col-sm-4 col-form-label col-form-label-sm"><?=$lang[2817]?></label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control form-control-sm" value="<?=$result_fieldtype[$table_gtab[$bzm]]['select_pool'][1]?>" onchange="document.form2.val.value=this.value;ajaxEditField('<?=$fieldid?>','textblocksize')">
                            </div>
                        </div>
                    <?php endif; ?>


                    <?php // Grouping
                    if($result_fieldtype[$table_gtab[$bzm]]["fieldtype"][1] == 101):
                        ?>
                        <div class="mb-3 row">
                            <label class="col-sm-4 col-form-label col-form-label-sm"><?=$result_fieldtype[$table_gtab[$bzm]]["beschreibung_typ"][1]?></label>
                            <div class="col-sm-8">
                                <span class="form-control-plaintext">
                                    <a href="#" onclick="newwin7('<?=$result_fieldtype[$table_gtab[$bzm]]["field_id"][1]?>','<?=$bzm?>');"><?=$lang[2593]?></a>
                                    <?php if($result_fieldtype[$table_gtab[$bzm]]["genlink"][1]){echo "Link";} ?>
                                </span>
                            </div>
                        </div>
                    <?php endif; ?>


                    <?php // User / Group List
                    if($result_fieldtype[$table_gtab[$bzm]]["fieldtype"][1] == 16):
                        ?>
                    <hr>
                        <div class="mb-3 row">
                            <label class="col-sm-4 col-form-label col-form-label-sm"><?=$lang[2595]?></label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control form-control-sm" value="<?=$result_fieldtype[$table_gtab[$bzm]]['select_cut'][1]?>" onchange="document.form2.val.value=this.value;ajaxEditField('<?=$fieldid?>','select_cut')">
                            </div>
                        </div>
                    <?php endif; ?>



                    <?php // relations
                    if($result_fieldtype[$table_gtab[$bzm]]["fieldtype"][1] == 11):
                        ?>
                        <hr>
                        <p class="mb-0 fw-bold col-form-label-sm"><?=$result_fieldtype[$table_gtab[$bzm]]["beschreibung_typ"][1]?></p>


                        <?php // target of relation ?>
                        <div class="row">
                            <label class="col-sm-4 col-form-label col-form-label-sm"><?=$lang[1460]?></label>
                            <div class="col-sm-8">
                                <span class="form-control-plaintext">
                                    <?php
                                    $verknTabid = $result_fieldtype[$table_gtab[$bzm]]["verkntabid"][1];
                                    $verknFieldid = $result_fieldtype[$table_gtab[$bzm]]["verknfieldid"][1];
                                    $warn = '';
                                    if (!$verknTabid or !$verknFieldid) {
                                        $warn = 'text-danger';
                                    }
                                    
                                    $reloutput = '?';
                                    if($verknTabid){
                                        $sqlquery = "SELECT BESCHREIBUNG FROM LMB_CONF_TABLES WHERE TAB_ID = ".$verknTabid;
                                        $rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
                                        $reloutput .= "<a onclick=\"document.location.href='main_admin.php?&action=setup_gtab_ftype&tab_group=$tab_group&atid=$verknTabid'\">".$lang[lmbdb_result($rs, "BESCHREIBUNG")]."</a> | ";

                                        if($verknFieldid){
                                            $sqlquery = "SELECT SPELLING FROM LMB_CONF_FIELDS WHERE TAB_ID = ".$verknTabid." AND FIELD_ID = ".$verknFieldid;
                                            $rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
                                            $reloutput .= $lang[lmbdb_result($rs, "SPELLING")];
                                        } else {
                                            $reloutput .= '?';
                                        }
                                    }

                                    if($result_fieldtype[$table_gtab[$bzm]]["verkntabletype"][1] == 2){
                                        $reloutput .= '<span class="text-primary"> recursive</span>';
                                    }
                                    
                                    echo $reloutput;
                                    ?>
                                </span>
                            </div>
                        </div>




                        <?php // relation table ?>
                        <div class="mb-3 row">
                            <label class="col-sm-4 col-form-label col-form-label-sm"><?=$lang[2604]?></label>
                            <div class="col-sm-8">
                                <span class="form-control-plaintext">
                                    <?=$result_fieldtype[$table_gtab[$bzm]]["verkntab"][1]?>
                                    <?php if($result_fieldtype[$table_gtab[$bzm]]["verkntabletype"][1] == 2): ?>
                                        <span class="text-primary"> view</span>
                                    <?php endif; ?>
                                </span>
                            </div>
                        </div>


                        <?php // configure ?>
                        <div class="mb-3 row">
                            <label class="col-sm-4 col-form-label col-form-label-sm"><?=$lang[2593]?></label>
                            <div class="col-sm-8">
                                <span class="form-control-plaintext <?=$warn?>">
                                    <i class="lmb-icon-cus lmb-rel-edit cursor-pointer" title="<?=$lang[1301]?>" onlick="newwin5('<?=$result_fieldtype[$table_gtab[$bzm]]["field_id"][1]?>','<?=$KEYID_gtab[$bzm]?>','<?=$result_fieldtype[$table_gtab[$bzm]]["verkntabid"][1]?>')"></i>
                                </span>
                            </div>
                        </div>

                        <?php // rebuild temp ?>
                        <div class="mb-3 row">
                            <label class="col-sm-4 col-form-label col-form-label-sm"><?=$lang[2761]?></label>
                            <div class="col-sm-8">
                                <div class="form-control-plaintext">
                                    <i class="lmb-icon lmb-refresh cursor-pointer" onclick="document.form2.val.value=1;ajaxEditField('<?=$fieldid?>','tablesync')"></i>
                                    <?php if($tablesync): ?>
                                        <i class="lmb-icon lmb-aktiv"></i>
                                    <?php endif; ?>
                                </div>
                                <small class="form-text text-muted"><?=$lang[2030]?></small>
                            </div>
                        </div>

                    

                    <?php endif; ?>



                    <?php // inheritance
                    if($result_fieldtype[$table_gtab[$bzm]]["inherit_tab"][1]):
                        ?>
                        <hr>
                        <p class="mb-0 fw-bold col-form-label-sm"><?=$lang[2611]?></p>


                        <div class="row">
                            <label class="col-sm-4 col-form-label col-form-label-sm"><?=$lang[164]?></label>
                            <div class="col-sm-8">
                                <span class="form-control-plaintext">
                                    <?=$gtab["desc"][$result_fieldtype[$table_gtab[$bzm]]["inherit_tab"][1]]?>
                                </span>
                            </div>
                        </div>

                        <div class="row">
                            <label class="col-sm-4 col-form-label col-form-label-sm"><?=$lang[168]?></label>
                            <div class="col-sm-8">
                                <span class="form-control-plaintext">
                                    <?=$gfield[$result_fieldtype[$table_gtab[$bzm]]["inherit_tab"][1]]["spelling"][$result_fieldtype[$table_gtab[$bzm]]["inherit_field"][1]]?>
                                </span>
                            </div>
                        </div>

                        <div class="mb-3 row">
                            <label class="col-sm-4 col-form-label col-form-label-sm"><?=$lang[561]?></label>
                            <div class="col-sm-8">
                                <select class="form-select form-select-sm" onchange="document.form2.val.value=this.value;ajaxEditField('<?=$fieldid?>','inherit_group')">
                                    <option value="null"></option>
                                    <?php
                                    for($i=1; $i<=10; $i++){
                                        if($result_fieldtype[$table_gtab[$bzm]]["inherit_group"][1] == $i){$selected = "selected";}else{$selected = "";}
                                        echo "<option value=\"$i\" $selected>$i</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>


                        <?php if($result_fieldtype[$table_gtab[$bzm]]["inherit_search"][1] == 1): ?>
                        <div class="mb-3 row">
                            <label class="col-sm-4 col-form-label col-form-label-sm"><?=$lang[2608]?></label>
                            <div class="col-sm-8">
                                <textarea onchange="document.form2.val.value=this.value+' ';ajaxEditField('<?=$fieldid?>','inherit_filter')" rows="3" class="form-control form-control-sm"><?=$result_fieldtype[$table_gtab[$bzm]]['inherit_filter'][1]?></textarea>
                                <small class="form-text text-muted"><?=$lang[2857]?></small>
                            </div>
                        </div>
                        <?php endif; ?>


                        <?php if($result_fieldtype[$table_gtab[$bzm]]["fieldtype"][1] == 3): ?>
                            <div class="mb-3 row">
                                <label class="col-sm-4 col-form-label col-form-label-sm"><?=$lang[2610]?></label>
                                <div class="col-sm-8">
                                    <input type="checkbox" value="1" <?=($result_fieldtype[$table_gtab[$bzm]]['inherit_eval'][1] == 1)?'checked':''?> onchange="document.form2.val.value=this.checked;ajaxEditField('<?=$fieldid?>','inherit_eval')">
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if($result_fieldtype[$table_gtab[$bzm]]["fieldtype"][1] == 1 OR $result_fieldtype[$table_gtab[$bzm]]["fieldtype"][1] == 5): ?>
                        <div class="mb-3 row">
                            <label class="col-sm-4 col-form-label col-form-label-sm"><?=$lang[2609]?></label>
                            <div class="col-sm-8">
                                <input type="checkbox" value="1" <?=($result_fieldtype[$table_gtab[$bzm]]['inherit_search'][1] == 1)?'checked':''?> onchange="document.form2.val.value=this.checked;ajaxEditField('<?=$fieldid?>','inherit_search')">
                            </div>
                        </div>
                        <?php endif; ?>

                        <div class="mb-3 row">
                            <label class="col-sm-4 col-form-label col-form-label-sm"><?=$lang[1879]?></label>
                            <div class="col-sm-8">
                                <input type="checkbox" value="1" <?=($result_fieldtype[$table_gtab[$bzm]]['argument_edit'][1] == 1)?'checked':''?> onchange="document.form2.val.value=this.checked;ajaxEditField('<?=$fieldid?>','argument_edit')">
                            </div>
                        </div>



                    <?php endif; ?>


                    
                    <?php //check dependencies
                    $depviews = lmb_checkViewDependency($gtab["table"][$gtabid],$result_fieldtype[$table_gtab[$bzm]]["field"][1]);
                    if(is_array($depviews)):
                        ?>
                        <div class="mb-3 row">
                            <label class="col-sm-4 col-form-label col-form-label-sm"><?=$lang[2912]?></label>
                            <div class="col-sm-8">
                                <input type="checkbox" onclick="if(this.checked){document.form1.solve_dependency.value=1;document.form2.solve_dependency.value=1;}else{document.form1.solve_dependency.value='';document.form2.solve_dependency.value='';};">
                                <small class="form-text text-muted"><?=$lang[2911]?></small>
                                <?php
                                echo '- '.implode('<br>- ',$depviews);
                                ?>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                </div>
            </div>
            

        </div>
        <div class="tab-pane" id="info" role="tabpanel">
            
            <table class="table table-sm table-striped table-hover">
                <tr><td colspan="2"><hr></td></tr>
                <?php
                if($rs = dbf_5(array($DBA["DBSCHEMA"],$table_gtab[$bzm],dbf_4($result_fieldtype[$table_gtab[$bzm]]["field"][1]),1))):

                    while (lmbdb_fetch_row($rs)):
                        
                        $jml = lmbdb_num_fields($rs);

                        for($i=1;$i<=$jml;$i++):
                        ?>

                        <tr>
                            <th><?=lmbdb_field_name($rs,$i)?></th>
                            <td><?=lmbdb_result($rs,$i)?></td>
                        </tr>

                <?php
                        endfor;

                    endwhile;
                endif;
                ?>
            </table>

        </div>


        <div class="tab-pane" id="grouprights" role="tabpanel">
            
            <table class="table table-sm table-striped table-hover">
            <tr><td colspan="9"><hr></td></tr>
            <tr><td tyle="width:90%"></td>
            <td style="width:25px"><i class="lmb-icon lmb-eye"></i></td>
            <td style="width:25px"><i class="lmb-icon lmb-pencil"></i></td>
            <td style="width:25px"><i class="lmb-icon lmb-copy"></i></td>
            <td style="width:25px"><i class="lmb-icon lmb-exclamation"></i></td>
            <td style="width:40px"><i class="fa-solid fa-filter"></i>v</td>
            <td style="width:40px"></i><i class="fa-solid fa-filter"></i>e</td>
            <td style="width:25px">Default</td>
            </tr>
	
            <?php
            global $groupdat;
            foreach($result_fieldtype[$table_gtab[$bzm]]["group_rights"][1] as $key => $value){
            ?>
                <tr>
                <td><?=$groupdat["name"][$value[0]]?></td>
                <td><?php echo ($value[1] ? '<i class="lmb-icon lmb-check-alt"></i>' : '<input type="checkbox" class="checkb" disabled>') ?></td>
                <td><?php echo ($value[2] ? '<i class="lmb-icon lmb-check-alt"></i>' : '<input type="checkbox" class="checkb" disabled>') ?></td>
                <td><?php echo ($value[3] ? '<i class="lmb-icon lmb-check-alt"></i>' : '<input type="checkbox" class="checkb" disabled>') ?></td>
                <td><?php echo ($value[4] ? '<i class="lmb-icon lmb-check-alt"></i>' : '<input type="checkbox" class="checkb" disabled>') ?></td>
                <td><?php echo ($value[5] ? '<i class="lmb-icon lmb-check-alt" title="'.$value.'"></i>' : '') ?></td>
                <td><?php echo ($value[6] ? '<i class="lmb-icon lmb-check-alt" title="'.$value.'"></i>' : '') ?></td>
                <td><?php echo ($value[7] ? $value : '') ?></td>
                </tr>

            <?php } ?>
            </table>

        </div>


    </div>
