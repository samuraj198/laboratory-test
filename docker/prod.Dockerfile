# --- СТАДИЯ 1: Сборка фронтенда Vue 3 ---
FROM node:20-alpine AS frontend-builder
WORKDIR /app

# Копируем файлы конфигурации фронтенда из папки src
COPY ./src/package*.json ./
COPY ./src/vite.config.js ./
COPY ./src/tailwind.config.js ./
RUN npm ci

# Копируем весь исходный код фронта для сборки
COPY ./src ./
RUN npm run build

# --- СТАДИЯ 2: PHP + Nginx + Supervisor ---
FROM php:8.4-fpm

# Установка системных зависимостей, Nginx и Supervisor
RUN apt-get update && apt-get install -y \
    zip \
    unzip \
    git \
    nginx \
    supervisor \
    libpq-dev \
    && docker-php-ext-install pdo_mysql pdo_pgsql pcntl \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Установка расширений PCOV
RUN pecl install pcov && docker-php-ext-enable pcov \
    && echo "pcov.directory=/var/www/html" >> /usr/local/etc/php/conf.d/docker-php-ext-pcov.ini


# Установка Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Копируем исходный код бэкенда из папки src вашего репозитория
COPY ./src /var/www/html

# Копируем скомпилированный фронтенд из первой стадии (Vite собирает его в public/build)
COPY --from=frontend-builder /app/public/build /var/www/html/public/build

# Настраиваем Nginx (Файл берется из корня вашего репозитория: ./docker/nginx/...)
COPY ./nginx/nginx.prod.conf /etc/nginx/sites-available/default
RUN ln -sf /etc/nginx/sites-available/default /etc/nginx/sites-enabled/default

# Настраиваем Supervisor (Файл берется из корня вашего репозитория: ./docker/supervisor/...)
COPY ./docker/supervisor/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Устанавливаем зависимости Laravel
RUN composer install --optimize-autoloader --no-dev --no-scripts --prefer-dist

# Выдаем права пользователю www-data
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

EXPOSE 80

# Запуск миграций, кэширования и Supervisor (перенос строк оформлен правильно)
CMD php artisan migrate --force && \
    php artisan config:cache && \
    php artisan route:cache && \
    /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
