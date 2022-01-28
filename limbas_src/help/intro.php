<?php
/*
 * Copyright notice
 * (c) 1998-2021 Limbas GmbH(support@limbas.org)
 * All rights reserved
 * This script is part of the LIMBAS project. The LIMBAS project is free software; you can redistribute it and/or modify it on 2 Ways:
 * Under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * Or
 * In a Propritary Software Licence http://limbas.org
 * The GNU General Public License can be found at http://www.gnu.org/copyleft/gpl.html.
 * A copy is found in the textfile GPL.txt and important notices to the license from the author is found in LICENSE.txt distributed with these scripts.
 * This script is distributed WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * This copyright notice MUST APPEAR in all copies of the script!
 * Version 4.3.36.1319
 */

/*
 * ID: 4
 */
?>


<div>
<div class="tabfringe lmbinfo">


<h2>LIMBAS-<?= $umgvar["version"];// $umgvar["version"] ?></h2>
<div class="infonav">
	<?php
	if(file_exists("EXTENSIONS/customization/logo_small.png")){
		echo "<img style=\"float:right;\" src=\"EXTENSIONS/customization/logo_small.png\">";
	}
	?>

	<a href="main.php?action=intro">Info</a>&nbsp;&nbsp;|&nbsp;&nbsp;
	<a href="main.php?action=intro&view=credits">credits</a>&nbsp;&nbsp;|&nbsp;&nbsp;
    <a href="main.php?action=intro&view=notes">release notes</a>&nbsp;&nbsp;|&nbsp;&nbsp;
    <a href="main.php?action=intro&view=ChangeLog">ChangeLog</a>&nbsp;&nbsp;|&nbsp;&nbsp;
    <a href="main.php?action=intro&view=update_check">check for updates</a>&nbsp;&nbsp;|&nbsp;&nbsp;
    <a href="main.php?action=intro&view=environment_check">environment check</a>&nbsp;&nbsp;|&nbsp;&nbsp;
	<a href="http://www.limbas.org" target="_new">help</a>&nbsp;&nbsp;|&nbsp;&nbsp;
	<a href="intro.html" target=_new>quickstart</a>
</div>

