<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */


namespace Limbas\admin\tools\update\updates;

use Limbas\admin\setup\schema\ColorSchema;
use Limbas\admin\tools\update\Update;
use Limbas\lib\db\Database;
use Limbas\lib\db\functions\Dbf;
use Throwable;

class Update7m0 extends Update
{

    public function __construct()
    {
        $this->id = 6;
        $this->major = 7;
        $this->minor = 0;
    }


    /**
     * update lmb_umgvar
     * @return bool
     */
    protected function patch0(): bool
    {
        return $this->databaseUpdate("UPDATE LMB_UMGVAR SET SORT = 84, field_type = 'select', field_options = '[\"same\",\"nested\"]' WHERE FORM_NAME = 'modal_level'");
    }

    /**
     * add selected field to mail account
     * @return bool
     */
    protected function patch1(): bool
    {
        return $this->databaseUpdate('ALTER TABLE LMB_MAIL_ACCOUNTS ADD IS_SELECTED ' . LMB_DBTYPE_BOOLEAN);
    }


    /**
     * change all filters from serialize to json
     * @return bool
     */
    protected function patch2(): bool
    {
        $rs = Database::select('LMB_SNAP',['ID,FILTER']);

        lmb_StartTransaction();
        
        try {
            while (lmbdb_fetch_row($rs)) {

                $filter = unserialize(lmbdb_result($rs,'FILTER'));
                $json = json_encode($filter);
                
                if($json === false) {
                    $this->patches[$this->patch]['error'] = 'json encode error on ID ' . lmbdb_result($rs,'ID') . ': ' . json_last_error();
                    lmb_EndTransaction(0);
                    return false;
                }

                Database::update('LMB_SNAP',['FILTER'=>$json],['ID'=>lmbdb_result($rs,'ID')]);
            }
        } catch (Throwable $t) {
            $this->patches[$this->patch]['error'] = $t->getMessage();
            lmb_EndTransaction(0);
            return false;
        }

        lmb_EndTransaction(1);
        return true;
    }

    /**
     * adding SEARCHMODUS, SEARCHBAR to LMB_CONF_TABLES
     * @return bool
     */
    protected function patch3(): bool
    {

        $sqlQueries = [
            'ALTER TABLE LMB_CONF_TABLES ADD SEARCHMODUS '.LMB_DBTYPE_NUMERIC.'(1)',
            'ALTER TABLE LMB_CONF_TABLES ADD SEARCHBAR '.LMB_DBTYPE_BOOLEAN.' DEFAULT '.LMB_DBDEF_TRUE,
            'UPDATE LMB_CONF_TABLES SET SEARCHBAR = '.LMB_DBDEF_TRUE
            ];

        return $this->databaseUpdate($sqlQueries);
    }

    /**
     * adding TABLESEARCH to LMB_CONF_FIELDS
     * @return bool
     */
    protected function patch4(): bool
    {
        return $this->databaseUpdate('ALTER TABLE LMB_CONF_FIELDS ADD TABLESEARCH '.LMB_DBTYPE_BOOLEAN);
    }

    /**
     * adding PRINTER to LMB_USERDB
     * @return bool
     */
    protected function patch5(): bool
    {
        return $this->databaseUpdate('ALTER TABLE LMB_USERDB ADD PRINTER '.LMB_DBTYPE_SMALLINT);
    }

    /**
     * add calendar preview timeslot umgvars
     * @return bool
     */
    protected function patch6(): bool
    {
        $sqlQueries = [];

        $exists = $this->valueExistsInDb('LMB_UMGVAR', 'FORM_NAME', 'calendar_preview_hour_start');
        if (!$exists) {
            $nid = next_db_id('lmb_umgvar');
            $sqlQueries += ["INSERT INTO LMB_UMGVAR VALUES($nid,$nid,'calendar_preview_hour_start','2','events started x hours before current time are shown',2700)"];
        }

        $exists = $this->valueExistsInDb('LMB_UMGVAR', 'FORM_NAME', 'calendar_preview_hour_end');
        if (!$exists) {
            $nid = next_db_id('lmb_umgvar');
            $sqlQueries += ["INSERT INTO LMB_UMGVAR VALUES($nid,$nid,'calendar_preview_hour_end','24','events that start x hours after current time are shown',2700)"];
        }

        return $this->databaseUpdate($sqlQueries);
    }


