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
use Limbas\admin\tools\update\Updater;

use Limbas\lib\db\Database;
use Throwable;

class Update5m0 extends Update
{

    public function __construct()
    {
        $this->id = 0;
        $this->major = 5;
        $this->minor = 0;
    }


    /**
     * lmb_report_templates
     * @return bool
     */
    protected function patch1(): bool
    {
        global $db;
        global $umgvar;
        global $action;


        $sqlquery = "SELECT ID FROM LMB_CONF_TABLES WHERE LOWER(TABELLE) = 'lmb_report_templates'";
        $rs = lmbdb_exec($db, $sqlquery) or errorhandle(lmbdb_errormsg($db), $sqlquery, $action, __FILE__, __LINE__);
        if (lmbdb_fetch_row($rs)) {
            if (lmbdb_result($rs, "ID")) {
                return true;
            }
        }

        require_once(COREPATH . 'admin/setup/language.lib');
        require_once(COREPATH . 'admin/tables/tab.lib');
        $sqlquery = "SELECT TAB_GROUP FROM LMB_CONF_TABLES WHERE UPPER(TABELLE) LIKE 'LDMS_%' LIMIT 1";
        $rs = lmbdb_exec($db, $sqlquery) or errorhandle(lmbdb_errormsg($db), $sqlquery, $action, __FILE__, __LINE__);
        $tab_group = 0;
        while (lmbdb_fetch_row($rs)) {
            $tab_group = lmbdb_result($rs, "TAB_GROUP");
        }
        if ($tab_id = add_tab('LMB_REPORT_TEMPLATES', $tab_group, 'lmb_report_templates', 0, 8, 1)) {
            # Felder anlegen
            $nfield = extended_fields_report_templ();
            add_extended_fields($nfield, $tab_id[0], 1);
        }

        return true;
    }

    /**
     * add root_template in lmb_report_list
     * @return bool
     */
    protected function patch2(): bool
    {
        return $this->databaseUpdate('ALTER TABLE LMB_REPORT_LIST ADD ROOT_TEMPLATE ' . LMB_DBTYPE_INTEGER);
    }

    /**
     * add root_template_id in lmb_report_list
     * @return bool
     */
    protected function patch3(): bool
    {
        return $this->databaseUpdate('ALTER TABLE LMB_REPORT_LIST ADD ROOT_TEMPLATE_ID ' . LMB_DBTYPE_NUMERIC . '(18)');
    }

    /**
     * add dpi in lmb_report_list
     * @return bool
     */
    protected function patch4(): bool
    {
        return $this->databaseUpdate('ALTER TABLE LMB_REPORT_LIST ADD DPI ' . LMB_DBTYPE_NUMERIC . '(3)');
    }

    /**
     * add orientation in lmb_report_list
     * @return bool
     */
    protected function patch5(): bool
    {
        return $this->databaseUpdate('ALTER TABLE LMB_REPORT_LIST ADD ORIENTATION VARCHAR(1)');
    }

    /**
     * add used_fonts in lmb_report_list
     * @return bool
     */
    protected function patch6(): bool
    {
        return $this->databaseUpdate('ALTER TABLE LMB_REPORT_LIST ADD USED_FONTS VARCHAR(250)');
    }

    /**
     * add RECURSIV_DELETE in LMB_CONF_TABLES
     * @return bool
     */
    protected function patch7(): bool
    {
        return $this->databaseUpdate('ALTER TABLE LMB_CONF_TABLES ADD RECURSIV_DELETE ' . LMB_DBTYPE_BOOLEAN);
    }

    /**
     * add encryption to fields
     * @return bool
     */
    protected function patch8(): bool
    {
        return $this->databaseUpdate('ALTER TABLE LMB_CONF_FIELDS ADD ENCRYPTION_MODE ' . LMB_DBTYPE_SMALLINT);
    }

    /**
     * resize lmb_conf_fields field_name
     * @return bool
     */
    protected function patch9(): bool
    {
        global $DBA;
        $sqlquery = dbq_15(array($DBA, 'LMB_CONF_FIELDS', 'FIELD_NAME', 'VARCHAR(120)'));
        return $this->databaseUpdate($sqlquery);
    }

    /**
     * add snapgroup to lmb_snap
     * @return bool
     */
    protected function patch10(): bool
    {
        return $this->databaseUpdate('ALTER TABLE LMB_SNAP ADD SNAPGROUP VARCHAR(30)');
    }

