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


document.body.bgColor = "#ffffff";
document.body.background =  "";


/* --- Zoom Bewegen Maus ----------------------------------- */
var dx = 0, dy = 0;
var mv = "1.0";
var objList = new Array();
var current = null;
var zIndexTop = 0;

function startDrag(e) {
	var found = false;
	var i = objList.length;
	var obj = window.event.srcElement;
	current = obj.parentElement.style;
	dx = window.event.clientX - current.pixelLeft;
	// Setze Objekt nach oben
	zIndexTop++;
	current.zIndex = zIndexTop;
	document.onmousemove = drag;
	return false;
}

function drag(e) {
	if (current != null) {
		mv = window.event.clientX/500;
		current.pixelLeft = window.event.clientX - dx;
		parent.main.document.body.style.zoom = mv;
	}
	return false;
}

function endDrag(e) {
	document.onmousemove = null;
	current = null;
	return false;
}

function reset() {
	document.all["zoom"].style.left=500;
	parent.main.document.body.style.zoom=1.0 ;
	mv = "1.0";
}


var size_status = 1;
var size_value = 230;
/*
function frame_size(){
	if(size_status){
		parent.document.getElementById("mainset").cols = "163,*,0";
		document.resize.src='pic/maximize_s.gif';
		size_status = 0;
	}else{
		parent.document.getElementById("mainset").cols = "163,*,"+size_value;
		document.resize.src='pic/minimize_s.gif';
		size_status = 1;
	}
}
*/

function logout_refresh(result){
	top.document.location.href = 'index.php';
}

function logout(){
	cnf = confirm('<?=$lang[1251]?>');
	if(cnf){
		//top.document.location.href = 'index.php?logout=1';
		ajaxGet(null,'main_dyns.php','logout&logout=1','','logout_refresh');
	}
}

function srefresh(el=null) {
    // start rotating icon
    var icon = null;
    if (el) {
        icon = $(el).find('i');
        icon.addClass('lmb-rotating');
        icon.css('color', '#e4d314');
    }

    // get id of selected menu element to show after frame is loaded
    var activeMenu = '';
    const activeItem = $('.lmbMenuItemActiveTop2[onclick]');
    if (activeItem) {
        const ids = activeItem.attr('onclick').match(/openMenu\((.*?)\)/);
        if (ids && ids[1]) {
            activeMenu = '&activeMenu=' + ids[1];
        }
    }

    // reload frame
    parent.nav.document.location.href="main.php?action=nav&sparte=gtab&tab_group=1&refresh=no&sess_refresh=<?=$session["user_id"]?>" + activeMenu;

    // stop rotating icon when frame is loaded
    if (el) {
        parent.nav.frameElement.addEventListener('load', function () {
            icon.removeClass('lmb-rotating');
            icon.css('color', '');
        });
    }
}

function help(){
	Helpdesc = open("<?=$umgvar["helplink"]?>" ,"Helpdesc","toolbar=1,location=0,status=0,menubar=0,scrollbars=1,resizable=1,width=800,height=600");
}

function infos(){
	parent.main.location.href='main.php?&action=nav_info';
	//information = open("main.php?&action=nav_info" ,"Infos","toolbar=1,location=0,status=0,menubar=0,scrollbars=1,resizable=1,width=800,height=600");
}

function f_3(){

}

var activ1 = 0;
var activ2 = 0;

function l_over(el){
	<?php
	foreach($LINK["name"] as $key => $value){
		if($LINK["subgroup"][$key] == 2 AND $LINK["typ"][$key] == 1){
	        echo "document.getElementById('el_".$key."').style.color='';\n";
	        echo "document.getElementById('el_".$key."').style.borderBottom='1px solid #FFFFFF';\n";
	    }
	}
	?>
	el.style.color = 'black';
	el.style.borderBottom = '<?=$farbschema["WEB8"]?>';
}


function l_out(el){
	if(activ1 != el){
		el.style.color='';
		el.style.borderBottom='1px solid #FFFFFF';
		if(activ1){
			document.getElementById(activ1).style.color='black';
			document.getElementById(activ1).style.borderBottom='<?=$farbschema["WEB8"]?>';
		}
	}
}

