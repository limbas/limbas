<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */
?>
<FORM ACTION="main_admin.php" METHOD="post" NAME="form3">
    <input type="hidden" name="action" value="setup_verkn_editor">
    <input type="hidden" name="tabid" value="<?=$tabid?>">
    <input type="hidden" name="fieldid" value="<?=$fieldid;?>">
    <input type="hidden" name="relation_extension" value="1">
    <input type="hidden" name="category" value="relation">

    <div class="accordion mb-3" id="filter-relation-acc">
        <div class="card">
            <div class="card-header cursor-pointer" data-bs-toggle="collapse" data-bs-target="#filter-relation-acc-content">
                <?=$lang[2377]?>
            </div>

            <div id="filter-relation-acc-content" class="collapse" data-parent="#filter-relation-acc">
                <div class="card-body">
                    <table class="table table-sm table-hover" ID="relation_verknview">
                        <?php
                        rec_verknpf_tabs($tabid,$verkntab);
                        ?>
                    </table>

                    <TEXTAREA class="form-control" ID="relation_preview" rows="3" readonly OnDblClick="document.getElementById('relation_value').value=this.value"></TEXTAREA>
                </div>
            </div>
        </div>
    </div>
    
    <TEXTAREA ID="relation_value" NAME="relation_value" class="form-control mb-2" rows="8"><?=htmlentities($rfield['relext'],ENT_QUOTES,$umgvar["charset"]);?></TEXTAREA>

    <div class="alert alert-info" role="alert">
        <b>examples:</b><br>
        $extension['where'][] = "VERK_52131C929EFDD.ID = ".$verkn["id"]." AND ....<br>
        $extension['from'][] = "VERK_52131C929EFDD";<br>
        $extension['select'][] = 'CONTACTS.NAME, CONTACTS.GIVENNAME';<br>
        $extension['order'][] = 'CONTACTS.NAME ASC, CONTACTS.GIVENNAME ASC';<br>
        $extension['ojoin'][] = '';<br>
        ...<br>
        $gsr = array();<br>
        ...<br>
        $filter['order'][$gtabid][0] = array($gtabid,2,'ASC');<br>
        ...<br>
        return myExt_function();
    </div>
    
    <div class="text-center">
        <button type="submit" class="btn btn-primary"><?=$lang[33]?></button>
    </div>
</FORM>
