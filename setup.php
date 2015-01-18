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

$monthYearConditions = array(
	'year'  => '[123]\d{3}',
	'month' => '(0[1-9]|1[012])',
);

// routing
Route::get('/',       'Controllers\\Entrypoint:login')->name('root');
Route::post('/login', 'Controllers\\Entrypoint:loginAction')->name('login');
Route::get('/:year-:month', 'Controllers\\Main:index')->conditions($monthYearConditions)->name('main');
Route::post('/:year-:month', 'Controllers\\Main:create')->conditions($monthYearConditions);
Route::get('/logout', 'Controllers\\Entrypoint:logout')->name('logout');

App::run();
