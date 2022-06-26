### Mac安装python环境

#### 安装pyevn（**python**版本控制工具）

```bash
#安装
$ brew install pyenv

#添加环境变量
$ export PYENV_ROOT=~/.pyenv
$ export PATH=$PYENV_ROOT/shims:$PATH

#查看所有的python版本
# * 表示当前正在使用的版本，system表示用的是系统python版本
$ pyenv versions
～> * system (set by /Users/xxx/.pyenv/version)

#查看可安装python版本
$ pyenv install --list

#卸载python版本
$ pyenv uninstall 3.5.5

#查看pyenv指令列表
$ pyenv commands

#选择版本进行安装
$ pyenv install 3.7
# 如果是macOS big sur，直接使用上面命令会出现问题，可使用如下命令安装（修改版本号即可）,因为墙的原因，下载比较慢，所以先用下面的自定义命令pyinstall把版本文件下载下来
CFLAGS="-I$(brew --prefix openssl)/include -I$(brew --prefix bzip2)/include -I$(brew --prefix readline)/include -I$(xcrun --show-sdk-path)/usr/include" LDFLAGS="-L$(brew --prefix openssl)/lib -L$(brew --prefix readline)/lib -L$(brew --prefix zlib)/lib -L$(brew --prefix bzip2)/lib" pyenv install --patch 3.8.0 < <(curl -sSL https://github.com/python/cpython/commit/8ea6353.patch\?full_index\=1)

#切换版本
$ pyenv global 3.5.5 # 全局切换
$ pyenv local 3.5.5 # 项目切换，只对当前目录生效
$ python -V # 验证一下是否切换成功
```



#### pyenv 使用国内源

```bash
#创建目录 ~/.pyenv/cache
#创建一个方法，放到 ~/.bashrc 文件中
function pyinstall() {
    v=$1
    echo '准备按照 Python' $v
    curl -L https://npm.taobao.org/mirrors/python/$v/Python-$v.tar.xz -o ~/.pyenv/cache/Python-$v.tar.xz
    pyenv install $v
}

#加载命令
$ source .bashrc

#使用命令
$ pyinstall 3.8.0
```



### pip包管理

```bash
$ pip install xxx
$ pip list
$ pip search keyword 或者 pypi
$ pip help install
```



#### pip 配置国内源

```bash
$ vim ~/.pip/pip.conf
[global]
index-url=http://mirrors.aliyun.com/pypi/simple
trusted-host=mirrors.aliyun.com

#阿里云、清华、豆瓣的国内源
```



#### pip导出/安装依赖包

```bash
pip freeze > requirement #导出依赖包
pip install -r requirement #安装依赖包
```
