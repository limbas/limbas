<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */
?>
<FORM ACTION="main_admin.php" METHOD="post" NAME="form4">
    <input type="hidden" name="action" value="setup_verkn_editor">
    <input type="hidden" name="tabid" value="<?=$tabid?>">
    <input type="hidden" name="fieldid" value="<?=$fieldid;?>">
    <input type="hidden" name="relation_parameter" value="1">
    <input type="hidden" name="category" value="relparams">

    <?php
    edit_relationparams($tabid,$fieldid);
    ?>
    

    <div class="text-center">
        <button type="submit" class="btn btn-primary"><?=$lang[33]?></button>
    </div>
</FORM>
