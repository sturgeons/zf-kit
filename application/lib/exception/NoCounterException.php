<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/6/28
 * Time: 19:10
 */

namespace app\lib\exception;


class NoCounterException extends BaseException
{
//错误代码
    public $code = 201;
//错误信息
    public $msg = '包装下没有产品';
//状态码
    public $statusCode = 255;
}