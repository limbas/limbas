<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */

# tell browser to accept any incoming request
if($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: POST, OPTIONS');
    header('Content-Type: multipart/form-data');
    exit(0);
}

# limbas target information
$folderID = $_REQUEST['LID'];
if (!$folderID or lmb_count($_FILES['file']['name']) !== 1) {
    exit('Error: No folder or file.');
}
$fileKey = key($_FILES['file']['name']);

# unzipping currently not supported
if ($_REQUEST['file_archiv'] and $_REQUEST['file_archiv'][0] and $_REQUEST['file_archiv'][0] == 'true') {
    exit('Extraction is not supported for external storage!');
}

# TODO modify these paths
require_once('LmbExternalStorage.php');
require_once('externalStorage.lib');
require_once(COREPATH . 'lib/include.lib');

try {

    lmbExternalStorageUpload(
        $_REQUEST['authToken'],
        $_FILES['file']['name'][$fileKey],
        $_FILES['file']['tmp_name'][$fileKey],
        $_FILES['file']['type'][$fileKey],
        $folderID,
        $_REQUEST['gtabid'],
        $_REQUEST['fieldid'],
        $_REQUEST['ID'],
        $_REQUEST['dublicate']);

} catch (Exception $e) {
    exit('Error: ' . $e->getMessage());
}
