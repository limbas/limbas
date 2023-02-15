<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */
header('Content-type: text/javascript');

global $session;
$etag = md5($session['user_id'] . $session['lastreset']);

header('Cache-Control: max-age=86400, private');
header('ETag: ' . $etag);

if(isset($_SERVER['HTTP_IF_NONE_MATCH'])) {
    if($_SERVER['HTTP_IF_NONE_MATCH'] === $etag) {
        header('HTTP/1.1 304 Not Modified', true, 304);
        exit();
    }
}



$jsvar_ = array(24,49,50,51,56,84,93,101,134,138,164,721,744,745,813,815,822,844,1311,1318,1424,1441,1504,1509,1560,1608,1615,1683,1733,126,1709,1717,1760,1868,1913,2006,2007,2008,2009,2010,2082,2083,2114,2138,2139,2146,2147,2153,2154,2155,2156,2157,2158,2186,2187,2219,2226,2354,2359,2433,2454,2676,2776,2794,2897,2902,2705,2979,3085,3103,3102,3101);
foreach($jsvar_ as $key => $value){
	$jsvar .= "jsvar['lng_".$value."'] = \"".$lang[$value]."\";\n";
}

echo 'var RULE = [];' .
'var DATA_TYPE_EXP = [];' .
    'var FORMAT = [];' .
    'var jsvar = [];' .
    'var input_check = null;';




$sqlquery = "SELECT * FROM LMB_FIELD_TYPES WHERE FIELD_TYPE > 0 ORDER BY SORT";
$rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
while(lmbdb_fetch_row($rs)) {

    echo "RULE[".lmbdb_result($rs, "DATA_TYPE")."] = \"".str_replace("\\","\\\\",lmbdb_result($rs, "LMRULE"))."\";
DATA_TYPE_EXP[".lmbdb_result($rs, "DATA_TYPE")."] = \"".str_replace("\"","'",$lang[lmbdb_result($rs, "DATA_TYPE_EXP")])."\";
FORMAT[".lmbdb_result($rs, "DATA_TYPE")."] = \"".str_replace("\"","'",$lang[lmbdb_result($rs, "FORMAT")])."\";\n";
    
    
}
$sqlquery = "SELECT * FROM LMB_FIELD_TYPES_DEPEND WHERE FIELD_TYPE > 0 ORDER BY SORT";
$rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
while(lmbdb_fetch_row($rs)) {

    echo "RULE[".lmbdb_result($rs, "DATA_TYPE")."] = \"".str_replace("\\","\\\\",lmbdb_result($rs, "LMRULE"))."\";
DATA_TYPE_EXP[".lmbdb_result($rs, "DATA_TYPE")."] = \"".str_replace("\"","'",$lang[lmbdb_result($rs, "DATA_TYPE_EXP")])."\";
FORMAT[".lmbdb_result($rs, "DATA_TYPE")."] = \"".str_replace("\"","'",$lang[lmbdb_result($rs, "FORMAT")])."\";\n";
    
}


echo $jsvar;

# color
echo "
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
jsvar['WEB13'] = \"".$farbschema["WEB13"]."\";

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
jsvar['RGB13'] = \"".lmb_strtolower($farbschema["RGB13"])."\";
";



# umgvar

echo "
jsvar['umgvar_uploadprogress'] = \"".$umgvar["upload_progress"]."\";
jsvar['thumbsize2'] = '".$umgvar["thumbsize2"]."';
jsvar['thumbsize3'] = '".$umgvar["thumbsize3"]."';
jsvar['language'] = '".$session['language']."';
";


# validate
foreach($gtab["validate"] as $id => $value){
    if($value) {
        echo "jsvar['gtab_validate_$id'] = true;";
    }
}
