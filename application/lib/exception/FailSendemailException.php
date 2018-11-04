<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/5
 * Time: 15:42
 */

namespace app\lib\exception;


class FailSendemailException extends BaseException
{
//错误代码
    public $code = 100;
//错误信息
    public $msg = '邮件发送失败！';
//状态码
    public $statusCode = 200;
}