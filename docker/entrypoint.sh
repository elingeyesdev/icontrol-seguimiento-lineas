#!/bin/sh
set -eu

PORT="${PORT:-10000}"

sed -i -E "s#<VirtualHost \*:[^>]+>#<VirtualHost *:${PORT}>#" /etc/apache2/sites-available/000-default.conf
sed -i "s/^Listen .*/Listen ${PORT}/" /etc/apache2/ports.conf

exec "$@"
