<?php
/**
 * Created by PhpStorm.
 * User: Sixstar-Peter
 * Date: 2019/9/10
 * Time: 22:25
 */

//定时任务,监控无法容忍大量延迟场景，可以编写外部监控程序监听主从节点的复制偏移量，
//当延迟较大时触发报警或者通知客户端避免读取延迟过高的从节点(修改配置文件)

//supvisor

$redis=new Redis();
$redis->connect('101.200.56.53',6380);
$redis->auth('123456');

//安装swoole扩展
swoole_timer_tick(2000,function ()use($redis){
    $serverInfo=$redis->info('replication');
    $masterOffset=$serverInfo['master_repl_offset'];
    //节点个数
    $slaveCount=$serverInfo['connected_slaves'];
    $config=[];

    for($i=0;$i<=$slaveCount;$i++){
        //"ip=101.200.56.53,port=6379,state=online,offset=4003,lag=0"
        preg_match('/ip=(\d+.+),port=(\d+),state=(\w+),offset=(\d+)/',$serverInfo['slave'.$i],$match);
        $node['ip']=$match[1];
        $node['port']=$match[2];
        $offset=$match[4];
        //如果从节点偏移量是在延迟范围之内
        if($masterOffset-$offset<10){
            $config[]=$node;
        }
    }

    $config = var_export($config, true);
    $str = <<<str
<?php
    return {$config};
?>
str;

    //重新生成配置文件
    file_put_contents(__DIR__.'/config.php', $str);
});
