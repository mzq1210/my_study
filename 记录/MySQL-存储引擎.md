# MySQL-存储引擎

[TOC]

# 1. 存储引擎种类

## 1.1 介绍(Oracle MySQL)

> MySQL 5.5 之前，使用MyISAM引擎作为模式引擎。用户数据、系统表数据都是在MyISAM。
> MySQL 5.5 版本，将InnoDB引擎作为默认的存储引擎。存储用户表数据，系统相关表有部分是MyISAM。
>
> 其他种类： 
> Tokudb引擎： Percona  MairaDB 默认支持的。
> insert性能高、压缩比都比较高
```sh
InnoDB  #5.5以后默认的存储引擎
MyISAM  #5.1以前默认存储引擎
MEMORY  #只在内存里生成，不会存储到磁盘上
ARCHIVE
FEDERATED
EXAMPLE
BLACKHOLE #黑洞，不存储内存，也不在磁盘上存储。但是会生成日志
MERGE
NDBCLUSTER
CSV
```

## 1.2 引擎种类查看

```sh
#查看所有的存储引擎类型
show engines;
存储引擎是作用在表上的，也就意味着，不同的表可以有不同的存储引擎类型。
PerconaDB:默认是XtraDB
MariaDB:默认是InnoDB
其他的存储引擎支持:
TokuDB    
RocksDB
MyRocks
以上三种存储引擎的共同点:压缩比较高,数据插入性能极高
现在很多的NewSQL,使用比较多的功能特性.
```

#### 简历案例---zabbix监控系统架构整改

```sh
环境: zabbix 3.2    mariaDB 5.5  centos 7.3
现象 : zabbix卡的要死 ,  每隔3-4个月,都要重新搭建一遍zabbix,存储空间经常爆满.
问题 :
1. zabbix 版本 
2. 数据库版本
3. zabbix数据库500G,存在一个文件里
优化建议:
1.数据库版本升级到5.7版本,zabbix升级更高版本
2.存储引擎改为tokudb
3.监控数据按月份进行切割(二次开发:zabbix 数据保留机制功能重写,数据库分表)
4.关闭binlog和双1
5.参数调整....
优化结果:
监控状态良好

为什么?
1. 原生态支持TokuDB,另外经过测试环境,5.7要比5.5 版本性能 高  2-3倍
2. TokuDB:insert数据比Innodb快的多，数据压缩比要Innodb高
3.监控数据按月份进行切割,为了能够truncate每个分区表,立即释放空间
4.关闭binlog ----->减少无关日志的记录.
5.参数调整...----->安全性参数关闭,提高性能.

```

## 1.3 存储引擎查看简单修改

```sql
show engines; 
show table status;
show table status like 'city\G';
select @@default_storage_engine;
selectr table_name,engine from information_schema.tables where  
table_schema='world';
+-----------------+--------+
| table_name      | engine |
+-----------------+--------+
| city            | InnoDB |
| country         | InnoDB |
| countrylanguage | InnoDB |
+-----------------+--------+
```

#### 1.3.1 创建一个引擎为Myisam表然后修改引擎为innodb

```mysql
create table t (id int) engine=myisam;

mysql> show create table t;
+-------+------------------------------------------+
| Table | Create Table      |
+-------+------------------------------------------+
| t     | CREATE TABLE `t` (
  `id` int(11) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 |
+-------+------------------------------------------+
1 row in set (0.01 sec)

mysql> alter table t engine=innodb;
Query OK, 0 rows affected (0.07 sec)
Records: 0  Duplicates: 0  Warnings: 0

```

#### 1.3.2 单独修改某一张表的存储引擎

```mysql
alter table world.t1 engine=innodb;
注意：此命令我们经常使用他，进行innodb表的碎片整理
```

#### 1.3.3 替换world库下所有表的引擎为innodb

```csharp
mysql> select concat("alter table" ,table_name,"engine=innodb") from information_schema.tables where table_schema='world';
+---------------------------------------------------+
| concat("alter table" ,table_name,"engine=innodb") |
+---------------------------------------------------+
| alter tablecityengine=innodb                      |
| alter tablecountryengine=innodb                   |
| alter tablecountrylanguageengine=innodb           |
| alter tabletengine=innodb                         |
+---------------------------------------------------+
```

#### 1.3.4 替换存储引擎

```sql
扩展： 将多张非InnoDB的业务表，替换成InnoDB 
mysql> select concat("alter table ",table_schema,".",table_name," engine=innodb;") 
from information_schema.tables  
where table_schema not in ('mysql','sys','information_schema','performance_schema')  and engine!='innodb' 
into outfile '/tmp/alter.sql';
```

`alter table t engine=innodb;`可以整理碎片

> ### 在线修改存储引擎 重启后都会失效

- 会话级别

```sql
set default_storage_engine=myisam；
```

- 全局级别（影响新会话）

```sql
set global default_storage_engine=myisam；
```

> 如果想永久生效就写入配置文件中
>
> ##### innodb frm ibd .frm 用于存储表的定义

## 1.4 InnoDB存储引擎介绍

![1589267157](MySQL-存储引擎.assets/1589267157.jpg)

