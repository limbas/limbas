<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */

/**
 * @deprecated
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
