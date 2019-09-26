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
 * ID: 11
 */

$intro = 1;
require_once("layout/".$session["layout"]."/css.php");
$menu_setting = lmbGetMenuSetting();

?>
<SCRIPT LANGUAGE="JavaScript">
document.body.bgColor = "#ffffff";
document.body.background =  "";

var frame_status = <?= parse_db_int($menu_setting["frame"]["top"]) ?>;
var frame_value = null;

</SCRIPT>


<?php
if($menu_setting["frame"]["top"]){
	$displ1 = "display:none";$displ2 = "";
}else{
	$displ1 = "";$displ2 = "display:none";
}

echo "<div class=\"lmbfringeFrameTop\" id=\"small_frame\">";
if(file_exists($umgvar["pfad"]."/EXTENSIONS/customization/logo_small.png")){?>
<a target="_blank" href="index.php"><div id="small_image" class="lmbItemInfoTop"><img src="EXTENSIONS/customization/logo_small.png"></div></a>
<div class="lmbItemUsernameTop"><?=$lang[2455]?>: <b><?= $session["vorname"]." ".$session["name"] ?></b>
<?php }else{?>
<a target="_blank" href="index.php"><div id="small_image" class="lmbItemInfoTop"><b>L<span style="color:orange">I</span>MBAS</b> <?=$umgvar["company"]?></div></a>
<div class="lmbItemUsernameTop"><?=$lang[2455]?>: <b><?= $session["vorname"]." ".$session["name"] ?></b>
<?php }?>
<?php if($umgvar["admin_mode"] AND $LINK[17] AND $session["group_id"] == 1){
	echo "<a href=\"main_admin.php?action=setup_umgvar#admin-mode\" target=\"main\" style=\"color:red; margin-left: 10px; border: 1px solid red; padding: 2px 4px;\">admin mode on!</a>";
}?>
</div>

<!--<div class="lmbItemUsernameTop"><?=$lang[2455]?>: <B><?= $session["vorname"]." ".$session["name"] ?>-->

</div>
</div>



