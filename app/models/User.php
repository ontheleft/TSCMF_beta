<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;
use Zizaco\Entrust\HasRole;

class User extends Eloquent implements UserInterface, RemindableInterface
{

	use UserTrait, RemindableTrait,HasRole;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'users';

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = array('password', 'remember_token');


	public static $admin_config = [
		'title'             => '用户',
		'description'       => '',
		'router'            => 'user',
		'router_controller' => 'UserController',
		'items'             => [
			'id' => [
				'title'     => '序号',
				'type'      => 'hidden',
				'attr'      => 'onlyShow',
			],
			'username' => [
				'title'     => '用户名',
				'type'      => 'text',
				'validator' => 'required'
			],
			'email'    => [
				'title'     => 'Email',
				'type'      => 'text',
				'validator' => 'required|email'
			],
            'mobile'    => [
                'title'     => '联系电话',
                'type'      => 'text',
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
