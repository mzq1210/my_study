<?php
/**
 * Created by PhpStorm.
 * User: Sixstar-Peter
 * Date: 2019/10/8
 * Time: 22:19
 */
require  "vendor/autoload.php";

class Clusster {

    public $server;
    public $options;
    public $client;
    public $slotNodes;

    public function __construct($server, $options) {
        $this->server = $server;
        $this->options = $options;
        $this->client=new Predis\Client($server,$options);
        $this->_getSlotNodes();
    }

    public function mdelete($delKeys){
        $slotKeys = $this->_getSlotKeys($delKeys);
        //执行命令并且返回结果
        $result=[];
        foreach ($slotKeys  as $node=>$keys){
            $res=$this->client->getClientFor($node)->pipeline(function ($pipe)use($keys){
                foreach ($keys as $v) {
                    $pipe->del($v);
                }
            });
            foreach ($keys as $k => $v) {
                $result[$node .'=>'. $v] = $res[$k];
            }
        }
        return $result;
    }

    public function mget($delKeys){
        $slotKeys = $this->_getSlotKeys($delKeys);
        //执行命令并且返回结果
        $result=[];
        foreach ($slotKeys  as $node=>$keys){
            $res=$this->client->getClientFor($node)->pipeline(function ($pipe)use($keys){
                foreach ($keys as $v) {
                    $pipe->get($v);
                }
            });
            foreach ($keys as $k => $v) {
                $result[$node .'=>'. $v] = $res[$k];
            }
        }
        return $result;
    }

    /**
     * @ 待解决问题: set之后返回的是一个对象该怎么处理
     *
     * object(Predis\Response\Status)#21 (1) {
        ["payload":"Predis\Response\Status":private]=>
        string(2) "OK"
       }
     *
     * @param $delKeys
     * @return array
     */
    public function mset($delKeys){
        $slotKeys = $this->_getSlotKeys(array_keys($delKeys));
        //执行命令并且返回结果
        $result=[];
        foreach ($slotKeys  as $node=>$keys){
            $res=$this->client->getClientFor($node)->pipeline(function ($pipe)use($keys, $delKeys){
                foreach ($keys as $v) {
                    $pipe->set($v, $delKeys[$v]);
                    $pipe->get($v);
                }
            });

            foreach ($keys as $k => $v) {
                $result[$node .'=>'. $v] = $res[2*$k+1];
            }
        }
        return $result;
    }

    private function _getSlotNodes(){
        $connectionId=$this->server[array_rand($this->server)];
        //得到槽节点信息,保存在某个缓存文件当中
        $slotInfo=$this->client->getClientFor($connectionId)->executeRaw(['cluster','slots']);
        foreach ($slotInfo as $slots=>$nodes){
            $this->slotNodes[$nodes[0].','.$nodes[1]]=$nodes[2][0].':'.$nodes[2][1];
        }
    }

    private function _getSlotKeys($keys){
        $slotKeys=[];
        $crc=new \Predis\Cluster\Hash\CRC16();
        foreach ($keys as $keyName){
            $code=$crc->hash($keyName) % 16384; //计算出某个key对应的槽节点
            //循环匹配,如果key在某个节点的范围之内就拼接数据(有可能多个key在同一节点)
            array_walk($this->slotNodes,function($node,$slotRange)use($code,&$slotKeys,$keyName){
                $range=explode(",",$slotRange);
                //判断某个key计算出来的槽,是否在这个范围之内,如果是就添加
                if($code>=$range[0] && $code<=$range[1] ){
                    $slotKeys[$node][]=$keyName;
                }
            });
        }
        return $slotKeys;
    }
}

$server=[
    '101.200.56.53:6391',
    '101.200.56.53:6392',
    '101.200.56.53:6393'
];
$options=[
    'cluster'=>'redis',
    'parameters'=>
        [
            'password'=>'sixstar'
        ]
];

$cluster = new Clusster($server, $options);
$keys=['name1','name2','name3','name4'];
$setkeys=['name1'=>111,'name2'=>222,'name3'=>333,'name4'=>444];


$delRes = $cluster->mdelete($keys);
var_dump($delRes);

/*$keys=['name1'=>111,'name2'=>222,'name3'=>333,'name4'=>444];
$setRes = $cluster->mset($setkeys);
var_dump($setRes);*/

/*$getRes = $cluster->mget($keys);
var_dump($getRes);*/


