FROM redis:8.4.0
LABEL org.opencontainers.image.authors="github@chokoladis"

#/etc/redis
RUN mkdir /var/lib/redis && mkdir /var/log/redis
RUN chmod 777 /var/log/redis

COPY ./conf/redis.conf /usr/local/etc/redis/redis.conf
CMD ["redis-server"]