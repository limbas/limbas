<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */

use Limbas\lib\db\functions\Dbf;

?>
<FORM ACTION="main_admin.php" METHOD="post" name="form1">
    <input type="hidden" name="action" VALUE="setup_verkn_editor">
    <input type="hidden" name="tabid" VALUE="<?=$tabid?>">
    <input type="hidden" name="fieldid" VALUE="<?=$fieldid?>">
    <input type="hidden" name="drop_viev">
    <input type="hidden" name="set_verknfieldid">
    <input type="hidden" name="category" value="edit">
    <input type="hidden" name="relationtree">
    <input type="hidden" name="change_vparams">
    <input type="hidden" name="change_refint">
    <input type="hidden" name="change_multiralation">
    <input type="hidden" name="change_uniqueralation">


                <?php if(!$rfield['verkntabid'] AND $rfield['datatype'] == 23){?>


                    <?php

                    $sqlquery = "SELECT DISTINCT LMB_CONF_FIELDS.FIELD_ID, LMB_CONF_FIELDS.FIELD_NAME,LMB_CONF_FIELDS.TAB_ID, LMB_CONF_TABLES.TABELLE 
                    FROM LMB_CONF_FIELDS,LMB_CONF_TABLES 
                    WHERE LMB_CONF_TABLES.TAB_ID = LMB_CONF_FIELDS.TAB_ID AND LMB_CONF_FIELDS.VERKNTABID = $tabid AND LMB_CONF_FIELDS.FIELD_TYPE = 11 AND LMB_CONF_FIELDS.VERKNTABLETYPE = 1 ORDER BY LMB_CONF_FIELDS.TAB_ID";
                    
                    $rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
                    while(lmbdb_fetch_row($rs)) :

                        if(lmbdb_result($rs, "NAME") != $temp):

                            ?>

                        <?php endif; ?>

                        <div class="form-check mb-1">
                            <label class="form-check-label" <?=(lmbdb_result($rs, "TAB_ID") == $tabid)?'text-danger':''?>>
                                <input type="radio" name="new_backview_verkn" value="<?=lmbdb_result($rs, "TAB_ID").'_'.lmbdb_result($rs, "FIELD_ID")?>" OnChange="document.form1.submit();">
                                <?=Dbf::handleCaseSensitive(lmbdb_result($rs, "TABELLE"))?> (<?=Dbf::handleCaseSensitive(lmbdb_result($rs, "FIELD_NAME"))?>)
                            </label>
                        </div>

                        <?php
                        $temp = lmbdb_result($rs, "TAB_ID");
                    endwhile;

                    ?>

                <?php }
                elseif(!$rfield['verkntabid']){?>
                    
                    <p class="fw-bold"><?=$lang[1824]?></p>
                    
                        <?php

                        # back view relation filter
                        if($rfield['datatype'] == 23 AND $tabid){
                            $bsqu = " AND LMB_CONF_TABLES.TAB_ID IN(SELECT LMB_CONF_FIELDS.TAB_ID FROM LMB_CONF_FIELDS WHERE LMB_CONF_FIELDS.VERKNTABID = $tabid AND LMB_CONF_FIELDS.FIELD_TYPE = 11 AND LMB_CONF_FIELDS.VERKNTABLETYPE = 1)";
                        }

                        $sqlquery = "SELECT DISTINCT LMB_CONF_TABLES.TAB_ID,LMB_CONF_TABLES.TAB_GROUP,LMB_CONF_TABLES.TABELLE,LMB_CONF_TABLES.BESCHREIBUNG,LMB_CONF_GROUPS.NAME,LMB_CONF_GROUPS.ID 
                        FROM LMB_CONF_TABLES,LMB_CONF_GROUPS 
                        WHERE LMB_CONF_TABLES.TAB_GROUP = LMB_CONF_GROUPS.ID $bsqu 
                        ORDER BY LMB_CONF_GROUPS.ID";
                        $rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
                        while(lmbdb_fetch_row($rs)) {

                            // skip DMS tables if 1:n direct
                            if($rfield['datatype'] == 25 && substr(strtoupper(lmbdb_result($rs, "TABELLE")),0,5) == 'LDMS_'){
                                continue;
                            }
                            
                            if(lmbdb_result($rs, "NAME") != $temp){
                            ?>
                            
                            <div class="table-section bg-contrast p-1 my-1"><?=$lang[lmbdb_result($rs, "NAME")]?></div>
                            
                            <?php } ?>

                            <div class="form-check mb-1">
                                <label class="form-check-label" <?=(lmbdb_result($rs, "TAB_ID") == $tabid)?'text-danger':''?>>
                                    <input type="radio" name="new_verkntabid" value="<?=lmbdb_result($rs, "TAB_ID")?>" OnChange="document.form1.submit();">
                                    <?=$lang[lmbdb_result($rs, "BESCHREIBUNG")]?> (<?=lmbdb_result($rs, "TABELLE")?>)
                                </label>
                            </div>
                        
                        <?php
                            $temp = lmbdb_result($rs, "NAME");
                        }

                        ?>

                <?php }
                else{?>

                    <p class="fw-bold">
                        <?php
                        echo $rfield['verkntabname'];
                        if($rfield['verkntabletype'] == 3){
                            echo "&nbsp; &nbsp;&nbsp;($lang[2855]&nbsp;&nbsp;<i style=\"vertical-align:text-bottom\" class=\"lmb-icon lmb-switch\"></i>) ";
                        }elseif($rfield['verkntabletype'] == 2){
                            echo "&nbsp;&nbsp;($lang[2371]&nbsp;&nbsp;<i style=\"vertical-align:text-bottom\" class=\"lmb-icon lmb-long-arrow-left\"></i>) ";
                        }
                        ?>
                    </p>

                    <table class="table table-sm table-borderless table-hover">
                        <thead>
                        <tr>
                            <th></th>
                            <th title="<?=$lang[2954]?>" class="text-center <?php echo (!$rfield['veknfieldid'] ? 'text-danger' : '')?>"><?=$lang[1825]?>&nbsp;</th>
                            <th title="<?=$lang[2808]?>" class="text-center">&nbsp;<?=$lang[1826]?>&nbsp;</th>
                            <th title="<?=$lang[2806]?>" class="text-center" colspan="2">&nbsp;<?=$lang[2089]?>&nbsp;</th>
                            <th title="<?=$lang[2807]?>" class="text-center" colspan="2">&nbsp;<?=$lang[1846]?>&nbsp;</th>
                        </tr>
                        </thead>
                        
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

                        while(lmbdb_fetch_row($rs)):
                            $recrelation = null;
                            #if(lmbdb_result($rs, "VERKNTABLETYPE") == 2){$bzm++;continue;} !!!!!!!!!!!!!!! rückwertige Verknüpfung - mögliche ENDLOSSCHLEIFE
                            #if(lmbdb_result($rs, "FIELD_TYPE") == 11 AND $rfield['verkntree']){continue;}

                            $field_id = lmbdb_result($rs, "FIELD_ID");
                            if(lmbdb_result($rs, "FIELD_TYPE") == 11 OR lmbdb_result($rs, "VERKNTABLETYPE") == 2){$recrelation = 'font-style:italic;text-decoration:underline';}

                            if(!in_array(lmbdb_result($rs, "DATA_TYPE"),$wrong_fields)):

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
                                
                                ?>
                                
                            <tr>
                                <td title="<?=lmbdb_result($rs, "FIELD_NAME")?>" style="<?=$recrelation?>"><?=$lang[lmbdb_result($rs, "SPELLING")]?></td>
                                <td class="text-center">
                                    <?php if(lmbdb_result($rs, "VERKNTABLETYPE") != 2 AND $field_id < 1000 AND lmbdb_result($rs, "FIELD_TYPE") != 11): ?>
                                        <input type="radio" <?=$vradio?> NAME="new_verknfieldid" VALUE="<?=$field_id?>"  <?=$checked?> OnChange="document.form1.set_verknfieldid.value=1;document.form1.submit()">
                                    
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <input type="checkbox" name="new_verknsearchid[<?=$field_id?>]" value="<?=$field_id?>" <?=$schecked?>>
                                </td>
                                <td class="text-center">
                                    <?php if(lmbdb_result($rs, "VERKNTABLETYPE") != 2 AND $field_id < 1000): ?>
                                        <input type="checkbox" class="verknfindid" elid="<?=$field_id?>" NAME="new_verknfindid[<?=$field_id?>]" VALUE="<?=$field_id?>" <?=$eschecked?> onchange="changeorderf(this,<?=$field_id?>)">
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <span id="verknfindid_<?=$field_id?>" class="text-primary"> 
                                    <?php if($fchecked): ?>
                                        (<?=$sortkeyf?>)
                                    <?php endif; ?>
                                    </span><input type="hidden" id="verknsortf_<?=$field_id?>" name="verknsortf[<?=$field_id?>]" value="<?=$sortkeyf?>">
                                </td>
                                <td class="text-center">
                                    <input type="checkbox" class="verknviewid" elid="<?=$field_id?>" NAME="new_verknviewid[<?=$field_id?>]" VALUE="<?=$field_id?>" <?=$gchecked?> onchange="changeorder(this,<?=$field_id?>)">
                                </td>
                                <td class="text-center">
                                    <span id="verknviewid_<?=$field_id?>" class="text-primary"> 
                                    <?php if($gchecked): ?>
                                        (<?=$sortkey?>)
                                    <?php endif; ?>
                                    </span><input type="hidden" id="verknsort_<?=$field_id?>" name="verknsort[<?=$field_id?>]" value="<?=$sortkey?>">
                                </td>
                            </tr>
                            
                                <?php
                            endif;

                        endwhile;
                        ?>

                        <tr>
                        </tr>


                    </table>
                    
                    
                <?php }?>


    <hr>


    <div class="mb-0 row">
        <label class="col-sm-6 col-form-label">
            <?=$lang[2595]?><br>
            <small class="form-text text-muted"><?=$lang[2806]?> (string / html)</small>
        </label>
        <div class="col-sm-6 pt-2">
            <input type="text" name="findidCut" class="form-control form-control-sm w-25" value="<?=$rfield['findidcut']?>">
        </div>
    </div>

    <div class="mb-0 row">
        <label class="col-sm-6 col-form-label">
            <?=$lang[2595]?><br>
            <small class="form-text text-muted"><?=$lang[2807]?> (string / html)</small>
        </label>
        <div class="col-sm-6 pt-2">
            <input type="text" name="viewidCut" class="form-control form-control-sm w-25" value="<?=$rfield['viewidcut']?>">
        </div>
    </div>

    <?php if($rfield['verkntabletype'] == 1 AND $rfield['verkntabid'] AND $rfield['datatype'] != 23): ?>

    <div class="mb-0 row">
        <label class="col-sm-6 col-form-label">
            <?=$lang[941]?><br>
            <small class="form-text text-muted">(DELETE RESTRICT)</small>
        </label>
        <div class="col-sm-6 pt-2">
            <input type="checkbox" name="new_refint" <?=($rfield['refint'])?'checked':''?> onchange="document.form1.change_refint.value=1">
            <?php if($refint_rule){echo "( $f1 | $f2 )";}?>
            <?=$message1?>
            <input type="hidden" name="new_refint_rule" value="1">
        </div>
    </div>

    <div class="mb-0 row">
        <label class="col-sm-6 col-form-label ">
            <?=$lang[3147]?><br>
            <small class="form-text text-muted"> <?=$lang[3148]?></small>
        </label>
        <div class="col-sm-6 pt-2">
            <input type="checkbox" name="new_isunique" <?=($rfield['isunique'])?'checked':''?> onchange="document.form1.change_uniqueralation.value=1">
        </div>
    </div>

    <?php if($rfield['datatype'] == 24): ?>
    <div class="mb-0 row">
        <label class="col-sm-6 col-form-label">
            <?=$lang[3149]?><br>
            <small class="form-text text-muted"><?=$lang[3150]?></small>
        </label>
        <div class="col-sm-6 pt-2">
            <input type="checkbox" name="new_multiralation" <?=($rfield['multirelation'])?'checked':''?> onchange="document.form1.change_multiralation.value=1">
        </div>
    </div>
    <?php endif; ?>

    <?php if($rfield['datatype'] != 25): ?>
    <div class="mb-0 row">
        <label class="col-sm-6 col-form-label ">
            <?=$lang[2809]?><br>
            <small class="form-text text-muted"><?=$lang[3151]?></small>
        </label>
        <div class="col-sm-6 pt-2">
            <input type="checkbox" name="new_vparams" value="1" <?=($rfield['verknparams'])?'checked':''?> onchange="document.form1.change_vparams.value=1">
            <?php if($rfield['verknparams']){echo "(ID: ".$rfield['verknparams'].")";}?>
            <span class="text-success"><?= $GLOBALS['message'] ?></span>
        </div>
    </div>
    <?php endif; ?>


    <?php endif; ?>
    

        

    <div class="text-center">
        <button type="submit" class="btn btn-primary" NAME="vknsave" value="1"><?=$lang[33]?></button>
    </div>
</FORM>
