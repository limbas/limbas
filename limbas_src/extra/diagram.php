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
 * ID: 6
 */


$gtabid = $gdiaglist['gtabid'][$diag_id];
if ($gdiaglist[$gtabid]["id"][$diag_id]) {
    if ($gdiaglist[$gtabid]["template"][$diag_id] AND file_exists($umgvar["pfad"] . $gdiaglist[$gtabid]["template"][$diag_id])) {
        require_once ($umgvar["pfad"] . $gdiaglist[$gtabid]["template"][$diag_id]);
    } else {
        require_once ('extra/diagram/diagram.php');
        $link = lmb_createDiagram($diag_id, $gsr, $filter);
        ?>
            <style>
                .diagramm-wrapper { 
                    overflow: auto; 
                    margin: -5px;
                }

                @media print {
                    .u-noprint { display: none !important; }
                    .diagramm-wrapper { overflow: visible; }
                    .tabprint {
                        border: 1px solid black;
                    }        
                }
            </style>
            
            <script type="text/javascript">
                function print() {
                    popup = window.open();
                    popup.document.write("<img src='<?= $link . "?" . time() ?>'></img>");                    
                    popup.focus();
                    popup.print();
                    popup.close();
                }
            </script>
            
            <div class="lmbPositionContainerMainTabPool">
                <table class="tabpool" style="width:500" border="0" cellpadding="0" cellspacing="0">
                    <tbody>
                        <tr class="u-noprint">
                            <td>
                                <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                    <tbody>
                                        <tr>
                                            <td class="tabpoolItemActive" nowrap=""><?= $lang[2118] ?></td>
                                            <td class="tabpoolItemSpace">&nbsp;</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                        <tr class="u-noprint">
                            <td class="tabpoolfringe">
                                <a href="<?=$link?>" title="<?=$lang[1612]?>" download='<?=preg_replace('/[^A-Za-z0-9_\-]/', '_', $gdiaglist[$gtabid]['name'][$diag_id]) . '.png'?>'>
                                    <i class='lmb-icon lmb-page-save'></i>
                                </a>
                                <a id="lmbButtonPrint" onclick="print();" title="<?=$lang[392]?>" download>
                                    <i class='lmb-icon lmb-print'></i>
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <td class="tabpoolfringe tabprint">
                                <div class='diagramm-wrapper'>
                                    <img src='<?= $link . "?" . time() ?>'></img>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

        <?php
    }
}
?>