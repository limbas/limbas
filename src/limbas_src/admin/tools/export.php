<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */


?>


<script>


function lmb_selectAll(el){
    if($(el).prop("checked")){
        $(".syncexport").prop( "checked", true );
    }else{
    	$(".syncexport").prop( "checked", false );
    }
}

function lmb_disableAllBut(el) {
    if ($(el).prop("checked")) {
        // uncheck and disable all .syncexport and .syncexportf
        $(".syncexport, .syncexportf").not($(el)).prop("checked", false).attr("disabled", true);
        // clear text inputs
        $("input.syncexportf[type='text']").val('');
    } else {
        // enable all .syncexport and .syncexportf
        $(".syncexport, .syncexportf").not($(el)).removeAttr("disabled");
    }
}

function openSyncClients(el,id){
    $('#syncClients').hide();
    $('#syncHost').hide();
    if($('#'+id).css('display') == 'none') {
        $('#'+id).show();
    }
}

function edit_synctemplate(id,value){
    document.form4.edit_template.value=id;
    document.form4.edit_template_value.value=value;
    document.form4.sync_export_remote.value='';
    document.form4.submit();
}

function start_syncExport(stamp){

    var bzm = 0;
    $('.sync-precheck').filter('div[data-precheck=false]').each(function(){
        var el = $(this);
        syncid = el.attr('data-id');
        el.attr('data-precheck','true');
        el.addClass("spinner-border");

        postfunc = function(result){start_syncExportPost(result,el,syncid,stamp);};
        ajaxGet(null,'main_dyns_admin.php','syncToClient&syncid='+syncid+'&stamp='+stamp,null,'postfunc','form4',null,null,1);
        return false;
    });

}

function start_syncExportPost(data,el,syncid,stamp){

    try {
        var resultObj = JSON.parse(data);

    } catch (e) {
        var resultObj = new Object();
        resultObj.errLog = 'response error - no valid JSON';
    }

    el.removeClass("spinner-border");

    if(resultObj.notice){
        var notice = resultObj.notice;
    }else{
        var notice = 'no valid data';
    }

    // success
    if(resultObj && resultObj.success){

        // adding status text
        $('.sync-status').filter('div[data-id='+syncid+']').append("<a target='_new' href='USER/<?=$session["user_id"]?>/temp/syncexp_"+syncid+".html'>"+notice+"</a>");
        // adding status symbol
        el.addClass("fa-solid fa-circle-check fa-3x text-success");

        // adding checksum symbol
        if(resultObj.checksum == 'true'){
            $('#checksum_'+syncid).addClass("fa-solid fa-circle-check fa-3x text-success");
        }else if(resultObj.checksum == 'false'){
            $('#checksum_'+syncid).addClass("fa-solid fa-circle-check fa-3x text-warning");
        }

        // rstart next sync process
        start_syncExport(stamp);
    // no success
    }else {

        // adding status symbol
        el.addClass("fa-solid fa-circle-xmark fa-3x text-danger");

        // adding status error text
        if(resultObj.log){
            $('.sync-status').filter('div[data-id='+syncid+']').append("<a target='_new' href='USER/<?=$session["user_id"]?>/temp/syncexp_"+syncid+".html'><i>"+notice+"</i></a>");
        }

        // rstart next sync process
        start_syncExport(stamp);
    }

}

</script>

<?php

if(!$openTab) {
    $openTab = 1;
}

// get all slaves
$slaves = lmb_GetSyncSlaves();

