<?php
class DB
{
    //mysql配置
    protected $config;

    //mysql连接属性
    protected $connections;

    public function __construct($config = [])
    {
        if ($config["is_ms"]) {
            $this->connections = $this->getMysql($config["host"], $config["dbname"], $config["user"], $config["password"]);
        }
        $this->config = $config;
    }

    //mysql的连接
    protected function getMysql($host, $dbname = 'mysql_php', $user, $password, $dbms = 'mysql')
    {
        $dsn = "$dbms:host=$host;dbname=$dbname";
        try {
            $dbh = new PDO($dsn, $user, $password); //初始化一个PDO对象
            return $dbh;
        } catch (PDOException $e) {
            die ("Error!: " . $e->getMessage() . "<br/>");
        }
    }

    public function first($sql)
    {
        try {
            $res = $this->connections->prepare($sql);
            $res->execute();
            while ($result = $res->fetch(PDO::FETCH_ASSOC)) {
                return $result;
            }
        }catch (PDOException $PDOException){
            die ("Error!: " . $PDOException->getMessage() . "<br/>");
        }
    }

    public function wirte($sql)
    {
        try {
            $result = $this->connections->exec($sql);
            if ($result == true) {
                return true;
            } else {
                return false;
            }
        }catch (PDOException $PDOException){
            die ("Error!: " . $PDOException->getMessage() . "<br/>");
        }

    }

    public function create_drop_db($command, $dbname)
    {
        if ($command == "drop") {
            $sql = "drop database $dbname;";
        } else {
            $sql = "create database $dbname;";
        }
        if ($this->wirte($sql)) {
            return true;
        } else {
            return false;
        }
    }

    public function create_drop_table($command, $table, $create_table = null)
    {
        if ($command == "drop") {
            $sql = "drop table $table;";
        } else {
            $sql = "$create_table";
        }
        if ($this->wirte($sql)) {
            return true;
        } else {
            return false;
        }
    }

    public function add_db($databases_num, $tables_num, $tablename, $databasesname, $create_table = null, $strlen)
    {
        try {
            for ($i = 0; $i < $databases_num; $i++) {//10个库
                $this->create_drop_db("drop", "$databasesname{$i}");
                $this->create_drop_db("create", "$databasesname{$i}");
                $this->config["dbname"] = "$databasesname{$i}";
                $this->connections = $this->getMysql($this->config["host"], $this->config["dbname"], $this->config["user"], $this->config["password"]);
                for ($j = 0; $j < $tables_num; $j++) {        //10个表
                    $table = "CREATE TABLE $tablename{$j}" . substr($create_table, $strlen);
                    $this->create_drop_table("drop", "$tablename{$j}", $table);
                    $this->create_drop_table("create", "$tablename{$j}", $table);
                }
            }
            return "数据库$databasesname" . "与表$tablename" . "分库分表成功";
        } catch (Exception $exception) {
            die ("Error!: " . $exception->getMessage() . "<br/>");
        }
    }

    public function tables_add($databasename,$tables_num, $tablename, $create_table = null, $strlen)
    {
        try {
            $this->config["dbname"] = "$databasename";
            $this->connections = $this->getMysql($this->config["host"], $this->config["dbname"], $this->config["user"], $this->config["password"]);
            for ($j = 0; $j < $tables_num; $j++) {      //10个表
                $table = "CREATE TABLE $tablename{$j}" . substr($create_table, $strlen);
                $this->create_drop_table("drop", "$tablename{$j}", $table);
                $this->create_drop_table("create", "$tablename{$j}", $table);
            }
            return "表$tablename" . "分表成功";
        } catch (Exception $exception) {
            die ("Error!: " . $exception->getMessage() . "<br/>");
        }
    }

    public function databases_add($databases_num, $databasesname)
    {
        try {
            for ($i = 0; $i < $databases_num; $i++) {//10个库
                $this->create_drop_db("drop", "$databasesname{$i}");
                $this->create_drop_db("create", "$databasesname{$i}");
            }
            return "数据库$databasesname" . "分库成功";
        } catch (Exception $exception) {
            die ("Error!: " . $exception->getMessage() . "<br/>");
        }
    }
}
