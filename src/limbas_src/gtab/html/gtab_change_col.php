<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */




if($change_col){
	/* --- Transaktion START --------------------------------------------- */
	lmb_StartTransaction();
	
	# -------BOOLEAN---------
	if($gfield[$gtabid]['field_type'][$fieldid] == 10){
		if($change_col[1] == 1){
			$sqlquery = "SELECT ID,".$gfield[$gtabid]["field_name"][$fieldid]." FROM ".$gtab["table"][$gtabid];
			$rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
			if(!$rs) {$commit = 1;}
			while(lmbdb_fetch_row($rs)) {
				if(lmbdb_result($rs, $gfield[$gtabid]["field_name"][$fieldid])){
					$sqlquery1 = "UPDATE ".$gtab["table"][$gtabid]." SET ".$gfield[$gtabid]["field_name"][$fieldid]." = ".LMB_DBDEF_FALSE." WHERE ID = ".lmbdb_result($rs, "ID");
					$rs1 = lmbdb_exec($db,$sqlquery1) or errorhandle(lmbdb_errormsg($db),$sqlquery1,$action,__FILE__,__LINE__);
					if(!$rs1) {$commit = 1;}				
				}else{
					$sqlquery1 = "UPDATE ".$gtab["table"][$gtabid]." SET ".$gfield[$gtabid]["field_name"][$fieldid]." = ".LMB_DBDEF_TRUE." WHERE ID = ".lmbdb_result($rs, "ID");
					$rs1 = lmbdb_exec($db,$sqlquery1) or errorhandle(lmbdb_errormsg($db),$sqlquery1,$action,__FILE__,__LINE__);
					if(!$rs1) {$commit = 1;}				
				}
			}			
		}elseif($change_col[1] == 2){
			$sqlquery = "UPDATE ".$gtab["table"][$gtabid]." SET ".$gfield[$gtabid]["field_name"][$fieldid]." = ".LMB_DBDEF_TRUE;
			$rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
			if(!$rs) {$commit = 1;}
		}elseif($change_col[1] == 3){
			$sqlquery = "UPDATE ".$gtab["table"][$gtabid]." SET ".$gfield[$gtabid]["field_name"][$fieldid]." = ".LMB_DBDEF_FALSE;
			$rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
			if(!$rs) {$commit = 1;}
		}
	# -------Text---------
	}elseif($gfield[$gtabid]['field_type'][$fieldid] == 1){
		if($change_col[2]){
			if(preg_match("/".$gfield[$gtabid]["regel"][$fieldid]."/", $change_col[2])){
				$where = "WHERE ".$gfield[$gtabid]["field_name"][$fieldid]." LIKE '".lmb_substr(str_replace("'","''",trim($change_col[2])),0,$gfield[$gtabid]['size'][$fieldid])."'";
			}else{echo "<B>$lang[58]</B>";}
		}
		if($change_col[1]){
			if(preg_match("/".$gfield[$gtabid]["regel"][$fieldid]."/", $change_col[1])){
				$sqlquery = "UPDATE ".$gtab["table"][$gtabid]." SET ".$gfield[$gtabid]["field_name"][$fieldid]." = '".lmb_substr(str_replace("'","''",trim($change_col[1])),0,$gfield[$gtabid]['size'][$fieldid])."' ".$where;
			}else{echo "<B>$lang[58]</B>";}
		}else{
			$sqlquery = "UPDATE ".$gtab["table"][$gtabid]." SET ".$gfield[$gtabid]["field_name"][$fieldid]." = '' ".$where;	
		}	
		$rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
		if(!$rs) {$commit = 1;}
	# -------Zahl---------
	}elseif($gfield[$gtabid]['field_type'][$fieldid] == 5){
		if($change_col[2]){
			if(preg_match("/".$gfield[$gtabid]["regel"][$fieldid]."/", $change_col[2])){
				if($change_col[3] == 1){$change_col[3] = "=";}
				if($change_col[3] == 2){$change_col[3] = ">";}
				if($change_col[3] == 3){$change_col[3] = "<";}
				$where = "WHERE ".$gfield[$gtabid]["field_name"][$fieldid]." ".$change_col[3]." ".lmb_substr($change_col[2],0,$gfield[$gtabid]['size'][$fieldid]);
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
		$rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
		if(!$rs) {$commit = 1;}	
	# -------Datum---------
	}elseif($gfield[$gtabid]['field_type'][$fieldid] == 2){
		if($change_col[2]){
			if(preg_match("/".$gfield[$gtabid]["regel"][$fieldid]."/", $change_col[2])){
				$change_col[2] = convert_date($change_col[2]);
				if($change_col[3] == 1){$change_col[3] = "=";}
				if($change_col[3] == 2){$change_col[3] = ">";}
				if($change_col[3] == 3){$change_col[3] = "<";}
				$where = "WHERE ".$gfield[$gtabid]["field_name"][$fieldid]." ".$change_col[3]." '".lmb_substr($change_col[2],0,$gfield[$gtabid]['size'][$fieldid])."'";
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
		
		$rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
		if(!$rs) {$commit = 1;}
	# -------SELECT---------
	}elseif($gfield[$gtabid]['field_type'][$fieldid] == 4){
		if($change_col[2]){
			if(preg_match("/".$gfield[$gtabid]["regel"][$fieldid]."/", $change_col[2])){
				$where = "WHERE ".$gfield[$gtabid]["field_name"][$fieldid]." LIKE '".lmb_substr(str_replace("'","''",trim($change_col[2])),0,$gfield[$gtabid]['size'][$fieldid])."'";
			}else{echo "<B>$lang[58]</B>";}
		}
		if($change_col[1]){
			if(preg_match("/".$gfield[$gtabid]["regel"][$fieldid]."/", $change_col[1])){
				$sqlquery = "UPDATE ".$gtab["table"][$gtabid]." SET ".$gfield[$gtabid]["field_name"][$fieldid]." = '".lmb_substr(str_replace("'","''",trim($change_col[1])),0,$gfield[$gtabid]['size'][$fieldid])."' ".$where;
			}else{echo "<B>$lang[58]</B>";}
		}else{
			$sqlquery = "UPDATE ".$gtab["table"][$gtabid]." SET ".$gfield[$gtabid]["field_name"][$fieldid]." = '' ".$where;	
		}	
		$rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
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
<BR><B><?=$gfield[$gtabid]['spelling'][$fieldid]?></B>

<FORM ACTION="main.php" METHOD="post" NAME="form1">
<input type="hidden" name="action" value="gtab_change_col">
<input type="hidden" name="gtabid" value="<?= $gtabid ?>">
<input type="hidden" name="fieldid" value="<?= $fieldid ?>">

<TABLE BORDER="0" cellspacing="0" cellpadding="1">

<?php #-------Text---------
if($gfield[$gtabid]['field_type'][$fieldid] == 1){?>
<TR ALIGN="LEFT"><TD><?=$lang[59]?></TD></TR>
<TR ALIGN="LEFT"><TD><INPUT TYPE="TEXT" SIZE="30" NAME="change_col[1]" VALUE="<?=$change_col[1]?>" maxlength="<?=$gfield[$gtabid]['size'][$fieldid]?>"></TD></TR>
<TR><TD COLSPAN="2">&nbsp;</TD></TR>
<TR ALIGN="LEFT"><TD><?=$lang[60]?></TD></TR>
<TR ALIGN="LEFT"><TD><INPUT TYPE="TEXT" SIZE="30" NAME="change_col[2]" VALUE="<?=$change_col[2]?>" maxlength="<?=$gfield[$gtabid]['size'][$fieldid]?>"></TD></TR>
<TR><TD COLSPAN="2">&nbsp;</TD></TR>
<TR ALIGN="LEFT"><TD COLSPAN="2"><INPUT TYPE="submit" VALUE="<?=$lang[1341]?>"></TD></TR>
<?php #-------BOOLEAN---------
}elseif($gfield[$gtabid]['field_type'][$fieldid] == 10){?>
<TR ALIGN="LEFT"><TD><INPUT TYPE="RADIO" NAME="change_col[1]" VALUE="1" STYLE="background-color:transparent;border:none">&nbsp;&nbsp;</TD><TD><?=$lang[1330]?></TD></TR>
<TR ALIGN="LEFT"><TD><INPUT TYPE="RADIO" NAME="change_col[1]" VALUE="2" STYLE="background-color:transparent;border:none">&nbsp;&nbsp;</TD><TD><?=$lang[1331]?></TD></TR>
<TR ALIGN="LEFT"><TD><INPUT TYPE="RADIO" NAME="change_col[1]" VALUE="3" STYLE="background-color:transparent;border:none">&nbsp;&nbsp;</TD><TD><?=$lang[1331]?></TD></TR>
<TR><TD COLSPAN="2">&nbsp;</TD></TR>
<TR ALIGN="LEFT"><TD COLSPAN="2"><INPUT TYPE="submit" VALUE="<?=$lang[1341]?>"></TD></TR>
<?php #-------Zahl---------
}elseif($gfield[$gtabid]['field_type'][$fieldid] == 5){?>
<TR ALIGN="LEFT"><TD><SELECT STYLE="width:200px" NAME="change_col[0]"><OPTION VALUE="1"><?=$lang[59]?><OPTION VALUE="2"><?=$lang[1333]?><OPTION VALUE="3"><?=$lang[1334]?></SELECT></TD></TR>
<TR ALIGN="LEFT"><TD><INPUT TYPE="TEXT" STYLE="width:200px" NAME="change_col[1]" VALUE="<?=$change_col[1]?>" maxlength="<?=$gfield[$gtabid]['size'][$fieldid]?>"></TD></TR>
<TR><TD COLSPAN="2">&nbsp;</TD></TR>
<TR ALIGN="LEFT"><TD><?=$lang[60]?></TD></TR>
<TR ALIGN="LEFT"><TD><SELECT STYLE="width:200px" NAME="change_col[3]"><OPTION VALUE="1"><?=$lang[713]?><OPTION VALUE="2"><?=$lang[711]?><OPTION VALUE="3"><?=$lang[712]?></SELECT></TD></TR>
<TR ALIGN="LEFT"><TD><INPUT TYPE="TEXT" STYLE="width:200px" NAME="change_col[2]" VALUE="<?=$change_col[2]?>" maxlength="<?=$gfield[$gtabid]['size'][$fieldid]?>"></TD></TR>
<TR><TD COLSPAN="2">&nbsp;</TD></TR>
<TR ALIGN="LEFT"><TD COLSPAN="2"><INPUT TYPE="submit" VALUE="<?=$lang[1341]?>"></TD></TR>
<?php #-------Datum---------
}elseif($gfield[$gtabid]['field_type'][$fieldid] == 2){?>
<TR ALIGN="LEFT"><TD><SELECT STYLE="width:200px" NAME="change_col[0]"><OPTION VALUE="1"><?=$lang[1338]?><OPTION VALUE="2"><?=$lang[1339]?><OPTION VALUE="3"><?=$lang[1340]?></SELECT></TD></TR>
<TR ALIGN="LEFT"><TD><INPUT TYPE="TEXT" STYLE="width:200px" NAME="change_col[1]" VALUE="<?=get_date($change_col[1],1)?>" maxlength="<?=$gfield[$gtabid]['size'][$fieldid]?>"></TD></TR>
<TR><TD COLSPAN="2">&nbsp;</TD></TR>
<TR ALIGN="LEFT"><TD><?=$lang[60]?></TD></TR>
<TR ALIGN="LEFT"><TD><SELECT STYLE="width:200px" NAME="change_col[3]"><OPTION VALUE="1"><?=$lang[713]?><OPTION VALUE="2"><?=$lang[711]?><OPTION VALUE="3"><?=$lang[712]?></SELECT></TD></TR>
<TR ALIGN="LEFT"><TD><INPUT TYPE="TEXT" STYLE="width:200px" NAME="change_col[2]" VALUE="<?=get_date($change_col[2],1)?>" maxlength="<?=$gfield[$gtabid]['size'][$fieldid]?>"></TD></TR>
<TR><TD COLSPAN="2">&nbsp;</TD></TR>
<TR ALIGN="LEFT"><TD COLSPAN="2"><INPUT TYPE="submit" VALUE="<?=$lang[1341]?>"></TD></TR>
<?php #-------SELECT---------
}elseif($gfield[$gtabid]['field_type'][$fieldid] == 4){?>
<TR ALIGN="LEFT"><TD><?=$lang[59]?></TD></TR>
<TR ALIGN="LEFT"><TD><SELECT STYLE="width:200px" NAME="change_col[1]"><OPTION VALUE="">
<?php
$sqlquery = "SELECT WERT,SORT FROM LMB_SELECT_W WHERE POOL = TAB_ID = ".$gfield[$gtabid]['select_pool'][$fieldid]." ORDER BY SORT";
$rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
while(lmbdb_fetch_row($rs)) {
	echo "<OPTION VALUE=\"".lmbdb_result($rs, "WERT")."\">";
	echo lmbdb_result($rs, "WERT");
}
?>
</SELECT></TD></TR>
<TR><TD COLSPAN="2">&nbsp;</TD></TR>
<TR ALIGN="LEFT"><TD><?=$lang[60]?></TD></TR>
<TR ALIGN="LEFT"><TD><SELECT STYLE="width:200px" NAME="change_col[2]"><OPTION VALUE="">
<?php
while(lmbdb_fetch_row($rs)) {
	echo "<OPTION VALUE=\"".lmbdb_result($rs, "AUSWAHL")."\">";
	echo lmbdb_result($rs, "AUSWAHL");
}
?>
</SELECT></TD></TR>
<TR><TD COLSPAN="2">&nbsp;</TD></TR>
<TR ALIGN="LEFT"><TD COLSPAN="2"><INPUT TYPE="submit" VALUE="<?=$lang[1341]?>"></TD></TR>
<?php }?>

</TABLE>
</TD></TR></TABLE>
</FORM>





