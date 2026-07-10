# Academic Module
# Laravel Implementation
## Filament

Version : 1.0

---

# Objectif

Ce document décrit les conventions d'utilisation de Filament 4 dans le module Academic.

Filament constitue le Back Office officiel de Nexus School.

Toutes les interfaces administratives doivent respecter les conventions décrites dans ce document afin d'assurer une expérience utilisateur homogène et une maintenance facilitée.

---

# Architecture

Toutes les interfaces Filament sont organisées par module métier.

Exemple :

```
app/

└── Filament/

    ├── Resources/

    │      └── Academic/

    ├── Widgets/

    ├── Pages/

    ├── Clusters/

    └── Providers/
```

---

# Ressources

Chaque entité métier possède sa propre Resource.

Exemple :

```
AcademicYearResource

AcademicTermResource

ProgramResource

ClassroomResource

SubjectResource

EnrollmentResource
```

Une Resource ne contient jamais de logique métier.

---

# Pages

Les Pages représentent les écrans.

Exemples :

```
ListAcademicYears

CreateAcademicYear

EditAcademicYear

ViewAcademicYear
```

Des pages personnalisées peuvent être créées.

Exemples :

```
PromoteStudents

CloseAcademicYear

PublishResults
```

---

# Formulaires

Les formulaires utilisent :

- Sections
- Fieldsets
- Wizards
- Tabs
- Grids

Les validations complexes restent dans les Form Requests ou les Services.

---

# Tables

Les Tables utilisent :

- recherche
- tri
- filtres
- pagination
- colonnes formatées
- actions
- actions de masse

Les requêtes sont optimisées avec eager loading.

---

# Widgets

Les Widgets servent à afficher :

- statistiques
- indicateurs
- graphiques
- activités récentes

Les Widgets ne modifient jamais directement les données.

---

# Infolists

Les Infolists affichent les détails d'une ressource.

Ils sont utilisés pour :

- consultation
- audit
- historique
- lecture seule

---

# Relation Managers

Les Relation Managers permettent de gérer les relations.

Exemple :

```
AcademicYear

↓

AcademicTerms

↓

Enrollments

↓

Subjects
```

Chaque Relation Manager est responsable d'une seule relation.

---

# Actions

Les Actions représentent les opérations métier.

Exemples :

```
Activate

Archive

Close

Publish

Transfer

Promote
```

Chaque Action appelle un Service.

---

# Bulk Actions

Les Bulk Actions permettent :

- archivage
- suppression
- export
- promotion
- changement de statut

Toutes les Bulk Actions utilisent des transactions.

---

# Notifications

Les notifications Filament sont utilisées pour :

- succès
- erreur
- avertissement
- information

Les messages doivent être explicites.

---

# Navigation

Chaque Resource définit :

- Navigation Group
- Navigation Label
- Navigation Icon
- Navigation Badge
- Navigation Sort

Les groupes sont organisés par domaine fonctionnel.

---

# Permissions

Filament est intégré avec :

- Laravel Policies
- Spatie Laravel Permission
- Filament Shield

Toutes les actions sont protégées.

---

# Multi-tenant

Toutes les Resources appliquent automatiquement :

```
school_id
```

Les utilisateurs ne voient que les données de leur établissement.

---

# Performance

Toutes les pages doivent :

- utiliser eager loading ;
- limiter les colonnes ;
- paginer les résultats ;
- éviter les requêtes N+1 ;
- utiliser des filtres indexés.

---

# Accessibilité

Les interfaces doivent :

- être responsive ;
- être accessibles au clavier ;
- utiliser des contrastes suffisants ;
- fournir des messages d'erreur clairs.

---

# Tests

Les interfaces Filament doivent être testées pour :

- affichage ;
- navigation ;
- permissions ;
- formulaires ;
- tables ;
- actions ;
- widgets.

---

# Bonnes pratiques

- Une Resource par entité.
- Aucune logique métier.
- Utiliser les Services.
- Respecter les Policies.
- Réutiliser les composants.
- Garder les interfaces simples.

---

# Résumé

Filament constitue la couche de présentation du Back Office de Nexus School.

Son rôle est de fournir une interface moderne, performante, sécurisée et cohérente tout en laissant la logique métier aux Services et les règles d'autorisation aux Policies.