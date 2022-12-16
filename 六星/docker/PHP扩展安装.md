#### 1.进入容器
```bash
docker exec -it php /bin/bash #进入php容器
```

#### 2.命令说明
##### 2.1 PHP安装和配置工具命令位置

```bash
ls /usr/local/bin
```
##### 2.2 常用命令
- docker-php-source
- docker-php-ext-install
- docker-php-ext-enable
- docker-php-ext-configure

**docker-php-source** 此命令是在php容器中创建扩展源码存放的目录。linux一般源码存放地址为/usr/src，而此命令就是在/usr/src下创建一个php文件夹：/usr/src/php里面放一些php的扩展源码，php安装后的扩展实际存放路径为/usr/src/php/ext里面。它的参数有两个：

```bash
#创建并初始化/usr/src/php目录，初始化运行之后会把镜像中所有自带的扩展全部展示出来
docker-php-source extract
#删除 /usr/src/php目录
docker-php-source delete
```

**docker-php-ext-install** 用来安装并启动PHP扩展。

这里分为两种情况：
- /usr/src/php/ext 目录下自带的，直接运行即可

```bash
docker-php-ext-install [扩展名]
```

- 目录下没有的（比如Redis）

```bash
#1.先下载扩展
wget https://pecl.php.net/get/redis-4.0.1.tgz
tar -zxvf redis-4.0.1.tgz
#2.移动到目标目录
mv redis-4.0.1 /usr/src/php/ext/redis
#3.安装
docker-php-ext-install redis
#4.重启容器
docker restart (容器名称)
```

> 切记:
> 要先下载源码包，并把源码包放到/usr/src/php/ext 目录下(默认情况，PHP容器没有 /usr/src/php这个目录，需要使用 docker-php-source extract来生成。)

扩展在安装完成后会自动调用docker-php-ext-enable来启动安装的扩展。
**卸载**：直接删除/usr/local/etc/php/conf.d 对应的配置文件即可。

**docker-php-ext-enable **用来启动 PHP扩展。使用pecl安装PHP扩展的时候，默认是没有启动这个扩展的，而 docker-php-ext-enable 则可以自动启动PHP扩展，不需要再去php.ini配置文件中配置。


**docker-php-ext-configure** 一般是跟 **docker-php-ext-install** 搭配使用。它的作用是当安装扩展需要自定义配置时，可以使用它来完成。 可以把他理解为编译安装的 ./configure –with 。。。。。等参数，一般Dockerfile里会用到



如果缺少依赖，安装相关依赖

```bash
apt-get update && apt-get install -y libfreetype6-dev libjpeg62-turbo-dev libmcrypt-dev libpng-dev
```



[安装GD库](https://blog.51cto.com/u_14508118/5857683)