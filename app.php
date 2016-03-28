<?php

if (preg_match('/^\/(public\/.*|favicon\.ico)$/', $_SERVER["REQUEST_URI"])) {
	return false;    // serve the requested resource as-is.
}

require_once __DIR__ . '/vendor/autoload.php';

use SlimFacades\App;

require __DIR__ . '/setup.php';

App::run();
