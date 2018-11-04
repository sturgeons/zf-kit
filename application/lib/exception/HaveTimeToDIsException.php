<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/6/28
 * Time: 19:10
 */

namespace app\lib\exception;


class HaveTimeToDIsException extends BaseException
{
//错误代码
    public $code = 201;
//错误信息
    public $msg = '方向盘还没有带到规定的存放时间。';
//状态码
    public $statusCode = 200;
}