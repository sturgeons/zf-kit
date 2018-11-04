<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/7
 * Time: 13:13
 */

namespace app\api\model;


class LayerArea extends baseModel
{
    public function  checkList(){
        return $this->belongsToMany('LayerAuditlist','LayerMatchAreaAuditlist','checklist_id','area_id')->where('active','=','1');
    }
    public static function getChecklistList($id){
        return self::with(['checkLIst'])
            ->find($id);
    }
//    获取审核列表
    public static function getAreaList($page,$count)
    {
         return self::paginate($count, false, ['page' => $page]);

    }

//    获取有效审核区域清单
    public static function getActiveAreaList()
    {
        return self::where('active', '=', '1')->select();
    }
}