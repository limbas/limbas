<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */



/*----------------- Filter DIV -------------------*/?>


<DIV ID="element3" class="lmbContextMenu" style="position:absolute;visibility:hidden;z-index:10001;" onclick="activ_menu=1">
    <FORM NAME="ffilter_form">
        <?php pop_left();?>
        <TEXTAREA NAME="filter" OnChange="eval('document.form1.filterrule_'+this.form.id.value+'.value = this.value');save_rules(this.form.gtabid.value,this.form.field_id.value,8);" STYLE="width:200px;height:100px;background-color:<?= $farbschema['WEB8'] ?>;"></TEXTAREA>
        <?php pop_right();?>
        <?php pop_bottom();?>
        <INPUT TYPE="HIDDEN" NAME="id">
        <INPUT TYPE="HIDDEN" NAME="gtabid">
        <INPUT TYPE="HIDDEN" NAME="field_id">
    </FORM>
</DIV>


<DIV ID="element6" class="lmbContextMenu" style="position:absolute;visibility:hidden;z-index:10001;" onclick="activ_menu=1">
    <FORM NAME="edittab_form">
        <?php pop_left();?>
        <TEXTAREA ID="edittab_value" NAME="edittab_value" OnChange="eval('document.form1.edit_rule_'+this.form.gtabid.value+'.value = this.value'); save_rules(this.form.gtabid.value,'',27)" STYLE="width:200px;height:100px;background-color:<?= $farbschema['WEB8'] ?>;"></TEXTAREA>
        <?php pop_right();?>
        <?php pop_bottom();?>
        <INPUT TYPE="HIDDEN" NAME="gtabid">
    </FORM>
</DIV>


<!--<div ID="element4" class="lmbContextMenu" style="visibility:hidden;z-index:10001;" OnClick="activ_menu = 1;">
    <FORM NAME="fcolor_form">
        <?php #----------------- Farb-Menü -------------------
        unset($opt);
        pop_top('limbasDivMenuFarb');
        pop_color(null, null, 'element4');
        pop_bottom();
        ?>
    </FORM>
</div>-->


<DIV ID="element5" class="lmbContextMenu" style="position:absolute;visibility:hidden;z-index:10001;" onclick="activ_menu=1">
    <FORM NAME="editrule_form">
        <?php pop_left();?>
        <TEXTAREA NAME="editrule" OnChange="eval('document.form1.'+this.form.id.value+'.value = this.value');save_rules(this.form.gtabid.value,this.form.field_id.value,17);" STYLE="width:200px;height:100px;background-color:<?= $farbschema['WEB8'] ?>;"></TEXTAREA>
        <?php pop_right();?>
        <?php pop_bottom();?>
        <INPUT TYPE="HIDDEN" NAME="id">
        <INPUT TYPE="HIDDEN" NAME="gtabid">
        <INPUT TYPE="HIDDEN" NAME="field_id">
    </FORM>
</DIV>



<DIV ID="element1" class="lmbContextMenu" style="position:absolute;visibility:hidden;z-index:10001;" onclick="activ_menu=1">
    <FORM NAME="indicator_form">
        <?php pop_left();?>
        <TEXTAREA ID="indicator_value" NAME="indicator_value" OnChange="eval('document.form1.indicator_rule_'+this.form.gtabid.value+'.value = this.value'); save_rules(this.form.gtabid.value,'',31)" STYLE="width:200px;height:100px;background-color:<?= $farbschema['WEB8'] ?>;"></TEXTAREA>
        <?php pop_right();?>
        <?php pop_bottom();?>
        <INPUT TYPE="HIDDEN" NAME="gtabid">
    </FORM>
</DIV>


<DIV ID="element7" class="lmbContextMenu" style="position:absolute;visibility:hidden;z-index:10001;" onclick="activ_menu=1">
    <FORM NAME="orderby_form">
        <?php pop_left();?>
        <TEXTAREA ID="orderby_value" NAME="orderby_value" OnChange="eval('document.form1.orderby_value_'+this.form.gtabid.value+'.value = this.value'); save_rules(this.form.gtabid.value,'',7)" STYLE="width:200px;height:100px;background-color:<?= $farbschema['WEB8'] ?>;"></TEXTAREA>
        <?php pop_right();?>
        <?php pop_bottom();?>
        <INPUT TYPE="HIDDEN" NAME="gtabid">
    </FORM>
</DIV>




<script>

var activ_menu = null;
function divclose(){
	if(!activ_menu){
		hide_trigger();
		document.getElementById("element1").style.visibility='hidden';
		document.getElementById("element3").style.visibility='hidden';
		document.getElementById("element4").style.visibility='hidden';
		document.getElementById("element5").style.visibility='hidden';
		document.getElementById("element6").style.visibility='hidden';
		document.getElementById("element7").style.visibility='hidden';
	}
	activ_menu = 0;
}


function set_color(color) {
	save_rules(ftab,ffield,'3',color);
	var el = "color_"+ftab+"_"+ffield;
	document.getElementById(el).style.backgroundColor=color;
}

// --- Farbmenüsteuerung -----------------------------------
function div4(el,tab,field) {
	limbasDivShow(el,null,"element4");
	ftab = tab;
	ffield = field;
}

// --- Filtermenüsteuerung -----------------------------------
function div3(el,id,gtabid,field_id) {
	limbasDivShow(el,null,"element3");
	eval("document.ffilter_form.filter.value = document.form1.filterrule_"+id+".value;");
	document.ffilter_form.id.value = id;
	document.ffilter_form.gtabid.value = gtabid;
	document.ffilter_form.field_id.value = field_id;
}

// --- Editrules -----------------------------------
function div5(el,id,gtabid,field_id) {
	limbasDivShow(el,null,"element5");
	eval("document.editrule_form.editrule.value = document.form1."+id+".value;");
	document.editrule_form.id.value = id;
	document.editrule_form.gtabid.value = gtabid;
	document.editrule_form.field_id.value = field_id;
}

// --- table edit -----------------------------------
function div6(el,gtabid) {
	limbasDivShow(el,null,"element6");
	document.edittab_form.gtabid.value = gtabid;
	el = "edit_rule_"+gtabid;
	document.getElementById("edittab_value").value = document.form1.elements[el].value;
}

// --- orderby-----------------------------------
function div7(el,gtabid) {
	limbasDivShow(el,null,"element7");
	document.orderby_form.gtabid.value = gtabid;
	el = "orderby_value_"+gtabid;
	document.getElementById("orderby_value").value = document.form1.elements[el].value;
}

// --- indicator -----------------------------------
function div1(el,gtabid) {
	limbasDivShow(el,null,"element1");
	document.indicator_form.gtabid.value = gtabid;
	el = "indicator_rule_"+gtabid;
	document.getElementById("indicator_value").value = document.form1.elements[el].value;
}

// --- Popup-funktion ----------
var popups = new Array();
function pops(tab){
	eval("var ti = 'table_"+tab+"';");
	eval("var pi = 'popicon_"+tab+"';");
	if(document.getElementById(ti).style.display){
		document.getElementById(ti).style.display='';
		eval("document."+pi+".src='assets/images/legacy/outliner/minusonly.gif';");
	}else{
		document.getElementById(ti).style.display='none';
		eval("document."+pi+".src='assets/images/legacy/outliner/plusonly.gif';");
	}
}



// --- Wertespeicher ----------
var ftab;
var ffield;
var saverules = new Array();
function save_rules(tab,field,typ,value){
    saverules[tab+"_"+field+"_"+typ] = value;
//	eval("saverules['"+tab+"_"+field+"_"+typ+"'] = value;");
}


function send(){
	var saval = '';
	for (var e in saverules){
		var saval = saval + e + "_" + saverules[e] + "|";
	}
	document.form1.rules.value = saval;

	var popup = new Array();
	$.each($(".popicon"), function() {
	    if($(this).attr('src') == 'assets/images/legacy/outliner/minusonly.gif'){
	    	popup.push($(this).attr('tabid'));
	    }
	});
	
	document.form1.popup.value = popup.join(';');
}


