<?php

namespace Sabre\CalDAV\Backend;

use Sabre\VObject;
use Sabre\CalDAV;
use Sabre\DAV;
use Sabre\VObject\Component as Component;
use Sabre\VObject\Property\ICalendar\DateTime;
use Sabre\VObject\Property\Boolean;
use Sabre\VObject\Property\Time;
use Sabre\VObject\Property\Unknown;

/**
 * Limbas CalDAV backend
 *
 * This backend is used to store calendar-data in Limbas
 *
 * @copyright Copyright (C) 2007-2013 fruux GmbH (https://fruux.com/).
 * @copyright Limbas GmbH
 * @author Evert Pot (http://evertpot.com/)
 * @author Limbas GmbH
 * @license http://code.google.com/p/sabredav/wiki/License Modified BSD License
 */
class Limbas extends AbstractBackend {

	/**
	 * We need to specify a max date, because we need to stop *somewhere*
	 *
	 * On 32 bit system the maximum for a signed integer is 2147483647, so
	 * MAX_DATE cannot be higher than date('Y-m-d', 2147483647) which results
	 * in 2038-01-19 to avoid problems when the date is converted
	 * to a unix timestamp.
	 */
	const MAX_DATE = '2038-01-01';
	
	/**
	 * pdo
	 *
	 * @var \PDO
	 */
	protected $pdo;
	
	/**
	 * The table name that will be used for calendars
	 *
	 * @var string
	 */
	protected $calendarTableName;
	
	/**
	 * The table name that will be used for calendar objects
	 *
	 * @var string
	 */
	protected $calendarObjectTableName;
	
	/**
	 * List of CalDAV properties, and how they map to database fieldnames
	 * Add your own properties by simply adding on to this array.
	 *
	 * Note that only string-based properties are supported here.
	 *
	 * @var array
	 */
	public $propertyMap = array (
			'{DAV:}displayname' => 'displayname',
			'{urn:ietf:params:xml:ns:caldav}calendar-description' => 'description',
			'{urn:ietf:params:xml:ns:caldav}calendar-timezone' => 'timezone',
			'{http://apple.com/ns/ical/}calendar-order' => 'calendarorder',
			'{http://apple.com/ns/ical/}calendar-color' => 'calendarcolor' 
	);
	
	/**
	 * Creates the backend
	 */
	public function __construct() {
	}
	
	/**
	 * Returns a list of calendars for a principal.
	 *
	 * Every project is an array with the following keys:
	 * * id, a unique id that will be used by other functions to modify the
	 * calendar. This can be the same as the uri or a database key.
	 * * uri, which the basename of the uri with which the calendar is
	 * accessed.
	 * * principaluri. The owner of the calendar. Almost always the same as
	 * principalUri passed to this method.
	 *
	 * Furthermore it can contain webdav properties in clark notation. A very
	 * common one is '{DAV:}displayname'.
	 *
	 * @param string $principalUri        	
	 * @return array
	 */
	public function getCalendarsForUser($principalUri) {
		global $gtab;
		global $umgvar;
		global $db;
		
		$fields = array_values ( $this->propertyMap );
		$fields [] = 'id';
		$fields [] = 'uri';
		$fields [] = 'ctag';
		$fields [] = 'components';
		$fields [] = 'principaluri';
		$fields [] = 'transparent';
		
		$calendar = array ();
		$bzm = 0;
		$ctag = array ();
		$sqlquery = 'SELECT LASTMODIFIED, TAB_ID FROM LMB_CONF_TABLES WHERE TYP=2';
		$rs = lmbdb_exec ( $db, $sqlquery ) or errorhandle ( lmbdb_errormsg ( $db ), $sqlquery, $action, __FILE__, __LINE__ );
		while ( lmbdb_fetch_row ( $rs ) ) {
			$ts = lmbdb_result ( $rs, 'LASTMODIFIED' );
			$tabId = lmbdb_result ( $rs, 'TAB_ID' );
			$ctag [$tabId] = get_Stamp ( $ts );
		}
		
		foreach ( $gtab ["typ"] as $calid => $type ) {
			
			if ($type == 2) {
				$calendar = array (
						'components' => 'VEVENT',
						'id' => $calid,
						'uri' => strval($calid),
						'principaluri' => $principalUri,
						'{' . CalDAV\Plugin::NS_CALENDARSERVER . '}getctag' => $ctag [$calid] ? $ctag [$calid] : '0',
						'{' . CalDAV\Plugin::NS_CALDAV . '}supported-calendar-component-set' => new CalDAV\Xml\Property\SupportedCalendarComponentSet ( array (
								'VEVENT' 
						) ),
						'{' . CalDAV\Plugin::NS_CALDAV . '}schedule-calendar-transp' => new CalDAV\Xml\Property\ScheduleCalendarTransp ( 'opaque' ),
						'{DAV:}displayname' => $gtab ['desc'] [$calid],
						'{urn:ietf:params:xml:ns:caldav}calendar-description' => $gtab ['desc'] [$calid],
						'{urn:ietf:params:xml:ns:caldav}calendar-timezone' => $umgvar ['default_timezone'],
						'{http://apple.com/ns/ical/}calendar-order' => $bzm ++,
						'{http://apple.com/ns/ical/}calendar-color' => $gtab ['markcolor'] [$calid] ? '#' . $gtab ['markcolor'] [$calid] : '#000000' 
				);
				
				$calendars [] = $calendar;
			}
		}
		return $calendars;
	}
	
