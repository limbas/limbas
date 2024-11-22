<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */

global $lang;
global $session;
global $DBA;

use Limbas\admin\install\Installer;
use Limbas\admin\tools\update\Updater; ?>

<div class="card h-100">
    <div class="card-header">
        <h2 class="mb-0">LIMBAS-<?= $umgvar['version']; ?></h2>
    </div>
    <nav class="nav">
        <a class="nav-link" href="main.php?action=nav_info">Info</a>
        <a class="nav-link" href="main.php?action=nav_info&view=credits">credits</a>
        <a class="nav-link" href="main.php?action=nav_info&view=notes">release notes</a>
        <a class="nav-link" href="main.php?action=nav_info&view=environment_check">environment check</a>
        <a class="nav-link" href="http://www.limbas.org" target="_new">help</a>
    </nav>
    <div class="card-body">

        <?php
        if (!$view) {
            ?>


            <table class="table table-sm table-borderless fs-6">
                <TR>
                    <TD>info</td>
                </tr>
                <tr>
                    <td><?= $lang[2] ?>:</td>
                    <td class="text-muted"><?= $umgvar['version'] ?></td>
                </tr>
                <?php if ($session['user_id'] == 1) {
                    $latestVersion = Updater::checkNewVersionAvailable();

                    if ($latestVersion !== false && $latestVersion !== true):
                        ?>
                        <tr>
                            <td><?= $lang[2930] ?>:</td>
                            <td>
                                <a href="main_admin.php?action=setup_update" target="_blank"><?= $latestVersion ?><i
                                            class="lmb-icon lmb-fav-filled text-danger" title="update available"></i></a>
                            </td>
                        </tr>
                    <?php endif; ?>
                <?php } ?>
                <tr>
                    <td><?= $lang[519] ?>:</td>
                    <td class="text-muted"><?= $session['username'] ?></td>
                </tr>
                <tr>
                    <td><?= $lang[4] ?>:</td>
                    <td class="text-muted"><?= "{$session['vorname']} {$session['name']}" ?></td>
                </tr>
                <tr>
                    <td><?= $lang[11] ?>:</td>
                    <td class="text-muted"><?= $umgvar['company'] ?></td>
                </tr>
                <tr>
                    <td><?= $lang[749] ?>:</td>
                    <td class="text-muted"><?= $session['lastlogin'] ?></td>
                </tr>
                <tr>
                    <td><?= $lang[7] ?>:</td>
                    <td class="text-muted"><?= $_SERVER['SERVER_NAME'] ?></td>
                </tr>
                <tr>
                    <td><?= $lang[8] ?>:</td>
                    <td class="text-muted"><?= $_SERVER['REMOTE_ADDR'] ?></td>
                </tr>
                <tr>
                    <td><?= $lang[9] ?>:</td>
                    <td class="text-muted"><?= $_SERVER['HTTP_USER_AGENT'] ?></td>
                </tr>

                <?php if ($session['group_id'] == 1) {
                    require_once(COREPATH . 'lib/db/db_' . $DBA['DB'] . '_admin.lib');
                    $setup_version = dbf_version($DBA);
                    #if (!$DBA['VERSION'] or $DBA['VERSION'] != $setup_version[1]) {
                    #    $versionconlict = "<br><i style=\"color:red\"><b>Version Conflict! \$DBA['VERSION'] in include_db.lib has to be updated!</b></i>";
                    #}
                    unset($setup_version[1]);
                    ?>
                    <tr>
                        <td colspan="2">
                            <HR>
                        </td>
                    </tr>
                    <tr>
                        <td>Database Vendor:</td>
                        <td class="text-muted"><?= $DBA["DB"] . ' - ' . implode(' - ', $setup_version) . ' ' . $versionconlict ?></td>
                    </tr>
                    <tr>
                        <td>Database User:</td>
                        <td class="text-muted"><?= $DBA["DBUSER"] ?></td>
                    </tr>
                    <tr>
                        <td>Database Name:</td>
                        <td class="text-muted"><?= $DBA["DBNAME"] ?></td>
                    </tr>
                    <tr>
                        <td>Database Host:</td>
                        <td class="text-muted"><?= $DBA["DBHOST"] ?></td>
                    </tr>
                    <tr>
                        <td><?= $lang[10] ?>:</td>
                        <td class="text-muted"><?= isset($AUTH_TYPE) ? $AUTH_TYPE : "limbas-db" ?></td>
                    </tr>
                <?php } ?>

            </table>


            <?php
        } elseif ($view == "credits") {
            ?>

            <table class="table table-sm table-borderless fs-6">
                <TR>
                    <th colspan="2">credits</th>
                </tr>
                <TR>
                    <TD>Bootstrap</TD>
                    <TD><A href="https://getbootstrap.com/">https://getbootstrap.com/</A></TD>
                </TR>
                <TR>
                    <TD>Chart.js</TD>
                    <TD><A href="https://www.chartjs.org/">https://www.chartjs.org/</A></TD>
                </TR>
                <TR>
                    <TD>CodeMirror</TD>
                    <TD><A href="https://codemirror.net/">https://codemirror.net/</A></TD>
                </TR>
                <TR>
                    <TD>DataTables</TD>
                    <TD><A href="https://datatables.net/">https://datatables.net/</A></TD>
                </TR>
                <TR>
                    <TD>ExifTool</TD>
                    <TD><A href="https://exiftool.org/">https://exiftool.org/</A>
                    </TD>
                </TR>
                <TR>
                    <TD>Font Awesome</TD>
                    <TD><A href="http://fontawesome.io/">http://fontawesome.io/</A></TD>
                </TR>
                <TR>
                    <TD>FullCalendar</TD>
                    <TD><A href="https://fullcalendar.io/">https://fullcalendar.io/</A></TD>
                </TR>
                <TR>
                    <TD>jQuery</TD>
                    <TD><A href="https://jquery.com/">https://jquery.com/</A></TD>
                </TR>
                <TR>
                    <TD>mPDF</TD>
                    <TD><A href="https://mpdf.github.io/">https://mpdf.github.io/</A></TD>
                </TR>
                <TR>
                    <TD>SabreDAV</TD>
                    <TD><A href="https://sabre.io/">https://sabre.io/</A>
                    </TD>
                </TR>
                <TR>
                    <TD>TCPDF</TD>
                    <TD><A href="https://tcpdf.org/">https://tcpdf.org/</A></TD>
                </TR>
                <TR>
                    <TD>TinyMCE</TD>
                    <TD><A href="https://www.tiny.cloud/">https://www.tiny.cloud/</A></TD>
                </TR>
            </table>

            <?php
        } elseif ($view == "notes") {
            ?>

            <p class="fw-bold mb-3">Release notes <?= $umgvar["version"] ?> - main features</p>
            <p>Full PHP 8 Support</p>
            <p>Fully revised layout and style based on Bootstrap 5</p>
            <p>New report and form creation based on html template parts</p>
            <p>First version of customizable dashboard</p>
            <p>New menu editor for code-free creation of own menus</p>
            <p>Advanced data synchronization between two Limbas systems</p>
            <p>Full structure and configuration transfer between two Limbas systems</p>
            <p>Dependencies are now managed by composer / npm</p>
            <p>New authentication allowing easier implementation of custom authentication providers</p>
            <p>Refactored folder structure to better separate source and local files</p>
            <p>Extended multitenancy capability</p>
            <p>New field LMB_STATUS replacing field DEL and allowing trashed and archived state</p>
            <p>New field types e.g. exncryptable fields</p>
            <p>Bug fixes</p>


            <?php
        } elseif ($view == 'environment_check') { ?>
            <div class="overflow-hidden">
                <?php {                    
                    define('LIMBAS_INSTALL', true);
                    define('LANG', 'en');

                    require_once(COREPATH . 'admin/install/install.lib');
                    
                    $installer = new Installer();
                    $phpIniMessages = $installer->checkPhpIni();
                    $writePermissionMessages = $installer->checkWritePermissions();
                    $dependencyMessages = $installer->checkDependencies();
                    
                    require(COREPATH . 'admin/install/html/dependencies.php');
                } ?>
            </div>
            <?php
        }
        ?>


    </div>
    <div class="card-footer">
        LIMBAS. Copyright &copy; 1998-<?= date('Y') ?> LIMBAS GmbH (info@limbas.com). LIMBAS is free
        software; You can redistribute it and/or modify it under the terms of the GPL General Public License
        V2 as published by the Free Software Foundation; Go to <a href="http://www.limbas.org/" title="LIMBAS Website" target="new">http://www.limbas.org/</a>
        for details. LIMBAS comes with ABSOLUTELY NO WARRANTY; Please note that some external scripts are
        copyright of their respective owners, and are released under different licences.
    </div>
</div>
