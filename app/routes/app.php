<?php
/**
 * Created by PhpStorm.
 * User: Ming
 * Date: 2014/10/31
 * Time: 15:43
 */

Route::get('/', function()
{
    return View::make('hello');
});

Route::controller('/app', 'HomeController');