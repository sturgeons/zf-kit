<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/6/28
 * Time: 19:10
 */

namespace app\lib\exception;


class ParameterException extends BaseException
{
//错误代码
    public $code = 200;
//错误信息
    public $msg = '参数错误';
//状态码
    public $statusCode = 400;
}