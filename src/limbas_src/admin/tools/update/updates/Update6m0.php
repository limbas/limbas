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
use Limbas\lib\db\Database;
use Limbas\lib\db\functions\Dbf;

class Update6m0 extends Update
{

    public function __construct()
    {
        $this->id = 4;
        $this->major = 6;
        $this->minor = 0;
    }


    /**
     * init v6.0.0
     * @return bool
     */
    protected function patch0(): bool
    {
        return true;
    }

    /**
     * adding CHECKSUM to LMB_CONF_TABLES / LMB_CONF_FIELDS
     * @return bool
     */
    protected function patch1(): bool
    {
        $sqlQueries = [
            'ALTER TABLE LMB_CONF_TABLES ADD CHECKSUM '.LMB_DBTYPE_BOOLEAN,
            'ALTER TABLE LMB_CONF_FIELDS ADD CHECKSUM '.LMB_DBTYPE_BOOLEAN
        ];
        return $this->databaseUpdate($sqlQueries);
    }

    /**
     * adding INACTIVE / DISABLED to LMB_CUSTMENU
     * @return bool
     */
    protected function patch2(): bool
    {
        $sqlQueries = [
            'ALTER TABLE LMB_CUSTMENU ADD INACTIVE ' . LMB_DBTYPE_VARCHAR . '(150)',
            'ALTER TABLE LMB_CUSTMENU ADD DISABLED ' . LMB_DBTYPE_VARCHAR . '(150)',
        ];
        return $this->databaseUpdate($sqlQueries);
    }


    /**
     * adding TABLE LMB_SNAP_GROUP
     * @return bool
     */
    protected function patch3(): bool
    {
        $sqlquery = 'CREATE TABLE LMB_SNAP_GROUP (
         ID ' . LMB_DBTYPE_INTEGER . ' NOT NULL ' . LMB_DBFUNC_PRIMARY_KEY . ',
         NAME ' . LMB_DBTYPE_VARCHAR . '(50),
         INTERN ' . LMB_DBTYPE_BOOLEAN . '
        )';

