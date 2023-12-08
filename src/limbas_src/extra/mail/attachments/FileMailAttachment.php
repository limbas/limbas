<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */
namespace Limbas\extra\mail\attachments;

class FileMailAttachment extends MailAttachment
{

    public function __construct(string $path, ?string $name = null) {
        $this->name = $name;
        $this->path = $path;
    }


    public function uploadToDms(int $tabId, int $id, int $fieldId): bool
    {
        global $gfield;
        
        if(empty($this->name)) {
            $this->name = pathinfo($this->path,PATHINFO_BASENAME);
        }
        
        $file['file_name'][0] = $this->name;
        $file['file'][0] = $this->path;
        $file['file_archiv'][0] = 0;
        $duplicate['type'][0] = 'rename';
        $relation = [
            'datid' => $id,
            'gtabid' => $tabId,
            'fieldid' => $fieldId
        ];
        $level = $gfield[$tabId]['file_level'][$fieldId];

        $dmsId = lmb_fileUpload($file,$level,$relation,2,$duplicate);
        if($dmsId === false) {
            return false;
        }

        $this->path = get_PathFromID($dmsId);
        return true;
        
    }
    
}
