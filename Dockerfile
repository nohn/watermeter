FROM php:8.1.0alpha2-cli
RUN apt-get update \
    && apt-get install -y libmagickwand-dev tesseract-ocr \
    && pecl install imagick \
    && docker-php-ext-enable imagick
COPY ./src /usr/src/watermeter/src
COPY ./vendor /usr/src/watermeter/vendor
WORKDIR /usr/src/watermeter/src
CMD [ "php", "-S", "0.0.0.0:3000" ]


