# Academic Module
# Laravel Implementation
## Migrations

Version : 1.0

---

# Objectif

Ce document décrit les conventions utilisées pour les migrations du module Academic.

Les migrations sont responsables de la création, de la modification et de l'évolution du schéma de la base de données.

Toutes les migrations doivent être idempotentes, reproductibles et compatibles avec Laravel.

---

# Organisation

Les migrations sont stockées dans :

```
database/
└── migrations/
```

Les migrations suivent la convention Laravel :

```
YYYY_MM_DD_HHMMSS_create_academic_years_table.php

YYYY_MM_DD_HHMMSS_create_academic_terms_table.php

YYYY_MM_DD_HHMMSS_create_programs_table.php

YYYY_MM_DD_HHMMSS_create_classrooms_table.php

YYYY_MM_DD_HHMMSS_create_subjects_table.php

YYYY_MM_DD_HHMMSS_create_enrollments_table.php
```

---

# Tables principales

Le module Academic crée les tables suivantes :

- academic_years
- academic_terms
- programs
- classrooms
- subjects
- enrollments

---

# Conventions

Toutes les tables utilisent :

- UUID comme clé primaire
- timestamps()
- softDeletes()
- contraintes de clés étrangères
- index sur les colonnes fréquemment recherchées

---

# Exemple

```php
$table->uuid('id')->primary();

$table->timestamps();

$table->softDeletes();
```

---

# Clés étrangères

Toutes les relations utilisent des UUID.

Exemple :

```php
$table->foreignUuid('school_id')
      ->constrained()
      ->cascadeOnUpdate()
      ->restrictOnDelete();
```

---

# Nommage

Tables

```
academic_years

academic_terms

subjects
```

Colonnes

```
school_id

academic_year_id

subject_id

created_at

updated_at
```

---

# Types de colonnes

| Type | Utilisation |
|-------|-------------|
| uuid | Clés primaires et étrangères |
| string | Textes courts |
| text | Descriptions |
| integer | Valeurs numériques |
| boolean | États simples |
| date | Dates |
| datetime | Horodatage métier |
| json | Données structurées |
| decimal | Valeurs financières |

---

# Index

Créer des index sur :

- school_id
- academic_year_id
- classroom_id
- subject_id
- status
- code

Exemple

```php
$table->index('status');

$table->index('school_id');
```

---

# Contraintes d'unicité

Exemples

Une année académique doit avoir un code unique par école.

```php
$table->unique([
    'school_id',
    'code'
]);
```

Une matière doit avoir un code unique par école.

```php
$table->unique([
    'school_id',
    'code'
]);
```

---

# Soft Deletes

Toutes les entités métier utilisent :

```php
$table->softDeletes();
```

Cela permet :

- la restauration des données ;
- l'audit ;
- la conservation de l'historique.

---

# Transactions

Les migrations doivent être exécutables dans une transaction lorsque le moteur de base de données le permet.

---

# Rollback

Chaque migration doit pouvoir être annulée proprement.

```php
Schema::dropIfExists('academic_years');
```

---

# Multi-tenant

Toutes les tables métier doivent contenir :

```php
$table->foreignUuid('school_id');
```

Cette colonne permet l'isolation des données entre les établissements.

---

# Performance

Éviter :

- les colonnes inutiles ;
- les index redondants ;
- les contraintes dupliquées.

Privilégier :

- des index ciblés ;
- des clés étrangères explicites ;
- des requêtes optimisées.

---

# Tests

Les migrations doivent être testées pour :

- création des tables ;
- rollback ;
- contraintes ;
- index ;
- clés étrangères ;
- unicité.

---

# Bonnes pratiques

- Une migration par responsabilité.
- Ne jamais modifier une migration déjà exécutée en production.
- Préférer une nouvelle migration pour toute évolution du schéma.
- Documenter les changements majeurs.

---

# Résumé

Les migrations du module Academic définissent un schéma de base de données robuste, cohérent et évolutif.

Elles appliquent des conventions communes sur les UUID, les clés étrangères, les index, les contraintes d'unicité et la gestion multi-tenant afin de garantir la fiabilité et les performances de Nexus School.