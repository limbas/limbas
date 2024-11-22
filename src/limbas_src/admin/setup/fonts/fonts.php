<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */

use Limbas\admin\setup\fonts\Font;
?>

<form action="main_admin.php" method="post" name="form1">
    <input type="hidden" name="action" value="setup_fonts">
    <input type="hidden" name="active" value="1">
    
    <div class="container-fluid p-3">

        <?php if(empty($fonts)) : ?>
            <p>No fonts were found on the operating system!</p>
        <?php else: ?>

            <table class="table table-sm table-striped mb-0 border bg-contrast">
                <thead>
                <tr>
                    <th></th>
                    <th>Font</th>
                    <th>Style</th>
                    <th><?=e($lang[1745])?></th>
                    <th><?=e($lang[1739])?></th>
                </tr>
                </thead>

                <tbody>
                
                    <?php
                    /** @var Font $font */
                    foreach($fonts as $font) : ?>
    
                        <tr class="<?= $font->active && !$font->fileExists() ? 'table-danger' : '' ?>">
                            <td><input id="font-<?=$font->id?>" type="checkbox" name="setFont[]" value="<?=$font->id?>" <?= $font->active ? 'checked' : '' ?>></td>
                            <td><label for="font-<?=$font->id?>"><?=e($font->family)?></label></td>
                            <td><?=e($font->style)?></td>
                            <td><?=e($font->source)?></td>
                            <td>
                                <?php if($font->hasImage()): ?>
                                    <img src="localassets/images/fonts/font_<?=$font->id?>.png" alt="">
                                <?php endif; ?>
                            </td>
                        </tr>
                    
                    <?php endforeach; ?>
                
                </tbody>


                <tfoot>
                
                <tr>
                    <td colspan="5" class="text-end"><button class="btn btn-sm btn-primary" type="submit" name="set_fonts" value="1"><?=e($lang[33])?></button></td>
                </tr>

                </tfoot>




            </table>

        <?php endif; ?>

    </div>

</form>
