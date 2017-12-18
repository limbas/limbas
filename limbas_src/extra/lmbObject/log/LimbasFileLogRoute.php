<?php
/*
 * Copyright notice
 * (c) 1998-2016 Limbas GmbH - Axel westhagen (support@limbas.org)
 * All rights reserved
 * This script is part of the LIMBAS project. The LIMBAS project is free software; you can redistribute it and/or modify it on 2 Ways:
 * Under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * Or
 * In a Propritary Software Licence http://limbas.org
 * The GNU General Public License can be found at http://www.gnu.org/copyleft/gpl.html.
 * A copy is found in the textfile GPL.txt and important notices to the license from the author is found in LICENSE.txt distributed with these scripts.
 * This script is distributed WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * This copyright notice MUST APPEAR in all copies of the script!
 * Version 3.0
 */

/*
 * ID:
 */
class LimbasFileLogRoute implements LimbasLogRoute
{
	private $filename;
	private $fh = null;
	
	/**
	 * Create a LimbasFileLogRoute object
	 * 
	 * @param string $filename complete path to file
	 * @return LimbasFileLogRoute instance
	 * @throws LimbasException
	 */
	public function __construct($filename) {
		$this->filename = $filename;
		$this->fh = fopen($filename, 'a');
		if (false === $this->fh) {
			throw new LimbasException('Unable to open file \'' . $filename . '\'!');
		}
	}
	
	/**
	 * Clean up all necessary members
	 */
	public function __destruct() {
		fclose($this->fh);
	}
	
	/**
	 * Process all log lines
	 */
	public function processLog() {
		foreach(LimbasLogger::getLogLines() as $log) {
			fwrite($this->fh, $log['timestamp'] . "\t" . $log['level'] . "\t" . $log['message'] . "\n");
		}
	}

	/**
	 * Process an uncaught exception
	 * 
	 * @param multi $exception
	 */
	public function processException($exception) {
		$this->processLog();
		fwrite($this->fh, $exception);
	}
}