#!/bin/sh
msg=`redis-cli -a 123456 bgsave`
result=`redis-cli -a 123456 info Persistence |grep rdb_bgsave_in_progress |awk -F":" '{print $2}'`
while [ `echo  $result`  -eq "1" ] ;
do
  sleep 1
  result=`redis-cli -a 123456 info Persistence |grep rdb_bgsave_in_progress |awk -F":" '{print $2}'`
done
dateDir=`date +%Y%m%d%H`
dateFile=`date +%M`
mkdir -p /usr/src/redis/backups/$dateDir
cp /usr/src/redis/data/dump.rdb  /usr/src/redis/backups/$dateDir/$dateFile".rdb"
find /usr/src/redis/data   -mmin +1 -name dump* -exec  rm -rf {} \;


