<?php

namespace Limbas\extra\calendar\fullcalendar;

use DateTime;
use Limbas\Controllers\LimbasController;
use Limbas\extra\calendar\general\CalendarEvent;

class FullCalendarController extends LimbasController
{
    /**
     * Mapping of calendar fields. Used when overriding it for an extension
     * @var array|string[]
     */
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

    function lmb_getlmbCalFields(): array
    {
        return $this->lmbCalFields;
    }

    function lmb_getlmbCalFieldsID(): array
    {
        return $this->lmbCalFieldsID;
    }

    public function handleRequest(array $request): array
    {
        $response = match ($request['action']) {
            'delete' => $this->deleteEvent($request),
            'drop', 'resize' => $this->dropEvent($request),
            'move' => $this->moveEvent($request),
            'copy' => $this->copyEvent($request),
            'add' => $this->addEvent($request),
            'details' => $this->getDetails($request),
            'saveDetails' => $this->saveDetails($request),
            'get' => $this->getEvent($request),
            'reload' => $this->reloadEvents($request),
            default => ['success' => false],
        };
        if($request['reload']) {
            $response = $this->reloadEvents($request);
        }
        return $response;
    }

    /**
     * @param $request mixed IMPORTANT -> used to extract request parameters in include
     * @return string
     */
    public function index(mixed $request): string
    {
        ob_start();
        include(COREPATH . 'extra/calendar/fullcalendar/html/index.php');
        return ob_get_clean() ?: '';
    }

    private function initRelationCalendar(array $request): array|false
    {
        [
            'verkn_ID' => $verkn_ID,
            'verkn_tabid' => $verkn_tabid,
            'verkn_fieldid' => $verkn_fieldid,
        ] = $request;

        return $verkn_ID ? init_relation($verkn_tabid, $verkn_fieldid, $verkn_ID, null, null, 1) : false;
    }

    private function deleteEvent(array $request): array
    {
        [
            'ID' => $ID,
            'gtabid' => $gtabid,
        ] = $request;

        $verkn = $this->initRelationCalendar($request);

        $forcedel = $verkn['typ'] == 27 ? 1 : null;

        require_once COREPATH . 'gtab/gtab.lib';

        $success = del_data($gtabid, $ID, 'delete', $forcedel);

        return ['success' => $success];
    }

    private function dropEvent(array $request): array
    {
        [
            'gtabid' => $gtabid,
            'ID' => $ID,
            'newStartDate' => $newStartDate,
            'newEndDate' => $newEndDate,
            'allDay' => $allDay,
            'oldAllDay' => $oldAllDay
        ] = $request;

        $update["$gtabid," . $this->lmbCalFieldsID['STARTSTAMP'] . ",$ID"] = $newStartDate;

        # TODO use delta to keep the day count instead of 1 day
        if ($allDay != $oldAllDay && !$newEndDate) {
            $newEndDate = $newStartDate;
            if ($allDay) {
                $newEndDate[1] = intval($newEndDate[1]) + 1;
            }
        }

        if ($newEndDate) {
            $update["$gtabid," . $this->lmbCalFieldsID['ENDSTAMP'] . ",$ID"] = $newEndDate;
        }

        $update["$gtabid," . $this->lmbCalFieldsID['ALLDAY'] . ",$ID"] = $allDay;

        require_once COREPATH . 'gtab/gtab.lib';

        $success = (bool)update_data($update);
        return ['success' => $success];
    }

