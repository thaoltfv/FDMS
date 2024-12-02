FROM php:7.4
RUN apt-get update -y && apt-get install -y openssl zip unzip git
RUN apt-get -y install gcc make autoconf libc-dev pkg-config libzip-dev
RUN docker-php-ext-install bcmath
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
WORKDIR /app
COPY . /app
RUN composer install

CMD php artisan serve --host=0.0.0.0 --port=8000
EXPOSE 8000
