# 如何区分 -bash 和-zsh

## 切换方法

-zsh 下输入 （然后输入开机密码，密码不会显示，直接输入回车即可）

```
chsh -s /bin/bash
```

-bash下输入 （然后输入开机密码，密码不会显示，直接输入回车即可）

```
chsh -s /bin/zsh
```

## 两个环境变量添加位置

-zsh下命令的环境变量添加位置

```
open ~/.zshrc 
```

-bash下命令的环境变量添加位置

```
open ~/.bash_profile
```