# 2. 表空间(Tablespace)

## 2.1 共享表空间

```sh
需要将所有数据存储到同一个表空间中 ，管理比较混乱
5.5版本出现的管理模式，也是默认的管理模式。
5.6版本以，共享表空间保留，只用来存储:数据字典信息,undo,临时表。
5.7 版本,临时表被独立出来了
8.0版本,undo也被独立出去了
```

## 2.2 共享表空间设置

```sh
共享表空间设置(在搭建MySQL时，初始化数据之前设置到参数文件中)
mysql> select @@innodb_data_file_path;
+---------------------------------------------------+
| @@innodb_data_file_path                           |
+---------------------------------------------------+
| ibdata1:100M;ibdata2:100M;ibdata3:100M:autoextend |
+---------------------------------------------------+
mysql>show variables like '%extend%';
innodb_data_file_path=ibdata1:512M:ibdata2:512M:autoextend
innodb_autoextend_increment=64
#vim /etc/my.cnf 
innodb_data_file_path=ibdata1:50M;ibdata2:50M;ibdata3:50M:autoextend
生产建议： 512M-1G  2-3个
重新初始化数据，生效。
```

## 2.3 独立表空间

```sh
从5.6，默认表空间不再使用共享表空间，替换为独立表空间。
主要存储的是用户数据
存储特点为：一个表一个ibd文件，存储数据行和索引信息
基本表结构元数据存储：
xxx.frm
最终结论：
      元数据            数据行+索引
mysql表数据    =（ibdataX+frm）+ibd(段、区、页)
        DDL             DML+DQL

MySQL的存储引擎日志：
Redo Log: ib_logfile0  ib_logfile1，重做日志
Undo Log: ibdata1 ibdata2(存储在共享表空间中)，回滚日志
临时表:ibtmp1，在做join union操作产生临时数据，用完就自动
```

## 2.4 独立表空间设置问题

```sh
db01 [(none)]>select @@innodb_file_per_table;
+-------------------------+
| @@innodb_file_per_table |
+-------------------------+
|                      1 |
+-------------------------+
alter table city dicard tablespace;
alter table city import tablespace;
```

## 2.5表空间迁移 
```sh
模拟：

## 3306 
mysql> create table t1(id int)charset utf8mb4;
mysql> insert into t1 values(1),(2),(3);
mysql> commit;
mysql> lock table t1 read;

## 3307 
mysql> use test;
mysql>  create table t1(id int)charset utf8mb4;
mysql> alter table t1 discard tablespace;
[root@db01 test]# cp -a /data/3306/data/world/t1.ibd  /data/3307/data/test/
mysql> alter table t1 import tablespace;
```

# 3. 事务的ACID特性

**Atomic（原子性）**

```undefined
所有语句作为一个单元全部成功执行或全部取消。不能出现中间状态。
```

**Consistent（一致性）**

```undefined
如果数据库在事务开始时处于一致状态，则在执行该事务期间将保留一致状态。
```

**Isolated（隔离性）**

```undefined
事务之间不相互影响。
```

**Durable（持久性）**

```undefined
事务成功完成后，所做的所有更改都会准确地记录在数据库中。所做的更改不会丢失。
```

# 4. 事务的生命周期（事务控制语句）

## 4.1 事务的开始

```ruby
begin
说明:在5.5 以上的版本，不需要手工begin，只要你执行的是一个DML，会自动在前面加一个begin命令。
```

## 4.2 事务的结束

```undefined
commit：提交事务
完成一个事务，一旦事务提交成功 ，就说明具备ACID特性了。
rollback ：回滚事务
将内存中，已执行过的操作，回滚回去
```

## 4.3 自动提交策略（autocommit）

```csharp
db01 [(none)]>select @@autocommit;
db01 [(none)]>set autocommit=0;
db01 [(none)]>set global autocommit=0;
注：
自动提交是否打开，一般在有事务需求的MySQL中，将其关闭
不管有没有事务需求，我们一般也都建议设置为0，可以很大程度上提高数据库性能
(1)
set autocommit=0;   
set global autocommit=0;
(2)
vim /etc/my.cnf
autocommit=0     
```

## 4.4  隐式提交语句

```ruby
用于隐式提交的 SQL 语句：
begin 
a
b
begin

SET AUTOCOMMIT = 1

导致提交的非事务语句：
DDL语句： （ALTER、CREATE 和 DROP）
DCL语句： （GRANT、REVOKE 和 SET PASSWORD）
锁定语句：（LOCK TABLES 和 UNLOCK TABLES）
导致隐式提交的语句示例：
TRUNCATE TABLE
LOAD DATA INFILE
SELECT FOR UPDATE
```

## 4.5 开始事务流程：

```csharp
1、检查autocommit是否为关闭状态
select @@autocommit;
或者：
show variables like 'autocommit';
2、开启事务,并结束事务
begin
delete from student where name='alexsb';
update student set name='alexsb' where name='alex';
rollback;

begin
delete from student where name='alexsb';
update student set name='alexsb' where name='alex';
commit;
```

# 5. InnoDB 事务的ACID如何保证?

## 5.0 一些概念

