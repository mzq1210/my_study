# shell随笔

[TOC]

### shell基本概念

> 解释器类型
>
> bash是centos中的默认解释器
> sh

##### 脚本定义

> #!/bin/bash 
> 其中#!叫做幻数，用来指定脚本用的命令解释器

##### 父shell和子shell

```css
脚本嵌套
父shell中的环境变量，在子shell中可以看到
而子shell中的变量，在父shell中看不到
```

##### shell执行方式

```bash
sh & bash，最常用的使用方式
cat *.sh | bash，适用于执行多个脚本
sh < oldboy.sh，了解一下，输入重定向
/root/oldboy.sh，需要执行权限
.  oldboy.sh
source oldboy.sh
```

### 变量基础

##### 定义变量

> 值可变的量，称为变量
> 变量名=变量值，常说的变量，一般是变量名
> 字母数字下划线，不能是数字开头

##### 环境变量和普通变量

###### 环境变量（全局变量）

- 可在创建他们的shell以及派生出来的任意子shell中使用
- 环境变量包括内置的环境变量和自定义的环境变量，且通常为大写。
- 环境变量的定义方式：

```bash
declare -x 变量名=value
export 变量名=value
```

- 登陆shell会加载所有的环境变量

- 非登陆shell可能会加载~/.bashrc或者/etc/bashrc，然而有些定时任务以上两个根本不会加载，所以需要手动指定，建议在定义变量时定义到/etc/bashrc

- 可以在环境变量文件中定义普通变量

###### 普通变量

- 普通变量只有在当前shell下才能使用
- 定义方式

```sh
# 适用于一般场景，不适用于带有空格等字符
变量名=value
# 所见即所得的定义方式
变量名=’value’
# 解析双引号之内的变量
变量名=”value
```

- 注意点：（举例说明）

1. 变量如果后面有内容，一定要把变量括{}起来

2. 希望变量内容原样输出则加单引号

3. 希望获取变量中的命令执行结果用``或者$()

```sh
   1、定义环境变量的方式：
   export 变量名=变量值
   2、定义普通变量的方式：
   变量名=变量值
   3、定义变量的三种方式
   # 适用于一般场景，不适用于带有空格等字符
   [export] 变量名=value
   # 所见即所得的定义方式
   [export] 变量名=’value’
   # 解析双引号之内的变量
   [export] 变量名=”value”
   4、环境变量文件的加载顺序
   /etc/profile ===> ~/.bash_profile ===> ~/.bashrc ===> /etc/bashrc
   5、非登录式（ssh）的shell只加载后两个
```

##### 临时变量和永久变量

如果按照变量的生存周期来划分的话，Linux变量可以分为两类：

- 永久变量：需要修改变量配置文件，使得变量永久生效
- 临时变量，使用export命令或者直接在当前shell中赋值的变量

##### 变量的计算方式(与、或、非、异或)

```sh
[root@liufeng ~]#a=1
[root@liufeng ~]#b=2
#加法计算
[root@liufeng ~]#echo $a+$b   #错误
1+2
[root@liufeng ~]#let c=a+b    #正确  第一种方法
[root@liufeng ~]#echo $c
3
[root@liufeng ~]#echo $[a+b]  #正确  第二种方法
3

```

