<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
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
	$rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
	if(!$rs) {$commit = 1;}
	$form_typ = lmbdb_result($rs,"FORM_TYP");
	
	if(!$form_extension){
		$form_extension = lmbdb_result($rs,"EXTENSION");
	}
	if(!$referenz_tab){
		$referenz_tab = lmbdb_result($rs,"REFERENZ_TAB");
	}

	/* --- Next ID ---------------------------------------- */
	$new_formid = next_conf_id("LMB_FORM_LIST");
	/* --- Next ID ---------------------------------------- */
	$new_fieldkeyid = next_db_id('LMB_FORMS');
	
	/* --- Neues Formular anlegen---------------------------------------- */
	$sqlquery = "INSERT INTO LMB_FORM_LIST (ID,ERSTUSER,NAME,REFERENZ_TAB,FORM_TYP,CSS,EXTENSION,DIMENSION) VALUES(".parse_db_int($new_formid,5).",".$session["user_id"].",'".parse_db_string($form_name,160)."',".parse_db_int($referenz_tab,5).",".parse_db_int($form_typ,5).",'".parse_db_string(lmbdb_result($rs,"CSS"),120)."','".parse_db_string($form_extension,250)."','".parse_db_string(lmbdb_result($rs,"DIMENSION"),10)."')";
	$rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);

	# Rechte
	$NEXTID = next_db_id("LMB_RULES_REPFORM");
	$sqlquery = "INSERT INTO LMB_RULES_REPFORM (ID,TYP,GROUP_ID,LMVIEW,REPFORM_ID) VALUES ($NEXTID,2,".$session["group_id"].",".LMB_DBDEF_TRUE.",$new_formid)";
	$rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);

	$gformlist[$referenz_tab]["id"][$new_formid] = $new_formid;
	$gformlist[$referenz_tab]["name"][$new_formid] = $form_name;
	$gformlist[$referenz_tab]["typ"][$new_formid] = $form_typ;
	$gformlist[$referenz_tab]["redirect"][$new_formid] = "";
	$gformlist[$referenz_tab]["ref_tab"][$new_formid] = $referenz_tab;
	$gformlist[$referenz_tab]["erstuser"][$new_formid] = $session["user_id"];
	$gformlist[$referenz_tab]["extension"][$new_formid] = $form_extension;
	$gformlist["argresult_id"][$new_formid] = $referenz_tab;
	$_SESSION["gformlist"] = $gformlist;
	
	/* --- LMB_FORMS ---------------------------------------- */ 
	$domain_columns = dbf_5(array($DBA["DBSCHEMA"],"LMB_FORMS"));
	$fieldlist = implode(', ', $domain_columns["columnname"]);
	
	$sqlquery = "SELECT $fieldlist FROM LMB_FORMS WHERE FORM_ID = ".$ID;
	$rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
	if(!$rs) {$commit = 1;}
	while(lmbdb_fetch_row($rs)) {
		
		$qu_field = array();
		$qu_value = array();
		$secname = null;
		$bzm = 0;
		foreach ($domain_columns["columnname"] as $key1 => $fieldname){

			$value1 = lmbdb_result($rs, $fieldname);
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
				$tab_size  = lmbdb_result($rs, 'TAB_SIZE');
				$ext = explode(".",$tab_size);
				$secname = lmb_substr(md5($new_fieldkeyid.date("U")),0,12).".".$ext[1];
				$qu_value[] = lmb_parseImport($value1,$domain_columns,$key1);

				if(file_exists(UPLOADPATH . 'form/'.$tab_size)){
					copy(UPLOADPATH. 'form/'.$tab_size, UPLOADPATH . 'form/'.$secname);
					if(file_exists(TEMPPATH . 'thumpnails/form/'.$tab_size)){
						copy(TEMPPATH . 'thumpnails/form/'.$tab_size, TEMPPATH . 'thumpnails/form/'.$secname);
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
		$rs1 = lmbdb_exec($db,$sqlquery1) or errorhandle(lmbdb_errormsg($db),$sqlquery1,$action,__FILE__,__LINE__);
		if(!$rs1) {$commit = 1;}
		
		$new_fieldkeyid++;
	}

}
	

#---------------------------- copy formular -------------------------
if($formcopy){
	
	lmb_formCopy($formcopy,$form_name,$referenz_tab,$form_extension);
	
#---------------------------- new formular -------------------------
}
elseif($new_form) {
	
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
		$rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
		if(!lmbdb_result($rs,"TAB_ID")){
			lmb_alert("referenz table not found!");
			$fail = 1;
		}
	}
	
	if(!$fail){
		/* --- NEXT ID ---------------------------------------- */
		$form_id = next_conf_id("LMB_FORM_LIST");
	
		/* --- Neues Formular anlegen---------------------------------------- */
		$sqlquery = "INSERT INTO LMB_FORM_LIST (ID,ERSTUSER,NAME,REFERENZ_TAB,FORM_TYP,EXTENSION) VALUES($form_id,{$session['user_id']},'".parse_db_string($form_name,160)."',".parse_db_int($referenz_tab).",".parse_db_int($form_typ).",'".parse_db_string($form_extension,250)."')";
		$rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
	
		# Rechte
		$NEXTID = next_db_id("LMB_RULES_REPFORM");
		$sqlquery = "INSERT INTO LMB_RULES_REPFORM (ID,TYP,GROUP_ID,LMVIEW,REPFORM_ID) VALUES ($NEXTID,2,".$session["group_id"].",".LMB_DBDEF_TRUE.",".parse_db_int($form_id).")";
		$rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);

		
		if(!$form_extension AND $referenz_tab){
	
			/* --- Next ID ---------------------------------------- */
			$sqlquery = "SELECT MAX(KEYID) AS NEXTID FROM LMB_FORMS WHERE FORM_ID = $form_id";
			$rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
			$NEXTID = lmbdb_result($rs,"NEXTID") + 1;
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
					$rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
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
					$rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
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
					$rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
					if(!$rs) {$commit = 1;}
					$NEXTID++;
					$NEXTKEYID++;
					/* --- Feldbezeichnung eintragen ---------------------------------------- */
					$sqlquery = "INSERT INTO LMB_FORMS (KEYID,KEYID,FORM_ID,ERSTUSER,TYP,POSX,POSY,HEIGHT,WIDTH,STYLE,INHALT,FIELD_ID,FORM_FRAME) VALUES ($NEXTKEYID,$NEXTID,$form_id,{$session['user_id']},'dbdesc',$posx,".($posy + 24).",$height,$width,'$style','".$gfield[$gtab['tab_id'][$vgtabid]]['spelling'][$bzm1]."',".$gfield[$gtab['tab_id'][$vgtabid]]['field_id'][$bzm1].",1)";
					$rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
					if(!$rs) {$commit = 1;}
					$NEXTID++;
					$NEXTKEYID++;
					$posy = 0;
					/* --- Feldinhalt eintragen ---------------------------------------- */
					$sqlquery = "INSERT INTO LMB_FORMS (KEYID,KEYID,FORM_ID,ERSTUSER,TYP,POSX,POSY,HEIGHT,WIDTH,STYLE,TAB_GROUP,TAB_ID,FIELD_ID,INHALT,FORM_FRAME) VALUES ($NEXTKEYID,$NEXTID,$form_id,{$session['user_id']},'dbdat',$posx,$posy,$height,$width,'$style',".$gtab['tab_group'][$bzm].",".$gtab['tab_id'][$bzm].",".$gfield[$gtab['tab_id'][$vgtabid]]['field_id'][$bzm1].",'".$gfield[$gtab['tab_id'][$vgtabid]]['field_name'][$bzm1]."',2)";
					$rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
					if(!$rs) {$commit = 1;}
					$NEXTID++;
					$NEXTKEYID++;
					$posx = $posx + $width + 10;
	
					$bzm1++;
				}
			}
		}
	}

	$gformlist = resultformlist_();
	$_SESSION["gformlist"] = $gformlist;

}


