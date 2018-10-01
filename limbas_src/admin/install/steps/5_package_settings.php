<?php
# get all demos
require_once("../../lib/include.lib");
require_once("../../lib/include_string.lib");

unset($demoPaths);
if($folderval = read_dir($setup_path_project."/BACKUP")){
    foreach($folderval["name"] as $key => $value){
        $demoPaths[] = $value;
    }
}

# sort demonames
if (count($demoPaths)){
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
if($index = array_search("clean.tar.gz", $demoPaths)) {
    unset($demoPaths[$index]);
    ?>

    <table class="table table-condensed borderless">
        <tbody>
            <tr>
                <td>
                    <div class="radio">
                        <label style="display:block;">
                            <input type="radio" checked name="backupdir" value="clean.tar.gz">Install limbas without example values (ready for your application)
                        </label>
                    </div>
                </td>
            </tr>
        </tbody>
    </table>

    <?php
} else {
    $noCleanFound = 1;
    ?>
        
    <div class="alert alert-warning" role="alert">No clean install archive found! You must install one of the following example values:</div>

    <?php
}
?>


<table class="table table-condensed">  
    <thead>
        <tr>
            <th>
                Optionally select example values to install
            </th>
        </tr>
    </thead>
    <tbody>
        <?php
            # demo radio buttons
            if(count($demoPaths) > 0){
            if($noCleanFound){$checked = 'checked';}
            foreach($demoPaths as $demoName) {
                if(lmb_substr($demoName, -7) == '.tar.gz') {
                    $demoFileName = lmb_substr($demoName, 0, lmb_strlen($demoName) - 7);
                    ?>

                    <tr>
                        <td>
                            <div class="radio">
                                <label style="display:block;">
                                    <input type="radio" name="backupdir" <?= $checked?> value="<?= urlencode($demoName) ?>" onchange="onRadioChange();"> <?= $demoFileName ?>
                                </label>
                            </div>
                        </td>
                    </tr>

                    <?php
                    $checked = '';
                }
            }
            }else{
                
                ?><div class="alert alert-warning" role="alert">No example install archive found!</div><?php
                
                $noDemoFound = 1;
            }
        ?>
    </tbody>
</table>

<div>    
    <button type="button" class="btn btn-default" onclick="switchToStep('<?= array_keys($steps)[4] ?>')">Back</button>
    <?php
    $nextStep = 6;
    $text = "Next step";
    if($noCleanFound AND $noDemoFound) {
        $tooltip = "title=\"All required functions must work before continuing!\"";
        $disabled = "disabled";
        $nextStep--;
        $text = "Reload";
    }
    ?>
    <button type="submit" class="btn btn-info pull-right <?= $disabled ?>" <?= $tooltip ?> name="install" value="<?= array_keys($steps)[$nextStep] ?>"><?= $text ?></button>
</div>

    