```ruby
redo log ---> 重做日志 ib_logfile0~1   50M   , 轮询使用
redo log buffer ---> redo内存区域
ibd     ----> 存储 数据行和索引 
buffer pool --->缓冲区池,数据和索引的缓冲
LSN : 日志序列号 
磁盘数据页,redo文件,buffer pool,redo buffer
MySQL 每次数据库启动,都会比较磁盘数据页和redolog的LSN,必须要求两者LSN一致数据库才能正常启动
WAL : write ahead log 日志优先写的方式实现持久化
脏页: 内存脏页,内存中发生了修改,没写入到磁盘之前,我们把内存页称之为脏页.
CKPT:Checkpoint,检查点,就是将脏页刷写到磁盘的动作
TXID: 事务号,InnoDB会为每一个事务生成一个事务号,伴随着整个事务.
```

## 5.1 redo log

### 5.1.1 Redo是什么？

```ruby
redo,顾名思义“重做日志”，是事务日志的一种。
作用： 记录内存数据页的变化。提供“前进”功能。
存储方式： 
ib_logfileN ,轮序使用方式。
```

### 5.1.2 作用是什么？

```undefined
在事务ACID过程中，实现的是“D”持久化的作用。对于AC也有相应的作用
```

### 5.1.3 redo日志位置

```ruby
redo的日志文件：iblogfile0 iblogfile1
```

### 5.1.4 redo buffer

```ruby
redo的buffer:数据页的变化信息+数据页当时的LSN号
LSN：日志序列号  磁盘数据页、内存数据页、redo buffer、redolog
```

### 5.1.5 redo的刷新策略

```ruby
commit;
刷新当前事务的redo buffer到磁盘
还会顺便将一部分redo buffer中没有提交的事务日志也刷新到磁盘
```

### 5.1.6 MySQL CSR——前滚

```ruby
MySQL : 在启动时,必须保证redo日志文件和数据文件LSN必须一致, 如果不一致就会触发CSR,最终保证一致
情况一:
我们做了一个事务,begin;update;commit.
1.在begin ,会立即分配一个TXID=tx_01.
2.update时,会将需要修改的数据页(dp_01,LSN=101),加载到data buffer中
3.DBWR线程,会进行dp_01数据页修改更新,并更新LSN=102
4.LOGBWR日志写线程,会将dp_01数据页的变化+LSN+TXID存储到redobuffer
5. 执行commit时,LGWR日志写线程会将redobuffer信息写入redolog日志文件中,基于WAL原则,
在日志完全写入磁盘后,commit命令才执行成功,(会将此日志打上commit标记)
6.假如此时宕机,内存脏页没有来得及写入磁盘,内存数据全部丢失
7.MySQL再次重启时,必须要redolog和磁盘数据页的LSN是一致的.但是,此时dp_01,TXID=tx_01磁盘是LSN=101,dp_01,TXID=tx_01,redolog中LSN=102
MySQL此时无法正常启动,MySQL触发CSR.在内存追平LSN号,触发ckpt,将内存数据页更新到磁盘,从而保证磁盘数据页和redolog LSN一值.这时MySQL正长启动
以上的工作过程,我们把它称之为基于REDO的"前滚操作"
```

## 5.2 undo 回滚日志

### 5.2.1 undo是什么？

```undefined
undo,顾名思义“回滚日志”
结构： 
128 回滚段   ,  96个 undo表空间中，32个在ibtmpN中。
每个段1024 slot槽位。
查看： 
SELECT @@innodb_undo_tablespaces;  ---->3-5个    #打开独立undo模式，并设置undo的个数。
SELECT @@innodb_max_undo_log_size;               #undo日志的大小，默认1G。
SELECT @@innodb_undo_log_truncate;               #开启undo自动回收的机制（undo_purge线程）。
SELECT @@innodb_purge_rseg_truncate_frequency;   #触发自动回收的条件，单位是检测次数。

设定方法： 
innodb_undo_tablespaces=3
innodb_max_undo_log_size=128M
innodb_undo_log_truncate=ON
innodb_purge_rseg_truncate_frequency=32

```

### 5.2.2 作用是什么？

```ruby
在事务ACID过程中，实现的是“A” 原子性的作用
另外CI也依赖于Undo
在rolback时,将数据恢复到修改之前的状态
在CSR实现的是,将redo当中记录的未提交的时候进行回滚.
undo提供快照技术,保存事务修改之前的数据状态.保证了MVCC,隔离性,mysqldump的热备
```

## 5.3 概念性的东西:

```ruby
redo怎么应用的
undo怎么应用的
CSR(自动故障恢复)过程
LSN :日志序列号
TXID:事务ID
CKPT(Checkpoint)
```

## 5.4 锁

```ruby
“锁”顾名思义就是锁定的意思。
“锁”的作用是什么？
在事务ACID过程中，“锁”和“隔离级别”一起来实现“I”隔离性和"C" 一致性 (redo也有参与).
悲观锁:行级锁定(行锁)
谁先操作某个数据行,就会持有<这行>的(X)锁.
乐观锁: 没有锁
```

### 5.4.1共享和排他锁

