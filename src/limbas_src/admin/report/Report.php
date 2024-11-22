<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */

namespace Limbas\admin\report;

use Limbas\admin\templates\HasTemplateRoot;
use Limbas\extra\template\TemplateTable;
use Limbas\lib\db\Database;
use Limbas\lib\LimbasModel;

class Report extends LimbasModel
{
    use HasTemplateRoot;

    protected static string $tableName = 'LMB_REPORT_LIST';

    public static array $gtabDmsFolders = [];

    /**
     * @param int $id
     * @param string $name
     * @param int $gtabId
     * @param string $defFormat
     * @param string|null $beschreibung
     * @param string $page_style
     * @param string|null $sql_statement
     * @param string|null $erstdatum
     * @param string|null $editdatum
     * @param int|null $erstuser
     * @param int|null $edituser
     * @param string|null $grouplist
     * @param int|null $dmsFolderId
     * @param string|null $saveName
     * @param bool $tagmode
     * @param string|null $indexorder
     * @param int|null $odt_template
     * @param bool $listmode
     * @param int|null $ods_template
     * @param string|null $css
     * @param string|null $savedTemplate
     * @param int|null $parentId
     * @param int|null $template_element_id
     * @param int|null $template_gtab_id
     * @param int|null $rootTemplateTabId
     * @param int|null $rootTemplateElementId
     * @param int|null $dpi
     * @param string|null $orientation
     * @param string|null $used_fonts
     * @param int|null $default_font_size
     * @param string|null $default_font
     * @param int|null $printer
     */
    public function __construct(
        public int     $id,
        public string  $name,
        public int     $gtabId,
        public string  $defFormat,
        public ?string $beschreibung = null,
        public string  $page_style = '210;295;5;5;5;5',
        public ?string $sql_statement = null,
        public ?string $erstdatum = null,
        public ?string $editdatum = null,
        public ?int    $erstuser = null,
        public ?int    $edituser = null,
        public ?string $grouplist = null,
        public ?int    $dmsFolderId = null,
        public ?string $saveName = null,
        public bool    $tagmode = false,
        public ?string $indexorder = null,
        public ?int    $odt_template = null,
        public bool    $listmode = false,
        public ?int    $ods_template = null,
        public ?string $css = null,
        public ?string $savedTemplate = null,
        public ?int    $parentId = null,
        public ?int    $template_element_id = null,
        public ?int    $template_gtab_id = null,
        public ?int    $rootTemplateTabId = null,
        public ?int    $rootTemplateElementId = null,
        public ?int    $dpi = null,
        public ?string $orientation = null,
        public ?string $used_fonts = null,
        public ?int    $default_font_size = null,
        public ?string $default_font = null,
        public ?int    $printer = null
    )
    {

    }


