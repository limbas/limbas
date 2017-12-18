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
 * ID:
 */

if($BODYHEADER){
	if($LINK["icon_url"][$LINK_ID[$action]]){
                $BODYHEADER = "<i class=\"lmb-icon {$LINK["icon_url"][$LINK_ID[$action]]}\" style=\"vertical-align:middle;\"></i>&nbsp;&nbsp;".$BODYHEADER;
	}
	echo "<script language=\"JavaScript\">\n";
	echo "var val='";
	echo "<table cellpadding=\"0\" cellspacing=\"0\" width=\"100%\"><tr>";
	echo "<td>$BODYHEADER</td>";
	if($LINK["help_url"][$LINK_ID[$action]]){echo "<td align=\"right\"><a href=\"".$LINK["help_url"][$LINK_ID[$action]]."\" target=\"new\"><i class=\"lmb-icon lmb-help\"></i></a></td>";}
	echo "</tr></table>";
	echo "';\n";
	echo "if(top.main_top && top.main_top.document.getElementById('main_top_value')){top.main_top.document.getElementById('main_top_value').innerHTML = val;}\n";
	echo "</script>\n";
}elseif($BODYHEADER != "0"){
	echo "<script language=\"JavaScript\">\n";
	echo "if(top.main_top){\n
	if(top.main_top.document.getElementById('main_top_value')){\n
	top.main_top.document.getElementById('main_top_value').innerHTML = '';\n
	}\n
	}\n";
	echo "</script>\n";
}

?>
