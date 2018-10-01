/*
 * Copyright notice
 * (c) 1998-2018 Limbas GmbH(support@limbas.org)
 * All rights reserved
 * This script is part of the LIMBAS project. The LIMBAS project is free software; you can redistribute it and/or modify it on 2 Ways:
 * Under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * Or
 * In a Propritary Software Licence http://limbas.org
 * The GNU General Public License can be found at http://www.gnu.org/copyleft/gpl.html.
 * A copy is found in the textfile GPL.txt and important notices to the license from the author is found in LICENSE.txt distributed with these scripts.
 * This script is distributed WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * This copyright notice MUST APPEAR in all copies of the script!
 * Version 3.5
 */

/*
 * ID:
 */


function limbasCompleteTyping(el,x,y,tableName,fieldSearch,displayField,jsFunction,fieldId,inputId,inputDisplay)
{
	document.getElementsByName("limbasBeginValue")[0].value = el.value;
	document.getElementsByName("limbasTableName")[0].value = tableName;
	document.getElementsByName("limbasFieldSearch")[0].value = fieldSearch;
	document.getElementsByName("limbasDisplayField")[0].value = displayField;
	document.getElementsByName("limbasJsFunction")[0].value = jsFunction;
	document.getElementsByName("limbasIdField")[0].value = fieldId;
	
	document.getElementsByName("limbasInputDisplay")[0].value = inputDisplay;
	document.getElementsByName("limbasInputId")[0].value = inputId;
	
	document.getElementById("limbasPossibleCompleteTyping").style.left = findPosX(document.getElementsByName(inputDisplay)[0]);
	document.getElementById("limbasPossibleCompleteTyping").style.top = findPosY(document.getElementsByName(inputDisplay)[0]) + (document.getElementsByName(inputDisplay)[0].style.height?document.getElementsByName(inputDisplay)[0].style.height:15);
	
		
	var params = new Array("limbasTableName","limbasFieldSearch","limbasDisplayField","limbasJsFunction","limbasIdField","limbasBeginValue","limbasInputDisplay","limbasInputId");
	ajaxGet(0,"main_dyns.php","completeTyping",params,"limbasPostCompleteTyping");
	
	
}

function limbasPostCompleteTyping(string)
{
	
	document.getElementById("limbasPossibleCompleteTyping").style.visibility = "visible";
	document.getElementById("limbasPossibleCompleteTyping").innerHTML = string;
	
}

function limbasClickCompleteTyping(displayField,displayValue,idField,idValue)
{
	
	document.getElementsByName(displayField)[0].value = displayValue;
	document.getElementsByName(idField)[0].value = idValue;
	document.getElementById("limbasPossibleCompleteTyping").style.visibility = "hidden";
	document.getElementById("limbasPossibleCompleteTyping").innerHTML = "";
	
}

