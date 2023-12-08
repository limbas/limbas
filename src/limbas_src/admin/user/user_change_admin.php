<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */


//TODO: bootstrap popovers anpassen
?>

<SCRIPT LANGUAGE="JavaScript">

    function selected(cal, date) {
        eval("document.form1.elements['" + elfieldname + "'].value = date;");
    }

    function closeHandler(cal) {
        cal.hide();
    }

    function showCalendar(event, sell, fieldname, value) {
        elfieldname = fieldname;
        var sel = document.getElementById('diagv');
        var cal = new Calendar(true, null, selected, closeHandler);
        calendar = cal;
        cal.create();
        calendar.setDateFormat("%d.%m.%Y");
        calendar.sel = sel;
        if (value) {
            calendar.parseDate(value);
        }
        calendar.showAtElement(sel);
        return false;
    }

    function delete_user(ID, USER) {
        del = confirm("<?=$lang[908]?> \"" + USER + "\" <?=$lang[160]?>?");
        if (del) {
            document.form1.user_del.value = ID;
            document.form1.action.value = 'setup_user_erg';
            document.form1.submit();
        }
    }

    function gurefresh(DATA) {
        gu = confirm("<?=$lang[896]?>");
        if (gu) {
            document.location.href = "main_admin.php?action=setup_grusrref&user=<?=$ID?>&datarefresh=" + DATA + "";
        }
    }

    function lrefresh() {
        link = confirm("<?=$lang[896]?>");
        if (link) {
            document.location.href = "main_admin.php?action=setup_linkref&user=<?=$ID?>";
        }
    }

    function srefresh() {
        link = confirm("<?=$lang[899]?>");
        if (link) {
            document.location.href = "main_admin.php?action=setup_user_change_admin&ID=<?=$ID?>&srefresh=1";
        }
    }

    function createpass() {
        var x = 0;
        var pass = "";
        var laenge = 8;
        var zeichen = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        while (x != laenge) {
            pass += zeichen.charAt(Math.random() * zeichen.length);
            x++;
        }
        document.form1.elements['userdata[passwort]'].value = pass
    }

    function send(action) {
        if (action == 'setup_user_neu') {
            document.form1.user_add.value = '1';
        }
        if ((document.form1.elements['userdata[passwort]'].value.length < 5 && document.form1.elements['userdata[passwort]'].value.length > 0) || document.form1.elements['userdata[username]'].value.length < 5) {
            alert('<?=$lang[1315]?>');
        } else {
            document.form1.submit();
        }
    }

    function newwin1(USERID) {
        tracking = open("main_admin.php?action=setup_user_tracking&typ=1&userid=" + USERID, "Tracking", "toolbar=0,location=0,status=0,menubar=0,scrollbars=1,resizable=1,width=600,height=600");
    }

    function newwin2(USERID) {
        userstat = open("main.php?action=userstat&userstat=" + USERID, "userstatistic", "toolbar=0,location=0,status=0,menubar=0,scrollbars=1,resizable=1,width=750,height=550");
    }
</SCRIPT>