    /**
     * @param int $id
     * @return Report|null
     */
    public static function get(int $id): Report|null
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
    public static function all(array $where = ['PARENT_ID' => null]): array
    {
        $rs = Database::select(self::$tableName, where: $where, orderBy: ['REFERENZ_TAB' => 'asc', 'NAME' => 'asc']);

        $output = [];

        while (lmbdb_fetch_row($rs)) {
            $output[] = new self(
                intval(lmbdb_result($rs, 'ID')),
                lmbdb_result($rs, 'NAME'),
                lmbdb_result($rs, 'REFERENZ_TAB'),
                lmbdb_result($rs, 'DEFFORMAT'),
                lmbdb_result($rs, 'BESCHREIBUNG') ?: null,
                lmbdb_result($rs, 'PAGE_STYLE') ?: null,
                lmbdb_result($rs, 'SQL_STATEMENT') ?: null,
                intval(lmbdb_result($rs, 'ERSTDATUM')) ?: null,
                intval(lmbdb_result($rs, 'EDITDATUM')) ?: null,
                intval(lmbdb_result($rs, 'ERSTUSER')) ?: null,
                intval(lmbdb_result($rs, 'EDITUSER')) ?: null,
                lmbdb_result($rs, 'GROUPLIST') ?: null,
                intval(lmbdb_result($rs, 'TARGET')) ?: null,
                lmbdb_result($rs, 'SAVENAME') ?: null,
                boolval(lmbdb_result($rs, 'TAGMOD')),
                lmbdb_result($rs, 'INDEXORDER') ?: null,
                intval(lmbdb_result($rs, 'ODT_TEMPLATE')) ?: null,
                boolval(lmbdb_result($rs, 'LISTMODE')),
                intval(lmbdb_result($rs, 'ODS_TEMPLATE')) ?: null,
                lmbdb_result($rs, 'CSS') ?: null,
                lmbdb_result($rs, 'SAVED_TEMPLATE') ?: null,
                intval(lmbdb_result($rs, 'PARENT_ID')) ?: null,
                0,
                0,
                intval(lmbdb_result($rs, 'ROOT_TEMPLATE')) ?: null,
                intval(lmbdb_result($rs, 'ROOT_TEMPLATE_ID')) ?: null,
                intval(lmbdb_result($rs, 'DPI')) ?: null,
                lmbdb_result($rs, 'ORIENTATION') ?: null,
                lmbdb_result($rs, 'USED_FONTS') ?: null,
                intval(lmbdb_result($rs, 'DEFAULT_FONT_SIZE')) ?: null,
                lmbdb_result($rs, 'DEFAULT_FONT') ?: null,
                intval(lmbdb_result($rs, 'PRINTER')) ?: null
            );

        }

        return $output;
    }


    /**
     * @return bool
     */
    public function save(): bool
    {
        global $session;

        $data = [
            'NAME' => $this->name,
            'BESCHREIBUNG' => $this->beschreibung,
            'PAGE_STYLE' => $this->page_style,
            'SQL_STATEMENT' => $this->sql_statement,
            'REFERENZ_TAB' => $this->gtabId,
            'GROUPLIST' => $this->grouplist,
            'TARGET' => $this->dmsFolderId,
            'SAVENAME' => $this->saveName,
            'TAGMOD' => $this->tagmode ? LMB_DBDEF_TRUE : LMB_DBDEF_FALSE,
            'INDEXORDER' => $this->indexorder,
            'ODT_TEMPLATE' => $this->odt_template,
            'DEFFORMAT' => $this->defFormat,
            'LISTMODE' => $this->listmode ? LMB_DBDEF_TRUE : LMB_DBDEF_FALSE,
            'ODS_TEMPLATE' => $this->ods_template,
            'CSS' => $this->css,
            'SAVED_TEMPLATE' => $this->savedTemplate,
            'PARENT_ID' => $this->parentId,
            #'TEMPLATE_ELEMENT_ID' => $this->template_element_id,
            #'TEMPLATE_GTAB_ID' => $this->template_gtab_id,
            'ROOT_TEMPLATE' => $this->rootTemplateTabId,
            'ROOT_TEMPLATE_ID' => $this->rootTemplateElementId,
            'DPI' => $this->dpi,
            'ORIENTATION' => $this->orientation,
            'USED_FONTS' => $this->used_fonts,
            'DEFAULT_FONT_SIZE' => $this->default_font_size,
            'DEFAULT_FONT' => $this->default_font,
            'PRINTER' => $this->printer
        ];

        lmb_StartTransaction();

        if (empty($this->id)) {

            $nextId = next_db_id(self::$tableName);

            $data['ID'] = $nextId;
            $data['ERSTUSER'] = $session['user_id'];

            if ($this->parentId === null) {
                $dmsId = $this->createFileStructure();
                if ($dmsId !== false) {
                    $data['TARGET'] = $dmsId;
                    $this->dmsFolderId = $dmsId;
                }
            }


            $result = Database::insert(self::$tableName, $data);

            if ($result) {
                $this->id = $nextId;
            }

            if ($this->parentId === null && $result && $this->defFormat === 'mpdf') {
                $this->rootTemplateTabId = TemplateTable::getDefaultTable()->id;
                $this->insertOrUpdateTemplateRoot();
            }


            $lmbRulesId = next_db_id('LMB_RULES_REPFORM');

            Database::insert('LMB_RULES_REPFORM', [
                'ID' => $lmbRulesId,
                'TYP' => 1,
                'GROUP_ID' => $session['group_id'],
                'LMVIEW' => LMB_DBDEF_TRUE,
                'REPFORM_ID' => $nextId
            ]);


        } else {
            $data['EDITUSER'] = $session['user_id'];
            $result = Database::update(self::$tableName, $data, ['ID' => $this->id]);
        }

        if ($result) {
            lmb_EndTransaction(1);
        } else {
            lmb_EndTransaction(0);
        }

        return $result;
    }


