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
 * ID:231
 */
?>

<?php

if(!$tab){$tab = 1;}


function lmb_custmenuDetail(&$custmenu,$params){
    global $lang;
    global $tabgroup;
    global $gtab;

    $id = $params['id'];
    $tab = $params['tab'];
    $linkid = $params['linkid'];


    // tab 1 = Mainmenu
    // tab 2 = contextmenu

    if($linkid >= 1000){
        $tab = 1;
    }else{
        $tab = 2;
    }

    if($custmenu['typ'][$id]){$disabled = 'disabled style="opacity:0.6;width:100%"';}else{$disabled = 'style="width:100%"';}

    echo "
    <form name=\"form11\" method=\"get\">
    <table width=\"100%\"  cellpadding=4 cellspacing=2>";

    ${'typ'.$custmenu['typ'][$id]} = "SELECTED";

    echo "<tr><td>Typ</td><td>
    <select onchange=\"lmb_loadMenueDetail($id,'update')\" id=\"menutyp_$parent\" name=\"menudetail[typ]\" $disabled><option>";

    if($tab == 1) {
        echo "
        <option value=\"1\" $typ1>{$lang[2144]}
        <option value=\"2\" $typ2>{$lang[577]}
        <option value=\"3\" $typ3>{$lang[2608]}
        <option value=\"4\" $typ4>{$lang[1137]}
        <option value=\"5\" $typ5>{$lang[1179]}
        <option value=\"6\" $typ6>{$lang[2119]}
        <option value=\"7\" $typ7>{$lang[573]}
        ";
    }elseif($tab == 2){
        echo "
        <option value=\"8\" $typ8>{$lang[2144]}
        <option value=\"9\" $typ9>{$lang[573]}
        <option value=\"10\" $typ10>{$lang[1179]}
        <option value=\"11\" $typ11>{$lang[1137]}
        <option value=\"12\" $typ12>{$lang[2985]}
        <option value=\"13\" $typ13>{$lang[2625]}
        ";
    }



    echo "</select></td></tr>
    <tr><td colspan=\"2\"><hr></td></tr>";


    ############################## main menu ###############################

    // manuell
    if($custmenu['typ'][$id] == 1) {
        echo "<tr><td>Name</td><td><input type=\"text\" name=\"menudetail[name]\" style=\"width:100%\" value=\"".$lang[$custmenu['name'][$id]]."\"></td></tr>";
        echo "<tr><td>Title</td><td><input type=\"text\" name=\"menudetail[title]\" style=\"width:100%\" value=\"".$lang[$custmenu['title'][$id]]."\"></td></tr>";
        echo "<tr><td>URL</td><td><input type=\"text\" name=\"menudetail[url]\" style=\"width:100%\" value=\"".$custmenu['url'][$id]."\"></td></tr>";
        echo "<tr><td>ICON</td><td><input type=\"text\" name=\"menudetail[icon]\" style=\"width:100%\" value=\"".$custmenu['icon'][$id]."\"></td></tr>";
        echo "<tr><td>{$lang[ 1107]}</td><td><input type=\"text\" name=\"menudetail[bg]\" style=\"width:100%\" value=\"".$custmenu['bg'][$id]."\"></td></tr>";
    }
    // tables
    elseif($custmenu['typ'][$id] == 2) {

        echo "<tr><td valign=\"top\" style=\"width:100px;\">".$lang[577]."</td><td valign=\"top\">
        <input class=\"gtabHeaderInputINP\"  id=\"gdquicksearch\" style=\"width:100%;height:20px;\" onclick=\"lmb_searchDropdown(this,'gdsearchfield',15);\">
        <select id=\"gdsearchfield\" name=\"menudetail[name]\" style=\"width: 100%;max-height:200px\"><option value=\"0\">";

        foreach ($tabgroup['id'] as $groupKey => $groupID) {
            $tabgroupOptions = '';
            foreach ($gtab['tab_id'] as $tabKey => $tabID) {
                if($gtab['tab_group'][$tabKey] == $groupID){
                    if($custmenu['name'][$id] == $tabKey){$SELECTED = 'SELECTED';}else{$SELECTED = '';}
                    $tabgroupOptions .= "<option value=\"{$tabID}\" $SELECTED>{$gtab['desc'][$tabKey]}</option>";
                }
            }
            if ($tabgroupOptions) {
                echo "<optgroup label=\"{$tabgroup['name'][$groupKey]}\">{$tabgroupOptions}</optgroup>";
            }
        }
        echo "</select><br>
        
        </td></tr>
        ";
        #echo "<tr><td>icon</td><td><input type=\"text\" name=\"menudetail[icon]\" style=\"width:100%\" value=\"".$custmenu['icon'][$id]."\"></td></tr>";

    }

    // snapshot
    elseif($custmenu['typ'][$id] == 3) {
        global $gsnap;

        echo "<tr><td valign=\"top\" style=\"width:100px;\">".$lang[2608]."</td><td valign=\"top\">
        <input class=\"gtabHeaderInputINP\" id=\"gdquicksearch\" style=\"width:100%;height:20px;\" onclick=\"lmb_searchDropdown(this,'gdsearchfield',15);\">
        <select id=\"gdsearchfield\" name=\"menudetail[name]\" style=\"width: 100%;max-height:200px\"><option value=\"0\">";

        foreach ($gsnap as $tableid => $snapgroup) {
            $tabgroupOptions = '';
            foreach ($snapgroup["id"] as $snapkey => $snapid) {
                #if($gsnap[$tableid]["glob"][$snapkey]) {
                    if($custmenu['name'][$id] == $snapkey){$SELECTED = 'SELECTED';}else{$SELECTED = '';}
                    $tabgroupOptions .= "<option value=\"{$snapid}\" $SELECTED>{$snapgroup["name"][$snapkey]}</option>";
                #}
            }
            if ($tabgroupOptions) {
                echo "<optgroup label=\"{$gtab['desc'][$tableid]}\">{$tabgroupOptions}</optgroup>";
            }
        }
        echo "</select>
        </td></tr>
        ";
        #echo "<tr><td>icon</td><td><input type=\"text\" name=\"menudetail[icon]\" style=\"width:100%\" value=\"".$custmenu['icon'][$id]."\"></td></tr>";

    }
    // reports
    elseif($custmenu['typ'][$id] == 4  OR $custmenu['typ'][$id] == 11) {
        global $greportlist;

        echo "<tr><td valign=\"top\" style=\"width:100px;\">".$lang[1137]."</td><td valign=\"top\">
        <input class=\"gtabHeaderInputINP\" id=\"gdquicksearch\" style=\"width:100%;height:20px;\" onclick=\"lmb_searchDropdown(this,'gdsearchfield',15);\">
        <select id=\"gdsearchfield\" name=\"menudetail[name]\" style=\"width: 100%;max-height:200px\"><option value=\"0\">";

        foreach ($greportlist as $tableid => $reportgroup) {
            $tabgroupOptions = '';
            foreach ($reportgroup["id"] as $reportkey => $reportid) {
                if($greportlist[$tableid]["listmode"][$reportkey]) {
                    if($custmenu['name'][$id] == $reportkey){$SELECTED = 'SELECTED';}else{$SELECTED = '';}
                    $tabgroupOptions .= "<option value=\"{$reportid}\" $SELECTED>{$reportgroup["name"][$reportkey]}</option>";
                }
            }
            if ($tabgroupOptions) {
                echo "<optgroup label=\"{$gtab['desc'][$tableid]}\">{$tabgroupOptions}</optgroup>";
            }
        }
        echo "</select>

        </td></tr>
        ";
        #echo "<tr><td>icon</td><td><input type=\"text\" name=\"menudetail[icon]\" style=\"width:100%\" value=\"".$custmenu['icon'][$id]."\"></td></tr>";
    }
    // forms
    elseif($custmenu['typ'][$id] == 5 OR $custmenu['typ'][$id] == 10) {
        global $gformlist;

        echo "<tr><td valign=\"top\" style=\"width:100px;\">".$lang[1179]."</td><td valign=\"top\">
        <input class=\"gtabHeaderInputINP\" id=\"gdquicksearch\" style=\"width:100%;height:20px;\" onclick=\"lmb_searchDropdown(this,'gdsearchfield',15);\">
        <select id=\"gdsearchfield\" name=\"menudetail[name]\" style=\"width: 100%;max-height:200px\"><option value=\"0\">";

        foreach ($gformlist as $tableid => $formgroup) {
            $tabgroupOptions = '';
            foreach ($formgroup["id"] as $formkey => $formid) {
                #if($gformlist[$tableid]["typ"][$formkey] == 1 OR $gformlist[$tableid]["extension"][$formkey]) {
                    if($custmenu['name'][$id] == $formkey){$SELECTED = 'SELECTED';}else{$SELECTED = '';}
                    $tabgroupOptions .= "<option value=\"{$formid}\" $SELECTED>{$formgroup["name"][$formkey]}</option>";
                #}
            }
            if ($tabgroupOptions) {
                echo "<optgroup label=\"{$gtab['desc'][$tableid]}\">{$tabgroupOptions}</optgroup>";
            }
        }
        echo "</select>
        </td></tr>
        ";

        if($custmenu['typ'][$id] == 10){
            echo "<tr><td>Attributes</td><td><input type=\"text\" name=\"menudetail[attribute]\" value=\"".$custmenu['url'][$id]."\"></td></tr>";
        }

        #echo "<tr><td>icon</td><td><input type=\"text\" name=\"menudetail[icon]\" style=\"width:100%\" value=\"".$custmenu['icon'][$id]."\"></td></tr>";
    }
    // diagramm
    elseif($custmenu['typ'][$id] == 6) {
        global $gdiaglist;

        echo "<tr><td valign=\"top\" style=\"width:100px;\">".$lang[2119]."</td><td valign=\"top\">
        <input class=\"gtabHeaderInputINP\" id=\"gdquicksearch\" style=\"width:100%;height:20px;\" onclick=\"lmb_searchDropdown(this,'gdsearchfield',15);\">
        <select id=\"gdsearchfield\" name=\"menudetail[name]\" style=\"width: 100%;max-height:200px\"><option value=\"0\">";

        foreach ($gdiaglist as $tableid => $formgroup) {
            $tabgroupOptions = '';
            foreach ($formgroup["id"] as $formkey => $formid) {
                if($custmenu['name'][$id] == $formkey){$SELECTED = 'SELECTED';}else{$SELECTED = '';}
                $tabgroupOptions .= "<option value=\"{$formid}\" $SELECTED>{$formgroup["name"][$formkey]}</option>";
            }
            if ($tabgroupOptions) {
                echo "<optgroup label=\"{$gtab['desc'][$tableid]}\">{$tabgroupOptions}</optgroup>";
            }
        }
        echo "</select>
        </td></tr>
        ";
        #echo "<tr><td>icon</td><td><input type=\"text\" name=\"menudetail[icon]\" style=\"width:100%\" value=\"".$custmenu['icon'][$id]."\"></td></tr>";
    }

    // System
    elseif($custmenu['typ'][$id] == 7) {
        global $LINK;

        # ------- Gruppenschema ----------
        $link_groupdesc[1][0] = $lang[1809];		#main
        $link_groupdesc[1][1] = "Frameset";		#frameset
        $link_groupdesc[1][2] = $lang[1809];	#main
        $link_groupdesc[1][3] = $lang[1872];	#info

        $link_groupdesc[2][0] = $lang[1810];		#admin
        $link_groupdesc[2][1] = $lang[$LINK['name'][58]];		#setup
        $link_groupdesc[2][2] = $lang[$LINK['name'][59]];		#tools
        $link_groupdesc[2][3] = $lang[$LINK['name'][54]];		#User/Gruppen
        $link_groupdesc[2][4] = $lang[$LINK['name'][56]];		#Tabellen
        $link_groupdesc[2][6] = $lang[$LINK['name'][107]];	#Formulare
        $link_groupdesc[2][8] = $lang[$LINK['name'][65]];		#Berichte
        $link_groupdesc[2][7] = $lang[$LINK['name'][114]];	#Diagramme
        $link_groupdesc[2][9] = "Workflow";					#Workflow

        $link_groupdesc[4][0] = $lang[1812];		#user
        $link_groupdesc[4][3] = $lang[$LINK['name'][29]];		#Einstellungen
        $link_groupdesc[4][6] = $lang[$LINK['name'][189]];	#Schnappschuss
        $link_groupdesc[4][7] = $lang[2059];	#workflow
        $link_groupdesc[4][8] = $lang[$LINK['name'][40]];		#Wiedervorlage
        $link_groupdesc[4][9] = $lang[$LINK['name'][32]];		#Farben

        $link_groupdesc[5][0] = $lang[1813];		#add_on

       echo "<tr><td valign=\"top\" style=\"width:100px;\">".$lang[573],"</td><td valign=\"top\">
        <input class=\"gtabHeaderInputINP\" id=\"gdquicksearch\" style=\"width:100%;height:20px;\" onclick=\"lmb_searchDropdown(this,'gdsearchfield',15);\">
        <select id=\"gdsearchfield\" name=\"menudetail[name]\" style=\"width: 100%;max-height:200px\"><option value=\"0\">";

        foreach ($link_groupdesc as $gkey => $subgroup) {
            foreach ($subgroup as $sgey => $subgroupDesc) {

                # collect tables of that tabgroup
                $tabgroupOptions = '';
                foreach ($LINK["name"] as $LinkKey => $LinkID) {
                    if($LINK['subgroup'][$LinkKey] == $sgey AND $LINK['typ'][$LinkKey] == $gkey AND ($LINK['link_url'][$LinkKey] OR $LINK['extension'][$LinkKey])){
                        if($custmenu['name'][$id] == $LinkKey){$SELECTED = 'SELECTED';}else{$SELECTED = '';}
                        $tabgroupOptions .= "<option value=\"{$LinkKey}\" $SELECTED>{$lang[$LINK['name'][$LinkKey]]}</option>";
                    }
                }
                # only show tabgroup if tables are available
                if ($tabgroupOptions) {
                    echo "<optgroup label=\"{$subgroupDesc}\">{$tabgroupOptions}</optgroup>";
                }
            }
        }
        echo "</select>
        </td></tr>
        ";

    ############################## context menu ###############################


    // contextmenu - manuel
    }elseif($custmenu['typ'][$id] == 8) {
        echo "<tr><td>Name</td><td><input type=\"text\" name=\"menudetail[name]\" style=\"width:100%\" value=\"".$lang[$custmenu['name'][$id]]."\"></td></tr>";
        echo "<tr><td>Title</td><td><input type=\"text\" name=\"menudetail[title]\" style=\"width:100%\" value=\"".$lang[$custmenu['title'][$id]]."\"></td></tr>";
        echo "<tr><td>URL</td><td><input type=\"text\" name=\"menudetail[url]\" style=\"width:100%\" value=\"".$custmenu['url'][$id]."\"></td></tr>";
        echo "<tr><td>ICON</td><td><input type=\"text\" name=\"menudetail[icon]\" style=\"width:100%\" value=\"".$custmenu['icon'][$id]."\"></td></tr>";
        echo "<tr><td>{$lang[ 1107]}</td><td><input type=\"text\" name=\"menudetail[bg]\" style=\"width:100%\" value=\"".$custmenu['bg'][$id]."\"></td></tr>";
        echo "<tr><td>disabled</td><td><input type=\"text\" name=\"menudetail[disabled]\" style=\"width:100%\" value=\"".$custmenu['disabled'][$id]."\"></td></tr>";
        echo "<tr><td>inactive</td><td><input type=\"text\" name=\"menudetail[inactive]\" style=\"width:100%\" value=\"".$custmenu['inactive'][$id]."\"></td></tr>";

    // contextmenu - function
    }elseif($custmenu['typ'][$id] == 13) {
        echo "<tr><td>Name</td><td><input type=\"text\" name=\"menudetail[name]\" style=\"width:100%\" value=\"".$lang[$custmenu['name'][$id]]."\"></td></tr>";
        echo "<tr><td>Funktionsname</td><td><input type=\"text\" name=\"menudetail[url]\" style=\"width:100%\" value=\"".$custmenu['url'][$id]."\"></td></tr>";

    // contextmenu - system
    }elseif($custmenu['typ'][$id] == 9) {
        global $LINK;

        $link_groupdesc = array();
        # ------- Gruppenschema ----------
        $link_groupdesc[3][1] = $lang[545];	    #Datei
        $link_groupdesc[3][2] = $lang[843];	    #Bearbeiten
        $link_groupdesc[3][3] = $lang[1625];	#Ansicht
        $link_groupdesc[3][7] = $lang[1939];	#Extras
        $link_groupdesc[5][0] = $lang[1813];	#add_on

       echo "<tr><td valign=\"top\" style=\"width:100px;\">".$lang[573],"</td><td valign=\"top\">
        <input class=\"gtabHeaderInputINP\" id=\"gdquicksearch\" style=\"width:100%;height:20px;\" onclick=\"lmb_searchDropdown(this,'gdsearchfield',15);\">
        <select id=\"gdsearchfield\" name=\"menudetail[name]\" style=\"width: 100%;max-height:200px\"><option value=\"0\">";

        foreach ($link_groupdesc as $gkey => $subgroup) {
            foreach ($subgroup as $sgey => $subgroupDesc) {

                # collect tables of that tabgroup
                $tabgroupOptions = '';
                foreach ($LINK["name"] as $LinkKey => $LinkID) {
                    if($LINK['subgroup'][$LinkKey] == $sgey AND $LINK['typ'][$LinkKey] == $gkey AND ($LINK['link_url'][$LinkKey] OR $LINK['extension'][$LinkKey])){
                        if($custmenu['name'][$id] == $LinkKey){$SELECTED = 'SELECTED';}else{$SELECTED = '';}
                        $tabgroupOptions .= "<option value=\"{$LinkKey}\" $SELECTED>{$lang[$LINK['name'][$LinkKey]]}</option>";
                    }
                }
                # only show tabgroup if available
                if ($tabgroupOptions) {
                    echo "<optgroup label=\"{$subgroupDesc}\">{$tabgroupOptions}</optgroup>";
                }
            }
        }
        echo "</select>
        </td></tr>";

    }elseif($custmenu['typ'][$id] == 12) {
        $custmenu_list = lmb_custmenulistGet();

        echo "<tr><td valign=\"top\" style=\"width:100px;\">".$lang[573],"</td><td valign=\"top\">
        <input class=\"gtabHeaderInputINP\" id=\"gdquicksearch\" style=\"width:100%;height:20px;\" onclick=\"lmb_searchDropdown(this,'gdsearchfield',15);\">
        <select id=\"gdsearchfield\" name=\"menudetail[name]\" style=\"width: 100%;max-height:200px\"><option value=\"0\">";


        foreach($custmenu_list['name'] as $ufkey => $ufvalue){
            if($custmenu['name'][$id] == $ufkey){$SELECTED = 'SELECTED';}else{$SELECTED = '';}
            echo "<option $SELECTED value=\"{$ufkey}\">{$lang[$ufvalue]}</option>";
        }
        echo "</select>
        </td></tr>";

    }


    echo "<tr><td colspan=\"2\"><div style=\"height:20px\"></div></TD></TR>";
    echo "<tr><td colspan=\"2\" align=\"right\"><input type=\"button\" onclick=\"lmb_loadMenueditor(null,$id,'','update')\" name=\"menudetail[update]\" value=".$lang[33]."></TD></TR>";

    echo "</form></table>";

}


