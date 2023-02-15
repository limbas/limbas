<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */


class LimbasRecord extends LimbasComponent {
	/*
	 * field types
	 */
	const FT_SELECTION = 4;
	const FT_UPLOAD    = 6;
	const FT_RELATION  = 11;

	/*
	 * data types
	 */
	const DT_SELECTION_SELECT      = 12;
	const DT_SELECTION_RADIO       = 14;
	const DT_SELECTION_CHECKBOX    = 18;
	const DT_SELECTION_MULTISELECT = 31;
	const DT_SELECTION_NEWWIN      = 32;

	const DT_RELATION_1N = 27;
	const DT_RELATION_NM = 24;

	/*
	 * allowed parse types
	 */
	const PT_INTEGER = 1;
	const PT_TEXT    = 2;
	const PT_BOOLEAN = 3;
	const PT_DATE    = 4;
	const PT_TIME    = 5;
	const PT_FLOAT   = 6;

	/*
	 * relation types
	 */
	const RT_FORWARD  = 1;
	const RT_BACKWARD = 2;
	
	/*
	 * operation types
	 */
	const OT_LESS         = 'lt';
	const OT_LESSEQUAL    = 'le';
	const OT_EQUAL        = 'eq';
	const OT_GREATEREQUAL = 'ge';
	const OT_GREATER      = 'gt';
	const OT_NOTEQUAL     = 'ne';
	const OT_LIKE         = 'like';
	const OT_START        = 'start';
	const OT_AND          = 'and';
	const OT_OR           = 'or';
	
	private static $OPERATIONS = array(
		'txt' => array(self::OT_LIKE => 1,self::OT_EQUAL => 2, self::OT_START => 3),
		'num' => array(self::OT_EQUAL => 1, self::OT_GREATER => 2, self::OT_LESS => 3, self::OT_LESSEQUAL => 4, self::OT_GREATEREQUAL => 5, self::OT_NOTEQUAL => 6),
		'logical' => array(self::OT_AND => 1, self::OT_OR => 2)
	);

	/*
	 * sort direction
	 */
	const SD_ASC  = 'asc';
	const SD_DESC = 'desc';

	/* load record with all relations */
	const EAGER_LOADING = 'eager';
	/* load record without relations */
	const LAZY_LOADING = 'lazy';
	
	/** @var array one master object for each table */
	private static $masterObjects = array();
	
	/** @var mixed[] the attribute values */
	protected $attributes = array();
	/** @var mixed[] the attribute definitions */
	protected $definitions = array();
	/** @var string[] the attribute ids */
	protected $attributeIds = array();
	/** @var string[] the name of all attributes the user wants to use */ 
	protected $usableAttributes = null;
	/** @var string[] mapping from attribute id to attribute name */
	protected $mappings = array();
	/** @var string name of the table */
	protected $_table = '??';
	/** @var int Limbas id of the table */
	protected $_tableId  = -1;
	/** @var int Limbas id of this database record */
	protected $_id = -1;
	/** @var boolean true if this is a new record, otherwise false */
	protected $_new = true;
	/** @var boolean true if this record was changed, otherwise false */
	protected $_changed = false;
	/** @var boolean true if this record was read from relation, otherwise false */
	protected $_fromRelation = false;
	/** @var string how to load relations */
	protected $_loading = LimbasRecord::LAZY_LOADING;
	/** @var string[] meta data, filled after each find */ 
	protected $_findMetaData = array();
	
	
	/**
	 * Returns the model of this LimbasRecord class
	 *
	 * @param string $tableName Limbas table name
	 * @param array $attributes Attributes to use
	 * @return LimbasRecord record instance
	 */
	public function __construct($table, $attributes=null) {
		parent::__construct();
		$this->_table = lmb_strtoupper($table);
		$this->log('LimbasRecord(' . $this->refId . ').__construct ' . $this->_table);
		$this->createAttributes($attributes);
	}
	
	/**
	 * Returns the static model of the specified LimbasRecord class.
	 * The model returned is a static instance of the LimbasRecord class.
	 * It is provided for invoking class-level methods (something similar to static
	 * class methods.)
	 *
	 * EVERY derived LimbasRecord class must write a method as follows,
	 * <pre>
	 * public static function createModel($attributes=null)
	 * {
	 * 	return parent::model('<theTableName>', $attributes);
	 * }
	 * </pre>
	 *
	 * @param string $tableName Limbas table name
	 * @param array $attributes attributes to use
	 * @return LimbasRecord record model instance
	 */
	public static function model($tableName, $attributes=null) {
		$tableName = ucfirst(lmb_strtolower($tableName));
		LimbasLogger::log('LimbasRecord::model(' . $tableName . ')');
		if (!isset(self::$masterObjects[$tableName])) {
			if (class_exists('Limbas' . $tableName)) {
				$className = 'Limbas' . $tableName;
				self::$masterObjects[$tableName] = new $className();
			}
			else {
				self::$masterObjects[$tableName] = new self($tableName);
			}
		}
		$newObject = clone self::$masterObjects[$tableName];
		$newObject->setUsableAttributes($attributes);
		$newObject->resetAttributes();
		LimbasLogger::log('LimbasRecord::model(' . $tableName . ') newObject ' . $newObject->refId);
		return $newObject;
	}

	/**
	 * Set the loading method for related records
	 * 
	 * @see LimbasRecord::EAGER_LOADING eager loading
	 * @see LimbasRecord::LAZY_LOADING lazy loading
	 * 
	 * @param String $loading 
	 */
	public function setLoading($loading) {
		$this->_loading = $loading;
	}

