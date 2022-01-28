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
class LimbasWebLogRoute implements LimbasLogRoute {
	/**
	 * Process all log lines
	 */
	public function processLog() {
		$logs = LimbasLogger::getLogLines();
		$str = '<table class="limbasRecordLogger">';
		$str .= '<tr><th colspan="3" style="background-color: black; color: white;">Log</th></tr>';
		$str .= '<tr><th>Timestamp</th><th>Level</th><th>Message</th></tr>';
		$i = 0;
		foreach($logs as $log) {
			$str .= '<tr class="' . (0 === $i % 2 ? 'even' : 'odd') . '"><td>' . $log['timestamp'] . '</td><td class="loglevel ' . lmb_strtolower($log['level']) . '">' . $log['level'] . '</td><td class="message"><pre>'. $log['message'] . '</pre></td></tr>';
			$i++;
		}
		
		$str .= '</table><br/>';
		echo $str;
		
	}
	
	/**
	 * Process an uncaught exception
	 * 
	 * @param multi $exception
	 */
	public function processException($exception) {
		$str = '<table class="limbasRecordLogger">';
		$str .= '<tr><th colspan="3" style="background-color: black; color: white;">Exception \'' . get_class($exception) . '\'</th></tr>';
		$str .= '<tr><th colspan="3" style="background-color: black; color: white;">in ' . $exception->getFile() . '(' . $exception->getLine() . ')</th></tr>';
		$str .= '<tr><td colspan="3"><pre>' . $exception->getMessage() . '</pre></td></tr>';
		$str .= '<tr><th>&nbsp;</th><th>File</th><th>Function</th></tr>';
		$i = 0;
		foreach($exception->getTrace() as $trace) {
			$str .= '<tr class="' . (0 === $i % 2 ? 'even' : 'odd') . '"><td>#' . $i . '</td><td>' . $trace['file'] . '(' . $trace['line'] . ')</td><td>';
			$str .= (isset($trace['class']) ? $trace['class'] . $trace['type'] : '') . $trace['function'] . '(';
			$args = array();
			foreach($trace['args'] as $idx => $arg) {
				switch(gettype($arg)) {
					case 'boolean' : $args[] = $arg ? 'TRUE' : 'FALSE'; break;
					case 'integer' : $args[] = $arg; break;
					case 'double'  : $args[] = $arg; break;
					case 'string'  : $args[] = '\'' . (15 < lmb_strlen($arg) ? lmb_substr($arg, 0, 15) . '...' : $arg) . '\''; break;
					case 'array'   : $args[] = 'Array'; break;
					case 'object'  : $args[] = get_class($arg); break;
					case 'resource': $args[] = 'Resource id #' . $arg; break;
					case 'NULL'    : $args[] =  'NULL'; break;
					default: $args[] = '???';
				}
			}
			$str .= implode(', ', $args);
			$str .=  ')</td></tr>';
			$i++;
		}
		$str .= '</table><br/>';
		echo $str;
		$this->processLog();
	}
	
}