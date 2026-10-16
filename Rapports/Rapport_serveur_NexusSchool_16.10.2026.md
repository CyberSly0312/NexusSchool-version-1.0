# Rapport d’état du serveur de préproduction NexusSchool

**Date de mise à jour : 16 octobre 2026**  
**Serveur :** `debianserver`  
**Compte système utilisé :** `vboxuser`  
**Objectif :** préparer une préproduction privée de NexusSchool, accessible uniquement via Tailscale.

> Ce rapport distingue les éléments confirmés par les sorties de terminal et les précisions apportées par l’utilisateur, les éléments restant à réaliser et les propositions qui n’ont pas encore été exécutées. Une commande proposée n’est pas considérée comme une opération déjà effectuée.

---

## 1. Résumé exécutif

Le socle du serveur Debian est installé. Apache est présent, PHP 8.4 est installé, Composer est installé et MariaDB répond aux requêtes. Le réseau et Tailscale sont stables selon la confirmation de l’utilisateur.

La prochaine étape d’infrastructure est de mettre en place un nom privé pour les tests, sans acheter de domaine public. Ensuite, NexusSchool devra être transféré sur le serveur, configuré pour la préproduction et connecté à une base de données séparée de la base de développement Windows/XAMPP.

**État global : socle serveur prêt ; nom DNS privé à configurer ; déploiement de NexusSchool et base de préproduction encore à réaliser.**

## 2. État des composants

| Composant | État | Détails |
|---|---|---|
| Système | Confirmé | Debian GNU/Linux 13 (Trixie), version complète indiquée : 13.7. |
| Apache | Installé | Apache HTTP Server 2.4.68 (Debian). Le VirtualHost actuellement affiché est `000-default.conf`, sur le port 80. |
| DocumentRoot Apache | Confirmé | `/var/www/html`. Le site HTML/CSS/JavaScript de test a déjà été servi par Apache selon les confirmations de l’utilisateur. |
| PHP CLI | Fonctionnel | PHP 8.4.26, Zend Engine 4.4.26, Zend OPcache 8.4.26. Exécutable : `/usr/bin/php`. Configuration CLI : `/etc/php/8.4/cli/php.ini`. |
| Extensions PHP | Paquets installés | La commande APT a installé/vérifié les paquets `php8.4-mysql`, `php8.4-xml`, `php8.4-curl`, `php8.4-mbstring`, `php8.4-zip`, `php8.4-bcmath`, `php8.4-intl`, `php8.4-gd` et `php8.4-opcache`, ainsi que `php8.4-cli` et `php8.4-common`. |
| Composer | Fonctionnel | Composer 2.8.8, installé depuis les paquets Debian. La sortie indique PHP 8.4.26. |
| MariaDB | Fonctionnel | MariaDB Server/Client 11.8.6 installés. Le service a été activé au démarrage et affiché `active (running)`. La requête `SELECT VERSION();` a répondu `11.8.6-MariaDB-0+deb13u1 from Debian`. |
| Git, cURL, Unzip | Installés | L’installation de Git, cURL et Unzip est confirmée par les sorties APT. |
| Réseau de la VM | Stable selon l’utilisateur | La carte réseau VirtualBox est configurée en mode Bridged selon les échanges précédents. |
| Tailscale | Stable selon l’utilisateur | L’accès réseau par Tailscale a déjà été mis en place et testé. Ne pas refaire les vérifications de base sans raison. |
| DNS privé | À configurer | Aucun domaine public ne sera acheté pour l’instant. Le nom privé de test reste à mettre en place. |
| NexusSchool sur Debian | À faire | Le projet Laravel n’a pas encore été déployé sur ce serveur. |
| Base de préproduction NexusSchool | À faire | Elle doit être distincte de la base de développement sur Windows/XAMPP. Aucun transfert/restauration n’est confirmé dans ce rapport. |
| Domaine public et HTTPS public | Hors périmètre immédiat | Pas nécessaire pour la préproduction privée actuelle. À reconsidérer si l’objectif change. |

### Précision sur les extensions PHP

La première commande `php -m` du fichier source a été exécutée **avant** l’installation des extensions additionnelles et ne listait alors que les modules de base. Plus loin, la sortie APT confirme l’installation des paquets d’extensions cités dans le tableau. Pour le déploiement, il faudra s’assurer que les extensions sont bien chargées dans l’environnement PHP utilisé par Composer et par Apache/PHP-FPM, sans refaire maintenant l’installation déjà effectuée.

## 3. Versions et chemins relevés

- OS : `Debian GNU/Linux 13 (trixie)`, `DEBIAN_VERSION_FULL=13.7`.
- Apache : `Apache/2.4.68 (Debian)`.
- PHP CLI : `PHP 8.4.26`.
- Zend Engine : `4.4.26`.
- Composer : `2.8.8`.
- MariaDB : `11.8.6-MariaDB-0+deb13u1`.
- Configuration PHP CLI : `/etc/php/8.4/cli/php.ini`.
- Configuration Apache : `/etc/apache2`.
- VirtualHost actif observé : `/etc/apache2/sites-enabled/000-default.conf`.
- DocumentRoot actuel : `/var/www/html`.
- Journal d’erreur Apache : `/var/log/apache2/error.log`.

## 4. Explication du message Composer

Le fichier source montre cette commande lancée depuis `~/Downloads` :

```bash
composer check-platform-reqs
```

Composer a répondu :

```text
Composer could not find a composer.json file in /home/vboxuser/Downloads
To initialize a project, please create a composer.json file.
```

Ce message **ne signifie pas que Composer est mal installé**. La commande a été lancée dans le dossier `Downloads`, qui ne contenait pas le fichier `composer.json` d’un projet.

