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
	watcher = open("main_admin.php?action="+ PARAMETER+ "" ,"watcher","toolbar=0,location=0,status=0,menubar=0,scrollbars=0,resizable=0,width=400,height=500");
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
		<?php //firefox without getElementByName
		if(lmb_strpos($_SERVER["HTTP_USER_AGENT"],"MSIE")<1){?>
		child = document.getElementsByName(NR);

		for(i=0;i<child.length;i++){
			child[i].style.name='';
	    	document.getElementById('pfeil' + NR).src = icon['pfeil_u'].src;
		}

		<?php //IE without.name
		}else{?>

		child = document.getElementById(TABLE_TOP).getElementsByTagName("TR");

		for(i=0;i<child.length;i++){
			if(child[i].name==NR){
				child[i].style.name='<<<';
		    	document.getElementById('pfeil' + NR).src = icon['pfeil_u'].src;
			}
		}
		<?php }?>
	}
}



function mainMenu(menu){
	<?php
	foreach($menu as $key1 => $menuType){
	    echo '$("#' . $key1 . '").hide();';
	}
	?>
    $('#' + menu)
        .show()
        .find('.lmbTableSearch')
        .focus();
}

function hideShow(name, show=false, noAjax=false){

	var el = document.getElementById('CONTENT_' + name);
	if(el.style.display=='none' || show){

		$(el).show('fast');
		pic = document.getElementById('HS' + name);
		if(pic){
                        $( pic ).removeClass("lmb-angle-down");
                        $( pic ).addClass("lmb-angle-up");
		}
		show = 1;
	}else{
		$(el).hide('fast');
		pic = document.getElementById('HS' + name);
		if(pic){
                        $( pic ).removeClass("lmb-angle-up");
                        $( pic ).addClass("lmb-angle-down");
		}
		show = 0;
	}
	if (!noAjax) {
        ajaxGet(null,'main_dyns.php','layoutSettings&menu='+name+'&show='+show,null);
    }
}

function hideShowSub(div, elt, noAjax=false){
	var eltDiv = document.getElementById(div);

	if(!eltDiv) return;

	var eltTr = eltDiv.getElementsByTagName("TR");
	if(!eltTr) return;

    var arrowIcon = $('i[id^=arrowSub' + elt + ']').filter(':visible').first(); // because the id was given twice, once in a hidden div
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
	if (!noAjax) {
        ajaxGet(null, 'main_dyns.php', 'layoutSettings&submenu=' + div + '_' + elt + '&show=' + show, null);
    }
}

hide_frame_size = 200;
function hide_frame(){
    var hiddenDiv = $('#hiddenframe');
    var multiDiv = $('#multiframe');
    var navFrame = $('iframe#nav', top.document);
    var navFrameWidth = navFrame.width();

    var newWidth;
    var frameSize;
    if (navFrameWidth <= 30) {
        newWidth = hide_frame_size;
        hiddenDiv.hide();
        multiDiv.show();
        frameSize = 0;
    } else {
        newWidth = 15;
        hiddenDiv.show();
        multiDiv.hide();
        frameSize = newWidth;
    }

    ajaxGet(null, 'main_dyns.php', 'layoutSettings&frame=nav&size=' + frameSize, null);
    navFrame.width(newWidth);

    document.onmouseup = null;
    document.onmousemove = null;
    return false;
}




var dropel = null;
var dropel_width = null;
var posx = null;
var elwidth = null;
var scrollbarWidth = null;
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

    var mainframe = top.document.getElementById("nav").contentWindow.document.documentElement;
    scrollbarWidth = mainframe.scrollHeight > mainframe.clientHeight ? lmbGetScrollbarWidth() : 0;
	return false;
}

