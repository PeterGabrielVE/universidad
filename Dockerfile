FROM php:8.1-apache

# 1. Instalar dependencias del sistema y Node.js LTS
RUN apt-get update && \
    apt-get install -y \
    openssl \
    zip \
    unzip \
    git \
    sqlite3 \
    gnupg \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    && curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs \
    && rm -rf /var/lib/apt/lists/*

# 2. Verificar versiones instaladas (útil para debugging)
RUN node --version && npm --version

# 3. Instalar extensiones PHP
RUN docker-php-ext-install pdo pdo_mysql mbstring exif pcntl bcmath gd

# 4. Configurar Apache
RUN a2enmod rewrite
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf \
    && sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# 5. Instalar Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# 6. Configurar directorio y permisos
RUN mkdir -p /var/www/html \
    && chown -R www-data:www-data /var/www/html

WORKDIR /var/www/html

# 7. Copiar solo los archivos necesarios para composer install primero
COPY composer.json composer.lock ./

# 8. Instalar dependencias de Composer (esta capa se cachea si no cambian composer.json/composer.lock)
RUN composer install --no-interaction --optimize-autoloader --no-dev

# 9. Copiar el resto de los archivos
COPY . .

# 10. Construir assets de frontend (opcional, dependiendo de tu flujo de trabajo)
RUN npm install && npm run prod

# 11. Script de entrada para comandos que deben ejecutarse al iniciar el contenedor
COPY entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/entrypoint.sh
ENTRYPOINT ["entrypoint.sh"]
