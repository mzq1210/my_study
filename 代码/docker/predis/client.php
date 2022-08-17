<?php
/**
 * Created by PhpStorm.
 * User: Sixstar-Peter
 * Date: 2019/9/10
 * Time: 22:26
 */

require 'vendor/autoload.php';

$config = include "config.php";
$slaves = [];
foreach ($config as $key => $value){
    $slaves[$key] = 'tcp://' . $value['ip'] . ':' . $value['port'] . '?alias=slave' . $key;
}

$master = ['tcp://101.200.56.53:6380?alias=master'];
$all = array_merge($master, $slaves);

$redis = new Predis\Client($all, [
    'replication'=>true,
    'parameters' => [
        'password' => '123456',
    ]
]);
echo $redis->get('test');
var_dump($redis->get('test'));

#1.修改从节点配置文件端口  port  和  pid文件为 6380

# 当初错了是因为INFO replication slave显示的是从服务器的外网ip和端口，这就是为什么当初一直不对，应该SLAVEOF到master上去

#（这一步不要，之所以加是提个醒，因为从服务器端口不应该绑定宿主机，而应该和master有关，）2.创建容器把宿主机的8084绑定到本容器的6380端口
#3.容器内连接： redis-cli -a 123456 -p 6380
#4.监听master  SLAVEOF 192.168.1.2 6379
#5.从服务器监听master的6379端口，则master会获取到从服务器信息（从ip 和 从 port）