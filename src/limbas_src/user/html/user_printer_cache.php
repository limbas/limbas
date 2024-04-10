<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */



if($delete_single AND is_numeric($delete_single)){
    $cache_select[$delete_single] = 1;
    $delete_selected = 1;
}

if($print_single AND is_numeric($print_single)){
    $cache_select[$print_single] = 1;
    $print_selected = 1;
}

if($delete_selected && is_array($cache_select)) {
    foreach ($cache_select as $id => $value) {
        $sqlquery = "DELETE FROM LMB_PRINTER_CACHE WHERE LMB_PRINTER_CACHE.USER_ID = " . $session['user_id'] . " AND ID = " . parse_db_int($id);
        $rs = lmbdb_exec($db, $sqlquery) or errorhandle(lmbdb_errormsg($db), $sqlquery, $action, __FILE__, __LINE__);
    }
}

$sqlquery = "SELECT
  LMB_PRINTER_CACHE.ID,
  LMB_PRINTER_CACHE.USER_ID,
  LMB_PRINTER_CACHE.GROUP_ID,
  LMB_PRINTER_CACHE.ERSTDATUM,
  LMB_PRINTER_CACHE.PRINTER_ID,
  LMB_PRINTER_CACHE.FILE_ID,
  LDMS_FILES.NAME AS FILENAME
  FROM LMB_PRINTER_CACHE,LDMS_FILES 
  WHERE LMB_PRINTER_CACHE.USER_ID = ".$session['user_id']." AND LMB_PRINTER_CACHE.FILE_ID = LDMS_FILES.ID";
$rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);

while(lmbdb_fetch_row($rs)) {

    $key = lmbdb_result($rs, 'ID');
    $printer = lmbdb_result($rs, 'PRINTER_ID');

    $cache[$printer][$key]['file_id'] = lmbdb_result($rs, 'FILE_ID');
    $cache[$printer][$key]['erstdatum'] = lmbdb_result($rs, 'ERSTDATUM');
    $cache[$printer][$key]['group_id'] = lmbdb_result($rs, 'GROUP_ID');
    $cache[$printer][$key]['user_id'] = lmbdb_result($rs, 'USER_ID');
    $cache[$printer][$key]['filename'] = lmbdb_result($rs, 'FILENAME');
    $cachelist[$key] = $printer;

}


if($print_selected && is_array($cache_select)) {
    foreach ($cache_select as $id => $value) {

        $printer = $cachelist[$id];

        if(!is_numeric($printer)){
            continue;
        }

        if (!lmbPrint($printer, null, $cache[$printer][$key]['file_id'], true)) {
            lmb_log::error('print failed');
            $errormsg = lmb_log::getLogMessage(true);
            lmb_alert(print_r($errormsg,1));
            $cache_success[$id] = false;
        }

        #$sqlquery = "DELETE FROM LMB_PRINTER_CACHE WHERE LMB_PRINTER_CACHE.USER_ID = " . $session['user_id'] . " AND ID = " . parse_db_int($id);
        #$rs = lmbdb_exec($db, $sqlquery) or errorhandle(lmbdb_errormsg($db), $sqlquery, $action, __FILE__, __LINE__);
        $cache_success[$id] = true;
    }
}




?>


<script>

function select_all(val) {

    if (val.checked) {
        $('.cache_select').prop("checked", true);
    }else{
        $('.cache_select').prop("checked", false);
    }

}

</script>


<div class="container-fluid p-3">

<FORM ACTION="main.php" METHOD=post name="form1">
    <input type="hidden" name="action" value="user_printer_cache">

    <ul class="nav nav-tabs">
        <li class="nav-item">
            <a class="nav-link active bg-contrast'?>" href="main.php?action=user_printer_cache"><?=$lang[$LINK["name"][$LINK_ID['user_printer_cache']]]?></a>
        </li>
    </ul>
    <div class="tab-content border border-top-0 bg-contrast">
        <div class="tab-pane active">

                <table class="table table-sm table-striped mb-0">
                    <thead>
                    <tr>
                        <th class="border-top-0">Dokument</th>
                        <th class="border-top-0">Benutzer</th>
                        <th class="border-top-0"></th>
                        <th class="border-top-0"></th>
                        <th class="border-top-0"></th>
                    </tr>
                    </thead>

                    <?php foreach ($cache as $printer => $pcache) { ?>

                    <TR class="table-section">
                        <TD colspan="2"><?=$gprinter[$printer]['name']?></TD>
                        <TD></TD>
                        <TD></TD>
                        <TD><input type="checkbox" onclick="select_all(this)"></TD>
                    </TR>

                    <?php foreach ($pcache as $key => $fcache) { ?>

                    <TR
                        <?php if($cache_success[$key] === true){ ?>
                            class="table-success"
                        <?php } elseif($cache_success[$key] === false){ ?>
                            class="table-danger"
                        <?php } ?>
                    >
                        <TD><a href="main.php?action=download&ID=<?=$cache[$printer][$key]['file_id']?>" target="_new"><?=$cache[$printer][$key]['filename']?></a></TD>
                        <TD><?=$userdat["bezeichnung"][$cache[$printer][$key]['user_id']]?></TD>
                        <TD><A HREF="main.php?action=user_printer_cache&print_single=<?=$key?>"><i class="lmb-icon lmb-print"></i></A></TD>
                        <TD><A HREF="main.php?action=user_printer_cache&delete_single=<?=$key?>"><i class="lmb-icon lmb-trash"></i></A></TD>
                        <TD><input type="checkbox" class="form-check-input me-2 cache_select" name="cache_select[<?=$key?>]">

                            <?php if($cache_success[$key] === true){ ?>
                                <label class="form-check-label" for="flexCheckChecked"><i class="lmb-icon lmb-aktiv"></i></label>
                            <?php } elseif($cache_success[$key] === false){ ?>
                                <label class="form-check-label" for="flexCheckChecked"><i class="lmb-icon lmb-exclamation"></i></label>
                            <?php } ?>

                        </TD>
                    </TR>

                    <?php }} ?>

                    <tfoot>
                        <tr class="border-bottom border-top">
                            <td colspan="6">
                                <button class="btn btn-sm btn-primary" type="submit" name="print_selected" value="1">ausgewählte drucken</button>
                                <button class="btn btn-sm btn-danger" type="submit" name="delete_selected" value="1">ausgewählte löschen</button>
                            </td>
                        </tr>

                    </tfoot>
                </table>



        </div>
    </div>

</FORM>

</div>
