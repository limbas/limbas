<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */





if($view_preview){$view_section = 3;}
if(!$view_section){$view_section = 1;}

# save view options
if($options_save){
    lmb_QuestOptions($viewid,$options);
}


// get view infos from limbas
$gview = lmb_getQuestValue($viewid);



if($view_section == 1 OR $view_section == 2) {


    // generate SQL from editor
    $manually = true;
    if ($view_section == 2 AND ($act == 'view_save' OR $act == 'view_isvalid' OR $act == 'view_create' OR $act == 'view_replace')) {
        $view_def = lmb_questCreateSQL($viewid);
        $manually = false;
    }

    // save view definition
    if ($act == 'view_save') {
        lmb_saveViewDefinition($viewid, $view_def, null, $manually);
    } elseif ($act == 'view_isvalid') {
        lmb_saveViewDefinition($viewid, $view_def);
        if (lmb_precheckView($viewid, $view_def)) {
            $gview["isvalid"] = 1;
        }
    } elseif ($act == 'view_drop') {
        lmb_createQuestView($viewid, $view_def, $gview["ispublic"], 1);
    } elseif ($act == 'view_create') {
        lmb_saveViewDefinition($viewid, $view_def);
        if (lmb_precheckView($viewid, $view_def)) {
            $gview["isvalid"] = 1;
            lmb_createQuestView($viewid, $view_def, $gview["ispublic"]);
        }
    } elseif ($act == 'view_replace') {
        lmb_saveViewDefinition($viewid, $view_def);
        if (lmb_precheckView($viewid, $view_def)) {
            $gview["isvalid"] = 1;
            lmb_createQuestView($viewid, $view_def, $gview["ispublic"], null, 1);
        } else {

        }
    }


    if ($act == 'view_public') {
        // drop lmb_conf_fields
        if ($gview["ispublic"]) {
            lmb_QuestDeleteConfig($viewid);
            // create lmb_conf_fields
        } elseif ($gview['isvalid']) {
            lmb_QuestConfig($viewid, $view_group, $view_name);
        }
    }

    // get view infos from limbas after update
    $gview = lmb_getQuestValue($viewid);


}


?>



<!-- include codemirror with sql syntax highlighting and sql code completion -->
<script src="assets/vendor/codemirror/lib/codemirror.js?v=<?=$umgvar["version"]?>"></script>
<script src="assets/vendor/codemirror/addon/edit/matchbrackets.js?v=<?=$umgvar["version"]?>"></script>
<script src="assets/vendor/codemirror/addon/edit/matchtags.js?v=<?=$umgvar["version"]?>"></script>
<script src="assets/vendor/codemirror/mode/sql/sql.js?v=<?=$umgvar["version"]?>"></script>
<script src="assets/vendor/codemirror/addon/hint/show-hint.js?v=<?=$umgvar["version"]?>"></script>
<link rel="stylesheet" href="assets/vendor/codemirror/addon/hint/show-hint.css?v=<?=$umgvar["version"]?>">
<script src="assets/vendor/codemirror/addon/hint/sql-hint.js?v=<?=$umgvar["version"]?>"></script>
<link rel="stylesheet" href="assets/vendor/codemirror/lib/codemirror.css?v=<?=$umgvar["version"]?>">
<script src="assets/vendor/sql-formatter/sql-formatter.min.js?v=<?=$umgvar["version"]?>"></script>

<style>

.this_viewfieldform{
	border:none;
	background-color:transparent;
	width:100px;
	height:14px;
	overflow:hidden;
	text-align:left;
        float:left;
}

.this_viewfieldtd{
	border:1px solid grey;
	width:100px;
	height:16px;
}

</style>


<script>

var zoomKey = null;
var zoomEl = null;
function ZoomViewField(event,el,key) {
	zoomKey = key;
	zoomEl = el;

    limbasDivShow(el,event,'ZoomFieldContainer');

    zoomFieldCodeMirror.setValue(el.value);
    zoomFieldCodeMirror.focus();
    zoomFieldCodeMirror.refresh();

	return false;
}

