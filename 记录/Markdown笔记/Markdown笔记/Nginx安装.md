## nginx安装

[TOC]

## 1.nginx安装方法



-  直接yum安装软件: 
    	   yum install -y nginx 
    	   特点: 老版本nginx软件程序/软件目录结构和新版本有区别
    	   好处: 简单方便

-  官方yum源安装软件: *****


  ```sh
  # 第一个历程: 更新yum源  
  #官方网址  http://nginx.org/en/linux_packages.html
  vim /etc/yum.repo.d/nginx.repo 
  [nginx-stable]
    name=nginx stable repo
    baseurl=http://nginx.org/packages/centos/$releasever/$basearch/
    gpgcheck=1
    enabled=1
    gpgkey=https://nginx.org/keys/nginx_signing.key
    module_hotfixes=true
    # 第二个历程: 安装软件
    yum install -y nginx
    特点: 最新稳定版nginx
  ```

- 3) 编译安装软件: (自定义安装/灵活安装)  

```sh
#第一个历程: 下载编译源码包
wget http://nginx.org/download/nginx-1.16.1.tar.gz   
#第二个历程: 解决软件依赖关系
yum install -y  openssl-devel  pcre-devel
#openssl-devel:  可以实现HTTPs访问网站
#pcre-devel:     兼容perl语言正则表达式依赖包   location
#第三个历程: 编译安装软件
tar xf nginx-1.16.1.tar.gz
cd nginx-1.16.1
#编译安装三部曲:
#a 进行编译配置过程:
./configure  --prefix=软件安装目录	   	 
--prefix=PATH       #指定软件程序安装目录               
		                                    
--user=USER         #设置普通用户管理worker进程              
                                            
--group=GROUP       #设置普通用户管理worker进程               
                                            
--with-http_ssl_module   #开启HTTPS功能         
		                                    
 --with-http_stub_status_module  #开启nginx程序状态模块功能(监控)   

#b 编译过程: 翻译
make
#c 编译安装过程:
make install
#启动nginx
./sbin/nginx
#关闭nginx
./sbin/nginx -s stop
#平滑重启
./sbin/nginx -s reload
```
### 1.nginx依赖包（以下安装为第二种方式）

```sh
yum install -y gcc gcc-c++ autoconf pcre pcre-devel make automake wget httpd-tools vim tree
```

### 2.配置nginx官方yum源

```sh
[root@web ~]# vim /etc/yum.repos.d/nginx.repo
[nginx]
name=nginx repo
baseurl=http://nginx.org/packages/centos/7/$basearch/
gpgcheck=0
enabled=1
```

### 3.检查yum源

```sh
[root@web01 ~]# yum repolist
Loaded plugins: fastestmirror
Loading mirror speeds from cached hostfile
 * base: mirrors.aliyun.com
 * extras: mirrors.aliyun.com
 * updates: mirrors.aliyun.com
nginx                                      | 2.9 kB     00:00     
nginx/x86_64/primary_db                      |  46 kB   00:01     
repo id          repo name                                  status
base/7/x86_64    CentOS-7 - Base - mirrors.aliyun.com       10,019
epel/x86_64      Extra Packages for Enterprise Linux 7 - x8 13,221
extras/7/x86_64  CentOS-7 - Extras - mirrors.aliyun.com        409
nginx/x86_64     nginx repo                                    152
updates/7/x86_64 CentOS-7 - Updates - mirrors.aliyun.com     1,982
repolist: 25,783
```

### 4.检查nginx版本

