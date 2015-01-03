<?php

namespace Controllers;

use SlimFacades\Input;
use SlimFacades\View;

class Main
{
	use Controller;

	static public function index($year, $month)
	{
		View::display(self::template('index'));
	}
}