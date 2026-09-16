# Deployment Documentation
## Vaccination Management System (VMS)

---

## 1. Environment Requirements

| Component | Version |
|-----------|---------|
| PHP | 8.2+ (8.3+ recommended) |
| MySQL | 8.0+ |
| Composer | 2.x |
| Node.js | 18+ |
| npm | 9+ |
| Web Server | Apache with mod_rewrite |

### 1.1 PHP Extensions
- pdo_mysql, mbstring, openssl, curl, gd, xml, bcmath, tokenizer, fileinfo

---

## 2. Deployment Steps (Hostinger)

### 2.1 Upload Project
```bash
# Via Git (recommended)
cd /home/u123456789/domains/vms.permetheon.com
git clone <repository-url> public_html

# Or via FTP to public_html/
```

### 2.2 Install Dependencies
```bash
cd public_html
composer install --optimize-autoloader --no-dev
npm install
```

### 2.3 Configure Environment
```bash
cp .env.example .env
php artisan key:generate
```

Edit `.env`:
```env
APP_NAME=VMS
APP_ENV=production
APP_KEY=base64:...
APP_DEBUG=false
APP_URL=https://vms.permetheon.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=vms_database
DB_USERNAME=vms_user
DB_PASSWORD=secure_password

SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_ENCRYPT=false
SESSION_PATH=/
SESSION_DOMAIN=null
SESSION_SECURE_COOKIE=true
SESSION_HTTP_ONLY=true

BCRYPT_ROUNDS=12
```

### 2.4 Database Setup
```bash
# Create MySQL database via Hostinger control panel
# Note the database name, username, and password
# Update .env with credentials

php artisan migrate --force
php artisan db:seed
```

### 2.5 Storage & Assets
```bash
php artisan storage:link
npm run build
```

### 2.6 Caching & Optimization
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
```

### 2.7 Document Root
- Set document root to `public_html/public/`
- In Hostinger: hPanel → Website → General → Document Root → `/public_html/public`

### 2.8 .htaccess
Ensure `public/.htaccess` exists with:
```apache
RewriteEngine On
RewriteRule ^(.*)$ public/$1 [L]
```
Or point document root directly to `public/`.

---

## 3. Production Configuration

| Setting | Development | Production |
|---------|-------------|------------|
| APP_ENV | local | production |
| APP_DEBUG | true | **false** |
| APP_URL | http://localhost:8000 | https://vms.permetheon.com |
| DB_CONNECTION | mysql | mysql |
| SESSION_DRIVER | database | database |
| SESSION_SECURE_COOKIE | false | **true** |
| CACHE_STORE | database | database |
| LOG_CHANNEL | stack | stack |
| LOG_LEVEL | debug | error |

---

## 4. SSL/HTTPS Configuration

1. Enable SSL in Hostinger hPanel
2. Force HTTPS in `.env`: `APP_URL=https://vms.permetheon.com`
3. Add to `public/.htaccess`:
```apache
RewriteEngine On
RewriteCond %{HTTPS} off
RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
```

---

## 5. Post-Deployment Verification

| # | Check | Command/Method | Expected |
|---|-------|---------------|----------|
| 1 | Site loads | Visit https://vms.permetheon.com | Login page renders |
| 2 | Login works | Login as admin | Dashboard loads |
| 3 | Admin portal | Navigate all pages | All pages render |
| 4 | Parent portal | Login as parent | Dashboard loads |
| 5 | Hospital portal | Login as hospital | Dashboard loads |
| 6 | Database connected | Check KPIs on dashboard | Real data displays |
| 7 | Assets load | Check CSS/JS loading | Styles correct |
| 8 | No debug info | Force error | Custom error page |
| 9 | HTTPS working | Check URL bar | Lock icon present |
| 10 | Logout works | Click sign out | Redirects to login |

---

## 6. Backup Procedure

### 6.1 Database Backup
```bash
# Via SSH
mysqldump -u vms_user -p vms_database > backup_$(date +%Y%m%d).sql

# Via Hostinger hPanel
# Databases → phpMyAdmin → Export
```

### 6.2 File Backup
```bash
# Backup key directories
tar -czf vms_backup_$(date +%Y%m%d).tar.gz \
  app/ config/ database/ resources/ routes/ public/build/
```

### 6.3 Recommended Backup Schedule
- Database: Daily automated
- Files: Weekly or before major changes
- Retain: 30 days minimum

---

## 7. Maintenance Mode

```bash
# Enable maintenance
php artisan down

# Disable maintenance
php artisan up

# With custom message
php artisan down --message="Scheduled maintenance. Back in 30 minutes."
```

---

## 8. Troubleshooting

| Issue | Solution |
|-------|----------|
| 500 Internal Server Error | Check storage/logs/laravel.log, verify .env |
| Database connection refused | Verify DB credentials in .env, check MySQL service |
| Assets not loading | Run `npm run build`, check public/build/ exists |
| Session errors | Run `php artisan migrate` (sessions table) |
| Cache errors | Run `php artisan config:clear && php artisan cache:clear` |
| Permission errors | Set storage/ and bootstrap/cache/ to 775 |
| Storage link broken | Run `php artisan storage:link` |

---

## 9. Rollback Procedure

```bash
# If issues occur after deployment:
1. Enable maintenance mode: php artisan down
2. Restore database from backup
3. Restore files from backup
4. Clear caches: php artisan optimize:clear
5. Disable maintenance: php artisan up
```

---

*Document: Deployment Documentation v1.0 — Vaccination Management System*
