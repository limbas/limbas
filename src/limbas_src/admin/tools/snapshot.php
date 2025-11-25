<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */


use Limbas\gtab\lib\tables\TableFilter;
use Limbas\gtab\lib\tables\TableFilterGroup;
use Limbas\lib\general\Log\Log;


/**
 * @deprecated create and save new TableFilterGroup instance 
 * @param $name
 * @return bool
 */
function SNAP_add_group($name): bool
{
    Log::deprecated('Method ' . __METHOD__ . ' is deprecated. Create and save new TableFilterGroup instance');
    $tableFilterGroup = new TableFilterGroup(0,$name);
    return $tableFilterGroup->save();
}

/**
 * @deprecated delete instance of TableFilterGroup
 * @param $snapgroup_id
 * @return bool
 */
function SNAP_del_group($snapgroup_id){
    
    Log::deprecated('Method ' . __METHOD__ . ' is deprecated. Delete instance of TableFilterGroup');
    
    $tableFilterGroup = TableFilterGroup::get(intval($snapgroup_id));
    if($tableFilterGroup) {
        return $tableFilterGroup->delete();
    }

    return false;
}

/**
 * @deprecated update instance of TableFilterGroup
 * @param $snapgroup_id
 * @param $name
 * @param $intern
 * @return bool
 */
function SNAP_update_group($snapgroup_id,$name,$intern){

    Log::deprecated('Method ' . __METHOD__ . ' is deprecated. Update instance of TableFilterGroup');
    
    $tableFilterGroup = TableFilterGroup::get(intval($snapgroup_id ?? 0));

    if($name) {
        $tableFilterGroup->name = $name;
    }
    if($intern) {
        $tableFilterGroup->intern = $intern;
    }
    return $tableFilterGroup->save();
}



function show_snapDetail(array &$gsnap_, $tabId, $snapshotId, $group, bool $showTableName = true): void
{
    global $userdat;
    global $LINK;
    global $gtab;
    global $lang;
    global $gsnapgroup;

    if ($tabId) {
        $tablename = $gtab['table'][$tabId];
    } else {
        $tablename = 'extension only';
    }
    if ($gsnap_[$tabId]['type'][$snapshotId] == 2) {
        $icon = 'fa-solid fa-magnifying-glass';
        $title = $lang[3161];
    } else {
        $icon = 'fa-solid fa-filter';
        $title = $lang[1602];
    }

    $htmlATableViewLink =
        $tabId && $gsnap_[$tabId]['type'][$snapshotId] != 2
            ? "<a href=\"main.php?action=gtab_erg&gtabid=$tabId&snapid=$snapshotId&snap_id=$snapshotId\" class='d-flex align-items-center' target=\"_new\"><i class=\"lmb-icon lmb-list-ul-alt\"></i></a>"
            : "<i class=\"lmb-icon lmb-list-ul-alt invisible\"></i>";

    $htmlSnapshotShare =
        $LINK[225]
            ? "<i class=\"lmb-icon lmb-groups\" onclick=\"limbasSnapshotShare(this,$snapshotId,'');\" STYLE=\"cursor:pointer;\"></i>"
            : "<i class=\"lmb-icon lmb-groups invisible\"></i>";

    $htmlTableName =
        $showTableName
            ? "<div class=\"col\">{$tablename}</div>"
            : "";

    echo <<<HTML
    <div class="row d-flex align-items-center">
        <div class="col-3">
            <div class="input-group">
                <span class="input-group-text" title="$title"><i class="$icon"></i></span>
                <input type="text" class="form-control" value="{$gsnap_[$tabId]["name"][$snapshotId]}" onchange="edit_snap('{$tabId}','{$snapshotId}',this.value,1)">
            </div>
        </div>

        $htmlTableName

        <div class="col">
        
            <select title="$lang[2785]" class="form-select form-select-sm" onchange="edit_snap('$tabId','$snapshotId',this.value,2)">
                <option value="0"></option>
    HTML;
                foreach ($gsnapgroup['name'] as $key => $value) {
                    echo "<option value=\"$key\" ".($key == $group ? 'selected' : '').">$value</option>";
                }
    echo <<<HTML
            </select>

        </div>
        
        <div class="col d-flex align-items-center">
            $htmlATableViewLink
            <i class="lmb-icon lmb-pencil" style="cursor:pointer" border="0" onclick="lmbSnapExtension(this,{$snapshotId})"></i>
            <input type="hidden" name="snap_extvalue[{$snapshotId}]" value="{$gsnap_[$tabId]["ext"][$snapshotId]}">
            $htmlSnapshotShare
            <i class="lmb-icon lmb-trash" onclick="document.form1.del.value={$snapshotId};document.form1.gtabid.value={$tabId};document.form1.submit();" style="cursor:pointer;"></i>
        </div>
        
        <div class="col">
            &nbsp; (ID {$snapshotId}) &nbsp; <i>{$userdat["bezeichnung"][$gsnap_[$tabId]["user_id"][$snapshotId]]}</i>
        </div>
    </div>
    HTML;
}




