FROM php:8.1-apache

# 1. Instalar dependencias del sistema y Node.js LTS
RUN apt-get update && \
    apt-get install -y \
    openssl \
    zip \
    unzip \
    git \
    curl \
    gnupg \
    sqlite3 \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    libjpeg-dev \
    libfreetype6-dev \
    && curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs \
    && rm -rf /var/lib/apt/lists/*

# 2. Verificar versiones instaladas
RUN node -v && npm -v

# 3. Instalar extensiones PHP necesarias
RUN docker-php-ext-configure gd --with-freetype --with-jpeg && \
    docker-php-ext-install \
        pdo \
        pdo_mysql \
        mbstring \
        exif \
        pcntl \
        bcmath \
        zip \
        gd

# 4. Habilitar mod_rewrite de Apache
RUN a2enmod rewrite

# 5. Cambiar el DocumentRoot a /public
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf && \
    sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# 6. Instalar Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# 7. Crear carpeta del proyecto y establecer permisos
WORKDIR /var/www/html
RUN chown -R www-data:www-data /var/www/html && \
    chmod -R 775 /var/www/html

# 8. Copiar script de espera por MySQL
COPY wait-for-mysql.sh /wait-for-mysql.sh
RUN chmod +x /wait-for-mysql.sh

# 9. Comando por defecto
ENTRYPOINT ["/wait-for-mysql.sh"]