    private function moveEvent(array $request, $newID = null): array
    {
        global $db;
        global $gtab;
        global $action;

        [
            'gtabid' => $gtabid,
            'ID' => $ID,
            'movestamp' => $movestamp,
        ] = $request;

        if ($newID) {
            $ID = $newID;
        }

        $sqlquery = "SELECT " . $this->lmbCalFields['STARTSTAMP'] . "," . $this->lmbCalFields['ENDSTAMP'] . " FROM " . $gtab["table"][$gtabid] . " WHERE ID = " . $ID;
        $rs = lmbdb_exec($db, $sqlquery) or errorhandle(lmbdb_errormsg($db), $sqlquery, $action, __FILE__, __LINE__);
        if (!$rs) {
            $commit = 1;
        }
        if (lmbdb_fetch_row($rs)) {
            $startstamp = get_stamp(lmbdb_result($rs, $this->lmbCalFields['STARTSTAMP']));
            $endstamp = get_stamp(lmbdb_result($rs, $this->lmbCalFields['ENDSTAMP']));
        }
        $start = convert_stamp($movestamp);
        $len = ($endstamp - $startstamp);
        $end = convert_stamp($movestamp + $len);

        $sqlquery = "UPDATE " . $gtab["table"][$gtabid] . " SET " . $this->lmbCalFields['STARTSTAMP'] . " = '$start', " . $this->lmbCalFields['ENDSTAMP'] . " = '$end' WHERE ID = " . $ID;
        $rs = lmbdb_exec($db, $sqlquery) or errorhandle(lmbdb_errormsg($db), $sqlquery, $action, __FILE__, __LINE__);
        if (!$rs) {
            $commit = 1;
        }

        return ['success' => true];
    }

    private function copyEvent(array $request): array
    {
        [
            'gtabid' => $gtabid,
            'ID' => $ID
        ] = $request;

        $newID = new_data($gtabid, null, $ID);

        return $this->moveEvent($request, $newID);
    }