function ZoomViewFieldUpdate(cm) {
    if (zoomEl) {
        zoomEl.value = cm.getValue();
        lmbAjax_EditViewfield(event,zoomKey,'field','key_'+zoomKey,zoomKey);
        divclose();
    }
}


$(function() {
    zoomFieldCodeMirror = CodeMirror.fromTextArea(document.getElementById("ZoomFieldArea"), {
        lineNumbers: true,
        matchBrackets: true,
        mode: "text/x-sql",
        indentWithTabs: true,
        smartIndent: true,
        autofocus: false,
        extraKeys: {
            "Ctrl-Space": "autocomplete"
        }
    });
    zoomFieldCodeMirror.on("blur", function(cm) {
        ZoomViewFieldUpdate(cm);
    });
});



</script>



<?php if(!$use_codemirror){$use_codemirror='true';}?>
<div class="container-fluid p-3">
    <FORM ACTION="main_admin.php" METHOD=post name="form1">
        <input type="hidden" name="action" value="setup_gtab_view">
        <input type="hidden" name="view_section" value="<?=$view_section?>">
        <input type="hidden" name="viewid" value="<?=$viewid?>">
        <input type="hidden" name="setdrag">
        <input type="hidden" name="setrelation">
        <input type="hidden" name="setviewfield">
        <input type="hidden" name="settype">
        <input type="hidden" name="showsystabs">
        <input type="hidden" name="view_save">
        <input type="hidden" name="options_save">
        <input type="hidden" name="act">        
        <input type="hidden" name="use_codemirror" value="<?=$use_codemirror?>">
        
        <ul class="nav nav-tabs">
            <li class="nav-item">
                <a class="nav-link <?= ($view_section == 1) ? 'active bg-contrast' : '" onclick="document.form1.view_section.value=\'1\';document.form1.submit();' ?>" href="#" ><?=$lang[2026]?></a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= ($view_section == 2) ? 'active bg-contrast' : '" onclick="document.form1.view_section.value=\'2\';document.form1.submit();' ?>" href="#" ><?=$lang[2612]?></a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= ($view_section == 3) ? 'active bg-contrast' : '" onclick="document.form1.view_section.value=\'3\';document.form1.submit();' ?>" href="#" ><?=$lang[1739]?></a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= ($view_section == 4) ? 'active bg-contrast' : '" onclick="document.form1.view_section.value=\'4\';document.form1.submit();' ?>" href="#" ><?=$lang[2795]?></a>
            </li>
            
            <?php if($gview["ispublic"]): ?>
                <li class="nav-item">
                    <a class="nav-link <?= ($view_section == 5) ? 'active bg-contrast' : '" onclick="document.form1.view_section.value=\'5\';document.form1.submit();' ?>" href="#" ><?=$lang[953]?></a>
                </li>
            <?php endif; ?>
        </ul>
        <div class="tab-content border border-top-0 bg-contrast">
            <div class="tab-pane active p-3">

                <div id="ZoomFieldContainer" class="ajax_container" style="position:absolute;visibility:hidden;width:600px;z-index:999;" onclick="activ_menu=1;">
                    <?php pop_closetop('ZoomFieldContainer'); ?>
                    <br>
                    <textarea id="ZoomFieldArea" style="overflow:auto;"></textarea>
                </div>

                <?php

                if($view_section == 1){
                    require('view/view_section1.php');
                }
                elseif($view_section == 2){
                    require('view/view_section2.php');
                }
                elseif($view_section == 3){
                    require('view/view_section3.php');
                }
                elseif($view_section == 4){
                    require('view/view_section4.php');
                }
                elseif($view_section == 5){
                    require('view/view_section5.php');
                }
                ?>

            </div>
        </div>



    </FORM>
</div>