    /**
     * drop deprecated PATH setting
     * @return bool
     */
    protected function patch11(): bool
    {
        return $this->databaseUpdate('ALTER TABLE LDMS_STRUCTURE DROP PATH');
    }

    /**
     * add NO_META setting
     * @return bool
     */
    protected function patch12(): bool
    {
        return $this->databaseUpdate('ALTER TABLE LDMS_STRUCTURE ADD NO_META ' . LMB_DBTYPE_BOOLEAN);
    }

    /**
     * add NO_THUMBS setting
     * @return bool
     */
    protected function patch13(): bool
    {
        return $this->databaseUpdate('ALTER TABLE LDMS_STRUCTURE ADD NO_THUMBS ' . LMB_DBTYPE_BOOLEAN);
    }

    /**
     * add CUSTMENU to LMB_CONF_TABLES
     * @return bool
     */
    protected function patch14(): bool
    {
        return $this->databaseUpdate('ALTER TABLE LMB_CONF_TABLES ADD CUSTMENU ' . LMB_DBTYPE_SMALLINT);
    }

    /**
     * add CUSTMENU to LMB_FORM_LIST
     * @return bool
     */
    protected function patch15(): bool
    {
        return $this->databaseUpdate('ALTER TABLE LMB_FORM_LIST ADD CUSTMENU ' . LMB_DBTYPE_SMALLINT);
    }

    /**
     * add LMB_REPORT_LIST to DEFAULT_FONT_SIZE
     * @return bool
     */
    protected function patch16(): bool
    {
        return $this->databaseUpdate('ALTER TABLE LMB_REPORT_LIST ADD DEFAULT_FONT_SIZE ' . LMB_DBTYPE_SMALLINT);
    }

    /**
     * add LMB_REPORT_LIST to DEFAULT_FONT
     * @return bool
     */
    protected function patch17(): bool
    {
        return $this->databaseUpdate('ALTER TABLE LMB_REPORT_LIST ADD DEFAULT_FONT VARCHAR(50)');
    }

    /**
     * rename LMB_SYNC_SLAVES to LMB_SYNC_CLIENTS
     * @return bool
     */
    protected function patch18(): bool
    {
        return $this->databaseUpdate('ALTER TABLE LMB_SYNC_SLAVES RENAME TO LMB_SYNC_CLIENTS');
    }

    /**
     * add ACTIVE to LMB_MIMETYPES
     * @return bool
     */
    protected function patch19(): bool
    {
        $sqlQueries = [
            'ALTER TABLE LMB_MIMETYPES ADD ACTIVE ' . LMB_DBTYPE_BOOLEAN . ' DEFAULT ' . LMB_DBDEF_TRUE,
            'UPDATE LMB_MIMETYPES SET ACTIVE = ' . LMB_DBDEF_TRUE
        ];

        return $this->databaseUpdate($sqlQueries);
    }

