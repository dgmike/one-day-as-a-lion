<?php

use SlimFacades\Facade;
use Rimelek\I18n\Languages;

class Configuration
{
	use Pattern\Singleton;

	protected $environment;

	public function setup($app, $environment)
	{
		$this->setEnvironment(strtolower($environment));
		$this->setupGlobal($app);

		$method = 'setup' . ucfirst($this->environment);
		$this->{$method}();
	}

	public function setEnvironment($environment)
	{
		$this->environment = $environment;
	}

	public function setupGlobal($app)
	{
		// initialize the Facade class
		Facade::setFacadeApplication($app);
		Facade::registerAliases();

		// Configs
		$twig = new \Slim\Views\Twig();
		$twig->parserExtensions = array(
		    new \Slim\Views\TwigExtension(),
		    new \Slim\Views\TwigExtraExtension(),
		);

		Config::set('view', $twig);
		Config::set('templates.path', __DIR__ . '/Views');

		// Languages
		Config::set('language', 'pt-br');
		Languages::setGlobalPath(__DIR__ . '/Languages');
		Languages::setGlobalDefault(Config::get('language'));
	}

	public function setupDevelopment()
	{
		$_SERVER['SCRIPT_NAME'] = '/';
		$_SERVER['PATH_INFO'] = __FILE__ . $_SERVER['REQUEST_URI'];

		Config::set('mode', 'debug');
		Config::set('debug', true);

		Config::set('log.enable', true);
		Config::set('log.level', \Slim\Log::DEBUG);

		Strong\Strong::factory(array(
			'name' => 'default',
			'provider' => 'Hashtable',
			'users' => array(
				'michael' => 'michael',
			),
		));
	}

	public function setupProduction()
	{}
}