>   0  false
>
>   1  true
>
>   - & 并且  and 
>
>     0&0=0
>
>     0&1=0
>
>     1&0=0
>
>     1&1=1
>
>   
>
>   - | 或者  or
>
>     0|0=0
>
>     0|1=1
>
>     1|0=1
>
>     1|1=1
>
>   - 短路与  &&
>
>     #cmd1  命令
>
>     cmd1 && cmd2 (cmd1为假，cmd2不需要执行，反之，cmd1为真，cmd2执行)
>
>   - 短路或  ||
>
>     cmd1 || cmd2 (cmd1为真，cmd2不需要执行，反之，cmd1为假，cmd2执行)
>
>   - 异或 ^(两个相同值异或为假，反之为真)
>
>     > 理解起来，两个值是指二进制的值，出现两个1或者两个0结果为假[0]，出现两个不一样的值结果为[1]。
>     >
>     > 例如：
>     >
>     > | 十进制      | 二进制 |
>     > | ----------- | ------ |
>     > | 10          | 01010  |
>     > | 22          | 10110  |
>     > | 异或结果 28 | 11100  |
>
>     0^1=1
>
>     0^0=0
>
>     1^1=0
>
>     1^0=1
>
>     ```sh
>     #利用临时变量将a b进行互换值
>     [root@liufeng ~]#a=1
>     [root@liufeng ~]#b=2
>     [root@liufeng ~]#tmp=$a
>     [root@liufeng ~]#a=$b
>     [root@liufeng ~]#b=$tmp
>     [root@liufeng ~]#echo $a $b
>     1 2
>     #利用异或将a b进行互换值
>     [root@liufeng ~]#a=$[a^b]
>     [root@liufeng ~]#echo $a 
>     3
>     [root@liufeng ~]#b=$[a^b]
>     [root@liufeng ~]#echo $b
>     1
>     [root@liufeng ~]#a=$[a^b]
>     [root@liufeng ~]#echo $a
>     2
>     [root@liufeng ~]#echo $a $b
>     2 1
>     [root@liufeng ~]#
>     
>     ```

##### 判断两个字符串是否相同

```sh
[root@liufeng ~]#str1=aaa
[root@liufeng ~]#str2=bbb
[root@liufeng ~]#str3=aaa
[root@liufeng ~]#test $str1 = $str2   #test为内部命令   第一种方法
[root@liufeng ~]#echo $?			  #不相同为1
1
[root@liufeng ~]#test $str1 = $str3
[root@liufeng ~]#echo $?	          #相同为0
0
[root@liufeng ~]#[ $str1 = $str3 ]    #第二种方法
[root@liufeng ~]#echo $?
0

```

##### 判断变量是否为空

```sh
#help test 查看帮助(-n 是判断非空的)
[root@liufeng ~]#[ -z $val ]  #$val变量不存在，-z判断是否为空，为空$?返回0，反之返回1
[root@liufeng ~]#echo $?
0
```



### ※※※ 获取变量字符串长度的五种方法

```sh
[root@shell ~]# data="www.baidu.com"
[root@shell ~]# echo ${#data}
13
[root@shell ~]# echo ${data} |wc -L
13
[root@shell ~]# expr length $data
13
[root@shell ~]# echo ${data}|awk '{print length}'
13
[root@shell ~]# echo ${data}|awk '{print length($0)}'
13
```

###### 改变分隔符

```sh
#以换行分割
IFS=$'\n'                                                                                             
for i in $(cat  /etc/hosts)
do
    echo "$i"
    sleep 1
done

```

###### 小括号与大括号的区别：

```sh
#大括号里面定义的变量，会影响外层的变量的值
[root@liufeng ~]#{ name=liufeng;echo $name; }
liufeng
[root@liufeng ~]#echo $name
liufeng
#小括号里面定义的变量，不会影响外层的变量的值(只会执行一次，当前命令有效)
[root@liufeng ~]#(name=liufeng-1;echo $name)
liufeng-1
[root@liufeng ~]#echo $name
liufeng
```

###### $#:显示脚本的命令行中参数的个数

```bash
[root@m01 scripts]# cat test.sh 
#!/bin/bash
echo $#
[root@m01 scripts]# sh test.sh  a b c d e f 
6
[root@m01 scripts]# 
```

###### $*：显示脚本所有的参数

```bash
[root@m01 scripts]# cat test.sh 
#!/bin/bash
echo $*
[root@m01 scripts]# sh test.sh  a b c d e f 
a b c d e f

#shell $* 与 $@的区别
#未加引号,二者相同
for i in $*
do
    echo $i
done

for i in $@        
do
    echo $i
done
[root@liufeng ~]#sh /server/scripts/shell/test.sh a b c
a
b
c
a
b
c
#加引号,二者区别
for i in "$*"
do
    echo $i
done

for i in "$@"        
do
    echo $i
done
[root@liufeng ~]#sh /server/scripts/shell/test.sh a b c
a b c
a
b
c

#可以看到不加引号时,二者都是返回传入的参数,但加了引号后,此时$*把参数作为一个字符串整体(单字符串)返回,$@把每个参数作为一个字符串返回
```