	/**
	 * Creates a new calendar for a principal.
	 *
	 * If the creation was a success, an id must be returned that can be used to reference
	 * this calendar in other methods, such as updateCalendar
	 * error_log ( print_r ( $calendar, 1 ) );
	 *
	 * @param string $principalUri        	
	 * @param string $calendarUri        	
	 * @param array $properties        	
	 * @return string
	 */
	public function createCalendar($principalUri, $calendarUri, array $properties) {
		// not supported
		throw new DAV\Exception ( 'Calendar creation not supported' );
	}
	
	/**
	 * Updates properties for a calendar.
	 *
	 * The mutations array uses the propertyName in clark-notation as key,
	 * and the array value for the property value. In the case a property
	 * should be deleted, the property value will be null.
	 *
	 * This method must be atomic. If one property cannot be changed, the
	 * entire operation must fail.
	 *
	 * If the operation was successful, true can be returned.
	 * If the operation failed, false can be returned.
	 *
	 * Deletion of a non-existent property is always successful.
	 *
	 * Lastly, it is optional to return detailed information about any
	 * failures. In this case an array should be returned with the following
	 * structure:
	 *
	 * array(
	 * 403 => array(
	 * '{DAV:}displayname' => null,
	 * ),
	 * 424 => array(
	 * '{DAV:}owner' => null,
	 * )
	 * )
	 *
	 * In this example it was forbidden to update {DAV:}displayname.
	 * (403 Forbidden), which in turn also caused {DAV:}owner to fail
	 * (424 Failed Dependency) because the request needs to be atomic.
	 *
	 * @param string $calendarId        	
	 * @param \Sabre\DAV\PropPatch $propPatch
	 * @return void
	 */
	public function updateCalendar($calendarId, \Sabre\DAV\PropPatch $propPatch) {
		// not possible
	}
	
	/**
	 * Delete a calendar and all it's objects
	 *
	 * @param string $calendarId        	
	 * @return void
	 */
	public function deleteCalendar($calendarId) {
		// not supported
		throw new DAV\Exception ( 'Calendar deletion not supported' );
	}
	
	/**
	 * Returns all calendar objects within a calendar.
	 *
	 * Every item contains an array with the following keys:
	 * * id - unique identifier which will be used for subsequent updates
	 * * calendardata - The iCalendar-compatible calendar data
	 * * uri - a unique key which will be used to construct the uri. This can be any arbitrary string.
	 * * lastmodified - a timestamp of the last modification time
	 * * etag - An arbitrary string, surrounded by double-quotes. (e.g.:
	 * ' "abcdef"')
	 * * calendarid - The calendarid as it was passed to this function.
	 * * size - The size of the calendar objects, in bytes.
	 *
	 * Note that the etag is optional, but it's highly encouraged to return for
	 * speed reasons.
	 *
	 * The calendardata is also optional. If it's not returned
	 * 'getCalendarObject' will be called later, which *is* expected to return
	 * calendardata.
	 *
	 * If neither etag or size are specified, the calendardata will be
	 * used/fetched to determine these numbers. If both are specified the
	 * amount of times this is needed is reduced by a great degree.
	 *
	 * @param string $calendarId        	
	 * @return array
	 */
	public function getCalendarObjects($calendarId) {
		return $this->lmb_getEvents ( $calendarId, null );
	}
	
