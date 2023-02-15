<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */




if($GLOBALS["action"]): ?>

	<div class="progress mb-3">
		<div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" id="pb-overall">
			0%
		</div>
	</div>

	<p><?=$lang[561]?>: <span id="status_group"> - </span></p>
	<p><?=$lang[164]?>: <span id="status_table"> - </span></p>

<?php

endif;

if($check_all){
    # ------------------------------ Gruppenrechte ergänzen ------------------------------------
    check_grouprights1All(1,$check_table);

}elseif($group_id){
    # ------------------------------ Gruppenrechte zurücksetzten ------------------------------------	
    function group_list($group_id,$group_name){
        global $db;
        global $commit;

        $sqlquery = "SELECT GROUP_ID,NAME FROM LMB_GROUPS WHERE LEVEL = $group_id";
        $rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
        if(!$rs) {$commit = 1;}
        while(lmbdb_fetch_row($rs)) {
            group_list(lmbdb_result($rs, "GROUP_ID"),lmbdb_result($rs,"NAME"));
        }
        check_grouprights2($group_id,$group_name,1,1);
    }
    group_list($group_id,$groupdat['name'][$group_id]);
}

if($GLOBALS["action"]): ?>
	<script>
        showprogress('pb-overall',100);
        showprogressLabel('status_group','<i class="lmb-icon lmb-aktiv"></i>');
        showprogressLabel('status_table','<i class="lmb-icon lmb-aktiv"></i>');
	</script>

	<p class="text-success"><?=$lang[987]?></p>
<?php endif; ?>
