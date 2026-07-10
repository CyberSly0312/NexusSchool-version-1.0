# Academic Module
# Laravel Implementation
## Resources

Version : 1.0

---

# Objectif

Ce document décrit les conventions d'implémentation des Resources Filament du module Academic.

Les Resources représentent les interfaces d'administration permettant de gérer les entités métier du module Academic.

Chaque Resource doit rester légère et déléguer toute logique métier aux Services.

---

# Organisation

Toutes les Resources sont regroupées dans :

```
app/
└── Filament/
    └── Resources/
        └── Academic/
```

Exemple :

```
AcademicYearResource.php

AcademicTermResource.php

ProgramResource.php

ClassroomResource.php

SubjectResource.php

EnrollmentResource.php
```

Chaque Resource possède son propre dossier :

```
AcademicYearResource/

├── Pages/
├── RelationManagers/
├── Tables/
├── Forms/
└── Widgets/
```

---

# Structure d'une Resource

Une Resource est composée de :

- Form
- Table
- Pages
- Relation Managers
- Widgets
- Actions
- Filters

Chaque partie possède une responsabilité précise.

---

# Forms

Les Forms servent uniquement à :

- afficher les champs
- appliquer les validations de base
- améliorer l'expérience utilisateur

Ils ne doivent contenir aucune logique métier.

---

# Tables

Les Tables permettent :

- l'affichage des données
- le tri
- la recherche
- les filtres
- les actions

Les requêtes complexes doivent être déplacées dans les modèles ou les Services.

---

# Pages

Chaque Resource possède généralement :

```
ListRecords

CreateRecord

EditRecord

ViewRecord
```

Des pages personnalisées peuvent être ajoutées.

Exemples :

```
PromoteStudents

CloseAcademicYear

PublishResults
```

---

# Relation Managers

Les Relation Managers permettent de gérer les relations.

Exemple :

AcademicYear

↓

AcademicTerms

↓

Enrollments

↓

Subjects

Chaque Relation Manager est responsable d'une seule relation.

---

# Widgets

Les Widgets affichent :

- statistiques
- indicateurs
- graphiques
- tableaux de bord

Ils ne modifient jamais directement les données métier.

---

# Actions

Les Actions représentent les opérations disponibles.

Exemples :

```
Activate

Close

Archive

Promote

Transfer

Publish
```

Les Actions appellent toujours un Service.

---

# Bulk Actions

Exemples :

```
Archive Selected

Delete Selected

Promote Selected

Export Selected
```

Les Bulk Actions utilisent des transactions.

---

# Filters

Les filtres permettent notamment :

- année académique
- programme
- classe
- statut
- période

Ils doivent être réutilisables.

---

# Navigation

Chaque Resource définit :

- Navigation Group
- Navigation Label
- Navigation Icon
- Navigation Sort

Exemple :

```
Navigation Group

Academic Management
```

---

# Permissions

Chaque Resource respecte :

- Policies Laravel
- Spatie Permission
- Filament Shield

Les permissions sont vérifiées avant toute action.

---

# Performance

Les Resources doivent :

- éviter les requêtes N+1
- utiliser eager loading
- paginer les résultats
- limiter les colonnes affichées

---

# Tests

Chaque Resource doit être testée pour :

- affichage
- création
- modification
- suppression
- filtres
- actions
- permissions

---

# Bonnes pratiques

- Une Resource par entité.
- Une responsabilité par composant.
- Aucune logique métier.
- Utiliser les Services.
- Respecter les Policies.
- Optimiser les requêtes.

---

# Résumé

Les Resources Filament constituent la couche d'administration du module Academic.

Elles fournissent une interface moderne, performante et sécurisée tout en respectant les principes de séparation des responsabilités définis par l'architecture de Nexus School.