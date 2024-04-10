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

img3=new Image();img3.src="assets/images/legacy/outliner/plusonly.gif";
img4=new Image();img4.src="assets/images/legacy/outliner/minusonly.gif";

function listdata(ID,LEVEL,TABID,TYP,NAME,NO){
	var picname = "p" + ID;

        $('.lmb-folder-open').filter('i[id^=p]').removeClass('lmb-folder-open').addClass('lmb-folder-closed');
        $('#'+picname).removeClass('lmb-folder-closed');
        $('#'+picname).addClass('lmb-folder-open');
        
	document.form2.filename_.value = NAME;
    $('#filename_text').text(NAME);
	document.form2.group_id.value = ID;
	if(!NO){
	parent.user_main.location.href="main_admin.php?action=setup_user_erg&mid=<?=$mid?>&group_id=" + ID + "";
	}
}

function showuser(ID){
	parent.user_main.location.href="main_admin.php?action=setup_user_change_admin&ID=" + ID + "";
}

function showgroup(ID){
    parent.$('#user_tree_symbol').trigger('limbas:hideside');
    parent.user_main.location.href="main_admin.php?action=setup_group_erg&ID=" + ID + "";
}

function popup(ID,LEVEL,TABID,TYP){
	var cli;
	cli = ".nextSibling";
	eval("var nested = document.getElementById('f_"+ID+"_"+LEVEL+"').nextSibling"+cli);
	var picname = "i" + ID;
	if (document.images[picname].src == img4.src) {
		document.images[picname].src = img3.src;
		nested.style.display="none";
	}else {
		document.images[picname].src = img4.src;
		nested.style.display='';
	}
}

/* --- Plaziere DIV-Element auf Cursorposition ----------------------------------- */
function setxypos(evt,el) {

    document.getElementById(el).style.left=evt.pageX - 60;
    document.getElementById(el).style.top=evt.pageY;

}


</script>



