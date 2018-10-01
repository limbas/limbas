/*
 * Copyright notice
 * (c) 1998-2018 Limbas GmbH(support@limbas.org)
 * All rights reserved
 * This script is part of the LIMBAS project. The LIMBAS project is free software; you can redistribute it and/or modify it on 2 Ways:
 * Under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * Or
 * In a Propritary Software Licence http://limbas.org
 * The GNU General Public License can be found at http://www.gnu.org/copyleft/gpl.html.
 * A copy is found in the textfile GPL.txt and important notices to the license from the author is found in LICENSE.txt distributed with these scripts.
 * This script is distributed WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * This copyright notice MUST APPEAR in all copies of the script!
 * Version 3.5
 */

/*
 * ID:
 */

function olIMAPUserInterface(e){
	var _self = this;

	this.version = '$Id$';

	this.win_message = null;
	this.win_popup = null;
	this.win_ajax = null;

	this.button_get = null;
	this.button_new = null;
	this.button_reply = null;
	this.button_fwd = null;
	this.button_del = null;
	this.button_print = null;

	this.buttons_hide = function(){
		/* deactivate all buttons with message context */
		_self.button_fwd.deactivate();
		_self.button_reply.deactivate();
		_self.button_del.deactivate();
		_self.button_print.deactivate();
	};

	this.buttons_show = function(mbox, uid){
		/* reconfigure button actions to use current mbox and uid */
		_self.button_fwd.action   = function(e, self){ mail_fwd(mbox, uid) };
		_self.button_reply.action = function(e, self){ mail_reply(mbox, uid) };
		_self.button_del.action   = function(e, self){ mail_delete(mbox, uid) };
		_self.button_print.action = function(e, self){ mail_print(mbox, uid) };

		/* activate all buttons with message context */
		_self.button_fwd.activate();
		_self.button_reply.activate();
		_self.button_del.activate();
		_self.button_print.activate();
	};

	this._onWindowResize = function(e){
		if (_guiSTYLE_ == 1)
			document.getElementById('mail_panel').style.display = 'none';

		document.getElementById('mail_folders').style.display = 'none';
		//document.getElementById('mail_folders').style.height = (document.getElementById('mail_hsizer').offsetHeight)+ 'px';
		document.getElementById('mail_folders').style.display = 'block';
                
		if (_guiSTYLE_ != 1){
			document.getElementById('mail_right_column').style.height =
				(document.getElementById('mail_hsizer').offsetHeight - document.getElementById('mail_list').offsetHeight) + 'px';
			return;
		}

		document.getElementById('mail_panel').style.height = (
				document.getElementById('mail_hsizer').offsetHeight -
				document.getElementById('mail_vsizer').offsetHeight -
				document.getElementById('mail_list').offsetHeight
			) + 'px';

		document.getElementById('mail_panel').style.display = 'block';
	};

	this._gui_init_win_message = function(){
		var msg_initial_width  = Math.round(window.innerWidth  * .85); // percentage of body width
		var msg_initial_height = Math.round(window.innerHeight * .85); // percentage of body height

		switch(_guiSTYLE_){
			case 1:	/* create a panel */
				_self.win_message = new olPanel('mail_panel',  'Nachricht', _baseURL_);
				break;
			case 2: /* create a window */
				_self.win_message = new olWindow('mail_panel',  'Nachricht',
					msg_initial_width, msg_initial_height, _baseURL_);
				break;
			default: /* create a popup with default dimensions */
				_self.win_message = new olPopup('olWin',  'Nachricht',
					msg_initial_width, msg_initial_height, _baseURL_);
		}

		_self.win_message.hide = function(win){
			/* hide the message itself */
			switch(_guiSTYLE_){
				case 1:
					/* wipeout content of message panel */
					win.el.innerHTML = '';
					break;
				case 2:
					/* TODO: close browser window */
					break;
				default:
					/* hide the popup and remove its eventhandlers */
					win.container.style.display = 'none';
					E.remove(document, 'mouseup', win._mouseup);
					E.remove(document, 'selectstart', win._stop);
					E.remove(win.picker, 'mousedown', win._mousedown);
			}
			/* deactivate all buttons with message context */
			_self.buttons_hide();
			return false;
		};
	};

	this._gui_init_sizers = function(){
		/* initialize horizontal sizer */
		new olSizer('mail_hsizer', 'mail_left_column', true, function (v){
			var el = document.getElementById('mail_left_column');
			el.style.width = (v-el.offsetLeft-3) + 'px';
		}, 170);

		if (_guiSTYLE_==1) 	/* initialize vertical sizer */
			new olSizer('mail_vsizer', 'mail_right_column', false,
				function (v){
					document.getElementById('mail_right_column').style.height = v - document.getElementById('mail_menu').offsetHeight + 'px';
					document.getElementById('mail_panel').style.display = 'none';
					document.getElementById('mail_panel').style.height = ( document.getElementById('mail_hsizer').offsetHeight -
							document.getElementById('mail_vsizer').offsetHeight -	v +
							document.getElementById('mail_menu').offsetHeight ) + 'px';
					document.getElementById('mail_panel').style.display = 'block';

				}, 120, document.getElementById('mail_hsizer').offsetHeight - document.getElementById('mail_vsizer').offsetHeight);
	};

	this._gui_init_textmenu = function(el){
		/*var xxx	= new olButton(el, "xxx",	"Posteingang",
			null,
			function(e, o){
				olEvent().stop(e);
				_self.ctxTextmenu.show(e);
			}, 16, 16, _basePATH_);
		var xxx1	= new olButton(el, "xxx",	"Blabla",
			null,
			function(e, o){
				olEvent().stop(e);
				_self.ctxTextmenu.show(e);
			}, 16, 16, _basePATH_);*/
	};

	this._gui_init_buttons = function(el){
		/* olButton can also be 'misused' as a Menu, if no image is given: */
		_self.button_get	= new olButton(el, "_self.button_get",	"Posteingang",
			"lmb-mail",
			function(e, o){
				location.reload();
			}, 20, 20, _basePATH_);

		_self.button_new	= new olButton(el, "_self.button_new",	"Neue Nachricht",
			"lmb-mail-new",
			function(e, o){
				_self.win_message.show('?compose=1', true, false);
			}, 20, 20, _basePATH_);

		_self.button_reply	= new olButton(el, "_self.button_reply", "beantworten",
			"lmb-mail-reply",
			null, 20, 20, _basePATH_);

		_self.button_fwd	= new olButton(el, "_self.button_fwd",   "weiterleiten",
			"lmb-mail-forward",
			null, 20, 20, _basePATH_);

		_self.button_del	= new olButton(el, "_self.button_del",   "löschen",
			"lmb-icon-cus lmb-email-del",
			null, 20, 20, _basePATH_);

		_self.button_print	= new olButton(el, "_self.button_print", "drucken",
			"lmb-print",
			null, 20, 20, _basePATH_);

		_self.button_find	= new olButton(el, "_self.button_find",  "suchen",
			"lmb-mail-search",
			function(e, o){
				var x = E.getX(e), y = E.getY(e);
				var offsetX = document.getElementById('mail_main').tBodies[0].offsetLeft;
				if ((x + _self.win_ajax.width) > (document.body.clientWidth - offsetX))
					x = document.body.clientWidth - _self.win_ajax.width - offsetX;
				_self.win_ajax.show("?popup=1&find=1", true, true, x, y);
				_self.win_ajax.setStatus('');
			}, 20, 20, _basePATH_);
	};

	this._gui_init_contextmenus = function(){
		_self.ctxMessage = new olContextMenu();

		_self.ctxMessage.add('öffnen',		"lmb-icon-cus lmb-email-open",
			function(e, mbox, uid){ mail_get(mbox, uid) });

		_self.ctxMessage.add('weiterleiten',	"lmb-mail-forward",
			function(e, mbox, uid){ mail_fwd(mbox, uid) });

		_self.ctxMessage.add('beantworten',	"lmb-mail-reply",
			function(e, mbox, uid){ mail_reply(mbox, uid) });

		_self.ctxMessage.add('drucken',		"lmb-print",
			function(e, mbox, uid){ mail_print(mbox, uid) });

		_self.ctxMessage.addLine();

		_self.ctxMessage.add('merken',	"lmb-icon-cus lmb-email-link",
			function(e, mbox, uid){
				var el = document.getElementById(uid);
				//if (SEL.getCount()==0)
				if (el.getAttribute('name') != 'marked')
					SEL.select(el);
				C.set("mail_marked", SEL.getString());
			});

		_self.ctxMessage.addLine();

		_self.ctxMessage.add('löschen',	"lmb-icon-cus lmb-email-del",
			function(e, mbox, uid){
				var el = document.getElementById(uid);
				if (el.getAttribute('name') != 'marked')
					SEL.select(el);
				mail_delete_selection(mbox, SEL);
			});
			
		/* --- */
		_self.ctxFolder = new olContextMenu();
		_self.ctxFolder.add('öffnen',	"lmb-folder",
			function(e, mbox){
				mail_list(mbox);
			});
		_self.ctxFolder.add('umbenennen', "lmb-icon-cus lmb-folder-edit",
			function(e, mbox){
				_self.win_popup.show("?popup=1&rename=" + mbox, true, true, E.getX(e), E.getY(e));
			});
		_self.ctxFolder.addLine();
		_self.ctxFolder.add('Neuer Unterordner', "lmb-icon-cus lmb-folder-add",
			function(e, mbox){
				_self.win_popup.show("?popup=1&create=" + mbox, true, true, E.getX(e), E.getY(e));
			});
		_self.ctxFolder.addLine();
		_self.ctxFolder.add('löschen', "lmb-icon-cus lmb-folder-add",
			function(e, mbox){
				_self.win_popup.show("?popup=1&delete=" + mbox, true, false, E.getX(e), E.getY(e));
			});
		/* --- */
		_self.ctxFolderList = new olContextMenu();
		_self.ctxFolderList.add('Neuer Ordner', "lmb-icon-cus lmb-folder-add",
			function(e, mbox){
				_self.win_popup.show("?popup=1&create=INBOX", true, true, E.getX(e), E.getY(e));
			});
		/* --- */
		_self.ctxTextmenu = new olContextMenu();
		_self.ctxTextmenu.add('Neuer Ordner', "lmb-icon-cus lmb-folder-add",
			function(e, mbox){
				_self.win_popup.show("?popup=1&create=INBOX", true, true, E.getX(e), E.getY(e));
			});
		_self.ctxTextmenu.add('Test', "lmb-icon-cus lmb-folder-add",
			function(e, mbox){
				_self.win_popup.show("?popup=1&create=INBOX", true, true, E.getX(e), E.getY(e));
			});
	};

	this._init = function (e){
		_self.win_popup = new olPopup('olPop', 'Info', 240, 128, _baseURL_);
		_self.win_ajax = new olPopup('olPop', 'Nachricht suchen...', 320, 192, _baseURL_);

		_self._gui_init_win_message();
		_self._gui_init_textmenu(document.getElementById('mail_textmenu'));
		_self._gui_init_contextmenus();
		_self._gui_init_buttons(document.getElementById('mail_buttons'));
		_self._gui_init_sizers();

                //olEvent().observe(window, "click", _self.win_ajax.hide(_self.win_ajax));
		/* register custom onWindowResize handler */
		olEvent().observe(window, "resize", _self._onWindowResize);
		_self._onWindowResize(e);
	};

	this._init(e);
}
