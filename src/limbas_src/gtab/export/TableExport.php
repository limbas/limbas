<?php

namespace Limbas\gtab\export;

use DateTime;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Shared\Date as SharedDate;

/**
 * @brief TableExport
 * @details Class to export a table to different file formats
 */
class TableExport
{
    private Spreadsheet $spreadsheet;
    private int $gtabid;
    private TableExportTypes $exportType;
    private int $cftypType;


    /**
     * Construct the TableExport, use export function to output the file
     * @param int $gtabid
     * @param array|null $relations
     * @param bool $onlyGetVisibleRows / only get currently visible rows
     * @param TableExportTypes $exportType / the type of export
     */
    public function __construct(int $gtabid, TableExportTypes $exportType, ?array $relations = [], bool $onlyGetVisibleRows = false) {
        $this->spreadsheet = new Spreadsheet();
        $this->gtabid = $gtabid;
        $this->exportType = $exportType;
        $this->cftypType = $exportType->getCFTyp_returnType();

        $data = $this->getData($relations, $onlyGetVisibleRows);
        $this->constructWorksheet($data, $onlyGetVisibleRows);
    }

    /**
     * Fetches gresult
     * @param array|null $relations
     * @param bool $onlyGetVisibleRows
     * @return array
     */
    private function getData(?array $relations, bool $onlyGetVisibleRows = false): array {
        global $filter;
        global $gsr;

        $modifiedFilter = $filter;
        $modifiedFilter["getlongval"][$this->gtabid] = 1;
        if (!$onlyGetVisibleRows) {
            $modifiedFilter["anzahl"][$this->gtabid] = 'all';
        }
        return get_gresult($this->gtabid, 1, $modifiedFilter, $gsr, $relations);
    }

    /**
     * Construct the worksheet in spreadsheet memory
     * @param array $gresult
     * @param bool $onlyGetVisibleRows
     */
    private function constructWorksheet(array $gresult, bool $onlyGetVisibleRows): void {
        global $gfield;
        global $filter;

        $worksheet = $this->spreadsheet->getActiveSheet();


        $sortKeys = array_keys($gfield[$this->gtabid]["sort"]);

        // xlsx/odf coordinates [column, row] => [1, 1] is A1
        $rowIndex = 1;
        $colIndex = 1;

        // Set the column titles in the top row
        foreach ($sortKeys as $fieldId) {
            if (!$gfield[$this->gtabid]["funcid"][$fieldId]) {
                continue;
            }

            $fieldType = $gfield[$this->gtabid]["field_type"][$fieldId];
            $hideCols = $filter["hidecols"][$this->gtabid][$fieldId];

            if (!$hideCols && $fieldType < 100 && $fieldType != 20) {
                $worksheet->setCellValue([$colIndex, $rowIndex], $gfield[$this->gtabid]['spelling'][$fieldId]);
                $colIndex++;
            }
        }

        // Move to the next row
        $rowIndex++;
        $colIndex = 1;


        // Only get results of visible rows
        if (!$onlyGetVisibleRows) {
            $rescount = $gresult[$this->gtabid]["res_count"];
        } else {
            $rescount = $gresult[$this->gtabid]["res_viewcount"];
        }

        // Set the data in the rows
        for ($resultCounter = 0; $resultCounter < $rescount; $resultCounter++) {
            foreach ($sortKeys as $fieldId) {
                if (!$gfield[$this->gtabid]["funcid"][$fieldId]) {
                    continue;
                }

                $fieldType = $gfield[$this->gtabid]["field_type"][$fieldId];
                $hideCols = $filter["hidecols"][$this->gtabid][$fieldId];

                if (!$hideCols && $fieldType < 100 && $fieldType != 20) {
                    $fieldFunctionName = "cftyp_" . $gfield[$this->gtabid]["funcid"][$fieldId];

                    $formattedField = $fieldFunctionName($resultCounter, $fieldId, $this->gtabid, $this->cftypType, $gresult, 0);
                    if (is_array($formattedField)) {
                        if ($fieldType == 11 && is_array($formattedField["value"])) {
                            $formattedField = implode("; ", $formattedField["value"]);
                        } else {
                            $formattedField = implode("; ", $formattedField);
                        }
                    }

                    $parseType = $gfield[$this->gtabid]["parse_type"][$fieldId];
                    $dataType = $gfield[$this->gtabid]["data_type"][$fieldId];

                    // todo: outsource the following lines when field types get refactored
                    // Add percentage sign to percentage fields
                    if ($dataType == 21 && $formattedField != '') {
                        $formattedField .= '%';
                    } elseif ($dataType == 39) {
                        // Strip HTML tags from text fields
                        $formattedField = strip_tags($formattedField);
                    }

                    // Dates
                    if($parseType == 4 && $this->cftypType == 7) {
                        if ($formattedField == '') {
                            $worksheet->setCellValue([$colIndex, $rowIndex], '');
                        }
                        else {
                            $dateTime = (new DateTime)->setTimestamp((int)$formattedField);
                            $dateformat = $gfield[$this->gtabid]["datetime"][$fieldId];

                            $formatCode = match ((int) $dateformat) {
                                1 => setDateFormat(1, 2),               // date
                                4 => setDateFormat(4, 2),               // date with time
                                default => setDateFormat(5, 2),         // date with time and seconds
                            };

                            $cellCoordinate = [$colIndex, $rowIndex];
                            $worksheet
                                ->setCellValue($cellCoordinate, SharedDate::PHPToExcel($dateTime))
                                ->getStyle($cellCoordinate)
                                ->getNumberFormat()
                                ->setFormatCode($formatCode);
                        }
                    } else {
                        $worksheet->setCellValue([$colIndex, $rowIndex], $formattedField);
                    }
                    $colIndex++;
                }
            }
            $rowIndex++;
            $colIndex = 1;
        }
    }


    /**
     * Write export file to output
     * @return void
     */
    public function export(): void {
        global $gtab;

        $writer = $this->exportType->getIWriter($this->spreadsheet);

        ob_clean();

        $fileName = $gtab["table"][$this->gtabid] . "_" . date("Y-m-d-h-m-s");
        $fileName .=  '.' . $this->exportType->getExtension();

        header('Content-Type: ' . $this->exportType->getMimeType());
        header('Content-Disposition: attachment;filename="' . $fileName . '"');
        header('Cache-Control: max-age=0');
        $writer->save("php://output");
    }
}