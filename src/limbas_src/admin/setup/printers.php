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




<form name="form1" action="main_admin.php" method="post">
    <input type="hidden" name="action" value="setup_printers">
    <input type="hidden" name="delPrinter" value="">
    <input type="hidden" name="configPrinter" value="<?= $configPrinter ?>">
    <input type="hidden" name="changePrinter" value="">



    <div class="container-fluid p-3">

        <?php if(!empty($printerMsg)): ?>
            <div class="alert alert-danger">
                <?=implode('<br>',$printerMsg)?>
            </div>
        <?php endif; ?>




    <?php 
    
    //TODO: LAYOUT
    if ($configPrinter): /* --------- Printer Config ----------*/
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
        
        <div class="card">
            <div class="card-header">
                <?=$printers[$configPrinter]['sysName']?>
            </div>
            <div class="card-body">

                <div class="mb-3">
                    <label for="currentConfig" class="form-label">Current config</label>
                    <input type="text" class="form-control" id="currentConfig" value="<?= $currentConfig ?>" readonly>
                </div>

                <div class="mb-3">
                    <label for="newConfig" class="form-label">Selected config</label>
                    <input type="text" class="form-control" name="selectedConfig" id="newConfig" value="<?= $currentConfig ?>">
                </div>

                <p class="font-monospace">
                    <b>&gt;</b>
                    <span id="command"></span>
                </p>
            </div>

            <table class="table table-sm">
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
            
            
            <div class="card-footer text-end">
                <button class="btn btn-outline-secondary" type="submit" name="abortConfig" value="1" onclick="document.form1.configPrinter.value='';">Abort</button>
                <button class="btn btn-primary" type="submit" name="saveSelectedConfig" value="1">Save selected config</button>
            </div>
        </div>
        

    <?php else: /* --------- Printer Overview ----------*/ ?>


        <table class="table table-sm table-striped mb-0 border bg-white">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Default</th>
                    <th>Description</th>
                    <th>System Name</th>
                    <th>Config</th>
                    <th>Delete</th>
                </tr>
            </thead>

            <tbody>
            <?php foreach ($printers as $id => $printer): ?>
                <tr <?= $printer['error'] ? 'class="text-danger"' : '' ?>>
                    <td><?= $id ?></td>
                    <td><input type="radio" name="defaultPrinter" value="<?= $id ?>" onchange="document.form1.submit();" <?= $printer['default'] ? 'checked' : ''?> class="form-control form-control-sm"></td>
                    <td><input type="text" name="printerName_<?= $id ?>" value="<?= $printer['name'] ?>" onchange="window.changePrinter(<?= $id ?>)" class="form-control form-control-sm"></td>
                    <td><?= $printer['sysName'] ?></td>
                    <td>
                        <i class="lmb-icon lmb-cog cursor-pointer" onclick="configPrinter(<?= $id ?>)"></i>
                        <?= $printer['config'] ?>
                    </td>
                    <td><i class="lmb-icon lmb-trash cursor-pointer" onclick="delPrinter(<?= $id ?>)"></i></td>
                </tr>
            <?php endforeach; ?>
            </tbody>


            <tfoot>

            <TR>
                <td></td>
                <td></td>
                <TD><input type="TEXT" name="addPrinterName" class="form-control form-control-sm"></TD>
                <TD>
                    <select name="addPrinterSysName" class="form-select form-select-sm">
                        <option></option>
                        <?php foreach (array_keys($availablePrinterConfigs) as $printerSysName): ?>
                            <option><?= $printerSysName ?></option>
                        <?php endforeach; ?>
                    </select>
                </TD>
                <td></td>
                <td><button class="btn btn-sm btn-primary" type="submit" name="addPrinter" value="1"><?= $lang[540] ?></button></td>
            </TR>

            </tfoot>
            

        </table>
    <?php endif; ?>

        

    </div>
</form>
