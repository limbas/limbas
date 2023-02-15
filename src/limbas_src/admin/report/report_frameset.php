<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */



$greportlist = resultreportlist();
$greport = $greportlist[$greportlist['gtabid'][$report_id]];


if($greport['defaultformat'][$report_id] == 'mpdf'){
    require_once(COREPATH . 'admin/report/report_editor.php');
}else{
?>
<div class="frame-container">
    <iframe name="report_main" src="main_admin.php?&action=setup_report_main&report_id=<?=$report_id?>&referenz_tab=<?=$greportlist['gtabid'][$report_id]?>" class="frame-fill"></iframe>
    <iframe name="report_menu" src="main_admin.php?&action=setup_report_menu&report_id=<?=$report_id?>&referenz_tab=<?=$greportlist['gtabid'][$report_id]?>" style="width: 240px;"></iframe>
</div>
<?php
}
?>
