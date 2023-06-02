/*
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */

var s_link = new Array();

var zIndexTop = 100;
var dx = 0, dy = 0, px = 0, py = 0, ex = 0, ey = 0, ax = 0, ay = 0;
var ityp = 1;
var setdrag = "";
var fieldel = null;

var claert;

$(function () {
    $('#container').height(($(window).height()) - 100);
    $('[data-tabadd]').click(tablistadd);
    $('#groupButton').click(addNewGroup);
    $('#deleteButton').click(deleteGroup);
    let $viewEditorPattern = $('#vieweditorPattern');
    $viewEditorPattern.contextmenu(viewEditorContextMenu);
    $viewEditorPattern.click(function () {
        $('#tablist').hide();
    });
    $('#groupSelect').on("change", function() {
        document.form1.submit();
    });
    initViewPatternWindow();
    paint_lines();
});

/***
 * Initialization of dragging, calls endDrag function on mouse up
 * @param evt Event
 * @param el Dragged element
 * @param fel
 */
function iniDrag(evt, el, fel) {
    document.onmouseup = endDrag;
    startDrag(evt, el);
    fieldel = fel;
    ityp = 1;
}

/***
 * Initialization of resize, calls endDrag function on mouse up
 * @param evt Event
 * @param el Resized element
 */
function iniResize(evt, el) {
    document.onmouseup = endDrag;
    startDrag(evt, el);
    ityp = 2;
}

/***
 * Starts drag functionality, when mouse is moved the function calls drag function
 * @param evt Event
 * @param el Dragged/Resized element
 */
function startDrag(evt, el) {

    document.body.style.cursor = 'pointer';

    if (typeof (el) == "object") {
        el1 = el;
        el2 = 0;
    } else {
        el1 = document.getElementById('tabsh_' + el);
        el2 = document.getElementById('selsh_' + el);
    }

    px = evt.pageX;
    py = evt.pageY;


    if (el1.id == 'relationSign') {
        el1.style.visibility = 'visible';
        $(el1).css("left", px + 10);
        $(el1).css("top", py + 3);
    }

    dx = parseInt(el1.offsetLeft);
    dy = parseInt(el1.offsetTop);

    ex = parseInt(el1.offsetWidth);
    ey = parseInt(el1.offsetHeight);

    zIndexTop++;
    el1.zIndex = zIndexTop;
    document.onmousemove = drag;

    return false;
}

function drag(evt) {
    if (el1 != null) {


        ax = evt.pageX;
        ay = evt.pageY;


        // move
        if (ityp == 1) {
            if ((ax - px + dx) < 0) {
                ax = 1 + px + dx;
            }
            if ((ay - py + dy) < 0) {
                ay = 1 + py + dy;
            }
            cl = ax - px + dx;
            ct = ay - py + dy;
            if (cl > 0) {
                el1.style.left = cl + 'px';
            }
            if (ct > 0) {
                el1.style.top = ct + 'px';
            }
            // resize
        } else if (ityp == 2) {
            if ((ax - px + ex) < 100) {
                ax = 100 + px - ex;
            }
            if ((ay - py + ey) < 100) {
                ay = 100 + py - ey;
            }


            el1.style.width = (ax - px + ex) + 'px';
            el1.style.height = (ay - py + ey) + 'px';


            el2.style.width = (el1.style.offsetWidth) + 'px';
            el2.style.height = (ay - py + ey - 50) + 'px';
        }
    }
    paint_lines();
    return false;
}

function endDrag(evt) {
    document.onmouseup = null;
    document.onmousemove = null;
    document.body.style.cursor = 'default';
    if (document.getElementById('relationSign')) {
        document.getElementById('relationSign').style.visibility = 'hidden';
    }

    el1 = null;
    fieldel = null;

    lmbAjax_SaveEditorPattern();
    paint_lines();

    return false;
}

function setDrag() {
    var ar = document.querySelectorAll('[id^="tabsh"]');
    setdrag = document.form1.setdrag.value;
    for (const cc of ar) {
        const size = parseInt(cc.offsetWidth) + "," + parseInt(cc.offsetHeight);
        const pos = parseInt(cc.style.left) + "," + parseInt(cc.style.top);
        const tab = cc.id.substring(6, 100);
        setdrag = setdrag + ":" + tab + ";" + size + ";" + pos;
    }
    document.form1.setdrag.value = setdrag;
}


// ---------------- Sendkeypress----------------------
var stopEvent = 0;

function sendkeydown(evt) {
    if (evt.keyCode == 13 && !evt.shiftKey && !stopEvent && evt.target.localName !== "textarea") {
        setDrag();
        document.form1.submit();
    }
}

var activ_menu = null;

function divclose() {
    if (!activ_menu) {
        limbasDivClose('');
    }
    activ_menu = 0;
}

/***
 * line renderer, only to be used by paint_lines()
 * @param tabid tab id
 * @param vtabid
 * @param fieldid field id
 * @param fel from element
 * @param tel to element
 * @param bzm
 * @param dtp
 */
