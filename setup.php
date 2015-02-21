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

require __DIR__ . '/App/Routes.php';
