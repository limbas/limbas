<?php
/*
 * Copyright notice
 * (c) 1998-2018 Limbas GmbH(support@limbas.org)
 * All rights reserved
 * This script is part of the LIMBAS project. The LIMBAS project is free software; you can redistribute it and/or modify it on 2 Ways:
 * Under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * Or
 * In a Propritary Software Licence http://limbas.org
 * The GNU General Public License can be found at http://www.gnu.org/copyleft/gpl.html.
 * A copy is found in the textfile GPL.txt and important notices to the license from the author is found in LICENSE.txt distributed with these scripts.
 * This script is distributed WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * This copyright notice MUST APPEAR in all copies of the script!
 * Version 3.5
 */

/*
 * ID: 148
 */
?>

<TABLE ID="tab6" width="100%" cellspacing="2" cellpadding="1" class="tabBody importcontainer">
<TR class="tabHeader"><TD class="tabHeaderItem" COLSPAN="5"><?=$lang[2860]?></TD></TR>
<TR class="tabBody"><TD>File</TD><TD><input type="file" NAME="filesync" style="width:250px;"></TD></TR>
<TR class="tabBody"><TD></TD><TD><input type="button" value="<?=$lang[2243]?>" onclick="document.form1.precheck.value=1;document.form1.syncimport.value=1;document.form1.submit();"></TD></TR>
<TR class="tabBody"><TD colspan="2" class="tabFooter"></TD></TR>
</TABLE>


<?php

    require_once("admin/tools/import_sync.php");
    if (($syncimport AND !empty($_FILES["filesync"])) OR $confirm_syncimport) {
        $result = secure_sync_import($_FILES["filesync"]['tmp_name'], true, $precheck ? 2 : 0, $confirm_syncimport);
        #$result = sync_import(null, $precheck ? 2 : 0, $confirm_syncimport, $_FILES["filesync"]['tmp_name']);
        if(!$result['success']) {
            echo "Errors!";
            print_r($result,1);
        } elseif(!$confirm_syncimport) {
            echo '<div class="lmbPositionContainerMain">';
            echo '<input type="button" value="' . $lang[979] . '" onclick="document.form1.confirm_syncimport.value=1;document.form1.submit();" style="color:green">';
            echo '</div>';
        }
    }



?>