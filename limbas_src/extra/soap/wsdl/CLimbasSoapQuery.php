<?php
/*
 * Copyright notice
 * (c) 1998-2021 Limbas GmbH(support@limbas.org)
 * All rights reserved
 * This script is part of the LIMBAS project. The LIMBAS project is free software; you can redistribute it and/or modify it on 2 Ways:
 * Under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * Or
 * In a Propritary Software Licence http://limbas.org
 * The GNU General Public License can be found at http://www.gnu.org/copyleft/gpl.html.
 * A copy is found in the textfile GPL.txt and important notices to the license from the author is found in LICENSE.txt distributed with these scripts.
 * This script is distributed WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * This copyright notice MUST APPEAR in all copies of the script!
 * Version 4.3.36.1319
 */

/*
 * ID:
 */
class CLimbasSoapQuery extends CLimbasSoapTable
{
	public function __construct($table)
	{
		parent::__construct($table);
	}

	protected function createAttributes()
	{
		$this->definitions['q_orderby'] = array('name' => 'q_orderby', 'soap_type' => 'xsd:string');
		$this->attributes['q_orderby'] = null;
		
		$this->definitions['q_page'] = array('name' => 'q_page', 'soap_type' => 'xsd:string');
		$this->attributes['q_page'] = null;
		
		$this->definitions['q_rows'] = array('name' => 'q_rows', 'soap_type' => 'xsd:string');
		$this->attributes['q_rows'] = null;
		
		$this->definitions['q_nolimit'] = array('name' => 'q_nolimit', 'soap_type' => 'xsd:string');
		$this->attributes['q_nolimit'] = null;
	
		parent::createAttributes();
	}
		
	public function getClassMap() {
		$retval = array();

		$retval[$this->className . 'Query'] = $this->className . 'Query';

		return $retval;
	}

	public function getWsdlTypes($level=0) {
		$retval = array();
		$own = array();
		$this->foundComplexTypes = array($this->table);
		foreach($this->definitions as $name => $definition) {
			if(isset($definition['complex_type'])) {
				if (!in_array($definition['verkntabname'], $this->foundComplexTypes)) {
					$complex = CLimbasSoapFactory::getInstance()->createQuery($definition['verkntabname']);
					//echo '<pre>complex ' . print_r($complex->getWsdlTypes(), true) . '</pre>';
					$retval = array_merge($retval, $complex->getWsdlTypes($level + 1));
									
					$this->foundComplexTypes[] = $definition['verkntabname'];
				}
			}
			if ('J' !== $name[0]) {
 				$own[$name] = $definition['soap_type'];
			}
		}

		$retval[$this->className . 'Query'] = $own;
		return $retval;
	}

	protected function setFieldSoapType(&$field) {
		$field_type = $field['field_type'];
		switch ($field_type) {
			case 11:
				$field['soap_type'] = 'tns:' . ucfirst(lmb_strtolower($field['verkntabname'])) . 'Query';
				$field['complex_type'] = ucfirst(lmb_strtolower($field['verkntabname'])) . 'Query';
				break;
			default:
				$field['soap_type'] = 'xsd:string';
				break;
		}
	}
}