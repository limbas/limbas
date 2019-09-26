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