```sh
[root@web01 ~]# yum list nginx
Loaded plugins: fastestmirror
Loading mirror speeds from cached hostfile
 * base: mirrors.aliyun.com
 * extras: mirrors.aliyun.com
 * updates: mirrors.aliyun.com
Available Packages
nginx.x86_64               1:1.16.0-1.el7.ngx                nginx
[root@web01 ~]# nginx -V
nginx version: nginx/1.16.0
built by gcc 4.8.5 20150623 (Red Hat 4.8.5-36) (GCC) 
built with OpenSSL 1.0.2k-fips  26 Jan 2017
TLS SNI support enabled
configure arguments: --prefix=/etc/nginx --sbin-path=/usr/sbin/nginx --modules-path=/usr/lib64/nginx/modules --conf-path=/etc/nginx/nginx.conf --error-log-path=/var/log/nginx/error.log --http-log-path=/var/log/nginx/access.log --pid-path=/var/run/nginx.pid --lock-path=/var/run/nginx.lock --http-client-body-temp-path=/var/cache/nginx/client_temp --http-proxy-temp-path=/var/cache/nginx/proxy_temp --http-fastcgi-temp-path=/var/cache/nginx/fastcgi_temp --http-uwsgi-temp-path=/var/cache/nginx/uwsgi_temp --http-scgi-temp-path=/var/cache/nginx/scgi_temp --user=nginx --group=nginx --with-compat --with-file-aio --with-threads --with-http_addition_module --with-http_auth_request_module --with-http_dav_module --with-http_flv_module --with-http_gunzip_module --with-http_gzip_static_module --with-http_mp4_module --with-http_random_index_module --with-http_realip_module --with-http_secure_link_module --with-http_slice_module --with-http_ssl_module --with-http_stub_status_module --with-http_sub_module --with-http_v2_module --with-mail --with-mail_ssl_module --with-stream --with-stream_realip_module --with-stream_ssl_module --with-stream_ssl_preread_module --with-cc-opt='-O2 -g -pipe -Wall -Wp,-D_FORTIFY_SOURCE=2 -fexceptions -fstack-protector-strong --param=ssp-buffer-size=4 -grecord-gcc-switches -m64 -mtune=generic -fPIC' --with-ld-opt='-Wl,-z,relro -Wl,-z,now -pie'
[root@web01 ~]# nginx -v
nginx version: nginx/1.16.0
```

### 5.启动nginx

```sh
[root@web01 ~]# systemctl start nginx
#类似的开启 关闭 平滑重启命令
#systemctl start nginx ===== nginx
#systemctl reload nginx ===== nginx -s reload
#systemctl stop nginx ===== nginx -s stop
```

### 6.检查nginx进程

```sh
[root@web01 ~]# ps -ef|grep nginx
root       9161      1  0 10:51 ?        00:00:00 nginx: master process /usr/sbin/nginx -c /etc/nginx/nginx.conf
nginx      9162   9161  0 10:51 ?        00:00:00 nginx: worker process
root       9202   7641  0 10:52 pts/0    00:00:00 grep --color=auto nginx
```

### 7.nginx -t #检查语法

> 修改完nginx的配置文件一定要先检查语法再启动

```sh
[root@web01 ~]# nginx -t 
nginx: the configuration file /etc/nginx/nginx.conf syntax is ok
nginx: configuration file /etc/nginx/nginx.conf test is successful
```

## 2.nginx配置详解

# 虚拟主机

## 预备知识

### 1.什么是是虚拟主机？

- 一个虚拟主机相对于一个网站
- nginx中多个server标签就等于多个虚拟主机

### 2.nginx相关错误

- ping 域名
- curl 域名
- nginx配置及检查语法与reload平滑重启

## nginx主机的常见模型

### 基于域名的虚拟主机必备（必备）

> 不同的域名访问不同的虚拟主机（网站）

- 1.nginx配置文件进行配置

```sh
[root@web01 /etc/nginx/conf.d]# vim 01.www.conf 
 server   {
    listen      80;
    server_name  www.oldboy.com;
    access_log  /var/log/nginx/access_www.log  main;
    location / {
    root   /usr/share/nginx/html/www;
    index  index.html index.htm;
    }
}
[root@web01 /etc/nginx/conf.d]# vim 02.blog.conf 
 server   {
    listen      80;
    server_name  blog.oldboy.com;
    access_log  /var/log/nginx/access_blog.log  main;
    location / {
    root   /usr/share/nginx/html/blog;
    index  index.html index.htm;
    }
}
```

