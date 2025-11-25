/*
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */


//onload
$(function () {
	$('[data-decrypt]').click(lmbAjax_decryptField);
});


function lmbAjax_PreViewLongtext(evt, gtabid, gfieldid, ID) {
    var actid = "actid=gresultPreView&gtabid=" + gtabid + "&gfieldid=" + gfieldid + "&ID=" + ID;
    $.ajax({
        type: "GET",
        url: "main_dyns.php",
        async: false,
        data: actid,
        success: function (data) {
            document.getElementById("lmbAjaxContainer").innerHTML = data;
            limbasDivShow('', evt, 'lmbAjaxContainer');
        }
    });
}

// Ajax post formelements
function lmbAjax_postHistoryResult(form, sync) {
	var url = "main_dyns.php";
	var actid = "postHistoryFields";
	ajaxGet(null, url, actid, null, "lmbAjax_postHistoryResultPost", form, null, sync);
}

// Ajax post formelements output
function lmbAjax_postHistoryResultPost(result) {
	if (result) {
		ajaxEvalScript(result);
		document.form1.history_fields.value = '';
	}
}

// Ajax color select
function limbasAjxColorSelect(evt, formname, gtabid, fieldid, id) {
	url = "main_dyns.php";
	actid = "colorSelect&formname=" + formname + "&gtabid=" + gtabid + "&fieldid=" + fieldid + "&id=" + id + "&container=lmbAjaxContainer";
	mainfunc = function (result) {
		limbasAjxColorSelectPost(result, evt);
	};
	ajaxGet(null, url, actid, null, "mainfunc");
}

// Ajax color select output
function limbasAjxColorSelectPost(result, evt) {
	document.getElementById("lmbAjaxContainer").innerHTML = result;
	limbasDivShow('', evt, "lmbAjaxContainer");
}

// Ajax color select action
function limbasColorSelectSet(val, formname, gtabid, fieldid, id) {
	document.getElementById(formname).value = val;
	document.getElementById(formname).style.backgroundColor = val;
	document.getElementById(formname).onchange();
}

/* --- dynsPress ----------------------------------- */
function lmbAjax_dynsearch(evt, el, actid, fname, par1, par2, par3, par4, par5, par6, gformid, formid, datatype, nextpage) {
	if (evt === 'object') {
		if (evt.keyCode == 27) {
			var el = document.getElementById(el.name + "l");
			el.innerHTML = '';
			return;
		}
	}

	if (!par5) {
		par5 = '';
	}
	if (!par6) {
		par6 = '';
	}
	if (!formid) {
		formid = '';
	}
	if (!gformid) {
		gformid = '';
	}
	if (!nextpage) {
		nextpage = '';
	}

	if (!el) {
		el = document.getElementsByName(fname + "_ds")[0];
	}

	if (typeof evt === 'string') {
		dyns_value = evt;
	} else {
		dyns_value = el.value;
	}

	if (dyns_value.length > 1 || dyns_value == '*' || nextpage) {
		url = "main_dyns.php";
		actid = actid + "&form_name=" + fname + "&form_value=" + dyns_value + "&par1=" + par1 + "&par2=" + par2 + "&par3=" + par3 + "&par4=" + par4 + "&par5=" + par5 + "&par6=" + par6 + "&gformid=" + gformid + "&formid=" + formid + "&nextpage=" + nextpage;
		mainfunc = function (result) {
			lmbAjax_dynsearchPost(result, el);
		};
		ajaxGetWait(null, url, actid, null, "mainfunc", '', 500);
	} else if (dyns_value == '') { // drop relation for ajax relation fields if empty
		// ajax select
		if (datatype == '12') {
			var d = '';
			// relation
		} else if (datatype == '32') {
			var d = '#L#d' + document.getElementById(fname).value;
			// relation
		} else {
			var d = '#L#delete';
		}
		document.getElementById(fname).value = d;
		checktyp(24, fname, '', par4, par3, '', par5);
	}
	oMultipleSelectLoader = new MultipleSelectShow("oMultipleSelectLoader");
}

/* --- dynsShow ----------------------------------- */
function lmbAjax_dynsearchPost(value, el) {
	//if(document.getElementById("lmbAjaxContainer").style.display == 'none' || document.getElementById("lmbAjaxContainer").style.visibility == 'hidden'){
	limbasDivShow(el, null, 'lmbAjaxContainer', '', '', 1);
	activ_menu = 0;
	//}
	document.getElementById("lmbAjaxContainer").innerHTML = value;
}

/* --- dynsClose ----------------------------------- */
function dynsClose(id) {
	var el = document.getElementById(id);
	el.innerHTML = '';
	$('#lmbAjaxContainer').hide();
}

// Ajax extended relation
function LmExt_RelationFields(el, gtabid, gfieldid, viewmode, edittype, ID, orderfield, relationid, ExtAction, ExtValue, gformid, formid, ajaxpost, event) {

	var relationkeyid = '';

	if (!gformid) {
		gformid = $('#extRelationFieldsTab_' + gtabid + "_" + gfieldid).attr("data-gformid");
	}

	// hide sortmenu
	$('#limbasDivRowSetting').hide();

	lmbCollReplaceSource = null;
	if (ExtAction == "showall") {
		var tel = document.getElementById("extRelationFieldsTab_" + gtabid + "_" + gfieldid);
		if (!gformid) {
			if (tel.style.height == '110px') {
				tel.style.height = '';
				tel.style.overflow = 'visible';
				relationid = '1';
			} else {
				tel.style.height = '110px';
				tel.style.overflow = 'auto';
				relationid = '0';
			}
		}
	} else if (ExtAction == "delete" || ExtAction == "unlink" || ExtAction == "copy" || ExtAction == "trash" || ExtAction == "archive") {
		if (ExtAction == "delete") {
			var mess = jsvar['lng_2153'];
		} else if (ExtAction == "unlink") {
			var mess = jsvar['lng_2359'];
		} else if (ExtAction == "copy") {
			var mess = jsvar['lng_2156'];
		} else if (ExtAction == "trash") {
			var mess = jsvar['lng_3102'];
		} else if (ExtAction == "archive") {
			var mess = jsvar['lng_3103'];
		}
		var actrows_keyid = Array();
		if (!relationid) {
			var actrows_ = checkActiveRows(ExtValue, 1);
			var actrows = actrows_[0];
			var actrows_keyid = actrows_[1];
		} else {
			var actrows = new Array(relationid);
		}

		ExtValue = '';
		if (actrows.length > 0) {
			if (!confirm(mess + ' (' + actrows.length + ')')) {
				return;
			}

			// multirelation
			//for (var i in actrows) {
			//    actrows_keyid[i] = $('#'+actrows[i]).attr("data-keyid");
			//}
			var relationid = actrows.join(",");
			var relationkeyid = actrows_keyid.join(",");

		} else {
			alert(jsvar["lng_2083"]);
			return;
		}
	} else if (ExtAction == "replace") {
		lmbCollReplaceSource = Array(gtabid, gfieldid, ID, gformid, formid);
		limbasCollectiveReplace(event, el, 0, ExtValue);
		return;
	} else if (ExtAction == "unlinkall") {
		if (!confirm(jsvar['lng_2776'])) {
			return;
		}
	}

	var url = "main_dyns.php";

	// ajax based POST of complete formular
	if (ajaxpost) {
		document.getElementById("myExtForms").innerHTML = "<input type='hidden' name='gfieldid' value='" + gfieldid + "'><input type='hidden' name='viewmode' value='" + viewmode + "'><input type='hidden' name='ExtAction' value='" + ExtAction + "'><input type='hidden' name='ExtValue' value='" + ExtValue + "'><input type='hidden' name='edittype' value='" + edittype + "'><input type='hidden' name='ID' value='" + ID + "'><input type='hidden' name='orderfield' value='" + orderfield + "'><input type='hidden' name='relationid' value='" + relationid + "'><input type='hidden' name='gformid' value='" + gformid + "'><input type='hidden' name='formid' value='" + formid + "'>";
		var actid = "extRelationFields&gtabid=" + gtabid + "&gfieldid=" + gfieldid + "&ID=" + ID + "&edittype=" + edittype + "&relationid=" + relationid + "&relationkeyid=" + relationkeyid + "&ExtAction=" + ExtAction; // overwrite form
		dynfunc = function (result) {
			LmExt_RelationFieldsPost(result, gtabid, gfieldid);
		};
		ajaxGet(null, url, actid, null, "dynfunc", "form1");
		document.getElementById("myExtForms").innerHTML = "";
	} else {
		if (el) {
			var text = el.innerHTML;
		}

		// use text of child span .lmbContextItemIcon if the context menu functions were used
		var contextChildren = $(el).children('.lmbContextItemIcon');
		if (contextChildren.length > 0) {
			text = contextChildren.first().html();
		}

		// no ajax update (userdefined formular) - use formid as formname
		if (formid && isNaN(formid)) {
			var formElement = document.getElementsByName(formid)[0];
			if (formElement) {
				formElement.value = relationid;
				document.getElementById(formid + "_ds").value = text;
			}
			// no ajax update (ajax based select)
			// detail
		} else if (document.getElementById("g_" + gtabid + "_" + gfieldid + "_ds") && el && !document.getElementById("extRelationFieldsTab_" + gtabid + "_" + gfieldid)) {
			document.getElementsByName("g_" + gtabid + "_" + gfieldid)[0].value = relationid;
			checktyp('27', 'g_' + gtabid + '_' + gfieldid, '', gfieldid, gtabid, relationid, ID);
			document.getElementById("g_" + gtabid + "_" + gfieldid + "_ds").value = text;
			if (document.getElementById("g_" + gtabid + "_" + gfieldid + "_ds").onchange) {
				document.getElementById("g_" + gtabid + "_" + gfieldid + "_ds").onchange();
			}
			// tablelist
		} else if (document.getElementById("g_" + gtabid + "_" + gfieldid + "_" + ID + "_ds") && el && !document.getElementById("extRelationFieldsTab_" + gtabid + "_" + gfieldid)) {
			document.getElementsByName("g_" + gtabid + "_" + gfieldid + "_" + ID)[0].value = relationid;
			checktyp('27', 'g_' + gtabid + '_' + gfieldid, '', gfieldid, gtabid, relationid, ID);
			document.getElementById("g_" + gtabid + "_" + gfieldid + "_" + ID + "_ds").value = text;
			if (document.getElementById("g_" + gtabid + "_" + gfieldid + "_" + ID + "_ds").onchange) {
				document.getElementById("g_" + gtabid + "_" + gfieldid + "_" + ID + "_ds").onchange();
			}
			document.getElementById("g_" + gtabid + "_" + gfieldid + "_" + ID).click();
			// ajax update GET of single relation
		} else {
			confirm_submit = function () {
				var actid = "extRelationFields&gtabid=" + gtabid + "&gfieldid=" + gfieldid + "&viewmode=" + viewmode + "&edittype=" + edittype + "&ID=" + ID + "&orderfield=" + orderfield + "&relationid=" + relationid + "&relationkeyid=" + relationkeyid + "&ExtAction=" + ExtAction + "&ExtValue=" + ExtValue + "&gformid=" + gformid + "&formid=" + formid;
				dynfunc = function (result) {
					LmExt_RelationFieldsPost(result, gtabid, gfieldid, viewmode, el, ID);
				};
				ajaxGet(null, url, actid, null, "dynfunc");
			}

			validateAction = ['unlink', 'link', 'create', 'unlinkall', 'delete', 'copy'];
			var query = {relationid: relationid, gfieldid: gfieldid, gtabid: gtabid};

			if (jsvar['gtab_validate_' + gtabid] && validateAction.includes(ExtAction)) {
				if (validate_request(ExtAction, query)) {
					confirm_submit();
				}
			} else {
				confirm_submit();
			}

			var actid = "extRelationFields&gtabid=" + gtabid + "&gfieldid=" + gfieldid + "&viewmode=" + viewmode + "&edittype=" + edittype + "&ID=" + ID + "&orderfield=" + orderfield + "&relationid=" + relationid + "&relationkeyid=" + relationkeyid + "&ExtAction=" + ExtAction + "&ExtValue=" + ExtValue + "&gformid=" + gformid + "&formid=" + formid;

		}
	}

	if (ExtAction == "showall") {
		return;
	}


	if ((ExtAction == "link" || ExtAction == "unlink" || ExtAction == "unlinkall") && !(event && event.shiftKey)) {
		document.getElementById('lmbAjaxContainer').style.display = 'none';
	}
}


