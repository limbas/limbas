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
 * ID: 108
 */
?>

<style>
    textarea {
        resize: vertical;
        min-height: 40px;
    }
</style>

<form action="main_admin.php" method="post" name="form1">
    <input type="hidden" name="action" value="setup_error_msg">
    <input type="hidden" name="del">
    <input type="hidden" name="log_typ" value="<?= $log_typ ?>">
    <input type="hidden" name="sort" value="<?= $sort ?>">

    <div class="lmbPositionContainerMainTabPool">
        <table class="tabpool" border="0" cellspacing="0" cellpadding="0">
            <tr>
                <td>
                    <table border="0" cellspacing="0" cellpadding="0" style="width: 100%;">
                        <tr class="tabpoolItemTR">
                            <td nowrap class="tabpoolItem<?= $log_typ == 1 || !$log_typ ? 'Active' : 'Inactive' ?>"
                                OnClick="document.location.href='main_admin.php?action=setup_error_msg&log_typ=1'">
                                sql_error.log
                            </td>
                            <td nowrap class="tabpoolItem<?= $log_typ == 2 ? 'Active' : 'Inactive' ?>"
                                OnClick="document.location.href='main_admin.php?action=setup_error_msg&log_typ=2'">
                                indize_error.log
                            </td>
                            <td nowrap class="tabpoolItem<?= $log_typ == 3 ? 'Active' : 'Inactive' ?>"
                                OnClick="document.location.href='main_admin.php?action=setup_error_msg&log_typ=3'">
                                indize.log
                            </td>
                            <td class="tabpoolItemSpace">&nbsp;</td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td class="tabpoolfringe">
                    <table border="0" cellpadding="2" width="100%" class="tabBody" style="border-spacing: 10px;">
                        <?php
                        if ($log_typ == 1 OR !$log_typ):
                            $path = $umgvar['pfad'] . '/TEMP/log/sql_error.log';

                            if ($del) {
                                unlink($path);
                                $rf = fopen($path, 'w');
                                fclose($rf);
                            }

                            if (!$showcount) {
                                $showcount = 50;
                            }
                            if (file_exists($path)) {
                                $handle = file($path);
                                $lcount = count($handle);
                            }
                            ?>

                            <?php if (!is_writable($path)): ?>
                                <tr class="tabBody">
                                    <td colspan="9" style="color: red;">
                                        <i class="lmb-icon lmb-exclamation-triangle"></i>
                                        Logfile is not writable!
                                    </td>
                                </tr>
                            <?php endif; ?>
                            <tr class="tabBody">
                                <td colspan="2"><?= $lang[86] ?></td>
                                <td><?= $lcount ?></td>
                            </tr>
                            <tr class="tabBody">
                                <td colspan="2"><?= $lang[550] ?></td>
                                <TD><i class="lmb-icon lmb-trash" onclick="document.form1.del.value=1;document.form1.submit();"></i></TD>
                            </tr>
                            <tr class="tabBody">
                                <td colspan="2"><?= $lang[96] ?></td>
                                <td><input type="text" value="<?= $showcount ?>" name="showcount" size="5"></td>
                            </tr>
                            <tr class="tabBody">
                                <td colspan="2"><?= $lang[1837] ?></td>
                                <td>
                                    <?php
                                    if (!$sort) {
                                        $sort = 'desc';
                                    }
                                    ?>
                                    <i class="lmb-icon lmb-textsort-up" onclick="document.form1.sort.value='asc';document.form1.submit();" title="<?= $lang[861] ?>" style="font-weight: <?= $sort == 'asc' ? 'bold' : 'normal' ?>"></i>
                                    <i class="lmb-icon lmb-textsort-down" onclick="document.form1.sort.value='desc';document.form1.submit();" title="<?= $lang[862] ?>" style="font-weight: <?= $sort == 'desc' ? 'bold' : 'normal' ?>"></i>
                                </td>
                            </tr>
                            <tr class="tabBody">
                                <td colspan="9">&nbsp;</td>
                            </tr>
                            <tr class="tabHeader">
                                <td class="tabHeaderItem"><?= $lang[3] ?></td>
                                <td class="tabHeaderItem"><?= $lang[543] ?></td>
                                <td class="tabHeaderItem"><?= $lang[544] ?></td>
                                <td class="tabHeaderItem"><?= $lang[545] ?></td>
                                <td class="tabHeaderItem"><?= $lang[546] ?></td>
                                <td class="tabHeaderItem"><?= $lang[547] ?></td>
                                <td class="tabHeaderItem"><?= $lang[548] ?></td>
                            </tr>

                            <?php
                            if ($handle) {
                                $showcount = min($showcount, $lcount);
                                if ($sort == 'desc') {
                                    $start = $lcount - $showcount;
                                } else {
                                    $start = 0;
                                }
                                $handle = array_slice($handle, $start, $showcount);
                                if ($sort == 'desc') {
                                    $handle = array_reverse($handle);
                                }

                                foreach ($handle as $line):
                                    if (!$line) continue;
                                    list($fDate, $fUser, $fAction, $fFile, $fLine, $fError, $fQuery) = explode("\t", $line);
                                    ?>

                                    <tr class="tabBody">
                                        <td valign="top"><?= $fUser ?></td>
                                        <td valign="top"><?= $fDate ?></td>
                                        <td valign="top"><?= $fAction ?></td>
                                        <td valign="top"><?= $fFile ?></td>
                                        <td valign="top"><?= $fLine ?></td>
                                        <td valign="top">
                                            <textarea readonly cols="50" rows="3"><?= htmlentities($fError, ENT_QUOTES, $umgvar["charset"]) ?></textarea>
                                        </td>
                                        <td valign="top">
                                            <textarea readonly cols="50" rows="3"><?= htmlentities($fQuery, ENT_QUOTES, $umgvar["charset"]) ?></textarea>
                                        </td>
                                    </tr>
                                <?php endforeach;
                            }
                            ?>

                        <?php elseif ($log_typ == 2):
                            $path = $umgvar['pfad'] . '/TEMP/log/indize_error.log';
                            if ($del) {
                                unlink($path);
                                $rf = fopen($path, 'w');
                                fclose($rf);
                            }
                            ?>

                            <?php if (!is_writable($path)): ?>
                                <tr class="tabBody">
                                    <td colspan="9" style="color: red;">
                                        <i class="lmb-icon lmb-exclamation-triangle"></i>
                                        Logfile is not writable!
                                    </td>
                                </tr>
                            <?php endif; ?>

                            <tr class="tabBody">
                                <td>
                                    <?= $lang[550] ?>
                                    &nbsp;&nbsp;&nbsp;
                                    <i class="lmb-icon lmb-trash" onclick="document.form1.del.value=1;document.form1.submit();"></i>
                                </td>
                            </tr>
                            <tr class="tabBody">
                                <td>&nbsp;</td>
                            </tr>
                            <tr>
                                <td>
                                    <textarea readonly style="width:100%;height:500px;overflow:auto;">
                                        <?php if (file_exists($path)) {
                                            echo file_get_contents($path);
                                        } ?>
                                    </textarea>
                                </td>
                            </tr>

                        <?php elseif ($log_typ == 3):
                            $path = $umgvar['pfad'] . '/TEMP/log/indize.log';
                            if ($del) {
                                unlink($path);
                                $rf = fopen($path, 'w');
                                fclose($rf);
                            }
                            ?>


                            <?php if (!is_writable($path)): ?>
                                <tr class="tabBody">
                                    <td colspan="2" style="color: red;">
                                        <i class="lmb-icon lmb-exclamation-triangle"></i>
                                        Logfile is not writable!
                                    </td>
                                </tr>
                            <?php endif; ?>

                            <tr class="tabBody">
                                <td colspan="2">
                                    <?= $lang[550] ?> &nbsp;&nbsp;&nbsp;
                                    <i class="lmb-icon lmb-trash" onclick="document.form1.del.value=1;document.form1.submit();"></i>
                                </td>
                            </tr>
                            <tr class="tabBody">
                                <td colspan="2">&nbsp;</td>
                            </tr>
                            <tr class="tabHeader">
                                <td class="tabHeaderItem"><?= $lang[543] ?></td>
                                <td class="tabHeaderItem"><?= $lang[544] ?></td>
                            </tr>
                            <?php
                            if (file_exists($path)) {
                                $handle = fopen($path, 'r');
                                if ($handle) {
                                    while (($line = fgets($handle)) !== false) {
                                        if (!$line) continue;

                                        $line = htmlentities($line, ENT_QUOTES, $umgvar['charset']);
                                        list($date, $message) = explode(' : ', $line, 2);
                                        echo '<tr>';
                                        echo '<td>', $date, '</td>';
                                        echo '<td>', $message, '</td>';
                                        echo '</tr>';
                                    }
                                    fclose($handle);
                                }
                            }
                            ?>
                        <?php endif; ?>
                    </table>
                </td>
            </tr>
        </table>
    </div>
</form>