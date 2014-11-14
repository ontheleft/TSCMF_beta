<?php
/**
 * Created by PhpStorm.
 * User: Ming
 * Date: 2014/11/14
 * Time: 15:28
 */

class ActionController extends BaseController {

    public function getLogin()
    {
        return View::make('Action.login',[]);
    }

    public function getLogout()
    {

    }

    public function postLogin()
    {

    }
} 