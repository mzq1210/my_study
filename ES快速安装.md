

### ES快速安装

Jps命令查看Java进程  如：es  logstash

```bash
#查看已经安装的jdk版本
[root@hadoop1 ~]# rpm -qa|grep jdk
#卸载jdk
[root@hadoop1 ~]# yum -y remove jdk-1.7.0_71-fcs.x86_64
#安装jdk-1.8.0
[root@hadoop1 ~]# yum search java
[root@hadoop1 ~]# yum -y install java-1.8.0-openjdk.x86_64

#安装es
[root@hadoop1 ~]# wget https://artifacts.elastic.co/downloads/elasticsearch/elasticsearch-7.3.0-linux-x86_64.tar.gz
#安装ik、pinyin
[root@hadoop1 ~]# ./elasticsearch-plugin install https://github.com/medcl/elasticsearch-analysis-ik/releases/download/v7.3.0/elasticsearch-analysis-ik-7.3.0.zip
[root@hadoop1 ~]# ./elasticsearch-plugin install https://github.com/medcl/elasticsearch-analysis-pinyin/releases/download/v7.3.0/elasticsearch-analysis-pinyin-7.3.0.zip
#安装kibana
[root@hadoop1 ~]# wget https://artifacts.elastic.co/downloads/kibana/kibana-7.3.0-linux-x86_64.tar.gz
#安装logstash
[root@hadoop1 ~]# wget https://artifacts.elastic.co/downloads/logstash/logstash-7.3.0.tar.gz

#查看创建的索引
[root@hadoop1 ~]# curl -XGET 'localhost:9200/_cat/indices?v&pretty'

#启动
[root@hadoop1 ~]# nohup ./elasticsearch &
```



#### Nginx代理

```nginx
listen 80;
server_name es.watcn.com;
index index.php index.html index.htm default.php default.htm default.html;
root /www/wwwroot/es.watcn.com;

location / {
    proxy_pass http://127.0.0.1:9200/;
    proxy_redirect  off;
    proxy_set_header Host $host;
    proxy_set_header X-Real-IP $remote_addr;
    proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
}


listen 80;
server_name kibana.watcn.com;
index index.php index.html index.htm default.php default.htm default.html;
root /www/wwwroot/kibana.watcn.com;
location / {
    #auth_basic "secret";
    #auth_basic_user_file /www/server/nginx/passwd/kibana.db;
    proxy_pass http://127.0.0.1:5601;
    proxy_redirect off;
}
```



#### 其他参考

[解决nohup.out文件过大](https://blog.csdn.net/qq_43318840/article/details/120408785)

[xpack ca证书](https://blog.csdn.net/less_more548/article/details/108200433)