function set_searchrule(tab){
	var ar = document.getElementsByTagName("input");
	for (var i = ar.length; i > 0;) {
		cc = ar[--i];
		var rsel = cc.name.split("_");
		if(rsel[0] == "filterrule" && rsel[1] == tab){
			var filterrule = cc.name;
			var filterprev = "filterprev_"+rsel[1]+"_"+rsel[2];
			if(!cc.value){
				eval("document.form1."+filterrule+".value = document.form1."+filterprev+".value;");
				save_rules(rsel[1],rsel[2],8);
			}
		}
	}
}

function hide_trigger(){
	var ar = document.getElementsByTagName("span");
	for (var i = ar.length; i > 0;) {
		cc = ar[--i];
		if(cc.id.substring(0,11) == "tab_trigger" || cc.id.substring(0,13) == "field_trigger"){
			cc.style.display='none';
		}
	}
}

function click_all(tab,typ,el){
	var ar = document.getElementsByTagName("input");
	for (var i = ar.length; i > 0;) {
		cc = ar[--i];
		if(cc.name.substring(0,4) == "view" && typ == 1){
			var rsel = cc.name.split("_");
			if(rsel[1] == tab){
				save_rules(tab,rsel[2],typ,el.value);
				if(el.checked == true){
					cc.checked = true;
				}else{
					cc.checked = false;
				}
			}
		}
		if(cc.name.substring(0,4) == "edit" && typ == 2){
			var rsel = cc.name.split("_");
			if(rsel[1] == tab){
				save_rules(tab,rsel[2],typ,el.value);
				if(el.checked == true){
					cc.checked = true;
				}else{
					cc.checked = false;
				}
			}
		}

		if(cc.name.substring(0,4) == "need" && typ == 9){
			var rsel = cc.name.split("_");
			if(rsel[1] == tab){
				save_rules(tab,rsel[2],typ,el.value);
				if(el.checked == true){
					cc.checked = true;
				}else{
					cc.checked = false;
				}
			}
		}

		if(cc.name.substring(0,4) == "copy" && typ == 13){
			var rsel = cc.name.split("_");
			if(rsel[1] == tab){
				save_rules(tab,rsel[2],typ,el.value);
				if(el.checked == true){
					cc.checked = true;
				}else{
					cc.checked = false;
				}
			}
		}
		
		if(cc.name.substring(0,6) == "option" && typ == 32){
			var rsel = cc.name.split("_");
			if(rsel[1] == tab){
				save_rules(tab,rsel[2],typ,el.value);
				if(el.checked == true){
					cc.checked = true;
				}else{
					cc.checked = false;
				}
			}
		}

		if(cc.name.substring(0,11) == "versionrule" && typ == 16){
			var rsel = cc.name.split("_");
			if(rsel[1] == tab){
				save_rules(tab,rsel[2],typ,el.value);
				if(el.checked == true){
					cc.checked = true;
				}else{
					cc.checked = false;
				}
			}
		}
	}
}

function f_3(PARAMETER) {
	document.form1.action.value = PARAMETER;
	document.form1.submit();
}
</SCRIPT>

