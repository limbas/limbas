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
 * ID: 175
 */
?>
<script language="JavaScript">

    <?php
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

        if ($form["form_typ"] == 1) {
            array_push($reset, 'rect', 'tabulator', 'menue', 'frame');
            if ($umgvar["use_jsgraphics"]) {
                array_push($reset, 'ellipse', 'line');
            }
            echo "$('#new_uform_area').hide();";
            echo "$('#new_tile_area').hide();";
        }

        if ($form["form_typ"] == 2) {
            array_push($reset, 'stab', 'dbsearch', 'tabmenu');
            echo "$('#new_stab_area').hide();";
        }

        if ($form["form_typ"] == 1 || $form["form_typ"] == 2) {
            array_push($reset, 'wflhist', 'reminder', 'dbdat', 'dbdesc', 'dbnew', 'text', 'datum', 'scroll', 'js', 'php', 'submt', 'button', 'inptext', 'inparea', 'inpselect', 'inpcheck', 'inpradio', 'inphidden', 'chart', 'templ', 'frame','tabulator','tab','uform','html');
            echo "$('#new_dbdat_area').hide();";
            echo "$('#new_wflhist_area').hide();";
        }

        foreach ($reset as $id) {
            echo "$('#$id').css('border-style', 'outset');";
            echo "$('#$id').css('background-color', '');";
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
            objst.backgroundColor = '<?= $farbschema['WEB7'] ?>';
            // add fullserach
            if(id == 'dbnew'){

            }

        } else {
            stati.style.display = 'none';
            objst.borderStyle = 'outset';
            objst.backgroundColor = '';
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
                var parentrelpath = new Array();
                var fieldid = new Array();
                var tabgroup = new Array();
                $(".markAsActive").each(function (index, el) {
                    spelling.push(el.getAttribute('lmspelling'));
                    fieldid.push(el.getAttribute('lmfieldid'));
                    gtabid.push(el.getAttribute('lmgtabid'));
                    parentrel.push(el.getAttribute('lmparentrel'));
                    parentrelpath.push( el.getAttribute('lmparentrelpath'));
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
                parent.form_main.document.form1.form_dbdat_parentrelpath.value = parentrelpath.join(";");
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
                } else if (obj == 'templ') {
                    parent.form_main.document.form1.form_templ_id.value = document.form1.templ_id.value;
                    document.getElementById('new_templ_area').style.display = 'none';
                }else if (obj == 'wflhist') {
                    parent.form_main.document.form1.uform_typ.value = document.form1.wfl_id.value;
                } else if (obj == 'tile') {
                    parent.form_main.document.form1.uform_set.value = document.form1.tile_form.value;
                }
                if (obj == 'stab' && document.form1.stab_snap_id[document.form1.stab_snap_id.selectedIndex].value) {
                    parent.form_main.document.form1.form_stab_snapid.value = document.form1.stab_snap_id[document.form1.stab_snap_id.selectedIndex].value;
                    parent.form_main.document.form1.form_stab_show.value = document.form1.stab_showtabs.checked + ';' + document.form1.stab_showmenu.checked + ';' + document.form1.stab_showsearch.checked + ';' + document.form1.stab_showfooter.checked;
                } else if (obj == 'chart' && document.form1.chart_chart_id[document.form1.chart_chart_id.selectedIndex].value) {
                    parent.form_main.document.form1.form_chart_id.value = document.form1.chart_chart_id[document.form1.chart_chart_id.selectedIndex].value;
                }
                document.form1.objekt.value = '';
                parent.form_main.document.form1.submit();
        }
        resetmenu();
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
        parent.form_main.lmb_dropEl('<?= $lang[84] ?>', dv);
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

<div id="lmbAjaxContainer" class="ajax_container"
     style="position: absolute; visibility: hidden;"
     OnClick="activ_menu = 1;"></div>

<TABLE BORDER="0" cellspacing="0" cellpadding="0">
    <TR>
        <TD WIDTH="10">&nbsp;</TD>
        <TD>

            <FORM ENCTYPE="multipart/form-data" ACTION="main_admin.php"
                  METHOD="post" name="form1">
                <input type="hidden"
                                                                name="action" value="setup_form_menu"> <input
                        type="hidden"
                        name="form_id" value="<?= $form_id ?>"> <input type="hidden"
                                                                            name="form_name"
                                                                            value="<?= $form_name ?>"> <input
                        type="hidden" name="referenz_tab" value="<?= $referenz_tab ?>">
                <input type="hidden" name="form_id"
                       value="<?= $form["form_id"] ?>"> <input type="hidden"
                                                                    name="form_typ"
                                                                    value="<?= $form["form_typ"] ?>"> <input
                        type="hidden" name="form_add"> <input type="hidden" name="form_tab">
                <input type="hidden" name="form_tab_el"> <input type="hidden"
                                                                name="objekt"> <input type="hidden" name="aktiv_id">
                <input
                        type="hidden" name="aktiv_tabcontainer"> <input type="hidden"
                                                                        name="aktiv_tabulator"> <input type="hidden"
                                                                                                       name="form_dimension">
                <input type="hidden" name="default_class">


                <TABLE class="formeditorPanel" cellspacing="0" cellpadding="2">
                    <TR>
                        <TD class="formeditorPanelHead" COLSPAN="4"><?= $lang[828] ?></TD>
                    </TR>
                    <TR>
                        <TD VALIGN="TOP"><b><?= $lang[828] ?></TD>
                        <TD><?= $form["name"]; ?></TD>
                    </TR>
                    <TR>
                        <TD VALIGN="TOP"><b><?= $lang[164] ?></TD>
                        <TD><?= $gtab["desc"][$referenz_tab]; ?></TD>
                    </TR>
                    <TR>
                        <TD VALIGN="TOP"><b><?= $lang[925] ?></TD>
                        <TD><?php if ($form["form_typ"] == 1) {
                                echo $lang[1183];
                            } else {
                                echo $lang[1184];
                            } ?></TD>
                    </TR>
                </TABLE>


                <TABLE class="formeditorPanel" cellspacing="0" cellpadding="2">
                    <TR>
                        <TD COLSPAN="4" class="formeditorPanelHead"><?= $lang[2782] ?></TD>
                    </TR>
                    <TR>
                        <TD STYLE="height: 14px;"><B>X:</B></TD>
                        <TD>
                            <INPUT STYLE="width:40px;height:13px;"
                                   NAME="XPOSI" OnChange="parent.form_main.posxy_change(this.value, '');"></TD>
                        <TD>&nbsp;&nbsp;&nbsp;&nbsp;<B>W:</B>&nbsp;&nbsp;
                        </TD>
                        <TD><INPUT TYPE="TEXT"
                                   STYLE="width:40px;height:13px;"
                                   NAME="WPOSI" OnChange="parent.form_main.sizexy_change('', this.value);"></TD>
                    </TR>
                    <TR>
                        <TD STYLE="height: 14px;"><B>Y:</B></TD>
                        <TD>
                            <INPUT STYLE="width:40px;height:13px;"
                                   NAME="YPOSI" OnChange="parent.form_main.posxy_change('', this.value);"></TD>
                        <TD>&nbsp;&nbsp;&nbsp;&nbsp;<B>H:</B>&nbsp;&nbsp;
                        </TD>
                        <TD><INPUT TYPE="TEXT"
                                   STYLE="width:40px;height:13px;"
                                   NAME="HPOSI" OnChange="parent.form_main.sizexy_change(this.value, '');"></TD>
                    </TR>
                    </TD>
                    </TR>
                </TABLE>


                <TABLE cellspacing="0" cellpadding="2" class="formeditorPanel">
                    <TR>
                        <TD class="formeditorPanelHead" COLSPAN="3" align="center"><?= $lang[2331] ?></TD>
                    </TR>

                    <TR>
                        <TD><?= $lang[2581] ?></TD>
                        <TD COLSPAN="2" align="right">
                            <select OnChange="document.form1.default_class.value = this.value; document.form1.submit();" style="width:100px;">
                                <option value="NULL">

                                <?php
                               if (file_exists($umgvar['pfad'] . '/EXTENSIONS/css')) {
                                    $extfiles = read_dir($umgvar['pfad'] . '/EXTENSIONS/css', 0);

                                    $extfiles['name'][] = 'layout.css';
                                    $extfiles['typ'][] = 'file';
                                    $extfiles['path'][] = '/EXTENSIONS/css/layout.css';
                                    $extfiles['ext'][] = 'css';

                                    if ($extfiles['name']) {
                                        foreach ($extfiles['name'] as $key1 => $filename) {
                                            if ($extfiles['typ'][$key1] == 'file' AND $extfiles['ext'][$key1] == 'css') {
                                                $path = lmb_substr($extfiles['path'][$key1], lmb_strlen($umgvar['pfad']), 100);
                                                if ($form['css'] == $path . $filename) {
                                                    $selected = 'SELECTED';
                                                } else {
                                                    $selected = '';
                                                }
                                                echo '<option value="' . $path . $filename . '" ' . $selected . '>' . str_replace('/EXTENSIONS/css/', '', $path) . $filename;
                                            }
                                        }
                                    }
                                }
                                ?>

                            </select>
                        </TD>
                    </TR>

                    <TR>
                        <TD><?= $lang[1170] ?>:</TD>
                        <TD VALIGN="TOP" ALIGN="right"><SELECT NAME="default_font" STYLE="width:60;">
                                <?php
                               foreach ($sysfont as $key => $value) {
                                    echo "<OPTION VALUE=\"" . $value . "\">" . $value . "\n";
                                }
                                ?>
                            </SELECT>

                        <TD VALIGN="TOP" align="right"><INPUT TYPE="TEXT" STYLE="width: 30px;" NAME="default_size" VALUE=""></TD>
                    </TR>

                    <TR>
                        <TD><?= $lang[210] ?> X/Y:</TD>
                        <TD VALIGN="TOP" ALIGN="right"><input id="formX" type="text" OnChange="document.form1.form_dimension.value = this.value + 'x' + document.getElementById('formY').value;
                            document.form1.submit();" STYLE="width:30px;" VALUE="<?= $form["dimension"][0] ?>"></TD>
                        <TD align="right"><input id="formY" OnChange=" document.form1.form_dimension.value = document.getElementById('formX').value + 'x' + this.value;document.form1.submit();" type="text" STYLE="width:30px;" VALUE="<?= $form["dimension"][1] ?>"></TD>

                    <TR>


                    <TR>
                        <TD COLSPAN="2">Raster:</TD>
                        <TD align="right"><INPUT TYPE="TEXT" NAME="raster" VALUE="10" STYLE="width:30px;"></TD>
                    </TR>
                    <TR>
                        <TD COLSPAN="2"><?= $lang[1148] ?>:</TD>
                        <TD align="right"><INPUT TYPE="CHECKBOX" NAME="prop"></TD>
                    </TR>
                    <TR>
                        <TD COLSPAN="2"><?= $lang[2063] ?>:</TD>
                        <TD align="right"><INPUT TYPE="CHECKBOX" NAME="set_zindex"></TD>
                    </TR>
                    <TR>
                        <TD COLSPAN="3"><a href="#" onclick="setNewZindex();"><?= $lang[2067] ?></a></TD>
                    </TR>
                    </TD>
                    </TR>


                    <?php if ($form["form_typ"] == 2) { ?>
                    <TR>
                        <TD>&nbsp;<?= $lang[2456] ?>:</TD>
                        <TD COLSPAN="2" align="right">
                            <SELECT NAME="form_redirect" style="width:100px"
                                            OnChange="parent.form_main.document.form1.form_redirect.value = this.value;parent.form_main.document.form1.submit();">
                                        <OPTION
                                                VALUE="0">
                                            <?php
                                           $bzm = 0;
                                            while ($formlist["id"][$bzm]) {
                                                if ($formlist["id"][$bzm] != $form_id) {
                                                    if ($formlist["id"][$bzm] == $form['redirect']) {
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
                                </TD>
                    </TR>
                    <?php } ?>


                </TABLE>

                <TABLE cellspacing="0" cellpadding="0" class="formeditorPanel">
                    <TR>
                        <TD COLSPAN="10" class="formeditorPanelHead"><?= $lang[2780] ?></TD>
                    </TR>
                    <TR>
                        <TD>
                            <TABLE BORDER="0" cellspacing="0" cellpadding="0">
                                <TR>
                                    <?php if ($form["form_typ"] == 1 or $form["form_typ"] == 2) {$st = "";} else {$st = "none";} ?>
                                    <TD VALIGN="TOP" STYLE="display:<?= $st ?>;"><I ID="text" class="lmb-icon lmb-rep-txt btn" TITLE="<?= $lang[1149] ?>" VALUE="text" OnMouseDown="pressbutton('text', 'inset', '<?= $farbschema['WEB10'] ?>');" OnMouseUp="pressbutton('text', 'outset', '<?= $farbschema['WEB7'] ?>');add('text');send();"></i></TD>
                                    <?php if (($form["form_typ"] == 1 or $form["form_typ"] == 2) and $form["referenz_tab"]) {$st = "";} else {$st = "none";}?>
                                    <TD VALIGN="TOP" STYLE="display:<?= $st ?>;"><i ID="dbdat" class="lmb-icon lmb-rep-db btn" TITLE="<?= $lang[1150] ?>" VALUE="dbdat" OnClick="parent.form_main.document.form1.form_add.value = 'dbdat'; actbutton('dbdat', 'new_dbdat_area', 0);add('dbdat');"></i></TD>
                                    <?php if (($form["form_typ"] == 1 or $form["form_typ"] == 2) and $form["referenz_tab"]) {$st = "";} else {$st = "none";} ?>
                                    <TD VALIGN="TOP" STYLE="display:<?= $st ?>;"><i ID="dbdesc" class="lmb-icon lmb-rep-dbdesc btn" TITLE="<?= $lang[1773] ?>" VALUE="dbdesc" OnClick="parent.form_main.document.form1.form_add.value = 'dbdesc';actbutton('dbdesc', 'new_dbdat_area', 0);add('dbdesc');"></i></TD>
                                    <?php if ($form["form_typ"] == 2 and $form["referenz_tab"]) {$st = "";} else {$st = "none";}?>
                                    <TD VALIGN="TOP" STYLE="display:<?= $st ?>;"><i ID="dbsearch" class="lmb-icon lmb-rep-dbsearch btn" TITLE="<?= $lang[1774] ?>" VALUE="dbsearch" OnClick="parent.form_main.document.form1.form_add.value = 'dbsearch'; actbutton('dbsearch', 'new_dbdat_area', 0);add('dbsearch');"></i></TD>
                                    <?php if ($form["form_typ"] == 2 and $form["referenz_tab"]) {$st = "";} else {$st = "none";}?>
                                    <TD VALIGN="TOP" STYLE="display:<?= $st ?>;"><i ID="dbnew" class="lmb-icon lmb-rep-dbnew btn" TITLE="New dataset" VALUE="dbnew" OnClick="parent.form_main.document.form1.form_add.value = 'dbnew';actbutton('dbnew', 'new_dbdat_area', 0);add('dbnew');"></i></TD>
                                    <?php if ($form["form_typ"] == 2 and $form["referenz_tab"]) {$st = "";} else {$st = "none";}?>
                                    <TD VALIGN="TOP" STYLE="display:<?= $st ?>;"><i ID="tabmenu" class="lmb-icon lmb-rep-table-menu btn" TITLE="Table menu" VALUE="tabmenu" OnMouseDown="pressbutton('tabmenu', 'inset', '<?= $farbschema['WEB10'] ?>');" OnMouseUp="pressbutton('tabmenu', 'outset', '<?= $farbschema['WEB7'] ?>');add('tabmenu');send();"></i>
                                    </TD>
                                    <?php if (($form["form_typ"] == 1 or $form["form_typ"] == 2) and $form["referenz_tab"]) {$st = "";} else {$st = "none";}?>
                                    <TD VALIGN="TOP" STYLE="display:<?= $st ?>;"><i ID="scroll" class="lmb-icon lmb-rep-scroll btn" TITLE="<?= $lang[1173] ?>" VALUE="scroll" OnMouseDown="pressbutton('scroll', 'inset', '<?= $farbschema['WEB10'] ?>');" OnMouseUp="pressbutton('scroll', 'outset', '<?= $farbschema['WEB7'] ?>');add('scroll');send();"></i></TD>
                                </TR>

                                <TR>
                                    <?php if ($umgvar["use_jsgraphics"]) {
                                        if ($form["form_typ"] == 1) { ?>
                                            <TD VALIGN="TOP"><i ID="ellipse" class="lmb-icon lmb-rep-circle btn" TITLE="<?= $lang[1154] ?>" VALUE="ellipse" OnMouseDown="pressbutton('ellipse', 'inset', '<?= $farbschema['WEB10'] ?>');" OnMouseUp="pressbutton('ellipse', 'outset', '<?= $farbschema['WEB7'] ?>');add('ellipse');send();"></i></TD>
                                    <?php }
                                        if ($form["form_typ"] == 1) {$st = "";} else {$st = "none";}?>
                                        <TD VALIGN="TOP" STYLE="display:<?= $st ?>;"><i ID="line" class="lmb-icon lmb-rep-line btn" TITLE="<?= $lang[1152] ?>" VALUE="line" OnMouseDown="pressbutton('line', 'inset', '<?= $farbschema['WEB10'] ?>');" OnMouseUp="pressbutton('line', 'outset', '<?= $farbschema['WEB7'] ?>');add('line');send();"></i></TD>
                                    <?php }

                                    if ($form["form_typ"] == 1) {$st = "";} else {$st = "none";} ?>
                                    <TD VALIGN="TOP" STYLE="display:<?= $st ?>;"><i ID="rect" class="lmb-icon lmb-rep-rect btn" TITLE="<?= $lang[1153] ?>" VALUE="rect" OnMouseDown="pressbutton('rect', 'inset', '<?= $farbschema['WEB10'] ?>');" OnMouseUp="pressbutton('rect', 'outset', '<?= $farbschema['WEB7'] ?>');add('rect');send();"></i>
                                    </TD>
                                    <?php
                                    if ($form["form_typ"] == 1) {$st = "";} else {$st = "none";} ?>
                                    <TD VALIGN="TOP" STYLE="display:<?= $st ?>;"><i ID="menue" class="lmb-icon lmb-rep-menu btn" TITLE="<?= $lang[1946] ?>" VALUE="menue" OnMouseDown="pressbutton('menue', 'inset', '<?= $farbschema['WEB10'] ?>');" OnMouseUp="pressbutton('menue', 'outset', '<?= $farbschema['WEB7'] ?>');add('menue');send();"></i></TD>
                                    <?php if ($form["form_typ"] == 1 or $form["form_typ"] == 2) {$st = "";} else {$st = "none";}?>
                                    <TD VALIGN="TOP" STYLE="display:<?= $st ?>;"><i ID="tabulator" class="lmb-icon lmb-rep-tab btn" TITLE="<?= $lang[2561] ?>" VALUE="menue" OnMouseDown="pressbutton('tabulator', 'inset', '<?= $farbschema['WEB10'] ?>');" OnMouseUp="pressbutton('tabulator', 'outset', '<?= $farbschema['WEB7'] ?>');add('tabulator');send();"></i>
                                    </TD>
                                    <?php if ($form["form_typ"] == 1 OR $form["form_typ"] == 2) {$st = "";} else {$st = "none";}?>
                                    <TD VALIGN="TOP" STYLE="display:<?= $st ?>;"><i ID="frame" class="lmb-icon lmb-rep-frame btn" TITLE="<?= $lang[2661] ?>" VALUE="menue" OnMouseDown="pressbutton('frame', 'inset', '<?= $farbschema['WEB10'] ?>');" OnMouseUp="pressbutton('frame', 'outset', '<?= $farbschema['WEB7'] ?>');add('frame');send();"></i></TD>
                                </TR>

                                <TR>
                                    <?php if ($form["form_typ"] == 2) {$st = "";} else {$st = "none";}?>
                                    <TD VALIGN="TOP" STYLE="display:<?= $st ?>;"><i ID="tab" class="lmb-icon lmb-rep-table btn" TITLE="<?= $lang[164] ?>" VALUE="tab" OnMouseDown="pressbutton('tab', 'inset', '<?= $farbschema['WEB10'] ?>');" OnMouseUp="pressbutton('tab', 'outset', '<?= $farbschema['WEB7'] ?>');add('tab');send();"></i></TD>
                                    <?php if ($form["form_typ"] == 2) {$st = "";} else {$st = "none";} ?>
                                    <TD VALIGN="TOP" STYLE="display:<?= $st ?>;"><i ID="tile" class="lmb-icon lmb-tile btn" TITLE="<?= $lang[2924] ?>" VALUE="tile" OnMouseDown="pressbutton('tile', 'inset', '<?= $farbschema['WEB10'] ?>');" OnMouseUp="pressbutton('tile', 'outset', '<?= $farbschema['WEB7'] ?>');actbutton('tile', 'new_tile_area', 0);add('tile');"></i></TD>
                                    <?php if ($form["form_typ"] == 2 or $form["form_typ"] == 1) {$st = "";} else {$st = "none";} ?>
                                    <TD VALIGN="TOP" STYLE="display:<?= $st ?>;"><i ID="stab" class="lmb-icon lmb-camera btn" TITLE="Snapshot table" VALUE="stab" OnClick="parent.form_main.document.form1.form_add.value = 'stab';actbutton('stab', 'new_stab_area', 0);add('stab');"></i></TD>
                                    <TD VALIGN="TOP" STYLE="display:<?= $st ?>;"><i ID="uform" class="lmb-icon-cus lmb-rep-uform btn" TITLE="<?= $lang[1171] ?>" VALUE="uform" OnClick="parent.form_main.document.form1.form_add.value = 'uform';actbutton('uform', 'new_uform_area', 0);add('uform');"></i></TD>
                                    <?php if ($form["form_typ"] == 1) {$st = "";} else {$st = "none";} ?>
                                    <TD VALIGN="TOP" STYLE="display:<?= $st ?>;"><i ID="tab" class="lmb-icon lmb-rep-tab btn" TITLE="<?= $lang[164] ?>" VALUE="tab" OnMouseDown="pressbutton('tab', 'inset', '<?= $farbschema['WEB10'] ?>');" OnMouseUp="pressbutton('tab', 'outset', '<?= $farbschema['WEB7'] ?>');add('tab');send();"></i></TD>
                                    <TD VALIGN="TOP" STYLE="display:<?= $st ?>;"><i ID="chart" class="lmb-icon lmb-line-chart btn" TITLE="<?= $lang[2117] ?>" VALUE="chart" OnClick="parent.form_main.document.form1.form_add.value = 'chart';actbutton('chart', 'new_chart_area', 0);add('chart');"></i></TD>
                                    <?php if ($form["form_typ"] == 1 or $form["form_typ"] == 2) {$st = "";} else {$st = "none";} ?>
                                    <TD VALIGN="TOP" STYLE="display:<?= $st ?>;"><i ID="bild" class="lmb-icon lmb-rep-pic btn" TITLE="<?= $lang[1151] ?>" VALUE="bild" OnClick="parent.form_main.document.form1.form_add.value = 'bild';actbutton('bild', 'new_bild_area', 0);add('bild');"></i></TD>
                                    <?php if ($form["form_typ"] == 1) {$st = "";} else {$st = "none";} ?>
                                    <TD VALIGN="TOP" STYLE="display:<?= $st ?>;"><i ID="reminder" class="lmb-icon lmb-reminder btn" TITLE="<?= $lang[425] ?> " VALUE="php" OnMouseDown="pressbutton('reminder', 'inset', '<?= $farbschema['WEB10'] ?>');" OnMouseUp="pressbutton('reminder', 'outset', '<?= $farbschema['WEB7'] ?>');add('reminder');send();"></i></TD>
                                    <?php if ($form["form_typ"] == 1) {$st = "";} else {$st = "none";} ?>
                                    <TD VALIGN="TOP" STYLE="display:<?= $st ?>;"><i ID="wflhist" class="lmb-icon lmb-icon-cus lmb-workflow-car btn" TITLE="<?= $lang[2035] ?> <?= $lang[1134] ?>" VALUE="php" OnClick="parent.form_main.document.form1.form_add.value = 'wflhist';actbutton('wflhist', 'new_wflhist_area', 0);add('wflhist');"></i></TD>
                                    <?php if ($form["form_typ"] == 1 OR $form["form_typ"] == 2) {$st = "";} else {$st = "none";} ?>
                                    <TD VALIGN="TOP" STYLE="display:<?= $st ?>;"><i ID="templ" class="lmb-icon lmb-code btn" TITLE="HTML Template" VALUE="html" OnClick="parent.form_main.document.form1.form_add.value = 'wflhist';actbutton('templ', 'new_templ_area', 0);add('templ');"></i></TD>

                                </TR>

                                <TR>
                                    <?php if ($form["form_typ"] == 1 or $form["form_typ"] == 2) {$st = "";} else {$st = "none";} ?>
                                    <TD VALIGN="TOP" STYLE="display:<?= $st ?>;"><i ID="submt" class="lmb-icon lmb-rep-submit btn" TITLE="<?= $lang[1174] ?>" VALUE="submt" OnMouseDown="pressbutton('submt', 'inset', '<?= $farbschema['WEB10'] ?>');" OnMouseUp="pressbutton('submt', 'outset', '<?= $farbschema['WEB7'] ?>');add('submt');send();"></i></TD>
                                    <?php if ($form["form_typ"] == 1 or $form["form_typ"] == 2) {$st = "";} else {$st = "none";} ?>
                                    <TD VALIGN="TOP" STYLE="display:<?= $st ?>;"><i ID="button" class="lmb-icon lmb-rep-button btn" TITLE="Button" VALUE="submt" OnMouseDown="pressbutton('button', 'inset', '<?= $farbschema['WEB10'] ?>');" OnMouseUp="pressbutton('button', 'outset', '<?= $farbschema['WEB7'] ?>');add('button');send();"></i></TD>
                                    <?php if ($form["form_typ"] == 1 or $form["form_typ"] == 2) {$st = "";} else {$st = "none";} ?>
                                    <TD VALIGN="TOP" STYLE="display:<?= $st ?>;"><i ID="inptext" class="lmb-icon lmb-rep-text btn" TITLE="<?= $lang[1947] ?>" VALUE="inptext" OnMouseDown="pressbutton('inptext', 'inset', '<?= $farbschema['WEB10'] ?>');" OnMouseUp="pressbutton('inptext', 'outset', '<?= $farbschema['WEB7'] ?>');add('inptext');send();"></i></TD>
                                    <?php if ($form["form_typ"] == 1 or $form["form_typ"] == 2) {$st = "";} else {$st = "none";}?>
                                    <TD VALIGN="TOP" STYLE="display:<?= $st ?>;"><i ID="inparea" class="lmb-icon lmb-rep-area btn" TITLE="<?= $lang[1948] ?>" VALUE="inparea" OnMouseDown="pressbutton('inparea', 'inset', '<?= $farbschema['WEB10'] ?>');" OnMouseUp="pressbutton('inparea', 'outset', '<?= $farbschema['WEB7'] ?>');add('inparea');send();"></i></TD>
                                    <?php if ($form["form_typ"] == 1 or $form["form_typ"] == 2) {$st = "";} else {$st = "none";}?>
                                    <TD VALIGN="TOP" STYLE="display:<?= $st ?>;"><i ID="inpselect" class="lmb-icon lmb-rep-select btn" TITLE="<?= $lang[1949] ?>" VALUE="inpselect" OnMouseDown="pressbutton('inpselect', 'inset', '<?= $farbschema['WEB10'] ?>');" OnMouseUp="pressbutton('inpselect', 'outset', '<?= $farbschema['WEB7'] ?>');add('inpselect');send();"></i></TD>
                                    <?php if ($form["form_typ"] == 1 or $form["form_typ"] == 2) {$st = "";} else {$st = "none";}?>
                                    <TD VALIGN="TOP" STYLE="display:<?= $st ?>;"><i ID="inpcheck" class="lmb-icon lmb-rep-check btn" TITLE="<?= $lang[1950] ?>" VALUE="inpcheck" OnMouseDown="pressbutton('inpcheck', 'inset', '<?= $farbschema['WEB10'] ?>');" OnMouseUp="pressbutton('inpcheck', 'outset', '<?= $farbschema['WEB7'] ?>');add('inpcheck');send();"></i></TD>
                                    <?php if ($form["form_typ"] == 1 or $form["form_typ"] == 2) {$st = "";} else {$st = "none";}?>
                                    <TD VALIGN="TOP" STYLE="display:<?= $st ?>;"><i ID="inpradio" class="lmb-icon lmb-rep-radio btn" TITLE="<?= $lang[1951] ?>" VALUE="inpradio" OnMouseDown="pressbutton('inpradio', 'inset', '<?= $farbschema['WEB10'] ?>');" OnMouseUp="pressbutton('inpradio', 'outset', '<?= $farbschema['WEB7'] ?>');add('inpradio');send();"></i></TD>
                                    <?php if ($form["form_typ"] == 1 or $form["form_typ"] == 2) {$st = "";} else {$st = "none";}?>
                                    <TD VALIGN="TOP" STYLE="display:<?= $st ?>;"><i ID="inphidden" class="lmb-icon lmb-rep-hidden btn" TITLE="<?= $lang[1968] ?>" VALUE="inphidden" OnMouseDown="pressbutton('inphidden', 'inset', '<?= $farbschema['WEB10'] ?>');" OnMouseUp="pressbutton('inphidden', 'outset', '<?= $farbschema['WEB7'] ?>');add('inphidden');send();"></i></TD>
                                 </TR>

                                 <TR>
                                    <?php if ($form["form_typ"] == 1 or $form["form_typ"] == 2) {$st = "";} else {$st = "none";} ?>
                                    <TD VALIGN="TOP" STYLE="display:<?= $st ?>;"><i ID="js" class="lmb-icon lmb-rep-js btn" TITLE="<?= $lang[1505] ?>" VALUE="js" OnMouseDown="pressbutton('js', 'inset', '<?= $farbschema['WEB10'] ?>');" OnMouseUp="pressbutton('js', 'outset', '<?= $farbschema['WEB7'] ?>');add('js');send();"></i></TD>
                                    <?php if ($form["form_typ"] == 1 OR $form["form_typ"] == 2) {$st = "";} else {$st = "none";} ?>
                                    <TD VALIGN="TOP" STYLE="display:<?= $st ?>;"><i ID="php" class="lmb-icon lmb-rep-php btn" TITLE="<?= $lang[1772] ?>" VALUE="php" OnMouseDown="pressbutton('php', 'inset', '<?= $farbschema['WEB10'] ?>');" OnMouseUp="pressbutton('php', 'outset', '<?= $farbschema['WEB7'] ?>');add('php');send();"></i></TD>
                                    <?php if ($form["form_typ"] == 1) {$st = "";} else {$st = "none";} ?>
                                    <TD VALIGN="TOP" STYLE="display:<?= $st ?>;"><i ID="datum" class="lmb-icon lmb-rep-date btn" TITLE="<?= $lang[197] ?>" VALUE="datum" OnMouseDown="pressbutton('datum', 'inset', '<?= $farbschema['WEB10'] ?>');" OnMouseUp="pressbutton('datum', 'outset', '<?= $farbschema['WEB7'] ?>');add('datum');send();"></i>
                                </TR>
                            </TABLE>
                        </TD>
                    </TR>
                </TABLE>


                <div ID="new_bild_area" style="display: none;">
                    <TABLE class="formeditorPanel" cellspacing="0" cellpadding="2">
                        <TR>
                            <TD class="formeditorPanelHead" colspan="2" STYLE="height: 15px"></TD>
                        </TR>
                        <TR>
                            <TD colspan="2">
                                <INPUT TYPE="FILE" NAME="new_pic" SIZE="20" STYLE="width:210px;">
                            </TD>
                        </TR>

                        <TR>
                            <TD><?= $lang[925] ?></td>
                            <td><SELECT STYLE="width: 60px;" NAME="pic_type">
                                    <OPTION VALUE="jpg">jpg

                                    <OPTION VALUE="png">png

                                    <OPTION VALUE="gif">gif

                                </SELECT></td>
                        </tr>

                        <TR>
                            <TD HEIGHT="20"><?= $lang[1176] ?></td>
                            <td><SELECT STYLE="width: 60px;" NAME="pic_compress">
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

                                </SELECT></TD>
                        </TR>
                        <TR ID="send_bild_area" style="display: none;">
                            <TD STYLE="height: 30px;" VALIGN="CENTER">&nbsp;<SPAN ID="uploadlevel" STYLE="width:1px;height:15px;border:2px inset grey;background-color:<?= $farbschema['WEB10'] ?>">&nbsp;</SPAN>
                            </TD>
                        </TR>
                    </TABLE>
                    <BR>
                </div>


                <div ID="new_stab_area" style="display: none;">
                    <TABLE cellspacing="0" cellpadding="0"
                           STYLE="border: 1px solid grey; width: 210px">
                        <TR>
                            <TD COLSPAN="2" STYLE="height: 15px"></TD>
                        </TR>
                        <TR>
                            <TD COLSPAN="2" VALIGN="TOP">&nbsp;snapshot:<BR> &nbsp;<SELECT
                                        NAME="stab_snap_id" STYLE="width: 190px">
                                    <OPTION>
                                        <?php
                                       if ($snap["name"][$form["referenz_tab"]]) {
                                            foreach ($snap["name"][$form["referenz_tab"]] as $key => $value) {
                                                echo "<OPTION VALUE=\"" . $key . "\">" . $value;
                                            }
                                        }
                                        ?>
                                </SELECT></TD>
                        </TR>

                        <TR BGCOLOR="<?= $farbschema["WEB7"] ?>">
                            <TD>&nbsp;tabulator:</TD>
                            <TD><input type="checkbox" name="stab_showtabs" value="1"></TD>
                        </TR>
                        <TR BGCOLOR="<?= $farbschema["WEB7"] ?>">
                            <TD>&nbsp;header menu :</TD>
                            <TD><input type="checkbox" name="stab_showmenu" value="1"></TD>
                        </TR>
                        <TR BGCOLOR="<?= $farbschema["WEB7"] ?>">
                            <TD>&nbsp;header searchfields:</TD>
                            <TD><input type="checkbox" name="stab_showsearch" value="1"></TD>
                        </TR>
                        <TR BGCOLOR="<?= $farbschema["WEB7"] ?>">
                            <TD>&nbsp;footer:</TD>
                            <TD><input type="checkbox" name="stab_showfooter" value="1"></TD>
                        </TR>

                    </TABLE>
                    <BR>
                </div>


                <div ID="new_uform_area" style="display: none;">
                    <TABLE class="formeditorPanel" cellspacing="0" cellpadding="0">
                        <TR>
                            <TD class="formeditorPanelHead"><?= $lang[1171] ?></TD>
                        </TR>
                        <TR>
                            <TD>

                                <div style="padding: 2px;"><?= $lang[1569] ?>:<BR> <SELECT
                                            NAME="uform_style" STYLE="width: 90%">
                                        <OPTION VALUE="1">iframe

                                        <OPTION VALUE="2">div

                                    </SELECT>
                                </div>

                                <div style="padding: 2px;"><?= $lang[925] ?>:<BR> <SELECT
                                            NAME="uform_typ" STYLE="width: 90%"
                                            onchange="set_uform(this);">
                                        <OPTION VALUE="1"><?=$lang[1986]?>

                                        <OPTION VALUE="2"><?=$lang[1171]?>

                                        <OPTION VALUE="3"><?=$lang[577]?>

                                    </SELECT>
                                </div>

                                <div id="uform_form" style="display: none; padding: 2px;"><?= $lang[1171] ?>:<BR>
                                    <SELECT NAME="uform_form" STYLE="width: 90%;">
                                        <?php
                                       foreach ($formlist["id"] as $fid => $fval) {
                                            // if($fval != $form_id AND $formlist["typ"][$fid] == 1){
                                            echo "<OPTION VALUE=\"" . $fval . "\">" . $formlist["name"][$fid];
                                            // }
                                        }
                                        ?>
                                    </SELECT>
                                </div>

                                <div id="uform_table" style="display: none; padding: 2px;"><?= $lang[1171] ?>:<BR>
                                    <SELECT NAME="uform_tab" STYLE="width: 90%;">
                                        <?php
                                       foreach ($gtab["table"] as $tid => $tval) {
                                            echo "<OPTION VALUE=\"" . $tid . "\">" . $gtab["table"][$tid];
                                        }
                                        ?>
                                    </SELECT>
                                </div>

                            </TD>
                        </TR>
                    </TABLE>
                </div>


                <div ID="new_tile_area" style="display: none;">
                    <TABLE class="formeditorPanel" cellspacing="0" cellpadding="0">
                        <TR>
                            <TD class="formeditorPanelHead"><?= $lang[1171] ?></TD>
                        </TR>
                        <TR>
                            <TD BGCOLOR="<?= $farbschema['WEB7'] ?>" VALIGN="TOP">

                                <div id="uform_form" style="padding: 2px;">
                                    <SELECT NAME="tile_form" STYLE="width: 190px;">
                                        <?php
                                       foreach ($formlist["id"] as $fid => $fval) {
                                            if($fval != $form_id AND $formlist["typ"][$fid] == 2 AND $formlist["ref_tab"][$fid] == $form["referenz_tab"] ){
                                            echo "<OPTION VALUE=\"" . $fval . "\">" . $formlist["name"][$fid];
                                            }
                                        }
                                        ?>
                                    </SELECT>
                                </div>

                            </TD>
                        </TR>
                    </TABLE>
                </div>


                <div ID="new_dbdat_area" style="display: none;">
                    <TABLE class="formeditorPanel no-padding" cellspacing="1" cellpadding="2">
                        <TR>
                            <TD class="formeditorPanelHead"><?= $lang[972] ?></TD>
                        </TR>
                        <TR id="dbdat_area_fullsearch">
                            <TD onclick="add_dbfield(this,event);" lmfieldid="0" lmgtabid="<?= $referenz_tab?>" lmspelling="<?= $lang[2922] ?>" style="cursor:pointer"><i class="lmb-icon lmb-rep-dbsearch" title="<?= $lang[1774] ?>"></i><?= $lang[2922] ?><hr></TD>
                        </TR>
                        <tr>
                            <td>
                                <?php
                               $gtabid = $form["referenz_tab"];
                                if ($form["form_typ"] != 3 and $gfield[$gtabid]["id"]) {
                                    require_once("admin/form/form_tabliste.php");
                                }
                                ?>
                            </td>
                        </tr>
                    </table>
                    <br>
                </div>


                <div id="new_chart_area" style="display: none;">
                    <TABLE cellspacing="0" cellpadding="2" class="formeditorPanel">
                        <TR>
                            <TD class="formeditorPanelHead" align="center"><?= $lang[2119] ?></TD>
                        </TR>
                        <TR>
                            <TD>
                                <SELECT NAME="chart_chart_id" STYLE="width:95%;">
                                    <option>
                                        <?php
                                       foreach ($gdiaglist as $keyk => $valuek) {
                                            foreach ($valuek["name"] as $key => $value) {
                                                echo "<OPTION VALUE=\"" . $key . "\">" . $value;
                                            }
                                        }
                                        ?>
                                </SELECT>
                            </TD>
                        </TR>
                    </TABLE>
                </div>

                <div id="new_wflhist_area" style="display:none;">
                    <br>
                    <table cellspacing="0" cellpadding="2" class="formeditorPanel">
                        <tr>
                            <td class="formeditorPanelHead" align="center">Workflow History</td>
                        </tr>
                        <tr>
                            <td>
                                <select name="wfl_id" style="width:95%;">
                                    <?php
                                    foreach ($gwfl as $wflId => $wflData) {
                                        echo '<option value="' . $wflId . '">' . $wflData['name'] . '</option>';
                                    }
                                    ?>
                                </select>
                            </td>
                        </tr>
                    </table>
                </div>


                <div ID="new_templ_area" style="display:none;">
                    <TABLE cellspacing="0" cellpadding="0" class="formeditorPanel">
                        <TR>
                            <TD class="formeditorPanelHead">Template</TD>
                        </TR>
                        <TR>
                            <TD VALIGN="TOP">
                                <div style="padding:2px;">
                                    <SELECT NAME="templ_id" STYLE="width:190px;">
                                        <option>
                                            <?php
                                            foreach ($gtab['table'] as $rkey => $rval) {
                                                if ($gtab['typ'][$rkey] == 8) {
                                                    echo "<OPTION VALUE=\"" . $rkey . "\">" . $rval;
                                                }
                                            }
                                            ?>
                                    </SELECT></div>
                            </TD>
                        </TR>
                    </TABLE>
                </div>

                <TABLE cellspacing="0" cellpadding="0" class="formeditorPanel">
                    <TR>
                        <TD class="formeditorPanelHead"></TD>
                    </TR>
                    <TR>
                        <TD STYLE="height: 15px" ALIGN="CENTER">
                            <INPUT TYPE="BUTTON" STYLE="border: 1px solid grey; cursor: pointer" VALUE="<?= $lang[33] ?>" OnCliCk="send();">
                        </TD>
                    </TR>
                </TABLE>


                <TABLE cellspacing="0" cellpadding="0" class="formeditorPanel">
                    <TR>
                        <TD class="formeditorPanelHead"><?= $lang[2783] ?></TD>
                    </TR>
                    <tr><td><div ID="itemlist_area" style="margin-top: 0"></div></td></tr>
                </TABLE>

            </FORM>
        </TD>
    </TR>
</TABLE>