<?php
/**
 * Created by PhpStorm.
 * User: Ming
 * Date: 2014/10/31
 * Time: 15:43
 */

Route::controller('/action','ActionController');
Route::controller('/test','TestController');

Route::group(array('before' => 'auth'), function () {
    Route::get('/','HomeController@getIndex');
    Route::controller('/home', 'HomeController');
    CrudController::initRouter([
        User::$admin_config,
        Role::$admin_config,
    ]);
});