<div class="container-fluid p-3">
    <form ACTION="main_admin.php" METHOD="post" NAME="form1">
        <input type="hidden" name="action" value="setup_group_tab">
        <INPUT TYPE="hidden" NAME="ID" VALUE="<?= $ID ?>">
        <INPUT TYPE="hidden" NAME="rules">
        <INPUT TYPE="hidden" NAME="popup" VALUE="<?=$popup?>">

        <div class="row">
            <?php
            $activeTabLinkId = 100;
            require(__DIR__.'/group_tabs.php') ?>


            <div class="tab-content col-9 ps-0">
                <div class="tab-pane active p-3 d-inline-block border border-start-0 bg-white">

                    <h5><i class="lmb-icon lmb-group"></i>&nbsp;<?=$groupdat["name"][$ID]?></h5>

                    <hr>

                    <div class="">

                        <TABLE ID="tab1" width="100%" cellspacing="2" cellpadding="1">

                            <?php
                            # ------ Suchkriterien übersetzten -------
                            $searchlang["txt"][1] = $lang[106];
                            $searchlang["txt"][2] = $lang[107];
                            $searchlang["txt"][3] = $lang[108];
                            $searchlang["num"][1] = $lang[713];
                            $searchlang["num"][2] = $lang[711];
                            $searchlang["num"][3] = $lang[712];
                            $searchlang["andor"][1] = $lang[854];
                            $searchlang["andor"][2] = $lang[855];

                            /* --- Spaltenrechte --------------------------------------- */
                            function viewrows($gtabid){
                                global $farbschema;
                                global $f_result;
                                global $l_result;
                                global $s_result;
                                global $is_popup;
                                global $ID;
                                global $gsr;
                                global $searchlang;
                                global $lang;
                                global $group_level;
                                global $session;
                                global $lmcurrency;
                                global $ext_fk;
                                global $gtrigger;
                                global $gtab;
                                global $gfield;

                                if($gtab["typ"][$gtabid] == 5){$isview = 1;}

                                if($is_popup){if(in_array($gtabid,$is_popup)){$display = "";}else{$display = "none";}}else{$display = "none";}
                                echo "<TR ID=\"table_$gtabid\" STYLE=\"display:$display\">";
                                echo "<TD ALIGN=\"left\" COLSPAN=\"8\"><TABLE class=\"table table-sm table-striped\" BORDER=\"0\" cellspacing=\"1\" cellpadding=\"1\">";
                                echo "<TR class=\"table-section\"><TD ALIGN=\"LEFT\" COLSPAN=\"4\">";

                                echo "<TABLE BORDER=\"0\" cellspacing=\"1\" cellpadding=\"1\"><TR>";
                                # --- view ---
                                echo "<TD nowrap><i class=\"lmb-icon lmb-eye-slash\" TITLE=\"$lang[2302]\"></i><INPUT TYPE=\"checkbox\"  NAME=\"tabhidemenu_".$gtabid."\"  onclick=\"save_rules('$gtabid','',18)\" ";
                                if($f_result[$gtabid]["hidemenu"] == 1){echo "CHECKED";}
                                echo "></TD>";

                                # --- view versions ---
                                if($gtab["versioning"][$gtabid] AND !$isview){
                                    if((($l_result[$gtabid]["viewver"] == 1 OR !$l_result) AND $s_result[$gtabid]["viewver"]) OR $session["superadmin"]){
                                        echo "<TD nowrap><i class=\"lmb-icon-cus lmb-copy-eye\"  TITLE=\"$lang[2356]\"></i><INPUT TYPE=\"checkbox\" NAME=\"tabviewver_".$gtabid."\" onclick=\"save_rules('$gtabid','',20)\" ";
                                        if($f_result[$gtabid]["viewver"] == 1){echo "CHECKED";}
                                        echo "></TD>";
                                    }elseif(!$l_result[$gtabid]["viewver"] AND $s_result[$gtabid]["viewver"]){
                                        echo "<TD nowrap><i class=\"lmb-icon-cus lmb-copy-eye\"  TITLE=\"$lang[2356]\" style=\"opacity:0.3;filter:Alpha(opacity=30);\"></i><INPUT readonly disabled TYPE=\"checkbox\"></TD>";
                                    }

                                    if((($l_result[$gtabid]["editver"] == 1 OR !$l_result) AND $s_result[$gtabid]["editver"]) OR $session["superadmin"]){
                                        echo "<TD nowrap><i class=\"lmb-icon-cus lmb-copy-edit\"  TITLE=\"$lang[3010]\"></i><INPUT TYPE=\"checkbox\" NAME=\"tabeditver_".$gtabid."\" onclick=\"save_rules('$gtabid','',201)\" ";
                                        if($f_result[$gtabid]["editver"] == 1){echo "CHECKED";}
                                        echo "></TD>";
                                    }elseif(!$l_result[$gtabid]["editver"] AND $s_result[$gtabid]["editver"]){
                                        echo "<TD nowrap><i class=\"lmb-icon-cus lmb-copy-edit\"  TITLE=\"$lang[3010]\" style=\"opacity:0.3;filter:Alpha(opacity=30);\"></i><INPUT readonly disabled TYPE=\"checkbox\"></TD>";
                                    }

                                    if((($l_result[$gtabid]["delver"] == 1 OR !$l_result) AND $s_result[$gtabid]["delver"]) OR $session["superadmin"]){
                                        echo "<TD nowrap><i class=\"lmb-icon-cus lmb-copy-minus\"  TITLE=\"$lang[3009]\"></i><INPUT TYPE=\"checkbox\" NAME=\"tabdelver_".$gtabid."\" onclick=\"save_rules('$gtabid','',202)\" ";
                                        if($f_result[$gtabid]["delver"] == 1){echo "CHECKED";}
                                        echo "></TD>";
                                    }elseif(!$l_result[$gtabid]["delver"] AND $s_result[$gtabid]["delver"]){
                                        echo "<TD nowrap><i class=\"lmb-icon-cus lmb-copy-minus\"  TITLE=\"$lang[3009]\" style=\"opacity:0.3;filter:Alpha(opacity=30);\"></i><INPUT readonly disabled TYPE=\"checkbox\"></TD>";
                                    }

                                }
                                # --- unlock data ---
                                if($gtab["lockable"][$gtabid] AND !$isview){
                                    if((($l_result[$gtabid]["lock"] == 1 OR !$l_result) AND $s_result[$gtabid]["lock"]) OR $session["superadmin"]){
                                        echo "<TD nowrap><i class=\"lmb-icon lmb-lock\" TITLE=\"$lang[2428]\"></i><INPUT TYPE=\"checkbox\" NAME=\"lock_".$gtabid."\" onclick=\"save_rules('$gtabid','',21)\" ";
                                        if($f_result[$gtabid]["lock"] == 1){echo "CHECKED";}
                                        echo "></TD>";
                                    }elseif(!$l_result[$gtabid]["lock"] AND $s_result[$gtabid]["lock"]){
                                        echo "<TD nowrap><i class=\"lmb-icon lmb-lock\"  TITLE=\"$lang[2428]\" style=\"opacity:0.3;filter:Alpha(opacity=30);\"></i><INPUT readonly disabled TYPE=\"checkbox\"></TD>";
                                    }
                                }

                                # Dataset-Rules
                                if($gtab["has_userrules"][$gtabid] AND !$isview){
                                    # --- set userrules for administrate datasets ---
                                    if((($l_result[$gtabid]["userrules"] == 1 OR !$l_result) AND $s_result[$gtabid]["edit_userrules"]) OR $session["superadmin"]){
                                        echo "<TD nowrap><i class=\"lmb-icon lmb-group-gear\" TITLE=\"$lang[2337]\"></i><INPUT TYPE=\"checkbox\"  NAME=\"tabuserrules_".$gtabid."\"  onclick=\"save_rules('$gtabid','',19)\" ";
                                        if($f_result[$gtabid]["userrules"] == 1){echo "CHECKED";}
                                        echo "></TD>";
                                    }elseif(!$l_result[$gtabid]["userrules"] AND $s_result[$gtabid]["edit_userrules"]){
                                        echo "<TD nowrap><i class=\"lmb-icon lmb-group-gear\"  TITLE=\"$lang[2337]\" style=\"opacity:0.3;filter:Alpha(opacity=30);\"></i><INPUT readonly disabled TYPE=\"checkbox\" STYLE=\"background-color:transparent;opacity:0.3;filter:Alpha(opacity=30);\" ></TD>";
                                    }
                                    # --- set userrules for manage created datasets ---
                                    if((($l_result[$gtabid]["userprivilege"] == 1 OR !$l_result) AND $s_result[$gtabid]["edit_ownuserrules"]) OR $session["superadmin"]){
                                        echo "<TD nowrap><i class=\"lmb-icon lmb-user-gear\" TITLE=\"$lang[2453]\"></i><INPUT TYPE=\"checkbox\"  NAME=\"tabuserprivilege_".$gtabid."\"  onclick=\"save_rules('$gtabid','',28)\" ";
                                        if($f_result[$gtabid]["userprivilege"] == 1){echo "CHECKED";}
                                        echo "></TD>";
                                    }elseif(!$l_result[$gtabid]["userprivilege"] AND $s_result[$gtabid]["edit_ownuserrules"]){
                                        echo "<TD nowrap><i class=\"lmb-icon lmb-user-gear\"  TITLE=\"$lang[2453]\" style=\"opacity:0.3;filter:Alpha(opacity=30);\"></i><INPUT readonly disabled TYPE=\"checkbox\"  ></TD>";
                                    }
                                    # --- set userrules for manage single user/groups ---
                                    if((($l_result[$gtabid]["hierarchicprivilege"] == 1 OR !$l_result) AND $s_result[$gtabid]["hierarchicprivilege"]) OR $session["superadmin"]){
                                        
                                        echo "<TD nowrap>";

echo "<div class=\"dropdown\"><i class=\"lmb-icon lmb-groups cursor-pointer\" data-bs-toggle=\"dropdown\"  TITLE=\"$lang[2516]\" OnClick=\"save_rules('$gtabid','',30)\"></i><INPUT TYPE=\"checkbox\"  NAME=\"tabhierarchicprivilege_".$gtabid."\"  onclick=\"save_rules('$gtabid','',29)\" ";
                                        if($f_result[$gtabid]["hierarchicprivilege"] == 1){echo "CHECKED";}
                                        echo ">";
                                        
                                        $glitems["name"] = array("view_$gtabid","edit_$gtabid","delete_$gtabid");
                                        $glitems["header"] = array("<i class=\"lmb-icon lmb-eye\"></i>","<i class=\"lmb-icon lmb-pencil\"></i>","<i class=\"lmb-icon lmb-trash\"></i>");
                                        
                                        
                                        echo '<div class="dropdown-menu">';

                                            getGroupTree("GroupSelect_".$gtabid,$glitems,$f_result[$gtabid]["specificprivilege"]);

                                        echo '</div></div>';
                                        
                                        
                                        echo "</TD>";
                                    }elseif(!$l_result[$gtabid]["hierarchicprivilege"] AND $s_result[$gtabid]["hierarchicprivilege"]){
                                        echo "<TD nowrap><i class=\"lmb-icon lmb-groups\"  TITLE=\"$lang[2516]\" style=\"opacity:0.3;filter:Alpha(opacity=30);\"></i><INPUT readonly disabled TYPE=\"checkbox\" STYLE=\"background-color:transparent;opacity:0.3;filter:Alpha(opacity=30);\" ></TD>";
                                    }
                                }
                                # --- add ---
                                if(!$isview){
                                    if((($l_result[$gtabid]["add"] == 1 OR !$l_result) AND $s_result[$gtabid]["add"]) OR $session["superadmin"]){
                                        echo "<TD nowrap><i class=\"lmb-icon-cus lmb-page-new\" TITLE=\"$lang[571]\"></i><INPUT TYPE=\"checkbox\"  NAME=\"tabadd_".$gtabid."\"  onclick=\"save_rules('$gtabid','',6)\" ";
                                        if($f_result[$gtabid]["add"] == 1){echo "CHECKED";}
                                        echo "></TD>";
                                    }elseif(!$l_result[$gtabid]["add"] AND $s_result[$gtabid]["add"]){
                                        echo "<TD nowrap><i class=\"lmb-icon-cus lmb-page-new\"  TITLE=\"$lang[571]\" style=\"opacity:0.3;filter:Alpha(opacity=30);\"></i><INPUT readonly disabled TYPE=\"checkbox\" STYLE=\"background-color:transparent;opacity:0.3;filter:Alpha(opacity=30);\" ></TD>";
                                    }
                                }
                                # --- delete ---
                                if(!$isview){
                                    if((($l_result[$gtabid]["delete"] == 1 OR !$l_result) AND $s_result[$gtabid]["delete"]) OR $session["superadmin"]){
                                        echo "<TD nowrap><i class=\"lmb-icon lmb-page-delete-alt\" TITLE=\"$lang[160]\"></i><INPUT TYPE=\"checkbox\"  NAME=\"tabdel_".$gtabid."\"  onclick=\"save_rules('$gtabid','',4)\" ";
                                        if($f_result[$gtabid]["delete"] == 1){echo "CHECKED";}
                                        echo "></TD>";
                                    }elseif(!$l_result[$gtabid]["delete"] AND $s_result[$gtabid]["delete"]){
                                        echo "<TD nowrap><i class=\"lmb-icon lmb-page-delete-alt\"  TITLE=\"$lang[160]\" style=\"opacity:0.3;filter:Alpha(opacity=30);\"></i><INPUT readonly disabled TYPE=\"checkbox\" STYLE=\"background-color:transparent;opacity:0.3;filter:Alpha(opacity=30);\" ></TD>";
                                    }
                                }
                                # --- trash ---
                                if(!$isview){
                                    if((($l_result[$gtabid]["trash"] == 1 OR !$l_result) AND $s_result[$gtabid]["trash"]) OR $session["superadmin"]){
                                        echo "<TD nowrap><i class=\"lmb-icon lmb-trash\" TITLE=\"$lang[160]\"></i><INPUT TYPE=\"checkbox\"  NAME=\"tabtrash_".$gtabid."\"  onclick=\"save_rules('$gtabid','',7)\" ";
                                        if($f_result[$gtabid]["trash"] == 1){echo "CHECKED";}
                                        echo "></TD>";
                                    }elseif(!$l_result[$gtabid]["trash"] AND $s_result[$gtabid]["trash"]){
                                        echo "<TD nowrap><i class=\"lmb-icon lmb-trash\"  TITLE=\"$lang[160]\" style=\"opacity:0.3;filter:Alpha(opacity=30);\"></i><INPUT readonly disabled TYPE=\"checkbox\" STYLE=\"background-color:transparent;opacity:0.3;filter:Alpha(opacity=30);\" ></TD>";
                                    }
                                }
                                # --- archive ---
                                if(!$isview){
                                    if((($l_result[$gtabid]["hide"] == 1 OR !$l_result) AND $s_result[$gtabid]["hide"]) OR $session["superadmin"]){
                                        echo "<TD nowrap><i class=\"lmb-icon lmb-page-key\" TITLE=\"$lang[1257]\"></i><INPUT TYPE=\"checkbox\"  NAME=\"tabhide_".$gtabid."\"  onclick=\"save_rules('$gtabid','',5)\" ";
                                        if($f_result[$gtabid]["hide"] == 1){echo "CHECKED";}
                                        echo "></TD>";
                                    }elseif(!$l_result[$gtabid]["hide"] AND $s_result[$gtabid]["hide"]){
                                        echo "<TD nowrap><i class=\"lmb-icon lmb-page-key\"  TITLE=\"$lang[1257]\" style=\"opacity:0.3;filter:Alpha(opacity=30);\"></i><INPUT readonly disabled TYPE=\"checkbox\" STYLE=\"background-color:transparent;opacity:0.3;filter:Alpha(opacity=30);\" ></TD>";
                                    }
                                }
                                echo "</TR></TABLE>";

                                # --- view ---
                                echo "<TD ALIGN=\"RIGHT\"><TABLE cellspacing=\"0\" cellpadding=\"0\" STYLE=\"width:40px\"><TR><TD ALIGN=\"RIGHT\" nowrap><i class=\"lmb-icon lmb-eye\"  TITLE=\"$lang[2303]\"></i><INPUT TYPE=\"checkbox\" NAME=\"tabview_".$gtabid."\" ";
                                if(!$isview){echo "onclick=\"click_all('$gtabid','1',this);\" ";
                                }else{ echo "onclick=\"click_all('$gtabid','1',this);save_rules('$gtabid',0,1);\"";}
                                if($f_result[$gtabid]["tabview"]){echo " CHECKED";}
                                echo "></TD></TR></TABLE></TD>";
                                # --- edit ---
                                if(!$isview){
                                    echo "<TD ALIGN=\"RIGHT\"><TABLE cellspacing=\"0\" cellpadding=\"0\" STYLE=\"width:35px\"><TR><TD ALIGN=\"RIGHT\" nowrap><i class=\"lmb-icon lmb-pencil\"  TITLE=\"$lang[1259]\"></i><INPUT TYPE=\"checkbox\" NAME=\"tabedit_".$gtabid."\" onclick=\"click_all('$gtabid','2',this)\"";
                                    if($f_result[$gtabid]["tabedit"]){echo " CHECKED";}
                                    echo "></TD></TR></TABLE></TD>";
                                }
                                # --- need ---
                                if(!$isview){
                                    echo "<TD ALIGN=\"RIGHT\"><TABLE cellspacing=\"0\" cellpadding=\"0\" STYLE=\"width:35px\"><TR><TD ALIGN=\"RIGHT\" nowrap><i class=\"lmb-icon lmb-exclamation\"  TITLE=\"$lang[1508]\"></i><INPUT TYPE=\"checkbox\" NAME=\"tabneed_".$gtabid."\" onclick=\"click_all('$gtabid','9',this)\"";
                                    if($f_result[$gtabid]["tabneed"]){echo " CHECKED";}
                                    echo "></TD></TR></TABLE></TD>";
                                }
                                # --- copy ---
                                if(!$isview){
                                    echo "<TD ALIGN=\"RIGHT\"><TABLE cellspacing=\"0\" cellpadding=\"0\" STYLE=\"width:35px\"><TR><TD ALIGN=\"RIGHT\" nowrap><i class=\"lmb-icon lmb-copy\"  TITLE=\"$lang[1464]\"></i><INPUT TYPE=\"checkbox\" NAME=\"tabcopy_".$gtabid."\" onclick=\"click_all('$gtabid','13',this)\"";
                                    if($f_result[$gtabid]["tabcopy"]){echo " CHECKED";}
                                    echo "></TD></TR></TABLE></TD>";
                                }
                                # --- list edit ---
                                if(!$isview){
                                    echo "<TD ALIGN=\"CENTER\"><i class=\"lmb-icon-cus lmb-list-edit\"  TITLE=\"$lang[1290]\"></i></TD>";
                                }
                                # --- options ---
                                echo "<TD ALIGN=\"RIGHT\"><TABLE cellspacing=\"0\" cellpadding=\"0\" STYLE=\"width:35px\"><TR><TD ALIGN=\"RIGHT\" nowrap><i class=\"lmb-icon lmb-cog-alt\"  TITLE=\"$lang[2795]\"></i><INPUT TYPE=\"checkbox\" NAME=\"taboptions_".$gtabid."\" onclick=\"click_all('$gtabid','32',this)\"";
                                if($f_result[$gtabid]["taboption"]){echo " CHECKED";}
                                echo "></TD></TR></TABLE></TD>";

                                # --- speech recognition ---
                                if(!$isview){
                                    echo "<TD ALIGN=\"RIGHT\"><i class=\"lmb-icon lmb-microphone\"  TITLE=\"Speech recognition\"></i></TD>";
                                }

                                # --- versioning ---
                                if(!$isview AND $gtab["versioning"][$gtabid] AND $f_result[$gtabid]["versioning_type"] == 2){
                                    echo "<TD ALIGN=\"RIGHT\"><TABLE cellspacing=\"0\" cellpadding=\"0\" STYLE=\"width:35px\">";
                                    echo "<TR><TD ALIGN=\"RIGHT\" nowrap><i class=\"lmb-icon lmb-versioning-type\"  TITLE=\"$lang[2132]\"></i><INPUT TYPE=\"checkbox\" onclick=\"click_all('$gtabid','16',this)\"";
                                    if(@in_array("1",$f_result[$gtabid]["versionable"])){echo " CHECKED";}
                                    echo "></TD>";
                                    echo "</TR></TABLE></TD>";
                                }

                                # ---- IS VIEW ------
                                if($isview){
                                    echo "<TD ALIGN=\"CENTER\"><i class=\"lmb-icon lmb-colors\"  TITLE=\"$lang[2567]\"></i></TD>";
                                    echo "<TD colspan=\"4\"></TD>";
                                    if(!$f_result[$gtabid]["field_id"]){
                                        echo "<TD style=\"width:700px;color:red\">&nbsp;&nbsp;<b>".$lang[2699]." !</b></TD>";
                                    }
                                }

                                if(!$isview){
                                    echo "<TD ALIGN=\"CENTER\"><i class=\"lmb-icon lmb-colors\"  TITLE=\"$lang[2567]\"></i></TD>";
                                    echo "<TD ALIGN=\"CENTER\" TITLE=\"$lang[2568]\"><B>$lang[1614]</B></TD>";
                                    echo "<TD ALIGN=\"CENTER\" OnCLick=\"set_searchrule('$gtabid')\" STYLE=\"cursor:pointer\" TITLE=\"$lang[2569]\"><B><U>$lang[2569]</U></B></TD>";
                                    echo "<TD ALIGN=\"CENTER\" TITLE=\"$lang[2570]\"><B>$lang[2570]</B></TD>";
                                    if($gtrigger[$gtabid]){echo "<TD ALIGN=\"CENTER\"><B>".$lang[1987]."</B></TD>";}
                                    echo "<TD ALIGN=\"LEFT\" TITLE=\"$lang[2572]\"><B>&nbsp;".$lang[1563]."</B></TD>";
                                    echo "<TD ALIGN=\"LEFT\"><B>&nbsp;$lang[1986]</B></TD>";
                                }
                                echo "</TR>";

                                if($f_result[$gtabid]["field_id"]){
                                    foreach($f_result[$gtabid]["field_id"] as $value => $key){
                                        echo "<TR>";

                                        echo "<TD style=\"height:20px;\">&nbsp;$key&nbsp;&nbsp;&nbsp;&nbsp;</TD>";
                                        echo "<TD TITLE=\"".$f_result[$gtabid]["beschreibung"][$key]."\" nowrap>".$f_result[$gtabid]['field'][$key]."&nbsp;(".$key.")</TD>";
                                        echo "<TD STYLE=\"font-size:9;\" COLSPAN=\"2\" nowrap>".$f_result[$gtabid]["typ"][$key]."</TD>";

                                        # --- view ----
                                        echo "<TD ALIGN=\"RIGHT\">";
                                        if($l_result[$gtabid]["view"][$key] OR !$l_result){
                                            echo "<INPUT TYPE=\"checkbox\" NAME=\"viewrule_".$gtabid."_".$f_result[$gtabid]["field_id"][$key]."\" Onclick=\"save_rules('$gtabid','".$f_result[$gtabid]["field_id"][$key]."',1)\" ";
                                            if($f_result[$gtabid]["view"][$key]){echo "CHECKED";}
                                            echo ">";
                                            echo "</TD>";
                                        }else{
                                            echo "<INPUT TYPE=\"checkbox\" readonly disabled style=\"opacity:0.3;filter:Alpha(opacity=30);\">";
                                            echo "</TD></TR>";
                                            continue;
                                        }

                                        # --- edit ----
                                        if(($s_result[$gtabid]["edit"][$key] OR $session["superadmin"]) AND $f_result[$gtabid]["field_type"][$key] < 100){
                                            if(!$isview){
                                                echo "<TD ALIGN=\"RIGHT\">";
                                                if($l_result[$gtabid]["edit"][$key] OR !$l_result){
                                                    echo "<INPUT TYPE=\"checkbox\" NAME=\"edit_".$gtabid."_".$f_result[$gtabid]["field_id"][$key]."\" Onclick=\"save_rules('$gtabid','".$f_result[$gtabid]['field_id'][$key]."',2)\" ";
                                                    if($f_result[$gtabid]["edit"][$key]){echo "CHECKED";}
                                                    echo ">";
                                                }else{
                                                    echo "<INPUT TYPE=\"checkbox\" readonly disabled style=\"opacity:0.3;filter:Alpha(opacity=30);\">";
                                                }
                                                echo "</TD>";
                                            }
                                        }else{
                                            echo "</TR>";
                                            continue;
                                        }

                                        # --- need ----
                                        if(!$isview){
                                            echo "<TD WIDTH=\"40\" ALIGN=\"RIGHT\">";
                                            if($f_result[$gtabid]["field_type"][$key] < 100 AND $f_result[$gtabid]["data_type"][$key] != 22 AND $f_result[$gtabid]["field_type"][$key] != 14 AND $f_result[$gtabid]["field_type"][$key] != 15 AND $f_result[$gtabid]["field_type"][$key] != 16 AND $f_result[$gtabid]["field_type"][$key] != 19 AND $f_result[$gtabid]["field_type"][$key] != 6 AND $f_result[$gtabid]["field_type"][$key] != 9 AND $f_result[$gtabid]["field_type"][$key] != 17 AND $f_result[$gtabid]["field_type"][$key] != 20 AND !$f_result[$gtabid]["argument_typ"][$key]){
                                                if($l_result[$gtabid]["need"][$key]){
                                                    echo "<INPUT TYPE=\"checkbox\" readonly disabled checked style=\"opacity:0.3;filter:Alpha(opacity=30);\"></TD>";
                                                }else{
                                                    echo "<INPUT TYPE=\"checkbox\" NAME=\"needrule_".$gtabid."_".$f_result[$gtabid]['field_id'][$key]."\" Onclick=\"save_rules('$gtabid','".$f_result[$gtabid]['field_id'][$key]."',9)\" ";
                                                    if($f_result[$gtabid]["need"][$key] == 1){echo "CHECKED";}
                                                    echo ">";
                                                }
                                            }
                                            echo "</TD>";
                                        }

                                        if(!$isview){
                                            echo "<TD ALIGN=\"RIGHT\">";
                                            # --- copy ----
                                            if($f_result[$gtabid]["data_type"][$key] != 22 AND $f_result[$gtabid]["field_type"][$key] != 14 AND $f_result[$gtabid]["field_type"][$key] != 15 AND $f_result[$gtabid]["field_type"][$key] < 100){
                                                if($l_result[$gtabid]["copy"][$key] OR !$l_result){
                                                    echo "<INPUT TYPE=\"checkbox\" NAME=\"copyrule_".$gtabid."_".$f_result[$gtabid]["field_id"][$key]."\" Onclick=\"save_rules('$gtabid','".$f_result[$gtabid]["field_id"][$key]."',13)\" ";
                                                    if($f_result[$gtabid]["copy"][$key]){echo "CHECKED";}
                                                    echo ">";
                                                }else{echo "<INPUT TYPE=\"checkbox\" readonly disabled style=\"opacity:0.3;filter:Alpha(opacity=30);\">";}
                                            }
                                            echo "</TD>";
                                        }

                                        if(!$isview) {
                                            echo "<TD ALIGN=\"RIGHT\">";
                                            # --- list edit ----
                                            $noListEdit = array(10, 18, 22, 31, 32, 34, 35, 36, 37, 39, 45, 46);
                                            if (!in_array($f_result[$gtabid]["data_type"][$key], $noListEdit)) {
                                                if ($l_result[$gtabid]["listedit"][$key] OR !$l_result) {
                                                    echo "<INPUT TYPE=\"checkbox\" STYLE=\"border:none;\" NAME=\"listeditrule_" . $gtabid . "_" . $f_result[$gtabid]["field_id"][$key] . "\" Onclick=\"save_rules('$gtabid','" . $f_result[$gtabid]["field_id"][$key] . "',33)\" ";
                                                    if ($f_result[$gtabid]["listedit"][$key]) {
                                                        echo "CHECKED";
                                                    }
                                                    echo ">";
                                                } else {
                                                    echo "<INPUT TYPE=\"checkbox\" readonly disabled style=\"opacity:0.3;filter:Alpha(opacity=30);\">";
                                                }
                                            }
                                            echo "</TD>";
                                        }

                                        echo "<TD ALIGN=\"RIGHT\">";
                                        # --- option ----
                                        if($f_result[$gtabid]["field_type"][$key] == 2 OR $f_result[$gtabid]["field_type"][$key] == 21 OR $f_result[$gtabid]["data_type"][$key] == 42 OR $f_result[$gtabid]["data_type"][$key] == 30 OR $f_result[$gtabid]["data_type"][$key] == 28 OR $f_result[$gtabid]["data_type"][$key] == 29 OR $f_result[$gtabid]["field_type"][$key] == 4 OR $f_result[$gtabid]["field_type"][$key] == 11 OR $f_result[$gtabid]["field_type"][$key] == 19){
                                            if($l_result[$gtabid]["option"][$key] OR !$l_result){
                                                echo "<INPUT TYPE=\"checkbox\" NAME=\"optionrule_".$gtabid."_".$f_result[$gtabid]["field_id"][$key]."\" Onclick=\"save_rules('$gtabid','".$f_result[$gtabid]["field_id"][$key]."',32)\" ";
                                                if($f_result[$gtabid]["option"][$key]){echo "CHECKED";}
                                                echo ">";
                                            }else{echo "<INPUT TYPE=\"checkbox\" readonly disabled style=\"opacity:0.3;filter:Alpha(opacity=30);\">";}
                                        }
                                        echo "</TD>";

                                        if(!$isview) {
                                            echo "<TD ALIGN=\"RIGHT\">";
                                            # --- speech recognition ----
                                            if (in_array($f_result[$gtabid]["data_type"][$key], array(1 /* add more here */))) {
                                                if ($l_result[$gtabid]["speechrec"][$key] OR !$l_result) {
                                                    echo "<INPUT TYPE=\"checkbox\" STYLE=\"border:none;\" NAME=\"speechrecrule_" . $gtabid . "_" . $f_result[$gtabid]["field_id"][$key] . "\" Onclick=\"save_rules('$gtabid','" . $f_result[$gtabid]["field_id"][$key] . "',34)\" ";
                                                    if ($f_result[$gtabid]["speechrec"][$key]) {
                                                        echo "CHECKED";
                                                    }
                                                    echo ">";
                                                } else {
                                                    echo "<INPUT TYPE=\"checkbox\" readonly disabled style=\"opacity:0.3;filter:Alpha(opacity=30);\">";
                                                }
                                            }
                                            echo "</TD>";
                                        }

                                        # --- versioning ----
                                        if(!$isview AND $gtab["versioning"][$gtabid] AND $f_result[$gtabid]["versioning_type"] == 2){
                                            echo "<TD WIDTH=\"40\" ALIGN=\"RIGHT\">";
                                            if($f_result[$gtabid]["data_type"][$key] != 22 AND $f_result[$gtabid]['field_type'][$key] != 14 AND $f_result[$gtabid]["field_type"][$key] != 15 AND $f_result[$gtabid]['field_type'][$key] < 100){
                                                #if($l_result[$gtabid]["versionable"][$key]){
                                                #	echo "<INPUT TYPE=\"checkbox\" readonly disabled checked style=\"opacity:0.3;filter:Alpha(opacity=30);\"></TD>";
                                                #}else{
                                                echo "<INPUT TYPE=\"checkbox\"  NAME=\"versionrule_".$gtabid."_".$f_result[$gtabid]["field_id"][$key]."\" Onclick=\"save_rules('$gtabid','".$f_result[$gtabid]["field_id"][$key]."',16)\" ";
                                                if($f_result[$gtabid]["versionable"][$key] == 1){echo "CHECKED";}
                                                echo ">";
                                                #}
                                            }
                                            echo "</TD>";
                                        }

                                        # --- color ----
                                        echo "<TD STYLE=\"width:20px;cursor:pointer;border:none\">";
                                        echo "<DIV ID=\"color_".$gtabid."_".$f_result[$gtabid]["field_id"][$key]."\" OnClick=\"div4(this,'".$gtabid."','".$f_result[$gtabid]["field_id"][$key]."')\" STYLE=\"border:1px solid ".$farbschema["WEB3"].";width:15px;height:15px;";
                                        if($f_result[$gtabid]["color"][$key]){echo "background-color:".$f_result[$gtabid]['color'][$key].";";}
                                        echo "\">&nbsp;</DIV></TD>";

                                        # --- default ----
                                        if(!$isview){
                                            if($f_result[$gtabid]["field_type"][$key] < 100){
                                                echo "<TD><INPUT TYPE=\"TEXT\" NAME=\"filterdefault_".$gtabid."_".$f_result[$gtabid]['field_id'][$key]."\" VALUE=\"".htmlentities($f_result[$gtabid]["def"][$key],ENT_QUOTES,$GLOBALS["umgvar"]["charset"])."\" OnChange=\"save_rules('$gtabid','".$f_result[$gtabid]['field_id'][$key]."',12)\" STYLE=\"width:100px\"></TD>";
                                            }else{
                                                echo "<td></td>";
                                            }
                                        }

                                        # ----------- Filter ---------------
                                        if($f_result[$gtabid]["filtertyp"][$key] == 1){$st = "style=\"color:red;width:100px;cursor:pointer;\" title=\"use automatic filterrule setted fom filter\"";}else{$st = "style=\"width:100px;cursor:pointer;\" title=\"use manual filterrule [ = '\$abc' ] or [ < 23 ]\"";}
                                        # --------------------- Hiddenfeld -------------------
                                        echo "<TD><INPUT TYPE=\"HIDDEN\" $st NAME=\"filterprev_".$gtabid."_".$f_result[$gtabid]['field_id'][$key]."\" VALUE=\"";
                                        for($i = 0; $i <= 2; $i++){
                                            if($gsr[$gtabid][$f_result[$gtabid]['field_id'][$key]][$i] OR $gsr[$gtabid][$f_result[$gtabid]['field_id'][$key]][$i] == "0"){
                                                if($searchlang['andor'][$gsr[$gtabid][$f_result[$gtabid]['field_id'][$key]]['andor'][$i]]){echo " ".htmlentities($searchlang["andor"][$gsr[$gtabid][$f_result[$gtabid]["field_id"][$key]]["andor"][$i]],ENT_QUOTES,$GLOBALS["umgvar"]["charset"])." ";}
                                                echo "(";
                                                if($searchlang['num'][$gsr[$gtabid][$f_result[$gtabid]['field_id'][$key]]['num'][$i]]){echo htmlentities($searchlang["num"][$gsr[$gtabid][$f_result[$gtabid]["field_id"][$key]]["num"][$i]],ENT_QUOTES,$GLOBALS["umgvar"]["charset"])." ";}
                                                echo "'".htmlentities($gsr[$gtabid][$f_result[$gtabid]["field_id"][$key]][$i],ENT_QUOTES,$GLOBALS["umgvar"]["charset"])."'";
                                                if($searchlang['txt'][$gsr[$gtabid][$f_result[$gtabid]['field_id'][$key]]['txt'][$i]]){echo " ".htmlentities($searchlang["txt"][$gsr[$gtabid][$f_result[$gtabid]["field_id"][$key]]["txt"][$i]],ENT_QUOTES,$GLOBALS["umgvar"]["charset"]);}
                                                if($searchlang['cs'][$gsr[$gtabid][$f_result[$gtabid]['field_id'][$key]]['cs'][$i]]){echo " ".htmlentities($searchlang["cs"][$gsr[$gtabid][$f_result[$gtabid]["field_id"][$key]]["cs"][$i]],ENT_QUOTES,$GLOBALS["umgvar"]["charset"]);}
                                                echo ")";
                                            }
                                        }
                                        # --------------------- Inputfeld -------------------
                                        echo "\"><INPUT $st OnClick=\"div3(this,'".$gtabid."_".$f_result[$gtabid]["field_id"][$key]."','".$gtabid."','".$f_result[$gtabid]['field_id'][$key]."')\" TYPE=\"TEXT\" NAME=\"filterrule_".$gtabid."_".$f_result[$gtabid]['field_id'][$key]."\" VALUE=\"";
                                        $gsh[$gtabid][$f_result[$gtabid]["field_id"][$key]] = unserialize($f_result[$gtabid]["filter"][$key]);
                                        if($gsh[$gtabid][$f_result[$gtabid]["field_id"][$key]]){
                                            for($i = 0; $i <= 2; $i++){
                                                if($gsh[$gtabid][$f_result[$gtabid]['field_id'][$key]][$i] OR $gsh[$gtabid][$f_result[$gtabid]['field_id'][$key]][$i] == "0"){
                                                    if($searchlang['andor'][$gsh[$gtabid][$f_result[$gtabid]['field_id'][$key]]['andor'][$i]]){echo " ".htmlentities($searchlang["andor"][$gsh[$gtabid][$f_result[$gtabid]["field_id"][$key]]["andor"][$i]],ENT_QUOTES,$GLOBALS["umgvar"]["charset"])." ";}
                                                    echo "(";
                                                    if($searchlang['num'][$gsh[$gtabid][$f_result[$gtabid]['field_id'][$key]]['num'][$i]]){echo htmlentities($searchlang["num"][$gsh[$gtabid][$f_result[$gtabid]["field_id"][$key]]["num"][$i]],ENT_QUOTES,$GLOBALS["umgvar"]["charset"])." ";}
                                                    echo "'".htmlentities($gsh[$gtabid][$f_result[$gtabid]["field_id"][$key]][$i],ENT_QUOTES,$GLOBALS["umgvar"]["charset"])."'";
                                                    if($searchlang['txt'][$gsh[$gtabid][$f_result[$gtabid]['field_id'][$key]]['txt'][$i]]){echo " ".htmlentities($searchlang["txt"][$gsh[$gtabid][$f_result[$gtabid]["field_id"][$key]]["txt"][$i]],ENT_QUOTES,$GLOBALS["umgvar"]["charset"]);}
                                                    if($searchlang['cs'][$gsh[$gtabid][$f_result[$gtabid]['field_id'][$key]]['cs'][$i]]){echo " ".htmlentities($searchlang["cs"][$gsh[$gtabid][$f_result[$gtabid]["field_id"][$key]]["cs"][$i]],ENT_QUOTES,$GLOBALS["umgvar"]["charset"]);}
                                                    echo ")";
                                                }
                                            }
                                        }else{
                                            echo htmlentities($f_result[$gtabid]["filter"][$key],ENT_QUOTES,$GLOBALS["umgvar"]["charset"]);
                                        }
                                        echo "\" READONLY></TD>";




                                        if(!$isview){
                                            # ----------- Editrules ---------------
                                            echo "<TD><INPUT TYPE=\"TEXT\" OnClick=\"div5(this,'editrule_".$gtabid."_".$f_result[$gtabid]["field_id"][$key]."','".$gtabid."','".$f_result[$gtabid]["field_id"][$key]."')\" NAME=\"editrule_".$gtabid."_".$f_result[$gtabid]["field_id"][$key]."\" VALUE=\"".$f_result[$gtabid]["editrule"][$key]."\" STYLE=\"width:100px;cursor:pointer;\" READONLY></TD>";

                                            # --- trigger ----
                                            if($gtrigger[$gtabid]){
                                                echo "	<TD>";
                                                echo "
					   	<SELECT NAME=\"triggerrule_".$gtabid."_".$key."[]\" STYLE=\"width:120px;\" onchange=\"save_rules('$gtabid','".$key."',14)\"><OPTION VALUE=\"\">";
                                                $trlist = array();
                                                foreach($gtrigger[$gtabid]["id"] as $trid => $trval){
                                                    if($gtrigger[$gtabid]["type"][$trid] == "UPDATE"){
                                                        if(in_array($trid,$f_result[$gtabid]["field_trigger"][$key])){$SELECTED = "SELECTED";$trlist[] = $gtrigger[$gtabid]["trigger_name"][$trid];}else{$SELECTED = "";}
                                                        echo "<OPTION VALUE=\"".$trid."\" $SELECTED>".$gtrigger[$gtabid]["trigger_name"][$trid]." (".$gtrigger[$gtabid]["type"][$trid].")</OPTION>";
                                                    }
                                                }
                                                echo "</SELECT>
						</TD>";
                                            }
                                        }

                                        # --- number-format ----
                                        if($f_result[$gtabid]['field_type'][$key] == 5 OR $f_result[$gtabid]['field_type'][$key] == 2){
                                            echo "<TD NOWRAP><TABLE border=\"0\" cellspacing=\"0\" cellpadding=\"0\"><TR>";
                                            echo "<TD NOWRAP>&nbsp;<INPUT TYPE=\"TEXT\" STYLE=\"width:50px;\" VALUE=\"".$f_result[$gtabid]["nformat"][$key]."\" NAME=\"filterformat_".$gtabid."_".$f_result[$gtabid]['field_id'][$key]."\" onchange=\"save_rules('$gtabid','".$f_result[$gtabid]['field_id'][$key]."',10)\">&nbsp;";

                                            # ---- Währung ----------
                                            if($f_result[$gtabid]['data_type'][$key] == 30){
                                                echo "<SELECT STYLE=\"width:100px;\" NAME=\"filtercurrency_".$gtabid."_".$f_result[$gtabid]["field_id"][$key]."\" onchange=\"save_rules('$gtabid','".$f_result[$gtabid]['field_id'][$key]."',11)\"><OPTION VALUE=\"\">";
                                                asort($lmcurrency['currency']);
                                                foreach($lmcurrency['currency'] as $ckey => $cval){
                                                    if($lmcurrency['code'][$ckey] == $f_result[$gtabid]["currency"][$key]){$sel = "SELECTED";}else{$sel = "";}
                                                    echo "<OPTION VALUE=\"".$lmcurrency['code'][$ckey]."\" $sel>".$lmcurrency['currency'][$ckey];
                                                }
                                            }
                                            echo "</TD></TR></TABLE></TD>";
                                        }else{
                                            echo "<TD ALIGN=\"left\"></TD>";
                                        }


                                        # --- extension-type ----
                                        echo "<TD>";
                                        echo "<SELECT STYLE=\"\" NAME=\"filterextension_".$gtabid."_".$f_result[$gtabid]["field_id"][$key]."\" onchange=\"save_rules('$gtabid','".$f_result[$gtabid]["field_id"][$key]."',15)\"><OPTION VALUE=\" \">";
                                        foreach ($ext_fk as $key1 => $value1){
                                            echo "<OPTION VALUE=\"$value1\" ";
                                            if($f_result[$gtabid]["ext_type"][$key] == $value1){echo "SELECTED";}
                                            echo ">".$value1."\n";
                                        }
                                        echo "</SELECT>";
                                        echo "</TD>";

                                        echo "</TR>";

                                    }
                                }

                                echo "</TD></TR></TABLE>";
                            }


                            foreach($_tabgroup['id'] as $bzm => $val) {
                            $icon = '';
                            if ($iconClass = $_tabgroup['icon'][$bzm]) {
                                $icon = "<i class=\"lmb-icon $iconClass\"></i>&nbsp;";
                            }
                            echo "<TR><TD colspan=\"10\">$icon<b><i>".$_tabgroup['name'][$bzm]." (".$_tabgroup['beschreibung'][$bzm].")</i></b></TD></TR>";
                            echo "<TR><TD ALIGN=\"LEFT\">";
                            foreach($_gtab["tab_id"] as $key => $value){

                            if($_gtab["typ"][$key] == 5){$isview = 1;}else{$isview = 0;}
                            if($_gtab["tab_group"][$key] == $_tabgroup["id"][$bzm]){

                            if((!is_array($s_result[$key]["view"]) || !in_array('1',$s_result[$key]["view"])) && $session["user_id"] != 1 && !$session["superadmin"])
                            {
                                continue;
                            }

                            $icon = 'plusonly';
                            if($is_popup AND in_array($key,$is_popup)){$icon = 'minusonly';}else{$icon = 'plusonly';}
                            ?>

                            <TR>
                                <TD width="20" align="left"><IMG SRC="assets/images/legacy/outliner/<?=$icon?>.gif" tabid="<?=$key?>" CLASS="popicon" NAME="popicon_<?=$key?>" BORDER="0" STYLE="cursor:pointer" OnClick="pops('<?=$key?>')"></TD>
                                <TD width="300" align="left"><FONT><?=$_gtab['table'][$key]?> (<?=$key?>)&nbsp;</TD>


                                <?php if(!$s_result[$key]["hidemenu"] OR $session["superadmin"]){?>

                                <TD width="100" nowrap>

                                    <?php if(!$isview){?>
                                        <table><tr>
                                                <td nowrap><i class="lmb-icon lmb-pencil"></i><TEXTAREA NAME="edit_rule_<?=$key?>" readonly style="width:100px;height:17px;cursor:pointer;overflow:hidden;" OnClick="div6(this,'<?=$key?>')" title="<?=$lang[2573]?>"><?=$f_result[$key]["tabeditrule"]?></TEXTAREA></td>
                                                <td nowrap><i class="lmb-icon lmb-indicator-rule"></i><TEXTAREA NAME="indicator_rule_<?=$key?>" readonly style="width:100px;height:17px;cursor:pointer;overflow:hidden;" OnClick="div1(this,'<?=$key?>')" title="<?=$lang[1255]?>"><?=$f_result[$key]["indicator"]?></TEXTAREA></td>
                                                <td nowrap><i class="lmb-icon lmb-textsort-up"></i><TEXTAREA NAME="orderby_value_<?=$key?>" readonly style="width:100px;height:17px;cursor:pointer;overflow:hidden;" OnClick="div7(this,'<?=$key?>')" title="<?=$lang[1837]?>"><?=$f_result[$key]["orderby"]?></TEXTAREA></td>
                                            </tr></table>
                                    <?php }?>

                                </TD><TD nowrap>

                                    <?php
                                    # form selection
                                    $sqlquery = "SELECT ID,NAME,FORM_TYP FROM LMB_FORM_LIST WHERE REFERENZ_TAB = '".$_gtab["tab_id"][$key]."'";
                                    $rs1 = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
                                    $form = null;
                                    while(lmbdb_fetch_row($rs1)){
                                        $id = lmbdb_result($rs1, 'ID');
                                        $form['name'][$id] = lmbdb_result($rs1, 'NAME');
                                        $form['typ'][$id] = lmbdb_result($rs1, 'FORM_TYP');
                                    }

                                    if($form){
                                        echo "<i class=\"lmb-icon-cus lmb-form-alt\" title=\"".$lang[1169]."\"></i>&nbsp<SELECT NAME=\"view_form_".$key."\" STYLE=\"width:100px\" OnChange=\"save_rules('$key','',22)\"><OPTION VALUE=\"0\">default";
                                        foreach($form['name'] as $fid => $_value){
                                            if($form['typ'][$fid] == 1) {
                                                if ($f_result[$key]["view_form"] == $fid) {
                                                    $SELECTED = "SELECTED";
                                                } else {
                                                    $SELECTED = "";
                                                }
                                                echo "<OPTION VALUE=\"" . $fid . "\" $SELECTED>" . $form['name'][$fid];
                                            }
                                        }
                                        echo "</SELECT>&nbsp;";

                                        //tablelist form selection
                                        echo "<i class=\"lmb-icon lmb-icon-cus lmb-list-edit\" align=\"absbottom\" title=\"".$lang[2756]."\"></i>&nbsp<SELECT NAME=\"view_lform_".$key."\" OnChange=\"save_rules('$key','',24)\" STYLE=\"width:100px\"><OPTION VALUE=\"0\">none";
                                        foreach($form['name'] as $fid => $_value){
                                            if($f_result[$key]["view_lform"] == $fid){$SELECTED = "SELECTED";}else{$SELECTED = "";}
                                            echo "<OPTION VALUE=\"".$fid."\" $SELECTED>".$form['name'][$fid];
                                        }
                                        echo "</SELECT>&nbsp";


                                        //calendar form selection
                                        if($_gtab["typ"][$key] == 2){
                                            echo "<i class=\"lmb-icon lmb-calendar\" align=\"absbottom\" title=\"".$lang[1929]." ".$lang[2574]."\"></i>&nbsp<SELECT NAME=\"view_tform_".$key."\" OnChange=\"save_rules('$key','',23)\" STYLE=\"width:100px\"><OPTION VALUE=\"0\">default";
                                            foreach($form['name'] as $fid => $_value){
                                                if($f_result[$key]["view_tform"] == $fid){$SELECTED = "SELECTED";}else{$SELECTED = "";}
                                                echo "<OPTION VALUE=\"".$fid."\" $SELECTED>".$form['name'][$fid];
                                            }
                                            echo "</SELECT>&nbsp";
                                        }

                                        //kanban form selection
                                        if($_gtab["typ"][$key] == 7){
                                            echo "<i class=\"lmb-icon lmb-columns\" align=\"absbottom\" title=\"kanban ".$lang[2574]."\"></i>&nbsp<SELECT NAME=\"view_tform_".$key."\" OnChange=\"save_rules('$key','',23)\" STYLE=\"width:100px\"><OPTION VALUE=\"0\">default";
                                            $bzm1 = 1;
                                            foreach($form['name'] as $fid => $_value){
                                                if($f_result[$key]["view_tform"] == $fid){$SELECTED = "SELECTED";}else{$SELECTED = "";}
                                                echo "<OPTION VALUE=\"".$fid."\" $SELECTED>".$form['name'][$fid];
                                            }
                                            echo "</SELECT>&nbsp";
                                        }
                                    }

                                    # Versioning Type
                                    if($gtab["versioning"][$key] AND !$isview){
                                        echo "<i class=\"lmb-icon lmb-versioning-type\" title=\"".$lang[2565]."\"></i>&nbsp<SELECT NAME=\"versioning_type_".$key."\" OnChange=\"save_rules('$key','',25)\" STYLE=\"width:100px\"><OPTION VALUE=\"0\">";
                                        echo "<OPTION VALUE=\"1\" ";if($f_result[$key]["versioning_type"] == 1){echo "SELECTED";}echo ">".$lang[2144];
                                        echo "<OPTION VALUE=\"2\" ";if($f_result[$key]["versioning_type"] == 2){echo "SELECTED";}echo ">".$lang[2145];
                                        echo "</SELECT>&nbsp;";
                                    }

                                    if(!$isview){
                                        if($LINK[226] AND $gtrigger[$value]){
                                            echo "<i class=\"lmb-icon lmb-database\" ALIGN=\"absbottom\" TITLE=\"trigger\" OnClick=\"activ_menu=1;document.getElementById('tab_trigger_$key').style.display=''\"></i>&nbsp;
                    <SPAN STYLE=\"display:none;position:absolute\" ID=\"tab_trigger_$key\" OnClick=\"activ_menu=1\">
                    <SELECT NAME=\"tab_trigger_".$key."[]\" OnChange=\"save_rules('$key','',26)\" STYLE=\"width:200px;\" MULTIPLE SIZE=\"5\"><OPTION VALUE=\"\">";
                                            $trlist = array();
                                            foreach($gtrigger[$value]["id"] as $trid => $trval){
                                                if(in_array($trid,$f_result[$key]["tab_trigger"])){$SELECTED = "SELECTED";$trlist[] = $gtrigger[$value]["trigger_name"][$trid];}else{$SELECTED = "";}
                                                echo "<OPTION VALUE=\"".$trid."\" $SELECTED>".$gtrigger[$value]["trigger_name"][$trid]." (".$gtrigger[$value]["type"][$trid].")</OPTION>";
                                            }
                                            echo "</SELECT>
                    </SPAN>
                    <INPUT TYPE=\"TEXT\" STYLE=\"width:100px;\" VALUE=\"".implode(";",$trlist)."\" OnClick=\"activ_menu=1;document.getElementById('tab_trigger_$key').style.display=''\">";
                                        }
                                    }

                                    echo "</TD>";

                                    }else{
                                        echo "<td>&nbsp;</td>";
                                    }

                                    echo "</TR>";


                                    viewrows($key);



                                    }
                                    }
                                    echo "</TD></TR>";
                                    }

                            
                                    ?>
                                    

                            <TR><TD colspan="10" class="tabFooter">&nbsp;</TD></TR>
                        </TABLE>
                    </div>

                    <?php
                    $useSubmitJavascript = true;
                    require __DIR__ . '/submit-footer.php'; ?>
                    
                </div>
            </div>
        </div>
    </form>
</div>
    
