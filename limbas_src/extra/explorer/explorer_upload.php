<?php
/*
 * Copyright notice
 * (c) 1998-2019 Limbas GmbH(support@limbas.org)
 * All rights reserved
 * This script is part of the LIMBAS project. The LIMBAS project is free software; you can redistribute it and/or modify it on 2 Ways:
 * Under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * Or
 * In a Propritary Software Licence http://limbas.org
 * The GNU General Public License can be found at http://www.gnu.org/copyleft/gpl.html.
 * A copy is found in the textfile GPL.txt and important notices to the license from the author is found in LICENSE.txt distributed with these scripts.
 * This script is distributed WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * This copyright notice MUST APPEAR in all copies of the script!
 * Version 3.6
 */

/*
 * ID:
 */

require_once('extra/explorer/external/LmbExternalStorage.php');
global $filestruct;

# ---------------- Datei-upload -----------------------
if(!is_numeric($LID) OR !$LINK[128] OR !$_FILES) {
    return;
}

$file_['file'] = $_FILES['file']['tmp_name'];
$file_['file_name'] = $_FILES['file']['name'];
$file_['file_type'] = $_FILES['file']['type'];
$file_['file_archiv'] = $file_archiv;

if ($storageID = $filestruct['storageID'][$LID] AND count($file_['file']) === 1) {
    lmb_StartTransaction();
    $ufileId = lmbExternalStorageUploadIntern($storageID, $file_, $f_tabid, $f_fieldid, $f_datid);
    lmb_EndTransaction($ufileId ? true : false);
} else {
    # default upload of multiple files
    $ufileId = upload($file_,$LID,array("datid" => $f_datid,"gtabid" => $f_tabid,"fieldid" => $f_fieldid),0,$dublicate);
}

# link file to dataset / not by versioning because versioning use same relation as old version
if($f_tabid AND $f_fieldid AND $f_datid AND $dublicate['type'][1] != 'versioning'){
    $verkn = set_verknpf($f_tabid,$f_fieldid,$f_datid,$ufileId,0,0,0);
    $verkn["linkParam"]["LID"] = $LID;
    set_joins($f_tabid,$verkn);
}

function lmbExternalStorageUploadIntern($storageID, $file, $tabid, $fieldid, $datid) {
    global $LID;
    global $dublicate;
    global $db;
    global $action;

    # upload files to limbas (only create dataset, dont upload file)
    $fileID = upload($file, $LID, array('datid' => $datid, 'gtabid' => $tabid, 'fieldid' => $fieldid), 3 /* external storage */, $dublicate);
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