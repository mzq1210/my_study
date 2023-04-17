### win10环境搭建

1.官网下载win10[安装包](https://www.mongodb.com/try/download/community-kubernetes-operator) 

[安装教程](https://blog.csdn.net/qq_44162474/article/details/104216788)

安装过程中一直弹窗mongodb Verify that you have sufficient privileges 错误？
解决：可能是被360拦截，把360给关闭了，然后就安装成功了

2.设置Path环境变量：

```bash
#变量名：
MONGO_HOME
#变量值：
E:\MongoDB （写自己MongoDB程序安装所在的路径）
#添加到path 
%MONGO_HOME%\bin
```

3.安装[MongoDB Shell](https://www.mongodb.com/try/download/shell) 

[安装教程](https://dblab.xmu.edu.cn/blog/3980/) 

```bash
#增加Path环境变量
E:\mongodb\mongosh-1.8.0\bin

#启动为cmd运行 
mongosh
```

4.安装php7.4的[mongodb扩展](https://pecl.php.net/package/mongodb) 

- 根据phpinfo()中的版本信息：Thread Safety判断下载哪个版本，如果是enabled则是Thread Safe，反之Non Thread Safe
- 下载解压后把php_mongo.dll，php_mongo.pdb复制到php7.4的ext下，打开php.ini文件，开启extension=php_mongo.dll
- phpstudy直接可以在【网站-php扩展】中选中开启，会自动重启php7.4

5.安装composer的mongodb扩展 

```php
composer require ext-mongodb
```

6.打开http://127.0.0.1:27017/，如果出现**It looks like you are trying to access MongoDB over HTTP on the native driver port.**则代表成功

7.代码示例

```php
$manager = new MongoDB\Driver\Manager("mongodb://127.0.0.1:27017");

// 插入数据
$bulk = new MongoDB\Driver\BulkWrite;
$bulk->insert(['x' => 1, 'name'=>'菜鸟教程', 'url' => 'http://www.runoob.com']);
$bulk->insert(['x' => 2, 'name'=>'Google', 'url' => 'http://www.google.com']);
$bulk->insert(['x' => 3, 'name'=>'taobao', 'url' => 'http://www.taobao.com']);
$manager->executeBulkWrite('test.sites', $bulk);

$filter = ['x' => ['$gt' => 1]];
$options = [
    'projection' => ['_id' => 0],
    'sort' => ['x' => -1],
];
```









