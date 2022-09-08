#!/bin/sh

sed -i "s,LISTEN_PORT,$PORT,g" /etc/nginx/nginx.conf

php-fpm -D

#while ! nc -w 1 -z 127.0.0.1 9000; do sleep 0.1; done;

# composer install

# errored out: chmod -R 775 storage/



#composer require laravel/passport

#php artisan passport:install
#php artisan passport:keys

nginx