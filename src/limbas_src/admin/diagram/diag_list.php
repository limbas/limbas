<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */



/* --- Diagramm löschen  ------------------------------ */
if($del AND $id){
	//pchart
	$sqlquery = "DELETE FROM LMB_CHART_LIST WHERE ID = $id";
	$rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);

	$sqlquery = "DELETE FROM LMB_CHARTS WHERE CHART_ID = $id";
	$rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
	
	$sqlquery = "DELETE FROM LMB_RULES_REPFORM WHERE REPFORM_ID = $id AND TYP = 3";
	$rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
}

/* --- Diagramm erstellen  ---------------------------- */
if($new_diag AND $diag_name and $tab_id){
	//pchart
	$NEXTID = next_db_id("LMB_CHART_LIST");
	$sqlquery = "INSERT INTO LMB_CHART_LIST (ID,ERSTUSER,DIAG_NAME,TAB_ID) VALUES($NEXTID,".$session["user_id"].",'".parse_db_string($diag_name,80)."',$tab_id)";
	$rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);

	$NEXTID2 = next_db_id("LMB_RULES_REPFORM");
	$sqlquery = "INSERT INTO LMB_RULES_REPFORM(ID,TYP,GROUP_ID,LMVIEW,REPFORM_ID) VALUES ($NEXTID2,3,".$session["group_id"].",".LMB_DBDEF_TRUE.",$NEXTID)";
	$rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
}

/* --- Diagrammtyp ändern ----------------------------- */
if($diag_type AND $ID){
	//pchart
	$sqlquery = "UPDATE LMB_CHART_LIST SET DIAG_TYPE = '".trim(parse_db_string($diag_type,50))."',TEMPLATE='' WHERE ID = $ID";
	$rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
}

/* --- Template ändern -------------------------------- */
if($template AND $ID){
	//pchart
	$sqlquery = "UPDATE LMB_CHART_LIST SET TEMPLATE = '".trim(parse_db_string($template,50))."' WHERE ID = $ID";
	$rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
}

/* --- Transposed ändern ------------------------------ */
if($transposed AND $ID){
	//pchart
	$sqlquery = "UPDATE LMB_CHART_LIST SET TRANSPOSED = $transposed WHERE ID = $ID";
	$rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
}

/* Get Diagram Settings */
$sqlquery = "SELECT 
				CHARTS.ID, 
				CHARTS.TAB_ID, 
				CHARTS.DIAG_NAME, 
				CHARTS.DIAG_DESC, 
				CHARTS.DIAG_TYPE, 
				CHARTS.TEMPLATE, 
				CHARTS.TRANSPOSED 
			FROM 
				LMB_CHART_LIST AS CHARTS, 
				LMB_RULES_REPFORM
			WHERE 
				LMB_RULES_REPFORM.REPFORM_ID = CHARTS.ID
				AND LMB_RULES_REPFORM.TYP = 3
				AND LMB_RULES_REPFORM.GROUP_ID = ".$session["group_id"];
$rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
if(!$rs) {$commit = 1;}
$gdiaglist = array();
while(lmbdb_fetch_row($rs)) {
	$key = lmbdb_result($rs, "ID");
	$gtabid = lmbdb_result($rs, "TAB_ID");
	$gdiaglist[$gtabid]["id"][$key] = lmbdb_result($rs, "ID");
	$gdiaglist[$gtabid]["name"][$key] = lmbdb_result($rs, "DIAG_NAME");
	$gdiaglist[$gtabid]["desc"][$key] = lmbdb_result($rs, "DIAG_DESC");
	$gdiaglist[$gtabid]["type"][$key] = lmbdb_result($rs, "DIAG_TYPE");
	$gdiaglist[$gtabid]["grouplist"][$key] = lmbdb_result($rs, "TEMPLATE");
	$gdiaglist[$gtabid]["transposed"][$key] = lmbdb_result($rs, "TRANSPOSED");
}

?>

<script>
	function deleteDiagram(ID){
		var del = confirm('<?=$lang[2285]?>');
		if(del){
			document.location.href="main_admin.php?action=setup_diag&del=1&id="+ID;
		}
	}
	function divclose(){
	
	}
</script>


