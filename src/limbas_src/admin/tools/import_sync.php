<?php

/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */



class importSync
{

    public $htmloutput;

    /**
     * Performs a secure version of the sync import (only import the structure of the tables) by first doing a precheck,
     * then if no error occured doing the actual import.
     * Wrapped in a transaction -> Rolls back everything on error
     *
     * @param $file_to_import               path to .tar.gz file that should be imported
     * @param bool $file_uploaded_via_http whether the file was uploaded via an http upload or not
     * @return array                        error log TODO
     */
    public function secure_sync_import($file_to_import, $file_uploaded_via_http = true, $precheck, $confirm_syncimport)
    {
        # try precheck
        $result = $this->sync_import(null, $precheck, false, $file_to_import, $file_uploaded_via_http);

        # precheck error
        if (!$result['success'] || !$confirm_syncimport) {
            return $result;
        }

        LimbasLogger::clear();

        # start transaction
        lmb_StartTransaction();

        # try import
        $result = $this->sync_import(null, $precheck, true, $file_to_import, $file_uploaded_via_http);

        lmb_EndTransaction($result['success']);
        return $result;
    }

    /*
     * Performs a sync import (only import the structure of the tables)
     *
     * if $precheck:
     *      - Prepare data (export of current system, extract uploaded import-file)
     *      - Compare configurations and print report
     *
     * if $confirm_syncimport:
     *      - Uses the prepared data
     *      - Performs the import
     *
     * @param string    $path_right_export     .tar.gz file to use instead of local export
     * @param boolean   $precheck
     * @param boolean   $confirm_syncimport
     * @param string    $file_to_import         path to .tar.gz file that should be imported
     * @param bool      $file_uploaded_via_http whether the file was uploaded via an http upload or not
     * @param bool      $reverse_diff           reverses the diff output (a created, b deleted) instead of (a deleted, b created)
     * @return array    'success' => bool, 'log' => array
     */
    public function sync_import($path_right_export = null, $precheck, $confirm_syncimport, $file_to_import, $file_uploaded_via_http = true, $reverse_diff = false)
    {
        $success = $this->sync_import_inner($path_right_export, $confirm_syncimport==true ? false : $precheck, $confirm_syncimport, $file_to_import, $file_uploaded_via_http, $reverse_diff);

        foreach (LimbasLogger::getLogLines() as $key => $log_) {
            if($log_['level'] == LimbasLogger::LL_ERROR) {
                $log['error'][] = $log_['message'];
            }elseif($log_['level'] == LimbasLogger::LL_NOTICE) {
                $log['notice'][] = $log_['message'];
            }

            if($precheck == 2 AND $log_['level'] == LimbasLogger::LL_INFO) {
                $log['info'][] = $log_['message'];
            }
        }

        return array('log' => $log, 'success' => $success);
    }

