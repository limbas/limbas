<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */


if(!defined('LIMBAS_INSTALL')) {
    define('LIMBAS_INSTALL', true);
}

require_once __DIR__ . '/Msg.php';
require_once __DIR__ . '/install.lib';
require_once __DIR__ . '/../../bootstrap.php';
require_once(COREPATH . 'extra/lmbObject/log/LimbasLogger.php');

$install = '';
if (isset($_POST['install'])) {
    $install = $_POST['install'];
}


# get path
$pt = isset($_SERVER['PATH_TRANSLATED']) ? $_SERVER['PATH_TRANSLATED'] : $_SERVER['SCRIPT_FILENAME'];
$path = explode("/", $pt);
array_pop($path);
array_pop($path);
array_pop($path);
$path = implode("/",$path);


$umgvar["pfad"] = $path;
global $setup;
global $umgvar;

$dockerFlag = DEPENDENTPATH . 'inc/docker';
$configFile = DEPENDENTPATH . 'inc/include_db.lib';
$deleteConfigError = false;
if ($install === 'restart') {
    $deleteConfigError = !unlink($configFile);
    $install = '';
}

/* --- revision check --------------------------------- */
if(file_exists(COREPATH . 'lib/version.inf')){
    $fullVersion = file_get_contents(COREPATH . 'lib/version.inf');
    $vers = trim($fullVersion);
    $revision = substr($fullVersion,0,strrpos($fullVersion,'.'));
}


$skipDependencies = false;
$configExists = file_exists($configFile);

// if already installed prevent access and force administrator to delete config
$alreadyInstalled = false;
$skipDatabase = false;

if ($configExists) {
    ob_start();
    $dbTest = testDatabase($configFile);
    ob_end_clean();
    if ($dbTest === 2) {
        // db connection was successful and limbas tables were found => fully installed
        $alreadyInstalled = true;
    } elseif ($dbTest === 1) {
        // db connection was successful but no limbas tables were found => not installed but DB connection is good
        $skipDatabase = true;
    }
}

if (!$skipDatabase) {
    if (file_exists($dockerFlag)) {
        $skipDependencies = true;
    }
}

if(!isset($_GET['lang'])) {
    $available = ['en','de','fr'];
    $_GET['lang'] = 'en';
    if ( isset( $_SERVER[ 'HTTP_ACCEPT_LANGUAGE' ] ) ) {
        $languages = explode( ',', $_SERVER['HTTP_ACCEPT_LANGUAGE'] );
        foreach ( $languages as $lang ){
            $lang = substr( $lang, 0, 2 );
            if( in_array( $lang, $available ) ) {
                $_GET['lang'] = $lang;
                break;
            }
        }
    }
}

switch ($_GET['lang']) {
    case 'de':
        require_once __DIR__ . '/lang/de.php';
        define('LANG', 'de');
        break;
    case 'fr':
        require_once __DIR__ . '/lang/fr.php';
        define('LANG', 'fr');
        break;
    default:
        define('LANG', 'en');
}


# all steps of installer
$steps = array(
    '' => lang('Welcome'),
    'test_php_ini' => 'PHP.ini',
    'test_dependencies' => lang('Dependencies'),
    'database_config' => lang('Database configuration'),
    'settings' => lang('Settings'),
    'package_settings' => lang('Example values'),
    'summary' => lang('Summary'),
    'install' => lang('Installation')
);

// if a configuration file exists, there is no need to redo these steps
if ($skipDatabase) {
    $skipDependencies = true;
    unset($steps['database_config']);
}

if($skipDependencies) {
    unset($steps['test_php_ini']);
    unset($steps['test_dependencies']);
}

$stepKeys = array_keys($steps);
$currentStep = array_search($install,$stepKeys);
if (!$currentStep) {
    $currentStep = 0;
}

require_once(COREPATH . 'lib/include_mbstring.lib');
$GLOBALS['umgvar']['charset'] = 'UTF-8';
ini_set('default_charset', 'utf-8');


