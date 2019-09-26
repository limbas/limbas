<?php
/* --- php-ini --- */



# php-version
$phpversion = phpversion();
list($major, $minor, $_) = explode('.', $phpversion);
if($major > 5 or ($major == 5 and $minor >= 5)){$msg['func_php_version'] .= $msgOK.' ('.$phpversion.')';$msic['func_php_version'] = "1";}else{$msg['func_php_version'] .= "($phpversion) <span style=\"color: red; \">PHP >= 5.5 needed!</span>";$msic['func_php_version'] = "3";$commit = 1;}
# php-ini - mbstring
if(function_exists("mb_strlen")){
    if(ini_get('mbstring.func_overload') > 0){$msg['func_ini_mbstring'] .= "<span style=\"color: red;\"><b>Error</b>...mbstring present, but func_overload > 0 (must be set to 0)</span>";$msic['func_ini_mbstring'] = "3";$commit = 1;}else{$msg['func_ini_mbstring'] .= $msgOK;$msic['func_ini_mbstring'] = "1";}
}else{
    $msg['func_ini_mbstring'] .= "<span style=\"color: red;\"><b>$msgError</b><br>mbstring extension must be enabled to use utf8</span>";$msic['func_ini_mbstring'] = "3";$commit = 1;
}
# php-ini - file_uploads
if(ini_get('file_uploads')){$msg['func_ini_uploads'] .= $msgOK;$msic['func_ini_uploads'] = "1";}else{$msg['func_ini_uploads'] .= "<span style=\"color: orange; \"><b>Off</b>...must be enabled to activate file uploads</span>";$msic['func_ini_uploads'] = "2";}
# php-ini - func_ini_upload
if(ini_get('upload_max_filesize')){$msg['func_ini_upload'] .= ini_get('upload_max_filesize');$msic['func_ini_upload'] = "1";}else{$msg['func_ini_upload'] .= $msgWarn;$msic['func_ini_upload'] = "2";}
# php-ini - func_ini_post
if(ini_get('post_max_size')){$msg['func_ini_post'] .= ini_get('post_max_size');$msic['func_ini_post'] = "1";}else{$msg['func_ini_post'] .= $msgWarn;$msic['func_ini_post'] = "2";}
# php-ini - max_input_vars
if(ini_get('max_input_vars') >= 10000){$msg['func_input_vars'] .= ini_get('max_input_vars');$msic['func_input_vars'] = "1";}elseif(ini_get('max_input_vars') ){$msg['func_input_vars'] .= "<span style=\"color: orange; \"><b>" .ini_get('max_input_vars')."</b> (>= 10000)</span>";$msic['func_input_vars'] = "2";}
# php-ini - defaultlrl
if(get_cfg_var('odbc.defaultlrl')){$msg['func_ini_tlrl'] .= get_cfg_var('odbc.defaultlrl')." bytes";$msic['func_ini_tlrl'] = "1";}else{$msg['func_ini_tlrl'] .= $msgWarn;$msic['func_ini_tlrl'] = "2";}
# php-ini - timezone
if(ini_get('date.timezone')){$msg['func_ini_timezone'] .= ini_get('date.timezone');$msic['func_ini_timezone'] = "1";}else{$msg['func_ini_timezone'] .= "<span style=\"color: red; \"><b>Must be set! (e.g. Europe/Berlin)</b></span>";$msic['func_ini_timezone'] = "3";$commit=1;}
# php-ini - func_ini_derr
if(ini_get('display_errors')){$msg['func_ini_derr'] .= "yes";$msic['func_ini_derr'] = "2";}else{$msg['func_ini_derr'] .= "no";$msic['func_ini_derr'] = "1";}
# php-ini - func_ini_lerr
if(ini_get('log_errors')){$msg['func_ini_lerr'] .= "yes (" . (ini_get('error_log') ? ini_get('error_log') : 'default') . ")";$msic['func_ini_lerr'] = "1";}else{$msg['func_ini_lerr'] .= "<b>no</b>";$msic['func_ini_lerr'] = "2";}

