#### curl用法就是：创建curl会话 -> 配置参数 -> 执行 -> 关闭会话。

```php
//创建了一个curl会话资源，成功返回一个句柄
$ch = curl_init();
//设置URL，不用说
curl_setopt($ch, CURLOPT_URL, "baidu.com");
//上面两句可以合起来变一句
$ch = curl_init("baidu.com");
//这是设置是否将响应结果存入变量，1是存入，0是直接echo
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 0);
//执行，然后将响应结果存入$output变量，供下面echo
$output = curl_exec($ch);
//关闭这个curl会话资源
curl_close($ch);
```

#### SSH

> 除非用了非法或者自制的证书，这大多数出现在开发环境中，你才将这两行设置为`false`以避开ssl证书检查，否者不需要这么做，这么做是不安全的做法。

```php
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
```

#### (默认GET方式，所以可以省略)POST请求

```php
//表明是POST请求
curl_setopt($ch, CURLOPT_POST, 1);
//设置一个最长的可忍受的连接时间，秒为单位，总不能一直等下去变成木乃伊吧；
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60);
//设置POST的数据域，因为这里是数组数据形式的，所以用http_build_query处理一下。
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
```

#### JSON请求

```php
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Content-Length:' . strlen($data)));
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
```

#### PHP5.6以上图片上传

```php
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
$data['upload']=new CURLFile(realpath(getcwd().'/boy.png'));
curl_setopt($ch, CURLOPT_POSTFIELDS , $data);
```

#### 获取远程图片

> `curl_getinfo`方法是一个获取本次请求相关信息的方法，对于调试很有帮助，要善用。

```php
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60);
//先本地创建空文件并附加写权限
$fp=fopen('./girl.jpg', 'w');
curl_setopt($ch, CURLOPT_URL, "http://远程服务器地址马赛克/girl.jpg");
curl_setopt($ch, CURLOPT_FILE, $fp);
$output = curl_exec($ch);
$info = curl_getinfo($ch);
fclose($fp);
$size = filesize("./girl.jpg");
if ($size != $info['size_download']) {
    echo "下载的数据不完整，请重新下载";
} else {
    echo "下载数据完整";
}
```

#### HTTP认证(拿到了用户名和密码，我们怎么通过`PHP CURL`搞定HTTP认证呢？)

> `curl_setopt_array` 可以通过数组一次性地设置多个参数，防止有些需要多处设置的出现密密麻麻的`curl_setopt`方法。

```php
function curl_auth($url,$user,$passwd){
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_USERPWD => $user.':'.$passwd,
        CURLOPT_URL     => $url,
        CURLOPT_RETURNTRANSFER => true
    ]);

    $result = curl_exec($ch);
    curl_close($ch);
    return $result;
}

$authurl = 'http://要请求HTTP认证的地址';
echo curl_auth($authurl,'vace','passwd');
```

#### 利用cookie模拟登陆

> 这个事情分两步，一是去登陆界面通过账号密码登陆，然后获取cookie，二是去利用cookie模拟登陆到信息页面获取信息。

```php
<?php

//模拟登录 
function login_post($url, $cookie, $post) {
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 0);
    curl_setopt($curl, CURLOPT_COOKIEJAR, $cookie);
    curl_setopt($curl, CURLOPT_POST, 1);
    curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($post));
    curl_exec($curl);
    curl_close($curl);
}

//登录成功后获取数据 
function get_content($url, $cookie) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
    $rs = curl_exec($ch);
    curl_close($ch);
    return $rs;
}

//设置post的数据 
$post = array (
    'email' => '账户',
    'pwd' => '密码'
);

//登录地址 
$url = "登陆地址"; 

//设置cookie保存路径 
$cookie = dirname(__FILE__) . '/cookie.txt'; 

//登录后要获取信息的地址 
$url2 = "登陆后要获取信息的地址"; 

//模拟登录
login_post($url, $cookie, $post); 

//获取登录页的信息 
$content = get_content($url2, $cookie); 

//删除cookie文件
@ unlink($cookie);
var_dump($content); 
?>
```

