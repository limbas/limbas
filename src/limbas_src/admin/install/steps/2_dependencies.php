<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */

/* --- check system dependencies --- 
 *
 * just checks, no post data generation
 * 
 */

if (!defined('LIMBAS_INSTALL')) {
    return;
}

/* --- test location of main.php --- */
if (file_exists(COREPATH . 'main.php')) {
    $msg['func_path'] = Msg::ok();
    $msic['func_path'] = '1';
} else {
    $msg['func_path'] = Msg::error();
    $msic['func_path'] = '3';
    $commit = 1;
}

/* --- imagedestroy --- */
if (function_exists("imagedestroy")) {
    $msg['func_gd'] = Msg::ok();
    $msic['func_gd'] = "1";
    $gbsupport = gd_info();
    $msg['func_gd_version'] = '<span class="text-info">' . $gbsupport['GD Version'] . "</span>";
    $msg['func_gd_freetype_support'] = ($gbsupport['FreeType Support']) ? Msg::ok() : Msg::error();
    $msg['func_gd_freetype_linkage'] = $gbsupport['FreeType Linkage'];
    $msg['func_gd_gif_read'] = ($gbsupport['GIF Read Support']) ? Msg::ok() : Msg::error();
    $msg['func_gd_gif_create'] = ($gbsupport['GIF Create Support']) ? Msg::ok() : Msg::error();
    $msg['func_gd_jpg'] = ($gbsupport['JPG Support'] or $gbsupport['JPEG Support']) ? Msg::ok() : Msg::error();
    $msg['func_gd_png'] = ($gbsupport['PNG Support']) ? Msg::ok() : Msg::error();
} else {
    $msg['func_gd'] = Msg::error();
    $msic['func_gd'] = '3';
    $commit = 1;
}

/* --- imagemagick --- */
chdir(COREPATH . 'admin/install/');

$cmd = 'convert --version';
$msg['func_imv'] = explode("\n", `$cmd`);
$msg['func_imv'] = $msg['func_imv'][0];

$cmd = "convert -auto-orient -thumbnail 'x30>' -gravity center -extent x30 " .
    COREPATH . 'admin/install/testfiles/test.jpg ' .
    TEMPPATH . 'test.png';

$func_im = `$cmd 2>/dev/null`;

if (is_file(TEMPPATH . 'test.png')) {
    $msg['func_im'] = '<span class="text-info">' . $msg['func_imv'] . '</span>';
    $msic['func_im'] = '1';
} else if ($msg['func_imv']) {
    $msg['func_im'] = Msg::warn() . ' (version: ' .
        $msg['func_imv'] . ')<br>V 6.3.x or higher needed!';
    $msic['func_im'] = '4';
} else {
    $msg['func_im'] = Msg::error();
    $msic['func_im'] = '3';
    $commit = 1;
}

if (file_exists(TEMPPATH . 'test.png')) {
    unlink(TEMPPATH . 'test.png');
}

/* --- imap, gzip, zip --- */
if (function_exists('imap_open')) {
    $msg['func_imap'] = Msg::ok();
    $msic['func_imap'] = '1';
} else {
    $msg['func_imap'] = Msg::warn();
    $msic['func_imap'] = '4';
    $commit = 2;
}

/* --- intl --- */
if(function_exists('collator_create')) {
    $msg['func_intl'] = Msg::ok();
    $msic['func_intl'] = '1';
} else {
    $msg['func_intl'] = Msg::warn();
    $msic['func_intl'] = '4';
    $commit = 2;
}

/* --- utf8_decode --- */
if (function_exists('utf8_decode')) {
    $msg['func_utf8_decode'] .= Msg::ok();
    $msic['func_utf8_decode'] = '1';
} else {
    $msg['func_utf8_decode'] .= '<span class="text-danger">' . lang('Not found .. in some distros you have to install') . ' <i><b>php-xml</b></i></span>';
    $msic['func_utf8_decode'] = '3';
    $commit = 1;
}

