<?php

namespace Limbas\gtab\export;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\IWriter;
use PhpOffice\PhpSpreadsheet\Writer\Csv;
use PhpOffice\PhpSpreadsheet\Writer\Ods;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

/**
 * @brief TableExportTypes
 * @details Enum for the different types of table exports,
 * To add a new type:
 * => add a new constant to this enum
 * => match arm + function to TableExport::exportByType
 */
enum TableExportTypes: int
{
    case CSV = 3;
    case ODS = 2;
    case Excel = 1;

    public function getExtension(): string
    {
        return match($this) {
            TableExportTypes::CSV => "csv",
            TableExportTypes::ODS => "ods",
            TableExportTypes::Excel => "xlsx",
        };
    }

    public function getMimeType(): string
    {
        return match($this) {
            TableExportTypes::CSV => "text/csv",
            TableExportTypes::ODS => "application/vnd.oasis.opendocument.spreadsheet",
            TableExportTypes::Excel => "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
        };
    }

    public function getCFTyp_returnType(): int
    {
        return match($this) {
            TableExportTypes::CSV => 5,
            TableExportTypes::ODS => 7,
            TableExportTypes::Excel => 7,
        };
    }

    public function getIWriter(Spreadsheet $spreadsheet): IWriter
    {
        return match($this) {
            TableExportTypes::CSV => new Csv($spreadsheet),
            TableExportTypes::ODS => new Ods($spreadsheet),
            TableExportTypes::Excel => new Xlsx($spreadsheet),
        };
    }
}