###### $数字：

> $1,$2...$n：显示脚本的第n个参数（n>1）

```bash
[root@m01 scripts]# cat test.sh 
#!/bin/bash
echo $1 $2 $3 ${10}
[root@m01 scripts]# sh test.sh  a b c d e f g h i j k
a b c j
```

###### $0：表示脚本的路径名字

```bash
#全路径的名称
cat /server/scripts/shell/hosts.sh 
#!/bin/bash
echo $0
[root@liufeng ~]#sh /server/scripts/shell/hosts.sh 
/server/scripts/shell/hosts.sh
#当前文件名称(加basenaem)
cat /server/scripts/shell/hosts.sh 
#!/bin/bash
echo $(basename $0)    
[root@liufeng ~]#sh /server/scripts/shell/hosts.sh 
hosts.sh
```
###### 清空位置参数(相当于清空变量)

```sh
set --  
```

###### 删除变量

```sh
[root@liufeng ~]#name=liufeng
[root@liufeng ~]#echo $name
liufeng
[root@liufeng ~]#unset name
[root@liufeng ~]#echo $name

[root@liufeng ~]#
```

###### 定义只读变量

```sh
[root@liufeng ~]#cat /server/scripts/shell/readonly.sh 
#!/bin/bash
name=liufeng
echo $name
readonly name
name=liufeng_1
echo $name 
#运行脚本会报错,在指定name为只读变量后，无法打印出修改值，而是直接报错 readonly variable
[root@liufeng ~]#sh /server/scripts/shell/readonly.sh 
liufeng
/server/scripts/shell/readonly.sh: line 11: name: readonly variable

```

### 流程控制

> (break（循环控制）、continue（循环控制）、exit（退出脚本）、return（退出函数）)

##### 区别和对比

在上述命令中，break、continue在条件语句及循环语句（for、while、if等）中用于控制程序的走向；而exit用于终止所有语句并退出当前脚本。除此之外，exit还可以返回上一次程序或命令的执行状态给当前Shell；return类似于exit，只不过return仅用于在函数内部

| 命令       | 说明                                                         |                        |
| ---------- | ------------------------------------------------------------ | ---------------------- |
| break n    | 如果省略n，则表示跳出整个循环，n表示跳出的循环层数           | 结束本次循环           |
| continue n | 如果省略n，则表示跳过本次循环，忽略本次循环剩余代码，进入循环的下一次循环，n表示遇到第n层继续循环 | 结束当前循环开始下一次 |
| exit n     | 退出当前shell、n为上一次程序指向的状态返回值，n也可以省略，在下一个shell里面可以通过“$?”接收exit n 的n值 | 结束脚本               |
| return n   | 用于在函数里作为函数的返回值，判断函数执行是否正确。在下一个代码块里面可以通过“$?”接收return n 的n值 |                        |

### 数组

> 数组的分类:
>
> 普通数组：只能使用整数作为索引
> 关联数组：可以只用字符串作为数组索引

##### 普通数组

> 数组名[索引]=变量值

```sh
[root@liufeng ~]# array[0]=ceshi_1
[root@liufeng ~]# array[1]=ceshi_2
[root@liufeng ~]# array[2]=ceshi_3
[root@liufeng ~]# echo ${array}       #<= 使用变量方式查看，其实查看的是数组第一个内容
ceshi_1
[root@liufeng ~]# echo ${array[0]}    #<= 查看数组第一个元素（0号）
ceshi_1
[root@liufeng ~]# echo ${array[1]}    #<= 查看数组第二个元素（1号）
ceshi_2
[root@liufeng ~]# echo ${array[*]}    #<= 查看数组的所有元素
ceshi_1 ceshi_2 ceshi_3
[root@liufeng ~]# echo ${!array[*]}   #<= 查看数组的所有索引
0 1 2
[root@liufeng ~]# echo ${#array[*]}   #<= 查看数组元素个数
3
[root@liufeng ~]# array[5]=ceshi_5   #<= 使用不连续索引赋值也可以
[root@liufeng ~]# echo ${array[*]}
ceshi_1 ceshi_2 ceshi_3 ceshi_5
[root@liufeng ~]# unset array[5]      #<= 和变量类似，使用usset即可去掉数组中的的某些值
[root@liufeng ~]# echo ${array[*]}
ceshi_1 ceshi_2 ceshi_3
[root@liufeng ~]# array=(a b c d e f g) #<= 数组的批量赋值
[root@liufeng ~]# echo ${array[*]}
a b c d e f g

注：通过declare可以看到用户定义的数组
```

