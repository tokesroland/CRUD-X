# Dockerfile
FROM php:8.2-apache

# Szükséges kiegészítők
RUN docker-php-ext-install pdo pdo_mysql mysqli

# URL átírás modul bekapcsolása
RUN a2enmod rewrite

# --- EZ A KULCS A MEGOLDÁSHOZ ---
# Engedélyezzük, hogy a .htaccess felülírja a beállításokat (AllowOverride All)
RUN sed -i '/<Directory \/var\/www\/>/,/<\/Directory>/ s/AllowOverride None/AllowOverride All/' /etc/apache2/apache2.conf

# Munkakönyvtár és jogok
WORKDIR /var/www/html
RUN chown -R www-data:www-data /var/www/html