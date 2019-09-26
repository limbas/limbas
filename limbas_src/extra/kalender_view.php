<?php
/*
 * Copyright notice
 * (c) 1998-2019 Limbas GmbH(support@limbas.org)
 * All rights reserved
 * This script is part of the LIMBAS project. The LIMBAS project is free software; you can redistribute it and/or modify it on 2 Ways:
 * Under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * Or
 * In a Propritary Software Licence http://limbas.org
 * The GNU General Public License can be found at http://www.gnu.org/copyleft/gpl.html.
 * A copy is found in the textfile GPL.txt and important notices to the license from the author is found in LICENSE.txt distributed with these scripts.
 * This script is distributed WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * This copyright notice MUST APPEAR in all copies of the script!
 * Version 3.6
 */

/*
 * ID: 88
 */




/* --- Kalenderlisting Ansicht  -------------------------------------------- */
if($kalenderlist) {

?>
<CENTER>
<BR><BR><CENTER><U><B><?=$lang[304]?>:</B></U><BR><BR><BR>
<TABLE Border="0" CELLPADDING="1" CELLSPACING="1">
<TR><TD COLSPAN="5">
<?php
echo "&nbsp;&nbsp<A HREF=\"main.php?action=kalender_view&kategorie=$kategorie&ID=$ID&gtabid=$gtabid&fieldid=$fieldid&tab_group=$tab_group\"><i class=\"lmb-icon lmb-calendar-alt\" border=\"0\" TITLE=\"$lang[300]\"></i></A>";
echo "&nbsp;&nbsp;&nbsp;&nbsp;<A HREF=\"main.php?action=kalender_view&kalenderlist=1&kategorie=$kategorie&ID=$ID&gtabid=$gtabid&fieldid=$fieldid&tab_group=$tab_group\"><i class=\"lmb-icon lmb-calendar-alt3\" border=\"0\" TITLE=\"$lang[301]\" ALT=\"$lang[301]\"></i></A>";
?>
&nbsp;&nbsp;&nbsp;&nbsp;<?= $tab." ID".$ID ?></TD></TR>

<TR><TD COLSPAN="5"><HR noshade STYLE="color: black; height:1px;"></TD></TR>
<TR><TD><?=$lang[197]?></TD><TD><?=$lang[293]?></TD><TD><?=$lang[294]?></TD><TD><?=$lang[295]?></TD></TR>

<?php $bzm = 0;
while($result_kallist['keyid'][$bzm]){
        if($BGCOLOR == $farbschema['WEB7']){$BGCOLOR = $farbschema['WEB8'];} else {$BGCOLOR = $farbschema['WEB7'];}
        echo "<TR BGCOLOR=\"".$BGCOLOR."\"><TD>&nbsp;&nbsp;".$result_kallist['datum'][$bzm]."&nbsp;&nbsp;</TD><TD>&nbsp;&nbsp;".strftime("%U",$result_kallist['zeitstempel'][$bzm])."&nbsp;&nbsp;</TD><TD BGCOLOR=\"".$result_kallist['farbe'][$bzm]."\">&nbsp;&nbsp;".$result_kallist['farbe'][$bzm]."&nbsp;&nbsp;</TD><TD>&nbsp;&nbsp;".$result_kallist['bemerkung'][$bzm]."</TD></TR>";
$bzm++;
}
?>
</TABLE>
<BR><BR>
</CENTER>
<?php



/* --- Kalenderansicht  -------------------------------------------- */
} else {
?>
<FORM NAME="form1">
<BR><BR><CENTER><U><B><?=$lang[304]?></B></U></CENTER><BR><BR>
<CENTER>
<TABLE Border="0" CELLPADDING="1" CELLSPACING="1">
<TR><TD>
<?php
echo "&nbsp;&nbsp;<A HREF=\"main.php?action=kalender_view&kategorie=$kategorie&ID=$ID&gtabid=$gtabid&fieldid=$fieldid&tab_group=$tab_group\"><i class=\"lmb-icon lmb-calendar-alt\" border=\"0\" TITLE=\"$lang[300]\"></i></A>";
echo "&nbsp;&nbsp;&nbsp;&nbsp;<A HREF=\"main.php?action=kalender_view&kalenderlist=1&kategorie=$kategorie&ID=$ID&gtabid=$gtabid&fieldid=$fieldid&tab_group=$tab_group\"><i class=\"lmb-icon lmb-calendar-alt3\" border=\"0\" TITLE=\"$lang[301]\" ALT=\"$lang[301]\"></i></A>";
echo "&nbsp;&nbsp;&nbsp;&nbsp;".$tab." ID".$ID."";
?>
</TD></TR>
<TR><TD><HR noshade STYLE="color: black; height:1px;"></TD></TR>
<TR>
<?php
$next_line=0;
$bzm3 = 1;
while($result_month[$bzm3]) {
$next_line++;
echo "<TD VALIGN=\"TOP\">";


$tage_m = date("t", mktime(0,0,0,$result_month[$bzm3] ,"01" ,$result_year[$bzm3]));
$wochentag = date("w", mktime(0,0,0,$result_month[$bzm3] ,"01" ,$result_year[$bzm3]));
?>


<TABLE Border="1" CELLPADDING="0" CELLSPACING="1">
<TR bgcolor="<?= $farbschema['WEB8'] ?>"><TD COLSPAN="8" ALIGN="center"><U><B><?= $FONT3.date("F Y", mktime(0,0,0,$result_month[$bzm3] ,"01" ,$result_year[$bzm3])) ?></B></U></TD></TR>
<TR bgcolor="<?= $farbschema['WEB3'] ?>"><TD><?=$lang[311]?></TD><TD><?=$lang[312]?></TD><TD><?=$lang[313]?></TD><TD><?=$lang[314]?></TD><TD><?=$lang[315]?></TD><TD><?=$lang[316]?></TD><TD><?=$lang[317]?></TD><TD><B><?=$lang[293]?></B></TD><TR>

<?php
$tag = 0;
$wtag = 0;
$bzm = 1;
while($tag < $tage_m AND $bzm < 10) {
?>
<TR>
	<?php
	$bzm2 = 1;
	while($bzm2 < 8) {
	if($bzm2 >= $wochentag OR $tag >= 1){$tag++;} else {$tag = "0";}
	$res_tag = mktime(0,0,0,$result_month[$bzm3] ,$tag ,$result_year[$bzm3]);
        if($bzm2 == 6 OR $bzm2 == 7){$fontcolor = "<FONT COLOR=\"".$farbschema['WEB4']."\">";} else {$fontcolor = "<FONT COLOR=\"".$farbschema['WEB2']."\">";}
	if($tag != 0 AND $tag <= $tage_m ){
		if($result_date[$res_tag]) {
                        if($result_color[$res_tag]){$farbe = $result_color[$res_tag];}else{$farbe = $farbschema['WEB3'];}
                        echo "<TD BGCOLOR=\"".$farbe."\" ALIGN=\"CENTER\" HEIGHT=\"30\" CLASS=\"ts\" ID=\"t".$res_tag."\">&nbsp";
			echo $fontcolor."<A HREF=\"#\" OnMouseOver=\"document.form1.descvalue.value='$result_bemerkung[$res_tag]'\" OnMouseOut=\"document.form1.descvalue.value=''\">$tag<A>";
			} else {
                        echo "<TD ALIGN=\"CENTER\" HEIGHT=\"30\" CLASS=\"ts\" ID=\"t".$res_tag."\">&nbsp";
			echo $fontcolor.$tag;
			}
	} else {
                echo "<TD ALIGN=\"CENTER\" HEIGHT=\"30\">&nbsp";
        }
	echo "&nbsp</TD>";
        unset($res_tag);
	$bzm2++;
	}
	?>

<TD BGCOLOR="<?= $farbschema['WEB3'] ?>"><?= strftime("%U",mktime(0,0,0,date("m") + $next_m ,$tag ,date("Y"))) ?></TD>

</TR>
<?php
$bzm++;
}
?>
</TABLE>



<?php
echo "</TD>";
if($next_line == 3){echo "</TR><TR>"; $next_line = 0;}
$bzm3++;
}
?>
</TR></TABLE>
&nbsp;&nbsp;<Input TYPE="text" SIZE="25" NAME="descvalue" STYLE="border-style:none;BACKGROUND-COLOR:<?= $farbschema['WEB8'] ?>;">
</FORM>
</CENTER>
<?php
}
?>
