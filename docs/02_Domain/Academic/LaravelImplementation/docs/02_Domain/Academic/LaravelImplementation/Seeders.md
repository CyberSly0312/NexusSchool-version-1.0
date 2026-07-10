# Academic Module
# Laravel Implementation
## Seeders

Version : 1.0

---

# Objectif

Ce document décrit les conventions utilisées pour les Seeders du module Academic.

Les Seeders permettent d'initialiser la base de données avec des données de référence, des données de démonstration ou des données nécessaires au fonctionnement du système.

Ils doivent être reproductibles, cohérents et idempotents.

---

# Organisation

Les Seeders sont stockés dans :

```
database/
└── seeders/
```

Le module Academic comprend notamment :

```
AcademicYearSeeder.php

AcademicTermSeeder.php

ProgramSeeder.php

ClassroomSeeder.php

SubjectSeeder.php

EnrollmentSeeder.php
```

---

# Catégories de Seeders

Les Seeders sont répartis en trois catégories.

## 1. Reference Data

Contiennent les données indispensables.

Exemples :

- Types de programmes
- Statuts
- Matières système
- Périodes par défaut

Ces données changent rarement.

---

## 2. Demo Data

Utilisées pour :

- démonstrations
- formations
- environnement de recette

Exemples :

- écoles fictives
- élèves
- enseignants
- classes

---

## 3. Test Data

Utilisées uniquement pour :

- tests automatisés
- développement local

Ces données ne doivent jamais être installées en production.

---

# Ordre d'exécution

Les Seeders doivent respecter les dépendances.

Ordre recommandé :

```
AcademicYearSeeder

↓

AcademicTermSeeder

↓

ProgramSeeder

↓

ClassroomSeeder

↓

SubjectSeeder

↓

EnrollmentSeeder
```

---

# Appels

Le DatabaseSeeder orchestre l'exécution.

Exemple :

```php
$this->call([
    AcademicYearSeeder::class,
    AcademicTermSeeder::class,
    ProgramSeeder::class,
    ClassroomSeeder::class,
    SubjectSeeder::class,
    EnrollmentSeeder::class,
]);
```

---

# Idempotence

Les Seeders doivent pouvoir être exécutés plusieurs fois sans créer de doublons.

Utiliser :

```php
firstOrCreate()

updateOrCreate()
```

Éviter :

```php
Model::create()
```

pour les données de référence.

---

# Relations

Les Seeders doivent créer les relations automatiquement.

Exemples :

- une année possède plusieurs périodes ;
- une classe appartient à un programme ;
- une inscription appartient à une classe.

---

# Utilisation des Factories

Les Seeders de démonstration utilisent les Factories.

Exemple :

```php
Student::factory()

    ->count(100)

    ->create();
```

---

# Multi-tenant

Chaque Seeder doit être capable de créer des données pour une école spécifique.

Toutes les données doivent être associées à :

```php
school_id
```

---

# Performances

Pour de gros volumes :

Utiliser :

```php
insert()

upsert()

chunk()
```

Éviter de créer les enregistrements un par un lorsque cela est possible.

---

# Tests

Les Seeders doivent être testés pour vérifier :

- leur exécution ;
- l'absence de doublons ;
- les relations créées ;
- les données obligatoires.

---

# Bonnes pratiques

- Un Seeder par responsabilité.
- Aucune donnée sensible.
- Données réalistes.
- Respect de l'ordre d'exécution.
- Réutilisation des Factories.

---

# Résumé

Les Seeders du module Academic permettent d'initialiser la base de données de manière fiable, cohérente et reproductible.

Ils garantissent une installation rapide, des démonstrations réalistes et des environnements de développement homogènes pour Nexus School.