// Ajax extended relation output
function LmExt_RelationFieldsPost(result, gtabid, gfieldid, viewmode, textel, ID) {

	selected_rows = new Array();
	if (document.getElementById("extRelationFieldsTab_" + gtabid + "_" + gfieldid)) {
		document.getElementById("extRelationFieldsTab_" + gtabid + "_" + gfieldid).innerHTML = result;
		// for detailview
	} else if (document.getElementById("g_" + gtabid + "_" + gfieldid + "_ds")) {
		el = document.getElementById("g_" + gtabid + "_" + gfieldid + "_ds");
		el.value = result;
		if (el.onchange) {
			el.onchange();
		}
		// for tablelist
	} else if (document.getElementById("g_" + gtabid + "_" + gfieldid + "_" + ID + "_ds")) {
		el = document.getElementById("g_" + gtabid + "_" + gfieldid + "_" + ID + "_ds");
		el.value = result;
		if (el.onchange) {
			el.onchange();
		}
	}
	ajaxEvalScript(result);

	var iconel = document.getElementById("extRelationFieldsPIC_" + gtabid + "_" + gfieldid + "_" + viewmode);

	if (iconel) {
		if (iconel.style.display == 'none') {
			iconel.style.display = '';
			textel.style.color = 'green';
			textel.className = 'lmbContextItemIcon';
		} else {
			iconel.style.display = 'none';
			textel.style.color = '';
			textel.className = 'lmbContextItem';
		}
	}

	//$('#extRelationTab_4_4XXX').DataTable({
	//    destroy: true,
	//    scrollY: 600,
	//    scrollX: true,
	//    scrollCollapse: true,
	//    paging: false,
	//    fixedColumns: true,
	//    colReorder: true,
	//    searching: false,
	//    info:     false
	//} );

}


var lmbCollReplaceField = '';

function lmbSetColSetting(el, gtabid, fieldid, id, sortid, collreplace, gtabfieldid, gformid, formid) {

	if (isNaN(gformid)) {
		gformid = '';
	}
	if (isNaN(formid)) {
		formid = '';
	}

	lmbSetGlobVar('ActiveRow', fieldid);
	lmbSetGlobVar('ActiveTab', gtabid);
	lmbSetGlobVar('ID', id); // ID
	lmbSetGlobVar('sortid', sortid); // field to sort
	lmbSetGlobVar('gformid', gformid); // only relations
	lmbSetGlobVar('formid', formid); // only relations

	// show/hide coll replace menu
	if (collreplace) {
		lmbCollReplaceField = collreplace;
		$("#limbasDivRowSetting span[id='pop_menu_287']").parent().prev().css("display", "");
		$("#limbasDivRowSetting span[id='pop_menu_287']").parent().css("display", "");
	} else {
		lmbCollReplaceField = '';
		$("#limbasDivRowSetting span[id='pop_menu_287']").parent().prev().css("display", "none");
		$("#limbasDivRowSetting span[id='pop_menu_287']").parent().css("display", "none");
	}

	limbasDivShow(el, '', 'limbasDivRowSetting');
}

function lmbAjax_multilang(el, gtabid, gfieldid, ID) {
	actid = "actid=gmultilang&gtabid=" + gtabid + "&field_id=" + gfieldid + "&ID=" + ID;
	$.ajax({
		type: "GET",
		url: "main_dyns.php",
		async: false,
		data: actid,
		success: function (data) {
			$('<div>').html('<div id="lmb_multilang">' + data + '</div>').children('div').css('width', '100%').css({
				'position': 'relative',
				'left': '0',
				'top': '0',
				'width': '100%'
			}).dialog({
				appendTo: "#form1",
				resizable: true,
				modal: true,
				width: '600',
				title: jsvar['lng_2897'],
				close: function () {
					$(this).dialog('destroy').remove();
				}
			});
		}
	});
}

function fs_check_all(check) {

	var cblist = $('.fs_checkbox');
	if (check) {
		cblist.prop("checked", true);
	} else {
		cblist.prop("checked", false);
	}
}

// validate request
var confirm_submit = null;

function validate_request(type, query) {

	var result = 1;
	$.ajax({
		type: "POST",
		url: "main_dyns.php?actid=gtabValidate&function=" + type,
		data: query,
		dataType: 'json',
		async: false,
		success: function (obj) {

			if (obj.status == 'submit' || obj.status == 'true') {
				result = true;
				return;
			}

			//if(Array.isArray(wclose)){ // has listmode
			//    wclose_ = wclose.join('_');
			//}

			var confirm_ok = "<div style='float:left;width:49%'><input type='button' value='OK' class='submit lmbSbmConfirm' onclick=\"confirm_submit(); $('#lmbValidityContainer').dialog('destroy').remove();\"></div>";
			var confirm_abort = "<div style='float:right;width:49%;text-align:right'><input type='button' value='abbrechen' class='submit lmbSbmAbort' onclick=\"$('#lmbValidityContainer').dialog('destroy').remove();document.form1.use_typ.value = ''\"></div>";

			//var obj = JSON.parse(data);
			data = obj.value;

			if (obj.status == 'notice') {
				data += "<br><br><br><hr>" + confirm_ok;
			} else if (obj.status == 'alert') {
				data += "<br><br><br><hr>" + confirm_abort;
			} else if (obj.status == 'confirm') {
				data += "<br><br><br><hr>" + confirm_ok + confirm_abort;
			} else {
				result = true;
				return;
				//confirm_form(id, ajax, need, wclose, typ);
				//eval(confirm_link);
			}

			// change title
			if (obj.title) {
				for (const [key, value] of Object.entries(obj.title)) {
					$('#' + key).prop('title', value);
				}
			}
			// change class
			if (obj.class) {
				for (const [key, value] of Object.entries(obj.class)) {
					$('#' + key).addClass(value);
				}
			}

			$("<div id='lmbValidityContainer'></div>").html(data).dialog({
				title: jsvar['lng_138'],
				modal: true,
				resizable: false,
				width: 'auto',
				close: function () {
					$('#lmbValidityContainer').dialog("destroy").remove();
					document.form1.use_typ.value = '';
				}
			});

			result = false;
			return;
		}
	});

	return result;
}


// --------------- multiselect/attribute detail -----------------
function lmbAjax_multiSelect(change) {

	var i = 0;
	var e = 0;
	var fs_sel = new Array();
	var msradio = $('input[name=msrd]');

	if (change) {

		// single select
		if (msradio.attr("name")) {

			var checkedValue = msradio.filter(':checked').val();
			var msid = msradio.filter(':checked').attr("elid");
			var gtabid = document.form_fs.gtabid.value;
			var fieldid = document.form_fs.field_id.value;
			var ID = document.form_fs.ID.value;

			if (document.getElementById("g_" + gtabid + "_" + fieldid)) {
				form_name = "g_" + gtabid + "_" + fieldid;
			} else if (document.getElementById("g_" + gtabid + "_" + fieldid + "_" + ID)) {
				form_name = "g_" + gtabid + "_" + fieldid + "_" + ID;
			} else {
				return;
			}

			var sel_element = document.getElementById(form_name);
			var sel_element_ = sel_element;
			var checkedValue_ = checkedValue;
			var is_multiple = 0;

			// is select multiple
			if (document.getElementsByName(form_name + '[]')[0]) {
				sel_element_ = document.getElementsByName(form_name + '[]')[0];
				checkedValue_ = msid;
				var is_multiple = 1;
			}

			// add new select element if not exists
			if ($(sel_element_).is("select") && $(sel_element_).find('option[value="' + checkedValue_ + '"]').length == 0) {
				sel_element_.options[sel_element_.options.length] = new Option(checkedValue, checkedValue_);
			}

			// ajax based single select
			if (document.getElementById(form_name + '_ds')) {
				document.getElementById(form_name + '_ds').value = checkedValue;
				sel_element.value = checkedValue;
				// select multiple
			} else if (is_multiple) {
				sel_element_.value = msid;
			} else {
				sel_element.value = checkedValue;
			}

			if (sel_element.onchange) {
				sel_element.onchange();
			} else {
				checktyp('12', form_name, 'Anrede', fieldid, gtabid, checkedValue, ID, '');
			}

			// save changed values
			if (document.form_fs.change_id.value) {
				ajaxGet(null, 'main_dyns.php', 'multipleSelect', null, null, 'form_fs', null, 1);
			}

			$('#lmb_multiSelect').dialog('destroy').remove();

			return;

			// multiselect/attribute
		} else {

			var gse = new Array();
			var cblist = $('.fs_checkbox');
			cblist.each(function (index) {

				if ($(this).prop("checked") && $(this).attr("active")) {
					return;
				} else if (!$(this).prop("checked") && !$(this).attr("active")) {
					return;
				} else if ($(this).prop("checked") && !$(this).attr("active")) {
					var c = 1;
				} else if (!$(this).prop("checked") && $(this).attr("active")) {
					var c = 0;
				}

				if (c) {
					fs_sel[i] = 'a' + $(this).attr("elid");
				} else {
					fs_sel[i] = 'd' + $(this).attr("elid");
				}

				i++;

			});

			var gtabid = document.form_fs.gtabid.value;
			var fieldid = document.form_fs.field_id.value;
			var select_cut = document.form_fs.select_cut.value;

		}

	}

	if (fs_sel.length > 0) {
		document.form_fs.fs_sel.value = fs_sel.join(";");
	}

	ajaxGet(null, 'main_dyns.php', 'multipleSelect', null, 'lmbAjax_multiSelectPost', 'form_fs');

}

function lmbAjax_multiSelectPost(result) {
	ajaxEvalScript(result);
	document.getElementById("lmb_multiSelect").innerHTML = result;
}


// multipleselect - edit details
function lmbAjax_multibleSelect(el, gtabid, gfieldid, ID) {
	actid = "actid=multipleSelect&gtabid=" + gtabid + "&field_id=" + gfieldid + "&ID=" + ID;
	$.ajax({
		type: "GET",
		url: "main_dyns.php",
		async: false,
		data: actid,
		success: function (data) {
			$('<div id="lmb_multiSelect">' + data + '</div>').dialog({
				resizable: true,
				modal: true,
				width: '480',
				title: el.title,
				close: function () {
					// refresh tablelist for gtab_erg
					if (document.form1.action.value == 'gtab_erg') {
						send_form(1, 2);
					} else {
						// reload LmExt_RelationFields
						var extrelid = $(el).parents("[id^='extRelationFieldsTab']").attr("id");
						if (extrelid) {
							extrelid = extrelid.split('_');
							document.getElementById("extRelationFieldsReload_" + extrelid[1] + "_" + extrelid[2]).click();
						}
					}

					$(this).dialog('destroy').remove();
				}
			});
		}
	});
}

