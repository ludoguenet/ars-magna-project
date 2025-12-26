# Application de Facturation - Architecture Modular Monolith

Application Laravel de facturation complète utilisant une architecture **Modular Monolith** inspirée d'Artisan Airlines (Laracon India 2025).

## 🏗️ Architecture

### Principe du Modular Monolith

Un monolithe modulaire est un système où toutes les fonctionnalités résident dans une seule codebase, mais avec des **frontières strictement appliquées** entre différents domaines métier.

**Avantages** :
- ✅ Pas de complexité réseau (contrairement aux microservices)
- ✅ Transactions ACID maintenues
- ✅ Déploiement simplifié
- ✅ Performance optimale
- ✅ Migration progressive possible vers microservices si besoin

### Structure des Modules

Chaque module suit la structure **Laravel standard** organisée par domaine métier :

```
app-modules/
├── ModuleName/
│   ├── src/                  # Équivalent à app/ - Tout le code
│   │   ├── Http/             # Controllers, Requests
│   │   ├── Models/           # Modèles Eloquent
│   │   ├── Repositories/     # Abstraction d'accès aux données
│   │   ├── Services/         # Services d'orchestration
│   │   ├── Actions/          # Actions à responsabilité unique
│   │   ├── DataTransferObjects/  # DTOs
│   │   ├── Events/           # Événements
│   │   ├── Enums/            # PHP Enums
│   │   ├── Exceptions/       # Exceptions personnalisées
│   │   ├── Contracts/        # APIs publiques (interfaces)
│   │   ├── Jobs/             # Tâches en arrière-plan
│   │   ├── Listeners/        # Écouteurs d'événements
│   │   └── Providers/        # Service Provider
│   ├── routes/               # Routes du module
│   │   └── web.php
│   ├── database/             # Migrations, Factories, Seeders
│   └── tests/                # Tests unitaires et fonctionnels
```

**Vues et Composants:**
- Vues: `resources/views/modules/{module}/` (chargées avec namespace)
- Composants: `resources/views/components/{module}/` (composants anonymes)

## 📦 Modules Disponibles

### Modules Métier

- **User** - Gestion utilisateurs et équipe
- **Auth** - Authentification et sessions
- **Dashboard** - Tableau de bord et statistiques
- **Client** - Gestion des clients
- **Product** - Catalogue produits/services
- **Invoice** - Cœur de la facturation (module le plus complexe)
- **Quote** - Devis (logique similaire aux factures)
- **Payment** - Gestion des paiements
- **Document** - Génération documents (PDF, Excel)
- **Reporting** - Rapports et analyses
- **Settings** - Configuration application

### Module Partagé

- **Shared** - Code partagé entre modules (ValueObjects, Components Blade)

## 🛠️ Commandes Artisan Personnalisées

### Créer un nouveau module

```bash
php artisan make:module ModuleName
```

Crée un module complet avec toute la structure de dossiers nécessaire.

### Créer une Action dans un module

```bash
php artisan make:module-action Invoice CreateInvoiceAction
```

### Créer un Service dans un module

```bash
php artisan make:module-service Invoice InvoiceService
```

### Créer un Repository dans un module

```bash
php artisan make:module-repository Invoice InvoiceRepository
```

## 📝 Bonnes Pratiques

### 1. Actions (Single Responsibility)

Chaque Action doit :
- **Faire une seule chose** (principe SOLID)
- **Être facilement testable** unitairement
- **Utiliser l'injection de dépendances**
- **Pouvoir s'exécuter dans la queue** si nécessaire

**Exemple** :
```php
class CreateInvoiceAction
{
    public function __construct(
        private InvoiceRepository $repository,
        private GenerateInvoiceNumberAction $generateNumber
    ) {}

    public function handle(InvoiceData $data): Invoice
    {
        // Logique métier ici
    }
}
```

### 2. Services pour Orchestration

Les Services orchestrent plusieurs Actions pour implémenter des use cases complexes :

```php
class InvoiceService
{
    public function createCompleteInvoice(
        InvoiceData $invoiceData, 
        array $items
    ): Invoice {
        return DB::transaction(function () use ($invoiceData, $items) {
            $invoice = $this->createInvoice->handle($invoiceData);
            // ... orchestration
            return $invoice->fresh();
        });
    }
}
```

