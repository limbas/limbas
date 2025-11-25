/*
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */



// change focus before sending document




function set_focus() {

    $(window).focus();

}

function LmGs_sendForm(reset,gtabid){

    if(reset){
        document.form11.filter_reset.value = 1;
        $("#snap_id").val(0);
    }

    if(document.form1.snap_id){
    if($("#snap_id").val()) {
        //$('#tabs_' + gtabid).html($("#snap_id option:selected").text()); // header tablename
        document.form1.snap_id.value = $("#snap_id").val(); // set form snap_id
        $("#globalFilter option[value='" + $('#snap_id').val() + "']").attr('selected', true);
    }else{
        document.form1.snap_id.value = $("#snap_id").val();
        $("#globalFilter option[value='0']").attr('selected', true);
    }}

    if(document.form2.verknpf){document.form11.verknpf.value = document.form2.verknpf.value}
    if(document.form2.verkn_ID){document.form11.verkn_ID.value = document.form2.verkn_ID.value}
    if(document.form2.verkn_tabid){document.form11.verkn_tabid.value = document.form2.verkn_tabid.value}
    if(document.form2.verkn_fieldid){document.form11.verkn_fieldid.value = document.form2.verkn_fieldid.value}
    if(document.form2.verkn_showonly){document.form11.verkn_showonly.value = document.form2.verkn_showonly.value}

    if(document.form1.action.value == 'explorer_main') {
        document.form11.action.value = 'explorer_main';
        document.form11.LID.value = document.form1.LID.value;
        LmEx_send_form(11, 1);
    }else if(document.form1.action.value == 'gtab_erg' && document.form1.form_id.value) {
        send_form(11);
    }else if(document.form1.action.value == 'gtab_erg') {
        lmbAjax_resultGtab('form11', 1, 1, 1, 1);
    }

    //$( '#lmbAjaxContainer' ).dialog( "destroy" );
    $('#searchFilterModal').modal('hide');

	//if(typeof(window.send_form) == "function") {
	//	document.form11.action.value = 'gtab_erg';
	//	document.form11.gtabid.value = document.form1.gtabid.value;
	//	document.form11.snap_id.value = document.form1.snap_id.value;
	//	document.form11.gfrist.value = document.form1.gfrist.value;
	//	send_form(11);
	//}else if(typeof(window. send_form) == "function") {
	//	document.form11.action.value = 'explorer_main';
	//	document.form11.LID.value = document.form1.LID.value;
	//	LmEx_send_form(11);
	//}
}

function LmGs_elDisable(id) {
	var ar = document.getElementsByTagName("input");
	var cc = null;
	for (var i = ar.length; i > 0;) {
		cc = ar[--i];
		if(cc.id.substring(0,3) == "gds"){
			elname = cc.name.split("[");
			if(elname[2] == id+"]" || elname[2] == "andor]"){
				cc.disabled = 0;
			}else{
				cc.disabled = 1;
			}
		}
	}
	var ar = document.getElementsByTagName("select");
	var cc = null;
	for (var i = ar.length; i > 0;) {
		cc = ar[--i];
		if(cc.name.substring(0,3) == "gds"){
			elname = cc.name.split("[");
			if(elname[2] == id+"]"){
				cc.disabled = 0;
			}else{
				cc.disabled = 1;
			}
		}
	}
}

/**
 * Called when double clicking on search field above table.
 * Opens search form in new overlay window
 * @param evt browser mouse event
 * @param el td element that was double clicked
 * @param gtabid table id
 * @param fieldid field id
 * @param container id of container to open search form in
 * @param snap_id id of currently opened snapshot
 * @param module
 */
function limbasDetailSearch(evt,el,gtabid,fieldid,container,snap_id,module,save_search_selection){
	if(!fieldid){fieldid = '';}
	if(!gtabid){gtabid = '';}
	if(!snap_id && snap_id != 0){snap_id = '';}
    if(!module){module = '';}
    //if(!container){container = 'lmbAjaxContainer';}

    container = 'searchFilterModal-body';

    if(save_search_selection){
        var formname = 'form11';
    }else{
        var formname = '';
        save_search_selection = '';
    }

    //if(!document.getElementById(container)) {
        //$("body").append('<div id="'+container+'"></div>');
    //}

    if(document.form1.form_id){
        form_id = document.form1.form_id.value;
    }else{
        form_id = '';
    }

    ajaxfunc = function(data){
        ajaxEvalScript(data,fieldid);
        document.getElementById(container).innerHTML = data;

        $('#searchFilterModal').modal('show');

        // $("#"+container).css({'position':'relative','left':'0','top':'0'}).dialog({
        //     title: jsvar["lng_101"],
        //     width: 650,
        //     minHeight: 400,
        //     maxHeight: 700,
        //     resizable: false,
        //     modal: true,
        //     zIndex: 10
        // });

        // show row that corresponds to clicked search field
        if (fieldid){
            limbasAddSearchPara('', gtabid, fieldid);
        }
    };
    actid = "gtabSearch&gtabid="+gtabid+"&snap_id="+snap_id+'&module='+module+'&form_id='+form_id+'&save_search_selection='+save_search_selection;
    ajaxGet(null,"main_dyns.php",actid,null,"ajaxfunc",formname);

}

/**
 * Relevant when double clicking on search field above table.
 * Called when searchfield selection dropdown was changed.
 * Loads the corresponding search field and displays underneath.
 * @param originStr string of <tabID>_<relationFieldId>_<filterIndex>_<tabID2>_...
 * @param gtabid int id of table whose field should be searched
 * @param fieldid int id of field in gtabid which should be searched in
 */
function limbasAddSearchPara(originStr, gtabid, fieldid){
	// convention: recursive gsr has filterIndex != 0
	if (originStr) {
		const originParts = originStr.split('_');
		for (let i = 0; i < originParts.length; i += 3) {
			const filterIndex = parseInt(originParts[i + 2]);
			if (filterIndex === 0) {
				originParts[i + 2] = "1";
			}
		}
		originStr = originParts.join('_');
	}

    // already added to list?
	const originStrPad = originStr ? (originStr + '_') : '';
    const identifierStr = originStrPad + gtabid + '_' + fieldid;
    const tbody = $('#searchFilterRowTable').children('tbody');
    const existingRows = tbody.children('[id="gdr_' + identifierStr + '_0"]');
    if (existingRows.length > 0) {
		existingRows.first()
			.find('input[id^="gds_"]').focus() // focus text input
			.closest('tr').show(); // un-hide row
        return;
	}

	// parent already added to list -> increase filterIndex
	const originParts = originStr.split('_');
    for (let i = 0; i < originParts.length; i += 3) {
    	const subIdentifierStr = originParts.slice(0, i + 3).join('_');
    	const existingRow = tbody.children('[id="gdr_' + subIdentifierStr + '"]');
    	if (existingRow.length > 0) {
    		originParts[i + 2] = (parseInt(originParts[i + 2]) + 1).toString();
		}
	}

    // fetch single tr
    const params = $.param({
        originStr: originParts.join('_'),
        gtabid: gtabid,
        fieldid: fieldid
    });
    ajaxGet(null, 'main_dyns.php', 'gtabSearchFilterRow&' + params, null, function(result) {
        // add new row
        $(result)
            .appendTo($('#searchFilterRowTable').children('tbody'))
            .find('input[id^="gds_"]')
            .focus();
	});
}

/**
 * Relevant when double clicking on search field above table.
 * Called when "equals"/"contains"/"begins with" - dropdown of a filter row is changed.
 * Enables/Disabled the corresponding "negate"/"case sensitive" checkboxes of that row.
 * @param value string the value of the operator dropdown
 * @param idStr string the gdsneg_xxx/gdscs_xxx suffix to identify the row
 */
function limbasSetSearchOptionsActive(value, idStr) {
	if (!value) {
		return;
	}
    value = parseInt(value);

    // ["txt"] = 1 : %$%
    // ["txt"] = 2 : ==
    // ["txt"] = 3 : $%
    // ["txt"] = 4 : Metaphone
	// ["txt"] = 5 : %$
	// ["txt"] = 7 : #NULL
	// ["txt"] = 8 : #NOTNULL

	const $negate = $('#gdsneg_' + idStr);
    const $cs = $('#gdscs_' + idStr);
    const $val = $('#gds_' + idStr);
    if (value === 7 || value === 8) {
		$negate.prop('checked', false).prop('disabled', true);
        $cs.prop('checked', false).prop('disabled', true);
        $val.val('').prop('disabled', true);
    } else {
        $negate.prop('disabled', false);
        $cs.prop('disabled', false);
        $val.prop('disabled', false);
    }
}

/**
 * Relevant when double clicking on search field above table.
 * Called when clicking on plus icon in a filter row.
 * Loads an additional row via ajax and displays underneath.
 *
 * @param el plus icon
 * @param originStr
 * @param gtabid
 * @param fieldid
 * @param filterIndex
 */
