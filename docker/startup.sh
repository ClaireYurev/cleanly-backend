#!/bin/sh

sed -i "s,LISTEN_PORT,$PORT,g" /etc/nginx/nginx.conf

php-fpm -D

composer require laravel/passport

php artisan migrate

php artisan passport:install

php artisan passport:keys

php artisan config:clear

#while ! nc -w 1 -z 127.0.0.1 9000; do sleep 0.1; done;

nginx