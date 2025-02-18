<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */


if($GLOBALS["gLmbExt"]["ext_gtab.inc"]){
    foreach ($GLOBALS["gLmbExt"]["ext_gtab.inc"] as $key => $extfile){
        require_once($extfile);
    }
}
if($GLOBALS["gLmbExt"]["ext_gtab_erg_dao.inc"]){
    foreach ($GLOBALS["gLmbExt"]["ext_gtab_erg_dao.inc"] as $key => $extfile){
        require_once($extfile);
    }
}



$gview = lmb_getQuestValue($viewid);
?>

<h3><?=$gview["viewname"]?></h3>

<?php
if($gview["viewdef"]){
    if($rs = lmbdb_exec($db, lmb_paramTransView($viewid, $gview["viewdef"])) or lmb_questerror(lmbdb_errormsg($db),$gview["viewdef"])){
        echo ODBCResourceToHTML($rs, 'class="table table-sm table-bordered"', '', 1000);
    }
}
?>
