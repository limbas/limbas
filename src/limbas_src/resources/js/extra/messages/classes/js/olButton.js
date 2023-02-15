/*
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */



function olButton(container, id, text, image) {
	var _self = this;

	this.version = '$Id: olButton.js 1174 2015-12-04 17:04:22Z axel $';

	this.id = null;
	this._is_image = true;
	this.container = null;
	this.text = null;
	this.img = null;
	this.action = null;
	this._baseURL = (arguments[7]) ? arguments[7] : location.href.replace(/(.*)\/.*/g, "$1/");

	this.width =  arguments[5] ? arguments[5] : 24; /* default button width */
	this.height = arguments[6] ? arguments[6] : 24; /* default button height */

	if (!id)
		throw new Error('Must define a button id!');

	if (!text)
		throw new Error('Must define a button text!');

	this._createElement = function(container, id, text, image, action){
		_self.text = text;
		_self.id = id;

		if (image){
			_self.img = document.createElement("i");
			//_self.img.src = _self._baseURL + image;
			_self.img.id = id + "_img";
			_self.img.className = "mail_button_img lmb-icon " + image;
			//_self.img.setAttribute("border", "0");
			//_self.img.setAttribute("alt", _self.text);
			_self.img.setAttribute("title", _self.text);
			/*_self.img.setAttribute("width", _self.width);
			_self.img.setAttribute("height", _self.height);*/
			_self.container = document.createElement("td");
                        //style="cursor:pointer;width:20px;height:20px"
			_self.container.style.width = _self.width + 'px';
			_self.container.style.height = _self.height + 'px';
                        _self.container.style.cursor = 'pointer';
                        _self.container.style.textAlign = 'center';
			_self.container.appendChild(_self.img);
		} else {
			_self._is_image = false;
			_self.img = document.createElement("a");
			_self.img.href = '#';
			_self.img.innerHTML = _self.text;
			_self.img.style.cssFloat = 'left';
			_self.img.style.styleFloat = 'left';
			_self.container = document.createElement("td");
                        _self.container.style.width = _self.width + 'px';
			_self.container.style.height = _self.height + 'px';
                        _self.container.style.cursor = 'pointer';
			var _tmp = document.createElement("td");
			//_tmp.style.cssFloat = 'left';
			//_tmp.style.styleFloat = 'left';
			//_tmp.innerHTML = '&nbsp;|&nbsp;';
			_self.container.appendChild(_self.img);
			_self.container.appendChild(_tmp);
		}
		_self.container.id = id + "_container";
		_self.container.className = "mail_button_container";
		_self.container.style.cssFloat = 'left';
		_self.container.style.styleFloat = 'left';


		container.appendChild(_self.container);

		_self.show();

		if (action){
			_self.action = action;
			_self.activate();
		} else
			_self.deactivate();
	};

	this._action = function(e){ _self.action(e, _self) };

	this._mousedown = function(e){
		if (_self._is_image)
			_self.img.style.border = 'solid 1px black';
	};

	this._mouseup = function(e){
		if (_self._is_image)
			_self.img.style.border = '';
	};

	this.activate = function(){
		if ((typeof _self.img.src) != 'undefined')
			try {
				_self.img.style.opacity = '';
			} catch(e) {
				_self.img.style.display = 'block';
			}
		else
			_self.img.style.color = '';

		_self.container.style.cursor = 'pointer';
		E.observe(_self.container, 'click', this._action);
		E.observe(_self.img, 'mousedown', this._mousedown);
		E.observe(_self.img, 'mouseup', this._mouseup);
	};

	this.deactivate = function(){
		if ((typeof _self.img.src) != 'undefined')
			try {
				_self.img.style.opacity = '.25';
			} catch(e) {
				_self.img.style.display = 'none';
			}
		else
			_self.img.style.color = 'gray';

		_self.container.style.cursor = 'default';
		E.remove(_self.container, 'click', this._action);
		E.remove(_self.img, 'mousedown', this._mousedown);
		E.remove(_self.img, 'mouseup', this._mouseup);
	};

	this.show = function (url, _use_ajax, _ajax_execJS){
		_self.container.style.display = 'inline';
	};

	this.hide = function(){
		this.container.style.display = 'none';
		return false;
	};

	this._createElement(container, id, text, image, arguments[4]);
	return this;
}
