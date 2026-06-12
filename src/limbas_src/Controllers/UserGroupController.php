<?php

namespace Limbas\Controllers;

use Symfony\Component\HttpFoundation\Request;

class UserGroupController extends LimbasController
{

    public function handleRequest(Request|array $request): array
    {
        return match ($request->get('action')) {
            'data' => $this->getUsers($request),
            'groups' => $this->getGroups(),
            default => ['success' => false],
        };
    }

    public function getUsers(Request $request = null): array
    {
        global $userdat;
        $data = array();
        $term = '';
        if($request !== null) {
            $term = $request->get('term');
            if ($term) {
                $term = strtolower($term);
            }
        }

        foreach ($userdat['username'] as $key => $value) {
            if ($userdat['hidden'][$key]) {
                continue;
            }

            $name = $userdat['bezeichnung'][$key];
            if ($term && !str_contains(strtolower($name), $term)) {
                continue;
            }

            $data[] = [
                'id' => $key,
                'name' => $name,
                'type' => 'user'
            ];
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