    private function sync_import_inner($path_right_export = null, $precheck, $confirm_syncimport, $file_to_import, $file_uploaded_via_http = true, $reverse_diff = false)
    {
        global $umgvar;
        global $session;
        global $DBA;
        global $db;
        global $action;
        global $lang;

        # path of import files
        $path = $umgvar['pfad'] . '/USER/' . $session['user_id'] . '/temp/sync/';

        # path of export files of current system
        $pathtmp = $umgvar['pfad'] . '/USER/' . $session['user_id'] . '/temp/';

        # include import functions
        if ($confirm_syncimport) {
            require_once(COREPATH . 'admin/tools/import.dao');
        }

        if ($precheck) {
            # include export functions
            require_once(COREPATH . 'admin/tools/export.lib');

            # empty temp directories
            rmdirr($pathtmp);
            #mkdir($pathtmp);
            mkdir($path);

            # move file to import into temp folder
            $moveFunc = $file_uploaded_via_http ? 'move_uploaded_file' : 'rename';
            if (!$moveFunc($file_to_import, $path . 'export.tar.gz')) {
                LimbasLogger::log("Could not move file '$file_to_import' to '{$path}export.tar.gz'!", LimbasLogger::LL_ERROR);
                return false;
            }

            # extract file to import
            exec("tar -x -v -C $path -f {$path}export.tar.gz");


            # read configuration
            if (!file_exists($path . 'export.php')) {
                LimbasLogger::log("Configuration file export.php in '$path' not found!", LimbasLogger::LL_ERROR);
                return false;
            }
            include_once($path . 'export.php');

            # the configuration file should give $export_conf, $tosyn and $types
            if (!isset($export_conf, $tosyn, $types, $viewDefinitions, $callExtensionFunctionName)) {
                LimbasLogger::log(
                    "Configuration file invalid, following vars should be set:\n"
                    . ' - $export_conf: ' . json_encode($export_conf) . "\n"
                    . ' - $tosyn: ' . json_encode($tosyn) . "\n"
                    . ' - $types: ' . json_encode($types) . "\n"
                    . ' - $viewDefinitions: ' . json_encode($viewDefinitions) . "\n"
                    . ' - $callExtensionFunctionName: ' . json_encode($callExtensionFunctionName), LimbasLogger::LL_ERROR);
                return false;
            }

            # collect tables to export locally
            $exptables = array();

            # add tabs
            if (in_array('tabs', $types)) {
                $odbc_table = dbf_20(array($DBA['DBSCHEMA'], null, "'TABLE'"));
                foreach ($odbc_table['table_name'] as $tkey => $table) {
                    # stripos for case insensitivity
                    # dont include systemtables, system_files, ldms_*
                    if ((lmb_stripos($table, 'lmb_') === false && lmb_stripos($table, 'ldms_') === false) OR lmb_strtoupper($table) == 'LMB_CUSTVAR_DEPEND') {
                        $expTablesConf[] = $table;
                    }
                }
                $exptables[] = dbf_4('lmb_conf_tables');
                $exptables[] = dbf_4('lmb_conf_fields');
                $exptables[] = dbf_4('lmb_conf_views');
                #$exptables[] = dbf_4('lmb_conf_viewfields');
                #$exptables[] = dbf_4('lmb_conf_groups');
            }

            # add forms
            if (in_array('forms', $types)) {
                $exptables[] = dbf_4('lmb_form_list');
                $exptables[] = dbf_4('lmb_forms');
            }

            # add reports
            if (in_array('rep', $types)) {
                $exptables[] = dbf_4('lmb_report_list');
                $exptables[] = dbf_4('lmb_reports');
            }

            // chart
            if (in_array('charts', $types)) {
                $exptables[] = dbf_4('lmb_chart_list');
                $exptables[] = dbf_4('lmb_charts');
            }

            // workflow
            if (in_array('work', $types)) {
                $exptables[] = dbf_4('lmb_wfl_task');
                $exptables[] = dbf_4('lmb_wfl');
            }

            // group & rules
            if (in_array('group', $types)) {
                $exptables[] = dbf_4('lmb_groups');
                if (in_array('tabs', $types)) {
                    $exptables[] = dbf_4('lmb_rules_tables');
                    $exptables[] = dbf_4('lmb_rules_fields');
                    #$exptables[] = dbf_4('lmb_rules_dataset');
                }
                if (in_array('forms', $types) or in_array('rep', $types) or in_array('work', $types) or in_array('charts', $types) or in_array('reminder', $types)) {
                    $exptables[] = dbf_4('lmb_rules_repform');
                }
                if (in_array('links', $types)) {
                    $exptables[] = dbf_4('lmb_rules_action');
                }
                if (in_array('dms', $types)) {
                    $exptables[] = dbf_4('ldms_rules');
                }
            }

            // DMS
            if (in_array('dms', $types)) {
                $exptables[] = dbf_4('ldms_structure');
                $exptables[] = dbf_4('lmb_external_storage');
            }

            // system --
            if (in_array('system', $types)) {
                $exptables[] = dbf_4('lmb_field_types');
                $exptables[] = dbf_4('lmb_fonts');
                $exptables[] = dbf_4('lmb_mimetypes');
                $exptables[] = dbf_4('lmb_lang');
                $exptables[] = dbf_4('lmb_action');
            }

            // === sonst ===
            // snapshot
            if (in_array('snapshots', $types)) {
                $exptables[] = dbf_4('lmb_snap');
                $exptables[] = dbf_4('lmb_snap_shared');
            }

            // reminder
            if (in_array('reminder', $types)) {
                $exptables[] = dbf_4('lmb_reminder_list');
            }

            // currency
            if (in_array('currency', $types)) {
                $exptables[] = dbf_4('lmb_currency');
            }

            // colorscheme
            if (in_array('colorscheme', $types)) {
                $exptables[] = dbf_4('lmb_colorschemes');
                $exptables[] = dbf_4('lmb_colorvars'); // todo ?
            }

            // usercolors
            if (in_array('usercolors', $types)) {
                $exptables[] = dbf_4('lmb_user_colors');
            }

            // crontab
            if (in_array('crontab', $types)) {
                $exptables[] = dbf_4('lmb_crontab');
            }

            // trigger
            if (in_array('trigger', $types)) {
                $exptables[] = dbf_4('lmb_trigger');
            }

            // links
            if (in_array('links', $types)) {
                $exptables[] = dbf_4('lmb_action_depend');
                $exptables[] = dbf_4('lmb_custmenu');
                $exptables[] = dbf_4('lmb_custmenu_list');
            }

            // pools
            if (in_array('pools', $types)) {
                $exptables[] = dbf_4('lmb_select_p');
                $exptables[] = dbf_4('lmb_select_w');
                $exptables[] = dbf_4('lmb_attribute_p');
                $exptables[] = dbf_4('lmb_attribute_w');
            }

            // rules&groups
            if (in_array('rules', $types)) {
                $exptables[] = dbf_4('lmb_groups');
                $exptables[] = dbf_4('lmb_rules_action');
                $exptables[] = dbf_4('lmb_rules_repform');
                $exptables[] = dbf_4('lmb_rules_tables');
                $exptables[] = dbf_4('lmb_rules_fields');
                #$exptables[] = dbf_4('lmb_rules_dataset');
                $exptables[] = dbf_4('ldms_rules');
            }

            // synchronisation
            if (in_array('synchronisation', $types)) {
                $exptables[] = dbf_4('lmb_sync_conf');
                $exptables[] = dbf_4('lmb_sync_template');
            }

            // custvar
            if (in_array('custvar', $types)) {
                $exptables[] = dbf_4('lmb_custvar');
            }

            // === /sonst ===

            // always
            #$exptables[] = dbf_4('lmb_dbpatch');

            # TODO add more here

            if ($path_right_export) {
                # copy tar file and extract
                copy($path_right_export, "{$pathtmp}export.tar.gz");
                exec("tar -x -v -C $pathtmp -f {$pathtmp}export.tar.gz");
            } else {
                // export local tables for comparison - configuration only
                if ($expTablesConf AND lmbExport_ToSystem($expTablesConf, null, null, null, true) === false) {
                    LimbasLogger::log("Could not export tableconf '" . json_encode($expTablesConf) . "' to system!", LimbasLogger::LL_ERROR);
                    return false;
                }
                // export local tables for comparison
                if ($exptables AND lmbExport_ToSystem($exptables) === false) {
                    LimbasLogger::log("Could not export tables '" . json_encode($exptables) . "' to system!", LimbasLogger::LL_ERROR);
                    return false;
                }
            }
        } else {
            # read configuration
            if (!file_exists($path . 'export.php')) {
                LimbasLogger::log("Configuration file export.php in '$path' not found!", LimbasLogger::LL_ERROR);
                return false;
            }
            require($path . 'export.php');

            # the configuration file should give $callExtensionFunctionName
            if (!isset($callExtensionFunctionName)) {
                LimbasLogger::log(
                    "Configuration file invalid, following vars should be set:\n"
                    . ' - $callExtensionFunctionName', LimbasLogger::LL_ERROR);
                return false;
            }

            // extension
            if($confirm_syncimport AND $callExtensionFunctionName) {
                if (is_dir(EXTENSIONSPATH)) {
                    if ($extdir = read_dir(EXTENSIONSPATH, 1)) {
                        foreach ($extdir["name"] as $key => $value) {
                            if ($value == 'ext_import.inc') {
                                require_once($extdir["path"][$key] . $value);
                            }
                        }
                    }
                }
            }

        }


        # reverse
        if ($reverse_diff) {
            $tmp = $pathtmp;
            $pathtmp = $path;
            $path = $tmp;
        }

        # collect all limbas system tables in extracted directory
        $tablegrouplist = array();
        if ($folderval = read_dir($path)) {
            foreach ($folderval['name'] as $key => $fileName) {
                if ($folderval['typ'][$key] == 'file' and lmb_substr($fileName, 0, 7) != 'export.') {
                    # get table name
                    $tablename = str_replace('.tar', '', str_replace('.gz', '', $fileName));

                    # add to list if lmb_* table
                    if (lmb_stripos($tablename, 'lmb_') !== false OR lmb_stripos($tablename, 'ldms_') !== false) {
                        $tablegrouplist[$tablename] = 1;
                    }
                }
            }
        } else {
            LimbasLogger::log("Could not read directory '$path'!", LimbasLogger::LL_ERROR);
            return false;
        }

        # read new configuration
        $tables = array();
        $imptableconf = array();
        if ($handle = opendir($path)) {
            while (false !== ($file = readdir($handle))) {
                if ($file != "." && $file != ".." && lmb_strtolower(lmb_substr($file, -5)) == '.conf') {
                    $table = str_replace('.conf', '', $file);
                    if ($table != 'export') {
                        $tables[$table] = 1;
                        $imptableconf[$table] = $this->parseConfigToArray($path . $file);
                    }
                }
            }
            closedir($handle);
        } else {
            LimbasLogger::log("Could not open directory '$path'!", LimbasLogger::LL_ERROR);
            return false;
        }

        # array for screen output
        $output = array();

        # read old configuration - delete table not existing as config *.conf
        $delete = array();
        if ($handle = opendir($pathtmp)) {
            while (false !== ($fileName = readdir($handle))) {
                if ($fileName != '.' && $fileName != '..' && lmb_strtolower(lmb_substr($fileName, -5)) == '.conf') {
                    $tableName = str_replace('.conf', '', $fileName);
                    if (file_exists($path . $tableName . '.conf')) {
                        $exttableconf[$tableName] = $this->parseConfigToArray($pathtmp . $fileName);
                    } else {

                        // ignore global tables
                        if(is_array($globalSyncTables) AND in_array($tableName,$globalSyncTables)){
                            continue;
                        }

                        #if (lmb_strtolower(lmb_substr($tableName, 0, 5)) == 'verk_') {
                        $delete[$tableName] = 1;
                        #} else {
                        #    $delete[$tableName] = 2;
                        #}

                        $output['deletetab'][] = '<p style="color:#f00">delete table ' . $tableName . '</p>';
                        $hasoutput['deletetab'] = 1;
                    }
                }
            }
            closedir($handle);
        } else {
            LimbasLogger::log("Could not open directory '$pathtmp'!", LimbasLogger::LL_ERROR);
            return false;
        }


        # call extension function before
        if ($confirm_syncimport AND $callExtensionFunctionName AND is_callable($callExtensionFunctionName)) {
            # call function by passed name
            if ($callExtensionFunctionName('before') === false) {
                LimbasLogger::log("Function '$callExtensionFunctionName' returned failure!", LimbasLogger::LL_ERROR);
                return false;
            } else {
                LimbasLogger::log("Function '$callExtensionFunctionName' returned success!", LimbasLogger::LL_NOTICE);
                $output['createtab'][] = "Function '$callExtensionFunctionName' returned success!";
            }
        }

        // drop all views - resolve dependencies
        if (is_array($viewDefinitions) AND (in_array('tabs', $types) OR in_array('globalsynctables', $types)) AND $confirm_syncimport) {
            LimbasLogger::log("function : drop dependency views", LimbasLogger::LL_NOTICE);
            if (!lmb_dropAllViews()) {
                LimbasLogger::log("Could not delete dependency views! - Message:".lmbdb_errormsg($db), LimbasLogger::LL_ERROR);
                return false;
            }
            $rebuild_dependency_views = 1;
        }

        // drop all foreign keys - resolve dependencies
        if ((in_array('funcforkey', $types) OR in_array('tabs', $types) OR in_array('globalsynctables', $types)) AND $confirm_syncimport) {
            if (!lmb_dropALLForeignKeys()) {
                LimbasLogger::log("Could not delete Foreign Keys! - Message:".lmbdb_errormsg($db), LimbasLogger::LL_ERROR);
                return false;
            }
            $rebuild_dependency_ForeignKeys = 1;
        }

        # ====== table diff ======
        foreach ($tables as $table => $value) {
            # table will be created
            if (is_array($exttableconf) AND !array_key_exists($table, $exttableconf)) {
                $output['createtab'][] = '<p style="color:#05ce05">create table ' . $table . '</p>';
                $hasoutput['createtab'] = 1;
                if ($confirm_syncimport) {
                    import(false, 'over', null, null, null, null, $table);
                }
            } else {
                $cmp = array();
                foreach ($imptableconf[$table]['table'] as $field => $value1) {
                    # field already exists
                    if (is_array($exttableconf[$table]['table']) AND array_key_exists($field, $exttableconf[$table]['table'])) {
                        $cmptmp = array_diff_assoc($imptableconf[$table]['table'][$field], $exttableconf[$table]['table'][$field]);

                        if (is_array($cmptmp) && lmb_count($cmptmp) > 0) {

                            $cmp[$field] = $cmptmp;

                            # Check if fieldtype has change (type & scale)
                            if ($exttableconf[$table]['table'][$field]['datatype'] != 'TEXT' && $exttableconf[$table]['table'][$field]['datatype'] != 'LONG' && ($exttableconf[$table]['table'][$field]['datatype'] != $imptableconf[$table]['table'][$field]['datatype'] || $exttableconf[$table]['table'][$field]['fieldlength'] != $imptableconf[$table]['table'][$field]['fieldlength'] || $exttableconf[$table]['table'][$field]['scale'] != $imptableconf[$table]['table'][$field]['scale'])) {

                                $newsize = ($imptableconf[$table]['table'][$field]['scale'] == 0 OR !$imptableconf[$table]['table'][$field]['scale']) ? $imptableconf[$table]['table'][$field]['fieldlength'] : $imptableconf[$table]['table'][$field]['fieldlength'] . ',' . $imptableconf[$table]['table'][$field]['scale'];
                                $oldsize = ($exttableconf[$table]['table'][$field]['scale'] == 0 OR !$exttableconf[$table]['table'][$field]['scale']) ? $exttableconf[$table]['table'][$field]['fieldlength'] : $exttableconf[$table]['table'][$field]['fieldlength'] . ',' . $exttableconf[$table]['table'][$field]['scale'];

                                $output[$table]['changefield'][] = '<p>change type of ' . $field . ' [' . $exttableconf[$table]['table'][$field]['datatype'] . '(' . $oldsize . ')' . ' -> ' . $imptableconf[$table]['table'][$field]['datatype'] . '(' . $newsize . ')]</p>';
                                $hasoutput['changefield'] = 1;
                                if ($confirm_syncimport) {

                                    $ct = parse_db_type($imptableconf[$table]['table'][$field]['datatype'], $newsize, $imptableconf[$table]['table'][$field]['fieldlength']);
                                    $datentyp = $ct[0];

                                    if (lmb_strpos($datentyp, 'BOOL') !== false) {
                                        $datentyp = 'BOOLEAN';
                                    }
                                    if (lmb_strpos($datentyp, 'TEXT') !== false) {
                                        $datentyp = 'TEXT';
                                    }

                                    $pt = lmb_get_db_fieldtype(lmb_strtoupper($imptableconf[$table]['table'][$field]['datatype']));
                                    if (!lmb_convert_fieldtype(null, $pt, $field, $newsize, $datentyp, $table, 1)) {
                                        LimbasLogger::log("field '$field' in table '$table' could not be converted to '$datentyp' - Message:".lmbdb_errormsg($db), LimbasLogger::LL_ERROR);
                                        if (is_array($GLOBALS["alert"])) {
                                            LimbasLogger::log(implode("\n", $GLOBALS["alert"]), LimbasLogger::LL_ERROR);
                                        }
                                        return false;
                                    }
                                }
                            } # check if default value has changed / modify field
                            elseif ($exttableconf[$table]['table'][$field]['datatype'] != 'TEXT' && $exttableconf[$table]['table'][$field]['datatype'] != 'LONG' && array_key_exists('default', $cmptmp)) {
                                $output[$table]['changefield'][] = '<p>change default of ' . $field . ' [' . $exttableconf[$table]['table'][$field]['default'] . ' -> ' . $imptableconf[$table]['table'][$field]['default'] . ']</p>';
                                $hasoutput['changefield'] = 1;
                                if ($confirm_syncimport) {
                                    /* --- set default value --------------------------------------------- */
                                    $def = lmb_get_db_defaultValue($imptableconf[$table]['table'][$field]['default'],$exttableconf[$table]['table'][$field]['datatype']);

                                    // modify field
                                    $sqlquery = dbq_9(array($DBA['DBSCHEMA'], $table, $field, $def));
                                    if (!$rs1 = lmbdb_exec($db, $sqlquery) or errorhandle(lmbdb_errormsg($db), $sqlquery, $action, __FILE__, __LINE__)){
                                        LimbasLogger::log("execute query: $sqlquery - Message:".lmbdb_errormsg($db), LimbasLogger::LL_ERROR);
                                    }
                                }
                            }
                        }
                    } # create field if not existent
                    else {

                        $newsize = ($imptableconf[$table]['table'][$field]['scale'] == 0 OR !$imptableconf[$table]['table'][$field]['scale']) ? $imptableconf[$table]['table'][$field]['fieldlength'] : $imptableconf[$table]['table'][$field]['fieldlength'] . ',' . $imptableconf[$table]['table'][$field]['scale'];
                        $output[$table]['createfield'][] = '<p style="color:#05ce05">create field ' . $field . ' ' . $imptableconf[$table]['table'][$field]['datatype'] . '(' . $newsize . ')</p>'; // in table '.$table.'</p>';
                        $hasoutput['createfield'] = 1;

                        if ($confirm_syncimport) {
                            /* --- create new field --------------------------------------------- */
                            $def = lmb_get_db_defaultValue($imptableconf[$table]['table'][$field]['default'],$exttableconf[$table]['table'][$field]['datatype']);

                            #$newsize = ($imptableconf[$table]['table'][$field]['scale'] == 0 OR !$imptableconf[$table]['table'][$field]['scale']) ? $imptableconf[$table]['table'][$field]['fieldlength'] : $imptableconf[$table]['table'][$field]['fieldlength'] . ',' . $imptableconf[$table]['table'][$field]['scale'];
                            $ct = parse_db_type($imptableconf[$table]['table'][$field]['datatype'], $newsize, $imptableconf[$table]['table'][$field]['fieldlength']);

                            $datentyp = $ct[0];
                            if (lmb_strpos($datentyp, 'BOOL') !== false) {
                                $datentyp = 'BOOLEAN';
                            }
                            if (lmb_strpos($datentyp, 'TEXT') !== false) {
                                $datentyp = 'TEXT';
                            }

                            if ($sqlquery = dbq_29(array($GLOBALS['DBA']['DBSCHEMA'], $table, $field, $datentyp, $def))) {
                                $rs1 = lmbdb_exec($db, $sqlquery) or errorhandle(lmbdb_errormsg($db), $sqlquery, $action, __FILE__, __LINE__);
                                if (!$rs1) {
                                    LimbasLogger::log("Could not add column '$field' in table '$table' with query '$sqlquery'! - Message:" . lmbdb_errormsg($db), LimbasLogger::LL_ERROR);
                                    return false;
                                }
                            }
                        }
                    }
                }

                # delete field
                if(is_array($exttableconf[$table]['table'])){
                foreach ($exttableconf[$table]['table'] as $field => $value1) {
                    if (!array_key_exists($field, $imptableconf[$table]['table'])) {
                        $output[$table]['deletefield'][] = '<p style="color:#f00">delete field ' . $field . '</p>'; // in table '.$table.'</p>';
                        $hasoutput['deletefield'] = 1;

                        if ($confirm_syncimport AND dbf_5(array($DBA['DBSCHEMA'], $table, $field))) {
                            if ($sqlquery = dbq_22(array($table, $field))) {
                                $rs1 = lmbdb_exec($db, $sqlquery) or errorhandle(lmbdb_errormsg($db), $sqlquery, $action, __FILE__, __LINE__);
                                if (!$rs1) {
                                    LimbasLogger::log("Could not drop column '$field' in table '$table' with query '$sqlquery'! - Message:" . lmbdb_errormsg($db), LimbasLogger::LL_ERROR);
                                    return false;
                                }
                            }
                        }
                    }
                }}


                //break;
            }
        }

        # ====== other diff ======
        if ($precheck) {

            # print collected tables
            $this->htmloutput[] = "<DIV class=\"lmbPositionContainerMain\">";

            # fields to ignore in diff
            $defaultIgnore = array(dbf_4('erstdatum'), dbf_4('editdatum'), dbf_4('erstuser'), dbf_4('edituser'), dbf_4('lastmodified'));

            # compare table conf
            if (in_array('tabs', $types)) {
                # filter: take only non-system values
                $filterFunc = function ($key, $value, $columnIds) {
                    $internId = $columnIds[dbf_4('intern')];
                    return $value[$internId] != 0;
                };

                # get conf_tables diff
                $lmbConfTablesDiff = $this->compareTableData($path, $pathtmp, dbf_4('lmb_conf_tables') . '.tar.gz', array(dbf_4('tab_id')), $defaultIgnore, dbf_4('tabelle'));
                if ($lmbConfTablesDiff === false) {
                    LimbasLogger::log("Could not compare lmb_conf_tables!", LimbasLogger::LL_ERROR);
                    return false;
                }
                $lmbConfTablesDiff = $this->groupBy1Key($lmbConfTablesDiff, 'table');

                # get conf_fields diff
                $lmbConfFieldsDiff = $this->compareTableData($path, $pathtmp, dbf_4('lmb_conf_fields') . '.tar.gz', array(dbf_4('tab_id'), dbf_4('field_id')), $defaultIgnore, dbf_4('field_name'));
                if ($lmbConfFieldsDiff === false) {
                    LimbasLogger::log("Could not compare lmb_conf_fields!", LimbasLogger::LL_ERROR);
                    return false;
                }
                $lmbConfFieldsDiff = $this->groupBy2Keys($lmbConfFieldsDiff, 'table', 'field');

                # get conf_views diff
                $lmbConfViewsDiff = $this->compareTableData($path, $pathtmp, dbf_4('lmb_conf_views') . '.tar.gz', array(dbf_4('id')), $defaultIgnore, array(dbf_4('viewdef')));
                if ($lmbConfViewsDiff === false) {
                    LimbasLogger::log("Could not compare lmb_conf_views!", LimbasLogger::LL_ERROR);
                    return false;
                }
                $lmbConfViewsDiff = $this->groupBy1Key($lmbConfViewsDiff, 'view');
            }

            # compare forms
            if (in_array('forms', $types) && $precheck == 2) {
                # get forms diff
                $lmbFormsDiff = $this->compareTableData($path, $pathtmp, dbf_4('lmb_forms') . '.tar.gz', array(dbf_4('form_id'), dbf_4('keyid')), array_merge($defaultIgnore, array(dbf_4('id'), dbf_4('z_index'))), dbf_4('inhalt'));
                if ($lmbFormsDiff === false) {
                    LimbasLogger::log("Could not compare forms!", LimbasLogger::LL_ERROR);
                }
                $lmbFormsDiff = $this->groupBy2Keys($lmbFormsDiff, 'form', 'ID');
                ksort($lmbFormsDiff);

                # get form list diff
                $lmbFormListDiff = $this->compareTableData($path, $pathtmp, dbf_4('lmb_form_list') . '.tar.gz', array(dbf_4('id')), array_merge($defaultIgnore, array(dbf_4('id'), dbf_4('z_index'))), dbf_4('name'));
                if ($lmbFormListDiff === false) {
                    LimbasLogger::log("Could not compare form lists!", LimbasLogger::LL_ERROR);
                }
                $lmbFormListDiff = $this->groupBy1Key($lmbFormListDiff, 'form');
            }

            # compare reports
            if (in_array('rep', $types) && $precheck == 2) {
                # get reports diff
                $lmbReportsDiff = $this->compareTableData($path, $pathtmp, dbf_4('lmb_reports') . '.tar.gz', array(dbf_4('bericht_id'), dbf_4('el_id')), array_merge($defaultIgnore, array(dbf_4('id'), dbf_4('z_index'))), dbf_4('inhalt'));
                if ($lmbReportsDiff === false) {
                    LimbasLogger::log("Could not compare reports!", LimbasLogger::LL_ERROR);
                }
                $lmbReportsDiff = $this->groupBy2Keys($lmbReportsDiff, 'report', 'ID');
                ksort($lmbReportsDiff);

                # get report list diff
                $lmbReportListDiff = $this->compareTableData($path, $pathtmp, dbf_4('lmb_report_list') . '.tar.gz', array(dbf_4('id')), array_merge($defaultIgnore, array(dbf_4('id'), dbf_4('z_index'))), dbf_4('name'));
                if ($lmbReportListDiff === false) {
                    LimbasLogger::log("Could not compare report list!", LimbasLogger::LL_ERROR);
                    return false;
                }
                $lmbReportListDiff = $this->groupBy1Key($lmbReportListDiff, 'report');
            }

            # compare charts
            if (in_array('charts', $types) && $precheck == 2) {
                # get charts diff
                $lmbChartsDiff = $this->compareTableData($path, $pathtmp, dbf_4('lmb_charts') . '.tar.gz', array(dbf_4('chart_id'), dbf_4('field_id')), $defaultIgnore);
                if ($lmbChartsDiff === false) {
                    LimbasLogger::log("Could not compare charts!", LimbasLogger::LL_ERROR);
                }
                $lmbChartsDiff = $this->groupBy2Keys($lmbChartsDiff, 'chart', 'field');
                ksort($lmbChartsDiff);

                # get chart list diff
                $lmbChartListDiff = $this->compareTableData($path, $pathtmp, dbf_4('lmb_chart_list') . '.tar.gz', array(dbf_4('id')), $defaultIgnore, dbf_4('diag_name'));
                if ($lmbChartListDiff === false) {
                    LimbasLogger::log("Could not compare chart list!", LimbasLogger::LL_ERROR);
                }
                $lmbChartListDiff = $this->groupBy1Key($lmbChartListDiff, 'chart');
            }

            # compare workflow
            if (in_array('work', $types) && $precheck == 2) {
                # get wfl task diff
                $lmbWflTaskDiff = $this->compareTableData($path, $pathtmp, dbf_4('lmb_wfl_task') . '.tar.gz', array(dbf_4('wfl_id'), dbf_4('id')), $defaultIgnore, dbf_4('name'));
                if ($lmbWflTaskDiff === false) {
                    LimbasLogger::log("Could not compare workflow tasks!", LimbasLogger::LL_ERROR);
                }
                $lmbWflTaskDiff = $this->groupBy2Keys($lmbWflTaskDiff, 'workflow', 'task');
                ksort($lmbWflTaskDiff);

                # get wfl diff
                $lmbWflDiff = $this->compareTableData($path, $pathtmp, dbf_4('lmb_wfl') . '.tar.gz', array(dbf_4('id')), $defaultIgnore, dbf_4('name'));
                if ($lmbWflDiff === false) {
                    LimbasLogger::log("Could not compare workflow!", LimbasLogger::LL_ERROR);
                }
                $lmbWflDiff = $this->groupBy1Key($lmbWflDiff, 'workflow');
            }

            # compare DMS
            if (in_array('dms', $types) && $precheck == 2) {
                # get reports diff
                $lmbDMSDiff = $this->compareTableData($path, $pathtmp, dbf_4('ldms_structure') . '.tar.gz', array(dbf_4('id')), array_merge($defaultIgnore, array(dbf_4('id'), dbf_4('readonly'))), dbf_4('name'));
                if ($lmbDMSDiff === false) {
                    LimbasLogger::log("Could not compare DMS!", LimbasLogger::LL_ERROR);
                }
                $lmbDMSDiff = $this->groupBy1Key($lmbDMSDiff, 'file');
            }

            # compare snapshots
            if (in_array('snapshots', $types) && $precheck == 2) {
                # get snapshots diff
                $lmbSnapshotsDiff = $this->compareTableData($path, $pathtmp, dbf_4('lmb_snap') . '.tar.gz', array(dbf_4('id')), $defaultIgnore, dbf_4('name'));
                if ($lmbSnapshotsDiff === false) {
                    LimbasLogger::log("Could not compare snapshots!", LimbasLogger::LL_ERROR);
                }
                $lmbSnapshotsDiff = $this->groupBy1Key($lmbSnapshotsDiff, 'snapshot');

                # get shared snapshots diff
                $lmbSharedSnapshotsDiff = $this->compareTableData($path, $pathtmp, dbf_4('lmb_snap_shared') . '.tar.gz', array(dbf_4('snapshot_id'), dbf_4('id')), $defaultIgnore, dbf_4('entity_type'));
                if ($lmbSharedSnapshotsDiff === false) {
                    LimbasLogger::log("Could not compare shared snapshots!", LimbasLogger::LL_ERROR);
                }
                $lmbSharedSnapshotsDiff = $this->groupBy2Keys($lmbSharedSnapshotsDiff, 'snapshot', 'share');
            }

            # get trigger diff
            if (in_array('trigger', $types) && $precheck == 2) {
                $triggerDiff = $this->compareTableData($path, $pathtmp, dbf_4('lmb_trigger') . '.tar.gz', array(dbf_4('id')), $defaultIgnore, null, $filterFunc);
                if ($triggerDiff === false) {
                    LimbasLogger::log("Could not compare triggers!", LimbasLogger::LL_ERROR);
                }
                $triggerDiff = $this->groupBy1Key($triggerDiff, 'trigger');
            }

            # compare currency
            if (in_array('currency', $types) && $precheck == 2) {
                # get currency diff
                $lmbCurrencyDiff = $this->compareTableData($path, $pathtmp, dbf_4('lmb_currency') . '.tar.gz', array(dbf_4('id')), $defaultIgnore, dbf_4('currency'));
                if ($lmbCurrencyDiff === false) {
                    LimbasLogger::log("Could not compare currency!", LimbasLogger::LL_ERROR);
                }
                $lmbCurrencyDiff = $this->groupBy1Key($lmbCurrencyDiff, 'currency');
            }

            # compare reminder
            if (in_array('reminder', $types) && $precheck == 2) {
                # get reminder diff
                $lmbReminderDiff = $this->compareTableData($path, $pathtmp, dbf_4('lmb_reminder_list') . '.tar.gz', array(dbf_4('tab_id'), dbf_4('id')), $defaultIgnore);
                if ($lmbReminderDiff === false) {
                    LimbasLogger::log("Could not compare reminders!", LimbasLogger::LL_ERROR);
                }
                $lmbReminderDiff = $this->groupBy2Keys($lmbReminderDiff, 'table', 'reminder');
            }

            # compare colorschemes
            if (in_array('colorscheme', $types) && $precheck == 2) {
                # get colorschemes diff
                $lmbColorschemesDiff = $this->compareTableData($path, $pathtmp, dbf_4('lmb_colorschemes') . '.tar.gz', array(dbf_4('id')), $defaultIgnore, dbf_4('name'));
                if ($lmbColorschemesDiff === false) {
                    LimbasLogger::log("Could not compare colorschemes!", LimbasLogger::LL_ERROR);
                }
                $lmbColorschemesDiff = $this->groupBy1Key($lmbColorschemesDiff, 'scheme');
            }

            # compare user colors
            if (in_array('usercolors', $types) && $precheck == 2) {
                # get usercolor diff
                $lmbUsercolorDiff = $this->compareTableData($path, $pathtmp, dbf_4('lmb_user_colors') . '.tar.gz', array(dbf_4('userid'), dbf_4('id')), $defaultIgnore, dbf_4('wert'));
                if ($lmbUsercolorDiff === false) {
                    LimbasLogger::log("Could not compare usercolors!", LimbasLogger::LL_ERROR);
                }
                $lmbUsercolorDiff = $this->groupBy2Keys($lmbUsercolorDiff, 'user', 'color');
            }

            # compare crontab
            if (in_array('crontab', $types) && $precheck == 2) {
                # get crontab diff
                $lmbCrontabDiff = $this->compareTableData($path, $pathtmp, dbf_4('lmb_crontab') . '.tar.gz', array(dbf_4('id')), $defaultIgnore, dbf_4('description'));
                if ($lmbCrontabDiff === false) {
                    LimbasLogger::log("Could not compare crontab!", LimbasLogger::LL_ERROR);
                }
                $lmbCrontabDiff = $this->groupBy1Key($lmbCrontabDiff, 'cron entry');
            }

            # compare menustructure
            if (in_array('links', $types) && $precheck == 2) {
                # get menustructure diff
                $lmbMenustructureDiff = $this->compareTableData($path, $pathtmp, dbf_4('lmb_action_depend') . '.tar.gz', array(dbf_4('id')), $defaultIgnore);
                if ($lmbMenustructureDiff === false) {
                    LimbasLogger::log("Could not compare lmb_action_depend!", LimbasLogger::LL_ERROR);
                }
                $lmbMenustructureDiff = $this->groupBy1Key($lmbMenustructureDiff, 'menu entry');
            }

            # compare pools
            if (in_array('pools', $types) && $precheck == 2) {
                # get pool diff
                $lmbPoolsDiff = $this->compareTableData($path, $pathtmp, dbf_4('lmb_attribute_p') . '.tar.gz', array(dbf_4('id')), $defaultIgnore, dbf_4('name'));
                if ($lmbPoolsDiff === false) {
                    LimbasLogger::log("Could not compare pools!", LimbasLogger::LL_ERROR);
                }
                $lmbPoolsDiff = $this->groupBy1Key($lmbPoolsDiff, 'pool');

                # get pool values diff
                $lmbPoolValuesDiff = $this->compareTableData($path, $pathtmp, dbf_4('lmb_attribute_w') . '.tar.gz', array(dbf_4('pool'), dbf_4('id')), $defaultIgnore, dbf_4('wert'));
                if ($lmbPoolValuesDiff === false) {
                    LimbasLogger::log("Could not compare pool values!", LimbasLogger::LL_ERROR);
                }
                $lmbPoolValuesDiff = $this->groupBy2Keys($lmbPoolValuesDiff, 'pool', 'value');

                # get select diff
                $lmbSelectDiff = $this->compareTableData($path, $pathtmp, dbf_4('lmb_select_p') . '.tar.gz', array(dbf_4('id')), $defaultIgnore, dbf_4('name'));
                if ($lmbSelectDiff === false) {
                    LimbasLogger::log("Could not compare selects!", LimbasLogger::LL_ERROR);
                }
                $lmbSelectDiff = $this->groupBy1Key($lmbSelectDiff, 'select');

                # get select value diff
                $lmbSelectValueDiff = $this->compareTableData($path, $pathtmp, dbf_4('lmb_select_w') . '.tar.gz', array(dbf_4('pool'), dbf_4('id')), $defaultIgnore, dbf_4('wert'));
                if ($lmbSelectValueDiff === false) {
                    LimbasLogger::log("Could not compare select values!", LimbasLogger::LL_ERROR);
                }
                $lmbSelectValueDiff = $this->groupBy2Keys($lmbSelectValueDiff, 'select', 'value');
            }

            # compare rights
            if ((in_array('rules', $types) || in_array('lmb_groups', $exptables)) && $precheck == 2) {
                # get group diff
                $lmbGroupsDiff = $this->compareTableData($path, $pathtmp, dbf_4('lmb_groups') . '.tar.gz', array(dbf_4('group_id')), $defaultIgnore, dbf_4('name'));
                if ($lmbGroupsDiff === false) {
                    LimbasLogger::log("Could not compare groups!", LimbasLogger::LL_ERROR);
                }
                $lmbGroupsDiff = $this->groupBy1Key($lmbGroupsDiff, 'group');
            }
            if ((in_array('rules', $types) || in_array('lmb_rules_action', $exptables)) && $precheck == 2) {
                # get rules_action diff
                $lmbRulesActionDiff = $this->compareTableData($path, $pathtmp, dbf_4('lmb_rules_action') . '.tar.gz', array(dbf_4('group_id'), dbf_4('link_id')), $defaultIgnore);
                if ($lmbRulesActionDiff === false) {
                    LimbasLogger::log("Could not compare action rules!", LimbasLogger::LL_ERROR);
                }
                $lmbRulesActionDiff = $this->groupBy2Keys($lmbRulesActionDiff, 'group', 'link entry');
            }
            if ((in_array('rules', $types) || in_array('lmb_rules_repform', $exptables)) && $precheck == 2) {
                # get rules_repform diff
                $lmbRulesRepformDiff = $this->compareTableData($path, $pathtmp, dbf_4('lmb_rules_repform') . '.tar.gz', array(dbf_4('group_id'), dbf_4('id')), $defaultIgnore);
                if ($lmbRulesRepformDiff === false) {
                    LimbasLogger::log("Could not compare repform rules!", LimbasLogger::LL_ERROR);
                }
                $lmbRulesRepformDiff = $this->groupBy2Keys($lmbRulesRepformDiff, 'group', 'report/form entry');
            }
            if ((in_array('rules', $types) || in_array('lmb_rules_tables', $exptables)) && $precheck == 2) {
                # get rules_repform diff
                $lmbRulesTableDiff = $this->compareTableData($path, $pathtmp, dbf_4('lmb_rules_tables') . '.tar.gz', array(dbf_4('group_id'), dbf_4('id')), $defaultIgnore);
                if ($lmbRulesTableDiff === false) {
                    LimbasLogger::log("Could not compare table rules!", LimbasLogger::LL_ERROR);
                }
                $lmbRulesTableDiff = $this->groupBy2Keys($lmbRulesTableDiff, 'group', 'table entry');
            }
            if ((in_array('rules', $types) || in_array('lmb_rules_fields', $exptables)) && $precheck == 2) {
                # get rules_repform diff
                $lmbRulesTablefieldDiff = $this->compareTableData($path, $pathtmp, dbf_4('lmb_rules_fields') . '.tar.gz', array(dbf_4('group_id'), dbf_4('id')), $defaultIgnore);
                if ($lmbRulesTablefieldDiff === false) {
                    LimbasLogger::log("Could not compare tablefield rules!", LimbasLogger::LL_ERROR);
                }
                $lmbRulesTablefieldDiff = $this->groupBy2Keys($lmbRulesTablefieldDiff, 'group', 'tablefield entry');
            }

            if (in_array('synchronisation', $types) && $precheck == 2) {
                $lmbSyncDiff = $this->compareTableData($path, $pathtmp, dbf_4('lmb_sync_conf') . '.tar.gz', array(dbf_4('id')), $defaultIgnore);
                if ($lmbSyncDiff === false) {
                    LimbasLogger::log("Could not compare sync conf!", LimbasLogger::LL_ERROR);
                }
                $lmbSyncDiff = $this->groupBy1Key($lmbSyncDiff, 'conf_id');

                $lmbSyncTemplDiff = $this->compareTableData($path, $pathtmp, dbf_4('lmb_sync_template') . '.tar.gz', array(dbf_4('id')), $defaultIgnore);
                if ($lmbSyncTemplDiff === false) {
                    LimbasLogger::log("Could not compare sync template!", LimbasLogger::LL_ERROR);
                }
                $lmbSyncTemplDiff = $this->groupBy1Key($lmbSyncTemplDiff, 'template_id');
            }


            // todo check
            if (in_array('custvar', $types) && $precheck == 2) {
                $lmbSyncDiff = $this->compareTableData($path, $pathtmp, dbf_4('lmb_custvar') . '.tar.gz', array(dbf_4('id')), $defaultIgnore);
                if ($lmbSyncDiff === false) {
                    LimbasLogger::log("Could not compare sync custvar!", LimbasLogger::LL_ERROR);
                }
                $lmbCustvarDiff = $this->groupBy1Key($lmbSyncDiff, 'conf_id');
            }

            # tables to delete
            if (is_array($output['deletetab'])) {
                foreach ($output['deletetab'] as $value) {
                    $this->structureoutput['database']['delete table'][] = $value;
                }
            }

            # new tables
            if (is_array($output['createtab'])) {
                foreach ($output['createtab'] as $value) {
                    $this->structureoutput['database']['create table'][] = $value;
                }
            }

            # field changes
            if ($hasoutput['deletefield'] or $hasoutput['createfield'] or $hasoutput['changefield']) {
                foreach ($output as $table => $_value) {
                    if ($table != 'createtab' && $table != 'deletetab') {
                        if (is_array($output[$table]['deletefield']) or is_array($output[$table]['createfield']) or is_array($output[$table]['changefield'])) {
                            if (is_array($output[$table]['deletefield'])) {
                                foreach ($output[$table]['deletefield'] as $value) {
                                    $this->structureoutput['database']["alter table $table"]['delete column'][] = $value;
                                }
                            }
                            if (is_array($output[$table]['createfield'])) {
                                foreach ($output[$table]['createfield'] as $value) {
                                    $this->structureoutput['database']["alter table $table"]['add column'][] = $value;
                                }
                            }
                            if (is_array($output[$table]['changefield'])) {
                                foreach ($output[$table]['changefield'] as $value) {
                                    $this->structureoutput['database']["alter table $table"]['modify column'][] = $value;
                                }
                            }
                        }
                    }
                }
            }

            # show diff
            $this->tableOutput1($lmbConfTablesDiff, 'table changes in configuration');
            $this->tableOutput2($lmbConfFieldsDiff, 'field changes in configuration');
            $this->tableOutput1($lmbConfViewsDiff, 'view changes in configuration');
            $this->tableOutput1($triggerDiff, 'Trigger changes');
            $this->tableOutput1($lmbFormListDiff, 'Form list changes');
            $this->tableOutput2($lmbFormsDiff, 'Form changes');
            $this->tableOutput1($lmbReportListDiff, 'Report list changes');
            $this->tableOutput2($lmbReportsDiff, 'Report changes');
            $this->tableOutput1($lmbChartListDiff, 'Chart list changes');
            $this->tableOutput2($lmbChartsDiff, 'Chart changes');
            $this->tableOutput1($lmbWflDiff, 'Workflow changes');
            $this->tableOutput2($lmbWflTaskDiff, 'Workflow task changes');
            $this->tableOutput1($lmbDMSDiff, 'DMS changes');
            $this->tableOutput1($lmbSnapshotsDiff, 'Snapshot changes');
            $this->tableOutput2($lmbSharedSnapshotsDiff, 'Shared snapshot changes');
            $this->tableOutput1($lmbCurrencyDiff, 'Currency changes');
            $this->tableOutput2($lmbReminderDiff, 'Reminder changes');
            $this->tableOutput1($lmbColorschemesDiff, 'Colorscheme changes');
            $this->tableOutput2($lmbUsercolorDiff, 'Usercolor changes');
            $this->tableOutput1($lmbCrontabDiff, 'Crontab changes');
            $this->tableOutput1($lmbMenustructureDiff, 'Menu structure changes');
            $this->tableOutput1($lmbPoolsDiff, 'Pool changes');
            $this->tableOutput2($lmbPoolValuesDiff, 'Pool value changes');
            $this->tableOutput1($lmbSelectDiff, 'Select changes');
            $this->tableOutput2($lmbSelectValueDiff, 'Select value changes');
            $this->tableOutput1($lmbGroupsDiff, 'Group changes');
            $this->tableOutput2($lmbRulesTableDiff, 'Table rule changes');
            $this->tableOutput2($lmbRulesTablefieldDiff, 'Tablefield rule changes');
            $this->tableOutput2($lmbRulesActionDiff, 'Link rule changes');
            $this->tableOutput2($lmbRulesRepformDiff, 'Report/Form rule changes');
            $this->tableOutput1($lmbActionDiff, 'Link changes');
            $this->tableOutput1($lmbFieldtypesDiff, 'Fieldtype changes');
            $this->tableOutput1($lmbFontDiff, 'Font changes');
            $this->tableOutput2($lmbLangDiff, 'Language changes');
            $this->tableOutput1($lmbMimetypeDiff, 'Mimetype changes');
            $this->tableOutput1($lmbUmgvarDiff, 'Environment variable changes');
            $this->tableOutput1($lmbCustvarDiff, 'Custom global variable changes');
            $this->tableOutput1($lmbSyncDiff, 'Sync config changes');
            $this->tableOutput1($lmbSyncTemplDiff, 'Sync template changes');

            if ($precheck) {
                foreach ($tablegrouplist as $tableName => $value) {
                    $this->structureoutput['overwrite system tables'][] = $tableName;
                }
                foreach ($globalSyncTables as $key => $tableName) {
                    $this->structureoutput['overwrite global tables'][] = $tableName;
                }
            }

            $diff = lmb_renderDiffToBootstrap($this->structureoutput);
            $outputDiffFile = $this->writeDiffIntoFile($diff);

            # TODO maybe use this?
            #require_once('lib/context.lib');
            #pop_menu2('test', 'title', null, 'lmb-download');

        }

        if ($confirm_syncimport) {

            // overwrite lmb_* tables
            foreach ($tablegrouplist as $table => $value) {
                if(file_exists($path . $table . '.tar.gz')) {
                    $sys = exec('tar -x -C ' . $path . ' -f ' . $path . $table . '.tar.gz');
                }else{
                    continue;
                }
                if(deleteExistingTab($table)) {
                    if(import(false, 'over', null, null, null, null, 'export')){
                        LimbasLogger::log("import table $table", LimbasLogger::LL_INFO);
                    }else{
                        LimbasLogger::log("import table $table - Message:".lmbdb_errormsg($db), LimbasLogger::LL_ERROR);
                        return false;
                    }
                }else{
                    LimbasLogger::log("delete table $table - Message:".lmbdb_errormsg($db), LimbasLogger::LL_ERROR);
                    return false;
                }
            }

            // overwrite globalSyncTables tables
            foreach ($globalSyncTables as $key => $table) {
                if(file_exists($path . $table . '.tar.gz')) {
                    $sys = exec('tar -x -C ' . $path . ' -f ' . $path . $table . '.tar.gz');
                }else{
                    continue;
                }
                if(deleteExistingTab($table)) {
                    if(import(false, 'over', null, null, null, null, 'export')){
                        LimbasLogger::log("import table $table", LimbasLogger::LL_INFO);
                    }else{
                        LimbasLogger::log("import table $table - Message:".lmbdb_errormsg($db), LimbasLogger::LL_ERROR);
                        return false;
                    }
                }else{
                    LimbasLogger::log("delete table $table - Message:".lmbdb_errormsg($db), LimbasLogger::LL_ERROR);
                    return false;
                }

            }

            // delete tables
            foreach ($delete as $table => $value) {
                // ignore global tables
                #if(is_array($globalSyncTables) AND in_array($table,$globalSyncTables)){
                #    continue;
                #}
                if(deleteExistingTab($table)) {
                    LimbasLogger::log("delete temporary tables", LimbasLogger::LL_INFO);
                }else{
                    LimbasLogger::log("delete table $table - Message:".lmbdb_errormsg($db), LimbasLogger::LL_ERROR);
                    return false;
                }
            }

            // table / link rules - generate empty rules if rules not exported
            if ((in_array('tabs', $types) or in_array('links', $types)) and !in_array('group', $types)) {
                require_once(COREPATH . 'admin/group/group.lib');
                $sqlquery = "SELECT GROUP_ID,NAME FROM LMB_GROUPS";
                $rs = lmbdb_exec($db, $sqlquery) or errorhandle(lmbdb_errormsg($db), $sqlquery, $action, __FILE__, __LINE__);
                if (!$rs) {
                    $commit = 1;
                }
                while (lmbdb_fetch_row($rs)) {
                    if (in_array('tabs', $types)) {
                        check_grouprights1(lmbdb_result($rs, "GROUP_ID"));
                        LimbasLogger::log("check table rules for group " . lmbdb_result($rs, "NAME"), LimbasLogger::LL_NOTICE);
                    }
                    if (in_array('links', $types)) {
                        check_grouprights(lmbdb_result($rs, "GROUP_ID"));
                        del_grouprights(lmbdb_result($rs, "GROUP_ID"));
                        LimbasLogger::log("check action rules for group " . lmbdb_result($rs, "NAME"), LimbasLogger::LL_NOTICE);
                    }
                }
            }

            // re-create views - resolve dependencies
            if ($rebuild_dependency_views) {

                foreach ($viewDefinitions as $viewName => $viewDefinition) {

                    // drop dependent views
                    #$dv = lmb_dropDependViews($viewName);
                    // drop view if already exists
                    #lmb_dropView($viewname);
                    // create new view

                    if (!lmb_createView($viewDefinition, $viewName)) {
                        LimbasLogger::log("Could not create view '$viewName'! - Message:".lmbdb_errormsg($db), LimbasLogger::LL_ERROR);
                        return false;
                    }

                    // add dependent views
                    #lmb_addDependViews($dv,$viewDefinitions);
                }
                LimbasLogger::log("function : rebuild dependency views", LimbasLogger::LL_NOTICE);
            }

            // re-create foreign keys - resolve dependencies
            if ($rebuild_dependency_ForeignKeys) {
                if (!lmb_rebuildForeignKey()) {
                    LimbasLogger::log("Could not create Foreign Keys! - Message:".lmbdb_errormsg($db), LimbasLogger::LL_ERROR);
                    return false;
                }else{
                    LimbasLogger::log("function : rebuild Foreign Keys", LimbasLogger::LL_NOTICE);
                }
            }

            // restore umgvar
            /*
            if (in_array('lmb_umgvar', $exptables)) {
                foreach ($lmbUmgvarStorage as $key => $value) {
                    $sqlquery = "UPDATE LMB_UMGVAR SET NORM='$value' WHERE FORM_NAME='$key'";
                    $rs = lmbdb_exec($db, $sqlquery) or errorhandle(lmbdb_errormsg($db), $sqlquery, $action, __FILE__, __LINE__);
                    if (!$rs) {
                        LimbasLogger::log("Could not restore umgvar!", LimbasLogger::LL_ERROR);
                        return false;
                    } else {
                        LimbasLogger::log("Restored umgvar '$key' to '$value'", LimbasLogger::LL_INFO);
                    }
                }
            }*/

            ################### functions #####################

            if (in_array('funcdelsession', $types)) {
                $sqlquery = "DELETE FROM LMB_SESSION";
                $rs = lmbdb_exec($db, $sqlquery);
                LimbasLogger::log("function : delete sessions", LimbasLogger::LL_NOTICE);
            }

            if (in_array('funcmenurefresh', $types)) {
                require_once(COREPATH . 'admin/group/group.lib');
                check_grouprightsAll();
                LimbasLogger::log("function : refresh link rules ", LimbasLogger::LL_NOTICE);
            }

            if (in_array('functablerefresh', $types)) {
                require_once(COREPATH . 'admin/group/group.lib');
                check_grouprights1All();
                LimbasLogger::log("function : refresh table/field rules ", LimbasLogger::LL_NOTICE);
            }

            if (in_array('funcproz', $types)) {
                if (dbq_16(array($DBA["DBSCHEMA"], 1))) {
                    LimbasLogger::log("function : rebuild db prozedures ", LimbasLogger::LL_NOTICE);
                } else {
                    LimbasLogger::log("function : rebuild db prozedures failed - Message:".lmbdb_errormsg($db), LimbasLogger::LL_ERROR);
                    return false;
                }
            }

            if (in_array('funcindize', $types)) {
                $rebuild = null;
                if (in_array('funcindize_rebuild', $types)) {$rebuild = 1;} // get from extension

                if (lmb_rebuildIndex($rebuild)) {
                    LimbasLogger::log("function : rebuild db indexes ", LimbasLogger::LL_NOTICE);
                } else {
                    LimbasLogger::log("function : rebuild db indexes failed - Message:".lmbdb_errormsg($db), LimbasLogger::LL_ERROR);
                    return false;
                }
                $rebuild = null;
            }

            if (in_array('funcsequ', $types)) {
                if (lmb_rebuildSequences()) {
                    LimbasLogger::log("function : rebuild sequences ", LimbasLogger::LL_NOTICE);
                } else {
                    LimbasLogger::log("function : rebuild sequences failed - Message:".lmbdb_errormsg($db), LimbasLogger::LL_ERROR);
                    return false;
                }
            }

            if (in_array('functrigger', $types)) {
                if (lmb_rebuildTrigger(1)) {
                    LimbasLogger::log("function : rebuild trigger ", LimbasLogger::LL_NOTICE);
                } else {
                    LimbasLogger::log("function : rebuild trigger failed - Message:".lmbdb_errormsg($db), LimbasLogger::LL_ERROR);
                    return false;
                }
            }

            if (in_array('functmpfiles', $types)) {
                if (lmb_delete_user_filesave()) {
                    LimbasLogger::log("function : delete temporary user files ", LimbasLogger::LL_NOTICE);
                } else {
                    LimbasLogger::log("function : delete temporary user files failed ", LimbasLogger::LL_ERROR);
                    return false;
                }
            }

            if (in_array('functmpfields', $types)) {
                require_once(COREPATH . 'admin/tools/multiselect_refresh.lib');
                multiselectRefreshCount();
                relationRefreshCount();
                LimbasLogger::log("function : rebuild temporary tables values ", LimbasLogger::LL_NOTICE);
            }

            if (in_array('source', $types)) {
                $limbasSrcTarGz = $path . 'limbas_src.tar.gz';
                if (file_exists($limbasSrcTarGz)) {
                    $pathCore = COREPATH.'../';
                    # extract new source
                    system("tar xzf '$limbasSrcTarGz' -C '$pathCore'");
                    LimbasLogger::log("overwrite limbas_src directory", LimbasLogger::LL_NOTICE);
                }else{
                    LimbasLogger::log("overwrite limbas_src directory failed, $limbasSrcTarGz does not exist!", LimbasLogger::LL_ERROR);
                }
                $limbasAssetsTarGz = $path . 'assets.tar.gz';
                if (file_exists($limbasAssetsTarGz)) {
                    $pathAssets = PUBLICPATH;
                    # extract new assets
                    system("tar xzf '$limbasAssetsTarGz' -C '$pathAssets'");
                    LimbasLogger::log("overwrite assets directory", LimbasLogger::LL_NOTICE);
                }else{
                    LimbasLogger::log("overwrite assets directory failed, $limbasAssetsTarGz does not exist!", LimbasLogger::LL_ERROR);
                }
                $limbasVendorTarGz = $path . 'vendor.tar.gz';
                if (file_exists($limbasVendorTarGz)) {
                    $pathVendor = COREPATH.'../';
                    # extract new assets
                    system("tar xzf '$limbasVendorTarGz' -C '$pathVendor'");
                    LimbasLogger::log("overwrite vendor directory", LimbasLogger::LL_NOTICE);
                }else{
                    LimbasLogger::log("overwrite vendor directory failed, $limbasVendorTarGz does not exist!", LimbasLogger::LL_ERROR);
                }
            }

            if (in_array('extensions', $types)) {

                # copy extensions
                $extensionsTarGz = $path . 'extensions.tar.gz';
                if (file_exists($extensionsTarGz)) {
                    $pathExt = DEPENDENTPATH.'EXTENSIONS';

                    # remove old extensions
                    system("rm -r '$pathExt'", $returnCode);
                    if ($returnCode != 0) {
                        LimbasLogger::log("could not delete EXTENSIONS directory: return code $returnCode!", LimbasLogger::LL_ERROR);
                    }

                    # extract new extensions
                    system("tar xzf '$extensionsTarGz' -C '".DEPENDENTPATH."'");
                    LimbasLogger::log("overwrite EXTENSIONS directory", LimbasLogger::LL_NOTICE);
                }else{
                    LimbasLogger::log("overwrite EXTENSIONS directory failed, $extensionsTarGz does not exist!", LimbasLogger::LL_ERROR);
                }

                # copy localassets
                $limbasLocalassetsTarGz = $path . 'localassets.tar.gz';
                if (file_exists($limbasLocalassetsTarGz)) {
                    $pathLass = rtrim(LOCALASSETSPATH,'/');

                    # remove old extensions
                    system("rm -r '$pathLass'", $returnCode);
                    if ($returnCode != 0) {
                        LimbasLogger::log("could not delete localassets directory: return code $returnCode!", LimbasLogger::LL_ERROR);
                    }

                    # extract new extensions
                    system("tar xzf '$limbasLocalassetsTarGz' -C '".PUBLICPATH."'");
                    LimbasLogger::log("overwrite localassets directory", LimbasLogger::LL_NOTICE);
                }else{
                    LimbasLogger::log("overwrite localassets directory failed, $limbasLocalassetsTarGz does not exist!", LimbasLogger::LL_ERROR);
                }
            }

            # call extension function
            if ($confirm_syncimport AND $callExtensionFunctionName AND is_callable($callExtensionFunctionName)) {
                # call function by passed name
                if ($callExtensionFunctionName('after') === false) {
                    LimbasLogger::log("Function '$callExtensionFunctionName' returned failure!", LimbasLogger::LL_ERROR);
                    return false;
                } else {
                    LimbasLogger::log("Function '$callExtensionFunctionName' returned success!", LimbasLogger::LL_NOTICE);
                }
            }

        }
        return true;
    }

# read configuration to array
    private function parseConfigToArray($file)
    {
        require_once(COREPATH . 'admin/tables/tab.lib');

        $config_datei = fopen($file, "r");
        $output = array();
        $output['table'] = array();

        while ($line = fgets($config_datei, 100000)) {

            if (lmb_substr($line, 0, 1) != "#" and lmb_substr($line, 0, 1) != " ") {

                if (lmb_substr($line, 0, 22) == '<begin indexdefinition') {
                    $definition = "index";
                } elseif (lmb_substr($line, 0, 20) == "<end indexdefinition") {
                    $definition = "";
                } elseif (lmb_substr($line, 0, 20) == "<begin keydefinition") {
                    $definition = "key";
                } elseif (lmb_substr($line, 0, 18) == "<end keydefinition") {
                    $definition = "";
                } elseif (lmb_substr($line, 0, 21) == "<begin viewdefinition") {
                    $definition = "view";
                } elseif (lmb_substr($line, 0, 19) == "<end viewdefinition") {
                    $definition = "";
                } elseif (lmb_substr($line, 0, 24) == "<begin triggerdefinition") {
                    $definition = "trigger";
                } elseif (lmb_substr($line, 0, 22) == "<end triggerdefinition") {
                    $definition = "";
                } elseif (lmb_substr($line, 0, 22) == "<begin tabledefinition") {
                    $definition = "table";
                } elseif (lmb_substr($line, 0, 20) == "<end tabledefinition") {
                    $definition = "";
                } elseif ($definition == "table") {
                    unset($cdf);

                    $cdf = explode("::", $line);

                    $table = array();
                    $fieldname = parse_db_syntax($cdf[0], 2); # fieldname
                    $table['datatype'] = trim($cdf[1]);   # datatype
                    $table['codetype'] = trim($cdf[2]);   # codetype
                    $table['fieldlength'] = trim($cdf[3]);   # fieldlength
                    $table['scale'] = (trim($cdf[4]) == '') ? '0' : trim($cdf[4]);   # scale
                    $table['mode'] = trim($cdf[5]);    # mode
                    #$table['default'] = (trim($cdf[6]) == '') ? 'NULL' : trim($cdf[6]);   # default
                    $table['default'] = trim($cdf[6]);   # default
                    $table['database_specific_datatype'] = constant("LMB_DBTYPE_" . trim($cdf[1])); # database specific datatype
                    if ($cdf[2]) {
                        $table['database_specific_codetype'] = constant("LMB_DBTYPE_" . trim($cdf[2]));
                    } # database specific codetype

                    $output['table'][$fieldname] = $table;
                } elseif ($definition == "index") {
                    unset($cdf);
                    $cdf = explode("::", $line);
                    $output['index'][] = array(lmb_strtoupper($cdf[0]), $cdf[1]);
                } elseif ($definition == "view") {
                    $output['view'][] = array(lmb_substr($line, 0, lmb_strpos($line, "::")), lmb_substr($line, lmb_strpos($line, "::") + 2, lmb_strlen($line)));
                } elseif ($definition == "key") {
                    /*
                      unset($cdf);

                      $cdf = explode("::",$line);
                      $sqlquery = "ALTER TABLE ".$newtabname[$cdf[0]]." ADD FOREIGN KEY(".$cdf[1].") REFERENCES ".$cdf[2]."(".$cdf[3].") ON DELETE RESTRICT";
                      $rs5 = lmbdb_exec($db,$sqlquery);
                      $outdesc1 = "<div>$lang[1026] <FONT COLOR=\"#0033CC\">".$newtabname[$cdf[0]]."</FONT></div>\n<Script language=\"JavaScript\">scrolldown();</SCRIPT>\n";
                      $outdesc2 = "<div style=\"color:red;\">$lang[1026] <FONT COLOR=\"#0033CC\">".$newtabname[$cdf[0]]."</FONT> $lang[1019]</div>\n<Script language=\"JavaScript\">scrolldown();</SCRIPT>\n";
                      if($rs5){
                      if($GLOBALS["action"]){$this->htmloutput[] = $outdesc1."\n";}
                      }else{
                      $GLOBALS["LAST_SQL"]["sql"][] = $sqlquery;
                      $GLOBALS["LAST_SQL"]["desc1"][] = $outdesc1;
                      $GLOBALS["LAST_SQL"]["desc2"][] = $outdesc2;
                      if($GLOBALS["action"]){$this->htmloutput[] = $outdesc2."\n";}
                      }
                     */
                } else {
                    $definition = "";
                }
            }
        }
        return $output;
    }

