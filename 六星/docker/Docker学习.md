一、

#### Centos 安装docker

```bash
#1、更新软件包
yum  update
#2、卸载老版本docker
yum  remove docker docker-common docker-selinux docker-engine
#3、安装需要的软件包
yum install -y yum-utils device-mapper-persistent-data lvm2
#4、设置yum源
yum-config-manager --add-repo https://download.docker.com/linux/centos/docker-ce.repo
#5、查看docker可安装版本列表
yum list docker-ce --showduplicates|sort -r
#6、指定版本
yum install docker-ce-18.03.1.ce -y
#7、启动docker
systemctl start docker
#查看状态
systemctl status docker
#8、加入开机自启
systemctl enable docker
#9、配置国内镜像
vi /etc/docker/daemon.json 
{
"registry-mirrors": ["http://hub-mirror.c.163.com"]
}
#10.更换docker国内源重启
systemctl restart docker
```

#### docker常用指令

```bash
#************************** 镜像相关 **************************
#删除镜像，i代表image
docker rmi  $(docker ps -a -q) 

#************************** 容器相关 **************************
#1.启动容器
docker start 容器名
#2.删除容器
docker rm 容器名
#3.停止/删除所有容器
docker stop  $(docker ps -a -q)
docker rm  $(docker ps -a -q) 

#4.根据Dockerfile构建镜像
docker build -t 镜像名称 .
-t ，--tag list  #构建后的镜像名称
-f， --file string #指定Dockerfiile文件位置
--no-cache #不从缓存构建
#示例：
1，docker build . 
2，docker build -t redis:v1 .
3，docker build -t redis:v2 -f /path/Dockerfile /path

#5.查看容器信息
docker inspect 容器名
#6.进入容器
docker exec -it 容器 bash
#7.重启容器，两种方式
#exec代表在容器在执行一个命令
docker restart nginx
docker exec nginx nginx -s load
#8.导出容器
docker export 已存在的容器id/name > 文件名.tar
docker save 已存在的容器id/name > 文件名.tar
#9.导入容器
docker import 文件名.tar 容器自定义name
docker load < 文件名.tar

#************************** 镜像/容器日志排错 **************************
#构建镜像历史记录
docker history 镜像ID
#构建容器日志
docker logs 容器id

#Dockerfile调错：有一种情况是无法查看日志，一构建就退出，找不到原因，那就先把dockerfile文件中的CMD命令注释掉，docker run 构建的命令最后加上bash,直接进入容器运行应用启动命令

#10、使用此docker镜像 创建容器
docker run -itd --name  redis-master  --net mynetwork  -p 6380:6379  --ip 192.168.1.2  redis 
-d:     后台运行容器，并返回容器ID；  
-i:     以交互模式运行容器，通常与 -t 同时使用；
-p:     端口映射，格式为：主机(宿主)端口:容器端口
-t:     为容器重新分配一个伪输入终端，通常与 -i 同时使用；
-V:     为容器挂载目录，比如 /usr/docker/data:/data 前者为数宿主机目录后者为容器内目录
--ip:   为容器制定一个固定的ip 
--net:  指定网络模式
```

> 注意容器导入导出的区别，两对命令都能导入导出，save方式导出的文件比export方式导出的文件大，因为save方式保存了镜像的历史和层，使其可以回滚到之前的历史层。反观export方式，在导出过程中丢失所有的历史，导致其不可以层回滚，导出的文件会小一些。

#### 容器网络

```bash
#Docker安装后，默认会创建三种网络类型，查看所有：
docker network ls

#默认情况下启动Docker容器都是使用桥接方式bridge，每次Docker容器重启时，会按照顺序获取对应的IP地址，这个就导致重启下，Docker的IP地址就变了。所以我们需要创建自定义网络并指定网段：192.168.1.0/24，命名为mynetwork
docker network create --subnet=192.168.1.0/24 mynetwork

#查看自定义网络信息
docker network inspect mynetwork
#查看某个容器的网络信息
docker network inspect 容器id

#查看端口的绑定情况
iptables -t nat -L -n
```

> **Mac上docker-connector给容器设置固定ip：**
>
> docker创建网卡之后把子网ip加入 /usr/local/etc/docker-connector.conf，无需重启

#### 配置redis主从

原理步骤，可以通过redis的log日志查看：

