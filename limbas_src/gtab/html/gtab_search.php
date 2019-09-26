<?php
/*
 * Copyright notice
 * (c) 1998-2019 Limbas GmbH(support@limbas.org)
 * All rights reserved
 * This script is part of the LIMBAS project. The LIMBAS project is free software; you can redistribute it and/or modify it on 2 Ways:
 * Under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * Or
 * In a Propritary Software Licence http://limbas.org
 * The GNU General Public License can be found at http://www.gnu.org/copyleft/gpl.html.
 * A copy is found in the textfile GPL.txt and important notices to the license from the author is found in LICENSE.txt distributed with these scripts.
 * This script is distributed WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * This copyright notice MUST APPEAR in all copies of the script!
 * Version 3.6
 */

/*
 * ID: 19
 */


// explorer extension
if($module == 'explorer_search') {
    $gsr = $GLOBALS['ffilter']['gsr'];
}

?>

<br>
<FORM ACTION="main.php" METHOD="post" name="form11" id="form11">
    <input type="hidden" name="action" value="gtab_erg">
    <input type="hidden" name="gtabid" value="<?= $gtabid ?>">
    <input type="hidden" name="gfrist">
    <input type="hidden" name="LID">
    <input type="hidden" name="next" value="1">
    <input type="hidden" name="supersearch" value="1">
    <input type="hidden" name="filter_reset">
    <input type="hidden" name="fieldid">


    <TABLE BORDER="0" cellspacing="0" cellpadding="3" style="padding:0px;width:100%">


        <?php
        echo '<tr><td nowrap>' . $lang[103] . '</td><td nowrap colspan="3" align="right" valign="top">';
        // search field
        echo '<div style="position: relative;"><div style="position: absolute;">';
        echo '<div style="position: absolute;"><input id="gdquicksearch" style="width:300px;height:19px;" onclick="lmb_searchDropdown(this, \'gdsearchfield\', 15);"></div>';
        echo '<div style="position: absolute;right:0;pointer-events:none;"><i class="lmb-icon lmb-caret-down"></i></div>';
        echo '<select id="gdsearchfield" style="width:300px;" onchange="limbasAddSearchPara(this)"><option value=0></option><option value=0></option><optgroup label="---'.$gtab['desc'][$gtabid].'---">';
        foreach ($gfield[$gtabid]["sort"] as $fkey => $value) {
            if ($gfield[$gtabid]['field_type'][$fkey] > 100 OR $gsr[$gtabid][$fkey][0]) {
                continue;
            }
            if($gfield[$gtabid]['field_type'][$fkey] == 100){
                echo "</optgroup><optgroup label=\"".$gfield[$gtabid]['spelling'][$fkey]."\">";
            }
            echo '<option VALUE="' . $fkey . '">' . $gfield[$gtabid]['spelling'][$fkey] . '</option>';
        }
        echo '</select>';
        echo '</div></div> </td><td align="right"><i class="lmb-icon lmb-camera-plus" TITLE="'.$lang[1602].'"></td><td colspan="3">';

        // snapshot
        if($gsnap[$gtabid]["id"]) {
            echo '<select id="gdssnapid" name="snap_id" style="width:100%" onchange="limbasDetailSearch(event,this,'.$gtabid.',null,null,this.value)"><option value="0">';
            foreach ($gsnap[$gtabid]["id"] as $snapkey => $snapID) {
                if($filter["snapid"][$gtabid] == $snapID){$SELECTED = 'SELECTED';}else{$SELECTED = '';}
                echo '<option value="' . $snapID . '" '.$SELECTED.'>' . $gsnap[$gtabid]["name"][$snapkey] . '</option>';
            }
            echo '</select>';
        }

        echo '</td></tr>';
        echo '<tr><td colspan="3">&nbsp;</td></tr>';

        // list of search fields
        #foreach($gsr[$gtabid] as $key => $gval){
        foreach ($gfield[$gtabid]["sort"] as $key => $gval) {

            $hasOptions = 0;

            for ($nkey = 0; $nkey <= ($umgvar['searchcount'] - 1); $nkey++) {

                $gsrres = $gsr[$gtabid][$key][$nkey];

                $st = '';
                $fm = '';
                $hascontent_first = 0;
                if ($gsr[$gtabid][$key][0] OR $gsr[$gtabid][$key]["txt"][0] >= 7) {
                    $hascontent_first = 1;
                }
                $hascontent_next = 0;
                if ($gsr[$gtabid][$key][$nkey] OR $gsr[$gtabid][$key]["txt"][$nkey] >= 7) {
                    $hascontent_next = 1;
                }


                if (!$hascontent_first OR ($nkey > 0 AND !$hascontent_next)) {
                    $st = 'display:none';
                    $fm = 'disabled';
                }

                echo '<tr style="' . $st . '" id="gdr_' . $key . '_' . $nkey . '" onclick="this.style.opacity=1">';


                // ###################### searchfield ########################

                if ($nkey > 0) {
                    echo '<td colspan="2"></td><td align="right">';

                    if (!$gsr[$gtabid][$key]['string'][0]) {
                        echo "<SELECT $fm NAME=\"gs[" . $gtabid . "][" . $key . "][andor][" . $nkey . "]\">";
                        echo "<OPTION VALUE=\"1\"";
                        if ($gsr[$gtabid][$key]["andor"][$nkey] == 1) {
                            echo " SELECTED";
                        }
                        echo ">" . $lang[854];
                        echo "<OPTION VALUE=\"2\"";
                        if ($gsr[$gtabid][$key]["andor"][$nkey] == 2) {
                            echo " SELECTED";
                        }
                        echo ">" . $lang[855];
                        echo "</SELECT>";
                    }

                    echo '</td>';

                } else {

                    echo '<td><i class="lmb-icon lmb-plus" style="cursor:pointer;" onclick="limbasExpandSearchPara(this,'.$key.')"></i></td>';
                    echo '<td colspan="2" align="right" style="min-width:120px">';
                    echo "<select $fm style=\"width:100%\">";

                    foreach ($gfield[$gtabid]["sort"] as $fkey => $value) {
                        if ($fkey == $key) {
                            echo '<option VALUE="' . $fkey . '" ' . $SELECTED . '>' . $gfield[$gtabid]['spelling'][$fkey] . '</option>';
                        }
                    }
                    echo '</select></td>';

                }



                // ###################### searchoptions ########################

                echo "<td>";

                if ($gfield[$gtabid]['field_type'][$key] == 2 OR $gfield[$gtabid]['field_type'][$key] == 7 OR $gfield[$gtabid]['field_type'][$key] == 5 OR $gfield[$gtabid]['field_type'][$key] == 12 OR $gfield[$gtabid]['field_type'][$key] == 15) {

                    echo "<SELECT $fm ID=\"gdsnum_" . $gtabid . "_" . $key . "_" . $nkey . "\" NAME=\"gs[" . $gtabid . "][" . $key . "][num][" . $nkey . "]\" STYLE=\"width:100px;\">";
                    echo "<OPTION VALUE=\"1\"";
                    if ($gsr[$gtabid][$key]["num"][$nkey] == 1) {
                        echo " SELECTED";
                    }
                    echo ">$lang[713]";
                    echo "<OPTION VALUE=\"2\"";
                    if ($gsr[$gtabid][$key]["num"][$nkey] == 2) {
                        echo " SELECTED";
                    }
                    echo ">$lang[711]";
                    echo "<OPTION VALUE=\"3\"";
                    if ($gsr[$gtabid][$key]["num"][$nkey] == 3) {
                        echo " SELECTED";
                    }
                    echo ">$lang[712]";
                    echo "<OPTION VALUE=\"5\"";
                    if ($gsr[$gtabid][$key]["num"][$nkey] == 5) {
                        echo " SELECTED";
                    }
                    echo ">$lang[711] $lang[713]";
                    echo "<OPTION VALUE=\"4\"";
                    if ($gsr[$gtabid][$key]["num"][$nkey] == 4) {
                        echo " SELECTED";
                    }
                    echo ">$lang[712] $lang[713]";
                    echo "<OPTION VALUE=\"6\"";
                    if ($gsr[$gtabid][$key]["num"][$nkey] == 6) {
                        echo " SELECTED";
                    }
                    echo ">$lang[2683]";

                    echo "<OPTION VALUE=\"7\"";
                    if ($gsr[$gtabid][$key]["num"][$nkey] == 7) {
                        echo " SELECTED";
                    }
                    echo ">$lang[2681]";
                    echo "<OPTION VALUE=\"8\"";
                    if ($gsr[$gtabid][$key]["num"][$nkey] == 8) {
                        echo " SELECTED";
                    }
                    echo ">$lang[2682]";

                    echo "</SELECT>";

                } else {
                    echo "<SELECT $fm ID=\"gdstxt_" . $gtabid . "_" . $key . "_" . $nkey . "\" NAME=\"gs[" . $gtabid . "][" . $key . "][txt][" . $nkey . "]\" STYLE=\"width:100px;\" OnChange=\"if(this.selectedIndex == 5){};limbasCheckforindex(this.value)\">";

                    echo "<optgroup label=\"Suchoptionen\">";

                    echo "<OPTION VALUE=\"2\"";
                    if ($gsr[$gtabid][$key]["txt"][$nkey] == 2 OR !$gsr[$gtabid][$key][$nkey]) {
                        echo " SELECTED";
                    }
                    echo ">$lang[107]";
                    echo "<OPTION VALUE=\"1\"";
                    if ($gsr[$gtabid][$key]["txt"][$nkey] == 1 OR (!$gsr[$gtabid][$key]["txt"][$nkey] AND $gsr[$gtabid][$key][$nkey])) {
                        echo " SELECTED";
                    }
                    echo ">$lang[106]";
                    echo "<OPTION VALUE=\"3\"";
                    if ($gsr[$gtabid][$key]["txt"][$nkey] == 3) {
                        echo " SELECTED";
                    }
                    echo ">$lang[108]";
                    if ($gfield[$gtabid]["data_type"][$key] == 39 OR $gfield[$gtabid]["data_type"][$key] == 13) {
                        echo "<OPTION VALUE=\"4\"";
                        if ($gsr[$gtabid][$key]["txt"][$nkey] == 4) {
                            echo " SELECTED";
                        }
                        echo ">$lang[1597]";
                    }

                    echo "<OPTION VALUE=\"7\"";
                    if ($gsr[$gtabid][$key]["txt"][$nkey] == 7) {
                        echo " SELECTED";
                    }
                    echo ">$lang[2681]";
                    echo "<OPTION VALUE=\"8\"";
                    if ($gsr[$gtabid][$key]["txt"][$nkey] == 8) {
                        echo " SELECTED";
                    }
                    echo ">$lang[2682]";

                    echo "</optgroup>";

                    echo "</SELECT>";

                    $hasOptions = 1;
                }

                echo "</td>";




                // ###################### searchvalue ########################


                echo "<td valign=\"top\" nowrap>";

                #------- BOOLEAN -------
                if ($gfield[$gtabid]['field_type'][$key] == 10) {

                    echo "<INPUT $fm TYPE=\"HIDDEN\" NAME=\"rules_$nkey\" VALUE=\"num_rules\">";
                    echo "<SELECT $fm STYLE=\"width:100%;\" ID=\"gds_" . $gtabid . "_" . $key . "_" . $nkey . "\" NAME=\"gs[" . $gtabid . "][" . $key . "][" . $nkey . "]\"><OPTION>
	                <OPTION VALUE=\"TRUE\"";
                    if ($gsrres == LMB_DBDEF_TRUE) {
                        echo " SELECTED";
                    }
                    echo ">$lang[1506]<OPTION VALUE=\"FALSE\"";
                    if ($gsrres == LMB_DBDEF_FALSE) {
                        echo " SELECTED";
                    }
                    echo ">$lang[1507]</SELECT>";

                    # ------- Selectfelder -------
                } elseif ($gfield[$gtabid]['field_type'][$key] == 4 AND $gfield[$gtabid]['artleiste'][$key]) {

                    echo "<SELECT $fm STYLE=\"width:100%;\" ID=\"gds_" . $gtabid . "_" . $key . "_" . $nkey . "\" NAME=\"gs[" . $gtabid . "][" . $key . "][" . $nkey . "]\" onClick=\"setTimeout('this.focus()',100);\"><OPTION>";
                    if (!$gfield[$gtabid]['select_sort'][$key]) {
                        $gfield[$gtabid]['select_sort'][$key] = "SORT";
                    }
                    $sqlquery = "SELECT DISTINCT WERT," . $gfield[$gtabid]['select_sort'][$key] . " FROM LMB_SELECT_W WHERE POOL = " . $gfield[$gtabid]['select_pool'][$key] . " ORDER BY " . $gfield[$gtabid]['select_sort'][$key];
                    $rs = odbc_exec($db, $sqlquery) or errorhandle(odbc_errormsg($db), $sqlquery, $action, __FILE__, __LINE__);
                    while (odbc_fetch_row($rs)) {
                        if (lmb_strtolower($gsrres) == lmb_strtolower(odbc_result($rs, "WERT"))) {
                            $SELECTED = "SELECTED";
                        } else {
                            $SELECTED = "";
                        }
                        echo "<OPTION VALUE=\"" . str_replace("\"", "", odbc_result($rs, "WERT")) . "\" $SELECTED>" . odbc_result($rs, "WERT");

                    }
                    echo "</SELECT>";
                    # ------- Upload/Memo (Index) ------
                } elseif ($gfield[$gtabid]["data_type"][$key] == 39 OR $gfield[$gtabid]["data_type"][$key] == 13) {

                    echo "<Input $fm TYPE=\"TEXT\" STYLE=\"width:100%;\" ID=\"gds_" . $gtabid . "_" . $key . "_" . $nkey . "\" NAME=\"gs[" . $gtabid . "][" . $key . "][" . $nkey . "]\" VALUE=\"" . $gsrres . "\"";
                    if ($nkey == 0) {
                        echo " OnChange=\"limbasCheckforindex(this.value,'$key','$gtabid')\">";
                    } else {
                        echo ">";
                    }

                    if ($gsr[$gtabid][$key]['string'][$nkey] AND $gsr[$gtabid][$key]['string'][$nkey + 1]) {
                        $dspl = '';
                    } else {
                        $dspl = 'display:none;';
                    }
                    echo "<i class=\"lmb-icon-cus lmb-pfeildown2\" ID=\"indpic_" . $key . "_" . $nkey . "\" STYLE=\"$dspl\"></i>";
                    echo "<INPUT $fm TYPE=\"HIDDEN\" ID=\"gds_" . $gtabid . "_" . $key . "_" . $nkey . "\" NAME=\"gs[" . $gtabid . "][" . $key . "][string][" . $nkey . "]\" VALUE=\"" . $gsr[$gtabid][$key]['string'][$nkey] . "\">";

                    # ------- Date ------
                } elseif ($gfield[$gtabid]["parse_type"][$key] == 4) {
                    echo "<input TYPE=\"TEXT\" STYLE=\"width:94%;\" ID=\"gds_" . $gtabid . "_" . $key . "_" . $nkey . "\" NAME=\"gs[" . $gtabid . "][" . $key . "][" . $nkey . "]\" VALUE=\"" . $gsrres . "\">";
                    $dateformat = $gfield[$gtabid]["datetime"][$key];
                    if ($gfield[$gtabid]["data_type"][$key] == 40) {
                        $dateformat = 1;
                    }
                    $dateformat = dateStringToDatepicker(setDateFormat($dateformat, 1));
                    echo "&nbsp;<i class=\"lmb-icon lmb-edit-caret\" style=\"cursor:pointer\" OnClick=\"lmb_datepicker(event,this,'',this.value,'" . $dateformat . "',20)\"></i>";

                } elseif ($gfield[$gtabid]["field_type"][$key] != 8) {

                    echo "<input $fm TYPE=\"TEXT\" STYLE=\"width:100%;\" ID=\"gds_" . $gtabid . "_" . $key . "_" . $nkey . "\" NAME=\"gs[" . $gtabid . "][" . $key . "][" . $nkey . "]\" VALUE=\"" . $gsrres . "\">";
                }


                echo "</td>";







                echo '<td nowrap>';

                #$hasOptions
                if ($hasOptions) {
                    echo "<i class=\"lmb-icon lmb-font\" TITLE=\"".$lang[109]."\"></i><INPUT $fm TYPE=\"CHECKBOX\" VALUE=\"1\" TITLE=\"".$lang[109]."\" NAME=\"gs[" . $gtabid . "][" . $gfield[$gtabid]["field_id"][$key] . "][cs][" . $nkey . "]\" STYLE=\"border:none;margin:0px\"";
                    if ($gsr[$gtabid][$gfield[$gtabid]["field_id"][$key]]["cs"][$nkey] == 1) {
                        echo " CHECKED";
                    }
                    echo ">";

                    echo "<i class=\"lmb-icon lmb-exclamation\" TITLE=\"".$lang[2683]."\"></i><INPUT $fm TYPE=\"CHECKBOX\" VALUE=\"1\" TITLE=\"".$lang[2683]."\" NAME=\"gs[" . $gtabid . "][" . $gfield[$gtabid]["field_id"][$key] . "][neg][" . $nkey . "]\" STYLE=\"border:none;margin:0px\"";
                    if ($gsr[$gtabid][$gfield[$gtabid]["field_id"][$key]]["neg"][$nkey] == 1) {
                        echo " CHECKED";
                    }
                    echo ">";
                }


                echo '<td>';


                if ($nkey == 0) {
                    $oc = '$(\'[id^=gds_' . $gtabid . '_' . $key . '_]\').val(\'\');$(\'[id^=gdr_' . $key . '_]\').hide();';
                } else {
                    $oc = '$(\'[id^=gds_' . $gtabid . '_' . $key . '_' . $nkey . ']\').val(\'\');$(\'[id^=gdr_' . $key . '_' . $nkey . ']\').hide();';
                }
                echo '<td><i class="lmb-icon lmb-close-alt" style="cursor:pointer;" onclick="' . $oc . '"></td>';

                echo '</tr>';

            }

        }


        echo '<tr><td colspan="8"><hr></td></tr>';

        if ($gsr[$gtabid]['andor'] == 2) {
            $or = "CHECKED";
        } else {
            $and = "CHECKED";
        }

        echo '
        <tr>
        <td></td><td><i class="lmb-icon lmb-globe" style="padding:2px"></i><select name="gs['.$gtabid.'][andor]"><option value="1" '.$and.'>'.$lang[854].'</option><option value="2" '.$or.'>'.$lang[855].'</option></select></td>
        <td colspan=2><i class="lmb-icon lmb-page-find" style="padding:2px"></i><INPUT TYPE="button" style="font-weight:bold;" VALUE="' . $lang[30] . '" NAME="search" OnClick="LmGs_sendForm(0,\''.$gtabid.'\');">
        </td><td colspan="5" align="right"><i class="lmb-icon lmb-undo" style="padding:2px"></i><INPUT TYPE="button" VALUE="' . $lang[1891] . '" NAME="search" OnClick="LmGs_sendForm(1);"></td></tr>
        ';

        ?>




    </TABLE>

