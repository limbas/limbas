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
/* --- Berichtmenü ----------------------------------- */
function showdiv(evt,NAME) {
    document.getElementById("farbschema").style.visibility='hidden';
    document.getElementById("ampel").style.visibility='hidden';
    document.getElementById(NAME).style.left=evt.pageX;
    document.getElementById(NAME).style.top=evt.pageY;
    document.getElementById(NAME).style.visibility='visible';

}

function newwin1(typ,server,user,pass) {
checkemail = open("main.php?action=user_check_email&ID=<?=$ID?>&e_typ=" + typ + "&e_server=" + server + "&e_user=" + user + "&e_pass=" + pass + "" ,"CheckEmail","toolbar=0,location=0,status=0,menubar=0,scrollbars=1,resizable=0,width=650,height=300");
}

function srefresh() {
	link = confirm("<?=$lang[856]?>");
	if(link) {
		document.location.href="main.php?action=user_change&ID=<?=$ID?>&sess_refresh=<?=$ID?>";
	}
}

function check_pass(pass2) {
	if(pass2 != document.form1.passwort.value){
		document.form1.passwort.value = '';
		document.form1.passwort2.value = '';
		alert('Password incorrect\ntry again!');
	}
}

</script>


<div class="p-3">
    <FORM ACTION="main.php" METHOD="post" NAME="form1">
        <input type="hidden" name="action" value="user_change">
        <input type="hidden" name="ID" value="<?=$ID?>">
        <input type="hidden" name="user_change" value="1">
        <input type="hidden" name="farbe_change">
        <input type="hidden" name="lang_change">
        <input type="hidden" name="fileview_change">
        <input type="hidden" name="username" value="<?=$result_user["username"]?>">

        <div class="container-fluid bg-white p-3 border">
            <div class="row">
                <div class="col-md-6">
                    <h5><i class="lmb-icon lmb-user"></i><?=$lang[140]?></h5>
                    <hr>

                    <div class="row">
                        <label class="col-sm-4 col-form-label"><?=$lang[519]?></label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control form-control-sm" name="vorname" readonly disabled value="<?=$result_user["username"]?>">
                        </div>
                    </div>
                    <div class="row">
                        <label class="col-sm-4 col-form-label"><?=$lang[142]?></label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control form-control-sm" name="vorname" value="<?=$result_user['vorname']?>" <?= !$session['change_pass'] ? 'readonly disabled' : ''?>>
                        </div>
                    </div>
                    <div class="row">
                        <label class="col-sm-4 col-form-label"><?=$lang[4]?></label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control form-control-sm" name="name" value="<?=$result_user['name']?>" <?= !$session['change_pass'] ? 'readonly disabled' : ''?>>
                        </div>
                    </div>
                    <div class="row">
                        <label class="col-sm-4 col-form-label"><?=$lang[612]?></label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control form-control-sm" name="email" value="<?=$result_user['email']?>" <?= !$session['change_pass'] ? 'readonly disabled' : ''?>>
                        </div>
                    </div>
                    <div class="row">
                        <label class="col-sm-4 col-form-label">Tel</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control form-control-sm" name="tel" value="<?=$result_user['tel']?>">
                        </div>
                    </div>
                    <div class="row">
                        <label class="col-sm-4 col-form-label">Fax</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control form-control-sm" name="fax" value="<?=$result_user['fax']?>">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-4 col-form-label">Position</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control form-control-sm" name="position" value="<?=$result_user['position']?>">
                        </div>
                    </div>
                    <?php if($session["change_pass"]): ?>
                        <div class="row">
                            <label class="col-sm-4 col-form-label"><?=$lang[141]?></label>
                            <div class="col-sm-8">
                                <input type="password" class="form-control form-control-sm" name="passwort">
                            </div>
                        </div>
                        <div class="row mb-4">
                            <label class="col-sm-4 col-form-label"><?=$lang[1600]?></label>
                            <div class="col-sm-8">
                                <input type="password" class="form-control form-control-sm" name="passwort2" onchange="check_pass(this.value)">
                            </div>
                        </div>
                    <?php endif; ?>



                    <?php
                    if(!$result_user["uploadsize"]){$result_user["uploadsize"] = $umgvar["default_uloadsize"];}
                    if(!$result_user["maxresult"]){$result_user["maxresult"] = $umgvar["default_results"];}
                    if(!isset($result_user["logging"])){$result_user["logging"] = $umgvar["default_loglevel"];}
                    if(!$result_user["dateformat"]){$result_user["dateformat"] = $umgvar["default_dateformat"];}
                    if(!$result_user["timezone"]){$result_user["timezone"] = $umgvar["default_timezone"];}
                    if(!$result_user["setlocale"]){$result_user["setlocale"] = $umgvar["default_setlocale"];}
                    if(!$result_user["farbschema"]){$result_user["farbschema"] = $umgvar["default_usercolor"];}
                    if(!$result_user["language"]){$result_user["language"] = $umgvar["default_language"];}
                    ?>

                    <h5><i class="lmb-icon lmb-wrench-alt"></i><?=$lang[146]?></h5>

                    <hr>

                    <div class="row">
                        <label class="col-sm-4 col-form-label"><?=$lang[624]?></label>
                        <div class="col-sm-8">
                            <SELECT NAME="language" class="form-select form-select-sm">
                                <OPTION VALUE="-1">system</OPTION>
                                <?php
                                $sqlquery = "SELECT DISTINCT LANGUAGE,LANGUAGE_ID FROM LMB_LANG";
                                $rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
                                while(lmbdb_fetch_row($rs)) {
                                    $langid = lmbdb_result($rs,"LANGUAGE_ID");
                                    if($result_user["language"] == $langid){$SELECTED =  "SELECTED";}else {unset($SELECTED);}
                                    echo "<OPTION VALUE=\"".urlencode($langid)."\" $SELECTED>".lmbdb_result($rs,"LANGUAGE");
                                }
                                ?>
                            </SELECT>
                        </div>
                    </div>

                    <div class="row">
                        <label class="col-sm-4 col-form-label"><?=$lang[2576]?></label>
                        <div class="col-sm-8">
                            <SELECT name="dateformat" class="form-select form-select-sm">
                                <OPTION VALUE="1" <?=($result_user["dateformat"] == '1')?'selected':''?>>deutsch</OPTION>
                                <OPTION VALUE="2" <?=($result_user["dateformat"] == '2')?'selected':''?>>english</OPTION>
                                <OPTION VALUE="3" <?=($result_user["dateformat"] == '3')?'selected':''?>>us</OPTION>
                            </SELECT>
                        </div>
                    </div>



                    <div class="row">
                        <label class="col-sm-4 col-form-label"><?=$lang[623]?></label>
                        <div class="col-sm-8">
                            <SELECT name="farbe" class="form-select form-select-sm" OnChange="this.form.farbe_change.value='1';">
                                <?php
                                $sqlquery = "SELECT * FROM LMB_COLORSCHEMES";
                                $rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
                                while(lmbdb_fetch_row($rs)) {
                                    $farbid = lmbdb_result($rs,"ID");
                                    echo '<OPTION VALUE="'.$farbid.'" '.(($result_user["farbschema"] == $farbid) ? 'selected' : '').'>'.lmbdb_result($rs,"NAME").'</option>';;
                                }
                                ?>
                            </SELECT>
                        </div>
                    </div>
                    <div class="row">
                        <label class="col-sm-4 col-form-label"><?=$lang[698]?></label>
                        <div class="col-sm-8">
                            <SELECT NAME="layout" class="form-select form-select-sm">
                                <?php
                                $layouts = Layout::getAvailableLayouts();
                                foreach($layouts as $layout){
                                    echo '<option value="'.$layout.'" '.(($result_user['layout'] == $layout) ? 'selected' : '').'>'.$layout.'</option>';
                                }
                                ?>
                            </SELECT>

                        </div>
                    </div>

                    <div class="row">
                        <label class="col-4 col-form-label"><?=$lang[2167]?></label>
                        <div class="col-8 pt-2">
                            <INPUT TYPE="CHECKBOX" value="1" NAME="symbolbar" <?=($result_user["symbolbar"])?'checked':''?>>
                        </div>
                    </div>
                </div>


                <div class="col-md-6 pt-3 pt-md-0">

                    <?php 
                    $isFirstMail = true;
                    foreach ($gtab['table'] as $key => $value):
                    if($gtab['typ'][$key] == 6): ?>
                        <div class="<?=$isFirstMail ? '' : 'pt-3'?>">
                            <h5><i class="lmb-icon lmb-mail"></i><?=$gtab['desc'][$key]?></h5>
                            <hr>
                            <div class="row">
                                <label class="col-sm-4 col-form-label"><?=$lang[4]?></label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control form-control-sm" name="e_setting[<?=$key?>][full_name" value="<?=$result_user['e_setting'][$key]['full_name']?>">
                                </div>
                            </div>
                            <div class="row">
                                <label class="col-sm-4 col-form-label"><?=$lang[2519]?></label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control form-control-sm" name="e_setting[<?=$key?>][email_address]" value="<?=$result_user['e_setting'][$key]['email_address']?>">
                                </div>
                            </div>
                            <div class="row">
                                <label class="col-sm-4 col-form-label"><?=$lang[2520]?></label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control form-control-sm" name="e_setting[<?=$key?>][reply_address]" value="<?=$result_user['e_setting'][$key]['reply_address']?>">
                                </div>
                            </div>
                            <div class="row">
                                <label class="col-sm-4 col-form-label"><?=$lang[2521]?></label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control form-control-sm" name="e_setting[<?=$key?>][imap_hostname]" value="<?=$result_user['e_setting'][$key]['imap_hostname']?>">
                                </div>
                            </div>
                            <div class="row">
                                <label class="col-sm-4 col-form-label"><?=$lang[2522]?></label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control form-control-sm" name="e_setting[<?=$key?>][imap_username]" value="<?=$result_user['e_setting'][$key]['imap_username']?>">
                                </div>
                            </div>
                            <div class="row">
                                <label class="col-sm-4 col-form-label"><?=$lang[2524]?></label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control form-control-sm" name="e_setting[<?=$key?>][imap_password]" value="<?=$result_user['e_setting'][$key]['imap_password']?>">
                                </div>
                            </div>
                            <div class="row">
                                <label class="col-sm-4 col-form-label"><?=$lang[2523]?>(143)</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control form-control-sm" name="e_setting[<?=$key?>][imap_port]" value="<?=$result_user['e_setting'][$key]['imap_port']?>">
                                </div>
                            </div>
                        </div>
                    
                    <?php
                        $isFirstMail = false;
                    endif;
                    endforeach;?>
                </div>
            </div>

            <hr>

            <button type="submit" class="btn btn-outline-secondary" value="<?=$lang[842]?>"><?=$lang[842]?></button>
        </div>


    </FORM>
</div>