/* --- gzip --- */
if (function_exists('gzopen')) {
    $msg['func_zlib'] = Msg::ok();
    $msic['func_zlib'] = '1';
} else {
    $msg['func_zlib'] = Msg::warn();
    $msic['func_zlib'] = '4';
    $commit = 2;
}

$out = exec('zip');
if ($out) {
    $msg['func_zip'] = Msg::ok();
    $msic['func_zip'] = '1';
} else {
    $msg['func_zip'] = Msg::warn();
    $msic['func_zip'] = '4';
    $commit = 2;
}

/* --- htmldoc --- */
$cmd = 'htmldoc --size 295x210mm --left 0mm --right 0mm --top 0mm --bottom 0mm --webpage --header ... --footer ... -f ' . TEMPPATH . 'test.pdf ' . COREPATH . 'admin/install/testfiles/test.html';
exec($cmd);
$cmd = 'htmldoc --version';
$msg['func_htmldoc'] = `$cmd`;
if (is_file(TEMPPATH . 'test.pdf')) {
    $msg['func_htmldoc'] = '<span class="text-info">version: ' . $msg['func_htmldoc'] . '</span>';
    $msic['func_htmldoc'] = '1';
} else {
    $msg['func_htmldoc'] = Msg::warn();
    $msic['func_htmldoc'] = '4';
    $commit = 2;
}
if (file_exists(TEMPPATH . 'test.pdf')) {
    unlink(TEMPPATH . 'test.pdf');
}

/* --- pdftotext --- */
$cmd = 'pdftotext ' . COREPATH . 'admin/install/testfiles/test.pdf ' . TEMPPATH . 'test.txt';
exec($cmd);
if (is_file(TEMPPATH . 'test.txt')) {
    $msg['func_pdftotext'] .= Msg::ok();
    $msic['func_pdftotext'] = '1';
} else {
    $msg['func_pdftotext'] .= Msg::warn();
    $msic['func_pdftotext'] = '4';
    $commit = 2;
}
if (file_exists(TEMPPATH . 'test.txt')) {
    unlink(TEMPPATH . 'test.txt');
}

/* --- pdftohtml --- */
$cmd = 'pdftohtml ' . COREPATH . 'admin/install/testfiles/test.pdf ' . TEMPPATH. 'test.html';
exec($cmd);
if (is_file(TEMPPATH . 'test.html')) {
    $msg['func_pdftohtml'] .= Msg::ok();
    $msic['func_pdftohtml'] = '1';
} else {
    $msg['func_pdftohtml'] .= Msg::warn();
    $msic['func_pdftohtml'] = '4';
    $commit = 2;
}
if (file_exists(TEMPPATH . 'test.html')) {
    unlink(TEMPPATH . 'test.html');
    unlink(TEMPPATH . 'tests.html');
    unlink(TEMPPATH . 'test_ind.html');
}

/* --- exiftool --- */
$cmd = 'exiftool -ver';
$exiftool = `$cmd`;
$exiftoolVer = explode('.', $exiftool);
if ($exiftool and $exiftoolVer[0] >= 9) {
    $msg['func_exiftool'] .= "<span class=\"text-info\">$exiftool</span> " . Msg::ok();
    $msic['func_exiftool'] = '1';
} else {
    $msg['func_exiftool'] .= Msg::warn() . " (V.$exiftool < 9)";
    $msic['func_exiftool'] = '4';
    $commit = 2;
}

/* --- ghostscript --- */
$cmd = 'cd ' . COREPATH . 'admin/install/testfiles/; gs -dNOPAUSE -dBATCH -sDEVICE=pdfwrite -sOutputFile=' . TEMPPATH . 'test.pdf test.pdf';
exec($cmd);
if (is_file(TEMPPATH . 'test.pdf')) {
    $msg['func_ghost'] .= Msg::ok();
    $msic['func_ghost'] = '1';
} else {
    $msg['func_ghost'] .= Msg::warn();
    $msic['func_ghost'] = '4';
    $commit = 2;
}
if (file_exists(TEMPPATH . 'test.pdf')) {
    unlink(TEMPPATH . 'test.pdf');
}

