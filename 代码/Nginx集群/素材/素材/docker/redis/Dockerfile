FROM centos
#centos8已停止维护，更新yum源
RUN cd /etc/yum.repos.d/
RUN sed -i 's/mirrorlist/#mirrorlist/g' /etc/yum.repos.d/CentOS-*
RUN sed -i 's|#baseurl=http://mirror.centos.org|baseurl=http://vault.centos.org|g' /etc/yum.repos.d/CentOS-*
#安装相关依赖
RUN yum -y update
RUN yum -y install readline-devel gcc gcc-c++ autoconf automake make net-tools zlib vim zlib-devel openssl* pcre* wget

#通过选择更小的镜像，删除不必要文件清理不必要的安装缓存，从而瘦身镜像
#创建相关目录能够看到日志信息跟数据跟配置文件
RUN mkdir -p /usr/src/redis \
      && mkdir -p /usr/src/redis/data \
      && mkdir -p /usr/src/redis/conf \
      && mkdir -p /usr/src/sh         \
      && mkdir -p /usr/src/module     \
      && mkdir -p /usr/src/redis/log

RUN wget  -O /usr/src/redis/redis-5.0.5.tar.gz  "https://github.com/antirez/redis/archive/5.0.5.tar.gz" \
   && tar -xzf /usr/src/redis/redis-5.0.5.tar.gz  -C /usr/src/redis \
   && cp  /usr/src/redis/redis-5.0.5/src/redis-trib.rb  /usr/src/redis/ \
   && rm -rf /usr/src/redis/redis-5.0.5.tar.tgz

RUN cd /usr/src/redis/redis-5.0.5 &&  make && make PREFIX=/usr/local/redis install \
&& ln -s /usr/local/redis/bin/*  /usr/local/bin/  && rm -rf /usr/src/redis/redis-5.0.5

COPY ./redis/sh/cluster.sh   /usr/src/sh
COPY ./redis/module/*   /usr/src/module


RUN  cd  /usr/src/module &&  make
RUN  chmod +x  /usr/src/sh/cluster.sh
