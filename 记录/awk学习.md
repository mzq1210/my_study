### awk学习

![](awk学习.assets/1089507-20170126222420597-662074402.jpg)



#### 基本使用及参数

```bash
# 格式
# 每一行称为记录，记录中的每个元素称为字段，同理，文件代表数据表
$ awk 动作 文件名

# print是打印命令，$0代表当前行
$ echo 'this is a test' | awk '{print $0}'
this is a test

# 示例/etc/passwd文件为demo.txt
# -F参数指定分隔符为冒号
# awk会根据空格和制表符，将每一行分成若干字段，依次用$1、$2、$3代表字段
$ awk -F ':' '{ print $1 }' demo.txt

# 变量NF表示当前行有多少个字段，因此$NF就代表最后一个字段。
$ echo 'this is a test' | awk '{print $NF}'
test

# $(NF-1)代表倒数第二个字段。
# print命令里面的逗号，表示输出的时候，两个部分之间使用空格分隔。
$ awk -F ':' '{print $1, $(NF-1)}' demo.txt

# 变量NR表示当前处理的是第几行，NR后面的括号只是一个分隔符。
$ awk -F ':' '{print NR ") " $1}' demo.txt
```

`awk`的其他内置变量如下。

> - `FILENAME`：当前文件名
> - `FS`：字段分隔符，默认是空格和制表符。
> - `RS`：行分隔符，用于分割每一行，默认是换行符。
> - `OFS`：输出字段的分隔符，用于打印时分隔字段，默认为空格。
> - `ORS`：输出记录的分隔符，用于打印时分隔记录，默认为换行符。
> - `OFMT`：数字输出的格式，默认为`％.6g`。



#### 函数

`awk`还提供了一些内置函数。

> ```bash
> # 函数`toupper()`用于将字符转为大写。
> $ awk -F ':' '{ print toupper($1) }' demo.txt
> ```

常用函数如下。

> - `tolower()`：字符转为小写。
> - `length()`：返回字符串长度。
> - `substr()`：返回子字符串。
> - `sin()`：正弦。
> - `cos()`：余弦。
> - `sqrt()`：平方根。
> - `rand()`：随机数。

`awk`内置函数的完整列表，可以查看[手册](https://www.gnu.org/software/gawk/manual/html_node/Built_002din.html#Built_002din)。



#### 条件

`awk`允许指定输出条件。

> ```bash
> # print`命令前面是一个正则表达式，只输出包含`usr`的行
> $ awk -F ':' '/usr/ {print $1}' demo.txt
> 
> # 输出奇数行
> $ awk -F ':' 'NR % 2 == 1 {print $1}' demo.txt
> 
> # 输出第三行以后的行
> $ awk -F ':' 'NR >3 {print $1}' demo.txt
> 
> # 下面的例子输出第一个字段等于指定值的行。
> $ awk -F ':' '$1 == "root" || $1 == "bin" {print $1}' demo.txt
> ```



#### if

```bash
$ awk -F ':' '{if ($1 > 999) print $1; else print "---"}' demo.txt
```



#### 例子：从该文件中过滤出'Poe'字符串与33794712

> 结果：Poe 33794712

```bash
`[root@Gin scripts]``# awk -F '[ ,]+' '{print $3" "$7}' test.txt``Poe 33794712`
```



#### BEGIN 和 END 模块

> 通常，对于每个输入行 awk 都会执行每个脚本代码块一次。然而，在许多编程情况中，可能需要在 awk 开始处理文件中的文本之前执行初始化代码。对于这种情况，BEGIN 块应运而生。它是引用全局变量的极佳位置，END 块同理。

##### 实例一：统计/etc/passwd的账户人数

```bash
$ awk 'BEGIN {count=0;print "user count is ",count} {count=count+1;print $0} END {print "user count is ",count}' passwd

# 可简写为：
$ awk '{count++;print $0;} END{print "user count is ",count}' 
```

##### 实例二：统计某个文件夹下的文件占用的字节数

```bash
$ ll |awk 'BEGIN {size=0;} {size=size+$5;} END{print "size is ", size}'

# 如果以M为单位显示
$ ll |awk 'BEGIN{size=0;} {size=size+$5;} END{print "size is ", size/1024/1024,"M"}'
```

![](awk学习.assets/1089507-20170126224150269-207487187.jpg)

[其他可参考](https://www.cnblogs.com/ginvip/p/6352157.html)



























