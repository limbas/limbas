<?php
/*
 * Copyright notice
 * (c) 1998-2016 Limbas GmbH - Axel westhagen (support@limbas.org)
 * All rights reserved
 * This script is part of the LIMBAS project. The LIMBAS project is free software; you can redistribute it and/or modify it on 2 Ways:
 * Under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * Or
 * In a Propritary Software Licence http://limbas.org
 * The GNU General Public License can be found at http://www.gnu.org/copyleft/gpl.html.
 * A copy is found in the textfile GPL.txt and important notices to the license from the author is found in LICENSE.txt distributed with these scripts.
 * This script is distributed WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * This copyright notice MUST APPEAR in all copies of the script!
 * Version 3.0
 */

/*
 * ID: 35
 */

extract($_SERVER, EXTR_SKIP);
extract($_POST, EXTR_SKIP);

# get path
$pt = isset($_SERVER['PATH_TRANSLATED']) ? $_SERVER['PATH_TRANSLATED'] : $_SERVER['SCRIPT_FILENAME'];
$path = explode("/", $pt);
array_pop($path);
array_pop($path);
array_pop($path);
$path = implode("/",$path);
$setup_path_project = $path;

# tooltips
$tooltips = array(
    1 => "OK",
    2 => "OK, but will be better to change",
    3 => "Necessary. You can not continue until this function works!",
    4 => "Function or tool does not work or exist, you can install later"
);

# messages
$msgOK = '<font color="green">' . $tooltips[1] . '</font>';
$msgWarn = '<font color="orange">' . $tooltips[2] . '</font>';
$msgWarnHeavy = '<font color="orange">' . $tooltips[4] . '</font>';
$msgError = '<font color="red">' . $tooltips[3] . '</font>';
  
# function to insert icons
function insIcon($code=null) {
    global $tooltips;    
    
    if($code) {        
        $tooltip = $tooltips[$code];  
        
        return "<td title=\"$tooltip\" style=\"width: 20px;\"><i class=\"lmb-icon lmb-status-$code\"></i></td>";
    } else {
        return "<td style=\"width: 20px;\"></td>";
    }
}

# all steps of installer
$steps = array(
    '' => 'Welcome',
    'test_php_ini' => 'PHP.ini',
    'test_dependencies' => 'Dependencies',
    'database_config' => 'Database configuration',
    'settings' => 'Settings',
    'package_settings' => 'Example values',
    'summary' => 'Summary',
    'install' => 'Installation'
);

?>

