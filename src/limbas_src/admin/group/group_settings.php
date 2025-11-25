<?php
/*
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */

use Limbas\admin\setup\tinymce\TinyMceConfig;

global $change_rules;
global $ID;
global $addsubgroup;

if($change_rules){
    group_settings_update($ID, $addsubgroup);
}
$kvs = get_group_settings($ID);

$tinyMceConfigs = TinyMceConfig::all();


/*
 * Settings format:
    <tr>
     <td>Name</td>
     <td><input name="rules_settings[$key]" value="<?=$kvs['$key']?>"></td>
     <td>Description</td>
    </tr>
 *
 * Where $key should be replaced with the string representation of the key, which should get used in the db
 * Make sure the key isn't already in use
 */

?>

<div class="container-fluid p-3">
    <FORM ACTION="main_admin.php" METHOD="post" name="form1">
        <input type="hidden" name="ID" value="<?=$ID?>">
        <input type="hidden" name="action" value="setup_group_settings">

        <div class="row">
            <?php
            $activeTabLinkId = 331;

            require(__DIR__.'/group_tabs.php') ?>

            <div class="tab-content col-9 border border-start-0 bg-contrast">
                <div class="tab-pane active p-3">

                    <h5><i class="lmb-icon lmb-group"></i>&nbsp;<?=$groupdat["name"][$ID]?></h5>

                    <hr>

                    <table class="table table-sm table-striped table-bordered table-hover bg-contrast w-100">
                        <thead>
                            <tr>
                                <th class="border-top-0"><?= $lang[926];?></th>
                                <th class="border-top-0"><?= $lang[29];?></th>
                                <th class="border-top-0"><?= $lang[126];?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Default TinyMce-Config</td>
                                <td>
                                    <select class="form-select form-select-sm" name="rules_settings[tinymceConfig]">
                                        <option value=""></option>
                                        <?php
                                        /** @var TinyMceConfig $tinyMceConfig */
                                        foreach($tinyMceConfigs as $tinyMceConfig):?>
                                            <option value="<?=e($tinyMceConfig->id)?>" <?= intval($kvs['tinymceConfig']) === $tinyMceConfig->id ? 'selected' : '' ?>><?=e($tinyMceConfig->name)?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </td>
                                <td>The default TinyMce config to be used for this group.</td>
                            </tr>
                        </tbody>
                    </table>
                    <?php require __DIR__ . '/submit-footer.php'; ?>
                </div>
            </div>
        </div>
    </FORM>
</div>