```sql
共享：我可以读 写 加锁 , 别人可以 读 加锁。
排他：只有我 才 可以 读 写 加锁 , 也就是说，必须要等我提交事务，其他的才可以操作。
行锁：锁只对一行生效，比如 id=1 的那行
表锁：锁对整个表都生效
加排它锁:select ...for update语句(使用排他锁解决超卖)
加共享锁可以使用select ... lock in share mode语句
-- ※ ※ ※ ※ ※
-- 使用InnoDB引擎，如果筛选条件里面没有索引字段，就会锁住整张表，否则的话，锁住相应的行。

-- sharedLock 与 lockForUpdate 相同的地方是，都能避免同一行数据被其他 transaction 进行 update
-- 不同的地方是：sharedLock 不会阻止其他 transaction 读取同一行
-- lockForUpdate 会阻止其他 transaction 读取同一行 （需要特别注意的是，普通的非锁定读取读取依然可以读取到该行，只有 sharedLock 和 lockForUpdate 的读取会被阻止。）
```

## 5.5 隔离级别

```csharp
影响到数据的读取,默认的级别是 RR模式.
transaction_isolation   隔离级别(参数)
负责的是,MVCC,读一致性问题
RU  : 读未提交,隔离性差，会出现的问题： 脏读、不可重复读、幻读。
RC  : 读已提交,可能出现幻读,不可重复读,可以防止脏读.
RR  : 可重复读,功能是防止"幻读"现象 ,利用的是undo的快照技术+GAP(间隙锁)+NextLock(下键锁)
SR   : 可串行化,可以防止死锁,但是并发事务性能较差
补充: 在RC级别下,可以减轻GAP+NextLock锁的问题,但是会出现幻读现象,一般在为了读一致性会在正常select后添加for update语句.但是,请记住执行完一定要commit 否则容易出现所等待比较严重.
例如:
[world]>select * from city where id=999 for update;
[world]>commit;
```

## 5.6 架构改造项目

```css
项目背景:
2台  IBM X3650   32G  ,原来主从关系,2年多没有主从了,"小问题"不断(锁,宕机后的安全)
MySQL 5.1.77   默认存储引擎 MyISAM  
数据量: 60G左右 ,每周全备,没有开二进制日志
架构方案:
    1. 升级数据库版本到5.7.20 
    2. 更新所有业务表的存储引擎为InnoDB
    3. 重新设计备份策略为热备份,每天全备,并备份日志
    4. 重新构建主从
结果:
    1.性能
    2.安全方面
    3.快速故障处理
```

# 6 InnoDB存储引擎核心特性-参数补充

## 6.1 存储引擎相关

### 6.1.1 查看

```sql
show engines;
show variables like 'default_storage_engine';
select @@default_storage_engine;
-- 查询存储引擎为MyISAM的表
select table_catalog
      ,table_schema
      ,table_name
      ,engine
from information_schema.tables
where engine='MyISAM';
```

### 6.1.2 如何指定和修改存储引擎

```undefined
(1) 通过参数设置默认引擎
(2) 建表的时候进行设置
(3) alter table t1 engine=innodb;
```

## 6.2. 表空间

### 6.2.1 共享表空间

```undefined
innodb_data_file_path
一般是在初始化数据之前就设置好
例子:
innodb_data_file_path=ibdata1:512M:ibdata2:512M:autoextend
```

### 6.2.2 独立表空间

```dart
show variables like 'innodb_file_per_table';
```

## 6.3. 缓冲区池

### 6.3.1 查询

```css
select @@innodb_buffer_pool_size;
show engine innodb status\G
innodb_buffer_pool_size 
一般建议最多是物理内存的 75-80%
```

## 6.4. innodb_flush_log_at_trx_commit  (双一标准之一)

### 6.4.1 作用

```bash
主要控制了innodb将log buffer中的数据写入日志文件并flush磁盘的时间点，取值分别为0、1、2三个。
```

### 6.4.2 查询

```css
select @@innodb_flush_log_at_trx_commit;
```

### 6.4.3 参数说明:

```bash
1，每次事务的提交都会引起日志文件写入、flush磁盘的操作，确保了事务的ACID；flush  到操作系统的文件系统缓存  fsync到物理磁盘.
0，表示当事务提交时，不做日志写入操作，而是每秒钟将log buffer中的数据写入文件系统缓存并且秒fsync磁盘一次；
2，每次事务提交引起写入文件系统缓存,但每秒钟完成一次fsync磁盘操作。
--------
The default setting of 1 is required for full ACID compliance. Logs are written and flushed to disk at each transaction commit.
With a setting of 0, logs are written and flushed to disk once per second. Transactions for which logs have not been flushed can be lost in a crash.
With a setting of 2, logs are written after each transaction commit and flushed to disk once per second. Transactions for which logs have not been flushed can be lost in a crash.
-------
```

## 6.5. Innodb_flush_method=(O_DIRECT, fdatasync)

### 6.5.1 作用

```bash
控制的是,log buffer 和data buffer,刷写磁盘的时候是否经过文件系统缓存
```

### 6.5.2 查看

```dart
show variables like '%innodb_flush%';
```

### 6.5.3 参数值说明