1. 在【从】节点设置slaveof的时候保存主节点信息
2. 【从】节点会通过定时任务不断请求【主】节点建立socket连接
3. 【从】节点会发送ping命令，ping不通的话也会通过定时任务不断请求
4. 权限验证，验证密码等
5. 同步数据集，这里第一次是全量复制，主节点压力会非常大，可能影响正常业务
6. 命令持续复制，增量复制。如果存在多个【从】节点，数据同步是主节点依次向每个【从】节点推送数据，而不是从节点去获取，并且是一个一个推，而不是同时推

```bash
#1.通过docker命令分别运行redis容器
docker run -it  --name  redis-master  --net mynetwork  -p 6380:6379  --ip 192.168.1.2 -v /home/redis/master:/usr/src/redis redis 
docker run -it  --name  redis-slave  --net mynetwork  -p 6381:6379   --ip 192.168.1.3 -v /home/redis/slave:/usr/src/redis redis 

#2.在【从机】上连接【主机】，如果【主】redis没有设置密码，需要关闭它配置文件中的保护模式：protected-mode no（protected-mode代表禁止公网访问redis）
redis-cli -a 123456
SLAVEOF 192.168.1.2 6379

#3.进入【从机】查看redis连接状态
redis-cli -a 123456
info replication
# 这是【从】节点查看的主要参数
  -role				//角色
  -master_host		//【主机】ip
  -master_port
  -master_link_status //【主机】连接状态，up为正常
  
#4.【主】节点查看
info replication
#这是【主】节点查看的主要参数
  -role					//角色
  -connected_slaves		//连接的【从】机数量
  -slave0：ip=192.168.0.3,port=6379,state=online,offset=46546,lag=0
  # state=online 为正常
  # offset=46546 从节点偏移量
  -master_repl_offset	//主节点偏移量，主要用于记录同步推送时的偏移位置

#5.因为【从】节点只能读取，无法设置变量，则在【主】节点设置一个
set test 1111
#【从】节点查看是否成功
keys *
```

#### 主从配置参数参考

```bash
###########  从库配置文件  ##############
#设置该数据库为其他数据库的从数据库 
slaveof <masterip> <masterport>
#主从复制中，设置连接master服务器的密码（前提master启用了认证） 
masterauth <master-password>
slave-serve-stale-data yes

# 当从库同主库失去连接或者复制正在进行，从库有两种运行方式：
# 1) 如果slave-serve-stale-data设置为yes(默认设置)，从库会继续相应客户端的请求
# 2) 如果slave-serve-stale-data设置为no，除了INFO和SLAVOF命令之外的任何请求都会返回一个错误"SYNC with master in progress" 

#当主库发生宕机时候，哨兵会选择优先级最高的一个称为主库，从库优先级配置默认100，数值越小优先级越高
slave-priority 100
#从节点是否只读；默认yes只读，为了保持数据一致性，应保持默认 
slave-read-only yes


########  主库配置文件  ##############
#在slave和master同步后（发送psync/sync），后续的同步是否设置成TCP_NODELAY假如设置成yes，则redis会合并小的TCP包从而节省带宽，但会增加 同步延迟（40ms），造成master与slave数据不一致假如设置成no，则redis master会立即发送同步数据，没有延迟
#前者关注性能，后者关注一致性 
repl-disable-tcp-nodelay no
#从库会按照一个时间间隔向主库发送PING命令来判断主服务器是否在线，默认是10秒 
repl-ping-slave-period 10

#复制积压缓冲区大小设置 
repl-backlog-size 1mb

#master没有slave一段时间会释放复制缓冲区的内存，repl-backlog-ttl用来设置该时间长度。单位为秒。 
repl-backlog-ttl 3600

#redis提供了可以让master停止写入的方式，如果配置了min-slaves-to-write，健康的slave的个数小于N，mater就禁止写入。master最少得有多少个健康的slave存活才能执行写命令。这个配置虽然不能保证N个slave都一定能接收到master的写操作，但是能避免没有足够健康的slave的时候，master不能写入来避免数据丢失。设置为0是关闭该功能。
min-slaves-to-write 3 
min-slaves-max-lag 10
```

#### 各种主从问题请参考docker第4节笔记

> redis.conf需要注意的设置
> 如果主redis没有设置密码，就需要关闭他的保护模式：protected-mode no

