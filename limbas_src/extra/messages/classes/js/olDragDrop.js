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

function olDragDrop(el){
	var _self = this;
	
	this.version = '$Id: olDragDrop.js 342 2009-09-30 00:52:24Z daniel $';
	this.eventHandler = new olEvent();
	
	this.el = el;
	this.temp = '';
	
	this.validTarget = ((typeof arguments[1]) == 'function') ? arguments[1] :
		function(obj, el){
			return (el.className == 'dropTarget');
		};

	this.onDrop = ((typeof arguments[2]) == 'function')	? arguments[2] :
		function(obj, evt, el){
			throw new Error('onDrop() - Handler not implemented!');
		};

	this.onDragStart = ((typeof arguments[3]) == 'function') ? arguments[3] :
		function(obj, evt, el){
			obj.temp = el.style.backgroundColor;
			el.style.backgroundColor = '#ffff99';
		};
	
	this.onDragStop = ((typeof arguments[4]) == 'function')	? arguments[4] :
		function(obj, evt, el){
			el.style.backgroundColor = obj.temp;
		};

	this._stop = function(e){ _self.eventHandler.stop(e) };
	
	this._mouseover = function(e){
		var el = (e.srcElement) ? e.srcElement : e.target;
		
		if (!_self.validTarget(_self, el)){
			document.body.style.cursor = 'no-drop';
			return;
		}
		
		document.body.style.cursor = 'move';
		_self.onDragStart(_self, e, el);
	};

	this._mouseout = function(e){
		var el = (e.srcElement) ? e.srcElement : e.target;
		
		document.body.style.cursor = 'auto';
	
		if (_self.validTarget(_self, el))
			_self.onDragStop(_self, e, el);
	};
	
	this._mousedown = function(e){
		_self._stop(e);
		_self.eventHandler.observe(_self.el, "drag", _self._stop);
		_self.eventHandler.observe(document, "selectstart", _self._stop);
		_self.eventHandler.observe(document, "mouseover",   _self._mouseover);
		_self.eventHandler.observe(document, "mouseout",    _self._mouseout);
		_self.eventHandler.observe(document, "mouseup",     _self._mouseup);
	};
	
	this._mouseup = function(e){
		var el = (e.srcElement) ? e.srcElement : e.target;

		document.body.style.cursor = 'auto';
		_self._mouseout(e);
		
		_self.eventHandler.remove(document, "mouseup",     _self._mouseup);
		_self.eventHandler.remove(document, "mouseout",    _self._mouseout);
		_self.eventHandler.remove(document, "mouseover",   _self._mouseover);
		_self.eventHandler.remove(document, "selectstart", _self._stop);
	
		if (_self.validTarget(_self, el))
			_self.onDrop(_self, e, el);
	};

	this.eventHandler.observe(this.el, "mousedown", this._mousedown);

	return this;
}