header('Content-Encoding:none');
?>
<!DOCTYPE html>
<html lang="<?=LANG?>">
    <head>
        <title><?= $_SERVER['SERVER_NAME'] ?> : LIMBAS Installation</title>
        <link rel="stylesheet" href="../assets/css/default.css?v=<?=$revision?>">
        <script type="text/javascript" src="../assets/vendor/jquery/jquery.min.js?v=<?=$revision?>"></script>
        
        <?php if(!$alreadyInstalled): ?>
        <script type="text/javascript">

            $(function() {
                $('#lang-select').change(function () {
                    let lang = $(this).val();
                    window.location.href = window.location.protocol + '//' + window.location.host + window.location.pathname + '?lang=' + lang;
                    return false;
                });
            });
            
            function switchToStep(step) {
                var buttons = document.getElementsByName('install');
                var nextStepButton;
                for(var i = 0; i < buttons.length; i++) {
                    if(buttons[i].innerHTML === 'Next step' || buttons[i].innerHTML === 'Check') {
                        nextStepButton = buttons[i];
                        break;
                    }
                }
                                
                // if no button was found, create one and add to form1
                if(nextStepButton == null) {
                    nextStepButton = document.createElement("button");
                    nextStepButton.name = 'install';
                    document.form1.appendChild(nextStepButton);
                }
                
                // set value to step to got to and submit
                nextStepButton.value = step;
                nextStepButton.click();
            }
            
            function showprogress(id, value){
                let $progressBar = $('#'+id);

                //if (value==100){
                //    $progressBar.parent().hide();
                //    return;
                //}

                $progressBar.width(Math.round(value)+'%');
                $progressBar.text(Math.round(value)+'%');
            }

            function scrolldown(){
                $('.scrollcontainer').scrollTop(999999);
            }
            
            
        </script>
        <?php endif; ?>
    </head>
    
    <body class="main-install">

    <?php if($alreadyInstalled): ?>
        <div class="container container-xl pt-5">
            <div class="card border-danger">
                <div class="card-body">
                    <h5 class="card-title"><?=lang('Limbas is already installed.')?></h5>
                    <p class="mb-0"><?=lang('For security reasons, the installation can only be restarted by deleting the configuration file.')?></p>
                </div>
            </div>
        </div>
    <?php else: ?>

        <form action=".?lang=<?=LANG?>" method="POST" name="form1">
            <div class="container container-xl">

                <div class="row copyright-header">
                    <div class="col-md-3 text-center">
                        <img src="../assets/images/logo_topleft.png" alt="LIMBAS Business Solutions">
                    </div>
                    <?php if ($install) { ?>
                    <div class="col-md-9 text-end">
                        <img src="../assets/images/limbas-logo-text.png">
                    </div>
                    <?php } ?>
                </div>

                <div class="row mb-3" style="height: calc(100% - 165px - 1em)">
                    <div class="col-md-3">
                        <div class="list-group">
                            <?php
                            $afterCurrent = false;
                            foreach($steps as $key => $name) {
                                if($install == $key) {
                                    // current step
                                    echo '<button type="button" class="list-group-item active text-start" onclick="switchToStep(\'' . $key . '\');">' . $name . '</button>';
                                    $afterCurrent = true;
                                } else if(!$afterCurrent) {
                                    // previous steps
                                    echo '<button type="button" class="list-group-item previous-step text-start" onclick="switchToStep(\'' . $key . '\');">' . $name . '</button>';
                                } else {
                                    // upcoming steps
                                    echo '<div class="list-group-item">' . $name . '</div>';
                                }
                            }
                            ?>                    

                        </div>
                    </div>

                    <div class="col-md-9 scrollcontainer well">
                        <?php
                        if(!$install) {
                            require_once(__DIR__ . '/steps/0_welcome.php');

                        } else if($install == 'test_php_ini') {
                            require_once(__DIR__ . '/steps/1_php_ini.php');         

                        } else if($install == 'test_dependencies') {
                            require_once(__DIR__ . '/steps/2_dependencies.php');

                        } else if($install == 'database_config') {
                            require_once(__DIR__ . '/steps/3_database_config.php');

                        } else if($install == 'settings') {
                            require_once(__DIR__ . '/steps/4_settings.php');

                        } else if($install == 'package_settings') {
                            require_once(__DIR__ . '/steps/5_package_settings.php');

                        } else if($install == 'summary') {
                            require_once(__DIR__ . '/steps/6_summary.php');
                            
                        } else if($install == 'install') {
                            require_once(__DIR__ . '/steps/7_installation.php');
                        }
                        ?>


                    </div>

                </div>            

            </div>

            <?php if ($install != 'database_config') {
                # those are set in database_config and must be unique
                ?>

                <input type="hidden" name="db_vendor" value="<?= $_POST['db_vendor'] ?>">
                <input type="hidden" name="db_version" value="<?= $_POST['db_version'] ?>">
                <input type="hidden" name="setup_host" value="<?= $_POST['setup_host'] ?>">
                <input type="hidden" name="setup_database" value="<?= $_POST['setup_database'] ?>">
                <input type="hidden" name="setup_dbuser" value="<?= $_POST['setup_dbuser'] ?>">
                <input type="hidden" name="setup_dbpass" value="<?= $_POST['setup_dbpass'] ?>">
                <input type="hidden" name="setup_dbschema" value="<?= $_POST['setup_dbschema'] ?>">
                <input type="hidden" name="setup_dbport" value="<?= $_POST['setup_dbport'] ?>">
                <input type="hidden" name="setup_dbdriver" value="<?= $_POST['setup_dbdriver'] ?>">
                <input type="hidden" name="radio_odbc" value="<?= $_POST['radio_odbc']; ?>">

            <?php }
            if($install != 'settings') {
                # those are set in settings and must be unique
                ?>

                <input type="hidden" name="setup_language" value="<?= $_POST['setup_language'] ?>">
                <input type="hidden" name="setup_dateformat" value="<?= $_POST['setup_dateformat'] ?>">
                <input type="hidden" name="setup_charset" value="<?= $_POST['setup_charset'] ?>">
                <input type="hidden" name="setup_company" value="<?= $_POST['setup_company'] ?>">
                <input type="hidden" name="setup_color_scheme" value="<?= $_POST['setup_color_scheme'] ?>">

                <?php
            }
            if($install != 'package_settings') {
                ?>
                <input type="hidden" name="backupdir" value="<?= $_POST['backupdir'] ?>">
                <?php
            }
            ?>

            
            <input type="hidden" name="action" value="1">

        </form>
    
    <?php endif; ?>
    </body>
</html>
