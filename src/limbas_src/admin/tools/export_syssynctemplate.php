<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */



?>


<?php

// delete Template
if($edit_template AND $edit_template_value=='delete'){

    $sqlquery = "DELETE FROM LMB_SYNCSTRUCTURE_TEMPLATE WHERE ID = ".parse_db_int($edit_template);
    $rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);

// edit Template
}else if($edit_template AND $edit_template_value){

    $sqlquery = "UPDATE LMB_SYNCSTRUCTURE_TEMPLATE SET NAME = '".parse_db_string($edit_template_value,50)."' WHERE ID = ".parse_db_int($edit_template);
    $rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
}

// add new Template
if($new_synctemplate_name AND $add_synctemplate){

    $tosync = json_encode($syncmodule);

    $nid = next_db_id("LMB_SYNCSTRUCTURE_TEMPLATE");
    $sqlquery = "INSERT INTO LMB_SYNCSTRUCTURE_TEMPLATE (ID,NAME,MODUL) VALUES($nid,'".parse_db_string($new_synctemplate_name,50)."','".parse_db_string($tosync)."')";
    $rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);

}


// get Templates
$sqlquery = "SELECT * FROM LMB_SYNCSTRUCTURE_TEMPLATE ORDER BY NAME";
$rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);

$result_synctempl = array();
while(lmbdb_fetch_row($rs)) {
    $id_ = lmbdb_result($rs, 'ID');
    $result_synctempl['id'][$id_] = $id_;
    $result_synctempl['name'][$id_] = lmbdb_result($rs, 'NAME');
    $result_synctempl['modul'][$id_] = lmbdb_result($rs, 'MODUL');
}


?>


    <div class="container-fluid p-3">
        <table class="table table-sm table-striped mb-0 border bg-white">
            <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Delete</th>
            </tr>
            </thead>

            <tbody>
            <?php foreach($result_synctempl['id'] as $index => $id) : ?>
                <tr>
                    <!-- id -->
                    <td>
                        <?=$id?>
                    </td>
                    <!-- mimetype -->
                    <td>
                        <input type="text" name="synctempl_<?=$id?>" value="<?= $result_synctempl['name'][$index]?>" class="form-control form-control-sm" OnChange="edit_synctemplate('<?=$id?>',this.value)">
                    </td>
                    <!-- delete -->
                    <td>
                        <i class="lmb-icon lmb-trash" title="delete" OnClick="edit_synctemplate('<?=$id?>','delete')"></i>
                    </td>
                </tr>

                <?php endforeach; ?>
            </tbody>
            <tfoot>

            <tr>
                <td></td>
                <td><input type="text" name="new_synctemplate_name" class="form-control form-control-sm"></td>
                <td><button class="btn btn-sm btn-primary" onclick="document.form4.add_synctemplate.value='1';document.form4.sync_export_remote.value='';document.form4.submit();" type="button" value="1"><?= $lang[540] ?></button></td>
            </tr>

            </tfoot>
        </table>
    </div>
