<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */

namespace Limbas\admin\tools\datasync\Traits;

use Exception;
use Limbas\admin\tools\datasync\Data\DatasyncRelation;

trait HandleData
{


    /**
     * Checks if a special field type is allowed for synchronization and prepare its data
     *
     * @param ?int $currentClient
     * @param int $tabId
     * @param int $fieldId
     * @param int $recordId
     * @param mixed $value
     * @param array $gresult
     * @param bool $skipRelations
     * @return bool
     */
    protected function prepareFieldType(?int $currentClient, int $tabId, int $fieldId, int $recordId, mixed $value, array $gresult, bool $skipRelations = false): mixed
    {
        global $gtab;
        global $gfield;

        $isMain = !empty($currentClient);
        
        if (array_key_exists('sys', $gfield[$tabId]) && is_array($gfield[$tabId]['sys']) && array_key_exists($fieldId, $gfield[$tabId]['sys']) && $gfield[$tabId]['sys'][$fieldId]) {
            return false;
        }

        $levelIds = false;
        switch ($gfield[$tabId]['data_type'][$fieldId]) {
            //Relation: only valid if both, table and linked table, are synchronized
            case 27:
            case 24:
                //special case: LEVEL ID of DMS

                //if relation table is parameterized
                $relationTableId = $this->getRelationTableId($tabId, $fieldId);
                if ($relationTableId === null) {
                    $levelIds = $this->getRelationLevelIds($tabId, $fieldId, $value);
                }
            // break is intentionally missing
            case 25: // 1:n direct
                if ($skipRelations) {
                    return false;
                }


                //check if relation is hierarchical and ignore it
                if (array_key_exists('verkntree', $gfield[$tabId]) && array_key_exists($fieldId, $gfield[$tabId]['verkntree'])) {
                    return false;
                }

                $relatedTableId = $gfield[$tabId]['verkntabid'][$fieldId];
                $relatedTableSynced = array_key_exists($relatedTableId, $gtab['datasync']) && !empty($gtab['datasync'][$relatedTableId]);
                $relationTableSynced = !empty($relationTableId) && $gtab['datasync'][$relationTableId];

                //if related table is not synchronized ignore
                if (!$relatedTableSynced) {
                    return false;
                }

                if (!array_key_exists($fieldId, $this->relations)) {
                    $this->relations[$fieldId] = [];
                }                

                foreach ($value as $relationId) {
                    if (empty($relationId)) {
                        continue;
                    }


                    $relationMainId = $isMain ? $relationId : null;
                    $relationClientId = $isMain ? null : $relationId;
                    $levelId = null;

                    if ($levelIds !== false && is_array($levelIds) && array_key_exists($relationId, $levelIds)) {
                        $levelId = $levelIds[$relationId];
                    }

                    if(intval($gtab['datasync'][$relatedTableId]) === 2) {
                        // globally sync table
                        $relationClientId = $relationId;
                        $relationMainId = $relationId;
                    }
                    elseif ($isMain) {
                        // resolve client id on master
                        $relationClientId = $this->convertID($relatedTableId, $relationMainId, $currentClient, self::MAIN_TO_CLIENT);
                        if (empty($relationClientId)) {
                            $relationClientId = null;
                        }
                    }
                    
                    //if parameterized
                    $mainRecordId = null;
                    $clientRecordId = null;
                    if ($relationTableSynced) {
                        $keyId = $this->getRelKeyID($relationTableId, $recordId, $relationId);
                        $mainRecordId = $isMain ? $keyId : null;
                        $clientRecordId = $isMain ? null : $keyId;
                    }

                    $syncCacheId = $this->updatedFields[$fieldId][0] ?? ($this->created ? $this->createdCacheIds[0] : $this->updatedFields[0][0]); // in case the update is for all fields, only field id zero exists
                    $this->relations[$fieldId][] = new DatasyncRelation($syncCacheId, $relationMainId, $relationClientId, $levelId, $mainRecordId, $clientRecordId);
                }
                return false;
            case 30: //Currency
                if (is_array($value)) {
                    $value = $value['V'] . ' ' . $value['C'];
                }
                break;
            case 18: // Auswahl (checkbox)
            case 31: // Auswahl (multiselect)
            case 32: // Auswahl (ajax)
            case 46: // Attribute
                $value = $this->getMultiSelectFieldValue($tabId, $fieldId, $recordId, $value, $gresult);
                break;
            case 38: // user group list
                $func = 'cftyp_' . $gfield[$tabId]['funcid'][$fieldId];
                $values = $func(0, $fieldId, $tabId, 6, $gresult);
                if (is_array($values)) {
                    $value = [];
                    foreach ($values as $ugValue) {
                        $value[] = $ugValue['id'] . '_' . lmb_substr($ugValue['typ'], 0, 1);
                    }
                }
                break;
        }

        return $value;
    }


