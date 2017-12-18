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
 * ID: 177
 */

/* --- Diagramm löschen  ------------------------------ */
if($del AND $id){
	//pchart
	$sqlquery = "DELETE FROM LMB_CHART_LIST WHERE ID = $id";
	$rs = odbc_exec($db,$sqlquery) or errorhandle(odbc_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);

	$sqlquery = "DELETE FROM LMB_CHARTS WHERE CHART_ID = $id";
	$rs = odbc_exec($db,$sqlquery) or errorhandle(odbc_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
	
	$sqlquery = "DELETE FROM LMB_RULES_REPFORM WHERE REPFORM_ID = $id AND TYP = 3";
	$rs = odbc_exec($db,$sqlquery) or errorhandle(odbc_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
}

/* --- Diagramm erstellen  ---------------------------- */
if($new_diag AND $diag_name and $tab_id){
	//pchart
	$NEXTID = next_db_id("LMB_CHART_LIST");
	$sqlquery = "INSERT INTO LMB_CHART_LIST (ID,ERSTUSER,DIAG_NAME,TAB_ID) VALUES($NEXTID,".$session["user_id"].",'".parse_db_string($diag_name,80)."',$tab_id)";
	$rs = odbc_exec($db,$sqlquery) or errorhandle(odbc_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);

	$NEXTID2 = next_db_id("LMB_RULES_REPFORM");
	$sqlquery = "INSERT INTO LMB_RULES_REPFORM(ID,TYP,GROUP_ID,LMVIEW,REPFORM_ID) VALUES ($NEXTID2,3,".$session["group_id"].",".LMB_DBDEF_TRUE.",$NEXTID)";
	$rs = odbc_exec($db,$sqlquery) or errorhandle(odbc_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
}

/* --- Diagrammtyp ändern ----------------------------- */
if($diag_type AND $ID){
	//pchart
	$sqlquery = "UPDATE LMB_CHART_LIST SET DIAG_TYPE = '".trim(parse_db_string($diag_type,50))."',TEMPLATE='' WHERE ID = $ID";
	$rs = odbc_exec($db,$sqlquery) or errorhandle(odbc_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
}

/* --- Template ändern -------------------------------- */
if($template AND $ID){
	//pchart
	$sqlquery = "UPDATE LMB_CHART_LIST SET TEMPLATE = '".trim(parse_db_string($template,50))."' WHERE ID = $ID";
	$rs = odbc_exec($db,$sqlquery) or errorhandle(odbc_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
}

/* --- Transposed ändern ------------------------------ */
if($transposed AND $ID){
	//pchart
	$sqlquery = "UPDATE LMB_CHART_LIST SET TRANSPOSED = $transposed WHERE ID = $ID";
	$rs = odbc_exec($db,$sqlquery) or errorhandle(odbc_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
}

/* Get Diagram Settings */
$sqlquery = "SELECT 
				CHARTS.ID, 
				CHARTS.TAB_ID, 
				CHARTS.DIAG_NAME, 
				CHARTS.DIAG_DESC, 
				CHARTS.DIAG_TYPE, 
				CHARTS.TEMPLATE, 
				CHARTS.TRANSPOSED 
			FROM 
				LMB_CHART_LIST AS CHARTS, 
				LMB_RULES_REPFORM
			WHERE 
				LMB_RULES_REPFORM.REPFORM_ID = CHARTS.ID
				AND LMB_RULES_REPFORM.TYP = 3
				AND LMB_RULES_REPFORM.GROUP_ID = ".$session["group_id"];
$rs = odbc_exec($db,$sqlquery) or errorhandle(odbc_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
if(!$rs) {$commit = 1;}
$bzm = 1;
$gdiaglist = array();
while(odbc_fetch_row($rs, $bzm)) {
	$key = odbc_result($rs, "ID");
	$gtabid = odbc_result($rs, "TAB_ID");
	$gdiaglist[$gtabid]["id"][$key] = odbc_result($rs, "ID");
	$gdiaglist[$gtabid]["name"][$key] = odbc_result($rs, "DIAG_NAME");
	$gdiaglist[$gtabid]["desc"][$key] = odbc_result($rs, "DIAG_DESC");
	$gdiaglist[$gtabid]["type"][$key] = odbc_result($rs, "DIAG_TYPE");
	$gdiaglist[$gtabid]["grouplist"][$key] = odbc_result($rs, "TEMPLATE");
	$gdiaglist[$gtabid]["transposed"][$key] = odbc_result($rs, "TRANSPOSED");
	$bzm++;
}

?>

<Script language="JavaScript">
	function deleteDiagram(ID){
		var del = confirm('<?=$lang[2285]?>');
		if(del){
			document.location.href="main_admin.php?<?SID?>&action=setup_diag&del=1&id="+ID;
		}
	}
	function divclose(){
	
	}
</Script>


<FORM ACTION="main_admin.php" METHOD="post" name="form1">
<input type="hidden" name="<?echo $_SID;?>" value="<?echo session_id();?>">
<input type="hidden" name="action" value="setup_diag">
<input type="hidden" name="ID">
<input type="hidden" name="diag_name">
<input type="hidden" name="diag_type">
<input type="hidden" name="template">
<input type="hidden" name="transposed">
<input type="hidden" id="diag_id" name="diag_id" value="<?=$diag_id?>">
<input type="hidden" id="diag_tab_id" name="diag_tab_id" value="<?=$diag_tab_id?>">


<DIV class="lmbPositionContainerMain">
    <TABLE class="tabfringe" BORDER="0" cellspacing="0" cellpadding="0"><tr><td>


<?php
if((!$diag_id)or(!$diag_tab_id)){
?>

	<!-- Diagramm-Liste -->
	<TABLE BORDER="0" cellspacing="1" cellpadding="0" style="width: 100%">
		<TR class="tabHeader">
			<TD class="tabHeaderItem">ID</TD>
			<TD class="tabHeaderItem" COLSPAN=2></TD>
			<TD class="tabHeaderItem"><?=$lang[1187]?></TD>
			<TD class="tabHeaderItem"><?=$lang[2863]?></TD>
			<TD class="tabHeaderItem"><?=$lang[2864]?></TD>
			<TD class="tabHeaderItem"><?=$lang[2207]?></TD>
		</TR>

		<?php
		# Extension Files
		$extfiles = read_dir($umgvar["pfad"]."/EXTENSIONS",1);

		if($gdiaglist){
			foreach ($gdiaglist as $gtabid => $value0){
				echo "<tr class=\"tabSubHeader\"><td class=\"tabSubHeaderItem\" colspan=8>" . $gtab["desc"][$gtabid] . "</td></tr>";
				foreach ($gdiaglist[$gtabid]["id"] as $key => $value){
					echo "<TR class=\"tabBody\">";
					# ID
					echo "<TD ALIGN=\"CENTER\">$value</TD>";
					# Stift
					echo "<TD ALIGN=\"CENTER\">";
					//HREF=\"main_admin.php?$_SID&action=setup_tab&group_bzm=$bzm&tab_group=".$tabgroup_["id"][$bzm]."\"
					echo "<I OnClick=\"document.form1.diag_tab_id.value='$gtabid';document.form1.diag_id.value='$key';document.form1.submit();\" BORDER=\"0\" style=\"cursor:pointer\" class=\"lmb-icon lmb-pencil\"></i>";
					echo "</TD>";
					# Zahnrad
					//echo "<TD VALIGN=\"TOP\">";
					//onclick=\"activ_menu=1;ajaxEditTable(null,' $gtabid ',' $tab_group ')\"
					#echo "<A title=\"" . $lang[2689] . "\"><i class=\"lmb-icon lmb-cog-alt\" BORDER=\"0\" style=\"cursor:pointer\"></i></A>";
					//echo "</TD>";
					# Delete
					echo "<TD ALIGN=\"CENTER\"><I OnClick=\"deleteDiagram('".$gdiaglist[$gtabid]["id"][$key]."')\" BORDER=\"0\" style=\"cursor:pointer\" class=\"lmb-icon lmb-trash\"></i></TD>";
					# Name
					echo "<TD>".$gdiaglist[$gtabid]["name"][$key]."</TD>";
					# Diagrammtyp
					$diag_types = array("Line-Graph","Bar-Chart","Pie-Chart");
					echo "<TD>";
					echo "<SELECT OnChange=\"document.form1.ID.value='".$gdiaglist[$gtabid]["id"][$key]."';document.form1.diag_type.value=this.value;document.form1.submit();\" style=\"width:100px\"><OPTION value=\" \">";
					foreach ($diag_types as $diag_type_temp){
						if($gdiaglist[$gtabid]["type"][$key] == $diag_type_temp){$selected = "SELECTED";}else{$selected = "";}
						echo "<OPTION VALUE=\"" . $diag_type_temp . "\" " . $selected . ">" . $diag_type_temp;
					}
					echo "</SELECT>";
					echo "</TD>";
					# Transponiert
					echo "<TD>";
					echo "<INPUT type='checkbox' OnChange=\"document.form1.ID.value='".$gdiaglist[$gtabid]["id"][$key]."';document.form1.transposed.value=this.checked;document.form1.submit();\" " . (($gdiaglist[$gtabid]["transposed"][$key]==1)?"checked":"") . "></INPUT>";				
					echo "</TD>";
					# Template
                                        echo "<TD>";
					if($gdiaglist[$gtabid]["type"][$key] == ''){
                                            echo "<SELECT OnChange=\"document.form1.ID.value='".$gdiaglist[$gtabid]["id"][$key]."';document.form1.template.value=this.value;document.form1.submit();\" style=\"width:200px;\"><OPTION value=\" \">";
                                            foreach ($extfiles["name"] as $key1 => $filename){
                                                    if($extfiles["typ"][$key1] == "file" AND ($extfiles["ext"][$key1] == "php" OR $extfiles["ext"][$key1] == "inc")){
                                                            $path = lmb_substr($extfiles["path"][$key1],lmb_strlen($umgvar["pfad"]),100);
                                                            if($gdiaglist[$gtabid]["grouplist"][$key] == $path.$filename){$selected = "SELECTED";}else{$selected = "";}
                                                            echo "<OPTION VALUE=\"".$path.$filename."\" $selected>".str_replace("/EXTENSIONS/","",$path).$filename;
                                                    }
                                            }
                                            echo "</SELECT>";
					}
                                        echo "</TD>";
					echo "</TR>";
				}
			}
		}

		?>

		<TR><TD>&nbsp;</TD></TR>
		<TR><TD COLSPAN="8"><HR></TD></TR>
	</TABLE>

	<!-- Neues Diagramm -->
	<TABLE>
		<TR>
			<TD><?=$lang[1187]?></TD>
			<TD><?=$lang[164]?></TD>
		</TR>
		<TR>
			<TD><INPUT TYPE="TEXT" NAME ="diag_name" SIZE="20" MAXLENGTH="20"></TD>
			<TD>
			<SELECT NAME="tab_id"><OPTION>
			<?php
			foreach ($tabgroup["id"] as $key0 => $value0) {
				echo "<OPTION VALUE=\"".$value."\">(".$tabgroup["name"][$key0].")";
				foreach ($gtab["tab_id"] as $key => $value) {
					if($gtab["tab_group"][$key] == $value0){
						echo "<OPTION VALUE=\"".$value."\">&nbsp;&nbsp;".$gtab["desc"][$key];
					}
				}
			}
			?>
			</SELECT>
			</TD>
			<TD><INPUT TYPE="SUBMIT" VALUE="<?=$lang[1191]?>" NAME="new_diag"></TD>
		</TR>
	</TABLE>

<?php
}else{ 
	// Detail Diagram Page
	require_once("admin/diagram/diag_detail.php");
}
?>

</TD></TR></TABLE>
</td></tr></table></div>
</FORM>