    /**
     * umgvar: add mail_skip_selection
     * @return bool
     */
    protected function patch7(): bool
    {
        $exists = $this->valueExistsInDb('LMB_UMGVAR', 'FORM_NAME', 'mail_skip_selection');
        if ($exists) {
            return true;
        }
        $nextId = next_db_id('LMB_UMGVAR');       

        $sqlQueries = [
            "INSERT INTO LMB_UMGVAR VALUES($nextId,$nextId,'mail_skip_selection','0','If checked, the template selection is skipped and the editor is displayed directly with an empty mail',2818,'bool',". LMB_DBDEF_NULL . ")",
            'UPDATE LMB_ACTION SET LINK_URL = \'showMailModal(true);\' WHERE ID = 322'
        ];

        return $this->databaseUpdate($sqlQueries);
    }

    /**
     * extend size of action dependent extension field
     * @return bool
     */
    protected function patch8(): bool
    {
        return $this->databaseUpdate(Dbf::modifyColumnTypeSql('lmb_action_depend','extension', LMB_DBTYPE_VARCHAR . '(150)'));
    }

    /**
     * add mail signatures
     * @return bool
     */
    protected function patch9(): bool
    {

        $sqlQueries = [
            'CREATE TABLE LMB_MAIL_SIGNATURES (
             ID ' . LMB_DBTYPE_INTEGER . ' NOT NULL ' . LMB_DBFUNC_PRIMARY_KEY . ',
             USER_ID ' . LMB_DBTYPE_INTEGER . ',
             TENANT_ID ' . LMB_DBTYPE_INTEGER . ',
             
             NAME ' . LMB_DBTYPE_VARCHAR . '(200),
             CONTENT ' . LMB_DBTYPE_LONG .',
             FORTABLE ' . LMB_DBTYPE_INTEGER . ',
             
             IS_ACTIVE ' . LMB_DBTYPE_BOOLEAN . ',
             IS_DEFAULT ' . LMB_DBTYPE_BOOLEAN . ',
             IS_HIDDEN ' . LMB_DBTYPE_BOOLEAN . '
             
            )',
            'ALTER TABLE LMB_MAIL_ACCOUNTS ADD DEFAULT_SIGNATURE_ID ' . LMB_DBTYPE_INTEGER
        ];

