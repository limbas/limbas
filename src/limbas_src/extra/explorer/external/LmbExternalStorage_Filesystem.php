<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */


// ignored - local filesystem instead used

return;

class LmbExternalStorage_Filesystem extends LmbExternalStorage {

    public function __construct($config) {
        $config['path'] = rtrim($config['path']) . '/';
        parent::__construct($config);
    }

    /**
     * Uploads a file to the external storage.
     * Any existing file having the same filename should be overwritten.
     *
     * @param $localFilePath string the path to the file to upload
     * @param $externalFilename string the new filename in the external storage
     * @return bool success
     */
    public function uploadFile($localFilePath, $externalFilename) {
        return move_uploaded_file($localFilePath, $this->config['path'] . $externalFilename);
    }

    /**
     * Downloads and outputs the file from the external storage.
     *
     * @param $externalFilename string the filename to download
     * @return void
     */
    public function downloadFile($externalFilename) {
        $path = $this->config['path'] . $externalFilename;
        if (!file_exists($path)) {
            return;
        }

        header('Content-Length: ' . filesize($path));
        readfile($path);
    }

    /**
     * Creates a public download link of the specified file.
     * This function must only be implemented, if the 'publicCloud' setting is active.
     *
     * @param $externalFilename string the filename to download when accessing the download link
     * @return string|false download link or false on error
     */
    public function createDownloadLink($externalFilename) {
        # Filesystem must not be used as public cloud!
        return false;
    }
}
