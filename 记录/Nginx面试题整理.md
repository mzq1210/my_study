# nginx面试题整理

[TOC]

### 什么是nginx？

Nginx是一个web服务器和方向代理服务器,缓存服务器，用于HTTP、HTTPS、SMTP、POP3和IMAP协议。

### 为什么使用nginx

跨平台，配置简单，非阻塞，高并发连接：处理2-3万并发连接数，官方监测能支持5万并发。

内存消耗小：开启10个nginx才占150M内存，nginx处理静态资源好，消费内存少.

内置的简单检查功能:如果一个服务器宕机,会做一个健康检查,再发送的请求就不会发送到宕机的服务器了,从新将请求提交给其他节点上.
 节省带宽：支持gzip压缩，可以添加浏览器本地缓存。

稳定性高：宕机的概率非常小。

接收用户请求是异步的：浏览器将请求发送给nginx服务器，他将用户请求全部接收下来，再一次性发送给后端的web服务器，极大减轻了web服务器的压力，一遍接收web服务器的返回数据，一边发送给浏览器客户端，网络依赖性比较低，只有ping通就可以负载均衡，可以用多台nginx服务器，使用dns做负载均衡，时间驱动，通信机制采用epoll模型。

### nginx是如何处理一个请求的？

首先，nginx在启动时，会解析配置文件，得到需要监听的端口与ip地址，然后在nginx的master进程里面先初始化好这个监控的socket，再进行listen,然后再fork出多个子进程出来,  子进程会竞争accept新的连接。此时，客户端就可以向nginx发起连接了。当客户端与nginx进行三次握手，与nginx建立好一个连接后,此时，某一个子进程会accept成功，然后创建nginx对连接的封装，即ngx_connection_t结构体,接着，根据事件调用相应的事件处理模块，如http模块与客户端进行数据的交换。最后，nginx或客户端来主动关掉连接，到此，一个连接就寿终正寝了

### 为什么nginx性能这么高

得益于它的事件处理机制：异步非阻塞事件处理机制，运用了epoll模型，提供了一个队列，排队解决。

### nginx的负载均衡算法都要哪些？

> nginx常用的支持4种负载均衡调度算法(策略)有：轮训、ip_hash、最少连接、权重算法。
> 另外有2种是第三方的：fair（第三方） 响应时间方式 ，url_hash（第三方） 依据URL分配方式

nginx的upstream目前支持4种方式的分配

0）**轮询(round-robin默认):每个请求按时间顺序逐一分配到不同的后端服务器,也是最简单的配置算法;如果后端服务器down掉，能自动剔除。**

```sh
打开 nginx 配置文件（请以你的安装路径为主）
vi /usr/local/nginx/conf/nginx.conf
编写轮训配置，设定负载均衡服务器列表：

upstream testround.com {
server 192.168.1.1:8080 ;
server 192.168.1.2:8081 ;
server 192.168.1.3:8082 ;
}
#当访问 http://192.168.188 的时候，会把这个请求负载到 192.168.1.1 的 8080 端口、192.168.1.2 的 8080 端口、192.168.1.3 的 8080 端口。每个请求按时间顺序逐一分配到不同的后端服务器。
```

1）weight 权重轮询

**指定轮询几率，weight和访问比率成正比，用于后端服务器性能不均的情况。weight的值越大分配到的访问概率越高，主要用于后端每台服务器性能不均衡的情况下，或者仅仅为在主从（2台服务器）的情况下设置不同的权值，达到合理有效的地利用主机资源。**

```sh
upstream testweight.com {
#后端服务器访问规则
server 192.168.1.1:8080 weight=2;
server 192.168.1.2:8081 weight=2;
}
```

2）**ip_hash：每个请求按访问IP的hash值结果进行分配，同一个IP客户端固定访问一个后端服务器。可以保证来自同一ip的请求被打到固定的机器上，可以解决session问题。如果后端服务器down掉，要手工down掉。**

```sh
upstream testiphash.com {
#后端服务器访问规则
ip_hash;
server 192.168.1.1:8080 ;
server 192.168.1.2:8081 ;
server 192.168.1.3:8082 ;
}
```

3）最少连接（least_conn）

**把请求转发给连接数较少的后端服务器进行处理。例如Nginx负载中配置了两台服务器，sky和fans，当Nginx接收到一个请求时，sky正在处理的请求数是100，fans正在处理的请求数是200，则Nginx会把当前请求交给sky来处理。**

```sh
upstream testleastconn.com {
least_conn;
server 192.168.1.1:8080;
server 192.168.1.2:8081;
}
```

