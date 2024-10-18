FROM php:8.3

RUN apt-get update
RUN apt-get upgrade -y

RUN docker-php-ext-install pdo pdo_mysql

WORKDIR /app/

COPY ../../src ./src
COPY ../../vendor ./vendor
COPY ../../server.php .
COPY ../../composer.json .

EXPOSE 8000

CMD ["php", "-S", "0.0.0.0:8000", "server.php"]
