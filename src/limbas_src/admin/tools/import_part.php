<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */


?>



<?php
# * --- Teilimport -------------------------------
if(!$txt_terminate){$txt_terminate = $umgvar['csv_delimiter'];}
if(!$txt_enclosure){$txt_enclosure = $umgvar['csv_enclosure'];}
if(!$import_typ){$import_typ = 'atm';}
?>

<div class="row">
    <div class="col-md-9 col-lg-7">
        <div class="card border-top-0 mb-3">
            <div class="card-body">

                <div class="row">
                    <div class="col-sm-3">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" value="atm" id="import-typ" name="import_typ" <?=($import_typ=='atm')?'checked':''?>>
                            <label class="form-check-label" for="import-typ">
                                <img src="assets/images/logo.svg" alt="<?=$lang[995]?>" style="height: 1em"> <?=$lang[998]?>
                            </label>
                        </div>
                    </div>
                    <div class="col-sm-5">
                        <input type="file" NAME="fileatm">
                    </div>
                    <div class="col-sm-4">

                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" value="over" id="import-overwrite-over" name="import_overwrite" <?=($import_overwrite == 'over' OR !$import_overwrite)?'checked':''?>>
                            <label class="form-check-label" for="import-overwrite-over">
                                <?=$lang[1002]?>
                            </label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" value="add" id="import-overwrite-add" name="import_overwrite" <?=($import_overwrite == 'add')?'checked':''?>>
                            <label class="form-check-label" for="import-overwrite-add">
                                <?=$lang[1003]?>
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" value="add_with_ID" id="import-overwrite-addid" name="import_overwrite" <?=($import_overwrite == 'add_with_ID')?'checked':''?>>
                            <label class="form-check-label" for="import-overwrite-addid">
                                <?=$lang[1003]?> (<?=$lang[1004]?>)
                            </label>
                        </div>
                    </div>
                </div>

                <hr>

                <div class="row">
                    <div class="col-sm-3">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" value="txt" id="import-typ" name="import_typ" <?=($import_typ=='txt')?'checked':''?>>
                            <label class="form-check-label" for="import-typ">
                                <i class="lmb-icon lmb-file-text"></i> <?=$lang[992]?>&nbsp;(csv,gz,zip)
                            </label>
                        </div>
                    </div>
                    <div class="col-sm-5">
                        <input type="file" NAME="filetxt">
                    </div>
                    <div class="col-sm-4">

                        <div class="row">
                            <label for="txt-calculate" class="col-sm-7 col-form-label"><?=$lang[997]?></label>
                            <div class="col-sm-5">
                                <select name="txt_calculate" id="txt-calculate" class="form-select form-select-sm">
                                    <option value="0"><?=$lang[993]?></option>
                                    <option value="50" selected>50</option>
                                    <option value="100">100</option>
                                    <option value="1000">1000</option>
                                    <option value="99999999"><?=$lang[994]?></option>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <label for="txt-terminate" class="col-sm-7 col-form-label">field terminated</label>
                            <div class="col-sm-5">
                                <input type="text" name="txt_terminate" id="txt-terminate" class="form-control form-control-sm"value="<?=htmlentities($txt_terminate,ENT_QUOTES,$umgvar["charset"])?>">
                            </div>
                        </div>
                        <div class="row">
                            <label for="txt-enclosure" class="col-sm-7 col-form-label">field enclosure</label>
                            <div class="col-sm-5">
                                <input type="text" name="txt_enclosure" id="txt-enclosure" class="form-control form-control-sm" value="<?=htmlentities($txt_enclosure,ENT_QUOTES,$umgvar["charset"])?>">
                            </div>
                        </div>
                        <div class="row">
                            <label for="attach-gtabid" class="col-sm-7 col-form-label"><?=$lang[1003]?></label>
                            <div class="col-sm-5">
                                <select name="attach_gtabid" id="attach-gtabid" class="form-select form-select-sm">
                                    <option value=""></option>
                                    <?php
                                    $gtab_ = $gtab;
                                    asort($gtab_['table']);
                                    foreach ($gtab_["table"] as $key => $value){
                                        if($attach_gtabid == $gtab_["tab_id"][$key]){$SELECTED = 'SELECTED';}else{$SELECTED = '';}
                                        echo "<option value=\"".$gtab_["tab_id"][$key]."\" $SELECTED>".$gtab_["table"][$key]."</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>

                    </div>
                </div>

                <hr>

                <div class="row">
                    <div class="col-6">

                    </div>
                    <div class="col-6 text-end">
                        <button class="btn btn-primary" type="button" onclick="document.form1.import_action.value=1;document.form1.submit();"><?=$lang[979]?></button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php if ($import_action) : ?>

<div class="card">
    <div class="card-body">
        
        <?php
/* 
 * System import
 * import limbas export files
*/
if($import_action AND $import_typ == "atm"){
    
    $fileatm = $_FILES['fileatm']['tmp_name'];
    $fileatm_name = $_FILES['fileatm']['name'];
    $fileatm_error = $_FILES['fileatm']['error'];
    
    # precheck of uploaded archive
    if($fileatm){
    	if(is_uploaded_file($fileatm)){
    		$pfad = $umgvar["pfad"]."/USER/".$session["user_id"]."/temp/";
    		rmdirr($pfad);
    		$copy = copy ($_FILES['fileatm']['tmp_name'], $pfad.$fileatm_name);
    		$sys = "tar -tz -C ".$pfad." -f \"".rtrim($pfad, '/')."/".$fileatm_name."\"";
    		$out = `$sys`;
    		if($out){
    			$out = explode("\n",$out);
    			if(in_array("export.conf",$out) AND in_array("export.dat",$out)){
    				$import_count = "single";
    			}elseif(lmb_count($out) > 1){
    				$import_count = "group";
    			}
    		}else{
    			echo "error while unpacking archive using the following command: <br><i>$sys</i>!";
    		}
    	}elseif($fileatm OR $fileatm_error){
    		echo "error while uploading archiv! $fileatm_error";
    	}
    }

    // import single or multible atm files
    if($import_count == 'single' OR $import_action == 2){
        
    	$result = import_tab_pool($import_typ,$import_overwrite,$import_count,1,$fileatm,$fileatm_name,null,null,null,$txt_encode);
    	
    	$imptabgroup = $result[0];
    	$existingfields = $result[1];
    	
    // preselect multible atm files
    }elseif($import_count == 'group'){
       // show form for selecting files
	   import_part_groupselect($fileatm,$fileatm_name);
    }

/* 
 * TEXT import
 * import text based files
*/
}elseif($import_action AND $import_typ == "txt"){
    
    // import and attach to existing table
    if($attach_gtabid){
        if($import_action == 1){
            $parsefile = import_parse_txt($txt_terminate,$txt_calculate,$txt_enclosure);
            $header = $parsefile["header"];
            import_attach_fieldmapping($attach_gtabid,$header,$ifield,'import file');
        }else{
            echo "<br>";
	        $result = import_attach_fromfile($ifield,$attach_gtabid,$txt_terminate,$txt_encode,$txt_enclosure);
            if ($result['false']) {
                echo"
        		<p style=\"color:red;\">&nbsp;&nbsp;".$result['false']." ".$lang[1012] . "</p><br>
        		<p style=\"color:green;\">&nbsp;&nbsp;".$result['true']." ".$lang[1017] . "</p>";
            } else {
                echo "<p style=\"color:green;\">&nbsp;&nbsp;".$result['true']." ".$lang[1017] . "</p>";
            }
	    }
    // import and create new table
    }else{
        // step 1 - merge fieldtypes
        if($import_action == 1){
            import_create_fieldmapping($ifield,$import_typ,null,null,$txt_terminate,$txt_enclosure,$txt_calculate,$txt_encode);
        // step 2 - import datasets
        }elseif($import_action == 2){
            $ifield = import_create_addtable($import_typ,$ifield,$add_permission,1);
            $result = import_create_filltable($import_typ,$ifield,$txt_terminate,$txt_encode = null, $txt_enclosure = null, 1);

            if ($result['false']) {
                echo"
        		<p style=\"color:red;\">&nbsp;&nbsp;".$result['false']." ".$lang[1012] . "</p><br>
        		<p style=\"color:green;\">&nbsp;&nbsp;".$result['true']." ".$lang[1017] . "</p>";
            } else {
                echo "<p style=\"color:green;\">&nbsp;&nbsp;".$result['true']." ".$lang[1017] . "</p>";
            }
        }
    }
}



?>


    </div>
</div>
    <?php endif; ?>
