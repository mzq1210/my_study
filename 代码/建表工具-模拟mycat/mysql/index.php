<?php
require_once "config/database.php";
require_once "./db/DB.php";
$Db = new DB($config);
$databases = $_POST["database"];
$tables = $_POST["table"];
$databases_num = (int)$_POST["database_num"];
$tables_num = (int)$_POST["tables_num"];
$create = $_POST["sql"];
$type = $_POST["type"];
$strlen = strlen($tables)+13;
$table = substr($create,13,1);
if ($table == "`"){
    $strlen+=2;
}
//$create = "CREATE TABLE $table (`id` char(100) NOT NULL,`name` varchar(255) NOT NULL,`city` varchar(10) NOT NULL,`gender` tinyint(4) NOT NULL,`birthdate` date NOT NULL,`mobile` char(11) DEFAULT NULL,`photo` varchar(20) DEFAULT NULL,PRIMARY KEY (`id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8";
//$Db->add_db(1,10,'lms_products','lms_products',$create);
if ($type == 2){
    echo $Db->add_db($databases_num,$tables_num,$tables,$databases."_",$create,$strlen);
}else if ($type == 1){
    echo $Db->tables_add($databases,$tables_num,$tables,$create,$strlen);
}else{
    echo $Db->databases_add($databases_num,$databases);
}

// $str = "abd";
// $isMatched = preg_match('/^abd.*\w{6,12}$/is', $str, $matches);
//
// var_dump($isMatched, $matches);
