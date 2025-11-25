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

class LmbMailForm {


    /**
     * @param int $gtabid
     * @param int|array $id
     * @param int|null $templateId
     * @param array $resolvedTemplateGroups
     * @param array $resolvedDynamicData
     * @param array $attachments array of names of the files add automatically later (via extension)
     * @param array $appendData
     * @return string
     */
    public function render(int $gtabid, int|array $id, ?int $templateId = null, array $resolvedTemplateGroups = [], array $resolvedDynamicData = [], array $attachments = [], array $appendData = []): string
    {
        global $lang, $gsr, $verkn;
        
        if($id === 0) {
            # Abfrage
            $filter['anzahl'][$gtabid] = 'all';
            $gresult = get_gresult($gtabid, 1, $filter, $gsr, $verkn);

            $ids = [];
            foreach ($gresult[$gtabid]['id'] as $id) {
                $ids[] = intval($id);
            }
            $id = $ids;
        }
        
        if(is_array($id) && count($id) > 1) {
            return $this->renderPreview($gtabid,$id,$templateId, $resolvedTemplateGroups, $resolvedDynamicData);
        }
        elseif(is_array($id) && !empty($id)) {
            $id = $id[0];
        }
        
        
        ob_start();


        $lmbMail = new LmbMail();

        /** @var array<MailAccount> $senderAccounts */
        $senderAccounts = MailAccount::getUserMailAccounts();
        $mailSignatures = MailSignature::getUserMailSignatures();
        $defaultMailSignature = MailSignature::getSystemDefaultMailSignature();
        
        if(!empty($appendData['receivers']) && is_array($appendData['receivers'])) {
            $receivers = $appendData['receivers'];
        }
        elseif(!empty($appendData['receiver']) && is_string($appendData['receiver'])) {
            $receivers = [$appendData['receiver']];
        }
        else {
            $receivers = $lmbMail->getReceiverAddresses($gtabid,$id);
        }
        
        if(function_exists('lmbBeforeMailFormRender')) {
            lmbBeforeMailFormRender($gtabid,$id,$senderAccounts,$receivers,$templateId,$resolvedTemplateGroups,$resolvedDynamicData,$attachments);
        }

        $templateHtml = '';
        $subject = '';
        if($templateId !== null) {
            $mailTemplate = MailTemplate::get($templateId);
            if($mailTemplate) {
                $subject = $mailTemplate->name;
                $templateHtml = $mailTemplate->getRendered($gtabid,$id, $resolvedTemplateGroups, $resolvedDynamicData);
            }
        }
        
        if(!empty($appendData['subject']) && is_string($appendData['subject'])) {
            $subject = $appendData['subject'];
        }
        

        $senderAccountCount = count($senderAccounts);
        $bulkMail = false;
        $readonly = false;
        $firstId = $id;
        
        require(__DIR__ . '/html/mailform.php');
        return ob_get_clean() ?: '';
    }

    /**
     * @param int $gtabid
     * @param array $ids
     * @param int|null $templateId
     * @param array $resolvedTemplateGroups
     * @param array $resolvedDynamicData
     * @param array $attachments array of names of the files add automatically later (via extension)
     * @return string
     */
    public function renderPreview(int $gtabid, array $ids, ?int $templateId = null, array $resolvedTemplateGroups = [], array $resolvedDynamicData = [], array $attachments = []): string
    {
        global $lang;

        ob_start();

        
        $firstId = intval($ids[0]);
        $ids = array_unique($ids);

        $lmbMail = new LmbMail();

        /** @var array<MailAccount> $senderAccounts */
        $senderAccounts = MailAccount::getUserMailAccounts();
        $receivers = [];
        foreach($ids as $id) {
            $id = intval($id);
            if(empty($id)) {
                continue;
            }
            $receivers = array_merge($receivers,$lmbMail->getReceiverAddresses($gtabid,$id));
        }
        $id = json_encode($ids);
        
        //$receivers = array_unique($receivers);
        

        if(function_exists('lmbBeforeMailPreviewRender')) {
            lmbBeforeMailPreviewRender($gtabid,$ids,$senderAccounts,$receivers,$templateId,$resolvedTemplateGroups,$resolvedDynamicData,$attachments);
        }

        $templateHtml = '';
        $subject = '';
        $readonly = false;
        if(!empty($templateId)) {
            $readonly = true;
            $mailTemplate = MailTemplate::get($templateId);
            if($mailTemplate) {
                $subject = $mailTemplate->name;
                $templateHtml = $mailTemplate->getRendered($gtabid,$firstId, $resolvedTemplateGroups, $resolvedDynamicData);
            }
        }

        $senderAccountCount = count($senderAccounts);
        $bulkMail = true;

        require(__DIR__ . '/html/mailform.php');
        return ob_get_clean() ?: '';
    }

}
