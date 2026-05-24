<?php

namespace App\Services\Parser\Format;

use App\Interfaces\ParserInterface;

/**
 * CSV file parser service.
 *
 * Reads a CSV file line by line and converts
 * its contents into a multidimensional array.
 *
 * Example:
 * name,age
 * John,25
 *
 * Result:
 * [
 *     ['name', 'age'],
 *     ['John', '25'],
 * ]
 */
class CsvParserService implements ParserInterface
{
    /**
     * Parse a CSV file into an array.
     *
     * Reads the file line by line, removes empty lines,
     * splits columns by comma, and trims surrounding
     * whitespace and single quotes from each value.
     *
     * @param string $filePath Absolute or relative path to the CSV file.
     *
     * @throws \RuntimeException Thrown when the file cannot be opened.
     *
     * @return array<int, array<int, string>>
     */
    public function parse(string $filePath): array
    {
        $handle = @fopen($filePath, 'r');

        if ($handle === false) {
            throw new \RuntimeException("Cannot open file: {$filePath}");
        }

        $rows = [];

        while (($row = fgetcsv($handle, 0, ',', '"', '\\')) !== false) {
            if ($row === [null] || $row === false) {
                continue;
            }

            $rows[] = array_map(
                static fn ($cell) => is_string($cell) ? trim($cell, " \t\n\r\0\x0B'") : '',
                $row
            );
        }

        fclose($handle);

        return $rows;
    }
}