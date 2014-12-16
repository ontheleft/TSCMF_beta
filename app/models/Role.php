<?php
/**
 * Created by PhpStorm.
 * User: Lenovo
 * Date: 2014/12/16
 * Time: 15:59
 */

use Zizaco\Entrust\EntrustRole;
class Role extends EntrustRole{

    public static $admin_config = [
        'title'             => '用户',
        'description'       => '',
        'router'            => 'user',
        'router_controller' => 'UserController',
        'items'             => [
            'id' => [
                'title'     => '序号',
                'type'      => 'int',
            ],
            'username' => [
                'title'     => '用户名',
                'type'      => 'string',
                'validator' => 'required'
            ],
            'email'    => [
                'title'     => 'Email',
                'type'      => 'string',
                'validator' => 'required|email'
            ],
            'mobile'    => [
                'title'     => '联系电话',
                'type'      => 'string',
                'validator' => 'required|mobile'
            ],
            'password' => [
                'title'  => '密码',
                'type'   => 'password',
                'hidden' => true,
            ],
            'avatar'   => [
                'title' => '头像',
                'type'  => 'image',
            ],
        ],
    ];
}