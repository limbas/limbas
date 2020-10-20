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
    <input type="hidden" name="action" value="setup_currency">
    <?php if (isset($currates)) { ?>
        <input type="hidden" name="currates" value="1">
    <?php } ?>
    <input type="hidden" name="changecur">


    <div class="lmbPositionContainerMainTabPool">
        <table class="tabpool" style="min-width:700px;width:700px" cellspacing="0" cellpadding="0">

            <tbody>
            <tr>
                <td>
                    <table width="100%" cellspacing="0" cellpadding="0">
                        <tbody>
                        <tr>
                            <?php
                            echo '<TD NOWRAP class="tabpoolItem'.((isset($currates))?'Inactive':'Active').'" OnClick="document.location.href=\'main_admin.php?action=setup_currency\'">'.$lang[$LINK["name"][$LINK_ID['setup_currency']]].'</TD>';
                            echo '<TD NOWRAP class="tabpoolItem'.((isset($currates))?'Active':'Inactive').'" OnClick="document.location.href=\'main_admin.php?action=setup_currency&currates=1\'">'.$lang[2973].'</TD>';
                            ?>
                            <td class="tabpoolItemSpace">&nbsp;</td>
                        </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
            <tr>
                <td class="tabpoolfringe">


                    <?php if (!isset($currates)) { ?>
                        <table class="tabfringe" border="0" cellspacing="0" cellpadding="1" style="width:100%">
                            <tr class="tabHeader">
                                <td class="tabHeaderItem"><?php echo $lang[4]; ?></td>
                                <td class="tabHeaderItem"><?php echo $lang[2976]; ?></td>
                                <td class="tabHeaderItem"><?php echo $lang[2718]; ?></td>
                                <td class="tabHeaderItem"></td>
                            </tr>
                            <?php

                            /* --- Ergebnisliste --------------------------------------- */
                            foreach ($result_currencies as $id => $currency){
                            #$rowcol = lmb_getRowColor();
                            ?>
                            <TR class="tabBody" style="background-color:<?= $rowcol ?>">
                                <TD><INPUT TYPE="text" NAME="cur_n<?= $id ?>" value="<?php echo $currency['name']; ?>" OnChange="document.form1.changecur.value=document.form1.changecur.value+',<?= $id ?>'">
                                </TD>
                                <TD><INPUT TYPE="text" NAME="cur_c<?= $id ?>" value="<?php echo $currency['code']; ?>" OnChange="document.form1.changecur.value=document.form1.changecur.value+',<?= $id ?>'">
                                </TD>
                                <TD><INPUT TYPE="text" NAME="cur_s<?= $id ?>" value="<?php echo $currency['symbol']; ?>" OnChange="document.form1.changecur.value=document.form1.changecur.value+',<?= $id ?>'">
                                </TD>
                                <TD nowrap class="tabitem" ALIGN="CENTER" VALIGN="TOP"><A HREF="main_admin.php?action=setup_currency&del=1&id=<?= urlencode($id) ?>"><i class="lmb-icon lmb-trash"></i></A></TD>
                                <?php
                                }


                                ?>

                            <TR class="tabBody">
                                <TD COLSPAN="4">
                                    <HR>
                                </TD>
                            </TR>
                            <TR class="tabBody">
                                <TD COLSPAN="4"><INPUT TYPE="submit" VALUE="<?= $lang[522] ?>" NAME="change"></TD>
                            </TR>
                            <TR class="tabBody">
                                <TD COLSPAN="4">
                                    <HR>
                                </TD>
                            </TR>

                            <TR class="tabBody">
                                <TD><?php echo $lang[4]; ?></TD>
                                <TD><?php echo $lang[2976]; ?></TD>
                                <TD><?php echo $lang[2718]; ?></TD>
                                <TD></TD>
                            </TR>

                            <TR class="tabBody">
                                <TD><INPUT TYPE="TEXT" SIZE="30" NAME="name"></TD>
                                <TD><INPUT TYPE="TEXT" SIZE="30" NAME="code" maxlength="3"></TD>
                                <TD><INPUT TYPE="TEXT" SIZE="30" NAME="symbol"></TD>
                                <TD><INPUT TYPE="submit" VALUE="<?= $lang[540] ?>" NAME="add"></TD>
                            </TR>
                            <TR class="tabFooter">
                                <TD COLSPAN="5"></TD>
                            </TR>
                        </TABLE>
                    <?php } else { ?>
                        <table class="tabfringe" border="0" cellspacing="0" cellpadding="1" style="width:100%">
                            <tr class="tabHeader">
                                <td class="tabHeaderItem"><?php echo $lang[2049]; ?> <?php echo $lang[1367]; ?></td>
                                <td class="tabHeaderItem"><?php echo $lang[2974]; ?> <?php echo $lang[1367]; ?></td>
                                <td class="tabHeaderItem"><?php echo $lang[2973]; ?></td>
                                <td class="tabHeaderItem" colspan="2"></td>
                            </tr>
                            <?php

                            /* --- Ergebnisliste --------------------------------------- */
                            foreach ($result_rates as $exrate){
                            #$rowcol = lmb_getRowColor();
                            ?>
                                <TR class="tabBody" style="background-color:<?= $rowcol ?>">
                                <TD><?php echo $exrate['curfromc']; ?></TD>
                                <TD><?php echo $exrate['curtoc']; ?></TD>
                                <TD><?php echo $exrate['excount']; ?></TD>
                                <TD colspan="2"></TD>
                                <?php
                                }


                                ?>

                            <TR class="tabBody">
                                <TD COLSPAN="5">
                                    <HR>
                                </TD>
                            </TR>

                            <TR class="tabBody">
                                <TD><?php echo $lang[2049]; ?> <?php echo $lang[1367]; ?></TD>
                                <TD><?php echo $lang[2974]; ?> <?php echo $lang[1367]; ?></TD>
                                <TD><?php echo $lang[2975]; ?></TD>
                                <TD><?php echo $lang[197]; ?></TD>
                                <TD></TD>
                            </TR>

                            <TR class="tabBody">
                                <TD><select name="curfrom">
                                        <?php
                                            foreach ($result_currencies as $id => $currency) {
                                                echo '<option value="'.$id.'">'.$currency['code'].'</option>';
                                            }
                                        ?>
                                    </select></TD>
                                <TD><select name="curto">
                                        <?php
                                        foreach ($result_currencies as $id => $currency) {
                                            echo '<option value="'.$id.'">'.$currency['code'].'</option>';
                                        }
                                        ?>
                                    </select></TD>
                                <TD><INPUT TYPE="number" NAME="rate" step="0.0001"></TD>
                                <TD><INPUT TYPE="date" NAME="rday"></TD>
                                <TD><INPUT TYPE="submit" VALUE="<?= $lang[540] ?>" NAME="add"></TD>
                            </TR>
                            <TR class="tabFooter">
                                <TD COLSPAN="5"></TD>
                            </TR>
                        </TABLE>
                    <?php } ?>


                </td>
            </tr>
            </tbody>
        </table>
    </div>