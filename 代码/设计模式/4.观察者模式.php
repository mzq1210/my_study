<?php

//php5中提供观察者observer与被观察者subject的接口
//观察者们需要观察着某个类的状态去发生变化，这就需要这个类变化的时候去通知他们，由此需要
//1、定义一个通知列表，
//2、一个增加观察者的方法attach
//3、一个删除观察者的方法detach
//4、一个通知方法notify
//使用场景：用户登录记录日志，发送邮件等

class user implements SplSubject {
    public $lognum;
    public $hobby;

    protected $observers = null;

    public function __construct($hobby)
    {
        $this->lognum = rand(1, 10);
        $this->hobby = $hobby;
        $this->observers = new SplObjectStorage();
    }

    public function login(){
        $this->notify();
    }

    public function attach(SplObserver $observer)
    {
        $this->observers->attach($observer);
    }

    public function detach(SplObserver $observer)
    {
        $this->observers->detach($observer);
    }

    public function notify()
    {
        $this->observers->rewind();
        while ($this->observers->valid()) {
            $observer = $this->observers->current();
            $observer->update($this);
            $this->observers->next();
        }
    }
}


class secrity implements SplObserver {
    public function update(SplSubject $subject)
    {
        if($subject->lognum < 3){
            echo '这是第'. $subject->lognum . '次安全登录';
        }else{
            echo '这是第'. $subject->lognum . '次登录，异常';
        }
    }
}


class ad  implements SplObserver {
    public function update(SplSubject $subject)
    {
        if($subject->hobby == 'sports'){
            echo '联赛开始了';
        }else{
            echo '好好学习';
        }
    }
}


//观察
$user = new user('sports');
$user->attach(new secrity());
$user->attach(new ad());
$user->login();


















