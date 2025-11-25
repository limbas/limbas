<?php

namespace Limbas\layout\parts;

use Limbas\Controllers\LimbasController;
use Limbas\lib\db\Database;
use Symfony\Component\HttpFoundation\Request;

/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */
class TableTreeController extends LimbasController
{

    public function handleRequest(Request|array $request): array
    {
        return match ($request->get('action')) {
            'savePath' => $this->postRecursiveTableTreePath($request),
            'getTree' => $this->getTableTree($request),
            default => ['success' => false]
        };
    }

    private function getTableTree($request): array
    {
        global $gtab;
        global $gfield;
        global $filter;
        global $gverkn;
        global $session;
        global $db;

        static $tree;

        require_once(COREPATH . 'gtab/gtab.lib');
        require_once(COREPATH . 'gtab/gtab_type_erg.lib');

        $gtabid = $request->get('gtabid');
        $treeid = $request->get('treeid');
        $verkn_tabid = $request->get('verkn_tabid');
        $verkn_fieldid = $request->get('verkn_fieldid');
        $verkn_ID = $request->get('verkn_ID');
        $openFullTree = boolval($request->get('open_full_tree'));

        $treeid = str_replace("PH_279_2790", "", $treeid);

        if (!$tree) {
            $tree = $this->fetchTableTreeData($treeid);
        }

        $this->postRecursiveTableTreePath($request);

        $html = $this->constructTableTree($gtabid, $tree, $extension, $treeid, $verkn_tabid, $verkn_fieldid, $verkn_ID, $openFullTree);

        return ['success' => true, 'html' => $html];
    }

    private function fetchTableTreeData($treeid): array
    {
        $tree = [];

        $rs = Database::select('lmb_tabletree', where: ['treeid' => $treeid]);
        while (lmbdb_fetch_row($rs)) {
            if ($md5tab = lmbdb_result($rs, "RELATIONID")) {
                $tree['tform'][$md5tab] = lmbdb_result($rs, "TARGET_FORMID");
                $tree['tsnap'][$md5tab] = lmbdb_result($rs, "TARGET_SNAP");
                $tree['display'][$md5tab] = lmbdb_result($rs, "DISPLAY");
                $tree['tfield'][$md5tab] = lmbdb_result($rs, "DISPLAY_FIELD");
                $tree['ttitle'][$md5tab] = lmbdb_result($rs, "DISPLAY_TITLE");
                $tree['tsort'][$md5tab] = lmbdb_result($rs, "DISPLAY_SORT");
                $tree['ticon'][$md5tab] = lmbdb_result($rs, "DISPLAY_ICON");
                $tree['trule'][$md5tab] = lmbdb_result($rs, "DISPLAY_RULE");
            }
        }

        return $tree;
    }

