<?php
/*
 * Copyright notice
 * (c) 1998-2019 Limbas GmbH(support@limbas.org)
 * All rights reserved
 * This script is part of the LIMBAS project. The LIMBAS project is free software; you can redistribute it and/or modify it on 2 Ways:
 * Under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * Or
 * In a Propritary Software Licence http://limbas.org
 * The GNU General Public License can be found at http://www.gnu.org/copyleft/gpl.html.
 * A copy is found in the textfile GPL.txt and important notices to the license from the author is found in LICENSE.txt distributed with these scripts.
 * This script is distributed WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * This copyright notice MUST APPEAR in all copies of the script!
 * Version 3.6
 */

/*
 * ID: 173
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