```undefined
O_DIRECT  :数据缓冲区写磁盘,不走OS buffer
fsync :日志和数据缓冲区写磁盘,都走OS buffer
O_DSYNC  :日志缓冲区写磁盘,不走 OS buffer
```

### 6.5.4 使用建议

```undefined
最高安全模式
innodb_flush_log_at_trx_commit=1
Innodb_flush_method=O_DIRECT
最高性能:
innodb_flush_log_at_trx_commit=0
Innodb_flush_method=fsync
```

## 6.6. redo日志有关的参数

```undefined
innodb_log_buffer_size=16777216
innodb_log_file_size=50331648
innodb_log_files_in_group = 3
```

# 7.扩展(自己扩展，建议是官方文档。)

```csharp
RR模式(对索引进行删除时):
GAP:          间隙锁
next-lock:    下一键锁定

例子:
id（有索引）
1 2 3 4 5 6 
GAP：
在对3这个值做变更时，会产生两种锁，一种是本行的行级锁，另一种会在2和4索引键上进行枷锁
next-lock：
对第六行变更时，一种是本行的行级锁，在索引末尾键进行加锁，6以后的值在这时是不能被插入的。
总之：
GAP、next lock都是为了保证RR模式下，不会出现幻读，降低隔离级别或取消索引，这两种锁都不会产生。
IX IS X S是什么?
```

# 8.其他参数优化细节

## 8.1 Max_connections *****

```sh
8. （1）简介
    Mysql的最大连接数，如果服务器的并发请求量比较大，可以调高这个值，当然这是要建立在机器能够支撑的情况下，因为如果连接数越来越多，mysql会为每个连接提供缓冲区，就会开销的越多的内存，所以需要适当的调整该值，不能随便去提高设值。
   （2）判断依据
   show variables like 'max_connections';
    +-----------------+-------+
    | Variable_name   | Value |
    +-----------------+-------+
    | max_connections | 151   |
    +-----------------+-------+
   show status like 'Max_used_connections';
    +----------------------+-------+
    | Variable_name        | Value |
    +----------------------+-------+
    | Max_used_connections | 101   |
    +----------------------+-------+
   （3）修改方式举例
   vim /etc/my.cnf 
   max_connections=1024

案例1： 
连接数设置不生效的问题，214问题。
/etc/security/limits.conf 

- soft　　nofile　　65536
- hard　　nofile　　65536
```

## 8.2 back_log ***

```sh
（1）简介
    mysql能暂存的连接数量，当主要mysql线程在一个很短时间内得到非常多的连接请求时候它就会起作用，如果mysql的连接数据达到max_connections时候，新来的请求将会被存在堆栈中，等待某一连接释放资源，该推栈的数量及back_log,如果等待连接的数量超过back_log，将不被授予连接资源。
back_log值指出在mysql暂时停止回答新请求之前的短时间内有多少个请求可以被存在推栈中，只有如果期望在一个短时间内有很多连接的时候需要增加它
（2）判断依据
show full processlist
发现大量的待连接进程时，就需要加大back_log或者加大max_connections的值
（3）修改方式举例
vim /etc/my.cnf 
back_log=1024
```

## 8.3 wait_timeout和interactive_timeout *****

```sh
（1）简介
wait_timeout：指的是mysql在关闭一个非交互的连接之前所要等待的秒数
interactive_timeout：指的是mysql在关闭一个交互的连接之前所需要等待的秒数，比如我们在终端上进行mysql管理，使用的即使交互的连接，这时候，如果没有操作的时间超过了interactive_time设置的时间就会自动的断开，默认的是28800，可调优为7200。
wait_timeout:如果设置太小，那么连接关闭的就很快，从而使一些持久的连接不起作用
（2）设置建议
如果设置太大，容易造成连接打开时间过长，在show processlist时候，能看到很多的连接 ，一般希望wait_timeout尽可能低
（3）修改方式举例
wait_timeout=120
interactive_timeout=7200

长连接的应用，为了不去反复的回收和分配资源，降低额外的开销。
一般我们会将wait_timeout设定比较小，interactive_timeout要和应用开发人员沟通长链接的应用是否很多。如果他需要长链接，那么这个值可以不需要调整。
另外还可以使用类外的参数弥补。

案例2：MySQL 连接长时间(7200和1200秒)无法释放
场景： MySQL 5.7  ， DELL730 E5-2650  96G内存  1主2从
Keepalive + LVS + 1主 2从 

处理方法：
ipvsadmin -l -timeout  
Timeout (tcp  tcpfin  udp ):  90 120 300
net.ipv4.tcp_keepalive_time = 60  
```

## 8.4 key_buffer_size *****

