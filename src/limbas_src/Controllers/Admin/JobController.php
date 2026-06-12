<?php

namespace Limbas\Controllers\Admin;

use Exception;
use Limbas\admin\tools\cron\CronJob;
use Limbas\admin\tools\cron\JobType;
use Limbas\Controllers\LimbasController;
use Limbas\lib\db\Database;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class JobController extends LimbasController
{

    public function handleRequest(array $request): array
    {
        return [];
    }

    public function index(): string
    {
        global $lang;
        global $umgvar;
        require_once COREPATH . 'lib/include.lib';

        $jobs = CronJob::all(orderBy: ['TYPE' => 'asc', 'ID' => 'asc']);

        ob_start();
        include COREPATH . 'admin/tools/cron/html/index.php';
        return ob_get_clean() ?: '';
    }

    /**
     * @throws Exception
     */
    public function create(Request $request): Response
    {
        global $file_struct;
        global $lang;
        global $farbschema;
        global $gtab;
        global $gfield;
        global $umgvar;

        $file_struct = self::constructFileStruct();

        $job = new CronJob(); // create new job for default values

        $route = route('admin.jobs.store');

        ob_start();
        include COREPATH . 'admin/tools/cron/html/edit.php';
        $response = ['html' => ob_get_clean() ?: ''];

        return $this->respond($response);
    }

    public function store(Request $request): Response
    {
        $job = new CronJob(
            0,
            JobType::from($request->get('jobtype')),
            $request->get('cron_minutes'),
            $request->get('cron_hours'),
            $request->get('cron_monthdays'),
            $request->get('cron_months'),
            $request->get('cron_weekdays'),
            $request->get('active', false),
            $request->get('description', ''),
            (int)$request->get('jobuser'),
        );

        $response = ['success' => $job->save()];

        ob_start();
        include COREPATH . 'admin/tools/cron/html/job-row.php';
        $response += ['html' => ob_get_clean() ?: ''];

        return $this->respond($response);
    }

    /**
     * @throws Exception
     */
    public function edit(Request $request): Response
    {
        global $file_struct;
        global $lang;
        global $farbschema;
        global $gtab;
        global $gfield;
        global $umgvar;

        $file_struct = self::constructFileStruct();

        $id = $request->get('id');

        $job = CronJob::get($id);

        $route = route('admin.jobs.update', ['id' => $id]);

        ob_start();
        include COREPATH . 'admin/tools/cron/html/edit.php';
        $response = ['html' => ob_get_clean() ?: ''];
        $response['selected_user'] = ['id' => $job->userId, 'name' => $job->userName];

        return $this->respond($response);
    }

    private static function constructFileStruct(): array
    {
        global $file_struct;

        require_once(COREPATH . 'extra/explorer/metadata.lib');
        require_once(COREPATH . 'extra/explorer/filestructure.lib');

        $rs = Database::query("SELECT ID,LEVEL,NAME FROM LDMS_STRUCTURE WHERE TYP = 1 OR TYP = 3 OR TYP = 7 ORDER BY TYP,NAME");
        if (!$rs) {
            $commit = 1;
        }
        while (lmbdb_fetch_row($rs)) {
            $file_struct["id"][] = lmbdb_result($rs, "ID");
            $file_struct["name"][] = lmbdb_result($rs, "NAME");
            $file_struct["level"][] = lmbdb_result($rs, "LEVEL");
        }

        return $file_struct;
    }

    public function update(Request $request): Response
    {
        $job = CronJob::get($request->get('id'));

        $job->cronMinutes = $request->get('cron_minutes');
        $job->cronHours = $request->get('cron_hours');
        $job->cronMonthdays = $request->get('cron_monthdays');
        $job->cronMonths = $request->get('cron_months');
        $job->cronWeekdays = $request->get('cron_weekdays');

        $job->active = boolval($request->get('active'));

        $job->description = $request->get('description', '');

        $job->config->includeSubdirs = boolval($request->get('include_subdirs'));
        $job->config->template = $request->get('template', '');
        $job->config->files = $request->get('files', []);
        $job->config->fields = $request->get('fields', []);

        $job->config->backupType = $request->get('backup_type', '');
        $job->config->backupMedium = $request->get('backup_medium', '');
        $job->config->backupTarget = $request->get('backup_target', '');
        $job->config->backupAlive = $request->get('backup_alive', 0);

        $job->userId = (int)$request->get('jobuser');

        $response = ['success' => $job->save()];

        ob_start();
        include COREPATH . 'admin/tools/cron/html/job-row.php';
        $response += ['html' => ob_get_clean() ?: ''];

        return $this->respond($response);
    }

    public function delete(Request $request): Response
    {
        $job = CronJob::get($request->get('id'));

        $response = ['success' => $job->delete()];

        return $this->respond($response);
    }

    public function run(Request $request): Response
    {
        CronJob::get($request->get('id'))->run();

        return $this->respond('');
    }
}