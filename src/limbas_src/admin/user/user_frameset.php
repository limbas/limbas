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
    $(function (){
        let $userTree = $('#user_tree');
        let $userMain = $('#user_main');
        $('#user_tree_symbol').click(function() {
            $(this).addClass('d-none');
            $userTree.removeClass('d-none');
            $userMain.addClass('col-lg-9');
        }).on( 'limbas:hideside', function () {
            $(this).removeClass('d-none');
            $userTree.addClass('d-none');
            $userMain.removeClass('col-lg-9');
        });
    });
</script>

<div class="container-fluid h-100">
    <div class="d-flex h-100">
        <div class="flex-shrink-1 pt-3 px-2 text-center cursor-pointer d-none" id="user_tree_symbol">
            <i class="fas fa-bars fa-2x"></i>
        </div>
        <div class="row flex-grow-1 h-100">
            <div class="col-lg-3" id="user_tree"><iframe class="h-100 w-100" name="user_tree" src="main_admin.php?action=<?=$frame1para?>"></iframe></div>
            <div class="col-lg-9" id="user_main" ><iframe class="h-100 w-100" name="user_main" src="main_admin.php?action=<?=$frame2para?>&group_id=1"></iframe></div>
        </div>
    </div>
</div>