	/**
	 * Helper function to get calendar events from Limbas
	 *
	 * @param unknown $calendarId        	
	 * @param string $gsr        	
	 * @return array
	 */
	private function lmb_getEvents($calendarId, $gsr = null) {
		global $gfield;
		
		$lmb_UID = $gfield[$calendarId]['argresult_name']['UID'];
		
		$filter ['anzahl'] [$calendarId] = 'all';
		$filter ['order'] [$calendarId] = null;
		
		$gresult = get_gresult ( $calendarId, 1, $filter, $gsr, null, null, null, null );
		$gresult = $gresult [$calendarId];
		
		$result = array ();
		
		foreach ( $gresult ['id'] as $key => $value ) {
			$d = $this->getCalendarObject ( $calendarId, $value . '.ics' );
			$editdatum = $gresult ['EDITDATUM'] [$key];
			$erstdatum = $gresult ['ERSTDATUM'] [$key];
			$uid = $gresult [$lmb_UID] [$key];
			$result [] = array (
					'id' => $uid ? $uid : $value,
					'uri' => $uid ? $uid . '.ics' : $value . '.ics',
					'lastmodified' => $editdatum ? get_Stamp ( $editdatum ) : get_Stamp ( $erstdatum ),
					'etag' => '"' . $d ['etag'] . '"',
					'calendarid' => $calendarId,
					'size' => $d ['size'] 
			);
		}
		
		return $result;
	}
	
	/**
	 * Convert a db time string to the VCAL time format
	 * 
	 * @param string $dbtime
	 * @return string
	 */
	private function lmb_getVcalStamp($dbtime) {
		$format = 'Ymd\THis\Z';
		$ts = get_Stamp ( $dbtime );
		return\date ( $format, $ts );
	}
	/**
	 * Convert a db time string to PHP DateTime
	 * 
	 * @param string $dbtime        	
	 * @return \DateTime
	 */
	private function lmb_getDateTime($dbtime) {
		$ts = get_Stamp ( $dbtime );
		$dt = new \DateTime ();
		$dt->setTimestamp ( $ts );
		
		return $dt;
	}
	
