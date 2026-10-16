Excellent ! 🔥 Tu as déjà franchi une étape importante : Debian fonctionne dans VirtualBox en mode Bridged, Apache sert ton site, et Tailscale te permet d'accéder à ton serveur depuis tes appareils de test.

Nous pouvons maintenant transformer cette installation en véritable environnement de préproduction pour NexusSchool, avec Laravel, MariaDB, des sous-domaines d'écoles et, ensuite, HTTPS.

Je te recommande de procéder dans cet ordre :

1. Vérifier le serveur Debian et son réseau actuel.
2. Installer les versions compatibles de PHP et les extensions nécessaires à NexusSchool.
3. Déployer une copie indépendante du projet existant.
4. Configurer MariaDB avec une base dédiée à la préproduction.
5. Configurer Apache pour servir Laravel depuis le dossier `public/`.
6. Simuler les sous-domaines, par exemple `ebenezer.nexusschool.test` et `zinda.nexusschool.test`.
7. Tester la résolution des écoles, l'authentification, l'isolation des données et les erreurs.
8. Configurer un vrai domaine et HTTPS lorsque nous serons prêts.

Point important : puisque ton site HTML fonctionne déjà, nous allons conserver cette configuration comme référence et examiner les sites virtuels Apache avant toute modification. Nous ne voulons pas casser ton site de test pour installer Laravel.

## Étape 1 — Inspecter Debian avant d'installer Laravel

Debian 13 fournit PHP 8.4 dans ses dépôts officiels, ce qui constitue un bon point de départ pour notre stack. Nous devons cependant vérifier les dépendances réelles du projet avant de choisir les paquets.&#x20;

[image](https://www.google.com/s2/favicons?domain=https://packages.debian.org\&sz=32)

Debian Packages

+1



Sur ton serveur Debian, connecte-toi par SSH ou ouvre un terminal et exécute ces commandes sans rien installer ni modifier.

### A. Vérifier le système et les ressources

```
cat /etc/os-release
uname -m
free -h
df -h /
ip -br address
```

### B. Vérifier Apache, PHP et MariaDB

```
sudo systemctl status apache2 --no-pager
sudo apache2ctl -S
php -v
php -m
composer --version
mariadb --version
sudo systemctl status mariadb --no-pager
```

Si `composer`, `php` ou `mariadb` n'est pas installé, note-le simplement. Ne tente pas encore de corriger les éventuelles erreurs.

### C. Identifier le site HTML actuel

```
ls -la /var/www/
ls -la /etc/apache2/sites-available/
ls -la /etc/apache2/sites-enabled/
```

Apache doit continuer à servir ton site HTML tant que nous n'avons pas validé la configuration de NexusSchool.

### D. Vérifier Tailscale

```
tailscale status
tailscale ip -4
```

Garde l'adresse IP Tailscale du serveur : elle sera utile pour configurer l'accès privé depuis tes appareils.

## Étape 2 — Choisir comment simuler les domaines

Je te recommande de commencer avec un domaine local dédié à NexusSchool :

- `nexusschool.test` — domaine de test principal
- `ebenezer.nexusschool.test` — administration de l'école Eben Ezer
- `zinda.nexusschool.test` — administration de l'école Zinda

Le suffixe `.test` est destiné aux essais et évite de dépendre d'un domaine public acheté.

Pour que ces noms fonctionnent sur tous tes appareils Tailscale, nous pourrons installer un petit service DNS privé sur Debian, puis configurer Tailscale pour envoyer les requêtes concernant `nexusschool.test` vers ce DNS. Tailscale prend en charge le DNS conditionnel (split DNS), mais MagicDNS seul ne crée pas automatiquement des entrées arbitraires pour chaque sous-domaine.&#x20;

[image](https://www.google.com/s2/favicons?domain=https://tailscale.com\&sz=32)

Tailscale Docs

+1



Cela nous donnera une simulation plus réaliste que l'ajout manuel de chaque école dans le fichier `hosts` de chaque appareil.

## Étape 3 — Préparer la copie NexusSchool

Sur ton PC Windows, vérifie également l'état du dépôt avant de transférer les fichiers :

```
cd C:\xampp\htdocs\NexusSchool

git status --short
git branch --show-current
git log -1 --oneline

php -v
php artisan --version
```

Nous devons préserver les changements non commités, et ne pas supposer qu'un simple `git clone` contiendra tout le travail actuel.

Envoie-moi les résultats de ces vérifications Debian et Windows. À partir de là, nous passerons directement à l'installation des extensions PHP, de Composer et des composants manquants, puis au déploiement de la copie. Nous conserverons Apache et ton site HTML existant jusqu'à ce que la nouvelle configuration soit validée.
