<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/6/28
 * Time: 19:10
 */

namespace app\lib\exception;


class PreNoCodeException extends BaseException
{
//错误代码
    public $code = 201;
//错误信息
    public $msg = '你的标签没有经过上序检测，请进行上序检测';
//状态码
    public $statusCode = 253;
}