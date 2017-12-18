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
	
	/*
		Änderungen in: 
			main_dyns_admin.php (dyns_saveDiagDetail, dyns_createDiag, dyns_saveSettings),
			main_admin.php (setup_diag) (weiß nicht warum)
			diagramm.php
	*/
	
	// pchart and jqplot
	function lmb_updateData($diag_id,$field_id,$show,$axis,$color){
		global $db;
		
		# Check if parameters are valid
		if($diag_id===null or $field_id===null or $show===null or $axis===null or $color===null){return;}

		# Check if record already exists
		$sq = "SELECT CHART_ID, FIELD_ID 
			FROM LMB_CHARTS 
			WHERE CHART_ID = $diag_id AND FIELD_ID = $field_id;";
		$rs = odbc_exec($db,$sq) or errorhandle(odbc_errormsg($db),$sq,$action,__FILE__,__LINE__);
		
		# Create/Update/Delete record
		$sqlquery=null;
		if(odbc_fetch_row($rs, 1)==null){
			# Record doesnt exist
			if($show==true){
				# Create new record
				$NEXTID = next_db_id("LMB_CHARTS");
				$sqlquery = "INSERT INTO LMB_CHARTS (ID, CHART_ID, FIELD_ID, AXIS, FUNCTION, COLOR) 
					VALUES($NEXTID,$diag_id,$field_id,1,0,'000000');";
			}
		}else{
			# Record exists
			if($show==true){
				# Update record
				$sqlquery = "UPDATE LMB_CHARTS 
					SET AXIS=$axis,FUNCTION=0,COLOR='$color'
					WHERE CHART_ID=$diag_id AND FIELD_ID = $field_id;";
			}elseif($show==false){
				# Delete record
				$sqlquery = "DELETE FROM LMB_CHARTS WHERE CHART_ID=$diag_id AND FIELD_ID = $field_id;";
			}
		}
		if($sqlquery!=null){
			$rs = odbc_exec($db,$sqlquery) or errorhandle(odbc_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
		}
		
		# Create return value
		$sqlquery = "SELECT CHART_ID, FIELD_ID, AXIS, FUNCTION, COLOR 
			FROM LMB_CHARTS 
			WHERE CHART_ID=$diag_id AND FIELD_ID = $field_id";
		$rs = odbc_exec($db,$sqlquery) or errorhandle(odbc_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
		if(odbc_fetch_row($rs,1)==null) {
			echo json_encode(null);
		}else{
			odbc_fetch_row($rs, 1);
			$diagdetaillist["axis"] = odbc_result($rs, "AXIS");
			//$diagdetaillist["function"] = odbc_result($rs, "FUNCTION");
			$diagdetaillist["color"] = odbc_result($rs, "COLOR");	
			echo json_encode($diagdetaillist); 
		}
	}
		
	// pchart
	function lmb_saveCustomizationSettings($par){
		global $db;
		
		$sqlquery = "UPDATE LMB_CHART_LIST SET ";
		foreach($par as $key => $part){
			if($key != "actid" && $key != "diag_id"){
				$sqlquery .= parse_db_string(strtoupper($key)) . "='" . parse_db_string($part) . "',";
			}
		}
		$sqlquery = rtrim($sqlquery,",");
		$sqlquery .= " WHERE ID=".$par['diag_id'].";";
		$rs = odbc_exec($db,$sqlquery) or errorhandle(odbc_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);		

	}
	
?>