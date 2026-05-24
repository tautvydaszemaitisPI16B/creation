FROM php:8.3-fpm

WORKDIR /var/www

RUN useradd -m appuser

COPY . .

RUN chown -R appuser:www-data /var/www \
    && chmod -R 775 /var/www/public/uploads

USER appuser

CMD ["php-fpm"]