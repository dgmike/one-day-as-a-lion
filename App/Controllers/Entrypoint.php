<?php

namespace Controllers;
use SlimFacades\View;
use Strong\Strong;

class Entrypoint
{
	use Controller;

	public static function login()
	{
		$logged = Strong::getInstance()->loggedIn();
		if ($logged) {
			// redirect
		}
		View::display(self::template('login'));
	}
}