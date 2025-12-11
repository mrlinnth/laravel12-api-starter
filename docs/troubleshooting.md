# Troubleshooting

## Common Issues

### Vite Manifest Error

**Error:** `Unable to locate file in Vite manifest`

**Solution:**
```bash
npm run build
# or for development
npm run dev
```

### Permission Denied Errors

**Error:** `Permission denied` when writing to storage/logs

**Solution:**
```bash
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

### Database Connection Failed

**Error:** `SQLSTATE[HY000] [2002] Connection refused`

**Solution:**
1. Verify database is running
2. Check `.env` database credentials
3. Test connection: `php artisan db:show`

### Queue Not Processing

**Issue:** Jobs remain in queue

**Solution:**
```bash
# Run queue worker
php artisan queue:work

# For development, use with timeout
php artisan queue:listen --tries=3

# Clear failed jobs
php artisan queue:flush
```

### Class Not Found After Creating New File

**Solution:**
```bash
composer dump-autoload
```

### Route Not Found

**Solution:**
```bash
php artisan route:clear
php artisan route:cache
php artisan route:list  # Verify route exists
```

### Config Cached in Development

**Issue:** `.env` changes not taking effect

**Solution:**
```bash
php artisan config:clear
php artisan cache:clear
```

### Tests Failing Due to Database

**Solution:**
```bash
# Use in-memory SQLite for tests
# In phpunit.xml or .env.testing
DB_CONNECTION=sqlite
DB_DATABASE=:memory:
```

### API Documentation Not Showing

**Solution:**
```bash
php artisan scramble:generate
php artisan route:clear
```

## Getting Help

- **Laravel 12 Documentation**: https://laravel.com/docs/12.x
- **Telescope Dashboard**: http://localhost:8000/telescope
- **Debug Mode**: Set `APP_DEBUG=true` in `.env`
- **Logs**: Check `storage/logs/laravel.log` or use `php artisan pail`