function lmb_custmenu(&$custmenu,$linkid,$parent){
    global $lang;
    global $farbschema;
    global $gtab;
    global $gsnap;
    global $greportlist;
    global $gformlist;
    global $gdiaglist;
    global $LINK;
    global $custmenu_list;

    $types[1] = $lang[2144];
    $types[2] = $lang[577];
    $types[3] = $lang[2608];
    $types[4] = $lang[1137];
    $types[5] = $lang[1179];
    $types[6] = $lang[2119];
    $types[7] = $lang[573];
    $types[8] = $lang[2144];
    $types[9] = $lang[573];
    $types[10] = $lang[1179];
    $types[11] = $lang[1137];
    $types[12] = $lang[2985];
    $types[13] = $lang[2625];

    if(!$custmenu_list){$custmenu_list = lmb_custmenulistGet();}

    if(!$parent) {
        echo "<div class=\"tabSubHeader tabSubHeaderItem\">" . $lang[$custmenu_list['name'][$linkid]] . "</div><br>";
    }

    echo "<table border=0 style=\"border-collapse: collapse;\">";
    foreach ($custmenu['id'] as $key => $id) {
        if($custmenu['parent'][$key] == $parent) {
            $cli = null;
            $cl = null;
            $readonly = null;

            if($parent) {
                if($custmenu['typ'][$key] != 1) {
                    $readonly = 'readonly disabled';
                }
                $cli = "<a onclick=\"lmb_loadMenueDetail($id)\"><div style=\"width:15px\" border=\"0\" class=\"lmb-icon lmb-edit\"></div></a>";
            }else{
                $cl = "class=\"lmbMenuHeaderNav\"";
            }

            $name = '';
            if($custmenu['typ'][$key] <= 1 OR $custmenu['typ'][$key] == 8 OR $custmenu['typ'][$key] == 13){
                $name = $lang[$custmenu['name'][$key]];
            }elseif($custmenu['typ'][$key] == 2){
                $name = $gtab['desc'][$custmenu['name'][$key]];
            }elseif($custmenu['typ'][$key] == 3){
                $gtabid = lmb_getGElementTabID($gsnap,$custmenu['name'][$key]);
                $name = $gsnap[$gtabid]['name'][$custmenu['name'][$key]];
            }elseif($custmenu['typ'][$key] == 4 OR $custmenu['typ'][$key] == 11){
                $gtabid = lmb_getGElementTabID($greportlist,$custmenu['name'][$key]);
                $name = $greportlist[$gtabid]['name'][$custmenu['name'][$key]];
            }elseif($custmenu['typ'][$key] == 5 OR $custmenu['typ'][$key] == 10){
                $gtabid = lmb_getGElementTabID($gformlist,$custmenu['name'][$key]);
                $name = $gformlist[$gtabid]['name'][$custmenu['name'][$key]];
            }elseif($custmenu['typ'][$key] == 6){
                $gtabid = lmb_getGElementTabID($gdiaglist,$custmenu['name'][$key]);
                $name = $gdiaglist[$gtabid]['name'][$custmenu['name'][$key]];
            }elseif($custmenu['typ'][$key] == 7 OR $custmenu['typ'][$key] == 9){
                $name = $lang[$LINK['name'][$custmenu['name'][$key]]];
            }elseif($custmenu['typ'][$key] == 12){
                $name = $lang[$custmenu_list['name'][$custmenu['name'][$key]]];
            }

            echo "<tr title=\"".$types[$custmenu['typ'][$key]]."\">
            <td>$cli</td>
            <td $cl><input $readonly type=\"text\" style=\"width:100%\" value=\"".$name."\" onchange=\"lmb_loadMenueditor(this,$id,'$parent','update')\"></td>
            <td align=\"right\"><a onclick=\"lmb_loadMenueditor(null,$id,'$parent','delete')\"><i class=\"lmb-icon lmb-trash\" border=\"0\"></i></a></td>
            <td><a onclick=\"lmb_loadMenueditor(null,$id,'$parent','up')\"><i class=\"lmb-icon lmb-long-arrow-up\" border=\"0\"></i></a></td>
            <td><a onclick=\"lmb_loadMenueditor(null,$id,'$parent','down')\"><i class=\"lmb-icon lmb-long-arrow-down\" border=\"0\"></i></a></td>

            
            </tr>";

            if(in_array($id,$custmenu['parent'])) {
            echo "<tr><td><div style=\"width:15px\">&nbsp;</div></td><td colspan=\"4\">";
                lmb_custmenu($custmenu,$linkid,$id);
            echo "</td></tr>";
            }
            $id_ = $id;
        }
    }

    echo "<tr>
    <td style=\"opacity:0.3;background-color:".$farbschema['WEB7']."\"></td>
    <td style=\"opacity:0.3;background-color:".$farbschema['WEB7']."\"></td>
    <td align=\"right\"><a onclick=\"lmb_loadMenueditor(document.getElementById('menutyp_$parent'),null,'$parent','add')\"><i class=\"lmb-icon lmb-plus\" border=\"0\"></a></i></td>
    <td colspan=\"2\"><a onclick=\"lmb_loadMenueditor(document.getElementById('menutyp_$parent'),null,'$id_','add')\"><i class=\"lmb-icon lmb-long-arrow-right\" border=\"0\"></a></i></td>
    </tr>";


    echo "</table>";
}



