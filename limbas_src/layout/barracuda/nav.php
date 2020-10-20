<?php
/*
 * Copyright notice
 * (c) 1998-2019 Limbas GmbH(support@limbas.org)
 * All rights reserved
 * This script is part of the LIMBAS project. The LIMBAS project is free software; you can redistribute it and/or modify it on 2 Ways:
 * Under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * Or
 * In a Propritary Software Licence http://limbas.org
 * The GNU General Public License can be found at http://www.gnu.org/copyleft/gpl.html.
 * A copy is found in the textfile GPL.txt and important notices to the license from the author is found in LICENSE.txt distributed with these scripts.
 * This script is distributed WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * This copyright notice MUST APPEAR in all copies of the script!
 * Version 3.6
 */

/*
 * ID: 10
 */
?>


<?php
$multfrdispl = "";
$hiddendispl = "display:none";

$menu_setting = lmbGetMenuSetting();
if($menu_setting["frame"]["nav"] AND $menu_setting["frame"]["nav"] <= 30){
	$multfrdispl = "display:none";
	$hiddendispl = "";
}
?>

<div id="hiddensidenav" style="height:90%;cursor:pointer;<?=$hiddendispl?>" data-showframe="sidenav">
    <div class="lmbFrameShow">
        <i class="lmb-icon lmb-icon-aw lmb-caret-right"></i>
    </div>
</div>

<div id="sidenav" style="<?=$multfrdispl?>">

<div id="lmbAjaxContainer" class="ajax_container" style="position:absolute;visibility:hidden;" OnClick="activ_menu=1;"></div>

<div id="framecontext" class="evt-hide-frame-con">
    <div class="lmbfringeFrameNav clearfix" onmousedown="lmbIniDrag(event,this,'lmbResizeFrame')">
        <div class="evt-hide-frame text-center d-lg-none p-3"><i class="lmb-icon lmb-erase"></i></div>
<?php

function recRender($entryKey, $entry, $depth = 0, $data = null) {
    global $menu_setting;
    global $defaultExpandMenu;
    global $tabGroupIndex;
    global $lang;
    global $dwme;
    global $farbschema;
    global $activeMenu;

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
        echo "<ul id='{$entryKey}' {$style} class=\"mainmenu\">";

        if ($entryKey !== 301) {
            # quick search
            echo '<li onmousedown="event.stopPropagation();" class="sidenav-quicksearch"><div class="menu-side-header"><i class="lmbMenuHeaderImage lmb-icon-32 lmb-fav" onclick="openMenu(301)"></i><input type="text" class="form-control form-control-sm" onkeyup="lmbFilterTablesTimer(event, this, ' . $entryKey . ');" placeholder="' . $lang[2507] . '"></div>
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
        $combinedId = $data['depth0Key'] . '_' . $entryKey;

        # get onclick for header and angle up/down icon
        if ($entry['link']) {                
            $onHeaderSymbolClick = "onclick=\"{$entry['onclick']}; {$entry['link']}\"";
            $onToggleAngleSymbolClick = "onclick=\"hideShow(event, '{$combinedId}'); {$entry['onclick']};\"";
        } else {
            $onHeaderSymbolClick = "onclick=\"hideShow(event, '{$combinedId}'); {$entry['onclick']}\"";
            $onToggleAngleSymbolClick = '';
        }


        # get correct angle icon (up/down)
        if ($menu_setting['menu']["{$combinedId}"] && !$entry['extension']) {
            $eldispl = 'open';
            $iconclass = 'lmb-angle-up';
        } else {
            $eldispl = '';
            $iconclass = 'lmb-angle-down';
        }

        # popupIcon
        $popupIcon = '';
        if (($entry['child'] AND count($entry['child']) > 0) || $entry['extension']) {
            $popupIcon = "<i id=\"HS{$combinedId}\" class=\"lmb-icon {$iconclass} float-right\" $onToggleAngleSymbolClick></i>";
        }

        # title
        $title = (is_numeric($entry['name']) ? $lang[$entry['name']] : $entry['name']);


        echo '<li '.$onHeaderSymbolClick.' class="clearfix '.$eldispl.'">
                <div class="menu-side-header"><i class="lmbMenuHeaderImage lmb-icon-32 '.($entry['gicon'] ?? $entry['icon']).'"></i>
            <span class="hide-menu">'.$title.'</span>
            '.$popupIcon.'</div>
           <ul id="CONTENT_'.$combinedId.'">';

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
        $aClass = '';
        if($entry['header']) {
            $menuValue = "<b>{$menuValue}</b>";
            $aClass = 'lmbMenuItemHeader';
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


        $id = 'mel_'.$data['depth0Key'].'_'.$entry['depth2Id'].'_'.$entry['id'];

        $textToDisplay = (is_numeric($entry['name']) ? $lang[$entry['name']] : $entry['name']);

        echo '<li id="subElti_'.$tabGroupIndex.'" style="overflow:hidden;background-color:'.$entry['bg'].'">
                            <span class="contentSub" title="'.$entry['desc'].'">
                                <span id="'.$id.'" style="'.$entry['style'].'">
                                    <a class="lmbMenuItemBodyNav" '.$onclick.'>
                                        '.$icon.'&nbsp;'.$textToDisplay.'
                                    </a>
                                </span>
                            </span
                        </li>';
    }
}

$defaultExpandMenu = "none";

# index for #arrowSub{$tabGroupIndex} and #subElt_{$tabGroupIndex}
$tabGroupIndex = 0;

# width
if(!$dwme = $menu_setting["frame"]["nav"]){
	$dwme = 180;
}

# render all menus
foreach($menu as $key => $val) {
    recRender($key, $val);
}

?>

<div class="evt-hide-frame"><i class="lmb-icon lmb-icon-8 lmb-caret-left"></i></div>

</div>
</div>

<?php

if (isset($activeMenu)) {
    # show last opened menu in nav frame
    $displayMainMenu = "mainMenu(" . $activeMenu . ")";
} else {
    # show first menu in nav frame
    foreach ($LINK["name"] as $key => $value) {
        if ($LINK["subgroup"][$key] == 2 AND $LINK["typ"][$key] == 1) {
            $displayMainMenu = "mainMenu(" . $key . ")";
            break;
        }
    }
}

echo "<script language='javascript'>" . $displayMainMenu . "</script>";

?>
</div>