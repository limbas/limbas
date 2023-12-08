<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */

namespace Limbas\admin\mailTemplates;

use Limbas\extra\template\TemplateTable;

class MailTemplateController
{

    /**
     * @param array $request
     * @return array|false[]
     */
    public function handleRequest(array $request): array
    {
        return match ($request['action']) {
            'save' => $this->saveMailTemplate($request),
            'delete' => $this->deleteMailTemplate($request),
            default => ['success' => false],
        };
    }

    /**
     * @param array $request
     * @return array
     */
    private function saveMailTemplate(array $request): array
    {
        global $LINK, $LINK_ID;
        global $gtab;

        if (!$LINK[$LINK_ID['setup_mail_templates']] || empty($request['name'])) {
            return ['success' => false];
        }


        $tabId = $request['tabId'] ?: 0;
        if ($tabId !== 0 && !in_array($tabId, $gtab['tab_id'])) {
            $tabId = 0;
        }

        $templateTable = TemplateTable::get($request['templateTabId']);
        if ($templateTable === null) {
            return ['success' => false];
        }

        $mailTemplate = new MailTemplate(
            $request['name'],
            $tabId,
            $request['description'] ?? '',
            rootTemplateTabId: $request['templateTabId']
        );

        $success = $mailTemplate->save();
        $html = '';
        if ($success) {
            ob_start();
            include(__DIR__ . '/mail-row.php');
            $html = ob_get_contents();
            ob_end_clean();
        }

        return compact('success', 'html');
    }


    /**
     * @param array $request
     * @return array
     */
    private function deleteMailTemplate(array $request): array
    {
        global $LINK, $LINK_ID;

        if (!$LINK[$LINK_ID['setup_mail_templates']]) {
            return ['success' => false];
        }

        $mailAccount = MailTemplate::get(intval($request['id']));


        return ['success' => $mailAccount->delete()];
    }

}