if (!$snap_view) {
    $snap_view = 1;
}



if($new_snapgroup AND $new_snapgroupname){
    (new TableFilterGroup(0,$new_snapgroupname))->save();
}

$tableFilterGroup = TableFilterGroup::get(intval($groupid ?? 0));
if($tableFilterGroup){    
    if($del){
        $tableFilterGroup->delete();
    }
    elseif($group_name || $group_intern) {
        if($group_name) {
            $tableFilterGroup->name = $group_name;
        }
        if($group_intern) {
            $tableFilterGroup->intern = filter_var($group_intern, FILTER_VALIDATE_BOOLEAN);
        }
        $tableFilterGroup->save();
    }
    
}

$gsnapgroup = SNAP_get_group();

if ($newsnap and $new_snapname) {
    $new_snapname = preg_replace("/[^[:alnum:]_-äöüÄÖÜ]/", '', $new_snapname);

    if (!$gtabid and $new_snapgroup) {
        $snap_view = 2; // show all filter
    }

    $NEXTID = next_db_id("LMB_SNAP");
    $sqlquery = "INSERT INTO LMB_SNAP (ID,USER_ID,TABID,NAME,SNAPGROUP,TYPE) VALUES ($NEXTID," . $session["user_id"] . "," . parse_db_int($gtabid) . ",'" . parse_db_string($new_snapname, 30) . "'," . parse_db_int($new_snapgroup) . ",1)";
    $rs = lmbdb_exec($db, $sqlquery) or errorhandle(lmbdb_errormsg($db), $sqlquery1, $action, __FILE__, __LINE__);
}


if ($del) {
    $sqlquery = "DELETE FROM LMB_SNAP_SHARED WHERE ID = $del";
    $rs = lmbdb_exec($db, $sqlquery) or errorhandle(lmbdb_errormsg($db), $sqlquery, $action, __FILE__, __LINE__);
    if (!$rs) {
        $commit = 1;
    }

    $sqlquery = "DELETE FROM LMB_SNAP WHERE ID = $del";
    $rs = lmbdb_exec($db, $sqlquery) or errorhandle(lmbdb_errormsg($db), $sqlquery, $action, __FILE__, __LINE__);
    if (!$rs) {
        $commit = 1;
    } else {
        unset($gsnap[$gtabid]['id'][$del]);
        unset($gsnap[$gtabid]['name'][$del]);
        unset($gsnap[$gtabid]['filter'][$del]);
        if (lmb_count($gsnap[$gtabid]['id']) == 0) {
            unset($gsnap[$gtabid]);
        }
    }
}

if ($snap_edit and $snapid) {
    if (isset($snap_name) AND $snap_name = trim($snap_name)) {
        $update[] = "NAME = '" . parse_db_string(str_replace(";", ",", $snap_name), 50) . "'";
        $gsnap[$gtabid]['name'][$snapid] = lmb_substr(str_replace(";", ",", $snap_name), 0, 50);
    }
    if (isset($snap_group)) {
        $update[] = "SNAPGROUP = " . parse_db_int( $snap_group);
        $gsnap[$gtabid]['group'][$snapid] = parse_db_int( $snap_group);
    }

    if ($update) {
        $update = implode(",", $update);
        $sqlquery = "UPDATE LMB_SNAP SET $update WHERE ID = $snapid";
        $rs = lmbdb_exec($db, $sqlquery) or errorhandle(lmbdb_errormsg($db), $sqlquery, $action, __FILE__, __LINE__);
    }
}

