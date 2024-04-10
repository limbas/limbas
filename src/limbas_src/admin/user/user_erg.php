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
function newwin1(USERID) {
	tracking = open("main_admin.php?action=setup_user_tracking&typ=1&userid=" + USERID ,"Tracking","toolbar=0,location=0,status=0,menubar=0,scrollbars=1,resizable=1,width=600,height=600");
}

function newwin2(USERID) {
	userstat = open("main.php?action=kalender&userstat=" + USERID ,"userstatistic","toolbar=0,location=0,status=0,menubar=0,scrollbars=1,resizable=1,width=750,height=550");
}

function activate_user(USERID) {
	document.location.href = "main_admin.php?action=setup_user_change_admin&reactivate=1&ID="+USERID;
}

function select_all_user(el){
	var cc = null;

	var ar = document.getElementsByTagName("input");
	for (var i = ar.length; i > 0;) {
		cc = ar[--i];
		var ln = cc.name.split("[");
		if(cc.type == "checkbox" && ln[0] == 'edit_user'){
			if(el.checked){
				cc.checked = 1;
			}else{
				cc.checked = 0;
			}
		}
	}

}

</script>


<div class="container-fluid p-3">
    <FORM ACTION="main_admin.php" METHOD="post" NAME="form1" TARGET="user_main">
        <input type="hidden" name="action" VALUE="setup_user_erg">
        <input type="hidden" name="ID" VALUE="<?=$ID?>">
        <input type="hidden" name="order" VALUE="<?=$order?>">
        <input type="hidden" name="group_id" VALUE="<?=$group_id?>">
        <input type="hidden" name="mid" VALUE="<?=$mid?>">
        <input type="hidden" name="lock">
        <input type="hidden" name="logout">
        <input type="hidden" name="debug">
        <input type="hidden" name="logging">
        <input type="hidden" name="user_del">
        <input type="hidden" name="filter" VALUE="<?=$filter?>">
        <input type="hidden" name="ufilter_user" VALUE="<?=$ufilter_user?>">
        <input type="hidden" name="ufilter_vorname" VALUE="<?=$ufilter_vorname?>">
        <input type="hidden" name="ufilter_name" VALUE="<?=$ufilter_name?>">
        <input type="hidden" name="ufilter_group" VALUE="<?=$ufilter_group?>">

        <table class="table table-sm table-striped mb-0 border bg-contrast">
            <thead>
            <tr>
                <th><INPUT TYPE="CHECKBOX" onclick="select_all_user(this);"></th>
                <th><A HREF="Javascript:document.form1.order.value='LMB_USERDB.USERNAME';document.form1.submit();"><?=$lang[3]?></A></th>
                <th><?=$lang[4]?></th>
                <th><A HREF="Javascript:document.form1.order.value='LMB_GROUPS.NAME';document.form1.submit();"><?=$lang[561]?></th>
                <th><?=$lang[563]?></th>
                <th><?=$lang[657]?></th>
                <th><?=$lang[911]?></th>
                <th><?=$lang[1250]?></th>
                <th><?=$lang[1791]?></th>
                <th></th>
            </tr>
            </thead>

            <?php
            /* --- Ergebnisliste --------------------------------------- */
            $bzm = 1;

            if($result_user["username"]):
                foreach ($result_user["username"] as $bzm => $value):

                    if(($ufilter == "activ" AND $result_user['aktiv'][$bzm]) OR $ufilter != "activ"):

                        if($result_user["del"][$bzm]):
                            ?>
            
                            <TR class="text-danger">
                                <TD>&nbsp;</TD>
                                <TD class="text-danger"><?=$result_user["username"][$bzm]?></TD>
                                <TD class="text-danger"><?=$result_user["vorname"][$bzm]?> <?=$result_user['name'][$bzm]?></TD>
                                <TD class="text-danger"><?=$result_user["gruppe"][$bzm]?></TD>
                                <TD class="text-danger"><?=$result_user["erstdatum"][$bzm]?></TD>
                                <TD COLSPAN="2"></TD>
                                <TD><i class="lmb-icon lmb-history cursor-pointer" OnClick="newwin1('<?= $result_user["user_id"][$bzm] ?>')"></i></TD>
                                <TD><i class="lmb-icon lmb-calendar-alt2 cursor-pointer" OnClick="newwin2('<?= $result_user["user_id"][$bzm] ?>')"></i></TD>
                                <TD TITLE="<?=$lang[1728]?>"><i class="lmb-icon lmb-action cursor-pointer" OnClick="activate_user('<?=$result_user["user_id"][$bzm]?>')"></i></TD>
                            </TR>
            
                            <?php
                        else:

                            if($filter != "activ" OR ($filter == "activ" AND $result_user["aktiv"][$bzm])) {
                                if ($result_user["aktiv"][$bzm]) {
                                    $usercolor = "text-success";
                                } elseif ($result_user["gruppen_id"][$bzm] != $group_id AND $group_id) {
                                    $usercolor = "text-white-50;";
                                } elseif ($result_user["lock"][$bzm]) {
                                    $usercolor = "text-danger";
                                } else {
                                    $usercolor = "";
                                }
                            }
                                
                                ?>

                                <TR>
                    
                                    <TD><INPUT TYPE="CHECKBOX"NAME="edit_user[<?=$result_user["user_id"][$bzm]?>]" value="1"></TD>
                    
                                    <TD class="<?=$usercolor?>"><A HREF="main_admin.php?action=setup_user_change_admin&ID=<?= $result_user["user_id"][$bzm] ?>"><?= $result_user["username"][$bzm]; ?>&nbsp;</A></TD>
                                    <TD class="<?=$usercolor?>"><?= $result_user["vorname"][$bzm]." ".$result_user["name"][$bzm] ?>&nbsp;</TD>
                                    <TD class="<?=$usercolor?>"><?= $result_user["gruppe"][$bzm] ?>&nbsp;</TD>
                                    <TD class="<?=$usercolor?>"><?= $result_user["erstdatum"][$bzm] ?>&nbsp;</TD>
                    
                                    <TD>
                                        <?php if($result_user["username"][$bzm] != 'admin'){?>
                                            <INPUT TYPE="CHECKBOX" OnClick="document.form1.lock.value='<?= $result_user["user_id"][$bzm] ?>';document.form1.submit();" <?=($result_user["lock"][$bzm] == 1)?'checked':''?>>
                                        <?php }?>
                                    </TD>
                    
                                    <TD>
                                        <?php if($result_user["username"][$bzm] != 'admin' AND $session['username'] == 'admin'){?>
                                            <INPUT TYPE="CHECKBOX" OnClick="document.form1.debug.value='<?= $result_user['user_id'][$bzm] ?>';document.form1.submit();" <?=($result_user["debug"][$bzm] == 1)?'checked':''?>>
                                        <?php }?>
                                    </TD>
                    
                                    <TD><i class="lmb-icon lmb-history cursor-pointer" OnClick="newwin1('<?= $result_user["user_id"][$bzm] ?>')"></i></TD>
                                    <TD><i class="lmb-icon lmb-calendar-alt2 cursor-pointer" OnClick="newwin2('<?= $result_user["user_id"][$bzm] ?>')"></i></TD>
                                    <TD></TD>
                                </TR>
                                <?php
                        endif;
                    endif;
                    $bzm++;
                endforeach;
            endif;
            ?>


        </table>
        
        <div class="bg-contrast border p-2">
            <div class="form-check form-check-inline">
                <label class="form-check-label">
                    <input class="form-check-input" type="radio" value="" name="ufilter" OnClick="document.form1.submit();" <?=(!$ufilter)?'checked':''?>>
                    <?=$lang[1790]?>
                </label>
            </div>
            <div class="form-check form-check-inline">
                <label class="form-check-label">
                    <input class="form-check-input" type="radio" value="activ" name="ufilter" OnClick="document.form1.submit();" <?=($ufilter == 'activ')?'checked':''?>>
                    <?=$lang[1789]?>
                </label>
            </div>
            <div class="form-check form-check-inline">
                <label class="form-check-label">
                    <input class="form-check-input" type="radio" value="lock" name="ufilter" OnClick="document.form1.submit();" <?=($ufilter == 'lock')?'checked':''?>>
                    <?=$lang[1793]?>
                </label>
            </div>
            <div class="form-check form-check-inline">
                <label class="form-check-label">
                    <input class="form-check-input" type="radio" value="viewdel" name="ufilter" OnClick="document.form1.submit();" <?=($ufilter == 'viewdel')?'checked':''?>>
                    <?=$lang[1687]?>
                </label>
            </div>
            <div class="mt-3">
                <button class="btn btn-primary btn-sm" type="submit" name="send_message" value="1"><?=$lang[2463]?></button>
            </div>
            
        </div>
        
        


    </FORM>

</div>
