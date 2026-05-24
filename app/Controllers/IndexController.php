<?php

namespace App\Controllers;

use App\Core\View;
use App\Services\ConfigService;

class IndexController
{
    public function index(): string
    {
        $config = new ConfigService();
        $supportedFormats = array_keys($config->getParsers());

        return View::render('index', [
            'title' => 'MVC Framework',
            'message' => 'Hello from controller',
            'supportedFormats' => $supportedFormats,
        ]);
    }
}