<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */

use Limbas\extra\mail\LmbMailForm;

require_once COREPATH . 'gtab/gtab.lib';

global $type;

$mailForm = new LmbMailForm();

$bulkMail = false;
if(is_array($_GET['id'])) {
    $bulkMail = true;
    $id = $_GET['id'];
}
else {
    $id = intval($_GET['id'] ?? 0);
}


$gtabid = intval($_GET['gtabid'] ?? 0);
$templateId = $_GET['template_id'] ?? null;
$resolvedTemplateGroups = json_decode($_GET['resolvedTemplateGroups'], true) ?? [];
$resolvedDynamicData = json_decode($_GET['resolvedDynamicData'], true) ?? [];
$appendData = $_GET['appendData'] ?? [];

echo $mailForm->render($gtabid,$id,$templateId, $resolvedTemplateGroups, $resolvedDynamicData, appendData: $appendData);