	/**
	 * Returns information from a single calendar object, based on it's object
	 * uri.
	 *
	 * The returned array must have the same keys as getCalendarObjects. The
	 * 'calendardata' object is required here though, while it's not required
	 * for getCalendarObjects.
	 *
	 * This method must return null if the object did not exist.
	 *
	 * @param string $calendarId        	
	 * @param string $objectUri        	
	 * @return array null
	 */
	public function getCalendarObject($calendarId, $objectUri) {
		global $umgvar;
		global $gtab;
		global $db;
		global $gfield;
		
		$lmb_ALLDAY = $gfield[$calendarId]['argresult_name']['ALLDAY']; #(12)
		$lmb_UID = $gfield[$calendarId]['argresult_name']['UID']; #(15)
		$lmb_REPETITION = $gfield[$calendarId]['argresult_name']['REPETITION']; #(16)
		$lmb_REPEATUNTIL = $gfield[$calendarId]['argresult_name']['REPEATUNTIL']; #(17)
		$lmb_EXTRAPROPERTIES = $gfield[$calendarId]['argresult_name']['EXTRAPROPERTIES']; #(18)
		$lmb_INTERVALS = $gfield[$calendarId]['argresult_name']['INTERVALS']; #(19)
		
		
		// {event id}={uri}.ics
		$c = basename ( $objectUri, '.ics' );
		
		$id = null;
		// Events created by limbas have a numeric id, while those created by caldav clients use UUIDs
		if (! is_numeric ( $c )) {
			$sqlquery = "SELECT ID FROM " . $gtab ['table'] [$calendarId] . " WHERE UID = '$c'";
			$rs = lmbdb_exec ( $db, $sqlquery ) or errorhandle ( lmbdb_errormsg ( $db ), $sqlquery, $action, __FILE__, __LINE__ );
			$id = lmbdb_result ( $rs, "ID" );
		} else {
			$id = $c;
		}
		
		if (! $id) {
			return array ();
		}
		
		$gresult = get_gresult ( $calendarId, 1, null, null, null, null, $id );
		
		$vcal = new Component\VCalendar ( array (), false );
		
		$vevent = $vcal->createComponent ( 'VEVENT' );
		$gresult = $gresult [$calendarId];
		$dtStart = $vcal->createProperty ( 'DTSTART' );
		$dtEnd = $vcal->createProperty ( 'DTEND' );
		
		if ($gresult [$lmb_ALLDAY] [0] == true) {
			$dtStart ['VALUE'] = 'DATE';
			$dtEnd ['VALUE'] = 'DATE';
		}
		
		// Getting event reminders from Limbas
		$reminders = lmb_getReminder ( $calendarId, $id );
		$reminders = $reminders ['datum'];
		
		if (count ( $reminders ) > 0) {
			foreach ( $reminders as $key => $date ) {
				$valarm = $vcal->createComponent ( 'VALARM' );
				$valarm->ACTION = 'DISPLAY';
				$dt = new \DateTime ();
				$dt->setTimestamp ( dateToStamp ( $date ) );
				$dt->setTimezone ( new \DateTimeZone ( 'UTC' ) );
				
				$trigger = $vcal->createProperty ( 'TRIGGER', $dt->format ( 'Ymd\\THis\\Z' ), array (
						'VALUE' => 'DATE-TIME' 
				) );
				
				$valarm->add ( $trigger );
				$vevent->add ( $valarm );
			}
		}
		
		$dtStart->setDateTime ( $this->lmb_getDateTime ( $gresult [7] [0] ) );
		$vevent->add ( $dtStart );
		
		$dtEnd->setDateTime ( $this->lmb_getDateTime ( $gresult [8] [0] ) );
		$vevent->add ( $dtEnd );
		
		$created = $vcal->createProperty ( 'CREATED' );
		$created->setDateTime ( $this->lmb_getDateTime ( $gresult ['ERSTDATUM'] [0] ) );
		$vevent->add ( $created );
		
		$modified = $vcal->createProperty ( 'LAST-MODIFIED' );
		$modified->setDateTime ( $this->lmb_getDateTime ( $gresult ['EDITDATUM'] [0] ? $gresult ['EDITDATUM'] [0] : $gresult ['ERSTDATUM'] [0] ) );
		$vevent->add ( $modified );
		
		switch ($gresult [$lmb_REPETITION] [0]) {
			case lmb_utf8_decode ( 'täglich' ) :
				$rrule = 'FREQ=DAILY';
				break;
			case lmb_utf8_decode ( 'wöchentlich' ) :
				$rrule = 'FREQ=WEEKLY';
				break;
			case lmb_utf8_decode ( 'monatlich' ) :
				$rrule = 'FREQ=MONTHLY';
				break;
			case lmb_utf8_decode ( 'jährlich' ) :
				$rrule = 'FREQ=YEARLY';
				break;
		}
		
		if ($gresult [$lmb_REPEATUNTIL] [0] != null && $gresult [$lmb_REPEATUNTIL] [0] != '') {
			$rrule = $rrule . ';until=' . $this->lmb_getVcalStamp ( $gresult [$lmb_REPEATUNTIL] [0] );
		}
		
		if ($rrule != null)
		$vevent->RRULE = $rrule;
		$vevent->SEQUENCE = 0;
		$vevent->SUMMARY = lmb_utf8_encode ( $gresult [9] [0] );
		
		$extraData = json_decode ( $gresult [$lmb_EXTRAPROPERTIES] [0] );
		
		// Add extra data like invitations and special properties
		if ($extraData != null) {
			foreach ( $extraData as $key => $value ) {
				foreach ( $value as $prop => $val ) {
					$vevent->add ( lmb_strtoupper ( $val [0] ), ( array ) ($val [3]), get_object_vars ( $val [1] ) );
				}
			}
		}
		
		$vevent->UID = $gresult [$lmb_UID] [0] ? $gresult [$lmb_UID] [0] : $c;
		$vcal->add ( $vevent );
		$data = $vcal->serialize ();
		
		$result = array (
				'id' => $gresult [$lmb_UID] [0] ? $gresult [$lmb_UID] [0] : $c,
				'uri' => $gresult [$lmb_UID] [0] ? $gresult [$lmb_UID] [0] . '.ics' : $objectUri,
				'lastmodified' => $this->lmb_getDateTime(['EDITDATUM'] [0] ? $gresult ['EDITDATUM'] [0] : $gresult ['ERSTDATUM'] [0]),
				'etag' => '"' . md5 ( $data ) . '"',
				'calendarid' => $calendarId,
				'size' => lmb_strlen ( $data ),
				'calendardata' => $data 
		);
		return $result;
	}
	