</FORM>





<?php return; ?>



        <tr>
            <td colspan="4">

                <table id="lmbsearchhelp" style="display:none">
                    <tr>
                        <td colspan="2"><?= $lang[2693] ?>:</td>
                    </tr>
                    <tr>
                        <td><?= $lang[711] ?></td>
                        <td><b>> / >=</b></td>
                    </tr>
                    <tr>
                        <td><?= $lang[712] ?></td>
                        <td><b>< / <=</b></td>
                    </tr>
                    <tr>
                        <td><?= $lang[2683] ?></td>
                        <td><b>!=</b></td>
                    </tr>
                    <tr>
                        <td><?= $lang[2681] ?></td>
                        <td><b>#NULL#</b></td>
                    </tr>
                    <tr>
                        <td><?= $lang[2682] ?></td>
                        <td><b>#NOTNULL#</b></td>
                    </tr>
                    <tr>
                        <td colspan="2"><br>Date-Examples:</td>
                    </tr>
                    <tr>
                        <td>Date</td>
                        <td><b>01.05.2011 10:25</b></td>
                    </tr>                    <tr>
                        <td>Date</td>
                        <td><b>01 may 2011</b></td>
                    </tr>
                    <tr>
                        <td>Year</td>
                        <td><b>2011</b></td>
                    </tr>
                    <tr>
                        <td>Month of Year</td>
                        <td><b>05 2011</b></td>
                    </tr>
                    <tr>
                        <td>Week of Year</td>
                        <td><b>CW24 2011 | KW 24</b></td>
                    </tr>

                </table>
            </td>
        </tr>

    </TABLE>
</FORM>