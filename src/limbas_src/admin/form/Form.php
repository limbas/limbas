<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */

namespace Limbas\admin\form;

use Limbas\admin\templates\HasTemplateRoot;
use Limbas\extra\template\base\TemplateConfig;
use Limbas\extra\template\base\TemplateElement;
use Limbas\extra\template\mail\MailTemplateConfig;
use Limbas\extra\template\TemplateTable;
use Limbas\lib\db\Database;
use Limbas\lib\LimbasModel;

class Form extends LimbasModel
{
    use HasTemplateRoot;

    protected static string $tableName = 'LMB_FORM_LIST';

    /**
     * @param int $id
     * @param string $name
     * @param int $gtabId
     * @param FormMode $mode
     * @param FormType $formType
     * @param string|null $description
     * @param string|null $createDate
     * @param string|null $editDate
     * @param int|null $createUserId
     * @param int|null $editUserId
     * @param string|null $frameSize
     * @param int|null $redirect
     * @param string|null $css
     * @param string|null $extension
     * @param string|null $dimension
     * @param int|null $customMenu
     * @param string|null $header
     * @param int|null $rootTemplateTabId
     * @param int|null $rootTemplateElementId
     * @param array|null $config
     */
    public function __construct(        
        public int     $id,
        public string  $name,
        public int     $gtabId,
        public FormMode $mode,
        public FormType $formType,
        public ?string $description = null,
        public ?string $createDate = null,
        public ?string $editDate = null,
        public ?int    $createUserId = null,
        public ?int    $editUserId = null,
        public ?string $frameSize = null,
        public ?int    $redirect = null,
        public ?string $css = null,
        public ?string $extension = null,
        public ?string $dimension = null,
        public ?int    $customMenu = null,
        public ?string $header = null,
        public ?int    $rootTemplateTabId = null,
        public ?int    $rootTemplateElementId = null,
        public ?array  $config = null
    )
    {

    }


    /**
     * @param int $id
     * @return Form|null
     */
    public static function get(int $id): Form|null
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
        $rs = Database::select(self::$tableName, where: $where, orderBy: ['REFERENZ_TAB' => 'asc', 'NAME' => 'asc']);

