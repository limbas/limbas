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
