<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/6/28
 * Time: 19:10
 */

namespace app\lib\exception;


class NoEmailInfoException extends BaseException
{
//错误代码
    public $code = 201;
//错误信息
    public $msg = '没有可用的邮箱地址！';
//状态码
    public $statusCode = 202;
}