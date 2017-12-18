<?php
/* --- php-ini --- */
# php-ini - func_ini_tags
if(ini_get('short_open_tag')){$msg[func_ini_tags] .= $msgOK;$msic[func_ini_tags] = "1";}else{$msg[func_ini_tags] .= "<font color=\"orange\">Off</font> .. it will be better to set <b>On</b>";$msic[func_ini_tags] = "2";}
# php-ini - func_ini_globals
if(ini_get('register_globals')){$msg[func_ini_globals] .= "<font color=\"orange\">On</font> .. it will be better to set <b>Off</b>";$msic[func_ini_globals] = "2";}else{$msg[func_ini_globals] .= "<font color=\"green\">Off";$msic[func_ini_globals] = "1";}
# php-ini - func_ini_globals
if(get_magic_quotes_gpc()){$msg[func_ini_magic_quotes] .= "<font color=\"orange\">On</font>  .. it will be better to set <b>Off</b>";$msic[func_ini_magic_quotes] = "2";}else{$msg[func_ini_magic_quotes] .= "<font color=\"green\">Off";$msic[func_ini_magic_quotes] = "1";}
# php-ini - file_uploads
if(ini_get('file_uploads')){$msg[func_ini_uploads] .= $msgOK;$msic[func_ini_uploads] = "1";}else{$msg[func_ini_uploads] .= "<font color=\"orange\"><b>FALSE</b> .. for file uploads you need to set ON";$msic[func_ini_uploads] = "2";}
# php-ini - func_ini_derr
if(ini_get('display_errors')){$msg[func_ini_derr] .= "yes";}else{$msg[func_ini_derr] .= "<b>no</b>";}
# php-ini - func_ini_lerr
if(ini_get('log_errors')){$msg[func_ini_lerr] .= "yes (" . (ini_get('error_log') ? ini_get('error_log') : 'default') . ")";}else{$msg[func_ini_lerr] .= "<b>no</b>";}
# php-ini - func_ini_upload
if(ini_get('upload_max_filesize')){$msg[func_ini_upload] .= ini_get('upload_max_filesize');$msic[func_ini_upload] = "1";}else{$msg[func_ini_upload] .= $msgWarn;$msic[func_ini_upload] = "2";}
# php-ini - func_ini_post
if(ini_get('post_max_size')){$msg[func_ini_post] .= ini_get('post_max_size');$msic[func_ini_post] = "1";}else{$msg[func_ini_post] .= $msgWarn;$msic[func_ini_post] = "2";}
# php-ini - max_input_vars
if(ini_get('max_input_vars') >= 10000){$msg[func_input_vars] .= ini_get('max_input_vars');$msic[func_input_vars] = "1";}elseif(ini_get('max_input_vars') ){$msg[func_input_vars] .= "<font color=\"orange\"><b>".ini_get('max_input_vars')."</b> (>= 10000)";$msic[func_input_vars] = "2";}
# php-ini - defaultlrl
if(ini_get('odbc.defaultlrl')){$msg[func_ini_tlrl] .= ini_get('odbc.defaultlrl')." bytes";$msic[func_ini_tlrl] = "1";}else{$msg[func_ini_tlrl] .= $msgWarn;$msic[func_ini_tlrl] = "2";}
# php-ini - mbstring
if(function_exists("mb_strlen")){
    if(ini_get('mbstring.func_overload') == 7){$msg[func_ini_mbstring] .= $msgOK;$msic[func_ini_mbstring] = "1";}else{$msg[func_ini_mbstring] .= "<font color=\"orange\"><b>Warning</b> .. mbstring present, but not enabled (need only for utf-8 instances)";$msic[func_ini_mbstring] = "2";$commit = 2;}
}else{
    $msg[func_ini_mbstring] .= "<font color=\"orange\"><b>Warning</b> .. mbstring not present (need only for utf-8 instances)";$msic[func_ini_mbstring] = "2";$commit = 2;                            
}
# php-ini - timezone
if(ini_get('date.timezone')){$msg[func_ini_timezone] .= ini_get('date.timezone');$msic[func_ini_timezone] = "1";}else{$msg[func_ini_timezone] .= "<font color=\"red\"><b>Must be set! (e.g. Europe/Berlin)</b>";$msic[func_ini_timezone] = "3";$commit=1;}

#if(function_exists("mb_strlen()"))
#    mbstring.encoding_translation


/* --- write permissions --- */
/* --- include_db ---------------------------------main_admin.php--------------------- */
if(file_exists($setup_path_project."/inc/include_db.lib")){
    if(is_writable($setup_path_project."/inc/include_db.lib")){
        $msg['func_incdb'] = "<font color=\"green\">OK</font> .. you should set readonly after installation!";
        $msic[func_incdb] = "1";
    } else {
        $msg['func_incdb'] = $msgError;
        $msic[func_incdb] = "3";
        $commit = 1;
    }
} else {
    $f = fopen($setup_path_project."/inc/include_db.lib", "wt");
    if (is_resource($f)){
        fclose($f);
        $msg['func_incdb'] = "<font color=\"orange\">file does not exist .. try to create OK";
        $msic[func_incdb] = "1";
    } else {
        $msg['func_incdb'] = "<font color=\"red\">file does not exist .. try to create FAILED";
        $msic[func_incdb] = "3";
        $commit = 1;
    }
}