	/**
	 * Finds all records satisfying the specified criteria.
	 * 
	 * @param mixed[] $criteria criteria
	 * @return LimbasRecord[] list of records satisfying the specified criteria. An
	 * empty array is returned if none is found
	 */
	public function findAll($criteria) {
		$this->log('LimbasRecord(' . $this->refId . ').findAll ' . print_r($criteria, true), 'trace');
		$params = array();
		$params[0] = array();
		$id = $this->_tableId;
		if (isset($criteria['attr'])) {
			$params[0]['gsr'] = $this->handleCriteriaAttr($criteria['attr']);
/*
			$gsr = array();
			$gsr[$id] = array();
			foreach ($criteria['attr'] as $key => $value) {
				$key = lmb_strtoupper($key);
				list($key, $relfield) = explode('.', $key);
				//echo 'key: ' . var_export($key, true) . ', relfield: ' . var_export($relfield, true) . '<br/>';
				//echo 'FIELD: ' . $key . '(' . $this->definitions[$key]['parse_type'] . ') : ' . print_r($value, true) . '<br/>';
				if (isset($this->definitions[$key]) && !$this->isRelation($key)) {
					$fieldid = $this->definitions[$key]['id'];
					$gsr[$id][$fieldid] = array();
					if (is_array($value)) {
						$i = 0;
						foreach($value as $vkey => $vpart) {
							//echo 'VALUE: ' . $vkey . '  ' . print_r($vpart, true) . '<br/>';
							if (is_array($vpart)) {
								$gsr[$id][$fieldid][$i] = utf8_decode($vkey);
								if (isset($vpart['operator'])) {
									if (LimbasRecord::PT_TEXT == $this->definitions[$key]['parse_type'] && isset(LimbasRecord::$OPERATIONS['txt'][$vpart['operator']])) {
										$gsr[$id][$fieldid]['txt'][0] = LimbasRecord::$OPERATIONS['txt'][$vpart['operator']];
									}
									elseif (isset(LimbasRecord::$OPERATIONS['num'][$vpart['operator']])) {
										$gsr[$id][$fieldid]['num'][0] = LimbasRecord::$OPERATIONS['num'][$vpart['operator']];
									}
									else {
										throw new LimbasRecordException('Operator ' . $vpart['operator'] . ' not allowed for attribute ' . $key);
									}
								}
								if ($i+1 < count($value)) {
									if (isset($vpart['logical']) && isset(LimbasRecord::$OPERATIONS['logical'][$vpart['logical']])) {
										$gsr[$id][$fieldid]['andor'][$i+1] = LimbasRecord::$OPERATIONS['logical'][$vpart['logical']];
									}
									else {
										$gsr[$id][$fieldid]['andor'][$i+1] = LimbasRecord::$OPERATIONS['logical'][self::OT_OR];
									}
								}
							}
							else {
								$gsr[$id][$fieldid][$i] = utf8_decode($value[$vkey]);
								if ($i+1 < count($value)) {
									$gsr[$id][$fieldid]['andor'][$i+1] = LimbasRecord::$OPERATIONS['logical']['or'];
								}
							}
							$i++;
						}
					}
					else {
						$gsr[$id][$fieldid][0] = utf8_decode($value);
					}
				}
				elseif (!is_null($relfield)) {
					$relobj = LimbasRecord::model($this->definitions[$key]['verkntabname']);
					if ($relobj->isAttribute($relfield)) {
						if (!is_array($value)) {
							$gsr[$this->definitions[$key]['verkntabid']] = array();
							$gsr[$this->definitions[$key]['verkntabid']][$relobj->getDefinition($relfield)['id']] = array();
							$gsr[$this->definitions[$key]['verkntabid']][$relobj->getDefinition($relfield)['id']][0] = utf8_decode($value);

							if (is_null($this->attributes[$key])) {
								$this->attributes[$key] = new LimbasRelation(array_merge(array('tabname' => $this->_table), $this->definitions[$key]));
							}
							$this->attributes[$key]->addCriteria($relfield, $value);
						}
						else {
							throw new LimbasRecordException('Array as value not allowed in criteria ' . $key . '.' . $relfield . '!');
						}
					}
					else {
						throw new LimbasRecordException('Unknown attribute ' . $relfield . ' in table ' . $this->definitions[$key]['verkntabname'] . '!');
					}
				}
				else {
					throw new LimbasRecordException('Unknown attribute ' . $key . ' or a relation!');
				}
			}
			//echo '<pre>' . print_r($gsr, true) . '</pre>'; 
			$params[0]['gsr'] = $gsr;								# array of search requests
*/
		}
		else {
			
		}
/*
		if (isset($criteria['relation'])) {
			$relation = LimbasRecord::model($criteria['relation']['table']);
			$params[0][$id]['relation']['gtabid'] = $this->_definitions['tables'][$relation->_table]['id'];
			$params[0][$id]['relation']['fieldid'] = $relation->getFieldIdByName($criteria['relation']['field']);
			$params[0][$id]['relation']['ID'] = $criteria['relation']['ID'];
		}
		if (isset($criteria['function'])) {
			foreach($criteria['function'] as $func => $vals)
			{
				$params[0][$id]['extension'][$func] = $vals;
			}
		}
		if (isset($criteria['reminder'])) {
			$params[0][$id]['reminder'] = $criteria['reminder'];
			if (isset($criteria['remindergroup'])) {
				$params[0][$id]['reminder_group'] = $criteria['remindergroup'];
			}
			if (isset($criteria['reminderuser'])) {
				$params[0][$id]['reminder_user'] = $criteria['reminderuser'];
			}
			if (isset($criteria['reminderdate'])) {
				$params[0][$id]['reminder_date'] = $criteria['reminderdate'];
			}
			if (isset($criteria['reminderfrom'])) {
				$params[0][$id]['reminder_from'] = $criteria['reminderfrom'];
			}
			if (isset($criteria['reminderto'])) {
				$params[0][$id]['reminder_to'] = $criteria['reminderto'];
			}
			if (isset($criteria['remindercreate'])) {
				$params[0][$id]['reminder_create'] = $criteria['remindercreate'];
			}
		}
*/
		if (isset($criteria['sort'])) { // !isset($criteria['sort']['function'])
			foreach($criteria['sort'] as $key => $sort) {
				$key = lmb_strtoupper($key);
				if ($this->isAttribute($key) && (self::SD_ASC === $sort || self::SD_DESC === $sort)) {
					$order[] = $this->definitions[$key]['id'] . ',' . $sort;
				}
			}
			$params[0][$id]['order'] = $order;
		}
		if (isset($criteria['page'])) {
			$params[0][$id]['res_next'] = $criteria['page'] + 1;
		}

		$params[0]['getvars'] = array('fresult');				# return result arrays, you can use (fresult, gtab, gfield, umgvar). fresult is needed for resultsets
		$params[0]['action'] = 'gtab_erg';						# you can use tables [gtab_erg] or filemanager [explorer_main]
		$params[0]['gtabid'] = $id;							# ID of requested table
		$params[0][$id]['showfields'] = implode(',', $this->attributeIds);			# IDs of requested fields in table

		if (LimbasRecord::EAGER_LOADING == $this->_loading) {
			foreach ($this->definitions as $name => $definition) {
				if ($definition['use'] && LimbasRecord::FT_RELATION == $definition['field_type']) {
					$refmodel = LimbasRecord::model($definition['verkntabname']);
					$params[0][$definition['verkntabid']]['showfields'] = implode(',', $refmodel->attributeIds);
					$this->log('LimbasRecord(' . $this->refId . ').findAll ' . implode(',', $refmodel->attributeIds));
				}
			}
		}
		else {
/*
			foreach ($this->definitions as $name => $definition) {
				if ($definition['use'] && LimbasRecord::FT_RELATION == $definition['field_type']) {
					//echo $name . ': ' . print_r($definition, true) . "\n";
					$params[0][$definition['verkntabid']]['showfields'] = $definition['verknfieldid'];
				}
			}
*/
		}
		
		//$params[0][$id]["res_next"] = 1;					# current page
		if (isset($criteria['limit'])) {
			$params[0][$id]["count"] = $criteria['limit'];
		}
		else {
			$params[0][$id]["count"] = 'all';
			$params[0][$id]["nolimit"] = '1';
		}
		if (isset($criteria['nolimit']) && $criteria['nolimit']) {
			$params[0][$id]["nolimit"] = '1';
		}
		
		$this->log('LimbasRecord(' . $this->refId . ').findAll Action:' . print_r($params, true));
		$result = parse_action($params);
		$this->trace('LimbasRecord(' . $this->refId . ').findAll Result:' . print_r($result, true));
		unset($params);
		if (isset($result[0]['fresult'][$id])) {
			$this->_findMetaData = array();
			$this->_findMetaData ['page'] = $result[0]['result']['page'][$id];
			$this->_findMetaData ['max'] = $result[0]['result']['max_count'][$id];
			$this->_findMetaData ['res'] = $result[0]['result']['res_count'][$id];
			return $this->handleRecords($result[0]['fresult'][$id]);
		}
		$this->_findMetaData = null;
		return array();
	}
	
