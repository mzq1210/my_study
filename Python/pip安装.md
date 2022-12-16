### Mac更换pip源

> 报错：HTTPSConnectionPool(host='files.pythonhosted.org', port=443): Read timed out
>
> 这是因为墙的原因，换源解决

```bash
cd ~/Library/Application Support
#查看是否有pip/pip.conf，没有就创建并配置
[global]
index-url = https://mirrors.aliyun.com/pypi/simple/
[install]
trusted-host = mirrors.aliyun.com
#查询在使用那个源
pip3 config list
```

Mac无法打开chormdriver 因为无法验证开发者的解决方案

```bash
 xattr -d com.apple.quarantine chromedriver
```





### Selenium安装

> 宝塔环境下php7.0和google-chrome有冲突，所以需要把google-chrome安装到别的服务器

#### [1.chrome下载](http://www.chromeliulanqi.com/)

```bash
#先下载，再安装（选择：“原版Chrome -> Linux 64位：稳定版” 下载。）
yum install -y google-chrome-stable_current_x86_64.rpm
#安装glib2
yum update glib2 -y
#查看chome版本：
google-chrome --version
#创建软连接：
ln -s /usr/bin/google-chrome /usr/bin/chrome
chrome --version
```


#### [2.Chrome驱动程序安装](http://npm.taobao.org/mirrors/chromedriver/)
```bash
#下载与Chrome版本对应的驱动程序
unzip chromedriver_92.0.4515.43_linux64.zip -d /usr/local/bin/
#查看Chrome驱动版本：
chromedriver --version
mv chromedriver /usr/local/bin/
chmod -R 744 /usr/local/bin/chromedriver
```

#### 3.pip3安装selenium

```bash 
pip3 install selenium
```
