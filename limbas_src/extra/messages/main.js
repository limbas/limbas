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
 * ID:
 */

/* ------------------------------------------------------------
 * global objects  */
/* global Scol */

var E = null;
var C = null;
var SEL = null;
var olGUI = null;
var search_active;
var active_folder;

/* entry point */
var onDocumentLoaded = function(e){
	var _use_browser_cache = false;

	olFramework().load(_basePATH_ + 'classes/js/', [
		'olButton',
		'olContextMenu',
		'olCookie',
		'olDragDrop',
		'olEvent',
		'olIMAPUserInterface',
		'olListSelection',
		'olPanel',
		'olPopup',
		'olSizer',
		'olWindow'
	], _use_browser_cache, function(loaded_modules){ // onFrameworkLoaded
		/* create some global object instances */
		E = new olEvent();
		C = new olCookie();

		/* initialize gui (buttons(main menu), contextmenus, maillist, resizehandler... */
		olGUI = new olIMAPUserInterface();
                search_active = new Array();

		/* and display folder list. */
		mail_folders();
	});
};

if (window.addEventListener)
	window.addEventListener('load', onDocumentLoaded, false);
else
	window.attachEvent('onload', onDocumentLoaded);

/* ------------------------------------------------------------
 * global functions
 */
function mail_folders(){
	var info = document.getElementById('mail_list');
	var folders = document.getElementById('mail_folders');
	var ajax = new olAjax(_baseURL_);
		
	ajax.onComplete = function(){
		/* clear info */
		info.innerHTML = '';

		/* show folder list */
		folders.innerHTML = arguments[0];

		E.observe(folders, 'contextmenu', function (e){
			var el = (e.target) ? e.target : e.srcElement;
			if (el.className == 'mail')
				olGUI.ctxFolder.show(e, el.id);
			else if (el.id == 'mail_folders')
				olGUI.ctxFolderList.show(e);
			E.stop(e);
		});
                
                /*expand explorer tree based on cookie information*/
                if (C.get('mail_toogledfolders') == null || C.get('mail_toogledfolders') == '' || C.get('mail_toogledfolders') == 'null')
                {
                    C.set('mail_toogledfolders',JSON.stringify(new Array()))
                }
                var toogledfolders = JSON.parse(C.get('mail_toogledfolders'));
                if (toogledfolders)
                {
                    toogledfolders.forEach(function (element, index) {
                        mail_foldertoggle(element)
                    });
                }
                
                
                
		var firstFolder = 'INBOX';
		//If no folder
                if (C.get('mail_activefolder'))
                {
                    firstFolder = C.get('mail_activefolder');
                }
                /* list mails of INBOX or prior selected folder, if it exists */
                var anchors = folders.getElementsByTagName('a');
                for(var anchor=0; anchor < anchors.length; anchor++)
                        if (anchors[anchor].id==firstFolder){
                                mail_list(firstFolder);
                                return;
                        }
                /* otherwise list mails of the first mailbox */
                if (anchors.length)
                        mail_list(anchors[0].id);
	};
	
	ajax.onError = function(){
		mail_info_error(info, arguments[1] ? arguments[1] : arguments[0]);
		folders.innerHTML = '';
	};
	
	folders.innerHTML = '';
	mail_info_wait(info, 'Ordnerliste wird geladen...');
	
	if (arguments[3])
		folders.innerHTML = '';
		
	ajax.send('?folders=1');
	return false;
}

