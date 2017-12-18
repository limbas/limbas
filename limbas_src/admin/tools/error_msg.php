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
 * ID: 108
 */
?>

<FORM ACTION="main_admin.php" METHOD="post" NAME="form1">
<input type="hidden" name="action" value="setup_error_msg">
<input type="hidden" name="del">
<input type="hidden" name="log_typ" value="<?=$log_typ?>">


<DIV class="lmbPositionContainerMainTabPool">

<TABLE class="tabpool" BORDER="0" width="600" cellspacing="0" cellpadding="0"><TR><TD>

<?
if($log_typ == 1 OR !$log_typ){
	
	if($del){
		unlink($umgvar["pfad"]."/TEMP/log/sql_error.log");
		$rf = fopen($umgvar["pfad"]."/TEMP/log/sql_error.log","w");
		fclose($rf);
	}
	
	if(!$showcount){$showcount = 50;}
	if(file_exists($umgvar["pfad"]."/TEMP/log/sql_error.log")){	
		$handle = file($umgvar["pfad"]."/TEMP/log/sql_error.log");
		$lcount = count($handle);
	}

?>
	<TABLE BORDER="0" cellspacing="0" cellpadding="0"><TR class="tabpoolItemTR">
	<TD nowrap class="tabpoolItemActive">sql_error.log</TD>
	<TD nowrap class="tabpoolItemInactive" OnClick="document.location.href='main_admin.php?action=setup_error_msg&log_typ=2'">indize_error.log</TD>
	<TD nowrap class="tabpoolItemInactive" OnClick="document.location.href='main_admin.php?action=setup_error_msg&log_typ=3'">indize.log</TD>
	<TD class="tabpoolItemSpace">&nbsp;</TD>
	</TR></TABLE>

	</TD></TR>
	
	<TR><TD class="tabpoolfringe">
	
	<TABLE BORDER="0" cellspacing="1" cellpadding="2" width="100%" class="tabBody">

	<TR class="tabBody"><TD COLSPAN="2"><?=$lang[2559]?></TD><TD><?=$lcount?></TD></TR>
        <TR class="tabBody"><TD COLSPAN="2"><?=$lang[550]?></TD><TD><i class="lmb-icon lmb-trash" BORDER="0" onclick="document.form1.del.value=1;document.form1.submit();" style="cursor:pointer;"></i></TD></TR>
	<TR class="tabBody"><TD COLSPAN="2"><?=$lang[2560]?></TD><TD><input type="text" value="<?=$showcount?>" name="showcount" size="5"></TD></TR>
	<TR class="tabBody"><TD COLSPAN="9">&nbsp;</TD></TR>
	
	<TR>
	<TD class="tabHeader"><?=$lang[542]?></TD>
	<TD class="tabHeader"><?=$lang[543]?></TD>
	<TD class="tabHeader"><?=$lang[544]?></TD>
	<TD class="tabHeader"><?=$lang[545]?></TD>
	<TD class="tabHeader"><?=$lang[546]?></TD>
	<TD class="tabHeader"><?=$lang[547]?></TD>
	<TD class="tabHeader"><?=$lang[548]?></TD>
	</TR>
	
	<?
	if($handle){
		$start = $lcount - $showcount;
		if($start < 0){$start = 0;$showcount=$lcount;}
		#echo $lcount."-".$start."-".$showcount."-";
		for ($i=$start; $i<$lcount ;$i++){

			$parts = explode("\t",$handle[$i]);
			
	        echo "<TR class=\"tabBody\">";
	        echo"<TD valign=\"top\">".$parts[1]."</TD>";
	        echo"<TD valign=\"top\">".$parts[0]."</TD>";
	        echo"<TD valign=\"top\">".$parts[2]."</TD>";
	        echo"<TD valign=\"top\">".$parts[3]."</TD>";
	        echo"<TD valign=\"top\">".$parts[4]."</TD>";
	        echo"<TD valign=\"top\"><TEXTAREA readonly COLS=\"50\" ROWS=\"3\">".htmlentities($parts[5],ENT_QUOTES,$umgvar["charset"])."</TEXTAREA>&nbsp;&nbsp;</TD>";
	        echo"<TD valign=\"top\"><TEXTAREA readonly COLS=\"50\" ROWS=\"3\">".htmlentities($parts[6],ENT_QUOTES,$umgvar["charset"])."</TEXTAREA>&nbsp;&nbsp;</TD>";
	        echo"</TR>";
		}
	}
	?>
	
	<TR class="tabFooter"><TD colspan="9"></TD></TR>
	</TABLE>
	
	
	
	
	
<?}elseif($log_typ == 2){
	
	if($del){
		unlink($umgvar["pfad"]."/TEMP/log/indize_error.log");
		$rf = fopen($umgvar["pfad"]."/TEMP/log/indize_error.log","w");
		fclose($rf);
	}
?>
	<TABLE BORDER="0" cellspacing="0" cellpadding="0"><TR class="tabpoolItemTR">
	<TD nowrap class="tabpoolItemInactive" OnClick="document.location.href='main_admin.php?action=setup_error_msg&log_typ=1'">sql_error.log</TD>
	<TD nowrap class="tabpoolItemActive">indize_error.log</TD>
	<TD nowrap class="tabpoolItemInactive" OnClick="document.location.href='main_admin.php?action=setup_error_msg&log_typ=3'">indize.log</TD>
	<TD class="tabpoolItemSpace">&nbsp;</TD>
	</TR></TABLE>

	</TD></TR>
	
	<TR><TD class="tabpoolfringe">
	
	<TABLE BORDER="0" cellspacing="1" cellpadding="2" width="100%" class="tabBody">

            <TR class="tabBody"><TD><?=$lang[550]?> &nbsp;&nbsp;&nbsp;<i class="lmb-icon lmb-trash" BORDER="0" onclick="document.form1.del=1;document.form1.submit();" style="cursor:pointer;"></i></TD></TR>
	<TR class="tabBody"><TD>&nbsp;</TD></TR>
	
	<TR>
	<TD>
<textarea readonly style="width:100%;height:500px;overflow:auto;"><?if(file_exists($umgvar["pfad"]."/TEMP/log/indize_error.log")){echo file_get_contents($umgvar["pfad"]."/TEMP/log/indize_error.log");}?></textarea>
	</TD>
	</TR>
	
	<TR class="tabFooter"><TD colspan="9"></TD></TR>
	</TABLE>
	
	
	
<?}elseif($log_typ == 3){
	
	if($del){
		unlink($umgvar["pfad"]."/TEMP/log/indize.log");
		$rf = fopen($umgvar["pfad"]."/TEMP/log/indize.log","w");
		fclose($rf);
	}
?>
	<TABLE BORDER="0" cellspacing="0" cellpadding="0"><TR class="tabpoolItemTR">
	<TD nowrap class="tabpoolItemInactive" OnClick="document.location.href='main_admin.php?action=setup_error_msg&log_typ=1'">sql_error.log</TD>
	<TD nowrap class="tabpoolItemInactive" OnClick="document.location.href='main_admin.php?action=setup_error_msg&log_typ=2'">indize_error.log</TD>
	<TD nowrap class="tabpoolItemActive">indize.log</TD>
	<TD class="tabpoolItemSpace">&nbsp;</TD>
	</TR></TABLE>

	</TD></TR>
	
	<TR><TD class="tabpoolfringe">
	
	<TABLE BORDER="0" cellspacing="1" cellpadding="2" width="100%" class="tabBody">

            <TR class="tabBody"><TD><?=$lang[550]?> &nbsp;&nbsp;&nbsp;<i class="lmb-icon lmb-trash" BORDER="0" onclick="document.form1.del=1;document.form1.submit();" style="cursor:pointer;"></i></TD></TR>
	<TR class="tabBody"><TD>&nbsp;</TD></TR>
	
	<TR>
	<TD>
	<textarea readonly style="width:100%;height:500px;overflow:auto;"><?if(file_exists($umgvar["pfad"]."/TEMP/log/indize.log")){echo file_get_contents($umgvar["pfad"]."/TEMP/log/indize.log");}?></textarea>
	</TD>
	</TR>
	
	<TR class="tabFooter"><TD colspan="9"></TD></TR>
	</TABLE>
<?}?>
	
	
	
	
	
	
	






</TABLE>
</FORM>
</div>