    private function tableOutput1($diff, $description)
    {
        if ($out = $this->printCompareTable1($diff)) {
            #$this->htmloutput[] = '<h3>' . $description . '</h3><div style="max-height:400px;overflow:auto;border:1px solid grey;padding:4px;">';
            $this->structureoutput[$description][] = $out;
            #$this->htmloutput[] = $out;
            #$this->htmloutput[] = '</div><br><br>';
        }
    }

    private function tableOutput2($diff, $description)
    {
        if ($out = $this->printCompareTable2($diff)) {
            #$this->htmloutput[] = '<h3>' . $description . '</h3><div style="max-height:400px;overflow:auto;border:1px solid grey;padding:4px;">';
            $this->structureoutput[$description][] = $out;
            #$this->htmloutput[] = $out;
            #$this->htmloutput[] = '</div><br><br>';
        }
    }


    /*
     * Compares two files and returns diff or false on error
     *
     * @param string    $path           path where the tar to import is located
     * @param string    $pathtmp        path where the tar to local export is located
     * @param string    $tarFileName    filename of both files to compare
     * @param array     $primaryKeys    array of columnNames that will be used to determine if a dataset
     *                                  of one table is also in the other
     * @param array     $ignoreColumns  array of columnNames that will be ignored and not compared
     * @param function|string  $desc    user function (type, leftData, rightData, key, columnIds)=>string
     *                                  to add someDesc into the output array
     * @param function  $filterFunc     user function(key, value, columnIds)=>bool to filter left/right arrays
     * @return false|array [
     *   'diff': array of [primaryKey => values, 'type' => added/removed, 'desc' => someDesc]
     *        or array of [primaryKey => [columnKey => ['name' => columnName, 'left' => value, 'right' => value], 'type' => changed, 'desc' => someDesc]]
     *   'columnIds': array of [columnName => columnId]
     * ]
     */
    private function compareTableData($path, $pathtmp, $tarFileName, $primaryKeys, $ignoreColumns = array(), $desc = null, $filterFunc = null)
    {
        if (is_string($desc)) {
            # gets content of column $desc
            $descFunc = function ($type, $leftData, $rightData, $key, $columnIds) use ($desc) {
                # always take right data if existent
                $data = ($type == 'removed') ? $leftData : $rightData;

                # return column inhalt into desc field
                return $data[$key][$columnIds[$desc]];
            };
        } elseif (is_callable($desc)) {
            # desc function passed
            $descFunc = $desc;
        } else {
            # no desc
            $descFunc = function () {
            };
        }

        # collect table data
        $left = $this->tarToPrimaryKeyIndexedArr($path, $tarFileName, $primaryKeys, $header);
        if ($left === false) {
            LimbasLogger::log("Could not read '$tarFileName' in '$path' into array indexed by " . json_encode($primaryKeys) . "!", LimbasLogger::LL_ERROR);
            return false;
        }
        $_header = array();
        $right = $this->tarToPrimaryKeyIndexedArr($pathtmp, $tarFileName, $primaryKeys, $_header);
        if ($right === false) {
            LimbasLogger::log("Could not read '$tarFileName' in '$pathtmp' into array indexed by " . json_encode($primaryKeys) . "!", LimbasLogger::LL_ERROR);
            return false;
        }

        $columnIds = array_flip($header);
        $result = array(
            'columnIds' => $columnIds,
            'diff' => array()
        );

        # filter
        if ($filterFunc != null && is_callable($filterFunc)) {
            foreach ($left as $leftKey => $leftData) {
                if (!$filterFunc($leftKey, $leftData, $columnIds)) {
                    unset($left[$leftKey]);
                }
            }
            foreach ($right as $rightKey => $rightData) {
                if (!$filterFunc($rightKey, $rightData, $columnIds)) {
                    unset($right[$rightKey]);
                }
            }
        }

        #$this->htmloutput[] = json_encode($left);
        #$this->htmloutput[] = json_encode($right);

        # find entries only in left
        foreach ($left as $leftKey => $leftData) {
            # remove all ignored columns
            foreach ($ignoreColumns as $someKey => $columnName) {
                $columnId = $columnIds[$columnName];
                unset($left[$leftKey][$columnId]);
            }

            # remove if only in left
            if (!array_key_exists($leftKey, $right)) {
                $result['diff'][$leftKey] = $left[$leftKey];
                $result['diff'][$leftKey]['type'] = 'removed';
                $result['diff'][$leftKey]['desc'] = $descFunc('removed', $left, null, $leftKey, $columnIds);
                unset($left[$leftKey]);
            }
        }

        # find entries only in right
        foreach ($right as $rightKey => $rightData) {
            # remove all ignored columns
            foreach ($ignoreColumns as $someKey => $columnName) {
                $columnId = $columnIds[$columnName];
                unset($right[$rightKey][$columnId]);
            }

            # remove if only in right
            if (!array_key_exists($rightKey, $left)) {
                $result['diff'][$rightKey] = $right[$rightKey];
                $result['diff'][$rightKey]['type'] = 'added';
                $result['diff'][$rightKey]['desc'] = $descFunc('added', null, $right, $rightKey, $columnIds);
                unset($right[$rightKey]);
            }
        }

        # compare entries in both
        foreach ($left as $key => $leftData) {
            $rightData = $right[$key];

            foreach ($leftData as $valueKey => $valueLeft) {
                $valueRight = $rightData[$valueKey];
                if ($valueRight != $valueLeft) {
                    if (!array_key_exists($key, $result['diff'])) {
                        $result['diff'][$key] = array();
                        $result['diff'][$key]['type'] = 'changed';
                        $result['diff'][$key]['desc'] = $descFunc('changed', $left, $right, $key, $columnIds);
                    }

                    $result['diff'][$key][$valueKey] = array(
                        'name' => $header[$valueKey],
                        'left' => $valueLeft,
                        'right' => $valueRight
                    );
                }
            }
        }

        return $result;
    }

