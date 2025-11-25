<?php

namespace Limbas\extra\calendar\resource;

use DateTime;

use Exception;
use Limbas\Controllers\LimbasController;
use Limbas\extra\calendar\general\CalendarEvent;
use Limbas\lib\globals\SearchRequest;
use Symfony\Component\HttpFoundation\Request;

class ResourceCalendarController extends LimbasController
{
    protected array $lmbCalFields = [
        'SUBJECT' => 'SUBJECT',
        'STARTSTAMP' => 'STARTSTAMP',
        'ENDSTAMP' => 'ENDSTAMP',
        'COLOR' => 'COLOR',
        'ALLDAY' => 'ALLDAY',
        'REPETITION' => 'REPETITION',
        'REPEATUNTIL' => 'REPEATUNTIL',
        'INTERVAL' => 'INTERVAL'
    ];

    protected array $lmbCalFieldsID = [];

    function __construct($gtabid = null)
    {
        global $gfield;
        foreach ($this->lmbCalFields as $key => $value) {
            $this->lmbCalFieldsID[$key] = $gfield[$gtabid]["argresult_name"][$value];
        }
    }

    /**
     * @throws Exception
     */
    public function handleRequest(Request|array $request): array
    {
        return match ($request->get('action')) {
            'details' => $this->getDetailModal(),
            'save' => $this->saveEvent($request),
            'saveDetails' => $this->saveDetails($request),
            'delete' => $this->deleteEvent($request),
            'get_events' => $this->getEvents($request),
            'get_resources' => $this->getResources($request),
            'get_options' => $this->getOptionsRequest($request),
            default => ['success' => false]
        };
    }

    public function index($request): string
    {
        ob_start();
        include(COREPATH . 'extra/calendar/resource/html/index.php');
        return ob_get_clean() ?: '';
    }

    private function getDetailModal(): array
    {
        global $lang;

        ob_start();
        require COREPATH . 'extra/calendar/resource/html/resourcecalendar_detail_modal.php';
        $htmlModal = ob_get_clean() ?: '';
        return [
            'success' => true,
            'html' => $htmlModal
        ];
    }

    private function saveEvent(Request $request): array
    {
        $tabId = $request->get('tab_id');
        $eventId = $request->get('event_id');
        $resourceId = $request->get('resource_id');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        $title = $request->get('title');

        if ($eventId == null) {
            [$success, $eventId] = $this->addEvent($tabId, $startDate, $endDate, $title, $resourceId);
        } else {
            $success = $this->moveEvent($tabId, $eventId, $startDate, $endDate, $resourceId);
        }

        return [
            'success' => $success,
            'event_id' => $eventId
        ];
    }

    private function addEvent($tabId, $startDate, $endDate, $title, $resourceId): array
    {
        global $gtab;

        require_once COREPATH . 'gtab/gtab.lib';

        $startstamp = strtotime($startDate);
        $endstamp = strtotime($endDate);

        lmb_StartTransaction();
        if ($ID = new_record($tabId)) {
            # --- subject ---
            $tkey = "$tabId," . $this->lmbCalFieldsID['SUBJECT'] . ",$ID";
            $change_kal[$tkey] = $title;
            # --- startdate ---
            $tkey = "$tabId," . $this->lmbCalFieldsID['STARTSTAMP'] . ",$ID";
            $change_kal[$tkey] = stampToDate($startstamp);
            # --- enddate ---
            $tkey = "$tabId," . $this->lmbCalFieldsID['ENDSTAMP'] . ",$ID";
            $change_kal[$tkey] = stampToDate($endstamp);

            # Extension
            /*if (method_exists($this, 'addEventExtend')) {
                $this->addEventExtend($ID, $request, $change_kal);
            }*/

            $resource = parse_db_int($resourceId);
            if ($resource and $gtab['params1'][$tabId]) {
                $relation = init_relation($tabId, $gtab['params1'][$tabId], $ID, $resource);
                $relation['unique'] = 1;
                set_relation($relation);
            }

            if (update_data($change_kal, 3, 0)) {
                lmb_EndTransaction(1);
                $success = true;
            } else {
                lmb_EndTransaction(0);
                $success = false;
            }
        } else {
            lmb_EndTransaction(0);
            $success = false;
        }

        return [$success, $ID];
    }

