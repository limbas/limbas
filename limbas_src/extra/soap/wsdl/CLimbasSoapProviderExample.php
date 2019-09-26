<?php
/*
 * Copyright notice
 * (c) 1998-2019 Limbas GmbH(support@limbas.org)
 * All rights reserved
 * This script is part of the LIMBAS project. The LIMBAS project is free software; you can redistribute it and/or modify it on 2 Ways:
 * Under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * Or
 * In a Propritary Software Licence http://limbas.org
 * The GNU General Public License can be found at http://www.gnu.org/copyleft/gpl.html.
 * A copy is found in the textfile GPL.txt and important notices to the license from the author is found in LICENSE.txt distributed with these scripts.
 * This script is distributed WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * This copyright notice MUST APPEAR in all copies of the script!
 * Version 3.6
 */

/*
 * ID:
 */
class CLimbasSoapProvider extends CLimbasSoapComponent implements ILimbasSoapProvider
{
	private $soapTable;
	private $soapQuery;
	private $soapJoin;
	private $className;
	
	private $items;
	
	public function __construct($className)
	{
		$this->className = $className;
		$this->soapTable = CLimbasSoapFactory::getInstance()->createTable($className);
		$this->soapQuery = CLimbasSoapFactory::getInstance()->createQuery($className);
		$this->soapJoin = CLimbasSoapFactory::getInstance()->createJoin($className);
		
		if (!file_exists('soaptestdata.ser')) {
			$this->items = array();
			$obj = CLimbasSoapFactory::getInstance()->createTable($className);
			$obj->ID = 1;
			$obj->NAME = 'Wittkowski';
			$obj->VORNAME = 'Christian';
			$ks = array();
			$k = CLimbasSoapFactory::getInstance()->createTable('Korrespondenz');
			$k->ID = 1;
			$k->TYP = 'typ';
			$k->ART = 'art';
			$ks[] = $k;
			$obj->KORRESPONDENZ = $ks;
			$this->items[1] = $obj;
			$obj = CLimbasSoapFactory::getInstance()->createTable($className);
			$obj->ID = 2;
			$obj->NAME = 'Westhagen';
			$obj->VORNAME = 'Axel';
			$this->items[2] = $obj;
			$obj = CLimbasSoapFactory::getInstance()->createTable($className);
			$obj->ID = 3;
			$obj->NAME = 'Einsiedel';
			$obj->VORNAME = 'Christian';
			$this->items[3] = $obj;

			$this->writeData();
		}
		else {
			$this->items = unserialize(file_get_contents('soaptestdata.ser'));
		}
	}
	
	public function getClassName() {
		return $this->className;
	}

	public function query($model) {
		if (!is_null($model)) {
			error_log('query: ' . $model->VORNAME . ', ' . $model->NAME, 0, 'server');
			error_log(print_r($model, true), 0, 'server');
		}
		else {
			error_log('query: null', 0, 'server');
		}
		
		$retval = array();
		error_log("count: " . count($this->items));
		foreach($this->items as $item) {
			$matches = true;
			error_log(print_r($item, true), 0, 'server');
			if (!is_null($model)) {
				if (!is_null($model->VORNAME) && '' !== $model->VORNAME && $model->VORNAME !== $item->VORNAME) {
					$matches = false;
				}
				if ($matches && !is_null($model->NAME) && '' !== $model->NAME && $model->NAME !== $item->NAME) {
					$matches = false;
				}
			}
			if ($matches) {
				$retval[] = $item;
			}
		}
		return $retval;
	}
	
	public function getByPk($id) {
		error_log('getByPk: ' . var_export($id, true));
		
		if (isset($this->items[$id])) {
			return $this->items[$id];
		}
		
		throw new SoapFault('Server', 'ID "' . $id . '" out of bounds');
		//throw new LimbasException('ID "' . $id . '" out of bounds');
		//throw new OutOfBoundsException('ID "' . $id . '" out of bounds');
	}
	
	public function delete($id) {
		
	}
	
