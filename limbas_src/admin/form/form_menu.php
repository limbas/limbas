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
 * ID: 175
 */
?>
<script language="JavaScript">

<?
if ($alert) {
    echo "parent.form_main.document.form1.submit();\n";
}
?>

    /* ---------------- Sendkeypress---------------------- */
    function sendkeydown(evt) {
        if (evt.keyCode == 13) {
            window.focus();
        }
    }

    function change(func, wert1, wert2) {
        eval("parent.form_main.window." + func + "('" + wert1 + "','" + wert2 + "');");
    }

    function set_posframe() {
        frame_size = document.form1.framesize_top.value + ";" + document.form1.framesize_center.value;
        loc = parent.location.href + "&frame_size=" + frame_size;
        parent.location.href = loc;
    }

    function resetmenu() {
        document.form1.form_tab.value = '';
        document.form1.form_tab_el.value = '';

        <?php
            $reset = array('bild');
            echo "$('#new_bild_area').hide();";
            
            if($form["form_typ"] == 1) { 
                array_push($reset, 'uform', 'rect', 'tab', 'tabulator', 'menue', 'frame');
                if($umgvar["use_jsgraphics"]){
                    array_push($reset, 'ellipse', 'line');
                }
                echo "$('#new_uform_area').hide();";
            }

            if($form["form_typ"] == 2) {
                array_push($reset, 'stab', 'dbsearch', 'tabmenu');
                echo "$('#new_stab_area').hide();";
            }

            if($form["form_typ"] == 1 || $form["form_typ"] == 2) {
                array_push($reset, 'dbdat', 'dbdesc', 'dbnew', 'text', 'datum', 'scroll', 'js', 'php', 'submt', 'button', 'inptext', 'inparea', 'inpselect', 'inpcheck', 'inpradio', 'inphidden');
                echo "$('#new_dbdat_area').hide();";
            }

            foreach($reset as $id) {
                echo "$('#$id').css('border-style', 'outset');";
                echo "$('#$id').css('background-color', '{$farbschema["WEB7"]}');";
            }
        ?>

    }

    function pressbutton(id, st, col) {
        resetmenu();
        var objst = document.getElementById(id).style;
        objst.borderStyle = st;
        objst.backgroundColor = col;
    }

    function actbutton(id, st, opt) {
        var objst = document.getElementById(id).style;
        var stati = document.getElementById(st);
        resetmenu();
        if (stati.style.display == 'none') {
            stati.style.display = '';
            objst.borderStyle = 'inset';
            objst.backgroundColor = '<?= $farbschema[WEB10] ?>';
        } else {
            stati.style.display = 'none';
            objst.borderStyle = 'outset';
            objst.backgroundColor = '<?= $farbschema[WEB7] ?>';
        }
        form_add_val = id;
    }

    function start_uploadlevel() {
        var w = document.getElementById('uploadlevel').style.width;
        var w = parseInt(w) + 2;
        if (w > 190) {
            w = 1;
        }
        document.getElementById('uploadlevel').style.width = w;
        setTimeout("start_uploadlevel()", 100);
    }


    function add(obj) {
        document.form1.objekt.value = obj;
    }

