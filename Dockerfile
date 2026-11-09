FROM php:8.4-fpm

  RUN apt-get update && apt-get install -y \
      libpq-dev \
      libicu-dev \
      libzip-dev \
      libpng-dev \
      libjpeg62-turbo-dev \
      && docker-php-ext-configure gd --with-jpeg \
      && docker-php-ext-install -j$(nproc) \
          pdo_pgsql \
          intl \
          zip \
          gd \
          opcache \
      && pecl install redis \
      && docker-php-ext-enable redis \
      && apt-get clean \
      && rm -rf /var/lib/apt/lists/*

  COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

  WORKDIR /var/www/html

  ARG APP_VERSAO=dev
  ENV APP_VERSAO=${APP_VERSAO}