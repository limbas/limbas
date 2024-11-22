<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */

# parse file id
$fileID = intval($_REQUEST['ID']);
if (!$fileID) {
    exit('Error: No file id given!');
}

$authToken = $_REQUEST['authToken'];
if (!$authToken) {
    exit('Error: No auth token given!');
}

# TODO modify these paths
require_once('LmbExternalStorage.php');
require_once('externalStorage.lib');
require_once(COREPATH . 'lib/include.lib');

try {
    lmbExternalFileDownload($authToken, $fileID, $_REQUEST['disposition']);
} catch (Exception $e) {
    exit('Error: ' . $e->getMessage());
}
