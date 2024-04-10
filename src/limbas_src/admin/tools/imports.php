<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */


?>


<script>

<?php
if(!$aktivid){$aktivid = 1;}

if($imp_msg){
?>
alert('<?=$lang[988]?>:\n\n<?= $imp_msg ?>');
<?php }?>

</script>



<div class="container-fluid p-3">
    <FORM ENCTYPE="multipart/form-data" ACTION="main_admin.php" METHOD="post" name="form1">
        <INPUT TYPE="hidden" NAME="action" VALUE="setup_import">
        <INPUT TYPE="hidden" NAME="aktivid"  VALUE="<?=$aktivid?>">
        <INPUT TYPE="hidden" NAME="import_action">
        <INPUT TYPE="hidden" NAME="kompimport">
        <INPUT TYPE="hidden" NAME="remoteimport">
        <INPUT TYPE="hidden" NAME="syncimport">
        <INPUT TYPE="hidden" NAME="odbcimport" VALUE="<?=$odbcimport?>">
        <INPUT TYPE="hidden" NAME="setup">
        <INPUT TYPE="hidden" NAME="del_all">
        <INPUT TYPE="hidden" NAME="install">
        <INPUT TYPE="hidden" NAME="convertimport">
        <INPUT TYPE="hidden" NAME="precheck">
        <INPUT TYPE="hidden" NAME="hold_id" VALUE="1">
        <INPUT TYPE="hidden" NAME="confirm_fileimport">
        <INPUT TYPE="hidden" NAME="confirm_syncimport">
        <INPUT TYPE="hidden" NAME="template">
        <INPUT TYPE="hidden" NAME="odbc[odbc_table]" VALUE="<?=$odbc['odbc_table']?>">


        <ul class="nav nav-tabs">
            <li class="nav-item">
                <a class="nav-link <?=($aktivid==1)?'active bg-contrast':''?>" href="#" onclick="document.form1.aktivid.value=1;document.form1.submit();"><?=$lang[990]?></a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?=($aktivid==2)?'active bg-contrast':''?>" href="#" onclick="document.form1.aktivid.value=2;document.form1.submit();"><?=$lang[1006]?></a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?=($aktivid==5)?'active bg-contrast':''?>" href="#" onclick="document.form1.aktivid.value=5;document.form1.submit();"><?=$lang[2240]?></a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?=($aktivid==3)?'active bg-contrast':''?>" href="#" onclick="document.form1.aktivid.value=3;document.form1.submit();"><?=$lang[2208]?></a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?=($aktivid==6)?'active bg-contrast':''?>" href="#" onclick="document.form1.aktivid.value=6;document.form1.submit();"><?=$lang[2860]?></a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?=($aktivid==7)?'active bg-contrast':''?>" href="#" onclick="document.form1.aktivid.value=7;document.form1.submit();">ODBC Import</a>
            </li>
        </ul>

        <div class="tab-content">
            <div class="tab-pane active">

                <?php

                ob_flush();

                if($aktivid == 1){
                    require_once(__DIR__.'/import_part.php');
                }elseif($aktivid == 2){
                    require_once(__DIR__.'/import_complete.php');
                }elseif($aktivid == 5){
                    require_once(__DIR__.'/import_convert.php');
                }elseif($aktivid == 3){
                    require_once(__DIR__.'/import_project.php');
                }elseif($aktivid == 6){
                    require_once(__DIR__.'/import_syncs.php');
                }elseif($aktivid == 7){
                    require_once(__DIR__.'/import_odbc.php');
                }

                ?>

            </div>
        </div>
        



    </FORM>
</div>
