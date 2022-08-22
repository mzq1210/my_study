FROM centos
#centos8已停止维护，更新yum源
RUN cd /etc/yum.repos.d/
RUN sed -i 's/mirrorlist/#mirrorlist/g' /etc/yum.repos.d/CentOS-*
RUN sed -i 's|#baseurl=http://mirror.centos.org|baseurl=http://vault.centos.org|g' /etc/yum.repos.d/CentOS-*
#安装相关依赖
RUN yum -y update
RUN yum -y install readline-devel gcc gcc-c++ autoconf automake make net-tools zlib vim zlib-devel openssl* pcre* wget

#tar解压的包名和--add-module的地址要根据下载的实际情况调整
RUN mkdir -p /usr/local/openresty/lualib/project && mkdir -p /usr/local/openresty/nginx/conf/user  &&  cd /tmp \
 && wget https://github.com/FRiCKLE/ngx_cache_purge/archive/refs/tags/2.3.tar.gz \
 && tar -xzvf 2.3.tar.gz \
 && rm -rf 2.3.tar.gz   \

 && wget  https://github.com/weibocom/nginx-upsync-module/archive/v2.1.0.tar.gz \
 && tar -xzvf v2.1.0.tar.gz \
 && rm  -rf   v2.1.0.tar.gz \

 && wget https://openresty.org/download/openresty-1.15.8.2.tar.gz  \
 && tar -xzvf openresty-1.15.8.2.tar.gz \
 && cd openresty-1.15.8.2 \
 && ./configure --prefix=/usr/local/openresty  --with-luajit \
    --add-module=/tmp/nginx-upsync-module-2.1.0 \
 	--add-module=/tmp/ngx_cache_purge-2.3 \
    &&  make &&  make install

RUN  ln -s /usr/local/openresty/bin/*  /usr/bin

CMD ["openresty","-c","/usr/local/openresty/nginx/conf/nginx.conf"]