```bash
#查看runid
info server

#用第一个【从机1】的配置增加一台【从机2】，为什么【从机2】没有和【主机】建立连接就有了数据？
#因为【从机2】加载了【从机1】data目录下的数据文件dump.rdb
```

从节点开启主从复制，有3种方式：

> （1）配置文件（一般不推荐）
> 在从服务器的配置文件中设置：slaveof <masterip> <masterport>
> （2）启动命令（推荐）
> redis-server启动命令后加入    --slaveof <masterip> <masterport>
> （3）客户端命令
> Redis服务器启动后，直接通过客户端执行命令：slaveof <masterip> <masterport>
> 通过 info replication 命令可以看到复制的一些参数信息

#### 全量复制过程

> **runid(服务器运行ID)：**每个Redis节点(无论主从)，在启动时都会自动生成一个随机ID(每次启动都不一样)，由40个随机的十六进制字符组成；runid用来唯一识别一个Redis节点。  通过 info server 命令，可以查看节点的runid
>
> **复制积压缓冲区：**主节点在复制的时候也需要向外提供服务，这段时间的写命令无法同步，所以需要暂存到复制积压缓冲区，缓冲区默认大小为1MB，由于复制积压缓冲区定长且先进先出，所以它保存的是主节点最近执行的写命令，时间较早的写命令会被挤出缓冲区。

1、Redis【从】节点内部会发出一个同步命令，刚开始是Psync 命令，Psync ? -1表示要求 master 主机同步数据
2、【主】机会向【从】机发送 runid 和 offset，因为 slave 并没有对应的 offset，所以是全量复制
3、从机 slave 会保存主机master 的基本信息 save masterInfo
4、主节点收到全量复制的命令后，执行bgsave（异步执行），在后台生成RDB文件（快照，4.0之后不需要这个文件也能推送），并使用一个复制积压缓冲区，记录从现在开始执行的所有写命令。
5、主机send RDB 发送 RDB 文件给从机
6、RDB 文件发送完成后，发送缓冲区数据
7、刷新旧的数据，从节点在载入主节点的数据之前要先将所有老数据清除
8、加载RDB 文件将数据库状态更新至主节点执行bgsave时（快照）的数据库状态和缓冲区数据的加载。

> 全量复制开销，主要有以下几项。
> bgsave 时间
> RDB 文件网络传输时间 
> 从节点清空数据的时间 
> 从节点加载 RDB 的时间

#### 部分复制过程

> Redis 2.8 以后出现的，之所以要加入部分复制，是因为全量复制会产生很多问题，比如像上面的时间开销大、无法隔离等问题，  Redis 希望能够在 master 出现抖动（断开连接）的时候，可以有一些机制将复制的损失降低到最低。

1、如果网络抖动（连接断开connection lost）
2、主机master 还是会写 replbackbuffer（复制缓冲区）
3、从机slave 会继续尝试连接主机
4、从机slave 会把自己当前 runid 和偏移量传输给主机 master，并且执行 pysnc 命令同步
5、如果 master 发现你的偏移量是在缓冲区的范围内，就会返回 continue 命令
6、同步了 offset 的部分数据，所以部分复制的基础就是偏移量 offset。

#### redis如何决定是全量复制还是部分复制？
> 从节点将offset发送给主节点后，主节点根据offset和缓冲区大小决定能否执行部分复制:
> 如果offset偏移量之后的数据，仍然都在复制积压缓冲区里，则执行部分复制；
> 如果offset偏移量之后的数据已不在复制积压缓冲区中（数据已被挤出），则执行全量复制。

#### 设置复制积压缓冲区大小（【主机】）
> 为了提高网络中断时部分复制执行的概率，可以根据需要增大复制积压缓冲区的大小(通过配置repl-backlog-size)来设置。
> 例如：如果网络中断的平均时间是60s，而主节点平均每秒产生的写命令占的字节数为100KB，则复制积压缓冲区的平均需求为6MB，保险起见可以设置为12MB，来保证绝大多数断线情况都可以使用部分复制。
> repl-backlog-size 在redis的配置文件中，默认为 1mb，不过一般够用了。注意这个是网络断开的情况下才有用，因为从节点的runid变了



#### [Predis](https://packagist.org/search/?query=predis)简单使用

