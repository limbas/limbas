<?php

namespace Limbas\extra\reminder;


use DateTime;
use Limbas\extra\mail\LmbMail;
use Limbas\lib\db\Database;
use Limbas\lib\LimbasModel;

# todo refactor insert, as it potentially adds multiple entries, and should only add one for correct model
class Reminder extends LimbasModel
{
    protected static string $tableName = 'LMB_REMINDER';
    protected static string $tableNameGroup = 'LMB_REMINDER_GROUP';

    public function __construct(
        public int      $id,
        public int      $tableId,
        public ?int     $dataId,
        public DateTime $deadline,
        public ?string  $description,
        public ?int     $fromUser = null,
        public ?string  $content = '',
        public ?int     $category = 0,
        public ?int     $workflowInstance = 0,
        public ?int     $formId = 0,
        public ?int     $multitenantId = 0,
    )
    {
        global $session;
        if ($this->fromUser == null) {
            $this->fromUser = parse_db_int($session["user_id"]);
        }
    }

    public static function get(int $id): LimbasModel|null
    {
        $output = self::all(['ID' => $id]);
        if (empty($output)) {
            return null;
        }

        return $output[0];
    }

    public static function getReminderByFilter(?int $category = null, ?int $tableId = null, ?int $dataId = null, ?int $wfl_inst = null, ?bool $valid = false): array
    {
        if ($valid === null) {
            $valid = false;
        }

        $whereArgs = $wfl_inst && $category ? ['LMB_REMINDER.WFL_INST' => parse_db_int($wfl_inst)] : [];

        return self::all($whereArgs, $category, $tableId, $dataId, $valid);
    }

    public static function all(array $where = [], ?int $category = null, ?int $tableId = null, ?int $dataId = null, bool $valid = false): array
    {
        $db = Database::get();
        global $gtab;

        $whereFields = [];
        foreach ($where as $field => $value) {
            if ($value === null) {
                $whereFields[] = strtoupper($field) . ' IS ' . LMB_DBDEF_NULL;
            } else {
                $whereFields[] = strtoupper($field) . ' = ' . $value;
            }
        }

        $whereFields = array_merge($whereFields, self::getReminderWhereFilter($category, $tableId, $dataId, $valid));
        $whereFields = implode(' AND ', $whereFields);
        if($whereFields){
            $whereFields = 'WHERE '.$whereFields;
        }else{
            return array();
        }

        $sqlquery = <<<SQL
            SELECT DISTINCT
                LMB_REMINDER.ID,
                LMB_REMINDER.FRIST,
                LMB_REMINDER.DESCRIPTION,
                LMB_REMINDER.CONTENT,
                LMB_REMINDER.TAB_ID,
                LMB_REMINDER.DAT_ID,
                LMB_REMINDER.FORM_ID,
                LMB_REMINDER.WFL_INST,
                LMB_REMINDER.CATEGORY,
                LMB_REMINDER.FROMUSER
            FROM LMB_REMINDER,LMB_REMINDER_GROUP
            $whereFields 
            ORDER BY LMB_REMINDER.TAB_ID,LMB_REMINDER.FRIST
        SQL;

        $rs = lmbdb_exec($db, $sqlquery) or errorhandle(lmbdb_errormsg($db), $sqlquery, $GLOBALS['action'], __FILE__, __LINE__);

        $output = [];

        while (lmbdb_fetch_row($rs)) {
            $deadline = new DateTime();
            $deadline->setTimestamp(get_stamp(lmbdb_result($rs, 'FRIST')));
            $remtab = lmbdb_result($rs, "TAB_ID");

            // permission on table
            if(!$gtab['tab_id'][$remtab]){
                continue;
            }

            $output[] = new self(
                intval(lmbdb_result($rs, 'ID')),
                intval($remtab),
                intval(lmbdb_result($rs, 'DAT_ID')),
                $deadline,
                lmbdb_result($rs, 'DESCRIPTION'),
                intval(lmbdb_result($rs, 'FROMUSER')) ?: null,
                lmbdb_result($rs, 'CONTENT') ?: '',
                intval(lmbdb_result($rs, 'CATEGORY')) ?: 0,
                intval(lmbdb_result($rs, 'WFL_INST')) ?: 0,
                intval(lmbdb_result($rs, 'FORM_ID')) ?: 0,
                intval(lmbdb_result($rs, 'LMB_MID')) ?: 0,
            );
        }

        return $output;
    }

