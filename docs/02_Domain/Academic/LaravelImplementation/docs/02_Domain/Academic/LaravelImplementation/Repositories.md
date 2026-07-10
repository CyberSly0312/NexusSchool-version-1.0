# Academic Module
# Laravel Implementation
## Repositories

Version : 1.0

---

# Objectif

Ce document décrit l'implémentation des Repositories du module Academic.

Les Repositories sont responsables de l'accès aux données.

Ils encapsulent les requêtes Eloquent et fournissent une interface claire entre les Services et la couche de persistance.

---

# Objectifs principaux

Les Repositories permettent de :

- centraliser les requêtes ;
- éviter la duplication du code ;
- faciliter les tests ;
- masquer l'implémentation Eloquent ;
- simplifier les évolutions futures.

---

# Organisation

Les Repositories sont regroupés dans :

```
app/
└── Domain/
    └── Academic/
        └── Repositories/
```

Exemple :

```
AcademicYearRepository.php

AcademicTermRepository.php

ProgramRepository.php

ClassroomRepository.php

SubjectRepository.php

EnrollmentRepository.php
```

---

# Interfaces

Chaque Repository possède une interface.

```
Contracts/

AcademicYearRepositoryInterface.php

EnrollmentRepositoryInterface.php

SubjectRepositoryInterface.php
```

Les Services dépendent des interfaces et non des implémentations.

---

# Responsabilités

Les Repositories sont responsables de :

- récupérer les données ;
- sauvegarder les modèles ;
- supprimer les données ;
- construire les requêtes complexes ;
- gérer la pagination.

Ils ne doivent contenir aucune logique métier.

---

# Exemple

```php
public function findById(string $id): ?AcademicYear;

public function findCurrent(): ?AcademicYear;

public function findActive(): Collection;

public function create(array $data): AcademicYear;

public function update(AcademicYear $year, array $data): AcademicYear;

public function delete(AcademicYear $year): bool;
```

---

# Utilisation

Les Services utilisent uniquement les Repositories.

Exemple :

```
Controller

↓

Service

↓

Repository

↓

Eloquent

↓

Database
```

Les Controllers ne doivent jamais interroger directement Eloquent.

---

# Critères de recherche

Les Repositories peuvent proposer :

- recherche par code ;
- recherche par statut ;
- recherche par établissement ;
- recherche par année académique ;
- pagination.

---

# Pagination

Les listes volumineuses utilisent :

```php
paginate()

simplePaginate()

cursorPaginate()
```

selon le besoin.

---

# Multi-tenant

Tous les Repositories appliquent automatiquement :

```
school_id
```

afin de garantir l'isolation des données.

Aucune requête ne doit pouvoir accéder aux données d'un autre établissement.

---

# Optimisation

Les Repositories utilisent :

- eager loading ;
- select ciblés ;
- scopes ;
- index de base de données.

Éviter les requêtes N+1.

---

# Transactions

Les Repositories ne gèrent pas les transactions.

Les transactions appartiennent aux Services.

---

# Gestion des erreurs

Les erreurs techniques peuvent être propagées.

Les exceptions métier restent gérées par les Services.

---

# Tests

Chaque Repository doit être testé pour :

- récupération des données ;
- création ;
- modification ;
- suppression ;
- pagination ;
- filtres ;
- School Scope.

---

# Bonnes pratiques

- Une interface par Repository.
- Une implémentation par Repository.
- Aucune logique métier.
- Réutiliser les Scopes.
- Optimiser les requêtes.
- Toujours appliquer le filtre school_id.

---

# Résumé

Les Repositories centralisent l'accès aux données du module Academic.

Ils permettent de découpler les Services de Laravel Eloquent tout en garantissant des requêtes performantes, testables et compatibles avec l'architecture multi-tenant de Nexus School.