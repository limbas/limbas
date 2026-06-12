<?php

namespace Limbas\admin\tools\cron;

abstract class CronConfig
{
    private function __construct(
        # config for indize (and ocr)
        public array  $files = [],
        public bool   $includeSubdirs = false,
        public array  $fields = [],

        # config for syncs, template
        public string $template = '',

        # config for backup
        public string $backupType = '',
        public string $backupMedium = '',
        public string $backupTarget = '',
        public int    $backupAlive = 30,
    )
    {
    }

    private static function fromJSON(string $json): static
    {
        $data = json_decode($json, true) ?: [];
        return new static(...$data);
    }

    public static function fromJob(CronJob $job, string $json = ''): ?CronConfig
    {
        return self::fromType($job->type, $json);
    }

    public static function fromType(JobType $type, string $json = ''): ?CronConfig
    {
        /** @var class-string<CronConfig>|"" $class */
        $class = $type->getConfigClass();

        if ($class === '' || !class_exists($class)) {
            return null;
        }

        return $class::fromJSON($json);
    }

    public function toArray(): array
    {
        return array_filter([
            'files' => $this->files,
            'includeSubdirs' => $this->includeSubdirs,
            'fields' => $this->fields,
            'template' => $this->template,
            'backupType' => $this->backupType,
            'backupMedia' => $this->backupMedium,
            'backupTarget' => $this->backupTarget,
            'backupAlive' => $this->backupAlive,
        ]);
    }

    public function toJson(): string
    {
        return json_encode($this->toArray());
    }

    public function getTemplate(): string
    {
        return '';
    }

    public function getAvailableTemplates(): array
    {
        return [];
    }
}
