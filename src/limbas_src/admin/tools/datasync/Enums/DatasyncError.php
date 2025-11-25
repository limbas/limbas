<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */

namespace Limbas\admin\tools\datasync\Enums;

enum DatasyncError: int
{

    case UNKNOWN = 0;

    case NO_CLIENT_DATA_ID = 1;

    case FIELD_NOT_MARKED_AS_SYNC = 2;

    case FILL_RECORD = 3;

    case RECORD_NOT_FOUND = 4;

    case NEW_DATA_FAILED = 5;
    case DELETE_DATA_FAILED = 6;

    case UPDATE_DATA_FAILED = 7;

    case CLIENT_ID_COULD_NOT_BE_RESOLVED = 8;
    case ONE_TO_ONE_ID_COULD_NOT_BE_RESOLVED = 9;

    case ADD_RELATIONS_FAILED = 10;
    case DELETE_RELATIONS_FAILED = 11;
    case APPLYING_RELATIONS_FAILED = 12;

    case CONFLICT = 13;


    public function getMessage(): string
    {
        return match ($this) {
            self::NO_CLIENT_DATA_ID => 'No client data ID',
            self::FIELD_NOT_MARKED_AS_SYNC => 'Field not marked as sync',
            self::FILL_RECORD => 'Error during data fill',
            self::RECORD_NOT_FOUND => 'Record not found',
            self::NEW_DATA_FAILED => 'New data failed',
            self::DELETE_DATA_FAILED => 'Delete data failed',
            self::UPDATE_DATA_FAILED => 'Update data failed',
            self::CLIENT_ID_COULD_NOT_BE_RESOLVED => 'Client ID not resolved',
            self::ONE_TO_ONE_ID_COULD_NOT_BE_RESOLVED => 'One-to-one ID not resolved',
            self::ADD_RELATIONS_FAILED => 'Add relations failed',
            self::DELETE_RELATIONS_FAILED => 'Delete relations failed',
            self::APPLYING_RELATIONS_FAILED => 'Applying relations failed',
            self::CONFLICT => 'Conflict',
            default => 'Unknown error'
        };
    }

}
