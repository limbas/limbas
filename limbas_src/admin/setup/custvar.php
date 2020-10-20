<?php
/*
 * Copyright notice
 * (c) 1998-2019 Limbas GmbH(support@limbas.org)
 * All rights reserved
 * This script is part of the LIMBAS project. The LIMBAS project is free software; you can redistribute it and/or modify it on 2 Ways:
 * Under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * Or
 * In a Propritary Software Licence http://limbas.org
 * The GNU General Public License can be found at http://www.gnu.org/copyleft/gpl.html.
 * A copy is found in the textfile GPL.txt and important notices to the license from the author is found in LICENSE.txt distributed with these scripts.
 * This script is distributed WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * This copyright notice MUST APPEAR in all copies of the script!
 * Version 3.6
 */

/*
 * ID:
 */

?>
<FORM ACTION="main_admin.php" METHOD=post name="form1">
    <input type="hidden" name="action" value="setup_custvar">
    <input type="hidden" name="changevar">


    <div class="lmbPositionContainerMainTabPool">
        <table class="tabpool" style="width:97%" cellspacing="0" cellpadding="0">

            <tbody>
            <tr>
                <td>
                    <table width="100%" cellspacing="0" cellpadding="0">
                        <tbody>
                        <tr>
            <?php
            if ($LINK[43]) {
                                echo "<TD class=\"tabpoolItemInactive\" NOWRAP  OnClick=\"document.location.href='main_admin.php?action=setup_umgvar'\">".$lang[$LINK["desc"][43]]."</TD>";
            }
            if ($LINK['setup_custvar']) {
                                echo '<TD class="tabpoolItemActive" NOWRAP>'.$lang[$LINK["desc"][$LINK_ID['setup_custvar']]].'</TD>';
            }
            ?>
                            <td class="tabpoolItemSpace">&nbsp;</td>
                        </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
            <tr>
                <td class="tabpoolfringe">

                    <table class="tabfringe" border="0" cellspacing="0" cellpadding="1" style="width:100%">
                        <tr class="tabHeader">
                            <td class="tabHeaderItem"><?php echo $lang[926];?></td>
                            <td class="tabHeaderItem"><?php echo $lang[29];?></td>
                            <td class="tabHeaderItem"><?php echo $lang[126];?></td>
                            <td class="tabHeaderItem"><?php echo $lang[2957];?></td>
                            <td class="tabHeaderItem"><?php echo $lang[632];?></td>
                        </tr>
                        <?php

                        /* --- Ergebnisliste --------------------------------------- */
                        foreach ($result_custvar["id"] as $key1 => $value1){
                        #$rowcol = lmb_getRowColor();
                        ?>
                        <TR class="tabBody" style="background-color:<?= $rowcol ?>">
                            <TD nowrap class="tabitem" VALIGN="TOP"><?= $result_custvar["key"][$key1] ?>&nbsp;</TD>
                            <TD nowrap class="tabitem" VALIGN="TOP"><INPUT TYPE="TEXT" SIZE="50" OnChange="document.form1.changevar.value=document.form1.changevar.value+',<?= $result_custvar["id"][$key1] ?>'" VALUE="<?= str_replace("\"", "&quot;", str_replace("<", "&lt;", $result_custvar['value'][$key1])) ?>" NAME="cv_val_<?= $result_custvar['id'][$key1] ?>">
                            </TD>
                            <TD nowrap class="tabitem" VALIGN="TOP"><?= $result_custvar["description"][$key1] ?>&nbsp;
                            </TD>
                            <TD><INPUT TYPE="checkbox" NAME="cv_ov_<?= $result_custvar['id'][$key1] ?>" value="1" <?php echo ($result_custvar["overridable"][$key1]) ? 'checked' : ''; ?> OnChange="document.form1.changevar.value=document.form1.changevar.value+',<?= $result_custvar["id"][$key1] ?>'"><?php echo ($result_custvar["overridden"][$key1]) ? '<i class="lmb-icon lmb-long-arrow-down" title="'.$lang[2960].'"></i>' : ''; ?>
                            </TD>
                            <TD><INPUT TYPE="checkbox" NAME="cv_ac_<?= $result_custvar['id'][$key1] ?>" value="1" <?php echo ($result_custvar["active"][$key1]) ? 'checked' : ''; ?> OnChange="document.form1.changevar.value=document.form1.changevar.value+',<?= $result_custvar["id"][$key1] ?>'">
                            </TD>
                            <TD nowrap class="tabitem" ALIGN="CENTER" VALIGN="TOP"><A HREF="main_admin.php?action=setup_custvar&del=1&id=<?= urlencode($result_custvar["id"][$key1]) ?>"><i class="lmb-icon lmb-trash"></i></A></TD>
                            <?php
                            }


                            ?>

                        <TR class="tabBody">
                            <TD COLSPAN="6">
                                <HR>
                            </TD>
                        </TR>
                        <TR class="tabBody">
                            <TD COLSPAN="6"><INPUT TYPE="submit" VALUE="<?= $lang[522] ?>" NAME="change"></TD>
                        </TR>
                        <TR class="tabBody">
                            <TD COLSPAN="6">
                                <HR>
                            </TD>
                        </TR>

                        <TR class="tabBody">
                            <TD><?php echo $lang[926];?></TD>
                            <TD><?php echo $lang[29];?></TD>
                            <TD><?php echo $lang[126];?></TD>
                            <TD><?php echo $lang[2957];?></TD>
                            <TD><?php echo $lang[632];?></TD>
                            <TD></TD>
                        </TR>

                        <TR class="tabBody">
                            <TD><INPUT TYPE="TEXT" SIZE="30" NAME="name"></TD>
                            <TD><INPUT TYPE="TEXT" SIZE="30" NAME="value"></TD>
                            <TD><INPUT TYPE="TEXT" SIZE="70" NAME="description"></TD>
                            <TD><INPUT TYPE="checkbox" NAME="overridable" value="1"></TD>
                            <TD><INPUT TYPE="checkbox" NAME="active" value="1"></TD>
                            <TD><INPUT TYPE="submit" VALUE="<?= $lang[540] ?>" NAME="add"></TD>
                        </TR>
                        <TR class="tabFooter">
                            <TD COLSPAN="5"></TD>
                        </TR>
                </TABLE>

                </td>
            </tr>
        </tbody>
    </table>
    </div>
