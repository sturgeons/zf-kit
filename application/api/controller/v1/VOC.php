<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/28
 * Time: 10:23
 */

namespace app\api\controller\v1;


use app\api\model\foamingVOCModel\FoamingVocConfig;
use app\api\model\foamingVOCModel\FoamingVocHistory;
use app\api\model\foamingVOCModel\FoamingVocPartMatchName;
use app\lib\exception\CustomizationFailException;
use app\lib\exception\FoamingVocHaveSameCode;
use app\lib\exception\FoamingVocNoFullTime;
use app\lib\exception\NoDataException;
use app\lib\exception\SuccessException;
use think\exception\DbException;

class VOC extends baseController
{



    //获取登陆的等待出库的条码列表
    public function getPartNolistWait()
    {
        $data = [];
        $part_no_list = FoamingVocHistory::getWaitOutputPartNoList();
        foreach ($part_no_list as $i) {
            $cdata['part_no'] = $i['part_no'];
            $cdata['data'] = FoamingVocHistory::getWaitDataByPartNo($i['part_no']);
            $data[] = $cdata;
        }
        return $data;
    }


//    v2 入库
//      @method post
//      @data   code:(USN18022600459|FGLABEL2|34177380F|535|20|32|203||1000|)
//  获取到条码  切割条码信息
//  获取零件号   获取包装数量    获取流水号
//  根据流水号查找条码是否已经录入  如果是  返回错误
//  如果不是 存储条码信息
//  零件号 流水号 包装数量  入库时间
    public function income()
    {
        //获取录入的条码
        $data = explode('|', input('post.code'));
//        流水号
        $code = substr($data[0], 1);
//        零件号
        $part_no = $data[2];
//        包装数量
        $size = $data[5];

        $searchLable = FoamingVocHistory::where('code', '=', $code)->select();
        if ($searchLable) {
            throw new FoamingVocHaveSameCode();
        }
        try {
            $foamingVocModal = new FoamingVocHistory();
            $foamingVocModal->size = $size;
            $foamingVocModal->part_no = $part_no;
            $foamingVocModal->code = $code;
            $foamingVocModal->flag = 0;
            $saveData = $foamingVocModal->save();
        } catch (\Exception $e) {
            throw new  CustomizationFailException('条码已经录入，请勿重复录入。');
        }

        throw new SuccessException();
    }
//    v2 出库
//      @method post
//      @data   code:(USN18022600459|FGLABEL2|34177380F|535|20|32|203||1000|) 条码信息
//      @data
//  获取到条码  切割条码信息
//  获取零件号   获取包装数量    获取流水号
//  获取系统配置的 出库时间
//  根据流水号查找是否已经入库
//  如果已经入库 查找 入库时间，确定是否已经达到出库时间
//  如果达到 出库  flag =1
//  如果没达到 则返回错误

    public function moveOut()
    {
        //获取录入的条码
        $data = explode('|', input('post.code'));
//        流水号
        $code = substr($data[0], 1);
//        零件号
        $part_no = $data[2];
//        包装数量
        $size = $data[5];


//      根据 code  查找记录
        $dataSave = FoamingVocHistory::get(['code' => $code]);

        if ($dataSave === null) {
            //如果没有找到这个条码
            throw new NoDataException();
        } else {

            $dd=$dataSave->create_time['canRemove'];

//            是否到达正常出库时间 或者 进行强制出库？
            if (($dataSave->create_time['canRemove']) || (input('post.outRule') == '1')) {
                //如果找到条码 但是没有达到出库时间
                $dataSave->delete();
                throw new SuccessException();
            } else {
                throw new FoamingVocNoFullTime($dataSave->create_time['restTime']);
            }
        }
    }

//    获取配置信息
    public function getConfig()
    {
        return FoamingVocConfig::all();
    }

//    更新配置信息
    public function updateConfig()
    {
        $configModel = new FoamingVocConfig();
        try {
            $configModel->saveAll(request()->post());
        } catch (\Exception $e) {
            throw new  NoDataException();
        }
    }

//    新增或更新零件对应表
    public function updatePartNo()
    {
        $configModel = new FoamingVocPartMatchName();
        try {
            return $configModel->saveAll(request()->post());
        } catch (\Exception $e) {
            throw new  NoDataException();
        }
    }

//    查询零件对应表
    public function getPartNo()
    {
        try {
            return FoamingVocPartMatchName::all();
        } catch (DbException $e) {
            throw new  NoDataException();
        }
    }

//    查询现在系统里有那些产品在等待出库
    public function getStoragePartNo()
    {
        $foamingHistoryData=new FoamingVocHistory();
        return $foamingHistoryData->getWaitOutputPartNoList();
    }
}