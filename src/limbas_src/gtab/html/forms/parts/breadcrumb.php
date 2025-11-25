<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */
?>


<?php
if($verkn_relationpath):
?>

<nav style="--bs-breadcrumb-divider: '>'; <?=$style?>" aria-label="breadcrumb">
<ol class="breadcrumb mb-2">

<?php
$value = null;
$addfrom = explode(";",$verkn_relationpath);
foreach($addfrom as $key => $value){
    if($value){
        $tab = explode(",",$value);
        if($tab[0]){$brct = 0;}else{$brct = 1;}
        echo "<li class=\"breadcrumb-item\"><a href=\"#\" OnClick=\"jump_to_breadcrumb('$key');\">".$gtab['desc'][$tab[$brct]]."</a></li>";
    }
}
if($value) {
    echo "<li class=\"breadcrumb-item\">" . $gtab['desc'][$gtabid] . "</li>";
}
?>

</ol>
</nav>

<?php
endif;
?>