function mail_list(mbox){
	var _sort = arguments[1];
	var el = document.getElementById('mail_list');
	var ajax = new olAjax(_baseURL_);
	
        if (active_folder)
        {
            document.getElementById(active_folder).style.fontWeight = 'normal';
        }
        active_folder = mbox;
        document.getElementById(active_folder).style.fontWeight = 'bold';
        C.set('mail_activefolder',active_folder);
        
        
	ajax.onComplete = function(){
		el.innerHTML = arguments[0];
		var mailbox = el.getElementsByTagName("table")[0];
		var messages = el.getElementsByTagName("tr");

		document.getElementById('mail_status').innerHTML = (mailbox && mailbox.id) ?
				'<b>' + (messages.length-2) + '</b> Nachrichten' : '&nbsp;';

		/* instantiate a new list selection object. */
		SEL = new olListSelection(mailbox, _guiBEHAVIOUR_);
		
		/* implement various handlers */
		SEL.onContext = function (obj, evt, row) {
			//document.getElementById('mail_status').innerHTML = obj.getCount() + " Nachrichten markiert";
			olGUI.ctxMessage.show(evt, obj.table.id, row.id);
		};
               
		SEL.onAction = function (evt, tbl, row) { mail_get(tbl.id, row.id); };
		SEL.onSelect = function(obj, row) { olGUI.buttons_show(obj.table.id, row.id); document.getElementById('mail_status').innerHTML = obj.getCount() + " Nachrichten markiert"; };
		SEL.onUnselect = function(obj, row) { olGUI.buttons_hide(); };
		
		/* implement a customized DragDrop-Handler for moving
		 * messages into another folder. */
		SEL.onDragDrop = function (obj, tbl, row) {
			new olDragDrop(row,
				function /* validateTarget */ (obj, el) {
					return (
						(el.tagName=='A') &&
						(el.className=='mail') && 
						(el.parentNode.tagName=='LI') && 
						(el.parentNode.className=='mail')
					);
				},
				function /* onDrop */ (obj, evt, el) {
					var mbox = obj.el.parentNode.parentNode.id;
					var uid = obj.el.id;
					var dst = el.id;
					mail_move(mbox, uid, dst);
				}
			);
		};
		/* initialize the selection handling for the mail_list table items. */
		SEL.initialize();
	};

	mail_info_wait(el, 'Nachrichtenliste wird geladen...');

	ajax.onError = function(){
		mail_info_error(el, arguments[1] ? arguments[1] : arguments[0]);
	};
	
	if (!_sort){ /* get default sort parameters from cookie */
		var sort_col = C.get('mail_sort_col');
		var sort_desc = C.get('mail_sort_desc');
		sort_desc = (sort_desc) ? parseInt(sort_desc) : 0;
		_sort = (sort_col) ? ('sort_col=' + sort_col +
			( sort_desc ? '&sort_desc=1' : '')) : '';
	}
	
	ajax.send('?mbox=' + mbox + (_sort ? '&' + _sort : ''));
	return false;
}

function mail_sortlist(mbox, column, ascending){
	/* save new sort parameters in cookie */
	C.set('mail_sort_col', column);
	C.set('mail_sort_desc', ascending ? '0' : '1');
	
	/* reload the list with new sort parameters*/
	return mail_list(mbox, (column) ? ('sort_col=' + column +
		( ascending ? '' : '&sort_desc=1')) : null);
}

function mail_get(mbox, uid){
	/* show message */
	olGUI.win_message.show('?mbox=' + mbox + '&uid=' + uid, true, true);
	olGUI.buttons_show(mbox, uid);
}

function mail_fwd(mbox, uid){
	olGUI.win_message.show('?mbox=' + mbox + '&fwd_uid=' + uid, true, true);
	olGUI.buttons_hide();
}

function mail_reply(mbox, uid){
	olGUI.win_message.show('?mbox=' + mbox + '&reply_uid=' + uid, true, false);
	olGUI.buttons_hide();
}

function mail_print(mbox, uid){
	window.open(_baseURL_ + '&mbox=' + mbox + '&uid=' + uid + '&print=1', "_blank");
}

function mail_delete_selection(mbox, sel){
	var _cnt = sel.getCount();
	switch (_cnt){
		case 1:
			_msg = "Nachricht wird";
			break;
		default:
			_msg = _cnt + " Nachrichten werden";
	}
	
	document.getElementById('mail_status').innerHTML = "Bitte warten, " + _msg + " gelöscht...";
	
	var ajax = new olAjax(_baseURL_, true);
	
	ajax.onComplete = function (){
		this.el.innerHTML = arguments[0];
		olGUI.win_message.hide(olGUI.win_message);
	};
	
	ajax.onError = function (){
		this.el.innerHTML = (arguments[1]) ? arguments[1] : arguments[0];
	};
	
	ajax.send('?mbox=' + mbox + '&del_range=' + sel.getUIDs(), document.getElementById('mail_status'));
	sel.clear();
	olGUI.buttons_hide();
}

