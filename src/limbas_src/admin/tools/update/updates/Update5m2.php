<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */


namespace Limbas\admin\tools\update\updates;

use Exception;
use Limbas\admin\tools\update\Update;
use Limbas\lib\db\functions\Dbf;

class Update5m2 extends Update
{

    public function __construct()
    {
        $this->id = 2;
        $this->major = 5;
        $this->minor = 2;
    }


    /**
     * init v5.2.0
     * @return bool
     */
    protected function patch0(): bool
    {
        return true;
    }

    /**
     * adding TABLE LMB_MAIL_ACCOUNTS and remove old settings
     * @return bool
     */
    protected function patch1(): bool
    {
        $sqlQueries = [
                'CREATE TABLE LMB_MAIL_ACCOUNTS (
             ID ' . LMB_DBTYPE_INTEGER . ' NOT NULL ' . LMB_DBFUNC_PRIMARY_KEY . ',
             USER_ID ' . LMB_DBTYPE_INTEGER . ',
             TENANT_ID ' . LMB_DBTYPE_INTEGER . ',
             
             TRANSPORT_TYPE ' . LMB_DBTYPE_SMALLINT . ',
             
             NAME ' . LMB_DBTYPE_VARCHAR . '(200),
             EMAIL ' . LMB_DBTYPE_VARCHAR . '(200),
             
             IS_ACTIVE ' . LMB_DBTYPE_BOOLEAN . ',
             IS_DEFAULT ' . LMB_DBTYPE_BOOLEAN . ',
             IS_HIDDEN ' . LMB_DBTYPE_BOOLEAN . ',
             
             IMAP_HOST ' . LMB_DBTYPE_VARCHAR . '(200),
             IMAP_PORT ' . LMB_DBTYPE_SMALLINT . ',
             IMAP_USER ' . LMB_DBTYPE_VARCHAR . '(200),
             IMAP_PASSWORD ' . LMB_DBTYPE_LONG . ',
             
             SMTP_HOST ' . LMB_DBTYPE_VARCHAR . '(200),
             SMTP_PORT ' . LMB_DBTYPE_SMALLINT . ',
             SMTP_USER ' . LMB_DBTYPE_VARCHAR . '(200),
             SMTP_PASSWORD ' . LMB_DBTYPE_LONG . '
             
            )',
            'DELETE FROM LMB_UMGVAR WHERE FORM_NAME = \'use_phpmailer\' OR FORM_NAME = \'php_mailer\''
        ];

