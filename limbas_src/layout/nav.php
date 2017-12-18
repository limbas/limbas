<?php
/*
 * Copyright notice
 * (c) 1998-2016 Limbas GmbH - Axel westhagen (support@limbas.org)
 * All rights reserved
 * This script is part of the LIMBAS project. The LIMBAS project is free software; you can redistribute it and/or modify it on 2 Ways:
 * Under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * Or
 * In a Propritary Software Licence http://limbas.org
 * The GNU General Public License can be found at http://www.gnu.org/copyleft/gpl.html.
 * A copy is found in the textfile GPL.txt and important notices to the license from the author is found in LICENSE.txt distributed with these scripts.
 * This script is distributed WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * This copyright notice MUST APPEAR in all copies of the script!
 * Version 3.0
 */

/*
 * ID: 10
 */
?>
<SCRIPT LANGUAGE="JavaScript">

/** Linkfunktionen */

browserType();

/* ---------------- User ------------------ */
function f_2(act,frame1,frame2,main) {
	top.main.location.href = "main.php?action="+ act + "&frame1para=" + frame1 + "&frame2para=" + frame2;
}

function f_3(act,frame1,frame2) {
	top.main.location.href = "main_admin.php?action="+ act + "&frame1para=" + frame1 + "&frame2para=" + frame2;
}

function f_4(PARAMETER) {
	watcher = open("main_admin.php?<?=SID?>&action="+ PARAMETER+ "" ,"watcher","toolbar=0,location=0,status=0,menubar=0,scrollbars=0,resizable=0,width=400,height=500");
}

function print_report(tabid,value,defformat) {
	top.main.location.href="main.php?action=report&gtabid="+tabid+"&report_id="+value+"&report_medium="+defformat;
}

function listdata(ID,NR,TABLE_TOP){

	//must be closed
	if(document.getElementById('pfeil' + NR).src == icon['pfeil_u'].src){

		topParent = "id" + NR.split("id")[1];
		child = document.getElementById(topParent).getElementsByTagName("TR");

		for(i=0;i<child.length;i++){
			if(child[i].id.substring(0,(NR+'id').length)==(NR+'id')  ||  child[i].id.substring(0,(NR+'bo').length)==(NR+'bo') || child[i].id.substring(0,(NR+'to').length)==(NR+'to')){
				if(document.getElementById('pfeil' + NR).src){
					document.getElementById('pfeil' + NR).src = icon['pfeil_r'].src;
				}
	    		child[i].style.name='none';
			}
		}
	// must be opened
	}else{
		<?//firefox without getElementByName
		if(strpos($_SERVER["HTTP_USER_AGENT"],"MSIE")<1){?>
		child = document.getElementsByName(NR);

		for(i=0;i<child.length;i++){
			child[i].style.name='';
	    	document.getElementById('pfeil' + NR).src = icon['pfeil_u'].src;
		}

		<?//IE without.name
		}else{?>

		child = document.getElementById(TABLE_TOP).getElementsByTagName("TR");

		for(i=0;i<child.length;i++){
			if(child[i].name==NR){
				child[i].style.name='<<<';
		    	document.getElementById('pfeil' + NR).src = icon['pfeil_u'].src;
			}
		}
		<?}?>
	}
}



function mainMenu(menu){
	<?php
	foreach($menu as $key1 => $menuType){
		echo "document.getElementById('" . $key1 . "').style.display='none';\n";
	}
	?>
	var el = document.getElementById(menu);
	if(el){
		document.getElementById(menu).style.display = "";
	}
}

function hideShow(name){

	show = hideShow.arguments[1];

	var el = document.getElementById('CONTENT_' + name);
	if(el.style.display=='none' || show){
		
		$(el).show('fast');
		pic = document.getElementById('HS' + name);
		if(pic){
                        $( pic ).removeClass("lmb-angle-down");
                        $( pic ).addClass("lmb-angle-up");
		}
		var show = 1;
	}else{
		$(el).hide('fast');
		pic = document.getElementById('HS' + name);
		if(pic){
                        $( pic ).removeClass("lmb-angle-up");
                        $( pic ).addClass("lmb-angle-down");
		}
		var show = 0;
	}
	ajaxGet(null,'main_dyns.php','layoutSettings&menu='+name+'&show='+show,null);
}

