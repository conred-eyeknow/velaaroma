# Dockerfile para Vela Aroma
FROM php:8.1-apache

# Instalar extensiones PHP necesarias
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Habilitar mod_rewrite de Apache
RUN a2enmod rewrite

# Copiar archivos del proyecto
COPY . /var/www/html/

# Configurar permisos
RUN chown -R www-data:www-data /var/www/html
RUN chmod -R 755 /var/www/html

# Configurar Apache para permitir .htaccess
COPY docker/apache-config.conf /etc/apache2/sites-available/000-default.conf

# Puerto de exposición
EXPOSE 80

# Comando de inicio
CMD ["apache2-foreground"]