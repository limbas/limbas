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
function change_mimetype(id) {
	document.form1.changeIds.value = document.form1.changeIds.value + ";" + id;
}
function change_active() {
	document.form1.changeactive.value = '1';
}
function del_mimetype(id) {
    if (confirm('<?= $lang[84] ?>')) {
        document.form1.del.value = id;
        document.form1.submit();
    }
}
</script>

<form action="main_admin.php" method="post" name="form1">
    <input type="hidden" name="action" value="setup_mimetypes">
    <input type="hidden" name="changeIds">
    <input type="hidden" name="del">
    <input type="hidden" name="changeactive">

    <div class="container-fluid p-3">

        <table class="table table-sm table-striped mb-0 border bg-white">
            <thead>
            <tr>
                <th>ID</th>
                <th>Mimetype</th>
                <th>Ext</th>
                <th colspan="2">Pic</th>
                <th>Delete</th>
                <th>active</th>
            </tr>
            </thead>

            <tbody>
            <?php foreach($result_mimetypes['id'] as $index => $id) : ?>

                <tr>
                    <!-- id -->
                    <td>
                        <?=$id?>
                    </td>
                    <!-- mimetype -->
                    <td>
                        <input type="text" name="mimetype_<?=$id?>" value="<?= $result_mimetypes['mimetype'][$index]?>" onchange="change_mimetype('<?= $id ?>');" class="form-control form-control-sm">
                    </td>
                    <!-- ext -->
                    <td>
                        <input type="text" name="ext_<?=$id?>" value="<?= $result_mimetypes['ext'][$index]?>" onchange="change_mimetype('<?= $id ?>');" class="form-control form-control-sm">
                    </td>
                    <!-- pic -->
                    <td>
                        <?php
                        $filename = $result_mimetypes['pic'][$index];
                        if($filename && file_exists(ASSETSPATH . "images/legacy/fileicons/$filename")) {
                            echo "&nbsp;&nbsp;<img src=\"assets/images/legacy/fileicons/$filename\">";
                        }
                        ?>
                    </td>
                    <td>
                        <input type="text" name="pic_<?=$id?>" value="<?= $result_mimetypes['pic'][$index]?>" onchange="change_mimetype('<?= $id ?>');" class="form-control form-control-sm">
                    </td>
                    <!-- delete -->
                    <td>
                        <i class="lmb-icon lmb-trash" title="delete mimetype <?=$result_mimetypes['mimetype'][$index]?>" OnClick="del_mimetype('<?= $id ?>')"></i>
                    </td>
                    <!-- active -->
                    <td>
                        <input class="form-check-input" type="checkbox" name="active[<?=$id?>]" onchange="change_active();" <?php echo ($result_mimetypes['active'][$index] ? 'checked' : '') ?>>
                    </td>
                </tr>

                <?php endforeach; ?>
            </tbody>


            <tfoot>
            <tr class="border-bottom border-top">
                <td colspan="4">
                    <button class="btn btn-sm btn-primary" type="submit" name="change" value="1"><?= $lang[522] ?></button>
                </td>
            </tr>


            <tr>
                <td></td>
                <td><input type="text" name="new_mimetype" class="form-control form-control-sm"></td>
                <td><input type="text" name="new_ext" class="form-control form-control-sm"></td>
                <td></td>
                <td><input type="text" name="new_pic" class="form-control form-control-sm"></td>
                <td><button class="btn btn-sm btn-primary" type="submit" name="add" value="1"><?= $lang[540] ?></button></td>
            </tr>

            </tfoot>




        </table>

    </div>
    
    
</form>