4）fair：按后端服务器的响应来分配请求，响应时间短的优先分配。

5）url_hash：**根据url的hash结果分配请求，是url定向到同一服务器，在upstream中加入hash语句后，server语句不能写入weight等其他参数，这种算法一般在后端缓存的时候比较适合**。

### nginx的upstream中的ip_hash和url_hash的区别和特点。

ip_hash：每次请求访问，按照ip的hash结果分配，这样每个访客固定访问一台后端服务器，可以解决session的问题。

url_hash: 根据url的hash结果分配请求，使url定向到同一服务器，在upstream中加入hash语句后，server语句中不能写入weight等其他参数，这种算法一般在后端缓存的时候比较适合。

### nginx和apache的区别

轻量级，同样起web服务，比apache占用更少的内存和资源。

抗并发，nginx处理请求是异步非阻塞的，而apache则是阻塞性的，在高并发下nginx能保持低资源，低消耗高性能。

高度模块化的设计，编写模块相对简单。

最核心的区别在于apache是同步多进程模型，一个连接对应一个进程，nginx是异步的，多个连接可以对应一个进程。

| Nginx                                       | Apache                                                       |
| ------------------------------------------- | ------------------------------------------------------------ |
| - nginx是一个基于事件的web服务器            | apache是一个基于流程的服务器                                 |
| 所有请求都由一个线程处理                    | 单个线程处理单个请求                                         |
| nginx避免子进程的概念                       | apache是基于子进程的                                         |
| nginx类似于速度                             | apache类似于功率                                             |
| nginx在内存消耗和连接方面比较好             | apache在内存消耗和连接上没有提高                             |
| nginx在负载均衡方面表现较好                 | 当流量到达进程极限时，apache将拒绝新的连接。                 |
| 对于php来说，nginx可能更可取，因为它支持php | apache支持php，python，perl和其他语言使用插件，当应用程序基于python或ruby时，它非常有用。 |
| nginx不支持IBMI和openvms一样的os            | apache支持更多的os                                           |
| nginx只具有核心功能                         | apache提供了比nginx更多的功能                                |
| nginx的性能和可伸缩性不依赖于硬件           | apache依赖于cpu和内存等硬件组件。                            |

### 什么是正向代理和反向代理

一个位于客户端与原始服务器之间的服务器，为了从原始服务器取得内容，客户端向代理发送了一个请求并指定目标（原始服务器），然后代理向原始服务器转交请求并将获得的内容返回给客户端，客户端才能使用正向代理。正向代理总结一句话就是：代理端代理的是客户端

反向代理服务器作用在服务端，他在服务器端接收客户端的请求，然后将请求分发给具体的服务器进行处理，然后再将服务器的相应结果反馈给客户端，nginx就是一个反向代理服务器软件。

### 负载均衡

负载均衡即是代理服务器将接收的请求均衡的分发到各服务器中，负载均衡主要解决网络拥塞问题，提高服务器响应速度，服务就近提供，达到更好的访问质量，减少后台服务器大并发压力。

### 动态资源，静态资源分离？

动态资源，静态资源分离是让动态网站里的动态网页根据一定规则把不变的资源和经常变的资源区分开来，动静资源做好了拆分以后，我们就可以根据静态资源的特点将其做缓存操作，这就是网站静态化的核心思路，动态资源，静态资源分离简单概括是：动态文件和静态文件的分离。

### 为什么要做动静分离？

在我们的软件开发中，有些请求是需要后台处理的，有些请求不需要后台处理，这些不需要经过后台处理的文件称为静态文件，因此我们后台处理忽略静态文件。这会有人说那我后台忽略静态文件不就完了吗，当然这是可以的，但是这样后台的请求次数明显增多了，在我们对资源的响应速度有要求的时候，我们应该使用这种动静分离的策略去解决，动，静分离将网站静态资源与后台分开部署，提高用户访问静态代码的速度，降低对后台应用的访问，这里我们将静态资源放在nginx中，动态资源转发到tomcat服务器中。

### 请解释ngx_http_upstream_module作用是什么？