/* --- php odbc --- */
if (extension_loaded('odbc')) {
    $msg['func_php_odbc'][] = 'odbc';
}
if (extension_loaded('pdo')) {
    $msg['func_php_pdo'][] = 'pdo';
}
if (extension_loaded('pdo_pgsql')) {
    $msg['func_php_pdo'][] = 'pdo_pgsql';
}
if (extension_loaded('pdo_mysql')) {
    $msg['func_php_pdo'][] = 'pdo_mysql';
}

$msic['func_php_odbc'] = '4';
$msic['func_php_pdo'] = '4';

$vendorNames = array(
    'PostgreSQL',
    'mysql',
    'MaxDB V7.6 / V7.9',
    'MSSQL',
    'Sybase',
    'HANA',
    'oracle'
);

if (extension_loaded('odbc')) {
    $msg['func_php_odbc'] = '<span class="text-success">' . implode(' ; ', $msg['func_php_odbc']) . '</span><br><i>' . lang('You can use ODBC for database connection. Available databases are:') . '</i><br><b>' . implode(', ', $vendorNames);
    $msic['func_php_odbc'] = '1';
}

if (!extension_loaded('odbc') and extension_loaded('pdo') and (extension_loaded('pdo_pgsql') or extension_loaded('pdo_mysql'))) {
    $msg['func_php_pdo'] = '<span class="text-success">' . implode(' ; ', $msg['func_php_pdo']) . '</span><br><i>' . lang('You can use PDO for database connection.<br>PDO support is only for <b>mysql</b> or <b>PostgreSQL</b>. For other databases use ODBC') . '</i>';
    $msic['func_php_pdo'] = '1';
} elseif (extension_loaded('pdo') and (extension_loaded('pdo_pgsql') or extension_loaded('pdo_mysql'))) {
    $msg['func_php_pdo'] = '<span class="text-success">' . implode(' ; ', $msg['func_php_pdo']) . '</span><br><i>' . lang('You can use PDO for database connection.<br>PDO support is only for <b>mysql</b> or <b>PostgreSQL</b>. For other databases use ODBC') . '</i>';
    $msic['func_php_pdo'] = '1';
}


if ($msic['func_php_odbc'] == '4' and $msic['func_php_pdo'] == '4') {
    $msic['func_php_odbc'] = '3';
    $msic['func_php_pdo'] = '3';
    $msg['func_php_odbc'] = Msg::error() . '<br>' . lang('You can use ODBC or PDO for database connection. If you want to use PDO you have to deinstall ODBC module fom PHP!<br>PDO support is only for <b>mysql</b> or <b>PostgreSQL</b>. For other databases use ODBC.</i><br>available db extensions:') . ' ' . implode(' ; ', $msg['func_php_odbc']);
    $commit = 1;
}
?>