	/**
	 * Helper function for creating
	 *
	 * @param string $calendarId        	
	 * @param string $objectUri        	
	 * @param unknown $calendarData        	
	 * @param string $create        	
	 * @return NULL
	 */
	private function lmb_updateCalendarObject($calendarId, $objectUri, $calendarData, $create = false) {
		global $gtab;
		global $db;
		global $gfield;
		
		$lmb_ALLDAY = $gfield[$calendarId]['argresult_name']['ALLDAY']; #(12)
		$lmb_UID = $gfield[$calendarId]['argresult_name']['UID']; #(15)
		$lmb_REPETITION = $gfield[$calendarId]['argresult_name']['REPETITION']; #(16)
		$lmb_REPEATUNTIL = $gfield[$calendarId]['argresult_name']['REPEATUNTIL']; #(17)
		$lmb_EXTRAPROPERTIES = $gfield[$calendarId]['argresult_name']['EXTRAPROPERTIES']; #(18)
		$lmb_INTERVALS = $gfield[$calendarId]['argresult_name']['INTERVALS']; #(19)
		
		$extraData = $this->getDenormalizedData ( $calendarData );
		
		$update = array ();
		$dataId = null;
		$uid = basename ( $objectUri, '.ics' );
		$gresult = null;
		
		/*
		# prüfe auf doppelte $uid wenn ja keinen neuen anlegen sondern überschreiben;
		# Nötig falls client $uid doppelt vergiebt
		# unterer Aufruf in else muß auskommentiert werden
		if (! is_numeric ( $uid )) {
			$sqlquery = "SELECT ID FROM " . $gtab ['table'] [$calendarId] . " WHERE UID = '$uid'";
			$rs = lmbdb_exec ( $db, $sqlquery ) or errorhandle ( lmbdb_errormsg ( $db ), $sqlquery, $action, __FILE__, __LINE__ );
			$dataId = lmbdb_result ( $rs, "ID" );
			if($dataId){$create = false;}
		} else {
			$dataId = $uid;
		}
		*/

		// Creation of event by Caldav client
		if ($create) {
			$dataId = new_data ( $calendarId );
			// UID should not change
			$update ["$calendarId,$lmb_UID,$dataId"] = parse_db_string ( $uid, 36 );
			// Update of event
		} else {
			// Events created by limbas have a numeric id, while those created by caldav clients use UUIDs
			if (! is_numeric ( $uid )) {
				$sqlquery = "SELECT ID FROM " . $gtab ['table'] [$calendarId] . " WHERE UID = '$uid'";
				$rs = lmbdb_exec ( $db, $sqlquery ) or errorhandle ( lmbdb_errormsg ( $db ), $sqlquery, $action, __FILE__, __LINE__ );
				$dataId = lmbdb_result ( $rs, "ID" );
			} else {
				$dataId = $uid;
			}
			$gresult = get_gresult ( $calendarId, 1, null, null, null, null, $dataId );
			$gresult = $gresult [$calendarId];
		}
		// Set as an all-day event in limbas
		if ($extraData ['end'] - $extraData ['start'] == 86400) {
			$update ["$calendarId,$lmb_ALLDAY,$dataId"] = parse_db_bool ( true );
		}
		if (! $create) {
			$reminder = lmb_getReminder ( $calendarId, $dataId );
			if ($reminder != null) {
				foreach ( $reminder as $key => $value ) {
					lmb_dropReminder ( $value ['id'] );
				}
			}
		}
		foreach ( $extraData ['alarm'] as $key => $value ) {
			lmb_addReminder ( stampToDate ( $value->getTimestamp () ), substr ( $extraData ['summary'], 0, 60 ), $calendarId, $dataId );
		}
		$update ["$calendarId,7,$dataId"] = convert_stamp ( $extraData ['start'] );
		$update ["$calendarId,8,$dataId"] = convert_stamp ( $extraData ['end'] );
		$update ["$calendarId,9,$dataId"] = lmb_utf8_decode ( parse_db_string ( $extraData ['summary'], 399 ) );
		$rule = null;
		switch (lmb_strtoupper ( $extraData ['frequency'] )) {
			case 'DAILY' :
				$rule = 'täglich';
				break;
			case 'WEEKLY' :
				$rule = 'wöchentlich';
				break;
			case 'MONTHLY' :
				$rule = 'monatlich';
				break;
			case 'YEARLY' :
				$rule = 'jährlich';
				break;
		}
		$update ["$calendarId,$lmb_REPETITION,$dataId"] = lmb_utf8_decode ( parse_db_string ( $rule ) );
		$update ["$calendarId,$lmb_REPEATUNTIL,$dataId"] = convert_stamp ( $extraData ['lastOccurence'] );
		$extras = $extraData ['extras'];
		$update ["$calendarId,$lmb_EXTRAPROPERTIES,$dataId"] = json_encode ( $extras );

		
		update_data ( $update );
		return null;
	}
	
