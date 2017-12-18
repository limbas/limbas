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


# Pfadcodierung erkennen und falls UTF-8 decodieren
function path_decode($path){
	$path = html_entity_decode(rawurldecode($path));

	if (iconv('UTF-8', 'UTF-8', $path) != $path) {
		$path = $path;
	}else{
		$path = lmb_utf8_decode($path,1);
	}

	return $path;
}

$path = explode("/",$_SERVER["REQUEST_URI"]);
unset($path[0]);
$lastelement = $path[count($path)];

#if(in_array("closed",$path)){
	if($_SERVER['PHP_AUTH_USER'] == $lastelement){$auth_user = "gast";}
	if($_SERVER['PHP_AUTH_PW'] == $lastelement){$auth_pass = "gast";}
#}

$filestruct = null;

require("inc/include_db.lib");
require("lib/include.lib");
require("lib/session_auth.lib");
require("extra/explorer/filestructure.lib");


$path = implode("/",$path);
$path = path_decode($path);
$LID = parse_path($path);

$_SESSION["umgvar"]["dms_user"] = 1;
$_SESSION["umgvar"]["dms_lid"] = $LID;

$filestruct = null;

if ($db) {odbc_close($db);}
?>

<html>
<frameset rows="40,*" cols="*" frameborder=NO framespacing=0>
<frame src="/EXTENSIONS/dms/head.php?folder=<?=rawurlencode($folder)?>" scrolling="No" noresize>
<frameset cols="100,*" frameborder=0 framespacing=0>
<frame src="/EXTENSIONS/dms/links.htm" scrolling="No" noresize>
<frame src="/main.php?action=explorer_main&ffilter_viewmode=2&LID=<?=$LID?>&typ=1">
</frameset>
</frameset>
</html>









<div style="visibility:hidden;">
egiej lgijeogj ioerhrgiouewrhg öoweiu ghpöweouigh wepoig hzweopig hwerpgr
wegr opweiu hgpoweug h püweuog hwepough ewüoirghwe oirg
eqrg +oüiwehr gopiwehg poweuighwepougih weoughwe g
qerg +o0wiezrguio hewoug hpoweurgh üewroighüweiopgh wepoihg

egiej lgijeogj ioerhrgiouewrhg öoweiu ghpöweouigh wepoig hzweopig hwerpgr
wegr opweiu hgpoweug h püweuog hwepough ewüoirghwe oirg
eqrg +oüiwehr gopiwehg poweuighwepougih weoughwe g
qerg +o0wiezrguio hewoug hpoweurgh üewroighüweiopgh wepoihg
egiej lgijeogj ioerhrgiouewrhg öoweiu ghpöweouigh wepoig hzweopig hwerpgr
wegr opweiu hgpoweug h püweuog hwepough ewüoirghwe oirg
eqrg +oüiwehr gopiwehg poweuighwepougih weoughwe g
qerg +o0wiezrguio hewoug hpoweurgh üewroighüweiopgh wepoihg
egiej lgijeogj ioerhrgiouewrhg öoweiu ghpöweouigh wepoig hzweopig hwerpgr
wegr opweiu hgpoweug h püweuog hwepough ewüoirghwe oirg
eqrg +oüiwehr gopiwehg poweuighwepougih weoughwe g
qerg +o0wiezrguio hewoug hpoweurgh üewroighüweiopgh wepoihg
egiej lgijeogj ioerhrgiouewrhg öoweiu ghpöweouigh wepoig hzweopig hwerpgr
wegr opweiu hgpoweug h püweuog hwepough ewüoirghwe oirg
eqrg +oüiwehr gopiwehg poweuighwepougih weoughwe g
qerg +o0wiezrguio hewoug hpoweurgh üewroighüweiopgh wepoihg
egiej lgijeogj ioerhrgiouewrhg öoweiu ghpöweouigh wepoig hzweopig hwerpgr
wegr opweiu hgpoweug h püweuog hwepough ewüoirghwe oirg
eqrg +oüiwehr gopiwehg poweuighwepougih weoughwe g
qerg +o0wiezrguio hewoug hpoweurgh üewroighüweiopgh wepoihg
egiej lgijeogj ioerhrgiouewrhg öoweiu ghpöweouigh wepoig hzweopig hwerpgr
wegr opweiu hgpoweug h püweuog hwepough ewüoirghwe oirg
eqrg +oüiwehr gopiwehg poweuighwepougih weoughwe g
qerg +o0wiezrguio hewoug hpoweurgh üewroighüweiopgh wepoihg
egiej lgijeogj ioerhrgiouewrhg öoweiu ghpöweouigh wepoig hzweopig hwerpgr
wegr opweiu hgpoweug h püweuog hwepough ewüoirghwe oirg
eqrg +oüiwehr gopiwehg poweuighwepougih weoughwe g
qerg +o0wiezrguio hewoug hpoweurgh üewroighüweiopgh wepoihg
egiej lgijeogj ioerhrgiouewrhg öoweiu ghpöweouigh wepoig hzweopig hwerpgr
wegr opweiu hgpoweug h püweuog hwepough ewüoirghwe oirg
eqrg +oüiwehr gopiwehg poweuighwepougih weoughwe g
qerg +o0wiezrguio hewoug hpoweurgh üewroighüweiopgh wepoihg
egiej lgijeogj ioerhrgiouewrhg öoweiu ghpöweouigh wepoig hzweopig hwerpgr
wegr opweiu hgpoweug h püweuog hwepough ewüoirghwe oirg
eqrg +oüiwehr gopiwehg poweuighwepougih weoughwe g
qerg +o0wiezrguio hewoug hpoweurgh üewroighüweiopgh wepoihg
egiej lgijeogj ioerhrgiouewrhg öoweiu ghpöweouigh wepoig hzweopig hwerpgr
wegr opweiu hgpoweug h püweuog hwepough ewüoirghwe oirg
eqrg +oüiwehr gopiwehg poweuighwepougih weoughwe g
qerg +o0wiezrguio hewoug hpoweurgh üewroighüweiopgh wepoihg
egiej lgijeogj ioerhrgiouewrhg öoweiu ghpöweouigh wepoig hzweopig hwerpgr
wegr opweiu hgpoweug h püweuog hwepough ewüoirghwe oirg
eqrg +oüiwehr gopiwehg poweuighwepougih weoughwe g
qerg +o0wiezrguio hewoug hpoweurgh üewroighüweiopgh wepoihg
egiej lgijeogj ioerhrgiouewrhg öoweiu ghpöweouigh wepoig hzweopig hwerpgr
wegr opweiu hgpoweug h püweuog hwepough ewüoirghwe oirg
eqrg +oüiwehr gopiwehg poweuighwepougih weoughwe g
qerg +o0wiezrguio hewoug hpoweurgh üewroighüweiopgh wepoihg
egiej lgijeogj ioerhrgiouewrhg öoweiu ghpöweouigh wepoig hzweopig hwerpgr
wegr opweiu hgpoweug h püweuog hwepough ewüoirghwe oirg
eqrg +oüiwehr gopiwehg poweuighwepougih weoughwe g
qerg +o0wiezrguio hewoug hpoweurgh üewroighüweiopgh wepoihg

</div>