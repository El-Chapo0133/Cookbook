#!/bin/sh
set -e

mkdir -p /var/www/data /var/www/html/uploads/recipes
chown -R www-data:www-data /var/www/data /var/www/html/uploads

exec docker-php-entrypoint "$@"
