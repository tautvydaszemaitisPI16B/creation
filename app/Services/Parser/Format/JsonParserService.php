<?php

namespace App\Services\Parser\Format;

use App\Interfaces\ParserInterface;

/**
 * JSON file parser service.
 *
 * Reads a JSON file and converts its contents
 * into a multidimensional array structure.
 *
 * Expected JSON format:
 *
 * [
 *     {
 *         "name": "John",
 *         "age": 25
 *     },
 *     {
 *         "name": "Jane",
 *         "age": 30
 *     }
 * ]
 *
 * Result:
 *
 * [
 *     ['name', 'age'],
 *     ['John', '25'],
 *     ['Jane', '30'],
 * ]
 */
class JsonParserService implements ParserInterface
{
    /**
     * Parse a JSON file into an array.
     *
     * The JSON file must contain a non-empty array of objects.
     * The first object's keys are used as table headers.
     *
     * @param string $filePath Absolute or relative path to the JSON file.
     *
     * @throws \RuntimeException Thrown when:
     * - The file cannot be read
     * - JSON is invalid
     * - JSON structure is not a non-empty array
     *
     * @return array<int, array<int, string>>
     */
    public function parse(string $filePath): array
    {
        $content = file_get_contents($filePath);

        if ($content === false) {
            throw new \RuntimeException("Cannot read file: {$filePath}");
        }

        /**
         * Decode JSON into associative array.
         */
        $decoded = json_decode($content, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \RuntimeException(
                'Invalid JSON: ' . json_last_error_msg()
            );
        }

        /**
         * Ensure JSON contains a non-empty array.
         */
        if (!is_array($decoded) || empty($decoded)) {
            throw new \RuntimeException(
                'JSON must be a non-empty array of objects.'
            );
        }

        /**
         * Use first object keys as headers.
         */
        $headers = array_keys($decoded[0]);

        /**
         * Initialize result rows with headers.
         */
        $rows = [$headers];

        /**
         * Convert each object into row values.
         */
        foreach ($decoded as $item) {
            $row = [];

            foreach ($headers as $header) {
                $row[] = (string) ($item[$header] ?? '');
            }

            $rows[] = $row;
        }

        return $rows;
    }
}