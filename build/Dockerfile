FROM registry.ci.tutu.ru/services/base-php71-rest:latest

ADD ./build/nginx/nginx.app.conf /etc/nginx/conf.d/app.conf
ADD ./build/php/php.app.ini /etc/php.d/99-app.ini
ADD ./build/php/www.conf /etc/php-fpm.d/www.conf
EXPOSE 9001

ADD ./build/confd/php-fpm-etcd-trigger.tmpl /etc/confd/templates/
ADD ./build/confd/php-fpm-etcd-trigger.toml /etc/confd/conf.d/

CMD ["/entrypoint"]
ADD ./build/entrypoint.sh /entrypoint
ADD ./build/bootstrap.sh /bootstrap

ADD ./ /service/
RUN run_with_ssh_key composer install --optimize-autoloader && \
    chown -R nginx:nginx /service && \
    ln -sf /tmp /service/tmp
