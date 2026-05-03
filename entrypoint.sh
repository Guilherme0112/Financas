#!/bin/sh

# Roda as migrations
php artisan migrate --force

# Inicia o servidor (ajuste para o seu comando de início, ex: apache, nginx ou artisan)
php artisan serve --host=0.0.0.0 --port=$PORT