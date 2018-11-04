<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件

//生成随机字符串
function getRandChar($length)
{
    $str = null;
    $strPol = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz";
    $max = strlen($strPol) - 1;

    for ($i = 0; $i < $length; $i++) {
        $str .= $strPol[rand(0, $max)];
    }
    return $str;
}
//IPL打印机打印标签
function prinIPLlable($ip,$data){
     $port=9100;
     $socket=socket_create(AF_INET,SOCK_STREAM,SOL_TCP)or die("fail lto create socket");
     $res=socket_connect($socket,$ip,$port) or die("fail to connect");
     echo  $res;
     $sendRes=socket_write($socket,$data,strlen($data));
     socket_close($socket);

}
//生成包装条码标签
function createPKGcode($pre){
    return $pre.date('ymdhis',time()).getRandChar(1);
}