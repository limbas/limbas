<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */


?>
<Script language="JavaScript">
function f_3(PARAMETER) {
	document.form1.action.value = PARAMETER;
	document.form1.submit();
}
</SCRIPT>


<div class="container-fluid p-3">

    <table class="table table-sm table-striped mb-0 border bg-contrast">
        <thead>
        <tr>
            <th>Nr</th>
            <th WIDTH="80">Action</th>
            <th WIDTH="130">Date</th>
            <th>Status</th>
            <th>Media</th>
            <th WIDTH="70">Size</th>
            <th>Server</th>
            <th>Target</th>
        </tr>
        </thead>

        <tbody>
        <?php

        $sqlquery = "SELECT * FROM LMB_HISTORY_BACKUP ORDER BY ERSTDATUM DESC";
        $rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
        if(!$rs) {$commit = 1;}
        if(!$maxresult){$maxresult = 100;}
        $status = "OK";
        $bzm = 1;
        while(lmbdb_fetch_row($rs) AND $bzm <= $maxresult) : 

            if(!lmbdb_result($rs,"RESULT")){$bgColor = "#FBE3BF";$status = "FALSE";}
            elseif(lmbdb_result($rs,"ACTION") == "SAVE DATA"){$bgColor = "#D0DEEF";}
            elseif(lmbdb_result($rs,"ACTION") == "SAVE LOG"){$bgColor = "#CEDBF7";}
            elseif(lmbdb_result($rs,"ACTION") == "SAVE PAGES"){$bgColor = "#E3ECF5";}
            else{$bgColor = "#FFFFFF";}

            $color = lmbSuggestColor($bgColor);
            ?>

            <TR style="background-color:<?=$bgColor?>; color:<?=$color?>;">
            <TD style="background-color: inherit; color: inherit;"><?= lmbdb_result($rs,"ID") ?></TD>
            <TD style="background-color: inherit; color: inherit;"><?= lmbdb_result($rs,"ACTION") ?></TD>
            <TD style="background-color: inherit; color: inherit;"><?= get_date(lmbdb_result($rs,"ERSTDATUM"),2) ?></TD>
            <TD style="background-color: inherit; color: inherit;"><?= $status.' '.lmbdb_result($rs,"MESSAGE") ?></TD>
            <TD style="background-color: inherit; color: inherit;"><?= lmbdb_result($rs,"MEDIUM") ?></TD>
            <TD style="background-color: inherit; color: inherit;"><?= file_size(lmbdb_result($rs,"SIZE")*1024) ?></TD>
            <TD style="background-color: inherit; color: inherit;"><?= lmbdb_result($rs,"SERVER") ?></TD>
            <TD style="background-color: inherit; color: inherit;"><?= lmbdb_result($rs,"LOCATION") ?></TD>
            </TR>
            
        <?php
            $bzm++;
        endwhile; ?>
        </tbody>

    </table>

</div>
