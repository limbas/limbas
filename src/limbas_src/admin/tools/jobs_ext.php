<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */




?>

<script language="JavaScript">
var img3=new Image();img3.src="assets/images/legacy/outliner/plusonly.gif";
var img4=new Image();img4.src="assets/images/legacy/outliner/minusonly.gif";

function popup(ID,LEVEL){
    var nested = $('#f_' + ID).next().get(0);
	var picname = "i" + ID;
	if(document.images[picname].src == img4.src) {
		document.images[picname].src = img3.src;
		nested.style.display="none";
	}else{
		document.images[picname].src = img4.src;
		nested.style.display='';
	}
}

function popup_all(fid){
    $('#f_' + fid)
        .next().show()
        .parents().show();
}

function close_all(){
    $('div[id^="f_"]').next().hide();
}

function show_val(val) {
	if(val){
		var arr1 = val.split(";");
		for (var i in arr1){
			var arr2 = arr1[i].split(",");
			if(arr2[0] == "field"){
				var el = "memo_" + arr2[1];
				if(document.form1[el]){
					document.form1[el].checked = 1;
				}
			}else if(arr2[0] == "file"){
				var el = document.getElementById("ifile_"+arr2[1]);
				if(el){
					if(arr2[2] == "s"){document.form1['subdir'].checked = 1;}
					popup_all(arr2[1]);
					el.checked = 1;
				}
			}
		}
	}else{
        $('input[name^="ifile"]').prop('checked', false);
        $('input[name^="memo"]').prop('checked', false);
		document.form1['subdir'].checked = 0;
		close_all();
	}
}

function index_del(val) {
	if(confirm('delete job?')){
		document.form1.del_index.value = val;
		document.form1.submit();
	}
}

function index_refresh(val) {
	if(confirm('renew job?')){
		document.form1.refresh_index.value = val;
		document.form1.submit();
	}
}
</script>