if($par) {
    $linkid = $par['linkid'];
    $custmenu = lmb_custmenuGet($linkid);

    if ($par['detail']) {

        if($par['act']) {
            lmb_custmenuEdit($custmenu, $par);
            $custmenu = lmb_custmenuGet($linkid);
        }
        lmb_custmenuDetail($custmenu,$par);

    } else {

        if($par['act']) {
            lmb_custmenuEdit($custmenu, $par);
            $custmenu = lmb_custmenuGet($linkid);
        }
        lmb_custmenu($custmenu,$linkid,0);
    }

    return;
}


?>

<script language="JavaScript">

    function lmb_loadMenueditor(el,id,parent,act) {

        container = 'lmbMainContainer';
        if(!id){id = '';}
        if(!act){act = '';}
        if(el){
            actid = "menuEditor&id="+id+"&linkid=<?=$linkid?>&act="+act+"&parent="+parent+"&menudetail[name]="+el.value;
        }else{
            actid = "menuEditor&id="+id+"&linkid=<?=$linkid?>&act="+act+"&parent="+parent;
        }

        ajaxGet(null,"main_dyns_admin.php",actid,null,function(data) {
            document.getElementById(container).innerHTML = data;
        },"form11");
        if($('.ui-dialog').length) {
            $('#lmbDetailContainer').dialog("destroy").remove();
        }

    }


    function lmb_loadMenueDetail(id,act){
        if(!id){id='';}
        if(!act){act='';}

        actid = "menuEditor&id="+id+"&detail=1&linkid=<?=$linkid?>&act="+act;

        ajaxGet(null,"main_dyns_admin.php",actid,null, function(data){
            if($('.ui-dialog').length) {
                document.getElementById('lmbDetailContainer').innerHTML = data;

            }else {

                $("<div id='lmbDetailContainer'></div>").html(data).dialog({
                    title: 'Menupunkt',
                    width: 500,
                    height: 350,
                    resizable: false,
                    modal: true,
                    zIndex: 10,
                    close: function () {
                        $('#lmbDetailContainer').dialog("destroy").remove();
                    }
                });

            }
        },"form11");
    }




