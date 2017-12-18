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

//browserType();

// Ajax Preview
function lmbAjax_resultPreView(evt,gtabid,gfieldid,ID){
	var url = "main_dyns.php";
	var actid = "gresultPreView&gtabid=" + gtabid + "&gfieldid=" + gfieldid + "&ID=" + ID;
	dynfunc = function(result){lmbAjax_resultPreViewPost(result,evt);}
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
	mainfunc = function(result){limbasAjxColorSelectPost(result,evt);}
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
	checktyp(50,formname,'',fieldid,gtabid,val,id);
}

/* --- dynsPress ----------------------------------- */
function lmbAjax_dynsearch(evt,el,actid,fname,par1,par2,par3,par4,par5,par6,gformid,formid,datatype,nextpage) {
	if(evt){
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
	
	dyns_value = el.value;
	if(dyns_value.length > 1 || dyns_value == '*'){
		dyns_value = dyns_value;
		url = "main_dyns.php";
		actid = actid+"&form_name="+fname+"&form_value="+dyns_value+"&par1="+par1+"&par2="+par2+"&par3="+par3+"&par4="+par4+"&par5="+par5+"&par6="+par6+"&gformid="+gformid+"&formid="+formid+"&nextpage="+nextpage;
		mainfunc = function(result){lmbAjax_dynsearchPost(result,el);}
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
	oMultipleSelectLoader = new multipleSelectShow("oMultipleSelectLoader");
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
}

// Ajax extended relation
function LmExt_RelationFields(el,gtabid,gfieldid,viewmode,edittype,ID,orderfield,relationid,ExtAction,ExtValue,gformid,formid,ajaxpost,event){

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
	}
	
	var url = "main_dyns.php";
	
	// ajax based POST of complete formular
	if(ajaxpost){
		document.getElementById("myExtForms").innerHTML = "<input type='hidden' name='gfieldid' value='"+gfieldid+"'><input type='hidden' name='viewmode' value='"+viewmode+"'><input type='hidden' name='ExtAction' value='"+ExtAction+"'><input type='hidden' name='ExtValue' value='"+ExtValue+"'><input type='hidden' name='edittype' value='"+edittype+"'><input type='hidden' name='ID' value='"+ID+"'><input type='hidden' name='orderfield' value='"+orderfield+"'><input type='hidden' name='relationid' value='"+relationid+"'><input type='hidden' name='gformid' value='"+gformid+"'><input type='hidden' name='formid' value='"+formid+"'>";
		var actid = "extRelationFields&gtabid="+gtabid+"&gfieldid="+gfieldid+"&ID="+ID; // overwrite form
		dynfunc = function(result){LmExt_RelationFieldsPost(result,gtabid,gfieldid);}
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
			document.getElementsByName(formid)[0].value = relationid;
			document.getElementById(formid+"_ds").value = text;
		// no ajax update (ajax based select)
		}else if(document.getElementById("g_"+gtabid+"_"+gfieldid+"_ds") && el && !document.getElementById("extRelationFieldsTab_"+gtabid+"_"+gfieldid)){
			document.getElementsByName("g_"+gtabid+"_"+gfieldid)[0].value = relationid;
			checktyp('27','g_'+gtabid+'_'+gfieldid,'',gfieldid,gtabid,relationid,ID);
			document.getElementById("g_"+gtabid+"_"+gfieldid+"_ds").value = text;
			if(document.getElementById("g_"+gtabid+"_"+gfieldid+"_ds").onchange){document.getElementById("g_"+gtabid+"_"+gfieldid+"_ds").onchange();}
		// ajax update GET of single relation
		}else{
			var actid = "extRelationFields&gtabid=" + gtabid + "&gfieldid=" + gfieldid + "&viewmode=" + viewmode + "&edittype=" + edittype +  "&ID=" + ID + "&orderfield=" + orderfield + "&relationid=" + relationid + "&ExtAction=" + ExtAction +"&ExtValue=" + ExtValue + "&gformid=" + gformid+ "&formid=" + formid;
			dynfunc = function(result){LmExt_RelationFieldsPost(result,gtabid,gfieldid,viewmode,el,ID);}
			ajaxGet(null,url,actid,null,"dynfunc");
		}
	}

	if(ExtAction == "showall"){return;}

	if((ExtAction == "link" || ExtAction == "unlink") && !(event && event.shiftKey)){
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




// --------------- multiselect -----------------

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

		// multiselect
		}else{

			var gse = new Array();
			var cblist = $('.fs_checkbox');
			cblist.each(function( index ) {
				
				if($( this ).prop("checked")){ 
					gse[e] = document.getElementById("fs_val_"+$( this ).attr("elid")).value;
					e++;
				}
		
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
			
			// ajax based mutiple select
			if(document.getElementById("g_"+gtabid+"_"+fieldid+"_dse")){
				document.getElementById("g_"+gtabid+"_"+fieldid+"_dse").innerHTML = gse.join(select_cut);
			}
		
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

function fs_check_all(check){

	var cblist = $('.fs_checkbox');
	if(check){
		cblist.prop("checked", true);
	}else{
		cblist.prop("checked", false);
	}
	
}

function lmbAjax_multibleSelect(el,gtabid,gfieldid,ID){
	actid = "actid=multipleSelect&gtabid=" + gtabid + "&field_id=" + gfieldid + "&ID="+ID;
	$.ajax({
		type: "GET",
		url: "main_dyns.php",
		async: false,
		data: actid,
		success: function(data){
			$('<div>').html('<div id="lmb_multiSelect">'+data+'</div>').children('div').css('width', '100%').css({'position':'relative','left':'0','top':'0','width':'100%'}).dialog({
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

function lmbAjax_multilang(el,gtabid,gfieldid,ID){
	actid = "actid=gmultilang&gtabid=" + gtabid + "&field_id=" + gfieldid + "&ID="+ID;
	$.ajax({
		type: "GET",
		url: "main_dyns.php",
		async: false,
		data: actid,
		success: function(data){
			$('<div>').html('<div id="lmb_multiSelect">'+data+'</div>').children('div').css('width', '100%').css({'position':'relative','left':'0','top':'0','width':'100%'}).dialog({
				resizable: true,
				modal: true,
				width:'480',
				title: el.title,
				close: function() {
					$(this).dialog('destroy').remove();
				}
			});
		}
	});
}

/* --- select / multiselect / attribute  ----------------------------------- */
function dyns_11_a(evt,el_value,el_id,form_name,fieldid,gtabid,ID,dash,attribute,action,level) {

	// contextmenu
	if(!level){
		level = '';
		contextel = form_name+'_dsl';
	}else{
		contextel = form_name+'_'+level+'_dsl';
	}
	
	if(el_id){
		// attribute add / delete
		if(action == "a"){
			if(!evt.ctrlKey){dynsClose(contextel);}
			el = document.getElementById(form_name+'_dse');
			el.innerHTML = '<table style="width:95%;"><TR><TD width="2%">&nbsp;</TD><TD width="30%" style="border:1px solid #CCCCCC;background-color:#EFF7E7;"><I style="color:blue">'+el_value+'</I></TD><TD style="border:1px solid #CCCCCC;" width="68%"><INPUT TYPE="TEXT" NAME="'+form_name+'_att['+el_id+']" STYLE="width:100%;border:none;opacity:0.3;filter:Alpha(opacity=30);"></TD></TR></table>' + el.innerHTML;
			document.form1[form_name].value = document.form1[form_name].value + ";" + el_id;
		// select
		}else if(action == "b"){
			dynsClose(contextel);
			document.getElementById(form_name+'_ds').value = el_value;
			document.getElementById(form_name).value = el_value;
		// multiple select ajax
		}else if(action == "c"){
			el = document.getElementById(form_name+'_dse');
			el.innerHTML = '<SPAN STYLE=\'color:green;\'>' + el_value + '</SPAN>' + dash + el.innerHTML;
			document.form1[form_name].value = document.form1[form_name].value + ";" + el_id;
			activ_menu=1;
		// attribute modify
		}else if(action == "v"){
			document.form1[form_name].value = "v" + el_id + document.form1[form_name].value;
		// attribute (un)link
		}else if(action == "d"){
			var container = document.getElementById(form_name+'_dse');
			if(container.childNodes.length>0){
				for(var i=0;i<container.childNodes.length;i++){
					if(container.childNodes[i].innerHTML && container.childNodes[i].innerHTML==el_value){
						var element = container.childNodes[i];
						var dash_element = element.nextSibling;
						if(element){
							var delete_action = value_in(document.form1[form_name].value,el_id);
							break;
						}
					}
				}
				if(element && delete_action){
					dyns_11_a_delete(evt,element,ID,gtabid,fieldid,el_id,form_name);
				}else{
					if(element){
						if(dash_element.nodeName && dash_element.nodeName.toLowerCase()==dash.toLowerCase().replace(/\W+/g,"")){container.removeChild(dash_element);}
						container.removeChild(element);
					}
					dyns_11_a(evt,el_value,el_id,form_name,fieldid,gtabid,ID,dash,attribute,"c",level);
				}
			}else{
				dyns_11_a(evt,el_value,el_id,form_name,fieldid,gtabid,ID,dash,attribute,"c",level);
			}
			activ_menu=1;
		// load multiple select
		}else if(action == "e"){
			oMultipleSelectLoader.load(evt,el_value,el_id,form_name,fieldid,gtabid,ID,dash,attribute,action,level);
			activ_menu=1;
			return false;
		// multiple select ajax - unique
		}else if(action == "f"){
			dynsClose(contextel);
			document.getElementById(form_name+'_ds').value = el_value;
			document.getElementById(form_name).value = el_id;
		}
	}
	checktyp('32','',form_name,fieldid,gtabid,' ',ID);
}

function value_in(search_in,search_on){
	var y = 0;
	var tmp = search_in.split(";");
	if(tmp && tmp.length>0){for(y=0;y<tmp.length;y++){if(search_on==tmp[y]) return true;}}
	return false;
}

/* --- dyns_11_a delete ----------------------------------- */
function dyns_11_a_delete(evt,el,ID,gtabid,fieldid,el_id,form_name,select_cut){
	
	var val = document.form1[form_name].value;
	if(val){
		var e = 0;
		var newval = new Array();
		var valy = val.split(";");
		for(i=0;i<valy.length;i++){
			if(valy[i] != el_id){
				newval[e] = valy[i];
				e++;
			}
			newvalue = newval.join(";");
		}
		document.form1[form_name].value = newvalue;
		checktyp('32','',form_name,fieldid,gtabid,' ',ID);
	}

	el.style.color='red';
	el.style.textDecoration='line-through';
	
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
	limbasDivShow(el,null,'lmbAjaxContainer','','',1);
	document.getElementById("lmbAjaxContainer").innerHTML = document.getElementById(elname).innerHTML;
}



//----------------- neuen Verkn. Datensatz anlegen -------------------
function create_new_verkn(gtabid,fieldid,vgtabid,ID,tablename,fdimsn,verknaddfrom,formid,inframe) {
	neu = confirm(jsvar["lng_164"]+':'+tablename+'\n'+jsvar["lng_24"]);
	if(neu){
		document.body.style.cursor = 'wait';
		divclose();
		verknpf = 1;
		
		// simple table without relation
		if(!gtabid){
			gtabid = vgtabid;
			vgtabid = '';
			ID = '';
			fieldid = '';
			verknpf = '';
		}
		
		if(document.getElementById("gtabDetail") && verknaddfrom){
			var addfrom = new Array();
			addfrom = document.form1.verkn_addfrom.value.split(";");
			addfrom.push(document.form1.gtabid.value+","+document.form1.ID.value+","+document.form1.form_id.value);
			var add_from = addfrom.join(";");
			document.form1.form_id.value = '';
			document.form1.change_ok.value = 1;
			document.form1.action.value = "gtab_neu";
			document.form1.verkn_ID.value = ID;
			document.form1.verkn_tabid.value = vgtabid;
			document.form1.verkn_fieldid.value = fieldid;
			document.form1.gtabid.value = gtabid;
			document.form1.verkn_showonly.value = "1";
			document.form1.verknpf.value = "1";
			document.form1.verkn_addfrom.value = add_from;
			if(!document.form1.verkn_poolid.value){document.form1.verkn_poolid.value = vgtabid;}
			document.form1.submit();
		}else{
			newwin7('gtab_neu',gtabid,vgtabid,fieldid,ID,ID,'',fdimsn,formid,inframe);
		}
	}
}










var oMultipleSelectLoader = new multipleSelectShow("oMultipleSelectLoader");
function multipleSelectShow(instance_name) {
    this.events = new Array();
    var name = instance_name;
    
    /** returns the DOM-Element with the corresponding ID
     * @param {string} id  The ID of the element
     */
    var dom = function (id) {
        return document.getElementById(id);
    };
    this.get = function (property, value) {
        if (this.events.length <= 0)
            return false;
        if (!property) {
            if ((typeof this.events[this.events.length - 1]) !== "object")
                return false;
            return this.events[this.events.length - 1];
        }
        for (var i = 0; i < this.events.length; i++) {
            if ((typeof this.events[i]) !== "object") {
                continue;
            }
            if (this.events[i][property] == value) {
                return this.events[i];
            }
        }
        return false;
    };
    this.add = function (form_name, id, level, container_level) {
        var tmp = this.get("id", id);
        if (tmp && (typeof tmp) === "object") {
            return tmp;
        }

        var eo = new Object();
        eo.initialize = function (eid, peid, pceid, cid, fn, l, id) {
            this.element = dom(eid);
            this.parent_element = dom(peid+"_dsl") ? dom(peid+"_dsl") : dom(fn);
            this.parent = dom(pceid);
            this.child = cid ? cid+"_dsl" : "";
            this.form_name = fn;
            this.level = l;
            this.id = id;
        };
        eo.isVisible = false;
        eo.display = function (arg) {
            var parentJQuery = $('#' + this.parent.id);
            
            if (arg === false) {
                dom(this.element.id).style.textDecoration = "none";
                dom(this.element.id).style.color = "black";
                if (dom(this.child)) {
                    $('#' + this.parent.id).width("-=" + dom(this.child).offsetWidth);
                    dom(this.child).style.display = "none";
                    this.isVisible = false;
                }
                return;
            }
            dom(this.element.id).style.textDecoration = "underline";
            dom(this.element.id).style.color = "green";
            if (dom(this.child)) {
                dom(this.child).style.display = "";
               
                if (dom(this.element.id) && dom(this.parent_element.id)) {
                    dom(this.child).style.position = "absolute";
                    if (dom(this.child).style.left === "") {
                        dom(this.child).style.left = 
                            dom(this.element.id).offsetLeft + 
                            dom(this.parent_element.id).offsetLeft + 
                            $('#'+this.element.id).width() - 3;
                        // TODO funktioniert nicht in Chrome, da das span in chrome width=0 hat!!
                        // vgl. main_dyns.php :917
                    }
                    if (dom(this.child).style.top === "") {
                        dom(this.child).style.top = 
                            dom(this.element.id).offsetTop +
                            dom(this.parent_element.id).offsetTop;
                    }
                    dom(this.child).style.zIndex = this.element.style.zIndex + 1;
                    
                    // preventing style-bugs
                    $('#'+this.child).width($('#' + this.child).width());
                    dom(this.child).style.display = "block";
                    
                    this.isVisible = true;
                    
                    // enlarge the container
                    // TODO: may get a bit to big
                    parentJQuery.width( "+=" + dom(this.child).offsetWidth);
                }
            }
        };
        eo.initialize(form_name + "_" + level + "_" + id, form_name + "_" + level, form_name + "_dsl", form_name + "_" + id, form_name, level, id);
        this.events.push(eo);

        if (dom(eo.child))
            return eo;
        return false;
    };

    this.display = function (elem) {
        if (this.events.length < 1 || (typeof elem) !== "object")
            return;
        var exclude = new Array();
        var include = new Array();
        
        //console.log("!");
        //console.log(elem);
        
        
        // hide if already displayed and the other way round
        elem.display(!elem.isVisible);
        
        exclude[elem.level] = elem.child;
        var ev = this.get("id", elem.level);
        if (ev && (typeof ev) == "object") {
            exclude[ev.level] = ev.child;
            for (var i = 0; i < this.events.length; i++) {
                if (!this.events[i] || (typeof this.events[i]) !== "object")
                    continue;
                if (exclude[this.events[i].level] && exclude[this.events[i].level] !== this.events[i].child) {
                    ev = this.events[i];
                    include.push(ev.id);
                    while (ev = this.get("level", ev.id)) {
                        include.push(ev.id);
                    }
                }
            }
        } else {
            for (var i = 0; i < this.events.length; i++) {
                if (!this.events[i] || (typeof this.events[i]) != "object")
                    continue;
                if ((exclude[this.events[i].level] && exclude[this.events[i].level] !== this.events[i].child) || (!exclude[this.events[i].level])) {
                    include.push(this.events[i].id);
                }
            }
        }

        for (var y = 0; y < include.length; y++) {
            for (var i = 0; i < this.events.length; i++) {
                if (!this.events[i] || (typeof this.events[i]) != "object")
                    continue;
                if (this.events[i].id == include[y]) {
                    this.events[i].display(false);
                    delete this.events[i];
                    break;
                }
            }
        }
        
        //limbasDivShow(elem,elem.id,elem.parent_element.id,'',1);
    };
    this.load = function (e, el_value, el_id, form_name, fieldid, gtabid, ID, dash, attribute, action, element_level) {
        var eo = this.add(form_name, el_id, element_level);
        /*var tmp = $(form_name+"_0");
         var exclude = new Array();
         if(tmp && tmp.childNodes.length>0){for(var i=0;i<tmp.childNodes.length;i++){if(tmp.childNodes[i].id){var tmp1=tmp.childNodes[i].id.split("_");exclude.push(tmp1[tmp1.length-1]);}}}//+"&exclude="+exclude.join(",")*/
        if (!eo) {
            ajaxGet(null, "main_dyns.php", "limbasExtMultipleSelect&page=" + dash + "&gtabid=" + gtabid + "&fieldid=" + fieldid + "&ID=" + ID + "&level=" + el_id, null, name + ".post");
        } else {
            this.display(eo);
        }
    };

    this.post = function (response) {
        var tmp = response.split("#L#");
        if (tmp[1]) {
            var eo = this.get();
            if (eo && (typeof eo) === "object" && eo.parent) {
                eo.parent.innerHTML += tmp[1];
                this.display(eo);
            }
        }
        if (!eo || (typeof eo) !== "object")
            delete this.events[this.events.length - 1];
    };
}

/* ########################################################################## */




var oMultipleSelectLoader_depr = new multipleSelectShow_depr("oMultipleSelectLoader_depr");
function multipleSelectShow_depr(instance_name){
	this.events = new Array();
	var name = instance_name;
	var $ = function(id){return document.getElementById(id);};
	var $_ = function(name){return document.getElementsByName(name)[arguments[1] ? arguments[1] : 0];};
	var _$ = function(name){return document.getElementsByTagName(name)[arguments[1] ? arguments[1] : 0];};
	this.get = function(property,value){
		if(this.events.length<=0) return false;
		if(!property){
			if((typeof this.events[this.events.length-1])!="object") return false;
			return this.events[this.events.length-1];
		}
		for(var i=0;i<this.events.length;i++){
			if((typeof this.events[i])!="object") continue;
			if(this.events[i][property]==value) return this.events[i];
		}
		return false;
	};
	this.add = function(form_name,id,level,container_level){
		var tmp = this.get("id",id);
		if(tmp && (typeof tmp)=="object"){return tmp;}

		var eo = new Object();
		eo.initialize = function(eid,peid,pceid,cid,fn,l,id){
			this.element = $(eid);
			this.parent_element = $(peid) ? $(peid) : $(fn+"_0");
			this.parent = $(pceid);
			this.child = cid;this.form_name = fn;this.level = l;this.id = id;
		};
		eo.display = function(arg){
			if(arg==false){
				$(this.element.id).style.textDecoration = "none";
				$(this.element.id).style.color = "black";
				if($(this.child)) $(this.child).style.display = "none";
				return;
			}
			$(this.element.id).style.textDecoration = "underline";
			$(this.element.id).style.color = "green";
			if($(this.child)){
				$(this.child).style.display = "";
				if($(this.element.id) && $(this.parent_element) && $(this.parent_element.id)){
					$(this.child).style.position = "absolute";
					if(!$(this.child).style.left)
						$(this.child).style.left = $(this.element.id).offsetLeft + ($(this.child).style.left ? 0 : $(this.parent_element.id).offsetLeft) + $(this.element.id).offsetWidth-3;
					if(!$(this.child).style.top)
						$(this.child).style.top = $(this.element.id).offsetTop + ($(this.child).style.top ? 0 : $(this.parent_element.id).offsetTop);
					$(this.child).style.zIndex = this.element.style.zIndex+1;
				}
			}
		};
		eo.initialize(form_name+"_"+level+"_"+id,form_name+"_"+level,form_name+"_dsl",form_name+"_"+id,form_name,level,id);
		this.events.push(eo);
		
		if($(eo.child)) return eo;
		return false;
	};
	
	this.display = function(elem){
		if(this.events.length<1 || (typeof elem)!="object") return;
		var exclude = new Array();
		var include = new Array();
		
		elem.display(true);
		exclude[elem.level] = elem.child;
		var ev = this.get("id",elem.level);
		if(ev && (typeof ev)=="object"){
			exclude[ev.level] = ev.child;
			for(var i=0;i<this.events.length;i++){
				if(!this.events[i] || (typeof this.events[i])!="object") continue;
				if(exclude[this.events[i].level] && exclude[this.events[i].level]!==this.events[i].child){
					ev = this.events[i];
					include.push(ev.id);
					while(ev=this.get("level",ev.id)){include.push(ev.id);}
				}
			}
		}else{
			for(var i=0;i<this.events.length;i++){
				if(!this.events[i] || (typeof this.events[i])!="object") continue;
				if((exclude[this.events[i].level] && exclude[this.events[i].level]!==this.events[i].child) || (!exclude[this.events[i].level])){include.push(this.events[i].id);}
			}
		}

		for(var y=0;y<include.length;y++){
			for(var i=0;i<this.events.length;i++){
				if(!this.events[i] || (typeof this.events[i])!="object") continue;
				if(this.events[i].id==include[y]){this.events[i].display(false);delete this.events[i];break;}
			}
		}
	};
	this.load = function(e,el_value,el_id,form_name,fieldid,gtabid,ID,dash,attribute,action,element_level){
		var eo = this.add(form_name,el_id,element_level);
		/*var tmp = $(form_name+"_0");
		var exclude = new Array();
		if(tmp && tmp.childNodes.length>0){for(var i=0;i<tmp.childNodes.length;i++){if(tmp.childNodes[i].id){var tmp1=tmp.childNodes[i].id.split("_");exclude.push(tmp1[tmp1.length-1]);}}}//+"&exclude="+exclude.join(",")*/
		if(!eo){
			ajaxGet(null,"main_dyns.php","limbasExtMultipleSelect&page="+dash+"&gtabid="+gtabid+"&fieldid="+fieldid+"&ID="+ID+"&level="+el_id,null,name+".post");
		}else{
			this.display(eo);
		}
	};

	this.post = function(response){
		var tmp = response.split("#L#");
		if(tmp[1]){
			var eo = this.get();
			if(eo && (typeof eo)=="object" && eo.parent){
				eo.parent.innerHTML += tmp[1];
				this.display(eo);
			}
		}
		if(!eo || (typeof eo)!="object") delete this.events[this.events.length-1];
	};
	return this;
}


function checktypbool(data_type,fieldname,fielddesc,field_id,tab_id,obj,id) {
	eval('var reg = RULE['+data_type+'];');
	if (obj != ''){
		if (!reg.exec(obj)){
			return false;
		}
	}
	return true;
}


function lmb_updateField(field_id,tab_id,id,changevalue,syntaxcheck,fielddesc,ajaxpost,fieldsize,el) {
	checktyp(syntaxcheck,'g_'+tab_id+'_'+field_id,fielddesc,field_id,tab_id,changevalue,id,ajaxpost,fieldsize,el);
}

function checktyp(data_type,fieldname,fielddesc,field_id,tab_id,obj,id,ajaxpost,fieldsize,el) {
	if(!fielddesc){fielddesc = fieldname;}

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
		regexp = regexp.replace(/xx/g,'');
	}

	eval('var reg = /'+regexp+'/;');
	if (obj != ''){
		if (val && !reg.exec(val)){
			newvalue = prompt(jsvar['lng_134']+'\n' + fielddesc +' \t '+DATA_TYPE_EXP[data_type]+' \t '+FORMAT[data_type]+' \n',obj);
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
						document.form1.history_fields.value=document.form1.history_fields.value + tab_id + ',' + field_id + ',' + id + ';';
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
					document.form1.history_fields.value = document.form1.history_fields.value + tab_id + ',' + field_id + ',' + id + ';';
					var isupdate = 1;
				}else{
					document.form1.history_search.value = document.form1.history_search.value + tab_id + ',' + field_id + ';';
				}
			}
		}
	}else{
		if(field_id && tab_id){
			if(id){
				document.form1.history_fields.value = document.form1.history_fields.value + tab_id + ',' + field_id + ',' + id + ';';
				var isupdate = 1;
			}else{
				document.form1.history_search.value = document.form1.history_search.value + tab_id + ',' + field_id + ';';
				var isupdate = 1;
			}
		}
	}
	
	if(isupdate){
		limbasDuplicateForm(el,fieldname,val);
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
			if(obj.type == 'checkbox'){
				if(val){obj.checked = true;}else{obj.checked = false;}
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
		elid = 'GtabTableBody';
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
				if(npos > 0){nspell = titleval.substring(1,npos);
				}else{nspell = titleval.substring(1);}
			}
			
			
			// ajax relation & select
			if(cc.name.substr(cc.name.length-3, 3) == '_ds'){
				nval = document.getElementById(cc.name.substr(0, cc.name.length-3)).value;
				if(nval.substr(0, 3) == '#L#'){nval = '';}
			}
			
			if((ntype == 'text' || ntype == 'textarea') && !nval){
				need[need.length] = nspell;
				setbg($(this));
			}
			else if(ntype == 'file' && !document.form1['uploaded_'+nname]){
				need[need.length] = nspell;
				setbg($(this));
			}
			else if(ntype == 'select' && cc.selectedIndex <= 0 && nval == ''){
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
	})
	
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

// report menu options
function limbasReportMenuOptions(evt,el,gtabid,reportid,ID,output,listmode,report_medium,report_rename)
{
	activ_menu = 1;
	linkadd = '';
	if(!report_medium){report_medium = '';}
	if(!el){
		con = '';
		use_record = '';
		
		if(!report_medium && document.report_form){report_medium = document.report_form.report_medium.value;}
		if(!report_rename && document.report_form && document.report_form.report_rename){report_rename = document.report_form.report_rename.value;}
		if(!report_rename){report_rename = '';}
		
		if(listmode){
			linkadd = "&report_output="+output+"&report_medium="+report_medium+"&report_rename="+report_rename;
		}else{
			if(ID){
				use_record = ID;
			}else if(use_record = userecord('report',1)){
				var count =	countofActiveRows();
			}else{
				use_record = "all";
				if(document.getElementById("GtabResCount")){var count = document.getElementById("GtabResCount").innerHTML;}else{var count = 'undefined'}
			}
			
			if(count && !confirm(jsvar['lng_2676']+' '+count+'\n'+jsvar['lng_51'])){
				activ_menu = 0;
				limbasDivClose();
				return;
			}
			linkadd = "&use_record="+use_record+"&report_output="+output+"&report_medium="+report_medium+"&report_rename="+report_rename;
		}
		
		limbasWaitsymbol(evt,1);
	}else{
		linkadd = "&report_medium="+report_medium+"&report_output="+output;
	}
	
	actid = "menuReportOption&gtabid=" + gtabid + "&reportid=" + reportid + "&ID=" + ID + linkadd;
	mainfunc = function(result){limbasReportMenuOptionsPost(result,el,output);}
	ajaxGet(null,"main_dyns.php",actid,null,"mainfunc","form1");
}


function limbasReportMenuOptionsPost(result,el,output){
	// use Scripts
	
	ajaxEvalScript(result);
	document.getElementById("limbasDivMenuReportOption").innerHTML = result;
	if(el){
		limbasDivShow(el,'limbasDivMenuBericht','limbasDivMenuReportOption');
	}else{
		limbasWaitsymbol(0,0,1);
	}
	
	if(output==2){
		document.getElementById("lmbReportMenuOptionsArchive").innerHTML = "<i class='lmb-icon lmb-aktiv' border=0></i>";
	}else if(output==1){
		activ_menu = 0;
		limbasDivClose();
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
		mainfunc = function(result){limbasSearchInheritPost(result,el);}
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

function limbasInheritFrom(evt, sourceGtabid, sourceId, destGtabid, destGfieldid, destId){
	limbasDivClose("");
	url = "main_dyns.php";
	actid = "inheritFrom&gtabid=" + destGtabid + "&dest_gfieldid=" + destGfieldid + "&source_id=" + sourceId + "&dest_id=" + destId;
	mainfunc = function(result){limbasInheritFromPost(result,evt);}
	ajaxGet(null,url,actid,null,"mainfunc");
}

function limbasInheritFromPost(json,evt){
	if(json != "false"){
		var data = eval('(' + json + ')');
		var ajaxpost = 0;
		for (var i in data['destId']){
			var el = null;
			
			if(document.getElementsByName(data['destFormname'][i])[0]){
				var el = document.getElementsByName(data['destFormname'][i])[0];
			}else if(document.getElementsByName(data['destFormname'][i]+'_'+data['destId'][i])[0]){
				var el = document.getElementsByName(data['destFormname'][i]+'_'+data['destId'][i])[0];
			}else{
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
				document.form1.history_fields.value +=  data['destGtabid'][i] + ',' + data['destFieldid'][i] + ',' + data['destId'][i] + ';';
				
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
		dyns_time = window.setTimeout('eval("ajaxGet(null,\'main_dyns.php\',\'"+actid+"\',null,\'lmbQuickSearchPost\')")',1000); 
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





// --- Fenster fuer Detail  -----------------------------------
function newwin2(TABID,V_TABID,V_FIELDID,V_ID,ID,FORMID) {
	divclose();
	relationa = open("main.php?&action=gtab_change&verknpf=1&verkn_showonly=1&ID=" + ID + "&verkn_ID=" + V_ID + "&gtabid=" + TABID + "&verkn_tabid=" + V_TABID + "&verkn_fieldid=" + V_FIELDID + "&form_id=" + FORMID + "&wfl_id=" + jsvar["wfl_id"] + "&wfl_inst=" + jsvar["wfl_inst"] ,"relationdetail","toolbar=0,location=0,status=0,menubar=0,scrollbars=1,resizable=1,width=800,height=600");
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

// show detail in dialog
function newwin7(action,gtabid,v_tabid,v_fieldid,v_id,id,formid,formdimension,v_formid,inframe){
	
	if(action != 'gtab_deterg' && action != 'gtab_neu'){action = 'gtab_change'}
	if(v_tabid){verknpf = 1;}
	if(formdimension && formid != '0'){
		var dimsn = formdimension.split("x");
		var x = (parseInt(dimsn[0])+20);
		var y = (parseInt(dimsn[1])+10);
	}else{
		var x = 660;
		var y = 450;
	}

	// show in new window
	if(!inframe){
		relationtab = open("main.php?action=" + action + "&verkn_showonly=1&ID=" + id + "&verkn_ID=" + v_id + "&gtabid=" + gtabid + "&verkn_tabid=" + v_tabid + "&verkn_fieldid=" + v_fieldid + "&form_id="+formid + "&verknpf="+ verknpf + "&verkn_formid="+ v_formid  ,"relationtable","toolbar=0,location=0,status=0,menubar=0,scrollbars=1,resizable=1,width="+x+",height="+y);
		return;
	}
	
	// show in frame as iframe
	if(!document.getElementById("lmb_gtabDetailFrame")){$( "body" ).append("<div id='lmb_gtabDetailFrame' style='position:absolute;display:none;z-index:9999;overflow:hidden;width:300px;height:300px;padding:0px;'><iframe id='lmb_gtabDetailIFrame' name='lmb_gtabDetailIFrame' style='width:100%;height:100%;border:none;overflow:auto;'></iframe></div>");}
	$("#lmb_gtabDetailFrame").css({'position':'relative','left':'0','top':'0'}).dialog({
		width: x,
		height: y,
		resizable: true,
		modal: true,
		zIndex: 99999,
		open: function(ev, ui){
			$('#lmb_gtabDetailIFrame').attr("src","main.php?action=" + action + "&verkn_showonly=1&ID=" + id + "&verkn_ID=" + v_id + "&gtabid=" + gtabid + "&verkn_tabid=" + v_tabid + "&verkn_fieldid=" + v_fieldid + "&form_id="+formid + "&verknpf="+ verknpf + "&verkn_formid="+ v_formid);
		},
		close:function(){
			lmb_gtabDetailIFrame.document.form1.action.value='gtab_change';
			lmb_gtabDetailIFrame.send_form(1,0,0,1);
		}
	});

}

// --- Fenster Mehrfach-Selectfeld -----------------------------------
function newwin10(FIELDID,TABID,TABGROUP,ID) {
	divclose();
	selectpool = open("main.php?&action=add_select&ID=" + ID + "&field_id=" + FIELDID + "&gtabid=" + TABID + "&wfl_id=" + jsvar["wfl_id"] + "&wfl_inst=" + jsvar["wfl_inst"] ,"selectpool","toolbar=0,location=0,status=0,menubar=0,scrollbars=1,resizable=1,width=500,height=600");
}