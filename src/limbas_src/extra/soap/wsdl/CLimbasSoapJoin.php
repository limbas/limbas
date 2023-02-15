<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */



class CLimbasSoapJoin extends CLimbasSoapTable
{
	public function __construct($table)
	{
		parent::__construct($table);
	}

	protected function createAttributes()
	{
		global $gtab, $gfield;
		
		parent::createAttributes();
		
		//echo '<div style="float: left;"><pre>' . print_r($this->definitions, true) . '</pre></div>';
		foreach($this->definitions as $name => $definition) {
			if ('ID' !== $name) {
				if (isset($definition['complex_type'])) {
					$this->definitions[$name]['soap_type'] = 'xsd:string';
					unset($this->definitions[$name]['complex_type']);
				}
				else {
					unset($this->definitions[$name]);
					unset($this->attributes[$name]);
				}
			}	
		}
		//echo '<div style="float: left;"><pre>' . print_r($this->definitions, true) . '</pre></div>';
	}
		
	public function getClassMap() {
		$retval = array();

		$retval[$this->className . 'Join'] = $this->className . 'Join';

		return $retval;
	}

	public function getWsdlTypes($level=0) {
		$retval = array();
		$own = array();
		foreach($this->definitions as $name => $definition) {
			$own[$name] = $definition['soap_type'];
		}

		$retval[$this->className . 'Join'] = $own;
		return $retval;
	}
}
