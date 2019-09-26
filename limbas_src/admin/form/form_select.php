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
 * ID: 176
 */


function lmb_formCopy($ID,$form_name,$referenz_tab,$form_extension){
	
	global $gformlist;
	global $session;
	global $umgvar;
	global $db;
	global $DBA;

	if(!$form_name){$form_name = "new formular";}
	$form_name = str_replace("\"","",$form_name);
	
	$sqlquery = "SELECT FORM_TYP,REFERENZ_TAB,CSS,EXTENSION,DIMENSION FROM LMB_FORM_LIST WHERE ID = $ID";
	$rs = odbc_exec($db,$sqlquery) or errorhandle(odbc_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
	if(!$rs) {$commit = 1;}
	$form_typ = odbc_result($rs,"FORM_TYP");
	
	if(!$form_extension){
		$form_extension = odbc_result($rs,"EXTENSION");
	}
	if(!$referenz_tab){
		$referenz_tab = odbc_result($rs,"REFERENZ_TAB");
	}

	/* --- Next ID ---------------------------------------- */
	$new_formid = next_conf_id("LMB_FORM_LIST");
	/* --- Next ID ---------------------------------------- */
	$new_fieldkeyid = next_db_id('LMB_FORMS');
	
	/* --- Neues Formular anlegen---------------------------------------- */
	$sqlquery = "INSERT INTO LMB_FORM_LIST (ID,ERSTUSER,NAME,REFERENZ_TAB,FORM_TYP,CSS,EXTENSION,DIMENSION) VALUES(".parse_db_int($new_formid,5).",".$session["user_id"].",'".parse_db_string($form_name,160)."',".parse_db_int($referenz_tab,5).",".parse_db_int($form_typ,5).",'".parse_db_string(odbc_result($rs,"CSS"),120)."','".parse_db_string($form_extension,250)."','".parse_db_string(odbc_result($rs,"DIMENSION"),10)."')";
	$rs = odbc_exec($db,$sqlquery) or errorhandle(odbc_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);

	# Rechte
	$NEXTID = next_db_id("LMB_RULES_REPFORM");
	$sqlquery = "INSERT INTO LMB_RULES_REPFORM (ID,TYP,GROUP_ID,LMVIEW,REPFORM_ID) VALUES ($NEXTID,2,".$session["group_id"].",".LMB_DBDEF_TRUE.",$new_formid)";
	$rs = odbc_exec($db,$sqlquery) or errorhandle(odbc_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);

	$gformlist[$referenz_tab]["id"][$new_formid] = $new_formid;
	$gformlist[$referenz_tab]["name"][$new_formid] = $form_name;
	$gformlist[$referenz_tab]["typ"][$new_formid] = $form_typ;
	$gformlist[$referenz_tab]["redirect"][$new_formid] = "";
	$gformlist[$referenz_tab]["ref_tab"][$new_formid] = $referenz_tab;
	$gformlist[$referenz_tab]["erstuser"][$new_formid] = $session["user_id"];
	$gformlist[$referenz_tab]["extension"][$new_formid] = $form_extension;
	$_SESSION["gformlist"] = $gformlist;
	
	/* --- LMB_FORMS ---------------------------------------- */ 
	$domain_columns = dbf_5(array($DBA["DBSCHEMA"],"LMB_FORMS"));
	$fieldlist = implode(', ', $domain_columns["columnname"]);
	
	$sqlquery = "SELECT $fieldlist FROM LMB_FORMS WHERE FORM_ID = ".$ID;
	$rs = odbc_exec($db,$sqlquery) or errorhandle(odbc_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
	if(!$rs) {$commit = 1;}
	while(odbc_fetch_row($rs)) {
		
		$qu_field = array();
		$qu_value = array();
		$secname = null;
		$bzm = 0;
		foreach ($domain_columns["columnname"] as $key1 => $fieldname){

			$value1 = odbc_result($rs, $fieldname);
			if($value1 == ''){continue;}

			$qu_field[] = $fieldname;
			
			# Key-ID
			if(lmb_strtoupper($fieldname) == "ID"){
				$qu_value[] = $new_fieldkeyid;
			# FORM_ID
			}elseif(lmb_strtoupper($fieldname) == "FORM_ID"){
				$qu_value[] = $new_formid;

			/* ---- upload ------ */
			}elseif(lmb_strtoupper($fieldname) == 'TYP' AND $value1 == 'bild'){
				$tab_size  = odbc_result($rs, 'TAB_SIZE');
				$ext = explode(".",$tab_size);
				$secname = lmb_substr(md5($new_fieldkeyid.date("U")),0,12).".".$ext[1];
				$qu_value[] = lmb_parseImport($value1,$domain_columns,$key1);

				if(file_exists($umgvar['path']."/UPLOAD/form/".$tab_size)){
					copy($umgvar['path']."/UPLOAD/form/".$tab_size, $umgvar['path']."/UPLOAD/form/".$secname);
					if(file_exists($umgvar['path']."/TEMP/thumpnails/form/".$tab_size)){
						copy($umgvar['path']."/TEMP/thumpnails/form/".$tab_size, $umgvar['path']."/TEMP/thumpnails/form/".$secname);
					}
				}else{unset($secname);}

			}elseif(lmb_strtoupper($fieldname) == 'TAB_SIZE'){
				$pic_key = $bzm;
				$qu_value[] = lmb_parseImport($value1,$domain_columns,$key1);
			}else{
				$qu_value[] = lmb_parseImport($value1,$domain_columns,$key1);
			}
			
			$bzm++;

		}

		
		# bild
		if($pic_key AND $secname){$qu_value[$pic_key] = "'".$secname."'";}

		$sqlquery1 = "INSERT INTO LMB_FORMS (".implode(",",$qu_field).") VALUES (".implode(",",$qu_value).")";
		$rs1 = odbc_exec($db,$sqlquery1) or errorhandle(odbc_errormsg($db),$sqlquery1,$action,__FILE__,__LINE__);
		if(!$rs1) {$commit = 1;}
		
		$new_fieldkeyid++;
	}

}
	

#---------------------------- copy formular -------------------------
if($formcopy){
	
	lmb_formCopy($formcopy,$form_name,$referenz_tab,$form_extension);
	
#---------------------------- new formular -------------------------
}elseif($new_form){
	
	$referenz_tab = $tabid;

	if(!$report_name){
		$report_name = "new formular";
	}

	/* --- Name umbenennen ------------- */
	$form_name = str_replace("\"","",$form_name);

	$posx = 50;
	$posy = 50;
	$height = 16;
	$width = 200;
	
	if(!$form_name){
		lmb_alert("need form name!");
		$fail = 1;
	}
	
	if($referenz_tab){
		$sqlquery = "SELECT TAB_ID FROM LMB_CONF_TABLES WHERE TAB_ID = ".parse_db_int($referenz_tab);
		$rs = odbc_exec($db,$sqlquery) or errorhandle(odbc_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
		if(!odbc_result($rs,"TAB_ID")){
			lmb_alert("referenz table not found!");
			$fail = 1;
		}
	}
	
	if(!$fail){
		/* --- NEXT ID ---------------------------------------- */
		$form_id = next_conf_id("LMB_FORM_LIST");
	
		/* --- Neues Formular anlegen---------------------------------------- */
		$sqlquery = "INSERT INTO LMB_FORM_LIST (ID,ERSTUSER,NAME,REFERENZ_TAB,FORM_TYP,EXTENSION) VALUES($form_id,{$session['user_id']},'".parse_db_string($form_name,160)."',".parse_db_int($referenz_tab).",".parse_db_int($form_typ).",'".parse_db_string($form_extension,250)."')";
		$rs = odbc_exec($db,$sqlquery) or errorhandle(odbc_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
	
		# Rechte
		$NEXTID = next_db_id("LMB_RULES_REPFORM");
		$sqlquery = "INSERT INTO LMB_RULES_REPFORM (ID,TYP,GROUP_ID,LMVIEW,REPFORM_ID) VALUES ($NEXTID,2,".$session["group_id"].",".LMB_DBDEF_TRUE.",".parse_db_int($form_id).")";
		$rs = odbc_exec($db,$sqlquery) or errorhandle(odbc_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
	
		$gformlist[$referenz_tab]["id"][$form_id] = $form_id;
		$gformlist[$referenz_tab]["name"][$form_id] = $form_name;
		$gformlist[$referenz_tab]["typ"][$form_id] = $form_typ;
		$gformlist[$referenz_tab]["redirect"][$form_id] = "";
		$gformlist[$referenz_tab]["ref_tab"][$form_id] = $referenz_tab;
		$gformlist[$referenz_tab]["erstuser"][$form_id] = $session["user_id"];
		$gformlist[$referenz_tab]["extension"][$form_id] = $form_extension;
		
		if(!$form_extension AND $referenz_tab){
	
			/* --- Next ID ---------------------------------------- */
			$sqlquery = "SELECT MAX(KEYID) AS NEXTID FROM LMB_FORMS WHERE FORM_ID = $form_id";
			$rs = odbc_exec($db,$sqlquery) or errorhandle(odbc_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
			$NEXTID = odbc_result($rs,"NEXTID") + 1;
			$NEXTKEYID = next_db_id("LMB_FORMS","KEYID");
	
			/* --- Einspaltig --------------------------------------- */
			if($form_typ == "2"){
				$style = "VERDANA;normal;;11;normal;0;0;none;none;#000000;;;justify;;none;none;none;none;;none;;;";
				$bzm1 = 0;
				while($gfield[$gtab["tab_id"][$gtabid]]["field_id"][$bzm1]) {
	
					/* --- Feldbezeichnung eintragen ---------------------------------------- */;
					$height = 16;
					$width = 200;
					$sqlquery = "INSERT INTO LMB_FORMS (KEYID,KEYID,form_id,ERSTUSER,POSX,POSY,HEIGHT,WIDTH,TYP,STYLE,INHALT) VALUES ($NEXTKEYID,$NEXTID,$form_id,{$session['user_id']},$posx,$posy,$height,$width,'text','$style','".$gfield[$gtab['tab_id'][$gtabid]]['spelling'][$bzm1].":')";
					$rs = odbc_exec($db,$sqlquery) or errorhandle(odbc_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
					if(!$rs) {$commit = 1;}
					$NEXTID++;
					$NEXTKEYID++;
					/* --- EingabeFeld eintragen ---------------------------------------- */
					if($gfield[$gtab['tab_id'][$gtabid]]['data_type'][$bzm1] == 24 OR $gfield[$gtab['tab_id'][$gtabid]]['data_type'][$bzm1] == 27){
						$height = 85;
						$width = 430;
					}else{
						$height = 16;
						$width = 200;
					}
	
					$sqlquery = "INSERT INTO LMB_FORMS (KEYID,KEYID,form_id,ERSTUSER,POSX,POSY,HEIGHT,WIDTH,TYP,STYLE,TAB_GROUP,TAB_ID,FIELD_ID,INHALT) VALUES ($NEXTKEYID,$NEXTID,$form_id,{$session['user_id']},".($posx + 210).",$posy,$height,$width,'dbdat','$style',".$gtab['tab_group'][$bzm].",".$gtab['tab_id'][$bzm].",".$gfield[$gtab['tab_id'][$gtabid]]['field_id'][$bzm1].",'".$gfield[$gtab['tab_id'][$gtabid]]['field_name'][$bzm1]."')";
					$rs = odbc_exec($db,$sqlquery) or errorhandle(odbc_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
					if(!$rs) {$commit = 1;}
					$NEXTID++;
					$NEXTKEYID++;
					if($gfield[$gtab['tab_id'][$gtabid]]['data_type'][$bzm1] == 24 OR $gfield[$gtab['tab_id'][$gtabid]]['data_type'][$bzm1] == 27){
						$posy = $posy + 109;
					}else{
						$posy = $posy + 24;
					}
					$bzm1++;
				}
	
				/* --- Tabellarisch --------------------------------------- */
			}elseif($form_typ == "3"){
				$posx = 10;
				$bzm = 0;
				$style = "VERDANA;normal;;11;normal;0;0;none;none;#000000;;;justify;;none;none;none;none;;none;;;";
				$bzm1 = 0;
				while($gfield[$gtab['tab_id'][$vgtabid]]['field_id'][$bzm1]) {
					$posy = 10;
					$height = 14;
					$width = lmb_strlen($gfield[$gtab['tab_id'][$vgtabid]]['field_name'][$bzm1]) * 8 + 40;
					
					/* --- EingabeFeld eintragen ---------------------------------------- */
					$sqlquery = "INSERT INTO LMB_FORMS (KEYID,KEYID,FORM_ID,ERSTUSER,TYP,POSX,POSY,HEIGHT,WIDTH,STYLE,INHALT,FIELD_ID,FORM_FRAME) VALUES ($NEXTKEYID,$NEXTID,$form_id,{$session['user_id']},'dbinput',$posx,$posy,$height,$width,'$style','".$gfield[$gtab['tab_id'][$vgtabid]]['spelling'][$bzm1]."',".$gfield[$gtab['tab_id'][$vgtabid]]['field_id'][$bzm1].",1)";
					$rs = odbc_exec($db,$sqlquery) or errorhandle(odbc_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
					if(!$rs) {$commit = 1;}
					$NEXTID++;
					$NEXTKEYID++;
					/* --- Feldbezeichnung eintragen ---------------------------------------- */
					$sqlquery = "INSERT INTO LMB_FORMS (KEYID,KEYID,FORM_ID,ERSTUSER,TYP,POSX,POSY,HEIGHT,WIDTH,STYLE,INHALT,FIELD_ID,FORM_FRAME) VALUES ($NEXTKEYID,$NEXTID,$form_id,{$session['user_id']},'dbdesc',$posx,".($posy + 24).",$height,$width,'$style','".$gfield[$gtab['tab_id'][$vgtabid]]['spelling'][$bzm1]."',".$gfield[$gtab['tab_id'][$vgtabid]]['field_id'][$bzm1].",1)";
					$rs = odbc_exec($db,$sqlquery) or errorhandle(odbc_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
					if(!$rs) {$commit = 1;}
					$NEXTID++;
					$NEXTKEYID++;
					$posy = 0;
					/* --- Feldinhalt eintragen ---------------------------------------- */
					$sqlquery = "INSERT INTO LMB_FORMS (KEYID,KEYID,FORM_ID,ERSTUSER,TYP,POSX,POSY,HEIGHT,WIDTH,STYLE,TAB_GROUP,TAB_ID,FIELD_ID,INHALT,FORM_FRAME) VALUES ($NEXTKEYID,$NEXTID,$form_id,{$session['user_id']},'dbdat',$posx,$posy,$height,$width,'$style',".$gtab['tab_group'][$bzm].",".$gtab['tab_id'][$bzm].",".$gfield[$gtab['tab_id'][$vgtabid]]['field_id'][$bzm1].",'".$gfield[$gtab['tab_id'][$vgtabid]]['field_name'][$bzm1]."',2)";
					$rs = odbc_exec($db,$sqlquery) or errorhandle(odbc_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
					if(!$rs) {$commit = 1;}
					$NEXTID++;
					$NEXTKEYID++;
					$posx = $posx + $width + 10;
	
					$bzm1++;
				}
			}
		}
	}
	$_SESSION["gformlist"] = $gformlist;
}















/*----------------- Formular umbenennen -------------------*/
if($rename_id) {
	$sqlquery = "SELECT REFERENZ_TAB FROM LMB_FORM_LIST WHERE ID = $rename_id";
	$rs = odbc_exec($db,$sqlquery) or errorhandle(odbc_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
	$reftab = odbc_result($rs, "REFERENZ_TAB");
	if($gformlist[$reftab]["id"][$rename_id]){
		$sqlquery = "UPDATE LMB_FORM_LIST SET NAME = '".parse_db_string(${"form_name_".$rename_id},160)."' WHERE ID = $rename_id";
		$rs = odbc_exec($db,$sqlquery) or errorhandle(odbc_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
		if(!$rs) {$commit = 1;}
		$gformlist[$reftab]["name"][$rename_id] = ${"form_name_".$rename_id};
		$_SESSION["gformlist"] = $gformlist;
	}
}

/*----------------- Formular löschen -------------------*/
if($del AND $form_id){

	$sqlquery = "SELECT ID,REFERENZ_TAB,EXTENSION FROM LMB_FORM_LIST WHERE ID = $form_id";
	$rs = odbc_exec($db,$sqlquery) or errorhandle(odbc_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
	$reftab = odbc_result($rs, "REFERENZ_TAB");
	$exten = odbc_result($rs, "EXTENSION");
	
	if($gformlist[$reftab]["id"][$form_id] OR $gformlist['']["id"][$form_id]){

		$sqlquery = "SELECT TAB_SIZE FROM LMB_FORMS WHERE FORM_ID = $form_id AND TYP = 'bild'";
		$rs = odbc_exec($db,$sqlquery) or errorhandle(odbc_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
		while(odbc_fetch_row($rs)) {
			if(file_exists($umgvar['uploadpfad']."form/".odbc_result($rs, "TAB_SIZE"))){
				unlink($umgvar['uploadpfad']."form/".odbc_result($rs, "TAB_SIZE"));
			}
			if(file_exists($umgvar['pfad']."/TEMP/thumpnails/form/".odbc_result($rs, "TAB_SIZE"))){
				unlink($umgvar['pfad']."/TEMP/thumpnails/form/".odbc_result($rs, "TAB_SIZE"));
			}
		}
		
		$sqlquery = "DELETE FROM LMB_FORMS WHERE FORM_ID = $form_id";
		$rs = odbc_exec($db,$sqlquery) or errorhandle(odbc_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
		$sqlquery = "DELETE FROM LMB_FORM_LIST WHERE ID = $form_id";
		$rs = odbc_exec($db,$sqlquery) or errorhandle(odbc_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
		
	}
	unset($gformlist[$reftab]["id"][$form_id]);
	unset($gformlist['']["id"][$form_id]);
	unset($gformlist[$reftab]["name"][$form_id]);

	$_SESSION["gformlist"] = $gformlist;
}

?>

<Script language="JavaScript">

function change_name(val,name,ID) {
	document.location.href="main_admin.php?action=setup_form_select&rename_id="+ID+"&"+name+"="+val;
}

function form_delete(ID){
	var del = confirm('<?=$lang[2279]?>');
	if(del){
		document.location.href="main_admin.php?action=setup_form_select&del=1&form_id="+ID;
	}
}

</Script>

<FORM ACTION="main_admin.php" METHOD="post" name="form1">
<input type="hidden" name="action" value="setup_form_select">
<input type="hidden" name="form_id" value="<?=$form_id?>">
<input type="hidden" name="form_name" value="<?=$form_name?>">
<input type="hidden" name="referenz_tab" value="<?=$referenz_tab?>">
<input type="hidden" name="rename_id">
<input type="hidden" name="new_form" value="1">


<div class="lmbPositionContainerMain">


<TABLE class="tabfringe" BORDER="0" width="700" cellspacing="1" cellpadding="2">
<TR class="tabHeader">
<TD class="tabHeaderItem">ID</TD>
<TD class="tabHeaderItem"></TD>
<TD class="tabHeaderItem"><?=$lang[160]?></TD>
<TD class="tabHeaderItem"><?=$lang[1179]?></TD>
<TD class="tabHeaderItem"><?=$lang[925]?></TD>
<TD class="tabHeaderItem"><?=$lang[1162]?></TD>
<TD class="tabHeaderItem"><?=$lang[1986]?></TD>
<TD class="tabHeaderItem"><?=$lang[1638]?></TD>
</TR>

<?php
if($gformlist){
	foreach ($gformlist as $key => $value){
		if($gtab["table"][$key]){
			$cat = $gtab["desc"][$key];
		}else{
			$cat = $lang[1986];
		}
		echo "<tr class=\"tabSubHeader\"><td class=\"tabSubHeaderItem\" colspan=\"8\">" . $cat . "</td></tr>";
		if($value["id"]){
			foreach ($value["id"] as $key2 => $value2){
				if($value2){
					if($value["typ"][$key2] == 1){$typ = $lang[1183];}else{$typ = $lang[1184];}
					echo "<TR class=\"tabBody\">";
					echo "<TD>&nbsp;".$value["id"][$key2]."&nbsp;</TD>";
					echo "<TD>";
					if(!$value["extension"][$key2]){echo "<A HREF=\"main_admin.php?&action=setup_form_frameset&form_typ=".$value["typ"][$key2]."&form_id=".$value["id"][$key2]."&referenz_tab=".$value["ref_tab"][$key2]."\"><i class=\"lmb-icon lmb-pencil\" BORDER=\"0\" style=\"cursor:pointer\"></i></A>";}
					echo "</TD>";
					echo "<TD ALIGN=\"CENTER\"><i OnClick=\"form_delete('".$value["id"][$key2]."')\" class=\"lmb-icon lmb-trash\" BORDER=\"0\" style=\"cursor:pointer\"></i></TD>";
					echo "<TD><INPUT TYPE=\"TEXT\" NAME=\"form_name_".$value["id"][$key2]."\" VALUE=\"".$value["name"][$key2]."\" STYLE=\"width:160px;\" OnChange=\"change_name(this.value,this.name,'".$value["id"][$key2]."');\"></TD>";
					
					echo "<TD>".$typ."</TD>";
					
					echo "<TD>".$gtab["desc"][$value["ref_tab"][$key2]]."</TD>";
					echo "<TD>".$value["extension"][$key2]."</TD>";
					echo "<TD>".$userdat["bezeichnung"][$value["erstuser"][$key2]]."</TD>";
					echo "</TR>";
				}
			}
		}

	}
}
?>
<TR><TD COLSPAN="7" class="tabFooter"></TD></TR>
</TABLE>

<br>

<TABLE class="tabfringe" BORDER="0" width="700" cellspacing="1" cellpadding="2">
<TR class="tabHeader"><TD class="tabHeaderItem"><?=$lang[4]?></TD><TD class="tabHeaderItem"><?=$lang[164]?></TD><TD class="tabHeaderItem"><?=$lang[1464]?></TD><TD class="tabHeaderItem"><?=$lang[925]?></TD><TD class="tabHeaderItem"><?=$lang[1986]?></TD><TD class="tabHeaderItem"></TD></TR>

<TR class="tabBody"><TD><INPUT TYPE="TEXT" NAME ="form_name" SIZE="20"></TD>
<TD><SELECT NAME="tabid"><OPTION>
<?php
foreach ($tabgroup["id"] as $key0 => $value0) {
    echo '<optgroup label="' . $tabgroup["name"][$key0] . '">';
	foreach ($gtab["tab_id"] as $key => $value) {
		if($gtab["tab_group"][$key] == $value0){
			echo "<OPTION VALUE=\"".$value."\">".$gtab["desc"][$key];
		}
	}
	echo '</optgroup>';
}
?>
</SELECT></TD>

<TD><SELECT NAME="formcopy"><OPTION VALUE="0">
<?php
if($gformlist){
foreach ($gformlist as $key => $value){
	if($gtab["table"][$key]){
	if($value["id"]){
	foreach ($value["id"] as $key2 => $value2){
		if($value2){
			echo "<OPTION VALUE=\"".$value2."\">".$value["name"][$key2];
		}
	}}}
}}
?>
</SELECT></TD>


<TD><SELECT NAME="form_typ">
<OPTION VALUE="1"><?=$lang[1183]?>
<OPTION VALUE="2"><?=$lang[1184]?>
</SELECT></TD>

<TD>
<?php
# Extension Files
$extfiles = read_dir($umgvar["pfad"]."/EXTENSIONS",1);
echo "<SELECT ID=\"zmenufiles\" NAME=\"form_extension\" style=\"width:100px;\"><OPTION>";
foreach ($extfiles["name"] as $key1 => $filename){
	if($extfiles["typ"][$key1] == "file" AND $extfiles["ext"][$key1] == "ext"){
		$path = lmb_substr($extfiles["path"][$key1],lmb_strlen($umgvar["pfad"]),100);
		if($result_links['ext'][$bzm] == $path.$filename){$selected = "SELECTED";}else{$selected = "";}
		echo "<OPTION VALUE=\"".$path.$filename."\" $selected>".str_replace("/EXTENSIONS/","",$path).$filename;
	}
}
echo "</SELECT>";
?>
</TD>


<TD><INPUT TYPE="SUBMIT" VALUE="<?=$lang[1186]?>" NAME="new"></TD></TR>
<TR><TD COLSPAN="7" class="tabFooter"></TD></TR>

</TABLE>

</div>
</FORM>




