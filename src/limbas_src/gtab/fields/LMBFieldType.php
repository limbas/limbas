<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */
namespace gtab\fields;

require_once __DIR__ . '/../loadClasses.php';

abstract class LMBFieldType
{
    protected LMBField $field;
    
    protected string $fieldTypeName;
    
    public static function getFieldType(int|null $number, LMBField $field): LMBFieldType|null {

        if (empty($number)) {
            $number = 'Undefined';
        }

        $className = 'FieldType' . $number;
        $classPath = 'gtab\\fields\\' . $className;

        if (!class_exists($classPath)) {
            $file = __DIR__ . '/' . $className . '.php';
            if (!file_exists($file)) {
                return new FieldTypeUndefined($field);
            }
            require_once __DIR__ . '/' . $className . '.php';
        }

        return new $classPath($field);

    }
    
    
    public function __construct(LMBField $field) {
        $this->field = $field;
    }
    
    
    /*

20	Boolean
49	Fließkomma-Zahl


30	Währung

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
    public abstract function renderInput(mixed $value = null): string;
    
    
    public function getId(): int {
        return 0;
    }
    
    public function getName(): string {
        return $this->fieldTypeName;
    }
    
    protected function encodeValue(mixed $value): string {
        global $umgvar;
        return htmlspecialchars( (string) $value,ENT_QUOTES,$umgvar['charset']);
    }
}
