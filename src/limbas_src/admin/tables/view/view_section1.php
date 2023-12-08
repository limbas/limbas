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

    <?php if($use_codemirror == 'true') {?>

        var editor;

        function formatSQL() {
            editor.setValue(sqlFormatter.format(editor.getValue(), {indent: "    "}));
        }

        $(function () {
            editor = CodeMirror.fromTextArea(document.getElementById("view_def"), {
                lineNumbers: true,
                lineWrapping: true,
                matchBrackets: true,
                mode: "text/x-sql",
                indentWithTabs: true,
                smartIndent: true,
                autofocus: true,
                extraKeys: {
                    "Ctrl-Space": "autocomplete"
                }
            });
            $('.CodeMirror').css('height','auto');
            let $cm_s = $('.CodeMirror-scroll')
            $cm_s.css('max-height','77vh');
            editor.on('blur', formatSQL);
            formatSQL();
        });

    <?php }?>

</script>


<h3><?=$gview["viewname"]?></h3>

<div class="w-100 h-100 border">
    <textarea id="view_def" name="view_def" <?php if($use_codemirror != 'true'){echo 'class="form-control" style="height:300px;"';}?>><?=htmlentities($gview["viewdef"],ENT_QUOTES,$GLOBALS["umgvar"]["charset"])?></textarea>
</div>



<div class="row mt-3">
    <div class="col-md-4">
        <?=$lang[1996]?><?php if ($gview["ispublic"]) {echo "<i class=\"lmb-icon lmb-check\"></i>";}else{echo "<i class=\"lmb-icon lmb-minus-circle\"></i>";}?>&nbsp;
        <?=$lang[2023]?><?php if ($gview["viewexists"]) {echo "<i class=\"lmb-icon lmb-check\"></i>";}else{echo "<i class=\"lmb-icon lmb-minus-circle\"></i>";}?>&nbsp;
        Syntax<?php if ($gview["isvalid"]) {echo "<i class=\"lmb-icon lmb-check\"></i>";}else{echo "<i class=\"lmb-icon lmb-minus-circle\"></i>";}?>&nbsp;
        &nbsp;&nbsp;&nbsp;format code: <input type="checkbox" class="align-middle" value="2"  onchange="document.form1.use_codemirror.value=this.checked; document.form1.submit();" <?=($use_codemirror == 'true')?'checked':''?>>
    </div>
    <div class="col-md-4 text-center">
        <button type="button" class="btn btn-sm btn-outline-dark" onclick="document.form1.act.value='view_save';setDrag();document.form1.submit();"><?=$lang[2940]?></button>
        <button type="button" class="btn btn-sm btn-outline-dark" onclick="document.form1.act.value='view_isvalid';setDrag();document.form1.submit();"><?=$lang[2941]?></button>
    </div>
    <div class="col-md-4 text-end">
        <?php
        if ($gview["viewexists"]):
            
            if($gview["ispublic"]){$ispublic_lang = $lang[2943];}else{$ispublic_lang = $lang[1996];}

            ?>
            <button type="button" class="btn btn-sm btn-outline-dark" onclick="document.form1.act.value='view_public';document.form1.submit();" <?=(!$gview["isvalid"])?'disabled':''?>><?=$ispublic_lang?></button>
            <button type="button" class="btn btn-sm btn-outline-dark" onclick="document.form1.act.value='view_replace';document.form1.submit();"><?=$lang[2942]?></button>
            <button type="button" class="btn btn-sm btn-outline-dark" onclick="document.form1.act.value='view_drop';document.form1.submit();"><?=$lang[2023] . " " . $lang[160]?></button>
            
        <?php else: ?>

            <button type="button" class="btn btn-sm btn-outline-dark" onclick="document.form1.act.value='view_create';document.form1.submit();"><?=$lang[2942]?></button>
            
        <?php endif; ?>
    </div>
    
</div>
