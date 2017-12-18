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

function olListSelection(table) {
	var _self = this;
	
	this.version = '$Id$';

	this.table = table;
	this.rows = null;
	
	this.behaviour = ((typeof arguments[1]) != 'undefined')
		? arguments[1] : 0;

	this.colorHighlight = '#fbe16b';
	//this.colorHighlight2 = '#ff00ff';
	
	this.onDragDrop = null; // optional DragDrop initialisation handler
	this.onSelect = null; // optional Select-handler
	this.onUnselect = null; // optional Unselect-handler
	
	/* public methods  */
	this.initialize = function(){
		_self.rows = _self.table.getElementsByTagName("tr");

		for (var idx=1; idx < _self.rows.length; idx++)
			_self._onIterateElement(idx);
	};

	this.select = function(row){
		_self._toggle_selection(row, true);
	};

	this.unselect = function(row){
		_self._toggle_selection(row, false);
	};

	this.clear = function(){
		for (var idx=1; idx < _self.rows.length; idx++)
			_self._toggle_selection(_self.rows[idx], false);
	};
	
	this.getCount = function(){
		var ret = 0;
		for (var idx=1; idx < _self.rows.length; idx++)
			if (_self.rows[idx].getAttribute('name') == 'marked')
				ret++;
		return ret;
	};
	
	this.getUIDs = function(){
		var ret = '';
		for (var idx=1; idx < _self.rows.length; idx++)
			if (_self.rows[idx].getAttribute('name') == 'marked')
				ret += _self.rows[idx].id + ',';
		return ret;
	};
	
	this.getString = function(){
		return _self.table.id + ":" + this.getUIDs();
	};

	/* private methods  */
	this._onAction = function (e, table, el){
		if ((typeof _self.onAction) == 'undefined')
			throw new Error("olListSelection::onAction() not implemented!");
		_self.onAction(e, table, el);
	};
	
	this._onContext = function (e, el){
		if ((typeof _self.onContext) == 'undefined')
			throw new Error("olListSelection::onContext() not implemented!");
		_self.onContext(_self, e, el);
	};
	
	this._getTarget = function(el){
		if (el.tagName=='TD') return el.parentNode;
		else if (el.tagName=='A') return el.parentNode.parentNode;
		return null;
	};
	
	this._onIterateElement = function(idx){
		var table = _self.table;
		var row = _self.rows[idx];
		
		/* if available, register custom DragDrop-Handler... */
		if ((typeof _self.onDragDrop) == 'function')
			_self.onDragDrop(self, table, row);
		else {
			/* otherwise prevent selection of browser text ... */
			E.observe(row, 'mousedown', function(e){E.stop(e)}); /* within webbrowsers... */
			E.observe(row, 'selectstart', function(e){E.stop(e)}); /* and also within MSIE. */
		}
		
		/* handle selection, deselection and action for a list items. */
		if (_self.behaviour) { /* M$-Style GUI behaviour */
			E.observe(row, 'dblclick', function(e){
				var el = _self._getTarget((e.target) ? e.target : e.srcElement);
				if (el)
					_self._onAction(e, table, el);
			});
			E.observe(row, 'click', function(e){
				var el = _self._getTarget((e.target) ? e.target : e.srcElement);
				if (!el) return;
				if(e.ctrlKey)
					_self._toggle_selection(el);
				else if (e.shiftKey)
					_self._select_range(el.parentNode, el);
				else {
					_self.clear();
					_self._toggle_selection(el);
				}
			});
		} else { /* WEB-Style GUI behaviour */
			E.observe(row, 'click', function(e){
				var el = _self._getTarget((e.target) ? e.target : e.srcElement);
				if (!el) return;
				if (e.altKey){
					_self._onContext(e, el);
					E.stop(e);
				} else if(e.ctrlKey)
					_self._toggle_selection(el);
				else if (e.shiftKey)
					_self._select_range(el.parentNode, el);
				else {
					_self.clear();
					_self._toggle_selection(el);
					_self._onAction(e, table, el);
				}
			});
		}
			
		/* contextmenu and hot-tracking effects are identical in all behaviour modes. */
		E.observe(row, "contextmenu", function(e){
			var el = _self._getTarget((e.target) ? e.target : e.srcElement);
			if (el){
				_self._onContext(e, el);
				E.stop(e);
			}
		});

		E.observe(row, 'mouseover', function(e){
			var el = _self._getTarget((e.target) ? e.target : e.toElement);
			if (el)
				el.style.backgroundColor = /* (el.getAttribute('name') == 'marked')
					? _self.colorHighlight2 : */ _self.colorHighlight;
		});

		E.observe(row, 'mouseout', function(e){
			var el = _self._getTarget((e.target) ? e.target : e.fromElement);
			if (el)
				el.style.backgroundColor = (el.getAttribute('name') == 'marked')
					? _self.colorHighlight : '';
		});
	};	

	this._toggle_selection = function(el){
               
                    var hide = (typeof arguments[1] != 'undefined') && (arguments[1] == false);
                    var show = (typeof arguments[1] != 'undefined') && (arguments[1] == true);

                    if (!show){
                            if (hide || (el.getAttribute('name') == 'marked')){
                                    el.style.backgroundColor = '';
                                    el.setAttribute('name', "unmarked");
                                    if ((typeof _self.onUnselect) == 'function')
                                            _self.onUnselect(_self, el);
                                    return;
                            }
                    }
                if (el.style.display !== 'none')
                {
                    el.style.backgroundColor = _self.colorHighlight;
                    el.setAttribute('name', "marked");

                    if ((typeof _self.onSelect) == 'function')
                            _self.onSelect(_self, el);
                }
	};

	this._select_range = function(el, dst){
		var srcidx, dstidx;

		for (srcidx=1; srcidx < _self.rows.length; srcidx++)
			if (_self.rows[srcidx].getAttribute('name') == 'marked')
				break;

		for (dstidx=1; dstidx < _self.rows.length; dstidx++)
			if (_self.rows[dstidx]==dst)
				break;

		if (srcidx > dstidx) {
			var _tmp = dstidx;
			dstidx = srcidx;
			srcidx = _tmp;
		}

		do {
			_self._toggle_selection(_self.rows[srcidx], true);
		} while(srcidx++ < dstidx);
		
		if ((typeof _self.onUnselect) == 'function')
			_self.onUnselect(_self, el);
	};

	return this;
}
