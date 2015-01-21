<?php

namespace Controllers;
use SlimFacades\Response;
use Strong\Strong;

class Secure
{
    static public function secureRoute()
	{
		if (!Strong::protect()) {
			Response::redirect('/logout');
		}
	}
}