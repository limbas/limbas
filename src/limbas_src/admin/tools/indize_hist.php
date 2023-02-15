<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */


?>

<div class="container-fluid p-3">

    <table class="table table-sm table-striped mb-0 border bg-white">
        <tr>
            <td>Nr</td>
            <td>Job-Nr</td>
            <td>Aktion</td>
            <td>Datum</td>
            <td>Status</td>
            <td>Zeit (sek.)</td>
            <td>Indizes</td>
            <td></td>
        </tr>

        <?php
        $sqlquery = "SELECT * FROM LMB_INDIZE_HISTORY ORDER BY ERSTDATUM DESC";
        $rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
        if(!$rs) {$commit = 1;}
        if(!$maxresult){$maxresult = 30;}
        $status = "OK";
        $bzm = 1;
        while(lmbdb_fetch_row($rs) AND $bzm <= $maxresult) :

            if(!lmbdb_result($rs,"RESULT")){$bgColor = 'danger';$status = "FALSE";}
            elseif(!lmbdb_result($rs,"INUM")){$bgColor = "";}
            elseif(lmbdb_result($rs,"INUM") > 0 AND lmbdb_result($rs,"INUM") <= 1000){$bgColor = 'success';}
            elseif(lmbdb_result($rs,"INUM") > 1000 AND lmbdb_result($rs,"INUM") <= 10000){$bgColor = 'success';}
            elseif(lmbdb_result($rs,"INUM") > 10000 AND lmbdb_result($rs,"INUM") <= 100000){$bgColor = 'success';}
            elseif(lmbdb_result($rs,"INUM") > 100000 AND lmbdb_result($rs,"INUM") <= 500000){$bgColor = 'warning';}
            elseif(lmbdb_result($rs,"INUM") > 500000 AND lmbdb_result($rs,"INUM") <= 1000000){$bgColor = 'warning';}
            elseif(lmbdb_result($rs,"INUM") > 1000000){$bgColor = 'danger';}
            
            ?>
            
            <tr class="bg-<?=$bgColor?>">
                <td><?=lmbdb_result($rs,"ID")?></td>
                <td><?=lmbdb_result($rs,"JOB")?></td>
                <td><?=lmbdb_result($rs,"ACTION")?></td>
                <td><?=get_date(lmbdb_result($rs,"ERSTDATUM"),2)?></td>
                <td><?=lmbdb_result($rs,"MESSAGE")?></td>
                <td><?=number_format((lmbdb_result($rs,"USED_TIME")/60),1,".",".")?></td>
                <td><?=lmbdb_result($rs,"JNUM")?></td>
                <td><?=lmbdb_result($rs,"INUM")?></td>
            </tr>
            
        
        <?php
        $bzm++;
        
        endwhile; ?>
    </table>
</div>
