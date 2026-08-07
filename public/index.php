<?php

require dirname(__DIR__) . '/bootstrap/app.php';

/** @var \App\Core\Router $router */
$router->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