    /*
     * Opens a tar->csv file and creates array indexed with primary key
     * @return array|false  primary key indexed array or false on error
     */
    private function tarToPrimaryKeyIndexedArr($path, $tarFileName, $primaryKeys, &$header)
    {
        # get data from tar as array
        $dataArr = $this->tarToArr($path, $tarFileName, $header);
        if ($dataArr === false) {
            LimbasLogger::log("Could not load tar '$path$tarFileName'!", LimbasLogger::LL_ERROR);
            return false;
        }

        # get primary key indices
        $primaryKeyIndices = array();
        $fieldNames = array_flip($header);
        foreach ($primaryKeys as $someKey => $keyName) {
            if (array_key_exists($keyName, $fieldNames)) {
                $primaryKeyIndices[] = $fieldNames[$keyName];
            } else {
                LimbasLogger::log("Key '$keyName' not found in table! (" . json_encode($fieldNames) . ")", LimbasLogger::LL_ERROR);
                return false;
            }
        }

        # index array
        $indexedDataArr = array();
        foreach ($dataArr as $someKey => $data) {
            $primaryKey = $this->getCompositePrimaryKey($primaryKeyIndices, $data);

            # key already exists -> primary key must be invalid as it must be unique
            if (array_key_exists($primaryKey, $indexedDataArr)) {
                LimbasLogger::log("Primary key $primaryKey (" . json_encode($primaryKeyIndices) . ") is invalid, entry already exists! In file '$tarFileName'", LimbasLogger::LL_ERROR);
                return false;
            }

            $indexedDataArr[$primaryKey] = $data;
        }

        return $indexedDataArr;
    }

