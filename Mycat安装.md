### Mycat安装

```bash
ls /opt #目录是用来存放安装包的
ls /usr/local #是用来放置解压安装文件的

#centos重启mysql
systemctl restart mysqld
#centos查看mysql状态
systemctl status mysqld

#查看防火墙是否关闭
systemctl status firewalld

#从机如果之前搭过主从复制会报错，解决：在从机的mysql控制台执行这两条命令：
stop slave
reset master
```



