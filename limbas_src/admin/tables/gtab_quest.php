<?php
/*
 * Copyright notice
 * (c) 1998-2018 Limbas GmbH(support@limbas.org)
 * All rights reserved
 * This script is part of the LIMBAS project. The LIMBAS project is free software; you can redistribute it and/or modify it on 2 Ways:
 * Under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * Or
 * In a Propritary Software Licence http://limbas.org
 * The GNU General Public License can be found at http://www.gnu.org/copyleft/gpl.html.
 * A copy is found in the textfile GPL.txt and important notices to the license from the author is found in LICENSE.txt distributed with these scripts.
 * This script is distributed WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * This copyright notice MUST APPEAR in all copies of the script!
 * Version 3.5
 */

/*
 * ID: 205
 */
?>
<FORM ACTION="main_admin.php" METHOD="post" NAME="form1">
<input type="hidden" name="action" value="setup_gtab_quest">
<input type="hidden" name="tab_group" value="<?=$tab_group;?>">
<input type="hidden" name="tabid" value="<?=$tabid;?>">
	
<br><br>
<table border="0"><tr><td style="width:20px;" rowspan="3">&nbsp;</td><td>


<select name="quest_showtype" style="width:100px;">
<option value="1" <?php if($quest_showtype == 1){echo "selected";}?>><?=$lang[2026]?>
<option value="2" <?php if($quest_showtype == 2){echo "selected";}?>><?=$lang[2027]?>
</select>
</td></tr><tr><td>
<textarea name="quest_sqlvalue" style="width:600px;height:300px;border:1px solid black;"><?=$quest_sqlvalue?></textarea>
</td></tr><tr><td>
<input type="submit" name="update_quest" value="<?=$lang[842]?>">
</td></tr></table>