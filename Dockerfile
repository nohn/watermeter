FROM php:8.3.12-cli AS base
RUN apt-get update \
    && apt-get install -y libmagickwand-dev tesseract-ocr unzip \
    && pecl install imagick \
    && docker-php-ext-enable imagick

FROM base AS build
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
WORKDIR /usr/src/watermeter
COPY composer.json composer.lock ./
RUN composer install --no-ansi --no-interaction --no-progress
COPY . .

# Run Static Analysis
RUN vendor/bin/phpstan analyse src classes
# Run Unit Tests
RUN vendor/bin/phpunit tests

FROM base AS final
WORKDIR /usr/src/watermeter

# Ensure that the build stage (tests and PHPStan) has been executed successfully
# by copying a file from it. This creates a dependency so Docker won't skip it.
COPY --from=build /usr/src/watermeter/composer.json /usr/src/watermeter/composer.json
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
COPY composer.json composer.lock ./

# Do not install dev dependencies on final build
RUN composer install --no-dev --optimize-autoloader --no-ansi --no-interaction --no-progress

# Create necessary directories and set permissions
RUN mkdir -p log/debug log/error tmp \
    && chmod -R 777 log tmp

COPY classes/ ./classes/
COPY public/ ./public/
COPY src/ ./src/
COPY LICENSE README.md ./

WORKDIR /usr/src/watermeter/public
CMD [ "php", "-S", "0.0.0.0:3000" ]
