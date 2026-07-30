# Nexus Platform
## Routing Architecture

**Version:** 1.0

**Status:** Draft

---

# 1. Purpose

Ce document définit l'architecture de routage de Nexus Platform.

Le système de routage permet de diriger chaque requête vers le bon Tenant, le bon Panel, la bonne Application et la bonne ressource tout en garantissant la sécurité et l'isolation des données.

---

# 2. Scope

Ce document couvre :

- Les routes de la plateforme
- Les routes des Tenants
- Les routes des Panels
- La résolution des domaines
- Les middlewares
- Les préfixes d'URL

Ce document ne couvre pas :

- Les règles métier
- Les permissions

---

# 3. Objectives

L'architecture de routage doit être :

- sécurisée
- modulaire
- lisible
- performante
- extensible

---

# 4. High-Level Routing

```text
Incoming Request

↓

DNS Resolution

↓

Domain Resolution

↓

Tenant Resolution

↓

Middleware Pipeline

↓

Panel Resolution

↓

Application Routing

↓

Controller

↓

Response
```

---

# 5. Routing Levels

Le routage est organisé selon quatre niveaux.

## Platform Level

Accessible uniquement depuis :

```text
platform.nexus.com
```

Responsable de :

- Gestion SaaS
- Gestion des Tenants
- Facturation
- Support
- Monitoring
- Administration globale

---

## Tenant Level

Accessible via :

```text
zinda.nexus.com
```

Responsable de :

- Initialisation du Tenant
- Authentification
- Chargement des Panels

---

## Panel Level

Chaque Panel possède son propre préfixe.

Exemples :

```text
/admin

/director

/teacher

/student

/parent

/accountant

/secretary

/hr

/library

/nurse
```

---

## Application Level

Les Applications enregistrent leurs propres routes à l'intérieur du Panel.

Exemple :

```text
/admin/students

/admin/classes

/admin/teachers

/admin/finance
```

---

# 6. Route Resolution

Chaque requête suit le processus suivant.

```text
Request

↓

Host

↓

Tenant Resolver

↓

Panel Resolver

↓

Middleware

↓

Route

↓

Controller

↓

Response
```

---

# 7. Middleware Pipeline

Toutes les routes passent par une chaîne de middlewares.

Ordre d'exécution :

1. HTTPS
2. Maintenance
3. Tenant Resolver
4. Tenant Context
5. Session
6. Authentication
7. Authorization
8. Localization
9. Panel Context

---

# 8. Route Groups

Les routes sont regroupées par :

- Domaine
- Tenant
- Panel
- Application

Chaque groupe possède ses propres middlewares.

---

# 9. URL Examples

## Platform

```text
https://platform.nexus.com
```

---

## School Admin

```text
https://zinda.nexus.com/admin
```

---

## Director

```text
https://zinda.nexus.com/director
```

---

## Teacher

```text
https://zinda.nexus.com/teacher
```

---

## Student

```text
https://zinda.nexus.com/student
```

---

## Parent

```text
https://zinda.nexus.com/parent
```

---

# 10. Route Registration

Les routes sont enregistrées automatiquement lors du démarrage.

Chaque Application enregistre :

- Pages
- Resources
- API
- Actions

Le Platform Kernel centralise l'enregistrement.

---

# 11. Route Protection

Toutes les routes sont protégées par :

- Authentification
- Permissions
- Policies
- Tenant Context
- Panel Context

Aucune route sensible ne peut être publique.

---

# 12. Route Naming

Convention :

```text
platform.dashboard

school.dashboard

students.index

students.create

students.edit

students.view

finance.payments.index

teachers.profile
```

Les noms doivent être uniques.

---

# 13. Routing Constraints

Les règles suivantes sont obligatoires.

- Une route appartient à un seul Panel.
- Une route appartient à une seule Application.
- Une route est toujours exécutée dans un Tenant.
- Toutes les routes utilisent HTTPS.
- Les middlewares sont exécutés dans le même ordre.

---

# 14. Future Evolution

Les prochaines versions pourront intégrer :

- API Gateway
- Versionnement des API
- Dynamic Routing
- Route Discovery
- Multi-Region Routing
- Edge Routing

---

## Status

Draft

## Version

1.0

## Last Updated

2026-07-27

## Related Documents

- Panel_Architecture
- Panel_Lifecycle
- Navigation_Architecture
- Multi_Tenant_Architecture
- Domain_Management
```