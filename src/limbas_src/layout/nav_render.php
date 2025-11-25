<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */


?>


<?php
function recRender($entryKey, $entry, $depth = 0, $data = null) {
    global $menu_setting;
    global $defaultExpandMenu;
    global $tabGroupIndex;
    global $lang;
    global $dwme;
    global $farbschema;
    global $activeMenu;
    global $LINK;

    # check param
    if(!$entry) { return; }

    # depth 0 -> User-menu / Admin-menu / Tables ...
    if($depth == 0) {
        # session refresh: restore last active menu
        $style = 'style="display:none"';
        if (isset($activeMenu)) {
            #$style = 'style="display: none;"';
            if ($activeMenu == $entryKey) {
                $style = '';
            }
        }

        //TODO: change $entryKey from single number to sth like "menu{$entryKey}"
        echo '<ul id="mainmenu-' . $entryKey . '" ' . $style. ' class="mainmenu">';

        if ($entryKey !== 301 && !empty($LINK['menu_search'][$entryKey])) {
            # quick search
            echo '<li onmousedown="event.stopPropagation();" class="sidenav-quicksearch sticky-top"><div class="menu-side-header"><i class="lmbMenuHeaderImage lmb-icon lmb-fav" onclick="openMenu(301)"></i><input type="text" class="form-control form-control-sm" onkeyup="lmbFilterTablesTimer(event, this, \'mainmenu-' . $entryKey . '\');" placeholder="' . $lang[2507] . '"></div>
                </li>';
        }

        $data = array('depth0Key' => $entryKey);
        foreach ($entry as $childKey => $child) {
            recRender($childKey, $child, 1, $data);
        }
        echo '</ul>';
    }

    # depth 1 -> e.g. "Beispiel-CRM"-Header
    else if($depth == 1) {

        // skip empty childs
        if(!$entry['child'] AND !$entry['eval'] AND !$entry['link'] AND !$entry['extension']){return;}

        $combinedId = $data['depth0Key'] . '_' . $entryKey;

        $hasChildren = !empty($entry['child']);

        # get onclick for header and angle up/down icon
        if($hasChildren) {
            if ($entry['link']) {
                $onHeaderSymbolClick = "onclick=\"{$entry['onclick']}; {$entry['link']}\"";
                $onToggleAngleSymbolClick = "onclick=\"hideShow(event, '{$combinedId}'); {$entry['onclick']};\"";
            } else {
                $onHeaderSymbolClick = "onclick=\"hideShow(event, '{$combinedId}'); {$entry['onclick']}\"";
                $onToggleAngleSymbolClick = '';
            }
        }
        else {
            if (lmb_substr($entry['link'], 0, 4) == 'main') {
                $onHeaderSymbolClick = "onclick=\"{$entry['onclick']}; parent.main.location.href='{$entry['link']}'\"";
            } elseif ($entry['link']) {
                $onHeaderSymbolClick = "onclick=\"{$entry['onclick']}; {$entry['link']}\"";
            }
            else {
                $onHeaderSymbolClick = "onclick=\"hideShow(event, '{$combinedId}'); {$entry['onclick']}\"";
            }
            $onToggleAngleSymbolClick = '';
        }
        


        # get correct angle icon (up/down)
        if ($menu_setting['menu']["{$combinedId}"] && !$entry['extension']) {
            $eldispl = $hasChildren ? 'open' : '';
            $iconclass = 'lmb-angle-up';
        } else {
            $eldispl = '';
            $iconclass = 'lmb-angle-down';
        }

        # popupIcon
        $popupIcon = '';
        if ($hasChildren || $entry['extension']) {
            $popupIcon = "<i id=\"HS{$combinedId}\" class=\"lmb-icon {$iconclass} float-end\" $onToggleAngleSymbolClick></i>";
        }

        # title
        $title = (is_numeric($entry['name']) ? $lang[$entry['name']] : $entry['name']);

        $entryIcon = $entry['gicon'] ?? $entry['icon'];

        echo <<<HTML
            <li id="menu-side-header-{$combinedId}" class="clearfix $eldispl">
                <div class="menu-side-header" $onHeaderSymbolClick>
                    <i class="lmbMenuHeaderImage lmb-icon $entryIcon"></i>
                    <span class="hide-menu">$title</span>$popupIcon
                </div>
                <ul id="CONTENT_$combinedId">
        HTML;

        if ($entry['eval']) {
            // TODO still works?
            eval($entry['eval'] . ';');
        }
        else if ($entry['child']) {
            $data = array_merge($data, array(
                'depth1Id' => $entry['id']
            ));
            foreach ($entry['child'] as $childKey => $child) {
                recRender($childKey, $child, 2, $data);
            }
        }


        if($entry['extension']){
            echo "<div id=\"PH_{$combinedId}\" style=\"width:100%;\"></div>";
        }

        echo '</ul></li>';


    }        

    # depth 2 -> e.g. "Aufträge" / "Kunden" / all sub-tabgroups
    else if($depth == 2) {
        # get icon
        if ($entry['icon']) {
            $icon = "<i class=\"lmbMenuItemImage lmb-icon {$entry['icon']}\" ></i>";
        } else {
            $icon = '';
        }

        # get cursor and onclick
        $cursor = '';
        if (lmb_substr($entry['link'], 0, 4) == 'main') {
            $onclick = "onclick=\"{$entry['onclick']}; parent.main.location.href='{$entry['link']}'\"";
        } elseif ($entry['link']) {
            $onclick = "onclick=\"{$entry['onclick']}; {$entry['link']}\"";
        } else {
            $onclick = "onclick=\"hideShowSub(event,'{$data['depth0Key']}', '{$tabGroupIndex}')\"";
            $cursor = 'cursor:default;';
        }

        # bold text for subgroups
        $menuValue = (is_numeric($entry['name']) ? $lang[$entry['name']] : $entry['name']);
        $aClass = $entry['class'] ?? ' ';
        if($entry['header']) {
            $menuValue = "<b>{$menuValue}</b>";
            $aClass .= 'lmbMenuItemHeader';
        }



        # check if display none
        if ($menu_setting['submenu'][$data['depth0Key'] . "_" . $tabGroupIndex]) {
            $openClass = 'class="open"';
        } else {
            $openClass = '';
        }

        # get correct caret right/down
        if ($openClass) {
            $iconClass = ' lmb-caret-down ';
        } else {
            $iconClass = ' lmb-caret-right ';
        }



        $id = 'mel_'.$data['depth0Key'].'_'.$entry['id'];

        $childheader = '';
        if ($entry['child']) {
            $childheader = '<span onclick="hideShowSub(event,\''.$data['depth0Key'].'\',\''.$tabGroupIndex.'\')">
                            <i id="arrowSub'.$tabGroupIndex.'" class="lmb-icon '.$iconClass.'"></i>
                        </span>';
        }


        /*echo '<li>
                        '.$icon.'
                        <span style="overflow:hidden;background-color:'.$entry['bg'].'" title="'.$entry['desc'].'">
                            <a class="'.$aClass.' lmbMenuItemBodyNav" '.$onclick.'>
                                <span id="'.$id.'" '.$entry['attr'].' style="'.$entry['style'].'">'.$menuValue.'</div>
                            </a>
                        </span>
                        '.$childheader.'
                        
                        ';*/

        echo '<li '.$openClass.'><a id="'.$id.'" class="'.$aClass.'" style="'.$entry['style'].'" '.$entry['attr'].' '.$onclick.'>
                    '.$icon.'
                    '.$menuValue.'
                    '.$childheader.'
                </a>';



        echo '<ul id="subElt_'.$tabGroupIndex.'" style="overflow:hidden;">';

        
        # render next level
        $data = array_merge($data, array(
            'depth2Id' => $entry['id'],
            'eldispl' => $eldispl
        ));

        if($entry['child']) {
            foreach ($entry['child'] as $childKey => $child) {
                # has children -> render as depth 2 | or in menu 'table' -> render as depth 2 to ensure correct table layout
                if($child['child'] != null || $data['depth0Key'] == 20) {
                    $tabGroupIndex++;
                    recRender($childKey, $child, 2, $data);
                }
                # no children -> render as depth 3
                else {
                    recRender($childKey, $child, 3, $data);
                }
            }
        }

        echo '</ul></li>';

        $tabGroupIndex++;
    }
    else if($depth == 3) {
        # get correct onclick
        if (lmb_substr($entry['link'], 0, 4) == 'main') {
            $onclick = "onclick=\"{$entry['onclick']}; parent.main.location.href='{$entry['link']}'\"";
        } elseif ($entry['link']) {
            $onclick = "onclick=\"{$entry['onclick']}; {$entry['link']}\"";
        } else {
            $onclick = '';
        }

        # get icon
        if ($entry['icon']) {
            $icon = "<i class=\"lmb-icon {$entry['icon']}\" style=\"vertical-align:baseline\"></i>";
        } else {
            $icon = '';
        }

        $aClass = 'lmbMenuItemBodyNav ' . $entry['class'] ?? '';


        $id = 'mel_'.$data['depth0Key'].'_'.$entry['depth2Id'].'_'.$entry['id'];

        $textToDisplay = (is_numeric($entry['name']) ? $lang[$entry['name']] : $entry['name']);

        echo '<li id="subElti_'.$tabGroupIndex.'" style="overflow:hidden;background-color:'.$entry['bg'].'">
                            <a class="' . $aClass . '" id="'.$id.'" title="'.$entry['desc'].'" style="'.$entry['style'].'" '.$onclick.'>
                                '.$icon.'&nbsp;'.$textToDisplay.'
                            </a>
                        </li>';
    }
}
?>