</script>

<form name="form1" method="post" action="main_admin.php">
<input type="hidden" name="action" value="setup_menueditor">
<input type="hidden" name="tab" value="<?=$tab?>">
<input type="hidden" name="linkid" value="<?=$linkid?>">
<input type="hidden" name="listact">

<DIV class="lmbPositionContainerMainTabPool">

<TABLE class="tabpool" BORDER="0" width="700" cellspacing="0" cellpadding="0"><TR><TD>

	<TABLE BORDER="0" cellspacing="0" cellpadding="0"><TR class="tabpoolItemTR">
	<TD nowrap class="tabpoolItem<?=(($tab==1)?'Active':'Inactive')?>" OnClick="document.location.href='main_admin.php?action=setup_menueditor&tab=1'"><?=$lang[2981]?></TD>
	<TD nowrap class="tabpoolItem<?=(($tab==2)?'Active':'Inactive')?>" OnClick="document.location.href='main_admin.php?action=setup_menueditor&tab=2'"><?=$lang[2982]?></TD>
	<TD class="tabpoolItemSpace"> </TD>
	</TR></TABLE>

</TD></TR>

<TR><TD class="tabpoolfringe">

	<TABLE BORDER="0" cellspacing="1" cellpadding="2" width="100%" class="tabBody">
	<TR></TR><TD>&nbsp;</TD><TR>
    <TR></TR><TD>


