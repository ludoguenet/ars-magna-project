# Setup Guide - Application de Facturation

## 📋 Prérequis

- PHP 8.5.1 ou supérieur
- Composer installé
- Node.js et npm installés

## 🚀 Installation et Configuration

### 1. Installer les dépendances (si pas déjà fait)

```bash
composer install
npm install
```

### 2. Vérifier la configuration

Le fichier `.env` existe déjà. Vérifiez qu'il contient :

```env
APP_NAME="Facturation"
APP_URL=http://localhost:8000
DB_CONNECTION=sqlite
```

### 3. Vérifier la base de données SQLite

La base de données SQLite devrait déjà exister à `database/database.sqlite`. Si elle n'existe pas :

```bash
touch database/database.sqlite
chmod 664 database/database.sqlite
```

### 4. Exécuter les migrations

```bash
php artisan migrate
```

Cela créera toutes les tables nécessaires :
- `users` (Laravel par défaut)
- `clients`
- `products`
- `invoices`
- `invoice_items`
- Et les tables système (cache, jobs, sessions, etc.)

### 5. Régénérer l'autoloader (important)

```bash
composer dump-autoload
```

### 6. Nettoyer les caches

```bash
php artisan optimize:clear
```

### 7. Compiler les assets frontend

**Pour la production :**
```bash
npm run build
```

**Pour le développement avec hot-reload :**
```bash
npm run dev
```

### 8. Démarrer le serveur de développement

Dans un terminal :
```bash
php artisan serve
```

L'application sera accessible à : **http://localhost:8000**

**Note :** Si vous utilisez `npm run dev` pour le développement, utilisez plutôt :
```bash
composer run dev
```

Cela démarre à la fois le serveur PHP, la queue, les logs, et Vite en mode développement.

## 🎯 Utilisation de l'application

### Accès à l'application

1. Ouvrez votre navigateur et allez sur **http://localhost:8000**
2. Vous serez automatiquement redirigé vers le **Dashboard**

### Workflow recommandé

#### 1. Créer des Clients

1. Cliquez sur **"Clients"** dans la navigation
2. Cliquez sur **"Nouveau client"**
3. Remplissez les informations :
   - Nom (obligatoire)
   - Email, téléphone, entreprise (optionnels)
   - Adresse complète (optionnel)
   - Notes (optionnel)
4. Cliquez sur **"Créer"**

#### 2. Créer des Produits

1. Cliquez sur **"Produits"** dans la navigation
2. Cliquez sur **"Nouveau produit"**
3. Remplissez les informations :
   - Nom (obligatoire)
   - Prix (obligatoire)
   - Taux de TVA (optionnel, par défaut 0%)
   - SKU, description, unité (optionnels)
4. Cliquez sur **"Créer"**

#### 3. Créer des Factures

1. Cliquez sur **"Factures"** dans la navigation
2. Cliquez sur **"Nouvelle facture"**
3. Sélectionnez un client
4. Ajoutez des articles :
   - Description
   - Quantité
   - Prix unitaire
   - Taux de TVA (%)
5. Cliquez sur **"+ Ajouter un article"** pour ajouter plus d'articles
6. Optionnel : Ajoutez des notes et conditions
7. Cliquez sur **"Créer la facture"**

La facture sera créée avec :
- Un numéro de facture automatique
- Calcul automatique des totaux (sous-total, TVA, total)
- Statut "Draft" par défaut

#### 4. Consulter le Dashboard

Le dashboard affiche :
- Nombre total de clients, produits, factures
- Chiffre d'affaires total (factures payées)
- Factures en attente
- Factures impayées
- Liste des factures récentes

## 🔧 Commandes utiles

### Voir toutes les routes disponibles

```bash
php artisan route:list
```

### Réinitialiser la base de données (⚠️ supprime toutes les données)

```bash
php artisan migrate:fresh
```

### Créer un utilisateur (si vous ajoutez l'authentification)

```bash
php artisan tinker
```

Puis dans tinker :
```php
User::create([
    'name' => 'Votre Nom',
    'email' => 'votre@email.com',
    'password' => Hash::make('votre-mot-de-passe')
]);
```

### Formater le code

```bash
vendor/bin/pint
```

### Lancer les tests

```bash
php artisan test
```

## 📁 Structure de l'application

L'application utilise une architecture **Modular Monolith** :

```
app-modules/
├── client/          # Gestion des clients
├── product/         # Catalogue produits
├── invoice/         # Facturation (module principal)
├── dashboard/       # Tableau de bord
└── shared/          # Composants partagés
```

Chaque module contient :
- `src/` - Tout le code (Controllers, Models, Services, Actions, etc.)
- `routes/web.php` - Routes du module
- `database/` - Migrations, Factories, Seeders
- `tests/` - Tests unitaires et fonctionnels

**Vues et Composants:**
- Vues: `resources/views/modules/{module}/`
- Composants: `resources/views/components/{module}/`

## 🐛 Dépannage

### Les routes ne s'affichent pas

Vérifiez que `ModuleServiceProvider` est bien enregistré dans `bootstrap/providers.php`

### Erreur "Class not found"

Exécutez :
```bash
composer dump-autoload
```

### Les migrations ne s'exécutent pas

Vérifiez que le fichier `database/database.sqlite` existe et est accessible en écriture :
```bash
chmod 664 database/database.sqlite
```

### Les assets ne se chargent pas

Compilez les assets :
```bash
npm run build
```

Ou en mode développement :
```bash
npm run dev
```

## 📝 Prochaines étapes

Pour compléter l'application, vous pouvez :

1. **Ajouter l'authentification** - Utiliser `php artisan make:auth` ou Laravel Breeze
2. **Créer des factories et seeders** - Pour générer des données de test
3. **Ajouter le module Quote** - Pour gérer les devis
4. **Ajouter le module Payment** - Pour gérer les paiements
5. **Ajouter des tests** - Pour garantir la qualité du code

## 🎉 C'est tout !

Votre application de facturation est prête à être utilisée. Commencez par créer quelques clients et produits, puis créez votre première facture !