function limbasExpandSearchPara(el, originStr, gtabid, fieldid, filterIndex){
    let $lastRowCurrentField = $(el).closest('tr').nextUntil('[id$="_0"]').last();
    if ($lastRowCurrentField.length === 0) {
        $lastRowCurrentField = $(el).closest('tr');
    }
    const lastFilterIndex = parseInt($lastRowCurrentField.attr('id').split('_').slice(-1)[0]);

    // check if new identifier already exists
    const identifierStr = originStr + gtabid + '_' + fieldid + '_' + (lastFilterIndex + 1);
    if ($(el).closest('tbody').children('[id^="gdr_' + identifierStr + '"]').length > 0) {
    	// this happens when both Relation and recursive Relation filters exist (e.g. Kunde and Kunde/Name)
		// Kunde then has index 0, Kunde/Name has index 1
		// if the plus icon is pressed for Kunde, then index 1 is not available anymore
		// TODO solve in future if needed
    	return;
	}
    const params = $.param({
        originStr: originStr,
        gtabid: gtabid,
        fieldid: fieldid,
        filterIndex: lastFilterIndex + 1
    });
    ajaxGet(null, 'main_dyns.php', 'gtabSearchFilterRow&' + params, null, function(result) {
		const $result = $(result);
		const $selectCombinator = $result.find('select[name^="gs"]');

		// check if row above is attribute, and hide 'oder Inhalt'
		if ($lastRowCurrentField.attr('id').endsWith('0')) {
			if ($selectCombinator.length > 0) {
				$selectCombinator.find('option[value="4"]').remove();
			}
		}
        $lastRowCurrentField.after($result);
    });
}

/**
 * Relevant when double clicking on search field above table.
 * Called when clicking on X icon in a filter row.
 * Hides the row and clears its input.
 *
 * @param el the X icon
 * @param identifierStr originStr_gtabid_fieldid
 * @param filterIndex
 */
function limbasHideFilterRow(el, identifierStr, filterIndex) {
	// row has to be hidden (instead of removed) s.t. its empty value is still sent to server
	var rows;
	if (filterIndex > 0) {
		// the current row
		rows = $(el).closest('tr');
    } else {
		// all rows of that field
		rows = $(el).closest('tr').nextUntil('[id$="_0"]').add($(el).closest('tr'));
	}
	rows.find('[name^=gs]').val('');
	rows.hide();
}

function lmb_searchShowLevel(level) {

    if($(".slevel-"+level).first().css("display") == "none"){
        $(".slevel-"+level).show();
    }else{
        $(".slevel-"+level).hide();
        $(".slevel-"+(level+1)).hide();
        $(".slevel-"+(level+2)).hide();
        $(".slevel-"+(level+3)).hide();
    }

}

function lmb_searchDropdown(inputElem, selectID, openSize) {
    // default size
    if (!openSize) {
        openSize = 10;
    }

    // get both input and select elements
    const inputEl = $(inputElem);
    const selectEl = $('#' + selectID);

    // open select
    selectEl.attr('size', openSize);

    // add event listeners if not already added
    if (!inputEl.is('data-lmb-initialized')) {
        inputEl.prop('data-lmb-initialized', true);

        // focus lost from input
        inputEl.blur(function(event) {
            // focus from input -> select
            if (selectEl.is(event.relatedTarget)) {
                return;
            }

            selectEl.attr('size', 1);
        });

        // focus lost from select
        selectEl.blur(function(event) {
            // focus from select -> input
            if (inputEl.is(event.relatedTarget)) {
                return;
            }

            selectEl.attr('size', 1);
        });

        // typed into input -> filter select
        inputEl.keyup(function() {
            selectEl.attr('size', openSize);

            var searchVal = $(this).val();
            if (searchVal && searchVal !== '') {
                searchVal = searchVal.toLowerCase();
                selectEl.children('optgroup').hide();
                selectEl.find('option').each(function() {
                    if ($(this).text().toLowerCase().indexOf(searchVal) >= 0 || $(this).val() === '0' /* empty rows */) {
                        $(this).show();
                        $(this).parent().show();
                    } else {
                        $(this).hide();
                    }
                });
            } else {
                selectEl.find('*').show();
                inputEl.val('');
            }
        });

        // selected option -> close
        selectEl.change(function() {
            selectEl.attr('size', 1);
        });
    }
}

/**
 * If batch change is enabled, in table under edit->batch change:
 * Shows the value entry field if field was selected
 * @param fieldID id of field that was selected
 */
function lmb_showReplaceField(fieldID) {
	if (!fieldID) {
		return;
	}

    // batch change
    $('div[id^=gse]').hide();
    $('tr[id^=gsea]').show();
    $('[id^=gse_'+fieldID+'_]').show();
    $('[id^=gser_'+fieldID+'_]').show();
    $('[id^=gsec_'+fieldID+'_]').show();
    $('[id^=gseo_'+fieldID+'_]').show();
}

// ------------------------------------------

function showprogress(id, value){
	let $progressBar = $('#'+id);
	
	if (value==100){
        $progressBar.parent().hide();
		return;
	}

    $progressBar.width(Math.round(value)+'%');
    $progressBar.text(Math.round(value)+'%');
}

function showprogressLabel(id,value){
	let $progressLabel = $('#'+id);
    $progressLabel.html(value);
}

function scrolldown(id){
	document.body.scrollTop = '999999';
	$("[id$=scolldown]").scrollTop(999999);
}


// get center position
function limbasSetCenterPos(el){
	if(el.offsetWidth){elwidth = parseInt(el.offsetWidth);}else if (el.style.width){elwidth = el.style.width}else {elwidth = 0;}
	if(el.offsetHeight){elheight = parseInt(el.offsetHeight);}else if (el.style.height){elheight = el.style.height}else {elheight = 0;}
	
	elwidth = parseFloat(elwidth);
	elheight = parseFloat(elheight);

    posx = ((innerWidth-elwidth)/2)+document.body.scrollLeft;
    posy = ((innerHeight-elheight)/2)+document.body.scrollTop;

	
	if(posx<0){posx = 10;}
	if(posy<0){posy = 10;}

	el.style.left = posx+"px";
	el.style.top = posy+"px";

}

/**
 * Display/Hide a waiting symbol
 * @param evt click event, optional
 * @param inlay boolean (false means the whole document will be replaced by wait symbol)
 * @param hide boolean true to hide
 * @param symbol
 */
function limbasWaitsymbol(evt,inlay,hide,symbol){
	
	if(hide){
		$('#limbasWaitsymbol').hide();
		return;
	}

	var lmbWaitSymbol = 'lmbWaitSymbol';
	if(symbol){
	    lmbWaitSymbol = 'symbol';
    }
	
	if(inlay){
		if(el = document.getElementById("limbasWaitsymbol")){
			el.style.display='';
		}else{
			var el = document.createElement('DIV');
			document.body.appendChild(el);
			el.id='limbasWaitsymbol';
			el.style.position='absolute';
			el.style.zIndex=99999;
			el.className = lmbWaitSymbol;
			el.style.display='';
		}
	}else{
		document.body.innerHTML = "<i class='lmbWaitSymbol'></i>";
		el = document.getElementById("lmbWaitSymbol");
	}
	
	if(evt){
		setxypos(evt,el);
	}else{
		limbasSetCenterPos(el);
	}
}

/* --- Phone ----------------------------------- */
function openPhone_(number) {
        ajaxGet(null, "main_dyns.php", "dialNumber", {"phoneNr": number}, "ajaxEvalScript");	
}

function openPhone_skype(number) {
	document.location.href = "skype:echo"+number+"?call";
}

function limbasRoundMath(value) {
  var k = (Math.round(value * 100) / 100).toString();
  k += (k.indexOf('.') == -1)? '.00' : '00';
  return k.substring(0, k.indexOf('.') + 3);
}

function limbasWriteFormValue(form,value)
{
	if(value){
		value = value.split(";");
		for (var i in value){
			if(value[i]){
				fvalue = value[i].split("->");
				eval("var form_el = document."+form+"."+fvalue[0]+";");
				if(fvalue[1] != 'null' && form_el){
					eval("document."+form+"."+fvalue[0]+".value='"+fvalue[1]+"';");
				}
			}
		}
	}
}

function limbasLinkTags(evt,val0,val1,val2,val3,val4,val5,val6,val7,val8,val9){

	if(evt.ctrlKey){
		val6 = 'null';
	}

	limbasWriteFormValue('form1','gtabid->'+val0+';ID->'+val1+';verkn_tabid->'+val2+';verkn_fieldid->'+val3+';verkn_ID->'+val4+';verknpf->'+val5+';action->'+val6+';verkn_showonly->'+val7+';verkn_poolid->'+val8+';form_id->'+val9);

	if(evt.ctrlKey){
		document.form1.verkn_poolid.value = 'reset';
	}

	document.form1.submit();
}

function limbasGlobalIsValueInSelect(selectName,value)
{
	if(document.getElementById(selectName)){
		list = document.getElementById(selectName);
	}
	else if(document.getElementsByName(selectName)){
		list = document.getElementsByName(selectName)[0];
	}
	else
		return false;

	for(i=0;i<list.options.length;i++)
		if(list.options[i].value == value)
			return true;

	return false;
}

function limbasGlobalSetValueInSelect(selectName,value)
{
	if(document.getElementById(selectName)){
		list = document.getElementById(selectName);
	}
	else if(document.getElementsByName(selectName)){
		list = document.getElementsByName(selectName)[0];
	}
	else{
		return false
	}


	for(i=0;i<list.options.length;i++){
		if((list.options[i].value +"").replace(/^\s*|\s*$/g,"") == (value+"").replace(/^\s*|\s*$/g,"")){
			list.selectedIndex =  i;
		}

	}

	return false;
}

function limbasGlobalGetIndexofValueInSelect(selectName,value)
{
	if(document.getElementById(selectName)){
		list = document.getElementById(selectName);
	}else if(document.getElementsByName(selectName)){
		list = document.getElementsByName(selectName)[0];
	}else
		return -1;

	for(i=0;i<list.options.length;i++)
		if(list.options[i].value == value)
			return i;

	return -1;
}

