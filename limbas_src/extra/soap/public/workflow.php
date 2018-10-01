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


require("lib/include.lib");


function call_soap(){
	
	# general
	$lmpar[0]["getvars"] = array('workflow');	# list of avilable workflows
	$lmpar[0]["action"] = "workflow";			# action = reminder
	
	# run task
	$lmpar[0]['workflow']['wfl_id'] = null;		# workflow ID
	$lmpar[0]['workflow']['wfl_inst'] = null;	# if set specific workflow instance is used - else limbas try to get the instance out of the dataset (gtabid and ID needed)
	$lmpar[0]['workflow']['wfl_task'] = null;	# task to run
	$lmpar[0]['workflow']['gtabid'] = null;		# table ID
	$lmpar[0]['workflow']['ID'] = null;			# dataset ID
	$lmpar[0]['workflow']['wfl_params'] = null;	# array of specific userdefined parameters

	$GLOBALS["lmpar"] = $lmpar;
	return call_client($lmpar);

}


$lmb = call_soap();

echo "<pre>";
print_r($lmb);


?>