if(is_writable($setup_path_project."/BACKUP")){$msg['func_wr_backup'] = $msgOK;$msic[func_wr_backup] = "1";}else{$msg['func_wr_backup'] = $msgError . " (apache needs recursive write permissions)";$msic[func_wr_backup] = "3";$commit = 1;}
if(is_writable($setup_path_project."/TEMP")){$msg['func_wr_temp'] = $msgOK;$msic[func_wr_temp] = "1";}else{$msg['func_wr_temp'] = $msgError . " (apache needs recursive write permissions)";$msic[func_wr_temp] = "3";$commit = 1;}
if(is_writable($setup_path_project."/UPLOAD")){$msg['func_wr_upload'] = $msgOK;$msic[func_wr_upload] = "1";}else{$msg['func_wr_upload'] = $msgError . " (apache needs recursive write permissions)";$msic[func_wr_upload] = "3";$commit = 1;}
if(is_writable($setup_path_project."/USER")){$msg['func_wr_user'] = $msgOK;$msic[func_wr_user] = "1";}else{$msg['func_wr_user'] = $msgError . " (apache needs recursive write permissions)";$msic[func_wr_user] = "3";$commit = 1;}


?>

<table class="table table-condensed">
    <thead>        
        <tr>
            <th colspan="3">
                php.ini
                <a href="http://www.limbas.org/wiki/-OpenSuse#PHP_Konfiguration" target="new"><i class="lmb-icon lmb-help"></i></a>
            </th>
        </tr>
    </thead>
    <tbody>
        <tr><?= insIcon($msic[func_ini_tags]); ?><td>short_open_tag</td><td><?php echo $msg[func_ini_tags];?></td></tr>
        <tr><?= insIcon($msic[func_ini_globals]); ?><td>register_globals</td><td valign="top"><?php echo $msg[func_ini_globals]; ?></td></tr>
        <tr><?= insIcon($msic[func_ini_magic_quotes]); ?><td>magic_quotes</td><td valign="top"><?php echo $msg[func_ini_magic_quotes]; ?></td></tr>
        <tr><?= insIcon($msic[func_ini_mbstring]); ?><td>mb_string</td><td valign="top"><?php echo $msg[func_ini_mbstring]; ?></td></tr>
        <tr><?= insIcon($msic[func_ini_uploads]); ?><td>file_uploads</td><td valign="top"><?php echo $msg[func_ini_uploads]; ?></td></tr>
        <tr><?= insIcon($msic[func_ini_upload]); ?><td>upload_max_filesize</td><td valign="top"><?php echo $msg[func_ini_upload]; ?></td></tr>
        <tr><?= insIcon($msic[func_ini_post]); ?><td>post_max_size</td><td valign="top"><?php echo $msg[func_ini_post]; ?></td></tr>
        <tr><?= insIcon($msic[func_input_vars]); ?><td>max_input_vars</td><td valign="top"><?php echo $msg[func_input_vars]; ?></td></tr>
        <tr><?= insIcon($msic[func_ini_tlrl]); ?><td>odbc.defaultlrl</td><td valign="top"><?php echo $msg[func_ini_tlrl]; ?></td></tr>
        <tr><?= insIcon($msic[func_ini_timezone]); ?><td>date.timezone</td><td valign="top"><?php echo $msg[func_ini_timezone]; ?></td></tr>
        <tr><?= insIcon(); ?><td>display_errors</td><td valign="top"><?php echo $msg[func_ini_derr]; ?></td></tr>
        <tr><?= insIcon(); ?><td>log_errors</td><td valign="top"><?php echo $msg[func_ini_lerr]; ?></td></tr>
    </tbody>
</table>

<table class="table table-condensed">
    <thead>
        <tr>
            <th colspan="3">
                write permissions (recursive)
                <a href="http://www.limbas.org/wiki/LIMBAS" target="new"><i class="lmb-icon lmb-help"></i></a>
            </th>
        </tr>
    </thead>
    <tbody>
        <tr><?= insIcon($msic[func_incdb]); ?><td>inc/include_db</td><td><?php echo $msg[func_incdb]; ?></td></tr>
        <tr><?= insIcon($msic[func_wr_backup]); ?><td>BACKUP</td><td><?php echo $msg[func_wr_backup]; ?></td></tr>
        <tr><?= insIcon($msic[func_wr_temp]); ?><td>TEMP</td><td><?php echo $msg[func_wr_temp]; ?></td></tr>
        <tr><?= insIcon($msic[func_wr_upload]); ?><td>UPLOAD</td><td><?php echo $msg[func_wr_upload]; ?></td></tr>
        <tr><?= insIcon($msic[func_wr_user]); ?><td>USER</td><td><?php echo $msg[func_wr_user]; ?></td></tr>       
    </tbody>
</table>

<div>
    <button type="button" class="btn btn-default" onclick="switchToStep('<?php $s=array_keys($steps); echo $s[0]?>')">Back</button>
    <?php 
    $nextStep = 2;
    $text = "Next step";
    if($commit == 1) {
        $tooltip = "title=\"All required functions must work before continuing!\"";
        $disabled = "disabled";
        $nextStep--;
        $text = "Reload";
    }
    ?>
    <button type="submit" class="btn btn-info pull-right <?= $disabled ?>" <?= $tooltip ?> name="install" value="<?php $s=array_keys($steps); echo $s[$nextStep]?>"><?= $text ?></button>
</div>