```php
#首先需要安装
composer require predis/predis

#使用
require 'vendor/autoload.php';
include "config.php";

$redis=new Predis\Client([
  'tcp://118.24.109.254:6380?alias=master',
  'tcp://118.24.109.254:6381?alias=slave-01'
],[
    'replication'=>true,
    'parameters' => [
        'password' => '123456',
  ]
]);
echo $redis->set('test',123);
var_dump($redis->get('test'));
```



#### 五、持久化处理（看笔记即可）

##### 持久化的选择

1.如果Redis只用作DB层数据的cache，则完全不需要持久化处理

2.如果可以接受十多分钟的数据丢失，可以选择RDB；如果对数据的安全性要求较高，则可以使用AOF

3.对于主从环境，主服务器可以完全关闭持久化，从服务器可以开启AOF，并使用定时任务对持久化文件进行备份，然后关闭AOF的自动重写（因为占用资源太大），然后添加定时任务，手动在每天Redis空闲时调用bgrewriteaof命令。

4.应尽量避免“自动拉起机制”和“不做持久化”同时出现。因为那样会清空内存中的数据，从节点同步之后也会清空。

如果做了持久化，主节点就可以自动重启。

5.异地灾备，一般来说，由于RDB文件文件小、恢复快，因此灾难恢复常用RDB文件；异地备份的频率最好不要低于 
一天一次。

#### 七、docker-compose

##### <font face="微软雅黑"  color = #42A5F5 > Curl方式下载新的版本 </font>
```bash
#下载，如果太慢可以去 http://get.daocloud.io/ 下载
curl -L https://github.com/docker/compose/releases/download/1.25.1/docker-compose-`uname -s`-`uname -m` > /usr/local/bin/docker-compose  

#修改权限
chmod +x /usr/local/bin/docker-compose

#安装完成后可以查看版本
docker-compose --version

#编写docker-compose.yaml文件并构建
docker-compose up -d

#停止所有
docker-compose stop

#查看主从
info replication

#删除所有，需要再确认一下
docker-compose rm

#查看所有
docker-compose ps

#查看日志
docker-compose logs

#err:自己构建的redis镜像编排的时候排错，查看日志也没有任何消息，因为日志目录映射到了宿主机，可以把容器中的日志下载下来看，即使容器没启动
```

#### 8.哨兵+主从方案

> 适用于一主多从，但解决不了内存不够的问题。具有木桶效应，内存存储上限取决于内存最小的那一个。
>
> 容器全部停止后再重启【从机】无需再去slaveof【主机】，会自动加载哨兵的配置信息，但主节点可能会迁移

```bash
#***************sentinel.conf配置文件需要注意的地方*************
#核心配置
sentinel monitor mymaster 192.168.1.12 6379 2
	-mymaster 自定义即可
	-192.168.1.12 6379 监听的【主机】ip和端口
	-2 代表票数超过所有节点的一半（n/2+1）就开始投票
#这个是超时的时间（单位为毫秒）。打个比方，当你去 ping 一个机器的时候，多长时间后仍ping 不通，那么就认为它是有问题 
sentinel down-after-millseconds mymaster 30000

#当Sentinel节点集合对主节点故障判定达成一致时，Sentinel领导者节点会做故障转移操作，选出新的主节点，原来的从节点会向新的主节点发起复制操作。parallel-syncs就是用来限制在一次故障转移之后，每次向新的主节点发起复制操作的从节点个数，指出Sentinel属于并发还是串行。1代表每次只能复制一个，可以减轻master压力
sentinel parallel-syncs mymaster 1

#如果 Sentinel 监控的主节点配置了密码，sentinel auth-pass 配置通过添加主节点的密码，防止 Sentinel 节点对主节点无法监控。
sentinel auth-pass mymaster 123456
#表示故障转移的时间
sentinel failover-timeout mymaster 180000
#用于容器中设置公网ip和port（因为在容器中通过php获取的端口全部是6379，外网是无法使用的，所有在哨兵启动之前要设置一下，如果不需要外网获取则不需要设置）
sentinel announce-ip <ip>  #公网宿主机ip
sentinel announce-port <port> #公网宿主机端口（注意是宿主机映射的哨兵端口）例：26386
#redis也有这样的配置
slave-announce-ip <ip>  #公网宿主机ip
slave-announce-port <port> #公网宿主机端口 例：6388

#手动配置外网ip太麻烦也可以通过shell脚本自动设置，参考09的sh文件，注意docker-compose.yaml中的environment和entrypoint两项，一个是设置环境变量，.sh文件内需要使用，另一个是执行.sh的命令，还有redis的Docker镜像构建文件也修改了


#哨兵日志
cat /usr/src/redis/sentinel.log

#************************命令方式******************

#连接哨兵
docker exec -it sentinel-1 sh
redis-cli -a 123456 -p 26379
#查看
info

#添加新的监听
SENTINEL monitor mymaster 127.0.0.1 6379 2  
#放弃对某个master监听
SENTINEL REMOVE test    
#设置配置选项
SENTINEL set failover-timeout mymaster 180000 

#显示被监控的所有master以及它们的状态.
SENTINEL masters 
#显示指定master的信息和状态；
SENTINEL master <master name>
#显示指定master的所有slave以及它们的状态；
SENTINEL slaves <master name> 
#返回指定master的ip和端口，
SENTINEL get-master-addr-by-name <master name> 
#强制sentinel执行failover，并且不需要得到其他sentinel的同意。如果正在进行failover或者failover已经完成，将会显示被提升为master的slave的ip和端口。但是failover后会将最新的配置发送给其他sentinel。
SENTINEL failover <master name>  
```