    private function createFileStructure(): int|false
    {
        require_once COREPATH . 'extra/explorer/filestructure.lib';
        require_once COREPATH . 'admin/tools/add_filestruct.lib';
        if ($lid = create_fs_report_dir($this->gtabId, $this->id, $this->name)) {
            get_filestructure(1);
            return intval($lid);
        }
        return false;
    }

    /**
     * @return bool
     */
    public function delete(): bool
    {
        lmb_StartTransaction();

        $rs = Database::select('LMB_REPORTS', ['TAB_SIZE'], ['BERICHT_ID' => $this->id, 'TYP' => 'bild']);
        while (lmbdb_fetch_row($rs)) {
            if (file_exists(UPLOADPATH . 'report/' . lmbdb_result($rs, "TAB_SIZE"))) {
                unlink(UPLOADPATH . 'report/' . lmbdb_result($rs, "TAB_SIZE"));
            }
            if (file_exists(TEMPPATH . 'thumpnails/report/' . lmbdb_result($rs, "TAB_SIZE"))) {
                unlink(TEMPPATH . 'thumpnails/report/' . lmbdb_result($rs, "TAB_SIZE"));
            }
        }

        Database::delete('LMB_REPORTS', ['BERICHT_ID' => $this->id]);
        Database::delete('LMB_RULES_REPFORM', ['REPFORM_ID' => $this->id, 'TYP' => 1]);

        $deleted = Database::delete(self::$tableName, ['ID' => $this->id]);

        if (!$deleted) {
            lmb_EndTransaction(0);
        } else {
            lmb_EndTransaction(1);
        }

        return $deleted;
    }


    public function getChildReports(): array
    {
        return self::all(['PARENT_ID' => $this->id]);
    }

    public function addChild(string $name, string $template): bool
    {
        $this->name = $name;
        $this->savedTemplate = $template;
        $this->parentId = $this->id;
        $this->id = 0;

        return $this->save();

    }


    public function dmsFolderList(): array
    {
        require_once COREPATH . 'admin/tools/add_filestruct.lib';
        require_once COREPATH . 'extra/explorer/filestructure.lib';

        if (!array_key_exists($this->gtabId, self::$gtabDmsFolders)) {
            get_filestructure();

            self::$gtabDmsFolders[$this->gtabId] = [];

            $rs = Database::select('LDMS_STRUCTURE', ['ID'], ['TAB_ID' => $this->gtabId, 'FIELD_ID' => 0, 'TYP' => 3]);
            while (lmbdb_fetch_row($rs)) {
                self::$gtabDmsFolders[$this->gtabId] += $this->loadDmsFolderList(intval(lmbdb_result($rs, 'ID')));
            }

        }

        return self::$gtabDmsFolders[$this->gtabId];
    }

