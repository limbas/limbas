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
 * ID:
 */

$multfrdispl = "";
$hiddendispl = "display:none";

$menu_setting = lmbGetMenuSetting();
if($menu_setting["frame"]["multiframe"] AND $menu_setting["frame"]["multiframe"] <= 30){
	$multfrdispl = "display:none";
	$hiddendispl = "";
	?>
	<SCRIPT LANGUAGE="JavaScript">
	noMoreRefresh = 1;
	</SCRIPT>
	<?php
}
?>

        <div id="hiddenmultiframe" style="height:90%;cursor:pointer;<?=$hiddendispl?>" data-showframe="multiframe">
            <div class="lmbFrameShow">
                <i class="lmb-icon lmb-icon-aw lmb-caret-left"></i>
            </div>
        </div>

<div id="multiframe" style="<?=$multfrdispl?>">

<div OnClick="activ_menu=1" id="frmlist" style="width:90%;background-color:<?=$farbschema["WEB7"]?>;border:1px solid <?=$farbschema["WEB12"]?>;font-size:12px;visibility:hidden;position:absolute;right:0px;top:3px;padding:2px;z-index:100;">
<?php

if($groupdat["multiframelist"][$session["group_id"]]){
	foreach ($groupdat["multiframelist"][$session["group_id"]] as $key => $value){
		if($value == $session["multiframe"]){
			echo "&nbsp;<span style=\"color:green;\">".lmb_substr($value,0,lmb_strlen($value)-4)."</span><BR>";
		}else{
			echo "&nbsp;<span style=\"color:".$farbschema["WEB12"].";\" OnClick=\"change_frame('".rawurlencode($value)."')\" style=\"cursor:pointer;\" onmouseout=\"this.style.fontWeight='normal';\" onmouseover=\"this.style.fontWeight='bold';\">".lmb_substr($value,0,lmb_strlen($value)-4)."</span><BR>";
		}
	}
}

?>
</div>


<div id="lmbAjaxContainer" class="ajax_container" style="position:absolute;visibility:hidden;" OnClick="activ_menu=1;"></div>

    <div id="framecontext" class="evt-hide-frame-con">
        <div class="lmbfringeFrameNav multiframe clearfix" onmousedown="lmbIniDrag(event,this,'lmbResizeFrame')">

            <div class="evt-hide-frame text-center d-lg-none p-3 multiframe"><i class="lmb-icon lmb-erase"></i></div>
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
			echo "<input type=\"hidden\" id=\"autorefreshPreviewWorkflow_".$firstLevel["id"]."\" VALUE=\"".$firstLevel["autorefresh"]."\">";
			echo "<input type=\"hidden\" name=\"multiframeType_".$firstLevel["preview"]."\" value=\"".$firstLevel["id"]."\">";
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
					$onClick = "onClick=\"parent.main.location.href = '" . $firstLevel["link"] . "'\"; console.log('clicked')";
				}
			}

			$cursor = "cursor:pointer;";
		}else{
			$cursor = "";
		}


        echo '<li '.$onClick.'><div class="menu-side-header">
                <i class="lmbMenuHeaderImage lmb-icon-32 '.($firstLevel['gicon'] ?? $firstLevel['icon']).'"></i>
            <span class="hide-menu">'.$firstLevel["name"].'</span>
            ';


		// default event is "hideShow"
		$event = "hideShowMulti";
		if($firstLevel["event"] != null){
			$event = $firstLevel["event"];
		}

        $onclick = "$event(event,'".$firstLevel["id"]."','".$firstLevel["name"]."','".$firstLevel["gtabid"]."','".$firstLevel["params"]."'$preview,1);var event = arguments[0] || window.event; event.stopPropagation();";

		if($firstLevel["child"] || $firstLevel["preview"] || $firstLevel["event"]){
            echo '<i id="HS'.$firstLevel["id"].'" onclick="'.$onclick.'" class="lmb-icon lmb-angle-down  float-right" valign="top" style="cursor:pointer"></i>';
		}

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


<div class="evt-hide-frame multiframe"><i class="lmb-icon lmb-icon-8 lmb-caret-right"></i></div>

        </div>
    </div>

</div>