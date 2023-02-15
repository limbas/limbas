<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */



if(!$group_id){$group_id = 1;}
$sqlquery = "SELECT GROUP_ID,NAME,LEVEL FROM LMB_GROUPS WHERE GROUP_ID = $group_id";
$rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
if(!$rs){$commit = 1;}

?>

<div class="container-fluid p-3">
    <div class="row">
        <div class="col-sm-6">
            <FORM ACTION="main_admin.php" METHOD="post" name="form1">
                <input type="hidden" name="action" value="setup_group_add">

                <div class="card">
                    <div class="card-body">

                        <div class="mb-3 row">
                            <label class="col-sm-4 col-form-label"><?=$lang[897]?></label>
                            <div class="col-sm-8">
                                <select name="group_level" class="form-select form-select-sm">
                                    <option value="0">root</option>
                                    <?php viewgrouptree(0,"",0); ?>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3 row">
                            <label class="col-sm-4 col-form-label"><?=$lang[569]?></label>
                            <div class="col-sm-8">
                                <input type="text" name="group_name" class="form-control form-control-sm">
                            </div>
                        </div>
                        <div class="mb-3 row">
                            <label class="col-sm-4 col-form-label"><?=$lang[126]?></label>
                            <div class="col-sm-8">
                                <textarea NAME="group_beschr" class="form-control form-control-sm"></textarea>
                            </div>
                        </div>

                        <hr>

                        <p><?=$lang[2585]?></p>

                        <div class="row">
                            <label class="col-sm-4 col-form-label"><?=$lang[2129]?></label>
                            <div class="col-sm-8">
                                <INPUT TYPE="checkbox" NAME="update_parent_tabsettings" VALUE="1" checked>
                            </div>
                        </div>
                        <div class="row">
                            <label class="col-sm-4 col-form-label"><?=$lang[2130]?></label>
                            <div class="col-sm-8">
                                <INPUT TYPE="checkbox" NAME="update_parent_filesettings" VALUE="1" checked>
                            </div>
                        </div>
                        <div class="row">
                            <label class="col-sm-4 col-form-label"><?=$lang[2131]?></label>
                            <div class="col-sm-8">
                                <INPUT TYPE="checkbox" NAME="update_parent_menusettings" VALUE="1" checked>
                            </div>
                        </div>
                        <div class="mb-3 row">
                            <label class="col-sm-4 col-form-label"><?=$lang[2306]?></label>
                            <div class="col-sm-8">
                                <INPUT TYPE="checkbox" NAME="update_parent_formsettings" VALUE="1" checked>
                            </div>
                        </div>


                        <div class="text-end">
                            <button type="submit" class="btn btn-primary"><?=$lang[571]?></button>
                        </div>

                        <INPUT TYPE="hidden" NAME="level" value="<?=$group_id;?>">
                    </div>
                </div>


            </FORM>
        </div>
    </div>
    
</div>
