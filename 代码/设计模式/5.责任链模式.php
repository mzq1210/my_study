<?php

//分级处理，比如帖子删帖  封账号 报警
//先传给最低级的方法，最低级的权限不够处理，就把数据在处理方法中往上再传一级，直到最顶级

class board {
    public  $power = 1;
    protected $top = 'admin';

    public function process($lev) {
        if($lev <= $this->power){
            echo '版主删帖';
        }else{
            $top = new $this->top;
            $top->process($lev);
        }

    }
}

class admin {
    public  $power = 2;
    protected $top = 'police';

    public function process($lev) {

        if($lev <= $this->power){
            echo '管理员注销';
        }else{
            $top = new $this->top;
            $top->process($lev);
        }
    }
}

class police {
    public  $top = null;

    public function process() {
        echo '抓起来';
    }
}

$lev = $_POST['lev'];
$j = new board();
$j->process($lev);