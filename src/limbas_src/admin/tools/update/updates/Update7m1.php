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

class Update7m1 extends Update
{

    public function __construct()
    {
        $this->id = 7;
        $this->major = 7;
        $this->minor = 1;
    }


    /**
     * update lmb_umgvar add cron token and cron session timeout
     * @return bool
     */
    protected function patch0(): bool
    {
        $exists = $this->valueExistsInDb('LMB_UMGVAR', 'FORM_NAME', 'cron_token');
        $nextId = null;
        $sqlQueries = [];
        if (!$exists) {
            $nextId = next_db_id('LMB_UMGVAR');
            $sqlQueries[] = "INSERT INTO LMB_UMGVAR VALUES($nextId,$nextId,'cron_token','0','The token required to call the cron job via http.',2818,'',". LMB_DBDEF_NULL . ")";
        }

        $exists = $this->valueExistsInDb('LMB_UMGVAR', 'FORM_NAME', 'session_save_duration');
        if (!$exists) {
            $nextId = $nextId === null ? next_db_id('LMB_UMGVAR') : $nextId+1;
            $sqlQueries[] = "INSERT INTO LMB_UMGVAR VALUES($nextId,$nextId,'session_save_duration','1440','The minutes how long a saved session should be stored.',1900,'number',". LMB_DBDEF_NULL . ")";
        }
        
        if(empty($sqlQueries)) {
            return true;
        }

        return $this->databaseUpdate($sqlQueries);
    }

    
    /**
     * update lmb_crontab
     * @return bool
     */
    protected function patch1(): bool
    {
        global $DBA;

        $renameCategory = Dbf::renameColumnSql($DBA['DBSCHEMA'],'LMB_CRONTAB','KATEGORY', 'TYPE');
        $renameActive = Dbf::renameColumnSql($DBA['DBSCHEMA'],'LMB_CRONTAB','ACTIV', 'ACTIVE');
        $renameConfig = Dbf::renameColumnSql($DBA['DBSCHEMA'],'LMB_CRONTAB','VAL', 'CONFIG');
        
        $sqlQueries = [
            'ALTER TABLE LMB_CRONTAB ' . LMB_DBFUNC_DROP_COLUMN_FIRST . ' JOB_USER',
            'ALTER TABLE LMB_CRONTAB ADD USER_ID ' . LMB_DBTYPE_INTEGER,
            $renameCategory,
            $renameActive,
            $renameConfig,
        ];
        
        return $this->databaseUpdate($sqlQueries);
    }

    /**
     * add textwrap to lmb_conf_fields
     * @return bool
     */
    protected function patch2(): bool
    {
        return $this->databaseUpdate("ALTER TABLE LMB_CONF_FIELDS ADD TEXTWRAP ".LMB_DBTYPE_BOOLEAN);
    }

    /**
     * add sync_counter to datasync client
     * @return bool
     */
    protected function patch3(): bool
    {
        $sqlQueries = [
            'ALTER TABLE LMB_SYNC_CLIENTS ADD SYNC_COUNTER ' . LMB_DBTYPE_INTEGER,
        ];

        $exists = $this->valueExistsInDb('LMB_UMGVAR', 'FORM_NAME', 'sync_rounds');
        if (!$exists) {
            $nextId = next_db_id('LMB_UMGVAR');
            $sqlQueries[] = "INSERT INTO LMB_UMGVAR VALUES($nextId,$nextId,'sync_rounds','0','Additional sync rounds per client before next client',2818)";
        }
        
        
        return $this->databaseUpdate($sqlQueries);
    }

    /**
     * add searchrule to lmb_conf_fields
     * @return bool
     */
    protected function patch4(): bool
    {
        return $this->databaseUpdate("ALTER TABLE LMB_CONF_FIELDS ADD SEARCHRULE VARCHAR(255)");
    }