function hideShowSub(div,elt){
	eltDiv = document.getElementById(div);

	if(!eltDiv) return;

	eltTr = eltDiv.getElementsByTagName("TR");
	if(!eltTr) return;

        arrowIcon = $('i[id^=arrowSub' + elt + ']').filter(':visible').first(); // because the id was given twice, once in a hidden div
        if(!arrowIcon) return;
        
        if(arrowIcon.hasClass('lmb-caret-down')){
                arrowIcon.removeClass('lmb-caret-down');
                arrowIcon.addClass('lmb-caret-right');

		for(i=0;i<eltTr.length;i++){
			//document.write (eltTr.id  + "== subElt_" + elt + "<br>\n" + eltTr[]);
			if(eltTr[i].id == "subElt_" + elt) //eltTr[i].style.backgroundColor="blue";
				eltTr[i].style.display='none';
		}
		var show = 0;
	}else{     
                arrowIcon.removeClass('lmb-caret-right');
                arrowIcon.addClass('lmb-caret-down');

		for(i=0;i<eltTr.length;i++){
			//document.write (eltTr.id  + "== subElt_" + elt + "<br>\n" + eltTr[]);
			if(eltTr[i].id == "subElt_" + elt) //eltTr[i].style.backgroundColor="red";
				eltTr[i].style.display='';
		}
		var show = 1;
	}
	ajaxGet(null,'main_dyns.php','layoutSettings&submenu='+div+'_'+elt+'&show='+show,null);
}

hide_frame_size = 200;
function hide_frame(){
	var pcols = top.document.getElementById("mainset").cols;
	fcols = pcols.split(",");

	if(fcols[0] <= 30){
		lfr = hide_frame_size;
		document.getElementById("hiddenframe").style.display = 'none';
		document.getElementById("multiframe").style.display = '';
		fs = 0;
	}else{
		if(browser_ns5){
			lfr = 15;
		}else{
			lfr = 30;
		}
		document.getElementById("hiddenframe").style.display = '';
		document.getElementById("multiframe").style.display = 'none';
		fs = lfr;
	}

	if(fcols.length == 3){
		var size = lfr+",*,"+fcols[2];
	}else{
		var size = lfr+",*";
	}
	
	ajaxGet(null,'main_dyns.php','layoutSettings&frame=nav&size='+fs,null);
	top.document.getElementById("mainset").cols = size;
	
	document.onmouseup = null;
	document.onmousemove = null;
	return false;
}




var dropel = null;
var dropel_width = null;
var posx = null;
var elwidth = null;
function lmbIniDrag(evt,el) {
		dropel = el;
		document.onmouseup = lmbEndResizeFrame;
		elwidth = el.offsetWidth;
		if(browser_ns5){
			posx = evt.screenX;
		}else{
			posx = window.event.screenX;
		}
		document.onmousemove = lmbResizeFrame;
	
	return false;
}

function lmbEndResizeFrame() {
	document.onmouseup = null;
	document.onmousemove = null;
	
	var elw = dropel.offsetWidth;
	hide_frame_size = elw+10;
	if(elwidth > 50 && elwidth < 400 && Math.abs((elw-elwidth)) > 10 ){
		ajaxGet(null,'main_dyns.php','layoutSettings&frame=nav&size='+elw,null);
	}
	
	return false;
}

function lmbResizeFrame(e) {
	
	if(browser_ns5){
		evw = e.screenX - posx
		dw = evw + elwidth;
	}else{
		evw = window.event.screenX - posx
		dw = evw + elwidth;
	}
	
	if(dw > 400 || dw < 50){return false;}
	
	var pcols = top.document.getElementById("mainset").cols;
	fcols = pcols.split(",");
	if(fcols.length == 3){
		var size = dw+",*,"+fcols[2];
	}else{
		var size = dw+",*";
	}
	dwme = (dw-110);

	if(Math.abs((dw-elwidth)) < 5){return;}
	
	var ar = document.getElementsByTagName("div");
	for (var i = ar.length; i > 0;) {
		cc = ar[--i];
		var cid = cc.id.split("_");
		if(cid[0] == "mel"){
			cc.style.width = dwme;
		}
	}
	
	top.document.getElementById("mainset").cols = size;

	return false;
}


