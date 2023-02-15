<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */



#/*----------------- Aktions-Eintrag -------------------*/
#if(isset($aktion_value) AND $report_id AND !$del) {
#	$sqlquery = "UPDATE LMB_REPORT_LIST SET SQL_STATEMENT = '".parse_db_string($aktion_value,255)."' WHERE ID = $report_id";
#	$rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
#	if(!$rs) {$commit = 1;}
#}



require_once(COREPATH . 'admin/report/report.lib');

if($new AND $reportcopy){
    $newreport_id = lmb_report_copy($reportcopy,$report_name);
}
elseif($new){
    $newreport_id = lmb_report_create($report_name,$reporttarget,$report_desc,$referenz_tab,$reportextension,$action,$report_format);
}

if($changedefaultformat_id){
    lmb_report_format($changedefaultformat_id);
}
if($changeext_id){
    lmb_report_extension($changeext_id);
}
if($changetarget_id){
    lmb_report_target($changetarget_id);
}
if($changename_id){
    lmb_report_rename($changename_id);
}
if($savename_id){
    lmb_report_savename($savename_id);
}
if($changedefaultformat_id){
    lmb_report_format($changedefaultformat_id);
}
if ($del AND $report_id) {
    lmb_report_delete($report_id);
}

$greport = resultreportlist();



?>

<Script language="JavaScript">


function submit_form(act,targ){
	document.form1.action.value = act;
	document.form1.action = targ;
	alert(document.form1.action);
	//document.form1.submit();
}

// Layer positionieren und öffnen
function setxypos(evt,el) {
    document.getElementById(el).style.left = evt.pageX;
    document.getElementById(el).style.top = evt.pageY;
}

function element1(evt,ID) {
	setxypos(evt,'element1');
	document.getElementById('element1').style.visibility='visible';
	document.form1.report_id.value = ID;
	eval("document.form1.aktion_value.value = document.form1.report_"+ID+".value");
}

function report_delete(ID){
	var del = confirm('<?=$lang[2284]?>');
	if(del){
		document.location.href="main_admin.php?&action=setup_report_select&del=1&report_id="+ID;
	}
}

function lmb_setOOTemplate(el,val){
	if(!val){return;}
	var files = val.split('-');
	var el_ = el.split('-');
	elid = el_[0];
	format = el_[1];
	var typ = files[0].substr(0,1);
	if(typ != 'd'){alert('only files permitted!');return;}
	var file = files[0].substring(2);
	if(!file || !elid || !document.form1.elements['report_'+format+'_template['+elid+']']){return;}
	document.form1.elements['report_'+format+'_template['+elid+']'].value=file;
	document.form1.changeootemplate_id.value=el;
	document.form1.action.value='setup_report_select';
	document.form1.submit();
}

/* --- miniexplorer-Fenster ----------------------------------- */
function lmb_openMiniexplorer(elid,format) {
	miniexplorer=open("main.php?&action=mini_explorer&funcname=lmb_setOOTemplate&funcpara=<?=rawurlencode("'")?>"+elid+"-"+format+"<?=rawurlencode("'")?>" ,"miniexplorer","toolbar=0,location=0,status=0,menubar=0,scrollbars=1,resizable=1,width=410,height=320");
}

</Script>



