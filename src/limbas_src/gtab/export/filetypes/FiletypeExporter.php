<?php

namespace Limbas\gtab\export\filetypes;

abstract class FiletypeExporter
{
    const FILE_EXTENSION = "";
    const MIME_TYPE = "";
    const CF_TYP_RETURN_TYPE = 5;

    public function getFileExtension(): string
    {
        return static::FILE_EXTENSION;
    }

    public function getMimeType(): string
    {
        return static::MIME_TYPE;
    }

    public function getCFTyp_returnType(): int
    {
        return static::CF_TYP_RETURN_TYPE;
    }

    /**
     * export should only write the table to php://output, headers get set by TableExport class
     * @param array $gresult
     * @param int $gtabid
     * @return void
     */
    public abstract function export(array $gresult, int $gtabid): void;
}