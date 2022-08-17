<?php
/**
 * Created by PhpStorm.
 * User: wat
 * Date: 2019/9/24
 * Time: 15:50
 */
$sentinelConf = [
    ['ip' => '192.168.1.14', 'port' => '26379'],
    ['ip' => '192.168.1.15', 'port' => '26379'],
    ['ip' => '192.168.1.16', 'port' => '26379']
];

$index = array_rand($sentinelConf);
$info = $sentinelConf[$index];

try{
    //随机取一个
    $redis = new Redis();
    $redis->connect($info['ip'], $info['port']);
    //模拟客户端发起请求
    while (true){
        //定期去更新配置，当程序出现连接异常，触发某个事件，去重新获取最新的主从节点信息
        $slaveInfo = $redis->rawCommand('sentinel', 'slaves', 'mymaster');
        $masterInfo = $redis->rawCommand('sentinel', 'get-master-addr-by-name', 'mymaster');

        var_dump($masterInfo);die;

        //轮询算法
        foreach ($slaveInfo as $v){
            $slaves[] = ['ip' => $v[3], 'port' => $v[5]];
            //生成到配置文件中
        }

        //这里的代码应该放到别的地方，和上面代码没关系，不过框架中可能已经实现，所以这一步不需要  开始
        $retry = 3; //尝试3次
        Retry:
        try{
            $redis = new Redis();
            $redis->connect($info['ip'], $info['port']);
        }catch (\Exception $e){
            $message = $e->getMessage();
            while ($message == 'Redis server went away' && $retry--){
                echo "链接失败测试";
                goto Retry;
            }
            echo "超时重试次数";
        }
        //这里的框架中可能已经实现  结束

        sleep(1);
    }
}catch (\Exception $e){
    //链接失败，重新选择一个哨兵
}


//主从文件配置无区别，slave监听即可
//哨兵配置文件修改四个地方
//15 bind 0 0 0 0
//17 打开保护模式
//23 增加日志文件
//70 ip改为主节点名或主节点ip


