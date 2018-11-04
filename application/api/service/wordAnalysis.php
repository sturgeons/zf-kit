<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/2
 * Time: 21:44
 */

namespace app\api\service;
use think\Loader;

Loader::import('phpanalysis.phpanalysis', EXTEND_PATH, '.class.php');

class wordAnalysis
{
    public function analysis($str)
    {
        //初始化类
        \PhpAnalysis::$loadInit = false;
        $pa = new \PhpAnalysis('utf-8', 'utf-8', true);
        //载入词典
        $pa->LoadDict();
        $pa->SetSource($str);
        //多元切分
        $pa->differMax = true;
        //新词识别
        $pa->unitWord = false;
        $pa->StartAnalysis(false);
//        $res= $pa->GetFinallyResult(' ',false);
        $res = $pa->GetFinallyKeywords(5);
//        $res=$pa->GetSimpleResult();
        return $res;
    }
}