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


require_once("admin/user/user_tree.dao");

# Buffer
ob_start();

function files1($LEVEL){
	global $userstruct;
	global $umgvar;

	if($LEVEL){
		echo "<TABLE CELLPADDING=\"0\" CELLSPACING=\"0\" BORDER=\"0\"><TR><TD WIDTH=\"20\">&nbsp;</TD><TD>\n";
	}
	$bzm = 0;
	while($userstruct[id][$bzm]){
		if($userstruct[level][$bzm] == $LEVEL){
			if(in_array($userstruct[id][$bzm],$userstruct[level])){
				$next = 1;
			}else{
				$next = 0;
			}

			if($userstruct[user_id][$bzm]){
				# --- Hauptgruppe ----
				if($userstruct[maingroup][$bzm]){
					if($userstruct[del][$bzm]){$iconclass = "lmb-user1-3";}
					elseif($userstruct[lock][$bzm]){$iconclass = "lmb-user1-2";}
					else{$iconclass = "lmb-user1-1";}
				# --- Untergruppe ----
				}else{
					if($userstruct[del][$bzm]){$iconclass = "lmb-user2-3";}
					elseif($userstruct[lock][$bzm]){$iconclass = "lmb-user2-2";}
					else{$iconclass = "lmb-user2-1";}
				}
				if($umgvar["clear_password"]){$pass = "<i>(".$userstruct["clearpass"][$bzm].")</i>";}else{$pass = "";}
				echo "<TABLE CELLPADDING=\"0\" CELLSPACING=\"0\" BORDER=\"0\"><TR><TD>$pic</TD><TD><i class=\"lmb-icon " .$iconclass. "\"></i></TD><TD>&nbsp;".$userstruct["name"][$bzm]."&nbsp;$pass </TD></TR></TABLE>\n";
			}else{
				echo "<TABLE CELLPADDING=\"0\" CELLSPACING=\"0\" BORDER=\"0\"><TR><TD>$pic</TD><TD><i class=\"lmb-icon lmb-folder-open\"></i></TD><TD>&nbsp;<b>".$userstruct[name][$bzm]."</b></TD></TR></TABLE>\n";
			}

			if($next){
				$tab = 20;
				files1($userstruct[id][$bzm]);
			}
		}
		$bzm++;
	}
	if($LEVEL){
		echo "</TD></TR></TABLE>\n";
	}
}
files1(0);


$output = ob_get_contents();
ob_end_clean();



echo "<DIV class=\"lmbPositionContainerMain small\"><TABLE BORDER=\"0\" cellspacing=\"1\" cellpadding=\"0\" class=\"tabfringe\"><tr><td>";

echo "<a href=\"main_admin.php?action=setup_user_overview&pdf=1\">".$lang[2355]."&nbsp;<i class=\"lmb-icon lmb-file-pdf\" border=\"0\"></i></a>";
#echo "<a href=\"USER/".$session["user_id"]."/temp/user_overview.pdf\">".$lang[2355]."&nbsp;<img src=\"pic/fileicons/pdf.gif\" border=\"0\"></a>";
echo "<hr><br><br>";


echo $output;

echo "</td></tr></table></div>";



if($pdf){

	$output = "<html><body>
	$output
	</body></html>";
	
	$filename = $umgvar["pfad"]."/USER/".$session["user_id"]."/temp/user_overview";
	
	#$url = explode("/",$umgvar["url"]);
	#$url[2] = "localhost";
	#$url = implode("/",$url);
	#$output = str_replace($umgvar["url"],$url,$output);
	
	
	if ($handle = fopen($filename.".html", "w")) {
		fwrite($handle, $output);
	}
	
	$cmd = "htmldoc --size 295x210mm --left 10mm --right 10mm --top 10mm --bottom 10mm --webpage --header ... --footer ... -f ".$filename.".pdf $filename.html";
	$pdfout = `$cmd`;
	
	if(file_exists("USER/".$session["user_id"]."/temp/user_overview.pdf")){
		echo "
			<script language=\"JavaScript\">
			document.location.href='USER/".$session["user_id"]."/temp/user_overview.pdf';
			</script>
		";
	}else{
		echo "pdf generation failed! check if htmldoc is installed.";
	}
}

?>

