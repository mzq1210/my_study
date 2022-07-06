# inotify+rsync（实时同步）

[TOC]



### 服务器

> nfs01 10.0.0.31
> backup 10.0.0.41

> backup 服务器安装好rsync，保持正常环境。/backup 目录所有者，所属组都为rsync

### 1.第一个历程: 安装软件程序(nfs01)

	yum install -y inotify-tools

### 2.创建backup目录(nfs01)

```sh
[root@nfs01 backup]# mkdir /backup
[root@nfs01 backup]# ll /backup/ -d 
drwxr-xr-x. 3 root root 28 Jul 16 09:49 /backup/
```

### 3.第二个历程:编写脚本(nfs01)

> /server/scripts/inotify.sh

```sh
#!/bin/bash
inotifywait -mrq --timefmt "%F %T" --format "%T %w %f %e" -e create,delete,move,close_write  /backup|\
while read line
do
   rsync -az  /backup/ --delete  rsync_backup@10.0.0.41::backup_all --password-file=/etc/rsync.password
done

```

### 3.检测

```sh
#nfs01服务器 执行
[root@nfs01 backup]# sh /server/scripts/inotify.sh
#重新开启一个窗口，进入/backup 查看文件是否同步成功


#backup服务器 进入到/backup目录下执行创建文件操作
[root@nfs01 backup]# touch a.log

```

