/*
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */



function olContextMenu(){
	var _self = this;
        
        this.isShown = false;

	this.version = '$Id: olContextMenu.js 1174 2015-12-04 17:04:22Z axel $';

	this.eventHandler = new olEvent();
	this.items = new Array();
	/* TODO: fix the static parameter count of 2 */
	this.param1 = null;
	this.param2 = null;

	this.parent_el = (arguments[0]) ? arguments[0] : document.body;

	this.add = function(title, image, action){
		var item = new olContextMenuItem(title, _self, image, action);
		_self.items.push(item);
		return item;
	};

	this.addLine = function(){
		var a = document.createElement('div');
		var b = document.createElement('div');
		a.className = 'lmbContextRowSeparator';
		b.className = 'lmbContextRowSeparatorLine';
		a.appendChild(b);
		_self.container.appendChild(a);
	};

	this.show = function(showEvent, param1, param2){
		var x = _self.eventHandler.getX(showEvent);
		var y = _self.eventHandler.getY(showEvent);
		/* var el = (typeof showEvent.currentTarget == 'object')
			? showEvent.currentTarget
			: showEvent.srcElement; */

		_self.param1 = param1;
		_self.param2 = param2;

		_self.container.style.display = 'block';

		if ((document.body.clientWidth-_self.container.offsetWidth-x) < 0)
			x -= _self.container.offsetWidth;

		if ((document.body.clientHeight-_self.container.offsetHeight-y) < 0)
			y -= _self.container.offsetHeight;

		_self.container.style.left = x + 'px';
		_self.container.style.top = y + 'px';

		var hideme = function(hideEvent){
                    if (hideEvent.target === showEvent.target) return;
                    _self.eventHandler.stop(hideEvent);
                    _self.eventHandler.remove(document, "click", hideme);
                    _self.hide();
		};
                _self.eventHandler.observe(document, "click", hideme);
	};

	this.hide = function(e){
            _self.container.style.display = 'none';
            _self.param1 = null;
            _self.param2 = null;
	};

	this._createElement = function(){
		_self.container = document.createElement('div');
		_self.container.className = 'lmbContextMenu'; //'olContextMenu';
		//_self.container.id = 'limbasDivMenuContext';
		_self.container.style.position = 'absolute';
		_self.container.style.display = 'none';
		_self.parent_el.appendChild(_self.container);
	};

	this._createElement();
	return this;
}

function olContextMenuItem(title, parent, image, action){
	var _self = this;
	this.version = '$Id: olContextMenu.js 1174 2015-12-04 17:04:22Z axel $';

	this.title = title;
	this.parent = parent;
	this.action = action;
	this.child = (arguments[3]) ? arguments[3] : null;
	this.el = null;

	this._createElement = function(image){
		var span = document.createElement('span');
		//span.className = "lmbContextItemIcon";
		span.innerHTML = _self.title;

		_self.el = document.createElement('div');
		_self.el.className = 'lmbContextLink';
		_self.el.style.cursor = 'pointer';

		if (image){
			var img = document.createElement('i');
			img.className = "lmbContextLeft lmb-icon " + image;
			_self.el.appendChild(img);
		}

		_self.el.appendChild(span);
		_self.parent.container.appendChild(_self.el);
	};

	this._registerEventHandlers = function(){
		_self.parent.eventHandler.observe(_self.el, "click", function(e){
			if (_self.action(e, _self.parent.param1, _self.parent.param2))
				_self.parent.eventHandler.stop(e);
		});
	};

	this._createElement(image);
	this._registerEventHandlers();

	return this;
}
