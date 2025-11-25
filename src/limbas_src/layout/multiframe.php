<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */



$multfrdispl = "";
$hiddendispl = "display:none";

$menu_setting = lmbGetMenuSetting();

?>
<SCRIPT LANGUAGE="JavaScript">

    <?php
    if(!$rightMenuOpen){
        $multfrdispl = "display:none";
        $hiddendispl = "";
    ?>
	noMoreRefresh = 1;
    <?php } ?>

    var multiframe_refresh = '<?=$umgvar['multiframe_refresh']?>'

</SCRIPT>


<div id="hiddenmultiframe" style="height:90%;cursor:pointer;<?=$hiddendispl?>" data-hideshow-sidebars="multi">
    <div class="lmbFrameShow">
        <i class="lmb-icon lmb-icon-aw lmb-caret-left"></i>
    </div>
</div>

<div id="multiframe" style="<?=$multfrdispl?>">

<div OnClick="activ_menu=1" id="frmlist" style="width:90%;background-color:<?=$farbschema["WEB7"]?>;border:1px solid <?=$farbschema["WEB12"]?>;font-size:12px;visibility:hidden;position:absolute;right:0px;top:3px;padding:2px;z-index:100;">
<?php

if($groupdat["multiframelist"][$session["group_id"]]){
	foreach ($groupdat["multiframelist"][$session["group_id"]] as $key => $value){
		if($value == $session['multiframe']){
			echo "&nbsp;<span style=\"color:green;\">".lmb_substr($value,0,lmb_strlen($value)-4)."</span><BR>";
		}else{
			echo "&nbsp;<span style=\"color:".$farbschema["WEB12"].";\" OnClick=\"change_frame('".rawurlencode($value)."')\" style=\"cursor:pointer;\" onmouseout=\"this.style.fontWeight='normal';\" onmouseover=\"this.style.fontWeight='bold';\">".lmb_substr($value,0,lmb_strlen($value)-4)."</span><BR>";
		}
	}
}

?>
</div>


<div id="lmbAjaxContainer" class="ajax_container" style="position:absolute;display: none" OnClick="activ_menu=1;"></div>
    <div id="framecontext" class="evt-hide-frame-con" data-hideshow-sidebars-right="multi">
        <div class="lmbfringeFrameNav multiframe clearfix">
            <div class="evt-hide-frame text-center d-lg-none p-3 multiframe"  data-hideshow-sidebars="multi"><i class="lmb-icon lmb-erase"></i></div>
<?php


$defaultExpandMenu = "none";

$key3 = 0;
foreach($menu as $key1 => $menuType){

	echo "<ul style=\"$name\">";

	$name = "display:none;";
	

	if($key1 == 110){
		$menuTmp["name"] = "Forms";
		$menuTmp["child"] = $menuType;
		unset($menuType);
		$menuType[] = $menuTmp;
	}elseif($key1 == 21){
		$menuTmp["name"] = "Profile";
		$menuTmp["child"] = $menuType;
		unset($menuType);
		$menuType[] = $menuTmp;
	}

	foreach ($menuType as $key2 => $firstLevel) {

		if($firstLevel["preview"]){
			echo "<input type=\"hidden\" id=\"multiframeAutorefresh_".$firstLevel["id"]."\" VALUE=\"".$firstLevel["autorefresh"]."\">";
			echo "<input type=\"hidden\" id=\"multiframeType_".$firstLevel["preview"]."\" value=\"".$firstLevel["id"]."\">";
			$preview = ",'" . $firstLevel["preview"] . "'";
		}else{
			$preview = "";
		}


		if($firstLevel["link"]){
			if($firstLevel["target"]){
				$onClick = "onClick = \"parent." . $firstLevel["target"] . ".location.href = '" . $firstLevel["link"] ."'\"";
			}else{
				if(lmb_substr($firstLevel["link"],0,11)=="javascript:"){
					$onClick = "onClick=" . $firstLevel["link"];
				}else{
					$onClick = "onClick=\"parent.main.location.href = '" . $firstLevel["link"] . "'\";";
				}
			}

			$cursor = "cursor:pointer;";
		}else{
			$cursor = "";
		}

        echo '<li><div class="menu-side-header" '.$onClick.'>
                <i class="lmbMenuHeaderImage lmb-icon '.($firstLevel['gicon'] ?? $firstLevel['icon']).'"></i>
            <span class="hide-menu">'.$firstLevel["name"].'</span>
            ';

		// default event is "hideShow"
		$event = "hideShowMulti";
		if($firstLevel["event"] != null){
			$event = $firstLevel["event"];
		}

        $onclick = "$event(event,'".$firstLevel["id"]."','".$firstLevel["name"]."','".$firstLevel["gtabid"]."','".$firstLevel["params"]."'$preview,1);var event = arguments[0] || window.event; event.stopPropagation();";

		if($firstLevel["child"] || $firstLevel["preview"] || $firstLevel["event"]){
            echo '<i id="HS'.$firstLevel["id"].'" onclick="'.$onclick.'" class="lmb-icon lmb-angle-down  float-end" valign="top" style="cursor:pointer"></i>';
		}

        echo '<span id="multiframeCount_'.$firstLevel["id"].'" class="badge bg-warning rounded-2 d-none float-end mt-1"></span>';

		$autoopen='';
        if($menu_setting["menu"][$firstLevel["name"]]){
            $autoopen = "data-autoopen=\"$event(null,'".$firstLevel["id"]."','".$firstLevel["name"]."','".$firstLevel["gtabid"]."','".$firstLevel["params"]."'$preview,'0');\"";
        }

		echo "</div>
		<ul id=\"CONTENT_".$firstLevel["id"]."\" $autoopen>
		    <div class=\"lmbMenuBodyNav\" id=\"limbasDivMultiframePreview" . $firstLevel["id"] . "\" style=\"width:100%;\"></div>
		</ul>
		

		</li>";


		$key3++;
		
		
	}

	echo  "</ul>";
}

?>


<div class="evt-hide-frame multiframe" data-hideshow-sidebars="multi"><i class="lmb-icon lmb-icon-8 lmb-caret-right"></i></div>

        </div>
    </div>

</div>
