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

class Update5m3 extends Update
{

    public function __construct()
    {
        $this->id = 3;
        $this->major = 5;
        $this->minor = 3;
    }


    /**
     * init v5.3.0
     * @return bool
     */
    protected function patch0(): bool
    {
        return true;
    }



    /**
     * adding DMS folder for templates
     * @return bool
     */
    protected function patch1(): bool
    {
        global $db;
        global $action;

        // adding DMS folder
        require_once(COREPATH . 'extra/explorer/filestructure.lib');
        require_once(COREPATH . 'admin/tools/add_filestruct.lib');

        $sqlquery = "SELECT TAB_ID,TAB_GROUP FROM LMB_CONF_TABLES WHERE TYP = 8 AND LEVEL IS NULL";
        $rs = lmbdb_exec($db, $sqlquery) or errorhandle(lmbdb_errormsg($db), $sqlquery, $action, __FILE__, __LINE__);
        while (lmbdb_fetch_row($rs)) {
            $gtabid = lmbdb_result($rs, "TAB_ID");
            $tab_group = lmbdb_result($rs, "TAB_GROUP");
            create_fs_tab_dir($gtabid,$tab_group,0,null,3);
        }

        if(rebuild_fs_level(true) !== true) {
            throw new Exception($rebuildStatus);
        }

        return true;
    }


    /**
     * adding SELECT_DEFAULTTYPE to LMB_CONF_FIELDS
     * @return bool
     */
    protected function patch2(): bool
    {
        return $this->databaseUpdate("ALTER TABLE LMB_CONF_FIELDS ADD SELECT_DEFAULTTYPE SMALLINT");
    }

    /**
     * adding TYPE to LMB_SNAP
     * @return bool
     */
    protected function patch3(): bool
    {
        $sqlQueries = [
            'ALTER TABLE LMB_SNAP ADD TYPE '.LMB_DBTYPE_NUMERIC.'(1) DEFAULT 1',
            'UPDATE LMB_SNAP SET TYPE = 1'
        ];
        return $this->databaseUpdate($sqlQueries);
    }

    /**
     * adding RECURSIVMOD to LMB_ATTRIBUTE_P, LMB_SELECT_P
     * @return bool
     */
    protected function patch4(): bool
    {

        $sqlQueries = [
            'ALTER TABLE LMB_ATTRIBUTE_P ADD RECURSIVEMODE '.LMB_DBTYPE_BOOLEAN,
            'ALTER TABLE LMB_SELECT_P ADD RECURSIVEMODE '.LMB_DBTYPE_BOOLEAN
        ];

        return $this->databaseUpdate($sqlQueries);
    }


    /**
     * adding theme to color schemas
     * @return bool
     */
    protected function patch5(): bool
    {
        return $this->databaseUpdate('ALTER TABLE LMB_COLORSCHEMES ADD THEME '. LMB_DBTYPE_VARCHAR . '(20)');
    }


    /**
     * adding TABLE LMB_REMINDER_GROUP
     * @return bool
     */
    protected function patch6(): bool
    {
        $sqlQuery =
             'CREATE TABLE LMB_REMINDER_GROUP (
             REMINDER_ID ' . LMB_DBTYPE_NUMERIC . '(16),
             USER_ID ' . LMB_DBTYPE_INTEGER . ',
             GROUP_ID ' . LMB_DBTYPE_INTEGER . '
            )';

        return $this->databaseUpdate($sqlQuery);
    }

    /**
     * rebuild reminder to new schema
     * @return bool
     */
    protected function patch7(): bool
    {
        global $db;
        global $action;
        $commit = true;

        $sqlquery = "SELECT * FROM LMB_REMINDER";
        $rs = lmbdb_exec($db, $sqlquery) or errorhandle(lmbdb_errormsg($db), $sqlquery, $action, __FILE__, __LINE__);
        if(!$rs){$commit=false;}

        while (lmbdb_fetch_row($rs)) {

            $ID = lmbdb_result($rs, "ID");
            $group_id = parse_db_int(lmbdb_result($rs, "GROUP_ID"));
            $user_id = parse_db_int(lmbdb_result($rs, "USER_ID"));

            $sqlquery1 = "INSERT INTO LMB_REMINDER_GROUP VALUES($ID,$user_id,$group_id)";
            $rs1 = lmbdb_exec($db, $sqlquery1) or errorhandle(lmbdb_errormsg($db), $sqlquery1, $action, __FILE__, __LINE__);

            if(!$rs1){$commit=false;}
        }

        lmb_rebuildIndex();
        lmb_rebuildForeignKey();

        return $commit;
    }

    /**
     * drop fields in TABLE LMB_REMINDER
     * @return bool
     */
    protected function patch8(): bool
    {
        $sqlQueries = [
            'ALTER TABLE LMB_REMINDER DROP USER_ID',
            'ALTER TABLE LMB_REMINDER DROP GROUP_ID'
        ];

        return $this->databaseUpdate($sqlQueries);
    }

    /**
     * drop deprecated umgvars
     * @return bool
     */
    protected function patch9(): bool
    {
        $sqlQuery = 'DELETE FROM LMB_UMGVAR WHERE 
        FORM_NAME = \'logout_page\' OR 
        FORM_NAME = \'wysiwygeditor\' OR
        FORM_NAME = \'report_max_level\' OR 
        FORM_NAME = \'LDAP_domain\' OR 
        FORM_NAME = \'LDAP_baseDn\' OR 
        FORM_NAME = \'LDAP_accountSuffix\' OR 
        FORM_NAME = \'LDAP_defaultGroup\' OR 
        FORM_NAME = \'LDAP_useSSL\'
        ';

        return $this->databaseUpdate($sqlQuery);
    }

    /**
     * update umgvar: add sync_template
     * @return bool
     */
    protected function patch10(): bool
    {
        $exists = $this->valueExistsInDb('LMB_UMGVAR', 'FORM_NAME', 'sync_template');
        if ($exists) {
            return true;
        }
        $nid = next_db_id('lmb_umgvar');
        return $this->databaseUpdate("INSERT INTO LMB_UMGVAR VALUES($nid,$nid,'sync_template','1','adding fields automatic to synctemplates (1,2,3) ',2818)");
    }

    /**
     * update umgvar: add override_group_perm
     * @return bool
     */
    protected function patch11(): bool
    {
        $exists = $this->valueExistsInDb('LMB_UMGVAR', 'FORM_NAME', 'override_group_perm');
        if ($exists) {
            return true;
        }
        $nid = next_db_id('lmb_umgvar');
        return $this->databaseUpdate("INSERT INTO LMB_UMGVAR VALUES($nid,$nid,'override_group_perm','0','allow to override group permissions for superadmin',1900)");
    }

    /**
     * import systemtables
     * @return bool
     */
    protected function patch12(): bool
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
     * bug fixes
     * @return bool
     */
    protected function patch13(): bool
    {
        // code fixes only
        return true;
    }

}
