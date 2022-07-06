# Nginx:日志切割

[TOC]

> 我们知道，nginx会将访问日志写入到access.log日志文件中，天长日久，access.log文件就会越来越大，如果访问量巨大，并不用多长时间，access.log文件的体积就会变得非常大，对于我们的管理工作来说，这是不利的，首先，当我们打开一个非常大的日志文件时，就会比较慢，而且，从一个非常大的日志中找到某个时间段的日志也会比较慢，所以，我们最好将日志按天分割开（或者按照你觉得合适的时间段分隔开），比如，每天晚上0点5分生成一个新的日志文件，0点5分之后（新的一天）的日志写入到新的日志文件中，之前的日志则保留在老的文件中，这样每天就会生成一个日志文件，而不是将所有日志都写入到同一个日志文件中。
>
> 所以，我们需要为nginx配置"日志分割"的功能，或者称之为"日志滚动"的功能，说到nginx的日志切割，要分如下两种情况来说：
>
> 一、通过编译的方式安装nginx后，默认没有日志分割的功能。
>
> 二、通过yum源的方式安装nginx后，默认会对nginx日志进行切割。
>
> 也就是说，当安装完nginx以后，默认是否存在日志滚动的功能，取决于你的安装方式，看完这篇文章你就会理解为什么会出现这种情况。
>
> 为nginx实现日志切割的方法通常有两种，第一种方法是编写脚本实现日志切割，第二种方法是使用系统自带的日志滚动软件"logrotate"完成日志切割，但是无论选择哪种方法，其实都是殊途同归，在本质上都是一样的，那么，我们先来看看怎样通过最"原始"的方法为nginx进行日志滚动。
>
> 
>
> 在之前的文章中，我们已经了解到，通过"nginx -s"命令可以向nginx的主进程(master进程)发送信号，这些信号就是quit信号、stop信号、reload信号以及reopen信号，其实，我们借助reopen信号，就能为nginx实现日志滚动的效果，此处先演示手动实现日志滚动的操作，手动操作步骤如下：
>
> ```sh
> 一、进入日志目录
> cd /var/log/nginx
>  
> 二、重命名日志文件
> 此处，假设当前时间为2019年2月12日凌晨0点5分，我想要在这个时间点切割日志，所谓的"切割"，并不是真的把一个文件"切成两个"，只是把原来的"access.log"文件重命名，比如重命名为昨天的日期"access.log-20190211"，然后再创建一个名为"access.log"的新文件，以便新生成的日志仍然可以写入到名为"access.log"的新文件中，这样就能实现所谓的"日志滚动"或者"日志切割"的效果了。
> 但是，这样做会遇到一些问题，我们来手动操作一下，首先，重命名文件
> # mv access.log access.log-20190211
> 我们已经重命名了"access.log"文件，但是你会发现，重命名后，nginx日志仍然会写入到"access.log-20190211"文件中，并不会自动创建一个新的"access.log"文件，即使你手动创建了一个新的"access.log"文件，nginx仍然会把日志写入到重命名后的"access.log-20190211"文件中。
> 出现上述情况，是因为nginx进程读写日志文件时，是通过文件描述符去操作的，虽然我们修改了原"access.log"文件的文件名，但是原文件描述符与文件本身的对应关系仍然存在，所以，单单对文件重命名是不够的，我们需要让nginx重新打开一个新文件，以便将新的日志写入到新文件中。
>  
> 三、发送信号
> 此刻，就需要用到我们刚才提到的reopen信号了，我们需要向nginx主进程发送一个reopen信号，以便nginx能够打开一个新的日志文件，具体命令如下：
> # nginx -s reopen
> 执行完上述命令后，你会发现日志目录中自动生成了一个新的"access.log"文件，再次访问nginx，会发现新生成的日志已经写入到了新生成的"access.log"文件中了。
> ```
>
> 如果每天0点5分的时候都执行一遍上述操作，就能够实现每天日志自动滚动的效果了，当然，我们需要编写一个脚本，将上述过程自动化，然后定时执行脚本即可，其实上述过程非常简单，说白了就是重命名日志文件，发送信号，生成新的日志文件。
>
> 
>
> 其实，除了能够使用"nginx -s"命令发送信号，我们也可以借助"kill"命令向nginx进程发送信号，你肯定经常使用kill命令，当你想要强制停掉进程的时候，会使用"kill -9 pid"向进程发送"SIGKILL"信号，除了"-9"代表的"SIGKILL"信号，我们也可以借助kill命令向进程发送一些别的信号，kill命令并不是此处讨论的重点，而是我们需要借助kill命令，向nginx主进程发送一个名为"USR1"的信号，在程序中，"USR1"信号的作用是可以自定义的，也就是说，当程序捕捉到"USR1"信号的时候进行什么操作，取决于编程时的设定，不同的程序采取的操作可能不同，而在nginx中，"USR1"信号可以帮助我们重新打开日志，换句话说就是，"nginx -s reopen"命令的作用和"kill -USR1 NginxPid"的作用是一样的，"NginxPid"指的是nginx的master进程的进程号，所以，在编写nginx日志滚动脚本时，你可以使用这两个命令中的任何一个，以便nginx可以重新打开日志文件。
>
> 
>
> 如果你使用了yum源的方式安装了nginx，你会发现在安装完nginx后默认就有日志滚动的功能，这是因为通过yum源安装nginx后，默认会安装一个日志滚动的配置文件，这个配置文件就是"/etc/logrotate.d/nginx"，可以看出，这是一个logrotate配置文件，也就是说，nginx借助这个配置文件，使用logrotate完成了日志分割的操作，通常情况下，centos系统默认自带logrotate，logrotate是一个日志管理工具，此处讨论的重点也不是logrotate，重点是nginx怎样通过logrotate完成日志滚动的，所以，打开"/etc/logrotate.d/nginx"配置文件，你会从中找到如下一行命令
>
> ```sh
> kill -USR1 `cat /var/run/nginx.pid`
> 看到此处你一定明白了，无论是我们自己编写脚本，还是通过别的什么方式，其实本质上都是在向nginx进程发送信号，只是实现的方法不同，本质上是完全一样的。
> ```
>
> ```sh
> [root@liufeng ~]#cat /etc/logrotate.d/nginx
> /var/log/nginx/*log {                                                                                                                                                                        
>     create 0664 nginx root
>     daily                                       
>     rotate 10									
>     missingok
>     notifempty
>     compress
>     sharedscripts
>     postrotate
>         /bin/kill -USR1 `cat /run/nginx.pid 2>/dev/null` 2>/dev/null || true
>     endscript
> }
> ```
>
> > 在该配置文件中，每个参数作用如下：
> >
> > /var/log/nginx/为nginx日志的存储目录，可以根据实际情况进行修改。
> >
> > daily：日志文件将按天轮循。
> >
> > weekly：日志文件将按周轮循。
> >
> > monthly：日志文件将按月轮循。
> >
> > missingok：在日志轮循期间，任何错误将被忽略，例如“文件无法找到”之类的错误。
> >
> > rotate 7：一次存储7个日志文件。对于第8个日志文件，时间最久的那个日志文件将被删除。
> >
> > dateext：定义日志文件后缀是日期格式,也就是切割后文件是:xxx.log-20160402.gz这样的格式。如果该参数被注释掉,切割出来是按数字递增,即前面说的 xxx.log-1这种格式。
> >
> > compress：在轮循任务完成后，已轮循的归档将使用gzip进行压缩。
> >
> > delaycompress：总是与compress选项一起用，delaycompress选项指示logrotate不要将最近的归档压缩，压缩将在下一次轮循周期进行。这在你或任何软件仍然需要读取最新归档时很有用。
> >
> > notifempty：如果是空文件的话，不进行转储。
> >
> > create 640 nginx adm：以指定的权限和用书属性，创建全新的日志文件，同时logrotate也会重命名原始日志文件。
> >
> > postrotate/endscript：在所有其它指令完成后，postrotate和endscript里面指定的命令将被执行。在这种情况下，rsyslogd进程将立即再次读取其配置并继续运行。注意：这两个关键字必须单独成行。

