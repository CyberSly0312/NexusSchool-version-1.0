# ADR-001
# Multi-School Multi-Tenant Architecture

**Status:** Accepted

**Date:** 2026-07-30

**Decision Makers:**

- Solution Architect
- Backend Architect
- Product Owner

---

# Context

Nexus Platform est conçu pour être utilisé simultanément par plusieurs établissements scolaires sans qu'aucun établissement ne puisse accéder aux données d'un autre.

Le système doit pouvoir héberger des centaines, puis des milliers d'écoles sur une seule plateforme tout en garantissant la confidentialité, la sécurité et les performances.

L'architecture devait donc choisir entre plusieurs approches de gestion du multi-tenant.

---

# Problem Statement

Comment permettre à plusieurs établissements scolaires de partager la même plateforme tout en garantissant une isolation complète des données, des utilisateurs, des permissions et des ressources ?

La solution devait également :

- faciliter les mises à jour
- réduire les coûts d'exploitation
- permettre la montée en charge
- simplifier la maintenance
- garantir la sécurité

---

# Decision

Nexus adopte une architecture **Shared Application / Multi-Tenant**.

Chaque établissement est représenté par un **Tenant**.

Chaque requête est exécutée dans le contexte du Tenant actif.

Toutes les ressources sont isolées :

- utilisateurs
- permissions
- fichiers
- paramètres
- stockage
- sessions
- cache
- traitements

Le Tenant est identifié principalement par son domaine ou son sous-domaine.

---

# Alternatives Considered

## Option 1

Une installation indépendante par école.

### Avantages

- Isolation naturelle
- Simplicité conceptuelle

### Inconvénients

- Maintenance très coûteuse
- Mises à jour complexes
- Déploiements multiples
- Faible évolutivité

---

## Option 2

Base de données unique avec Tenant ID.

### Avantages

- Déploiement simple
- Maintenance réduite

### Inconvénients

- Risque d'accès inter-Tenant
- Requêtes plus sensibles aux erreurs
- Isolation plus faible

---

## Option 3 (Choisie)

Architecture Multi-Tenant avec résolution automatique du Tenant.

### Avantages

- Forte isolation
- Maintenance simplifiée
- Haute évolutivité
- Déploiement unique
- Compatible SaaS

---

# Consequences

Cette décision implique :

- un Tenant Resolver
- un contexte de Tenant obligatoire
- une isolation systématique
- des contrôles de sécurité renforcés
- une gestion centralisée des écoles

---

# Positive Impacts

- Architecture évolutive
- Coût d'exploitation réduit
- Déploiement unique
- Administration centralisée
- Haute disponibilité
- Sécurité renforcée
- Compatible Cloud

---

# Negative Impacts

- Complexité technique plus importante
- Validation obligatoire du contexte Tenant
- Surveillance accrue de la sécurité
- Tests Multi-Tenant indispensables

---

# Implementation Notes

L'architecture repose notamment sur :

- Laravel 12
- Stancl Tenancy
- Filament 4
- Spatie Permission
- UUID/ULID
- Middleware de résolution du Tenant
- Policies de sécurité
- Audit centralisé

Toutes les Applications doivent respecter cette architecture.

---

# Related Documents

- 04_Platform/Tenancy
- 04_Platform/Authentication
- 04_Platform/Authorization
- 04_Platform/Permission_Engine
- 06_SaaS/01_Tenant_Lifecycle
- 06_SaaS/11_Multi_Tenant_Isolation