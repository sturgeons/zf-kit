<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/8/31
 * Time: 22:49
 */

namespace app\lib\exception;


use Exception;
use think\exception\Handle;
use think\Log;
use think\Request;

class ExceptionHandler extends Handle
{
// 错误代码
    private $code;
//错误信息
    private $msg;
//状态码
    private $statusCode;

    public function render(Exception $e)
    {
        if ($e instanceof BaseException) {
            $this->code = $e->code;
            $this->msg = $e->msg;
            $this->statusCode = $e->statusCode;
        } else {
            if (config('app_debug')) {
                // 如果在调试阶段，方便查看错误信息，利用原有的错误渲染页面
                return parent::render($e);
            } else {
                $this->code = 999;
                $this->msg = '服务器存在致命错误';
                $this->statusCode = 500;
                //日志记录 服务器致命错误
                $this->recordException($e);
            }
        }
        $request = Request::instance();
        $res = [
            'msg' => $this->msg,
            'errorCode' => $this->code,
            'URl' => $request->url()
        ];
        return json($res, $this->statusCode);

    }

    /*
 * 记录错误日志
 */
    private function recordException(Exception $e){
        Log::init([
            'type'=>'File',
            'level'=>['error']
        ]);
        Log::record($e->getMessage(),'error');
    }
}