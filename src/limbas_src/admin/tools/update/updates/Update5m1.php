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

class Update5m1 extends Update
{

    public function __construct()
    {
        $this->id = 1;
        $this->major = 5;
        $this->minor = 1;
    }


    /**
     * init v5.1.0
     * @return bool
     */
    protected function patch0(): bool
    {
        return true;
    }

    /**
     * adding TABLE LMB_PRINTER_CACHE
     * @return bool
     */
    protected function patch1(): bool
    {
        $sqlquery = 'CREATE TABLE LMB_PRINTER_CACHE (
         ID ' . LMB_DBTYPE_INTEGER . ' NOT NULL ' . LMB_DBFUNC_PRIMARY_KEY . ',
         user_id ' . LMB_DBTYPE_SMALLINT . ',
         group_id ' . LMB_DBTYPE_SMALLINT . ',
         erstdatum ' . LMB_DBTYPE_TIMESTAMP . ' DEFAULT ' . LMB_DBDEF_TIMESTAMP . ', 
         printer_id ' . LMB_DBTYPE_SMALLINT . ',
         file_id ' . LMB_DBTYPE_NUMERIC . '(18)
        )';

        return $this->databaseUpdate($sqlquery);
    }

    /**
     * update umgvar: add printer_cache
     * @return bool
     */
    protected function patch2(): bool
    {
        $exists = $this->valueExistsInDb('LMB_UMGVAR', 'FORM_NAME', 'printer_cache');
        if ($exists) {
            return true;
        }
        $nid = next_db_id('lmb_umgvar');
        return $this->databaseUpdate("INSERT INTO LMB_UMGVAR VALUES($nid,$nid,'printer_cache','','use always printer caching (0 / 1) ',2935)");
    }

    /**
     * add PRINTER to LMB_REPORT_LIST
     * @return bool
     */
    protected function patch3(): bool
    {
        return $this->databaseUpdate('ALTER TABLE LMB_REPORT_LIST ADD PRINTER ' . LMB_DBTYPE_SMALLINT);
    }

    /**
     * resize filter name
     * @return bool
     */
    protected function patch4(): bool
    {
        global $DBA;
        return $this->databaseUpdate(dbq_15(array($DBA["DBSCHEMA"],'LMB_SNAP','NAME','VARCHAR(50)')));
    }

    /**
     * add ROW_SIZE to LMB_CONF_FIELDS
     * @return bool
     */
    protected function patch5(): bool
    {
        return $this->databaseUpdate('ALTER TABLE LMB_CONF_FIELDS ADD ROW_SIZE ' . LMB_DBTYPE_SMALLINT);
    }


    /**
     * add ROW_VIEW to LMB_CONF_FIELDS
     * @return bool
     */
    protected function patch6(): bool
    {
        return $this->databaseUpdate('ALTER TABLE LMB_CONF_FIELDS ADD COL_HIDE ' . LMB_DBTYPE_BOOLEAN . ' DEFAULT '.LMB_DBDEF_FALSE);
    }


    /**
     * set default fields for DMS
     * @return bool
     */
    protected function patch7(): bool
    {
        $sqlQueries = [
            "UPDATE LMB_CONF_FIELDS SET COL_HIDE = " . LMB_DBDEF_TRUE . " WHERE
         TAB_ID = (SELECT TAB_ID FROM LMB_CONF_TABLES WHERE UPPER(TABELLE) = 'LDMS_FILES')
         OR TAB_ID = (SELECT TAB_ID FROM LMB_CONF_TABLES WHERE UPPER(TABELLE) = 'LDMS_META')
         ",
            "UPDATE LMB_CONF_FIELDS SET COL_HIDE = " . LMB_DBDEF_FALSE . " WHERE
         TAB_ID = (SELECT TAB_ID FROM LMB_CONF_TABLES WHERE UPPER(TABELLE) = 'LDMS_FILES') AND (
         UPPER(FIELD_NAME) = 'NAME'
         OR UPPER(FIELD_NAME) = 'SIZE' 
         OR UPPER(FIELD_NAME) = 'MIMETYPE' 
         OR UPPER(FIELD_NAME) = 'INFO')
         ",
            "UPDATE LMB_USERDB SET UFILE = NULL"
        ];

        return $this->databaseUpdate($sqlQueries);
    }

    /**
     * update tabschema and view editor container sizes
     * @return bool
     */
    protected function patch8(): bool
    {
        return $this->databaseUpdate('UPDATE LMB_GTAB_PATTERN SET WIDTH = 0, HEIGHT = 0 WHERE WIDTH < 50 OR HEIGHT < 50');
    }

    /**
     * import systemtables
     * @return bool
     */
    protected function patch9(): bool
    {
        $tables = array('LMB_LANG', 'LMB_ACTION', 'LMB_FIELD_TYPES');
        return $this->importTables($tables);
    }

    /**
     * adding DEFAULTSELECTION to LMB_REMINDER_LIST
     * @return bool
     */
    protected function patch10(): bool
    {
        return $this->databaseUpdate('ALTER TABLE LMB_REMINDER_LIST ADD DEFAULTSELECTION BOOLEAN');
    }

    /**
     * adding FORM_ID  to LMB_REMINDER
     * @return bool
     */
    protected function patch11(): bool
    {
        return $this->databaseUpdate('ALTER TABLE LMB_REMINDER ADD FORM_ID SMALLINT');
    }

    /**
     * adding LMB_MID to LMB_PRINTERS
     * @return bool
     */
    protected function patch12(): bool
    {
        $sqlQueries = [
            'ALTER TABLE LMB_PRINTERS ADD LMB_MID INTEGER',
            'UPDATE LMB_PRINTERS SET LMB_MID = 0'
        ];

        return $this->databaseUpdate($sqlQueries);
    }

    /**
     * add GROUPNAME to LMB_GTAB_PATTERN
     * @return bool
     */
    protected function patch13(): bool
    {
        return $this->databaseUpdate('ALTER TABLE LMB_GTAB_PATTERN ADD GROUPNAME ' . LMB_DBTYPE_VARCHAR . '(150)');
    }
}