        $output = [];
        while (lmbdb_fetch_row($rs)) {
            $output[] = new self(
                intval(lmbdb_result($rs, 'ID')),
                lmbdb_result($rs, 'NAME'),
                lmbdb_result($rs, 'REFERENZ_TAB'),
                FormMode::tryFrom(intval(lmbdb_result($rs, 'MODE'))) ?? FormMode::OLD,
                FormType::tryFrom(intval(lmbdb_result($rs, 'FORM_TYP'))) ?? FormType::DETAILS,
                lmbdb_result($rs, 'BESCHREIBUNG') ?: null,
                lmbdb_result($rs, 'ERSTDATUM') ?: null,
                lmbdb_result($rs, 'EDITDATUM') ?: null,
                intval(lmbdb_result($rs, 'ERSTUSER')) ?: null,
                intval(lmbdb_result($rs, 'EDITUSER')) ?: null,
                lmbdb_result($rs, 'FRAME_SIZE') ?: null,
                intval(lmbdb_result($rs, 'REDIRECT')) ?: null,
                lmbdb_result($rs, 'CSS'),
                lmbdb_result($rs, 'EXTENSION') ?: null,
                lmbdb_result($rs, 'DIMENSION') ?: null,
                intval(lmbdb_result($rs, 'CUSTMENU')),
                lmbdb_result($rs, 'HEADER') ?: null,
                intval(lmbdb_result($rs, 'ROOT_TEMPLATE_TAB_ID')) ?: null,
                intval(lmbdb_result($rs, 'ROOT_TEMPLATE_ELEMENT_ID')) ?: null,
                json_decode(lmbdb_result($rs, 'CONFIG'), true) ?: null,
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
            'ID' => $this->id,
            'NAME' => $this->name,
            'BESCHREIBUNG' => $this->description,
            'REFERENZ_TAB' => $this->gtabId,
            'MODE' => $this->mode->value,
            'ERSTDATUM' => $this->createDate,
            'EDITDATUM' => $this->editDate,
            'ERSTUSER' => $this->createUserId,
            'EDITUSER' => $this->editUserId,
            'FORM_TYP' => $this->formType->value,
            'FRAME_SIZE' => $this->frameSize,
            'REDIRECT' => $this->redirect,
            'CSS' => $this->css,
            'EXTENSION' => $this->extension,
            'DIMENSION' => $this->dimension,
            'CUSTMENU' => $this->customMenu,
            'HEADER' => $this->header,
            'ROOT_TEMPLATE_TAB_ID' => $this->rootTemplateTabId,
            'ROOT_TEMPLATE_ELEMENT_ID' => $this->rootTemplateElementId,
            'CONFIG' => !empty($this->config) ? (json_encode($this->config) ?: null) : null
        ];

        lmb_StartTransaction();

        if (empty($this->id)) {

            $nextId = next_db_id(self::$tableName);

            $data['ID'] = $nextId;
            $data['ERSTUSER'] = $session['user_id'];


            $result = Database::insert(self::$tableName, $data);

            if ($result) {
                $this->id = $nextId;
            }

            if ($result && $this->mode === FormMode::NEW) {
                $this->rootTemplateTabId = TemplateTable::getDefaultTable()->id;
                $this->insertOrUpdateTemplateRoot();
            }


            $lmbRulesId = next_db_id('LMB_RULES_REPFORM');

            Database::insert('LMB_RULES_REPFORM', [
                'ID' => $lmbRulesId,
                'TYP' => 2,
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


    /**
     * @return bool
     */
    public function delete(): bool
    {
        lmb_StartTransaction();
        
        $rs = Database::select('LMB_FORMS', ['TAB_SIZE'], ['FORM_ID' => $this->id, 'TYP' => 'bild']);
        while (lmbdb_fetch_row($rs)) {
            if (file_exists(UPLOADPATH . 'form/' . lmbdb_result($rs, 'TAB_SIZE'))) {
                unlink(UPLOADPATH . 'form/' . lmbdb_result($rs, 'TAB_SIZE'));
            }
            if (file_exists(TEMPPATH . 'thumpnails/form/' . lmbdb_result($rs, 'TAB_SIZE'))) {
                unlink(TEMPPATH . 'thumpnails/form/' . lmbdb_result($rs, 'TAB_SIZE'));
            }
        }

        Database::delete('LMB_FORMS', ['FORM_ID' => $this->id]);
        Database::delete('LMB_RULES_REPFORM', ['REPFORM_ID' => $this->id, 'TYP' => 2]);

        $deleted = Database::delete(self::$tableName, ['ID' => $this->id]);

        if (!$deleted) {
            lmb_EndTransaction(0);
        } else {
            lmb_EndTransaction(1);
        }

        return $deleted;
    }

    public function copy(string $name): ?Form
    {
        global $session;


        lmb_StartTransaction();


        $originalId = $this->id;

        $this->id = 0;
        $this->name = $name;

        if ($this->mode === FormMode::NEW) {
            $templateTable = TemplateTable::get($this->rootTemplateTabId);
            $this->rootTemplateElementId = $templateTable->insertRootElement($this->name, $this->rootTemplateTabId, $this->rootTemplateElementId);
        }

        $success = $this->save();
        if (!$success) {
            return null;
        }
        
        
        
        
        $rs = Database::select('LMB_FORMS', ['KEYID', 'ERSTDATUM', 'EDITDATUM', 'ERSTUSER', 'EDITUSER', 'TYP', 'POSX', 'POSY', 'HEIGHT', 'WIDTH', 'STYLE', 'TAB_GROUP', 'TAB_ID', 'FIELD_ID', 'Z_INDEX', 'UFORM_TYP', 'UFORM_VTYP', 'PIC_TYP', 'PIC_STYLE', 'PIC_SIZE', 'TAB', 'TAB_SIZE', 'TAB_EL_COL', 'TAB_EL_ROW', 'TAB_EL_COL_SIZE', 'TITLE', 'EVENT_CLICK', 'EVENT_OVER', 'EVENT_OUT', 'EVENT_DBLCLICK', 'EVENT_CHANGE', 'INHALT', 'CLASS', 'PARENTREL', 'SUBELEMENT', 'CATEGORIE', 'TAB_EL', 'PARAMETERS', 'EXTENSION'], ['FORM_ID' => $originalId]);


        $nextElementId = next_db_id('LMB_FORMS');
        while ($oldElement = lmbdb_fetch_array($rs)) {

            $oldElement['ID'] = $nextElementId;
            $oldElement['FORM_ID'] = $this->id;
            
            $oldElement['ERSTDATUM'] = date('Y-m-d');
            $oldElement['ERSTUSER'] = $session['user_id'];


            if (!Database::insert('LMB_FORMS', $oldElement)) {
                $success = false;
                break;
            }

            $nextElementId++;
        }
        
        
        
        if (!$success) {
            lmb_EndTransaction(0);
        } else {
            lmb_EndTransaction(1);
        }

        return $success ? $this : null;
    }
    
    public function getCreatedByUserName(): string
    {
        global $userdat;
        
        if(array_key_exists($this->createUserId, $userdat['bezeichnung'])) {
            return $userdat['bezeichnung'][$this->createUserId] ?: '';
        }
        
        return '';
    }
    
    public function render(): string
    {
        if($this->extension !== null && file_exists(EXTENSIONSPATH . $this->extension)) {
            require_once EXTENSIONSPATH . $this->extension;
            return '';
        }
        TemplateConfig::$instance = new MailTemplateConfig([], null, $this->gtabId, null);
        $templateElement = TemplateElement::fromID($this->rootTemplateTabId, $this->rootTemplateElementId);
        return $templateElement->getHtml();
    }
    
}
