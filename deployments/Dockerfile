FROM tangramor/nginx-php8-fpm:php8.1.13_node19.2.0

LABEL name="biyong admin@biyong.com"


ADD ./ /var/www/html
ENV WEBROOT=/var/www/html/public
RUN composer update
RUN chmod -R 777 /var/www/html/storage
RUN chmod +x /var/www/html/queue.sh
RUN chmod +x /var/www/html/run.sh
CMD [ "/var/www/html/run.sh" ]