<html>
    <head>
        <title><?= $_SERVER['SERVER_NAME'] ?> : LIMBAS Installation</title>
        <link rel="stylesheet" href="../../extern/bootstrap/bootstrap.min.css">
        <link rel="stylesheet" href="../../layout/comet/icons.css">
        <script type="text/javascript" src="../../extern/jquery/jquery-1.11.0.min.js"></script>
        <style type="text/css">
            .borderless td, .borderless th {
                border: none !important; 
            }

            .copyright-header img {
	            margin-top: 40px; 
	            margin-bottom: 15px;
            	height: 90px;
            }
            
            .scrollcontainer {
                max-height: calc(100% - 5px);
                overflow-y: auto;
            }     
            
            .previous-step {
                color: #777;
                background-color: #eee;
            }
                                    
            .lmb-icon.lmb-status-1::before {
                content: "\f00c"; /* fa-check */
                color: green;
            }
            
            .lmb-icon.lmb-status-2::before {
                content: "\f00c"; /* fa-check */
                color: orange;
            }
            
            .lmb-icon.lmb-status-3::before {
                content: "\f00d"; /* fa-times */
                color: red;
            }
            
            .lmb-icon.lmb-status-4::before {
                content: "\f00d"; /* fa-times */
                color: orange;
            }            
        </style>        
        <script type="text/javascript">
            function switchToStep(step) {
                var buttons = document.getElementsByName('install');
                var nextStepButton;
                for(var i = 0; i < buttons.length; i++) {
                    if(buttons[i].innerHTML == 'Next step' || buttons[i].innerHTML == 'Check') {
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
                if (value==100){
                    $('#' + id).hide();
                    $('#' + id + '_container').hide();
                    return;
                }

                document.getElementById(id).style.width = Math.round(value)+"%";
                document.getElementById(id).innerHTML = Math.round(value)+"%";
            }

            function scrolldown(){
                $('.scrollcontainer').scrollTop(999999);
            }

            
        </script>
        
    </head>
    
    <body>

        <form action="." method="POST" name="form1">
            <div class="container">

                <div class="row copyright-header">
                    <div class="col-md-3 text-center">
                        <img src="../../pic/Limbas-Mandarine-trans.png" alt="LIMBAS Business Solutions">
                    </div>                
                </div>

                <div class="row" style="height: calc(100% - 165px - 1em)">
                    <div class="col-md-3">
                        <div class="list-group">
                            <?php      
                            $afterCurrent = false;
                            foreach($steps as $key => $name) {
                                if($install == $key) {
                                    // current step
                                    echo '<button type="button" class="list-group-item active" onclick="switchToStep(\'' . $key . '\');">' . $name . '</button>';
                                    $afterCurrent = true;
                                } else if(!$afterCurrent) {
                                    // previous steps
                                    echo '<button type="button" class="list-group-item previous-step" onclick="switchToStep(\'' . $key . '\');">' . $name . '</button>';
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
                            require_once('steps/0_welcome.php');

                        } else if($install == 'test_php_ini') {
                            require_once('steps/1_php_ini.php');         

                        } else if($install == 'test_dependencies') {
                            require_once('steps/2_dependencies.php');

                        } else if($install == 'database_config') {
                            require_once('steps/3_database_config.php');

                        } else if($install == 'settings') {
                            require_once('steps/4_settings.php');

                        } else if($install == 'package_settings') {
                            require_once('steps/5_package_settings.php');

                        } else if($install == 'summary') {
                            require_once('steps/6_summary.php');
                            
                        } else if($install == 'install') {
                            require_once('steps/7_installation.php');
                        }
                        ?>


                    </div>

                </div>            

            </div>

            <?php if ($install != 'database_config') {
                # those are set in database_config and must be unique
                ?>

                <input type="hidden" name="DBA[DB]" value="<?php echo $DBA["DB"]; ?>">
                <input type="hidden" name="setup_host" value="<?php echo $setup_host; ?>">
                <input type="hidden" name="setup_database" value="<?php echo $setup_database; ?>">
                <input type="hidden" name="setup_dbuser" value="<?php echo $setup_dbuser; ?>">
                <input type="hidden" name="setup_dbpass" value="<?php echo $setup_dbpass; ?>">
                <input type="hidden" name="setup_dbschema" value="<?php echo $setup_dbschema; ?>">
                <input type="hidden" name="setup_dbport" value="<?php echo $setup_dbport; ?>">
                <input type="hidden" name="setup_dbdriver" value="<?php echo $setup_dbdriver; ?>">

            <?php }
            if($install != 'settings') {
                # those are set in settings and must be unique
                ?>

                <input type="hidden" name="setup_language" value="<?php echo $setup_language; ?>">
                <input type="hidden" name="setup_dateformat" value="<?php echo $setup_dateformat; ?>">
                <input type="hidden" name="setup_charset" value="<?php echo $setup_charset; ?>">
                <input type="hidden" name="setup_company" value="<?php echo $setup_company; ?>">        

                <?php
            }
            if($install != 'package_settings') {
                ?>
                <input type="hidden" name="backupdir" value="<?php echo $backupdir; ?>">
                <?php
            }
            ?>

            <input type="hidden" name="setup_path_images" value="<?php echo $setup_path_images; ?>">
            <input type="hidden" name="setup_path_imageurl" value="<?php echo $setup_path_imageurl; ?>">
            <input type="hidden" name="setup_path_pdf" value="<?php echo $setup_path_pdf; ?>">
            <input type="hidden" name="setup_path_temp" value="<?php echo $setup_path_temp; ?>">
            <input type="hidden" name="setup_path_upload" value="<?php echo $setup_path_upload; ?>">
            <input type="hidden" name="setup_mailto" value="<?php echo $setup_mailto; ?>">
            <input type="hidden" name="setup_mailfrom" value="<?php echo $setup_mailfrom; ?>">
            <input type="hidden" name="setup_font" value="<?php echo $setup_font; ?>">
            <input type="hidden" name="setup_fontsize" value="<?php echo $setup_fontsize; ?>">
            <input type="hidden" name="setup_memolength" value="<?php echo $setup_memolength; ?>">
            <input type="hidden" name="setup_session_length" value="<?php echo $setup_session_length; ?>">
            <input type="hidden" name="setup_cookie_length" value="<?php echo $setup_cookie_length; ?>">
            <input type="hidden" name="action" value="1">

        </form>

    </body>    
</html>
