<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */

namespace Limbas\extra\mail;

require_once(COREPATH . 'gtab/gtab.lib');

use Limbas\admin\mailTemplates\MailTemplate;
use Limbas\extra\mail\attachments\MailAttachment;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Part\DataPart;
use Symfony\Component\Mime\Part\File;
use Symfony\Component\Mime\RawMessage;
use Throwable;
use function get_gresult;

class LmbMail
{
    

    /**
     * @param int $gtabId
     * @param int $id
     * @return array
     */
    public function getReceiverAddresses(int $gtabId, int $id): array
    {
        global $gfield;

        $receiverFields = array_keys(array_filter($gfield[$gtabId]['data_type'], function ($dataType) {
            return intval($dataType) === 28;
        }));

        $receivers = [];

        $gresult = get_gresult($gtabId, 1, null, null, null, [$gtabId => $receiverFields], $id);

        foreach ($receiverFields as $receiverField) {
            foreach ($gresult[$gtabId][$receiverField] as $email) {
                $receivers[] = $email;
            }
        }

        if(function_exists('lmbGetMailReceivers')) {
            lmbGetMailReceivers($gtabId,$id,$receivers);
        }

        return array_unique($receivers);
    }


    /**
     * @param string|array $to
     * @param string $subject
     * @param string $message
     * @param array|null $attachments
     * @param string|array|null $cc
     * @param string|array|null $bcc
     * @param MailTable|null $mailTable
     * @return bool
     */
    public function sendFromDefault(string|array $to, string $subject, string $message, ?array $attachments = null, string|array $cc = null, string|array $bcc = null, MailTable $mailTable = null): bool
    {
        $defaultAccount = MailAccount::getSystemDefaultAccount();
        if ($defaultAccount) {
            return $this->send($defaultAccount, $to, $subject, $message, $attachments, $cc, $bcc, $mailTable);
        }
        return false;
    }


    /**
     * @param MailAccount $fromAccount
     * @param string|array $to
     * @param string $subject
     * @param string $message
     * @param array|null $attachments array of MailAttachment
     * @param string|array|null $cc
     * @param string|array|null $bcc
     * @param MailTable|null $mailTable
     * @param int|null $mailTableDatId
     * @return bool
     */
    public function send(MailAccount $fromAccount, string|array $to, string $subject, string $message, ?array $attachments = null, string|array $cc = null, string|array $bcc = null, MailTable $mailTable = null, int $mailTableDatId = null): bool
    {
        
        if ($mailTableDatId === null) {

            if(function_exists('lmbBeforeMailGenerated')) {
                lmbBeforeMailGenerated($fromAccount, $to, $subject, $message, $attachments, $cc, $bcc, $mailTable);
            }
            
            $this->saveMailToTable($fromAccount, $to, $subject, $message, $attachments, $cc, $bcc, $mailTable);
        }


        $dsn = match ($fromAccount->transportType) {
            MailAccount::TRANSPORT_SMTP => "smtp://" . urlencode($fromAccount->smtpUser) . ":" . urlencode($fromAccount->getSmtpPassword()) . "@" . $fromAccount->smtpHost . ":" . $fromAccount->smtpPort.'?verify_peer=false',
            MailAccount::TRANSPORT_NATIVE => 'native://default',
            default => 'sendmail://default',
        };

        $transport = Transport::fromDsn($dsn);


        $mailer = new Mailer($transport);


        if (!is_array($to)) {
            $to = [$to];
        }

        $textMessage = strip_tags(preg_replace('/<br(\s+)?\/?>/i', "\n", $message));

        $email = (new Email())
            ->from($fromAccount->email)
            ->to(...$to)
            ->subject($subject)
            ->text($textMessage)
            ->html($message);

        if ($cc !== null) {
            if (!is_array($cc)) {
                $cc = [$cc];
            }
            $email->cc(...$cc);
        }

        if ($bcc !== null) {
            if (!is_array($bcc)) {
                $bcc = [$bcc];
            }
            $email->bcc(...$bcc);
        }
        
        if(!empty($attachments)) {
            /** @var MailAttachment $attachment */
            foreach($attachments as $attachment) {
                if($attachment instanceof MailAttachment) {
                    $filePath = $attachment->getPath();
                    if(!empty($filePath) && file_exists($filePath)) {
                        $email->addPart(new DataPart(new File($attachment->getPath()), $attachment->getName()));
                    }
                }
            }
        }


        if(function_exists('lmbBeforeMailSend')) {
            lmbBeforeMailSend($email);
        }
        

        $status = true;
        try {
            $mailer->send($email);
            $this->saveMailToImap($fromAccount, $email);
        } catch (Throwable) {
            $status = false;
        }

        if ($mailTable !== null && $mailTableDatId !== null) {
            $mailTable->setSendStatus($status, $mailTableDatId);
        }

        return $status;
    }


