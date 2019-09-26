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
 * ID: 51
 */

set_time_limit(1200); #20min

function exportTabCSV($fh,$gtabid,&$gresult,&$filter, $expview=1, $typ=null){
    global $session;
    global $gtab;
    global $gfield;
    global $farbschema;
    global $umgvar;

    /* ---------------- Seperator ------------------ */
    $delimiter = ($umgvar['csv_delimiter'] == "") ? ',' : $umgvar['csv_delimiter'];
    $enclosure = ($umgvar['csv_enclosure'] == "") ? '"' : $umgvar['csv_enclosure'];
    if($umgvar['csv_delimiter'] == '\t'){$delimiter = "\t";}

    /* ---------------- Header ------------------ */
    if($typ == 1){$line = '<!DOCTYPE html>
<html>
<head>
    <title></title>
    <meta http-equiv="content-type" content="application/vnd.ms-excel; charset='.$umgvar["charset"].'">
</head><BODY><TABLE><TR>';}
    foreach ($gfield[$gtabid]["sort"] as $key => $value){
        if(!$gfield[$gtabid]["funcid"][$key]){continue;}
        if(!$filter["hidecols"][$gtabid][$key] AND $gfield[$gtabid]["field_type"][$key] < 100 AND $gfield[$gtabid]["field_type"][$key] != 20){
            if($typ == 1){$line .= "<TD>".$gfield[$gtabid]['spelling'][$key]."</TD>";}
			elseif($typ == 3){
                $l[] = $gfield[$gtabid]['spelling'][$key]; //'"'.str_replace('"','""',$gfield[$gtabid]['spelling'][$key]).'"';
            }
        }
    }
    if($typ == 3){
        //$line .= implode(";",$l)."\n";
        fputcsv($fh,$l,$delimiter,$enclosure);
    }
    if($typ == 1){
        $line .= "</TR>\n";
        fwrite($fh, $line);
        $line = '';
    }

    /* ---------------- Body ------------------ */
    if($expview == 2){
        $rescount = $gresult[$gtabid]["res_count"];
    }else{
        $rescount = $gresult[$gtabid]["res_viewcount"];
    }

    $bzm = 0;
    while($bzm < $rescount) {
        $l = array();

        /* --- Feldschleife für export --------------------------------------- */
        if($typ == 1){
            if($BGCOLOR1 == $farbschema["WEB8"]){$BGCOLOR = $farbschema["WEB8"];$BGCOLOR1 = $farbschema["WEB8"];} else {$BGCOLOR = $farbschema["WEB8"];$BGCOLOR1 = $farbschema["WEB8"];}
            if($gresult[$gtabid]["color"][$bzm]){$BGCOLOR = "#".$gresult[$gtabid]["color"][$bzm];}
            if($cres){$BGCOLOR = "#".$cres;}

            $line = "<TR BGCOLOR=\"$BGCOLOR\">";
        }

        foreach ($gfield[$gtabid]["sort"] as $key => $value){
            if(!$gfield[$gtabid]["funcid"][$key]){continue;}
            if(!$filter["hidecols"][$gtabid][$key] AND $gfield[$gtabid]["field_type"][$key] < 100 AND $gfield[$gtabid]["field_type"][$key] != 20){
                if($gfield[$gtabid]["color"][$key] AND !$gresult[$gtabid]["color"][$bzm]){$BGCOLORTD = " BGCOLOR = \"#".$gfield[$gtabid]["color"][$key]."\"";}else{$BGCOLORTD = "";}

                if($typ == 1){$line .= "<TD".$BGCOLORTD.">";}

                /* ------------------ Typfunction --------------------- */
                $fname = "cftyp_".$gfield[$gtabid]["funcid"][$key];
                $retrn = $fname($bzm,$key,$gtabid,5,$gresult,0);
                /* ------------------ /Typfunction -------------------- */
                if(is_array($retrn)){
                    if($gfield[$gtabid]["field_type"][$key] == 11 AND is_array($retrn["value"])){
                        $retrn = implode("; ",$retrn["value"]);
                    }else{
                        $retrn = implode("; ",$retrn);
                    }
                }

                if($typ == 1){
                    if($gfield[$gtabid]["wysiwyg"][$field_id]){
                        $retrn = strip_tags($retrn,"<br>");
                    }
                    $line .= "$retrn</TD>";
                }elseif($typ == 3){
                    $l[] = $retrn;//'"'.str_replace('"','""',$retrn).'"';
                }
            }
        }

        if($typ == 3){
            //$line .= implode(";",$l)."\n";
            fputcsv($fh,$l,$delimiter,$enclosure);
        }
        if($typ == 1){
            $line .= "</TR>\n";
            fwrite($fh, $line);
            $line = '';
        }

        $bzm++;
    }

    if($typ == 1){
        $line .= '</TABLE></BODY></html>';
        fwrite($fh, $line);
        $line = '';
    }
}



# ----------------- XML ---------------------
function domTableElement($dom,$parentElement,$gtabid,$verkn,$filter,$gsr){
	global $umgvar;
	global $session;
	global $gfield;
	global $gtab;
	global $gverkn;
	global $subcount;
	global $still_done;
	global $still_sdone;
	global $popc;


	# Abfrage
	$gresult = get_gresult($gtabid,1,$filter,$gsr,$verkn);
	
	# Anzahl Egebnisse
	$rescount = $gresult[$gtabid]["res_count"];
	#$rescount = $gresult[$gtabid]["res_viewcount"];

	$bzm = 0;
	while($bzm < $rescount) {
		#echo "org.".$gtab[table][$gtabid]." ".$gresult[$gtabid]["id"][$bzm]."<BR>";
		#echo "sub.".$verkn[tab]." ".$verkn["id"]."<BR><BR>";
		
		# ---- auf Endlosverknüpfungen prüfen ------
		$still_done[$gtabid] = $gtabid;
		# ---- Endlosverknüpfungen bei Selbstverknüpfung ------
		if($gverkn[$gtabid]["id"] AND $popc[$gtabid] AND $gtab['sverkn'][$gtabid]){
			if(!$still_sdone[$gtabid]){$still_sdone[$gtabid] = array();}
			if(in_array($gresult[$gtabid]["id"][$bzm],$still_sdone[$gtabid])){$bzm++; continue;}
			$still_sdone[$gtabid][] = $gresult[$gtabid]["id"][$bzm];
		}
		# Datensätze
		$tableElement = $dom->createElement(lmb_utf8_encode($gtab["table"][$gtabid]));
		
		# Felder
		foreach ($gfield[$gtabid]["sort"] as $key => $value){
			if(!$filter["hidecols"][$gtabid][$key] AND $gfield[$gtabid]["field_type"][$key] < 100 AND $gfield[$gtabid]["field_type"][$key] != 20){
				# ---- Typfunction ------
				$fname = "cftyp_".$gfield[$gtabid]["funcid"][$key];
				$retrn = $fname($bzm,$key,$gtabid,5,$gresult,0);
				# Ohne Verknüpfungsfelder
				if($gfield[$gtabid]["field_type"][$key] != 11){
					$fieldElement = $dom->createElement(lmb_utf8_encode($gfield[$gtabid]['field_name'][$key]));
					$fieldElement->appendChild($dom->createTextNode(lmb_utf8_encode($retrn)));
					$tableElement->appendChild($fieldElement);
				}
			}
		}
		# Verknüpfungs-ID
		if($verkn["id"]){
			$fieldElement = $dom->createElement(lmb_utf8_encode($verkn['tab']."_ID"));
			$fieldElement->appendChild($dom->createTextNode($verkn["id"]));
			$tableElement->appendChild($fieldElement);
		}
			

		# Plusverknüpfung aufsteigend
		if($gverkn[$gtabid]["id"] AND $popc[$gtabid]){
			foreach($gverkn[$gtabid]["id"] as $key => $value){
				if($popc[$gtabid][$gfield[$gtabid]["verkntabid"][$key]] AND $gtab["groupable"][$gtabid] > 0){								
					# Abbruch bei Endlosverknüpfung
					if($value != $gtabid AND in_array($value,$still_done)) continue;
					# Abfrage
					$verkn_ = set_verknpf($gtabid,$gfield[$gtabid]["field_id"][$key],$gresult[$gtabid]["id"][$bzm],0,0,1,0);
					# Ausgabe
					$subElement = domTableElement($dom,$tableElement,$gfield[$gtabid]["verkntabid"][$key],$verkn_,$filter,0);
				}
			}
		}
		#---- Funktionen aufrufen für Plusverknüpfung absteigend -------
		if($gfield[$gtabid]["r_verkntabid"] AND $popc[$gtabid] AND $gtab["groupable"][$gtabid] > 0){
			foreach($gfield[$gtabid]["r_verkntabid"] as $key => $value){
				# Abbruch bei Selbstverknüpfung
				if($gfield[$gtabid]["r_verkntabid"][$key] == $gtabid) continue;
				if($popc[$gtabid][$gfield[$gtabid]["r_verkntabid"][$key]] AND !in_array($value,$still_done)){
					# Abfrage
					$verkn_ = set_verknpf($value,$gfield[$value]["field_id"][$key],$gresult[$gtabid]["id"][$bzm],0,0,1,2);
					# Ausgabe
					#if($gresult_[$value]['res_viewcount'] > 0){
						$subElement = domTableElement($dom,$tableElement,$gfield[$gtabid]["r_verkntabid"][$key],$verkn_,$filter,0);
					#}
				}
			}
		}
		
		# ---- Endlosverknüpfungen leeren ------
		$still_done[$gtabid] = null;
		$still_sdone[$gtabid] = null;
		
		$parentElement->appendChild($tableElement);
		$bzm++;
	}

	return $parentElement;
}


#---- Excell Export -------
if($exp_medium == 1 OR $exp_medium == 3){
	#header("Content-Type: application/vnd.ms-excel");
	#header("Content-Disposition: attachement; filename=".$gtab['table'][$gtabid].".xls");

	if($exp_medium == 1){$ext = '.xls';}else{$ext = '.csv';}
	
	# export file
	$name = $gtab["table"][$gtabid]."_".date("Y-m-d-h-m-s").$ext;
	$out = $umgvar['pfad']."/USER/".$session['user_id']."/temp/".$name;
	$outurl = $umgvar['url']."/USER/".$session['user_id']."/temp/".$name;
	
	# gresult
	$filter_ = $filter;
	$filter_["getlongval"][$gtabid] = 1;
	if ($exp_typ == 2) {
		$filter_["anzahl"][$gtabid] = 'all';
	}
	$gresult = get_gresult($gtabid,1,$filter_,$gsr,$verkn);
	
	# open file handler
	$fh = fopen ($out, 'w');
	
	# fill file
	exportTabCSV($fh,$gtabid,$gresult,$filter, $exp_typ, $exp_medium);

	# close file
	fclose($fh);
	
	if(file_exists($out)){
		echo "<script language=\"JavaScript\">document.location.href = '".$outurl."';</script>";
	}
	
#---- XML Export -------
}elseif($exp_medium == 2){
	# neues DOM Objekt
	$dom = new DomDocument('1.0', $umgvar['charset']);

	$filter_ = $filter;
	
	# root Element (Beschreibung)
	$root = $dom->createElement("Limbas-Export-".date("Y-m-d"));
	# Tabellen Eelement
	$root = domTableElement($dom,$root,$gtabid,$verkn,$filter,$gsr);
	# Rootelement schliesen
	$dom->appendChild($root);
	
	# ------- xml Pfad ---------
	$name = "limbasExport_".date("Y-m-d-h-m-s").".xml";
	$out = $umgvar['pfad']."/USER/".$session['user_id']."/temp/".$name;
	$outurl = $umgvar['url']."/USER/".$session['user_id']."/temp/".$name;
	# ------- xml schreiben ---------
	
	$fh = fopen ($out, 'w');
	fwrite($fh, $dom->saveXML());
	fclose($fh);
	if(file_exists($out)){
		echo "<script language=\"JavaScript\">document.location.href = '".$outurl."';</script>";
	}

#---- CSV Export -------
}
	

?>