<FORM ACTION="main_admin.php" METHOD="post" name="form1">
    <input type="hidden" name="action" VALUE="setup_indize_db">
    <input type="hidden" name="del_index">
    <input type="hidden" name="refresh_index">
    <input type="hidden" name="templupdate">
    <input type="hidden" name="kategorie" value="<?= $kategorie ?>">
    
    <div class="container-fluid p-3">

        

        <ul class="nav nav-tabs">


            <?php foreach ($jobdir['name'] as $key => $jobName): ?>
            
                <li class="nav-item">
                    <a class="nav-link <?= ($kategorie == $jobName) ? 'active bg-contrast' : '' ?>" onclick="document.form1.kategorie.value='<?= $jobName ?>';document.form1.submit();"><?= $jobName ?></a>
                </li>
            <?php endforeach; ?>
            
        </ul>
        <div class="tab-content">
            <div class="tab-pane active">

                <div class="row">
                    <div class="col-md-10">
                        <table class="table table-sm table-striped mb-0 border border-top-0 bg-contrast">
                            <thead>
                            <tr>
                                <th class="border-top-0"><?=$lang[2068]?></th>
                                <th class="border-top-0"><?=$lang[1749]?></th>
                                <th class="border-top-0">template</th>
                                <th class="border-top-0"><?=$lang[2070]?></th>
                                <th class="border-top-0"><?=$lang[1242]?></th>
                                <th class="border-top-0"><?=$lang[126]?></th>
                                <th class="border-top-0"><?=$lang[2072]?></th>
                                <th class="border-top-0">start</th>
                                <th class="border-top-0"><?=$lang[160]?></th>
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

                            $sqlquery = "SELECT ID,KATEGORY,START,VAL,ERSTDATUM,ACTIV,DESCRIPTION,ALIVE,JOB_USER FROM LMB_CRONTAB WHERE KATEGORY = '" . lmb_strtoupper($kategoriedesc) . "' ORDER BY ERSTDATUM";
                            $rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);

                            while(lmbdb_fetch_row($rs)):
                                $tid = lmbdb_result($rs,"ID");
                                $val = lmbdb_result($rs,"VAL");
                                $category = lmbdb_result($rs,"KATEGORY") ;
                                $val0 = explode(";",$val);
                                $color = '';
                                if(lmbdb_result($rs,"KATEGORY") == "INDIZE"){
                                    $color = "#7AB491";
                                }
                                $activ = '';
                                if(lmbdb_result($rs,"ACTIV")){
                                    $activ = "CHECKED";
                                }
                                $template = '';
                                if(lmbdb_result($rs,"KATEGORY") == "TEMPLATE" OR lmbdb_result($rs,"KATEGORY") == "STRUCTURESYNC" OR lmbdb_result($rs,"KATEGORY") == "RSYNC" OR lmbdb_result($rs,"KATEGORY") == "DATASYNC"){
                                    $template = $val0[0];
                                }

                                if($category == "TEMPLATE"){
                                    $template = $val0[0];
                                }elseif($category == "STRUCTURESYNC"){
                                    $template = $cat['structursync'][$val0[0]];
                                }elseif($category == "DATASYNC" OR $category == "RSYNC"){
                                    $template = $cat['datasync'][$val0[0]];
                                }



                                ?>

                                <tr onclick="show_val('');show_val('<?=$val?>');">
                                    <td><?=lmbdb_result($rs,"ID")?></td>
                                    <td class="text-nowrap"><?=lmbdb_result($rs,"KATEGORY")?> </td>
                                    <td><?=$template?></td>
                                    <td><?=lmbdb_result($rs,"START")?></td>
                                    <td><?=lmbdb_result($rs,"JOB_USER")?></td>
                                    <td><?=lmbdb_result($rs,"DESCRIPTION")?></td>
                                    <td><input type="checkbox" name="activ_<?=lmbdb_result($rs,"ID")?>" onclick="document.location.href='main_admin.php?&action=setup_indize_db&kategorie=<?=$kategorie?>&activate_job=<?=lmbdb_result($rs,"ID")?>'" <?=$activ?>></td>
                                    <td><i class="lmb-icon lmb-action" name="activate_<?=lmbdb_result($rs,"ID")?>" OnClick="document.location.href='main_admin.php?&action=setup_indize_db&kategorie=<?=$kategorie?>&run_job=<?=lmbdb_result($rs,"ID")?>';limbasWaitsymbol(event,1);"></i></td>
                                    <td><A HREF="main_admin.php?&action=setup_indize_db&kategorie=<?=$kategorie?>&del_job=<?=lmbdb_result($rs,"ID")?>"><i class="lmb-icon lmb-trash"></i></A></td>
                                </tr>

                                <?php


                                #$cronvalue[] = str_replace(";"," ",lmbdb_result($rs,"START"))."\t php \"".$umgvar["pfad"]."/cron.php\" ".lmbdb_result($rs,"ID")." ".lmbdb_result($rs,"JOB_USER");

                                endwhile;
                            ?>

                            </tbody>


                        </table>



                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Cron</h5>

                                <table class="table table-sm mb-0 table-borderless">
                                    <tr class="small">
                                        <th><?=$lang[2074]?></th>
                                        <th><?=$lang[2075]?></th>
                                        <th><?=$lang[2076]?></th>
                                        <th><?=$lang[1437]?></th>
                                        <th><?=$lang[2078]?></th>
                                        <th class="w-25"><?=$lang[1242]?></th>
                                        <th></th>
                                    </tr>
                                    <TR>
                                        <TD><INPUT TYPE="TEXT" NAME="cron[0]" VALUE="0" class="form-control form-control-sm"></TD>
                                        <TD><INPUT TYPE="TEXT" NAME="cron[1]" VALUE="1" class="form-control form-control-sm"></TD>
                                        <TD><INPUT TYPE="TEXT" NAME="cron[2]" VALUE="*"  class="form-control form-control-sm"></TD>
                                        <TD><INPUT TYPE="TEXT" NAME="cron[3]" VALUE="*" class="form-control form-control-sm"></TD>
                                        <TD><INPUT TYPE="TEXT" NAME="cron[4]" VALUE="*" class="form-control form-control-sm"></TD>
                                        <TD class="px-2">
                                            <div class="input-group input-group-sm">
                                            <span class="input-group-text">
                                                <i class="lmb-icon lmb-user-alt cursor-pointer"></i>
                                            </span>
                                                <input id="job_user_input" type="text" class="form-control" name="job_user">
                                            </div>
                                        </TD>
                                        <td class="text-end"><button type="submit" NAME="add_job" value="1" class="btn btn-primary btn-sm text-nowrap"><?=$lang[2079]?></button></td>
                                    </TR>
                                </table>

                            </div>
                        </div>

                        

                                <?php
                                if($kategoriedesc == "INDIZE" OR $kategoriedesc == "OCR"){
                                    ?>
                        <div class="card">
                            <div class="card-body">
                                    <TABLE BORDER="0" cellspacing="0" cellpadding="0">
                                        <TR>
                                            <TD VALIGN="TOP">
                                                <TABLE cellspacing="0" STYLE="border:1px solid <?=$farbschema["WEB4"]?>;">
                                                    <TR>
                                                        <TD STYLE="border-bottom:1px solid <?=$farbschema['WEB4']?>;background-color:<?=$farbschema['WEB7']?>"><?=$lang[2080]?></TD>
                                                        <TD VALIGN="TOP" STYLE="border-bottom:1px solid <?=$farbschema['WEB4']?>;background-color:<?=$farbschema['WEB7']?>"><?=$lang[2081]?>
                                                            <input type="checkbox" name="subdir">&nbsp;&nbsp;
                                                        </TD>
                                                    </TR>
                                                    <TR>
                                                        <TD COLSPAN="2">
                                                            <?php

                                                            # --- Dateiordner ---
                                                            function files1($LEVEL){
                                                                global $file_struct;

                                                                if($LEVEL){
                                                                    echo "<div id=\"foldinglist\" style=\"display:none\">\n";
                                                                    echo "<TABLE CELLPADDING=\"0\" CELLSPACING=\"0\" BORDER=\"0\" WIDTH=\"100%\" STYLE=\"border-collapse:collapse;\"><TR><TD WIDTH=\"10\">&nbsp;</TD><TD>\n";
                                                                }
                                                                $bzm = 0;
                                                                while($file_struct["id"][$bzm]){
                                                                    if($file_struct['level'][$bzm] == $LEVEL){
                                                                        if(in_array($file_struct["id"][$bzm],$file_struct['level'])){
                                                                            $next = 1;
                                                                            $pic = "<IMG SRC=\"assets/images/legacy/outliner/plusonly.gif\" NAME=\"i".$file_struct["id"][$bzm]."\" OnClick=\"popup('".$file_struct["id"][$bzm]."','$LEVEL')\" STYLE=\"cursor:hand\">";
                                                                        }else{
                                                                            $next = 0;
                                                                            $pic = "<IMG SRC=\"assets/images/legacy/outliner/blank.gif\">";
                                                                        }
                                                                        echo "<div ID=\"f_".$file_struct["id"][$bzm]."\"><TABLE CELLPADDING=\"0\" CELLSPACING=\"0\" BORDER=\"0\" WIDTH=\"100%\">";
                                                                        echo "<TR><TD WIDTH=\"20\">$pic</TD><TD WIDTH=\"20\"><i class=\"lmb-icon lmb-folder-closed\"></i></TD><TD>&nbsp;".$file_struct['name'][$bzm]."</TD>";
                                                                        echo "<TD ALIGN=\"RIGHT\"><TABLE CELLPADDING=\"0\" CELLSPACING=\"0\" BORDER=\"0\"><TR>";
                                                                        # --- view ---
                                                                        if(!$file_struct['readonly'][$bzm]){
                                                                            echo "<TD><INPUT TYPE=\"CHECKBOX\" ID=\"ifile_".$file_struct["id"][$bzm]."\" NAME=\"ifile[".$file_struct["id"][$bzm]."]\" STYLE=\"border:none;background-color:transparent;\"></TD>";
                                                                            echo "<TD><i class=\"lmb-icon lmb-trash\" TITLE=\"drop jobresults\" OnClick=\"index_del('file_".$file_struct["id"][$bzm]."');\"></i></TD><TD><i class=\"lmb-icon lmb-action\" TITLE=\"run job\" BORDER=\"0\" STYLE=\"cursor:pointer\" OnClick=\"index_refresh('file_".$file_struct["id"][$bzm]."');\"></i></TD>";
                                                                        }
                                                                        echo "</TR></TABLE></TD></TR></TABLE></div>\n";
                                                                        if($next){
                                                                            files1($file_struct["id"][$bzm]);
                                                                        }else{
                                                                            echo "<div id=\"foldinglist\" style=\"display:none;\"></div>\n";
                                                                        }

                                                                    }
                                                                    $bzm++;
                                                                }
                                                                if($LEVEL){
                                                                    echo "</TD></TR></TABLE>\n";
                                                                    echo "</div>\n";
                                                                }
                                                            }
                                                            files1(0);
                                                            ?>
                                                        </TD>
                                                    </TR>
                                                </TABLE>
                                            </TD>
                                            <TD>&nbsp;&nbsp;&nbsp;</TD>

                                            <?php
                                            # --- Tabellenfelder ---
                                            if($gtab["table"] AND $kategoriedesc == "INDIZE"){
                                                foreach($gtab["table"] as $key => $value){
                                                    if($gfield[$key]["indize"]){
                                                        if(in_array("1",$gfield[$key]["indize"])){
                                                            echo "<TD VALIGN=\"TOP\"><TABLE cellspacing=\"0\" cellpadding=\"3\" STYLE=\"border:1px solid ".$farbschema['WEB4'].";\"><TR><TD COLSPAN=\"4\" STYLE=\"border-bottom:1px solid ".$farbschema['WEB4'].";background-color:".$farbschema['WEB7']."\">".$gtab['desc'][$key]."</TD></TR>\n";
                                                            foreach($gfield[$key]["id"] as $key1 => $value1){
                                                                if($gfield[$key]['indize'][$key1] AND $gfield[$key]['data_type'][$key1] == 39){
                                                                    echo "<TR><TD STYLE=\"border:none;color:green\" TITLE=\"".$gfield[$key]['beschreibung'][$key1]."\">".$gfield[$key]['spelling'][$key1]."</TD><TD><INPUT TYPE=\"CHECKBOX\" STYLE=\"border:none;background-color:transparent\" NAME=\"memo_".$key."_".$key1."\"></TD><TD><i class=\"lmb-icon lmb-trash\" TITLE=\"drop Index\" BORDER=\"0\" STYLE=\"cursor:pointer\" OnClick=\"index_del('field_".$key."_".$key1."');\"></i><TD><i class=\"lmb-icon lmb-action\" TITLE=\"renew Index\" BORDER=\"0\" STYLE=\"cursor:pointer\" OnClick=\"index_refresh('field_".$key."_".$key1."');\"></i></TD></TR>\n";
                                                                }
                                                            }
                                                            echo "</TABLE></TD><TD>&nbsp;&nbsp;&nbsp;</TD>\n";
                                                        }
                                                    }
                                                }
                                            }
                                            ?>
                                        </tr>
                                    </TABLE>
                            </div>
                        </div>
                                    <?php
                                } else if($kategoriedesc == "TEMPLATE" || $kategoriedesc == "DATASYNC" || $kategoriedesc == "STRUCTURESYNC" || $kategoriedesc == "RSYNC"){
                                    ?>
                        <div class="card">
                            <div class="card-body">
                                
                                <div class="row">
                                    <div class="mb-3 col-3 mb-0">
                                        <label><?=$lang[2207]?> (*.job)</label>
                                        <select class="form-select form-select-sm" NAME="job_template">
                                            <option></option>
                                                <?php
                                                
                                                if ($kategoriedesc == "TEMPLATE") {
                                                    $extfiles = read_dir(EXTENSIONSPATH,1);

                                                    foreach ($extfiles["name"] as $key1 => $filename){
                                                        $ext = explode(".",$filename);
                                                        $ext = $ext[lmb_count($ext)-1];
                                                        if($extfiles["typ"][$key1] == "file" AND lmb_strtolower($ext) == "job"){
                                                            $path = lmb_substr($extfiles["path"][$key1],lmb_strlen($umgvar["pfad"]),100);
                                                            echo '<option value="'.$path.$filename."\">".str_replace("/EXTENSIONS/","",$path).$filename.'</option>';
                                                        }
                                                    }
                                                } elseif ($kategoriedesc == "DATASYNC" || $kategoriedesc == "RSYNC") {
                                                    foreach($cat['datasync'] as $key => $value) {
                                                        echo '<option value="'.$key."\">".$value.'</option>';
                                                    }
                                                } elseif ($kategoriedesc == "STRUCTURESYNC") {
                                                    foreach($cat['structursync'] as $key => $value) {
                                                        echo '<option value="'.$key."\">".$value.'</option>';
                                                    }
                                                }
                                                
                                                ?>
                                        </select>
                                    </div>
                                    <div class="mb-3 col-5 mb-0">
                                        <label><?=$lang[126]?></label>
                                        <INPUT TYPE="TEXT" NAME="job_desc" class="form-control form-control-sm">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php

                                }
                        ?>

                    </div>
                </div>

            </div>
        </div>

        

    </div>
</FORM>
