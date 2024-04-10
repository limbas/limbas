<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */



?>

<FORM ACTION="main_admin.php" METHOD="post" NAME="form1">
    <input type="hidden" name="action" value="setup_fonts">
    <input type="hidden" name="active" value="1">
    
    <div class="container-fluid p-3">

        <?php if(empty($fontex["family"])) : ?>
            <p>No fonts were found on the operating system!</p>
        <?php else: ?>

            <table class="table table-sm table-striped mb-0 border bg-contrast">
                <thead>
                <tr>
                    <th></th>
                    <th>Font</th>
                    <th>Style</th>
                    <th>Hersteller</th>
                    <th>Vorschau</th>
                </tr>
                <tr>
                    <td colspan="4"></td>
                    <td><input type="text" name="preview" value="<?=$text?>" OnChange="document.form1.submit();" class="form-control form-control-sm"></td>
                </tr>
                </thead>

                <tbody>
                <?php
                foreach ($fontex["family"] as $key => $value) :
                    if($fontex["file"][$key] AND lmb_strtolower($fontex["type"][$key]) == "ttf") :
                        $fontname = explode("/",$fontex["file"][$key]);
                        $fontname = $fontname[(lmb_count($fontname)-1)];
                        $fontname = lmb_substr($fontname,0,(lmb_strlen($fontname)-4));
                        if(!file_exists(TEMPPATH . "fonts/font_$key.gif")){
                            if($path = paintTextToImage($text,$size=12,$fontex["file"][$key])){
                                copy($path,TEMPPATH . "fonts/font_$key.gif");
                            }
                        }

                        $CHECKED = '';
                        $BGCOLOR = '';
                        $COLOR = '';

                        if($ifont[$fontname]){
                            $CHECKED = 'CHECKED';
                            $BGCOLOR = 'bg-light';
                        }
                        
                        ?>

                        <tr class="<?=$BGCOLOR?>"><td><input type="checkbox" name="nfnt[<?=$key?>]" value="1" <?=$CHECKED?>></td><td><?=$fontex["family"][$key]?></td><td><?=$fontex["style"][$key]?></td><td><?=$fontex["foundry"][$key]?></td><td><img src="TEMP/fonts/font_<?=$key?>.gif"></td></tr>
                    <?php

                    endif;
                endforeach;
                ?>
                </tbody>


                <tfoot>


                <tr>
                    <td colspan="5" class="text-end"><button class="btn btn-sm btn-primary" type="submit" name="set_fonts" value="1">Fonts de/installieren</button></td>
                </tr>

                </tfoot>




            </table>

        <?php endif; ?>
        
        

    </div>

</FORM>
