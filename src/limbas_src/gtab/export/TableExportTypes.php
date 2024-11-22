<?php

namespace Limbas\gtab\export;

use Limbas\gtab\export\filetypes\CSV;
use Limbas\gtab\export\filetypes\Excel;
use Limbas\gtab\export\filetypes\FiletypeExporter;
use Limbas\gtab\export\filetypes\ODS;

/**
 * Table Export Types.
 * Is currently needed only for include.lib for dynamically showing the export types in select, could be removed otherwise
 */
enum TableExportTypes: int
{
    case CSV = 3;
    case ODS = 2;
    case Excel = 1;

    public function exporter(): FiletypeExporter {
        return match ($this) {
            self::Excel => new Excel(),
            self::ODS => new ODS(),
            self::CSV => new CSV(),
        };
    }
}