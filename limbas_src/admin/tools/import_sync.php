<?php

/*
 * Copyright notice
 * (c) 1998-2016 Limbas GmbH - Axel westhagen (support@limbas.org)
 * All rights reserved
 * This script is part of the LIMBAS project. The LIMBAS project is free software; you can redistribute it and/or modify it on 2 Ways:
 * Under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * Or
 * In a Propritary Software Licence http://limbas.org
 * The GNU General Public License can be found at http://www.gnu.org/copyleft/gpl.html.
 * A copy is found in the textfile GPL.txt and important notices to the license from the author is found in LICENSE.txt distributed with these scripts.
 * This script is distributed WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * This copyright notice MUST APPEAR in all copies of the script!
 * Version 3.0
 */

/*
 * ID:
 */

/*
 * 
 * if $preckeck = true:
 * Daten vorbereiten (Export bestehendes System, hochgeladenen Import-Datei entpacken)
 * Konfigurationen vergleichen und Bericht ausgeben
 * 
 * if $confirm_syncimport = true:
 * Nutzer vorbereitete Daten
 * Führe Import und Anpassungen durch
 *
 */
if (($syncimport AND ! empty($_FILES["filesync"])) OR $confirm_syncimport) {
    //Array for screen output
    $output = array();


    //Pfad für Import-Dateien
    $path = $umgvar["pfad"] . "/USER/" . $session["user_id"] . "/temp/sync/";
    //Normales Temp-Verzeichnis für den Export der Bestandsdaten
    $pathtmp = $umgvar["pfad"] . "/USER/" . $session["user_id"] . "/temp/";

    //Funktionen für den Import einbinden
    if ($confirm_syncimport) {
        require_once("admin/tools/import.dao");
    }



    if ($precheck) {
        //Funktionen für den Export einbinden
        require_once("admin/tools/export.lib");

        # Tempverzeichniss leeren
        rmdirr($pathtmp);
        mkdir($pathtmp);
        mkdir($path);

        # Datei in Tempverzeichnis (Sync) verschieben und import durchführen
        if (!move_uploaded_file($_FILES['filesync']['tmp_name'], $path . 'export.tar.gz')) {
            return;
        }
        # Datei entpacken
        $sys = exec("tar -x -v -C " . $path . " -f " . $path . "export.tar.gz");
        
        # read configuration
        if (file_exists($path . "export.php")) {
            include_once($path . "export.php");
        }
        
        $exptables = array();
        
        # Bestehenden Tabellen für Vergleich exportieren
        if (in_array('tabs', $types)) {
            $odbc_table = dbf_20(array($DBA["DBSCHEMA"], null, "'TABLE'"));
            foreach ($odbc_table["table_name"] as $tkey => $table) {
                //no  systemtables, system_files, ldms_* , dontinclude
                if (lmb_strpos(lmb_strtolower($table), 'lmb_') === false && lmb_strpos(lmb_strtolower($table), 'systemtables') === false && lmb_strpos(lmb_strtolower($table), 'system_files') === false && lmb_strpos(lmb_strtolower($table), 'ldms_') === false) {
                    $exptables[] = $table;
                }
            }
            
            $exptables = array_merge($exptables, array(dbf_4('lmb_trigger')) );
        }
        
        # Forms
        if (in_array('forms', $types)) {
			$exptables = array_merge($exptables, array(dbf_4('lmb_form_list'),dbf_4('lmb_forms')) );
        }
        
        # Reports
        if (in_array('rep', $types)) {
			$exptables = array_merge($exptables, array(dbf_4('lmb_report_list'),dbf_4('lmb_reports')) );
		}
           
        # TODO add more here
        
        lmbExport_ToSystem($exptables, null, null, null, true);
    }


    //Systemtabellen überschreiben
    # Liste aller Limbas Systemtabellen im entpackten Verzeichnis
    if ($folderval = read_dir($path)) {
        foreach ($folderval["name"] as $key => $file) {
            if ($folderval["typ"][$key] == "file" AND lmb_substr($file, 0, 7) != "export.") {
                # Nur zu importierende Tabellen
                $tablename = str_replace(".tar", "", str_replace(".gz", "", $file));
                //$tablename = str_replace(".tar","",$tablename);
                if (lmb_strpos(lmb_strtolower($tablename), "lmb_") !== false) {
                    $tablegrouplist[$tablename] = 1;
                }
            }
        }
    }
    if (!$confirm_syncimport) {
        echo "<h3>Tables that are overwritten</h3>
                <div id=\"rimport_import_scolldown\" style=\"max-height:200px;overflow:auto;border:1px solid grey;padding:4px;\">";
        foreach ($tablegrouplist as $table => $value) {
            echo '<p>' . $table . '</p>';
        }
        echo '</div>';
    }





    $tables = array();
    $delete = array();
    //lese Konfigurationen (neu)
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
    }
    //lese Konfigurationen (alt)
    if ($handle = opendir($pathtmp)) {
    	
        while (false !== ($file = readdir($handle))) {
            if ($file != "." && $file != ".." && lmb_strtolower(lmb_substr($file, -5)) == '.conf') {
                $table = str_replace('.conf', '', $file);
                if (file_exists($path . $table . '.conf')) {
                    $exttableconf[$table] = parseConfigToArray($pathtmp . $file);
                } else {
                    if (lmb_strtolower(lmb_substr($table, 0, 5)) == 'verk_') {
                        $delete[$table] = 1;
                    } else {
                        $delete[$table] = 2;
                    }

                    $output['deletetab'][] = '<p style="color:#f00">delete table ' . $table . '</p>';
                }
            }
        }
        closedir($handle);
    }

    unset($table);
    
    
    foreach ($tables as $table => $value) {
        if (!array_key_exists($table, $exttableconf)) {
            $output['createtab'][] = '<p>create table ' . $table . '</p>';
            if ($confirm_syncimport) {
                import(false, 'over', null, null, null, null, $table);
            }
        } else {


            $cmp = array();
            foreach ($imptableconf[$table]['table'] as $field => $value) {
                //Prüfen ob Feld bereits existiert
                if (array_key_exists($field, $exttableconf[$table]['table'])) {
                    $cmptmp = array_diff($imptableconf[$table]['table'][$field], $exttableconf[$table]['table'][$field]);
                    if (count($cmptmp) > 0) {
                        $cmp[$field] = $cmptmp;

                        //Prüfen, ob sich der Feldtyp geändert hat (sowohl type als auch scale)
                        if ($exttableconf[$table]['table'][$field]['datatype'] != 'TEXT' && $exttableconf[$table]['table'][$field]['datatype'] != 'LONG' && ($exttableconf[$table]['table'][$field]['datatype'] != $imptableconf[$table]['table'][$field]['datatype'] || $exttableconf[$table]['table'][$field]['fieldlength'] != $imptableconf[$table]['table'][$field]['fieldlength'] || $exttableconf[$table]['table'][$field]['scale'] != $imptableconf[$table]['table'][$field]['scale'])) {
                            $output[$table]['changefield'][] = '<p>change type of ' . $field . ' [' . $exttableconf[$table]['table'][$field]['datatype'] . '(' . $exttableconf[$table]['table'][$field]['fieldlength'] . ',' . $exttableconf[$table]['table'][$field]['scale'] . ')' . ' -> ' . $imptableconf[$table]['table'][$field]['datatype'] . '(' . $imptableconf[$table]['table'][$field]['fieldlength'] . ',' . $imptableconf[$table]['table'][$field]['scale'] . ')]</p>';

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

                                $pt = get_db_field_type(lmb_strtoupper($imptableconf[$table]['table'][$field]['datatype']));
                                if (!convert_type($pt, $DBA["DBSCHEMA"], $table, $field, $datentyp)) {
                                    echo '<p style="color:#f00">field <b>' . $field . '</b> in table <b>' . $table . '</b> could not be converted to ' . $datentyp . '</p>';
                                }


                                # try db-based modify
                                //$sqlquery = dbq_15(array($DBA["DBSCHEMA"],$table,$field,$datentyp));
                                /* $rs = @odbc_exec($db,$sqlquery);
                                  # manual modify
                                  if(!$rs) {
                                  echo '<p>field '.$field.' in table '.$table.' could not be converted to '.$datentyp.'</p>';
                                  error_log($sqlquery);
                                  } */
                            }
                        }

                        //Prüfen, ob sich Default-Wert geändert hat
                        elseif ($exttableconf[$table]['table'][$field]['datatype'] != 'TEXT' && $exttableconf[$table]['table'][$field]['datatype'] != 'LONG') {
                            $output[$table]['changefield'][] = '<p>change default of ' . $field . ' [' . $exttableconf[$table]['table'][$field]['default'] . ' -> ' . $imptableconf[$table]['table'][$field]['default'] . ']</p>';

                            if ($confirm_syncimport) {
                                /* --- Defaultwert setzen --------------------------------------------- */
                                $parsetype = get_db_field_type(lmb_strtoupper($exttableconf[$table]['table'][$field]['datatype']));
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

                                if (!$def AND ! $def0) {
                                    $def = LMB_DBDEF_NULL;
                                }

                                if ($sqlquery = dbq_9(array($DBA["DBSCHEMA"], $table, $field, $def))) {
                                    $rs1 = odbc_exec($db, $sqlquery) or errorhandle(odbc_errormsg($db), $sqlquery, $action, __FILE__, __LINE__);
                                }
                            }
                        }
                    }
                }
                //Falls Feld nicht vorhanden, erstelle es
                else {
                    $output[$table]['createfield'][] = '<p style="color:#050">create field ' . $field . '</p>'; // in table '.$table.'</p>';
                    //add_field($field,$gtabid,$typ,$typ2,$typ_size,$ifield_id,$description,$spellingf,$default,$sort,$add_permission,$inherit_tab,$inherit_field,$import_typ='default',$extension=null){

                    if ($confirm_syncimport) {
                        /* --- Neues Feld anlegen --------------------------------------------- */
                        $parsetype = get_db_field_type(lmb_strtoupper($imptableconf[$table]['table'][$field]['datatype']));
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

                        if (!$def AND ! $def0) {
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
                            error_log($sqlquery);
                        }
                    }
                }
            }

            //Feld löschen
            foreach ($exttableconf[$table]['table'] as $field => $value) {
                if (!array_key_exists($field, $imptableconf[$table]['table'])) {
                    $output[$table]['deletefield'][] = '<p style="color:#f00">delete field ' . $field . '</p>'; // in table '.$table.'</p>';

                    if ($confirm_syncimport) {
                        if ($sqlquery = dbq_22(array($table, $field))) {
                            $rs1 = odbc_exec($db, $sqlquery) or errorhandle(odbc_errormsg($db), $sqlquery, $action, __FILE__, __LINE__);
                        }
                    }
                }
            }


            //break;
        }
    }

    # fields to ignore in diff
    $defaultIgnore = array(dbf_4('erstdatum'), dbf_4('editdatum'), dbf_4('erstuser'), dbf_4('edituser'));
    
    # function to get information about the type of form element that was changed/added/removed
    $getInhalt = function($type, $leftData, $rightData, $key, $columnIds) {
        # always take right data if existent
        $data = ($type == 'removed') ? $leftData : $rightData;

        # return column inhalt into desc field
        return $data[$key][$columnIds[dbf_4('inhalt')]];
    };
    
    # compare trigger
    if (in_array('tabs', $types)) {
        # filter: take only non-system values
        $filterFunc = function($key, $value, $columnIds) {
            $internId = $columnIds[dbf_4('intern')];
            return $value[$internId] != 0;            
        };
        
        # get trigger diff
        $triggerDiff = compareTableData(dbf_4('lmb_trigger').'.tar.gz', array(dbf_4('id')), $defaultIgnore, null, $filterFunc);
        $triggerDiff = groupBy1Key($triggerDiff, 'trigger');
    }
        
    # compare forms
    if (in_array('forms', $types)) {        
        # get forms diff
        $lmbFormsDiff = compareTableData(dbf_4('lmb_forms').'.tar.gz', array(dbf_4('form_id'), dbf_4('keyid')), array_merge($defaultIgnore, array(dbf_4('id'), dbf_4('z_index'))), $getInhalt);
        $lmbFormsDiff = groupBy2Keys($lmbFormsDiff, 'form', 'ID');
        ksort($lmbFormsDiff);
        
        # get form list diff
        $lmbFormListDiff = compareTableData(dbf_4('lmb_form_list').'.tar.gz', array(dbf_4('id')), array_merge($defaultIgnore, array(dbf_4('id'), dbf_4('z_index'))));
        $lmbFormListDiff = groupBy1Key($lmbFormListDiff, 'form');
    }
    
    # compare reports
    if (in_array('rep', $types)) {
        # get reports diff
        $lmbReportsDiff = compareTableData(dbf_4('lmb_reports').'.tar.gz', array(dbf_4('bericht_id'), dbf_4('el_id')), array_merge($defaultIgnore, array(dbf_4('id'), dbf_4('z_index'))), $getInhalt);
        $lmbReportsDiff = groupBy2Keys($lmbReportsDiff, 'report', 'ID');
        ksort($lmbReportsDiff);      
        
        # get report list diff
        $lmbReportListDiff = compareTableData(dbf_4('lmb_report_list').'.tar.gz', array(dbf_4('id')), array_merge($defaultIgnore, array(dbf_4('id'), dbf_4('z_index'))));
        $lmbReportListDiff = groupBy1Key($lmbReportListDiff, 'report');
    }    

    //Adminrechte abgleichen
    if ($confirm_syncimport) {
        echo '<p>refresh table and field rules</p>';
        $check_all = true;
        require_once("admin/group/group.lib");
        require_once("admin/tools/grusrref.php");

        //Versuche alle Views zu speichern / überschreiben
        echo '<h3>Try to save views</h3>
                                <div style="height:200px;overflow:auto;border:1px solid grey;padding:4px;">';


        //Alle Views sammeln
        $sqlquery1 = 'SELECT tabelle, viewdef FROM lmb_conf_views INNER JOIN lmb_conf_tables ON (lmb_conf_views.ID = lmb_conf_tables.tab_id)';
        $result = odbc_exec($db, $sqlquery1) or errorhandle(odbc_errormsg($db), $sqlquery1, $action, __FILE__, __LINE__);
        while ($view = odbc_fetch_array($result)) {
            //$views[] = $view;
            $sqlquery2 = dbq_19(array($view['tabelle'], $view['viewdef']));
            $rs = odbc_exec($db, $sqlquery2) or errorhandle(odbc_errormsg($db), $sqlquery2, $action, __FILE__, __LINE__);
            if (!$rs) {
                echo '<p style="color:#f00">view ' . $view['tabelle'] . ' NOT saved';
            } else {
                echo '<p>view ' . $view['tabelle'] . ' saved';
            }
        }
        echo '</div><br><br>';
    }
    
    //Bericht ausgeben
    if ($precheck) {
        //Zu löschende Tabellen
        echo '<h3 style="color:#f00">delete tables</h3>
                                <div style="max-height:200px;overflow:auto;border:1px solid grey;padding:4px;">';
        if (is_array($output['deletetab'])) {
            foreach ($output['deletetab'] as $value) {
                echo $value;
            }
        }
        echo '</div><br><br>';

        //Neue Tabellen
        echo '<h3>create tables</h3>
                                <div style="max-height:200px;overflow:auto;border:1px solid grey;padding:4px;">';
        if (is_array($output['createtab'])) {
            foreach ($output['createtab'] as $value) {
                echo $value;
            }
        }
        echo '</div><br><br>';


        //Feldänderungen
        echo '<h3>fieldchanges per table</h3>
                                <div style="max-height:400px;overflow:auto;border:1px solid grey;padding:4px;">';
        foreach ($output as $table => $value) {
            if ($table != 'createtab' && $table != 'deletetab') {
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
        echo '</div><br><br>';
        
        # trigger diff
        echo '<h3>change triggers</h3>
            <div style="max-height:400px;overflow:auto;border:1px solid grey;padding:4px;">';
        printCompareTable1($triggerDiff);
        echo '</div><br><br>';        
        
        # Show form list diff        
        echo '<h3>form list changes</h3>
            <div style="max-height:400px;overflow:auto;border:1px solid grey;padding:4px;">';
        printCompareTable1($lmbFormListDiff);
        echo '</div><br><br>';
        
        # Show form diff
        echo '<h3>form changes</h3>
            <div style="max-height:400px;overflow:auto;border:1px solid grey;padding:4px;">';
        printCompareTable2($lmbFormsDiff);
        echo '</div><br><br>';
        
        # Show report list diff        
        echo '<h3>report list changes</h3>
            <div style="max-height:400px;overflow:auto;border:1px solid grey;padding:4px;">';
        printCompareTable1($lmbReportListDiff);
        echo '</div><br><br>';
        
        # Show reports diff
        echo '<h3>report changes</h3>
            <div style="max-height:400px;overflow:auto;border:1px solid grey;padding:4px;">';
        printCompareTable2($lmbReportsDiff);
        echo '</div><br><br>';
        
        echo '<input type="button" value="' . $lang[1005] . '" onclick="document.form1.confirm_syncimport.value=1;document.form1.submit();">';
    }

    if ($confirm_syncimport) {
        //alle lmb_ überschreiben
        foreach ($tablegrouplist as $table => $value) {
            $sys = exec('tar -x -C ' . $path . ' -f ' . $path . $table . '.tar.gz');
            deleteExistingTab($table);
            import(false, 'over', null, null, null, null, 'export');
        }


        //Tabellen löschen
        asort($delete);

        foreach ($delete as $table => $value) {
            deleteExistingTab($table);
        }

        echo 'Sync DONE';
    }
}

//Konfiguration in Array einlesen
function parseConfigToArray($file) {
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

function convert_type($parsetype, $dbschema, $table, $field, $datentyp) {
    global $db;
    global $result_type;
    global $action;
    global $lang;

    # try db-based modify
    $sqlquery = dbq_15(array($dbschema, $table, $field, $datentyp));
    $rs = @odbc_exec($db, $sqlquery);
    # manual modify
    if (!$rs) {
        if ($parsetype == 1 OR $parsetype == 6) {
            $conversion = "NUM";
        } else {
            $conversion = "CHAR";
        }

        //delete LMB_TEMP_CONVERT
        $sqlquery = dbq_22(array($table, 'LMB_TEMP_CONVERT'));
        $rs = odbc_exec($db, $sqlquery) or errorhandle(odbc_errormsg($db), $sqlquery, $action, __FILE__, __LINE__);

        # copy
        $sqlquery = "ALTER TABLE $table " . LMB_DBFUNC_ADD_COLUMN_FIRST . " LMB_TEMP_CONVERT " . $datentyp;
        $rs = @odbc_exec($db, $sqlquery) or errorhandle(odbc_errormsg($db), $sqlquery, $action, __FILE__, __LINE__);
        if (!$rs) {
            lmb_alert("unable to add temporay field!");
            return false;
        }
        # db-based update
        $sqlquery = "UPDATE $table SET LMB_TEMP_CONVERT = $conversion(" . $field . ")";
        $rs = @odbc_exec($db, $sqlquery);

        # Einträge in neues Tabellenfeld
        if (!$rs AND ( $result_type["data_type"][$convert] != 18 AND $result_type["data_type"][$convert] != 31 AND $result_type["data_type"][$convert] != 32 OR $raw)) {
            $sqlquery = "SELECT ID," . $field . " FROM $table";
            $rs = odbc_exec($db, $sqlquery) or errorhandle(odbc_errormsg($db), $sqlquery, $action, __FILE__, __LINE__);
            $bzm = 1;
            while (odbc_fetch_row($rs, $bzm)) {
                # numeric
                if ($parsetype == 1 OR $parsetype == 6) {
                    $value = parse_db_int(odbc_result($rs, $field), $result_type["size"][$convert]);
                    # string
                } else {
                    $value = parse_db_string(odbc_result($rs, $field), $result_type["size"][$convert]);
                }
                $prepare_string = "UPDATE $table SET LMB_TEMP_CONVERT = ? WHERE ID = " . odbc_result($rs, "ID");
                lmb_PrepareSQL($prepare_string, array(parse_db_blob($value)), __FILE__, __LINE__);

                $bzm++;
            }
            $GLOBALS["alert"] = $lang[2022];
        }

        if (!$commit) {
            # drop origin
            $sqlquery = dbq_22(array($table, $field));
            $rs = odbc_exec($db, $sqlquery) or errorhandle(odbc_errormsg($db), $sqlquery, $action, __FILE__, __LINE__);
            if (!$rs) {
                $commit = 1;
            }
            # rename copy
            $sqlquery = dbq_7(array($DBA["DBSCHEMA"], $table, "LMB_TEMP_CONVERT", $field));
            $rs = odbc_exec($db, $sqlquery) or errorhandle(odbc_errormsg($db), $sqlquery, $action, __FILE__, __LINE__);
            if (!$rs) {
                $commit = 1;
            }
        }
        //delete LMB_TEMP_CONVERT
        $sqlquery = dbq_22(array($table, 'LMB_TEMP_CONVERT'));
        $rs = odbc_exec($db, $sqlquery) or errorhandle(odbc_errormsg($db), $sqlquery, $action, __FILE__, __LINE__);
    }

    if ($commit) {
        return false;
    } else {
        return true;
    }
}

function get_db_field_type($type) {
    $parsetypes = array('FIXED' => 1, 'INTEGER' => 1, 'SMALLINT' => 1, 'VARCHAR' => 2, 'LONG' => 2, 'BOOLEAN' => 3, 'DATE' => 4, 'TIMESTAMP' => 4, 'TIME' => 4, 'FLOAT' => 6, 'NUMERIC' => 6);
    return $parsetypes[$type];
}


/*
 * Compares two files and returns diff
 * 
 * tarFileName: filename of both files to compare
 * primaryKeys: array of columnNames that will be used to determine if a dataset of one table is also in the other
 * ignoreColumns: array of columnNames that will be ignored and not compared
 * descFunc: user function (type, leftData, rightData, key, columnIds)=>String to add someDesc into the output array
 * filterFunc: user function(key, value, columnIds)=>Boolean to filter left/right arrays
 * returns: [
 *   'diff': array of [primaryKey => values, 'type' => added/removed, 'desc' => someDesc]   
 *        or array of [primaryKey => [columnKey => ['name' => columnName, 'left' => value, 'right' => value], 'type' => changed, 'desc' => someDesc]]
 *   'columnIds': array of [columnName => columnId]
 * ]
 */
function compareTableData($tarFileName, $primaryKeys, $ignoreColumns=array(), $descFunc=null, $filterFunc=null) {
    global $path;
    global $pathtmp;
    
    if($descFunc == null) {
        $descFunc = function() {};
    }

    # collect table data
    $left = tarToPrimaryKeyIndexedArr($path, $tarFileName, $primaryKeys, $header);
    $right = tarToPrimaryKeyIndexedArr($pathtmp, $tarFileName, $primaryKeys);
    
    $columnIds = array_flip($header);
    $result = array(
        'columnIds' => $columnIds,
        'diff' => array()
    );
    
    # filter
    if($filterFunc != null) {
        foreach($left as $leftKey => $leftData) {
            if(!$filterFunc($leftKey, $leftData, $columnIds)) {
                unset($left[$leftKey]);
            }
        }
        foreach($right as $rightKey => $rightData) {
            if(!$filterFunc($rightKey, $rightData, $columnIds)) {
                unset($right[$rightKey]);
            }
        }
    }
    
    #echo json_encode($left);
    #echo json_encode($right);
    
    # find entries only in left
    foreach($left as $leftKey => $leftData) {
        # remove all ignored columns
        foreach($ignoreColumns as $someKey => $columnName) {
            $columnId = $columnIds[$columnName];
            unset($left[$leftKey][$columnId]);
        }

        # remove if only in left
        if(!array_key_exists($leftKey, $right)) {
            $result['diff'][$leftKey] = $left[$leftKey];
            $result['diff'][$leftKey]['type'] = 'removed';
            $result['diff'][$leftKey]['desc'] = $descFunc('removed', $left, null, $leftKey, $columnIds);
            unset($left[$leftKey]);
        }
    }

    # find entries only in right
    foreach($right as $rightKey => $rightData) {
        # remove all ignored columns
        foreach($ignoreColumns as $someKey => $columnName) {
            $columnId = $columnIds[$columnName];
            unset($right[$rightKey][$columnId]);
        }

        # remove if only in right
        if(!array_key_exists($rightKey, $left)) {
            $result['diff'][$rightKey] = $right[$rightKey];
            $result['diff'][$rightKey]['type'] = 'added';
            $result['diff'][$rightKey]['desc'] = $descFunc('added', null, $right, $rightKey, $columnIds);
            unset($right[$rightKey]);
        }
    }

    # compare entries in both
    foreach($left as $key => $leftData) {
        $rightData = $right[$key];

        foreach($leftData as $valueKey => $valueLeft) {
            $valueRight = $rightData[$valueKey];
            if($valueRight != $valueLeft) {
                if(!array_key_exists($key, $result['diff'])) {
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
 */
function tarToPrimaryKeyIndexedArr($path, $tarFileName, $primaryKeys, &$header) {
    $dataArr = tarToArr($path, $tarFileName, $header);        
    $primaryKeyIndices = getPrimaryKeyIndices($primaryKeys, $header);

    $indexedDataArr = array();

    foreach($dataArr as $someKey => $data) {
        $primaryKey = getCompositePrimaryKey($primaryKeyIndices, $data);

        # key already exists -> primary key must be invalid as it must be unique
        if(array_key_exists($primaryKey, $indexedDataArr)) {
            error_log("Primary key $primaryKey (" . json_encode($primaryKeyIndices) . ") is invalid, entry already exists! In file '$tarFileName'");
        }

        $indexedDataArr[$primaryKey] = $data;
    }
    
    return $indexedDataArr;
}

/*
 * Opens a tar->csv file and creates array from it
 */
function tarToArr($path, $tarFileName, &$header) {
    $tarPath = $path . $tarFileName;
    $extractDir = $path . 'extract_' . $tarFileName;
    $extractFile = $extractDir . '/export.dat';

    # check if tar to extract is missing
    if(!file_exists($tarPath)) {
        error_log("File to extract '$tarPath' does not exist!");
        return;
    }
    
    # extract lmb_forms archive
    exec("mkdir '$extractDir'");
    exec("tar -x -C '$extractDir' -f '$tarPath'");

    # check if file to read from is missing
    if(!file_exists($extractFile)) {
        error_log("Extracted file '$extractFile' does not exist!");
        return;
    }
    
    # load csv data file into php array indexed by primary keys of table
    $file = fopen($extractFile,"r");

    $header = lmb_fgetcsv($file);

    $dataArr = array();
    while($line = lmb_fgetcsv($file)) {
        $dataArr[] = $line;
    }
    
    fclose($file);

    return $dataArr;
}

/*
 * Converts primary key names to indices
 */
function getPrimaryKeyIndices($primaryKeys, $header) {
    $indices = array();
    $fieldNames = array_flip($header);

    foreach($primaryKeys as $someKey => $keyName) {
        if(array_key_exists($keyName, $fieldNames)) {
            $indices[] = $fieldNames[$keyName];
        } else {
            error_log("Key '$keyName' not found in table! (" . json_encode($fieldNames) . ")");
        }
    }

    return $indices;
}

/*
 * Combines primary key indices to get a unique string
 */
function getCompositePrimaryKey($primaryKeyIndices, $dataArr) {
    $primaryKey = "key";
    foreach($primaryKeyIndices as $someKey => $keyIndex) {
        $primaryKey .= '_' . $dataArr[$keyIndex];
    }
    return $primaryKey;
}

# function to replace key from diff with readable key and to group by it
function groupBy1Key($arr, $prefix) {
    $grouped = array();

    foreach($arr['diff'] as $key => $value) {
        $keyParts = explode('_', $key);
        $groupedKey = "$prefix $keyParts[1]";
        $grouped[$groupedKey] = $value;
    }

    return $grouped;
}

# function to replace key from diff with readable keys and to group by it
function groupBy2Keys($arr, $prefixKey1, $prefixKey2) {
    $grouped = array();

    foreach($arr['diff'] as $key => $value) {
        $keyParts = explode('_', $key);

        $groupedKey = "$prefixKey1 $keyParts[1]";

        if(!array_key_exists($groupedKey, $grouped)) {
            $grouped[$groupedKey] = array();
        }

        $grouped[$groupedKey]["$prefixKey2 $keyParts[2]"] = $value;
    }

    return $grouped;
}

/*
 * Prints a table for comparison of data from functions compareTableData and groupBy2Keys with 2 primary keys, grouped by one of the keys
 * @deprecated use printCompareTable2
 */
function dep_printCompareTable2($dataArr) {
    echo '<table class="tabfringe"><tbody>';
    
    # foreach form
    foreach ($dataArr as $key => $data) {
        # <key>
        echo "<tr class=\"tabSubHeader\">
            <td class=\"tabSubHeaderItem\" colspan=\"5\">$key</td>
        </tr>";
        
        # <sub-table diff>
        echo '<tr><td>&nbsp;&nbsp;</td><td>';
        printCompareTable1($data, false);
        echo '</td></tr>';        
    }
    
    echo '</tbody></table>';
}

/*
 * Prints a table for comparison of data from function compareTableData and groupBy1Key
 * @deprecated use printCompareTable1
 */
function dep_printCompareTable1($dataArr, $printHeader=true) {
    echo '<table class="tabfringe"><tbody>';
    
    if ($printHeader) {
        echo '<tr class="tabHeader">
            <td class="tabHeaderItem"></td>
            <td class="tabHeaderItem">Column</td>
            <td class="tabHeaderItem">Left</td>
            <td class="tabHeaderItem">Right</td>
        </tr>';
    }    
    
    foreach ($dataArr as $key => $data) {
        # add user-desc
        $key = $data['desc'] ? "$key ({$data['desc']})" : "$key";
        
        # added
        if ($data['type'] == 'added') {
            # + <key>
            echo "<tr class=\"tabSubHeader\">
                <td style=\"background-color:white;\">+</td>
                <td class=\"tabSubHeaderItem\" colspan=\"3\">$key</td>
            </tr>";   
        }

        # removed
        if ($data['type'] == 'removed') {
            # - <key>
            echo "<tr class=\"tabSubHeader\">
                <td style=\"background-color:white;\">-</td>
                <td class=\"tabSubHeaderItem\" colspan=\"3\">$key</td>
            </tr>";   
        }

        # changed            
        if($data['type'] == 'changed') {
            # * <key>
            echo "<tr class=\"tabSubHeader\" style=\"cursor:pointer;\" onclick=\"$(this).parent().children('.tabBody').toggle('fast');\">
                <td style=\"background-color:white;\">*</td>
                <td class=\"tabSubHeaderItem\" colspan=\"3\">$key</td>
            </tr>";

            # print diff
            foreach ($data as $columnKey => $columnData) {
                # dont render non-diff information
                if($columnKey == 'type' || $columnKey == 'desc') continue;

                # cap to 80 chars
                if(strlen($columnData['left']) > 80) $columnData['left'] = substr($columnData['left'], 0, 80) . '...';
                if(strlen($columnData['right']) > 80) $columnData['right'] = substr($columnData['right'], 0, 80) . '...';

                # diff: <name> <left> <right>
                echo "<tr class=\"tabBody\" style=\"display:none;\">
                    <td>&nbsp;&nbsp;</td>
                    <td><i>{$columnData['name']}</i></td>
                    <td>{$columnData['left']}</td>
                    <td>{$columnData['right']}</td>
                </tr>";
            }
        }
    }

    echo '</tbody></table>';
}

/*
 * Prints a table for comparison of data from functions compareTableData and groupBy2Keys with 2 primary keys, grouped by one of the keys
 */
function printCompareTable2($dataArrArr) {
    $red = '#f00';
    $green = '#050';
    $yellow = '#c19620';
    
    foreach($dataArrArr as $topKey => $dataArr) {
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
        echo '<span title="expand all children" onclick="'.$onclick.'" style="cursor:pointer;"><i class="lmb-icon lmb-angle-double-down"></i><strong>' . $topKey . '</strong></span>';
        echo '<div style="border:1px solid grey;padding:4px;">';
        
        # render diff
        printCompareTable1($dataArr);
        
        # end border div
        echo '</div><br>';
    }
}

/*
 * Prints a table for comparison of data from function compareTableData and groupBy1Key
 */
function printCompareTable1($dataArr) {
    $red = '#f00';
    $green = '#050';
    $yellow = '#c19620';
    
    foreach($dataArr as $subKey => $data) {            
        # add user-desc, cap to 80 chars but show full text on hover
        $data['desc'] = htmlspecialchars($data['desc']);
        if(strlen($data['desc']) > 80) {
            $data['desc'] = '<span title="'.$data['desc'].'">' . substr($data['desc'], 0, 80) . '...</span>';                
        }
        $key = $data['desc'] ? "$subKey (<i>{$data['desc']}</i>)" : "$subKey";

        # added
        if ($data['type'] == 'added') {
            echo "<p><i class=\"lmb-icon\"></i><span style=\"color:$red;\">delete $key</span></p>";   
        }

        # removed
        if ($data['type'] == 'removed') {
            echo "<p><i class=\"lmb-icon\"></i><span style=\"color:$green;\">create $key</span></p>";   
        }

        # changed            
        if($data['type'] == 'changed') {
            # * <key>
            $onclick = "
                    $(this).next().next().toggle('fade');
                    if($(this).children().first().hasClass('lmb-angle-down')) {
                        $(this).children().first().removeClass('lmb-angle-down').addClass('lmb-angle-up');
                    } else {
                        $(this).children().first().removeClass('lmb-angle-up').addClass('lmb-angle-down');
                    }";
            echo "<p><span onclick=\"$onclick\" style=\"cursor:pointer;\"><i class=\"lmb-icon lmb-angle-down\"></i><span style=\"color:$yellow;\">change $key</span></span><br>";

            # print diff
            echo '<table style="margin-left:40px;display:none;">';
            foreach ($data as $columnKey => $columnData) {
                # dont render non-diff information
                if($columnKey == 'type' || $columnKey == 'desc') { continue; }

                # cap to 80 chars
                # if(strlen($columnData['left']) > 80) { $columnData['left'] = substr($columnData['left'], 0, 80) . '...'; }
                # if(strlen($columnData['right']) > 80) { $columnData['right'] = substr($columnData['right'], 0, 80) . '...'; }

                # diff: <name> <left> <right>
                echo "<tr>
                    <td style=\"padding-right:10px; vertical-align:top;\"><i>{$columnData['name']}:</i></td>
                    <td>" . textDiff($columnData['left'], $columnData['right']) . "</td>
                    </tr>";
            }
            echo '</table></p>';
        }        
    }
}

?>