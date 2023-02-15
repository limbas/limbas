<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */

/* --- php-ini --- 
 *
 * just checks, no post data generation
 * 
 */

if (!defined('LIMBAS_INSTALL')) {
    return;
}


# php-version
$phpversion = phpversion();
list($major, $minor, $_) = explode('.', $phpversion);
if ($major > 5 or ($major == 5 and $minor >= 5)) {
    $msg['func_php_version'] .= Msg::ok() . ' (' . $phpversion . ')';
    $msic['func_php_version'] = '1';
} else {
    $msg['func_php_version'] .= "($phpversion) <span class=\"text-danger\">PHP >= 5.5 " . lang('needed') . '!</span>';
    $msic['func_php_version'] = '3';
    $commit = 1;
}
# php-ini - mbstring
if (function_exists('mb_strlen')) {
    if (ini_get('mbstring.func_overload') > 0) {
        $msg['func_ini_mbstring'] .= '<span class="text-danger"><b>Error</b>' . lang('...mbstring present, but func_overload > 0 (must be set to 0)') . '</span>';
        $msic['func_ini_mbstring'] = '3';
        $commit = 1;
    } else {
        $msg['func_ini_mbstring'] .= Msg::ok();
        $msic['func_ini_mbstring'] = '1';
    }
} else {
    $msg['func_ini_mbstring'] .= '<span class="text-danger"><b>' . Msg::error() . '</b><br>' . lang('mbstring extension must be enabled to use utf8') . '</span>';
    $msic['func_ini_mbstring'] = '3';
    $commit = 1;
}
# php-ini - file_uploads
if (ini_get('file_uploads')) {
    $msg['func_ini_uploads'] .= Msg::ok();
    $msic['func_ini_uploads'] = '1';
} else {
    $msg['func_ini_uploads'] .= '<span class="text-warning"><b>' . lang('Off') . '</b>' . lang('...must be enabled to activate file uploads') . '</span>';
    $msic['func_ini_uploads'] = '2';
}
# php-ini - func_ini_upload
if (ini_get('upload_max_filesize')) {
    $msg['func_ini_upload'] .= ini_get('upload_max_filesize');
    $msic['func_ini_upload'] = '1';
} else {
    $msg['func_ini_upload'] .= Msg::okwarn();
    $msic['func_ini_upload'] = '2';
}
# php-ini - func_ini_post
if (ini_get('post_max_size')) {
    $msg['func_ini_post'] .= ini_get('post_max_size');
    $msic['func_ini_post'] = '1';
} else {
    $msg['func_ini_post'] .= Msg::okwarn();
    $msic['func_ini_post'] = '2';
}
# php-ini - max_input_vars
if (ini_get('max_input_vars') >= 10000) {
    $msg['func_input_vars'] .= ini_get('max_input_vars');
    $msic['func_input_vars'] = '1';
} elseif (ini_get('max_input_vars')) {
    $msg['func_input_vars'] .= '<span class="text-warning"><b>' . ini_get('max_input_vars') . '</b> (>= 10000)</span>';
    $msic['func_input_vars'] = '2';
}
# php-ini - defaultlrl
if (get_cfg_var('odbc.defaultlrl')) {
    $msg['func_ini_tlrl'] .= get_cfg_var('odbc.defaultlrl') . " bytes";
    $msic['func_ini_tlrl'] = '1';
} else {
    $msg['func_ini_tlrl'] .= Msg::okwarn();
    $msic['func_ini_tlrl'] = '2';
}
# php-ini - timezone
if (ini_get('date.timezone')) {
    $msg['func_ini_timezone'] .= ini_get('date.timezone');
    $msic['func_ini_timezone'] = '1';
} else {
    $msg['func_ini_timezone'] .= '<span class="text-danger"><b>' . lang('Must be set! (e.g. Europe/Berlin)') . '</b></span>';
    $msic['func_ini_timezone'] = '3';
    $commit = 1;
}
# php-ini - func_ini_derr
if (ini_get('display_errors')) {
    $msg['func_ini_derr'] .= lang('yes');
    $msic['func_ini_derr'] = '2';
} else {
    $msg['func_ini_derr'] .= lang('no');
    $msic['func_ini_derr'] = '1';
}
# php-ini - func_ini_lerr
if (ini_get('log_errors')) {
    $msg['func_ini_lerr'] .= lang('yes') . ' (' . (ini_get('error_log') ?: 'default') . ')';
    $msic['func_ini_lerr'] = '1';
} else {
    $msg['func_ini_lerr'] .= '<b>' . lang('no') . '</b>';
    $msic['func_ini_lerr'] = '2';
}

