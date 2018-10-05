FROM registry.ci.tutu.ru/services/base-php71-rest:latest

ENV PHP_CONF /etc/php.ini
ENV FPM_WWW_CONF=/etc/php-fpm.d/www.conf

ARG PHP_MEM_LIMIT=128M
ARG PHP_POST_MAX_SIZE=8M
ARG PHP_UPLOAD_MAX_FILESIZE=2M

ARG FPM_WWW_MAX_CHILDREN=50
ARG FPM_WWW_START_SERVERS=1
ARG FPM_WWW_MIN_SPARE_SERVERS=1
ARG FPM_WWW_MAX_SPARE_SERVERS=35
ARG FPM_WWW_MAX_REQUESTS=1000


ADD ./build/nginx/nginx.app.conf /etc/nginx/conf.d/app.conf

RUN sed -i "s/memory_limit = 128M/memory_limit = ${PHP_MEM_LIMIT}/g" ${PHP_CONF} && \
	sed -i "s/post_max_size = 8M/post_max_size = ${PHP_POST_MAX_SIZE}/g" ${PHP_CONF} && \
	sed -i "s/upload_max_filesize = 2M/upload_max_filesize = ${PHP_UPLOAD_MAX_FILESIZE}/g" ${PHP_CONF} && \
	sed -i "s/pm.max_children = 50/pm.max_children = ${FPM_WWW_MAX_CHILDREN}/g" ${FPM_WWW_CONF} && \
	sed -i "s/pm.start_servers = 5/pm.start_servers = ${FPM_WWW_START_SERVERS}/g" ${FPM_WWW_CONF} && \
	sed -i "s/pm.min_spare_servers = 5/pm.min_spare_servers = ${FPM_WWW_MIN_SPARE_SERVERS}/g" ${FPM_WWW_CONF} && \
	sed -i "s/pm.max_spare_servers = 35/pm.max_spare_servers = ${FPM_WWW_MAX_SPARE_SERVERS}/g" ${FPM_WWW_CONF} && \
	sed -i "s/;pm.max_requests = 500/pm.max_requests = ${FPM_WWW_MAX_REQUESTS}/g" ${FPM_WWW_CONF}

CMD ["/entrypoint"]
ADD ./build/entrypoint.sh /entrypoint
ADD ./build/bootstrap.sh /bootstrap

ADD ./ /service/
RUN run_with_ssh_key composer install --optimize-autoloader && \
    chown -R nginx:nginx /service && \
    ln -sf /tmp /service/tmp