function showAlert(val) {
	//alert(val);
    lmbShowWarningMsg(val);
}

function showAlertXXX(output_msg, title_msg)
{
    if (!title_msg)
        title_msg = 'Info';

    if (!output_msg)
        return;

    output_msg = output_msg.replace('\n','<br>');

    $("<div></div>").html(output_msg).dialog({
        title: title_msg,
        resizable: false,
        modal: true,
        buttons: {
            "Ok": function()
            {
                $( this ).dialog( "destroy" );
            }
        }
    });
}



function ajaxFormToURL(formname,actid){

	var cc = null;
	var e = null;
	var actpartname;
	var actvarname = new Array;
	var query = "";


	if(typeof(formname) == 'object') {
        var formobject = formname;
    }else{
	    var formobject = document.forms[formname];
    }

	if(formobject){

		if(actid){
			actelements = actid.split("&");
			for (var i in actelements){
				actpart = actelements[i].split("=");
				actpartname = actpart[0];
				actvarname[actpartname] = 1;
			}
		}

		ellist = formobject.elements;

		for (var i=0; i<ellist.length; i++)
		{
			// already set in actid
			if(actvarname[ellist[i].name] && ellist[i].type != "radio"){continue;}
			actvarname[ellist[i].name] = 1;
			var elname = ellist[i].name;
			val = '';

			if(ellist[i].type == "checkbox"){
				if(ellist[i].checked == true){
					val = ellist[i].value;
				}else{
					continue;
				}
			}else if(ellist[i].type == "select-multiple"){
				var selname = ellist[i].name;
				for(e=0;e<ellist[i].options.length;e++){
					if(ellist[i].options[e].selected == true){
						query += "&"+selname+'['+e+']' + "=" + encodeURIComponent(ellist[i].options[e].value);

					}
				}
				continue;


			    //if(ellist[i].selected){



			}else if(ellist[i].type == "radio"){

			    if(ellist[i].checked){
				val = ellist[i].value;
			    }else {
				continue;
			    }

			}else{
				val = ellist[i].value;
			}

			val = encodeURIComponent(val);
			query += "&"+ellist[i].name + "=" + val;
		}

		return query;
	}

}


var ajaxGet_evt = null;
var ajaxGet_time = null;
function ajaxGetWait(evt,url,actid,parameters,functionName,formname,wait) {
	ajaxGet_evt = evt;
	params = "ajaxGet('','"+url+"','"+actid+"','"+parameters+"','"+functionName+"','"+formname+"')";
	if (ajaxGet_time) {window.clearTimeout(ajaxGet_time);}
	ajaxGet_time = window.setTimeout(params,wait);
}


/* --- ajaxGet ----------------------------------- */
/**
 * Queries a dyns_xxx function in the backend
 * @param evt object|null Browser-Mouse-Event
 * @param url string The url that is queried ("main_dyns.php?your_get_parameters..."
 * @param actid string The backend function "dyns_"+actid will be executed
 * @param parameters array of element ids Form elements to include in the query
 * @param functionName function|string The function to execute when the query's result is available
 * @param formname string|null The form to include in the query
 * @param tocontainer html-element|null The html element to insert the query's result-html into
 * @param syncron bool If true, the query will not be asynchronous
 * @param nosymbol bool If true, the loading-icon will not be shown during the query
 */
function ajaxGet(evt,url,actid,parameters,functionName,formname,tocontainer,syncron,nosymbol) {

	if(evt){
		setxypos(evt,'lmbAjaxContainer');
	}else if(ajaxGet_evt){
		setxypos(ajaxGet_evt,'lmbAjaxContainer');
	}

	if(nosymbol != 1){
		limbasWaitsymbol(evt,1,0,nosymbol);
		document.body.style.cursor = 'wait';
	}

	//document.body.style.cursor = 'url(wait.png), wait';
	
	
	if(actid){
		var actid_ = new Array;
		actelements = actid.split("&");
		for (var i in actelements){
			actpart = actelements[i].split("=");
			if(i == 0){actid_[i] = actpart[0];}else{
				if(typeof actpart[1] == 'undefined'){continue;}
				actid_[i] = actpart[0]+'='+encodeURIComponent(actpart[1]);
			}
		}
		actid = actid_.join('&');
	}
	

	url += "?actid=" + actid;
	var separator = "";
	var query = "";
	
	if(parameters != null && parameters != 'null'){
		for(intI=0;intI<parameters.length;intI++)
		{
			var value = "";
			var el;
			if(parameters[intI].indexOf(".")>0){
				el = eval(parameters[intI]);
				
				parameters[intI] = parameters[intI].split(".");
				parameters[intI] = parameters[intI][parameters[intI].length-1];
			}else{
				el = document.getElementById(parameters[intI]);
				if(!el){
					el = document.getElementsByName(parameters[intI])[0];
					if(!el)
						continue;
				}
			}
			if(el.type == "checkbox"){
				if(el.checked){
					value = "on";
				}else{
					value = "";
				}
			}else{
				value = el.value;
			}
			
			query += separator + parameters[intI] + "=" + encodeURIComponent(value);
			separator = "&";
			//globalLmbAjax[globalLmbAjaxKey][parameters[intI]] = escape(value);
		}
		//globalLmbAjaxKey++;
		
	}else if(formname){
		query = ajaxFormToURL(formname,actid);
	}

	if(url.length > 1){


		if(window.XMLHttpRequest){
			var xmlhttp = new XMLHttpRequest();
		}else if (window.ActiveXObject) {
			var xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
		}

		if(!syncron){syncron = true;}else{syncron = false;}
		xmlhttp.open("POST", url,syncron);
		xmlhttp.setRequestHeader('Content-Type','application/x-www-form-urlencoded');
		xmlhttp.onreadystatechange = function(){
			if(xmlhttp.readyState == 4){
				if(functionName == undefined){
				}else if(tocontainer){

                    if(typeof functionName === "function") {
                        functionName(xmlhttp.responseText);
                    }else if(functionName){
                        eval(functionName + "(xmlhttp.responseText)");
                    }

					if(typeof(tocontainer) == 'object'){
						tocontainer.innerHTML = xmlhttp.responseText;
					}else{
						var fktn = "ajaxContainerPost(xmlhttp.responseText,'"+tocontainer+"')";
					}
					eval(fktn);
				}else if(functionName=='' || !functionName){
					functionName = 'ajaxShow';
					ajaxShow(xmlhttp.responseText);
				}else if(typeof functionName === "function") {
                    functionName(xmlhttp.responseText);
                } else {
					eval(functionName + "(xmlhttp.responseText)");
				}
				
				if(!nosymbol){
					limbasWaitsymbol(evt,1,1);
					document.body.style.cursor = 'default';
				}
			}else if(xmlhttp.readyState == 2 || xmlhttp.readyState == 3){
				
				if(!nosymbol){
					limbasWaitsymbol(evt,1,1);
					document.body.style.cursor = 'default';
				}
			}
		};
		xmlhttp.send(query);
	}

}

/* --- ajaxShow ----------------------------------- */
function ajaxShow(value) {
	if(value){
		prompt(value,value);
	}
	return;
//	var el = document.getElementById(dyns_el.name+"l");
//	el.innerHTML = value;
}

/* --- ajaxClose ----------------------------------- */
function ajaxClose(id) {
	var el = document.getElementById(id);
	el.innerHTML = '';
}

/* --- ajax - execute Scripts ----------------------------------- */
function ajaxEvalScript(val)
{ try
  { if(val != '')
    { var script = "";
      val = val.replace(/<script[^>]*>([\s\S]*?)<\/script>/gi, function(){
           if (val !== null) script += arguments[1] + '\n';
           return '';
           });
      if(script) (window.execScript) ? window.execScript(script) : window.setTimeout(script, 0);
    }
    if(script){
    	return true;
    }else{
    	return false;
    }
  }
  catch(e)
  { alert(e)
  }
}





function ajaxContainerPost(string,container)
{
	if(!container) {
        container = 'lmbAjaxContainer';
        limbasDivCloseTab.push(container);
    }

	if(!document.getElementById(container)){
	    $("<div id='"+container+"'></div>").appendTo(document.body);
    }

    var el = document.getElementById(container);

	el.innerHTML = string;
	el.style.visibility='visible';
	el.style.display='';
}

// detect of upload.addEventListener 
navigator.hasxmlProgress= (function(){
	var r= window.XMLHttpRequest && new window.XMLHttpRequest() || '';
	return 'onprogress' in r;
})();



// extend formulars
function lmb_extendForm(form,name,value){
	if(document.getElementById(form)){
		document.getElementById(form).innerHTML = document.getElementById(form).innerHTML + "<input type='hidden' value='" + value + "'>";
	}
}

function limbasGetBoolVal(val){
	if(val == true){return 1;}else{return 0;}
}


function lmb_fileSize(size,round) {
	var sizes = new Array('B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB');
	var ext = sizes[0];
	
	for (i=1; ((i < 7) && (size >= 1024)); i++) {
		size = (size / 1024);
		ext  = sizes[i];
	}
	
	if(round){
		size = Math.round(size);
	}

	return size+ext;
        
}


/**
 * return the multiplier between millisec and the unit in argument
 * @param unit
 */
function getTimeMultiplierForUnit(unit)
{

	var multiplier=1;
	if(unit=='min')
		multiplier = 60000;
	else if(unit=='hour')
		multiplier = 3600000;
	else if(unit=='day')
		multiplier = 24*60*60*1000;
	else if(unit=='week')
		multiplier = 7*24*60*60*1000;
	else if(unit=='month')
		multiplier = 30*24*60*60*1000;
	else if(unit=='year')
		multiplier = 365*24*60*60*1000;

	return multiplier;
}