	/**
	 * Finds the record satisfying the specified id.
	 * @param int $id the record id
	 * @return LimbasRecord|null record satisfying the specified id. Null if none is found
	 */
	public function findById($id) {
		$this->log('LimbasRecord(' . $this->refId . ').findById(' . $id . ')');
		$params = array();
		$params[0] = array();
		$tid = $this->tableid;
		$params[0][$tid]['ID'] = $id;	
		$params[0]['getvars'] = array('fresult');				# return result arrays, you can use (fresult, gtab, gfield, umgvar). fresult is needed for resultsets
		$params[0]['action'] = 'gtab_erg';						# you can use tables [gtab_erg] or filemanager [explorer_main]
		$params[0]['gtabid'] = $tid;							# ID of requested table
		$params[0][$tid]['showfields'] = implode(',', $this->attributeIds);			# IDs of requested fileds in table

		if (LimbasRecord::EAGER_LOADING == $this->_loading) {
			foreach ($this->definitions as $name => $definition) {
				if ($definition['use'] && LimbasRecord::FT_RELATION == $definition['field_type']) {
					$refmodel = LimbasRecord::model($definition['verkntabname']);
					$params[0][$definition['verkntabid']]['showfields'] = implode(',', $refmodel->attributeIds);
					$this->log('LimbasRecord(' . $this->refId . ').findById ' . implode(',', $refmodel->attributeIds));
				}
			}
		}
		else {
/*
 * no longer needed!!
 * 
			foreach ($this->definitions as $name => $definition) {
				if ($definition['use'] && LimbasRecord::FT_RELATION == $definition['field_type']) {
					//echo $name . ': ' . print_r($definition, true) . "\n";
					$params[0][$definition['verkntabid']]['showfields'] = $definition['verknfieldid'];
				}
			}
*/
		}
		$this->log('LimbasRecord::findById Action:' . print_r($params, true));
		$result = parse_action($params);
		$this->trace('LimbasRecord::findById Result:' . print_r($result, true));
		if (isset($result[0]['fresult'][$tid])) {
			$this->_findMetaData = array();
			$this->_findMetaData ['page'] = $result[0]['result']['page'][$id];
			$this->_findMetaData ['max'] = $result[0]['result']['max_count'][$id];
			$this->_findMetaData ['res'] = $result[0]['result']['res_count'][$id];
			$records = $this->handleRecords($result[0]['fresult'][$tid]);
			if (1 < lmb_count($records)) {
				throw new LimbasRecordException('Found ' . lmb_count($records) . ' ' . get_class($this). ' with same ID (' . $id . ')!');
			}
			elseif (0 == lmb_count($records)) {
				return null;
			}
			return $records[0];
		}
		$this->_findMetaData = null;
		return null;
	}

	/**
	 * 
	 * @param string $fieldname
	 * @return LimbasRecord[] records coming through the relation represented by the attribute name ($fieldname)
	 */
	protected function findRelationalRecords($fieldname, $attributes=null, $criteria=null) {
		$this->log('LimbasRecord(' . $this->refId . ').findRelationalRecords(' . $fieldname . ', ' . 
				var_export($attributes, true) . ', ' . var_export($criteria, true) . ')');
		$params = array();
		$params[0] = array();
		if (!is_null($criteria)) {
			$params[0]['gsr'] = $this->handleCriteriaAttr($criteria['attr']);
		}
		$other = LimbasRecord::model($this->definitions[$fieldname]['verkntabname'], $attributes);
		$otherid = $this->definitions[$fieldname]['verkntabid'];
		
		$params[0][$otherid] = array();
		$params[0][$otherid]['relation'] = array();
		$params[0][$otherid]['relation']['gtabid'] = $this->_tableId;
		$params[0][$otherid]['relation']['fieldid'] = $this->definitions[$fieldname]['id'];
		$params[0][$otherid]['relation']['ID'] = $this->_id;
		$params[0][$otherid]['showfields'] = implode(',', $other->getAttributeIds());			# IDs of requested fileds in table
		$params[0][$otherid]['count'] = 'all';
		$params[0][$otherid]['nolimit'] = '1';
		$params[0]['getvars'] = array('fresult');				# return result arrays, you can use (fresult, gtab, gfield, umgvar). fresult is needed for resultsets
		$params[0]['action'] = 'gtab_erg';						# you can use tables [gtab_erg] or filemanager [explorer_main]
		$params[0]['gtabid'] = $otherid;							# ID of requested table

		if (LimbasRecord::EAGER_LOADING === $this->_loading) {
			foreach ($this->definitions as $name => $definition) {
				if ($definition['use'] && LimbasRecord::FT_RELATION === $definition['field_type']) {
					$refmodel = LimbasRecord::model($definition['verkntabname']);
					$params[0][$definition['verkntabid']]['showfields'] = implode(',', $refmodel->attributeIds);
					$this->log('LimbasRecord(' . $this->refId . ').findAll ' . implode(',', $refmodel->attributeIds));
				}
			}
		}
		else {
			foreach ($this->definitions as $name => $definition) {
				if ($definition['use'] && LimbasRecord::FT_RELATION === $definition['field_type']
//						&&
//					$this->_tableId != $definition['verkntabid']) // keine rückwertige Verknüpfung
				)
				{
					//echo $name . ': ' . print_r($definition, true) . "\n";
					$params[0][$definition['verkntabid']]['showfields'] = $definition['verknfieldid'];
				}
			}
		}

		$this->log('LimbasRecord::findRelationalRecords Action:' . print_r($params, true));
		$result = parse_action($params);
		$this->trace('LimbasRecord::findRelationalRecords Result:' . print_r($result, true));
		if (isset($result[0]['fresult'][$otherid])) {
			$this->_findMetaData = array();
			$this->_findMetaData ['page'] = $result[0]['result']['page'][$id];
			$this->_findMetaData ['max'] = $result[0]['result']['max_count'][$id];
			$this->_findMetaData ['res'] = $result[0]['result']['res_count'][$id];
			return $other->handleRecords($result[0]['fresult'][$otherid]);
		}
		$this->_findMetaData = null;
		return array();
	}

	/**
	 * Get the meta data filled after a find call
	 * 
	 * <pre>
	 * array (
	 * 	'page' => int,	// ??next page
	 * 	'max' => int,	// ??max count of records
	 * 	'res' => int	// ??max count of records
	 * )
	 * </pre>
	 * @return string[]|null the meta data or null if there was no previous find call
	 */
	public function getFindMetaData() {
		return $this->_findMetaData;
	}