//g, v, e
/* --- select / multiselect / attribute - actions ----------------------------------- */
function dyns_11_a(evt, el_value, el_id, form_name, fieldid, gtabid, ID, dash, attribute, action, level, form_elid) {

	// contextmenu
	var contextel = form_name + '_dsl';
	if (!level) {
		level = '';
	}

	if (el_id) {
		// load multiple select
		if (action === "e") {
			oMultipleSelectLoader.load(evt, el_value, el_id, form_name, fieldid, gtabid, ID, dash, attribute, action, level);
			activ_menu = 1;
			return false;
		// select
		} else if (action === "b") {
			dynsClose(contextel);
			$('#' + form_name + '_ds').val(el_value);
			$('#' + form_name).val(el_value);
            checktyp('32', '', form_name, fieldid, gtabid, ' ', ID);
		// attribute modify
		} else if (action === "v") {
			document.form1[form_name].value += ";v";
		// multiple select ajax - unique
		} else if (action === "f") {
			dynsClose(contextel);
			$('#' + form_name + '_ds').val(el_value);
			$('#' + form_name).val("a" + el_id);
            checktyp('32', '', form_name, fieldid, gtabid, ' ', ID);
		// ajax based - add / delete select_w / delete select_d  (action = a, c, g)
		} else {
			var el = evt.currentTarget;
			var is_active = $(el).attr('is_active');
			if (is_active) {
				// delete select_w value
				var sep = 'd';
				// delete select_d value
				if (action === 'g') {
					sep = 'e';
				}
				$(el).find("td:first").html("");
				$(el).attr('is_active', '')
				document.form1[form_name].value = sep + el_id;
			} else {
				$(el).find("td:first").html("<i class='lmb-icon lmb-check'></i>");
				$(el).attr('is_active', '1')
				document.form1[form_name].value = "a" + el_id;
			}

			if (!evt.ctrlKey) {
				dynsClose(contextel);
			}

			confirm_submit = function () {
				checktyp('32', '', form_name, fieldid, gtabid, ' ', ID);
				ajaxGet(null, 'main_dyns.php', '11&gtabid=' + gtabid + '&fieldid=' + fieldid + '&form_elid=' + form_elid + '&ID=' + ID, null, function (result) {
					$('#' + form_name + '_dse').html(result).show();
					if (typeof tagAttr_initSize === 'function') {
						tagAttr_initSize();
					}
				}, 'form1');
				document.form1[form_name].value = '';
				//checktyp('32','',form_name,fieldid,gtabid,' ',ID);  // todo !!??
			}

			// validate
			if (jsvar['gtab_validate_' + document.form1.gtabid.value]) {
				var query = ajaxFormToURL('form1', 'gtabValidate');
				if (validate_request('update', query)) {
					confirm_submit();
				}
			} else {
				confirm_submit();
			}

			// attribute modify
			if (action === 'a' || action === 'g') {
				document.form1[form_name].value += ";v";
			}

		}
	}
}


/* --- dyns_11_a delete ----------------------------------- */
function dyns_11_a_delete(evt, el, ID, gtabid, fieldid, w_id, d_id, form_name, select_cut) {

	// e = delete with select_d ID
	// d = delete with select_w ID

	var sep = 'd';
	if (d_id) {
		var w_id = d_id;
		sep = 'e';
	}

	var val = document.form1[form_name].value;
	var color, textDecoration;
	var d = val.indexOf(sep);
	var ids = val.split(";");
	var elIndex = ids.indexOf(sep + w_id);

	// first empty field
	if (d == -1) {
		document.form1[form_name].value = '';
		ids = new Array();
	}

	if (elIndex < 0) {
		// element excluded -> include
		ids.push(sep + w_id);
		color = 'red';
		textDecoration = 'line-through';
	} else {
		// element included -> remove
		ids.splice(elIndex, 1);
		color = '';
		textDecoration = '';
	}

	document.form1[form_name].value = ids.join(";");
	$(el).css('color', color);
	$(el).closest('td').next('td').css('text-decoration', textDecoration);
	$(el).next().css('text-decoration', textDecoration);

	checktyp('32', '', form_name, fieldid, gtabid, ' ', ID);

}


function value_in(search_in, search_on) {
	var y = 0;
	var tmp = search_in.split(";");
	if (tmp && tmp.length > 0) {
		for (y = 0; y < tmp.length; y++) {
			if (search_on == tmp[y]) return true;
		}
	}
	return false;
}

// render single form Element
function lmb_formRenderElement(el, ID, gtabid, gformid, elid, line) {
	actid = "actid=formRenderElement&gtabid=" + gtabid + "&gformid=" + gformid + "&ID=" + ID + "&elid=" + elid + "&line=" + line;
	//if(typeof el != 'object'){el = $('#'+el);}

	el = $('#' + el);
	$.ajax({
		type: "GET",
		url: "main_dyns.php",
		async: false,
		data: actid,
		success: function (data) {
			el.replaceWith(data)
			ajaxEvalScript(data);
		}
	});
}


/*---------------- Tabulator-Element für Formular anzeigen ---------------------*/
var lmbTabulatorKey = new Array();

function lmbSwitchTabulator(el, mainel, elkey, vert) {

	// show subtitle in header
	parent.$('#lmb-main-subtitle').text(' - ' + $(el).text());

	if (vert) {
		var hel = el.parentNode.parentNode.parentNode;
		var stpos = vert;
	} else {
		var hel = el.parentNode;
		var stpos = "";
	}

	if (!hel) {
		return;
	}
	for (var i = 0; i < hel.childNodes.length; i++) {
		if (vert) {
			divel = hel.childNodes[i].firstChild.firstChild;
		} else {
			divel = hel.childNodes[i];
		}

		var mt = "LmbTabulatorHeader";
		if (!divel || !divel.id || divel.id.substr(0, mt.length) != mt) continue;
		var dsp = 0;
		var cl = "lmbGtabTabulator" + stpos + "Inactive";
		var nodel = divel.id.replace(mt, "LmbTabulatorItem");
		if (divel.id == el.id) {
			dsp = 1;
			cl = "lmbGtabTabulator" + stpos + "Active";
		}
		divel.className = cl;
		if (dsp) {
			// $('[id^='+nodel+']').show(); // ??
			$('#' + nodel).show();
		} else {
			$('#' + nodel).hide();
		}
	}

	var tabKey = new Array();
	lmbTabulatorKey[mainel] = elkey;
	var e = 0;
	for (var i in lmbTabulatorKey) {
		tabKey[e] = i + "_" + lmbTabulatorKey[i];
		e++;
	}
	document.form1.filter_tabulatorKey.value = tabKey.join(";");
	lmb_initTables();
}


// <div id="lmbAjaxContainer2" class="ajax_container" style="position:absolute;display:none;z-index:999;" onclick="activ_menu=1;"></div>


// Ajax extended relation for tablelist
var lmbRelationDialog = 0;

function LmExt_RelationList(el, gtabid, gfieldid, ID) {

	pos = (50 * lmbRelationDialog);

	lmbRelationDialog++;

	actid = "actid=extRelationList&gtabid=" + gtabid + "&gfieldid=" + gfieldid + "&ID=" + ID;
	$.ajax({
		type: "GET",
		url: "main_dyns.php",
		async: false,
		data: actid,
		success: function (data) {
			//document.getElementById("lmbAjaxContainer2").innerHTML = data;
			//$("#lmbAjaxContainer2 div").css('width', '100%');
			$('<div>').html(data).children('div').css('width', '100%').css({
				'position': 'relative',
				'left': '0',
				'top': '0',
				'width': '100%'
			}).dialog({
				appendTo: "#form1",
				resizable: true,
				modal: true,
				width: '500',
				title: el.title,
				position: {
					at: "center+" + pos + " center+" + pos,
					of: window,
				},
				close: function (event, ui, parentel) {

					//alert(parentel);
					var destel = $(el).parentsUntil("table").parent();
					if (destel) {
						var destelid = destel.attr('id').split('_');
						if (destelid[0] == 'extRelationTab') {
							var datid = destel.attr('datid');
							var form_id = '';
							if (form1.form_id) {
								form_id = form1.form_id.value;
							}
							// refresh relation with post
							LmExt_RelationFields(null, destelid[1], destelid[2], '', 1, datid, '', '', '', '', form_id, '', 1);
							var isclosed = 1;
						}
					}

					// refresh tablelist for gtab_erg
					if (!isclosed) {
						send_form(1, 2);
					}

					$(this).dialog('destroy').remove();
					lmbRelationDialog--;
				}
			});
		}
	});

}


// Ajax extended relation for tablelist
//function LmExt_RelationListxx(el,gtabid,gfieldid,ID){

//	var actid = "extRelationList&gtabid=" + gtabid + "&gfieldid=" + gfieldid + "&ID="+ID;
//	dynfunc = function(result){LmExt_RelationListPost(el,result,gtabid,gfieldid,ID);}
//	ajaxGet(null,'main_dyns.php',actid,null,"dynfunc");

//}

// Ajax extended relation for tablelist output
//function LmExt_RelationListPostxx(el,result,gtabid,gfieldid,ID){

//	document.getElementById("lmbAjaxContainer").innerHTML = result;

//	limbasDivShow(el,null,'lmbAjaxContainer','','',1);

//}

/*----------------- Zeige Liste anzuzeigender Verknüpfungsfelder -------------------*/
function lmb_relShowField(event, el, elname) {

	$($('#' + elname).detach()).appendTo('body');
	limbasDivShow(el, null, elname, '', '', 1);

	// put ajax container in front of jquery ui
	var ui = $('.ui-front:visible');
	if (ui) {
		var uiZIndex = parseInt(ui.css("z-index"));
		$("#lmbAjaxContainer").css("z-index", uiZIndex + 1);
	}
}

//----------------- neuen Verkn. Datensatz anlegen -------------------
function create_new_verkn(evt, gtabid, vfieldid, vgtabid, ID, tablename, fdimsn, destformid, formid, inframe, layername) {
	if (jsvar["confirm_level"] > 0) {
		neu = 1;
	} else {
		neu = confirm(jsvar["lng_164"] + ':' + tablename + '\n' + jsvar["lng_24"]);
	}
	if (neu) {
		divclose();

		// simple table without relation
		if (!gtabid) {
			gtabid = vgtabid;
			vgtabid = '';
			ID = '';
			vfieldid = '';
		}

		lmbOpenForm(evt, gtabid, ID, {
			formid: destformid,
			action: 'gtab_neu',
			v_tabid: vgtabid,
			v_fieldid: vfieldid,
			v_id: ID,
			v_formid: formid,
			formdimension: fdimsn,
			formopenas: inframe
		});
	}
}

var oMultipleSelectLoader = new MultipleSelectShow("oMultipleSelectLoader");

function MultipleSelectShow(instance_name) {
	this.cache = {};

	this.load = function (evt, el_value, el_id, form_name, fieldid, gtabid, ID, dash, attribute, action, level) {
		var id = form_name + '_' + level + '_' + el_id;
		var parent = $('#' + id);

		// already data displayed -> remove
		if (parent.next().is(':not(.lmbSelectLink)')) {
			parent.next().remove();
			return;
		}

		// add to cache
		if (id in this.cache) {
			parent.after('<tr><td style="width:16px;"></td><td colspan="2">' + this.cache[id] + '</td></tr>');
			return;
		}

		var self = this;
		ajaxGet(null, "main_dyns.php", "11_a_level&page=" + dash + "&gtabid=" + gtabid + "&fieldid=" + fieldid + "&ID=" + ID + "&level=" + el_id + "&form_name=" + form_name + "&form_value=" + el_value, null, function (response) {
			var tmp = response.split("#L#");
			var html = tmp[1];

			parent.after('<tr><td style="width:16px;"></td><td colspan="2">' + html + '</td></tr>');
			self.cache[id] = html;
		});
	};

}

function lmb_updateField(field_id, tab_id, id, changevalue, syntaxcheck, fielddesc, ajaxpost, fieldsize, el, parentage, language) {
	checktyp(syntaxcheck, 'g_' + tab_id + '_' + field_id, fielddesc, field_id, tab_id, changevalue, id, ajaxpost, fieldsize, el, parentage, language);
}

/**
 * Called on change of an input/select. Shows error message is the input's value is not in the correct format
 * @param data_type int limbas datatype
 * @param fieldname string name of the input/select html element
 * @param fielddesc string readable description of the field to show in error messages. Defaults to fieldname
 * @param field_id int limbas field id, optional
 * @param tab_id int limbas table id, optional
 * @param obj string the new value (often this.value)
 * @param id int limbas dataset id, optional
 * @param ajaxpost int 1 to update immediately via ajax
 * @param fieldsize int the max length of the fields datatype, optional
 * @param el html element
 * @param parentage string parentage, optional
 */