    private function moveEvent($tabId, $eventId, $startDate, $endDate, $resourceId): bool
    {
        global $gtab;

        $startDate = stampToDate(strtotime($startDate));
        $endDate = stampToDate(strtotime($endDate));

        $update["$tabId," . $this->lmbCalFieldsID['STARTSTAMP'] . ",$eventId"] = $startDate;

        if ($endDate) {
            $update["$tabId," . $this->lmbCalFieldsID['ENDSTAMP'] . ",$eventId"] = $endDate;
        }

        require_once COREPATH . 'gtab/gtab.lib';

        $success = (bool)update_data($update);

        # --- resource ---
        $resource = parse_db_int($resourceId);
        if ($resource and $gtab['params1'][$tabId]) {
            $relation = init_relation($tabId, $gtab['params1'][$tabId], $eventId, $resource);
            $relation['unique'] = 1;
            set_relation($relation);
        }

        return $success;
    }

    private function getEvents($request): array
    {
        $events = $this->getEvent($request);

        $events = array_map(fn(CalendarEvent $e): array => $e->jsonSerialize(), $events);

        return [
            'success' => true,
            'events' => $events
        ];
    }

    /**
     * @throws Exception
     */
    private function getResources(Request $request): array
    {
        $tabId = intval($request->get('tab_id'));

        SearchRequest::fillFromRequest($request);

        $resources = $this->getResourceArray($tabId, $request);

        return [
            'success' => true,
            'resources' => $resources,
        ];
    }

    private function getOptionsRequest(Request $request): array
    {
        $tabId = $request->get('tab_id');

        $options = $this->getOptions($tabId);

        return [
            'success' => true,
            'options' => $options
        ];
    }

    public function getOptions($tabId): array {
        global $gtab;
        global $gformlist;
        global $lang;

        $options = [...$gtab['params2'][$tabId]];
        $customOptions = [];

        $options['customOptions'] = &$customOptions;

        $options['tabId'] = $tabId;

        $customOptions['alertText'] = $lang[24];

        $options['subdivision'] = $this->getSubdivision();

        if ($gtab["tab_view_tform"][$tabId]) {
            $customOptions['customFormId'] = $gtab["tab_view_tform"][$tabId];
            $customOptions['customFormDimension'] = $gformlist[$tabId]["dimension"][$gtab["tab_view_tform"][$tabId]];
        }

        $options['defaultEventColor'] = '#5e5e5e'; # todo set using farbschema

        return $options;
    }

    private function getResourceArray($gtabid, Request $params): array
    {
        global $gsr;
        global $gtab;
        global $gfield;

        require_once(COREPATH . 'gtab/gtab.lib');
        require_once(COREPATH . 'gtab/gtab_type_erg.lib');

        $recsfield = $gtab['params1'][$gtabid];

        $rgtabid = $gfield[$gtabid]["verkntabid"][$recsfield];
        $showfields = $gfield[$gtabid]["verknview"][$recsfield];

        $resourceGsr = $gsr[$gtabid][$recsfield][1];

        foreach ($showfields as $fkey => $fval) {
            $onlyfields[$rgtabid][] = $fval;
        }
        $filter["anzahl"][$rgtabid] = 'all';
        $filter["nolimit"][$rgtabid] = 1;

        $gresult = get_gresult($rgtabid, 1, $filter, $resourceGsr, null, $onlyfields);
        if (!$gresult[$rgtabid]['id']) {
            return [];
        }

        foreach ($gresult[$rgtabid]['id'] as $vkey => $resourceId) {
            # ist of shown fields
            $retrn = array();
            foreach ($showfields as $fkey => $ffieldid) {
                if (!$gfield[$rgtabid]["funcid"][$ffieldid]) {
                    continue;
                }
                $fname = "cftyp_" . $gfield[$rgtabid]["funcid"][$ffieldid];
                $retrn[] = $fname($vkey, $ffieldid, $rgtabid, 3, $gresult, 0);
            }

            $resources[$resourceId] = implode($gfield[$gtabid]["verknviewcut"][$recsfield], $retrn);
        }

        if (method_exists($this, 'lmb_getResourcesExtend')) {
            $this->lmb_getResourcesExtend($gtabid, $params, $gresult, $resources);
        }

        if ($resources) {
            return $resources;
        }

        return [];
    }


    private function getSubdivision(): array
    {
        return [
            'week' => 1,
            'month' => 1,
            'day' => 1
        ];
    }