	/**
	 * Create SOAP like array of search criteria
	 * 
	 * @param string[] $criteriaAttr 
	 * @throws LimbasRecordException if operator is not allowed or attribute not defined in table
	 * @return mixed[] 
	 */
	protected function handleCriteriaAttr($criteriaAttr) {
		$tid = $this->_tableId;
		$gsr = array();
		$gsr[$tid] = array();
		foreach ($criteriaAttr as $key => $value) {
			$key = lmb_strtoupper($key);
			list($key, $relfield) = explode('.', $key);
			//echo 'key: ' . var_export($key, true) . ', relfield: ' . var_export($relfield, true) . '<br/>';
			//echo 'FIELD: ' . $key . '(' . $this->definitions[$key]['parse_type'] . ') : ' . print_r($value, true) . '<br/>';
			if (isset($this->definitions[$key]) && !$this->isRelation($key)) {
				$fieldid = $this->definitions[$key]['id'];
				$gsr[$tid][$fieldid] = array();
				if (is_array($value)) {
					$i = 0;
					foreach($value as $vkey => $vpart) {
						//echo 'VALUE: ' . $vkey . '  ' . print_r($vpart, true) . '<br/>';
						if (is_array($vpart)) {
							$gsr[$tid][$fieldid][$i] = utf8_decode($vkey);
							if (isset($vpart['operator'])) {
								if (LimbasRecord::PT_TEXT == $this->definitions[$key]['parse_type'] && isset(LimbasRecord::$OPERATIONS['txt'][$vpart['operator']])) {
									$gsr[$tid][$fieldid]['txt'][0] = LimbasRecord::$OPERATIONS['txt'][$vpart['operator']];
								}
								elseif (isset(LimbasRecord::$OPERATIONS['num'][$vpart['operator']])) {
									$gsr[$tid][$fieldid]['num'][0] = LimbasRecord::$OPERATIONS['num'][$vpart['operator']];
								}
								else {
									throw new LimbasRecordException('Operator ' . $vpart['operator'] . ' not allowed for attribute ' . $key);
								}
							}
							if ($i+1 < lmb_count($value)) {
								if (isset($vpart['logical']) && isset(LimbasRecord::$OPERATIONS['logical'][$vpart['logical']])) {
									$gsr[$tid][$fieldid]['andor'][$i+1] = LimbasRecord::$OPERATIONS['logical'][$vpart['logical']];
								}
								else {
									$gsr[$tid][$fieldid]['andor'][$i+1] = LimbasRecord::$OPERATIONS['logical'][self::OT_OR];
								}
							}
						}
						else {
							$gsr[$tid][$fieldid][$i] = utf8_decode($value[$vkey]);
							if ($i+1 < lmb_count($value)) {
								$gsr[$tid][$fieldid]['andor'][$i+1] = LimbasRecord::$OPERATIONS['logical']['or'];
							}
						}
						$i++;
					}
				}
				else {
					$gsr[$tid][$fieldid][0] = utf8_decode($value);
				}
			}
			elseif (!is_null($relfield)) {
				$relobj = LimbasRecord::model($this->definitions[$key]['verkntabname']);
				if ($relobj->isAttribute($relfield)) {
					if (!is_array($value)) {
						$gsr[$this->definitions[$key]['verkntabid']] = array();
//						$gsr[$this->definitions[$key]['verkntabid']][$relobj->getDefinition($relfield)['id']] = array();
//						$gsr[$this->definitions[$key]['verkntabid']][$relobj->getDefinition($relfield)['id']][0] = utf8_decode($value);

						if (is_null($this->attributes[$key])) {
							$this->attributes[$key] = new LimbasRelation(array_merge(array('tabname' => $this->_table), $this->definitions[$key]));
						}
						$this->attributes[$key]->addCriteria($key . '.' . $relfield, $value);
						//echo $key . '.' . $relfield . ' criteria: ' . var_export($this->attributes[$key]->getCriteria(), true);
					}
					else {
						throw new LimbasRecordException('Array as value not allowed in criteria ' . $key . '.' . $relfield . '!');
					}
				}
				else {
					throw new LimbasRecordException('Unknown attribute ' . $relfield . ' in table ' . $this->definitions[$key]['verkntabname'] . '!');
				}
			}
			else {
				throw new LimbasRecordException('Unknown attribute ' . $key . ' or a relation!');
			}
		}
		//echo '<pre>' . print_r($gsr, true) . '</pre>';
		return $gsr;

	}