if ($snap_extension) {
    $prepare_string = "UPDATE LMB_SNAP SET EXT = ? WHERE ID = " . $snap_extension;
    if (!lmb_PrepareSQL($prepare_string, array($snap_extensionValue), __FILE__, __LINE__)) {
        $commit = 1;
    }
}


?>


    <script>

        function edit_snapgroup(groupid, val, typ) {
            $('#'+typ).val(val);
            document.form1.groupid.value = groupid;
            document.form1.submit();
        }

        function edit_snap(gtabid, snapid, val, typ) {
            document.form1.gtabid.value = gtabid;
            document.form1.snapid.value = snapid;
            if (typ == 1) {
                document.form1.snap_name.value = val;
            } else if (typ == 2) {
                document.form1.snap_group.value = val;
            }

            document.form1.snap_edit.value = 1;
            document.form1.submit();
        }

        function nav_refresh(gtabid, snapid, val) {
            if (parent.nav) {
                parent.nav.document.location.href = 'main.php?action=nav&sparte=gtab&refresh=no';
            }
            if (parent.parent.nav) {
                parent.parent.nav.document.location.href = 'main.php?action=nav&sparte=gtab&refresh=no';
            }
        }

        function limbasSnapshotShare(el, snap_id, destUser, del, edit, drop) {
            if (typeof (del) == "undefined") {
                del = 0;
            }
            if (typeof (edit) == "undefined") {
                edit = 0;
            }
            if (typeof (drop) == "undefined") {
                drop = 0;
            }

            ajaxGet('', 'main_dyns.php', 'manageTableFilters&action=shareSelect&gtabid=' + snap_id + '&destUser=' + destUser + '&del=' + del + '&edit=' + edit + '&drop=' + drop, '', function (result) {
                $('#lmbAjaxContainer').html(result).show();
                if (el) {
                    $('#modal_publicShare').modal('show');
                }
            });

        }

        function lmbSnapShareSelect(ugval, snapname, gtabid) {
            limbasSnapshotShare(null, gtabid, ugval);
        }

        function lmbSnapExtension(el, snapid) {
            document.form1.snap_extension.value = snapid;
            $('#modal_snapExtension').modal('show');
            document.form1.snap_extensionValue.value = document.form1.elements['snap_extvalue[' + snapid + ']'].value;
        }

        var activ_menu = null;

        function divclose() {
            if (!activ_menu) {
                limbasDivClose('');
            }
            activ_menu = 0;
        }

    </script>


    <form action="main_admin.php" method="post" name="form1">
        <input type="hidden" name="action" value="setup_snap">
        <input type="hidden" name="snap_view" value="<?= $snap_view ?>">
        <input type="hidden" name="gtabid">
        <input type="hidden" name="snapid">
        <input type="hidden" name="snap_id">
        <input type="hidden" name="snap_name">
        <input type="hidden" name="snap_group">
        <input type="hidden" name="snap_edit">
        <input type="hidden" name="snap_extension">
        <input type="hidden" name="del" id="del">
        <input type="hidden" name="groupid">
        <input type="hidden" name="group_intern" id="group_intern">
        <input type="hidden" name="group_name" id="group_name">

        <!-- Modal Extensions -->
        <div class="modal" id="modal_snapExtension">
            <div class="modal-dialog">
                <div class="modal-content">

                    <div class="modal-header">
                        <h4 class="modal-title">Extension</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                    <textarea id="snap_extensionValue" name="snap_extensionValue"
                              style="width:400px;height:250px;"></textarea>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" onclick="document.form1.submit();">
                            Save changes
                        </button>
                        <button type="button" class="btn btn-danger" onclick="document.form1.snap_extension.value = '';"
                                data-bs-dismiss="modal">
                            Close
                        </button>
                    </div>

                </div>
            </div>
        </div>

        <!-- Modal public share -->
        <div class="modal" id="modal_publicShare">
            <div class="modal-dialog">
                <div class="modal-content">

                    <div class="modal-header">
                        <h4 class="modal-title">Public share</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body" id="lmbAjaxContainer">
                    </div>

                </div>
            </div>
        </div>


        <div class="container-fluid p-3">
            <ul class="nav nav-tabs">
                <?php if ($snap_view == 1) : ?>
                    <li class="nav-item">
                        <a class="nav-link active bg-contrast"
                           href="#"><?= $lang[2498] ?> <?= $lang[102] ?> <?= $lang[164] ?></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link"
                           href="main_admin.php?action=setup_snap&snap_view=2&show_public_filter=<?= $show_public_filter ?>"><?= $lang[2498] ?> <?= $lang[102] ?> <?= $lang[2785] ?></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link"
                           href="main_admin.php?action=setup_snap&snap_view=3"><?= $lang[2785] ?></a>
                    </li>

                <?php elseif ($snap_view == 2): ?>
                    <li class="nav-item">
                        <a class="nav-link"
                           href="main_admin.php?action=setup_snap&snap_view=1&show_public_filter=<?= $show_public_filter ?>"><?= $lang[2498] ?> <?= $lang[102] ?> <?= $lang[164] ?></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active bg-contrast"
                           href="#"><?= $lang[2498] ?> <?= $lang[102] ?> <?= $lang[2785] ?></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link"
                           href="main_admin.php?action=setup_snap&snap_view=3"><?= $lang[2785] ?></a>
                    </li>

                <?php elseif ($snap_view == 3): ?>
                    <li class="nav-item">
                        <a class="nav-link"
                           href="main_admin.php?action=setup_snap&snap_view=1&show_public_filter=<?= $show_public_filter ?>"><?= $lang[2498] ?> <?= $lang[102] ?> <?= $lang[164] ?></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link"
                           href="main_admin.php?action=setup_snap&snap_view=2&show_public_filter=<?= $show_public_filter ?>"><?= $lang[2498] ?> <?= $lang[102] ?> <?= $lang[2785] ?></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active bg-contrast"
                           href="#"><?= $lang[2785] ?></a>
                    </li>

                <?php endif; ?>


            </ul>


            <div class="tab-content border border-top-0 bg-contrast">
                <div class="tab-pane active">


                    <?php

                    if ($snap_view == 1 OR $snap_view == 2) {

                    $show_public_filter = $show_public_filter ? 1 : 2;
                    $gsnap_ = TableFilter::loadInSession(null, $show_public_filter);

                    ?>
                    <ul class="list-group">
                        <?php
                        if ($snap_view == 1) {
                            if ($tabgroup["name"] and $gtab["tab_id"]) {
                                foreach ($tabgroup["name"] as $tabGroupId => $tabGroupName) {
                                    $showTabGroup = false;
                                    foreach (array_keys($gtab["tab_id"] ?? []) as $tabIDfe) {
                                        if ($gsnap_[$tabIDfe] and $gtab["tab_group"][$tabIDfe] == $tabgroup["id"][$tabGroupId] && array_sum($gsnap_[$tabIDfe]["user_id"]) > 0) {
                                            if (!$showTabGroup) {
                                                echo "<li class=\"list-group-item border-0\">$tabGroupName<ul class=\"list-group\">";
                                                $showTabGroup = true;
                                            }
                                            echo "<li class=\"list-group-item list-group-item-primary\">{$lang[4]}: {$gtab["desc"][$tabIDfe]}<ul class=\"list-group list-group-item-primary\">";
                                            foreach (array_keys($gsnap_[$tabIDfe]["id"] ?? []) as $snapshotId) {
                                                show_snapDetail($gsnap_, $tabIDfe, $snapshotId, $gsnap_[$tabIDfe]["group"][$snapshotId], false);
                                            }
                                            echo "</ul></li>";
                                        }
                                    }
                                    if ($showTabGroup) {
                                        echo "</ul></li>";
                                    }
                                }
                            }
                        } elseif ($snap_view == 2) {
                            foreach (array_keys($gsnap_[-1] ?? []) as $snapshotGroup) {
                                ?>
                                <li class="list-group-item border-0"><?= $lang[2785] ?>: <?= $snapshotGroup ?>
                                    <ul class="list-group">
                                        <li class="list-group-item list-group-item-primary">
                                            <?php
                                            foreach ($gsnap_[-1][$snapshotGroup] as $snapshotId => $gsnapTabId) {
                                                $tabId = $gsnap_["argresult_id"][$gsnapTabId];
                                                show_snapDetail($gsnap_, $tabId, $snapshotId, $snapshotGroup);
                                            }
                                            ?>
                                        </li>
                                    </ul>
                                </li>
                                <?php
                            }
                        }
                        ?>
                    </ul>

                    <ul class="list-group">
                        <li class="list-group-item border-0">
                            <table>
                                <tr>
                                    <td><?= $lang[4] ?></td>
                                    <td><?= $lang[164] ?></td>
                                    <td><?= $lang[2785] ?></td>
                                </tr>

                                <tr>
                                    <td><input class="form-control form-control-sm" type="text" name="new_snapname">
                                    </td>
                                    <td><select NAME="gtabid" class="form-select form-select-sm">
                                            <option></option>
                                            <?php
                                            foreach ($tabgroup["id"] as $key0 => $value0) {
                                                echo '<optgroup label="' . $tabgroup["name"][$key0] . '">';
                                                foreach ($gtab["tab_id"] as $key => $value) {
                                                    if ($gtab["tab_group"][$key] == $value0) {
                                                        echo "<option value=\"$value\">{$gtab["desc"][$key]}</option>";
                                                    }
                                                }
                                                echo '</optgroup>';
                                            }
                                            ?>
                                        </select></td>
                                    <td>

                                        <select title="<?=$lang[2785]?>" class="form-select form-select-sm" name="new_snapgroup">
                                            <option></option>
                                            <?php
                                            foreach ($gsnapgroup['name'] as $key => $value) {
                                                echo "<option value=\"$key\" ".($key == $group ? 'selected' : '').">$value</option>";
                                            }
                                            ?>
                                        </select>

                                    </td>
                                    <td>
                                        <button class="btn btn-primary btn-sm" TYPE="SUBMIT" NAME="newsnap"
                                                value="1"><?= $lang[2009] ?></button>
                                    </td>
                                    <td>&nbsp;<?= $lang[2784] ?>&nbsp;
                                        <input type="checkbox" name="show_public_filter"
                                               onchange="document.form1.submit();"
                                            <?= $show_public_filter == 1 ? 'checked' : '' ?>>
                                    </td>
                                </tr>
                            </table>
                        </li>
                    </ul>
                </div>

                <?php }else{ ?>

                    <ul class="list-group">
                        <li class="list-group-item border-0">


                            <?php ?>


                            <div class="row d-flex">

                                <div class="col-1 " >
                                    ID
                                </div>

                                <div class="col ">
                                    Name
                                </div>

                                <div class="col-1">
                                    intern
                                </div>

                                <div class="col-1">
                                    delete
                                </div>
                            </div>


                            <?php
                            foreach($gsnapgroup['name'] as $groupid => $groupname){
                            ?>
                            <div class="row d-flex align-items-center">

                                <div class="col-1" >
                                   <?=$groupid?>
                                </div>

                                <div class="col text-start" >
                                    <input type="text" class="form-control" value="<?=$groupname?>" onchange="edit_snapgroup(<?=$groupid?>,this.value,'group_name')">
                                </div>

                                <div class="col-1">
                                    <input class="form-check-input" type="checkbox" onchange="edit_snapgroup(<?=$groupid?>,this.checked,'group_intern')" <?php echo ($gsnapgroup['intern'][$groupid] ? 'checked' : '') ?> >
                                </div>

                                <div class="col-1">
                                     <i class="lmb-icon lmb-trash cursor-pointer" onclick="edit_snapgroup(<?=$groupid?>,1,'del')" ></i>
                                </div>

                           </div>

                            <?php }?>

                            <hr>

                            <div class="row d-flex">
                                <div class="col-1 " >
                                     Name
                                </div>
                            </div>
                            <div class="row d-flex">

                                <div class="col-3 " >
                                     <input type="text" class="form-control" name="new_snapgroupname">
                                </div>
                                <div class="col-2">
                                    <button class="btn btn-primary btn-sm" TYPE="SUBMIT" NAME="new_snapgroup" value="1"><?= $lang[2785] ?> <?= $lang[571] ?> </button>
                                </div>
                            </div>



                        </li>
                    </ul>

                <?php } ?>

            </div>
        </div>
    </form>
<?php

