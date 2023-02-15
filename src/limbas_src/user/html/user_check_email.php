<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */




echo "<BR>";
echo "<table border=\"0\" cellspacing=\"0\"><TR><TD>&nbsp;&nbsp;</TD><TD>";
echo "<B><U>Check Mailbox-settings</U></B><BR><BR>";

if($e_typ == "pop3"){
	$stream = imap_open("{".$e_server.":110/pop3/notls}INBOX", $e_user, $e_pass) or die("Could not open Mailbox, try again!");
}elseif($e_typ == "imap"){
	$stream = imap_open("{".$e_server.":143/notls}INBOX", $e_user, $e_pass) or die("Could not open Mailbox, try again!");
}


get_header($stream);


function get_header($stream){

	if ($hdr = imap_check($stream)) {
		echo "Messages <B>" . $hdr->Nmsgs ."</B>\n\n<br><br>";
		$msgCount = $hdr->Nmsgs;
	} else {
		echo "No Message";
	}
	$MN=$msgCount;
	$overview=imap_fetch_overview($stream,"1:$MN",0);
	$size=sizeof($overview);

	echo "<table border=\"0\" cellspacing=\"2\" width=\"600\">";
	echo "<tr bgcolor=\"#C0C0C0\"><td>from</td><td>subjekt</td><td>date</td></tr>";

	for($i=$size-1;$i>=0;$i--){
		$val=$overview[$i];
		$msg=$val->msgno;
		$from=$val->from;
		$date=$val->date;
		$subj=$val->subject;
		$seen=$val->seen;

		$from = str_replace("\"","",$from);

		list($dayName,$day,$month,$year,$time) = explode(' ',$date);
		$time = lmb_substr($time,0,5);
		$date = $day ." ". $month ." ". $year . " ". $time;

		if ($bgColor == "#F0F0F0") {
			$bgColor = "#FFFFFF";
		} else {
			$bgColor = "#F0F0F0";
		}

		if (lmb_strlen($subj) > 60) {
			$subj = lmb_substr($subj,0,20) ."...";
		}

		echo "<tr bgcolor=\"$bgColor\"><td>$from</td><td>$subj</td><td class=\"tblContent\">$date</td></tr>\n";
	}
	echo "</table>";
	imap_close($stream);

}

echo "</td></tr></table>";





?>
