<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */

namespace Limbas\admin\setup\fonts;

use Limbas\lib\db\Database;
use Limbas\lib\LimbasModel;

class Font extends LimbasModel
{

    protected static string $tableName = 'LMB_FONTS';

    /**
     * @param int $id
     * @param string $family
     * @param string $style
     * @param string $source
     * @param string $file
     * @param string $fileName
     * @param string $fileType
     * @param bool $active
     */
    public function __construct(
        public int    $id,
        public string $family,
        public string $style,
        public string $source = '',
        public string $file = '',
        public string $fileName = '',
        public string $fileType = '',
        public bool   $active = false
    )
    {
        //
    }


    /**
     * @param int $id
     * @return Font|null
     */
    public static function get(int $id): Font|null
    {
        $output = self::all(['ID' => $id]);
        if (empty($output)) {
            return null;
        }

        return $output[0];
    }


    /**
     * @param array $where
     * @return array
     */
    public static function all(array $where = []): array
    {
        $rs = Database::select(self::$tableName, where: $where, orderBy: ['FAMILY' => 'asc']);

        $output = [];
        while (lmbdb_fetch_row($rs)) {
            $output[] = new self(
                intval(lmbdb_result($rs, 'ID')),
                lmbdb_result($rs, 'FAMILY'),
                lmbdb_result($rs, 'STYLE') ?? '',
                fileName: lmbdb_result($rs, 'NAME'),
                active: true
            );
        }

        return $output;
    }

    public static function getUniqueFontFamilies(array $where = []): array
    {
        $rs = Database::select(self::$tableName, ['DISTINCT FAMILY'], where: $where, orderBy: ['FAMILY' => 'asc']);

        $output = [];
        while (lmbdb_fetch_row($rs)) {
            $output[] = new self(
                0,
                lmbdb_result($rs, 'FAMILY'),
                '',
                '',
                active: true
            );
        }

        return $output;
    }

    /**
     * @return array
     */
    public static function getSystemFonts(): array
    {
        //$installedFonts = self::all();

        /*$fontFamilies = array_map(function ($font) {
            return $font->family;
        }, $installedFonts);
        //$fontIDs = array_map(function($font) {return $font->id;}, $installedFonts);*/

        $fonts = [];

        $cmd = 'fc-list ":scalable=true" family file foundry style';
        $fontList = explode(chr(10), shell_exec($cmd));

        $idCounter = 0;
        foreach ($fontList as $fontLine) {

            preg_match('/([^:]*)\s*:\s*([^:]*)\s*:\s*style=([^:]*)\s*:\s*foundry=\s*([^:]*)\s*/', $fontLine, $fontParts);

            if (empty($fontParts)) {
                continue;
            }

            $fontPath = pathinfo($fontParts[1]);

            if ($fontPath['extension'] !== 'ttf') {
                continue;
            }

            if (empty($fontPath['basename'])) {
                continue;
            }

            $multiStyleCommaPos = strpos($fontParts[3], ',');
            $style = $multiStyleCommaPos !== false ? substr($fontParts[3], 0, $multiStyleCommaPos) : $fontParts[3];

            $idCounter++;
            $font = self::get($idCounter);
            if (empty($font)) {
                $font = new self(
                    $idCounter,
                    $fontParts[2],
                    $style,
                    $fontParts[4],
                    $fontParts[1],
                    $fontPath['filename'],
                    $fontPath['extension'],
                    false
                );
            } else {
                $font->style = $style;
                $font->source = $fontParts[4];
                $font->file = $fontParts[1];
                $font->fileType = $fontPath['extension'];
            }


            $fonts[] = $font;

        }

        usort($fonts, function ($a, $b) {
            return strcmp($a->family, $b->family);
        });

        return $fonts;
    }

    /**
     * @param array $setFont
     * @return void
     */
    public static function applyFonts(array $setFont): void
    {
        Database::delete('LMB_FONTS', all: true);

        $fonts = self::getSystemFonts();
        /** @var Font $font */
        foreach ($fonts as $font) {
            if (in_array($font->id, $setFont)) {
                $font->activate();
            } else {
                $font->deactivate();
            }
        }

    }


    /**
     * @param bool $forceId
     * @return bool
     */
    public function save(bool $forceId = false): bool
    {
        $data = [
            'ID' => $this->id,
            'NAME' => $this->fileName,
            'FAMILY' => $this->family,
            'STYLE' => $this->style
        ];

        lmb_StartTransaction();

        if (empty($this->id) || $forceId === true) {
            if (!$forceId) {
                $data['ID'] = next_db_id(self::$tableName);
            }
            $result = Database::insert(self::$tableName, $data);
        } else {
            $result = Database::update(self::$tableName, $data, ['ID' => $this->id]);
        }

        if ($result) {
            lmb_EndTransaction(1);
        } else {
            lmb_EndTransaction(0);
        }

        return $result;
    }


    /**
     * @return bool
     */
    public function delete(): bool
    {
        lmb_StartTransaction();

        $deleted = Database::delete(self::$tableName, ['ID' => $this->id]);

        if (!$deleted) {
            lmb_EndTransaction(0);
        } else {
            lmb_EndTransaction(1);
        }

        return $deleted;
    }


    /**
     * @return string
     */
    public function getFontFilePath(): string
    {
        return DEPENDENTPATH . 'inc/fonts/' . $this->fileName . '.ttf';
    }

    /**
     * @return void
     */
    public function activate(): void
    {
        $fontFile = $this->getFontFilePath();

        copy($this->file, $fontFile);
        if (file_exists($fontFile)) {

            $this->setFontStyle();
            $this->save(true);
        }
    }

    /**
     * @return void
     */
    public function deactivate(): void
    {
        $fontFile = $this->getFontFilePath();
        if (file_exists($fontFile)) {
            unlink($fontFile);
        }
    }


    /**
     * @return void
     */
    private function setFontStyle(): void
    {
        $fontStyle = '';

        $style = lmb_strtolower($this->style);

        if ($style === 'italic' || $style === 'recursiv' || $style == 'oblique') {
            $fontStyle = 'I';
        } elseif ($style === 'bold' || $style === 'fett') {
            $fontStyle = 'B';
        } elseif ($style === 'bold italic' || $style === 'bold oblique') {
            $fontStyle = 'BI';
        } elseif ($style !== 'Regular') {

            if (str_contains($this->family, 'BoldItalic')) {
                $fontStyle = 'BI';
            } elseif (str_contains($this->family, 'Bold')) {
                $fontStyle = 'B';
            } elseif (str_contains($this->family, 'Italic')) {
                $fontStyle = 'I';
            }
        }

        $this->style = $fontStyle;
    }

    /**
     * @return bool
     */
    public function hasImage(): bool
    {
        $imagePath = LOCALASSETSPATH . 'images/fonts/font_' . $this->id . '.png';

        if (!file_exists($imagePath)) {
            $path = paintTextToImage('Hallo LIMBAS', 12, $this->file);
            if (!empty($path)) {
                return copy($path, $imagePath);
            }
        }

        return true;
    }

    /**
     * @return bool
     */
    public function fileExists(): bool
    {
        return file_exists($this->getFontFilePath());
    }

}
