<?php

namespace Sabre\DAV\Auth\Backend;

use Sabre\DAV;

/**
 * This is the base class for any authentication object.
 *
 * @copyright Copyright (C) 2007-2013 fruux GmbH (https://fruux.com/).
 * @copyright Limbas GmbH
 * @author Evert Pot (http://evertpot.com/)
 * @author Limbas GmbH
 * @license http://code.google.com/p/sabredav/wiki/License Modified BSD License
 */
class Limbas implements BackendInterface {
	
	
   /**
     * Returns information about the currently logged in username.
     *
     * If nobody is currently logged in, this method should return null.
     *
     * @return string|null
     */
	function getCurrentUser() {
		global $session;
		return $session ['username'];
	}
    /**
     * Authenticates the user based on the current request.
     *
     * If authentication is successful, true must be returned.
     * If authentication fails, an exception must be thrown.
     *
     * @param \Sabre\DAV\Server $server
     * @param string $realm
     * @return bool
     */
	function authenticate(\Sabre\DAV\Server $server, $realm) {
		global $session;
		return $session ['username'];
	}
}
