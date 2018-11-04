<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/7
 * Time: 13:17
 */

namespace app\api\model;


class LayerQuestion extends baseModel
{
    //获取检查单 描述
    public function checkPoint()
    {
        return $this->hasOne('LayerAuditlistItem', 'id', 'checklist_id');
    }

    //获取检查单信息
    public function checklist()
    {
        return $this->hasOne('LayerAuditlist', 'id', 'checklist_id');
    }

    //获取用户信息
    public function getUserinfo()
    {
        return $this->hasOne('User', 'id', 'auditor_id');
    }

    //获取审核计划
    public function getPlan()
    {
        return $this->belongsTo('LayerPlan', 'plan_id', 'id');
    }

    public function getPic()
    {
        return $this->hasMany('LayerPicture', 'plan_id', 'plan_id');
    }

    //获取问题信息
    public static function getDetail($id, $page = 1, $count = 15)
    {
        if ($id == '0') {
            $data = self:: with(['getPic', 'getPlan'])
                ->join('layer_auditlist_item', 'layer_question.audit_id=layer_auditlist_item.id and layer_question.checklist_id=layer_auditlist_item.pid')
                ->order('layer_question.id', 'desc')
                ->paginate($count, false, ['page' => $page]);
        } else {
            $data = self::with(['getPic', 'getPlan'])
                ->join('layer_auditlist_item', 'layer_question.audit_id=layer_auditlist_item.id and layer_question.checklist_id=layer_auditlist_item.pid')
                ->find($id);
        }
        return $data;
    }
//    安卓获取所有的问题审核清单
    //获取问题信息
    public static function getDetailByAndriod($page = 1, $count = 15)
    {
        $data = self:: with(['getPic', 'getPlan'])
            ->field('*,layer_auditlist_item.class as cat')
            ->join('layer_auditlist_item', 'layer_question.audit_id=layer_auditlist_item.id and layer_question.checklist_id=layer_auditlist_item.pid')
            ->order('layer_question.id', 'desc')
            ->paginate($count, false, ['page' => $page]);
        return $data;
    }
    //获取未完成问题信息
    public static function getUncloseDetailByAndriod($page = 1, $count = 15)
    {
        $data = self:: with(['getPic', 'getPlan'])
            ->field('	layer_question.id,layer_question.question,layer_question.plan_id,	layer_question.`commit`,layer_auditlist_item.point,	layer_question.owner,layer_question.due_date,layer_auditlist_item.checklist,layer_auditlist_item.class AS cat,`user`.`name` as userName,	layer_area.area,	layer_auditlist.`name` as checkName')
            ->where('statue','=','0')
            ->join('layer_auditlist_item', 'layer_question.audit_id=layer_auditlist_item.id and layer_question.checklist_id=layer_auditlist_item.pid')
            ->join('layer_plan', 'layer_question.plan_id = layer_plan.id ')
            ->join('`user`', 'layer_plan.auditor_id = `user`.id ')
            ->join('layer_area', 'layer_plan.area_id = layer_area.id ')
            ->join('layer_auditlist ', 'layer_plan.checklist_id = layer_auditlist.id ')
            ->order('layer_question.id', 'desc')
            ->paginate($count, false, ['page' => $page]);
        return $data;
    }
    //获取完成问题信息
    public static function getCloseDetailByAndriod($page = 1, $count = 15)
    {
        $data = self:: with(['getPic', 'getPlan'])
            ->field('	layer_question.id,layer_question.question,layer_question.plan_id,	layer_question.`commit`,layer_auditlist_item.point,	layer_question.owner,layer_question.due_date,layer_auditlist_item.checklist,layer_auditlist_item.class AS cat,`user`.`name` as userName,	layer_area.area,	layer_auditlist.`name` as checkName')
            ->where('statue','=','1')
            ->join('layer_auditlist_item', 'layer_question.audit_id=layer_auditlist_item.id and layer_question.checklist_id=layer_auditlist_item.pid')
            ->join('layer_plan', 'layer_question.plan_id = layer_plan.id ')
            ->join('`user`', 'layer_plan.auditor_id = `user`.id ')
            ->join('layer_area', 'layer_plan.area_id = layer_area.id ')
            ->join('layer_auditlist ', 'layer_plan.checklist_id = layer_auditlist.id ')
            ->order('layer_question.id', 'desc')
            ->paginate($count, false, ['page' => $page]);
        return $data;
    }

//    获取一年的问题数量统计
    public static function getCount()
    {
        return self::alias('a')
            ->field('concat(year(layer_plan.finish_date),"-",month(layer_plan.finish_date)) as date,count(*) as count')
            ->join('layer_plan', 'layer_question.plan_id=layer_plan.id')
            ->group('year(layer_plan.finish_date),month(layer_plan.finish_date)')
            ->select();
    }

}