    /**
     * create table LMB_COLORVARS
     * @return bool
     */
    protected function patch20(): bool
    {
        global $DBA;

        $odbc_table = dbf_20(array($DBA["DBSCHEMA"], 'LMB_COLORVARS', "'TABLE','VIEW'"));
        if (!$odbc_table) {
            $sqlquery = 'CREATE TABLE LMB_COLORVARS (
            ID ' . LMB_DBTYPE_SMALLINT . ' NOT NULL  ' . LMB_DBFUNC_PRIMARY_KEY . ',
            SCHEMA ' . LMB_DBTYPE_SMALLINT . ',
            NAME ' . LMB_DBTYPE_VARCHAR . '(100),
            VALUE ' . LMB_DBTYPE_VARCHAR . '(100)
            )';
            return $this->databaseUpdate($sqlquery);
        }
        return true;
    }

    /**
     * create table LMB_SYNC_CACHE
     * @return bool
     */
    protected function patch21(): bool
    {

        $odbc_table = dbf_20(array($DBA["DBSCHEMA"], 'LMB_SYNC_CACHE', "'TABLE','VIEW'"));
        if ($odbc_table) {
            return $this->databaseUpdate('ALTER TABLE LMB_SYNC_CACHE ADD PROCESS_KEY ' . LMB_DBTYPE_INTEGER);
        }

        $sqlquery = 'CREATE TABLE LMB_SYNC_CACHE (
        ID ' . LMB_DBTYPE_NUMERIC . '(18) NOT NULL ' . LMB_DBFUNC_PRIMARY_KEY . ',
        TABID ' . LMB_DBTYPE_SMALLINTEGER . ',
        FIELDID ' . LMB_DBTYPE_SMALLINTEGER . ',
        DATID ' . LMB_DBTYPE_NUMERIC . '(18),
        SLAVE_ID ' . LMB_DBTYPE_SMALLINTEGER . ', 
        SLAVE_DATID ' . LMB_DBTYPE_NUMERIC . '(18),
        PROCESS_KEY ' . LMB_DBTYPE_INTEGER . ',
        TYPE ' . LMB_DBTYPE_NUMERIC . '(1),
        ERSTDATUM ' . LMB_DBTYPE_TIMESTAMP . '
        )';

        return $this->databaseUpdate($sqlquery);
    }

    /**
     * add RS_USER, RS_PARAMS, RS_PATH to LMB_SYNC_CLIENTS
     * @return bool
     */
    protected function patch22(): bool
    {
        $sqlQueries = [
            'ALTER TABLE LMB_SYNC_CLIENTS ADD SYNCED ' . LMB_DBTYPE_BOOLEAN,
            'ALTER TABLE LMB_SYNC_CLIENTS ADD SYNC_ORDER ' . LMB_DBTYPE_SMALLINT,
            'ALTER TABLE LMB_SYNC_CLIENTS ADD RS_USER VARCHAR(20)',
            'ALTER TABLE LMB_SYNC_CLIENTS ADD RS_PARAMS VARCHAR(100)',
            'ALTER TABLE LMB_SYNC_CLIENTS ADD RS_PATH VARCHAR(100)',
            'ALTER TABLE LMB_SYNC_CLIENTS ADD ACTIVE ' . LMB_DBTYPE_BOOLEAN,
        ];

        return $this->databaseUpdate($sqlQueries);
    }

    /**
     * update umgvar: add modal_size
     * @return bool
     */
    protected function patch23(): bool
    {
        $exists = $this->valueExistsInDb('LMB_UMGVAR', 'FORM_NAME', 'modal_level');
        if ($exists) {
            return true;
        }
        $nid = next_db_id('lmb_umgvar');
        return $this->databaseUpdate("INSERT INTO LMB_UMGVAR VALUES($nid,$nid,'modal_level','same','open modals in sublevels like modals in modals (same / nested)',1896)");
    }

    /**
     * adding TABLE LMB_SYNC_GLOBAL
     * @return bool
     */
    protected function patch24(): bool
    {
        $sqlquery = 'CREATE TABLE LMB_SYNC_GLOBAL (
     CLIENT_ID ' . LMB_DBTYPE_INTEGER . ',
     CACHE_ID ' . LMB_DBTYPE_INTEGER . '
)';

        return $this->databaseUpdate($sqlquery);
    }

    /**
     * adding TABLE LMB_SYNC_HISTORY
     * @return bool
     */
    protected function patch25(): bool
    {
        $sqlquery = 'CREATE TABLE LMB_SYNC_HISTORY (
     ID ' . LMB_DBTYPE_INTEGER . ' NOT NULL ' . LMB_DBFUNC_PRIMARY_KEY . ',
     CLIENT_ID ' . LMB_DBTYPE_INTEGER . ',
     TEMPLATE_ID ' . LMB_DBTYPE_INTEGER . ',
     START_TIME ' . LMB_DBTYPE_TIMESTAMP . ', 
     END_TIME ' . LMB_DBTYPE_TIMESTAMP . ',
     STATUS ' . LMB_DBTYPE_SMALLINT . ',
     LOGFILE ' . LMB_DBTYPE_VARCHAR . '(50)
)';

        return $this->databaseUpdate($sqlquery);
    }

    /**
     * add CLASS to LMB_CUSTMENU
     * @return bool
     */
    protected function patch26(): bool
    {
        return $this->databaseUpdate('ALTER TABLE LMB_CUSTMENU ADD CLASS VARCHAR(60)');
    }

    /**
     * update umgvar: add confirm_level
     * @return bool
     */
    protected function patch27(): bool
    {
        $exists = $this->valueExistsInDb('LMB_UMGVAR', 'FORM_NAME', 'confirm_level');
        if ($exists) {
            return true;
        }
        $nid = next_db_id('lmb_umgvar');
        return $this->databaseUpdate("INSERT INTO LMB_UMGVAR VALUES($nid,$nid,'confirm_level','','confirm level (0=full;1=pass add,2=pass add/copy/versioning) ',1896)");
    }

    /**
     * update umgvar: add modal_size
     * @return bool
     */
    protected function patch28(): bool
    {
        $exists = $this->valueExistsInDb('LMB_UMGVAR', 'FORM_NAME', 'modal_size');
        if ($exists) {
            return true;
        }
        $nid = next_db_id('lmb_umgvar');
        return $this->databaseUpdate("INSERT INTO LMB_UMGVAR VALUES($nid,$nid,'modal_size','800x600','default modal size (XxY / auto) ',1896)");
    }

    /**
     * update umgvar: add modal_opener
     * @return bool
     */
    protected function patch29(): bool
    {
        $exists = $this->valueExistsInDb('LMB_UMGVAR', 'FORM_NAME', 'modal_opener');
        if ($exists) {
            return true;
        }
        $nid = next_db_id('lmb_umgvar');
        return $this->databaseUpdate("INSERT INTO LMB_UMGVAR VALUES($nid,$nid,'modal_opener','modal','default modal opener (window / tab / modal) ',1896)");
    }

    /**
     * update umgvar: add sync_transaction
     * @return bool
     */
    protected function patch30(): bool
    {
        $exists = $this->valueExistsInDb('LMB_UMGVAR', 'FORM_NAME', 'sync_transaction');
        if ($exists) {
            return true;
        }
        $nid = next_db_id('lmb_umgvar');
        return $this->databaseUpdate("INSERT INTO LMB_UMGVAR VALUES($nid,$nid,'sync_transaction','1','use database transaction during synchronisation (0 / 1) ',2818)");
    }

    /**
     * create table lmb_syncstructure_template
     * @return bool
     */
    protected function patch31(): bool
    {
        $sqlquery = 'CREATE TABLE LMB_SYNCSTRUCTURE_TEMPLATE (
     ID ' . LMB_DBTYPE_INTEGER . ' NOT NULL ' . LMB_DBFUNC_PRIMARY_KEY . ',
     NAME ' . LMB_DBTYPE_VARCHAR . '(50),
     MODUL ' . LMB_DBTYPE_LONG . '
)';

        return $this->databaseUpdate($sqlquery);
    }

    /**
     * add HIDE to LMB_USERDB
     * @return bool
     */
    protected function patch32(): bool
    {
        return $this->databaseUpdate('ALTER TABLE LMB_USERDB ADD HIDDEN ' . LMB_DBTYPE_BOOLEAN);
    }

    /**
     * add message column to LMB_DBPATCH
     * @return bool
     */
    protected function patch34(): bool
    {
        return true;//$this->databaseUpdate('ALTER TABLE LMB_DBPATCH ADD MSG VARCHAR(300)' );
    }

    /**
     * update umgvar: add update_mode
     * @return bool
     */
    protected function patch35(): bool
    {
        $exists = $this->valueExistsInDb('LMB_UMGVAR', 'FORM_NAME', 'update_mode');
        if ($exists) {
            return true;
        }
        $nid = next_db_id('lmb_umgvar');
        return $this->databaseUpdate("INSERT INTO LMB_UMGVAR VALUES($nid,$nid,'update_mode','foreground','show update page to user or try automatically in background (background / foreground) ',1893)");
    }

    /**
     * change umgvar: description of update check
     * @return bool
     */
    protected function patch36(): bool
    {
        return $this->databaseUpdate('UPDATE LMB_UMGVAR SET BESCHREIBUNG = \'check for updates (0/1)\' WHERE FORM_NAME = \'update_check\'');
    }


    /**
     * insert and update LMB_STATUS in all tables
     * @return bool
     */
    protected function patch37(): bool
    {
        global $DBA;
        $db = Database::get();
        $alreadyApplied = Updater::checkPatchDidRun($this->major, $this->minor, $this->patch);

        if ($alreadyApplied) {
            return true;
        }

        $sqlquery = "SELECT TABELLE FROM LMB_CONF_TABLES";
        $rs = lmbdb_exec($db, $sqlquery);
        $field = 'LMB_STATUS';
        while (lmbdb_fetch_row($rs)) {
            $table = dbf_4(lmbdb_result($rs, "TABELLE"));

            if (!dbf_20(array($DBA["DBSCHEMA"], $table, "'TABLE'"))) {
                continue;
            }

            if (!dbf_5(array($DBA['DBSCHEMA'], $table, $field))) {
                $sqlquery1 = 'ALTER TABLE ' . $table . ' ADD ' . $field . ' ' . LMB_DBTYPE_FIXED . '(1) DEFAULT 0';
                if (!$rs1 = lmbdb_exec($db, $sqlquery1)) {
                    $this->patches[$this->patch]['error'] = lmbdb_errormsg($db);
                }
                $sqlquery1 = 'UPDATE ' . $table . ' SET ' . $field . ' = 0';
                if (!$rs1 = lmbdb_exec($db, $sqlquery1)) {
                    $this->patches[$this->patch]['error'] = lmbdb_errormsg($db);
                }
                $sqlquery1 = 'UPDATE ' . $table . ' SET ' . $field . ' = 1 WHERE DEL = ' . LMB_DBDEF_TRUE;
                if (!$rs1 = lmbdb_exec($db, $sqlquery1)) {
                    $this->patches[$this->patch]['error'] = lmbdb_errormsg($db);
                }
            }
        }
        return true;
    }

    /**
     * change lmb_form_list: add header
     * @return bool
     */
    protected function patch38(): bool
    {
        return $this->databaseUpdate('ALTER TABLE LMB_FORM_LIST ADD HEADER VARCHAR(120)');
    }

    /**
     * change lmb_lang_depend: add override
     * @return bool
     */
    protected function patch39(): bool
    {
        return $this->databaseUpdate('ALTER TABLE LMB_LANG_DEPEND ADD OVERRIDE ' . LMB_DBTYPE_SMALLINT);
    }

    /**
     * change lmb_userdb: resize multitenant
     * @return bool
     */
    protected function patch40(): bool
    {
        global $DBA;
        $qu = dbq_15(array($DBA["DBSCHEMA"], 'LMB_USERDB', 'MULTITENANT', 'VARCHAR(255)'));
        return $this->databaseUpdate($qu);
    }

    /**
     * change Lmb_conf_fields: set keyfield default
     * @return bool
     */
    protected function patch41(): bool
    {
        global $DBA;
        $qu = dbq_9(array($DBA["DBSCHEMA"], 'LMB_CONF_TABLES', 'KEYFIELD', null));
        return $this->databaseUpdate($qu);
    }

    /**
     * change Lmb_conf_fields: resize description
     * @return bool
     */
    protected function patch42(): bool
    {
        global $DBA;
        $qu = dbq_15(array($DBA["DBSCHEMA"], 'LMB_CONF_FIELDS', 'BESCHREIBUNG', 'INTEGER'));
        return $this->databaseUpdate($qu);
    }

    /**
     * change Lmb_conf_fields: resize spelling
     * @return bool
     */
    protected function patch43(): bool
    {
        global $DBA;
        $qu = dbq_15(array($DBA["DBSCHEMA"], 'LMB_CONF_FIELDS', 'SPELLING', 'INTEGER'));
        return $this->databaseUpdate($qu);
    }

    /**
     * change lmb_rules_tables: add trash
     * @return bool
     */
    protected function patch44(): bool
    {
        return $this->databaseUpdate('ALTER TABLE LMB_RULES_TABLES ADD TRASH ' . LMB_DBTYPE_BOOLEAN);
    }

    /**
     * update umgvar: add sync_max_records
     * @return bool
     */
    protected function patch45(): bool
    {
        $exists = $this->valueExistsInDb('LMB_UMGVAR', 'FORM_NAME', 'sync_max_records');
        if ($exists) {
            return true;
        }
        $nid = next_db_id('lmb_umgvar');
        return $this->databaseUpdate("INSERT INTO LMB_UMGVAR VALUES($nid,$nid,'sync_max_records','','max records synced during one process (0/empty = no limit)',2818)");
    }


    /**
     * add dashboard tables
     * @return bool
     */
    protected function patch47(): bool
    {

        $sqlQueries = [

            'CREATE TABLE LMB_DASHBOARDS (
                 ID ' . LMB_DBTYPE_INTEGER . ' NOT NULL ' . LMB_DBFUNC_PRIMARY_KEY . ',
                 NAME ' . LMB_DBTYPE_VARCHAR . '(100)
            )',

            'CREATE TABLE LMB_DASHBOARD_WIDGETS (
                 ID ' . LMB_DBTYPE_INTEGER . ' NOT NULL ' . LMB_DBFUNC_PRIMARY_KEY . ',
                 DASHBOARD ' . LMB_DBTYPE_INTEGER . ',
                 TYPE ' . LMB_DBTYPE_VARCHAR . '(100),
                 X ' . LMB_DBTYPE_INTEGER . ',
                 Y ' . LMB_DBTYPE_INTEGER . ',
                 WIDTH ' . LMB_DBTYPE_INTEGER . ',
                 HEIGHT ' . LMB_DBTYPE_INTEGER . ',
                 OPTIONS ' . LMB_DBTYPE_LONG . '
            )',

            'INSERT INTO LMB_DASHBOARD_WIDGETS VALUES (1,0,	\'lmbinfo\', 0, 0, 6, 6, \'[]\')'
        ];

        return $this->databaseUpdate($sqlQueries);
    }


    /**
     * update umgvar: add download_method
     * @return bool
     */
    protected function patch48(): bool
    {
        $exists = $this->valueExistsInDb('LMB_UMGVAR', 'FORM_NAME', 'download_method');
        if ($exists) {
            return true;
        }
        $nid = next_db_id('lmb_umgvar');
        return $this->databaseUpdate("INSERT INTO LMB_UMGVAR VALUES($nid,$nid,'download_method','php','download method (php / symlink)',1899)");
    }

    /**
     * update umgvar: drop use_datetimeclass
     * @return bool
     */
    protected function patch49(): bool
    {
        return $this->databaseUpdate("DELETE FROM LMB_UMGVAR WHERE FORM_NAME ='use_datetimeclass'");
    }

    /**
     * update umgvar: resize lmb_cust_menu language field name
     * @return bool
     */
    protected function patch50(): bool
    {
        global $DBA;
        $qu = dbq_15(array($DBA["DBSCHEMA"], 'LMB_CUSTMENU', 'NAME', LMB_DBTYPE_INTEGER));
        return $this->databaseUpdate($qu);
    }

    /**
     * update umgvar: resize lmb_cust_menu language field title
     * @return bool
     */
    protected function patch51(): bool
    {
        global $DBA;
        $qu = dbq_15(array($DBA["DBSCHEMA"], 'LMB_CUSTMENU', 'TITLE', LMB_DBTYPE_INTEGER));
        return $this->databaseUpdate($qu);
    }

    /**
     * update umgvar: add dubl_fields
     * @return bool
     */
    protected function patch52(): bool
    {
        $exists = $this->valueExistsInDb('LMB_UMGVAR', 'FORM_NAME', 'dubl_fields');
        if ($exists) {
            return true;
        }
        $nid = next_db_id('lmb_umgvar');
        return $this->databaseUpdate("INSERT INTO LMB_UMGVAR VALUES($nid,$nid,'dubl_fields','','fields used for dublicate check. NAME is always used. (LDMS_FIELDS.NAME,LDMS_FIELDS.MD5)',1899)");
    }

    /**
     * update color schemas
     * @return bool
     */
    protected function patch53(): bool
    {
        $sqlQueries = [
            'ALTER TABLE LMB_COLORSCHEMES ADD LAYOUT VARCHAR(20)',
            'UPDATE LMB_COLORSCHEMES SET LAYOUT = \'barracuda\'',
            'UPDATE LMB_USERDB SET LAYOUT = \'barracuda\''
        ];

        $success = $this->databaseUpdate($sqlQueries);

        if ($success) {
            try {
                require_once(COREPATH . 'admin/setup/color_schema.lib');

                $colorSchemas = lmbcs_load_schema();
                foreach ($colorSchemas as $colorSchema) {
                    lmbcs_generate_css($colorSchema);
                }
            } catch (Throwable $t) {
                $success = false;
                $this->patches[$this->patch]['error'] = 'Generation of css failed';
            }
        }

        return $success;
    }

    /**
     * create table lmb_syncstructure_template
     * @return bool
     */
    protected function patch54(): bool
    {

        $odbc_table = dbf_20(array($DBA["DBSCHEMA"], 'LMB_FIELD_TYPES_DEPEND', "'TABLE','VIEW'"));
        if ($odbc_table) {
            return $this->databaseUpdate('ALTER TABLE LMB_FIELD_TYPES_DEPEND ADD CATEGORIE ' . LMB_DBTYPE_SMALLINT);
        }

        $sqlquery = 'CREATE TABLE LMB_FIELD_TYPES_DEPEND (
         ID ' . LMB_DBTYPE_INTEGER . ' NOT NULL ' . LMB_DBFUNC_PRIMARY_KEY . ',
         FIELD_TYPE ' . LMB_DBTYPE_SMALLINT . ',
         DATA_TYPE ' . LMB_DBTYPE_SMALLINT . ',
         DATA_TYPE_EXP ' . LMB_DBTYPE_VARCHAR . '(50),
         DATENTYP ' . LMB_DBTYPE_VARCHAR . '(20),
         SIZE ' . LMB_DBTYPE_INTEGER . ',
         LMRULE ' . LMB_DBTYPE_VARCHAR . '(300),
         SORT ' . LMB_DBTYPE_SMALLINT . ',
         FORMAT ' . LMB_DBTYPE_VARCHAR . '(50),
         PARSE_TYPE ' . LMB_DBTYPE_SMALLINT . ',
         FUNCID ' . LMB_DBTYPE_SMALLINT . ',
         HASSIZE ' . LMB_DBTYPE_BOOLEAN . ',
         CATEGORIE ' . LMB_DBTYPE_SMALLINT . '
         )';

        return $this->databaseUpdate($sqlquery);
    }


    /**
     * import systemtables
     * @return bool
     */
    protected function patch55(): bool
    {
        $tables = array('LMB_LANG', 'LMB_ACTION', 'LMB_FIELD_TYPES', 'LMB_MIMETYPES', 'LMB_SYNC_CACHE');
        return $this->importTables($tables);
    }


    /**
     * add theme option to lmb_conf_tables
     * @return bool
     */
    protected function patch56(): bool
    {
        return $this->databaseUpdate('ALTER TABLE LMB_CONF_TABLES ADD THEME ' . LMB_DBTYPE_FIXED . '(2)');
    }

    /**
     * migrate fpdf to tcpdf
     * @return bool
     */
    protected function patch57(): bool
    {
        return $this->databaseUpdate("UPDATE LMB_REPORT_LIST SET DEFFORMAT = 'tcpdf' WHERE DEFFORMAT != 'mpdf'");
    }

    /**
     * resize language field in lmb_conf_groups
     * @return bool
     */
    protected function patch58(): bool
    {
        global $DBA;
        $sqlQueries = [
            dbq_15(array($DBA, 'LMB_CONF_GROUPS', 'BESCHREIBUNG', LMB_DBTYPE_INTEGER)),
            dbq_15(array($DBA, 'LMB_CONF_GROUPS', 'NAME', LMB_DBTYPE_INTEGER))
        ];

        return $this->databaseUpdate($sqlQueries);
    }

    /**
     * rebuild all font definition files
     * @return bool
     */
    protected function patch59(): bool
    {
        $path = DEPENDENTPATH . 'inc/fonts/';
        $files = read_dir($path);
        foreach ($files['path'] as $key => $value) {
            if ($files['ext'][$key] == 'php') {
                error_log('drop file: ' . $files['name'][$key]);
                unlink($value . $files['name'][$key]);
            }
        }

        return true;
    }

    /**
     * rebuild stored procedures
     * @return bool
     */
    protected function patch60(): bool
    {
        global $DBA;
        if (!dbq_16(array($DBA["DBSCHEMA"]))) {
            return false;
        }

        return true;
    }

    /**
     * replace field JOB_USER in LMB_CRONTAB
     * @return bool
     */
    protected function patch61(): bool
    {
        $sqlQueries = [
            'ALTER TABLE LMB_CRONTAB ' . LMB_DBFUNC_DROP_COLUMN_FIRST . ' JOB_USER',
            'ALTER TABLE LMB_CRONTAB ' . LMB_DBFUNC_ADD_COLUMN_FIRST . ' JOB_USER VARCHAR(50)'
        ];

        return $this->databaseUpdate($sqlQueries);
    }

    /**
     * rename LMB_COLORVARS.SCHEMA to SCHEMA_ID
     * @return bool
     */
    protected function patch63(): bool
    {
        return $this->databaseUpdate('ALTER TABLE LMB_COLORVARS RENAME COLUMN SCHEMA TO SCHEMA_ID;');
    }


    /**
     * update umgvars
     * @return bool
     */
    protected function patch64(): bool
    {
        $sqlQueries = [
            'ALTER TABLE LMB_UMGVAR ADD FIELD_TYPE ' . LMB_DBTYPE_VARCHAR . '(10)',
            'ALTER TABLE LMB_UMGVAR ADD FIELD_OPTIONS ' . LMB_DBTYPE_VARCHAR . '(500)'
        ];

        $boolFields = [
            'indize_cs',
            'indize_level',
            'indize_badword',
            'indize_header',
            'indize_clean',
            'checkmime',
            'write_systemfolders',
            'lock',
            'LDAP_useSSL',
            'debug_lang',
            'clear_password',
            'debug_messages',
            'db-custerrors',
            'calendar_repetition',
            'admin_mode',
            'use_jsgraphics',
            'use_html2text',
            'wsdl_cache',
            'use_phpmailer',
            'multitenant',
            'soap_base64',
            'sync_transaction',
            'imagemagickpdf',
            'use_gs',
            'ps_downsample',
            'postgres_strip_tsvector',
            'postgres_use_fulltextsearch',
            'postgres_headline',
            'postgres_strip_tsvector'
        ];

        $sqlQueries[] = 'UPDATE LMB_UMGVAR SET FIELD_TYPE = \'bool\' WHERE FORM_NAME IN (\'' . implode('\',\'', $boolFields) . '\')';

        $selectFields = [
            'default_dateformat' => ['DE' => 1, 'US' => 2, 'FRA' => 3],
            'default_loglevel' => ['none' => 0, 'simple' => 1, 'full' => 2],
            'confirm_level' => ['full' => 0, 'pass add' => 1, 'pass add/copy/versioning' => 2],
            'use_exif' => ['XMP' => 1, 'IPTC' => 2],
            'calendar_firstday' => ['Sunday' => 0, 'Monday' => 1, 'Tuesday' => 2, 'Wednesday' => 3, 'Thursday' => 4, 'Friday' => 5, 'Saturday' => 6],
            'sync_mode' => ['master' => 0, 'slave' => 1],
            'postgres_rank_func', ['TS_RANK' => 'TS_RANK', 'TS_RANK_CD' => 'TS_RANK_CD', 'No order' => ''],
            'sync_method' => ['socket', 'soap'],
            'dubl_type', ['rename', 'overwrite', 'versioning'],
            'download_method' => ['php', 'symlink'],
            'modal_opener' => ['window', 'tab', 'modal'],
            'update_mode' => ['background', 'foreground'],
            'detail_viewmode' => ['editmode', 'viewmode'],
            'multi_language' => [1, 2, 3],
            'ps_output' => ['screen', 'ebook', 'printer', 'prepress', 'default'],
            'report_calc_output' => ['xls', 'xlsx', 'CSV']
        ];

        foreach ($selectFields as $selectField => $options) {
            $sqlQueries[] = 'UPDATE LMB_UMGVAR SET FIELD_TYPE = \'select\', FIELD_OPTIONS = \'' . json_encode($options) . '\' WHERE FORM_NAME = \'' . $selectField . '\'';
        }


        return $this->databaseUpdate($sqlQueries);
    }

    /**
     * rename prefix of limbas sequences
     * @return bool
     */
    protected function patch65(): bool
    {
        global $DBA;

        $sequence = dbf_26(array($DBA["DBSCHEMA"], 'seq'));
        foreach ($sequence as $key => $squname) {
            dbf_22(array($DBA["DBSCHEMA"], dbf_4($squname)));
        }

        return lmb_rebuildSequences(null,null,1);
    }

    /**
     * rebuild indexes
     * @return bool
     */
    protected function patch66(): bool
    {
        return lmb_rebuildIndex(1);
    }


    /**
     * rename umgvar session_length to inactive_duration
     * @return bool
     */
    protected function patch67(): bool
    {
        return $this->databaseUpdate("UPDATE LMB_UMGVAR SET FORM_NAME = 'inactive_duration', BESCHREIBUNG = 'minutes after user mark as inaktive' WHERE FORM_NAME = 'session_length'");
    }


    /**
     * change umgvar server_auth description
     * @return bool
     */
    protected function patch68(): bool
    {
        return $this->databaseUpdate("UPDATE LMB_UMGVAR SET BESCHREIBUNG = 'http server authentication method (default / htacces / custom classname)',NORM = 'htaccess' WHERE FORM_NAME = 'server_auth'");
    }

    /**
     * change fieldtype of DEFAULT_FONT in LMB_REPORT_LIST
     * @return bool
     */
    protected function patch69(): bool
    {
        $sqlQueries = [
            'ALTER TABLE LMB_REPORT_LIST DROP DEFAULT_FONT',
            'ALTER TABLE LMB_REPORT_LIST ADD DEFAULT_FONT VARCHAR(50)'
        ];
        return $this->databaseUpdate($sqlQueries);
    }


    /**
     * change M_SETTING in LMB_USERDB to NULL
     * @return bool
     */
    protected function patch70(): bool
    {
        return $this->databaseUpdate("UPDATE LMB_USERDB SET M_SETTING = NULL");
    }

}
