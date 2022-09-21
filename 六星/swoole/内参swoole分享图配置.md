首先确认服务器是否**开启9502**端口

```bash
netstat -ntlp
```

**设置代理**

```nginx
upstream backend{
    server 127.0.0.1:9502;
}

server
{
    listen 80;
    listen 443 ssl http2;
    server_name admin.watcn.com;
    index index.php index.html index.htm;
    root /www/wwwroot/app.watcn.com/backend/web;
    //设置ssl证书
    ssl_certificate    /www/server/panel/vhost/cert/crm.watcn.com/fullchain.pem;
    ssl_certificate_key    /www/server/panel/vhost/cert/crm.watcn.com/privkey.pem;
    ssl_session_timeout 5m;
    ssl_ciphers ECDHE-RSA-AES128-GCM-SHA256:ECDHE:ECDH:AES:HIGH:!NULL:!aNULL:!MD5:!ADH:!RC4;
    ssl_protocols  TLSv1.1 TLSv1.2;
    ssl_prefer_server_ciphers on;

	//设置代理
    location /websocket/ {
        proxy_pass http://backend;
        proxy_http_version 1.1;
        proxy_set_header        Host $host;
        proxy_set_header        X-Real-IP $remote_addr;
        proxy_set_header        X-Forwarded-For 		   $proxy_add_x_forwarded_for;
        proxy_set_header        Host $http_host;
        proxy_set_header        Whatis-Scheme $scheme;
        proxy_set_header Upgrade $http_upgrade;
        proxy_set_header Connection "upgrade";
    }
}
```

**前端请求**（js websocket最后的地址'/'要和nginx路由一致，要有都有，没有都没有），还要注意 ws只能用在http下，https下得用wss

```js
var sServer = 'wss://admin.watcn.com/websocket/';
```



参考

自动重连： https://www.cnblogs.com/pxblog/p/15396332.html

错误参考：https://blog.joshua317.com/article/61