?>
<div class="container-fluid p-3">
    
    <div class="row">
        <div class="col-md-9">
            
            <ul class="nav nav-tabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <a class="nav-link <?=($openTab == 1)?'active':''?>" id="part-exp-tab" data-bs-toggle="tab" href="#part-exp" role="tab" aria-controls="part-exp" aria-selected="true"><?=$lang[965]?></a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link <?=($openTab == 2)?'active':''?>" id="full-exp-tab" data-bs-toggle="tab" href="#full-exp" role="tab" aria-controls="full-exp" aria-selected="false"><?=$lang[967]?></a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link <?=($openTab == 3)?'active':''?>" id="project-exp-tab" data-bs-toggle="tab" href="#project-exp" role="tab" aria-controls="project-exp" aria-selected="false"><?=$lang[968]?></a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link <?=($openTab == 4)?'active':''?>" id="sync-exp-tab" data-bs-toggle="tab" href="#sync-exp" role="tab" aria-controls="sync-exp" aria-selected="false"><?=$lang[2859]?></a>
                </li>
            </ul>

            <div class="tab-content border border-top-0 bg-white mb-3">

                <?php /* --- Teilexport ------------------------------- */?>
                <div class="tab-pane p-3 <?=($openTab == 1)?'show active':''?>" id="part-exp"role="tabpanel" aria-labelledby="part-exp-tab">

                    <FORM ACTION="main_admin.php" METHOD="post" name="form1">
                        <INPUT TYPE="hidden" NAME="action" VALUE="setup_export">
                        <INPUT TYPE="hidden" NAME="openTab" VALUE="1">
                        <INPUT TYPE="hidden" NAME="make_package" VALUE="0">
                        
                        <div class="row mb-3">
                            
                            <div class="col-md-5">
                                <p class="fw-bold"><?=$lang[961]?></p>
                                <select NAME="exptable[]" MULTIPLE class="form-select form-select-sm" size="16" >
                                    <?php
                                    $odbc_table = dbf_20(array($DBA["DBSCHEMA"],null,"'TABLE','VIEW'"));
                                    foreach($odbc_table["table_name"] as $tkey => $tvalue) {
                                        if($exptable){
                                            if(in_array($tvalue,$exptable)){$slct = "SELECTED";}else{$slct = "";}
                                        }
                                        echo "<OPTION VALUE=\"".$tvalue."\" $slct>".$tvalue;
                                    }
                                    ?>
                                </select>
                            </div>

                            <div class="col-md-5">
                                <p class="fw-bold"><?=$lang[925]?></p>
                                <?php
                                if(!$format){
                                    $format = 'system';
                                }
                                ?>
                                
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" name="format" id="part-exp-excel" value="excel" <?=($format == 'excel')?'checked':''?>>
                                    <label class="form-check-label" for="part-exp-excel">
                                        <i class="lmb-icon lmb-excel-alt2" title="<?=$lang[962]?>"></i>
                                    </label>
                                </div>

                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" name="format" id="part-exp-txt" value="txt" <?=($format == 'txt')?'checked':''?>>
                                    <label class="form-check-label" for="part-exp-txt">
                                        <i class="lmb-icon lmb-file-text" title="<?=$lang[963]?>"></i>
                                    </label>
                                </div>
                                
                                <div class="form-check mb-4">
                                    <input class="form-check-input" type="radio" name="format" id="part-exp-system" value="system" <?=($format == 'system')?'checked':''?>>
                                    <label class="form-check-label" for="part-exp-system">
                                        <img src="assets/images/limbasicon.png" title="<?=$lang[964]?>" alt="<?=$lang[964]?>">
                                    </label>
                                </div>


                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="1" id="part-exp-txt-encode" name="txt_encode" <?=($txt_encode)?'checked':''?>>
                                    <label class="form-check-label" for="part-exp-txt-encode">
                                        utf8 en/decode
                                    </label>
                                </div>
                                
                            </div>
                            
                        </div>
                        
                        <div class="mb-3">
                            <p><span class="fw-bold">Filter:</span> SQL conform (WHERE ...)</p>
                            <textarea class="form-control form-control-sm" NAME="export_filter"><?=$export_filter?></textarea>
                        </div>

                        <div class="text-end">
                            <button class="btn btn-primary" type="submit" value="1" name="single_export"><?=$lang[979]?></button>
                        </div>

                    </FORM>



                </div>

                <?php /* --- Komplettexport --------------------------- */ ?>
                <?php
                if ($dump_export)
                {
                    echo '<script language="JavaScript">
                    limbasWaitsymbol(false,true,false);
                    </script>';
                }
                ?>
                <div class="tab-pane p-3 <?=($openTab == 2)?'show active':''?>" id="full-exp" role="tabpanel" aria-labelledby="full-exp-tab">
                    
                    <FORM METHOD="post" name="form2">
                        <INPUT TYPE="hidden" NAME="action" VALUE="setup_export">
                        <INPUT TYPE="hidden" NAME="openTab" VALUE="2">

                        <p class="fw-bold"><?=$lang[966]?></p>
                        
                        
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="format" id="full-exp-system" value="system" checked>
                            <label class="form-check-label" for="full-exp-system">
                                <img src="assets/images/limbasicon.png" title="<?=$lang[964]?>" alt="<?=$lang[964]?>">
                            </label>
                        </div>                        

                        <div class="row">
                            <div class="col-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="1" id="struct-only" name="struct_only">
                                    <label class="form-check-label" for="struct-only">
                                        Nur Struktur
                                    </label>
                                </div>
                            </div>
                            <div class="col-6 text-end">
                                <button class="btn btn-primary" type="submit" value="1" name="dump_export"><?=$lang[979]?></button>
                            </div>
                        </div>
                        
                        
                    </FORM>

                </div>

                <?php /* --- Projectexport ---------------------------- */?>
                <div class="tab-pane p-3 <?=($openTab == 3)?'show active':''?>" id="project-exp" role="tabpanel" aria-labelledby="project-exp-tab">
                    <FORM METHOD="post" name="form3">
                        <INPUT TYPE="hidden" NAME="action" VALUE="setup_export">
                        <INPUT TYPE="hidden" NAME="openTab" VALUE="3">
                        <INPUT TYPE="hidden" NAME="format" VALUE="group">

                        <p class="fw-bold"><?=$lang[2462]?></p>


                        <?php
                        
                        
                        $submit = 'group_export_';
                        $btntext = $lang[979];
                        if(!$group_export){
                            lmbExport_groupSelection();
                            $submit = 'group_export';
                        }else
                        {
                            echo '<script>
                            limbasWaitsymbol(false,true,false);
                            </script>';
                            $btntext = $lang[2462];
                        }
                        ?>

                        <div class="text-end">
                            <button class="btn btn-primary" type="submit" value="1" name="<?=$submit?>"><?=$btntext?></button>
                        </div>

                    </FORM>
                </div>

                <?php

                /* --- Sync Export ------------------------------- */
                if($sync_loadFromTempl) {
                    $syncmodule = lmb_loadSyncTemplate($sync_loadFromTempl);
                    if($syncmodule['syncToSlavesActive']){
                        $sync_to_slaves = 'sync_to_clients';
                    }else{
                        $sync_to_slaves = 'sync_to_host';
                    }
                }

                $modules = lmb_availableSyncModules();

                ?>
                <div class="tab-pane p-3 <?=($openTab == 4)?'show active':''?>" id="sync-exp" role="tabpanel" aria-labelledby="sync-exp-tab">
                    <FORM METHOD="post" name="form4">
                        <INPUT TYPE="hidden" NAME="action" VALUE="setup_export">
                        <INPUT TYPE="hidden" NAME="openTab" VALUE="4">
                        <INPUT TYPE="hidden" NAME="format" VALUE="sync">
                        <INPUT TYPE="hidden" NAME="sync_export_remote" VALUE="<?=$sync_export_remote?>">
                        <INPUT TYPE="hidden" NAME="edit_template">
                        <INPUT TYPE="hidden" NAME="edit_template_value">
                        <INPUT TYPE="hidden" NAME="add_synctemplate">

                        <div class="modal fade" id="modal-synctemplate" tabindex="-1" role="dialog">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Manage Sync Templates</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body" id="body-synctemplate">
                                    <?php

                                    require_once(COREPATH . 'admin/tools/export_syssynctemplate.php')
                                    ?>
                                    </div>

                                </div>
                            </div>
                        </div>



                        <div class="modal fade" id="modal-syncglobaltables" tabindex="-1" role="dialog">
                            <div class="modal-dialog modal-sm">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Select global tables</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body" id="sync-syncglobaltables">
                                    <?php

                                    $sqlquery = "SELECT TAB_ID,TABELLE FROM LMB_CONF_TABLES WHERE DATASYNC = 2 ORDER BY TABELLE";
                                    $rs = lmbdb_exec($db, $sqlquery) or errorhandle(lmbdb_errormsg($db), $sqlquery, $action, __FILE__, __LINE__);
                                    while (lmbdb_fetch_row($rs)) {
                                        $globtable = lmbdb_result($rs, 'TABELLE');
                                    ?>
                                        <div class="form-check mb-1" title="<?=$globtable?>">
                                            <input class="form-check-input syncexport" type="checkbox" value="1" name="syncmodule[<?='globalsynctables_'.$globtable?>]" <?=($syncmodule['globalsynctables_'.$globtable]?'checked':'')?>>
                                            <label class="form-check-label" for="misc-<?='globalsynctables_'.$globtable?>">
                                                <?=$globtable?>
                                            </label>
                                        </div>
                                    <?php
                                    }
                                    ?>
                                    </div>
                                </div>
                            </div>
                        </div>




                        <p class="fw-bold">Structure-Sync to host or slaves:</p>
                        
                        <div class="row">
                            <div class="col-md-2 pe-0">
                                <p>Module:</p>
                                
                                <div class="form-check mb-1">
                                    <input class="form-check-input syncexportf" type="checkbox" value="0" id="mod-selectall" OnClick="lmb_selectAll(this)">
                                    <label class="form-check-label" for="mod-selectall">
                                        <?=$lang[994]?>
                                    </label>
                                </div>

                                <hr>
                                
                                <?php
                                foreach($modules as $module => $params):

                                    if($params['group'] != 1){continue;}
                                ?>

                                    <div class="form-check mb-1" title="<?=$params['title']?>">
                                        <input class="form-check-input syncexport" type="checkbox" value="1" id="mod-<?=$module?>" name="syncmodule[<?=$module?>]" <?=($syncmodule[$module]?'checked':'')?>>
                                        <label class="form-check-label" for="mod-<?=$module?>">
                                            <?=$params['name']?>
                                        </label>
                                    </div>
                                
                                <?php endforeach; ?>
                                
                            </div>
                            <div class="col-md-2 pe-0">
                                <p><?=$lang[1924]?>:</p>

                                <?php
                                foreach($modules as $module => $params):

                                    if($params['group'] != 2){continue;}

                                    ?>

                                    <div class="form-check mb-1" title="<?=$params['title']?>">
                                        <input class="form-check-input syncexport" type="checkbox" value="1" id="misc-<?=$module?>" name="syncmodule[<?=$module?>]" <?=($syncmodule[$module]?'checked':'')?>>
                                        <label class="form-check-label <?=($module == 'globalsynctables' ? ' link-primary pe-auto' : '')?> " <?=($module == 'globalsynctables' ? 'onclick="$(\'#modal-syncglobaltables\').modal(\'show\');"' : 'for="misc-'.$module.'"')?> >
                                            <?=$params['name']?>
                                        </label>
                                    </div>

                                <?php endforeach; ?>
                                
                            </div>
                            <div class="col-md-4 pe-0">
                                <p><?=$lang[2483]?>:</p>


                                <?php
                                foreach($modules as $module => $params):

                                    if($params['group'] != 3){continue;}
                                    ?>

                                    <div class="form-check mb-1">
                                        <input class="form-check-input syncexportf" type="checkbox" value="1" id="func-<?=$module?>" name="syncmodule[<?=$module?>]" <?=($syncmodule[$module]?'checked':'')?>>
                                        <label class="form-check-label" for="func-<?=$module?>">
                                            <?=$params['name']?>
                                        </label>
                                    </div>

                                <?php endforeach; ?>
                                
                            </div>
                            <div class="col-md-4">

                                <div class="row mb-2" title="run own function after update">
                                    <label for="synccallextensionfunction" class="col-sm-2 col-form-label">run:</label>
                                    <div class="col-sm-10">
                                        <input type="text" id="synccallextensionfunction" name="syncmodule[synccallextensionfunction]" class="form-control form-control-sm syncexportf" value="<?=($syncmodule['synccallextensionfunction']?$syncmodule['synccallextensionfunction']:'')?>">
                                        <i>usage: functionname<br>@param ('before' | 'after') </i>
                                    </div>

                                </div>
                                
                            </div>
                        </div>

                        <hr>

                        <div class="row justify-content-end">
                            <div class="col col-2">
                                <button class="btn btn-outline-dark" type="submit" value="1" name="sync_export_config">export config</button>
                            </div>
                            <div class="col col-2">
                                <button class="btn btn-outline-dark" type="submit" value="1" name="sync_export_local">export archiv</button>
                            </div>
                            <div class="col col-2">
                                <button class="btn btn-outline-dark" type="button" onclick="$('#modal-synctemplate').modal('show');">save as template</button>
                            </div>
                            <div class="col col-2">
                                <select name="sync_loadFromTempl" class="form-select form-select-sm" onchange="document.form4.sync_export_remote.value='';document.form4.submit()"><option></option>
                                    <?php foreach($result_synctempl['name'] as $key => $value){?>
                                    <option value="<?=$key?>" ><?=$value?></option>";
                                    <?php }?>
                                    </select>
                                <label class="form-check-label">load from template
                                </label>
                            </div>
                        </div>

                        
                        <hr>


                        <div class="form-check mb-3">
                            <input class="form-check-input" type="radio" value="sync_to_host" name="sync_to_slaves" id="sync_to_host" onclick="openSyncClients(this,'syncHost')" <?=($sync_to_slaves == 'sync_to_host')?'checked':''?>>
                            <label class="fw-bold form-check-label">Sync to single host:
                            </label>
                        </div>

                        <div class="container" id="syncHost" style="display:<?=($sync_to_slaves == 'sync_to_host')?'':'none'?>">
                        <div class="row mb-2">
                            <label for="remoteHost" class="col-sm-2 col-form-label">Host:</label>
                            <div class="col-sm-10">
                                <input type="text" id="remoteHost" name="remoteHost" class="form-control form-control-sm" value="<?=$remoteHost?>" onchange="$('#sync_to_host').prop( 'checked', true )">
                            </div>
                        </div>
                        <div class="row mb-2">
                            <label for="remoteUser" class="col-sm-2 col-form-label">User:</label>
                            <div class="col-sm-10">
                                <input type="text" id="remoteUser" name="remoteUser" class="form-control form-control-sm" value="<?=$remoteUser?>" onchange="$('#sync_to_host').prop( 'checked', true )">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label for="remotePass" class="col-sm-2 col-form-label">Pass:</label>
                            <div class="col-sm-10">
                                <input type="password" id="remotePass" name="remotePass" class="form-control form-control-sm" value="<?=$remotePass?>" onchange="$('#sync_to_host').prop( 'checked', true )">
                            </div>
                        </div>
                            <hr>
                        </div>

                        <!-- sync to slaves -->
                        <?php if($slaves['count']){?>


                            <div class="form-check mb-3">
                                <input class="form-check-input" type="radio" value="sync_to_clients" name="sync_to_slaves" id="sync_to_clients" onclick="openSyncClients(this,'syncClients')" <?=($sync_to_slaves == 'sync_to_clients')?'checked':''?>>
                                <label class="fw-bold form-check-label">Sync to remote Clients: (<?=$slaves['count']?>)
                                </label>
                            </div>

                            <div class="container" id="syncClients" style="display:<?=($sync_to_slaves == 'sync_to_clients')?'':'none'?>">
                            <?php foreach($slaves['name'] as $slyve_key => $slave_name) {
                                if (!$slaves['slave_active'][$slyve_key]) {
                                    continue;
                                }?>
                                <div class="row">
                                    <div class="col-sm">
                                      <?=$slave_name?>
                                    </div>
                                    <div class="col-sm-7">
                                      <?=$slaves['slave_url'][$slyve_key]?>
                                    </div>
                                    <div class="col-sm-1">
                                      <input class="form-check-input" type="checkbox" value="1"  name="syncmodule[syncToSlavesActive][<?=$slyve_key?>]" <?=($syncmodule['syncToSlavesActive'][$slyve_key])?'checked':''?>>
                                    </div>
                                </div>
                            <?php  }?>
                            </div>

                        <hr>
                        <?php }?>

                        <input class="form-check-input" type="checkbox" value="1" name="precheck_level" <?=($precheck_level)?'checked':''?>>
                        <label class="form-check-label" for="part-exp-txt-encode">
                            show more details
                        </label>

                        <div class="text-end">
                            <button class="btn btn-primary" id="sync_export_remote_precheck" onclick="document.form4.sync_export_remote.value='precheck';form4.submit();">start remote precheck</button>
                            &nbsp;<button class="btn btn-warning" id="sync_export_remote_confirm" onclick="document.form4.sync_export_remote.value='confirm';form4.submit();">start remote export</button>
                        </div>


                    </FORM>
                </div>

            </div>









            <?php
            ob_flush();
            flush();

            set_time_limit(1000000);

            if($dump_export) {

                echo '<script language="JavaScript">
                    limbasWaitsymbol(false,false,true);
                    </script>';

                $path_backup = lmbExport_Dump($format,$struct_only);
                ?>



                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title"><?=$lang[970]?>!</h4>
                        <p>
                            <?=$lang[971]?> <?=$lang[973]?>:<br>
                            <?=$lang[577]?>: <span class="fw-bold"><?=$result_exp_tabs?></span><br>
                            <?=$lang[972]?>: <span class="fw-bold"><?=$result_exp_dat?></span>
                        </p>
                        <p>
                            <?=$lang[974]?>:<BR><BR>
                            <a href="<?=$path_backup?>" class="text-success" target="_blank"><i class="lmb-icon lmb-download"></i>&nbsp;<?=$path_backup?></a> (export dump)
                        </p>
                    </div>
                </div>

                <?php
            }elseif(($single_export OR $group_export) AND is_array($exptable)){
                if($result_backup = lmbExport($exptable,$format,$export_filter,null,$txt_encode)){
                    ?>


                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title"><?=$lang[970]?>!</h4>
                            <p>
                                <?=$lang[971]?> <?=$lang[973]?>:<br>
                                <?=$lang[577]?>: <span class="fw-bold"><?=$result_exp_tabs?></span><br>
                                <?=$lang[972]?>: <span class="fw-bold"><?=$result_exp_dat?></span>
                            </p>
                            
                            <?php if(array_key_exists('path', $result_backup)): ?>
                            <p><i class="lmb-icon lmb-download"></i> <?=$lang[975]?></p>
                            <?php endif; ?>
                            
                            <ul class="list-unstyled">
                                <?php
                                if(array_key_exists('path', $result_backup)):
                                    foreach ($result_backup["path"] as $key => $value): ?>
                                        <li><a name="download" href="<?=$value?>" class="text-success" target="_blank"><?=$result_backup["name"][$key]?></a></li>
                                    <?php endforeach;
                                    
                                    if(lmb_count($result_backup["path"]) > 1): ?>
                                    <li class="pt-3"><i class="lmb-icon lmb-collapse-all"></i><a href="#" onclick="document.form1.make_package.value=1;document.form1.submit();"><?=$lang[977]?>!</a></li>   
                                <?php
                                    endif;
                                    
                                else: ?>

                                    <li><a name="download" HREF="<?=$result_backup?>" class="text-success" target="_blank"><?=$result_backup?></a></I></li>
                                    <script>limbasWaitsymbol(false,false,true);</script>
                                
                                <?php endif; ?>
                            </ul>
                                
                        </div>
                    </div>

                    <?php
                }

            }elseif($make_package){
                $path = "USER/".$session["user_id"]."/temp";
                if($path_ = make_fileArchive($path,"export_dump")): ?>

                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title"><?=$lang[970]?>!</h4>
                            <p><i class="lmb-icon lmb-download"></i> <?=$lang[975]?></p>
                            <p><a name="download" href="<?=$path_?>" class="text-success" target="_blank"><?=$path_?></a></p>

                        </div>
                    </div>
            
            <?php
                    
                endif;
            }

            // Sync export
            elseif($sync_to_slaves && ($sync_export_local || $sync_export_remote || $sync_export_config))
            {

                // get active modules
                $syncModules = lmb_activeSyncModules();

                if(count($syncModules) <= 0){
                    lmb_alert('no modules selected!');
                    return;
                }

                # export config as file
                if ($sync_export_config) :
                    $configFilePath = $umgvar['path'] . '/TEMP/conf/autosync.conf.php';
                    $handle = fopen($configFilePath, 'w+');
                    fwrite($handle, '<?php $syncModules = ' . var_export($syncModules, true) . '; ?>');
                    fclose($handle);
                    ?>
                
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title"><?=$lang[970]?>!</h4>
                            <p><i class="lmb-icon lmb-download"></i> <?=$lang[975]?></p>
                            <p><a name="download" href="TEMP/conf/autosync.conf.php" class="text-success" target="_blank">autosync.conf.php</a></p>

                        </div>
                    </div>
                
                    <?php
                    
                    return;
                endif;

                # local export
                if($sync_export_local) {
                    if($result_backup = lmbExport(null,$format,null,$syncModules,$txt_encode)){
                        ?>

                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title"><?=$lang[970]?>!</h4>
                                <p>
                                    <?=$lang[971]?> <?=$lang[973]?>:<br>
                                    <?=$lang[577]?>: <span class="fw-bold"><?=$result_exp_tabs?></span><br>
                                    <?=$lang[972]?>: <span class="fw-bold"><?=$result_exp_dat?></span>
                                </p>

                                <p><i class="lmb-icon lmb-download"></i> <?=$lang[975]?></p>
                                <p><a name="download" href="<?=$result_backup?>" class="text-success" target="_blank"><?=$result_backup?></a></p>

                            </div>
                        </div>
                        
                        <?php
                    }


                # remote export
                } else if($sync_export_remote AND $syncModules) { ?>

                    <div class="card">
                        <div class="card-body">
                            
                    <?php

                    // use single host
                    if($sync_to_slaves == 'sync_to_host' AND $remoteHost) {
                        $slaves = array();
                        $slaves['name'][0] = $remoteHost;
                        $slaves['slave_url'][0] = $remoteHost;
                        $slaves['slave_username'][0] = $remoteUser;
                        $slaves['slave_pass'][0] = $remotePass;
                        $slaves['slave_active'][0] = 1;
                        $sync_to_host = 1;
                    }

                    ?>
                    <table class="table table-sm table-striped mb-0 border bg-white">
                        <thead>
                        <tr>
                            <th>Name</th>
                            <th>Status</th>
                            <th>Checksum</th>
                            <th></th>
                        </tr>
                        </thead>

                        <tbody>
                    <?php

                    foreach($slaves['name'] as $slyve_key => $slave_name){
                        if(!$sync_to_host AND (!$slaves['slave_active'][$slyve_key] OR !$syncmodule['syncToSlavesActive'][$slyve_key])){continue;}
                        ?>

                        <tr>
                            <td >
                                <?=$slave_name?>
                            </td>
                            <td >
                                <div class="sync-precheck" data-precheck="false" data-id="<?=$slyve_key?>"></div>
                            </td>
                            <td>
                                <div class="sync-schecksum" id=checksum_<?=$slyve_key?>></div>
                            </td>
                            <td >
                                <div class="sync-status" data-status="false" data-id="<?=$slyve_key?>"></div>
                            </td>
                        </tr>

                    <?php }?>

                        </tbody>
                    </table>

                    <script>
                        stamp = Math.floor(Date.now() / 1000);
                        start_syncExport(stamp);
                    </script>

                    </div>
                    </div>
                <?php
                }
            }
            ?>
            
        </div>
    </div>
    
</div>