/* --- write permissions --- */
/* --- include_db ---------------------------------main_admin.php--------------------- */
if (file_exists(DEPENDENTPATH . 'inc/include_db.lib')) {
    if (is_writable(DEPENDENTPATH . 'inc/include_db.lib') and !$embed) {
        $msg['func_incdb'] = Msg::ok() . ' ' . lang('... you should set readonly after installation!');
        $msic['func_incdb'] = '1';
    } elseif (is_writable(DEPENDENTPATH . 'inc/include_db.lib') and $embed) {
        $msg['func_incdb'] = Msg::okwarn() . ' ' . lang('... you should set readonly!');
        $msic['func_incdb'] = '1';
    } elseif (!is_writable(DEPENDENTPATH . 'inc/include_db.lib') and !$embed) {
        $msg['func_incdb'] = Msg::error();
        $msic['func_incdb'] = '3';
        $commit = 1;
    } elseif (!is_writable(DEPENDENTPATH . 'inc/include_db.lib') and $embed) {
        $msg['func_incdb'] = Msg::ok();
        $msic['func_incdb'] = '1';
    }
} else {
    $f = fopen(DEPENDENTPATH . 'inc/include_db.lib', 'wt');
    if (is_resource($f)) {
        fclose($f);
        $msg['func_incdb'] = '<span class="text-success">' . lang('file can be created') . '</span>';
        $msic['func_incdb'] = '1';
        unlink(DEPENDENTPATH . 'inc/include_db.lib');
    } else {
        $msg['func_incdb'] = '<span class="text-danger">' . lang('file does not exist .. try to create FAILED') . '</span>';
        $msic['func_incdb'] = '3';
        $commit = 1;
    }
}

if (is_writable(BACKUPPATH)) {
    $msg['func_wr_backup'] = Msg::ok();
    $msic['func_wr_backup'] = '1';
} else {
    $msg['func_wr_backup'] = Msg::error() . ' (' . lang('apache needs recursive write permissions') . ')';
    $msic['func_wr_backup'] = '4';
    $commit = 1;
}
if (is_writable(TEMPPATH)) {
    $msg['func_wr_temp'] = Msg::ok();
    $msic['func_wr_temp'] = '1';
} else {
    $msg['func_wr_temp'] = Msg::error() . ' (' . lang('apache needs recursive write permissions') . ')';
    $msic['func_wr_temp'] = '4';
    $commit = 1;
}
if (is_writable(UPLOADPATH)) {
    $msg['func_wr_upload'] = Msg::ok();
    $msic['func_wr_upload'] = '1';
} else {
    $msg['func_wr_upload'] = Msg::error() . ' (' . lang('apache needs recursive write permissions') . ')';
    $msic['func_wr_upload'] = '4';
    $commit = 1;
}
if (is_writable(USERPATH)) {
    $msg['func_wr_user'] = Msg::ok();
    $msic['func_wr_user'] = '1';
} else {
    $msg['func_wr_user'] = Msg::error() . ' (' . lang('apache needs recursive write permissions') . ')';
    $msic['func_wr_user'] = '4';
    $commit = 1;
}
$adminPath = USERPATH . '1/temp';
if(!file_exists(USERPATH . '1')){
    mkdir(USERPATH . '1');
}
if(!file_exists($adminPath)){
    mkdir($adminPath);
}
if (is_writable($adminPath)) {
    $fileOwner = fileowner($adminPath);
    $fileGroup = filegroup($adminPath);
    $msg['func_wr_user_temp'] = Msg::ok();
    $msic['func_wr_user_temp'] = '1';
} else {
    $msg['func_wr_user_temp'] = Msg::error() . ' (' . lang('apache needs recursive write permissions') . ')';
    $msic['func_wr_user_temp'] = '4';
    $commit = 1;
}

if ($fileOwner && $fileGroup) {
    $iter = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator(DEPENDENTPATH, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::SELF_FIRST,
        RecursiveIteratorIterator::CATCH_GET_CHILD
    );

    $failedPaths = array();
    foreach ($iter as $path => $dir) {
        if ($dir->isDir() && !is_link($path)) {
            if (fileowner($path) != $fileOwner || filegroup($path) != $fileGroup) {
                $failedPaths[] = $path;
            }
        }
    }

    if (!$failedPaths) {
        $msg['func_wr_dependent'] = Msg::ok();
        $msic['func_wr_dependent'] = '1';
    } else {
        $msg['func_wr_dependent'] = lang('apache might need recursive write permissions') . ':<ul>' . implode('', array_map(function ($p) {
                return '<li>' . $p . '</li>';
            }, $failedPaths)) . '</ul>';
        $msic['func_wr_dependent'] = '2';
    }
} else {
    $msg['func_wr_dependent'] = lang('apache might need recursive write permissions');
    $msic['func_wr_dependent'] = '2';
}
?>


