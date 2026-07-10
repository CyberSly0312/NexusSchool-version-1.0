# Academic Module
# Laravel Implementation
## Permissions

Version : 1.0

---

# Objectif

Ce document décrit l'implémentation du système de permissions du module Academic.

Le contrôle d'accès repose sur :

- Laravel Authorization
- Laravel Policies
- Spatie Laravel Permission
- Filament Shield

Cette architecture garantit un contrôle d'accès centralisé, sécurisé et évolutif.

---

# Architecture

Le système suit la hiérarchie suivante :

```
User

↓

Role

↓

Permission

↓

Policy

↓

Resource / Service
```

Les utilisateurs héritent des permissions via leurs rôles.

---

# Composants

Le système utilise :

- Roles
- Permissions
- Policies
- Gates (si nécessaire)
- Filament Shield

Chaque composant possède une responsabilité spécifique.

---

# Structure

Les permissions sont créées via :

```
database/

└── seeders/

        PermissionsSeeder.php
```

Les rôles sont créés via :

```
RolesSeeder.php
```

---

# Convention de nommage

Les permissions suivent le format :

```
module.action
```

Exemples :

```
academic.view

academic.create

academic.update

academic.delete

subject.create

subject.archive

enrollment.transfer
```

Cette convention est utilisée dans tout Nexus School.

---

# Attribution

Les permissions sont attribuées aux rôles.

Exemple :

```
Super Administrator

↓

Toutes les permissions

-------------------------

Academic Director

↓

academic.*

subject.*

enrollment.*

-------------------------

Teacher

↓

subject.view

enrollment.view
```

Les utilisateurs ne reçoivent généralement pas de permissions directement.

---

# Vérification

Les vérifications utilisent :

```php
$user->can('academic.create');
```

ou

```php
Gate::allows(...);
```

ou directement les Policies.

---

# Intégration avec les Policies

Les Policies utilisent les permissions.

Exemple :

```php
public function create(User $user): bool
{
    return $user->can('academic.create');
}
```

---

# Intégration avec Filament Shield

Filament Shield génère automatiquement les permissions des Resources.

Exemple :

```
AcademicYearResource

↓

View

Create

Update

Delete
```

Les permissions personnalisées sont ajoutées manuellement.

---

# Multi-tenant

Les permissions déterminent

"ce que l'utilisateur peut faire".

Le School Scope détermine

"sur quelles données".

Les deux mécanismes sont complémentaires.

---

# Synchronisation

Les nouvelles permissions sont créées automatiquement via :

```
php artisan shield:generate
```

ou via les Seeders.

---

# Audit

Toutes les modifications concernant :

- les rôles ;
- les permissions ;
- les affectations ;

doivent être journalisées.

---

# Tests

Les tests vérifient :

- les rôles ;
- les permissions ;
- les Policies ;
- Filament Shield ;
- le School Scope ;
- les refus d'accès.

---

# Bonnes pratiques

- Ne jamais vérifier un rôle directement lorsqu'une permission existe.
- Utiliser les Policies.
- Centraliser les permissions.
- Respecter la convention module.action.
- Journaliser les changements sensibles.

---

# Résumé

Le système de permissions du module Academic repose sur Laravel Authorization, Spatie Laravel Permission et Filament Shield.

Cette architecture garantit un contrôle d'accès cohérent, sécurisé et facilement extensible pour l'ensemble de Nexus School.