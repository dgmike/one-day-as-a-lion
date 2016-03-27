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
            new \Twig_SimpleFunction('loginBackground', array($this, 'loginBackground')),
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

    public function loginBackground()
    {
        $backgrounds = [
            ['https://c1.staticflickr.com/5/4068/4640877836_870454aa38_b_d.jpg', 'https://www.flickr.com/photos/48283115@N03/4640877836/sizes/l'],
            ['https://c2.staticflickr.com/4/3405/3625041997_259faacb32_b_d.jpg', 'https://www.flickr.com/photos/vlima/3625041997/sizes/l'],
            ['https://c2.staticflickr.com/8/7456/11239843786_1495275a30_h_d.jpg', 'https://www.flickr.com/photos/rodrigowblum/11239843786/sizes/l'],
            ['https://c1.staticflickr.com/7/6214/6352564795_63a012fcfc_b_d.jpg', 'https://www.flickr.com/photos/rogerplachinski/6352564795/sizes/l']
        ];
        shuffle($backgrounds);
        return sprintf('<img class="background" src="%s" title="" alt="Flickr" />', $backgrounds[0][0]);
    }
}