    public static function allLegacy(?int $category = null, ?int $tableId = null, ?int $dataId = null, ?int $wfl_inst = null, ?bool $valid = false): array {
        if ($valid === null) {
            $valid = false;
        }

        $whereArgs = $wfl_inst && $category ? ['LMB_REMINDER.WFL_INST' => parse_db_int($wfl_inst)] : [];

        $reminderObjects = self::all($whereArgs, $category, $tableId, $dataId, $valid);


        $reminderArrays = [
            "id" => [],
            "validdate" => [],
            "desc" => [],
            "content" => [],
            "tab_id" => [],
            "dat_id" => [],
            "form_id" => [],
            "wfl_inst" => [],
            "category" => [],
            "fromuser" => [],
        ];

        foreach ($reminderObjects as $reminderObject) {
            $id = $reminderObject->id;
            $reminderArrays["id"][$id] = $id;
            $reminderArrays["validdate"][$id] = $reminderObject->getDeadlineTimezoned();
            $reminderArrays["desc"][$id] = $reminderObject->description;
            $reminderArrays["content"][$id] = $reminderObject->content;
            $reminderArrays["tab_id"][$id] = $reminderObject->tableId;
            $reminderArrays["dat_id"][$id] = $reminderObject->dataId;
            $reminderArrays["form_id"][$id] = $reminderObject->formId;
            $reminderArrays["wfl_inst"][$id] = $reminderObject->workflowInstance;
            $reminderArrays["category"][$id] = $reminderObject->category;
            $reminderArrays["fromuser"][$id] = $reminderObject->fromUser;
        }

        return $reminderArrays;
    }

    public function toArray(): array {
        return get_object_vars($this);
    }

    public function toArrayLegacy(): array {
        return [
            'id' => $this->id,
            'tab_id' => $this->tableId,
            'dat_id' => $this->dataId,
            'validdate' => $this->getDeadlineTimezoned(),
            'desc' => $this->description,
            'fromuser' => $this->fromUser,
            'content' => $this->content,
            'category' => $this->category,
            'wfl_inst' => $this->workflowInstance,
            'form_id' => $this->formId,
        ];
    }

    public function save(?array $userOrGroupArray = [], ?bool $sendMail = false): bool
    {
        return match ($this->id) {
            0 => $this->insert($userOrGroupArray, (bool)$sendMail),
            default => $this->update(),
        };
    }

    private function update(): bool
    {
        # updateable fields, maybe change this
        $data = [
            'tab_id' => $this->tableId,
            'dat_id' => $this->dataId,
            'frist' => $this->preparedDeadline(),
            'description' => $this->description,
            'content' => $this->content,
            'category' => $this->category,
            'wfl_inst' => $this->workflowInstance,
            'form_id' => $this->formId,
        ];

        return Database::update(self::$tableName, $data, ['ID' => $this->id]);
    }

    public static function convertLegacyUsergroupArray(mixed $userOrGroupArray): array
    {
        if (is_array($userOrGroupArray)) {
            $touser = $userOrGroupArray;
        } elseif ($userOrGroupArray) {
            $touser = explode(";", $userOrGroupArray);
        } else {
            $touser = [];
        }

        $newUserOrGroupArray = [];

        foreach ($touser as $to) {
            if ($to) {
                $to = explode("_", $to);
                if ($to[1] == "u" and is_numeric($to[0])) {
                    $newUserOrGroupArray[] = ['type' => 'user', 'id' => $to[0]];
                } elseif ($to[1] == "g" and is_numeric($to[0])) {
                    $newUserOrGroupArray[] = ['type' => 'group', 'id' => $to[0]];
                }
            }
        }

        return $newUserOrGroupArray;
    }

    private function preparedDeadline(): string
    {
        return convert_stamp($this->deadline->getTimestamp());
    }

    public function getDeadlineTimezoned(): string
    {
        return stampToDate($this->deadline->getTimestamp(), 0);
    }

