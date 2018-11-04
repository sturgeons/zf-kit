<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/6/28
 * Time: 22:05
 */

namespace app\api\validate;


class UserVali extends BaseValidate
{
    protected $rule = [
        'code' => 'require|isNotEmpty',
        'password' => 'require|isNotEmpty'
    ];

    protected $message = [
        'code' => '账户不存在',
        'password' => '密码不存在'
    ];
}