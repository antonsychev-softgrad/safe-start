<?php
/**
 * This makes our life easier when dealing with paths. Everything is relative
 * to the application root now.
 */

/**
 *  Nginx environment settings
 */

defined('APP_ENV')
|| define('APP_ENV', 'prod');

defined('APP_DEBUG')
|| define('APP_DEBUG', false);

defined('APP_RESQUE')
|| define('APP_RESQUE', true);

defined('APP_LOGS')
|| define('APP_LOGS', true);

defined('APP_CACHE')
|| define('APP_CACHE', false);


if (APP_DEBUG) {
    ini_set('display_errors', true);
    error_reporting(E_ALL | E_STRICT);
}

chdir(dirname(__DIR__));

// Decline static file requests back to the PHP built-in webserver
if (php_sapi_name() === 'cli-server' && is_file(__DIR__ . parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH))) {
    return false;
}

// Setup autoloading
require 'init_autoloader.php';

// Run the application!
Zend\Mvc\Application::init(require 'config/application.config.php')->run();
