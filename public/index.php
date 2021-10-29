<?php

/**
 * Front controller
 *
 * PHP version 7.4
 */

/**
 * Composer Autoloader
 */
require_once dirname(__DIR__) . '/vendor/autoload.php';

/**
 * Error and Exception handling
 */
error_reporting(E_ALL);
//ini_set('display_errors',1); // For Debugging Only
set_error_handler('Core\Error::errorHandler');
set_exception_handler('Core\Error::exceptionHandler');

/** Set timezone and start session */
date_default_timezone_set('America/Los_Angeles');
session_save_path('../sessions');
isset($_SESSION) ? null : session_start();

/**
 * Routing
 */
$router = new Core\Router();

// Add the routes
$router->add('', ['controller' => 'Home', 'action' => 'index']);
$router->add('login', ['controller' => 'Login', 'action' => 'index']);
$router->add('logout', ['controller' => 'Login', 'action' => 'destroy']);
$router->add('{controller}/{action}');
    
$router->dispatch($_SERVER['QUERY_STRING']);
