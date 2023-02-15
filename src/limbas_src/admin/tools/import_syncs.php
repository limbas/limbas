<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */


?>
<div class="row">
    <div class="col-md-9 col-lg-7">

        <div class="card border-top-0 mb-3">
            <div class="card-body">

                <div class="row mb-2">
                    <label for="filesync" class="col-sm-2 col-form-label">File:</label>
                    <div class="col-sm-10">
                        <input type="file" name="filesync" id="filesync">
                    </div>
                </div>

                <div class="text-end">
                    <button class="btn btn-primary" type="button" onclick="document.form1.precheck.value=1;document.form1.import_action.value=1;document.form1.syncimport.value=1;document.form1.submit();"><?=$lang[2243]?></button>
                </div>

            </div>
        </div>
    </div>
</div>



<?php

    require_once(COREPATH . 'admin/tools/import_sync.php');
    if (($syncimport AND !empty($_FILES["filesync"])) OR $confirm_syncimport) {
        $importSync = new importSync();
        $result = $importSync->secure_sync_import($_FILES["filesync"]['tmp_name'], true, $precheck, $confirm_syncimport);
        if(!$result['success']) {
            echo "Errors!";
            print_r($result,1);
        } elseif(!$confirm_syncimport) {
            echo '<div class="lmbPositionContainerMain">';
            echo '<input type="button" value="' . $lang[979] . '" onclick="document.form1.confirm_syncimport.value=1;document.form1.submit();" style="color:green">';
            echo '</div>';
        }
    }



?>
