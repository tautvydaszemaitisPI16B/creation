<?php

namespace App\Services\Table;

/**
 * Renders parsed file data as an HTML table.
 *
 * Expects the two-dimensional array returned by any ParserInterface:
 *   row[0] = header columns
 *   row[1..n] = data rows
 */
class TableViewService
{
    /**
     * Build and return the HTML <table> markup.
     *
     * All output is escaped with htmlspecialchars to prevent XSS.
     *
     * @param  array<int, array<int, string>> $rows Parsed rows (first row = headers)
     * @return string HTML fragment
     */
    public function render(array $rows): string
    {
        if (empty($rows)) {
            return '<p class="no-data">No data found in file.</p>';
        }

        $headers  = array_shift($rows);
        $colCount = count($headers);
        $html     = '<div class="table-wrapper"><table class="data-table">';

        // Header row
        $html .= '<thead><tr>';
        foreach ($headers as $header) {
            $html .= '<th>' . htmlspecialchars($header, ENT_QUOTES, 'UTF-8') . '</th>';
        }
        $html .= '</tr></thead>';

        // Data rows
        $html .= '<tbody>';

        if (empty($rows)) {
            $html .= '<tr><td colspan="' . $colCount . '" class="no-data">No records.</td></tr>';
        } else {
            foreach ($rows as $row) {
                $html .= '<tr>';
                foreach ($row as $cell) {
                    $html .= '<td>' . htmlspecialchars((string) $cell, ENT_QUOTES, 'UTF-8') . '</td>';
                }
                $html .= '</tr>';
            }
        }

        $html .= '</tbody></table></div>';

        return $html;
    }
}
