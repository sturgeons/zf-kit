<?php

namespace app\api\controller\v1;

use app\api\model\accSws;
use think\Cookie;


class Index
{
    public function index()
    {
//        mail
//        $mail = new sendEmail();
//        $ok = $mail->sendMail('xin.yao@zf.com', '', '邮件来了', '<p style="color: #f60; font-weight: 700;">恭喜，邮件成功!</p>');
//        return $ok;
//        return LayerAuditlist::getAllChecklistByArea('1');
//        $dd= accSws::where('sn','=','yaoxin')->find();
//        return $dd;
//        $data = "<STX>R<ETX><STX><ESC>C<SI>W869<SI>h<ETX><STX><ESC>P<ETX><STX>F*<ETX><STX>H2;f3;o590,70;c26;b0;h40;w35;d3,[PRODTYPE]<ETX><STX>B1;f3;o420,70;c17,200,0;w6;h6;d3,(B[PACKID]|FGLABEL2|[PRODTYPE]|||[CURRQUANTITY]|202|||)<ETX><STX>H4;f3;o290,330;c26;b0;h25;w20;d3,B[PACKID]<ETX><STX>H5;f3;o729,60;c26;b0;h58;w58;d3,[PRODDESC]<ETX><STX>H6;f3;o170,480;c26;b0;h30;w30;d3,[YYYY]/[MM]/[DD]<ETX><STX>H7;f3;o100,480;c26;b0;h35;w35;d3,QTY:[CURRQUANTITY]<ETX><STX>D0<ETX><STX>R<ETX><STX><SI>l13<ETX><STX><ESC>E*,1<CAN><ETX><STX><RS>1<US>1<ETB><ETX><STX><FF><ETX>";
//        prinIPLlable("192.168.10.106",$data);
//        return createPKGcode("78MK0");
//        return preg_match("/P3.{4}$/","P30023402");
        $AA='This is is y-server';
        return ['info'=>$AA];
    }

//    获取用户列表
    public function getUserList()
    {
        $user = \app\api\model\User::all();
        return $user;
    }

    public function t1()
    {
        return 1;
    }
}
