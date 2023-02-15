<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */
class LmbExternalStorage_Dropbox extends LmbExternalStorage {

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
        $filesize = filesize($localFilePath);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://content.dropboxapi.com/2/files/upload');
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_INFILE, $fh_res);
        curl_setopt($ch, CURLOPT_INFILESIZE, filesize($localFilePath));
        curl_setopt($ch, CURLOPT_BINARYTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, fread($fh_res, $filesize));
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Authorization: Bearer ' . $this->config['token'],
            'Dropbox-Api-Arg: {"path": "/' . $externalFilename . '", "mode": "overwrite", "mute": true}',
            'Content-Type: application/octet-stream'
        ));
        $responseStr = curl_exec($ch);
        curl_close ($ch);

        fclose($fh_res);

        if (!$responseStr or !($response = json_decode($responseStr, true)) or !array_key_exists('name', $response) or array_key_exists('error', $response)) {
            return false;
        }

        return true;
    }

    /**
     * Downloads and outputs the file from the external storage.
     *
     * @param $externalFilename string the filename to download
     * @return void
     */
    public function downloadFile($externalFilename) {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, 'https://content.dropboxapi.com/2/files/download');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Authorization: Bearer ' . $this->config['token'],
            'Dropbox-Api-Arg: {"path": "/' . $externalFilename . '"}',
            'Content-Type: application/octet-stream'
        ));
        $fileData = curl_exec($ch);
        $contentLength = curl_getinfo($ch, CURLINFO_CONTENT_LENGTH_DOWNLOAD);
        curl_close ($ch);

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
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, 'https://api.dropboxapi.com/2/sharing/create_shared_link_with_settings');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(array(
            'path' => '/' . $externalFilename,
            'settings' => array(
                'requested_visibility' => 'public'
            )
        )));
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Authorization: Bearer ' . $this->config['token'],
            'Content-Type: application/json'
        ));
        $responseStr = curl_exec($ch);
        curl_close ($ch);

        if (!$responseStr or !($response = json_decode($responseStr, true)) or !array_key_exists('url', $response) or array_key_exists('error', $response)) {
            if ($response['error']['.tag'] === 'shared_link_already_exists') {
                return $this->getExistingDownloadLink($externalFilename);
            }
            return false;
        }

        return $response['url'];
    }

    private function getExistingDownloadLink($externalFilename) {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, "https://api.dropboxapi.com/2/sharing/list_shared_links");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(array(
            'path' => '/' . $externalFilename
        )));
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Authorization: Bearer ' . $this->config['token'],
            'Content-Type: application/json'
        ));
        $responseStr = curl_exec($ch);
        curl_close ($ch);

        $response = json_decode($responseStr, true);
        if ($response and array_key_exists('links', $response) and lmb_count($response['links']) > 0 and $response['links'][0]['url']) {
            return $response['links'][0]['url'];
        }
        return false;
    }

}