- 2.nginx -t 检查语法 没问题后重启

```sh
[root@web01 /etc/nginx/conf.d]# nginx -t
nginx: the configuration file /etc/nginx/nginx.conf syntax is ok
nginx: configuration file /etc/nginx/nginx.conf test is successful
[root@web01 /etc/nginx/conf.d]# systemctl reload nginx
```

- 3.创建不同的站点目录。一个网站一个目录

```sh
mkdir -p /usr/share/nginx/html/{blog,www}
```

- 4.创建主页文件

```sh
[root@web01 /etc/nginx/conf.d]# for n in www blog
> do
> echo $n.oldboy.com >/usr/share/nginx/html/$n/index.html
> done
You have new mail in /var/spool/mail/root
[root@web01 /etc/nginx/conf.d]# cat /usr/share/nginx/html/{blog,www}/index.html
blog.oldboy.com
www.oldboy.com
```

- 5.添加hosts解析

```sh
[root@web01 /etc/nginx/conf.d]# cat /etc/hosts
127.0.0.1   localhost localhost.localdomain localhost4 localhost4.localdomain4
::1         localhost localhost.localdomain localhost6 localhost6.localdomain6
172.16.1.5      lb01
172.16.1.6      lb02
172.16.1.7      web01 www.oldboy.com  blog.oldboy.com 
172.16.1.8      web02
172.16.1.31     nfs01
172.16.1.41     backup
172.16.1.51     db01 db01.etiantian.org
172.16.1.61     m01
```

- 6.curl 域名 检查

```sh
[root@web01 /etc/nginx/conf.d]# curl www.oldboy.com
www.oldboy.com
[root@web01 /etc/nginx/conf.d]# curl blog.oldboy.com
blog.oldboy.com
```

### 基于端口的虚拟主机（网站）

- 1.配置nginx文件

```sh
[root@web01 /etc/nginx/conf.d]# vim 01.www.conf 
 server   {
    listen      81;
    server_name  www.oldboy.com;
▽   access_log  /var/log/nginx/access_www.log  main;
    location / {
    root   /usr/share/nginx/html/www;
    index  index.html index.htm;
    }
}                                                                                   
[root@web01 /etc/nginx/conf.d]# vim 02.blog.conf 
 server   {
    listen      82;
    server_name  blog.oldboy.com;
    access_log  /var/log/nginx/access_blog.log  main;
    location / {
    root   /usr/share/nginx/html/blog;
    index  index.html index.htm;
    }
}
```

- 2.nginx检查语法之后重启

```sh
[root@web01 /etc/nginx/conf.d]# nginx -t
nginx: the configuration file /etc/nginx/nginx.conf syntax is ok
nginx: configuration file /etc/nginx/nginx.conf test is successful
You have new mail in /var/spool/mail/root
[root@web01 /etc/nginx/conf.d]# systemctl reload nginx
```

- 3.检查nginx端口

```sh
[root@web01 /etc/nginx/conf.d]# ss -lntup|grep nginx
tcp    LISTEN     0      128       *:80                    *:*                   users:(("nginx",pid=13621,fd=8),("nginx",pid=10510,fd=8))
tcp    LISTEN     0      128       *:81                    *:*                   users:(("nginx",pid=13621,fd=14),("nginx",pid=10510,fd=14))
tcp    LISTEN     0      128       *:82                    *:*                   users:(("nginx",pid=13621,fd=15),("nginx",pid=10510,fd=15))
```

- 4.检查结果

```sh
[root@web01 /etc/nginx/conf.d]# curl 10.0.0.7:81
www.oldboy.com
[root@web01 /etc/nginx/conf.d]# curl 10.0.0.7:82
blog.oldboy.com
```

### 基于ip的虚拟主机（网站）

- 1.配置nginx文件