    private function insert(?array $userOrGroupArray = [], bool $sendMail = false): bool
    {
        global $gfield;
        global $userdat;
        global $greminder;
        global $umgvar;
        global $lmmultitenants;
        global $session;
        global $gtab;
        global $lang;

        require_once COREPATH . 'gtab/gtab.lib';

        # check if category/default category is groupbased
        $isGroupbased = false;
        if ($this->category) {
            $gtabid_reminder = 0;
            if ($greminder["argresult_id"][$this->category]) {
                $gtabid_reminder = $greminder["argresult_id"][$this->category];
            }
            if ($greminder[$gtabid_reminder]["groupbased"][$this->category]) {
                $isGroupbased = true;
            }
        }

        $multitenantId = null;
        $multitenantIdTranslated = null;

        $gfieldTable = $gfield[$this->tableId];
        if ((!$this->content and $gfieldTable["mainfield"]) or $umgvar['multitenant']) {
            require_once(COREPATH . 'gtab/gtab_type_erg.lib');
            $onlyfield = 'ID';
            if ($gfieldTable["mainfield"]) {
                $ffieldid = $gfieldTable["mainfield"];
                $onlyfield = [
                    $this->tableId => [$this->tableId => $ffieldid]
                ];
            }
            $gresult = get_gresult($this->tableId, 1, null, null, null, $onlyfield, $this->dataId);
            if ($ffieldid) {
                $fname = "cftyp_" . $gfieldTable["funcid"][$ffieldid];
                $fielddesc = $fname(0, $ffieldid, $this->tableId, 3, $gresult, 0);
            }

            // multitenant
            $multitenantId = $gresult[$this->tableId]['MID'][0];
            $multitenantIdTranslated = $lmmultitenants['translate'][$multitenantId];
        }

        $grouplist = [];
        $userlist = [];
        $maillist = [];

        if (count($userOrGroupArray) === 0) {
            $currentUserId = $session['user_id'];
            $userOrGroupArray[] = ['type' => 'user', 'id' => $currentUserId, 'name' => $userdat["bezeichnung"][$currentUserId]];
        }

        $isNotCorrectMultitenant = function ($userId) use ($session, $multitenantIdTranslated, $userdat) {
            return
                $session['multitenant'] &&
                $multitenantIdTranslated &&
                !in_array($multitenantIdTranslated, $userdat["multitenant"][$userId]);
        };

        foreach ($userOrGroupArray as $userOrGroup) {
            switch ($userOrGroup['type']) {
                case 'user':
                    if ($isNotCorrectMultitenant($userOrGroup['id'])) {
                        break;
                    }
                    $userlist[] = $userOrGroup['id'];
                    $maillist[] = $userOrGroup['id'];
                    break;
                case 'group':
                    foreach ($userdat["userid"] as $userId => $userValue) {
                        // filter hidden user
                        if ($userdat["hidden"][$userId]) {
                            continue;
                        }

                        // filter multitenants
                        if ($isNotCorrectMultitenant($userId)) {
                            continue;
                        }

                        $subgroup = explode(",", $userdat["subgroup"][$userId]);
                        if (in_array($userOrGroup['id'], $subgroup)) {
                            # todo check why userValue, not userId
                            $maillist[] = $userValue;
                        }
                    }
                    # if not groupbased, put every member of group onto the userlist
                    if ($isGroupbased) {
                        $grouplist[] = $userOrGroup['id'];
                    } else {
                        $userlist = $maillist;
                    }
                    break;
            }
        }

        $data = [
            'TAB_ID' => parse_db_int($this->tableId),
            'DAT_ID' => parse_db_int($this->dataId),
            'FRIST' => parse_db_string($this->preparedDeadline()),
            'DESCRIPTION' => parse_db_string($this->description),
            'FROMUSER' => parse_db_int($this->fromUser),
            'CONTENT' => parse_db_string($this->content),
            'CATEGORY' => parse_db_int($this->category),
            'WFL_INST' => parse_db_int($this->workflowInstance),
            'FORM_ID' => parse_db_int($this->formId),
            'LMB_MID' => parse_db_int($multitenantId),
        ];

        lmb_StartTransaction();

        # if reminder is groupbased, only create one reminder, and every user/group references that transaction, otherwise, create a reminder for each user and user in group
        if ($isGroupbased) {
            $data['ID'] = next_db_id(self::$tableName, 'ID', 1);
            if (!Database::insert(self::$tableName, $data)) {
                lmb_EndTransaction(false);
                return false;
            }
        }

        foreach ($userlist as $userId) {
            if (!$isGroupbased) {
                $data['ID'] = next_db_id(self::$tableName, 'ID', 1);
                if (!Database::insert(self::$tableName, $data)) {
                    lmb_EndTransaction(false);
                    return false;
                }
            }

            if (!Database::insert(self::$tableNameGroup, [
                'reminder_id' => $data['ID'],
                'user_id' => $userId,
            ])) {
                lmb_EndTransaction(false);
                return false;
            }
        }

        foreach ($grouplist as $groupId) {
            if (!Database::insert(self::$tableNameGroup, [
                'reminder_id' => $data['ID'],
                'group_id' => $groupId,
            ])) {
                lmb_EndTransaction(false);
                return false;
            }
        }

        if ($sendMail) {
            # if not standard category
            $mailHeader = match ($this->category !== 0) {
                true => $greminder[$gtabid_reminder]['name'][$this->category],
                false => $gtab['desc'][$this->tableId],
            };

            foreach ($maillist as $userId) {
                if (!$userdat['email'][$userId]) {
                    continue;
                }

                if ($greminder[$gtabid_reminder]["message"][$this->category]) {
                    $formatFunction = $greminder[$gtabid_reminder]['message'][0];
                    $message = $formatFunction (
                        $userId,
                        $this->tableId,
                        $this->dataId,
                        $this->category,
                        $this->preparedDeadline(),
                        $this->description,
                        $this->content,
                        $this->workflowInstance
                    );
                } else {
                    $message = $this->getDefaultMail($userId, $mailHeader);
                }

                $lmbMail = new LmbMail();
                $lmbMail->sendFromDefault(
                    $userdat["email"][$userId],
                    "Limbas - $lang[425] für $this->description {$greminder[$gtabid_reminder]['name'][$this->category]}",
                    $message
                );
            }
        }

        lmb_EndTransaction(true);
        return true;
    }

