<?php

namespace App\Controllers;

use App\Core\Request;
use App\Services\ConfigService;
use App\Services\FileService;
use App\Services\Parser\ParserFactoryService;
use App\Services\Table\TableViewService;
use App\Services\Validator\FileValidatorService;

class UploadController
{
    public function upload()
    {
        header('Content-Type: application/json');

        if (!isset($_FILES['file'])) {
            http_response_code(400);
            echo json_encode(['error' => 'No file uploaded']);
            return;
        }

        $file = $_FILES['file'] ?? [];

        $config     = new ConfigService();
        $validator  = new FileValidatorService($config);
        $factory    = new ParserFactoryService($config);
        $view       = new TableViewService();
        $controller = new FileService($config, $validator, $factory, $view);
        $result   = $controller->handle($file);
        echo json_encode($result);
        exit;
    }
}