<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */

/**
 * Class Background
 * Placeholder for background (only supported by mpdf)
 */
class Background extends AbstractHtmlPart {
    
    
    /**
     * @var boolean defines if this background is used on page one
     */
    protected $firstPage;


    /**
     * @var int number of the background shrink type
     */
    protected $shrinkMode;

    /**
     * @var boolean whether the background should be reapeated or not
     */
    protected $repeat;


    /**
     * @var boolean whether the image should be search in Limbas DMS or not
     */
    protected $useDMS;

    /**
     * @var string the source path of the background image
     */
    protected $imgPath;

    public function __construct($attributes, $options) {

        $this->firstPage = false;        
        if (array_key_exists('first-page',$options) && filter_var($options['first-page'], FILTER_VALIDATE_BOOLEAN)) {
            $this->firstPage = true;
        }
        
        $this->shrinkMode = (int)$options['shrink-mode'];
        if (!in_array($this->shrinkMode,[0,1,2,3,4,5,6])) {
            $this->shrinkMode = 6;
        }

        $this->repeat = false;
        if (array_key_exists('repeat',$options) && filter_var($options['repeat'], FILTER_VALIDATE_BOOLEAN)) {
            $this->repeat = true;
        }

        $this->useDMS = false;
        if (array_key_exists('use-dms',$options) && filter_var($options['use-dms'], FILTER_VALIDATE_BOOLEAN)) {
            $this->useDMS = true;
        }

        $this->imgPath = $attributes['value'];
        
        if ($this->useDMS) {
            $this->imgPath = $this->getLmbFilePath($this->imgPath);
        }
        
    }

    public function getAsHtmlArr() {
        
        $first = '';
        if ($this->firstPage) {
            $first = ':first';
        }
        
        $noRepeat = 'no-';
        if ($this->repeat) {
            $noRepeat = '';
        }
        
        $css = '@page ' . $first . ' {
                background: url("' . $this->imgPath . '") 0 0 ' . $noRepeat . 'repeat;
                background-image-resize: ' . $this->shrinkMode . ';
            }';

        return array('<style>',$css,'</style>');
    }

    /**
     * Resolves a given ID or path in the dms to the real path of the file
     * 
     * @param string|int $pathOrId the id or path to a file in Limbas dms
     * @return string
     */
    private function getLmbFilePath($pathOrId) {        
        global $db, $action;
        global $gmimetypes;

        require_once(COREPATH . 'extra/explorer/filestructure.lib');


        // path -> file id
        if (is_numeric($pathOrId)) {
            $fileID = $pathOrId;
        } else {
            $fileparts = explode('/', html_entity_decode($pathOrId));
            $filename = array_pop($fileparts);
            $folder = implode('/', $fileparts);
            $fileID = lmb_getFileIDFromName(lmb_getLevelFromPath($folder), $filename);
        }
        
        // file id -> secname
        $mttfilter = set_mttfilter()['where'];
        $sqlquery = 'SELECT SECNAME, MIMETYPE, LEVEL FROM LDMS_FILES WHERE ID='.parse_db_int($fileID).' '.$mttfilter;
        $rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
        $ext = $gmimetypes['ext'][lmbdb_result($rs, 'MIMETYPE')];
        #$secname = $umgvar['upload_pfad'] . lmbdb_result($rs, 'SECNAME') . '.' . $ext;
        $secname = lmb_getFilePath($fileID,lmbdb_result($rs, 'LEVEL'),lmbdb_result($rs, 'SECNAME'),$ext);
        
        return $secname;        
    }

}
