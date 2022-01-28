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

if (typeof(_basePATH_) == "undefined")
	_basePATH_='';

function olPopup(id, heading) {
	var self = this;
	
	this.version = '$Id: olPopup.js 1174 2015-12-04 17:04:22Z axel $';
	this.eventHandler = new olEvent();

	this.el = null;
	this.container = null;
	this.picker = null;
	this.heading = null;
	this.status = null;
	this.sizer = null;
	this.imgR = null;
	
	this.width = arguments[2] ? arguments[2] : 320;
	this.height = arguments[3] ? arguments[3] : 240;
	this.baseurl = arguments[4] ? arguments[4] : null;
	
	this.offsetX = 0;
	this.offsetY = 0;
	this.resizeOffsetX =0;
	this.resizeOffsetY =0;
	this.resizeOffsetW = this.width;
	this.resizeOffsetH = this.height;
	
	if (!id)
		throw new Error('Must define an id!');
		
	if (!heading)
		throw new Error('Must define a heading!');
	
	this._initialize = function(id, heading){
		var img = document.createElement("img");
		img.src = _basePATH_  + "images/close.gif";
		img.id = id + "_closeimg";
		img.setAttribute("class", "popup_closeimg");
		img.setAttribute("border", "0");
		img.setAttribute("alt", "X");
		img.setAttribute("title", "Fenster schliessen");
		self.eventHandler.observe(img, 'click', function(e){self.hide(self)});

		this.resize_mu = function(e){
			self.eventHandler.stop(e);
			self.eventHandler.remove(document, 'mouseup', self.resize_mu);
			self.eventHandler.remove(document, 'mousemove', self.resize_mm);
		};
		
		this.resize_mm = function(e){
			var w = self.eventHandler.getX(e) - self.resizeOffsetX;
			var h = self.eventHandler.getY(e) - self.resizeOffsetY;
			if ((w += self.resizeOffsetW) < 128)
				w = 128;
			if ((h += self.resizeOffsetH) < 128)
				h = 128;
			self.resize(w, h);
		};
		
		this.resize_md = function(e){
			self.resizeOffsetW = self.container.offsetWidth;
			self.resizeOffsetH = self.container.offsetHeight;
			self.resizeOffsetX = self.eventHandler.getX(e);
			self.resizeOffsetY = self.eventHandler.getY(e);
			self.eventHandler.stop(e);
			self.eventHandler.observe(document, 'mousemove', self.resize_mm);
			self.eventHandler.observe(document, 'mouseup', self.resize_mu);
		};
		
		this.imgR = document.createElement("img");
		this.imgR.src = _basePATH_ + "images/resize.png";
		this.imgR.id = id + "_sizeimg";
		//this.imgR.setAttribute("class", "popup_sizeimg");
		this.imgR.setAttribute("border", "0");
		this.imgR.setAttribute("alt", "#");
		this.imgR.setAttribute("title", "Fenstergröße ändern");
		self.eventHandler.observe(this.imgR, 'mousedown', self.resize_md);
		self.eventHandler.observe(this.imgR, 'drag', function(e){self.eventHandler.stop(e);});
                
                self.eventHandler.observe(window, 'mouseup', function(e){self.hide(self)});                
		//this.heading = document.createElement("p");
		//this.heading.innerHTML = heading;
		//this.heading.id = id + "_heading";
		//this.heading.setAttribute("class", "lmbContextItem");

		this.picker = document.createElement("div");
		//this.picker.id = id + "_picker";
		this.picker.setAttribute("class", "lmbContextItem");

		//this.picker.appendChild(this.heading);
		//this.picker.appendChild(img);

		this.status = document.createElement("div");
		this.status.innerHTML = heading;
		//this.status.id = id + "_status";
		this.status.setAttribute("class", "lmbContextItem");

		this.sizer = document.createElement("div");
		//this.sizer.id = id + "_sizer";
		this.sizer.setAttribute("class", "lmbContextItem");
		this.sizer.appendChild(this.status);
		this.sizer.appendChild(this.imgR);
		
		
		this.el = document.createElement("div");
		//this.el.id = id + "_inner";
		this.el.setAttribute("class", "lmbContextItem");
		this.el.style.overflow="auto";
		
		this.container = document.createElement("div");
		this.container.id = id + "_container";
		this.container.setAttribute("class", 'ajax_container');//"popup_container");
		this.container.style.zIndex = '2';
		this.container.style.width = this.width+'px';
		this.container.style.height = this.height+'px';

                var wrap = document.createElement("div");
		wrap.setAttribute("class", "lmbContextRow");
                wrap.style.padding = 0;
                
		wrap.appendChild(this.picker);
		wrap.appendChild(this.el);
		//wrap.appendChild(this.sizer);
		this.container.appendChild(wrap);
		document.body.appendChild(this.container);
		
		this.el.style.height = (this.height-this.picker.offsetHeight-this.sizer.offsetHeight)+'px';
		
		if (navigator.userAgent.indexOf('MSIE') != -1) // fix ie bug
			this.el.style.width = (this.width-2)+'px';
			
		this.container.style.display = 'none';
	};


	this.resize = function(w, h){
		self.width = w;
		self.height = h;
		self.container.style.width = self.width + 'px';
		self.container.style.height = self.height + 'px';
		self.el.style.height = (self.height-self.picker.offsetHeight-self.sizer.offsetHeight) + 'px';
	};
	
	this.onWait = function(el,msg){
		el.innerHTML = '<table style="margin:auto;" border="0"><tr><td><img src="'+_basePATH_+'images/wait.gif" '+
			'width="20" height="20" border="0"></td><td>' + msg + ' wird geladen...</td></tr></table>';
	};
	this.setCaption = function(caption){
		this.heading.innerHTML=caption;
	};
	this.setStatus = function(status){
		this.status.innerHTML=status;
	};

	this.show = function (url, _use_ajax, _ajax_execJS){
		var self = this;
		var ajax = null;
		
		var active = (_use_ajax) ? _use_ajax : false;
		var active_js = (_ajax_execJS) ? _ajax_execJS : false;
		
		var xPos = (arguments[3]) ? arguments[3] : -1;
		var yPos = (arguments[4]) ? arguments[4] : -1;
		var baseurl = (arguments[5]) ? arguments[5] : self.baseurl;
		
		var observeEvents = function(){
			self.eventHandler.observe(document, 'selectstart', self._stop);
			self.eventHandler.observe(document, 'mouseup', self._mouseup);
			self.eventHandler.observe(self.picker, 'mousedown', self._mousedown);
		};

		observeEvents();
		if (!active) {
			if (xPos>-2)
				self.eventHandler.setPosition(self.container, xPos, yPos);
			else { 
				self.container.style.display = 'block';
				self.container.style.position = 'absolute';
			}
			self.el.innerHTML=url;
		} else {
			ajax = new olAjax(baseurl, active_js);
			ajax.onComplete = function (){
				this.el.innerHTML = arguments[0];
			};
			ajax.onError = function (){
				self.onError(this.el, (arguments[1]) ? arguments[1] : arguments[0]);
			};
			self.onWait(self.el, heading);
			if (xPos>-2)
				self.eventHandler.setPosition(self.container, xPos, yPos);
			else { 
				self.container.style.display = 'block';
				self.container.style.position = 'absolute';
			}

			ajax.send(url, self.el);
		}
	};
	
	this.hide = function(self){
		self.container.style.display = 'none';
		self.eventHandler.remove(document, 'mouseup', self._mouseup);
		self.eventHandler.remove(document, 'selectstart', self._stop);
		self.eventHandler.remove(self.picker, 'mousedown', self._mousedown);
		return false;
	};
	
	this.onError = function(el, msg){
		el.innerHTML = '<table style="margin:auto;" border="0"><tr><td><img src="'+_basePATH_+'images/error.gif" '+
			'width="35" height="35" border="0"></td><td>' + msg + '</td></tr></table>';
	};

	this._mousedown = function (e){
		self.eventHandler.stop(e);
		self.offsetX = self.eventHandler.getX(e) - self.container.offsetLeft;
		self.offsetY = self.eventHandler.getY(e) - self.container.offsetTop;
		self.eventHandler.observe(document, 'mousemove', self._mousemove);
	};

	this._mousemove = function(e){
		var x = self.eventHandler.getX(e) - self.offsetX;
		var y = self.eventHandler.getY(e) - self.offsetY;
		if (y<0) y=0;
		if (x<0) x=0;
		self.container.style.top = y + 'px';
		self.container.style.left = x + 'px';
	};
	
	this._mouseup = function(e){
		self.eventHandler.remove(document, 'mousemove', self._mousemove);
	};

	this._stop = function (e){
		self.eventHandler.stop(e);
	};
	
	this._initialize(id, heading);
	return this;
}
