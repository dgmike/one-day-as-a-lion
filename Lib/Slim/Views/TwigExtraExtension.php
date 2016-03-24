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
            new \Twig_SimpleFunction('translations', array($this, 'translations')),
            new \Twig_SimpleFunction('t', array($this, 'translate')),
            new \Twig_SimpleFunction('money', array($this, 'money')),
            new \Twig_SimpleFunction('panelWidget', array($this, 'panelWidget')),
        );
    }

    public function translations()
    {
				$languageCode = Config::get('language');
				$language = Languages::getInstance();
				return json_encode($language[$languageCode]);
    }

    public function translate($keyword = '')
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
        return money_format('%n', $amount);
    }

		public function panelWidget($label, $value)
		{
				$widget_title = sprintf('<div class="widget-heading clearfix">%s</div>', $label);
				$widget_body = sprintf('<div class="widget-body clearfix"><div class="number money">%s</div></div>', $value);
				return sprintf('<div class="widget">%s%s</div>', $widget_title, $widget_body);
		}
}
