<?php
/*
 * Copyright notice
 * (c) 1998-2018 Limbas GmbH(support@limbas.org)
 * All rights reserved
 * This script is part of the LIMBAS project. The LIMBAS project is free software; you can redistribute it and/or modify it on 2 Ways:
 * Under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * Or
 * In a Propritary Software Licence http://limbas.org
 * The GNU General Public License can be found at http://www.gnu.org/copyleft/gpl.html.
 * A copy is found in the textfile GPL.txt and important notices to the license from the author is found in LICENSE.txt distributed with these scripts.
 * This script is distributed WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * This copyright notice MUST APPEAR in all copies of the script!
 * Version 3.5
 */

/*
 * ID: 86
 */

/* --- Kalenderlisting Ansicht  -------------------------------------------- */
if($kalenderlist){
?>
<TABLE Border="1" CELLPADDING="1" CELLSPACING="1">
<TR><TD COLSPAN="5">
<?php
echo "&nbsp;&nbsp<A HREF=\"main.php?action=kalender&kategorie=$kategorie&ID=$ID&gtabid=$gtabid&fieldid=$fieldid&tab_group=$tab_group\"><i class=\"lmb-icon lmb-calendar-alt\" border=\"0\" TITLE=\"$lang[300]\"></i></A>";
echo "&nbsp;&nbsp;&nbsp;&nbsp;<A HREF=\"main.php?action=kalender&kalenderlist=1&kategorie=$kategorie&ID=$ID&gtabid=$gtabid&fieldid=$fieldid&tab_group=$tab_group\"><i class=\"lmb-icon lmb-calendar-alt3\" border=\"0\" TITLE=\"$lang[301]\" ALT=\"$lang[301]\"></i></A>";
?>
<B>&nbsp;&nbsp;&nbsp;&nbsp;<?= $tab." ID".$ID ?></B></TD></TR>
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
<?php
/* --- Kalenderansicht  -------------------------------------------- */
} else {


if(!$month_count){$month_count = 12;}
if(!$year_start){$year_start = date("Y");}
if(!$month_start){$month_start = date("m");}
?>

<Script language="JavaScript">

function change_color(ID){
        if( eval("document.form1.f"+ID+".value") == ID ){
                eval("document.getElementById('t"+ID+"').style.backgroundColor = '<?= $farbschema['WEB8'] ?>'");
                eval("document.form1.f"+ID+".value = ''");
        } else {
                eval("document.getElementById('t"+ID+"').style.backgroundColor = document.form1.selected_color.value");
                eval("document.form1.f"+ID+".value = '"+ID+"'");
                eval("document.form1.d"+ID+".value = document.form1.description.value");
                if(document.form1.selected_color.value != '<?= $farbschema['WEB3'] ?>'){
                        eval("document.form1.c"+ID+".value = document.form1.selected_color.value");
                }
        }
}


function select_kw(KW,KT) {
var kwtage = KT.split(",");
var bzm = 0;
while(kwtage[bzm]){
        change_color(kwtage[bzm]);
bzm++;
}
}

function view_kw(KW,KT,COLOR) {
var kwtage = KT.split(",");
var bzm = 0;
while(kwtage[bzm]){
        eval("var el = document.form1.f"+kwtage[bzm]+".value");
        if(el == ''){
        eval("document.getElementById('t"+kwtage[bzm]+"').style.backgroundColor = COLOR");
        }
bzm++;
}
}


function set_color(COLOR){
        if(COLOR == ' '){COLOR = '<?=$farbschema['WEB3'];?>';}
        document.form1.tcolor.style.backgroundColor = COLOR;
        document.form1.selected_color.value = COLOR;
}

</SCRIPT>




<TABLE CELLPADDING="0" CELLSPACING="0" BORDER="0" BGCOLOR="<?=$farbschema['WEB8']?>" STYLE="width:588px"><TR><TD ALIGN="CENTER">

<FORM ACTION="main.php" METHOD="post" NAME="form1">
<input type="hidden" name="action" VALUE="kalender">
<input type="hidden" name="tab_group" VALUE="<?= $tab_group ?>">
<input type="hidden" name="gtabid" VALUE="<?= $gtabid ?>">
<input type="hidden" name="fieldid" VALUE="<?= $fieldid ?>">
<input type="hidden" name="kategorie" VALUE="<?= $kategorie ?>">
<input type="hidden" name="ID" VALUE="<?= $ID ?>">
<input type="hidden" name="selected_color" VALUE="<?= $farbschema['WEB3'] ?>">

<TABLE CELLPADDING="0" CELLSPACING="0" BORDER="0" BGCOLOR="<?=$farbschema['WEB7']?>" STYLE="width:588px">
<TR><TD colspan="6">&nbsp;</TD></TR>
<TR><TD>&nbsp;</TD><TD VALIGN="TOP">

<TABLE CELLPADDING="0" CELLSPACING="0">
        <TR><TD>Jahr:&nbsp;</TD><TD>
        <SELECT NAME="year_start" OnChange="document.form1.submit();" style="width:90px;">
        <OPTION VALUE="<?= date("Y") ?>" <?php if($month_count == date("Y")){echo "SELECTED";}?>><?= date("Y") ?>
        <OPTION VALUE="2002" <?php if($year_start == 2002){echo "SELECTED";}?>>2002
        <OPTION VALUE="2001" <?php if($year_start == 2001){echo "SELECTED";}?>>2001
        <OPTION VALUE="2000" <?php if($year_start == 2000){echo "SELECTED";}?>>2000
        <OPTION VALUE="1999" <?php if($year_start == 1999){echo "SELECTED";}?>>1999
        </SELECT>
        </TD></TR>

        <TR><TD><?=$lang[296]?>:&nbsp;</TD><TD>
        <SELECT NAME="month_count" OnChange="document.form1.submit();" style="width:90px;">
        <OPTION VALUE="1" <?php if($month_count == 1){echo "SELECTED";}?>>1
        <OPTION VALUE="3" <?php if($month_count == 3){echo "SELECTED";}?>>3
        <OPTION VALUE="6" <?php if($month_count == 6){echo "SELECTED";}?>>6
        <OPTION VALUE="12" <?php if($month_count == 12){echo "SELECTED";}?>>12
        </SELECT>
        </TD></TR>

        <TR><TD><?=$lang[297]?>:</TD><TD>
        <SELECT NAME="month_start" OnChange="document.form1.submit();" style="width:90px;">
        <OPTION VALUE="1" <?php if($month_start == 1){echo "SELECTED";}?>>Januar
        <OPTION VALUE="2" <?php if($month_start == 2){echo "SELECTED";}?>>Februar
        <OPTION VALUE="3" <?php if($month_start == 3){echo "SELECTED";}?>>März
        <OPTION VALUE="4" <?php if($month_start == 4){echo "SELECTED";}?>>April
        <OPTION VALUE="5" <?php if($month_start == 5){echo "SELECTED";}?>>Mai
        <OPTION VALUE="6" <?php if($month_start == 6){echo "SELECTED";}?>>Juni
        <OPTION VALUE="7" <?php if($month_start == 7){echo "SELECTED";}?>>Juli
        <OPTION VALUE="8" <?php if($month_start == 8){echo "SELECTED";}?>>August
        <OPTION VALUE="9" <?php if($month_start == 9){echo "SELECTED";}?>>September
        <OPTION VALUE="10" <?php if($month_start == 10){echo "SELECTED";}?>>Oktober
        <OPTION VALUE="11" <?php if($month_start == 11){echo "SELECTED";}?>>November
        <OPTION VALUE="12" <?php if($month_start == 12){echo "SELECTED";}?>>Dezember
        </SELECT>
        </TD></TR>
</TABLE>

</TD><TD VALIGN="TOP">

<TABLE CELLPADDING="2" CELLSPACING="0">
        <TR><TD><?=$lang[295]?>:&nbsp;</TD><TD COLSPAN="2"><INPUT TYPE="TEXT" NAME="description" style="width:200px;"></TD></TR>
        <TR><TD OnClick="div_farbauswahl(event,'farbauswahl')" STYLE="color:blue;cursor:pointer;"><?=$lang[294]?>:&nbsp;</A></TD>
        <TD><INPUT TYPE="TEXT" OnClick="div_farbauswahl(event,'farbauswahl')" NAME="tcolor" READONLY style="width:20px;border:1px solid black;cursor:pointer;background-color:<?= $farbschema['WEB3'] ?>;"></TD>
        <TD WIDTH="30">&nbsp;</TD></TR>
        <TR><TD>&nbsp;</TD><TD COLSPAN="2"><Input TYPE="text" READONLY NAME="descvalue" STYLE="width:200px;border-style:none;background-color:<?= $farbschema['WEB8'] ?>;"></TD></TR>

</TABLE>

</TD><TD VALIGN="TOP">

<TABLE CELLPADDING="0" CELLSPACING="0">
        <TR><TD><?php
        echo "&nbsp;&nbsp;&nbsp<A HREF=\"main.php?action=kalender&kategorie=$kategorie&ID=$ID&gtabid=$gtabid&fieldid=$fieldid&tab_group=$tab_group\"><i class=\"lmb-icon lmb-calendar-alt\" border=\"0\" TITLE=\"$lang[300]\"></i></A>";
        echo "&nbsp;&nbsp;&nbsp;&nbsp;<A HREF=\"main.php?action=kalender&kalenderlist=1&kategorie=$kategorie&ID=$ID&gtabid=$gtabid&fieldid=$fieldid&tab_group=$tab_group\"><i class=\"lmb-icon lmb-calendar-alt3\" border=\"0\" TITLE=\"$lang[301]\" ALT=\"$lang[301]\"></i></A>";
        ?></TD></TR>
        <TR><TD COLSPAN="2">&nbsp;</TD></TR>
        <TR><TD COLSPAN="2"><INPUT TYPE="submit" STYLE="cursor:pointer" VALUE="<?=$lang[302]?>" NAME="change_ok"></TD></TR>
</TABLE>

</TD><TR>
<TR BGCOLOR="<?=$farbschema['WEB3']?>"><TD STYLE="height:2px;" COLSPAN="10"></TD></TR>
</TABLE>






<TABLE Border="0" CELLPADDING="3" CELLSPACING="0">
<TR><TD STYLE="height:5px;"></TD></TR>
<TR>
<?php
$next_line=0;
$next_m=0;
while($next_m < $month_count) {
$next_line++;
        echo "<TD VALIGN=\"TOP\">";


        $tage_m = date("t", mktime(0,0,0,$month_start + $next_m ,01 ,$year_start));
        $wochentag = date("w", mktime(0,0,0,$month_start + $next_m ,01 ,$year_start));
        if($wochentag == 0){$wochentag = 7;}
        ?>

        <TABLE Border="1" CELLPADDING="2" CELLSPACING="0" STYLE="border-style:solid;border-width:1px;border-color:#000000">
        <?php if(mktime(0,0,0,$month_start + $next_m ,01 ,$year_start) == mktime(0,0,0,date("m") ,01 ,date("Y"))){$BGCOLOR = $farbschema['WEB3'];}else{$BGCOLOR = $farbschema['WEB8'];}?>
        <TR bgcolor="<?= $BGCOLOR ?>"><TD COLSPAN="8" ALIGN="center"><U><B><?= $FONT3.date("F Y", mktime(0,0,0,$month_start + $next_m ,01 ,$year_start)) ?></B></U></TD></TR>
        <TR bgcolor="<?= $BGCOLOR ?>"><TD><?=$lang[311]?></TD><TD><?=$lang[312]?></TD><TD><?=$lang[313]?></TD><TD><?=$lang[314]?></TD><TD><?=$lang[315]?></TD><TD><?=$lang[316]?></TD><TD><?=$lang[317]?></TD><TD><B><?=$lang[293]?></B></TD><TR>

        <?php
        $tag = 0;
        $wtag = 0;
        $bzm = 1;
        while($tag < $tage_m AND $bzm < 10) {
        unset($ktag);
        ?>
                <TR>
                <?php
                $bzm2 = 1;
                while($bzm2 < 8) {
                        if($bzm2 >= $wochentag OR $tag >= 1){$tag++;} else {$tag = "0";}
                        $res_tag = mktime(0,0,0,$month_start + $next_m ,$tag ,$year_start);
                        if($bzm2 == 6 OR $bzm2 == 7){$fontcolor = "<FONT COLOR=\"".$farbschema['WEB4']."\">";} else {$fontcolor = "<FONT COLOR=\"".$farbschema['WEB2']."\">";}
                        if($tag != 0 AND $tag <= $tage_m){
                                if($result_date[$res_tag]) {
                                        if($result_color[$res_tag]){$farbe = $result_color[$res_tag];}else{$farbe = $farbschema['WEB3'];}
                                        $ktag .= "$res_tag,";
                                        echo "<TD BGCOLOR=\"".$farbe."\" ALIGN=\"CENTER\" HEIGHT=\"30\" CLASS=\"ts\" ID=\"t".$res_tag."\" STYLE=\"cursor:pointer\" OnClick=\"change_color('".$res_tag."');\" OnMouseOver=\"document.form1.descvalue.value='$result_bemerkung[$res_tag]';\" OnMouseOut=\"document.form1.descvalue.value=''\">";
                                        echo "$fontcolor$tag<INPUT TYPE=\"HIDDEN\" NAME=\"f".$res_tag."\" VALUE=\"$res_tag\"><INPUT TYPE=\"HIDDEN\" NAME=\"c".$res_tag."\" VALUE=\"$result_color[$res_tag]\"><INPUT TYPE=\"HIDDEN\" NAME=\"d".$res_tag."\" VALUE=\"$result_bemerkung[$res_tag]\">";
                                } else {
                                        $ktag .= "$res_tag,";
                                        echo "<TD ALIGN=\"CENTER\" HEIGHT=\"30\" CLASS=\"ts\" ID=\"t".$res_tag."\" STYLE=\"cursor:pointer\" OnClick=\"change_color('".$res_tag."');\" OnMouseOver=\"if(document.form1.f".$res_tag.".value != '".$res_tag."'){this.style.backgroundColor='".$farbschema['WEB10']."';}\" OnMouseOut=\"if(document.form1.f".$res_tag.".value != '".$res_tag."'){this.style.backgroundColor='".$farbschema['WEB8']."';}\">";
                                        echo "$fontcolor$tag<INPUT TYPE=\"HIDDEN\" NAME=\"f".$res_tag."\"><INPUT TYPE=\"HIDDEN\" NAME=\"c".$res_tag."\"><INPUT TYPE=\"HIDDEN\" NAME=\"d".$res_tag."\">";
                                }
                        } else {
                                echo "<TD ALIGN=\"CENTER\" HEIGHT=\"30\">";
                        }
                        echo "</TD>";
                        unset($res_tag);
                        $bzm2++;
                }
                ?>

                <TD STYLE="cursor:pointer" OnMouseOver="view_kw('<?= strftime("%U",mktime(0,0,0,$month_start + $next_m ,$tag ,$year_start)) ?>','<?= $ktag ?>','<?=$farbschema['WEB10']?>')" OnMouseOut="view_kw('<?= strftime("%U",mktime(0,0,0,$month_start + $next_m ,$tag ,$year_start)) ?>','<?= $ktag ?>','<?=$farbschema['WEB8']?>')" OnCLick="select_kw('<?= strftime("%U",mktime(0,0,0,$month_start + $next_m ,$tag ,$year_start)) ?>','<?= $ktag ?>');" BGCOLOR="<?= $BGCOLOR ?>"><?= strftime("%U",mktime(0,0,0,$month_start + $next_m ,$tag ,$year_start)) ?></TD>

                </TR>
        <?php
        $bzm++;
        }
        ?>
        </TABLE>


        <?php
        echo "</TD>";
        if($next_line == 3){echo "</TR><TR>"; $next_line = 0;}
$next_m++;
}
?>
</TR></TABLE>
</FORM>
</TD></TR></TABLE>

<?php
}
?>

