<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */



#---------------------------- Formular Kopieren -------------------------


if($form_id){
	?>
    <div class="frame-container">
        <iframe name="form_main" src="main_admin.php?&action=setup_form_main&form_id=<?=$form_id?>&form_name=<?=$form_name?>&form_typ=<?=$form_typ?>&referenz_tab=<?=$referenz_tab?>&new=<?=$new?>" class="frame-fill"></iframe>
        <iframe name="form_menu" src="main_admin.php?&action=setup_form_menu&form_id=<?=$form_id?>&form_name=<?=$form_name?>&form_typ=<?=$form_typ?>&referenz_tab=<?=$referenz_tab?>" style="width: 260px;"></iframe>
    </div>
	<?php
}else{
	echo "<Script language=\"JavaScript\">\ndocument.location.href='main_admin.php?&action=setup_form_select';\n</SCRIPT>";
}
