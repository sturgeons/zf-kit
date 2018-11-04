<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/7
 * Time: 13:16
 */

namespace app\api\model;


class LayerPicture extends baseModel
{
    public function getPathAttr($value, $data)
    {
        return config('setting.img_lpa_prefix') . $value;
    }

    public static function getPic($id)
    {
        return self::find($id);
    }

//    根据plan id 获取审核图片
    public function getPicByPlanId($plan_id)
    {
        return $this->where('plan_id', '=', $plan_id)->select();
    }

//    根据plan——id 和问题检查点 来获取 图片
    public function getPicByPlanIdAndPoint($plan_id, $point)
    {
        return $this->where('plan_id','=',$plan_id)
            ->where('point','=',$point)
            ->select();
    }

}