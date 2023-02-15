<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */




if($gfield[$gtabid]["field_name"][$field_id] AND $gtab["table"][$gtabid] AND $ID AND $gfield[$gtabid]["data_type"][$field_id] == 39){

    $formname = $gfield[$gtabid]["form_name"][$field_id];
	$onlyfield[$gtabid] = array($field_id);
	$fieldname = $field_id;
	if($language){
	    $formname = 'LANG' . $language . '_' . $gfield[$gtabid]["form_name"][$field_id];
	    $fieldname = 'LANG' . $language . '_' . $gfield[$gtabid]['rawfield_name'][$field_id];
	    $onlyfield[$gtabid][] = $fieldname;
	    $extension['select'][] = $fieldname;
    }
	
	if($gfield[$gtabid]["sort"][$field_id] AND $gfield[$gtabid]["perm_edit"][$field_id]){
		# --- Inhalt ändern ---
		if(${$formname}){
			update_data("$gtabid,$field_id,$ID,null,$language",1,null);
		}
	}

	$gresult = get_gresult($gtabid,1,null,null,null,$onlyfield,$ID,$extension);
	$longresult = $gresult[$gtabid][$fieldname][0];
	?>

    <script src="assets/vendor/tinymce/tinymce.min.js?v=<?=$umgvar["version"]?>"></script>
	
	<DIV class="p-3">
	
	
	<?php if($gfield[$gtabid]["perm_edit"][$field_id]){?>

		<FORM ACTION="main.php" METHOD="post" NAME="form1">
		<input type="hidden" name="action" value="edit_long">
		<input type="hidden" name="gtabid" value="<?= $gtabid ?>">
		<input type="hidden" name="field_id" value="<?= $field_id ?>">
		<input type="hidden" name="ID" value="<?= $ID ?>">
		<textarea id="<?=$formname?>" NAME="<?=$formname?>" style="height:70vh"><?=htmlentities($longresult,ENT_QUOTES,$umgvar["charset"])?></textarea>
            
            
        <div class="d-flex mt-3 justify-content-between">
            <div class="w-25">
                <select class="form-select" name="language" onchange="document.form1.submit()">
                    <option value="0"><?= $umgvar['multi_language_desc'][$session['dlanguage']] ?></option>
                    <?php if($umgvar['multi_language']): ?>
                        <?php foreach ($umgvar['multi_language'] as $lkey => $langID): ?>
                            <option value="<?=$langID?>" <?=$language == $langID ? 'selected' : ''?>><?=$umgvar['multi_language_desc'][$langID]?></option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary"><?=$lang[842]?></button>
        </div>

		</FORM>

		<?php
        if($gform[$gformid]["parameters"][$formid]){
            $params = $gform[$gformid]["parameters"][$formid];
        }else{
            $params = 1;
        }
        echo lmb_ini_wysiwyg($formname,$gfield[$gtabid]["relext"][$field_id],null,$params);
        ?>



	<?php
	}elseif($gfield[$gtabid]["sort"][$field_id]){
		echo htmlentities($gfield[$gtabid]["field_name"][$field_id],ENT_QUOTES,$umgvar["charset"]);
	}
	?>
	
	
<?php }else{echo '<BR><div style="text-align:center;">'.$lang[114].'</div>';}?>
        
        
    </DIV>
