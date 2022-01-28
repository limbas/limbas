<?php
/*
 * Copyright notice
 * (c) 1998-2021 Limbas GmbH(support@limbas.org)
 * All rights reserved
 * This script is part of the LIMBAS project. The LIMBAS project is free software; you can redistribute it and/or modify it on 2 Ways:
 * Under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * Or
 * In a Propritary Software Licence http://limbas.org
 * The GNU General Public License can be found at http://www.gnu.org/copyleft/gpl.html.
 * A copy is found in the textfile GPL.txt and important notices to the license from the author is found in LICENSE.txt distributed with these scripts.
 * This script is distributed WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * This copyright notice MUST APPEAR in all copies of the script!
 * Version 4.3.36.1319
 */

/*
 * ID:
 */
?>

<FORM ACTION="main_admin.php" METHOD="post" NAME="form1">
<input type="hidden" name="action" value="setup_multitenant">
<input type="hidden" name="drop_multitenant" >


<DIV class="lmbPositionContainerMainTabPool">

<TABLE class="tabpool" BORDER="0" width="700" cellspacing="0" cellpadding="0"><TR><TD>

	<TABLE BORDER="0" cellspacing="0" cellpadding="0"><TR class="tabpoolItemTR">
	<TD nowrap class="tabpoolItemActive"><?=$lang[2962]?></TD>
	<?php #<TD nowrap class="tabpoolItemInactive" OnClick="document.location.href='main_admin.php?action=setup_multitenant&tab=2'">Rechte</TD>?>
	<TD class="tabpoolItemSpace"> </TD>
	</TR></TABLE>

	</TD></TR>

	<TR><TD class="tabpoolfringe">

	<TABLE BORDER="0" cellspacing="1" cellpadding="2" width="100%" class="tabBody">
	<TR></TR><TD>&nbsp;</TD><TR>

	<TR class="tabHeader">
            <TD></TD>
            <TD class="tabHeaderItem">ID</TD>
            <TD class="tabHeaderItem"><?=$lang[2235]?></TD>
            <TD class="tabHeaderItem"><?=$lang[4]?></TD>
            <?if($result_crypto_keys) {?><TD class="tabHeaderItem">Public-Key</TD><?}?>
    <?php

        if($umgvar['sync_mode'] == 0) {
            $sqlquery = "SELECT ID,NAME,SLAVE_URL FROM LMB_SYNC_SLAVES ORDER BY NAME";
            $rs = lmbdb_exec($db, $sqlquery) or errorhandle(lmbdb_errormsg($db), $sqlquery, $action, __FILE__, __LINE__);
            while (lmbdb_fetch_row($rs)) {
                $lmb_sync_slaves[lmbdb_result($rs, 'ID')] = lmbdb_result($rs, 'NAME');
            }
        }


        if($lmb_sync_slaves) {
             echo "<TD class=\"tabHeaderItem\">".$lang[2916]."</TD>";
        }

        echo  "</TR>";


        if($result_multitenant){
        foreach($result_multitenant['name'] as $mkey => $mval){
            echo "<TR>
            <TD style=\"width:20px\"><i class=\"lmb-icon lmb-trash\" onclick=\"document.form1.drop_multitenant.value=$mkey;document.form1.submit();\" style=\"cursor:pointer\" border=\"0\"></i></TD>
            <TD style=\"width:20px\">$mkey</TD>
            <TD><input type=\"text\" value=\"".$result_multitenant['mid'][$mkey]."\" name=\"multitenant[mid][$mkey]\" style=\"width:150px\" onchange=\"document.getElementById('multitenant_$mkey').value=1\";></TD>
            <TD><input type=\"text\" value=\"".$result_multitenant['name'][$mkey]."\" name=\"multitenant[name][$mkey]\" style=\"width:250\" onchange=\"document.getElementById('multitenant_$mkey').value=1\";></TD>


            <TD>";
            if($result_crypto_keys) {
                echo "<select name=\"multitenant[crypto_key][$mkey]\" style=\"width:250\" onchange=\"document.getElementById('multitenant_$mkey').value=1\";><option value=\"0\">";
                foreach($result_crypto_keys as $skey => $svalue){
                    if($result_multitenant['crypto_key'][$mkey] == $skey) {$SELECTED = 'SELECTED';}else{$SELECTED = '';};
                    echo "<option value=\"$skey\" $SELECTED>$svalue</option>";
                }
                echo "</select>";
            }
            echo "</TD>


            <TD>";
            if($lmb_sync_slaves) {
                echo "<select name=\"multitenant[syncslave][$mkey]\" style=\"width:250\" onchange=\"document.getElementById('multitenant_$mkey').value=1\";><option value=\"0\">";
                foreach($lmb_sync_slaves as $skey => $svalue){
                    if($result_multitenant['syncslave'][$mkey] == $skey) {$SELECTED = 'SELECTED';}else{$SELECTED = '';};
                    echo "<option value=\"$skey\" $SELECTED>$svalue</option>";
                }
                echo "</select>";
            }
            echo "</TD>
            
            
            
            <input type=\"hidden\" value=\"\" name=\"multitenant[edit][$mkey]\" id=\"multitenant_$mkey\">
            </TD>
            </TR>";
        }
        }
        echo "
        <TR class=\"tabBody\"><TD colspan=\"5\">&nbsp;</TD></TR>
        <TR class=\"tabBody\"><TD colspan=\"2\"></TD><TD><INPUT TYPE=\"submit\" VALUE=".$lang[522]." NAME=\"edit_multitenant\"></TD></TR>
        <TR></TR><TD colspan=\"6\"><HR></TD><TR>
        <TR>
	    <TD></TD><TD></TD>
	    <TD><input type=\"TEXT\" name=\"new_multitenant_id\" style=\"width:150\"></TD>
	    <TD><input type=\"TEXT\" name=\"new_multitenant_name\" style=\"width:250\"></TD>
	    <TD><input type=\"submit\" VALUE=\"".$lang[540]."\" name=\"add_multitenant\"></TD></TR>";
    ?>

	<TR class="tabFooter"><TD colspan="6"></TD></TR>
	</TABLE>

</td></tr></table>

</div>
</FORM>