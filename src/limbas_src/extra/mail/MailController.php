<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */

namespace Limbas\extra\mail;

use Limbas\admin\mailTemplates\MailTemplate;
use Limbas\extra\mail\attachments\DmsMailAttachment;
use Limbas\extra\mail\attachments\FileMailAttachment;
use Limbas\lib\LimbasController;
use Symfony\Component\HttpFoundation\Request;

class MailController extends LimbasController
{

    public function __construct(
        private bool $isAdmin = false
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
            'save' => $this->saveMailAccount($request),
            'delete' => $this->deleteMailAccount($request),
            'preview' => $this->getMailPreview($request),
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
     * @param Request $request
     * @return array
     */
    private function sendMail(Request $request): array
    {
        $required = ['account', 'subject', 'message'];
        foreach ($required as $r) {
            if (empty($request->get($r))) {
                return ['success' => false];
            }
        }

        
        $userAccounts = MailAccount::getUserMailAccounts(true);
        $allowedAccountIds = [];
        if(!empty($userAccounts)) {
            $allowedAccountIds = $userAccounts;
        }
        

        $mailAccount = MailAccount::get(intval($request->get('account')));
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
        if( !empty($request->get('attachments')) && is_array($request->get('attachments')) )
        {
            $requestAttachments = array_unique($request->get('attachments'));
            foreach( $requestAttachments as $attachment ) {
                if(is_numeric($attachment)) {
                    $dmsId = intval($attachment);
                    $allowed = file_download($dmsId);
                    if($allowed !== false) {
                        $dmsAttachment = new DmsMailAttachment( intval($attachment) );
                        if(!empty($dmsAttachment->getName())) {
                            $attachments[] = $dmsAttachment;
                        }
                    }
                }
            }
        }
        
        $cc = $request->get('cc',[]);
        $bcc = $request->get('bcc',[]);
        
        
        $receivers = $request->get('receivers',[]);
        $id = $request->get('id',[]);


        $lmbMail = new LmbMail();
        
        if(is_array($id)) {

            $templateId = intval($request->get('templateId'));
            if(!empty($templateId)) {
                $mailTemplate = MailTemplate::get($templateId);
            }
            else {
                $mailTemplate = new MailTemplate(
                    '',0,'',
                    savedTemplate: $request->get('message'),
                    rootTemplateTabId: 0,rootTemplateElementId: 0
                );
            }

            $resolvedTemplateGroups = json_decode($request->get('resolvedTemplateGroups',''), true) ?? [];
            $resolvedDynamicData = json_decode($request->get('resolvedDynamicData',''), true) ?? [];
            
            $lmbMail->sendMailToRecords($mailAccount, intval($request->get('gtabid')),$id, $mailTemplate,$request->get('subject'), $attachments, $cc, $bcc,$resolvedTemplateGroups,$resolvedDynamicData);
            
            $success = true;
        }
        elseif(empty($receivers)) {
            $success = false;
        }
        else {
            $success = $lmbMail->sendMailToRecord($mailAccount, intval($request->get('gtabid')),intval($request->get('id')),$receivers, $request->get('subject'), $request->get('message'), $attachments, $cc, $bcc);
        }
        

        return compact('success');
    }


    /**
     * @param Request $request
     * @return array
     */
    private function getMailPreview(Request $request): array
    {
        $ids = $request->get('ids',[]);
        $tabId = $request->get('tabId',[]);


        $lmbMail = new LmbMail();

        if(!is_array($ids) || empty($ids) || empty($tabId)) {
            return ['success'=>false];
        }


        $templateId = intval($request->get('templateId'));
        if(!empty($templateId)) {
            $mailTemplate = MailTemplate::get($templateId);
        }
        else {
            $mailTemplate = new MailTemplate(
                '',0,'',
                savedTemplate: $request->get('message'),
                rootTemplateTabId: 0,rootTemplateElementId: 0
            );
        }
        
        if(!$mailTemplate) {
            return ['success'=>false];
        }
        
        $success = true;
        $html = '';
        foreach($ids as $id) {
            $id = intval($id);
            if(empty($id)) {
                continue;
            }

            $to = $lmbMail->getReceiverAddresses($tabId,$id);

            $resolvedTemplateGroups = json_decode($request->get('resolvedTemplateGroups',''), true) ?? [];
            $resolvedDynamicData = json_decode($request->get('resolvedDynamicData',''), true) ?? [];

            $templateHtml = $mailTemplate->getRendered($tabId,$id, $resolvedTemplateGroups, $resolvedDynamicData);

            $html .= ($to ? $to[0] . '<br>-------------------<br><br>': '') . $templateHtml . '<hr><hr>';
        }
        

        return compact('success','html');
    }
    
}
