<?php

namespace Limbas\extra\reminder;

use DateTime;
use Exception;
use Limbas\Controllers\UserGroupController;
use Limbas\lib\LimbasController;

class ReminderController extends LimbasController
{

    /**
     * @throws Exception
     */
    public function handleRequest(array $request): array
    {
        return match ($request['action']) {
            'save' => $this->saveReminder($request),
            'delete' => $this->deleteReminder($request),
            'update' => $this->updateReminder($request),
            'get' => $this->getReminder($request),
            'show' => $this->showReminder($request),
            'preview' => $this->previewReminder($request),
            default => ['success' => false],
        };
    }

    /**
     * Save reminder
     * @throws Exception
     */
    private function saveReminder(array $request): array
    {
        [
            'reminderId' => $reminderId,
            'tableId' => $tableId,
            'dataId' => $dataId,
            'deadline' => $deadline,
            'description' => $description,
            'category' => $category,
            'userOrGroupArray' => $userOrGroupArray,
            'reminderRows' => $reminderRows,
            'verkn_ID' => $verkn_ID,
            'verkn_tabid' => $verkn_tabid,
            'verkn_fieldid' => $verkn_fieldid,
            'verkn_showonly' => $verkn_showonly,
            'form_id' => $form_id,
        ] = $request;

        if (!$category) {
            $category = 0;
        }

        if (!$form_id) {
            $form_id = 0;
        }

        if (is_string($deadline)) {
            $deadline = new DateTime($deadline);
        }

        if (!$reminderId) {
            $reminderId = 0;
        }

        if (!is_array($userOrGroupArray)) {
            $userOrGroupArray = [];
        }

        if ($reminderRows == 'all') {
            require_once(COREPATH . 'gtab/gtab.lib');
            $verkn = null;
            if ($verkn_ID) {
                $verkn = set_verknpf($verkn_tabid, $verkn_fieldid, $verkn_ID, null, null, $verkn_showonly, 1);
            }
            if ($gresult = get_gresult($tableId, 1, $GLOBALS["filter"], $GLOBALS["gsr"], $verkn, 'ID')) {
                $reminderRows = $gresult[$tableId]['id'];
            }
        }

        if (!is_array($reminderRows)) {
            $reminderRows = [$dataId];
        }

        $success = false;
        foreach ($reminderRows as $rowDataId) {
            $reminder = new Reminder($reminderId, $tableId, $rowDataId, $deadline, $description, category: $category, formId: $form_id);
            $success = $reminder->save($userOrGroupArray);
        }

        $multiframeUpdateId = '';
        if ($success) {
            $multiframeUpdateId = $this->getMultiframeUpdateId('' . $category);
        }

        return ['success' => $success, 'multiframeUpdateId' => $multiframeUpdateId];
    }

    /**
     * Gets the multiframeId for a given category, right sidebar
     * @param string $category
     * @return mixed
     */
    private function getMultiframeUpdateId(string $category = '0')
    {
        global $multiframeCount;
        if (is_array($multiframeCount)) {
            foreach ($multiframeCount['id'] as $mfk => $mfkID) {
                if ($multiframeCount['type'][$mfk] == 'reminder' && $multiframeCount['value'][$mfk] == $category) {
                    $limbasMultiframeItem = $mfkID;
                }
                if ($multiframeCount['type'][$mfk] == 'dreminder' && $category == '0') {
                    $limbasMultiframeItem = $mfkID;
                }
            }
        }

        return $limbasMultiframeItem;
    }

    /**
     * Delete reminder with reminderId
     * @param array $request
     * @return array|false[]
     */
    private function deleteReminder(array $request): array
    {
        [
            'reminderId' => $reminderId,
        ] = $request;

        $reminder = Reminder::get($reminderId);
        if (!$reminder) {
            return ['success' => false];
        }
        $success = $reminder->delete();

        $multiframeUpdateId = '';
        if ($success) {
            $multiframeUpdateId = $this->getMultiframeUpdateId('' . $reminder->category);
        }

        return ['success' => $success, 'multiframeUpdateId' => $multiframeUpdateId, 'category' => $reminder->category];
    }

