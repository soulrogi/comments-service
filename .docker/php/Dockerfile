FROM php:8.1-alpine

# --- Easy installation of PHP extensions in official PHP Docker images
# --- https://github.com/mlocati/docker-php-extension-installer
COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/
# ---

RUN install-php-extensions @composer

WORKDIR /var/www/app