##### 关联数组

```sh
# 首先定义一个关联数组
[root@liufeng ~]# declare -A array_1
# 关联数组赋值
[root@liufeng ~]#array_1[index_1]=value_1
# 查看关联数组方式和查看普通数组方式相同
[root@liufeng ~]#echo ${array_1[*]} 
value_1
[root@liufeng ~]#array_1[index_2]=value_2
[root@liufeng ~]#echo ${array_1[*]} 
value_1 value_2
[root@liufeng ~]#array_1[index_3]=value_3
[root@liufeng ~]#echo ${array_1[*]} 
value_1 value_3 value_2
```

#####  数组自增

> 特殊情况下，可以用关联数组做计数

```sh
# 将array[m]（关联数组）的值累加
# 先定义关联数组
[root@liufeng ~]# declare -A array_sex
# f解释为female，女性，即关联数组中女性的值自增1
[root@liufeng ~]# let array_sex[f]++
[root@liufeng ~]# let array_sex[f]++
[root@liufeng ~]# let array_sex[f]++
# f解释为male，男性，即关联数组中男性的值自增1
[root@liufeng ~]# let array_sex[m]++
[root@liufeng ~]# let array_sex[m]++
# 查看所有值
[root@liufeng ~]# echo ${array_sex[*]}
3 2
# 查看男性的个数
[root@liufeng ~]# echo ${array_sex[m]}
2
# 查看女性个数
[root@liufeng ~]# echo ${array_sex[f]}
3
```

### for循环

> for循环和while循环类似，但是for主要用于执行次数有限的循环，而不是守护进程和无限循环。for语常见的语法有两种

- 第一种是for为变量取值型，语法如下：

```bash
for 变量名 in 变量取值列表
do
    指令
done
```

或

```bash
for 变量名 in 变量取值列表;do
    指令
done
```

##### for(( 语法

> 此语法我们称之为c语言型for循环语句，其语法结构如下

```bash
for((exp1;exp2;exp3))
do
    指令
done
```

> for关键字后的双括号内是三个表达式，第一个是变量初始化（例如：i=0），第二个为变量的范围（例如：i<100）,第三个为变量自增或自减（例如：i++）

#####  for循环打印99乘法表

```sh
[root@liufeng ~]#sh test.sh
1*1=1	 
1*2=2	2*2=4	 
1*3=3	2*3=6	3*3=9	 
1*4=4	2*4=8	3*4=12	4*4=16	 
1*5=5	2*5=10	3*5=15	4*5=20	5*5=25	 
1*6=6	2*6=12	3*6=18	4*6=24	5*6=30	6*6=36	 
1*7=7	2*7=14	3*7=21	4*7=28	5*7=35	6*7=42	7*7=49	 
1*8=8	2*8=16	3*8=24	4*8=32	5*8=40	6*8=48	7*8=56	8*8=64	 
1*9=9	2*9=18	3*9=27	4*9=36	5*9=45	6*9=54	7*9=63	8*9=72	9*9=81	 
[root@liufeng ~]#cat test.sh 
#!/bin/bash
#********************************************************************
#Author: LiuFeng
#Date： 2019-12-17
#FileName： test.sh
#Description： The test script
#*******************************************************************
for((i=1; i<=9; i++));do
	for((k=1; k<=i; k++));do
		let num=$i*$k
		 #\t的意思是制表符也就是tab键(当前相当于空格)  等价于echo -en "$k*$i=$num "
		echo -en "$k*$i=$num\t"  
	done
	echo ' '
done
```

### while循环

