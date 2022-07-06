# 教程

https://www.cnblogs.com/2bjiujiu/p/9041051.html

https://blog.csdn.net/hguisu/article/details/

https://arabelatso.github.io/2017/03/11/os1/

https://www.cnblogs.com/liuyuanq/p/9657475.html

https://www.cnblogs.com/itplay/p/11146791.html

cp /usr/local/etc/php-fpm.d/www.conf.default /usr/local/etc/php-fpm.d/www.conf

## 0. 课程内容

## 1. 虚拟机安装centos

这是额外的操作: 非centos7的配置自个儿百度啦

VMware的安装和破解这里省,可以自己看百度安装根据素材提供的账号破解即可..

下载一个小版本的centos,不需要桌面系统https://www.centos.org/download/

DVD桌面,minimal纯命令行;

### 安装过程,自行脑补-就过了

```
ip 配置 - 准备
```
因为虚拟机安装之后的centos它的IP是不固定的,在操作的时候非常的不方便所以需要先固定centos网络ip;

首先可以查看vm的默认网关操作:编辑->虚拟网络编辑器

一般就是VMnet

### 如图就是网关信息,至于网络信息这里我暂时不解释,自个而可以百度的.主要是注意里面的网关IP(G):192.168.153.

我们等会需要给centos分配对应的网络地址那么网络ip地址就需要在192.168.153下,但是不能为 2;可以是100,128,127等

那么对于centos来说我们就可以这么分配网络

详细的看看这个网址:https://www.cnblogs.com/guojun-junguo/p/9966412.html

```
IPADDR=192.168.153.127 # centos的ip地址
NETMASK=255.255.255.0 # 子网掩码
GATEWAY=192.168.153.2 # 网关
```
```
ip 配置 - 查看centos7 ip信息及配置
```
查看的方式就是通过命令 ip addr

### 正常情况是这样的

```
[root@localhost ~]# ip addr
1: lo: <LOOPBACK,UP,LOWER_UP> mtu 65536 qdisc noqueue state UNKNOWN group default qlen 1000
link/loopback 00:00:00:00:00:00 brd 00:00:00:00:00:
inet 127.0.0.1/8 scope host lo
valid_lft forever preferred_lft forever
inet6 ::1/128 scope host
valid_lft forever preferred_lft forever
2: ens33: <BROADCAST,MULTICAST,UP,LOWER_UP> mtu 1500 qdisc pfifo_fast state UP group default qlen 1000
link/ether 00:0c:29:63:c4:7b brd ff:ff:ff:ff:ff:ff
inet 192.168.153.128/24 brd 192.168.153.255 scope global noprefixroute ens
valid_lft forever preferred_lft forever
inet6 fe80::165:58f9:b155:4777/64 scope link noprefixroute
valid_lft forever preferred_lft forever
```
可以看到ip就是192.168.153.128 接下来配置一下ip

```
/etc/sysconfig/network-scripts/.ifcfg-ens33.swp
[root@localhost ~]# vi /etc/sysconfig/network-scripts/ifcfg-ens
TYPE=Ethernet
PROXY_METHOD=none
BROWSER_ONLY=no
BOOTPROTO=dhcp
DEFROUTE=yes
IPV4_FAILURE_FATAL=no
IPV6INIT=yes
IPV6_AUTOCONF=yes
IPV6_DEFROUTE=yes
IPV6_FAILURE_FATAL=no
IPV6_ADDR_GEN_MODE=stable-privacy
NAME=ens
UUID=ec4396d1-23f3-4522-aea3-eaaca3efc9ec
DEVICE=ens
ONBOOT=yes
ZONE=public
IPADDR=192.168.153.
NETMASK=255.255.255.
GATEWAY=192.168.153.
:wq
[root@localhost ~]# systemctl restart network
[root@localhost ~]# ip addr
1: lo: <LOOPBACK,UP,LOWER_UP> mtu 65536 qdisc noqueue state UNKNOWN group default qlen 1000
link/loopback 00:00:00:00:00:00 brd 00:00:00:00:00:
inet 127.0.0.1/8 scope host lo
valid_lft forever preferred_lft forever
inet6 ::1/128 scope host
valid_lft forever preferred_lft forever
2: ens33: <BROADCAST,MULTICAST,UP,LOWER_UP> mtu 1500 qdisc pfifo_fast state UP group default qlen 1000
link/ether 00:0c:29:63:c4:7b brd ff:ff:ff:ff:ff:ff
```