/* --- write permissions --- */
/* --- include_db ---------------------------------main_admin.php--------------------- */
if(file_exists($setup_path_project."/inc/include_db.lib")){
    if(is_writable($setup_path_project."/inc/include_db.lib") AND !$embed) {
        $msg['func_incdb'] = "<span style=\"color: green;\">OK</span> .. you should set readonly after installation!";
        $msic['func_incdb'] = "1";
    } elseif(is_writable($setup_path_project."/inc/include_db.lib") AND $embed){
        $msg['func_incdb'] = "<span style=\"color: orange;\">Warning</span> .. you should set readonly!";
        $msic['func_incdb'] = "1";
    } elseif(!is_writable($setup_path_project."/inc/include_db.lib") AND !$embed) {
        $msg['func_incdb'] = $msgError;
        $msic['func_incdb'] = "3";
        $commit = 1;
    } elseif(!is_writable($setup_path_project."/inc/include_db.lib") AND $embed) {
        $msg['func_incdb'] = "<span style=\"color: green;\">OK</span>";
        $msic['func_incdb'] = "1";
    }
} else {
    $f = fopen($setup_path_project."/inc/include_db.lib", "wt");
    if (is_resource($f)){
        fclose($f);
        $msg['func_incdb'] = "<span style=\"color: orange;\">file does not exist .. try to create OK</span>";
        $msic['func_incdb'] = "1";
    } else {
        $msg['func_incdb'] = "<span style=\"color: red; \">file does not exist .. try to create FAILED</span>";
        $msic['func_incdb'] = "3";
        $commit = 1;
    }
}

if(is_writable($setup_path_project."/BACKUP")){$msg['func_wr_backup'] = $msgOK;$msic['func_wr_backup'] = "1";}else{$msg['func_wr_backup'] = $msgError . " (apache needs recursive write permissions)";$msic['func_wr_backup'] = "3";$commit = 1;}
if(is_writable($setup_path_project."/TEMP")){$msg['func_wr_temp'] = $msgOK;$msic['func_wr_temp'] = "1";}else{$msg['func_wr_temp'] = $msgError . " (apache needs recursive write permissions)";$msic['func_wr_temp'] = "3";$commit = 1;}
if(is_writable($setup_path_project."/UPLOAD")){$msg['func_wr_upload'] = $msgOK;$msic['func_wr_upload'] = "1";}else{$msg['func_wr_upload'] = $msgError . " (apache needs recursive write permissions)";$msic['func_wr_upload'] = "3";$commit = 1;}
if(is_writable($setup_path_project."/USER")){$msg['func_wr_user'] = $msgOK;$msic['func_wr_user'] = "1";}else{$msg['func_wr_user'] = $msgError . " (apache needs recursive write permissions)";$msic['func_wr_user'] = "3";$commit = 1;}
$adminPath = $setup_path_project."/USER/1/temp";
if(is_writable($adminPath)){
    $fileOwner = fileowner($adminPath);
    $fileGroup = filegroup($adminPath);
    $msg['func_wr_user_temp'] = $msgOK;
    $msic['func_wr_user_temp'] = "1";
}else{
    $msg['func_wr_user_temp'] = $msgError . " (apache needs recursive write permissions)";
    $msic['func_wr_user_temp'] = "3";
    $commit = 1;
}

if ($fileOwner && $fileGroup) {
    $iter = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($setup_path_project, RecursiveDirectoryIterator::SKIP_DOTS),
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
        $msg['func_wr_dependent'] = $msgOK;
        $msic['func_wr_dependent'] = "1";
    } else {
        $msg['func_wr_dependent'] = "apache might need recursive write permissions:<ul>". implode('', array_map(function($p) { return '<li>' . $p . '</li>'; }, $failedPaths)) . '</ul>';
        $msic['func_wr_dependent'] = "2";
    }
} else {
    $msg['func_wr_dependent'] = "apache might need recursive write permissions";
    $msic['func_wr_dependent'] = "2";
}
?>

