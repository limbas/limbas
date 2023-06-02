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
function gurefresh() {
        let gu = confirm("<?=$lang[896]?>");
        if(gu) {
        	refresh = open("main_admin.php?action=setup_grusrref&group_id=<?=$ID?>&group_name=<?=urlencode($groupdat["name"][$ID])?>" ,"refresh","toolbar=0,location=0,status=1,menubar=0,scrollbars=1,resizable=1,width=550,height=650");
        }
}
function lrefresh() {
        let link = confirm("<?=$lang[896]?>");
        if(link) {
            refresh = open("main_admin.php?action=setup_linkref&group=<?=$ID?>" ,"refresh","toolbar=0,location=0,status=1,menubar=0,scrollbars=1,resizable=1,width=550,height=650");
        }
}

function del() {
        let del = confirm("<?=$lang[1254]?>");
        if(del) {
                document.location.href="main_admin.php?action=setup_group_erg&group_del=1&ID=<?=$ID?>&duf=document.form1.duf.value";
        }
}

function activate_subrules(val) {
    if(val == '0'){
        $('.subrules').prop( "checked", false );
    }else{
        $('.subrules').prop( "checked", true);
    }
}



</script>


<div class="container-fluid p-3">
    <FORM ACTION="main_admin.php" METHOD="post" name="form1">
        <input type="hidden" name="action" VALUE="setup_group_erg">
        <input type="hidden" name="ID" VALUE="<?=$ID?>">
        <input type="hidden" name="maingroup">
        <input type="hidden" name="change">
        <input type="hidden" name="duf" value="1">

        <div class="row">
        <?php
        $activeTabLinkId = 135;
        require(__DIR__.'/group_tabs.php') ?>
        
        <div class="tab-content col-9 border border-start-0 bg-white">
            <div class="tab-pane active p-3">

                <h5><i class="lmb-icon lmb-group"></i>&nbsp;<?=$groupdat["name"][$ID]?></h5>
                
                <hr>

                
                <div class="row mb-3">
                    <div class="col-sm-6">

                        <div class="row">
                            <label class="col-sm-4 col-form-label">ID</label>
                            <div class="col-sm-8">
                                <span class="form-control-plaintext"><?=$ID?></span>
                            </div>
                        </div>
                        <div class="row">
                            <label class="col-sm-4 col-form-label"><?=$lang[563]?></label>
                            <div class="col-sm-8">
                                <span class="form-control-plaintext"><?=$result_group["erstdatum"]?></span>
                            </div>
                        </div>

                        <?php if($ID == 1):?>

                            <div class="row">
                                <label class="col-sm-4 col-form-label"><?=$lang[561]?></label>
                                <div class="col-sm-8">
                                    <span class="form-control-plaintext"><?=$result_group["name"]?></span>
                                </div>
                            </div>
                            <div class="row">
                                <label class="col-sm-4 col-form-label"><?=$lang[126]?></label>
                                <div class="col-sm-8">
                                    <span class="form-control-plaintext"><?=$result_group["beschreibung"]?></span>
                                </div>
                            </div>
                        
                        <?php else: ?>

                            <div class="mb-3 row">
                                <label class="col-sm-4 col-form-label"><?=$lang[561]?></label>
                                <div class="col-sm-8">
                                    <input type="text" name="groupname" class="form-control form-control-sm" value="<?=$result_group["name"]?>">
                                </div>
                            </div>
                            <div class="mb-3 row">
                                <label class="col-sm-4 col-form-label"><?=$lang[126]?></label>
                                <div class="col-sm-8">
                                    <textarea NAME="groupdesc" class="form-control form-control-sm"><?=htmlentities($result_group["beschreibung"],ENT_QUOTES,$umgvar["charset"])?></textarea>
                                </div>
                            </div>
                        
                        <?php endif; ?>


                        <?php if($ID != 1 AND $session["superadmin"] == 1):?>
                            <div class="mb-3 row">
                                <label class="col-sm-4 col-form-label"><?=$lang[3120]?></label>
                                <div class="col-sm-8">
                                    <select id="userdata_group_id" name="userdata[group_id]" class="form-select form-select-sm" onchange="document.form1.maingroup.value=this.value;">
                                        <option value="0"></option>
                                        <?php viewgrouptree($startgroup,'');?>
                                    </select>
                                </div>
                            </div>
                        <?php endif; ?>

                        <div class="mb-3 row">
                            <label class="col-sm-4 col-form-label"><?=$lang[1242]?></label>
                            <div class="col-sm-8">
                                <select onchange="document.form1.action.value='setup_user_change_admin';document.form1.ID.value=this.value;document.form1.submit();" class="form-select form-select-sm">
                                    <option></option>
                                        <?php
                                        $sqlquery = "SELECT DISTINCT USER_ID,USERNAME FROM LMB_USERDB WHERE GROUP_ID = $ID AND DEL = ".LMB_DBDEF_FALSE;
                                        $rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
                                        while(lmbdb_fetch_row($rs)) {
                                            echo "<option value=\"".lmbdb_result($rs, "USER_ID")."\">".lmbdb_result($rs, "USERNAME").'</option>';
                                        }
                                        ?>
                                </select>
                            </div>
                        </div>

                        
                        
                    </div>
                    <div class="col-sm-6">
                        <?php if($ID != 1):?>

                            <div class="mb-3 row">
                                <label class="col-sm-4 col-form-label"><?=$lang[1532]?></label>
                                <div class="col-sm-8">
                                    <input type="text" value="<?=htmlentities($result_group["redirect"],ENT_QUOTES,$umgvar["charset"])?>" name="redirect" class="form-control form-control-sm"><small id="emailHelp" class="form-text text-muted">(e.g. action=gtab_change&uml;gtabid=1&uml;formid=1)</small>
                                </div>
                            </div>

                            <div class="mb-3 row">
                                <label class="col-sm-4 col-form-label"><?=$lang[1954]?></label>
                                <div class="col-sm-8">
                                    <select name="multiframe[]" size="3" multiple class="form-select form-select-sm">
                                        <?php

                                        if($result_group["mframelist"]){
                                            if($path = read_dir($umgvar["pfad"] . "/extra/multiframe")){
                                                foreach($path["name"] as $key => $value){
                                                    $value_ = explode(".",$value);
                                                    if($path["typ"][$key] == "file" AND $value_[1] == "dao"){
                                                        if(in_array($value,$result_group["mframelist"])){$SELECTED =  "SELECTED";}else {unset($SELECTED);}
                                                        echo "<option value=\"$value\" $SELECTED>$value</option>";
                                                    }
                                                }
                                            }
                                        }

                                        ?>
                                    </select>
                                </div>
                            </div>


                            
                            <hr>
                                <div class="mb-3 row">
                                    <label class="col-sm-4 col-form-label"><?=$lang[2585]?></label>
                                    <div class="col-sm-8">
                                        <select name="update_parent_group" class="form-select form-select-sm" onchange="activate_subrules(this.value);">
                                            <option value="0"></option>
                                                <?php
                                                viewgrouptree($startgroup,'');
                                                ?>
                                        </select>
                                    </div>
                                </div>


                                <div class="row row-cols-2">
                                    <label class="col-sm-4 col-form-label"><?=$lang[483]?></label>
                                    <div class="col-sm-2">
                                        <INPUT  class="subrules" TYPE="checkbox" NAME="update_parent[menu]" VALUE="1">
                                    </div>

                                    <label class="col-sm-4 col-form-label"><?=$lang[2745]?></label>
                                    <div class="col-sm-2">
                                        <INPUT class="subrules" TYPE="checkbox" NAME="update_parent[form]" VALUE="1">
                                    </div>
                                </div>

                                <div class="row row-cols-2">
                                    <label class="col-sm-4 col-form-label"><?=$lang[2129]?></label>
                                    <div class="col-sm-2">
                                        <INPUT class="subrules" TYPE="checkbox" NAME="update_parent[table]" VALUE="1">
                                    </div>

                                    <label class="col-sm-4 col-form-label"><?=$lang[2747]?></label>
                                    <div class="col-sm-2">
                                        <INPUT class="subrules" TYPE="checkbox" NAME="update_parent[diagramm]" VALUE="1">
                                    </div>

                                </div>
                                <div class="row row-cols-2">
                                    <label class="col-sm-4 col-form-label"><?=$lang[2130]?></label>
                                    <div class="col-sm-2">
                                        <INPUT  class="subrules" TYPE="checkbox" NAME="update_parent[file]" VALUE="1">
                                    </div>

                                    <label class="col-sm-4 col-form-label"><?=$lang[2743]?></label>
                                    <div class="col-sm-2">
                                        <INPUT class="subrules" TYPE="checkbox" NAME="update_parent[reminder}" VALUE="1">
                                    </div>

                                </div>
                                <div class="row row-cols-2">
                                    <label class="col-sm-4 col-form-label"><?=$lang[2277]?></label>
                                    <div class="col-sm-2">
                                        <INPUT class="subrules" TYPE="checkbox" NAME="update_parent[report]" VALUE="1">
                                    </div>
                                    <label class="col-sm-4 col-form-label"><?=$lang[2749]?></label>
                                    <div class="col-sm-2">
                                        <INPUT class="subrules" TYPE="checkbox" NAME="update_parent[workflow]" VALUE="1">
                                    </div>
                                </div>

                        
                        <?php endif; ?>
                    </div>
                </div>

                <?php if($ID != 1):?>
                <div class="row">
                    <div class="col-6">
                        <button type="button" class="btn btn-outline-danger" onclick="del();"><?=$lang[160]?></button>
                    </div>
                    <div class="col-6 text-end">
                        <button type="button" class="btn btn-primary" onclick="document.form1.change.value='1';document.form1.submit();"><?=$lang[522]?></button>
                    </div>
                </div>
                <?php endif; ?>
                
            </div>
        </div>
        </div>


    </FORM>
</div>
