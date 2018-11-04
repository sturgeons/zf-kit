<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/7
 * Time: 13:18
 */

namespace app\api\controller\v1;

use app\api\controller\baseController;
use app\api\model\LayerArea;
use app\api\model\LayerAuditlist;
use app\api\model\LayerAuditlistItem;
use app\api\model\LayerMatchAreaAuditlist;
use app\api\model\LayerPicture;
use app\api\model\LayerPlan;
use app\api\model\LayerQuestion;
use app\api\model\UserLayer;
use app\api\service\LPA;
use app\lib\exception\FailException;
use app\lib\exception\NoDataException;
use app\lib\exception\NoEmailInfoException;
use app\lib\exception\SuccessException;
use app\lib\exception\SuccessSendemailException;
use think\Exception;
use think\Request;

class LayerProcessAudit extends baseController
{
    protected $beforeActionList = [
        'isPowerUser' => ['except' => 'getCloseObservations,getUnCloseObservations,updateObservations,savePicture,getUserPlan,getFullChecklist,submit,getUserObserve,observations,getPersonEffective,getAllObservations']
    ];

    public $page = 1;
    public $count = 15;

//检查是否是power user 管理员
    public function isPowerUser()
    {
        $request = Request::instance();
        $token = $request->header('token');
        $userServer = new \app\api\service\User();
        $data = $userServer->isPowerUser($token, 'layer');
        if ($data) {
            return true;
        }
        return false;

    }

//    获取历史观察点数量
    public function observations()
    {
        return LayerQuestion::getCount();
    }

//变更审核区域
    public function getChangeChecklistByArea()
    {
        $id = input('post.id');
        $item = input('post.item');
        $active = input('post.active');
        if ($id == '' || empty($item)) {
            throw  new NoDataException();
        }
        if ($active) {
            LayerMatchAreaAuditlist::where([
                'area_id' => $id,
                'checklist_id' => $item
            ])->delete();
        } else {
            $LayerMachArea = new LayerMatchAreaAuditlist();
            $LayerMachArea->area_id = $id;
            $LayerMachArea->checklist_id = $item;
            $LayerMachArea->save();
        }
        throw new SuccessException();

    }

    public function getFullChecklistByArea()
    {
        return LayerAuditlist::getAllChecklistByArea();
    }

    public function updatePage()
    {
        if (input('?get.page')) {
            $this->page = input('get.page');
        }
        if (input('?get.count')) {
            $this->count = input('get.count');
        }
    }

//    获取审核区域
    public function getArea($id)
    {
        $this->updatePage();
        $data = LayerArea::getAreaList($this->page, $this->count);
        if (!$data) {
            throw new NoDataException();
        }
        return json($data);
    }

//  更新审核区域
    public function updateArea()
    {
        $id = input('post.id');
        $active = input('post.active');
        $area = LayerArea::update(['active' => $active], ['id' => $id]);
        $area->save();
        if ($area) {
            throw new SuccessException();
        }
        throw new FailException();
    }

//    获取有效区域
    public function getActiveArea()
    {
        $data = LayerArea::getActiveAreaList();
        return $data;
    }

//    添加审核区域
    public function addArea()
    {
        if (input('?post.area')) {
            $area = input('post.area');
            $area = new LayerArea(['area' => $area]);
            $area->save();
            return $area;
        }
        throw new NoDataException();
    }

//    更改审核区域状态
    public function changeActiveArea($id)
    {
        $area = LayerArea::update(['active' => input('post.active')], ['id' => $id]);
        return $area;
    }

//    获取审核清单列表
    public function getChecklist($id)
    {
        $this->updatePage();
        $data = LayerAuditlist::getCheklist($id, $this->page, $this->count);
        if (!$data) {
            throw new NoDataException();
        }
        return json($data);
    }

//更新审核清单
    public function updateChecklist()
    {
        $id = input('post.id');
        $active = input('post.active');
        $area = LayerAuditlist::update(['active' => $active], ['id' => $id]);
        $area->save();
        if ($area) {
            throw new SuccessException();
        }
        throw new FailException();
    }

//     添加新审核单
    public function addChecklist()
    {
        $name = input('post.name');
        $pid = input('post.pid');
        $data = input('post.data/a');
        $lpa = new LPA();
        $res = $lpa->addChecklist($name, $data, $pid);
        return $res;
    }

//    获取包含审核项的审核单
    public function getFullChecklist($id)
    {
        $data = LayerPlan::getAllInfo($id);
        if (!$data) {
            throw new NoDataException();
        }
        return json($data);
    }

    //    获取包含审核项的审核单 根据checklist id
    public function checklistById($id)
    {
        $data = LayerAuditlistItem::where('pid', '=', $id)->select();
        if (!$data) {
            throw new NoDataException();
        }
        return json($data);
    }

//    获取审核单适用的区域
    public function getCheckArea($id)
    {
        $data = LayerAuditlist::getChecklistArea($id);
        if (!$data) {
            throw new NoDataException();
        }
        return json($data);
    }

//    获取区域适用的审核单
    public function getChecklistByArea($id)
    {
        $data = LayerArea::getChecklistList($id);
        if (!$data) {
            throw new NoDataException();
        }
        return json($data);
    }

//    获取审核计划信息
    public function getPlan($id)
    {
        $this->updatePage();
        $data = LayerPlan::getPlan($id, $this->page, $this->count);
        if (!$data) {
            throw new NoDataException();
        }
        return json($data);
    }

