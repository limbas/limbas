<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */
class LmbExternalStorage_SeaFile extends LmbExternalStorage {

    private $authToken;

    public function __construct($config) {
        parent::__construct($config);

        # obtain auth token for later access
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $config['url'] . '/api2/auth-token/');
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, array(
            'username' => $config['user'],
            'password' => $config['pass']
        ));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $responseStr = curl_exec($ch);
        curl_close($ch);

        $response = json_decode($responseStr, true);
        $this->authToken = $response['token'];

        # set folder
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
        # default behavior should be overwrite -> update the file if it exists
        if ($this->fileExists($externalFilename)) {
            return $this->uploadFileCustom($localFilePath, $externalFilename, 'update');
        } else {
            return $this->uploadFileCustom($localFilePath, $externalFilename, 'upload');
        }
    }

    /**
     * Downloads and outputs the file from the external storage.
     *
     * @param $externalFilename string the filename to download
     * @return void
     */
    public function downloadFile($externalFilename) {
        if (!$this->authToken) {
            return;
        }

        # get private download link
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->config['url'] . '/api2/repos/' . $this->config['repo'] . '/file/?p=/' . $this->config['folder'] . $externalFilename);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Authorization: Token ' . $this->authToken
        ));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $linkStr = curl_exec($ch);
        $link = json_decode($linkStr);
        curl_close($ch);

        # download file
        $ch = curl_init($link);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
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
        if (!$this->authToken) {
            return false;
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->config['url'] . '/api2/repos/' . $this->config['repo'] . '/file/shared-link/');
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Authorization: Token ' . $this->authToken
        ));
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(array(
            'p' => '/' . $this->config['folder'] . $externalFilename
        )));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        $responseStr = curl_exec($ch);
        $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $headersStr = substr($responseStr, 0, $headerSize);
        curl_close($ch);

        $headersArr = explode("\r\n", $headersStr);
        foreach ($headersArr as $headerStr) {
            list ($name, $value) = explode(':', $headerStr, 2);
            if ($name === 'Location') {
                return trim($value);
            }
        }

        return false;
    }

    /**
     * Checks if a file exists
     *
     * @param $externalFilename string filename to check if it exists
     * @return bool true if existing, false otherwise
     */
    private function fileExists($externalFilename) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->config['url'] . '/api2/repos/' . $this->config['repo'] . '/file/detail/?p=/' . $this->config['folder'] . $externalFilename);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Authorization: Token ' . $this->authToken
        ));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $responseStr = curl_exec($ch);
        curl_close($ch);

        $response = json_decode($responseStr, true);
        if (array_key_exists('id', $response)) {
            return true;
        }
        return false;
    }

    /**
     * Uploads or updates a file, depending on $type
     *
     * @param $localFilePath string the path to the file to upload
     * @param $externalFilename string the new filename in the external storage
     * @param $type string either 'update' or 'upload'
     * @return bool success
     */
    private function uploadFileCustom($localFilePath, $externalFilename, $type) {
        if (!$this->authToken) {
            return false;
        }

        # get upload link
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->config['url'] . '/api2/repos/' . $this->config['repo'] . '/' . $type . '-link/?p=/' . $this->config['folder']);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Authorization: Token ' . $this->authToken
        ));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $uploadUrl = curl_exec($ch);
        curl_close($ch);

        if (!$uploadUrl) {
            return false;
        }
        $uploadUrl = trim($uploadUrl, '"');

        # post to upload link
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $uploadUrl);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Authorization: Token ' . $this->authToken
        ));
        curl_setopt($ch, CURLOPT_POST, 1);
        $file = curl_file_create($localFilePath, null, $externalFilename);
        if ($type === 'upload') {
            # upload
            curl_setopt($ch, CURLOPT_POSTFIELDS, array(
                'file' => $file,
                'filename' => $externalFilename,
                'parent_dir' => '/' . $this->config['folder']
            ));
        } else {
            # update
            curl_setopt($ch, CURLOPT_POSTFIELDS, array(
                'file' => $file,
                'filename' => $externalFilename,
                'target_file' => '/' . $this->config['folder'] . $externalFilename
            ));
        }
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $responseStr = curl_exec($ch);
        curl_close($ch);

        if ($responseStr) {
            $response = json_decode(str_replace("\n", '', $responseStr), true);
            if (is_array($response) and $response['error']) {
                return false;
            }
            return true;
        }

        return false;
    }

}
