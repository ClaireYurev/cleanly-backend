FROM php:8.1-fpm-alpine

RUN apk add --no-cache nginx wget

RUN mkdir -p /run/nginx

COPY docker/nginx.conf /etc/nginx/nginx.conf

# Install extensions
RUN docker-php-ext-install pdo pdo_mysql
RUN docker-php-ext-install mysqli && docker-php-ext-enable mysqli

RUN mkdir -p /app
COPY . /app

RUN sh -c "wget http://getcomposer.org/composer.phar && chmod a+x composer.phar && mv composer.phar /usr/local/bin/composer"
RUN cd /app && \
    /usr/local/bin/composer install --no-dev

RUN chown -R www-data: /app

RUN composer install

RUN php artisan key:generate 

RUN php artisan cache:clear

RUN php artisan migrate

RUN composer dump-autoload

RUN sudo chown -R nginx:nginx storage

RUN sudo chown -R nginx:nginx bootstrap/cache

RUN chmod -R 775 storage

RUN chmod -R 775 bootstrap/cache

RUN php artisan config:clear
RUN php artisan cache:clear
RUN php artisan optimize

CMD sh /app/docker/startup.sh