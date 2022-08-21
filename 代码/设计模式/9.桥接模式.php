<?php

//需要做到适当耦合，不然子类太多，虽然有点违反规则，不用桥接模式是m*n个类，用了就是m+n个类

abstract class info {
    protected $send = null;

    public function __construct($send)
    {
        $this->send = $send;
    }

    abstract public function msg($content);

    public function send($to, $content){
        $content = $this->msg($content);
        $this->send($to, $content);
    }
}

class zn {
    public function send($to, $content){
        echo '发站内给：' . $to . '内容是：' . $content;
    }
}

class email {
    public function send($to, $content){
        echo '发邮件给：' . $to . '内容是：' . $content;
    }
}

class sms {
    public function send($to, $content){
        echo '发短信给：' . $to . '内容是：' . $content;
    }
}

class commoninfo extends info {
    public function msg($content){
        return '普通' . $content;
    }
}

class warninfo extends info {
    public function msg($content){
        return '紧急 ' . $content;
    }
}

class dangeinfo extends info {
    public function msg($content){
        return '最紧急' . $content;
    }
}

$common = new commoninfo(new zn());
$common->send('小明', '吃饭了 ');