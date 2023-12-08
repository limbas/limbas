<?php

namespace Limbas\admin\group;

class GroupRightsController
{
    public function handleRequest(array $request): void
    {
        switch ($request['action']) {
            case 'tabRowsModal':
                $this->createTabRowsModal($request);
        }
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
        global $s_result;
        $gtabid = $request["tabid"];
        $ID = $request["ID"];
        $ismodal = true;
        $isview = false;
        require_once(COREPATH . 'admin/group/group_tab.dao');
        require_once(COREPATH . 'admin/group/group_tabrows.php');
    }
}