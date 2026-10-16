FROM php:8.2-apache

RUN apt-get update \
    && apt-get install -y --no-install-recommends libsqlite3-dev \
    && docker-php-ext-install pdo pdo_sqlite \
    && a2enmod rewrite \
    && rm -rf /var/lib/apt/lists/*

COPY docker/apache.conf /etc/apache2/sites-available/000-default.conf
COPY docker/php.ini /usr/local/etc/php/conf.d/cookbook.ini
COPY docker/entrypoint.sh /usr/local/bin/cookbook-entrypoint.sh

COPY src /var/www/src
COPY templates /var/www/templates
COPY config /var/www/config
COPY public /var/www/html
COPY LICENSE /var/www/LICENSE

RUN chmod +x /usr/local/bin/cookbook-entrypoint.sh \
    && mkdir -p /var/www/data /var/www/html/uploads/recipes \
    && chown -R www-data:www-data /var/www/data /var/www/html/uploads /var/www/src /var/www/templates /var/www/config

ENV APACHE_DOCUMENT_ROOT=/var/www/html
ENV COOKBOOK_UPLOAD_DIR=/var/www/html/uploads/recipes

EXPOSE 80

ENTRYPOINT ["/usr/local/bin/cookbook-entrypoint.sh"]
CMD ["apache2-foreground"]