/**
 * return the number with miinimum 2 digits.
 * especially used for date display
 */
function set2digit(number)
{if(number<10) return "0" + number; else return number; }



/**
 * set a reminder date using a unit (min,hour,day,week,month,year) and an gap from now
 *
 * depend on : set2digit ; getTimeMultiplierForUnit
 *
 */
function setReminderDateTime(REMINDER_VALUE,REMINDER_UNIT,REMINDER_DATE_TIME)
{
	var unit=document.getElementsByName(REMINDER_UNIT)[0].value;
	var value=document.getElementsByName(REMINDER_VALUE)[0].value;
	var multiplier = getTimeMultiplierForUnit(unit);
	var reminderDate = new Date();

	reminderDate.setTime(Date.parse(reminderDate.toString()) + multiplier*value);

	strDate = set2digit(reminderDate.getDate()) + "." + set2digit(reminderDate.getMonth() + 1) + "." + reminderDate.getFullYear() + " " + set2digit(reminderDate.getHours()) + ":" + set2digit(reminderDate.getMinutes());

	document.getElementsByName(REMINDER_DATE_TIME)[0].value = strDate;

}


/**
 * set  gap  using a reminder date and a unit (min,hour,day,week,month,year) from now
 *
 * depend on : getTimeMultiplierForUnit
 *
 */
function setReminderValue(REMINDER_DATE_TIME,REMINDER_VALUE,REMINDER_UNIT)
{
	var unit=document.getElementsByName(REMINDER_UNIT)[0].value;
	var strDateWieder = document.getElementsByName(REMINDER_DATE_TIME)[0].value;
	var Jahr= strDateWieder.split(" ")[0].split(".")[2];
	var Monat= strDateWieder.split(" ")[0].split(".")[1] - 1;
	var Tag= strDateWieder.split(" ")[0].split(".")[0];
	var Stunden= strDateWieder.split(" ")[1].split(":")[0];
	var Minuten= strDateWieder.split(" ")[1].split(":")[1];

	var dateWieder = new Date(Jahr, Monat, Tag, Stunden, Minuten,0);


	var delta = Date.parse(dateWieder.toString()) - (new Date()).getTime();

	var multiplier = getTimeMultiplierForUnit(unit);

	var value = Math.round(delta/multiplier);

	document.getElementsByName(REMINDER_VALUE)[0].value= value;

}

function limbasResizeTextArea(t, minRows, minCols) {
	if(!t.value){return;}

	t.style.height = '';
	t.rows = minRows;
	//t.setAttribute("wrap", "off");
	//t.style.overflow = "auto";
	lines = t.value.split("\n");
	if (arguments.length > 2) {
		t.cols = minCols;
		maxChars = lines[0].length;
		for(i = 1; i < lines.length; i++) {
			currentLength = lines[i].length;
			if (currentLength > maxChars) maxChars = currentLength;
		}
		t.cols = Math.max(maxChars, minCols);
	}
	t.rows = Math.max(lines.length + 1, minRows);
}

function limbasGetDate(typ) {
	if (!typ || typ === 5) {
		typ = "H:i:s";
	}

	const today = new Date();
	const addLeadingZero = function(number) {
		if (number < 10) {
			return "0" + number;
		}
		return number;
	};

	// placeholder -> function returning actual date/time value
	// corresp. to http://php.net/manual/de/function.date.php
	const placeholders = {
        F: function(date) { return date.toLocaleDateString(navigator.language, {month: "long"}); },
        H: function(date) { return addLeadingZero(date.getHours()); },
        Y: function(date) { return date.getFullYear(); },
        d: function(date) { return addLeadingZero(date.getDate()); },
        i: function(date) { return addLeadingZero(date.getMinutes()); },
        j: function(date) { return date.getDate(); },
        l: function(date) { return date.toLocaleDateString(navigator.language, {weekday: "long"}); },
        m: function(date) { return addLeadingZero(date.getMonth() + 1); },
        s: function(date) { return addLeadingZero(date.getSeconds()); },
        y: function(date) { return date.getFullYear().toString().substring(2); }
	};

	// replace format string with date/time values
	var dateTimeString = typ;
	for (var formatChar in placeholders) {
        dateTimeString = dateTimeString.replace(formatChar, placeholders[formatChar](today));
	}
	return dateTimeString;
}

function limbasParseDate(datestring,format) {
	
	var ds = datestring.replace(/[^0-9]/g, "-");
	var ds = ds.replace(/[-]{1,}/g, "-");
	var dp = ds.split("-");
	
	if(isNaN(dp[3])){dp[3] = '00';}
	if(isNaN(dp[4])){dp[4] = '00';}
	if(isNaN(dp[5])){dp[5] = '00';}

	//de d-m-Y H:i:s
	if(format == 1){
		var y = dp[2];
		var m = dp[1];
		var d = dp[0];
		var h = dp[3];
		var mi = dp[4];
		var s = dp[5];
	// us Y-m-d H:i:s
	}else if(format == 2){
		var y = dp[0];
		var m = dp[1];
		var d = dp[2];
		var h = dp[3];
		var mi = dp[4];
		var s = dp[5];
	//fr m-d-Y H:i:s
	}else if(format == 3){
		var y = dp[2];
		var m = dp[0];
		var d = dp[1];
		var h = dp[3];
		var mi = dp[4];
		var s = dp[5];
	}
	
	nd = new Date(y, (m-1), d, h, mi, s);
	if(String(nd).indexOf('Invalid') != -1){
		return false;
	}
	
	return nd;
}
	
function limbasParseTimestamp(stamp,format,time) {

	var t = new Date(stamp * 1000);
    var y = t.getFullYear();
    var m = t.getMonth()+1;
    var d = t.getDate();
    var h = t.getHours();
    var i = t.getMinutes();
    var s = t.getSeconds();

	//de d-m-Y H:i:s
	if(format == 1){
	    if(1) {
            return d+"-"+m+"-"+y;
        }else{
	        return d+"-"+m+"-"+y+" "+h+":"+i+":"+s;
        }
    // us Y-m-d H:i:s
	}else if(format == 2){
	    if(1) {
            return y+"-"+m+"-"+d;
        }else{
	        return y+"-"+m+"-"+d+" "+h+":"+i+":"+s;
        }
    // fr m-d-Y H:i:s
	}else if(format == 3){
	    if(1) {
            return m+"-"+d+"-"+y;
        }else{
	        return m+"-"+d+"-"+y+" "+h+":"+i+":"+s;
        }
    }


}



/** 
 *  Datepicker
 *  
 *  event
 *  showsat  - show calendar at  (element or id)
 *  formname - fromular of datestring (element or id)
 *  value - preselected date
 *  dateformat - default 'dd.mm.yy HH:mm'
 *  left - add to y position 
 *  selected - event function after selecting a date (functionname) 
 *  
 */
function lmb_datepicker(event, showsat, formname, value, dateformat, left, selected, extendOptions) {
    //activ_menu=1;
    var showsecond = false;
    var timeformat = '';

    var pos = dateformat.indexOf(':');
    if (pos > 0) {
        timeformat = dateformat.substr(pos - 2, 8);
        dateformat = dateformat.substr(0, pos - 3);
    }

    if (timeformat.length === 8) {
        showsecond = true;
    }

    var sel = null;
    if (typeof(showsat) === 'object') {
        sel = showsat;
    } else if (showsat) {
        sel = document.getElementById(showsat);
    }

    // set to width of contextmenu
    if (!left) {
        left = $(sel).outerWidth();
    }

    if (typeof(formname) === 'object') {
        cal_inputel = formname;
    } else if (formname) {
        if (document.getElementsByName(formname)[0]) {
            cal_inputel = document.getElementsByName(formname)[0];
        }
        if (document.getElementById(formname)) {
            cal_inputel = document.getElementById(formname);
        }
    } else {
        //cal_inputel = sel.parentNode.firstChild;
        cal_inputel = $(sel).siblings('input:first').get(0);
    }

    if (!extendOptions) {
        extendOptions = {};
    }

    var elid = $(cal_inputel).attr('id');
    $(cal_inputel).attr('id', elid + '_cal');

    // overwrite vor/zurück texts
    $.datepicker.regional['de'].prevText = '';
    $.datepicker.regional['de'].nextText = '';
    $.datepicker.setDefaults($.datepicker.regional['de']);

    // fixes beforeShow not being called
    // (from https://stackoverflow.com/questions/3961963/beforeshow-event-not-firing-on-jqueryui-datepicker)
    $.extend($.datepicker, {
        _inlineDatepicker2: $.datepicker._inlineDatepicker,
        _inlineDatepicker: function (target, inst) {
            this._inlineDatepicker2(target, inst);

            var beforeShow = $.datepicker._get(inst, 'beforeShow');
            if (beforeShow) {
                beforeShow.apply(target, [target, inst]);
            }
        }
    });

    if (timeformat) {
        $(cal_inputel).datetimepicker($.extend({
            changeMonth: true,
            changeYear: true,
            dateFormat: dateformat,
            timeFormat: timeformat,
            showSecond: showsecond,
            controlType: 'select',
            beforeShow: function (input, inst) {
            	if (sel) {
                    $.datepicker._pos = $.datepicker._findPos(input); //this is the default position
                    $.datepicker._pos[0] = findPosX(sel, 1) + left; //left
                    $.datepicker._pos[1] = findPosY(sel, 1); //top
                }
            },
            onClose: function () {
                $(this).attr('id', $(this).attr('id').replace(/_cal/g, ''));
                $(this).datepicker("destroy");
            },
            onSelect: function (datestr, obj) {
                if ((cal_inputel.onchange !== null) && (cal_inputel.onchange !== undefined)) {
                    cal_inputel.onchange();
                }
                $(cal_inputel).change();
                eval('fnc = window.' + selected);
                if (fnc && typeof fnc == 'function') {
                    fnc(datestr, obj);
                }
            }
        }, extendOptions));

        try {
            $(cal_inputel).datetimepicker("show");
        } catch (e) {
        }
    } else {
        $(cal_inputel).datepicker($.extend({
            changeMonth: true,
            changeYear: true,
            dateFormat: dateformat,
            showButtonPanel: true,
            beforeShow: function (input, inst) {
                if (sel) {
                    $.datepicker._pos = $.datepicker._findPos(input); //this is the default position
                    $.datepicker._pos[0] = findPosX(sel, 1) + left; //left
                    $.datepicker._pos[1] = findPosY(sel, 1); //top
                }
            },
            onClose: function () {
                $(this).attr('id', $(this).attr('id').replace(/_cal/g, ''));
                $(this).datepicker("destroy");
            },
            onSelect: function (datestr, obj) {
                if ((cal_inputel.onchange !== null) && (cal_inputel.onchange !== undefined)) {
                    cal_inputel.onchange();
                }
                $(cal_inputel).change();
                eval('fnc = window.' + selected);
                if (fnc && typeof fnc == 'function') {
                    fnc(datestr, obj);
                }
            }
        }, extendOptions));
        try {
            $(cal_inputel).datepicker("show");
        } catch (e) {
        }
    }
    $('#ui-datepicker-div').css("z-index", 99999);
    $("#ui-datepicker-div").click(function () {
        activ_menu = 1;
    });
}
	

