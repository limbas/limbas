<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */
require_once COREPATH . 'lib/include_admin.lib';

use Limbas\admin\tools\update\Updater;

function dyns_updateRunPatch($params)
{

    $result = Updater::dynsRunPatch($params);

    echo json_encode($result);
}

function dyns_updateMarkPatchAsDone($params)
{

    $result = Updater::dynsMarkPatchAsDone($params);

    echo json_encode($result);

}


function dyns_updateFetchRemoteStatus($params)
{

    $clientId = $params['client'] ?? 0;

    $success = false;

    if (!empty($clientId)) {
        $status = Updater::getRemoteSystemInfo($clientId);
        $success = true;
    }

    $html = '';
    if ($success) {
        $client = DatasyncClient::get($clientId);
        $systemInformation = $status;
        ob_start();
        include(COREPATH . 'admin/tools/update/html/client.php');
        $html = ob_get_contents();
        ob_end_clean();
    }


    echo json_encode(compact('success', 'html'));

}
