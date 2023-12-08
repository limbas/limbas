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
     * @param int $id
     * @param int|null $templateId
     * @param array $resolvedTemplateGroups
     * @param array $resolvedDynamicData
     * @param array $attachments array of names of the files add automatically later (via extension) 
     * @return string
     */
    public function render(int $gtabid, int $id, ?int $templateId = null, array $resolvedTemplateGroups = [], array $resolvedDynamicData = [], array $attachments = []): string
    {
        global $lang;
        
        ob_start();


        $lmbMail = new LmbMail();

        /** @var array<MailAccount> $senderAccounts */
        $senderAccounts = MailAccount::getUserMailAccounts();
        $receivers = $lmbMail->getReceiverAddresses($gtabid,$id);
        
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

        $senderAccountCount = count($senderAccounts);
        $receiverCount = count($receivers);
        
        require(__DIR__ . '/html/mailform.php');
        return ob_get_clean() ?: '';
    }

}
