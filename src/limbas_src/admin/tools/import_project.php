<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */


?>

<div class="row">
    <div class="col-md-9 col-lg-7">

        <div class="card border-top-0 mb-3">
            <div class="card-body">

                <div class="row mb-2">
                    <label for="fileproject" class="col-sm-2 col-form-label">File:</label>
                    <div class="col-sm-10">
                        <input type="file" name="fileproject" id="fileproject">
                    </div>
                </div>

                <div class="row mb-2">
                    <label for="remoteHost" class="col-sm-2 col-form-label">Host:</label>
                    <div class="col-sm-10">
                        <input type="text" id="remoteHost" name="remote_host" class="form-control" value="<?=stripslashes($remote_host)?>">
                    </div>
                </div>
                <div class="row mb-2">
                    <label for="remoteUser" class="col-sm-2 col-form-label">User:</label>
                    <div class="col-sm-10">
                        <input type="text" id="remoteUser" name="remote_user" class="form-control" value="<?=stripslashes($remote_user)?>">
                    </div>
                </div>
                <div class="row mb-3">
                    <label for="remotePass" class="col-sm-2 col-form-label">Pass:</label>
                    <div class="col-sm-10">
                        <input type="password" id="remotePass" name="remote_pass" class="form-control">
                    </div>
                </div>

                <div class="text-end">
                    <button class="btn btn-primary" type="button" value="1" onclick="document.form1.precheck.value=1;document.form1.remoteimport.value=1;document.form1.submit();"><?=$lang[2243]?></button>
                </div>

            </div>
        </div>
    </div>
</div>
    


<?php



# ------ user / url ----------
$LIM["username"] = $remote_user;
$LIM["pass"] = $remote_pass;
if(lmb_substr($remote_host,0,5) == "https"){
	$protocol = "https";
}else{
	$protocol = "http";
}
$remote_host = "$protocol://".str_replace("//","/",$remote_host);
$remote_host = str_replace("$protocol://$protocol:/","$protocol://",$remote_host);
$LIM["lim_url"] = $remote_host;

#session_name("limbas_remote_import");
#session_start();