<div class="container-fluid py-3">
    <ul class="nav nav-tabs" id="user-group-tab">
        <li class="nav-item" role="presentation">
            <a class="nav-link active" id="groups-tab" data-bs-toggle="tab" href="#tab-groups" role="tab" aria-controls="tab-groups" aria-selected="true"><?=$lang[1469]?></a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link" id="users-tab" data-bs-toggle="tab" href="#tab-users" role="tab" aria-controls="tab-users" aria-selected="false"><?=$lang[1242]?></a>
        </li>
    </ul>
    <div class="tab-content bg-contrast border border-top-0 p-2 pt-3 h-75">

        <?php if($umgvar['multitenant']) {?>
        <SELECT class="form-select form-select-sm" onchange="document.form2.mid.value=this.value; document.form2.submit(); document.form1.mid.value=this.value; document.form1.submit();"><option>
            <?php
            $sqlquery3 = "SELECT ID,MID,NAME,SYNCSLAVE FROM LMB_MULTITENANT";
            $rs3 = lmbdb_exec($db,$sqlquery3) or errorhandle(lmbdb_errormsg($db),$sqlquery3,$action,__FILE__,__LINE__);
            while(lmbdb_fetch_row($rs3)) {
                echo '<option value="'.lmbdb_result($rs3, "ID").'" '.(($mid == lmbdb_result($rs3, "ID")) ? 'selected' : '').'>'.lmbdb_result($rs3,"NAME").'</option>';;
            }
            ?>
        </SELECT>
        <hr>
        <?php }?>

        <div class="tab-pane show active" id="tab-groups" role="tabpanel" aria-labelledby="groups-tab">
            <DIV ID="filelist">
                <FORM ACTION="main_admin.php" METHOD="post" name="form1" TARGET="user_tree">
                    <input type="hidden" name="action" value="setup_user_tree">
                    <input type="hidden" name="mid" VALUE="<?=$mid?>">
                </FORM>
                <?php
                function files1($LEVEL){
                    global $userstruct;
                    if($LEVEL){
                        if($LEVEL){$vis = "style=\"display:none\"";}else{$vis = "";}
                        echo "<div id=\"foldinglist\" $vis>\n";
                        echo "<TABLE CELLPADDING=\"0\" CELLSPACING=\"0\" BORDER=\"0\"><TR><TD WIDTH=\"10\">&nbsp;</TD><TD>\n";
                    }
                    $bzm = 0;
                    while($userstruct['id'][$bzm]){
                        if($userstruct['level'][$bzm] == $LEVEL && !$userstruct['del'][$bzm]){
                            if(in_array($userstruct['id'][$bzm],$userstruct['level'])){
                                $next = 1;
                                $pic = "<IMG SRC=\"assets/images/legacy/outliner/plusonly.gif\" NAME=\"i".$userstruct['id'][$bzm]."\" OnClick=\"popup('".$userstruct['id'][$bzm]."','$LEVEL','".$userstruct['tabid'][$bzm]."','".$userstruct['typ'][$bzm]."')\" STYLE=\"cursor:pointer\">";
                            }else{
                                $next = 0;
                                $pic = "<IMG SRC=\"assets/images/legacy/outliner/blank.gif\">";
                            }

                            if($userstruct['user_id'][$bzm]){
                                # --- Hauptgruppe ----
                                if($userstruct['maingroup'][$bzm]){
                                    if($userstruct['lock'][$bzm]){$iconclass = "lmb-user1-2";}
                                    else{$iconclass = "lmb-user1-1";}
                                    # --- Untergruppe ----
                                }else{
                                    if($userstruct['lock'][$bzm]){$iconclass = "lmb-user2-2";}
                                    else{$iconclass = "lmb-user2-1";}
                                }
                                echo "<div ID=\"u_".$userstruct['id'][$bzm]."_$LEVEL\"><TABLE CELLPADDING=\"0\" CELLSPACING=\"0\" BORDER=\"0\"><TR><TD>$pic</TD><TD><i class=\"lmb-icon " .$iconclass. "\" ID=\"u".$userstruct['id'][$bzm]."\" NAME=\"u".$userstruct['id'][$bzm]."\" ";
                                echo "OnClick=\"showuser('".$userstruct['user_id'][$bzm]."')\" STYLE=\"cursor:hand\" ";
                                echo "TITLE=\"".$userstruct['user_name'][$bzm]."\"></i></TD><TD ";
                                echo "style=\"cursor:pointer;\" OnClick=\"showuser('".$userstruct['user_id'][$bzm]."')\"";
                                echo "></i>&nbsp;".$userstruct['name'][$bzm]."</TD></TR></TABLE></div>\n";
                            }else{
                                echo "<div ID=\"f_".$userstruct['id'][$bzm]."_$LEVEL\"><TABLE CELLPADDING=\"0\" CELLSPACING=\"0\" BORDER=\"0\"><TR><TD>$pic</TD><TD><i class=\"lmb-icon lmb-folder-closed\" ID=\"p".$userstruct['id'][$bzm]."\" NAME=\"p".$userstruct['id'][$bzm]."\" OnClick=\"listdata('".$userstruct['id'][$bzm]."','$LEVEL','".$userstruct['tabid'][$bzm]."','".$userstruct['typ'][$bzm]."','".$userstruct['name'][$bzm]."')\" STYLE=\"cursor:pointer\" title=\"show user\"></i></TD><TD ";
                                echo "style=\"cursor:pointer;\" OnClick=\"showgroup(".$userstruct['id'][$bzm].");listdata('".$userstruct['id'][$bzm]."','$LEVEL','".$userstruct['tabid'][$bzm]."','".$userstruct['typ'][$bzm]."','".$userstruct['name'][$bzm]."',1)\" title=\"open details\"";
                                echo ">&nbsp;".$userstruct['name'][$bzm]."</TD></TR></TABLE></div>\n";
                            }

                            if($next){
                                $tab = 20;files1($userstruct['id'][$bzm]);
                            }else{
                                echo "<div id=\"foldinglist\" style=\"display:none\"></div>\n";
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
            </DIV>
        </div>
        <div class="tab-pane" id="tab-users" role="tabpanel" aria-labelledby="users-tab">
            <DIV ID="searchmenu">
                <FORM ACTION="main_admin.php" METHOD="post" name="form2" TARGET="user_main">
                    <input type="hidden" name="action" value="setup_user_erg">
                    <input type="hidden" name="group_id">
                    <input type="hidden" name="mid" VALUE="<?=$mid?>">
                    <input type="hidden" name="filename_" VALUE="root">

                    <div class="mb-3">
                        <i class="lmb-icon lmb-folder-open"></i> <span id="filename_text">root</span>
                    </div>
                    
                    <div class="row">
                        <label class="col-4 col-form-label"><?=$lang[519]?>:</label>
                        <div class="col-8">
                            <input type="text" class="form-control form-control-sm" name="ufilter_user">
                        </div>
                    </div>
                    <div class="row">
                        <label class="col-4 col-form-label"><?=$lang[142]?>:</label>
                        <div class="col-8">
                            <input type="text" class="form-control form-control-sm" name="ufilter_vorname">
                        </div>
                    </div>
                    <div class="row">
                        <label class="col-4 col-form-label"><?=$lang[4]?>:</label>
                        <div class="col-8">
                            <input type="text" class="form-control form-control-sm" name="ufilter_name">
                        </div>
                    </div>
                    <div class="row">
                        <label class="col-4 col-form-label"><?=$lang[561]?>:</label>
                        <div class="col-8">
                            <input type="text" class="form-control form-control-sm" name="ufilter_group">
                        </div>
                    </div>
                    
                    <hr>

                    <div class="form-check">
                        <label class="form-check-label">
                            <input class="form-check-input" type="radio" name="ufilter" value="" <?=(!$ufilter)?'checked':''?>>
                            <i class="lmb-icon lmb-user1-1"></i><?=$lang[1790]?>
                        </label>
                    </div>
                    <div class="form-check">
                        <label class="form-check-label">
                            <input class="form-check-input" type="radio" name="ufilter" value="lock" <?=($ufilter == "lock")?'checked':''?>>
                            <i class="lmb-icon-cus lmb-user1-2"></i><?=$lang[1793]?>
                        </label>
                    </div>
                    <div class="form-check">
                        <label class="form-check-label">
                            <input class="form-check-input" type="radio" name="ufilter" value="viewdel" <?=($ufilter == "viewdel")?'checked':''?>>
                            <i class="lmb-icon lmb-user1-3"></i><?=$lang[1687]?>
                        </label>
                    </div>
                    <div class="form-check">
                        <label class="form-check-label">
                            <input class="form-check-input" type="radio" name="ufilter" value="activ" <?=($ufilter == "activ")?'checked':''?>>
                            <i class="lmb-icon lmb-user1-4"></i><?=$lang[1789]?>
                        </label>
                    </div>
                    
                    <hr>
                    
                    <button type="button" class="btn btn-primary btn-sm" OnClick="document.form2.group_id.value=0;document.form2.submit();"><?=$lang[1626]?></button>
                </FORM>
            </DIV>
        </div>
    </div>
</div>


