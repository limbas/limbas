<?php


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