# remoteimport with file
if(($remoteimport AND (!empty($_FILES["fileproject"])) AND $_FILES["fileproject"]['size'] > 0) OR $confirm_fileimport){

	require_once(COREPATH . 'admin/tools/import_remote.lib');

	$lmpar[0]["action"] = "setup_remote_exportfiles";
	$path = $umgvar["pfad"]."/USER/".$session["user_id"]."/temp/";
	
	if($precheck) {
		# Temptabellen löschen
		clean_import_temp();
		# Tempverzeichniss leeren
		rmdirr($umgvar["pfad"]."/USER/".$session["user_id"]."/temp/");
		
		# Datei in Tempverzeichnis verschieben und import durchführen
		if(!move_uploaded_file($_FILES['fileproject']['tmp_name'], $umgvar["pfad"]."/USER/".$session["user_id"]."/temp/export.tar.gz")){
                    echo '<div>Dateiupload fehlgeschlagen</div>';
                    return;
                }
		# Datei entpacken
		$sys = exec("tar -x -C ".$umgvar["pfad"]."/USER/".$session["user_id"]."/temp/ -f ".$umgvar["pfad"]."/USER/".$session["user_id"]."/temp/export.tar.gz");
		
		# read configuration
		if(file_exists($path."/export.php")){
			include_once($path."/export.php");
			
			# check for different encodings
			if ($GLOBALS["umgvar"]["charset"] != $export_conf['encoding']){
				$txt_encode = 1;
			}
		}
		
		# Liste aller Tabellen im entpackten Verzeichnis
		if($folderval = read_dir($path)){
			foreach($folderval["name"] as $key => $file){
				if($folderval["typ"][$key] == "file" AND lmb_substr($file,0,7) != "export."){
					# Nur zu importierende Tabellen
					$tablename = str_replace(".gz","",$file);
					$tablename = str_replace(".tar","",$tablename);
					$tablegrouplist[$tablename] = 1;
					# Nur Projekt-Tabellen
					#if(lmb_substr($tablename,0,10) != "LMBX_VERK_" AND lmb_substr($tablename,0,9) != "LMBX_LMB_"){
					#	$tableprojektlist[lmb_strtoupper($tablename)] = 1;
					#}
				}
			}
		}
		# ----------- import with prefix LMBX_ -----------
		if($precheck){
			echo "'<div>import details</div>
			<div id=\"rimport_import_scolldown\" style=\"height:200px;overflow:auto;border:1px solid grey;padding:4px;\">";
			$result = import_tab_pool("atm","rename","group",1,null,null,null,$tablegrouplist,"LMBX_",$txt_encode);
			echo '</div>';
		}
	}

	# read configuration
	include_once($path."/export.php");

	# Importieren
	$lmbs["lmp"]["gtab"] = $export_conf['gtab'];
	$lmbs["lmp"]["tabgroup"] = $export_conf['tabgroup'];
	$exptable = $export_conf['exptable'];
	$lmpar[0]["exptables"] = $exptable;
	
	/* --- Transaktion START --------------------------------------- */
	lmb_StartTransaction(1);

	if(!remote_mergeTables($lmbs,$path,$precheck)){$commit=1;}
	if(!remote_mergeForms($exptable,$precheck)){$commit=1;}
	if(!remote_mergeReport($exptable,$precheck)){$commit=1;}
	
	/* --- Transaktion ENDE -------------------------------------- */
	if($commit){
		lmb_EndTransaction(0);
	}elseif($precheck){
		lmb_EndTransaction(0,'none');
	} else {
		lmb_EndTransaction(1);
	}

	if($precheck){
		#$_SESSION['lmb_remotimport']['lmbs'] = $lmbs;
		if(is_array($summary)){
			echo "<hr><div><i class=\"lmb-icon lmb-info-circle-alt\"></i>&nbsp;<a onclick=\"document.getElementById('rimport_summary').style.display=''\">view summary</a></div>
			<div id=\"rimport_summary\" style=\"display:none;height:200px;overflow:auto;border:1px solid grey;padding:4px;\">";
			echo implode('<br><br>',$summary);
			echo '</div>';
		}
		echo '<hr><input type="button" onclick="document.form1.confirm_fileimport.value=1;document.form1.remoteimport.value=1;document.form1.submit();" value="importieren">';
	}else{
		clean_import_temp();
	}

	
}
# remoteimport with host
elseif($remoteimport AND $merge_import AND $exptable){
	
	require_once(COREPATH . 'admin/tools/import_remote.lib');
	
	$LIM["username"] = $_SESSION['lmbs']['LIM']['username'];
	$LIM["pass"] = $_SESSION['lmbs']['LIM']['pass'];
	$LIM["lim_url"] = $_SESSION['lmbs']['LIM']['lim_url'];

	$lmpar[0]["action"] = "setup_remote_exportfiles";
	$lmpar[0]["exptables"] = $exptable;
	
	if($precheck) {
		clean_import_temp();
		# Tempverzeichniss leeren
		rmdirr($umgvar["pfad"]."/USER/".$session["user_id"]."/temp/");
		
		# export soap Aufruf
		$lmp = soap_call_client($lmpar,$LIM);
		
		# kopieren in USER temp
		$lurl =parse_url($LIM["lim_url"]);
		$url = $lurl['scheme'].':'.$lurl['port'].'//'.$LIM['username'].':'.$LIM['pass'].'@'.$lurl['host'].$lurl['path'];
		
		# URL kopieren / allow_url_fopen = true
		if(!copy($url."USER/".$lmbs["session"]["user_id"]."/temp/export_bundle.tar.gz",$umgvar["pfad"]."/USER/".$session["user_id"]."/temp/export.tar.gz")){return;}
		$sys = exec("tar -x -C ".$umgvar["pfad"]."/USER/".$session["user_id"]."/temp/ -f ".$umgvar["pfad"]."/USER/".$session["user_id"]."/temp/export.tar.gz");
		unlink($umgvar["pfad"]."/USER/".$session["user_id"]."/temp/export.tar.gz");
	}

	# Importieren
	remote_mergeTables($lmbs,$umgvar["pfad"]."/USER/".$session["user_id"]."/temp/",$precheck);
	remote_mergeForms($exptable,$precheck);
	remote_mergeReport($exptable,$precheck);

	if(!$precheck) {
		clean_import_temp();
	}
	
	if(is_array($summary)){
		echo implode('<br>',$summary);	
	}

# Import-Auswahl Liste
}elseif($remoteimport AND $remote_user AND $remote_pass AND $remote_host){

	require_once(COREPATH . 'admin/tools/export.lib');
	
	
	# Soapaufruf
	$lmpar[0]["action"] = "setup_remote_exportlist";
	if($lmp = soap_call_client($lmpar,$LIM)){
		
		
		echo "<FORM ACTION=\"main_admin.php\" METHOD=\"post\" name=\"form2\">
		<INPUT TYPE=\"hidden\" NAME=\"action\" VALUE=\"setup_import\">
		<INPUT TYPE=\"hidden\" NAME=\"merge_import\" VALUE=\"1\">
		<hr>
		<div style=\"padding:2px;\">";

		$_SESSION["lmbs"]["LIM"] = $LIM;
		$_SESSION["lmbs"]["lmp"] = $lmp[0];
	
		lmbExport_groupSelection($lmp);

		echo "<HR><div align= \"center\"><input type=\"submit\" name=\"remoteimport\" value=\"".$lang[979]."..\"></div>
		</div>
		</FORM>";
	}

}


?>