        return $this->databaseUpdate($sqlQueries);
    }

    /**
     * adding MENUDISPLAY to LMB_CONF_TABLES
     * @return bool
     */
    protected function patch10(): bool
    {
        return $this->databaseUpdate('ALTER TABLE LMB_CONF_TABLES ADD MENUDISPLAY VARCHAR(250)');
    }


    /**
     * add lmb_rules_settings
     * @return bool
     */
    protected function patch11(): bool
    {
        return $this->databaseUpdate('CREATE TABLE LMB_RULES_SETTINGS (
             ID ' . LMB_DBTYPE_INTEGER . ' NOT NULL ' . LMB_DBFUNC_PRIMARY_KEY . ',
             GROUP_ID ' . LMB_DBTYPE_INTEGER . ',
             KEY ' . LMB_DBTYPE_VARCHAR . '(50),
             VALUE ' . LMB_DBTYPE_VARCHAR . '(400)
            )');
    }


    /**
     * add tinymce configurations
     * @return bool
     */
    protected function patch12(): bool
    {

        $sqlQueries = [
            'CREATE TABLE LMB_TINYMCE_CONFIGS (
             ID ' . LMB_DBTYPE_INTEGER . ' NOT NULL ' . LMB_DBFUNC_PRIMARY_KEY . ',
                          
             NAME ' . LMB_DBTYPE_VARCHAR . '(200),
             CONFIG ' . LMB_DBTYPE_LONG .',
             
             IS_DEFAULT ' . LMB_DBTYPE_BOOLEAN . '
             
            )',
            'ALTER TABLE LMB_CONF_FIELDS ADD WYSIWYG_CONFIG_ID ' . LMB_DBTYPE_INTEGER
        ];

        return $this->databaseUpdate($sqlQueries);
    }


    /**
     * update field extensions
     * @return bool
     */
    protected function patch13(): bool
    {
        return $this->databaseUpdate("UPDATE LMB_CONF_FIELDS SET EXT_TYPE = '' WHERE EXT_TYPE = 'extendedFileManager' OR EXT_TYPE = 'extendedCalender'");
    }

    /**
     * add argument modus
     * @return bool
     */
    protected function patch14(): bool
    {
        return $this->databaseUpdate("ALTER TABLE LMB_CONF_FIELDS ADD ARGUMENT_MODUS ".LMB_DBTYPE_FIXED.'(1)');
    }

    /**
     * umgvar: add export_sys_column_header
     * @return bool
     */
    protected function patch15(): bool
    {
        $exists = $this->valueExistsInDb('LMB_UMGVAR', 'FORM_NAME', 'export_sys_column_header');
        if ($exists) {
            return true;
        }
        $nextId = next_db_id('LMB_UMGVAR');

        return $this->databaseUpdate("INSERT INTO LMB_UMGVAR VALUES($nextId,$nextId,'export_sys_column_header','0','If checked, the system column names are used as column header of the export.',2818,'bool',". LMB_DBDEF_NULL . ")");
    }

    /**
     * umgvar: delete restpath
     * @return bool
     */
    protected function patch16(): bool
    {
        return $this->databaseUpdate("DELETE FROM LMB_UMGVAR WHERE FORM_NAME = 'restpath'");
    }


    /**
     * adding SQLTRIGGER to trigger
     * @return bool
     */
    protected function patch17(): bool
    {
        $sqlQueries = [
            "ALTER TABLE LMB_TRIGGER ADD SQLTRIGGER BOOLEAN",
            'UPDATE LMB_TRIGGER SET SQLTRIGGER = TRUE',
            'UPDATE LMB_TRIGGER SET SQLTRIGGER = FALSE WHERE INTERN = TRUE',
            'UPDATE LMB_TRIGGER SET INTERN = FALSE',
            "UPDATE LMB_TRIGGER SET INTERN = TRUE WHERE LOWER(TRIGGER_VALUE) LIKE 'lmb_vkn(%' OR LOWER(TRIGGER_VALUE) LIKE 'lmb_lastmodified(%'"
        ];

        return $this->databaseUpdate($sqlQueries);
    }

    /**
     * umgvar: delete restpath
     * @return bool
     */
    protected function patch18(): bool
    {
         return $this->databaseUpdate("ALTER TABLE LMB_CONF_FIELDS ADD MENUDISPLAY VARCHAR(1000)");
    }

    /**
     * add additional fields to lmb action
     * @return bool
     */
    protected function patch19(): bool
    {
        $sqlQueries = [
            'ALTER TABLE LMB_ACTION ADD SHOW_MENU_SEARCH ' . LMB_DBTYPE_BOOLEAN . ' DEFAULT '. LMB_DBDEF_TRUE,
            'ALTER TABLE LMB_ACTION_DEPEND ADD SHOW_MENU_SEARCH ' . LMB_DBTYPE_BOOLEAN . ' DEFAULT '. LMB_DBDEF_TRUE
        ];
        return $this->databaseUpdate($sqlQueries);
    }


    /**
     * patch formular parent relations for 1:n direct backward relations
     * @return bool
     */
    protected function patch20(): bool{

        global $db;

        $sqlquery = "SELECT LMB_FORMS.ID,LMB_FORMS.PARENTREL 
        FROM LMB_FORMS
        WHERE
          UPPER(LMB_FORMS.PARENTREL) LIKE 'LMB_%'
          AND UPPER(LMB_FORMS.PARENTREL) NOT LIKE '%_BD'";
        $rs = lmbdb_exec($db,$sqlquery);
        while(lmbdb_fetch_row($rs)) {
            $id = lmbdb_result($rs, "ID");
            $parentrel = lmbdb_result($rs, "PARENTREL");

            $sqlquery2 = "SELECT MD5TAB,VERKNTABID,HASRECVERKN FROM LMB_CONF_FIELDS
              WHERE
              LMB_CONF_FIELDS.DATA_TYPE = 25
              AND LMB_CONF_FIELDS.VERKNTABLETYPE = 2
	          AND LOWER(LMB_CONF_FIELDS.MD5TAB) = '".strtolower($parentrel)."'";
            $rs2 = lmbdb_exec($db,$sqlquery2);
            $verkntabid = lmbdb_result($rs2, "VERKNTABID");
            $hasrecverkn = lmbdb_result($rs2, "HASRECVERKN");
            if(!$hasrecverkn){continue;}

            $sqlquery2 = "SELECT MD5TAB FROM LMB_CONF_FIELDS
              WHERE
              TAB_ID = ".parse_db_int($verkntabid)."
              AND FIELD_ID = ".parse_db_int($hasrecverkn)."
              AND LMB_CONF_FIELDS.DATA_TYPE = 25
              AND LMB_CONF_FIELDS.VERKNTABLETYPE = 1";
            $rs2 = lmbdb_exec($db,$sqlquery2);
            $md5tab = lmbdb_result($rs2, "MD5TAB");
            if(!$md5tab){continue;}

            $sqlquery4 = "UPDATE LMB_FORMS
              SET PARENTREL = '".strtoupper($md5tab)."_BD'
              WHERE ID = $id";

            $rs4 = lmbdb_exec($db,$sqlquery4);
		}



        return true;
    }

    /**
     * add additional fields to sync tables
     * @return bool
     */
    protected function patch21(): bool
    {
        $sqlQueries = [
            'ALTER TABLE LMB_SYNC_LOG ADD SYNC_CACHE_ID ' . LMB_DBTYPE_INTEGER,
            'ALTER TABLE LMB_SYNC_CACHE ADD ERROR ' . LMB_DBTYPE_LONG,
            'ALTER TABLE LMB_SYNC_CACHE ADD TRY_COUNT ' . LMB_DBTYPE_INTEGER,
            'ALTER TABLE LMB_SYNC_CONF ADD CONFLICT_MODE ' . LMB_DBTYPE_INTEGER
        ];

        return $this->databaseUpdate($sqlQueries);
    }

    
    /**
     * regenerate style sheets
     * @return bool
     */
    protected function patch22(): bool
    {
        $success = true;
        try {
            $colorSchemas = ColorSchema::all();
            foreach ($colorSchemas as $colorSchema) {
                $colorSchema->generateCss();
            }
        } catch (Throwable) {
            $success = false;
            $this->patches[$this->patch]['error'] = 'Generation of css failed';
        }
        return $success;
    }

    /**
     * add additional fields to sync tables and umgvar
     * @return bool
     */
    protected function patch23(): bool
    {
        $sqlQueries = [
            'ALTER TABLE LMB_SYNC_CACHE ADD DONE ' . LMB_DBTYPE_BOOLEAN . ' DEFAULT '.LMB_DBDEF_FALSE
        ];

        $exists = $this->valueExistsInDb('LMB_UMGVAR', 'FORM_NAME', 'sync_max_attempts');
        if (!$exists) {
            $nextId = next_db_id('LMB_UMGVAR');
            $sqlQueries[] = "INSERT INTO LMB_UMGVAR VALUES($nextId,$nextId,'sync_max_attempts','0','Number of attempts to synchronise a record before it is ignored (0 = infinite).',2818,'number',". LMB_DBDEF_NULL . ")";
        }

        $exists = $this->valueExistsInDb('LMB_UMGVAR', 'FORM_NAME', 'sync_keep_cache');
        if (!$exists) {
            if(!isset($nextId)) {
                $nextId = next_db_id('LMB_UMGVAR');
            }
            else {
                $nextId++;
            }
            $sqlQueries[] = "INSERT INTO LMB_UMGVAR VALUES($nextId,$nextId,'sync_keep_cache','0','Number of days the cache will be retained.',2818,'number',". LMB_DBDEF_NULL . ")";
        }

        

        return $this->databaseUpdate($sqlQueries);
    }

    /**
     * import system tables
     * @return bool
     */
    protected function patch24(): bool
    {
        $tables = array('LMB_LANG', 'LMB_ACTION', 'LMB_FIELD_TYPES');
        return $this->importTables($tables);
    }

    /**
     * update grouprights
     * @return bool
     */
    protected function patch25(): bool
    {
        require_once(COREPATH . 'admin/group/group.lib');
        return check_grouprightsAll();
    }

}
