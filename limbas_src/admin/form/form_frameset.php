<?php
/*
 * Copyright notice
 * (c) 1998-2016 Limbas GmbH - Axel westhagen (support@limbas.org)
 * All rights reserved
 * This script is part of the LIMBAS project. The LIMBAS project is free software; you can redistribute it and/or modify it on 2 Ways:
 * Under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * Or
 * In a Propritary Software Licence http://limbas.org
 * The GNU General Public License can be found at http://www.gnu.org/copyleft/gpl.html.
 * A copy is found in the textfile GPL.txt and important notices to the license from the author is found in LICENSE.txt distributed with these scripts.
 * This script is distributed WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * This copyright notice MUST APPEAR in all copies of the script!
 * Version 3.0
 */

/*
 * ID: 173
 */

#---------------------------- Formular Kopieren -------------------------


if($form_id){
	?>
	<FRAMESET COLS="*,240" Border="0" FRAMEBORDER="0" FRAMESPACING="0">
	        <FRAME SRC="main_admin.php?&action=setup_form_main&form_id=<?=$form_id?>&form_name=<?=$form_name?>&form_typ=<?=$form_typ?>&referenz_tab=<?=$referenz_tab?>&new=<?=$new?>" Scrolling="AUTO" NAME="form_main" Marginheight="0" Marginwidth="0">
	        <FRAME SRC="main_admin.php?&action=setup_form_menu&form_id=<?=$form_id?>&form_name=<?=$form_name?>&form_typ=<?=$form_typ?>&referenz_tab=<?=$referenz_tab?>" Scrolling="AUTO" NAME="form_menu" Marginheight="0" Marginwidth="0">
	</FRAMESET>
	<?
}else{
	echo "<Script language=\"JavaScript\">\ndocument.location.href='main_admin.php?&action=setup_form_select';\n</SCRIPT>";
}