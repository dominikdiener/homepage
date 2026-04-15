FROM php:8.3-apache

# mod_rewrite aktivieren (für saubere URLs)
RUN a2enmod rewrite

# Mail-Dienst installieren (für Kontaktformular via Microsoft 365)
RUN apt-get update && apt-get install -y msmtp msmtp-mta && rm -rf /var/lib/apt/lists/* \
    && echo 'sendmail_path = "/usr/bin/msmtp -t"' > /usr/local/etc/php/conf.d/mail.ini

# PHP Upload-Limits erhöhen (für PDFs)
RUN echo "upload_max_filesize = 20M\npost_max_size = 25M" > /usr/local/etc/php/conf.d/uploads.ini

# Dateien kopieren
COPY . /var/www/html/

# Berechtigungen setzen (alle Dateien lesbar, data-Ordner beschreibbar)
RUN chmod -R 755 /var/www/html/ \
    && chown -R www-data:www-data /var/www/html/data/ \
    && chmod -R 775 /var/www/html/data/ \
    && touch /var/log/msmtp.log \
    && chown www-data:www-data /var/log/msmtp.log

EXPOSE 80