    /**
     * cronjob time split
     * @return bool
     */
    protected function patch5(): bool
    {
        lmb_StartTransaction();

        $sqlQueries = [
            'ALTER TABLE LMB_CRONTAB ADD CRON_MINUTES ' . LMB_DBTYPE_VARCHAR . '(50)',
            'ALTER TABLE LMB_CRONTAB ADD CRON_HOURS ' . LMB_DBTYPE_VARCHAR . '(50)',
            'ALTER TABLE LMB_CRONTAB ADD CRON_MONTHDAYS ' . LMB_DBTYPE_VARCHAR . '(50)',
            'ALTER TABLE LMB_CRONTAB ADD CRON_MONTHS ' . LMB_DBTYPE_VARCHAR . '(50)',
            'ALTER TABLE LMB_CRONTAB ADD CRON_WEEKDAYS ' . LMB_DBTYPE_VARCHAR . '(50)',
        ];

        $success = $this->databaseUpdate($sqlQueries);

        $rs = Database::select('LMB_CRONTAB', ['ID', 'START']);
        while (lmbdb_fetch_row($rs)) {
            $id = lmbdb_result($rs, 'ID');
            $start = lmbdb_result($rs, 'START');

            [$minutes, $hours, $monthdays, $months, $weekdays] = preg_split('/[ ;]+/', $start);

            Database::update('LMB_CRONTAB',
                [
                    'CRON_MINUTES' => $minutes,
                    'CRON_HOURS' => $hours,
                    'CRON_MONTHDAYS' => $monthdays,
                    'CRON_MONTHS' => $months,
                    'CRON_WEEKDAYS' => $weekdays,
                ], ['ID' => $id]);
        }

        $sqlQuery = 'ALTER TABLE LMB_CRONTAB DROP COLUMN START';

        Database::query($sqlQuery);

        lmb_EndTransaction($success);

        return $success;
    }

    /**
     * update cronjob config
     * @return bool
     */
    protected function patch6(): bool
    {
        $convertLegacyConfig = function (string $oldConfig, $id, $type, $alive): string {
            $convertIndizeOcr = function ($oldConfig): string {
                $oldConfig = explode(";", $oldConfig);
                $newConfig = [];
                if ($oldConfig) {
                    foreach ($oldConfig as $cronValue) {
                        $partval = explode(",", $cronValue);
                        if ($partval[0] == "field") {
                            $fieldval = explode("_", $partval[1]);
                            $newConfig['fields'][$fieldval[0]][] = $fieldval[1];
                        } elseif ($partval[0] == "file") {
                            if ($partval[2] == "s") {
                                $newConfig['includeSubdirs'] = true;
                            }
                            $newConfig['files'][] = $partval[1];
                        }
                    }
                }
                return json_encode($newConfig);
            };

            $convertTemplate = function ($oldConfig): string {
                $newConfig = [];
                $cronval = explode(";", $oldConfig);
                $newConfig['template'] = $cronval[0];
                return json_encode($newConfig);
            };

            $convertSyncs = function ($oldConfig): string {
                $newConfig = [];
                $cronval = explode(";", $oldConfig);
                $template = $cronval[0];

                if (!$template or !is_numeric($template)) {
                    return json_encode($newConfig);
                }

                $newConfig['template'] = $template;
                return json_encode($newConfig);
            };

            $convertBackups = function ($oldConfig) use ($alive): string {
                $legacyOptions = array_column(
                    array_map(
                        fn($item) => explode(',', $item, 2),
                        explode(";", $oldConfig)
                    ),
                    1,
                    0);

                $legacyMedium = $legacyOptions['medium'];
                $medium = match ($legacyMedium) {
                    2 => 'tape',
                    default => 'file',
                };

                $legacyType = $legacyOptions['art'];
                $type = match ($legacyType) {
                    2 => 'incremental_data_backup',
                    3 => 'log_backup',
                    default => 'complete_data_backup',
                };

                if ($medium == 'tape') {
                    $target = $legacyOptions['path2'];
                } else {
                    $target = $legacyOptions['path1'];
                }

                $target = explode(':', $target, 2)[1];
                $target = preg_replace('#/+#', '/', $target);

                $newConfig = [
                    'backupType' => $type,
                    'backupMedium' => $medium,
                    'backupTarget' => $target,
                    'backupAlive' => $alive,
                ];

                return json_encode($newConfig);
            };

            return (match ($type) {
                'OCR', 'INDIZE' => $convertIndizeOcr,
                'TEMPLATE' => $convertTemplate,
                'DATASYNC', 'RSYNC', 'STRUCTURESYNC' => $convertSyncs,
                'BACKUP' => $convertBackups,
                'REMINDER' => fn($config) => json_encode([]),
            })($oldConfig);
        };

        $sqlQueries = [];

        $rs = Database::select('LMB_CRONTAB', ['ID', 'CONFIG', 'TYPE', 'ALIVE']);
        while (lmbdb_fetch_row($rs)) {
            $id = lmbdb_result($rs, 'ID');
            $config = lmbdb_result($rs, 'CONFIG');
            $type = lmbdb_result($rs, 'TYPE');
            $alive = lmbdb_result($rs, 'ALIVE');

            $newConfig = $convertLegacyConfig($config, $id, $type, $alive);

            Database::update('LMB_CRONTAB',
                [
                    'CONFIG' => $newConfig,
                ], ['ID' => $id]);

            $sqlQueries[] = "UPDATE LMB_CRONTAB SET CONFIG = '$newConfig' WHERE ID = $id";
        }

        return $this->databaseUpdate($sqlQueries);
    }

