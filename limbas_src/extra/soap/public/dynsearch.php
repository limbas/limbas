<?php
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


require("include.lib");

if(lmb_strpos($HTTP_USER_AGENT,"MSIE")){$browser = "ie";}else{$browser = "ns";}

# ---- Select - Verkn�pfung --------
function soapdyns_1($value,$form_name,$gtabid,$fieldid,$par3,$par4,$par5){
	global $browser;
	
	$lmpar[0][getvars] = array('fresult');
	$lmpar[0][action] = "gtab_erg";
	$lmpar[0][gtabid] = $gtabid;
	$lmpar[0][fieldid] = $fieldid;
	$lmpar[0][res_next] = "1";
	
	# zus�tzliche Suchkriterien
	if($value){
		if($browser == "ns"){$value = utf8_decode($value);}
		$gsr[$gtabid][$fieldid][] = $value;
	}
	if($par3){
		$par3 = explode(";",$par3);
		$gsr[$gtabid][$par3[0]][] = $par3[1];
		$gsr[$gtabid][$par3[0]][txt][0] = 2;
		$gsr[$gtabid][$par3[0]][cs][0] = 1;
	}
	if($par4){
		$par4 = explode(";",$par4);
		$gsr[$gtabid][$par4[0]][] = $par4[1];
	}
	if($par5){
		$par5 = explode(";",$par5);
		$gsr[$gtabid][$par5[0]][] = $par5[1];
	}
	
	# Eingabe Suchkriterium
	$lmpar[0][gsr] = $gsr;
	$lmb = call_client($lmpar);
					
	if($lmb[0][fresult]){
		echo "<DIV STYLE=\"background-color:#FEF9D5; border:1px solid grey; padding:4px;\">";
		echo "<TABLE BORDER=\"0\" CELLPADDING=\"0\" CELLSPACING=\"0\">";
		$bzm = 0;
		foreach($lmb[0][fresult] as $key => $value){
			echo "<TR><TD STYLE=\"cursor:pointer;\" 
				OnMouseOver=\"this.style.color='blue';\" 
				OnMouseOut=\"this.style.color='black';\" 
				OnClick=\"
				document.form1.".$form_name."_ds.value=this.firstChild.nodeValue;
				document.form1.".$form_name.".value='$key';
				dynsClose('".$form_name."_dsl');
				\">".utf8_encode($lmb[0][fresult][$key][$fieldid])."</TD></TR>";
			$bzm++;
		}
		if($bzm > 50){echo "<TR><TD>...</TD></TR>";}
		echo "</TABLE>";
		echo "</DIV>";
	}
}




# --- Funktions-Aufruf -----------
if($actid AND function_exists("soapdyns_".$actid)){
	eval("soapdyns_".$actid."(\$value,\$form_name,\$par1,\$par2,\$par3,\$par4,\$par5);");
}