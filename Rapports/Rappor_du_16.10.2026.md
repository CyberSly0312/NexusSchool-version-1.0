# RAPPORT TECHNIQUE — PRÉPRODUCTION NEXUSSCHOOL

**Date :** 16 octobre 2026  
**Projet :** NexusSchool ERP  
**Environnement :** Debian Server sur VirtualBox  
**Statut général :** Infrastructure de base opérationnelle — préparation de l'environnement Laravel en cours.

---

## 1. Objectif du serveur

Le serveur Debian est destiné à héberger une copie de préproduction de NexusSchool afin de tester le comportement réel de l'application dans un environnement proche de la production.

Les objectifs sont les suivants :

- Héberger NexusSchool sur un serveur distinct du poste de développement Windows/XAMPP.
- Installer et configurer Laravel, PHP, Composer et MariaDB.
- Conserver Apache et son site de test existant.
- Simuler les domaines et sous-domaines des établissements scolaires.
- Tester la résolution automatique des écoles et l'isolation des données.
- Permettre l'accès depuis plusieurs appareils, notamment via Tailscale.
- Préparer ultérieurement un déploiement utilisant un véritable domaine et HTTPS.

## 2. Architecture actuelle

### 2.1 Serveur physique et virtualisation

Le serveur est installé dans une machine virtuelle VirtualBox utilisant le mode réseau **Bridged (accès par pont)**.

Ce mode permet à la machine virtuelle d'être présente sur le réseau local comme une machine distincte, avec sa propre adresse IP.

### 2.2 Serveur web Apache

Apache est installé et fonctionne.

Un site de test statique comprenant HTML, CSS et JavaScript a été transféré sur le serveur et testé avec succès. Lorsque l'adresse IP du serveur est saisie dans un navigateur, le site s'affiche correctement.

Cela confirme le fonctionnement de base du serveur web et de l'accès HTTP.

### 2.3 Accès distant avec Tailscale

Tailscale est installé et utilisé pour permettre l'accès depuis les appareils de test.

L'accès au site depuis les appareils concernés a été configuré dans le cadre de cet environnement de test. Les paramètres précis du réseau Tailscale, du DNS et de l'accès distant restent à documenter.

### 2.4 PHP

PHP CLI est installé.

Version communiquée :

- PHP : 8.4.26
- Zend Engine : 4.4.26
- Zend OPcache : 8.4.26

La commande `php` fonctionne dans le terminal.

**Point restant à vérifier :** la présence des extensions PHP nécessaires à NexusSchool et l'intégration de PHP avec Apache. Le fonctionnement de PHP en ligne de commande ne prouve pas, à lui seul, que les requêtes PHP sont correctement exécutées par Apache.

### 2.5 Composer

Composer n'est pas encore installé, selon le dernier état communiqué.

Il sera nécessaire pour installer les dépendances PHP du projet Laravel.

### 2.6 MariaDB

MariaDB n'est pas encore installé sur le serveur Debian, selon le dernier état communiqué.

Le poste Windows utilise actuellement XAMPP et dispose d'une base MariaDB pour le développement.

Cette base existante doit être préservée. La préproduction devra utiliser une base distincte sur Debian.

### 2.7 Nom de domaine

Aucun nom de domaine public n'a encore été acheté.

Les domaines de test envisagés sont :

- `nexusschool.test`
- `ebenezer.nexusschool.test`
- `zinda.nexusschool.test`

Ces noms serviront à simuler le domaine principal et les sous-domaines des écoles.

La résolution DNS de ces noms sur les appareils de test n'est pas encore configurée.

---

## 3. État du projet NexusSchool

Le projet principal est actuellement développé sous Windows avec XAMPP.

Technologies et architecture connues :

- Laravel 12
- Filament 4
- PHP
- MariaDB
- Stancl Tenancy pour la gestion multi-écoles
- Gestion centralisée des écoles et résolution de leurs domaines

Le projet dispose déjà d'un panneau Super Admin et d'un environnement d'administration des écoles dans le cadre du développement.

L'objectif de la préproduction est de déployer une copie indépendante du projet et de vérifier que le fonctionnement actuel reste correct dans un environnement serveur distinct.

**Aucun transfert du projet complet, aucune installation de ses dépendances sur Debian et aucun déploiement Laravel ne sont confirmés comme terminés à ce stade.**

---

## 4. État d'avancement

| Composant | État |
|---|---|
| Debian Server dans VirtualBox | Installé |
| Réseau VirtualBox en mode Bridged | Configuré |
| Apache | Installé et opérationnel |
| Site HTML/CSS/JavaScript de test | Fonctionnel |
| Tailscale | Installé et utilisé pour les appareils de test |
| PHP 8.4.26 CLI | Installé |
| Extensions PHP requises | À vérifier |
| PHP avec Apache | À vérifier |
| Composer | Non installé |
| MariaDB sur Debian | Non installé |
| Copie de NexusSchool sur Debian | À réaliser |
| Base de préproduction indépendante | À créer |
| Configuration Laravel de préproduction | À réaliser |
| Configuration des hôtes virtuels Apache | À réaliser |
| DNS privé pour les sous-domaines `.test` | À configurer |
| HTTPS de préproduction | À configurer |
| Domaine public | Pas encore acheté |
| Tests fonctionnels de NexusSchool sur Debian | À réaliser |

