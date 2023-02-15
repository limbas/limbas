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

                <div class="row">
                    <div class="col-sm-3">
                        <?=$lang[1007]?>:
                    </div>
                    <div class="col-sm-5">
                        <select name="backupdir" class="form-select form-select-sm">
                            <option></option>
                            <?php
                            if($path = read_dir($umgvar["pfad"]."/BACKUP")){
                                foreach($path["name"] as $key => $value){
                                    if($path["typ"][$key] == "file"){
                                        echo "<option value=\"".$value."\">$value</option>";
                                    }
                                }
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-sm-4 text-end">
                        <button class="btn btn-danger" type="button" onclick="document.form1.import_action.value=1;document.form1.submit();"><?=$lang[1009]?></button>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>



<?php
if($report): ?>

<div class="card mb-3">
    <div class="card-body">
        <h5 class="card-title"></h5>
        <?=$report?>
    </div>
</div>

<?php endif; ?>


<?php if($import_action): ?>

<div class="card mb-3">
    <div class="card-body">
        <h5 class="card-title"></h5>
        <?php
        import_complete(1,$txt_encode);
        ?>
    </div>
</div>
    
<?php endif; ?>