function act(el){
	var sel = 'el_'+el;
	var menel = el;
	<?php
	foreach($LINK["name"] as $key => $value){
		#if($LINK["typ"][$key] == 1 OR $LINK["typ"][$key] == 5){
		#	echo "if(parent.nav.document.getElementById('menu_".$key."')){\n";
		#	echo "parent.nav.document.getElementById('menu_".$key."').style.display='none';\n";
		#	echo "}\n";
		#}

		if($LINK["subgroup"][$key] == 3 AND ($LINK["typ"][$key] == 1 OR $LINK["typ"][$key] == 5)){
			echo "document.getElementById('el_".$key."').style.color='{$farbschema['WEB12']}';\n";
			echo "activ1 = 0;\n";
		}
	}
	?>

	act2(el);

	if(parent.nav.document.getElementById(menel)){
	parent.nav.document.getElementById(menel).style.display='';
	}
	activ1 = sel;
	activ2 = 0;
}


function l2_over(el){
	<?php
	foreach($LINK["name"] as $key => $value){
		if($LINK["subgroup"][$key] == 2 AND $LINK["typ"][$key] == 1){
	        echo "document.getElementById('el_".$key."').style.color='" . $farbschema["WEB12"] . "';\n";
	    }
	}
	?>
	el.style.color = '<?=$farbschema["WEB8"]?>';
}

function l2_out(el){
	if(activ2 != el){
		el.style.color='<?=$farbschema["WEB12"]?>';
		if(activ2){
			document.getElementById(activ2).style.color='<?=$farbschema["WEB8"]?>';
		}
	}
}

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

function openMenu(id){
	parent.nav.mainMenu(id);
}

</SCRIPT>





<TABLE cellpadding="0" cellspacing="0" border="0" class="lmbfringeMenuTop2"><TR>

<!-- TOP of the cells -->
<?php

echo '<td class="lmbMenuItemInactiveTop2" style="padding: 0 5px;" title="' . $lang[$LINK['name'][301]] . '" onclick="openMenu(301); limbasSetLayoutClassTabs(this,\'lmbMenuItemInactiveTop2\',\'lmbMenuItemActiveTop2\')">';
$topLeft = 'pic/logo_topleft.png';
if(file_exists("EXTENSIONS/customization/logo_topleft.png")){
    $topLeft = 'EXTENSIONS/customization/logo_topleft.png';
}
echo '<img alt="" style="max-height: 47px;" src="' . $topLeft . '">';
echo '</td>';

$bzm = 1;
if($menu["main"]){	
    foreach ($menu["main"] as $key => $value) {
        # favorites
        if ($key === 301) {
            continue;
        }
        if($value["link"]){$onclick = $value["link"];}else{$onclick = "openMenu($key)";}
        if($bzm == 1){$class = "class=\"lmbMenuItemActiveTop2\"";}else{$class = "class=\"lmbMenuItemInactiveTop2\"";}
        echo "<TD nowrap $class onclick=\"$onclick;limbasSetLayoutClassTabs(this,'lmbMenuItemInactiveTop2','lmbMenuItemActiveTop2')\" title=\"" . $value["desc"] . "\"><a class=\"lmbMenuItemTop2\">".$value["name"]."</a></TD>";
        $bzm++;
    }
}

echo "<TD style=\"width:100%\" class=\"lmbMenuItemspaceTop2\"><div>&nbsp;</div></TD>";


//TODO: Wiedervorlage / Mail etc Icons zwischen abmelden und reset


$bzm = 1;
if($menu["info"]){
    foreach ($menu["info"] as $key => $value) {
        if($value["link"]){
            $onclick = $value["link"];
        }else{
            $onclick = "openMenu($key)";
        }
        if($key != 214) { # not session refresh
            $onclick .= ";limbasSetLayoutClassTabs(this,'lmbMenuItemInactiveTop2','lmbMenuItemActiveTop2')";
        }

        # automatically check for updates
        $class = '';
        if ($key == 207 /* info menu */ AND $latestVersion = lmbCheckForUpdates() AND is_string($latestVersion)) {
            $class = 'lmbUpdateAvailable';
            $value['desc'] .= "\n" . sprintf($lang[2927], $latestVersion);
        }
        echo "<TD class=\"lmbMenuItemInactiveTop2\" nowrap onclick=\"$onclick\" title=\"" . $value["desc"] . "\"><a class=\"lmbMenuItemTop2 $class\">".(($value['icon']) ? '<div class="lmbMenuItemTop2Icon"><i class="lmb-icon '.$value['icon'].'"></i></div><div class="lmbMenuItemTop2Text">'.$value["name"].'</div>' : $value["name"])."</a></TD>";
        $bzm++;
    }
}

?>
</TR>


</TABLE>