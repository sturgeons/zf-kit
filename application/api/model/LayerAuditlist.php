<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/7
 * Time: 13:14
 */

namespace app\api\model;


class LayerAuditlist extends baseModel
{
//    获取审核列表
    public static function getCheklist($id, $page, $count)
    {
        if ($id == '0') {
            $data = self::paginate($count, false, ['page' => $page]);
        } else {
            $data = self::find($id);
        }
        return $data;
    }

//    获取包含审核项的审核单
    public static function getFullChecklist($id)
    {
        return self::with('item')->find($id);
    }

//    获取审核单适用的区域
    public static function getChecklistArea($id)
    {
        return self::with('area')->find($id);
    }

//    获取审核项
    public function item()
    {
        return $this->hasMany('LayerAuditlistItem', 'pid', 'id');
    }

//    获取审核区域
    public function area()
    {
        return $this->hasManyThrough('LayerArea', 'LayerMatchAreaAuditlist', 'area_id', 'checklist_id');
    }

//    获取区域适用列表
    public function areaAllChecklist()
    {
        return $this->belongsToMany('LayerArea', 'LayerMatchAreaAuditlist', 'area_id', 'checklist_id');
    }
    public static function getAllChecklistByArea(){
        return self::with(['areaAllChecklist'])->select();
    }
}