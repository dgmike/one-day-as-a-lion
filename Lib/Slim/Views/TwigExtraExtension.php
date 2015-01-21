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
            new \Twig_SimpleFunction('money', array($this, 'money')),
        );
    }

    public function translate($keyword = '', array $params = array())
    {
        $languageCode = Config::get('language');
    	$language = Languages::getInstance();
    	return $language[$languageCode][$keyword];
    }

    public function money($amount, $realAmount = false)
    {
        if (false === $realAmount) {
            $amount = abs($amount);
        }
        return money_format('%i', $amount);
    }
}