>循环语句常用于重复执行一条指令或一组指令，直到条件不满足停止，shell脚本语言的循环语句常见的有while、until、for、select循环语句，其中，until和select已经基本淡出历史舞台.while循环语句主要用来重复执行一组命令会语句。在企业中常用于守护进程或持续运行的程序，也有时候会用while来读取文件的每一行内容

##### while语法

```bash
while <条件表达式>
do
    指令
done

#例子1  while循环从1加到100，然后再额外使用两种方式计算1
a=0
sum=0
while [ $a -lt 100 ];do
    let a++
    let sum=$sum+$a
done
echo $sum
```
或
```bash
while <条件表达式>;do
    指令
done
```

> while循环会对紧跟在while后的条件表达式判断，如果条件成立，就执行while里面的命令或语句，每次执行到done时候就重新判断while表达式是否真的成立，直到表达式不成立才退出while循环体，如果一开始就不成立，就不会进入循环体.

##### while按行读入文件

> while可以读取指定的文件，然后可以对每行数据进行自定义处理，一共有三种方式

```sh
#exec方式，仅供了解
exec < file
sum=0
while read line
do
    echo $line
done

#cat方式
cat file | while read line
do 
    echo $line
done

#重定向输入方式(推荐使用)
while read line
do
    echo $line
done < file
```

##### 例子

> while循环案例，可以批量创建10个用户，并通过传参方式传入密码123

```bash
read -p "请输入用户名的前缀: " m
read -p "请输入希望创建的用户数：" n
read -p "请输入用户的密码：" p

expr $n + 1 &> /dev/null

if [ $? -ne 0 ];then 
    echo "请输入正确的数字"
    exit
fi

a=1
while [ $a -le $n ];do
    user=$m$a
    let a++
    id $user &> /dev/null
    if [ $? -eq 0 ];then
        echo "用户" $user "已存在,创建失败"
        continue
    else
        useradd $user
        if [ $? -eq 0 ]; then
            echo "创建" $user "成功"
        fi
    fi
done


a=1
while [ $a -le $n ];do
    user=$m$a
    let a++
    
    echo $p | passwd --stdin $user &> /dev/null

    if [ $? -eq 0 ];then
        echo "用户" $user "密码修改成功"
    else
        echo "用户" $user "密码修改失败"
    fi
done
```

> 使用case和while循环批量删除用户[y|n]

```bash
read -p "请输入用户名的前缀: " m
read -p "请输入希望删除的用户数：" n

expr $n + 1 &> /dev/null

if [ $? -ne 0 ];then 
    echo "请输入正确的数字"
    exit
fi

a=1
while [ $a -le $n ];do
    user=$m$a
    let a++
    read -p "是否确定删除？[y|n]" input
    case $input in 
        y)

        userdel -r $user &> /dev/null
        if [ $? -ne 0 ];then
            echo "用户" $user "删除失败"
        else
            echo "用户" $user "删除成功"
        fi
            ;;
        n)
            exit
            ;;
        *)
            echo "cuowu!!!"
            exit
    esac
done
```

### case语句

> case结构条件句相当于多分支的if/elif/else条件句，但是它比这些条件句看起来更规范公正，常被用于实现系统服务启动脚本等企业应用场景中。
>
> 在case语句中，程序会将case获取的变量的值与表达式部分的值1、值2、值3等逐个进行比较，如果获取的变量值与某个值（例如值1）匹配，就会执行值（例如值1）的后面对应的指令（例如指令1，可能是一组指令），直到执行到双分号（;;）才停止，然后跳出case语句主体，执行case语句（即esac字符）后面的其他命令
> 如果没有找到匹配变量的任何值，则执行*）后面的指令（通常是给使用者的使用提示），直到遇到双分号（;;）或者esac结束，这部分相当于if多分支中最后的else语句部分。另外，case语句中的表达式对应值的部分，可以使用管道及通配符等更多功能匹配

##### case语法