/**
 * function set and reset opacity after click
 */
function limbasSetOpacity(el) {

	if(el.style.opacity || el.style.filter){
		el.style.opacity = "";
		el.style.filter = "";
	}else{
		el.style.opacity = "0.3";
		el.style.filter = "Alpha(opacity=30)";
	}
}



/**
 * function that return the X position
 */
function findPosX(obj,abs)
{
	var offset = $(obj).offset();
	return offset.left;
	
//	var curleft = 0;
//	if(obj.offsetParent)
//	{
//		while(obj.offsetParent)
//		{
//			curleft += obj.offsetLeft;
//			obj = obj.offsetParent;
//			if(obj.style.position == 'relative' && !abs){
//				break;
//			}
//		}
//	}
//	else if(obj.x){
//		curleft += obj.x;
//	}
//	return curleft;
}

/**
 * function that return the Y position
 */
function findPosY(obj,abs)
{
	var offset = $(obj).offset();
	return offset.top;


//	var curtop = 0;
//	if(obj.offsetParent)
//	{
//		{
//			curtop += obj.offsetTop;
//			obj = obj.offsetParent;
//			if(obj.style.position == 'relative' && !abs){
//				break;
//			}
//		}
//	}else if(obj.y){
//		curtop +=obj.y;
//	}
//

	//if(document.getElementById("GtabTableBody") && document.getElementById("GtabTableBody").scrollTop){
	//	curtop = (curtop - document.getElementById("GtabTableBody").scrollTop);
	//}
	
	//return curtop;
}

function limbasGetElementHeight(el) {
	//if (ns4) {
	//	var elem = getObjNN4(document, Elem);
	//	return elem.clip.height;
	//} else {
		if(document.getElementById) {
			var elem = document.getElementById(el);
		} else if (document.all){
			var elem = document.all[el];
		}
		xPos = el.offsetHeight;
		return xPos;
	//}
}

function limbasGetElementWidth(el) {
	//if (ns4) {
	//	var elem = getObjNN4(document, Elem);
	//	return elem.clip.width;
	//} else {
		//if (op5) {
		//	xPos = elem.style.pixelWidth;
		//} else {

		xPos = el.offsetWidth;
		//}
		return xPos;
	//}
}


function resizeElementStep(elname,direction,formelnt,step){
	
	if(!step){
		step = 50;
	}
	
	var el = document.getElementById(elname);
	var acsize = el.offsetWidth;

	var newsize = '';
	if (isNaN(step)){
		newsize = step;
	}else{
		if(direction === 'right'){
			newsize = acsize + step;
		}	
		if(direction === 'left'){
			newsize = acsize - step;
		}
	}
	el.style.width = newsize;

	if(formelnt){
		$(formelnt).val(newsize).trigger('change');
	}
}


// sets layout tabs to new class
function limbasSetLayoutClassTabs(el,oldclass,newclass) {

	var rowel = el.parentNode;
	var len = rowel.cells.length;
	for (var r = 0; r < len; r++){
		var cell = rowel.cells[r];
		if(cell.className.indexOf('lmbGtabTabSpace') < 0 && cell.className.substr(0,16) != 'tabpoolItemSpace' && cell.className.substr(0,20) != 'lmbMenuItemspaceTop2'){
			if(el != cell){
				cell.className = oldclass;
			}else{
				cell.className = newclass;
			}
		}
	}
}

function limbasSetElementAtDeltaElement(curEl,element,deltaX,deltaY)
{
	var el = document.getElementById(element);
	if(el==null)
		el = document.getElementByName(element)[0];

	if(el==null)
		return false;

	el.style.top = findPosY(curEl) + deltaY;
	el.style.left = findPosX(curEl) + deltaX;
}

// table used for the menu management
var limbasDivCloseTab = new Array();

// close all menu under the menu designed with this id as parent
function limbasDivClose(parent){

	validEnter = false;
	if(parent=='' || parent==null){
		while(limbasDivCloseTab.length>0){
			divToClose = limbasDivCloseTab[limbasDivCloseTab.length-1];
			//document.getElementById(divToClose).style.visibility = "hidden";
			document.getElementById(divToClose).style.display = "none";
			limbasDivCloseTab.pop();
		}
	}else{
		var id=0;
		var closeThemAll = false;
        if ((typeof parent === 'string') && $('#'+parent).is(':hidden')) {
			closeThemAll = true; // if parent isn't visible, call might come from a button (, not a context menu entry) -> close all other open menus
        }
        var popNumber = 0;
		for(i=0;i<limbasDivCloseTab.length;i++){
			divToClose = limbasDivCloseTab[i];
			if(closeThemAll){
				divToClose = limbasDivCloseTab[i];
				//if(divToClose!="menu"){document.getElementById(divToClose).style.visibility = "hidden";}  //nicht für form menus
				if(divToClose!="menu"){document.getElementById(divToClose).style.display = "none";}  //nicht für form menus
				popNumber++;
			}else if(limbasDivCloseTab[i]==parent){
				closeThemAll = true;
			}
		}

		while(popNumber){
			popNumber--;
			limbasDivCloseTab.pop();
		}

		/*while(limbasDivCloseTab.length>id+1)
		{
			divToClose = limbasDivCloseTab[limbasDivCloseTab.length-1];
			document.getElementById(divToClose).style.visibility = "hidden";
			limbasDivCloseTab.pop();
		}*/
	}

}

function limbasGetParentMenuId(elId){
	for(i=limbasDivCloseTab.length;i>0;i--){
		if(limbasDivCloseTab[i] == elId){
			return limbasDivCloseTab[i-1];
		}
	}
}


function limbasDivHide(el,elname)
{
	if(el){
		el.style.display='none';
	}else if(elname){
		document.getElementById(elname).style.display='none';
	}
}

var lmb_progressbar_el;
function lmb_progressbar(evt,w){
	if(!w){
		var el = document.createElement('DIV');
		el.style.position='absolute';
		document.body.appendChild(el);
		lmb_progressbar_el = el;
		if(evt){
			setxypos(evt,el);
		}else{
			limbasSetCenterPos(el);
		}
		w = 5;
	}

	el = lmb_progressbar_el;
	el.innerHTML = "<div style=\"height:12px;width:150px;background-color:white\"><div style=\"background-color:green;height:100%;width:"+w+"%\"></div></div>";
	var w = w + 5;
	if(w > 100){w = 1;}
	
	setTimeout("lmb_progressbar('',"+w+")",500);
}

// --- Plaziere DIV-Element auf Cursorposition -----------------------------------
function setxypos(evt,el) {
	if(typeof(el)!="object"){
		el = document.getElementById(el);
	}

	if(typeof(el)=="object" && evt.pageX) {
        $(el).css("left",evt.pageX - 5);
        $(el).css("top",evt.pageY - 5);
    }

}


// --- get scrollposition -----------------------------------
function limbasGetScroll(scroll){
	isscroll = new Array;
	isscroll[0] = 0;
	isscroll[1] = 0;
	if(typeof(scroll)=="object"){
		isscroll[1] = scroll.scrollTop;
		isscroll[0] = scroll.scrollLeft;
	}else if(document.getElementById(scroll)){
		isscroll[1] = document.getElementById(scroll).scrollTop;
		isscroll[0] = document.getElementById(scroll).scrollLeft;
	}
	return isscroll;
}


