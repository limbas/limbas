<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */



require_once(COREPATH . 'gtab/gtab.lib');
require_once(COREPATH . 'gtab/gtab_type_erg.lib');
require_once(COREPATH . 'lib/include.lib');
$use_soap = 0;
require_once(COREPATH . 'extra/soap/soap.lib');

require_once(COREPATH . 'extra/lmbObject/log/LimbasLogger.php');

spl_autoload_register(function ($className) {
//	echo 'spl_autoload ' . $className . "\n";
	LimbasLogger::trace('spl_autoload ' . $className);
	if (file_exists('extra/lmbObject/base/' . $className . '.php')) {
		require_once COREPATH . 'extra/lmbObject/base/' . $className . '.php';
		return true;
	}
	elseif (file_exists(COREPATH . 'extra/lmbObject/db/' . $className . '.php')) {
		require_once COREPATH . 'extra/lmbObject/db/' . $className . '.php';
		return true;
	}
	elseif (file_exists(COREPATH . 'extra/lmbObject/log/' . $className . '.php')) {
		require_once COREPATH . 'extra/lmbObject/log/' . $className . '.php';
		return true;
	}
	elseif (file_exists(EXTENSIONSPATH . 'lmbObject/' . $className . '.php')) {
		require_once EXTENSIONSPATH . 'lmbObject/' . $className . '.php';
		return true;
	}
	return false;
});

	
