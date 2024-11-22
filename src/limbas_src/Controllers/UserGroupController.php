<?php

namespace Limbas\Controllers;

use Limbas\lib\LimbasController;

class UserGroupController extends LimbasController
{

    public function handleRequest(array $request): array
    {
        return match ($request['action']) {
            'data' => $this->getUsers(),
            'groups' => $this->getGroups(),
            default => ['success' => false],
        };
    }

    public function getUsers(): array
    {
        global $userdat;
        $data = array();
        foreach($userdat["username"] as $key => $value) {

            // filter hidden user
            if($userdat["hidden"][$key]){continue;}

            $data[] = array(
                "id" => $key,
                "name" => $userdat["bezeichnung"][$key],
                "type" => "user"
            );
        }
        return ['success' => true, 'data' => $data];
    }

    public function getGroups(): array
    {
        global $groupdat;
        $data = array();
        foreach($groupdat["name"] as $key => $value) {
            $data[] = array(
                "id" => $key,
                "name" => $value,
                "type" => "group"
            );
        }
        return ['success' => true, 'data' => $data];
    }
}