    private function updateReminder(array $request): array
    {
        [
            'changeId' => $changeId,
            'deadline' => $deadline,
            'description' => $description,
        ] = $request;

        $reminder = Reminder::get($changeId);
        if (!$reminder instanceof Reminder) {
            return ['success' => false];
        }
        $reminder->deadline = $deadline;
        $reminder->description = $description;
        $success = $reminder->save();

        return ['success' => $success];
    }

    private function getReminder(array $request): array
    {
        [
            'reminderId' => $reminderId,
        ] = $request;

        $reminder = Reminder::get($reminderId);

        if (!$reminder) {
            return ['success' => false];
        }

        return [''];
    }

    /**
     * Show reminder
     * @param array $request
     * @return array
     */
    public function showReminder(array $request): array
    {
        global $lang;

        require_once(COREPATH . 'gtab/gtab.lib');

        [
            'tableId' => $tableId, # gtabid
            'dataId' => $dataId, # ID => row ID
            'reminderId' => $reminderId,

            # probably unused / not needed
            'category' => $category,
            'listmode' => $listmode,
            'defaults' => $defaultsJson,
        ] = $request;

        $defaults = $defaultsJson ? json_decode($defaultsJson, 1) : array();

        $reminder = null;
        if ($reminderId != 0) {
            $reminder = Reminder::get($reminderId) ?: null;
        }

        $componentDateTimeSelect = $this->componentDateTimeSelect($defaults['datetime'], $reminder, $lang);

        $componentUserGroupSelection = $this->componentUserGroupSelection($defaults['usergroups'], $reminder);

        $componentDescriptionInput = $this->componentDescriptionInput($defaults['remark'], $reminder);

        $componentMailCheck = $this->componentMailCheck($defaults['mail'], $reminder);

        $componentCategorySelection = $this->componentCategorySelection($defaults['category'], $reminder, $tableId, $category);

        $componentExistingReminders = $this->componentExistingReminders($listmode, $tableId, $dataId, $reminder, $defaults['hidecurrent']);

        $putSaveButtonInFooter = $componentExistingReminders == '';

        $componentSaveButton = $putSaveButtonInFooter ? '' : $this->componentSaveButtonRow($reminderId);

        $body = <<<HTML
            <link href="assets/vendor/select2/select2.min.css" rel="stylesheet">
            <form id="form_reminder" name="form_reminder" onclick="limbasDivClose('lmbAjaxContainer')">
                <div class="container">
                    $componentDateTimeSelect
                    $componentUserGroupSelection
                    $componentDescriptionInput
                    $componentMailCheck
                    $componentCategorySelection
                    $componentSaveButton
                    $componentExistingReminders
                </div>
            </form>
        HTML;

        $saveButton = $this->componentSaveButton($reminderId);

        $footer = $putSaveButtonInFooter ? $saveButton : '';

        if ($reminder) {
            $footer .= <<<HTML
                <button type="button" class="btn btn-dark" id="btn-reminder-createmodal">$lang[3086]</button>
            HTML;
        } else {
            $closeMessage = ucfirst($lang[844]);
            $footer .= <<<HTML
                <button type="button" class="btn btn-dark" data-bs-dismiss="modal">$closeMessage</button>
            HTML;
        }

        return ['body' => $body, 'footer' => $footer, 'dataId' => $dataId ?: '', 'tableId' => $tableId ?: ''];
    }

    /**
     * todo, currently uses deprecated function, needs to be updated
     * @param array $request
     * @return string[]
     */
    private function previewReminder(array $request): array
    {
        require_once COREPATH . 'extra/reminder/reminder.lib';

        # todo array unpacking is not needed
        [
            'dropitem' => $dropitem,
            'category' => $category,
            'gtabid' => $gtabid,
            'id' => $id,
        ] = $request;

        ob_start();
        self::previewReminderLegacy(
            compact('dropitem', 'category', 'gtabid', 'id')
        );
        return ['html' => ob_get_clean() ?: ''];
    }

