/*
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */



function olEvent() {
	this.version = '$Id: olEvent.js 1174 2015-12-04 17:04:22Z axel $';

	this.observe = function (elem, name, func) {
		var capture = (arguments[3]) ? arguments[3] : false;
		if (elem.addEventListener)
			elem.addEventListener(name, func, capture);
		else if (elem.attachEvent)
			elem.attachEvent('on' + name, func);
	};

	this.stop = function (e) {
		if (e.preventDefault) { 
		  e.preventDefault(); 
		  e.stopPropagation(); 
		} else
		  e.returnValue = false;
	};	

	this.remove = function (elem, name, func) {
		var capture = (arguments[3]) ? arguments[3] : false;
		if (elem.removeEventListener)
			elem.removeEventListener(name, func, capture);
		else if (elem.detachEvent)
			elem.detachEvent('on' + name, func);		
	};

	this.getX = function (e) {
		return (e.pageX) ? e.pageX : e.clientX + document.body.scrollLeft;
	};
	
	this.getY = function (e) {
		return (e.pageY) ? e.pageY : e.clientY + document.body.scrollTop;
	};

	this.setPosition = function(el, x, y){
		if(x<0 || y<0)
			this.center(el);
		else {
			el.style.position = 'absolute';
			el.style.left = x +'px';
			el.style.top = y +'px';
			el.style.display = 'block';
		}
	};
	
	this.center = function(el){
		el.style.display = 'block';
		el.style.position = 'absolute';
		var w = document.body.scrollLeft+document.body.clientWidth;
		var x = (w-el.offsetWidth) >> 1;

		var y = document.body.scrollTop + ((document.body.clientHeight-el.offsetHeight)>>1);

		if (x < 0) x = 0;
		if (y < 0) y = 0;
		el.style.left = x +'px';
		el.style.top = y +'px';
	};
	
	this._centerSomehowBroken = function(el){
		el.style.display = 'block';
		el.style.position = 'absolute';
		var w = (window.innerWidth) ? window.innerWidth : document.body.offsetWidth;
		var h = (window.innerHeight) ? window.innerHeight : document.body.offsetHeight;
		var x = (w-el.offsetWidth) >> 1;
		var y = (h-el.offsetHeight) >> 1;
		if (x < 0) x = 0;
		if (y < 0) y = 0;
		el.style.left = x +'px';
		el.style.top = y +'px';
	};
	
	return this;
}