	/**
	 * Create array of LimbasRecords from SOAP result array
	 * 
	 * @param mixed[] $records SOAP result array
	 * @return LimbasRecord[]
	 */
	protected function handleRecords($records) {
		$retval = array();
		$this->log('LimbasRecord(' . $this->refId . ').handleRecords ' . $this->_loading);
		if (0 < lmb_count($records)) {
			foreach ($records as $rid => $record) {
				//echo 'handleRecord: ' . print_r($record, true);
				//$item = clone $this; 
				$item = LimbasRecord::model($this->_table, $this->usableAttributes);
				//$item = new LimbasRecord($this->table);
				//$item->setDefinitions($this->definitions);
				//$item->setAttributes($this->attributes);
				foreach ($record as $id => $value) {
					$fieldname = $this->mappings[$id];
					if (!$this->isUsableAttribute($fieldname)) {
						continue;
					}
					$this->trace('LimbasRecord(' . $this->refId . ').handleRecords ' . $this->_table . '.' . $fieldname . ' => ' . $value);

					if (LimbasRecord::FT_RELATION == $this->definitions[$fieldname]['field_type']) {
						if (LimbasRecord::LAZY_LOADING === $this->_loading) {
							if (is_array($value)) {
/*
								$values = array();
								foreach($value[$this->definitions[$fieldname]['verkntabid']] as $relrecord) {
									foreach($relrecord as $relid => $relvalue) {
										$values[] = $relvalue;
									}
								}
*/
								if (is_null($item->attributes[$fieldname])) {
									$item->attributes[$fieldname] = new LimbasRelation(array_merge(array('tabname' => $this->_table), $this->definitions[$fieldname]));
									if (!is_null($this->attributes[$fieldname])) {
										$item->attributes[$fieldname]->setCriteria($this->attributes[$fieldname]->getCriteria());
									} 
								}
								$item->attributes[$fieldname]->setStrings($value);
							}
						}
						else {
							$relitem = LimbasRecord::model($this->definitions[$fieldname]['verkntabname']);
							//$relitem = new LimbasRecord($this->definitions[$fieldname]['verkntabname']);
							$reldefs = $relitem->getDefinitions();
							$relmaps = $relitem->getMappings();
// 							if (CLimbasRecord::HAS_ONE == $relation[0]) {
// 								$item->_related[$name] = null;
// 							}
// 							else {
// 								$item->_related[$name] = array();
// 							}
							if (is_array($value)) {
								$objects = array();
								foreach($value[$this->definitions[$fieldname]['verkntabid']] as $relrecord) {
									$relitem = LimbasRecord::model($this->definitions[$fieldname]['verkntabname']);
									foreach($relrecord as $relid => $relvalue) {
										$relfieldname = $relmaps[$relid];
										$attr = $reldefs[$relfieldname]['name'];
										//echo $attr . ', ' . $reldefs[$relfieldname]['field_type'] . '<br/>';
										if (is_array($relvalue)) {
											$newvals = array();
											foreach($relvalue as $key => $val) {
												//echo '<pre>' . $key . print_r($val, true) . '</pre>';
												if (LimbasRecord::FT_UPLOAD == $reldefs[$relfieldname]['field_type'] && is_array($val)) {
													$newvals2 = array();
													foreach($val as $key2 => $val2) {
														$newvals2[$key2] = utf8_encode(html_entity_decode($val2));
													}
													$newvals[$key] = $newvals2;
												}
												else {
													$newvals[] = utf8_encode(html_entity_decode($val));
												}
											}
											$relitem->$attr = $newvals;
										}
										else {
											$relitem->$attr = utf8_encode(html_entity_decode($relvalue));
										}
									}
									//echo '<pre>' . get_class($relation) . print_r($relitem, true) . '</pre>';
									$relitem->_new = false;
									$relitem->_fromRelation = true;
									$objects[] = $relitem;
								}
								if (is_null($item->attributes[$fieldname])) {
									$item->attributes[$fieldname] = new LimbasRelation(array_merge(array('tabname' => $this->_table), $this->definitions[$fieldname]));
								}
								$item->attributes[$fieldname]->setObjects($objects);
								$this->log('LimbasRecord(' . $this->refId . ').handleRecords objectcount: ' . lmb_count($item->attributes[$fieldname]->getObjects()));
							}
						}
					}
					elseif (LimbasRecord::FT_SELECTION == $this->definitions[$fieldname]['field_type'])
					{
						if (LimbasRecord::DT_SELECTION_SELECT == $this->definitions[$fieldname]['data_type'] ||
								LimbasRecord::DT_SELECTION_RADIO == $this->definitions[$fieldname]['data_type']) {
							// Auswahl (Single Select)
							if (is_array($value) && 1 == lmb_count($value)) {
								$vals =  array_values($value);
								$item->attributes[$fieldname] = utf8_encode(html_entity_decode($vals[0]));
							}
							else {
								$item->attributes[$fieldname] = utf8_encode(html_entity_decode($value));
							}
						}
						elseif (LimbasRecord::DT_SELECTION_CHECKBOX == $this->definitions[$fieldname]['data_type'] ||
								LimbasRecord::DT_SELECTION_MULTISELECT == $this->definitions[$fieldname]['data_type'] ||
								LimbasRecord::DT_SELECTION_NEWWIN == $this->definitions[$fieldname]['data_type']) {
							// Auswahl (Multi Select)
							if (is_array($value)) {
								$this->log('LimbasRecord(' . $this->refId . ').handleRecords ' . $fieldname . ' IS_ARRAY ' . var_export($value, true));
								$newvals = array();
								foreach($value as $key => $val) {
									if ('keyword' === $key) {
										continue;
									}
									if (is_array($val)) {
										$this->log('LimbasRecord(' . $this->refId . ').handleRecords ' . $fieldname . ' IS_ARRAY ' . var_export($val, true));
										throw new LimbasRecordException('Array of subvalues (' . $key . ' =&gt; ' . var_export($val, true) . ') not allowed in field ' . $fieldname . ' of table ' . $this->table . '!');
									}
									$newvals[] = utf8_encode(html_entity_decode($val));
								}
								$item->attributes[$fieldname] = $newvals;
							}
							else {
								$item->attributes[$fieldname] = utf8_encode(html_entity_decode($value));
							}
						}
						else {
							$item->attributes[$fieldname] = utf8_encode(html_entity_decode($value));
						}
					}
					else {
						// normal data
						$item->attributes[$fieldname] = utf8_encode(html_entity_decode($value));
					}
				}
				$item->_new = false;
				$item->_id = $rid;
				$retval[] = $item;
			}
			unset($records);
			$records = array();
		}
		return $retval;
	}

	/**
	 * Save this record
	 * 
	 * If it is a new record insert() will be called, otherwise update()
	 * 
	 * @param boolean $runValidation whether to run the validation 
	 * @param string[] $attributes attributes to save
	 * @return boolean true if save was ok, otherwise false
	 * @throws LimbasRecordException from a saveRelation call
	 */
	public function save($runValidation=true, $attributes=null) {
		$retval = false;
		if (is_null($attributes)) {
			$attributes = array();
			foreach ($this->definitions as $definition) {
				$attributes[] = $definition['name'];
			}
		}
		elseif (!is_array($attributes)) {
			$attributes = array($attributes);
		}
		array_map('strtoupper', $attributes);
		
		if(!$runValidation || $this->validate($attributes)) {
			$retval = $this->_new ? $this->insert($attributes) : $this->update($attributes);
			if ($retval) {
				$this->_new = false;
				$this->_changed = false;
				foreach($attributes as $name) {
					if ($this->isUsableAttribute($name)) {
						$this->definitions[$name]['changed'] = false;
						if ('ID' != $name) {
							if (is_object($this->attributes[$name]) && is_a($this->attributes[$name], 'LimbasRelation')) {
								if ($this->attributes[$name]->hasObjects()) {
									LimbasRecord::startTransaction();
									foreach($this->attributes[$name]->getObjects() as $object) {
										if ($object->isChanged()) {
											if ($object->save()) {
												if (!$object->isFromRelation()) {
													$this->saveRelation($name, $object);
												}
											}
											else {
												LimbasRecord::rollbackTransaction();
												$retval = false;
												break;
											}
										}
									}
									if ($retval) {
										LimbasRecord::endTransaction();
									}
								}
							}
						}
					}
				}
			}
		}
		return $retval;
	}

	/**
	 * Validate a list of attributes
	 * 
	 * @param string[]|null $attributes the attributes to validate; null means all attributes
	 * @return boolean true if validation was ok, otherwise false
	 */
	public function validate($attributes=null) {
		// TODO: Implement validation for mandatory attributes
		return true;
	}
	
	/**
	 * Insert this record
	 *
	 * @param string $attributes
	 * @throws LimbasRecordException if record is not new
	 * @return boolean true if insert was ok, otherwise false
	 */
	protected function insert($attributes=null) {
		if(!$this->_new) {
			throw new LimbasRecordException('The record cannot be inserted within Limbas because it is not new.');
		}

		$params = array();
		$params[0] = array();
		$params[0]['gnup'] = array();
		$id = $this->_tableId;
		
		foreach($attributes as $name) {
			if ($this->isUsableAttribute($name)) {
				if ('ID' != $name) {
					if (is_array($this->attributes[$name])) {
						$data = array();
						foreach($this->attributes[$name] as $key => $value) {
							$data[$key] = utf8_decode($value);
						}
						$params[0]['gnup'][$this->definitions[$name]['id']] = $data;
					}
					elseif (!is_object($this->attributes[$name])) {
						$params[0]['gnup'][$this->definitions[$name]['id']] = utf8_decode($this->attributes[$name]);
					}
				}
			}
			else {
				throw new LimbasRecordException('insert: Unknown field "' . $name . '"!');
			}
		}
		$params[0]["action"] = "gtab_new";
		$params[0]["gtabid"] = $id;
		//echo '<pre>' . print_r($params, true) . '</pre>';
		$this->log('LimbasRecord(' . $this->refId . ').insert Action:' . print_r($params, true));
		$result = parse_action($params);
		$this->trace('LimbasRecord(' . $this->refId . '). Result:' . print_r($result, true));
		if (!isset($result[0])) {
			return false;
		}
		else {
			$this->_id = $result[0];
			$this->ID = $result[0];
			return true;
		}
	}

