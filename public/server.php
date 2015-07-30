<?php
set_exception_handler(function (Exception $e) {
    header("Content-Type: application/api-problem+json");
    die(json_encode(array(
        'httpStatus' => $e->getCode(),
        'title' => $e->getMessage()
    )));
});

/**
 * Display all errors when APPLICATION_ENV is development.
 */
if (isset($_SERVER['APPLICATION_ENV']) && $_SERVER['APPLICATION_ENV'] == 'development') {
    error_reporting(E_ALL);
    ini_set("display_errors", 1);
}

chdir(dirname(__DIR__));

// Decline static file requests back to the PHP built-in webserver
if (php_sapi_name() === 'cli-server' && is_file(__DIR__ . parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH))) {
    return false;
}

// Setup auto-loading
require 'init_autoloader.php';

// Run the application!
Zend\Mvc\Application::init(require 'config/application.config.php')->run();