// --- Datensatzfeld hinzufügen ----------------------------------
    var form_add_val = "";
    function add_dbfield(el, evt) {

        if (parent.form_main.document.form1.form_replace_element.value > 0 || evt.ctrlKey) {
            el.className = "markAsActive";
            document.form1.objekt.value = form_add_val;
            send();
        } else {
            if (el.className == "markAsActive") {
                el.className = "";
            } else {
                el.className = "markAsActive";
            }
        }

        //parent.form_main.window.set_posxy();

    }

    function send() {
        var obj = document.form1.objekt.value;
        switch (obj) {
            case "dbdat":
            case "dbdesc":
            case "dbsearch":
            case "dbnew":
                var spelling = new Array();
                var gtabid = new Array();
                var parentrel = new Array();
                var fieldid = new Array();
                var tabgroup = new Array();
                $(".markAsActive").each(function (index, el) {
                    spelling.push(el.getAttribute('lmspelling'));
                    fieldid.push(el.getAttribute('lmfieldid'));
                    gtabid.push(el.getAttribute('lmgtabid'));
                    parentrel.push(el.getAttribute('lmparentrel'));
                    tabgroup.push(el.getAttribute('lmptabgroup'));
                    el.className = "";
                });
                if (spelling.length <= 0) {
                    break;
                }
                parent.form_main.document.form1.form_dbdat_fieldname.value = spelling.join(";");
                parent.form_main.document.form1.form_dbdat_fieldid.value = fieldid.join(";");
                parent.form_main.document.form1.form_dbdat_tabid.value = gtabid.join(";");
                parent.form_main.document.form1.form_dbdat_parentrel.value = parentrel.join(";");
                parent.form_main.document.form1.form_dbdat_tabgroup.value = tabgroup.join(";");
                parent.form_main.document.form1.form_raster.value = document.form1.raster.value;
                parent.form_main.window.set_posxy();
                parent.form_main.document.form1.form_posxy_edit.value = '1';
                parent.form_main.document.form1.default_font.value = document.form1.default_font.value;
                parent.form_main.document.form1.default_size.value = document.form1.default_size.value;
                parent.form_main.document.form1.form_add.value = obj;
                parent.form_main.document.form1.submit();
                break;
            case "bild":
                document.getElementById('send_bild_area').style.display = '';
                start_uploadlevel();
                document.form1.form_add.value = "bild";
                document.form1.aktiv_id.value = parent.form_main.document.form1.aktiv_id.value;
                document.form1.aktiv_tabcontainer.value = parent.form_main.document.form1.aktiv_tabcontainer.value;
                document.form1.form_tab.value = parent.form_main.document.form1.form_tab.value;
                document.form1.form_tab_el.value = parent.form_main.document.form1.form_tab_el.value;
                document.form1.aktiv_tabulator.value = parent.form_main.document.form1.aktiv_tabulator.value;
                parent.form_main.document.form1.form_add.value = '';
                document.form1.submit();
                break;
            case "itemlist":
                document.getElementById('itemlist_area').style.display = '';
                break;
            default:
                parent.form_main.document.form1.form_add.value = obj;
                parent.form_main.window.set_posxy();
                parent.form_main.document.form1.form_posxy_edit.value = '1';
                parent.form_main.document.form1.default_font.value = document.form1.default_font.value;
                parent.form_main.document.form1.default_size.value = document.form1.default_size.value;
                parent.form_main.document.form1.new_text.value = 'TEXTBLOCK';
                if (obj == 'uform') {
                    parent.form_main.document.form1.uform_style.value = document.form1.uform_style.value;
                    parent.form_main.document.form1.uform_typ.value = document.form1.uform_typ.value;
                    if (document.form1.uform_typ.value == 2) {
                        parent.form_main.document.form1.uform_set.value = document.form1.uform_form.value;
                    } else if (document.form1.uform_typ.value == 3) {
                        parent.form_main.document.form1.uform_set.value = document.form1.uform_tab.value;
                    }
                }
                if (obj == 'stab' && document.form1.stab_snap_id[document.form1.stab_snap_id.selectedIndex].value) {
                    parent.form_main.document.form1.form_stab_snapid.value = document.form1.stab_snap_id[document.form1.stab_snap_id.selectedIndex].value;
                    parent.form_main.document.form1.form_stab_show.value = document.form1.stab_showtabs.checked + ';' + document.form1.stab_showmenu.checked + ';' + document.form1.stab_showsearch.checked + ';' + document.form1.stab_showfooter.checked;
                }
                document.form1.objekt.value = '';
                parent.form_main.document.form1.submit();
        }
    }

    function getElement(ev) {
        if (window.event && window.event.srcElement) {
            el = window.event.srcElement;
        } else {
            el = ev.target;
        }
        return el;
    }

    function make_bold(ev) {
        var el = getElement(ev);
        el.style.textDecoration = "underline";
    }
    function make_unbold(ev) {
        var el = getElement(ev);
        el.style.textDecoration = "none";
    }
    function delete_element(ev) {
        //open_details(ev);
        var el = getElement(ev);
        var dv = el.id.substr(2, 10);
        parent.form_main.lmb_dropEl('Element löschen', dv);
    }
    function open_details(ev) {

        var el = getElement(ev);
        var dv = 'div' + el.id.substr(2, 10);

        parent.form_main.document.getElementById(dv).onmousedown(ev);
        // enable selectable function
        parent.form_main.$('#innerframe').selectable("enable");

    }
    function setNewZindex() {
        parent.form_main.document.form1.set_new_zindex.value = '1';
        send();
    }


    function set_uform(el) {
        document.getElementById('uform_form').style.display = 'none';
        document.getElementById('uform_table').style.display = 'none';
        if (el.value == 2) {
            document.getElementById('uform_form').style.display = '';
        } else if (el.value == 3) {
            document.getElementById('uform_table').style.display = '';
        }
    }

</script>

<div id="lmbAjaxContainer" class="ajax_container" style="position:absolute;visibility:hidden;" OnClick="activ_menu = 1;"></div>

