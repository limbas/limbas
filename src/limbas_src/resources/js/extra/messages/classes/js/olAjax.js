/*
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */



function olAjax() {
	/* optional parameter : baseURL, default is current directory */
	this._baseURL = (arguments[0]) ? arguments[0] : location.href.replace(/(.*)\/.*/g, "$1/");
	
	/* optional parameter : JS_Exec flag, default is no subsequent execution of JS within XMLHttpReq response */
	this._js_exec = (arguments[1]) ? arguments[1] : false;
	
	/* public : holds an array of name/value pairs for POST data */
	this.postdata = new Array();
	
	/* public : target html element */
	this.el = null;
	this.version = '$Id: olAjax.js 375 2009-10-07 22:39:09Z daniel $';

	/* public : gets triggered when HTTP Status code <> 200 */
	this.onError = function (msg){ 
		if (this.el)
			this.el.innerHTML = msg;
		else
			throw new Error(msg);
	};

	/* public : gets triggered when request was successful and data is available */
	this.onComplete = function (msg){
		if (this.el)
			this.el.innerHTML = msg;
		else
			throw new Error("onComplete handler not implemented!");
	};
	
	/* public : send the HTTP Request */
	this.send = function (url){
		var slf = this;

		/* optional target element */
		this.el = ((typeof arguments[1]) != 'undefined') ? arguments[1] : null;

		/* optional request method */
		this.method = ((typeof arguments[2]) == 'string') ? arguments[2].toUpperCase() : 'GET';

		if ((typeof url) == 'string'){
			if ((url != '') && (this._baseURL.match(/\?/)))
				url = '&' + url.substr(1);
		} else
			url = '';
		
		try {
			this._req.open(this.method, this._baseURL + url, true);
		} catch(e) {
			this.onError(e.message);
		}

		this._req.onreadystatechange = function (){
			switch(slf._req.readyState){
				case 4:
					if (slf._req.status==0)
						break;
					if (slf._req.status==200){
						slf.onComplete(slf._req.responseText);
						if (slf._js_exec)
							slf._execJS(slf.el, slf._req.responseText);
					}else
						slf.onError("HTTP Error " + slf._req.status, slf._req.responseText);
					break;
				default:
			}
		};

		try {
			if (this.method == 'POST'){
				this._req.setRequestHeader("Content-type", "application/x-www-form-urlencoded"); 
				var data = "";
				for(idx in this.postdata)
						if (this.postdata[idx]!='undefined')
							data += encodeURI(this.postdata[idx]) + "&";
				this._req.send(data);
			} else /* if (this.method == 'GET') */
				this._req.send(null);
		} catch(e) {
			this.onError(e.message);
		}
	};

	/* private */
	this._execJS = function(node, code) {
//		if (navigator.userAgent.indexOf('MSIE') != -1){
			/* WARNING! THIS PART IS UGLY SLOW AND INCOMPLETE!
			 * ...used only for compatibility with MSIE */
//			code = code.replace(/[\s\S.]*\<script.*>/gi, '');
//			code = code.replace(/\<\/script.*/gi, '');
//			try { eval(code); }
//			catch(e) { alert (e.message); }
//			return;
//		}
		
		var bSaf = (navigator.userAgent.indexOf('Safari') != -1);
		var bMoz = (navigator.appName == 'Netscape');
		
		var st = node.getElementsByTagName('SCRIPT');
		var strExec;

		for(var i=0; i<st.length; i++) {
			if (bSaf)
				strExec = st[i].innerHTML;
			else if (bMoz)
				strExec = st[i].textContent;
			else
				strExec = st[i].text;
			
			try {
				eval(strExec.split("<!--").join("").split("-->").join(""));
			} catch(e) {
				alert (e);
			}
		}
	};

	/* private */
	this._createXMLHttpReq = function() {
		var ret = false;
		
		if(((window.ActiveXObject)=='undefined') || !(window.ActiveXObject))
			try { /* native XMLHttpRequest object */
				ret = new XMLHttpRequest();
			} catch(e) {
				throw new Error(e.message);
			}
		else { /* IE ActiveX object */
			try {
				ret = new ActiveXObject("Msxml2.XMLHTTP");
			} catch(e) { 
				try {
					ret = new ActiveXObject("Microsoft.XMLHTTP");
				} catch(e) { 
					throw new Error(e.message);
				}
			}
		}
		return (ret) ? ret : false;
	};

	/* private */
	this._req = this._createXMLHttpReq();
	
	return this;
}