    private function getDefaultMail($userId, $mailHeader): string
    {
        global $userdat, $session, $umgvar;

        $messageDescription = $this->description ?: $this->description . '<br><br>';

        return <<<HTML
            <br><br>
            hallo {$userdat['bezeichnung'][$userId]}<br><br>
            {$session['vorname']} {$session['name']} sent you a $mailHeader - reminder for:<br>
            <i>
                <b>
                    <a href=\"{$umgvar['url']}/main.php?action=\"> $this->content </a>
                </b>
            </i>
            <br><br><br>
            $messageDescription
            -------------------------------------------------------------------------------------<br>
            This is an automatically generated email, please do not reply!<br>
            -------------------------------------------------------------------------------------<br>
            <br><br>
         HTML;
    }

    public static function getReminderWhereFilter(?int $category = null, ?int $tableId = null, ?int $dataId = null, ?bool $valid = false): array
    {
        global $session;
        global $lmmultitenants;
        global $greminder;
        global $gtab;

        if ($valid === null) {
            $valid = false;
        }

        $where = [];

        // permissions
        #if ($category && !$greminder["argresult_id"][$category]) {
            #return [];
        #}

        $rgtabid = $greminder["argresult_id"][$category];

        // multitenat
        $sessionMultitenantId = $lmmultitenants['mid'][$session['mid']];
        if ($sessionMultitenantId) {
            // table based
            if ($rgtabid) {
                if ($gtab["multitenant"][$rgtabid]) {
                    $where[] = 'LMB_REMINDER.LMB_MID = ' . parse_db_int($sessionMultitenantId);
                }
                // table independent
            } else {
                $where[] = '(LMB_REMINDER.LMB_MID = ' . parse_db_int($sessionMultitenantId) . ' OR LMB_REMINDER.LMB_MID = 0)';
            }
        }

        $where[] = "LMB_REMINDER.ID = LMB_REMINDER_GROUP.REMINDER_ID";

        // group based
        if ($greminder[$rgtabid]["groupbased"][$category]) {
            $where[] = "(LMB_REMINDER_GROUP.GROUP_ID IN (" . implode(",", $session["subgroup"]) . ") OR LMB_REMINDER_GROUP.USER_ID = " . $session["user_id"] . ")";
            // user based
        } else {
            $where[] = "LMB_REMINDER_GROUP.USER_ID = " . $session["user_id"];
        }

        // is valid
        if ($valid) {
            $where[] = "LMB_REMINDER.FRIST < " . LMB_DBDEF_TIMESTAMP;
        }

        // category
        if ((($category && $greminder[$greminder["argresult_id"][$category]]["name"][$category]) || $category === '0')) {
            $where[] = "LMB_REMINDER.CATEGORY = " . parse_db_int($category);
            // default reminder
        } elseif ($category === 0) {
            $where[] = "LMB_REMINDER.CATEGORY = 0";
            // all
        } elseif ($tableId) {
            $where[] = "LMB_REMINDER.TAB_ID = " . parse_db_int($tableId);
        }

        if ($dataId) {
            $where[] = "LMB_REMINDER.DAT_ID = " . parse_db_int($dataId);
        }

        return $where;
    }

