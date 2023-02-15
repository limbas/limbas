<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */



require_once(COREPATH . 'extra/explorer/external/LmbExternalStorage.php');
global $filestruct;
global $externalStorage;

# ---------------- Datei-upload -----------------------
if(!is_numeric($LID) OR !$LINK[128] OR !$_FILES) {
    return;
}

$file_['file'] = $_FILES['file']['tmp_name'];
$file_['file_name'] = $_FILES['file']['name'];
$file_['file_type'] = $_FILES['file']['type'];
$file_['file_archiv'] = $file_archiv;


// upload using external storage / Filesystem using default storage
if ($storageID = $filestruct['storageID'][$LID] AND $externalStorage['className'][$storageID] != 'Filesystem' AND lmb_count($file_['file']) === 1) {
    lmb_StartTransaction();
    $ufileId = lmbExternalStorageUploadIntern($storageID, $file_, $f_tabid, $f_fieldid, $f_datid);
    lmb_EndTransaction($ufileId ? true : false);
// default upload using intern filesystem
} else {
    $ufileId = lmb_fileUpload($file_,$LID,array("datid" => $f_datid,"gtabid" => $f_tabid,"fieldid" => $f_fieldid),0,$dublicate);
}

# link file to dataset / not by versioning because versioning use same relation as old version
/*
if($f_tabid AND $f_fieldid AND $f_datid AND $dublicate['type'][1] != 'versioning'){
    $verkn = set_verknpf($f_tabid,$f_fieldid,$f_datid,$ufileId,0,0,0);
    $verkn["linkParam"]["LID"] = $LID;
    set_joins($f_tabid,$verkn);
}*/

function lmbExternalStorageUploadIntern($storageID, $file, $tabid, $fieldid, $datid) {
    global $LID;
    global $dublicate;
    global $db;
    global $action;

    # upload files to limbas (only create dataset, dont upload file)
    $fileID = lmb_fileUpload($file, $LID, array('datid' => $datid, 'gtabid' => $tabid, 'fieldid' => $fieldid), 3 /* external storage */, $dublicate);
    if (!$fileID) {
        return false;
    }

    # generate unique filename from id
    $fileKey = key($file['file']);
    $uniqueFileName = lmbUniqueFilename($file['file_name'][$fileKey], $fileID);

    # get cloud class instance
    $storageConfig = lmbGetExternalStorageConfig($storageID);
    try {
        $storage = LmbExternalStorage::fromConfig($storageConfig);
    } catch (Exception $e) {
        return false;
    }

    # upload to external storage
    if (!$storage->uploadFile($file['file'][$fileKey], $uniqueFileName)) {
        return false;
    }

    # create download link if cloud
    $downloadLink = null;
    if ($storageConfig['publicCloud']) {
        $downloadLink = $storage->createDownloadLink($uniqueFileName);
        if (!$downloadLink) {
            return false;
        }
    }

    # finish upload
    $dlLink = '';
    if ($downloadLink) {
        $dlLink = ",DOWNLOAD_LINK='" . parse_db_string($downloadLink) . "'";
    }
    $sqlquery = "UPDATE LDMS_FILES SET SECNAME='" . parse_db_string($uniqueFileName) . "' $dlLink WHERE STORAGE_ID IS NOT NULL AND ID=" . parse_db_int($fileID);
    $rs = lmbdb_exec($db, $sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
    if (!$rs) {
        return false;
    }

    return $fileID;
}

?>
