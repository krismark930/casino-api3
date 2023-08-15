#!/bin/bash

sh /start.sh &
php artisan migrate 
php artisan route:cache
chmod -R 755 /var/www/html
mkdir -p /var/www/html/storage/framework/cache/data
mkdir -p /var/www/html/storage/framework/views
mkdir -p /var/www/html/storage/framework/sessions
chmod -R 777 /var/www/html/storage
mkdir -p /var/www/html/storage/app/public/tmp
hmod -R 777 /var/www/html/storage/app/public/tmp
php artisan cache:clear
cd /var/www/html
echo "test"
php artisan storage:link
sh ./queue.sh &
php artisan schedule:work 