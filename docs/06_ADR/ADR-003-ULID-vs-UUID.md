# ADR-003
# ULID as the Primary Identifier Strategy

**Status:** Accepted

**Date:** 2026-07-30

**Decision Makers:**

- Solution Architect
- Backend Architect
- Database Architect

---

# Context

Nexus Platform est une plateforme SaaS Multi-Tenant destinée à gérer plusieurs milliers d'établissements scolaires.

Les identifiants primaires doivent garantir :

- l'unicité mondiale
- la sécurité
- la scalabilité
- la compatibilité avec une architecture distribuée
- de bonnes performances de stockage

L'utilisation d'identifiants auto-incrémentés (`INT AUTO_INCREMENT`) n'était pas compatible avec ces exigences.

---

# Problem Statement

Quel type d'identifiant utiliser comme clé primaire dans l'ensemble de Nexus Platform ?

La solution devait :

- fonctionner en environnement distribué
- éviter les collisions
- empêcher l'énumération des ressources
- être compatible avec Laravel
- améliorer les performances des index

---

# Decision

Nexus adopte **ULID (Universally Unique Lexicographically Sortable Identifier)** comme identifiant principal pour toutes les entités métier.

Chaque ressource possède un identifiant ULID généré automatiquement lors de sa création.

Les identifiants numériques auto-incrémentés ne sont pas utilisés comme clés primaires.

---

# Alternatives Considered

## Option 1

INT AUTO_INCREMENT

### Avantages

- Très rapide
- Simple

### Inconvénients

- Prévisible
- Peu adapté au SaaS
- Impossible à générer de manière distribuée
- Mauvaise sécurité

---

## Option 2

UUID v4

### Avantages

- Unique
- Standard mondial

### Inconvénients

- Taille importante
- Fragmentation des index
- Mauvaises performances d'insertion

---

## Option 3 (Choisie)

ULID

### Avantages

- Unique
- Tri chronologique
- Compatible Base32
- Meilleures performances des index
- Compatible Laravel
- Génération distribuée

---

# Consequences

Toutes les tables métier utilisent un ULID.

Les relations entre entités utilisent également des ULID.

Les API exposent uniquement ces identifiants.

---

# Positive Impacts

- Haute sécurité
- Architecture distribuée
- Excellentes performances
- URLs non prévisibles
- Compatible Multi-Tenant
- Compatible microservices

---

# Negative Impacts

- Identifiants plus longs
- Lecture moins intuitive
- Taille supérieure aux entiers

---

# Implementation Notes

Toutes les migrations utilisent :

- ULID
- Foreign ULID
- Laravel HasUlids

Les modèles Laravel utilisent :

```php
use HasUlids;
```

Les clés primaires sont définies comme :

```php
$table->ulid('id')->primary();
```

---

# Related Documents

- 03_Database
- Laravel_Conventions
- Naming_Conventions
- Multi_Tenant_Isolation
- Coding_Standards