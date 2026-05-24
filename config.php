<?php

use App\Services\Parser\Format\CsvParserService;
use App\Services\Parser\Format\JsonParserService;
use App\Services\Parser\Format\XmlParserService;

/**
 * Application configuration file.
 *
 * This file contains:
 * - Parser service mappings
 * - File upload configuration
 *
 * Returned configuration is loaded globally by the application.
 *
 * @return array{
 *     parsers: array<string, class-string>,
 *     upload: array{
 *         max_size_bytes: int,
 *         upload_dir: string
 *     }
 * }
 */
return [

    /*
    |--------------------------------------------------------------------------
    | File Parser Services
    |--------------------------------------------------------------------------
    |
    | Maps file extensions to parser service classes.
    | The application resolves the correct parser dynamically
    | based on the uploaded file type.
    |
    */

    'parsers' => [

        /**
         * CSV file parser.
         */
        'csv' => CsvParserService::class,

        /**
         * XML file parser.
         */
        'xml' => XmlParserService::class,

        /**
         * JSON file parser.
         */
        'json' => JsonParserService::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | File Upload Settings
    |--------------------------------------------------------------------------
    |
    | max_size_bytes:
    |     Maximum allowed upload size in bytes.
    |
    | upload_dir:
    |     Directory where uploaded files are stored.
    |
    */

    'upload' => [

        /**
         * Maximum upload size (2 MB).
         */
        'max_size_bytes' => 2 * 1024 * 1024,

        /**
         * Upload storage directory.
         */
        'upload_dir' => __DIR__ . '/public/uploads/',
    ],

];