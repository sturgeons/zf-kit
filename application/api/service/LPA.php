<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/7
 * Time: 17:25
 */

namespace app\api\service;


use app\api\model\LayerAuditlist;
use app\api\model\LayerAuditlistItem;
use app\api\model\LayerPicture;
use app\api\model\LayerPlan;
use app\api\model\LayerQuestion;
use app\api\model\UserLayer;
use app\lib\exception\NoDataException;
use think\Db;
use think\Exception;

class LPA
{
//添加分层审核计划
    public function add($auditor,$area,$checklist,$planDate)
    {
        $plan = new LayerPlan();
        $plan->auditor_id = $auditor;
        $plan->area_id = $area;
        $plan->checklist_id = $checklist;
        $plan->plan_date = $planDate;
        $plan->status = 0;
        $plan->save();
        return $plan;
    }

//    通知审核员完成审核计划
    public function notify($email, $id)
    {
        $name = '';
        $user = \app\api\model\User::find($id);
        $name = $user->name;
        $count = LayerPlan::getnoCompleteCount($id);
        $context = '<p style="color: #9d9d9d; font-weight: 700;">' . $name . ',你好：</p>';
        $context .= '<div style="margin-left: 30px"><p style="color: #9d9d9d; font-weight: 700;">  你有<strong style="color: #ff0000">' . $count . '</strong>个分层审核需要处理，请及时登录系统完成。</p>';
        $context .= '<p style="color: #9d9d9d; font-weight: 700;">  分层审核网址<a href="http://10.223.9.53">登录</a></p>';
        $context .= '<p style="color: #9d9d9d; font-weight: 700;">  如果你对审核流程不了解，请联系分层审核管理员：<a href="mailto:li.qin@zf.com">秦丽</a></p></div>';
        $res = self::LPAemail($email, $context);
        return $res;
    }

//    发送分层审核邮件
    public function LPAemail($email, $context)
    {
        $mail = new sendEmail();
        $ok = $mail->sendMail($email, '', '分层审核通知', $context);
        return $ok;
    }

//    收集审核问题项
    public function assembleQuestion($planId, $checklistId, $data)
    {
        $res = null;
        $saveData = [];
        foreach ($data as $i) {
            $saveData[] = [
                'plan_id' => $planId,
                'audit_id' => $i['id'],
                'checklist_id' => $checklistId,
                'question' => $i['question']
            ];
        }
        Db::startTrans();
        try {
            $planUpdate = new LayerPlan();
            $res = $planUpdate->find($planId);

            if ($res->status == 0) {
                $question = new LayerQuestion();
                $question->saveAll($saveData);
                $spendDays = (strtotime(date('Y-m-d', time())) - strtotime($res->plan_date)) / 86400;
                $res = LayerPlan::update([
                    'spend_days' => $spendDays,
                    'finish_date' => date('Y-m-d', time()),
                    'status' => 1
                ], ['id' => $planId]);
            }
            Db::commit();

        } catch (Exception $e) {
            Db::rollback();
        }
        return $res;
    }

//    添加审核单
    public function addChecklist($name, $data, $pid)
    {
        $save = [];
        Db::startTrans();
        try {
            $listIndex = new LayerAuditlist([
                'create_date'=>date('Y-m-d',time()),
                'aditcode'=>getRandChar(3),
                'name' => $name,
                'pid' => $pid]);
            $listIndex->save();

            foreach ($data as $i) {
                $save[] = [
                    'pid' => $listIndex->id,
                    'point' => $i['point'],
                    'checklist' => $i['checklist'],
                    'class' => $i['class'],
                    'methods' => $i['methods'],
                    'point_en' => $i['point_en'],
                    'checklist_en' => $i['checklist_en'],
                    'class_en' => $i['class_en'],
                    'methods_en' => $i['methods_en']
                ];
            }
            $layerChecklistItem = new LayerAuditlistItem();
            $layerChecklistItem->saveAll($save);
            Db::commit();
            return $layerChecklistItem;
        } catch (Exception $e) {
            Db::rollback();
        }
        throw new NoDataException();

    }

//添加审核员
    public function addAdutior($code, $pid, $layer)
    {
        $layerUser = UserLayer::getUserByCode($code);
        if ($layerUser) {
            $res = UserLayer::update([
                'code_id' => $code,
                'layer' => $layer,
                'pid' => $pid,
            ], ['code_id' => $code]);
        } else {
            $res = new UserLayer([
                'code_id' => $code,
                'layer' => $layer,
                'pid' => $pid
            ]);
            $res->save();
        }
        return $res;
    }

//    存储问题照片
    public function savePic($planId,$point)
    {
        $file = request()->file('file');
        if (!$file){
            throw new NoDataException();
        }
        $path=ROOT_PATH . 'public' . DS . 'LPA';
        $info = $file->move(ROOT_PATH . 'public' . DS . 'LPA');
        if ($info) {
            $path = str_replace('\\','/',$info->getSaveName()) ;
            $pic = new LayerPicture();
            $pic->path = $path ;
            $pic->plan_id = $planId ;
            $pic->point = $point ;
            $pic->save();
            $url = config('setting.img_lpa_prefix') . $path;
            $data = [
                'point'=>$point,
                'path' => $url,
                'id' => $pic->id
            ];
            return $data;
        } else {
            return $file->getError();
        }
    }
}