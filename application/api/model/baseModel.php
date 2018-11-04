<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/2
 * Time: 2:00
 */

namespace app\api\model;


use think\Model;

class baseModel extends Model
{
    protected $autoWriteTimestamp=true;
    protected $field = true;
}