<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/7
 * Time: 13:17
 */

namespace app\api\model;


class UserLayer extends baseModel
{

    public function info(){
        return $this->hasOne('user','id','code_id');
    }

    public static function getUser($id,$page=1,$count=15){
        if ($id == '0') {
            $data = self:: with(['info'])
                ->paginate($count, false, ['page' => $page]);
        } else {
            $data = self::with(['info'])
                ->where('id','=',$id)
                ->whereOr('code_id','like','%'.$id.'%')
                ->paginate($count, false, ['page' => $page]);
        }
        return $data;
    }

    public static function getUserByCode($code){
        return self::where('code_id','=',$code)->select();
    }
}