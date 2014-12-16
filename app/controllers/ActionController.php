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
        $password = Hash::make('secret');
        //echo $password;
        if (Auth::check()) {
            return Redirect::to(URL::action('HomeController@getIndex'));
        }

        return View::make('Action.login', array(
            'title' => '登录TSCMF管理平台',
        ));
    }

    public function getLogout()
    {
        Auth::logout();
        return Redirect::to(URL::action('ActionController@getLogin'));
    }

    public function postLogin()
    {
        if (Auth::attempt(array('username' => Input::get('username'), 'password' => Input::get('password')), Input::get('remember', 'off') == 'on' ? true : false)) {
            return Redirect::intended(URL::action('HomeController@getIndex'));
        } else {
            return Redirect::to(URL::action('ActionController@getLogin'));
        }
    }

} 