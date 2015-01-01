<?php

namespace Pattern;

trait Singleton
{
	static protected $instance;

	final private function __construct()
	{}

	final private function __clone()
	{}

	final static public function getInstance()
	{
		if (!self::$instance) {
			$class = __CLASS__;
			self::$instance = new $class;
		}
		return self::$instance;
	}
}