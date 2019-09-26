<?php

/*
 * Copyright notice
 * (c) 1998-2019 Limbas GmbH(support@limbas.org)
 * All rights reserved
 * This script is part of the LIMBAS project. The LIMBAS project is free software; you can redistribute it and/or modify it on 2 Ways:
 * Under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * Or
 * In a Propritary Software Licence http://limbas.org
 * The GNU General Public License can be found at http://www.gnu.org/copyleft/gpl.html.
 * A copy is found in the textfile GPL.txt and important notices to the license from the author is found in LICENSE.txt distributed with these scripts.
 * This script is distributed WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * This copyright notice MUST APPEAR in all copies of the script!
 * Version 3.6
 */

/*
 * ID:
 */

require_once('extra/lmbObject/log/LimbasLogger.php');

/**
 * Performs a secure version of the sync import (only import the structure of the tables) by first doing a precheck,
 * then if no error occured doing the actual import.
 * Wrapped in a transaction -> Rolls back everything on error
 *
 * @param $file_to_import               path to .tar.gz file that should be imported
 * @param bool $file_uploaded_via_http whether the file was uploaded via an http upload or not
 * @return array                        error log TODO
 */
function secure_sync_import($file_to_import, $file_uploaded_via_http = true, $precheck, $confirm_syncimport)
{
    # try precheck
    $result = sync_import(null, $precheck, false, $file_to_import, $file_uploaded_via_http);

    # precheck error
    if (!$result['success'] || !$confirm_syncimport) {
        return $result;
    }

    # start transaction
    lmb_StartTransaction();

    # try import    
    $result = sync_import(null, false, true, $file_to_import, $file_uploaded_via_http);

    lmb_EndTransaction($result['success']);
    return $result;
}

