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
 * ID: 37
 */


$jsvar_ = array(24,49,50,51,56,84,93,101,134,164,721,744,745,813,815,822,1311,1318,1424,1441,1504,1509,1560,1608,1615,1683,1733,126,1709,1717,1760,1868,1913,2006,2007,2008,2009,2010,2082,2083,2114,2138,2139,2146,2147,2153,2154,2155,2156,2157,2158,2186,2187,2219,2359,2433,2454,2676,2794,2354,2902,2705,2979);
foreach($jsvar_ as $key => $value){
	$jsvar .= "jsvar['lng_".$value."'] = \"".$lang[$value]."\";\n";
}

$jsfile = fopen($umgvar["pfad"]."/USER/".$session["user_id"]."/syntaxcheck.js","w+");

fputs($jsfile,

"var RULE = new Array();
var DATA_TYPE_EXP = new Array();
var FORMAT = new Array();
var jsvar = new Array();
var input_check = null;\n\n
");



$sqlquery = "SELECT * FROM LMB_FIELD_TYPES WHERE FIELD_TYPE > 0 ORDER BY SORT";
$rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
while(lmbdb_fetch_row($rs)) {
	fputs($jsfile,"RULE[".lmbdb_result($rs, "DATA_TYPE")."] = \"".str_replace("\\","\\\\",lmbdb_result($rs, "LMRULE"))."\";
DATA_TYPE_EXP[".lmbdb_result($rs, "DATA_TYPE")."] = \"".$lang[lmbdb_result($rs, "DATA_TYPE_EXP")]."\";
FORMAT[".lmbdb_result($rs, "DATA_TYPE")."] = \"".$lang[lmbdb_result($rs, "FORMAT")]."\";\n");
}

# lang
fputs($jsfile,"

\n\n".$jsvar."\n\n");

# color
fputs($jsfile,"
jsvar['WEB1'] = \"".$farbschema["WEB1"]."\";
jsvar['WEB2'] = \"".$farbschema["WEB2"]."\";
jsvar['WEB3'] = \"".$farbschema["WEB3"]."\";
jsvar['WEB4'] = \"".$farbschema["WEB4"]."\";
jsvar['WEB5'] = \"".$farbschema["WEB5"]."\";
jsvar['WEB6'] = \"".$farbschema["WEB6"]."\";
jsvar['WEB7'] = \"".$farbschema["WEB7"]."\";
jsvar['WEB8'] = \"".$farbschema["WEB8"]."\";
jsvar['WEB9'] = \"".$farbschema["WEB9"]."\";
jsvar['WEB10'] = \"".$farbschema["WEB10"]."\";
jsvar['WEB11'] = \"".$farbschema["WEB11"]."\";
jsvar['WEB12'] = \"".$farbschema["WEB12"]."\";
jsvar['WEB13'] = \"".$farbschema["WEB13"]."\";\n\n

jsvar['RGB1'] = \"".lmb_strtolower($farbschema["RGB1"])."\";
jsvar['RGB2'] = \"".lmb_strtolower($farbschema["RGB2"])."\";
jsvar['RGB3'] = \"".lmb_strtolower($farbschema["RGB3"])."\";
jsvar['RGB4'] = \"".lmb_strtolower($farbschema["RGB4"])."\";
jsvar['RGB5'] = \"".lmb_strtolower($farbschema["RGB5"])."\";
jsvar['RGB6'] = \"".lmb_strtolower($farbschema["RGB6"])."\";
jsvar['RGB7'] = \"".lmb_strtolower($farbschema["RGB7"])."\";
jsvar['RGB8'] = \"".lmb_strtolower($farbschema["RGB8"])."\";
jsvar['RGB9'] = \"".lmb_strtolower($farbschema["RGB9"])."\";
jsvar['RGB10'] = \"".lmb_strtolower($farbschema["RGB10"])."\";
jsvar['RGB11'] = \"".lmb_strtolower($farbschema["RGB11"])."\";
jsvar['RGB12'] = \"".lmb_strtolower($farbschema["RGB12"])."\";
jsvar['RGB13'] = \"".lmb_strtolower($farbschema["RGB13"])."\";\n\n
");



# umgvar
fputs($jsfile,"
jsvar['umgvar_uploadprogress'] = \"".$umgvar["upload_progress"]."\";
jsvar['thumbsize2'] = \"".$umgvar["thumbsize2"]."\";
jsvar['thumbsize3'] = \"".$umgvar["thumbsize3"]."\";
jsvar['language'] = '".$session['language']."';
\n\n
");


?>