    public function delete(): bool
    {
        global $db;

        $whereFields = self::getReminderWhereFilter($this->category, $this->tableId, $this->dataId);
        if ($this->workflowInstance) {
            $whereFields[] = "LMB_REMINDER.WFL_INST = " . parse_db_int($this->workflowInstance);
        }
        if ($this->id) {
            $whereFields[] = "LMB_REMINDER.ID = " . parse_db_int($this->id);
        }
        $whereFields = implode(' AND ', $whereFields);

        $sqlquery = <<<SQL
            DELETE 
            FROM LMB_REMINDER 
            WHERE ID 
            IN (
                  SELECT DISTINCT LMB_REMINDER.ID 
                  FROM LMB_REMINDER,LMB_REMINDER_GROUP 
                  WHERE $whereFields
            )
        SQL;
        $rs = lmbdb_exec($db, $sqlquery) or errorhandle(lmbdb_errormsg($db), $sqlquery, $GLOBALS['action'], __FILE__, __LINE__);

        return (bool)$rs;
    }

    public static function deleteByFilter($reminderId, $tableId=null, $category=null, $dataId=null, $workflowInstance=null, $valid=null): bool {
        global $db;
        global $greminder;
        $sql = array();

        if(!isset($category) && $reminderId){
            $sqlquery = "SELECT CATEGORY FROM LMB_REMINDER WHERE ID = $reminderId";
            $rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$GLOBALS['action'],__FILE__,__LINE__);
            $category = parse_db_int(lmbdb_result($rs, 'CATEGORY'));
        }

        # check if is category
        if($category){
            $gtabid_reminder = 0;
            if($greminder["argresult_id"][$category]){
                $gtabid_reminder = $greminder["argresult_id"][$category];
            }
            if($greminder[$gtabid_reminder]["groupbased"][$category]){
                $groupbased = 1;
            }
        }

        // filter
        $reminderWhereFilter = Reminder::getReminderWhereFilter($category, $tableId, $dataId, $valid);
        $sql = array_merge($sql, $reminderWhereFilter);

        if($workflowInstance){$sql[] = "LMB_REMINDER.WFL_INST = ".parse_db_int($workflowInstance);}
        if($reminderId){$sql[] = "LMB_REMINDER.ID = ".parse_db_int($reminderId);}

        $where = implode(" AND ",$sql);

        // LMB_REMINDER_GROUP deleted from foreign key
        $sqlquery = "DELETE FROM LMB_REMINDER WHERE ID IN (SELECT DISTINCT LMB_REMINDER.ID FROM LMB_REMINDER,LMB_REMINDER_GROUP WHERE " . $where . ")";
        $rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$GLOBALS['action'],__FILE__,__LINE__);

        return (bool) $rs;
    }

    public static function countPerCategory(int $category): int
    {
        #$db = Database::get();
        global $db;

        $where = self::getReminderWhereFilter(parse_db_int($category), valid: true);
        $where = implode(' AND ', $where);

        $sqlquery = <<<SQL
            SELECT COUNT(*) AS COUNT
            FROM LMB_REMINDER 
            WHERE ID 
                      IN (
                          SELECT DISTINCT LMB_REMINDER.ID 
                          FROM LMB_REMINDER,LMB_REMINDER_GROUP 
                          WHERE $where
                      )
        SQL;
        $result = lmbdb_exec($db, $sqlquery) or errorhandle(lmbdb_errormsg($db), $sqlquery, $GLOBALS['action'], __FILE__, __LINE__);

        if (!$result) {
            return 0;
        }
        if (lmbdb_fetch_row($result)) {
            return lmbdb_result($result, 'COUNT');
        }
        return 0;
    }
}
