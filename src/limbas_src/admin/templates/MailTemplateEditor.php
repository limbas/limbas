<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */

namespace Limbas\admin\templates;

use Limbas\admin\mailTemplates\MailTemplate;

class MailTemplateEditor extends TemplateEditor
{
    protected string $type = 'mail';

    public function render(): string
    {
        global $id;

        $mailTemplate = MailTemplate::get($id);
        
        $templateTableId = $mailTemplate->rootTemplateTabId;
        $templateId = $mailTemplate->rootTemplateElementId;

        $title = $mailTemplate->name;
        $tableName = $mailTemplate->getTabName();

        $editorSettings = '';

        return $this->renderMain($mailTemplate->id, $templateTableId, $templateId, $title, $tableName, $editorSettings);
    }

    protected function changeRootTemplate(int $elementId, int $newRootTemplateTabId): bool
    {
        $mailTemplate = MailTemplate::get($elementId);

        if ($mailTemplate === null) {
            return false;
        }

        return $mailTemplate->insertOrUpdateTemplateRoot($newRootTemplateTabId);
    }

    protected function saveSettings(array $params): bool
    {
        return true;
    }
}