    protected function getEvent(Request $request): array
    {
        $tabId = $request->get('tab_id');

        $tabId = intval($tabId);

        $tresult = []; // todo this probably doesnt work with extensions, check this

        $gresult = $this->getEventGSR($request);
        $bzm = lmb_count($tresult);

        if ($gresult[$tabId]["id"]) {
            foreach ($gresult[$tabId]["id"] as $key => $value) {

                $stst = get_stamp(lmb_substr($gresult[$tabId][$this->lmbCalFieldsID['STARTSTAMP']][$key], 0, 17) . "00");
                $endst = get_stamp(lmb_substr($gresult[$tabId][$this->lmbCalFieldsID['ENDSTAMP']][$key], 0, 17) . "00");
                if ($stst && $endst && $endst >= $stst) {

                    $this->getEventFromGSR($tresult, $gresult, $tabId, $key, $bzm, $stst, $endst);

                    $bzm++;
                }
            }
        }

        return $tresult;
    }

    protected function getEventGSR(Request $request)
    {
        global $gtab;
        global $gfield;
        global $gsr;

        $tabId = $request->get('tab_id');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        $recsfield = $gtab['params1'][$tabId];

        $rgtabid = $gfield[$tabId]["verkntabid"][$recsfield];
        $showfields = $gfield[$tabId]["verknview"][$recsfield];

        $startstamp = strtotime($startDate);
        $endstamp = strtotime($endDate);

        $fieldlist[$tabId] = $gfield[$tabId]["key"];

        # Zeige Heute
        if (!$startstamp) {
            $startstamp = (local_stamp(1) - 432000);
            $endstamp = (local_stamp(1) + 1209600);
        }

        $tab = $gtab['table'][$tabId];

        $extension['where'][0] = "
			(
			($tab.STARTSTAMP >= '" . convert_stamp($startstamp, 0) . "' AND $tab.STARTSTAMP <= '" . convert_stamp($endstamp, 0) . "') OR
			($tab.ENDSTAMP >= '" . convert_stamp($startstamp, 0) . "' AND $tab.ENDSTAMP <= '" . convert_stamp($endstamp, 0) . "') OR
			($tab.STARTSTAMP < '" . convert_stamp($startstamp, 0) . "' AND $tab.ENDSTAMP > '" . convert_stamp($endstamp, 0) . "')
			)
			";

        # resource
        if ($gfield[$tabId]["md5tab"][$gtab['params1'][$tabId]]) {
            $rfieldid = $gtab['params1'][$tabId];
            $rgtabid = $gfield[$tabId]['verkntabid'][$gtab["params1"][$tabId]];

            $extension['select'][0] = $gfield[$tabId]["md5tab"][$rfieldid] . '.VERKN_ID AS LMB_RESOURCE';
            $fieldlist[$tabId] = $gfield[$tabId]["key"];
            $fieldlist[$tabId][] = 'LMB_RESOURCE';

            $gsrResource = $gsr[$tabId][$recsfield][1];
            if (empty($gsrResource)) {
                $ojoin[$gtab["table"][$tabId]][$gfield[$tabId]["md5tab"][$rfieldid]][0] = $gtab['table'][$tabId] . '.ID = ' . $gfield[$tabId]["md5tab"][$rfieldid] . '.ID';
                $extension["ojoin"] = $ojoin;
            }
        }

        # Query ausführen
        $filter["anzahl"][$tabId] = 'all';
        $filter["nolimit"][$tabId] = 1;
        $filter["order"][$tabId] = "$tabId&" . $this->lmbCalFieldsID['STARTSTAMP'] . "&ASC";

        /*
        # Extension
        if (method_exists($this, 'lmb_getEventGSRExtend')) {
            $this->lmb_getEventGSRExtend($tabId, $filter, $fieldlist, $extension, $request);
        }
        */

        require_once COREPATH . 'gtab/gtab.lib';
        require_once COREPATH . 'gtab/gtab_type.lib';

        return get_gresult($tabId, 1, $filter, $gsr, null, $fieldlist, null, $extension);
    }

