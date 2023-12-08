<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */
namespace Limbas\extra\mail\attachments;

class DmsMailAttachment extends MailAttachment
{

    public function __construct(
        private readonly int $dmsId
    ) {

        $this->name = get_NameFromID($dmsId);
        $this->path = get_PathFromID($dmsId);
        
    }


    public function uploadToDms(int $tabId, int $id, int $fieldId): bool
    {

        global $gfield;

        $relparam = [
            'LID' => $gfield[$tabId]['file_level'][$fieldId]
        ];

        $relation = init_relation($tabId,$fieldId,$id,[$this->dmsId],null,null,$relparam);
        return boolval(set_relation($relation));
    }
    
}
