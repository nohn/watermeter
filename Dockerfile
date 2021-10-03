FROM php:8.0.11-cli
RUN apt-get update \
    && apt-get install -y libmagickwand-dev tesseract-ocr \
    && pecl install imagick \
    && docker-php-ext-enable imagick
COPY ./htdocs /usr/src/watermeter/htdocs
COPY ./config /usr/src/watermeter/config
COPY ./log /usr/src/watermeter/log
COPY ./vendor /usr/src/watermeter/vendor
WORKDIR /usr/src/watermeter/htdocs
CMD [ "php", "-S", "0.0.0.0:3000" ]