允许定义一组服务器。它们可以在指令[proxy_pass](https://links.jianshu.com/go?to=http%3A%2F%2Ftengine.taobao.org%2Fnginx_docs%2Fcn%2Fdocs%2Fhttp%2Fngx_http_proxy_module.html%23proxy_pass)、 [fastcgi_pass](https://links.jianshu.com/go?to=http%3A%2F%2Ftengine.taobao.org%2Fnginx_docs%2Fcn%2Fdocs%2Fhttp%2Fngx_http_fastcgi_module.html%23fastcgi_pass)和 [memcached_pass](https://links.jianshu.com/go?to=http%3A%2F%2Ftengine.taobao.org%2Fnginx_docs%2Fcn%2Fdocs%2Fhttp%2Fngx_http_memcached_module.html%23memcached_pass)中被引用到。

### 请解释什么是C10K问题？

C10K问题是指无法同时处理大量客户端的网络套接字。

### 请陈述stub_status和sub_filter指令的作用是什么?

stub_status 指定：该指令用于了解nginx当前状态，如当前的活动链接，

接受和处理当前读/写/等待的总数。

sub_filter指定：它用于搜索和替换响应中的内容，并快速修复陈旧的数据

### 解释nginx是否支持将请求压缩到上游

你可以使用nginx模块gunzip将请求压缩到上游，gunzip模块是一个过滤器，他可以对不支持gzip编码方法的客户机或服务器使用内容编码：gzip来解压缩响应。

### 用Nginx服务器解释-s的目的是什么?

用于运行nginx -s参数的可执行文件



### 请列举nginx的一些特性？

跨平台：可以在unix系统编译运行，而且有windows的移植版本。

配置简单：非常的简单，易上手。

非阻塞高并发连接：数据复制时，磁盘I/O的第一阶段是非阻塞的。官方测试能支持5万并发连接，实际生产中能跑2-3万并发连接数。发送报文是，nginx是一边接受web服务器的返回数据，一边把数据发送给客户端浏览器。

自带健康检查：当有服务器宕机后，新的请求就不会发送到这台机器上了，而是发送到其他节点。

节省带宽：支持gzip压缩，开启开启浏览器缓存。

网络依赖性低，理论上只要能够ping通就可以实施负载均衡，而且可以有效区分内网、外网流量。

内存消耗少，稳定性高：开启10个nginx消耗内存125M,可以很好的处理静态资源，内存消耗少。宕机率很低。

### 在nginx中，如何使用未定义的服务器名称来阻止处理请求？

只需将请求删除的服务器就可以定义为：

```sh
Server {

listen 80;

server_name “ “ ;

return 444;

}
```

这里，服务器名被保留为一个空字符串，它将在没有“主机”头字段的情况下匹配请求，而一个特殊的Nginx的非标准代码444被返回，从而终止连接。

### 请列举Nginx服务器的最佳用途

nginx服务器的最佳用法是在网络上部署动态HTTP内容，使用SCGI,WSGI应用程序服务器，用于脚本的fastcgi处理程序，他还可以作为负载均衡器。

### 请解释nginx服务器上的master和worker进程分别是什么？

nginx的master和worker进程之间的关系，就像是一家饭店的服务员和老板的关系。

加入有一个饭店有多个服务员，而管理这些服务员的老板一个人，其中老板负载对外招揽业务，而服务员负责干活，如果一个服务员接待不完这些客人，老板会把随后的客人交给其他的服务员接待。

在这里，老板就属于master进程，客户端所有的请求都是由master来接收，服务员就相当于worker进程。

Master进程：读取及评估配置和维持

Worker进程：处理请求

### 生产中如何设置worker进程的数量呢？

在有多个cpu的情况下，可以设置多个worker，worker进程的数量可以设置到和cpu的核心数一样多，如果在单个cpu上起多个worker进程，那么操作系统会在多个worker之间进行调度，这种情况会降低系统性能，如果只有一个cpu，那么只启动一个worker进程就可以了。

### Last-Modified,Expires,Max-age,Etag他们的含义，作用于浏览器端的是那些？作用于服务端的是那些？

Last-Modified：标记浏览器请求的URL对应的文件在服务器端最后被修改的时间。

Expires：需要和last-Modified结合使用，用于控制请求文件的有效日期，当请求数据在有效期内时客户端从缓存请求数据而不是服务器端，当缓存中数据失效或过期，才决定从服务器更新数据。

Max-age:指定的是文档被访问后的存活时间，这个时间是一个相对值（比如3600s）,相对的是文档第一次被请求时服务器记录的request_time（请求时间）

Etag:服务器响应时，给url标记，并在http响应头中将其传送到客户端。

### cookie和session区别？

共同：

存放用户信息。存放的形式：key-value格式 变量和变量内容键值对。

区别：

cookie

存放在客户端浏览器

每个域名对应一个cookie，不能跨跃域名访问其他cookie

用户可以查看或修改cookie

http响应报文里面给你浏览器设置

钥匙（用于打开浏览器上锁头）

session:

存放在服务器（文件，数据库，redis）

存放敏感信息

锁头

### 为什么nginx不使用多线程？

Apache：创建多个进程和线程，而每个进程或线程都会为其分配cpu和内存（线程要比进程小的多,多以worker支持比perfork高的并发），并发多大会消耗光服务器资源。

Nginx:采用单线程来异步非阻塞处理请求（管理员可以配置nginx主进程的工作进程数量）（epoll），不会为每个请求分配cpu和内存资源，节省了大量资源，同时也减少了大量的cpu的上下文切换。所以才使得nginx支持更高的并发。

### nginx常见的优化配置有哪些？

##### 安全优化：

###### 隐藏nginx版本信息优化：修改nginx配置文件实现优化。

server_tokens off;

###### 修改nginx进程用户信息：

修改默认进程用户nginx为其他，如www.

###### 修改nginx服务上传文件限制：

client_max_body_size 设置客户端请求报文主体最大尺寸，用户上传文件 大小。

###### nginx图片及目录防盗链解决方法

根据HTTP referer实现防盗链

用户从哪里跳转过来的（通过域名）referer控制

根据cookie防盗链

###### nginx站点目录文件及目录权限优化

只将用户上传数据目录设置为755用户和组使用nginx

其余目录和文件为755/644，用户和组使用root

###### 使用普通用户启动nginx

利用nginx -c参数启动nginx多实例，使master进程让普通用户管理。普通用户无法使用1-1024端口。使用iptables转发。

###### 控制nginx并发连接数

###### 控制客户端请求nginx的速率

##### 性能优化：

###### 1.调整worker_processes

指nginx要生成的worker数量，一般和cpu的核心数设置一致，高并发可以和cpu核心2倍.

cat /proc/cpuinfo

###### 2.优化nginx服务进程均匀分配到不同cpu进行处理。

利用worker_cpu_affinity进行优化让cpu的每颗核心平均。

###### 3.优化nginx事件处理模型

利用use epoll参数修改事件模型为epoll模型。

事件模型指定配置参数放置在event区块中

###### 4.优化nginx单进程客户端连接数

利用worker_connections连接参数进行调整

用户最大并发连接数=worker进程数*worker连接数

###### 5.优化nginx服务进程打开文件数

利用worker_rlimit_nofile 参数进行调整

###### 6.优化nginx服务数据高效传输模式。

利用sendfile on开启高速传输模式。

tcp_nopush on 表示将数据积累到一定的量再进行传输。

tcp_nopush on 表示将数据信息进行快速传输

###### 7.优化nginx服务超时信息。

keepalive_timeout 优化客户端访问 nginx服务端超时时间。

http协议特点：连接断开后会给你保留一段时间

### nginx常用模块

access 访问模块

auth  认证模块

gzip 压缩模块

proxy  代理模块

upstream  负载均衡

rewrite 重写模块

log 日志模块

limit conn现在用户访问并发连接

ssl模块

autoindex 开启目录浏览

### location匹配的优先级别

1.首先精准匹配 =

2.其次前缀匹配 ^~

3.其次是按文件中的顺序正则匹配

4.然后配置不带任何修饰的前缀匹配

5.最后交给/ 通用匹配

6.当有匹配成功时，停止匹配，按当前匹配规则处理请求。

### 使用“反向代理服务器”的优点是什么?

反向代理服务器可以隐藏源服务器的存在和特征。它充当互联网云和web服务器之间的中间层。这对于安全方面来说是很好的，特别是当您使用web托管服务时。

### 请解释是否有可能将Nginx的错误替换为502错误、503?

502 =错误网关

503 =服务器超载

有可能，但是您可以确保fastcgi_intercept_errors被设置为ON，并使用错误页面指令。

Location / {

fastcgi_pass 127.0.01:9001;

fastcgi_intercept_errors on;

error_page 502 =503/error_page.html;

### 在Nginx中，解释如何在URL中保留双斜线?

要在URL中保留双斜线，就必须使用merge_slashes_off;

语法:merge_slashes [on/off]

默认值: merge_slashes on

环境: http，server

### 解释如何在Nginx中获得当前的时间?

要获得Nginx的当前时间，必须使用SSI模块、$date_gmt和$date_local的变量。

Proxy_set_header THE-TIME $date_gmt;

### 解释如何在Nginx服务器上添加模块?

在编译过程中，必须选择Nginx模块，因为Nginx不支持模块的运行时间选择。