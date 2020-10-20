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
class LimbasLogger {
	/*
	 * Log Level
	 */
	const LL_TRACE   = 1;
	const LL_INFO    = 2;
	const LL_PROFILE = 4;
	const LL_WARNING = 8;
	const LL_ERROR   = 16;
	
	
	/** @var int minimum log level to handle */
	public static $loglevel = self::LL_INFO;
	
	private static $llstrings = array('1' => 'TRACE', '2' => 'INFO', '4' => 'PROFILE', '8' => 'WARNING', '16' => 'ERROR');
	private static $log = array();
	private static $profile = array();
	private static $profileStarts = array();
	
	private static $logRoutes = array();
	
	/**
	 * Log a string to the logger
	 *
	 * @param string $str the string to log
	 * @param int $level the level to use
	 */
	public static function log($str, $level=self::LL_INFO) {
		if (self::$loglevel <= $level) {
			self::$log[] = array('timestamp' => self::formatedTime(), 'level' => self::$llstrings[$level], 'message' => $str);
		}
	}
	
	/**
	 * Trace a string to the logger
	 *
	 * @param string $str the string to log
	 */
	public static function trace($str) {
		self::log($str, self::LL_TRACE);
	}
	
	/**
	 * Marks the beginning of a code block for profiling. This has to be matched with a call to endProfile() with the same token. The begin- and end- calls must also be properly nested.
	 * 
	 * @see LimbasLogger::endProfile() matching call is endProfile()
	 * 
	 * @param string $token unique string for the code block
	 */
	public static function beginProfile($token) {
		array_push(self::$profile, $token);
		self::$profileStarts[$token] = microtime(true);
		self::log('Begin: ' . $token, self::LL_PROFILE);
	}

	/**
	 * Marks the end of a code block for profiling. This has to be matched with a previous call to {@link LimbasLogger::beginProfile() beginProfile()} with the 
	 * same token.
	 * 
	 * @see LimbasLogger::beginProfile() matching call is beginProfile()
	 * 
	 * @param string $token unique string for the code block
	 * @throws LimbasException if no matching call was found
	 */
	public static function endProfile($token) {
		if ($token !== array_pop(self::$profile)) {
			throw new LimbasException('LimbasLogger::endProfile found a missmatching code block "' . $token . '". Make sure the calls to LimbasLogger::beginProfile() and LimbasLogger::endProfile() be properly nested.');
		}
		$end = microtime(true);
		self::log('End: ' . $token . ' took: ' . round($end - self::$profileStarts[$token], 5) . 'sec', self::LL_PROFILE);
		unset(self::$profileStarts[$token]);
	}

	/**
	 * Add a route to the logger
	 * 
	 * @param LimbasLogRoute $route
	 */
	public static function addLogRoute($route) {
		if ($route instanceof LimbasLogRoute) {
			self::$logRoutes[] = $route;
		}
	}
	/**
	 * Return complete log for further processing.
	 * 
	 * @return string[][]
	 */
	public static function getLogLines() {
		return self::$log;
	}
	
	/**
	 * Process log with defined log route.
	 *
	 * @return void
	 */
	public static function processLog() {
// 		switch(self::$logRoute) {
// 			case self::LR_PLAIN: LimbasPlainLogRoute::processLog(); break; 
// 			case self::LR_WEB: LimbasWebLogRoute::processLog(); break;
// 			default: throw new LimbasException('LimbasLogger::processLog unknown log route ' . $self::$logRoute); 
// 		}
		foreach(self::$logRoutes as $route) {
			$route->processLog();
		}
	}

	/**
	 * Return complete log with defined log route.
	 *
	 * @return void
	 */
///	public static function processException($exception) {
// 		switch(self::$logRoute) {
// 			case self::LR_PLAIN: LimbasPlainLogRoute::processException($exception); break; 
// 			case self::LR_WEB: LimbasWebLogRoute::processException($exception); break;
// 			default: throw new LimbasException('LimbasLogger::processException unknown log route ' . $self::$logRoute); 
// 		}
///		foreach(self::$logRoutes as $route) {
///			$route->processException($exception);
///		}
///		
///	}

	/**
	 * Return complete log as HTML table
	 * 
	 * @return string
	 */
// 	public static function getLogAsTable() {
// 		$str = '<table class="limbasRecordLogger">';
// 		$str .= '<tr><th colspan="3" style="background-color: black; color: white;">Log</th></tr>';
// 		$str .= '<tr><th>Timestamp</th><th>Level</th><th>Message</th></tr>';
// 		$i = 0;
// 		foreach(self::$log as $log) {
// 			$parts = explode("\t", $log, 3);
			
// 			$str .= '<tr class="' . (0 === $i % 2 ? 'even' : 'odd') . '"><td>' . $parts[0] . '</td><td class="loglevel ' . lmb_strtolower($parts[1]) . '">' . $parts[1] . '</td><td class="message">'. $parts[2] . '</td></tr>';
// 			$i++;
// 		}
		
// 		$str .= '</table>';
// 		return $str;
// 	} 

	public static function useExceptionHandler() {
		set_exception_handler(array('LimbasLogger', 'processException'));
	}
	
	public static function processException($exception) {
		foreach(self::$logRoutes as $route) {
			$route->processException($exception);
		}
	}

	public static function getLLString($logLevel) {
	    return LimbasLogger::$llstrings[$logLevel];
    }

	/**
	 * Format acutal time with micro seconds
	 * 
	 * @return string
	 */
	private static function formatedTime() {
		list($usec, $sec) = explode(" ", microtime());
		return date('H:i:s.', $sec) . round(100000 * $usec, 0);
	}
}