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

require_once('gtab/gtab.lib');
require_once("gtab/gtab_type_erg.lib");
require_once("lib/include.lib");
$use_soap = 0;
require_once('extra/soap/soap.lib');

require_once 'extra/lmbObject/log/LimbasLogger.php';

spl_autoload_register(function ($className) {
//	echo 'spl_autoload ' . $className . "\n";
	LimbasLogger::trace('spl_autoload ' . $className);
	if (file_exists('extra/lmbObject/base/' . $className . '.php')) {
		require_once 'extra/lmbObject/base/' . $className . '.php';
		return true;
	}
	elseif (file_exists('extra/lmbObject/db/' . $className . '.php')) {
		require_once 'extra/lmbObject/db/' . $className . '.php';
		return true;
	}
	elseif (file_exists('extra/lmbObject/log/' . $className . '.php')) {
		require_once 'extra/lmbObject/log/' . $className . '.php';
		return true;
	}
	elseif (file_exists('../dependent/EXTENSIONS/lmbObject/' . $className . '.php')) {
		require_once '../dependent/EXTENSIONS/lmbObject/' . $className . '.php';
		return true;
	}
	return false;
});

	