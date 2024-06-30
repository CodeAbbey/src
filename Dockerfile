FROM php:8.3-apache

RUN docker-php-ext-install mysqli \
  && apt-get update && apt-get install -y mariadb-server \
  && mkdir /run/mysqld \
  && chmod a+s /usr/bin/mariadb

CMD ["/bin/bash", "-c", "coproc mariadbd --user=root && sleep 5 && apache2-foreground"]