```
inet 192.168.153.127/24 brd 192.168.153.255 scope global noprefixroute ens
valid_lft forever preferred_lft forever
inet6 fe80::165:58f9:b155:4777/64 scope link noprefixroute
valid_lft forever preferred_lft forever
```
到此OK,然后可以在cmd中通过ping 192.168.153.127试试

```
C:\Users\shineyork>ping 192.168.153.
```
```
正在 Ping 192.168.153.127 具有 32 字节的数据:
来自 192.168.153.127 的回复: 字节=32 时间<1ms TTL=
来自 192.168.153.127 的回复: 字节=32 时间<1ms TTL=
来自 192.168.153.127 的回复: 字节=32 时间<1ms TTL=
来自 192.168.153.127 的回复: 字节=32 时间<1ms TTL=
```
```
192.168.153.127 的 Ping 统计信息:
数据包: 已发送 = 4，已接收 = 4，丢失 = 0 (0% 丢失)，
往返行程的估计时间(以毫秒为单位):
最短 = 0ms，最长 = 0ms，平均 = 0ms
```
ok然后就可以通过xftp连接了

宝塔的安装命令

```
yum install -y wget && wget -O install.sh http://download.bt.cn/install/install_6.0.sh && sh install.sh
```
### 其他自己看手册

## 2. linux下搭建PHP,nginx,mysql环境

https://www.php.net/manual/zh/reserved.variables.get.php

2.1 编译安装php