<div class="container-fluid p-3">
    <FORM ACTION="main_admin.php" METHOD="post" NAME="form1">
        <input type="hidden" name="action" value="setup_report_select">
        <input type="hidden" name="referenz_tab" value="<?=$referenz_tab?>">
        <input type="hidden" name="report_id">
        <input type="hidden" name="rename_id">
        <input type="hidden" name="savename_id">
        <input type="hidden" name="changename_id">
        <input type="hidden" name="changetarget_id">
        <input type="hidden" name="changeext_id">
        <input type="hidden" name="changeootemplate_id">
        <input type="hidden" name="changedefaultformat_id">

        <table class="table table-sm table-striped mb-0 border bg-white">

            <thead>
                <tr>
                    <th>ID</th>
                    <th></th>
                    <th><?=$lang[160]?></th>
                    <th><?=$lang[1137]?></th>
                    <th><?=$lang[1162]?></th>
                    <th><?=$lang[2509]?></th>
                    <th><?=$lang[2111]?></th>
                    <th><?=$lang[2511]?></th>
                    <th>Report Class</th>
                    <!--<th><?=$lang[1161]?></th>
                    <th><?=$lang[1161]?></th>
                    -->
                </tr>
            </thead>

            <?php
            #----------------- Berichte -------------------
            

            if($greport):
                
                # Extension Files
                $extfiles = read_dir(EXTENSIONSPATH,1);

                foreach ($greport as $gtabid => $value0):

                    if($gtabid == 'gtabid'){continue;}

                    if($gtab["table"][$gtabid]){
                        $cat = $gtab["desc"][$gtabid];
                    }else{
                        $cat = $lang[1986];
                    }
                    
                    ?>
                    <tr class="table-section"><td colspan="12"><?=$cat?></td></tr>
                
                    
                    <?php
                    
                    if($greport[$gtabid]["id"]):
                        foreach ($greport[$gtabid]["id"] as $key => $value):
                            ?>
                        
                            <tr <?=($value == $newreport_id)?'class="alert alert-success"':''?>>
                                <td <?=(!$greport[$gtabid]["grouplist"][$key])?'':'class="text-danger"'?>><?=$key?></td>
                                <td>
                                    <?php if(!$greport[$gtabid]["grouplist"][$key] AND !$greport[$gtabid]["extension"][$key]){ ?>
                                        <a href="main_admin.php?action=setup_report_frameset&report_id=<?=$key?>&referenz_tab=<?=$gtabid?>"><i class="lmb-icon lmb-pencil cursor-pointer"></i></a>
                                    <?php } ?>
                                </td>
                                <td><i OnClick="report_delete('<?=$key?>')" class="lmb-icon lmb-trash cursor-pointer"></i></td>
                                <td><INPUT TYPE="TEXT" NAME="report_changename[<?=$key?>]" VALUE="<?=$greport[$gtabid]["name"][$key]?>" STYLE="width:160px;" OnChange="document.form1.changename_id.value='<?=$key?>';document.form1.action='main_admin.php';document.form1.action.value='setup_report_select';document.form1.submit();" class="form-control form-control-sm"></td>
                                <td><?=$gtab["desc"][$gtabid]?></td>
                                <td>
                                    <?php if($extfiles["name"]){ ?>

                                        <SELECT NAME="report_extension[<?=$key?>]" OnChange="document.form1.changeext_id.value='<?=$key?>';document.form1.action='main_admin.php';document.form1.action.value='setup_report_select';document.form1.submit();" style="width:100px;" class="form-select form-select-sm">
                                            <option></option>
                                                
                                                
                                        <?php
                                        foreach ($extfiles["name"] as $key1 => $filename){
                                            if($extfiles["typ"][$key1] == "file" AND ($extfiles["ext"][$key1] == "ext" OR $extfiles["ext"][$key1] == "php")){
                                                $path = lmb_substr($extfiles["path"][$key1],lmb_strlen($umgvar["pfad"]),100);
                                                if($path.$filename == $greport[$gtabid]["extension"][$key]){$SELECTED = "SELECTED";}else{$SELECTED = "";}
                                                echo "<option VALUE=\"".$path.$filename."\" $SELECTED>".str_replace("/EXTENSIONS/","",$path).$filename.'</option>';
                                            }
                                        } ?>
                                        
                                            
                                        </SELECT>
                                                
                                    <?php } ?>
                                </td>
                                <td nowrap>
                                    <div class="input-group">
                                    <SELECT OnChange="document.getElementById('report_changetarget_<?=$key?>').value=this.value;document.form1.changetarget_id.value='<?=$key?>';document.form1.action='main_admin.php';document.form1.action.value='setup_report_select';document.form1.submit();" style="width:100px;" class="form-select form-select-sm">
                                        <OPTION VALUE="0"></OPTION>
                                        <?php
                                        if($gtabid){
                                            $sqlquery = "SELECT ID FROM LDMS_STRUCTURE WHERE TAB_ID = $gtabid AND FIELD_ID = 0 AND TYP = 3";
                                            $rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
                                            if(!$rs){$commit = 1;}
                                            while(lmbdb_fetch_row($rs)){
                                                echo rep_sub_folderlist(lmbdb_result($rs,"ID"),$greport[$gtabid]["target"][$key]);
                                            }
                                        }?>
                                    </SELECT><input type="text" style="width:30px;" id="report_changetarget_<?=$key?>" name="report_changetarget[<?=$key?>]" value="<?=$greport[$gtabid]["target"][$key]?>" OnChange="document.form1.changetarget_id.value='<?=$key?>';document.form1.action='main_admin.php';document.form1.action.value='setup_report_select';document.form1.submit();" class="form-control form-control-sm">
                                    </div>
                                </td>
                                <td><INPUT TYPE="TEXT" NAME="report_savename[<?=$key?>]" STYLE="width:100px" VALUE="<?=htmlentities($greport[$gtabid]["savename"][$key],ENT_QUOTES,$GLOBALS["umgvar"]["charset"])?>" OnChange="document.form1.savename_id.value='<?=$key?>';document.form1.action='main_admin.php';document.form1.action.value='setup_report_select';document.form1.submit();" class="form-control form-control-sm"></td>
                                <td>
                                    <select readonly name="report_defaultformat[<?=$key?>]" style="width:80px;" class="form-select form-select-sm"  OnChange="document.form1.changedefaultformat_id.value='<?=$key?>';document.form1.action.value='setup_report_select';document.form1.submit();">
                                        <?php
                                        foreach (['tcpdf','mpdf'] as $filetype) {
                                            echo "<option  value=\"$filetype\" ".(($greport[$gtabid]["defaultformat"][$key] == $filetype)?'selected':'').">$filetype</option>";
                                        } ?>
                                    </select>
                                </td>
                                <!--
                                <td nowrap><div class="input-group"><INPUT TYPE="TEXT" NAME="report_odt_template[<?=$key?>]" VALUE="<?=$greport[$gtabid]["odt_template"][$key]?>" STYLE="width:100px" onchange="document.form1.changeootemplate_id.value='<?=$key?>-odt';document.form1.action.value='setup_report_select';document.form1.submit();" class="form-control form-control-sm">&nbsp;<i class="lmb-icon lmb-file-odt cursor-pointer" onclick="lmb_openMiniexplorer(<?=$key?>,'odt');" title="odt-template"></i></div></td>
                                <td nowrap><div class="input-group"><INPUT TYPE="TEXT" NAME="report_ods_template[<?=$key?>]" VALUE="<?=$greport[$gtabid]["ods_template"][$key]?>" STYLE="width:100px" onchange="document.form1.changeootemplate_id.value='<?=$key?>-ods';document.form1.action.value='setup_report_select';document.form1.submit();" class="form-control form-control-sm">&nbsp;<i class="lmb-icon lmb-file-ods cursor-pointer" onclick="lmb_openMiniexplorer(<?=$key?>,'ods');" title="ods-template"></i></div></td>
                                -->
                            </tr>

                            <input type="hidden" name="report_<?=$key?>" VALUE="<?=htmlentities($greport[$gtabid]["sql"][$key],ENT_QUOTES,$GLOBALS["umgvar"]["charset"])?>">
                        
                            <?php
                            
                        endforeach;

                    endif;

                endforeach;

            endif;

            



            ?>

            <tfoot>
            
            
            <tr>
                <th colspan="3"></th>
                <th><?=$lang[4]?></th>
                <th><?=$lang[164]?></th>
                <th><?=$lang[1163]?></th>
                <th><?=$lang[1464]?></th>
                <th colspan="5"></th>
            </tr>

            <tr>
                <td colspan="3"></td>
                <td><input type="text" name="report_name" class="form-control form-control-sm"></td>
                <td>
                    <SELECT NAME="referenz_tab" class="form-select form-select-sm">
                        <OPTION VALUE="-1"></OPTION>
                        <?php
                        foreach ($tabgroup["id"] as $key0 => $value0) {
                            echo '<optgroup label="' . $tabgroup["name"][$key0] . '">';
                            foreach ($gtab["tab_id"] as $key => $value) {
                                if($gtab["tab_group"][$key] == $value0){
                                    echo "<OPTION VALUE=\"".$value."\">".$gtab["desc"][$key]."</OPTION>";
                                }
                            }
                            echo '</optgroup>';
                        }
                        ?>
                    </SELECT>                    
                </td>

                <td>
                    <select name="report_format" class="form-select form-select-sm">
                        <option value="tcpdf">tcpdf</option>
                        <option value="mpdf">mpdf</option>
                    </select>
                </td>
                <td>
                    <SELECT NAME="reportcopy" class="form-select form-select-sm">
                        <OPTION VALUE="0"></OPTION>
                        <?php
                        if($greport){
                            foreach ($greport as $gtabid => $value0){
                                if($greport[$gtabid]["id"]){
                                    foreach ($greport[$gtabid]["id"] as $key => $value){
                                        echo "<OPTION VALUE=\"".$key."\">".$greport[$gtabid]["name"][$key]."</OPTION>\n";
                                    }
                                }
                            }
                        }
                        ?>
                    </SELECT>
                </td>
                <td><button type="submit" name="new" class="btn btn-primary btn-sm" value="1"><?=$lang[1165]?></button></td>
                <td colspan="4"></td>
            </tr>
            </tfoot>

        </table>
        
        
    </FORM>

</div>
