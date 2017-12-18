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
 * ID: 54
 */



if (isset($text)) {

        $text = parse_db_string($text,4999);
        $sqlquery = "UPDATE VORLAGEN SET INHALT = '$text' WHERE ID = $ID AND USER_ID = $session[user_id]";
        $rs = odbc_exec($db,$sqlquery) or errorhandle(odbc_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);

        if($rs){
                require("extra/vorlagen/vorlagen_inhalt.dao");
        }else{
                lmb_alert($lang[195]);
        }


} else {
	?>
        <BR>
        <TABLE BORDER="0"><TR><TD WIDTH="20">&nbsp;</TD><TD>

        <style type="text/css">
        .btnImage {cursor: pointer; cursor: hand;}
        </style>

	<form name="form1" method="post" action="main.php">
        <input type="hidden" name="<?echo $_SID;?>" value="<?echo session_id();?>">
        <input type="hidden" name="action" VALUE="user_vorlage_change">
        <input TYPE="hidden" NAME="ID" VALUE="<?=$ID;?>">
        <input TYPE="hidden" NAME="text">

        <iframe id="testFrame" style="position: absolute; visibility: hidden; width: 0px; height: 0px;"></iframe>
        <script language="JavaScript" type="text/javascript" src="extra/richtext/browserdetect.js"></script>
        <script language="JavaScript" type="text/javascript">
        <!--
        function submitForm() {
                try {
                        text = document.getElementById('edit').contentWindow.document.body.innerHTML;
                }
                catch (e) {
                        text = document.getElementById('edit').value;
                }
                document.form1.text.value = text;
                document.form1.submit();
        }

        Start();
        //-->
        </script>

        <p><input type="button" value="<?=$lang[842]?>" onclick="submitForm()">&nbsp;&nbsp;<input type="button" value="<?=$lang[844]?>" onclick="window.close();"></p>

	</form>
        </TD></TR></TABLE>
	<?
}
?>
