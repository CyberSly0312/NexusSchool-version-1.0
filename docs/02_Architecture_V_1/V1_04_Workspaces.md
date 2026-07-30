# V1_02_Workspaces.md

# Nexus Platform

## Workspace Engine

**Version :** 1.0
**Statut :** Draft

---

# 1. Objectif

Le Workspace Engine est responsable de l'organisation fonctionnelle d'un tenant.

Un Workspace représente un grand domaine métier.

Il regroupe plusieurs applications partageant une même finalité.

Les utilisateurs n'accèdent pas directement aux applications.

Ils accèdent d'abord à un ou plusieurs Workspaces.

---

# 2. Philosophie

Nexus Platform n'organise pas son interface autour des rôles.

Elle l'organise autour des responsabilités métier.

Les rôles servent uniquement à attribuer des accès.

Le Workspace devient la porte d'entrée principale de l'utilisateur.

---

# 3. Définition

Un Workspace est un environnement de travail spécialisé.

Il possède :

* un tableau de bord
* une navigation
* des applications
* des widgets
* des permissions
* des paramètres

Chaque Workspace est indépendant.

---

# 4. Workspaces par défaut

La première version de Nexus School comprend les Workspaces suivants.

## Administration

Responsabilités :

* Admissions
* Élèves
* Parents
* Documents
* Archives
* Paramètres administratifs

---

## Académique

Responsabilités :

* Classes
* Matières
* Enseignants
* Présences
* Examens
* Bulletins
* Emplois du temps

---

## Finance

Responsabilités :

* Frais scolaires
* Paiements
* Dépenses
* Factures
* Bourses
* Rapports financiers

---

## Ressources Humaines

Responsabilités :

* Personnel
* Contrats
* Présences du personnel
* Congés
* Évaluations

---

## Communication

Responsabilités :

* SMS
* Emails
* Notifications
* Annonces
* Événements

---

## Bibliothèque

Responsabilités :

* Livres
* Emprunts
* Retours
* Membres

---

## Transport

Responsabilités :

* Véhicules
* Chauffeurs
* Itinéraires
* Élèves transportés

---

## Inventaire

Responsabilités :

* Stocks
* Fournitures
* Matériel
* Immobilisations

---

# 5. Structure interne

Chaque Workspace contient une ou plusieurs applications.

Exemple :

Workspace Académique

↓

Applications

* Élèves
* Classes
* Examens
* Présences
* Bulletins
* Emplois du temps

---

# 6. Accès utilisateur

Un utilisateur peut appartenir à plusieurs Workspaces.

Exemple :

Mme Awa

Accès :

✓ Administration

✓ Finance

Elle ne voit que ces deux Workspaces.

Les autres restent invisibles.

---

# 7. Navigation

Lors de la connexion, le Navigation Engine construit automatiquement le menu.

Le menu est généré à partir des Workspaces accessibles.

Aucun menu ne doit être codé en dur selon un rôle.

---

# 8. Dashboard

Chaque Workspace possède son propre tableau de bord.

Exemples :

Workspace Académique

* Effectif
* Présences
* Examens à venir
* Résultats

Workspace Finance

* Encaissements
* Dépenses
* Factures impayées
* Revenus mensuels

---

# 9. Isolation

Les Workspaces partagent le même tenant.

Ils ne créent pas de nouvelles frontières de sécurité.

La sécurité reste assurée par :

* le Tenant Engine
* le Permission Engine

Le Workspace organise uniquement l'expérience utilisateur.

---

# 10. Personnalisation

L'Administrateur Général peut :

* activer un Workspace
* désactiver un Workspace
* masquer un Workspace
* définir la page d'accueil par défaut

Exemple :

Une école primaire peut désactiver :

* Bibliothèque
* Transport

sans modifier le système.

---

# 11. Principes

Chaque Workspace respecte les règles suivantes.

* Une responsabilité métier principale.
* Une navigation indépendante.
* Un tableau de bord dédié.
* Des applications cohérentes.
* Une évolution indépendante.

---

# 12. Évolutions futures

Le moteur devra permettre :

* création de Workspaces personnalisés
* Marketplace de Workspaces
* widgets personnalisés
* tableaux de bord configurables
* favoris utilisateur
* recherche globale entre Workspaces

---

# Conclusion

Le Workspace Engine constitue le point d'entrée fonctionnel de chaque utilisateur.

Il organise les applications métier de Nexus Platform tout en restant indépendant des rôles.

Les rôles attribuent des accès.

Les Workspaces organisent l'expérience utilisateur.

Cette séparation garantit une architecture plus simple, plus modulaire et plus évolutive.
