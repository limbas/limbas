<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */
namespace Sabre\DAV\Auth\Backend;

use Sabre\DAV;
use Sabre\HTTP\RequestInterface;
use Sabre\HTTP\ResponseInterface;

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

    /**
     * When this method is called, the backend must check if authentication was
     * successful.
     *
     * The returned value must be one of the following
     *
     * [true, "principals/username"]
     * [false, "reason for failure"]
     *
     * If authentication was successful, it's expected that the authentication
     * backend returns a so-called principal url.
     *
     * Examples of a principal url:
     *
     * principals/admin
     * principals/user1
     * principals/users/joe
     * principals/uid/123457
     *
     * If you don't use WebDAV ACL (RFC3744) we recommend that you simply
     * return a string such as:
     *
     * principals/users/[username]
     *
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @return array
     */
    function check(RequestInterface $request, ResponseInterface $response)
    {
        global $session;
        return array(true, 'principals/' . $session['username']);
    }

    /**
     * This method is called when a user could not be authenticated, and
     * authentication was required for the current request.
     *
     * This gives you the opportunity to set authentication headers. The 401
     * status code will already be set.
     *
     * In this case of Basic Auth, this would for example mean that the
     * following header needs to be set:
     *
     * $response->addHeader('WWW-Authenticate', 'Basic realm=SabreDAV');
     *
     * Keep in mind that in the case of multiple authentication backends, other
     * WWW-Authenticate headers may already have been set, and you'll want to
     * append your own WWW-Authenticate header instead of overwriting the
     * existing one.
     *
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @return void
     */
    function challenge(RequestInterface $request, ResponseInterface $response)
    {
        // TODO: Implement challenge() method.
    }
}
