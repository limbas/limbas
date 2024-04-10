<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */


?>

<div class="container-fluid p-3">
    <FORM ACTION="main_admin.php" METHOD=post name="form1">
        <input type="hidden" name="action" value="setup_error_msg">
        <input type="hidden" name="del">
        <input type="hidden" name="log_typ" value="<?= $log_typ ?>">
        <input type="hidden" name="sort" value="<?= $sort ?>">

        
        <ul class="nav nav-tabs">
            <li class="nav-item">
                <a class="nav-link <?= $log_typ == 1 || !$log_typ ? 'active bg-contrast' : '' ?>" href="main_admin.php?action=setup_error_msg&log_typ=1">sql_error.log</a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= $log_typ == 2 ? 'active bg-contrast' : '' ?>" href="main_admin.php?action=setup_error_msg&log_typ=2">indize_error.log</a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= $log_typ == 3 ? 'active bg-contrast' : '' ?>" href="main_admin.php?action=setup_error_msg&log_typ=3">indize.log</a>
            </li>
        </ul>
        <div class="tab-content border border-top-0 bg-contrast">
            <div class="tab-pane active">

                <?php
                if ($log_typ == 1 OR !$log_typ):
                    $path = TEMPPATH . 'log/sql_error.log';

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
                        $lcount = lmb_count($handle);
                    }
                    ?>

                    <div class="p-3">
                        <?php if (!is_writable($path)): ?>
                            <p class="text-danger"><i class="lmb-icon lmb-exclamation-triangle"></i> Logfile is not writable!</p>
                        <?php endif; ?>

                        <p><?= $lang[86] ?>: <?= $lcount ?></p>
                        <p><?= $lang[550] ?>: <i class="lmb-icon lmb-trash" onclick="document.form1.del.value=1;document.form1.submit();"></i></p>
                        <p><?= $lang[96] ?>: <input type="text" value="<?= $showcount ?>" name="showcount" size="5" class="form-control form-control-sm d-inline w-25"></p>
                        <p><?= $lang[1837] ?>: <?php
                            if (!$sort) {
                                $sort = 'desc';
                            }
                            ?>
                            <i class="lmb-icon lmb-textsort-up" onclick="document.form1.sort.value='asc';document.form1.submit();" title="<?= $lang[861] ?>" style="font-weight: <?= $sort == 'asc' ? 'bold' : 'normal' ?>"></i>
                            <i class="lmb-icon lmb-textsort-down" onclick="document.form1.sort.value='desc';document.form1.submit();" title="<?= $lang[862] ?>" style="font-weight: <?= $sort == 'desc' ? 'bold' : 'normal' ?>"></i></p>
                    </div>
                    

                    <table class="table table-sm table-striped mb-0">
                        <thead>
                        <tr>
                            <th><?= $lang[3] ?></th>
                            <th><?= $lang[543] ?></th>
                            <th><?= $lang[544] ?></th>
                            <th><?= $lang[545] ?></th>
                            <th><?= $lang[546] ?></th>
                            <th><?= $lang[547] ?></th>
                            <th><?= $lang[548] ?></th>
                        </tr>
                        </thead>

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

                                <tr>
                                    <td><?= $fUser ?></td>
                                    <td><?= $fDate ?></td>
                                    <td><?= $fAction ?></td>
                                    <td><?= $fFile ?></td>
                                    <td><?= $fLine ?></td>
                                    <td>
                                        <textarea readonly cols="50" rows="3" class="form-control form-control-sm"><?= htmlentities($fError, ENT_QUOTES, $umgvar["charset"]) ?></textarea>
                                    </td>
                                    <td>
                                        <textarea readonly cols="50" rows="3" class="form-control form-control-sm"><?= htmlentities($fQuery, ENT_QUOTES, $umgvar["charset"]) ?></textarea>
                                    </td>
                                </tr>
                            <?php endforeach;
                        }
                        ?>

                    </table>
                
                
                    

                <?php elseif ($log_typ == 2):
                    $path = TEMPPATH . 'log/indize_error.log';
                    if ($del) {
                        unlink($path);
                        $rf = fopen($path, 'w');
                        fclose($rf);
                    }
                    ?>


                    <div class="p-3">
                        <?php if (!is_writable($path)): ?>
                            <p class="text-danger"><i class="lmb-icon lmb-exclamation-triangle"></i> Logfile is not writable!</p>
                        <?php endif; ?>

                        <p><?= $lang[550] ?>: <i class="lmb-icon lmb-trash" onclick="document.form1.del.value=1;document.form1.submit();"></i></p>

                        <textarea readonly class="form-control form-control-sm">
                                        <?php if (file_exists($path)) {
                                            echo file_get_contents($path);
                                        } ?>
                                    </textarea>
                    </div>

                <?php elseif ($log_typ == 3):
                    $path = TEMPPATH . 'log/indize.log';
                    if ($del) {
                        unlink($path);
                        $rf = fopen($path, 'w');
                        fclose($rf);
                    }
                    ?>


                    <div class="p-3">
                        <?php if (!is_writable($path)): ?>
                            <p class="text-danger"><i class="lmb-icon lmb-exclamation-triangle"></i> Logfile is not writable!</p>
                        <?php endif; ?>
                        <p class="mb-0"><?= $lang[550] ?>: <i class="lmb-icon lmb-trash" onclick="document.form1.del.value=1;document.form1.submit();"></i></p>
                    </div>


                    <table class="table table-sm table-striped mb-0">
                        <thead>
                        <tr>
                            <th><?= $lang[543] ?></th>
                            <th><?= $lang[544] ?></th>
                        </tr>
                        </thead>

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

                    </table>
                    
                <?php endif; ?>
                
                

            </div>
        </div>



    </FORM>
</div>