    /*
     * Opens a tar->csv file and creates array from it
     * @return array|false  data array or false on error
     */
    private function tarToArr($path, $tarFileName, &$header)
    {
        $tarPath = $path . $tarFileName;
        $extractDir = $path . 'extract_' . $tarFileName;
        $extractFile = $extractDir . '/export.dat';

        # check if tar to extract is missing
        if (!file_exists($tarPath)) {
            LimbasLogger::log("File to extract '$tarPath' does not exist!", LimbasLogger::LL_ERROR);
            return false;
        }

        # extract lmb_forms archive
        exec("mkdir '$extractDir'");
        exec("tar -x -C '$extractDir' -f '$tarPath'");

        # check if file to read from is missing
        if (!file_exists($extractFile)) {
            LimbasLogger::log("Extracted file '$extractFile' does not exist!", LimbasLogger::LL_ERROR);
            return false;
        }

        # load csv data file into php array indexed by primary keys of table
        $file = fopen($extractFile, "r");
        $header = lmb_fgetcsv($file);
        $dataArr = array();
        while ($line = lmb_fgetcsv($file)) {
            $dataArr[] = $line;
        }
        fclose($file);

        return $dataArr;
    }

    /*
     * Combines primary key indices to get a unique string
     * @return string   primary key of format "key_{id1}_{id2}..."
     */
    private function getCompositePrimaryKey($primaryKeyIndices, $dataArr)
    {
        $primaryKey = "key";
        foreach ($primaryKeyIndices as $someKey => $keyIndex) {
            $primaryKey .= '_' . $dataArr[$keyIndex];
        }
        return $primaryKey;
    }

