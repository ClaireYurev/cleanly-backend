#!/bin/sh

sed -i "s,LISTEN_PORT,$PORT,g" /etc/nginx/nginx.conf

php-fpm -D

#while ! nc -w 1 -z 127.0.0.1 9000; do sleep 0.1; done;

# errored out: chmod -R 775 storage/

composer install
composer require laravel/passport
composer require laravel-admin-ext/log-viewer -vvv

php artisan admin:import log-viewer
php artisan migrate
php artisan key:generate
php artisan cache:clear
php artisan config:clear
composer dump-autoload
php artisan optimize
php artisan passport:install
php artisan passport:keys

# two new ones
php artisan storage:link
php artisan serve

nginx