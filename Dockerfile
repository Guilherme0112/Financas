FROM php:8.3-fpm-alpine

# Instala dependências do sistema
RUN apk add --no-cache \
    bash curl git unzip zip \
    libpng-dev libjpeg-turbo-dev libwebp-dev freetype-dev \
    libzip-dev oniguruma-dev postgresql-dev icu-dev \
    linux-headers nginx supervisor nodejs npm

# Instala extensões PHP
RUN docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install -j$(nproc) \
        bcmath ctype exif gd intl mbstring opcache pdo pdo_pgsql pgsql pcntl zip

# Instala Redis extension
RUN apk add --no-cache --virtual .build-deps $PHPIZE_DEPS \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && apk del .build-deps

# Instala Composer
COPY --from=composer:2.7 /usr/bin/composer /usr/bin/composer

# Configurações PHP inline
RUN echo "[PHP]" > /usr/local/etc/php/conf.d/app.ini \
    && echo "memory_limit = 256M" >> /usr/local/etc/php/conf.d/app.ini \
    && echo "max_execution_time = 60" >> /usr/local/etc/php/conf.d/app.ini \
    && echo "upload_max_filesize = 50M" >> /usr/local/etc/php/conf.d/app.ini \
    && echo "post_max_size = 50M" >> /usr/local/etc/php/conf.d/app.ini \
    && echo "date.timezone = America/Sao_Paulo" >> /usr/local/etc/php/conf.d/app.ini

RUN echo "[opcache]" > /usr/local/etc/php/conf.d/opcache.ini \
    && echo "opcache.enable = 1" >> /usr/local/etc/php/conf.d/opcache.ini \
    && echo "opcache.memory_consumption = 128" >> /usr/local/etc/php/conf.d/opcache.ini \
    && echo "opcache.max_accelerated_files = 10000" >> /usr/local/etc/php/conf.d/opcache.ini \
    && echo "opcache.validate_timestamps = 0" >> /usr/local/etc/php/conf.d/opcache.ini

# Configuração Nginx inline
RUN mkdir -p /etc/nginx/http.d && \
    printf 'user www-data;\nworker_processes auto;\npid /run/nginx.pid;\nerror_log /var/log/nginx/error.log warn;\nevents { worker_connections 1024; }\nhttp {\n    include /etc/nginx/mime.types;\n    default_type application/octet-stream;\n    sendfile on;\n    keepalive_timeout 65;\n    client_max_body_size 50M;\n    gzip on;\n    include /etc/nginx/http.d/*.conf;\n}\n' > /etc/nginx/nginx.conf && \
    printf 'server {\n    listen 80;\n    server_name _;\n    root /var/www/html/public;\n    index index.php index.html;\n    location / {\n        try_files $uri $uri/ /index.php?$query_string;\n    }\n    location ~ \\.php$ {\n        fastcgi_pass 127.0.0.1:9000;\n        fastcgi_index index.php;\n        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;\n        include fastcgi_params;\n        fastcgi_read_timeout 300;\n    }\n    location ~ /\\.(?!well-known).* { deny all; }\n}\n' > /etc/nginx/http.d/default.conf

# Configuração Supervisor inline
RUN printf '[supervisord]\nnodaemon=true\nuser=root\nlogfile=/var/log/supervisord.log\n\n[program:nginx]\ncommand=nginx -g "daemon off;"\nautostart=true\nautorestart=true\nstdout_logfile=/dev/stdout\nstdout_logfile_maxbytes=0\nstderr_logfile=/dev/stderr\nstderr_logfile_maxbytes=0\n\n[program:php-fpm]\ncommand=php-fpm -F\nautostart=true\nautorestart=true\nstdout_logfile=/dev/stdout\nstdout_logfile_maxbytes=0\nstderr_logfile=/dev/stderr\nstderr_logfile_maxbytes=0\n' > /etc/supervisord.conf

WORKDIR /var/www/html

COPY . .

RUN COMPOSER_ALLOW_SUPERUSER=1 composer install --no-interaction --prefer-dist --optimize-autoloader --no-dev

# Puxa as variáveis do Railway para o processo de build
ARG VITE_REVERB_APP_KEY
ARG VITE_REVERB_HOST
ARG VITE_REVERB_PORT
ARG VITE_REVERB_SCHEME

# Transforma os ARGs em variáveis de ambiente para o Vite ler
ENV VITE_REVERB_APP_KEY=$VITE_REVERB_APP_KEY
ENV VITE_REVERB_HOST=$VITE_REVERB_HOST
ENV VITE_REVERB_PORT=$VITE_REVERB_PORT
ENV VITE_REVERB_SCHEME=$VITE_REVERB_SCHEME

RUN npm ci && npm run build && rm -rf node_modules

RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage \
    && chmod -R 755 /var/www/html/bootstrap/cache

COPY entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/entrypoint.sh

EXPOSE 80

# Define o entrypoint
ENTRYPOINT ["entrypoint.sh"]

# Mantém o comando do supervisord
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisord.conf"]