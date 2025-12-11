# Deployment

## Pre-Deployment Checklist

Before deploying to production:

- [ ] Run all tests: `php artisan test`
- [ ] Format code: `vendor/bin/pint`
- [ ] Update dependencies: `composer update`
- [ ] Build assets: `npm run build`
- [ ] Set `APP_ENV=production`
- [ ] Set `APP_DEBUG=false`
- [ ] Generate secure `APP_KEY`
- [ ] Configure production database
- [ ] Set up queue workers
- [ ] Configure Redis for cache/sessions
- [ ] Review `.env` for sensitive data
- [ ] Set up SSL certificates
- [ ] Configure CORS if needed
- [ ] Set up monitoring and logging

## Deployment Steps

### 1. Server Requirements

- PHP 8.4+
- Composer
- MySQL/PostgreSQL
- Redis (recommended)
- Node.js & NPM
- Supervisor (for queue workers)

### 2. Initial Deployment

```bash
# Clone repository
git clone <repository-url>
cd laravel12-api

# Install dependencies
composer install --optimize-autoloader --no-dev

# Set up environment
cp .env.example .env
nano .env  # Configure production settings

# Generate application key
php artisan key:generate

# Run migrations
php artisan migrate --force

# Build assets
npm ci
npm run build

# Optimize application
php artisan optimize
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 3. Set Permissions

```bash
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

### 4. Configure Web Server

**Nginx Example:**

```nginx
server {
    listen 80;
    server_name api.example.com;
    root /var/www/laravel12-api/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.4-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

### 5. Configure Queue Workers

Create Supervisor configuration at `/etc/supervisor/conf.d/laravel-worker.conf`:

```ini
[program:laravel-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/laravel12-api/artisan queue:work --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=8
redirect_stderr=true
stdout_logfile=/var/www/laravel12-api/storage/logs/worker.log
stopwaitsecs=3600
```

Reload Supervisor:
```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start laravel-worker:*
```

### 6. Set Up Scheduled Tasks

Add to crontab:
```bash
* * * * * cd /var/www/laravel12-api && php artisan schedule:run >> /dev/null 2>&1
```

## Continuous Deployment

For automated deployments, consider:

- **Laravel Forge** - Managed Laravel hosting
- **Laravel Vapor** - Serverless deployment
- **Envoyer** - Zero-downtime deployment
- **GitHub Actions** - CI/CD pipeline
- **GitLab CI/CD** - Automated deployment

## Zero-Downtime Deployment

```bash
# Using symlinks
ln -nfs /var/www/releases/new /var/www/current

# Reload PHP-FPM
sudo systemctl reload php8.4-fpm

# Restart queue workers
php artisan queue:restart
```

## Post-Deployment

```bash
# Verify deployment
php artisan about

# Check health
curl https://api.example.com/up

# Monitor logs
tail -f storage/logs/laravel.log
```