    /**
     * Applies data of special fields
     *
     * @param int $tabId
     * @param int $fieldId
     * @param int $id
     * @param mixed $value
     * @return bool|string
     * @throws Exception
     */
    protected function applyFieldType(int $tabId, int $fieldId, int $id, mixed &$value): bool|string
    {
        global $gfield;

        if (!is_array($gfield[$tabId])) {
            return false;
        }

        if (array_key_exists('sys', $gfield[$tabId]) && is_array($gfield[$tabId]['sys']) && array_key_exists($fieldId, $gfield[$tabId]['sys']) && $gfield[$tabId]['sys'][$fieldId]) {
            return false;
        }

        if (array_key_exists('argument', $gfield[$tabId]) && array_key_exists($fieldId, $gfield[$tabId]['argument'])) {
            return false;
        }

        $filter = [];
        $filter['relationval'][$tabId] = 1;
        $filter['status'][$tabId] = -1;
        $filter['validity'][$tabId] = 'all';


        switch ($gfield[$tabId]['data_type'][$fieldId]) {
            //Validity
            case 53:
            case 54:
                //Multitenant
            case 52:
                //sync slave
            case 51:
                //version comment
            case 43:
                //erst / edit user / date
            case 34:
            case 35:
            case 36:
            case 37:
                //ID field
            case 22:
                //Upload
            case 13:
            case 48:
                //TODO: PHP-Argument
                //SQL-Argument
            case 47:
                return false; // ignore
            //Relation: only valid if both, table and linked table, are synchronized
            case 27:
            case 24:
            case 25:
                //Backward relation
            case 23:
                /*$relatedTabId = $gfield[$tabId]['verkntabid'][$fieldId];
                if (!array_key_exists($relatedTabId, $this->template)) {
                    return false;
                }*/
                return false;
            //Currency
            case 30:
                if (is_array($value)) {
                    $value = $value['V'] . ' ' . $value['C'];
                }
                break;
            //Auswahl (checkbox), Auswahl (multiselect), Auswahl (ajax)
            case 18:
            case 31:
            case 32:
            case 46:
                if (is_array($value)) {
                    $wvalues = [];
                    if (array_key_exists('values', $value)) {
                        $wvalues = $value['values'];
                        unset($value['values']);
                    }


                    //compare existing values only if attribute or ajax; others are already handled by uftyp
                    if ($gfield[$tabId]['data_type'][$fieldId] == 32 || $gfield[$tabId]['data_type'][$fieldId] == 46) {

                        $gresult = get_gresult($tabId, 1, $filter, [], null, array($tabId => array($fieldId)), $id);

                        $fvalue = null;
                        if ($gresult[$tabId]['res_count'] > 0) {
                            $existing = $this->getMultiSelectFieldValue($tabId, $fieldId, $id, $value, $gresult);

                            if (!is_array($existing)) {
                                $existing = [];
                            }
                            if (array_key_exists('values', $existing)) {
                                unset($existing['values']);
                            }

                            $fvalue = [];
                            $removes = array_diff($existing, $value);
                            foreach ($removes as $rv) {
                                $fvalue[] = 'd' . $rv;
                            }

                            $adds = array_diff($value, $existing);
                            foreach ($adds as $av) {
                                $fvalue[] = 'a' . $av;
                            }

                        }

                        $value = $fvalue;

                    }

                    if ($gfield[$tabId]['data_type'][$fieldId] == 32) {
                        //$value = ';' . implode(';', $value);
                    }


                    uftyp_23($tabId, $fieldId, $id, $value);


                    if (!empty($wvalues)) {
                        foreach ($wvalues as $wid => $val) {
                            $this->setAttributeValue($tabId, $fieldId, $id, $wid, $val);
                        }
                    }


                }
                return false;
            // user group list
            case 38:

                // get existing values
                $func = 'cftyp_' . $gfield[$tabId]['funcid'][$fieldId];
                $gresult = get_gresult($tabId, 1, $filter, [], null, array($tabId => array($fieldId)), $id);
                $existing = $func(0, $fieldId, $tabId, 6, $gresult);

                $existingValues = [];
                if (is_array($existing)) {
                    foreach ($existing as $ugValue) {
                        $existingValues[] = $ugValue['id'] . '_' . lmb_substr($ugValue['typ'], 0, 1);
                    }
                }

                if (!is_array($value)) {
                    $value = [];
                }


                $diff = array_merge(array_diff($existingValues, $value), array_diff($value, $existingValues));
                if (!empty($diff)) {
                    $updateFunc = 'uftyp_' . $gfield[$tabId]['funcid'][$fieldId];
                    foreach ($diff as $ug) {
                        $updateFunc($tabId, $fieldId, $id, $ug);
                    }
                }

                return false;
        }

        return true;
    }


