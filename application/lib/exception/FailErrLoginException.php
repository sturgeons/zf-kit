<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/5
 * Time: 15:42
 */

namespace app\lib\exception;


class FailErrLoginException extends BaseException
{
//错误代码
    public $code = 102;
//错误信息
    public $msg = '账号或密码错误';
//状态码
    public $statusCode = 200;
}