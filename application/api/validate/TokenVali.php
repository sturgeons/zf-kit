<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/6/28
 * Time: 22:05
 */

namespace app\api\validate;


class TokenVali extends BaseValidate
{
    protected $rule = [
        'token' => 'require|isNotEmpty'
    ];

    protected $message = [
        'token' => 'Token不存在'
    ];
}