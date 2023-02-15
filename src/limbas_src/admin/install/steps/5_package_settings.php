<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */

/* --- data seeding settings --- 
 *
 * fills
 *      backupdir
 *  
 */

if (!defined('LIMBAS_INSTALL')) {
    return;
}


# get all demos
require_once(COREPATH . 'lib/include.lib');

unset($demoPaths);
if($folderval = read_dir(BACKUPPATH)){
    foreach($folderval["name"] as $key => $value){
        $demoPaths[] = $value;
    }
}

# sort demonames
if (lmb_count($demoPaths)){
    rsort($demoPaths);
}
?>

<script type="text/javascript">
    function onRadioChange() {
        document.getElementById('nextButton').className = document.getElementById('nextButton').className.replace('disabled','');
        document.getElementById('nextButton').type = 'submit';
    }

</script>


<?php
# check if clean install tar exists
$noCleanFound = false;
if($index = array_search("clean.tar.gz", $demoPaths)) {
    unset($demoPaths[$index]);
    ?>

    <div class="card mb-3">
        <div class="card-body">
            <div class="form-check">
                <input class="form-check-input" type="radio" name="backupdir" id="clean" checked value="clean.tar.gz" onchange="onRadioChange();">
                <label class="form-check-label" for="clean">
                    <?=lang('Install limbas without example values (ready for your application)')?>
                </label>
            </div>
        </div>
    </div>

    <?php
} else {
    $noCleanFound = true;
    ?>
        
    <div class="alert alert-warning" role="alert"><?=lang('No clean install archive found! You must install one of the following example values:')?></div>

    <?php
}
?>


<table class="table mb-3 table-striped bg-white border">  
    <thead>
        <tr>
            <th>
                <?=lang('Optionally select example values to install')?>
            </th>
        </tr>
    </thead>
    <tbody>
        <?php
            $noDemoFound = false;
            # demo radio buttons
            if(lmb_count($demoPaths) > 0){
            if($noCleanFound){$checked = 'checked';}
            foreach($demoPaths as $demoName) {
                if(lmb_substr($demoName, -7) == '.tar.gz') {
                    $demoFileName = lmb_substr($demoName, 0, lmb_strlen($demoName) - 7);
                    ?>

                    <tr>
                        <td>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="backupdir" id="<?= $demoFileName ?>" <?= $checked?> value="<?= urlencode($demoName) ?>" onchange="onRadioChange();">
                                <label class="form-check-label" for="<?= $demoFileName ?>">
                                    <?= $demoFileName ?>
                                </label>
                            </div>
                        </td>
                    </tr>

                    <?php
                    $checked = '';
                }
            }
            }else{
                
                ?><div class="alert alert-warning" role="alert"><?=lang('No example install archive found!')?></div><?php
                
                $noDemoFound = true;
            }
        ?>
    </tbody>
</table>

<div class="card">
    <div class="card-body">
        <div class="row">
            <div class="col-6">
                <button type="button" class="btn btn-outline-dark" onclick="switchToStep('<?= $stepKeys[$currentStep-1]  ?>')">Back</button>
            </div>
            <div class="col-6 text-end">
                <?php
                $tooltip = '';
                $disabled = '';
                $nextStep = 1;
                $text = lang('Next step');
                if($noCleanFound AND $noDemoFound) {
                    $tooltip = 'title="All required functions must work before continuing!"';
                    $disabled = 'disabled';
                    $nextStep--;
                    $text = lang('Reload');
                }
                ?>
                <button type="submit" class="btn btn-primary" <?= $disabled ?> <?= $tooltip ?> name="install" value="<?= $stepKeys[$currentStep+$nextStep] ?>"><?= $text ?></button>
            </div>

        </div>
    </div>
</div>
    


