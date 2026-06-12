<?php

namespace Limbas\admin\tools\cron\configs;

use Limbas\admin\tools\cron\CronConfig;

class Template extends CronConfig
{
    public function getTemplate(): string
    {
        return $this->removeExtensionsFromPath($this->template) ?? '';
    }

    public function getAvailableTemplates(): array
    {
        global $umgvar;

        $templates = [];
        $basePath = $umgvar["pfad"] ?? '';

        $files = read_dir(EXTENSIONSPATH, 1);

        if (empty($files['name'])) {
            return [];
        }

        foreach ($files['name'] as $key => $filename) {
            $type = $files['typ'][$key] ?? '';
            $fullPath = $files['path'][$key] ?? '';

            $isJobFile = ($type === 'file' && strtolower(pathinfo($filename, PATHINFO_EXTENSION)) === 'job');

            if ($isJobFile) {
                $relativeUrlPath = str_replace($basePath, "", $fullPath);
                $displayPath = $this->removeExtensionsFromPath($relativeUrlPath);

                $templates[$relativeUrlPath . $filename] = $displayPath . $filename;
            }
        }

        return $templates;
    }

    private function removeExtensionsFromPath(string $path): string {
        return preg_replace('~^/EXTENSIONS/~', '', $path);
    }
}