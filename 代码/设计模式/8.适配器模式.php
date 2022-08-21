<?php

//把原本不适用的数据格式转化为需要的数据格式，所以肯定是父子级，适配器为子

class Tianqi {
    public static function show(){
        $today = ['tep' => 28, 'wind' => 7, 'sun' => 'sunny'];
        return serialize($today);
    }
}

class JsonTianqi extends Tianqi {
    public static function show()
    {
        $today = parent::show();
        $today = unserialize($today);
        $today = json_encode($today);
        return $today;
    }
}

//php调用
$tq = unserialize(Tianqi::show());

//Java调用
$tq = json_decode(JsonTianqi::show(), true);