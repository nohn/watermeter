FROM php:8.1.2-cli
RUN apt-get update \
    && apt-get install -y libmagickwand-dev tesseract-ocr \
    && pecl install imagick \
    && docker-php-ext-enable imagick
COPY ./src/config /usr/src/watermeter/config
COPY ./log        /usr/src/watermeter/log
COPY ./public     /usr/src/watermeter/public
COPY ./src        /usr/src/watermeter/src
COPY ./vendor     /usr/src/watermeter/vendor
WORKDIR /usr/src/watermeter/public
CMD [ "php", "-S", "0.0.0.0:3000" ]