function lmb_treeElOpen(treeid,tabid,elid,rand){
	var elname = treeid+'_'+tabid+'_'+elid+'_'+rand;
	var el = document.getElementById('lmbTreeEl_'+elname);
	var img_src = document.getElementById('lmbTreePlus_'+elname).src;
        
	if(el.style.display == 'none'){
		el.style.display = '';
		document.getElementById('lmbTreePlus_'+elname).src = img_src.replace(/(plus)/,"minus");
	}else{
		el.style.display = 'none';
                document.getElementById('lmbTreePlus_'+elname).src = img_src.replace(/(minus)/,"plus");
	}
}


function lmb_treeOpen(treeid,tabid,id){
	
	if(id.length>0 && document.getElementById("img"+treeid)){
		var img_src = document.getElementById("img"+treeid).src;
		if(img_src && img_src.match(/(minus)+/g)){
			document.getElementById("img"+treeid).src = img_src.replace(/(minus)/,"plus");
			document.getElementById(treeid).style.display = "none";
			return;
		}
		if(document.getElementById(treeid)
			&& document.getElementById(treeid).innerHTML.length>1
			&& img_src.match(/(plus)+/g)){
			document.getElementById("img"+treeid).src = img_src.replace(/(plus)/,"minus");
			document.getElementById(treeid).style.display = "";
			return;
		}
	}

	ajaxGet(null,"main_dyns.php","getRelationTree&gtabid="+tabid+"&treeid="+treeid,null,"","",treeid);
}


function lmb_treeSubOpen(treeid,tabid,elid,rand,gtabid,rkey){

	var elname = treeid+'_'+tabid+'_'+elid+'_'+rand;
	var el = document.getElementById('lmbTreeTR_'+elname);
	var img_src1 = document.getElementById('lmbTreeSubPlus_'+elname).src;

        if(el.style.display == 'none'){
		el.style.display = '';
		document.getElementById('lmbTreeSubPlus_'+elname).src = img_src1.replace(/(plus)/,"minus");

                if($('#lmbTreeSubBox_'+elname).hasClass('lmb-folder-closed')){
                    $('#lmbTreeSubBox_'+elname).removeClass('lmb-folder-closed');
                    $('#lmbTreeSubBox_'+elname).addClass('lmb-folder-open');
                }
	}else{
		el.style.display = 'none';
		document.getElementById('lmbTreeSubPlus_'+elname).src = img_src1.replace(/(minus)/,"plus");

                if($('#lmbTreeSubBox_'+elname).hasClass('lmb-folder-open')){
                    $('#lmbTreeSubBox_'+elname).removeClass('lmb-folder-open');
                    $('#lmbTreeSubBox_'+elname).addClass('lmb-folder-closed');
                }
	}
	
	ajaxGet(null,"main_dyns.php","getRelationTree&gtabid="+tabid+"&treeid="+treeid+"&verkn_tabid="+gtabid+"&verkn_fieldid="+rkey+"&verkn_ID="+elid,null,"","","lmbTreeDIV_"+elname);

}


function lmbTreeOpenTable(gtabid,verkn_tabid,verkn_fieldid,verkn_ID){
	parent.main.location.href='main.php?action=gtab_erg&verknpf=1&verkn_showonly=1&verkn_ID='+verkn_ID+'&gtabid='+gtabid+'&verkn_tabid='+verkn_tabid+'&verkn_fieldid='+verkn_fieldid;
}

function lmbTreeOpenData(gtabid,ID,verkn_tabid,verkn_fieldid,verkn_ID,form_id){
	parent.main.location.href='main.php?action=gtab_change&verknpf=1&verkn_showonly=1&gtabid='+gtabid+'&ID='+ID+'&verkn_tabid='+verkn_tabid+'&verkn_fieldid='+verkn_fieldid+'&verkn_ID='+verkn_ID+'&formid='+form_id;
}

