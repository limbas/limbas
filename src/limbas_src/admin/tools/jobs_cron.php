<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */
?>

<form action="main_admin.php" method="post" name="form1">
    <input type="hidden" name="action" VALUE="setup_jobs_cron">
    <input type="hidden" name="apply_jobs_crontab">
    
<div class="container-fluid p-3">

    <div class="row">
        <div class="col-lg-6">
            
        </div>
    </div>
    
    <table class="table table-sm table-striped border bg-white">
        <thead>
            <tr>
                <th><?=$lang[2068]?></th>
                <th>Template</th>
                <th><?=$lang[2070]?></th>
                <th><?=$lang[1242]?></th>
                <th><?=$lang[126]?></th>
                <th><?=$lang[2072]?></th>
            </tr>
        </thead>

        <tbody>

        <?php

        // get category descriptions
        $cat['structursync'] = array();
        $sqlquery = "SELECT ID,NAME FROM LMB_SYNCSTRUCTURE_TEMPLATE ORDER BY NAME";
        $rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
        while(lmbdb_fetch_row($rs)) {
            $cat['structursync'][lmbdb_result($rs,'ID')] = lmbdb_result($rs,'NAME');
        }

        $cat['datasync'] = array();
        $sqlquery = "SELECT ID,NAME FROM LMB_SYNC_TEMPLATE WHERE TABID IS NULL OR TABID = 0 ORDER BY NAME";
        $rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
        while(lmbdb_fetch_row($rs)) {
            $cat['datasync'][lmbdb_result($rs,'ID')] = lmbdb_result($rs,'NAME');
        }

        $sqlquery = "SELECT ID,KATEGORY,START,VAL,ERSTDATUM,ACTIV,DESCRIPTION,ALIVE,JOB_USER FROM LMB_CRONTAB ORDER BY KATEGORY";
        $rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);

        $kategorie = null;
        while(lmbdb_fetch_row($rs)){
            $kat = lmbdb_result($rs,"KATEGORY");

            if ($kategorie != $kat):
                $kategorie = $kat;
                ?>
                <tr class="table-section"><td colspan="11"><?=$kategorie?></td></tr>
                <?php
            endif;

            $tid = lmbdb_result($rs,'ID');
            $val = lmbdb_result($rs,'VAL');
            $category = lmbdb_result($rs,"KATEGORY") ;
            $val0 = explode(';',$val);

            $activ = '';
            if(lmbdb_result($rs,"ACTIV")){
                $activ = "<i class=\"lmb-icon lmb-check-alt2\" border=\"0\"></i>";
            }

            $template = '';
            if($category == "TEMPLATE"){
                $template = $val0[0];
            }elseif($category == "STRUCTURESYNC"){
                $template = $cat['structursync'][$val0[0]];
            }elseif($category == "DATASYNC" OR $category == "RSYNC"){
                $template = $cat['datasync'][$val0[0]];
            }

            $crontime = explode(';',lmbdb_result($rs,"START"));


            ?>
            <TR>
                <TD><?=lmbdb_result($rs,"ID")?></TD>
                <TD><?=$template?></TD>
                <TD><?=$crontime[0]?> <?=$crontime[1]?> <?=$crontime[2]?> <?=$crontime[3]?> <?=$crontime[4]?></TD>
                <TD><?=lmbdb_result($rs,'JOB_USER')?></TD>
                <TD><?=lmbdb_result($rs,'DESCRIPTION')?></TD>
                <TD><?=$activ?></TD>
            </TR>

            <?php
            $cronvalue[] = str_replace(';',' ',lmbdb_result($rs,'START')). ' php "' . COREPATH . '/cron.php" ' . lmbdb_result($rs,"ID");
        }

        ?>
        
        </tbody>

    </table>

    <?php

    /* crontab value */
    $cronStr = '';
    if($cronvalue){
        foreach($cronvalue as $key => $value){
            $cronStr .= str_replace(";", " ", $value) . "\n";
        }
    }
    ?>



        <div class="card mt-3">
            <div class="card-body">
                <h4><?=$lang[1500]?></h4>
                <textarea class="form-control mb-3" rows="15" readonly><?php echo get_cron_tab(); ?></textarea>
                <div class="row">
                    <div class="col-6 text-success">
                        <?php if ($apply_jobs_crontab_success) : ?>
                            <?=$lang[2006]?>
                        <?php endif; ?>
                    </div>
                    <div class="col-6 text-end">
                        <button type="button" class="btn btn-primary" onclick="if(confirm('write jobs to crontab of webserver!')){document.form1.apply_jobs_crontab.value=1;document.form1.submit();}"><?=$lang[2444]?></button>
                    </div>
                </div>
            </div>

        </div>

</div>

</form>
