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
        if (Auth::check()) {
            return Redirect::to('/');
        }

        return View::make('Action.login', array(
            'title' => '登录TSCMF管理平台',
        ));

    }

    public function getLogout()
    {
        Auth::logout();

        return Redirect::to(URL::action('ActionController@getLogin()'));
    }

    public function postLogin()
    {
        if (Auth::attempt(array('username' => Input::get('username'), 'password' => Input::get('password')), Input::get('remember', 'off') == 'on' ? true : false)) {
            return Redirect::intended('/');

        } else {
            return Redirect::to(URL::action('ActionController@getLogin()'));
        }
    }

} 