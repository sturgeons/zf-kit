<?php
/**
 * Created by PhpStorm.
 * User: yx392
 * Date: 2018/3/1
 * Time: 11:59
 */

namespace app\api\model;


use traits\model\SoftDelete;

class foamingVoc extends baseModel
{
    use SoftDelete;
    protected $autoWriteTimestamp = true;

    //对创建时间进行修饰
    public function getCreateTimeAttr($value, $data)
    {
        $timeStramp = 8 * 3600 - (time() - $value);
        if ($timeStramp < 0) {
            $timeStramp = 0;
        }
        return $timeStramp;
    }

    //获取扫描等待出库的零件清单
    public static function getWaitOutputPartNoList()
    {
        return self::field('part_no')
            ->distinct(true)
            ->select();
    }

    //获取当前零件号没有出库的产品信息
    public static function getWaitDataByPartNo($part_no)
    {
        return self::where('part_no', '=', $part_no)
            ->select();
    }
}