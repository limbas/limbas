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
 * ID: 148
 */
?>



<?php
# * --- Teilimport -------------------------------
if(!$txt_terminate){$txt_terminate = $umgvar['csv_delimiter'];}
if(!$txt_enclosure){$txt_enclosure = $umgvar['csv_enclosure'];}
if(!$import_typ){$import_typ = 'atm';}
?>


<TABLE ID="tab1" width="100%" cellspacing="2" cellpadding="1" class="tabBody importcontainer">
<TR class="tabHeader"><TD class="tabHeaderItem" COLSPAN="99"><?=$lang[990]?>:</TD></TR>

<TR class="tabBody">
<TD valign="top"><IMG SRC="pic/limbasicon.gif" ALT="<?=$lang[995]?>" Border="0"><input type="radio" NAME="import_typ" VALUE="atm" STYLE="BACKGROUND-COLOR:<?= $farbschema['WEB8'] ?>;BORDER:none" <?php if($import_typ == 'atm'){echo 'checked';}?>></TD>
<TD valign="top">&nbsp;<?=$lang[998]?>:&nbsp;</TD>
<TD valign="top"><input type="file" NAME="fileatm"></TD>

<TD align="right" colspan="5">
<?php if($import_overwrite == "over" OR !$import_overwrite){$checked = "checked";}else{$checked = "";}?>
<?=$lang[1002]?><INPUT TYPE="RADIO" NAME="import_overwrite" VALUE="over" <?=$checked?>>&nbsp;&nbsp;<br>
<?php if($import_overwrite == "add"){$checked = "checked";}else{$checked = "";}?>
<?=$lang[1003]?><INPUT TYPE="RADIO" NAME="import_overwrite" VALUE="add" <?=$checked?>>&nbsp;&nbsp;<br>
<?php if($import_overwrite == "add_with_ID"){$checked = "checked";}else{$checked = "";}?>
<?=$lang[1003].' ('.$lang[1004].')'?><INPUT TYPE="RADIO" NAME="import_overwrite" VALUE="add_with_ID" <?=$checked?>>&nbsp;&nbsp;
</TD>

</TR>

<TR class="tabBody"><TD colspan="5"><HR></TD></TR>

<TR class="tabBody">
<TD valign="top"><i class="lmb-icon lmb-file-text" ALT="<?=$lang[991]?>" Border="0"></i><input type="radio" ID="import_typ" NAME="import_typ" VALUE="txt" STYLE="BACKGROUND-COLOR:<?= $farbschema['WEB8'] ?>;BORDER:none" <?php if($import_typ == 'txt'){echo 'checked';}?>></TD>
<TD valign="top">&nbsp;<?=$lang[992]?>&nbsp;(csv,gz,zip):&nbsp;</TD>
<TD valign="top"><input type="file" NAME="filetxt"></TD>
<TD valign="top" align="right">
<td>
<table cellpadding="0" cellspacing="0">
    <style>
        .table-spacer-2px { padding: 2px 0; }
    </style>
<tr><td><?=$lang[997]?>&nbsp;</td><td class="table-spacer-2px" align="right"><SELECT NAME="txt_calculate" style="width:50px"><OPTION VALUE="0"><?=$lang[993]?><OPTION VALUE="50" selected>50<OPTION VALUE="100">100<OPTION VALUE="1000">1000<OPTION VALUE="99999999"><?=$lang[994]?></SELECT></td></tr>
<tr><td>field terminated&nbsp;</td><td class="table-spacer-2px"  align="right"><input type="text" NAME="txt_terminate" style="width:50px" value="<?=htmlentities($txt_terminate,ENT_QUOTES,$umgvar["charset"])?>"></td></tr>
<tr><td>field enclosure&nbsp;</td><td class="table-spacer-2px" align="right"><input type="text" NAME="txt_enclosure" style="width:50px" value="<?=htmlentities($txt_enclosure,ENT_QUOTES,$umgvar["charset"])?>"></td></tr>

<tr><td><?=$lang[1003]?>&nbsp;</td><td align="right" class="table-spacer-2px">
	<select name="attach_gtabid"><option value="">
	<?php
	$gtab_ = $gtab;
	asort($gtab_['table']);
	foreach ($gtab_["table"] as $key => $value){
	    if($attach_gtabid == $gtab_["tab_id"][$key]){$SELECTED = 'SELECTED';}else{$SELECTED = '';}
		echo "<option value=\"".$gtab_["tab_id"][$key]."\" $SELECTED>".$gtab_["table"][$key]."</option>";
	}
	?>
	</select>
</td></tr>