<table class="table table-condensed">
    <thead>        
        <tr>
            <th colspan="3">
                php.ini
                <a href="http://www.limbas.org/wiki/-OpenSUSE#PHP_Konfiguration" target="new"><i class="lmb-icon lmb-help"></i></a>
            </th>
        </tr>
    </thead>
    <tbody>
        <tr><?= insIcon($msic['func_php_version']); ?><td>php version</td><td><?= $msg['func_php_version'] ?></td></tr>
        <tr><?= insIcon($msic['func_ini_mbstring']); ?><td>mb_string</td><td valign="top"><?= $msg['func_ini_mbstring'] ?></td></tr>
        <tr><?= insIcon($msic['func_ini_uploads']); ?><td>file_uploads</td><td valign="top"><?= $msg['func_ini_uploads'] ?></td></tr>
        <tr><?= insIcon($msic['func_ini_upload']); ?><td>upload_max_filesize</td><td valign="top"><?= $msg['func_ini_upload'] ?></td></tr>
        <tr><?= insIcon($msic['func_ini_post']); ?><td>post_max_size</td><td valign="top"><?= $msg['func_ini_post'] ?></td></tr>
        <tr><?= insIcon($msic['func_input_vars']); ?><td>max_input_vars</td><td valign="top"><?= $msg['func_input_vars'] ?></td></tr>
        <tr><?= insIcon($msic['func_ini_tlrl']); ?><td>odbc.defaultlrl</td><td valign="top"><?= $msg['func_ini_tlrl'] ?></td></tr>
        <tr><?= insIcon($msic['func_ini_timezone']); ?><td>date.timezone</td><td valign="top"><?= $msg['func_ini_timezone'] ?></td></tr>
        <tr><?= insIcon($msic['func_ini_derr']); ?><td>display_errors</td><td valign="top"><?= $msg['func_ini_derr'] ?></td></tr>
        <tr><?= insIcon($msic['func_ini_lerr']); ?><td>log_errors</td><td valign="top"><?= $msg['func_ini_lerr'] ?></td></tr>
    </tbody>
</table>

<table class="table table-condensed">
    <thead>
        <tr>
            <th colspan="3">
                write permissions (recursive)
                <a href="http://www.limbas.org/wiki/-OpenSUSE#LIMBAS_Installation" target="new"><i class="lmb-icon lmb-help"></i></a>
            </th>
        </tr>
    </thead>
    <tbody>
        <tr><?= insIcon($msic['func_incdb']); ?><td>inc/include_db</td><td><?= $msg['func_incdb'] ?></td></tr>
        <tr><?= insIcon($msic['func_wr_backup']); ?><td>BACKUP</td><td><?= $msg['func_wr_backup'] ?></td></tr>
        <tr><?= insIcon($msic['func_wr_temp']); ?><td>TEMP</td><td><?= $msg['func_wr_temp'] ?></td></tr>
        <tr><?= insIcon($msic['func_wr_upload']); ?><td>UPLOAD</td><td><?= $msg['func_wr_upload'] ?></td></tr>
        <tr><?= insIcon($msic['func_wr_user']); ?><td>USER</td><td><?= $msg['func_wr_user'] ?></td></tr>
        <tr><?= insIcon($msic['func_wr_user_temp']); ?><td>USER/1/temp</td><td><?= $msg['func_wr_user_temp'] ?></td></tr>
        <tr><?= insIcon($msic['func_wr_dependent']); ?><td>dependent/.*</td><td><?= $msg['func_wr_dependent'] ?></td></tr>
    </tbody>
</table>

<?php
# skip buttons when embedding in info-page
if ($embed)
    return;
?>

<div>
    <button type="button" class="btn btn-default" onclick="switchToStep('<?= array_keys($steps)[0] ?>')">Back</button>
    <?php
    $nextStep = 2;
    $text = "Next step";
    if($commit == 1) {
        $tooltip = "title=\"All required functions must work before continuing!\"";
        $nextStep--;
        $text = "Reload";
    }
    ?>
    <button type="submit" class="btn btn-info pull-right <?= $tooltip ?>" name="install" value="<?= array_keys($steps)[$nextStep] ?>"><?= $text ?></button>
</div>