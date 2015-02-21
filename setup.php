<?php

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
$secureRoute = 'Controllers\\Secure::secureRoute';

Route::get('/',       'Controllers\\Entrypoint:login')->name('root');
Route::post('/login', 'Controllers\\Entrypoint:loginAction')->name('login');

Route::get('/:year-:month',    $secureRoute, 'Controllers\\Main:index')->conditions($monthYearConditions)->name('main');
Route::post('/:year-:month',   $secureRoute, 'Controllers\\Main:create')->conditions($monthYearConditions);
Route::put('/:year-:month',    $secureRoute, 'Controllers\\Main:commit')->conditions($monthYearConditions);
Route::patch('/:year-:month',  $secureRoute, 'Controllers\\Main:update')->conditions($monthYearConditions);
Route::delete('/:year-:month', $secureRoute, 'Controllers\\Main:remove')->conditions($monthYearConditions);

Route::post('/import',   $secureRoute, 'Controllers\\Main:import')->conditions($monthYearConditions)->name('import');

Route::get('/logout', 'Controllers\\Entrypoint:logout')->name('logout');
