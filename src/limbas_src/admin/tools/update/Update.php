<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */
namespace Limbas\admin\tools\update;

use Limbas\lib\auth\Session;
use Limbas\lib\db\Database;
use Limbas\lib\globals\Lang;
use ReflectionClass;
use ReflectionException;
use Symfony\Component\HttpFoundation\Request;
use Throwable;

abstract class Update
{
    protected int $id;

    protected int $major;
    protected int $minor;
    protected int $patch;

    protected array $patches;

    public bool $completed;

    /**
     * Return the unique id of this update file
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }
    

    /**
     * Run all patches of this updates
     * @return bool
     */
    public function run(): bool
    {
        $patches = $this->getMissingPatches();

        $allSuccessful = true;
        foreach ($patches as $patchNr => $patch) {
            $status = $this->runPatch($patchNr);
            if (!$status) {
                $allSuccessful = false;
            }
        }

        return $allSuccessful;
    }

    /**
     * Run specific patch
     *
     * @param $patchNr
     * @return false
     */
    public function runPatch($patchNr): bool
    {
        global $alert;
        $alert = null;
        
        #lmb_StartTransaction();
        
        
        $this->getPatches();
        try {
            $this->patch = $patchNr;
            $methodName = 'patch' . $patchNr;
            $success = false;
            if (method_exists($this, $methodName)) {
                $success = $this->$methodName();
            }
            $this->patches[$patchNr]['status'] = $success;
        } catch (Throwable $t) {
            $success = false;
            $this->patches[$patchNr]['error'] = $t->getMessage();
        }

        #lmb_EndTransaction($success,'none');
        
        $this->applyPatch($this->patches[$patchNr]['desc'], $success, $this->patches[$patchNr]['error']);

        if($success) {
            $this->resetSession();
        }
        
        return $success;
    }
    
    private function resetSession(): void
    {
        $systemInformation = Updater::getSystemInfo(true);
        $updateNecessary = $systemInformation['updateNecessary'];
        $completed = $systemInformation['completed'];

        if($updateNecessary || !$completed['current']) {
            return;
        }
        
        $request = Request::createFromGlobals();
        Session::load($request,true);
    }

    /**
     * Returns the version without patch number as string or array
     *
     * @param bool $asArray
     * @return array|string
     */
    public function getVersion(bool $asArray = false): array|string
    {

        if ($asArray) {
            return [
                $this->major,
                $this->minor
            ];
        }

        return $this->major . '.' . $this->minor;
    }


    /**
     * Get all patches of this update
     * @return array
     */
    public function getPatches(): array
    {
        if (empty($this->patches)) {
            $functions = get_class_methods($this);

            $this->patches = [];
            $allSuccessful = true;
            foreach ($functions as $function) {
                if (str_starts_with($function, 'patch')) {
                    $patchNr = intval(substr($function, 5));
                    $status = Updater::checkPatchDidRun($this->major, $this->minor, $patchNr);
                    $this->patches[$patchNr] = [
                        'status' => $status,
                        'desc' => $this->getPatchDescription($patchNr),
                        'error' => $this->getPatchMessage($patchNr),
                        'uid' => $this->id
                    ];
                    if (!$status) {
                        $allSuccessful = false;
                    }
                }
            }

            ksort($this->patches);

            $this->completed = $allSuccessful;
        }
        return $this->patches;
    }

    /**
     * Get all patches that were unsuccessful or never run
     * @return array
     */
    public function getMissingPatches(): array
    {
        $this->getPatches();
        return array_filter($this->patches, function ($patch) {
            return !$patch['status'];
        });
    }


    /**
     * Return specific patch
     *
     * @param $patchNr
     * @return mixed|null
     */
    public function getPatch($patchNr): mixed
    {
        $this->getPatches();
        if (!array_key_exists($patchNr, $this->patches)) {
            return null;
        }

        return $this->patches[$patchNr];
    }

    /**
     * Get the description of a patch
     *
     * @param $patchNr
     * @return string
     */
    private function getPatchDescription($patchNr): string
    {

        try {
            $reflectionMethod = (new ReflectionClass($this))->getMethod('patch' . $patchNr);
        } catch (ReflectionException $e) {
            return '';
        }

        $phpDoc = $reflectionMethod->getDocComment();

        $lines = array_map(function ($line) {
            return trim($line, ' */');
        }, explode("\n", $phpDoc));


        $lines = array_filter($lines, function ($line) {
            return !str_contains($line, "@");
        });

        return implode(' ', $lines);
    }

