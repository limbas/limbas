/*
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */



var onMessage = function(a) { player.onMessage(a) };
var onPlayerReady = function(a) { player.onPlayerReady(a) };

function olInlineAudio(id, swf, width, height, baseurl){
	this.play = function(){
		document.getElementById('olInlineAudio_play').style.display = 'none';
		document.getElementById('olInlineAudio_stop').style.display = 'block';
		this.cmd("play", arguments[0] ? arguments[0] : 0);
	};
	this.stop = function(){
		document.getElementById('olInlineAudio_stop').style.display = 'none';
		document.getElementById('olInlineAudio_play').style.display = 'block';
		this.onMessage('');
		this.cmd("stop", "");
	};

	this.cmd = function(arg1, arg2){
		var is_MSIE = (navigator.appName.indexOf("Microsoft") != -1);
		try {
			if (typeof (is_MSIE ? window[id+"_swf"].command :
					document[id+"_swf"].command) == "undefined")
				this.onMessage("Flash Player not available?");
			else
				return is_MSIE ?
					window[id+"_swf"].command(arg1, arg2) :
					document[id+"_swf"].command(arg1, arg2);
		} catch (e) {
			this.onMessage(e);
		}
		return false;
	};
	
	this.onMessage = function(msg){
		var el = document.getElementById('olInlineAudio_message');
		el.innerHTML = "<div>" + msg + "</div>";
	};
	
	this.onPlayerReady = function (cnt){
		this.play();
	};
	
	document.getElementById(id).innerHTML = '<OBJECT cla'+
		'ssid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="'+
		'http://download.macromedia.com/pub/shockwave/cabs/flash/swfl'+
		'ash.cab#version=9,0,0,0" width="'+width+'" height="'+height+
		'" id="'+id+'_swf"><PARAM NAME="FlashVars" VALUE="url='+baseurl+
		'"><PARAM NAME="movie" VALUE="'+swf+'"><PARAM NAME="bgcolor" VALUE'+
		'="#ffffff"><PARAM name="allowScriptAccess" value="always"><EMBED src="'+swf+'" bgcolor="#ffffff" wi'+
		'dth="'+width+'" height="'+height+'" FlashVars="url='+baseurl+'" allowscriptaccess="always" '+
		'name="'+id+'_swf" type="application/x-shockwave-flash" pluginspage="http://www.'+
		'macromedia.com/go/getflashplayer"></EMBED></OBJECT>';
		
	return this;
};