<table class="table table-sm mb-3 table-striped bg-white border">
    <thead>
    <tr>
        <th colspan="3">
            php.ini
            <a href="http://www.limbas.org/wiki/-OpenSUSE#PHP_Konfiguration" target="new"><i
                        class="lmb-icon lmb-help"></i></a>
        </th>
    </tr>
    </thead>
    <tbody>
    <tr><?= Msg::icon($msic['func_php_version']); ?>
        <td>php version</td>
        <td><?= $msg['func_php_version'] ?></td>
    </tr>
    <tr><?= Msg::icon($msic['func_ini_mbstring']); ?>
        <td>mb_string</td>
        <td><?= $msg['func_ini_mbstring'] ?></td>
    </tr>
    <tr><?= Msg::icon($msic['func_ini_uploads']); ?>
        <td>file_uploads</td>
        <td><?= $msg['func_ini_uploads'] ?></td>
    </tr>
    <tr><?= Msg::icon($msic['func_ini_upload']); ?>
        <td>upload_max_filesize</td>
        <td><?= $msg['func_ini_upload'] ?></td>
    </tr>
    <tr><?= Msg::icon($msic['func_ini_post']); ?>
        <td>post_max_size</td>
        <td><?= $msg['func_ini_post'] ?></td>
    </tr>
    <tr><?= Msg::icon($msic['func_input_vars']); ?>
        <td>max_input_vars</td>
        <td><?= $msg['func_input_vars'] ?></td>
    </tr>
    <tr><?= Msg::icon($msic['func_ini_tlrl']); ?>
        <td>odbc.defaultlrl</td>
        <td><?= $msg['func_ini_tlrl'] ?></td>
    </tr>
    <tr><?= Msg::icon($msic['func_ini_timezone']); ?>
        <td>date.timezone</td>
        <td><?= $msg['func_ini_timezone'] ?></td>
    </tr>
    <tr><?= Msg::icon($msic['func_ini_derr']); ?>
        <td>display_errors</td>
        <td><?= $msg['func_ini_derr'] ?></td>
    </tr>
    <tr><?= Msg::icon($msic['func_ini_lerr']); ?>
        <td>log_errors</td>
        <td><?= $msg['func_ini_lerr'] ?></td>
    </tr>
    </tbody>
</table>

<table class="table table-sm table-striped bg-white border">
    <thead>
    <tr>
        <th colspan="3">
            <?=lang('write permissions (recursive)')?>
            <a href="http://www.limbas.org/wiki/-OpenSUSE#LIMBAS_Installation" target="new"><i
                        class="lmb-icon lmb-help"></i></a>
        </th>
    </tr>
    </thead>
    <tbody>
    <tr><?= Msg::icon($msic['func_incdb']); ?>
        <td>inc/include_db</td>
        <td><?= $msg['func_incdb'] ?></td>
    </tr>
    <tr><?= Msg::icon($msic['func_wr_backup']); ?>
        <td>BACKUP</td>
        <td><?= $msg['func_wr_backup'] ?></td>
    </tr>
    <tr><?= Msg::icon($msic['func_wr_temp']); ?>
        <td>TEMP</td>
        <td><?= $msg['func_wr_temp'] ?></td>
    </tr>
    <tr><?= Msg::icon($msic['func_wr_upload']); ?>
        <td>UPLOAD</td>
        <td><?= $msg['func_wr_upload'] ?></td>
    </tr>
    <tr><?= Msg::icon($msic['func_wr_user']); ?>
        <td>USER</td>
        <td><?= $msg['func_wr_user'] ?></td>
    </tr>
    <tr><?= Msg::icon($msic['func_wr_user_temp']); ?>
        <td>USER/1/temp</td>
        <td><?= $msg['func_wr_user_temp'] ?></td>
    </tr>
    <tr><?= Msg::icon($msic['func_wr_dependent']); ?>
        <td>dependent/.*</td>
        <td><?= $msg['func_wr_dependent'] ?></td>
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
                if ($commit == 1) {
                    $tooltip = 'title="' . lang('All required functions must work before continuing!') . '"';
                    $nextStep--;
                    $text = lang('Reload');
                }
                ?>
                <button type="submit" class="btn btn-primary" <?= $tooltip ?> name="install"
                        value="<?= $stepKeys[$currentStep + $nextStep] ?>"><?= $text ?></button>
            </div>

        </div>
    </div>
</div>




