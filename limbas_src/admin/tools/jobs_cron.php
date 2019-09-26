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
 * ID:
 */


?>
<form action="main_admin.php" method="post" name="form1">
    <input type="hidden" name="action" VALUE="setup_jobs_cron">

<table>
    <tr><td>
<div class="lmbPositionContainerMain">


        <table class="tabfringe" width="100%" cellspacing="2" cellpadding="2" border="0">
            <thead>
            <tr class="tabHeader">
                <td class="tabHeaderItem"><?=$lang[2068]?></td>
                <td class="tabHeaderItem"><?=$lang[1749]?></td>

                <TD class="tabHeaderItem"><?=$lang[2074]?></TD>
                <TD class="tabHeaderItem"><?=$lang[2075]?></TD>
                <TD class="tabHeaderItem"><?=$lang[2076]?></TD>
                <TD class="tabHeaderItem"><?=$lang[1437]?></TD>
                <TD class="tabHeaderItem"><?=$lang[2078]?></TD>

                <td class="tabHeaderItem"><?=$lang[1242]?></td>
                <td class="tabHeaderItem"><?=$lang[126]?></td>
                <td class="tabHeaderItem"><?=$lang[2072]?></td>
                <td class="tabHeaderItem" align="center">start</td>
            </tr>
            </thead>
            <tbody>
                <?php

                $sqlquery = "SELECT LMB_CRONTAB.ID,KATEGORY,START,VAL,LMB_CRONTAB.ERSTDATUM,ACTIV,DESCRIPTION,ALIVE,JOB_USER,USERNAME FROM LMB_CRONTAB LEFT JOIN LMB_USERDB ON LMB_USERDB.ID = LMB_CRONTAB.JOB_USER ORDER BY KATEGORY";
                $rs = odbc_exec($db,$sqlquery) or errorhandle(odbc_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);

                $kategorie = null;
                while(odbc_fetch_row($rs)){
                    $kat = odbc_result($rs,"KATEGORY");

                    if ($kategorie != $kat) {
                        $kategorie = $kat;
                        echo '<tr class="tabSubHeader"><td class="tabSubHeaderItem" colspan="11">'.$kategorie.'</td></tr>';
                    }

                    $tid = odbc_result($rs,"ID");
                    $val = odbc_result($rs,"VAL");
                    $val0 = explode(";",$val);

                    $activ = '';
                    if(odbc_result($rs,"ACTIV")){
                        $activ = "CHECKED";
                    }
                    $template = '';
                    if(odbc_result($rs,"KATEGORY") == "TEMPLATE" OR odbc_result($rs,"KATEGORY") == "D"){
                        $template = $val0[0];
                    }

                    $crontime = explode(';',odbc_result($rs,"START"));


                    echo "
                                    <TR class=\"tabBody\" OnClick=\"show_val('');show_val('".$val."');\">
                                        <TD>&nbsp;".odbc_result($rs,"ID")."&nbsp;</TD>
                                        <TD>&nbsp;".$template."&nbsp;</TD>
                                        
                                        <TD>&nbsp;".$crontime[0]."&nbsp;</TD>
                                        <TD>&nbsp;".$crontime[1]."&nbsp;</TD>
                                        <TD>&nbsp;".$crontime[2]."&nbsp;</TD>
                                        <TD>&nbsp;".$crontime[3]."&nbsp;</TD>
                                        <TD>&nbsp;".$crontime[4]."&nbsp;</TD>
                                        
                                        <TD>&nbsp;".odbc_result($rs,"USERNAME")."&nbsp;</TD>
                                        <TD>&nbsp;".odbc_result($rs,"DESCRIPTION")."&nbsp;</TD>
                                        <TD>&nbsp;<INPUT TYPE=\"CHECKBOX\" STYLE=\"border:none;background-color:transparent;\" NAME=\"activ_".odbc_result($rs,"ID")."\" OnClick=\"document.location.href='main_admin.php?&action=setup_jobs_cron&kategorie=$kategorie&activate_job=".odbc_result($rs,"ID")."'\" $activ>&nbsp;</TD>
                                        <TD ALIGN=\"CENTER\">&nbsp;<i class=\"lmb-icon lmb-action\" STYLE=\"cursor:pointer;border:1px solid grey;\" NAME=\"activate_".odbc_result($rs,"ID")."\" OnClick=\"document.location.href='main_admin.php?&action=setup_jobs_cron&kategorie=$kategorie&run_job=".odbc_result($rs,"ID")."';limbasWaitsymbol(event,1);\"></i>&nbsp;</TD>
                                    </TR>";
                    $cronvalue[] = str_replace(";"," ",odbc_result($rs,"START"))." php \"".$umgvar["pfad"]."/cron.php\" ".odbc_result($rs,"ID");
                }

                ?>

                <tr><td class="tabFooter" colspan="3"></td></tr>
            </tbody>
        </table>

        <?php

        /* crontab value */
        $cronStr = '';
        if($cronvalue){
            foreach($cronvalue as $key => $value){
                $cronStr .= str_replace(";", " ", $value) . "\n";
            }
        }
        ?>
        <h4>Crontab value</h4>
        <textarea readonly="readonly" STYLE="width:100%;min-width:600px;height:100px;overflow:hidden;"><?= $cronStr ?></textarea>


        <table width="100%">
            <tr>
                <td align="RIGHT"><input type="submit" value="<?=$lang[1500]?>" name="preview_jobs_crontab"></td>
            </tr>
        </table>
</div>
            <?php if ($preview_jobs_crontab || $apply_jobs_crontab) { ?>
                <div class="lmbPositionContainerMain" style="min-height: 400px; height:400px">
                    <h3 style="margin: 0 0 5px 0"><?=$lang[1500]?></h3>
                    <textarea width="100%" height="90%" style="width:100%;height: 85%" readonly><?php echo get_cron_tab(); ?></textarea>
                    <table width="100%">
                        <tr>

                            <?php if ($apply_jobs_crontab_success) { ?>
                                <td align="LEFT" style="color:green"><?=$lang[2006]?></td>
                            <?php } ?>
                            <td align="RIGHT"><input type="submit" value="<?=$lang[2444]?>" name="apply_jobs_crontab"></td>
                        </tr>
                    </table>
                </div>
            <?php } ?>
        </td></tr>
</table>
</form>
