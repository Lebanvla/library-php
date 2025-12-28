# docker/php/Dockerfile
FROM ubuntu:22.04

# Неинтерактивный режим для apt
ENV DEBIAN_FRONTEND=noninteractive

# Установка Apache, PHP 8.4 и расширений
RUN apt-get update && apt-get install -y \
    software-properties-common \
    && add-apt-repository ppa:ondrej/php -y \
    && apt-get update && apt-get install -y \
    apache2 \
    php8.4 \
    php8.4-cli \
    php8.4-common \
    php8.4-mysql \
    php8.4-curl \
    php8.4-gd \
    php8.4-mbstring \
    php8.4-xml \
    php8.4-zip \
    php8.4-bcmath \
    libapache2-mod-php8.4 \
    curl \
    git \
    unzip \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Настройка Apache
RUN a2enmod php8.4 rewrite headers
COPY docker/apache/sites-available/000-default.conf /etc/apache2/sites-available/000-default.conf
RUN a2ensite 000-default.conf

# Рабочая директория
WORKDIR /var/www/html

# Копируем PHP код
COPY ../../src/ /var/www/html/

# Порт
EXPOSE 80

# Запуск Apache в foreground режиме
CMD ["apache2ctl", "-D", "FOREGROUND"]
