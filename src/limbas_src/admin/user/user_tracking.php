<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */


use Limbas\lib\db\functions\Dbf;


/* --- Tabellenarray ------------------------------- */
$sqlquery = "SELECT TAB_ID,BESCHREIBUNG FROM LMB_CONF_TABLES";
$rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
while(lmbdb_fetch_row($rs)) {
	$tabarray[lmbdb_result($rs,"TAB_ID")] = lmbdb_result($rs,"BESCHREIBUNG");
}
/* --- Felderarray ------------------------------- */
$sqlquery = "SELECT FIELD_ID,TAB_ID,SPELLING FROM LMB_CONF_FIELDS";
$rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
while(lmbdb_fetch_row($rs)) {
	$fieldarray[lmbdb_result($rs,"TAB_ID")][lmbdb_result($rs,"FIELD_ID")] = lmbdb_result($rs,"SPELLING");
}

# --- Zeitperiode -----
if($periodid){
	$sqlquery = "SELECT LOGIN_DATE,UPDATE_DATE FROM LMB_HISTORY_USER WHERE ID = $periodid";
	$rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
	if(lmbdb_fetch_row($rs)) {
		$diag_von = get_date(lmbdb_result($rs,"LOGIN_DATE"),1);
		$diag_bis = get_date(lmbdb_result($rs,"UPDATE_DATE"),1);
	}
}

?>


<script>


// --- Subtabellen-Popup-funktion ----------
function poprecord(id){
	if(document.getElementById(id)){
		if(document.getElementById(id).style.display){
			document.getElementById(id).style.display='';
		}else{
			document.getElementById(id).style.display='none';
		}
	}
}

function newwin(GTAB,ID) {
	spalte = open("main.php?action=gtab_change&ID=" + ID + "&gtabid=" + GTAB + "" ,"Datensatzdetail","toolbar=0,location=0,status=0,menubar=0,scrollbars=1,resizable=0,width=700,height=600");
}


</script>

<?php
if(!$diag_von){$diag_von = date("d.m.Y");}
if(!$diag_bis){$diag_bis = date("d.m.Y");}
?>