    /**
     * remove lmb_crontab column alive
     * @return bool
     */
    protected function patch7(): bool
    {
        $sqlQueries = [
            'ALTER TABLE LMB_CRONTAB DROP COLUMN ALIVE'
        ];

        return $this->databaseUpdate($sqlQueries);
    }

    /**
     * update lmb_umgvar add default_url_protocol
     * @return bool
     */
    protected function patch8(): bool
    {
        $exists = $this->valueExistsInDb('LMB_UMGVAR', 'FORM_NAME', 'default_url_protocol');
        $nextId = null;
        $sqlQueries = [];
        if (!$exists) {
            $nextId = next_db_id('LMB_UMGVAR');
            $sqlQueries[] = "INSERT INTO LMB_UMGVAR VALUES($nextId,$nextId,'default_url_protocol','https://','default protocol for URL fieldtype',1893,'',". LMB_DBDEF_NULL . ")";
        }

        if(empty($sqlQueries)) {
            return true;
        }

        return $this->databaseUpdate($sqlQueries);
    }

    /**
     * import system tables
     * @return bool
     */
    protected function patch9(): bool
    {
        $tables = array('LMB_LANG','LMB_ACTION', 'LMB_FIELD_TYPES');
        return $this->importTables($tables);
    }

    /**
     * update grouprights
     * @return bool
     */
    protected function patch10(): bool
    {
        require_once(COREPATH . 'admin/group/group.lib');
        return check_grouprightsAll();
    }

    /**
     * rebuild db procedures
     * @return bool
     */
    protected function patch11(): bool
    {
        global $DBA;
        if (!Dbf::createLimbasVknFunction($DBA["DBSCHEMA"])) {
            return false;
        }

        return true;

    }

    /**
     * rebuild foreign keys
     * @return bool
     */
    protected function patch12(): bool
    {
        require_once(COREPATH . 'lib/include_admin.lib');
        return lmb_rebuildForeignKey();
    }

    /**
     * rebuild indizes
     * @return bool
     */
    protected function patch13(): bool
    {
        require_once(COREPATH . 'lib/include_admin.lib');
        return lmb_rebuildIndex();

    }


    /**
     * rename field key to setting
     * @return bool
     */
    protected function patch14(): bool
    {
        global $DBA;
        require_once(COREPATH . 'lib/include_admin.lib');

        $sqlquery = Dbf::renameColumnSql($DBA["DBSCHEMA"],'LMB_RULES_SETTINGS' ?? '','KEY','SETTING');
        return $this->databaseUpdate($sqlquery);
        
    }

    /**
     * bug fixes
     * @return bool
     */
    protected function patch15(): bool
    {
        return true;   
    }

}
