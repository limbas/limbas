<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */

namespace Limbas\lib\globals;

use Limbas\lib\db\Database;

class Lang
{

    private static array $defaultLanguages = [
        1 => 'de',
        2 => 'en',
        3 => 'es',
        4 => 'fr',
    ];

    private static array $defaultLanguageNames = [
        1 => 'deutsch',
        2 => 'english',
        3 => 'Espagñol',
        4 => 'francais',
    ];

    protected static array $languageEntries;

    public static function get(int $langEntryId): string
    {
        if(empty(self::$languageEntries)) {
            global $lang;
            self::$languageEntries = $lang;
        }
        return self::$languageEntries[$langEntryId] ?? '';
    }

    public static function load(): void
    {
        global $session;
        global $umgvar;
        global $lang;

        $db = Database::get();

        /* --- Sprachtabelle system auslesen (default) ------------------- */
        if ($session["language"] != $umgvar["default_language"]) {
            $sqlquery3 = "SELECT ELEMENT_ID,WERT,JS FROM LMB_LANG WHERE LANGUAGE_ID = " . $umgvar["default_language"];
            $rs3 = lmbdb_exec($db, $sqlquery3);
            if (!$rs3) {
                $commit = 1;
            }
            while (lmbdb_fetch_row($rs3)) {
                $debug_lang = '';
                if ($session["group_id"] == 1 and $umgvar["debug_lang"]) {
                    $debug_lang = "(" . lmbdb_result($rs3, "ELEMENT_ID") . ")";
                }
                $lvalue = lmbdb_result($rs3, "WERT");
                if (lmbdb_result($rs3, "JS")) {
                    $lvalue = str_replace("\r\n", "\\n", $lvalue);
                    $lvalue = str_replace("\n", "\\n", $lvalue);
                    $lvalue = str_replace("\t", "\\t", $lvalue);
                    $lvalue = str_replace("'", "\\'", $lvalue);
                } else {
                    $lvalue = str_replace("\n", "<br>", $lvalue);
                    $lvalue = str_replace("\t", "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;", $lvalue);
                }
                $lang[lmbdb_result($rs3, "ELEMENT_ID")] = $debug_lang . $lvalue;
            }
        }

        /* --- Sprachtabelle system auslesen ------------------- */
        $sqlquery3 = "SELECT ELEMENT_ID,WERT,JS FROM LMB_LANG WHERE LANGUAGE_ID = " . $session['language'];
        $rs3 = lmbdb_exec($db, $sqlquery3);
        if (!$rs3) {
            $commit = 1;
        }
        while (lmbdb_fetch_row($rs3)) {
            $debug_lang = '';
            if ($session["group_id"] == 1 and $umgvar["debug_lang"]) {
                $debug_lang = "(" . lmbdb_result($rs3, "ELEMENT_ID") . ")";
            }
            if ($lvalue = lmbdb_result($rs3, "WERT")) {
                if (lmbdb_result($rs3, "JS")) {
                    $lvalue = str_replace("\r\n", "\\n", $lvalue);
                    $lvalue = str_replace("\n", "\\n", $lvalue);
                    $lvalue = str_replace("\t", "\\t", $lvalue);
                    $lvalue = str_replace("'", "\\'", $lvalue);
                } else {
                    $lvalue = str_replace("\n", "<br>", $lvalue);
                    $lvalue = str_replace("\t", "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;", $lvalue);
                }
                $lang[lmbdb_result($rs3, "ELEMENT_ID")] = $debug_lang . $lvalue;
            }
        }

        /* --- Sprachtabelle local default language auslesen ------------------- */
        if ($session["language"] != $umgvar["default_language"]) {
            $sqlquery3 = "SELECT ELEMENT_ID,WERT,OVERRIDE FROM LMB_LANG_DEPEND WHERE LANGUAGE_ID = " . $umgvar["default_language"];
            $rs3 = lmbdb_exec($db, $sqlquery3);
            if (!$rs3) {
                $commit = 1;
            }
            while (lmbdb_fetch_row($rs3)) {
                $lid = lmbdb_result($rs3, "ELEMENT_ID");
                $wert = trim(lmbdb_result($rs3, "WERT"));
                $override = trim(lmbdb_result($rs3, "OVERRIDE"));
                if ($wert) {
                    $debug_lang = '';
                    if ($session["group_id"] == 1 and $umgvar["debug_lang"]) {
                        $debug_lang = "(" . $lid . ")";
                    }
                    $lang[$lid] = $debug_lang . str_replace(chr(10), "", $wert);
                    if ($override) {
                        $lang[$override] = $lang[$lid];
                    }
                }
            }
        }

        /* --- Sprachtabelle local user language auslesen ------------------- */
        $sqlquery3 = "SELECT ELEMENT_ID,WERT,OVERRIDE FROM LMB_LANG_DEPEND WHERE LANGUAGE_ID = " . $session["language"];
        $rs3 = lmbdb_exec($db, $sqlquery3);
        if (!$rs3) {
            $commit = 1;
        }
        while (lmbdb_fetch_row($rs3)) {
            $lid = lmbdb_result($rs3, "ELEMENT_ID");
            $wert = trim(lmbdb_result($rs3, "WERT"));
            $override = trim(lmbdb_result($rs3, "OVERRIDE"));
            if ($wert) {
                if ($session["group_id"] == 1 and $umgvar["debug_lang"]) {
                    $debug_lang = "(" . $lid . ")";
                }
                $lang[$lid] = $debug_lang . str_replace(chr(10), "", $wert);
                if ($override) {
                    $lang[$override] = $lang[$lid];
                }
            }
        }

        self::$languageEntries = $lang;
    }

