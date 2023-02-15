<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */


class CLimbasSoapTable extends CLimbasSoapComponent
{
	protected $attributes = array();
	protected $definitions = array();
	protected $table = '??';
	protected $tableId  = -1;
	protected $className = '??';
	protected $classMap = array();
	protected $foundComplexTypes = array();

	public function __construct($table)
	{
		$this->table = lmb_strtoupper($table);
		$this->className = ucfirst(lmb_strtolower($table));
		$this->foundComplexTypes[] = $this->table;
		$this->createAttributes();

	}
	
	public function getAttributes() {
		return $this->attributes;
	}

	public function getDefinitions() {
		return $this->definitions;
	}

	public function getTable() {
		return $this->table;
	}

	public function getTableId() {
		return $this->tableId;
	}

	public function getClassName() {
		return $this->className;
	}

	public function __get($name)
	{
		if (array_key_exists($name, $this->attributes)) {
			return $this->attributes[$name];
		}
		else {
			return parent::__get($name);
		}
	}

	public function __set($name, $value)
	{
		if(array_key_exists($name, $this->attributes)) {
			return $this->attributes[$name] = $value;
		}
		else {
			return parent::__set($name, $value);
		}
	}

	public function __isset($name)
	{
		if (isset($this->attributes[$name])) {
			return true;
		}
		else {
			return parent::__isset($name);
		}
	}

	public function __unset($name)
	{
		if (isset($this->attributes[$name])) {
			unset($this->attributes[$name]);
		}
		else {
			parent::__unset($name);
		}
	}

	public function __clone()
	{
		//error_log('CLimbasTable::__clone');
	}

