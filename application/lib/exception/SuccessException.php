<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/5
 * Time: 15:42
 */

namespace app\lib\exception;


class SuccessException extends BaseException
{
//错误代码
    public $code = 200;
//错误信息
    public $msg = 'Success. O(∩_∩)O';
//状态码
    public $statusCode = 200;
}