<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */
namespace gtab\fields;

abstract class LMBField
{

    /*
16	Zahl
20	Boolean
49	Fließkomma-Zahl
19	Numerische Kommazahl
21	Numerische Kommazahl (Prozent)
30	Währung
1	Text
10	Textblock
39	Long
28	E-Mail-Adresse
42	Telefonie
29	URL
40	Datum
11	Datum/Zeit
26	Zeit
14	Auswahl (Radio)
12	Auswahl (Select)
18	Auswahl (Checkbox)
31	Auswahl (Multiselect)
32	Auswahl (Ajax)
46	Attribut
27	Verknüpfung 1:n
25	Verknüpfung 1:n direkt
24	Verknüpfung n:m
41	Vererbung
23	Verknüpfung rückwertig
13	Upload
33	Bild URL
48	Dokument-Inhalt
45	Mimetype
44	Dateigröße
100	Sparte
101	Gruppierung Reiter
102	Gruppierung Zeile
50	Farbauswahl
15	PHP-Argument
47	SQL-Argument
22	Auto-ID
34	Post-User
36	Post-Date
35	Edit-User
37	Edit-Date
38	User/Gruppen-Liste
43	Versionsbemerkung
51	Sync-Slave
52	Mandant
53	Gültigkeit
     */
    public abstract function render(): string;

    public function getId(): int {
        return 0;
    }

    public static function getField(int|null $number): LMBField|null {

        $className = match ($number) {
            1 => 'Text',
            10 => 'Textblock',
            11 => 'Datetime',
            //12 => 'Auswahl (Select)',
            13 => 'Upload',
            14 => 'Auswahl (Radio)',
            15 => 'PHP-Argument',
            16 => 'Zahl',
            18 => 'Auswahl (Checkbox)',
            19 => 'Numerische Kommazahl',
            20 => 'Boolean',
            21 => 'Numerische Kommazahl (Prozent)',
            22 => 'Auto-ID',
            23 => 'Verknüpfung rückwertig',
            24 => 'Verknüpfung n:m',
            25 => 'Verknüpfung 1:n direkt',
            26 => 'Zeit',
            27 => 'Verknüpfung 1:n',
            28 => 'E-Mail-Adresse',
            29 => 'URL',
            30 => 'Währung',
            31 => 'Auswahl (Multiselect)',
            32 => 'Auswahl (Ajax)',
            33 => 'Bild URL',
            34 => 'Post-User',
            35 => 'Edit-User',
            36 => 'Post-Date',
            37 => 'Edit-Date',
            38 => 'User/Gruppen-Liste',
            39 => 'Long',
            40 => 'Datum',
            41 => 'Vererbung',
            42 => 'Telefonie',
            43 => 'Versionsbemerkung',
            44 => 'Dateigröße',
            45 => 'Mimetype',
            46 => 'Attribut',
            47 => 'SQL-Argument',
            48 => 'Dokument-Inhalt',
            49 => 'Fließkomma-Zahl',
            50 => 'Farbauswahl',
            51 => 'Sync-Slave',
            52 => 'Mandant',
            53 => 'Gültigkeit',
            100 => 'Sparte',
            101 => 'Gruppierung Reiter',
            102 => 'Gruppierung Zeile',
            default => 'Text',
        };

        if (empty($className)) {
            return null;
        }

        $className = 'gtab\\fields\\Field' . $className;

        if (!class_exists($className)) {
            $file = __DIR__ . '/' . $className . '.php';
            if (!file_exists($file)) {
                return null;
            }
            require_once __DIR__ . '/' . $className . '.php';
        }



        return new $className();

    }
}
