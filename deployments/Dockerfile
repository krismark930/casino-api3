FROM tangramor/nginx-php8-fpm:php8.2.2_node19.6.0

LABEL name="biyong admin@biyong.com"


ADD ./ /var/www/html
ENV WEBROOT=/var/www/html/public
RUN composer update
RUN chmod -R 777 /var/www/html/storage
RUN chmod +x /var/www/html/queue.sh
RUN chmod +x /var/www/html/run.sh
CMD [ "/var/www/html/run.sh" ]