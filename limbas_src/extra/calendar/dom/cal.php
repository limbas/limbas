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
 * ID: 189
 */
?>

<FRAMESET COLS="*,230" Border="0" FRAMEBORDER="0" FRAMESPACING="0">
	<FRAME SRC="main.php?<?=SID?>&action=userstat_main&userstat=<?=$userstat?>" Scrolling="YES" NAME="cal_main" Marginheight="0" Marginwidth="0">
	<FRAMESET ROWS="230,*" Border="0" FRAMEBORDER="0" FRAMESPACING="0">
		<FRAME SRC="main.php?<?=SID?>&action=userstat_set" Scrolling="NO" noresize NAME="cal_js" Marginheight="0" Marginwidth="0">
		<FRAME SRC="main.php?<?=SID?>&action=userstat_list&userstat=<?=$userstat?>" Scrolling="AUTO" NAME="cal_list" Marginheight="0" Marginwidth="0">	
	</FRAMESET>
</FRAMESET>