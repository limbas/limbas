<?php
/*
 * Copyright notice
 * (c) 1998-2021 Limbas GmbH (support@limbas.org)
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
 * ID:
 */

define("IS_REST",1);

# ------- Limbas include Dateien --------
require_once("lib/db/db_wrapper.lib");
require_once("lib/include.lib");
require_once("lib/session.lib");
require_once("extra/explorer/metadata.lib");
require_once('extra/rest/server.php');

if ($db) {lmbdb_close($db);}