function errorsOccured($log) {
    foreach($log as $entry) {
        if ($entry['level'] == LimbasLogger::getLLString(LimbasLogger::LL_ERROR)) {
            return true;
        }
    }
    return false;
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
 * @return array    'success' => bool, 'errLog' => array
 */
function sync_import($path_right_export = null, $precheck, $confirm_syncimport, $file_to_import, $file_uploaded_via_http = true, $reverse_diff = false) {
    $success = sync_import_inner($path_right_export, $precheck, $confirm_syncimport, $file_to_import, $file_uploaded_via_http, $reverse_diff);
    $log = LimbasLogger::getLogLines();
    return array('errLog' => $log, 'success' => $success && !errorsOccured($log));
}
function sync_import_inner($path_right_export = null, $precheck, $confirm_syncimport, $file_to_import, $file_uploaded_via_http = true, $reverse_diff = false)
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
        require_once('admin/tools/import.dao');
    }

    if ($precheck) {
        # include export functions
        require_once('admin/tools/export.lib');

        # empty temp directories
        rmdirr($pathtmp);
        mkdir($pathtmp);
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
                if (lmb_stripos($table, 'lmb_') === false
                    && lmb_stripos($table, 'systemtables') === false
                    && lmb_stripos($table, 'system_files') === false
                    && lmb_stripos($table, 'ldms_') === false
                ) {
                    $exptables[] = $table;
                }
            }
            $exptables[] = dbf_4('lmb_trigger');
            $exptables[] = dbf_4('lmb_conf_tables');
            $exptables[] = dbf_4('lmb_conf_fields');
            $exptables[] = dbf_4('lmb_conf_views');
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
        if (in_array('charts',$types)) {
            $exptables[] = dbf_4('lmb_chart_list');
            $exptables[] = dbf_4('lmb_charts');
        }

        // workflow
        if (in_array('work',$types)) {
            $exptables[] = dbf_4('lmb_wfl_task');
            $exptables[] = dbf_4('lmb_wfl');
        }

        // group & rules
        if (in_array('group',$types)) {
            $exptables[] = dbf_4('lmb_groups');
            if (in_array('tabs',$types)) {
                $exptables[] = dbf_4('lmb_rules_tables');
                $exptables[] = dbf_4('lmb_rules_fields');
                $exptables[] = dbf_4('lmb_rules_dataset');
            }
            if (in_array('forms',$types) OR in_array('rep',$types) OR in_array('work',$types) OR in_array('charts',$types) OR in_array('reminder',$types)) {
                $exptables[] = dbf_4('lmb_rules_repform');
            }
            if (in_array('links',$types)) {
                $exptables[] = dbf_4('lmb_rules_action');
            }
            if (in_array('dms',$types)) {
                $exptables[] = dbf_4('ldms_rules');
            }
        }
        
        // DMS
        if (in_array('dms',$types)) {
            $exptables[] = dbf_4('ldms_structure');
        }

        // system
        if (in_array('system',$types)) {
            $exptables[] = dbf_4('lmb_field_types');
            $exptables[] = dbf_4('lmb_fonts');
            $exptables[] = dbf_4('lmb_mimetypes');
            $exptables[] = dbf_4('lmb_lang');
            $exptables[] = dbf_4('lmb_umgvar');
            $exptables[] = dbf_4('lmb_action');
            $exptables[] = dbf_4('lmb_dbpatch');
        }

        // === sonst ===
        // snapshot
        if (in_array('snapshots',$types)) {
            $exptables[] = dbf_4('lmb_snap');
            $exptables[] = dbf_4('lmb_snap_shared');
        }

        // reminder
        if (in_array('reminder',$types)) {
            $exptables[] = dbf_4('lmb_reminder_list');
        }

        // currency
        if (in_array('currency',$types)) {
            $exptables[] = dbf_4('lmb_currency');
        }

        // colorscheme
        if (in_array('colorscheme',$types)) {
            $exptables[] = dbf_4('lmb_colorschemes');
        }

        // usercolors
        if (in_array('usercolors',$types)) {
            $exptables[] = dbf_4('lmb_user_colors');
        }

        // crontab
        if (in_array('crontab',$types)) {
            $exptables[] = dbf_4('lmb_crontab');
        }
        
        // links
        if (in_array('links',$types)) {
            $exptables[] = dbf_4('lmb_action_depend');
        }
        
        // pools
        if (in_array('pools',$types)) {
            $exptables[] = dbf_4('lmb_select_p');
            $exptables[] = dbf_4('lmb_select_w');
            $exptables[] = dbf_4('lmb_attribute_p');
            $exptables[] = dbf_4('lmb_attribute_w');
        }
        
        // rules&groups
        if (in_array('rules',$types)) {
            $exptables[] = dbf_4('lmb_groups');
            $exptables[] = dbf_4('lmb_rules_action');
            $exptables[] = dbf_4('lmb_rules_repform');
            $exptables[] = dbf_4('lmb_rules_tables');
            $exptables[] = dbf_4('lmb_rules_fields');
            $exptables[] = dbf_4('lmb_rules_dataset');
            $exptables[] = dbf_4('ldms_rules');
        }
        
        // synchronisation
        if (in_array('synchronisation',$types)) {
            $exptables[] = dbf_4('lmb_sync_conf');
            $exptables[] = dbf_4('lmb_sync_template');
        }
        
        // === /sonst ===

        // always
        $exptables[] = dbf_4('lmb_dbpatch');

        # TODO add more here

        if ($path_right_export) {
            # copy tar file and extract
            copy($path_right_export, "{$pathtmp}export.tar.gz");
            exec("tar -x -v -C $pathtmp -f {$pathtmp}export.tar.gz");
        } else {
            # export local tables for comparison
            if (lmbExport_ToSystem($exptables, null, null, null, true) === false) {
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
            if ($folderval['typ'][$key] == 'file' AND lmb_substr($fileName, 0, 7) != 'export.') {
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
                    $imptableconf[$table] = parseConfigToArray($path . $file);
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

    # read old configuration    
    $delete = array();
    if ($handle = opendir($pathtmp)) {
        while (false !== ($fileName = readdir($handle))) {
            if ($fileName != '.' && $fileName != '..' && lmb_strtolower(lmb_substr($fileName, -5)) == '.conf') {
                $tableName = str_replace('.conf', '', $fileName);
                if (file_exists($path . $tableName . '.conf')) {
                    $exttableconf[$tableName] = parseConfigToArray($pathtmp . $fileName);
                } else {
                    if (lmb_strtolower(lmb_substr($tableName, 0, 5)) == 'verk_') {
                        $delete[$tableName] = 1;
                    } else {
                        $delete[$tableName] = 2;
                    }

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

    
    // drop all views for postgresql - dependent on fields
    if (in_array('tabs', $types) AND $confirm_syncimport AND LMB_DBFUNC_VIEWDEPENDENCY) {
        if(!lmb_dropView()){
            LimbasLogger::log("Could not delete views!", LimbasLogger::LL_ERROR);
            return false;
        }
    }

    # ====== table diff ======
    foreach ($tables as $table => $value) {
        # table will be created
        if (!array_key_exists($table, $exttableconf)) {
            $output['createtab'][] = '<p style="color:#05ce05">create table ' . $table . '</p>';
            $hasoutput['createtab'] = 1;
            if ($confirm_syncimport) {
                import(false, 'over', null, null, null, null, $table);
            }
        } else {
            $cmp = array();
            foreach ($imptableconf[$table]['table'] as $field => $value1) {
                # field already exists
                if (array_key_exists($field, $exttableconf[$table]['table'])) {
                    $cmptmp = array_diff($imptableconf[$table]['table'][$field], $exttableconf[$table]['table'][$field]);
                    if (count($cmptmp) > 0) {

                        $cmp[$field] = $cmptmp;

                        # Check if fieldtype has change (type & scale)
                        if ($exttableconf[$table]['table'][$field]['datatype'] != 'TEXT' && $exttableconf[$table]['table'][$field]['datatype'] != 'LONG' && ($exttableconf[$table]['table'][$field]['datatype'] != $imptableconf[$table]['table'][$field]['datatype'] || $exttableconf[$table]['table'][$field]['fieldlength'] != $imptableconf[$table]['table'][$field]['fieldlength'] || $exttableconf[$table]['table'][$field]['scale'] != $imptableconf[$table]['table'][$field]['scale'])) {
                            $output[$table]['changefield'][] = '<p>change type of ' . $field . ' [' . $exttableconf[$table]['table'][$field]['datatype'] . '(' . $exttableconf[$table]['table'][$field]['fieldlength'] . ',' . $exttableconf[$table]['table'][$field]['scale'] . ')' . ' -> ' . $imptableconf[$table]['table'][$field]['datatype'] . '(' . $imptableconf[$table]['table'][$field]['fieldlength'] . ',' . $imptableconf[$table]['table'][$field]['scale'] . ')]</p>';
                            $hasoutput['changefield'] = 1;
                            if ($confirm_syncimport) {

                                $newsize = ($imptableconf[$table]['table'][$field]['scale'] == 0) ? $imptableconf[$table]['table'][$field]['fieldlength'] : $imptableconf[$table]['table'][$field]['fieldlength'] . ',' . $imptableconf[$table]['table'][$field]['scale'];
                                $ct = parse_db_type($imptableconf[$table]['table'][$field]['datatype'], $newsize, $imptableconf[$table]['table'][$field]['fieldlength']);
                                $datentyp = $ct[0];
                                if (lmb_strpos($datentyp, 'BOOL') !== false) {
                                    $datentyp = 'BOOLEAN';
                                }
                                if (lmb_strpos($datentyp, 'TEXT') !== false) {
                                    $datentyp = 'TEXT';
                                }

                                $pt = lmb_get_db_fieldtype(lmb_strtoupper($imptableconf[$table]['table'][$field]['datatype']));
                                if (!lmb_convert_fieldtype(null, $pt, $field, $newsize, $datentyp, $table, 1)){
                                #if (!$sqlquery = convert_type($pt, $table, $field, $datentyp)) {
                                    LimbasLogger::log("field '$field' in table '$table' could not be converted to '$datentyp'", LimbasLogger::LL_ERROR);
                                    if(is_array($GLOBALS["alert"])){
                            		      LimbasLogger::log(implode("\n",$GLOBALS["alert"]), LimbasLogger::LL_ERROR);
                                    }
                                    return false;
                                }
                            }
                        } # check if default value has changed / modify field
                        elseif ($exttableconf[$table]['table'][$field]['datatype'] != 'TEXT' && $exttableconf[$table]['table'][$field]['datatype'] != 'LONG' && array_key_exists('default',$cmptmp)) {
                            $output[$table]['changefield'][] = '<p>change default of ' . $field . ' [' . $exttableconf[$table]['table'][$field]['default'] . ' -> ' . $imptableconf[$table]['table'][$field]['default'] . ']</p>';
                            $hasoutput['changefield'] = 1;
                            if ($confirm_syncimport) {
                                /* --- set default value --------------------------------------------- */
                                $parsetype = lmb_get_db_fieldtype(lmb_strtoupper($exttableconf[$table]['table'][$field]['datatype']));
                                $def = trim($imptableconf[$table]['table'][$field]['default']);

                                if ($def == "0") {
                                    $def0 = 1;
                                }

                                if ($parsetype == 1) {
                                    $def = parse_db_int($def);
                                } elseif ($parsetype == 6) {
                                    $def = parse_db_float($def);
                                } elseif ($parsetype == 2) {
                                    $def = "'" . parse_db_string($def) . "'";
                                } elseif ($parsetype == 4) {
                                    if (!$def) {
                                        $def = LMB_DBDEF_NULL;
                                    }
                                } elseif ($parsetype == 3) {
                                    $def = parse_db_bool($def);
                                }

                                if (!$def AND !$def0) {
                                    $def = LMB_DBDEF_NULL;
                                }

                                // modify field
                                if ($sqlquery = dbq_9(array($DBA['DBSCHEMA'], $table, $field, $def))) {
                                    $rs1 = odbc_exec($db, $sqlquery) or errorhandle(odbc_errormsg($db), $sqlquery, $action, __FILE__, __LINE__);
                                }else{
                                    LimbasLogger::log("execute query: $sqlquery", LimbasLogger::LL_INFO);
                                }
                            }
                        }
                    }
                } # create field if not existent
                else {
                    
                    $output[$table]['createfield'][] = '<p style="color:#05ce05">create field ' . $field . ' ' . $imptableconf[$table]['table'][$field]['datatype'].'('.$imptableconf[$table]['table'][$field]['fieldlength'].')</p>'; // in table '.$table.'</p>';
                    //add_field($field,$gtabid,$typ,$typ2,$typ_size,$ifield_id,$description,$spellingf,$default,$sort,$add_permission,$inherit_tab,$inherit_field,$import_typ='default',$extension=null){
                    $hasoutput['createfield'] = 1;
                    
                    if ($confirm_syncimport) {
                        /* --- create new field --------------------------------------------- */
                        $parsetype = lmb_get_db_fieldtype(lmb_strtoupper($imptableconf[$table]['table'][$field]['datatype']));
                        $def = trim($imptableconf[$table]['table'][$field]['default']);

                        if ($def == "0 ") {
                            $def0 = 1;
                        }

                        if ($parsetype == 1) {
                            $def = parse_db_int($def);
                        } elseif ($parsetype == 6) {
                            $def = parse_db_float($def);
                        } elseif ($parsetype == 2) {
                            $def = "'" . parse_db_string($def) . "'";
                        } elseif ($parsetype == 4) {
                            if (!$def) {
                                $def = LMB_DBDEF_NULL;
                            }
                        } elseif ($parsetype == 3) {
                            $def = parse_db_bool($def);
                        }

                        if (!$def AND !$def0) {
                            $def = LMB_DBDEF_NULL;
                        }

                        $ct = parse_db_type($imptableconf[$table]['table'][$field]['datatype'], $imptableconf[$table]['table'][$field]['fieldlength'], $imptableconf[$table]['table'][$field]['fieldlength']);
                        $datentyp = $ct[0];
                        if (lmb_strpos($datentyp, 'BOOL') !== false) {
                            $datentyp = 'BOOLEAN';
                        }
                        if (lmb_strpos($datentyp, 'TEXT') !== false) {
                            $datentyp = 'TEXT';
                        }

                        $sqlquery = 'ALTER TABLE ' . $table . ' ADD COLUMN ' . $field . ' ' . $datentyp . ' DEFAULT ' . $def;
                        $rs1 = odbc_exec($db, $sqlquery) or errorhandle(odbc_errormsg($db), $sqlquery, $action, __FILE__, __LINE__);
                        if (!$rs1) {
                            LimbasLogger::log("Could not add column '$field' in table '$table' with query '$sqlquery'! Message:" . odbc_errormsg($db), LimbasLogger::LL_ERROR);
                            return false;
                        }else{
                            LimbasLogger::log("execute query: $sqlquery", LimbasLogger::LL_INFO);
                        }
                    }
                }
            }

            # delete field
            foreach ($exttableconf[$table]['table'] as $field => $value1) {
                if (!array_key_exists($field, $imptableconf[$table]['table'])) {
                    $output[$table]['deletefield'][] = '<p style="color:#f00">delete field ' . $field . '</p>'; // in table '.$table.'</p>';
                    $hasoutput['deletefield'] = 1;
                    
                    if ($confirm_syncimport) {
                        if ($sqlquery = dbq_22(array($table, $field))) {
                            $rs1 = odbc_exec($db, $sqlquery) or errorhandle(odbc_errormsg($db), $sqlquery, $action, __FILE__, __LINE__);
                        }else{
                            LimbasLogger::log("execute query: $sqlquery", LimbasLogger::LL_INFO);
                        }
                    }
                }
            }


            //break;
        }
    }

    # ====== other diff ======
    if ($precheck) {

        # print collected tables
        echo "<DIV class=\"lmbPositionContainerMain\">";

        # fields to ignore in diff
        $defaultIgnore = array(dbf_4('erstdatum'), dbf_4('editdatum'), dbf_4('erstuser'), dbf_4('edituser'));

        # compare conf & trigger
        if (in_array('tabs', $types)) {
            # filter: take only non-system values
            $filterFunc = function ($key, $value, $columnIds) {
                $internId = $columnIds[dbf_4('intern')];
                return $value[$internId] != 0;
            };

            # get trigger diff
            $triggerDiff = compareTableData($path, $pathtmp, dbf_4('lmb_trigger') . '.tar.gz', array(dbf_4('id')), $defaultIgnore, null, $filterFunc);
            if ($triggerDiff === false) {
                LimbasLogger::log("Could not compare triggers!", LimbasLogger::LL_ERROR);
                return false;
            }
            $triggerDiff = groupBy1Key($triggerDiff, 'trigger');

            # get conf_tables diff
            $lmbConfTablesDiff = compareTableData($path, $pathtmp, dbf_4('lmb_conf_tables') . '.tar.gz', array(dbf_4('tab_id')), $defaultIgnore, dbf_4('tabelle'));
            if ($lmbConfTablesDiff === false) {
                LimbasLogger::log("Could not compare lmb_conf_tables!", LimbasLogger::LL_ERROR);
                return false;
            }
            $lmbConfTablesDiff = groupBy1Key($lmbConfTablesDiff, 'table');

            # get conf_fields diff
            $lmbConfFieldsDiff = compareTableData($path, $pathtmp, dbf_4('lmb_conf_fields') . '.tar.gz', array(dbf_4('tab_id'), dbf_4('field_id')), $defaultIgnore, dbf_4('field_name'));
            if ($lmbConfFieldsDiff === false) {
                LimbasLogger::log("Could not compare lmb_conf_fields!", LimbasLogger::LL_ERROR);
                return false;
            }
            $lmbConfFieldsDiff = groupBy2Keys($lmbConfFieldsDiff, 'table', 'field');

            # get conf_views diff
            $lmbConfViewsDiff = compareTableData($path, $pathtmp, dbf_4('lmb_conf_views') . '.tar.gz', array(dbf_4('id')), $defaultIgnore, array(dbf_4('viewdef')));
            if ($lmbConfViewsDiff === false) {
                LimbasLogger::log("Could not compare lmb_conf_views!", LimbasLogger::LL_ERROR);
                return false;
            }
            $lmbConfViewsDiff = groupBy1Key($lmbConfViewsDiff, 'view');
        }

        # compare forms
        if (in_array('forms', $types)) {
            # get forms diff
            $lmbFormsDiff = compareTableData($path, $pathtmp, dbf_4('lmb_forms') . '.tar.gz', array(dbf_4('form_id'), dbf_4('keyid')), array_merge($defaultIgnore, array(dbf_4('id'), dbf_4('z_index'))), dbf_4('inhalt'));
            if ($lmbFormsDiff === false) {
                LimbasLogger::log("Could not compare forms!", LimbasLogger::LL_ERROR);
                return false;
            }
            $lmbFormsDiff = groupBy2Keys($lmbFormsDiff, 'form', 'ID');
            ksort($lmbFormsDiff);

            # get form list diff
            $lmbFormListDiff = compareTableData($path, $pathtmp, dbf_4('lmb_form_list') . '.tar.gz', array(dbf_4('id')), array_merge($defaultIgnore, array(dbf_4('id'), dbf_4('z_index'))), dbf_4('name'));
            if ($lmbFormListDiff === false) {
                LimbasLogger::log("Could not compare form lists!", LimbasLogger::LL_ERROR);
                return false;
            }
            $lmbFormListDiff = groupBy1Key($lmbFormListDiff, 'form');
        }

        # compare reports
        if (in_array('rep', $types)) {
            # get reports diff
            $lmbReportsDiff = compareTableData($path, $pathtmp, dbf_4('lmb_reports') . '.tar.gz', array(dbf_4('bericht_id'), dbf_4('el_id')), array_merge($defaultIgnore, array(dbf_4('id'), dbf_4('z_index'))), dbf_4('inhalt'));
            if ($lmbReportsDiff === false) {
                LimbasLogger::log("Could not compare reports!", LimbasLogger::LL_ERROR);
                return false;
            }
            $lmbReportsDiff = groupBy2Keys($lmbReportsDiff, 'report', 'ID');
            ksort($lmbReportsDiff);

            # get report list diff
            $lmbReportListDiff = compareTableData($path, $pathtmp, dbf_4('lmb_report_list') . '.tar.gz', array(dbf_4('id')), array_merge($defaultIgnore, array(dbf_4('id'), dbf_4('z_index'))), dbf_4('name'));
            if ($lmbReportListDiff === false) {
                LimbasLogger::log("Could not compare report list!", LimbasLogger::LL_ERROR);
                return false;
            }
            $lmbReportListDiff = groupBy1Key($lmbReportListDiff, 'report');
        }

        # compare charts
        if (in_array('charts', $types)) {
            # get charts diff
            $lmbChartsDiff = compareTableData($path, $pathtmp, dbf_4('lmb_charts') . '.tar.gz', array(dbf_4('chart_id'), dbf_4('field_id')), $defaultIgnore);
            if ($lmbChartsDiff === false) {
                LimbasLogger::log("Could not compare charts!", LimbasLogger::LL_ERROR);
                return false;
            }
            $lmbChartsDiff = groupBy2Keys($lmbChartsDiff, 'chart', 'field');
            ksort($lmbChartsDiff);

            # get chart list diff
            $lmbChartListDiff = compareTableData($path, $pathtmp, dbf_4('lmb_chart_list') . '.tar.gz', array(dbf_4('id')), $defaultIgnore, dbf_4('diag_name'));
            if ($lmbChartListDiff === false) {
                LimbasLogger::log("Could not compare chart list!", LimbasLogger::LL_ERROR);
                return false;
            }
            $lmbChartListDiff = groupBy1Key($lmbChartListDiff, 'chart');
        }

        # compare workflow
        if (in_array('work', $types)) {
            # get wfl task diff
            $lmbWflTaskDiff = compareTableData($path, $pathtmp, dbf_4('lmb_wfl_task') . '.tar.gz', array(dbf_4('wfl_id'), dbf_4('id')), $defaultIgnore, dbf_4('name'));
            if ($lmbWflTaskDiff === false) {
                LimbasLogger::log("Could not compare workflow tasks!", LimbasLogger::LL_ERROR);
                return false;
            }
            $lmbWflTaskDiff = groupBy2Keys($lmbWflTaskDiff, 'workflow', 'task');
            ksort($lmbWflTaskDiff);

            # get wfl diff
            $lmbWflDiff = compareTableData($path, $pathtmp, dbf_4('lmb_wfl') . '.tar.gz', array(dbf_4('id')), $defaultIgnore, dbf_4('name'));
            if ($lmbWflDiff === false) {
                LimbasLogger::log("Could not compare workflow!", LimbasLogger::LL_ERROR);
                return false;
            }
            $lmbWflDiff = groupBy1Key($lmbWflDiff, 'workflow');
        }

        # compare DMS
        if (in_array('dms', $types)) {
            # get reports diff
            $lmbDMSDiff = compareTableData($path, $pathtmp, dbf_4('ldms_structure') . '.tar.gz', array(dbf_4('id')),array_merge($defaultIgnore, array(dbf_4('id'), dbf_4('readonly'))), dbf_4('name'));
            if ($lmbDMSDiff === false) {
                LimbasLogger::log("Could not compare DMS!", LimbasLogger::LL_ERROR);
                return false;
            }
            $lmbDMSDiff = groupBy1Key($lmbDMSDiff, 'file');
        }

        # compare snapshots
        if (in_array('snapshots', $types)) {
            # get snapshots diff
            $lmbSnapshotsDiff = compareTableData($path, $pathtmp, dbf_4('lmb_snap') . '.tar.gz', array(dbf_4('id')), $defaultIgnore, dbf_4('name'));
            if ($lmbSnapshotsDiff === false) {
                LimbasLogger::log("Could not compare snapshots!", LimbasLogger::LL_ERROR);
                return false;
            }
            $lmbSnapshotsDiff = groupBy1Key($lmbSnapshotsDiff, 'snapshot');

            # get shared snapshots diff
            $lmbSharedSnapshotsDiff = compareTableData($path, $pathtmp, dbf_4('lmb_snap_shared') . '.tar.gz', array(dbf_4('snapshot_id'), dbf_4('id')), $defaultIgnore, dbf_4('entity_type'));
            if ($lmbSharedSnapshotsDiff === false) {
                LimbasLogger::log("Could not compare shared snapshots!", LimbasLogger::LL_ERROR);
                return false;
            }
            $lmbSharedSnapshotsDiff = groupBy2Keys($lmbSharedSnapshotsDiff, 'snapshot', 'share');
        }

        # compare currency
        if (in_array('currency', $types)) {
            # get currency diff
            $lmbCurrencyDiff = compareTableData($path, $pathtmp, dbf_4('lmb_currency') . '.tar.gz', array(dbf_4('id')), $defaultIgnore, dbf_4('currency'));
            if ($lmbCurrencyDiff === false) {
                LimbasLogger::log("Could not compare currency!", LimbasLogger::LL_ERROR);
                return false;
            }
            $lmbCurrencyDiff = groupBy1Key($lmbCurrencyDiff, 'currency');
        }

        # compare reminder
        if (in_array('reminder', $types)) {
            # get reminder diff
            $lmbReminderDiff = compareTableData($path, $pathtmp, dbf_4('lmb_reminder_list') . '.tar.gz', array(dbf_4('tab_id'), dbf_4('id')), $defaultIgnore);
            if ($lmbReminderDiff === false) {
                LimbasLogger::log("Could not compare reminders!", LimbasLogger::LL_ERROR);
                return false;
            }
            $lmbReminderDiff = groupBy2Keys($lmbReminderDiff, 'table', 'reminder');
        }

        # compare colorschemes
        if (in_array('colorscheme', $types)) {
            # get colorschemes diff
            $lmbColorschemesDiff = compareTableData($path, $pathtmp, dbf_4('lmb_colorschemes') . '.tar.gz', array(dbf_4('id')), $defaultIgnore, dbf_4('name'));
            if ($lmbColorschemesDiff === false) {
                LimbasLogger::log("Could not compare colorschemes!", LimbasLogger::LL_ERROR);
                return false;
            }
            $lmbColorschemesDiff = groupBy1Key($lmbColorschemesDiff, 'scheme');
        }

        # compare user colors
        if (in_array('usercolors', $types)) {
            # get usercolor diff
            $lmbUsercolorDiff = compareTableData($path, $pathtmp, dbf_4('lmb_user_colors') . '.tar.gz', array(dbf_4('userid'), dbf_4('id')), $defaultIgnore, dbf_4('wert'));
            if ($lmbUsercolorDiff === false) {
                LimbasLogger::log("Could not compare usercolors!", LimbasLogger::LL_ERROR);
                return false;
            }
            $lmbUsercolorDiff = groupBy2Keys($lmbUsercolorDiff, 'user', 'color');
        }

        # compare crontab
        if (in_array('crontab', $types)) {
            # get crontab diff
            $lmbCrontabDiff = compareTableData($path, $pathtmp, dbf_4('lmb_crontab') . '.tar.gz', array(dbf_4('id')), $defaultIgnore, dbf_4('description'));
            if ($lmbCrontabDiff === false) {
                LimbasLogger::log("Could not compare crontab!", LimbasLogger::LL_ERROR);
                return false;
            }
            $lmbCrontabDiff = groupBy1Key($lmbCrontabDiff, 'cron entry');
        }

        # compare menustructure
        if (in_array('links', $types)) {
            # get menustructure diff
            $lmbMenustructureDiff = compareTableData($path, $pathtmp, dbf_4('lmb_action_depend') . '.tar.gz', array(dbf_4('id')), $defaultIgnore);
            if ($lmbMenustructureDiff === false) {
                LimbasLogger::log("Could not compare lmb_action_depend!", LimbasLogger::LL_ERROR);
                return false;
            }
            $lmbMenustructureDiff = groupBy1Key($lmbMenustructureDiff, 'menu entry');
        }

        # compare pools
        if (in_array('pools', $types)) {
            # get pool diff
            $lmbPoolsDiff = compareTableData($path, $pathtmp, dbf_4('lmb_attribute_p') . '.tar.gz', array(dbf_4('id')), $defaultIgnore, dbf_4('name'));
            if ($lmbPoolsDiff === false) {
                LimbasLogger::log("Could not compare pools!", LimbasLogger::LL_ERROR);
                return false;
            }
            $lmbPoolsDiff = groupBy1Key($lmbPoolsDiff, 'pool');

            # get pool values diff
            $lmbPoolValuesDiff = compareTableData($path, $pathtmp, dbf_4('lmb_attribute_w') . '.tar.gz', array(dbf_4('pool'), dbf_4('id')), $defaultIgnore, dbf_4('wert'));
            if ($lmbPoolValuesDiff === false) {
                LimbasLogger::log("Could not compare pool values!", LimbasLogger::LL_ERROR);
                return false;
            }
            $lmbPoolValuesDiff = groupBy2Keys($lmbPoolValuesDiff, 'pool', 'value');

            # get select diff
            $lmbSelectDiff = compareTableData($path, $pathtmp, dbf_4('lmb_select_p') . '.tar.gz', array(dbf_4('id')), $defaultIgnore, dbf_4('name'));
            if ($lmbSelectDiff === false) {
                LimbasLogger::log("Could not compare selects!", LimbasLogger::LL_ERROR);
                return false;
            }
            $lmbSelectDiff = groupBy1Key($lmbSelectDiff, 'select');

            # get select value diff
            $lmbSelectValueDiff = compareTableData($path, $pathtmp, dbf_4('lmb_select_w') . '.tar.gz', array(dbf_4('pool'), dbf_4('id')), $defaultIgnore, dbf_4('wert'));
            if ($lmbSelectValueDiff === false) {
                LimbasLogger::log("Could not compare select values!", LimbasLogger::LL_ERROR);
                return false;
            }
            $lmbSelectValueDiff = groupBy2Keys($lmbSelectValueDiff, 'select', 'value');
        }

        # compare rights
        if (in_array('rules', $types) || in_array('lmb_groups', $exptables)) {
            # get group diff
            $lmbGroupsDiff = compareTableData($path, $pathtmp, dbf_4('lmb_groups') . '.tar.gz', array(dbf_4('group_id')), $defaultIgnore, dbf_4('name'));
            if ($lmbGroupsDiff === false) {
                LimbasLogger::log("Could not compare groups!", LimbasLogger::LL_ERROR);
                return false;
            }
            $lmbGroupsDiff = groupBy1Key($lmbGroupsDiff, 'group');
        }
        if (in_array('rules', $types) || in_array('lmb_rules_action', $exptables)) {
            # get rules_action diff
            $lmbRulesActionDiff = compareTableData($path, $pathtmp, dbf_4('lmb_rules_action') . '.tar.gz', array(dbf_4('group_id'), dbf_4('link_id')), $defaultIgnore);
            if ($lmbRulesActionDiff === false) {
                LimbasLogger::log("Could not compare action rules!", LimbasLogger::LL_ERROR);
                return false;
            }
            $lmbRulesActionDiff = groupBy2Keys($lmbRulesActionDiff, 'group', 'link entry');
        }
        if (in_array('rules', $types) || in_array('lmb_rules_repform', $exptables)) {
            # get rules_repform diff
            $lmbRulesRepformDiff = compareTableData($path, $pathtmp, dbf_4('lmb_rules_repform') . '.tar.gz', array(dbf_4('group_id'), dbf_4('id')), $defaultIgnore);
            if ($lmbRulesRepformDiff === false) {
                LimbasLogger::log("Could not compare repform rules!", LimbasLogger::LL_ERROR);
                return false;
            }
            $lmbRulesRepformDiff = groupBy2Keys($lmbRulesRepformDiff, 'group', 'report/form entry');
        }
        if (in_array('rules', $types) || in_array('lmb_rules_tables', $exptables)) {
            # get rules_repform diff
            $lmbRulesTableDiff = compareTableData($path, $pathtmp, dbf_4('lmb_rules_tables') . '.tar.gz', array(dbf_4('group_id'), dbf_4('id')), $defaultIgnore);
            if ($lmbRulesTableDiff === false) {
                LimbasLogger::log("Could not compare table rules!", LimbasLogger::LL_ERROR);
                return false;
            }
            $lmbRulesTableDiff = groupBy2Keys($lmbRulesTableDiff, 'group', 'table entry');
        }
        if (in_array('rules', $types) || in_array('lmb_rules_fields', $exptables)) {
            # get rules_repform diff
            $lmbRulesTablefieldDiff = compareTableData($path, $pathtmp, dbf_4('lmb_rules_fields') . '.tar.gz', array(dbf_4('group_id'), dbf_4('id')), $defaultIgnore);
            if ($lmbRulesTablefieldDiff === false) {
                LimbasLogger::log("Could not compare tablefield rules!", LimbasLogger::LL_ERROR);
                return false;
            }
            $lmbRulesTablefieldDiff = groupBy2Keys($lmbRulesTablefieldDiff, 'group', 'tablefield entry');
        }
        
        if (in_array('synchronisation', $types)) {
            $lmbSyncDiff = compareTableData($path, $pathtmp, dbf_4('lmb_sync_conf') . '.tar.gz', array(dbf_4('id')), $defaultIgnore);
            if ($lmbSyncDiff === false) {
                LimbasLogger::log("Could not compare sync conf!", LimbasLogger::LL_ERROR);
                return false;
            }
            $lmbSyncDiff = groupBy1Key($lmbSyncDiff, 'conf_id');
            
            $lmbSyncTemplDiff = compareTableData($path, $pathtmp, dbf_4('lmb_sync_template') . '.tar.gz', array(dbf_4('id')), $defaultIgnore);
            if ($lmbSyncTemplDiff === false) {
                LimbasLogger::log("Could not compare sync template!", LimbasLogger::LL_ERROR);
                return false;
            }
            $lmbSyncTemplDiff = groupBy1Key($lmbSyncTemplDiff, 'template_id');
        }
            
        
        # compare system
        if (in_array('system', $types)) {
            # get lmb_action diff
            $lmbActionDiff = compareTableData($path, $pathtmp, dbf_4('lmb_action') . '.tar.gz', array(dbf_4('id')), $defaultIgnore);
            if ($lmbActionDiff === false) {
                LimbasLogger::log("Could not compare lmb_action!", LimbasLogger::LL_ERROR);
                return false;
            }
            $lmbActionDiff = groupBy1Key($lmbActionDiff, 'link entry');

            # get fieldtypes diff
            $lmbFieldtypesDiff = compareTableData($path, $pathtmp, dbf_4('lmb_field_types') . '.tar.gz', array(dbf_4('id')), $defaultIgnore, dbf_4('datentyp'));
            if ($lmbFieldtypesDiff === false) {
                LimbasLogger::log("Could not compare fieldtypes!", LimbasLogger::LL_ERROR);
                return false;
            }
            $lmbFieldtypesDiff = groupBy1Key($lmbFieldtypesDiff, 'fieldtype');

            # get font diff
            $lmbFontDiff = compareTableData($path, $pathtmp, dbf_4('lmb_fonts') . '.tar.gz', array(dbf_4('id')), $defaultIgnore, dbf_4('name'));
            if ($lmbFontDiff === false) {
                LimbasLogger::log("Could not compare fonts!", LimbasLogger::LL_ERROR);
                return false;
            }
            $lmbFontDiff = groupBy1Key($lmbFontDiff, 'font');

            # get lang diff
            $lmbLangDiff = compareTableData($path, $pathtmp, dbf_4('lmb_lang') . '.tar.gz', array(dbf_4('language_id'), dbf_4('element_id')), $defaultIgnore, dbf_4('wert'));
            if ($lmbLangDiff === false) {
                LimbasLogger::log("Could not compare lang!", LimbasLogger::LL_ERROR);
                return false;
            }
            $lmbLangDiff = groupBy2Keys($lmbLangDiff, 'language', 'value');

            # get mimetype diff
            $lmbMimetypeDiff = compareTableData($path, $pathtmp, dbf_4('lmb_mimetypes') . '.tar.gz', array(dbf_4('id')), $defaultIgnore, dbf_4('mimetype'));
            if ($lmbMimetypeDiff === false) {
                LimbasLogger::log("Could not compare mimetypes!", LimbasLogger::LL_ERROR);
                return false;
            }
            $lmbMimetypeDiff = groupBy1Key($lmbMimetypeDiff, 'mimetype');

            # get umgvar diff
            $lmbUmgvarIgnore = array('company', 'url', 'path', 'backup_default');
            # filter: ignore independent settings like path
            $filterFunc = function ($key, $value, $columnIds) use ($lmbUmgvarIgnore) {
                $formNameId = $columnIds[dbf_4('form_name')];
                return !in_array($value[$formNameId], $lmbUmgvarIgnore);
            };
            $lmbUmgvarDiff = compareTableData($path, $pathtmp, dbf_4('lmb_umgvar') . '.tar.gz', array(dbf_4('id')), $defaultIgnore, dbf_4('form_name'), $filterFunc);
            if ($lmbUmgvarDiff === false) {
                LimbasLogger::log("Could not compare umgvar!", LimbasLogger::LL_ERROR);
                return false;
            }
            $lmbUmgvarDiff = groupBy1Key($lmbUmgvarDiff, 'envvar');
        }

        # output report
        ob_start(); # buffer output to write diff into file

        # tables to delete
        if (is_array($output['deletetab'])) {
            echo '<h3 style="color:#f00">delete tables</h3><div style="max-height:200px;overflow:auto;border:1px solid grey;padding:4px;">';
            foreach ($output['deletetab'] as $value) {
                echo $value;
            }
            echo '</div><br><br>';
        }

        # new tables
        if (is_array($output['createtab'])) {
            echo '<h3 style="color:#05ce05">create tables</h3><div style="max-height:200px;overflow:auto;border:1px solid grey;padding:4px;">';
            foreach ($output['createtab'] as $value) {
                echo $value;
            }
            echo '</div><br><br>';
        }

        # field changes
        if($hasoutput['deletefield'] OR $hasoutput['createfield'] OR $hasoutput['changefield']){
        echo '<h3>fieldchanges per table</h3><div style="border:1px solid lightgrey;padding:4px;">';
        foreach ($output as $table => $_value) {
            if ($table != 'createtab' && $table != 'deletetab') {
                if(is_array($output[$table]['deletefield']) OR is_array($output[$table]['createfield']) OR is_array($output[$table]['changefield'])){
                echo '<strong>' . lmb_strtoupper($table) . '</strong>';
                echo '<div style="overflow:auto;border:1px solid grey;padding:4px 10px;">';

                if (is_array($output[$table]['deletefield'])) {
                    foreach ($output[$table]['deletefield'] as $value) {
                        echo $value;
                    }
                }
                if (is_array($output[$table]['createfield'])) {
                    foreach ($output[$table]['createfield'] as $value) {
                        echo $value;
                    }
                }
                if (is_array($output[$table]['changefield'])) {
                    foreach ($output[$table]['changefield'] as $value) {
                        echo $value;
                    }
                }
                echo '</div><br>';
                }
            }
        }
        echo '</div><br><br>';
        }

        # show diff
        tableOutput1($lmbConfTablesDiff, 'Lmb table changes');
        tableOutput2($lmbConfFieldsDiff, 'Lmb field changes');
        tableOutput1($lmbConfViewsDiff, 'Lmb view changes');

        tableOutput1($triggerDiff, 'Trigger changes');
        tableOutput1($lmbFormListDiff, 'Form list changes');
        tableOutput2($lmbFormsDiff, 'Form changes');
        tableOutput1($lmbReportListDiff, 'Report list changes');
        tableOutput2($lmbReportsDiff, 'Report changes');
        tableOutput1($lmbChartListDiff, 'Chart list changes');
        tableOutput2($lmbChartsDiff, 'Chart changes');
        tableOutput1($lmbWflDiff, 'Workflow changes');
        tableOutput2($lmbWflTaskDiff, 'Workflow task changes');
        tableOutput1($lmbDMSDiff, 'DMS changes');
        tableOutput1($lmbSnapshotsDiff, 'Snapshot changes');
        tableOutput2($lmbSharedSnapshotsDiff, 'Shared snapshot changes');
        tableOutput1($lmbCurrencyDiff, 'Currency changes');
        tableOutput2($lmbReminderDiff, 'Reminder changes');
        tableOutput1($lmbColorschemesDiff, 'Colorscheme changes');
        tableOutput2($lmbUsercolorDiff, 'Usercolor changes');
        tableOutput1($lmbCrontabDiff, 'Crontab changes');
        tableOutput1($lmbMenustructureDiff, 'Menu structure changes');
        tableOutput1($lmbPoolsDiff, 'Pool changes');
        tableOutput2($lmbPoolValuesDiff, 'Pool value changes');
        tableOutput1($lmbSelectDiff, 'Select changes');
        tableOutput2($lmbSelectValueDiff, 'Select value changes');
        tableOutput1($lmbGroupsDiff, 'Group changes');
        tableOutput2($lmbRulesTableDiff, 'Table rule changes');
        tableOutput2($lmbRulesTablefieldDiff, 'Tablefield rule changes');
        tableOutput2($lmbRulesActionDiff, 'Link rule changes');
        tableOutput2($lmbRulesRepformDiff, 'Report/Form rule changes');
        tableOutput1($lmbActionDiff, 'Link changes');
        tableOutput1($lmbFieldtypesDiff, 'Fieldtype changes');
        tableOutput1($lmbFontDiff, 'Font changes');
        tableOutput2($lmbLangDiff, 'Language changes');
        tableOutput1($lmbMimetypeDiff, 'Mimetype changes');
        tableOutput1($lmbUmgvarDiff, 'Environment variable changes');
        tableOutput1($lmbSyncDiff, 'Sync config changes');
        tableOutput1($lmbSyncTemplDiff, 'Sync template changes');

        if($precheck === 2){
            echo '<h3>Systemtables that are overwritten</h3>';
            echo '<div id="rimport_import_scolldown" style="max-height:200px;overflow:auto;border:1px solid grey;padding:4px;">';
            foreach ($tablegrouplist as $tableName => $value) {
                echo "<p>$tableName</p>";
            }
            echo '</div><br><br>';
        }
        $diff = ob_get_clean();
        echo $diff;

        echo "</DIV>";

        $outputDiffFile = writeDiffIntoFile($diff);
        echo "<DIV class=\"lmbPositionContainerMain\">";
        # TODO maybe use this?
        #require_once('lib/context.lib');
        #pop_menu2('test', 'title', null, 'lmb-download');
        echo "<i class=\"lmb-icon lmb-download\"></i>&nbsp;<A NAME=\"download\" HREF=\"$outputDiffFile\" target=\"_new\" style=\"color:green;\">Download html diff</A>";
        echo "</DIV>";
    }

    if ($confirm_syncimport) {
        // store umgvar ignore
        if (in_array('lmb_umgvar', $exptables)) {
            $lmbUmgvarStorage = array();
            foreach ($lmbUmgvarIgnore as $key => $value) {
                $lmbUmgvarStorage[$value] = $umgvar[$value];
            }
        }
        
        // overwrite lmb_* tables
        foreach ($tablegrouplist as $table => $value) {
            $sys = exec('tar -x -C ' . $path . ' -f ' . $path . $table . '.tar.gz');
            deleteExistingTab($table);
            import(false, 'over', null, null, null, null, 'export');
            LimbasLogger::log("import table $table", LimbasLogger::LL_INFO);
        }

        // delete tables
        asort($delete);
        foreach ($delete as $table => $value) {
            deleteExistingTab($table);
            LimbasLogger::log("delete temporary tables", LimbasLogger::LL_INFO);
        }
        
        // table / link rules - generate empty rules if rules not exported
        if ((in_array('tabs',$types) OR in_array('links',$types)) AND !in_array('group',$types)){
            require_once("admin/group/group.lib");
        	$sqlquery = "SELECT GROUP_ID,NAME FROM LMB_GROUPS";
        	$rs = odbc_exec($db,$sqlquery) or errorhandle(odbc_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
        	if(!$rs) {$commit = 1;}
        	while(odbc_fetch_row($rs)){
        	    if(in_array('tabs',$types)){
            		check_grouprights1(odbc_result($rs,"GROUP_ID"));
            		LimbasLogger::log("check table rules for group ".odbc_result($rs,"NAME"), LimbasLogger::LL_INFO);
        	    }
        	    if(in_array('links',$types)){
                    check_grouprights(odbc_result($rs,"GROUP_ID"));
                    del_grouprights(odbc_result($rs,"GROUP_ID"));
            		LimbasLogger::log("check action rules for group ".odbc_result($rs,"NAME"), LimbasLogger::LL_INFO);
        	    }
        	}
        }

        // create views again
        if (in_array('tabs', $types) AND $confirm_syncimport AND LMB_DBFUNC_VIEWDEPENDENCY) {
            foreach ($viewDefinitions as $viewName => $viewDefinition) {
                if (!lmb_createView($viewDefinition, $viewName)) {
                    LimbasLogger::log("Could not create view '$viewName'!", LimbasLogger::LL_ERROR);
                }
            }
        }

        // restore umgvar
        if (in_array('lmb_umgvar', $exptables)) {
            foreach ($lmbUmgvarStorage as $key => $value) {
                $sqlquery = "UPDATE LMB_UMGVAR SET NORM='$value' WHERE FORM_NAME='$key'";
                $rs = odbc_exec($db,$sqlquery) or errorhandle(odbc_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
                if(!$rs) {
                    LimbasLogger::log("Could not restore umgvar!", LimbasLogger::LL_ERROR);
                    return false;
                } else {
                    LimbasLogger::log("Restored umgvar '$key' to '$value'", LimbasLogger::LL_INFO);
                }
            }
        }
        
        
        ################### functions #####################
        
        if (in_array('funcdelsession',$types)){
        	$sqlquery = "DELETE FROM LMB_SESSION";
        	$rs = odbc_exec($db,$sqlquery);
        	LimbasLogger::log("function : delete sessions", LimbasLogger::LL_INFO);
        }
        
        if (in_array('funcmenurefresh',$types)){
            require_once("admin/group/group.lib");
            check_grouprightsAll();
        	LimbasLogger::log("function : refresh link rules ", LimbasLogger::LL_INFO);
        }
        
        if (in_array('functablerefresh',$types)){
            require_once("admin/group/group.lib");
            check_grouprights1All();
        	LimbasLogger::log("function : refresh table/field rules ", LimbasLogger::LL_INFO);
        }
        
        if (in_array('funcsequ',$types)){
            if(lmb_rebuildSequences()){
        	   LimbasLogger::log("function : rebuild sequences ", LimbasLogger::LL_INFO);
            }else{
                LimbasLogger::log("function : rebuild sequences failed ", LimbasLogger::LL_ERROR);
                return false;
            }
        }
        
        if (in_array('functrigger',$types)){
            if(lmb_rebuildTrigger(1)){
        	   LimbasLogger::log("function : rebuild trigger ", LimbasLogger::LL_INFO);
            }else{
                LimbasLogger::log("function : rebuild trigger failed ", LimbasLogger::LL_ERROR);
                return false;
            }
        }

        if (in_array('funcproz',$types)){
            if(dbq_16(array($DBA["DBSCHEMA"],1))){
        	   LimbasLogger::log("function : rebuild db prozedures ", LimbasLogger::LL_INFO);
            }else{
                LimbasLogger::log("function : rebuild db prozedures failed ", LimbasLogger::LL_ERROR);
                return false;
            }
        }
        
        if (in_array('funcindize',$types)){
            if(lmb_rebuildIndex()){
        	   LimbasLogger::log("function : rebuild db indexes ", LimbasLogger::LL_INFO);
            }else{
                LimbasLogger::log("function : rebuild db indexes failed ", LimbasLogger::LL_ERROR);
                return false;
            }
        }

        if (in_array('funcforkey',$types)){
            if(lmb_rebuildForeignKey(1)){
        	   LimbasLogger::log("function : rebuild db Foreign Keys ", LimbasLogger::LL_INFO);
            }else{
                LimbasLogger::log("function : rebuild db Foreign Keys failed ", LimbasLogger::LL_ERROR);
                return false;
            }
        }
        
        if (in_array('functmpfiles',$types)){
            if(lmb_delete_user_filesave()){
        	    LimbasLogger::log("function : delete temporary user files ", LimbasLogger::LL_INFO);
            }else{
                LimbasLogger::log("function : delete temporary user files failed ", LimbasLogger::LL_ERROR);
                return false;
            }
        }

        if (in_array('functmpfields',$types)){
	       require_once("admin/tools/multiselect_refresh.lib");
	       multiselectRefreshCount();
	       relationRefreshCount();
           LimbasLogger::log("function : rebuild temporary tables values ", LimbasLogger::LL_INFO);
        }

        # overwrite source code TODO doesnt work yet because of memory limit
        $limbasSrcTarGz = $path . 'limbas_src.tar.gz';
        if (file_exists($limbasSrcTarGz)) {
            # resolve admin-symlink in dependent to get location of limbas_src
            $relPathLmbSrc = preg_replace('/admin$/', '', readlink($umgvar['path'] . '/admin'));
            $pathLmb = $umgvar['path'] . '/' . $relPathLmbSrc;
            $pathLmb = rtrim($pathLmb, "/");
            $pathLmb = substr($pathLmb, 0, strrpos($pathLmb, "/") + 1);

            # remove old source
            $lmbSrcDir = $pathLmb . 'limbas_src';
            system("rm -r '$lmbSrcDir'", $returnCode);
            if($returnCode != 0) {
                LimbasLogger::log("could not delete old source code: return code $returnCode!", LimbasLogger::LL_ERROR);
                return false;
            }

            # extract new source
            system("tar xzf '$limbasSrcTarGz' -C '$pathLmb'");
            LimbasLogger::log("overwrote old source code", LimbasLogger::LL_INFO);
        }
        
        # copy extensions
        $extensionsTarGz = $path . 'extensions.tar.gz';
        if (file_exists($extensionsTarGz)) {
            # remove old extensions
            $extensionsDir = $umgvar['path'] . '/EXTENSIONS';
            system("rm -r '$extensionsDir'", $returnCode);
            if($returnCode != 0) {
                LimbasLogger::log("could not delete old EXTENSIONS: return code $returnCode!", LimbasLogger::LL_ERROR);
                return false;
            }

            # extract new extensions
            system("tar xzf '$extensionsTarGz' -C '{$umgvar['path']}'");            
            LimbasLogger::log("overwrote old extensions", LimbasLogger::LL_INFO);
        }
                
        # call extension function
        if($callExtensionFunctionName){
            if(is_dir($umgvar["pfad"]."/EXTENSIONS/")){
                if($extdir = read_dir($umgvar["pfad"]."/EXTENSIONS/",1)){
                    foreach ($extdir["name"] as $key => $value){
                        if ($value == 'ext_import.inc') {
                            require_once($extdir["path"][$key].$value);
                        }
                    }
                }
            }
            
            if (is_callable($callExtensionFunctionName)) {
                # call function by passed name
                if (!$callExtensionFunctionName()) {
                    LimbasLogger::log("Function '$callExtensionFunctionName' returned failure!", LimbasLogger::LL_ERROR);
                    return false;
                } else {
                    LimbasLogger::log("Function '$callExtensionFunctionName' returned success!", LimbasLogger::LL_INFO);
                }
            } else {
                LimbasLogger::log("Function '$callExtensionFunctionName' does not exist!", LimbasLogger::LL_ERROR);
                return false;
            }
        }       
        
        
        ######### OUTPUT #########
        echo "<DIV class=\"lmbPositionContainerMain\">";
        echo "<i class=\"lmb-icon lmb-check\" style=\"font-size:20px\"></i>&nbsp;$lang[987]";
        echo "</DIV>";

        echo "<DIV class=\"lmbPositionContainerMain\">";
        echo '<h3>Log</h3><div style="max-height:400px;overflow:auto;border:1px solid grey;padding:4px;">';
        foreach(LimbasLogger::getLogLines() as $key => $log) {
            echo "<i>{$log['level']}</i>:&nbsp;{$log['message']}<br>";
        }
        echo '</div><br><br>';
        echo "</DIV>";
    }
    return true;
}

# read configuration to array
function parseConfigToArray($file)
{
    require_once('admin/tables/tab.lib');

    $config_datei = fopen($file, "r");
    $output = array();
    $output['table'] = array();

    while ($line = fgets($config_datei, 100000)) {

        if (lmb_substr($line, 0, 1) != "#" AND lmb_substr($line, 0, 1) != " ") {

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
                  $rs5 = odbc_exec($db,$sqlquery);
                  $outdesc1 = "<div>$lang[1026] <FONT COLOR=\"#0033CC\">".$newtabname[$cdf[0]]."</FONT></div>\n<Script language=\"JavaScript\">scrolldown();</SCRIPT>\n";
                  $outdesc2 = "<div style=\"color:red;\">$lang[1026] <FONT COLOR=\"#0033CC\">".$newtabname[$cdf[0]]."</FONT> $lang[1019]</div>\n<Script language=\"JavaScript\">scrolldown();</SCRIPT>\n";
                  if($rs5){
                  if($GLOBALS["action"]){echo $outdesc1."\n";}
                  }else{
                  $GLOBALS["LAST_SQL"]["sql"][] = $sqlquery;
                  $GLOBALS["LAST_SQL"]["desc1"][] = $outdesc1;
                  $GLOBALS["LAST_SQL"]["desc2"][] = $outdesc2;
                  if($GLOBALS["action"]){echo $outdesc2."\n";}
                  }
                 */
            } else {
                $definition = "";
            }
        }
    }
    return $output;
}

function tableOutput1($diff, $description) {
    if($out = printCompareTable1($diff)){
        echo '<h3>' . $description . '</h3><div style="max-height:400px;overflow:auto;border:1px solid grey;padding:4px;">';
        echo $out;
        echo '</div><br><br>';
    }
}
function tableOutput2($diff, $description) {
    if($out = printCompareTable2($diff)){
        echo '<h3>' . $description . '</h3><div style="max-height:400px;overflow:auto;border:1px solid grey;padding:4px;">';
        echo $out;
        echo '</div><br><br>';
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
function compareTableData($path, $pathtmp, $tarFileName, $primaryKeys, $ignoreColumns = array(), $desc = null, $filterFunc = null)
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
    $left = tarToPrimaryKeyIndexedArr($path, $tarFileName, $primaryKeys, $header);
    if ($left === false) {
        LimbasLogger::log("Could not read '$tarFileName' in '$path' into array indexed by " . json_encode($primaryKeys) . "!", LimbasLogger::LL_ERROR);
        return false;
    }
    $right = tarToPrimaryKeyIndexedArr($pathtmp, $tarFileName, $primaryKeys, $_header = array());
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

    #echo json_encode($left);
    #echo json_encode($right);

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
function tarToPrimaryKeyIndexedArr($path, $tarFileName, $primaryKeys, &$header)
{
    # get data from tar as array
    $dataArr = tarToArr($path, $tarFileName, $header);
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
        $primaryKey = getCompositePrimaryKey($primaryKeyIndices, $data);

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
function tarToArr($path, $tarFileName, &$header)
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
function getCompositePrimaryKey($primaryKeyIndices, $dataArr)
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
function groupBy1Key($arr, $prefix)
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
function groupBy2Keys($arr, $prefixKey1, $prefixKey2)
{
    $grouped = array();

    foreach ($arr['diff'] as $key => $value) {
        $keyParts = explode('_', $key);

        $groupedKey = "$prefixKey1 $keyParts[1]";

        if (!array_key_exists($groupedKey, $grouped)) {
            $grouped[$groupedKey] = array();
        }

        $grouped[$groupedKey]["$prefixKey2 $keyParts[2]"] = $value;
    }

    return $grouped;
}

/*
 * Prints a table for comparison of data from functions compareTableData and groupBy2Keys with 2 primary keys, grouped by one of the keys
 */

function printCompareTable2($dataArrArr)
{
    $count = count($dataArrArr);
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
        $out .= printCompareTable1($dataArr);

        # end border div
        $out .= '</div><br>';
    }

    return $out;
}


/*
 * Prints a table for comparison of data from function compareTableData and groupBy1Key
 */
function printCompareTable1($dataArr)
{
    $count = count($dataArr);
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
            $out .= '<table style="margin-left:40px;display:none;">';
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

function stripNonVisibleAttrs($html) {
    $domd = new DOMDocument();
    $domd->loadHTML($html);
    $domx = new DOMXPath($domd);

    # remove max-height and overflow
    $items = $domx->query("//div[@style]");
    foreach($items as $item) {
        $style = $item->getAttribute("style");
        $style = preg_replace('/max-height:.*?;/', '', $style);
        $style = preg_replace('/overflow:.*?;/', '', $style);
        $item->setAttribute("style", $style);
    }

    # remove display
    $items = $domx->query("//table[@style]");
    foreach($items as $item) {
        $style = $item->getAttribute("style");
        $style = preg_replace('/display:.*?;/', '', $style);
        $item->setAttribute("style", $style);
    }

    # remove onclick
    $items = $domx->query("//*[@onclick]");
    foreach($items as $item) {
        $item->removeAttribute("onclick");
    }

    return $domd->saveHTML();
}

function writeDiffIntoFile($diff) {
    global $umgvar;
    global $session;

    $diff = stripNonVisibleAttrs($diff);
    $url = (lmb_substr($umgvar['url'], 0, -1) === '/') ? $umgvar['url'] : ($umgvar['url'] . '/');

    # write into file
    $pathOffset = 'USER/' . $session['user_id'] . '/temp/diff.html';
    $outputFile = $umgvar['path'] . '/' . $pathOffset;
    $downloadFile = $url . $pathOffset;
    if (file_put_contents($outputFile, $diff) !== false AND file_exists($outputFile)) {
        return $downloadFile;
    }
    return false;
}

?>
