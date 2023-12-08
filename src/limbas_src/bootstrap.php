<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */
const COREPATH = __DIR__ . '/';

define('PUBLICPATH', realpath(COREPATH . '../public/').'/');

const VENDORPATH = __DIR__ . '/../vendor/';

const LEGACYPATH = COREPATH . 'vlegacy/';

define("DEPENDENTPATH", realpath(COREPATH . '../dependent/').'/');

const ASSETSPATH = PUBLICPATH . 'assets/';

const LOCALASSETSPATH = PUBLICPATH . 'localassets/';

const TEMPPATH = DEPENDENTPATH . 'TEMP/';

const UPLOADPATH = DEPENDENTPATH . 'UPLOAD/';

const BACKUPPATH = DEPENDENTPATH . 'BACKUP/';

const EXTENSIONSPATH = DEPENDENTPATH . 'EXTENSIONS/';

const USERPATH = DEPENDENTPATH . 'USER/';

require_once(VENDORPATH . 'autoload.php');

require_once(COREPATH . 'autoload.php');





// require dependencies
if (!defined('LIMBAS_INSTALL') AND !defined('IS_CRON')) {
    require_once COREPATH . 'layout/Layout.php';
}
