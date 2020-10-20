<script src="extern/codemirror/lib/codemirror.js"></script>
<script src="extern/codemirror/edit/matchbrackets.js"></script>
<script src="extern/codemirror/edit/matchtags.js"></script>
<script src="extern/codemirror/mode/htmlmixed/htmlmixed.js"></script>
<script src="extern/codemirror/mode/xml/xml.js"></script>
<script src="extern/codemirror/mode/javascript/javascript.js"></script>
<script src="extern/codemirror/mode/css/css.js"></script>
<script src="extern/codemirror/mode/clike/clike.js"></script>
<script src="extern/codemirror/mode/php/php.js"></script>
<link rel="stylesheet" href="extern/codemirror/lib/codemirror.css">
<style>
    .CodeMirror {
        border: 1px solid<?=$farbschema['WEB3']?>;
        width: 600px;
        height: 300px;
    }
</style>
<script>
    let phpEditorResultText = '';
    let resultExecutionTimeHeader;
    let resultExecutionTime;
    let resultOutputHeader;
    let resultOutput;
    let resultErrorHeader;
    let resultError;

    $(function () {
        resultExecutionTimeHeader = $('#resultExecutionTimeHeader');
        resultExecutionTime = $('#resultExecutionTime');
        resultOutputHeader = $('#resultOutputHeader');
        resultOutput = $('#resultOutput');
        resultErrorHeader = $('#resultErrorHeader');
        resultError = $('#resultError');

        hideAll();

        $('#phpexec').click(executePhpCode);
        $('#htmlResult').change(changeOutputFormat);
    });

    /**
     * Hides all output elements
     */
    function hideAll() {
        resultExecutionTimeHeader.hide();
        resultOutputHeader.hide();
        resultOutput.hide();
        resultErrorHeader.hide();
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
                let error = '';
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
                    resultExecutionTimeHeader.show();
                    resultExecutionTime.text(executionTime + ' secs.');
                } else {
                    resultExecutionTimeHeader.hide();
                }

                // show output
                if (output) {
                    resultOutputHeader.show();
                    resultOutput.show();
                    if ($('#htmlResult').is(':checked')) {
                        resultOutput.html(output);
                    } else {
                        resultOutput.text(output);
                    }
                } else {
                    resultOutputHeader.hide();
                    resultOutput.hide();
                }

                // show error
                if (error) {
                    resultErrorHeader.show();
                    resultError.show();
                    resultError.html(error);
                } else {
                    resultErrorHeader.hide();
                    resultError.hide();
                }
            },
            error: function (_jqXHR, textStatus, errorThrown) {
                limbasWaitsymbol(null, 1, 1);
                alert(textStatus + ': ' + errorThrown);

                resultExecutionTimeHeader.hide();
                resultOutputHeader.hide();
                resultOutput.hide();
                resultErrorHeader.hide();
                resultError.hide();
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
        const resultOutput = $('#resultOutput');
        if ($(this).is(':checked')) {
            resultOutput.html(phpEditorResultText);
        } else {
            resultOutput.text(phpEditorResultText);
        }
    }
</script>

<div class="lmbPositionContainerMain">
    <form action="main_admin.php" method="post" name="form2">
        <input type="hidden" name="action" value="setup_php_editor">
        <input type="hidden" name="delete">
        <input type="hidden" name="phpFavoriteName">

        <table class="tabfringe" border="0" cellspacing="1" cellpadding="0">
            <?php if ($phpFavorites) { ?>
            <tr class="tabHeader"><td class="tabHeaderItem" colspan="5"><b><?=$lang[2932]?></b></td></tr>
            <tr class="tabBody">
                <td>
                    <select name="favorite" style="width: 200px;">
                        <?php
                        foreach ($phpFavorites as $favoriteID => $favoriteName){
                            $selected = ($favorite == $favoriteID) ? 'selected' : '';
                            echo "<option value='$favoriteID' $selected>$favoriteName</option>";
                        }
                        ?>
                    </select>
                </td>
                <td></td>
                <td><input type="submit" value="<?=$lang[1061]?>" name="showFavorite"></td>
                <td></td>
                <td><input type="button" onclick="if(confirm('<?=$lang[84]?>')){ document.form2.delete.value='favorite'; document.form2.submit(); }" value="<?=$lang[160]?>"></td>
            </tr>
            <?php } ?>
            <tr class="tabBody">
                <td class="tabHeaderItem" colspan="5"><b>PHP-Code</b></td>
            </tr>
            <tr class="tabBody">
                <td colspan="5">
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
                </td>
            </tr>
            <tr>
                <td colspan="5">
                    <input type="button" id="phpexec" value="<?= $lang[1065] ?>" name="phpexec">
                    <input type="button" value="<?=$lang[2218]?>" name="phpFavorite" onclick="const name = window.prompt('Name:'); document.form2.phpFavoriteName.value = name; document.form2.submit();">
                    <label>Show result as html<input type="checkbox" id="htmlResult"></label>
                    <div style="float: right">
                        <label>Max. execution time: <input type="number" id="maxExecutionSeconds" value="5"
                                                           style="width: 40px;"> seconds</label>
                    </div>
                </td>
            </tr>
            <tr>
                <td class="tabFooter" colspan="5">&nbsp;</td>
            </tr>
            <tr id="resultExecutionTimeHeader" style="display: none;">
                <td colspan="5">
                    <b>Execution time:</b>
                    <span id="resultExecutionTime"></span>
                </td>
            </tr>
            <tr id="resultOutputHeader" class="tabBody">
                <td class="tabHeaderItem" colspan="5"><b>Output</b></td>
            </tr>
            <tr>
                <td colspan="5">
                    <div id="resultOutput"
                         style="max-height:400px;overflow:auto;border:1px solid grey;padding:4px;"></div>
                </td>
            </tr>
            <tr>
                <td class="tabFooter" colspan="5">&nbsp;</td>
            </tr>
            <tr id="resultErrorHeader" class="tabBody">
                <td class="tabHeaderItem" colspan="5"><b>Errors</b></td>
            </tr>
            <tr>
                <td colspan="5">
                    <div id="resultError"
                         style="max-height:400px;overflow:auto;border:1px solid grey;padding:4px;"></div>
                </td>
            </tr>
        </table>
    </form>
</div>
