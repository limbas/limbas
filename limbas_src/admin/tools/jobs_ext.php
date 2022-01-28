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
 * ID: 212
 */


?>

<script language="JavaScript">
var img3=new Image();img3.src="pic/outliner/plusonly.gif";
var img4=new Image();img4.src="pic/outliner/minusonly.gif";

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


function lmb_UGtype(usgr,name,gtabid,fieldid,ID,parameter) {
    $('#fjob_user').val(usgr);
    $('#job_user_input').val(name);
    $('#g_cj_cjlmb_UGtypeuser').hide();
}
</script>

<?php

//TODO: dialog not closing
$userfield = '<i class="lmb-icon lmb-user-alt" style="cursor:pointer" onclick="activ_menu=1;lmbAjax_showUserGroupsSearch(event,\'*\',\'0\',\'cj\',\'cj\',\'lmb_UGtype\',\'g_cj_cj\',\'user\')"></i>&nbsp;<input id="job_user_input" type="text" style="width:80%;" ondblclick="this.value=\'*\';lmbAjax_showUserGroupsSearch(event,this.value,\'0\',\'cj\',\'cj\',\'lmb_UGtype\',\'g_cj_cj\',\'user\',\'\');" \'="" onkeyup="lmbAjax_showUserGroupsSearch(event,this.value,\'0\',\'cj\',\'cj\',\'lmb_UGtype\',\'g_cj_cj\',\'user\',\'\');">
			<div id="g_cj_cjlmb_UGtypeuser" class="ajax_container" style="display:none;position:absolute;border:1px solid black;padding:2px;background-color:#FDFEFD"></div>';

?>

