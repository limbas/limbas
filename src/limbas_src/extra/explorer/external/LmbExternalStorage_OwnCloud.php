<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */
class LmbExternalStorage_OwnCloud extends LmbExternalStorage {

    public function __construct($config) {
        parent::__construct($config);
        if ($this->config['folder']) {
            $this->config['folder'] = trim($this->config['folder'], '/') . '/';
        } else {
            $this->config['folder'] = '';
        }
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
        $fh_res = fopen($localFilePath, 'r');

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->config['url'] . '/remote.php/webdav/' . $this->config['folder'] . $externalFilename);
        curl_setopt($ch, CURLOPT_USERPWD, $this->config['user'] . ':' . $this->config['pass']);
        curl_setopt($ch, CURLOPT_PUT, 1);
        curl_setopt($ch, CURLOPT_INFILE, $fh_res);
        curl_setopt($ch, CURLOPT_INFILESIZE, filesize($localFilePath));
        curl_setopt($ch, CURLOPT_BINARYTRANSFER, TRUE);
        $success = curl_exec($ch);
        curl_close($ch);

        fclose($fh_res);

        return $success;
    }

    /**
     * Downloads and outputs the file from the external storage.
     *
     * @param $externalFilename string the filename to download
     * @return void
     */
    public function downloadFile($externalFilename) {
        # get file content
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->config['url'] . '/remote.php/webdav/' . $this->config['folder'] . $externalFilename);
        curl_setopt($ch, CURLOPT_USERPWD, $this->config['user'] . ':' . $this->config['pass']);
        $fileData = curl_exec($ch);
        $contentLength = curl_getinfo($ch, CURLINFO_CONTENT_LENGTH_DOWNLOAD);
        curl_close($ch);

        # output file
        header('Content-Length: ' . $contentLength);
        echo $fileData;
    }

    /**
     * Creates a public download link of the specified file.
     * This function must only be implemented, if the 'publicCloud' setting is active.
     *
     * @param $externalFilename string the filename to download when accessing the download link
     * @return string|false download link or false on error
     */
    public function createDownloadLink($externalFilename) {
        $url = $this->config['url'] . '/ocs/v1.php/apps/files_sharing/api/v1/shares';
        $post = array(
            'name' => 'lmbDownload',
            'path' => $this->config['folder'] . $externalFilename,
            'shareType' => 3, /* public link */
            'expireDate' => (new DateTime('tomorrow'))->format('Y-m-d')
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_USERPWD, $this->config['user'] . ':' . $this->config['pass']);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $responseStr = curl_exec($ch);
        curl_close($ch);

        if (!$responseStr) {
            return false;
        }

        $response = new SimpleXMLElement($responseStr);
        $statusCode = $response->meta->statuscode;
        if ($statusCode != 100) {
            return false;
        }

        $baseDownloadUrl = $response->data->url;
        if (!$baseDownloadUrl) {
            return false;
        }

        return rtrim($baseDownloadUrl, '/');
    }

}
