<?php

$MODULE_PATH = __DIR__ . '/src/';

spl_autoload_register(function ($class) use ($MODULE_PATH) {
    $class = implode('/', array_filter(explode('\\', $class)));
    if (file_exists($MODULE_PATH . $class . '.php')) {
        require_once $MODULE_PATH . $class . '.php';
    }
});
