<?php
require './db/config.php';
require './db/Model.php';
class User extends Model
{
    protected $dbnamePrefix = 'user';
    protected $tablePrefix = 'user';
}

//生成唯一uuid
function create_uuid($prefix = ""){    //可以指定前缀
    $str = md5(uniqid(mt_rand(), true));
    $uuid  = substr($str,0,8) . '-';
    $uuid .= substr($str,8,4) . '-';
    $uuid .= substr($str,12,4) . '-';
    $uuid .= substr($str,16,4) . '-';
    $uuid .= substr($str,20,12);
    return $prefix . $uuid;
}


$userId=create_uuid();
$user = new User($userId);
$data=array(
  'id'=>$userId,
  'username'=>'大明',
  'phone' =>'15245124512',
  'sex'=>1
);

if($result=$user->insert($data)){
    echo '插入成功：','<pre/>';
    print_r($result);
}else{
    echo '插入失败：','<pre/>';
    print_r($result);
}

$condition=array("id"=>$userId);
$list=$user->select($condition);
if($list){
    echo '查询成功：','<pre/>';
    print_r($list);
}
