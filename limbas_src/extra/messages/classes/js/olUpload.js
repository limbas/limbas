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

function olUpload(url, w, h, sid, maxlen, debug){ 
	this.version = '$Id: olUpload.js 299 2009-09-18 12:41:03Z daniel $';
	this.obj = "olUploadObject"; eval(this.obj + "=this");
	this.el_cnt = 'upload_container';
	this.el_inr = 'upload_inner';
	this.el_res = 'upload_result';
	this.el_err = 'upload_error';
	this.el_swf = 'upload_swf';
	this.swf = 'olUpload.swf';
	this.doc = null;
	this.pos = 0;
	this.is_running = 0;
	
	this._init = function(url, w, h, sid, maxlen, debug){
		var el = $(this.el_cnt);
		var fv = 'sid=' + escape(sid);
		fv+='&url=' + escape(url);
		fv+='&maxlen=' + escape(maxlen);
		
		if (debug)
			fv+="&debug=1";

		if (navigator.appName.indexOf("Microsoft") != -1){
			el.innerHTML='<OBJECT CLASSID="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" '+
				'CODEBASE="http://active.macromedia.com/flash/cabs/swflash.cab#version=9,0,0,0" '+
				'WIDTH="'+w+'" HEIGHT="'+h+'" ID="'+this.el_swf+'" BGCOLOR="#e0e0e0">'+
				'<param name="movie" value="'+this.swf+'"> '+
				'<param name="Flashvars" value="'+fv+'"> '+
				'<param name="allowScriptAccess" value="always"></OBJECT>';
		} else {
			if (navigator.mimeTypes && navigator.mimeTypes["application/x-shockwave-flash"]){
				el.innerHTML='<embed src="'+this.swf+'" bgcolor="#e0e0e0" width="'+w+'" height="'+h+'" '+
					'name="'+this.el_swf+'" Flashvars="'+fv+'" type="application/x-shockwave-flash" '+
					'allowScriptAccess="always" pluginspage="http://www.adobe.com/go/getflashplayer" />';
			} else
				el.innerHTML = 'Bitte installieren Sie zuerst den <a href="http://www.adobe.com/go/getflashplayer">Adobe Flash Player</a>!';
		}
		
		$(this.el_inr).innerHTML = '<iframe class="'+this.el_res+'" id="'+this.el_res+'" src="about:blank" frameborder="0" vspace="0" hspace="0" marginwidth="0" marginheight="0" scrolling="auto"></iframe>';
		this.pos = 0;
	}

	this.reset = function(){
		this.clear();
		this.doc.close();
	};
	
	this.clear = function(){
		var frm = $(this.el_res);
		this.doc = frm.contentDocument;
		if (this.doc == undefined || doc == null)
			this.doc = frm.contentWindow.document;
			
		this.doc.open();
		this.doc.writeln('<html><head><link href="../styles/olUpload.css" rel="stylesheet" type="text/css">'+
			'</head><body id="body">');
		this.pos = 0;
	},
	
	this.browse = function(a,b){
		$('upload_page').style.display = 'none';
		this.clear();
		/* clear errors */
		var e = $(this.el_err);
		e.innerHTML = '';
		e.style.display = 'none';
	},

	this.start = function(c,s){
		$(this.el_inr).style.display = 'block';
		$('upload_page').style.display = 'block';
		this.is_running=1;
	};
	
	this.print = function(t){
		this.doc.writeln('<div class="upload_status">'+t+'</div>');
		$(this.el_res).contentWindow.scrollTo(0, this.pos+=24);
	};
	
	this.status = function(a,b){
		this.doc.writeln('<div class="upload_status">a='+a+', b='+b+'</div>');
		$(this.el_res).contentWindow.scrollTo(0, this.pos+=24);
	};
	
	this.begin = function(f){
		this.doc.writeln('<div class="upload_item"><img id="img_'+f+
			'" src="../images/wait.gif" border="0" width="20" height="20" align="top">'+f+
			'</div>'
		);
		$(this.el_res).contentWindow.scrollTo(0, this.pos+=32);
	};
	
	this.complete = function(f, s){
		el = this.doc.getElementById('img_'+f);
		el.src = "../images/doc.png";
	
		$(this.el_res).contentWindow.scrollTo(0, this.pos+=32);
	};

	this._delayed_close = function(){
		this.doc.writeln("</body></html>");  
		this.doc.close(); 
	},
	
	this.finished = function(complete, msg){
		if (this.is_running){
			this.is_running = 0;
			window.setTimeout(this._delayed_close, 200);
		}
	};
	
	this.error = function(f, d){
		var e = $(this.el_err);
		var span = document.createElement('span');
		var img = document.createElement('img');
		var div = document.createElement('div');
		
		img.src = "../images/error.gif";
		img.setAttribute("border", "0");
		img.setAttribute("width", "20");
		img.setAttribute("height", "20");
		img.setAttribute("align", "top");
		span.innerHTML = f+': '+d;

		div.appendChild(img);
		div.appendChild(span);
		e.appendChild(div);
		e.style.display = 'block';
		
		/* this.doc.writeln('<div class="upload_item"><img id="img_'+f+
			'" src="../images/error.gif" border="0" width="20" '+
			'height="20" align="top">'+f+'</div>'); */

			/*
		var elist = this.doc.getElementsByTagName('div');
		var el = elist[elist.length-1];
		
		if (el.innerHTML.indexOf("wait.gif")>0)
		el.innerHTML='<table border="0" width="120" height="120" cellpadding="0" cellspacing="0"><tr><td valign="top" align="center" height="98"><b>Fehler</b><small><p>'+d+'</p></small></td></tr><tr><td class="upload_item">'+f+'</td></tr></table>';
		*/
	};
	
	this._init(url, w, h, sid, maxlen, debug);
	this.reset();
	
	return this;
};