    private function loadDmsFolderList(int $level, string $space = ''): array
    {
        global $filestruct;

        $output = [];

        if ($filestruct['id']) {
            foreach ($filestruct['id'] as $key => $value) {
                if ($filestruct['level'][$key] == $level and $filestruct['view'][$key]) {
                    $output[$key] .= $space . $filestruct["name"][$key];
                    if (in_array($filestruct['id'][$key], $filestruct['level'])) {
                        $space .= '--';
                        $output = array_merge($output, $this->loadDmsFolderList($filestruct["id"][$key], $space));
                    }

                }
            }
        }
        return $output;
    }


    public function copy(string $name): ?Report
    {
        global $session;


        lmb_StartTransaction();


        $originalId = $this->id;

        $this->id = 0;
        $this->name = $name;


        if ($this->defFormat === 'mpdf') {
            $templateTable = TemplateTable::get($this->rootTemplateTabId);
            $this->rootTemplateElementId = $templateTable->insertRootElement($this->name, $this->rootTemplateTabId, $this->rootTemplateElementId);
        }

        $success = $this->save();
        if (!$success) {
            return null;
        }


        if ($this->defFormat === 'tcpdf') {

            $rs = Database::select('LMB_REPORTS', ['ID', 'EL_ID', 'ERSTDATUM', 'EDITDATUM', 'ERSTUSER', 'EDITUSER', 'TYP', 'POSX', 'POSY', 'HEIGHT', 'WIDTH', 'INHALT', 'DBFIELD', 'VERKN_BAUM', 'STYLE', 'DB_DATA_TYPE', 'SHOW_ALL', 'BERICHT_ID', 'Z_INDEX', 'LISTE', 'TAB', 'TAB_SIZE', 'TAB_EL_COL', 'TAB_EL_ROW', 'TAB_EL_COL_SIZE', 'HEADER', 'FOOTER', 'PIC_TYP', 'PIC_STYLE', 'PIC_SIZE', 'PIC_RES', 'PIC_NAME', 'BG', 'EXTVALUE', 'TAB_EL'], ['BERICHT_ID' => $originalId]);


            $nextElementId = next_db_id('LMB_REPORTS');
            while ($oldElement = lmbdb_fetch_array($rs)) {
                $tabSize = $oldElement['TAB_SIZE'];
                $picName = $oldElement['PIC_NAME'];

                if ($oldElement['TYP'] === 'bild') {
                    $elId = $oldElement['EL_ID'];

                    $ext = explode('.', $picName);
                    $secName = $elId . substr(md5($elId . date('U') . $picName), 0, 12) . "." . $ext[1];
                    if (file_exists(UPLOADPATH . 'report/' . $tabSize)) {
                        copy(UPLOADPATH . 'report/' . $tabSize, UPLOADPATH . 'report/' . $secName);
                        if (file_exists(TEMPPATH . 'thumpnails/report/' . $tabSize)) {
                            copy(TEMPPATH . 'thumpnails/report/' . $tabSize, TEMPPATH . 'thumpnails/report/' . $secName);
                        }
                    }

                }

                $oldElement['ID'] = $nextElementId;
                $oldElement['BERICHT_ID'] = $this->id;
                $oldElement['ERSTDATUM'] = date('Y-m-d');
                $oldElement['ERSTUSER'] = $session['user_id'];
                $oldElement['TAB_SIZE'] = $tabSize;
                $oldElement['PIC_NAME'] = $picName;

                //translate bool values
                foreach (['HEADER', 'FOOTER', 'LISTE', 'SHOW_ALL'] as $boolField) {
                    $oldElement[$boolField] = $oldElement[$boolField] ? LMB_DBDEF_TRUE : LMB_DBDEF_FALSE;
                }


                if (!Database::insert('LMB_REPORTS', $oldElement)) {
                    $success = false;
                    break;
                }

                $nextElementId++;
            }
        }


        if (!$success) {
            lmb_EndTransaction(0);
        } else {
            lmb_EndTransaction(1);
        }

        return $success ? $this : null;
    }

}
