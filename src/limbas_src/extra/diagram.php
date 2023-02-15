<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */




$gtabid = $gdiaglist['gtabid'][$diag_id];
if ($gdiaglist[$gtabid]["id"][$diag_id]) {
    if ($gdiaglist[$gtabid]["template"][$diag_id] AND file_exists($umgvar["pfad"] . $gdiaglist[$gtabid]["template"][$diag_id])) {
        require_once ($umgvar["pfad"] . $gdiaglist[$gtabid]["template"][$diag_id]);
    } else {
        require_once (COREPATH . 'extra/diagram/LmbChart.php');
        $link = LmbChart::makeChart($diag_id, $gsr, $filter, $verkn, $extension);
        $link = trim(str_replace(DEPENDENTPATH,'',$link),'/');
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
                                <a href="<?=$link?>" title="<?=$lang[1612]?>" download='<?=preg_replace('/[^a-z0-9_\-]/i', '_', $gdiaglist[$gtabid]['name'][$diag_id]) . '.png'?>'>
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
                                    <img src='<?= $link . "?" . time() ?>'>
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
