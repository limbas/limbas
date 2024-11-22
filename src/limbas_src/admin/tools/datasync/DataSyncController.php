<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */


namespace Limbas\admin\tools\datasync;

require_once(COREPATH . 'admin/tools/datasync/loaddatasync.php');

use Limbas\lib\LimbasController;

class DataSyncController extends LimbasController
{

    /**
     * @param array $request
     * @return array|false[]
     */
    public function handleRequest(array $request): array
    {
        return match ($request['action']) {
            'getCache' => $this->getCache($request),
            default => ['success' => false],
        };
    }


    public function index(): string
    {
        global $lang;
        global $tab;
        global $db;
        global $gfield;
        global $tabgroup;
        global $gtab;

        ob_start();

        if (!$tab) {
            $tab = 3;
        }

        $tab = intval($tab);

        if ($tab === 3) {
            $clients = DatasyncClient::all(true);
            $status = DatasyncProcess::status();
            require_once(COREPATH . 'admin/tools/datasync/datasync.dao');
            include(COREPATH . 'admin/tools/datasync/html/dashboard.php');
        } elseif ($tab === 5) {
            $clients = DatasyncClient::all(true);
            include(COREPATH . 'admin/tools/datasync/html/cache.php');
        } else {
            require_once(COREPATH . 'admin/tools/datasync/datasync.dao');
            include(COREPATH . 'admin/tools/datasync/html/settings.php');
        }


        return ob_get_clean() ?: '';
    }


    /**
     * @param array $request
     * @return array
     */
    private function getCache(array $request): array
    {
        $datasyncClient = DatasyncClient::get($request['id']);

        $recordCount = $datasyncClient->getSyncCacheCount();

        $limit = $request['length'] ?? 10;
        $offset = $request['start'] ?? 0;

        $syncCache = $datasyncClient->getSyncCache($limit, $offset);

        $data = [];

        /** @var SyncCache $cache */
        foreach ($syncCache as $cache) {
            $data[] = [
                $cache->id,
                $cache->tabId,
                $cache->fieldId,
                $cache->recordId,
                $cache->clientRecordId,
                $cache->type->name(),
                $cache->createDate ? $cache->createDate->format('Y-m-d H:i:s') : '',
                $cache->processKey
            ];
        }

        $output = [
            'draw' => $request['draw'],
            'recordsTotal' => $recordCount,
            'recordsFiltered' => $recordCount,
            'data' => $data,
        ];


        return $output;
    }


}