function js_line(tabid, vtabid, fieldid, fel, tel, bzm, dtp) {

    var tab_el = document.getElementById("tabsh_" + tabid);
    var tab_toel = document.getElementById("tabsh_" + vtabid);
    var from_el = document.getElementById("opt_" + fel);
    var to_el = document.getElementById("opt_" + tel);
    var kont_el = document.getElementById("di_" + bzm);

    if (!from_el || !to_el) {
        return;
    }

    jg = "jg_" + bzm;

    var pos_from_x = from_el.offsetParent.offsetLeft + tab_el.offsetWidth;
    let tabFromTopY = from_el.offsetParent.offsetTop;
    var pos_from_y = tabFromTopY + from_el.offsetTop - from_el.parentNode.parentNode.scrollTop + (from_el.offsetHeight / 2);

    let headerHeight = from_el.parentNode.parentNode.parentNode.firstElementChild.offsetHeight;
    if (pos_from_y < tabFromTopY + headerHeight) {
        pos_from_y = tabFromTopY + headerHeight;
    }
    let tabFromHeight = from_el.parentNode.parentNode.parentElement.offsetHeight;
    let footerHeight = from_el.parentNode.parentNode.parentNode.lastElementChild.offsetHeight;

    if (pos_from_y > tabFromTopY + tabFromHeight - footerHeight) {
        pos_from_y = tabFromTopY + tabFromHeight - footerHeight
    }

    var pos_to_x = to_el.offsetParent.offsetLeft - pos_from_x;
    let tabToTopY = to_el.offsetParent.offsetTop;
    let tabToHeight = to_el.parentNode.parentNode.parentElement.offsetHeight;
    var pos_to_y = tabToTopY - pos_from_y + to_el.offsetTop - to_el.parentNode.parentNode.scrollTop + (to_el.offsetHeight / 2);
    if (pos_to_y < tabToTopY + headerHeight - pos_from_y) {
        pos_to_y = tabToTopY + headerHeight - pos_from_y;
    }

    if (pos_to_y > tabToTopY + tabToHeight - footerHeight - pos_from_y) {
        pos_to_y = tabToTopY + tabToHeight - footerHeight - pos_from_y;
    }

    var svg_lines = document.getElementById("svg_lines");
    svg_lines.innerHTML += '<path id="path_' + bzm + '" d="m ' + pos_from_x + ' ' + pos_from_y + ' l 20 0 l ' + (pos_to_x - 42) + ' ' + pos_to_y + ' l 20 0" fill="none" stroke="black" stroke-width="2" marker-end="url(#arrow)" onclick=\'lmbAjax_fieldinfo(event,3,document.form1.viewid.value,"' + fel + '","' + tel + '","");\'/>';

    to_el.style.color = '#999999';

}

// --- Linien zeichnen ----
function paint_lines(clear) {
    let svg_lines = document.getElementById("svg_lines");
    if(svg_lines != null) {
        svg_lines.innerHTML = "";
    }
    for (var e in s_link) {
        var part = s_link[e].split(",");
        js_line(part[0], part[1], part[2], part[3], part[4], part[5], part[6]);
    }
}

// --- Setze Feldfarbe bei onmouseover ----------
function set_color(el, color) {
    if (el.style.color == 'black' || el.style.color == 'red') {
        el.style.color = color;
    }
}

//show_fieldinfo
function lmbAjax_fieldinfo(evt, act, par1, par2, par3, par4) {
    url = "main_dyns_admin.php";
    actid = "tabschemaInfos&act=" + act + "&par1=" + par1 + "&par2=" + par2 + "&par3=" + par3 + "&par4=" + par4;
    dynfunc = function (result) {
        lmbAjax_fieldinfoPost(result, evt, par4);
    };
    ajaxGet(null, url, actid, null, "dynfunc");
}

//show_fieldinfo
function lmbAjax_fieldinfoPost(value, evt, par4) {
    document.getElementById("fieldinfo").innerHTML = value;
    if (!par4) {
        limbasDivShow('', evt, 'fieldinfo');
    }
}

//drop relation
function lmbDropRelation(relation) {
    document.form1.setrelation.value = relation;
    document.form1.settype.value = 4;
    s_link.length = 0;
    lmbAjax_ViewEditorPattern();
    document.getElementById('fieldinfo').style.visibility = 'hidden';
    document.form1.setrelation.value = '';
    document.form1.settype.value = '';
    paint_lines();
}

// ---------------------- View Functions --------------------------

var viewsortid = null;

// Ajax table request
function lmbAjax_ViewEditorPattern(setdrag) {
    console.log("vieweditorpattern");
    if (setdrag) {
        document.form1.setdrag.value = setdrag + ";add";
    }
    setDrag();
    ajaxGet(null, "main_dyns_admin.php", "VieweditorPattern", null, "lmbAjax_VieweditorPatternPost", "form1");
    document.form1.setdrag.value = '';
}

