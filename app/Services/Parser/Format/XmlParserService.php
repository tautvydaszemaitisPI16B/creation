<?php

namespace App\Services\Parser\Format;

use App\Interfaces\ParserInterface;

/**
 * XML file parser service.
 *
 * Reads an XML file and converts its contents
 * into a multidimensional array structure.
 *
 * Expected XML format:
 *
 * <users>
 *     <user>
 *         <name>John</name>
 *         <age>25</age>
 *     </user>
 *     <user>
 *         <name>Jane</name>
 *         <age>30</age>
 *     </user>
 * </users>
 *
 * Result:
 *
 * [
 *     ['name', 'age'],
 *     ['John', '25'],
 *     ['Jane', '30'],
 * ]
 */
class XmlParserService implements ParserInterface
{
    /**
     * Parse an XML file into an array.
     *
     * The XML document must contain child elements.
     * The first child element is used to determine headers.
     *
     * @param string $filePath Absolute or relative path to the XML file.
     *
     * @throws \RuntimeException Thrown when:
     * - XML is invalid
     * - XML contains no child elements
     *
     * @return array<int, array<int, string>>
     */
    public function parse(string $filePath): array
    {
        /**
         * Enable internal XML error handling.
         */
        libxml_use_internal_errors(true);

        /**
         * Load XML document.
         */
        $xml = simplexml_load_file($filePath);

        if ($xml === false) {
            $errors = libxml_get_errors();

            libxml_clear_errors();

            $message = !empty($errors)
                ? $errors[0]->message
                : 'Unknown XML error';

            throw new \RuntimeException(
                'Invalid XML: ' . trim($message)
            );
        }

        /**
         * Get root child elements.
         */
        $children = $xml->children();

        if (iterator_count($children) === 0) {
            throw new \RuntimeException(
                'XML document contains no child elements.'
            );
        }

        /**
         * Derive headers from first child element.
         */
        $firstChild = $children[0];

        $headers = [];

        foreach ($firstChild->children() as $field) {
            $headers[] = $field->getName();
        }

        /**
         * Initialize rows with headers.
         */
        $rows = [$headers];

        /**
         * Convert XML items into row arrays.
         */
        foreach ($children as $item) {
            $row = [];

            foreach ($headers as $header) {
                $row[] = (string) ($item->{$header} ?? '');
            }

            $rows[] = $row;
        }

        return $rows;
    }
}