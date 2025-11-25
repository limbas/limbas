<?php

namespace Limbas\gtab\export;
use Limbas\gtab\export\filetypes\FiletypeExporter;

/**
 * @brief TableExport
 * @details Class to export a table to different file formats
 */
class TableExport
{
    private int $gtabid;
    private FiletypeExporter $filetypeExporter;
    private array $selectedRecords;

    private ?array $relations;

    /**
     * Construct the TableExport, use export function to output the file
     * @param int $gtabid
     * @param TableExportTypes $exportFileType
     * @param array $selectedRecords
     * @param array|null $relations
     */
    public function __construct(int $gtabid, TableExportTypes $exportFileType, array $selectedRecords = [], ?array $relations = [])
    {
        $this->gtabid = $gtabid;

        $this->filetypeExporter = $exportFileType->exporter();

        $this->relations = $relations;

        $this->selectedRecords = $selectedRecords;
    }

    /**
     * Fetches gresult
     * @return array
     */
    private function getData(): array
    {
        global $filter;
        global $gtab;
        global $gsr;

        $modifiedFilter = $filter;
        $modifiedFilter["getlongval"][$this->gtabid] = 1;
        $modifiedFilter["anzahl"][$this->gtabid] = 'all';

        if ($this->selectedRecords && $tableName = $gtab['table'][$this->gtabid]) {
            $selectedRecords = implode(',', $this->selectedRecords);
            $extension['where'][0] = "($tableName.ID IN ($selectedRecords))";
        } else {
            $extension = null;
        }

        return get_gresult($this->gtabid, 1, $modifiedFilter, $gsr, $this->relations, extension: $extension);
    }

    private function getHeaders(): void
    {
        global $gtab;

        ob_clean();

        $fileName = $gtab["table"][$this->gtabid] . "_" . date("Y-m-d-h-m-s");
        $fileName .= '.' . $this->filetypeExporter->getFileExtension();

        header('Content-Type: ' . $this->filetypeExporter->getMimeType());
        header('Content-Disposition: attachment;filename="' . $fileName . '"');
        header('Cache-Control: max-age=0');
    }

    /**
     * Write export file to output
     * @return void
     */
    public function export(): void
    {
        $gresult = $this->getData();

        $this->getHeaders();

        $this->filetypeExporter->export($gresult, $this->gtabid);

        exit(1);
    }

    public static function useRecordToSelectedRecords(string $useRecord): array
    {
        return $useRecord ? array_map(fn($pair) => (int)explode('_', $pair)[0],explode(';', $useRecord)) : [];
    }
}
