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

//browserType();

// Ajax Preview
function lmbAjax_resultPreView(evt,gtabid,gfieldid,ID){
	var url = "main_dyns.php";
	var actid = "gresultPreView&gtabid=" + gtabid + "&gfieldid=" + gfieldid + "&ID=" + ID;
	dynfunc = function(result){lmbAjax_resultPreViewPost(result,evt);};
	ajaxGet(null,url,actid,null,"dynfunc");
}

// Ajax Preview output
function lmbAjax_resultPreViewPost(result,evt){
	if(result){
		document.getElementById("lmbAjaxContainer").innerHTML = result;
		limbasDivShow('',evt,'lmbAjaxContainer');
	}
}

// Ajax post formelements
function lmbAjax_postHistoryResult(form,sync){
	var url = "main_dyns.php";
	var actid = "postHistoryFields";
	ajaxGet(null,url,actid,null,"lmbAjax_postHistoryResultPost",form,null,sync);
}

// Ajax post formelements output
function lmbAjax_postHistoryResultPost(result){
	if(result){
		ajaxEvalScript(result);
		document.form1.history_fields.value='';
	}
}

// Ajax color select
function limbasAjxColorSelect(evt,formname,gtabid,fieldid,id){
	url = "main_dyns.php";
	actid = "colorSelect&formname="+formname+"&gtabid="+gtabid+"&fieldid="+fieldid+"&id="+id+"&container=lmbAjaxContainer";
	mainfunc = function(result){limbasAjxColorSelectPost(result,evt);};
	ajaxGet(null,url,actid,null,"mainfunc");
}
// Ajax color select output
function limbasAjxColorSelectPost(result,evt){
	document.getElementById("lmbAjaxContainer").innerHTML = result;
	limbasDivShow('',evt,"lmbAjaxContainer");
}
// Ajax color select action
function limbasColorSelectSet(val,formname,gtabid,fieldid,id){
	document.getElementById(formname).value = val;
	document.getElementById(formname).style.backgroundColor = val;
	document.getElementById(formname).onchange();
}

/* --- dynsPress ----------------------------------- */
function lmbAjax_dynsearch(evt,el,actid,fname,par1,par2,par3,par4,par5,par6,gformid,formid,datatype,nextpage) {
	if(evt === 'object'){
	if (evt.keyCode == 27) {
		var el = document.getElementById(el.name+"l");
		el.innerHTML = '';
		return;
	}}
	
	if(!par5){par5 = '';}
	if(!par6){par6 = '';}
	if(!formid){formid = '';}
	if(!gformid){gformid = '';}
	if(!nextpage){nextpage = '';}

	if(!el){
		el = document.getElementsByName(fname+"_ds")[0];
	}

	if(typeof evt === 'string') {
	    dyns_value = evt;
    }else{
        dyns_value = el.value;
    }

	if(dyns_value.length > 1 || dyns_value == '*' || nextpage){
		url = "main_dyns.php";
		actid = actid+"&form_name="+fname+"&form_value="+dyns_value+"&par1="+par1+"&par2="+par2+"&par3="+par3+"&par4="+par4+"&par5="+par5+"&par6="+par6+"&gformid="+gformid+"&formid="+formid+"&nextpage="+nextpage;
		mainfunc = function(result){lmbAjax_dynsearchPost(result,el);};
		ajaxGetWait(null,url,actid,null,"mainfunc",'',500);
	}else if(dyns_value == ''){ // drop relation for ajax relation fields if empty
		// ajax select
		if(datatype == '12'){
			var d = '';
		// relation
		}else if(datatype == '32'){
			var d = '#L#d'+document.getElementById(fname).value;
		// relation
		}else{
			var d = '#L#delete';
		}
		document.getElementById(fname).value=d;
		checktyp(24,fname,'',par4,par3,'',par5);
	}
	oMultipleSelectLoader = new MultipleSelectShow("oMultipleSelectLoader");
}

/* --- dynsShow ----------------------------------- */
function lmbAjax_dynsearchPost(value,el) {
	//if(document.getElementById("lmbAjaxContainer").style.display == 'none' || document.getElementById("lmbAjaxContainer").style.visibility == 'hidden'){
		limbasDivShow(el,null,'lmbAjaxContainer','','',1);
                activ_menu = 0;
	//}
	document.getElementById("lmbAjaxContainer").innerHTML = value;
}

/* --- dynsClose ----------------------------------- */
function dynsClose(id) {
	var el = document.getElementById(id);
	hide_selects(2);
	el.innerHTML = '';
	$('#lmbAjaxContainer').hide();
}

// Ajax extended relation
function LmExt_RelationFields(el,gtabid,gfieldid,viewmode,edittype,ID,orderfield,relationid,ExtAction,ExtValue,gformid,formid,ajaxpost,event){

    if(!gformid){
        gformid = $('#extRelationFieldsTab_'+gtabid+"_"+gfieldid).attr( "data-gformid" );
    }

	lmbCollReplaceSource = null;
	if(ExtAction == "showall"){
		var tel = document.getElementById("extRelationFieldsTab_"+gtabid+"_"+gfieldid);
		if(!gformid) {
                        if(tel.style.height == '110px'){
                                tel.style.height = '';
                                tel.style.overflow = 'visible';
                                relationid = '1';
                        }else{
                                tel.style.height = '110px';
                                tel.style.overflow = 'auto';
                                relationid = '0';
                        }
                }
	}else if(ExtAction == "delete" || ExtAction == "unlink" || ExtAction == "copy"){
		if(ExtAction == "delete"){var mess = jsvar['lng_2153'];}
		else if (ExtAction == "unlink") {var mess = jsvar['lng_2359'];}
		else if (ExtAction == "copy") {var mess = jsvar['lng_2156'];}
		if(!relationid){
			var actrows = checkActiveRows(ExtValue);
		}else{
			var actrows = new Array(relationid);
		}
		ExtValue = '';
		if(actrows.length > 0){
			if(!confirm(mess+' ('+actrows.length+')')){return;}
			var relationid = actrows.join(",");
		}else {alert(jsvar["lng_2083"]);return;}
	}else if(ExtAction == "replace"){
		lmbCollReplaceSource = Array(gtabid, gfieldid, ID, gformid, formid);
		limbasCollectiveReplace(event,el,0,ExtValue);
		return;
	}else if(ExtAction == "unlinkall"){
	    if(!confirm(jsvar['lng_2776'])){return;}
	}
	
	var url = "main_dyns.php";
	
	// ajax based POST of complete formular
	if(ajaxpost){
		document.getElementById("myExtForms").innerHTML = "<input type='hidden' name='gfieldid' value='"+gfieldid+"'><input type='hidden' name='viewmode' value='"+viewmode+"'><input type='hidden' name='ExtAction' value='"+ExtAction+"'><input type='hidden' name='ExtValue' value='"+ExtValue+"'><input type='hidden' name='edittype' value='"+edittype+"'><input type='hidden' name='ID' value='"+ID+"'><input type='hidden' name='orderfield' value='"+orderfield+"'><input type='hidden' name='relationid' value='"+relationid+"'><input type='hidden' name='gformid' value='"+gformid+"'><input type='hidden' name='formid' value='"+formid+"'>";
		var actid = "extRelationFields&gtabid="+gtabid+"&gfieldid="+gfieldid+"&ID="+ID; // overwrite form
		dynfunc = function(result){LmExt_RelationFieldsPost(result,gtabid,gfieldid);};
		ajaxGet(null,url,actid,null,"dynfunc","form1");
		document.getElementById("myExtForms").innerHTML = "";
	}else{

		if(el){
			var text = el.innerHTML;
		}
	
        // use text of child span .lmbContextItemIcon if the context menu functions were used
        var contextChildren = $(el).children('.lmbContextItemIcon');
        if(contextChildren.length > 0) {
        	text = contextChildren.first().html();
        }
            
		// no ajax update (userdefined formular) - use formid as formname
		if(formid && isNaN(formid)){
			var formElement = document.getElementsByName(formid)[0];
			if (formElement) {
				formElement.value = relationid;
                document.getElementById(formid+"_ds").value = text;
            }
		// no ajax update (ajax based select)
		// detail
        }else if(document.getElementById("g_"+gtabid+"_"+gfieldid+"_ds") && el && !document.getElementById("extRelationFieldsTab_"+gtabid+"_"+gfieldid)){
			document.getElementsByName("g_"+gtabid+"_"+gfieldid)[0].value = relationid;
			checktyp('27','g_'+gtabid+'_'+gfieldid,'',gfieldid,gtabid,relationid,ID);
			document.getElementById("g_"+gtabid+"_"+gfieldid+"_ds").value = text;
			if(document.getElementById("g_"+gtabid+"_"+gfieldid+"_ds").onchange){document.getElementById("g_"+gtabid+"_"+gfieldid+"_ds").onchange();}
		// tablelist
        }else if(document.getElementById("g_"+gtabid+"_"+gfieldid+"_"+ID+"_ds") && el && !document.getElementById("extRelationFieldsTab_"+gtabid+"_"+gfieldid)){
			document.getElementsByName("g_"+gtabid+"_"+gfieldid+"_"+ID)[0].value = relationid;
			checktyp('27','g_'+gtabid+'_'+gfieldid,'',gfieldid,gtabid,relationid,ID);
			document.getElementById("g_"+gtabid+"_"+gfieldid+"_"+ID+"_ds").value = text;
			if(document.getElementById("g_"+gtabid+"_"+gfieldid+"_"+ID+"_ds").onchange){document.getElementById("g_"+gtabid+"_"+gfieldid+"_"+ID+"_ds").onchange();}
			document.getElementById("g_"+gtabid+"_"+gfieldid+"_"+ID).click();
		// ajax update GET of single relation
		}else{
			var actid = "extRelationFields&gtabid=" + gtabid + "&gfieldid=" + gfieldid + "&viewmode=" + viewmode + "&edittype=" + edittype +  "&ID=" + ID + "&orderfield=" + orderfield + "&relationid=" + relationid + "&ExtAction=" + ExtAction +"&ExtValue=" + ExtValue + "&gformid=" + gformid+ "&formid=" + formid;
			dynfunc = function(result){LmExt_RelationFieldsPost(result,gtabid,gfieldid,viewmode,el,ID);};
			ajaxGet(null,url,actid,null,"dynfunc");
		}
	}

	if(ExtAction == "showall"){return;}


	if((ExtAction == "link" || ExtAction == "unlink" || ExtAction == "unlinkall") && !(event && event.shiftKey)){
		document.getElementById('lmbAjaxContainer').style.display='none';
	}
}