function lmbAjax_TabEditorPattern(setdrag) {
    if (setdrag) {
        document.form1.setdrag.value = setdrag + ";add";
    }
    setDrag();
    document.form1.setdrag.value = "group:" + document.form1.groupname.value + ";" + document.form1.setdrag.value;
    ajaxGet(null, "main_dyns_admin.php", "VieweditorPattern", null, "lmbAjax_TabeditorPatternPost", "form1");
    document.form1.setdrag.value = '';
}

function lmbAjax_SaveEditorPattern(setdrag) {
    if (setdrag) {
        document.form1.setdrag.value = setdrag + ";add";
    }
    setDrag();
    ajaxGet(null, "main_dyns_admin.php", "VieweditorPattern", null, "lmbAjax_SaveeditorPatternPost", "form1");
    document.form1.setdrag.value = '';
}


// Ajax Preview output
function lmbAjax_VieweditorPatternPost(result) {
    document.getElementById("vieweditorPattern").innerHTML = result;
    ajaxEvalScript(result);
    paint_lines();
}

function lmbAjax_TabeditorPatternPost(result) {
    var resEl = new DOMParser().parseFromString(result, "text/html");
    document.getElementById("vieweditorPattern").innerHTML = resEl.getElementById("vieweditorPattern").innerHTML;
    ajaxEvalScript(result);
    initViewPatternWindow();
    paint_lines();
}

function lmbAjax_SaveeditorPatternPost(result) {
    paint_lines();

}

// add new relation
function iniRelation(evt, el) {
    var lfield = fieldel.id.split('_');
    var rfield = el.id.split('_');

    if (lfield[1] != rfield[1]) {
        document.form1.setrelation.value = fieldel.id + ';' + el.id;
        lmbAjax_ViewEditorPattern();
    }
    document.form1.setrelation.value = '';
}


// Ajax fieldlist
function lmbAjax_EditViewfield(event, el, act, fieldid, formid) {
    if (fieldid) {
        document.form1.setviewfield.value = fieldid;
    } else if (fieldel && fieldel.id && act == 'add') {
        document.form1.setviewfield.value = fieldel.id.substring(4, 900);
    } else if (act == 'addalias') {
        document.form1.setviewfield.value = '1';
    }
    if ((isNaN(fieldid) || isNaN(formid) || fieldid == null || formid == null || fieldid == formid) && act == 'move') {
        document.form1.setviewfield.value = '';
        return;
    }
    if (document.form1.setviewfield.value) {
        ajaxGet(null, "main_dyns_admin.php", "VieweditorFields&act=" + act + "&formid=" + formid, null, "lmbAjax_EditViewfieldPost", "form1");
    }
    document.form1.setviewfield.value = '';
}

// Ajax fieldlist post
function lmbAjax_EditViewfieldPost(value) {
    document.getElementById("lmbViewfieldContainer").innerHTML = value;
}


function lmb_show_relations(name, filter) {

    $(":not(.view_" + name + ")").css('color', '');
    $(".view_" + name).css('color', 'red');

    if (filter) {
        $("li.roottree").removeAttr('hidetree');
        $(".view_" + name).closest('li.roottree').attr('hidetree', '1');
        $("li.roottree:not(li[hidetree='1'])").hide();
    }

}

function lmb_search_relations(filter) {

    $("li.roottree").show();
    $(".list-hierarchy div").css('color', '');
    $("li.roottree").removeAttr('hidetree');

    if (filter) {
        $('div.list-hierarchy div:contains(' + filter + ')').css('color', 'red').closest('li.roottree').attr('hidetree', '1');
        $("li.roottree:not(li[hidetree='1'])").hide();
    }

}

function lmb_search_pause(filter) {
    if (claert) {
        clearTimeout(claert);
    }
    claert = setTimeout(function () {
        lmb_search_relations(filter)
    }, 500);

}

function tablistadd() {
    let tabListId = $(this).data("tabadd");
    lmbAjax_TabEditorPattern(tabListId + ';;'+document.getElementById('tablist').style.left+','+document.getElementById('tablist').style.top);
    $('#li_' + tabListId).hide();
    $('#tablist').hide();
    divclose();
}

function tablistremove() {
    let tabListId = $(this).data("tabremove");
    lmbAjax_TabEditorPattern(tabListId + ';;;1');
    $('#li_' + tabListId).show();
}

function initViewPatternWindow() {
    let $viewPattern = $('#vieweditorPattern');
    $viewPattern.find('[data-tabremove]').click(tablistremove);
}

function addNewGroup() {
    let groupSelect = document.getElementById("groupSelect");
    let newGroupInput = document.getElementById('newGroupInput').value;
    if(newGroupInput.match(/^(\S.*)$/) && !($('option[value="' + newGroupInput + '"]').length)) {
        groupSelect.add(new Option(newGroupInput, newGroupInput, false, true));
        document.form1.submit();
    }
}

function deleteGroup() {
    document.form1.deleteG.value = 1;
}

function viewEditorContextMenu() {
    let groupname = $(this).data("groupname");
    if(groupname) {
        limbasDivShow('',event,'tablist');
    }
    return false;
}