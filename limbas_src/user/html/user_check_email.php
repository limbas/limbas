<?php
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
 * ID: 45
 */


echo "<BR>";
echo "<table border=\"0\" cellspacing=\"0\"><TR><TD>&nbsp;&nbsp;</TD><TD>";
echo "<B><U>Check Mailbox-settings</U></B><BR><BR>";

if($e_typ == "pop3"){
	$stream = imap_open("{".$e_server.":110/pop3/notls}INBOX", $e_user, $e_pass) or die("Could not open Mailbox, try again!");
}elseif($e_typ == "imap"){
	$stream = imap_open("{".$e_server.":143/notls}INBOX", $e_user, $e_pass,PLAIN) or die("Could not open Mailbox, try again!");
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