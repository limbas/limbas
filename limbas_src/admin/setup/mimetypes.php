<?php
/*
 * Copyright notice
 * (c) 1998-2021 Limbas GmbH(support@limbas.org)
 * All rights reserved
 * This script is part of the LIMBAS project. The LIMBAS project is free software; you can redistribute it and/or modify it on 2 Ways:
 * Under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * Or
 * In a Propritary Software Licence http://limbas.org
 * The GNU General Public License can be found at http://www.gnu.org/copyleft/gpl.html.
 * A copy is found in the textfile GPL.txt and important notices to the license from the author is found in LICENSE.txt distributed with these scripts.
 * This script is distributed WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * This copyright notice MUST APPEAR in all copies of the script!
 * Version 4.3.36.1319
 */

/*
 * ID: 164
 */

?>

<script language="JavaScript">
function change_mimetype(id) {
	document.form1.changeIds.value = document.form1.changeIds.value + ";" + id;
}
function del_mimetype(id) {
    if (confirm('<?= $lang[84] ?>')) {
        document.form1.del.value = id;
        document.form1.submit();
    }
}
</script>

<form action="main_admin.php" method="post" name="form1">
    <input type="hidden" name="action" value="setup_mimetypes">
    <input type="hidden" name="add">
    <input type="hidden" name="change">
    <input type="hidden" name="changeIds">
    <input type="hidden" name="del">

    <div class="lmbPositionContainerMain" style="width: 700px;">
        <table class="tabfringe" border="0" cellspacing="0" cellpadding="1" style="width:100%;">
            <tr class="tabHeader">
                <td class="tabHeaderItem" nowrap><b>ID &nbsp;</b></td>
                <td class="tabHeaderItem" nowrap><b>&nbsp; Mimetype &nbsp;</b></td>
                <td class="tabHeaderItem" nowrap><b>&nbsp; Ext &nbsp;</b></td>                    
                <td class="tabHeaderItem" nowrap colspan="2"><b>&nbsp; Pic &nbsp;</b></td>                    
                <td class="tabHeaderItem" nowrap style="text-align: center;"><b>&nbsp; Delete &nbsp;</b></td>                    
            </tr>
            
            <?php
            foreach($result_mimetypes['id'] as $index => $id) {
            ?>

                <tr class="tabBody">
                    <!-- id -->
                    <td nowrap class="tabitem vAlignTop">
                        <?=$id?>
                    </td>
                    <!-- mimetype -->
                    <td nowrap class="tabitem vAlignTop"> 
                        <input type="text" name="mimetype_<?=$id?>" style="width:350px;" value="<?= $result_mimetypes['mimetype'][$index]?>" onchange="change_mimetype('<?= $id ?>');">
                    </td>
                    <!-- ext -->
                    <td nowrap class="tabitem vAlignTop">
                        <input type="text" name="ext_<?=$id?>" style="width:80px;" value="<?= $result_mimetypes['ext'][$index]?>" onchange="change_mimetype('<?= $id ?>');">
                    </td>
                    <!-- pic -->
                    <td nowrap class="tabitem vAlignTop">
                        <?php
                            $filename = $result_mimetypes['pic'][$index];
                            if($filename && file_exists("pic/fileicons/$filename")) {
                                echo "&nbsp;&nbsp;<img src=\"pic/fileicons/$filename\">";
                            }
                        ?>
                    </td>
                    <td nowrap class="tabitem vAlignTop">
                        <input type="text" name="pic_<?=$id?>" style="width:80px;" value="<?= $result_mimetypes['pic'][$index]?>" onchange="change_mimetype('<?= $id ?>');">
                    </td>
                    <!-- delete -->
                    <td nowrap class="tabitem vAlignTop" style="text-align: center;">
                        <i class="lmb-icon lmb-trash" title="delete mimetype <?=$result_mimetypes['mimetype'][$index]?>" style="cursor:pointer;" OnClick="del_mimetype('<?= $id ?>')"></i>
                    </td>
                </tr>	

            <?php
            }             
            ?>
            
            <tr class="tabBody"><td colspan="6"><HR></td></tr>
            
            <tr class="tabBody">
                <td><input type="submit" value="<?=$lang[522]?>" onclick="document.form1.change.value='1';"></td>
            </tr>
   
            <tr class="tabBody"><td colspan="6"><HR></td></tr>
            
            <tr class="tabBody">
                <td></td>
                <td><input type="text" name="new_mimetype" style="width:350px;"></td>
                <td><input type="text" name="new_ext" style="width:80px;"></td>
                <td></td>
                <td><input type="text" name="new_pic" style="width:80px;"></td>
                <td><input type="submit" value="<?=$lang[540]?>" onclick="document.form1.add.value='1';"></td>
            </tr>
            <tr class="tabFooter"><td colspan="6"></td></tr>
            
        </table>
    </div>
</form>
