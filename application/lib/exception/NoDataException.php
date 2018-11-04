<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/6/28
 * Time: 19:10
 */

namespace app\lib\exception;


class NoDataException extends BaseException
{
//错误代码
    public $code = 201;
//错误信息
    public $msg = '你所查询的数据为空，请确定传递的信息是否正确，O(∩_∩)O谢谢';
//状态码
    public $statusCode = 202;
}