<?php

if($tab == 1){

    if (!$linkid) {
        echo "<div id=\"lmbMainContainer\">";
        echo "<table>";
        foreach ($LINK['name'] as $key => $value) {
            // mainmenu
            if ($LINK['typ'][$key] == 1 AND $LINK['subgroup'][$key] == 2 AND $key >= 1000) {
                echo "<tr><td><a onclick=\"document.location.href='main_admin.php?action=setup_menueditor&type=1&linkid=$key'\"><i class=\"lmb-icon lmb-edit\" border=\"0\"></i></a></td><td><a onclick=\"document.location.href='main_admin.php?action=setup_menueditor&linkid=$key'\">" . $lang[$value] . "</a></td></tr>";
                $b = 1;
            }
        }
        echo "</table>";
        if(!$b){echo "No extended menu exists. First add an individual mainmenu link in <a onclick=\"parent.main.location='main_admin.php?action=setup_links'\"><u>menu functions</u></a>";}
        echo "</div>";
    } else {

        echo "<select onchange=\"document.form1.linkid.value=this.value;document.form1.submit();\">";
        foreach ($LINK['name'] as $key => $value) {
            if ($LINK['typ'][$key] == 1 AND $LINK['subgroup'][$key] == 2 AND $key >= 1000) {
                if($key == $linkid){$SELECTED = 'SELECTED';}else{$SELECTED = '';}
                echo "<option value=\"$key\" $SELECTED>" . $lang[$value] . "</option>";
            }
        }
        echo "</select><hr><br>";

        echo "<div id=\"lmbMainContainer\">";
        $custmenu = lmb_custmenuGet($linkid);
        lmb_custmenu($custmenu,$linkid,0);
        echo "</div>";

    }

}elseif($tab == 2){

    if($custmenulist){
        lmb_custmenulistEdit($listact,$linkid,$custmenulist);
    }

    $custmenu_list = lmb_custmenulistGet();

    if (!$linkid OR $listact) {
        echo "<div id=\"lmbMainContainer\">";
        echo "<table>";

        echo "<tr class=\"tabHeader\"><td class=\"tabHeaderItem\"></td>
        <td class=\"tabHeaderItem\">$lang[4]</td>
        <td class=\"tabHeaderItem\">$lang[164]</td>
        <td class=\"tabHeaderItem\">$lang[168]</td>
        <td class=\"tabHeaderItem\">$lang[925]</td>
        </tr>";

        foreach ($custmenu_list['name'] as $id => $name) {

            echo "<tr class=\"tabBody\">
            <td><a onclick=\"document.location.href='main_admin.php?action=setup_menueditor&tab=2&linkid=$id'\"><i class=\"lmb-icon lmb-pencil\" style=\"cursor:pointer\"></a></i></td>
            <td><input type=\"text\" name=\"custmenulist[$id][name]\" value=\"$lang[$name]\" onchange=\"document.form1.listact.value='change';document.form1.linkid.value='$id';document.form1.submit();\"></td>";

            echo "<td><select name=\"custmenulist[$id][tabid]\" onchange=\"document.form1.listact.value='change';document.form1.linkid.value='$id';document.form1.submit();\"><option>";
            foreach ($tabgroup["id"] as $key0 => $value0) {
                echo '<optgroup label="' . $tabgroup["name"][$key0] . '">';
                foreach ($gtab["tab_id"] as $key => $value) {
                    if($gtab["tab_group"][$key] == $value0){
                        if($custmenu_list['tabid'][$id] == $value){$selected = "selected";}else{$selected = "";}
                        echo "<OPTION VALUE=\"".$value."\" $selected>".$gtab["desc"][$key]."</OPTION>";
                    }
                }
                echo '</optgroup>';
            }
            echo "</select></td>";


            echo "<td><select name=\"custmenulist[$id][fieldid]\" onchange=\"document.form1.listact.value='change';document.form1.linkid.value='$id';document.form1.submit();\"><option>";
            if($tabid = $custmenu_list['tabid'][$id]) {
                foreach($gfield[$tabid]['field_name'] as $fkey => $fval){
                    if($custmenu_list['fieldid'][$id] == $fkey){$selected = "selected";}else{$selected = "";}
                    echo "<option $selected value=\"$fkey\">$fval</option>";
                }
            }
            echo "</select></td>";

            
            $type_ = array();
            $type_[$custmenu_list['type'][$id]] = 'SELECTED';
            echo "<td><select name=\"custmenulist[$id][type]\" onchange=\"document.form1.listact.value='change';document.form1.linkid.value='$id';document.form1.submit();\">
            
            
            <option></option>
            
            <optgroup label='Liste'>
            <option value='1' {$type_[1]}>{$lang[2983]}</option>
            <option value='3' {$type_[3]}>{$lang[545]}</option>
            <option value='4' {$type_[4]}>{$lang[843]}</option>
            <option value='5' {$type_[5]}>{$lang[1625]}</option>
            <option value='6' {$type_[6]}>{$lang[1939]}</option>
            <option value='2' {$type_[2]}>{$lang[2984 ]}</option>
            </optgroup>
            <optgroup label='Detail'>
            <option value='13' {$type_[13]}>{$lang[545]}</option>
            <option value='15' {$type_[15]}>{$lang[1625]}</option>
            <option value='16' {$type_[16]}>{$lang[1939]}</option>
            <option value='12' {$type_[12]}>{$lang[2984 ]}</option>
            </optgroup>
            </select></td>

            <td><i class=\"lmb-icon lmb-trash\" onclick=\"document.form1.listact.value='del';document.form1.linkid.value='$id';document.form1.submit();\" style=\"cursor:pointer\" border=\"0\"></i></td>
            </tr>";
        }

        echo "<tr class=\"tabBody\"><TD colspan=\"5\"><hr></td></tr>
        <tr class=\"tabBody\"><td colspan=\"5\">
        <input type=\"text\" name=\"custmenulist[add][name]\">
        <input type=\"submit\" value=\"".$lang[540]."\" onclick=\"document.form1.listact.value='add';document.form1.submit();\">
        </td></tr></table>";

        echo "</div>";
    } else {

        echo "<div id=\"lmbMainContainer\">";
        $custmenu = lmb_custmenuGet($linkid);
        lmb_custmenu($custmenu,$linkid,0);
        echo "</div>";
    }

}

?>


    </TD><TR></TABLE>
</TD><TR></TABLE>

</form>