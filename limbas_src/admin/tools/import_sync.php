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
if(($syncimport AND !empty($_FILES["fileproject"])) OR $confirm_syncimport){
	//Array for screen output
        $output = array();
        
	
        //Pfad für Import-Dateien
	$path = $umgvar["pfad"]."/USER/".$session["user_id"]."/temp/sync/";
        //Normales Temp-Verzeichnis für den Export der Bestandsdaten
	$pathtmp = $umgvar["pfad"]."/USER/".$session["user_id"]."/temp/";
        
        //Funktionen für den Import einbinden
        if ($confirm_syncimport)
        {
            require_once("admin/tools/import.dao");
        }
        
        
       
        if($precheck){
            
            //Funktionen für den Export einbinden
            require_once("admin/tools/export.lib");

            # read configuration
            if(file_exists($path."export.php")){
                    include_once($path."export.php");
            }
            
            # Tempverzeichniss leeren
            rmdirr($pathtmp);
            mkdir($pathtmp);
            mkdir($path);

            # Datei in Tempverzeichnis (Sync) verschieben und import durchführen
            if(!move_uploaded_file($_FILES['fileproject']['tmp_name'], $path.'export.tar.gz')){return;}
            # Datei entpacken
            $sys = exec("tar -x -C ".$path." -f ".$path."export.tar.gz");


            //Bestehenden Tabellen für Vergleich exportieren
            $odbc_table = dbf_20(array($DBA["DBSCHEMA"],null,"'TABLE'"));
            foreach($odbc_table["table_name"] as $tkey => $table) {
                    //no  systemtables, system_files, ldms_* , dontinclude
                    if (strpos(strtolower($table),'lmb_') === false && strpos(strtolower($table),'systemtables') === false && strpos(strtolower($table),'system_files') === false && strpos(strtolower($table),'ldms_') === false)
                    {
                            $exptables[] = $table;
                    }
            }

            lmbExport_ToSystem($exptables,null,null,null,true);
        }
        
        
        //Systemtabellen überschreiben
        # Liste aller Limbas Systemtabellen im entpackten Verzeichnis
        if($folderval = read_dir($path)){
                foreach($folderval["name"] as $key => $file){
                        if($folderval["typ"][$key] == "file" AND substr($file,0,7) != "export."){
                                # Nur zu importierende Tabellen
                                $tablename = str_replace(".tar","",str_replace(".gz","",$file));
                                //$tablename = str_replace(".tar","",$tablename);
                                if (strpos(strtolower($tablename),"lmb_") !== false)
                                {
                                        $tablegrouplist[$tablename] = 1;
                                }
                        }
                }
        }
                if (!$confirm_syncimport)
                {
                    echo "<h3>Tables that are overwritten</h3>
                <div id=\"rimport_import_scolldown\" style=\"height:200px;overflow:auto;border:1px solid grey;padding:4px;\">";
                    foreach ($tablegrouplist as $table => $value)
                    {
                        echo '<p>'.$table.'</p>';
                    }
                     echo '</div>';
                }
       
            
            
            
                
                $tables = array();
                $delete = array();
                //lese Konfigurationen (neu)
                if ($handle = opendir($path)) {
                    while (false !== ($file = readdir($handle)))
                    {
                        if ($file != "." && $file != ".." && strtolower(substr($file, -5)) == '.conf')
                        {
                            
                            $table = str_replace('.conf','',strtolower($file));
                            if ($table != 'export')
                            {
                                $tables[$table] = 1;
                                $imptableconf[$table] = parseConfigToArray($path.$file);
                            }
                        }
                    }
                    closedir($handle);
                }
                //lese Konfigurationen (alt)
                if ($handle = opendir($pathtmp)) {
                    while (false !== ($file = readdir($handle)))
                    {
                        if ($file != "." && $file != ".." && strtolower(substr($file, -5)) == '.conf')
                        {
                            
                            $table = str_replace('.conf','',strtolower($file));
                            if (file_exists($path.$table.'.conf'))
                            {
                                $exttableconf[$table] =  parseConfigToArray($pathtmp.$file);
                            }
                            else
                            {
                                if (strtolower(substr($table,0,5)) == 'verk_')
                                {
                                    $delete[$table] = 1;
                                }
                                else
                                {
                                    $delete[$table] = 2;
                                }
                                
                                $output['deletetab'][] = '<p style="color:#f00">delete table '.$table.'</p>';
                }
                            
                        }
                    }
                    closedir($handle);
                }
                
                unset($table);
                
                foreach ($tables as $table => $value)
                {
                    if (!array_key_exists($table, $exttableconf)) 
                    {
                        $output['createtab'][] = '<p>create table '.$table.'</p>';
                        if ($confirm_syncimport) {
                            import(false,'over',null,null, null,null,$table);
                        }
                    }
                    else
                    {                        
                        
                                           
                        $cmp = array();
                        foreach ($imptableconf[$table]['table'] as $field => $value)
                        {
                            //Prüfen ob Feld bereits existiert
                            if (array_key_exists($field, $exttableconf[$table]['table']))
                            {
                                $cmptmp = array_diff($imptableconf[$table]['table'][$field], $exttableconf[$table]['table'][$field]);
                                if (count($cmptmp) > 0)
                                {
                                    $cmp[$field] = $cmptmp;
                                    
                                    //Prüfen, ob sich der Feldtyp geändert hat (sowohl type als auch scale)
                                    if ($exttableconf[$table]['table'][$field]['datatype'] != 'TEXT' && $exttableconf[$table]['table'][$field]['datatype'] != 'LONG' && ($exttableconf[$table]['table'][$field]['datatype'] != $imptableconf[$table]['table'][$field]['datatype'] || $exttableconf[$table]['table'][$field]['fieldlength'] != $imptableconf[$table]['table'][$field]['fieldlength'] || $exttableconf[$table]['table'][$field]['scale'] != $imptableconf[$table]['table'][$field]['scale']))
                                    {
                                        $output[$table]['changefield'][] = '<p>change type of '.$field.' ['.$exttableconf[$table]['table'][$field]['datatype'].'('.$exttableconf[$table]['table'][$field]['fieldlength'].','.$exttableconf[$table]['table'][$field]['scale'].')'.' -> '.$imptableconf[$table]['table'][$field]['datatype'].'('.$imptableconf[$table]['table'][$field]['fieldlength'].','.$imptableconf[$table]['table'][$field]['scale'].')]</p>';
                                        
                                        if ($confirm_syncimport) {
                                            
                                            $newsize = ($imptableconf[$table]['table'][$field]['scale'] == 0) ? $imptableconf[$table]['table'][$field]['fieldlength'] : $imptableconf[$table]['table'][$field]['fieldlength'].','.$imptableconf[$table]['table'][$field]['scale'];
                                            $ct = parse_db_type($imptableconf[$table]['table'][$field]['datatype'],$newsize,$imptableconf[$table]['table'][$field]['fieldlength']);
                                            $datentyp = $ct[0];
                                            if (strpos($datentyp,'BOOL') !== false) { $datentyp = 'BOOLEAN'; }
                                            if (strpos($datentyp,'TEXT') !== false) { $datentyp = 'TEXT'; }
                                            
                                            $pt = get_db_field_type(strtoupper($imptableconf[$table]['table'][$field]['datatype']));
                                            if(!convert_type($pt, $DBA["DBSCHEMA"],$table,$field,$datentyp)) {
                                                echo '<p style="color:#f00">field <b>'.$field.'</b> in table <b>'.$table.'</b> could not be converted to '.$datentyp.'</p>';
                                            }
                                            
                                            
                                            # try db-based modify
                                            //$sqlquery = dbq_15(array($DBA["DBSCHEMA"],$table,$field,$datentyp));
                                            /*$rs = @odbc_exec($db,$sqlquery);
                                            # manual modify
                                            if(!$rs) {
                                                echo '<p>field '.$field.' in table '.$table.' could not be converted to '.$datentyp.'</p>';
                                                error_log($sqlquery);
                                            }*/
                                            
                                            
                                            
                                        }
                                    }
                                    
                                    //Prüfen, ob sich Default-Wert geändert hat
                                    elseif ($exttableconf[$table]['table'][$field]['datatype'] != 'TEXT' && $exttableconf[$table]['table'][$field]['datatype'] != 'LONG')
                                    {
                                        $output[$table]['changefield'][] = '<p>change default of '.$field.' ['.$exttableconf[$table]['table'][$field]['default'].' -> '.$imptableconf[$table]['table'][$field]['default'].']</p>';
                                        
                                        if ($confirm_syncimport) {
                                            /* --- Defaultwert setzen --------------------------------------------- */
                                            $parsetype = get_db_field_type(strtoupper($exttableconf[$table]['table'][$field]['datatype']));
                                            $def = trim($imptableconf[$table]['table'][$field]['default']);

                                            if($def == "0"){
                                                    $def0 = 1;
                                            }

                                            if($parsetype == 1){
                                                    $def = parse_db_int($def);
                                            }elseif($parsetype == 6){
                                                    $def = parse_db_float($def);
                                            }elseif($parsetype == 2){
                                                    $def = "'".parse_db_string($def)."'";
                                            }elseif($parsetype == 4){
                                                    if(!$def){
                                                            $def = LMB_DBDEF_NULL;
                                                    }
                                            }elseif($parsetype == 3){
                                                    $def = parse_db_bool($def);
                                            }

                                            if(!$def AND !$def0){
                                                    $def = LMB_DBDEF_NULL;
                                            }

                                            if($sqlquery = dbq_9(array($DBA["DBSCHEMA"],$table,$field,$def))){
                                                    $rs1 = odbc_exec($db,$sqlquery) or errorhandle(odbc_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
                                            }
                                        }

                                    }
                                    
                                    
                                }
                            }
                            //Falls Feld nicht vorhanden, erstelle es
                            else
                            {
                                $output[$table]['createfield'][] = '<p style="color:#050">create field '.$field.'</p>';// in table '.$table.'</p>';
                                //add_field($field,$gtabid,$typ,$typ2,$typ_size,$ifield_id,$description,$spellingf,$default,$sort,$add_permission,$inherit_tab,$inherit_field,$import_typ='default',$extension=null){
                            
                                if ($confirm_syncimport) {
                                    /* --- Neues Feld anlegen --------------------------------------------- */
                                    $parsetype = get_db_field_type(strtoupper($imptableconf[$table]['table'][$field]['datatype']));
                                    $def = trim($imptableconf[$table]['table'][$field]['default']);

                                    if($def == "0 "){
                                            $def0 = 1;
                                    }

                                    if($parsetype == 1){
                                            $def = parse_db_int($def);
                                    }elseif($parsetype == 6){
                                            $def = parse_db_float($def);
                                    }elseif($parsetype == 2){
                                            $def = "'".parse_db_string($def)."'";
                                    }elseif($parsetype == 4){
                                            if(!$def){
                                                    $def = LMB_DBDEF_NULL;
                                            }
                                    }elseif($parsetype == 3){
                                            $def = parse_db_bool($def);
                                    }

                                    if(!$def AND !$def0){
                                            $def = LMB_DBDEF_NULL;
                                    }

                                    $ct = parse_db_type($imptableconf[$table]['table'][$field]['datatype'],$imptableconf[$table]['table'][$field]['fieldlength'],$imptableconf[$table]['table'][$field]['fieldlength']);
                                    $datentyp = $ct[0];
                                    if (strpos($datentyp,'BOOL') !== false) { $datentyp = 'BOOLEAN'; }
                                    if (strpos($datentyp,'TEXT') !== false) { $datentyp = 'TEXT'; }
                                    
                                    $sqlquery = 'ALTER TABLE '.$table.' ADD COLUMN '.$field.' '.$datentyp.' DEFAULT '.$def;
                                    
                                    $rs1 = odbc_exec($db,$sqlquery) or errorhandle(odbc_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
                                    if(!$rs1) {
                                             error_log($sqlquery);
                                    }
                                }
                            }
                        }
                        
                        //Feld löschen
                        foreach ($exttableconf[$table]['table'] as $field => $value)
                        {
                            if (!array_key_exists($field, $imptableconf[$table]['table']))
                            {
                                $output[$table]['deletefield'][] = '<p style="color:#f00">delete field '.$field.'</p>';// in table '.$table.'</p>';
                                
                                if ($confirm_syncimport) {
                                    if($sqlquery = dbq_22(array($table,$field))){
                                        $rs1 = odbc_exec($db,$sqlquery) or errorhandle(odbc_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
                                    }
                                }
                            }
                        }
                        
                       
                        //break;
                    }
                    
                }
                
                
                
                
                //Adminrechte abgleichen
                if ($confirm_syncimport)
                {
                    echo '<p>refresh table and field rules</p>';
                    $check_all = true;
                    require_once("admin/group/group.lib");
                    require_once("admin/tools/grusrref.php");
                    
                    //Versuche alle Views zu speichern / überschreiben
                    echo '<h3>Try to save views</h3>
                                <div style="height:200px;overflow:auto;border:1px solid grey;padding:4px;">';
                    
                    
                    //Alle Views sammeln
                    $sqlquery1 = 'SELECT tabelle, viewdef FROM lmb_conf_views INNER JOIN lmb_conf_tables ON (lmb_conf_views.ID = lmb_conf_tables.tab_id)';
                    $result = odbc_exec($db,$sqlquery1) or errorhandle(odbc_errormsg($db),$sqlquery1,$action,__FILE__,__LINE__);
                    while ($view = odbc_fetch_array($result)) {
                        //$views[] = $view;
                        $sqlquery2 = dbq_19(array($view['tabelle'],$view['viewdef']));
                        $rs = odbc_exec($db,$sqlquery2) or errorhandle(odbc_errormsg($db),$sqlquery2,$action,__FILE__,__LINE__);
                        if(!$rs) {
                            echo '<p style="color:#f00">view '.$view['tabelle'].' NOT saved';
                        }
                        else
                        {
                            echo '<p>view '.$view['tabelle'].' saved';
                        }
                    }
                    echo '</div><br><br>';
                }
                
                
                
                
                
                //Bericht ausgeben
                if ($precheck)
                {
                    //Zu löschende Tabellen
                    echo '<h3 style="color:#f00">delete tables</h3>
                                <div style="height:200px;overflow:auto;border:1px solid grey;padding:4px;">';
                        if(is_array($output['deletetab']))
                        {
                            foreach($output['deletetab'] as $value)
                            {
                                echo $value;
                            }
                        }
                    echo '</div><br><br>';

                    //Neue Tabellen
                    echo '<h3>create tables</h3>
                                <div style="height:200px;overflow:auto;border:1px solid grey;padding:4px;">';
                        if(is_array($output['createtab']))
                        {
                            foreach($output['createtab'] as $value)
                            {
                                echo $value;
                            }
                        }
                    echo '</div><br><br>';


                    //Feldänderungen
                    echo '<h3>fieldchanges per table</h3>
                                <div style="height:400px;overflow:auto;border:1px solid grey;padding:4px;">';
                        foreach($output as $table => $value)
                        {
                            if ($table != 'createtab' && $table != 'deletetab')
                            {
                                echo '<strong>'.strtoupper($table).'</strong>';
                                echo '<div style="overflow:auto;border:1px solid grey;padding:4px 10px;">';

                                if (is_array($output[$table]['deletefield'])) {
                                    foreach ($output[$table]['deletefield'] as $value) {
                                        echo $value;
                                    }
                                }
                                if (is_array($output[$table]['createfield'])) {
                                    foreach($output[$table]['createfield'] as $value)
                                    {
                                        echo $value;
                                    }
                                }
                                if (is_array($output[$table]['changefield'])) {
                                    foreach($output[$table]['changefield'] as $value)
                                    {
                                        echo $value;
                                    }
                                }
                                echo '</div><br>';
                            }
                        }
                    echo '</div><br><br>';
                    
                    echo '<input type="button" value="'.$lang[1005].'" onclick="document.form1.confirm_syncimport.value=1;document.form1.submit();">';
                }
                
                if ($confirm_syncimport) {
                    //alle lmb_ überschreiben
                    foreach ($tablegrouplist as $table => $value)
                    {
                            $sys = exec('tar -x -C '.$path.' -f '.$path.$table.'.tar.gz');
                            deleteExistingTab($table);
                            import(false,'over',null,null, null,null,'export');
                    }
                    
                    
                    //Tabellen löschen
                    asort ($delete);
                    
                    foreach ($delete as $table => $value)
                    {
                        deleteExistingTab($table);
                    }
                    
                    echo 'Sync DONE';
                    
                }
	
}

//Konfiguration in Array einlesen
function parseConfigToArray($file)
{
    $config_datei = fopen($file,"r");
    $output = array();
    $output['table'] = array();

	while($line = fgets($config_datei, 100000)){
            
		if(substr($line,0,1) != "#" AND substr($line,0,1) != " "){

			if(substr($line,0,22) == '<begin indexdefinition'){
				$definition = "index";
			} elseif(substr($line,0,20) == "<end indexdefinition"){
				$definition = "";

			} elseif(substr($line,0,20) == "<begin keydefinition"){
				$definition = "key";
			} elseif(substr($line,0,18) == "<end keydefinition"){
				$definition = "";

			} elseif(substr($line,0,21) == "<begin viewdefinition"){
				$definition = "view";
			} elseif(substr($line,0,19) == "<end viewdefinition"){
				$definition = "";

			} elseif(substr($line,0,24) == "<begin triggerdefinition"){
				$definition = "trigger";
			} elseif(substr($line,0,22) == "<end triggerdefinition"){
				$definition = "";

			} elseif(substr($line,0,22) == "<begin tabledefinition"){
				$definition = "table";

			} elseif(substr($line,0,20) == "<end tabledefinition"){
				$definition = "";

			}elseif($definition == "table"){
				unset($cdf);
                                
				$cdf = explode("::",$line);
                                
                                $table = array();
				$fieldname = parse_db_syntax($cdf[0],2);	# fieldname
				$table['datatype'] = trim($cdf[1]);			# datatype
				$table['codetype'] = trim($cdf[2]);			# codetype
				$table['fieldlength'] = trim($cdf[3]);			# fieldlength
				$table['scale'] = (trim($cdf[4]) == '') ? '0' : trim($cdf[4]);			# scale
				$table['mode'] = trim($cdf[5]);				# mode
				$table['default'] = trim($cdf[6]);			# default
				$table['database_specific_datatype'] = constant("LMB_DBTYPE_".trim($cdf[1]));	# database specific datatype
				if($cdf[2]){$table['database_specific_codetype'] = constant("LMB_DBTYPE_".trim($cdf[2]));}	# database specific codetype

                                $output['table'][$fieldname] = $table;
                                
			}elseif($definition == "index"){
				unset($cdf);
				$cdf = explode("::",$line);
				$output['index'][] = array(strtoupper($cdf[0]),$cdf[1]);

			}elseif($definition == "view"){
				$output['view'][] = array(substr($line,0,strpos($line,"::")),substr($line,strpos($line,"::")+2,strlen($line)));
			}elseif($definition == "key"){
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


function convert_type($parsetype,$dbschema,$table,$field,$datentyp){
        global $db;
        global $result_type;
        global $action;
        global $lang;

        # try db-based modify
        $sqlquery = dbq_15(array($dbschema,$table,$field,$datentyp));
        $rs = @odbc_exec($db,$sqlquery);
        # manual modify
        if(!$rs) {
                if($parsetype == 1 OR $parsetype == 6){
                        $conversion = "NUM";
                }else{
                        $conversion = "CHAR";
                }

                //delete LMB_TEMP_CONVERT
                $sqlquery = dbq_22(array($table,'LMB_TEMP_CONVERT'));
                $rs = odbc_exec($db,$sqlquery) or errorhandle(odbc_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
                
                # copy
                $sqlquery = "ALTER TABLE $table ".LMB_DBFUNC_ADD_COLUMN_FIRST." LMB_TEMP_CONVERT ".$datentyp;
                $rs = @odbc_exec($db,$sqlquery) or errorhandle(odbc_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
                if(!$rs){lmb_alert("unable to add temporay field!");return false;}
                # db-based update
                $sqlquery =  "UPDATE $table SET LMB_TEMP_CONVERT = $conversion(".$field.")";
                $rs = @odbc_exec($db,$sqlquery);

                # Einträge in neues Tabellenfeld
                if(!$rs AND ($result_type["data_type"][$convert] != 18 AND $result_type["data_type"][$convert] != 31 AND $result_type["data_type"][$convert] != 32 OR $raw)) {
                        $sqlquery =  "SELECT ID,".$field." FROM $table";
                        $rs = odbc_exec($db,$sqlquery) or errorhandle(odbc_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
                        $bzm = 1;
                        while(odbc_fetch_row($rs,$bzm)){
                                # numeric
                                if($parsetype == 1 OR $parsetype == 6){
                                        $value = parse_db_int(odbc_result($rs,$field),$result_type["size"][$convert]);
                                # string
                                }else{
                                        $value = parse_db_string(odbc_result($rs,$field),$result_type["size"][$convert]);
                                }
                                $prepare_string = "UPDATE $table SET LMB_TEMP_CONVERT = ? WHERE ID = ".odbc_result($rs,"ID");
                                lmb_PrepareSQL($prepare_string,array(parse_db_blob($value)),__FILE__,__LINE__);

                                $bzm++;
                        }
                        $GLOBALS["alert"] = $lang[2022];
                }

                if(!$commit){
                        # drop origin
                        $sqlquery = dbq_22(array($table,$field));
                        $rs = odbc_exec($db,$sqlquery) or errorhandle(odbc_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
                        if(!$rs) {$commit = 1;}
                        # rename copy
                        $sqlquery = dbq_7(array($DBA["DBSCHEMA"],$table,"LMB_TEMP_CONVERT",$field));
                        $rs = odbc_exec($db,$sqlquery) or errorhandle(odbc_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
                        if(!$rs) {$commit = 1;}
                }
            //delete LMB_TEMP_CONVERT
            $sqlquery = dbq_22(array($table,'LMB_TEMP_CONVERT'));
            $rs = odbc_exec($db,$sqlquery) or errorhandle(odbc_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
        }

        if($commit){return false;}else{return true;}
}

function get_db_field_type($type)
{
    $parsetypes = array('FIXED' => 1,'INTEGER' =>1,'SMALLINT' => 1, 'VARCHAR' => 2, 'LONG' => 2,'BOOLEAN'=> 3,'DATE'=>4,'TIMESTAMP'=> 4,'TIME'=>4,'FLOAT'=> 6,'NUMERIC' => 6);
    return $parsetypes[$type];
}
?>