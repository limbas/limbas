<?php
/*
 * Copyright notice
 * (c) 1998-2021 Limbas GmbH(support@limbas.org)
 * All rights reserved
 * This script is part of the LIMBAS project. The LIMBAS project is free software; you can redistribute it and/or modify it on 2 Ways:
 * Under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * Or
 * In a Propritary Software Licence http://limbas.org
 * The GNU General Public License can be found at http://www.gnu.org/copyleft/gpl.html.
 * A copy is found in the textfile GPL.txt and important notices to the license from the author is found in LICENSE.txt distributed with these scripts.
 * This script is distributed WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * This copyright notice MUST APPEAR in all copies of the script!
 * Version 4.3.36.1319
 */

/*
 * ID: 16
 */


foreach($LINK["name"] as $key => $value){
	if($LINK["subgroup"][$key] == 2 AND $LINK["typ"][$key] == 1){
		$menuItem["icon"] = $LINK["icon_url"][$key];
		$menuItem["desc"] = $lang[$LINK["desc"][$key]];
		$menuItem["name"] = $lang[$LINK["name"][$key]];
		$menuItem["link"] = $LINK["link_url"][$key];
		$menu["main"][$key] = $menuItem;
	}
}

foreach($LINK["name"] as $key => $value){
	if($LINK["subgroup"][$key] == 3 AND $LINK["typ"][$key] == 1 AND $key != 18 AND $key != 246){
		if($LINK["typ"][$key] == 1 OR $LINK["typ"][$key] == 5){
			$act ="act2('".$key."');";
		}else{
			$act = "";
		}

		$menuItem["icon"] = $LINK["icon_url"][$key];
		$menuItem["desc"] = $lang[$LINK["desc"][$key]];
		$menuItem["name"] = $lang[$LINK["name"][$key]];
		$menuItem["link"] = $LINK["link_url"][$key];
		$menu["info"][$key] = $menuItem;
	}
}



?>

<SCRIPT type="text/javascript">
    //TODO: javascript komplett auslagern




    function act2(el){
        var sel = 'el_'+el;
        var menel = ""+el;
        var toDisplay;
        alert(menel);
        zusatzMenu = parent.nav.zusatzMenu;

        toHide = parent.nav.document.getElementsByTagName("TABLE");
        for(i=0;i<toHide.length;i++){
            toHide[i].style.top = "-5000px";
        }

        for(i=0;i<zusatzMenu.length;i++){
            divEl = parent.nav.document.getElementById(zusatzMenu[i]);
            if(divEl){
                divEl.style.top = "-5000px";
            }
        }

        <?php //firefox without getElementByName
        if(lmb_strpos($_SERVER["HTTP_USER_AGENT"],"MSIE")<1){?>
        toDisplay = parent.nav.document.getElementsByName(menel)[0];

        <?php }else{?>
        toDisplayList = parent.nav.document.getElementsByTagName("TABLE");
        toDisplay = null;
        for(i=0;i<toDisplayList.length;i++){
            if(toDisplayList[i].name==menel){
                toDisplay = toDisplayList[i];
                break;
            }
        }

        if(!toDisplay){
            for(i=0;i<zusatzMenu.length;i++){
                divEl = parent.nav.document.getElementById(zusatzMenu[i]);
                if(divEl){
                    if(divEl.name==menel){
                        toDisplay = divEl;

                    }
                }
            }
        }
        <?php }?>


        if(toDisplay){
            toDisplay.style.top=  "0px";
        }
        activ2 = sel;
        activ1 = 0;
    }
</script>


<?php

if($umgvar["multitenant"] AND count($lmmultitenants['mid']) > 1){

    $selectmultitenant = '<a class="dropdown-item disabled border-bottom" data-mid="'.$session["mid"].'" data-mname="'.$lmmultitenants['name'][$session["mid"]].'">'.$lmmultitenants['name'][$session["mid"]].'</a>';

    foreach ($lmmultitenants['mid'] as $key => $value) {
        if ($key != $session["mid"]) {
            $selectmultitenant .= '<a class="dropdown-item" data-mid="'.$key.'" data-mname="'.$lmmultitenants['name'][$key].'">'.$lmmultitenants['name'][$key].'</a>';
        }

    }

    $show_multitenant = 1;

    //<li class="nav-item pt-lg-2 mr-lg-2"><a class="nav-link active-tenant">'.$lmmultitenants['name'][$session["mid"]].'</a></li>
    $multitenantMenuWrapper = '
<li class="nav-item dropdown pl-lg-2">
                <a class="nav-link "><div class="lmbMenuItemTop2Icon"><i class="lmb-icon lmb-user-circle-o"></i></div><div class="lmbMenuItemTop2Text">%s</div></a>
                <div class="dropdown-menu rounded-0 border-light py-0 mt-0">
                    '.$selectmultitenant.'
                </div>
            </li>';
}



$profileMenuWrapper = '<li class="nav-item dropdown pl-lg-2 ml-lg-2 border-left">
                <a class="nav-link lmbMenuProfileDropdown" data-toggle="dropdown">';

