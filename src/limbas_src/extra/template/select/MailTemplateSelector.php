<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */

namespace Limbas\extra\template\select;

use Limbas\admin\mailTemplates\MailTemplate;
use Limbas\extra\template\base\TemplateResolver;
use Limbas\extra\template\mail\MailTemplateResolver;

class MailTemplateSelector extends TemplateSelector
{

    public function __construct(string $type)
    {
        $this->type = $type;
    }


    public function getElementListRendered(int $gtabid, string $search = '', int $page = 1, int $perPage = 10, int $id = 0): array
    {

        // if no template is found => directly open mail form

        $mailTemplates = MailTemplate::all(['TAB_ID' => $gtabid]);

        if (empty($mailTemplates)) {
            return [
                'skipResolve' => true,
                'table' => '',
                'pagination' => '',
                'params' => $this->getFinalResolvedParameters(0, $gtabid, $id, null, null)
            ];
        }

        return parent::getElementListRendered($gtabid, $search, $page, $perPage, $id);
    }

    protected function getElementList(int $gtabid, string $search = '', int $page = 1, int $perPage = 10): array
    {

        $elementList = [];

        $mailTemplates = MailTemplate::all(['TAB_ID' => $gtabid]);

        /** @var MailTemplate $mailTemplate */
        foreach ($mailTemplates as $mailTemplate) {

            //if (false) {

                // TODO: resolve sub template groups like ReportTemplateSelector->getPartsListOfTemplate

                // continue;
            //}

            $elementList[] = [
                'id' => $mailTemplate->id,
                'name' => $mailTemplate->name,
                'gtabid' => $mailTemplate->tabId,
                'resolved' => '{}'
            ];
        }

        $elementList = $this->getFilteredElementList($elementList, $search);
        array_unshift($elementList, [
            'id' => 0,
            'name' => 'Mit leerer Mail starten',
            'gtabid' => $gtabid,
            'resolved' => '{}'
        ]);


        return $this->paginateElementList($elementList, $page, $perPage);
    }

    public function resolveSelect($params): array
    {

        $elementId = $params['elementId'];
        $gtabid = $params['gtabid'];
        $use_record = $params['use_record'];
        $ID = $params['id'];

        if (empty($elementId)) {

            return [
                'resolved' => true,
                'html' => '',
                'type' => $this->type,
                'params' => $this->getFinalResolvedParameters(0, $gtabid, $ID, $use_record, '')
            ];

        }

        return parent::resolveSelect($params);
    }


    protected function getFinalResolvedParameters(int $elementId, int $gtabid, int $id, $use_record, $resolvedTemplateGroups): array
    {

        $url = 'main.php?' . http_build_query(array(
                'action' => 'mail_preview',
                'gtabid' => $gtabid,
                'id' => $id,
                'use_record' => $use_record,
                'template_id' => $elementId,
                'resolvedTemplateGroups' => urldecode($resolvedTemplateGroups),
            ));

        return [
            'url' => $url,
            'name' => ''
        ];
    }


    protected function getTemplateResolver(int $elementId): TemplateResolver
    {
        return new MailTemplateResolver($elementId, 'mail');
    }
}




