<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/6/28
 * Time: 19:10
 */

namespace app\lib\exception;


class ErrCodeException extends BaseException
{
//错误代码
    public $code = 201;
//错误信息
    public $msg = '你的标签不是对应的产品，请检查后重新扫描';
//状态码
    public $statusCode = 200;
}