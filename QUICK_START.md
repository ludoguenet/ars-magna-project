# 🚀 Guide de Démarrage Rapide

## Étapes pour utiliser l'application

### 1. Vérifier que tout est installé

```bash
# Vérifier que les dépendances sont installées
composer install
npm install
```

### 2. Préparer la base de données

```bash
# S'assurer que la base SQLite existe
touch database/database.sqlite
chmod 664 database/database.sqlite

# Exécuter les migrations
php artisan migrate
```

### 3. Nettoyer les caches

```bash
php artisan optimize:clear
composer dump-autoload
```

### 4. Compiler les assets

**Option A - Production (recommandé pour tester) :**
```bash
npm run build
```

**Option B - Développement (avec hot-reload) :**
```bash
npm run dev
```

### 5. Démarrer le serveur

**Option A - Serveur simple :**
```bash
php artisan serve
```

**Option B - Mode développement complet (serveur + queue + logs + Vite) :**
```bash
composer run dev
```

### 6. Accéder à l'application

Ouvrez votre navigateur et allez sur : **http://localhost:8000**

Vous serez automatiquement redirigé vers le **Dashboard**.

## 🎯 Utilisation

### Workflow de base

1. **Créer des Clients** → Menu "Clients" → "Nouveau client"
2. **Créer des Produits** → Menu "Produits" → "Nouveau produit"  
3. **Créer des Factures** → Menu "Factures" → "Nouvelle facture"

### URLs principales

- **Dashboard** : http://localhost:8000/dashboard
- **Clients** : http://localhost:8000/clients
- **Produits** : http://localhost:8000/products
- **Factures** : http://localhost:8000/invoices

## ⚠️ Si les routes ne fonctionnent pas

Si vous obtenez une erreur 404, essayez :

```bash
# Nettoyer tous les caches
php artisan optimize:clear
php artisan route:clear
php artisan config:clear
php artisan view:clear

# Régénérer l'autoloader
composer dump-autoload

# Redémarrer le serveur
php artisan serve
```

## 🐛 Dépannage

### Erreur "Class not found"

```bash
composer dump-autoload
php artisan optimize:clear
```

### Les assets ne se chargent pas

```bash
npm run build
# ou
npm run dev
```

### La base de données est verrouillée

```bash
chmod 664 database/database.sqlite
```

## 📝 Note importante

Les routes peuvent ne pas apparaître dans `php artisan route:list` à cause d'un problème de casse dans l'autoloading, mais **l'application devrait fonctionner** quand même. Testez en accédant directement aux URLs dans votre navigateur.

Si vous rencontrez des problèmes, vérifiez les logs :
```bash
tail -f storage/logs/laravel.log
```