    /**
     * This functions sends an email to a given record. If not specified, an attempt is made to resolve all variables appropriately.
     * @param MailAccount $fromAccount
     * @param int $gtabid
     * @param int $id
     * @param string|array $to
     * @param string $subject
     * @param string $message
     * @param array|null $attachments
     * @param string|array|null $cc
     * @param string|array|null $bcc
     * @param MailTemplate|null $mailTemplate
     * @param array $resolvedTemplateGroups
     * @param array $resolvedDynamicData
     * @param MailTable|null $mailTable
     * @return bool
     */
    public function sendMailToRecord(MailAccount $fromAccount, int $gtabid, int $id, string|array $to = '', string $subject = '', string $message = '', ?array $attachments = null, string|array $cc = null, string|array $bcc = null, MailTemplate $mailTemplate = null, array $resolvedTemplateGroups = [], array $resolvedDynamicData = [], MailTable $mailTable = null): bool
    {
        
        if(empty($to)) {
            $to = $this->getReceiverAddresses($gtabid,$id);
            if(empty($to)) {
                return false;
            }
        }

        if(!empty($mailTemplate)) {
            if(empty($message)) {
                $message = $mailTemplate->getRendered($gtabid,$id, $resolvedTemplateGroups, $resolvedDynamicData);
            }
            if(empty($message)) {
                return false;
            }
            if(empty($subject)) {
                $subject = $mailTemplate->name;
            }
            if(empty($subject)) {
                return false;
            }
        }

        if (empty($mailTable)) {
            $mailTable = $fromAccount->getMailTable();
        }
        if (!empty($mailTable)) {
            $mailTable->addRelation($gtabid, $id);
        }
        

        if(function_exists('lmbMailSendToRecord')) {
            lmbMailSendToRecord($fromAccount, $gtabid, $id, $to, $subject, $message, $attachments, $cc, $bcc, $mailTable);
        }

        return $this->send($fromAccount, $to, $subject, $message, $attachments, $cc, $bcc, $mailTable);
    }
    
    
    private function saveMailToTable(MailAccount $fromAccount, string|array $to, string $subject, string $message, ?array $attachments = null, string|array $cc = null, string|array $bcc = null, MailTable $mailTable = null): void
    {
        // if no mail table is forced get default from mail account
        if (empty($mailTable)) {
            $mailTable = $fromAccount->getMailTable();
        }

        // if no mail table was found, no queue should be applied
        if (empty($mailTable)) {
            return;
        }

        $mailTable->insertEntry($fromAccount, $to, $subject, $message, $attachments, $cc, $bcc);
    }

    /**
     * @param MailAccount $mailAccount
     * @param RawMessage $message
     * @return void
     */
    private function saveMailToImap(MailAccount $mailAccount, RawMessage $message): void
    {
        if(empty($mailAccount->imapHost) || !function_exists('imap_timeout')) {
            return;
        }
        try
        {
            $serverPath = '{' . $mailAccount->imapHost . ':' . $mailAccount->imapPort . '/imap/ssl}' . $mailAccount->imapPath;
            
            imap_timeout(IMAP_OPENTIMEOUT, 10);
            $msg = $message->toString();
            $stream = imap_open($serverPath, $mailAccount->imapUser, $mailAccount->getImapPassword());
            imap_append($stream, $serverPath, $msg . "\r\n", "\\Seen");
            imap_close($stream);
        } catch (Throwable) {}
    }


}
