<?php

use App\Controllers\IndexController;
use App\Controllers\UploadController;

/** @var $router */

$router->get('/', [IndexController::class, 'index']);
$router->post('/upload', [UploadController::class, 'upload']);