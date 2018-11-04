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
use app\api\model\foamingVoc;
use app\api\model\PkgHistory;
use app\api\model\pkgInfo;
use app\api\model\vocCotainer;
use app\lib\exception\BaseException;
use app\lib\exception\ErrCodeException;
use app\lib\exception\FailException;
use app\lib\exception\FoamingVocHaveSameCode;
use app\lib\exception\HaveSameCodeException;
use app\lib\exception\HaveTimeToDIsException;
use app\lib\exception\NoCounterException;
use app\lib\exception\NoDataException;
use app\lib\exception\NoPKGinCotainerException;
use app\lib\exception\PreNoCodeException;
use app\lib\exception\SuccessException;
use think\db\exception\DataNotFoundException;
use think\Exception;

class VOC extends baseController
{

    /**
     * 发泡完的方向盘录入系统
     * 录入标签 (USN18022600459|FGLABEL2|34177380F|535|20|32|203||1000|)
     * step1 检查是否录入 如果没有 录入系统 如果有 返回报错信息
     *
     * @throws FoamingVocHaveSameCode
     */
    public function recordCode()
    {
        //获取录入的条码
        $data = explode('|', input('post.label'));
        $code = substr($data[0], 1);
        $part_no = $data[2];
        $size = $data[5];
        $searchLable = foamingVoc::where('code', '=', $code)->select();
        if ($searchLable) {
            throw new FoamingVocHaveSameCode();
        }
        $foamingVocModal = new foamingVoc();
        $foamingVocModal->size = $size;
        $foamingVocModal->part_no = $part_no;
        $foamingVocModal->code = $code;
        $foamingVocModal->flag = 0;
        $saveData = $foamingVocModal->save();
        throw new SuccessException();
    }

    //获取登陆的等待出库的条码列表
    public function getPartNolistWait()
    {
        $data = [];
        $part_no_list = foamingVoc::getWaitOutputPartNoList();
        foreach ($part_no_list as $i) {
            $cdata['part_no'] = $i['part_no'];
            $cdata['data'] = foamingVoc::getWaitDataByPartNo($i['part_no']);
            $data[] = $cdata;
        }
        return $data;
    }

    //出库
    public function output()
    {
        $data = explode('|', input('post.label'));
        $code = substr($data[0], 1);

        $data = foamingVoc::where('code', '=', $code)->find();
        if (!$data) {
            throw new NoDataException();
        }
        if ($data->create_time <= 0) {
            $data->delete();
            throw new SuccessException();
        }
        return $data;
    }
}