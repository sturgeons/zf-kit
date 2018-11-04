<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/6/28
 * Time: 19:10
 */

namespace app\lib\exception;


class HaveSameCodeException extends BaseException
{
//错误代码
    public $code = 201;
//错误信息
    public $msg = '标签已经扫描过，请重新检查已经扫描的标签';
//状态码
    public $statusCode = 200;
}