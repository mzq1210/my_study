<?php

//给每种老虎都创建一个工厂，工厂中只new属于自己的老虎，然后调用爬方法
//使用场景：切换多种数据库驱动

//共同接口
interface db {
    function conn();
}

interface Factory {
    function createDb();
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

class mysqlFactory implements Factory {
    public function createDb(){
        return new dbmysql();
    }
}

class sqliteFactory implements Factory {
    public function createDb(){
        return new dbsqlite();
    }
}

//客户端
$fact = new mysqlFactory();
$fact->createDb();