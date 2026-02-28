FROM php:8.4-cli

RUN apt-get update && \
    apt-get install -y --no-install-recommends \
      git \
      unzip \
      libxml2-dev \
      libcurl4-openssl-dev \
      libmbedtls-dev \
    && docker-php-ext-install curl xml dom && \
    apt-get clean && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app
COPY . .

RUN composer install --no-interaction --no-ansi --no-progress --no-dev

CMD ["php", "vendor/bin/phel", "run", "src/main.phel"]
