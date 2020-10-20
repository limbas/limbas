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

if (typeof(_basePATH_) == "undefined")
	_basePATH_='';

function olWindow(id, heading) {
	var _self = this;
	
	this.version = '$Id: olWindow.js 329 2009-09-29 13:41:33Z daniel $';
	this.id = id;
	this.win = null;
	
	if (!id)
		throw new Error('Must define an id!');
		
	if (!heading)
		throw new Error('Must define a heading!');
	this.heading = heading;

	this.width = arguments[2] ? arguments[2] : 320;
	this.height = arguments[3] ? arguments[3] : 240;

	this.baseurl = arguments[4] ? arguments[4] : null;
	
	this.onWait2 = function(el, msg){};
	
	this.onWait = function(el, msg){
		var code = '<table style="margin:auto;" border="0"><tr><td><img src="'+_basePATH_+'images/wait.gif" '+
			'width="20" height="20" border="0"></td><td>' + msg + ' wird geladen...</td></tr></table>';
		el.document.open();
		el.document.write(code);
		el.document.close();
	};

	this.setCaption = function(caption){};
	this.setStatus = function(status){};

	this._open = function(){
		_self.win = window.open(
			(arguments[0]) ? arguments[0] : 'about:blank',
			'_blank' ,"location=no,scrollbars=yes,toolbar=no,resizable=yes" +
			",width=" + _self.width + ",height=" + _self.height
		);
		/*_self.win.onunload=function(e){alert(e);};
		_(_self.win);*/
	};

	this.show = function (url, _use_ajax, _ajax_execJS){
		var active = (_use_ajax) ? _use_ajax : false;
		var baseurl = (arguments[5]) ? arguments[5] : _self.baseurl;

		_self._open();
		
		if (!active){
			_self.win.document.innerHTML = url;
			return;
		}
		
		if (baseurl.match(/\?/))
			url = '&' + url.substr(1);
			
		_self.onWait(_self.win, _self.heading);
		_self.win.location.href = baseurl + url;
	};
	
	this.hide = function(me){
		if (_self.win){
			_self.win.close();
			_self.win = null;
		}
	};
	
	this.onError = function(el, msg){};
	
	return this;
}