/*----------------- Formular editieren -------------------*/
if($edit_id and is_numeric($edit_id)) {
	$sqlquery = "SELECT REFERENZ_TAB FROM LMB_FORM_LIST WHERE ID = $edit_id";
	$rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
	$reftab = lmbdb_result($rs, "REFERENZ_TAB");
	if($gformlist[$reftab]["id"][$edit_id]){
        if($typ == 'name'){
            $sqlquery = "UPDATE LMB_FORM_LIST SET NAME = '".parse_db_string($edit_value,160)."' WHERE ID = $edit_id";
            $gformlist[$reftab]["name"][$edit_id] = $edit_value;
            $_SESSION['gformlist'][$reftab]["name"][$edit_id] = $edit_value;
        }elseif($typ == 'custmenu') {
            $sqlquery = "UPDATE LMB_FORM_LIST SET CUSTMENU = ".parse_db_int($edit_value)." WHERE ID = $edit_id";
            $gformlist[$reftab]["custmenu"][$edit_id] = $edit_value;
            $_SESSION['gformlist'][$reftab]["custmenu"][$edit_id] = $edit_value;
        }
		$rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
		if(!$rs) {$commit = 1;}

	}
}

/*----------------- Formular löschen -------------------*/
if($del AND $form_id){

	$sqlquery = "SELECT ID,REFERENZ_TAB,EXTENSION FROM LMB_FORM_LIST WHERE ID = $form_id";
	$rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
	$reftab = lmbdb_result($rs, "REFERENZ_TAB");
	$exten = lmbdb_result($rs, "EXTENSION");
	
	if($gformlist[$reftab]["id"][$form_id] OR $gformlist['']["id"][$form_id]){

		$sqlquery = "SELECT TAB_SIZE FROM LMB_FORMS WHERE FORM_ID = $form_id AND TYP = 'bild'";
		$rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
		while(lmbdb_fetch_row($rs)) {
			if(file_exists(UPLOADPATH.'form/'.lmbdb_result($rs, "TAB_SIZE"))){
				unlink(UPLOADPATH.'form/'.lmbdb_result($rs, "TAB_SIZE"));
			}
			if(file_exists(TEMPPATH . 'thumpnails/form/'.lmbdb_result($rs, "TAB_SIZE"))){
				unlink(TEMPPATH . 'thumpnails/form/'.lmbdb_result($rs, "TAB_SIZE"));
			}
		}
		
		$sqlquery = "DELETE FROM LMB_FORMS WHERE FORM_ID = $form_id";
		$rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
		$sqlquery = "DELETE FROM LMB_FORM_LIST WHERE ID = $form_id";
		$rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
		
	}

	$gformlist = resultformlist_();
	$_SESSION["gformlist"] = $gformlist;

}