#### 遇到的问题：

**1.启动哨兵的时候出现以下错误：**
**Reading the configuration file, at line 208 **
**sentinel known-replica mymaster 192.168.1.12 6379'**

> 解决：这是哨兵自己在配置文件的最后生成的内容，如果报错了删除即可

**2.**把master停止掉并重启以后，虽然成为了从节点，但master_link_status=down，新的主节点上面也没找到这个从节点信息。

> 解决：master节点也要设置masterauth，避免当master重启后无法变成新master节点的从节点
>
> 主从无法切换，可能会有以下几种情况：
> 1-redis保护模式开启了，即：  protected-mode yes
> 2-端口没有放开；即：ping不通
> 3-master密码和从密码不一致。
> 4-master节点的redis.conf没有添加masterauth



#### 9.集群方案（至少6个，3主3从）

##### 虚拟槽分区

```bash
#redis.conf配置文件需要注意的
#开启集群
cluster-enabled yes
cluster-config-file ... #自动生成的配置文件，默认即可

cluster-announce-ip <ip>  #公网宿主机ip （配合.sh文件让外网访问）
cluster-announce-port <port> #公网宿主机端口 例：6388

#*****************命令***************
CLUSTER info                    #打印集群的信息。
CLUSTER addslots <slot> [slot ...]： #将一个或多个槽（slot）指派（assign）给当前节点。
#移除一个或多个槽对当前节点的指派。
CLUSTER delslots <slot> [slot ...]： 
#示例：
CLUSTER delslots 10935

CLUSTER slots  #列出槽位、节点信息。
CLUSTER slaves <node_id> #列出指定节点下面的从节点信息。
CLUSTER saveconfig 				#手动执行命令保存保存集群的配置文件，集群默认在配置修改的时候会自动保存配置文件。
CLUSTER keyslot <key>     #列出key被放置在哪个槽上。
CLUSTER flushslots 				#移除指派给当前节点的所有槽，让当前节点变成一个没有指派任何槽的节点。

#返回槽目前包含的键值对数量。
CLUSTER countkeysinslot <slot> 
#示例：
CLUSTER countkeysinslot 5687

CLUSTER getkeysinslot <slot> <count> #返回count个槽中的键。
CLUSTER setslot <slot> node <node_id>     #将槽指派给指定的节点，如果槽已经指派给另一个节点，那么先让另一个节点删除该槽，然后再进行指派。
CLUSTER setslot <slot> migrating <node_id> #将本节点的槽迁移到指定的节点中。
CLUSTER setslot <slot> importing <node_id> #从node_id 指定的节点中导入槽 slot 到本节点。
CLUSTER setslot <slot> stable 	#取消对槽    slot 的导入（import）或者迁移（migrate）。 
CLUSTER failover		#手动进行故障转移。
CLUSTER forget <node_id>    #从集群中移除指定的节点，这样就无法完成握手，过期时为60s，60s后两节点又会继续完成握手。
CLUSTER reset [HARD|SOFT]  #重置集群信息，soft是清空其他节点的信息，但不修改自己的id，hard还会修改自己的id，不传该参数则使用soft方式。
CLUSTER count-failure-reports <node_id>  #列出某个节点的故障报告的长度。
CLUSTER SET-CONFIG-EPOCH 		#设置节点epoch，只有在节点加入集群前才能设置。
```