    private function constructTableTree($gtabid, $tree, $extension, $treeid, $verkn_tabid, $verkn_fieldid, $verkn_ID, bool $openFullTree, $mSettingTree = null, $path = ''): string
    {
        global $gfield;
        global $gverkn;
        global $gsr;

        $md5tab = $gfield[$verkn_tabid]["md5tab"][$verkn_fieldid];
        if (!$md5tab) {
            $md5tab = "top";
        }

        // get msettings for restore on full tree opening, skip on recursion
        if ($mSettingTree === null && $openFullTree) {
            $mSetting = $this->fetchMSetting();
            if ($mSetting) {
                $mSettingTree = $mSetting['tabletree'][$treeid][$gtabid];
                $path = "{$treeid}_{$gtabid}";
            }
        }

        $tfield = $tree['tfield'][$md5tab];
        $ttitle = $tree['ttitle'][$md5tab];
        $tsort = $tree['tsort'][$md5tab];
        $tform = $tree['tform'][$md5tab];
        $trule = $tree['trule'][$md5tab];

        if ($gverkn[$gtabid]["id"]) {
            foreach ($gverkn[$gtabid]["id"] as $rkey => $rval) {
                $of[] = $rkey;
            }
            $onlyfield[$gtabid] = $of;
        }
        $fieldid = $gfield[$gtabid]["mainfield"];
        $onlyfield[$gtabid][] = $fieldid;

        $tfield = $tfield ?: $gfield[$gtabid]["fieldkey_id"][$fieldid];

        if ($tfield) {
            $onlyfield[$gtabid][] = $tfield;
        }
        if ($ttitle) {
            $onlyfield[$gtabid][] = $ttitle;
        }
        if ($tsort) {
            $filter["order"][$gtabid][0] = array($gtabid, $tsort);
        }
        $verkn = null;
        if ($verkn_ID) {
            $verkn = init_relation($verkn_tabid, $verkn_fieldid, $verkn_ID, null, null, 1);
        }
        $filter['anzahl'][$gtabid] = 'all';
        if (trim($trule)) {
            eval($trule . ";");
        }
        $gresult = get_gresult($gtabid, 1, $filter, $gsr, $verkn, $onlyfield, null, $extension);
        $gsum = lmb_count($gresult[$gtabid]['id']);
        $rand = mt_rand();
        $bzm = 1;

        $htmlRows = '';
        if (is_array($gresult[$gtabid]['id'])) {
            foreach ($gresult[$gtabid]['id'] as $gkey => $gval) {
                $imgpref = $gverkn[$gtabid]["id"] ? 'plus' : 'join';
                $outliner = ($bzm == $gsum) ? "bottom" : (($bzm == 1) ? "top" : "");

                $gvalue = '';
                if ($tfield) {
                    $fname = "cftyp_" . $gfield[$gtabid]["funcid"][$tfield];
                    $gvalue = $fname($gkey, $tfield, $gtabid, 3, $gresult, 0);
                } elseif ($fieldid) {
                    $fname = "cftyp_" . $gfield[$gtabid]["funcid"][$fieldid];
                    $gvalue = $fname($gkey, $fieldid, $gtabid, 3, $gresult, 0);
                }
                $gvalue = trim($gvalue) ?: 'unknown';

                $gtitle = '';
                if ($ttitle) {
                    $fname = "cftyp_" . $gfield[$gtabid]["funcid"][$ttitle];
                    $gtitle = htmlentities($fname($gkey, $ttitle, $gtabid, 3, $gresult, 0), ENT_QUOTES, $GLOBALS["umgvar"]["charset"]);
                }


                $htmlSubRows = '';
                if ($gverkn[$gtabid]["id"]) {
                    foreach ($gverkn[$gtabid]["id"] as $rkey => $rval) {

                        if ($gfield[$gtabid]["verkntabletype"][$rkey] == 2) {
                            continue;
                        }
                        if ($tree['display'][$gfield[$gtabid]["md5tab"][$rkey]]) {
                            continue;
                        }
                        if ($gresult[$gtabid][$rkey][$gkey]) {
                            $simgpref = "plusonly";
                        } else {
                            $simgpref = "hline";
                        }
                        if ($tree['ticon'][$gfield[$gtabid]["md5tab"][$rkey]]) {
                            $icon = $tree['ticon'][$gfield[$gtabid]["md5tab"][$rkey]];
                        } else {
                            $icon = "lmb-folder-closed";
                        }

                        $display = 'display: none';
                        $subrows = '';
                        $data = '';
                        if ($mSettingTree && $mSettingTree[$gval][$rkey]) {
                            $display = '';
                            $recPath = "{$path}_{$gval}_{$rkey}";
                            $subrows = $this->constructTableTree($rval, $tree, $extension, $treeid, $gtabid, $rkey, $gval, false, $mSettingTree[$gval][$rkey], $recPath);
                            $simgpref = 'minusonly';
                            $data = "data-recursivepath= \"$recPath\"";
                        }


                        $uniqueId = "{$treeid}_{$rval}_{$gval}_{$rand}";
                        $htmlSubRows .= <<<HTML
                        <tr>
                            <td style="width:18px">
                                <img class="lmb-image-as-icon" src="assets/images/legacy/outliner/join.gif">
                            </td>
                            <td style="width:18px">
                                <img class="lmb-image-as-icon" src="assets/images/legacy/outliner/{$simgpref}.gif" style="cursor:pointer" id="lmbTreeSubPlus_$uniqueId" onclick="lmb_treeSubOpen('{$treeid}','{$rval}','{$gval}','{$rand}','{$gtabid}','{$rkey}');event.stopPropagation();">
                            </td>
                            <td style="width:18px">
                                <i class="lmb-icon {$icon}" border="0" id="lmbTreeSubBox_$uniqueId"></i>
                            </td>
                            <td>
                                <b>
                                    <a class="lmbFileTreeItem" onclick="event.stopPropagation();lmbTreeOpenTable('{$rval}','{$gtabid}','{$rkey}','{$gval}');">{$gfield[$gtabid]["spelling"][$rkey]}</a>
                                </b>
                            </td>
                        </tr>
                        <tr style="$display" id="lmbTreeTR_$uniqueId" $data>
                            <td style="background-image:url(assets/images/legacy/outliner/line.gif);background-repeat:repeat-y;"></td>
                            <td></td>
                            <td colspan="2">
                                <div id="lmbTreeDIV_$uniqueId">$subrows</div>
                            </td>
                        </tr>
                    HTML;
                    }
                }
                $bzm++;
                $display = 'display: none';
                $data = '';
                if ($mSettingTree && $mSettingTree[$gval]) {
                    $display = '';
                    $imgpref = 'minus';
                    $recPath = "{$path}_{$gval}";
                    $data = "data-recursivepath= \"$recPath\"";
                }

                $uniqueId = "{$treeid}_{$gtabid}_{$gval}_{$rand}";
                $htmlRows .= <<<HTML
                <tr>
                    <td style="width:18px">
                        <img src="assets/images/legacy/outliner/{$imgpref}{$outliner}.gif" id="lmbTreePlus_$uniqueId" style="cursor:pointer" onclick="lmb_treeElOpen('$treeid','$gtabid','$gval','$rand');event.stopPropagation();">
                    </td>
                    <td>
                        <a class="lmbFileTreeItem" onclick="event.stopPropagation();lmbTreeOpenData('$gtabid','$gval','$verkn_tabid','$verkn_fieldid','$verkn_ID','$tform')" title="$gtitle">$gvalue</a>
                    </td>
                </tr>
                <tr style="$display" id="lmbTreeEl_$uniqueId" $data>
                    <td colspan="2">
                        <table>
                            $htmlSubRows
                        </table>
                    </td>
                </tr>
            HTML;
            }
        }

        return <<<HTML
        <table class="w-100">
            $htmlRows
        </table>
    HTML;
    }

