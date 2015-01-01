<?php

namespace Controllers;
use SlimFacades\App;
use SlimFacades\View;
use SlimFacades\Response;
use Strong\Strong;

class Entrypoint
{
	use Controller;

	static public function login()
	{
		$logged = Strong::getInstance()->loggedIn();
		if ($logged) {
			Response::redirect('/');
		}
		View::display(self::template('login'));
	}

	static public function loginAction()
	{
		App::flash('loginerror', true);
		Response::redirect('/');
	}
}