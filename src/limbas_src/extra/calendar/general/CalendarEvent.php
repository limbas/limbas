<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */
namespace Limbas\extra\calendar\general;

use DateTime;
use DateTimeInterface;
use JsonSerializable;

class CalendarEvent implements JsonSerializable
{
    public ?string $startStr;
    public ?string $endStr;

    public function __construct(
        public ?string $id,
        public ?string $groupId = null,
        public bool $allDay = false,
        public ?DateTime $start = null,
        public ?DateTime $end = null,
        public string $title = '',
        public ?string $url = '',
        public array $classNames = [],
        public ?bool $editable = null,
        public ?bool $startEditable = null,
        public ?bool $durationEditable = null,
        public ?bool $resourceEditable = null,
        public string $display = 'auto',
        public ?bool $overlap = null,
        public mixed $constraint = null, // Could be a string or object
        public ?string $backgroundColor = null,
        public ?string $borderColor = null,
        public ?string $textColor = null,
        public array $extendedProps = [],
        public mixed $source = null, // Can be an EventSource or other data
        public ?int $resourceId = null
    ) {
        $this->startStr = $start?->format(DateTimeInterface::ATOM);
        $this->endStr = $end?->format(DateTimeInterface::ATOM);
    }

    // Set an extended property in the extendedProps array
    public function setExtendedProp(string $key, mixed $value): void {
        $this->extendedProps[$key] = $value;
    }


    public function jsonSerialize(): mixed
    {
        $output = get_object_vars($this);
        $output['start'] = $this->start?->format(DateTimeInterface::ATOM);
        $output['end'] = $this->end?->format(DateTimeInterface::ATOM);
        if($this->groupId === null) {
            unset($output['groupId']);
        }
        return $output;
    }
}
