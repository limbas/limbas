<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */
?>
<script src="assets/vendor/codemirror/lib/codemirror.js"></script>
<script src="assets/vendor/codemirror/addon/edit/matchbrackets.js"></script>
<script src="assets/vendor/codemirror/addon/edit/matchtags.js"></script>
<script src="assets/vendor/codemirror/mode/htmlmixed/htmlmixed.js"></script>
<script src="assets/vendor/codemirror/mode/xml/xml.js"></script>
<script src="assets/vendor/codemirror/mode/javascript/javascript.js"></script>
<script src="assets/vendor/codemirror/mode/css/css.js"></script>
<script src="assets/vendor/codemirror/mode/clike/clike.js"></script>
<script src="assets/vendor/codemirror/mode/php/php.js"></script>
<link rel="stylesheet" href="assets/vendor/codemirror/lib/codemirror.css">
<script>
    let phpEditorResultText = '';
    let resultWrapper;
    let resultExecutionTime;
    let resultOutput;
    let resultError;

    $(function () {
        resultExecutionTime = $('#resultExecutionTime');
        resultWrapper = $('#resultWrapper');
        resultWrapper.hide();
        resultOutput = $('#resultOutput');
        resultError = $('#resultError');

        hideAll();

        $('#phpexec').click(executePhpCode);
        $('#htmlResult').change(changeOutputFormat);
    });

    /**
     * Hides all output elements
     */
    function hideAll() {
        resultWrapper.hide();
        resultOutput.hide();
        resultError.hide();
    }

    /**
     * Executes the entered php code via ajax
     */
    function executePhpCode() {
        hideAll();
        showLoadingIcon();

        $.ajax({
            type: 'POST',
            url: 'main_dyns_admin.php',
            data: {
                actid: 'executePhpCode',
                phpCode: editor.getValue(),
                maxExecutionSeconds: $('#maxExecutionSeconds').val()
            },
            success: function (data) {
                limbasWaitsymbol(null, 1, 1);

                let executionTime = '';
                let output = '';
                let error;
                try {
                    const dataObj = JSON.parse(data);
                    executionTime = dataObj.executionTime;
                    output = dataObj.output;
                    error = dataObj.error;
                } catch (e) {
                    error = data.replace(/(?:\r\n|\r|\n)/g, '<br>');
                }

                // store plain result for later
                phpEditorResultText = output;

                // show execution time
                if (executionTime) {
                    resultExecutionTime.text(executionTime + ' secs.');
                }

                // show output
                if (output) {
                    resultOutput.show();
                    if ($('#htmlResult').is(':checked')) {
                        resultOutput.find('.htmlcontent').html(output);
                    } else {
                        resultOutput.find('.htmlcontent').text(output);
                    }
                } else {
                    resultOutput.hide();
                }

                // show error
                if (error.length > 0) {
                    resultError.show();
                    resultError.find('.htmlcontent').html(error);
                } else {
                    resultError.hide();
                }

                resultWrapper.show();
            },
            error: function (_jqXHR, textStatus, errorThrown) {
                limbasWaitsymbol(null, 1, 1);
                // alert(textStatus + ': ' + errorThrown);
                lmbShowErrorMsg(textStatus + ': ' + errorThrown);

                resultWrapper.hide();
            }
        });
    }

    /**
     * Shows lmb loading icon above editor
     */
    function showLoadingIcon() {
        const rect = editor.display.scroller.getBoundingClientRect();
        const fakeEvent = {
            pageX: rect.x + rect.width / 2,
            pageY: rect.y + rect.height / 2
        };
        limbasWaitsymbol(fakeEvent, 1, 0);
    }

    /**
     * Toggles code output format between "html" and "plaintext" depending on checkbox state
     * @this checkbox
     */
    function changeOutputFormat() {
        const resultOutput = $('#resultOutput').find('.htmlcontent');
        if ($(this).is(':checked')) {
            resultOutput.html(phpEditorResultText);
        } else {
            resultOutput.text(phpEditorResultText);
        }
    }
</script>


<div class="container-fluid p-3">
    <div class="card mb-3">
        <div class="card-body">

            <form action="main_admin.php" method="post" name="form2">
                <input type="hidden" name="action" value="setup_php_editor">
                <input type="hidden" name="delete">
                <input type="hidden" name="phpFavoriteName">

                

                <?php if ($phpFavorites) : ?>
                    <h3><?=$lang[2932]?></h3>
                    <div class="row mb-2">
                        <div class="col-6">
                            <select name="favorite" class="form-select form-select-sm">
                                <?php
                                foreach ($phpFavorites as $favoriteID => $favoriteName){
                                    $selected = ($favorite == $favoriteID) ? 'selected' : '';
                                    echo "<option value='$favoriteID' $selected>$favoriteName</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col-6">
                            <button class="btn btn-primary btn-sm" type="submit" name="showFavorite" value="<?=$lang[1061]?>"><?=$lang[1061]?></button>
                            <button class="btn btn-primary btn-sm" type="button" onclick="if(confirm('<?=$lang[84]?>')){ document.form2.delete.value='favorite'; document.form2.submit(); }"><?=$lang[160]?></button>
                        </div>
                    </div>

                <?php endif; ?>

                <h3>PHP-Code</h3>
                <div class="mb-3 border">
                    <textarea id="phpvalue" name="phpvalue"><?= $phpvalue ?></textarea>
                    <script language="JavaScript">
                        var editor = CodeMirror.fromTextArea(document.getElementById("phpvalue"), {
                            lineNumbers: true,
                            matchBrackets: true,
                            mode: "application/x-httpd-php-open",
                            indentUnit: 4,
                            indentWithTabs: true,
                            autofocus: true,
                            extraKeys: {
                                "Ctrl-Enter": function () {
                                    $("#phpexec").trigger("click");
                                },
                                "Ctrl-Space": "autocomplete"
                            }
                        });
                    </script>
                </div>

                <div class="row">
                    <div class="col-sm-8">
                        <button class="btn btn-primary btn-sm" id="phpexec" type="button" value="<?=$lang[1065]?>" name="phpexec"><?=$lang[1065]?></button>
                        <button class="btn btn-primary btn-sm" type="button" name="phpFavorite" onclick="const name = window.prompt('Name:'); document.form2.phpFavoriteName.value  = name; document.form2.submit();"><?=$lang[2218]?></button>

                        <label>Show result as html<input type="checkbox" class="align-middle" id="htmlResult" >
                    </div>
                    <div class="col-sm-4 text-end text-nowrap">
                        <label>Max. execution time: <input type="number" name="maxExecutionSeconds" class="form-control form-control-sm d-inline-block w-auto align-middle" min="0" value="5"> seconds</label>
                    </div>
                </div>
            </form>

            


        </div>
    </div>

    <div class="card" id="resultWrapper">
        <div class="card-body">
            <p><span class="fw-bold">Execution time:</span><span id="resultExecutionTime"></span></p>

            <div id="resultOutput" class="mb-3">
                <h3>Output</h3>
                <div class="htmlcontent border p-2"></div>
            </div>
            
            <div id="resultError">
                <h3>Errors</h3>
                <div class="htmlcontent border p-2"></div>
            </div>
        </div>
    </div>
    
</div>
