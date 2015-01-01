<?php

namespace Slim\Views;

use SlimFacades\Config;
use Rimelek\I18n\Languages;

class TwigExtraExtension
	extends \Twig_Extension
{
    public function getName()
    {
        return 'TwigExtraExtension';
    }

    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('t', array($this, 'translate')),
        );
    }

    public function translate($keyword = '', array $params = array())
    {
        $languageCode = Config::get('language');
    	$language = Languages::getInstance();
    	return $language[$languageCode][$keyword];
    }
}