function format_tree(elemid){
    var tmp = document.getElementsByTagName("a");
    var elems = new Array();
    if(tmp && tmp.length>0){
        var i,s;
        for(i=0;i<tmp.length;i++){
            s = tmp[i].id;
            if(!s) continue;

            if(s.match(/(atitle)+/g)){
                elems.push(tmp[i]);
                if(tmp[i].id=="atitle"+elemid){
                    for(var k=elems.length;k>0;k--){
                        if(elems[k-1].className==tmp[i].className && elems[k-1].id!=tmp[i].id)
                            continue;
                        elems[k-1].style.fontWeight = "bold";
                        if(elems[k-1].className=="atitle_level0") break;
                    }
                }else
                    tmp[i].style.fontWeight = "normal";
            }
        }
    }
    return true;
} 


<?
/*
var a = document.createElement('a');
a.href='http://www.google.com';
a.target = '_blank';
document.body.appendChild(a);
a.click();
*/
?>



//-->
</script>
<FORM ACTION="main.php" METHOD="post" NAME="form1" TARGET="main" style="display:none;">
<INPUT TYPE="hidden" name="<?echo $_SID;?>" value="<?echo session_id();?>">
<INPUT TYPE="hidden" NAME="ID" VALUE="<?echo $session["user_id"];?>">
<INPUT TYPE="hidden" NAME="aktivid">
<INPUT TYPE="hidden" NAME="action">
<INPUT TYPE="hidden" NAME="alter">
<INPUT TYPE="hidden" NAME="error_msg" VALUE="<?=$lang[25]?>">
<INPUT TYPE="hidden" NAME="csvexp">
<INPUT TYPE="hidden" NAME="tab_group">
<INPUT TYPE="hidden" NAME="gtabid">
<INPUT TYPE="hidden" NAME="snap_id">
<INPUT TYPE="hidden" NAME="source" VALUE="root">
</FORM>

<FORM ACTION="main_admin.php" METHOD="post" NAME="form2" TARGET="main" style="display:none;">
<INPUT TYPE="hidden" name="<?echo $_SID;?>" value="<?echo session_id();?>">
<INPUT TYPE="hidden" NAME="aktivid">
<INPUT TYPE="hidden" NAME="action">
<INPUT TYPE="hidden" NAME="frame1para">
<INPUT TYPE="hidden" NAME="frame2para">
<INPUT TYPE="hidden" NAME="error_msg" VALUE="<?=$lang[25]?>">
</FORM>


<?
$multfrdispl = "";
$hiddendispl = "display:none";

$menu_setting = lmbGetMenuSetting();
if($menu_setting["frame"]["nav"] AND $menu_setting["frame"]["nav"] <= 30){
	$multfrdispl = "display:none";
	$hiddendispl = "";
}
?>

<div id="hiddenframe" style="height:90%;cursor:pointer;<?=$hiddendispl?>" OnClick="return hide_frame();">
    <div class="lmbFrameShow">
        <i class="lmb-icon lmb-icon-aw lmb-caret-right"></i>
    </div>
</div>

<div id="multiframe" style="<?=$multfrdispl?>">

<div id="lmbAjaxContainer" class="ajax_container" style="position:absolute;visibility:hidden;" OnClick="activ_menu=1;"></div>
<table id="framecontext" cellpadding="0" cellspacing="0" style="width:100%;height:100%;" OnContextMenu="return hide_frame();"><tr><td valign="top" class="lmbfringeFrameNav" onmousedown="lmbIniDrag(event,this,'lmbResizeFrame')">

<div style="clear:both;height:0"></div>

<?php

$defaultExpandMenu = "none";
if(!$dwme = $menu_setting["frame"]["nav"]){
	$dwme = 180;
}

