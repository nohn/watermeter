FROM php:8.5.4-cli AS base
RUN apt-get update \
    && apt-get install -y libmagickwand-dev tesseract-ocr unzip \
    && pecl install imagick \
    && docker-php-ext-enable imagick

FROM base AS build
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
WORKDIR /usr/src/watermeter

# Create necessary directories and set permissions
RUN mkdir -p log/debug log/error tmp \
    && chmod -R 777 log tmp

COPY composer.json ./
RUN composer install --no-ansi --no-interaction --no-progress
COPY . .

FROM build AS test
# Run Static Analysis
RUN vendor/bin/phpstan analyse --no-progress --memory-limit=512M src classes
# Run Unit Tests
RUN vendor/bin/phpunit tests

FROM base AS final
WORKDIR /usr/src/watermeter

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
COPY composer.json ./

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
