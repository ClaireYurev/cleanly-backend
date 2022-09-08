#!/bin/sh

sed -i "s,LISTEN_PORT,$PORT,g" /etc/nginx/nginx.conf

php-fpm -D

#while ! nc -w 1 -z 127.0.0.1 9000; do sleep 0.1; done;

composer install

php artisan key:generate 

php artisan cache:clear

php artisan migrate

# errored out: chmod -R 775 storage/

composer dump-autoload

sudo chown -R apache storage

sudo chown -R apache bootstrap/cache

chmod -R 775 storage

chmod -R 775 bootstrap/cache

php artisan config:clear
php artisan cache:clear
php artisan optimize

#composer require laravel/passport

#php artisan passport:install
#php artisan passport:keys

nginx