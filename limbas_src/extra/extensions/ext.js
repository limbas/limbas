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

// explorer select folder
function LmExt_Ex_SelectFolder(LID,gtabid,gfieldid,ID){
	var url = "main_dyns.php";
	var actid = "extFileManager&gtabid=" + gtabid + "&gfieldid=" + gfieldid + "&ID=" + jsvar["ID"] + "&LID=" + LID;
	mainfunc = function(result){LmExt_RelationFieldsPost(result,gtabid,gfieldid);}
	ajaxGet(null,url,actid,null,"mainfunc");
}

// explorer
function LmExt_Ex_RelationFields(gtabid,gfieldid,viewmode,edittype,ID,orderfield,addrelation,droprelation,delrelation,picshow,formid,addfolder,sub,search){
	var url = "main_dyns.php";
	var LID = document.getElementById("ExtFileManager_LID_"+gtabid+"_"+gfieldid).value;
	var textel = document.getElementById("extRelationFields_"+gtabid+"_"+gfieldid+"_"+viewmode);
	if(document.form1.form_id){var gformid = document.form1.form_id.value;}
	if(typeof search == 'object'){var searchval = search.value;var searchfield = search.id;}else{var searchval = '';var searchfield = search;}
	actid = "extFileManager&gtabid=" + gtabid + "&gfieldid=" + gfieldid + "&viewmode=" + viewmode + "&edittype=" + edittype + "&LID="+ LID +"&ID=" + ID + "&orderfield=" + orderfield + "&addrelation=" + addrelation + "&droprelation=" + droprelation + "&delrelation=" + delrelation + "&picshow=" + picshow + "&gformid=" + gformid + "&formid=" + formid + "&addfolder=" + addfolder+ "&viewsub=" + sub+ "&searchval=" + searchval+ "&searchfield=" + searchfield;
	mainfunc = function(result){LmExt_RelationFieldsPost(result,gtabid,gfieldid,viewmode,textel);}
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