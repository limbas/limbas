<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */




require_once(COREPATH . 'admin/user/user_tree.dao');

# Buffer
ob_start();

function files1($LEVEL){
	global $userstruct;
	global $umgvar;

	if($LEVEL){
		echo "<TABLE CELLPADDING=\"0\" CELLSPACING=\"0\" BORDER=\"0\"><TR><TD WIDTH=\"20\">&nbsp;</TD><TD>\n";
	}
	$bzm = 0;
	while($userstruct['id'][$bzm]){
		if($userstruct['level'][$bzm] == $LEVEL){
			if(in_array($userstruct['id'][$bzm],$userstruct['level'])){
				$next = 1;
			}else{
				$next = 0;
			}

			if($userstruct['user_id'][$bzm]){
				# --- Hauptgruppe ----
				if($userstruct['maingroup'][$bzm]){
					if($userstruct['del'][$bzm]){$iconclass = "lmb-user1-3";}
					elseif($userstruct['lock'][$bzm]){$iconclass = "lmb-user1-2";}
					else{$iconclass = "lmb-user1-1";}
				# --- Untergruppe ----
				}else{
					if($userstruct['del'][$bzm]){$iconclass = "lmb-user2-3";}
					elseif($userstruct['lock'][$bzm]){$iconclass = "lmb-user2-2";}
					else{$iconclass = "lmb-user2-1";}
				}
				if($umgvar["clear_password"]){$pass = "<i>(".$userstruct["clearpass"][$bzm].")</i>";}else{$pass = "";}
				echo "<TABLE CELLPADDING=\"0\" CELLSPACING=\"0\" BORDER=\"0\"><TR><TD>$pic</TD><TD><i class=\"lmb-icon " .$iconclass. "\"></i></TD><TD>&nbsp;".$userstruct["name"][$bzm]."&nbsp;$pass </TD></TR></TABLE>\n";
			}else{
				echo "<TABLE CELLPADDING=\"0\" CELLSPACING=\"0\" BORDER=\"0\"><TR><TD>$pic</TD><TD><i class=\"lmb-icon lmb-folder-open\"></i></TD><TD>&nbsp;<b>".$userstruct['name'][$bzm]."</b></TD></TR></TABLE>\n";
			}

			if($next){
				$tab = 20;
				files1($userstruct['id'][$bzm]);
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
?>

<div class="container-fluid p-3">
	<div class="card">
		<div class="card-body">
			<p><a href="main_admin.php?action=setup_user_overview&pdf=1"><?=$lang[2355]?>&nbsp;<i class="lmb-icon lmb-file-pdf"></i></a></p>
			<?=$output?>
		</div>
	</div>
</div>

<?php



if($pdf){

	$output = "<html><body>
	$output
	</body></html>";
	
	$filename = USERPATH.$session["user_id"]."/temp/user_overview";
	
	#$url = explode("/",$umgvar["url"]);
	#$url[2] = "localhost";
	#$url = implode("/",$url);
	#$output = str_replace($umgvar["url"],$url,$output);
	
	
	if ($handle = fopen($filename.".html", "w")) {
		fwrite($handle, $output);
	}
	
	$cmd = "htmldoc --size 295x210mm --left 10mm --right 10mm --top 10mm --bottom 10mm --webpage --header ... --footer ... -f ".$filename.".pdf $filename.html";
	$pdfout = `$cmd`;
	
	if(file_exists(USERPATH.$session["user_id"]."/temp/user_overview.pdf")){
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