// Ajax extended relation output
function LmExt_RelationFieldsPost(result,gtabid,gfieldid,viewmode,textel,ID){
        //console.log(gtabid, gfieldid, viewmode, textel, ID);
	
	selected_rows = new Array();
	var el = gtabid+"_"+gfieldid;
	if(document.getElementById("extRelationFieldsTab_"+el)){
		document.getElementById("extRelationFieldsTab_"+el).innerHTML = result;
	// for detailview
	}else if(document.getElementById("g_"+gtabid+"_"+gfieldid+"_ds")){
		el = document.getElementById("g_"+gtabid+"_"+gfieldid+"_ds");
		el.value = result;
		if(el.onchange){el.onchange();}
	// for tablelist
	}else if(document.getElementById("g_"+gtabid+"_"+gfieldid+"_"+ID+"_ds")){
		el = document.getElementById("g_"+gtabid+"_"+gfieldid+"_"+ID+"_ds");
		el.value = result;
		if(el.onchange){el.onchange();}
	}
	ajaxEvalScript(result);
	
	var iconel = document.getElementById("extRelationFieldsPIC_"+gtabid+"_"+gfieldid+"_"+viewmode);
	
	if(iconel){
                if(iconel.style.display == 'none'){
                        iconel.style.display = '';
                        textel.style.color = 'green';
                        textel.className = 'lmbContextItemIcon';
                }else{
                        iconel.style.display = 'none';
                        textel.style.color = '';
                        textel.className = 'lmbContextItem';
                }
	}
}

function lmbAjax_multilang(el,gtabid,gfieldid,ID){
	actid = "actid=gmultilang&gtabid=" + gtabid + "&field_id=" + gfieldid + "&ID="+ID;
	$.ajax({
		type: "GET",
		url: "main_dyns.php",
		async: false,
		data: actid,
		success: function(data){
			$('<div>').html('<div id="lmb_multilang">'+data+'</div>').children('div').css('width', '100%').css({'position':'relative','left':'0','top':'0','width':'100%'}).dialog({
				appendTo: "#form1",
                resizable: true,
				modal: true,
				width:'600',
				title: jsvar['lng_2897'],
				close: function() {
					$(this).dialog('destroy').remove();
				}
			});
		}
	});
}

function fs_check_all(check){

	var cblist = $('.fs_checkbox');
	if(check){
		cblist.prop("checked", true);
	}else{
		cblist.prop("checked", false);
	}
}

// --------------- multiselect/attribute detail -----------------
function lmbAjax_multiSelect(change){
	
	var i = 0;
	var e = 0;
	var fs_sel = new Array();
	var msradio = $('input[name=msrd]');
	
	if(change){
	
		// single select
		if(msradio.attr("name")){

			var checkedValue = msradio.filter(':checked').val();
			var msid = msradio.filter(':checked').attr("elid");
			var gtabid = document.form_fs.gtabid.value;
			var fieldid = document.form_fs.field_id.value;
			var ID = document.form_fs.ID.value;
	
			if(document.getElementById("g_"+gtabid+"_"+fieldid)){
				form_name = "g_"+gtabid+"_"+fieldid;
			}else if(document.getElementById("g_"+gtabid+"_"+fieldid+"_"+ID)){
				form_name = "g_"+gtabid+"_"+fieldid+"_"+ID;
			}else{
				return;
			}

			var sel_element = document.getElementById(form_name);
			var sel_element_ = sel_element;
			var checkedValue_ = checkedValue;
			var is_multiple = 0;

			// is select multiple
			if(document.getElementsByName(form_name+'[]')[0]){
				sel_element_ = document.getElementsByName(form_name+'[]')[0];
				checkedValue_ = msid;
				var is_multiple = 1;
			}

			// add new select element if not exists
			if ($(sel_element_).is("select") && $(sel_element_).find('option[value=' + checkedValue_ + ']').length == 0){
				sel_element_.options[sel_element_.options.length]= new Option(checkedValue, checkedValue_);
			}

			// ajax based single select
			if(document.getElementById(form_name+'_ds')){
				document.getElementById(form_name+'_ds').value = checkedValue;
				sel_element.value = checkedValue;
			// select multiple
			}else if(is_multiple){
				sel_element_.value = msid;
			}else{
				sel_element.value = checkedValue;
			}
			
			if(sel_element.onchange){
				sel_element.onchange();
			}else{
				checktyp('12',form_name,'Anrede',fieldid,gtabid,checkedValue,ID,'');
			}

			// save changed values
			if(document.form_fs.change_id.value){
				ajaxGet(null,'main_dyns.php','multipleSelect',null,null,'form_fs',null,1);
			}
			
			$('#lmb_multiSelect').dialog('destroy').remove();
			
			return;

		// multiselect/attribute
		}else{

			var gse = new Array();
			var cblist = $('.fs_checkbox');
			cblist.each(function( index ) {

				if($( this ).prop("checked") && $( this ).attr("active")){
					return;
				}else if(!$( this ).prop("checked") && !$( this ).attr("active")){
					return;
				}else if($( this ).prop("checked") && !$( this ).attr("active")){
					var c = 1;
				}else if(!$( this ).prop("checked") && $( this ).attr("active")){
					var c = 0;
				}
		
				if(c){
					fs_sel[i] = 'a'+$( this ).attr("elid");
				}else{
					fs_sel[i] = 'd'+$( this ).attr("elid");
				}

				i++;
				
			});
			
			var gtabid = document.form_fs.gtabid.value;
			var fieldid = document.form_fs.field_id.value;
			var select_cut = document.form_fs.select_cut.value;
		
		}
	
	}
	
	if(fs_sel.length > 0){
		document.form_fs.fs_sel.value = fs_sel.join(";");
	}
	
	ajaxGet(null,'main_dyns.php','multipleSelect',null,'lmbAjax_multiSelectPost','form_fs');

}

function lmbAjax_multiSelectPost(result){
	ajaxEvalScript(result);
	document.getElementById("lmb_multiSelect").innerHTML = result;
}