    /*
     * Replaces key from diff with readable key and groups by it
     * @return array    $arr grouped by key
     */
    private function groupBy1Key($arr, $prefix)
    {
        $grouped = array();

        foreach ($arr['diff'] as $key => $value) {
            $keyParts = explode('_', $key);
            $groupedKey = "$prefix $keyParts[1]";
            $grouped[$groupedKey] = $value;
        }

        return $grouped;
    }

    /*
     * Replaces keys from diff with readable keys and groups by them
     * @return array    $arr grouped by keys
     */
    private function groupBy2Keys($arr, $prefixKey1, $prefixKey2)
    {
        $grouped = array();

        foreach ($arr['diff'] as $key => $value) {
            $keyParts = explode('_', $key);

            $groupedKey = "$prefixKey1 $keyParts[1]";

            if (is_array($grouped) AND !array_key_exists($groupedKey, $grouped)) {
                $grouped[$groupedKey] = array();
            }

            $grouped[$groupedKey]["$prefixKey2 $keyParts[2]"] = $value;
        }

        return $grouped;
    }

    /*
     * Prints a table for comparison of data from functions compareTableData and groupBy2Keys with 2 primary keys, grouped by one of the keys
     */

    private function printCompareTable2($dataArrArr)
    {
        if(!is_array($dataArrArr)){
            return '';
        }
        $count = lmb_count($dataArrArr);
        if ($count > 250) {
            return "Too many to display ({$count})";
        }

        $out = "";
        foreach ($dataArrArr as $topKey => $dataArr) {
            # expand all children
            $onclick = "
            $(this).next().find('p').children('span').each(function() {
                var icon = $(this).find('.lmb-icon').first();
                if(icon && icon.hasClass('lmb-angle-down')){
                    icon.removeClass('lmb-angle-down').addClass('lmb-angle-up');
                    $(this).next().next().show('fade');
                }
            });";
            # border div
            $out .= '<span title="expand all children" onclick="' . $onclick . '" style="cursor:pointer;"><i class="lmb-icon lmb-angle-double-down"></i><strong>' . $topKey . '</strong></span>';
            $out .= '<div style="border:1px solid grey;padding:4px;">';

            # render diff
            $out .= $this->printCompareTable1($dataArr);

            # end border div
            $out .= '</div><br>';
        }

        return $out;
    }