<div class="container-fluid p-3">
    <FORM ACTION="main_admin.php" METHOD=post name="form1">
        <input type="hidden" name="action" VALUE="setup_user_tracking">
        <input type="hidden" name="userid" VALUE="<?=$userid?>">
        <input type="hidden" name="typ" VALUE="<?=$typ?>">
        <input type="hidden" name="order" VALUE="<?=$order?>">
        <input type="hidden" name="loglevel" VALUE="<?=$loglevel?>">

        <ul class="nav nav-tabs">
            <li class="nav-item">
                <a class="nav-link <?=($typ == 1)?'active bg-contrast':''?>" onclick="document.form1.typ.value='1';document.form1.submit();" href="#"><?=$lang[1431]?></a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?=($typ == 2)?'active bg-contrast':''?>" onclick="document.form1.typ.value='2';document.form1.submit();" href="#"><?=$lang[544]?></a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?=($typ == 3)?'active bg-contrast':''?>" onclick="document.form1.typ.value='3';document.form1.submit();" href="#"><?=$lang[1433]?></a>
            </li>
            
            <?php if ($typ == 3): ?>

                <li class="nav-item">
                    <span class="nav-link border-0" id="usetime">refresh</span>
                </li>
            
            <?php endif; ?>
            
        </ul>
        
        <div class="tab-content border border-top-0 bg-contrast">
            <div class="tab-pane active <?=($typ != 3)?'pt-2':''?>">
                
                
                <?php if($typ != 3):?>
                    <div class="row row-cols-auto g-3 align-items-center ms-2">
                        <div>
                            <label for="diag_von" class="me-2">von:</label>
                        </div>
                        <div>
                            <input type="date" class="form-control form-control-sm mb-2 me-sm-2" NAME="diag_von" id="diag_von" <?php if($typ != 3){echo "value=\"$diag_von\"";}?> onchange="document.form1.submit();">
                        </div>
                        <div>
                            <label for="diag_bis" class="me-2">bis:</label>
                        </div>
                        <div>
                            <input type="date" class="form-control form-control-sm mb-2 me-sm-2" NAME="diag_bis" id="diag_bis" <?php if($typ != 3){echo "value=\"$diag_bis\"";}?> onchange="document.form1.submit();">
                        </div>
                        <?php if($typ == 2): ?>
                            <div>
                                <select name="loglevel" onchange="document.form1.submit();" class="form-select form-select-sm mb-2 me-sm-2">
                                    <option value="1" <?=($loglevel == 1) ? 'selected':''?>>Level 1</option>
                                    <option value="2" <?=($loglevel == 2) ? 'selected':''?>>Level 2</option>
                                </select>
                            </div>
                        <?php endif; ?>
                    </div>
                
                <?php endif; ?>
                
                

                <table class="table table-sm table-striped mb-0">
                    
                    

                    
                    
                    <?php

                    if($typ == 3){
                        $where2 .= " AND ".LMB_DBFUNC_DATE."ERSTDATUM) = '".date("Y-m-d",mktime(date("H"),date("i"),date("s"),date("m"),date("d"),date("Y")))."'";
                    }
                    else{
                        if(!$diag_von){$diag_von = date("d.m.Y");}
                        if(!$diag_bis){$diag_bis = date("d.m.Y");}
                        if(convert_date($diag_von)){$where = "AND ".LMB_DBFUNC_DATE."LOGIN_DATE) >= '".lmb_substr(convert_date($diag_von),0,10)."'";$where2 = "AND ".LMB_DBFUNC_DATE."ERSTDATUM) >= '".lmb_substr(convert_date($diag_von),0,10)."'";}else{$where = "AND ".LMB_DBFUNC_DATE."LOGIN_DATE) >= '".convert_date(date("Y-m-d 00:00:00."))."'";$where2 = "AND ".LMB_DBFUNC_DATE."ERSTDATUM) >= '".convert_date(date("Y-m-d 00:00:00."))."'";}
                        if(convert_date($diag_bis)){$where .= " AND ".LMB_DBFUNC_DATE."LOGIN_DATE) <= '".lmb_substr(convert_date($diag_bis),0,10)."'";$where2 .= " AND ".LMB_DBFUNC_DATE."ERSTDATUM) <= '".lmb_substr(convert_date($diag_bis),0,10)."'";}
                        
                        
                    }
                    if($typ == 2){
                        if(!$loglevel){$loglevel = 1;}
                        $where2 .= " AND LOGLEVEL <= $loglevel";
                    }


                    if($typ == 1){
                        ?>
                        <thead>
                            <tr>
                                <th><?php echo $lang[1800];?></th>
                                <th><?php echo $lang[1801];?></th>
                                <th><?php echo $lang[1803];?></th>
                                <th><?php echo $lang[1802];?></th>
                            </tr>
                        </thead>
                        
                        
                    <?php
                        $sqlquery =  "SELECT DISTINCT ID,LOGIN_DATE, UPDATE_DATE, IP, HOST, ".Dbf::sqlTimeDiff('LOGIN_DATE','UPDATE_DATE')." AS DAUER FROM LMB_HISTORY_USER WHERE USERID = $userid $where ORDER BY LOGIN_DATE";
                        $rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
                        while(lmbdb_fetch_row($rs)): ?>
                            
                        <tr>
                            <td><?=get_date(lmbdb_result($rs,"LOGIN_DATE"),2)?></td>
                            <td><?=get_date(lmbdb_result($rs,"UPDATE_DATE"),2)?></td>
                            <td><?=lmb_substr(lmbdb_result($rs,"DAUER"),0,8)?></td>
                            <td><?=lmbdb_result($rs,"IP")?></td>
                        </tr>
                        
                            <?php
                        endwhile;
                    }
                    elseif($typ == 2 OR $typ == 3)
                    {
                        
                        
                        if($typ == 3){?>
                            <script>
                                function reload(){
                                    document.form1.submit();
                                }
                                var firsttime = null;
                                function inusetime() {
                                    now = new Date();
                                    if(!firsttime){firsttime = now.getTime();}
                                    locktime = 30;
                                    thistime = now.getTime();
                                    leftsec = (thistime - firsttime) / 1000;
                                    leftsec = Math.round(leftsec);
                                    showtime = locktime - leftsec;
                                    lefttime = (showtime - Math.floor(showtime/60)*60); //Math.floor(showtime/3600) + "." + Math.floor(showtime/60) + "." + 
    
                                    if(showtime > 1){
                                        Timer = setTimeout("inusetime()",1000);
                                        document.getElementById("usetime").firstChild.nodeValue = lefttime + 's';
                                    }else{
                                        reload();
                                    }
                                }
    
                            </script>
                            
                        <?php }
    
                        if($order == "ERSTDATUM"){$or_erst = "ERSTDATUM DESC";}else{$or_erst = "ERSTDATUM";}
                        if($order == "ACTION"){$or_act = "ACTION DESC";}else{$or_act = "ACTION";}
                        if($order == "TAB"){$or_tab = "TAB DESC";}else{$or_tab = "TAB";}
                        
                        ?>

                        <thead>
                            <tr>
                                <th class="cursor-pointer <?=($typ == 3)?'border-top-0':''?>" OnClick="document.form1.order.value='<?=$or_erst?>';document.form1.submit();"><?=$lang[1804]?></th>
                                <th class="cursor-pointer <?=($typ == 3)?'border-top-0':''?>" OnClick="document.form1.order.value='<?=$or_act?>';document.form1.submit();"><?=$lang[1805]?></th>
                                <th class="cursor-pointer <?=($typ == 3)?'border-top-0':''?>" OnClick="document.form1.order.value='<?=$or_tab?>';document.form1.submit();"><?=$lang[1806]?>><?php echo $lang[1803];?></th>
                                <th class="<?=($typ == 3)?'border-top-0':''?>"><?=$lang[1807]?></th>
                            </tr>
                        </thead>
                        
                            <?php
                            if(!$order){
                                $order = "ERSTDATUM";
                                if($typ == 3){$order = "ERSTDATUM DESC";}
                            }elseif($typ == 3){
                                if($order == "ERSTDATUM"){$order = "ERSTDATUM DESC";}else{$order .= ",ERSTDATUM DESC";}
                            }
    
                            $popuparray = array(3,128,171,129,130,203,190,116,200,195,119);
    
                            $sqlquery =  "SELECT * FROM LMB_HISTORY_ACTION WHERE USERID = $userid $where2 ORDER BY $order";
                            $rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
                            $bzm = 1;
                            while(lmbdb_fetch_row($rs)) {
                                if(lmbdb_result($rs,"DATAID")){$dat_id = lmbdb_result($rs,"DATAID");}else{$dat_id = "";}
                                $action_id = lmbdb_result($rs,"ACTION");
                                if($action_id == 1){$action = "<SPAN STYLE=\"color:blue\">".$lang[$LINK["desc"][$action_id]]."</SPAN>";}
                                elseif($action_id == 11){$action = "<SPAN STYLE=\"color:red\">".$lang[$LINK["desc"][$action_id]]."</SPAN>";}
                                elseif($action_id == 164){$action = "<SPAN STYLE=\"color:orange\">".$lang[$LINK["desc"][$action_id]]."</SPAN>";}
                                elseif($action_id == 166){$action = "<SPAN STYLE=\"color:purple\">".$lang[$LINK["desc"][$action_id]]."</SPAN>";}
                                elseif($action_id == 201){$action = "<SPAN STYLE=\"color:brown\">".$lang[$LINK["desc"][$action_id]]."</SPAN>";}
                                else{$action = $lang[$LINK["desc"][$action_id]];}
    
                                if($LINK["typ"][$action_id] == 3){$BGCOLOR1 = "";}
                                elseif($LINK["typ"][$action_id] == 4){$BGCOLOR1 = "";}
                                elseif($LINK["typ"][$action_id] == 2){$BGCOLOR1 = "#F1C2C2";}
                                else{$BGCOLOR1 = "";}


                                if(in_array($action_id,$popuparray)){
                                    $sqlquery1 = "SELECT DISTINCT ID,ERSTDATUM,USERID,TAB,FIELD,DATAID,FIELDVALUE FROM LMB_HISTORY_UPDATE WHERE DATAID = ".lmbdb_result($rs, "DATAID")." AND ACTION_ID = ".lmbdb_result($rs, "ID")." AND TAB = ".lmbdb_result($rs, "TAB")." ORDER BY ERSTDATUM";
                                    $sqlquery1c = "SELECT COUNT(*) AS RESULT FROM LMB_HISTORY_UPDATE WHERE DATAID = ".lmbdb_result($rs, "DATAID")." AND ACTION_ID = ".lmbdb_result($rs, "ID")." AND TAB = ".lmbdb_result($rs, "TAB");
                                    $rs1 = lmbdb_exec($db,$sqlquery1) or errorhandle(lmbdb_errormsg($db),$sqlquery1,$action,__FILE__,__LINE__);
                                    $numrows = lmb_num_rows($rs1,$sqlquery1c);
                                    if($numrows){
                                        $rstyle = "class=\"cursor-pointer\" OnClick=\"poprecord('tr_$bzm')\" ";
                                        if($action_id == 3){$num = "(".$numrows.")";}
                                        $action = "<SPAN STYLE=\"color:green;\">".$lang[$LINK["desc"][$action_id]]."</SPAN> ".$num;
                                    }else{
                                        $action = $lang[$LINK["desc"][$action_id]];
                                    }
                                }
                                
                                
                                $extendedAction = [];
                                if(in_array($action_id,$popuparray)){
                                    
                                    while(lmbdb_fetch_row($rs1)) {
                                        unset($val);
                                        
                                        $ftype = $gfield[lmbdb_result($rs1, "TAB")]['field_type'][lmbdb_result($rs1, "FIELD")];
                                        if($ftype == 11){
                                            $links = explode(";",lmbdb_result($rs1, "FIELDVALUE"));
                                            if($links){
                                                foreach($links as $key1 => $value1){
                                                    $lid = lmb_substr($value1,1,10);
                                                    $val[] = "<A HREF=\"#\" OnClick=\"newwin('".$gfield[lmbdb_result($rs1, "TAB")]["verkntabid"][lmbdb_result($rs1, "FIELD")]."','".$lid."');\">$value1</A>";
                                                }

                                                $extendedAction[] = $lang[$fieldarray[lmbdb_result($rs,"TAB")][lmbdb_result($rs1, "FIELD")]] . ': ' . implode("|",$val);
                                            }
                                        }else{
                                            $extendedAction[] = $lang[$fieldarray[lmbdb_result($rs,"TAB")][lmbdb_result($rs1, "FIELD")]] . ': ' . nl2br(htmlentities(lmbdb_result($rs1, "FIELDVALUE"),ENT_QUOTES,$umgvar["charset"]));
                                        }
                                    }
                                    
                                }
                                
                                ?>

                                    <tr <?=$rstyle?>>
                                        <td><?=get_date(lmbdb_result($rs,"ERSTDATUM"),2)?></td>
                                        <td>
                                            <span <?=!empty($extendedAction)?'class="cursor-pointer" data-bs-toggle="collapse" data-bs-target="#extact'.$bzm.'"':''?> >
                                                <?=$action?>
                                            </span>
                                            <?php if (!empty($extendedAction)) : ?>
                                            <div class="collapse" id="extact<?=$bzm?>">
                                                <?=implode('<br>',$extendedAction)?>
                                            </div>
                                            <?php endif; ?>
                                        </td>
                                        <td><?=$lang[$tabarray[lmbdb_result($rs,"TAB")]]?></td>
                                        <td><?=$dat_id?></td>
                                    </tr>
                            <?php
                                
                                
                                
                                
                                $bzm++;
                                if($typ == 3 AND $bzm >= 31){$bzm = 0;}
                                if($typ == 2 AND $bzm >= 500){$bzm = 0;}
                            }
                    }
                    ?>
                    
                                
                    
                    
                    
                    
                    
                </table>

            </div>
        </div>



    </FORM>
</div>