	protected function createAttributes()
	{
		global $gtab, $gfield;
		
		$this->definitions['ID'] = array('name' => 'ID', 'soap_type' => 'xsd:long');
		$this->attributes['ID'] = null;
		
		//$this->tableId = $tid = $gtab["argresult_id"][$this->table];

		$this->tableId = $tid = array_search($this->table, $gtab['table']);

		//echo '<pre>' . print_r($gfield[$tid], true) . '</pre>';

		foreach($gfield[$tid]['field_id'] as $fid) {
			if (!isset($gfield[$tid]['data_type'][$fid])) continue;
			if (isset($gfield[$tid]['verkntabletype'][$fid]) && 2 == $gfield[$tid]['verkntabletype'][$fid]) continue;
					
			$field = array(
					'name' => $gfield[$tid]['field_name'][$fid],
					'id' => $fid,
					'alias' => $gfield[$tid]['spelling'][$fid],
					'field_type' => $gfield[$tid]['field_type'][$fid],
					'data_type' => $gfield[$tid]['data_type'][$fid],
					//'parse_type' => $gfield[$tid]['parse_type'][$fid],
			);

			if (isset($gfield[$tid]['verkntabid'][$fid])) {
				$field['verkntabid'] = $gfield[$tid]['verkntabid'][$fid];
				$field['verkntabname'] = $gtab['table'][$field['verkntabid']];
			}
			if (isset($gfield[$tid]['verknfieldid'][$fid])) {
				$field['verknfieldid'] = $gfield[$tid]['verknfieldid'][$fid];
			}
			
			$this->setFieldSoapType($field);

			if (isset($gfield[$tid]['md5tab'][$fid])) {
				$field['md5tab'] = lmb_strtoupper($gfield[$tid]['md5tab'][$fid]);
			}
			if (4 == $gfield[$tid]['field_type'][$fid]) {
				if (isset($definition['gselect'][$tid]) && isset($definition['gselect'][$tid][$fid])) {
					$select = array();
					foreach($definition['gselect'][$tid][$fid]['id'] as $key => $sid) {
						$value = $definition['gselect'][$tid][$fid]['val'][$key];
						if (12 == $gfield[$tid]['data_type'][$fid] || 14 == $gfield[$tid]['data_type'][$fid]) {
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
			$this->attributes[$field['name']] = null;

			# possible relations to join
			if ($gfield[$tid]["r_verkntabid"]) {
				foreach($gfield[$tid]["r_verkntabid"] as $key => $value) {
 				$field = array('name' => 'J_'.$gtab['table'][$value], 'soap_type' => 'xsd:long');
 				$this->definitions['J_'.$gtab['table'][$value]] = $field;
					$this->attributes['J_'.$gtab['table'][$value]] = null;
				}
			}

		}
	}

	public function getClassMap() {
		$retval = array();
		$this->foundComplexTypes = array($this->table);
		foreach($this->definitions as $name => $definition) {
			if(isset($definition['complex_type'])) {
				if (!in_array($definition['verkntabname'], $this->foundComplexTypes)) {
					//global $defs;
					$complex = CLimbasSoapFactory::getInstance()->createTable($definition['verkntabname']);
					//$complex = new $definition['complex_type']($definition['verkntabname'], $defs);
					//echo '<pre>complex ' . print_r($complex, true) . '</pre>';
					$retval = array_merge($retval, $complex->getClassMap());
	
					$retval[$definition['complex_type']] = $definition['complex_type'];
					
					$this->foundComplexTypes[] = $definition['verkntabname'];
				}
			}
		}

		$retval[$this->className] = $this->className;
		$retval[$this->className . 'Array'] = $this->className . 'Array';

		return $retval;
	}

	public function getWsdlTypes($level=0) {
		$retval = array();
		$own = array();
		$join = array('ID' => 'xsd:integer');
		$this->foundComplexTypes = array($this->table);
		foreach($this->definitions as $name => $definition) {
			if(isset($definition['complex_type'])) {
				if (!in_array($definition['verkntabname'], $this->foundComplexTypes)) {
					$complex = CLimbasSoapFactory::getInstance()->createTable($definition['verkntabname']);
					//echo '<pre>complex ' . print_r($complex, true) . '</pre>';
					$retval = array_merge($retval, $complex->getWsdlTypes($level + 1));
					
					$this->foundComplexTypes[] = $definition['verkntabname'];
				}
				if (0 == $level) {
					$join[$name] = 'xsd:string';
				}
			}
			if (0 == $level || 'J' !== $name[0]) {
 				$own[$name] = $definition['soap_type'];
			}
		}

		$retval[$this->className] = $own;
		$retval[$this->className . 'Array'] = 'complex_Type_Array';
		if (0 == $level) {
			$retval[$this->className . 'Join'] = $join;
		}
		
		return $retval;
	}

	protected function setFieldSoapType(&$field) {
		$field_type = $field['field_type'];
		$data_type = $field['data_type'];
		switch ($field_type) {
			case 1:
				switch ($data_type) {
					case 1:
					case 28:
					case 29:
					case 42: $field['soap_type'] = 'xsd:string'; break;
				}
				break;
			case 2:
				if (11 == $data_type) $field['soap_type'] =  'xsd:dateTime';
				else if (40 == $data_type) $field['soap_type'] =  'xsd:date';
				break;
			case 3:
				switch ($data_type) {
					case 10:
					case 39: $field['soap_type'] = 'xsd:string'; break;
				}
				break;
			case 4:
				switch ($data_type) {
					case 12:
					case 14: $field['soap_type'] = 'xsd:string'; break;
					case 18: $field['soap_type'] = 'xsd:string'; break;
					case 31: $field['soap_type'] = 'xsd:string'; break;
					case 32: $field['soap_type'] = 'xsd:integer'; break;
				}
				break;
			case 5:
				switch ($data_type) {
					case 16:
					case 44: $field['soap_type'] = 'xsd:long'; break;
					case 19:
					case 21:
					case 30: $field['soap_type'] = 'xsd:decimal'; break;
					case 22: $field['soap_type'] = 'xsd:integer'; break;
					case 49: $field['soap_type'] = 'xsd:float'; break;
					default:
				}
				break;
			case 6:
				if (13 == $data_type) $field['soap_type'] =  'xsd:string';
				break;
			case 7:
				if (26 == $data_type) $field['soap_type'] =  'xsd:time';
				break;
			case 8:
				if (15 == $data_type) $field['soap_type'] =  'xsd:string';
				else if (47 == $data_type) $field['soap_type'] =  'xsd:boolean';
				break;
			case 9:
				if (41 == $data_type) $field['soap_type'] =  'xsd:boolean';
				break;
			case 10:
				if (20 == $data_type) $field['soap_type'] =  'xsd:boolean';
				break;
			case 11:
				$field['soap_type'] = 'tns:' . ucfirst(lmb_strtolower($field['verkntabname'])) . 'Array';
				$field['complex_type'] = ucfirst(lmb_strtolower($field['verkntabname']));
				break;
			case '14':
				switch ($data_type) {
					case 34:
					case 35: $field['soap_type'] = 'xsd:integer'; break;
				}
				break;
			case '15': 
				switch ($data_type) {
					case 36:
					case 37: $field['soap_type'] = 'xsd:dateTime'; break;
				}
				break;
			case '16':
				if (38 == $data_type) $field['soap_type'] =  'xsd:integer';
				break;
			case '17':
				if (43 == $data_type) $field['soap_type'] =  'xsd:integer';
				break;
			case '18':
				if (45 == $data_type) $field['soap_type'] =  'xsd:integer';
				break;
			case '19':
				if (46 == $data_type) $field['soap_type'] =  'xsd:integer';
				break;
			case '20':
				if (48 == $data_type) $field['soap_type'] =  'xsd:boolean';
				break;
			case '21':
				if (50 == $data_type) $field['soap_type'] =  'xsd:string';
				break;
			}
		
		if (!isset($field['soap_type'])) {
			throw new UnexpectedValueException('Unknown soap_type for \'' . $this->table . '.' . $field["name"] . '\' (ft: ' . $field_type . ', dt: ' . $data_type);
		}
	}
}
