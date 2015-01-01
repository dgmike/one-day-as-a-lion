<?php

use SlimFacades\Facade;

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
		Config::set('view', new \Slim\Views\Twig());
		Config::set('templates.path', 'App/Views');
	}

	public function setupDevelopment()
	{
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