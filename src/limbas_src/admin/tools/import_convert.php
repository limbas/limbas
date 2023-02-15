<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */


?>

<?php if(!$import_action){?>


    <div class="row">
        <div class="col-md-9 col-lg-7">
            <div class="card border-top-0 mb-3">
                <div class="card-body">

                    <div class="row">
                        <div class="col-sm-3">
                            <?=$lang[2240]?>:
                        </div>
                        <div class="col-sm-5">
                            <select name="covertfromtable" class="form-select form-select-sm">
                                <option></option>
                                <?php
                                $sqlquery = "SELECT TABELLE FROM LMB_CONF_TABLES";
                                $rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
                                while(lmbdb_fetch_row($rs)) {
                                    $existing_tables[] = lmb_strtoupper(lmbdb_result($rs, "TABELLE"));
                                }

                                $odbctable = dbf_20(array($DBA["DBSCHEMA"],null,"'TABLE','VIEW'"));
                                foreach($odbctable["table_name"] as $tkey => $tvalue) {
                                    if(!in_array(lmb_strtoupper($tvalue),$existing_tables)){
                                        echo "<option value=\"".$tvalue."\">$tvalue</option>";
                                    }
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col-sm-4 text-end">
                            <button class="btn btn-primary" type="button" onclick="document.form1.import_action.value=1;document.form1.submit();"><?=$lang[2240]?></button>
                        </div>

                    </div>

                </div>
            </div>

        </div>
    </div>
    

<?php }?>




<?php if(!$import_action){return;}?>


<div class="card">
    <div class="card-body">

<?php
if($import_action == 1 AND $covertfromtable){
	/* --- Tabellenfelder auslesen --------------------------------------------- */
	$columns = dbf_5(array($DBA["DBSCHEMA"],$tabname));
	foreach ($columns["columnname"] as $key => $value){
		$header[] = $value;
		$e["field_type"][] = $columns["datatype"][$key];
		$e["length"][] = $columns["length"][$key];
	}
	
    import_create_fieldmapping($ifield,'convert',$covertfromtable);

}elseif($import_action == 2){
    $result = import_create_addtable('convert', $ifield, $add_permission = null, 1);
}

?>
    </div>
</div>


