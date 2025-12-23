# ADR 001: Architecture Modular Monolith

## Statut
Accepté

## Contexte
L'application de facturation nécessite une architecture qui :
- Reste maintenable avec 50+ modèles attendus
- Permet un développement parallèle par plusieurs équipes
- Facilite les tests et la maintenance
- Reste proche des conventions Laravel
- Permet une migration progressive vers microservices si nécessaire

## Décision
Adopter une architecture **Modular Monolith** inspirée de **Ryuta Hamasaki** (Laracon India 2025 - Artisan Airlines).

### Principes (selon Ryuta)
1. **Frontières strictes entre modules** : Chaque module est isolé et auto-contenu
2. **Code end-to-end par module** : Backend + Frontend dans le même module (mini-application Laravel)
3. **Respect des conventions Laravel** : Structure similaire à `app/`, juste organisée différemment
4. **Scalabilité horizontale** : Ajout de nouveaux modules sans toucher aux existants
5. **Communication via Contracts** : Interfaces PHP pour définir les APIs publiques entre modules
6. **DTOs pour transfert de données** : Jamais de modèles Eloquent directement entre modules
7. **Events pour réactions** : Modules réagissent aux événements, pas aux appels directs
8. **Tests d'architecture** : Pest Architecture Testing pour enforcer les frontières

### Structure (Simplifiée - Approche Ryuta)
```
app-modules/
├── ModuleName/
│   ├── src/              # Équivalent à app/ - Tout le code
│   │   ├── Http/         # Controllers, Requests
│   │   ├── Models/       # Modèles Eloquent
│   │   ├── Repositories/ # Abstraction d'accès aux données
│   │   ├── Services/      # Services d'orchestration
│   │   ├── Actions/       # Actions à responsabilité unique
│   │   ├── DataTransferObjects/  # Data Transfer Objects
│   │   ├── Events/        # Événements domaine
│   │   ├── Enums/         # PHP Enums
│   │   ├── Exceptions/    # Exceptions personnalisées
│   │   ├── Contracts/     # APIs publiques (interfaces)
│   │   ├── Jobs/          # Tâches en arrière-plan
│   │   └── Listeners/     # Écouteurs d'événements
│   ├── database/          # Migrations, Factories, Seeders
│   ├── routes/            # Routes du module
│   │   └── web.php
│   ├── tests/             # Tests unitaires et fonctionnels
│   └── src/Providers/     # Service Provider
```

**Vues et Composants:**
- Vues: `resources/views/modules/{module}/` (chargées avec namespace)
- Composants: `resources/views/components/{module}/` (composants anonymes)

## Conséquences

### Avantages
- ✅ Pas de complexité réseau (contrairement aux microservices)
- ✅ Transactions ACID maintenues
- ✅ Déploiement simplifié
- ✅ Performance optimale
- ✅ Migration progressive possible vers microservices
- ✅ Structure claire et prévisible
- ✅ Onboarding facile pour nouveaux développeurs

### Inconvénients
- ⚠️ Nécessite de la discipline pour maintenir les frontières
- ⚠️ Plus de fichiers/dossiers qu'une structure Laravel classique
- ⚠️ Courbe d'apprentissage initiale

### Alternatives Considérées
1. **Structure Laravel classique** : Rejetée car devient difficile à maintenir avec 50+ modèles
2. **Microservices** : Rejetée car trop complexe pour le besoin actuel
3. **Laravel Modules (nwilart)** : Rejetée car trop de "magie" et s'éloigne des conventions Laravel

**Points clés :**
- `src/` = équivalent à `app/` dans Laravel
- Structure conventionnelle Laravel, organisée par domaine métier
- Chaque module contient tout ce dont il a besoin
- Pas de couches DDD (domain/infrastructure/presentation) - on utilise les conventions Laravel

## Références
- [Laracon India 2025 - Artisan Airlines](https://laracon.in) - Talk de Ryuta Hamasaki
- [Modular Monolith Primer](https://www.kamilgrzybek.com/blog/modular-monolith-primer)
- [Pest Architecture Testing](https://pestphp.com/docs/arch-testing) - Enforcer les frontières
