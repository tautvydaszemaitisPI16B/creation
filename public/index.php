<?php

require_once __DIR__ . '/../bootstrap.php';

$app = new App\Core\App();

$router = $app->router;

require_once __DIR__ . '/../routes/web.php';

$app->run();