<TABLE BORDER="0" cellspacing="0" cellpadding="0"><TR><TD WIDTH="10">&nbsp;</TD><TD>

            <FORM ENCTYPE="multipart/form-data" ACTION="main_admin.php" METHOD="post" name="form1">
                <input type="hidden" name="<? echo $_SID; ?>" value="<? echo session_id(); ?>">
                <input type="hidden" name="action" value="setup_form_menu">
                <input type="hidden" name="form_id" value="<? echo $form_id; ?>">
                <input type="hidden" name="form_name" value="<? echo $form_name; ?>">
                <input type="hidden" name="referenz_tab" value="<? echo $referenz_tab; ?>">
                <input type="hidden" name="form_id" value="<? echo $form["form_id"]; ?>">
                <input type="hidden" name="form_typ" value="<? echo $form["form_typ"]; ?>">
                <input type="hidden" name="form_add">
                <input type="hidden" name="form_tab">
                <input type="hidden" name="form_tab_el">
                <input type="hidden" name="objekt">
                <input type="hidden" name="aktiv_id">
                <input type="hidden" name="aktiv_tabcontainer">
                <input type="hidden" name="aktiv_tabulator">
                <input type="hidden" name="form_dimension">
                <input type="hidden" name="default_class">


                <TABLE  class="formeditorPanel" cellspacing="0" cellpadding="2">
                    <TR><TD class="formeditorPanelHead" COLSPAN="4"><?= $lang[828] ?></TD></TR>
                    <TR><TD VALIGN="TOP"><b><?= $lang[828] ?></TD><TD><?= $form["name"]; ?></TD></TR>
                    <TR><TD VALIGN="TOP"><b><?= $lang[164] ?></TD><TD><?= $gtab["desc"][$referenz_tab]; ?></TD></TR>
                    <TR><TD VALIGN="TOP"><b><?= $lang[1182] ?></TD><TD><? if ($form["form_typ"] == 1) {
    echo $lang[1183];
} else {
    echo $lang[1184];
} ?></TD></TR>
                </TABLE>


                <TABLE class="formeditorPanel" cellspacing="0" cellpadding="2">
                    <TR><TD COLSPAN="4" class="formeditorPanelHead"><?= $lang[2782] ?></TD></TR>
                    <TR><TD STYLE="height:14px;"><B>X:</B></TD><TD><INPUT STYLE="border:none;BACKGROUND-COLOR:<? echo $farbschema[WEB8]; ?>;width:40px;height:13px;" NAME="XPOSI" OnChange="parent.form_main.posxy_change(this.value, '');"></TD><TD>&nbsp;&nbsp;&nbsp;&nbsp;<B>W:</B>&nbsp;&nbsp;</TD><TD><INPUT TYPE="TEXT" STYLE="border:none;BACKGROUND-COLOR:<? echo $farbschema[WEB8]; ?>;width:40px;height:13px;" NAME="WPOSI" OnChange="parent.form_main.sizexy_change('', this.value);"></TD></TR>
                    <TR><TD STYLE="height:14px;"><B>Y:</B></TD><TD><INPUT STYLE="border:none;BACKGROUND-COLOR:<? echo $farbschema[WEB8]; ?>;width:40px;height:13px;" NAME="YPOSI" OnChange="parent.form_main.posxy_change('', this.value);"></TD><TD>&nbsp;&nbsp;&nbsp;&nbsp;<B>H:</B>&nbsp;&nbsp;</TD><TD><INPUT TYPE="TEXT" STYLE="border:none;BACKGROUND-COLOR:<? echo $farbschema[WEB8]; ?>;width:40px;height:13px;" NAME="HPOSI" OnChange="parent.form_main.sizexy_change(this.value, '');"></TD></TR>
                    </TD></TR></TABLE>


                <TABLE cellspacing="0" cellpadding="2" class="formeditorPanel">
                    <TR><TD class="formeditorPanelHead" COLSPAN="3" align="center"><?= $lang[2781] ?></TD></TR>

                    <TR>
                        <TD><?= $lang[2581] ?></TD>
                        <TD COLSPAN="2"><SELECT OnChange="document.form1.default_class.value = this.value; document.form1.submit();" style="border:none;BACKGROUND-COLOR:<? echo $farbschema[WEB8]; ?>;width:120;height:14;">
                                <OPTION VALUE="NULL">
                                <OPTION VALUE="/USER/<?= $session["user_id"] ?>/layout.css" <? if (strpos($form["css"], "layout.css")) {
                                        echo "SELECTED";
                                    } ?>>layout.css
                                    <?
                                    if (file_exists($umgvar["pfad"] . "/EXTENSIONS/css")) {
                                        $extfiles = read_dir($umgvar["pfad"] . "/EXTENSIONS/css", 0);
                                        if ($extfiles["name"]) {
                                            foreach ($extfiles["name"] as $key1 => $filename) {
                                                if ($extfiles["typ"][$key1] == "file") {
                                                    $path = substr($extfiles["path"][$key1], strlen($umgvar["pfad"]), 100);
                                                    if ($form["css"] == $path . $filename) {
                                                        $selected = "SELECTED";
                                                    } else {
                                                        $selected = "";
                                                    }
                                                    echo "<OPTION VALUE=\"" . $path . $filename . "\" $selected>" . str_replace("/EXTENSIONS/css/", "", $path) . $filename;
                                                }
                                            }
                                        }
                                    }
                                    ?>
                            </SELECT></TD></TR>

                    <TR><TD><?= $lang[1170] ?>:</TD><TD VALIGN="TOP" ALIGN="right">
                            <SELECT NAME="default_font" STYLE="border:none;BACKGROUND-COLOR:<? echo $farbschema[WEB8]; ?>;width:70;height:14;">