    public static function reSeedLanguage(): void
    {
        //check if any language has been added
        $rs = Database::query('SELECT DISTINCT LANGUAGE_ID, LANGUAGE FROM LMB_LANG ORDER BY LANGUAGE');
        $dbLanguages = [];
        while ($data = lmbdb_fetch_object($rs)) {
            if (!array_key_exists($data->LANGUAGE_ID, self::$defaultLanguages)) {
                $dbLanguages[$data->LANGUAGE_ID] = [
                    'temp' . $data->LANGUAGE_ID,
                    $data->LANGUAGE
                ];
            }
        }

        foreach ($dbLanguages as $languageId => $languageData) {
            self::exportLanguage($languageId, $languageData[0]);
        }

        Database::delete('LMB_LANG', all: true);

        self::importLanguageFile(1, 'de', 'deutsch');

        foreach (self::$defaultLanguages as $languageId => $languageShort) {
            if ($languageShort === 'de') {
                continue;
            }

            self::importLanguageFile($languageId, $languageShort, self::$defaultLanguageNames[$languageId]);
        }
        foreach ($dbLanguages as $languageId => $languageData) {
            self::importLanguageFile($languageId, $languageData[0], $languageData[1]);
        }

        self::load();
    }

    private static function importLanguageFile(int $languageId, string $languageShort, string $languageName): void
    {
        $languageEntries = include(RESOURCEPATH . 'lang/' . $languageShort . '.php');

        $nextId = null;
        if($languageId !== 1) {
            $nextId = next_db_id('LMB_LANG');
        }

        foreach ($languageEntries as $elementId => $languageData) {
            Database::insert('LMB_LANG', [
                'ID' => $nextId ?? $elementId,
                'LANGUAGE_ID' => $languageId,
                'LANGUAGE' => $languageName,
                'ELEMENT_ID' => $elementId,
                'TYP' => $languageData[0],
                'WERT' => $languageData[1],
                'EDIT' => $languageData[2],
                'LMFILE' => $languageData[3],
                'JS' => $languageData[4],
            ]);
            if($nextId !== null) {
                $nextId++;
            }
        }
    }


    public static function exportAllDefaultLanguages(): void
    {
        foreach (self::$defaultLanguages as $languageId => $languageShort) {
            self::exportLanguage($languageId, $languageShort);
        }
    }

    public static function exportLanguage(int $languageId, string $fileName): void
    {

        $rs = Database::select('LMB_LANG', where: ['LANGUAGE_ID' => $languageId], orderBy: ['ID' => 'asc']);

        $content = '<?php return [';
        while ($data = lmbdb_fetch_object($rs)) {
            /*$language[$data->ELEMENT_ID] = [
                $data->TYP,
                $data->WERT,
                $data->EDIT,
                $data->LMFILE,
                $data->JS
            ];*/
            $content .= $data->ELEMENT_ID . '=>[' . $data->TYP . ',\'' . str_replace('\'', '\\\'', trim($data->WERT)) . '\',' . ($data->EDIT ? 1 : 0) . ',' . '\'' . $data->LMFILE . '\'' . ',' . ($data->JS ? '1' : '0') . '],';
        }
        $content .= '];';
        file_put_contents(RESOURCEPATH . 'lang/' . $fileName . '.php', $content);
    }


}
