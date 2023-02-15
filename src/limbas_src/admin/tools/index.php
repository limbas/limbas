<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */

# rebuild index
if(($rebuild OR $delete) AND $use_index){
    foreach ($use_index as $indname => $value){
        $indspec = explode('#',$value);
        $indt = dbf_4($indspec[0]);
        $indf = dbf_4($indspec[1]);
        $indname = dbf_4($indname);
        if(!$indname OR !$indt OR !$indf){continue;}
        # drop index
        $sqlquery = dbq_5(array($DBA["DBSCHEMA"],$indname,$indt));
        $rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
        if($rs){lmb_alert("index $indname deleted");}

        if($rebuild){
            # create index
            $indname = lmb_getConstraintName('LMB_INDV',$indt,$indf);
            $sqlquery = dbq_4(array($DBA["DBSCHEMA"],$indname,$indt,$indf));
            $rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
            if($rs){lmb_alert("index $indname created");}
        }
    }
    $active_tab = 1;
}

# delete contraint
if(($delete_constraint AND $use_constraint)){
    foreach ($use_constraint as $constraint_name => $value){
        $indspec = explode('#',$value);
        $indt = dbf_4($indspec[0]);
        $indf = dbf_4($indspec[1]);
        $constraint_name = dbf_4($constraint_name);
        if(!$constraint_name OR !$indt OR !$indf){continue;}
        # drop constraint
        $sqlquery = dbq_25(array($indt,$indf,$constraint_name));
        $rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
        if($rs){lmb_alert("contraint $constraint_name deleted");}
    }
    $active_tab = 2;
}

# delete key
if(($delete_fkey AND $use_fkey)){
    foreach ($use_fkey as $fkey_name => $value){
        $indspec = explode('#',$value);
        $indt = dbf_4($indspec[0]);
        $indf = dbf_4($indspec[1]);
        $fkey_name = dbf_4($fkey_name);
        if(!$fkey_name OR !$indt OR !$indf){continue;}
        # drop constraint
        $sqlquery = dbq_6(array($indt,$fkey_name));
        $rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
        if($rs){lmb_alert("Foreign Keys $fkey_name deleted");}
    }
    $active_tab = 4;
}


?>


<script>

function checkall(el,name){

    if(el.checked){
        $("."+name).prop( "checked", true );
    }else{
        $("."+name).prop( "checked", false );
    }

}

function ind_sort(val){
	document.form1.ind_sort.value=val;
	document.form1.submit();
}

</script>



