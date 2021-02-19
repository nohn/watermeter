FROM php:7.4-cli
RUN apt-get update \
    && apt-get install -y libmagickwand-dev tesseract-ocr \
    && pecl install imagick \
    && docker-php-ext-enable imagick
COPY . /usr/src/watermeter
WORKDIR /usr/src/watermeter/src
CMD [ "php", "-S", "0.0.0.0:3000" ]

