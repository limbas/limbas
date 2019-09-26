<?php

/**
 * Class LmbExternalStorage can be inherited from to extend Limbas' DMS functionality
 */
abstract class LmbExternalStorage {

    /**
     * @var $config array can be set in Limbas to configure the behavior of the class
     */
    protected $config;

    public function __construct($config) {
        $this->config = $config;
    }

    /**
     * Uploads a file to the external storage.
     * Any existing file having the same filename should be overwritten.
     *
     * @param $localFilePath string the path to the file to upload
     * @param $externalFilename string the new filename in the external storage
     * @return bool success
     */
    public abstract function uploadFile($localFilePath, $externalFilename);

    /**
     * Downloads and outputs the file from the external storage.
     *
     * @param $externalFilename string the filename to download
     * @return void
     */
    public abstract function downloadFile($externalFilename);

    /**
     * Creates a public download link of the specified file.
     * This function must only be implemented, if the 'publicCloud' setting is active.
     *
     * @param $externalFilename string the filename to download when accessing the download link
     * @return string|false download link or false on error
     */
    public abstract function createDownloadLink($externalFilename);

    /**
     * Instantiates a LmbExternalStorage object from the given cloud configuration.
     * The classname depends on the 'className' setting (LmbExternalStorage_<className>).
     *
     * @param $cloudConfig array set in Limbas
     * @return LmbExternalStorage
     * @throws Exception
     */
    public static function fromConfig($cloudConfig) {
        $className = basename('LmbExternalStorage_' . $cloudConfig['className']);

        # class not loaded e.g. by extension?
        if (!class_exists($className)) {
            # load class from limbas
            $fileName = __DIR__ . '/' . $className . '.php';
            if (!file_exists($fileName)) {
                throw new Exception('No file ' . $fileName . ' found for class ' . $className);
            }
            require_once($fileName);
        }

        # still doesnt exist
        if (!class_exists($className)) {
            throw new Exception('No class ' . $className);
        }

        # return new object
        return new $className($cloudConfig['config']);
    }

}