	public function insert($list) {
		error_log('insert ' . print_r($list, true));
		$retval = new InsertResultArray();

		//$model = $models;
		foreach($list->items as $model) {
			error_log('model ' . print_r($model, true));
			$model->ID = 1 + count($this->items);
			$this->items[$model->ID] = $model;
			$this->writeData();

			$result = new InsertResult();
			$result->ID = $model->ID;
			$result->REPRESENTATION = $model->NAME . ', ' . $model->VORNAME;
			
			$retval->items[] = $result;
		}
// 			$result = new InsertResult();
// 			$result->ID = 11;
// 			$result->REPRESENTATION = '11JUHU';
			
// 			$retval->items[] = $result;
// 			$result = new InsertResult();
// 			$result->ID = 22;
// 			$result->REPRESENTATION = '22JUHU';
			
// 			$retval->items[] = $result;
		return $retval;
	}
	
	public function update($model) {
		$id = $model->ID;
		error_log('getByPk: ' . var_export($id, true));
		
		if (isset($this->items[$id])) {
			if ('' !== $model->VORNAME && $model->VORNAME !== $this->items[$id]->VORNAME) {
				$this->items[$id]->VORNAME = $model->VORNAME;
			}
			if ('' !== $model->NAME && $model->NAME !== $this->items[$id]->NAME) {
				$this->items[$id]->NAME = $model->NAME;
			}
			$this->writeData();
			return;
		}
		
		throw new SoapFault('Server', 'ID "' . $id . '" out of bounds');
		//throw new LimbasException('ID "' . $id . '" out of bounds');
		//throw new OutOfBoundsException('ID "' . $id . '" out of bounds');
	}
	
	public function join($join) {
		
	}

	public function getClassMap() {
		//echo '<pre>' . print_r($this->soapTable->getClassMap(), true) . '</pre>';
		//echo '<pre>Query ' . print_r($this->soapQuery->getClassMap(), true) . '</pre>';
		return array_merge(array('PersonenArray' => 'PersonenArray', 'InsertResult' => 'InsertResult', 'InsertResultArray' => 'InsertResultArray'), 
				$this->soapTable->getClassMap(), $this->soapQuery->getClassMap(), $this->soapJoin->getClassMap());
	}
	
	public function getWsdlOperations() {
		$retval = array();
		
		$retval['query'] = '';
		$retval['getByPk'] = '';
		$retval['delete'] = '';
		$retval['insert'] = '';
		$retval['update'] = '';
		$retval['join'] = '';
		
		return $retval;
	}
	
	public function getWsdlTypes() {
		//echo '<pre>' . print_r(array_keys($this->soapTable->getWsdlTypes()), true) . '</pre>';
		//echo '<pre>' . print_r(array_keys($this->soapQuery->getWsdlTypes()), true) . '</pre>';
		return array_merge(array(
					'InsertResult' => array('ID' => 'xsd:long', 'REPRESENTATION' => 'xsd:string'),
					'InsertResultArray' => 'complex_Type_Array'
				),
				$this->soapTable->getWsdlTypes(), $this->soapQuery->getWsdlTypes(), $this->soapJoin->getWsdlTypes());
	}
	
	public function getWsdlMessages() {
		$retval = array();
		
		$retval['queryRequest'] = array('params' => array('tns:' . $this->className . 'Query'));
		$retval['queryResponse'] = array('return' => array('tns:' . $this->className . 'Array'));
		$retval['getByPkRequest'] = array('params' => array('xsd:long'));
		$retval['getByPkResponse'] = array('return' => array('tns:' . $this->className));
		$retval['deleteRequest'] = array('params' => array('xsd:long'));
		$retval['deleteResponse'] = array();
		$retval['insertRequest'] = array('params' => array('tns:' . $this->className . 'Array'));
		$retval['insertResponse'] = array('return' => array('tns:InsertResultArray'));
		$retval['updateRequest'] = array('params' => array('tns:' . $this->className));
		$retval['updateResponse'] = array();
		$retval['joinRequest'] = array('params' => array('tns:' . $this->className . 'Join'));
		$retval['joinResponse'] = array();
		
		return $retval;
	}
	
	private function writeData() {
		file_put_contents('soaptestdata.ser', serialize($this->items));
	}
}