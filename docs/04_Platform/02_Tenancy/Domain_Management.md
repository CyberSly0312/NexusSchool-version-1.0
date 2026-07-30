# Nexus Platform
## Domain Management

**Version:** 1.0

**Status:** Draft

---

# 1. Purpose

Ce document définit la gestion des domaines et sous-domaines dans Nexus Platform.

Il décrit comment les Tenants sont accessibles, comment les domaines sont attribués, validés et administrés, ainsi que les règles garantissant leur unicité.

Chaque Tenant possède au minimum un domaine d'accès.

---

# 2. Scope

Ce document couvre :

- Les sous-domaines
- Les domaines personnalisés
- L'attribution des domaines
- La validation
- La résolution
- Les redirections
- Les certificats SSL
- Les règles de nommage

Ce document ne couvre pas :

- Les règles métier
- La gestion des utilisateurs

---

# 3. Objectives

Les objectifs sont :

- Identifier un Tenant de manière unique
- Garantir l'unicité des domaines
- Simplifier l'accès aux établissements
- Permettre les domaines personnalisés
- Assurer la sécurité des accès

---

# 4. Domain Architecture

```text
Internet

↓

DNS

↓

Domain Resolver

↓

Tenant Resolver

↓

Tenant Context

↓

Platform Kernel

↓

Applications

↓

Response
```

---

# 5. Domain Types

La plateforme prend en charge deux types de domaines.

## Platform Subdomain

Attribué automatiquement.

Exemple :

```text
zinda.nexus.com

saint-joseph.nexus.com

lycee-ouaga.nexus.com
```

---

## Custom Domain

Attribué par le Tenant.

Exemples :

```text
erp.zinda.edu

school.excellence.edu

portal.lycee-ouaga.bf
```

Le domaine personnalisé est optionnel.

---

# 6. Domain Assignment

Lors de la création d'un Tenant :

- génération du slug
- validation de disponibilité
- création du sous-domaine
- enregistrement DNS
- association avec le Tenant

Chaque domaine appartient à un seul Tenant.

---

# 7. Domain Resolution

Chaque requête suit le processus suivant.

```text
Incoming Request

↓

Host Detection

↓

Domain Validation

↓

Tenant Lookup

↓

Tenant Context

↓

Continue Request
```

Si aucun Tenant n'est trouvé, une erreur contrôlée est retournée.

---

# 8. Naming Rules

Les domaines doivent respecter les règles suivantes.

Autorisés :

- lettres
- chiffres
- tirets

Interdits :

- espaces
- caractères spéciaux
- accents
- doublons

Les noms sont convertis en minuscules.

---

# 9. Reserved Domains

Les domaines suivants sont réservés par la plateforme.

```text
www

admin

platform

api

mail

docs

support

status

cdn
```

Ils ne peuvent pas être utilisés comme sous-domaines de Tenant.

---

# 10. SSL Management

Tous les domaines utilisent HTTPS.

Les certificats SSL sont :

- générés automatiquement
- renouvelés automatiquement
- surveillés

Aucune connexion HTTP n'est autorisée en production.

---

# 11. Redirections

Les redirections autorisées sont :

- HTTP → HTTPS
- Ancien domaine → Nouveau domaine
- Sous-domaine → Domaine personnalisé

Les boucles de redirection sont interdites.

---

# 12. Domain Change

Un Tenant peut modifier son domaine.

Le processus comprend :

- validation
- vérification DNS
- activation
- mise à jour des configurations
- redirection éventuelle

Les anciennes URL peuvent être conservées temporairement.

---

# 13. Domain Lifecycle

```text
Requested

↓

Validated

↓

Assigned

↓

Activated

↓

Operational

↓

Updated

↓

Archived

↓

Released
```

---

# 14. Security

Les contrôles suivants sont obligatoires.

- Validation DNS
- Validation SSL
- Vérification de propriété
- Protection contre l'usurpation
- Journalisation des modifications

---

# 15. Constraints

Les règles suivantes sont obligatoires.

- Un domaine appartient à un seul Tenant.
- Chaque Tenant possède au moins un domaine actif.
- Les domaines réservés sont inaccessibles aux Tenants.
- Toutes les connexions utilisent HTTPS.
- Toute modification est enregistrée dans les journaux d'audit.

---

# 16. Future Evolution

Les prochaines versions pourront intégrer :

- Multi-domaines par Tenant
- Domaines internationaux (IDN)
- Gestion avancée du DNS
- Certificats SSL personnalisés
- Routage géographique
- Haute disponibilité DNS

---

## Status

Draft

## Version

1.0

## Last Updated

2026-07-27

## Related Documents

- Multi_Tenant_Architecture
- Tenant_Lifecycle
- Tenant_Isolation
- Platform_Kernel
- Routing_Architecture
```