```sh
[root@web01 /etc/nginx/conf.d]# vim 01.www.conf 
 server   {
    listen      10.0.0.7:80;
    server_name  www.oldboy.com;
    access_log  /var/log/nginx/access_www.log  main;
▽   location / {
    root   /usr/share/nginx/html/www;
    index  index.html index.htm;
    }
}
root@web01 /etc/nginx/conf.d]# vim 02.blog.conf 
 server   {
    listen     10.0.0.9:80;
    server_name  blog.oldboy.com;
    access_log  /var/log/nginx/access_blog.log  main;
    location / {
    root   /usr/share/nginx/html/blog;
    index  index.html index.htm;
    }
}
```

- 2.检查语法

```bash
[root@web01 /etc/nginx/conf.d]# nginx -t
nginx: the configuration file /etc/nginx/nginx.conf syntax is ok
nginx: [emerg] bind() to 10.0.0.9:80 failed (99: Cannot assign requested address)
nginx: configuration file /etc/nginx/nginx.conf test failed
```

> 出现错误提示，第一行提示我们语法没有问题，第二行提示无法分配ip，原因是我们配置文件配置的10.0.0.9这个ip是不存在的。可以给它临时添加一个。

- 3.添加ip

```sh
[root@web01 /etc/nginx/conf.d]# ip addr add 10.0.0.9/24 dev eth0  label eth0:1
```

> 这条命令含义是添加10.0.0.0.9这个ip 基于eth0这个网卡 给它起个小名叫eth:1

- 4.检查添加的ip

```sh
[root@web01 /etc/nginx/conf.d]# ip a s eth0
2: eth0: <BROADCAST,MULTICAST,UP,LOWER_UP> mtu 1500 qdisc pfifo_fast state UP group default qlen 1000
    link/ether 00:0c:29:0f:39:5c brd ff:ff:ff:ff:ff:ff
    inet 10.0.0.7/24 brd 10.0.0.255 scope global eth0
       valid_lft forever preferred_lft forever
    inet 10.0.0.9/24 scope global secondary eth0:1
       valid_lft forever preferred_lft forever
    inet6 fe80::20c:29ff:fe0f:395c/64 scope link 
       valid_lft forever preferred_lft forever
```

- 5.添加之后再检查语法 没问题就重启

```sh
[root@web01 /etc/nginx/conf.d]# nginx -t
nginx: the configuration file /etc/nginx/nginx.conf syntax is ok
nginx: configuration file /etc/nginx/nginx.conf test is successful
[root@web01 /etc/nginx/conf.d]# systemctl restart nginx
```

>  在这里就不能使用reload平滑重启了。
> reload一般只是从新读取一次配置文件。
> restart则是把进程停掉，从头启动一次。
> 所有有关ip的修改需要重启服务

- 6.curl 域名 检查

```sh
[root@web01 /etc/nginx/conf.d]# curl 10.0.0.7
www.oldboy.com
[root@web01 /etc/nginx/conf.d]# curl 10.0.0.9
blog.oldboy.com
```

### nginx处理用户请求过程

