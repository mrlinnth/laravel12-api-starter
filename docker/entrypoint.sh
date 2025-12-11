#!/bin/bash
set -e

# Start MySQL in the background
mysqld_safe &

# Wait for MySQL to start
echo "Waiting for MySQL to start..."
sleep 10

# Create database and user dynamically from environment variables
MYSQL_DB=${DB_DATABASE:-homestead}
MYSQL_USER=${DB_USERNAME:-homestead}
MYSQL_PASSWORD=${DB_PASSWORD:-secret}

mysql -e "CREATE DATABASE IF NOT EXISTS \`${MYSQL_DB}\`;"
mysql -e "CREATE USER IF NOT EXISTS '${MYSQL_USER}'@'%' IDENTIFIED BY '${MYSQL_PASSWORD}';"
mysql -e "GRANT ALL PRIVILEGES ON \`${MYSQL_DB}\`.* TO '${MYSQL_USER}'@'%';"
mysql -e "FLUSH PRIVILEGES;"

# Start Supervisor to run PHP-FPM and Nginx
exec /usr/bin/supervisord -n -c /etc/supervisor/conf.d/supervisord.conf
