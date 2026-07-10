# Academic Module
# Laravel Implementation
## Routes

Version : 1.0

---

# Objectif

Ce document décrit l'organisation des routes du module Academic.

Les routes permettent d'exposer les fonctionnalités du module via :

- Interfaces Web
- API REST
- Intégrations externes
- Console Commands (si nécessaire)

Toutes les routes doivent être sécurisées, organisées et faciles à maintenir.

---

# Organisation

Les routes sont réparties selon leur contexte.

```
routes/

├── web.php
├── api.php
├── console.php
└── channels.php
```

Pour un projet modulaire, chaque domaine peut également disposer de ses propres fichiers :

```
routes/

└── Academic/

    web.php

    api.php
```

---

# Routes Web

Les routes Web sont utilisées pour :

- pages publiques
- portail enseignant
- portail étudiant
- portail parent

Les interfaces Filament utilisent leurs propres routes.

---

# Routes API

Les routes API permettent :

- intégration mobile
- applications tierces
- synchronisation
- services externes

Toutes les API utilisent :

```
/api/v1/
```

Exemple :

```
GET

/api/v1/academic-years

POST

/api/v1/enrollments
```

---

# Versionnement

Toutes les API sont versionnées.

Exemple :

```
/api/v1/

/api/v2/
```

Les anciennes versions restent compatibles pendant la période de transition.

---

# Middleware

Les routes utilisent les middlewares adaptés.

Exemple :

```
auth

verified

tenant

permission

throttle
```

Les middlewares sont appliqués au niveau des groupes lorsque cela est possible.

---

# Regroupement

Les routes sont regroupées par domaine.

Exemple :

```php
Route::prefix('academic')
    ->group(function () {

    });
```

---

# Convention de nommage

Toutes les routes possèdent un nom.

Exemple :

```
academic.index

academic.show

academic.create

academic.update

academic.destroy
```

Les noms sont utilisés dans les redirections et les Policies.

---

# Contrôleurs

Chaque groupe de routes pointe vers un Controller dédié.

Exemple :

```
AcademicYearController

EnrollmentController

SubjectController
```

Les Controllers délèguent toute logique métier aux Services.

---

# Sécurité

Toutes les routes protégées utilisent :

- authentification ;
- Policies ;
- Permissions ;
- School Scope.

Aucune donnée d'un autre établissement ne doit être accessible.

---

# Limitation de débit

Les API publiques utilisent :

```
throttle
```

pour limiter les abus.

---

# Réponses API

Les réponses respectent un format cohérent.

Succès :

```json
{
    "success": true,
    "data": {}
}
```

Erreur :

```json
{
    "success": false,
    "message": "...",
    "errors": {}
}
```

---

# Documentation

Les API doivent être documentées avec :

- OpenAPI
- Swagger
- Laravel Scribe

Toute nouvelle route doit être documentée.

---

# Tests

Les routes doivent être testées pour :

- accès autorisé ;
- accès refusé ;
- permissions ;
- middleware ;
- réponses HTTP ;
- validation.

---

# Bonnes pratiques

- Grouper les routes.
- Utiliser les noms de routes.
- Versionner les API.
- Déléguer aux Services.
- Utiliser les Policies.
- Documenter les endpoints.

---

# Résumé

Les routes du module Academic exposent les fonctionnalités du système de manière cohérente, sécurisée et évolutive.

Elles assurent une séparation claire entre les interfaces Web, les API et les autres points d'entrée de Nexus School tout en respectant les conventions Laravel.