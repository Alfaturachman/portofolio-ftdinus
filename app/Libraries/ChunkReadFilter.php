<?php

namespace App\Libraries;

use PhpOffice\PhpSpreadsheet\Reader\IReadFilter;

class ChunkReadFilter implements IReadFilter
{
    private $startRow = 0;
    private $endRow = 0;

    /**
     * Set the rows to be read
     *
     * @param int $startRow Starting row number
     * @param int $endRow Ending row number
     * @return void
     */
    public function setRows($startRow, $endRow)
    {
        $this->startRow = $startRow;
        $this->endRow = $endRow;
    }

    /**
     * Should this cell be read?
     *
     * @param string $columnAddress Column address (e.g. 'A')
     * @param int $row Row number
     * @param string $worksheetName Optional worksheet name
     * @return bool
     */
    public function readCell($column, $row, $worksheetName = ''): bool
    {
        // Only read the rows we're interested in
        if ($row >= $this->startRow && $row <= $this->endRow) {
            return true;
        }
        return false;
    }
}