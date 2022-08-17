echo " slave-announce-ip $REALIP
       slave-announce-port $PORT " >> /usr/src/redis/conf/redis.conf
redis-server /usr/src/redis/conf/redis.conf