### 3. Controllers Fins

Les Controllers doivent être fins (< 15 lignes) et juste déléguer aux Services :

```php
public function store(StoreInvoiceRequest $request)
{
    $invoice = $this->invoiceService->createCompleteInvoice(
        InvoiceData::fromRequest($request),
        $request->input('items')
    );
    
    return redirect()
        ->route('invoice::show', $invoice)
        ->with('success', 'Facture créée avec succès');
}
```

### 4. Communication Inter-Modules

Les modules communiquent via **Events** pour éviter les dépendances directes :

```php
// Module Invoice
event(new InvoiceCreated($invoice));

// Module Payment (Listener)
class SendPaymentNotification
{
    public function handle(InvoiceCreated $event)
    {
        // Réagir à la création de facture
    }
}
```

### 5. Blade Components avec Namespace

Utiliser les composants avec namespace pour éviter les conflits :

```blade
<x-invoice::invoice-status :status="$invoice->status" />
<x-shared::button variant="primary">Créer</x-shared::button>
```

## 🧪 Tests

Les tests sont organisés par module :

```
app-modules/Invoice/tests/
├── Unit/
│   ├── CalculateInvoiceTotalsActionTest.php
│   └── InvoiceStateMachineTest.php
└── Feature/
    ├── CreateInvoiceTest.php
    └── FinalizeInvoiceTest.php
```

## 🎨 Frontend

- **Blade** pour les templates
- **Alpine.js** pour l'interactivité légère
- **Tailwind CSS** pour le styling
- **Chart.js** pour les graphiques (dashboard)

## 📚 Documentation Supplémentaire

- [Guide de création d'un module](docs/creating-a-module.md)
- [Architecture Decision Records](docs/adr/)
- [Conventions de nommage](docs/naming-conventions.md)

## 🚀 Installation

### Prérequis

- PHP 8.5.1 ou supérieur
- Composer
- Node.js et npm

### Étapes d'installation

1. **Cloner le projet**
   ```bash
   git clone <repository-url>
   cd big-project
   ```

2. **Installer les dépendances**
   ```bash
   composer install
   npm install
   ```

3. **Configurer l'environnement**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Configurer la base de données**
   
   Pour SQLite (par défaut) :
   ```bash
   touch database/database.sqlite
   chmod 664 database/database.sqlite
   ```
   
   Ou configurez votre base de données dans `.env` :
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=your_database
   DB_USERNAME=your_username
   DB_PASSWORD=your_password
   ```

5. **Exécuter les migrations**
   ```bash
   php artisan migrate
   ```

6. **Compiler les assets**
   
   Pour le développement (avec hot-reload) :
   ```bash
   npm run dev
   ```
   
   Pour la production :
   ```bash
   npm run build
   ```

7. **Démarrer le serveur**
   
   Mode simple :
   ```bash
   php artisan serve
   ```
   
   Mode développement complet (serveur + queue + logs + Vite) :
   ```bash
   composer run dev
   ```

8. **Accéder à l'application**
   
   Ouvrez votre navigateur et allez sur : **http://localhost:8000**

## 🎯 Utilisation

### Workflow de base

1. **Créer des Clients** → Menu "Clients" → "New Client"
2. **Créer des Produits** → Menu "Products" → "New Product"
3. **Créer des Factures** → Menu "Invoices" → "New Invoice"

### URLs principales

- **Dashboard** : `/dashboard`
- **Clients** : `/clients`
- **Products** : `/products`
- **Invoices** : `/invoices`
- **Notifications** : `/notifications`

## 🔧 Commandes utiles

### Voir toutes les routes
```bash
php artisan route:list
```

### Formater le code
```bash
vendor/bin/pint
```

### Lancer les tests
```bash
php artisan test
```

### Réinitialiser la base de données (⚠️ supprime toutes les données)
```bash
php artisan migrate:fresh
```

### Nettoyer les caches
```bash
php artisan optimize:clear
composer dump-autoload
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

### Base de données SQLite verrouillée
```bash
chmod 664 database/database.sqlite
```

### Les routes ne fonctionnent pas
```bash
php artisan optimize:clear
php artisan route:clear
composer dump-autoload
```

## 📄 Licence

MIT