```sh
（1）简介
    key_buffer_size指定索引缓冲区的大小，它决定索引处理的速度，尤其是索引读的速度
    《1》此参数与myisam表的索引有关
    《2》临时表的创建有关（多表链接、子查询中、union）
     在有以上查询语句出现的时候，需要创建临时表，用完之后会被丢弃
     临时表有两种创建方式：
                        内存中------->key_buffer_size
                        磁盘上------->ibdata1(5.6)
                                      ibtmp1 (5.7）
                              
注：key_buffer_size只对myisam表起作用，即使不使用myisam表，但是内部的临时磁盘表是myisam表，也要使用该值。
可以使用检查状态值created_tmp_disk_tables得知：

mysql> show status like "created_tmp%";
+-------------------------+-------+
| Variable_name           | Value |
+-------------------------+-------+
| Created_tmp_disk_tables | 0     |
| Created_tmp_files       | 6     |
| Created_tmp_tables      | 1     |
+-------------------------+-------+
3 rows in set (0.00 sec)

mysql> 
通常地，我们习惯以 
Created_tmp_tables/(Created_tmp_disk_tables + Created_tmp_tables) 
Created_tmp_disk_tables/(Created_tmp_disk_tables + Created_tmp_tables) 

或者已各自的一个时段内的差额计算，来判断基于内存的临时表利用率。所以，我们会比较关注 Created_tmp_disk_tables 是否过多，从而认定当前服务器运行状况的优劣。
Created_tmp_disk_tables/(Created_tmp_disk_tables + Created_tmp_tables) 
控制在5%-10%以内

看以下例子：
在调用mysqldump备份数据时，大概执行步骤如下：
180322 17:39:33       7 Connect     root@localhost on
7 Query       /*!40100 SET @@SQL_MODE='' */
7 Init DB     guo
7 Query       SHOW TABLES LIKE 'guo'
7 Query       LOCK TABLES `guo` READ /*!32311 LOCAL */
7 Query       SET OPTION SQL_QUOTE_SHOW_CREATE=1
7 Query       show create table `guo`
7 Query       show fields from `guo`
7 Query       show table status like 'guo'
7 Query       SELECT /*!40001 SQL_NO_CACHE */ * FROM `guo`
7 Query       UNLOCK TABLES
7 Quit

其中，有一步是：show fields from `guo`。从slow query记录的执行计划中，可以知道它也产生了 Tmp_table_on_disk。
所以说，以上公式并不能真正反映到mysql里临时表的利用率，有些情况下产生的 Tmp_table_on_disk 我们完全不用担心，因此没必要过分关注 Created_tmp_disk_tables，但如果它的值大的离谱的话，那就好好查一下，你的服务器到底都在执行什么查询了。 

（3）配置方法
key_buffer_size=64M
```

## 8.5 query_cache_size ***

```sh
（1）简介：
    查询缓存简称QC，主要缓存SQL语句hash值+执行结果。
10条语句，经常做查询。

案例3 ： 开QC ，导致性能降低。 QPS ，TPS降低。
没开起的时候。QPS 2000 TPS 500 
开了之后直接降低到 800，200 

为什么呢？
分区表。Query Cache 不支持。
select * from city where id=10
```

## 8.6 sort_buffer_size ***

```sh
（1）简介：
    每个需要进行排序的线程分配该大小的一个缓冲区。增加这值加速
ORDER BY 
GROUP BY
distinct
union 
（2）配置依据
Sort_Buffer_Size并不是越大越好，由于是connection级的参数，过大的设置+高并发可能会耗尽系统内存资源。
列如：500个连接将会消耗500*sort_buffer_size（2M）=1G内存

（3）配置方法
 修改/etc/my.cnf文件，在[mysqld]下面添加如下：
sort_buffer_size=1M

建议： 尽量排序能够使用索引更好。
```

## 8.7 max_allowed_packet *****

```sh
（1）简介：
mysql根据配置文件会限制，server接受的数据包大小。
（2）配置依据：
有时候大的插入和更新会受max_allowed_packet参数限制，导致写入或者更新失败，更大值是1GB，必须设置1024的倍数
（3）配置方法：
max_allowed_packet=32M

案例： mysqldump备份报错，超出数据包大小。
mysqldump --max_allowed_packet=64M 
```

## 8.8 join_buffer_size ***

```sh
select a.name,b.name from a join b on a.id=b.id where xxxx

用于表间关联缓存的大小，和sort_buffer_size一样，该参数对应的分配内存也是每个连接独享。
尽量在SQL与方面进行优化，效果较为明显。
优化的方法：在on条件列加索引，至少应当是有MUL索引

建议： 尽量能够使用索引优化更好。
```

## 8.9 thread_cache_size = 16 *****

