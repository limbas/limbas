<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */

//TODO: Autoloader

require_once(COREPATH . 'gtab/gtab.lib');
require_once(COREPATH . 'gtab/gtab_type_erg.lib');
require_once(COREPATH . 'gtab/gtab_type_update.lib');
require_once(COREPATH . 'lib/include.lib');
require_once(COREPATH . 'lib/include_admin.lib');

require_once(COREPATH . 'extra/lmbObject/log/LimbasLogger.php');
require_once(COREPATH . 'admin/tools/datasync/socket/Socket.php');
require_once(COREPATH . 'admin/tools/datasync/socket/Factory.php');


require_once(COREPATH . 'admin/tools/datasync/DatasyncClient.php');

require_once(COREPATH . 'admin/tools/datasync/filesync.lib');
require_once(COREPATH . 'admin/tools/datasync/Datasync.php');
require_once(COREPATH . 'admin/tools/datasync/DatasyncClientProcess.php');
require_once(COREPATH . 'admin/tools/datasync/DatasyncMain.php');
require_once(COREPATH . 'admin/tools/datasync/DatasyncProcess.php');

