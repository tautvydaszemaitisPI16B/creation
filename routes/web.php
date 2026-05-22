<?php

use App\Controllers\IndexController;

/** @var $router */

$router->get('/', [IndexController::class, 'index']);