docker run --name laravel -p 8088:80 -v /Users/villian/app/php/fujia/casino-api3:/var/www/html/app laraedit/laraedit
docker rm -rf laravel
docker run --name laravel -p 8088:80  -e WEBROOT=/var/www/html/public -v /Users/villian/app/php/fujia/casino-api3:/var/www/html tangramor/nginx-php8-fpm:php8.2.2_node19.6.0

           TZ: 'Asia/Shanghai'
            WEBROOT: '/var/www/html/public'
            PHP_REDIS_SESSION_HOST: 'redis'
            CREATE_LARAVEL_STORAGE: '1'
            COMPOSERMIRROR: 'https://mirrors.cloud.tencent.com/composer/'
            NPMMIRROR: 'https://registry.npmmirror.com'
            
composer update
php artisan passport:install
php artisan passport:keys
php artisan migrate

1. Within your app/Http directory, create a helpers.php file and add your functions.
2. Within composer.json, in the autoload block, add "files": ["app/Http/helpers.php"].
3. Run composer dump-autoload.

php artisan migrate --path=\\database\\migrations\\fileName.php

php artisan migrate:rollback --path=/database/migrations/fileName.php


# project install

1. casino-web
    - npm i
    - npm run dev

2. casino-admin

    - npm i
    - npm run dev

3. casino-api3

    - composer install
    - php artisan passport:install
    - php artisan config:clear
    - php artisan serve

4. casino-socket (file path: casino-api3/casino-socket)
    
    - npm i
    - node(nodemon) server.js
