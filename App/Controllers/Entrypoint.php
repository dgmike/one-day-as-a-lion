<?php

namespace Controllers;

use SlimFacades\App;
use SlimFacades\View;
use SlimFacades\Response;
use SlimFacades\Input;

use Strong\Strong;

class Entrypoint
{
    use Controller;

    static public function login()
    {
        $logged = Strong::getInstance()->loggedIn();
        if ($logged) {
            Response::redirect('/' . date('Y-m'));
            return;
        }
        View::display(self::template('login'));
    }

    static public function loginAction()
    {
        $username = preg_replace('/[^a-z0-9à-ú !=@#$%*(){}`´^~_-]/i', '', Input::post('username'));
        $password = preg_replace('/[^a-z0-9à-ú !=@#$%*(){}`´^~_-]/i', '', Input::post('password'));

        if (!Strong::getInstance()->login($username, $password)) {
            App::flash('loginerror', true);
        }
        Response::redirect('/', 301);
    }

    static public function logout()
    {
        Strong::getInstance()->logout(true);
        Response::redirect('/', 301);
    }
}