        return $this->databaseUpdate($sqlquery);
    }

    /**
     * adding HEADER / HEADERICON to LMB_CUSTMENU
     * @return bool
     */
    protected function patch4(): bool
    {
        $sqlQueries = [
            'ALTER TABLE LMB_CUSTMENU ADD HEADER ' . LMB_DBTYPE_VARCHAR . '(150)',
            'ALTER TABLE LMB_CUSTMENU ADD HEADERICON ' . LMB_DBTYPE_VARCHAR . '(50)'
        ];
        return $this->databaseUpdate($sqlQueries);
    }

    /**
     * change snapgroup of lmb_snap to number
     * @return bool
     */
    protected function patch5(): bool
    {
        $sqlQueries = [
            'ALTER TABLE LMB_SNAP DROP SNAPGROUP',
            'ALTER TABLE LMB_SNAP ADD SNAPGROUP ' . LMB_DBTYPE_SMALLINT
        ];
        return $this->databaseUpdate($sqlQueries);
    }


    /**
     * add mode and template to form
     * @return bool
     */
    protected function patch6(): bool
    {
        $changeCssColumn = Dbf::modifyColumnTypeSql('LMB_FORM_LIST','CSS',LMB_DBTYPE_LONG);
        
        $sqlQueries = [
            'ALTER TABLE LMB_FORM_LIST ADD MODE '. LMB_DBTYPE_SMALLINT,
            'ALTER TABLE LMB_FORM_LIST ADD ROOT_TEMPLATE_TAB_ID '. LMB_DBTYPE_INTEGER,
            'ALTER TABLE LMB_FORM_LIST ADD ROOT_TEMPLATE_ELEMENT_ID '. LMB_DBTYPE_INTEGER,
            'ALTER TABLE LMB_FORM_LIST ADD CONFIG '. LMB_DBTYPE_LONG,
            $changeCssColumn
        ];

        return $this->databaseUpdate($sqlQueries);
    }


    /**
     * add HIDE to ldms_rules
     * @return bool
     */
    protected function patch7(): bool
    {
        return $this->databaseUpdate("ALTER TABLE LDMS_RULES ADD HIDE ".LMB_DBTYPE_BOOLEAN);
    }



    /**
     * update umgvar: add http_cors_domains
     * @return bool
     */
    protected function patch8(): bool
    {
        $exists = $this->valueExistsInDb('LMB_UMGVAR', 'FORM_NAME', 'http_cors_domains');
        if ($exists) {
            return true;
        }
        $nid = next_db_id('LMB_UMGVAR');
        return $this->databaseUpdate("INSERT INTO LMB_UMGVAR VALUES($nid,$nid,'http_cors_domains','','comma separated list of domains',1900)");
    }


    /**
     * add glopbalfilter to LMB_CONF_TABLES, LMB_RULES_TABLES
     * @return bool
     */
    protected function patch9(): bool
    {
        $sqlQueries = [
            'ALTER TABLE LMB_CONF_TABLES ADD GLOBALFILTER VARCHAR(250)',
            'ALTER TABLE LMB_RULES_TABLES ADD GLOBALFILTER VARCHAR(250)'
        ];

        return $this->databaseUpdate($sqlQueries);
    }

    /**
     * add detail search to LMB_CONF_FIELDS
     * @return bool
     */
    protected function patch10(): bool
    {
         return $this->databaseUpdate('ALTER TABLE LMB_CONF_FIELDS ADD DETAILSEARCH ' . LMB_DBTYPE_BOOLEAN);
    }

    /**
     * drop TYPE in LMB_REMINDER
     * @return bool
     */
    protected function patch11(): bool
    {
         return $this->databaseUpdate('ALTER TABLE LMB_REMINDER DROP TYP');
    }

    /**
     * add DETAILFORM, DETAILFORM_OPENER to LMB_CONF_TABLES
     * @return bool
     */
    protected function patch12(): bool
    {
        $sqlQueries = [
            'ALTER TABLE LMB_CONF_TABLES ADD DETAILFORM ' . LMB_DBTYPE_SMALLINT,
            'ALTER TABLE LMB_CONF_TABLES ADD DETAILFORM_OPENER ' . LMB_DBTYPE_SMALLINT
        ];
        return $this->databaseUpdate($sqlQueries);
    }

    /**
     * add RECURSIV_ARCHIVE in LMB_CONF_TABLES
     * @return bool
     */
    protected function patch13(): bool
    {
        return $this->databaseUpdate('ALTER TABLE LMB_CONF_TABLES ADD RECURSIV_ARCHIVE ' . LMB_DBTYPE_BOOLEAN);
    }

    /**
     * import systemtables
     * @return bool
     */
    protected function patch14(): bool
    {
        $tables = array('LMB_LANG', 'LMB_ACTION', 'LMB_FIELD_TYPES');
        return $this->importTables($tables);
    }


    /**
     * set default value of RECURSIV_DELETE, RECURSIV_ARCHIVE to true
     * @return bool
     */
    protected function patch15(): bool
    {
        $sqlQueries = [
            'UPDATE LMB_CONF_TABLES SET RECURSIV_DELETE = ' . LMB_DBDEF_TRUE,
            'UPDATE LMB_CONF_TABLES SET RECURSIV_ARCHIVE = ' . LMB_DBDEF_TRUE
        ];
        return $this->databaseUpdate($sqlQueries);
    }


    /**
     * add report template cache to LMB_CONF_TABLES
     * @return bool
     */
    protected function patch16(): bool
    {
        return $this->databaseUpdate('ALTER TABLE LMB_CONF_TABLES ADD REPORT_RESOLVE_CACHE ' . LMB_DBTYPE_LONG);
    }
    
    
    /**
     * update umgvar: add image_processing
     * @return bool
     */
    protected function patch17(): bool
    {
        $exists = $this->valueExistsInDb('LMB_UMGVAR', 'FORM_NAME', 'image_processing');
        if ($exists) {
            return true;
        }
        $nextId = next_db_id('LMB_UMGVAR');
        return $this->databaseUpdate("INSERT INTO LMB_UMGVAR VALUES($nextId,$nextId,'image_processing','GD','DMS Image processing method',2819,'select','[\"GD\",\"Imagick\"]')");
    }

    /**
     * increase message of lmb_sync_log
     * @return bool
     */
    protected function patch18(): bool
    {
        global $DBA;
        $query = Dbf::modifyColumnTypeSql('LMB_SYNC_LOG','MESSAGE','VARCHAR(400)');
        return $this->databaseUpdate($query);
    }

    /**
     * add regex to LMB_CONF_FIELDS
     * @return bool
     */
    protected function patch19(): bool
    {
        return $this->databaseUpdate('ALTER TABLE LMB_CONF_FIELDS ADD REGEX VARCHAR(250)');
    }

    /**
     * update umgvar calendar settings
     * @return bool
     */
    protected function patch20(): bool
    {
        $sqlQueries = [
            "UPDATE LMB_UMGVAR SET BESCHREIBUNG = 'default view mode of FullCalendar', FIELD_TYPE = 'select', FIELD_OPTIONS = '[\"dayGridMonth\",\"timeGridWeek\",\"timeGridDay\",\"listMonth\",\"listWeek\",\"listDay\"]' WHERE FORM_NAME='calendar_viewmode'",
            "UPDATE LMB_UMGVAR SET NORM = 'timeGridWeek' WHERE NORM = 'agendaWeek' AND FORM_NAME='calendar_viewmode'",
            "UPDATE LMB_UMGVAR SET NORM = 'timeGridDay' WHERE NORM = 'agendaDay' AND FORM_NAME='calendar_viewmode'",
            "UPDATE LMB_UMGVAR SET NORM = 'listWeek' WHERE NORM = 'basicWeek' AND FORM_NAME='calendar_viewmode'",
            "UPDATE LMB_UMGVAR SET NORM = 'listDay' WHERE NORM = 'basicDay' AND FORM_NAME='calendar_viewmode'",
            "UPDATE LMB_UMGVAR SET NORM = 'dayGridMonth' WHERE NORM NOT IN ('timeGridWeek','timeGridDay','listWeek','listDay') AND FORM_NAME='calendar_viewmode'",
        ];
        return $this->databaseUpdate($sqlQueries);
    }
}