<div class="p-3">
    <form enctype="multipart/form-data" action="main_admin.php" method="post" name="form1">
        <input type="hidden" name="action" value="setup_user_change_admin">
        <input type="hidden" name="ID" value="<?= $ID ?>">
        <input type="hidden" name="group_id" value="<?= $result_user["group_id"] ?>">
        <input type="hidden" name="user_change" value="1">
        <input type="hidden" name="user_del">
        <input type="hidden" name="fileview_change">
        <input type="hidden" name="debug">
        <input type="hidden" name="user_add">
        <input type="hidden" name="lockbackend">
        <input type="hidden" name="lock">
        <input type="hidden" name="delete_user_total">

        <div class="container-fluid bg-white p-3 border">
            <div class="row">
                <div class="col-6">
                    <h5><?= ($result_user['lock']) ? '<i class="lmb-icon-cus lmb-user1-2"></i>' : '' ?> <?= ($result_user['aktiv']) ? '<i class="lmb-icon lmb-user1-4"></i>' : '<i class="lmb-icon lmb-user1-1"></i>' ?> <?= $lang[140] ?></h5>

                    <div class="row">
                        <label class="col-sm-4 col-form-label"><?= $lang[563] ?></label>
                        <div class="col-sm-8">
                            <span class="form-control-plaintext"><?= $result_user["erstdatum"] ?></span>
                        </div>
                    </div>
                    <div class="row">
                        <label class="col-sm-4 col-form-label"><?= $lang[1792] ?></label>
                        <div class="col-sm-8">
                            <span class="form-control-plaintext"><?= $result_user["editdatum"] ?></span>
                        </div>
                    </div>

                    <hr>

                    <div class="row">
                        <label class="col-sm-4 col-form-label">user-id</label>
                        <div class="col-sm-8">
                            <span class="form-control-plaintext"><?= $result_user["user_id"] ?></span>
                        </div>
                    </div>

                    <div class="row">
                        <label class="col-sm-4 col-form-label"><?= $lang[519] ?></label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control form-control-sm" name="userdata[username]"
                                   value="<?= $result_user["username"] ?>" <?= ($action != "setup_user_neu") ? 'OnChange="alert(\'for change username, you need to set a password again!\');"' : '' ?>>
                        </div>
                    </div>
                    <div class="row">
                        <label class="col-sm-4 col-form-label"><?= $lang[141] ?></label>
                        <div class="col-sm-8">
                            <div class="input-group input-group-sm mb-3">
                                <input type="password" class="form-control form-control-sm" name="userdata[passwort]"
                                       value="<?= $pass ?>">
                                <span class="input-group-text"><i class="lmb-icon lmb-lock-file cursor-pointer"
                                                                  OnClick="createpass();"></i></span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <label class="col-sm-4 col-form-label"><?= $lang[142] ?></label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control form-control-sm" name="userdata[vorname]"
                                   value="<?= $result_user['vorname'] ?>">
                        </div>
                    </div>
                    <div class="row">
                        <label class="col-sm-4 col-form-label"><?= $lang[4] ?></label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control form-control-sm" name="userdata[name]"
                                   value="<?= $result_user['name'] ?>">
                        </div>
                    </div>
                    <div class="row">
                        <label class="col-sm-4 col-form-label"><?= $lang[612] ?></label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control form-control-sm" name="userdata[email]"
                                   value="<?= $result_user['email'] ?>">
                        </div>
                    </div>
                    <div class="row">
                        <label class="col-sm-4 col-form-label">Tel</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control form-control-sm" name="userdata[tel]"
                                   value="<?= $result_user['tel'] ?>">
                        </div>
                    </div>
                    <div class="row">
                        <label class="col-sm-4 col-form-label">Fax</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control form-control-sm" name="userdata[fax]"
                                   value="<?= $result_user['fax'] ?>">
                        </div>
                    </div>
                    <div class="row">
                        <label class="col-sm-4 col-form-label">Position</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control form-control-sm" name="userdata[position]"
                                   value="<?= $result_user['position'] ?>">
                        </div>
                    </div>
                    <div class="row">
                        <label class="col-sm-4 col-form-label"><?= $lang[126] ?></label>
                        <div class="col-sm-8">
                            <textarea name="userdata[beschreibung]"
                                      class="form-control"><?= htmlentities($result_user["beschreibung"], ENT_QUOTES, $umgvar["charset"]) ?></textarea>
                        </div>
                    </div>

                    <div class="row">
                        <label class="col-sm-4 col-form-label <?= (!$result_user["group_id"] or !$groupdat["name"][$result_user["group_id"]]) ? 'text-danger' : '' ?>"><?= $lang[900] ?></label>
                        <div class="col-sm-8">
                            <div class="row py-2">
                                <div class="col-10">
                                    <a href="main_admin.php?action=setup_group_erg&ID=<?= $result_user["group_id"] ?>"><?= $groupdat["name"][$result_user["group_id"]] ?></a>
                                </div>
                                <div class="col-2">
                                    <?php if ($ID != 1): ?>
                                        <div class="dropdown">
                                            <i class="lmb-icon lmb-pencil cursor-pointer" data-bs-toggle="dropdown"></i>
                                            <div class="dropdown-menu">

                                                <?php
                                                $glitems["name"] = array("maingroup");
                                                $glitems["typ"] = array("radio");
                                                $glsel["maingroup"] = array($result_user["group_id"]);
                                                getGroupTree("GroupSelect_main", $glitems, $glsel);
                                                ?>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <label class="col-sm-4 col-form-label"><?= $lang[901] ?></label>
                        <div class="col-sm-8">
                            <div class="row py-2">
                                <div class="col-10">
                                    <?php
                                    if (is_array($result_user["sub_group"])):
                                        foreach ($result_user["sub_group"] as $key => $value): ?>
                                            <a href="main_admin.php?action=setup_group_erg&ID=<?= $value ?>"><?= $groupdat["name"][$value] ?></a>
                                            <br>
                                        <?php endforeach;
                                    endif;
                                    ?>
                                </div>
                                <div class="col-2">
                                    <div class="dropdown">
                                        <i class="lmb-icon lmb-pencil cursor-pointer" data-bs-toggle="dropdown"></i>
                                        <div class="dropdown-menu">

                                            <?php
                                            $glitems["name"] = array("subgroup");
                                            $glitems["typ"] = array("checkbox");
                                            $glsel["subgroup"] = $result_user["sub_group"];
                                            getGroupTree("GroupSelect_sub", $glitems, $glsel);
                                            ?>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>


                    <?php if ($umgvar['multitenant']) { ?>
                        <div class="row">
                            <label class="col-sm-4 col-form-label"><?= $lang[2965] ?></label>
                            <div class="col-sm-8">
                                <div class="row py-2">
                                    <div class="col-10">
                                        <?php
                                        $result_multitenants = getMultitenant();
                                        if (is_array($result_user["multitenant"])):
                                            foreach ($result_user["multitenant"] as $key => $value): ?>
                                                <?= $result_multitenants['name'][$value] ?><br>
                                            <?php endforeach;
                                        endif;
                                        ?>
                                    </div>
                                    <div class="col-2">
                                        <div class="dropdown">
                                            <i class="lmb-icon lmb-pencil cursor-pointer" data-bs-toggle="dropdown"></i>
                                            <div class="dropdown-menu">
                                                <?php foreach ($result_multitenants['mid'] as $id => $mid):

                                                    $mname = $result_multitenants['name'][$id]; ?>

                                                    <div class="dropdown-item" title="<?= $mid ?>">
                                                        <div class="form-check">
                                                            <label class="form-check-label">
                                                                <input class="form-check-input mt-0" type="checkbox"
                                                                       value="<?= $id ?>"
                                                                       name="userdata[multitenant][]" <?= ($id && is_array($result_user["multitenant"]) && in_array($id, $result_user["multitenant"])) ? 'checked' : '' ?>>
                                                                <?= $mname ?>
                                                            </label>
                                                        </div>
                                                    </div>

                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php } ?>


                    <div class="mb-3 row">
                        <label class="col-sm-4 col-form-label"><?= $lang[903] ?></label>
                        <div class="col-sm-8">
                            <TEXTAREA NAME="userdata[iprange]"
                                      class="form-control form-control-sm"><?= $result_user['iprange'] ?></TEXTAREA>
                        </div>
                    </div>
                    <div class="row">
                        <label class="col-sm-4 col-form-label">Farbkennung</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control form-control-sm" name="userdata[usercolor]"
                                   value="<?= $result_user['usercolor'] ?>"
                                   style="background-color:#<?= $result_user["usercolor"] ?>; color:<?= lmbSuggestColor('#' . $result_user['usercolor']) ?>">
                        </div>
                    </div>

                    <div>
                        <?php if (file_exists(USERPATH . "portrait/portrait_$ID.jpg")) { ?><IMG
                            SRC="USER/portrait/portrait_<?= $ID ?>.jpg" BORDER="1"><?php } ?>
                    </div>


                </div>
                <div class="col-6">

                    <?php
                    if (!$result_user["uploadsize"]) {
                        $result_user["uploadsize"] = $umgvar["default_uloadsize"];
                    }
                    if (!$result_user["maxresult"]) {
                        $result_user["maxresult"] = $umgvar["default_results"];
                    }
                    if (!isset($result_user["logging"])) {
                        $result_user["logging"] = $umgvar["default_loglevel"];
                    }
                    if (!$result_user["dateformat"]) {
                        $result_user["dateformat"] = $umgvar["default_dateformat"];
                    }
                    if (!$result_user["timezone"]) {
                        $result_user["timezone"] = $umgvar["default_timezone"];
                    }
                    if (!$result_user["setlocale"]) {
                        $result_user["setlocale"] = $umgvar["default_setlocale"];
                    }
                    if (!$result_user["farbschema"]) {
                        $result_user["farbschema"] = $umgvar["default_usercolor"];
                    }
                    if (!$result_user["language"]) {
                        $result_user["language"] = $umgvar["default_language"];
                    }
                    ?>

                    <h5><?= $lang[146] ?></h5>

                    <div class="row">
                        <label class="col-sm-4 col-form-label"><?= $lang[1817] ?></label>
                        <div class="col-sm-8">
                            <input type="date" class="form-control form-control-sm" name="userdata[validdate]"
                                   value="<?= $result_user['validdate'] ?>">
                        </div>
                    </div>
                    <div class="row">
                        <label class="col-sm-4 col-form-label"><?= $lang[1300] ?></label>
                        <div class="col-sm-8">
                            <select name="userdata[change_pass]" class="form-select form-select-sm">
                                <OPTION VALUE="true" <?= ($result_user["change_pass"] == "1") ? 'selected' : '' ?>><?= $lang[867] ?></OPTION>
                                <OPTION VALUE="false" <?= (!$result_user["change_pass"]) ? 'selected' : '' ?>><?= $lang[866] ?></OPTION>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <label class="col-sm-4 col-form-label"><?= $lang[2262] ?></label>
                        <div class="col-sm-8">
                            <div class="input-group input-group-sm">
                                <input type="text" class="form-control form-control-sm" name="userdata[gc_maxlifetime]"
                                       value="<?= $result_user['gc_maxlifetime'] ?>">
                                <span class="input-group-text">days</span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <label class="col-sm-4 col-form-label"><?= $lang[656] ?></label>
                        <div class="col-sm-8">
                            <select NAME="userdata[logging]" class="form-select form-select-sm">
                                <OPTION VALUE="0" <?= ($result_user["logging"] == 0) ? 'selected' : '' ?>><?= $lang[1797] ?></OPTION>
                                <OPTION VALUE="1" <?= ($result_user["logging"] == 1) ? 'selected' : '' ?>><?= $lang[1798] ?></OPTION>
                                <OPTION VALUE="2" <?= ($result_user["logging"] == 2) ? 'selected' : '' ?>><?= $lang[1799] ?></OPTION>
                            </select>
                        </div>
                    </div>

                    <hr>

                    <div class="row">
                        <label class="col-sm-4 col-form-label"><?= $lang[624] ?></label>
                        <div class="col-sm-8">
                            <SELECT NAME="userdata[language]" class="form-select form-select-sm">
                                <OPTION VALUE="-1">system</OPTION>
                                <?php
                                $sqlquery = "SELECT DISTINCT LANGUAGE,LANGUAGE_ID FROM LMB_LANG";
                                $rs = lmbdb_exec($db, $sqlquery) or errorhandle(lmbdb_errormsg($db), $sqlquery, $action, __FILE__, __LINE__);
                                while (lmbdb_fetch_row($rs)) {
                                    $langid = lmbdb_result($rs, "LANGUAGE_ID");
                                    echo '<option value="' . urlencode($langid) . '" ' . (($result_user["language"] == $langid) ? 'selected' : '') . '>' . lmbdb_result($rs, "LANGUAGE") . '</option>';
                                }

                                ?>
                            </SELECT>
                        </div>
                    </div>

                    <?php if ($umgvar['multi_language'] and !$result_user["superadmin"]) : ?>
                        <div class="row">
                            <label class="col-sm-4 col-form-label"><?= $lang[2980] ?></label>
                            <div class="col-sm-8">
                                <SELECT NAME="userdata[dlanguage]" class="form-select form-select-sm">
                                    <OPTION VALUE="-1"></OPTION>
                                    <?php

                                    $sqlquery = "SELECT DISTINCT LANGUAGE,LANGUAGE_ID FROM LMB_LANG";
                                    $rs = lmbdb_exec($db, $sqlquery) or errorhandle(lmbdb_errormsg($db), $sqlquery, $action, __FILE__, __LINE__);
                                    while (lmbdb_fetch_row($rs)) {
                                        $langid = lmbdb_result($rs, "LANGUAGE_ID");
                                        echo '<option value=".' . urlencode($langid) . '" ' . (($result_user["dlanguage"] == $langid) ? 'selected' : '') . '>' . lmbdb_result($rs, "LANGUAGE") . '</option>';
                                    }
                                    ?>
                                </SELECT>
                            </div>
                        </div>
                    <?php endif; ?>


                    <div class="row">
                        <label class="col-sm-4 col-form-label"><?= $lang[2576] ?></label>
                        <div class="col-sm-8">
                            <SELECT name="userdata[dateformat]" class="form-select form-select-sm">
                                <OPTION VALUE="1" <?= ($result_user["dateformat"] == '1') ? 'selected' : '' ?>>deutsch
                                </OPTION>
                                <OPTION VALUE="2" <?= ($result_user["dateformat"] == '2') ? 'selected' : '' ?>>english
                                </OPTION>
                                <OPTION VALUE="3" <?= ($result_user["dateformat"] == '3') ? 'selected' : '' ?>>us
                                </OPTION>
                            </SELECT>
                        </div>
                    </div>
                    <div class="row">
                        <label class="col-sm-4 col-form-label"><?= $lang[1622] ?></label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control form-control-sm" name="userdata[timezone]"
                                   value="<?= $result_user['timezone'] ?>">
                        </div>
                    </div>
                    <div class="row">
                        <label class="col-sm-4 col-form-label"><?= $lang[902] ?></label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control form-control-sm" name="userdata[setlocale]"
                                   value="<?= $result_user['setlocale'] ?>">
                        </div>
                    </div>
                    <div class="row">
                        <label class="col-sm-4 col-form-label"><?= $lang[623] ?></label>
                        <div class="col-sm-8">
                            <SELECT name="userdata[farbe_schema]" class="form-select form-select-sm">
                                <?php
                                $sqlquery = "SELECT * FROM LMB_COLORSCHEMES";
                                $rs = lmbdb_exec($db, $sqlquery) or errorhandle(lmbdb_errormsg($db), $sqlquery, $action, __FILE__, __LINE__);
                                while (lmbdb_fetch_row($rs)) {
                                    $farbid = lmbdb_result($rs, "ID");
                                    echo '<OPTION VALUE="' . $farbid . '" ' . (($result_user["farbschema"] == $farbid) ? 'selected' : '') . '>' . lmbdb_result($rs, "NAME") . '</option>';;
                                }
                                ?>
                            </SELECT>
                        </div>
                    </div>
                    <div class="row">
                        <label class="col-sm-4 col-form-label"><?= $lang[698] ?></label>
                        <div class="col-sm-8">
                            <SELECT NAME="userdata[layout]" class="form-select form-select-sm">
                                <?php
                                $layouts = Layout::getAvailableLayouts();
                                foreach ($layouts as $layout) {
                                    echo '<option value="' . $layout . '" ' . (($result_user["layout"] == $layout) ? 'selected' : '') . '>' . $layout . '</option>';
                                }
                                ?>
                            </SELECT>

                        </div>
                    </div>


                    <div class="row">
                        <label class="col-sm-4 col-form-label"><?= $lang[616] ?></label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control form-control-sm" name="userdata[maxresult]"
                                   value="<?= $result_user['maxresult'] ?>">
                        </div>
                    </div>

                    <div class="row">
                        <label class="col-sm-4 col-form-label"><?= $lang[716] ?></label>
                        <div class="col-sm-8">
                            <div class="input-group input-group-sm">
                                <input type="text" class="form-control form-control-sm" name="userdata[uploadsize]"
                                       value="<?= $result_user['uploadsize'] ?>">
                                <span class="input-group-text">Mbyte</span>
                            </div>
                        </div>
                    </div>

                    <?php if ($ID): ?>
                        <hr>

                        <h5><?= $lang[1780] ?></h5>
                        <div class="row">


                        <div class="col-sm-6 d-flex align-items-center">
                            <div class="me-2">
                                <input type="checkbox" class="form-check-input mx-1"
                                       name="userdata[hidden]" <?= ($result_user['hidden']) ? 'checked' : '' ?>>
                            </div>
                            <label class="col-form-label"><?= $lang[2088] ?></label>
                        </div>

                        <?php if ($session['superadmin'] and $ID != 1): ?>
                            <div class="col-sm-6 d-flex align-items-center">

                                <div class="me-2">
                                    <input type="checkbox" class="form-check-input mx-1"
                                           name="userdata[superadmin]" <?= ($result_user['superadmin']) ? 'checked' : '' ?>>
                                </div>
                                <label class="col-form-label">Superadmin</label>
                            </div>
                        <?php endif; ?>

                        <div class="col-sm-6 d-flex align-items-center">

                            <div class="me-2">
                                <input type="checkbox" class="form-check-input mx-1"
                                       OnClick="document.form1.debug.value='1';" <?= ($result_user['debug']) ? 'checked' : '' ?>>
                            </div>
                            <label class="col-form-label"><?= $lang[911] ?></label>
                        </div>
                        <div class="col-sm-6 d-flex align-items-center">

                            <div class="me-2">
                                <input type="checkbox" class="form-check-input mx-1"
                                       name="userdata[staticip]" <?= ($result_user['staticip']) ? 'checked' : '' ?>>
                            </div>
                            <label class="col-form-label"><?= $lang[2353] ?></label>
                        </div>
                        <div class="col-sm-6 d-flex align-items-center">

                            <div class="me-2">
                                <i class="lmb-icon lmb-application-refresh cursor-pointer" onclick="srefresh()"></i>
                            </div>
                            <label class="col-form-label"><a onclick="srefresh()" href=#><?= $lang[904] ?></a></label>
                        </div>
                        <div class="col-sm-6 d-flex align-items-center">

                            <div class="me-2">
                                <i class="lmb-icon lmb-history cursor-pointer" onclick="newwin1('<?= $ID ?>')"></i>
                            </div>
                            <label class="col-form-label"><a onclick="newwin1('<?= $ID ?>')"
                                                                     href=#><?= $lang[1250] ?></a></label>
                        </div>
                        <div class="col-sm-6 d-flex align-items-center">

                            <div class="me-2">
                                <i class="lmb-icon lmb-calendar-alt2 cursor-pointer"
                                   onclick="newwin2('<?= $ID; ?>')"></i>
                            </div>
                            <label class="col-form-label"><a onclick="newwin2('<?= $ID; ?>')"
                                                                     href=#><?= $lang[1791] ?></a></label>
                        </div>

                        </div>

                        <?php if ($ID != 1): ?>
                            <hr>
                        <div class="row">
                            <div class="col-sm-6 d-flex align-items-center">
                                <div class="me-2">
                                    <input type="checkbox" class="form-check-input mx-1"
                                           OnClick="document.form1.lock.value='1';" <?= ($result_user['lock']) ? 'checked' : '' ?>>
                                </div>
                                <label class="col-form-label"><?= $lang[657] ?></label>
                            </div>
                            <div class="col-sm-6 d-flex align-items-center">
                                <label class="col-form-label me-2"><?= $lang[1781] ?></label>
                                <div class="">
                                    <textarea name="userdata[locktxt]"
                                              class="form-control form-control-sm w-100"><?= $result_user['locktxt'] ?></textarea>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>

                    <?php endif; ?>


                </div>
            </div>
            <div class="mt-3 pt-3 border-top">
                <div class="row">
                    <div class="col">
                        <div class="d-flex align-items-center">
                        <button type="button" class="btn btn-primary btn-sm me-2"
                                onclick="send('<?= $action ?>');"><?= $lang[522] ?></button>
                        <?php if ($session["user_id"] != $ID && $ID) { ?>
                            <div class="form-check form-check-inline">
                                <label class="form-check-label">
                                    <input class="form-check-input" type="checkbox" value="1" name="usermail">
                                    <?= $lang[2577] ?>
                                </label>
                            </div>
                        <?php } ?>
                        </div>
                    </div>
                    <div class="col">
                        <?php if ($result_user["username"] != 'admin' && $session["group_id"] == 1 && $ID): ?>


                            <button type="button" class="btn btn-outline-danger btn-sm me-2"
                                    onclick="document.form1.delete_user_total.value = 1; delete_user('<?= $result_user["user_id"] ?>','<?= $result_user["username"] ?>');"><?= $lang[160] ?></button>
                            <button type="button" class="btn btn-outline-warning btn-sm me-2"
                                    onclick="delete_user('<?= $result_user["user_id"] ?>','<?= $result_user["username"] ?>');"><?= $lang[2811] ?></button>
                            <div class="form-check form-check-inline">
                                <label class="form-check-label">
                                    <input class="form-check-input" type="checkbox" value="1" name="delete_user_files"
                                           checked>
                                    <?= $lang[1481] ?>
                                </label>
                            </div>
                            <div class="py-2">
                                <?= $lang[1727] ?>
                            </div>

                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

    </form>
</div>
