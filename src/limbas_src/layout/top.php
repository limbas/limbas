<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
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

if($umgvar["multitenant"] AND lmb_count($lmmultitenants['mid']) > 1){

    $selectmultitenant = '<a class="dropdown-item disabled border-bottom" data-mid="'.$session["mid"].'" data-mname="'.$lmmultitenants['name'][$session["mid"]].'">'.$lmmultitenants['name'][$session["mid"]].'</a>';

    foreach ($lmmultitenants['mid'] as $key => $value) {
        if ($key != $session["mid"]) {
            $selectmultitenant .= '<a class="dropdown-item cursor-pointer" data-mid="'.$key.'" data-mname="'.$lmmultitenants['name'][$key].'">'.$lmmultitenants['name'][$key].'</a>';
        }

    }

    $show_multitenant = 1;

    //<li class="nav-item pt-lg-2 me-lg-2"><a class="nav-link active-tenant">'.$lmmultitenants['name'][$session["mid"]].'</a></li>
    $multitenantMenuWrapper = '
<li class="nav-item dropdown ps-lg-2" data-lmb-dptoggle="hover">
                <a class="nav-link "><div class="lmbMenuItemTop2Icon"><i class="lmb-icon lmb-user-circle-o"></i></div><div class="lmbMenuItemTop2Text">%s</div></a>
                <div class="dropdown-menu rounded-0 py-0 mt-0">
                    '.$selectmultitenant.'
                </div>
            </li>';
}



$profileMenuWrapper = '<li class="nav-item dropdown ps-lg-2 ms-lg-2 border-left" data-lmb-dptoggle="hover">
                <a class="nav-link lmbMenuProfileDropdown" data-bs-toggle="dropdown">';

if ($umgvar["multitenant"]) {
    $profileMenuWrapper .= '<div class="row">
                        <div class="col-12"><span class="lmbMenuItemTop2Text align-middle text-start">'.$session["vorname"].' '.$session["name"].'<br><span class="active-tenant fw-bold">'.$lmmultitenants['name'][$session["mid"]].'</span></div>';
                        //TODO: Profile Pic <div class="col-3 text-end p-0"><img class="rounded-circle" src=""></div>
    $profileMenuWrapper .= '</div>';
} else {
    $profileMenuWrapper .= '<span class="lmbMenuItemTop2Text align-middle">'.$session["vorname"].' '.$session["name"].'</span>';
    //TODO: Profile Pic '<img class="rounded-circle" src="">';
}

$profileMenuWrapper .= '</a><div class="dropdown-menu rounded-0 border-light py-0 mt-0">%s</div></li>';

?>


<nav class="navbar navbar-expand-lg navbar-light py-0 shadow-sm bg-nav" id="lmbtopnav">

    <div class="container-fluid">
        <a class="navbar-brand pe-4 pe-sm-5" href=""  title="<?php echo $lang[$LINK['name'][301]]; ?>">
            <?php
                $topLeft = 'assets/images/logo_topleft.png';
                $customLogo = false;
                if(file_exists(LOCALASSETSPATH . 'images/logo_topleft.png')){
                    $topLeft = 'localassets/images/logo_topleft.png';
                    $customLogo = true;
                }
            ?>
            <img src="<?=$topLeft?>" class="d-inline-block align-middle top-logo-img" alt="" style="max-height: 40px;" title="<?=$umgvar['company']?>">
            <?php if (!$customLogo): ?>
                <span class="top-logo-limbas" title="<?=$umgvar['company']?>">L<span style="color:orange">I</span>MBAS</span>
            <?php endif; ?>
        </a>
    
    
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#lmbtopnavleft" aria-controls="lmbtopnavleft" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
    
        <div class="collapse navbar-collapse text-nowrap" id="lmbtopnavleft">
            <ul class="navbar-nav me-auto" id="lmb-main-nav">
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
                        
                        echo "<li class=\"nav-item $active\" data-id=\"$key\" onclick=\"$onclick;limbasSetActiveClass(this,'#lmbtopnav','li')\" title=\"" . $value["desc"] . "\"><a class=\"nav-link\">".(($value['icon']) ? '<div class="lmbMenuItemTop2Icon"><i class="lmb-icon '.$value['icon'].'"></i></div><div class="lmbMenuItemTop2Text">'.$value["name"].'</div>' : $value["name"])."</a></li>";
                        
                        $bzm++;
                    }
                }
                ?>
                <li class="nav-item dropdown d-none" data-lmb-dptoggle="hover">
                    <a class="nav-link" href="#" id="main-nav-dropdown-toggle" role="button" data-bs-toggle="dropdown" data-lmb-dptoggle="hover" aria-haspopup="true" aria-expanded="false">
                        <span class="navbar-toggler-icon"></span>
                    </a>
                    <ul class="dropdown-menu mt-0" aria-labelledby="main-nav-dropdown-toggle" id="main-nav-dropdown">
                    </ul>
                </li>
            </ul>
            <ul class="navbar-nav ms-auto" id="lmb-navbar-top-right">
                <?php if($umgvar["admin_mode"] AND $LINK[17] AND $session["group_id"] == 1){
                    echo '<li class="nav-item pt-lg-2 me-lg-2"><a href="main_admin.php?action=setup_umgvar#admin-mode" target="main" class="nav-link text-danger border border-danger ps-2 small">admin mode on!</a></li>';
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
    
                        if ($key == 21) {
                            $profilemenu .= '<a class="dropdown-item" onclick="'.$onclick.'" title="'.$value['desc'].'">
                                                '.(($value['icon']) ? '<i class="lmb-icon '.$value['icon'].'"></i>':'').' '.$value["name"].'
                                            </a>';
                        } else if ($key == 213) {
                            $profilemenu .= '<div class="dropdown-divider m-0"></div>
                                                <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#modal-logout">
                                                    <i class="lmb-icon lmb-sign-out"></i> '.$value["name"].'
                                                </a>';
    
                        } else if ($key == 308) {
                            echo sprintf($multitenantMenuWrapper,$value["name"]);
                        } else {
                            echo "<li class=\"nav-item ps-lg-2\" onclick=\"$onclick\" title=\"" . $value["desc"] . "\"><a class=\"nav-link $class\">".(($value['icon']) ? '<div class="lmbMenuItemTop2Icon"><i class="lmb-icon '.$value['icon'].'"></i></div><div class="lmbMenuItemTop2Text">'.$value["name"].'</div>' : $value["name"])."</a></li>";
                        }
    
                        $bzm++;
                    }
    
                    echo sprintf($profileMenuWrapper,$profilemenu);
                }
                ?>
    
            </ul>
        </div>

    </div>
</nav>

<div class="modal fade" id="modal-logout" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?=$lang[1251]?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Wählen Sie "Abmelden" um Ihre aktuelle Session zu beenden.<br>Tipp: Es reicht ebenfalls aus, den gesamten Browser zu schließen.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Abbrechen</button>
                <form method="post">
                    <input type="hidden" name="actid" value="logout">
                    <input type="hidden" name="logout" value="1">
                    <button type="submit" class="btn btn-primary">Abmelden</button>
                </form>
            </div>
        </div>
    </div>
</div>
