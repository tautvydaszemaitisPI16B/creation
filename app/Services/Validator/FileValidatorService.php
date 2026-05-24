<?php

namespace App\Services\Validator;

use App\Services\ConfigService;

/**
 * Validates an uploaded file before parsing.
 *
 * Checks:
 *  - PHP upload error codes
 *  - Non-empty file size
 *  - File size within configured limit
 *  - Extension is among supported parsers
 */
class FileValidatorService
{
    /**
     * Human-readable labels for PHP upload error codes.
     *
     * @var array<int, string>
     */
    private const UPLOAD_ERRORS = [
        UPLOAD_ERR_INI_SIZE   => 'File exceeds the server upload limit.',
        UPLOAD_ERR_FORM_SIZE  => 'File exceeds the form upload limit.',
        UPLOAD_ERR_PARTIAL    => 'File was only partially uploaded.',
        UPLOAD_ERR_NO_FILE    => 'No file was uploaded.',
        UPLOAD_ERR_NO_TMP_DIR => 'Server temporary directory is missing.',
        UPLOAD_ERR_CANT_WRITE => 'Failed to write the file to disk.',
        UPLOAD_ERR_EXTENSION  => 'A PHP extension blocked the upload.',
    ];

    /**
     * @param ConfigService $config Application configuration
     */
    public function __construct(private readonly ConfigService $config)
    {
    }

    /**
     * Validate the uploaded file array from $_FILES.
     *
     * @param  array<string, mixed> $file Single entry from $_FILES (e.g. $_FILES['file'])
     * @return array<int, string>   List of error messages; empty on success
     */
    public function validate(array $file): array
    {
        $errors = [];

        $uploadError = (int) ($file['error'] ?? UPLOAD_ERR_NO_FILE);

        if ($uploadError !== UPLOAD_ERR_OK) {
            $errors[] = self::UPLOAD_ERRORS[$uploadError]
                ?? "Upload failed with error code {$uploadError}.";
            return $errors;
        }

        $size = (int) ($file['size'] ?? 0);

        if ($size === 0) {
            $errors[] = 'The uploaded file is empty.';
        }

        $maxSize = (int) $this->config->get('upload.max_size_bytes', 2097152);

        if ($size > $maxSize) {
            $maxMb    = round($maxSize / 1048576, 1);
            $errors[] = "File is too large. Maximum allowed size is {$maxMb} MB.";
        }

        $name = (string) ($file['name'] ?? '');
        $extension = strtolower(pathinfo($name, PATHINFO_EXTENSION));

        $parsers = $this->config->getParsers();

        $supportedExtensions = array_map(
            fn($ext) => strtolower($ext),
            array_keys($parsers)
        );

        if (!in_array($extension, $supportedExtensions, true)) {
            $errors[] = sprintf(
                'Unsupported file type "%s". Allowed: %s.',
                $extension,
                implode(', ', $supportedExtensions)
            );
        }

        return $errors;
    }
}