// multipleselect - edit details
function lmbAjax_multibleSelect(el,gtabid,gfieldid,ID){
	actid = "actid=multipleSelect&gtabid=" + gtabid + "&field_id=" + gfieldid + "&ID="+ID;
	$.ajax({
		type: "GET",
		url: "main_dyns.php",
		async: false,
		data: actid,
		success: function(data){
			$('<div id="lmb_multiSelect">'+data+'</div>').dialog({
				resizable: true,
				modal: true,
				width:'480',
				title: el.title,
                close: function() {
					// refresh tablelist for gtab_erg
					if(document.form1.action.value=='gtab_erg'){
						send_form(1,2);
					}else{
						// reload LmExt_RelationFields
						var extrelid = $(el).parents("[id^='extRelationFieldsTab']").attr("id");
						if(extrelid){
							extrelid = extrelid.split('_');
							document.getElementById("extRelationFieldsReload_"+extrelid[1]+"_"+extrelid[2]).click();
						}
					}
					
					$(this).dialog('destroy').remove();
				}
			});
		}
	});
}



/* --- select / multiselect / attribute - actions ----------------------------------- */
function dyns_11_a(evt,el_value,el_id,form_name,fieldid,gtabid,ID,dash,attribute,action,level,form_elid) {

	// contextmenu
	var contextel = form_name+'_dsl';
	if(!level){
		level = '';
	}

	if(el_id){
		// attribute add / delete
		if(action === "a" || action === "c"){

            var el = evt.currentTarget;
            var is_active = $(el).attr('is_active');
            if(is_active){
                $(el).find("td:first").html("");
                $(el).attr('is_active','')
                document.form1[form_name].value = "d" + el_id;
            }else{
                $(el).find("td:first").html("<i class='lmb-icon lmb-check'></i>");
                $(el).attr('is_active','1')
                document.form1[form_name].value = "a" + el_id;
            }

            if (!evt.ctrlKey){
                dynsClose(contextel);
            }

			checktyp('32','',form_name,fieldid,gtabid,' ',ID);
            ajaxGet(null,'main_dyns.php','11&gtabid='+gtabid+'&fieldid='+fieldid+'&form_elid='+form_elid+'&ID='+ID,null,function(result) { $('#' + form_name + '_dse').html(result).show();if(typeof tagAttr_initSize==='function') {tagAttr_initSize();}},'form1');
			document.form1[form_name].value = '';
			return;

		// select
		}else if(action === "b"){
			dynsClose(contextel);
            $('#' + form_name + '_ds').val(el_value);
            $('#' + form_name).val(el_value);
		// multiple select ajax
		}else if(action === "c"){
            if (!evt.ctrlKey){
                dynsClose(contextel);
            }
			const el = $('#' + form_name + '_dse');
			var htmlToInsert = '<span style="color:green;vertical-align:middle;">' + el_value + '</span>';
			if (dash) {
				const upperDash = dash.toUpperCase();
				if (upperDash === '<OL>' || upperDash === '<UL>' || upperDash === '<LI>') {
                    htmlToInsert = '<li>' + htmlToInsert + '</li>';
				} else {
                    htmlToInsert += dash;
				}
			}
			el.prepend(htmlToInsert);
			document.form1[form_name].value += ";" + el_id;
			activ_menu = 1;
		// attribute modify
		}else if(action === "v"){
			document.form1[form_name].value += ";v";
		// load multiple select
		}else if(action === "e"){
			oMultipleSelectLoader.load(evt,el_value,el_id,form_name,fieldid,gtabid,ID,dash,attribute,action,level);
			activ_menu=1;
			return false;
		// multiple select ajax - unique
		}else if(action === "f"){
			dynsClose(contextel);
			$('#' + form_name + '_ds').val(el_value);
			$('#' + form_name).val(el_id);
		}
	}
	checktyp('32','',form_name,fieldid,gtabid,' ',ID);
}