    /*
     * Prints a table for comparison of data from function compareTableData and groupBy1Key
     */
    private function printCompareTable1($dataArr)
    {

        if(!is_array($dataArr)){
            return '';
        }
        $count = lmb_count($dataArr);
        if ($count > 250) {
            return "Too many to display ({$count})";
        }

        $red = '#f00';
        $green = '#05ce05';
        $yellow = '#ddaa1e';

        $out = "";
        foreach ($dataArr as $subKey => $data) {

            # add user-desc, cap to 80 chars but show full text on hover
            $data['desc'] = htmlspecialchars($data['desc']);
            if (strlen($data['desc']) > 80) {
                $data['desc'] = '<span title="' . $data['desc'] . '">' . substr($data['desc'], 0, 80) . '...</span>';
            }
            $key = $data['desc'] ? "$subKey (<i>{$data['desc']}</i>)" : "$subKey";

            # added
            if ($data['type'] == 'added') {
                $out .= "<p><i class=\"lmb-icon\"></i><span style=\"color:$red;\">delete $key</span></p>";
            }

            # removed
            if ($data['type'] == 'removed') {
                $out .= "<p><i class=\"lmb-icon\"></i><span style=\"color:$green;\">create $key</span></p>";
            }

            # changed
            if ($data['type'] == 'changed') {
                # * <key>
                $onclick = "
                    $(this).next().next().toggle('fade');
                    if($(this).children().first().hasClass('lmb-angle-down')) {
                        $(this).children().first().removeClass('lmb-angle-down').addClass('lmb-angle-up');
                    } else {
                        $(this).children().first().removeClass('lmb-angle-up').addClass('lmb-angle-down');
                    }";
                $out .= "<p><span onclick=\"$onclick\" style=\"cursor:pointer;\"><i class=\"lmb-icon lmb-angle-down\"></i><span style=\"color:$yellow;\">change $key</span></span><br>";

                # print diff
                $out .= '<table style="margin-left:40px;">';
                foreach ($data as $columnKey => $columnData) {
                    # dont render non-diff information
                    if ($columnKey == 'type' || $columnKey == 'desc') {
                        continue;
                    }

                    # cap to 80 chars
                    # if(strlen($columnData['left']) > 80) { $columnData['left'] = substr($columnData['left'], 0, 80) . '...'; }
                    # if(strlen($columnData['right']) > 80) { $columnData['right'] = substr($columnData['right'], 0, 80) . '...'; }

                    # diff: <name> <left> <right>
                    $out .= "<tr>
                    <td style=\"padding-right:10px; vertical-align:top;\"><i>{$columnData['name']}:</i></td>
                    <td>" . textDiff($columnData['left'], $columnData['right']) . "</td>
                    </tr>";
                }
                $out .= '</table></p>';
            }
        }

        return $out;
    }

