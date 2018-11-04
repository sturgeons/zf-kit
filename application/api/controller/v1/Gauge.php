<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/14
 * Time: 11:19
 */

namespace app\api\controller\v1;


use app\api\controller\baseController;
use app\api\model\File;
use think\Request;

class Gauge extends baseController
{
//上传文件
    public function UpdateFile()
    {
        $file = \request()->file('file');
        $info = $file->move('File');
        if ($info) {
            print_r($info);
            $fileModel=new File();
            $fileModel->save([
                'path'=>$info->getSaveName(),
                'name'=>$info->getInfo('name')
            ]);
        } else {
            echo $info->getError();
        }
    }
}