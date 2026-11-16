FROM php:8.4-apache

RUN docker-php-ext-install mysqli \
    && install -d -o www-data -g www-data /var/lib/sicapat/uploads \
    && for year in 2022 2023 2024 2025 2026; do \
         install -d -o www-data -g www-data "/var/lib/sicapat/uploads/lhp_${year}"; \
       done

COPY docker/php.ini /usr/local/etc/php/conf.d/sicapat.ini
COPY docker/apache.conf /etc/apache2/conf-enabled/sicapat.conf
COPY docker/check-db.php docker/create-admin.php /opt/sicapat/
COPY *.php /var/www/html/
COPY assets/ /var/www/html/assets/
