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
interface LimbasLogRoute
{
	/**
	 * Process all log lines
	 */
	public function processLog();
	
	/**
	 * Process an uncaught exception
	 *
	 * @param multi $exception
	 */
	public function processException($exception);
}
