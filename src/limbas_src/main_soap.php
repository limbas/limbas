<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */



define("IS_SOAP",true);
ini_set("session.use_only_cookies",0);
# ------- Limbas include Dateien --------
require_once(__DIR__ . '/lib/session.lib');
require_once(COREPATH . 'extra/lmbObject/log/LimbasLogger.php');
require_once(COREPATH . 'extra/explorer/metadata.lib');
require_once(COREPATH . 'extra/soap/server.php');


if ($db) {lmbdb_close($db);}
