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

function _(){ var content = '<H5>'+arguments[0]+'<H5>', cnt=0; 	for (var i in
arguments[0]){ cnt++;	t = typeof arguments[0][i];	content += "<p><b>"+ i +
"</b>("+t+") = " + arguments[0][i] + "</p>"; } content += '<HR>'+ cnt +
' properties.'; $('mail_status').innerHTML = content; }

function set_cursor(name){ document.getElementsByTagName("body")[0].style.cursor = name; }

function mail_check(){
	mail_observe("mail_link", "click", link_mail);
	mail_observe("mail_delete", "click", delete_mail);
	mail_observe("mail_new", "click", new_mail);
	mail_observe("mail_open", "click", open_mail);
	mail_observe("mail_refresh", "click", refresh_mail);
}

function mail_observe(name,event_name,func){
	var elems = document.getElementsByName(name);
	if (elems && elems.length != 0)
		for (var i = 0; i < elems.length; i++)
			olEvent().observe(elems[i], event_name, func);
}

function open_mail(e){
	var _el = (e.target) ? e.target : e.srcElement;
	window.open(_el.href,
		'_blank',
		"location=no,scrollbars=yes,toolbar=no,resizable=yes" + ",width=600,height=400");
}

function new_mail(e){
	var _mail_new_el = (e.target) ? e.target : e.srcElement;
	var _mail_new = _mail_new_el.id.split("_");
	var el = $('mail_status_'+_mail_new[1]+"_"+_mail_new[2]);

	var _win = window.open("main_dyns.php?actid=extComposeMessage"
		+ "&gtabid="+_mail_new[1]+"&fldid="+_mail_new[2]+"&id="+_mail_new[3]+"&edittype="+_mail_new[4],
		'_blank',
		"location=no,scrollbars=yes,toolbar=no,resizable=yes" + ",width=600,height=400");
	_win.focus();
}

function delete_mail(e){
	var _mail_delele_el = (e.target) ? e.target : e.srcElement;
	var _mail_delete = _mail_delele_el.id.split("_");

	var ajax = new olAjax("main_dyns.php?actid=extDelRelMessages"
		+"&rids="+_mail_delete[0]
		+"&gtabid="+_mail_delete[1]+"&fldid="+_mail_delete[2]+"&id="+_mail_delete[3]+"&edittype="+_mail_delete[4]);

	var el = $('mail_status_'+_mail_delete[1]+"_"+_mail_delete[2]);
	set_cursor("wait");

	ajax.onComplete = function(content){
		set_cursor("default");
		$("extRelationFieldsTab_"+_mail_delete[1]+"_"+_mail_delete[2]).innerHTML = content;
		mail_check();
	};
	ajax.onError = function (content){
		set_cursor("default");
		el.innerHTML += "<br>Error:"+content + (arguments[1] ? " ("+arguments[1]+")" : '');
	};

	el.innerHTML += "Nachricht wird gel&ouml;scht...";
	ajax.send('msgs=' + content, el);
}

function link_mail(e){
	var _mail_link_el = (e.target) ? e.target : e.srcElement;
	var _mail_link = _mail_link_el.id.split("_");

	var _cookie = olCookie().get("mail_marked");
	if (!_cookie){
		alert("Keine Nachrichten vorgemerkt.");
		return;
	}
	var tmp = _cookie.split(':');

	var ajax = new olAjax("main_dyns.php?actid=extAddRelMessages"
		+"&mbox="+tmp[0]+"&ids="+tmp[1]
		+"&gtabid="+_mail_link[0]+"&fldid="+_mail_link[1]+"&id="+_mail_link[2]+"&edittype="+_mail_link[3]);

	var el = $('mail_status_'+_mail_link[0]+"_"+_mail_link[1]);
	set_cursor("wait");

	ajax.onComplete = function(content){
		olCookie().remove("mail_marked");
		set_cursor("default");
		$("#extRelationFieldsTab_"+_mail_link[0]+"_"+_mail_link[1]).innerHTML = content;
		mail_check();
	};

	ajax.onError = function (content){
		olCookie().remove("mail_marked");
		set_cursor("default");
		el.innerHTML += "<br>Error:"+content + (arguments[1] ? " ("+arguments[1]+")" : '');
	};

	el.innerHTML += "Nachricht wird verkn&uuml;pft...";
	ajax.send('msgs=' + content, el);
}

function refresh_mail(e){
	var _el = (e.target) ? e.target : e.srcElement;
	var _params = _el.id.split("_");
	var el = $('mail_status_'+_params[0]+"_"+_params[1]);

	var ajax = new olAjax("main_dyns.php?actid=extMessagesDisplay"
		+"&gtabid="+_params[0]+"&fldid="+_params[1]+"&id="+_params[2]
		+"&edittype="+_params[3]);
		// +"&gformid="+_params[4]
		//+(_params[4] ? "&viewmode="+_params[4] : ""));

	ajax.onComplete = function(content){
		$("#extRelationFieldsTab_"+_params[0]+"_"+_params[1]).innerHTML = content;
		mail_check();
	};
	ajax.onError = function (content){
		el.innerHTML += "<br>Error:"+content + (arguments[1] ? " ("+arguments[1]+")" : '');
	};

	ajax.send('msgs=' + content, el);
}


// {$gtabid}_{$fieldid}_{$ID}_{$typ}_{$gformid}
var mail_search_box = null;
function search_mails(el, info) {
    var params = info.split("_");
    mail_search_box = el;

    mainfunc = function(result) {
        $("#lmbAjaxContainer").html(result);
        limbasDivShow(mail_search_box,'','lmbAjaxContainer');
	};

    ajaxGet(null, "main_dyns.php", "extSearchMessages"
	+ "&gtabid=" + params[0]
	+ "&fldid=" + params[1]
	+ "&id=" + params[2]
	+ "&edittype=" + params[3]
	+ "&gformid=" + params[4]
	+ "&search=" + encodeURIComponent(el.value), null, "mainfunc");
}

function link_search_mail(uid, info) {
    var params = info.split("_");

    mainfunc = function(result) {
        $("#extRelationFieldsTab_"+params[0]+"_"+params[1]).html(result);
        mail_check();
    };

    ajaxGet(null, "main_dyns.php", "extAddRelMessages"
	+ "&mbox=INBOX"
    + "&gtabid=" + params[0]
    + "&fldid=" + params[1]
    + "&id=" + params[2]
    + "&edittype=" + params[3]
    + "&gformid=" + params[4]
	+ "&ids=" + uid, null, "mainfunc");
}