if ($umgvar["multitenant"]) {
    $profileMenuWrapper .= '<div class="row">
                        <div class="col-9"><span class="lmbMenuItemTop2Text align-middle text-left">'.$session["vorname"].' '.$session["name"].'<br><span class="active-tenant font-weight-bold">'.$lmmultitenants['name'][$session["mid"]].'</span></div>
                        <div class="col-3 text-right p-0"><img class="rounded-circle" src="EXTENSIONS/customization/logo_topleft.png"></div>
                    </div>';
} else {
    $profileMenuWrapper .= '<span class="lmbMenuItemTop2Text align-middle">'.$session["vorname"].' '.$session["name"].'</span> <img class="rounded-circle" src="EXTENSIONS/customization/logo_topleft.png">';
}

$profileMenuWrapper .= '</a><div class="dropdown-menu rounded-0 border-light py-0 mt-0">%s</div></li>';

?>


<nav class="navbar navbar-expand-xl navbar-light py-0 shadow-sm bg-nav" id="lmbtopnav">
    <!-- TODO: Favorites onclick="openMenu(301); limbasSetActiveClass(this,'#lmbtopnav','li')" -->
    <a class="navbar-brand pr-4 pr-sm-5" href=""  title="<?php echo $lang[$LINK['name'][301]]; ?>">
        <?php
        $topLeft = 'pic/logo_topleft.png';
        if(file_exists("EXTENSIONS/customization/logo_topleft.png")){
            $topLeft = 'EXTENSIONS/customization/logo_topleft.png';
        }
        echo '<img src="' . $topLeft . '" class="d-inline-block align-middle top-logo-img" alt="" style="max-height: 40px;" >';
        ?>
        <?php echo ($umgvar["company"] && $umgvar["company"] != 'Limbas') ? '<span class="top-logo-company">'.$umgvar["company"].'</span>' : '<span class="top-logo-limbas">L<span style="color:orange">I</span>MBAS</span>';?>
    </a>


    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#lmbtopnavleft" aria-controls="lmbtopnavleft" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="lmbtopnavleft">
        <ul class="navbar-nav mr-auto">
            <?php
            $bzm = 1;
            if($menu["main"]){
                foreach ($menu["main"] as $key => $value) {

                    # favorites
                    if ($key === 301) {
                        continue;
                    }
                    if($value["link"]){$onclick = $value["link"];}else{$onclick = "openMenu($key)";}

                    if($bzm == 1){$active = 'active';}else{$active = '';}
                    echo "<li class=\"nav-item $active\" data-id=\"$key\" onclick=\"$onclick;limbasSetActiveClass(this,'#lmbtopnav','li')\" title=\"" . $value["desc"] . "\"><a class=\"nav-link\">".$value["name"]."</a></li>";
                    $bzm++;
                }
            }
            ?>
        </ul>
        <ul class="navbar-nav ml-auto">
            <?php if($umgvar["admin_mode"] AND $LINK[17] AND $session["group_id"] == 1){
                echo '<li class="nav-item pt-lg-2 mr-lg-2"><a href="main_admin.php?action=setup_umgvar#admin-mode" target="main" class="nav-link text-danger border border-danger pl-2 small">admin mode on!</a></li>';
            }?>

            <?php
            $bzm = 1;
            $profilemenu = '';
            if($menu["info"]){
                foreach ($menu["info"] as $key => $value) {

        // hide multitenant if not needed
        if($key == 308 AND !$show_multitenant) {continue;}

                    if($value["link"]){
                        $onclick = $value["link"];
                    }else{
                        $onclick = "openMenu($key)";
                    }
                    if($key != 214 && $key != 21) { # not session refresh
                        $onclick .= ";limbasSetActiveClass(this,'#lmbtopnav','li')";
                    }

                    # automatically check for updates
                    $class = '';
                    if ($key == 207 /* info menu */ AND $latestVersion = lmbCheckForUpdates() AND is_string($latestVersion)) {
                        $class = 'lmbUpdateAvailable';
                        $value['desc'] .= "\n" . sprintf($lang[2927], $latestVersion);
                    }

                    if ($key == 21) {
                        $profilemenu .= '<a class="dropdown-item" onclick="'.$onclick.'" title="'.$value['desc'].'">
                                            '.(($value['icon']) ? '<i class="lmb-icon '.$value['icon'].'"></i>':'').' '.$value["name"].'
                                        </a>';
                    } else if ($key == 213) {
                        $profilemenu .= '<div class="dropdown-divider m-0"></div>
                                            <a class="dropdown-item" href="#" data-toggle="modal" data-target="#modal-logout">
                                                <i class="lmb-icon lmb-sign-out"></i> '.$value["name"].'
                                            </a>';

                    } else if ($key == 308) {
                        echo sprintf($multitenantMenuWrapper,$value["name"]);
                    } else {
                        echo "<li class=\"nav-item pl-lg-2\" onclick=\"$onclick\" title=\"" . $value["desc"] . "\"><a class=\"nav-link $class\">".(($value['icon']) ? '<div class="lmbMenuItemTop2Icon"><i class="lmb-icon '.$value['icon'].'"></i></div><div class="lmbMenuItemTop2Text">'.$value["name"].'</div>' : $value["name"])."</a></li>";
                    }

                    $bzm++;
                }

                echo sprintf($profileMenuWrapper,$profilemenu);
            }
            ?>

        </ul>
    </div>

</nav>

<div class="modal fade" id="modal-logout" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?=$lang[1251]?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                Wählen Sie "Abmelden" um Ihre aktuelle Session zu beenden.<br>Tipp: Es reicht ebenfalls aus, den gesamten Browser zu schließen.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Abbrechen</button>
                <button type="button" class="btn btn-primary" onclick="logout()">Abmelden</button>
            </div>
        </div>
    </div>
</div>