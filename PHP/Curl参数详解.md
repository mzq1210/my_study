### Curl参数详解

```php
$data = [];

//上传文件
//$data['upload']=new CURLFile(realpath(getcwd().'/boy.png'));

//创建curl
$ch = curl_init();

//简写
/*curl_setopt_array($ch, [
            CURLOPT_USERPWD => $user.':'.$passwd,
            CURLOPT_URL     => $url,
            CURLOPT_RETURNTRANSFER => true
        ]);*/

//设置url
curl_setopt($ch, CURLOPT_URL, "https://github.com/search?q=react");
//设置客户端名称
curl_setopt($ch,CURLOPT_USERAGENT,"user-agent:Mozilla/50(Windows NT 5.1;vv:24.0) Gecko/20100101 Firefox/24.0");
//设置HEADER
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Content-Length:' . strlen($data)));

//POST请求，默认为GET，可以不设置
curl_setopt($ch, CURLOPT_POST, 1);
//为POST请求添加数据$data
curl_setopt($ch, CURLOPT_POSTFIELDS , $data);

//JSON请求
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Content-Length:' . strlen($data)));
//为JSON请求添加数据$data
curl_setopt($ch, CURLOPT_POSTFIELDS , $data);

//设置登录的用户名和密码
curl_setopt($ch,CURLOPT_USERPWD,"root:root");

//设置cookie必备参数
curl_setopt($ch,CURLOPT_COOKIEJAR,"cookiefile");
curl_setopt($ch,CUROPT_COOKIE,session_name().'='.session_id());

//设为0表示不检查证书
//设为2表示校验当前的域名是否与CN匹配(1去掉了)
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
//如果上面两个SSL配置都为1，则需要设置证书地址，如果证书验证失败，请到 http://curl.haxx.se/ca/cacert.pem 下载。
curl_setopt($ch,CURLOPT_CAINFO,dirname(__FILE__).'/cacert.pem');

//设置最长连接时间
//PS: 请求的时候如果只加了CURLOPT_CONNECTTIMEOUT 经常会卡死
//使用下载MP3文件做一个例子，CURLOPT_CONNECTTIMEOUT 可以设置为30秒，如果服务器30秒内没有响应，请求就会断开连接，
//CURLOPT_TIMEOUT可以设置为60秒，如果MP3文件60秒内没有下载完成，请求将会断开连接。
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
curl_setopt($ch, CURLOPT_TIMEOUT, 60);

//获取请求返回的头内容，默认false
curl_setopt($ch, CURLOPT_HEADER, 1);

//把cookie存到cookiefile.txt文件中
curl_setopt($ch,CURLOPT_COOKIESESSION,true);
curl_setopt($ch,CURLOPT_COOKIEFILE,"./cookiefile.txt");

//设置保存下载的文件
$outfile=fopen('dest.txt','web');//保存到本地的文件名
curl_setopt($ch,CURLOPT_FILE,$outfile);

//设置是否将响应结果存入变量，1是存入，0是直接echo出
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
//执行，将响应结果存入$output变量，供下面echo
$output = curl_exec($ch);
echo $output;
curl_close($ch);
```

