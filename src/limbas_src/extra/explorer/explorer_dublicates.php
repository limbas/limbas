<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */


?>


<Script language="JavaScript">
function LmEx_delete(){
	var del = confirm("<?=$lang[822]?>");
	if(del){
		document.form1.delete.value = 1;
		document.form1.submit();
	}
}
</Script>

<?php
if($subdir = get_subdir($LID,1)){

	# delete file
	if($delete AND is_array($file)){
		foreach ($file as $key => $value){
			if($LINK[171] AND $filestruct["del"][$LID]){
				lmb_deleteFile($value);
			}
		}
	}
	
	
	

	$indir = implode(",",$subdir);
	
	echo "<div class=\"lmbPositionContainerMain\">";
	echo "<FORM ACTION=\"main.php\" METHOD=\"post\" name=\"form1\">";
	echo "<input type=\"hidden\" name=\"action\" value=\"explorer_dublicates\">";
	echo "<input type=\"hidden\" name=\"LID\" value=\"$LID\">";
	echo "<input type=\"hidden\" name=\"delete\">";

	echo "<TABLE style=\"width:100%\" class=\"tabfringe\">";
	echo "<TR class=\"tabHeader\"><TD class=\"tabHeaderItem\">".$lang[544]."</TD><TD class=\"tabHeaderItem\">".$lang[545]."</TD></TR>";
	
	$sqlquery = "SELECT LDMS_FILES.ID,LDMS_FILES.LEVEL,LDMS_FILES.NAME,LDMS_FILES.MD5 FROM LDMS_FILES,(SELECT MD5 FROM LDMS_FILES WHERE LEVEL IN ($indir) GROUP BY MD5 HAVING COUNT(MD5)>1) DBL WHERE LDMS_FILES.MD5 = DBL.MD5";
	$rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
	if(!$rs) {$commit = 1;}
	while(lmbdb_fetch_row($rs)) {
		
		$md5 = lmbdb_result($rs,"MD5");
		
		if($lastmd5 != $md5){
			echo "<TR class=\"tabBody\"><TD colspan=\"2\"><hr></TD></TR>";
		}
		
		echo "<TR class=\"tabBody\">
		<TD nowrap>
		<input type=\"checkbox\" NAME=\"file[".lmbdb_result($rs,"ID")."]\" VALUE=\"".lmbdb_result($rs,"ID")."\">
		</TD>
		<TD><A HREF=\"main.php?&action=download&ID=".lmbdb_result($rs,"ID")."\" TARGET=\"new\" style=\"font-style:italic\">".lmb_getUrlFromLevel(lmbdb_result($rs,"LEVEL"),lmbdb_result($rs,"ID")).lmbdb_result($rs,"NAME")."</A></TD></TR>";

		$lastmd5 = $md5;

	}
	
	echo "<TR class=\"tabBody\"><TD colspan=\"2\"><hr></TD></TR>";
	echo "<TR class=\"tabBody\"><TD colspan=\"2\"><INPUT TYPE=\"button\" VALUE=\"".$lang[160]."\" OnClick=\"LmEx_delete()\"></TD></TR>";
	
	echo "</TABLE>";
	echo "</div>";
	echo "</FORM>";
	
}


?>