	/**
	 * Update this record
	 *
	 * @param string $attributes
	 * @throws LimbasRecordException if record is new
	 * @return boolean true if update was ok, otherwise false
	 */
	protected function update($attributes=null) {
		if($this->_new) {
			throw new LimbasRecordException('The record cannot be updated within Limbas because it is new.');
		}

		$params = array();
		$params[0] = array();
		$params[0]['gup'] = array();
		if (is_null($attributes)) {
			$attributes = array();
			foreach ($this->getAttributes() as $attribute) {
				$attributes[] = $attribute['name'];
			}
		}
		elseif (!is_array($attributes)) {
			$attributes = array($attributes);
		}
		foreach($attributes as $name) {
			//echo '<pre>' . print_r($attribute, true) . '</pre>';
			if ($this->isUsableAttribute($name)) {
				if ('ID' != $name) {
					if (is_array($this->$name)) {
						$data = array();
						foreach($this->$name as $key => $value) {
							$data[$key] = utf8_decode($value);
						}
						$params[0]['gup'][$this->_tableId . ',' . $this->definitions[$name]['id'] . ',' . $this->_id] = $data;
					}
					else {
						$params[0]['gup'][$this->_tableId . ',' . $this->definitions[$name]['id'] . ',' . $this->_id] = utf8_decode($this->$name);
					}
				}
			}
			else {
				throw new LimbasRecordException('update: Unknown field "' . $name . '"!');
			}
		}
		$params[0]["action"] = "gtab_change";
		$params[0]["gtabid"] = $this->_tableId;
		//echo '<pre>' . print_r($params, true) . '</pre>';
		$this->log('LimbasRecord(' . $this->refId . ').update Action: ' . print_r($params, true));
		$result = parse_action($params);
		$this->trace('LimbasRecord(' . $this->refId . '). Result:' . print_r($result, true));
		if (!isset($result[0])) {
			return false;
		}
		else {
			return 1 == $result[0];
		}
	}

	/**
	 * Save a relation from this object to other 
	 * 
	 * @param String $field name of the field
	 * @param LimbasRecord $other object to relate with
	 * @throws LimbasRecordException
	 * @return boolean true if the relation was saved, otherwise false
	 */
	public function saveRelation($field, &$other) {
		$params = array();
		$params[0] = array();
		$params[0]['gnup'] = array();
		$params[0]['gnup']["verknpf"][0] = 1;
		
		if (self::RT_FORWARD == $this->definitions[$field]['relation_type']) {
			$params[0]['gnup']["verkn_tabid"][0] = $this->_tableId;
			$params[0]['gnup']["verkn_fieldid"][0] = $this->definitions[$field]['id'];
			$params[0]['gnup']["verkn_ID"][0] = $this->_id;
			$params[0]['gnup']["verkn_add_ID"][0] = $other->_id;
		}
		elseif (self::RT_BACKWARD == $this->definitions['tables'][$model->table]['fields'][$field]['relation_type']) {
			throw new LimbasRecordException("Backward relations not implemented yet!");
		}
		
		$params[0]["action"] = "gtab_change";
		$params[0]["gtabid"] = $this->_tableId;
		$this->log('LimbasRecord(' . $this->refId . ').saveRelation Action: ' . print_r($params, true));
		$result = parse_action($params);
		$this->trace('LimbasRecord(' . $this->refId . ').saveRelation Result:' . print_r($result, true));
		if (isset($result['error'])) {
			throw new LimbasRecordException($result['error'][0][0]);
		}
		return true;
	}

	/**
	 * Delete this record
	 *
	 * @throws LimbasRecordException if error string comes back
	 * @return boolean true if delete was ok, otherwise false
	 */
	public function delete() {
		$this->log('LimbasRecord(' . $this->refId . ').delete ');
		$params = array();
		$params[0] = array();
		$params[0]["action"] = "gtab_delete";
		$params[0]["gtabid"] = $this->_tableId;
		$params[0]['id'] = $this->_id;
	
		//echo '<pre> params: ' . print_r($params, true) . '</pre>';
		$this->log('LimbasRecord(' . $this->refId . ').delete Action: ' . print_r($params, true));
		$result = parse_action($params);
		$this->trace('LimbasRecord(' . $this->refId . ').delete Result: ' . print_r($result, true));
		if (!is_array($result[0])) {
			return 1 == $result[0];
		}
		elseif (isset($result[0]['error'])) {
			throw new LimbasRecordException($result[0]['error']);
		}
		return false;
	}
	
	/**
	 * 
	 * @param unknown $attributes
	 */
	public function setAttributes($attributes) {
		$this->attributes = $attributes;
	}

	/**
	 * 
	 * @return multitype:
	 */
	public function getAttributes() {
		return $this->attributes;
	}
	
	/**
	 * 
	 * @return multitype:
	 */
	public function getAttributeIds() {
		return $this->attributeIds;
	}
	
	/**
	 * 
	 * @return multitype:
	 */
	public function getMappings() {
		return $this->mappings;
	}

	/**
	 * 
	 * @param unknown $definitions
	 */
	public function setDefinitions($definitions) {
		$this->definitions = $definitions;
	}
	
	/**
	 * 
	 * @return multitype:
	 */
	public function getDefinitions() {
		return $this->definitions;
	}
	
	/**
	 * 
	 * @param unknown $name
	 * @return multitype:|NULL
	 */
	public function getDefinition($name) {
		$name = lmb_strtoupper($name);
		if (isset($this->definitions[$name])) {
			return $this->definitions[$name];
		}
		return null;
	}
	
	/**
	 * 
	 * @param unknown $id
	 */
	public function setId($id) {
		$this->log('LimbasRecord.setId(' . $id . ')');
		$this->_id = $id;
	}

	/**
	 * 
	 * @return number
	 */
	public function getId() {
		return $this->_id;
	}

	/**
	 * 
	 * @return string
	 */
	public function getTable() {
		return $this->_table;
	}

	/**
	 * 
	 * @return number
	 */
	public function getTableId() {
		return $this->_tableId;
	}

	/**
	 * 
	 */
	public function getClassName() {
		return $this->className;
	}

	/**
	 * This magic method is called each time a variable is referenced from the object
	 *
	 * @param string $name the variable name
	 * @return mixed
	 * @see LimbasComponent::__get()
	 */
	public function __get($name) {
		$this->trace('LimbasRecord(' . $this->refId . ').__get(' . $this->_table . '.' . $name . ')');
		$attrname = lmb_strtoupper($name);
		//if(array_key_exists($attrname, $this->attributes)) {
		if(isset($this->definitions[$attrname])) {
			if (self::FT_RELATION == $this->definitions[$attrname]['field_type']) {
				if (is_null($this->attributes[$attrname])) {
					$this->attributes[$attrname] = new LimbasRelation(array_merge(array('tabname' => $this->_table), $this->definitions[$attrname]));
				}
				return $this->attributes[$attrname]->getStrings();
			}
			return $this->attributes[$attrname];
		}
		else {
			return parent::__get($name);
		}
	}

