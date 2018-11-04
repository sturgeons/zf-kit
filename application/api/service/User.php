<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/5
 * Time: 15:33
 */

namespace app\api\service;


use app\api\model\UserScope;
use app\lib\exception\FailErrLoginException;
use app\lib\exception\TokenException;
use think\Cache;
use think\Exception;
use think\Request;

class User
{
    //检测用户名和密码是否正确
    public function checkUser($name, $password)
    {
        $Muser = new \app\api\model\User();
        $res = $Muser->where('code', '=', $name)
            ->where('password', '=', $password)
            ->find();
        if ($res) {
            $scope=UserScope::where('user_id','=',$res->id)->select();
            $returnScope=[];
            if ($scope){
                foreach ($scope as $item){
                    $returnScope[]=$item->scope;
                }
            }
            $token = $this->grateToken($res,$returnScope);
            return ['token' => $token,
                'scope'=>$returnScope
            ];
        }
        throw  new FailErrLoginException();
    }

    //生成和存储Token
    public function grateToken($res,$scope)
    {
        $token = getRandChar(13);
        $data['uid'] = $res->id;
        $data['name'] = $res->name;
        $data['code'] = $res->code;
        $data['scope'] = $scope;
        $saveData = json_encode($data);
        Cache::set($token, $saveData, 86400);
        return $token;
    }

    //检查是否登录
    public function checkLogin($token)
    {
        $res = Cache::get($token);
        if ($res) {
            return json_decode($res,true);
        }
        throw new TokenException();
    }

    //获取用户UID
    public static function getUid()
    {
        $uid = self::getCurrentTokenVar('uid');
        return $uid;
    }

    //获取token中的属性
    public static function getCurrentTokenVar($key)
    {
        $token = Request::instance()
            ->header('token');
        $vars = Cache::get($token);
        if (!$vars) {
            throw new TokenException();
        } else {
            if (!is_array($vars)) {
                $vars = json_decode($vars, true);
            }
            if (array_key_exists($key, $vars)) {
                return $vars[$key];
            } else {
                throw new Exception('尝试获取的Token变量并不存在');
            }
        }
    }

//    检查是否是管理员
    public function isPowerUser($token, $scope)
    {
        $user=$this->checkLogin($token);
        if (!$user){
            throw new TokenException();
        }

        $userScope=UserScope::where('scope','=','layer')
            ->where('user_id','=',$user['uid'])
            ->find();
        if (!$userScope||$userScope->level!=2){
            throw new TokenException();
        }
        return $user;
    }

}
