<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/6/28
 * Time: 19:10
 */

namespace app\lib\exception;


class NoCotainerToVocException extends BaseException
{
//错误代码
    public $code = 201;
//错误信息
    public $msg = '已经没有过多的存储空间用于存放方向盘，改善气味了，请及时将存放时间足够的方向盘取出。';
//状态码
    public $statusCode = 251;
}