<div class="container-fluid p-3">
    <FORM ACTION="main_admin.php" METHOD="post" NAME="form1">
        <input type="hidden" name="action" value="setup_index">
        <input type="hidden" name="ind_sort" value="<?=$ind_sort;?>">
        
                
        <ul class="nav nav-tabs" role="tablist" id="nav-tab">
            <li class="nav-item" role="presentation">
                <a class="nav-link active" id="indexes-tab" data-bs-toggle="tab" href="#indexes" role="tab" aria-controls="indexes" aria-selected="true"><?=$lang[2723]?></a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link" id="foreign-tab" data-bs-toggle="tab" href="#foreign" role="tab" aria-controls="foreign" aria-selected="false"><?=$lang[2725]?></a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link" id="primary-tab" data-bs-toggle="tab" href="#primary" role="tab" aria-controls="primary" aria-selected="false"><?=$lang[2729]?></a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link" id="unique-tab" data-bs-toggle="tab" href="#unique" role="tab" aria-controls="unique" aria-selected="false"><?=$lang[2724]?></a>
            </li>
        </ul>
        <div class="tab-content bg-white border border-top-0" id="nav-tabContent">

            <div class="tab-pane show active" id="indexes" role="tabpanel" aria-labelledby="indexes-tab">
                <table class="table table-sm table-striped table-hover mb-0">
                    <thead>
                    <tr>
                        <th onclick="ind_sort('name')" class="border-top-0"><?= $lang[4] ?></th>
                        <th onclick="ind_sort('table')" class="border-top-0"><?= $lang[164] ?></th>
                        <th onclick="ind_sort('column')" class="border-top-0"><?= $lang[168] ?></th>
                        <th onclick="ind_sort('unique')" class="border-top-0">unique</th>
                        <th onclick="ind_sort('used')" class="border-top-0"><?= $lang[1856] ?></th>
                        <th class="border-top-0"><input type="checkbox" onclick="checkall(this,'use_index')"></th>
                    </tr>

                    </thead>

                    <?php

                    /* -------- indexes --------*/

                    # get indexes
                    $sqlquery = dbq_2(array($DBA["DBSCHEMA"],null,null,1));
                    $rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
                    $bzm = 1;
                    while(lmbdb_fetch_row($rs)) {
                        $ind["name"][] = lmbdb_result($rs,"INDEXNAME");
                        $ind["table"][] = lmbdb_result($rs,"TABLENAME");
                        $ind["used"][] = lmbdb_result($rs,"INDEX_USED");
                        $ind["column"][] = lmbdb_result($rs,"COLUMNNAME");
                        $ind["unique"][] = lmbdb_result($rs,"IS_UNIQUE");
                        $bzm1++;
                    }
                    $ind["key"] = $ind["name"];

                    if($ind_sort == "name"){
                        asort($ind["name"]);
                        $ind["key"] = $ind["name"];
                    }elseif($ind_sort == "table"){
                        asort($ind["table"]);
                        $ind["key"] = $ind["table"];
                    }elseif($ind_sort == "column"){
                        asort($ind["column"]);
                        $ind["key"] = $ind["column"];
                    }elseif($ind_sort == "used"){
                        asort($ind["used"]);
                        $ind["key"] = $ind["used"];
                    }elseif($ind_sort == "unique"){
                        asort($ind["unique"]);
                        $ind["key"] = $ind["unique"];
                    }



                    if($ind["name"]):

                        foreach ($ind["key"] as $key => $value):
                            ?>
                            
                    <tr>
                        <td <?php echo (strtolower(substr($ind["name"][$key],0,4)) != 'lmb_') ? 'class="text-info"' : ''?>><?=$ind["name"][$key]?></td>
                        <td><?=$ind["table"][$key]?></td>
                        <td><?=$ind["column"][$key]?></td>
                        <td><?=$ind["unique"][$key]?></td>
                        <td><?=$ind["used"][$key]?></td>
                        <td>
                            <input type="checkbox" class="use_index" name="use_index[<?=$ind["name"][$key]?>]" value="<?=$ind["table"][$key]."#".$ind["column"][$key]?>">
                        </td>
                    </tr>
                    
                    
                    <?php
                        endforeach;
                    endif;


                    ?>

                    <tfoot>

                    <?php if($ind["name"]) : ?>

                        <TR>
                            <TD colspan="3">
                                <button type="submit" class="btn btn-outline-danger" name="delete" value="1"><?=$lang[160]?></button>
                            </TD>
                            <TD colspan="3" class="text-end">
                                <button type="submit" class="btn btn-primary" name="rebuild" value="1"><?=$lang[1858]?></button>
                            </TD>
                        </TR>
                    
                    <?php endif; ?>

                    </tfoot>
                </table>
            </div>

            <div class="tab-pane" id="foreign" role="tabpanel" aria-labelledby="foreign-tab">
                <table class="table table-sm table-striped table-hover mb-0">
                    <thead>
                    <tr>
                        <th class="border-top-0"><?= $lang[4] ?></th>
                        <th class="border-top-0"><?= $lang[164] ?></th>
                        <th class="border-top-0"><?= $lang[168] ?></th>
                        <th class="border-top-0"><?= $lang[2727] ?></th>
                        <th class="border-top-0"><?= $lang[2728] ?></th>
                        <th class="border-top-0"><input type="checkbox" onclick="checkall(this,'use_fkey')"></th>
                    </tr>

                    </thead>

                    <?php
                    /* -------- foreign keys --------*/

                    # get foreign keys
                    $fkey = lmb_getForeignKeys();

                    if($fkey["keyname"]):
                        foreach ($fkey["keyname"] as $key => $value):

                            ?>


                            <tr>
                                <td <?php echo (strtolower(substr($fkey["keyname"][$key],0,4)) != 'lmb_') ? 'class="text-info"' : ''?>><?=$fkey["keyname"][$key]?></td>
                                <td><?=$fkey["tablename"][$key]?></td>
                                <td><?=$fkey["columnname"][$key]?></td>
                                <td><?=$fkey["reftablename"][$key]?></td>
                                <td><?=$fkey["refcolumnname"][$key]?></td>
                                <td>
                                    <input type="checkbox" class="use_fkey" name="use_fkey[<?=$fkey["keyname"][$key]?>]" value="<?=$fkey["tablename"][$key]."#".$fkey["columnname"][$key]?>">
                                </td>
                            </tr>
                        <?php
                        endforeach;
                    endif;

                    ?>
                    
                    <tfoot>
                    <TR>
                        <TD colspan="6">
                            <button type="submit" class="btn btn-outline-danger" name="delete_fkey" value="1"><?=$lang[160]?></button>
                        </TD>
                    </TR>
                    </tfoot>
                    

                </table>

            </div>

            <div class="tab-pane" id="primary" role="tabpanel" aria-labelledby="primary-tab">

                <table class="table table-sm table-striped table-hover mb-0">
                    <thead>
                    <tr>
                        <th class="border-top-0"><?= $lang[4] ?></th>
                        <th class="border-top-0"><?= $lang[164] ?></th>
                        <th class="border-top-0"><?= $lang[168] ?></th>
                    </tr>

                    </thead>

                    <?php
                    # get primary keys
                    $pkey = dbq_23(array($DBA["DBSCHEMA"]));

                    if($pkey["PK_NAME"]){
                        foreach ($pkey["PK_NAME"] as $key => $value){
                            echo "<TR>
                            <TD> ".$pkey["PK_NAME"][$key]." </TD>
                            <TD> ".$pkey["TABLE_NAME"][$key]." </TD>
                            <TD> ".$pkey["COLUMN_NAME"][$key]." </TD>
                            </TR>";
                        }
                    }

                    ?>

                </table>

            </div>

            <div class="tab-pane" id="unique" role="tabpanel" aria-labelledby="unique-tab">

                <table class="table table-sm table-striped table-hover mb-0">
                    <thead>
                    <tr>
                        <th class="border-top-0"><?= $lang[4] ?></th>
                        <th class="border-top-0"><?= $lang[164] ?></th>
                        <th class="border-top-0"><?= $lang[168] ?></th>
                        <th class="border-top-0"><input type="checkbox" onclick="checkall(this,'use_constraint')"></th>
                    </tr>

                    </thead>

                    <?php

                    # get unique constraints
                    $constr = dbq_26(array($DBA["DBSCHEMA"]));

                    if($constr["TABLE_NAME"]):
                        foreach ($constr["TABLE_NAME"] as $key => $value):
                        ?>
                    <tr>
                        <td <?php echo (strtolower(substr($constr["PK_NAME"][$key],0,4)) != 'lmb_') ? 'class="text-info"' : ''?>><?=$constr["PK_NAME"][$key]?></td>
                            <td><?=$constr["TABLE_NAME"][$key]?></td>
                            <td><?=$constr["COLUMN_NAME"][$key]?></td>
                            <td>
                                <input type="checkbox" class="use_constraint" name="use_constraint[<?=$constr["PK_NAME"][$key]?>]" value="<?=$constr["TABLE_NAME"][$key]."#".$constr["COLUMN_NAME"][$key]?>">
                            </td>
                    </tr>
                    <?php
                        
                        endforeach;
                    endif;


                    ?>

                    <tfoot>
                    <TR>
                        <TD colspan="6">
                            <button type="submit" class="btn btn-outline-danger" name="delete_constraint" value="1"><?=$lang[160]?></button>
                        </TD>
                    </TR>
                    </tfoot>

                </table>




            </div>
        </div>


    </FORM>
</div>