    protected function getEventFromGSR(array &$tresult, array &$gresult, int $gtabid, int|string $key, int $bzm, $stst, $endst): void
    {
        global $umgvar;
        global $gtab;

        $color = null;

        # row-color from field
        if (is_array($gresult[$gtabid]["indicator"]["color"][$key])) {
            $color = average_color($gresult[$gtabid]["indicator"]["color"][$key]);
            # row-color from user
        } elseif ($gresult[$gtabid][$this->lmbCalFieldsID['COLOR']][$key]) {
            $color = $gresult[$gtabid][$this->lmbCalFieldsID['COLOR']][$key];
            # row-color from indicator
        } elseif ($gresult[$gtabid]["color"][$key]) {
            $color = $gresult[$gtabid]["color"][$key];
        }

        if ($color) {
            $text_color = lmbSuggestColor($color);
        }


        # show indicator
        if ($gresult[$gtabid]["indicator"]["object"][$key]) {
            $preobject = array();
            foreach ($gresult[$gtabid]["indicator"]["object"][$key] as $ikey => $ival) {
                $preobject[] = $ival;
            }
        }


        $calendarEvent = new CalendarEvent($gresult[$gtabid]['id'][$key]);

        $calendarEvent->title = lmb_substr($gresult[$gtabid][$this->lmbCalFieldsID['SUBJECT']][$key], 0, $umgvar['memolength']);
        $calendarEvent->allDay = (bool)$gresult[$gtabid][$this->lmbCalFieldsID['ALLDAY']][$key];

        $calendarEvent->start = new DateTime('@' . $stst);
        $calendarEvent->end = new DateTime('@' . $endst);
        $calendarEvent->resourceId = $gresult[$gtabid]['LMB_RESOURCE'][$key];

        #$calendarEvent->editable = $this->getFieldPermissions($value, [1], $gtabid);
        #$calendarEvent->startEditable = $this->getFieldPermissions($value, [2, 5], $gtabid);
        #$calendarEvent->durationEditable = $calendarEvent->startEditable;
        #$calendarEvent->resourceEditable = $calendarEvent->startEditable;

        /*
        $calendarEvent->classNames = [
            'event_' . $gresult[$gtabid]["id"][$key]
        ];
        */


        if ($color) {
            $calendarEvent->backgroundColor = $color;
            $calendarEvent->borderColor = $color;
        }
        if ($text_color) {
            $calendarEvent->textColor = $text_color;
        }

        if ($preobject) {
            $calendarEvent->setExtendedProp('evtBeforeEvent', implode('', $preobject));
        }


        $tresult[$bzm] = $calendarEvent;

        /*if (method_exists($this, 'lmb_getEventExtend')) {
            $this->lmb_getEventExtend($tresult, $gresult, $gtabid, $key, $bzm, $stst, $endst);
        }*/
    }

    private function deleteEvent(Request $request): array
    {
        $eventId = $request->get('event_id');
        $tabId = $request->get('tab_id');

        require_once COREPATH . 'gtab/gtab.lib';

        $success = del_data($tabId, $eventId);

        return ['success' => $success];
    }

    private function saveDetails(Request $request): array
    {
        $tabId = $request->get('tab_id');
        $eventId = $request->get('event_id');
        $resourceId = $request->get('resource_id');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        $title = $request->get('title');
        $color = $request->get('color');
        $allDay = $request->get('all_day');

        if ($eventId == null) {
            [$success, $eventId] = $this->addEvent($tabId, $startDate, $endDate, $title, $resourceId);
            if (!$success) {
                return ['success' => $success];
            }
        }

        global $gtab;

        $startDate = stampToDate(strtotime($startDate));
        $endDate = stampToDate(strtotime($endDate));

        $update["$tabId," . $this->lmbCalFieldsID['STARTSTAMP'] . ",$eventId"] = $startDate;

        if ($endDate) {
            $update["$tabId," . $this->lmbCalFieldsID['ENDSTAMP'] . ",$eventId"] = $endDate;
        }

        $update["$tabId," . $this->lmbCalFieldsID['SUBJECT'] . ",$eventId"] = $title;
        $update["$tabId," . $this->lmbCalFieldsID['COLOR'] . ",$eventId"] = $color;
        $update["$tabId," . $this->lmbCalFieldsID['ALLDAY'] . ",$eventId"] = $allDay;

        require_once COREPATH . 'gtab/gtab.lib';

        $success = (bool)update_data($update);

        # --- resource ---
        $resource = parse_db_int($resourceId);
        if ($resource && $gtab['params1'][$tabId]) {
            $relation = init_relation($tabId, $gtab['params1'][$tabId], $eventId, $resource);
            $relation['unique'] = 1;
            set_relation($relation);
        }

        return ['success' => $success, 'event_id' => $eventId];
    }
}