	/**
	 * Creates a new calendar object.
	 *
	 * It is possible return an etag from this function, which will be used in
	 * the response to this PUT request. Note that the ETag must be surrounded
	 * by double-quotes.
	 *
	 * However, you should only really return this ETag if you don't mangle the
	 * calendar-data. If the result of a subsequent GET to this object is not
	 * the exact same as this request body, you should omit the ETag.
	 *
	 * @param mixed $calendarId        	
	 * @param string $objectUri        	
	 * @param string $calendarData        	
	 * @return string null
	 */
	public function createCalendarObject($calendarId, $objectUri, $calendarData) {
		$this->lmb_updateCalendarObject ( $calendarId, $objectUri, $calendarData, true );
		return null;
	}
	
	/**
	 * Updates an existing calendarobject, based on it's uri.
	 *
	 * It is possible return an etag from this function, which will be used in
	 * the response to this PUT request. Note that the ETag must be surrounded
	 * by double-quotes.
	 *
	 * However, you should only really return this ETag if you don't mangle the
	 * calendar-data. If the result of a subsequent GET to this object is not
	 * the exact same as this request body, you should omit the ETag.
	 *
	 * @param mixed $calendarId        	
	 * @param string $objectUri        	
	 * @param string $calendarData        	
	 * @return string null
	 */
	public function updateCalendarObject($calendarId, $objectUri, $calendarData) {
		$this->lmb_updateCalendarObject ( $calendarId, $objectUri, $calendarData, false );
		return null;
	}
	