#### 10.构建集群（使用10的文件）

```bash
配置流程：
# 1.节点握手(和每个节点握手一次即可)
cluster meet 192.168.1.17 6399
# 1.1 查看集群当前已知的所有节点（node）的相关信息。
CLUSTER nodes

# 2.指定主从关系
#格式
CLUSTER replicate <node_id> #将当前节点设置为某个节点的从节点。
# 2.1 设置主从（把【118.24.109.254：6395】设置为b2081d4b1730d19的从机，后面的id通过CLUSTER nodes查看），这里使用的是公网ip和端口
redis-cli -a sixstar -h 118.24.109.254  -p 6395  cluster replicate c848bcafde36c74e1e8c43cf5b2081d4b1730d19
# 2.2查看配置的情况
redis-cli -a sixstar -h 118.24.109.254  -p 6395 CLUSTER nodes

# 3.分配虚拟槽（三个主节点的分配）
redis-cli -h 118.24.109.254  -p 6390 -a sixstar cluster addslots {0..5461}
redis-cli -h 118.24.109.254 -p 6391  -a sixstar cluster addslots {5462..10922}
redis-cli -h 118.24.109.254 -p 6392  -a sixstar cluster addslots {10923..16383}
# 3.1查看分配的情况
redis-cli -a sixstar -h 118.24.109.254  -p 6395 CLUSTER nodes
#或者列出槽位、节点信息。
CLUSTER slots

# 4.设置缓存的时候就不能直接设置了，需要从集群模式状态下设置：
redis-cli -a sixstar -c
set test 1111
```

#### 借助脚本构建集群（参考文档，注意别用记事本打开 sh/cluster.sh 这个文件，会有察觉不出来的错误）

> 切记关闭虚拟机的防护墙
>
> systemctl status firewalld.service
>
> systemctl stop firewalld.service  #临时关闭
>
> systemctl disable firewalld.service  #永久关闭

> 这个脚本工具是封装用来迁移槽节点的工具，简化了通过命令行的方式，查看帮助直接./redis-trib.rb即可
>
> 运行命令之前把除了配置文件的其他文件都删除，因为可能存在分配的槽节点信息
>
> 注意：如果sh/cluster.sh中的ip端口没有写入配置文件，请新建cluster.sh手动写一份，然后根据日志处理

```bash
# 1.docker-compose up -d
# 2.进入某个容器中并进入/usr/src/sh/执行redis-trib.rb（使用之前需要安装Ruby依赖环境，相关扩展在dockerfile文件中已经写了指令）
cd /usr/src/sh/
# 3.执行(192.168.1.154为虚拟机的地址，即容器的宿主机，切记关闭虚拟机的防护墙，不然连接不上)
ruby redis-trib.rb create --replicas 1  192.168.233.11:6391 192.168.233.11:6392 192.168.233.11:6393 192.168.233.11:6394 192.168.233.11:6395  192.168.233.11:6396 192.168.233.11:6397  192.168.233.11:6398
#解释：
	-1 代表每个主节点只有1个从节点，会自动分配，前三个为主节点，后三个为从节点

#删除节点（需要先迁移槽）
ruby redis-trib.rb del-node 118.24.109.254:6390  cd7a5fac0dd8a0d90565342506914b5ad2bd5818

#添加新节点扩容，第一个ip是要加入的新节点，第二个ip是集群中已存在的节点
ruby redis-trib.rb add-node 192.168.233.11:6399 192.168.233.11:6391

#槽节点迁移（扩容之后）redis-trib 提供了槽重分片功能，命令如下：
ruby redis-trib.rb reshard host:port --from <arg> --to <arg> --slots <arg> --yes --timeout <arg> --pipeline <arg>

#参数说明： 
·--host： port：#必传参数，集群内任意节点地址，用来获取整个集群信息。
·--from： #制定源节点的id，如果有多个源节点，使用逗号分隔，如果是all源节点变为集群内所有主节点，在迁移过程中提示用户输入。 
·--to： #需要迁移的目标节点的id，目标节点只能填写一个，在迁移过程中提示用户输入。
·--slots：  #需要迁移槽的总数量，在迁移过程中提示用户输入。
·--yes：    #当打印出reshard 执行计划时，是否需要用户输入yes确认后再执行 reshard。 
·--timeout： #控制每次 migrate 操作的超时时间，默认为60000毫秒。
·--pipeline： #控制每次批量迁移键的数量，默认为10。


#******************扩容迁移槽分区示例*****************
ruby redis-trib.rb reshard 192.168.233.11:6391
# 交互窗口1：要迁移的节点数
4096
# 交互窗口2：目标节点ID
ef7a786ef6436728db3462da7fe142532e552e33
# 交互窗口3：源节点ID，可以输入all(会平均从每个节点中取一部分)或者输入多个源节点ID，每输入一个就enter换行，最后一行done结束
>:ef7a786ef6436728db3462da7fe142532e552e1b
>:done
# 交互窗口4：确认之后yes
yes

#查看最新迁移的节点信息
cluster nodes
#平衡集群节点slot数量，后面的ip为随意一个节点
ruby redis-trib.rb rebalance ip:port

#查看集群状态
ruby redis-trib.rb check 192.168.233.11:6391

#查看单节点信息
ruby redis-trib.rb info 192.168.233.11:6391

#修复单节点
ruby redis-trib.rb fix 故障节点h:p
```



