<?php
/**
 * Created by PhpStorm.
 * User: Sixstar-Peter
 * Date: 2019/9/10
 * Time: 22:10
 */
require 'vendor/autoload.php';

$redis=new Predis\Client(['tcp://101.200.56.53:6380?alias=master','tcp://101.200.56.53:6381?alias=slave-01'],[
    'replication'=>true,
    'parameters' => [
        'password' => '123456',
    ],]);
echo $redis->set('test',123);

/*结合哨兵*/

var_dump($redis->get('test'));