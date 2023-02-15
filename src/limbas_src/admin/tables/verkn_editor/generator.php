<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */
?>
<FORM ACTION="main_admin.php" METHOD="post" NAME="form2">
    <input type="hidden" name="action" value="setup_verkn_editor">
    <input type="hidden" name="fieldid" value="<?=$fieldid;?>">
    <input type="hidden" name="tabid" value="<?=$tabid?>">
    <input type="hidden" name="category" value="create">

    <?php if(!empty($message2)): ?>
        <div class="alert alert-success mb-2" role="alert">
            <?=$message2?>
        </div>
    <?php endif; ?>
    
    
    
    <div class="row mb-3">
        <div class="col-sm-6">
            <p class="fw-bold"><?=$rfield['tabname']?></p>
            <select name="v_field1" size="20" class="form-select">
                <option value="ID" selected>ID</option>
                    <?php
                    $sqlquery =  "SELECT DISTINCT FIELD_NAME FROM LMB_CONF_FIELDS WHERE TAB_ID = $tabid AND FIELD_TYPE < 100 AND FIELD_NAME != 'ID'";
                    $rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
                    if(!$rs) {$commit = 1;}
                    while(lmbdb_fetch_row($rs)) {
                        echo '<option value="'.lmbdb_result($rs, "FIELD_NAME").'">'.lmbdb_result($rs, "FIELD_NAME").'</option>';
                    }
                    ?>
            </select>
        </div>
        <div class="col-sm-6">
            <p class="fw-bold"><?=$rfield['verkntabname']?></p>
            <select name="v_field2" size="20" class="form-select">
                <option value="ID" selected>ID</option>
                    <?php
                    $sqlquery =  "SELECT DISTINCT FIELD_NAME FROM LMB_CONF_FIELDS WHERE TAB_ID = ".$rfield['verkntabid']." AND FIELD_TYPE < 100 AND FIELD_NAME != 'ID'";
                    $rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
                    if(!$rs) {$commit = 1;}
                    while(lmbdb_fetch_row($rs)) {
                        echo '<option value="'.lmbdb_result($rs, "FIELD_NAME").'">'.lmbdb_result($rs, "FIELD_NAME").'</option>';
                    }
                    ?>
            </select>
        </div>
    </div>

    <div class="text-center">
        <button type="submit" class="btn btn-primary" NAME="verknrefresh" value="1"><?=$lang[1303]?></button>
    </div>
</FORM>