>  使用shell脚本切割
>
>  ```sh
>  使用shell脚本切割nginx日志很简单，shell脚本内容如下：
>  
>  vim /usr/local/cut_del_logs.sh
>  
>  #!/bin/bash
>  
>  #初始化
>  
>  LOGS_PATH=/var/log/nginx
>  
>  YESTERDAY=$(date -d "yesterday" +%Y%m%d)
>  
>  #按天切割日志
>  
>  mv ${LOGS_PATH}/ilanni.com.log ${LOGS_PATH}/ilanni.com_${YESTERDAY}.log
>  
>  #向nginx主进程发送USR1信号，重新打开日志文件，否则会继续往mv后的文件写数据的。原因在于：linux系统中，内核是根据文件描述符来找文件的。如果不这样操作导致日志切割失败。
>  
>  kill -USR1 `ps axu | grep "nginx: master process" | grep -v grep | awk '{print $2}'`
>  
>  #删除7天前的日志
>  
>  cd ${LOGS_PATH}
>  
>  find . -mtime +7 -name "*20[1-9][3-9]*" | xargs rm -f
>  
>  #或者
>  
>  #find . -mtime +7 -name "ilanni.com_*" | xargs rm -f
>  
>  exit 0
>  该shell脚本有两个功能，第一个是切割nginx日志，第二个是删除7天之前的nginx日志。
>  
>  在切割nginx日志的功能中，我们要注意该shell脚本命名切割的日志是以切割时，是以前一天的时间就行命名该日志文件的。
>  
>  所以我们在把该shell脚本放在crontab中执行时，建议在每天的0点0分执行。如下：
>  vim /etc/crontab
>  
>  0 0 * * * root /bin/sh /usr/local/cut_del_logs.sh
>  ```