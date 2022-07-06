# Lsyncd

[TOC]

### **简介**

> Lysncd 实际上是lua语言封装了 inotify 和 rsync 工具

### **配置**

| 主机      | 操作系统  | IP地址    |
| --------- | --------- | --------- |
| 主 nfs    | centos7.2 | 10.0.0.31 |
| 备 backup | centos7.2 | 10.0.0.41 |

#### 主 nfs 搭建安装

##### 1.下载安装

```sh
[root@nfs ~]# yum -y install lsyncd
[root@nfs ~]# rpm -qa lsyncd
lsyncd-2.2.2-1.el7.x86_64
[root@nfs ~]# rpm -qc lsyncd
/etc/logrotate.d/lsyncd
/etc/lsyncd.conf                            #==》Lsyncd主配置文件
/etc/sysconfig/lsyncd
```

##### 2.配置文件/etc/lsyncd.conf

```sh
[root@nfs ~]# vim /etc/lsyncd.conf 
settings {
  logfile = "/var/log/lsyncd/lsyncd.log",
  statusFile = "/var/log/lsyncd/lsyncd.status",
  inotifyMode = "CloseWrite",
  maxProcesses = 8,
}
sync {
  default.rsync,
  source = "/backup",
  target = "rsync_backup@10.0.0.41::backup_all",
  delete = true,
  exclude = { ".*" },
  delay = 1,
  rsync = {
    binary = "/usr/bin/rsync",
    archive = true,
    compress = true,
    verbose = true,
    password_file = "/etc/rsync.password", #这个文件是主服务器的文件
    _extra = {"--bwlimit=200"}
  }
}

```

##### 3.创建密码文件

```sh
[root@nfs01 ~]# vim /etc/rsync.password 
oldboy123

[root@nfs01 ~]# chmod 600 /etc/rsync.password # 修改密码文件权限
[root@nfs01 ~]# ll -d /etc/rsync.password 
-rw-------. 1 root root 10 Jun  8 03:50 /etc/rsync.password

#backup文件所属组所有者无所谓
[root@nfs01 backup]# ll -d /backup/
drwxr-xr-x. 2 root root 78 Jul 18 01:59 /backup/
```

##### 4.启动Lsyncd并设置开机自启动

```sh
[root@nfs backup]# systemctl start lsyncd
[root@nfs backup]# systemctl enable lsyncd
[root@nfs01 backup]# systemctl status lsyncd
● lsyncd.service - Live Syncing (Mirror) Daemon
   Loaded: loaded (/usr/lib/systemd/system/lsyncd.service; enable; vendor preset: disabled)
   Active: active (running) since Thu 2019-07-18 01:46:02 CST; 1s ago
 Main PID: 7682 (lsyncd)
   CGroup: /system.slice/lsyncd.service
           ├─7682 /usr/bin/lsyncd -nodaemon /etc/lsyncd.conf
           └─7683 /usr/bin/rsync --exclude-from=- --delete --ignore-errors -gvzsolptD --bwlimit=200 --password-file=/etc/rsync.pas...

Jul 18 01:46:02 nfs01 systemd[1]: Started Live Syncing (Mirror) Daemon.
```

#### 备 backup 搭建安装

##### 1.安装rsync

```sh
[root@backup ~]# yum -y install rsync
```

##### 2.配置文件

```sh
[root@backup backup]# vim /etc/rsyncd.conf
uid = rsync
gid = rsync
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
secrets file = /etc/rsync.password   #--- 创建一个认证用户密码文件(rsync_backup:oldboy123)
[backup_all]
#comment = "backup dir"    
path = /backup/

```

##### 3.密码文件(配置和权限600)

```sh
[root@backup backup]# cat /etc/rsync.password 
rsync_backup:oldboy123
[root@backup backup]# ll -d /etc/rsync.password 
-rw-------. 1 root root 23 Jun  8 02:33 /etc/rsync.password

#backup文件所属组，所有者变为rsync
[root@backup backup]# ll -d /backup/
drwxr-xr-x. 2 rsync rsync 78 Jul 18 01:59 /backup/
```

##### 4.启动rsyncd并设置开机自启动

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

```sh
#==》nfs
[root@nfs ~]# touch /backup/test1{1..5}.txt
[root@nfs01 backup]# ll
total 0
-rw-r--r--. 1 root root 0 Jul 18 01:49 text01.txt
-rw-r--r--. 1 root root 0 Jul 18 01:49 text02.txt
-rw-r--r--. 1 root root 0 Jul 18 01:49 text03.txt
-rw-r--r--. 1 root root 0 Jul 18 01:49 text04.txt
-rw-r--r--. 1 root root 0 Jul 18 01:49 text05.txt

#==》backup
[root@backup backup]# ll
total 0
-rw-r--r--. 1 rsync rsync 0 Jul 18 01:49 text01.txt
-rw-r--r--. 1 rsync rsync 0 Jul 18 01:49 text02.txt
-rw-r--r--. 1 rsync rsync 0 Jul 18 01:49 text03.txt
-rw-r--r--. 1 rsync rsync 0 Jul 18 01:49 text04.txt
-rw-r--r--. 1 rsync rsync 0 Jul 18 01:49 text05.txt
```