<?
foreach ($sysfont as $key => $value) {
    echo "<OPTION VALUE=\"" . $value . "\">" . $value . "\n";
}
?>
                            </SELECT>
                        <TD VALIGN="TOP"><INPUT TYPE="TEXT" STYLE="width:30px;height:15px;" NAME="default_size" VALUE=""></TD>
                    </TR>

                    <TR><TD><?= $lang[1103] ?> X/Y:</TD><TD VALIGN="TOP" ALIGN="right"><input id="formX" type="text" OnChange="document.form1.form_dimension.value = this.value + 'x' + document.getElementById('formY').value;
                            document.form1.submit();" STYLE="width:30px;border:none;BACKGROUND-COLOR:<? echo $farbschema[WEB8]; ?>;" VALUE="<?php echo $form["dimension"][0]; ?>"></TD><TD><input id="formY" OnChange=" document.form1.form_dimension.value = document.getElementById('formX').value + 'x' + this.value;
                                    document.form1.submit();" type="text" STYLE="width:30px;border:none;BACKGROUND-COLOR:<? echo $farbschema[WEB8]; ?>;" VALUE="<?php echo $form["dimension"][1]; ?>"></TD><TR>

                    <TR><TD COLSPAN="2">Raster:</TD><TD><INPUT TYPE="TEXT" NAME="raster" VALUE="10" STYLE="width:30px;border:none;BACKGROUND-COLOR:<? echo $farbschema[WEB8]; ?>;"></TD></TR>
                    <TR><TD COLSPAN="2"><?= $lang[1148] ?>:</TD><TD><INPUT TYPE="CHECKBOX" NAME="prop" STYLE="border:none;BACKGROUND-COLOR:<? echo $farbschema[WEB7]; ?>;"></TD></TR>
                    <TR><TD COLSPAN="2"><?= $lang[2063] ?>:</TD><TD><INPUT TYPE="CHECKBOX" NAME="set_zindex" STYLE="border:none;BACKGROUND-COLOR:<? echo $farbschema[WEB7]; ?>;"></TD></TR>
                    <TR><TD COLSPAN="3"><a href="#" onclick="setNewZindex();"><?= $lang[2067] ?></a></TD></TR>
                    </TD></TR></TABLE>


                <? if ($form["form_typ"] == 2) { ?>
                    <TABLE BORDER="0" WIDTH="200" cellspacing="0" cellpadding="0">
                        <TR><TD STYLE="height:15px"></TD></TR>
                        <TR><TD HEIGHT="25">&nbsp;<?= $lang[2456] ?>:&nbsp;<SELECT STYLE="width:150px;" NAME="form_redirect" OnChange="parent.form_main.document.form1.form_redirect.value = this.value;parent.form_main.document.form1.submit();"><OPTION VALUE="0">
    <?
    $bzm = 0;
    while ($formlist["id"][$bzm]) {
        if ($formlist["id"][$bzm] != $form_id) {
            if ($formlist["id"][$bzm] == $form[redirect]) {
                $SELECTED = "SELECTED";
            } else {
                $SELECTED = "";
            }
            echo "<OPTION VALUE=\"" . $formlist["id"][$bzm] . "\" $SELECTED>" . $formlist["name"][$bzm];
        }
        $bzm++;
    }
    ?>
                                </SELECT>
                            </TD></TR>
                    </TABLE>
                                    <? } ?>

                <BR>

                <TABLE cellspacing="0" cellpadding="0"  class="formeditorPanel">
                    <TR><TD COLSPAN="10"  class="formeditorPanelHead"><?= $lang[2780] ?></TD></TR>
                    <TR><TD><TABLE BORDER="0" cellspacing="0" cellpadding="0"><TR>
                                    <? if ($form["form_typ"] == 1 OR $form["form_typ"] == 2) {
                                        $st = "";
                                    } else {
                                        $st = "none";
                                    } ?><TD VALIGN="TOP" STYLE="display:<?= $st ?>;"><I ID="text" class="lmb-icon lmb-rep-txt btn" TITLE="<?= $lang[1149] ?>" VALUE="text" OnMouseDown="pressbutton('text', 'inset', '<? echo $farbschema[WEB10]; ?>');" OnMouseUp="pressbutton('text', 'outset', '<? echo $farbschema[WEB7]; ?>');add('text');
            send();"></i></TD>
                                    <? if (($form["form_typ"] == 1 OR $form["form_typ"] == 2) AND $form["referenz_tab"]) {
                                        $st = "";
                                    } else {
                                        $st = "none";
                                    } ?><TD VALIGN="TOP" STYLE="display:<?= $st ?>;"><i ID="dbdat" class="lmb-icon lmb-rep-db btn" TITLE="<?= $lang[1150] ?>" VALUE="dbdat" OnClick="parent.form_main.document.form1.form_add.value = 'dbdat';
                actbutton('dbdat', 'new_dbdat_area', 0);
                add('dbdat');"></i></TD>
                                    <? if (($form["form_typ"] == 1 OR $form["form_typ"] == 2) AND $form["referenz_tab"]) {
                                        $st = "";
                                    } else {
                                        $st = "none";
                                    } ?><TD VALIGN="TOP" STYLE="display:<?= $st ?>;"><i ID="dbdesc" class="lmb-icon lmb-rep-dbdesc btn" TITLE="<?= $lang[1773] ?>" VALUE="dbdesc" OnClick="parent.form_main.document.form1.form_add.value = 'dbdesc';
                    actbutton('dbdesc', 'new_dbdat_area', 0);add('dbdesc');"></i></TD>
<? if ($form["form_typ"] == 2 AND $form["referenz_tab"]) {
    $st = "";
} else {
    $st = "none";
} ?><TD VALIGN="TOP" STYLE="display:<?= $st ?>;"><i ID="dbsearch" class="lmb-icon lmb-rep-dbsearch btn" TITLE="<?= $lang[1774] ?>" VALUE="dbsearch" OnClick="parent.form_main.document.form1.form_add.value = 'dbsearch';
                        actbutton('dbsearch', 'new_dbdat_area', 0);add('dbsearch');"></i></TD>
<? if ($form["form_typ"] == 2 AND $form["referenz_tab"]) {
    $st = "";
} else {
    $st = "none";
} ?><TD VALIGN="TOP" STYLE="display:<?= $st ?>;"><i ID="dbnew" class="lmb-icon lmb-rep-dbnew btn" TITLE="<?= $lang[1774] ?>" VALUE="dbnew" OnClick="parent.form_main.document.form1.form_add.value = 'dbnew';
                            actbutton('dbnew', 'new_dbdat_area', 0);add('dbnew');"></i></TD>
