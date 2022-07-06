### 1.docker for window创建容器的时候需要添加网络桥接参数：

```
--net="bridge"
```

### 2.外网访问容器地址直接127.0.0.1:端口号即可

### 3.nginx项目地址为容器内部地址

### 4.解决win10 Docker的elastic容器因用户内存太小而无法启动的问题

1. 打开Window 10 的CMD
2. 执行以下命令：

```
wsl -d docker-desktop #这一句是进入一个新的控制台
echo 262144 >> /proc/sys/vm/max_map_count #这一句是调整虚拟机内存大小，然后exit退出重启es
```

### 错误

1. 1.创建kibana的时候-name参数后面需要跟kibana

```
-name kibana
```

1. 2.es.yml配置文件里面需要新增一个配置

```
node.name: node-1
```

1. 3.汉化只需要在kibana.yml配置文件中加入

```
i18n.locale: "zh-CN"
```

1. 4.docker创建mysql容器报错

```
mysqld: Error on realpath() on '/var/lib/mysql-files' (Error 2 - No such file or directory
```

 解决：在启动容器时 需要加上

```
　　-v /home/mysql/mysql-files:/var/lib/mysql-files/
```

更改my.cnf权限

```
chmod 644 /etc/mysql/my.cnf
```

## 诊断es无法同步数据：

1. 根据控制台数据判断是否从mysql读取了数据
2. 把output中的stdout注释掉，不在控制台输出mysql数据，查看es写入是否报错