foreach($menu as $key1 => $menuType){
	$elnr = 1;

	echo "\n<div id=\"" . $key1 . "\">\n";
	if($displayMainMenu==null){
		$displayMainMenu = "mainMenu(" . $key1 . ");";
	}

	if($menuType){
		foreach ($menuType as $key2 => $firstLevel) {

			if($firstLevel["link"]){
				$onclickSymbol = "onclick=\"".$firstLevel["onclick"].";".$firstLevel["link"]."\"";
                                $onclickIcon = "onclick=\"hideShow('" . $key1 . "_" . $key2 . "');".$firstLevel["onclick"].";var event = arguments[0] || window.event; event.stopPropagation();\"";
			}else{
				$onclickSymbol = "onclick=\"hideShow('" . $key1 . "_" . $key2 . "');".$firstLevel["onclick"]."\"";
                                $onclickIcon = "";
			}

			echo "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" class=\"lmbfringeMenuNav\">";
			echo "<td class=\"lmbMenuHeaderNav\" $onclickSymbol>";

			# Symbol
			if($firstLevel["gicon"]){
                                echo "<i class=\"lmbMenuHeaderImage lmb-icon-32 ".$firstLevel["gicon"]."\" style=\"position:absolute;cursor:pointer\"></i>";
                        }
                        else if($firstLevel['icon'])
                        {
                            echo "<i class=\"lmbMenuHeaderImage lmb-icon-32 ".$firstLevel["icon"]."\" style=\"position:absolute;cursor:pointer\"></i>";
                        }
			
                        if($menu_setting["menu"][$key1 . "_" . $key2] AND !$firstLevel["extension"]){
                            $eldispl = "";  
                            $iconclass = "lmb-angle-up";
                        }else{
                            $eldispl = $defaultExpandMenu;
                            $iconclass = "lmb-angle-down";
                        }
                        
			# popupIcon
			echo "<div style=\"float:right;margin-right:0.5em;\" $onclickIcon>";
			if(count($firstLevel["child"]) > 0 OR $firstLevel["extension"]){
				//echo "<img id=\"HS" . $key1 . "_" . $key2 . "\" src=\"USER/".$session["user_id"]."/menuicons/arrowDown3.png\" valign=\"top\" style=\"cursor:pointer\" border=\"0\">";
				echo "<i id=\"HS" . $key1 . "_" . $key2 . "\" class=\"lmb-icon $iconclass\" valign=\"top\" style=\"cursor:pointer\" border=\"0\"></i>";
                        }
			echo "</div>\n";
			echo "<div class=\"lmbMenuItemHeaderNav\" style=\"cursor:pointer\">".(is_numeric($firstLevel["name"])?$lang[$firstLevel["name"]]:$firstLevel["name"])."</div>\n";

			echo "<div style=\"clear:both;height:1px;overflow:hidden;\"></div>\n";

			echo "</td></tr>";
			echo "<tr >";

			echo "<td colspan=2><div class=\"lmbMenuHeaderNavContent\" id=\"CONTENT_" . $key1 . "_" . $key2 . "\" style=\"display:$eldispl\">";
			if($firstLevel["extension"]){
				echo "<div id=\"PH_" . $key1 . "_" . $key2 . "\" style=\"width:100%;\"></div>";
			}

			# use eval extension
			if($firstLevel["eval"]){
				eval($firstLevel["eval"].";");
			}else{

				echo "<table width=\"100%\" border=0 class=\"lmbMenuBodyNav\">";

				if($firstLevel["child"]){
					foreach ($firstLevel["child"] as $key3 => $element) {

						if($element["header"]){
							echo "<tr><td colspan=\"3\"><span>".$element["name"]."</span></td></tr>";
							echo "<tr><td style=\"height:1px;overflow:hidden;\" colspan=\"3\"><div style=\"height:1px;background-color:black;width:100%\"></div></td></tr>";
							continue;
						}

						if($element["icon"]){
							$img = "<i class=\"lmbMenuItemImage lmb-icon ".$element["icon"]."\"></i>" ;
						}else{$img ='';}

						$cursor = "";
						if(substr($element["link"],0,4) == 'main'){
							$onclick = "onclick=\"".$element["onclick"].";parent.main.location.href='".$element["link"]."'\"";
						}elseif($element["link"]){
							$onclick = "onclick=\"".$element["onclick"].";".$element["link"]."\"";
						}else{
							$onclick = "onclick=\"hideShowSub('$key1','" . $element["name"] . "')\"";
							$cursor = "cursor:default;";
						}

						echo "<tr><td>$img</td><td nowrap style=\"overflow:hidden;width:100%;background-color:".$element["bg"]."\" title=\"" . $element["desc"] . "\">";
						$menuValue = (is_numeric($element["name"])?$lang[$element["name"]]:$element["name"]);
						echo "<a style=\"overflow:hidden;\" class=\"lmbMenuItemBodyNav\" $onclick><div id=\"mel_".$firstLevel["id"]."_".$element["id"]."\" style=\"width:".($dwme-100)."px;cursor:pointer;".$element["style"]."\">".$menuValue."</div></a>";
						
						if($menu_setting["submenu"][$key1."_".$element["name"]]){$eldispl = "";}else{$eldispl = $defaultExpandMenu;}

						if($element["child"]){
							if($eldispl){$sadr = " lmb-caret-right ";}else{$sadr = " lmb-caret-down ";}
							echo "<td class=\"lmbMenuItemBodyNav\" width=\"100%\" align=\"right\" onclick=\"hideShowSub('$key1','" . $element["name"] . "')\" style=\"cursor:pointer\">";
                                                        //echo "<img id=\"arrowSub" . $element["name"] . "\" src=\"USER/" . $session["user_id"] . "/menuicons/$sadr.png\">";
                                                        echo "<i id=\"arrowSub" . $element["name"] . "\" class=\"lmb-icon $sadr\"></i>";
                                                        echo "</td>";
						}else{
							echo "<td>&nbsp;</td>";
						}

						if($element["child"])

						foreach ($element["child"] as $key4 => $subelement) {
							$cursor = "";
							if(substr($subelement["link"],0,4) == 'main'){
								$onclick = "onclick=\"".$subelement["onclick"].";parent.main.location.href='".$subelement["link"]."'\"";
							}elseif($subelement["link"]){
								$onclick = "onclick=\"".$subelement["onclick"].";".$subelement["link"]."\"";
							}else{
								$onclick = "";
								$cursor = "cursor:default;";
							}
							if($subelement["icon"]){
								//$img = "<img border=\"0\" src=\"" . $subelement["icon"] . "\" style=\"vertical-align:text-bottom\">" ;
								$img = "<i border=\"0\" class=\"lmb-icon " . $subelement["icon"] . "\" style=\"vertical-align:text-bottom\"></i>" ;
							}else{$img = "";}
							
							echo "<tr id=\"subElt_" . $element["name"] . "\" style=\"display:$eldispl;overflow:hidden;width:100px\"><td>&nbsp;</td>";
							echo "<td colspan=\"2\" class=\"contentSub\" nowrap title=\"" . $subelement["desc"] . "\"><div id=\"mel_".$firstLevel["id"]."_".$element["id"]."_".$subelement["id"]."\" style=\"width:".($dwme-110).";".$subelement["style"]."\">";

							$textToDisplay = (is_numeric($subelement["name"])?$lang[$subelement["name"]]:$subelement["name"]);
							echo "<a class=\"lmbMenuItemBodyNav\" $onclick>$img&nbsp;" . $textToDisplay."</a>";

							echo "</div></td></tr>";
							$elnr++;
						}
					}
				}

				echo "<tr><td style=\"height:5px;overflow:hidden\"></td></tr>";
				echo "</table>";
			}
			echo "</div></td></tr></table>";
		}
	}


	echo  "</div>\n";


}



echo '<div onclick="return hide_frame();"><div class="lmbMenuHeaderNav lmbMenuHide"><i class="lmb-icon lmb-icon-8 lmb-caret-left"></i></div></div>';

echo "</td></tr></table>\n";


# sho first menu in nav frame
foreach($LINK["name"] as $key => $value){
	if($LINK["subgroup"][$key] == 2 AND $LINK["typ"][$key] == 1){
		echo "<script language='javascript'>mainMenu(" . $key . ")</script>";
		$displayMainMenu = "mainMenu(" . $key . ")";
		break;
	}
}

echo "<script language='javascript'>" . $displayMainMenu . "</script>";

?>
</div>