<table class="table table-sm mb-3 table-striped bg-contrast border">
    <thead>
    <tr>
        <th colspan="3">
            <?=lang('Functions')?>
            <a href="http://www.limbas.org/wiki/Tools" target="new"><i class="lmb-icon lmb-help"></i></a>
        </th>
    </tr>
    </thead>
    <tbody>
    <tr><?= Msg::icon($msic['func_php_odbc']); ?>
        <td>php_odbc</td>
        <td><?= $msg['func_php_odbc'] ?></td>
    </tr>
    <tr><?= Msg::icon($msic['func_php_pdo']); ?>
        <td>php_pdo</td>
        <td><?= $msg['func_php_pdo'] ?></td>
    </tr>
    <tr><?= Msg::icon($msic['func_path']); ?>
        <td>path</td>
        <td><?= $msg['func_path'] ?></td>
    </tr>
    <tr><?= Msg::icon($msic['func_gd']); ?>
        <td>gdlib</td>
        <td><?= $msg['func_gd'] ?></td>
    </tr>

    <?php if ($msic['func_gd'] != '3') { ?>
        <tr>
            <td></td>
            <td nowrap>GD Version</td>
            <td><?= $msg['func_gd_version'] ?></td>
        </tr>
        <tr>
            <td></td>
            <td nowrap>Freetype Support for gd</td>
            <td><?= $msg['func_gd_freetype_support'] ?></td>
        </tr>
        <tr>
            <td></td>
            <td nowrap>Freetype Linkage</td>
            <td><?= $msg['func_gd_freetype_linkage'] ?></td>
        </tr>
        <tr>
            <td></td>
            <td nowrap>Gif Read Support</td>
            <td><?= $msg['func_gd_gif_read'] ?></td>
        </tr>
        <tr>
            <td></td>
            <td nowrap>Gif Create Support</td>
            <td><?= $msg['func_gd_gif_create'] ?></td>
        </tr>
        <tr>
            <td></td>
            <td nowrap>JPG Support</td>
            <td><?= $msg['func_gd_jpg'] ?></td>
        </tr>
        <tr>
            <td></td>
            <td nowrap>PNG Support</td>
            <td><?= $msg['func_gd_png'] ?></td>
        </tr>
    <?php } ?>

    <tr><?= Msg::icon($msic['func_im']); ?>
        <td>ImageMagick</td>
        <td><?= $msg['func_im'] ?></td>
    </tr>
    <tr><?= Msg::icon($msic['func_imap']); ?>
        <td>Imap</td>
        <td><?= $msg['func_imap'] ?></td>
    </tr>
    <tr><?= Msg::icon($msic['func_intl']); ?>
        <td>Intl</td>
        <td><?= $msg['func_intl'] ?></td>
    </tr>
    <tr><?= Msg::icon($msic['func_utf8_decode']); ?>
        <td>utf8_decode</td>
        <td><?= $msg['func_utf8_decode'] ?></td>
    </tr>
    <tr><?= Msg::icon($msic['func_zip']); ?>
        <td>zip</td>
        <td><?= $msg['func_zip'] ?></td>
    </tr>
    <tr><?= Msg::icon($msic['func_zlib']); ?>
        <td>Zlib</td>
        <td><?= $msg['func_zlib'] ?></td>
    </tr>
    <tr><?= Msg::icon($msic['func_htmldoc']); ?>
        <td>htmldoc</td>
        <td><?= $msg['func_htmldoc'] ?></td>
    </tr>
    <tr><?= Msg::icon($msic['func_pdftotext']); ?>
        <td>pdftotext (Xpdf)</td>
        <td><?= $msg['func_pdftotext'] ?></td>
    </tr>
    <tr><?= Msg::icon($msic['func_pdftohtml']); ?>
        <td>pdftohtml</td>
        <td><?= $msg['func_pdftohtml'] ?></td>
    </tr>
    <tr><?= Msg::icon($msic['func_ghost']); ?>
        <td>ghostscript</td>
        <td><?= $msg['func_ghost'] ?></td>
    </tr>
    <tr><?= Msg::icon($msic['func_exiftool']); ?>
        <td>exiftool</td>
        <td><?= $msg['func_exiftool'] ?></td>
    </tr>
    </tbody>
</table>

<?php
# skip buttons when embedding in info-page
if ($embed)
    return;
?>


<div class="card">
    <div class="card-body">
        <div class="row">
            <div class="col-6">
                <button type="button" class="btn btn-outline-dark"
                        onclick="switchToStep('<?= $stepKeys[$currentStep - 1] ?>')">Back
                </button>
            </div>
            <div class="col-6 text-end">
                <?php
                $tooltip = '';
                $nextStep = 1;
                $text = lang('Next step');
                $disabled = '';
                if ($commit == 1) {
                    $tooltip = 'title="' . lang('All required functions must work before continuing!') . '"';
                    $nextStep--;
                    $text = lang('Reload');
                    $disabled = 'disabled';
                }
                ?>
                <button type="submit" class="btn btn-primary" <?= $disabled ?> <?= $tooltip ?> name="install"
                        value="<?= $stepKeys[$currentStep + $nextStep] ?>"><?= $text ?></button>
            </div>

        </div>
    </div>
</div>
