<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/6
 * Time: 14:57
 */

namespace app\push\controller;


use app\push\service\WSserver;


class Worker extends WSserver
{
    protected $socket = 'websocket://127.0.0.1:2346';
    private $client = [];

    /**
     * 收到信息
     * @param $connection
     * @param $data
     */
    public function onMessage($connection, $data)
    {
        if (preg_match('/^login:(\w{3,20})/i', $data, $result)) {
            $ip = $connection->getRemoteIp();
            if (!array_key_exists($ip, $this->client)) {
                $this->client[$ip] = ['ip' => $ip,
                    'name' => $result[1],
                    'con' => $connection];
                //返回信息
                $connection->send('notice:success');
                $connection->send('msg:welcome' . $result[1]);
                echo $ip . ':' . $result[1] . '登录成功' . PHP_EOL;

                $users = 'users:' . json_encode(array_column($this->client, 'name', 'ip'));
                foreach ($this->client as $ip => $client) {
                    $client['con']->send($users);
                }
            }
        }
        else {
            foreach ($this->client as $ip => $client) {
                $callbackData=$data.'('.$connection->getRemoteIp().')';
                $client['con']->send($callbackData);
            }
        }
        echo '收到消息：'.$data.PHP_EOL;
    }


    /**
     * 当连接建立时触发的回调函数
     * @param $connection
     */
    public function onConnect($connection)
    {

    }

    /**
     * 当连接断开时触发的回调函数
     * @param $connection
     */
    public function onClose($connection)
    {
        unset($this->client[$connection->getRemoteIp()]);
        echo $connection->getRemoteIp().' closed;'.PHP_EOL;
    }

    /**
     * 当客户端的连接上发生错误时触发
     * @param $connection
     * @param $code
     * @param $msg
     */
    public function onError($connection, $code, $msg)
    {
        echo "error $code $msg\n";
    }

    /**
     * 每个进程启动
     * @param $worker
     */
    public function onWorkerStart($worker)
    {

    }
}