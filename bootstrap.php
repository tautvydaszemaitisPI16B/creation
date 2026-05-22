<?php

require_once __DIR__ . '/Autoloader.php';

$loader = new Autoloader();
$loader->addNamespace('App', __DIR__ . '/app');
$loader->register();