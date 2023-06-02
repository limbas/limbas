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


<FORM ACTION="main_admin.php" METHOD="post" name="form1">
    <input type="hidden" name="action" VALUE="setup_backup_cron">
    <input type="hidden" name="device" VALUE="1">
    <div class="container-fluid p-3">
        
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">

                        <?php if($umgvar['backup_default']){$path1 = $umgvar['backup_default'];}else{$path1 = "localhost:".$umgvar['pfad']."/BACKUP";}?>

                        <div class="row"  ID="art_path">
                            <label for="art" class="col-sm-3 col-form-label fw-bold">Type</label>
                            <div class="col-sm-9">
                                <SELECT NAME="art"  class="form-select form-select-sm">
                                    <OPTION VALUE="1" SELECTED>Complete Data Backup</OPTION>
                                    <?php if($DB["DBVENDOR"] == "maxdb76"){?><OPTION VALUE="2">Incremental Data Backup</OPTION><?php }?>
                                    <?php if($DB["DBVENDOR"] == "maxdb76"){?><OPTION VALUE="3">Log Backup</OPTION><?php }?>
                                </SELECT>
                            </div>
                        </div>
                        <div class="row" ID="medium_path">
                            <label for="medium" class="col-sm-3 col-form-label fw-bold">Media</label>
                            <div class="col-sm-9">
                                <SELECT NAME="medium" OnChange="medium_path(this.value);" class="form-select form-select-sm">
                                    <OPTION VALUE="1" SELECTED>File</OPTION>
                                    <?php if($DB["DBVENDOR"] == "maxdb76"){?><OPTION VALUE="2">Tape</OPTION><?php }?>
                                </SELECT>
                            </div>
                        </div>
                        <div class="row" ID="file_path">
                            <label for="path1" class="col-sm-3 col-form-label"><span class=" fw-bold">Target</span> (on db-host)</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control form-control-sm" id="path1" name="path1" VALUE="<?=$path1?>">
                            </div>
                        </div>
                        <div class="row" ID="tabe_path" style="display: none">
                            <label for="path2" class="col-sm-3 col-form-label"><span class="fw-bold">Target</span> (dev)</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control form-control-sm" id="path2" NAME="path2" VALUE="/dev/rft0">
                            </div>
                        </div>
                        <div class="row" ID="alive">
                            <label for="alive-in" class="col-sm-3 col-form-label"><span class="fw-bold">Alive</span> (days)</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control form-control-sm" id="alive-in" NAME="alive" VALUE="31">
                            </div>
                        </div>
                        <div class="row">
                            <label class="col-sm-3 col-form-label"><span class="fw-bold">Timeperiod</span> (cron)</label>
                            <div class="col-sm-9">

                                <table>
                                    <tr class="small">
                                        <th>minute</th>
                                        <th>hour</th>
                                        <th>day of month</th>
                                        <th>month</th>
                                        <th>day of week</th>
                                    </tr>
                                    <TR>
                                        <TD><INPUT TYPE="TEXT" NAME="cron_1" VALUE="0" class="form-control form-control-sm"></TD>
                                        <TD><INPUT TYPE="TEXT" NAME="cron_2" VALUE="1" class="form-control form-control-sm"></TD>
                                        <TD><INPUT TYPE="TEXT" VALUE="*" NAME="cron_3" class="form-control form-control-sm"></TD>
                                        <TD><INPUT TYPE="TEXT" NAME="cron_4" VALUE="*" class="form-control form-control-sm"></TD>
                                        <TD><INPUT TYPE="TEXT" NAME="cron_5" VALUE="*" class="form-control form-control-sm"></TD>
                                    </TR>
                                </table>


                            </div>
                        </div>
                        <div class="text-end pt-3">
                            <button type="submit" NAME="cron_backup" value="1" class="btn btn-primary btn-sm">add Job!</button>
                        </div>
                        


                        <?php if (!empty($message) || $device == 2) : ?>

                            <div class="card bg-light mt-3">
                                <div class="card-body">
                                    <p class="card-text"><?=implode("<BR>",$message)?></p>
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




                    </div>
                </div>


                <table class="table table-sm table-striped mb-0 border bg-white">
                    <thead>
                    <tr>
                        <th>Nr</th>
                        <th>Type</th>
                        <th>Time</th>
                        <th>Desc</th>
                        <th>Activ</th>
                        <th>Alive</th>
                        <th>delete</th>
                    </tr>
                    </thead>

                    <tbody>


                    <?php

                    $sqlquery = "SELECT * FROM LMB_CRONTAB WHERE KATEGORY = 'BACKUP' ORDER BY ERSTDATUM";
                    $rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
                    if(!$rs) {$commit = 1;}
                    while(lmbdb_fetch_row($rs)):
                        if(lmbdb_result($rs,"KATEGORY") == "BACKUP"){$color = "#94AEEF";}
                        elseif(lmbdb_result($rs,"KATEGORY") == "INDEX"){$color = "#FF9294";}
                        
                        ?>
                    
                    <tr style="background-color: <?=$color?>">
                        <td><?=lmbdb_result($rs,"ID")?></td>
                        <td><?=lmbdb_result($rs,"KATEGORY")?></td>
                        <td><?=lmbdb_result($rs,"START")?></td>
                        <td><?=lmbdb_result($rs,"DESCRIPTION")?></td>
                        <td><?=lmbdb_result($rs,"ACTIV")?></td>
                        <td><?=lmbdb_result($rs,"ALIVE")?></td>
                        <td><a href="main_admin.php?action=setup_backup_cron&del_job=<?=lmbdb_result($rs,"ID")?>"><i class="lmb-icon lmb-trash"></i></a></td>
                    </tr>
                    
                    <?php
                        
                        
                        $cronvalue[] = lmbdb_result($rs,"START")."\tphp \"".COREPATH."cron.php\" ".lmbdb_result($rs,"ID");
                    endwhile;

                    ?>
                    </tbody>


                </table>


                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Crontab Value</h5>
                        <textarea id="crontab" class="form-control form-control-sm">
                    <?php
                    if($cronvalue){
                        foreach($cronvalue as $key => $value){
                            echo str_replace(";"," ",$value)."\n";
                        }
                    }
                    ?>
                </textarea>
                    </div>
                </div>
            </div>
        </div>
    
    </div>
</FORM>