function mail_delete(mbox, uid){
	document.getElementById('mail_status').innerHTML = "Bitte warten, Nachricht wird gelöscht...";
	
	var ajax = new olAjax(_baseURL_, true);
	
	ajax.onComplete = function (el){
		this.el.innerHTML = arguments[0];
		olGUI.win_message.hide(olGUI.win_message);
	};
	
	ajax.onError = function (){
		this.el.innerHTML = (arguments[1]) ? arguments[1] : arguments[0];
	};
	
	ajax.send('?mbox=' + mbox + '&del_uid=' + uid, document.getElementById('mail_status'));
	olGUI.buttons_hide();
}

function mail_arch(mbox, uid){
	var ajax = new olAjax(_baseURL_, true);
	
	ajax.onComplete = function (el){
		this.el.innerHTML = arguments[0];
		olGUI.win_popup.hide(olGUI.win_popup);
		olGUI.win_message.hide(olGUI.win_message);
	};
	
	ajax.onError = function (){
		this.el.innerHTML = (arguments[1]) ? arguments[1] : arguments[0];
	};
	
	mail_popup_status('Nachricht wird archiviert...', 'wait.gif');
	
	ajax.send('?mbox=' + mbox + '&uid=' + uid + '&arch=1', document.getElementById('mail_status'));
}

function mail_move(mbox, uid, dst){
	var ajax = new olAjax(_baseURL_, true);
	
	ajax.onComplete = function (el){
		this.el.innerHTML = arguments[0];
		olGUI.win_popup.hide(olGUI.win_popup);
	};
	
	ajax.onError = function (){
		this.el.innerHTML = (arguments[1]) ? arguments[1] : arguments[0];
	};
	
	olGUI.buttons_hide();
	
	mail_popup_status('Nachricht wird verschoben...', 'wait.gif');
	ajax.send('?mbox=' + mbox + '&uid=' + uid + '&move=' + dst, document.getElementById('mail_status'));
}

function mail_foldertoggle(id){
	var elem = document.getElementById('_' + id);
	var togbox = document.getElementById('toggle_' + id);
        var folderpic = document.getElementById('folderpic_' + id);
	var shown = (elem.style.display == 'table-row');
        
        //save toogled folders in cookie
        var toogledfolders = JSON.parse(C.get('mail_toogledfolders'));
        if (toogledfolders)
        {
            if (!shown)
            {
                var exists = toogledfolders.indexOf(id);
                if (exists <= -1)
                {
                    toogledfolders.push(id);
                }
            }
            else
            {
                var rem = toogledfolders.indexOf(id);
                if (rem > -1) {
                    toogledfolders.splice(rem, 1);
                }
            }
        }
        C.set('mail_toogledfolders',JSON.stringify(toogledfolders));
        
	
	elem.style.display = shown ? 'none' : 'table-row';
	togbox.src = shown ? 'pic/outliner/plusonly.gif' : 'pic/outliner/minusonly.gif';
        folderpic.src = shown ? 'pic/outliner/box_close.gif' : 'pic/outliner/box_open.gif';
	return false;
}

function mail_info_wait(el, msg){
	el.innerHTML = '<table class="mail_info" border="0"><tr><td><img src="'+_basePATH_+'images/wait.gif" '+
		'width="20" height="20" border="0"></td><td>' + msg + '</td></tr></table>';
}

function mail_info_error(el, msg){
	el.innerHTML = '<table class="mail_info" border="0"><tr><td><img src="'+_basePATH_+'images/error.gif" '+
		'width="35" height="35" border="0"></td><td>' + msg + '</td></tr></table>';
}

function mail_popup_status(msg, img){
	olGUI.win_popup.show('<table class="mail_desc" width="100%" height="100%" border="0" cellpadding="0" '+
		'cellspacing="0"><tr><td align="center"><table height="100%" border="0" cellpadding="4"  '+
		'cellspacing="0"><tr><td align="right"><img src="'+_basePATH_+'images/'+img+'" width="20" height="20"  '+
		'border="0"></td><td align="left">'+msg+'</td></tr></table></td>'+
		'</tr></table>', false, false);
}


