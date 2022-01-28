<?php
/*
 * Copyright notice
 * (c) 1998-2021 Limbas GmbH(support@limbas.org)
 * All rights reserved
 * This script is part of the LIMBAS project. The LIMBAS project is free software; you can redistribute it and/or modify it on 2 Ways:
 * Under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * Or
 * In a Propritary Software Licence http://limbas.org
 * The GNU General Public License can be found at http://www.gnu.org/copyleft/gpl.html.
 * A copy is found in the textfile GPL.txt and important notices to the license from the author is found in LICENSE.txt distributed with these scripts.
 * This script is distributed WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * This copyright notice MUST APPEAR in all copies of the script!
 * Version 4.3.36.1319
 */

/*
 * ID: 93
 */

global $umgvar, $session, $lang;
require_once("extra/report/report_pdf.lib");


$generatedReport = lmb_reportPDF($report_id,$gtabid,$ID,null,$report_name,$gsr,$filter,$use_record,$filter_relation);

#$gsr_[$gtabid][1][0] = 'test3-2';
#lmb_reportCreatePDFtoDMS(5,null,null,null,'supertest',null,$gsr_);


if($report_output != 2 AND $report_output != 5 AND !$params){
    view_report($generatedReport);
}
