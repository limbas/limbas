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
 * ID: 200
 */
?>




<DIV ID="viewmenu" class="lmbContextMenu" style="display:none;z-index:2000;" OnClick="activ_menu = 1;">
<? #-----------------  -------------------
pop_left();
echo "<a href=\"#\" OnClick=\"window.califrame.document.form1.viewtype.value='1';window.califrame.document.form1.submit();\">&nbsp;".$lang[1943]."</a>";
pop_right();
pop_left();
echo "<a href=\"#\" OnClick=\"window.califrame.document.form1.viewtype.value='2';window.califrame.document.form1.submit();\">&nbsp;".$lang[1944]."</a>";
pop_right();
pop_left();
echo "<a href=\"#\" OnClick=\"window.califrame.document.form1.viewtype.value='3';window.califrame.document.form1.submit();\">&nbsp;".$lang[1945]."</a>";
pop_right();
pop_bottom();
?>
</DIV>

<DIV ID="tzonemenu" class="lmbContextMenu" style="display:none;z-index:2001;" OnClick="activ_menu = 1;">
<? #-----------------  -------------------
pop_left();
echo "<a href=\"#\" OnClick=\"window.califrame.document.form1.subh.value='5';window.califrame.document.form1.submit();\">&nbsp;5 ".$lang[1942]."</a>";
pop_right();
pop_left();
echo "<a href=\"#\" OnClick=\"window.califrame.document.form1.subh.value='15';window.califrame.document.form1.submit();\">&nbsp;15 ".$lang[1942]."</a>";
pop_right();
pop_left();
echo "<a href=\"#\" OnClick=\"window.califrame.document.form1.subh.value='30';window.califrame.document.form1.submit();\">&nbsp;30 ".$lang[1942]."</a>";
pop_right();
pop_left();
echo "<a href=\"#\" OnClick=\"window.califrame.document.form1.subh.value='60';window.califrame.document.form1.submit();\">&nbsp;60 ".$lang[1942]."</a>";
pop_right();
pop_bottom();
?>
</DIV>


<form action="main.php" method="post" name="form1">
<input type="hidden" name="action" value="kalender_main">
<input type="hidden" name="show_date" value="<?=$show_date?>">
<input type="hidden" name="subh" value="<?=$subh?>">
<input type="hidden" name="viewtype" value="<?=$viewtype?>">
<input type="hidden" name="gtabid" value="<?=$gtabid?>">
<input type="hidden" name="verkn_tabid" value="<?=$verkn_tabid?>">
<input type="hidden" name="verkn_fieldid" value="<?=$verkn_fieldid?>">
<input type="hidden" name="verkn_ID" value="<?=$verkn_ID?>">
<input type="hidden" name="ctyp" value="pro">
<input type="hidden" name="drag_el">
<input type="hidden" name="resize_el">
<input type="hidden" name="change_el">
<input type="hidden" name="delete_el">
<input type="hidden" name="posy">


<div class="lmbfringegtab" style="margin-right:20px;">

<table id="CalTable" width="100%"  cellspacing="0" cellpadding="0" border="0" style="border-collapse:collapse;margin-right:20px;">

<tr><td>
<table border="0" cellspacing="0" cellpadding="0"><tr>
<td nowrap id="menu1" class="lmbGtabTabmenuActive"><?=$gtab["desc"][$gtabid]?></TD>
<td class="lmbGtabTabmenuSpace">&nbsp;</td>
</tr></table>
</td></tr>

<tr><td>
<div class="gtabHeaderMenuTR">
<table border="0" cellspacing="0" cellpadding="0" style="border-collapse:collapse;width:100%;"><tr>
<td class="gtabHeaderMenuTD" nowrap onmouseover="this.className='gtabHeaderMenuTDhover'" onmouseout="this.className='gtabHeaderMenuTD'" onclick="limbasDivShow(this,'','viewmenu');"><?=$lang[1625]?></TD>
<?if($LINK[224] AND $viewtype != 3){?><td class="gtabHeaderMenuTD" nowrap onmouseover="this.className='gtabHeaderMenuTDhover'" onmouseout="this.className='gtabHeaderMenuTD'" onclick="limbasDivShow(this,'','tzonemenu');">&nbsp;|&nbsp;<?=$lang[$LINK[name][224]]?>&nbsp;</td><?}?>
<td width="100%">&nbsp;</td></td>
</tr></table>
</div>
</td></tr>

<tr><td>
<div class="gtabHeaderSymbolTR">
<table><tr>
<?if($viewtype!=1){$style1="opacity:0.3;filter:Alpha(opacity=30)";}?>
<?if($viewtype!=2){$style2="opacity:0.3;filter:Alpha(opacity=30)";}?>
<?if($viewtype!=3){$style3="opacity:0.3;filter:Alpha(opacity=30)";}?>
<TD><i ID="tzoneday" class="lmb-icon-cus lmb-cal-day" OnClick="window.califrame.document.form1.viewtype.value='1';window.califrame.document.form1.submit();" TITLE="<?=$lang[1943]?>" style="cursor:pointer;<?=$style1?>"></i></TD>
<TD><i ID="tzoneweek" class="lmb-icon-cus lmb-cal-week" OnClick="window.califrame.document.form1.viewtype.value='2';window.califrame.document.form1.submit();" TITLE="<?=$lang[1944]?>" style="cursor:pointer;<?=$style2?>"></i></TD>
<TD><i ID="tzonemonth" class="lmb-icon lmb-calendar-alt2" OnClick="window.califrame.document.form1.viewtype.value='3';window.califrame.document.form1.submit();" TITLE="<?=$lang[1945]?>" style="cursor:pointer;<?=$style3?>"></i></TD>
</tr></table>
</div>
</td></tr>


<tr><td>

<div id="body_element" style="overflow:auto; height:1000px;">
<iframe name="califrame" style="height:100%;width:100%;border:none" src="main.php?action=kalender_iframe&ctyp=pro&viewtype=<?=$viewtype?>&show_date=<?=$show_date?>&subh=<?=$subh?>&gtabid=<?=$gtabid?>&verkn_tabid=<?=$verkn_tabid?>&verkn_fieldid=<?=$verkn_fieldid?>&verkn_ID=<?=$verkn_ID?>"></iframe>
</div>

<?if($viewtype!=3){
echo "<tr><td><table class=\"gtabFooterTAB\"><tr><td></td></tr></table></td></tr>";
}?>

</table>




</div>
</form>