```sh
(1)简介
服务器线程缓存，这个值表示可以重新利用保存在缓存中线程的数量,当断开连接时,那么客户端的线程将被放到缓存中以响应下一个客户而不是销毁(前提是缓存数未达上限),如果线程重新被请求，那么请求将从缓存中读取,如果缓存中是空的或者是新的请求，那么这个线程将被重新创建,如果有很多新的线程，增加这个值可以改善系统性能.

（2）配置依据
通过比较 Connections 和 Threads_created 状态的变量，可以看到这个变量的作用。
设置规则如下：1GB 内存配置为8，2GB配置为16，3GB配置为32，4GB或更高内存，可配置更大。
服务器处理此客户的线程将会缓存起来以响应下一个客户而不是销毁(前提是缓存数未达上限)

试图连接到MySQL(不管是否连接成功)的连接数
mysql>  show status like 'threads_%';
+-------------------+-------+
| Variable_name     | Value |
+-------------------+-------+
| Threads_cached    | 8     |
| Threads_connected | 2     |
| Threads_created   | 4783  |
| Threads_running   | 1     |
+-------------------+-------+
4 rows in set (0.00 sec)

Threads_cached :代表当前此时此刻线程缓存中有多少空闲线程。
Threads_connected:代表当前已建立连接的数量，因为一个连接就需要一个线程，所以也可以看成当前被使用的线程数。
Threads_created:代表从最近一次服务启动，已创建线程的数量，如果发现Threads_created值过大的话，表明MySQL服务器一直在创建线程，这也是比较耗cpu SYS资源，可以适当增加配置文件中thread_cache_size值。
Threads_running :代表当前激活的（非睡眠状态）线程数。并不是代表正在使用的线程数，有时候连接已建立，但是连接处于sleep状态。

(3)配置方法：
thread_cache_size=32

整理：
Threads_created  ：一般在架构设计阶段，会设置一个测试值，做压力测试。
结合zabbix监控，看一段时间内此状态的变化。
如果在一段时间内，Threads_created趋于平稳，说明对应参数设定是OK。
如果一直陡峭的增长，或者出现大量峰值，那么继续增加此值的大小，在系统资源够用的情况下（内存）
```

## 8.10 innodb_buffer_pool_size *****

```sh
（1）简介
对于InnoDB表来说，innodb_buffer_pool_size的作用就相当于key_buffer_size对于MyISAM表的作用一样。
（2）配置依据：
InnoDB使用该参数指定大小的内存来缓冲数据和索引。
对于单独的MySQL数据库服务器，最大可以把该值设置成物理内存的80%,一般我们建议不要超过物理内存的70%。
（3）配置方法
innodb_buffer_pool_size=2048M
```

## 8.11 innodb_flush_log_at_trx_commit ******

```sh
（1）简介
主要控制了innodb将log buffer中的数据写入日志文件并flush磁盘的时间点，取值分别为0、1、2三个。
0，表示当事务提交时，不做日志写入操作，而是每秒钟将log buffer中的数据写入日志文件并flush磁盘一次；
1，
每次事务的提交都会引起redo日志文件写入、flush磁盘的操作，确保了事务的ACID；
2，每次事务提交引起写入日志文件的动作,但每秒钟完成一次flush磁盘操作。

（2）配置依据
实际测试发现，该值对插入数据的速度影响非常大，设置为2时插入10000条记录只需要2秒，设置为0时只需要1秒，而设置为1时则需要229秒。因此，MySQL手册也建议尽量将插入操作合并成一个事务，这样可以大幅提高速度。
根据MySQL官方文档，在允许丢失最近部分事务的危险的前提下，可以把该值设为0或2。
（3）配置方法
innodb_flush_log_at_trx_commit=1
双1标准中的一个1
```

## 8.12 innodb_thread_concurrency ***

```SH
（1）简介
此参数用来设置innodb线程的并发数量，默认值为0表示不限制。
（2）配置依据
在官方doc上，对于innodb_thread_concurrency的使用，也给出了一些建议，如下：
如果一个工作负载中，并发用户线程的数量小于64，建议设置innodb_thread_concurrency=0；
如果工作负载一直较为严重甚至偶尔达到顶峰，建议先设置innodb_thread_concurrency=128，
并通过不断的降低这个参数，96, 80, 64等等，直到发现能够提供最佳性能的线程数，
例如，假设系统通常有40到50个用户，但定期的数量增加至60，70，甚至200。你会发现，
性能在80个并发用户设置时表现稳定，如果高于这个数，性能反而下降。在这种情况下，
建议设置innodb_thread_concurrency参数为80，以避免影响性能。
如果你不希望InnoDB使用的虚拟CPU数量比用户线程使用的虚拟CPU更多（比如20个虚拟CPU），
建议通过设置innodb_thread_concurrency 参数为这个值（也可能更低，这取决于性能体现），
如果你的目标是将MySQL与其他应用隔离，你可以l考虑绑定mysqld进程到专有的虚拟CPU。
但是需 要注意的是，这种绑定，在myslqd进程一直不是很忙的情况下，可能会导致非最优的硬件使用率。在这种情况下，
你可能会设置mysqld进程绑定的虚拟 CPU，允许其他应用程序使用虚拟CPU的一部分或全部。
在某些情况下，最佳的innodb_thread_concurrency参数设置可以比虚拟CPU的数量小。
定期检测和分析系统，负载量、用户数或者工作环境的改变可能都需要对innodb_thread_concurrency参数的设置进行调整。

128   -----> top  cpu  

设置标准：
1、当前系统cpu使用情况，均不均匀
top

2、当前的连接数，有没有达到顶峰
show status like 'threads_%';
show processlist;

（3）配置方法：
innodb_thread_concurrency=8

方法:
    1. 看top ,观察每个cpu的各自的负载情况
    2. 发现不平均,先设置参数为cpu个数,然后不断增加(一倍)这个数值
    3. 一直观察top状态,直到达到比较均匀时,说明已经到位了.
```

## 8.13 innodb_log_buffer_size