	/**
	 * This magic method is called each time a variable is set in the object
	 *
	 * @param string $name variable name
	 * @param mixed $value variable content
	 * @return void
	 * @see LimbasComponent::__set()
	 */
	public function __set($name, $value) {
		$this->trace('LimbasRecord(' . $this->refId . ').__set(' . $this->_table . '.' . $name . ', ' . $value . ')');
		$attrname = lmb_strtoupper($name);
		if ('id' === $name) {
			$this->setId($id);
			return;
		}
		//if(array_key_exists($attrname, $this->attributes)) {
		elseif(isset($this->definitions[$attrname])) {
			if ($this->attributes[$attrname] !== $value) {
				$this->attributes[$attrname] = $value;
				$this->definitions[$attrname]['changed'] = true;
				$this->_changed = true;
			}
		}
		else {
			parent::__set($name, $value);
		}
	}

	/**
	 * This magic method is invoked each time isset() is called on the object variable
	 *
	 * @param string $name variable name
	 * @return boolean true if the object variable is set, otherwise false
	 * @see LimbasComponent::__isset()
	 */
	public function __isset($name) {
		if (isset($this->attributes[$name])) {
			return true;
		}
		else {
			return parent::__isset($name);
		}
	}

	/**
	 * This magic method is invoked each time unset() is called on the object variable
	 *
	 * @param string $name variable name
	 * @see LimbasComponent::__unset()
	 */
	public function __unset($name) {
		if (isset($this->attributes[$name])) {
			unset($this->attributes[$name]);
		}
		else {
			parent::__unset($name);
		}
	}

	/**
	 * Calls the named method which is not a class method.
	 * 
	 * @param string $name the method name
	 * @param array $args method arguments
	 * @throws LimbasRecordException if a method with this name is not defined
	 * @return mixed the method return value
	 */
	public function __call($name, $args) {
		$this->log('LimbasRecord(' . $this->refId . ').__call(' . $name . ')');
		$name = lmb_strtoupper($name);
		if ($this->isAttribute($name)) {
			if ($this->isRelation($name)) {
				if (is_null($this->attributes[$name])) {
					$this->attributes[$name] = new LimbasRelation(array_merge(array('tabname' => $this->_table), $this->definitions[$name]));
				}
				if (!$this->_new) {
					$attributes = $args[0];
					$criteria = $args[1];
	
					$this->log('LimbasRecord(' . $this->refId . ').' . $name . '(attr: ' . var_export($attributes, true) . ', crit: ' . var_export($criteria, true) . ')');
					//echo '<pre>name ' . var_export($name, true) . '</pre>';
					//echo '<pre>attr ' . var_export($attributes, true) . '</pre>';
					//echo '<pre>crit ' . var_export($criteria, true) . '</pre>';
					
					if (is_null($criteria)) {
						$criteria = $this->attributes[$name]->getCriteria();
					}
					else {
						$this->attributes[$name]->setCriteria($criteria);
					}
					if (is_null($attributes)) {
						$attributes = $this->attributes[$name]->getAttributes();
					}
					else {
						$this->attributes[$name]->setAttributes($attributes);
					}
					$records = $this->findRelationalRecords($name, $attributes, $criteria);
					$this->attributes[$name]->setObjects($records);
				}
				return $this->attributes[$name];
			}
			else {
				throw new LimbasRecordException($name . ' ist not a callable relation!');
			}
		}
		else {
			throw new LimbasRecordException($name . ' ist not a callable attribute!');
		}
	}
	
	/**
	 * Returns the HTML String representation of this class
	 * 
	 * @return string
	 */
	public function __toString() {
		$this->trace('LimbasRecord(' . $this->refId . ').__toString');
		$str = '<table class="LimbasRecordToString">';
		$str .= '<tr><th>' . 
			get_class($this) . '</th><th>' . 
			$this->_table . ' (' . $this->_id . ')&nbsp;&nbsp;' . ($this->isNew() ? '<span title="isNew">N</span>&nbsp;' :'')  . ($this->isChanged() ? '<span title="isChanged">C</span>&nbsp;' :'') . '</th></tr>';
		$str .= '<tr><th>RefCount</th><th>' . $this->refId. '</th></tr>';
		$attrs = $this->attributes;
		ksort($attrs);
		foreach($attrs as $name => $data) {
			if (!$this->isUsableAttribute($name)) {
				continue;
			}
			//if ((isset($data) && (!is_array($data) && '' !== $data) || 0 < count($data))) {
			{
				$str .= '<tr><td class="label">' . $name . '&nbsp;</td>';
				$str .= '<td>';
				if (is_object($data) && 'LimbasRelation' === get_class($data)) {
					$log = $name . ' LimbasRelation(' . $this->refId . ') ' . lmb_count($data->getObjects() . ', ' . var_export($data->hasObjects(), true));
					$this->log($log);	
					if ($data->hasObjects()) {
						$this->log($name . ' hasObjects');
						foreach($data->getObjects() as $object) {
							$str .= $object;
						}
					}
					elseif ($data->hasStrings()) {
						$values = $data->getStrings();
						$str .= implode('<br/>', $values);
					}
					$str .= '</td></tr>';
				}
				elseif (!is_array($data)) {
					$str .= $data;
					$str .= '</td></tr>';
				}
			}
		}
		$str .= '</table>';
		return $str;
	}

	/**
	 * 
	 */
	public function __clone() {
		$refId = $this->refId;
		list($this->refId, $tmp) = LimbasComponent::refCount($this);
		$this->trace('LimbasRecord(' . $this->refId . ').__clone RefId old: ' . $refId . ' vs. new ' . $this->refId);
	}

	/**
	 * Returns if there is an attribute with this name
	 * 
	 * @param String $name of the attribute
	 * @return boolean true if there is an attribute, otherwise false
	 */
	public function isAttribute($name) {
		return isset($this->definitions[lmb_strtoupper($name)]);
	}

	/**
	 * Returns if the attribute with this name is usable
	 * 
	 * @param String $name
	 * @return boolean true if this attribute is usable, otherwise false
	 * @throws LimbasRecordException if the attribute is not known
	 */
	public function isUsableAttribute($name) {
		$name = lmb_strtoupper($name);
		if (!isset($this->definitions[$name])) {
			throw new LimbasRecordException('Unknown attribute ' . $name . '!');
		}
		return $this->definitions[$name]['use'];
	}

	/**
	 * Returns if the attribute with this name is a relation
	 * 
	 * @param unknown $name
	 * @return boolean true if this attribute represents a relation, otherwise false
	 */
	public function isRelation($name) {
		$name = lmb_strtoupper($name);
		return isset($this->definitions[$name]) && LimbasRecord::FT_RELATION == $this->definitions[$name]['field_type'];
	}

	/**
	 * Returns if this record is new.
	 * This property is automatically set in constructor and find... methods.
	 * 
	 * @return boolean true if this record is new, otherwise false
	 */
	public function isNew() {
		return $this->_new;
	}
	
	/**
	 * Returns if this record was changed.
	 * 
	 * @return boolean true if this record was changed, otherwise false
	 */
	public function isChanged() {
		return $this->_changed;
	}

