<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */
global $farbschema;
global $lang;
global $action;
global $gformlist;
global $umgvar;
global $gfield;
global $gtab;

// display custmenu
if ($gtab["custmenu"][$gtabid]): ?>
    <script>
        top.openMenu(<?=$gtab["custmenu"][$gtabid]?>);
    </script>
<?php endif; ?>


<?php

# Formular
print_form();

?>


<?php

if (!isset($ID)) {
    #lmb_alert($lang["98"]);
    #echo "<Script language=\"JavaScript\">\ndocument.location.href='main.php?action=gtab_erg&gtabid=".$gtabid."';\n</SCRIPT>";
    echo "<Script language=\"JavaScript\">\n";
    echo "document.form1.action.value='gtab_erg';\n";
    echo "send_form(1);\n";
    echo "</Script>";
    return false;
}

?>

<?php
if ($filter["gwidth"][$gtabid]) {
    $sw = $filter["gwidth"][$gtabid];
} else {
    $sw = "1000px";
}
?>

<TABLE class="lmbfringeGtab" ID="gtabDetail" BORDER="0" CELLPADDING="0" CELLSPACING="0" style="width:<?= $sw ?>">


    <tbody style="display: table; width: 100%;">

    <?php

    # Formularreiter
    #if(count($gformlist[$gtabid]["id"]) > 0){
    #print_formtags($gtabid,0);
    #}
    ?>

    <tr>
        <td>

            <?php
            print_linktags($gtabid, $GLOBALS["verkn_poolid"], $ID, $gresult);
            ?>

        </td>
    </tr>

    <tr>
        <td>

            <?php
            print_menue($gtabid, $gresult, $GLOBALS["verkn_addfrom"], $readonly);
            ?>

        </td>
    </tr>
    <tr>
        <td>

            <?php
            print_notice($gtabid, $ID, $gresult);
            ?>

        </td>
    </tr>


    <?php

    if (isset($ID)): ?>

        <tr>
            <td>
                <table class="gtabBodyDetailFringe" style="width:100%">

                    <?php if ($filter["groupheader"][$gtabid]): ?>
                        <tr class="gtabBodyDetailTopSpace">
                            <td></td>
                        </tr>
                        <tr class="gtabBodyDetailSubheader">
                            <td>

                                <?php $gf = $filter["groupheaderKey"][$gtabid]; ?>

                                <table class="lmbGtabSubheaderTable" border="0" cellspacing="0" cellpadding="0">
                                    <TR>
                                        <td class="tabpoolItemSpaceGtab" style="width:20px;">&nbsp;&nbsp;</td>

                                        <?php
                                        foreach ($gfield[$gtabid]["sort"] as $gkey => $gvalue):
                                            if ($gfield[$gtabid]["field_type"][$gkey] == 100):
                                                if (!$gf) {
                                                    $gf = $gkey;
                                                }
                                                # ----------- Viewrule -----------
                                                if ($gfield[$gtabid]["viewrule"][$gkey]) {
                                                    $returnval = eval(trim($gfield[$gtabid]["viewrule"][$gkey]) . ";");
                                                    if ($returnval) {
                                                        continue;
                                                    }
                                                }
                                                if ($gf == $gkey) {
                                                    $class = "lmbGtabSubheaderActive";
                                                } else {
                                                    $class = "lmbGtabSubheaderInactive";
                                                }
                                                ?>

                                                <td nowrap class="<?=$class?>" OnClick="limbasSubheaderSelection(this,'<?=$gtabid?>','<?=$gkey?>','TABLE');"><?=$gfield[$gtabid]["spelling"][$gkey]?></td>
                                        
                                        <?php
                                            endif;
                                        endforeach;
                                        ?>

                                        <td class="tabpoolItemSpaceGtab">&nbsp;</td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    <?php endif; ?>

                    <tr class="gtabBodyDetailBody">
                        <td>


                            <div id="GtabTableBody" style="overflow:auto;">
                                <table class="gtabBodyDetailTab" width="100%" border="0" cellspacing="0"
                                       cellpadding="0">
                                    <?php
                                    defaultViewElements($gtabid, $ID, $gresult, $action, $readonly, $filter);
                                    ?>

                                </table>
                            </div>

                </table>
            </td>
        </tr>
    <?php else: ?>
        <tr class="gtabBodyDetailSubheader">
            <td>

                <br><span class="text-danger"><?= $lang[98] ?>!</span>

            </td>
        </tr>
    <?php endif; ?>

    <tr>
        <td>

            <TABLE width="100%" cellpadding=0 cellspacing=0 class="gtabFooterTAB">
                <TR class="gtabFooterTR">
                    <TD COLSPAN="2" ALIGN="LEFT">
                        <TABLE>
                            <TR>
                                <TD WIDTH="200">

                                    <?php

                                    if (isset($ID)):
                                    print_scroll($ID);
                                    ?>
                                </TD>
                                <TD>
                                    <?php if ($action != 'gtab_deterg' and !$readonly): ?>
                                        <INPUT class="submit" TYPE="button" NAME="lmbSbm" ID="lmbSbm"
                                               STYLE="cursor:pointer" value="<?= $lang[33] ?>"
                                               onclick="document.form1.action.value='gtab_change'; send_form('1');">
                                        <INPUT class="submit" TYPE="button" NAME="lmbSbmClose" ID="lmbSbmClose_<?=$gtabid?>_<?=$ID?>"
                                               STYLE="cursor:pointer;display:none" value="<?= $lang[2796] ?>"
                                               onclick="document.form1.action.value='gtab_change'; send_form(1,0,1);">
                                    <?php
                                    endif;
                                    endif;

                                    ?>

                                </TD>
                            </TR>
                        </TABLE>
                    </TD>
                </TR>
            </TABLE>
        </TD>
    </TR>
    <TR>
        <TD class="lmbGtabBottom"></TD>
    </TR>
    </tbody>
</TABLE>
