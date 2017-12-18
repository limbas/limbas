<?
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
 * ID: 4
 */
?>


<div>
<div class="tabfringe lmbinfo">


<h2>LIMBAS-<?php echo $umgvar["version"];// $umgvar["version"]; ?></h2>
<div class="infonav">
	<?
	if(file_exists("EXTENSIONS/customization/logo_small.png")){
		echo "<img style=\"float:right;\" src=\"EXTENSIONS/customization/logo_small.png\">";
	}
	?>
	
	
	<a href="main.php?action=intro">Info</a>&nbsp;&nbsp;|&nbsp;&nbsp;
	<a href="main.php?action=intro&view=credits">credits</a>&nbsp;&nbsp;|&nbsp;&nbsp;
	<a href="main.php?action=intro&view=notes">release notes</a>&nbsp;&nbsp;|&nbsp;&nbsp;
	<a href="http://www.limbas.org" target="_new">help</a>&nbsp;&nbsp;|&nbsp;&nbsp;
	<a href="intro.html" target=_new>quickstart</a>
</div>

<?php
if(!$view){
?>


<table border="0" cellpadding="2" cellspacing="0" width="100%">
<TR class="tabHeader"><TD class="tabHeaderItem" colspan="2">info</td></tr>
<tr><td valign="top"><?php echo $lang[2];   ?>:</td><td style="color:#999999"><?php echo $umgvar["version"];//$umgvar['version']; ?></td></tr>
<tr><td valign="top"><?php echo $lang[3];   ?>:</td><td style="color:#999999"><?php echo $session['username']; ?></td></tr>
<tr><td valign="top"><?php echo $lang[4];   ?>:</td><td style="color:#999999"><?php echo "{$session['vorname']} {$session['name']}"; ?></td></tr>
<tr><td valign="top"><?php echo $lang[11];  ?>:</td><td style="color:#999999"><?php echo $umgvar['company']; ?></td></tr>
<tr><td valign="top"><?php echo $lang[749]; ?>:</td><td style="color:#999999"><?php echo $session['lastlogin']; ?></td></tr>
<tr><td valign="top"><?php echo $lang[7];   ?>:</td><td style="color:#999999"><?php echo $_SERVER['SERVER_NAME']; ?></td></tr>
<tr><td valign="top"><?php echo $lang[8];   ?>:</td><td style="color:#999999"><?php echo $_SERVER['REMOTE_ADDR']; ?></td></tr>
<tr><td valign="top"><?php echo $lang[9];   ?>:</td><td style="color:#999999"><?php echo $_SERVER['HTTP_USER_AGENT']; ?></td></tr>

<?php if($session['group_id'] == 1){?>
<tr><td valign="top" colspan="2"><HR></td></tr>
<tr><td valign="top">Database Vendor:</td><td style="color:#999999"><?php echo $DBA["DB"]; ?></td></tr>
<tr><td valign="top">Database User:</td><td style="color:#999999"><?php echo $DBA["DBUSER"]; ?></td></tr>
<tr><td valign="top">Database Name:</td><td style="color:#999999"><?php echo $DBA["DBNAME"]; ?></td></tr>
<tr><td valign="top">Database Host:</td><td style="color:#999999"><?php echo $DBA["DBHOST"]; ?></td></tr>
<tr><td valign="top"><?php echo $lang[10]; ?>:</td><td style="color:#999999"><?php echo isset($AUTH_TYPE) ? $AUTH_TYPE : "limbas-db"; ?></td></tr>
<?}?>

</table>


<?php
}elseif($view == "credits"){
?>

<TABLE CELLPADDING="1" CELLSPACING="3" WIDTH="100%">
<TR class="tabHeader"><TD class="tabHeaderItem" colspan="2">credits</td></tr>
<TR><TD>Silk Icons</TD><TD><A href="http://www.famfamfam.com/lab/icons/silk/">http://www.famfamfam.com/lab/icons/silk/</A></TD></TR>
<TR><TD>fpdi</TD><TD><A href="http://fpdi.setasign.de/">http://fpdi.setasign.de/</A></TD></TR>
<TR><TD>fpdf</TD><TD><A href="http://www.fpdf.org">http://www.fpdf.org</A></TD></TR>
<TR><TD>interpid</TD><TD><A href="http://www.interpid.eu">http://www.interpid.eu</A></TD></TR>
<TR><TD>ExifTool</TD><TD><A href="http://www.sno.phy.queensu.ca/~phil/exiftool/">http://www.sno.phy.queensu.ca/~phil/exiftool/</A></TD></TR>
<TR><TD>EXIF</TD><TD><A href="http://electronics.ozhiker.com">http://electronics.ozhiker.com</A></TD></TR>
<TR><TD>codemirror</TD><TD><A href="http://codemirror.net">http://codemirror.net</A></TD></TR>
<TR><TD>jquery</TD><TD><A href="http://jquery.com">http://jquery.com</A></TD></TR>
<TR><TD>colresizable</TD><TD><A href="http://quocity.com/colresizable/">http://quocity.com/colresizable/</A></TD></TR>
<TR><TD>fullcalendar</TD><TD><A href="http://arshaw.com/fullcalendar/">http://arshaw.com/fullcalendar/</A></TD></TR>
<TR><TD>html2fpdf</TD><TD><A href="http://html2fpdf.sourceforge.net">http://html2fpdf.sourceforge.net</A></TD></TR>
<TR><TD>SabreDAV</TD><TD><A href="http://code.google.com/p/sabredav/">http://code.google.com/p/sabredav/</A></TD></TR>
<TR><TD>PHPExcel</TD><TD><A href="http://phpexcel.codeplex.com">http://phpexcel.codeplex.com/</A></TD></TR>
<TR><TD>adldap</TD><TD><A href="http://adldap.sourceforge.net">http://adldap.sourceforge.net/</A></TD></TR>
<TR><TD>fontawesome</TD><TD><A href="http://fontawesome.io/">http://fontawesome.io/</A></TD></TR>
</TABLE>

<?php
}elseif($view == "notes"){
?>

<TABLE CELLPADDING="1" CELLSPACING="3" WIDTH="100%">

<TR class="tabHeader"><TD class="tabHeaderItem" colspan="3">release notes 3.0 - main features</td></tr>


<TR>
<TD valign=top class="bord">Added</TD>
<TD valign=top class="bord">multilanguage</TD>
<TD valign=top class="bord">multilanguage support for independent selectpools in formular</TD>
</TR>
<TR>
<TD valign=top class="bord">Bugfix</TD>
<TD valign=top class="bord">install process</TD>
<TD valign=top class="bord">fixing install process for php < 3.5</TD>
</TR>
<TR>
<TD valign=top class="bord">Bugfix</TD>
<TD valign=top class="bord">file explorer</TD>
<TD valign=top class="bord">bugfix union query in filestructure</TD>
</TR>


<TR>
<TD valign=top class="bord" colspan=3><br><i>more information on changes shown in "ChangeLog"</i></TD>
</TR>

</TABLE>

<?}elseif($view == "quickstart"){?>
	
<TABLE CELLPADDING="1" CELLSPACING="3" WIDTH="100%">

<td>
<?require_once("intro.html");?>
</td></tr>
</table>
<?}?>

<br><br>
<div class="footer">
LIMBAS. Copyright &copy; 1998-2016 Axel Westhagen (support@limbas.org). LIMBAS is free software; You can redistribute it and/or modify it under the terms of the GPL General Public License V2 as published by the Free Software Foundation; Go to <a href="http://www.limbas.org/" title="LIMBAS Website" target="new">http://www.limbas.org/</a> for details. LIMBAS comes with ABSOLUTELY NO WARRANTY; Please note that some external scripts are copyright of their respective owners, and are released under different licences.
</div>

</div>
</div>