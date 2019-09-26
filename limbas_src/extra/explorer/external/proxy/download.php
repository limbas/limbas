<?php
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
require_once('lib/include.lib');

try {
    lmbExternalFileDownload($authToken, $fileID, $_REQUEST['sendas']);
} catch (Exception $e) {
    exit('Error: ' . $e->getMessage());
}
