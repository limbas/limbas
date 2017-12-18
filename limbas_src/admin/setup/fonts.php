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
 * ID:
 */

echo "
<FORM ACTION=\"main_admin.php\" METHOD=\"post\" NAME=\"form1\">
<input type=\"hidden\" name=\"action\" value=\"setup_fonts\">
<input type=\"hidden\" name=\"active\" value=\"1\">

<div class=\"lmbPositionContainerMain\">";

if(count($fontex["family"]) == 0) {
    echo "No fonts were found on the operating system!";
    exit;    
} else {
    echo "<table class=\"tabfringe\">
    <tr class=\"tabHeader\">
    <td class=\"tabHeaderItem\" valign=\"center\"></td>
    <td class=\"tabHeaderItem\">Font</td>
    <td class=\"tabHeaderItem\">Style</td>
    <td class=\"tabHeaderItem\">Hersteller</td>
    <td class=\"tabHeaderItem\">Vorschau&nbsp;&nbsp;<input type=\"text\" name=\"preview\" value=\"".$text."\" style=\"width:200px;border:none;background-color:".$farbschema[WEB9]."\" OnChange=\"document.form1.submit();\"></td>
    </tr>
    ";

    foreach ($fontex["family"] as $key => $value){
            if($fontex["file"][$key] AND strtolower($fontex["type"][$key]) == "ttf"){
                    $fontname = explode("/",$fontex["file"][$key]);
                    $fontname = $fontname[(count($fontname)-1)];
                    $fontname = substr($fontname,0,(strlen($fontname)-4));
                    if(!file_exists($umgvar["pfad"]."/TEMP/fonts/font_$key.gif")){
                            if($path = paintTextToImage($text,$size=12,$fontex["file"][$key])){
                                    copy($path,$umgvar["pfad"]."/TEMP/fonts/font_$key.gif");
                            }
                    }
                    if($ifont[$fontname]){$CHECKED = "CHECKED";$BGCOLOR=$farbschema["WEB9"];}else{$CHECKED = "";$BGCOLOR="";}
                    echo "<tr class=\"tabBody\" style=\"background-color:$BGCOLOR\"><td><input type=\"checkbox\" name=\"nfnt[".$key."]\" value=\"1\" $CHECKED></td><td>".$fontex["family"][$key]."</td><td>".$fontex["style"][$key]."</td><td>".$fontex["foundry"][$key]."</td><td><img src=\"TEMP/fonts/font_$key.gif\"></td></tr>\n";
            }
    }
    echo "<tr><td>&nbsp;</td></tr><tr><td colspan=\"5\"><input type=\"submit\" value=\"Fonts de/installieren\" name=\"set_fonts\"></td></tr>";
    echo "<tr class=\"tabFooter\"><td colspan=\"6\"></td></tr>";
    echo "</table><br><br>";
}
echo "</div></form>";
?>