<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/2
 * Time: 2:01
 */

namespace app\api\model;

use app\api\service\wordAnalysis;



class Test extends baseModel
{
    public function test()
    {
        $str = '安全带生产线边有许多纸屑。我们需要制作一个新的废品收集车用来装没用的纸箱';
        $ana=new wordAnalysis();
        $res=$ana->analysis($str);
        return $res;
    }
}