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


// open upload options
var tmp_el=null;
var tmp_gformid=null;
var tmp_elid=null;
function lmb_formpic_open(id,name,elid,gformid){

    tmp_el = id;
    tmp_gformid = gformid;
    tmp_elid = elid;

    $('#'+id+'_detail').dialog({
        appendTo: "#form1",
        title: name,
        resizable: true,
        modal: true,
        width:400,
        height:400,
        zIndex: 1000,
        close: function () {
            $('#'+id+'_detail').dialog("destroy");
        }
    });
}

// open upload preview
function lmb_formpic_preview(url,name,size){
    if(size){
        size = size.split('x');
        var width = size[0];
        var height = size[1];
    }else{
        var height = 500;
    }

    height = 500;
    width = 500;

    $("<div><img src='" + url + "' height='100%' width='100%'></div>").dialog({
        title: name,
        width: width,
        height: height,
        resizable: false,
        modal: true,
        zIndex: 1000,
        close: function () {
            $('#lmbformpicpreview').dialog("destroy").remove();
        }
    });
}

// explorer select folder
function LmExt_Ex_SelectFolder(LID,gtabid,gfieldid,ID){
	var url = "main_dyns.php";
	var actid = "extFileManager&gtabid=" + gtabid + "&gfieldid=" + gfieldid + "&ID=" + jsvar["ID"] + "&LID=" + LID;
	mainfunc = function(result){LmExt_RelationFieldsPost(result,gtabid,gfieldid);};
	ajaxGet(null,url,actid,null,"mainfunc");
}


// explorer upload Extension
function LmExt_Ex_RelationUploadDelete(gtabid,gfieldid,ID,LID,droprelation,max){
    droprelation = $('#'+droprelation).attr("data-id");

    if(droprelation == 0){return;}
    droprelation = 'd_'+droprelation;

	var url = "main_dyns.php";
    actid = "extFileManager&gtabid=" + gtabid + "&gfieldid=" + gfieldid + "&LID="+ LID +"&ID=" + ID + "&droprelation=" + droprelation + "&delrelation=1";
	ajaxGet(null,url,actid,null,"ajaxEvalScript");

	lmb_formpicdmove(gtabid+'_'+gfieldid+'_'+ID,'1',max);
}


// explorer manager Extension
function LmExt_Ex_RelationFields(gtabid,gfieldid,viewmode,edittype,ID,orderfield,addrelation,droprelation,delrelation,picshow,formid,addfolder,sub,search){
	var url = "main_dyns.php";

	if(tmp_elid && tmp_el) {
	    $('#'+tmp_el+'_detail').dialog("destroy");
        lmb_formRenderElement(tmp_el, ID, gtabid, tmp_gformid, tmp_elid);
        tmp_el = null;
        tmp_gformid = null;
        tmp_elid = null;
        return;
    }

	if(!document.getElementById("ExtFileManager_LID_"+gtabid+"_"+gfieldid)){
	    send_form(1);
	    return;
	}

	var LID = document.getElementById("ExtFileManager_LID_"+gtabid+"_"+gfieldid).value;
	var textel = document.getElementById("extRelationFields_"+gtabid+"_"+gfieldid+"_"+viewmode);
	if(document.form1.form_id){var gformid = document.form1.form_id.value;}
	if(typeof search == 'object'){var searchval = search.value;var searchfield = search.id;}else{var searchval = '';var searchfield = search;}
	actid = "extFileManager&gtabid=" + gtabid + "&gfieldid=" + gfieldid + "&viewmode=" + viewmode + "&edittype=" + edittype + "&LID="+ LID +"&ID=" + ID + "&orderfield=" + orderfield + "&addrelation=" + addrelation + "&droprelation=" + droprelation + "&delrelation=" + delrelation + "&picshow=" + picshow + "&gformid=" + gformid + "&formid=" + formid + "&addfolder=" + addfolder+ "&viewsub=" + sub+ "&searchval=" + searchval+ "&searchfield=" + searchfield;
	mainfunc = function(result){LmExt_RelationFieldsPost(result,gtabid,gfieldid,viewmode,textel);};
	ajaxGet(null,url,actid,null,"mainfunc");
}

// action of miniexplorer
function LmExt_Ex_LinkMiniEx(gtabid,gfieldid,ID,result){
	LmExt_Ex_RelationFields(gtabid,gfieldid,'','',ID,'',result,'','','','','','','');
}

// unlink files
function LmExt_Ex_UnLinkFiles(gtabid,gfieldid,ID,LID,formid,del){
	filelist = LmEx_selectedFileList(LID);
	var e = 0;
	var f = 0;
	var result = new Array();
	for (var i in filelist){
		if(filelist[i]){
			result[e] = i;
			f++;
		}
		e++;
	}
	if(f > 0){
		if(del){conftxt = jsvar["lng_2153"];}else{conftxt = jsvar["lng_2187"];}
		var de = confirm(conftxt + ' (' + f + ')');
		if(de){
			result = result.join("-");
			LmExt_Ex_RelationFields(gtabid,gfieldid,'','',ID,'','',result,del,'',formid,'','');
		}
	}else{
		alert(jsvar["lng_1717"]);
	}
}

// symbol transparency
function LmExt_Ex_DisplaySymbols(smid,group,ident){
	var smid = document.getElementById('LmEx_Ex_symbol_'+ident+smid);
	if(group){
		var symlist = group.split(",");
		for (var i in symlist){
			document.getElementById('LmEx_Ex_symbol_'+ident+symlist[i]).style.opacity='0.3';
			document.getElementById('LmEx_Ex_symbol_'+ident+symlist[i]).style.filter='Alpha(opacity=30)';
		}
		smid.style.opacity='1';smid.style.filter='Alpha(opacity=100)';
	}else{
		if(smid.style.opacity == '0.3' || smid.style.filter == 'Alpha(opacity=30)'){
			smid.style.opacity='1';smid.style.filter='Alpha(opacity=100)';
		}else{
			smid.style.opacity='0.3';smid.style.filter='Alpha(opacity=30)';
		}
	}
}