<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/28
 * Time: 11:27
 */

namespace app\api\model;


class pkgInfo extends  baseModel
{
//获取当前packid下的所有零件号
public  function  getAllsn(){
    return $this->hasMany('PkgHistory','packid','lastlable');
}
//获取带当前packid下的额所有信息表
public static function getAllinfo($type){
    return self::where('type','=',$type)->with(['getAllsn'])->find();
}
}