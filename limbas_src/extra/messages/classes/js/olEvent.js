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
