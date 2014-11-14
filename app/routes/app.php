<?php
/**
 * Created by PhpStorm.
 * User: Ming
 * Date: 2014/10/31
 * Time: 15:43
 */

Route::get('/','HomeController@getIndex');
Route::controller('/home', 'HomeController');
Route::controller('/action','ActionController');
CrudController::initRouter([
    User::$admin_config,
]);