    /**
     * Preview reminder for navbar on the right, is considered Legacy as it should be called correctly from handleRequest
     * todo refactor this
     * @param array $params
     * @return bool|void
     */
    public static function previewReminderLegacy(array $params)
    {
        global $session;
        global $gtab;
        global $lang;
        global $userdat;
        global $greminder;

        require_once(COREPATH . 'gtab/gtab.lib');

        $dropitem = $params["dropitem"];
        $category = $params["category"];
        $gtabid = $params["gtabid"];
        $elid = $params["id"];

        if ($dropitem) {
            $reminder = Reminder::get($dropitem);
            if (!$reminder) {
                return false;
            }
            $reminder->delete();
        }

        #if (!$greminder["argresult_id"][$category]) {
        #    $gtabid = 0;
        #}

        $gresult = Reminder::allLegacy(category: (int) $category, valid: 1);

        if ($category) {
            $gfrist = $category;
        } else {
            $gfrist = 'true';
        }

        $currentTabId = 0;

        echo "<span>";
        if ($gresult["id"]) {
            echo "<TABLE width='100%'>";
            foreach ($gresult["id"] as $key => $value) {

                // set formular
                $form_id = '';
                if ($greminder[$gtabid]["formd_id"][$category] == -1 and $gresult["form_id"][$key]) {
                    $form_id = $gresult["form_id"][$key];
                } elseif ($greminder[$gtabid]["formd_id"][$category] >= 1) {
                    $form_id = $greminder[$gtabid]["formd_id"][$category];
                }

                if ($category and $gtabid) {
                    $gresult["tab_id"][$key] = $gtabid;
                } elseif ($currentTabId != $gresult["tab_id"][$key]) {
                    echo "<TR><TD colspan=\"2\">";
                    print_r($gtab[1]);
                    echo "<I><A TARGET=\"main\" HREF=\"main.php?&action=gtab_erg&source=root&gtabid=" . $gresult["tab_id"][$key] . "&gfrist=$gfrist\">" . $gtab["desc"][$gresult["tab_id"][$key]] . "</A></I>";
                    echo "</TD></TR>";
                    $currentTabId = $gresult["tab_id"][$key];
                }

                $validdate = lmb_substr(get_date($gresult["validdate"][$key], 0), 0, 16);

                echo "<TR><TD class=\"pb-2\" style=\"padding-left:20px\" title=\"" . $gresult["content"][$key] . ($gresult["desc"][$key] ? ' - ' . strip_tags($gresult["desc"][$key]) : '') . "\">";

                echo "<A TARGET=\"main\" HREF=\"main.php?&action=gtab_change&gtabid=" . $gresult["tab_id"][$key] . "&ID=" . $gresult["dat_id"][$key] . "&gfrist=$gfrist&form_id=" . $form_id . "&wfl_id=" . $gresult["LMBREM_WFLID"][$key] . "\" style=\"color:green\">";

                echo $validdate . "</A>";

                if ($gresult["fromuser"][$key] and $gresult["fromuser"][$key] != $session["user_id"]) {
                    echo "<i class=\"lmb-icon lmb-tag\" title=\"" . $userdat["bezeichnung"][$gresult["fromuser"][$key]] . "\"></i>";
                }

                if ($gresult["desc"][$key]) {
                    echo "<i style=\"color:grey\">" . lmb_substr(strip_tags($gresult["desc"][$key]), 0, 25) . "</i>";
                } elseif ($gresult["content"][$key]) {
                    echo "<span style=\"color:grey\">" . lmb_substr(strip_tags($gresult["content"][$key]), 0, 25) . "</span>";
                }

                echo "</TD>";

                if (!$gresult["wfl_inst"][$key]) {
                    echo "<TD VALIGN=\"TOP\" STYLE=\"width:10%;overflow:hidden\"><i class=\"lmb-icon lmb-close-alt\" style=\"cursor:pointer\" onclick=\"limbasMultiframePreview($elid,'Reminder',null,'" . $gresult["id"][$key] . "'," . $gresult["tab_id"][$key] . ",'category=$category')\"></i></TD>";
                }

                echo "</TD></TR>";
            }
            echo "</TABLE>";
            $count['count'][$elid] = count($gresult["id"]);
        } else {
            $count['count'][$elid] = 0;
            echo '&nbsp;' . $lang[98]; # Keine Datensätze vorhanden!
        }

        echo "</span>";
        echo '<script>';

        $count['info'][$elid] = $greminder[$greminder["argresult_id"][$category]]["notification"][$category];
        echo "limbasMultiframeCountPost('" . json_encode($count) . "');";

        echo '</script>';
    }

