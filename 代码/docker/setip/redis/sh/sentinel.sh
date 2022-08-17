echo " sentinel announce-ip $REALIP
       sentinel announce-port $PORT " >> /usr/src/redis/conf/sentinel.conf
redis-sentinel /usr/src/redis/conf/sentinel.conf