// https://stackoverflow.com/questions/13382516/getting-scroll-bar-width-using-javascript
function lmbGetScrollbarWidth() {
    var outer = document.createElement("div");
    outer.style.visibility = "hidden";
    outer.style.width = "100px";
    outer.style.msOverflowStyle = "scrollbar"; // needed for WinJS apps

    document.body.appendChild(outer);

    var widthNoScroll = outer.offsetWidth;
    // force scrollbars
    outer.style.overflow = "scroll";

    // add innerdiv
    var inner = document.createElement("div");
    inner.style.width = "100%";
    outer.appendChild(inner);

    var widthWithScroll = inner.offsetWidth;

    // remove divs
    outer.parentNode.removeChild(outer);

    return widthNoScroll - widthWithScroll;
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
    var evw; // drag width
    if(browser_ns5) {
		evw = e.screenX - posx;
	} else {
		evw = window.event.screenX - posx;
	}

    // 5px minimum drag distance
    if(Math.abs(evw) < 5) { return false; }

    // destination width
    var dw = evw + elwidth + scrollbarWidth;
    if (evw < 0) {
        dw += 10;
    }

    // max/min width
	if(dw > 400 || dw < 50) { return false; }

	// catch click event after resize
    var captureClick = function(e) {
        e.stopPropagation(); // Stop the click from being propagated.
        this.removeEventListener('click', captureClick, true); // cleanup
    };
    dropel.addEventListener(
        'click',
        captureClick,
        true
    );

	// resize frame
    $('iframe#nav', top.document).width(dw);

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
    var url = '';
    if(verkn_ID && verkn_tabid && verkn_fieldid){
        url += '&verkn_ID='+verkn_ID+'&verkn_tabid='+verkn_tabid+'&verkn_fieldid='+verkn_fieldid+'+&verknpf=1&verkn_showonly=1';
    }
    if(form_id){
        url += '&form_id='+form_id;
    }

	parent.main.location.href='main.php?action=gtab_change&gtabid='+gtabid+'&ID='+ID+url;
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

var typingTimer;
var typingInterval = 500; // ms
function lmbFilterTablesTimer(event, textDomEl, navID) {
    clearTimeout(typingTimer);

    if (event.code === "Enter" || event.code === "Escape") {
        // filter now
        lmbFilterTables(event, textDomEl, navID, true);
    } else {
        // filter tables after <typingInterval> ms without keyup
        typingTimer = setTimeout(function() { lmbFilterTables(event, textDomEl, navID); }, typingInterval);
    }
}

function lmbFilterTables(event, textDomEl, navID, enterPressed=false) {
    const nav = $('#' + navID);
    const textEl = $(textDomEl);

    // TODO also close/open level0 groups
    // TODO reset changes made while clicking on open/close icon while searching

    /**
     * Switches icon classes so that it looks as if the group was popped up
     * @param elem the jquery objects of the <i> element
     * @param open whether to open or to close the group
     */
    const openIcon = function(elem, open) {
        var a;
        var b;
        if (open) {
            a = 'lmb-caret-right';
            b = 'lmb-caret-down';
        } else {
            a = 'lmb-caret-down';
            b = 'lmb-caret-right';
        }

        if (elem.hasClass(a)) {
            elem.removeClass(a)
                .addClass(b)
                .addClass('lmb-table-search-icon-replace');
        }
    };

    /**
     * Removes all .lmb-table-search classes
     */
    const resetClasses = function(elem) {
        elem.find('.lmb-table-search-hide').removeClass('lmb-table-search-hide');
        elem.find('.lmb-table-search-popup').removeClass('lmb-table-search-popup').hide();
        elem.find('[lmb-table-search-onclick]').each(function() {
            $(this).attr('onclick', $(this).attr('lmb-table-search-onclick'));
            $(this).removeAttr('lmb-table-search-onclick');
        });
        elem.find('i.lmb-table-search-icon-replace').each(function() {
            openIcon($(this), false);
        }).removeClass('lmb-table-search-icon-replace');
        elem.find('.lmb-table-search-result').removeClass('lmb-table-search-result');
    };

    // restore to defaults
    resetClasses(nav);

    // clear text on escape key
    if (event && event.keyCode === 27) {
        $(textEl).val('');
    }

    // abort if no text
    const text = $(textEl).val().toLowerCase();
    const textUpper = text.toUpperCase();
    if (!text) {
        return;
    }

    /**
     * Replaces the default onclick with a custom onclick that prevents ajax calls
     */
    const replaceOnclick = function(elem) {
        const onclick = elem.attr('onclick');
        elem.attr('lmb-table-search-onclick', onclick);
        elem.attr('onclick', onclick
            .replace(/hideShowSub\((.*?)\)/, "$`hideShowSub($1, true)$'")
            .replace(/hideShow\((.*?)\)/, "$`hideShow($1, false, true)$'"));
    };

    /**
     * Recursively traverses the dom to filter for the search string
     * @param body
     */
    const filterRec = function(body) {
        // get all sub-trs, but exclude the contents of table groups
        const subEntries = body.children('tbody').children('tr').filter(function() {
            if ($(this).is('[id^="subElt_"]')) {
                return $(this).attr('id') === $(this).parentsUntil('[id^="subElt_"]').last().parent().attr('id');
            } else if ($(this).is(':last-child')) {
                return false;
            }
            return true;
        });
        if (!subEntries.length) {
            return false;
        }

        var oneVisible = false;
        subEntries.each(function() {
            const subEntry = $(this);

            // get link (user)
            var link = subEntry.children('td').children('a.lmbMenuItemBodyNav');
            var attrEl = link.children('[data-lmb-type]');
            if (!link.length) {
                // get link (admin)
                link = subEntry.children('td').children().children('a.lmbMenuItemBodyNav');
                attrEl = link.parent();
            }

            // text found?
            var textFound = false;
            if (link.length) {
                // contains text, but doesn't contain "new dataset"
                if (link.text().toLowerCase().indexOf(text) >= 0 && link.text().indexOf('<?= $lang[2741] ?>') < 0) {
                    textFound = true;
                }

                // tabid
                var tabID = attrEl.attr('data-lmb-tabid');
                if (tabID && tabID === text) {
                    textFound = true;
                }

                // form/rep/snapshot id
                var anyID = attrEl.attr('data-lmb-id');
                if (anyID && anyID === text) {
                    textFound = true;
                }

                // physical table name
                var tabName = attrEl.attr('data-lmb-table');
                if (tabName && tabName.toLowerCase().indexOf(text) >= 0) {
                    textFound = true;
                }

                // relations
                var relations = attrEl.data('lmb-relations');
                if (relations && relations.indexOf(textUpper) >= 0) {
                    textFound = true;
                }
            }

            // find content of table group
            const subBodyParent = subEntry.nextUntil('tr:not([id^="subElt_"])').last();
            const subBody = subBodyParent.children('td').children('table');

            // check recursively if any child is visible
            const showChildren = filterRec(subBody);
            if (showChildren) {
                oneVisible = true;

                // children shown -> also show entry and body
                if (subEntry.css('display') === 'none') {
                    subEntry.addClass('lmb-table-search-popup').show();
                }
                if (subBody.css('display') === 'none') {
                    subBody.addClass('lmb-table-search-popup').show();
                }

                // show all parents
                subBody.parentsUntil($(this), ':hidden')
                    .filter(function() {
                        if ($(this).is('tr[id^=subElt_]')) {
                            const icon = $(this).prev('tr').children('td.lmbMenuItemBodyNav').children('i');
                            openIcon(icon, true);
                        }
                        return $(this).css('display') === 'none';
                    })
                    .addClass('lmb-table-search-popup')
                    .show();
            } else {
                if (subBodyParent.css('display') === 'none') {
                    resetClasses(subBodyParent);
                } else {
                    subBody.addClass('lmb-table-search-hide');
                }
            }

            if (textFound) {
                subEntry.addClass('lmb-table-search-result');
                if (subEntry.css('display') === 'none') {
                    subEntry.addClass('lmb-table-search-popup').show();
                }
                oneVisible = true;
            } else if (!showChildren) {
                subEntry.addClass('lmb-table-search-hide');
                subBody.addClass('lmb-table-search-hide');
            }


        });
        return oneVisible;
    };

    nav.children('table').each(function(index) {
        // skip search bar
        if (index === 0) {
            return;
        }

        // filter table group (level 0)
        const trs = $(this).children('tbody').children('tr');
        const body = $(trs.get(1)).children('td').children('div.lmbMenuHeaderNavContent').children('table.lmbMenuBodyNav');
        if (!body || !filterRec(body)) {
           $(this).addClass('lmb-table-search-hide');
        }

        // replace onclick with no-ajax-onclick
        const carets = $('#244').find('td.lmbMenuItemBodyNav:visible:not([lmb-table-search-onclick])').each(function() {
            replaceOnclick($(this));
        });

        // replace top-level onclick
        const caret = $(trs.get(0)).children('td.lmbMenuHeaderNav:not([lmb-table-search-onclick])');
        if (caret.length) {
            replaceOnclick(caret);
        }
    });

    // enter pressed and only one search result -> perform click
    if (enterPressed) {
        const results = nav.find('.lmb-table-search-result');
        if (results.length === 1) {
            results.find('a').click();
        }
    }
}

// add clicked menu item to favorites
function addToFavorites(evt) {
    // only add if shift is pressed
    if (!evt.shiftKey)
        return;

    // stop click event
    evt.preventDefault();
    evt.stopImmediatePropagation();
    evt.stopPropagation();

    // get menu item id
    const target = $(this);
    const type = target.attr("data-lmb-type");
    const tabid = target.attr("data-lmb-tabid");
    const id = target.attr("data-lmb-id");
    const idStr = id ? ("&id=" + id) : "";

    ajaxGet(null, "main_dyns.php", "addToFavorites&type=" + type +"&tabid=" + tabid + idStr, null, function() {
        document.location.href = "main.php?&action=nav&sparte=gtab&tab_group=1&refresh=no";
    });
}
// add star icon to menu item
function addFavIconTo(elem) {
    if (elem.children().not("i").length > 0) {
        if (elem.children().children("i[data-lmb-fav-icon]").length > 0)
            return;
        elem.children().first().append("<i class=\"lmb-icon lmb-fav\" data-lmb-fav-icon></i>");
    } else {
        if (elem.children("i[data-lmb-fav-icon]").length > 0)
            return;
        elem.append("<i class=\"lmb-icon lmb-fav\" data-lmb-fav-icon></i>");
    }
}
// remove star icon from menu item
function removeFavIconFrom(elem) {
    if (elem.children().not("i").length > 0) {
        elem.children().children("i[data-lmb-fav-icon]").remove();
    } else {
        elem.children("i[data-lmb-fav-icon]").remove();
    }
}
// add onclick listener to add menu item to favorites
$(function() {
    $("[data-lmb-type][data-lmb-tabid]").on('click', addToFavorites);

    $('body').on('mousemove', function(evt) {
        var added = ($(this).attr('data-lmb-added') === 'true');
        if (evt.shiftKey && !added) {
            $(this).attr('data-lmb-added', 'true');
            $("[data-lmb-type][data-lmb-tabid]")
                .each(function () { addFavIconTo($(this)); });
        } else if (!evt.shiftKey && added) {
            $(this).attr('data-lmb-added', 'false');
            $("[data-lmb-type][data-lmb-tabid]")
                .each(function () { removeFavIconFrom($(this)); });
        }
    });
});

// show shadow on scroll
$(function() {
    $(document).scroll(function() {
        const searchTable = $("table.lmbfringeMenuSearch");
        if ($("body").scrollTop() > 0) {
            searchTable.css("box-shadow", "0 4px 2px -2px gray");
        } else {
            searchTable.css("box-shadow", "");
        }
    });
});


<?php
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
<INPUT TYPE="hidden" NAME="ID" VALUE="<?= $session["user_id"] ?>">
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
<INPUT TYPE="hidden" NAME="aktivid">
<INPUT TYPE="hidden" NAME="action">
<INPUT TYPE="hidden" NAME="frame1para">
<INPUT TYPE="hidden" NAME="frame2para">
<INPUT TYPE="hidden" NAME="error_msg" VALUE="<?=$lang[25]?>">
</FORM>


<?php
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

function recRender($entryKey, $entry, $depth = 0, $data = null) {
    global $menu_setting;
    global $defaultExpandMenu;
    global $tabGroupIndex;
    global $lang;
    global $dwme;
    global $farbschema;
    global $activeMenu;

    # check param
    if(!$entry) { return; }
    
    # depth 0 -> User-menu / Admin-menu / Tables ...
    if($depth == 0) {
        # session refresh: restore last active menu
        $style = '';
        if (isset($activeMenu)) {
            $style = 'style="display: none;"';
            if ($activeMenu == $entryKey) {
                $style = '';
            }
        }
        echo "<div id='{$entryKey}' {$style}>";

        # hide quick search bar in favorites
        if ($entryKey !== 301) {
            # quick search
            echo '
                <table border="0" cellpadding="0" cellspacing="0" class="lmbfringeMenuNav lmbfringeMenuSearch" onmousedown="event.stopPropagation();">
                    <tr>
                        <td class="lmbMenuHeaderNav">
                            <i class="lmbMenuHeaderImage lmb-icon-32 lmb-search"></i>
                            <div class="lmbMenuItemHeaderNav">
                                <input type="text" class="lmbTableSearch" onkeyup="lmbFilterTablesTimer(event, this, ' . $entryKey . ');" placeholder="' . $lang[2507] . '">
                            </div>
                        </td>
                    </tr>
                </table>
            ';
        }
        $data = array('depth0Key' => $entryKey);
        foreach ($entry as $childKey => $child) {
            recRender($childKey, $child, 1, $data);
        }
        echo '</div>';
    }

    # depth 1 -> e.g. "Beispiel-CRM"-Header
    else if($depth == 1) {
        $combinedId = $data['depth0Key'] . '_' . $entryKey;

        # get onclick for header and angle up/down icon
        if ($entry['link']) {                
            $onHeaderSymbolClick = "onclick=\"{$entry['onclick']}; {$entry['link']}\"";
            $onToggleAngleSymbolClick = "onclick=\"hideShow('{$combinedId}'); {$entry['onclick']}; var event = arguments[0] || window.event; event.stopPropagation();\"";
        } else {
            $onHeaderSymbolClick = "onclick=\"hideShow('{$combinedId}'); {$entry['onclick']}\"";
            $onToggleAngleSymbolClick = "";
        }

        # start table
        echo '<table border="0" cellpadding="0" cellspacing="0" class="lmbfringeMenuNav">';

        # start header tr
        echo '<tr>';
        echo "<td class=\"lmbMenuHeaderNav\" {$onHeaderSymbolClick}>";

        # add big symbol
        if ($entry['gicon']) {
            echo "<i class=\"lmbMenuHeaderImage lmb-icon-32 {$entry['gicon']}\"></i>";
        } else if ($entry['icon']) {
            echo "<i class=\"lmbMenuHeaderImage lmb-icon-32 {$entry['icon']}\"></i>";
        }

        # get correct angle icon (up/down)
        if ($menu_setting['menu']["{$combinedId}"] && !$entry['extension']) {
            $eldispl = '';
            $iconclass = 'lmb-angle-up';
        } else {
            $eldispl = $defaultExpandMenu;
            $iconclass = 'lmb-angle-down';
        }

        # popupIcon
        echo "<div style=\"float:right;margin-right:0.5em;\" {$onToggleAngleSymbolClick}>";
        if (($entry['child'] AND count($entry['child']) > 0) || $entry['extension']) {
            echo "<i id=\"HS{$combinedId}\" class=\"lmb-icon {$iconclass}\" valign=\"top\" border=\"0\"></i>";
        }
        echo '</div>';

        # title
        $title = (is_numeric($entry['name']) ? $lang[$entry['name']] : $entry['name']);
        echo "<div class=\"lmbMenuItemHeaderNav\">{$title}</div>";

        # end header tr
        echo '</td></tr>';

        # start data tr
        echo '<tr>';
        echo "<td colspan=2><div class=\"lmbMenuHeaderNavContent\" id=\"CONTENT_{$combinedId}\" style=\"display:{$eldispl}\">";
        if($entry['extension']){
            echo "<div id=\"PH_{$combinedId}\" style=\"width:100%;\"></div>";
        }

        # use eval extension
        if ($entry['eval']) {
            // TODO still works?
            eval($entry['eval'] . ';');
        } else {
            # start data table
            echo '<table border="0" cellspacing="0" cellpadding="0" class="lmbMenuBodyNav">';

            # render next level                
            if ($entry['child']) {
                $data = array_merge($data, array(
                    'depth1Id' => $entry['id']
                ));
                foreach ($entry['child'] as $childKey => $child) {
                    recRender($childKey, $child, 2, $data);
                }
            }

            # end data table
            echo '</table>';
        }

        # end data tr, end table
        echo '</td></tr></table>';
    }        

    # depth 2 -> e.g. "Aufträge" / "Kunden" / all sub-tabgroups
    else if($depth == 2) {
        # get icon
        if ($entry['icon']) {
            $colorStyle = $entry['header'] ? "style=\"color: {$farbschema['WEB12']};\"" : '';
            $icon = "<i class=\"lmbMenuItemImage lmb-icon {$entry['icon']}\" {$colorStyle}></i>";
        } else {
            $icon = '';
        }

        # get cursor and onclick
        $cursor = '';
        if (lmb_substr($entry['link'], 0, 4) == 'main') {
            $onclick = "onclick=\"{$entry['onclick']}; parent.main.location.href='{$entry['link']}'\"";
        } elseif ($entry['link']) {
            $onclick = "onclick=\"{$entry['onclick']}; {$entry['link']}\"";
        } else {
            $onclick = "onclick=\"hideShowSub('{$data['depth0Key']}', '{$tabGroupIndex}')\"";
            $cursor = 'cursor:default;';
        }

        # bold text for subgroups
        $menuValue = (is_numeric($entry['name']) ? $lang[$entry['name']] : $entry['name']);
        $aClass = '';
        if($entry['header']) {
            $menuValue = "<b>{$menuValue}</b>";
            $aClass = 'lmbMenuItemHeader';
        }
        
        # start tr and add image
        echo "<tr><td>{$icon}</td>";
        
        # add title
        echo "<td nowrap style=\"overflow:hidden;background-color:{$entry['bg']}\" title=\"{$entry['desc']}\">";
        $dwmeSub = $dwme - 115 - (25 * $entry['depth']); // -100 for first depth, -100 -25(img-width) for second depth ...
        echo "<a class=\"$aClass lmbMenuItemBodyNav\" {$onclick}>";
        echo "<div id=\"mel_{$data['depth0Key']}_{$entry['id']}\" {$entry['attr']} style=\"{$entry['style']}\">{$menuValue}</div>";
        echo '</a>';
        echo '</td>';

        # check if display none
        if ($menu_setting['submenu'][$data['depth0Key'] . "_" . $tabGroupIndex]) {
            $eldispl = "";
        } else {
            $eldispl = $defaultExpandMenu;
        }

        # get correct caret right/down
        if ($eldispl) {
            $iconClass = " lmb-caret-right ";
        } else {
            $iconClass = " lmb-caret-down ";
        }

        # add angle right/down icon
        if($entry['child']) {
            echo "<td class=\"lmbMenuItemBodyNav\" onclick=\"hideShowSub('{$data['depth0Key']}','{$tabGroupIndex}')\">";
            echo "<i id=\"arrowSub{$tabGroupIndex}\" class=\"lmb-icon {$iconClass}\"></i>";
            echo "</td>";
        } else {
            echo '<td>&nbsp;</td>';
        }

        # finish line
        echo '</tr>';

        # if header: start new line with table for children
        if($entry['header']) {
            # add separator
            echo "<tr id=\"subElt_{$tabGroupIndex}\" style=\"display:{$eldispl};\">";
            echo "<td style=\"height:1px;overflow:hidden;\" colspan=\"3\">";
            echo "<div style=\"height:1px;background-color:{$farbschema['WEB4']};width:100%\"></div>";
            echo "</td></tr>";
        }
        echo "<tr id=\"subElt_{$tabGroupIndex}\" style=\"display:{$eldispl};overflow:hidden;\"><td></td><td colspan=\"2\">";
        echo "<table class=\"lmbMenuSubBodyNav\">";
        
        # render next level
        $data = array_merge($data, array(
            'depth2Id' => $entry['id'],
            'eldispl' => $eldispl
        ));

        if($entry['child']) {
            foreach ($entry['child'] as $childKey => $child) {
                # has children -> render as depth 2 | or in menu 'table' -> render as depth 2 to ensure correct table layout
                if($child['child'] != null || $data['depth0Key'] == 20) {
                    $tabGroupIndex++;
                    recRender($childKey, $child, 2, $data);
                }
                # no children -> render as depth 3
                else {
                    recRender($childKey, $child, 3, $data);
                }
            }
        }

        echo '</table></td></tr>';

        $tabGroupIndex++;
    }
    else if($depth == 3) {
        # get correct onclick
        if (lmb_substr($entry['link'], 0, 4) == 'main') {
            $onclick = "onclick=\"{$entry['onclick']}; parent.main.location.href='{$entry['link']}'\"";
        } elseif ($entry['link']) {
            $onclick = "onclick=\"{$entry['onclick']}; {$entry['link']}\"";
        } else {
            $onclick = '';
        }

        # get icon
        if ($entry['icon']) {
            $icon = "<i class=\"lmb-icon {$entry['icon']}\" style=\"vertical-align:baseline\"></i>";
        } else {
            $icon = '';
        }

        # output entry
        echo "<tr id=\"subElt_{$tabGroupIndex}\" style=\"display:{$data['eldispl']};overflow:hidden;background-color:{$entry['bg']}\">";
        $dwmeSub = $dwme - 105 - (25 * $entry['depth']);
        echo "<td class=\"contentSub\" title=\"{$entry['desc']}\">";
        echo "<div id=\"mel_{$data['depth0Key']}_{$data['depth2Id']}_{$entry['id']}\" {$entry['attr']} style=\"{$entry['style']}\">";

        $textToDisplay = (is_numeric($entry['name']) ? $lang[$entry['name']] : $entry['name']);
        echo "<a class=\"lmbMenuItemBodyNav\" {$onclick}>{$icon}&nbsp;{$textToDisplay}</a>";

        echo '</div></td></tr>';
    }
}    

$defaultExpandMenu = "none";

# index for #arrowSub{$tabGroupIndex} and #subElt_{$tabGroupIndex}
$tabGroupIndex = 0;

# width
if(!$dwme = $menu_setting["frame"]["nav"]){
	$dwme = 180;
}

# render all menus
foreach($menu as $key => $val) {
    recRender($key, $val);
}

echo '<div onclick="return hide_frame();"><div class="lmbMenuHeaderNav lmbMenuHide"><i class="lmb-icon lmb-icon-8 lmb-caret-left"></i></div></div>';

echo "</td></tr></table>\n";

$displayMainMenu = '';
if (!isset($activeMenu)) {
    # show first menu in nav frame
    foreach ($LINK["name"] as $key => $value) {
        if ($LINK["subgroup"][$key] == 2 AND $LINK["typ"][$key] == 1) {
            $displayMainMenu = "mainMenu(" . $key . ")";
            break;
        }
    }
}

echo "<script language='javascript'>" . $displayMainMenu . "</script>";

?>
</div>