FROM php:8.2-apache

RUN docker-php-ext-install pdo pdo_mysql
RUN pecl install xdebug && docker-php-ext-enable xdebug
COPY xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini

RUN a2enmod rewrite
COPY apache-config.conf /etc/apache2/sites-available/000-default.conf
COPY . /var/www/html
RUN chown -R www-data:www-data /var/www/html
WORKDIR /var/www/html

EXPOSE 80
