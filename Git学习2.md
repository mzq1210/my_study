![](Git学习.assets/bg2015120901.png)

> - Workspace：工作区
> - Index / Stage：暂存区
> - Repository：仓库区（或本地仓库）
> - Remote：远程仓库



#### 一、Clone远程代码

```javascript
git clone <项目地址>
```

> 该命令会在本地生成一个目录，与远程主机的版本库同名。如果要指定不同的目录名，可以指定第二个参数。

```javascript
git clone <项目地址> <本地目录名>
```



#### 二、创建本地开发分支Develop，用于日常开发

```
git checkout -b develop master
```

> 将Develop分支发布到Master分支的命令：

```javascript
#切换到Master分支
git checkout master

#对Develop分支进行合并
git merge --no-ff develop
```

> 除了常设分支以外，还有一些临时性分支，用于应对一些特定目的的版本开发。他们从Develop分支上面分出来的。开发完成后，要再并入Develop

```javascript
# 功能（feature）分支
# 预发布（release）分支
# 修补bug（fixbug）分支

#创建一个功能分支：
git checkout -b feature-1.1 develop

#开发完成后，将功能分支合并到develop分支（其他类似）：
git checkout develop
git merge --no-ff feature-1.1

#删除feature分支：
git branch -d feature-1.1
```

#### 三、更新代码git fetch，注意和git pull的区别

> 默认情况下，`git fetch`取回所有分支（branch）的更新。如果只想取回特定分支的更新，可以指定分支名。

```javascript
$ git fetch <远程主机名> <分支名>

#比如：
$ git fetch origin master

# git branch命令的-r选项，可以用来查看远程分支，-a选项查看所有分支。
$ git branch -r
origin/master

$ git branch -a
* master
  remotes/origin/master
```

#### 四、git pull

> `git pull`命令的作用是，取回远程主机某个分支的更新，再与本地的指定分支合并。

```javascript
$ git pull <远程主机名> <远程分支名>:<本地分支名>

#比如，取回`origin`主机的`next`分支，与本地的`master`分支合并。
$ git pull origin next:master
```

> 如果远程分支是与当前分支合并，则冒号后面的部分可以省略。

```
$ git pull origin next
```

## 五、git push

> `git push`命令用于将本地分支的更新，推送到远程主机。它的格式与`git pull`命令相仿。

```
$ git push <远程主机名> <本地分支名>:<远程分支名>
```

> 注意，分支推送顺序的写法是<来源地>:<目的地>，所以`git pull`是<远程分支>:<本地分支>，而`git push`是<本地分支>:<远程分支>。

> 如果省略远程分支名，则表示将本地分支推送与之存在"追踪关系"的远程分支（通常两者同名），如果该远程分支不存在，则会被新建。

```
$ git push origin master
```

> 如果当前分支只有一个追踪分支，那么主机名都可以省略。

```
$ git push
```

> 不带任何参数的`git push`，默认只推送当前分支，这叫做simple方式。此外，还有一种matching方式，会推送所有有对应的远程分支的本地分支。Git 2.0版本之前，默认采用matching方法，现在改为默认采用simple方式。如果要修改这个设置，可以采用`git config`命令。

```
$ git config --global push.default matching
# 或者
$ git config --global push.default simple
```



#### 项目发布流程:

```javascript
#切换到master主分支
git checkout master
git pull

#把开发分支合并到主分支
git merger newfang

#把本地主分支推送到远程主分支
git push origin master

#打tag标签
git tag -a v1.0-20160901 -m " "

#推送tag
git push origin v1.0-20160901

#删除tag
git tag -d v1.0-20160901
```



#### 其他命令

#### 为了便于管理，Git要求每个远程主机都必须指定一个主机名。`git remote`命令就用于管理主机名。

> 不带选项的时候，`git remote`命令列出所有远程主机。

```
$ git remote
origin
```

> 使用`-v`选项，可以参看远程主机的网址。

```
$ git remote -v
origin  git@github.com:jquery/jquery.git (fetch)
origin  git@github.com:jquery/jquery.git (push)
```

> 克隆版本库的时候，所使用的远程主机自动被Git命名为`origin`。如果想用其他的主机名，需要用`git clone`命令的`-o`选项指定。

```
$ git clone -o jQuery https://github.com/jquery/jquery.git
$ git remote
jQuery
```

> `git remote show`命令加上主机名，可以查看该主机的详细信息。

```
$ git remote show <主机名>
```

> `git remote add`命令用于添加远程主机。

```
$ git remote add <主机名> <网址>
```

> `git remote rm`命令用于删除远程主机。

```
$ git remote rm <主机名>
```

> `git remote rename`命令用于远程主机的改名。

```
$ git remote rename <原主机名> <新主机名>
```

[更多命令参考](https://www.ruanyifeng.com/blog/2015/12/git-cheat-sheet.html)

