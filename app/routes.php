<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

$htmlDomain    = Config::get('app.html_domain');
$managerDomain = Config::get('app.manager_domain');
//内网接口
Route::group(array('domain' => $managerDomain), function () {
    include('routes/manager.php');
});

//外网
Route::group(array('domain' => $htmlDomain), function () {
    include('routes/www.php');
});