var validEnter = false;
/**
 * this function display a hierachy of div
 * if there is no parent then all the element displayed with this function are closed. Then parent is not specified
 * @param {(string|object)} el either an object or an id of an element on which the new element will be displayed below or the location splited with ;
 * @param {(string|object)} parent the parent of the new element in the div hierarchy. Either the name or the element itself for top floating menu use event
 * @param {string} child the id of the new element to display
 * @param {string} slide slide position relative to XxY
 * @param {boolean} display use display instead visibility
 * @param {boolean} abs force absolute position (ignoring relative parent elements) to find XY position
 * @param {boolean} center center element to window
 * @param {boolean} puttotop cut and paste child to body
*/
function limbasDivShow(el,parent,child,slide,display,abs,center,puttotop)
{

    if((!el && !parent) && child && document.getElementById(child)){
        document.getElementById(child).style.display ='';
        limbasDivCloseTab.push(child);
        return;
    }

    if(!parent && el && (typeof(el) !== 'string' || el.indexOf(';') < 0)) {
        parent = $(el).closest('.lmbContextMenu').attr('id');
    }
	
	let childElement = document.getElementById(child);
	let elementWidth =  limbasGetElementWidth(el);

    validEnter = false;
	activ_menu=1;
	if(el || parent){limbasDivClose(parent); }

	// replace all visibility to display
	if(childElement.style.visibility == 'hidden'){
		childElement.style.display ='none';
		childElement.style.visibility ='visible';
	}

	let $child = $('#' + child);
        
    var parel;
	if(parent != "" && parent != null)
	{
		//!="string")
		if(typeof(parent)=="string")
		{
			/*
			let posX = findPosX(el,abs);
			let childWidth = childWidth;
			let elWidth = limbasGetElementWidth(el);
			let documentWidth = $( document ).width();
			
			let left = posX + elWidth;
			if(posX + childWidth > documentWidth) {
				//let overflow = (posX + childWidth) - documentWidth;
				left = posX - childWidth;
			}
			
			
			$child.css('left',left);
			$child.css('top',findPosY(el,abs));
			 */
			$child.css('left',findPosX(el,abs) + elementWidth);
			$child.css('top',findPosY(el,abs));

			parel = document.getElementById(parent);
			if(!parel && document.getElementsByName(parent)[0]){
				parel = document.getElementsByName(parent)[0];
			}
			childElement.style.zIndex = (parseInt(parel.style.zIndex) + 1);
		//!="object")
		}else if(typeof(parent)=="object" && parent.pageX){
            // is event
            setxypos(parent, child);

		}else{
			setxypos(parent,child);
			parel = document.getElementById(parent);
			if(!parel && document.getElementsByName(parent)[0]){
				parel = document.getElementsByName(parent)[0];
			}
			childElement.style.zIndex = (parseInt(parel.style.zIndex) + 1);
		}
	}
	else
	{
		//isscroll = limbasGetScroll(scroll);
		let xPos, yPos;
		
		if(child!="menu"){
			if(typeof(el)=="string"){
				if(el.indexOf(";")>0){
					xPos = el.split(";")[0];
					yPos = el.split(";")[1];
				}else if(el){
					xPos = findPosX(document.getElementById(el),abs);
					yPos = findPosY(document.getElementById(el),abs) + limbasGetElementHeight(document.getElementById(el));
				}
			}else if(el){
				xPos = findPosX(el,abs);
				yPos = findPosY(el,abs) + limbasGetElementHeight(el);
			}
		}

		$child.css('left',xPos);
		$child.css('top',yPos);
	}
	
	// slide position
	if(slide){
		slide = slide.split('x');
		childElement.style.left = parseFloat(childElement.style.left) + parseFloat(slide[0]);
		childElement.style.top = parseFloat(childElement.style.top) + parseFloat(slide[1]);
	}
	
	
	limbasDivCloseTab.push(child);
	childel = childElement;

	// not used yet
//	if(puttotop){
//		childElement.style.display ='';
//		offsettop = findPosY(childElement,abs,1);
//		offsetleft = findPosX(childElement,abs,1);
//		$('body').append($(childElement));
//		childElement.style.display = 'none';
//		childElement.style.top = offsettop;
//		childElement.style.left = offsetleft;
//	}

	// measure size of document before opening child
	var oldWidth = $(document).width();
	var oldHeight = $(document).height();

	$child.css('visibility', 'hidden');
	$child.show(50,function (){
		let childWidth = $child.width();

		if(
			(parel && !center && oldWidth < $(document).width()) ||
			(parel && !center && oldHeight < $(document).height())
		) {
			
			
			// functionality to open child on the left of parent if too less space
			// TODO doesnt work if typeof(parent)=="object"
			let offset;

			if(parel && !center && oldWidth < $(document).width()) {

				let parElOffsetLeft = findPosX(el,abs);

				// move it by its width to the left of the parent
				offset = $child.offset();
				let initialLeft = offset.left;
				offset.left = parElOffsetLeft - childWidth - 8; // 8px to get overlap effect
				$child.offset(offset);

				// do it twice to also include width changes due to first movement
				childWidth = $child.width();
				offset.left = parElOffsetLeft - childWidth - 8;
				$child.offset(offset);

				// prevent element to move outside the window to the left
				if (offset.left < 5) {
					offset.left = initialLeft;
					$child.offset(offset);
				}

			}

			// move to top
			if(parel && !center && oldHeight < $(document).height()) {

				// move it by its height upwards, maintain baseline with parent contextmenu entry
				offset = $child.offset();
				offset.top -= $child.outerHeight(true) - limbasGetElementHeight(typeof(el)=="string" ? document.getElementById(el) : el);
				offset.top = Math.max(5, offset.top); // prevent element to move outside the window upwards
				$child.offset(offset);

				// TODO maybe do it twice here, too
			}

		}
		
		//$child.css("z-index", '');
		$child.css('visibility', 'visible');
		
		
		
		

		// open with animation
		if(center){
			limbasSetCenterPos(childel);
		}

		//childElement.style.visibility = "visible";
		//if(center){limbasSetCenterPos(childElement);}
		//if(display){childElement.style.display ='';}

		
		
	});

	// filter context menu on key press
	$(document)
		.off('keypress.limbasDivShow') /* remove existing events to prevent handler from being attached twice */
		.on('keypress.limbasDivShow', function(e) {
			// dont add key if we are typing into an input/select
			if (!e.target || $(e.target).is('input:not(:disabled),select:not(:disabled),textarea:not(:disabled,[readonly])'))
				return;

			if (e.keyCode === 13)
				return;

			// abort if no context menu available
			if (!childel)
				return;

			// abort if context menu is hidden
			const contextMenu =  $(childel);
			if (contextMenu.is(':hidden'))
				return;

			// prepend new row for filter input (if not exists)
			var filterRow = contextMenu.children('.lmbFilterRow');
			var filterInput = filterRow.find('input');
			if (!filterRow.length) {
				filterInput = $('<input type="text" class="contextmenu lmbFilterRowInput" value="">');
				filterRow = $('<div class="lmbContextRow lmbFilterRow" title="Filter"><i class="lmbContextLeft lmb-icon lmb-search"></i><span class="lmbContextItemIcon"></span></div>');
				filterRow.children().last().append(filterInput);
				const filterSeparator = $('<div class="lmbContextRowSeparator"><div class="lmbContextRowSeparatorLine">&nbsp;</div></div>');
				contextMenu.prepend(filterRow, filterSeparator);

				// listen for change of input
				filterInput
					.keyup(function(e) {
						// remove on escape or empty
						if (e.keyCode === 27 /* escape */ || this.value === '') {
							lmbFilterContextMenu(contextMenu, null);
							const filterRow = $(this).closest('.lmbFilterRow');
							const separator = filterRow.next();
							filterRow.remove();
							separator.remove();
							return;
						}

						const shownElements = lmbFilterContextMenu(contextMenu, this.value);
						if (e.keyCode === 13 /* enter */ && shownElements.length === 1) {
							filterInput.blur();
							shownElements.click();
						}
					});
			}

			// set value immediately, stop input event from being propagated by focus change (led to undefined behavior)
			// set focus to directly redirect future key inputs
			e.preventDefault();
			e.stopPropagation();
			e.stopImmediatePropagation();
			filterInput.val(filterInput.val() + e.key).focus();
		});
	
}

/**
 * Filters the context menu s.t. only rows containing the searchString are shown
 * @param contextMenu jQuery element representing the contextMenu (.lmbContextMenu)
 * @param searchString string to filter for, null to reset filter
 * @returns the remaining elements (which are still shown)
 */
function lmbFilterContextMenu(contextMenu, searchString) {
    const elementsToIgnore = contextMenu.children().slice(0, 2); /* remove filter row and separator from set */

    // reset filters
    contextMenu.find('.lmb-table-search-hide').removeClass('lmb-table-search-hide');

    if (searchString === null)
        return;

    // apply filter
    const searchStringLower = searchString.toLowerCase();
    const elementsToFilter = contextMenu
        .find('.lmbContextRow,.lmbContextLink')
        .not(elementsToIgnore);

    const hiddenElements = elementsToFilter
		.filter(function() {
			return $(this).text().toLowerCase().indexOf(searchStringLower) === -1; /* doesnt contain search string */
		})
		.addClass('lmb-table-search-hide');

    return elementsToFilter.not(hiddenElements);
}


function lmbSetContextActive(el,parent){

    $($('#'+parent).find('i').detach()).appendTo($(el));


}



//----------------- Schließe alle Context-Menüs bei Klick auf Hintergrund -------------------
function body_click(){
	window.setTimeout("divclose()",100);
}

