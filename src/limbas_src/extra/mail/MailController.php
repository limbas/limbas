<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */

namespace Limbas\extra\mail;

use Limbas\extra\mail\attachments\FileMailAttachment;
use Limbas\lib\LimbasController;

class MailController extends LimbasController
{

    public function __construct(
        private bool $isAdmin = false
    )
    {
    }

    /**
     * @param array $request
     * @return array|false[]
     */
    public function handleRequest(array $request): array
    {
        return match ($request['action']) {
            'save' => $this->saveMailAccount($request),
            'delete' => $this->deleteMailAccount($request),
            'send' => $this->sendMail($request),
            default => ['success' => false],
        };
    }

    /**
     * @param array $request
     * @return array
     */
    private function saveMailAccount(array $request): array
    {
        global $LINK, $LINK_ID, $session, $umgvar, $lmmultitenants, $userdat;

        if (!$LINK[$LINK_ID['setup_mails']] && !$LINK[$LINK_ID['user_mails']]) {
            return ['success' => false];
        }

        $id = intval($request['id'] ?? 0);
        $userId = intval($request['user'] ?? 0);

        $default = false;
        $tenantId = null;
        if (empty($userId)) {
            $tenantId = intval($request['tenant'] ?? 0);
            $default = boolval($request['default']);
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


        // manage mail accounts globally or for own user only
        if (!$this->isAdmin || !$LINK[$LINK_ID['setup_mails']]) {
            $userId = intval($session['user_id']);
            $default = false;
            $tenantID = null;
        }


        $mailAccount = new MailAccount(
            $request['name'],
            $request['email'],
            intval($request['type']),
            $request['imap_host'],
            intval($request['imap_port']),
            $request['imap_user'],
            $request['imap_password'],
            $request['imap_path'],
            $request['smtp_host'],
            intval($request['smtp_port']),
            $request['smtp_user'],
            $request['smtp_password'],
            $userId,
            $tenantId,
            $id,
            $default,
            boolval($request['status']),
            boolval($request['hidden']),
            intval($request['mail_table']),
        );

        $success = $mailAccount->save();
        $html = '';
        if ($success) {
            ob_start();
            $adminMails = $this->isAdmin;
            include(COREPATH . 'admin/setup/mail/mail-row.php');
            $html = ob_get_contents();
            ob_end_clean();
        }

        return compact('success', 'html');
    }


    /**
     * @param array $request
     * @return array
     */
    private function deleteMailAccount(array $request): array
    {
        global $LINK, $LINK_ID, $session;

        if (!$LINK[$LINK_ID['setup_mails']] && !$LINK[$LINK_ID['user_mails']]) {
            return ['success' => false];
        }

        $mailAccount = MailAccount::get(intval($request['id']));

        $success = false;
        if ($LINK[$LINK_ID['setup_mails']] || $mailAccount->userId !== intval($session['user_id'])) {
            $success = $mailAccount->delete();
        }
        return compact('success');
    }

    /**
     * @param array $request
     * @return array
     */
    private function sendMail(array $request): array
    {
        $required = ['account', 'receiver', 'subject', 'message'];
        foreach ($required as $r) {
            if (empty($request[$r])) {
                return ['success' => false];
            }
        }

        
        $userAccounts = MailAccount::getUserMailAccounts(true);
        $allowedAccountIds = [];
        if(!empty($userAccounts)) {
            $allowedAccountIds = $userAccounts;
        }
        

        $mailAccount = MailAccount::get(intval($request['account']));
        if (!$mailAccount || !in_array($mailAccount->id, $allowedAccountIds)) {
            return ['success' => false];
        }
        
        
        $attachments = [];
        if( !empty( $_FILES ) && array_key_exists('attachments', $_FILES) )
        {
            foreach( $_FILES[ 'attachments' ][ 'tmp_name' ] as $index => $tmpName )
            {
                if( !empty( $_FILES[ 'attachments' ][ 'error' ][ $index ] ) )
                {
                    continue;
                }

                $fileName = $_FILES[ 'attachments' ][ 'name' ][ $index ];
                if( !empty( $tmpName ) && is_uploaded_file( $tmpName ) && file_exists( $tmpName ) )
                {
                    $attachments[] = new FileMailAttachment( $tmpName, $fileName);
                }
            }
        }

        $lmbMail = new LmbMail();
        $success = $lmbMail->sendMailToRecord($mailAccount, intval($request['gtabid']),intval($request['id']),$request['receiver'], $request['subject'], $request['message'], $attachments);

        return compact('success');
    }

}
