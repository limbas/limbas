<?php
/*
 * Copyright notice
 * (c) 1998-2018 Limbas GmbH(support@limbas.org)
 * All rights reserved
 * This script is part of the LIMBAS project. The LIMBAS project is free software; you can redistribute it and/or modify it on 2 Ways:
 * Under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * Or
 * In a Propritary Software Licence http://limbas.org
 * The GNU General Public License can be found at http://www.gnu.org/copyleft/gpl.html.
 * A copy is found in the textfile GPL.txt and important notices to the license from the author is found in LICENSE.txt distributed with these scripts.
 * This script is distributed WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * This copyright notice MUST APPEAR in all copies of the script!
 * Version 3.5
 */

/*
 * ID:
 */

# generate xml report

$report_xml = 1;
require_once("extra/report/report_xml.php");

$sqlquery = "SELECT ODT_TEMPLATE,NAME FROM LMB_REPORT_LIST WHERE ID = ".parse_db_int($report_id);
$rs = odbc_exec($db,$sqlquery) or errorhandle(odbc_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
if($ootemplateid = odbc_result($rs,"ODT_TEMPLATE")){
	$sqlquery1 = "SELECT DISTINCT LDMS_FILES.NAME,LDMS_FILES.SECNAME,LMB_MIMETYPES.EXT FROM LDMS_FILES,LMB_MIMETYPES WHERE LDMS_FILES.ID = ".parse_db_int($ootemplateid)." AND LDMS_FILES.MIMETYPE = LMB_MIMETYPES.ID";
	$rs1 = odbc_exec($db,$sqlquery1) or errorhandle(odbc_errormsg($db),$sqlquery1,$action,__FILE__,__LINE__);
	$ootemplate = odbc_result($rs1,"SECNAME").".".odbc_result($rs1,"EXT");
	if(is_file($umgvar["pfad"]."/UPLOAD/".$ootemplate)){
		$ootemplatename = odbc_result($rs1,"NAME");
		$ootemplatepath = $umgvar["pfad"]."/USER/".$session["user_id"]."/temp/".$ootemplatename;
		copy($umgvar["pfad"]."/UPLOAD/".$ootemplate,$ootemplatepath);
		if(!file_exists($ootemplatepath)){
			echo "no Open Office Template found!";
			return false;
		}
	}
}

# $ootemplatepath
$tempdir = $umgvar["pfad"]."/USER/".$session["user_id"]."/temp/";
$preliminary = $generatedReport;

$dtdpath = $umgvar["pfad"]."/extra/report/ooReport/xml";

define('PATHSEP', '/');

$pr = "extra/report/ooReport/";
$pm = "extra/messages/classes/php/";

require_once("{$pm}olTemplate.class.php");
require_once("{$pr}olODFContainer.class.php");
require_once("{$pr}olODFPicture.class.php");
require_once("{$pr}olODFTemplate.class.php");
require_once("{$pr}xml/olXMLParser.class.php");

$container = new olODFContainer();
$container->tempdir = $tempdir;
$container->docdir = $tempdir."doc/";

$template = new olODFTemplate($container);
$parser = new olXMLParser($preliminary, $template);

/* fill the ODT file with XML data. */
$parser->parse();

/* update document content and download document. */
if ($template->save()){
	
	$reportname_ = reportSavename($report_name,$report_savename,$ID,$report_medium,$report_rename);
	
	# Buffer
	ob_start();
    if (!$container->download($reportname_)){
        echo "ERROR: failed downloading {$container->name}";
        ob_end_flush();
	}else{
		$output = ob_get_contents();
		ob_end_clean();
		$generatedReport = $tempdir.$reportname_;
		file_put_contents($generatedReport,$output);
		# reset basepath
		chdir($umgvar["pfad"]);
		reportArchiv($generatedReport,$report,$ID,$report_output);
	}
} else
    echo "ERROR: failed updating content! (no write permissions for ".
        "{$container->tempdir} folder?)";

?>