<?php
if(!$view){
?>


<table border="0" cellpadding="2" cellspacing="0" width="100%">
<TR class="tabHeader"><TD class="tabHeaderItem" colspan="2">info</td></tr>
<tr><td valign="top"><?=$lang[2]?>:</td><td style="color:#999999"><?= $umgvar['version'] ?></td></tr>
<?php if ($session['user_id'] == 1) { ?>
    <tr>
        <td valign="top"><?=$lang[2930]?>:</td>
        <td style="color:#999999">
            <?php
            $latestVersion = lmbCheckForUpdates($checkForUpdates ? true : false);
            if ($latestVersion === true) { # version already is latest
                echo $lang[2928];
            } else if ($latestVersion === false) { # error / auto-update not enabled
                ?>
                <form action="main.php">
                    <input type="hidden" name="action" value="<?= $action ?>">
                    <input type="submit" name="checkForUpdates" value="<?= $lang[2929] ?>">
                </form>
            <?php } else { # later version available ?>
                <a href="https://www.limbas.com" target="_blank"><?= $latestVersion ?><i class="lmb-icon lmb-fav-filled" style="color: #f9d421;vertical-align:middle;"></i></a>
            <?php } ?>
        </td>
    </tr>
<?php } ?>
<tr><td valign="top"><?=$lang[519]?>:</td><td style="color:#999999"><?= $session['username'] ?></td></tr>
<tr><td valign="top"><?=$lang[4]?>:</td><td style="color:#999999"><?= "{$session['vorname']} {$session['name']}" ?></td></tr>
<tr><td valign="top"><?=$lang[11]?>:</td><td style="color:#999999"><?= $umgvar['company'] ?></td></tr>
<tr><td valign="top"><?=$lang[749]?>:</td><td style="color:#999999"><?= $session['lastlogin'] ?></td></tr>
<tr><td valign="top"><?=$lang[7]?>:</td><td style="color:#999999"><?= $_SERVER['SERVER_NAME'] ?></td></tr>
<tr><td valign="top"><?=$lang[8]?>:</td><td style="color:#999999"><?= $_SERVER['REMOTE_ADDR'] ?></td></tr>
<tr><td valign="top"><?=$lang[9]?>:</td><td style="color:#999999"><?= $_SERVER['HTTP_USER_AGENT'] ?></td></tr>

<?php if($session['group_id'] == 1){
    require_once("lib/db/db_".$DBA["DB"]."_admin.lib");
    $setup_version = dbf_version($DBA);
    if(!$DBA['VERSION'] OR $DBA['VERSION'] != $setup_version[1]){
        $versionconlict = "<br><i style=\"color:red\"><b>Version Conflict! \$DBA['VERSION'] in include_db.lib has to be updated!</b></i>";
    }
    unset($setup_version[1]);
    ?>
<tr><td valign="top" colspan="2"><HR></td></tr>
<tr><td valign="top">Database Vendor:</td><td style="color:#999999"><?= $DBA["DB"] .' - '.implode(' - ',$setup_version).' '.$versionconlict?></td></tr>
<tr><td valign="top">Database User:</td><td style="color:#999999"><?= $DBA["DBUSER"] ?></td></tr>
<tr><td valign="top">Database Name:</td><td style="color:#999999"><?= $DBA["DBNAME"] ?></td></tr>
<tr><td valign="top">Database Host:</td><td style="color:#999999"><?= $DBA["DBHOST"] ?></td></tr>
<tr><td valign="top"><?= $lang[10] ?>:</td><td style="color:#999999"><?= isset($AUTH_TYPE) ? $AUTH_TYPE : "limbas-db" ?></td></tr>
<?php }?>

</table>


<?php
}elseif($view == "credits"){
?>

<TABLE CELLPADDING="1" CELLSPACING="3" WIDTH="100%">

<TR class="tabHeader"><TD class="tabHeaderItem" colspan="2">credits</td></tr>

<TR><TD>jquery</TD><TD><A href="http://jquery.com">http://jquery.com</A></TD></TR>
<TR><TD>fpdf</TD><TD><A href="http://www.fpdf.org">http://www.fpdf.org</A></TD></TR>
<TR><TD>fpdi</TD><TD><A href="http://fpdi.setasign.de/">http://fpdi.setasign.de/</A></TD></TR>
<TR><TD>tcpdf</TD><TD><A href="https://tcpdf.org/">https://tcpdf.org/</A></TD></TR>
<TR><TD>fullcalendar</TD><TD><A href="http://arshaw.com/fullcalendar/">http://arshaw.com/fullcalendar/</A></TD></TR>
<TR><TD>SabreDAV</TD><TD><A href="http://code.google.com/p/sabredav/">http://code.google.com/p/sabredav/</A></TD></TR>
<TR><TD>PHPExcel</TD><TD><A href="http://phpexcel.codeplex.com">http://phpexcel.codeplex.com/</A></TD></TR>
<TR><TD>ExifTool</TD><TD><A href="http://www.sno.phy.queensu.ca/~phil/exiftool/">http://www.sno.phy.queensu.ca/~phil/exiftool/</A></TD></TR>
<TR><TD>html2fpdf</TD><TD><A href="http://html2fpdf.sourceforge.net">http://html2fpdf.sourceforge.net</A></TD></TR>
<TR><TD>fontawesome</TD><TD><A href="http://fontawesome.io/">http://fontawesome.io/</A></TD></TR>
<TR><TD>Silk Icons</TD><TD><A href="http://www.famfamfam.com/lab/icons/silk/">http://www.famfamfam.com/lab/icons/silk/</A></TD></TR>
<TR><TD>interpid</TD><TD><A href="http://www.interpid.eu">http://www.interpid.eu</A></TD></TR>
<TR><TD>EXIF</TD><TD><A href="http://electronics.ozhiker.com">http://electronics.ozhiker.com</A></TD></TR>
<TR><TD>codemirror</TD><TD><A href="http://codemirror.net">http://codemirror.net</A></TD></TR>
<TR><TD>adldap</TD><TD><A href="http://adldap.sourceforge.net">http://adldap.sourceforge.net/</A></TD></TR>
</TABLE>

<?php
}elseif($view == "notes"){
?>

<TABLE CELLPADDING="1" CELLSPACING="3" WIDTH="100%">

<TR class="tabHeader"><TD class="tabHeaderItem" colspan="3">release notes <?=$umgvar["version"]?> - main features</td></tr>

    <TR>
        <TD valign=top class="bord">Added</TD>
        <TD valign=top class="bord">Validity functionality for tabledata</TD>
    </TR>
    <TR>
        <TD valign=top class="bord">Added</TD>
        <TD valign=top class="bord">Extended multitenant functionality</TD>
    </TR>
    <TR>
        <TD valign=top class="bord">Added</TD>
        <TD valign=top class="bord">Full template based reporting system</TD>
    </TR>
    <TR>
        <TD valign=top class="bord">Added</TD>
        <TD valign=top class="bord">Template wysiwyg extension</TD>
    </TR>
    <TR>
        <TD valign=top class="bord">Added</TD>
        <TD valign=top class="bord">inline editing for multilanguage values</TD>
    </TR>
    <TR>
        <TD valign=top class="bord">Added</TD>
        <TD valign=top class="bord">User based reportManager</TD>
    </TR>
    <TR>
        <TD valign=top class="bord">Added</TD>
        <TD valign=top class="bord">global data synchronisation</TD>
    </TR>
    <TR>
        <TD valign=top class="bord">Update</TD>
        <TD valign=top class="bord">using jquery 3.2.1</TD>
    </TR>

<TR>
    <TD valign=top class="bord" colspan=3><br><i>more information on changes shown in <a href="main.php?action=intro&view=ChangeLog">ChangeLog</a></i></TD>
</TR>

</TABLE>


<?php
}elseif($view == 'environment_check'){
?>

    <link rel="stylesheet" href="extern/bootstrap/bootstrap_3.3.7.min.css">
    <link rel="stylesheet" href="layout/comet/icons.css">
    <style>
        /* overwrite bootstrap's h2 settings */
        h2 {
            font-size: 1.5em;
            font-weight: bold;
        }
        .lmbfringeFrameMain .lmbinfo{
            width: 800px;
        }
        body {
            background-color: <?=$farbschema['WEB14']?>;
        }
    </style>
<?php

$embed = true;
$setup_path_project = $umgvar['path'];
require_once('admin/install/tooltips.php');
require_once('admin/install/steps/1_php_ini.php');
require_once('admin/install/steps/2_dependencies.php');


}elseif($view == 'ChangeLog'){
    echo '<div style="padding:10px">';
    echo str_replace(chr(10),'<br>',file_get_contents($umgvar['path'].'/../limbas_src/ChangeLog'));
    echo '</div>';



}elseif($view == 'update_check'){
    echo '<div style="padding:10px">';

    $latestVersion = lmbCheckForUpdates(true);
    echo 'current version : <b>'.$umgvar["version"].'</b><br>';
    echo 'latest version : ';
    if ($latestVersion === true) { # version already is latest
        echo '<b>'.$lang[2928].'</b>';
    } else if ($latestVersion === false) { # error / auto-update not enabled
        ?>
        <form action="main.php">
            <input type="hidden" name="action" value="<?= $action ?>">
            <input type="submit" name="checkForUpdates" value="<?= $lang[2929] ?>">
        </form>
    <?php } else { # later version available ?>
        <a href="https://www.limbas.com" target="_blank"><?= $latestVersion ?><i class="lmb-icon lmb-fav-filled"
                                                                                 style="color: #f9d421;vertical-align:middle;"></i></a>
    <?php }

    echo '</div>';


}elseif($view == "quickstart"){?>
	
<TABLE CELLPADDING="1" CELLSPACING="3" WIDTH="100%">

<td>
<?php require_once("intro.html");?>
</td></tr>
</table>
<?php }?>

<br><br>
<div class="footer">
LIMBAS. Copyright &copy; 1998-2021 LIMBAS GmbH (info@limbas.com). LIMBAS is free software; You can redistribute it and/or modify it under the terms of the GPL General Public License V2 as published by the Free Software Foundation; Go to <a href="http://www.limbas.org/" title="LIMBAS Website" target="new">http://www.limbas.org/</a> for details. LIMBAS comes with ABSOLUTELY NO WARRANTY; Please note that some external scripts are copyright of their respective owners, and are released under different licences.
</div>

</div>
</div>