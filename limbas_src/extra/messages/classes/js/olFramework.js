/*
 * Copyright notice
 * (c) 1998-2016 Limbas GmbH - Axel westhagen (support@limbas.org)
 * All rights reserved
 * This script is part of the LIMBAS project. The LIMBAS project is free software; you can redistribute it and/or modify it on 2 Ways:
 * Under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * Or
 * In a Propritary Software Licence http://limbas.org
 * The GNU General Public License can be found at http://www.gnu.org/copyleft/gpl.html.
 * A copy is found in the textfile GPL.txt and important notices to the license from the author is found in LICENSE.txt distributed with these scripts.
 * This script is distributed WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * This copyright notice MUST APPEAR in all copies of the script!
 * Version 3.0
 */

/*
 * ID:
 */

function $(){return document.getElementById(arguments[0])};

function olFramework() {
	this.version = '$Id: olFramework.js 421 2009-10-30 15:57:01Z daniel $';
	
	this.load = function(classpath, modules){
		var _loadstack = new Object();
		
		var _use_cache = ((typeof arguments[2]) == 'undefined')
			? false : arguments[2];
			
		var _onFrameworkLoadFinished = ((typeof arguments[3]) != 'undefined')
			? arguments[3] : function(){}; 
			
		var _onFrameworkLoadError = ((typeof arguments[4]) != 'undefined')
			? arguments[4] : function(failed_module, error_message){
				throw new Error("Failed loading JavaScript Framework Module '" +
					failed_module + "' (" + error_message + ")");
			}; 

		var _load_callback = function(_ajaxreq){
			var _missing = 0;
			_loadstack[_ajaxreq._module_id] = true;
			for (var _one in _loadstack)
				if(!_loadstack[_one]) _missing++;
			if (!_missing) _onFrameworkLoadFinished(modules);
		};
			
		for (var _each in modules){
			var _ajaxreq = new olAjax(classpath);
			
			_ajaxreq.onError = function(error_message){
				_onFrameworkLoadError(modules[this._module_id], error_message);
			};
			
			_ajaxreq.onComplete = function(js_content){
				try {
					var _js = document.createElement('script');
					
					_js.type = 'text/javascript';
					
					if (navigator.userAgent.indexOf('Safari')!=-1) // HACK-Alert!
						_js.innerHTML = js_content;
					else if ((typeof _js.textContent)!='undefined')
						_js.textContent = js_content;
					else
						_js.text = js_content;
						
					document.getElementsByTagName('head')[0].appendChild(_js);
					_load_callback(this);
				} catch(e) {
					_onFrameworkLoadError(modules[this._module_id],
						((typeof e.fileName)=='undefined')
							? 'script element creation'
							: (e.fileName + ':' + e.lineNumber));
				}
			};
			
			_loadstack[_each] = false;
			_ajaxreq._module_id = _each;

			var url = modules[_each] + '.js';
			if (!_use_cache)
				url += '?_rnd=' + Math.floor(Math.random() * 10000);
			
			_ajaxreq.send(url);
		}
	};

	return this;
}
