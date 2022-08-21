<?php

//最主要的目的是装饰，给原始数据或者对象增加一些功能点缀，同时要避免修改基类，因为装饰器都是平级，所以需要父类增加一个属性值用来控制装饰器类

class BaseArt {
    protected $content;

    protected $art = null;

    public function __construct($content)
    {
        $this->content = $content;
    }

    public function decorator(){
        return $this->content;
    }
}

class BianArt extends BaseArt {

    public function __construct($art)
    {
        $this->art = $art;
        $this->decorator();
    }

    public function decorator(){
        return $this->content = $this->art->content . '增加文章摘要';
    }
}

class SeoArt extends BaseArt {

    public function __construct($art)
    {
        $this->art = $art;
        $this->decorator();
    }

    public function decorator(){
        return parent::decorator() . '增加SEO';
    }
}

$b = new SeoArt(new BianArt(new BaseArt('好好学习')));
$b->decorator();