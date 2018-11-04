<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/28
 * Time: 10:23
 */

namespace app\api\controller\v1;


use app\api\controller\baseController;
use app\api\model\accSws;
use app\api\model\PkgHistory;
use app\api\model\pkgInfo;
use app\api\model\vocCotainer;
use app\lib\exception\BaseException;
use app\lib\exception\ErrCodeException;
use app\lib\exception\FailException;
use app\lib\exception\HaveSameCodeException;
use app\lib\exception\HaveTimeToDIsException;
use app\lib\exception\NoCounterException;
use app\lib\exception\NoDataException;
use app\lib\exception\NoPKGinCotainerException;
use app\lib\exception\PreNoCodeException;
use app\lib\exception\SuccessException;
use think\Exception;

class SwsPKG extends baseController
{
    //获取产品列表
    public  function  getProductList(){
        return pkgInfo::all();
    }
    /**
     * 录入标签
     * step1 检查是否登录
     * step2 检查条码规则是否满足正则表达式
     * step3 检查是否已经录入果标签
     * step4 是否需要验证追溯性 是-》检查上序是否合格 否-》跳过
     * step5 存储条码信息
     * step6 检查是否到达装箱数量 是-》打印标签 否-》返回
     * step7 如果打印标签请更新缓存 通知notify 模块要更新数据
     *
     */
    public function recordCode()
    {
        //检查登录
        $user = new User();
        $currentUser = $user->checkUser();
        //检查条码规则
        $code = input('post.code');
        $type = input('post.type');
        $info = pkgInfo::where('type', '=', $type)->find();
        if (!$info) {
            throw new NoDataException();
        }
        $this->checkReg($code, $info->snappattern);
        //检查标签是否已经存在
        $sameCheckk = PkgHistory::where('sn', '=', $code)->find();
        if ($sameCheckk) {
            throw  new HaveSameCodeException();
        }
        //检查是否需要追溯
        if ($info->prestation != 0) {
            $predata = accSws::where('sn', '=', $code)->find();
            if (!$predata) {
                throw new PreNoCodeException();
            }
        }
        //存储已经这个标签信息
        $saveMbhistory = new PkgHistory([
            'sn' => $code,
            'user_id' => $currentUser['uid'],
            'packid' => $info->lastlable
        ]);
        $saveMbhistory->save();
        $afterCounter = $info->counter + 1;
        $saveInfo = new pkgInfo;
        $saveInfo->save([
            'counter' => $afterCounter
        ], ['type' => $type]);

        //判断是否需要打印标签
        if ($afterCounter >= $info->packsize) {
            $this->printLabel($info->id);
        }
        cache('newSwsPkg', '1');
        throw new SuccessException();
    }

//分配一个voc改善的位置
    public function bindVOCcontainer($packid)
    {
        $nullContainer = vocCotainer::where('packid', '=', '0')->find();
        if (!$nullContainer) {
            throw new NoCounterException();
        }
        $newCainer = new vocCotainer();
        $newCainer->save(['packid' => $packid], ['id' => $nullContainer->id]);
        throw  new BaseException([
            'msg'=>'请将方向盘放到'.$nullContainer->container."位置上静止。",
            'statusCode'=>200
        ]);
    }

//取出voc位置下的方向盘
    public function removeVocCotainer($id)
    {
        $newCainer = vocCotainer::get($id);
        if(!$newCainer){
            throw new NoDataException();
        }
        if($newCainer->packid=="0"){
            throw new NoPKGinCotainerException();
        }
        if( time()-strtotime( $newCainer->update_time)<8*60*60){
            throw  new HaveTimeToDIsException();
        };
        $newCainer->packid="0";
        $newCainer->save();
        return $newCainer;
    }
    //获取voc气味改善方向盘状态信息
    public  function  getVocAllInfo(){
        return vocCotainer::all();
    }

//打印方能线盘标签
    public function printLabel($id)
    {
        $data = pkgInfo::find($id);
        if ($data->counter == 0) {
            throw new NoCounterException();
        }
        $printData = $this->getIPLmodel($data->finalIPL, $data->type, $data->lastlable, $data->counter, $data->desc);
        prinIPLlable($data->ip, $printData);
        //打印完毕生成新的标签信息
        $pkginfo = new pkgInfo();
        $pkginfo->save([
            'counter' => 0,
            'lastlable' => createPKGcode($data->pre)
        ], ['id' => $id]);
    }

//获取标签模板
    public function getIPLmodel($id, $type, $pkgcode, $quantity, $desc)
    {
        switch ($id) {
            case 1:
                return "<STX>R<ETX><STX><ESC>C<SI>W869<SI>h<ETX><STX><ESC>P<ETX><STX>F*<ETX><STX>H2;f3;o590,70;c26;b0;h40;w35;d3," . $type . "<ETX><STX>B1;f3;o420,70;c17,200,0;w6;h6;d3,(B" . $pkgcode . "|FGLABEL2|" . $type . "|||" . $quantity . "202|||)<ETX><STX>H4;f3;o290,330;c26;b0;h25;w20;d3,B" . $pkgcode . "<ETX><STX>H5;f3;o729,60;c26;b0;h58;w58;d3," . $desc . "<ETX><STX>H6;f3;o170,480;c26;b0;h30;w30;d3," . date("Y/m/d h:i") . "<ETX><STX>H7;f3;o100,480;c26;b0;h35;w35;d3,QTY:" . $quantity . "<ETX><STX>D0<ETX><STX>R<ETX><STX><SI>l13<ETX><STX><ESC>E*,1<CAN><ETX><STX><RS>1<US>1<ETB><ETX><STX><FF><ETX>";
        }
    }

//检查正则表达式
    public function checkReg($data, $parrent)
    {
        if (preg_match("/" . $parrent . "/", $data) == 0) {
            throw  new ErrCodeException();
        }
    }

//提前打印装箱标签
    public function prePrint($id)
    {
        $this->printLabel($id);
    }

    //检查是否需要更新大屏幕
    public function checkNeddUpdate()
    {
        if (cache('newSwsPkg')) {
            if (cache("newSwsPkg") == '1') {
                cache('newSwsPkg', 0);
                return true;
            }
        }
        return false;
    }

    //获取当前零件号线面的所有信息
    public function getTypeData()
    {
        $type = input('post.type');
        return pkgInfo::getAllinfo($type);
    }
}