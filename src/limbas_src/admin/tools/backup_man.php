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

function medium_path(med) {
	if(med == 1){
		document.getElementById('file_path').style.display = '';
		document.getElementById('tabe_path').style.display = 'none';
	}else{
		document.getElementById('file_path').style.display = 'none';
		document.getElementById('tabe_path').style.display = '';
	}
}

function device_path(device) {
	if(device == 1){
		document.getElementById('art_path').style.display = '';
		document.getElementById('medium_path').style.display = '';
	}else{
		document.getElementById('art_path').style.display = 'none';
		document.getElementById('medium_path').style.display = 'none';
	}
}

</SCRIPT>

<div class="container-fluid p-3">
    <div class="row">
        <div class="col-md-6">
            <div class="card">
        <div class="card-body">
            <FORM ACTION="main_admin.php" METHOD="post" name="form1">
                <input type="hidden" name="action" VALUE="setup_backup_man">
                <input type="hidden" name="device" VALUE="1">
        
                <?php if($umgvar["backup_default"]){$path1 = $umgvar["backup_default"];}else{$path1 = "localhost:".$umgvar["pfad"]."/BACKUP";}?>
        
        
                <div class="mb-3"  ID="art_path">
                    <label for="art">Type</label>
                    <SELECT NAME="art"  class="form-select form-select-sm">
                        <OPTION VALUE="1" SELECTED>Complete Data Backup</OPTION>
                        <?php if($DB["DBVENDOR"] == "maxdb76"){?><OPTION VALUE="2">Incremental Data Backup</OPTION><?php }?>
                        <?php if($DB["DBVENDOR"] == "maxdb76"){?><OPTION VALUE="3">Log Backup</OPTION><?php }?>
                    </SELECT>
                </div>
                <div class="mb-3" ID="medium_path">
                    <label for="medium">Media</label>
                    <SELECT NAME="medium" OnChange="medium_path(this.value);" class="form-select form-select-sm">
                        <OPTION VALUE="1" SELECTED>File</OPTION>
                        <?php if($DB["DBVENDOR"] == "maxdb76"){?><OPTION VALUE="2">Tape</OPTION><?php }?>
                    </SELECT>
                </div>
                <div class="mb-3" ID="file_path">
                    <label for="path1">Target (on db-host)</label>
                    <input type="text" class="form-control form-control-sm" id="path1" name="path1" VALUE="<?=$path1?>">
                </div>
                <div class="mb-3" ID="tabe_path" style="display: none">
                    <label for="path2">Target (dev)</label>
                    <input type="text" class="form-control form-control-sm" id="path2" NAME="path2" VALUE="/dev/rft0">
                </div>
        
                <button type="submit" NAME="int_backup" class="btn btn-primary" onclick="limbasWaitsymbol(event,1);" value="1"><?=$lang[2751]?></button>
        
        
        
                <?php if (!empty($message) || $device == 2) : ?>

                    <div class="card bg-light mt-3">
                        <div class="card-body">
                            <p class="card-text"><?=$message?></p>
                            <?php if($device == 2) : ?>
                                <p class="card-text"><?=$lang[971]?>:<BR>
                                    <span class="fw-bold"><?= $result_exp_tabs ?></span> <?=$lang[577]?><BR>
                                    <span class="fw-bold"><?= $result_exp_dat ?></span> <?=$lang[972]?><BR>
                                    <?=$lang[973]?>
                                </p>
                            <?php endif; ?>
                        </div>
                    </div>
                
                <?php endif; ?>
    
            </FORM>
        </div>
    </div>
        </div>
    </div>
</div>