```SH
此参数确定些日志文件所用的内存大小，以M为单位。缓冲区更大能提高性能，对于较大的事务，可以增大缓存大小。
innodb_log_buffer_size=128M

设定依据：
1、大事务： 存储过程调用 CALL
2、多事务
```

## 8.14 innodb_log_file_size = 100M *****

```SH
设置 ib_logfile0  ib_logfile1 
此参数确定数据日志文件的大小，以M为单位，更大的设置可以提高性能.
innodb_log_file_size = 100M
innodb_log_files_in_group = 3 *****

为提高性能，MySQL可以以循环方式将日志文件写到多个文件。推荐设置为3

read_buffer_size = 1M **
MySql读入缓冲区大小。对表进行顺序扫描的请求将分配一个读入缓冲区，MySql会为它分配一段内存缓冲区。如果对表的顺序扫描请求非常频繁，并且你认为频繁扫描进行得太慢，可以通过增加该变量值以及内存缓冲区大小提高其性能。和 sort_buffer_size一样，该参数对应的分配内存也是每个连接独享

read_rnd_buffer_size = 1M **
MySql的随机读（查询操作）缓冲区大小。当按任意顺序读取行时(例如，按照排序顺序)，将分配一个随机读缓存区。进行排序查询时，MySql会首先扫描一遍该缓冲，以避免磁盘搜索，提高查询速度，如果需要排序大量数据，可适当调高该值。但MySql会为每个客户连接发放该缓冲空间，所以应尽量适当设置该值，以避免内存开销过大。
注：顺序读是指根据索引的叶节点数据就能顺序地读取所需要的行数据。随机读是指一般需要根据辅助索引叶节点中的主键寻找实际行数据，而辅助索引和主键所在的数据段不同，因此访问方式是随机的。

bulk_insert_buffer_size = 8M **

change_buffer_size=8M

批量插入数据缓存大小，可以有效提高插入效率，默认为8M
tokuDB    percona
myrocks   
RocksDB
TiDB
MongoDB
HBASE
```

## 8.15 binary log *****

```SH
双1标准(基于安全的控制)：
sync_binlog=1   什么时候刷新binlog到磁盘，每次事务commit
innodb_flush_log_at_trx_commit=1
```

## 8.16 安全参数 *****

```SH
Innodb_flush_method=(O_DIRECT, fsync) 

1、fsync    ：
（1）在数据页需要持久化时，首先将数据写入OS buffer中，然后由os决定什么时候写入磁盘
（2）在redo buffuer需要持久化时，首先将数据写入OS buffer中，然后由os决定什么时候写入磁盘
但，如果innodb_flush_log_at_trx_commit=1的话，日志还是直接每次commit直接写入磁盘

2、 Innodb_flush_method=O_DIRECT
（1）在数据页需要持久化时，直接写入磁盘
（2）在redo buffuer需要持久化时，首先将数据写入OS buffer中，然后由os决定什么时候写入磁盘
但，如果innodb_flush_log_at_trx_commit=1的话，日志还是直接每次commit直接写入磁盘

最安全模式：
innodb_flush_log_at_trx_commit=1
innodb_flush_method=O_DIRECT

最高性能模式：
innodb_flush_log_at_trx_commit=0
innodb_flush_method=fsync

一般情况下，我们更偏向于安全。 
“双一标准”
innodb_flush_log_at_trx_commit=1                ***************
sync_binlog=1                                   ***************
innodb_flush_method=O_DIRECT
一般情况下，我们更偏向于性能的话。
innodb_flush_log_at_trx_commit=0                ***************
sync_binlog=0                                   ***************
innodb_flush_method=fsync
```

## 8.17. 参数优化结果

```SH
[mysqld]
basedir=/data/mysql
datadir=/data/mysql/data
socket=/tmp/mysql.sock
log-error=/var/log/mysql.log
log_bin=/data/binlog/mysql-bin
binlog_format=row
skip-name-resolve
server-id=52
gtid-mode=on
enforce-gtid-consistency=true
log-slave-updates=1
relay_log_purge=0
max_connections=1024
back_log=128
wait_timeout=60
interactive_timeout=7200
key_buffer_size=16M
query_cache_size=64M
query_cache_type=1
query_cache_limit=50M
max_connect_errors=20
sort_buffer_size=2M
max_allowed_packet=32M
join_buffer_size=2M
thread_cache_size=200
innodb_buffer_pool_size=4096M
innodb_flush_log_at_trx_commit=1
innodb_log_buffer_size=32M
innodb_log_file_size=128M
innodb_log_files_in_group=3
binlog_cache_size=2M
max_binlog_cache_size=8M
max_binlog_size=512M
expire_logs_days=7
read_buffer_size=2M
read_rnd_buffer_size=2M
bulk_insert_buffer_size=8M
[client]
socket=/tmp/mysql.sock  
        
再次压力测试  ：
mysqlslap --defaults-file=/etc/my.cnf --concurrency=100 --iterations=1 --create-schema='oldboy' --query="select * from oldboy.t_100w where k2='FGCD'" engine=innodb --number-of-queries=200000 -uroot -p123 -verbose
```

