<?php
ini_set('memory_limit', '-1');
require_once "";

class NewDB  extends DB
{
    public function add_db($table_num,$databases_num,$table_name,$database_name)
    {
        $con = $this->connections;
        if($con){
            for($i=0;$i<$databases_num;$i++){//10个库
                $sql="drop database $database_name{$i};";//删库 谨慎
                $this->wirte($sql);
                $sql="create database $database_name{$i} default character set utf8 collate utf8_general_ci;";
                $do=$this->wirte($sql);
                if($do){
                    $this->wirte("set name gtf8");
                    for($j=0;$j<$table_num;$j++){		//10个表
                        $sql="drop table if exists $table_name{$j};";
                        $this->wirte($sql);
                        $sql="create table $table_name{$j}
				(
					id char(36) not null primary key,
					name char(15) not null default '',
					password char(32) not null default '',
					sex char(1) not null default '男'
				)engine=InnoDB;";
                        $do=$this->wirte($sql);
                        if($do){
                            echo "create table $table_name{$j} successful! <br/>";
                        }else{
                            echo "create error!";
                        }
                    }
                }
            }
        }else {
            echo "connect error!!!!";
        }
    }
}
