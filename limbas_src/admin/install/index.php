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
 * ID: 35
 */

extract($_SERVER, EXTR_SKIP);
extract($_POST, EXTR_SKIP);

ini_set("short_open_tag",1);
ini_set("magic_quotes_gpc",0);
ini_set("mbstring.func_overload",7);
ini_set("mbstring.internal_encoding",$setup_charset);

?>
<html>
<head>
<title><?php echo $_SERVER['SERVER_NAME'];?> : LIMBAS Installation</title>
<style type="text/css">
body {
	font-size: .7em;
	font-family: sans-serif;
	padding:15px;
}
div.setup_page{
	width:600px;
	margin:auto;
}
h1 {
	font-size:1.5em;
	text-decoration:underline;
}
h2 {
	font-size:1.3em;
}
table {
	font-size: inherit;
	font-family: inherit;
}

input {
	font-size: inherit;
	font-family: inherit;
}
select {
	font-size: inherit;
	font-family: inherit;
}
.logo{
	text-align:center;
}
.header{
	width:700px;
	margin:auto;
}
.copyright{
	background-color:white;
	border:solid 1px orange;
	padding:2px;
	margin:16px;
}
.error{
	background-color:white;
	border:solid 2px red;
	padding:8px;
	margin:auto;
	margin-top:16px;
	width:300px;
}

</style>

<script language="javascript" type="text/javascript">
function update_dbvendor(value){
	var db_vendor = document.getElementById("db_vendor")
	var db_schema = document.getElementById("db_schema");
	var db_driver = document.getElementById("db_driver");
	var db_name = document.getElementById("db_name");
	if (db_vendor.value == 'postgres'){
		db_name.value = "limbas";
		db_schema.value = "public";
		db_driver.value = "PSQL";
	}else if (db_vendor.value == 'ingres'){
		db_schema.value = "ingres";
		db_name.value = "limbas";
		db_driver.value = "";
	}else if (db_vendor.value == 'mysql'){
		db_schema.value = "limbas";
		db_driver.value = "";
		db_name.value = "limbas_resource";
	}else if (db_vendor.value == 'mssql'){
		db_schema.value = "dbo";
		db_driver.value = "";
		db_name.value = "limbas_mssql";
	}else if (db_vendor.value == 'maxdb76'){
		db_schema.value = "limbasuser";
		db_driver.value = "maxdb";
		db_name.value = "limbas";
	}
}


function update_dbschema(value){
	var db_vendor = document.getElementById("db_vendor")
	var db_schema = document.getElementById("db_schema");
	var db_driver = document.getElementById("db_driver");
	var db_name = document.getElementById("db_name");
	if (db_vendor.value == 'postgres'){
		db_schema.value = "public";
	}else if (db_vendor.value == 'ingres'){
		db_schema.value = "ingres";
	}else if (db_vendor.value == 'mysql'){
		db_schema.value = "limbas";
	}else if (db_vendor.value == 'mssql'){
		db_schema.value = 'dbo';
	}else if (db_vendor.value == 'maxdb76'){
		db_schema.value = value;
	}
}






function showprogress(id, value){
	if (value==100){
		document.getElementById(id).style.display='none';
		document.getElementById(id+"_container").style.display='none';
		return;
	}

	document.getElementById(id).style.width = Math.round(value)+"%";
	document.getElementById(id).innerHTML = Math.round(value)+"%";
}

function scrolldown(){
	document.body.scrollTop = '999999';
}

</script>
</head>
<body bgcolor="#F5F5F5">
<form action="." method="POST" name="form1">

<div class="header">
<div class="logo"><img src="../../pic/logo_topleft.png" border="0" alt="LIMBAS Business Solutions"></div>
<div class="copyright">LIMBAS. Copyright &copy; 1998-2016 Axel Westhagen. Go to <a href="http://www.limbas.org/" target="new">http://www.limbas.org/</a> for details.
LIMBAS comes with ABSOLUTELY NO WARRANTY; This is free software, and you are welcome to redistribute it under
certain conditions. Extensions are copyright of their respective owners.</div>
</div>

