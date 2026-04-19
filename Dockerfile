FROM ubuntu:24.04 AS base
RUN apt-get update \
    && apt-get install -y tesseract-ocr unzip php-cli php-imagick php-xml php-mbstring

FROM base AS build
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
WORKDIR /usr/src/watermeter

# Create necessary directories and set permissions
RUN mkdir -p log/debug log/error tmp \
    && chmod -R 777 log tmp

COPY composer.json ./
RUN composer install --no-ansi --no-interaction --no-progress --no-cache
COPY classes/ ./classes/
COPY public/ ./public/
COPY src/ ./src/
COPY LICENSE README.md ./

FROM build AS test
# Install xdebug
RUN apt install -y php-xdebug
# Copy current source again to ensure it's not cached from build stage if it was already existing
COPY classes/ ./classes/
COPY tests/ ./tests/
# Run Static Analysis
RUN vendor/bin/phpstan analyse --no-progress src classes
# Run Unit Tests
RUN XDEBUG_MODE=coverage vendor/bin/phpunit tests

FROM base AS final
WORKDIR /usr/src/watermeter

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
COPY composer.json ./

# Do not install dev dependencies on final build
RUN composer install --no-dev --optimize-autoloader --no-ansi --no-interaction --no-progress

# Create necessary directories and set permissions
RUN mkdir -p log/debug log/error tmp \
    && chmod -R 777 log tmp
# Copy current source again to ensure it's not cached from build stage if it was already existing
COPY classes/ ./classes/
COPY public/ ./public/
COPY src/ ./src/
COPY LICENSE README.md ./

# Run Smoke Test
RUN php /usr/src/watermeter/public/index.php | grep 1189.3858

WORKDIR /usr/src/watermeter/public
CMD [ "php", "-S", "0.0.0.0:3000" ]
