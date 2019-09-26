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


require("lib/include.lib");


function call_soap(){

	# general
	$lmpar[0]["getvars"] = array('greminder');		# list of avilable reminders
	$lmpar[0]["action"] = "reminder";				# action = reminder
	
	# get reminder
	$lmpar[0]['getReminder']['gtabid'] = null;		# if set table-id, only default reminder is used
	$lmpar[0]['getReminder']['category'] = null;	# if set only specific reminder is used
	$lmpar[0]['getReminder']['ID'] = null;			# filter for dataset ID
	$lmpar[0]['getReminder']['wfl_inst'] = null;	# filter for workflow instance ID
	$lmpar[0]['getReminder']['active'] = null;		# filter for active reminders
	
	# add reminder
	$lmpar[0]["addReminder"]['date'] = null;		# date when reminder is aktive
	$lmpar[0]["addReminder"]['gtabid'] = null;		# table-id
	$lmpar[0]["addReminder"]['ID'] = null;			# dataset-id
	$lmpar[0]['addReminder']['category'] = null;	# reminder-id - if not set default reminder is used
	$lmpar[0]["addReminder"]['to_user'][] = null;	# recipients of reminder - user-id
	$lmpar[0]["addReminder"]['to_group'][] = null;	# recipients of reminder - group-id
	$lmpar[0]["addReminder"]['wfl_inst'] = null;	# workflow instance
	$lmpar[0]["addReminder"]['content'] = null;		# content
	$lmpar[0]["addReminder"]['desc'] = null;		# description
		
	# drop reminder
	$lmpar[0]["dropReminder"]['ID'] = null;			# reminder-id
	$lmpar[0]["dropReminder"]['gtabid'] = null;		# table-id
	$lmpar[0]["dropReminder"]['dat_id'] = null;		# dataset-id
	$lmpar[0]["dropReminder"]['category'] = null;	# reminder-id 
	$lmpar[0]["dropReminder"]['wfl_inst'] = null;	# workflow instance
	$lmpar[0]['dropReminder']['active'] = null;		# only active reminders
	$lmpar[0]['dropReminder']['extension'] = null;	# only Extension

	$GLOBALS["lmpar"] = $lmpar;
	return call_client($lmpar);

}


$lmb = call_soap();

echo "<pre>";
print_r($lmb);


?>