<?php
if($install == "check"){
		/* Check for unixODBC installation and bail out verbosely, if not installed */
		if (($DBA['DB']=="postgres" || $DBA['DB']=="ingres") && (!function_exists("odbc_pconnect"))){
			echo <<<EOD
		<div class="error">
			<h2>Error: unixODBC not installed!</h2>
			<p>Please install unixODBC and <a href="{$_SERVER['PHP_SELF']}">retry installation</a>.</p>
		</div>
		</body>
		</html>
EOD;
			exit(1);
		}

		require("../../lib/db/db_{$DBA['DB']}.lib");
		require("../../lib/include.lib");

        /* --- Datenbankverbindung ------------------------------------------- */
        $db = dbq_0($setup_host,$setup_database,$setup_dbuser,$setup_dbpass,$setup_dbdriver,$setup_dbport);
        /* --- Transaktion START ------------------------------------------------ */
        lmb_StartTransaction();
        /* --- DropTestTable ---------------------------------------- */
		#$odbc_table = dbf_20(array($DBA["DBSCHEMA"],dbf_4("LIMBASTEST"),"'TABLE'"));

        if($odbc_table){
        	$sqlquery1 = "DROP TABLE ".dbf_4("LIMBASTEST");
        	$rs1 = @odbc_exec($db,$sqlquery1) or errorhandle(odbc_errormsg($db),$sqlquery1,$action,__FILE__,__LINE__);
        }

        /* --- CREATE ---------------------------------------- */
        $sqlquery = "CREATE TABLE ".dbf_4("LIMBASTEST")." (ID ".LMB_DBTYPE_INTEGER.",ERSTDATUM ".LMB_DBTYPE_TIMESTAMP." DEFAULT ".LMB_DBDEF_TIMESTAMP.")";
        $rs = odbc_exec($db,$sqlquery) or errorhandle(odbc_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
        if($rs){$msg['db_create'] = "<font color=\"green\">OK";$msic[db_create] = "1";}else{$msg['db_create'] = "<font color=\"red\">FALSE";$msic[db_create] = "3";$commit = 1;}
        /* --- INSERT ---------------------------------------- */
        $sqlquery = "INSERT INTO LIMBASTEST (ID)  VALUES (1)";
        $rs = odbc_exec($db,$sqlquery) or errorhandle(odbc_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
        if($rs){$msg['db_insert'] = "<font color=\"green\">OK";$msic[db_insert] = "1";}else{$msg['db_insert'] = "<font color=\"red\">FALSE";$msic[db_insert] = "3";$commit = 1;}
        /* --- INSERT ---------------------------------------- */
        $sqlquery = "SELECT * FROM LIMBASTEST";
        $rs = odbc_exec($db,$sqlquery) or errorhandle(odbc_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
        if($rs){$msg['db_select'] = "<font color=\"green\">OK";$msic[db_select] = "1";}else{$msg['db_select'] = "<font color=\"red\">FALSE";$msic[db_select] = "3";$commit = 1;}
        /* --- DELETE ---------------------------------------- */
        $sqlquery = "DELETE FROM LIMBASTEST";
        $rs = odbc_exec($db,$sqlquery) or errorhandle(odbc_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
        if($rs){$msg['db_delete'] = "<font color=\"green\">OK";$msic[db_delete] = "1";}else{$msg['db_delete'] = "<font color=\"red\">FALSE";$msic[db_delete] = "3";$commit = 1;}
        /* --- DROP ---------------------------------------- */
        $sqlquery = "DROP TABLE ".dbf_4("LIMBASTEST");
        $rs = odbc_exec($db,$sqlquery) or errorhandle(odbc_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
        if($rs){$msg['db_drop'] = "<font color=\"green\">OK";$msic[db_drop] = "1";}else{$msg['db_drop'] = "<font color=\"red\">FALSE";$msic[db_drop] = "3";$commit = 1;}

        /* --- Transaktion ENDE -------------------------------------- */
		lmb_EndTransaction(!(isset($commit) && $commit));


        /* --- DB-CLOSE ------------------------------------------------------ */
        odbc_close($db);

        if(file_exists($setup_path_project."/main.php")){$msg['func_path'] = "<font color=\"green\">OK";$msic[func_path] = "1";}else{$msg['func_path'] = "<font color=\"red\">FALSE";$msic[func_path] = "3";$commit = 1;}

        /* --- include_db ---------------------------------main_admin.php--------------------- */
        if(file_exists($setup_path_project."/inc/include_db.lib")){
        	if(is_writable($setup_path_project."/inc/include_db.lib")){
				$msg['func_incdb'] = "<font color=\"green\">OK</font> .. you can set readonly after installation!";
				$msic[func_incdb] = "1";
			} else {
				$msg['func_incdb'] = "<font color=\"red\">FALSE";
				$msic[func_incdb] = "3";
				$commit = 1;
			}
        } else {
				$f = fopen($setup_path_project."/inc/include_db.lib", "wt");
				if (is_resource($f)){
					fclose($f);
					$msg['func_incdb'] = "<font color=\"orange\">file does not exist .. try to create OK";
					$msic[func_incdb] = "1";
				} else
				$msg['func_incdb'] = "<font color=\"red\">file does not exist .. try to create FAILED";
				$msic[func_incdb] = "3";
		}

        if(is_writable($setup_path_project."/BACKUP")){$msg['func_wr_backup'] = "<font color=\"green\">OK";$msic[func_wr_backup] = "1";}else{$msg['func_wr_backup'] = "<font color=\"red\">FALSE";$msic[func_wr_backup] = "3";$commit = 1;}
        if(is_writable($setup_path_project."/TEMP")){$msg['func_wr_temp'] = "<font color=\"green\">OK";$msic[func_wr_temp] = "1";}else{$msg['func_wr_temp'] = "<font color=\"red\">FALSE";$msic[func_wr_temp] = "3";$commit = 1;}
        if(is_writable($setup_path_project."/UPLOAD")){$msg['func_wr_upload'] = "<font color=\"green\">OK";$msic[func_wr_upload] = "1";}else{$msg['func_wr_upload'] = "<font color=\"red\">FALSE";$msic[func_wr_upload] = "3";$commit = 1;}
        if(is_writable($setup_path_project."/USER")){$msg['func_wr_user'] = "<font color=\"green\">OK";$msic[func_wr_user] = "1";}else{$msg['func_wr_user'] = "<font color=\"red\">FALSE";$msic[func_wr_user] = "3";$commit = 1;}


        if(function_exists("imagedestroy")){
                $msg[func_gd] = "<font color=\"green\">OK";$msic[func_gd] = "1";
                $gbsupport = gd_info();
                $msg[gdsupport] .= "<tr><td></td><td>&nbsp;&nbsp;&nbsp;GD Version</td><td><font color=\"blue\">".$gbsupport['GD Version']."</td></tr>";
                $msg[gdsupport] .= "<tr><td></td><td>&nbsp;&nbsp;&nbsp;Freetype Support for gd</td><td>";if($gbsupport['FreeType Support']){$msg[gdsupport] .= "<font color=\"green\">OK";}else{$msg[gdsupport] .= "<font color=\"red\">FALSE";} $msg[gdsupport] .= "</td></tr>";
                $msg[gdsupport] .= "<tr><td></td><td>&nbsp;&nbsp;&nbsp;Freetype Linkage</td><td><font color=\"blue\">".$gbsupport['FreeType Linkage']."</td></tr>";
                $msg[gdsupport] .= "<tr><td></td><td>&nbsp;&nbsp;&nbsp;Gif Read Support</td><td>";if($gbsupport['GIF Read Support']){$msg[gdsupport] .= "<font color=\"green\">OK";}else{$msg[gdsupport] .= "<font color=\"red\">FALSE";} $msg[gdsupport] .= "</td></tr>";
                $msg[gdsupport] .= "<tr><td></td><td>&nbsp;&nbsp;&nbsp;Gif Create Support</td><td>";if($gbsupport['GIF Create Support']){$msg[gdsupport] .= "<font color=\"green\">OK";}else{$msg[gdsupport] .= "<font color=\"red\">FALSE";} $msg[gdsupport] .= "</td></tr>";
                $msg[gdsupport] .= "<tr><td></td><td>&nbsp;&nbsp;&nbsp;JPG Support</td><td>"; if($gbsupport['JPG Support'] OR $gbsupport['JPEG Support']){$msg[gdsupport] .= "<font color=\"green\">OK";}else{$msg[gdsupport] .= "<font color=\"red\">FALSE";} $msg[gdsupport] .= "</td></tr>";
                $msg[gdsupport] .= "<tr><td></td><td>&nbsp;&nbsp;&nbsp;PNG Support</td><td>";if($gbsupport['PNG Support']){$msg[gdsupport] .= "<font color=\"green\">OK";}else{$msg[gdsupport] .= "<font color=\"red\">FALSE";} $msg[gdsupport] .= "</td></tr>";
        }else{
                $msg[func_gd] = "<font color=\"red\">FALSE";$msic[func_gd] = "3";$commit = 1;
        }
       # ImageMagick
        chdir($setup_path_project."/admin/install/");
		
        $cmd = "convert --version";
        $msg["func_imv"] = explode("\n",`$cmd`);
		$msg["func_imv"] = $msg["func_imv"][0];
        
        $cmd = "convert -auto-orient -thumbnail 'x30>' -gravity center -extent x30 ".
			$setup_path_project."/admin/install/test.jpg ".
			$setup_path_project."/TEMP/test.png";
	
        $func_im = `$cmd 2>/dev/null`;
		
        if(is_file($setup_path_project."/TEMP/test.png")){
			$msg['func_im'] = "<font color=\"blue\">".$msg['func_imv']."</FONT>";
			$msic[func_im] = "1";
		} else if($msg['func_imv']){
			$msg['func_im'] = "<font color=\"red\">FALSE</FONT> (version: ".
				$msg['func_imv'].")<br>V 6.3.x or higher needed!";
				$msic[func_im] = "4";
		} else{
			$msg['func_im'] = "<font color=\"red\">FALSE</FONT>";
			$msic[func_im] = "3";
		}
		
        if(file_exists($setup_path_project."/TEMP/test.png")){
			unlink($setup_path_project."/TEMP/test.png");
		}
		
        # Imap
        if(function_exists("imap_open")){$msg[func_imap] = "<font color=\"green\">OK";$msic[func_imap] = "1";}else{$msg[func_imap] = "<font color=\"red\">FALSE";$msic[func_imap] = "4";$commit = 2;}
        # gzip
        if(function_exists("gzopen")){$msg[func_zlib] = "<font color=\"green\">OK";$msic[func_zlib] = "1";}else{$msg[func_zlib] = "<font color=\"red\">FALSE";$msic[func_zlib] = "4";$commit = 2;}
        # MimeMagick
        if(function_exists("mime_content_type")){$msg[func_mime] = "<font color=\"green\">OK";$msic[func_mime] = "1";}else{$msg[func_mime] = "<font color=\"red\">FALSE";$msic[func_mime] = "4";$commit = 2;}

        # ZIP
        $out = exec("zip");
		if($out){$msg[func_zip] = "<font color=\"green\">OK";$msic[func_zip] = "1";}else{$msg[func_zip] = "<font color=\"red\">FALSE";$msic[func_zip] = "4";$commit = 2;}

        # htmldoc
        $cmd = "htmldoc --size 295x210mm --left 0mm --right 0mm --top 0mm --bottom 0mm --webpage --header ... --footer ... -f $setup_path_project/TEMP/test.pdf $setup_path_project/admin/install/test.html";
        exec($cmd);
        $cmd = "htmldoc --version";
        $msg["func_htmldoc"] = `$cmd`;
        if(is_file($setup_path_project."/TEMP/test.pdf")){$msg[func_htmldoc] = "<font color=\"blue\">version: ".$msg[func_htmldoc]."</FONT>";$msic[func_htmldoc] = "1";}else{$msg[func_htmldoc] = "<font color=\"red\">FALSE</FONT>";$msic[func_htmldoc] = "4";$commit = 2;}
        if(file_exists($setup_path_project."/TEMP/test.pdf")){unlink($setup_path_project."/TEMP/test.pdf");}

        # pdftotext
        $cmd = "pdftotext ".$setup_path_project."/admin/install/test.pdf ".$setup_path_project."/TEMP/test.txt";
        exec($cmd);
        if(is_file($setup_path_project."/TEMP/test.txt")){$msg[func_pdftotext] .= "<font color=\"green\">OK";$msic[func_pdftotext] = "1";}else{$msg[func_pdftotext] .= "<font color=\"red\">FALSE";$msic[func_pdftotext] = "4";$commit = 2;}
        if(file_exists($setup_path_project."/TEMP/test.txt")){unlink($setup_path_project."/TEMP/test.txt");}

        # pdftohtml
        $cmd = "pdftohtml ".$setup_path_project."/admin/install/test.pdf ".$setup_path_project."/TEMP/test.html";
        exec($cmd);
        if(is_file($setup_path_project."/TEMP/test.html")){$msg[func_pdftohtml] .= "<font color=\"green\">OK";$msic[func_pdftohtml] = "1";}else{$msg[func_pdftohtml] .= "<font color=\"red\">FALSE";$msic[func_pdftohtml] = "4";$commit = 2;}
        if(file_exists($setup_path_project."/TEMP/test.html")){unlink($setup_path_project."/TEMP/test.html");unlink($setup_path_project."/TEMP/tests.html");unlink($setup_path_project."/TEMP/test_ind.html");}

        # exiftool
        $cmd = "exiftool -ver";
        $exiftool = `$cmd`;
        $exiftoolVer = explode('.',$exiftool);
        if($exiftool AND $exiftoolVer[0] >= 9 ){$msg[func_exiftool] .= "<font color=\"blue\">$exiftool</font> <font color=\"green\">OK";$msic[func_exiftool] = "1";}else{$msg[func_exiftool] .= "<font color=\"red\">FALSE V.$exiftool < 9";$msic[func_exiftool] = "4";$commit = 2;}

        # ghostscript
        $cmd = "cd ".$setup_path_project."/admin/install/; gs -dNOPAUSE -dBATCH -sDEVICE=pdfwrite -sOutputFile=".$setup_path_project."/TEMP/test.pdf test.pdf";
        exec($cmd);
        if(is_file($setup_path_project."/TEMP/test.pdf")){$msg[func_ghost] .= "<font color=\"green\">OK";$msic[func_ghost] = "1";}else{$msg[func_ghost] .= "<font color=\"red\">FALSE";$msic[func_ghost] = "4";$commit = 2;}
        if(file_exists($setup_path_project."/TEMP/test.pdf")){unlink($setup_path_project."/TEMP/test.pdf");}

        # ttf2pt1
        chdir($setup_path_project."/TEMP");
		$cmd = "ttf2pt1 -a ".$setup_path_project."/admin/install/airmole.ttf airmole";
		exec($cmd);
        if(is_file($setup_path_project."/TEMP/airmole.afm") AND is_file($setup_path_project."/TEMP/airmole.t1a")){$msg[func_ttf2pt1] .= "<font color=\"green\">OK";$msic[func_ttf2pt1] = "1";}else{$msg[func_ttf2pt1] .= "<font color=\"red\">FALSE";$msic[func_ttf2pt1] = "4";$commit = 2;}
        if(file_exists($setup_path_project."/TEMP/airmole.afm")){unlink($setup_path_project."/TEMP/airmole.afm");}
        if(file_exists($setup_path_project."/TEMP/airmole.t1a")){unlink($setup_path_project."/TEMP/airmole.t1a");}

        # php-ini - func_ini_tags
        if(ini_get('short_open_tag')){$msg[func_ini_tags] .= "<font color=\"green\">On</font>";$msic[func_ini_tags] = "1";}else{$msg[func_ini_tags] .= "<font color=\"orange\">Off</font> .. it will be better to set <b>On</b>";$msic[func_ini_tags] = "2";}
        # php-ini - func_ini_globals
        if(ini_get('register_globals')){$msg[func_ini_globals] .= "<font color=\"orange\">On</font> .. it will be better to set <b>Off</b>";$msic[func_ini_globals] = "2";}else{$msg[func_ini_globals] .= "<font color=\"green\">Off";$msic[func_ini_globals] = "1";}
        # php-ini - func_ini_globals
        if(get_magic_quotes_gpc()){$msg[func_ini_magic_quotes] .= "<font color=\"orange\">On</font>  .. it will be better to set <b>Off</b>";$msic[func_ini_magic_quotes] = "2";}else{$msg[func_ini_magic_quotes] .= "<font color=\"green\">Off";$msic[func_ini_magic_quotes] = "1";}
        # php-ini - file_uploads
        if(ini_get('file_uploads')){$msg[func_ini_uploads] .= "<font color=\"green\">OK";$msic[func_ini_uploads] = "1";}else{$msg[func_ini_uploads] .= "<font color=\"orange\"><b>FALSE</b> .. for file uploads you need to set ON";$msic[func_ini_uploads] = "2";}
        # php-ini - func_ini_derr
        if(ini_get('display_errors')){$msg[func_ini_derr] .= "yes";}else{$msg[func_ini_derr] .= "<b>no</b>";}
        # php-ini - func_ini_lerr
        if(ini_get('log_errors')){$msg[func_ini_lerr] .= "yes";}else{$msg[func_ini_lerr] .= "<b>no</b>";}
        # php-ini - func_ini_upload
        if(ini_get('upload_max_filesize')){$msg[func_ini_upload] .= ini_get('upload_max_filesize');$msic[func_ini_upload] = "1";}else{$msg[func_ini_upload] .= "<font color=\"orange\"><b>FALSE</b>";$msic[func_ini_upload] = "2";}
        # php-ini - func_ini_post
        if(ini_get('post_max_size')){$msg[func_ini_post] .= ini_get('post_max_size');$msic[func_ini_post] = "1";}else{$msg[func_ini_post] .= "<font color=\"orange\"><b>FALSE</b>";$msic[func_ini_post] = "2";}
        # php-ini - max_input_vars
        if(ini_get('max_input_vars') >= 10000){$msg[func_input_vars] .= ini_get('max_input_vars');$msic[func_input_vars] = "1";}elseif(ini_get('max_input_vars') ){$msg[func_input_vars] .= "<font color=\"orange\"><b>".ini_get('max_input_vars')."</b> (>= 10000)";$msic[func_input_vars] = "2";}
        # php-ini - defaultlrl
        if(ini_get('odbc.defaultlrl')){$msg[func_ini_tlrl] .= ini_get('odbc.defaultlrl')." bytes";$msic[func_ini_tlrl] = "1";}else{$msg[func_ini_tlrl] .= "<font color=\"orange\"><b>FALSE</b>";$msic[func_ini_tlrl] = "2";}
        # php-ini - mbstring
        if(function_exists("mb_strlen")){
        	if(ini_get('mbstring.func_overload') == 7){$msg[func_ini_mbstring] .= "<font color=\"green\">OK";$msic[func_ini_mbstring] = "1";}else{$msg[func_ini_mbstring] .= "<font color=\"orange\"><b>Warning</b> .. mbstring present, but not enabled (need only for utf-8 instances)";$msic[func_ini_mbstring] = "2";$commit = 2;}
        }else{$msg[func_ini_mbstring] .= "<font color=\"orange\"><b>Warning</b> .. mbstring not present (need only for utf-8 instances)";$msic[func_ini_mbstring] = "2";$commit = 2;}

        
        
        if(function_exists("mb_strlen()"))
        mbstring.encoding_translation


        ?>

        <table style="margin:auto;" border="0" cellpadding="0" cellspacing="6"><tr><td valign="top">

        <table border="0" cellpadding="0" cellspacing="4" WIDTH="400">
        
        <tr><td COLSPAN="4"><B><U>Installation Details</U></B></td></tr>
        <tr><td COLSPAN="2">Installation Path</td><td><?php echo $setup_path_project?></td></tr>
        <tr><td COLSPAN="2">Database Name</td><td><?php echo $setup_database?></td></tr>
        <tr><td COLSPAN="2">Database User</td><td><?php echo $setup_dbuser?></td></tr>
        <tr><td COLSPAN="2">Database Host</td><td><?php echo $setup_host?></td></tr>
        <tr><td COLSPAN="2">Database Schema</td><td><?php echo $setup_dbschema?></td></tr>
        <tr><td COLSPAN="2">Database Port</td><td><?php echo $setup_dbport?></td></tr>
        <tr><td COLSPAN="4"><HR></td></tr>
        
        <tr><td COLSPAN="2"><B><U>Database</U></B></td><td><a href="http://www.limbas.org/wiki/Datenbank" target="new"><i class="lmb-icon lmb-help" border="0"></i></a></td></tr>
        <tr><td valign="top"><img src="status<?php echo $msic[db_create];?>.gif"></td><td>create table</td><td><?php echo $msg[db_create]; ?></td></tr>
        <tr><td valign="top"><img src="status<?php echo $msic[db_insert];?>.gif"></td><td>insert</td><td><?php echo $msg[db_insert]; ?></td></tr>
        <tr><td valign="top"><img src="status<?php echo $msic[db_select];?>.gif"></td><td>select</td><td><?php echo $msg[db_select]; ?></td></tr>
        <tr><td valign="top"><img src="status<?php echo $msic[db_delete];?>.gif"></td><td>delete</td><td><?php echo $msg[db_delete]; ?></td></tr>
        <tr><td valign="top"><img src="status<?php echo $msic[db_drop];?>.gif"></td><td>drop table</td><td><?php echo $msg[db_drop]; ?></td></tr>
        <tr><td COLSPAN="4"><HR></td></tr>

        <tr><td COLSPAN="2"><B><U>write permissions (recusive)</U></B></td><td><a href="http://www.limbas.org/wiki/LIMBAS" target="new"><i class="lmb-icon lmb-help" border="0"></i></a></td></tr>
        <tr><td valign="top"><img src="status<?php echo $msic[func_incdb];?>.gif"></td><td valign="top">inc/include_db</td><td valign="top"><?php echo $msg[func_incdb]; ?></td></tr>
        <tr><td valign="top"><img src="status<?php echo $msic[func_wr_backup];?>.gif"></td><td valign="top">BACKUP</td><td valign="top"><?php echo $msg[func_wr_backup]; ?></td></tr>
        <tr><td valign="top"><img src="status<?php echo $msic[func_wr_temp];?>.gif"></td><td valign="top">TEMP</td><td valign="top"><?php echo $msg[func_wr_temp]; ?></td></tr>
        <tr><td valign="top"><img src="status<?php echo $msic[func_wr_upload];?>.gif"></td><td valign="top">UPLOAD</td><td valign="top"><?php echo $msg[func_wr_upload]; ?></td></tr>
        <tr><td valign="top"><img src="status<?php echo $msic[func_wr_user];?>.gif"></td><td valign="top">USER</td><td valign="top"><?php echo $msg[func_wr_user]; ?></td></tr>
        <tr><td COLSPAN="4"><HR></td></tr>

        <tr><td COLSPAN="2"><B><U>php.ini</U></B></td><td><a href="http://www.limbas.org/wiki/-OpenSuse#php.ini" target="new"><i class="lmb-icon lmb-help" border="0"></i></a></td></tr>
        <tr><td valign="top"><img src="status<?php echo $msic[func_ini_tags];?>.gif"></td><td valign="top">short_open_tag</td><td valign="top"><?php echo $msg[func_ini_tags];?></td></tr>
        <tr><td valign="top"><img src="status<?php echo $msic[func_ini_globals];?>.gif"></td><td valign="top">register_globals</td><td valign="top"><?php echo $msg[func_ini_globals]; ?></td></tr>
        <tr><td valign="top"><img src="status<?php echo $msic[func_ini_magic_quotes];?>.gif"></td><td valign="top">magic_quotes</td><td valign="top"><?php echo $msg[func_ini_magic_quotes]; ?></td></tr>
        <tr><td valign="top"><img src="status<?php echo $msic[func_ini_mbstring];?>.gif"></td><td valign="top">mb_string</td><td valign="top"><?php echo $msg[func_ini_mbstring]; ?></td></tr>
        <tr><td valign="top"><img src="status<?php echo $msic[func_ini_uploads];?>.gif"></td><td valign="top">file_uploads</td><td valign="top"><?php echo $msg[func_ini_uploads]; ?></td></tr>
        <tr><td valign="top"><img src="status<?php echo $msic[func_ini_upload];?>.gif"></td><td valign="top">upload_max_filesize</td><td valign="top"><?php echo $msg[func_ini_upload]; ?></td></tr>
        <tr><td valign="top"><img src="status<?php echo $msic[func_ini_post];?>.gif"></td><td valign="top">post_max_size</td><td valign="top"><?php echo $msg[func_ini_post]; ?></td></tr>
        <tr><td valign="top"><img src="status<?php echo $msic[func_input_vars];?>.gif"></td><td valign="top">max_input_vars</td><td valign="top"><?php echo $msg[func_input_vars]; ?></td></tr>
        <tr><td valign="top"><img src="status<?php echo $msic[func_ini_tlrl];?>.gif"></td><td valign="top">odbc.defaultlrl</td><td valign="top"><?php echo $msg[func_ini_tlrl]; ?></td></tr>
		<tr><td valign="top"></td><td valign="top">display_errors</td><td valign="top"><?php echo $msg[func_ini_derr]; ?></td></tr>
		<tr><td valign="top"></td><td valign="top">log_errors</td><td valign="top"><?php echo $msg[func_ini_lerr]; ?></td></tr>
        </table>

        </td><td>&nbsp;</td><td valign="top">

        <table border="0" cellpadding="0" cellspacing="4" WIDTH="400">
        <tr><td COLSPAN="2"><B><U>Functions</U></B></td><td><a href="http://www.limbas.org/wiki/Tools" target="new"><i class="lmb-icon lmb-help" border="0"></i></a></td></tr>
        <tr><td valign="top"><img src="status<?php echo $msic[func_path];?>.gif"></td><td width="130">path</td><td><?php echo $msg[func_path]; ?></td></tr>
        <tr><td valign="top"><img src="status<?php echo $msic[func_gd];?>.gif"></td><td valign="top">gdlib</td><td valign="top"><?php echo $msg[func_gd]; ?></td></tr>
        <?php echo $msg[gdsupport]; ?>
        <tr><td valign="top"><img src="status<?php echo $msic[func_im];?>.gif"></td><td valign="top">ImageMagick</td><td><?php echo $msg[func_im]; ?></td></tr>
        <tr><td valign="top"><img src="status<?php echo $msic[func_imap];?>.gif"></td><td valign="top">Imap</td><td valign="top"><?php echo $msg[func_imap]; ?></td></tr>
        <tr><td valign="top"><img src="status<?php echo $msic[func_zip];?>.gif"></td><td valign="top">zip</td><td valign="top"><?php echo $msg[func_zip]; ?></td></tr>
        <tr><td valign="top"><img src="status<?php echo $msic[func_zlib];?>.gif"></td><td valign="top">Zlib</td><td valign="top"><?php echo $msg[func_zlib]; ?></td></tr>
        <tr><td valign="top"><img src="status<?php echo $msic[func_mime];?>.gif"></td><td valign="top">MimeMagic</td><td valign="top"><?php echo $msg[func_mime]; ?></td></tr>
        <tr><td valign="top"><img src="status<?php echo $msic[func_htmldoc];?>.gif"></td><td valign="top">htmldoc</td><td><?php echo $msg[func_htmldoc]; ?></td></tr>
        <tr><td valign="top"><img src="status<?php echo $msic[func_pdftotext];?>.gif"></td><td valign="top">pdftotext (Xpdf)</td><td valign="top"><?php echo $msg[func_pdftotext]; ?></td></tr>
        <tr><td valign="top"><img src="status<?php echo $msic[func_pdftohtml];?>.gif"></td><td valign="top">pdftohtml</td><td valign="top"><?php echo $msg[func_pdftohtml]; ?></td></tr>
        <tr><td valign="top"><img src="status<?php echo $msic[func_ghost];?>.gif"></td><td valign="top">ghostscript</td><td valign="top"><?php echo $msg[func_ghost]; ?></td></tr>
        <tr><td valign="top"><img src="status<?php echo $msic[func_exiftool];?>.gif"></td><td valign="top">exiftool</td><td valign="top"><?php echo $msg[func_exiftool]; ?></td></tr>
        <tr><td valign="top"><img src="status<?php echo $msic[func_ttf2pt1];?>.gif"></td><td valign="top">ttf2pt1</td><td valign="top"><?php echo $msg[func_ttf2pt1]; ?></td></tr>
        <tr><td COLSPAN="4">&nbsp;</td></tr>
        </table>

        </td></tr>
        <tr><td COLSPAN="4"><HR></td></tr>
        <tr><td COLSPAN="4">

        <table border="0" cellpadding="0" cellspacing="4">
<?php
		if(!$commit OR $commit == 2){
			echo "<tr><td><B>Install a new Instance:&nbsp;&nbsp;</B></td><td colspan=\"2\">\n";
			
			unset($pfad);
			if($folderval = read_dir($setup_path_project."/BACKUP")){
				foreach($folderval["name"] as $key => $value){
					$pfad[] = $value;
				}
			}
			
			if (count($pfad)){
				rsort($pfad);
				echo "<select name=\"backupdir\">\n";
				foreach ($pfad as $_tmp){
					if(strpos($_tmp,"tar.gz")){
						echo "<option value=\"".urlencode($_tmp)."\">{$_tmp}</option>\n";
					}
				}
				echo "</select>&nbsp;&nbsp;<input type=\"submit\" name=\"install\" value=\"install\">";
			}
			
	
        	echo "</td></tr>";

        	if($commit == 2){
				if (is_resource($cdir)){
					echo "<tr><td colspan=\"3\" bgcolor=\"orange\"><B>Some usefull functions or tools are not present, you can continue and install them later!</td></tr>";
				}else{
					echo "<tr><td colspan=\"3\" bgcolor=\"orange\"><B>No instances found in BACKUP directory!</td></tr>";
				}
			}
        } else {
			echo "<tr><td COLSPAN=\"3\" bgcolor=\"orange\"><B>Some necessary functions are not present for continue!</td></tr>";
        }
        echo "<tr><td COLSPAN=\"3\">
			<img src=\"status1.gif\">&nbsp;is ok<br>
			<img src=\"status2.gif\">&nbsp;is ok, but will be better to change<br>
			<img src=\"status4.gif\">&nbsp;function or tool do not work or exist, you can install later<br>
			<img src=\"status3.gif\">&nbsp;is necessary, you can not continue before this function work!<br>
			</td></tr>";
?>

        </table>
        </td></tr></table>

        <input type="hidden" name="setup_host" value="<?php echo $setup_host; ?>">
        <input type="hidden" name="setup_database" value="<?php echo $setup_database; ?>">
        <input type="hidden" name="setup_dbuser" value="<?php echo $setup_dbuser; ?>">
        <input type="hidden" name="setup_dbpass" value="<?php echo $setup_dbpass; ?>">
        <input type="hidden" name="setup_path_images" value="<?php echo $setup_path_images; ?>">
        <input type="hidden" name="setup_path_imageurl" value="<?php echo $setup_path_imageurl; ?>">
        <input type="hidden" name="setup_path_pdf" value="<?php echo $setup_path_pdf; ?>">
        <input type="hidden" name="setup_path_temp" value="<?php echo $setup_path_temp; ?>">
        <input type="hidden" name="setup_path_upload" value="<?php echo $setup_path_upload; ?>">
        <input type="hidden" name="setup_path_project" value="<?php echo $setup_path_project; ?>">
        <input type="hidden" name="setup_company" value="<?php echo $setup_company; ?>">
        <input type="hidden" name="setup_mailto" value="<?php echo $setup_mailto; ?>">
        <input type="hidden" name="setup_mailfrom" value="<?php echo $setup_mailfrom; ?>">
        <input type="hidden" name="setup_font" value="<?php echo $setup_font; ?>">
        <input type="hidden" name="setup_fontsize" value="<?php echo $setup_fontsize; ?>">
        <input type="hidden" name="setup_memolength" value="<?php echo $setup_memolength; ?>">
        <input type="hidden" name="setup_session_length" value="<?php echo $setup_session_length; ?>">
        <input type="hidden" name="setup_cookie_length" value="<?php echo $setup_cookie_length; ?>">
        <input type="hidden" name="DBA[DB]" value="<?php echo $DBA["DB"]; ?>">
        <input type="hidden" name="setup_language" value="<?php echo $setup_language; ?>">
        <input type="hidden" name="setup_dbschema" value="<?php echo $setup_dbschema; ?>">
        <input type="hidden" name="setup_dbdriver" value="<?php echo $setup_dbdriver; ?>">
        <input type="hidden" name="setup_dbport" value="<?php echo $setup_dbport; ?>">
        <input type="hidden" name="setup_charset" value="<?php echo $setup_charset; ?>">
        <input type="hidden" name="setup_dateformat" value="<?php echo $setup_dateformat; ?>">
        <input type="hidden" name="action" value="1">
        </form>

<?php
	}elseif($install == "install"){
		
		$session["setlocale"] = "de_DE";
		$session["timezone"] = "Europe/Berlin";
		$umgvar["use_datetimeclass"] = 1;
		
		require("../../lib/db/db_".$DBA["DB"].".lib");
		require("../../lib/db/db_".$DBA["DB"]."_admin.lib");
		require("../../lib/include.lib");
		require("../../lib/include_admin.lib");
		require("../../lib/include_DateTime.lib");

		setlocale(LC_ALL, $session["setlocale"]);
		date_default_timezone_set($session["timezone"]);

		# database spec
		if(!$setup_dbschema){
			if(substr($DBA["DB"],0,5) == "maxdb"){$DBA["DBSCHEMA"] = strtoupper($setup_dbuser);}
			elseif($DBA["DB"] == "postgres"){$DBA["DBSCHEMA"] = "public";}
			elseif($DBA["DB"] == "ingres"){$DBA["DBSCHEMA"] = "ingres";}
			elseif($DBA["DB"] == "mssql"){$DBA["DBSCHEMA"] = "dbo";}
		}else{
			$DBA["DBSCHEMA"] = $setup_dbschema;
		}
		$DBA["DBNAME"] = $setup_database;

        $db = dbq_0($setup_host,$setup_database,$setup_dbuser,$setup_dbpass,$setup_dbdriver,$setup_dbport);
        $setup = 1;

        /* --- default tabimport ------------------------------------------------------ */
        unset($commit);
        $GLOBALS["umgvar"]["pfad"] = $setup_path_project;
        
        require_once("../tools/import.dao");

        if($setup_charset == 'UTF-8'){
			$umgvar["charset"] = $setup_charset;
        	$txt_encode = 1;
        }
		import_complete(1,$txt_encode);

        /* --- update umgvar ------------------------------------------------------ */
        $setup_version = parse_db_int(dbf_version());
        
        if($setup_company){
        	$sqlquery = "UPDATE LMB_UMGVAR SET NORM = '$setup_company' WHERE FORM_NAME = 'company'";
        	$rs = odbc_exec($db,$sqlquery) or errorhandle(odbc_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
        }
        if($setup_path_project){
        	$sqlquery = "UPDATE LMB_UMGVAR SET NORM = '".parse_db_string($setup_path_project)."' WHERE FORM_NAME = 'path'";
        	$rs = odbc_exec($db,$sqlquery) or errorhandle(odbc_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
        	$sqlquery = "UPDATE LMB_UMGVAR SET NORM = 'localhost:///".$setup_path_project."/BACKUP' WHERE FORM_NAME = 'backup_default'";
        	$rs = odbc_exec($db,$sqlquery) or errorhandle(odbc_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
        }
        if(is_numeric($setup_language)){
        	$sqlquery = "UPDATE LMB_UMGVAR SET NORM = '$setup_language' WHERE FORM_NAME = 'default_language'";
        	$rs = odbc_exec($db,$sqlquery) or errorhandle(odbc_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
        	$sqlquery = "UPDATE LMB_USERDB SET LANGUAGE = ".$setup_language;
        	$rs = odbc_exec($db,$sqlquery) or errorhandle(odbc_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
        }
        if(is_numeric($setup_dateformat)){
        	$sqlquery = "UPDATE LMB_UMGVAR SET NORM = '$setup_dateformat' WHERE FORM_NAME = 'default_dateformat'";
        	$rs = odbc_exec($db,$sqlquery) or errorhandle(odbc_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
        	$sqlquery = "UPDATE LMB_USERDB SET DATEFORMAT = ".$setup_dateformat;
        	$rs = odbc_exec($db,$sqlquery) or errorhandle(odbc_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
        }
        if($setup_charset){
        	$sqlquery = "UPDATE LMB_UMGVAR SET NORM = '$setup_charset' WHERE FORM_NAME = 'charset'";
        	$rs = odbc_exec($db,$sqlquery) or errorhandle(odbc_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
        }
        
        
        
        #$defaulturl = $SERVER_NAME.$REQUEST_URI;
        #$defaulturl = str_replace("admin/install/index.php","",$defaulturl);
        #$defaulturl = "http://".$defaulturl;
        
		$defaulturl = dirname(dirname(dirname($_SERVER['PHP_SELF'])));
		$defaulturl = "http://{$_SERVER['SERVER_NAME']}{$defaulturl}/";

        $sqlquery = "UPDATE LMB_UMGVAR SET NORM = '$defaulturl' WHERE FORM_NAME = 'url'";
        $rs = odbc_exec($db,$sqlquery) or errorhandle(odbc_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);


		# --- update include_db.lib ----------------------------
		$dblibvalue = fopen($setup_path_project."/inc/include_db.lib","w+");

$line=<<<EOD
<?php
/*
Copyright notice
(c) 1998-2016 Axel Westhagen (support@limbas.org)
All rights reserved
This script is part of the LIMBAS project. The LIMBAS project is free software;
you can redistribute it and/or modify it on 2 Ways:
Under the terms of the GNU General Public License as published by the
Free Software Foundation; either version 2 of the License, or (at your option) 
any later version. Or in a proprietary software license http://limbas.com/
The GNU General Public License can be found at
http://www.gnu.org/copyleft/gpl.html. A copy is found in the textfile GPL.txt
and important notices to the license from the author is found in LICENSE.txt 
distributed with these scripts.
This script is distributed WITHOUT ANY WARRANTY; without even the implied
warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU 
General Public License for more details.
This copyright notice MUST APPEAR in all copies of the script!
*/

\$DBA["DB"] = '{$DBA['DB']}';				/* maxdb76 | masbd77 | postgres | ingres */
\$DBA["DBCUSER"] = '$setup_dbuser'; 		/* DB control user */
\$DBA["DBCPASS"] = '$setup_dbpass'; 		/* DB control password */
\$DBA["DBCNAME"] = '$setup_database'; 		/* DB control name */
	
\$DBA["DBUSER"] = "$setup_dbuser";			/* DB username */
\$DBA["DBPASS"] = "$setup_dbpass";			/* DB password */
\$DBA["DBNAME"] = "$setup_database";		/* DB instance name */
\$DBA["DBSCHEMA"] = "$setup_dbschema";		/* DB schema */
\$DBA["DBHOST"] = "$setup_host";			/* DB hostname or IP */
\$DBA["LMHOST"] = "$setup_host";			/* LIMBAS hostname or IP */
\$DBA["DBPATH"] = "/opt/sdb/programs/bin";	/* Path to database */
\$DBA["LMPATH"] = "$setup_path_project";	/* Path to LIMBAS */
\$DBA["ODBCDRIVER"] = "$setup_dbdriver";	/* unixODBC Driver */
\$DBA["PORT"] = "$setup_dbport";			/* database Port */
\$DBA["VERSION"] = "$setup_version";		/* database version */

require_once("{\$DBA['LMPATH']}/lib/db/db_{\$DBA['DB']}.lib");
?>
EOD;

		fputs($dblibvalue,$line);
		fclose($dblibvalue);
		
		
		
		/* --- DB-CLOSE ------------------------------------------------------ */
		if ($db){odbc_close($db);}
		
		
		
}else{
        /* --- new SAPDB ------------------------------------------- */
       # if($install_sapdb == "create db"){
       # 	$sys = $setup_path_project."/admin/lib/sapdbadmin.sh --create $install_database $install_host $install_dbuser $install_dbpass $install_dbmuser $install_dbmpass $install_dbauser $install_dbapass $install_sapdbpath $install_datapath";
       # }

		$pt = isset($_SERVER['PATH_TRANSLATED']) ? $_SERVER['PATH_TRANSLATED'] : $_SERVER['SCRIPT_FILENAME'];

        $path = explode("/", $pt);
        array_pop($path);
        array_pop($path);
        array_pop($path);
        $path = implode("/",$path);
?>
       <div class="setup_page">
		<H1>LIMBAS Installation</H1>
		<HR>
        <table width="100%" border="0">
        <tr><td COLSPAN="2" align="left"><B><U>Use existing Database</U></B></td></tr>
        <tr><td>Database Vendor</td>
			<td>
				<select name="DBA[DB]" id="db_vendor" onchange="update_dbvendor(this.value);">
					<option value="postgres" selected="selected">PostgreSQL</option>
					<option value="mysql">mysql</option>
					<option value="maxdb76">MaxDB V7.6</option>
					<option value="mssql">MSSQL</option>
					<option value="ingres">Ingres 10</option>
				</select>
			</td>
		</tr>
        <tr><td>Database Host:</td><td><input type="text" autocomplete="off" value="localhost" name="setup_host"></td></tr>
        <tr><td>Database Name:</td><td><input type="text" autocomplete="off" value="limbas" name="setup_database" id="db_name"></td></tr>
        <tr><td>Database User:</td><td><input type="text" autocomplete="off" name="setup_dbuser" onchange="update_dbschema(this.value);" value="limbasuser"></td></tr>
        <tr><td>Database Password:</td><td><input type="password" autocomplete="off" name="setup_dbpass"></td></tr>
        <tr><td>Database Schema:</td><td><input type="text" autocomplete="off" name="setup_dbschema" value="public" id="db_schema"></td></tr>
        <tr><td>Database Port:</td><td><input type="text" autocomplete="off" name="setup_dbport" id="db_port"></td></tr>
        <tr><td>SQL Driver (unixODBC):</td><td><input type="text" autocomplete="off" name="setup_dbdriver" id="db_driver" value="PSQL"></td></tr>

        <tr><td colspan="2" align="center"><HR></td></tr>
        <tr><td align="left"><B><U>Path</U></B></td></tr>
        <tr><td>Project:</td><td><input type="text" autocomplete="off" value="<?php echo $path?>" name="setup_path_project" SIZE="50"></td></tr>
        <tr><td colspan="2" align="center"><HR></td></tr>
        <tr><td align="left"><B><U>Options</U></B></td></tr>
        <tr><td>Language:</td><td><SELECT name="setup_language"><OPTION value="1">deutsch</OPTION><OPTION value="2">english</OPTION></SELECT></td></tr>
        <tr><td>Dateformat:</td><td><SELECT name="setup_dateformat"><OPTION value="1">deutsch</OPTION><OPTION value="2">english</OPTION><OPTION value="3">us</OPTION></SELECT></td></tr>
        <tr><td>charset:</td><td><SELECT name="setup_charset"></OPTION><OPTION value="ISO-8859-1">LATIN1</OPTION><OPTION value="UTF-8">UTF-8</SELECT></td></tr>
        <tr><td>Company:</td><td><input type="text" autocomplete="off" value="your company" name="setup_company" SIZE="50"></td></tr>
                
        <tr><td colspan="2"><HR></td></tr>
        <tr><td colspan="2" align="center"><input type="submit" name="install" value="check"></td></tr>
        </table>
       </div>
        </form>
<?php
	}
?>

</body>
</html>