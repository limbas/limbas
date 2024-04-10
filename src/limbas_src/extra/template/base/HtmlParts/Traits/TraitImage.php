<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */
namespace Limbas\extra\template\base\HtmlParts\Traits;

trait TraitImage
{
    /**
     * Resolves a given ID or path in the dms to the real path of the file
     *
     * @param string|int $pathOrId the id or path to a file in Limbas dms
     * @return string
     */
    protected function getLmbFilePath($pathOrId) {
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
