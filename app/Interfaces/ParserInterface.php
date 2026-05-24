<?php

namespace App\Interfaces;

/**
 * Contract every file parser must fulfil.
 *
 * Each implementation receives a file path and returns a uniform
 * two-dimensional array:
 *   - First element : array of column header strings
 *   - Remaining elements : arrays of row values (same order as headers)
 *
 * Example return value:
 * [
 *     ['first_name', 'age', 'gender'],
 *     ['Kiestis', '29', 'male'],
 *     ['Vytska',  '32', 'male'],
 * ]
 */
interface ParserInterface
{
    /**
     * Parse the file at the given path and return rows.
     *
     * @param  string               $filePath Absolute path to the uploaded file
     * @return array<int, array<int, string>>
     *
     * @throws \RuntimeException When the file cannot be parsed
     */
    public function parse(string $filePath): array;
}