#### 排错

 **1.删除了集群的容器以后再次构建会不成功，删除掉配置文件，重启docker即可**

```bash
systemctl restart docker
```

**2.构建php的时候如果报错docker build make: /bin/sh: Operation not permitted，需要指定alpine版本**,

```bash
FROM php:7.3-fpm-alpine3.13
```

**3.涉及权限的大部分都是alpine版本问题**

```bash
#所以要么指明alpine在4版本以下
FROM alpine:3.13

#要么升级docker到20
```

**4.masterauth是集群间的节点相互访问时候用到的密码，而requirepass是单独请求连接时候用到的密码。**

**5.电脑无法访问虚拟机中docker的nginx容器，重启docker即可**

```bash
systemctl restart docker
```

**6.nginx容器中关于php的配置**

```bash
location ~ \.php$ {
    #php-fpm容器当中的路径，不是nginx容器路径
    root           /var/www/html;
    #php-fpm容器的ip和端口
    fastcgi_pass   192.168.1.31:9000;
    #$DOCUMENT_ROOT是上面的root
    fastcgi_param  SCRIPT_FILENAME  $DOCUMENT_ROOT$fastcgi_script_name;
}
```

>fastcgi_param  SCRIPT_FILENAME    $document_root$fastcgi_script_name;#脚本文件请求的路径  
>fastcgi_param  QUERY_STRING       $query_string; #请求的参数;如?app=123  
>fastcgi_param  REQUEST_METHOD     $request_method; #请求的动作(GET,POST)  
>fastcgi_param  CONTENT_TYPE       $content_type; #请求头中的Content-Type字段  
>fastcgi_param  CONTENT_LENGTH     $content_length; #请求头中的Content-length字段。  
>fastcgi_param  SCRIPT_NAME        $fastcgi_script_name; #脚本名称   
>fastcgi_param  REQUEST_URI        $request_uri; #请求的地址不带参数  
>fastcgi_param  DOCUMENT_URI       $document_uri; #与$uri相同。   
>fastcgi_param  DOCUMENT_ROOT      $document_root; #网站的根目录。在server配置中root指令中指定的值  fastcgi_param  SERVER_PROTOCOL    $server_protocol; #请求使用的协议，通常是HTTP/1.0或HTTP/1.1。    
>
>fastcgi_param  GATEWAY_INTERFACE  CGI/1.1;#cgi 版本  
>fastcgi_param  SERVER_SOFTWARE    nginx/$nginx_version;#nginx 版本号，可修改、隐藏  
>
>fastcgi_param  REMOTE_ADDR        $remote_addr; #客户端IP  
>fastcgi_param  REMOTE_PORT        $remote_port; #客户端端口  
>fastcgi_param  SERVER_ADDR        $server_addr; #服务器IP地址  
>fastcgi_param  SERVER_PORT        $server_port; #服务器端口  
>fastcgi_param  SERVER_NAME        $server_name; #服务器名，域名在server配置中指定的server_name  
>
>#fastcgi_param  PATH_INFO           $path_info;#可自定义变量  



扩展参考
[官方镜像站](https://hub.docker.com/search?q=mysql)
[阿里云镜像站](https://developer.aliyun.com/mirror/) 
[网易云镜像](https://c.163yun.com/hub)