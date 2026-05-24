<?php

namespace App\Services;

use App\Services\ConfigService;
use App\Services\Parser\ParserFactoryService;
use App\Services\Validator\FileValidatorService;
use App\Services\Table\TableViewService;

/**
 * Front controller.
 *
 * Orchestrates the upload pipeline:
 *   1. Validate the uploaded file
 *   2. Move it to the upload directory
 *   3. Select and run the correct parser
 *   4. Render the result as an HTML table
 *   5. Clean up the temporary file
 */
class FileService
{
    /**
     * @param ConfigService        $config    Application configuration
     * @param FileValidatorService $validator Upload validator
     * @param ParserFactoryService $factory   Parser factory
     * @param TableViewService     $view      Table renderer
     */
    public function __construct(
        private readonly ConfigService        $config,
        private readonly FileValidatorService $validator,
        private readonly ParserFactoryService $factory,
        private readonly TableViewService     $view
    ) {
    }

    /**
     * Handle an HTTP POST upload request.
     *
     * @param  array<string, mixed> $fileData Entry from $_FILES (e.g. $_FILES['file'])
     * @return array{errors: array<int,string>, table: string, filename: string}
     */
    public function handle(array $fileData): array
    {
        $result = [
            'errors'   => [],
            'table'    => '',
            'filename' => (string) ($fileData['name'] ?? ''),
        ];

        // Step 1: Validate
        $errors = $this->validator->validate($fileData);

        if (!empty($errors)) {
            $result['errors'] = $errors;
            return $result;
        }

        // Step 2: Move uploaded file to uploads dir
        $uploadDir  = (string) $this->config->get('upload.upload_dir');
        $extension  = strtolower(pathinfo($fileData['name'], PATHINFO_EXTENSION));
        $targetPath = $uploadDir . uniqid('upload_', true) . '.' . $extension;

        if (!move_uploaded_file($fileData['tmp_name'], $targetPath)) {
            $result['errors'][] = 'Failed to save uploaded file. Please try again.';
            return $result;
        }

        // Step 3 & 4: Parse and render
        try {
            $parser        = $this->factory->create($extension);
            $rows          = $parser->parse($targetPath);
            $result['table'] = $this->view->render($rows);
        } catch (\Throwable $e) {
            $result['errors'][] = 'Parse error: ' . $e->getMessage();
        } finally {
            // Step 5: Remove the temporary file from disk
            if (file_exists($targetPath)) {
                unlink($targetPath);
            }
        }

        return $result;
    }
}
