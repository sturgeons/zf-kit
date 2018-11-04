<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/8/31
 * Time: 22:58
 */

namespace app\lib\exception;


use think\Exception;

class BaseException extends Exception
{
// 错误代码
    public $code;
//错误信息
    public $msg;
//状态码
    public $statusCode;

    public function __construct($params=[])
    {
        if(!is_array($params)){
            return;
        }
        if(array_key_exists('code',$params)){
            $this->code = $params['code'];
        }
        if(array_key_exists('msg',$params)){
            $this->msg = $params['msg'];
        }
        if(array_key_exists('statusCode',$params)){
            $this->statusCode = $params['statusCode'];
        }
    }
}