	/**
	 * Returns if this record was created from a relation
	 * 
	 * @return boolean true if this record was read from relation, otherwise false
	 */
	public function isFromRelation() {
		return $this->_fromRelation;
	}

	/**
	 * Create a list of possible attributes from limbas config arrays (gtab, gfield)
	 * 
	 * @param string[] $attributes names of usable attributes, null means all
	 */
	protected function createAttributes($attributes=null) {
		global $gtab, $gfield;
		
		$this->trace('LimbasRecord(' . $this->refId . ').createAttributes');
		
		if (!is_null($attributes)) {
			$attributes = array_map('strtoupper', $attributes);
		}
		$this->usableAttributes = $attributes;
		
		$this->definitions['ID'] = array('name' => 'ID', 'use' => true);
		$this->attributes['ID'] = null;
		
		//$this->tableId = $tid = $gtab["argresult_id"][$this->table];

		$this->_tableId = $tid = array_search($this->_table, $gtab['table']);
		//echo $this->table . ' ' . $tid . '<br/>';
		//echo '<pre>' . print_r($gfield[$tid]['verknfieldid'], true) . '</pre>';

		foreach($gfield[$tid]['field_id'] as $fid) {
			if (!isset($gfield[$tid]['data_type'][$fid])) {
				continue;
			}
			if (isset($gfield[$tid]['verkntabletype'][$fid]) && 2 == $gfield[$tid]['verkntabletype'][$fid]) {
				continue;
			}

			$field = array(
				'name' => $gfield[$tid]['field_name'][$fid],
				'id' => $fid,
				'alias' => $gfield[$tid]['spelling'][$fid],
				'field_type' => $gfield[$tid]['field_type'][$fid],
				'data_type' => $gfield[$tid]['data_type'][$fid],
				'parse_type' => $gfield[$tid]['parse_type'][$fid],
				'changed' => false,
				'use' => is_null($attributes) || in_array($name, $attributes) //true
			);

			if (isset($gfield[$tid]['verkntabid'][$fid])) {
				$field['verkntabid'] = $gfield[$tid]['verkntabid'][$fid];
				$field['verkntabname'] = $gtab['table'][$field['verkntabid']];
			}
			if (isset($gfield[$tid]['verknfieldid'][$fid])) {
				$field['verknfieldid'] = $gfield[$tid]['verknfieldid'][$fid];
				$field['relation_type'] = $gfield[$tid]['verkntabletype'][$fid];
				if (isset($data['gfield'][$tid]['hasrecverkn'][$fid])) {
					$field['verknrecfieldid'] = $gfield[$tid]['hasrecverkn'][$fid];
				}
			}
			
			if (isset($gfield[$tid]['md5tab'][$fid])) {
				$field['md5tab'] = lmb_strtoupper($gfield[$tid]['md5tab'][$fid]);
			}
			
			if (LimbasRecord::FT_SELECTION == $gfield[$tid]['field_type'][$fid]) {
				if (isset($definition['gselect'][$tid]) && isset($definition['gselect'][$tid][$fid])) {
					$select = array();
					foreach($definition['gselect'][$tid][$fid]['id'] as $key => $sid) {
						$value = $definition['gselect'][$tid][$fid]['val'][$key];
						if (LimbasRecord::DT_SELECTION_SELECT == $gfield[$tid]['data_type'][$fid] || 14 == $gfield[$tid]['data_type'][$fid]) {
							$select[$value] = $value;
						}
						else {
							$select[$sid] = $value;
						}
					}
					$field['select'] = $select;
				}
			}
			$this->definitions[$field['name']] = $field;
			$this->mappings[$field['id']] = $field['name'];
			$this->attributes[$field['name']] = null;
// 			if ($field['use']) {
// 				if (LimbasRecord::FT_RELATION == $gfield[$tid]['field_type'][$fid]) {
// 					$this->attributes[$field['name']] = new LimbasRelation($this->definitions[$field['name']]);
// 				}
// 				else {
// 					$this->attributes[$field['name']] = null;
// 				}
// 			}
			
			$this->attributeIds[] = $field['id'];
		}
		//$this->setUsableAttributes($attributes);
	}
	
	private function setUsableAttributes($attributes=null) {
		$this->trace('LimbasRecord(' . $this->refId . ').setUsableAttributes(' . var_export($attributes, true) . ')');
		$this->usableAttributes = $attributes;
		if (!is_null($attributes)) {
			$attributes = array_map('strtoupper', $attributes);
			foreach($this->definitions as $name => $definition) {
				if (!in_array($name, $attributes)) {
					//echo '<pre>unuse ' . $name . ' ' . print_r($definition, true) . '</pre>';
					$this->definitions[$name]['use'] = false;
					$this->attributes[$name] = null;
				}
			}
			$this->attributeIds = array();
			foreach($this->definitions as $name => $definition) {
				if ($definition['use']) {
					$this->attributeIds[] = $definition['id'];
				}
			}
			$this->trace('LimbasRecord(' . $this->refId . ').setUsableAttributes(' . implode(',', $this->attributeIds) . ')');
		}
	}
	
	protected function resetAttributes() {
		$this->trace('LimbasRecord(' . $this->refId . ').resetAttributes');
		$this->attributes = array();
		foreach($this->definitions as $key => $definition) {
			$this->attributes[$key] = null;
// 			if ($definition['use']) {
// 				if (LimbasRecord::FT_RELATION == $definition['field_type']) {
// 					$this->attributes[$key] = new LimbasRelation($this->definitions[$key]);
// 				}
// 				else {
// 					$this->attributes[$key] = null;
// 				}
// 			}
		}
	}

	/**
	 * Start a transaction
	 * 
	 * This has to be matched with a call to {@link LimbasRecord::endTransaction()}.
	 * 
	 */
	public static function startTransaction() {
		$params = array(array('action' => 'transaction', 'actionid' => 'start'));
		LimbasLogger::log('LimbasRecord::startTransaction Action:' . print_r($params, true));
		$result = parse_action($params);
		LimbasLogger::trace('LimbasRecord::startTransaction Result:' . print_r($result, true));
	}

	/**
	 * End a transaction
	 * 
	 * Ends the last started transaction
	 * 
	 * @see LimbasRecord::startTransaction()
	 */
	public static function endTransaction() {
		$params = array(array('action' => 'transaction', 'actionid' => 'end'));
		LimbasLogger::log('LimbasRecord::endTransaction Action:' . print_r($params, true));
		$result = parse_action($params);
		LimbasLogger::trace('LimbasRecord::endTransaction Result:' . print_r($result, true));
	}

	/**
	 * Rollback a transaction
	 * 
	 * Rolls back the last transaction
	 * 
	 * @see LimbasRecord::startTransaction()
	 */
	public static function rollbackTransaction() {
		$params = array(array('action' => 'transaction', 'actionid' => 'rollback'));
		LimbasLogger::log('LimbasRecord::rollbackTransaction Action:' . print_r($params, true));
		$result = parse_action($params);
		LimbasLogger::trace('LimbasRecord::rollbackTransaction Result:' . print_r($result, true));
	}
}
