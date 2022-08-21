<?php

//举例：老虎  会爬树的和不会爬树的(多态)
//创建一个工厂，根据字符串去判断new哪一种老虎，然后调用爬方法


//共同接口
interface db {
    function conn();
}

class dbmysql implements db {
    public function conn()
    {
        echo '连上了mysql';
    }
}

class dbsqlite implements db {
    public function conn()
    {
        echo '连上了sqlite';
    }
}

class Factory {
    public static function createDb($type){
        if($type == 'mysql'){
            return new dbmysql();
        }else if($type == 'sqlite'){
            return new dbsqlite();
        }else{
            throw new \yii\db\Exception('连接失败');
        }
    }
}

//客户端现在不知道服务端到底有哪些类名，只知道对方开放了一个Factory::createDb()方法
//这就是简单工厂模式
$mysql = Factory::createDb('mysql');
$mysql->conn();

//现在如果新增一个oracle类型呢？该怎么办，不希望修改工厂类