<?php
/**
 * Created by PhpStorm.
 * User: Sixstar-Peter
 * Date: 2019/10/8
 * Time: 21:47
 */

//$redis=new Redis();
//$redis->connect('118.24.109.254',6390);
//$redis->auth('sixstar');

//$obj_cluster = new RedisCluster(NULL, Array("118.24.109.254:6390"), 1.5, 1.5, true, "sixstar");
//
//var_dump($obj_cluster->mget(['name1','name2']));

require  "vendor/autoload.php";

$server=[
     '118.24.109.254:6390',
     '118.24.109.254:6392',
];
$options=[
    'cluster'=>'redis',
    'parameters'=>
    [
        'password'=>'sixstar'
    ]
];
$client=new Predis\Client($server,$options);
var_dump($client->set('ll','peter'));

//var_dump($client->mget(['name1','name2']));