    /**
     * @param $datetime
     * @param Reminder|null $reminder
     * @param array $lang
     * @return string
     */
    private function componentDateTimeSelect($datetime, ?Reminder $reminder, array $lang): string
    {
        if ($datetime) {
            return "<INPUT TYPE=\"hidden\" NAME=\"REMINDER_DATE_TIME\" VALUE=\"{$datetime}\" MAX=16>";
        }

        $dateValue = $reminder ? $reminder->deadline : new DateTime();
        $dateValue = $dateValue->format('Y-m-d\TH:i');

        return <<<HTML
            <div class="row my-2">
                    <div class="col-2"><span class="me-1 d-flex align-items-center">$lang[1975]</span></div>
                    <div class="col-1 pe-0"><input class="form-control form-control-sm" type="text" id="input-reminder-deadline-from-now" name="reminder-deadline-from-now" value="0"></div>
                    <div class="col-4 ps-0 pe-1"><select class="form-select form-select-sm" id="select-reminder-unit" name="select-reminder-unit">
                        <option value="day">$lang[1982]</option>
                        <option value="min">$lang[1980]</option>
                        <option value="hour">$lang[1981]</option>
                        <option value="week">$lang[1983]</option>
                        <option value="month">$lang[296]</option>
                        <option value="year">$lang[1985]</option>
                    </select></div>
                    <div class="col-5 ps-1"><input
                      type="datetime-local"
                      id="input-reminder-deadline"
                      name="reminder-deadline"
                      class="form-control form-control-sm col-5"
                      value="$dateValue"
                      /></div>
                
            </div>
        HTML;
    }

    /**
     * @param $defaultUsergroups
     * @param Reminder|null $reminder
     * @return string
     */
    private function componentUserGroupSelection($defaultUsergroups, ?Reminder $reminder): string
    {
        global $lang;
        global $farbschema;

        if (!is_null($defaultUsergroups)) {
            return <<<HTML
                <div style="display:none;">
                    <input type="hidden" id="REMINDER_USERGROUP" name="REMINDER_USERGROUP" value="{$defaultUsergroups}">
                </div>
            HTML;
        }

        if ($reminder) {
            return '';
        }

        $userGroupController = new UserGroupController();
        $users = $userGroupController->getUsers()['data'];
        $groups = $userGroupController->getGroups()['data'];

        $htmlUserOptions = '';
        foreach ($users as $user) {
            [
                'id' => $userId,
                'name' => $userName,
            ] = $user;

            $htmlUserOptions .= <<<HTML
                <option value="$userId">$userName</option>
            HTML;
        }

        $htmlGroupOptions = '';
        foreach ($groups as $group) {
            [
                'id' => $groupId,
                'name' => $groupName,
            ] = $group;

            $htmlGroupOptions .= <<<HTML
                <option value="$groupId">$groupName</option>
            HTML;
        }

        return <<<HTML
            <div class="row my-1">
                <div class="col-2"><span>$lang[2643]:</span></div>
                <div class="col-10">
                    <input type="hidden" ID="REMINDER_USERGROUP" NAME="REMINDER_USERGROUP">
                    <div class="fgtabchange d-flex">
                        <div class="d-flex flex-column w-100 pe-1">
                            <div class="d-flex align-items-center input-group">
                                <select name="reminderUsers" id="select-reminder-user" multiple="multiple" class="form-control form-control-sm w-100">
                                    $htmlUserOptions                            
                                </select>
                            </div>
                            <div id="contWvUGList" class="" style="background-color:{$farbschema["WEB8"]}"></div>
                        </div>
                        <div class="d-flex flex-column w-100 ps-1">
                            <div class="d-flex align-items-center input-group">
                                <select name="reminderGroups" id="select-reminder-group" multiple="multiple" class="form-control form-control-sm w-100">
                                    $htmlGroupOptions                            
                                </select>
                            </div>
                            <div id="contWvUGListGroups" class="" style="background-color:{$farbschema["WEB8"]}"></div>
                        </div>
                    </div>
                    <div 
                        id="lmb_reminderAddUserGroupuser" 
                        class="ajax_container border" 
                        style="display:none;position:absolute;padding:2px;background-color:{$farbschema["WEB11"]}"
                        >
                    </div>
                    <div 
                        id="lmb_reminderAddUserGroupgroup" 
                        class="ajax_container border" 
                        style="text-align:left;display:none;position:absolute;padding:2px;background-color:{$farbschema["WEB11"]}"
                        >
                    </div>
                </div>
            </div>
        HTML;
    }

