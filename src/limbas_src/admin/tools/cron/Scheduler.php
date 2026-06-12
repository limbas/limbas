<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */

namespace Limbas\admin\tools\cron;

use DateTimeImmutable;
use DateTimeInterface;
use InvalidArgumentException;
use Limbas\lib\auth\Auth;
use Limbas\lib\db\Database;
use RuntimeException;

class Scheduler
{
    /** @var Task[] */
    protected array $tasks = [];

    /**
     * Register a command by its class name.
     *
     * @param string $commandClass FQCN of the command
     * @param string $expression Cron expression
     * @param int|null $userId User ID to run as (calls Auth::login($userId))
     * @param string|null $name Optional name for logging
     */
    public function command(
        string  $commandClass,
        string  $expression,
        array   $parameters = [],
        ?int    $userId = null,
        ?string $name = null
    ): self
    {
        if (!class_exists($commandClass)) {
            throw new InvalidArgumentException("Command class $commandClass does not exist.");
        }

        $this->tasks[] = new Task($expression, $commandClass, $parameters, $userId, $name);
        return $this;
    }

    /**
     * Run all tasks now
     */
    public function runTasks(): void
    {
        foreach ($this->tasks as $task) {
            $this->runCommandClass($task);
        }
    }

    /**
     * Run all tasks that are due for the given time (default: now).
     *
     * @param DateTimeInterface|null $now
     * @return void
     */
    public function runDueTasks(?DateTimeInterface $now = null): void
    {
        $now = $now
            ? DateTimeImmutable::createFromInterface($now)
            : new DateTimeImmutable('now');

        foreach ($this->tasks as $task) {
            if ($this->expressionIsDue($task->expression, $now)) {
                $this->runCommandClass($task);
            }
        }
    }

    protected function runCommandClass(Task $task): void
    {
        // TODO: logging

        // load an admin as default user
        $adminUserId = null;
        $rs = Database::select('LMB_USERDB', ['ID'], ['GROUP_ID' => 1], 1, ['ID' => 'asc']);
        $adminUser = lmbdb_fetch_object($rs);
        if (!empty($adminUser)) {
            $adminUserId = intval($adminUser->ID);
        }
        if (empty($adminUserId)) {
            return;
        }

        if (!empty($task->userId)) {
            Auth::loginUsingId($task->userId, true);
        } else {
            Auth::loginUsingId($adminUserId, true);
        }

        // Instantiate and run command
        $instance = new $task->commandClass();

        if (!method_exists($instance, 'handle')) {
            throw new RuntimeException("Command $task->commandClass must have a handle() method.");
        }

        $instance->handle($task->parameters);

        Auth::logout(true);
    }

    /**
     * Check if a 5-part cron expression is due at $now.
     * Format: "min hour day month weekday"
     */
    protected function expressionIsDue(string $expression, DateTimeImmutable $now): bool
    {
        $expression = trim($expression);
        $parts = preg_split('/\s+/', $expression);
        if (count($parts) !== 5) {
            throw new InvalidArgumentException("Invalid cron expression: $expression");
        }

        [$min, $hour, $day, $month, $weekday] = $parts;

        $currentMinute = (int)$now->format('i'); // 0-59
        $currentHour = (int)$now->format('G'); // 0-23
        $currentDay = (int)$now->format('j'); // 1-31
        $currentMonth = (int)$now->format('n'); // 1-12
        $currentWeekday = (int)$now->format('w'); // 0-6 (0 = Sunday)

        return $this->fieldMatches($min, $currentMinute, 0, 59)
            && $this->fieldMatches($hour, $currentHour, 0, 23)
            && $this->fieldMatches($day, $currentDay, 1, 31)
            && $this->fieldMatches($month, $currentMonth, 1, 12)
            && $this->fieldMatches($weekday, $currentWeekday, 0, 6);
    }

    protected function fieldMatches(string $field, int $value, int $min, int $max): bool
    {
        $field = trim($field);

        if ($field === '*') {
            return true;
        }

        $items = explode(',', $field);

        foreach ($items as $item) {
            $item = trim($item);
            if ($item === '') {
                continue;
            }

            if ($this->itemMatches($item, $value, $min, $max)) {
                return true;
            }
        }

        return false;
    }

    protected function itemMatches(string $item, int $value, int $min, int $max): bool
    {
        $step = 1;
        if (str_contains($item, '/')) {
            [$rangePart, $stepPart] = explode('/', $item, 2);
            $step = (int)$stepPart ?: 1;
        } else {
            $rangePart = $item;
        }

        if ($rangePart === '*') {
            $start = $min;
            $end = $max;
        } elseif (str_contains($rangePart, '-')) {
            [$start, $end] = explode('-', $rangePart, 2);
            $start = max($min, (int)$start);
            $end = min($max, (int)$end);
        } else {
            $start = $end = (int)$rangePart;
        }

        if ($value < $start || $value > $end) {
            return false;
        }

        return (($value - $start) % $step) === 0;
    }
}
