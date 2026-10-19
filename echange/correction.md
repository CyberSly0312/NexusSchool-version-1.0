
Ce comportement est peut-être normal : avec le nouveau VirtualHost, l'ancienne URL ne doit plus fonctionner.

## Pourquoi l'ancienne URL casse

Avant, Apache servait un dossier parent, et tu entrais dans le projet par `/NexusSchool-version-1.0-main/public/`. Maintenant la racine d'Apache **est** `public/`, donc ce même chemin est cherché *à l'intérieur* de `public/` (`public/NexusSchool-version-1.0-main/public/`), qui n'existe pas. Les nouvelles adresses sont :

- `http://nexusschool.test/` et `http://nexusschool.test/super-admin`
- `http://100.123.173.99/` aussi, puisque `000-default` est désactivé et que ton vhost reçoit donc les requêtes par IP.

Teste ces trois adresses d'abord. Si elles marchent, il n'y a rien à réparer, tu utilises simplement les nouvelles URL.

## Si ça ne marche pas non plus

Vérifie d'abord que tu as bien remplacé `/CHEMIN` dans le fichier de configuration. Si tu l'as collé tel quel, Apache pointe vers un dossier qui n'existe pas et tout renvoie une erreur (la vérification `configtest` ne bloque pas dans ce cas). Je ne connais pas le chemin réel de ton projet : d'après ta première capture, il est probablement sous `/var/www/html/`, mais c'est une supposition à confirmer. Lance ceci sur le serveur et colle-moi la sortie :

```
sudo apache2ctl -S
grep -n -E "DocumentRoot|<Directory" /etc/apache2/sites-enabled/*.conf
ls -ld /var/www/html/NexusSchool-version-1.0-main/public
ls /var/www/html/NexusSchool-version-1.0-main/public/index.php
sudo tail -n 15 /var/log/apache2/nexusschool-error.log
```

Selon le résultat :

| Symptôme | Cause probable |
|---|---|
| 404 d'Apache | `DocumentRoot` faux (placeholder `/CHEMIN` ou mauvais dossier) |
| 403 Forbidden | Permissions : `www-data` doit pouvoir traverser chaque dossier parent du projet. Un projet sous `/home/vboxuser` pose souvent ce problème. |
| Page blanche ou erreur 500 de Laravel | Permissions sur `storage` et `bootstrap/cache`, ou `.env` manquant |
| Affichage sans style, Livewire qui ne répond pas | `APP_URL` encore sur l'ancien sous-chemin. Mets `APP_URL=http://nexusschool.test`, puis `php artisan config:clear`. |

## Retour arrière temporaire

Si tu as besoin de l'accès en urgence, tu peux revenir à l'ancien état :

```
sudo a2dissite nexusschool && sudo a2ensite 000-default && sudo systemctl reload apache2
```

Mais l'ancien état expose tout le projet (y compris `.env`) dans le navigateur. Fais-le seulement le temps de me renvoyer les diagnostics ci-dessus, et n'oublie pas de réactiver `nexusschool` ensuite.




sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R ug+rwX storage bootstrap/cache











