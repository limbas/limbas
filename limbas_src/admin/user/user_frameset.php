<?php
/*
 * Copyright notice
 * (c) 1998-2021 Limbas GmbH(support@limbas.org)
 * All rights reserved
 * This script is part of the LIMBAS project. The LIMBAS project is free software; you can redistribute it and/or modify it on 2 Ways:
 * Under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * Or
 * In a Propritary Software Licence http://limbas.org
 * The GNU General Public License can be found at http://www.gnu.org/copyleft/gpl.html.
 * A copy is found in the textfile GPL.txt and important notices to the license from the author is found in LICENSE.txt distributed with these scripts.
 * This script is distributed WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * This copyright notice MUST APPEAR in all copies of the script!
 * Version 4.3.36.1319
 */

/*
 * ID: 135
 */


/* --- Frameset ---------------------------------------------------------- */?>
<div class="frame-container">
    <iframe name="user_tree" src="main_admin.php?action=<?=$frame1para?>" style="width: 300px; border-right: 1px solid <?= $farbschema['WEB3'] ?>;"></iframe>
    <iframe name="user_main" src="main_admin.php?action=<?=$frame2para?>&group_id=1" class="frame-fill"></iframe>
</div>