#----------------- Form list -------------------
function resultformlist_(){
	global $db;
	global $session;

	$sqlquery = "SELECT DISTINCT LMB_FORM_LIST.DIMENSION,LMB_FORM_LIST.EXTENSION,LMB_FORM_LIST.NAME,LMB_FORM_LIST.CSS,LMB_FORM_LIST.ID,LMB_FORM_LIST.REDIRECT,LMB_FORM_LIST.FORM_TYP,LMB_FORM_LIST.REFERENZ_TAB,LMB_FORM_LIST.ERSTUSER,LMB_RULES_REPFORM.HIDDEN
	FROM LMB_FORM_LIST,LMB_RULES_REPFORM
	WHERE LMB_RULES_REPFORM.REPFORM_ID = LMB_FORM_LIST.ID
	AND LMB_RULES_REPFORM.TYP = 2
	AND (LMB_RULES_REPFORM.GROUP_ID IN (".implode(",",$session["subgroup"])."))
	ORDER BY REFERENZ_TAB,NAME";
	$rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
	if(!$rs) {$commit = 1;}
	while(lmbdb_fetch_row($rs)) {
		$key = lmbdb_result($rs, "ID");
		$gtabid = lmbdb_result($rs, "REFERENZ_TAB");
		if(!$gtabid){$gtabid = 0;}
		$gformlist["argresult_id"][$key] = $gtabid;
		$gformlist[$gtabid]["id"][$key] = $key;
		$gformlist[$gtabid]["name"][$key] = lmbdb_result($rs, "NAME");
		$gformlist[$gtabid]["typ"][$key] = lmbdb_result($rs, "FORM_TYP");
		$gformlist[$gtabid]["ref_tab"][$key] = $gtabid;
		$gformlist[$gtabid]["extension"][$key] = trim(lmbdb_result($rs, "EXTENSION"),"/");
	}

	return $gformlist;
}