/* --- dyns_11_a delete ----------------------------------- */
function dyns_11_a_delete(evt,el,ID,gtabid,fieldid,w_id,d_id,form_name,select_cut){

    // e = delete with select_d ID
    // d = delete with select_w ID

    var sep = 'd';
    if(d_id){
        var w_id = d_id;
        sep = 'e';
    }

	var val = document.form1[form_name].value;
    var color, textDecoration;
    var d = val.indexOf(sep);
    var ids = val.split(";");
    var elIndex = ids.indexOf(sep+w_id);

    // first empty field
    if(d == -1){
        document.form1[form_name].value = '';
        ids = new Array();
    }

    if (elIndex < 0) {
        // element excluded -> include
        ids.push(sep+w_id);
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

    checktyp('32','',form_name,fieldid,gtabid,' ',ID);

}


function value_in(search_in,search_on){
	var y = 0;
	var tmp = search_in.split(";");
	if(tmp && tmp.length>0){for(y=0;y<tmp.length;y++){if(search_on==tmp[y]) return true;}}
	return false;
}

/*---------------- Tabulator-Element für Formular anzeigen ---------------------*/
var lmbTabulatorKey = new Array();
function lmbSwitchTabulator(el,mainel,elkey,vert){

    if(vert){
        var hel = el.parentNode.parentNode.parentNode;
        var stpos = vert;
    }else{
        var hel = el.parentNode;
        var stpos = "";
    }

    if(!hel){return;}
    for(var i=0; i<hel.childNodes.length; i++){
        if(vert){
            divel =  hel.childNodes[i].firstChild.firstChild;
        }else{
            divel =  hel.childNodes[i];
        }

        var mt = "LmbTabulatorHeader";
        if(!divel.id || divel.id.substr(0,mt.length)!=mt) continue;
        var dsp = 0;
        var cl = "lmbGtabTabulator"+stpos+"Inactive";
        var nodel = divel.id.replace(mt,"LmbTabulatorItem");
        if(divel.id==el.id){
            dsp = 1;
            cl="lmbGtabTabulator"+stpos+"Active";
        }
        divel.className = cl;
        if(dsp) {
            // $('[id^='+nodel+']').show(); // ??
            $('#'+nodel).show();
        }else{
            $('#'+nodel).hide();
        }
    }

    var tabKey = new Array();
    lmbTabulatorKey[mainel] = elkey;
    var e = 0;
    for (var i in lmbTabulatorKey){
        tabKey[e] = i+"_"+lmbTabulatorKey[i];
        e++;
    }
    document.form1.filter_tabulatorKey.value = tabKey.join(";");
    lmb_initTables();
}








// <div id="lmbAjaxContainer2" class="ajax_container" style="position:absolute;display:none;z-index:999;" onclick="activ_menu=1;"></div>




// Ajax extended relation for tablelist
var lmbRelationDialog = 0;
function LmExt_RelationList(el,gtabid,gfieldid,ID){

	pos = (50*lmbRelationDialog);
	
	lmbRelationDialog++;
	
	actid = "actid=extRelationList&gtabid=" + gtabid + "&gfieldid=" + gfieldid + "&ID="+ID;
	$.ajax({
		type: "GET",
		url: "main_dyns.php",
		async: false,
		data: actid,
		success: function(data){
			//document.getElementById("lmbAjaxContainer2").innerHTML = data;
			//$("#lmbAjaxContainer2 div").css('width', '100%');
			$('<div>').html(data).children('div').css('width', '100%').css({'position':'relative','left':'0','top':'0','width':'100%'}).dialog({
				appendTo: "#form1",
				resizable: true,
				modal: true,
				width:'500',
				title: el.title,
				position: {
					at: "center+"+pos+" center+"+pos, 
					of: window,
				},
				close: function( event, ui ,parentel) {

					//alert(parentel);
					var destel = $(el).parentsUntil("table").parent();
					if(destel){
						var destelid = destel.attr('id').split('_');
						if(destelid[0] == 'extRelationTab'){
							var datid = destel.attr('datid');
							var form_id = '';
							if(form1.form_id){form_id = form1.form_id.value;}
							// refresh relation with post
							LmExt_RelationFields(null,destelid[1],destelid[2],'',1,datid,'','','','',form_id,'',1);
							var isclosed = 1;
						}
					}

					// refresh tablelist for gtab_erg
					if(!isclosed){send_form(1,2);}
					
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
function lmb_relShowField(event,el,elname){

    $($('#'+elname).detach()).appendTo('body');
    limbasDivShow(el,null,elname,'','',1);

	// put ajax container in front of jquery ui
	var ui = $('.ui-front:visible');
	if (ui) {
		var uiZIndex = parseInt(ui.css("z-index"));
		$("#lmbAjaxContainer").css("z-index", uiZIndex + 1);
	}
}

//----------------- neuen Verkn. Datensatz anlegen -------------------
function create_new_verkn(gtabid,vfieldid,vgtabid,ID,tablename,fdimsn,show_relationpath,destformid,formid,inframe,layername) {
	neu = confirm(jsvar["lng_164"]+':'+tablename+'\n'+jsvar["lng_24"]);
	if(neu){
		divclose();
		verknpf = 1;

		// simple table without relation
		if(!gtabid){
			gtabid = vgtabid;
			vgtabid = '';
			ID = '';
			vfieldid = '';
			verknpf = '';
		}

		newwin7('gtab_neu',gtabid,vgtabid,vfieldid,ID,ID,destformid,fdimsn,formid,inframe,show_relationpath);
	}
}

var oMultipleSelectLoader = new MultipleSelectShow("oMultipleSelectLoader");
function MultipleSelectShow(instance_name) {
	this.cache = {};

    this.load = function(evt, el_value, el_id, form_name, fieldid, gtabid, ID, dash, attribute, action, level) {
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
        ajaxGet(null, "main_dyns.php", "11_a_level&page=" + dash + "&gtabid=" + gtabid + "&fieldid=" + fieldid + "&ID=" + ID + "&level=" + el_id + "&form_name=" + form_name + "&form_value=" + el_value, null, function(response) {
            var tmp = response.split("#L#");
            var html = tmp[1];

            parent.after('<tr><td style="width:16px;"></td><td colspan="2">' + html + '</td></tr>');
            self.cache[id] = html;
        });
	};

}

function lmb_updateField(field_id,tab_id,id,changevalue,syntaxcheck,fielddesc,ajaxpost,fieldsize,el,parentage,language) {
	checktyp(syntaxcheck,'g_'+tab_id+'_'+field_id,fielddesc,field_id,tab_id,changevalue,id,ajaxpost,fieldsize,el,parentage,language);
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
function checktyp(data_type,fieldname,fielddesc,field_id,tab_id,obj,id,ajaxpost,fieldsize,el,parentage,language) {
	if(!fielddesc){fielddesc = fieldname;}
    if(!parentage){parentage = '';}
    if(!language){language = '';}

	// no syntax check
	if(!data_type && field_id && field_id){
		document.form1.history_fields.value=document.form1.history_fields.value + tab_id + ',' + field_id + ',' + id + ';';
		var isupdate = 1;
	}

	if(!data_type){obj = '';}
	val = obj;

	var regexp = RULE[data_type];
	if(fieldname.substr(0,2) == 'gs'){
		val = obj.replace(/^(>|<|<=|>=|!=|#NOTNULL#|#NULL#|#NOT#)?( )?/g,'');
	}

	if(fieldsize){
		regexp = regexp.replace(/xxx/g,(parseInt(fieldsize)+1));
		regexp = regexp.replace(/xx/g,fieldsize);
	}else{
        regexp = regexp.replace(/xxx/g,'');
        regexp = regexp.replace(/xx/g,'');
	}

	eval('var reg = /'+regexp+'/;');
	if (obj != ''){
		if (val && !reg.exec(val)){
			newvalue = prompt(jsvar['lng_134']+'\n' + fielddesc +': '+DATA_TYPE_EXP[data_type]+' ('+FORMAT[data_type]+')',obj);
			if(!newvalue){
				limbasDuplicateForm(fieldname,'');
				//eval("document.form1.elements['"+fieldname+"'].value='';");
				document.getElementsByName(fieldname)[0].value = newvalue;
				return;
			}
			if(reg.exec(newvalue)){
				val = newvalue;
				document.getElementsByName(fieldname)[0].value = newvalue;
				//eval("document.form1.elements['"+fieldname+"'].value=newvalue;");
				if(field_id && tab_id){
					if(id){
						document.form1.history_fields.value=document.form1.history_fields.value + tab_id + ',' + field_id + ',' + id + ',' + parentage + ',' + language + ';';
						var isupdate = 1;
					}else{
						document.form1.history_search.value = document.form1.history_search.value + tab_id + ',' + field_id + ';';
						var isupdate = 1;
					}
				}
			}else{
				checktyp(data_type,fieldname,fielddesc,field_id,tab_id,newvalue,id,ajaxpost,fieldsize);
			}
		}else{
			if(field_id && tab_id){
				if(id){
					document.form1.history_fields.value = document.form1.history_fields.value + tab_id + ',' + field_id + ',' + id + ',' + parentage + ',' + language + ';';
					var isupdate = 1;
				}else{
					document.form1.history_search.value = document.form1.history_search.value + tab_id + ',' + field_id + ';';
				}
			}
		}
	}else{
		if(field_id && tab_id){
			if(id){
				document.form1.history_fields.value = document.form1.history_fields.value + tab_id + ',' + field_id + ',' + id + ',' + parentage + ',' + language + ';';
				var isupdate = 1;
			}else{
				document.form1.history_search.value = document.form1.history_search.value + tab_id + ',' + field_id + ';';
				var isupdate = 1;
			}
		}
	}

	if(isupdate){
		limbasDuplicateForm(el,fieldname,val,obj);
		if(!ajaxpost){
		    // submit marker
            $('.submit').addClass( "lmbSbmConfirm" );
        }
	}

	if(isupdate && ajaxpost != 2 && id>0 && data_type != 13 && (ajaxpost == 1 || jsvar["ajaxpost"] == 1)){
		send_form(1,1);
		document.form1.history_fields.value = '';  //???? 0 war vorher; '' ist aber richtig
	}
}


function limbasDuplicateForm(el,name,val){
	// Multicheckbox
	if(el && el.name){
		var ellist = document.getElementsByName(el.name);
	// other Elements
	}else{
		var ellist = document.getElementsByName(name);
	}

	if(ellist.length > 1){
		for(var i = 0; i < ellist.length; i++) {
			var obj = ellist.item(i);
			if(obj == el){continue;}
			if(obj.type == 'checkbox') {
                if (val) {
                    obj.checked = true;
                } else {
                    obj.checked = false;
                }
            }else if(obj.type == 'radio'){
				if(obj.name+'_'+obj.getAttribute("data-index") == el.id) {
                    obj.value = val;
                }
			}else{
				obj.value = val;
			}
		}
	}
}

function needfield(elid){
	var need = new Array();
	var radel = new Array();
	
	function setbg(el){
		el.addClass("gtabBodyDetailNeedVal");
	}
	
	if(typeof elid === 'undefined'){elid = null;}
	
	if(!elid){
		elid = 'form1';
	}

	$('#'+elid+' input, #'+elid+' select, #'+elid+' textarea').each(function(){
		
		if($(this).attr('title') && $(this).attr('title').substr(0,1) == '*' && $(this).attr('id') && ($(this).attr('id').substring(0,2) == 'g_' || $(this).attr('id').substring(0,7) == 'element')){

			if($(this).attr('type') && $(this).attr('type').toLowerCase() == 'hidden'){return true;}
			
			nval = '';
			nname = '';
			ntype = '';
			npos = 0;
			nspell = 'formelement '+$(this).attr('id');

			titleval = $(this).attr('title');
			if($(this).attr('name')){nname = $(this).attr('name');}
			ntype = $(this).prop("tagName").toLowerCase();
			if($(this).attr('type')){ntype = $(this).attr('type').toLowerCase();}
			if($(this).val()){nval = $(this).val();}
			cc = $(this)[0];
			
			$(this).removeClass("gtabBodyDetailNeedVal");
			
			if(titleval){
				npos = titleval.indexOf(':');
				if(npos > 0){nspell = titleval.substring(1,npos)+' ';
				}else{nspell = titleval.substring(1)+' ';}
			}
			
			
			// ajax relation & select
			if(cc.name.substr(cc.name.length-3, 3) == '_ds'){
				nval = document.getElementById(cc.name.substr(0, cc.name.length-3)).value;
				if(nval.substr(0, 3) == '#L#'){nval = '';}
			}

			// inherit select
			if(cc.name.substr(cc.name.length-3, 3) == '_di'){
				nval = cc.value;
			}
			
			if((ntype == 'text' || ntype == 'textarea') && !nval){
				need[need.length] = nspell;
				setbg($(this));
			}
			else if(ntype == 'file' && !document.form1['uploaded_'+nname]){
				need[need.length] = nspell;
				setbg($(this));
			}
			else if(ntype == 'select' && cc.selectedIndex <= 0 && (nval == '' || nval == '#L#delete')){
				need[need.length] = nspell;
				setbg($(this));
			}
			else if(ntype == 'radio' || ntype == 'checkbox'){
				spos = nname.indexOf('[');
				if(spos > 1){nname = nname.substring(0,spos);}
				if(radel[nname] != 0){
					if(cc.checked){radel[nname] = 0;setbg($(this));
					}else{radel[nname] = nspell;}
				}
			}

		}
	});
	
	// Checkboxen und Radiofelder
	for (var z in radel){
		if(radel[z]){
			need[need.length] = radel[z];
		}
	}
	
	if(need.length > 0){
		need = array_unique(need);
		need = need.join("\n");
		return need;
	}
}

// change field-required flag
function lmb_needfieldChange(elid,status) {
	
	var el = document.getElementById(elid);
	if(!el){return;}

	var eltitle = el.title;
	if(eltitle){
		var need = eltitle.substr(0,1);
	}else{
		eltitle = 'input form';
	}
	
	if(status && need == '*'){
		return true;
	}else if (status && need != '*'){
		el.title = '*'+eltitle
	}else if (!status && need == '*'){
		el.title = eltitle.substr(1);
	}else if (!status && need != '*'){
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
 * @param report_printer int printer id, used when output=4
 * @param resolvedTemplateGroups object see TemplateConfig::$resolvedTemplateGroups
 * @param resolvedDynamicData object see TemplateConfig::$resolvedDynamicData
 *
 * @param saveAsTemplate if not empty, the selected configuration will be saved as template with this parameter as name
 * @param callback function callback for receiving the report generation html
 * @param context
 */
function limbasReportMenuHandler(preview,evt,el,gtabid,reportid,ID,output,listmode,report_medium,report_rename,report_printer, resolvedTemplateGroups={}, resolvedDynamicData={}, saveAsTemplate='', callback=null,context='old') {
    activ_menu = 1;

    
    
    let $reportForm;    
    if (context === 'bs4') {
        $reportForm = $('#report_form_bs');
	} else {
        $reportForm = $('#report_form');
	}
    
	//console.log($reportForm);
    
    // get medium from input
    if(!report_medium && $reportForm.length > 0){
        report_medium = $reportForm.find("[name='report_medium']").val();
    }

    let params = {
        gtabid: gtabid,
        report_id: reportid,
        report_output: output,
        report_medium: report_medium,
        report_printer: report_printer,
        resolvedTemplateGroups: !$.isEmptyObject(resolvedTemplateGroups) ? JSON.stringify(resolvedTemplateGroups) : null,
        resolvedDynamicData: !$.isEmptyObject(resolvedDynamicData) ? JSON.stringify(resolvedDynamicData) : null,
		preview: preview,
        context: context,
        saveAsTemplate: saveAsTemplate
    };

    
    if (preview) {
        params.action = 'report_preview';
    }


    let count = 1;
    // selected datasets
    if (!listmode) {
        if (ID && ID !== '0') {
            params.ID = ID;
            count = 1;
        } else {
            let use_record;

            let datid = document.form1.ID.value;
            if (datid > 0) {
            	use_record = ID+"_"+gtabid;
			} else {
                let actrows = checkActiveRows();
                if(actrows.length > 0){
                    use_record = actrows.join(";");
                }
			}
			
			
            if (use_record) {
                params.use_record = use_record;
                document.form1.use_record.value = use_record;
                count = countofActiveRows();
            } else {
                params.use_record = "all";
                const $countInput = $('#GtabResCount');
                if ($countInput.length) {
                    count = $countInput.html();
                } else {
                    count = 'undefined';
                }
            }
        }
        
    }
    
    if (!el && output !== 0 && output != null) {
    	
        // ask user when many datasets selected
        if (!listmode && count && count > 1) {
            if (!confirm(`${jsvar['lng_2676']} ${count}\n${jsvar['lng_51']}`)) {
                activ_menu = 0;
                limbasDivClose();
                return;
            }
        }
        
        
    	let $report_rename = $reportForm.find("input[name='report_rename']");
        if (report_rename == null && $report_rename.length > 0) {
            report_rename = $report_rename.val();
        }
        if (!report_rename) {
            report_rename = '';
        }
        params.report_rename = report_rename;

        limbasWaitsymbol(evt,1);
    }


    const actid = "menuReportOption&" + $.param(params);
    
    if (callback === null) {
        callback = function (result) {
            limbasReportMenuOptionsPost(result, el, output);
        };
	}
    
    ajaxGet(null, "main_dyns.php", actid, null, callback, "form1");
}

/**
 * @param result
 * @param el
 * @param output
 */
function limbasReportMenuOptionsPost(result,el,output){
	// use Scripts

    if (!ajaxEvalScript(result)) {
        $("#limbasDivMenuReportOption").html(result);
        if (el) {
            limbasDivShow(el, 'limbasDivMenuBericht', 'limbasDivMenuReportOption');
        }else{
            limbasWaitsymbol(0,0,1);
        }
    }
    
	
	if(output==2){
		document.getElementById("lmbReportMenuOptionsArchive").innerHTML = "<i class='lmb-icon lmb-aktiv'></i>";
	}else if(output==1 || output==4){
		activ_menu = 0;
		limbasDivClose();
	}
	
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

function limbasReportSaveTemlpate(evt, gtabid, reportid, resolvedTemplateGroups={}) {

	//TODO: language
    let name = prompt('Template Name', '');

    if (name != null) {
        limbasReportMenuHandler(0,evt,null,gtabid,reportid,0,0,0,'pdf','',0, resolvedTemplateGroups, {}, name, function(result){
            alert('Template gespeichert.');
		});
    }
    
}


// Ajax reminder
function limbasDivShowReminder(evt,el,add,remove,changeView,change,defaults) {
        activ_menu = 1;
	if(el){
		limbasDivShow(el,'limbasDivMenuExtras','lmbAjaxContainer');
	}
        
	var use_records = '';
	var verkn = '';
	var gfrist = '';
	var listmode = '';
	if(document.form1.gfrist.value){var gfrist = '&gfrist='+document.form1.gfrist.value;}
	if(document.form1.action.value == 'gtab_erg'){var listmode = 1;}
	
	// listmode
        // TODO auch für change/view?
	if(listmode && (add || remove)){
                listmode = 1;
                var count = countofActiveRows();
                // use selected rows
                if(count > 0){
                        var actrows = checkActiveRows(jsvar['gtabid']);
                        if(actrows.length > 0){
                                var use_records = actrows.join(";");
                        }else{alert(jsvar["lng_2083"]);return;}
                // use filter
                }else{
                        var use_records = 'all';
                        // if relation
                        if(document.form1.verkn_ID){
                                var verkn = '&verkn_tabid='+document.form1.verkn_tabid+'&verkn_fieldid='+document.form1.verkn_fieldid+'&verkn_ID='+document.form1.verkn_ID+'&verkn_showonly='+document.form1.verkn_showonly;
                        }
                        // get count from result
                        if(document.getElementById("GtabResCount")){var count = document.getElementById("GtabResCount").innerHTML;}else{var count = 'undefined'}
                }

                if(count && !confirm(jsvar['lng_2676']+' '+count+'\n'+jsvar['lng_2902'])){
                        activ_menu = 0;
                        limbasDivClose();
                        return;
                }
	}

	if(add){
		ajaxGet(null,'main_dyns.php','showReminder&gtabid='+jsvar['gtabid']+'&ID='+jsvar['ID']+'&listmode='+listmode+gfrist+'&add=1'+verkn+'&use_records='+use_records,null,'limbasDivShowReminderPost','form_reminder');
	}else if(remove){
		ajaxGet(null,'main_dyns.php','showReminder&gtabid='+jsvar['gtabid']+'&ID='+jsvar['ID']+'&listmode='+listmode+gfrist+'&remid='+remove+'&use_records='+use_records,null,'limbasDivShowReminderPost');
	}else if(changeView){
		ajaxGet(null,'main_dyns.php','showReminder&gtabid='+jsvar['gtabid']+'&ID='+jsvar['ID']+'&listmode='+listmode+gfrist+'&changeViewId='+changeView,null,'limbasDivShowReminderPost');
        }else if(change){
		ajaxGet(null,'main_dyns.php','showReminder&gtabid='+jsvar['gtabid']+'&ID='+jsvar['ID']+'&listmode='+listmode+gfrist+'&changeId='+change,null,'limbasDivShowReminderPost','form_reminder');
        }else {
		ajaxGet(null,'main_dyns.php','showReminder&gtabid='+jsvar['gtabid']+'&ID='+jsvar['ID']+'&listmode='+listmode+gfrist+'&defaults='+defaults,null,'limbasDivShowReminderPost');
	}
}

// reminder post
function limbasDivShowReminderPost(result){
	document.getElementById("lmbAjaxContainer").innerHTML = result;
}

// reminder
function lmb_reminderAddUserGroup(uid,udesc,gtabid,fieldid,ID,parameter){
        // display groups in italic
        if(uid.endsWith('_g')) {
            udesc = "<i>" + udesc + "</i>";
        }
        
        // append name to list of users/groups
        if($("#contWvUGList").children("#usergroup_" + uid).length == 0) {
            $('#contWvUGList').append("<span id=\"usergroup_" + uid + "\" style=\"cursor:pointer;\" onmouseover=\"this.className='markForDelete'\" onmouseout=\"this.className=''\" onclick=\"lmb_reminderRemoveUserGroup('" + uid + "', '" + udesc + "');\">" + udesc + "</span><br>");
            
            // append uid to hidden input for form submit
            var hiddenInp = $('#REMINDER_USERGROUP');
            hiddenInp.val(hiddenInp.val() + ";" + uid);    
        }
}

function lmb_reminderRemoveUserGroup(uid, udesc) {
        // remove name from list of users/groups
        var toRemove = $('#contWvUGList').children('#usergroup_' + uid);
        toRemove.next().remove();
        toRemove.remove();
    
        // remove uid from hidden input for form submit
        var hiddenInp = $('#REMINDER_USERGROUP');
        hiddenInp.val(hiddenInp.val().replace(";" + uid, ""));
}

// timeout for same form requests
function clear_send_form () {
	sendFormTimeout = 0;
}





// Ajax Feldverärbung
function limbasSearchInherit(desttabid,destgfieldid,gtabid,gfieldid,el,ID,showall){
	if(el.value.length > 1 || el.value == '*'){
		url = "main_dyns.php";
		actid = "searchInherit&gtabid=" + gtabid + "&gfieldid=" + gfieldid + "&dest_gtabid=" + desttabid + "&dest_gfieldid=" + destgfieldid + "&showall=" + showall + "&ID=" + ID + "&value=" + el.value;
		mainfunc = function(result){limbasSearchInheritPost(result,el);};
		ajaxGetWait(null,url,actid,null,"mainfunc",'',500);
	}
}

function limbasSearchInheritPost(result,el){

	if(result){
		limbasDivShow(el,null,'lmbAjaxContainer','','',1);
		element = document.getElementById("lmbAjaxContainer");
		element.innerHTML = result;
	}
	
	activ_menu=0;
}

function limbasInheritFrom(evt, sourceGtabid, sourceId, destGtabid, destGfieldid, destId, parentage){
	limbasDivClose("");
	url = "main_dyns.php";
	actid = "inheritFrom&gtabid=" + destGtabid + "&dest_gfieldid=" + destGfieldid + "&source_id=" + sourceId + "&dest_id=" + destId;
	mainfunc = function(result){limbasInheritFromPost(result,evt,parentage);};
	ajaxGet(null,url,actid,null,"mainfunc");
}

function limbasInheritFromPost(json,evt,parentage){
	if(json != "false"){
		var data = eval('(' + json + ')');
		var ajaxpost = 0;
		for (var i in data['destId']){
			var el = null;
			
			if(document.getElementsByName(data['destFormname'][i])[0]){
				var el = document.getElementsByName(data['destFormname'][i])[0];
			}else if(document.getElementsByName(data['destFormname'][i]+'_'+data['destId'][i])[0]){
				var el = document.getElementsByName(data['destFormname'][i]+'_'+data['destId'][i])[0];
			}else if(document.getElementsByName(data['destFormname'][i]+'[]')[0]) {
                var el = document.getElementsByName(data['destFormname'][i]+'[]')[0];
            } else {
				// if no formelement present
				$( "#form1" ).append("<input type='hidden' name='"+data['destFormname'][i]+"'>");
				var el = document.getElementsByName(data['destFormname'][i])[0];
			}

			if(el){

				// if no formelement or readonly
				var ttype = el.nodeName.toLowerCase();
				if(ttype != 'input' && ttype != 'textarea' && ttype != 'select'){
					el.innerHTML = data['resultval'][i];
					$(el).attr('name', '');
					$( "#form1" ).append("<input type='hidden' name='"+data['destFormname'][i]+"'>");
					var el = document.getElementsByName(data['destFormname'][i])[0];
				}

				if(data['destFieldtype'][i] == 3 && evt.shiftKey){
					el.value += '\n'+data['resultval'][i];
				}else{
					el.value = data['resultval'][i];
				}
				if(parentage && !data['destId'][i]){data['destId'][i] = 0;}
				document.form1.history_fields.value +=  data['destGtabid'][i] + ',' + data['destFieldid'][i] + ',' + data['destId'][i] + ',' + parentage + ';';
				
				// check for same formelements
				limbasDuplicateForm(data['destFormname'][i],data['resultval'][i]);
				if(data['ajaxpost'][i]){ajaxpost = 1;}
			}
		}
		if(ajaxpost){
			send_form(1,1);
			document.form1.history_fields.value = '';
		}
	}
}

// bgcolor of row
function lmb_tableRowColor(el,color,set) {
	prevcolor = color;
	if(typeof el != 'object'){el = document.getElementById(el);}
	if(set && selected_rows[el.id]){return;}
	if(!color){color = el.getAttribute("lmbbgcolor");}
	for (var e = 0; e < el.cells.length; e++){
		rowcolor = color;
		cellcolor = el.cells[e].getAttribute("lmbbgcolor");
		if(cellcolor && !prevcolor){rowcolor = cellcolor;}
		el.cells[e].style.backgroundColor = rowcolor;
	}
}


var dyns_time = null;
var dyns_el = null;
// quicksearch
function lmbQuickSearch(evt,el,gtabid,fieldid)
{
	dyns_el = el;
	actid = "gtabQuickSearch&gtabid=" + gtabid + "&fieldid=" + fieldid + "&value=" + el.value;
	
	if(el.value.length > 1){
		if (dyns_time) {window.clearTimeout(dyns_time);}
		dyns_time = window.setTimeout(function() {
            ajaxGet(null,'main_dyns.php',actid,null,'lmbQuickSearchPost');
		}, 1000);
	}
}

function lmbQuickSearchPost(result){
	document.getElementById("limbasAjaxGtabContainer").innerHTML = result;
	limbasDivShow(dyns_el,'','limbasAjaxGtabContainer');
	activ_menu=0;
	validEnter = 1;
}

function lmbQuickSearchAction(evt,gtabid,fieldid,id,val){
	limbasDivHide(null,'limbasAjaxGtabContainer');
	inpel = "tdinp_"+gtabid+"_"+fieldid;
	if(!evt.shiftKey && typeof window.view_detail == 'function'){
		view_detail(1,id);
	}else{
		document.getElementById(inpel).value = val;
	}
	activ_menu = 0;
}

// handle context-event on row
function lmbTableContextEvent(evt,el,ID,gtabid,contextmenu,formid,form_typ,form_dimension,ERSTDATUM,EDITDATUM,ERSTUSER,EDITUSER,V_ID,V_GID,V_FID,V_TYP) {

    evt.preventDefault();

    if(evt.ctrlKey && formid) {
        var t = 'div';
        if(form_typ == 1){t = 'iframe';}
        newwin7(null,gtabid,null,null,null,ID,formid,form_dimension,null,t);
    }else{
        lmbTableContextMenu(evt, el, ID, gtabid, contextmenu, null, formid, form_typ, form_dimension, ERSTDATUM, EDITDATUM, ERSTUSER, EDITUSER, V_ID, V_GID, V_FID, V_TYP);
    }

    return false;
}

// handle klick on row
function lmbTableClickEvent(evt,el) {
	// --------- activate row -------------
	var rowid = el.id;
	if(selected_rows[rowid]){
		aktivateRow(evt,rowid,0);
	// --------- deactivate row -------------
	}else{
		aktivateRow(evt,rowid,1);
	}
	
	return false;
}

/*----------------- prüfe selektierte Reihen -------------------*/
function checkActiveRows(gtabid) {

	var actrows = new Array();
	var actrows_ = new Array();
	var rsel = null;
	if(!gtabid && document.form2 && document.form2.ID.value){
		actrows[0] = document.form2.ID.value+"_"+document.form2.gtabid.value;var bzm = 1;
	}else{
		var bzm = 0;
	}

	for (var key in selected_rows){
		if(selected_rows[key]){
			rsel = key.split("_");
			if(gtabid && rsel[2] != gtabid){continue;}
			actrows[bzm] = rsel[1]+"_"+rsel[2];
			actrows_[bzm] = rsel[1];
			bzm++;
		}
	}

	if(gtabid){
		return actrows_;
	}else{
		return actrows;	
	}

}

// de/aktivate single row
var LmGl_edit_id = null;
var selected_rows = new Array();
function aktivateRow(evt,id,activ){

	if(!evt.shiftKey && !evt.ctrlKey){
		aktivateRows(0);
	}

	var prev_id = LmGl_edit_id;
	var vids = id.split("_");
	var datid = vids[1];
	var gtabid = vids[2];
	var fieldid = vids[3];

	if(evt.shiftKey && prev_id){
		var cc = null;
		var start = null;
		var down = null;
		var up = null;
		var ar = document.getElementsByTagName("tr");
		for (var i = ar.length; i > 0;) {
			cc = ar[--i];
			var cid = cc.id.split("_");
			if(cid[0] == "elrow" && cid[2] == gtabid){
				var elid = cc.id;

				if(start){
					aktivateSingleRow(elid,1);
				}

				if(elid == prev_id && !up){
					start = 1;
					down = 1
				}
				if(elid == id && !down){
					start = 1;
					up = 1;
				}
				if(elid == id && !up){
					start = 0;
					down = 1
				}
				if(elid == prev_id && !down){
					start = 0;
					up = 1;
				}
			}
		}
	}

	aktivateSingleRow(id,activ);
}

// de/aktivate single row
function aktivateSingleRow(id,activ) {
	if(!document.getElementById(id)){return true;}
	if(activ){
		lmb_tableRowColor(id,jsvar["WEB7"]);
		selected_rows[id] = 1;
		LmGl_edit_id = id;
	}else{
		var bgcolor = document.getElementById(id).getAttribute("lmbbgcolor");
		lmb_tableRowColor(id,bgcolor);
		selected_rows[id] = 0;
	}
}

// de/aktivate all rows
function aktivateRows(activ) {
	if(activ){
		var cc = null;
		var ar = document.getElementsByTagName("tr");
		for (var i = ar.length; i > 0;) {
			var cc = ar[--i];
			var cid = cc.id.split("_");
			if(cid[0] == "elrow"){
				var elid = 'elrow_'+cid[1]+'_'+cid[2];
				aktivateSingleRow(elid,1);
			}
		}
	}else{
		for (var key in selected_rows){
			if(selected_rows[key]){
				aktivateSingleRow(key,0);
			}
		}
	}
}

// count of active rows
function countofActiveRows(gtabid){
	var count = 0;
	for (var key in selected_rows){
		if(selected_rows[key]){
			count = count + 1;
		}
	}
	return count;
}



// ---- Suchkriterien markieren ----------
var select_text = new Array();
function setSelectedText(evt,el,val) {
	if(val && el){
		if(!select_text[el.id]){select_text[el] = 0;};
		var posStart = el.value.indexOf(val,select_text[el.id]);
		var posEnd = val.length + posStart;
		select_text[el.id] = posEnd;

		if(posStart > 0){
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
function lmbGlistBodyNotice(id,val,el) {
    
    // gtabBodyDetailFringe

    if(document.getElementById('lmbGlistBodyNotice'+id)){
        $('#lmbGlistBodyNotice'+id).replaceWith(val);
    }else {
        $('#GtabTableFull').prepend(val);
    }
    $('#GtabTableFull').first().css('float','');

}

// --- Fenster VERKNÜPFUNG 1:N / N:N -----------------------------------
function newwin5(TABID,V_TABID,V_FIELDID,V_ID,READONLY) {
	divclose();
	verknpf = 1;
	// simple table without relation
	if(!TABID){
		TABID = V_TABID;
		V_FIELDID = '';
		V_ID = '';
		verknpf = '';
	}
	if(READONLY == 2){READONLY = 1;}else{READONLY = '';}
	relationb = open("main.php?action=gtab_erg&wfl_id=" + jsvar["wfl_id"] + "&wfl_inst=" + jsvar["wfl_inst"] + "&verknpf="+verknpf+"&verkn_showonly=1&verkn_ID=" + V_ID + "&gtabid=" + TABID + "&verkn_tabid=" + V_TABID + "&verkn_fieldid=" + V_FIELDID + "&readonly="+READONLY ,"relationtable"+TABID,"toolbar=0,location=0,status=0,menubar=0,scrollbars=1,resizable=1,width=800,height=600");
}

/* --- Fenster History ----------------------------------- */
function newwin6() {
	divclose();
	
	if(document.form2){
		var ID = document.form2.ID.value;
		var gtabid = document.form2.gtabid.value;
	}else{
		var ID = document.form1.ID.value;
		var gtabid = document.form1.gtabid.value;
	}
	
	hist = open("main.php?action=history&wfl_id=" + jsvar["wfl_id"] + "&wfl_inst=" + jsvar["wfl_inst"] + "&ID=" + ID + "&tab=" + gtabid + "" ,"History","toolbar=0,location=0,status=0,menubar=0,scrollbars=1,resizable=1,width=600,height=600");
}


// document.form1.action.value,'5','4','4','30','98','0','','1','element2',''

// show detail in dialog or new window
function newwin7(action,gtabid,v_tabid,v_fieldid,v_id,id,formid,formdimension,v_formid,inframe,show_relationpath) {

    if (action != 'gtab_deterg' && action != 'gtab_neu') {
        action = 'gtab_change'
    }
    var verknpf = '';
    if (v_tabid) {
        verknpf = 1;
    }
    if (formdimension) {
        var dimsn = formdimension.split("x");
        var x = (parseInt(dimsn[0]) + 20);
        var y = (parseInt(dimsn[1]) + 10);
    } else {
        var x = 782;
        var y = 450;
    }

    divclose();

    var layername = 'lmb_gtabDetailFrame';

    if(inframe && inframe != 'div' && inframe != 'iframe' && inframe != 'same'){
        layername = inframe;
    }
    
    var relation_path = '';
    if (show_relationpath) {
        var addfrom = new Array();
        addfrom = document.form1.verkn_addfrom.value.split(";");
        var newpath = document.form1.gtabid.value + "," + document.form1.ID.value + "," + document.form1.form_id.value;
        // check for dublicates - todo selfrelation
        if(!addfrom.includes(newpath)) {
            addfrom.push(newpath);
            relation_path = addfrom.join(";");
        }
    }

    // show in new window
    if (!inframe) {
        var wpath = "main.php?action=" + action + "&verkn_showonly=1&ID=" + id + "&verkn_ID=" + v_id + "&gtabid=" + gtabid + "&verkn_tabid=" + v_tabid + "&verkn_fieldid=" + v_fieldid + "&form_id=" + formid + "&verknpf=" + verknpf + "&verkn_formid=" + v_formid + '&verkn_addfrom='+relation_path;
        relationtab = open(wpath, "relationtable", "toolbar=0,location=0,status=0,menubar=0,scrollbars=1,resizable=1,width=" + x + ",height=" + y);
        return;
    }

    if (inframe == 'tab') {
        wpath = "index.php?action=redirect&src=action=" + action + "%26verkn_showonly=1%26ID=" + id + "%26verkn_ID=" + v_id + "%26gtabid=" + gtabid + "%26verkn_tabid=" + v_tabid + "%26verkn_fieldid=" + v_fieldid + "%26form_id=" + formid + "%26verknpf=" + verknpf + "%26verkn_formid=" + v_formid + '%26verkn_addfrom='+relation_path;
        relationtab = open(wpath, "_blank");
        return;
    }

    // show in same window
    if (inframe == 'same') {
        document.form1.form_id.value = formid;
        document.form1.change_ok.value = 1;
        document.form1.action.value = action;
        document.form1.ID.value = id;
        document.form1.verkn_ID.value = v_id;
        document.form1.verkn_tabid.value = v_tabid;
        document.form1.verkn_fieldid.value = v_fieldid;
        document.form1.gtabid.value = gtabid;
        document.form1.verkn_showonly.value = "1";
        //document.form1.verknpf.value = "1";
        document.form1.verkn_addfrom.value = relation_path;
        if (!document.form1.verkn_poolid.value) {
            document.form1.verkn_poolid.value = v_tabid;
        }
        document.form1.submit();
        return;

    // show in dialog as div
    }else if(inframe == 'div') {
        // show in frame as div
        $("#"+layername).remove();
        $.ajax({
            type: "POST",
            url: "main_dyns.php",
            async: false,
            dataType: "html",
            data: $('#form1 #' + layername + ' :input').add('[name=history_fields]').serialize()+"&actid=openSubForm&ID=" + id + "&gtabid=" + gtabid + "&gformid=" + formid + "&action=" + action +"&verkn_ID=" + v_id + "&verkn_tabid=" + v_tabid + "&verkn_fieldid=" + v_fieldid + '&verkn_addfrom='+relation_path + '&subformlayername='+layername,
            success: function (data) {
                $("<div id='"+layername+"'></div>").html(data).css({'position': 'relative', 'left': '0', 'top': '0'}).dialog({
                    width: x,
                    height: y,
                    resizable: true,
                    modal: true,
                    zIndex: 99999,
                    appendTo: "#form1",
                    close: function () {
                        $("#"+layername).dialog('destroy').remove();
                        document.body.style.cursor = 'default';
                        $('[name=history_fields]').val('');
                    }
                });

                if(document.getElementById('lmbSbmClose')){document.getElementById('lmbSbmClose').style.display='';}
            }
        });
    // show in dialog as iframe
    }else if(inframe == 'iframe') {
	    $("#"+layername).remove();
        $("body").append("<div id='"+layername+"' style='position:absolute;display:none;z-index:9999;overflow:hidden;width:300px;height:300px;padding:0;'><iframe id='lmb_gtabDetailIFrame' name='lmb_gtabDetailIFrame' style='width:100%;height:100%;overflow:auto;'></iframe></div>");
        $("#"+layername).css({'position': 'relative', 'left': '0', 'top': '0'}).dialog({
            width: x,
            height: y,
            resizable: true,
            modal: true,
            zIndex: 99999,
            open: function (ev, ui) {
                $('#lmb_gtabDetailIFrame').attr("src", "main.php?action=" + action + "&verkn_showonly=1&ID=" + id + "&verkn_ID=" + v_id + "&gtabid=" + gtabid + "&verkn_tabid=" + v_tabid + "&verkn_fieldid=" + v_fieldid + "&form_id=" + formid + "&verknpf=" + verknpf + "&verkn_formid=" + v_formid + '&verkn_addfrom='+relation_path);
            },
            close: function () {
               //lmb_gtabDetailIFrame.document.form1.action.value = 'gtab_change';
               //lmb_gtabDetailIFrame.send_form(1, 0, 0, 1);
               $("#"+layername).dialog('destroy').remove();
            }
        });

    // show in existing container
    }else if(document.getElementById(layername) && document.getElementById(layername).tagName.toLowerCase() == 'div') {
        // show in frame as div
        $.ajax({
            type: 'POST',
            url: "main_dyns.php",
            async: false,
            dataType: "html",
            data: $('#form1 #' + layername + ' :input').add('[name=history_fields]').serialize()+"&actid=openSubForm&ID=" + id + "&gtabid=" + gtabid + "&gformid=" + formid + "&action=" + action +"&verkn_ID=" + v_id + "&verkn_tabid=" + v_tabid + "&verkn_fieldid=" + v_fieldid + '&verkn_addfrom='+relation_path + '&subformlayername='+layername,
            success: function (data) {
                $('#'+layername).html(data);
                document.body.style.cursor = 'default';
                $('[name=history_fields]').val('');
                if(document.getElementById('lmbSbmClose')){document.getElementById('lmbSbmClose').style.display='';}
            }
        });

    // show in existing iframe
    }else if(document.getElementById(layername) && document.getElementById(layername).tagName.toLowerCase() == 'iframe') {
        // show in frame as div
        $('#' + layername).attr("src", "main.php?action=" + action + "&verkn_showonly=1&ID=" + id + "&verkn_ID=" + v_id + "&gtabid=" + gtabid + "&verkn_tabid=" + v_tabid + "&verkn_fieldid=" + v_fieldid + "&form_id=" + formid + "&verknpf=" + verknpf + "&verkn_formid=" + v_formid + '&verkn_addfrom=' + relation_path);
    }

}

// --- Fenster Mehrfach-Selectfeld -----------------------------------
function newwin10(FIELDID,TABID,TABGROUP,ID) {
	divclose();
	selectpool = open("main.php?&action=add_select&ID=" + ID + "&field_id=" + FIELDID + "&gtabid=" + TABID + "&wfl_id=" + jsvar["wfl_id"] + "&wfl_inst=" + jsvar["wfl_inst"] ,"selectpool","toolbar=0,location=0,status=0,menubar=0,scrollbars=1,resizable=1,width=500,height=600");
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

    lmb_speechRec.onstart = function() {
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

    lmb_speechRec.onend = function() {
        lmb_speechRecIcon.removeClass('lmb-microphone-slash').addClass('lmb-microphone');
        lmb_speechRecIcon.css('color', '');
        lmb_speechRecInput.css('border-color', '');
        lmb_speechRecInput.trigger('change');
        lmb_speechRecActive = false;
    };

    lmb_speechRec.onresult = function(event) {
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



/*----------------- Attibute Tag-Mode -------------------*/
$.fn.textWidth = function(text, font) {
    if (!$.fn.textWidth.fakeEl) $.fn.textWidth.fakeEl = $('<span>').hide().appendTo(document.body);
    $.fn.textWidth.fakeEl.text(text || this.val() || this.text()).css('font', font || this.css('font'));
    return $.fn.textWidth.fakeEl.width();
};


function tagAttr_init() {
    tagAttr_initSize();
    var $tagattr = $('div.tagattr:not(:data(attrloaded))');
    $tagattr.data('attrloaded',true);
    $tagattr.children('input').val('+').click(tagAttr_onSearchClick).blur(tagAttr_onSearchBlur);
    $tagattr.on('focus','table input[type=text]',tagAttr_onInputFocus).on('blur','table input[type=text]',tagAttr_onInputBlur).on('keydown','table input[type=text]',tagAttr_resizeInput);
    $tagattr.on('focus','select',tagAttr_onFocusChangeSelect).on('blur','select',tagAttr_onBlurSelect).on('change','select',tagAttr_onFocusChangeSelect);
}

function tagAttr_initSize() {
    $('.tagattr').removeClass('transition');

    $('.tagattr table input[type=text]').css('width',function(){
        if (!$(this).val()) {
            $(this).closest('tr').addClass('empty');
            return '3rem';
        } else {
            $(this).closest('tr').removeClass('empty');
            return ($(this).textWidth()+25)+'px';
        }
    });


    $('.tagattr select').css('width',function(){
        if (!$(this).val()) {
            $(this).closest('tr').addClass('empty');
            return '3rem';
        } else {
            $(this).closest('tr').removeClass('empty');
            return '100%';
        }
    });
    setTimeout(tagAttr_Transitions,500);
}


function tagAttr_Transitions() {
    $('.tagattr').addClass('transition');
}

function tagAttr_onInputFocus() {
    if (!$(this).val()) {
        $(this).css('width','5rem');
    } else {
        let textWidth = $(this).textWidth()+25;
        if (textWidth<16*6) {
            $(this).css('width','5rem');
        }
    }
}

function tagAttr_onInputBlur() {
    if (!$(this).val()) {
        $(this).css('width','3rem').closest('tr').addClass('empty');
    } else {
        $(this).css('width',($(this).textWidth()+25)+'px').closest('tr').removeClass('empty');
    }
}

function tagAttr_resizeInput() {
    $(this).css('width',($(this).textWidth()+25)+'px');
}

function tagAttr_onFocusChangeSelect() {
    if (!$(this).val()) {
        $(this).width('100%');
    } else {
        $(this).css('width',($(this).textWidth()+25)+'px');
    }
}

function tagAttr_onBlurSelect() {
    if (!$(this).val()) {
        $(this).css('width','3rem').closest('tr').addClass('empty');
    } else {
        $(this).css('width',($(this).textWidth()+25)+'px').closest('tr').removeClass('empty');
    }
}


function tagAttr_onSearchClick() {
    $(this).val('').css('text-align','left');
    this.style.setProperty('width','8rem', 'important');
}

function tagAttr_onSearchBlur() {
    $(this).val('+').removeAttr('style');
}

