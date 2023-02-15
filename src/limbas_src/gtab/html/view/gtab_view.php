<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */


?>

<link rel="stylesheet" type="text/css" href="assets/vendor/datatables/dataTables.bootstrap5.min.css"/>
<script type="text/javascript" src="assets/vendor/datatables/jquery.dataTables.js"></script>
<script type="text/javascript" src="assets/vendor/datatables/dataTables.bootstrap5.min.js"></script>


<div class="container-fluid p-3">
    <div class="card">
        <div class="card-body">
            <?php
            if(!$gtab["tab_id"][$gtabid]){return false;}

            $sqlquery = "SELECT * FROM ".$gtab["table"][$gtabid];
            $rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
            $sTable = "id='lmbGlistBodyView_$gtabid' class=\"table table-sm table-bordered table-striped\" style=\"width:100%\"";
            echo ODBCResourceToHTML($rs, $sTable, '', $umgvar["resultspace"]);

            ?>  
        </div>
    </div>

</div>


<script type="text/javascript">
    $(document).ready(
        function () {
            $('#lmbGlistBodyView_<?=$gtabid?>').DataTable({
                scrollY: 'calc(100vh - 200px)',
                scrollX: true,
                scrollCollapse: true,
                paging: false,
                fixedColumns: true,
                colReorder: true
            });
            $('#lmbGlistBodyView_<?=$gtabid?>_wrapper').prepend("<div style=';float:left;font-size:1.3em'><?=$gtab["desc"][$gtabid]?></div>");
        }
    );
</script>