/**
 *
 * DICTIONARY FUNCTION
 *
 */
{
	function limbasDictionaryAdd() {

		for (var c=0; c < limbasDictionaryAdd.arguments.length; c+=2) {
			this[limbasDictionaryAdd.arguments[c]] = limbasDictionaryAdd.arguments[c+1];
			this.MD5limbasKeys[this.count++]=limbasDictionaryAdd.arguments[c];
		}
	}

	function limbasDictionaryRemove(strKeyName) {
		for (c=0; c < limbasDictionaryRemove.arguments.length; c++) {
			this[limbasDictionaryRemove.arguments[c]] = null;
		}
		this.count --;
	}

	function limbasDictionaryGet(strKeyName) {
	    return(this[strKeyName]);
	  }

	  function limbasDictionaryGetKeys() {
	  	return this.MD5limbasKeys;
	  }


	  function limbasDictionarySize(strKeyName) {
	    return(this.length);
	  }


	function limbasDictionary() {
		this.count=0;
		this.add = limbasDictionaryAdd;
		this.get = limbasDictionaryGet;
		this.getKeys = limbasDictionaryGetKeys;
		this.remove = limbasDictionaryRemove;
		this.MD5limbasKeys = new Array();

		for (var c=0; c < limbasDictionary.arguments.length; c+=2) {
			this.add(limbasDictionary.arguments[c],limbasDictionary.arguments[c+1])
		}
	}
}


var lmbGlobVar = new Array();
function lmbSetGlobVar(key,value) {
	lmbGlobVar[key] = value;
}



//  form to array
function lmb_getFormData(form_array){
  
    var indexed_array = {};
    var mergeArray = {};
    var unindexed_array = {};
    var a = 0;
    
    for (var i = 0; i < form_array.length; i++) {
		if(form_array[i]){
		    unindexed_array = $('#'+form_array[i]).serializeArray();
		    $.map(unindexed_array, function(n, i){
		    	
		    	// array / multiselect
		    	if(n['name'].substr(n['name'].length-2, 2) == '[]'){
		    		n['name'] = n['name'].substr(0,n['name'].length-2) + '[' + a + ']';
		    		a++;
		    	}else{
		    		a = 0;
		    	}
		    	
		    	indexed_array[n['name']] = n['value'];
		    });
		    mergeArray = $.extend(mergeArray, indexed_array);
		}
    }

    return mergeArray;
}





// deprecated - USE encodeURIComponent instead !!!!
// function limbasURLEncode(value){}







function encode_utf8(value) {
	// dient der Normalisierung des Zeilenumbruchs
	value = value.replace(/\r\n/g,"\n");
	var utftext = "";
	for(var n=0; n<value.length; n++)
	{
		// ermitteln des Unicodes des  aktuellen Zeichens
		var c=value.charCodeAt(n);
		// alle Zeichen von 0-127 => 1byte
		if (c<128)
		utftext += String.fromCharCode(c);
		// alle Zeichen von 127 bis 2047 => 2byte
		else if((c>127) && (c<2048)) {
			utftext += String.fromCharCode((c>>6)|192);
			utftext += String.fromCharCode((c&63)|128);}
			// alle Zeichen von 2048 bis 66536 => 3byte
			else {
				utftext += String.fromCharCode((c>>12)|224);
				utftext += String.fromCharCode(((c>>6)&63)|128);
				utftext += String.fromCharCode((c&63)|128);}
	}
	return utftext;
}


function decode_utf8(value) {
	var plaintext = ""; var i=0; var c=c1=c2=0;
	// while-Schleife, weil einige Zeichen uebersprungen werden
	while(i<value.length)
	{
		c = value.charCodeAt(i);
		if (c<128) {
			plaintext += String.fromCharCode(c);
			i++;
		}else if((c>191) && (c<224)) {
			c2 = value.charCodeAt(i+1);
			
			// ÄÖÜ Sonderfall - wird nicht von Funktion unterstützt
			if(c2 == 339){plaintext += String.fromCharCode(220);}
			else if(c2 == 8211){plaintext += String.fromCharCode(214);}
			else if(c2 == 8222){plaintext += String.fromCharCode(196);}
			else{
				plaintext += String.fromCharCode(((c&31)<<6) | (c2&63));
			}
			i+=2;
		}else {
			c2 = value.charCodeAt(i+1); c3 = value.charCodeAt(i+2);
			plaintext += String.fromCharCode(((c&15)<<12) | ((c2&63)<<6) | (c3&63));
			i+=3;
		}
	}
	return plaintext;
}

function radioValue(rObj) {
	for (var i=0; i<rObj.length; i++) if (rObj[i].checked) return rObj[i].value;
	return false;
}


// set layer height to window size
function lmb_setAutoHeight(el,offset,reset){

   offset = (typeof offset !== 'undefined') ? offset : 0;

   if(reset && el){
       $(el).height('');
   }else{
		window.focus();
		if($(el) && $(el).css('overflow-y') == 'auto'){
			var winHeight = getWindowHeight();

			var elHeight = $(el).height(); // height of el
			var elOffsetTop = $(el).position().top; // space between start of document and el

			// hide (possibly huge) context menues before calculating document height
			limbasDivCloseTab.forEach(function(id) { $('#' + id).hide(); });
            const docHeight = $(document).height();
            limbasDivCloseTab.forEach(function(id) { $('#' + id).show(); });

            var elOffsetBottom = docHeight - elOffsetTop - elHeight; // space between el and end of document

			var elNewHeight = winHeight - elOffsetTop - elOffsetBottom - offset;

			if(elOffsetTop + elHeight + elOffsetBottom > winHeight) {
				$(el).css('max-height', elNewHeight + 'px');
			}

			return elHeight;

		}
	}
}

function lmb_setAutoHeightCalendar(el, offset, reset) {
        offset = (typeof offset !== 'undefined') ? offset : 0;

        if(reset && el){
		$(el).height('');
	}else{
		window.focus();
		if($(el) && $(el).css('overflow-y') == 'auto'){
                        // set white background to fit window
                        var background = $(el).parents('.lmbfringegtab').first();                        
                        var bgMarginY = background.outerHeight(true) - background.outerHeight();
                        background.outerHeight(getWindowHeight() - bgMarginY);
            
                        // set element to fit background
                        var bgPaddingTop = (background.innerHeight() - background.height()) / 2;
                        var elOffsetTop = $(el).position().top - background.position().top; // space between background and el   
                        var elNewHeight = background.height() - elOffsetTop + bgPaddingTop - offset;
                        $(el).height(elNewHeight);
                        return elNewHeight;
		}
	}
}

// for use in lmb_setAutoHeight[Calendar]()
function getWindowHeight() {

    return window.innerHeight
        || document.documentElement.clientHeight
        || document.body.clientHeight;

}

function toggleHeight(el,maxHeight,maxWidth) {
    el = $('#'+el);

    if(!maxHeight){maxHeight = 100;}
    if(!maxWidth){maxWidth = 300;}

    // toggle height
    const actualHeight = el.get(0).scrollHeight+10;
    const actualWidth = el.get(0).scrollWidth+10;
    if (el.attr('data-lmb-opened')) {
        el.animate({height: maxHeight + "px",width: maxWidth + "px"});
        el.removeAttr('data-lmb-opened');
    } else {
        el.animate({height: actualHeight + "px",width: actualWidth + "px"});
        el.attr('data-lmb-opened', true);
    }
}

function array_unique(arrayName) {
	var newArray = new Array();
	label:for(var i=0; i<arrayName.length;i++ ) {
		for(var j=0; j<newArray.length;j++ ) {
			if(newArray[j] == arrayName[i])
			continue label;
		}
		newArray[newArray.length] = arrayName[i];
	}
	return newArray;
}


function lmbSuggestColor(rgb){
	
    var o = Math.round(((parseInt(rgb[0]) * 299) + (parseInt(rgb[1]) * 587) + (parseInt(rgb[2]) * 114)) /1000);
    
    if(o > 125) {
        return 'black';
    }else{ 
        return 'white';
    }
}


// ################### user selection #######################

var lmb_hirarrules='';

// usefunction : function to post result of userselect
// parentel : limbasDivShow parameter
// container : limbasDivShow parameter
// parameter : free parameters seperate with ","
function lmbAjax_showUserGroups(el,gtabid,ID,usefunction,parentel,parameter){
	actid = "showUserGroups&gtabid=" + gtabid + "&ID=" + ID + "&usefunction=" + usefunction + "&parameter=" + parameter;
	mainfunc = function(result){lmbAjax_showUserGroupsPost(result,el,parentel);};
	ajaxGet(null,"main_dyns.php",actid,null,"mainfunc");
}

function lmbAjax_showUserGroupsPost(result,el,parentel){
	limbasDivShow(el,parentel,"lmbAjaxContainer");
	document.getElementById("lmbAjaxContainer").innerHTML = result;
}

function lmbAjax_showUserGroupsSearch(evt,el,value,ID,gtabid,fieldid,usefunction,prefix,typ,parameter){
	actid = "showUserGroupsSearch&gtabid=" + gtabid + "&fieldid=" + fieldid + "&ID=" + ID + "&value=" +value+ "&usefunction=" + usefunction + "&prefix=" + prefix + "&typ=" + typ + "&parameter=" + parameter;
	mainfunc = function(result){lmbAjax_showUserGroupsSearchPost(evt,el,result,typ,usefunction,prefix);};
	ajaxGet(null,"main_dyns.php",actid,null,"mainfunc");
}

function lmbAjax_showUserGroupsSearchPost(evt,el,result,typ,usefunction,prefix){
	$('#'+prefix+usefunction+'user').hide();
    $('#'+prefix+usefunction+'group').hide();
    const ajaxContainerID = prefix+usefunction+typ;
    const container = $('#'+ajaxContainerID);
    container.html(result);
    limbasDivShow(null, null, ajaxContainerID);
}

