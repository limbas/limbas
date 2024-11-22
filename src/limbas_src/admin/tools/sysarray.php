<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */


use Limbas\lib\auth\Session;

$globvars = Session::$globvars;

asort($globvars, SORT_FLAG_CASE | SORT_NATURAL);

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

<div class="container-fluid p-3">
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-2">
                    <?php
                    $activeglob = '';
                        foreach ($globvars as $key => $value){
                            $class = '';
                            if ($showarray == $value) {
                                $class='fw-bold';
                                $activeglob = $value;
                            }
                            echo "<a class=\"$class\" href=\"main_admin.php?action=setup_sysarray&showarray=$value\">".$value."</a></br>";
                        }
                    ?>
                </div>
                <div class="col-10">
                    <?php 
                        if ($activeglob) {
                            echo "<h3>$activeglob</h3>";
                        }    
                    
                        if($showarray){
                            if(is_array(${$showarray})){
                                print_r_html(${$showarray}, $showarray, $style = "display: none; margin-left: 10px;");
                            }else{
                                echo "array is empty!";
                            }
                        }
                    ?>
                </div>
            </div>
            
        </div>
    </div>
</div>