    /**
     * @param $defaultRemark
     * @param Reminder|null $reminder
     * @return string
     */
    private function componentDescriptionInput($defaultRemark, ?Reminder $reminder): string
    {
        global $lang;

        if (!is_null($defaultRemark)) {
            return "<div class=\"row d-none\"><textarea class=\"col\" name=\"REMINDER_BEMERKUNG\">{$defaultRemark}</textarea></div>";
        }

        $changedesc = $reminder ? $reminder->description : "";
        return <<<HTML
            <div class="row my-1">
                <div class="col-2"><span>{$lang[295]}:</span></div>
                <div class="col-10"><textarea id="input-reminder-description" name="REMINDER_BEMERKUNG" class="form-control form-control-sm col-10">$changedesc</textarea></div>
            </div>
        HTML;
    }

    /**
     * @param $mail
     * @param Reminder|null $reminder
     * @return string
     */
    private function componentMailCheck($mail, ?Reminder $reminder): string
    {
        global $lang;

        if (!is_null($mail)) {
            return "<div class='row d-none'><div class='col'><input type=\"hidden\" name=\"REMINDER_MAIL\" value=\"{$mail}\"></div></div>";
        }

        if ($reminder) {
            return '';
        }

        return <<<HTML
            <div class="row my-1" title="$lang[2577]">
                <div class="col-2">$lang[521]:</div>   
                <div class="col-10">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="REMINDER_MAIL" id="input-reminder-mail">
                    </div>
                </div>
            </div>
        HTML;
    }

    /**
     * @param $defaultCategory
     * @param Reminder|null $reminder
     * @param mixed $tableId
     * @param mixed $category
     * @return string
     */
    private function componentCategorySelection($defaultCategory, ?Reminder $reminder, mixed $tableId, mixed $category): string
    {
        global $greminder;
        global $lang;

        if (!is_null($defaultCategory)) {
            return "<div class=\"row\"><div class=\"col\"><input type=\"hidden\" name=\"reminder_category\" value=\"{$defaultCategory}\"></div></div>";
        }

        if ($reminder) {
            return "<div class=\"row\"><div class=\"col\"><input type=\"hidden\" name=\"reminder_category\" value=\"{$reminder->category}\" id=\"input-reminder-category-hidden\"></div></div>";
        }

        $isAlreadyChecked = false;

        // all table based reminder
        $tableBasedReminders = '';
        if ($greminder[$tableId]['name']) {
            foreach ($greminder[$tableId]['name'] as $categoryId => $categoryName) {
                $checked = '';
                if (($category == $categoryId or ($greminder[$tableId]['default'][$categoryId] and !$category)) and !$isAlreadyChecked) {
                    $checked = 'checked';
                    $isAlreadyChecked = true;
                }

                $tableBasedReminders .= <<<HTML
                    <div class="form-check">
                      <input class="form-check-input" type="radio" name="reminder_category" id="input-reminder-category-$categoryId" value="$categoryId" $checked>
                      <label class="form-check-label" for="input-reminder-category-$categoryId">
                        $categoryName
                      </label>
                    </div>
                HTML;
            }
        }

        // all independent reminder
        $independentReminders = '';
        foreach ($greminder[0]['name'] as $categoryId => $categoryName) {
            $checked = '';
            if (($category == $categoryId or ($greminder[0]['default'][$categoryId] and !$category)) and !$isAlreadyChecked) {
                $checked = 'checked';
                $isAlreadyChecked = true;
            }

            $independentReminders .= <<<HTML
                <div class="form-check">
                  <input class="form-check-input" type="radio" name="reminder_category" id="input-reminder-category-$categoryId" value="$categoryId" $checked>
                  <label class="form-check-label" for="input-reminder-category-$categoryId">
                    $categoryName
                  </label>
                </div>
            HTML;
        }

        // default reminder
        $checked = $isAlreadyChecked ? '' : 'checked';
        $defaultReminder = <<<HTML
            <div class="form-check">
              <input class="form-check-input" type="radio" name="reminder_category" id="input-reminder-category-0" value="0" $checked>
              <label class="form-check-label" for="input-reminder-category-0">
                <b>{$lang[1219]}</b>
              </label>
            </div>
        HTML;

        if (!$tableBasedReminders && !$independentReminders) {
            return '';
        }

        return <<<HTML
            <div class="row my-1">
                <div class="col-2">Kategorie:</div>
                <div class="col">
                    $defaultReminder
                    $tableBasedReminders
                    $independentReminders
                </div>
            </div>
        HTML;
    }