    /**
     * Save the given recursive_path to m_settings['tabletree'], to restore the tabletree on re-open
     * @param $request
     * @return array
     */
    private function postRecursiveTableTreePath($request): array
    {
        $recursivePath = $request->get('recursive_path');
        $removePath = boolval($request->get('remove_path'));

        $success = $this->savePath($recursivePath, $removePath);

        return ['success' => $success];
    }

    private function savePath(string $recursivePath, bool $removePath): bool
    {
        $mSetting = $this->fetchMSetting();

        $pathIds = array_reverse(explode('_', $recursivePath));
        $recursivePathArray = true;
        foreach ($pathIds as $key) {
            $recursivePathArray = [$key => $recursivePathArray];
        }

        if (!isset($mSetting['tabletree'])) {
            $mSetting['tabletree'] = [];
        }

        $tabletreeSettings = &$mSetting['tabletree'];
        if ($removePath) {
            $this->removeRecursivePath($tabletreeSettings, $recursivePathArray);
        } else {
            $this->addRecursivePath($tabletreeSettings, $recursivePathArray);
        }

        $this->postMSetting($mSetting);

        return true;
    }

    private function removeRecursivePath(array &$array, array $recursivePathArray): void
    {
        function unsetRecursivePathRec(array &$array, array $recursivePathArray, $oldPathKey): void
        {
            foreach ($recursivePathArray as $pathKey => $pathId) {
                if (is_array($pathId) && is_array($array[$oldPathKey])) {
                    unsetRecursivePathRec($array[$oldPathKey], $pathId, $pathKey);
                } else {
                    if (is_array($array[$oldPathKey]) && count($array[$oldPathKey]) > 1) {
                        unset($array[$oldPathKey][$pathKey]);
                    } else {
                        $array[$oldPathKey] = true;
                    }
                }
            }
        }

        foreach ($recursivePathArray as $pathKey => $pathId) {
            if (is_array($pathId)) {
                unsetRecursivePathRec($array, $pathId, $pathKey);
            }
        }

        foreach ($array as $treeId => $tabId) {
            if (!is_array($tabId)) {
                unset($array[$treeId]);
            }
        }
    }

    private function addRecursivePath(array &$array, array $recursivePathArray): void
    {
        foreach ($recursivePathArray as $key => $value) {
            if (is_array($value) && isset($array[$key]) && is_array($array[$key])) {
                $this->addRecursivePath($array[$key], $value);
            } else {
                if (!(is_array($array[$key]) && !is_array($value))) {
                    $array[$key] = $value;
                }
            }
        }
    }

    private function fetchMSetting(): array
    {
        global $session;

        $rs = Database::select('LMB_USERDB', ['M_SETTING'], ['USER_ID' => $session['user_id']]);

        if ($m_setting = lmbdb_result($rs, "M_SETTING")) {
            return unserialize($m_setting);
        }
        return [];
    }

    private function postMSetting($mSetting): void
    {
        global $session;

        Database::update('LMB_USERDB', ['M_SETTING' => serialize($mSetting)], ['USER_ID' => $session['user_id']]);
    }
}