function checktyp(data_type, fieldname, fielddesc, field_id, tab_id, obj, id, ajaxpost, fieldsize, el, parentage, language) {
	if (!fielddesc) {
		fielddesc = fieldname;
	}
	if (!parentage) {
		parentage = '';
	}
	if (!language) {
		language = '';
	}

	// no syntax check
	if (!data_type && tab_id && field_id) {
		document.form1.history_fields.value = document.form1.history_fields.value + tab_id + ',' + field_id + ',' + id + ';';
		var isupdate = 1;
	}

	if (!data_type) {
		obj = '';
	}
	let val = obj;

	var regexp = RULE[data_type];
	if (fieldname.substr(0, 2) === 'gs') {
		val = obj.replace(/^(>|<|<=|>=|!=|#NOTNULL#|#NULL#|#NOT#)?( )?/g, '');
	}

	if (fieldsize) {
		regexp = regexp.replace(/xxx/g, (parseInt(fieldsize) + 1));
		regexp = regexp.replace(/xx/g, fieldsize);
	} else {
		regexp = regexp.replace(/xxx/g, '');
		regexp = regexp.replace(/xx/g, '');
	}

	if (el && el.getAttribute("data-regex")) {
		regexp = el.getAttribute("data-regex");
	}

	const reg = new RegExp(regexp);
	if (obj != '') {
		if (val && !reg.exec(val)) {
			newvalue = prompt(jsvar['lng_134'] + '\n' + fielddesc + ': ' + DATA_TYPE_EXP[data_type] + ' (' + FORMAT[data_type] + ')', obj);
			if (!newvalue) {
				limbasDuplicateForm(fieldname, '');
				//eval("document.form1.elements['"+fieldname+"'].value='';");
				document.getElementsByName(fieldname)[0].value = newvalue;
				return;
			}
			if (reg.exec(newvalue)) {
				val = newvalue;
				document.getElementsByName(fieldname)[0].value = newvalue;
				//eval("document.form1.elements['"+fieldname+"'].value=newvalue;");
				if (field_id && tab_id) {
					if (id) {
						document.form1.history_fields.value = document.form1.history_fields.value + tab_id + ',' + field_id + ',' + id + ',' + parentage + ',' + language + ';';
						var isupdate = 1;
					} else {
						document.form1.history_search.value = document.form1.history_search.value + tab_id + ',' + field_id + ';';
						var isupdate = 1;
					}
				}
			} else {
				checktyp(data_type, fieldname, fielddesc, field_id, tab_id, newvalue, id, ajaxpost, fieldsize);
			}
		} else {
			if (field_id && tab_id) {
				if (id) {
					document.form1.history_fields.value = document.form1.history_fields.value + tab_id + ',' + field_id + ',' + id + ',' + parentage + ',' + language + ';';
					var isupdate = 1;
				} else {
					document.form1.history_search.value = document.form1.history_search.value + tab_id + ',' + field_id + ';';
				}
			}
		}
	} else {
		if (field_id && tab_id) {
			if (id) {
				document.form1.history_fields.value = document.form1.history_fields.value + tab_id + ',' + field_id + ',' + id + ',' + parentage + ',' + language + ';';
				var isupdate = 1;
			} else {
				document.form1.history_search.value = document.form1.history_search.value + tab_id + ',' + field_id + ';';
				var isupdate = 1;
			}
		}
	}

	if (!isupdate) {
		return;
	}

	limbasDuplicateForm(el, fieldname, val, obj);

	if (ajaxpost != 2 && id > 0 && data_type != 13 && (ajaxpost == 1 || jsvar["ajaxpost"] == 1)) {
		send_form(1, 1);
		document.form1.history_fields.value = '';  //???? 0 war vorher; '' ist aber richtig
		return;
	}

	// submit marker
	$('.submit').addClass("lmbSbmConfirm"); // legacy formular
	$('.submit').removeClass("btn-outline-secondary");
	$('.submit').addClass("btn-primary");

}


function limbasDuplicateForm(el, name, val) {
	// Multicheckbox
	if (el && el.name) {
		var ellist = document.getElementsByName(el.name);
		// other Elements
	} else {
		var ellist = document.getElementsByName(name);
	}

	if (ellist.length > 1) {
		for (var i = 0; i < ellist.length; i++) {
			var obj = ellist.item(i);
			if (obj == el) {
				continue;
			}
			if (obj.type == 'checkbox') {
				if (val) {
					obj.checked = true;
				} else {
					obj.checked = false;
				}
			} else if (obj.type == 'radio') {
				if (obj.name + '_' + obj.getAttribute("data-index") == el.id) {
					obj.value = val;
				}
			} else {
				obj.value = val;
			}
		}
	}
}

function needfield(formid) {
	var need = new Array();
	var radel = new Array();

	function setbg(el) {
		el.addClass("gtabBodyDetailNeedVal");
	}

	if (typeof formid === 'undefined') {
		formid = null;
	}

	if (!formid) {
		formid = 'form1';
	}

	$('#' + formid + ' input, #' + formid + ' select, #' + formid + ' textarea').each(function () {

		if ($(this).attr('title') && $(this).attr('title').substr(0, 1) == '*' && $(this).attr('id') && ($(this).attr('id').substring(0, 2) == 'g_' || $(this).attr('id').substring(0, 7) == 'element')) {

			if ($(this).attr('type') && $(this).attr('type').toLowerCase() == 'hidden') {
				return true;
			}

			nval = '';
			nname = '';
			ntype = '';
			npos = 0;
			nspell = 'formelement ' + $(this).attr('id');

			titleval = $(this).attr('title');
			if ($(this).attr('name')) {
				nname = $(this).attr('name');
			}
			ntype = $(this).prop("tagName").toLowerCase();
			if ($(this).attr('type')) {
				ntype = $(this).attr('type').toLowerCase();
			}
			if ($(this).val()) {
				nval = $(this).val();
			}
			cc = $(this)[0];

			$(this).removeClass("gtabBodyDetailNeedVal");

			if (titleval) {
				npos = titleval.indexOf(':');
				if (npos > 0) {
					nspell = titleval.substring(1, npos) + ' ';
				} else {
					nspell = titleval.substring(1) + ' ';
				}
			}

			// ajax relation & select
			if (cc.name.substr(cc.name.length - 3, 3) == '_ds') {
				nval = document.getElementById(cc.name.substr(0, cc.name.length - 3)).value;
				if (nval.substr(0, 3) == '#L#') {
					nval = '';
				}
			}

			// inherit select
			if (cc.name.substr(cc.name.length - 3, 3) == '_di') {
				nval = cc.value;
			}

			if ((ntype == 'text' || ntype == 'textarea') && !nval) {
				need[need.length] = nspell;
				setbg($(this));
			} else if (ntype == 'file' && !document.form1['uploaded_' + nname]) {
				need[need.length] = nspell;
				setbg($(this));
			} else if (ntype == 'select' && cc.selectedIndex <= 0 && (nval == '' || nval == '#L#delete')) {
				need[need.length] = nspell;
				setbg($(this));
			} else if (ntype == 'radio' || ntype == 'checkbox') {
				spos = nname.indexOf('[');
				if (spos > 1) {
					nname = nname.substring(0, spos);
				}
				if (radel[nname] != 0) {
					if (cc.checked) {
						radel[nname] = 0;
						setbg($(this));
					} else {
						radel[nname] = nspell;
					}
				}
			}

		}
	});

	// Checkboxen und Radiofelder
	for (var z in radel) {
		if (radel[z]) {
			need[need.length] = radel[z];
		}
	}

	if (need.length > 0) {
		need = array_unique(need);
		need = need.join("\n");
		return need;
	}
}

// change field-required flag
function lmb_needfieldChange(formid, status) {

	var el = document.getElementById(formid);
	if (!el) {
		return;
	}

	var eltitle = el.title;
	if (eltitle) {
		var need = eltitle.substr(0, 1);
	} else {
		eltitle = 'input form';
	}

	if (status && need == '*') {
		return true;
	} else if (status && need != '*') {
		el.title = '*' + eltitle
	} else if (!status && need == '*') {
		el.title = eltitle.substr(1);
	} else if (!status && need != '*') {
		return true;
	}
}


/**
 * report menu options
 * @param preview load classic print menu or open preview
 * @param evt js event
 * @param el the button that was clicked
 * @param gtabid limbas table id
 * @param reportid limbas report id
 * @param ID limbas dataset id
 * @param output int (1=preview, 2=archive, 4=print)
 * @param listmode bool whether the report is a report for the whole table
 * @param report_medium string 'xml'/'pdf'/'tcpdf'/...
 * @param report_rename string the file name
 * @param printerId int printer id, used when output=4
 * @param resolvedTemplateGroups object see TemplateConfig::$resolvedTemplateGroups
 * @param resolvedDynamicData object see TemplateConfig::$resolvedDynamicData
 *
 * @param saveAsTemplate if not empty, the selected configuration will be saved as template with this parameter as name
 * @param callback function callback for receiving the report generation html
 */
function limbasReportMenuHandler(preview, evt, el, gtabid, reportid, ID, output, listmode, report_medium, report_rename, printerId, resolvedTemplateGroups = {}, resolvedDynamicData = {}, saveAsTemplate = '', callback = null) {
	console.warn('Deprecated function limbasReportMenuHandler called.');
	directlyOpenReportPreview(gtabid, reportid, ID, resolvedTemplateGroups);
}

function directlyOpenReportPreview(gtabid, reportId, id, resolvedTemplateGroups = {}) {
	let $templateSelect = $('#lmbTemplateSelect');

	if ($templateSelect.length <= 0) {
		$templateSelect = $('<div id="lmbTemplateSelect" data-type="report"></div>');
		$('body').append($templateSelect);
	}

	if (resolvedTemplateGroups === null) {
		resolvedTemplateGroups = {};
	}

	$templateSelect.data('type', 'report');
	selectTemplate('-', reportId, gtabid, resolvedTemplateGroups, id);
}

const reportMailModalFooter = '<div class="d-flex w-100 justify-content-between"><button type="button" class="btn btn-outline-secondary btn-select-template">Template auswählen</button><button type="button" class="btn btn-dark" data-bs-dismiss="modal">Schließen</button></div>';

function limbasReportShowPreview(url, report_name) {
	const $modal = showFullPageModal('Vorschau: ' + report_name, '<iframe src="' + url + '" class="w-100 h-100"></iframe>', 'fullscreen',reportMailModalFooter);
	if ($modal === null) {
		open(url);
		return;
	}

	$modal.find('.btn-select-template').on('click', function () {
		$modal.modal('hide');
		showReportModal();
	});
}

function lmbShowMailForm(gtabid,id,firstCall = false, appendData = {}) {
	lmbShowTemplateModal(gtabid, id, 'mail', firstCall, appendData);
}

function lmbOpenMailForm(url) {
	const $modal = showFullPageModal('E-Mail verfassen', '<iframe src="' + url + '" class="w-100 h-100"></iframe>', 'fullscreen', reportMailModalFooter);
	if ($modal === null) {
		open(url);
		return;
	}
	
	$modal.find('.btn-select-template').on('click', function () {
		$modal.modal('hide');
		showMailModal();
	});
}


/**
 * [{name: 'name', value: 'value'}, {name: 'name2', value: 'value2'}] -> {name: value, name2: value2}
 * used to convert jQuery::serializeArray to a usable format
 * @param arr
 * @returns {{}}
 */
function keyValueArrToObj(arr) {
	if (!arr || !Array.isArray(arr)) {
		return {};
	}

	const obj = {};
	for (const entry of arr) {
		obj[entry.name] = entry.value;
	}
	return obj;
}

function limbasReportSaveTemlpate(evt, gtabid, reportid, resolvedTemplateGroups = {}) {

	//TODO: language
	let name = prompt('Template Name', '');

	if (name != null) {
		limbasReportMenuHandler(0, evt, null, gtabid, reportid, 0, 0, 0, 'pdf', '', 0, resolvedTemplateGroups, {}, name, function (result) {
			lmbShowSuccessMsg('Template gespeichert.');
		});
	}

}

/**
 * Ajax reminder
 * @deprecated use limbasReminder instead
 *
 * @param evt unused
 * @param el unused
 * @param add
 * @param remove
 * @param changeView
 * @param change
 * @param defaults
 * @param gtabid
 * @param ID
 */
function limbasDivShowReminder(evt, el, add, remove, changeView, change, defaults, gtabid, ID) {
	if (!changeView) {
		changeView = 0;
	}

	limbasReminder(changeView);
}

/**
 * Shows the reminder modal, either for a new reminder (if reminderId is 0 / null) or for an existing one
 * @param reminderId
 */
function limbasReminder(reminderId = 0) {
	const tableId = jsvar['gtabid'];
	const dataId = jsvar['ID'];

	let listmode = 0;
	if (document.form1.action.value == 'gtab_erg') {
		listmode = 1;
	}

	if (!reminderId) {
		reminderId = 0;
	}

	const data = {
		'tableId': tableId,
		'dataId': dataId,
		'reminderId': reminderId,
		'listmode': listmode,
		'action': 'show',
	}

	sendReminderAction(data).then(result => limbasDivShowReminderPost(result, tableId));
}

function sendReminderAction(data) {
	data['actid'] = 'handleReminder';

	return new Promise((resolve, reject) => {
		$.ajax({
			url: 'main_dyns.php',
			type: 'POST',
			data: data,
			dataType: 'json',
			success: function (data) {
				resolve(data)
			},
			error: function (error) {
				reject(error)
			}
		});
	})
}

// reminder post / inside the modal
function limbasDivShowReminderPost(result, tableId) {
	if (!result || result['success'] === false) {
		return;
	}

	let footer = result['footer'];
	const $body = $(result['body']);

	let dataId = result['dataId'];

	const $footer = $(footer);

	$selectReminderUnit = $body.find('#select-reminder-unit');
	$inputReminderDeadlineFromNow = $body.find('#input-reminder-deadline-from-now');
	$inputReminderDeadline = $body.find('#input-reminder-deadline');

	let updateReminderDeadline = function () {
		const unit = $selectReminderUnit.val();
		let fromNow = parseInt($inputReminderDeadlineFromNow.val());
		if (!fromNow) {
			fromNow = 0;
		}
		const currentDate = new Date();

		switch (unit) {
			case 'min':
				currentDate.setMinutes(currentDate.getMinutes() + fromNow);
				break;
			case 'hour':
				currentDate.setHours(currentDate.getHours() + fromNow);
				break;
			case 'day':
				currentDate.setDate(currentDate.getDate() + fromNow);
				break;
			case 'week':
				currentDate.setDate(currentDate.getDate() + fromNow * 7);
				break;
			case 'month':
				currentDate.setMonth(currentDate.getMonth() + fromNow);
				break;
			case 'year':
				currentDate.setFullYear(currentDate.getFullYear() + fromNow);
				break;
			default:
				return;
		}

		const year = currentDate.getFullYear();
		const month = String(currentDate.getMonth() + 1).padStart(2, '0');
		const day = String(currentDate.getDate()).padStart(2, '0');
		const hours = String(currentDate.getHours()).padStart(2, '0');
		const minutes = String(currentDate.getMinutes()).padStart(2, '0');

		const formattedDate = `${year}-${month}-${day}T${hours}:${minutes}`;
		$inputReminderDeadline.val(formattedDate);
	}

	const updateMultiframe = function (multiframeUpdateId, category) {
		top.limbasMultiframePreview(multiframeUpdateId, 'Reminder', 0, 0, tableId, 'category=' + category);
	}

	$body.find('#select-reminder-unit, #input-reminder-deadline-from-now').on('change', updateReminderDeadline);

	const $modal = showFullPageModal('Reminder', $body, 'lg', $footer);

	$modal.find('#btn-reminder-createmodal').on('click', function () {
		limbasReminder();
	});

	const $btnSave = $modal.find('#btn-save-reminder');
	const reminderId = $btnSave.data('reminderid');

	const select2Options = {
		multiple: true,
		dropdownParent: $modal.find('.modal-content')
	};

	$btnSave.on('click', function () {
		let $categoryInput = $body.find('input[name="reminder_category"]:checked');
		if (!$categoryInput.length) {
			$categoryInput = $body.find('#input-reminder-category-hidden');
		}
		let category = $categoryInput.val();

		let selectedUsers = $body.find('#select-reminder-user').val();
		let selectedGroups = $body.find('#select-reminder-group').val();

		const userArray = selectedUsers.map(userId => ({type: 'user', id: userId.toString()}));
		const groupArray = selectedGroups.map(groupId => ({type: 'group', id: groupId.toString()}));

		let userOrGroupArray = userArray.concat(groupArray);

		let listmode = 0;
		if (document.form1.action.value == 'gtab_erg') {
			listmode = 1;
		}

		// listmode
		let selectedRowsCount = 0;
		let reminderRows = '';
		let verkn = {};

		if (listmode) {
			selectedRowsCount = countofActiveRows();
			// use selected rows
			if (selectedRowsCount > 0) {
				let activeRows = checkActiveRows(tableId);
				if (activeRows.length > 0) {
					reminderRows = activeRows;
				} else {
					alert(jsvar["lng_2083"]);
					return;
				}
				// use filter
			} else {
				reminderRows = 'all';
				if (document.form1.verkn_ID) {
					verkn = {
						verkn_tabid: document.form1.verkn_tabid.value,
						verkn_fieldid: document.form1.verkn_fieldid.value,
						verkn_ID: document.form1.verkn_ID.value,
						verkn_showonly: document.form1.verkn_showonly.value
					};
				}
				// get count from result
				if (document.getElementById("GtabResCount")) {
					selectedRowsCount = document.getElementById("GtabResCount").innerHTML;
				} else {
					selectedRowsCount = 'undefined'
				}
			}

			if (selectedRowsCount && !confirm(jsvar['lng_2676'] + ' ' + selectedRowsCount + '\n' + jsvar['lng_2902'])) {
				activ_menu = 0;
				limbasDivClose();
				return;
			}
		}

		let form_id = '';
		if (document.form1.form_id.value) {
			form_id = document.form1.form_id.value;
		}

		let data = {
			'action': 'save',
			'reminderId': reminderId,
			'tableId': tableId,
			'dataId': dataId,
			'category': category,
			'deadline': $body.find('#input-reminder-deadline').val(),
			'description': $body.find('#input-reminder-description').val(),
			'userOrGroupArray': userOrGroupArray,
			'reminderRows': reminderRows,
			'form_id': form_id,
		};

		data = {...data, ...verkn};

		sendReminderAction(data).then(r => {
			lmbShowSuccessMsg("Reminder saved");
			updateMultiframe(r['multiframeUpdateId'], category);
		}).catch(e => {
			lmbShowErrorMsg("Error saving reminder");
		}).finally(() => {
			$modal.modal('hide');
		});
	});

	$body.find('[data-reminder-changeview]').on('click', function () {
		limbasReminder($(this).data('reminder-changeview'));
	});

	$body.find('.toggle-breakable').on('click', function () {
		const $card = $(this).closest('.card');
		$card.find('.breakable').find('br').toggleClass('d-none');
	});

	$body.find('[data-reminder-remove]').on('click', function () {
		const reminderId = $(this).data('reminder-remove');
		sendReminderAction({
			'action': 'delete',
			'reminderId': reminderId,
		}).then(r => {
			lmbShowSuccessMsg("Reminder deleted");
			const $li = $(this).closest('li');
			const $card = $li.closest('.card');
			const $separator = $body.find('#div-existing-reminders-separator');
			const $list = $body.find('#div-existing-reminders-list');

			$li.remove();
			if (!$card.find('li').length) {
				$card.remove();
			}
			if (!$list.find('.card').length) {
				$list.remove();
				$separator.remove();
			}
			updateMultiframe(r['multiframeUpdateId'], r['category']);
		}).catch(e => {
			lmbShowErrorMsg("Error deleting reminder");
		}).finally(() => {

		});
	});

	$body.find('.breakable').find('br').toggleClass('d-none');
	$body.find('#select-reminder-user').select2(select2Options);
	$body.find('#select-reminder-group').select2(select2Options);
}

// timeout for same form requests
function clear_send_form() {
	sendFormTimeout = 0;
}

// Ajax Feldverärbung
function limbasSearchInherit(desttabid, destgfieldid, gtabid, gfieldid, el, ID, showall) {
	if (el.value.length > 1 || el.value == '*') {
		url = "main_dyns.php";
		actid = "searchInherit&gtabid=" + gtabid + "&gfieldid=" + gfieldid + "&dest_gtabid=" + desttabid + "&dest_gfieldid=" + destgfieldid + "&showall=" + showall + "&ID=" + ID + "&value=" + el.value;
		mainfunc = function (result) {
			limbasSearchInheritPost(result, el);
		};
		ajaxGetWait(null, url, actid, null, "mainfunc", '', 500);
	}
}

function limbasSearchInheritPost(result, el) {

	if (result) {
		limbasDivShow(el, null, 'lmbAjaxContainer', '', '', 1);
		element = document.getElementById("lmbAjaxContainer");
		element.innerHTML = result;
	}

	activ_menu = 0;
}

function limbasInheritFrom(evt, sourceGtabid, sourceId, destGtabid, destGfieldid, destId, parentage) {
	limbasDivClose("");
	url = "main_dyns.php";
	actid = "inheritFrom&gtabid=" + destGtabid + "&dest_gfieldid=" + destGfieldid + "&source_id=" + sourceId + "&dest_id=" + destId;
	mainfunc = function (result) {
		limbasInheritFromPost(result, evt, parentage);
	};
	ajaxGet(null, url, actid, null, "mainfunc");
}

function limbasInheritFromPost(json, evt, parentage) {
	if (json != "false") {
		var data = eval('(' + json + ')');
		var ajaxpost = 0;
		for (var i in data['destId']) {
			var el = null;


			if (document.getElementsByName(data['destFormname'][i])[0]) {
				var el = document.getElementsByName(data['destFormname'][i])[0];
			} else if (document.getElementsByName(data['destFormname'][i] + '_' + data['destId'][i])[0]) {
				var el = document.getElementsByName(data['destFormname'][i] + '_' + data['destId'][i])[0];
			} else if (document.getElementsByName(data['destFormname'][i] + '[]')[0]) {
				var el = document.getElementsByName(data['destFormname'][i] + '[]')[0];
			} else {
				// if no formelement present
				$("#form1").append("<input type='hidden' name='" + data['destFormname'][i] + "'>");
				var el = document.getElementsByName(data['destFormname'][i])[0];
			}


			if (el) {
				// if no formelement or readonly
				var ttype = el.nodeName.toLowerCase();
				var ty = null;

				// check for tiny
				if (typeof (tinymce) == 'object' && data['destFormname'][i]) {
					ty = tinymce.get(data['destFormname'][i]);
				}

				// is tiny
				if (ty) {
					ty.setContent(data['resultval'][i]);
				} else if (ttype != 'input' && ttype != 'textarea' && ttype != 'select') {
					el.innerHTML = data['resultval'][i];
					$(el).attr('name', '');
					$("#form1").append("<input type='hidden' name='" + data['destFormname'][i] + "'>");
					var el = document.getElementsByName(data['destFormname'][i])[0];
				}

				if (data['destFieldtype'][i] == 3 && evt.shiftKey) {
					el.value += '\n' + data['resultval'][i];
				} else {
					el.value = data['resultval'][i];
				}
				if (parentage && !data['destId'][i]) {
					data['destId'][i] = 0;
				}
				document.form1.history_fields.value += data['destGtabid'][i] + ',' + data['destFieldid'][i] + ',' + data['destId'][i] + ',' + parentage + ';';

				// check for same formelements
				limbasDuplicateForm(data['destFormname'][i], data['resultval'][i]);
				if (data['ajaxpost'][i]) {
					ajaxpost = 1;
				}
			}
		}
		if (ajaxpost) {
			send_form(1, 1);
			document.form1.history_fields.value = '';
		}
	}
}

var dyns_time = null;
var dyns_el = null;

// quicksearch
function lmbQuickSearch(evt, el, gtabid, fieldid) {
	dyns_el = el;
	actid = "gtabQuickSearch&gtabid=" + gtabid + "&fieldid=" + fieldid + "&value=" + el.value;

	if (el.value.length > 1) {
		if (dyns_time) {
			window.clearTimeout(dyns_time);
		}
		dyns_time = window.setTimeout(function () {
			ajaxGet(null, 'main_dyns.php', actid, null, 'lmbQuickSearchPost');
		}, 1000);
	}
}

function lmbQuickSearchPost(result) {
	document.getElementById("limbasAjaxGtabContainer").innerHTML = result;
	limbasDivShow(dyns_el, '', 'limbasAjaxGtabContainer');
	activ_menu = 0;
	validEnter = 1;
}

function lmbQuickSearchAction(evt, gtabid, fieldid, id, val) {
	limbasDivHide(null, 'limbasAjaxGtabContainer');
	inpel = "tdinp_" + gtabid + "_" + fieldid;
	if (!evt.shiftKey && typeof window.lmbOpenForm == 'function') {
		lmbOpenForm(evt, gtabid, id);
		//view_detail(1,id);
	} else {
		document.getElementById(inpel).value = val;
	}
	activ_menu = 0;
}

// --- Editmenüsteuerung -----------------------------------
function lmbTableContextMenu(evt, el, ID, gtabid, custmenu, parentid) {

	// --------- deactivate all rows -------------
	//aktivateRows(0);
	// --------- activate single row -------------
	// aktivateSingleRow('elrow_'+ID+'_'+TABID,1);
	// activate row if not active

	child = 'limbasDivMenuContext';
	parent = evt;

	// use data attribute
	if (!custmenu) {
		var custmenu = $(evt.target).closest('.element-cell').attr("data-custmenu");
	}

	if (parentid) {
		parent = 'lmb_custmenu_' + parentid;
		child = 'lmb_custmenu_' + custmenu;
	} else if (custmenu) {
		divclose();
		child = 'lmb_custmenu_' + custmenu;
	}

	//limbasDivCloseTab.push(child); //  ??
	if (!document.getElementById(child)) {
		$('#limbasDivMenuContext').after("<div id='" + child + "' class='lmbContextMenu' style='position:absolute;z-index:994;' onclick='activ_menu = 1;'>");
	} else {
		divclose();
	}

	if (!parentid) {
		const rowid = el.id;
		if (!selected_rows[rowid]) {
			lmbTableClickEvent(evt, el, gtabid, ID);
		}
	}

	// use custmenu
	if (custmenu) {
		var fieldid = $(evt.target).closest('.element-cell').attr("data-fieldid");
		if (!fieldid) {
			fieldid = '';
		}
		var actrows = checkActiveRows();
		if (actrows.length > 1) {
			ID = actrows.join(";");
		}
		var actid = "gtabCustmenu&custmenu=" + custmenu + "&ID=" + ID + "&gtabid=" + gtabid + "&fieldid=" + fieldid;
		ajaxGet(null, "main_dyns.php", actid, null, '', null, child);
	} else {
		document.getElementById("lmbInfoCreate").innerHTML = $(el).attr("data-createinfo");
		document.getElementById("lmbInfoEdit").innerHTML = $(el).attr("data-editinfo");
		if (ID > 0) {
			document.form2.ID.value = ID;
			document.form2.gtabid.value = gtabid;
			document.form2.verknpf.value = $(el).attr("data-reltype");
			document.form2.verkn_tabid.value = $(el).attr("data-reltab");
			document.form2.verkn_fieldid.value = $(el).attr("data-relfield");
			document.form2.verkn_ID.value = $(el).attr("data-relid");
			document.form2.form_id.value = $(el).attr("data-formid");
		}
	}

	limbasDivShow(el, parent, child);
	window.setTimeout('set_activ_menu()', 500);

	return false;
}

// handle klick on row
function lmbTableClickEvent(evt, el) {
	// --------- activate row -------------
	var rowid = el.id;
	if (selected_rows[rowid]) {
		aktivateRow(evt, rowid, 0);
		// --------- deactivate row -------------
	} else {
		aktivateRow(evt, rowid, 1);
	}

	return false;
}

/*----------------- prüfe selektierte Reihen -------------------*/
function checkActiveRows(gtabid, getkey) {

	var actrows = new Array();
	var actrows_ = new Array();
	var actrowskey_ = new Array();
	var rsel = null;
	if (!gtabid && document.form2 && document.form2.ID.value) {
		actrows[0] = document.form2.ID.value + "_" + document.form2.gtabid.value;
		var bzm = 1;
	} else {
		var bzm = 0;
	}

	for (var key in selected_rows) {
		if (selected_rows[key]) {
			rsel = key.split("_");
			if (gtabid && rsel[2] != gtabid) {
				continue;
			}
			actrowskey_[bzm] = rsel[4];
			actrows_[bzm] = rsel[1];
			actrows[bzm] = rsel[1] + "_" + rsel[2];
			bzm++;
		}
	}

	if (gtabid) {
		// multiselect
		if (getkey) {
			return [actrows_, actrowskey_];
		} else {
			return actrows_;
		}
	} else {
		return actrows;
	}

}

function set_activ_menu() {
	activ_menu = 0;
}

function lmbTableClickCheckbox(el) {
	selected_rows_mem = 1;
}


// de/aktivate single row
var LmGl_edit_id = null;
var selected_rows = new Array();
var selected_rows_mem = null;

function aktivateRow(evt, id, activ, mem) {

	if (evt.shiftKey || evt.ctrlKey || selected_rows_mem) {
		var memory = 1;
	}

	if (!memory) {
		aktivateRows(0);
	}

	var prev_id = LmGl_edit_id;
	var vids = id.split("_");
	var datid = vids[1];
	var gtabid = vids[2];
	var fieldid = vids[3];

	// select range
	if (evt.shiftKey && prev_id) {
		var cc = null;
		var start = null;
		var down = null;
		var up = null;
		var ar = document.getElementsByTagName("tr");
		for (var i = ar.length; i > 0;) {
			cc = ar[--i];
			var cid = cc.id.split("_");
			if (cid[0] == "elrow" && cid[2] == gtabid) {
				var elid = cc.id;

				if (start) {
					aktivateSingleRow(elid, 1);
				}

				if (elid == prev_id && !up) {
					start = 1;
					down = 1
				}
				if (elid == id && !down) {
					start = 1;
					up = 1;
				}
				if (elid == id && !up) {
					start = 0;
					down = 1
				}
				if (elid == prev_id && !down) {
					start = 0;
					up = 1;
				}
			}
		}
	}

	aktivateSingleRow(id, activ);

	selected_rows_mem = null;

	return;
}

// de/aktivate single row
function aktivateSingleRow(elid, activ) {
	if (!document.getElementById(elid)) {
		return true;
	}
	if (activ) {
		$('#' + elid).addClass("gtabBodyTRActive");
		selected_rows[elid] = 1;
		LmGl_edit_id = elid;
		$('#chkb' + elid).prop("checked", true);
	} else {
		$('#' + elid).removeClass("gtabBodyTRActive");
		selected_rows[elid] = 0;
		$('#chkb' + elid).prop("checked", false);
	}
}

// de/aktivate all rows
function aktivateRows(activ) {
	if (activ) {
		var cc = null;
		var ar = document.getElementsByTagName("tr");
		for (var i = ar.length; i > 0;) {
			var cc = ar[--i];
			var cid = cc.id.split("_");
			if (cid[0] == "elrow") {
				var elid = 'elrow_' + cid[1] + '_' + cid[2];
				aktivateSingleRow(elid, 1);
			}
		}
	} else {
		for (var key in selected_rows) {
			if (selected_rows[key]) {
				aktivateSingleRow(key, 0);
			}
		}
	}
}

// count of active rows
function countofActiveRows(gtabid) {
	var count = 0;
	for (var key in selected_rows) {
		if (selected_rows[key]) {
			count = count + 1;
		}
	}
	return count;
}


// ---- Suchkriterien markieren ----------
var select_text = new Array();

function setSelectedText(evt, el, val) {
	if (val && el) {
		if (!select_text[el.id]) {
			select_text[el] = 0;
		}
		;
		var posStart = el.value.indexOf(val, select_text[el.id]);
		var posEnd = val.length + posStart;
		select_text[el.id] = posEnd;

		if (posStart > 0) {
			el.focus();
			// Mozilla ---
			if (el.setSelectionRange) {
				el.setSelectionRange(posStart, posEnd);
			}
			// IE ---
			else if (el.createTextRange) {
				var range = el.createTextRange();
				range.collapse(true);
				range.moveEnd('character', posEnd);
				range.moveStart('character', posStart);
				range.select();
			}
		}
	}
}


// ---- Tabellenkopf Notice ----------
function lmbGlistBodyNotice(id, val, el) {

	// gtabBodyDetailFringe

	if (document.getElementById('lmbGlistBodyNotice' + id)) {
		$('#lmbGlistBodyNotice' + id).replaceWith(val);
	} else {
		$('#GtabTableFull').prepend(val);
	}
	$('#GtabTableFull').first().css('float', '');

}

/* --- Fenster History ----------------------------------- */
function openHistory() {
	divclose();

	if (document.form2) {
		var ID = document.form2.ID.value;
		var gtabid = document.form2.gtabid.value;
	} else {
		var ID = document.form1.ID.value;
		var gtabid = document.form1.gtabid.value;
	}

	hist = open("main.php?action=history&wfl_id=" + jsvar["wfl_id"] + "&wfl_inst=" + jsvar["wfl_inst"] + "&ID=" + ID + "&tab=" + gtabid + "", "History", "toolbar=0,location=0,status=0,menubar=0,scrollbars=1,resizable=1,width=600,height=600");
}

/* --- page title ----------------------------------- */
function lmb_setPageTitle(title, header) {

	if (title) {
		//title = title.substring(1, title.length) + title.substring(0, 1);
		top.document.title = title;
		//setTimeout("lmb_setPageTitle(title)", 450);
	}
	if (header) {
		$('#lmb-main-title', window.parent.document).html(header);
	}

}

function lmbUndefToNull(value) {
	if (!value || typeof value === 'undefined') {
		return '';
	}
	return value;
}

// show detail in dialog or new window
function lmbOpenForm(evt, gtabid, id, params) {
	if (!params) {
		params = new Array();
	}
	return newwin7(evt, lmbUndefToNull(params['action']), gtabid, lmbUndefToNull(params['v_tabid']), lmbUndefToNull(params['v_fieldid']), lmbUndefToNull(params['v_id']), id, lmbUndefToNull(params['formid']), lmbUndefToNull(params['formdimension']), lmbUndefToNull(params['v_formid']), lmbUndefToNull(params['formopenas']), lmbUndefToNull(params['readonly']), lmbUndefToNull(params['dataAction']), lmbUndefToNull(params['force']));
}


/*
 * extension alias for newwin7
 *
 * event
 * @gtabid : table id
 * @formid : formular id
 * @id : data ID
 * @params : params array
 * params['action'] : gtab_change / gtab_erg / gtab_deterg / gtab_neu
 * params['v_tabid'] : relation tab ID
 * params['v_fieldid'] : relation field ID
 * params['v_id'] : relation data ID
 * params['v_formid'] : relation formular ID
 * params['formdimension'] : formular dimension in px '123x123'
 * params['inframe'] : div / iframe / tab / same / window
 * params['readonly'] : 0 / 1
 * params['dataAction'] : copy / versioning
 * params['dataAction'] : force / force settings
 */
function newwin7(evt, action, gtabid, v_tabid, v_fieldid, v_id, id, formid, formdimension, v_formid, inframe, readonly, dataAction, force) {

	divclose();

	if (formid === null) {
		formid = '';
	}

	// simple table without relation
	if (!gtabid) {
		gtabid = v_tabid;
		v_fieldid = '';
		v_id = '';
	}

	// default action
	if (action != 'gtab_erg' && action != 'gtab_neu' && action != 'gtab_form') {
		if (jsvar["detailform_viewmode_" + gtabid] == 1) {
			action = 'gtab_deterg';
		} else {
			action = 'gtab_change';
		}
	}

	if ((action == 'gtab_erg' || typeof id === 'undefined') && readonly == 2) {
		readonly = 1;
	} else {
		readonly = '';
	} // todo

	verkn_showonly = '';
	if (action == 'gtab_erg' && v_id) {
		verkn_showonly = 1;
	}

	var verknpf = '';
	if (v_tabid > 0) {
		verknpf = 1;
	}

	// default modal size
	if (!formdimension || formdimension == '' || typeof formdimension === 'undefined') {
		if (jsvar["modal_size"]) {
			formdimension = jsvar["modal_size"];
		} else {
			formdimension = '800x600';
		}
	}

	// auto modal size
	if (formdimension == 'auto') {
		var wwidth = (document.body.offsetWidth - 200);
		var wheight = (document.body.offsetHeight - 100);
		formdimension = wwidth + 'x' + wheight;
	}

	if (formdimension) {
		var dimsn = formdimension.split("x");
		var x = (parseInt(dimsn[0]) + 30);
		var y = (parseInt(dimsn[1]) + 30);
	}

	// default modal opener
	if (!inframe) {
		if (verknpf && jsvar["detail_rel_openas"]) {
			inframe = jsvar["detail_rel_openas"];
		} else if (jsvar["detail_openas"]) {
			inframe = jsvar["detail_openas"];
		}
	}

	// model == iframe
	if (inframe == 'modal') {
		inframe = 'iframe';
	}

	// desination is userdefined element
	var layername = 'lmb_gtabDetailFrame';
	if (inframe && inframe != 'div' && inframe != 'iframe' && inframe != 'same') {
		layername = inframe;
	}

	// force new win
	if (evt && evt.ctrlKey) {
		inframe = 'window';
	}

	// resize window if window in window
	if (!force && (inframe == 'iframe' || inframe == 'div')) {
		// check if modal already exists
		if ($('#lmbSbmClose_' + v_tabid + '_' + v_id).is(':visible')) {
			// size of modal
			if (jsvar["modal_level"] == 'nested') {
				x = (x - 60);
				y = (y - 60);
			} else {
				inframe = 'same';
			}
		}
	}

    // set breadcrumb
	let relation_path = '';

    if(v_tabid && v_tabid > 0 && document.form1) {

        let breadcrumb = new Array();
        let parent_gtabid = '';
        let verkn_showonly = '';
        let parent_snap_id = '';
        let action_ = document.form1.action.value;

        if (document.form1.verkn_showonly && action_ == 'gtab_erg') {
            verkn_showonly = document.form1.verkn_showonly.value;
        }
        if (document.form1.verkn_relationpath.value) {
            breadcrumb = document.form1.verkn_relationpath.value.split(";");
        }
        if (document.form1.gtabid.value) {
            parent_gtabid = document.form1.gtabid.value;
        }
        if (document.form1.snap_id.value) {
            parent_snap_id = document.form1.snap_id.value;
        }

        let br_action = 1;
        if(action_ == 'gtab_erg'){
            br_action = 3;
        }else if(action_ == 'gtab_change'){
            br_action = 2;
        }

        let br_v_tabid = '';
        if(parent_gtabid == gtabid){
            br_v_tabid = gtabid;
        }

        var newpath = br_v_tabid + "," + v_tabid + "," + v_id + "," + v_formid + "," + v_fieldid + ","+verkn_showonly+"," + br_action+"," + parent_snap_id;

        // check for dublicates - todo selfrelation
        if (!breadcrumb.includes(newpath)) {
            breadcrumb.push(newpath);
            relation_path = breadcrumb.join(";");
        }
    }

    // ---------------

	var use_typ = '';
	if (dataAction) {
		var use_typ = '&use_typ=' + dataAction + '&use_record=' + id + '_' + gtabid;
	}

	// show in new window
	if (!inframe || inframe == 'window') {
		if (y > 0) {
			y = (y + 40);
		}
		var wpath = "main.php?action=" + action + "&verkn_showonly=" + verkn_showonly + "&ID=" + id + "&verkn_ID=" + v_id + "&gtabid=" + gtabid + "&verkn_tabid=" + v_tabid + "&verkn_fieldid=" + v_fieldid + "&form_id=" + formid + "&verknpf=" + verknpf + "&verkn_formid=" + v_formid + '&readonly=' + readonly + use_typ;
		relationtab = open(wpath, "relationtable", "toolbar=0,location=0,status=0,menubar=0,scrollbars=1,resizable=1,width=" + x + ",height=" + y);
		return;
	}

	if (inframe == 'tab') {
		//wpath = "index.php?action=redirect&src=action=" + action + "%26verkn_showonly=1%26ID=" + id + "%26verkn_ID=" + v_id + "%26gtabid=" + gtabid + "%26verkn_tabid=" + v_tabid + "%26verkn_fieldid=" + v_fieldid + "%26form_id=" + formid + "%26verkn_formid=" + v_formid + '%26verkn_relationpath='+relation_path + '%26readonly='+readonly+use_typ+'%26verknpf=' + verknpf;
		var baseurl = top.window.location.href.split("?")[0];
		wpath = baseurl + "?action=" + action + "&verkn_showonly=" + verkn_showonly + "&ID=" + id + "&verkn_ID=" + v_id + "&gtabid=" + gtabid + "&verkn_tabid=" + v_tabid + "&verkn_fieldid=" + v_fieldid + "&form_id=" + formid + "&verkn_formid=" + v_formid + '&verkn_relationpath=' + relation_path + '&readonly=' + readonly + use_typ + '&verknpf=' + verknpf + '&redirect=1';
		relationtab = open(wpath, "_blank");
		return;
	}

	// show in same window
	if (inframe == 'same') {

		var formindex = 'form1';       // detail form
		if (document.form2 && document.form2.gtabid) {
			var formindex = 'form2';   // tablelist
		}

		document.forms[formindex].ID.value = id;
		document.forms[formindex].action.value = action;
		document.forms[formindex].gtabid.value = gtabid;
		document.forms[formindex].form_id.value = formid;
        document.forms[formindex].verkn_relationpath.value = relation_path;

		if (document.forms[formindex].verkn_ID) {
			document.forms[formindex].verkn_ID.value = v_id;
		}
		if (document.forms[formindex].verkn_tabid) {
			document.forms[formindex].verkn_tabid.value = v_tabid;
		}
		if (document.forms[formindex].verkn_fieldid) {
			document.forms[formindex].verkn_fieldid.value = v_fieldid;
		}
		if (document.forms[formindex].verkn_showonly) {
			document.forms[formindex].verkn_showonly.value = verkn_showonly;
		}
		if (document.forms[formindex].verknpf) {
			document.forms[formindex].verknpf.value = verknpf;
		}

		// todo + "verknpf=" + verknpf // do not show close&save button but is needed for filter relations

		if (formindex == 'form1') {
			if (document.forms[formindex].change_ok) {
				document.forms[formindex].change_ok.value = 1;
			}
			if (!document.forms[formindex].verkn_poolid.value && document.forms[formindex].verkn_poolid) {
				document.forms[formindex].verkn_poolid.value = v_tabid;
			}
			if (dataAction) {
				if (document.forms[formindex].use_typ) {
					document.forms[formindex].use_typ.value = dataAction;
				}
				if (document.forms[formindex].use_record) {
					document.forms[formindex].use_record.value = id + '_' + gtabid;
				}
			}
		}
		document.forms[formindex].submit();
		return;

		// show in same window
	// } else if (inframe == 'same') {
	// 	var wpath = "main.php?action=" + action + "&verkn_showonly=" + verkn_showonly + "&ID=" + id + "&verkn_ID=" + v_id + "&gtabid=" + gtabid + "&verkn_tabid=" + v_tabid + "&verkn_fieldid=" + v_fieldid + "&form_id=" + formid + "&verknpf=" + verknpf + "&verkn_formid=" + v_formid + '&verkn_relationpath=' + relation_path + '&readonly=' + readonly + use_typ;
	// 	window.location.href = wpath;

		// show in dialog as div
	} else if (inframe == 'div') {
		// show in frame as div
		$("#" + layername).remove();
		$.ajax({
			type: "POST",
			url: "main_dyns.php",
			async: false,
			dataType: "html",
			data: $('#form1 #' + layername + ' :input').add('[name=history_fields]').add('[name=filter_tabulatorKey]').add('[name=filter_groupheader]').add('[name=filter_groupheaderKey]').add('[name=old_action]').serialize() + "&actid=openSubForm&ID=" + id + "&gtabid=" + gtabid + "&gformid=" + formid + "&action=" + action + "&verkn_ID=" + v_id + "&verkn_tabid=" + v_tabid + "&verkn_fieldid=" + v_fieldid + "&verkn_showonly=" + verkn_showonly + '&subformlayername=' + layername + '&detail_isopenas='+inframe+'&readonly=' + readonly + use_typ,
			success: function (data) {
				$("<div id='" + layername + "'></div>").html(data).css({
					'position': 'relative',
					'left': '0',
					'top': '0'
				}).dialog({
					width: x,
					height: y,
					resizable: true,
					modal: true,
					zIndex: 99999,
					appendTo: "#form1",
					close: function () {
						$("#" + layername).dialog('destroy').remove();
						document.body.style.cursor = 'default';
						$('[name=history_fields]').val('');
					}
				});

				// show save&close button
				$("#lmbSbmClose_" + gtabid + "_" + id).show();
			}
		});
		// show in dialog as iframe
	} else if (inframe == 'iframe') {
		$("#" + layername).remove();
		$("body").append("<div id='" + layername + "' style='position:absolute;display:none;z-index:9999;overflow:hidden;width:" + x + "px;height:" + y + "px;padding:0;'><iframe id='lmb_gtabDetailIFrame' name='lmb_gtabDetailIFrame' style='width:100%;height:100%;overflow:auto;'></iframe></div>");
		$("#" + layername).css({'position': 'relative', 'left': '0', 'top': '0'}).dialog({
			width: x,
			height: y,
			resizable: true,
			modal: true,
			zIndex: 99999,
			open: function (ev, ui) {
				$('#lmb_gtabDetailIFrame').attr("src", "main.php?action=" + action + "&verkn_showonly=" + verkn_showonly + "&ID=" + id + "&verkn_ID=" + v_id + "&gtabid=" + gtabid + "&verkn_tabid=" + v_tabid + "&verkn_fieldid=" + v_fieldid + "&form_id=" + formid + "&verknpf=" + verknpf + "&verkn_formid=" + v_formid + '&detail_isopenas='+inframe+'&readonly=' + readonly + use_typ);
			},
			close: function () {
				//lmb_gtabDetailIFrame.document.form1.action.value = 'gtab_change';
				//lmb_gtabDetailIFrame.send_form(1, 0, 1);
				$("#" + layername).dialog('destroy').remove();
			}
		});

		// show in existing container
	} else if (document.getElementById(layername) && document.getElementById(layername).tagName.toLowerCase() == 'div') {
		// show in frame as div
		$.ajax({
			type: 'POST',
			url: "main_dyns.php",
			async: false,
			dataType: "html",
			data: $('#form1 #' + layername + ' :input').add('[name=history_fields]').add('[name=filter_tabulatorKey]').add('[name=filter_groupheader]').add('[name=filter_groupheaderKey]').add('[name=old_action]').serialize() + "&actid=openSubForm&ID=" + id + "&gtabid=" + gtabid + "&gformid=" + formid + "&action=" + action + "&verkn_ID=" + v_id + "&verkn_tabid=" + v_tabid + "&verkn_fieldid=" + v_fieldid + '&subformlayername=' + layername + '&readonly=' + readonly + use_typ,
			success: function (data) {
				$('#' + layername).html(data);
				document.body.style.cursor = 'default';
				$('[name=history_fields]').val('');

				// show save&close button
				$("#lmbSbmClose_" + gtabid + "_" + id).show();
			}
		});

		// show in existing iframe
	} else if (document.getElementById(layername) && document.getElementById(layername).tagName.toLowerCase() == 'iframe') {
		$('#' + layername).attr("src", "main.php?action=" + action + "&verkn_showonly=" + verkn_showonly + "&ID=" + id + "&verkn_ID=" + v_id + "&gtabid=" + gtabid + "&verkn_tabid=" + v_tabid + "&verkn_fieldid=" + v_fieldid + "&form_id=" + formid + "&verknpf=" + verknpf + "&verkn_formid=" + v_formid + '&readonly=' + readonly + use_typ);

		// show in parent existing iframe
	} else if (parent.document.getElementById(layername) && parent.document.getElementById(layername).tagName.toLowerCase() == 'iframe') {
		parent.$('#' + layername).attr("src", "main.php?action=" + action + "&verkn_showonly=" + verkn_showonly + "&ID=" + id + "&verkn_ID=" + v_id + "&gtabid=" + gtabid + "&verkn_tabid=" + v_tabid + "&verkn_fieldid=" + v_fieldid + "&form_id=" + formid + "&verknpf=" + verknpf + "&verkn_formid=" + v_formid + '&readonly=' + readonly + use_typ);
	}

}

/* --- Fenster History ----------------------------------- */
function openHistory() {
	divclose();

	if (document.form2) {
		var ID = document.form2.ID.value;
		var gtabid = document.form2.gtabid.value;
	} else {
		var ID = document.form1.ID.value;
		var gtabid = document.form1.gtabid.value;
	}

	hist = open("main.php?action=history&wfl_id=" + jsvar["wfl_id"] + "&wfl_inst=" + jsvar["wfl_inst"] + "&ID=" + ID + "&tab=" + gtabid + "", "History", "toolbar=0,location=0,status=0,menubar=0,scrollbars=1,resizable=1,width=600,height=600");
}

//----------------- breadcrumb -------------------
function jump_to_breadcrumb(key) {

	if(!document.form1.verkn_relationpath || !document.form1.verkn_relationpath.value){return;}

    let breadcrumb = new Array();
    let last_breadcrumb = new Array();
    let prev_breadcrumb = new Array();
    let new_breadcrumb = new Array();

    breadcrumb = document.form1.verkn_relationpath.value.split(";");

    let brlen = breadcrumb.length;

    if(!key){
       key = (brlen-1);
    }

    // go to specified breadcrumb
    for(var i=0; i<=key; i++){
        new_breadcrumb[i] = breadcrumb[i];
        // last breadcrumb
        if(i == key) {
            last_breadcrumb = breadcrumb[i].split(",")
        }
        // previos breadcrumb
        if(i == (key - 1)){
            prev_breadcrumb = breadcrumb[i].split(",")
        }
    }

    // set gtabid if
    let gtabid = (last_breadcrumb[0]) ? last_breadcrumb[0] : last_breadcrumb[1];

    last_breadcrumb[3] = String(Number(last_breadcrumb[3])); // support 0 -> default formular
    document.form1.gtabid.value = gtabid;
    document.form1.ID.value = last_breadcrumb[2];
    document.form1.form_id.value = last_breadcrumb[3];
    document.form1.snap_id.value = last_breadcrumb[7];

    // higher level to gtab_erg
    if(key > 0) {
        document.form1.verkn_tabid.value = prev_breadcrumb[1];      // verkn_tabid
        document.form1.verkn_ID.value = prev_breadcrumb[2];         // verkn_ID
        document.form1.verkn_fieldid.value = prev_breadcrumb[4];    // verkn_fieldid
        document.form1.verknpf.value = 1;
    // first level without relation
    }else{
        document.form1.verkn_tabid.value = '';
        document.form1.verkn_ID.value = '';
        document.form1.verkn_fieldid.value = '';
        document.form1.verknpf.value = '';
    }

    // set new breadcrumb
    new_breadcrumb.pop();
    document.form1.verkn_relationpath.value = new_breadcrumb.join(";");

    // action
    let action = 'gtab_erg';
    if(last_breadcrumb[6] == 1){
        action = 'gtab_deterg';
    }else if(last_breadcrumb[6] == 2){
        action = 'gtab_change';
    }else{
        action = 'gtab_erg';
        document.form1.verkn_showonly.value=last_breadcrumb[5];
    }

    document.form1.action.value = action;

    send_form(1);

}

// --- Fenster Mehrfach-Selectfeld -----------------------------------
function newwin10(FIELDID, TABID, TABGROUP, ID) {
	divclose();
	selectpool = open("main.php?&action=add_select&ID=" + ID + "&field_id=" + FIELDID + "&gtabid=" + TABID + "&wfl_id=" + jsvar["wfl_id"] + "&wfl_inst=" + jsvar["wfl_inst"], "selectpool", "toolbar=0,location=0,status=0,menubar=0,scrollbars=1,resizable=1,width=500,height=600");
}


/*----------------- Speech recognition -------------------*/
var lmb_speechRec = window.SpeechRecognition || window.webkitSpeechRecognition || window.mozSpeechRecognition || window.msSpeechRecognition;
lmb_speechRec = lmb_speechRec ? new lmb_speechRec() : null;
var lmb_speechRecActive = false;
var lmb_speechRecIcon = null;
var lmb_speechRecInput = null;
var lmb_speechRecTextPre = null;
var lmb_speechRecTextPost = null;
if (lmb_speechRec) {
	lmb_speechRec.lang = "de";
	lmb_speechRec.continuous = true;
	lmb_speechRec.interimResults = true;

	lmb_speechRec.onstart = function () {
		lmb_speechRecIcon.removeClass('lmb-microphone').addClass('lmb-microphone-slash');
		lmb_speechRecIcon.css('color', 'red');
		lmb_speechRecInput.css('border-color', 'red');
		lmb_speechRecActive = true;

		// only replace selection -> store strings before and after selection
		var selectionStart = lmb_speechRecInput.get(0).selectionStart;
		var selectionEnd = lmb_speechRecInput.get(0).selectionEnd;
		lmb_speechRecTextPre = lmb_speechRecInput.val().substring(0, selectionStart).trim();
		if (lmb_speechRecTextPre !== '') {
			lmb_speechRecTextPre += ' ';
		}
		lmb_speechRecTextPost = lmb_speechRecInput.val().substring(selectionEnd).trim();
		if (lmb_speechRecTextPost !== '') {
			lmb_speechRecTextPost = ' ' + lmb_speechRecTextPost;
		}
	};

	lmb_speechRec.onend = function () {
		lmb_speechRecIcon.removeClass('lmb-microphone-slash').addClass('lmb-microphone');
		lmb_speechRecIcon.css('color', '');
		lmb_speechRecInput.css('border-color', '');
		lmb_speechRecInput.trigger('change');
		lmb_speechRecActive = false;
	};

	lmb_speechRec.onresult = function (event) {
		lmb_speechRecInput.val(lmb_speechRecTextPre + lmb_speechRecTextPost);
		var wholeTranscript = "";
		for (var i = 0; i < event.results.length; i++) {
			wholeTranscript += event.results[i][0].transcript;
		}
		lmb_speechRecInput.val(lmb_speechRecTextPre + wholeTranscript + lmb_speechRecTextPost);
	};
}

function lmb_addSpeechRecButton(inputID) {
	if (lmb_speechRec) {
		var inputs = $('input[name="' + inputID + '"]:visible');
		if (!inputs.parent().has('i.lmb-icon.lmb-icon-on-input.lmb-microphone').length) {
			inputs.parent().append('<i onclick="lmb_toggleSpeechRec(this, \'' + inputID + '\')" class="lmb-icon lmb-icon-on-input lmb-microphone" style="cursor:pointer;"></i>');
		}
	}
}

function lmb_toggleSpeechRec(icon, inputID) {
	if (lmb_speechRecActive) {
		lmb_speechRec.stop();
	} else {
		lmb_speechRecIcon = $(icon);
		lmb_speechRecInput = $(icon).parent().children('#' + inputID);
		lmb_speechRec.start()
	}
}


/*----------------- Attribute Tag-Mode -------------------*/
$.fn.textWidth = function (text, font) {
	if (!$.fn.textWidth.fakeEl) $.fn.textWidth.fakeEl = $('<span>').hide().appendTo(document.body);
	$.fn.textWidth.fakeEl.text(text || this.val() || this.text()).css('font', font || this.css('font'));
	return $.fn.textWidth.fakeEl.width();
};


function tagAttr_init() {
	tagAttr_initSize();
	var $tagattr = $('div.tagattr:not(:data(attrloaded))');
	$tagattr.data('attrloaded', true);
	$tagattr.children('input').val('+').click(tagAttr_onSearchClick).blur(tagAttr_onSearchBlur);
	$tagattr.on('focus', 'table input[type=text]', tagAttr_onInputFocus).on('blur', 'table input[type=text]', tagAttr_onInputBlur).on('keydown', 'table input[type=text]', tagAttr_resizeInput);
	$tagattr.on('focus', 'select', tagAttr_onFocusChangeSelect).on('blur', 'select', tagAttr_onBlurSelect).on('change', 'select', tagAttr_onFocusChangeSelect);
}

function tagAttr_initSize() {
	$('.tagattr').removeClass('transition');

	$('.tagattr table input[type=text]').css('width', function () {
		if (!$(this).val()) {
			$(this).closest('tr').addClass('empty');
			return '3rem';
		} else {
			$(this).closest('tr').removeClass('empty');
			return ($(this).textWidth() + 25) + 'px';
		}
	});


	$('.tagattr select').css('width', function () {
		if (!$(this).val()) {
			$(this).closest('tr').addClass('empty');
			return '3rem';
		} else {
			$(this).closest('tr').removeClass('empty');
			return '100%';
		}
	});
	setTimeout(tagAttr_Transitions, 500);
}


function tagAttr_Transitions() {
	$('.tagattr').addClass('transition');
}

function tagAttr_onInputFocus() {
	if (!$(this).val()) {
		$(this).css('width', '5rem');
	} else {
		let textWidth = $(this).textWidth() + 25;
		if (textWidth < 16 * 6) {
			$(this).css('width', '5rem');
		}
	}
}

function tagAttr_onInputBlur() {
	if (!$(this).val()) {
		$(this).css('width', '3rem').closest('tr').addClass('empty');
	} else {
		$(this).css('width', ($(this).textWidth() + 25) + 'px').closest('tr').removeClass('empty');
	}
}

function tagAttr_resizeInput() {
	$(this).css('width', ($(this).textWidth() + 25) + 'px');
}

function tagAttr_onFocusChangeSelect() {
	if (!$(this).val()) {
		$(this).width('100%');
	} else {
		$(this).css('width', ($(this).textWidth() + 25) + 'px');
	}
}

function tagAttr_onBlurSelect() {
	if (!$(this).val()) {
		$(this).css('width', '3rem').closest('tr').addClass('empty');
	} else {
		$(this).css('width', ($(this).textWidth() + 25) + 'px').closest('tr').removeClass('empty');
	}
}


function tagAttr_onSearchClick() {
	$(this).val('').css('text-align', 'left');
	this.style.setProperty('width', '8rem', 'important');
}

function tagAttr_onSearchBlur() {
	$(this).val('+').removeAttr('style');
}

