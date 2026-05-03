#!/bin/sh

# Roda as migrations
php artisan migrate --force

# Roda os seeds
php artisan db:seed --force

# O 'exec "$@"' faz com que o container execute o CMD do Dockerfile
# que no seu caso é o: /usr/bin/supervisord -c /etc/supervisord.conf
exec "$@"