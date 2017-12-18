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
 * ID: 14
 */


if($change_col){
	/* --- Transaktion START --------------------------------------------- */
	lmb_StartTransaction();
	
	# -------BOOLEAN---------
	if($gfield[$gtabid][field_type][$fieldid] == 10){
		if($change_col[1] == 1){
			$sqlquery = "SELECT ID,".$gfield[$gtabid]["field_name"][$fieldid]." FROM ".$gtab["table"][$gtabid];
			$rs = odbc_exec($db,$sqlquery) or errorhandle(odbc_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
			if(!$rs) {$commit = 1;}
			$bzm = 1;
			while(odbc_fetch_row($rs, $bzm)) {
				if(odbc_result($rs, $gfield[$gtabid]["field_name"][$fieldid])){
					$sqlquery1 = "UPDATE ".$gtab["table"][$gtabid]." SET ".$gfield[$gtabid]["field_name"][$fieldid]." = ".LMB_DBDEF_FALSE." WHERE ID = ".odbc_result($rs, "ID");
					$rs1 = odbc_exec($db,$sqlquery1) or errorhandle(odbc_errormsg($db),$sqlquery1,$action,__FILE__,__LINE__);
					if(!$rs1) {$commit = 1;}				
				}else{
					$sqlquery1 = "UPDATE ".$gtab["table"][$gtabid]." SET ".$gfield[$gtabid]["field_name"][$fieldid]." = ".LMB_DBDEF_TRUE." WHERE ID = ".odbc_result($rs, "ID");
					$rs1 = odbc_exec($db,$sqlquery1) or errorhandle(odbc_errormsg($db),$sqlquery1,$action,__FILE__,__LINE__);
					if(!$rs1) {$commit = 1;}				
				}
			$bzm++;	
			}			
		}elseif($change_col[1] == 2){
			$sqlquery = "UPDATE ".$gtab["table"][$gtabid]." SET ".$gfield[$gtabid]["field_name"][$fieldid]." = ".LMB_DBDEF_TRUE;
			$rs = odbc_exec($db,$sqlquery) or errorhandle(odbc_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
			if(!$rs) {$commit = 1;}
		}elseif($change_col[1] == 3){
			$sqlquery = "UPDATE ".$gtab["table"][$gtabid]." SET ".$gfield[$gtabid]["field_name"][$fieldid]." = ".LMB_DBDEF_FALSE;
			$rs = odbc_exec($db,$sqlquery) or errorhandle(odbc_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
			if(!$rs) {$commit = 1;}
		}
	# -------Text---------
	}elseif($gfield[$gtabid][field_type][$fieldid] == 1){
		if($change_col[2]){
			if(preg_match("/".$gfield[$gtabid]["regel"][$fieldid]."/", $change_col[2])){
				$where = "WHERE ".$gfield[$gtabid]["field_name"][$fieldid]." LIKE '".substr(str_replace("'","''",trim($change_col[2])),0,$gfield[$gtabid][size][$fieldid])."'";
			}else{echo "<B>$lang[58]</B>";}
		}
		if($change_col[1]){
			if(preg_match("/".$gfield[$gtabid]["regel"][$fieldid]."/", $change_col[1])){
				$sqlquery = "UPDATE ".$gtab["table"][$gtabid]." SET ".$gfield[$gtabid]["field_name"][$fieldid]." = '".substr(str_replace("'","''",trim($change_col[1])),0,$gfield[$gtabid][size][$fieldid])."' ".$where;
			}else{echo "<B>$lang[58]</B>";}
		}else{
			$sqlquery = "UPDATE ".$gtab["table"][$gtabid]." SET ".$gfield[$gtabid]["field_name"][$fieldid]." = '' ".$where;	
		}	
		$rs = odbc_exec($db,$sqlquery) or errorhandle(odbc_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
		if(!$rs) {$commit = 1;}
	# -------Zahl---------
	}elseif($gfield[$gtabid][field_type][$fieldid] == 5){	
		if($change_col[2]){
			if(preg_match("/".$gfield[$gtabid]["regel"][$fieldid]."/", $change_col[2])){
				if($change_col[3] == 1){$change_col[3] = "=";}
				if($change_col[3] == 2){$change_col[3] = ">";}
				if($change_col[3] == 3){$change_col[3] = "<";}
				$where = "WHERE ".$gfield[$gtabid]["field_name"][$fieldid]." ".$change_col[3]." ".substr($change_col[2],0,$gfield[$gtabid][size][$fieldid]);
			}else{echo "<B>$lang[58]</B>";}
		}
		if($change_col[1]){
			if(preg_match("/".$gfield[$gtabid]["regel"][$fieldid]."/", $change_col[1])){
				if($change_col[0] == 1){
					$sqlquery = "UPDATE ".$gtab["table"][$gtabid]." SET ".$gfield[$gtabid]["field_name"][$fieldid]." = ".$change_col[1]." ".$where;
				}elseif($change_col[0] == 2){
					$sqlquery = "UPDATE ".$gtab["table"][$gtabid]." SET ".$gfield[$gtabid]["field_name"][$fieldid]." = (".$gfield[$gtabid]["field_name"][$fieldid]." + ".$change_col[1].") ".$where;
				}elseif($change_col[0] == 3){
					$sqlquery = "UPDATE ".$gtab["table"][$gtabid]." SET ".$gfield[$gtabid]["field_name"][$fieldid]." = (".$gfield[$gtabid]["field_name"][$fieldid]." - ".$change_col[1].") ".$where;
				}
			}else{echo "<B>$lang[58]</B>";}
		}	
		$rs = odbc_exec($db,$sqlquery) or errorhandle(odbc_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
		if(!$rs) {$commit = 1;}	
	# -------Datum---------
	}elseif($gfield[$gtabid][field_type][$fieldid] == 2){	
		if($change_col[2]){
			if(preg_match("/".$gfield[$gtabid]["regel"][$fieldid]."/", $change_col[2])){
				$change_col[2] = convert_date($change_col[2]);
				if($change_col[3] == 1){$change_col[3] = "=";}
				if($change_col[3] == 2){$change_col[3] = ">";}
				if($change_col[3] == 3){$change_col[3] = "<";}
				$where = "WHERE ".$gfield[$gtabid]["field_name"][$fieldid]." ".$change_col[3]." '".substr($change_col[2],0,$gfield[$gtabid][size][$fieldid])."'";
			}else{echo "<B>$lang[58]</B>";}
		}
		if($change_col[1]){
			if($change_col[0] == 1){
				if(preg_match("/".$gfield[$gtabid]["regel"][$fieldid]."/", $change_col[1])){
					$change_col[1] = convert_date($change_col[1]);
					$sqlquery = "UPDATE ".$gtab["table"][$gtabid]." SET ".$gfield[$gtabid]["field_name"][$fieldid]." = '".$change_col[1]."' ".$where;
				}
			}elseif($change_col[0] == 2){
				$sqlquery = "UPDATE ".$gtab["table"][$gtabid]." SET ".$gfield[$gtabid]["field_name"][$fieldid]." = ADDDATE(".$gfield[$gtabid]["field_name"][$fieldid].", ".$change_col[1].") ".$where;
			}elseif($change_col[0] == 3){
				$sqlquery = "UPDATE ".$gtab["table"][$gtabid]." SET ".$gfield[$gtabid]["field_name"][$fieldid]." = SUBDATE(".$gfield[$gtabid]["field_name"][$fieldid].", ".$change_col[1].") ".$where;
			}
		}else{echo "<B>$lang[58]</B>";}
		
		$rs = odbc_exec($db,$sqlquery) or errorhandle(odbc_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
		if(!$rs) {$commit = 1;}
	# -------SELECT---------
	}elseif($gfield[$gtabid][field_type][$fieldid] == 4){
		if($change_col[2]){
			if(preg_match("/".$gfield[$gtabid]["regel"][$fieldid]."/", $change_col[2])){
				$where = "WHERE ".$gfield[$gtabid]["field_name"][$fieldid]." LIKE '".substr(str_replace("'","''",trim($change_col[2])),0,$gfield[$gtabid][size][$fieldid])."'";
			}else{echo "<B>$lang[58]</B>";}
		}
		if($change_col[1]){
			if(preg_match("/".$gfield[$gtabid]["regel"][$fieldid]."/", $change_col[1])){
				$sqlquery = "UPDATE ".$gtab["table"][$gtabid]." SET ".$gfield[$gtabid]["field_name"][$fieldid]." = '".substr(str_replace("'","''",trim($change_col[1])),0,$gfield[$gtabid][size][$fieldid])."' ".$where;
			}else{echo "<B>$lang[58]</B>";}
		}else{
			$sqlquery = "UPDATE ".$gtab["table"][$gtabid]." SET ".$gfield[$gtabid]["field_name"][$fieldid]." = '' ".$where;	
		}	
		$rs = odbc_exec($db,$sqlquery) or errorhandle(odbc_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
		if(!$rs) {$commit = 1;}
	# -------Zahl---------
	}		


	# --- Transaktion ENDE --------
	if($commit == 1){
		lmb_EndTransaction(0,$lang[115]);
	} else {
		lmb_EndTransaction(1);
	}
}
?>







<TABLE BORDER="0" cellspacing="0" cellpadding="0"><TR><TD WIDTH="20">&nbsp;</TD><TD>
<BR><B><?=$gfield[$gtabid][spelling][$fieldid]?></B>

<FORM ACTION="main.php" METHOD="post" NAME="form1">
<input type="hidden" name="action" value="gtab_change_col">
<input type="hidden" name="gtabid" value="<?echo $gtabid;?>">
<input type="hidden" name="fieldid" value="<?echo $fieldid;?>">

<TABLE BORDER="0" cellspacing="0" cellpadding="1">

<?# -------Text---------  
if($gfield[$gtabid][field_type][$fieldid] == 1){?>
<TR ALIGN="LEFT"><TD><?=$lang[59]?></TD></TR>
<TR ALIGN="LEFT"><TD><INPUT TYPE="TEXT" SIZE="30" NAME="change_col[1]" VALUE="<?=$change_col[1]?>" maxlength="<?=$gfield[$gtabid][size][$fieldid]?>"></TD></TR>
<TR><TD COLSPAN="2">&nbsp;</TD></TR>
<TR ALIGN="LEFT"><TD><?=$lang[60]?></TD></TR>
<TR ALIGN="LEFT"><TD><INPUT TYPE="TEXT" SIZE="30" NAME="change_col[2]" VALUE="<?=$change_col[2]?>" maxlength="<?=$gfield[$gtabid][size][$fieldid]?>"></TD></TR>
<TR><TD COLSPAN="2">&nbsp;</TD></TR>
<TR ALIGN="LEFT"><TD COLSPAN="2"><INPUT TYPE="submit" VALUE="<?=$lang[1341]?>"></TD></TR>
<?# -------BOOLEAN---------
}elseif($gfield[$gtabid][field_type][$fieldid] == 10){?>
<TR ALIGN="LEFT"><TD><INPUT TYPE="RADIO" NAME="change_col[1]" VALUE="1" STYLE="background-color:transparent;border:none">&nbsp;&nbsp;</TD><TD><?=$lang[1330]?></TD></TR>
<TR ALIGN="LEFT"><TD><INPUT TYPE="RADIO" NAME="change_col[1]" VALUE="2" STYLE="background-color:transparent;border:none">&nbsp;&nbsp;</TD><TD><?=$lang[1331]?></TD></TR>
<TR ALIGN="LEFT"><TD><INPUT TYPE="RADIO" NAME="change_col[1]" VALUE="3" STYLE="background-color:transparent;border:none">&nbsp;&nbsp;</TD><TD><?=$lang[1331]?></TD></TR>
<TR><TD COLSPAN="2">&nbsp;</TD></TR>
<TR ALIGN="LEFT"><TD COLSPAN="2"><INPUT TYPE="submit" VALUE="<?=$lang[1341]?>"></TD></TR>
<?# -------Zahl---------
}elseif($gfield[$gtabid][field_type][$fieldid] == 5){?>
<TR ALIGN="LEFT"><TD><SELECT STYLE="width:200px" NAME="change_col[0]"><OPTION VALUE="1"><?=$lang[59]?><OPTION VALUE="2"><?=$lang[1333]?><OPTION VALUE="3"><?=$lang[1334]?></SELECT></TD></TR>
<TR ALIGN="LEFT"><TD><INPUT TYPE="TEXT" STYLE="width:200px" NAME="change_col[1]" VALUE="<?=$change_col[1]?>" maxlength="<?=$gfield[$gtabid][size][$fieldid]?>"></TD></TR>
<TR><TD COLSPAN="2">&nbsp;</TD></TR>
<TR ALIGN="LEFT"><TD><?=$lang[60]?></TD></TR>
<TR ALIGN="LEFT"><TD><SELECT STYLE="width:200px" NAME="change_col[3]"><OPTION VALUE="1"><?=$lang[1335]?><OPTION VALUE="2"><?=$lang[1336]?><OPTION VALUE="3"><?=$lang[1337]?></SELECT></TD></TR>
<TR ALIGN="LEFT"><TD><INPUT TYPE="TEXT" STYLE="width:200px" NAME="change_col[2]" VALUE="<?=$change_col[2]?>" maxlength="<?=$gfield[$gtabid][size][$fieldid]?>"></TD></TR>
<TR><TD COLSPAN="2">&nbsp;</TD></TR>
<TR ALIGN="LEFT"><TD COLSPAN="2"><INPUT TYPE="submit" VALUE="<?=$lang[1341]?>"></TD></TR>
<?# -------Datum---------
}elseif($gfield[$gtabid][field_type][$fieldid] == 2){?>
<TR ALIGN="LEFT"><TD><SELECT STYLE="width:200px" NAME="change_col[0]"><OPTION VALUE="1"><?=$lang[1338]?><OPTION VALUE="2"><?=$lang[1339]?><OPTION VALUE="3"><?=$lang[1340]?></SELECT></TD></TR>
<TR ALIGN="LEFT"><TD><INPUT TYPE="TEXT" STYLE="width:200px" NAME="change_col[1]" VALUE="<?=get_date($change_col[1],1)?>" maxlength="<?=$gfield[$gtabid][size][$fieldid]?>"></TD></TR>
<TR><TD COLSPAN="2">&nbsp;</TD></TR>
<TR ALIGN="LEFT"><TD><?=$lang[60]?></TD></TR>
<TR ALIGN="LEFT"><TD><SELECT STYLE="width:200px" NAME="change_col[3]"><OPTION VALUE="1"><?=$lang[1335]?><OPTION VALUE="2"><?=$lang[1336]?><OPTION VALUE="3"><?=$lang[1337]?></SELECT></TD></TR>
<TR ALIGN="LEFT"><TD><INPUT TYPE="TEXT" STYLE="width:200px" NAME="change_col[2]" VALUE="<?=get_date($change_col[2],1)?>" maxlength="<?=$gfield[$gtabid][size][$fieldid]?>"></TD></TR>
<TR><TD COLSPAN="2">&nbsp;</TD></TR>
<TR ALIGN="LEFT"><TD COLSPAN="2"><INPUT TYPE="submit" VALUE="<?=$lang[1341]?>"></TD></TR>
<?# -------SELECT---------  
}elseif($gfield[$gtabid][field_type][$fieldid] == 4){?>
<TR ALIGN="LEFT"><TD><?=$lang[59]?></TD></TR>
<TR ALIGN="LEFT"><TD><SELECT STYLE="width:200px" NAME="change_col[1]"><OPTION VALUE="">
<?
$sqlquery = "SELECT WERT,SORT FROM LMB_SELECT_W WHERE POOL = TAB_ID = ".$gfield[$gtabid][select_pool][$fieldid]." ORDER BY SORT";
$rs = odbc_exec($db,$sqlquery) or errorhandle(odbc_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
$bzm2 = 1;
while(odbc_fetch_row($rs, $bzm2) AND $bzm2 < 50) {
	echo "<OPTION VALUE=\"".odbc_result($rs, "WERT")."\">";
	echo odbc_result($rs, "WERT");
	$bzm2++;
}
?>
</SELECT></TD></TR>
<TR><TD COLSPAN="2">&nbsp;</TD></TR>
<TR ALIGN="LEFT"><TD><?=$lang[60]?></TD></TR>
<TR ALIGN="LEFT"><TD><SELECT STYLE="width:200px" NAME="change_col[2]"><OPTION VALUE="">
<?
$bzm2 = 1;
while(odbc_fetch_row($rs, $bzm2) AND $bzm2 < 50) {
	echo "<OPTION VALUE=\"".odbc_result($rs, "AUSWAHL")."\">";
	echo odbc_result($rs, "AUSWAHL");
	$bzm2++;
}
?>
</SELECT></TD></TR>
<TR><TD COLSPAN="2">&nbsp;</TD></TR>
<TR ALIGN="LEFT"><TD COLSPAN="2"><INPUT TYPE="submit" VALUE="<?=$lang[1341]?>"></TD></TR>
<?}?>

</TABLE>
</TD></TR></TABLE>
</FORM>





