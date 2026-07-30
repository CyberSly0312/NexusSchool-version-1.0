# V1_01_Tenancy.md

# Nexus Platform

## Tenancy Architecture

**Version :** 1.0
**Statut :** Draft

---

# 1. Objectif

Le moteur de Tenancy est responsable de l'isolation complète des établissements utilisant Nexus Platform.

Chaque établissement est considéré comme un **Tenant** indépendant.

Un tenant possède ses propres utilisateurs, ses propres paramètres, ses propres données et son propre environnement de travail.

Aucun tenant ne doit pouvoir accéder aux ressources d'un autre tenant.

---

# 2. Définition d'un Tenant

Un Tenant représente une organisation cliente utilisant Nexus Platform.

Dans Nexus School, un tenant correspond à un établissement scolaire.

Exemples :

* Zinda International School
* Lycée Moderne
* Collège Saint Jean

---

# 3. Architecture des domaines

La plateforme est organisée autour de deux niveaux de domaines.

## Domaine central

Utilisé uniquement par Nexus.

Exemples :

```text
www.nexusschool.com
admin.nexusschool.com
```

Le domaine central ne contient aucune donnée métier des établissements.

Il sert uniquement à administrer la plateforme SaaS.

---

## Domaines des tenants

Chaque établissement reçoit automatiquement un sous-domaine.

Exemples :

```text
zinda.nexusschool.com

saintjean.nexusschool.com

lyceemoderne.nexusschool.com
```

Toutes les opérations quotidiennes de l'établissement sont effectuées exclusivement depuis ce domaine.

---

# 4. Création d'un tenant

La création d'un tenant est réalisée uniquement par le Super Administrateur Nexus.

Le processus comprend automatiquement :

1. Création du tenant
2. Attribution du sous-domaine
3. Initialisation des paramètres
4. Création du compte Administrateur Général
5. Installation des applications par défaut
6. Création des rôles système
7. Attribution des permissions initiales
8. Envoi des informations de connexion

L'ensemble de ces opérations doit être transactionnel.

Si une étape échoue, aucune ressource ne doit être conservée.

---

# 5. Isolation

Chaque tenant est totalement isolé.

L'isolation concerne :

* les utilisateurs
* les rôles
* les permissions
* les paramètres
* les fichiers
* les notifications
* les données métier
* les journaux d'activité

Aucun identifiant de tenant ne doit être modifiable par un utilisateur.

---

# 6. Authentification

Le domaine utilisé détermine automatiquement le contexte de connexion.

Exemple :

Connexion sur :

```text
zinda.nexusschool.com
```

Le système authentifie uniquement les utilisateurs appartenant au tenant Zinda.

Un utilisateur appartenant à un autre établissement ne peut pas être authentifié.

---

# 7. Administrateur Général

Chaque tenant possède un unique Administrateur Général.

Responsabilités :

* gérer les utilisateurs
* activer les applications
* configurer l'établissement
* attribuer les accès
* consulter les rapports
* administrer le tenant

L'Administrateur Général ne possède aucun accès au domaine central Nexus.

---

# 8. Cycle de vie d'un tenant

Création

↓

Configuration

↓

Activation

↓

Exploitation

↓

Suspension

↓

Réactivation

↓

Archivage

↓

Suppression

Chaque étape doit être journalisée.

---

# 9. Applications

Les applications disponibles dans un tenant sont activées individuellement.

Exemple :

```text
✓ Académique

✓ Administration

✓ Finance

✓ Communication

✗ Bibliothèque

✗ Transport
```

Un établissement peut utiliser uniquement les applications correspondant à ses besoins.

---

# 10. Sécurité

Le moteur de Tenancy doit garantir :

* isolation complète des données
* validation systématique du tenant actif
* contrôle d'accès sur chaque requête
* audit des opérations critiques
* impossibilité de changer de tenant sans nouvelle authentification

---

# 11. Principes

Le moteur de Tenancy respecte les règles suivantes.

* Un tenant représente une seule organisation.
* Un sous-domaine correspond à un seul tenant.
* Une session appartient à un seul tenant.
* Un utilisateur appartient à un seul tenant.
* Toutes les données métier appartiennent à un tenant.
* Le domaine central n'accède jamais directement aux données métier sans passer par les mécanismes d'administration prévus.

---

# 12. Évolutions futures

Le moteur de Tenancy devra permettre à terme :

* domaines personnalisés (`ecole.com`)
* gestion de plusieurs langues
* personnalisation graphique
* stockage dédié
* intégration SSO
* choix des applications installées
* marketplace de modules

---

# Conclusion

Le moteur de Tenancy constitue la fondation de Nexus Platform.

Toutes les applications, tous les utilisateurs et toutes les données reposent sur ce mécanisme d'isolation.

Aucune fonctionnalité ne peut contourner les règles définies dans ce document.
