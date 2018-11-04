<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/5
 * Time: 15:02
 */

namespace app\api\model;


class User extends  baseModel
{
    protected $hidden=['password','duty'];

    public static function getUserInfo($id){
        return self::find($id);
    }
}