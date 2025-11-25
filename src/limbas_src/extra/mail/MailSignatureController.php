<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */

namespace Limbas\extra\mail;

use Limbas\Controllers\LimbasController;
use Symfony\Component\HttpFoundation\Request;

class MailSignatureController extends LimbasController
{

    public function __construct(
        private readonly bool $isAdmin = false
    )
    {
    }

    /**
     * @param array|Request $request
     * @return array|false[]
     */
    public function handleRequest(array|Request $request): array
    {
        $action = is_array($request) ? $request['action'] : $request->get('action');
        return match ($action) {
            'save' => $this->saveMailSignature($request),
            'delete' => $this->deleteMailSignature($request),
            default => ['success' => false],
        };
    }

    /**
     * @param Request $request
     * @return array
     */
    private function saveMailSignature(Request $request): array
    {
        global $LINK, $LINK_ID, $session, $umgvar, $lmmultitenants, $userdat;

        if (!$LINK[$LINK_ID['setup_mail_signatures']] && !$LINK[$LINK_ID['user_mail_signatures']]) {
            return ['success' => false];
        }

        $id = intval($request->get('id') ?? 0);
        $userId = intval($request->get('user') ?? 0);

        $default = false;
        $tenantId = null;
        if (empty($userId)) {
            $tenantId = intval($request->get('tenant') ?? 0);
            $default = boolval($request->get('default'));
        }

        $multiTenantEnabled = false;
        if ($umgvar['multitenant'] && !empty($lmmultitenants['mid'])) {
            $multiTenantEnabled = true;
        }
        if (empty($tenantId) || !$multiTenantEnabled || !array_key_exists($tenantId, $lmmultitenants['mid'])) {
            $tenantId = null;
        }

        if (empty($userId) || empty($userdat['id']) || !in_array($userId, $userdat['id'])) {
            $userId = null;
        }


        // manage mail signatures globally or for own user only
        if (!$this->isAdmin || !$LINK[$LINK_ID['setup_mail_signatures']]) {
            $userId = intval($session['user_id']);
            $default = false;
            $tenantID = null;
        }


        $mailSignature = new MailSignature(
            $request->get('name'),
            $request->get('content'),
            $userId,
            $tenantId,
            $id,
            $default,
            boolval($request->get('status')),
            boolval($request->get('hidden')),
        );

        $success = $mailSignature->save();
        $html = '';
        if ($success) {
            ob_start();
            $adminSignatures = $this->isAdmin;
            include(COREPATH . 'admin/setup/mail/signature/signature-row.php');
            $html = ob_get_contents();
            ob_end_clean();
        }

        return compact('success', 'html');
    }


    /**
     * @param Request $request
     * @return array
     */
    private function deleteMailSignature(Request $request): array
    {
        global $LINK, $LINK_ID, $session;

        if (!$LINK[$LINK_ID['setup_mail_signatures']] && !$LINK[$LINK_ID['user_mail_signatures']]) {
            return ['success' => false];
        }

        $mailSignature = MailSignature::get(intval($request->get('id')));

        $success = false;
        if ($LINK[$LINK_ID['setup_mail_signatures']] || $mailSignature->userId === intval($session['user_id'])) {
            $success = $mailSignature->delete();
        }
        return compact('success');
    }

    
    
}
