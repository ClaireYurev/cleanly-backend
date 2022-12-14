FROM php:8.1-fpm-alpine

RUN apk add --no-cache nginx wget

RUN apk update && apk upgrade --no-cache

RUN mkdir -p /run/nginx

COPY docker/nginx.conf /etc/nginx/nginx.conf

# Install extensions
RUN docker-php-ext-install pdo pdo_mysql
#RUN docker-php-ext-install mysqli && docker-php-ext-enable mysqli

RUN mkdir -p /app
COPY . /app

RUN sh -c "wget http://getcomposer.org/composer.phar && chmod a+x composer.phar && mv composer.phar /usr/local/bin/composer"
RUN cd /app && \
    /usr/local/bin/composer install --no-dev

RUN chown -R www-data: /app

#RUN chown -R nginx:nginx storage

#RUN chown -R nginx:nginx bootstrap/cache

#RUN chmod -R 775 storage

#RUN chmod -R 775 bootstrap/cache

CMD sh /app/docker/startup.sh