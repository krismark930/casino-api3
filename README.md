docker run --name laravel -p 8088:80 -v /Users/villian/app/php/fujia/casino-api3:/var/www/html/app laraedit/laraedit
docker rm -rf laravel
docker run --name laravel -p 8088:80 --network coinuse  -e WEBROOT=/var/www/html/public -v /Users/villian/app/php/fujia/casino-api3:/var/www/html tangramor/nginx-php8-fpm:php8.2.2_node19.6.0

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
    - composer dump-autoload
    - php artisan passport:install
    - php artisan config:clear
    - php artisan serve

4. casino-socket (file path: casino-api3/casino-socket)
    
    - npm i
    - node(nodemon) server.js


## 比分

https://www.nowscore.com/index.html


## 测试账号
sniper
123456

27.126.187.10,17,24,30,37 (IP x 5)
密碼: Upq2JUhntzm4
端口: 35115

ssh -p 35115 root@27.126.187.10
ssh  root@27.126.187.127

27.126.187.127-131 (IP x 5)
root
A2epmRDYB4ba