    private function componentSaveButtonRow($reminderId): string
    {
        $saveButton = $this->componentSaveButton($reminderId);

        return <<<HTML
            <div class="row my-1">
                <div class="col d-flex justify-content-end">
                    $saveButton
                </div>
            </div>
        HTML;
    }

    private function componentSaveButton($reminderId): string
    {
        global $lang;

        $buttonText = $reminderId != 0 ? $lang[2443] : $lang[1038]; # save / add
        return <<<HTML
            <button 
                class="btn btn-primary me-1" 
                id="btn-save-reminder" 
                type="button" 
                data-reminderId="$reminderId">
                $buttonText
            </button>
        HTML;
    }

    /**
     * @param mixed $listmode
     * @param mixed $tableId
     * @param mixed $dataId
     * @param Reminder|null $reminder
     * @param $defaultHideCurrent
     * @return string
     */
    private function componentExistingReminders(mixed $listmode, mixed $tableId, mixed $dataId, ?Reminder $reminder, $defaultHideCurrent): string
    {
        global $userdat;
        global $lang;
        global $greminder;

        # listmode
        if ($listmode) {
            return '';
        }

        # single mode
        if (!$reminder && !$defaultHideCurrent) {
            $reminders = Reminder::getReminderByFilter(null, $tableId, $dataId);

            # sort reminder by group
            $groups = array();
            foreach ($reminders as $reminder) {
                $category = $reminder->category;

                $reminder_name = $greminder[$greminder["argresult_id"][$category]]["name"][$category];
                if (!$groups[$reminder_name]) {
                    $groups[$reminder_name] = array();
                }
                $groups[$reminder_name][] = $reminder;
            }

            $htmlRows = '';

            # display reminder sorted into groups
            foreach ($groups as $groupname => $reminders) {

                # display group name
                $groupname = $groupname ?: $lang[1219];

                $htmlListItems = '';
                # display reminders
                foreach ($reminders as $reminder) {
                    $reminderId = $reminder->id;
                    $remDescription = $reminder->fromUser ? "<br><i>" . $userdat["bezeichnung"][$reminder->fromUser] . "</i>" : "";
                    $remDate = $reminder->getDeadlineTimezoned();
                    $descriptionFormatted = nl2br(e($reminder->description));
                    $htmlListItems .= <<<HTML
                        <li class="list-group-item px-1 py-1">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="d-flex align-items-start">
                                    <span 
                                        class="me-2 text-primary cursor-pointer text-nowrap" 
                                        data-reminder-changeview="$reminderId">
                                        $remDate
                                    </span>
                                    <span class="m-0 me-2 breakable">$descriptionFormatted</span>
                                    $remDescription
                                </div>
                                <i 
                                    class="fa-solid fa-xmark p-1"
                                    style="cursor:pointer;"
                                    data-reminder-remove="$reminderId"
                                    title="$lang[721]"></i>
                            </div>          
                        </li>
                    HTML;
                }

                $htmlRows .= <<<HTML
                    <div class="card rounded-1 mb-1">
                        <div class="card-header py-1 px-1 d-flex justify-content-between">
                            <b>$groupname</b>
                            
                            <div class="d-flex">
                            <div class="px-1">kompakt</div>
                            <div class="form-check">
                                <input class="form-check-input toggle-breakable" type="checkbox" checked>
                            </div>
                            </div>
                        </div>
                        <ul class="list-group list-group-flush">
                            $htmlListItems
                        </ul>
                    </div>
                HTML;
            }

            if ($htmlRows) {
                return <<<HTML
                    <div class="row" id="div-existing-reminders-separator"><div class="col"><hr></div></div>
                    <div class="row" id="div-existing-reminders-list">
                        <div class="col-2">
                            <b>{$lang[2644]}:</b>
                        </div>
                        <div class="col-10">
                            $htmlRows               
                        </div>
                    </div>
                HTML;
            }
        }

        return '';
    }
}