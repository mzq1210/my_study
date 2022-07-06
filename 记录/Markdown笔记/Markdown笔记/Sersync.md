# sersync

[TOC]

## 主要内容：

#### 1.Sersync是什么

#### 2.准备环境

#### 3.Sersync的使用

#### 4.Sersync的配置

## 一、Sersync是什么

> 实时同步:sersync/inotify
> sersync:整合inotify和rsync命令
> 监控文件/目录是否有变化（增删改）

## 二、准备环境

> web01
> nfs01
> backup

## 三、sersync的使用

##### 1.准备rsync服务(nfs01)

（1）sersync 利用到了rsync的守护进程模式
（2）backup上面的共享目录为 /nfsbackup

##### 2.准备好sersync

（1）上传 解压 加权限(sersync-master.zip在https://github.com/wsgzao/sersync下载)

```sh
[root@nfs01 GNU-Linux-x86]# tree
.
├── confxml.xml
└── sersync2

0 directories, 2 files

```

```sh
mv sersync2 sersync
```

> 如果要监控多个目录，比如data , data01,可以复制一份confxml.xml,修改名称为confxml_01.xml

```sh
#backup文件所属组所有者无所谓
[root@nfs01 backup]# ll -d /backup/
drwxr-xr-x. 2 root root 78 Jul 18 01:59 /backup/

[root@nfs01 backup]# cat /etc/rsync.passwd
oldboy123
[root@nfs01 backup]# ll -d /etc/rsync.passwd 
-rw-------. 1 root root 23 Jun  8 02:33 /etc/rsync.passwd
```

##### 3.配置confxml.xml

![sersync ](F:\Linux学习课件\other\sersync .png)

```sh
<sersync>
        <localpath watch="/data">
            <remote ip="10.0.0.41" name="backup_all"/>
            <!--<remote ip="192.168.8.39" name="tongbu"/>-->
            <!--<remote ip="192.168.8.40" name="tongbu"/>-->
        </localpath>
        <rsync>
            <commonParams params="-az"/>
            <auth start="true" users="rsync_backup" passwordfile="/etc/rsync.password"/>
            <userDefinedPort start="false" port="874"/><!-- port=874 -->
            <timeout start="false" time="100"/><!-- timeout=100 -->
            <ssh start="false"/>
        </rsync>
        <failLog path="/tmp/rsync_fail_log.sh" timeToExecute="60"/><!--default every 60mins execute once-->
        <crontab start="false" schedule="600"><!--600mins-->
            <crontabfilter start="false">
                <exclude expression="*.php"></exclude>
                <exclude expression="info/*"></exclude>
            </crontabfilter>
        </crontab>
        <plugin start="false" name="command"/>
    </sersync>

```

##### 3.启动实时同步服务

> chmod +x /usr/local/sersync/GNU-Linux-x86/sersync
> serync  -dro  /user/local/sersync/conf/confxml.xml

```sh
[root@nfs01 GNU-Linux-x86]#  /usr/local/sersync/GNU-Linux-x86/sersync -h
set the system param
execute：echo 50000000 > /proc/sys/fs/inotify/max_user_watches
execute：echo 327679 > /proc/sys/fs/inotify/max_queued_events
parse the command param
_______________________________________________________
参数-d:启用守护进程模式
参数-r:在监控前，将监控目录与远程主机用rsync命令推送一遍
c参数-n: 指定开启守护线程的数量，默认为10个
参数-o:指定配置文件，默认使用confxml.xml文件
参数-m:单独启用其他模块，使用 -m refreshCDN 开启刷新CDN模块
参数-m:单独启用其他模块，使用 -m socket 开启socket模块
参数-m:单独启用其他模块，使用 -m http 开启http模块
不加-m参数，则默认执行同步程序
________________________________________________________________
```

> 为了以后方便操作，可以加环境变量
>
> export  PATH=$PATH:/usr/local/sersync/GNU-Linux-x86/
>
> 以后可以直接使用sersync

```sh
[root@nfs01 backup]# sersync -dro /usr/local/sersync/GNU-Linux-x86/confxml.xml 
set the system param
execute：echo 50000000 > /proc/sys/fs/inotify/max_user_watches
execute：echo 327679 > /proc/sys/fs/inotify/max_queued_events
parse the command param
option: -d 	run as a daemon
option: -r 	rsync all the local files to the remote servers before the sersync work
option: -o 	config xml name：  /usr/local/sersync/GNU-Linux-x86/confxml.xml
daemon thread num: 10
parse xml config file
host ip : localhost	host port: 8008
daemon start，sersync run behind the console 
use rsync password-file :
user is	rsync_backup
passwordfile is 	/etc/rsync.password
config xml parse success
please set /etc/rsyncd.conf max connections=0 Manually
sersync working thread 12  = 1(primary thread) + 1(fail retry thread) + 10(daemon sub threads) 
Max threads numbers is: 22 = 12(Thread pool nums) + 10(Sub threads)
please according your cpu ，use -n param to adjust the cpu rate
------------------------------------------
rsync the directory recursivly to the remote servers once
working please wait...
execute command: cd /backup && rsync -az -R --delete ./ rsync_backup@10.0.0.41::backup_all --password-file=/etc/rsync.password >/dev/null 2>&1 
[root@nfs01 backup]# run the sersync: 
watch path is: /backup
```

##### 4.backup服务器

1.安装rsync

```sh
[root@backup ~]# yum -y install rsync
```

2.配置文件

```sh
[root@backup backup]# cat /etc/rsyncd.conf 
uid = rsync                    		
gid = rsync
#uid = www
#gid = www
port = 873                           
fake super = yes                                     
use chroot = no                      
max connections = 200                
timeout = 300                           
pid file = /var/run/rsyncd.pid       
lock file = /var/run/rsync.lock      
log file = /var/log/rsyncd.log       
ignore errors                        
read only = false                    
list = false                         
hosts allow = 10.0.0.31          
#hosts deny = 0.0.0.0/32              
auth users = rsync_backup            
secrets file = /etc/rsync.password   
[backup_all]                             
#comment = "backup dir"    
path = /backup/
```

3.密码文件(配置和权限600)

```sh
[root@backup backup]# cat /etc/rsync.password 
rsync_backup:oldboy123
[root@backup backup]# ll -d /etc/rsync.password 
-rw-------. 1 root root 23 Jun  8 02:33 /etc/rsync.password

#backup文件所属组，所有者变为rsync
[root@backup backup]# ll -d /backup/
drwxr-xr-x. 2 rsync rsync 78 Jul 18 01:59 /backup/
```

4.启动rsyncd并设置开机自启动

```sh
[root@backup backup]# systemctl start rsyncd
[root@backup backup]# systemctl enable rsyncd
[root@backup backup]# systemctl status rsyncd
● rsyncd.service - fast remote file copy program daemon
   Loaded: loaded (/usr/lib/systemd/system/rsyncd.service; enabled; vendor preset: disabled)
   Active: active (running) since Thu 2019-07-18 01:09:10 CST; 56min ago
 Main PID: 6453 (rsync)
   CGroup: /system.slice/rsyncd.service
           └─6453 /usr/bin/rsync --daemon --no-detach

Jul 18 01:09:10 backup systemd[1]: Started fast remote file copy program daemon.
```

##### 5.测试

>在nfs01服务器上/backup目录下创建a.log文件，backup服务器中的/backup目录已经同步过去

