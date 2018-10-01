<?php
/*
 * Copyright notice
 * (c) 1998-2018 Limbas GmbH(support@limbas.org)
 * All rights reserved
 * This script is part of the LIMBAS project. The LIMBAS project is free software; you can redistribute it and/or modify it on 2 Ways:
 * Under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * Or
 * In a Propritary Software Licence http://limbas.org
 * The GNU General Public License can be found at http://www.gnu.org/copyleft/gpl.html.
 * A copy is found in the textfile GPL.txt and important notices to the license from the author is found in LICENSE.txt distributed with these scripts.
 * This script is distributed WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * This copyright notice MUST APPEAR in all copies of the script!
 * Version 3.5
 */

/*
 * ID:
 */
class LimbasRelation extends LimbasComponent
{
	/** @var array the definition of this relation */
	protected $definition;
	/** @var array criteria for this relation */
	protected $criteria = array('attr' => array());
	/** @var array of all attributes */
	protected $attributes = null;
	/** @var array the short name(s) of the relation */
	protected $strings = array();
	/** @var array the object(s) of the relation */
	protected $objects = array();
	
	/**
	 * Constructor
	 * 
	 * @param string[] $definition definition of this relation
	 */
	public function __construct($definition) {
		parent::__construct();
		$this->definition = $definition;
		$this->log('LimbasRelation(' . $this->refId . ').__construct ' . $definition['name']);
	}
	
	/**
	 * This magic method is used for setting a string value for the object. It will be used if the object is used as a string.
	 * 
	 * @return string representing this object
	 */
	public function __toString() {
		$str = '<table class="limbasRecordRelation">';
		$str .= '<tr><th>' .
				get_class($this) . '</th><th>' .
				$this->definition['tabname'] . '.' . $this->definition['name'] . '</th></tr>';
		$str .= '<tr><th>RefCount</th><th>' . $this->refId. '</thd></tr>';
		$str .= '<tr><td class="label">' . $this->definition['name'] . '</td><td>';
		if (0 < count($this->objects)) {
			foreach($this->objects as $object) {
				$str .= $object;
			}
		}
		elseif (0 < count($this->strings)) {
			$str .= implode('<br/>', $this->strings);
		}
		$str .= '</td></tr></table>';
		return $str;
	}

	/**
	 * This magic method is invoked each time a clone is called on the object variable
	 * 
	 */
	public function __clone() {
		$refId = $this->refId;
		list($this->refId, $tmp) = LimbasComponent::refCount($this);
		$this->log('RefId old: ' . $refId . ' vs. new ' . $this->refId);
	}
	
	/**
	 * replace all relational objects
	 * 
	 * @param LimbasRecord[] $objects the LimbasRecord objects to replace
	 */
	public function setObjects($objects) {
		$this->log('LimbasRelation(' . $this->refId . ', ' . $this->definition['name'] . ').setObjects(' . count($objects) . ')');
		$this->objects = array();
		foreach($objects as $object) {
			$this->addObject($object);
		}
	}
	
	/**
	 * add one relational object
	 * 
	 * @param LimbasRecord $object the LimbasRecord object to add
	 * @throws LimbasRecordException if $object represents wrong database table 
	 */
	public function addObject($object) {
		$this->log('LimbasRelation(' . $this->refId . ', ' . $this->definition['name'] . ').addObject(' . get_class($object) . ')');
		if ($this->definition['vkntabname'] !== $object->getTable()) {
			$this->objects[] = $object;
		}
		else {
			throw new LimbasRecordException('Not allowed to add object of table ' . $object->getTable() . ' to relation ' . $this->definition['name'] . ' of table ' . $this->definition['tabname']);
		}
	}
	
	/**
	 * Check if there are related objects
	 * 
	 * @return boolean true if there are objects, otherwise false
	 */
	public function hasObjects() {
		return 0 < count($this->objects);
	}
	
	/**
	 * return all objects
	 * 
	 * @return LimbasRecord[]
	 */
	public function getObjects() {
		$this->log('LimbasRelation(' . $this->refId . ', ' . $this->definition['name'] . ').getObjects ' . count($this->objects));
		return $this->objects;
	}
	
	/**
	 * replace all short names
	 * 
	 * @param string[] $strings the short names to replace
	 */
	public function setStrings($strings) {
		$this->log('LimbasRelation(' . $this->refId . ').setStrings(' . var_export($strings, true) . ')');
		$this->strings = $strings;
	}
	
	/**
	 * Check if there are short names
	 * 
	 * @return boolean true if there are short names, otherwise false
	 */
	public function hasStrings() {
		return 0 < count($this->strings);
	}
	
	/**
	 * return all short names
	 * 
	 * @return string[]
	 */
	public function getStrings() {
		$this->log('LimbasRelation(' . $this->refId . ').getStrings ' . var_export($this->strings, true));
		return $this->strings;
	}
	
	/**
	 * Add a criteria expression to the criteria
	 * 
	 * @param string $name attribute name
	 * @param mixed $value expression
	 */
	public function addCriteria($name, $value) {
		// TODO: check if $name is an attribute of the related table
		$this->log('LimbasRelation(' . $this->refId . ').addCriteria(' . $name . ', ' . $value . ')');
		$this->criteria['attr'][$name] = $value;
	}

	/**
	 * Replace the complete criteria
	 * @param mixed[] $criteria the complete criteria
	 */
	public function setCriteria($criteria) {
		$this->log('LimbasRelation(' . $this->refId . ').setCriteria(' . var_export($criteria, true) . ')');
		$this->criteria = $criteria;
	}
	
	/**
	 * Get the complete criteria
	 * 
	 * @return mixed[] criteria
	 */
	public function getCriteria() {
		$this->log('LimbasRelation(' . $this->refId . ').getCriteria ' . var_export($this->criteria, true));
		return $this->criteria;
	}

	/**
	 * Set all allowed attributes for the related table
	 * 
	 */
	public function setAttributes($attributes) {
		$this->log('LimbasRelation(' . $this->refId . ').setAttributes(' . var_export($attributes, true) . ')');
		$this->attributes = $attributes;
	}
	
	/**
	 * Get all allowed attributes for the related table
	 * 
	 * @return string[]
	 */
	public function getAttributes() {
		$this->log('LimbasRelation(' . $this->refId . ').getAttributes ' . var_export($this->attributes, true));
		return $this->attributes;
	}
}