// gtab user rules
var limbasAjaxGtabRulesContainer;
function limbasAjaxGtabRules(gtabid,use_records,parameter,container){
	if(container){limbasAjaxGtabRulesContainer = container;}
	actid = "showGtabRules&gtabid=" + gtabid + "&use_records=" + encodeURIComponent(use_records) + "&parameter=" + parameter;
	ajaxGet(null,"main_dyns.php",actid,null,"limbasAjaxGtabRulesPost");
}


function limbasAjaxGtabRulesPost(result){
	document.getElementById(limbasAjaxGtabRulesContainer).innerHTML = result;
}

var dyns_time = null;
var limbasAjaxGtabRulesUsersearchAct = null;
function limbasAjaxGtabRulesUsersearch(searchvalue,gtabid,use_records){
	limbasAjaxGtabRulesUsersearchAct = "showGtabRulesUsersearch&searchvalue=" + searchvalue + "&gtabid=" + gtabid + "&use_records=" + use_records;
	if (dyns_time) {window.clearTimeout(dyns_time);}
	dyns_time = window.setTimeout("limbasAjaxGtabRulesUsersearchGet()",400);

}
function limbasAjaxGtabRulesUsersearchGet(searchvalue,gtabid,use_records){
	ajaxGet(null,"main_dyns.php",limbasAjaxGtabRulesUsersearchAct,null,"limbasAjaxGtabRulesUsersearchPost");
}


function limbasAjaxGtabRulesUsersearchPost(result){
	document.getElementById("ContainerGtabRulesGroupsearch").style.visibility = 'hidden';
	document.getElementById("ContainerGtabRulesUsersearch").style.visibility = 'visible';
	document.getElementById("ContainerGtabRulesUsersearch").innerHTML = result;
	limbasDivCloseTab.push('ContainerGtabRulesGroupsearch');
	limbasDivCloseTab.push('ContainerGtabRulesUsersearch');
}

function limbasAjaxGtabRulesGroupsearch(searchvalue,gtabid,use_records){
	actid = "showGtabRulesGroupsearch&searchvalue=" + searchvalue + "&gtabid=" + gtabid + "&use_records=" + use_records;
	ajaxGet(null,"main_dyns.php",actid,null,"limbasAjaxGtabRulesGroupsearchPost");
}

function limbasAjaxGtabRulesGroupsearchPost(result){
	document.getElementById("ContainerGtabRulesUsersearch").style.visibility = 'hidden';
	document.getElementById("ContainerGtabRulesGroupsearch").style.visibility = 'visible';
	document.getElementById("ContainerGtabRulesGroupsearch").innerHTML = result;
	limbasDivCloseTab.push('ContainerGtabRulesGroupsearch');
	limbasDivCloseTab.push('ContainerGtabRulesUsersearch');
}

/**
 * Replaces values in arr1 with values from arr2 at same index (if set)
 * Modifies arr1
 * @param arr1
 * @param arr2
 */
function arrayReplace(arr1, arr2) {
    if (!arr2) {
        return;
    }
    for (var i = 0; i < arr1.length; i++) {
        if (arr2[i]) {
            arr1[i] = arr2[i];
        }
    }
}

function isInIframe () {
    try {
        return window.self !== window.top;
    } catch (e) {
        return true;
    }
}


function MultitemantChangeNotice () {

    $modal = showFullPageModal(jsvar['lng_2968'], '<div class="alert alert-info">'+jsvar['lng_3172']+'</div>', 'sm','<button type="button" class="btn btn-dark" data-bs-dismiss="modal" onclick="window.location=window.location.href.split(\'?\')[0]">'+jsvar['lng_844']+'</button>');
    //window.location = window.location.href.split("?")[0];

}

/**
 *
 * @param title
 * @param body
 * @param className
 * @param footer
 * @return {jQuery|HTMLElement|*|null}
 */
function showFullPageModal(title,body,className='fullscreen', footer = null) {

    let $modal;
    let $modalBody;
    let $modalTitle;
    let $modalFooter;
	let $modalFooterDefault;
    
    if (isInIframe()) {
        $modal = window.top.$("#general-main-modal");
        $modalBody = window.top.$("#general-main-modal-body");
        $modalTitle = window.top.$("#general-main-modal-title");
        $modalFooter = window.top.$("#general-main-modal-footer");
		$modalFooterDefault = window.top.$("#general-main-modal-footer-default");
	} else {
        $modal = $("#general-main-modal");
        $modalBody = $("#general-main-modal-body");
        $modalTitle = $("#general-main-modal-title");
        $modalFooter = $("#general-main-modal-footer");
		$modalFooterDefault = $("#general-main-modal-footer-default");
	}

    if ($modal.length <= 0) {
    	return null;
	}

	$modal.find('.modal-dialog').addClass('modal-' + className);

    if (footer) {
        $modalFooter.html(footer).show();
		$modalFooterDefault.hide();
    }
	else {
		$modalFooter.hide();
		$modalFooterDefault.show();
	}
	
    $modalTitle.html(title);
    $modalBody.html(body);
    $modal.modal('show');
    return $modal;
}

function hideFullPageModal() {
	let $modal;
	if (isInIframe()) {
		$modal = window.top.$("#general-main-modal");
	} else {
		$modal = $("#general-main-modal");
	}
	if ($modal.length <= 0) {
		return false;
	}
	$modal.modal('hide');
	return true;
}



// Load decrypted value on demand
function lmbAjax_decryptField(){
	let $this = $(this);
	let url = 'main_dyns.php';
	
	let gtabid = $this.data('gtabid');
	let fieldid = $this.data('fieldid');
	let id = $this.data('decrypt');
	let mode = $this.data('mode');
	let dparams = encodeURIComponent($this.data('dparams'));
	
	let actid = 'lmbDecryptField&gtabid=' + gtabid + '&fieldid=' + fieldid + '&ID=' + id + '&mode=' + mode + '&dparams=' + dparams;
	
	ajaxGet(null,url,actid,null,function(result){
		$this.replaceWith(result);
	});
}


function lmbShowToast(type, msg) {

	let $toast;
	let $toastBody;
	
	if(type !== 'success' && type !== 'warning' && type !== 'error') {
		return false;
	}

	if (isInIframe()) {
		$toast = window.top.$('#toast-' + type);
		$toastBody = window.top.$('#toast-' + type + '-body');
	} else {
		$toast = $('#toast-' + type);
		$toastBody = $('#toast-' + type + '-body');
	}

	if ($toast.length <= 0) {
		return false;
	}


	$toast.closest('.toast-container').css('z-index',99999);
	$toastBody.text(msg);
	$toast.toast('show');
	
	return true;
	
}

function lmbShowSuccessMsg(msg) {
	lmbShowToast('success', msg);
}

function lmbShowWarningMsg(msg) {
	lmbShowToast('warning', msg);
}

function lmbShowErrorMsg(msg) {
	lmbShowToast('error', msg);
}

function confirmDelete($element, callback) {
    
    const instanceExists = $element.data('bs.popover.exists');    
    
    if(instanceExists === undefined) {
        $element.popover({
            html: true,
            sanitize: false,
            trigger: 'click focus',
            content: '<p class="font-weight-bold text-danger">Confirm delete</p>' +
                '<button type="button" class="btn btn-sm btn-black pr-2 btn-cancel-delete">Cancel</button>' +
                '<button type="button" class="btn btn-sm btn-danger btn-confirm-delete">Delete</button>',
            placement: 'top'
        }).on('inserted.bs.popover', function () {
            let $this = $(this);
            let $popover = $('#'+$this.attr('aria-describedby'));

            $popover.find('.btn-cancel-delete').on('click',function(){
                $this.popover('hide');
            });

            $popover.find('.btn-confirm-delete').on('click',function(){
                $this.popover('hide');
                callback($element);
            });

            $element.data('bs.popover.exists', true);
        }).popover('show');
    }
    
    
}


function lmbShowProgress(showHide, progressValue = null, progressBase = null, msg= null) {

	let $modal;
	let $spinner;
	let $progressBar;
	let $messageHolder;

	if (isInIframe()) {
		$modal = window.top.$('#progress-modal');
		$spinner = window.top.$('#progress-modal-spinner');
		$progressBar = window.top.$('#progress-modal-progress');
		$messageHolder = window.top.$('#progress-modal-msg');
	} else {
		$modal = $('#progress-modal');
		$spinner = $('#progress-modal-spinner');
		$progressBar = $('#progress-modal-progress');
		$messageHolder = $('#progress-modal-msg');
	}

	if($modal.length <= 0) {
		return;
	}
	
	if(showHide) {
		$modal.modal('show');
	}
	else {
		$modal.modal('hide');
		return;
	}
	
	
	let text = jsvar['lng_3153'] + '... ' + jsvar['lng_3154'];
	
	if(progressBase !== null && progressValue !== null) {
		
		if(progressValue >= progressBase) {
			$modal.modal('hide');
			return;
		}
		
		$spinner.addClass('d-none');
		$progressBar.removeClass('d-none');
		
		const currentValue = Math.floor((progressValue / progressBase) * 100);
		$progressBar.find('.progress-bar').css('width', currentValue + '%');
		
		text = progressValue + ' / ' + progressBase;
		
	}
	else {
		$spinner.removeClass('d-none');
		$progressBar.addClass('d-none');
	}
	
	if(msg !== null) {
		text = msg;
	}

	$messageHolder.text(text);
}