    private function getMultiSelectFieldValue(int $tabId, int $fieldId, int $recordId, mixed $value, array $gresult): ?array
    {
        global $gfield;
        if ($value > 0) {
            $func = 'cftyp_' . $gfield[$tabId]['funcid'][$fieldId];
            $values = $func(0, $fieldId, $tabId, 5, $gresult);
            if (!is_array($values)) {
                $values = array();
            }
            $value = [];

            //Attribut Werte
            if ($gfield[$tabId]['data_type'][$fieldId] == 46) {
                $value['values'] = [];
            }

            foreach ($values as $wid => $text) {
                if (is_numeric($wid)) {
                    $value[] = $wid;
                    if ($gfield[$tabId]['data_type'][$fieldId] == 46) {
                        $value['values'][$wid] = $this->getAttributeValue($tabId, $fieldId, $recordId, $wid);
                    }
                }
            }


        } else {
            $value = null;
        }
        return $value;
    }


    /**
     * Sets the values of attribute field type
     *
     * @param int $tabId
     * @param int $fieldId
     * @param int $id
     * @param $wid
     * @param $value
     * @return void
     */
    private function setAttributeValue(int $tabId, int $fieldId, int $id, $wid, $value): void
    {
        global $db;

        //TODO: error handling in update

        if (empty($value)) {
            $sqlquery1 = "UPDATE LMB_ATTRIBUTE_D SET VALUE_STRING = '', VALUE_DATE = " . LMB_DBDEF_NULL . ", VALUE_NUM = " . LMB_DBDEF_NULL . "  WHERE LMB_ATTRIBUTE_D.W_ID = $wid AND LMB_ATTRIBUTE_D.TAB_ID = $tabId AND LMB_ATTRIBUTE_D.FIELD_ID = $fieldId AND LMB_ATTRIBUTE_D.DAT_ID = $id";
            lmbdb_exec($db, $sqlquery1);
            return;
        }

        $type = $value[0];
        $value = substr($value, 1);

        $update_field = '';
        switch ($type) {
            case 's':
                $update_field = 'VALUE_STRING';
                $value = "'$value'";
                break;
            case 'd':
                $update_field = 'VALUE_DATE';
                $value = "'$value'";
                break;
            case 'n':
                $update_field = 'VALUE_NUM';
                break;
        }

        if (empty($update_field)) {
            return;
        }


        $sqlquery1 = "UPDATE LMB_ATTRIBUTE_D SET $update_field = $value WHERE LMB_ATTRIBUTE_D.W_ID = $wid AND LMB_ATTRIBUTE_D.TAB_ID = $tabId AND LMB_ATTRIBUTE_D.FIELD_ID = $fieldId AND LMB_ATTRIBUTE_D.DAT_ID = $id";
        lmbdb_exec($db, $sqlquery1);
    }

}
