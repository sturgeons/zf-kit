<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/7
 * Time: 13:17
 */

namespace app\api\model;


class LayerPlan extends baseModel
{
//或审核员信息
    public function user()
    {
        return $this->hasOne('User', 'id', 'auditor_id');
    }

//获取审核区域
    public function area()
    {
        return $this->hasOne('LayerArea', 'id', 'area_id');
    }

//获取关于审核计划的照片
    public function picture()
    {
        return $this->hasMany('LayerPicture', 'plan_id', 'id');
    }

//获取审核单问题项
    public function checklistItem()
    {
        return $this->hasMany('LayerAuditlistItem', 'pid', 'checklist_id');
    }

//    获取审核单信息
    public function checklist()
    {
        return $this->hasOne('LayerAuditlist', 'id', 'checklist_id');
    }

//    管理问题
    public function question()
    {
        return $this->hasMany('LayerQuestion', 'plan_id', 'id');
    }


//获取审核计划
    public static function getPlan($id, $page, $count)
    {
        if ($id == '0') {
            $data = self:: with(['user', 'area', 'checklist'])
                ->order('plan_date desc')
                ->paginate($count, false, ['page' => $page]);
        } else {
            $data = self::with(['user', 'area', 'checklist'])
                ->where($id)
                ->paginate($count, false, ['page' => $page]);
        }
        return $data;
    }

//根据月份获取计划
    public static function getPlanByMonth($month)
    {
        $where = 'TO_DAYS(plan_date)- TO_DAYS("' . $month . '") BETWEEN -7 AND 37';
        return self::where($where)
            ->with(['user', 'area'])
            ->select();
    }

//    获取审核完的信息
    public static function getFinishAudit($id)
    {
        $data = self::with(['checklistItem', 'area', 'user', 'checklist', 'question','picture'])
            ->find($id);
        return $data;
    }

//    获取用户的分层审核计划
    public static function getUserPlan($uid, $page = 1, $count = 100)
    {
        $data = self::where('auditor_id', $uid)
            ->where('status', '=', 0)
            ->with(['checklistItem', 'question', 'area', 'user', 'checklist'])
            ->order('plan_date desc')
            ->paginate($count, false, ['page' => $page]);
        return $data;
    }

    //    获取完整的审核计划包括审核单
    public static function getAllInfo($id)
    {
        $data = self::where('id', $id)
            ->where('status', '=', 0)
            ->with(['checklistItem', 'question', 'area', 'user', 'checklist', 'picture'])
            ->order('plan_date desc')
            ->find();
        return $data;
    }

//    获取未完成审核的数量
    public static function getnoCompleteCount($id)
    {
        $count = self::where('auditor_id', '=', $id)
            ->where('status', '=', 0)
            ->where('plan_date', "<=", date('Y-m-d', time()))
            ->count();
        return $count;
    }

//    获取计划汇总数量
    public static function getCountPlan()
    {
        $where = 'layer_plan.plan_date<NOW()';
        return self::field('concat(YEAR(layer_plan.plan_date),"-",MONTH(plan_date)) as date,status, COUNT(*) as count')
            ->where($where)
            ->group(' YEAR(layer_plan.plan_date),MONTH(plan_date),status')
            ->order('plan_date')
            ->select();
    }

//    获取个人积分

    public static function getPersonEffective()
    {
        $where = 'layer_plan.plan_date<NOW()';
        return self::field('COUNT(*) as count,user.name,layer_plan.status,AVG(layer_plan.spend_days) as spendDays')
            ->join('user', 'user.id=layer_plan.auditor_id')
            ->where($where)
            ->group(' layer_plan.auditor_id,layer_plan.status')
            ->order('count DESC')
            ->select();
    }

//    获取已审核出的问题清单
    public static function getUserObsere($user_id, $page, $size)
    {
        return self::where("auditor_id", '=', $user_id)
            ->with(['picture', 'question', 'area', 'checklist'])
            ->order('finish_date', 'desc')
            ->paginate($size, false, ['page' => $page]);
    }
}