?>

<Script language="JavaScript">

function change_settings(val,ID,typ) {
	document.location.href="main_admin.php?action=setup_form_select&edit_id="+ID+"&edit_value="+val+"&typ="+typ;
}

function form_delete(ID){
	var del = confirm('<?=$lang[2279]?>');
	if(del){
		document.location.href="main_admin.php?action=setup_form_select&del=1&form_id="+ID;
	}
}

</Script>

<div class="container-fluid p-3">
    <FORM ACTION="main_admin.php" METHOD="post" NAME="form1">
        <input type="hidden" name="action" value="setup_form_select">
        <input type="hidden" name="form_id" value="<?=$form_id?>">
        <input type="hidden" name="form_name" value="<?=$form_name?>">
        <input type="hidden" name="referenz_tab" value="<?=$referenz_tab?>">
        <input type="hidden" name="new_form" value="1">

        <table class="table table-sm table-striped border bg-white">
            <thead>
            <tr>
                <th>ID</th>
                <th></th>
                <th><?=$lang[160]?></th>
                <th><?=$lang[1179]?></th>
                <th><?=$lang[925]?></th>
                <th><?=$lang[1162]?></th>
                <th><?=$lang[1986]?></th>
                <th><?=$lang[2555]?></th>
                <th><?=$lang[1638]?></th>
            </tr>
            </thead>

            <?php
            if($gformlist):
                $tablist = array_unique($gformlist['argresult_id']);
                foreach ($tablist as $key => $gtabid):
                    if($gtabid){
                        $cat = $gtab["desc"][$gtabid];
                    }else{
                        $cat = $lang[1986];
                    }
                    
                    ?>
            
                    <tr class="table-section"><td colspan="9"><?=$cat?></td></tr>
                    
                    
                    <?php
                    if($gformlist[$gtabid]['id']):
                        
                        foreach ($gformlist[$gtabid]['id'] as $key2 => $formid):

                                if($gformlist[$gtabid]["typ"][$key2] == 1){$typ = $lang[1183];}else{$typ = $lang[1184];}
                                
                                ?>
                                
                                <tr>
                                    <TD><?=$formid?></TD>
                                    <TD>
                                        <?php if(!$gformlist[$gtabid]["extension"][$key2]){ ?>
                                        <A HREF="main_admin.php?&action=setup_form_frameset&form_typ=<?=$gformlist[$gtabid]["typ"][$key2]?>&form_id=<?=$formid?>&referenz_tab=<?=$gformlist[$gtabid]["ref_tab"][$key2]?>"><i class="lmb-icon lmb-pencil cursor-pointer"></i></A>
                                        <?php } ?>
                                    </TD>
                                    
                                    <TD class="text-center"><i OnClick="form_delete('<?=$formid?>')" class="lmb-icon lmb-trash cursor-pointer"></i></TD>
                                    <TD>
                                        <INPUT class="form-control form-control-sm" TYPE="TEXT" NAME="form_name_<?=$formid?>" VALUE="<?=$gformlist[$gtabid]["name"][$key2]?>" STYLE="width:160px;" OnChange="change_settings(this.value,'<?=$gformlist[$gtabid]["id"][$key2]?>','name');">
                                    </TD>
                                    <TD><?=$typ?></TD>
                                    <TD><?=$gtab["desc"][$gformlist[$gtabid]["ref_tab"][$key2]]?></TD>
                                    <TD><?=$gformlist[$gtabid]["extension"][$key2]?></TD>
                                    <TD><select class="form-select form-select-sm" name="form_custmenu_<?=$formid?>" OnChange="change_settings(this.value,'<?=$gformlist[$gtabid]["id"][$key2]?>','custmenu');"><option>
                                        <?php
                                        foreach ($LINK['name'] as $key => $value) {
                                            if ($LINK['typ'][$key] == 1 AND $LINK['subgroup'][$key] == 2 AND $key >= 1000) {
                                                if($key == $gformlist[$gtabid]["custmenu"][$key2]){$SELECTED = 'SELECTED';}else{$SELECTED = '';}
                                                echo "<option value=\"$key\" $SELECTED>" . $lang[$value] . "</option>";
                                            }
                                        }
                                        ?>
                                    </select>
                                    </TD>
                                    <TD><?=$userdat["bezeichnung"][$gformlist[$gtabid]["erstuser"][$key2]]?></TD>
                                </tr>
                                
                                <?php
                        endforeach;
                    endif;

                endforeach;
            endif;
            ?>

        </table>

        <table class="table table-sm table-striped mb-0 border bg-white">
            <thead>
            <tr>
                <TD><?=$lang[4]?></TD>
                <TD><?=$lang[164]?></TD>
                <TD><?=$lang[1464]?></TD>
                <TD><?=$lang[925]?></TD>
                <TD><?=$lang[1986]?></TD>
                <TD></TD>
            </tr>
            </thead>

            <tr>
                <TD><INPUT TYPE="TEXT" NAME ="form_name" SIZE="20" class="form-control form-control-sm"></TD>
                <TD>
                    <SELECT NAME="tabid" class="form-select form-select-sm">
                        <option></option>
                            <?php
                            foreach ($tabgroup["id"] as $key0 => $value0) {
                                echo '<optgroup label="' . $tabgroup["name"][$key0] . '">';
                                foreach ($gtab["tab_id"] as $key => $value) {
                                    if($gtab["tab_group"][$key] == $value0){
                                        echo "<option value=\"".$value."\">".$gtab["desc"][$key].'</option>';
                                    }
                                }
                                echo '</optgroup>';
                            }
                            ?>
                    </SELECT>
                </TD>

                <TD>
                    <SELECT NAME="formcopy" class="form-select form-select-sm">
                        <option VALUE="0"></option>
                            <?php
                            if($gformlist){
                                foreach ($gformlist as $key => $value){
                                    if($gtab["table"][$key]){
                                        if($value["id"]){
                                            foreach ($value["id"] as $key2 => $value2){
                                                if($value2){
                                                    echo "<option value=\"".$value2."\">".$value["name"][$key2].'</option>';
                                                }
                                            }}}
                                }}
                            ?>
                    </SELECT>
                </TD>


                <TD>
                    <SELECT NAME="form_typ" class="form-select form-select-sm">
                        <OPTION VALUE="1"><?=$lang[1183]?>
                        <OPTION VALUE="2"><?=$lang[1184]?>
                    </SELECT></TD>
                <TD>
                    <select ID="zmenufiles" NAME="form_extension" style="width:100px;" class="form-select form-select-sm">
                        <option></option>
                        <?php
                        # Extension Files
                        $extfiles = read_dir(EXTENSIONSPATH,1);
                        foreach ($extfiles["name"] as $key1 => $filename){
                            if($extfiles["typ"][$key1] == "file" AND $extfiles["ext"][$key1] == "ext"){
                                $path = lmb_substr($extfiles["path"][$key1],lmb_strlen(EXTENSIONSPATH),100);
                                echo "<option value=\"".$path.$filename."\">".$path.$filename.'</option>';
                            }
                        }
                        
                        ?>
                    </select>
                </TD>


                <TD><button class="btn btn-primary btn-sm" TYPE="SUBMIT" NAME="new" value="1"><?=$lang[1186]?></button></TD>
            </tr>

        </table>
        
        
    </FORM>

</div>