vboxuser@debianserver:/var/www/html/NexusSchool-version-1.0-main$ sudo apache2ctl configtest && sudo systemctl reload apa
che2
Syntax OK
vboxuser@debianserver:/var/www/html/NexusSchool-version-1.0-main$ sudo chown -R www-data:www-data storage bootstrap/cache
vboxuser@debianserver:/var/www/html/NexusSchool-version-1.0-main$ sudo chmod -R ug+rwX storage bootstrap/cache
vboxuser@debianserver:/var/www/html/NexusSchool-version-1.0-main$ sudo apache2ctl -S
[sudo] password for vboxuser: 
VirtualHost configuration:
*:80                   debianserver.myguest.virtualbox.org (/etc/apache2/sites-enabled/000-default.conf:1)
ServerRoot: "/etc/apache2"
Main DocumentRoot: "/var/www/html"
Main ErrorLog: "/var/log/apache2/error.log"
Mutex watchdog-callback: using_defaults
Mutex rewrite-map: using_defaults
Mutex default: dir="/var/run/apache2/" mechanism=default 
Mutex mpm-accept: using_defaults
PidFile: "/var/run/apache2/apache2.pid"
Define: DUMP_VHOSTS
Define: DUMP_RUN_CFG
User: name="www-data" id=33
Group: name="www-data" id=33
vboxuser@debianserver:/var/www/html/NexusSchool-version-1.0-main$ grep -n -E "DocumentRoot|<Directory" /etc/apache2/sites-enabled/*.conf
12:     DocumentRoot /var/www/html
vboxuser@debianserver:/var/www/html/NexusSchool-version-1.0-main$ ls -ld /var/www/html/NexusSchool-version-1.0-main/public
drwxr-xr-x 5 root root 4096 Oct 16 16:12 /var/www/html/NexusSchool-version-1.0-main/public
vboxuser@debianserver:/var/www/html/NexusSchool-version-1.0-main$ ls /var/www/html/NexusSchool-version-1.0-main/public/index.php
/var/www/html/NexusSchool-version-1.0-main/public/index.php
vboxuser@debianserver:/var/www/html/NexusSchool-version-1.0-main$ sudo tail -n 15 /var/log/apache2/nexusschool_error.log
vboxuser@debianserver:/var/www/html/NexusSchool-version-1.0-main$ 







`apache2ctl -S` montre qu'une seule configuration est active : `000-default.conf`, avec `DocumentRoot /var/www/html`. Ton VirtualHost `nexusschool` n'est donc pas activé, et c'est pour cela que le projet est encore servi depuis son dossier parent. Aucune requête ne passe par `public/`. Le `configtest` « Syntax OK » ne prouvait rien : il valide la syntaxe des sites actifs, pas leur existence.

Le chemin est maintenant connu : `/var/www/html/NexusSchool-version-1.0-main/public`. `index.php` y est bien et les permissions de lecture suffisent. Ton `tail` portait sur `nexusschool_error.log` (avec un tiret bas), alors que mon fichier utilise `nexusschool-error.log` (avec un tiret). Tu as peut-être créé ta propre configuration avec un autre nom, donc regarde d'abord ce qui existe :

```
ls -l /etc/apache2/sites-available/ /etc/apache2/sites-enabled/
```

**Si tu vois un fichier `nexusschool*.conf` dans `sites-available`**, vérifie son contenu (`cat`) : le `DocumentRoot` doit se terminer par `/public`. Active-le avec son vrai nom.

**S'il n'existe pas**, crée-le avec le chemin réel :

```
sudo a2enmod rewrite
sudo tee /etc/apache2/sites-available/nexusschool.conf <<'EOF'
<VirtualHost *:80>
    ServerName nexusschool.test
    ServerAlias *.nexusschool.test
    DocumentRoot /var/www/html/NexusSchool-version-1.0-main/public

    <Directory /var/www/html/NexusSchool-version-1.0-main/public>
        AllowOverride All
        Require all granted
        Options -Indexes +FollowSymLinks
    </Directory>

    ErrorLog ${APACHE_LOG_DIR}/nexusschool-error.log
    CustomLog ${APACHE_LOG_DIR}/nexusschool-access.log combined
</VirtualHost>
EOF
```

Dans les deux cas, active le site, désactive celui par défaut, puis recharge :

```
sudo a2ensite nexusschool
sudo a2dissite 000-default
sudo apache2ctl configtest && sudo systemctl reload apache2
sudo apache2ctl -S
```

Cette fois `apache2ctl -S` doit afficher `nexusschool.test` avec le bon fichier. Si `a2ensite` répond « does not exist », le nom du fichier est différent : prends celui que `ls` t'a montré.

Ensuite, teste `http://nexusschool.test/super-admin` et `http://100.123.173.99/`. Si le résultat n'est pas bon, envoie-moi :

```
sudo tail -n 20 /var/log/apache2/nexusschool-error.log
sudo tail -n 20 /var/www/html/NexusSchool-version-1.0-main/storage/logs/laravel.log
ls -l /var/www/html/NexusSchool-version-1.0-main/.env
```

Le dernier fichier (le `.env`) doit exister et être lisible par `www-data`. S'il est en `600` et appartient à `vboxuser`, Laravel ne pourra pas le lire, et il faudra ajuster le propriétaire ou le groupe.

**Rappel de sécurité :** tant que `000-default` reste actif, `http://nexusschool.test/NexusSchool-version-1.0-main/.env` est téléchargeable par n'importe quel appareil du tailnet. Un `curl -I` sur cette URL te dira si c'est le cas. Dès que le nouveau vhost est actif, cette URL doit renvoyer 404. Si elle a répondu `200` à un moment, change `APP_KEY` et le mot de passe de la base de données.
