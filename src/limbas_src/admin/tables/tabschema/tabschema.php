<div class="ajax_container" ID="fieldinfo"
     style="width:300px;position:absolute;z-index:99999;border:1px solid black;padding:4px;visibility:hidden;background-color:<?= $farbschema['WEB11'] ?>">
</div>


<?php
global $gtab;
global $lang;

if($deleteG) {
    lmb_DeleteGroup($groupname);
}

if ($groupname):
    ?>
    <div id="tablist" class="ajax_container overflow-auto m-0 p-0" style="max-height:300px; visibility: hidden">
        <ul id="tablist_ul" class="list-group p-0 m-0">
            <?php
            $presentTabs = lmb_getTabNameByGroup($groupname);
            foreach ($gtab["table"] as $tableID => $tableName):
                if ($gtab["typ"][$tableID] == 5) {
                    continue;
                }
                ?>
                <li id="li_<?= $tableID ?>"
                    style="<?= $presentTabs[$tableName] ? 'display: none' : '' ?>"
                    class="list-group-item px-1 py-0" data-tabadd="<?= $tableID ?>"><?= $tableName ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php
endif;
?>

<div ID="container-fluid" class="" STYLE="width: 100%; height: 80vh;">
    <div id="vieweditorPattern" class="overflow-auto position-relative w-100 h-100 border"
         data-groupname="<?= $groupname ?>">
        <svg class="overflow-visible" height="100%" width="100%">
            <defs>
                <marker
                        id="arrow"
                        viewBox="0 0 10 10"
                        refX="5"
                        refY="5"
                        markerWidth="6"
                        markerHeight="6"
                        orient="auto-start-reverse">
                    <path d="M 0 0 L 10 5 L 0 10 z"/>
                </marker>
            </defs>
            <g id="svg_lines">

            </g>
        </svg>
        <?php

        if ($setdrag) {
            $groupnamedrag = strstr($setdrag, ";", true);
            $groupnamedrag = str_replace(";", "", $groupnamedrag);

            $groupnamedrag = explode(":", $groupnamedrag);
            if($groupnamedrag[0] != 'group') {
                $groupnamedrag = $groupnamedrag[0];
                lmb_SetTabschemaPattern($setdrag, groupname: $groupname);
            } else {
                $groupnamedrag = $groupnamedrag[1];
                $setdragWOgroup = substr(strstr($setdrag, ";"), 1);
                lmb_SetTabschemaPattern($setdragWOgroup, groupname: $groupnamedrag);
            }
        }

        if ($groupnamedrag) {
            $pat = lmb_GetTabschemaPattern(groupname: $groupnamedrag);
        } else {
            $pat = lmb_GetTabschemaPattern(groupname: $groupname);
        }

        foreach ($pat["id"] as $key => $id) {

            if ($gtab["table"][$key]) {
                schema_tab($pat, $key, $pat["posx"][$key], $pat["posy"][$key], $groupnamedrag ?: $groupname);
                schema_link($key);
            }
        }

        ?>
    </div>
</div>

<?php
$addGroupLang = $lang[561] . ' ' . $lang[540];
$clearGroupLang = $lang[160];
$standardGroupName = $lang[3056];
?>

<div class="row mt-3">
    <div class="col d-flex align-items-center">
        <b><?= $groupname ?: $standardGroupName ?></b>
    </div>
    <div class="col-6 ps-0">
        <form ACTION="main_admin.php" METHOD="post" name="form1" class="d-flex align-items-center">
            <input type="hidden" name="action" value="<?= $action ?>">
            <input type="hidden" name="setdrag">
            <input type="hidden" name="deleteG">

            <?php if($groupname): ?>
                <button id="deleteButton" class="btn btn-danger p-1 w-50 m-1" type="submit">
                    <?= $clearGroupLang ?>
                </button>
            <?php endif; ?>

            <button id="groupButton" class="btn btn-primary p-1 w-50" type="submit">
                <?= $addGroupLang ?>
            </button>

            <input id="newGroupInput" name="newgroupname" class="form-control form-control p-1 ms-2 w-100">

            <select id="groupSelect" name="groupname" class="form-select pe-4 p-1 ms-2 w-100">
                <option selected value="<?= $groupname ?>"><?= $groupname ?: $standardGroupName ?></option>
                <?php foreach (lmb_getGroupNames() as $key => $grName):
                    if ($grName != $groupname): ?>
                        <option value="<?= $grName ?>"><?= $grName ?: $standardGroupName ?></option>
                    <?php endif;
                endforeach; ?>
            </select>
        </form>

    </div>
</div>