> case结构条件句的语法格式为：
>
> ```sh
> case “变量” in 
> 值1)
>  指令1...
>  ;;
> 值2)
>  指令2...
>  ;;
> *)
>  指令3...
>  
> esac
> ```
> 例子
> ```
> case "$1" in
>     [a-zA-Z])
>             echo '你输入的是字母'
>             ;;
>     [0-9])
>             echo '你输入的是数字'
>             ;;
>      *)
>          echo '输入有误'
> esac
> ```

### 函数

> 简单地说，函数的作用就是将程序里面多次被调用的相同代码组合起来（函数体），并为其取个名字（函数名）。其他所有想重复调用这部分代码的地方，只需要调用这个名字就好了。可以把函数独立的写到文件里，当需要调用函数时候，再加载进来使用。下面是使用shell函数的优势：
>
> - 把相同的程序段定义成函数，可以减少整个程序的代码量，提升开发效率   
> - 增加程序的可读、易读性、提升管理效率
> - 可以实现程序功能模块化，使得程序具备通用性（可移植性）
> - 对于Shell来说，Linux系统里面近2000个命令都可以说是shell的函数。所以shell的函数还是很多的

##### 函数语法

> shell函数的常见语法格式，其标准写法为：
>
> ```sh
> function 函数名(){   # 推荐书写函数的方法（带括号）
> 指令集...
> return n
> }
> ```
>
> 简化写法1：
>
> ```sh
> function 函数名 {   # 不推荐使用此方法（无括号，函数名和左花括号之间需要有空格。）
> 指令集...
> return n
> }
> ```
>
> 简化写法2：
>
> ```sh
> 函数名(){   # 不用function的方法
> 指令集...
> return n
> }
> ```



##### 函数执行

> Shell函数分为最基本的函数和可以传参的函数两种

- ##### 不带参数的函数

> 不带参数的函数执行时，直接输入函数名即可（注意不带小括号）。格式如下：
>
> ```
> testFun{
>     echo "hello world!"
> }
> 
> testFun
> ```
>
> 有关执行函数的重要说明：
>
> - 执行Shell函数时候，函数名前面的function和函数后的小括号都不要带
> -  执行函数必须要在执行前定义或加载好（先定义再执行）
> - Shell执行系统中各种程序的顺序为：系统别名-->函数-->系统命令-->可执行文件
> - 在Shell里面，return功能与exit类似，作用是退出函数，而exit是退出脚本文件。
> - 如果函数存放于独立的文件中，被脚本加载使用时，需要使用source或者. 来加载。
> - 函数执行时，会和调用它的脚本共用变量，也可以为函数设定局部变量以及特殊位置参数
> - 在函数内一般使用local定义局部变量，这些变量离开后即消失。

- ##### 带参数的函数

> 带参数的函数执行方法，格式如下
>
> ```sh
> 函数名 参数1 参数2
> ```
>
> 函数后接的参数说明：
>
> - Shell的位置函数都可以做作为函数的参数使用
>
> - 此时父脚本的参数临时的被函数参数所掩盖或隐藏
>
> -  $0比较特殊，依然是父脚本的名称
>
>   举例说明：
>
>   ```sh
>   [root@liufeng ~]# cat func1.sh 
>   #!/bin/bash
>   function hello(){
>       echo "hello $1"
>   }
>   hello liufeng
>   
>   [root@oldboy ~]# sh func1.sh 
>   hello liufeng
>   ```

### if条件语句

##### 语法

> 第一种语法：
>
> ```sh
> if <条件表达式>
> then
>     指令
> fi
> ```
>
>  第二种语法：
>
> ```sh
> if <条件表达式>;then
>     指令
> fi
> ```
>
> ```sh
> if<条件表达式>;then
>     指令集1
> else
>     指令集2
> fi
> ```
>
> 
>
> 上文中的“<条件表达式>”位置部分，可以使用test、[]、[[]]、(())等条件表达式

##### if表达式嵌套

```sh
if <条件表达式>;then
	if <条件表达式>;then
        指令
    fi
fi
```

##### 正则

```sh
if [[ ! $name =~ ^[a-Z]+$ ]];then
	echo "请输入正确的英文..."
	exit
fi


if [[ ! $number =~ ^[0-9]+$ ]];then
	echo "请输入正确的数字..."
	exit
fi
```