<? if ($form["form_typ"] == 1 OR $form["form_typ"] == 2) {
    $st = "";
} else {
    $st = "none";
} ?><TD VALIGN="TOP" STYLE="display:<?= $st ?>;"><i ID="bild" class="lmb-icon lmb-rep-pic btn" TITLE="<?= $lang[1151] ?>" VALUE="bild" OnClick="parent.form_main.document.form1.form_add.value = 'bild';
                                actbutton('bild', 'new_bild_area', 0);
                                add('bild');"></i></TD>
<? if ($form["form_typ"] == 1) {
    $st = "";
} else {
    $st = "none";
} ?><TD VALIGN="TOP" STYLE="display:<?= $st ?>;"><i ID="uform" class="lmb-icon-cus lmb-rep-uform btn" TITLE="<?= $lang[1171] ?>" VALUE="uform" OnClick="parent.form_main.document.form1.form_add.value = 'uform';
                                    actbutton('uform', 'new_uform_area', 0);add('uform');"></i></TD>
<? if ($form["form_typ"] == 1) {
    $st = "";
} else {
    $st = "none";
} ?><TD VALIGN="TOP" STYLE="display:<?= $st ?>;"><i ID="tab" class="lmb-icon lmb-rep-tab btn" TITLE="<?= $lang[1155] ?>" VALUE="tab" OnMouseDown="pressbutton('tab', 'inset', '<? echo $farbschema[WEB10]; ?>');" OnMouseUp="pressbutton('tab', 'outset', '<? echo $farbschema[WEB7]; ?>');
                                        add('tab');
                                        send();"></i></TD>
<? if ($form["form_typ"] == 1) {
    $st = "";
} else {
    $st = "none";
} ?><TD VALIGN="TOP" STYLE="display:<?= $st ?>;"><i ID="datum" class="lmb-icon lmb-rep-date btn" TITLE="<?= $lang[1156] ?>" VALUE="datum" OnMouseDown="pressbutton('datum', 'inset', '<? echo $farbschema[WEB10]; ?>');" OnMouseUp="pressbutton('datum', 'outset', '<? echo $farbschema[WEB7]; ?>');
                                            add('datum');send();"></i></TD>
                                </TR><TR>
<? if ($form["form_typ"] == 2) {
    $st = "";
} else {
    $st = "none";
} ?><TD VALIGN="TOP" STYLE="display:<?= $st ?>;"><i ID="tab" class="lmb-icon lmb-rep-table btn" TITLE="<?= $lang[1155] ?>" VALUE="tab" OnMouseDown="pressbutton('tab', 'inset', '<? echo $farbschema[WEB10]; ?>');" OnMouseUp="pressbutton('tab', 'outset', '<? echo $farbschema[WEB7]; ?>');add('tab');
            send();"></i></TD>
                                        <? if ($form["form_typ"] == 2) {
                                            $st = "";
                                        } else {
                                            $st = "none";
                                        } ?><TD VALIGN="TOP" STYLE="display:<?= $st ?>;"><i ID="stab" class="lmb-icon lmb-rep-table btn" TITLE="<?= $lang[1155] ?>" VALUE="stab" OnClick="parent.form_main.document.form1.form_add.value = 'stab';
                actbutton('stab', 'new_stab_area', 0);
                add('stab');"></i></TD>
