/*
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
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
