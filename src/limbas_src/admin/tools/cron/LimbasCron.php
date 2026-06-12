<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */

namespace Limbas\admin\tools\cron;

class LimbasCron
{
    private Scheduler $scheduler;

    public function run(): void
    {
        define('IS_CRON', true);

        require_once(COREPATH . 'lib/include_admin.lib');
        require_once(COREPATH . 'admin/tools/jobs_ext.lib');

        $this->scheduler = new Scheduler();

        $this->loadJobs();

        if (file_exists(EXTENSIONSPATH . 'ext_cron.php')) {
            require_once(EXTENSIONSPATH . 'ext_cron.php');
        }

        $this->scheduler->runDueTasks();
    }


    public function loadJobs(): void
    {
        $jobs = CronJob::active();

        /** @var CronJob $job */
        foreach ($jobs as $job) {
            if ($job->type === JobType::UNSET) {
                continue;
            }
            $this->scheduler->command($job->type->getCommandClass(), $job->expression(), ['config' => $job->config->toArray()], $job->userId);
        }

    }

    public static function runJobById(int $id): void
    {
        define('IS_CRON', true);
        CronJob::get($id)->run();
    }

    public static function runCommand(int|string $command, array $args = []): int
    {
        define('IS_CRON', true);
        if (empty($command)) {
            return 1;
        }

        $scheduler = new Scheduler();

        if (is_int($command)) {
            $job = CronJob::get($command);
            if (empty($job)) {
                return 1;
            }
            $scheduler->command($job->type->getCommandClass(), $job->expression(), ['config' => $job->config->toArray()], $job->userId);
        } else {
            $commandClass = '\\Limbas\\admin\\tools\\cron\\commands\\' . $command;
            if (!class_exists($commandClass)) {
                return 1;
            }

            $short_options = 'c:';
            $long_options = ['config:'];
            $options = getopt($short_options, $long_options);

            if (isset($options['c']) || isset($options['config'])) {
                $config = $options['c'] ?? $options['config'];
                if (is_string($config)) {
                    $config = json_decode($config);
                }
                if (!empty($config)) {
                    $args['config'] = $config;
                }
            }

            $scheduler->command($commandClass, '', $args);
        }

        $scheduler->runTasks();

        return 0;
    }

}
