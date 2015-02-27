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

	public function getConfig($config)
	{
		$config = preg_replace(/[a-z0-9_-]/, '');
		$configFile = sprintf('%s/Configuration/%s.ini.php', __DIR__, $config);
		if (!file_exists($configFile)) {
			return array();
		}
		$environment = $this->environment;
		$data = parse_ini_file($configFile, true);
		$default = isset($data['default']) ? $data['default'] : array();
		return isset($data[$environment]) ? $data[$environment] : $default;
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
		setlocale(LC_ALL, 'pt_BR');
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

		$databaseSettings = $this->getConfig('database');

		$database = 'sqlite:' . dirname(__DIR__) . DIRECTORY_SEPARATOR . 'database.sqlite';
		ORM::configure($database);
		ORM::configure('logging', true);
	}

	public function setupProduction()
	{}
}