[http://nginx.org/en/docs/http/request_processing.html](https://links.jianshu.com/go?to=http%3A%2F%2Fnginx.org%2Fen%2Fdocs%2Fhttp%2Frequest_processing.html)

## nginx日志格式

```sh
    log_format  main  '$remote_addr - $remote_user [$time_local] "$request" '
                      '$status $body_bytes_sent "$http_referer" '
                      '"$http_user_agent" "$http_x_forwarded_for"';

    access_log  /var/log/nginx/access.log  main;
```

> log_format  用来定义日志格式
>  access_log 是用来开启，指定日志路径，调用日志格式的变量

> 这里可以使用的参数有main gzip buffer=16k  flush=5s
>  main 相对于给后面的日志格式定义了一个变量方便后面调用
>  gzip 对日志文件进行压缩
>  buffer=16k 相对于把日志临时放到内存中 最多能放16k的
>  flush=5s 相对于5秒钟将内存里的日志往硬盘里写一次，
>  `access_log /var/log/nginx/access_www-gzip.log main gzip buffer=16k flush=5s ;`

- ## 日志格式的每列含义

```sh
log_format  main          ##定义日志的格式 放到main变量的
$remote_addr -            ##客户端的地址
$remote_user              ## 远程用户（空）
$time_local]                 ##系统时间
$request" '                   ## 请求报文的起始行 $request_uri 只取出uri
'$status                       ##请求报文的起始行 $request_uri 只取出uri
'$status                        ## 状态码 

$status $body_bytes_sent "    ##服务端发给客户端大小（每个文件的大小）
$http_referer" '            ##  记录着用户从哪里跳转过来的
$http_user_agent" "     ##用户浏览器 

$http_x_forwarded_for"';   ##负载均衡： web服务器用来记录用户真实ip地址  
```

### nginx配置文件切割

### nginx一个server模块相对于一个虚拟主机，我们就可以为每一个网站创建一个文件,每个文件里写一个server模块

```sh
[root@web01 /etc/nginx/conf.d]# ll
total 16
-rw-r--r-- 1 root root 224 Jun  5 17:55 01.www.conf
-rw-r--r-- 1 root root 226 Jun  5 17:56 02.blog.conf
[root@web01 /etc/nginx/conf.d]# cat 01.www.conf 
 server   {
    listen      80;
    server_name  www.oldboy.com;
    access_log  /var/log/nginx/access_www.log  main;
    location / {
    root   /usr/share/nginx/html/www;
    index  index.html index.htm;
    }
}

You have new mail in /var/spool/mail/root
[root@web01 /etc/nginx/conf.d]# cat 02.blog.conf 
 server   {
    listen    80;
    server_name  blog.oldboy.com;
    access_log  /var/log/nginx/access_blog.log  main;
    location / {
    root   /usr/share/nginx/html/blog;
    index  index.html index.htm;
    }
}
```

### 在nginx主配置文件中调用

> include /etc/nginx/conf.d/*.conf

```sh
[root@web01 /etc/nginx]# tail nginx.conf

    sendfile        on;
    #tcp_nopush     on;

    keepalive_timeout  65;

    #gzip  on;

    include /etc/nginx/conf.d/*.conf;
}
```

### nginx的状态模块和权限控制

### 配置状态模块

## 权限控制 限制ip

```sh
[root@web01 /etc/nginx/conf.d]# cat /etc/nginx/conf.d/status.conf 
server {
   listen 89;     ##nginx状态我们并不想让所有人都能看，所以可以给它修改监听端口
   server_name status.oldboy.com;    ##指定域名
   stub_status on;                   ##开启状态
   access_log off;                   ##关闭日志
   allow 172.16.1.0/24;              ##只允许这个网段的访问
   deny all;                         ##其他网段的都不可以访问
}
```

### nginx -t 检查语法后reload重启

### 配置hosts解析

```sh
[root@web01 /etc/nginx/conf.d]# vim /etc/hosts
127.0.0.1   localhost localhost.localdomain localhost4 localhost4.localdomain4
::1         localhost localhost.localdomain localhost6 localhost6.localdomain6
172.16.1.5      lb01
172.16.1.6      lb02
172.16.1.7      web01 www.oldboy.com  blog.oldboy.com status.oldboy.com
172.16.1.8      web02
172.16.1.31     nfs01
172.16.1.41     backup
172.16.1.51     db01 db01.etiantian.org
172.16.1.61     m01
```

### curl 域名指定端口

```sh
[root@web01 /etc/nginx/conf.d]# curl status.oldboy.com:89
Active connections: 1 
server accepts handled requests
 3 3 3 
Reading: 0 Writing: 1 Waiting: 0 
```

> Active connections: 1 当前的连接数量（已经建立的连接）
> server accepts 服务器接收到的请求数量
> server handled 服务器接处理的请求数量
> server requests 用户一共向服务器发出多少请求
> Reading: 0 当前nginx正在读取的用户请求头的数量
> Writing: 1 当前nginx正在响应用户请求的数量
> Waiting: 0 当前等待被nginx处理的 请求数量

### 权限控制密码验证

```sh
server {
   listen 80;
   server_name status.oldboy.com;
   stub_status;
   access_log off;
   auth_basic "Auth access Blog Input your password";
   auth_basic_user_file /etc/nginx/htpasswd;
#   allow 172.16.1.0/24;
#   deny all;
}
```

### 创建密码文件

>  下载软件 yum install -y htpasswd. 并修改所有者为nginx和权限为600

```sh
[root@web01 /etc/nginx/conf.d]# htpasswd -bc /etc/nginx/htpasswd  oldboy   oldboy 
Adding password for user oldboy
[root@web01 /etc/nginx/conf.d]# ll /etc/nginx/htpasswd 
-rw-r--r-- 1 root root 45 Jun  6 09:15 /etc/nginx/htpasswd
[root@web01 /etc/nginx/conf.d]# chmod 600 /etc/nginx/htpasswd
[root@web01 /etc/nginx/conf.d]# ll /etc/nginx/htpasswd
-rw------- 1 root root 45 Jun  6 09:15 /etc/nginx/htpasswd
[root@web01 /etc/nginx/conf.d]# chown nginx.nginx /etc/nginx/htpasswd
[root@web01 /etc/nginx/conf.d]# ll /etc/nginx/htpasswd
-rw------- 1 nginx nginx 45 Jun  6 09:15 /etc/nginx/htpasswd
```

### ![1572839773696](nginx安装.assets/1572839773696.png)

### 取出本地的状态码

### 获取请求页面的请求头信息

```sh
[root@web01 /etc/nginx/conf.d]# curl  -I blog.oldboy.com
HTTP/1.1 200 OK
Server: nginx/1.16.0
Date: Fri, 07 Jun 2019 23:57:28 GMT
Content-Type: text/html
Content-Length: 16
Last-Modified: Wed, 05 Jun 2019 09:30:35 GMT
Connection: keep-alive
ETag: "5cf78bbb-10"
Accept-Ranges: bytes
```

### 想取出第一行的200

**1.直接使用管道加awk**

```sh
[root@web01 /etc/nginx/conf.d]# curl -I blog.oldboy.com|awk 'NR==1{print $2}'
  % Total    % Received % Xferd  Average Speed   Time    Time     Time  Current
                                 Dload  Upload   Total   Spent    Left  Speed
  0    16    0     0    0     0      0      0 --:--:-- --:--:-- --:--:--     0
200
```

>  发现确实取出来了，不过却多了一些额外的东西
> 这些信息表示从网站下载了多少东西和下载速度，可是这些信息我们一般不想要

**管道之前把这些信息定向到空**

```sh
[root@web01 /etc/nginx/conf.d]# curl -I blog.oldboy.com 2>/dev/null|awk 'NR==1{print $2}'
200
```

**使用-s参数**

```dart
[root@web01 /etc/nginx/conf.d]# curl -sI blog.oldboy.com|awk 'NR==1{print $2}'
200
```

> -s参数就表示静音模式。不输出任何东西。

**使用-w参数**

```cpp
[root@web01 /etc/nginx/conf.d]# curl -s -w "%{http_code}\n" blog.oldboy.com
blog.oldboy.com
200
```

> -w参数表示按指定的参数显示某一列 可是还多点东西，他吧网站内容也显示出来了。

**使用-o参数**

```dart
[root@web01 /etc/nginx/conf.d]# curl -s -w "%{http_code}\n" -o /dev/null blog.oldboy.com
200
```

> -o表示把-w指定的东西之外的不想要的东西放到一个文件中

## location匹配规则

## 2.1 location的作用

> 根据用户请求的URL来执行不同的应用，即URI的内容。

## 2.2 location语法

```ruby
location[=|~|~*|^~]url{
           ……
        }
```

## 2.3 location语法说明

| location | [=\|\|*\|^~] | url            | {……}                    |
| -------- | ------------ | -------------- | ----------------------- |
| 指令     | 匹配标识     | 匹配的网站网址 | 匹配URL后要执行的配置段 |

## 2.4 匹配标识分别代表的含义

| 匹配标识 | =    | ~                    | ~*                     | ^~                   |
| -------- | ---- | -------------------- | ---------------------- | -------------------- |
| 含义     | 精确 | 区分大小写的正则匹配 | 不区分大小写的正则匹配 | 不做正则表达式的检查 |

### location优先级测试

**配置文件修改**

```sh
[root@web01 /etc/nginx/conf.d]# vim 01.www.conf
server {
    listen       80;
    server_name  www.oldboy.com;
    root   html/www;
    location / {
       return 200  "location / \n";
    }
    location = / {
        return 200 "location = \n";
    }

    location /documents/ {
        return 200 "location /documents/ \n";
    }
    location ^~ /images/ {
        return 200 "location ^~ /images/ \n";

    }
    location ~* \.(gif|jpg|jpeg)$ {
        return 200 "location ~* \.\(gif|jpg|jpeg) \n";
    }
    access_log off;
}
```

> 例子：return 200 "location /documents/ \n"
>  表示符合规则后显示出状态码和引号里的内容 /n 表示换行
>  测试优先级更直观

```ruby
[root@web01 /etc/nginx/conf.d]# curl 10.0.0.7/oldboy.html 
location / 
[root@web01 /etc/nginx/conf.d]# curl 10.0.0.7/documents/alex.txt
location /documents/ 
[root@web01 /etc/nginx/conf.d]# curl 10.0.0.7/lidao/documents/alex.txt
location / 
[root@web01 /etc/nginx/conf.d]# curl 10.0.0.7/oldboy.jpg
location ~* \.(gif|jpg|jpeg) 
```

### 优先级验证

```ruby
#验证/documents 与 ~* 优先级 
[root@web01 /etc/nginx/conf.d]# curl 10.0.0.7/documents/oldboy.jpg 
location ~* \.(gif|jpg|jpeg) 
#验证 ~* 与 ^~  优先级
[root@web01 /etc/nginx/conf.d]# curl 10.0.0.7/images/oldboy.jpg 
location ^~ /images/ 
```

> 总结：当^~  和 ~* 都满足条件时 ^~的优先级更高。

### 优先级排名

```csharp
=
^~  匹配的不匹配正则   优先匹配 （更优先）
~*   匹配正则不区分大小写
/documents 
/ 
```

## 2.5 rewrite 工作案例

```sh
原始URL: http://127.0.0.1:8914/batch_no/11122asbc.jpeg
连接实现跳转到
目标:http://127.0.0.1:8914/email_open_check?
batch_no=11122asbc,这个咋实现?
分析规律
11122asbc.jpeg文件不同的文件实现连接
http://127.0.0.1:8914/email_open_check?batch_no=文件名字
通过正则 取出url中文件的名字 在后面使用 $1 引用
rewrite ^/batch_no/([0-9A-Za-Z]+)\.jpeg   
http://127.0.0.1:8914/email_open_check?batch_no=$1   
permenant;
sed  -r  's#xxx(.*)ooo#\1#g'
```

```sh
#if + rewrite工作案例
#用户浏览器类型是android 并且 用户ip范围是10.0.xx.xxx 执行
rewrite规则
if () {
   if () {
   
   }
}

##标记法
        set $flag 0; 设置变量 $flag 值 0; 
        if ( $remote_addr ~ "^10\.0\."){    
            set $flag "${flag}1";     
            #set $flag "01";   $flag 01
            #如果 ip符合规则 则 $flag的内容是01
         }
        if ($http_user_agent ~* "android"){  #如果用户的
客户端是android
 set $flag "${flag}2";    
         #set $flag "012";  
         # set $flag 012     $flag的内容012
           }
        if ($flag = "012"){
        return 200;
   }
```

