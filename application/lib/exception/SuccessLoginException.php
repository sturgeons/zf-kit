<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/5
 * Time: 15:42
 */

namespace app\lib\exception;


class SuccessLoginException extends BaseException
{
//错误代码
    public $code = 100;
//错误信息
    public $msg = 'OK';
//状态码
    public $statusCode = 200;
}