<div class="container-fluid p-3">
    <FORM ACTION="main_admin.php" METHOD="post" NAME="form1">
        <input type="hidden" name="action" value="setup_diag">
        <input type="hidden" name="ID">
        <input type="hidden" name="diag_name">
        <input type="hidden" name="diag_type">
        <input type="hidden" name="template">
        <input type="hidden" name="transposed">
        <input type="hidden" id="diag_id" name="diag_id" value="<?=$diag_id?>">
        <input type="hidden" id="diag_tab_id" name="diag_tab_id" value="<?=$diag_tab_id?>">

        
        <?php if((!$diag_id)or(!$diag_tab_id)): ?>
        <table class="table table-sm table-striped mb-0 border bg-white">
            <thead>
            <tr>
                <th>ID</th>
                <th colspan=2></th>
                <th><?=$lang[4]?></th>
                <th><?=$lang[2863]?></th>
                <th><?=$lang[2864]?></th>
                <th><?=$lang[2207]?></th>
            </tr>
            </thead>

            <?php
            # Extension Files
            $extfiles = read_dir(EXTENSIONSPATH,1);

            if($gdiaglist):
                foreach ($gdiaglist as $gtabid => $value0): ?>

                    <tr class="table-section"><td colspan=8><?=$gtab["desc"][$gtabid]?></td></tr>
            
            <?php
                
                    foreach ($gdiaglist[$gtabid]["id"] as $key => $id): ?>
            
            
                    <tr>
                        <td><?=$id?></td>
                        <td><i OnClick="document.form1.diag_tab_id.value='<?=$gtabid?>';document.form1.diag_id.value='<?=$key?>';document.form1.submit();" class="lmb-icon lmb-pencil cursor-pointer"></i></td>
                        <td><i OnClick="deleteDiagram('<?=$gdiaglist[$gtabid]["id"][$key]?>')" class="lmb-icon lmb-trash cursor-pointer"></i></td>
                        <td><?=$gdiaglist[$gtabid]["name"][$key]?></td>
                        <td>
                            <select OnChange="document.form1.ID.value='<?=$gdiaglist[$gtabid]["id"][$key]?>';document.form1.diag_type.value=this.value;document.form1.submit();" class="form-select form-select-sm">
                                <OPTION value=" "></OPTION>
                                <?php
                                $diag_types = array("Line-Graph","Bar-Chart","Pie-Chart");
                                foreach ($diag_types as $diag_type_temp){
                                    if($gdiaglist[$gtabid]["type"][$key] == $diag_type_temp){$selected = "SELECTED";}else{$selected = "";}
                                    echo "<option VALUE=\"" . $diag_type_temp . "\" " . $selected . ">" . $diag_type_temp.'</option>';
                                }
                                ?>
                            </select>
                        </td>
                        <td><input type='checkbox' OnChange="document.form1.ID.value='<?=$gdiaglist[$gtabid]["id"][$key]?>';document.form1.transposed.value=this.checked;document.form1.submit();" <?=(($gdiaglist[$gtabid]["transposed"][$key]==1)?"checked":"")?>></td>
                        <td>
                            <select OnChange="document.form1.ID.value='<?=$gdiaglist[$gtabid]["id"][$key]?>';document.form1.template.value=this.value;document.form1.submit();" class="form-select form-select-sm">
                                <OPTION value=" "></OPTION>
                                <?php
                                    foreach ($extfiles["name"] as $key1 => $filename){
                                        if($extfiles["typ"][$key1] == "file" AND ($extfiles["ext"][$key1] == "php" OR $extfiles["ext"][$key1] == "inc")){
                                            $path = lmb_substr($extfiles["path"][$key1],lmb_strlen($umgvar["pfad"]),100);
                                            if($gdiaglist[$gtabid]["grouplist"][$key] == $path.$filename){$selected = "SELECTED";}else{$selected = "";}
                                            echo "<option VALUE=\"".$path.$filename."\" $selected>".str_replace("/EXTENSIONS/","",$path).$filename.'</option>';
                                        }
                                    }
                                ?>
                            </select>
                        </td>
                    </tr>
            
            <?php
                    endforeach;
                endforeach;
            endif;

            ?>

            <tfoot>


            <tr>
                <th colspan="3"></th>
                <th><?=$lang[4]?></th>
                <th><?=$lang[164]?></th>
                <th></th>
                <th></th>
            </tr>

            <tr>
                <td colspan="3"></td>
                <td><input type="text" name="diag_name" class="form-control form-control-sm"></td>
                <td>
                    <SELECT NAME="tab_id" class="form-select form-select-sm">
                        <OPTION></OPTION>
                        <?php
                        foreach ($tabgroup["id"] as $key0 => $value0) {
                            echo '<optgroup label="' . $tabgroup["name"][$key0] . '">';
                            foreach ($gtab["tab_id"] as $key => $value) {
                                if($gtab["tab_group"][$key] == $value0){
                                    echo "<option VALUE=\"".$value."\">".$gtab["desc"][$key].'</option>';
                                }
                            }
                            echo '</optgroup>';
                        }
                        ?>
                    </SELECT>
                </td>
                <td><button type="submit" name="new_diag" class="btn btn-primary btn-sm" value="1"><?=$lang[1191]?></button></td>
                <td></td>
            </tr>
            </tfoot>


        </table>

        <?php else:
            // Detail Diagram Page
            require_once(COREPATH . 'admin/diagram/diag_detail.php');
        endif; ?>

    </FORM>

</div>
