<?php

namespace Limbas\gtab\export\filetypes;

use DateTime;

class CSV extends FiletypeExporter
{
    const FILE_EXTENSION = "csv";
    const MIME_TYPE = "text/csv";
    const CF_TYP_RETURN_TYPE = 5;

    public function export(array $gresult, int $gtabid): void
    {
        global $gfield;
        global $filter;
        global $umgvar;

        $sortKeys = array_keys($gfield[$gtabid]["key"]);

        $outputStream = fopen("php://output", "w");

        $titles = [];

        // Set the column titles in the top row
        foreach ($sortKeys as $fieldId) {
            if (!$gfield[$gtabid]["funcid"][$fieldId] OR $gfield[$gtabid]["col_hide"][$fieldId]) {
                continue;
            }

            $fieldType = $gfield[$gtabid]["field_type"][$fieldId];
            $hideCols = $filter["hidecols"][$gtabid][$fieldId];

            if (!$hideCols && $fieldType < 100 && $fieldType != 20) {
                if($umgvar['export_sys_column_header']) {
                    $titles[] = $gfield[$gtabid]['field_name'][$fieldId];
                }
                else
                {
                    $titles[] = $gfield[$gtabid]['spelling'][$fieldId];
                }
            }
        }

        fputcsv($outputStream, $titles, escape:false);

        $rescount = $gresult[$gtabid]["res_count"];

        // Set the data in the rows
        for ($resultCounter = 0; $resultCounter < $rescount; $resultCounter++) {
            $row = [];
            foreach ($sortKeys as $fieldId) {
                if (!$gfield[$gtabid]["funcid"][$fieldId] OR $gfield[$gtabid]["col_hide"][$fieldId]) {
                    continue;
                }

                $fieldType = $gfield[$gtabid]["field_type"][$fieldId];
                $hideCols = $filter["hidecols"][$gtabid][$fieldId];

                if (!$hideCols && $fieldType < 100 && $fieldType != 20) {
                    $fieldFunctionName = "cftyp_" . $gfield[$gtabid]["funcid"][$fieldId];
                    $formattedField = $fieldFunctionName($resultCounter, $fieldId, $gtabid, $this->getCFTyp_returnType(), $gresult, 0);
                    if (is_array($formattedField)) {
                        if ($fieldType == 11 && is_array($formattedField["value"])) {
                            $formattedField = implode("; ", $formattedField["value"]);
                        }
                        elseif ($fieldType == 19) {
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
                    $row[] = $formattedField;
                }
            }

            fputcsv($outputStream, $row);
        }

        fclose($outputStream);
    }
}