    //  获取一个月的审核计划信息
    public function getPlanByMonth()
    {
        $month = input('get.month');
        $data = LayerPlan::getPlanByMonth($month);
        if (!$data) {
            throw new NoDataException();
        }
        return json($data);
    }

//    获取当前用户的审核计划
    public function getUserPlan()
    {
        $this->updatePage();
        $uid = \app\api\service\User::getUid();
        $data = LayerPlan::getUserPlan($uid, $this->page, $this->count);
        return $data;
    }

//    获取当前用户的审核的问题清单
    public function getUserObserve($page = 1, $count = 3)
    {
        $uid = \app\api\service\User::getUid();
        $data = LayerPlan::getUserObsere($uid, $page, $count);
        return $data;
    }

//    获取审核员
    public function getUser($id)
    {
        $this->updatePage();
        $data = UserLayer::getUser($id, $this->page, $this->count);
        if (!$data) {
            throw new NoDataException();
        }
        return json($data);
    }

//获取审核问题
    public function getQuestion($id)
    {
        $this->updatePage();
        $data = LayerQuestion::getDetail($id, $this->page, $this->count);
        if (!$data) {
            throw new NoDataException();
        }
        return json($data);
    }

// 获取完成的审核单
    public function getFinishAudit($id)
    {
        $data = LayerPlan::getFinishAudit($id);
        if (!$data) {
            throw new NoDataException();
        }
        return json($data->hidden(['area.id', 'user.id', 'checklist_item.pid', 'auditor_id', 'area.id', 'question.checklist_id', 'checklist.pid', 'checklist.active', 'checklist_id', 'area_id']));
    }

//    添加分层审核计划
    /*{"data":{
    "auditor_id":1,
    "area_id":1,
    "checklist_id":1，
    "plan_date":'2017-09-19'
    }
    }*/
    public function addPlan()
    {
        $auditor = input('post.auditor_id');
        $area = input('post.area_id');
        $checklist_id = input('post.checklist_id');
        $plan_date = input('post.plan_date');
        if ($auditor == '' || $area == '' || $checklist_id == '') {
            throw new NoDataException();
        }

        $lpa = new LPA();
        $res = $lpa->add($auditor, $area, $checklist_id, $plan_date);
        return $res;
    }

//    通知审核员完成计划
    public function notify($id)
    {

        if (!input('?post.email') || !input('?post.id')) {
            throw new NoEmailInfoException();
        }
        $email = input('post.email');
        $id = input('post.id');
        $lpa = new LPA();
        $data = $lpa->notify($email, $id);
        if ($data) {
            throw new SuccessSendemailException();
        }
    }

//    提交完成的审核计划
    public function submit()
    {
        $planId = input('post.plan_id');
        $checklistId = input('post.checklist_id');
        $data = input('post.data/a');
        $lpa = new LPA();
        $res = $lpa->assembleQuestion($planId, $checklistId, $data);
        return $res;
    }

//   上传问题图片
    public function savePicture($planId, $point)
    {
        $file = \request()->file('file');
        $lpa = new LPA();
        $res = $lpa->savePic($planId, $point);
        return $res;
    }

//    获取照片
    public function getPicture($id)
    {
        $data = LayerPicture::getPic($id);
        return $data;
    }

//    获取审核人员清单
    public function addAuditor()
    {
        $code = input('post.code_id');
        $layer = input('post.layer');
        $pid = input('post.pid');
        if ($code == '') {
            throw new FailException();
        }

        $lpa = new LPA();
        $res = $lpa->addAdutior($code, $pid, $layer);
        throw new SuccessException();
    }

//    删除审核员
    public function delAuditor($id)
    {
        $user = UserLayer::get($id);
        if ($user) {
            $user->delete();
            throw new SuccessException();
        } else {
            throw  new FailException();
        }

    }

//    获取审核计划数量
    public function getPlanCount()
    {
        return LayerPlan::getCountPlan();
    }

//    重新审核
    public function reAduit($planid)
    {
        $layerplan = new LayerPlan();
        $layerQuestion = new LayerQuestion();
        $plan = $layerplan->save(['status' => 0], ['id' => $planid]);
        $question = $layerQuestion->where('plan_id', '=', $planid)
            ->delete();
        LayerPicture::where('plan_id', '=', $planid)->delete();
        return new SuccessException();

    }

//    个人绩效
    public function getPersonEffective()
    {
        return LayerPlan::getPersonEffective();
    }

//    删除审核计划
    public function delPlan()
    {
        $id = input('post.id');
        LayerPlan::find($id)->delete();
        throw  new SuccessException();

    }

//    获全部审核清单
    public function getAllObservations($page = 1, $count = 15)
    {
        return LayerQuestion::getUncloseDetailByAndriod($page, $count);
    }

//      获取没有关闭的审核单
    public function getUnCloseObservations($page, $count)
    {
        return LayerQuestion::getUncloseDetailByAndriod($page, $count);
    }

//          获取关闭的审核单
    public function getCloseObservations($page, $count)
    {
        return LayerQuestion::getCloseDetailByAndriod($page, $count);
    }

//    更新发现项目的跟踪信息
    public function updateObservations($id, $commit)
    {
        $observation=LayerQuestion::get($id);
        $observation['commit']=$commit;
        $observation->save();
        return $observation;
    }
}