---

## 5. Principes de sécurité et de préservation

Les règles suivantes doivent être respectées pendant toute la préparation :

1. Ne pas supprimer ou écraser le site HTML existant avant validation de la nouvelle configuration.
2. Ne pas désinstaller Apache ni remplacer sa configuration globale sans examiner les sites virtuels existants.
3. Ne pas modifier ni supprimer la base MariaDB de développement située sous Windows/XAMPP.
4. Ne pas utiliser de commande Git destructive pour nettoyer le dépôt.
5. Préserver les modifications locales de NexusSchool, même si elles ne sont pas encore commités.
6. Créer un environnement `.env` propre à la préproduction, avec des identifiants distincts.
7. Ne pas exposer MariaDB directement à Internet.
8. Ne pas activer le mode debug en environnement accessible à d'autres appareils.
9. Vérifier les configurations avant chaque opération susceptible de modifier des services ou des données.
10. Utiliser des données de test anonymisées si la préproduction doit contenir des informations relatives à des élèves.

---

## 6. Plan d'exécution restant

### Phase 1 — Inventaire technique

- Confirmer la version exacte de Debian.
- Vérifier les versions de PHP, les extensions disponibles et la configuration Apache.
- Identifier les sites virtuels Apache actuellement activés.
- Vérifier le fonctionnement et l'adressage de Tailscale.
- Confirmer les contraintes PHP et les dépendances du projet à partir de `composer.json` et `composer.lock`.

### Phase 2 — Installation des dépendances

Installer les composants compatibles avec le projet :

- Extensions PHP requises.
- Composer.
- MariaDB Server et client.
- Outils de transfert et de gestion du code.

Le choix des paquets devra correspondre à la version de Debian et à la provenance de PHP 8.4.26.

### Phase 3 — Déploiement indépendant

- Préparer une copie de NexusSchool sur Debian.
- Préserver le dépôt original et ses modifications locales.
- Créer une base de données distincte pour la préproduction.
- Exporter puis importer les données nécessaires, après vérification de l'export.
- Configurer l'environnement Laravel de préproduction.

### Phase 4 — Configuration Apache et Laravel

- Créer un hôte virtuel dédié à NexusSchool.
- Faire pointer le document root vers le répertoire `public/` de Laravel.
- Vérifier les permissions nécessaires sans rendre l'ensemble du projet accessible en écriture.
- Configurer les règles de réécriture et les journaux.
- Confirmer que le site HTML initial reste accessible ou qu'il dispose d'une configuration de remplacement validée.

### Phase 5 — Domaines et sous-domaines

Mettre en place un DNS privé adapté aux appareils de test.

Le fonctionnement cible est le suivant :

- `nexusschool.test` pointe vers le serveur.
- `ebenezer.nexusschool.test` pointe vers le même serveur.
- `zinda.nexusschool.test` pointe vers le même serveur.

Apache transmet les requêtes à l'application, qui doit ensuite identifier le domaine demandé et retrouver l'école correspondante.

Pour un accès via Tailscale, il faudra également configurer le DNS utilisé par les appareils concernés. Tailscale ne crée pas automatiquement des enregistrements pour chaque sous-domaine personnalisé.

### Phase 6 — Tests de préproduction

Les tests devront couvrir :

- Accès HTTP à l'application.
- Connexion au panneau Super Admin.
- Création d'une école.
- Enregistrement automatique de son domaine.
- Accès au panneau d'administration de l'école.
- Comportement face à un domaine inconnu.
- Isolation des données entre deux établissements.
- Gestion des erreurs et journaux Laravel.
- Permissions et configuration de production.
- Accès depuis les appareils de test.

### Phase 7 — Préparation du domaine public

Après validation de la préproduction locale :

- Acheter ou configurer un domaine public.
- Configurer les enregistrements DNS.
- Préparer les sous-domaines génériques.
- Mettre en place HTTPS.
- Vérifier la validation des hôtes, les proxys et les paramètres de sécurité.
- Effectuer les tests finaux avant toute ouverture publique.

---

## 7. Prochaine action immédiate

La prochaine étape consiste à effectuer un inventaire technique du serveur Debian avant d'installer les composants manquants.

Les commandes de vérification prioritaires sont :

```bash
cat /etc/os-release
php -v
php --ini
php -m
apache2 -v
sudo apache2ctl -S
command -v composer
command -v mariadb
tailscale status
tailscale ip -4
```

Ces commandes permettent de recueillir les informations nécessaires pour sélectionner les paquets et configurer l'environnement sans perturber le site déjà opérationnel.

## 8. Conclusion

Le serveur Debian est opérationnel au niveau de l'infrastructure de base : virtualisation, réseau, Apache et site statique fonctionnent. PHP 8.4.26 CLI est également disponible.

En revanche, l'environnement applicatif de NexusSchool reste à construire : extensions PHP, Composer, MariaDB, déploiement Laravel, configuration Apache dédiée et résolution DNS des sous-domaines.

La priorité est de compléter cet environnement progressivement, en maintenant une séparation stricte entre le développement Windows, la préproduction Debian et, plus tard, la production.

**Statut final : infrastructure de base fonctionnelle ; préproduction NexusSchool non encore déployée.**