	/**
	 * Parses some information from calendar objects, used for optimized
	 * calendar-queries.
	 *
	 * Returns an array with the following keys:
	 * * etag
	 * * size
	 * * componentType
	 * * start
	 * * lastOccurence
	 *
	 * @param string $calendarData        	
	 * @return array
	 */
	protected function getDenormalizedData($calendarData) {
		$vObject = VObject\Reader::read ( $calendarData );
		$componentType = null;
		$component = null;
		$start = null;
		$lastOccurence = null;
		$summary = null;
		$frequency = null;
		$eventEnd = null;
		$alarm = array ();
		foreach ( $vObject->getComponents () as $component ) {
			if ($component->name !== 'VTIMEZONE') {
				$componentType = $component->name;
				break;
			}
		}
		if (! $componentType) {
			throw new \Sabre\DAV\Exception\BadRequest ( 'Calendar objects must have a VJOURNAL, VEVENT or VTODO component' );
		}
		if ($componentType === 'VEVENT') {
			$start = $component->DTSTART->getDateTime ()->getTimeStamp ();
			$eventEnd = $component->DTEND->getDateTime ()->getTimeStamp ();
			$extra = null;
			$organizer = $component->ORGANIZER;
			if ($organizer != null) {
				
				$extra ['ORGANIZER'] [] = $organizer->jsonSerialize ();
				
				$attendee = $component->ATTENDEE;
				if ($attendee != null) {
					foreach ( $attendee as $att ) {
						$attSerial = $att->jsonSerialize ();
						$email = ( string ) $attSerial [3];
						$extra ['ATTENDEE'] [$email] = $attSerial;
					}
				}
				$xmsi = $component->{'X-MOZ-SEND-INVITATIONS'};
				if ($xmsi != null) {
					$tmp = new Boolean ( $component, 'X-MOZ-SEND-INVITATIONS', $xmsi->getRawMimeDirValue () );
					$extra ['X-MOZ-SEND-INVITATIONS'] [] = $tmp->jsonSerialize ();
				}

				
			}
			$xmst= $component->{'X-MOZ-SNOOZE-TIME'};
			if ($xmst != null) {			
				$tmp = new Unknown( $component, 'X-MOZ-SNOOZE-TIME', $xmst->getRawMimeDirValue () );
				$extra ['X-MOZ-SNOOZE-TIME'] [] = $tmp->jsonSerialize ();
			
			}
			
			// Finding the last occurence is a bit harder
			if (! isset ( $component->RRULE )) {
				if (isset ( $component->DTEND )) {
					$lastOccurence = $component->DTEND->getDateTime ()->getTimeStamp ();
				} elseif (isset ( $component->DURATION )) {
					$endDate = clone $component->DTSTART->getDateTime ();
					$endDate->add ( VObject\DateTimeParser::parse ( $component->DURATION->getValue () ) );
					$lastOccurence = $endDate->getTimeStamp ();
				} elseif (! $component->DTSTART->hasTime ()) {
					$endDate = clone $component->DTSTART->getDateTime ();
					$endDate->modify ( '+1 day' );
					$lastOccurence = $endDate->getTimeStamp ();
				} else {
					$lastOccurence = $start;
				}
			} else {
				$it = new VObject\RecurrenceIterator ( $vObject, ( string ) $component->UID );
				$frequency = $it->frequency;
				$maxDate = new \DateTime ( self::MAX_DATE );
				if ($it->isInfinite ()) {
					$lastOccurence = $maxDate->getTimeStamp ();
				} else {
					$end = $it->getDtEnd ();
					while ( $it->valid () && $end < $maxDate ) {
						$end = $it->getDtEnd ();
						$it->next ();
					}
					$lastOccurence = $end->getTimeStamp ();
				}
			}
			if ($component->VALARM != null) {
				foreach ( $component->VALARM as $key => $value ) {
					
					$alarm [] = $value->getEffectiveTriggerTime ();
				}
			}
			
			$summary = $component->SUMMARY->getValue ();
		}
		$result = array (
				'etag' => md5 ( $calendarData ),
				'size' => lmb_strlen ( $calendarData ),
				'componentType' => $componentType,
				'start' => $start,
				'end' => $eventEnd,
				'lastOccurence' => $lastOccurence,
				'frequency' => $frequency,
				'summary' => $summary,
				'alarm' => $alarm,
				'extras' => $extra 
		);
		return $result;
	}
	
	/**
	 * Deletes an existing calendar object.
	 *
	 * @param string $calendarId        	
	 * @param string $objectUri        	
	 * @return void
	 */
	public function deleteCalendarObject($calendarId, $objectUri) {
		global $db;
		global $gtab;
		$objectId = basename ( $objectUri, '.ics' );
		
		$id = null;
		// Events created by limbas have a numeric id, while those created by caldav clients use UUIDs
		if (! is_numeric ( $objectId )) {
			
			$sqlquery = "SELECT ID FROM " . $gtab ['table'] [$calendarId] . " WHERE UID = '$objectId'";
			
			$rs = lmbdb_exec ( $db, $sqlquery ) or errorhandle ( lmbdb_errormsg ( $db ), $sqlquery, $action, __FILE__, __LINE__ );
			$id = lmbdb_result ( $rs, "ID" );
		} else {
			$id = $objectId;
		}
		del_data ( $calendarId, $id );
	}
	