        return $this->databaseUpdate($sqlQueries);
    }

    /**
     * resize lmb_custmenu_list name
     * @return bool
     */
    protected function patch2(): bool
    {
        global $DBA;
        return $this->databaseUpdate(Dbf::modifyColumnTypeSql('LMB_CUSTMENU_LIST','NAME',LMB_DBTYPE_INTEGER));
    }

    /**
     * add conflict_mode to lmb_sync_template
     * @return bool
     */

    #protected function patch3(): bool
    #{
    #    return $this->databaseUpdate('ALTER TABLE LMB_SYNC_TEMPLATE ADD CONFLICT_MODE FIXED(1)');
    #}

    /**
     * add LMB_MID to LMB_REMINDER
     * @return bool
     */
    protected function patch4(): bool
    {
        return $this->databaseUpdate('ALTER TABLE LMB_REMINDER ADD LMB_MID ' . LMB_DBTYPE_SMALLINT);
    }

    /**
     * adding NOTIFICATION,REFRESHTIME to LMB_REMINDER_LIST
     * @return bool
     */
    protected function patch5(): bool
    {
        $sqlQueries = [
            'ALTER TABLE LMB_REMINDER_LIST ADD NOTIFICATION '.LMB_DBTYPE_BOOLEAN,
            'ALTER TABLE LMB_REMINDER_LIST ADD REFRESHTIME SMALLINT DEFAULT 5',
            'UPDATE LMB_REMINDER_LIST SET REFRESHTIME = 5'
        ];

        return $this->databaseUpdate($sqlQueries);
    }

    /**
     * update umgvar: rename menurefresh to multiframe_refresh in lmb_umgvar
     * @return bool
     */
    protected function patch6(): bool
    {
        return $this->databaseUpdate("UPDATE LMB_UMGVAR SET FORM_NAME = 'multiframe_refresh' WHERE FORM_NAME = 'menurefresh'");
    }


    /**
     * adding TABLE LMB_MAIL_TEMPLATES
     * @return bool
     */
    protected function patch7(): bool
    {
        return $this->databaseUpdate('CREATE TABLE LMB_MAIL_TEMPLATES (
             ID ' . LMB_DBTYPE_INTEGER . ' NOT NULL ' . LMB_DBFUNC_PRIMARY_KEY . ',
             
             NAME ' . LMB_DBTYPE_VARCHAR . '(200),
             DESCRIPTION ' . LMB_DBTYPE_VARCHAR . '(400),
             
             ERSTDATUM ' . LMB_DBTYPE_TIMESTAMP . ',
             EDITDATUM ' . LMB_DBTYPE_TIMESTAMP . ',
             ERSTUSER ' . LMB_DBTYPE_INTEGER . ',
             EDITUSER ' . LMB_DBTYPE_INTEGER . ',
             
             TAB_ID ' . LMB_DBTYPE_INTEGER . ',
             CSS ' . LMB_DBTYPE_VARCHAR . '(400),
             
             SAVED_TEMPLATE ' . LMB_DBTYPE_LONG . ',
             PARENT_ID ' . LMB_DBTYPE_INTEGER . ',
             ROOT_TEMPLATE_TAB_ID ' . LMB_DBTYPE_INTEGER . ',
             ROOT_TEMPLATE_ELEMENT_ID ' . LMB_DBTYPE_INTEGER . '             
            )');
    }


    /**
     * renaming TABLE LMB_REPORT_TEMPLATES TO LMB_DEFAULT_TEMPLATES
     * @return bool
     */
    protected function patch8(): bool
    {
        $sqlQueries = [
            Dbf::renameTableSql('LMB_REPORT_TEMPLATES','LMB_DEFAULT_TEMPLATES'),
            'UPDATE LMB_CONF_TABLES SET TABELLE = \'lmb_default_templates\'  WHERE LOWER(TABELLE) = \'lmb_report_templates\'',
            'UPDATE LMB_LANG_DEPEND SET WERT = \'lmb_default_templates\' WHERE WERT = \'lmb_report_templates\''
        ];

        $success = $this->databaseUpdate($sqlQueries);

        lmb_rebuildSequences('LMB_DEFAULT_TEMPLATES',null,true);

        return $success;
    }

    /**
     * adding IMAP_PATH and MAIL_TABLE_ID to LMB_MAIL_ACCOUNTS
     * @return bool
     */
    protected function patch9(): bool
    {
        $sqlQueries = [
            'ALTER TABLE LMB_MAIL_ACCOUNTS ADD IMAP_PATH '. LMB_DBTYPE_VARCHAR . '(100)',
            'ALTER TABLE LMB_MAIL_ACCOUNTS ADD MAIL_TABLE_ID '. LMB_DBTYPE_INTEGER
        ];

        return $this->databaseUpdate($sqlQueries);
    }

    /**
     * adding MULTIRELATION to LMB_CONF_FIELDS
     * @return bool
     */

    protected function patch10(): bool
    {
        return $this->databaseUpdate("ALTER TABLE LMB_CONF_FIELDS ADD MULTIRELATION BOOLEAN");
    }


    /**
     * adding LEVEL TO LMB_CONF_TABLE & LMB_CONF_FIELDS
     * @return bool
     */
    protected function patch11(): bool
    {
        $sqlQueries = [
            'ALTER TABLE LMB_CONF_TABLES ADD LEVEL '. LMB_DBTYPE_INTEGER,
            'ALTER TABLE LMB_CONF_FIELDS ADD LEVEL '. LMB_DBTYPE_INTEGER
        ];

        $success = $this->databaseUpdate($sqlQueries);

        return $success;
    }

    /**
     * update LEVEL TO LMB_CONF_TABLE & LMB_CONF_FIELDS
     * @return bool
     */
    protected function patch12(): bool
    {
        require_once(COREPATH . 'admin/tools/add_filestruct.lib');
        if(rebuild_fs_level(true) !== true) {
            throw new Exception($rebuildStatus);
        }
        return true;
    }

    /**
     * import systemtables
     * @return bool
     */
    protected function patch13(): bool
    {
        $tables = array('LMB_LANG', 'LMB_ACTION', 'LMB_FIELD_TYPES');
        $success = $this->importTables($tables);
        if($success) {
            require_once(COREPATH . 'admin/group/group.lib');
            check_grouprightsAll();
        }
        return $success;
    }

    /**
     * rebuild sequences
     * @return bool
     */
    protected function patch14(): bool
    {
        return lmb_rebuildSequences(rebuild:true);
    }


}