    private function stripNonVisibleAttrs($html)
    {
        if(!$html){return;}

        $domd = new DOMDocument();
        $domd->loadHTML($html);
        $domx = new DOMXPath($domd);

        # remove max-height and overflow
        $items = $domx->query("//div[@style]");
        foreach ($items as $item) {
            $style = $item->getAttribute("style");
            $style = preg_replace('/max-height:.*?;/', '', $style);
            $style = preg_replace('/overflow:.*?;/', '', $style);
            $item->setAttribute("style", $style);
        }

        # remove display
        $items = $domx->query("//table[@style]");
        foreach ($items as $item) {
            $style = $item->getAttribute("style");
            $style = preg_replace('/display:.*?;/', '', $style);
            $item->setAttribute("style", $style);
        }

        # remove onclick
        $items = $domx->query("//*[@onclick]");
        foreach ($items as $item) {
            $item->removeAttribute("onclick");
        }

        return $domd->saveHTML();
    }

    private function writeDiffIntoFile($diff)
    {
        global $umgvar;
        global $session;

        if(is_array($diff)){
            $diff = implode("\n",$diff);
        }

        //$diff = $this->stripNonVisibleAttrs($diff);
        $url = (lmb_substr($umgvar['url'], 0, -1) === '/') ? $umgvar['url'] : ($umgvar['url'] . '/');

        # write into file
        $pathOffset = 'USER/' . $session['user_id'] . '/temp/diff.html';
        $outputFile = $umgvar['path'] . '/' . $pathOffset;
        $downloadFile = $url . $pathOffset;
        if (file_put_contents($outputFile, $diff) !== false and file_exists($outputFile)) {
            return $downloadFile;
        }
        return false;
    }

}

function lmb_renderDiffToBootstrap($diff){
    global $umgvar;
    global $session;

    ob_start();

    ?>


    <link rel="stylesheet" type="text/css" href="../../../assets/css/<?=$session['css']?>?v=<?=$umgvar["version"]?>">
    <script src="../../../assets/vendor/bootstrap/bootstrap.bundle.min.js?v=<?=$umgvar['version']?>"></script>
    
    <?php

    renderDiffToBootstrap($diff);

    $output = ob_get_clean();

    return $output;

}

function renderDiffToBootstrap($diff){

    static $sublevel;
    $sublevel++;
    $expand = 1;

    if(!is_array($diff)) {
        echo $diff;
        return;
    }

    if(is_numeric(key($diff))){
        ?>
        <ul class="list-group">
        <?php
        foreach($diff as $key => $value) {
            echo "<li class=\"list-group-item py-0\">$value</li>";
        }
        ?>
        </ul>
        <?php
        return;
    }

    if(is_array($diff)) {
        ?>
        <div class="accordion" id="accordionDiff-<?=$sublevel?>">
        <?php

        foreach($diff as $key => $value){
            $level++;
            ?>
              <div class="accordion-item">
                <h2 class="accordion-header" id="heading-<?=$sublevel."-".$level?>">
                  <button class="accordion-button <?=($level != 1 AND !$expand)?'collapsed':''?>" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-<?=$sublevel."-".$level?>" aria-expanded="<?=($level == 1 OR $expand)?'true':'false'?>" aria-controls="collapse-<?=$sublevel."-".$level?>">
                    <?=$key?>
                  </button>
                </h2>
                <div id="collapse-<?=$sublevel."-".$level?>" class="accordion-collapse collapse <?=($level == 1 OR $expand)?'show':''?>" aria-labelledby="heading-<?=$sublevel."-".$level?>" >
                  <div class="accordion-body">
                    <?php
                        renderDiffToBootstrap($value);
                    ?>
                  </div>
                </div>
              </div>
        <?php }
        echo "</div>";
    }

}
