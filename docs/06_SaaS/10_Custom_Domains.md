# Nexus Platform
## Custom Domains

**Version:** 1.0

**Status:** Draft

---

# 1. Purpose

Le Custom Domains est responsable de la gestion des domaines utilisés par les établissements scolaires dans Nexus Platform.

Il permet à chaque Tenant d'utiliser un sous-domaine fourni par la plateforme ou un domaine personnalisé tout en garantissant l'isolation, la sécurité et la disponibilité des services.

Tous les domaines sont gérés exclusivement par ce composant.

---

# 2. Scope

Ce document couvre :

- Les sous-domaines
- Les domaines personnalisés
- La validation DNS
- Les certificats SSL
- Les redirections
- Les modifications de domaines

Ce document ne couvre pas :

- L'achat de noms de domaine
- Les services DNS externes

---

# 3. Objectives

Le Custom Domains doit être :

- sécurisé
- automatisé
- fiable
- évolutif
- compatible Multi-Tenant
- transparent pour les utilisateurs

---

# 4. High-Level Architecture

```text
Tenant

↓

Domain Request

↓

Validation

↓

DNS Verification

↓

SSL Configuration

↓

Domain Activation

↓

Platform Access
```

---

# 5. Responsibilities

Le composant est responsable de :

- enregistrer les domaines
- vérifier leur disponibilité
- valider les enregistrements DNS
- configurer les certificats SSL
- activer les domaines
- conserver l'historique

---

# 6. Domain Types

La plateforme prend en charge :

- Sous-domaines Nexus
- Domaines personnalisés
- Domaines secondaires
- Domaines de test

Chaque Tenant possède au minimum un domaine actif.

---

# 7. Domain Registration Flow

```text
Domain Request

↓

Syntax Validation

↓

Availability Check

↓

DNS Verification

↓

SSL Configuration

↓

Activation

↓

Ready
```

---

# 8. DNS Verification

Avant l'activation, la plateforme vérifie notamment :

- propriété du domaine
- enregistrements DNS
- résolution correcte
- absence de conflit

Les vérifications sont automatiques.

---

# 9. SSL Management

Le système peut :

- générer un certificat SSL
- renouveler automatiquement le certificat
- vérifier sa validité
- surveiller son expiration

Toutes les communications utilisent HTTPS.

---

# 10. Domain Management

Un Tenant peut :

- ajouter un domaine
- modifier un domaine
- supprimer un domaine
- définir un domaine principal

Les modifications sont soumises aux validations de sécurité.

---

# 11. Monitoring

Le système surveille notamment :

- domaines actifs
- certificats SSL
- expirations
- erreurs DNS
- domaines inactifs
- changements de configuration

Toutes les opérations sont visibles dans le Platform Panel.

---

# 12. Events

Le composant publie notamment :

- DomainRequested
- DomainValidated
- DomainActivated
- DomainUpdated
- DomainRemoved
- SSLRenewed

---

# 13. Constraints

Les règles suivantes sont obligatoires.

- Chaque Tenant possède au moins un domaine actif.
- Tous les domaines utilisent HTTPS.
- Les certificats SSL sont renouvelés automatiquement lorsque possible.
- Les validations DNS sont obligatoires avant activation.
- Toutes les modifications sont journalisées.

---

# 14. Future Evolution

Les prochaines versions pourront intégrer :

- Multi-domaines actifs
- CDN intégré
- DNS Management
- Wildcard Domains
- Géoroutage
- Certificats EV automatiques

---

## Status

Draft

## Version

1.0

## Last Updated

2026-07-29

## Related Documents

- 01_Tenant_Lifecycle
- 06_Tenant_Onboarding
- 09_Tenant_Migration
- 11_Multi_Tenant_Isolation
- 12_Provisioning
- Integration_Engine