<? if ($umgvar["use_jsgraphics"]) { ?>
    <? if ($form["form_typ"] == 1) { ?><TD VALIGN="TOP"><i ID="ellipse" class="lmb-icon lmb-rep-circle btn" TITLE="<?= $lang[1154] ?>" VALUE="ellipse" OnMouseDown="pressbutton('ellipse', 'inset', '<? echo $farbschema[WEB10]; ?>');" OnMouseUp="pressbutton('ellipse', 'outset', '<? echo $farbschema[WEB7]; ?>');
                add('ellipse');send();"></i></TD><? } ?>
    <? if ($form["form_typ"] == 1) {
        $st = "";
    } else {
        $st = "none";
    } ?><TD VALIGN="TOP" STYLE="display:<?= $st ?>;"><i ID="line" class="lmb-icon lmb-rep-line btn" TITLE="<?= $lang[1152] ?>" VALUE="line" OnMouseDown="pressbutton('line', 'inset', '<? echo $farbschema[WEB10]; ?>');" OnMouseUp="pressbutton('line', 'outset', '<? echo $farbschema[WEB7]; ?>');add('line');
                send();"></i></TD>
<? } ?>
<? if ($form["form_typ"] == 1) {
    $st = "";
} else {
    $st = "none";
} ?><TD VALIGN="TOP" STYLE="display:<?= $st ?>;"><i ID="rect" class="lmb-icon lmb-rep-rect btn" TITLE="<?= $lang[1153] ?>" VALUE="rect" OnMouseDown="pressbutton('rect', 'inset', '<? echo $farbschema[WEB10]; ?>');" OnMouseUp="pressbutton('rect', 'outset', '<? echo $farbschema[WEB7]; ?>');add('rect');send();"></i></TD>
<? if ($form["form_typ"] == 1) {
    $st = "";
} else {
    $st = "none";
} ?><TD VALIGN="TOP" STYLE="display:<?= $st ?>;"><i ID="menue" class="lmb-icon lmb-rep-menu btn" TITLE="<?= $lang[1946] ?>" VALUE="menue" OnMouseDown="pressbutton('menue', 'inset', '<? echo $farbschema[WEB10]; ?>');" OnMouseUp="pressbutton('menue', 'outset', '<? echo $farbschema[WEB7]; ?>');
                add('menue');send();"></i></TD>
<? if ($form["form_typ"] == 1) {
    $st = "";
} else {
    $st = "none";
} ?><TD VALIGN="TOP" STYLE="display:<?= $st ?>;"><i ID="tabulator" class="lmb-icon lmb-rep-tab btn" TITLE="<?= $lang[2561] ?>" VALUE="menue" OnMouseDown="pressbutton('tabulator', 'inset', '<? echo $farbschema[WEB10]; ?>');" OnMouseUp="pressbutton('tabulator', 'outset', '<? echo $farbschema[WEB7]; ?>');add('tabulator');send();"></i></TD>
<? if ($form["form_typ"] == 1) {
    $st = "";
} else {
    $st = "none";
} ?><TD VALIGN="TOP" STYLE="display:<?= $st ?>;"><i ID="frame" class="lmb-icon lmb-rep-frame btn" TITLE="<?= $lang[2661] ?>" VALUE="menue" OnMouseDown="pressbutton('frame', 'inset', '<? echo $farbschema[WEB10]; ?>');" OnMouseUp="pressbutton('frame', 'outset', '<? echo $farbschema[WEB7]; ?>');
                        add('frame');send();"></i></TD>
                                        <? if ($form["form_typ"] == 1 OR $form["form_typ"] == 2) {
                                            $st = "";
                                        } else {
                                            $st = "none";
                                        } ?><TD VALIGN="TOP" STYLE="display:<?= $st ?>;"><i ID="submt" class="lmb-icon lmb-rep-submit btn" TITLE="<?= $lang[1174] ?>" VALUE="submt" OnMouseDown="pressbutton('submt', 'inset', '<? echo $farbschema[WEB10]; ?>');" OnMouseUp="pressbutton('submt', 'outset', '<? echo $farbschema[WEB7]; ?>');add('submt');send();"></i></TD>
<? if ($form["form_typ"] == 1 OR $form["form_typ"] == 2) {
    $st = "";
} else {
    $st = "none";
} ?><TD VALIGN="TOP" STYLE="display:<?= $st ?>;"><i ID="button" class="lmb-icon lmb-rep-button btn" TITLE="Button" VALUE="submt" OnMouseDown="pressbutton('button', 'inset', '<? echo $farbschema[WEB10]; ?>');" OnMouseUp="pressbutton('button', 'outset', '<? echo $farbschema[WEB7]; ?>');add('button');send();"></i></TD>

                                        <? if (($form["form_typ"] == 1 OR $form["form_typ"] == 2) AND $form["referenz_tab"]) {
                                            $st = "";
                                        } else {
                                            $st = "none";
                                        } ?><TD VALIGN="TOP" STYLE="display:<?= $st ?>;"><i ID="scroll" class="lmb-icon lmb-rep-scroll btn" TITLE="<?= $lang[1173] ?>" VALUE="scroll" OnMouseDown="pressbutton('scroll', 'inset', '<? echo $farbschema[WEB10]; ?>');" OnMouseUp="pressbutton('scroll', 'outset', '<? echo $farbschema[WEB7]; ?>');add('scroll');send();"></i></TD>
<? if ($form["form_typ"] == 2 AND $form["referenz_tab"]) {
    $st = "";
} else {
    $st = "none";
} ?><TD VALIGN="TOP" STYLE="display:<?= $st ?>;"><i ID="tabmenu" class="lmb-icon lmb-table btn" TITLE="<?= $lang[1173] ?>" VALUE="tabmenu" OnMouseDown="pressbutton('tabmenu', 'inset', '<? echo $farbschema[WEB10]; ?>');" OnMouseUp="pressbutton('tabmenu', 'outset', '<? echo $farbschema[WEB7]; ?>');add('tabmenu');send();"></i></TD>
                                </TR><TR>
<? if ($form["form_typ"] == 1 OR $form["form_typ"] == 2) {
    $st = "";
} else {
    $st = "none";
} ?><TD VALIGN="TOP" STYLE="display:<?= $st ?>;"><i ID="inptext" class="lmb-icon lmb-rep-text btn" TITLE="<?= $lang[1947] ?>" VALUE="inptext" OnMouseDown="pressbutton('inptext', 'inset', '<? echo $farbschema[WEB10]; ?>');" OnMouseUp="pressbutton('inptext', 'outset', '<? echo $farbschema[WEB7]; ?>');
            add('inptext');send();"></i></TD>
                                <? if ($form["form_typ"] == 1 OR $form["form_typ"] == 2) {
                                    $st = "";
                                } else {
                                    $st = "none";
                                } ?><TD VALIGN="TOP" STYLE="display:<?= $st ?>;"><i ID="inparea" class="lmb-icon lmb-rep-area btn" TITLE="<?= $lang[1948] ?>" VALUE="inparea" OnMouseDown="pressbutton('inparea', 'inset', '<? echo $farbschema[WEB10]; ?>');" OnMouseUp="pressbutton('inparea', 'outset', '<? echo $farbschema[WEB7]; ?>');
                add('inparea');
                send();"></i></TD>
<? if ($form["form_typ"] == 1 OR $form["form_typ"] == 2) {
    $st = "";
} else {
    $st = "none";
} ?><TD VALIGN="TOP" STYLE="display:<?= $st ?>;"><i ID="inpselect" class="lmb-icon lmb-rep-select btn" TITLE="<?= $lang[1949] ?>" VALUE="inpselect" OnMouseDown="pressbutton('inpselect', 'inset', '<? echo $farbschema[WEB10]; ?>');" OnMouseUp="pressbutton('inpselect', 'outset', '<? echo $farbschema[WEB7]; ?>');add('inpselect');send();"></i></TD>
<? if ($form["form_typ"] == 1 OR $form["form_typ"] == 2) {
    $st = "";
} else {
    $st = "none";
} ?><TD VALIGN="TOP" STYLE="display:<?= $st ?>;"><i ID="inpcheck" class="lmb-icon lmb-rep-check btn" TITLE="<?= $lang[1950] ?>" VALUE="inpcheck" OnMouseDown="pressbutton('inpcheck', 'inset', '<? echo $farbschema[WEB10]; ?>');" OnMouseUp="pressbutton('inpcheck', 'outset', '<? echo $farbschema[WEB7]; ?>');add('inpcheck');send();"></i></TD>
<? if ($form["form_typ"] == 1 OR $form["form_typ"] == 2) {
    $st = "";
} else {
    $st = "none";
} ?><TD VALIGN="TOP" STYLE="display:<?= $st ?>;"><i ID="inpradio" class="lmb-icon lmb-rep-radio btn" TITLE="<?= $lang[1951] ?>" VALUE="inpradio" OnMouseDown="pressbutton('inpradio', 'inset', '<? echo $farbschema[WEB10]; ?>');" OnMouseUp="pressbutton('inpradio', 'outset', '<? echo $farbschema[WEB7]; ?>');add('inpradio');
                            send();"></i></TD>
<? if ($form["form_typ"] == 1 OR $form["form_typ"] == 2) {
    $st = "";
} else {
    $st = "none";
} ?><TD VALIGN="TOP" STYLE="display:<?= $st ?>;"><i ID="inphidden" class="lmb-icon lmb-rep-hidden btn" TITLE="<?= $lang[1968] ?>" VALUE="inphidden" OnMouseDown="pressbutton('inphidden', 'inset', '<? echo $farbschema[WEB10]; ?>');" OnMouseUp="pressbutton('inphidden', 'outset', '<? echo $farbschema[WEB7]; ?>');add('inphidden');
                                send();"></i></TD>
                                </TR><TR>
<? if ($form["form_typ"] == 1 OR $form["form_typ"] == 2) {
    $st = "";
} else {
    $st = "none";
} ?><TD VALIGN="TOP" STYLE="display:<?= $st ?>;"><i ID="js" class="lmb-icon lmb-rep-js btn" TITLE="<?= $lang[1505] ?>" VALUE="js" OnMouseDown="pressbutton('js', 'inset', '<? echo $farbschema[WEB10]; ?>');" OnMouseUp="pressbutton('js', 'outset', '<? echo $farbschema[WEB7]; ?>');
            add('js');
            send();"></i></TD>
<? if ($form["form_typ"] == 1 OR $form["form_typ"] == 2) {
    $st = "";
} else {
    $st = "none";
} ?><TD VALIGN="TOP" STYLE="display:<?= $st ?>;"><i ID="php" class="lmb-icon lmb-rep-php btn" TITLE="<?= $lang[1772] ?>" VALUE="php" OnMouseDown="pressbutton('php', 'inset', '<? echo $farbschema[WEB10]; ?>');" OnMouseUp="pressbutton('php', 'outset', '<? echo $farbschema[WEB7]; ?>');add('php');send();"></i></TD>
                                </TR></TABLE>
                        </TD></TR></TABLE>




                <div ID="new_bild_area" style="display:none;">
                    <TABLE class="formeditorPanel" cellspacing="0" cellpadding="2">
                        <TR><TD class="formeditorPanelHead" colspan="2" STYLE="height:15px"></TD></TR>
                        <TR><TD colspan="2" HEIGHT="25"><INPUT TYPE="FILE" NAME="new_pic" SIZE="20" STYLE="width:200px;background-color:<? echo $farbschema["WEB7"]; ?>;height:17px;"></TD></TR>

                        <TR><TD><?= $lang[925] ?></td>
                            <td><SELECT STYLE="width:60px;" NAME="pic_type">
                                    <OPTION VALUE="jpg">jpg
                                    <OPTION VALUE="png">png
                                    <OPTION VALUE="gif">gif
                                </SELECT>
                            </td></tr>

                        <TR><TD HEIGHT="20"><?= $lang[1176] ?></td>
                            <td><SELECT STYLE="width:60px;" NAME="pic_compress">
                                    <OPTION VALUE="30">30%
                                    <OPTION VALUE="40">40%
                                    <OPTION VALUE="50">50%
                                    <OPTION VALUE="60">60%
                                    <OPTION VALUE="70">70%
                                    <OPTION VALUE="75">75%
                                    <OPTION VALUE="80">80%
                                    <OPTION VALUE="85">85%
                                    <OPTION VALUE="90">90%
                                    <OPTION VALUE="95">95%
                                    <OPTION VALUE="100" SELECTED>100%
                                </SELECT>
                            </TD></TR>
                        <TR ID="send_bild_area" style="display:none;"><TD STYLE="height:30px;" VALIGN="CENTER">&nbsp;<SPAN ID="uploadlevel" STYLE="width:1px;height:15px;border:2px inset grey;background-color:<? echo $farbschema[WEB10]; ?>">&nbsp;</SPAN></TD></TR>
                    </TABLE>
                    <BR>
                </div>



                <div ID="new_stab_area" style="display:none;">
                    <TABLE cellspacing="0" cellpadding="0" STYLE="border:1px solid grey;width:210px">
                        <TR><TD COLSPAN="2" STYLE="height:15px"></TD></TR>
                        <TR><TD COLSPAN="2" VALIGN="TOP">&nbsp;snapshot:<BR>
                                &nbsp;<SELECT NAME="stab_snap_id" STYLE="width:190px"><OPTION>
<?
if ($snap["name"][$form["referenz_tab"]]) {
    foreach ($snap["name"][$form["referenz_tab"]] as $key => $value) {
        echo "<OPTION VALUE=\"" . $key . "\">" . $value;
    }
}
?>
                                </SELECT></TD></TR>

                        <TR BGCOLOR="<? echo $farbschema["WEB7"]; ?>"><TD>&nbsp;tabulator:</TD><TD><input type="checkbox" name="stab_showtabs" value="1"></TD></TR>
                        <TR BGCOLOR="<? echo $farbschema["WEB7"]; ?>"><TD>&nbsp;header menu :</TD><TD><input type="checkbox" name="stab_showmenu" value="1"></TD></TR>
                        <TR BGCOLOR="<? echo $farbschema["WEB7"]; ?>"><TD>&nbsp;header searchfields:</TD><TD><input type="checkbox" name="stab_showsearch" value="1"></TD></TR>
                        <TR BGCOLOR="<? echo $farbschema["WEB7"]; ?>"><TD>&nbsp;footer:</TD><TD><input type="checkbox" name="stab_showfooter" value="1"></TD></TR>

                    </TABLE>
                    <BR>
                </div>



                <div ID="new_uform_area" style="display:none;">
                    <TABLE class="formeditorPanel" cellspacing="0" cellpadding="0">
                        <TR><TD class="formeditorPanelHead"><?= $lang[1171] ?></TD></TR>
                        <TR><TD BGCOLOR="<? echo $farbschema[WEB7]; ?>" VALIGN="TOP">

                                <div style=";padding:2px;"><?= $lang[1569] ?>:<BR>
                                    <SELECT NAME="uform_style" STYLE="width:190px">
                                        <OPTION VALUE="1">iframe
                                        <OPTION VALUE="2">div
                                    </SELECT></div>

                                <div style=";padding:2px;"><?= $lang[1177] ?>:<BR>
                                    <SELECT NAME="uform_typ" STYLE="width:190px" onchange="set_uform(this);">
                                        <OPTION VALUE="1">benutzerdefiniert
                                        <OPTION VALUE="2">Unterformular
                                        <OPTION VALUE="3">Tabelle
                                    </SELECT></div>

                                <div id="uform_form" style="display:none;padding:2px;"><?= $lang[1178] ?>:<BR>
                                    <SELECT NAME="uform_form" STYLE="width:190px;">
<?
foreach ($formlist["id"] as $fid => $fval) {
    #if($fval != $form_id AND $formlist["typ"][$fid] == 1){
    echo "<OPTION VALUE=\"" . $fval . "\">" . $formlist["name"][$fid];
    #}
}
?>
                                    </SELECT></div>

                                <div id="uform_table" style="display:none;padding:2px;"><?= $lang[1178] ?>:<BR>
                                    <SELECT NAME="uform_tab" STYLE="width:190px;">
<?
foreach ($gtab["table"] as $tid => $tval) {
    echo "<OPTION VALUE=\"" . $tid . "\">" . $gtab["table"][$tid];
}
?>
                                    </SELECT></div>

                            </TD></TR>
                    </TABLE>
                </div>


                <div ID="new_dbdat_area" style="display:none;">
                    <TABLE class="formeditorPanel no-padding" cellspacing="0" cellpadding="2">
                        <TR><TD class="formeditorPanelHead"><?= $lang[972] ?></TD></TR>
                        <tr><td>
<?
$gtabid = $form["referenz_tab"];
if ($form["form_typ"] != 3 AND $gfield[$gtabid]["id"]) {
    require_once("admin/form/form_tabliste.php");
}
?>
                            </td></tr></table>
                    <br>
                </div>


                <TABLE cellspacing="0" cellpadding="0" class="formeditorPanel">
                    <TR><TD class="formeditorPanelHead"></TD></TR>
                    <TR><TD STYLE="height:15px" ALIGN="CENTER"><INPUT TYPE="BUTTON" STYLE="border:1px solid grey;cursor:pointer" VALUE="<?= $lang[2515] ?>" OnCliCk="send();"></TD></TR>
                </TABLE>

                <BR>
                <TABLE cellspacing="0" cellpadding="0" class="formeditorPanel">
                    <TR><TD class="formeditorPanelHead"><?= $lang[2783] ?></TD></TR></TABLE>
                <div ID="itemlist_area" class="formeditorPanel" style="margin-top:0"></div>

            </FORM>
        </TD></TR></TABLE>