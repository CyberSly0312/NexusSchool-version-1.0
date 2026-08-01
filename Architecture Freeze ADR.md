# NEXUS SCHOOL

# Architecture Freeze ADR

## ADR-003 - Architecture Freeze v1.0

**Status :** ACCEPTED

**Date :** 31 Juillet 2026

**Authors :** Nexus Architecture Board

---

# 1. Contexte

Après plusieurs mois de conception, de documentation, de refonte de l'architecture et d'audits techniques, Nexus School atteint un niveau de maturité suffisant pour considérer son architecture comme stable.

Les audits réalisés montrent que :

* la vision produit est clairement définie ;
* l'architecture DDD est cohérente ;
* les principaux domaines métier sont identifiés ;
* la stratégie SaaS Multi-Tenant est établie ;
* les conventions de développement sont documentées ;
* les décisions d'architecture sont largement couvertes par les ADR existants.

Le principal risque identifié n'est plus une faiblesse de conception, mais une évolution non contrôlée de l'architecture au fil des développements.

---

# 2. Décision

À compter de cette ADR, **l'architecture de Nexus School est officiellement gelée (Architecture Freeze v1.0).**

Les principes suivants deviennent la référence officielle du projet.

---

# 3. Principes non négociables

## 3.1 Domain Driven Design

Le découpage en Domaines constitue le cœur de l'architecture.

Aucune nouvelle fonctionnalité ne doit contourner cette organisation.

Tout nouveau développement appartient obligatoirement à un domaine métier identifié.

---

## 3.2 Multi-Tenant

La séparation entre :

* Central
* Tenant
* Shared

est considérée comme définitive.

Aucun accès direct entre les contextes ne sera autorisé sans justification documentée.

---

## 3.3 Architecture orientée métier

Le métier pilote la technique.

Les décisions techniques ne peuvent jamais modifier une règle métier existante.

---

## 3.4 Laravel

Laravel est un framework d'implémentation.

Il ne définit pas l'architecture.

Les conventions Laravel doivent être respectées uniquement lorsqu'elles ne contredisent pas les choix architecturaux du projet.

---

## 3.5 Filament

Filament constitue uniquement la couche d'administration.

La logique métier ne doit jamais être implémentée directement dans les Resources, Pages ou Widgets.

---

# 4. Règles de gouvernance

À partir de maintenant, toute modification importante doit répondre aux questions suivantes :

* Cette modification respecte-t-elle les Domaines ?
* Respecte-t-elle les ADR existants ?
* Respecte-t-elle la séparation Multi-Tenant ?
* Respecte-t-elle les règles métier documentées ?

Si la réponse est "non" à une seule question, la modification doit être revue.

---

# 5. Évolutions autorisées

Les éléments suivants pourront évoluer librement :

* fonctionnalités métier ;
* écrans utilisateur ;
* formulaires ;
* rapports ;
* tableaux de bord ;
* optimisations SQL ;
* performances ;
* cache ;
* jobs ;
* notifications ;
* tests ;
* intégrations externes.

---

# 6. Évolutions nécessitant un ADR

Les modifications suivantes nécessitent obligatoirement une nouvelle Architecture Decision Record :

* création d'un nouveau Domaine ;
* suppression d'un Domaine ;
* modification des frontières entre Domaines ;
* changement du modèle Multi-Tenant ;
* changement de la stratégie RBAC ;
* changement de l'architecture des événements ;
* remplacement d'une technologie principale ;
* modification du modèle de données central.

---

# 7. Évolutions interdites

Sans approbation architecturale, les actions suivantes sont interdites :

* déplacer arbitrairement des modèles entre domaines ;
* contourner les Services pour accéder directement au métier ;
* implémenter des règles métier dans les Resources Filament ;
* créer des dépendances circulaires entre Domaines ;
* partager des données entre tenants ;
* modifier les frontières Central/Tenant ;
* introduire une logique métier dans les migrations ;
* créer des dépendances techniques non documentées.

---

# 8. Gouvernance des Domaines

Chaque Domaine est désormais considéré comme un sous-système autonome.

Chaque Domaine doit contenir :

* Vision
* Business Rules
* Workflows
* Models
* Services
* Policies
* Events
* Tests
* Documentation

Tout développement doit respecter cette organisation.

---

# 9. Philosophie de développement

Le développement de Nexus School suit désormais le cycle suivant :

```text
Business Requirement
        │
        ▼
Documentation
        │
        ▼
Architecture Validation
        │
        ▼
ADR (si nécessaire)
        │
        ▼
Sprint Planning
        │
        ▼
Implementation
        │
        ▼
Automated Tests
        │
        ▼
Code Review
        │
        ▼
Merge
        │
        ▼
Documentation Update
```

Aucune étape ne doit être ignorée.

---

# 10. Définition de la stabilité

L'Architecture Freeze ne signifie pas que le projet est terminé.

Elle signifie que :

* les fondations sont considérées comme suffisamment solides ;
* les futurs efforts doivent porter sur l'implémentation métier ;
* toute évolution structurelle doit être exceptionnelle et justifiée.

---

# 11. Conséquences

À partir de cette décision :

* les prochains sprints sont orientés développement fonctionnel ;
* la documentation devient contractuelle ;
* les ADR deviennent obligatoires pour toute évolution majeure ;
* l'objectif principal est désormais la livraison progressive du MVP.

---

# 12. Validation

L'Architecture Board considère que l'architecture de Nexus School est suffisamment mature pour entrer dans une phase de développement intensif.

Les prochaines améliorations concerneront principalement :

* les fonctionnalités ;
* les performances ;
* l'expérience utilisateur ;
* la qualité des tests.

La structure fondamentale du projet est désormais considérée comme stable et constitue la référence officielle de Nexus School v1.0.
