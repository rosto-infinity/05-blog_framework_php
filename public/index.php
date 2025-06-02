<?php
require_once __DIR__ . '/../vendor/autoload.php';
$router = require __DIR__ . '/../routes/web.php';
$router->dispatch();
