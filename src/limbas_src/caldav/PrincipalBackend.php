<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */
namespace Sabre\DAVACL\PrincipalBackend;

use Sabre\DAV;
use Sabre\DAVACL;

/**
 * Limbas principal backend
 *
 *
 * This backend assumes all principals are in a single collection. The default collection
 * is 'principals/', but this can be overriden.
 *
 * @copyright Copyright (C) 2007-2013 fruux GmbH (https://fruux.com/).
 * @copyright Limbas GmbH
 * @author Evert Pot (http://evertpot.com/)
 * @author Limbas GmbH
 * @license http://code.google.com/p/sabredav/wiki/License Modified BSD License
 */
class Limbas extends AbstractBackend {
	
	/**
	 * pdo
	 *
	 * @var PDO
	 */
	protected $pdo;
	
	/**
	 * PDO table name for 'principals'
	 *
	 * @var string
	 */
	protected $tableName;
	
	/**
	 * PDO table name for 'group members'
	 *
	 * @var string
	 */
	protected $groupMembersTableName;
	
	/**
	 * A list of additional fields to support
	 *
	 * @var array
	 */
	protected $fieldMap = array (
			
			/**
			 * This property can be used to display the users' real name.
			 */
			'{DAV:}displayname' => array (
					'dbField' => 'displayname' 
			),
			
			/**
			 * This property is actually used by the CardDAV plugin, where it gets
			 * mapped to {http://calendarserver.orgi/ns/}me-card.
			 *
			 * The reason we don't straight-up use that property, is because
			 * me-card is defined as a property on the users' addressbook
			 * collection.
			 */
			'{http://sabredav.org/ns}vcard-url' => array (
					'dbField' => 'vcardurl' 
			),
			/**
			 * This is the users' primary email-address.
			 */
			'{http://sabredav.org/ns}email-address' => array (
					'dbField' => 'email' 
			) 
	);
	
	/**
	 * Sets up the backend.
	 *
	 * @param PDO $pdo        	
	 * @param string $tableName        	
	 * @param string $groupMembersTableName        	
	 */
	public function __construct() {
	}
	
	/**
	 * Returns a list of principals based on a prefix.
	 *
	 * This prefix will often contain something like 'principals'. You are only
	 * expected to return principals that are in this base path.
	 *
	 * You are expected to return at least a 'uri' for every user, you can
	 * return any additional properties if you wish so. Common properties are:
	 * {DAV:}displayname
	 * {http://sabredav.org/ns}email-address - This is a custom SabreDAV
	 * field that's actualy injected in a number of other properties. If
	 * you have an email address, use this property.
	 *
	 * @param string $prefixPath        	
	 * @return array
	 */
	public function getPrincipalsByPrefix($prefixPath) {
		global $session;
		$principals = array ();
		if ($prefixPath == 'principals') {
			$principal = array (
					'uri' => 'principals/' . $session ['username'],
					'{DAV:}displayname' => $session ['username'],
					'{http://sabredav.org/ns}email-address' => $session ['email'] 
			);
			$principals [] = $principal;
			return $principals;
		} else {
			return array ();
		}
	}
	
	/**
	 * Returns a specific principal, specified by it's path.
	 * The returned structure should be the exact same as from
	 * getPrincipalsByPrefix.
	 *
	 * @param string $path        	
	 * @return array
	 */
	public function getPrincipalByPath($path) {
		global $session;
		if ($path == 'principals/' . $session ['username']) {
			
			$principal = array (
					'uri' => 'principals/' . $session ['username'],
					'{DAV:}displayname' => $session ['username'],
					'{http://sabredav.org/ns}email-address' => $session ['email'] 
			);
			return $principal;
		} else {
			return null;
		}
	}
	
	/**
	 * Updates one ore more webdav properties on a principal.
	 *
	 * The list of mutations is supplied as an array. Each key in the array is
	 * a propertyname, such as {DAV:}displayname.
	 *
	 * Each value is the actual value to be updated. If a value is null, it
	 * must be deleted.
	 *
	 * This method should be atomic. It must either completely succeed, or
	 * completely fail. Success and failure can simply be returned as 'true' or
	 * 'false'.
	 *
	 * It is also possible to return detailed failure information. In that case
	 * an array such as this should be returned:
	 *
	 * array(
	 * 200 => array(
	 * '{DAV:}prop1' => null,
	 * ),
	 * 201 => array(
	 * '{DAV:}prop2' => null,
	 * ),
	 * 403 => array(
	 * '{DAV:}prop3' => null,
	 * ),
	 * 424 => array(
	 * '{DAV:}prop4' => null,
	 * ),
	 * );
	 *
	 * In this previous example prop1 was successfully updated or deleted, and
	 * prop2 was succesfully created.
	 *
	 * prop3 failed to update due to '403 Forbidden' and because of this prop4
	 * also could not be updated with '424 Failed dependency'.
	 *
	 * This last example was actually incorrect. While 200 and 201 could appear
	 * in 1 response, if there's any error (403) the other properties should
	 * always fail with 423 (failed dependency).
	 *
	 * But anyway, if you don't want to scratch your head over this, just
	 * return true or false.
	 *
	 * @param string $path        	
	 * @param array $mutations        	
	 * @return array bool
	 */
	public function updatePrincipal($path, \Sabre\DAV\PropPatch $propPatch) {
		return false;
	}
	
	/**
	 * This method is used to search for principals matching a set of
	 * properties.
	 *
	 * This search is specifically used by RFC3744's principal-property-search
	 * REPORT. You should at least allow searching on
	 * http://sabredav.org/ns}email-address.
	 *
	 * The actual search should be a unicode-non-case-sensitive search. The
	 * keys in searchProperties are the WebDAV property names, while the values
	 * are the property values to search on.
	 *
	 * If multiple properties are being searched on, the search should be
	 * AND'ed.
	 *
	 * This method should simply return an array with full principal uri's.
	 *
	 * If somebody attempted to search on a property the backend does not
	 * support, you should simply return 0 results.
	 *
	 * You can also just return 0 results if you choose to not support
	 * searching at all, but keep in mind that this may stop certain features
	 * from working.
	 *
	 * @param string $prefixPath        	
	 * @param array $searchProperties        	
	 * @return array
	 */
	public function searchPrincipals($prefixPath, array $searchProperties, $test = 'allof') {
		return array ();
	}
	
	/**
	 * Returns the list of members for a group-principal
	 *
	 * @param string $principal        	
	 * @return array
	 */
	public function getGroupMemberSet($principal) {
		return array ();
	}
	
	/**
	 * Returns the list of groups a principal is a member of
	 *
	 * @param string $principal        	
	 * @return array
	 */
	public function getGroupMembership($principal) {
		return array ();
	}
	
	/**
	 * Updates the list of group members for a group principal.
	 *
	 * The principals should be passed as a list of uri's.
	 *
	 * @param string $principal        	
	 * @param array $members        	
	 * @return void
	 */
	public function setGroupMemberSet($principal, array $members) {
	}
}
