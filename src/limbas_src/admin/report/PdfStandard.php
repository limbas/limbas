<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */
namespace Limbas\admin\report;

enum PdfStandard: int
{
    
    case DEFAULT = 0;

    case A1a = 1;
    case A1b = 2;


    case A2a = 3;
    case A2b = 4;
    case A2u = 5;
    
    case A3 = 6;

    case A4f = 7;
    case A4e = 8;
    
    case X1a = 9;


    public function isA(): bool
    {
        return match ($this) {
            self::A1a,
            self::A1b,
            self::A2a,
            self::A2b,
            self::A2u,
            self::A3,
            self::A4f,
            self::A4e => true,
            default => false
        };
    }

    public function isX(): bool
    {
        return match ($this) {
            self::X1a => true,
            default => false
        };
    }
    
    public function name(): string
    {
        return match ($this) {
            self::A1a => 'A-1a',
            self::A1b => 'A-1b',
            self::A2a => 'A-2a',
            self::A2b => 'A-2b',
            self::A2u => 'A-2u',
            self::A3  => 'A-3',
            self::A4f => 'A-4f',
            self::A4e => 'A-4e',
            self::X1a => 'X-1a',
            default => '-'
        };
    }

    public function tcpdfValue(): int|bool
    {
        return match ($this) {
            self::A1a,
            self::A1b => 1,
            self::A2a,
            self::A2b,
            self::A2u => 2,
            self::A3  => 3,
            self::A4f,
            self::A4e => 4,
            default => false
        };
    }

    public function mpdfValue(): string
    {
        return match ($this) {
            self::A1a,
            self::X1a => '1-A',
            self::A1b => '1-B',
            self::A2a => '2-A',
            self::A2b => '2-B',
            self::A2u => '2-U',
            self::A3  => '3-B',
            self::A4f => '4-F',
            self::A4e => '4-E',
            default => '-'
        };
    }
    
    
}