```
[root@localhost ~]# yum -y install wget
[root@localhost ~]# wget -c https://www.php.net/distributions/php-7.3.12.tar.gz
[root@localhost ~]# ls
[root@localhost ~]# tar -xzvf php-7.3.12.tar.gz
[root@localhost ~]# yum -y install libxml
[root@localhost ~]# yum -y install libxml2-devel
```
```
#需要执行一下因为有些系统不一的关系
[root@localhost ~]# yum -y install openssl
[root@localhost ~]# yum -y install openssl-devel
[root@localhost ~]# yum -y install curl
[root@localhost ~]# yum -y install curl-devel
[root@localhost ~]# yum -y install libjpeg
[root@localhost ~]# yum -y install libjpeg-devel
[root@localhost ~]# yum -y install libpng
[root@localhost ~]# yum -y install libpng-devel
[root@localhost ~]# yum -y install freetype
[root@localhost ~]# yum -y install freetype-devel
[root@localhost ~]# yum -y install pcre
[root@localhost ~]# yum -y install pcre-devel
[root@localhost ~]# yum -y install libxslt
[root@localhost ~]# yum -y install libxslt-devel
[root@localhost ~]# yum -y install bzip
[root@localhost ~]# yum -y install bzip2-devel
```
```
[root@localhost ~]# cd php-7.3.
```
```
[root@localhost ~]# ./configure --prefix=/usr/local/php --with-curl --with-freetype-dir --with-gd --with-gettext --with-iconv-dir --with-kerberos --with-libdir=lib64 --with-libxml-dir --with-mysqli --with-openssl --with-pcre-regex --with-pdo-mysql --with-pdo-sqlite --with-pear --
with-png-dir --with-jpeg-dir --with-xmlrpc --with-xsl --with-zlib --with-bz2 --with-mhash --enable-fpm --enable-bcmath --enable-libxml --enable-inline-optimization --enable-mbregex --enable-mbstring --enable-opcache --enable-pcntl --enable-shmop --enable-soap --enable-sockets --
enable-sysvsem --enable-sysvshm --enable-xml --enable-zip
```
```
> configure: error: Please reinstall the libzip distribution
```
```
[root@localhost ~]# yum remove libzip
[root@localhost ~]# cd /www
[root@localhost ~]# wget https://nih.at/libzip/libzip-1.2.0.tar.gz
[root@localhost ~]# tar -zxvf libzip-1.2.0.tar.gz
[root@localhost ~]# cd libzip-1.2.
[root@localhost ~]# ./configure
[root@localhost ~]# make -j4 && make install
```
```
[root@localhost ~]# ./configure --prefix=/usr/local/php --with-curl --with-freetype-dir --with-gd --with-gettext --with-iconv-dir --with-kerberos --with-libdir=lib64 --with-libxml-dir --with-mysqli --with-openssl --with-pcre-regex --with-pdo-mysql --with-pdo-sqlite --with-pear --
with-png-dir --with-jpeg-dir --with-xmlrpc --with-xsl --with-zlib --with-bz2 --with-mhash --enable-fpm --enable-bcmath --enable-libxml --enable-inline-optimization --enable-mbregex --enable-mbstring --enable-opcache --enable-pcntl --enable-shmop --enable-soap --enable-sockets --
enable-sysvsem --enable-sysvshm --enable-xml --enable-zip
```
```
> error: off_t undefined; check your library configuration
```
```
[root@localhost ~]# vi /etc/ld.so.conf
#添加如下几行
/usr/local/lib
/usr/local/lib
/usr/lib
/usr/lib
#按 esc 保存退出
:wq
[root@localhost ~]# ldconfig -v # 使之生效
```
```
[root@localhost ~]# ./configure --prefix=/usr/local/php --with-curl --with-freetype-dir --with-gd --with-gettext --with-iconv-dir --with-kerberos --with-libdir=lib64 --with-libxml-dir --with-mysqli --with-openssl --with-pcre-regex --with-pdo-mysql --with-pdo-sqlite --with-pear --
with-png-dir --with-jpeg-dir --with-xmlrpc --with-xsl --with-zlib --with-bz2 --with-mhash --enable-fpm --enable-bcmath --enable-libxml --enable-inline-optimization --enable-mbregex --enable-mbstring --enable-opcache --enable-pcntl --enable-shmop --enable-soap --enable-sockets --
enable-sysvsem --enable-sysvshm --enable-xml --enable-zip
```
```
[root@localhost ~]# make
```
```
> /usr/local/include/zip.h:59:21: 致命错误：zipconf.h：没有那个文件或目录
```
```
[root@localhost ~]# find / -name zipconf.h
[root@localhost ~]# cp /usr/local/lib/libzip/include/zipconf.h /usr/local/include/zipconf.h
```
```
> libtool: link: `ext/zip/php_zip.lo' is not a valid libtool object
make: *** [sapi/cli/php] 错误 1
```
```
[root@localhost ~]# make clear
[root@localhost ~]# make install
[root@localhost ~]# cp php.ini-development /usr/local/php/lib/php.ini
[root@localhost ~]# cp /usr/local/php/etc/php-fpm.conf.default /usr/local/php/etc/php-fpm.conf
[root@localhost ~]# ln -s /usr/local/php/sbin/php-fpm /usr/local/bin
[root@localhost ~]# groupadd www
[root@localhost ~]# useradd -g www www
[root@localhost ~]# cp /usr/local/php/etc/php-fpm.d/www.conf.default /usr/local/php/etc/php-fpm.d/www.conf
```
```
[root@localhost ~]# vi /usr/local/php/etc/php-fpm.d/www.conf
#修改如下内容
user=www
group=www
```

```
[root@localhost ~]# vi /usr/local/php/lib/php.ini
#修改如下内容
cgi.fix_pathinfo=
```
```
[root@localhost ~]# /usr/local/bin/php-fpm
[root@localhost ~]# yum install net-tools
[root@localhost php-fpm.d]# netstat -tln | grep 9000
tcp 0 0 127.0.0.1:9000 0.0.0.0:* LISTEN
[root@localhost php-fpm.d]# ps -aux | grep php
root 67971 0.0 0.3 231268 6560? Ss 15:24 0:00 php-fpm: master process (/usr/local/ph/etc/php-fpm.conf)
www 67972 0.0 0.3 231268 6100? S 15:24 0:00 php-fpm: pool www
www 67973 0.0 0.3 231268 6100? S 15:24 0:00 php-fpm: pool www
root 67998 0.0 0.0 112732 976 pts/0 S+ 15:24 0:00 grep --color=auto php
```
```
[root@localhost ~]# vi /etc/profile
export PATH="$PATH:/usr/local/php/bin"
```
```
[root@localhost ~]# source /etc/profile
[root@localhost php]# php -v
PHP 7.3.12 (cli) (built: Nov 27 2019 15:11:15) ( NTS )
Copyright (c) 1997-2018 The PHP Group
Zend Engine v3.3.12, Copyright (c) 1998-2018 Zend Technologies
```
2.2 编译安装nginx

```
[root@localhost ~]# yum install gcc-c++
[root@localhost ~]# yum install -y pcre pcre-devel
[root@localhost ~]# yum install -y zlib zlib-devel
[root@localhost ~]# wget https://nginx.org/download/nginx-1.16.0.tar.gz
[root@localhost ~]# ./configure
[root@localhost ~]# make
[root@localhost ~]# make install
[root@localhost ~]# whereis nginx
```
```
[root@localhost ~]# vi /etc/profile
export PATH="$PATH:/usr/local/php/bin:/usr/local/nginx/sbin/"
```
```
#nginx 启动nginx
#nginx -s stop 此方式相当于先查出nginx进程id再使用kill命令强制杀掉进程
#nginx -s quit 此方式停止步骤是待nginx进程处理任务完毕进行停止
#nginx -c /usr/local/nginx/conf/nginx.conf
```
```
[root@localhost ~]# ps -aux | grep nginx
root 73696 0.0 0.0 20556 636? Ss 16:24 0:00 nginx: master process nginx
nobody 73697 0.0 0.0 20928 1840? S 16:24 0:00 nginx: worker process
root 74075 0.0 0.0 112732 976 pts/0 S+ 16:32 0:00 grep --color=auto nginx
```
2.3 配置nginx与php

### 2.3.1 关闭防火墙关闭防火墙

### 首先要做的第一件事情 -> 关闭防火墙或者开发 80 端口如下命令

### #查看防火墙状态

```
[root@localhost ~]# systemctl status firewalld
#开启防火墙
[root@localhost ~]# systemctl start firewalld
#关闭防火墙
[root@localhost ~]# systemctl stop firewalld
#开启防火墙
[root@localhost ~]# service firewalld start
```
```
#若遇到无法开启
#先用：
[root@localhost ~]# systemctl unmask firewalld.service
#然后：
[root@localhost ~]# systemctl start firewalld.service
```
```
#开放端口
```
```
#开放端口
[root@localhost ~]# firewall-cmd --zone=public --add-port=80/tcp --permanent
#查询端口号 80 是否开启:
[root@localhost ~]# firewall-cmd --query-port=80/tcp
#重启防火墙:
[root@localhost ~]# firewall-cmd --reload
#查询有哪些端口是开启的:
[root@localhost ~]# firewall-cmd --list-port
```
### 自己虚拟机就关闭防火墙;

### 2.3.2 须知基础须知基础

先讲讲nginx与php的运行机制及原理; 花一点点时间还是需要解释一下nginx与php-fpm

```
nginx是什么
```
nginx是一个高性能的http和反向代理服务器

```
php-fpm是什么
```
_cgi_

早期的webserver只处理html等静态文件，但是随着技术的发展，出现了像php等动态语言。webserver处理不了了，怎么办呢?那就交给php解释器来处理吧!交给php解释器处理很好，但是，php解释器如何与webserver进行通信呢?

为了解决不同的语言解释器为了解决不同的语言解释器 **(** 如如 **php** 、、 **python** 解释器解释器 **)** 与与 **webserver** 的通信，于是出现了的通信，于是出现了 **cgit** 协议。只要你按照协议。只要你按照 **cgi** 协议去编写程序，就能实现语言解释器与协议去编写程序，就能实现语言解释器与 **webwerver** 的通信。如的通信。如 **php-cgi** 程序程序

_fast_cgi_

有了cgi协议，解决了php解释器与webserver通信的问题，webserver终 于可以处理动态语言了。但是，webserver每收到-个请求,都会去fork- 个cgi进程, 请求结束再lil掉这个进程。这样有 10000 个求，就需要fork、 kill php-cgi进程 0000 次。

于是，出现了cgi的改良版本，fast-cgi. fast-cgi每次处理完请求后，不会kill掉这个进程，而是保留这个进程，使这个进程可以- -次处理多个请求。这样每次就不用重新fork-个进程了，大大提高了效率。

_php-fpm_

php-fpm即php-Fastcgi Process Manager.php-fpm是FastCGI的实现，并提供了进程管理的功能。进程包含master进程和worker进程两种进程。

master进程只有一个， 负责监听端口，接收来自Web Server的请求，而worker进程则-般有多个(具体数量根据实际需要配置)，每个进程内部都嵌入了一一个PHP解释器，是PHP代码真正执行的地方。

**2.3.3 php** 与与 **nginx** 结合实现结合实现

修改nginx配置


```
server {
listen 80 ;
server_name localhost;
index index.php index.html;
root /www/wwwroot;
location / {
autoindex on;
}
error_page 500 502 503 504 /50x.html;
location = /50x.html {
root html;
}
location ~ \.php$ {
fastcgi_pass 127.0.0.1:9000;
fastcgi_index index.php;
fastcgi_split_path_info ^((?U).+\.php)(/?.+)$;
fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
include fastcgi_params;
}
}
```
其中关键配置主要是location ~ \.php$ 中的配置;

```
http://www.xxx.com -> nginx -> 路由到/www/wwwroot/index.php -> 加载nginx的fast-cgi模块 -> 通过fast-cgi监听127.0.0.1:9000 -> http://www.xxx.com/index.php请求到达127.0.0.1:9000 -> php-fpm 监听127.0.0.1:9000 -> php-fpm 接收到请求,启动worker进程处理请求 -> php-fpm处理完请求, 返回给nginx -> nginx
将结果通过http返回给浏览器
```
2.4 编译安装mysql

### -- 下载目录

```
[root@localhost ~] cd /.
[root@localhost ~] mkdir study
[root@localhost ~] cd study
```
```
-- 下载MySQL7的版本
-- https://dev.mysql.com/get/Downloads/MySQL-5.7/mysql-5.7.28-linux-glibc2.12-x86_64.tar.gz
```
```
-- 解压
[root@localhost study] tar -zvxf mysql-5.7.26-linux-glibc2.12-x86_64.tar.gz
```
```
-- /usr/local 目录下创建文件夹存
[root@localhost study] mkdir /usr/local/msyql
-- 移动
[root@localhost study] mv mysql-5.7.28-linux-glibc2.12-x86_64/* /usr/lcoal/mysql
```
```
-- 添加mysql组和mysql 用户：
-- 添加mysql组：
[root@localhost study] groupadd mysql
```
```
-- 添加mysql用户：
[root@localhost study] useradd -r -g mysql mysql
```
```
-- 配置MySQL
[root@localhost study] cd /usr/local/mysql
```
```
-- 主：从5.7.18开始不在二进制包中提供my-default.cnf文件
-- 进入MySQL的bin目录
-- 查看MySQL对于配置文件的查找路径，越左优先查找
[root@localhost mysql] ./bin/mysql --help | grep 'Default options' -A 1
```
```
-- 创建配置文件
[root@localhost mysql] rm -rf /etc/my.cnf
[root@localhost mysql] touch /etc/my.cnf
[root@localhost mysql] vi /etc/my.cnf
```
### 添加内容

```
[mysqld]
port=
datadir=/var/lib/mysql/data
socket=/var/lib/mysql/data/mysql.sock
user=root
max_connections=
symbolic-links=
```
```
# 设置忽略大小写
lower_case_table_names = 1
```
```
# 指定编码
character-set-server=utf
collation-server=utf8_general_ci
```
```
#指定客户端连接mysql时的socket通信文件路径
[client]
socket=/var/lib/mysql/data/mysql.sock
```
### -- 设置开机启动

### -- 复制启动脚本到资源目录

```
[root@localhost mysql] cp /usr/local/mysql/support-files/mysql.server /etc/rc.d/init.d/mysql
-- 增加 mysqld 服务控制脚本执行权限
[root@localhost mysql] chmod +x /etc/rc.d/init.d/mysql
-- 将 mysqld 服务加入到系统服务
[root@localhost mysql] chkconfig --add mysql
-- 检查mysqld服务是否已经生效
[root@localhost mysql] chkconfig --list mysql
```
```
[root@localhost mysql] mkdir /var/lib/mysql/
[root@localhost mysql] mkdir /var/lib/mysql/data
[root@localhost mysql] chown -R mysql:mysql /var/lib/mysql/
```
```
-- 环境变量配置
[root@localhost mysql] vi /etc/profile
```
### 添加如下内容 大概在 52 行左右

```
export PATH = $PATH:/usr/local/mysql/bin
```
### -- 刷新

```
[root@localhost mysql] source /etc/profile
-- 检测
[root@localhost mysql] echo $PATH
```
```
[root@localhost mysql] mysqld --user=root --basedir=/usr/local/mysql --datadir=/var/lib/mysql/data --initialize
```
```
-- 注意显示的最后这一行代码的意思就是密码
-- 2019-10-26T08:14:54.539744Z 0 [Warning] CA certificate ca.pem is self signed.
```

```
-- 2019-10-26T08:14:54.696247Z 1 [Note] A temporary password is generated for
-- root@localhost: i#%RHdF?r8e.
```
```
-- 启动MySQL
[root@localhost mysql] service mysql start
```
```
-- 修改登入密码
[root@localhost mysql] mysql -u root -p
```
```
mysql> alter user 'root'@'localhost' identified by 'root';
mysql> flush privileges;
mysql> quit;
```
```
-- 修改/usr/local/mysql/etc/my.cnf 文件
-- 注释skip-grant-tables
-- 重启MySQL
[root@localhost mysql] service mysql restart
```
```
-- 测试
[root@localhost mysql] mysql -u root -p
```
```
-- 配置远程访问
-- 防火墙端口设置，便于远程访问
[root@localhost mysql] firewall-cmd --zone=public --add-port=3306/tcp --permanent
[root@localhost mysql] firewall-cmd --reload
-- 查看端口
```
```
启动防火墙服务：systemctl ummask firewalld
启动防火墙：systemctl start firewalld
```
```
-- 进入MySQL
mysql> grant all privileges on *.* to root@'%' identified by "password";
mysql> flush privileges;
```
```
-- 如果没有效果可以尝试重启一下MySQL
```
## 3. linux目录

### 输入命令

```
ls /
```
[root@rhel6 ~]#



### 以下是对这些目录的解释：

```
/bin ： bin是Binary的缩写, 这个目录存放着最经常使用的命令。
```
```
/boot ：： 这里存放的是启动Linux时使用的一些核心文件，包括一些连接文件以及镜像文件。
```
```
/dev ：： dev是Device(设备)的缩写, 该目录下存放的是Linux的外部设备，在Linux中访问设备的方式和访问文件的方式是相同的。
```
```
/etc ：： 这个目录用来存放所有的系统管理所需要的配置文件和子目录。
```
```
/home ： 用户的主目录，在Linux中，每个用户都有一个自己的目录，一般该目录名是以用户的账号命名的。
```
```
/lib ： 这个目录里存放着系统最基本的动态连接共享库，其作用类似于Windows里的DLL文件。几乎所有的应用程序都需要用到这些共享库。
```
```
/lost+found ： 这个目录一般情况下是空的，当系统非法关机后，这里就存放了一些文件。
```
```
/media ： linux系统会自动识别一些设备，例如U盘、光驱等等，当识别后，linux会把识别的设备挂载到这个目录下。
```
```
/mnt ： 系统提供该目录是为了让用户临时挂载别的文件系统的，我们可以将光驱挂载在/mnt/上，然后进入该目录就可以查看光驱里的内容了。
```
```
/opt ： 这是给主机额外安装软件所摆放的目录。比如你安装一个ORACLE数据库则就可以放到这个目录下。默认是空的。
```
```
/proc ： 这个目录是一个虚拟的目录，它是系统内存的映射，我们可以通过直接访问这个目录来获取系统信息。 这个目录的内容不在硬盘上而是在内存里，我们也可以直接修改里面的某些文件，比如可以通过下面的命令来屏蔽主机的ping命令，使别人无法ping你的机器：
```
```
echo 1 > /proc/sys/net/ipv4/icmp_echo_ignore_all
```
```
/root ： 该目录为系统管理员，也称作超级权限者的用户主目录。
```
```
/sbin ： s就是Super User的意思，这里存放的是系统管理员使用的系统管理程序。
```
```
/selinux ： 这个目录是Redhat/CentOS所特有的目录，Selinux是一个安全机制，类似于windows的防火墙，但是这套机制比较复杂，这个目录就是存放selinux相关的文件的。
```
```
/srv ： 该目录存放一些服务启动之后需要提取的数据。
```
```
/sys ：
```
```
这是linux2.6内核的一个很大的变化。该目录下安装了2.6内核中新出现的一个文件系统 sysfs 。
```
```
sysfs文件系统集成了下面 3 种文件系统的信息：针对进程信息的proc文件系统、针对设备的devfs文件系统以及针对伪终端的devpts文件系统。
```
```
该文件系统是内核设备树的一个直观反映。
```
```
当一个内核对象被创建的时候，对应的文件和目录也在内核对象子系统中被创建。
```
```
/tmp ： 这个目录是用来存放一些临时文件的。
```
```
/usr ： 这是一个非常重要的目录，用户的很多应用程序和文件都放在这个目录下，类似于windows下的program files目录。
```
```
/usr/bin ：： 系统用户使用的应用程序。
```

```
/usr/sbin ：： 超级用户使用的比较高级的管理程序和系统守护程序。
```
```
/usr/src ：： 内核源代码默认的放置目录。
```
```
/var ： 这个目录中存放着在不断扩充着的东西，我们习惯将那些经常被修改的目录放在这个目录下。包括各种日志文件。
```
```
/run ： 是一个临时文件系统，存储系统启动以来的信息。当系统重启时，这个目录下的文件应该被删掉或清除。如果你的系统上有 /var/run 目录，应该让它指向 run。
```
在 Linux 系统中，有几个目录是比较重要的，平时需要注意不要误删除或者随意更改内部文件。 /etc： 上边也提到了，这个是系统中的配置文件，如果你更改了该目录下的某个文件可能会导致系统不能启动。 /bin, /sbin, /usr/bin, /usr/sbin: 这是系统预设的执行文件的放置目录，比如 ls 就是在/bin/ls 目录下的。 值得提出的
是，/bin, /usr/bin 是给系统用户使用的指令（除root外的通用户），而/sbin, /usr/sbin 则是给root使用的指令。 /var： 这是一个非常重要的目录，系统上跑了很多程序，那么每个程序都会有相应的日志产生，而这些日志就被记录到这个目录下，具体在/var/log 目录下，另外mail的预设放置也是在这里。

## 4. 为什么服务器选择linux

### 开源

Linux操作系统完全免费且可用作开源软件，通过开源方式，您可以轻松查看用于创建Linux内核的可用代码，还可以修改代码以修复任何错误等。它提供有许多编程接口，您甚至可以开发自己的程序并将其添加到Linux操作系统中，基本上，Linux可让您完全控制机器，只要你做够厉害，你完全可以按照自己的方式构建和自
定义服务器。这些是在windows上无法实现的

### 稳定性

Linux系统以其最终的稳定性而闻名。在windows上，我们进程看到系统崩溃或者卡死，但是在linux上，这种情况发生的几率极小，并且linux系统还可以同时处理多个任务，在Windows配置中，更改配置通常需要重新启动。但是在linux中则不需要重启，配置的更改都可在系统运行时完成，且不会影响到不相关的服务，同
样，windows服务器经常进行碎片整理，但是在linux上完全不需要这样做

### 安全

在安全方面，Linux显然比Windows更安全，因为Linux主要基于最初从多用户操作系统开发的UNIX操作系统。只有管理员或root用户具有管理权限，其次Linux也会病毒和恶意软件的攻击频率很低，很多病毒都是针对于windows，而针对linux的病毒比起windows少太多太多，比如此前的永恒之蓝病毒并未对linux造成影响。其
次，玩linux的用户群基本上都是计算机方面的人员，加上linux社区庞大，一般发现漏洞，很快会被并提交到linux开源社区

### 成本低

Linux是免费的，如果您想在其中一台服务器上安装Windows，则需要支付激活费用。这意味着你需要多花一笔钱

### 操作方便

对于普通人来说，windows操作更为快捷，但是对计算机专业人士来讲，linux操作比windows快捷的多，几条命令就可以执行很多的操作，比如某位大牛就出过一本书叫《完全用Linux 工作》，得到了很多人的好评。

虽然linux很好，但是也并非适合所有的公司，如果公司使用了ASP，ASP.NET，MSSQL，MS ACCESS或Visual Basic开发工具等软件，则需要使用Windows Server

## 4. linux 基础命令

### 4.0 基础命令

cd, mkdir, ls, ll, ps, top,

### 4.

### 语法：

```
chown [–R] 属主名 文件名
chown [-R] 属主名：属组名 文件名
```
进入 /root 目录（~）将install.log的拥有者改为bin这个账号：

```
[root@www ~] cd ~
[root@www ~]# chown bin install.log
[root@www ~]# ls -l
-rw-r--r-- 1 bin users 68495 Jun 25 08:53 install.log
```
将install.log的拥有者与群组改回为root：

```
[root@www ~]# chown root:root install.log
[root@www ~]# ls -l
-rw-r--r-- 1 root root 68495 Jun 25 08:53 install.log
```
### .