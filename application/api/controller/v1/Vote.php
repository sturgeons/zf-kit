<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/20
 * Time: 23:19
 */

namespace app\api\controller\v1;


use app\api\controller\baseController;
use app\api\model\VocAfterFoamingList;
use app\api\model\VotePoll;
use app\api\model\VoteUser;
use app\lib\exception\NoDataException;
use app\lib\exception\SuccessException;
use think\Db;

class Vote extends baseController
{
    public function Getresult()
    {
        $data =Db::query('SELECT	part_no , IF( TIMESTAMPDIFF(SECOND,create_time,NOW())>0,0,-TIMESTAMPDIFF(SECOND,create_time,NOW())) as saveTime ,size FROM	voc_after_foaming_list WHERE statue=0');;
        return $data;

    }

// 获取存储区域的在制品数量
    public function GetResultAss()
    {
        $res = new VocAfterFoamingList();
        $data = $res
            ->field('part_no,sum(size) as size')
            ->where('statue', '=', '0')
            ->group('part_no')
            ->select();
        return $data;
    }

//获取今天过去时间内消耗的方向盘数量
    public function last24HCount()
    {
        $data =Db::query('SELECT	part_no,sum( size ) AS size FROM	voc_after_foaming_list WHERE( to_days( update_time ) = TO_DAYS( NOW())) GROUP BY part_no');
        return $data;
    }

    public function submit()
    {
        $userId = input('post.userId');
        $poll = input('post.poll/a');
        $user = new VoteUser();
        $voteUser = $user->where('userId', '=', $userId)->where('isVote', '=', '0')->find();

        if ($voteUser) {
            foreach ($poll as $a) {
                $newpoll = new VotePoll([
                    'userId' => $voteUser->id,
                    'programId' => $a
                ]);
                $newpoll->save();
            }
            $updataUser = new VoteUser();
            $updataUser->save(['isVote' => '1'], ['id' => $voteUser->id]);
            return new SuccessException();
        } else {
            throw  new  NoDataException();
        }
    }
}