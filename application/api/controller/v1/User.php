<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/5
 * Time: 15:33
 */

namespace app\api\controller\v1;


use app\api\controller\baseController;
use app\api\service\User as userServer;
use app\api\validate\TokenVali;
use app\api\validate\UserVali;

class User extends baseController
{
    //登录
    public function login()
    {
        (new UserVali())->goCheck();
        $us=new userServer();
        $data=$us->checkUser(input('post.code'),input('post.password'));
        return $data;
    }
    //检测是否登录
    public function checkUser(){
        (new TokenVali())->goCheck();
        $us=new userServer();
        $data=$us->checkLogin(input('post.token'));
        return $data;
    }
    //检查是否是power user 管理员
    public function isPowerUser()
    {
        $request = Request::instance();
        $token = $request->header('token');
        $userServer = new \app\api\service\User();
        $data = $userServer->isPowerUser($token, 'layer');
        if($data){
            return true;
        }
        return false;

    }
}