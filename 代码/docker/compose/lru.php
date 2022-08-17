<?php
/**
 * Created by PhpStorm.
 * User: Sixstar-Peter
 * Date: 2019/9/19
 * Time: 21:17
 */

class LRU{

    protected  $lru;
    protected  $maxCount;
    public  function set($key,$value){
           //如果存在,则向头部移动
            if(array_key_exists($key,$this->lru)){
                 //先删除掉老数据,然后重新设置
                 unset($this->lru[$key]);
            }
            //如果检查发现,超长则删除末尾的元素
            if(count($this->lru)>$this->maxCount){
                    //
            }
          //设置
          $this->lru[$key]=$value;
    }

    public  function get($key,$value){


    }
}