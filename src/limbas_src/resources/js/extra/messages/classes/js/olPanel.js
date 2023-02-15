/*
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */



if (typeof(_basePATH_) == "undefined")
	_basePATH_='';

function olPanel(id, heading) {
	var _self = this;
	
	this.version = '$Id: olPanel.js 299 2009-09-18 12:41:03Z daniel $';
	this.el = document.getElementById(id);
	this.heading = null;

	if (!id)
		throw new Error('Must define an id!');
		
	if (!heading)
		throw new Error('Must define a heading!');
	
	this.baseurl = arguments[2] ? arguments[2] : null;
	
	this.onWait = function(el, msg){
		el.innerHTML = '<table style="margin:auto;" border="0"><tr><td><img src="'+_basePATH_+'images/wait.gif" '+
			'width="20" height="20" border="0"></td><td>' + msg + ' wird geladen...</td></tr></table>';
	};
	
	this.setCaption = function(caption){};
	this.setStatus = function(status){
		_self.el.innerHTML = status;
	};

	this.show = function (url, _use_ajax, _ajax_execJS){
		var ajax = null;
		
		var active = (_use_ajax) ? _use_ajax : false;
		var active_js = (_ajax_execJS) ? _ajax_execJS : false;
		
		var xPos = (arguments[3]) ? arguments[3] : -1;
		var yPos = (arguments[4]) ? arguments[4] : -1;
		var baseurl = (arguments[5]) ? arguments[5] : _self.baseurl;
		
		if (!active) {
			_self.el.innerHTML = url;
		} else {
			ajax = new olAjax(baseurl, active_js);
			ajax.onComplete = function (){
				this.el.innerHTML = arguments[0];
			};
			ajax.onError = function (){
				_self.onError(this.el, (arguments[1]) ? arguments[1] : arguments[0]);
			};
			_self.onWait(_self.el, heading);
			ajax.send(url, _self.el);
		}
	};
	
	this.hide = function(me){ };
	
	this.onError = function(el, msg){
		el.innerHTML = '<table style="margin:auto;" border="0"><tr><td><img src="' +
			_basePATH_ + 'images/error.gif" '+
			'width="35" height="35" border="0"></td><td>' + msg + '</td></tr></table>';
	};

	return this;
}