	/**
	 * Performs a calendar-query on the contents of this calendar.
	 *
	 * The calendar-query is defined in RFC4791 : CalDAV. Using the
	 * calendar-query it is possible for a client to request a specific set of
	 * object, based on contents of iCalendar properties, date-ranges and
	 * iCalendar component types (VTODO, VEVENT).
	 *
	 * This method should just return a list of (relative) urls that match this
	 * query.
	 *
	 * The list of filters are specified as an array. The exact array is
	 * documented by \Sabre\CalDAV\CalendarQueryParser.
	 *
	 * Note that it is extremely likely that getCalendarObject for every path
	 * returned from this method will be called almost immediately after. You
	 * may want to anticipate this to speed up these requests.
	 *
	 * This method provides a default implementation, which parses *all* the
	 * iCalendar objects in the specified calendar.
	 *
	 * This default may well be good enough for personal use, and calendars
	 * that aren't very large. But if you anticipate high usage, big calendars
	 * or high loads, you are strongly adviced to optimize certain paths.
	 *
	 * The best way to do so is override this method and to optimize
	 * specifically for 'common filters'.
	 *
	 * Requests that are extremely common are:
	 * * requests for just VEVENTS
	 * * requests for just VTODO
	 * * requests with a time-range-filter on a VEVENT.
	 *
	 * ..and combinations of these requests. It may not be worth it to try to
	 * handle every possible situation and just rely on the (relatively
	 * easy to use) CalendarQueryValidator to handle the rest.
	 *
	 * Note that especially time-range-filters may be difficult to parse. A
	 * time-range filter specified on a VEVENT must for instance also handle
	 * recurrence rules correctly.
	 * A good example of how to interprete all these filters can also simply
	 * be found in \Sabre\CalDAV\CalendarQueryFilter. This class is as correct
	 * as possible, so it gives you a good idea on what type of stuff you need
	 * to think of.
	 *
	 * This specific implementation (for the PDO) backend optimizes filters on
	 * specific components, and VEVENT time-ranges.
	 *
	 * @param string $calendarId        	
	 * @param array $filters        	
	 * @return array
	 */
	public function calendarQuery($calendarId, array $filters) {
		$result = array ();
		$validator = new \Sabre\CalDAV\CalendarQueryValidator ();
		
		$componentType = null;
		$requirePostFilter = true;
		$timeRange = null;
		
		// if no filters were specified, we don't need to filter after a query
		if (! $filters ['prop-filters'] && ! $filters ['comp-filters']) {
			$requirePostFilter = false;
		}
		
		// Figuring out if there's a component filter
		if (count ( $filters ['comp-filters'] ) > 0 && ! $filters ['comp-filters'] [0] ['is-not-defined']) {
			$componentType = $filters ['comp-filters'] [0] ['name'];
			
			// Checking if we need post-filters
			if (! $filters ['prop-filters'] && ! $filters ['comp-filters'] [0] ['comp-filters'] && ! $filters ['comp-filters'] [0] ['time-range'] && ! $filters ['comp-filters'] [0] ['prop-filters']) {
				$requirePostFilter = false;
			}
			// There was a time-range filter
			if ($componentType == 'VEVENT' && isset ( $filters ['comp-filters'] [0] ['time-range'] )) {
				$timeRange = $filters ['comp-filters'] [0] ['time-range'];
				
				// If start time OR the end time is not specified, we can do a
				// 100% accurate mysql query.
				if (! $filters ['prop-filters'] && ! $filters ['comp-filters'] [0] ['comp-filters'] && ! $filters ['comp-filters'] [0] ['prop-filters'] && (! $timeRange ['start'] || ! $timeRange ['end'])) {
					$requirePostFilter = false;
				}
			}
		}
		
		if ($requirePostFilter) {
			$query = "SELECT uri, calendardata FROM " . $this->calendarObjectTableName . " WHERE calendarid = :calendarid";
		} else {
			$query = "SELECT uri FROM " . $this->calendarObjectTableName . " WHERE calendarid = :calendarid";
		}
		
		$values = array (
				'calendarid' => $calendarId 
		);
		
		if ($componentType) {
			$values ['componenttype'] = $componentType;
		}
		
		if ($timeRange && $timeRange ['start']) {
			$values ['startdate'] = $timeRange ['start']->getTimeStamp ();
		}
		if ($timeRange && $timeRange ['end']) {
			$values ['enddate'] = $timeRange ['end']->getTimeStamp ();
		}
		
		$v = $this->getCalendarObjects ( $calendarId );
		$gsr [$calendarId] [7] [0] = stampToDate ( $values ['startdate'] );
		$gsr [$calendarId] [7] ['num'] [0] = 5;
		
		$calObjects = $this->lmb_getEvents ( $calendarId, $gsr );
		
		$result = array ();
		foreach ( $calObjects as $calObject ) {
			if ($requirePostFilter) {
				if (! $this->validateFilterForObject ( $row, $filters )) {
					continue;
				}
			}
			$result [] = $calObject ['uri'];
		}
		return $result;
	}
}