</table>
</td></tr>


<TR class="tabBody"><TD colspan="5"><HR></TD></TR>
<TR class="tabBody">
<TD colspan="5"><INPUT TYPE="button" onclick="document.form1.import_action.value=1;document.form1.submit();" VALUE="<?=$lang[979]?>">&nbsp;&nbsp;utf8 en/decode&nbsp;<input name="txt_encode" type="checkbox" <?php if($txt_encode){echo "checked";}?>></TD>
</TR>


<TR><TD colspan="5" class="tabFooter"></TD></TR>

</TABLE>
<?php





/* 
 * System import
 * import limbas export files
*/
if($import_action AND $import_typ == "atm"){
    
    $fileatm = $_FILES['fileatm']['tmp_name'];
    $fileatm_name = $_FILES['fileatm']['name'];
    $fileatm_error = $_FILES['fileatm']['error'];
    
    # precheck of uploaded archive
    if($fileatm){
    	if(is_uploaded_file($fileatm)){
    		$pfad = $umgvar["pfad"]."/USER/".$session["user_id"]."/temp/";
    		rmdirr($pfad);
    		$copy = copy ($_FILES['fileatm']['tmp_name'], $pfad.$fileatm_name);
    		$sys = "tar -tz -C ".$pfad." -f \"".rtrim($pfad, '/')."/".$fileatm_name."\"";
    		$out = `$sys`;
    		if($out){
    			$out = explode("\n",$out);
    			if(in_array("export.conf",$out) AND in_array("export.dat",$out)){
    				$import_count = "single";
    			}elseif(count($out) > 1){
    				$import_count = "group";
    			}
    		}else{
    			echo "error while unpacking archive using the following command: <br><i>$sys</i>!";
    		}
    	}elseif($fileatm OR $fileatm_error){
    		echo "error while uploading archiv! $fileatm_error";
    	}
    }

    // import single or multible atm files
    if($import_count == 'single' OR $import_action == 2){
        
        echo '<div class="lmbPositionContainerMain small">';
    	$result = import_tab_pool($import_typ,$import_overwrite,$import_count,1,$fileatm,$fileatm_name,null,null,null,$txt_encode);
    	echo '</div>';
    	$imptabgroup = $result[0];
    	$existingfields = $result[1];
    	
    // preselect multible atm files
    }elseif($import_count == 'group'){
       // show form for selecting files
	   import_part_groupselect($fileatm,$fileatm_name);
    }

/* 
 * TEXT import
 * import text based files
*/
}elseif($import_action AND $import_typ == "txt"){

    #tab_name = field['tablename']
    #tab_spelling = field['tablespelling']
    #tab_group = field['tablegroup']
    
    // import and attach to existing table
    if($attach_gtabid){
        if($import_action == 1){
            $parsefile = import_parse_txt($txt_terminate,$txt_calculate,$txt_enclosure);
            $header = $parsefile["header"];
            import_attach_fieldmapping($attach_gtabid,$header,$ifield,'import file');
        }else{
            echo "<br>";
	        $result = import_attach_fromfile($ifield,$attach_gtabid,$txt_terminate,$txt_encode,$txt_enclosure);
            if ($result['false']) {
                echo"
        		<p style=\"color:red;\">&nbsp;&nbsp;".$result['false']." ".$lang[1012] . "</p><br>
        		<p style=\"color:green;\">&nbsp;&nbsp;".$result['true']." ".$lang[1017] . "</p>";
            } else {
                echo "<p style=\"color:green;\">&nbsp;&nbsp;".$result['true']." ".$lang[1017] . "</p>";
            }
	    }
    // import and create new table
    }else{
        // step 1 - merge fieldtypes
        if($import_action == 1){
            import_create_fieldmapping($ifield,$import_typ,null,null,$txt_terminate,$txt_enclosure,$txt_calculate,$txt_encode);
        // step 2 - import datasets
        }elseif($import_action == 2){
            $ifield = import_create_addtable($import_typ,$ifield,$add_permission,1);
            $result = import_create_filltable($import_typ,$ifield,$txt_terminate,$txt_encode = null, $txt_enclosure = null, 1);

            if ($result['false']) {
                echo"
        		<p style=\"color:red;\">&nbsp;&nbsp;".$result['false']." ".$lang[1012] . "</p><br>
        		<p style=\"color:green;\">&nbsp;&nbsp;".$result['true']." ".$lang[1017] . "</p>";
            } else {
                echo "<p style=\"color:green;\">&nbsp;&nbsp;".$result['true']." ".$lang[1017] . "</p>";
            }
        }
    }
}



?>