function mail_search_column(e,el)
{
    
    if (e.keyCode != 13) 
    {
        return;
    }
    el.blur();
    var col = el.parentNode.cellIndex;
    var search = el.value.toLowerCase();
    
    if (search === '')
    {
        delete search_active[col];
    }
    else
    {
        search_active[col] = search;
    }
    
    var count = 0;
    for (var i = 2, row; row = SEL.rows[i]; i++) {
        //var ihtml = row.cells[col].getElementsByTagName('a')[0].innerHTML.toLowerCase();
        if (search_active.length > 0 && !mail_search_helper(row)) //ihtml.indexOf(search) <= -1 && search !== '')
        {
            row.style.display = 'none';
        }
        else
        {
            row.style.display = 'table-row';
            count++;
        }
    }
    
    
    document.getElementById('mail_status').innerHTML = count + " Nachrichten";
}
function mail_search_helper(row)
{
    var found = false;
    found = search_active.every(function (element, index) {
        var ihtml = row.cells[index].getElementsByTagName('a')[0].innerHTML.toLowerCase();
        if (ihtml.indexOf(element) > -1)
        {
            return true;
        }
    });
    return found;
}


function mail_search(){
	var _sort = arguments[1];
	var el = document.getElementById('mail_list');
	var ajax = new olAjax(_baseURL_);
        
	ajax.onComplete = function(){
		el.innerHTML = arguments[0];
		var mailbox = el.getElementsByTagName("table")[0];
		var messages = el.getElementsByTagName("tr");

		document.getElementById('mail_status').innerHTML = (mailbox && mailbox.id) ?
				'<b>' + (messages.length-2) + '</b> Nachrichten' : '&nbsp;';

		/* instantiate a new list selection object. */
		SEL = new olListSelection(mailbox, _guiBEHAVIOUR_);
		
		/* implement various handlers */
		SEL.onContext = function (obj, evt, row) {
			//document.getElementById('mail_status').innerHTML = obj.getCount() + " Nachrichten markiert";
			olGUI.ctxMessage.show(evt, obj.table.id, row.id);
		};
               
		SEL.onAction = function (evt, tbl, row) { mail_get(tbl.id, row.id); };
		SEL.onSelect = function(obj, row) { olGUI.buttons_show(obj.table.id, row.id); document.getElementById('mail_status').innerHTML = obj.getCount() + " Nachrichten markiert"; };
		SEL.onUnselect = function(obj, row) { olGUI.buttons_hide(); };
		
		/* implement a customized DragDrop-Handler for moving
		 * messages into another folder. */
		SEL.onDragDrop = function (obj, tbl, row) {
			new olDragDrop(row,
				function /* validateTarget */ (obj, el) {
					return (
						(el.tagName=='A') &&
						(el.className=='mail') && 
						(el.parentNode.tagName=='LI') && 
						(el.parentNode.className=='mail')
					);
				},
				function /* onDrop */ (obj, evt, el) {
					var mbox = obj.el.parentNode.parentNode.id;
					var uid = obj.el.id;
					var dst = el.id;
					mail_move(mbox, uid, dst);
				}
			);
		};
		/* initialize the selection handling for the mail_list table items. */
		SEL.initialize();
	};

	mail_info_wait(el, 'Nachrichten werden gesucht...');

	ajax.onError = function(){
		mail_info_error(el, arguments[1] ? arguments[1] : arguments[0]);
	};
	
	if (!_sort){ /* get default sort parameters from cookie */
		var sort_col = C.get('mail_sort_col');
		var sort_desc = C.get('mail_sort_desc');
		sort_desc = (sort_desc) ? parseInt(sort_desc) : 0;
		_sort = (sort_col) ? ('sort_col=' + sort_col +
			( sort_desc ? '&sort_desc=1' : '')) : '';
	}
	
        olGUI.win_ajax.hide(olGUI.win_ajax);
	ajax.send('?range='+document.getElementById('range').value+'&crit='+document.getElementById('criteria').value+'&search='+document.getElementById('find').value+'&find=1&mbox=' + C.get('mail_activefolder') + (_sort ? '&' + _sort : ''));
	return false;
}