# Academic Module
# Laravel Implementation
## Policies

Version : 1.0

---

# Objectif

Ce document décrit l'implémentation des Policies Laravel utilisées dans le module Academic.

Les Policies centralisent les règles d'autorisation permettant de contrôler l'accès aux ressources du système.

Elles garantissent qu'un utilisateur ne peut effectuer que les actions autorisées par son rôle et son contexte.

---

# Organisation

Toutes les Policies sont regroupées dans :

```
app/
└── Domain/
    └── Academic/
        └── Policies/
```

Exemple :

```
AcademicYearPolicy.php

AcademicTermPolicy.php

ProgramPolicy.php

ClassroomPolicy.php

SubjectPolicy.php

EnrollmentPolicy.php
```

---

# Responsabilités

Une Policy est responsable de :

- autoriser ou refuser une action ;
- vérifier les permissions ;
- vérifier le contexte de sécurité ;
- appliquer les règles d'accès.

Une Policy ne doit jamais :

- modifier des données ;
- appeler directement la base de données ;
- exécuter une logique métier complexe.

---

# Enregistrement

Les Policies sont enregistrées automatiquement dans Laravel.

Exemple :

```php
protected $policies = [

    AcademicYear::class => AcademicYearPolicy::class,

];
```

---

# Méthodes standards

Chaque Policy peut implémenter :

```php
viewAny()

view()

create()

update()

delete()

restore()

forceDelete()
```

Des méthodes métier peuvent être ajoutées :

```php
activate()

close()

archive()

publish()

promote()

transfer()
```

---

# Vérification des permissions

Les Policies utilisent Spatie Laravel Permission.

Exemple :

```php
public function create(User $user): bool
{
    return $user->can('academic.create');
}
```

---

# Vérification du School Scope

Chaque Policy doit vérifier que l'utilisateur agit uniquement sur les données de son établissement.

Exemple :

```php
return $user->school_id === $academicYear->school_id;
```

Cette vérification est obligatoire pour toutes les entités métier.

---

# Super Administrator

Le Super Administrator peut contourner les vérifications classiques.

Exemple :

```php
public function before(User $user)
{
    if ($user->hasRole('Super Administrator')) {
        return true;
    }

    return null;
}
```

---

# Intégration avec Filament

Les Resources Filament utilisent automatiquement les Policies.

Exemple :

```
AcademicYearResource

↓

AcademicYearPolicy
```

Aucune vérification supplémentaire ne doit être dupliquée dans les Resources.

---

# Gestion des erreurs

En cas de refus :

- HTTP 403 pour les API ;
- message d'accès refusé dans Filament ;
- journalisation de l'événement si nécessaire.

---

# Tests

Chaque Policy doit être testée pour :

- utilisateur autorisé ;
- utilisateur non autorisé ;
- School Scope ;
- rôles ;
- permissions ;
- méthodes personnalisées.

---

# Bonnes pratiques

- Une Policy par modèle.
- Une méthode par action.
- Aucune logique métier.
- Vérifier les permissions avant toute action.
- Toujours contrôler le School Scope.
- Utiliser la méthode before() uniquement pour les administrateurs globaux.

---

# Résumé

Les Policies assurent le contrôle d'accès du module Academic.

Elles s'intègrent à Laravel, Filament et Spatie Laravel Permission afin de fournir une sécurité centralisée, cohérente et adaptée à un environnement SaaS multi-écoles.