    private function addEvent(array $request): array
    {
        require_once COREPATH . 'gtab/gtab.lib';

        [
            'gtabid' => $gtabid,
            'title' => $title,
            'start' => $start,
            'end' => $end,
            'allDay' => $allDay
        ] = $request;

        $verkn = $this->initRelationCalendar($request);

        lmb_StartTransaction();
        if ($ID = new_record($gtabid, $verkn["verknpf"], $verkn["fieldid"], $verkn["tabid"], $verkn["id"], 0, 0)) {
            # --- subject ---
            $tkey = "$gtabid," . $this->lmbCalFieldsID['SUBJECT'] . ",$ID";
            $change_kal[$tkey] = $title;
            # --- startdate ---
            $tkey = "$gtabid," . $this->lmbCalFieldsID['STARTSTAMP'] . ",$ID";
            $change_kal[$tkey] = stampToDate($start);
            # --- enddate ---
            $tkey = "$gtabid," . $this->lmbCalFieldsID['ENDSTAMP'] . ",$ID";
            $change_kal[$tkey] = stampToDate($end);
            # --- allday ---
            $tkey = "$gtabid," . $this->lmbCalFieldsID['ALLDAY'] . ",$ID";
            $change_kal[$tkey] = $allDay;

            # Extension
            if (method_exists($this, 'addEventExtend')) {
                $this->addEventExtend($ID, $request, $change_kal);
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

        $return = ['success' => $success];
        if ($success) {
            $return += ['ID' => $ID];
        }
        return $return;
    }

    private function getDetails(array $request): array
    {
        global $gform;
        global $lang;
        global $gfield;

        [
            'gtabid' => $gtabid,
            'form_id' => $form_id,
            'ID' => $ID,
            'act' => $act,
        ] = $request;

        $html = '';

        if ($form_id) {
            require_once COREPATH . 'gtab/gtab.lib';
            require_once COREPATH . 'gtab/gtab_form.lib';
            require_once COREPATH . 'gtab/gtab_type.lib';
            require_once COREPATH . 'gtab/sql/gtab_change.dao';

            $ID = floor($ID);

            ob_start();
            if ($act == 'gtab_change') {
                $gresult = get_gresult($gtabid, null, null, null, null, $gform[$form_id]["used_fields"], $ID);
                form_gresult($ID, $gtabid, $form_id, $gresult); # need for fields of related tables
            } else {
                $ID = 0; # bei Anlage neuer Datensatz
            }
            formListElements($act, $gtabid, $ID, $gresult, $form_id);
            $htmlForm = ob_get_clean() ?: '';
        } else {
            require_once COREPATH . 'gtab/gtab.lib';
            require_once COREPATH . 'gtab/gtab_type.lib';
            $ID = floor($ID);
            if ($ID) {
                $gresult = get_gresult($gtabid, 1, null, null, null, null, $ID);
            } else {
                $ID = 0;
            }

            ob_start();
            require COREPATH . 'extra/calendar/fullcalendar/html/cal_detail.php';
            $htmlForm = ob_get_clean() ?: '';
        }

        ob_start();
        require COREPATH . 'extra/calendar/fullcalendar/html/cal_detail_modal.php';
        $htmlModal = ob_get_clean() ?: '';

        $return = ['success' => true];
        if ($htmlForm) {
            $return += ['html_form' => $htmlForm];
        }
        $return += ['html_modal' => $htmlModal];
        return $return;
    }

    private function saveDetails(array $request): array
    {
        [
            'calBulk_termStaH' => $calBulk_termStaH,
        ] = $request;

        $html = null;

        if ($calBulk_termStaH) {
            # adding bulk dates
            $html = $this->addBulkDate($request);
        } else {
            # add / change single date
            $this->historyUpdate($request);
        }

        $return = ['success' => true];
        if ($html) {
            $return += ['html' => $html];
        }

        return $return;
    }

    private function addBulkDate(array &$request): null|string
    {
        global $LINK;

        [
            "calBulk_periodStart" => $calBulk_periodStart,
            "calBulk_periodEnd" => $calBulk_periodEnd,
            'gtabid' => $gtabid,
            'calBulk_schmeaDay' => $calBulk_schmeaDay,
            'calBulk_termStaH' => $calBulk_termStaH,
            "calBulk_termEndH" => $calBulk_termEndH,
            "calBulk_termLenD" => $calBulk_termLenD,
            "calBulk_termStaM" => $calBulk_termStaM,
            "calBulk_termEndM" => $calBulk_termEndM,
            'calBulk_precalc' => $calBulk_precalc,
        ] = $request;

        require_once(COREPATH . 'gtab/gtab.lib');

        if (!$LINK[3]) {
            lmb_alert('permission denied!');
            return null;
        }

        # adding bulk dates
        $calBulk_periodStart = dateToStamp($calBulk_periodStart);
        $calBulk_periodEnd = dateToStamp($calBulk_periodEnd);

        # validate dates
        if (!$calBulk_periodStart or !$calBulk_periodEnd) {
            lmb_alert('kein valider Zeitraum angegeben');
            return null;
        }
        if ($calBulk_periodStart > $calBulk_periodEnd) {
            lmb_alert('Startdatum nach Enddatum!');
            return null;
        }

        $insStart = array();
        $insEnd = array();

        # day after day
        for ($t = $calBulk_periodStart; $t <= $calBulk_periodEnd; $t += 86400) {

            $daynr = date('N', $t);
            # check if weekday is selected
            if ($calBulk_schmeaDay[$daynr]) {

                # list of dates
                foreach ($calBulk_termStaH as $termKey => $termValue) {

                    # validate dates
                    if (!$calBulk_termStaH[$termKey] or (!$calBulk_termEndH[$termKey] and !$calBulk_termLenD[$termKey])) {
                        lmb_alert($calBulk_termStaH[$termKey] . ':' . $calBulk_termStaM[$termKey] . ' - Termin nicht vollständig!');
                        continue;
                    }
                    if ($calBulk_termLenD[$termKey] and !is_numeric($calBulk_termLenD[$termKey])) {
                        lmb_alert($calBulk_termLenD[$termKey] . ' - Dauer ist ungültig!');
                        return null;
                    }
                    if (!$calBulk_termStaM[$termKey]) {
                        $calBulk_termStaM[$termKey] = '00';
                    }
                    if (!$calBulk_termEndM[$termKey]) {
                        $calBulk_termEndM[$termKey] = '00';
                    }

                    # begin & end
                    if ($calBulk_termStaH[$termKey] and $calBulk_termEndH[$termKey] and !$calBulk_termLenD[$termKey]) {
                        if (!$tStart = stampToDate(mktime($calBulk_termStaH[$termKey], $calBulk_termStaM[$termKey], null, date('m', $t), date('d', $t), date('Y', $t)), 4)) {
                            lmb_alert('termin ist ungültig!');
                            continue;
                        }
                        if (!$tEnd = stampToDate(mktime($calBulk_termEndH[$termKey], $calBulk_termEndM[$termKey], null, date('m', $t), date('d', $t), date('Y', $t)), 4)) {
                            lmb_alert('termin ist ungültig!');
                            return null;
                        }
                        $insStart[] = $tStart;
                        $insEnd[] = $tEnd;
                        # begin & length
                    } elseif ($calBulk_termStaH[$termKey] and !$calBulk_termEndH[$termKey] and $calBulk_termLenD[$termKey]) {
                        if (!$tStart = stampToDate(mktime($calBulk_termStaH[$termKey], $calBulk_termStaM[$termKey], null, date('m', $t), date('d', $t), date('Y', $t)), 4)) {
                            lmb_alert('termin ist ungültig!');
                            continue;
                        }
                        if (!$tEnd = stampToDate(mktime($calBulk_termStaH[$termKey], $calBulk_termStaM[$termKey] + $calBulk_termLenD[$termKey], null, date('m', $t), date('d', $t), date('Y', $t)), 4)) {
                            lmb_alert('termin ist ungültig!');
                            continue;
                        }
                        $insStart[] = $tStart;
                        $insEnd[] = $tEnd;
                        # begin & end & length / fill period with dates
                    } elseif ($calBulk_termStaH[$termKey] and $calBulk_termEndH[$termKey] and $calBulk_termLenD[$termKey]) {
                        $tlStart = mktime($calBulk_termStaH[$termKey], $calBulk_termStaM[$termKey], null, date('m', $t), date('d', $t), date('Y', $t));
                        $tlEnd = mktime($calBulk_termEndH[$termKey], $calBulk_termEndM[$termKey], null, date('m', $t), date('d', $t), date('Y', $t));
                        $tlen = ($calBulk_termLenD[$termKey] * 60);

                        if ($tlStart >= $tlEnd) {
                            lmb_alert($calBulk_termLenD[$termKey] . ' - Termin ist ungültig!');
                            continue;
                        }

                        for ($tl = $tlStart; $tl < $tlEnd; $tl += $tlen) {
                            $tStart = stampToDate($tl, 4);
                            if ($tl + $tlen > $tlEnd) {
                                $tEnd = stampToDate($tlEnd, 4);
                            } else {
                                $tEnd = stampToDate($tl + $tlen, 4);
                            }

                            $insStart[] = $tStart;
                            $insEnd[] = $tEnd;
                        }
                    }
                }
            }
        }


        if (!$insStart) {
            lmb_alert('keine Termine ausgewählt');
            return null;
        }

        # cretate events
        if ($calBulk_precalc == 'create') {
            $this->insertBulkDate($gtabid, $insStart, $insEnd, $request);

            # confirm events
        } else {
            return $this->confirmBulkDate($insStart);
        }

        return null;
    }

    private function insertBulkDate($gtabid, $insStart, $insEnd, &$request): void
    {
        $request["history_fields"] .= ";$gtabid,7,0";
        $request["history_fields"] .= ";$gtabid,8,0";

        foreach ($insStart as $key => $value) {
            $_REQUEST['g_' . $gtabid . '_7'] = $insStart[$key];
            $_REQUEST['g_' . $gtabid . '_8'] = $insEnd[$key];
            $this->historyUpdate($request);
        }
    }

    private function confirmBulkDate($insStart): string
    {
        return "<script>
				if(confirm('Sollen " . lmb_count($insStart) . " Termine angelegt werden?')){
					send_form('','','','','create');
				}
				</script>";
    }

    private function historyUpdate(array $request): void
    {
        dyns_postHistoryFields($request);
    }

    protected function getEvent(array $request): array
    {
        [
            'gtabid' => $gtabid,
            'recurrent' => $recurrent,
        ] = $request;

        $gtabid = intval($gtabid);

        $tresult = []; // todo this probably doesnt work with extensions, check this

        $gresult = $this->getEventGSR($request);
        $bzm = lmb_count($tresult);

        if ($gresult[$gtabid]["id"]) {
            foreach ($gresult[$gtabid]["id"] as $key => $value) {

                $stst = get_stamp(lmb_substr($gresult[$gtabid][$this->lmbCalFieldsID['STARTSTAMP']][$key], 0, 17) . "00");
                $endst = get_stamp(lmb_substr($gresult[$gtabid][$this->lmbCalFieldsID['ENDSTAMP']][$key], 0, 17) . "00");
                if ($stst and $endst and $endst >= $stst) {

                    $this->getEventBasic($tresult, $gresult, $gtabid, $key, $bzm, $stst, $endst, $recurrent);

                    $bzm++;
                }

            }
        }

        return $tresult;
    }

    protected function getEventGSR(array $request)
    {
        global $gtab;
        global $gfield;
        global $umgvar;

        [
            'gtabid' => $gtabid,
            'start' => $start,
            'end' => $end,
            #'viewtype' => $viewtype,
            'verkn' => $verkn,
            'gs' => $gsr,
            'recurrent' => $recurrent,
        ] = $request;

        $startstamp = strtotime($start);
        $endstamp = strtotime($end);

        $fieldlist[$gtabid] = $gfield[$gtabid]["key"];

        # Zeige Heute
        if (!$startstamp) {
            $startstamp = (local_stamp(1) - 432000);
            $endstamp = (local_stamp(1) + 1209600);
        }

        $tab = $gtab['table'][$gtabid];

        $extension['where'][0] = "
			(
			($tab." . $this->lmbCalFields['STARTSTAMP'] . " >= '" . convert_stamp($startstamp, 0) . "' AND $tab." . $this->lmbCalFields['STARTSTAMP'] . " <= '" . convert_stamp($endstamp, 0) . "') OR
			($tab." . $this->lmbCalFields['ENDSTAMP'] . " >= '" . convert_stamp($startstamp, 0) . "' AND $tab." . $this->lmbCalFields['ENDSTAMP'] . " <= '" . convert_stamp($endstamp, 0) . "') OR
			($tab." . $this->lmbCalFields['STARTSTAMP'] . " < '" . convert_stamp($startstamp, 0) . "' AND $tab." . $this->lmbCalFields['ENDSTAMP'] . " > '" . convert_stamp($endstamp, 0) . "')
			)
			";

        # use calendar_repetition
        if ($umgvar['calendar_repetition']) {
            $extension['where'][0] = "((" . $extension['where'][0] . " AND $tab." . $this->lmbCalFields['REPETITION'] . " = 0)";
            if ($recurrent) {
                $extension['where'][0] .= " OR ($tab." . $this->lmbCalFields['REPETITION'] . " > 0)";
            }
            $extension['where'][0] .= ")";
        }

        # Query ausführen
        $filter["anzahl"][$gtabid] = 'all';
        $filter["nolimit"][$gtabid] = 1;
        $filter["order"][$gtabid] = "$gtabid&" . $this->lmbCalFieldsID['STARTSTAMP'] . "&ASC";

        # Extension
        if (method_exists($this, 'lmb_getEventGSRExtend')) {
            $this->lmb_getEventGSRExtend($gtabid, $filter, $fieldlist, $extension, $request);
        }

        require_once COREPATH . 'gtab/gtab.lib';
        require_once COREPATH . 'gtab/gtab_type.lib';

        return get_gresult($gtabid, 1, $filter, $gsr, $verkn, $fieldlist, null, $extension);
    }

    protected function getEventBasic(array &$tresult, array &$gresult, int $gtabid, int|string $key, int $bzm, $stst, $endst, $recurrent): void
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

        # row-title from indicator
        #$title = null;
        #if($gresult[$gtabid]["indicator"]["title"][$key]){
        #	$title = implode(", ",$gresult[$gtabid]["indicator"]["title"][$key]);
        #}

        # show indicator
        if ($gresult[$gtabid]["indicator"]["object"][$key]) {
            $preobject = array();
            foreach ($gresult[$gtabid]["indicator"]["object"][$key] as $ikey => $ival) {
                $preobject[] = $ival;
            }
        }
        
        $id = intval($gresult[$gtabid]['id'][$key]);
        $calendarEvent = new CalendarEvent($id);

        $calendarEvent->title = lmb_substr($gresult[$gtabid][$this->lmbCalFieldsID['SUBJECT']][$key], 0, $umgvar['memolength']);
        $calendarEvent->allDay = (bool) $gresult[$gtabid][$this->lmbCalFieldsID['ALLDAY']][$key];

        $calendarEvent->start = new DateTime('@' .$stst);
        if ($calendarEvent->allDay) {
            $calendarEvent->end = new DateTime('@' .intval($endst ) + 86400);
        } else {
            $calendarEvent->end = new DateTime('@' .$endst);
        }
        
        $calendarEvent->editable = $this->getFieldPermissions($id, [1], $gtabid);
        $calendarEvent->startEditable = $this->getFieldPermissions($id, [2, 5], $gtabid);
        $calendarEvent->durationEditable = $calendarEvent->startEditable;
        $calendarEvent->resourceEditable = $calendarEvent->startEditable;

        $calendarEvent->classNames = [
            'event_' . $gresult[$gtabid]["id"][$key]
        ];

         
        if ($color) {
            $calendarEvent->backgroundColor = $color;
            $calendarEvent->borderColor = $color;
        }
        if ($text_color) {
            $calendarEvent->textColor = $text_color;
        }
        if ($preobject) {
            $calendarEvent->setExtendedProp('imageurl',implode('', $preobject));
        }
        #$tresult[$bzm]["evtBeforeEvent"] = "<i class='lmb-icon lmb-page-find'></i>";
        #$tresult[$bzm]["evtAfterEvent"] = "<i class='lmb-icon lmb-page-find'></i>";
        if ($preobject) {
            $calendarEvent->setExtendedProp('evtBeforeEvent', implode('', $preobject));
        }
        /*
        $tresult[$bzm]["evtTextColor"] = "blue";
        $tresult[$bzm]["evtColor"] = "red";
        $tresult[$bzm]["evtborderColor"] = "green";
        $tresult[$bzm]["evtBackgroundColor"] = "yellow";
        $tresult[$bzm]["evtTitle"] = "hallo";
        */

        #editable
        if ($arg_result) {
            $calendarEvent->setExtendedProp('symbols',implode(',', $arg_result));
        }
        # allday
        /*
        if($endst-$stst > 86400){
            $tresult[$bzm]["allDay"]= true;
        }
        */
        # recuring event
        if ($gresult[$gtabid][$this->lmbCalFieldsID['REPETITION']][$key] and $recurrent) {
            $calendarEvent->setExtendedProp('repeat', intval($gresult[$gtabid][$this->lmbCalFieldsID['REPETITION']][$key]));
            if ($gresult[$gtabid][$this->lmbCalFieldsID['REPEATUNTIL']][$key]) {
                $calendarEvent->setExtendedProp('repeatEnd',get_format_date($gresult[$gtabid][$this->lmbCalFieldsID['REPEATUNTIL']][$key], 'Y-m-d H:i:s'));
            }
        }
        
        $tresult[$bzm] = $calendarEvent;
        
        if (method_exists($this, 'lmb_getEventExtend')) {
            $this->lmb_getEventExtend($tresult, $gresult, $gtabid, $key, $bzm, $stst, $endst);
        }
    }

    private function reloadEvents(array $request): array
    {
        return $this->getEvent($request);
    }

    private function getFieldPermissions($ID, array $fieldIds, int $tabId): bool
    {
        global $gfield;

        $editable = true;

        foreach ($fieldIds as $fieldId) {
            # ----------- Edit Permission -----------
            if (!$gfield[$tabId]['perm_edit'][$fieldId]) {
                $editable = false;
                break;
            }
            # ----------- Editrule -----------
            if ($gfield[$tabId]['editrule'][$fieldId]) {
                $editable = !check_GtabRules($ID, $tabId, $fieldId, $gfield[$tabId]['editrule'][$fieldId]);
                if (!$editable) {
                    break;
                }
            }
        }

        return $editable;
    }
}
