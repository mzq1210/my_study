<?php

//策略模式：和工厂类有点类似
//区别：
//工厂模式，直接根据条件返回一个你需要的对象
//策略模式，把你需要的对象赋值给策略类的一个属性，你调用策略类的属性就可以，不需要触碰需要的对象

interface Math {
    public function calc($op1, $op2);
}

class MathAdd implements Math {
    public function calc($op1, $op2)
    {
        return $op1+$op2;
    }
}

class MathSub implements Math {
    public function calc($op1, $op2)
    {
        return $op1-$op2;
    }
}

class MathMul implements Math {
    public function calc($op1, $op2)
    {
        return $op1*$op2;
    }
}

class MathDiv implements Math {
    public function calc($op1, $op2)
    {
        return $op1/$op2;
    }
}

//封装一个虚拟计算器

class CMath {
    protected $calc = null;

    public function __construct($type)
    {
        $calc = 'Math' . $type;
        $this->calc = new $calc();
    }

    public function calc($op1, $op2){
        return $this->calc->calc($op1, $op2);
    }
}

$res = new CMath('Add');
$res->calc(1,2);

















