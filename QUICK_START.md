# 🚀 Quick Start Guide

## Steps to use the application

### 1. Verify everything is installed

```bash
# Verify that dependencies are installed
composer install
npm install
```

### 2. Prepare the database

```bash
# Make sure the SQLite database exists
touch database/database.sqlite
chmod 664 database/database.sqlite

# Run migrations
php artisan migrate
```

### 3. Clear caches

```bash
php artisan optimize:clear
composer dump-autoload
```

### 4. Compile assets

**Option A - Production (recommended for testing):**
```bash
npm run build
```

**Option B - Development (with hot-reload):**
```bash
npm run dev
```

### 5. Start the server

**Option A - Simple server:**
```bash
php artisan serve
```

**Option B - Full development mode (server + queue + logs + Vite):**
```bash
composer run dev
```

### 6. Access the application

Open your browser and go to: **http://localhost:8000**

You will be automatically redirected to the **Dashboard**.

## 🎯 Usage

### Basic workflow

1. **Create Clients** → Menu "Clients" → "New Client"
2. **Create Products** → Menu "Products" → "New Product"  
3. **Create Invoices** → Menu "Invoices" → "New Invoice"

### Main URLs

- **Dashboard** : http://localhost:8000/dashboard
- **Clients** : http://localhost:8000/clients
- **Products** : http://localhost:8000/products
- **Invoices** : http://localhost:8000/invoices

## ⚠️ If routes don't work

If you get a 404 error, try:

```bash
# Clear all caches
php artisan optimize:clear
php artisan route:clear
php artisan config:clear
php artisan view:clear

# Regenerate autoloader
composer dump-autoload

# Restart the server
php artisan serve
```

## 🐛 Troubleshooting

### "Class not found" error

```bash
composer dump-autoload
php artisan optimize:clear
```

### Assets don't load

```bash
npm run build
# ou
npm run dev
```

### Database is locked

```bash
chmod 664 database/database.sqlite
```

## 📝 Important note

Routes may not appear in `php artisan route:list` due to a case sensitivity issue in autoloading, but **the application should still work**. Test by accessing the URLs directly in your browser.

If you encounter problems, check the logs:
```bash
tail -f storage/logs/laravel.log
```
