<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Access denied</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
        <meta charset="utf-8">
        
        <style>
            body, html {
                width: 100%;
                height: 100%;
                font-family: sans-serif;
                overflow: hidden;
            }
            .flex-container {
                width: 100%;
                height: 100%;
                display: flex;
                justify-content: center;
                align-items: center;
            }
            
            .text-center {
                text-align: center;
            }
        </style>
    </head>
    <body>
        <div class="flex-container">
            <div class="text-center">
                <?php
                    $topLeft = 'assets/images/logo_topleft.png';
                    if(file_exists(LOCALASSETSPATH . 'images/logo_topleft.png')){
                        $topLeft = 'localassets/images/logo_topleft.png';
                    }
                ?>
                <img src="<?=$topLeft?>" alt="" style="max-height: 50px;" />
                <br><br>🚫 Access denied!</div>
        </div>
    </body>
</html>

