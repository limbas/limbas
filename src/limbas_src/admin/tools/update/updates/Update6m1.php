<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */


namespace Limbas\admin\tools\update\updates;

use Limbas\admin\tools\update\Update;

class Update6m1 extends Update
{

    public function __construct()
    {
        $this->id = 5;
        $this->major = 6;
        $this->minor = 1;
    }


    /**
     * update umgvar: add sync_ignore_inactive
     * @return bool
     */
    protected function patch0(): bool
    {
        $exists = $this->valueExistsInDb('LMB_UMGVAR', 'FORM_NAME', 'sync_ignore_inactive');
        if ($exists) {
            return true;
        }
        $nextId = next_db_id('LMB_UMGVAR');
        return $this->databaseUpdate("INSERT INTO LMB_UMGVAR VALUES($nextId,$nextId,'sync_ignore_inactive','0','If checked, inactive clients are ignored in global data synchronisation',2818,'bool',". LMB_DBDEF_NULL . ")");
    }

    /**
     * update umgvar: add report_resolve_cache
     * @return bool
     */
    protected function patch1(): bool
    {
        $exists = $this->valueExistsInDb('LMB_UMGVAR', 'FORM_NAME', 'report_resolve_cache');
        if ($exists) {
            return true;
        }
        $nextId = next_db_id('LMB_UMGVAR');
        return $this->databaseUpdate("INSERT INTO LMB_UMGVAR VALUES($nextId,$nextId,'report_resolve_cache','0','If checked, reports are cached',2818,'bool',". LMB_DBDEF_NULL . ")");
    }

    /**
     * increase SECNAME of LMB_FILES
     * @return bool
     */
    protected function patch2(): bool
    {
        global $DBA;

        $sqlQueries = [
            "UPDATE LMB_CONF_FIELDS SET FIELD_SIZE = 50 WHERE ID = (SELECT ID FROM LMB_CONF_FIELDS WHERE UPPER(FIELD_NAME) = 'SECNAME' AND TAB_ID = (SELECT TAB_ID FROM LMB_CONF_TABLES WHERE UPPER(TABELLE) = 'LDMS_FILES'))",
            dbq_15(array($DBA['DBSCHEMA'],'LDMS_FILES','SECNAME','VARCHAR(50)'))
        ];
        
        return $this->databaseUpdate($sqlQueries);
    }

    /**
     * increase EXTENSION of LMB_ACTION
     * @return bool
     */
    protected function patch3(): bool
    {
        global $DBA;
        return $this->databaseUpdate(dbq_15(array($DBA['DBSCHEMA'],'LMB_ACTION','EXTENSION','VARCHAR(250)')));
    }

    /**
     * add INACTIVELINK to LMB_CONF_FIELDS
     * @return bool
     */
    protected function patch4(): bool
    {
        return $this->databaseUpdate('ALTER TABLE LMB_CONF_FIELDS ADD INACTIVELINK '.LMB_DBTYPE_BOOLEAN);
    }

    /**
     * add CHECKBOXSELECT to LMB_CONF_TABLES
     * @return bool
     */
    protected function patch5(): bool
    {
        return $this->databaseUpdate('ALTER TABLE LMB_CONF_TABLES ADD CHECKBOXSELECT '.LMB_DBTYPE_BOOLEAN);
    }

    /**
     * update umgvar: add archive_download_metadata
     * @return bool
     */
    protected function patch6(): bool
    {
        $exists = $this->valueExistsInDb('LMB_UMGVAR', 'FORM_NAME', 'archive_download_metadata');
        if ($exists) {
            return true;
        }
        $nextId = next_db_id('LMB_UMGVAR');
        return $this->databaseUpdate("INSERT INTO LMB_UMGVAR VALUES($nextId,$nextId,'archive_download_metadata','0','If checked, downloading an archive in the DMS will additionally export the Metadata as a XML file',1899,'bool',". LMB_DBDEF_NULL . ")");
    }

    /**
     * add DETAILFORM_EDITMODE to LMB_CONF_TABLES
     * @return bool
     */
    protected function patch7(): bool
    {
        return $this->databaseUpdate('ALTER TABLE LMB_CONF_TABLES ADD DETAILFORM_VIEWMODE '.LMB_DBTYPE_NUMERIC.'(1)');
    }


    /**
     * update lmb_umgvar
     * @return bool
     */
    protected function patch8(): bool
    {

        $sqlQueries = [
            "UPDATE LMB_UMGVAR SET FORM_NAME = 'detail_openas', FIELD_OPTIONS = '[\"same\",\"modal\",\"tab\",\"window\"]',SORT = 83,BESCHREIBUNG = 'default modal opener', NORM='same' WHERE FORM_NAME = 'modal_opener'",
            "UPDATE LMB_UMGVAR SET BESCHREIBUNG = 'update metadata to file (jpg,jpeg,tiff,pdf,gif)' WHERE FORM_NAME = 'update_metadata'",
            "UPDATE LMB_UMGVAR SET BESCHREIBUNG = 'read metadata from file (jpg,jpeg,tiff,pdf,gif)' WHERE FORM_NAME = 'read_metadata'"
            ];

        return $this->databaseUpdate($sqlQueries);
    }

    /**
     * update umgvar: add detail_rel_openas
     * @return bool
     */
    protected function patch9(): bool
    {
        $exists = $this->valueExistsInDb('LMB_UMGVAR', 'FORM_NAME', 'detail_rel_openas');
        if ($exists) {
            return true;
        }
        $nextId = next_db_id('LMB_UMGVAR');
        return $this->databaseUpdate("INSERT INTO LMB_UMGVAR VALUES($nextId,84,'detail_rel_openas','modal','default modal opener for relations',1896,'select','[\"same\",\"modal\",\"tab\",\"window\"]')");
    }

    /**
     * import systemtables
     * @return bool
     */
    protected function patch10(): bool
    {
        $tables = array('LMB_LANG', 'LMB_ACTION', 'LMB_FIELD_TYPES');
        return $this->importTables($tables);
    }

    /**
     * add USEVIEWPARAMS to LMB_CONF_TABLES
     * @return bool
     */
    protected function patch11(): bool
    {

        $sqlQueries = [
            'ALTER TABLE LMB_CONF_TABLES ADD USEVIEWPARAMS '.LMB_DBTYPE_BOOLEAN,
            "UPDATE LMB_CONF_TABLES SET USEVIEWPARAMS = ".LMB_DBDEF_TRUE." WHERE TYP = 5 AND PARAMS2 IS NOT NULL AND PARAMS2 != '' "
        ];

        return $this->databaseUpdate($sqlQueries);
    }


    /**
     * add pdf standard to reports
     * @return bool
     */
    protected function patch12(): bool
    {
        $sqlQueries = [
            'ALTER TABLE LMB_REPORT_LIST ADD STANDARD ' . LMB_DBTYPE_INTEGER,
            'ALTER TABLE LMB_REPORT_LIST ADD STANDARD_AUTO ' . LMB_DBTYPE_BOOLEAN
        ];
        return $this->databaseUpdate($sqlQueries);
    }


}

