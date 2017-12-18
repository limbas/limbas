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
 * ID:
 */
?>

<TABLE BORDER="0" cellspacing="0" cellpadding="0"><TR><TD width="20">&nbsp;</TD><TD>
<TABLE BORDER="0" cellspacing="1" cellpadding="1">
<TR><TD COLSPAN="99">&nbsp;</TD></TR>

<?
if($diag_myd == 3){$d = 1;}else{$d = 0;}
if($diag_myd == 2){$m = 1;}else{$m = 0;}
if($diag_myd == 1){$y = 1;}else{$y = 0;}

$sqlquery = "SELECT DISTINCT ID FROM LMB_CHARTS WHERE DIAG_ID = $diag_typ";
$rs = odbc_exec($db,$sqlquery) or errorhandle(odbc_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);

$bzm = 1;
while(odbc_fetch_row($rs, $bzm)) {
        unset($last);
        echo "<BR><BR>";

        $sqlquery1 = "SELECT DIAG_DESC,DIAG_VALUE,DIAG_SQL FROM LMB_CHARTS WHERE ID = ".odbc_result($rs, "ID");
        $rs1 = odbc_exec($db,$sqlquery1) or errorhandle(odbc_errormsg($db),$sqlquery1,$action,__FILE__,__LINE__);
        $phpfile = fopen($umgvar[pfad]."/USER/".$session[user_id]."/temp/diag".odbc_result($rs, "ID").".php","w+");

        $start_time = dateToStamp($diag_von);
        $end_time = dateToStamp($diag_bis);
        $run_time = dateToStamp($diag_von);


        if($diag_myd == 3){$tmp_time = mktime(0,0,0,date("m",$run_time),date("d",$run_time)+1,date("Y",$run_time));}
        if($diag_myd == 2){$tmp_time = mktime(0,0,0,date("m",$run_time)+1,01,date("Y",$run_time));}
        if($diag_myd == 1){$tmp_time = mktime(0,0,0,01,01,date("Y",$run_time)+1);}

        if($tmp_time <= $end_time){
                $count_of++;
                if($diag_myd == 1){$x_desc[] = "'".date("Y",$run_time)."'";}
                elseif($diag_myd == 2){$x_desc[] = "'".date("M",$run_time)."'";}
                elseif($diag_myd == 3){$x_desc[] = "'".date("D",$run_time)."'";}
                $db_date_start = convert_date(date("d.m.Y",$run_time));
                $run_time = $tmp_time;
                $db_date_end = convert_date(date("d.m.Y",mktime(0,0,0,date("m",$run_time),date("d",$run_time)-1,date("Y",$run_time))));
                eval(odbc_result($rs1, "DIAG_SQL"));
                #echo $db_date_start." ".$db_date_end."<BR>";
        }


        while(mktime(0,0,0,date("m",$run_time)+$m,date("d",$run_time)+$d,date("Y",$run_time)+$y) <= $end_time){
                $count_of++;
                if($diag_myd == 1){$x_desc[] = "'".date("Y",$run_time)."'";}
                elseif($diag_myd == 2){$x_desc[] = "'".date("M",$run_time)."'";}
                elseif($diag_myd == 3){$x_desc[] = "'".date("D",$run_time)."'";}
                $db_date_start = convert_date(date("d.m.Y",$run_time));
                $run_time = mktime(0,0,0,date("m",$run_time)+$m,date("d",$run_time)+$d,date("Y",$run_time)+$y);
                $db_date_end = convert_date(date("d.m.Y",mktime(0,0,0,date("m",$run_time),date("d",$run_time)-1,date("Y",$run_time))));
                eval(odbc_result($rs1, "DIAG_SQL"));
                #echo $db_date_start." ".$db_date_end."<BR>";
        }

        if($run_time <= $end_time){
                $count_of++;
                $db_date_start = convert_date(date("d.m.Y",$run_time));
                $db_date_end = convert_date(date("d.m.Y",$end_time));
                if($diag_myd == 1){$x_desc[] = "'".date("Y",$run_time)."'";}
                elseif($diag_myd == 2){$x_desc[] = "'".date("M",$run_time)."'";}
                elseif($diag_myd == 3){$x_desc[] = "'".date("D",$run_time)."'";}
                $run_time = mktime(0,0,0,date("m",$run_time)+$m,date("d",$run_time)+$d,date("Y",$run_time)+$y);
                $last = 1;
                eval(odbc_result($rs1, "DIAG_SQL"));
                #echo $db_date_start." ".$db_date_end."<BR>";
        }


        fputs($phpfile,"<?\n");
        fputs($phpfile,"\n\n".$result."\n");
        fputs($phpfile,odbc_result($rs1, "DIAG_VALUE"));
        fputs($phpfile,"\n?>");
        fclose($phpfile);
        echo "<IMG SRC=\"USER/".$session[user_id]."/temp/diag".odbc_result($rs, "ID").".php\">";
$bzm++;
}
?>





</TABLE>
</TD></TR></TABLE>

