/*
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */



function olCookie() {
	var _self = this;
	
	this.version = '$Id: olCookie.js 299 2009-09-18 12:41:03Z daniel $';
	
	this.set = function (name, value) {
		var expire = "";
		
		if (arguments[2]) {
			var date = new Date();
			date.setTime(date.getTime() + (arguments[2]*24*60*60*1000));
			expire = "; expires="  + date.toGMTString();
		}
		
		document.cookie = name + "=" + value + expire + "; path=/";
	};
	
	this.get = function (name) {
		var nameEQ = name + "=";
		var ca = document.cookie.split(';');
		
		for(var i=0; i < ca.length; i++){
			var c = ca[i];
			while (c.charAt(0)==' ')
				c = c.substring(1, c.length);
			if (c.indexOf(nameEQ) == 0)
				return c.substring(nameEQ.length, c.length);
		}
		
		return null;
	};
	
	this.remove = function (name) {
		_self.set(name, "", null);
	};

	return this;
}
