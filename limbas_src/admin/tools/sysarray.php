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


<div class="lmbPositionContainerMain">
<table border="0" cellspacing="0" cellpadding="1" class="tabfringe"><tr><td>

<?php
asort($globvars, SORT_FLAG_CASE | SORT_NATURAL);    

echo "<table clas=\"tabfringe\"><tr><td valign=\"top\"style=\"width:100px;\">";

foreach ($globvars as $key => $value){
	if($showarray == $value){$style="font-weight:bold;";}else{$style = "";}
	echo "<a class=\"link\" style=\"$style\" href=\"main_admin.php?action=setup_sysarray&showarray=$value\">".$value."</a></br>";
}

echo "</td><td valign=\"top\">";

if($showarray){
	if(is_array(${$showarray})){
		print_r_html(${$showarray}, $showarray, $style = "display: none; margin-left: 10px;");
	}else{
		echo "array is empty!";
	}
}

echo "</td></tr></table>";



# warhog at warhog dot net 12-Aug-2005
function print_r_html($arr, $adesc, $style = "display: none; margin-left: 10px;")
{ 
  ksort($arr, SORT_FLAG_CASE | SORT_NATURAL);
  static $i = 0; $i++;
  echo "\n<div id=\"array_tree_".$adesc."_".$i."\" class=\"array_tree_".$adesc."\">\n";
  foreach($arr as $key => $val)
  { switch (gettype($val))
   { case "array":
       echo "<a onclick=\"document.getElementById('array_tree_element_".$adesc."_".$i."').style.display = ";
       echo "document.getElementById('array_tree_element_".$adesc."_".$i."').style.display == 'block' ?";
       echo "'none' : 'block';\"\n";
       echo "name=\"array_tree_link_".$adesc."_".$i."\" href=\"#array_tree_link_".$adesc."_".$i."\">".htmlentities($key,ENT_QUOTES,$GLOBALS["umgvar"]["charset"])."</a><br />\n";
       echo "<div class=\"array_tree_element_".$adesc."_\" id=\"array_tree_element_".$adesc."_".$i."\" style=\"$style\">";
       print_r_html($val,$adesc);
       echo "</div>";
     break;
     case "integer":
       echo "<b>".htmlentities($key,ENT_QUOTES,$GLOBALS["umgvar"]["charset"])."</b> => <i>".htmlentities($val,ENT_QUOTES,$GLOBALS["umgvar"]["charset"])."</i><br />";
     break;
     case "double":
       echo "<b>".htmlentities($key,ENT_QUOTES,$GLOBALS["umgvar"]["charset"])."</b> => <i>".htmlentities($val,ENT_QUOTES,$GLOBALS["umgvar"]["charset"])."</i><br />";
     break;
     case "boolean":
       echo "<b>".htmlentities($key,ENT_QUOTES,$GLOBALS["umgvar"]["charset"])."</b> => ";
       if ($val)
       { echo "true"; }
       else
       { echo "false"; }
       echo  "<br />\n";
     break;
     case "string":
       echo "<b>".htmlentities($key,ENT_QUOTES,$GLOBALS["umgvar"]["charset"])."</b> => <code>".htmlentities($val,ENT_QUOTES,$GLOBALS["umgvar"]["charset"])."</code><br />";
     break;
     default:
       echo "<b>".htmlentities($key,ENT_QUOTES,$GLOBALS["umgvar"]["charset"])."</b> => ".gettype($val)."<br />";
     break; }
   echo "\n"; }
  echo "</div>\n"; }

?>


</td></tr></table></div>