<div id="lmbAjaxContainer" class="ajax_container" style="position:absolute;display:none;" OnClick="activ_menu=1;"></div>
<form action="main_admin.php" method="post" name="form1">
    <input type="hidden" name="action" VALUE="setup_indize_db">
    <input type="hidden" name="del_index">
    <input type="hidden" name="refresh_index">
    <input type="hidden" name="templupdate">
    <input type="hidden" name="kategorie" value="<?= $kategorie ?>">

    <div class="lmbPositionContainerMainTabPool">
        <table class="tabpool" cellspacing="0" cellpadding="0">
            <tbody>
            <tr>
                <td>
                    <table cellspacing="0" style="width: 100%;">
                        <tr>
                            <?php foreach ($jobdir['name'] as $key => $jobName): ?>
                                <td nowrap class="<?= ($kategorie == $jobName) ? 'tabpoolItemActive' : 'tabpoolItemInactive' ?>" onclick="document.form1.kategorie.value='<?= $jobName ?>';document.form1.submit();">
                                    <?= $jobName ?>
                                </td>
                            <?php endforeach; ?>
                            <td class="tabpoolItemSpace"></td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td class="tabpoolfringe">
                    <table cellspacing="1" cellpadding="2" style="width: 100%; cursor: pointer;" class="hoverable">
                        <tr class="tabHeader">
                            <td class="tabHeaderItem"><?=$lang[2068]?></td>
                            <td class="tabHeaderItem"><?=$lang[1749]?></td>
                            <td class="tabHeaderItem"><?=$lang[2070]?></td>
                            <td class="tabHeaderItem"><?=$lang[1242]?></td>
                            <td class="tabHeaderItem"><?=$lang[126]?></td>
                            <td class="tabHeaderItem"><?=$lang[2072]?></td>
                            <td class="tabHeaderItem" align="center">start</td>
                            <td class="tabHeaderItem" align="center"><?=$lang[160]?></td>
                        </tr>

                        <?php
                        if($templupdate){
                            $sqlquery = "SELECT VAL FROM LMB_CRONTAB WHERE ID = $templupdate";
                            $rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
                            $newval_ = explode(';',lmbdb_result($rs,"VAL"));
                            $newval_[0] = ${'templatevalue_'.$templupdate};
                            $newval = implode(';',$newval_);
                            if(file_exists($umgvar['path'].$newval_[0])) {
                                $sqlquery = "UPDATE LMB_CRONTAB SET VAL='".parse_db_string($newval,250)."' WHERE ID = $templupdate";
                                $rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
                            }else{
                                lmb_alert('file does not exists!');
                            }
                        }

                        $sqlquery = "SELECT LMB_CRONTAB.ID,KATEGORY,START,VAL,LMB_CRONTAB.ERSTDATUM,ACTIV,DESCRIPTION,ALIVE,JOB_USER,USERNAME FROM LMB_CRONTAB LEFT JOIN LMB_USERDB ON LMB_USERDB.ID = LMB_CRONTAB.JOB_USER WHERE KATEGORY = '" . lmb_strtoupper($kategoriedesc) . "' ORDER BY ERSTDATUM";
                        $rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);

                        while(lmbdb_fetch_row($rs)){
                            $tid = lmbdb_result($rs,"ID");
                            $val = lmbdb_result($rs,"VAL");
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
                            if(lmbdb_result($rs,"KATEGORY") == "TEMPLATE" OR lmbdb_result($rs,"KATEGORY") == "D"){
                                $template = $val0[0];
                            }
                            echo "
                                <TR class=\"tabBody\" OnClick=\"show_val('');show_val('".$val."');\">
                                    <TD>&nbsp;".lmbdb_result($rs,"ID")."&nbsp;</TD>
                                    <TD  BGCOLOR=\"$color\">&nbsp;".lmbdb_result($rs,"KATEGORY")."&nbsp;<input type=\"text\" style=\"width:200px\" value=\"$template\" name=\"templatevalue_$tid\" onchange=\"document.form1.kategorie.value='template.lib';document.form1.templupdate.value='$tid';document.form1.submit();\"></TD>
                                    <TD>&nbsp;".lmbdb_result($rs,"START")."&nbsp;</TD>
                                    <TD>&nbsp;".lmbdb_result($rs,"USERNAME")."&nbsp;</TD>
                                    <TD>&nbsp;".lmbdb_result($rs,"DESCRIPTION")."&nbsp;</TD>
                                    <TD>&nbsp;<INPUT TYPE=\"CHECKBOX\" STYLE=\"border:none;background-color:transparent;\" NAME=\"activ_".lmbdb_result($rs,"ID")."\" OnClick=\"document.location.href='main_admin.php?&action=setup_indize_db&kategorie=$kategorie&activate_job=".lmbdb_result($rs,"ID")."'\" $activ>&nbsp;</TD>
                                    <TD ALIGN=\"CENTER\">&nbsp;<i class=\"lmb-icon lmb-action\" STYLE=\"cursor:pointer;border:1px solid grey;\" NAME=\"activate_".lmbdb_result($rs,"ID")."\" OnClick=\"document.location.href='main_admin.php?&action=setup_indize_db&kategorie=$kategorie&run_job=".lmbdb_result($rs,"ID")."';limbasWaitsymbol(event,1);\"></i>&nbsp;</TD>
                                    <TD ALIGN=\"CENTER\">&nbsp;<A HREF=\"main_admin.php?&action=setup_indize_db&kategorie=$kategorie&del_job=".lmbdb_result($rs,"ID")."\"><i class=\"lmb-icon lmb-trash\" BORDER=\"0\"></i></A>&nbsp;</TD>
                                </TR>";
                            $cronvalue[] = str_replace(";"," ",lmbdb_result($rs,"START"))."\t php \"".$umgvar["pfad"]."/cron.php\" ".lmbdb_result($rs,"ID");
                        }
                        ?>

                        <TR><TD class="tabFooter" colspan="7"></TD></TR>
                    </table>

                    <hr>

                    <TABLE BORDER="0" cellspacing="1" cellpadding="2" class="tabfringe">
                        <TR class="tabHeader"><TD class="tabHeaderItem" colspan="8">cron</TD></TR>
                        <TR class="tabHeader">
                            <TD class="tabHeaderItem"><?=$lang[2074]?></TD>
                            <TD class="tabHeaderItem"><?=$lang[2075]?></TD>
                            <TD class="tabHeaderItem"><?=$lang[2076]?></TD>
                            <TD class="tabHeaderItem"><?=$lang[1437]?></TD>
                            <TD class="tabHeaderItem"><?=$lang[2078]?></TD>
                            <td>&nbsp;</td>
                            <TD class="tabHeaderItem">&nbsp;&nbsp;<?=$lang[1242]?></TD>
                        </TR>
                        <TR class="tabBody">
                            <TD style="width:50px;"><INPUT TYPE="TEXT" NAME="cron[0]" VALUE="0" STYLE="width:100%"></TD>
                            <TD style="width:50px;"><INPUT TYPE="TEXT" NAME="cron[1]" VALUE="1" STYLE="width:100%"></TD>
                            <TD style="width:50px;"><INPUT TYPE="TEXT" VALUE="*" NAME="cron[2]" STYLE="width:100%"></TD>
                            <TD style="width:50px;"><INPUT TYPE="TEXT" NAME="cron[3]" VALUE="*" STYLE="width:100%"></TD>
                            <TD style="width:50px;"><INPUT TYPE="TEXT" NAME="cron[4]" VALUE="*" STYLE="width:100%"></TD>
                            <td>&nbsp;</td>
                            <TD><?php echo $userfield; ?><input type="hidden" id="fjob_user" name="cron[5]"></TD>
                            <TD style="width:50px;"><INPUT TYPE="SUBMIT" NAME="add_job" VALUE="<?=$lang[2079]?>"></TD>
                        </TR>
                        <TR><TD class="tabFooter" colspan="7"></TR>
                    </TABLE>

                    <hr>

                    <?php
                    if($kategoriedesc == "INDIZE" OR $kategoriedesc == "OCR"){
                        ?>

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
                                                                $pic = "<IMG SRC=\"pic/outliner/plusonly.gif\" NAME=\"i".$file_struct["id"][$bzm]."\" OnClick=\"popup('".$file_struct["id"][$bzm]."','$LEVEL')\" STYLE=\"cursor:hand\">";
                                                            }else{
                                                                $next = 0;
                                                                $pic = "<IMG SRC=\"pic/outliner/blank.gif\">";
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
                        <hr>
                        <?php
                    } else if($kategoriedesc == "TEMPLATE"){
                        ?>
                        <TABLE BORDER="0" cellspacing="0" cellpadding="2" class="tabfringe">
                            <TR class="tabHeader"><TD class="tabHeaderItem"><?=$lang[2207]?> (*.job)</TD><TD class="tabHeaderItem"><?=$lang[126]?></TD></TR>
                            <TR class="tabBody">
                                <TD>
                                    <SELECT STYLE="width:120px;" NAME="job_template"><OPTION>
                                            <?php
                                            $extfiles = read_dir($umgvar["pfad"]."/EXTENSIONS",1);

                                            foreach ($extfiles["name"] as $key1 => $filename){
                                                $ext = explode(".",$filename);
                                                $ext = $ext[count($ext)-1];
                                                if($extfiles["typ"][$key1] == "file" AND lmb_strtolower($ext) == "job"){
                                                    $path = lmb_substr($extfiles["path"][$key1],lmb_strlen($umgvar["pfad"]),100);
                                                    echo "<OPTION VALUE=\"".$path.$filename."\">".str_replace("/EXTENSIONS/","",$path).$filename;
                                                }
                                            }
                                            ?>
                                    </SELECT>
                                </TD>
                                <TD><INPUT TYPE="TEXT" NAME="job_desc" STYLE="width:250px;"></TD>
                            </TR>
                            <TR><TD class="tabFooter" colspan="2"></TR>
                        </TABLE>
                        <hr>
                        <?php
                    } else if($kategoriedesc == "DATASYNC"){
                        ?>
                        <TABLE BORDER="0" cellspacing="0" cellpadding="2" class="tabfringe">
                            <TR class="tabHeader"><TD class="tabHeaderItem"><?=$lang[2207]?> (*.job)</TD><TD class="tabHeaderItem"><?=$lang[126]?></TD></TR>
                            <TR class="tabBody">
                                <TD>
                                    <SELECT STYLE="width:120px;" NAME="job_template" onchange="document.getElementById('job_desc').value=this.options[this.selectedIndex].text"><OPTION>
                                            <?php
                                            $sqlquery = "SELECT ID,NAME FROM LMB_SYNC_TEMPLATE WHERE TABID IS NULL OR TABID = 0 ORDER BY NAME";
                                            $rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
                                            while(lmbdb_fetch_row($rs)) {
                                                echo "<OPTION VALUE=\"".lmbdb_result($rs, "ID")."\">".lmbdb_result($rs, "NAME");
                                            }
                                            ?>
                                    </SELECT>
                                </TD>
                                <TD><INPUT TYPE="TEXT" NAME="job_desc" ID="job_desc" STYLE="width:250px;"></TD>
                            </TR>
                            <TR><TD class="tabFooter" colspan="2"></TR>
                        </TABLE>
                        <hr>
                        <?php
                    }

                    /* crontab value */
                    if($kategoriedesc) {
                        $cronStr = '';
                        if($cronvalue){
                            foreach($cronvalue as $key => $value){
                                $cronStr .= str_replace(";", " ", $value) . "\n";
                            }
                        }
                        ?>
                        <h4>Crontab value</h4>
                        <textarea readonly="readonly" STYLE="width:100%;min-width:700px;height:100px;overflow:hidden;"><?= $cronStr ?></textarea>
                    <?php } ?>

                </td>
            </tr>
            </tbody>
        </table>
    </div>
</form>
