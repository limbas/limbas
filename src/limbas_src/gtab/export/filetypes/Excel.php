<?php

namespace Limbas\gtab\export\filetypes;

use DateTime;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Common\Entity\Style\Style;
use OpenSpout\Common\Exception\IOException;
use OpenSpout\Writer\Exception\WriterNotOpenedException;
use OpenSpout\Writer\XLSX\Writer;

class Excel extends FiletypeExporter
{
    const FILE_EXTENSION = "xlsx";
    const MIME_TYPE = "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet";
    const CF_TYP_RETURN_TYPE = 7;

    /**
     * @throws IOException
     * @throws WriterNotOpenedException
     */
    public function export(array $gresult, int $gtabid, bool $onlyGetVisibleRows): void
    {
        global $gfield;
        global $filter;

        $sortKeys = array_keys($gfield[$gtabid]["sort"]);

        $writer = new Writer();

        $writer->openToFile("php://output");

        $titles = [];

        // Set the column titles in the top row
        foreach ($sortKeys as $fieldId) {
            if (!$gfield[$gtabid]["funcid"][$fieldId]) {
                continue;
            }

            $fieldType = $gfield[$gtabid]["field_type"][$fieldId];
            $hideCols = $filter["hidecols"][$gtabid][$fieldId];

            if (!$hideCols && $fieldType < 100 && $fieldType != 20) {
                $titles[] = $gfield[$gtabid]["field_name"][$fieldId];
            }
        }

        $writer->addRow(Row::fromValues($titles));

        // Only get results of visible rows
        if (!$onlyGetVisibleRows) {
            $rescount = $gresult[$gtabid]["res_count"];
        } else {
            $rescount = $gresult[$gtabid]["res_viewcount"];
        }

        // Set the data in the rows
        for ($resultCounter = 0; $resultCounter < $rescount; $resultCounter++) {
            $row = [];
            $styles = [];
            foreach ($sortKeys as $fieldId) {
                if (!$gfield[$gtabid]["funcid"][$fieldId]) {
                    continue;
                }

                $fieldType = $gfield[$gtabid]["field_type"][$fieldId];
                $hideCols = $filter["hidecols"][$gtabid][$fieldId];

                if($gfield[$gtabid]['col_hide'][$fieldId]){continue;}

                if (!$hideCols && $fieldType < 100 && $fieldType != 20) {
                    $fieldFunctionName = "cftyp_" . $gfield[$gtabid]["funcid"][$fieldId];

                    $parseType = $gfield[$gtabid]["parse_type"][$fieldId];
                    $dataType = $gfield[$gtabid]["data_type"][$fieldId];

                    $fieldStyle = null;

                    # check for implemented typ 7 in cftyp, if not use typ 5
                    if (in_array($parseType, [6, 3, 4]) || in_array($dataType, [16, 21])) {
                        $formattedField = $fieldFunctionName($resultCounter, $fieldId, $gtabid, self::CF_TYP_RETURN_TYPE, $gresult, 0);
                    } else {
                        $formattedField = $fieldFunctionName($resultCounter, $fieldId, $gtabid, CSV::CF_TYP_RETURN_TYPE, $gresult, 0);
                    }
                    if (is_array($formattedField)) {
                        if ($fieldType == 11 && is_array($formattedField["value"])) {
                            $formattedField = implode("; ", $formattedField["value"]);
                        } elseif ($fieldType == 19) {
                            $output = [];
                            foreach($formattedField as $key => $value) {
                                if($key === 'attrvalue' || $key === 'keyword') {
                                    continue;
                                }

                                $output[] = $value . (is_array($formattedField['attrvalue']) && array_key_exists($key,$formattedField['attrvalue']) && !empty($formattedField['attrvalue'][$key]) ? ' (' . $formattedField['attrvalue'][$key] . ')' : '');
                            }
                            $formattedField = implode("; ", $output);
                        } else {
                            $formattedField = implode("; ", $formattedField);
                        }
                    }

                    # Text
                    if ($dataType == 39) {
                        # Strip HTML tags from text fields
                        $formattedField = strip_tags($formattedField);
                    }

                    # Integer
                    if ($dataType == 16) {
                        $formattedField = intval($formattedField);
                    }

                    # Float
                    if ($parseType == 6) {
                        $formattedField = floatval($formattedField);

                        # Percentage
                        if ($dataType == 21) {
                            $formattedField /= 100;
                        }
                    }

                    # Boolean
                    if ($parseType == 3) {
                        $formattedField = boolval($formattedField);
                    }

                    # Dates
                    if ($parseType == 4) {

                        $dateTime = (new DateTime)->setTimestamp((int)$formattedField);
                        $dateFormat = $gfield[$gtabid]['datetime'][$fieldId];

                        if($dataType === 40) {
                            $dateFormat = 1;
                        }
                        
                        $formatCode = match ((int)$dateFormat) {
                            1 => setDateFormat(1, 2),               // date
                            4 => setDateFormat(4, 2),               // date with time
                            default => setDateFormat(5, 2),         // date with time and seconds
                        };

                        $formattedField = $formattedField == '' ? '' : $dateTime;

                        $fieldStyle = (new Style())->setFormat($formatCode);
                        }

                    # max length of chars excel accepts
                    $maxLengthExcel = 32767;

                    if (is_string($formattedField) && strlen($formattedField) > $maxLengthExcel) {
                        $formattedField = substr($formattedField, 0, $maxLengthExcel);
                    }

                        $row[] = $formattedField;
                    $styles[] = $fieldStyle;
                }
            }


            $writer->addRow(Row::fromValuesWithStyles($row, columnStyles: $styles));
        }

        $writer->close();
    }
}
