<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */


?>




<script>
    function edit_key(ID) {
        $(".crypto_keys_value").height(20);
        $("#crypto_keys_value_"+ID).height(300);
    }

    var lng_84 = '<?=$lang[84]?>';
</script>



<FORM ACTION="main_admin.php" METHOD="post" NAME="form1">
<input type="hidden" name="action" value="setup_keymanager">
<input type="hidden" name="drop_crypto_keys">
<input type="hidden" name="keytype" value="<?=$keytype?>">

<DIV class="lmbPositionContainerMainTabPool">

<TABLE class="tabpool" BORDER="0" width="800" cellspacing="0" cellpadding="0"><TR><TD>

	<TABLE BORDER="0" cellspacing="0" cellpadding="0"><TR class="tabpoolItemTR">
	<TD nowrap <?php if($keytype == 1){echo "class=\"tabpoolItemActive\"";}else{echo "class=\"tabpoolItemInactive\"";}?> onclick="document.location.href='main_admin.php?action=setup_keymanager&keytype=1'">Public Key</TD>
    <TD nowrap <?php if($keytype == 2){echo "class=\"tabpoolItemActive\"";}else{echo "class=\"tabpoolItemInactive\"";}?> onclick="document.location.href='main_admin.php?action=setup_keymanager&keytype=2'">Private Key</TD>
	<TD class="tabpoolItemSpace"> </TD>
	</TR></TABLE>

	</TD></TR>

	<TR><TD class="tabpoolfringe">

	<TABLE BORDER="0" cellspacing="1" cellpadding="2" width="100%" class="tabBody">
	<TR></TR><TD>&nbsp;</TD><TR>

	<TR class="tabHeader">
            <TD></TD>
            <TD class="tabHeaderItem">ID</TD>
            <TD class="tabHeaderItem"><?=$lang[4]?></TD>
            <TD class="tabHeaderItem"><?=$lang[926]?></TD>
            <TD class="tabHeaderItem" align="center">akiv</TD>
            <?php if ($umgvar['multitenant']){
                echo " <TD class=\"tabHeaderItem\">".$lang[2962]."</TD>";
            }?>

    <?php

        echo  "</TR>";

        if($result_crypto_keys){
        foreach($result_crypto_keys['name'] as $keyID => $keyName){

            $ca[$keyID][$result_crypto_keys['active'][$keyID]] = 'checked';

            echo "<TR>
            <TD valign=\"top\" style=\"width:20px\"><i class=\"lmb-icon lmb-trash\" onclick=\"if(confirm(lng_84)){document.form1.drop_crypto_keys.value=$keyID;document.form1.submit();}\" style=\"cursor:pointer\" border=\"0\"></i></TD>
            <TD valign=\"top\"style=\"width:20px\">$keyID</TD>
            <TD valign=\"top\"><input type=\"text\" value=\"".$result_crypto_keys['name'][$keyID]."\" name=\"crypto_keys[name][$keyID]\" style=\"width:150px\" onchange=\"document.getElementById('crypto_keys_$keyID').value=1\";></TD>
            <TD valign=\"top\"><textarea onclick=\"edit_key($keyID)\" class=\"crypto_keys_value\" id=\"crypto_keys_value_$keyID\" name=\"crypto_keys[value][$keyID]\" style=\"width:500px;height:20px\" onchange=\"document.getElementById('crypto_keys_$keyID').value=1\";>".$result_crypto_keys['key'][$keyID]."</textarea></TD>
            <TD valign=\"top\" align=\"center\"><input type=\"checkbox\" ".$ca[$keyID][1]." name=\"crypto_keys[active][$keyID]\" value=\"1\" style=\"width:50px\" onchange=\"document.getElementById('crypto_keys_$keyID').value=1\";></TD>
            ";
            if ($umgvar['multitenant']){
                $mtname = $lmmultitenants['name'][$result_crypto_keys['mid'][$keyID]];
                if(!$lmmultitenants['mid'][$result_crypto_keys['mid'][$keyID]] AND $keytype == 1){$mtname = 'global';}
                echo "<TD valign=\"top\" align=\"center\"><input type=\"text\" readonly value=\"$mtname\" style=\"width:100px\";></TD>";
            }
            echo "
            <input type=\"hidden\" value=\"\" name=\"crypto_keys[edit][$keyID]\" id=\"crypto_keys_$keyID\">
            </TD>
            </TR>";
        }
        }
        echo "
        <TR class=\"tabBody\"><TD colspan=\"5\">&nbsp;</TD></TR>
        <TR class=\"tabBody\">
        <TD colspan=\"2\"></TD><TD colspan=\"2\"><INPUT TYPE=\"submit\" VALUE=\"".$lang[522]."\" NAME=\"edit_crypto_keys\">&nbsp&nbsp;
        <INPUT TYPE=\"submit\" VALUE=\"erstellen\" NAME=\"create_crypto_keys\"></TD>
        </TR>
        <TR></TR><TD colspan=\"6\"><HR></TD><TR>
        <TR>
	    <TD></TD><TD></TD>
	    <TD><input type=\"TEXT\" name=\"new_crypto_keys[name]\" style=\"width:150\"></TD>";


        echo "<TD><select name=\"new_crypto_keys[mid]\" style=\"width:100\">
        <option>";
        foreach ($lmmultitenants['mid'] as $key => $value) {
            echo "<option value=\"$key\">",$lmmultitenants['name'][$key];
        }
        echo "</select></TD>";

        echo "<TD><input type=\"submit\" value=\"".$lang[540]."\" name=\"add_crypto_keys\"></TD>
	    </TR>";
    ?>

	<TR class="tabFooter"><TD colspan="5"></TD></TR>
	</TABLE>

</td></tr></table>

</div>
</FORM>
