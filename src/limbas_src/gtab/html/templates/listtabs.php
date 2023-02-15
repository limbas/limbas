<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */

// lmbGlistTabs

global $gtab;
global $verknpool;
global $gsnap;
global $greminder;

echo "<table class=\"GtabTableFringeHeader\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" style=\"border-collapse:collapse;width:100%\"><tr>";


# --- Verknüpfungs Pool setzen -------------------------
$verknpool[$gtabid][] = array($gtabid,'null','null','null','null','null','null','null',$GLOBALS["form_id"],'null');
if($snap_id){$desc = $gsnap[$gtabid]["name"][$snap_id];
}else{$desc = $gtab["desc"][$gtabid];}
if($gtab["markcolor"][$gtabid]){$mst = "style=\"background-color:".$gtab["markcolor"][$gtabid]."\"";}else{$mst = null;}
echo "<td nowrap class=\"lmbGtabTabmenuActive\" $mst id=\"tabs_".$layoutVer["tabid"] = $gtabid."\" onclick=\"send_form('1');\">".$desc;
if($greminder[$gtabid]['name'][$GLOBALS['gfrist']]){echo "&nbsp;(<i>".$greminder[$gtabid]["name"][$GLOBALS['gfrist']]."</i>)";}
echo "</td>";
#}

echo "<td class=\"lmbGtabTabmenuSpace\" width=\"100%\"></td></tr></table>";

