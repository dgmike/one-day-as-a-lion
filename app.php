<?php

if (preg_match('/^\/public/', $_SERVER["REQUEST_URI"])) {
    return false;    // serve the requested resource as-is.
}

error_reporting(E_ALL);
ini_set('error_reporting', E_ALL);
ini_set('display_errors', true);
ini_set('html_errors', true);

require_once __DIR__ . '/vendor/autoload.php';

$app = new Slim\Slim();

// config
Configuration::getInstance()->setup(
	$app, getEnv('PHP_ENV') ? getEnv('PHP_ENV') : 'development'
);

// routing
Route::get('/',       'Controllers\\Entrypoint:login')->name('root');
Route::post('/login', 'Controllers\\Entrypoint:loginAction')->name('login');

App::run();