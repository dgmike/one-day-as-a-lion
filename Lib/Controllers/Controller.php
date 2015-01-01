<?php

namespace Controllers;

trait Controller
{
	static protected function template($templateName)
	{
		$className = str_replace(__NAMESPACE__, '', __CLASS__);
		$pathClassName = str_replace('\\', DIRECTORY_SEPARATOR, $className);
		$namespacePath = trim($pathClassName, '\\');
		return $namespacePath . DIRECTORY_SEPARATOR . $templateName . '.twig';
	}
}