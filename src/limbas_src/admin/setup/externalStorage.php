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
    function delStorage(storageID) {
        if (confirm('<?= $lang[84] ?>')) {
            document.form1.deleteID.value = storageID;
            document.form1.submit();
        }
    }

    function changeStorage(storageID) {
        if (document.form1.changedIDs.value.indexOf(';' + storageID) === -1) {
            document.form1.changedIDs.value += ';' + storageID;
        }
    }

    function selectClass(className) {
        if (className === 'Extension') {
            $('#newClass').val('').show();
        } else {
            $('#newClass').val(className).hide();
        }
        const $configDiv = $('#newConfig_' + className);
        $configDiv.parent().children().hide();
        $configDiv.show();
    }

    $(function () {
        selectClass('Extension');
    })
</script>

<form action="main_admin.php" method="post" name="form1">
    <input type="hidden" name="action" value="setup_external_storage">
    <input type="hidden" name="deleteID">
    <input type="hidden" name="changedIDs">
    <div class="container-fluid p-3">
        <table class="table table-sm table-striped mb-0 border bg-contrast">
            <thead>
            <tr>
                <th><?= $lang[949] ?></th>
                <th></th>
                <th><?= $lang[126] ?></th>
                <th>Classname</th>
                <th>Config</th>
                <th>External Access url</th>
                <th>Public cloud</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($externalStorage['desc'] as $storageID => $storageDesc) :
                $onchange = "onchange=\"changeStorage($storageID)\"";
                ?>
                <tr>
                    <td><?= $storageID ?></td>
                    <td><i class="lmb-icon lmb-trash" onclick="delStorage(<?= $storageID ?>)"
                           title="<?= $lang[160] ?>"></i></td>
                    <td><input type="text" <?= $onchange ?> name="desc_<?= $storageID ?>" value="<?= $storageDesc ?>">
                    </td>
                    <td><input type="text" <?= $onchange ?> name="className_<?= $storageID ?>"
                               value="<?= $externalStorage['className'][$storageID] ?>"
                               title="Lmb_Cloud_<?= $externalStorage['className'][$storageID] ?>"></td>
                    <td><input type="text" <?= $onchange ?> name="config_<?= $storageID ?>"
                               value="<?= htmlentities($externalStorage['config'][$storageID], ENT_QUOTES, $GLOBALS["umgvar"]["charset"]) ?>">
                    </td>
                    <td><input type="text" <?= $onchange ?> name="externalAccessUrl_<?= $storageID ?>"
                               value="<?= htmlentities($externalStorage['externalAccessUrl'][$storageID], ENT_QUOTES, $GLOBALS["umgvar"]["charset"]) ?>">
                    </td>
                    <td><input type="checkbox" <?= $onchange ?>
                               name="publicCloud_<?= $storageID ?>" <?= (($externalStorage['publicCloud'][$storageID]) ? 'checked' : '') ?>>
                    </td>
                    <td></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
            <tfoot>
            <tr class="border-bottom border-top">
                <td colspan="8">
                    <button class="btn btn-sm btn-primary" type="submit" name="change"
                            value="1"><?= $lang[522] ?></button>
                </td>
            </tr>

            <tr>
                <td></td>
                <td></td>
                <td>
                    <input class="form-control form-control-sm" required type="text" name="newDesc">
                </td>
                <td>
                    <div class="d-flex flex-column">
                        <select onchange="selectClass(this.value);" class="form-select form-select-sm"
                                NAME="newExternalStorage">
                            <option value="Extension">Extension</option>
                            <option value="OwnCloud">OwnCloud</option>
                            <option value="SeaFile">SeaFile</option>
                            <option value="Dropbox">Dropbox</option>
                            <option value="Filesystem">Filesystem</option>
                        </select>
                        <input type="text" id="newClass" name="newClass" required class="form-control form-control-sm">
                    </div>
                </td>
                <td>
                    <div id="newConfig_Extension">
                        <input type="text" name="newConfig" class="form-control form-control-sm">
                    </div>
                    <div id="newConfig_OwnCloud">
                        <label>Url: <input type="text" name="newConfig_OwnCloud_Url"
                                           class="form-control form-control-sm"></label>
                        <label>User: <input type="text" name="newConfig_OwnCloud_User"
                                            class="form-control form-control-sm"></label>
                        <label>Pass: <input type="text" name="newConfig_OwnCloud_Pass"
                                            class="form-control form-control-sm"></label>
                        <label>Folder: <input type="text" name="newConfig_OwnCloud_Folder"
                                              class="form-control form-control-sm"></label>
                    </div>
                    <div id="newConfig_SeaFile">
                        <label>Url: <input type="text" name="newConfig_SeaFile_Url"
                                           class="form-control form-control-sm"></label>
                        <label>User: <input type="text" name="newConfig_SeaFile_User"
                                            class="form-control form-control-sm"></label>
                        <label>Pass: <input type="text" name="newConfig_SeaFile_Pass"
                                            class="form-control form-control-sm"></label>
                        <label>Folder: <input type="text" name="newConfig_SeaFile_Folder"
                                              class="form-control form-control-sm"></label>
                        <label>Repo: <input type="text" name="newConfig_SeaFile_Repo"
                                            class="form-control form-control-sm"></label>
                    </div>
                    <div id="newConfig_Dropbox">
                        <label>Token: <input type="text" name="newConfig_Dropbox_Token"
                                             class="form-control form-control-sm"></label>
                    </div>
                    <div id="newConfig_Filesystem">
                        <label>Path: <input type="text" name="newConfig_Filesystem_Path"
                                            class="form-control form-control-sm"></label>
                    </div>
                </td>
                <td>
                    <input class="form-control form-control-sm" type="text" name="newExternalAccessUrl">
                </td>
                <td>
                    <input type="checkbox" name="newPublicCloud">
                </td>
                <td>
                    <button class="btn btn-sm btn-primary" type="submit" name="add" value="1"><?= $lang[540] ?></button>
                </td>
            </tr>
            </tfoot>
        </table>
    </div>
</form>
