

## php composer 安装

### MacOS 安装方法一

- 手动下载： composer.phar 1.10.7 2020-06-03版本 https://getcomposer.org/download/

- 移动composer.phar文件到/usr/local/bin下，打开终端，输入命令:

```bash
mv composer.phar /usr/local/bin/composer
```
- 给composer赋予执行权限 (提示权限不足的时候执行此命令)

```bash
chmod +x /usr/local/bin/composer
composer -V
```


切换镜像地址：（注意：一定要切换镜像地址，要不然后面安装一直卡在那里。）
```bash
composer config -g repo.packagist composer https://mirrors.aliyun.com/composer/
```


### MacOS 安装方法二
```bash
$ curl -sS https://getcomposer.org/installer | php
$ sudo mv composer.phar /usr/local/bin/composer
$ composer --version
$ composer config -g repo.packagist composer https://mirrors.aliyun.com/composer/
$ composer selfupdate
```

### Windows安装
- 将php.exe路径加到环境变量;打开openssl扩展；

- https://getcomposer.org/下载composer.exe并安装，需要选择php；

- 可能出现安装缓慢的情况，可以打开vpn选择美国，这样就安装比较快；

- 安装完成后：cmd composer -V 查看composer版本；

- 切换镜像地址：composer config -g repo.packagist composer https://mirrors.aliyun.com/composer/
  
### Linux安装
- php -r “copy(‘https://install.phpcomposer.com/installer’, ‘composer-setup.php’);”
- php composer-setup.php
- 移动 composer.phar，这样 composer 就可以进行全局调用：
    mv composer.phar /usr/local/bin/composer
- 切换为国内镜像：composer config -g repo.packagist composer https://mirrors.aliyun.com/composer/
- 更新 composer：composer selfupdate



### 常用命令

```bash
#显示帮助信息
composer

#查看全局配置
composer config -gl

#官方镜像
composer config -g repo.packagist composer https://packagist.phpcomposer.com

#修改全局配置： 设置composer镜像为阿里云镜像
composer config -g repo.packagist composer https://mirrors.aliyun.com/composer/

#取消全局配置：
composer config -g --unset repos.packagist

#修改项目配置：
composer config repo.packagist composer https://mirrors.aliyun.com/composer/

#取消项目配置：
composer config --unset repos.packagist

#版本升级：
composer self-update || composer selfupdate

#版本回滚：
composer self-update --rollback

#执行诊断命令：
composer diagnose

#清除缓存：
composer clear

#若项目之前已通过其他源安装，则需要更新 composer.lock 文件，执行命令：
composer update --lock

#安装依赖包
composer install 
#强制使用压缩包，而不是克隆源代码 
composer install --prefer-dist

#可以使用 require 命令快速的安装一个依赖而不需要手动在 composer.json 里添加依赖信息：
composer require monolog/monolog

#全局安装依赖包(很少用到)
composer require global vendor/packages

#Composer 会先找到合适的版本，然后更新composer.json文件，在 require 那添加 monolog/monolog 包的相关信息，再把相关的依赖下载下来进行安装，最后更新 composer.lock 文件并生成 php 的自动加载文件。

#重新编译文件 (composer.json添加文件后需要执行此命令)
composer dump-autoload

"autoload": {
	"classmap": [ "database" ], 
	"psr-4": { "App\\": "app/" }, 
	"files":[ "app/Common/function.php", "app/helpers.php" ] 
},

#***********************************update命令***********************************
#更新所有依赖
composer update

#更新指定的包
composer update monolog/monolog

#更新指定的多个包
composer update monolog/monolog symfony/dependency-injection

#通过通配符匹配包
composer update monolog/monolog symfony/*

#需要注意的是，包能升级的版本会受到版本的约束，包不会升级到超出约束的版本的范围。例如如果 composer.json 里包的版本约束为 ^1.10，而最新版本为 2.0。那么 update 命令是不能把包升级到 2.0 版本的，只能最高升级到 1.x 版本。关于版本约束请看后面的介绍。

#***********************************remove命令***********************************
#remove 命令用于移除一个包及其依赖（在依赖没有被其他包使用的情况下），如果依赖被其他包使用，则无法移除：
composer remove monolog/monolog

#***********************************search命令***********************************
#用于搜索包
composer search monolog

#该命令会输出包及其描述信息，如果只想输出包名可以使用 --only-name 参数：
composer search --only-name monolog

#***********************************show命令***********************************
#show 命令可以列出当前项目使用到包的信息：

#列出所有已经安装的包 
composer show
#可以通过通配符进行筛选 
composer show monolog/*
#显示具体某个包的信息 
composer show monolog/monolog
#composer info 同 composer show命令
```




#### 错误：Composer detected issues in your platform: Your Composer dependencies require a PHP version “＞= 7.3.

原因：php版本不对，忽略即可

解决：修改composer.json

```php
"config": {
    "platform-check": false
}

//然后再执行
composer dump
```



