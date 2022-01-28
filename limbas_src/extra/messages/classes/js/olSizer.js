/*
 * Copyright notice
 * (c) 1998-2021 Limbas GmbH(support@limbas.org)
 * All rights reserved
 * This script is part of the LIMBAS project. The LIMBAS project is free software; you can redistribute it and/or modify it on 2 Ways:
 * Under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * Or
 * In a Propritary Software Licence http://limbas.org
 * The GNU General Public License can be found at http://www.gnu.org/copyleft/gpl.html.
 * A copy is found in the textfile GPL.txt and important notices to the license from the author is found in LICENSE.txt distributed with these scripts.
 * This script is distributed WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * This copyright notice MUST APPEAR in all copies of the script!
 * Version 4.3.36.1319
 */

/*
 * ID:
 */

function olSizer(id, resize_id, is_horiz, f, min, max) {
	var _self = this;
	this.version = '$Id: olSizer.js 329 2009-09-29 13:41:33Z daniel $';
	
	this.doc = document.getElementById('mail_main');
	this.offsetX = this.doc.tBodies[0].offsetLeft;
	this.offsetY = this.doc.tBodies[0].offsetTop;

	this.config_o = new olCookie();
	
	this.id = id;
	this.resize_id = resize_id;
	
	this.is_horiz = is_horiz;
	
	this.min = min;
	this.max = max;
	this.default_position = arguments[6] ? arguments[6] : (50.0);
	
	this._limit = function(v){
		v = Math.round(v);
		if (_self.max && (v > _self.max)) v = _self.max;
		if (_self.min && (v < _self.min)) v = _self.min;
		return v;
	};
	
	this._load = function(){
		var ret = _self.config_o.get(_self.id);
		ret = (isNaN(ret) ? _self.default_position : ret) / 100;
		ret = (_self.is_horiz)
			? (ret * document.body.clientWidth)
			: (ret * document.body.clientHeight);
		return _self._limit(ret);
	};
	
	this._save = function(e){
		var ret = 0;
		
		if (_self.is_horiz)
			ret = _self._limit(document.getElementById(_self.resize_id).offsetWidth) * 100 / document.body.clientWidth;
		else
			ret = _self._limit(document.getElementById(_self.resize_id).offsetHeight) * 100 / document.body.clientHeight;

		_self.config_o.set(_self.id, Math.round(ret));
	};

	this._resize = (f) ? f : function(v){
		if (_self.is_horiz)
			document.getElementById(_self.resize_id).style.width = v + 'px';
		else
			document.getElementById(_self.resize_id).style.height = v + 'px';
	};

	E.observe(document.getElementById(this.id), "mousedown", function(e0){
		var ss = function(e){ E.stop(e) };
		var mm = function(e){
			var x = e.clientX - _self.offsetX;
			var y = e.clientY - _self.offsetY;
			_self._resize((_self.is_horiz) 
				? _self._limit(x - 1)
				: _self._limit(y - 1)
			);
		};
		var mu = function(e){
			E.remove(_self.doc, "mousemove", mm);
			E.remove(_self.doc, "mouseup", mu);
			E.remove(_self.doc, 'selectstart', ss);
			_self._save(e);
		};
		E.observe(_self.doc, 'selectstart', ss);
		E.observe(_self.doc, "mouseup", mu);
		E.observe(_self.doc, "mousemove", mm);
		E.stop(e0);
		return false;
	});

	if (_self.is_horiz)
		document.getElementById(_self.resize_id).style.width = _self._load() + 'px';
	else
		document.getElementById(_self.resize_id).style.height = _self._load() + 'px';
	
	return this;
}
