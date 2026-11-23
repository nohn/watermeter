FROM debian:trixie AS base
RUN apt-get update \
    && apt-get install -y tesseract-ocr php-cli php-imagick \
    && rm -rf /var/lib/apt/lists/*

FROM base AS build
RUN apt-get update \
    && apt-get install -y unzip php-xml php-mbstring composer \
    && rm -rf /var/lib/apt/lists/*
WORKDIR /usr/src/watermeter

# Create necessary directories and set permissions
RUN mkdir -p log/debug log/error tmp \
    && chmod -R 777 log tmp

COPY composer.json ./
COPY classes/ ./classes/
COPY public/ ./public/
COPY src/ ./src/
COPY LICENSE README.md ./

FROM build AS test
RUN composer install --no-ansi --no-interaction --no-progress --no-cache
# Install xdebug
RUN apt-get update && apt-get install -y php-xdebug && rm -rf /var/lib/apt/lists/*
# Copy current source again to ensure it's not cached from build stage if it was already existing
COPY classes/ ./classes/
COPY tests/ ./tests/
# Run Static Analysis
RUN vendor/bin/phpstan analyse --no-progress src classes
# Run Unit Tests
RUN XDEBUG_MODE=coverage vendor/bin/phpunit tests

FROM build AS production-dependencies
RUN composer install --no-dev --optimize-autoloader --no-ansi --no-interaction --no-progress --no-cache

FROM base AS final
WORKDIR /usr/src/watermeter

COPY composer.json ./
COPY --from=production-dependencies /usr/src/watermeter/vendor/ ./vendor/

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
