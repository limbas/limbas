<?php

namespace Limbas\admin\group;

use Limbas\lib\LimbasController;

class GroupRightsController extends LimbasController
{
    public function handleRequest(array $request): array
    {
        switch ($request['action']) {
            case 'tabRowsModal':
                $this->createTabRowsModal($request);
        }
        return [];
    }

    private function createTabRowsModal(array $request): void
    {
        global $gtab;
        global $gfield;
        global $db;
        global $session;
        global $lang;
        global $f_result;
        global $l_result;
        $gtabid = $request["tabid"];
        $ID = $request["ID"];
        $ismodal = true;
        $isview = false;
        require_once(COREPATH . 'admin/group/group_tab_detail.dao');
        require_once(COREPATH . 'admin/group/group_tabrows.php');
    }
}
