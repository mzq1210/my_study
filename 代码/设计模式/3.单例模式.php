<?php

//构造方法用protected  然后通过getInstance方法获取对象实例
//如果子类继承以后，改变了单利状态怎么办
//答：子类构造方法前加上final
//如果单利对象被clone了呢，这样单利状态又被改变了
//答：在单利方法中final一个clone方法即可，方法里不需要逻辑
//常用语数据库，redis连接

 class sigle {
     protected static $_instance = null;

     //方法前加final则方法不能被覆盖，类前加final则类不能被继承
     final protected function __construct() {

     }

     /**
      * instanceof的作用
      * 1）判断一个对象是否是某个类的实例，
      * 2）判断一个对象是否实现了某个接口。
      */
     public static function getInstance() {
         if (is_null(self::$_instance) || !(self::$_instance instanceof self)) {
             self::$_instance = new self();
         }
         return self::$_instance;
     }

     //封锁clone方法
     final protected function __clone()
     {
         // TODO: Implement __clone() method.
     }
 }

 $obj = sigle::getInstance();