    /**
     * Get previous (error) message from db
     * @param $patchNr
     * @return string
     */
    private function getPatchMessage($patchNr): string
    {
        $db = Database::get();

        $sql = "SELECT MSG FROM LMB_DBPATCH WHERE MAJOR = $this->major AND VERSION = $this->minor AND REVISION = $patchNr AND MSG != ''";
        $rs = lmbdb_exec($db, $sql);
        if (!$rs || !lmbdb_fetch_row($rs)) {
            return '';
        }

        return '' . lmbdb_result($rs, 'MSG');
    }

    /**
     * Insert patch information into db
     *
     * @param string $desc
     * @param bool $success
     * @param string $error
     * @return bool
     */
    protected function applyPatch(string $desc, bool $success, string $error = ''): bool
    {
        return Updater::applyPatch($this->major, $this->minor, $this->patch, $desc, $success, $error);
    }


    /**
     * Run an update on the database
     *
     * @param string|array $sql
     * @return bool
     */
    protected function databaseUpdate(string|array $sql): bool
    {
        $db = Database::get();
        $alreadyApplied = Updater::checkPatchDidRun($this->major, $this->minor, $this->patch);

        if ($alreadyApplied) {
            return true;
        }


        $success = true;

        if (!empty($sql)) {
            
            if (is_array($sql)) {
                $errorMsg = '';
                foreach($sql as $sqlQuery) {
                    $rs = lmbdb_exec($db, $sqlQuery);

                    if (!$rs) {
                        $errorMsg .= ' ' . lmbdb_errormsg($db);
                        $success = false;
                    }
                }

                if (!$success) {
                    $this->patches[$this->patch]['error'] = $errorMsg;
                }

            } else {
                $rs = lmbdb_exec($db, $sql);
                if (!$rs) {
                    $this->patches[$this->patch]['error'] = lmbdb_errormsg($db);
                    $success = false;
                }
            }
        }

        return $success;

    }

    /**
     * @param string $table
     * @param string $field
     * @param string $value
     * @return bool
     */
    protected function valueExistsInDb(string $table, string $field, string $value): bool {
        $exists = false;

        $db = Database::get();

        $sqlQuery = "SELECT * FROM $table WHERE $field = '$value'";
        
        $rs = lmbdb_exec($db, $sqlQuery);

        if ($rs && lmbdb_num_rows($rs) > 0) {
            $exists = true;
        }
        
        return $exists;
    }

    /**
     * @param array $tables
     * @return bool
     */
    protected function importTables(array $tables): bool
    {
        global $session;
        
        $db = Database::get();
        $alreadyApplied = Updater::checkPatchDidRun($this->major, $this->minor, $this->patch);

        if ($alreadyApplied) {
            return true;
        }
        
        if(in_array('LMB_LANG', $tables)) {
            Lang::reSeedLanguage();
        }
        
        // table import replaced by new language import
        $tables = array_filter($tables, function ($table) {
            return $table !== 'LMB_LANG';
        });
        
        $tables = array_map(function($value) {
            return lmb_strtoupper($value . '.tar.gz');
        }, $tables);


        require_once(COREPATH . 'admin/tools/import.dao');

        $tempUserPath = USERPATH . $session['user_id'] . '/temp/';

        #rmdirf($umgvar["pfad"]."/USER/".$session["user_id"]."/temp/");
        system('rm ' . $tempUserPath . '*');
        system('cp -r ' . COREPATH . 'admin/tools/update/tables/* '. $tempUserPath);


        $tablegrouplist = [];
        
        # Liste aller Tabellen
        if($folderval = read_dir($tempUserPath)){
            foreach($folderval['name'] as $value){
                if(in_array(lmb_strtoupper($value),$tables)){
                    $tablename = lmb_substr($value,0,lmb_strlen($value)-7);
                    $tablegrouplist[$tablename] = 1;
                }
            }
        }

        ob_start(
            function () { return ''; }
        );
        $result = boolval(import_tab_pool('atm','over','group',1,null,null,null,$tablegrouplist));
        ob_end_clean();
        
        if(!$result){
            $this->patches[$this->patch]['error'] = 'Import of tables failed.';
        }

        return $result;
    }

}
