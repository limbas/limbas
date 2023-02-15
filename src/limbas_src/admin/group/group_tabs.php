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
    function f_3(PARAMETER) {
        document.form1.action.value = PARAMETER;
        document.form1.submit();
    }
</script>
<div id="lmbAjaxContainer" class="ajax_container" style="position:absolute;visibility:hidden;z-index:999" OnClick="activ_menu=1;"></div>

<div class="col-3 pe-0">
    <ul class="nav nav-tabs flex-column sticky-top h-100 border-0">

        <?php
        $links = [135,76,100,192,260,291,292,290,293];
        foreach($links as $link):
            if($LINK[$link]): ?>
                <li class="nav-item">
                    <a class="nav-link <?=($link == $activeTabLinkId)?'active bg-white border-end-0 border-bottom border-left border-top':' border-end'?>" href="#" onclick="<?=$LINK["link_url"][$link]?>" title="<?=$lang[$LINK["desc"][$link]]?>"><i class="lmb-icon <?=$LINK["icon_url"][$link]?>"></i> <?=$lang[$LINK["name"][$link]]?></a>
                </li>
            <?php
            endif;
        endforeach;
        ?>
        
        <li class="flex-grow-1 border-end"></li>

    </ul>
</div>

