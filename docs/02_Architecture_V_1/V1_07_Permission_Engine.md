# V1_04_Permission_Engine.md

# Nexus Platform

## Permission Engine

**Version :** 1.0
**Statut :** Draft

---

# 1. Objectif

Le Permission Engine est responsable du contrôle d'accès de l'ensemble de Nexus Platform.

Son rôle est de déterminer ce qu'un utilisateur peut :

* voir
* créer
* modifier
* supprimer
* approuver
* exporter
* administrer

Le moteur est indépendant de l'interface utilisateur.

---

# 2. Philosophie

Les permissions ne sont pas attribuées directement aux menus.

Les menus sont une conséquence des permissions.

Le système ne demande jamais :

> "Quel est ton rôle ?"

Il demande :

> "Quelles sont tes autorisations ?"

---

# 3. Architecture

Le moteur repose sur la hiérarchie suivante.

```text
Tenant

↓

Workspace

↓

Application

↓

Domain

↓

Module

↓

Action

↓

Permission
```

Chaque niveau hérite du contexte du niveau supérieur.

---

# 4. Les rôles

Les rôles sont uniquement des ensembles de permissions.

Ils ne contiennent aucune logique métier.

Exemples :

* Administrateur Général
* Directeur
* Secrétaire
* Comptable
* Enseignant
* Parent
* Élève

Un rôle peut évoluer sans modifier le fonctionnement du système.

---

# 5. Les permissions

Une permission suit toujours le même format.

```text
application.domain.action
```

Exemples :

```text
students.view
students.create
students.update
students.delete

attendance.mark
attendance.view

finance.payment.collect
finance.payment.refund

reportcard.publish
reportcard.approve
```

La nomenclature doit être identique dans toute la plateforme.

---

# 6. Attribution

Les permissions peuvent être accordées :

* directement à un utilisateur
* par l'intermédiaire d'un rôle

Les permissions attribuées directement à un utilisateur ont toujours priorité.

---

# 7. Évaluation

Lorsqu'une requête est exécutée, le moteur vérifie successivement :

1. Tenant actif
2. Utilisateur authentifié
3. Applications actives
4. Workspace accessible
5. Permission demandée

Si une seule étape échoue, l'accès est refusé.

---

# 8. Navigation

Le Navigation Engine interroge le Permission Engine.

Les menus sont construits automatiquement.

Une application sans permission devient invisible.

Un Workspace vide n'est pas affiché.

---

# 9. Interface utilisateur

Les permissions contrôlent également :

* les boutons
* les actions de table
* les formulaires
* les exports
* les imports
* les tableaux de bord
* les widgets

Aucun élément de l'interface ne doit apparaître si l'utilisateur ne possède pas la permission correspondante.

---

# 10. Audit

Chaque refus d'accès critique peut être journalisé.

Les événements suivants peuvent être enregistrés :

* accès refusé
* tentative d'action interdite
* modification des permissions
* modification des rôles

---

# 11. Évolutions futures

Le moteur devra permettre :

* permissions temporaires
* délégation de permissions
* permissions conditionnelles
* approbations à plusieurs niveaux
* héritage de rôles
* permissions par département
* permissions par année scolaire
* permissions par classe

---

# 12. Principes

Le Permission Engine respecte les règles suivantes.

* Les permissions gouvernent le système.
* Les rôles simplifient l'administration.
* Les menus sont générés automatiquement.
* Les autorisations sont évaluées à chaque requête.
* Toute action sensible doit être protégée.

---

# Conclusion

Le Permission Engine constitue le mécanisme central de sécurité de Nexus Platform.

Les rôles, les menus, les applications et les actions s'appuient tous sur ce moteur pour garantir un contrôle d'accès cohérent, évolutif et indépendant de l'interface utilisateur.
