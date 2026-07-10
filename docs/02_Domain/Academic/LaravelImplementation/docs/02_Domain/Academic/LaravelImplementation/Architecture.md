# Academic Module
# Laravel Implementation
## Architecture

Version : 1.0

---

# Objectif

Ce document décrit l'architecture technique utilisée pour implémenter le module Academic dans Nexus School.

L'objectif est de garantir :

- une architecture modulaire ;
- un faible couplage ;
- une forte cohésion ;
- une maintenance facilitée ;
- une évolutivité à long terme.

Le module Academic est conçu selon les principes de la Clean Architecture et du Domain-Driven Design (DDD), adaptés à l'écosystème Laravel.

---

# Architecture générale

Le module est organisé en plusieurs couches.

```
Presentation Layer
        │
        ▼
Application Layer
        │
        ▼
Domain Layer
        │
        ▼
Infrastructure Layer
```

Chaque couche possède une responsabilité unique.

---

# Présentation (Presentation Layer)

Responsable des interactions avec les utilisateurs.

Composants :

- Filament Resources
- Controllers
- API Controllers
- Form Requests
- Policies
- Middleware

Responsabilités :

- recevoir les requêtes ;
- autoriser les actions ;
- valider les données ;
- appeler les Services.

Aucune logique métier ne doit être présente dans cette couche.

---

# Application Layer

Responsable de l'orchestration.

Composants :

- Services
- DTOs
- Commands
- Actions (optionnel)

Responsabilités :

- coordonner les opérations ;
- appeler les Repositories ;
- déclencher les Events ;
- gérer les transactions.

---

# Domain Layer

Le cœur du système.

Composants :

- Models
- Enums
- Domain Events
- Policies métier
- State Machines
- Validation Rules

Responsabilités :

- représenter le métier ;
- appliquer les règles métier ;
- protéger l'intégrité des données.

Cette couche ne dépend d'aucune autre.

---

# Infrastructure Layer

Responsable de la persistance.

Composants :

- Eloquent Models
- Repositories
- Database
- Cache
- Filesystem
- Queue
- Notifications

Responsabilités :

- communiquer avec la base de données ;
- stocker les fichiers ;
- envoyer les notifications.

---

# Flux d'une requête

```
HTTP Request
      │
      ▼
Middleware
      │
      ▼
Policy
      │
      ▼
FormRequest
      │
      ▼
DTO
      │
      ▼
Service
      │
      ▼
Repository
      │
      ▼
Database
      │
      ▼
Event
      │
      ▼
Response
```

---

# Dépendances autorisées

Presentation

↓

Application

↓

Domain

↓

Infrastructure

Les dépendances inverses sont interdites.

---

# Principes de conception

Le module Academic applique les principes suivants :

- SOLID
- DRY
- KISS
- Clean Architecture
- Domain-Driven Design
- Repository Pattern
- Service Layer Pattern
- Dependency Injection
- Event-Driven Architecture

---

# Gestion des transactions

Les opérations critiques doivent être exécutées dans des transactions.

Exemples :

- création d'une inscription ;
- promotion ;
- transfert ;
- activation d'une année académique.

---

# Gestion des événements

Les événements métier sont déclenchés après validation des opérations.

Exemples :

- EnrollmentCreated
- StudentTransferred
- StudentPromoted
- SubjectCreated

---

# Gestion des erreurs

Les exceptions métier doivent être spécifiques.

Exemples :

- AcademicYearAlreadyActiveException
- EnrollmentAlreadyExistsException
- InvalidPromotionException
- SubjectAlreadyExistsException

Les exceptions techniques ne doivent jamais être exposées directement aux utilisateurs.

---

# Tests

Chaque couche possède ses propres tests.

Presentation

- Feature Tests

Application

- Service Tests

Domain

- Unit Tests

Infrastructure

- Integration Tests

---

# Bonnes pratiques

- Un Service par responsabilité métier.
- Un DTO par cas d'utilisation.
- Une Policy par ressource.
- Un Event par action métier.
- Aucun accès direct à la base depuis les Controllers.
- Aucune logique métier dans les Filament Resources.

---

# Résumé

Le module Academic repose sur une architecture modulaire inspirée de la Clean Architecture et du Domain-Driven Design.

Cette organisation garantit une séparation claire des responsabilités, une excellente testabilité et une forte évolutivité, tout en restant parfaitement compatible avec Laravel 12, PHP 8.4 et Filament 4.