<script>
    function configPrinter(id) {
        document.form1.configPrinter.value = id;
        document.form1.submit();
    }

    function delPrinter(id) {
        if (confirm('Delete printer?')) {
            document.form1.delPrinter.value = id;
            document.form1.submit();
        }
    }

    function changePrinter(id) {
        document.form1.changePrinter.value = id;
        document.form1.submit();
    }
</script>
<div class="lmbPositionContainerMain">
    <form name="form1" action="main_admin.php" method="post">
        <input type="hidden" name="action" value="setup_printers">
        <input type="hidden" name="delPrinter" value="">
        <input type="hidden" name="configPrinter" value="<?= $configPrinter ?>">
        <input type="hidden" name="changePrinter" value="">
        <?php if ($configPrinter): /* --------- Printer Config ----------*/
            $availableConfig = $availablePrinterConfigs[$printers[$configPrinter]['sysName']];
            $currentConfig = $printers[$configPrinter]['config'];

            $parsedConfig = parseConfig($currentConfig);

            ?>
            <script>
                function selectOption(key, value) {
                    var configInput = $('#newConfig');
                    var optionsStr = configInput.val();
                    var regex = new RegExp('-o ' + key + '=(.*?)( |$)', 'i');
                    if (value) {
                        if (value.substr(0, 1) === "*") {
                            value = value.substr(1);
                        }
                        if (optionsStr.indexOf('-o ' + key + '=') < 0) {
                            // add option
                            optionsStr += ' -o ' + key + '=' + value;
                        } else {
                            // replace option value
                            optionsStr = optionsStr.replace(regex, '-o ' + key + '=' + value + ' ')
                        }
                    } else {
                        // remove option
                        optionsStr = optionsStr.replace(regex, '')
                    }
                    configInput.val(optionsStr.trim()).change();
                }
                $(function() {
                    $('#newConfig')
                        .on('change', function() {
                            $('#command').html('lp <?= strval($umgvar['lp_params']) ?> -d <?= $printers[$configPrinter]['sysName'] ?> ' + $(this).val() + ' &lt;file&gt;');
                        })
                        .change();
                });
            </script>
            <p>
                <label>
                    Current config:
                    <input type="text" value="<?= $currentConfig ?>" readonly style="width: 100%">
                </label>
            </p>
            <p>
                <label>
                    Selected config:
                    <input type="text" name="selectedConfig" value="<?= $currentConfig ?>" id="newConfig" style="width: 100%">
                </label>
            </p>
            <p style="font-family: monospace">
                <b>&gt;</b>
                <span id="command"></span>
            </p>
            <input type="submit" style="background-color: #9ff19f;" name="saveSelectedConfig" value="Save selected config">
            <input type="submit" style="background-color: #ffbcbc;" name="abortConfig" onclick="document.form1.configPrinter.value='';" value="Abort">
            <hr>
            <table>
                <?php foreach ($availableConfig as $key => $values):
                    # split into system key / readable key
                    list($sysKey, $userKey) = explode('/', $key, 2); ?>

                    <tr>
                        <td><?= $userKey ?> (<b><?= $sysKey ?></b>)</td>
                        <td>
                            <select onchange="selectOption('<?= $sysKey ?>', this.value);" style="width: 100%;">
                                <option></option>
                                <?php foreach ($values as $value): ?>
                                    <option <?= ($parsedConfig[$sysKey] and ($parsedConfig[$sysKey] == $value or ('*' . $parsedConfig[$sysKey]) == $value)) ? 'selected' : '' ?>><?= $value ?></option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>

        <?php else: /* --------- Printer Overview ----------*/ ?>


            <table class="tabfringe" cellspacing="0" cellpadding="3" style="width: 100%;">
                <tr class="tabHeader">
                    <td class="tabHeaderItem">ID</td>
                    <td class="tabHeaderItem">Default</td>
                    <td class="tabHeaderItem">Description</td>
                    <td class="tabHeaderItem">System Name</td>
                    <td class="tabHeaderItem">Config</td>
                    <td class="tabHeaderItem">Delete</td>
                </tr>
                <?php foreach ($printers as $id => $printer): ?>
                    <tr class="tabBody" <?= $printer['error'] ? 'style="background-color: #ffbcbc;"' : '' ?>>
                        <td><?= $id ?></td>
                        <td><input type="radio" name="defaultPrinter" value="<?= $id ?>" onchange="document.form1.submit();" <?= $printer['default'] ? 'checked' : ''?>></td>
                        <td><input type="text" name="printerName_<?= $id ?>" value="<?= $printer['name'] ?>" onchange="window.changePrinter(<?= $id ?>)"></td>
                        <td><?= $printer['sysName'] ?></td>
                        <td style="width: 100%;">
                            <i class="lmb-icon lmb-cog" onclick="configPrinter(<?= $id ?>)"></i>
                            <?= $printer['config'] ?>
                        </td>
                        <td><i class="lmb-icon lmb-trash" onclick="delPrinter(<?= $id ?>)"></i></td>
                    </tr>
                <?php endforeach; ?>
                <tr class="tabFooter">
                    <td></td>
                    <td></td>
                    <td><input type="text" name="addPrinterName"></td>
                    <td>
                        <select name="addPrinterSysName">
                            <option></option>
                            <?php foreach (array_keys($availablePrinterConfigs) as $printerSysName): ?>
                                <option><?= $printerSysName ?></option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                    <td colspan="2"><input type="submit" name="addPrinter" value="Add Printer"></td>
                </tr>

            </table>
        <?php endif; ?>
    </form>
</div>