Ne pas créer manuellement un fichier `composer.json` dans `Downloads`. Une fois NexusSchool transféré, les commandes Composer devront être exécutées à la racine réelle du projet Laravel, là où se trouve son `composer.json`.

## 5. Plan de nommage privé

Le choix actuel est d’utiliser un nom privé, sans achat de domaine, accessible uniquement depuis les appareils connectés à Tailscale.

Noms de test envisagés :

- `nexusschool.test` — entrée principale de test ;
- `ebenezer.nexusschool.test` — exemple de sous-domaine d’école ;
- `zinda.nexusschool.test` — autre exemple de sous-domaine d’école.

Ces noms sont des propositions de travail, pas encore des noms DNS fonctionnels confirmés. Le suffixe `.test` est destiné aux environnements de test et ne requiert pas l’achat d’un domaine public.

### Architecture DNS envisagée

1. Installer et configurer un résolveur DNS privé tel que `dnsmasq` sur Debian, lié à l’adresse Tailscale du serveur.
2. Faire pointer `nexusschool.test` et ses sous-domaines vers l’adresse IPv4 Tailscale de Debian.
3. Configurer le DNS split-horizon dans l’administration Tailscale pour que les appareils du tailnet envoient les requêtes de `nexusschool.test` vers ce résolveur.
4. Tester la résolution depuis un autre appareil connecté à Tailscale.
5. Configurer ensuite Apache pour répondre aux noms retenus.

**Important :** cette configuration DNS a été proposée comme prochaine étape, mais elle n’est pas déclarée terminée. Ne pas considérer `dnsmasq`, le split DNS ou les VirtualHosts de NexusSchool comme déjà configurés tant que leur exécution et leur test ne sont pas confirmés.

## 6. Déploiement NexusSchool : travaux restants

Le projet existant est une application Laravel 12 avec Filament 4 et Stancl Tenancy. Le projet de développement est conservé sur Windows/XAMPP ; il ne faut pas remplacer ni altérer cet environnement pendant la préparation de la préproduction.

Ordre recommandé :

1. Finaliser et tester le DNS privé Tailscale.
2. Transférer une copie du projet NexusSchool vers Debian en préservant les changements locaux non commités. Ne pas supposer qu’un simple clone Git contient tout le travail actuel.
3. Installer les dépendances Composer depuis la racine du projet et vérifier les exigences de plateforme.
4. Créer une base de données et un utilisateur MariaDB dédiés à la préproduction, avec des privilèges limités à cette base.
5. Préparer le fichier `.env` de préproduction sans copier de secrets dans ce rapport. Configurer notamment l’environnement, l’URL, la connexion MariaDB et les paramètres nécessaires au tenancy.
6. Examiner les migrations et la stratégie de données avant toute importation. Ne jamais écraser la base Windows/XAMPP. Si une copie de données est nécessaire, réaliser un export contrôlé et restaurer uniquement dans la base de préproduction dédiée.
7. Configurer un VirtualHost Apache pointant vers le répertoire `public/` de Laravel, sans exposer la racine du projet ni le fichier `.env`.
8. Vérifier la méthode d’intégration PHP avec Apache (module Apache ou PHP-FPM) au moment du déploiement.
9. Tester l’accès au domaine principal, les sous-domaines d’école, la création/association des tenants, l’authentification, les journaux et l’isolation des données entre tenants.
10. Conserver la possibilité de revenir au site HTML de test jusqu’à ce que le VirtualHost NexusSchool soit vérifié.

Aucune migration, restauration de base ou mise en production de NexusSchool n’est déclarée accomplie dans ce rapport.

## 7. Sécurité et limites de la préproduction

- L’accès applicatif visé est privé via Tailscale.
- Ne pas exposer MariaDB directement sur Internet ou sur toutes les interfaces réseau.
- Utiliser un compte MariaDB dédié à l’application, pas le compte administrateur de la base.
- Ne pas partager le contenu du `.env`, les mots de passe ou les clés d’application.
- Ne pas lancer `migrate:fresh`, supprimer une base, écraser le site existant ou forcer un push Git pour préparer cette préproduction.
- Garder la base Windows/XAMPP et la base Debian de préproduction séparées.
- Le nom `.test` et la résolution DNS privée ne remplacent pas une configuration de production publique si l’objectif évolue plus tard.

## 8. Prochaine action exacte

La prochaine action prévue est la **configuration du DNS privé**. La séquence proposée est :

1. Installer `dnsmasq` si ce n’est pas déjà fait.
2. Relever l’adresse IPv4 Tailscale de Debian.
3. Préparer un fichier `/etc/dnsmasq.d/nexusschool.conf` pour les noms `nexusschool.test` et ses sous-domaines.
4. Tester la syntaxe avant de redémarrer le service.
5. Ajouter le résolveur personnalisé dans l’administration Tailscale, avec le split DNS pour `nexusschool.test`.
6. Tester depuis un second appareil connecté à Tailscale.

La commande de configuration proposée précédemment n’est **pas** considérée comme exécutée. Il faut vérifier les résultats réels étape par étape avant de poursuivre vers Apache.

## 9. Conclusion

Le serveur possède désormais les composants de base nécessaires : Debian 13, Apache, PHP 8.4, Composer et MariaDB. Les vérifications et installations déjà effectuées ne doivent pas être répétées inutilement. Le réseau et Tailscale sont stables selon l’utilisateur.

Le travail immédiat porte sur le DNS privé, puis sur le déploiement propre de NexusSchool et la création de sa base de préproduction. Ces deux derniers éléments restent à réaliser et à valider.
