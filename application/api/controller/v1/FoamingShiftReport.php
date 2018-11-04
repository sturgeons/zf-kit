<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/6
 * Time: 23:24
 */

namespace app\api\controller\v1;


use app\api\controller\baseController;
use app\api\model\FoamingBox;
use app\api\model\FoamingCycletime;
use app\api\model\FoamingFinalGoodScrap;
use app\lib\exception\NoDataException;

class FoamingShiftReport extends baseController
{
//获取发泡箱清单
    public function getFoamingBoxInfo($id)
    {
        $data=null;
        $model= new FoamingBox();
        if ($id=='0'){
            $data=$model->select();
        }else{
            $data=$model
                ->where('line','=',$id)
            ->select();
        }
        if (!$data){
            throw new NoDataException();
        }
    return json($data);
    }

    //获取发泡节拍
    public function getFoamingCT($id)
    {
        $data=null;
        $model= new FoamingCycletime();
        if ($id=='0'){
            $data=$model->select();
        }else{
            $data=$model
                ->where('id','=',$id)
                ->select();
        }
        if (!$data){
            throw new NoDataException();
        }
        return json($data);
    }

    //获取发泡废品类型
    public function getFoamingScrapt($id)
    {
        $data=null;
        $model= new FoamingFinalGoodScrap();
        if ($id=='0'){
            $data=$model->select();
        }else{
            $data=$model
                ->where('id','=',$id)
                ->select();
        }
        if (!$data){
            throw new NoDataException();
        }
        return json($data);
    }
}