<?php
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

	// pchart and jqplot
	function lmb_updateData($diag_id,$field_id,$show,$axis,$color){
		global $db;
		
		# Check if parameters are valid
		if($diag_id===null or $field_id===null or $show===null or $axis===null or $color===null){return;}

		# Check if record already exists
		$sq = "SELECT CHART_ID, FIELD_ID 
			FROM LMB_CHARTS 
			WHERE CHART_ID = $diag_id AND FIELD_ID = $field_id;";
		$rs = lmbdb_exec($db,$sq) or errorhandle(lmbdb_errormsg($db),$sq,$action,__FILE__,__LINE__);
		
		# Create/Update/Delete record
		$sqlquery=null;
		if(lmbdb_fetch_row($rs)){
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
		}else{
			# Record doesnt exist
			if($show==true){
				# Create new record
				$NEXTID = next_db_id("LMB_CHARTS");
				$sqlquery = "INSERT INTO LMB_CHARTS (ID, CHART_ID, FIELD_ID, AXIS, FUNCTION, COLOR) 
					VALUES($NEXTID,$diag_id,$field_id,1,0,'000000');";
			}
		}
		if($sqlquery!=null){
			$rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
		}
		
		# Create return value
		$sqlquery = "SELECT CHART_ID, FIELD_ID, AXIS, FUNCTION, COLOR 
			FROM LMB_CHARTS 
			WHERE CHART_ID=$diag_id AND FIELD_ID = $field_id";
		$rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
		if(lmbdb_fetch_row($rs)) {
			$diagdetaillist["axis"] = lmbdb_result($rs, "AXIS");
			//$diagdetaillist["function"] = lmbdb_result($rs, "FUNCTION");
			$diagdetaillist["color"] = lmbdb_result($rs, "COLOR");
			echo json_encode($diagdetaillist);
		}else{
            echo json_encode(null);
		}
	}
		
	// pchart
	function lmb_saveCustomizationSettings($par){
		global $db;
                		
		$sqlquery = "UPDATE LMB_CHART_LIST SET "
                        . "DIAG_WIDTH=" . parse_db_int($par['diag_width']) . ","
                        . "DIAG_HEIGHT=" . parse_db_int($par['diag_height']) . ","
                        . "TEXT_X='" . parse_db_string($par['text_x']) . "',"
                        . "TEXT_Y='" . parse_db_string($par['text_y']) . "',"
                        . "FONT_SIZE=" . parse_db_int($par['font_size']) . ","
                        . "PADDING_LEFT=" . ($par['padding_left']=='auto' ? 'null' : parse_db_int($par['padding_left'])) . ","
                        . "PADDING_TOP=" . ($par['padding_top']=='auto' ? 'null' : parse_db_int($par['padding_top'])) . ","
                        . "PADDING_RIGHT=" . ($par['padding_right']=='auto' ? 'null' : parse_db_int($par['padding_right'])) . ","
                        . "PADDING_BOTTOM=" . ($par['padding_bottom']=='auto' ? 'null' : parse_db_int($par['padding_bottom'])) . ","
                        . "LEGEND_X=" . ($par['legend_x']=='auto' ? 'null' : parse_db_int($par['legend_x'])) . ","
                        . "LEGEND_Y=" . ($par['legend_y']=='auto' ? 'null' : parse_db_int($par['legend_y'])) . ","
                        . "LEGEND_MODE='" . parse_db_string($par['legend_mode']) . "',"
                        . "PIE_WRITE_VALUES='" . parse_db_string($par['pie_write_values']) . "',"
                        . "PIE_RADIUS=" . ($par['pie_radius']=='auto' ? 'null' : parse_db_int($par['pie_radius']));
                
		$sqlquery .= " WHERE ID=" . parse_db_int($par['diag_id']);                
		$rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
        }
	
?>