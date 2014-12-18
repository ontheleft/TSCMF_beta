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
        'title'             => '角色',
        'description'       => '用户角色',
        'router'            => 'role',
        'router_controller' => 'RoleController',
        'items'             => [
            'id' => [
                'title'     => '序号',
                'type'      => 'int',
                'attr'      => 'onlyShow',
            ],
            'name' => [
                'title'     => '角色标识',
                'type'      => 'string',
                'validator' => 'required'
            ],
            'display_name'  => [
                'title'     => '角色名称',
                'type'      => 'string',
                'validator' => 'required'
            ],
            'remark'    => [
                'title'     => '角色备注',
                'type'      => 'text',
                'validator' => 'required'
            ],
        ],
    ];
}