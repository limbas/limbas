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
 * ID: 8
 */


if($umgvar["introlink"]){$intro = $umgvar["introlink"];}else{$intro = "main.php?action=intro";}
if ($action == "redirect" AND $src) {$intro = "main.php?".$src;}

require_once("layout/".$session["layout"]."/css.php");

$menu_setting = lmbGetMenuSetting();

if($menu_setting["frame"]["nav"]){
	$LeftMenuSize = $menu_setting["frame"]["nav"];
	if($LeftMenuSize > 30){$LeftMenuSize = $LeftMenuSize+10;}
}
if($menu_setting["frame"]["multiframe"]){
	$rightMenuSize = $menu_setting["frame"]["multiframe"];
}
if($menu_setting["frame"]["top"]){
	$topFrameSize = $menu_setting["frame"]["top"];
}

if(!$LINK[252]){$topFrameSize = "0";}
if(!$LINK[251]){$LeftMenuSize = "0";}
if(!$LINK[253] OR !$session["multiframe"]){$rightMenuSize = "0";}

$topset = "$topFrameSize,$topMenuSize,*";
$navset = "$LeftMenuSize,*,$rightMenuSize";

?>

<FRAMESET ID="topset" ROWS="<?=$topset?>" Border="0" FRAMEBORDER="0" FRAMESPACING="0">
	<FRAME ID="top1" SRC="main.php?&action=top" Scrolling="NO" NAME="top1" Marginheight="0" Marginwidth="0" NORESIZE>
	<?php if($LINK[252]){?>
		<FRAME ID="message" SRC="main.php?&action=top2" Scrolling="NO" NAME="message" Marginheight="0" Marginwidth="0" NORESIZE>
	<?php }else{?>
		<FRAME NAME="message" SRC="empty.html" Scrolling="No" Marginheight="0" Marginwidth="0">
	<?php }?>
	<FRAMESET ID="mainset" COLS="<?=$navset?>" Border="0" FRAMEBORDER="0" FRAMESPACING="0">
		 <?php if($LINK[251]){?>
		 <FRAME ID="nav" SRC="main.php?&action=nav&sparte=gtab&tab_group=1&refresh=no" Scrolling="AUTO" NAME="nav" Marginheight="5" Marginwidth="5">
		 <?php }else{?>
			<FRAME NAME="nav" SRC="empty.html" Scrolling="No" Marginheight="0" Marginwidth="0">
		<?php }?>
			<FRAMESET rows="38,*" FRAMEBORDER="0" FRAMESPACING="0" BORDER="0">
				<FRAME SRC="main.php?action=layoutframe&layoutframe=top" Scrolling="NO" ID="main_top" NAME="main_top" Marginheight="0" Marginwidth="0">
				<FRAME ID="main" SRC="<?=$intro?>" Scrolling="AUTO" NAME="main" Marginheight="0" Marginwidth="0">
			</FRAMESET>
		<?php if($LINK[253] AND $session["multiframe"]){?>
			<FRAME ID="multiframe" SRC="main.php?&action=multiframe" Scrolling="AUTO" NAME="multiframe" Marginheight="0" Marginwidth="0">
		<?php }else{?>
			<FRAME NAME="multiframe" SRC="empty.html" Scrolling="No" Marginheight="0" Marginwidth="0">
		<?php }?>
	
    </FRAMESET>
</FRAMESET>