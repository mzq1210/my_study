### Git学习

> commit 提交本地仓库  
>
> push提交远程仓库 

#### 基本命令

```bash
#初始化本地仓库
git init

#该命令会在本地生成一个目录，与远程主机的版本库同名。如果要指定不同的目录名，可以指定第二个参数。
git clone <项目地址> <本地目录名>

#设置签名，用来区分成员（项目级）
git config user.name myname
git config user.email 578577@qq.com

#设置签名，用来区分成员（系统级）
git config --global user.name myname
git config --global user.email 578577@qq.com

#文件恢复之前的状态
git rm --cached <file>...

#查看提交日志（注意logid索引值后面的HEAD=》，这个是指针，指向当前的版本位置）
#
git log --pretty=oneling
git log --pretty=oneling
git reflog

#切换提交的版本，推荐第一种，回退之后可以使用git reflog查看所有log
git reset --hard 索引值
git reset --hard HEAD~3...n

#reset三个命令区别
--soft
仅在本地库移动HEAD指针
--mixed
在本地库移动HEAD指针
重置缓冲区
--hard
在本地库移动HEAD指针
重置缓冲区
重置工作区

#reset找回删除的文件，前提：删除前，文件已经提交到了本地库
#1.删除操作已经commit提交到了本地库，HEAD指针指向索引值
#2.删除操作还未commit提到本地库，HEAD指针指向HEAD,即：
git reset --hard HEAD

#比较
git diff <file>...
#还能和历史版本比较
git diff HEAD~n <file>...
```

#### 分支

```bash
#查看分支（*指向当前分支）
git branch -v

#删除feature分支：
git branch -d feature-1.1

#创建分支
git branch new_name

#切换分支
git checkout new_name

#创建并切换到新的分支
#这个命令是将git branch develop 和git checkout develop 合在一起的结果。
git checkout -b develop

#合并分支，先切换到master分支,再合并
git checkout master
git merge new_name

#解决合并时的冲突，<<<<<<HEAD代表当前分支修改的内容，解决完冲突后提交到本地仓库，注意此时commit别带文件名，不然会报错
git add <file>...
git commit -m "merge add"
```

#### Tag版本

```bash
#打tag标签
git tag -a v1.0-20160901 -m "加版本"

#推送tag
git push origin v1.0-20160901

#删除tag
git tag -d v1.0-20160901
```

#### Github远程库

> 点击头像下拉中的new repository，只填写一个repository name即可

```bash
#查看本地项目远程地址
git remote -v

#增加本地项目的远程地址
git remote add origin http://github地址

#推送建立本地与远程的联系
git push origin master

pull = fetch + merge
fetch相当于先下载远程仓库的内容不覆盖，可以切换到别的分支查看修改的内容，没问题了就merge，如果不需要看则直接pull
```

> 别人克隆项目之后是无法直接提交的，因为没有权限，需要在GitHub进入项目，点击settings->collaborators添加对方的GitHub用户名，复制右上角的链接发给对方，当然对方的邮件中也可能有提示，对方接受邀请即可成为团队成员

#### 跨团队合作 Fork (GitHub)

#### SSH登录

> http的方式每次都需要输入密码很不方便，所以使用ssh方式

```bash
cd ~
ssh-keygin -t rsa -C 6465@qq.com
cd ~/.ssh
cat id_rsa.pub

#复制id_rsa.pub的内容到GitHub用户中心SSH and GPG keys，点击new ssh keys增加一个
#把之前的http地址改为ssh的，下面的origin_ssh是自定义的可以改，但是需要注意push后面也要一致
git remote add origin_ssh git@github地址
git remote -v
git push origin_ssh master

```

#### Gitlaba安装 [官网](https://about.gitlab.com/)

> 安装前需要下载安装包 [gitlab安装包](https://packages.gitlab.com/gitlab/gitlab-ce/packages/el/7/gitlab-ce-10.8.2-ce.0.el7.x86_64.rpm) 点击右上角下载，上传到/opt目录

```bash
sudo rpm -ivh /opt/gitlab-ce-10.8.2-ce.0.el7.x86_64.rpm
sudo yum install -y curl policycoreutils-python openssh-server cronie
sudo lokkit -s http -s ssh
sudo yum install postfix
sudo service postfix start
sudo chkconfig postfix on
curl https://packages.gitlab.com/install/repositories/gitlab/gitlab-ce/script.rpm.sh | sudo bash
sudo EXTERNAL_URL="http://gitlab.example.com" 
yum -y install gitlab-ce

#安装过程很长，中间很多卡顿，耐心等待即可
```

[更多命令参考](https://www.ruanyifeng.com/blog/2015/12/git-cheat-sheet.html)
