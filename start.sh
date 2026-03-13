#!/bin/bash
set -e

# Run database migrations
mysql -h "$MYSQL_HOST" -u "$MYSQL_USER" -p"$MYSQL_PASSWORD" "$MYSQL_DB" < create_db.sql

# Start PHP server on Railway's assigned port
php -S 0.0.0.0:$PORT
